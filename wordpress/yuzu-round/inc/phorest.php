<?php
/**
 * Booking links into Phorest. phorest.json is the online menu, refreshed by
 * scripts/phorest_sync.py; phorest-links.json says which Phorest service each
 * price books. wp-admin → Price list → Booking links overrides single prices.
 */

const YUZU_ROUND_PHOREST_BOOK = 'https://www.phorest.com/salon/yuzuhairandbeauty';

function yuzu_round_phorest_json(string $file): array
{
    $path = __DIR__ . '/' . $file;
    if (!is_readable($path)) {
        return [];
    }
    $data = json_decode((string) file_get_contents($path), true);
    return is_array($data) ? $data : [];
}

function yuzu_round_phorest_menu(): array
{
    static $menu = null;
    if ($menu === null) {
        $menu = yuzu_round_phorest_json('phorest.json') + ['book' => YUZU_ROUND_PHOREST_BOOK, 'categories' => [], 'services' => [], 'staff' => []];
    }
    return $menu;
}

function yuzu_round_phorest_defaults(): array
{
    static $links = null;
    if ($links === null) {
        $links = yuzu_round_phorest_json('phorest-links.json') + ['levels' => [], 'rows' => []];
    }
    return $links;
}

function yuzu_round_phorest_base(): string
{
    $book = (string) (yuzu_round_phorest_menu()['book'] ?? '');
    return str_starts_with($book, 'https://www.phorest.com/salon/') ? rtrim($book, '/') : YUZU_ROUND_PHOREST_BOOK;
}

function yuzu_round_phorest_category_id(string $name): string
{
    foreach (yuzu_round_phorest_menu()['categories'] as $cat) {
        if ((string) ($cat['name'] ?? '') === $name) {
            return (string) $cat['id'];
        }
    }
    return '';
}

/** The Phorest service id for a “Category / Service” reference, or ''. */
function yuzu_round_phorest_service_id(string $ref): string
{
    [$cat, $name] = array_pad(explode(' / ', $ref, 2), 2, '');
    foreach (yuzu_round_phorest_menu()['services'] as $svc) {
        if ((string) ($svc['category'] ?? '') === $cat && (string) ($svc['name'] ?? '') === $name) {
            return (string) $svc['id'];
        }
    }
    return '';
}

function yuzu_round_phorest_has(string $kind, string $id): bool
{
    $list = yuzu_round_phorest_menu()[$kind === 'svc' ? 'services' : 'categories'];
    foreach ($list as $item) {
        if ((string) ($item['id'] ?? '') === $id) {
            return true;
        }
    }
    return false;
}

/** Stored overrides: [group][row name][level] => 'main' | 'cat:ID' | 'svc:ID'. */
function yuzu_round_phorest_overrides(): array
{
    if (!function_exists('get_option')) {
        return [];
    }
    $stored = get_option('yuzu_round_phorest_links');
    return is_array($stored) ? $stored : [];
}

/** The theme's own choice for one price, as 'svc:ID', 'cat:ID' or 'main'. */
function yuzu_round_phorest_default_choice(string $group, string $row, string $level): string
{
    $links = yuzu_round_phorest_defaults();
    $ref = (string) ($links['rows'][$group][$row][$level] ?? '');
    $id = $ref === '' ? '' : yuzu_round_phorest_service_id($ref);
    if ($id !== '') {
        return 'svc:' . $id;
    }
    $cat = (string) ($links['levels'][$level][$group] ?? '');
    $id = $cat === '' ? '' : yuzu_round_phorest_category_id($cat);
    return $id !== '' ? 'cat:' . $id : 'main';
}

function yuzu_round_phorest_choice(string $group, string $row, string $level, ?array $overrides = null): string
{
    $overrides ??= yuzu_round_phorest_overrides();
    $choice = (string) ($overrides[$group][$row][$level] ?? '');
    if ($choice === 'main') {
        return 'main';
    }
    if (preg_match('/^(svc|cat):([A-Za-z0-9_-]+)$/', $choice, $m) === 1 && yuzu_round_phorest_has($m[1], $m[2])) {
        return $choice;
    }
    return yuzu_round_phorest_default_choice($group, $row, $level);
}

