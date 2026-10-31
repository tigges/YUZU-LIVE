<?php
/**
 * Cookie-free click counts for the actions that matter to the salon: Book,
 * Call, Directions, Email, WhatsApp and Review. js/track.js sends one beacon
 * per click; nothing about the visitor is stored, only daily totals per
 * action, page and place on the page.
 */

const YUZU_ROUND_CLICK_KINDS = [
    'book' => 'Book online',
    'call' => 'Call',
    'directions' => 'Directions',
    'email' => 'Email',
    'whatsapp' => 'WhatsApp',
    'review' => 'Google reviews',
];

function yuzu_round_clicks_today(): string
{
    return (new DateTimeImmutable('now', new DateTimeZone('Europe/London')))->format('Y-m-d');
}

function yuzu_round_clicks_is_bot(string $agent): bool
{
    if ($agent === '') {
        return true;
    }
    return preg_match('/bot|crawl|spider|slurp|preview|headless|lighthouse|pingdom|uptime|curl|wget|python|java\/|httpclient/i', $agent) === 1;
}

function yuzu_round_clicks_get(): array
{
    $data = function_exists('get_option') ? get_option('yuzu_round_clicks') : [];
    if (!is_array($data)) {
        $data = [];
    }
    $data['days'] = is_array($data['days'] ?? null) ? $data['days'] : [];
    $data['started'] = is_string($data['started'] ?? null) ? $data['started'] : yuzu_round_clicks_today();
    return $data;
}

/**
 * Adds one click to a day bucket. Keeps 120 days and at most 200 distinct
 * action/page/place keys per day so a flood of junk cannot grow the option.
 */
function yuzu_round_clicks_add(array $data, string $day, string $key): array
{
    $bucket = is_array($data['days'][$day] ?? null) ? $data['days'][$day] : [];
    if (!isset($bucket[$key]) && count($bucket) >= 200) {
        return $data;
    }
    $bucket[$key] = (int) ($bucket[$key] ?? 0) + 1;
    $data['days'][$day] = $bucket;
    $cutoff = (new DateTimeImmutable($day, new DateTimeZone('Europe/London')))->modify('-120 days')->format('Y-m-d');
    foreach (array_keys($data['days']) as $d) {
        if ($d < $cutoff) {
            unset($data['days'][$d]);
        }
    }
    ksort($data['days']);
    return $data;
}

function yuzu_round_clicks_handle(): void
{
    if (function_exists('status_header')) {
        status_header(204);
    }
    header('Cache-Control: no-store');
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        exit;
    }
    $kind = (string) ($_POST['e'] ?? '');
    $page = (string) ($_POST['p'] ?? '');
    $place = (string) ($_POST['s'] ?? '');
    $valid = '/^[a-z0-9-]{1,40}$/';
    if (!isset(YUZU_ROUND_CLICK_KINDS[$kind]) || preg_match($valid, $page) !== 1 || preg_match($valid, $place) !== 1) {
        exit;
    }
    if (yuzu_round_clicks_is_bot((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''))) {
        exit;
    }
    $origin = (string) ($_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '');
    $host = function_exists('home_url') ? (string) parse_url(home_url('/'), PHP_URL_HOST) : '';
    if ($origin !== '' && $host !== '' && parse_url($origin, PHP_URL_HOST) !== $host) {
        exit;
    }
    $data = yuzu_round_clicks_add(yuzu_round_clicks_get(), yuzu_round_clicks_today(), $kind . '|' . $page . '|' . $place);
    update_option('yuzu_round_clicks', $data, false);
    exit;
}

/** Totals per action, and Book clicks per page and place, over the last $days days. */
function yuzu_round_clicks_summary(array $data, int $days, ?string $today = null): array
{
    $today = $today ?? yuzu_round_clicks_today();
    $from = (new DateTimeImmutable($today, new DateTimeZone('Europe/London')))->modify('-' . ($days - 1) . ' days')->format('Y-m-d');
    $kinds = array_fill_keys(array_keys(YUZU_ROUND_CLICK_KINDS), 0);
    $places = [];
    foreach ($data['days'] as $day => $bucket) {
        if ($day < $from || $day > $today || !is_array($bucket)) {
            continue;
        }
        foreach ($bucket as $key => $count) {
            [$kind, $page, $place] = array_pad(explode('|', (string) $key, 3), 3, '');
            if (!isset($kinds[$kind])) {
                continue;
            }
            $kinds[$kind] += (int) $count;
            if ($kind === 'book') {
                $where = $page . ' · ' . $place;
                $places[$where] = ($places[$where] ?? 0) + (int) $count;
            }
        }
    }
    arsort($places);
    return ['kinds' => $kinds, 'places' => $places];
}

function yuzu_round_clicks_render(): void
{
    $data = yuzu_round_clicks_get();
    $spans = [7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 90 days'];
    $sums = [];
    foreach ($spans as $days => $label) {
        $sums[$days] = yuzu_round_clicks_summary($data, $days);
    }
    echo '<p class="description">Counted since ' . esc_html($data['started']) . '. Bots are ignored; no cookies and no personal data.</p>';
    echo '<table class="widefat striped" style="max-width:640px"><thead><tr><th>Action</th>';
    foreach ($spans as $label) {
        echo '<th>' . esc_html($label) . '</th>';
    }
    echo '</tr></thead><tbody>';
    foreach (YUZU_ROUND_CLICK_KINDS as $kind => $label) {
        echo '<tr><th scope="row">' . esc_html($label) . '</th>';
        foreach ($spans as $days => $_) {
            echo '<td>' . esc_html((string) $sums[$days]['kinds'][$kind]) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';

    $places = array_slice($sums[30]['places'], 0, 10, true);
    echo '<h3>Where people press Book (last 30 days)</h3>';
    if ($places === []) {
        echo '<p>No Book clicks yet.</p>';
        return;
    }
    echo '<table class="widefat striped" style="max-width:640px"><thead><tr><th>Page · place</th><th>Clicks</th></tr></thead><tbody>';
    foreach ($places as $where => $count) {
        echo '<tr><td>' . esc_html($where) . '</td><td>' . esc_html((string) $count) . '</td></tr>';
    }
    echo '</tbody></table>';
}

function yuzu_round_clicks_dashboard_widget(): void
{
    $sum = yuzu_round_clicks_summary(yuzu_round_clicks_get(), 7);
    echo '<ul>';
    foreach (['book', 'call', 'directions'] as $kind) {
        echo '<li><strong>' . esc_html((string) $sum['kinds'][$kind]) . '</strong> ' . esc_html(YUZU_ROUND_CLICK_KINDS[$kind]) . '</li>';
    }
    echo '</ul><p><a href="' . esc_url(admin_url('admin.php?page=yuzu-salon#yuzu-clicks')) . '">All website clicks</a></p>';
}

if (function_exists('add_action')) {
    add_action('wp_ajax_yuzu_track', 'yuzu_round_clicks_handle');
    add_action('wp_ajax_nopriv_yuzu_track', 'yuzu_round_clicks_handle');
    add_action('wp_dashboard_setup', static function (): void {
        if (current_user_can('edit_pages')) {
            wp_add_dashboard_widget('yuzu_round_clicks', 'Website clicks · last 7 days', 'yuzu_round_clicks_dashboard_widget');
        }
    });
}
