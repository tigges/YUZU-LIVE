<?php
/**
 * One list of offers. Each offer is a card on the homepage (front) and the tray
 * that opens under it (details). The template marks the cards with
 * <!-- yuzu-offers -->; what sits between the markers is the theme default.
 * The same list feeds the offers Google reads in the homepage structured data,
 * the Instagram page list (<!-- yuzu-ig-offers -->) and every
 * <span data-yuzu-offers>, which {offers} makes in answers and page copy.
 */

/** Card looks: the ground colour and the line drawing come as a pair. */
function yuzu_round_offer_styles(): array
{
    return [
        'tue' => ['label' => 'Mustard · fruit', 'art' => 'fruit', 'swatch' => '#7d5a12'],
        'wed' => ['label' => 'Olive · flowers', 'art' => 'bloom', 'swatch' => '#656145'],
        'thu' => ['label' => 'Terracotta · fans', 'art' => 'fan', 'swatch' => '#895136'],
        'forest' => ['label' => 'Forest green · fruit', 'art' => 'fruit', 'swatch' => '#2f3b25'],
        'cream-tile' => ['label' => 'Cream · flowers', 'art' => 'bloom', 'swatch' => '#dfd3bf'],
        'bronze' => ['label' => 'Bronze · fans', 'art' => 'fan', 'swatch' => '#7f5136'],
    ];
}

