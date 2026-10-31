<?php
declare(strict_types=1);

header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo 'Method not allowed';
    exit;
}

$wantsJson = str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');

$fail = static function (int $status, string $message) use ($wantsJson): void {
    http_response_code($status);
    if ($wantsJson) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => false, 'error' => $message]);
        exit;
    }
    echo $message;
    exit;
};

$to = 'info@yuzuhairandbeauty.co.uk';
$from = 'website@yuzu.hair';
$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$role = trim((string) ($_POST['role'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
$subject = trim((string) ($_POST['subject'] ?? 'Message from yuzu.hair'));

$trap = trim((string) ($_POST['website'] ?? ''));
$stamp = (int) ($_POST['form_ts'] ?? 0);
if ($trap !== '' || ($stamp > 0 && time() - $stamp < 3)) {
    if ($wantsJson) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => true]);
    }
    exit;
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $fail(400, 'Please include a valid email address.');
}

$subject = preg_replace('/[\r\n]+/', ' ', $subject) ?: 'Message from yuzu.hair';

$contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
$contentType = (string) ($_SERVER['CONTENT_TYPE'] ?? '');
if (
    $contentLength > 0
    && stripos($contentType, 'multipart/form-data') !== false
    && $_POST === []
    && $_FILES === []
) {
    $fail(400, 'That file is too large for the server. Please use a PDF, Word or text file under 5 MB.');
}

$attachment = null;
$file = $_FILES['cv'] ?? null;
if (is_array($file) && (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    $error = (int) $file['error'];
    if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
        $fail(400, 'That file is too large. Please use a PDF, Word or text file under 5 MB.');
    }
    if ($error !== UPLOAD_ERR_OK || !is_uploaded_file((string) $file['tmp_name'])) {
        $fail(400, 'The CV could not be uploaded. Please try again.');
    }
    if ((int) $file['size'] > 5 * 1024 * 1024) {
        $fail(400, 'That file is too large. Please use a PDF, Word or text file under 5 MB.');
    }

    $original = (string) ($file['name'] ?? 'cv');
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $allowed = [
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword', 'application/vnd.ms-office', 'application/octet-stream'],
        'docx' => [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
            'application/octet-stream',
        ],
        'txt' => ['text/plain', 'text/x-plain', 'application/octet-stream'],
        'rtf' => ['text/rtf', 'application/rtf', 'application/octet-stream'],
    ];
    if (!isset($allowed[$ext])) {
        $fail(400, 'Please attach a PDF, Word or text file.');
    }

    $detected = '';
    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $detected = (string) $finfo->file((string) $file['tmp_name']);
    }
    $blocked = ['text/html', 'application/x-php', 'application/x-httpd-php', 'image/svg+xml', 'application/javascript', 'text/x-php'];
    if ($detected !== '' && (in_array($detected, $blocked, true) || !in_array($detected, $allowed[$ext], true))) {
        $fail(400, 'Please attach a PDF, Word or text file.');
    }

    $bytes = file_get_contents((string) $file['tmp_name']);
    if ($bytes === false || $bytes === '') {
        $fail(400, 'The CV could not be read. Please try again.');
    }
    if (preg_match('/<\?(php|=)/i', substr($bytes, 0, 200)) === 1) {
        $fail(400, 'Please attach a PDF, Word or text file.');
    }

    $safeName = preg_replace('/[^A-Za-z0-9._-]+/', '-', $original) ?: ('cv.' . $ext);
    $safeName = trim($safeName, '.-');
    if (!str_ends_with(strtolower($safeName), '.' . $ext)) {
        $safeName .= '.' . $ext;
    }

    $attachment = [
        'name' => $safeName,
        'mime' => $detected !== '' ? $detected : $allowed[$ext][0],
        'bytes' => $bytes,
    ];
}

$body = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n";
if ($role !== '') {
    $body .= "Role: {$role}\n";
}
if ($attachment !== null) {
    $body .= "CV: {$attachment['name']}\n";
}
$body .= "\n{$message}\n";

$headers = [
    'From: YUZU hair website <' . $from . '>',
    'Reply-To: ' . $email,
    'MIME-Version: 1.0',
];

if ($attachment === null) {
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $payload = $body;
} else {
    $boundary = 'yuzu_' . bin2hex(random_bytes(8));
    $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';
    $payload = "--{$boundary}\r\n";
    $payload .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $payload .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $payload .= $body . "\r\n";
    $payload .= "--{$boundary}\r\n";
    $payload .= 'Content-Type: ' . $attachment['mime'] . '; name="' . $attachment['name'] . "\"\r\n";
    $payload .= "Content-Transfer-Encoding: base64\r\n";
    $payload .= 'Content-Disposition: attachment; filename="' . $attachment['name'] . "\"\r\n\r\n";
    $payload .= chunk_split(base64_encode($attachment['bytes']));
    $payload .= "--{$boundary}--\r\n";
}

$sent = @mail($to, $subject, $payload, implode("\r\n", $headers));

if ($wantsJson) {
    header('Content-Type: application/json; charset=UTF-8');
    if (!$sent) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'The message could not be sent. Please email info@yuzuhairandbeauty.co.uk.']);
        exit;
    }
    echo json_encode(['ok' => true]);
    exit;
}

$back = 'index.html';
$referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');
$refererHost = (string) parse_url($referer, PHP_URL_HOST);
if ($referer !== '' && $refererHost !== '' && strcasecmp($refererHost, (string) ($_SERVER['HTTP_HOST'] ?? '')) === 0) {
    $back = strtok($referer, '#');
}
header('Location: ' . $back . '#visit');
exit;
