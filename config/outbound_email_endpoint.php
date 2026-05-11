<?php
/**
 * AJAX API for Email Settings (outbound accounts). Calls SuiteCRM CustomEntryPoint.
 */
include __DIR__ . '/api.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = trim((string)($_SESSION['user']['id'] ?? ''));
if ($userId === '') {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$op = trim((string)($_POST['op'] ?? ''));
$payload = array_merge($_POST, ['user_id' => $userId]);

switch ($op) {
    case 'list':
        echo json_encode(fetchOutboundEmailAccounts($payload));
        exit;

    case 'detail':
        echo json_encode(fetchOutboundEmailAccountDetail($payload));
        exit;

    case 'save':
        echo json_encode(saveOutboundEmailAccount($payload));
        exit;

    case 'delete':
        echo json_encode(deleteOutboundEmailAccount($payload));
        exit;

    case 'test':
        echo json_encode(testOutboundEmailAccountConnection($payload));
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown operation']);
        exit;
}
