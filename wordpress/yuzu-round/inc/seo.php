<?php
/**
 * One public URL per salon page, and a real 404 for everything else.
 */

function yuzu_round_public_paths(): array
{
    $paths = [
        'send.php' => '/wp-admin/admin-ajax.php?action=yuzu_send',
        'offers.html' => '/#offers',
    ];
    foreach (yuzu_round_pages() as $slug => $page) {
        $paths[$page['file']] = $slug === 'home' ? '/' : '/' . $slug . '/';
    }
    return $paths;
}

function yuzu_round_rewrite_public_urls(string $html, string $asset_base, ?array $pages = null): string
{
    $pages = $pages ?? yuzu_round_public_paths();
    $asset_base = rtrim($asset_base, '/') . '/';
    $rewritten = preg_replace_callback(
        '/\s(href|src|action)="([^"]*)"/',
        static function (array $match) use ($pages, $asset_base): string {
            $attr = $match[1];
            $url = $match[2];
            if ($url === '' || preg_match('/^(https?:|mailto:|tel:|#|\/)/', $url) === 1) {
                return $match[0];
            }
            $path = $url;
            $suffix = '';
            $hash = strpos($path, '#');
            if ($hash !== false) {
                $suffix = substr($path, $hash);
                $path = substr($path, 0, $hash);
            }
            $query = strpos($path, '?');
            if ($query !== false) {
                $suffix = substr($path, $query) . $suffix;
                $path = substr($path, 0, $query);
            }
            if (isset($pages[$path])) {
                return ' ' . $attr . '="' . $pages[$path] . $suffix . '"';
            }
            return ' ' . $attr . '="' . $asset_base . $url . '"';
        },
        $html
    );
    $html = is_string($rewritten) ? $rewritten : $html;

    $sets = preg_replace_callback(
        '/\s(srcset|imagesrcset)="([^"]*)"/',
        static function (array $match) use ($asset_base): string {
            $candidates = array_map('trim', explode(',', $match[2]));
            foreach ($candidates as $i => $candidate) {
                if ($candidate !== '' && preg_match('/^(https?:|\/|data:)/', $candidate) !== 1) {
                    $candidates[$i] = $asset_base . $candidate;
                }
            }
            return ' ' . $match[1] . '="' . implode(', ', $candidates) . '"';
        },
        $html
    );
    $html = is_string($sets) ? $sets : $html;

    // Social cards and structured data name files as yuzu.hair/assets/…, which
    // only exist inside the theme.
    return str_replace('https://yuzu.hair/assets/', $asset_base . 'assets/', $html);
}

function yuzu_round_is_salon_front(): bool
{
    if (!function_exists('is_front_page') || !is_front_page()) {
        return false;
    }
    return !function_exists('is_paged') || !is_paged();
}

function yuzu_round_locked_file(): ?string
{
    $pages = yuzu_round_pages();
    if (function_exists('get_queried_object')) {
        $queried = get_queried_object();
        if ($queried instanceof WP_Post && $queried->post_type === 'page') {
            $slug = (string) $queried->post_name;
            if (isset($pages[$slug]['file'])) {
                return $pages[$slug]['file'];
            }
        }
    }
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (is_string($path)) {
        $slug = trim($path, '/');
        if ($slug !== '' && $slug !== 'home' && isset($pages[$slug]['file'])) {
            return $pages[$slug]['file'];
        }
    }
    return null;
}

function yuzu_round_force_404(): void
{
    global $wp_query;
    if ($wp_query instanceof WP_Query) {
        $wp_query->set_404();
    }
    if (function_exists('status_header')) {
        status_header(404);
    }
    if (function_exists('nocache_headers')) {
        nocache_headers();
    }
}