function yuzu_round_phorest_href(string $choice): string
{
    $base = yuzu_round_phorest_base();
    if (preg_match('/^(svc|cat):([A-Za-z0-9_-]+)$/', $choice, $m) !== 1) {
        return $base;
    }
    return $base . ($m[1] === 'svc' ? '/book/services/' : '/book/categories/') . $m[2];
}

/** Where the Book link for one price goes. $level is a chair key, or 'price' for one-price lists. */
function yuzu_round_phorest_book(string $group, string $row, string $level): string
{
    return yuzu_round_phorest_href(yuzu_round_phorest_choice($group, $row, $level));
}

/** Price list groups and the levels that can take a booking link. */
function yuzu_round_phorest_rows(): array
{
    $prices = yuzu_round_prices_current();
    $out = [];
    foreach (yuzu_round_prices_catalog() as $id => $meta) {
        if ($id === 'packages') {
            continue;
        }
        $levels = $meta['kind'] === 'list' ? ['price' => 'Price'] : array_map(static fn ($c) => $c['label'], $meta['columns']);
        $rows = [];
        foreach ($prices['groups'][$id] ?? [] as $row) {
            $cells = [];
            foreach ($levels as $key => $label) {
                $display = (string) ($row[$key] ?? '—');
                if ($display !== '—') {
                    $cells[$key] = $display;
                }
            }
            if ($cells !== []) {
                $rows[(string) $row['name']] = $cells;
            }
        }
        $out[$id] = ['title' => $meta['title'], 'levels' => $levels, 'rows' => $rows];
    }
    return $out;
}

function yuzu_round_phorest_sanitize(mixed $input): array
{
    if (!is_array($input)) {
        return [];
    }
    $clean = [];
    foreach (yuzu_round_phorest_rows() as $group => $meta) {
        foreach ($meta['rows'] as $row => $cells) {
            foreach (array_keys($cells) as $level) {
                $value = (string) ($input[$group][md5($row)][$level] ?? '');
                if ($value === '' || $value === yuzu_round_phorest_default_choice($group, $row, $level)) {
                    continue;
                }
                if ($value === 'main' || (preg_match('/^(svc|cat):([A-Za-z0-9_-]+)$/', $value, $m) === 1 && yuzu_round_phorest_has($m[1], $m[2]))) {
                    $clean[$group][$row][$level] = $value;
                }
            }
        }
    }
    return $clean;
}

function yuzu_round_phorest_money(float $price): string
{
    return '£' . rtrim(rtrim(number_format($price, 2, '.', ''), '0'), '.');
}

function yuzu_round_phorest_choice_label(string $choice): string
{
    if ($choice === 'main' || $choice === '') {
        return 'Main booking page';
    }
    if ($choice === 'voucher') {
        return 'Gift vouchers';
    }
    if ($choice === 'course') {
        return 'Series and packages';
    }
    [$kind, $id] = array_pad(explode(':', $choice, 2), 2, '');
    foreach (yuzu_round_phorest_menu()[$kind === 'svc' ? 'services' : 'categories'] as $item) {
        if ((string) $item['id'] === $id) {
            return $kind === 'svc' ? $item['category'] . ' / ' . $item['name'] : $item['name'] . ' (whole list)';
        }
    }
    return $choice;
}

