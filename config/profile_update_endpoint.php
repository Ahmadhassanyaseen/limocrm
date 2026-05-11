<?php
include __DIR__ . '/api.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sessionId = trim((string)($_SESSION['user']['id'] ?? ''));
if ($sessionId === '') {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$postedId = trim((string)($_POST['id'] ?? ''));
if ($postedId !== '' && $postedId !== $sessionId) {
    echo json_encode(['success' => false, 'message' => 'You can only update your own profile']);
    exit;
}

$first = trim((string)($_POST['first_name'] ?? ''));
$last  = trim((string)($_POST['last_name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$userName = trim((string)($_POST['user_name'] ?? ''));

if ($first === '' || $last === '') {
    echo json_encode(['success' => false, 'message' => 'First and last name are required']);
    exit;
}

$len = static function (string $s): int {
    return function_exists('mb_strlen') ? (int) mb_strlen($s, 'UTF-8') : strlen($s);
};
if ($len($first) < 1 || $len($first) > 100 || !preg_match('/^[\p{L}\p{M}\s.\'\-]+$/u', $first)) {
    echo json_encode(['success' => false, 'message' => 'First name contains invalid characters (use letters and common punctuation only).']);
    exit;
}
if ($len($last) < 1 || $len($last) > 100 || !preg_match('/^[\p{L}\p{M}\s.\'\-]+$/u', $last)) {
    echo json_encode(['success' => false, 'message' => 'Last name contains invalid characters (use letters and common punctuation only).']);
    exit;
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'A valid email is required']);
    exit;
}
if (strlen($email) > 254) {
    echo json_encode(['success' => false, 'message' => 'Email address is too long']);
    exit;
}

if ($phone !== '') {
    if (strlen($phone) > 32) {
        echo json_encode(['success' => false, 'message' => 'Phone number is too long']);
        exit;
    }
    $phoneBase = preg_replace('/\s*(?:ext\.?|extension)\s*\d+/i', '', $phone);
    $phoneBase = trim($phoneBase);
    if (!preg_match('/^[\d\s()+\-.]+$/', $phoneBase)) {
        echo json_encode(['success' => false, 'message' => 'Phone may only contain digits, spaces, and + ( ) . -']);
        exit;
    }
    $digits = preg_replace('/\D/', '', $phoneBase);
    $digitLen = strlen($digits);
    if ($digitLen < 7 || $digitLen > 15) {
        echo json_encode(['success' => false, 'message' => 'Phone must contain between 7 and 15 digits, or leave it blank']);
        exit;
    }
}

if ($userName === '') {
    echo json_encode(['success' => false, 'message' => 'Username is required']);
    exit;
}
if (!preg_match('/^[a-zA-Z0-9._@-]{3,64}$/', $userName)) {
    echo json_encode(['success' => false, 'message' => 'Username must be 3–64 characters: letters, numbers, and . _ @ - only']);
    exit;
}

$resp = update_user([
    'id'         => $sessionId,
    'first_name' => $first,
    'last_name'  => $last,
    'email'      => $email,
    'user_name'  => $userName,
    'phone'      => $phone,
]);

$ok = is_array($resp) && (
    (($resp['success'] ?? false) === true)
    || (($resp['status'] ?? '') === 'success')
);

if ($ok) {
    $_SESSION['user']['first_name'] = $first;
    $_SESSION['user']['last_name'] = $last;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['user_name'] = $userName;
    $_SESSION['user']['phone_work'] = $phone;

    echo json_encode([
        'success' => true,
        'message' => $resp['message'] ?? 'Profile saved',
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => is_array($resp) && !empty($resp['message'])
        ? $resp['message']
        : 'Could not save profile.',
]);
