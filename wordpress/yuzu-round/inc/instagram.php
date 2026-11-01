<?php
/**
 * Photos on the Instagram landing page. Each <img data-yuzu-ig="…"> in
 * instagram.html is a slot; the salon picks a Media Library image per slot
 * under Salon details → Instagram photos. Empty slots keep the theme photo.
 */

function yuzu_round_ig_slots(): array
{
    $slots = [];
    for ($i = 1; $i <= 6; $i++) {
        $slots['grid-' . $i] = ['group' => 'Grid beside the intro (2 rows of 3)', 'label' => 'Grid photo ' . $i];
    }
    for ($i = 1; $i <= 4; $i++) {
        $slots['strip-' . $i] = ['group' => 'Strip after “Choose your stylist”', 'label' => 'Strip photo ' . $i];
    }
    for ($i = 5; $i <= 8; $i++) {
        $slots['strip-' . $i] = ['group' => 'Strip after the reviews', 'label' => 'Strip photo ' . ($i - 4)];
    }
    return $slots;
}

function yuzu_round_ig_current(): array
{
    $stored = function_exists('get_option') ? get_option('yuzu_round_instagram') : [];
    return yuzu_round_ig_sanitize(is_array($stored) ? $stored : []);
}

function yuzu_round_ig_sanitize(array $input): array
{
    $clean = [];
    foreach (array_keys(yuzu_round_ig_slots()) as $slot) {
        $id = (int) ($input[$slot] ?? 0);
        if ($id > 0 && (!function_exists('wp_attachment_is_image') || wp_attachment_is_image($id))) {
            $clean[$slot] = $id;
        }
    }
    return $clean;
}

/** Theme photo for each slot, read from the template. */
function yuzu_round_ig_defaults(): array
{
    $file = function_exists('yuzu_round_static_dir') ? yuzu_round_static_dir() . '/instagram.html' : '';
    $html = $file !== '' && is_readable($file) ? (string) file_get_contents($file) : '';
    $base = function_exists('yuzu_round_static_uri') ? rtrim(yuzu_round_static_uri(), '/') . '/' : '';
    $out = [];
    if (preg_match_all('/<img data-yuzu-ig="([a-z0-9-]+)" src="([^"]+)"/', $html, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
            $out[$match[1]] = $base . $match[2];
        }
    }
    return $out;
}

function yuzu_round_ig_apply(string $html, string $file, ?array $chosen = null): string
{
    if (basename($file) !== 'instagram.html' || !str_contains($html, 'data-yuzu-ig=')) {
        return $html;
    }
    $chosen = $chosen ?? yuzu_round_ig_current();
    if ($chosen === [] || !function_exists('wp_get_attachment_image_src')) {
        return $html;
    }
    return preg_replace_callback('/<img data-yuzu-ig="([a-z0-9-]+)"[^>]*>/', static function (array $m) use ($chosen): string {
        $id = $chosen[$m[1]] ?? 0;
        $src = $id > 0 ? wp_get_attachment_image_src($id, 'medium_large') : false;
        if (!is_array($src) || empty($src[0])) {
            return $m[0];
        }
        $alt = trim((string) get_post_meta($id, '_wp_attachment_image_alt', true));
        if ($alt === '' && preg_match('/\salt="([^"]*)"/', $m[0], $old) === 1) {
            $alt = html_entity_decode($old[1], ENT_QUOTES);
        }
        $loading = preg_match('/\sloading="([a-z]+)"/', $m[0], $l) === 1 ? $l[1] : 'lazy';
        $srcset = function_exists('wp_get_attachment_image_srcset') ? (string) wp_get_attachment_image_srcset($id, 'medium_large') : '';
        $tag = '<img data-yuzu-ig="' . esc_attr($m[1]) . '" src="' . esc_url($src[0]) . '"';
        if ($srcset !== '') {
            $sizes = str_starts_with($m[1], 'grid-') ? '(max-width: 900px) 33vw, 200px' : '(max-width: 900px) 25vw, 290px';
            $tag .= ' srcset="' . esc_attr($srcset) . '" sizes="' . $sizes . '"';
        }
        return $tag . ' alt="' . esc_attr($alt) . '" width="' . (int) $src[1] . '" height="' . (int) $src[2] . '" loading="' . esc_attr($loading) . '" decoding="async" />';
    }, $html) ?? $html;
}

/* ---------- wp-admin ---------- */

