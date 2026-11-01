<?php
/**
 * One list of reviews and one list of questions for the whole site. Each item
 * is ticked onto the pages where it shows, so the same words are typed once.
 * Templates mark the spots with <!-- yuzu-reviews --> and <!-- yuzu-faq -->;
 * what sits between the markers is the theme default and is replaced here.
 * Answers may use {hours} (Salon details) and {from:cut|colour|highlights|
 * treatments} (Prices) and {offers} (Offers), so they never repeat a price,
 * time or offer by hand.
 */

function yuzu_round_lib_review_pages(): array
{
    return [
        'home' => ['label' => 'Home', 'file' => 'index.html', 'max' => 3, 'photo' => true],
        'instagram' => ['label' => 'Instagram page', 'file' => 'instagram.html', 'max' => 3],
        'haircut-ealing' => ['label' => 'Haircuts', 'file' => 'haircut-ealing.html', 'max' => 2],
        'hair-colour-ealing' => ['label' => 'Hair colour', 'file' => 'hair-colour-ealing.html', 'max' => 2],
        'balayage-ealing' => ['label' => 'Balayage', 'file' => 'balayage-ealing.html', 'max' => 2],
        'hair-smoothing-ealing' => ['label' => 'Smoothing', 'file' => 'hair-smoothing-ealing.html', 'max' => 2],
    ];
}

function yuzu_round_lib_faq_pages(): array
{
    return [
        'questions' => ['label' => 'Questions page', 'file' => 'questions.html', 'max' => 12],
        'haircut-ealing' => ['label' => 'Haircuts', 'file' => 'haircut-ealing.html', 'max' => 6],
        'hair-colour-ealing' => ['label' => 'Hair colour', 'file' => 'hair-colour-ealing.html', 'max' => 6],
        'balayage-ealing' => ['label' => 'Balayage', 'file' => 'balayage-ealing.html', 'max' => 6],
        'hair-smoothing-ealing' => ['label' => 'Smoothing', 'file' => 'hair-smoothing-ealing.html', 'max' => 6],
    ];
}