function yuzu_round_offer_days(): array
{
    return ['' => 'Any day', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday'];
}

function yuzu_round_offer_price_sections(): array
{
    return ['cut' => 'Cut & styling', 'colour' => 'Colour', 'highlights' => 'Highlights', 'treatments' => 'Treatments'];
}

/** Anchors the homepage already uses, so an offer link never lands on another section. */
function yuzu_round_offer_reserved_ids(): array
{
    return ['offers', 'reviews', 'services', 'visit', 'looks', 'prices', 'top', 'main', 'yuzu-hours', 'cut', 'colour', 'highlights', 'treatments'];
}

/** The theme's starting offers; `legacy` is the Page copy key the details used to live under. */
function yuzu_round_offer_seed(): array
{
    return [
        [
            'id' => 'tuesdays', 'legacy' => 'tue', 'home' => true, 'style' => 'tue', 'day' => '2',
            'title' => 'Colour Tuesdays', 'sub' => 'with a Creative Director', 'value' => '50% Off',
            'fine' => "Your most expensive colour\nservice with a full priced\nWash, Cut & Blowdry",
            'kicker' => 'Every Tuesday · Creative Director',
            'headline' => 'Don’t go back to black with this deal',
            'text' => 'Every Tuesday, enjoy 50% off your most expensive colour service with a full priced wash, cut & blow-dry with a Creative Director.',
            'terms' => '50% off your most expensive colour service · with a full priced wash, cut & blow-dry · Creative Director · patch test 48 hours before',
            'action' => 'book', 'button' => 'Book Tuesday colour', 'prices' => 'colour',
            'summary' => '50% off your most expensive colour service with a full priced wash, cut & blow-dry, with a Creative Director', 'ig' => true,
        ],
        [
            'id' => 'wednesdays', 'legacy' => 'wed', 'home' => true, 'style' => 'wed', 'day' => '3',
            'title' => "Smooth\nWednesdays", 'sub' => '', 'value' => '25% Off',
            'fine' => "Our three to six month\nsmoothing treatment",
            'kicker' => 'Every Wednesday · Smoothing',
            'headline' => 'Blow everyone away with this deal',
            'text' => 'Every Wednesday, enjoy 25% off our three to six month smoothing treatment — formaldehyde and ammonia free.',
            'terms' => '25% off our three to six month smoothing treatment · Wednesdays',
            'action' => 'book', 'button' => 'Book Wednesday smoothing', 'prices' => 'treatments',
            'summary' => '25% off our three to six month smoothing treatment', 'ig' => true,
        ],
        [
            'id' => 'thursdays', 'legacy' => 'thu', 'home' => true, 'style' => 'thu', 'day' => '4',
            'title' => 'Colour Thursdays', 'sub' => 'with a Senior Stylist and Stylist', 'value' => '50% Off',
            'fine' => "Your most expensive colour\nservice with a full priced\nWash, Cut & Blowdry",
            'kicker' => 'Every Thursday · Senior Stylist and Stylist',
            'headline' => 'Thursdays are the new Tuesdays',
            'text' => 'Every Thursday, enjoy 50% off your most expensive colour service with a full priced wash, cut & blow-dry with a Senior Stylist and Stylist.',
            'terms' => '50% off your most expensive colour service · with a full priced wash, cut & blow-dry · Senior Stylist and Stylist · patch test 48 hours before',
            'action' => 'book', 'button' => 'Book Thursday colour', 'prices' => 'colour',
            'summary' => '50% off your most expensive colour service with a full priced wash, cut & blow-dry, with a Senior Stylist and Stylist', 'ig' => true,
        ],
        [
            'id' => 'refer', 'legacy' => 'refer', 'home' => true, 'style' => 'forest', 'day' => '',
            'title' => 'Spread the Word', 'sub' => 'Refer a friend', 'value' => '£10',
            'fine' => "Credit for every new client\nyou recommend to YUZU hair",
            'kicker' => 'Ongoing reward · Unlimited referrals',
            'headline' => 'Spread the word, earn £10 credit',
            'text' => 'Share YUZU hair with friends, family, or colleagues. When your recommended friend books and completes their first appointment with us, you receive £10 credit towards your next visit—with no limit on how many friends you refer.',
            'terms' => '£10 credit per new client referred · unlimited referrals · redeemable on any service',
            'action' => 'book', 'button' => 'Book an appointment', 'prices' => 'cut',
            'summary' => '£10 credit for each new client you refer, with no limit', 'ig' => false,
        ],
        [
            'id' => 'rebook', 'legacy' => 'rebook', 'home' => true, 'style' => 'cream-tile', 'day' => '',
            'title' => 'Rebook', 'sub' => 'the same day', 'value' => '10% Off',
            'fine' => "Book your next appointment\nin the salon before you leave",
            'kicker' => 'In-salon loyalty reward',
            'headline' => 'Hair today, gone tomorrow (but not really)',
            'text' => 'At YUZU hair, we value loyalty. Book your next appointment at reception on the same day as your visit and receive 10% off that next appointment as a token of our gratitude.',
            'terms' => '10% off your next appointment when booked in-salon on the day of your visit',
            'action' => 'book', 'button' => 'Book your visit', 'prices' => 'cut',
            'summary' => '10% off your next appointment when you rebook in the salon on the day of your visit', 'ig' => false,
        ],
        [
            'id' => 'blowdry', 'legacy' => 'blowdry', 'home' => true, 'style' => 'bronze', 'day' => '',
            'title' => 'Blow-dry', 'sub' => 'package of four', 'value' => '£156',
            'fine' => "Four blow-drys · valid\nfor six months",
            'kicker' => 'Package · Save on regular styling',
            'headline' => '4 blow-drys for £156',
            'text' => 'Keep your hair styled for less with our 4-visit blow-dry bundle. Ask at reception on your next visit or call us to purchase.',
            'terms' => 'Valid 6 months · haircuts not included · not applicable for extensions · no-show deducts 1 blow-dry · non-refundable',
            'action' => 'prices', 'button' => 'View in price list', 'prices' => 'cut',
            'summary' => 'a package of four blow-drys for £156, valid for six months', 'ig' => false,
        ],
    ];
}

/** First run: the theme offers, with details already edited under Page copy carried over. */
function yuzu_round_offer_from_seed(): array
{
    $copy = function_exists('get_option') ? get_option('yuzu_round_copy') : [];
    $home = is_array($copy) && is_array($copy['home'] ?? null) ? $copy['home'] : [];
    $offers = yuzu_round_offer_seed();
    foreach ($offers as &$offer) {
        foreach (['kicker', 'headline', 'text', 'terms'] as $part) {
            $value = $home['offer-' . $offer['legacy'] . '-' . $part] ?? '';
            if (is_string($value) && trim($value) !== '') {
                $offer[$part] = $value;
            }
        }
        unset($offer['legacy']);
    }
    unset($offer);
    return yuzu_round_offer_sanitize($offers);
}

function yuzu_round_offer_current(): array
{
    $stored = function_exists('get_option') ? get_option('yuzu_round_offers') : false;
    if (is_array($stored)) {
        return yuzu_round_offer_sanitize($stored);
    }
    return yuzu_round_offer_from_seed();
}

function yuzu_round_offer_text(mixed $raw, int $max, int $lines = 1): string
{
    $text = is_string($raw) ? $raw : '';
    $text = function_exists('wp_strip_all_tags') ? wp_strip_all_tags($text) : strip_tags($text);
    $rows = preg_split("/\r\n|\r|\n/", $text) ?: [];
    $rows = array_values(array_filter(array_map(static fn ($r): string => trim((string) preg_replace('/\s+/u', ' ', $r)), $rows), 'strlen'));
    $text = $lines > 1 ? implode("\n", array_slice($rows, 0, $lines)) : implode(' ', $rows);
    return function_exists('mb_substr') ? mb_substr($text, 0, $max) : substr($text, 0, $max);
}

function yuzu_round_offer_slug(string $raw): string
{
    $slug = strtolower(trim($raw));
    $slug = (string) preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim(substr($slug, 0, 40), '-');
}

function yuzu_round_offer_date(mixed $raw): string
{
    $raw = is_string($raw) ? trim($raw) : '';
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $raw, new DateTimeZone('Europe/London'));
    return $d instanceof DateTimeImmutable && $d->format('Y-m-d') === $raw ? $raw : '';
}

function yuzu_round_offer_today(): string
{
    return (new DateTimeImmutable('now', new DateTimeZone('Europe/London')))->format('Y-m-d');
}

/** Shown today: ticked for the homepage and inside its dates. No dates means it runs until unticked. */
function yuzu_round_offer_live(array $o, ?string $today = null): bool
{
    $today = $today ?? yuzu_round_offer_today();
    return $o['home'] && ($o['from'] === '' || $o['from'] <= $today) && ($o['until'] === '' || $o['until'] >= $today);
}

