<?php
/**
 * Marketing deep-link:
 * - Logs Supabase enquiry_activity from public.leads (email, website)
 * - Sends email OTP via mail-server (/api/send-otp)
 * - Redirects to otp_verify.php (session-bound; no CRM login yet)
 *
 * URL: welcome.php?id={lead_uuid}
 */
declare(strict_types=1);

require_once __DIR__ . '/config/otp_mail_server.php';

// ===============================
// CONFIG
// ===============================
$SUPABASE_URL = 'https://vwfaxqqxhtmujonkskuk.supabase.co';
$SUPABASE_SERVICE_ROLE_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InZ3ZmF4cXF4aHRtdWpvbmtza3VrIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3NTEyNzkxNiwiZXhwIjoyMDkwNzAzOTE2fQ.sXCs2fQ7G0YDNxeFOhs4zb7mq6Sdz6pTp0JGmz-JNcg';

$CRM_VISIT_LOG = __DIR__ . '/crm_visit.logs';
$OTP_ERROR_LOG = __DIR__ . '/otp_error_logs.logs';

$REDIRECT_AFTER_OTP_SENT = 'otp_verify.php';

$activity_type = 'crm_visited';

// ===============================
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// ===============================
/** Append JSON line (sets logged_at unless already set). */
function welcome_log_append(string $file, array $payload): void
{
    if (!isset($payload['logged_at'])) {
        $payload['logged_at'] = date('Y-m-d H:i:s');
    }
    file_put_contents($file, json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);
}

/** @return array{body:string, http:int, curlErr:string} */
function supabase_curl(string $method, string $url, string $apiKey, $bodyJson = null): array
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    $headers = [
        'apikey: ' . $apiKey,
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
    ];
    if ($method === 'POST') {
        $headers[] = 'Prefer: return=representation';
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($bodyJson) ? $bodyJson : json_encode($bodyJson));
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $body = curl_exec($ch);
    $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    return ['body' => $body ?: '', 'http' => $http, 'curlErr' => $curlErr];
}

/** @return array{id?:string, email?:string|null, website?:string|null, name?:string|null}|null */
function supabase_fetch_lead_by_id(string $baseUrl, string $key, string $uuid): ?array
{
    $url = rtrim($baseUrl, '/') . '/rest/v1/leads?id=eq.' . rawurlencode($uuid) . '&select=id,email,website,name';
    $r = supabase_curl('GET', $url, $key);

    if ($r['curlErr'] !== '' || ($r['http'] !== 200 && $r['http'] !== 206)) {
        return null;
    }
    $rows = json_decode($r['body'], true);
    if (!is_array($rows) || $rows === []) {
        return null;
    }

    return is_array($rows[0] ?? null) ? $rows[0] : null;
}

/** @param array<string, mixed> $activityRow */
function supabase_insert_enquiry_activity(string $baseUrl, string $key, array $activityRow): array
{
    $url = rtrim($baseUrl, '/') . '/rest/v1/enquiry_activity';
    return supabase_curl('POST', $url, $key, $activityRow);
}

// ===============================
// INPUT
// ===============================
$leadUuid = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
$uuidOk = $leadUuid !== '' && preg_match(
    '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
    $leadUuid
);

if (!$uuidOk) {
    welcome_log_append($CRM_VISIT_LOG, [
        'step'               => 'error_validation',
        'userid'             => $leadUuid,
        'name'               => '',
        'email'              => '',
        'time'               => date('Y-m-d H:i:s'),
        'otp_requested_at'   => null,
        'message'            => 'missing or invalid id',
    ]);
    http_response_code(400);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'message' => 'id query parameter must be a valid lead UUID']);
    exit;
}

$leadRow = supabase_fetch_lead_by_id($SUPABASE_URL, $SUPABASE_SERVICE_ROLE_KEY, $leadUuid);

if ($leadRow === null) {
    welcome_log_append($CRM_VISIT_LOG, [
        'step'               => 'error_lead_not_found',
        'userid'             => $leadUuid,
        'name'               => '',
        'email'              => '',
        'time'               => date('Y-m-d H:i:s'),
        'otp_requested_at'   => null,
    ]);
    http_response_code(404);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'message' => 'Lead not found']);
    exit;
}

$email = isset($leadRow['email']) && $leadRow['email'] !== null
    ? trim((string) $leadRow['email'])
    : '';
$website_url = isset($leadRow['website']) && $leadRow['website'] !== null
    ? trim((string) $leadRow['website'])
    : '';
$leadName = isset($leadRow['name']) && $leadRow['name'] !== null
    ? trim((string) $leadRow['name'])
    : '';

$supActivity = supabase_insert_enquiry_activity($SUPABASE_URL, $SUPABASE_SERVICE_ROLE_KEY, [
    'email'         => $email,
    'website_url'   => $website_url,
    'activity_type' => $activity_type,
]);

$otpRequestedAt = date('Y-m-d H:i:s');
$otpSend = otp_mail_server_send_otp($leadUuid);
$otpData = $otpSend['data'] ?? [];
$otpErrorMsg = is_array($otpData)
    ? (string) ($otpData['message'] ?? $otpData['error'] ?? '')
    : '';
if ($otpErrorMsg === '') {
    $otpErrorMsg = $otpSend['curlErr'] !== '' ? $otpSend['curlErr'] : 'Could not send verification code.';
}

$crmVisitPayload = [
    'step'             => $otpSend['ok'] ? 'otp_sent' : 'otp_send_failed',
    'userid'           => $leadUuid,
    'name'             => $leadName,
    'email'            => $email,
    'website_url'      => $website_url,
    'time'             => $otpRequestedAt,
    'otp_requested_at' => $otpRequestedAt,
    'otp_send_success' => $otpSend['ok'],
    'enquiry_http'     => $supActivity['http'],
];

if (!$otpSend['ok']) {
    $crmVisitPayload['otp_send_http'] = $otpSend['http'];
    welcome_log_append($OTP_ERROR_LOG, [
        'step'               => 'error_send_otp',
        'userid'             => $leadUuid,
        'name'               => $leadName,
        'email'              => $email,
        'website_url'        => $website_url,
        'otp_requested_at'   => $otpRequestedAt,
        'message'            => $otpErrorMsg,
        'enquiry_http'       => $supActivity['http'],
        'enquiry_response'   => json_decode($supActivity['body'], true),
        'send_otp_http'      => $otpSend['http'],
        'send_otp_curl_error'=> $otpSend['curlErr'],
        'send_otp_body'      => $otpData,
    ]);
}

welcome_log_append($CRM_VISIT_LOG, $crmVisitPayload);

if (!$otpSend['ok']) {
    $_SESSION['limo_otp_flash_error'] = $otpErrorMsg;
    header('Location: otp_verify.php?send_failed=1&id=' . rawurlencode($leadUuid));
    exit;
}

$_SESSION['limo_otp_gate'] = [
    'lead_id'    => $leadUuid,
    'lead_name'  => $leadName,
    'email'      => $otpData['email'] ?? $email,
    'expires_at' => $otpData['expires_at'] ?? null,
    'verify_url' => $otpData['verify_url'] ?? null,
    'sent_at'    => time(),
];

header('Location: ' . $REDIRECT_AFTER_OTP_SENT);
exit;
