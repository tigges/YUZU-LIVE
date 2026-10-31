<?php
/**
 * Price list editor. The public pages stay locked HTML; this screen stores
 * the figures and the renderer drops them into the existing tables.
 */

function yuzu_round_prices_catalog(): array
{
    $chairs = [
        'creative' => ['label' => 'Creative director', 'short' => 'Creative', 'role' => 'creative director'],
        'senior' => ['label' => 'Senior stylist', 'short' => 'Senior', 'role' => 'senior stylist'],
        'stylist' => ['label' => 'Stylist', 'short' => 'Stylist', 'role' => 'stylist'],
        'junior' => ['label' => 'Junior stylist', 'short' => 'Junior', 'role' => 'junior stylist'],
    ];
    $without_junior = $chairs;
    unset($without_junior['junior']);

    return [
        'cut' => [
            'title' => 'Cut & style',
            'kind' => 'table',
            'columns' => $chairs,
            'card' => true,
            'home_label' => 'Cut & styling',
        ],
        'colour' => [
            'title' => 'Colour',
            'kind' => 'table',
            'columns' => $chairs,
            'card' => true,
            'home_label' => 'Colour',
        ],
        'highlights' => [
            'title' => 'Highlights / balayage / foiliage',
            'kind' => 'table',
            'columns' => $without_junior,
            'card' => true,
            'home_label' => 'Highlights & balayage',
        ],
        'treatments' => [
            'title' => 'Treatments',
            'kind' => 'list',
            'card' => true,
            'home_label' => 'Treatments',
        ],
        'packages' => [
            'title' => 'Packages',
            'kind' => 'list',
            'card' => false,
        ],
    ];
}