function yuzu_round_offer_sanitize(array $input): array
{
    $styles = yuzu_round_offer_styles();
    $days = yuzu_round_offer_days();
    $sections = yuzu_round_offer_price_sections();
    $taken = yuzu_round_offer_reserved_ids();
    $seed = array_column(yuzu_round_offer_seed(), null, 'id');
    $out = [];
    foreach ($input as $row) {
        if (!is_array($row) || !empty($row['remove'])) {
            continue;
        }
        $theme = $seed[(string) ($row['id'] ?? '')] ?? [];
        foreach (['summary' => '', 'ig' => false, 'from' => '', 'until' => ''] as $key => $blank) {
            if (!array_key_exists($key, $row)) {
                $row[$key] = $theme[$key] ?? $blank;
            }
        }
        $offer = [
            'id' => '',
            'home' => !empty($row['home']),
            'style' => isset($styles[$row['style'] ?? '']) ? (string) $row['style'] : 'forest',
            'day' => isset($days[(string) ($row['day'] ?? '')]) ? (string) ($row['day'] ?? '') : '',
            'title' => yuzu_round_offer_text($row['title'] ?? '', 60, 2),
            'sub' => yuzu_round_offer_text($row['sub'] ?? '', 60),
            'value' => yuzu_round_offer_text($row['value'] ?? '', 14),
            'fine' => yuzu_round_offer_text($row['fine'] ?? '', 110, 3),
            'kicker' => yuzu_round_offer_text($row['kicker'] ?? '', 80),
            'headline' => yuzu_round_offer_text($row['headline'] ?? '', 100),
            'text' => yuzu_round_offer_text($row['text'] ?? '', 600),
            'terms' => yuzu_round_offer_text($row['terms'] ?? '', 300),
            'action' => ($row['action'] ?? '') === 'prices' ? 'prices' : 'book',
            'button' => yuzu_round_offer_text($row['button'] ?? '', 40),
            'prices' => isset($sections[$row['prices'] ?? '']) ? (string) $row['prices'] : 'cut',
            'summary' => yuzu_round_offer_text(is_string($row['summary']) ? str_replace('{offers}', '', $row['summary']) : '', 160),
            'ig' => !empty($row['ig']),
            'from' => yuzu_round_offer_date($row['from']),
            'until' => yuzu_round_offer_date($row['until']),
        ];
        if ($offer['from'] !== '' && $offer['until'] !== '' && $offer['until'] < $offer['from']) {
            [$offer['from'], $offer['until']] = [$offer['until'], $offer['from']];
        }
        if ($offer['title'] === '' && $offer['headline'] === '') {
            continue;
        }
        $id = yuzu_round_offer_slug((string) ($row['id'] ?? ''));
        if ($id === '') {
            $id = yuzu_round_offer_slug(str_replace("\n", ' ', $offer['title'] !== '' ? $offer['title'] : $offer['headline']));
        }
        $id = $id !== '' ? $id : 'offer';
        $base = $id;
        for ($n = 2; in_array($id, $taken, true); $n++) {
            $id = $base . '-' . $n;
        }
        $taken[] = $id;
        $offer['id'] = $id;
        $out[] = $offer;
    }
    return $out;
}

/* ---------- rendering ---------- */

function yuzu_round_offer_plain(string $text): string
{
    return function_exists('yuzu_round_lib_fill') ? yuzu_round_lib_fill($text, false) : $text;
}

function yuzu_round_offer_name(array $o): string
{
    return str_replace("\n", ' ', $o['title'] !== '' ? $o['title'] : $o['headline']);
}

function yuzu_round_offer_line(array $o): string
{
    return rtrim(yuzu_round_offer_plain($o['summary']), ' .');
}

/** {offers}: the homepage offers as one phrase, "Colour Tuesdays (50% off …), … and Blow-dry (…)". */
function yuzu_round_offer_phrase(?array $offers = null): string
{
    $parts = [];
    foreach ($offers ?? yuzu_round_offer_current() as $o) {
        if (yuzu_round_offer_live($o)) {
            $line = yuzu_round_offer_line($o);
            $parts[] = yuzu_round_offer_name($o) . ($line !== '' ? ' (' . $line . ')' : '');
        }
    }
    if ($parts === []) {
        return 'seasonal offers';
    }
    $last = array_pop($parts);
    return $parts === [] ? $last : implode(', ', $parts) . ' and ' . $last;
}

function yuzu_round_offer_ig_html(array $o, string $pad): string
{
    $e = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $line = yuzu_round_offer_line($o);
    return $pad . '<li>' . $e(yuzu_round_offer_name($o)) . ($line !== '' ? ': ' . $e($line) . '.' : '') . "</li>\n";
}