/** The lists and services as <option>s, grouped by Phorest list. */
function yuzu_round_phorest_menu_options(string $selected): string
{
    $menu = yuzu_round_phorest_menu();
    $html = '<optgroup label="A whole Phorest list">';
    foreach ($menu['categories'] as $cat) {
        $value = 'cat:' . $cat['id'];
        $html .= '<option value="' . esc_attr($value) . '"' . ($selected === $value ? ' selected' : '') . '>' . esc_html($cat['name']) . '</option>';
    }
    $html .= '</optgroup>';
    foreach ($menu['categories'] as $cat) {
        $html .= '<optgroup label="' . esc_attr($cat['name']) . '">';
        foreach ($menu['services'] as $svc) {
            if ((string) $svc['category'] !== (string) $cat['name']) {
                continue;
            }
            $value = 'svc:' . $svc['id'];
            $html .= '<option value="' . esc_attr($value) . '"' . ($selected === $value ? ' selected' : '') . '>' . esc_html($svc['name'] . ' · ' . yuzu_round_phorest_money((float) $svc['price'])) . '</option>';
        }
        $html .= '</optgroup>';
    }
    return $html;
}

function yuzu_round_phorest_options_html(string $selected, string $default): string
{
    $html = '<option value=""' . ($selected === '' ? ' selected' : '') . '>Theme choice: ' . esc_html(yuzu_round_phorest_choice_label($default)) . '</option>';
    $html .= '<option value="main"' . ($selected === 'main' ? ' selected' : '') . '>Main booking page</option>';
    return $html . yuzu_round_phorest_menu_options($selected);
}

/**
 * Where an offer's Book button goes: '' (main booking page), 'voucher',
 * 'course', 'cat:ID' or 'svc:ID'. Anything else is dropped.
 */
function yuzu_round_phorest_target(string $raw): string
{
    if ($raw === 'voucher' || $raw === 'course') {
        return $raw;
    }
    return preg_match('/^(svc|cat):[A-Za-z0-9_-]{1,80}$/', $raw) === 1 ? $raw : '';
}

/** A stored target as a link; one Phorest no longer has opens the main booking page. */
function yuzu_round_phorest_target_href(string $target): string
{
    $base = yuzu_round_phorest_base();
    if ($target === 'voucher' || $target === 'course') {
        return $base . '/buy/' . $target;
    }
    if (preg_match('/^(svc|cat):(.+)$/', $target, $m) === 1 && yuzu_round_phorest_has($m[1], $m[2])) {
        return yuzu_round_phorest_href($target);
    }
    return $base;
}

function yuzu_round_phorest_target_options(string $selected): string
{
    $html = '';
    foreach (['' => 'Main booking page', 'voucher' => 'Gift vouchers', 'course' => 'Series and packages'] as $value => $label) {
        $html .= '<option value="' . esc_attr($value) . '"' . ($selected === $value ? ' selected' : '') . '>' . esc_html($label) . '</option>';
    }
    if ($selected !== '' && !in_array($selected, ['voucher', 'course'], true) && yuzu_round_phorest_target_href($selected) === yuzu_round_phorest_base()) {
        $html .= '<option value="' . esc_attr($selected) . '" selected>No longer in Phorest: opens the main booking page</option>';
    }
    return $html . yuzu_round_phorest_menu_options($selected);
}

function yuzu_round_phorest_service(string $choice): ?array
{
    if (!str_starts_with($choice, 'svc:')) {
        return null;
    }
    $id = substr($choice, 4);
    foreach (yuzu_round_phorest_menu()['services'] as $svc) {
        if ((string) $svc['id'] === $id) {
            return $svc;
        }
    }
    return null;
}

/** Phorest's staff role as a price-list level, or '' for roles that do not take bookings. */
function yuzu_round_phorest_level_key(string $role): string
{
    $map = ['creative director' => 'creative', 'senior stylist' => 'senior', 'stylist' => 'stylist', 'junior stylist' => 'junior'];
    return $map[strtolower(trim($role))] ?? '';
}

function yuzu_round_phorest_level_names(): array
{
    return ['creative' => 'Creative Director', 'senior' => 'Senior Stylist', 'stylist' => 'Stylist', 'junior' => 'Junior Stylist'];
}

/**
 * Everyone in the Phorest menu with what the site shows for them. Stylists in
 * a bookable level show by default; wp-admin → Booking links changes that.
 */
