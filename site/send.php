<?php
declare(strict_types=1);

header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo 'Method not allowed';
    exit;
}

$to = 'info@yuzuhairandbeauty.co.uk';
$from = 'website@yuzu.hair';
$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$role = trim((string) ($_POST['role'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
$subject = trim((string) ($_POST['subject'] ?? 'Message from yuzu.hair'));

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Please include a valid email address.';
    exit;
}

$subject = preg_replace('/[\r\n]+/', ' ', $subject) ?: 'Message from yuzu.hair';

$body = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n";
if ($role !== '') {
    $body .= "Role: {$role}\n";
}
$body .= "\n{$message}\n";

$headers = [
    'From: Yuzu Hair website <' . $from . '>',
    'Reply-To: ' . $email,
    'Content-Type: text/plain; charset=UTF-8',
];

$sent = @mail($to, $subject, $body, implode("\r\n", $headers));

if (str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')) {
    header('Content-Type: application/json; charset=UTF-8');
    if (!$sent) {
        http_response_code(500);
        echo json_encode(['ok' => false]);
        exit;
    }
    echo json_encode(['ok' => true]);
    exit;
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.html') . '#visit');
exit;