function yuzu_round_offer_card_html(array $o, string $pad): string
{
    $e = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $styles = yuzu_round_offer_styles();
    $art = $styles[$o['style']]['art'] ?? 'fruit';
    $draw = $art === 'fruit'
        ? ['draw fruit-a', 'draw fruit-b']
        : ['draw ' . $art . ' ' . $art . '-a', 'draw ' . $art . ' ' . $art . '-b'];

    $lead = [];
    foreach (explode("\n", $o['title']) as $title) {
        if ($title !== '') {
            $lead[] = '<span class="promo-title">' . $e($title) . '</span>';
        }
    }
    if ($o['sub'] !== '') {
        $lead[] = '<span class="promo-sub">' . $e($o['sub']) . '</span>';
    }
    $in = $pad . '      ';
    $copy = [];
    if ($lead !== []) {
        $copy[] = '<span class="promo-lead">' . implode("\n" . $in, $lead) . '</span>';
    }
    if ($o['value'] !== '') {
        $copy[] = '<span class="promo-off">' . $e($o['value']) . '</span>';
    }
    if ($o['fine'] !== '') {
        $copy[] = '<span class="promo-fine">' . implode(' <br />', array_map($e, explode("\n", $o['fine']))) . '</span>';
    }

    $book = $o['button'] !== '' ? $o['button'] : ($o['action'] === 'prices' ? 'View in price list' : 'Book your appointment');
    $html = $pad . "<li>\n"
        . $pad . '  <button type="button" class="promo ' . $e($o['style']) . '" data-promo="' . $e($o['id']) . '"' . ($o['day'] !== '' ? ' data-day="' . $e($o['day']) . '"' : '') . ($o['until'] !== '' ? ' data-until="' . $e($o['until']) . '"' : '') . ">\n"
        . $pad . '    <span class="' . $draw[0] . '" aria-hidden="true"></span>' . "\n"
        . $pad . '    <span class="' . $draw[1] . '" aria-hidden="true"></span>' . "\n"
        . $pad . "    <span class=\"promo-copy\">\n"
        . ($copy !== [] ? $in . implode("\n" . $in, $copy) . "\n" : '')
        . $pad . "    </span>\n"
        . $pad . "  </button>\n"
        . $pad . '  <div class="promo-detail" data-promo-detail="' . $e($o['id']) . '" data-book-label="' . $e($book) . '" data-prices-hash="#' . $e($o['prices']) . '"' . ($o['action'] === 'prices' ? ' data-in-salon' : '') . " hidden>\n";
    foreach (['kicker', 'headline', 'text', 'terms'] as $part) {
        $html .= $pad . '    <p data-part="' . $part . '">' . $e(yuzu_round_offer_plain($o[$part])) . "</p>\n";
    }
    return $html . $pad . "  </div>\n" . $pad . "</li>\n";
}

/** The homepage structured data lists the same offers, linked to their trays. */
function yuzu_round_offer_schema_name(array $o): string
{
    $name = str_replace("\n", ' ', $o['title'] !== '' ? $o['title'] : $o['headline']);
    if ($o['sub'] !== '') {
        $lower = preg_match('/^\p{Ll}/u', $o['sub']) === 1;
        $name .= ($lower ? ' ' : ' · ') . $o['sub'];
    }
    return $name . ($o['value'] !== '' ? ' — ' . $o['value'] : '');
}

/** Swaps only the "makesOffer" list, so the rest of the salon's structured data keeps its layout. */
function yuzu_round_offer_schema(string $html, array $offers): string
{
    if (preg_match('/\n([ \t]*)"makesOffer": \[/', $html, $m, PREG_OFFSET_CAPTURE) !== 1) {
        return $html;
    }
    $pad = $m[1][0];
    $start = $m[0][1] + 1;
    $open = $m[0][1] + strlen($m[0][0]) - 1;
    $depth = 0;
    $in_string = false;
    $end = -1;
    for ($i = $open, $n = strlen($html); $i < $n; $i++) {
        $c = $html[$i];
        if ($in_string) {
            if ($c === '\\') {
                $i++;
            } elseif ($c === '"') {
                $in_string = false;
            }
        } elseif ($c === '"') {
            $in_string = true;
        } elseif ($c === '[' || $c === '{') {
            $depth++;
        } elseif ($c === ']' || $c === '}') {
            $depth--;
            if ($depth === 0) {
                $end = $i + 1;
                break;
            }
        }
    }
    if ($end < 0) {
        return $html;
    }
    $list = [];
    foreach ($offers as $o) {
        $item = [
            '@type' => 'Offer',
            'name' => yuzu_round_offer_schema_name($o),
            'description' => yuzu_round_offer_plain($o['text'] !== '' ? $o['text'] : $o['terms']),
            'url' => 'https://yuzu.hair/#' . $o['id'],
        ];
        if ($o['from'] !== '') {
            $item['validFrom'] = $o['from'];
        }
        if ($o['until'] !== '') {
            $item['validThrough'] = $o['until'];
        }
        if ($o['action'] === 'prices' && preg_match('/^£(\d+(?:\.\d{2})?)$/u', $o['value'], $price) === 1) {
            $item['price'] = $price[1];
            $item['priceCurrency'] = 'GBP';
        }
        $list[] = $item;
    }
    if ($list === []) {
        $before = substr($html, 0, $m[0][1]);
        $after = substr($html, $end);
        if (preg_match('/^\s*,/', $after) === 1) {
            return $before . preg_replace('/^\s*,/', '', $after, 1);
        }
        return preg_replace('/,\s*$/', '', $before, 1) . $after;
    }
    $json = (string) json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
    $json = preg_replace_callback('/^( +)/m', static fn (array $x): string => str_repeat(' ', intdiv(strlen($x[1]), 2)), $json) ?? $json;
    $json = str_replace("\n", "\n" . $pad, $json);
    return substr($html, 0, $start) . $pad . '"makesOffer": ' . $json . substr($html, $end);
}

