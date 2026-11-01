<?php
/**
 * Salon details → Site map: a picture of every page, short link and outside
 * destination, worked out from the templates each time it is opened.
 */

function yuzu_round_map_groups(): array
{
    return [
        'services' => ['title' => 'Service pages', 'slugs' => ['haircut-ealing', 'hair-colour-ealing', 'balayage-ealing', 'hair-smoothing-ealing']],
        'booking' => ['title' => 'Prices, booking and gifts', 'slugs' => ['prices', 'patch', 'gift-cards', 'contact']],
        'footer' => ['title' => 'About the salon', 'slugs' => ['about', 'team', 'questions', 'terms', 'privacy']],
    ];
}

function yuzu_round_map_externals(): array
{
    return [
        'phorest.com' => 'Book online (Phorest)',
        'maps.app.goo.gl' => 'Google Maps',
        'google.com/maps' => 'Google Maps',
        'search.google.com' => 'Google reviews',
        'instagram.com' => 'Instagram',
        'facebook.com' => 'Facebook',
        'tiktok.com' => 'TikTok',
        'wa.me' => 'WhatsApp',
    ];
}

/** Links per page, split into header, body and footer. */
function yuzu_round_map_links(string $html): array
{
    $parts = ['header' => '', 'body' => $html, 'footer' => ''];
    $h1 = strpos($html, '<header');
    $h2 = strpos($html, '</header>');
    $f1 = strrpos($html, '<footer');
    if ($h1 !== false && $h2 !== false) {
        $parts['header'] = substr($html, $h1, $h2 - $h1);
    }
    if ($f1 !== false) {
        $parts['footer'] = substr($html, $f1);
    }
    $start = $h2 !== false ? $h2 : 0;
    $parts['body'] = substr($html, $start, ($f1 !== false ? $f1 : strlen($html)) - $start);
    $out = [];
    foreach ($parts as $where => $chunk) {
        preg_match_all('/\shref="([^"]+)"/', $chunk, $m);
        $out[$where] = array_values(array_unique($m[1]));
    }
    return $out;
}

function yuzu_round_map_data(): array
{
    $pages = yuzu_round_pages();
    $by_file = [];
    foreach ($pages as $slug => $page) {
        $by_file[$page['file']] = $slug;
    }
    $dir = yuzu_round_static_dir();
    $linked = [];
    $anchors = [];
    $external = [];
    $templates = 0;
    foreach ($pages as $slug => $page) {
        $file = $dir . '/' . $page['file'];
        if (!is_readable($file)) {
            continue;
        }
        $templates++;
        foreach (yuzu_round_map_links((string) file_get_contents($file)) as $where => $hrefs) {
            foreach ($hrefs as $href) {
                $href = html_entity_decode($href, ENT_QUOTES);
                if (preg_match('#^https?://#i', $href) === 1) {
                    foreach (yuzu_round_map_externals() as $needle => $label) {
                        if (stripos($href, $needle) !== false) {
                            $external[$label] = $external[$label] ?? $href;
                            break;
                        }
                    }
                    continue;
                }
                [$target_file, $hash] = array_pad(explode('#', $href, 2), 2, '');
                if (($target_file === '' || $target_file === 'index.html') && $hash !== '' && $where === 'header') {
                    $anchors[$hash] = true;
                }
                $target = $by_file[$target_file] ?? null;
                if ($target !== null && $target !== $slug) {
                    $linked[$target][$where][$slug] = true;
                }
            }
        }
    }
    return [
        'pages' => $pages,
        'linked' => $linked,
        'anchors' => array_keys($anchors),
        'external' => $external,
        'templates' => $templates,
    ];
}

function yuzu_round_map_url(string $slug): string
{
    return $slug === 'home' ? home_url('/') : home_url('/' . $slug . '/');
}

