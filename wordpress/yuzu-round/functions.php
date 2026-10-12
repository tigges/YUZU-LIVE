<?php
declare(strict_types=1);

add_action('after_setup_theme', static function (): void {
    add_theme_support('title-tag');
});

add_action('after_switch_theme', 'yuzu_round_seed_pages');

function yuzu_round_pages(): array
{
    return [
        'home' => ['title' => 'Home', 'file' => 'index.html'],
        'prices' => ['title' => 'Prices', 'file' => 'prices.html'],
        'offers' => ['title' => 'Offers', 'file' => 'offers.html'],
        'team' => ['title' => 'Join our team', 'file' => 'team.html'],
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
        $existing = get_page_by_path($slug === 'home' ? 'home' : $slug);
        if ($existing instanceof WP_Post) {
            if ($slug === 'home') {
                $home_id = $existing->ID;
            }
            continue;
        }
        $id = wp_insert_post([
            'post_title' => $page['title'],
            'post_name' => $slug,
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '<!-- Yuzu Round locked template: ' . $page['file'] . ' -->',
        ]);
        if ($slug === 'home' && is_int($id)) {
            $home_id = $id;
        }
    }
    if ($home_id) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_id);
    }
}

function yuzu_round_file_for_request(): string
{
    if (is_404()) {
        return '404.html';
    }
    if (is_front_page()) {
        return 'index.html';
    }
    $slug = get_queried_object() instanceof WP_Post ? get_queried_object()->post_name : '';
    $pages = yuzu_round_pages();
    return $pages[$slug]['file'] ?? 'index.html';
}

function yuzu_round_render(string $file): void
{
    $path = get_template_directory() . '/static/' . $file;
    if (!is_readable($path)) {
        status_header(500);
        echo 'Yuzu Round is missing static/' . esc_html($file);
        exit;
    }

    $html = (string) file_get_contents($path);
    $base = trailingslashit(get_template_directory_uri() . '/static');
    $html = preg_replace_callback(
        '/\s(href|src|action)="(?!https?:|mailto:|tel:|#|\/)([^"]+)"/',
        static function (array $match) use ($base): string {
            return ' ' . $match[1] . '="' . $base . $match[2] . '"';
        },
        $html
    ) ?? $html;

    echo $html;
    exit;
}

add_filter('template_include', static function (string $template): string {
    yuzu_round_render(yuzu_round_file_for_request());
    return $template;
});