function yuzu_round_robots_txt(): string
{
    $sitemap = 'https://yuzu.hair/sitemap.xml';
    return implode("\n", [
        'User-agent: *',
        'Disallow: /wp-admin/',
        'Allow: /wp-admin/admin-ajax.php',
        'Disallow: /review',
        'Disallow: /wp-content/themes/yuzu-round/static/',
        'Allow: /wp-content/themes/yuzu-round/static/assets/',
        'Allow: /wp-content/themes/yuzu-round/static/css/',
        'Allow: /wp-content/themes/yuzu-round/static/js/',
        'Allow: /wp-content/themes/yuzu-round/static/versions/',
        'Allow: /wp-content/themes/yuzu-round/static/favicon.svg',
        'Allow: /wp-content/themes/yuzu-round/wordpress/yuzu-round/static/',
        '',
        'Sitemap: ' . $sitemap,
        '',
    ]);
}

function yuzu_round_rewrite_version_urls(string $html, string $static_base): string
{
    $static_base = rtrim($static_base, '/') . '/';
    $pages = function_exists('yuzu_round_public_paths') ? yuzu_round_public_paths() : [];
    $map = [
        '../assets/' => $static_base . 'assets/',
        '../css/' => $static_base . 'css/',
        '../js/' => $static_base . 'js/',
        '../versions/' => $static_base . 'versions/',
        '../favicon.svg' => $static_base . 'favicon.svg',
    ];
    $html = strtr($html, $map);
    if ($pages !== []) {
        $rewritten = preg_replace_callback(
            '/\s(href|src|action)="\.\.\/([^"#?]+)((?:[?][^"#]*)?(?:#[^"]*)?)"/',
            static function (array $m) use ($pages): string {
                if (isset($pages[$m[2]])) {
                    return ' ' . $m[1] . '="' . $pages[$m[2]] . $m[3] . '"';
                }
                return $m[0];
            },
            $html
        );
        if (is_string($rewritten)) {
            $html = $rewritten;
        }
    }
    return $html;
}

function yuzu_round_serve_version_request(): void
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (!is_string($path) || preg_match('#^/versions(?:/(.*))?$#', $path, $match) !== 1) {
        return;
    }
    if ($path === '/versions') {
        $qs = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '' ? '?' . $_SERVER['QUERY_STRING'] : '';
        if (function_exists('wp_redirect')) {
            wp_redirect(home_url('/versions/' . $qs), 301);
        } else {
            header('Location: /versions/' . $qs, true, 301);
        }
        exit;
    }
    $rel = rawurldecode($match[1] ?? '');
    $rel = str_replace('\\', '/', $rel);
    if ($rel === '' || str_ends_with($rel, '/')) {
        $rel .= 'index.html';
    }
    if (str_contains($rel, "\0") || preg_match('#(^|/)\.\.(/|$)#', $rel) === 1) {
        return;
    }
    if (!function_exists('get_template_directory')) {
        return;
    }
    $static_dir = function_exists('yuzu_round_static_dir') ? yuzu_round_static_dir() : (get_template_directory() . '/static');
    $root_real = realpath($static_dir . '/versions');
    if ($root_real === false) {
        return;
    }
    $real = realpath($root_real . '/' . $rel);
    $prefix = $root_real . DIRECTORY_SEPARATOR;
    if ($real === false || !is_file($real) || !str_starts_with($real, $prefix)) {
        return;
    }
    $types = [
        'html' => 'text/html; charset=UTF-8',
        'css' => 'text/css; charset=UTF-8',
        'js' => 'text/javascript; charset=UTF-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'ttf' => 'font/ttf',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
    ];
    $ext = strtolower(pathinfo($real, PATHINFO_EXTENSION));
    if (!isset($types[$ext])) {
        return;
    }
    if (function_exists('status_header')) {
        status_header(200);
    }
    header('Content-Type: ' . $types[$ext]);
    header('X-Content-Type-Options: nosniff');
    header('X-Robots-Tag: noindex');
    header('Cache-Control: no-cache');
    if ($ext === 'html' && function_exists('get_template_directory_uri')) {
        $html = file_get_contents($real);
        $static_uri = function_exists('yuzu_round_static_uri') ? yuzu_round_static_uri() : (get_template_directory_uri() . '/static');
        $static_base = trailingslashit($static_uri);
        if (is_string($html) && function_exists('yuzu_round_prices_apply_version')) {
            try {
                $html = yuzu_round_prices_apply_version($html, $rel);
            } catch (Throwable $e) {
                // A bad price record must not take the page down.
            }
        }
        if (is_string($html)) {
            echo yuzu_round_rewrite_version_urls($html, $static_base);
            exit;
        }
    }
    readfile($real);
    exit;
}