function yuzu_round_phorest_stylists(): array
{
    $stored = function_exists('get_option') ? get_option('yuzu_round_phorest_stylists') : [];
    $stored = is_array($stored) ? $stored : [];
    $levels = yuzu_round_phorest_level_names();
    $order = array_flip(array_keys($levels));
    $out = [];
    foreach (yuzu_round_phorest_menu()['staff'] as $person) {
        $id = (string) ($person['id'] ?? '');
        if (preg_match('/^[A-Za-z0-9_-]{1,80}$/', $id) !== 1) {
            continue;
        }
        $mine = is_array($stored[$id] ?? null) ? $stored[$id] : [];
        $default_level = yuzu_round_phorest_level_key((string) ($person['level'] ?? ''));
        $level = isset($mine['level']) && ($mine['level'] === '' || isset($levels[$mine['level']])) ? (string) $mine['level'] : $default_level;
        $name = trim((string) ($mine['name'] ?? ''));
        $out[] = [
            'id' => $id,
            'phorest_name' => (string) ($person['name'] ?? ''),
            'phorest_role' => (string) ($person['level'] ?? ''),
            'name' => $name !== '' ? $name : ucfirst(trim((string) ($person['name'] ?? ''))),
            'level' => $level,
            'show' => $level !== '' && (isset($mine['show']) ? (bool) $mine['show'] : $default_level !== ''),
            'href' => yuzu_round_phorest_base() . '/book/services?staffId=' . $id,
        ];
    }
    usort($out, static fn ($a, $b) => [$order[$a['level']] ?? 9, strtolower($a['name'])] <=> [$order[$b['level']] ?? 9, strtolower($b['name'])]);
    return $out;
}

function yuzu_round_phorest_stylists_sanitize(mixed $input): array
{
    if (!is_array($input)) {
        return [];
    }
    $levels = yuzu_round_phorest_level_names();
    $clean = [];
    foreach (yuzu_round_phorest_menu()['staff'] as $person) {
        $id = (string) ($person['id'] ?? '');
        $row = is_array($input[$id] ?? null) ? $input[$id] : null;
        if ($row === null) {
            continue;
        }
        $level = (string) ($row['level'] ?? '');
        $name = yuzu_round_prices_clean_line((string) ($row['name'] ?? ''), 30);
        $clean[$id] = [
            'show' => !empty($row['show']),
            'level' => isset($levels[$level]) ? $level : '',
            'name' => $name === ucfirst(trim((string) ($person['name'] ?? ''))) ? '' : $name,
        ];
    }
    return $clean;
}

function yuzu_round_phorest_join(array $parts): string
{
    $last = array_pop($parts);
    return $parts === [] ? (string) $last : implode(', ', $parts) . ' or ' . $last;
}

/** "Book with Jasmine or Stefano" under the level buttons, one line per level plus one for All. */
function yuzu_round_phorest_stylists_html(string $pad = '      '): string
{
    $e = 'yuzu_round_prices_esc';
    $by = ['' => []];
    foreach (yuzu_round_phorest_stylists() as $s) {
        if (!$s['show']) {
            continue;
        }
        $link = '<a href="' . $e($s['href']) . '" target="_blank" rel="noopener" data-place="stylist">' . $e($s['name']) . '<span class="vh"> (opens Phorest in a new tab)</span></a>';
        $by[''][] = $link;
        $by[$s['level']][] = $link;
    }
    if ($by[''] === []) {
        return '';
    }
    $html = $pad . "<div class=\"level-stylists\" data-level-stylists>\n";
    foreach ($by as $level => $links) {
        $lead = $level === '' ? 'Or book with a stylist: ' : 'Book with ';
        $html .= $pad . '  <p data-for="' . $level . '"' . ($level === '' ? '' : ' hidden') . '>' . $lead . yuzu_round_phorest_join($links) . "</p>\n";
    }
    return $html . $pad . "</div>\n";
}

