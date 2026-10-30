<?php
/**
 * Google review short links (/review, /review/qr, /review/nfc, /review/receipt)
 * and the privacy-first scan counter on Settings → General.
 */

function yuzu_round_review_default_url(): string
{
    return 'https://search.google.com/local/writereview?placeid=ChIJi24kyPQNdkgRg7PrAlwp1iM';
}

function yuzu_round_review_channels(): array
{
    return [
        'qr' => [
            'label' => 'QR code at the till',
            'path' => '/review/qr',
        ],
        'nfc' => [
            'label' => 'NFC card at the till',
            'path' => '/review/nfc',
        ],
        'receipt' => [
            'label' => 'QR code on receipt',
            'path' => '/review/receipt',
        ],
        'typed' => [
            'label' => 'Typed in (receipt text, word of mouth)',
            'path' => '/review',
        ],
    ];
}

function yuzu_round_review_sanitize_url(mixed $raw): string
{
    $url = trim((string) $raw);
    if ($url === '') {
        return '';
    }
    if (!str_starts_with(strtolower($url), 'https://')) {
        return '';
    }
    $clean = filter_var($url, FILTER_VALIDATE_URL);
    return is_string($clean) ? $clean : '';
}

function yuzu_round_review_target_url(): string
{
    if (function_exists('get_option')) {
        $custom = yuzu_round_review_sanitize_url(get_option('yuzu_round_review_link', ''));
        if ($custom !== '') {
            return $custom;
        }
    }
    return yuzu_round_review_default_url();
}

function yuzu_round_review_is_bot_or_preview(): bool
{
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if ($method !== 'GET') {
        return true;
    }
    $purpose = strtolower(
        (string) ($_SERVER['HTTP_X_PURPOSE'] ?? '')
        . ' ' . (string) ($_SERVER['HTTP_PURPOSE'] ?? '')
        . ' ' . (string) ($_SERVER['HTTP_SEC_PURPOSE'] ?? '')
        . ' ' . (string) ($_SERVER['HTTP_X_MOZ'] ?? '')
    );
    if (preg_match('/(preview|prefetch|prerender)/', $purpose) === 1) {
        return true;
    }
    $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
    if ($ua === '') {
        return true;
    }
    return preg_match(
        '/(bot|crawler|spider|slurp|facebookexternalhit|facebot|whatsapp|twitterbot|slackbot|telegrambot|discordbot|linkedinbot|pinterest|applebot|googlebot|bingbot|yandex|duckduckbot|baiduspider|ia_archiver|semrush|ahrefs|mj12bot|dotbot|petalbot|bytespider|headless|phantomjs|puppeteer|playwright|lighthouse|pagespeed|gtmetrix|pingdom|uptimerobot|statuscake|curl\/|wget\/|python-requests|go-http-client|java\/|okhttp|axios|node-fetch)/i',
        $ua
    ) === 1;
}

function yuzu_round_review_london_now(): DateTimeImmutable
{
    return new DateTimeImmutable('now', new DateTimeZone('Europe/London'));
}

function yuzu_round_review_default_stats(): array
{
    return [
        'started_at' => yuzu_round_review_london_now()->getTimestamp(),
        'last_scan_at' => 0,
        'last_scan_channel' => '',
        'totals' => [
            'qr' => 0,
            'nfc' => 0,
            'receipt' => 0,
            'typed' => 0,
        ],
        'days' => [],
    ];
}

function yuzu_round_review_get_stats(): array
{
    $defaults = yuzu_round_review_default_stats();
    if (!function_exists('get_option')) {
        return $defaults;
    }
    $stored = get_option('yuzu_round_review_scans');
    if (!is_array($stored)) {
        return $defaults;
    }
    $stats = [
        'started_at' => isset($stored['started_at']) && (int) $stored['started_at'] > 0
            ? (int) $stored['started_at']
            : $defaults['started_at'],
        'last_scan_at' => isset($stored['last_scan_at']) ? max(0, (int) $stored['last_scan_at']) : 0,
        'last_scan_channel' => isset($stored['last_scan_channel']) ? (string) $stored['last_scan_channel'] : '',
        'totals' => $defaults['totals'],
        'days' => [],
    ];
    foreach (array_keys($defaults['totals']) as $key) {
        $stats['totals'][$key] = max(0, (int) ($stored['totals'][$key] ?? 0));
    }
    if (isset($stored['days']) && is_array($stored['days'])) {
        foreach ($stored['days'] as $day => $row) {
            if (!is_string($day) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $day) || !is_array($row)) {
                continue;
            }
            $cleanRow = [];
            foreach (array_keys($defaults['totals']) as $key) {
                $val = max(0, (int) ($row[$key] ?? 0));
                if ($val > 0) {
                    $cleanRow[$key] = $val;
                }
            }
            if ($cleanRow !== []) {
                $stats['days'][$day] = $cleanRow;
            }
        }
    }
    return $stats;
}