/** The theme's starting lists. `legacy` names the Page copy fields each item replaces. */
function yuzu_round_lib_seed(): array
{
    $services = ['hair-colour-ealing', 'balayage-ealing', 'hair-smoothing-ealing'];
    return [
        'reviews' => [
            [
                'quote' => 'Amazing — focused on every detail',
                'text' => 'Listened to my requests and delivered exactly what I wanted. I already booked my follow-up.',
                'name' => 'Alexa',
                'photo' => 0,
                'theme_photo' => 'assets/reviews/1.webp',
                'alt' => 'Curly black bob with a fringe, cut at YUZU hair, Ealing',
                'pages' => array_merge(['home', 'instagram', 'haircut-ealing'], $services),
                'legacy' => [['home', 'home-review-1'], ['haircut-ealing', 'haircut-ealing-review-2'], ['hair-colour-ealing', 'hair-colour-ealing-review-2'], ['balayage-ealing', 'balayage-ealing-review-2'], ['hair-smoothing-ealing', 'hair-smoothing-ealing-review-1']],
            ],
            [
                'quote' => 'I always leave feeling a million dollars',
                'text' => 'Cut and colour once again exactly how I asked. Calm, expert and friendly throughout.',
                'name' => 'Rachel',
                'photo' => 0,
                'theme_photo' => 'assets/reviews/2.webp',
                'alt' => 'Copper red colour with soft waves, finished at YUZU hair, Ealing',
                'pages' => array_merge(['home', 'instagram'], $services),
                'legacy' => [['home', 'home-review-2'], ['hair-colour-ealing', 'hair-colour-ealing-review-1'], ['balayage-ealing', 'balayage-ealing-review-1'], ['hair-smoothing-ealing', 'hair-smoothing-ealing-review-2']],
            ],
            [
                'quote' => 'A brilliant artist working in hair',
                'text' => 'Jasmine is not just another haircut, but a brilliant artist working in hair.',
                'name' => 'Steve',
                'photo' => 0,
                'theme_photo' => 'assets/reviews/steve.webp',
                'alt' => 'Gents skin fade with a long curly top, cut at YUZU hair, Ealing',
                'pages' => ['home', 'instagram', 'haircut-ealing'],
                'legacy' => [['home', 'home-review-3'], ['haircut-ealing', 'haircut-ealing-review-1']],
            ],
        ],
        'faq' => [
            ['q' => 'How much is a haircut at YUZU hair in Ealing?', 'a' => 'It depends on the stylist level and who the cut is for. Cuts start from {from:cut}; the price list shows ladies’, gents’ and children’s cuts for every level.', 'link' => 'prices.html', 'link_text' => 'See full price list →', 'pages' => ['questions', 'haircut-ealing']],
            ['q' => 'Do I need a patch test?', 'a' => 'Yes. All clients, new and existing, need a patch test at least 48 hours before any colour service. Walk in for one, no appointment needed.', 'link' => 'patch.html', 'link_text' => 'Read patch testing guide →', 'pages' => ['questions', 'hair-colour-ealing', 'balayage-ealing']],
            ['q' => 'Where is the salon?', 'a' => '5 Dickens Yard, Longfield Avenue, Ealing, W5 2TD — a short walk from Ealing Broadway station. This is YUZU hair in Ealing, not YUZUHAIR in Hucknall.', 'link' => 'https://maps.app.goo.gl/bhFS5wwkW3xxAdKd8', 'link_text' => 'Get directions →', 'pages' => ['questions'], 'legacy' => [['questions', 'questions-where']]],
            ['q' => 'When are you open?', 'a' => '{hours}.', 'link' => '', 'link_text' => '', 'pages' => ['questions'], 'legacy' => [['questions', 'questions-open']]],
            ['q' => 'How do I book?', 'a' => 'Book on Phorest or call 020 8840 2244. Choose creative director, senior stylist, stylist, or junior stylist so the price matches the chair. Colour guests should complete a patch test at least 48 hours first.', 'link' => '', 'link_text' => '', 'pages' => ['questions'], 'legacy' => [['questions', 'questions-book']]],
            ['q' => 'What is your cancellation and deposit policy?', 'a' => 'We ask for 48 working hours’ notice to cancel or reschedule so your deposit remains refundable or transferable. Because we are closed on Sundays and Mondays, changes to Tuesday appointments must be made by the end of Thursday.', 'link' => 'terms.html', 'link_text' => 'Read full terms →', 'pages' => ['questions'], 'legacy' => [['questions', 'questions-cancel']]],
            ['q' => 'What offers and loyalty rewards do you run?', 'a' => 'We currently run {offers}. Colour offers need a patch test at least 48 hours before.', 'link' => 'index.html#offers', 'link_text' => 'See current offers →', 'pages' => ['questions'], 'legacy' => [['questions', 'questions-offers']]],
            ['q' => 'How do I join the team?', 'a' => 'Think you’ve got what it takes? We welcome senior stylists, stylists, and models. Email your CV to info@yuzuhairandbeauty.co.uk.', 'link' => 'team.html', 'link_text' => 'Join our team', 'pages' => ['questions'], 'legacy' => [['questions', 'questions-join']]],
            ['q' => 'Do you cut men’s and children’s hair?', 'a' => 'Yes. We cut gents’ hair, including fades, and children and teens of all ages.', 'link' => '', 'link_text' => '', 'pages' => ['haircut-ealing'], 'legacy' => [['haircut-ealing', 'haircut-ealing-faq-2']]],
            ['q' => 'Can I book a blow-dry on its own?', 'a' => 'Yes. Blow-dries can be booked on their own, and our 4 blow-drys package saves on regular appointments.', 'link' => '', 'link_text' => '', 'pages' => ['haircut-ealing'], 'legacy' => [['haircut-ealing', 'haircut-ealing-faq-3']]],
            ['q' => 'How do I choose a stylist level?', 'a' => 'Creative Directors suit big changes and precise shapes; Senior Stylists bring advanced cutting experience; Stylists are fully qualified for all cuts; Junior Stylists work under senior guidance at a lower price.', 'link' => '', 'link_text' => '', 'pages' => ['haircut-ealing'], 'legacy' => [['haircut-ealing', 'haircut-ealing-faq-4']]],
            ['q' => 'What are Colour Tuesdays and Colour Thursdays?', 'a' => 'On Tuesdays with a Creative Director, and on Thursdays with a Senior Stylist and Stylist, you get 50% off your most expensive colour service when you book it with a full priced wash, cut & blow-dry.', 'link' => '', 'link_text' => '', 'pages' => ['hair-colour-ealing'], 'legacy' => [['hair-colour-ealing', 'hair-colour-ealing-faq-2']]],
            ['q' => 'Which colour brand do you use?', 'a' => 'Our colour line includes Wella Illumina, chosen for shine and natural-looking tone.', 'link' => '', 'link_text' => '', 'pages' => ['hair-colour-ealing'], 'legacy' => [['hair-colour-ealing', 'hair-colour-ealing-faq-3']]],
            ['q' => 'Can you cover grey hair?', 'a' => 'Yes. Root touch-ups and full head tints are both used to cover grey; your colourist will suggest which suits your hair.', 'link' => '', 'link_text' => '', 'pages' => ['hair-colour-ealing'], 'legacy' => [['hair-colour-ealing', 'hair-colour-ealing-faq-4']]],
            ['q' => 'What is the difference between balayage and highlights?', 'a' => 'Balayage is painted freehand for a soft, natural grow-out. Highlights are woven into foils for a brighter, more even result. Foiliage combines the two.', 'link' => '', 'link_text' => '', 'pages' => ['balayage-ealing'], 'legacy' => [['balayage-ealing', 'balayage-ealing-faq-1']]],
            ['q' => 'Is toner included?', 'a' => 'Toner and blending are part of our highlight and balayage services. Anything extra your hair may need is listed under “Added when the hair needs it” in the prices above.', 'link' => '', 'link_text' => '', 'pages' => ['balayage-ealing'], 'legacy' => [['balayage-ealing', 'balayage-ealing-faq-2']]],
            ['q' => 'Which stylist should I book?', 'a' => 'Creative Directors and Senior Stylists are the usual choice for a first balayage or a big change. Tap a level above to see the price for each chair.', 'link' => '', 'link_text' => '', 'pages' => ['balayage-ealing'], 'legacy' => [['balayage-ealing', 'balayage-ealing-faq-4']]],
            ['q' => 'How long does Aura smoothing last?', 'a' => 'Three to six months, depending on the option you choose and how you care for your hair at home.', 'link' => '', 'link_text' => '', 'pages' => ['hair-smoothing-ealing'], 'legacy' => [['hair-smoothing-ealing', 'hair-smoothing-ealing-faq-1']]],
            ['q' => 'Is it formaldehyde free?', 'a' => 'Yes. Lakmé Aura smoothing is 100% formaldehyde and ammonia free.', 'link' => '', 'link_text' => '', 'pages' => ['hair-smoothing-ealing'], 'legacy' => [['hair-smoothing-ealing', 'hair-smoothing-ealing-faq-2']]],
            ['q' => 'What is Smooth Wednesdays?', 'a' => 'Every Wednesday you get 25% off our three to six month smoothing treatment.', 'link' => '', 'link_text' => '', 'pages' => ['hair-smoothing-ealing'], 'legacy' => [['hair-smoothing-ealing', 'hair-smoothing-ealing-faq-3']]],
            ['q' => 'How is the price worked out?', 'a' => 'By the length of your hair, from short to long. The price list above shows each length and the longer-lasting upgrade.', 'link' => '', 'link_text' => '', 'pages' => ['hair-smoothing-ealing'], 'legacy' => [['hair-smoothing-ealing', 'hair-smoothing-ealing-faq-4']]],
        ],
    ];
}