function yuzu_round_phorest_replace_stylists(string $html): string
{
    if (!str_contains($html, '<!-- yuzu-stylists -->')) {
        return $html;
    }
    $block = "<!-- yuzu-stylists -->\n" . yuzu_round_phorest_stylists_html() . '      <!-- /yuzu-stylists -->';
    return preg_replace_callback('/<!-- yuzu-stylists -->.*?<!-- \/yuzu-stylists -->/s', static fn () => $block, $html, 1) ?? $html;
}

function yuzu_round_phorest_stylists_admin(): void
{
    $levels = ['' => 'Not shown (not a stylist)'] + yuzu_round_phorest_level_names();
    echo '<section class="yuzu-group"><h2>Stylists</h2>';
    echo '<p class="description">Under the level buttons on the price list and the service pages, guests see “Book with Jasmine or Stefano” for the level they picked. Each name opens Phorest with only that stylist’s services. The list comes from Phorest: someone who leaves Phorest disappears here after the weekly check, and a new stylist appears with their Phorest role.</p>';
    echo '<table class="widefat striped"><thead><tr><th>In Phorest</th><th>Show</th><th>Name on the site</th><th>Level</th><th></th></tr></thead><tbody>';
    foreach (yuzu_round_phorest_stylists() as $s) {
        $base = 'yuzu_stylists[' . $s['id'] . ']';
        echo '<tr><td><strong>' . esc_html($s['phorest_name']) . '</strong><br /><small>' . esc_html($s['phorest_role']) . '</small></td>';
        echo '<td><input type="hidden" name="' . esc_attr($base . '[show]') . '" value="0" /><input type="checkbox" name="' . esc_attr($base . '[show]') . '" value="1"' . ($s['show'] ? ' checked' : '') . ' aria-label="' . esc_attr('Show ' . $s['name']) . '" /></td>';
        echo '<td><input type="text" maxlength="30" name="' . esc_attr($base . '[name]') . '" value="' . esc_attr($s['name']) . '" aria-label="' . esc_attr('Name on the site for ' . $s['phorest_name']) . '" /></td>';
        echo '<td><select name="' . esc_attr($base . '[level]') . '" aria-label="' . esc_attr('Level for ' . $s['phorest_name']) . '">';
        foreach ($levels as $key => $label) {
            echo '<option value="' . esc_attr($key) . '"' . ($s['level'] === $key ? ' selected' : '') . '>' . esc_html($label) . '</option>';
        }
        echo '</select></td><td><a href="' . esc_url($s['href']) . '" target="_blank" rel="noopener">Test</a></td></tr>';
    }
    echo '</tbody></table></section>';
}