function yuzu_round_review_record_scan(string $channel): void
{
    $channels = yuzu_round_review_channels();
    if (!isset($channels[$channel]) || !function_exists('update_option')) {
        return;
    }
    if (yuzu_round_review_is_bot_or_preview()) {
        return;
    }
    $now = yuzu_round_review_london_now();
    $today = $now->format('Y-m-d');
    $cutoff = $now->sub(new DateInterval('P45D'))->format('Y-m-d');

    $stats = yuzu_round_review_get_stats();
    $stats['totals'][$channel] = ($stats['totals'][$channel] ?? 0) + 1;
    if (!isset($stats['days'][$today])) {
        $stats['days'][$today] = [];
    }
    $stats['days'][$today][$channel] = ($stats['days'][$today][$channel] ?? 0) + 1;
    $stats['last_scan_at'] = $now->getTimestamp();
    $stats['last_scan_channel'] = $channel;

    foreach (array_keys($stats['days']) as $day) {
        if ($day < $cutoff) {
            unset($stats['days'][$day]);
        }
    }

    update_option('yuzu_round_review_scans', $stats, false);
}

function yuzu_round_review_serve_request(): void
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (!is_string($path)) {
        return;
    }
    $trimmed = rtrim($path, '/');
    if ($trimmed === '/review/kit') {
        if (function_exists('wp_redirect')) {
            wp_redirect(home_url('/versions/review-kit.html'), 302);
        } else {
            header('Location: /versions/review-kit.html', true, 302);
        }
        exit;
    }
    $map = [
        '/review/qr' => 'qr',
        '/review/nfc' => 'nfc',
        '/review/receipt' => 'receipt',
        '/review' => 'typed',
    ];
    if (!isset($map[$trimmed])) {
        return;
    }
    try {
        yuzu_round_review_record_scan($map[$trimmed]);
    } catch (Throwable $e) {
        // Counting must never block a client leaving a review.
    }

    header('X-Robots-Tag: noindex, nofollow');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    $target = yuzu_round_review_target_url();
    if (function_exists('status_header')) {
        status_header(302);
    }
    header('Location: ' . $target, true, 302);
    exit;
}

function yuzu_round_review_compute_windows(array $stats): array
{
    $now = yuzu_round_review_london_now();
    $todayKey = $now->format('Y-m-d');
    $day7Key = $now->sub(new DateInterval('P6D'))->format('Y-m-d');
    $day30Key = $now->sub(new DateInterval('P29D'))->format('Y-m-d');

    $rows = [];
    $totals = ['today' => 0, 'd7' => 0, 'd30' => 0, 'all' => 0];

    foreach (yuzu_round_review_channels() as $key => $meta) {
        $cToday = 0;
        $c7 = 0;
        $c30 = 0;
        foreach ($stats['days'] as $day => $counts) {
            $n = (int) ($counts[$key] ?? 0);
            if ($n <= 0) {
                continue;
            }
            if ($day === $todayKey) {
                $cToday += $n;
            }
            if ($day >= $day7Key && $day <= $todayKey) {
                $c7 += $n;
            }
            if ($day >= $day30Key && $day <= $todayKey) {
                $c30 += $n;
            }
        }
        $cAll = max($c30, (int) ($stats['totals'][$key] ?? 0));
        $rows[$key] = [
            'label' => $meta['label'],
            'path' => $meta['path'],
            'today' => $cToday,
            'd7' => $c7,
            'd30' => $c30,
            'all' => $cAll,
        ];
        $totals['today'] += $cToday;
        $totals['d7'] += $c7;
        $totals['d30'] += $c30;
        $totals['all'] += $cAll;
    }

    return ['rows' => $rows, 'totals' => $totals];
}

function yuzu_round_review_render_link_field(): void
{
    $val = function_exists('get_option') ? (string) get_option('yuzu_round_review_link', '') : '';
    $default = yuzu_round_review_default_url();
    echo '<input name="yuzu_round_review_link" type="url" id="yuzu_round_review_link" value="'
        . esc_attr($val) . '" placeholder="' . esc_attr($default) . '" class="regular-text code" style="max-width:540px;width:100%" />';
    echo '<p class="description">Where <code>yuzu.hair/review</code> sends people (302, not indexed). Leave blank to use the built-in Google review form. Must start with <code>https://</code></p>';
}