/** First run: the theme lists, with anything already edited under Page copy carried over. */
function yuzu_round_lib_from_seed(): array
{
    $copy = function_exists('get_option') ? get_option('yuzu_round_copy') : [];
    $copy = is_array($copy) ? $copy : [];
    $edited = static function (string $page, string $key) use ($copy): string {
        $value = $copy[$page][$key] ?? '';
        return is_string($value) ? trim($value) : '';
    };
    $seed = yuzu_round_lib_seed();
    foreach ($seed['reviews'] as &$review) {
        foreach ($review['legacy'] ?? [] as [$page, $prefix]) {
            $quote = $edited($page, $prefix . '-title');
            if ($quote !== '') {
                $review['quote'] = $quote;
            }
            if (($text = $edited($page, $prefix . '-text')) !== '') {
                $review['text'] = $text;
            }
            if (($name = $edited($page, $prefix . '-name')) !== '') {
                $review['name'] = $name;
            }
        }
        unset($review['legacy']);
    }
    unset($review);
    foreach ($seed['faq'] as &$item) {
        foreach ($item['legacy'] ?? [] as [$page, $prefix]) {
            if (($q = $edited($page, $prefix . '-q')) !== '') {
                $item['q'] = $q;
            }
            if (($a = $edited($page, $prefix . '-a')) !== '') {
                $item['a'] = $a;
            }
        }
        unset($item['legacy']);
    }
    unset($item);
    return yuzu_round_lib_sanitize($seed);
}

/** The offers answer as the theme first wrote it out by hand; untouched copies switch to {offers}. */
function yuzu_round_lib_old_offers_answer(): string
{
    return 'Colour Tuesdays and Colour Thursdays give 50% off your most expensive colour service with a full priced wash, cut and blow-dry (with a Creative Director on Tuesdays, a Senior Stylist and Stylist on Thursdays). Smooth Wednesdays give 25% off our three to six month smoothing treatment. We also offer £10 Refer a Friend credit, 10% off when you rebook on the same day, and a 4 blow-drys for £156 package.';
}

function yuzu_round_lib_current(): array
{
    $stored = function_exists('get_option') ? get_option('yuzu_round_library') : false;
    if (is_array($stored) && isset($stored['reviews'], $stored['faq'])) {
        $lib = yuzu_round_lib_sanitize($stored);
        foreach ($lib['faq'] as &$item) {
            if ($item['a'] === yuzu_round_lib_old_offers_answer()) {
                $item['a'] = 'We currently run {offers}. Colour offers need a patch test at least 48 hours before.';
            }
        }
        unset($item);
        return $lib;
    }
    return yuzu_round_lib_from_seed();
}

function yuzu_round_lib_clean_text(mixed $raw, int $max, bool $multiline = false): string
{
    $text = is_string($raw) ? $raw : '';
    $text = function_exists('wp_strip_all_tags') ? wp_strip_all_tags($text) : strip_tags($text);
    $text = $multiline ? preg_replace("/\r\n?/", "\n", $text) : preg_replace('/\s+/u', ' ', $text);
    $text = trim((string) $text);
    $text = trim($text, " \t\"“”");
    return function_exists('mb_substr') ? mb_substr($text, 0, $max) : substr($text, 0, $max);
}

/** Relative theme pages (prices.html, index.html#offers), /paths, https, mailto and tel. */
function yuzu_round_lib_clean_link(mixed $raw): string
{
    $link = trim(is_string($raw) ? $raw : '');
    if ($link === '') {
        return '';
    }
    if (preg_match('/^[a-z0-9-]+\.html(#[a-z0-9-]+)?$/i', $link) === 1 || preg_match('#^/[^\s"<>]*$#', $link) === 1) {
        return $link;
    }
    if (preg_match('#^(https://|mailto:|tel:)[^\s"<>]+$#i', $link) === 1) {
        return $link;
    }
    return '';
}