function yuzu_round_offer_apply(string $html, string $file, ?array $offers = null): string
{
    $home = basename($file) === 'index.html' && str_contains($html, '<!-- yuzu-offers -->');
    $menu = '/\n[ \t]*<a href="(?:index\.html)?#offers">Offers<\/a>/';
    $has_menu = preg_match($menu, $html) === 1;
    $has_ig = str_contains($html, '<!-- yuzu-ig-offers -->');
    $has_phrase = str_contains($html, '<span data-yuzu-offers>');
    if (!$home && !$has_menu && !$has_ig && !$has_phrase) {
        return $html;
    }
    $offers = $offers ?? yuzu_round_offer_current();
    $shown = array_values(array_filter($offers, static fn (array $o): bool => yuzu_round_offer_live($o)));
    if ($shown === [] && $has_menu) {
        $html = preg_replace($menu, '', $html, 1) ?? $html;
    }
    if ($has_phrase) {
        $phrase = htmlspecialchars(yuzu_round_offer_phrase($offers), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = preg_replace_callback('/(<span data-yuzu-offers>)(.*?)(<\/span>)/s', static fn (array $m): string => $m[1] . $phrase . $m[3], $html) ?? $html;
    }
    if ($has_ig && function_exists('yuzu_round_lib_replace_block')) {
        $listed = array_values(array_filter($offers, static fn (array $o): bool => $o['ig'] && yuzu_round_offer_live($o)));
        $pad = preg_match('/\n([ \t]*)<!-- yuzu-ig-offers -->/', $html, $m) === 1 ? $m[1] : '        ';
        $inner = '';
        foreach ($listed as $offer) {
            $inner .= yuzu_round_offer_ig_html($offer, $pad);
        }
        $html = yuzu_round_lib_replace_block($html, 'yuzu-ig-offers', $inner, $listed === []);
    }
    if (!$home) {
        return $html;
    }
    $pad = preg_match('/\n([ \t]*)<!-- yuzu-offers -->/', $html, $m) === 1 ? $m[1] : '        ';
    $inner = '';
    foreach ($shown as $offer) {
        $inner .= yuzu_round_offer_card_html($offer, $pad);
    }
    if (function_exists('yuzu_round_lib_replace_block')) {
        $html = yuzu_round_lib_replace_block($html, 'yuzu-offers', $inner, $shown === []);
    }
    return yuzu_round_offer_schema($html, $shown);
}

/* ---------- wp-admin ---------- */

function yuzu_round_offer_select(string $name, array $options, string $current): string
{
    $html = '<select name="' . esc_attr($name) . '">';
    foreach ($options as $value => $label) {
        $html .= '<option value="' . esc_attr((string) $value) . '"' . ((string) $value === $current ? ' selected' : '') . '>' . esc_html($label) . '</option>';
    }
    return $html . '</select>';
}

function yuzu_round_offer_short_date(string $iso): string
{
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $iso, new DateTimeZone('Europe/London'));
    return $d instanceof DateTimeImmutable ? $d->format('D j M Y') : $iso;
}

/** What the owner sees next to each offer: whether it is on the site today, and why not. */
function yuzu_round_offer_status(array $o, ?string $today = null): array
{
    $today = $today ?? yuzu_round_offer_today();
    if (!$o['home']) {
        return ['off', 'Switched off'];
    }
    if ($o['from'] !== '' && $o['from'] > $today) {
        return ['soon', 'Starts ' . yuzu_round_offer_short_date($o['from'])];
    }
    if ($o['until'] !== '' && $o['until'] < $today) {
        return ['ended', 'Ended ' . yuzu_round_offer_short_date($o['until'])];
    }
    return ['live', $o['until'] !== '' ? 'On the site until ' . yuzu_round_offer_short_date($o['until']) : 'On the site · ongoing'];
}

