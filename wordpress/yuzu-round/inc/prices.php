<?php
/**
 * Price list editor. The public pages stay locked HTML; this screen stores
 * the figures and the renderer drops them into the existing tables.
 */

function yuzu_round_prices_catalog(): array
{
    return [
        'cut' => [
            'title' => 'Cut & style',
            'kind' => 'table',
            'home_label' => 'Cut & styling',
        ],
        'colour' => [
            'title' => 'Colour',
            'kind' => 'table',
            'home_label' => 'Colour',
        ],
        'highlights' => [
            'title' => 'Highlights / balayage / foiliage',
            'kind' => 'table',
            'home_label' => 'Highlights & balayage',
        ],
        'treatments' => [
            'title' => 'Treatments',
            'kind' => 'list',
            'home_label' => 'Treatments',
        ],
    ];
}

function yuzu_round_prices_default(): array
{
    return [
        'groups' => [
            'cut' => [
                ['name' => 'Ladies wash, cut & style', 'senior' => '£87', 'stylist' => '£58'],
                ['name' => 'Gents wash, cut & style', 'senior' => '£58', 'stylist' => '—'],
                ['name' => 'Kids (12 & under)', 'senior' => '£29', 'stylist' => '£23'],
                ['name' => 'Teens (13–16) boys', 'senior' => '£41', 'stylist' => '£35'],
                ['name' => 'Teens (13–16) girls', 'senior' => '£52', 'stylist' => '£35'],
                ['name' => 'Blow-dry', 'senior' => '£58', 'stylist' => '£41'],
            ],
            'colour' => [
                ['name' => 'Full head', 'senior' => '£101', 'stylist' => '£87'],
                ['name' => 'Roots', 'senior' => '£88', 'stylist' => '£75'],
                ['name' => 'In-between full head', 'senior' => '£124', 'stylist' => '£101'],
                ['name' => 'In-between roots', 'senior' => '£111', 'stylist' => '£88'],
                ['name' => 'Illumina colouring', 'senior' => '£133', 'stylist' => '—'],
            ],
            'highlights' => [
                ['name' => 'Full head', 'senior' => '£150', 'stylist' => '£98'],
                ['name' => 'Half head', 'senior' => '£115', 'stylist' => '£75'],
                ['name' => 'T-section', 'senior' => '£89', 'stylist' => '£64'],
                ['name' => 'Long hair below chest', 'senior' => '+£41', 'stylist' => '+£41'],
                ['name' => 'Toner', 'senior' => '£38', 'stylist' => '£38'],
                ['name' => 'Blending', 'senior' => '£41', 'stylist' => '£41'],
                ['name' => 'Colour remover', 'senior' => '£115', 'stylist' => '£104'],
                ['name' => 'Pre-lighten full head', 'senior' => '£179', 'stylist' => '£138'],
                ['name' => 'Pre-lighten roots', 'senior' => '£127', 'stylist' => '£104'],
            ],
            'treatments' => [
                ['name' => 'Nashi Filler Express', 'price' => '£33'],
                ['name' => 'Nashi Filler 1, 2, 3 intense moisture', 'price' => '£46'],
                ['name' => 'K2.0 short', 'price' => '£41'],
                ['name' => 'K2.0 medium', 'price' => '£52'],
                ['name' => 'K2.0 long', 'price' => '£64'],
                ['name' => 'K2.0 extra long', 'price' => '£75'],
                ['name' => 'Aura smoothing, long (3–4 months)', 'price' => '£357'],
                ['name' => 'Aura smoothing, below shoulder', 'price' => '£294'],
                ['name' => 'Aura smoothing, above shoulder', 'price' => '£228'],
                ['name' => 'Aura smoothing, short', 'price' => '£117–165'],
            ],
        ],
        'home' => [
            'cut' => '£29',
            'colour' => '£75',
            'highlights' => '£64',
            'treatments' => '£33',
        ],
        'blurbs' => [
            'cut' => 'From blow-outs to sleek cuts, crafted with precision and passion. Ladies wash, cut and style from £58 with a stylist, £87 with a senior. Kids from £29.',
            'colour' => 'Roots, full head and Illumina colouring. Colour guests, including existing clients, need a patch test at least 48 hours before.',
            'highlights' => 'Classic highlights, balayage and foiliage — T-section from £64, half head from £75, full head from £98, with toner and blending.',
            'treatments' => 'Nashi fillers from £33 and K2.0 moisture. Aura smoothing / Brazilian blow-dry from £117, formaldehyde and ammonia free.',
        ],
    ];
}