function yuzu_round_prices_default(): array
{
    return [
        'groups' => [
            'cut' => [
                ['name' => 'Ladies wash, cut & style', 'creative' => '£90', 'senior' => '£75', 'stylist' => '£60', 'junior' => 'Up to £30'],
                ['name' => 'Gents wash, cut & style', 'creative' => '£60', 'senior' => '—', 'stylist' => '—', 'junior' => '—'],
                ['name' => 'Kids (12 & under)', 'creative' => '£35', 'senior' => '—', 'stylist' => '—', 'junior' => '—'],
                ['name' => 'Kids, girls (12 & under)', 'creative' => '—', 'senior' => '£30', 'stylist' => '£25', 'junior' => '£15'],
                ['name' => 'Teens (13–16) boys', 'creative' => '£45', 'senior' => '—', 'stylist' => '—', 'junior' => '—'],
                ['name' => 'Teens (13–16) girls', 'creative' => '£55', 'senior' => '£50', 'stylist' => '£40', 'junior' => '£20'],
                ['name' => 'Blow-dry', 'creative' => '£60', 'senior' => '£52', 'stylist' => '£45', 'junior' => '£35'],
            ],
            'colour' => [
                ['name' => 'Full head', 'creative' => '£105', 'senior' => '£97', 'stylist' => '£90', 'junior' => '£65'],
                ['name' => 'Roots', 'creative' => '£90', 'senior' => '£85', 'stylist' => '£80', 'junior' => '£55'],
                ['name' => 'In-between full head', 'creative' => '£130', 'senior' => '£117', 'stylist' => '£105', 'junior' => '—'],
                ['name' => 'In-between roots', 'creative' => '£115', 'senior' => '£102', 'stylist' => '£90', 'junior' => '—'],
                ['name' => 'Illumia colour', 'creative' => '£135', 'senior' => '£125', 'stylist' => '£115', 'junior' => '—'],
            ],
            'highlights' => [
                ['name' => 'Full head', 'creative' => '£155', 'senior' => '£127', 'stylist' => '£100'],
                ['name' => 'Half head', 'creative' => '£120', 'senior' => '£100', 'stylist' => '£80'],
                ['name' => 'T-section', 'creative' => '£95', 'senior' => '£82', 'stylist' => '£70'],
                ['name' => 'Long hair below chest', 'creative' => '£45', 'senior' => '£45', 'stylist' => '£45'],
                ['name' => 'Toner', 'creative' => '£40', 'senior' => '£40', 'stylist' => '£40'],
                ['name' => 'Double toner', 'creative' => '£60', 'senior' => '£60', 'stylist' => '£60'],
                ['name' => 'Blending', 'creative' => '£45', 'senior' => '£45', 'stylist' => '£45'],
                ['name' => 'Colour remover', 'creative' => '£120', 'senior' => '£115', 'stylist' => '£110'],
                ['name' => 'Pre-lighten full head', 'creative' => '£185', 'senior' => '£165', 'stylist' => '£145'],
                ['name' => 'Pre-lighten roots', 'creative' => '£155', 'senior' => '£122', 'stylist' => '£110'],
            ],
            'treatments' => [
                ['name' => 'Nashi filler express', 'price' => '£35'],
                ['name' => 'Nashi filler 1, 2, 3 intense moisture', 'price' => '£50'],
                ['name' => 'K2.0 short hair', 'price' => '£45'],
                ['name' => 'K2.0 medium length hair', 'price' => '£55'],
                ['name' => 'K2.0 long hair', 'price' => '£65'],
                ['name' => 'K2.0 extra long hair', 'price' => '£80'],
                ['name' => 'Aura smoothing, 3–4 months, long', 'price' => '£360'],
                ['name' => 'Aura smoothing, below shoulder', 'price' => '£300'],
                ['name' => 'Aura smoothing, above shoulder', 'price' => '£230'],
                ['name' => 'Aura smoothing, short', 'price' => '£165'],
                ['name' => 'Aura smoothing, 4–6 months, with shampoo and conditioner', 'price' => '+£175'],
            ],
            'packages' => [
                ['name' => '4 blow-drys', 'price' => '£156'],
                ['name' => '4 roots for the price of 3, paid upfront', 'price' => '£264'],
            ],
        ],
        'home' => [
            'cut' => '£30',
            'colour' => '£55',
            'highlights' => '£70',
            'treatments' => '£35',
        ],
        'blurbs' => [
            'cut' => 'From blow-outs to sleek cuts, crafted with precision and passion across Creative Director, Senior Stylist, Stylist and Junior Stylist chairs.',
            'colour' => 'Roots, tints and full head colour, including Wella Illumina. All colour guests—existing and new—require a patch test 48 hours before.',
            'highlights' => 'Classic highlights, freehand balayage and foiliage with bespoke toner and root blending. Patch test required 48 hours prior.',
            'treatments' => 'Nashi filler therapy, K2.0 intense moisture, and 3-to-6 month Lakmé Aura smoothing—100% formaldehyde and ammonia free.',
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
    $plain = str_replace('£', '', $compact);
    if (preg_match('/^upto(\d+(?:\.\d+)?)$/iu', $plain, $matches)) {
        return 'Up to £' . yuzu_round_price_amount($matches[1]);
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
    if (preg_match('/^Up to £(\d+(?:\.\d+)?)$/u', $display, $matches)) {
        return [
            'price' => $matches[1],
            'description' => 'Up to £' . $matches[1],
        ];
    }
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
                $item = ['name' => $name];
                foreach ($meta['columns'] as $key => $column) {
                    $item[$key] = yuzu_round_price_display((string) ($row[$key] ?? ''));
                }
                $rows[] = $item;
            }
        }
        $kept = $fallback['groups'][$id] ?? yuzu_round_prices_default()['groups'][$id];
        $groups[$id] = $rows === [] ? $kept : $rows;
    }

    $home = [];
    $blurbs = [];
    $default = yuzu_round_prices_default();
    foreach (yuzu_round_prices_catalog() as $id => $meta) {
        if (empty($meta['card'])) {
            continue;
        }
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
            $html .= '              <thead><tr><th scope="col">Service</th>';
            foreach ($meta['columns'] as $column) {
                $html .= '<th scope="col">' . yuzu_round_prices_esc($column['short']) . '</th>';
            }
            $html .= "</tr></thead>\n";
            $html .= "              <tbody>\n";
            foreach ($rows as $row) {
                $html .= '                <tr><th scope="row">' . yuzu_round_prices_esc((string) $row['name']) . '</th>';
                foreach ($meta['columns'] as $key => $column) {
                    $html .= '<td data-label="' . yuzu_round_prices_esc($column['short']) . '">' . yuzu_round_prices_esc((string) ($row[$key] ?? '—')) . '</td>';
                }
                $html .= "</tr>\n";
            }
            $html .= "              </tbody>\n";
            $html .= "            </table>\n";
        }
        $html .= "          </div>\n";
    }
    return rtrim($html);
}

