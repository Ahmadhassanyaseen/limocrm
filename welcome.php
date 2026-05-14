<?php

// ===============================
// CONFIG
// ===============================
$SUPABASE_URL = "https://vwfaxqqxhtmujonkskuk.supabase.co";
$SUPABASE_SERVICE_ROLE_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InZ3ZmF4cXF4aHRtdWpvbmtza3VrIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3NTEyNzkxNiwiZXhwIjoyMDkwNzAzOTE2fQ.sXCs2fQ7G0YDNxeFOhs4zb7mq6Sdz6pTp0JGmz-JNcg";

// Log file
$LOG_FILE = __DIR__ . "/crm_visit.logs";

// Redirect page
$REDIRECT_PAGE = "login.php";

// ===============================
// GET PARAMS
// ===============================
$email = $_GET['email'] ?? '';
$website_url = $_GET['website_url'] ?? '';
$activity_type = 'crm_visited';

// ===============================
// VALIDATION
// ===============================
if (empty($email) || empty($website_url)) {

    $errorLog = "[" . date("Y-m-d H:i:s") . "] ERROR: Missing required params | email={$email} | website_url={$website_url}\n";

    file_put_contents($LOG_FILE, $errorLog, FILE_APPEND);

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "email and website_url are required"
    ]);

    exit;
}

// ===============================
// SUPABASE ENDPOINT
// ===============================
$endpoint = $SUPABASE_URL . "/rest/v1/enquiry_activity";

// ===============================
// DATA TO INSERT
// ===============================
$data = [
    "email" => $email,
    "website_url" => $website_url,
    "activity_type" => $activity_type
];

// ===============================
// CURL REQUEST
// ===============================
$ch = curl_init($endpoint);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "apikey: $SUPABASE_SERVICE_ROLE_KEY",
    "Authorization: Bearer $SUPABASE_SERVICE_ROLE_KEY",
    "Content-Type: application/json",
    "Prefer: return=representation"
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

// ===============================
// SAVE LOG
// ===============================
$logData = [
    "time" => date("Y-m-d H:i:s"),
    "email" => $email,
    "website_url" => $website_url,
    "activity_type" => $activity_type,
    "status_code" => $httpCode,
    "curl_error" => $curlError,
    "response" => json_decode($response, true)
];

$logLine = json_encode($logData, JSON_UNESCAPED_SLASHES) . PHP_EOL;

file_put_contents($LOG_FILE, $logLine, FILE_APPEND);

// ===============================
// REDIRECT TO LOGIN
// ===============================
header("Location: $REDIRECT_PAGE");
exit;

?>