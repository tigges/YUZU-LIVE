<?php
if (!function_exists('yuzu_round_render') || !function_exists('yuzu_round_file_for_request')) {
    return;
}

try {
    yuzu_round_render(yuzu_round_file_for_request());
} catch (Throwable $e) {
    if (function_exists('status_header')) {
        status_header(500);
    }
    echo 'Yuzu Round could not render this page.';
}
