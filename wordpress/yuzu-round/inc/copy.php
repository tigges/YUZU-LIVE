<?php
/**
 * Editors for the words on the locked pages. Pictures, buttons and layout stay put.
 */

function yuzu_round_copy_offer_names(): array
{
    return [
        'tue' => 'Colour Tuesdays',
        'wed' => 'Smooth Wednesdays',
        'thu' => 'Colour Thursdays',
        'refer' => 'Refer a Friend',
        'rebook' => 'Same-day rebook',
        'blowdry' => 'Blow-dry package',
    ];
}

function yuzu_round_copy_service_pages(): array
{
    return [
        'haircut-ealing' => 'Haircuts page',
        'hair-colour-ealing' => 'Hair colour page',
        'balayage-ealing' => 'Balayage page',
        'hair-smoothing-ealing' => 'Smoothing page',
    ];
}

/** Visit-card words that appear on every page and are edited once, under Home. */
function yuzu_round_copy_shared_keys(): array
{
    return ['home-address', 'home-hours-title', 'home-book-title'];
}

function yuzu_round_copy_catalog(): array
{
    $line = static function (string $key, string $label, string $group = ''): array {
        return ['key' => $key, 'label' => $label, 'type' => 'line', 'group' => $group];
    };
    $paragraph = static function (string $key, string $label, string $group = ''): array {
        return ['key' => $key, 'label' => $label, 'type' => 'paragraph', 'group' => $group];
    };
    $lines = static function (string $key, string $label, string $group = ''): array {
        return ['key' => $key, 'label' => $label, 'type' => 'lines', 'group' => $group];
    };

    $offer_fields = [];
    foreach (yuzu_round_copy_offer_names() as $offer => $name) {
        $offer_fields[] = $line('offer-' . $offer . '-kicker', 'Label', 'Offer details · ' . $name);
        $offer_fields[] = $line('offer-' . $offer . '-headline', 'Headline', 'Offer details · ' . $name);
        $offer_fields[] = $paragraph('offer-' . $offer . '-text', 'Text', 'Offer details · ' . $name);
        $offer_fields[] = $paragraph('offer-' . $offer . '-terms', 'Small print', 'Offer details · ' . $name);
    }

    $service_pages = [];
    foreach (yuzu_round_copy_service_pages() as $slug => $menu) {
        $fields = [
            $line($slug . '-title', 'Page heading', 'Page'),
            $paragraph($slug . '-intro', 'Introduction', 'Page'),
            $paragraph($slug . '-p1', 'First paragraph', 'Page'),
            $paragraph($slug . '-p2', 'Second paragraph', 'Page'),
        ];
        for ($i = 1; $i <= 3; $i++) {
            $fields[] = $line($slug . '-point-' . $i . '-title', 'Heading', 'Highlight ' . $i);
            $fields[] = $paragraph($slug . '-point-' . $i . '-text', 'Text', 'Highlight ' . $i);
        }
        for ($i = 1; $i <= 4; $i++) {
            $fields[] = $line($slug . '-faq-' . $i . '-q', 'Question', 'Question ' . $i);
            $fields[] = $paragraph($slug . '-faq-' . $i . '-a', 'Answer', 'Question ' . $i);
        }
        $service_pages[$slug] = ['menu' => $menu, 'file' => $slug . '.html', 'fields' => $fields];
    }

    return [
        'home' => [
            'menu' => 'Home',
            'file' => 'index.html',
            'fields' => [
                $line('home-hero-kicker', 'Label', 'Hero'),
                $line('home-hero-title', 'Heading', 'Hero'),
                $paragraph('home-hero-text', 'Text', 'Hero'),
                $line('home-gallery-title', 'Heading', 'Looks'),
                $paragraph('home-gallery-text', 'Text', 'Looks'),
                $line('home-services-title', 'Heading', 'Services'),
                $paragraph('home-services-intro', 'Introduction', 'Services'),
                $line('home-reviews-title', 'Heading', 'Reviews'),
                $paragraph('home-reviews-intro', 'Introduction', 'Reviews'),
                $line('home-review-1-title', 'First quote', 'Reviews'),
                $paragraph('home-review-1-text', 'First review', 'Reviews'),
                $line('home-review-2-title', 'Second quote', 'Reviews'),
                $paragraph('home-review-2-text', 'Second review', 'Reviews'),
                $line('home-review-3-title', 'Third quote', 'Reviews'),
                $paragraph('home-review-3-text', 'Third review', 'Reviews'),
                $line('home-offers-title', 'Heading', 'Offers on the homepage'),
                $paragraph('home-offers-intro', 'Introduction', 'Offers on the homepage'),
                $lines('home-address', 'Address, one line each', 'Visit card on every page'),
                $line('home-hours-title', 'Hours heading', 'Visit card on every page'),
                $line('home-book-title', 'Booking heading', 'Visit card on every page'),
                ...$offer_fields,
            ],
        ],
        'team' => [
            'menu' => 'Join our team',
            'file' => 'team.html',
            'fields' => [
                $line('team-kicker', 'Label', 'Page'),
                $line('team-title', 'Heading', 'Page'),
                $paragraph('team-intro', 'Introduction', 'Page'),
                $paragraph('team-caption', 'Photo caption', 'Page'),
                $line('team-senior-title', 'Heading', 'Senior stylists'),
                $paragraph('team-senior-text', 'Text', 'Senior stylists'),
                $line('team-stylist-title', 'Heading', 'Stylists'),
                $paragraph('team-stylist-text', 'Text', 'Stylists'),
                $line('team-model-title', 'Heading', 'Models'),
                $paragraph('team-model-text', 'Text', 'Models'),
                $line('team-cv-title', 'Form heading', 'CV'),
            ],
        ],
        'about' => [
            'menu' => 'About',
            'file' => 'about.html',
            'fields' => [
                $line('about-title', 'Page heading', 'Page'),
                $paragraph('about-intro', 'Introduction', 'Page'),
                $line('about-welcome-title', 'Heading', 'Welcome'),
                $paragraph('about-p1', 'First paragraph', 'Welcome'),
                $paragraph('about-p2', 'Second paragraph', 'Welcome'),
            ],
        ],
        'questions' => [
            'menu' => 'Questions',
            'file' => 'questions.html',
            'fields' => [
                $line('questions-title', 'Page heading', 'Page'),
                $line('questions-cut-q', 'Question', 'Haircut'),
                $paragraph('questions-cut-a', 'Answer', 'Haircut'),
                $line('questions-patch-q', 'Question', 'Patch test'),
                $paragraph('questions-patch-a', 'Answer', 'Patch test'),
                $line('questions-where-q', 'Question', 'Salon address'),
                $paragraph('questions-where-a', 'Answer', 'Salon address'),
                $line('questions-open-q', 'Question', 'Opening hours'),
                $paragraph('questions-open-a', 'Answer', 'Opening hours'),
                $line('questions-book-q', 'Question', 'Booking'),
                $paragraph('questions-book-a', 'Answer', 'Booking'),
                $line('questions-cancel-q', 'Question', 'Cancellation policy'),
                $paragraph('questions-cancel-a', 'Answer', 'Cancellation policy'),
                $line('questions-offers-q', 'Question', 'Offers and loyalty'),
                $paragraph('questions-offers-a', 'Answer', 'Offers and loyalty'),
                $line('questions-join-q', 'Question', 'Joining the team'),
                $paragraph('questions-join-a', 'Answer', 'Joining the team'),
            ],
        ],
        'patch' => [
            'menu' => 'Patch testing',
            'file' => 'patch.html',
            'fields' => [
                $line('patch-title', 'Page heading', 'Page'),
                $paragraph('patch-intro', 'Introduction', 'Page'),
                $paragraph('patch-p1', 'First paragraph', 'Details'),
                $paragraph('patch-p2', 'Second paragraph', 'Details'),
                $paragraph('patch-p3', 'Third paragraph', 'Details'),
                $paragraph('patch-p4', 'Closing line', 'Details'),
            ],
        ],
        'contact' => [
            'menu' => 'Contact',
            'file' => 'contact.html',
            'fields' => [
                $line('contact-title', 'Page heading', 'Page'),
                $line('contact-thanks-title', 'Thank-you heading', 'After the form'),
                $paragraph('contact-thanks-text', 'Thank-you text', 'After the form'),
            ],
        ],
        'terms' => [
            'menu' => 'Terms',
            'file' => 'terms.html',
            'fields' => [
                $line('terms-title', 'Page heading', 'Page'),
                $paragraph('terms-intro', 'Introduction', 'Page'),
                $line('terms-arrival-title', 'Heading', 'Arrival'),
                $paragraph('terms-arrival', 'Text', 'Arrival'),
                $line('terms-patch-title', 'Heading', 'Skin patch test'),
                $paragraph('terms-patch', 'Text', 'Skin patch test'),
                $line('terms-cancel-title', 'Heading', 'Cancellation'),
                $paragraph('terms-cancel-1', 'First paragraph', 'Cancellation'),
                $paragraph('terms-cancel-2', 'Second paragraph', 'Cancellation'),
                $line('terms-offers-title', 'Heading', 'Promotions and loyalty offers'),
                $paragraph('terms-offers', 'Text', 'Promotions and loyalty offers'),
                $line('terms-redo-title', 'Heading', 'Redos and refunds'),
                $paragraph('terms-redo-1', 'First paragraph', 'Redos and refunds'),
                $paragraph('terms-redo-2', 'Second paragraph', 'Redos and refunds'),
                $paragraph('terms-redo-3', 'Third paragraph', 'Redos and refunds'),
                $line('terms-product-title', 'Heading', 'Product refunds'),
                $paragraph('terms-product', 'Text', 'Product refunds'),
                $line('terms-children-title', 'Heading', 'Children’s services'),
                $paragraph('terms-children', 'Text', 'Children’s services'),
                $line('terms-online-title', 'Heading', 'Online booking'),
                $paragraph('terms-online-1', 'First paragraph', 'Online booking'),
                $paragraph('terms-online-2', 'Second paragraph', 'Online booking'),
            ],
        ],
        ...$service_pages,
        'privacy' => [
            'menu' => 'Privacy',
            'file' => 'privacy.html',
            'fields' => [
                $line('privacy-title', 'Page heading', 'Page'),
                $paragraph('privacy-intro', 'Introduction', 'Page'),
                $line('privacy-who-title', 'Heading', 'Who we are'),
                $line('privacy-site-title', 'Heading', 'This website'),
                $paragraph('privacy-site', 'Text', 'This website'),
                $line('privacy-book-title', 'Heading', 'Booking'),
                $paragraph('privacy-book', 'Text', 'Booking'),
                $line('privacy-messages-title', 'Heading', 'Messages'),
                $paragraph('privacy-messages', 'Text', 'Messages'),
                $line('privacy-maps-title', 'Heading', 'Maps and social networks'),
                $paragraph('privacy-maps', 'Text', 'Maps and social networks'),
                $line('privacy-ask-title', 'Heading', 'Ask us'),
            ],
        ],
    ];
}