function yuzu_round_prices_number(string $display): ?float
{
    if (preg_match_all('/£(\d+(?:\.\d+)?)/u', $display, $matches) < 1) {
        return null;
    }
    return (float) end($matches[1]);
}

function yuzu_round_prices_money(float $amount): string
{
    return '£' . yuzu_round_price_amount(rtrim(rtrim(number_format($amount, 2, '.', ''), '0'), '.'));
}

function yuzu_round_prices_blend_title(string $label): string
{
    return ucwords($label);
}

/**
 * One Blend row from a list of [label, display] pairs. $levels maps the
 * chair key to its display price for the level switch, or is empty.
 */
function yuzu_round_prices_blend_row(string $name, array $pairs, array $levels, float $scale, string $only = '', string $note = '', bool $open = false, string $who = ''): string
{
    $e = 'yuzu_round_prices_esc';
    $data = $levels === [] ? '' : " data-levels='" . $e((string) json_encode($levels, JSON_UNESCAPED_UNICODE)) . "'";
    $who_attr = $who === '' ? '' : ' data-who="' . $e($who) . '"';
    $open_attr = $open ? ' open' : '';
    $numbers = array_values(array_filter(array_map(static fn ($p) => yuzu_round_prices_number($p[1]), $pairs), static fn ($n) => $n !== null));
    if (count($pairs) === 1) {
        $small = $only === '' ? '' : '<small>' . $e($only) . '</small>';
        return "          <div class=\"price-row price-single\"{$who_attr}{$data}>\n"
            . '            <span class="price-name">' . $e($name) . $small . "</span>\n"
            . '            <span class="price-fig">' . $e($pairs[0][1]) . "</span>\n"
            . "          </div>\n";
    }
    $min = $numbers === [] ? 0 : min($numbers);
    $max = $numbers === [] ? 0 : max($numbers);
    $range = $min === $max ? yuzu_round_prices_money($max) : yuzu_round_prices_money($min) . '–' . yuzu_round_prices_money($max);
    $html = "          <details class=\"price-row\"{$who_attr}{$open_attr}{$data}>\n"
        . "            <summary>\n"
        . '              <span class="price-name">' . $e($name) . "</span>\n"
        . '              <span class="price-fig">' . $e($range) . "</span>\n"
        . "              <span class=\"chev\" aria-hidden=\"true\"></span>\n"
        . "            </summary>\n"
        . "            <ul class=\"chairs\">\n";
    foreach ($pairs as [$label, $display]) {
        $width = $scale > 0 ? round((yuzu_round_prices_number($display) ?? 0) / $scale * 100, 1) : 0;
        $html .= "              <li>\n"
            . '                <span>' . $e($label) . "</span>\n"
            . '                <span class="track" aria-hidden="true"><span class="fill" style="--x:0%;--w:' . $width . "%\"></span></span>\n"
            . '                <span class="price-fig">' . $e($display) . "</span>\n"
            . "              </li>\n";
    }
    $html .= "            </ul>\n";
    if ($note !== '') {
        $html .= '            <p class="price-note">' . $e($note) . "</p>\n";
    }
    return $html . "          </details>\n";
}

function yuzu_round_prices_blend_table_row(array $row, array $columns, float $scale, bool $open = false, string $who = ''): string
{
    $pairs = [];
    $levels = [];
    foreach ($columns as $key => $column) {
        $display = (string) ($row[$key] ?? '—');
        if ($display === '—') {
            continue;
        }
        $pairs[] = [yuzu_round_prices_blend_title($column['label']), $display];
        $levels[$key] = $display;
    }
    if ($pairs === []) {
        return '';
    }
    $same = count(array_unique(array_column($pairs, 1))) === 1;
    if ($same && count($pairs) === count($columns)) {
        return yuzu_round_prices_blend_row((string) $row['name'], [['Any stylist', $pairs[0][1]]], [], $scale, '', '', $open, $who);
    }
    $only = count($pairs) === 1 ? $pairs[0][0] . ' only' : '';
    return yuzu_round_prices_blend_row((string) $row['name'], $pairs, $levels, $scale, $only, '', $open, $who);
}