function yuzu_round_sitemap_xml(): string
{
    $static = function_exists('yuzu_round_static_dir') ? yuzu_round_static_dir() : '';
    $edits = function_exists('get_option') ? get_option('yuzu_round_edited_at') : [];
    $edits = is_array($edits) ? $edits : [];
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    foreach (yuzu_round_pages() as $slug => $page) {
        $url = $slug === 'home' ? 'https://yuzu.hair/' : 'https://yuzu.hair/' . $slug . '/';
        $stamp = 0;
        if ($static !== '' && is_file($static . '/' . $page['file'])) {
            $stamp = (int) @filemtime($static . '/' . $page['file']);
        }
        $stamp = max($stamp, (int) ($edits[$slug] ?? 0));
        $lastmod = $stamp > 0 ? gmdate('Y-m-d', $stamp) : '2026-10-31';
        $xml .= '  <url><loc>' . htmlspecialchars($url, ENT_QUOTES | ENT_XML1, 'UTF-8') . '</loc><lastmod>' . $lastmod . "</lastmod></url>\n";
    }
    $xml .= "</urlset>\n";
    return $xml;
}

/**
 * Remember when wp-admin changed the words or prices on a page, for the
 * sitemap's lastmod.
 */
function yuzu_round_mark_edited(array $slugs): void
{
    if (!function_exists('get_option') || !function_exists('update_option')) {
        return;
    }
    $edits = get_option('yuzu_round_edited_at');
    $edits = is_array($edits) ? $edits : [];
    foreach ($slugs as $slug) {
        $edits[(string) $slug] = time();
    }
    update_option('yuzu_round_edited_at', $edits, false);
    yuzu_round_write_root_sitemap();
}

function yuzu_round_retire_default_content(): void
{
    if (!function_exists('get_option') || get_option('yuzu_round_retired_defaults') === '1') {
        return;
    }
    $posts = get_posts([
        'name' => 'hello-world',
        'post_type' => 'post',
        'post_status' => 'any',
        'posts_per_page' => 1,
        'suppress_filters' => true,
    ]);
    foreach ($posts as $post) {
        if ($post instanceof WP_Post && $post->post_title === 'Hello world!') {
            wp_trash_post($post->ID);
        }
    }
    $sample = get_page_by_path('sample-page');
    if ($sample instanceof WP_Post && $sample->post_title === 'Sample Page') {
        wp_trash_post($sample->ID);
    }
    update_option('yuzu_round_retired_defaults', '1', false);
}

function yuzu_round_sync_root_robots(): void
{
    if (get_option('yuzu_round_robots_synced') === '1' || !defined('ABSPATH')) {
        return;
    }
    $path = ABSPATH . 'robots.txt';
    if (!is_file($path) || !is_writable($path)) {
        update_option('yuzu_round_robots_synced', '1', false);
        return;
    }
    $current = file_get_contents($path);
    $legacy = "User-agent: *\nAllow: /\n\nSitemap: https://yuzu.hair/sitemap.xml";
    if (!is_string($current) || trim(str_replace("\r\n", "\n", $current)) !== $legacy) {
        update_option('yuzu_round_robots_synced', '1', false);
        return;
    }
    file_put_contents($path, yuzu_round_robots_txt());
    update_option('yuzu_round_robots_synced', '1', false);
}

