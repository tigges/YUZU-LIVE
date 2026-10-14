<?php
if (defined('YUZU_ROUND_LOADED')) {
    return;
}
define('YUZU_ROUND_LOADED', true);

add_action('after_setup_theme', static function () {
    add_theme_support('title-tag');
});

add_action('after_switch_theme', static function () {
    try {
        yuzu_round_seed_pages();
        update_option('yuzu_round_seed', 'about-contact');
    } catch (Throwable $e) {
        // Page setup must not take the salon or wp-admin down.
    }
});

function yuzu_round_pages(): array
{
    return [
        'home' => ['title' => 'Home', 'file' => 'index.html'],
        'prices' => ['title' => 'Prices', 'file' => 'prices.html'],
        'offers' => ['title' => 'Offers', 'file' => 'offers.html'],
        'team' => ['title' => 'Join our team', 'file' => 'team.html'],
        'about' => ['title' => 'About', 'file' => 'about.html'],
        'contact' => ['title' => 'Contact', 'file' => 'contact.html'],
        'questions' => ['title' => 'Questions', 'file' => 'questions.html'],
        'patch' => ['title' => 'Patch testing', 'file' => 'patch.html'],
        'terms' => ['title' => 'Terms and conditions', 'file' => 'terms.html'],
        'privacy' => ['title' => 'Privacy', 'file' => 'privacy.html'],
    ];
}

function yuzu_round_seed_pages(): void
{
    $home_id = 0;
    foreach (yuzu_round_pages() as $slug => $page) {
        $existing = get_page_by_path($slug);
        if ($existing instanceof WP_Post) {
            if ($slug === 'home') {
                $home_id = (int) $existing->ID;
            }
            continue;
        }
        $id = wp_insert_post([
            'post_title' => $page['title'],
            'post_name' => $slug,
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '<!-- Yuzu Round locked template: ' . $page['file'] . ' -->',
        ], true);
        if (is_wp_error($id)) {
            continue;
        }
        if ($slug === 'home') {
            $home_id = (int) $id;
        }
    }
    if ($home_id > 0) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_id);
    }
}

function yuzu_round_file_for_request(): string
{
    if (function_exists('is_404') && is_404()) {
        return '404.html';
    }
    if (function_exists('is_front_page') && is_front_page()) {
        return 'index.html';
    }
    $queried = function_exists('get_queried_object') ? get_queried_object() : null;
    $slug = $queried instanceof WP_Post ? (string) $queried->post_name : '';
    $pages = yuzu_round_pages();
    return $pages[$slug]['file'] ?? 'index.html';
}

function yuzu_round_render(string $file): void
{
    $path = get_template_directory() . '/static/' . basename($file);
    if (!is_readable($path)) {
        if (function_exists('status_header')) {
            status_header(500);
        }
        echo 'Yuzu Round is missing static/' . esc_html(basename($file));
        return;
    }

    $html = file_get_contents($path);
    if (!is_string($html)) {
        if (function_exists('status_header')) {
            status_header(500);
        }
        echo 'Yuzu Round could not read this page.';
        return;
    }

    $base = trailingslashit(get_template_directory_uri() . '/static');
    $rewritten = preg_replace_callback(
        '/\s(href|src|action)="(?!https?:|mailto:|tel:|#|\/)([^"]+)"/',
        static function (array $match) use ($base): string {
            return ' ' . $match[1] . '="' . $base . $match[2] . '"';
        },
        $html
    );
    echo is_string($rewritten) ? $rewritten : $html;
}

add_filter('template_include', static function ($template) {
    $render = get_template_directory() . '/render.php';
    if (is_readable($render)) {
        return $render;
    }
    return is_string($template) ? $template : '';
});
