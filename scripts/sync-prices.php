<?php
/**
 * Rewrites the price panels baked into site/prices.html and the service pages
 * from the default price list in wordpress/yuzu-round/inc/prices.php, so the
 * static files match what WordPress renders. Run after changing the defaults
 * or the panel markup: php scripts/sync-prices.php
 */

$root = dirname(__DIR__);
require $root . '/wordpress/yuzu-round/inc/prices.php';

$data = yuzu_round_prices_sanitize(yuzu_round_prices_default());
if ($data === null) {
    fwrite(STDERR, "Default prices did not validate.\n");
    exit(1);
}

$files = ['prices.html'];
foreach (yuzu_round_prices_service_pages() as $page) {
    $files[] = $page['file'];
}
foreach ($files as $file) {
    $path = $root . '/site/' . $file;
    if (!is_file($path)) {
        continue;
    }
    $html = (string) file_get_contents($path);
    $next = $file === 'prices.html'
        ? yuzu_round_prices_replace_blend_board($html, $data, 'patch.html', true)
        : yuzu_round_prices_replace_service_panel($html, $data);
    if ($next !== $html) {
        file_put_contents($path, $next);
        echo "updated site/{$file}\n";
    }
}
