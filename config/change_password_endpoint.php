<?php
include 'api.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId  = trim((string)($_POST['id'] ?? ($_SESSION['user']['id'] ?? '')));
$current = (string)($_POST['current_password'] ?? '');
$next    = (string)($_POST['new_password']     ?? '');

if ($userId === '' || $current === '' || $next === '') {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

if (strlen($next) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}

// The remote SuiteCRM update_user action is responsible for verifying
// current_password against the stored hash before applying new_password.
$resp = update_user([
    'id'               => $userId,
    'current_password' => $current,
    'new_password'     => $next,
]);

if (is_array($resp) && (
        ($resp['success'] ?? false) === true
        || ($resp['status'] ?? '') === 'success'
    )) {
    echo json_encode([
        'success' => true,
        'message' => $resp['message'] ?? 'Password updated successfully',
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => is_array($resp) && !empty($resp['message'])
        ? $resp['message']
        : 'Could not change password. Please verify your current password and try again.',
]);
