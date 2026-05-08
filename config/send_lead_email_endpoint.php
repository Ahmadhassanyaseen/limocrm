<?php
include 'api.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$leadId    = trim((string)($_POST['lead_id']    ?? ''));
$emailType = trim((string)($_POST['email_type'] ?? ''));

if ($leadId === '' || $emailType === '') {
    echo json_encode(['success' => false, 'message' => 'Missing lead_id or email_type']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Map UI email types -> backend dispatcher.
//   formal_quote -> send_formal_quote_email($id)
//   agreement    -> send_agreement_email($id)
$dispatch = [
    'formal_quote' => [
        'fn'    => 'sendFormalQuoteEmail',
        'label' => 'Formal Quote',
    ],
    'agreement' => [
        'fn'    => 'sendAgreementEmail',
        'label' => 'Agreement',
    ],
];

if (!isset($dispatch[$emailType])) {
    echo json_encode(['success' => false, 'message' => 'Unsupported email_type']);
    exit;
}

$handler = $dispatch[$emailType];
$label   = $handler['label'];

// The backend functions only need `id`. user_id is passed in case the
// backend resolves a per-user template via session context.
$payload = [
    'id'               => $leadId,
    'user_id'          => $_SESSION['user_id'] ?? '',
    'assigned_user_id' => $_SESSION['user_id'] ?? '',
];

$response = call_user_func($handler['fn'], $payload);

// Normalize the various shapes the SuiteCRM endpoint can return into
// the { success: bool, message: string } contract the frontend expects.
if (is_array($response) && isset($response['success'])) {
    echo json_encode($response);
    exit;
}

if (is_array($response) && !empty($response)) {
    echo json_encode([
        'success' => true,
        'message' => $label . ' email sent successfully',
        'data'    => $response,
    ]);
    exit;
}

if ($response === true || $response === 1 || $response === '1' || (is_string($response) && strcasecmp($response, 'true') === 0)) {
    echo json_encode([
        'success' => true,
        'message' => $label . ' email sent successfully',
    ]);
    exit;
}

if (is_string($response) && $response !== '' && strcasecmp($response, 'false') !== 0) {
    // Some endpoints return a status / message string. Treat as success unless
    // it's literally 'false' / empty.
    echo json_encode([
        'success' => true,
        'message' => $response,
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Could not send the ' . $label . ' email. Please verify the workflow / email template is configured.',
    'raw'     => $response,
]);
