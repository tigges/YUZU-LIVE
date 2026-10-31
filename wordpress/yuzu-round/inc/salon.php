<?php
/**
 * Salon details: opening hours, holiday and special days, the notice banner,
 * the Google rating and optional analytics. One screen in wp-admin feeds the
 * visit card on every page, the open/closed line, the structured data and the
 * banner above the header.
 */

function yuzu_round_salon_day_names(): array
{
    return ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
}

function yuzu_round_salon_default(): array
{
    $closed = ['open' => false, 'from' => '10:00', 'to' => '18:00'];
    $late = ['open' => true, 'from' => '10:00', 'to' => '20:00'];
    return [
        'week' => [
            0 => $closed,
            1 => $closed,
            2 => $late,
            3 => $late,
            4 => $late,
            5 => $late,
            6 => ['open' => true, 'from' => '09:00', 'to' => '18:00'],
        ],
        'special' => [],
        'notice' => [
            'on' => false,
            'text' => '',
            'link_label' => '',
            'link_url' => '',
            'from' => '',
            'to' => '',
            'tone' => 'forest',
        ],
        'auto_notice' => true,
        'auto_days' => 21,
        'rating' => ['value' => '4.5', 'count' => '217'],
        'analytics' => ['provider' => 'none', 'id' => ''],
    ];
}

function yuzu_round_salon_time(mixed $raw, string $fallback): string
{
    $raw = trim((string) $raw);
    if (preg_match('/^(\d{1,2}):(\d{2})$/', $raw, $m) === 1 && (int) $m[1] < 24 && (int) $m[2] < 60) {
        return sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
    }
    return $fallback;
}

function yuzu_round_salon_date(mixed $raw): string
{
    $raw = trim((string) $raw);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw) !== 1) {
        return '';
    }
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $raw, new DateTimeZone('Europe/London'));
    return $d instanceof DateTimeImmutable && $d->format('Y-m-d') === $raw ? $raw : '';
}

function yuzu_round_salon_line(mixed $raw, int $max): string
{
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $raw)) ?? '');
    if (function_exists('mb_substr')) {
        return mb_substr($text, 0, $max);
    }
    return substr($text, 0, $max);
}

function yuzu_round_salon_link(mixed $raw): string
{
    $url = trim((string) $raw);
    if ($url === '') {
        return '';
    }
    if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
        return preg_match('#^/[A-Za-z0-9/_\-.\#?=&%]*$#', $url) === 1 ? $url : '';
    }
    if (preg_match('#^(https://|tel:|mailto:)#i', $url) !== 1) {
        return '';
    }
    return function_exists('esc_url_raw') ? (string) esc_url_raw($url, ['https', 'tel', 'mailto']) : $url;
}

function yuzu_round_salon_sanitize(mixed $input): array
{
    $default = yuzu_round_salon_default();
    $in = is_array($input) ? $input : [];
    $out = $default;

    foreach ($default['week'] as $day => $base) {
        $row = is_array($in['week'][$day] ?? null) ? $in['week'][$day] : [];
        $from = yuzu_round_salon_time($row['from'] ?? '', $base['from']);
        $to = yuzu_round_salon_time($row['to'] ?? '', $base['to']);
        $out['week'][$day] = [
            'open' => !empty($row['open']) && $from < $to,
            'from' => $from,
            'to' => $to,
        ];
    }

    $special = [];
    foreach ((is_array($in['special'] ?? null) ? $in['special'] : []) as $row) {
        if (!is_array($row) || count($special) >= 40) {
            continue;
        }
        $from = yuzu_round_salon_date($row['from'] ?? '');
        if ($from === '') {
            continue;
        }
        $to = yuzu_round_salon_date($row['to'] ?? '');
        if ($to === '' || $to < $from) {
            $to = $from;
        }
        $open = yuzu_round_salon_time($row['open'] ?? '', '');
        $close = yuzu_round_salon_time($row['close'] ?? '', '');
        $closed = !empty($row['closed']) || $open === '' || $close === '' || $open >= $close;
        $special[] = [
            'from' => $from,
            'to' => $to,
            'closed' => $closed,
            'open' => $closed ? '' : $open,
            'close' => $closed ? '' : $close,
            'label' => yuzu_round_salon_line($row['label'] ?? '', 60),
        ];
    }
    usort($special, static fn ($a, $b) => strcmp($a['from'], $b['from']));
    $out['special'] = $special;

    $notice = is_array($in['notice'] ?? null) ? $in['notice'] : [];
    $tone = (string) ($notice['tone'] ?? 'forest');
    $out['notice'] = [
        'on' => !empty($notice['on']),
        'text' => yuzu_round_salon_line($notice['text'] ?? '', 220),
        'link_label' => yuzu_round_salon_line($notice['link_label'] ?? '', 40),
        'link_url' => yuzu_round_salon_link($notice['link_url'] ?? ''),
        'from' => yuzu_round_salon_date($notice['from'] ?? ''),
        'to' => yuzu_round_salon_date($notice['to'] ?? ''),
        'tone' => in_array($tone, ['forest', 'terra', 'cream'], true) ? $tone : 'forest',
    ];
    $out['auto_notice'] = !empty($in['auto_notice']);
    $out['auto_days'] = max(1, min(90, (int) ($in['auto_days'] ?? 21)));

    $rating = is_array($in['rating'] ?? null) ? $in['rating'] : [];
    $value = str_replace(',', '.', trim((string) ($rating['value'] ?? '')));
    $out['rating'] = [
        'value' => preg_match('/^[1-5](\.\d)?$/', $value) === 1 ? $value : $default['rating']['value'],
        'count' => preg_match('/^\d{1,6}$/', trim((string) ($rating['count'] ?? ''))) === 1 ? trim((string) $rating['count']) : $default['rating']['count'],
    ];

    $analytics = is_array($in['analytics'] ?? null) ? $in['analytics'] : [];
    $provider = (string) ($analytics['provider'] ?? 'none');
    $id = trim((string) ($analytics['id'] ?? ''));
    $valid = [
        'plausible' => '/^[a-z0-9.-]{3,80}$/i',
        'cloudflare' => '/^[a-f0-9]{32}$/i',
    ];
    if (!isset($valid[$provider]) || preg_match($valid[$provider], $id) !== 1) {
        $provider = 'none';
        $id = '';
    }
    $out['analytics'] = ['provider' => $provider, 'id' => $id];

    return $out;
}