function yuzu_round_sync_root_sitemap(): void
{
    if (get_option('yuzu_round_sitemap_synced') === 'v3' || !defined('ABSPATH')) {
        return;
    }
    yuzu_round_write_root_sitemap();
    update_option('yuzu_round_sitemap_synced', 'v3', false);
}

/**
 * Apache serves a real sitemap.xml in the web root before WordPress runs, so
 * keep that copy in step with the generated one.
 */
function yuzu_round_write_root_sitemap(): void
{
    if (!defined('ABSPATH')) {
        return;
    }
    $path = ABSPATH . 'sitemap.xml';
    if (is_file($path) && is_writable($path)) {
        @file_put_contents($path, yuzu_round_sitemap_xml());
    }
}

function yuzu_round_handle_send_form(): void
{
    header('X-Content-Type-Options: nosniff');
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        if (function_exists('status_header')) {
            status_header(405);
        }
        header('Allow: POST');
        echo 'Method not allowed';
        exit;
    }

    $wantsJson = str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');
    $fail = static function (int $status, string $message) use ($wantsJson): void {
        if (function_exists('status_header')) {
            status_header($status);
        } else {
            http_response_code($status);
        }
        if ($wantsJson) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['ok' => false, 'error' => $message]);
            exit;
        }
        echo $message;
        exit;
    };

    $raw = function_exists('wp_unslash') ? wp_unslash($_POST) : $_POST;
    $to = 'info@yuzuhairandbeauty.co.uk';
    $from = 'website@yuzu.hair';
    $name = trim((string) ($raw['name'] ?? ''));
    $email = trim((string) ($raw['email'] ?? ''));
    $phone = trim((string) ($raw['phone'] ?? ''));
    $role = trim((string) ($raw['role'] ?? ''));
    $message = trim((string) ($raw['message'] ?? ''));
    $subject = trim((string) ($raw['subject'] ?? 'Message from yuzu.hair'));

    $trap = trim((string) ($raw['website'] ?? ''));
    $stamp = (int) ($raw['form_ts'] ?? 0);
    if ($trap !== '' || ($stamp > 0 && time() - $stamp < 3)) {
        // Bots get a quiet success so they do not retry.
        if ($wantsJson) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['ok' => true]);
        }
        exit;
    }

    if (!yuzu_round_form_rate_ok()) {
        $fail(429, 'Too many messages in a short time. Please call 020 8840 2244 or email info@yuzuhairandbeauty.co.uk.');
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fail(400, 'Please include a valid email address.');
    }
    if ($message === '' && empty($_FILES['cv']['name'])) {
        $fail(400, 'Please add a short message.');
    }

    $subject = preg_replace('/[\r\n]+/', ' ', $subject) ?: 'Message from yuzu.hair';

    $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
    $contentType = (string) ($_SERVER['CONTENT_TYPE'] ?? '');
    if (
        $contentLength > 0
        && stripos($contentType, 'multipart/form-data') !== false
        && $_POST === []
        && $_FILES === []
    ) {
        $fail(400, 'That file is too large for the server. Please use a PDF, Word or text file under 5 MB.');
    }

    $attachment = null;
    $tmpDir = '';
    $tmpPath = '';
    $file = $_FILES['cv'] ?? null;
    if (is_array($file) && (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $error = (int) $file['error'];
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            $fail(400, 'That file is too large. Please use a PDF, Word or text file under 5 MB.');
        }
        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file((string) $file['tmp_name'])) {
            $fail(400, 'The CV could not be uploaded. Please try again.');
        }
        if ((int) $file['size'] > 5 * 1024 * 1024) {
            $fail(400, 'That file is too large. Please use a PDF, Word or text file under 5 MB.');
        }

        $original = (string) ($file['name'] ?? 'cv');
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $allowed = [
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword', 'application/vnd.ms-office', 'application/octet-stream'],
            'docx' => [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/zip',
                'application/octet-stream',
            ],
            'txt' => ['text/plain', 'text/x-plain', 'application/octet-stream'],
            'rtf' => ['text/rtf', 'application/rtf', 'application/octet-stream'],
        ];
        if (!isset($allowed[$ext])) {
            $fail(400, 'Please attach a PDF, Word or text file.');
        }

        $detected = '';
        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $detected = (string) $finfo->file((string) $file['tmp_name']);
        }
        $blocked = ['text/html', 'application/x-php', 'application/x-httpd-php', 'image/svg+xml', 'application/javascript', 'text/x-php'];
        if ($detected !== '' && (in_array($detected, $blocked, true) || !in_array($detected, $allowed[$ext], true))) {
            $fail(400, 'Please attach a PDF, Word or text file.');
        }

        $bytes = file_get_contents((string) $file['tmp_name']);
        if ($bytes === false || $bytes === '') {
            $fail(400, 'The CV could not be read. Please try again.');
        }
        if (preg_match('/<\?(php|=)/i', substr($bytes, 0, 200)) === 1) {
            $fail(400, 'Please attach a PDF, Word or text file.');
        }

        $safeName = preg_replace('/[^A-Za-z0-9._-]+/', '-', $original) ?: ('cv.' . $ext);
        $safeName = trim($safeName, '.-');
        if (!str_ends_with(strtolower($safeName), '.' . $ext)) {
            $safeName .= '.' . $ext;
        }

        $attachment = [
            'name' => $safeName,
            'mime' => $detected !== '' ? $detected : $allowed[$ext][0],
            'bytes' => $bytes,
        ];
    }

    $body = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n";
    if ($role !== '') {
        $body .= "Role: {$role}\n";
    }
    if ($attachment !== null) {
        $body .= "CV: {$attachment['name']}\n";
    }
    $body .= "\n{$message}\n";

    $sent = false;
    try {
        if (function_exists('wp_mail')) {
            $wpAttachments = [];
            if ($attachment !== null) {
                $tmpDir = rtrim(sys_get_temp_dir(), '/\\') . '/yuzu-cv-' . bin2hex(random_bytes(8));
                if (@mkdir($tmpDir, 0700, true)) {
                    $tmpPath = $tmpDir . '/' . $attachment['name'];
                    if (@file_put_contents($tmpPath, $attachment['bytes']) !== false) {
                        $wpAttachments[] = $tmpPath;
                    }
                }
            }
            $wpHeaders = [
                'Reply-To: ' . $email,
                'Content-Type: text/plain; charset=UTF-8',
            ];
            $fromNameCb = static fn (): string => 'YUZU hair website';
            if (function_exists('add_filter')) {
                add_filter('wp_mail_from_name', $fromNameCb);
            }
            $sent = (bool) @wp_mail($to, $subject, $body, $wpHeaders, $wpAttachments);
            if (function_exists('remove_filter')) {
                remove_filter('wp_mail_from_name', $fromNameCb);
            }
        }

        if (!$sent) {
            $headers = [
                'From: YUZU hair website <' . $from . '>',
                'Reply-To: ' . $email,
                'MIME-Version: 1.0',
            ];
            if ($attachment === null) {
                $headers[] = 'Content-Type: text/plain; charset=UTF-8';
                $payload = $body;
            } else {
                $boundary = 'yuzu_' . bin2hex(random_bytes(8));
                $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';
                $payload = "--{$boundary}\r\n";
                $payload .= "Content-Type: text/plain; charset=UTF-8\r\n";
                $payload .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
                $payload .= $body . "\r\n";
                $payload .= "--{$boundary}\r\n";
                $payload .= 'Content-Type: ' . $attachment['mime'] . '; name="' . $attachment['name'] . "\"\r\n";
                $payload .= "Content-Transfer-Encoding: base64\r\n";
                $payload .= 'Content-Disposition: attachment; filename="' . $attachment['name'] . "\"\r\n\r\n";
                $payload .= chunk_split(base64_encode($attachment['bytes']));
                $payload .= "--{$boundary}--\r\n";
            }
            $sent = (bool) @mail($to, $subject, $payload, implode("\r\n", $headers));
        }
    } finally {
        if ($tmpPath !== '' && is_file($tmpPath)) {
            @unlink($tmpPath);
        }
        if ($tmpDir !== '' && is_dir($tmpDir)) {
            @rmdir($tmpDir);
        }
    }

    if ($wantsJson) {
        if (!$sent) {
            $fail(500, 'The message could not be sent. Please email info@yuzuhairandbeauty.co.uk.');
        }
        if (function_exists('status_header')) {
            status_header(200);
        }
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => true]);
        exit;
    }

    $back = function_exists('home_url') ? home_url('/') : '/';
    $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');
    if ($referer !== '' && function_exists('wp_validate_redirect')) {
        $back = wp_validate_redirect(strtok($referer, '#'), $back);
    }
    header('Location: ' . $back . '#visit');
    exit;
}