function yuzu_round_copy_field(string $slug, string $key): ?array
{
    foreach (yuzu_round_copy_catalog()[$slug]['fields'] ?? [] as $field) {
        if (($field['key'] ?? '') === $key) {
            return $field;
        }
    }
    return null;
}

function yuzu_round_copy_clip(string $text, int $max): string
{
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags($text)) ?? '');
    if ($text === '' || $max < 1) {
        return '';
    }
    if (preg_match_all('/./u', $text, $chars) < 1) {
        return '';
    }
    if (count($chars[0]) <= $max) {
        return $text;
    }
    return implode('', array_slice($chars[0], 0, $max));
}

function yuzu_round_copy_clean(string $raw, string $type): string
{
    $raw = str_replace(["\r\n", "\r"], "\n", strip_tags($raw));
    if ($type === 'lines') {
        $lines = [];
        foreach (preg_split('/\n/u', $raw) ?: [] as $line) {
            $line = yuzu_round_copy_clip($line, 160);
            if ($line !== '') {
                $lines[] = $line;
            }
            if (count($lines) >= 8) {
                break;
            }
        }
        return implode("\n", $lines);
    }
    $max = $type === 'line' ? 180 : 1200;
    return yuzu_round_copy_clip($raw, $max);
}

function yuzu_round_copy_esc(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function yuzu_round_copy_inner_html(string $text, string $type): string
{
    if ($type === 'lines') {
        $parts = [];
        foreach (preg_split('/\n/u', $text) ?: [] as $line) {
            if ($line !== '') {
                $parts[] = yuzu_round_copy_esc($line);
            }
        }
        return implode("<br />\n", $parts);
    }
    return yuzu_round_copy_esc($text);
}

function yuzu_round_copy_read_value(string $html, string $key, string $type): string
{
    $pattern = '/<([a-z0-9]+)(\s[^>]*?\sdata-yuzu-copy="' . preg_quote($key, '/') . '"[^>]*)>(.*?)<\/\1>/s';
    if (!preg_match($pattern, $html, $matches)) {
        $pattern = '/<([a-z0-9]+)([^>]*data-yuzu-copy="' . preg_quote($key, '/') . '"[^>]*)>(.*?)<\/\1>/s';
        if (!preg_match($pattern, $html, $matches)) {
            return '';
        }
    }
    $inner = preg_replace('/<br\s*\/?>/i', "\n", $matches[3]) ?? $matches[3];
    $text = html_entity_decode(strip_tags($inner), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = str_replace("\xC2\xA0", ' ', $text);
    return yuzu_round_copy_clean($text, $type);
}

function yuzu_round_copy_replace_value(string $html, string $key, string $inner): string
{
    $pattern = '/(<[a-z0-9]+\s[^>]*?\sdata-yuzu-copy="' . preg_quote($key, '/') . '"[^>]*>)(.*?)(<\/[a-z0-9]+>)/s';
    $put = static fn (array $m): string => $m[1] . $inner . $m[3];
    $count = 0;
    $next = preg_replace_callback($pattern, $put, $html, 1, $count);
    if (!is_string($next) || $count !== 1) {
        $pattern = '/(<[a-z0-9]+[^>]*data-yuzu-copy="' . preg_quote($key, '/') . '"[^>]*>)(.*?)(<\/[a-z0-9]+>)/s';
        $count = 0;
        $next = preg_replace_callback($pattern, $put, $html, 1, $count);
    }
    return is_string($next) && $count === 1 ? $next : $html;
}

function yuzu_round_copy_apply_values(string $html, string $slug, array $values): string
{
    foreach (yuzu_round_copy_catalog()[$slug]['fields'] ?? [] as $field) {
        $key = $field['key'];
        if (!isset($values[$key]) || !is_string($values[$key]) || $values[$key] === '') {
            continue;
        }
        $html = yuzu_round_copy_replace_value($html, $key, yuzu_round_copy_inner_html($values[$key], $field['type']));
    }
    if ($slug === 'questions') {
        $html = yuzu_round_copy_sync_faq($html, $values, [
            ['questions-cut-q', 'questions-cut-a'],
            ['questions-patch-q', 'questions-patch-a'],
            ['questions-where-q', 'questions-where-a'],
            ['questions-open-q', 'questions-open-a'],
            ['questions-book-q', 'questions-book-a'],
            ['questions-cancel-q', 'questions-cancel-a'],
            ['questions-offers-q', 'questions-offers-a'],
            ['questions-join-q', 'questions-join-a'],
        ]);
    }
    if (isset(yuzu_round_copy_service_pages()[$slug])) {
        $pairs = [];
        for ($i = 1; $i <= 4; $i++) {
            $pairs[] = [$slug . '-faq-' . $i . '-q', $slug . '-faq-' . $i . '-a'];
        }
        $html = yuzu_round_copy_sync_faq($html, $values, $pairs);
    }
    return $html;
}

function yuzu_round_copy_sync_faq(string $html, array $values, array $pairs): string
{
    $pattern = '/<script type="application\/ld\+json">\s*(\{(?:(?!<\/script>).)*?"@type": "FAQPage"(?:(?!<\/script>).)*?\})\s*<\/script>/s';
    if (!preg_match($pattern, $html, $matches)) {
        return $html;
    }
    $data = json_decode($matches[1], true);
    if (!is_array($data) || !isset($data['mainEntity']) || !is_array($data['mainEntity'])) {
        return $html;
    }
    foreach ($pairs as $index => $pair) {
        if (!isset($data['mainEntity'][$index]) || !is_array($data['mainEntity'][$index])) {
            continue;
        }
        if (!empty($values[$pair[0]]) && is_string($values[$pair[0]])) {
            $data['mainEntity'][$index]['name'] = $values[$pair[0]];
        }
        if (!empty($values[$pair[1]]) && is_string($values[$pair[1]]) && isset($data['mainEntity'][$index]['acceptedAnswer']) && is_array($data['mainEntity'][$index]['acceptedAnswer'])) {
            $data['mainEntity'][$index]['acceptedAnswer']['text'] = $values[$pair[1]];
        }
    }
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
    if (!is_string($json)) {
        return $html;
    }
    $json = preg_replace('/^(.)/m', '      $1', $json) ?? $json;
    return str_replace($matches[1], ltrim($json), $html);
}

function yuzu_round_copy_source(string $file): string
{
    $paths = [];
    if (function_exists('yuzu_round_static_dir')) {
        $paths[] = yuzu_round_static_dir() . '/' . basename($file);
    }
    if (function_exists('get_template_directory')) {
        $paths[] = get_template_directory() . '/static/' . basename($file);
    }
    $paths[] = dirname(__DIR__) . '/static/' . basename($file);
    $paths[] = dirname(__DIR__, 3) . '/site/' . basename($file);
    foreach ($paths as $path) {
        if (is_readable($path)) {
            $html = file_get_contents($path);
            if (is_string($html)) {
                return $html;
            }
        }
    }
    return '';
}

function yuzu_round_copy_current(string $slug): array
{
    $meta = yuzu_round_copy_catalog()[$slug] ?? null;
    if (!is_array($meta)) {
        return [];
    }
    $html = yuzu_round_copy_source($meta['file']);
    $stored = [];
    if (function_exists('get_option')) {
        $all = get_option('yuzu_round_copy');
        if (is_array($all) && isset($all[$slug]) && is_array($all[$slug])) {
            $stored = $all[$slug];
        }
    }
    $values = [];
    foreach ($meta['fields'] as $field) {
        $saved = $stored[$field['key']] ?? null;
        if (is_string($saved) && $saved !== '') {
            $values[$field['key']] = yuzu_round_copy_clean($saved, $field['type']);
            continue;
        }
        $values[$field['key']] = yuzu_round_copy_read_value($html, $field['key'], $field['type']);
    }
    return $values;
}

function yuzu_round_copy_sanitize_page(string $slug, mixed $input, array $previous): array
{
    $clean = [];
    foreach (yuzu_round_copy_catalog()[$slug]['fields'] ?? [] as $field) {
        $raw = is_array($input) ? (string) ($input[$field['key']] ?? '') : '';
        $value = yuzu_round_copy_clean($raw, $field['type']);
        if ($value === '') {
            $value = (string) ($previous[$field['key']] ?? '');
        }
        if ($value !== '') {
            $clean[$field['key']] = $value;
        }
    }
    return $clean;
}

function yuzu_round_copy_rename_gallery_heading(): void
{
    if (!function_exists('get_option') || !function_exists('update_option')) {
        return;
    }
    $stored = get_option('yuzu_round_copy');
    if (!is_array($stored)) {
        return;
    }
    $changed = false;
    if (($stored['home']['home-gallery-title'] ?? '') === 'Gallery') {
        $stored['home']['home-gallery-title'] = 'Looks';
        $changed = true;
    }
    if (($stored['home']['home-offers-intro'] ?? '') === 'Tuesday, Wednesday and Thursday.') {
        $stored['home']['home-offers-intro'] = 'Weekday colour and smoothing offers, plus ongoing salon rewards — tap any offer for details.';
        $changed = true;
    }
    if (isset($stored['about']['about-p2']) && !str_contains((string) $stored['about']['about-p2'], 'Ealing Town Hall')) {
        $stored['about']['about-p2'] = 'Located in Dickens Yard behind Ealing Town Hall, a 4-minute walk from Ealing Broadway station (Elizabeth, Central and District lines) with Dickens Yard underground car park right below the courtyard. Open Tuesday to Friday 10am to 8pm and Saturday 9am to 6pm. Guests describe the room as calm, and one Google review calls Jasmine “a brilliant artist working in hair”.';
        $changed = true;
    }
    if (isset($stored['offers']) && is_array($stored['offers'])) {
        $legacy_tue = ($stored['offers']['offers-tue-kicker'] ?? '') === 'Colour Tuesdays · Senior stylist';
        $legacy_thu = ($stored['offers']['offers-thu-kicker'] ?? '') === 'Thursdays · Stylist';
        if ($legacy_tue || $legacy_thu) {
            unset($stored['offers']);
            $changed = true;
        }
    }
    $legacy_offer = static fn ($text): bool => is_string($text)
        && (str_contains($text, 'Senior Stylist or Stylist') || str_contains($text, 'Aura smoothing') || str_contains($text, '3 to 6 month'));
    foreach ([['questions', 'questions-offers-a'], ['terms', 'terms-offers']] as [$page, $key]) {
        if (isset($stored[$page][$key]) && $legacy_offer($stored[$page][$key])) {
            unset($stored[$page][$key]);
            $changed = true;
        }
    }
    if ($changed) {
        update_option('yuzu_round_copy', $stored);
    }
}

function yuzu_round_copy_apply(string $html, string $file): string
{
    if (!function_exists('get_option')) {
        return $html;
    }
    yuzu_round_copy_rename_gallery_heading();
    $stored = get_option('yuzu_round_copy');
    if (!is_array($stored)) {
        return $html;
    }
    $file = basename($file);
    foreach (yuzu_round_copy_catalog() as $slug => $meta) {
        if ($meta['file'] !== $file || !isset($stored[$slug]) || !is_array($stored[$slug])) {
            continue;
        }
        $html = yuzu_round_copy_apply_values($html, $slug, $stored[$slug]);
    }
    if ($file !== 'index.html' && isset($stored['home']) && is_array($stored['home'])) {
        foreach (yuzu_round_copy_shared_keys() as $key) {
            $field = yuzu_round_copy_field('home', $key);
            $value = $stored['home'][$key] ?? '';
            if ($field !== null && is_string($value) && $value !== '') {
                $html = yuzu_round_copy_replace_value($html, $key, yuzu_round_copy_inner_html($value, $field['type']));
            }
        }
    }
    return $html;
}

function yuzu_round_copy_mark_edited(string $slug): void
{
    if (function_exists('yuzu_round_mark_edited')) {
        yuzu_round_mark_edited($slug === 'home' ? array_keys(yuzu_round_pages()) : [$slug]);
    }
}

function yuzu_round_copy_admin_slug(string $page): string
{
    if ($page === 'yuzu-copy') {
        return 'home';
    }
    if (str_starts_with($page, 'yuzu-copy-')) {
        return substr($page, strlen('yuzu-copy-'));
    }
    return '';
}

function yuzu_round_copy_admin_url(string $slug): string
{
    $page = $slug === 'home' ? 'yuzu-copy' : 'yuzu-copy-' . $slug;
    return admin_url('admin.php?page=' . $page);
}

function yuzu_round_copy_admin_page(): void
{
    if (!current_user_can('edit_pages')) {
        wp_die(esc_html__('Sorry, you are not allowed to edit these pages.', 'yuzu-round'), '', ['response' => 403]);
    }
    $slug = yuzu_round_copy_admin_slug((string) ($_GET['page'] ?? ''));
    $meta = yuzu_round_copy_catalog()[$slug] ?? null;
    if (!is_array($meta)) {
        echo '<div class="wrap"><h1>Page copy</h1><p>Choose a page from the menu.</p></div>';
        return;
    }
    $values = yuzu_round_copy_current($slug);
    $saved = isset($_GET['yuzu_saved']) && $_GET['yuzu_saved'] === '1';
    $reset = isset($_GET['yuzu_reset']) && $_GET['yuzu_reset'] === '1';
    $cache = isset($_GET['yuzu_cache']) && $_GET['yuzu_cache'] === '1';
    $view = '';
    if (function_exists('get_page_by_path')) {
        $post = get_page_by_path($slug === 'home' ? 'home' : $slug);
        if ($post instanceof WP_Post) {
            $permalink = get_permalink($post);
            $view = is_string($permalink) ? $permalink : '';
        }
    }
    echo '<div class="wrap yuzu-copy">';
    echo '<h1>' . esc_html($meta['menu']) . '</h1>';
    if ($saved) {
        echo '<div class="notice notice-success is-dismissible"><p>Saved. The public page now uses these words. ';
        echo $cache ? 'The Breeze cache was cleared.' : 'Purge the Breeze cache if the old words are still showing.';
        echo '</p></div>';
    }
    if ($reset) {
        echo '<div class="notice notice-success is-dismissible"><p>The original words for this page are back on the site.</p></div>';
    }
    echo '<p class="yuzu-copy-lead">Edit the words, then press Save. Pictures, buttons and the page design stay as they are.</p>';
    if ($view !== '') {
        echo '<p><a class="button" href="' . esc_url($view) . '" target="_blank" rel="noopener">View page</a></p>';
    }
    echo '<form method="post">';
    wp_nonce_field('yuzu_round_copy_' . $slug);
    echo '<input type="hidden" name="yuzu_copy_action" value="save" />';
    $group = '';
    foreach ($meta['fields'] as $field) {
        if ($field['group'] !== '' && $field['group'] !== $group) {
            $group = $field['group'];
            echo '<h2>' . esc_html($group) . '</h2>';
            if ($group === 'Visit card on every page') {
                echo '<p class="description">Opening hours, holidays and the notice banner are set under <a href="' . esc_url(admin_url('admin.php?page=yuzu-salon')) . '">Salon details</a>.</p>';
            }
        }
        $id = 'yuzu-copy-' . $field['key'];
        $value = (string) ($values[$field['key']] ?? '');
        echo '<p><label for="' . esc_attr($id) . '"><strong>' . esc_html($field['label']) . '</strong></label><br />';
        if ($field['type'] === 'line') {
            echo '<input class="large-text" id="' . esc_attr($id) . '" type="text" name="yuzu_copy[' . esc_attr($field['key']) . ']" value="' . esc_attr($value) . '" />';
        } else {
            $rows = $field['type'] === 'lines' ? 4 : 5;
            echo '<textarea class="large-text" id="' . esc_attr($id) . '" name="yuzu_copy[' . esc_attr($field['key']) . ']" rows="' . esc_attr((string) $rows) . '">' . esc_textarea($value) . '</textarea>';
        }
        echo '</p>';
    }
    submit_button('Save');
    echo '</form>';
    echo '<form method="post" class="yuzu-copy-reset">';
    wp_nonce_field('yuzu_round_copy_reset_' . $slug);
    echo '<input type="hidden" name="yuzu_copy_action" value="reset" />';
    submit_button('Restore original words', 'secondary');
    echo '</form></div>';
    echo <<<'JS'
<script>
document.querySelectorAll(".yuzu-copy-reset").forEach(function (form) {
  form.addEventListener("submit", function (event) {
    if (!window.confirm("Restore the original words for this page?")) event.preventDefault();
  });
});
</script>
JS;
}

function yuzu_round_copy_handle_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !isset($_POST['yuzu_copy_action'])) {
        return;
    }
    $admin_page = (string) ($_GET['page'] ?? $_POST['page'] ?? '');
    if (!str_starts_with($admin_page, 'yuzu-copy')) {
        return;
    }
    if (!current_user_can('edit_pages')) {
        return;
    }
    $slug = yuzu_round_copy_admin_slug($admin_page);
    if (!isset(yuzu_round_copy_catalog()[$slug])) {
        return;
    }
    $action = (string) $_POST['yuzu_copy_action'];
    $url = yuzu_round_copy_admin_url($slug);
    if ($action === 'save') {
        check_admin_referer('yuzu_round_copy_' . $slug);
        $all = get_option('yuzu_round_copy');
        if (!is_array($all)) {
            $all = [];
        }
        $previous = yuzu_round_copy_current($slug);
        $all[$slug] = yuzu_round_copy_sanitize_page($slug, wp_unslash($_POST['yuzu_copy'] ?? null), $previous);
        update_option('yuzu_round_copy', $all, false);
        yuzu_round_copy_mark_edited($slug);
        $cleared = function_exists('yuzu_round_prices_purge_cache') && yuzu_round_prices_purge_cache();
        wp_safe_redirect(add_query_arg(['yuzu_saved' => '1', 'yuzu_cache' => $cleared ? '1' : '0'], $url));
        exit;
    }
    if ($action === 'reset') {
        check_admin_referer('yuzu_round_copy_reset_' . $slug);
        $all = get_option('yuzu_round_copy');
        if (is_array($all)) {
            unset($all[$slug]);
            update_option('yuzu_round_copy', $all, false);
        }
        yuzu_round_copy_mark_edited($slug);
        if (function_exists('yuzu_round_prices_purge_cache')) {
            yuzu_round_prices_purge_cache();
        }
        wp_safe_redirect(add_query_arg('yuzu_reset', '1', $url));
        exit;
    }
}