function yuzu_round_salon_current(): array
{
    if (!function_exists('get_option')) {
        return yuzu_round_salon_default();
    }
    $stored = get_option('yuzu_round_salon');
    return is_array($stored) ? yuzu_round_salon_sanitize($stored + yuzu_round_salon_default()) : yuzu_round_salon_default();
}

function yuzu_round_salon_today(): string
{
    return (new DateTimeImmutable('now', new DateTimeZone('Europe/London')))->format('Y-m-d');
}

function yuzu_round_salon_clock(string $time): string
{
    [$h, $m] = array_map('intval', explode(':', $time));
    $suffix = $h < 12 ? 'am' : 'pm';
    $base = $h % 12 ?: 12;
    return $base . ($m > 0 ? sprintf(':%02d', $m) : '') . $suffix;
}

function yuzu_round_salon_span(string $from, string $to): string
{
    return yuzu_round_salon_clock($from) . ' – ' . yuzu_round_salon_clock($to);
}

function yuzu_round_salon_join_days(array $names): string
{
    if (count($names) <= 1) {
        return (string) ($names[0] ?? '');
    }
    $last = array_pop($names);
    return implode(', ', $names) . ' & ' . $last;
}

/**
 * Week rows for the visit card: runs of days with the same hours, Monday
 * first, then one row for the closed days.
 */
function yuzu_round_salon_week_rows(array $week): array
{
    $names = yuzu_round_salon_day_names();
    $order = [1, 2, 3, 4, 5, 6, 0];
    $rows = [];
    $closed = [];
    $run = [];
    $flush = static function () use (&$run, &$rows, $names): void {
        if ($run === []) {
            return;
        }
        $first = $run[0]['day'];
        $last = $run[count($run) - 1]['day'];
        $label = count($run) === 1 ? $names[$first]
            : (count($run) === 2 ? $names[$first] . ' & ' . $names[$last] : $names[$first] . ' – ' . $names[$last]);
        $rows[] = [$label, yuzu_round_salon_span($run[0]['from'], $run[0]['to'])];
        $run = [];
    };
    foreach ($order as $day) {
        $d = $week[$day];
        if (!$d['open']) {
            $flush();
            $closed[] = $names[$day];
            continue;
        }
        if ($run !== [] && ($run[0]['from'] !== $d['from'] || $run[0]['to'] !== $d['to'])) {
            $flush();
        }
        $run[] = ['day' => $day, 'from' => $d['from'], 'to' => $d['to']];
    }
    $flush();
    if ($closed !== []) {
        $rows[] = [yuzu_round_salon_join_days($closed), 'Closed'];
    }
    return $rows;
}

/**
 * The weekly hours as a phrase for running text, e.g. "Tuesday to Friday 10am
 * to 8pm, Saturday 9am to 6pm; closed Monday and Sunday". Page copy shows it
 * wherever an editor writes {hours}.
 */