function yuzu_round_review_render_scans_field(): void
{
    $stats = yuzu_round_review_get_stats();
    $computed = yuzu_round_review_compute_windows($stats);
    $tz = new DateTimeZone('Europe/London');

    $started = (new DateTimeImmutable('@' . $stats['started_at']))->setTimezone($tz)->format('d/m/Y');
    $channels = yuzu_round_review_channels();
    if ($stats['last_scan_at'] > 0 && isset($channels[$stats['last_scan_channel']])) {
        $lastTime = (new DateTimeImmutable('@' . $stats['last_scan_at']))->setTimezone($tz)->format('d/m/Y g:i a');
        $lastLabel = $channels[$stats['last_scan_channel']]['label'];
        $lastText = 'Last scan ' . esc_html($lastTime) . ' (' . esc_html($lastLabel) . ')';
    } else {
        $lastText = 'No scans recorded yet';
    }

    echo '<style>
      .yuzu-review-table { border-collapse: collapse; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; max-width: 760px; width: 100%; }
      .yuzu-review-table th, .yuzu-review-table td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #f0f0f1; font-size: 13px; }
      .yuzu-review-table thead th { background: #f6f7f7; border-bottom: 1px solid #c3c4c7; font-weight: 600; color: #1d2327; }
      .yuzu-review-table th.num, .yuzu-review-table td.num { text-align: right; font-variant-numeric: tabular-nums; width: 84px; }
      .yuzu-review-table tfoot td { border-top: 1px solid #c3c4c7; border-bottom: 0; font-weight: 600; background: #fcfcfc; }
      .yuzu-review-table code { background: #f0f0f1; padding: 2px 6px; border-radius: 3px; font-size: 12px; }
    </style>';
    echo '<table class="yuzu-review-table">';
    echo '<thead><tr>'
        . '<th>Where they came from</th>'
        . '<th>Link</th>'
        . '<th class="num">Today</th>'
        . '<th class="num">Last 7 days</th>'
        . '<th class="num">Last 30 days</th>'
        . '<th class="num">All time</th>'
        . '</tr></thead><tbody>';
    foreach ($computed['rows'] as $row) {
        echo '<tr>'
            . '<td>' . esc_html($row['label']) . '</td>'
            . '<td><code>' . esc_html($row['path']) . '</code></td>'
            . '<td class="num">' . (int) $row['today'] . '</td>'
            . '<td class="num">' . (int) $row['d7'] . '</td>'
            . '<td class="num">' . (int) $row['d30'] . '</td>'
            . '<td class="num">' . (int) $row['all'] . '</td>'
            . '</tr>';
    }
    echo '</tbody><tfoot><tr>'
        . '<td>Total</td>'
        . '<td></td>'
        . '<td class="num">' . (int) $computed['totals']['today'] . '</td>'
        . '<td class="num">' . (int) $computed['totals']['d7'] . '</td>'
        . '<td class="num">' . (int) $computed['totals']['d30'] . '</td>'
        . '<td class="num">' . (int) $computed['totals']['all'] . '</td>'
        . '</tr></tfoot></table>';
    echo '<p class="description" style="margin-top:8px">'
        . $lastText
        . ' · Counting since ' . esc_html($started)
        . ' · Link previews and bots are not counted. Counts only. No IP addresses, cookies or other personal data are stored.'
        . ' · <a href="' . esc_url(home_url('/versions/review-kit.html')) . '" target="_blank" rel="noopener">Open printable Google review kit</a>'
        . '</p>';
}

if (function_exists('add_action')) {
    add_action('init', static function (): void {
        if (!function_exists('get_option') || get_option('yuzu_round_review_scans', null) === null) {
            if (function_exists('add_option')) {
                add_option('yuzu_round_review_scans', yuzu_round_review_default_stats(), '', false);
            }
        }
        yuzu_round_review_serve_request();
    }, 0);

    add_action('admin_init', static function (): void {
        if (!function_exists('register_setting') || !function_exists('add_settings_field')) {
            return;
        }
        register_setting('general', 'yuzu_round_review_link', [
            'type' => 'string',
            'sanitize_callback' => 'yuzu_round_review_sanitize_url',
            'default' => '',
        ]);
        add_settings_field(
            'yuzu_round_review_link',
            'Review link',
            'yuzu_round_review_render_link_field',
            'general'
        );
        add_settings_field(
            'yuzu_round_review_scans',
            'Review scans',
            'yuzu_round_review_render_scans_field',
            'general'
        );
    });
}