function yuzu_round_prices_blend_scale(array $rows, array $keys): float
{
    $max = 0.0;
    foreach ($rows as $row) {
        foreach ($keys as $key) {
            $max = max($max, yuzu_round_prices_number((string) ($row[$key] ?? '')) ?? 0);
        }
    }
    return $max;
}

function yuzu_round_prices_blend_more(string $title, string $rows, bool $open = false): string
{
    if ($rows === '') {
        return '';
    }
    $open_attr = $open ? ' open' : '';
    $count = preg_match_all('/class="price-row/', $rows);
    return "          <details class=\"price-more\"{$open_attr}>\n"
        . '            <summary><span class="more-name">' . yuzu_round_prices_esc($title) . '</span><span class="more-count">' . $count . ' ' . ($count === 1 ? 'service' : 'services') . "</span><span class=\"chev\" aria-hidden=\"true\"></span></summary>\n"
        . $rows
        . "          </details>\n";
}

function yuzu_round_prices_blend_packages(array $rows, string $who = ''): string
{
    if ($rows === []) {
        return '';
    }
    $who_attr = $who === '' ? '' : ' data-who="' . yuzu_round_prices_esc($who) . '"';
    $html = "          <div class=\"packages\"{$who_attr}>\n            <h3>Packages</h3>\n            <ul>\n";
    foreach ($rows as $row) {
        $html .= '              <li><span>' . yuzu_round_prices_esc((string) $row['name']) . '</span><strong>' . yuzu_round_prices_esc((string) $row['price']) . "</strong></li>\n";
    }
    return $html . "            </ul>\n          </div>\n";
}

function yuzu_round_prices_blend_cut_who(string $name): string
{
    if (preg_match('/^(kids|teens?|girls|boys|children)/i', $name) === 1) {
        return 'children';
    }
    if (preg_match('/^(gents|men)/i', $name) === 1) {
        return 'gents';
    }
    return 'ladies';
}

function yuzu_round_prices_blend_cut_rows(array $rows): array
{
    $rename = [
        'Kids (12 & under)' => 'Boys, 12 and under',
        'Kids, girls (12 & under)' => 'Girls, 12 and under',
        'Teens (13–16) boys' => 'Teen boys, 13–16',
        'Teens (13–16) girls' => 'Teen girls, 13–16',
    ];
    $mapped = [];
    foreach ($rows as $row) {
        $name = (string) ($row['name'] ?? '');
        if (isset($rename[$name])) {
            $row['name'] = $rename[$name];
        }
        $mapped[] = $row;
    }
    // Keep Girls before Boys in each age pair when both are adjacent.
    for ($i = 0, $n = count($mapped); $i + 1 < $n; $i++) {
        $a = (string) $mapped[$i]['name'];
        $b = (string) $mapped[$i + 1]['name'];
        if (($a === 'Boys, 12 and under' && $b === 'Girls, 12 and under')
            || ($a === 'Teen boys, 13–16' && $b === 'Teen girls, 13–16')) {
            [$mapped[$i], $mapped[$i + 1]] = [$mapped[$i + 1], $mapped[$i]];
            $i++;
        }
    }
    return $mapped;
}

/**
 * The Blend price board panels. Cut & style includes client filter pills
 * (All, Ladies, Gents, Children & teens) with Ladies selected and expanded
 * by default; In-between folds under In-between colour, every highlight after
 * T-section under extras, Aura rows become one row by length, and packages
 * sit with roots (Colour) or otherwise Cut & style.
 */
function yuzu_round_prices_blend_board_html(array $data, string $patch_href = '../patch.html', bool $with_pages = false): string
{
    return rtrim(implode('', yuzu_round_prices_blend_panels($data, $patch_href, $with_pages)));
}