function yuzu_round_phorest_admin_page(): void
{
    if (!current_user_can('edit_pages')) {
        wp_die(esc_html__('Sorry, you are not allowed to edit the booking links.', 'yuzu-round'), '', ['response' => 403]);
    }
    $overrides = yuzu_round_phorest_overrides();
    $saved = isset($_GET['yuzu_saved']) && $_GET['yuzu_saved'] === '1';
    echo '<div class="wrap yuzu-phorest">';
    echo '<h1>Booking links</h1>';
    if ($saved) {
        echo '<div class="notice notice-success is-dismissible"><p>Booking links and stylists saved.</p></div>';
    }
    echo '<p class="yuzu-lead">Each price on the price list and the service pages has a small ↗ link that opens Phorest on that service, so guests skip the search. Leave a link on Theme choice unless it opens the wrong thing. A link whose service Phorest no longer has opens the stylist level’s list instead, never a dead page.</p>';
    $count = count(yuzu_round_phorest_menu()['services']);
    echo '<p class="description">The Phorest menu here has ' . (int) $count . ' online services. It is refreshed every week by the theme’s Phorest check, which also reports prices that differ between this site and Phorest.</p>';
    echo '<form method="post">';
    wp_nonce_field('yuzu_round_phorest_save');
    echo '<input type="hidden" name="yuzu_phorest_action" value="save" />';
    yuzu_round_phorest_stylists_admin();
    foreach (yuzu_round_phorest_rows() as $group => $meta) {
        echo '<section class="yuzu-group"><h2>' . esc_html($meta['title']) . '</h2>';
        echo '<table class="widefat striped"><thead><tr><th>Service</th><th>Level</th><th>Site price</th><th>Opens in Phorest</th></tr></thead><tbody>';
        foreach ($meta['rows'] as $row => $cells) {
            $first = true;
            foreach ($cells as $level => $display) {
                $default = yuzu_round_phorest_default_choice($group, $row, $level);
                $selected = (string) ($overrides[$group][$row][$level] ?? '');
                $current = yuzu_round_phorest_choice($group, $row, $level, $overrides);
                $svc = yuzu_round_phorest_service($current);
                $warn = '';
                $site = yuzu_round_prices_number($display);
                if ($svc !== null && $site !== null && abs($site - (float) $svc['price']) > 0.001) {
                    $warn = ' <span class="yuzu-warn">Phorest says ' . esc_html(yuzu_round_phorest_money((float) $svc['price'])) . '</span>';
                }
                $id = 'yuzu-book-' . $group . '-' . md5($row) . '-' . $level;
                echo '<tr><td>' . ($first ? '<strong>' . esc_html($row) . '</strong>' : '') . '</td>';
                echo '<td><label for="' . esc_attr($id) . '">' . esc_html($meta['levels'][$level]) . '</label></td>';
                echo '<td>' . esc_html($display) . $warn . '</td>';
                echo '<td><select id="' . esc_attr($id) . '" name="' . esc_attr('yuzu_phorest[' . $group . '][' . md5($row) . '][' . $level . ']') . '">' . yuzu_round_phorest_options_html($selected, $default) . '</select>';
                echo ' <a href="' . esc_url(yuzu_round_phorest_href($current)) . '" target="_blank" rel="noopener">Test</a></td></tr>';
                $first = false;
            }
        }
        echo '</tbody></table></section>';
    }
    submit_button('Save booking links');
    echo '</form></div>';
}

function yuzu_round_phorest_admin_assets(): void
{
    if (!function_exists('get_current_screen')) {
        return;
    }
    $screen = get_current_screen();
    if (!$screen || !str_ends_with((string) $screen->id, '_page_yuzu-booking-links')) {
        return;
    }
    echo '<style>
      .yuzu-phorest .yuzu-lead { max-width: 760px; }
      .yuzu-phorest .yuzu-group { background: #fff; border: 1px solid #c3c4c7; margin: 16px 0 24px; padding: 4px 16px 16px; max-width: 1180px; }
      .yuzu-phorest td { vertical-align: middle; }
      .yuzu-phorest select { max-width: 520px; width: 100%; }
      .yuzu-phorest .yuzu-warn { display: inline-block; margin-left: 6px; color: #b32d2e; font-weight: 600; }
    </style>';
}

function yuzu_round_phorest_handle_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !isset($_POST['yuzu_phorest_action']) || !current_user_can('edit_pages')) {
        return;
    }
    check_admin_referer('yuzu_round_phorest_save');
    update_option('yuzu_round_phorest_links', yuzu_round_phorest_sanitize(wp_unslash($_POST['yuzu_phorest'] ?? null)), false);
    update_option('yuzu_round_phorest_stylists', yuzu_round_phorest_stylists_sanitize(wp_unslash($_POST['yuzu_stylists'] ?? null)), false);
    yuzu_round_prices_mark_edited();
    yuzu_round_prices_purge_cache();
    wp_safe_redirect(add_query_arg('yuzu_saved', '1', admin_url('admin.php?page=yuzu-booking-links')));
    exit;
}

if (function_exists('add_action')) {
    add_action('admin_menu', static function (): void {
        $hook = add_submenu_page('yuzu-prices', 'Booking links', 'Booking links', 'edit_pages', 'yuzu-booking-links', 'yuzu_round_phorest_admin_page');
        if (is_string($hook)) {
            add_action('load-' . $hook, 'yuzu_round_phorest_handle_post');
        }
    }, 20);
    add_action('admin_head', 'yuzu_round_phorest_admin_assets');
}