function yuzu_round_map_from(string $slug, array $data): string
{
    $sources = $data['linked'][$slug] ?? [];
    $count = max(1, $data['templates']);
    $bits = [];
    foreach (['header' => 'header', 'footer' => 'footer'] as $where => $word) {
        $n = count($sources[$where] ?? []);
        if ($n >= $count - 1) {
            $bits[] = $word . ' on every page';
        } elseif ($n > 0) {
            $bits[] = $word . ' on ' . $n . ' of ' . ($count - 1) . ' pages';
        }
    }
    $body = array_keys($sources['body'] ?? []);
    if ($body !== []) {
        $names = array_map(static fn (string $s): string => $data['pages'][$s]['title'] ?? $s, $body);
        $bits[] = count($names) > 3 ? 'text on ' . count($names) . ' pages' : 'text on ' . implode(', ', $names);
    }
    return $bits === [] ? '' : 'Linked from ' . implode('; ', $bits);
}

function yuzu_round_map_box(string $slug, array $data, string $extra = ''): string
{
    $page = $data['pages'][$slug];
    $url = yuzu_round_map_url($slug);
    $indexed = ($page['sitemap'] ?? true) !== false;
    $from = $slug === 'home' ? '' : yuzu_round_map_from($slug, $data);
    $html = '<div class="ym-box' . ($slug === 'home' ? ' ym-home' : '') . '">';
    $html .= '<strong>' . esc_html($page['title']) . '</strong>';
    $html .= '<a href="' . esc_url($url) . '" target="_blank" rel="noopener">' . esc_html($url) . '</a>';
    $html .= '<span class="ym-tag ' . ($indexed ? 'is-on' : 'is-off') . '">' . ($indexed ? 'On Google' : 'Not on Google') . '</span>';
    if ($extra !== '') {
        $html .= '<span class="ym-note">' . esc_html($extra) . '</span>';
    }
    if ($from !== '') {
        $html .= '<span class="ym-note">' . esc_html($from) . '</span>';
    } elseif ($slug !== 'home' && $indexed) {
        $html .= '<span class="ym-note ym-warn">No page links here</span>';
    }
    return $html . '</div>';
}

function yuzu_round_map_admin_page(): void
{
    if (!current_user_can('edit_pages')) {
        wp_die(esc_html__('Sorry, you are not allowed to see this page.', 'yuzu-round'), '', ['response' => 403]);
    }
    $data = yuzu_round_map_data();
    $pages = $data['pages'];
    $placed = ['home' => true];
    $indexed = count(array_filter($pages, static fn (array $p): bool => ($p['sitemap'] ?? true) !== false));

    echo '<div class="wrap yuzu-map">';
    echo '<h1>Site map <button type="button" class="button" onclick="window.print()">Print</button></h1>';
    echo '<p class="ym-lead"><strong>' . (int) $indexed . ' pages on Google</strong> (listed in <a href="' . esc_url(home_url('/sitemap.xml')) . '" target="_blank" rel="noopener">' . esc_html(home_url('/sitemap.xml')) . '</a>), '
        . (count($pages) - $indexed) . ' kept off Google, ' . count(yuzu_round_review_channels()) . ' review short links and '
        . count($data['external']) . ' outside destinations. Drawn from the theme each time you open this page.</p>';

    echo '<div class="ym-row ym-center">' . yuzu_round_map_box('home', $data) . '</div>';
    if ($data['anchors'] !== []) {
        echo '<div class="ym-row ym-center">';
        foreach ($data['anchors'] as $anchor) {
            echo '<a class="ym-pill" href="' . esc_url(home_url('/#' . $anchor)) . '" target="_blank" rel="noopener">' . esc_html(ucfirst(str_replace('-', ' ', $anchor))) . '</a>';
        }
        echo '</div><p class="ym-caption">Header menu: sections on the home page</p>';
    }

    foreach (yuzu_round_map_groups() as $group) {
        $slugs = array_values(array_filter($group['slugs'], static fn (string $s): bool => isset($pages[$s])));
        if ($slugs === []) {
            continue;
        }
        echo '<h2>' . esc_html($group['title']) . '</h2><div class="ym-row">';
        foreach ($slugs as $slug) {
            $placed[$slug] = true;
            echo yuzu_round_map_box($slug, $data);
        }
        echo '</div>';
    }

    $hidden = array_keys(array_filter($pages, static fn (array $p): bool => ($p['sitemap'] ?? true) === false));
    $others = array_values(array_diff(array_keys($pages), array_keys($placed), $hidden));
    if ($others !== []) {
        echo '<h2>Other pages</h2><div class="ym-row">';
        foreach ($others as $slug) {
            echo yuzu_round_map_box($slug, $data);
        }
        echo '</div>';
    }
    if ($hidden !== []) {
        echo '<h2>Kept off Google</h2><div class="ym-row">';
        foreach ($hidden as $slug) {
            echo yuzu_round_map_box($slug, $data, $slug === 'instagram' ? 'For the Instagram bio link only' : '');
        }
        echo '</div>';
    }

    echo '<h2>Short links</h2><div class="ym-row">';
    foreach (yuzu_round_review_channels() as $channel) {
        $url = home_url($channel['path']);
        echo '<div class="ym-box ym-dash"><strong>' . esc_html($channel['label']) . '</strong><a href="' . esc_url($url) . '" target="_blank" rel="noopener">' . esc_html($url) . '</a><span class="ym-note">Sends to the Google review form</span></div>';
    }
    echo '<div class="ym-box ym-dash"><strong>Old addresses</strong><span class="ym-url">' . esc_html(home_url('/prices.html')) . '</span><span class="ym-note">Every old …html address moves to its new page</span></div>';
    echo '</div>';

    if ($data['external'] !== []) {
        echo '<h2>Leaves the site</h2><div class="ym-row">';
        foreach ($data['external'] as $label => $href) {
            echo '<div class="ym-box ym-dash"><strong>' . esc_html($label) . '</strong><a href="' . esc_url($href) . '" target="_blank" rel="noopener">' . esc_html($href) . '</a></div>';
        }
        echo '</div>';
    }
    echo '</div>';
}