function yuzu_round_offer_admin_card(string $i, array $o): string
{
    $base = 'yuzu_offers[' . $i . ']';
    $styles = yuzu_round_offer_styles();
    $style_options = array_map(static fn (array $s): string => $s['label'], $styles);
    $swatch = $styles[$o['style']]['swatch'] ?? '#2f3b25';
    $name = trim(str_replace("\n", ' ', $o['title'])) !== '' ? str_replace("\n", ' ', $o['title']) : 'New offer';

    [$state, $status] = $o['title'] === '' && $o['headline'] === '' ? ['live', 'New'] : yuzu_round_offer_status($o);
    $html = '<div class="yoff-card">';
    $html .= '<div class="yoff-head"><span class="yoff-swatch" style="background:' . esc_attr($swatch) . '"></span><strong class="yoff-name">' . esc_html($name) . '</strong>';
    $html .= '<label class="yoff-home"><input type="checkbox" name="' . $base . '[home]" value="1"' . ($o['home'] ? ' checked' : '') . ' /> Show on homepage</label>';
    $html .= '<span class="yoff-status is-' . esc_attr($state) . '">' . esc_html($status) . '</span>';
    $html .= '<label class="yoff-home"><input type="hidden" name="' . $base . '[ig]" value="0" /><input type="checkbox" name="' . $base . '[ig]" value="1"' . ($o['ig'] ? ' checked' : '') . ' /> Instagram page</label>';
    $html .= '<span class="yoff-tools"><button type="button" class="button-link yoff-up">Move up</button> · <button type="button" class="button-link yoff-down">Move down</button><label class="yoff-remove"><input type="checkbox" name="' . $base . '[remove]" value="1" /> Remove</label></span></div>';

    $html .= '<div class="yoff-cols"><div class="yoff-col"><h3>Card</h3>';
    $html .= '<p><label>Title <small>(a second line makes a second title line)</small><textarea rows="2" class="large-text yoff-title" name="' . $base . '[title]">' . esc_textarea($o['title']) . '</textarea></label></p>';
    $html .= '<p><label>Line under the title<input type="text" class="large-text" maxlength="60" name="' . $base . '[sub]" value="' . esc_attr($o['sub']) . '" placeholder="with a Creative Director" /></label></p>';
    $html .= '<p><label>Big figure<input type="text" maxlength="14" name="' . $base . '[value]" value="' . esc_attr($o['value']) . '" placeholder="50% Off" /></label></p>';
    $html .= '<p><label>Small print on the card <small>(up to 3 short lines)</small><textarea rows="3" class="large-text" name="' . $base . '[fine]">' . esc_textarea($o['fine']) . '</textarea></label></p>';
    $html .= '<p class="yoff-row"><label>Look' . yuzu_round_offer_select($base . '[style]', $style_options, $o['style']) . '</label>';
    $html .= '<label>Day <small>(shows “Today”)</small>' . yuzu_round_offer_select($base . '[day]', yuzu_round_offer_days(), $o['day']) . '</label></p>';
    $html .= '<p class="yoff-row yoff-dates"><label>From<input type="date" name="' . $base . '[from]" value="' . esc_attr($o['from']) . '" /></label>';
    $html .= '<label>Until <small>(last day)</small><input type="date" name="' . $base . '[until]" value="' . esc_attr($o['until']) . '" /></label>';
    $html .= '<small class="yoff-hint">Leave both empty for an ongoing offer; it then runs until “Show on homepage” is unticked.</small></p>';
    $html .= '</div><div class="yoff-col"><h3>Details <small>(open when the card is tapped)</small></h3>';
    $html .= '<p><label>Label<input type="text" class="large-text" maxlength="80" name="' . $base . '[kicker]" value="' . esc_attr($o['kicker']) . '" placeholder="Every Tuesday · Creative Director" /></label></p>';
    $html .= '<p><label>Headline<input type="text" class="large-text" maxlength="100" name="' . $base . '[headline]" value="' . esc_attr($o['headline']) . '" /></label></p>';
    $html .= '<p><label>Text<textarea rows="3" class="large-text" name="' . $base . '[text]">' . esc_textarea($o['text']) . '</textarea></label></p>';
    $html .= '<p><label>Terms<textarea rows="2" class="large-text" name="' . $base . '[terms]">' . esc_textarea($o['terms']) . '</textarea></label></p>';
    $html .= '<p><label>In one line <small>(Instagram page, Questions and Terms)</small><input type="text" class="large-text" maxlength="160" name="' . $base . '[summary]" value="' . esc_attr($o['summary']) . '" placeholder="50% off your most expensive colour service" /></label></p>';
    $html .= '<p class="yoff-row"><label>Main button' . yuzu_round_offer_select($base . '[action]', ['book' => 'Book online (Phorest)', 'prices' => 'Buy in salon (opens price list)'], $o['action']) . '</label>';
    $html .= '<label>Button text<input type="text" maxlength="40" name="' . $base . '[button]" value="' . esc_attr($o['button']) . '" placeholder="Book your appointment" /></label>';
    $html .= '<label>Prices section' . yuzu_round_offer_select($base . '[prices]', yuzu_round_offer_price_sections(), $o['prices']) . '</label></p>';
    $html .= '<p class="yoff-link"><label>Link name<input type="text" maxlength="40" name="' . $base . '[id]" value="' . esc_attr($o['id']) . '" placeholder="made from the title" /></label>';
    $html .= $o['id'] !== '' ? '<a href="' . esc_url(home_url('/#' . $o['id'])) . '" target="_blank" rel="noopener">' . esc_html(preg_replace('#^https?://#', '', home_url('/#' . $o['id']))) . '</a>' : '';
    $html .= '</p></div></div>';
    return $html . '</div>';
}

