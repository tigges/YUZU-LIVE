<?php
/**
 * Editors for the words on the locked pages. Pictures, buttons and layout stay put.
 */

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

    return [
        'home' => [
            'menu' => 'Home',
            'file' => 'index.html',
            'fields' => [
                $line('home-hero-kicker', 'Label', 'Hero'),
                $line('home-hero-title', 'Heading', 'Hero'),
                $paragraph('home-hero-text', 'Text', 'Hero'),
                $line('home-gallery-title', 'Heading', 'Gallery'),
                $paragraph('home-gallery-text', 'Text', 'Gallery'),
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
                $line('home-offer-tue-kicker', 'Tuesday label', 'Offers on the homepage'),
                $line('home-offer-tue-title', 'Tuesday title', 'Offers on the homepage'),
                $paragraph('home-offer-tue-text', 'Tuesday text', 'Offers on the homepage'),
                $line('home-offer-wed-kicker', 'Wednesday label', 'Offers on the homepage'),
                $line('home-offer-wed-title', 'Wednesday title', 'Offers on the homepage'),
                $paragraph('home-offer-wed-text', 'Wednesday text', 'Offers on the homepage'),
                $line('home-offer-thu-kicker', 'Thursday label', 'Offers on the homepage'),
                $line('home-offer-thu-title', 'Thursday title', 'Offers on the homepage'),
                $paragraph('home-offer-thu-text', 'Thursday text', 'Offers on the homepage'),
                $line('home-extra-refer-title', 'Refer a friend title', 'Offers on the homepage'),
                $paragraph('home-extra-refer-text', 'Refer a friend text', 'Offers on the homepage'),
                $line('home-extra-rebook-title', 'Rebook title', 'Offers on the homepage'),
                $paragraph('home-extra-rebook-text', 'Rebook text', 'Offers on the homepage'),
                $line('home-extra-blow-title', 'Blow-dry package title', 'Offers on the homepage'),
                $paragraph('home-extra-blow-text', 'Blow-dry package text', 'Offers on the homepage'),
                $line('home-team-title', 'Heading', 'Join our team'),
                $paragraph('home-team-text', 'Text', 'Join our team'),
                $lines('home-address', 'Address, one line each', 'Visit'),
                $line('home-hours-title', 'Hours heading', 'Visit'),
                $line('home-hours-week-label', 'Weekday label', 'Visit'),
                $line('home-hours-week', 'Weekday hours', 'Visit'),
                $line('home-hours-sat-label', 'Saturday label', 'Visit'),
                $line('home-hours-sat', 'Saturday hours', 'Visit'),
                $line('home-hours-sun-label', 'Closed days label', 'Visit'),
                $line('home-hours-sun', 'Closed days text', 'Visit'),
                $line('home-book-title', 'Booking heading', 'Visit'),
            ],
        ],
        'offers' => [
            'menu' => 'Offers',
            'file' => 'offers.html',
            'fields' => [
                $line('offers-title', 'Page heading', 'Page'),
                $paragraph('offers-intro', 'Introduction', 'Page'),
                $line('offers-tue-kicker', 'Label', 'Colour Tuesdays'),
                $line('offers-tue-title', 'Heading', 'Colour Tuesdays'),
                $paragraph('offers-tue-p1', 'First paragraph', 'Colour Tuesdays'),
                $paragraph('offers-tue-p2', 'Second paragraph', 'Colour Tuesdays'),
                $line('offers-wed-kicker', 'Label', 'Smooth Wednesdays'),
                $line('offers-wed-title', 'Heading', 'Smooth Wednesdays'),
                $paragraph('offers-wed-p1', 'First paragraph', 'Smooth Wednesdays'),
                $paragraph('offers-wed-p2', 'Second paragraph', 'Smooth Wednesdays'),
                $line('offers-thu-kicker', 'Label', 'Thursdays'),
                $line('offers-thu-title', 'Heading', 'Thursdays'),
                $paragraph('offers-thu-p1', 'First paragraph', 'Thursdays'),
                $paragraph('offers-thu-p2', 'Second paragraph', 'Thursdays'),
                $line('offers-refer-title', 'Heading', 'Refer a friend'),
                $paragraph('offers-refer-1', 'Step 1', 'Refer a friend'),
                $paragraph('offers-refer-2', 'Step 2', 'Refer a friend'),
                $paragraph('offers-refer-3', 'Step 3', 'Refer a friend'),
                $paragraph('offers-refer-4', 'Step 4', 'Refer a friend'),
                $paragraph('offers-refer-end', 'Closing line', 'Refer a friend'),
                $line('offers-rebook-title', 'Heading', 'Rebook the same day'),
                $paragraph('offers-rebook-text', 'Text', 'Rebook the same day'),
                $line('offers-blow-title', 'Heading', 'Blow-dry package'),
                $paragraph('offers-blow-text', 'Text', 'Blow-dry package'),
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
                $line('terms-redo-title', 'Heading', 'Redo’s and refunds'),
                $paragraph('terms-redo-1', 'First paragraph', 'Redo’s and refunds'),
                $paragraph('terms-redo-2', 'Second paragraph', 'Redo’s and refunds'),
                $paragraph('terms-redo-3', 'Third paragraph', 'Redo’s and refunds'),
                $line('terms-product-title', 'Heading', 'Product refunds'),
                $paragraph('terms-product', 'Text', 'Product refunds'),
                $line('terms-children-title', 'Heading', 'Children’s services'),
                $paragraph('terms-children', 'Text', 'Children’s services'),
                $line('terms-online-title', 'Heading', 'Online booking'),
                $paragraph('terms-online-1', 'First paragraph', 'Online booking'),
                $paragraph('terms-online-2', 'Second paragraph', 'Online booking'),
            ],
        ],
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
    $count = 0;
    $next = preg_replace($pattern, '$1' . $inner . '$3', $html, 1, $count);
    if (!is_string($next) || $count !== 1) {
        $pattern = '/(<[a-z0-9]+[^>]*data-yuzu-copy="' . preg_quote($key, '/') . '"[^>]*>)(.*?)(<\/[a-z0-9]+>)/s';
        $count = 0;
        $next = preg_replace($pattern, '$1' . $inner . '$3', $html, 1, $count);
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
        $html = yuzu_round_copy_sync_faq($html, $values);
    }
    return $html;
}

function yuzu_round_copy_sync_faq(string $html, array $values): string
{
    $pairs = [
        ['questions-cut-q', 'questions-cut-a'],
        ['questions-patch-q', 'questions-patch-a'],
        ['questions-where-q', 'questions-where-a'],
        ['questions-open-q', 'questions-open-a'],
        ['questions-book-q', 'questions-book-a'],
        ['questions-join-q', 'questions-join-a'],
    ];
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

function yuzu_round_copy_apply(string $html, string $file): string
{
    if (!function_exists('get_option')) {
        return $html;
    }
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
    return $html;
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
