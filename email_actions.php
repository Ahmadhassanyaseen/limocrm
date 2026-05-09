<?php
/**
 * Public email tracking & unsubscribe endpoint.
 *
 * Linked from outgoing mail (CustomEntryPoint placeholders):
 *   ?action=track&email_id={emails.id}
 *   ?action=unsubscribe&email_id={emails.id}
 *
 * Proxies to SuiteCRM CustomEntryPoint GET handlers (track_email_open / unsubscribe_email)
 * which update emails_cstm.opened_c and emails_cstm.unsubscribe_c.
 */

declare(strict_types=1);

/** SuiteCRM front controller (GET handlers: track_email_open, unsubscribe_email, + aliases track / unsubscribe) */
const LIMO_SUITE_INDEX = 'https://zabrin.xyz/limogen/index.php';

/** 1×1 transparent GIF (same as SuiteCRM pixel) */
const LIMO_PIXEL_GIF =
    'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

function limo_valid_email_pk(string $id): bool {
    return (bool)preg_match('/^[a-f0-9\-]{36}$/i', $id);
}

function limo_proxy_suitecrm_tracking(string $suiteAction, string $eid): void {
    $query = http_build_query([
        'entryPoint' => 'CustomEntryPoint',
        'action'     => $suiteAction,
        'eid'        => $eid,
    ]);
    $url = LIMO_SUITE_INDEX . '?' . $query;

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERAGENT      => 'LimoCRM-email-actions/1.0',
        ]);
        curl_exec($ch);
        curl_close($ch);
        return;
    }

    $ctx = stream_context_create([
        'http' => [
            'timeout' => 5,
            'header'  => "User-Agent: LimoCRM-email-actions/1.0\r\n",
        ],
    ]);
    @file_get_contents($url, false, $ctx);
}

$action = isset($_GET['action']) ? (string)$_GET['action'] : '';
$emailId = isset($_GET['email_id']) ? trim((string)$_GET['email_id']) : '';

// Tracking pixel: always return image (avoid leaking whether id exists).
if ($action === 'track') {
    if (limo_valid_email_pk($emailId)) {
        limo_proxy_suitecrm_tracking('track_email_open', $emailId);
    }
    header('Content-Type: image/gif');
    header('Cache-Control: no-store, no-cache, must-revalidate, private');
    header('Pragma: no-cache');
    echo base64_decode(LIMO_PIXEL_GIF);
    exit;
}

if ($action === 'unsubscribe') {
    if (limo_valid_email_pk($emailId)) {
        limo_proxy_suitecrm_tracking('unsubscribe_email', $emailId);
    }
    header('Content-Type: text/html; charset=UTF-8');
    http_response_code(200);
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Unsubscribed</title>
  <style>
    body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#f8fafc;color:#1e293b;}
    .card{text-align:center;padding:48px;border-radius:20px;background:#fff;box-shadow:0 4px 24px rgba(15,23,42,0.08);max-width:440px;}
    h1{font-size:24px;margin:0 0 8px;}
    p{color:#64748b;font-size:15px;line-height:1.6;margin:0;}
    .icon{width:64px;height:64px;border-radius:16px;background:rgba(239,68,68,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:28px;color:#ef4444;}
  </style>
</head>
<body>
  <div class="card">
    <div class="icon">&#9993;</div>
    <h1>Unsubscribed</h1>
    <p>You have been unsubscribed from future marketing emails for this address.</p>
  </div>
</body>
</html>
    <?php
    exit;
}

http_response_code(404);
header('Content-Type: text/plain; charset=UTF-8');
echo 'Not found.';