/** Price-board panels and the service landing page that explains each one. */
function yuzu_round_prices_service_pages(): array
{
    return [
        'cut' => ['file' => 'haircut-ealing.html', 'title' => 'Cut & style prices', 'more' => 'Haircuts at YUZU'],
        'colour' => ['file' => 'hair-colour-ealing.html', 'title' => 'Colour prices', 'more' => 'Hair colour at YUZU'],
        'high' => ['file' => 'balayage-ealing.html', 'title' => 'Highlights and balayage prices', 'more' => 'Balayage and highlights at YUZU'],
        'treat' => ['file' => 'hair-smoothing-ealing.html', 'title' => 'Smoothing and treatment prices', 'more' => 'Aura smoothing at YUZU'],
    ];
}

/**
 * The four panels keyed cut, colour, high, treat. Each opens with a heading
 * for screen readers and search engines (the visible tabs are labels, not
 * headings) and, on the price list, ends with a link to its service page.
 */
function yuzu_round_prices_blend_panels(array $data, string $patch_href = '../patch.html', bool $with_pages = false, bool $with_headings = true): array
{
    $catalog = yuzu_round_prices_catalog();
    $groups = $data['groups'];
    $pages = yuzu_round_prices_service_pages();
    $patch = '          <p class="panel-note">Colour needs a patch test at least 48 hours before. <a href="' . yuzu_round_prices_esc($patch_href) . "\">About patch testing</a></p>\n";

    $packages = ['cut' => [], 'colour' => []];
    foreach ($groups['packages'] ?? [] as $row) {
        $packages[preg_match('/root|colour/i', (string) $row['name']) ? 'colour' : 'cut'][] = $row;
    }

    $panel = static function (string $key, string $body, string $attrs = '') use ($pages, $with_pages, $with_headings): string {
        $page = $pages[$key];
        $heading = $with_headings ? '          <h2 class="vh">' . yuzu_round_prices_esc($page['title']) . "</h2>\n" : '';
        $more = $with_pages
            ? '          <p class="panel-more"><a href="' . yuzu_round_prices_esc($page['file']) . '">' . yuzu_round_prices_esc($page['more']) . " →</a></p>\n"
            : '';
        return "        <section class=\"price-panel panel-{$key}\"{$attrs}>\n{$heading}{$body}{$more}        </section>\n";
    };
    $table = static function (string $id, callable $is_extra) use ($catalog, $groups): array {
        $columns = $catalog[$id]['columns'];
        $rows = $groups[$id] ?? [];
        $scale = yuzu_round_prices_blend_scale($rows, array_keys($columns));
        $main = '';
        $extra = '';
        foreach ($rows as $index => $row) {
            $extra_row = $is_extra((string) $row['name'], $index);
            $html = yuzu_round_prices_blend_table_row($row, $columns, $scale, !$extra_row);
            if ($extra_row) {
                $extra .= $html;
            } else {
                $main .= $html;
            }
        }
        return [$main, $extra];
    };

    $cut_columns = $catalog['cut']['columns'];
    $cut_rows = yuzu_round_prices_blend_cut_rows($groups['cut'] ?? []);
    $cut_scale = yuzu_round_prices_blend_scale($cut_rows, array_keys($cut_columns));
    $cut_filter = "          <div class=\"cut-filter\" role=\"group\" aria-label=\"Filter Cut &amp; style by client\">\n"
        . "            <label><input type=\"radio\" name=\"who\" value=\"\" /><span>All</span></label>\n"
        . "            <label><input type=\"radio\" name=\"who\" value=\"ladies\" checked /><span>Ladies</span></label>\n"
        . "            <label><input type=\"radio\" name=\"who\" value=\"gents\" /><span>Gents</span></label>\n"
        . "            <label><input type=\"radio\" name=\"who\" value=\"children\" /><span>Children &amp; teens</span></label>\n"
        . "          </div>\n";
    $cut_body = $cut_filter;
    foreach ($cut_rows as $row) {
        $who = yuzu_round_prices_blend_cut_who((string) $row['name']);
        $cut_body .= yuzu_round_prices_blend_table_row($row, $cut_columns, $cut_scale, $who === 'ladies', $who);
    }
    $cut_body .= yuzu_round_prices_blend_packages($packages['cut'], 'ladies');
    $out = ['cut' => $panel('cut', $cut_body, ' data-who="ladies"')];

    [$main, $extra] = $table('colour', static fn ($name) => preg_match('/^in-between/i', $name) === 1);
    $out['colour'] = $panel('colour', $patch . $main . yuzu_round_prices_blend_more('In-between colour', $extra) . yuzu_round_prices_blend_packages($packages['colour']));

    $after_t = false;
    [$main, $extra] = $table('highlights', static function ($name) use (&$after_t) {
        $extra = $after_t;
        if (preg_match('/^t-section/i', $name) === 1) {
            $after_t = true;
        }
        return $extra;
    });
    $out['high'] = $panel('high', $patch . $main . yuzu_round_prices_blend_more('Added when the hair needs it', $extra));

    $aura = [];
    $note = '';
    $others = [];
    foreach ($groups['treatments'] ?? [] as $row) {
        $name = (string) $row['name'];
        $price = (string) $row['price'];
        if (preg_match('/^aura smoothing,\s*(.+)$/iu', $name, $m) === 1) {
            if (str_starts_with($price, '+')) {
                $note = 'Upgrade to ' . $m[1] . ': ' . $price . '.';
            } else {
                $aura[] = [ucfirst($m[1]), $price];
            }
            continue;
        }
        $others[] = $row;
    }
    $body = '';
    if ($aura !== []) {
        $scale = max(array_map(static fn ($p) => yuzu_round_prices_number($p[1]) ?? 0, $aura));
        $body .= yuzu_round_prices_blend_row('Aura smoothing, by length', $aura, [], $scale, '', $note, true);
    }
    $rows = '';
    foreach ($others as $row) {
        $rows .= yuzu_round_prices_blend_row((string) $row['name'], [['', (string) $row['price']]], [], 0);
    }
    $body .= yuzu_round_prices_blend_more('Fillers and K2.0', $rows, true);
    $out['treat'] = $panel('treat', $body);

    return $out;
}