function yuzu_round_lib_sanitize(array $input): array
{
    $review_pages = array_keys(yuzu_round_lib_review_pages());
    $faq_pages = array_keys(yuzu_round_lib_faq_pages());
    $out = ['reviews' => [], 'faq' => []];
    foreach (is_array($input['reviews'] ?? null) ? $input['reviews'] : [] as $row) {
        if (!is_array($row) || !empty($row['remove'])) {
            continue;
        }
        $review = [
            'quote' => yuzu_round_lib_clean_text($row['quote'] ?? '', 160),
            'text' => yuzu_round_lib_clean_text($row['text'] ?? '', 600, true),
            'name' => yuzu_round_lib_clean_text($row['name'] ?? '', 60),
            'photo' => max(0, (int) ($row['photo'] ?? 0)),
            'theme_photo' => preg_match('#^assets/[a-z0-9/_-]+\.(webp|jpe?g|png)$#i', (string) ($row['theme_photo'] ?? '')) === 1 ? (string) $row['theme_photo'] : '',
            'alt' => yuzu_round_lib_clean_text($row['alt'] ?? '', 200),
            'pages' => array_values(array_intersect($review_pages, is_array($row['pages'] ?? null) ? $row['pages'] : [])),
        ];
        if ($review['photo'] > 0 && function_exists('wp_attachment_is_image') && !wp_attachment_is_image($review['photo'])) {
            $review['photo'] = 0;
        }
        if ($review['quote'] !== '' || $review['text'] !== '') {
            $out['reviews'][] = $review;
        }
    }
    foreach (is_array($input['faq'] ?? null) ? $input['faq'] : [] as $row) {
        if (!is_array($row) || !empty($row['remove'])) {
            continue;
        }
        $item = [
            'q' => yuzu_round_lib_clean_text($row['q'] ?? '', 200),
            'a' => yuzu_round_lib_clean_text($row['a'] ?? '', 1500, true),
            'link' => yuzu_round_lib_clean_link($row['link'] ?? ''),
            'link_text' => yuzu_round_lib_clean_text($row['link_text'] ?? '', 80),
            'pages' => array_values(array_intersect($faq_pages, is_array($row['pages'] ?? null) ? $row['pages'] : [])),
        ];
        if ($item['q'] !== '' && $item['a'] !== '') {
            $out['faq'][] = $item;
        }
    }
    return $out;
}

/* ---------- rendering ---------- */

function yuzu_round_lib_from_price(string $id): string
{
    if (function_exists('get_option') && function_exists('yuzu_round_prices_sanitize')) {
        $prices = yuzu_round_prices_sanitize(get_option('yuzu_round_prices'));
        $value = is_array($prices) ? trim((string) ($prices['home'][$id] ?? '')) : '';
        if ($value !== '') {
            return $value;
        }
    }
    static $template = null;
    if ($template === null) {
        $file = function_exists('yuzu_round_static_dir') ? yuzu_round_static_dir() . '/index.html' : '';
        $template = $file !== '' && is_readable($file) ? (string) file_get_contents($file) : '';
    }
    if (preg_match('/data-yuzu-from="' . preg_quote($id, '/') . '">[^<]*<strong>([^<]+)<\/strong>/', $template, $m) === 1) {
        return html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
    }
    return '';
}