function yuzu_round_prices_clip(string $text, int $max): string
{
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

function yuzu_round_prices_clean_line(string $raw, int $max): string
{
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags($raw)) ?? '');
    return yuzu_round_prices_clip($text, $max);
}

function yuzu_round_price_amount(string $number): string
{
    if (!str_contains($number, '.')) {
        return $number;
    }
    [$whole, $fraction] = explode('.', $number, 2);
    $fraction = rtrim($fraction, '0');
    if ($fraction === '') {
        return $whole === '' ? '0' : $whole;
    }
    if (strlen($fraction) === 1) {
        $fraction .= '0';
    }
    return ($whole === '' ? '0' : $whole) . '.' . $fraction;
}

function yuzu_round_price_display(string $raw): string
{
    $spaced = yuzu_round_prices_clean_line($raw, 40);
    $compact = str_replace(' ', '', $spaced);
    if ($compact === '' || preg_match('/^(?:—|–|-|n\/a|na)$/iu', $compact)) {
        return '—';
    }
    $plus = str_starts_with($compact, '+');
    $body = $plus ? substr($compact, 1) : $compact;
    $body = str_replace(['£', ','], '', $body);
    if (preg_match('/^(\d+(?:\.\d+)?)[–-](\d+(?:\.\d+)?)$/u', $body, $matches)) {
        $text = '£' . yuzu_round_price_amount($matches[1]) . '–' . yuzu_round_price_amount($matches[2]);
        return $plus ? '+' . $text : $text;
    }
    if (preg_match('/^(\d+(?:\.\d+)?)$/', $body, $matches)) {
        $text = '£' . yuzu_round_price_amount($matches[1]);
        return $plus ? '+' . $text : $text;
    }
    return $spaced;
}

function yuzu_round_price_schema_parts(string $display): ?array
{
    $body = str_starts_with($display, '+') ? substr($display, 1) : $display;
    if (preg_match('/^£(\d+(?:\.\d+)?)–(\d+(?:\.\d+)?)$/u', $body, $matches)) {
        return [
            'price' => $matches[1],
            'description' => '£' . $matches[1] . ' to £' . $matches[2],
        ];
    }
    if (preg_match('/^£(\d+(?:\.\d+)?)$/', $body, $matches)) {
        return ['price' => $matches[1]];
    }
    return null;
}

function yuzu_round_prices_sanitize(mixed $input, ?array $previous = null): ?array
{
    if (!is_array($input)) {
        return null;
    }
    $fallback = $previous ?? yuzu_round_prices_default();
    $groups = [];
    foreach (yuzu_round_prices_catalog() as $id => $meta) {
        $rows = [];
        $incoming = $input['groups'][$id] ?? null;
        if (is_array($incoming)) {
            foreach ($incoming as $row) {
                if (!is_array($row) || count($rows) >= 60) {
                    continue;
                }
                $name = yuzu_round_prices_clean_line((string) ($row['name'] ?? ''), 120);
                if ($name === '') {
                    continue;
                }
                if ($meta['kind'] === 'list') {
                    $rows[] = [
                        'name' => $name,
                        'price' => yuzu_round_price_display((string) ($row['price'] ?? '')),
                    ];
                    continue;
                }
                $rows[] = [
                    'name' => $name,
                    'senior' => yuzu_round_price_display((string) ($row['senior'] ?? '')),
                    'stylist' => yuzu_round_price_display((string) ($row['stylist'] ?? '')),
                ];
            }
        }
        $kept = $fallback['groups'][$id] ?? yuzu_round_prices_default()['groups'][$id];
        $groups[$id] = $rows === [] ? $kept : $rows;
    }

    $home = [];
    $blurbs = [];
    $default = yuzu_round_prices_default();
    foreach (yuzu_round_prices_catalog() as $id => $meta) {
        $from = yuzu_round_price_display((string) ($input['home'][$id] ?? ''));
        $home[$id] = $from === '—' ? (string) ($fallback['home'][$id] ?? $default['home'][$id]) : $from;
        $blurb = yuzu_round_prices_clean_line((string) ($input['blurbs'][$id] ?? ''), 360);
        $blurbs[$id] = $blurb === '' ? (string) ($fallback['blurbs'][$id] ?? $default['blurbs'][$id]) : $blurb;
    }

    return [
        'groups' => $groups,
        'home' => $home,
        'blurbs' => $blurbs,
    ];
}