function yuzu_round_salon_hours_phrase(array $data): string
{
    $names = yuzu_round_salon_day_names();
    $clock = static fn (string $from, string $to): string => yuzu_round_salon_clock($from) . ' to ' . yuzu_round_salon_clock($to);
    $runs = [];
    $closed = [];
    foreach ([1, 2, 3, 4, 5, 6, 0] as $day) {
        $d = $data['week'][$day];
        if (!$d['open']) {
            $closed[] = $names[$day];
            continue;
        }
        $last = count($runs) - 1;
        if ($last >= 0 && $runs[$last]['end'] === $day - 1 && $runs[$last]['from'] === $d['from'] && $runs[$last]['to'] === $d['to']) {
            $runs[$last]['end'] = $day;
            continue;
        }
        $runs[] = ['start' => $day, 'end' => $day, 'from' => $d['from'], 'to' => $d['to']];
    }
    $parts = [];
    foreach ($runs as $r) {
        $span = $r['end'] - $r['start'];
        $label = $span === 0 ? $names[$r['start']]
            : ($span === 1 ? $names[$r['start']] . ' and ' . $names[$r['end']] : $names[$r['start']] . ' to ' . $names[$r['end']]);
        $parts[] = $label . ' ' . $clock($r['from'], $r['to']);
    }
    if ($parts === []) {
        return 'closed all week';
    }
    $phrase = implode(', ', $parts);
    if ($closed !== []) {
        $last = array_pop($closed);
        $phrase .= '; closed ' . ($closed === [] ? $last : implode(', ', $closed) . ' and ' . $last);
    }
    return $phrase;
}

function yuzu_round_hours_phrase(): string
{
    return yuzu_round_salon_hours_phrase(yuzu_round_salon_current());
}

/** Fills every <span data-yuzu-hours> and the same words inside structured data. */
function yuzu_round_salon_apply_hours_text(string $html, array $data): string
{
    if (!str_contains($html, 'data-yuzu-hours')) {
        return $html;
    }
    $phrase = yuzu_round_salon_esc(yuzu_round_salon_hours_phrase($data));
    $old = [];
    $html = preg_replace_callback('/(<span data-yuzu-hours>)(.*?)(<\/span>)/s', static function (array $m) use ($phrase, &$old): string {
        $old[] = html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $m[1] . $phrase . $m[3];
    }, $html) ?? $html;
    $plain = yuzu_round_salon_hours_phrase($data);
    $old = array_values(array_unique(array_filter($old, static fn ($o) => $o !== '' && $o !== $plain)));
    if ($old === []) {
        return $html;
    }
    return preg_replace_callback('/<script type="application\/ld\+json">.*?<\/script>/s', static function (array $m) use ($old, $plain): string {
        return str_replace($old, $plain, $m[0]);
    }, $html) ?? $html;
}

function yuzu_round_salon_short_date(string $iso): string
{
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $iso, new DateTimeZone('Europe/London'));
    return $d instanceof DateTimeImmutable ? $d->format('D j M') : $iso;
}

function yuzu_round_salon_special_when(array $s): string
{
    if ($s['from'] === $s['to']) {
        return yuzu_round_salon_short_date($s['from']);
    }
    return yuzu_round_salon_short_date($s['from']) . ' – ' . yuzu_round_salon_short_date($s['to']);
}

function yuzu_round_salon_special_hours(array $s): string
{
    return $s['closed'] ? 'Closed' : yuzu_round_salon_span($s['open'], $s['close']);
}

/** Special days that have not finished yet, soonest first. */
function yuzu_round_salon_upcoming(array $data, int $within_days = 60): array
{
    $today = yuzu_round_salon_today();
    $limit = (new DateTimeImmutable($today, new DateTimeZone('Europe/London')))->modify('+' . $within_days . ' days')->format('Y-m-d');
    return array_values(array_filter(
        $data['special'],
        static fn ($s) => $s['to'] >= $today && $s['from'] <= $limit
    ));
}

