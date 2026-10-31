<?php
if (defined('YUZU_ROUND_LOADED')) {
    return;
}
define('YUZU_ROUND_LOADED', true);

if (function_exists('add_action') && is_readable(__DIR__ . '/inc/prices.php')) {
    require_once __DIR__ . '/inc/prices.php';
}
if (function_exists('add_action') && is_readable(__DIR__ . '/inc/copy.php')) {
    require_once __DIR__ . '/inc/copy.php';
}
if (is_readable(__DIR__ . '/inc/seo.php')) {
    require_once __DIR__ . '/inc/seo.php';
}
if (is_readable(__DIR__ . '/inc/reviews.php')) {
    require_once __DIR__ . '/inc/reviews.php';
}

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

function yuzu_round_locked_post($post): bool
{
    return $post instanceof WP_Post
        && $post->post_type === 'page'
        && isset(yuzu_round_pages()[$post->post_name]);
}

function yuzu_round_preview_meta_box(WP_Post $post): void
{
    $pages = yuzu_round_pages();
    $slug = (string) $post->post_name;
    $view = get_permalink($post);
    echo '<p>Visitors see the designed page below, not a blank editor. Leave the slug as <code>' . esc_html($slug) . '</code>.</p>';
    if ($slug === 'prices' && current_user_can('edit_pages')) {
        echo '<p>Prices are changed on the <a href="' . esc_url(admin_url('admin.php?page=yuzu-prices')) . '">Price list</a> screen.</p>';
    }
    if (current_user_can('edit_pages') && function_exists('yuzu_round_copy_catalog') && isset(yuzu_round_copy_catalog()[$slug])) {
        $copy_label = (string) yuzu_round_copy_catalog()[$slug]['menu'];
        echo '<p>Change the words under <a href="' . esc_url(yuzu_round_copy_admin_url($slug)) . '">Page copy → ' . esc_html($copy_label) . '</a>.</p>';
    }
    if (is_string($view) && $view !== '') {
        echo '<p><a class="button button-primary" href="' . esc_url($view) . '" target="_blank" rel="noopener">Open this page</a></p>';
        echo '<iframe class="yuzu-round-preview" title="' . esc_attr($pages[$slug]['title'] ?? 'Page preview') . '" src="' . esc_url($view) . '"></iframe>';
    }
}

add_filter('use_block_editor_for_post', static function ($use, $post) {
    if (yuzu_round_locked_post($post)) {
        return false;
    }
    return $use;
}, 10, 2);

add_filter('replace_editor', static function ($replace, $post) {
    if (yuzu_round_locked_post($post)) {
        remove_post_type_support('page', 'editor');
    }
    return $replace;
}, 10, 2);

add_action('add_meta_boxes_page', static function ($post): void {
    if (!yuzu_round_locked_post($post)) {
        return;
    }
    add_meta_box(
        'yuzu-round-preview',
        'This page on the site',
        'yuzu_round_preview_meta_box',
        'page',
        'normal',
        'high'
    );
});

add_action('admin_head', static function (): void {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->base !== 'post' || $screen->post_type !== 'page') {
        return;
    }
    echo '<style>.yuzu-round-preview{width:100%;height:75vh;min-height:640px;border:1px solid #c3c4c7;background:#fff}</style>';
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
    if (function_exists('yuzu_round_is_salon_front') && yuzu_round_is_salon_front()) {
        return 'index.html';
    }
    if (function_exists('yuzu_round_locked_file')) {
        $locked = yuzu_round_locked_file();
        if (is_string($locked)) {
            return $locked;
        }
    }
    if (function_exists('is_404') && is_404()) {
        return '404.html';
    }
    if (function_exists('yuzu_round_force_404')) {
        yuzu_round_force_404();
    }
    return '404.html';
}

function yuzu_round_static_dir(): string
{
    $dir = get_template_directory();
    if (is_dir($dir . '/static')) {
        return $dir . '/static';
    }
    if (is_dir($dir . '/wordpress/yuzu-round/static')) {
        return $dir . '/wordpress/yuzu-round/static';
    }
    return $dir . '/site';
}

function yuzu_round_static_uri(): string
{
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    if (is_dir($dir . '/static')) {
        return $uri . '/static';
    }
    if (is_dir($dir . '/wordpress/yuzu-round/static')) {
        return $uri . '/wordpress/yuzu-round/static';
    }
    return $uri . '/site';
}

function yuzu_round_render(string $file): void
{
    $path = yuzu_round_static_dir() . '/' . basename($file);
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

    if (function_exists('yuzu_round_copy_apply')) {
        try {
            $copied = yuzu_round_copy_apply($html, basename($file));
            if (is_string($copied) && $copied !== '') {
                $html = $copied;
            }
        } catch (Throwable $e) {
            // A bad page-copy record must not take the page down.
        }
    }

    if (function_exists('yuzu_round_prices_apply')) {
        try {
            $applied = yuzu_round_prices_apply($html, basename($file));
            if (is_string($applied) && $applied !== '') {
                $html = $applied;
            }
        } catch (Throwable $e) {
            // A bad price record must not take the page down.
        }
    }

    $base = trailingslashit(yuzu_round_static_uri());
    if (function_exists('yuzu_round_rewrite_public_urls')) {
        $html = yuzu_round_rewrite_public_urls($html, $base);
    }
    echo $html;
}

add_filter('template_include', static function ($template) {
    $render = get_template_directory() . '/render.php';
    if (is_readable($render)) {
        return $render;
    }
    return is_string($template) ? $template : '';
});