function yuzu_round_prices_esc(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function yuzu_round_prices_groups_html(array $data): string
{
    $html = '';
    foreach (yuzu_round_prices_catalog() as $id => $meta) {
        $rows = $data['groups'][$id] ?? [];
        $html .= "          <div class=\"price-group\">\n";
        $html .= '            <h2>' . yuzu_round_prices_esc($meta['title']) . "</h2>\n";
        if ($meta['kind'] === 'list') {
            $html .= "            <ul class=\"treatment-list\">\n";
            foreach ($rows as $row) {
                $html .= '              <li><span>' . yuzu_round_prices_esc((string) $row['name']) . '</span><span>' . yuzu_round_prices_esc((string) $row['price']) . "</span></li>\n";
            }
            $html .= "            </ul>\n";
        } else {
            $html .= "            <table>\n";
            $html .= "              <thead><tr><th scope=\"col\">Service</th><th scope=\"col\">Senior</th><th scope=\"col\">Stylist</th></tr></thead>\n";
            $html .= "              <tbody>\n";
            foreach ($rows as $row) {
                $html .= '                <tr><th scope="row">' . yuzu_round_prices_esc((string) $row['name']) . '</th>';
                $html .= '<td data-label="Senior">' . yuzu_round_prices_esc((string) $row['senior']) . '</td>';
                $html .= '<td data-label="Stylist">' . yuzu_round_prices_esc((string) $row['stylist']) . "</td></tr>\n";
            }
            $html .= "              </tbody>\n";
            $html .= "            </table>\n";
        }
        $html .= "          </div>\n";
    }
    return rtrim($html);
}

function yuzu_round_prices_offer(string $name, string $display): ?array
{
    $parts = yuzu_round_price_schema_parts($display);
    if ($parts === null) {
        return null;
    }
    $offer = [
        '@type' => 'Offer',
        'name' => $name,
        'price' => $parts['price'],
        'priceCurrency' => 'GBP',
        'url' => 'https://yuzu.hair/prices.html',
        'seller' => ['@id' => 'https://yuzu.hair/#salon'],
    ];
    if (isset($parts['description'])) {
        $offer['description'] = $parts['description'];
    }
    return $offer;
}

function yuzu_round_prices_schema_script(array $data): string
{
    $offers = [];
    foreach (yuzu_round_prices_catalog() as $id => $meta) {
        foreach ($data['groups'][$id] ?? [] as $row) {
            if ($meta['kind'] === 'list') {
                $offer = yuzu_round_prices_offer($meta['title'] . ': ' . $row['name'], (string) $row['price']);
                if ($offer !== null) {
                    $offers[] = $offer;
                }
                continue;
            }
            foreach (['senior' => 'senior stylist', 'stylist' => 'stylist'] as $key => $role) {
                $price = (string) ($row[$key] ?? '');
                $name = (string) $row['name'];
                if (str_starts_with($price, '+')) {
                    $name .= ', extra';
                }
                $offer = yuzu_round_prices_offer($meta['title'] . ': ' . $name . ', ' . $role, $price);
                if ($offer !== null) {
                    $offers[] = $offer;
                }
            }
        }
    }
    $json = json_encode(
        [
            '@context' => 'https://schema.org',
            '@type' => 'OfferCatalog',
            'name' => 'Yuzu Hair price list',
            'url' => 'https://yuzu.hair/prices.html',
            'itemListElement' => $offers,
        ],
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG
    );
    if (!is_string($json) || $json === '') {
        return '';
    }
    return "<script type=\"application/ld+json\">\n" . $json . "\n    </script>";
}

function yuzu_round_prices_replace(string $html, string $pattern, callable $callback): string
{
    $count = 0;
    $next = preg_replace_callback($pattern, static function (array $matches) use ($callback): string {
        return (string) $callback($matches);
    }, $html, 1, $count);
    if (!is_string($next) || $count !== 1) {
        return $html;
    }
    return $next;
}

function yuzu_round_prices_replace_groups(string $html, array $data): string
{
    $block = "<!-- yuzu-price-groups -->\n" . yuzu_round_prices_groups_html($data) . "\n          <!-- /yuzu-price-groups -->";
    return yuzu_round_prices_replace(
        $html,
        '/<!-- yuzu-price-groups -->.*?<!-- \/yuzu-price-groups -->/s',
        static function () use ($block): string {
            return $block;
        }
    );
}

function yuzu_round_prices_replace_schema(string $html, array $data): string
{
    $script = yuzu_round_prices_schema_script($data);
    if ($script === '') {
        return $html;
    }
    return yuzu_round_prices_replace(
        $html,
        '/<script type="application\/ld\+json">\s*\{\s*"@context": "https:\/\/schema\.org",\s*"@type": "OfferCatalog",.*?<\/script>/s',
        static function () use ($script): string {
            return $script;
        }
    );
}

function yuzu_round_prices_replace_home(string $html, array $data): string
{
    foreach (array_keys(yuzu_round_prices_catalog()) as $id) {
        $from = yuzu_round_prices_esc((string) ($data['home'][$id] ?? ''));
        if ($from !== '') {
            $html = yuzu_round_prices_replace(
                $html,
                '/(<p class="price-from" data-yuzu-from="' . preg_quote($id, '/') . '">\s*From\s*<strong>)(.*?)(<\/strong>\s*<\/p>)/s',
                static function (array $matches) use ($from): string {
                    return $matches[1] . $from . $matches[3];
                }
            );
        }
        $blurb = yuzu_round_prices_esc((string) ($data['blurbs'][$id] ?? ''));
        if ($blurb !== '') {
            $html = yuzu_round_prices_replace(
                $html,
                '/(<p data-yuzu-blurb="' . preg_quote($id, '/') . '">)(.*?)(<\/p>)/s',
                static function (array $matches) use ($blurb): string {
                    return $matches[1] . $blurb . $matches[3];
                }
            );
        }
    }
    return $html;
}

function yuzu_round_prices_apply_data(string $html, string $file, array $data): string
{
    $clean = yuzu_round_prices_sanitize($data);
    if ($clean === null) {
        return $html;
    }
    $file = basename($file);
    if ($file === 'prices.html') {
        $html = yuzu_round_prices_replace_groups($html, $clean);
        $html = yuzu_round_prices_replace_schema($html, $clean);
    }
    if ($file === 'index.html') {
        $html = yuzu_round_prices_replace_home($html, $clean);
    }
    return $html;
}

function yuzu_round_prices_apply(string $html, string $file): string
{
    if (!function_exists('get_option')) {
        return $html;
    }
    $stored = get_option('yuzu_round_prices');
    if (!is_array($stored)) {
        return $html;
    }
    return yuzu_round_prices_apply_data($html, $file, $stored);
}

function yuzu_round_prices_current(): array
{
    $defaults = yuzu_round_prices_default();
    if (!function_exists('get_option')) {
        return $defaults;
    }
    $stored = get_option('yuzu_round_prices');
    if (!is_array($stored)) {
        return $defaults;
    }
    return yuzu_round_prices_sanitize($stored) ?? $defaults;
}

function yuzu_round_prices_purge_cache(): bool
{
    $cleared = false;
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    if (function_exists('has_action') && has_action('breeze_clear_all_cache')) {
        do_action('breeze_clear_all_cache');
        $cleared = true;
    }
    if (class_exists('Breeze_PurgeCache') && method_exists('Breeze_PurgeCache', 'breeze_cache_flush')) {
        Breeze_PurgeCache::breeze_cache_flush();
        $cleared = true;
    }
    if (function_exists('litespeed_purge_all')) {
        litespeed_purge_all();
        $cleared = true;
    }
    return $cleared;
}

function yuzu_round_prices_admin_notice(): void
{
    if (!function_exists('get_current_screen') || !current_user_can('edit_pages')) {
        return;
    }
    $screen = get_current_screen();
    if (!$screen || $screen->base !== 'post' || $screen->post_type !== 'page') {
        return;
    }
    $post = get_post();
    if (!$post instanceof WP_Post || $post->post_name !== 'prices') {
        return;
    }
    $url = admin_url('admin.php?page=yuzu-prices');
    echo '<div class="notice notice-info"><p>The public price list is locked to the Round layout. Change the figures on the <a href="' . esc_url($url) . '">Price list</a> screen.</p></div>';
}

function yuzu_round_prices_admin_assets(): void
{
    if (!function_exists('get_current_screen')) {
        return;
    }
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'toplevel_page_yuzu-prices') {
        return;
    }
    echo '<style>
      .yuzu-prices .yuzu-lead { max-width: 760px; }
      .yuzu-prices .yuzu-group,
      .yuzu-prices .yuzu-home { background: #fff; border: 1px solid #c3c4c7; margin: 16px 0 24px; padding: 4px 16px 16px; max-width: 980px; }
      .yuzu-prices table { width: 100%; }
      .yuzu-prices input.regular-text,
      .yuzu-prices textarea { width: 100%; max-width: none; }
      .yuzu-prices .yuzu-price { width: 8.5em; }
      .yuzu-prices td { vertical-align: middle; }
      .yuzu-prices .yuzu-row-actions { white-space: nowrap; }
      .yuzu-prices .yuzu-row-actions .button { margin: 0 4px 0 0; }
      .yuzu-home-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
      .yuzu-prices .submit { margin-bottom: 8px; }
    </style>';
}

function yuzu_round_prices_row_fields(string $id, string $kind, int $index, array $row): void
{
    $base = 'yuzu_prices[groups][' . $id . '][' . $index . ']';
    echo '<tr>';
    echo '<td><label class="screen-reader-text" for="' . esc_attr($id . '-name-' . $index) . '">Service</label>';
    echo '<input class="regular-text" id="' . esc_attr($id . '-name-' . $index) . '" type="text" name="' . esc_attr($base . '[name]') . '" value="' . esc_attr((string) ($row['name'] ?? '')) . '" /></td>';
    if ($kind === 'list') {
        echo '<td><label class="screen-reader-text" for="' . esc_attr($id . '-price-' . $index) . '">Price</label>';
        echo '<input class="yuzu-price" id="' . esc_attr($id . '-price-' . $index) . '" type="text" name="' . esc_attr($base . '[price]') . '" value="' . esc_attr((string) ($row['price'] ?? '')) . '" /></td>';
    } else {
        echo '<td><label class="screen-reader-text" for="' . esc_attr($id . '-senior-' . $index) . '">Senior</label>';
        echo '<input class="yuzu-price" id="' . esc_attr($id . '-senior-' . $index) . '" type="text" name="' . esc_attr($base . '[senior]') . '" value="' . esc_attr((string) ($row['senior'] ?? '')) . '" /></td>';
        echo '<td><label class="screen-reader-text" for="' . esc_attr($id . '-stylist-' . $index) . '">Stylist</label>';
        echo '<input class="yuzu-price" id="' . esc_attr($id . '-stylist-' . $index) . '" type="text" name="' . esc_attr($base . '[stylist]') . '" value="' . esc_attr((string) ($row['stylist'] ?? '')) . '" /></td>';
    }
    echo '<td class="yuzu-row-actions">';
    echo '<button type="button" class="button yuzu-up">Up</button>';
    echo '<button type="button" class="button yuzu-down">Down</button>';
    echo '<button type="button" class="button yuzu-remove">Remove</button>';
    echo '</td></tr>';
}

function yuzu_round_prices_admin_page(): void
{
    if (!current_user_can('edit_pages')) {
        wp_die(esc_html__('Sorry, you are not allowed to edit the price list.', 'yuzu-round'), '', ['response' => 403]);
    }
    $data = yuzu_round_prices_current();
    $saved = isset($_GET['yuzu_saved']) && $_GET['yuzu_saved'] === '1';
    $reset = isset($_GET['yuzu_reset']) && $_GET['yuzu_reset'] === '1';
    $cache = isset($_GET['yuzu_cache']) && $_GET['yuzu_cache'] === '1';
    echo '<div class="wrap yuzu-prices">';
    echo '<h1>Price list</h1>';
    if ($saved) {
        echo '<div class="notice notice-success is-dismissible"><p>Prices saved. The price list and the homepage From prices now use these figures. ';
        echo $cache
            ? 'The Breeze cache was cleared.'
            : 'Purge the Breeze cache if the old prices are still showing.';
        echo '</p></div>';
    }
    if ($reset) {
        echo '<div class="notice notice-success is-dismissible"><p>The original price list is back on the site.</p></div>';
    }
    echo '<p class="yuzu-lead">Edit the services and prices here, then press Save prices. The public tables and the homepage cards update. The page design stays as it is.</p>';
    echo '<form method="post">';
    wp_nonce_field('yuzu_round_prices_save');
    echo '<input type="hidden" name="yuzu_prices_action" value="save" />';

    foreach (yuzu_round_prices_catalog() as $id => $meta) {
        $rows = $data['groups'][$id];
        $kind = $meta['kind'];
        echo '<section class="yuzu-group" data-group="' . esc_attr($id) . '" data-next="' . esc_attr((string) (count($rows) + 1)) . '">';
        echo '<h2>' . esc_html($meta['title']) . '</h2>';
        if ($kind === 'table') {
            echo '<p class="description">Leave a stylist price empty if that chair does not offer it. For an extra, type +41. For a range, type 117-165.</p>';
        } else {
            echo '<p class="description">One price for each treatment. For a range, type 117-165.</p>';
        }
        echo '<table class="widefat striped"><thead><tr><th>Service</th>';
        if ($kind === 'list') {
            echo '<th>Price</th>';
        } else {
            echo '<th>Senior</th><th>Stylist</th>';
        }
        echo '<th>Order</th></tr></thead><tbody>';
        foreach ($rows as $index => $row) {
            yuzu_round_prices_row_fields($id, $kind, (int) $index, $row);
        }
        yuzu_round_prices_row_fields($id, $kind, count($rows), ['name' => '', 'senior' => '', 'stylist' => '', 'price' => '']);
        echo '</tbody></table>';
        echo '<p><button type="button" class="button yuzu-add">Add service</button></p>';
        echo '</section>';
    }

    echo '<section class="yuzu-home"><h2>Homepage cards</h2>';
    echo '<p class="description">These are the four From prices on the homepage, and the sentence under each card. They are not calculated from the table, so update them when a featured price changes.</p>';
    echo '<div class="yuzu-home-grid">';
    foreach (yuzu_round_prices_catalog() as $id => $meta) {
        echo '<div><label for="yuzu-from-' . esc_attr($id) . '"><strong>' . esc_html($meta['home_label']) . '</strong> From</label><br />';
        echo '<input class="yuzu-price" id="yuzu-from-' . esc_attr($id) . '" type="text" name="yuzu_prices[home][' . esc_attr($id) . ']" value="' . esc_attr($data['home'][$id]) . '" />';
        echo '<p><label for="yuzu-blurb-' . esc_attr($id) . '">Card text</label><br />';
        echo '<textarea id="yuzu-blurb-' . esc_attr($id) . '" name="yuzu_prices[blurbs][' . esc_attr($id) . ']" rows="4">' . esc_textarea($data['blurbs'][$id]) . '</textarea></p></div>';
    }
    echo '</div></section>';
    submit_button('Save prices');
    echo '</form>';

    echo '<form method="post" class="yuzu-reset">';
    wp_nonce_field('yuzu_round_prices_reset');
    echo '<input type="hidden" name="yuzu_prices_action" value="reset" />';
    submit_button('Restore original prices', 'secondary');
    echo '<p class="description">Puts back the price list that shipped with the theme.</p>';
    echo '</form>';
    echo '</div>';
    echo <<<'JS'
<script>
(function () {
  var root = document.querySelector(".yuzu-prices");
  if (!root) return;
  root.addEventListener("click", function (event) {
    var button = event.target.closest("button");
    if (!button) return;
    var row = button.closest("tr");
    if (button.classList.contains("yuzu-add")) {
      event.preventDefault();
      var group = button.closest("[data-group]");
      var tbody = group.querySelector("tbody");
      var rows = tbody.querySelectorAll("tr");
      var copy = rows[rows.length - 1].cloneNode(true);
      var next = Number(group.getAttribute("data-next") || rows.length);
      copy.querySelectorAll("input").forEach(function (input) {
        input.value = "";
        input.name = input.name.replace(/\[\d+\]/, "[" + next + "]");
        input.id = input.id.replace(/-\d+$/, "-" + next);
      });
      group.setAttribute("data-next", String(next + 1));
      tbody.appendChild(copy);
      var name = copy.querySelector("input");
      if (name) name.focus();
      return;
    }
    if (!row) return;
    if (button.classList.contains("yuzu-remove")) {
      event.preventDefault();
      var body = row.parentElement;
      if (body.querySelectorAll("tr").length === 1) {
        row.querySelectorAll("input").forEach(function (input) { input.value = ""; });
        return;
      }
      row.remove();
      return;
    }
    if (button.classList.contains("yuzu-up") && row.previousElementSibling) {
      event.preventDefault();
      row.parentElement.insertBefore(row, row.previousElementSibling);
    }
    if (button.classList.contains("yuzu-down") && row.nextElementSibling) {
      event.preventDefault();
      row.parentElement.insertBefore(row.nextElementSibling, row);
    }
  });
  var reset = document.querySelector(".yuzu-reset");
  if (reset) {
    reset.addEventListener("submit", function (event) {
      if (!window.confirm("Restore the original price list? The prices saved here will be replaced on the site.")) {
        event.preventDefault();
      }
    });
  }
})();
</script>
JS;
}

function yuzu_round_prices_handle_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        return;
    }
    if (!isset($_POST['yuzu_prices_action']) || !current_user_can('edit_pages')) {
        return;
    }
    $action = (string) $_POST['yuzu_prices_action'];
    $url = admin_url('admin.php?page=yuzu-prices');
    if ($action === 'save') {
        check_admin_referer('yuzu_round_prices_save');
        $clean = yuzu_round_prices_sanitize(wp_unslash($_POST['yuzu_prices'] ?? null), yuzu_round_prices_current());
        if ($clean === null) {
            wp_safe_redirect($url);
            exit;
        }
        update_option('yuzu_round_prices', $clean, true);
        $cleared = yuzu_round_prices_purge_cache();
        wp_safe_redirect(add_query_arg([
            'yuzu_saved' => '1',
            'yuzu_cache' => $cleared ? '1' : '0',
        ], $url));
        exit;
    }
    if ($action === 'reset') {
        check_admin_referer('yuzu_round_prices_reset');
        delete_option('yuzu_round_prices');
        yuzu_round_prices_purge_cache();
        wp_safe_redirect(add_query_arg('yuzu_reset', '1', $url));
        exit;
    }
}

if (function_exists('add_action')) {
    add_action('admin_menu', static function (): void {
        add_menu_page(
            'Price list',
            'Price list',
            'edit_pages',
            'yuzu-prices',
            'yuzu_round_prices_admin_page',
            'dashicons-money-alt',
            26
        );
    });
    add_action('load-toplevel_page_yuzu-prices', 'yuzu_round_prices_handle_post');
    add_action('admin_notices', 'yuzu_round_prices_admin_notice');
    add_action('admin_head', 'yuzu_round_prices_admin_assets');
}
