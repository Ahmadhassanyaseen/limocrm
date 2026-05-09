<?php
/**
 * Logged-in CRM user: returns public agreement URL for a lead they own.
 */
include __DIR__ . '/api.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$leadId = trim((string)($_POST['lead_id'] ?? ''));
$userId = trim((string)($_SESSION['user']['id'] ?? ''));

if ($leadId === '' || $userId === '') {
    echo json_encode(['success' => false, 'message' => 'Not signed in or missing lead']);
    exit;
}

$res = getAgreementSigningLink([
    'lead_id' => $leadId,
    'user_id' => $userId,
]);
echo json_encode($res);