/** Answer text with {hours} and {from:…} filled in, as HTML or plain text. */
function yuzu_round_lib_fill(string $text, bool $html): string
{
    $out = $html ? htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8') : $text;
    $out = preg_replace_callback('/\{from:([a-z]+)\}/', static function (array $m): string {
        $value = yuzu_round_lib_from_price($m[1]);
        return $value !== '' ? $value : 'our price list';
    }, $out) ?? $out;
    if (str_contains($out, '{offers}')) {
        $offers = function_exists('yuzu_round_offer_phrase') ? yuzu_round_offer_phrase() : 'seasonal offers';
        $out = str_replace('{offers}', $html ? '<span data-yuzu-offers>' . htmlspecialchars($offers, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</span>' : $offers, $out);
    }
    if (str_contains($out, '{hours}')) {
        $phrase = function_exists('yuzu_round_copy_hours_phrase') ? yuzu_round_copy_hours_phrase() : '';
        $out = str_replace('{hours}', $html ? '<span data-yuzu-hours>' . htmlspecialchars($phrase, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</span>' : $phrase, $out);
    }
    return $html ? nl2br($out, false) : $out;
}

function yuzu_round_lib_review_html(array $review, bool $photo, string $pad): string
{
    $e = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $lines = [
        '<p class="stars"><span aria-hidden="true">★★★★★</span><span class="vh">5 stars. </span></p>',
        '<h3>“' . $e($review['quote']) . '”</h3>',
        '<p>' . nl2br($e($review['text']), false) . '</p>',
        '<p class="review-author">' . $e($review['name'] !== '' ? $review['name'] : 'Guest') . ' · Google review</p>',
    ];
    if (!$photo) {
        return $pad . "<li>\n" . $pad . '  ' . implode("\n" . $pad . '  ', $lines) . "\n" . $pad . "</li>\n";
    }
    $img = '';
    $alt = $review['alt'] !== '' ? $review['alt'] : $review['quote'];
    if ($review['photo'] > 0 && function_exists('wp_get_attachment_image_src')) {
        $src = wp_get_attachment_image_src($review['photo'], 'medium_large');
        if (is_array($src) && !empty($src[0])) {
            $srcset = function_exists('wp_get_attachment_image_srcset') ? (string) wp_get_attachment_image_srcset($review['photo'], 'medium_large') : '';
            $img = '<img src="' . $e($src[0]) . '"' . ($srcset !== '' ? ' srcset="' . $e($srcset) . '" sizes="(max-width: 700px) 92vw, 390px"' : '')
                . ' alt="' . $e($alt) . '" width="' . (int) $src[1] . '" height="' . (int) $src[2] . '" loading="lazy" decoding="async" />';
        }
    }
    if ($img === '' && $review['theme_photo'] !== '') {
        $img = '<img src="' . $e($review['theme_photo']) . '" alt="' . $e($alt) . '" width="590" height="332" loading="lazy" decoding="async" />';
    }
    return $pad . "<li>\n"
        . ($img !== '' ? $pad . '  ' . $img . "\n" : '')
        . $pad . "  <div>\n" . $pad . '    ' . implode("\n" . $pad . '    ', $lines) . "\n" . $pad . "  </div>\n"
        . $pad . "</li>\n";
}

function yuzu_round_lib_faq_html(array $item, bool $open, string $pad): string
{
    $e = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $html = $pad . '<details' . ($open ? ' open' : '') . ">\n"
        . $pad . '  <summary>' . $e($item['q']) . "</summary>\n"
        . $pad . '  <p>' . yuzu_round_lib_fill($item['a'], true) . "</p>\n";
    if ($item['link'] !== '' && $item['link_text'] !== '') {
        $external = preg_match('#^https://#i', $item['link']) === 1;
        $html .= $pad . '  <p><a href="' . $e($item['link']) . '"' . ($external ? ' target="_blank" rel="noopener"' : '') . '>' . $e($item['link_text']) . "</a></p>\n";
    }
    return $html . $pad . "</details>\n";
}

function yuzu_round_lib_for_page(array $items, string $slug, int $max): array
{
    $picked = array_values(array_filter($items, static fn (array $i): bool => in_array($slug, $i['pages'], true)));
    return array_slice($picked, 0, $max);
}

/** Replace a marked block; an empty list removes the whole <section> around it. */
function yuzu_round_lib_replace_block(string $html, string $marker, string $inner, bool $empty): string
{
    $open = '<!-- ' . $marker . ' -->';
    $close = '<!-- /' . $marker . ' -->';
    $start = strpos($html, $open);
    $end = $start === false ? false : strpos($html, $close, $start);
    if ($start === false || $end === false) {
        return $html;
    }
    if ($empty) {
        $section = strrpos(substr($html, 0, $start), '<section');
        $section_end = strpos($html, '</section>', $end);
        $closed_between = $section !== false && strpos(substr($html, $section, $start - $section), '</section>') !== false;
        if ($section !== false && $section_end !== false && !$closed_between) {
            $line_start = strrpos(substr($html, 0, $section), "\n");
            $from = $line_start === false ? $section : $line_start;
            return substr($html, 0, $from) . substr($html, $section_end + strlen('</section>'));
        }
    }
    $line_start = strrpos(substr($html, 0, $start), "\n");
    $pad = $line_start === false ? '' : substr($html, $line_start + 1, $start - $line_start - 1);
    return substr($html, 0, $start + strlen($open)) . "\n" . $inner . $pad . substr($html, $end);
}

function yuzu_round_lib_faq_schema(string $html, array $items): string
{
    $pattern = '/(<script type="application\/ld\+json">\s*)(\{(?:(?!<\/script>).)*?"@type": "FAQPage"(?:(?!<\/script>).)*?\})(\s*<\/script>)/s';
    if (preg_match($pattern, $html, $m, PREG_OFFSET_CAPTURE) !== 1) {
        return $html;
    }
    $data = json_decode($m[2][0], true);
    if (!is_array($data)) {
        return $html;
    }
    if ($items === []) {
        $line = strrpos(substr($html, 0, $m[0][1]), "\n");
        $from = $line === false ? $m[0][1] : $line;
        return substr($html, 0, $from) . substr($html, $m[0][1] + strlen($m[0][0]));
    }
    $data['mainEntity'] = array_map(static fn (array $i): array => [
        '@type' => 'Question',
        'name' => $i['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => yuzu_round_lib_fill($i['a'], false)],
    ], $items);
    $json = (string) json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
    $json = preg_replace_callback('/^( +)/m', static fn (array $m): string => str_repeat(' ', intdiv(strlen($m[1]), 2)), $json) ?? $json;
    $json = str_replace("\n", "\n      ", $json);
    return substr($html, 0, $m[2][1]) . $json . substr($html, $m[2][1] + strlen($m[2][0]));
}

function yuzu_round_lib_apply(string $html, string $file, ?array $lib = null): string
{
    $file = basename($file);
    $has_reviews = str_contains($html, '<!-- yuzu-reviews -->');
    $has_faq = str_contains($html, '<!-- yuzu-faq -->');
    if (!$has_reviews && !$has_faq) {
        return $html;
    }
    $lib = $lib ?? yuzu_round_lib_current();
    if ($has_reviews) {
        foreach (yuzu_round_lib_review_pages() as $slug => $page) {
            if ($page['file'] !== $file) {
                continue;
            }
            $items = yuzu_round_lib_for_page($lib['reviews'], $slug, $page['max']);
            $pad = preg_match('/\n([ \t]*)<!-- yuzu-reviews -->/', $html, $m) === 1 ? $m[1] : '      ';
            $inner = '';
            foreach ($items as $review) {
                $inner .= yuzu_round_lib_review_html($review, !empty($page['photo']), $pad);
            }
            $html = yuzu_round_lib_replace_block($html, 'yuzu-reviews', $inner, $items === []);
        }
    }
    if ($has_faq) {
        foreach (yuzu_round_lib_faq_pages() as $slug => $page) {
            if ($page['file'] !== $file) {
                continue;
            }
            $items = yuzu_round_lib_for_page($lib['faq'], $slug, $page['max']);
            $pad = preg_match('/\n([ \t]*)<!-- yuzu-faq -->/', $html, $m) === 1 ? $m[1] : '      ';
            $inner = '';
            foreach ($items as $n => $item) {
                $inner .= yuzu_round_lib_faq_html($item, $slug === 'questions' && $n === 0, $pad);
            }
            $html = yuzu_round_lib_replace_block($html, 'yuzu-faq', $inner, $items === []);
            $html = yuzu_round_lib_faq_schema($html, $items);
        }
    }
    return $html;
}

/* ---------- wp-admin ---------- */

function yuzu_round_lib_checkboxes(string $name, array $pages, array $ticked): string
{
    $html = '<fieldset class="ylib-pages"><legend>Show on</legend>';
    foreach ($pages as $slug => $page) {
        $html .= '<label><input type="checkbox" name="' . esc_attr($name) . '[]" value="' . esc_attr($slug) . '"' . (in_array($slug, $ticked, true) ? ' checked' : '') . ' /> ' . esc_html($page['label']) . '</label>';
    }
    return $html . '</fieldset>';
}

function yuzu_round_lib_review_card(string $i, array $r): string
{
    $base = 'yuzu_lib[reviews][' . $i . ']';
    $thumb = '';
    if ($r['photo'] > 0 && function_exists('wp_get_attachment_image_url')) {
        $thumb = (string) wp_get_attachment_image_url($r['photo'], 'thumbnail');
    }
    if ($thumb === '' && $r['theme_photo'] !== '' && function_exists('yuzu_round_static_uri')) {
        $thumb = rtrim(yuzu_round_static_uri(), '/') . '/' . $r['theme_photo'];
    }
    $html = '<div class="ylib-card">';
    $html .= '<div class="ylib-tools"><button type="button" class="button-link ylib-up">Move up</button> · <button type="button" class="button-link ylib-down">Move down</button><label class="ylib-remove"><input type="checkbox" name="' . $base . '[remove]" value="1" /> Remove</label></div>';
    $html .= '<p><label>Quote heading<input type="text" class="large-text" name="' . $base . '[quote]" value="' . esc_attr($r['quote']) . '" /></label></p>';
    $html .= '<p><label>Review<textarea class="large-text" rows="3" name="' . $base . '[text]">' . esc_textarea($r['text']) . '</textarea></label></p>';
    $html .= '<p class="ylib-row"><label>First name<input type="text" name="' . $base . '[name]" value="' . esc_attr($r['name']) . '" /></label>';
    $html .= '<span class="ylib-photo"><img src="' . esc_url($thumb) . '" alt=""' . ($thumb === '' ? ' hidden' : '') . ' /><input type="hidden" name="' . $base . '[photo]" value="' . ($r['photo'] > 0 ? (int) $r['photo'] : '') . '" /><input type="hidden" name="' . $base . '[theme_photo]" value="' . esc_attr($r['theme_photo']) . '" /><button type="button" class="button ylib-pick">Photo for Home…</button></span></p>';
    $html .= '<p><label>Photo description (for screen readers and Google)<input type="text" class="large-text" name="' . $base . '[alt]" value="' . esc_attr($r['alt']) . '" /></label></p>';
    $html .= yuzu_round_lib_checkboxes($base . '[pages]', yuzu_round_lib_review_pages(), $r['pages']);
    return $html . '</div>';
}

function yuzu_round_lib_faq_card(string $i, array $q): string
{
    $base = 'yuzu_lib[faq][' . $i . ']';
    $html = '<div class="ylib-card">';
    $html .= '<div class="ylib-tools"><button type="button" class="button-link ylib-up">Move up</button> · <button type="button" class="button-link ylib-down">Move down</button><label class="ylib-remove"><input type="checkbox" name="' . $base . '[remove]" value="1" /> Remove</label></div>';
    $html .= '<p><label>Question<input type="text" class="large-text" name="' . $base . '[q]" value="' . esc_attr($q['q']) . '" /></label></p>';
    $html .= '<p><label>Answer<textarea class="large-text" rows="3" name="' . $base . '[a]">' . esc_textarea($q['a']) . '</textarea></label></p>';
    $html .= '<p class="ylib-row"><label>Link (optional)<input type="text" name="' . $base . '[link]" value="' . esc_attr($q['link']) . '" placeholder="prices.html or https://…" /></label><label>Link text<input type="text" name="' . $base . '[link_text]" value="' . esc_attr($q['link_text']) . '" placeholder="See full price list →" /></label></p>';
    $html .= yuzu_round_lib_checkboxes($base . '[pages]', yuzu_round_lib_faq_pages(), $q['pages']);
    return $html . '</div>';
}

function yuzu_round_lib_summary(array $items, array $pages): string
{
    $bits = [];
    foreach ($pages as $slug => $page) {
        $n = count(array_filter($items, static fn (array $i): bool => in_array($slug, $i['pages'], true)));
        $class = $n === 0 ? 'is-empty' : ($n > $page['max'] ? 'is-over' : '');
        $note = $n === 0 ? 'section hidden' : ($n > $page['max'] ? 'first ' . $page['max'] . ' show' : 'up to ' . $page['max']);
        $bits[] = '<li class="' . $class . '"><strong>' . esc_html($page['label']) . '</strong> ' . $n . ' <span>(' . esc_html($note) . ')</span></li>';
    }
    return '<ul class="ylib-summary">' . implode('', $bits) . '</ul>';
}

function yuzu_round_lib_admin_page(string $kind): void
{
    if (!current_user_can('edit_pages')) {
        wp_die(esc_html__('Sorry, you are not allowed to edit these.', 'yuzu-round'), '', ['response' => 403]);
    }
    $lib = yuzu_round_lib_current();
    $reviews = $kind === 'reviews';
    $items = $reviews ? $lib['reviews'] : $lib['faq'];
    $pages = $reviews ? yuzu_round_lib_review_pages() : yuzu_round_lib_faq_pages();
    echo '<div class="wrap ylib">';
    echo '<h1>' . ($reviews ? 'Reviews' : 'Questions') . '</h1>';
    if (isset($_GET['yuzu_saved'])) {
        echo '<div class="notice notice-success is-dismissible"><p>Saved. Every page that shows these has been updated.</p></div>';
    }
    if ($reviews) {
        echo '<p class="ylib-lead">One list for the whole site. Tick the pages each review appears on; pages show them in this order. Copy reviews word for word from <a href="https://maps.app.goo.gl/bhFS5wwkW3xxAdKd8" target="_blank" rel="noopener">Google</a>. The photo only shows on Home.</p>';
    } else {
        echo '<p class="ylib-lead">One list for the whole site. Tick the pages each question appears on; pages show them in this order, and Google gets the same list. In answers, <code>{hours}</code> shows the opening hours from Salon details and <code>{from:cut}</code>, <code>{from:colour}</code>, <code>{from:highlights}</code> or <code>{from:treatments}</code> show the “from” price set under Prices, and <code>{offers}</code> lists the homepage offers from <a href="' . esc_url(admin_url('admin.php?page=yuzu-offers')) . '">Offers</a>, so they never go out of date.</p>';
    }
    echo yuzu_round_lib_summary($items, $pages);
    echo '<form method="post">';
    wp_nonce_field('yuzu_round_lib_save');
    echo '<input type="hidden" name="yuzu_lib_kind" value="' . esc_attr($kind) . '" />';
    echo '<div class="ylib-list">';
    foreach ($items as $n => $item) {
        echo $reviews ? yuzu_round_lib_review_card((string) $n, $item) : yuzu_round_lib_faq_card((string) $n, $item);
    }
    echo '</div>';
    $blank = $reviews
        ? yuzu_round_lib_review_card('__i__', ['quote' => '', 'text' => '', 'name' => '', 'photo' => 0, 'theme_photo' => '', 'alt' => '', 'pages' => []])
        : yuzu_round_lib_faq_card('__i__', ['q' => '', 'a' => '', 'link' => '', 'link_text' => '', 'pages' => []]);
    echo '<template id="ylib-blank">' . $blank . '</template>';
    echo '<p><button type="button" class="button ylib-add">' . ($reviews ? 'Add a review' : 'Add a question') . '</button></p>';
    submit_button('Save ' . ($reviews ? 'reviews' : 'questions'));
    echo '</form></div>';
}

function yuzu_round_lib_admin_script(): string
{
    return <<<'JS'
document.addEventListener('click', function (e) {
  var t = e.target;
  var card = t.closest('.ylib-card');
  if (t.classList.contains('ylib-add')) {
    var tpl = document.getElementById('ylib-blank').innerHTML.replace(/__i__/g, 'n' + Date.now());
    document.querySelector('.ylib-list').insertAdjacentHTML('beforeend', tpl);
    var added = document.querySelector('.ylib-list').lastElementChild;
    added.scrollIntoView({ behavior: 'smooth', block: 'center' });
    var first = added.querySelector('input[type=text], textarea');
    if (first) first.focus();
    return;
  }
  if (!card) return;
  if (t.classList.contains('ylib-up') && card.previousElementSibling) {
    card.parentNode.insertBefore(card, card.previousElementSibling);
  } else if (t.classList.contains('ylib-down') && card.nextElementSibling) {
    card.parentNode.insertBefore(card.nextElementSibling, card);
  } else if (t.classList.contains('ylib-pick') && window.wp && wp.media) {
    var frame = wp.media({ title: 'Photo for this review', button: { text: 'Use this photo' }, library: { type: 'image' }, multiple: false });
    frame.on('select', function () {
      var a = frame.state().get('selection').first().toJSON();
      var box = t.closest('.ylib-photo');
      box.querySelector('input[name$="[photo]"]').value = a.id;
      var img = box.querySelector('img');
      img.src = a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;
      img.hidden = false;
    });
    frame.open();
  }
});
JS;
}

function yuzu_round_lib_handle_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !isset($_POST['yuzu_lib_kind'])) {
        return;
    }
    if (!current_user_can('edit_pages')) {
        return;
    }
    check_admin_referer('yuzu_round_lib_save');
    $kind = $_POST['yuzu_lib_kind'] === 'faq' ? 'faq' : 'reviews';
    $posted = wp_unslash($_POST['yuzu_lib'] ?? []);
    $lib = yuzu_round_lib_current();
    $lib[$kind] = is_array($posted[$kind] ?? null) ? array_values($posted[$kind]) : [];
    $clean = yuzu_round_lib_sanitize($lib);
    update_option('yuzu_round_library', $clean, true);
    if (function_exists('yuzu_round_mark_edited')) {
        $pages = $kind === 'faq' ? yuzu_round_lib_faq_pages() : yuzu_round_lib_review_pages();
        yuzu_round_mark_edited(array_keys($pages));
    }
    if (function_exists('yuzu_round_prices_purge_cache')) {
        yuzu_round_prices_purge_cache();
    }
    $page = $kind === 'faq' ? 'yuzu-questions' : 'yuzu-reviews';
    wp_safe_redirect(add_query_arg(['yuzu_saved' => '1'], admin_url('admin.php?page=' . $page)));
    exit;
}

if (function_exists('add_action')) {
    add_action('admin_menu', static function (): void {
        $hooks = [];
        $hooks[] = add_menu_page('Reviews', 'Reviews & questions', 'edit_pages', 'yuzu-reviews', static function (): void {
            yuzu_round_lib_admin_page('reviews');
        }, 'dashicons-format-quote', 26);
        $hooks[] = add_submenu_page('yuzu-reviews', 'Reviews', 'Reviews', 'edit_pages', 'yuzu-reviews', static function (): void {
            yuzu_round_lib_admin_page('reviews');
        });
        $hooks[] = add_submenu_page('yuzu-reviews', 'Questions', 'Questions', 'edit_pages', 'yuzu-questions', static function (): void {
            yuzu_round_lib_admin_page('faq');
        });
        $hooks = array_values(array_unique(array_filter($hooks, 'is_string')));
        foreach ($hooks as $hook) {
            add_action('load-' . $hook, 'yuzu_round_lib_handle_post');
        }
        add_action('admin_enqueue_scripts', static function (string $current) use ($hooks): void {
            if (!in_array($current, $hooks, true)) {
                return;
            }
            wp_enqueue_media();
            wp_add_inline_script('media-editor', yuzu_round_lib_admin_script());
            wp_register_style('yuzu-lib-admin', false);
            wp_enqueue_style('yuzu-lib-admin');
            wp_add_inline_style('yuzu-lib-admin', '
              .ylib .ylib-lead { max-width: 820px; }
              .ylib-summary { display: flex; flex-wrap: wrap; gap: 6px 16px; margin: 12px 0 18px; }
              .ylib-summary li { margin: 0; background: #fff; border: 1px solid #dcdcde; border-radius: 999px; padding: 3px 12px; }
              .ylib-summary span { color: #646970; }
              .ylib-summary .is-empty { border-color: #dba617; }
              .ylib-summary .is-over { border-color: #d63638; }
              .ylib-list { display: grid; gap: 14px; max-width: 900px; }
              .ylib-card { background: #fff; border: 1px solid #c3c4c7; padding: 6px 16px 14px; }
              .ylib-card label { display: block; font-weight: 600; }
              .ylib-card input[type=text], .ylib-card textarea { display: block; margin-top: 4px; font-weight: 400; }
              .ylib-row { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; }
              .ylib-photo { display: flex; align-items: center; gap: 10px; }
              .ylib-photo img { width: 72px; height: 48px; object-fit: cover; border-radius: 4px; }
              .ylib-tools { display: flex; gap: 6px; align-items: center; justify-content: flex-end; color: #646970; }
              .ylib-tools .ylib-remove { display: inline; font-weight: 400; margin-left: 14px; color: #b32d2e; }
              .ylib-pages { display: flex; flex-wrap: wrap; gap: 4px 18px; margin-top: 6px; }
              .ylib-pages legend { font-weight: 600; margin-bottom: 4px; }
              .ylib-pages label { font-weight: 400; display: inline; }
            ');
        });
    }, 11);
}