function yuzu_round_copy_admin_assets(): void
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen) {
        return;
    }
    $id = (string) $screen->id;
    if ($id !== 'toplevel_page_yuzu-copy' && !str_starts_with($id, 'page-copy_page_yuzu-copy')) {
        return;
    }
    echo '<style>
      .yuzu-copy .yuzu-copy-lead { max-width: 720px; }
      .yuzu-copy h2 { margin-top: 1.6em; }
      .yuzu-copy textarea.large-text { max-width: 720px; }
      .yuzu-copy input.large-text { max-width: 720px; }
    </style>';
}

if (function_exists('add_action')) {
    add_action('admin_menu', static function (): void {
        add_menu_page(
            'Page copy',
            'Page copy',
            'edit_pages',
            'yuzu-copy',
            'yuzu_round_copy_admin_page',
            'dashicons-edit-page',
            27
        );
        add_submenu_page('yuzu-copy', 'Home', 'Home', 'edit_pages', 'yuzu-copy', 'yuzu_round_copy_admin_page');
        foreach (yuzu_round_copy_catalog() as $slug => $meta) {
            if ($slug === 'home') {
                continue;
            }
            add_submenu_page(
                'yuzu-copy',
                $meta['menu'],
                $meta['menu'],
                'edit_pages',
                'yuzu-copy-' . $slug,
                'yuzu_round_copy_admin_page'
            );
        }
    });
    add_action('admin_init', 'yuzu_round_copy_handle_post');
    add_action('admin_head', 'yuzu_round_copy_admin_assets');
}