function yuzu_round_prices_replace_blend_board(string $html, array $data, string $patch_href = '../patch.html', bool $with_pages = false): string
{
    $block = "<!-- yuzu-blend-prices -->\n" . yuzu_round_prices_blend_board_html($data, $patch_href, $with_pages) . "\n        <!-- /yuzu-blend-prices -->";
    return yuzu_round_prices_replace(
        $html,
        '/<!-- yuzu-blend-prices -->.*?<!-- \/yuzu-blend-prices -->/s',
        static function () use ($block): string {
            return $block;
        }
    );
}

/** One panel on a service page, between <!-- yuzu-service-prices:KEY --> markers. */
function yuzu_round_prices_replace_service_panel(string $html, array $data): string
{
    if (preg_match('/<!-- yuzu-service-prices:(cut|colour|high|treat) -->/', $html, $m) !== 1) {
        return $html;
    }
    $key = $m[1];
    $panels = yuzu_round_prices_blend_panels($data, 'patch.html', false, false);
    $block = '<!-- yuzu-service-prices:' . $key . " -->\n" . rtrim($panels[$key]) . "\n        <!-- /yuzu-service-prices -->";
    return yuzu_round_prices_replace(
        $html,
        '/<!-- yuzu-service-prices:' . $key . ' -->.*?<!-- \/yuzu-service-prices -->/s',
        static function () use ($block): string {
            return $block;
        }
    );
}

/**
 * Version pages are served as files from static/versions; only the Blend
 * pages carry prices.
 */