function yuzu_round_offer_admin_page(): void
{
    if (!current_user_can('edit_pages')) {
        wp_die(esc_html__('Sorry, you are not allowed to edit these.', 'yuzu-round'), '', ['response' => 403]);
    }
    $offers = yuzu_round_offer_current();
    $shown = count(array_filter($offers, static fn (array $o): bool => yuzu_round_offer_live($o)));
    echo '<div class="wrap yoff">';
    echo '<h1>Offers</h1>';
    if (isset($_GET['yuzu_saved'])) {
        echo '<div class="notice notice-success is-dismissible"><p>Saved. The homepage offers, the offers Google reads, and the offers on the Instagram, Questions and Terms pages have been updated. <a href="' . esc_url(home_url('/#offers')) . '" target="_blank" rel="noopener">View offers</a></p></div>';
    }
    echo '<p class="yoff-lead">Each offer is a card in the homepage <a href="' . esc_url(home_url('/#offers')) . '" target="_blank" rel="noopener">Offers</a> section, in this order, three to a row. Tapping a card opens its details with a button. Untick “Show on homepage” to keep an offer here without showing it. Offers with a day get a “Today” flag on that day, and the next offer day is flagged with its name. An offer with dates appears on its first day and goes on the day after its last; without dates it runs until unticked. Each offer’s title and “In one line” also make up the offers list on the <a href="' . esc_url(home_url('/instagram/')) . '" target="_blank" rel="noopener">Instagram page</a> (tick “Instagram page” as well), the offers answer on the Questions page and the offers paragraph in the Terms (every offer on the homepage). The details may use <code>{hours}</code> and <code>{from:cut}</code>, <code>{from:colour}</code>, <code>{from:highlights}</code> or <code>{from:treatments}</code>.</p>';
    echo '<p class="yoff-count"><strong>' . (int) $shown . '</strong> on the homepage today' . ($shown % 3 !== 0 && $shown > 0 ? ' <span>· rows look best with 3 or 6</span>' : '') . ($shown === 0 ? ' <span>· the Offers section is hidden</span>' : '') . '</p>';
    echo '<form method="post">';
    wp_nonce_field('yuzu_round_offers_save');
    echo '<input type="hidden" name="yuzu_offers_save" value="1" />';
    echo '<div class="yoff-list">';
    foreach ($offers as $n => $offer) {
        echo yuzu_round_offer_admin_card((string) $n, $offer);
    }
    echo '</div>';
    $blank = ['id' => '', 'home' => true, 'style' => 'forest', 'day' => '', 'title' => '', 'sub' => '', 'value' => '', 'fine' => '', 'kicker' => '', 'headline' => '', 'text' => '', 'terms' => '', 'action' => 'book', 'button' => '', 'prices' => 'cut', 'summary' => '', 'ig' => false, 'from' => '', 'until' => ''];
    echo '<template id="yoff-blank">' . yuzu_round_offer_admin_card('__i__', $blank) . '</template>';
    echo '<p><button type="button" class="button yoff-add">Add an offer</button></p>';
    submit_button('Save offers');
    echo '</form></div>';
}

function yuzu_round_offer_admin_script(): string
{
    $swatches = wp_json_encode(array_map(static fn (array $s): string => $s['swatch'], yuzu_round_offer_styles()));
    return 'var yoffSwatch = ' . $swatches . ";\n" . <<<'JS'
document.addEventListener('click', function (e) {
  var t = e.target;
  if (t.classList.contains('yoff-add')) {
    var tpl = document.getElementById('yoff-blank').innerHTML.replace(/__i__/g, 'n' + Date.now());
    var list = document.querySelector('.yoff-list');
    list.insertAdjacentHTML('beforeend', tpl);
    list.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'center' });
    list.lastElementChild.querySelector('textarea').focus();
    return;
  }
  var card = t.closest('.yoff-card');
  if (!card) return;
  if (t.classList.contains('yoff-up') && card.previousElementSibling) {
    card.parentNode.insertBefore(card, card.previousElementSibling);
  } else if (t.classList.contains('yoff-down') && card.nextElementSibling) {
    card.parentNode.insertBefore(card.nextElementSibling, card);
  }
});
document.addEventListener('input', function (e) {
  var card = e.target.closest('.yoff-card');
  if (!card) return;
  if (e.target.classList.contains('yoff-title')) {
    card.querySelector('.yoff-name').textContent = e.target.value.replace(/\s+/g, ' ').trim() || 'New offer';
  }
});
document.addEventListener('change', function (e) {
  var card = e.target.closest('.yoff-card');
  if (card && e.target.name && /\[style\]$/.test(e.target.name)) {
    card.querySelector('.yoff-swatch').style.background = yoffSwatch[e.target.value] || '';
  }
});
JS;
}

function yuzu_round_offer_handle_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !isset($_POST['yuzu_offers_save'])) {
        return;
    }
    if (!current_user_can('edit_pages')) {
        return;
    }
    check_admin_referer('yuzu_round_offers_save');
    $posted = wp_unslash($_POST['yuzu_offers'] ?? []);
    $clean = yuzu_round_offer_sanitize(is_array($posted) ? array_values($posted) : []);
    update_option('yuzu_round_offers', $clean, true);
    update_option('yuzu_round_offers_live', yuzu_round_offer_live_ids($clean), true);
    if (function_exists('yuzu_round_mark_edited')) {
        yuzu_round_mark_edited(['home', 'instagram', 'questions', 'terms']);
    }
    if (function_exists('yuzu_round_prices_purge_cache')) {
        yuzu_round_prices_purge_cache();
    }
    wp_safe_redirect(add_query_arg(['yuzu_saved' => '1'], admin_url('admin.php?page=yuzu-offers')));
    exit;
}

function yuzu_round_offer_live_ids(array $offers): string
{
    return implode(',', array_column(array_filter($offers, static fn (array $o): bool => yuzu_round_offer_live($o)), 'id'));
}

