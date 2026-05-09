<?php
/**
 * Public AJAX bridge for standalone agreement.php (no session).
 */
include __DIR__ . '/api.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$op = isset($_POST['op']) ? (string)$_POST['op'] : '';

if ($op === 'fetch') {
    $res = fetchAgreementLeadData([
        'lead_id' => $_POST['lead_id'] ?? '',
        'token'   => $_POST['token'] ?? '',
    ]);
    echo json_encode($res);
    exit;
}

if ($op === 'submit') {
    $res = submitAgreementPayment([
        'lead_id'           => $_POST['lead_id'] ?? '',
        'token'             => $_POST['token'] ?? '',
        'payment_method_id'=> $_POST['payment_method_id'] ?? '',
        'signature_png'     => $_POST['signature_png'] ?? '',
    ]);
    echo json_encode($res);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid operation']);