function yuzu_round_prices_apply_version(string $html, string $rel): string
{
    if (!function_exists('get_option')) {
        return $html;
    }
    $stored = get_option('yuzu_round_prices');
    if (!is_array($stored)) {
        return $html;
    }
    $clean = yuzu_round_prices_sanitize($stored);
    if ($clean === null) {
        return $html;
    }
    if ($rel === 'blend-prices.html') {
        return yuzu_round_prices_replace_blend_board($html, $clean);
    }
    if ($rel === 'blend.html') {
        return yuzu_round_prices_replace_home($html, $clean);
    }
    return $html;
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
        'url' => 'https://yuzu.hair/prices/',
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
            foreach ($meta['columns'] as $key => $column) {
                $price = (string) ($row[$key] ?? '');
                $name = (string) $row['name'];
                if (str_starts_with($price, '+')) {
                    $name .= ', extra';
                }
                $offer = yuzu_round_prices_offer($meta['title'] . ': ' . $name . ', ' . $column['role'], $price);
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
            'name' => 'YUZU hair price list',
            'url' => 'https://yuzu.hair/prices/',
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
    foreach (yuzu_round_prices_catalog() as $id => $meta) {
        if (empty($meta['card'])) {
            continue;
        }
        $from = yuzu_round_prices_esc((string) ($data['home'][$id] ?? ''));
        if ($from !== '') {
            $html = yuzu_round_prices_replace(
                $html,
                '/(<p class="(?:price-from|from)" data-yuzu-from="' . preg_quote($id, '/') . '">[^<]*?from\s*<strong>)(.*?)(<\/strong>\s*<\/p>)/si',
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
        $html = yuzu_round_prices_replace_blend_board($html, $clean, 'patch.html', true);
        $html = yuzu_round_prices_replace_groups($html, $clean);
        $html = yuzu_round_prices_replace_schema($html, $clean);
    }
    if ($file === 'index.html') {
        $html = yuzu_round_prices_replace_home($html, $clean);
    }
    if (str_contains($html, '<!-- yuzu-service-prices:')) {
        $html = yuzu_round_prices_replace_service_panel($html, $clean);
    }
    return $html;
}

function yuzu_round_prices_legacy_blurbs(): array
{
    return [
        'cut' => [
            'From blow-outs to sleek cuts, crafted with precision and passion. Ladies wash, cut and style from £60 with a stylist, £75 with a senior, £90 with a creative director. Kids from £15.',
            'From blow-outs to sleek cuts, crafted with precision and passion. Ladies wash, cut and style with a stylist, a senior, or a creative director.',
        ],
        'colour' => [
            'Roots from £55 and full head from £65, including Illumia colour. Colour guests, including existing clients, need a patch test at least 48 hours before.',
            'Roots and full head, including Illumia colour. Colour guests, including existing clients, need a patch test at least 48 hours before.',
        ],
        'highlights' => [
            'Classic highlights, balayage and foiliage — T-section from £70, half head from £80, full head from £100, with toner and blending.',
            'Classic highlights, balayage and foiliage, with toner and blending.',
        ],
        'treatments' => [
            'Nashi fillers from £35 and K2.0 moisture. Aura smoothing from £165, formaldehyde and ammonia free.',
            'Nashi fillers and K2.0 moisture. Aura smoothing, formaldehyde and ammonia free.',
        ],
    ];
}

function yuzu_round_prices_refresh_stored_blurbs(): void
{
    if (!function_exists('get_option') || !function_exists('update_option')) {
        return;
    }
    $stored = get_option('yuzu_round_prices');
    if (!is_array($stored) || !isset($stored['blurbs']) || !is_array($stored['blurbs'])) {
        return;
    }
    $defaults = yuzu_round_prices_default()['blurbs'];
    $changed = false;
    foreach (yuzu_round_prices_legacy_blurbs() as $id => $old_list) {
        if (in_array((string) ($stored['blurbs'][$id] ?? ''), (array) $old_list, true)) {
            $stored['blurbs'][$id] = $defaults[$id];
            $changed = true;
        }
    }
    if ($changed) {
        update_option('yuzu_round_prices', $stored, true);
        yuzu_round_prices_purge_cache();
    }
}

function yuzu_round_prices_apply(string $html, string $file): string
{
    if (!function_exists('get_option')) {
        return $html;
    }
    yuzu_round_prices_refresh_stored_blurbs();
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
      .yuzu-prices .yuzu-home { background: #fff; border: 1px solid #c3c4c7; margin: 16px 0 24px; padding: 4px 16px 16px; max-width: 1180px; }
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

function yuzu_round_prices_row_fields(string $id, array $meta, int $index, array $row): void
{
    $base = 'yuzu_prices[groups][' . $id . '][' . $index . ']';
    echo '<tr>';
    echo '<td><label class="screen-reader-text" for="' . esc_attr($id . '-name-' . $index) . '">Service</label>';
    echo '<input class="regular-text" id="' . esc_attr($id . '-name-' . $index) . '" type="text" name="' . esc_attr($base . '[name]') . '" value="' . esc_attr((string) ($row['name'] ?? '')) . '" /></td>';
    if ($meta['kind'] === 'list') {
        echo '<td><label class="screen-reader-text" for="' . esc_attr($id . '-price-' . $index) . '">Price</label>';
        echo '<input class="yuzu-price" id="' . esc_attr($id . '-price-' . $index) . '" type="text" name="' . esc_attr($base . '[price]') . '" value="' . esc_attr((string) ($row['price'] ?? '')) . '" /></td>';
    } else {
        foreach ($meta['columns'] as $key => $column) {
            echo '<td><label class="screen-reader-text" for="' . esc_attr($id . '-' . $key . '-' . $index) . '">' . esc_html($column['label']) . '</label>';
            echo '<input class="yuzu-price" id="' . esc_attr($id . '-' . $key . '-' . $index) . '" type="text" name="' . esc_attr($base . '[' . $key . ']') . '" value="' . esc_attr((string) ($row[$key] ?? '')) . '" /></td>';
        }
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
    echo '<p class="yuzu-lead">Edit the services and prices here, then press Save prices. The price list, the homepage cards and the Blend version pages update. The page design stays as it is.</p>';
    echo '<form method="post">';
    wp_nonce_field('yuzu_round_prices_save');
    echo '<input type="hidden" name="yuzu_prices_action" value="save" />';

    foreach (yuzu_round_prices_catalog() as $id => $meta) {
        $rows = $data['groups'][$id];
        $kind = $meta['kind'];
        echo '<section class="yuzu-group" data-group="' . esc_attr($id) . '" data-next="' . esc_attr((string) (count($rows) + 1)) . '">';
        echo '<h2>' . esc_html($meta['title']) . '</h2>';
        if ($kind === 'table') {
            echo '<p class="description">Leave a price empty if that chair does not offer it. For an extra, type +175. For a junior cap, type up to 30.</p>';
        } else {
            echo '<p class="description">One price for each line. For an extra, type +175.</p>';
        }
        echo '<table class="widefat striped"><thead><tr><th>Service</th>';
        if ($kind === 'list') {
            echo '<th>Price</th>';
        } else {
            foreach ($meta['columns'] as $column) {
                echo '<th>' . esc_html($column['label']) . '</th>';
            }
        }
        echo '<th>Order</th></tr></thead><tbody>';
        foreach ($rows as $index => $row) {
            yuzu_round_prices_row_fields($id, $meta, (int) $index, $row);
        }
        yuzu_round_prices_row_fields($id, $meta, count($rows), ['name' => '']);
        echo '</tbody></table>';
        echo '<p><button type="button" class="button yuzu-add">Add service</button></p>';
        echo '</section>';
    }

    echo '<section class="yuzu-home"><h2>Homepage cards</h2>';
    echo '<p class="description">These are the four From prices on the homepage, and the sentence under each card. They are not calculated from the table, so update them when a featured price changes.</p>';
    echo '<div class="yuzu-home-grid">';
    foreach (yuzu_round_prices_catalog() as $id => $meta) {
        if (empty($meta['card'])) {
            continue;
        }
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

function yuzu_round_prices_mark_edited(): void
{
    if (function_exists('yuzu_round_mark_edited')) {
        $slugs = ['prices', 'home'];
        foreach (yuzu_round_prices_service_pages() as $page) {
            $slugs[] = basename($page['file'], '.html');
        }
        yuzu_round_mark_edited($slugs);
    }
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
        yuzu_round_prices_mark_edited();
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
        yuzu_round_prices_mark_edited();
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