/**
 * Five messages per visitor in ten minutes. The IP is hashed with the site
 * salt and only kept in a short-lived transient.
 */
function yuzu_round_form_rate_ok(): bool
{
    if (!function_exists('get_transient') || !function_exists('set_transient')) {
        return true;
    }
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    if ($ip === '') {
        return true;
    }
    $salt = function_exists('wp_salt') ? wp_salt('nonce') : 'yuzu';
    $key = 'yuzu_form_' . substr(hash('sha256', $salt . $ip), 0, 24);
    $count = (int) get_transient($key);
    if ($count >= 5) {
        return false;
    }
    set_transient($key, $count + 1, 10 * 60);
    return true;
}

if (function_exists('add_action')) {
    add_action('wp_ajax_yuzu_send', 'yuzu_round_handle_send_form');
    add_action('wp_ajax_nopriv_yuzu_send', 'yuzu_round_handle_send_form');

    add_filter('wp_sitemaps_enabled', '__return_false');

    add_filter('robots_txt', static function ($output, $public) {
        return yuzu_round_robots_txt();
    }, 99, 2);

    add_action('init', static function (): void {
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
            if ($path === '/send.php') {
                yuzu_round_handle_send_form();
            }
        }
        yuzu_round_serve_version_request();
    }, 0);

    add_action('init', static function (): void {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if ($path !== '/sitemap.xml') {
            return;
        }
        if (function_exists('status_header')) {
            status_header(200);
        }
        header('Content-Type: application/xml; charset=UTF-8');
        echo yuzu_round_sitemap_xml();
        exit;
    }, 0);

    add_action('init', static function (): void {
        try {
            yuzu_round_retire_default_content();
            yuzu_round_sync_root_robots();
            yuzu_round_sync_root_sitemap();
        } catch (Throwable $e) {
            // Cleanup must not take the salon down.
        }
    }, 20);

    add_action('template_redirect', static function (): void {
        if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
            return;
        }
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if (is_string($path) && preg_match('#^/offers(?:\.html|/)?$#i', $path) === 1) {
            wp_redirect(home_url('/#offers'), 301);
            exit;
        }
        if (is_string($path) && preg_match('#^/([A-Za-z0-9-]+)\.html$#', $path, $match) === 1) {
            $file = $match[1] === 'index' ? 'index.html' : $match[1] . '.html';
            $pages = yuzu_round_public_paths();
            if (isset($pages[$file])) {
                wp_redirect(home_url($pages[$file]), 301);
                exit;
            }
        }
        if ((function_exists('is_robots') && is_robots()) || (function_exists('is_favicon') && is_favicon())) {
            return;
        }
        $locked = yuzu_round_locked_file();
        if (yuzu_round_is_salon_front() || $locked !== null) {
            if ($locked !== null) {
                global $wp_query;
                if ($wp_query instanceof WP_Query && $wp_query->is_404) {
                    $wp_query->is_404 = false;
                }
                if (function_exists('status_header')) {
                    status_header(200);
                }
            }
            return;
        }
        yuzu_round_force_404();
    });
}