if (function_exists('add_action')) {
    add_action('admin_menu', static function (): void {
        $hook = add_submenu_page('yuzu-salon', 'Site map', 'Site map', 'edit_pages', 'yuzu-map', 'yuzu_round_map_admin_page');
        if (!is_string($hook) || $hook === '') {
            return;
        }
        add_action('admin_enqueue_scripts', static function (string $current) use ($hook): void {
            if ($current !== $hook) {
                return;
            }
            wp_register_style('yuzu-map-admin', false);
            wp_enqueue_style('yuzu-map-admin');
            wp_add_inline_style('yuzu-map-admin', '
              .yuzu-map { max-width: 1180px; }
              .yuzu-map h1 .button { margin-left: 12px; vertical-align: middle; }
              .yuzu-map h2 { margin: 28px 0 10px; padding-top: 14px; border-top: 1px solid #dcdcde; }
              .ym-lead { max-width: 820px; font-size: 14px; }
              .ym-row { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 12px; }
              .ym-center { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; margin: 12px 0 4px; }
              .ym-box { display: flex; flex-direction: column; gap: 4px; background: #fff; border: 1px solid #50575e; border-radius: 6px; padding: 10px 12px; }
              .ym-box a, .ym-url { font-family: Consolas, Monaco, monospace; font-size: 12px; word-break: break-all; }
              .ym-home { background: #2f3a25; border-color: #2f3a25; color: #fff; min-width: 260px; text-align: center; align-items: center; }
              .ym-home a { color: #e9eed9; }
              .ym-dash { border-style: dashed; background: #fbfaf7; }
              .ym-pill { background: #e9e5da; border-radius: 999px; padding: 4px 14px; text-decoration: none; color: #1d2327; }
              .ym-caption { text-align: center; color: #646970; margin: 4px 0 0; }
              .ym-tag { align-self: flex-start; font-size: 11px; border-radius: 999px; padding: 1px 8px; }
              .ym-home .ym-tag { align-self: center; }
              .ym-tag.is-on { background: #e6f2e8; color: #1a5e2a; }
              .ym-tag.is-off { background: #fcf0e3; color: #8a4b0f; }
              .ym-note { color: #646970; font-size: 12px; }
              .ym-home .ym-note { color: #e9eed9; }
              .ym-warn { color: #b32d2e; }
              @media print { #adminmenumain, #wpadminbar, #wpfooter, .yuzu-map h1 .button, .notice { display: none !important; } #wpcontent { margin: 0 !important; } }
            ');
        });
    }, 21);
}
