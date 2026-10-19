<?php
if (function_exists('yuzu_round_render') && function_exists('yuzu_round_file_for_request')) {
    try {
        yuzu_round_render(yuzu_round_file_for_request());
    } catch (Throwable $e) {
        status_header(500);
        echo 'Yuzu Round could not render this page.';
    }
    return;
}

status_header(500);
echo 'Yuzu Round could not start.';