function yuzu_round_ig_admin_page(): void
{
    if (!current_user_can('edit_pages')) {
        wp_die(esc_html__('Sorry, you are not allowed to change these photos.', 'yuzu-round'), '', ['response' => 403]);
    }
    $chosen = yuzu_round_ig_current();
    $defaults = yuzu_round_ig_defaults();
    $saved = isset($_GET['yuzu_saved']) && $_GET['yuzu_saved'] === '1';

    echo '<div class="wrap yuzu-ig">';
    echo '<h1>Instagram photos</h1>';
    echo '<p class="yuzu-lead">Photos on the <a href="' . esc_url(home_url('/instagram/')) . '" target="_blank" rel="noopener">Instagram landing page</a>. Save a post’s photo from Instagram, then choose it here. Square photos work best; others are cropped to a square from the middle. Set the alt text in the Media Library to describe the hair, for example “Copper balayage with soft waves”.</p>';
    if ($saved) {
        echo '<div class="notice notice-success is-dismissible"><p>Photos saved. Open the page to check, and purge Varnish if you still see the old photos.</p></div>';
    }
    echo '<form method="post">';
    wp_nonce_field('yuzu_round_ig_save');
    echo '<input type="hidden" name="yuzu_ig_action" value="save" />';
    $group = '';
    foreach (yuzu_round_ig_slots() as $slot => $meta) {
        if ($meta['group'] !== $group) {
            if ($group !== '') {
                echo '</div>';
            }
            $group = $meta['group'];
            echo '<h2>' . esc_html($group) . '</h2><div class="yuzu-ig-grid">';
        }
        $id = $chosen[$slot] ?? 0;
        $thumb = $id > 0 ? wp_get_attachment_image_url($id, 'thumbnail') : '';
        $preview = $thumb !== '' && $thumb !== false ? $thumb : ($defaults[$slot] ?? '');
        echo '<div class="yuzu-ig-slot" data-default="' . esc_url($defaults[$slot] ?? '') . '">';
        echo '<img src="' . esc_url($preview) . '" alt="" />';
        echo '<strong>' . esc_html($meta['label']) . '</strong>';
        echo '<span class="yuzu-ig-state">' . ($id > 0 ? 'Your photo' : 'Theme photo') . '</span>';
        echo '<input type="hidden" name="yuzu_ig[' . esc_attr($slot) . ']" value="' . ($id > 0 ? (int) $id : '') . '" />';
        echo '<button type="button" class="button yuzu-ig-pick">Choose photo</button> ';
        echo '<button type="button" class="button-link yuzu-ig-reset">Use theme photo</button>';
        echo '</div>';
    }
    echo '</div>';
    submit_button('Save photos');
    echo '</form></div>';
}

function yuzu_round_ig_admin_script(): string
{
    return <<<'JS'
document.addEventListener('click', function (e) {
  var slot = e.target.closest('.yuzu-ig-slot');
  if (!slot) return;
  var input = slot.querySelector('input');
  var img = slot.querySelector('img');
  var state = slot.querySelector('.yuzu-ig-state');
  if (e.target.classList.contains('yuzu-ig-reset')) {
    input.value = '';
    img.src = slot.dataset.default;
    state.textContent = 'Theme photo';
    return;
  }
  if (!e.target.classList.contains('yuzu-ig-pick') || !window.wp || !wp.media) return;
  var frame = wp.media({ title: 'Choose a photo', button: { text: 'Use this photo' }, library: { type: 'image' }, multiple: false });
  frame.on('select', function () {
    var a = frame.state().get('selection').first().toJSON();
    input.value = a.id;
    img.src = (a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url);
    state.textContent = 'Your photo';
  });
  frame.open();
});
JS;
}

function yuzu_round_ig_handle_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || ($_POST['yuzu_ig_action'] ?? '') !== 'save') {
        return;
    }
    if (!current_user_can('edit_pages')) {
        return;
    }
    check_admin_referer('yuzu_round_ig_save');
    $raw = wp_unslash($_POST['yuzu_ig'] ?? []);
    update_option('yuzu_round_instagram', yuzu_round_ig_sanitize(is_array($raw) ? $raw : []), false);
    if (function_exists('yuzu_round_mark_edited')) {
        yuzu_round_mark_edited(['instagram']);
    }
    if (function_exists('yuzu_round_prices_purge_cache')) {
        yuzu_round_prices_purge_cache();
    }
    wp_safe_redirect(add_query_arg(['yuzu_saved' => '1'], admin_url('admin.php?page=yuzu-instagram')));
    exit;
}

if (function_exists('add_action')) {
    add_action('admin_menu', static function (): void {
        $hook = add_submenu_page(
            'yuzu-salon',
            'Instagram photos',
            'Instagram photos',
            'edit_pages',
            'yuzu-instagram',
            'yuzu_round_ig_admin_page'
        );
        if (!is_string($hook) || $hook === '') {
            return;
        }
        add_action('load-' . $hook, 'yuzu_round_ig_handle_post');
        add_action('admin_enqueue_scripts', static function (string $current) use ($hook): void {
            if ($current !== $hook) {
                return;
            }
            wp_enqueue_media();
            wp_add_inline_script('media-editor', yuzu_round_ig_admin_script());
            wp_register_style('yuzu-ig-admin', false);
            wp_enqueue_style('yuzu-ig-admin');
            wp_add_inline_style('yuzu-ig-admin', '
              .yuzu-ig .yuzu-lead { max-width: 760px; }
              .yuzu-ig-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 16px; max-width: 1080px; }
              .yuzu-ig-slot { display: flex; flex-direction: column; gap: 6px; align-items: flex-start; background: #fff; border: 1px solid #c3c4c7; padding: 10px; }
              .yuzu-ig-slot img { width: 100%; aspect-ratio: 1; object-fit: cover; background: #f0f0f1; }
              .yuzu-ig-state { color: #646970; font-size: 12px; }
            ');
        });
    }, 20);
}