function yuzu_round_salon_hours_json(array $data): string
{
    $week = [];
    foreach ($data['week'] as $day => $d) {
        $week[(int) $day] = $d['open'] ? [$d['from'], $d['to']] : null;
    }
    ksort($week);
    $special = [];
    foreach (yuzu_round_salon_upcoming($data, 120) as $s) {
        $special[] = [
            'from' => $s['from'],
            'to' => $s['to'],
            'open' => $s['closed'] ? null : $s['open'],
            'close' => $s['closed'] ? null : $s['close'],
            'label' => $s['label'],
        ];
    }
    $json = json_encode(['week' => array_values($week), 'special' => $special], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
    return is_string($json) ? $json : '';
}

function yuzu_round_salon_esc(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function yuzu_round_salon_week_html(array $data): string
{
    $html = "<ul class=\"visit-hours\">\n";
    foreach (yuzu_round_salon_week_rows($data['week']) as [$label, $hours]) {
        $html .= '          <li><span>' . yuzu_round_salon_esc($label) . '</span><span>' . yuzu_round_salon_esc($hours) . "</span></li>\n";
    }
    return $html . '        </ul>';
}

function yuzu_round_salon_special_html(array $data): string
{
    $rows = yuzu_round_salon_upcoming($data, 45);
    if ($rows === []) {
        return '';
    }
    $html = "<ul class=\"visit-special\" aria-label=\"Holiday and special hours\">\n";
    foreach ($rows as $s) {
        $what = $s['label'] !== '' ? '<strong>' . yuzu_round_salon_esc($s['label']) . '</strong> · ' : '';
        $html .= '          <li data-special-to="' . yuzu_round_salon_esc($s['to']) . '"><span>' . $what . yuzu_round_salon_esc(yuzu_round_salon_special_when($s)) . '</span><span>' . yuzu_round_salon_esc(yuzu_round_salon_special_hours($s)) . "</span></li>\n";
    }
    return $html . '        </ul>';
}

/**
 * The banner to show, if any: the hand-written notice while it is switched on,
 * otherwise an automatic one for special days coming up soon. Dates are
 * checked again in the browser, so a cached page still starts and stops on time.
 */
function yuzu_round_salon_notice(array $data): ?array
{
    $today = yuzu_round_salon_today();
    $n = $data['notice'];
    if ($n['on'] && $n['text'] !== '' && ($n['to'] === '' || $n['to'] >= $today)) {
        return [
            'text' => $n['text'],
            'link_label' => $n['link_label'],
            'link_url' => $n['link_url'],
            'from' => $n['from'],
            'to' => $n['to'],
            'tone' => $n['tone'],
        ];
    }
    if (!$data['auto_notice']) {
        return null;
    }
    $rows = yuzu_round_salon_upcoming($data, (int) $data['auto_days']);
    if ($rows === []) {
        return null;
    }
    $parts = [];
    foreach (array_slice($rows, 0, 4) as $s) {
        $name = $s['label'] !== '' ? $s['label'] . ' (' . yuzu_round_salon_special_when($s) . ')' : yuzu_round_salon_special_when($s);
        $parts[] = $name . ': ' . ($s['closed'] ? 'closed' : yuzu_round_salon_special_hours($s));
    }
    $first = new DateTimeImmutable($rows[0]['from'], new DateTimeZone('Europe/London'));
    $last = max(array_column($rows, 'to'));
    return [
        'text' => 'Holiday hours · ' . implode(' · ', $parts),
        'link_label' => 'Book online',
        'link_url' => 'https://www.phorest.com/salon/yuzuhairandbeauty',
        'from' => $first->modify('-' . (int) $data['auto_days'] . ' days')->format('Y-m-d'),
        'to' => $last,
        'tone' => $n['tone'],
    ];
}

function yuzu_round_salon_notice_html(array $data): string
{
    $n = yuzu_round_salon_notice($data);
    if ($n === null) {
        return '';
    }
    $id = substr(md5($n['text'] . '|' . $n['link_url'] . '|' . $n['from']), 0, 12);
    $link = '';
    if ($n['link_url'] !== '' && $n['link_label'] !== '') {
        $external = str_starts_with($n['link_url'], 'https://') && !str_starts_with($n['link_url'], 'https://yuzu.hair');
        $link = ' <a href="' . yuzu_round_salon_esc($n['link_url']) . '"' . ($external ? ' target="_blank" rel="noopener"' : '') . '>'
            . yuzu_round_salon_esc($n['link_label']) . ($external ? '<span class="vh"> (opens in a new tab)</span>' : '') . '</a>';
    }
    return "\n  <div class=\"site-notice tone-" . yuzu_round_salon_esc($n['tone']) . '" role="region" aria-label="Salon notice" data-notice-id="' . $id . '"'
        . ' data-from="' . yuzu_round_salon_esc($n['from']) . '" data-to="' . yuzu_round_salon_esc($n['to']) . "\" hidden>\n"
        . '    <p>' . yuzu_round_salon_esc($n['text']) . $link . "</p>\n"
        . "    <button type=\"button\" class=\"notice-close\" data-notice-close aria-label=\"Dismiss notice\">×</button>\n"
        . "  </div>\n"
        . "  <script>(function(n){try{var d=new Intl.DateTimeFormat('en-CA',{timeZone:'Europe/London'}).format(new Date());if((n.dataset.from&&d<n.dataset.from)||(n.dataset.to&&d>n.dataset.to))return;if(localStorage.getItem('yuzu-notice-dismissed')===n.dataset.noticeId)return}catch(e){}n.hidden=false})(document.currentScript.previousElementSibling)</script>";
}

function yuzu_round_salon_schema(array $data): array
{
    $names = yuzu_round_salon_day_names();
    $groups = [];
    foreach ($data['week'] as $day => $d) {
        if (!$d['open']) {
            continue;
        }
        $groups[$d['from'] . '-' . $d['to']][] = $names[$day];
    }
    $spec = [];
    foreach ($groups as $span => $days) {
        [$opens, $closes] = explode('-', $span);
        $spec[] = [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => count($days) === 1 ? $days[0] : $days,
            'opens' => $opens,
            'closes' => $closes,
        ];
    }
    $special = [];
    foreach (yuzu_round_salon_upcoming($data, 120) as $s) {
        $special[] = [
            '@type' => 'OpeningHoursSpecification',
            'validFrom' => $s['from'],
            'validThrough' => $s['to'],
            'opens' => $s['closed'] ? '00:00' : $s['open'],
            'closes' => $s['closed'] ? '00:00' : $s['close'],
        ];
    }
    return [$spec, $special];
}

function yuzu_round_salon_apply_schema(string $html, array $data): string
{
    $pattern = '/<script type="application\/ld\+json">\s*(\{(?:(?!<\/script>).)*?"@type": "HairSalon",\s*"@id": "https:\/\/yuzu\.hair\/#salon"(?:(?!<\/script>).)*\})\s*<\/script>/s';
    if (!preg_match($pattern, $html, $m)) {
        return $html;
    }
    $json = json_decode($m[1], true);
    if (!is_array($json)) {
        return $html;
    }
    [$spec, $special] = yuzu_round_salon_schema($data);
    $json['openingHoursSpecification'] = $spec;
    if ($special !== []) {
        $json['specialOpeningHoursSpecification'] = $special;
    } else {
        unset($json['specialOpeningHoursSpecification']);
    }
    $encoded = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
    if (!is_string($encoded)) {
        return $html;
    }
    $encoded = preg_replace('/^(.)/m', '      $1', $encoded) ?? $encoded;
    return str_replace($m[1], ltrim($encoded), $html);
}

function yuzu_round_salon_head_html(array $data): string
{
    $out = '';
    if (function_exists('admin_url')) {
        $out .= '    <meta name="yuzu-track" content="' . yuzu_round_salon_esc(admin_url('admin-ajax.php?action=yuzu_track')) . "\" />\n";
    }
    $a = $data['analytics'];
    if ($a['provider'] === 'plausible') {
        $out .= '    <script defer data-domain="' . yuzu_round_salon_esc($a['id']) . "\" src=\"https://plausible.io/js/script.js\"></script>\n";
        $out .= "    <script>window.plausible=window.plausible||function(){(window.plausible.q=window.plausible.q||[]).push(arguments)}</script>\n";
    } elseif ($a['provider'] === 'cloudflare') {
        $out .= "    <script defer src=\"https://static.cloudflareinsights.com/beacon.min.js\" data-cf-beacon='{\"token\": \"" . yuzu_round_salon_esc($a['id']) . "\"}'></script>\n";
    }
    return $out;
}

function yuzu_round_salon_apply_data(string $html, string $file, array $data): string
{
    $json = yuzu_round_salon_hours_json($data);
    if ($json !== '') {
        $html = preg_replace(
            '/<script type="application\/json" id="yuzu-hours">.*?<\/script>/s',
            '<script type="application/json" id="yuzu-hours">' . str_replace(['\\', '$'], ['\\\\', '\\$'], $json) . '</script>',
            $html,
            1
        ) ?? $html;
    }

    $html = yuzu_round_salon_apply_hours_text($html, $data);

    $week = yuzu_round_salon_week_html($data);
    $html = preg_replace_callback('/<ul class="visit-hours">.*?<\/ul>/s', static fn () => $week, $html, 1) ?? $html;

    $special = yuzu_round_salon_special_html($data);
    $html = preg_replace_callback(
        '/<!-- yuzu-special-hours -->.*?<!-- \/yuzu-special-hours -->/s',
        static fn () => '<!-- yuzu-special-hours -->' . ($special === '' ? '' : "\n        " . $special . "\n        ") . '<!-- /yuzu-special-hours -->',
        $html,
        1
    ) ?? $html;

    $value = yuzu_round_salon_esc($data['rating']['value']);
    $count = yuzu_round_salon_esc($data['rating']['count']);
    $html = preg_replace('/(<strong data-yuzu-rating>)[^<]*(<\/strong>)/', '${1}' . $value . '${2}', $html) ?? $html;
    $html = preg_replace('/(<span data-yuzu-rating-count>)[^<]*(<\/span>)/', '${1}' . $count . '${2}', $html) ?? $html;
    $html = preg_replace('/(class="rating-stars" aria-hidden="true" style="--rating: )[0-9.]+(")/', '${1}' . $value . '${2}', $html) ?? $html;

    if (basename($file) === 'index.html') {
        $html = yuzu_round_salon_apply_schema($html, $data);
    }

    $head = yuzu_round_salon_head_html($data);
    if ($head !== '') {
        $html = preg_replace('/<\/head>/', str_replace(['\\', '$'], ['\\\\', '\\$'], $head) . '</head>', $html, 1) ?? $html;
    }

    $notice = yuzu_round_salon_notice_html($data);
    if ($notice !== '') {
        $html = preg_replace_callback('/<body[^>]*>/', static fn ($m) => $m[0] . $notice, $html, 1) ?? $html;
    }
    return $html;
}

function yuzu_round_salon_apply(string $html, string $file): string
{
    return yuzu_round_salon_apply_data($html, $file, yuzu_round_salon_current());
}

/* ---------- wp-admin ---------- */

function yuzu_round_salon_admin_page(): void
{
    if (!current_user_can('edit_pages')) {
        wp_die(esc_html__('Sorry, you are not allowed to edit salon details.', 'yuzu-round'), '', ['response' => 403]);
    }
    $data = yuzu_round_salon_current();
    $names = yuzu_round_salon_day_names();
    $saved = isset($_GET['yuzu_saved']) && $_GET['yuzu_saved'] === '1';
    $cache = isset($_GET['yuzu_cache']) && $_GET['yuzu_cache'] === '1';

    echo '<div class="wrap yuzu-salon">';
    echo '<h1>Salon details</h1>';
    if ($saved) {
        echo '<div class="notice notice-success is-dismissible"><p>Saved. Every page now uses these hours and notices. ';
        echo $cache ? 'The Breeze cache was cleared.' : 'Purge the Breeze cache if the old hours are still showing.';
        echo '</p></div>';
    }
    echo '<p class="yuzu-lead">Opening hours, holidays and the banner at the top of the site. The visit card, the “Open now” line, Google’s data and the hours mentioned in the About and Questions text all update from here.</p>';
    echo '<form method="post">';
    wp_nonce_field('yuzu_round_salon_save');
    echo '<input type="hidden" name="yuzu_salon_action" value="save" />';

    echo '<section class="yuzu-box"><h2>Opening hours</h2><p class="description">In sentences these read: <strong>' . esc_html(yuzu_round_salon_hours_phrase($data)) . '</strong></p><table class="widefat striped yuzu-week"><thead><tr><th>Day</th><th>Open</th><th>From</th><th>Until</th></tr></thead><tbody>';
    foreach ([1, 2, 3, 4, 5, 6, 0] as $day) {
        $d = $data['week'][$day];
        $base = 'yuzu_salon[week][' . $day . ']';
        echo '<tr><th scope="row">' . esc_html($names[$day]) . '</th>';
        echo '<td><label><input type="checkbox" name="' . esc_attr($base . '[open]') . '" value="1"' . checked($d['open'], true, false) . ' /> Open</label></td>';
        echo '<td><input type="time" name="' . esc_attr($base . '[from]') . '" value="' . esc_attr($d['from']) . '" /></td>';
        echo '<td><input type="time" name="' . esc_attr($base . '[to]') . '" value="' . esc_attr($d['to']) . '" /></td></tr>';
    }
    echo '</tbody></table></section>';

    echo '<section class="yuzu-box"><h2>Holidays and special days</h2>';
    echo '<p class="description">Bank holidays, Christmas, training days or late openings. Leave “Until” empty for a single day. Untick Closed and add times for shorter or longer hours. Past dates drop off the site by themselves.</p>';
    echo '<table class="widefat striped yuzu-special"><thead><tr><th>From</th><th>Until</th><th>Closed</th><th>Opens</th><th>Closes</th><th>Name (optional)</th><th></th></tr></thead><tbody>';
    $rows = $data['special'];
    $rows[] = ['from' => '', 'to' => '', 'closed' => true, 'open' => '', 'close' => '', 'label' => ''];
    foreach ($rows as $i => $s) {
        $base = 'yuzu_salon[special][' . $i . ']';
        echo '<tr>';
        echo '<td><input type="date" name="' . esc_attr($base . '[from]') . '" value="' . esc_attr($s['from']) . '" /></td>';
        echo '<td><input type="date" name="' . esc_attr($base . '[to]') . '" value="' . esc_attr($s['to'] === $s['from'] ? '' : $s['to']) . '" /></td>';
        echo '<td><input type="checkbox" name="' . esc_attr($base . '[closed]') . '" value="1"' . checked($s['closed'], true, false) . ' aria-label="Closed" /></td>';
        echo '<td><input type="time" name="' . esc_attr($base . '[open]') . '" value="' . esc_attr($s['open']) . '" /></td>';
        echo '<td><input type="time" name="' . esc_attr($base . '[close]') . '" value="' . esc_attr($s['close']) . '" /></td>';
        echo '<td><input type="text" class="regular-text" maxlength="60" placeholder="Christmas Day" name="' . esc_attr($base . '[label]') . '" value="' . esc_attr($s['label']) . '" /></td>';
        echo '<td><button type="button" class="button yuzu-remove">Remove</button></td>';
        echo '</tr>';
    }
    echo '</tbody></table><p><button type="button" class="button yuzu-add" data-next="' . esc_attr((string) count($rows)) . '">Add a day</button></p></section>';

    $n = $data['notice'];
    echo '<section class="yuzu-box"><h2>Notice banner</h2>';
    echo '<p class="description">A thin bar above the header on every page. Visitors can close it; it comes back if you change the words.</p>';
    echo '<p><label><input type="checkbox" name="yuzu_salon[notice][on]" value="1"' . checked($n['on'], true, false) . ' /> Show my notice</label></p>';
    echo '<p><label for="yuzu-notice-text"><strong>Message</strong></label><br /><input id="yuzu-notice-text" class="large-text" type="text" maxlength="220" name="yuzu_salon[notice][text]" value="' . esc_attr($n['text']) . '" placeholder="Christmas gift vouchers are now in the salon." /></p>';
    echo '<div class="yuzu-grid">';
    echo '<p><label for="yuzu-notice-label"><strong>Link text</strong> (optional)</label><br /><input id="yuzu-notice-label" type="text" maxlength="40" name="yuzu_salon[notice][link_label]" value="' . esc_attr($n['link_label']) . '" placeholder="Book now" /></p>';
    echo '<p><label for="yuzu-notice-url"><strong>Link</strong> (optional)</label><br /><input id="yuzu-notice-url" type="text" class="regular-text" name="yuzu_salon[notice][link_url]" value="' . esc_attr($n['link_url']) . '" placeholder="/prices/ or https://…" /></p>';
    echo '<p><label for="yuzu-notice-from"><strong>Show from</strong></label><br /><input id="yuzu-notice-from" type="date" name="yuzu_salon[notice][from]" value="' . esc_attr($n['from']) . '" /></p>';
    echo '<p><label for="yuzu-notice-to"><strong>Show until</strong></label><br /><input id="yuzu-notice-to" type="date" name="yuzu_salon[notice][to]" value="' . esc_attr($n['to']) . '" /></p>';
    echo '<p><label for="yuzu-notice-tone"><strong>Colour</strong></label><br /><select id="yuzu-notice-tone" name="yuzu_salon[notice][tone]">';
    foreach (['forest' => 'Dark green', 'terra' => 'Terracotta', 'cream' => 'Cream'] as $key => $label) {
        echo '<option value="' . esc_attr($key) . '"' . selected($n['tone'], $key, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select></p></div>';
    echo '<p><label><input type="checkbox" name="yuzu_salon[auto_notice]" value="1"' . checked($data['auto_notice'], true, false) . ' /> When my notice is off, announce holidays and special days automatically</label> ';
    echo '<label>starting <input type="number" min="1" max="90" class="small-text" name="yuzu_salon[auto_days]" value="' . esc_attr((string) $data['auto_days']) . '" /> days before.</label></p>';
    $preview = yuzu_round_salon_notice($data);
    if ($preview !== null) {
        echo '<p class="description"><strong>Banner now:</strong> ' . esc_html($preview['text']) . ($preview['from'] !== '' ? ' · from ' . esc_html($preview['from']) : '') . ($preview['to'] !== '' ? ' until ' . esc_html($preview['to']) : '') . '</p>';
    }
    echo '</section>';

    echo '<section class="yuzu-box"><h2>Google rating</h2><p class="description">Shown next to the stars on the homepage and price list. Copy the figures from the Google Business Profile.</p><div class="yuzu-grid">';
    echo '<p><label for="yuzu-rating"><strong>Rating</strong></label><br /><input id="yuzu-rating" type="text" class="small-text" name="yuzu_salon[rating][value]" value="' . esc_attr($data['rating']['value']) . '" /> out of 5</p>';
    echo '<p><label for="yuzu-rating-count"><strong>Number of reviews</strong></label><br /><input id="yuzu-rating-count" type="number" min="0" class="small-text" name="yuzu_salon[rating][count]" value="' . esc_attr($data['rating']['count']) . '" /></p>';
    echo '</div></section>';

    $a = $data['analytics'];
    echo '<section class="yuzu-box"><h2>Visitor analytics (optional)</h2>';
    echo '<p class="description">Clicks on Book, Call, Directions and Email are already counted below, without cookies. To see visits and traffic sources too, add a cookie-free analytics account.</p><div class="yuzu-grid">';
    echo '<p><label for="yuzu-analytics"><strong>Service</strong></label><br /><select id="yuzu-analytics" name="yuzu_salon[analytics][provider]">';
    foreach (['none' => 'None', 'plausible' => 'Plausible (enter the domain, e.g. yuzu.hair)', 'cloudflare' => 'Cloudflare Web Analytics (enter the site token)'] as $key => $label) {
        echo '<option value="' . esc_attr($key) . '"' . selected($a['provider'], $key, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select></p>';
    echo '<p><label for="yuzu-analytics-id"><strong>Domain or token</strong></label><br /><input id="yuzu-analytics-id" type="text" class="regular-text" name="yuzu_salon[analytics][id]" value="' . esc_attr($a['id']) . '" /></p>';
    echo '</div></section>';

    submit_button('Save salon details');
    echo '</form>';

    if (function_exists('yuzu_round_clicks_render')) {
        echo '<section class="yuzu-box" id="yuzu-clicks"><h2>Website clicks</h2>';
        yuzu_round_clicks_render();
        echo '</section>';
    }
    echo '</div>';
    echo <<<'JS'
<script>
(function () {
  var root = document.querySelector(".yuzu-salon");
  if (!root) return;
  root.addEventListener("click", function (event) {
    var button = event.target.closest("button");
    if (!button) return;
    if (button.classList.contains("yuzu-add")) {
      event.preventDefault();
      var tbody = root.querySelector(".yuzu-special tbody");
      var rows = tbody.querySelectorAll("tr");
      var copy = rows[rows.length - 1].cloneNode(true);
      var next = Number(button.getAttribute("data-next") || rows.length);
      copy.querySelectorAll("input").forEach(function (input) {
        if (input.type === "checkbox") input.checked = true; else input.value = "";
        input.name = input.name.replace(/\[special\]\[\d+\]/, "[special][" + next + "]");
      });
      button.setAttribute("data-next", String(next + 1));
      tbody.appendChild(copy);
      copy.querySelector("input").focus();
      return;
    }
    if (button.classList.contains("yuzu-remove")) {
      event.preventDefault();
      var row = button.closest("tr");
      if (row.parentElement.querySelectorAll("tr").length === 1) {
        row.querySelectorAll("input").forEach(function (input) { if (input.type !== "checkbox") input.value = ""; });
        return;
      }
      row.remove();
    }
  });
})();
</script>
JS;
}

function yuzu_round_salon_handle_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || ($_POST['yuzu_salon_action'] ?? '') !== 'save') {
        return;
    }
    if (!current_user_can('edit_pages')) {
        return;
    }
    check_admin_referer('yuzu_round_salon_save');
    $clean = yuzu_round_salon_sanitize(wp_unslash($_POST['yuzu_salon'] ?? []));
    update_option('yuzu_round_salon', $clean, true);
    if (function_exists('yuzu_round_mark_edited')) {
        yuzu_round_mark_edited(array_keys(yuzu_round_pages()));
    }
    $cleared = function_exists('yuzu_round_prices_purge_cache') && yuzu_round_prices_purge_cache();
    wp_safe_redirect(add_query_arg(['yuzu_saved' => '1', 'yuzu_cache' => $cleared ? '1' : '0'], admin_url('admin.php?page=yuzu-salon')));
    exit;
}

function yuzu_round_salon_admin_assets(): void
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->id !== 'toplevel_page_yuzu-salon') {
        return;
    }
    echo '<style>
      .yuzu-salon .yuzu-lead { max-width: 760px; }
      .yuzu-salon .yuzu-box { background: #fff; border: 1px solid #c3c4c7; margin: 16px 0 24px; padding: 4px 16px 16px; max-width: 1080px; }
      .yuzu-salon .yuzu-week { max-width: 560px; }
      .yuzu-salon .yuzu-special td { vertical-align: middle; }
      .yuzu-salon .yuzu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0 16px; }
      .yuzu-salon input.large-text { max-width: 760px; }
    </style>';
}

if (function_exists('add_action')) {
    add_action('admin_menu', static function (): void {
        add_menu_page(
            'Salon details',
            'Salon details',
            'edit_pages',
            'yuzu-salon',
            'yuzu_round_salon_admin_page',
            'dashicons-store',
            25
        );
    });
    add_action('load-toplevel_page_yuzu-salon', 'yuzu_round_salon_handle_post');
    add_action('admin_head', 'yuzu_round_salon_admin_assets');
}