function yuzu_round_offer_purge(): void
{
    if (function_exists('yuzu_round_prices_purge_cache')) {
        yuzu_round_prices_purge_cache();
    }
    if (function_exists('do_action')) {
        do_action('breeze_clear_varnish');
    }
    if (function_exists('wp_remote_request') && function_exists('home_url')) {
        @wp_remote_request(home_url('/.*'), [
            'method' => 'PURGE',
            'timeout' => 1,
            'blocking' => false,
            'headers' => ['X-Purge-Method' => 'regex'],
        ]);
    }
}

/**
 * Cached pages keep yesterday's offers, so the first request of a day on which
 * a dated offer starts or ends (and a daily WordPress event) clears the caches.
 */
function yuzu_round_offer_check_day(): void
{
    $stored = get_option('yuzu_round_offers');
    if (!is_array($stored)) {
        return;
    }
    $offers = yuzu_round_offer_sanitize($stored);
    $dated = array_filter($offers, static fn (array $o): bool => $o['from'] !== '' || $o['until'] !== '');
    $live = yuzu_round_offer_live_ids($offers);
    $seen = get_option('yuzu_round_offers_live');
    if ($seen === $live || ($dated === [] && $seen !== false)) {
        return;
    }
    update_option('yuzu_round_offers_live', $live, true);
    if (function_exists('yuzu_round_mark_edited')) {
        yuzu_round_mark_edited(['home', 'instagram', 'questions', 'terms']);
    }
    yuzu_round_offer_purge();
}

if (function_exists('add_action')) {
    add_action('init', static function (): void {
        try {
            yuzu_round_offer_check_day();
            if (function_exists('wp_next_scheduled') && !wp_next_scheduled('yuzu_round_offers_daily')) {
                $at = new DateTimeImmutable('tomorrow 00:05', new DateTimeZone('Europe/London'));
                wp_schedule_event($at->getTimestamp(), 'daily', 'yuzu_round_offers_daily');
            }
        } catch (Throwable $e) {
            // A date check must never interrupt a request.
        }
    }, 30);
    add_action('yuzu_round_offers_daily', 'yuzu_round_offer_check_day');
    add_action('admin_init', static function (): void {
        if (get_option('yuzu_round_offers') === false) {
            update_option('yuzu_round_offers', yuzu_round_offer_from_seed(), true);
        }
    });
    add_action('admin_menu', static function (): void {
        $hook = add_menu_page('Offers', 'Offers', 'edit_pages', 'yuzu-offers', 'yuzu_round_offer_admin_page', 'dashicons-tickets-alt', 27);
        if (!is_string($hook)) {
            return;
        }
        add_action('load-' . $hook, 'yuzu_round_offer_handle_post');
        add_action('admin_enqueue_scripts', static function (string $current) use ($hook): void {
            if ($current !== $hook) {
                return;
            }
            wp_register_script('yuzu-offers-admin', false, [], null, true);
            wp_enqueue_script('yuzu-offers-admin');
            wp_add_inline_script('yuzu-offers-admin', yuzu_round_offer_admin_script());
            wp_register_style('yuzu-offers-admin', false);
            wp_enqueue_style('yuzu-offers-admin');
            wp_add_inline_style('yuzu-offers-admin', '
              .yoff .yoff-lead { max-width: 860px; }
              .yoff-count span { color: #646970; }
              .yoff-list { display: grid; gap: 16px; max-width: 1100px; }
              .yoff-card { background: #fff; border: 1px solid #c3c4c7; padding: 10px 16px 14px; }
              .yoff-head { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 18px; padding-bottom: 8px; border-bottom: 1px solid #f0f0f1; }
              .yoff-swatch { width: 22px; height: 22px; border-radius: 6px; border: 1px solid rgb(0 0 0 / 15%); }
              .yoff-name { font-size: 15px; }
              .yoff-tools { margin-left: auto; color: #646970; }
              .yoff-status { border-radius: 999px; padding: 2px 10px; font-size: 12px; background: #edfaef; color: #00450c; }
              .yoff-status.is-soon { background: #fcf9e8; color: #614200; }
              .yoff-status.is-ended, .yoff-status.is-off { background: #f0f0f1; color: #50575e; }
              .yoff-dates input[type=date] { display: block; margin-top: 4px; font-weight: 400; }
              .yoff-dates .yoff-hint { flex-basis: 100%; }
              .yoff-tools .yoff-remove { margin-left: 14px; color: #b32d2e; }
              .yoff-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 0 28px; }
              .yoff-col h3 { font-size: 13px; text-transform: uppercase; letter-spacing: .04em; color: #50575e; margin: 12px 0 4px; }
              .yoff-col h3 small { text-transform: none; letter-spacing: 0; font-weight: 400; }
              .yoff-col label { display: block; font-weight: 600; }
              .yoff-col small { font-weight: 400; color: #646970; }
              .yoff-col input[type=text], .yoff-col textarea, .yoff-col select { display: block; margin-top: 4px; font-weight: 400; }
              .yoff-row { display: flex; flex-wrap: wrap; gap: 12px 16px; align-items: flex-end; }
              .yoff-link { display: flex; flex-wrap: wrap; gap: 6px 14px; align-items: flex-end; }
              .yoff-link a { padding-bottom: 6px; }
              @media (max-width: 960px) { .yoff-cols { grid-template-columns: 1fr; } }
            ');
        });
    }, 12);
}
