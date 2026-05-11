<?php
/**
 * Public AJAX bridge for standalone agreement.php (no session).
 * - fetch: gets lead data + Stripe publishable key
 * - submit: sends payment to remote, then generates PDF locally
 */
ob_start();
include __DIR__ . '/api.php';
ob_end_clean();

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$op = isset($_POST['op']) ? (string)$_POST['op'] : '';

/* ──── FETCH lead details + Stripe key ──── */
if ($op === 'fetch') {
    $leadId = trim((string)($_POST['lead_id'] ?? ''));
    if ($leadId === '') {
        echo json_encode(['success' => false, 'message' => 'Missing lead_id']);
        exit;
    }

    $leadRes = fetchSingleLead(['id' => $leadId]);
    if (empty($leadRes) || !is_array($leadRes)) {
        echo json_encode(['success' => false, 'message' => 'Lead not found']);
        exit;
    }

    $lead = isset($leadRes[0]) && is_array($leadRes[0]) ? $leadRes[0] : $leadRes;

    $stripeRes = fetchLeadStripeKey(['lead_id' => $leadId]);
    $stripePk = '';
    if (!empty($stripeRes['success'])) {
        $stripePk = (string)($stripeRes['stripe_publishable_key'] ?? '');
    }

    $out = json_encode([
        'success' => true,
        'lead' => $lead,
        'stripe_publishable_key' => $stripePk,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);

    echo $out !== false ? $out : json_encode(['success' => true, 'lead' => [], 'stripe_publishable_key' => $stripePk]);
    exit;
}

/* ──── SUBMIT payment → generate PDF locally ──── */
if ($op === 'submit') {
    $leadId      = trim((string)($_POST['lead_id'] ?? ''));
    $sigPng      = (string)($_POST['signature_png'] ?? '');

    $res = submitAgreementPayment([
        'lead_id'            => $leadId,
        'payment_method_id'  => $_POST['payment_method_id'] ?? '',
        'signature_png'      => $sigPng,
    ]);

    if (empty($res['success'])) {
        echo json_encode($res);
        exit;
    }

    $leadRes = fetchSingleLead(['id' => $leadId]);
    $lead = [];
    if (!empty($leadRes) && is_array($leadRes)) {
        $lead = isset($leadRes[0]) && is_array($leadRes[0]) ? $leadRes[0] : $leadRes;
    }

    $pdfUrl = '';
    $pdfRelPath = '';
    $now = date('Y-m-d H:i:s');

    $pdfResult = limo_local_generate_pdf(
        $leadId,
        $lead,
        $sigPng,
        $res['payment_intent_id'] ?? '',
        $now
    );

    if (!empty($pdfResult['success'])) {
        $pdfUrl     = $pdfResult['url'] ?? '';
        $pdfRelPath = $pdfResult['relative_path'] ?? '';
    }

    updateLeadAfterPayment([
            'id'                   => $leadId,
            'agreement_pdf_c'      => $pdfUrl,
            'agreement_sign_date_c' => $now,
            'status' => 'Converted',
        ]);

    $res['agreement_pdf_url'] = $pdfUrl;
    echo json_encode($res);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid operation']);

/* ================================================================
   LOCAL PDF GENERATION (uses TCPDF installed via composer)
   ================================================================ */
function limo_local_generate_pdf($leadId, $lead, $sigPngDataUri, $paymentIntentId, $signDateTime)
{
    $pdfDir = dirname(__DIR__) . '/pdf/';
    if (!is_dir($pdfDir)) {
        @mkdir($pdfDir, 0775, true);
    }

    $autoload = dirname(__DIR__) . '/vendor/autoload.php';
    if (!file_exists($autoload)) {
        return ['success' => false, 'message' => 'Composer autoload not found'];
    }
    require_once $autoload;

    if (!class_exists('TCPDF')) {
        return ['success' => false, 'message' => 'TCPDF not installed'];
    }

    $h = function($s) { return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8'); };

    $name        = trim(($lead['first_name'] ?? '') . ' ' . ($lead['last_name'] ?? ''));
    $email       = trim((string)($lead['email1'] ?? $lead['email'] ?? ''));
    $phone       = trim((string)($lead['phone_c'] ?? $lead['phone_work'] ?? $lead['phone_mobile'] ?? ''));
    $pickup      = trim((string)($lead['pickup_address_c'] ?? ''));
    $dropoff     = trim((string)($lead['dropoff_address_c'] ?? ''));
    $eventDate   = trim((string)($lead['event_date_c'] ?? ''));
    $serviceType = trim((string)($lead['service_type_c'] ?? ''));
    $passengers  = trim((string)($lead['passengers_c'] ?? ''));
    $serviceLen  = trim((string)($lead['service_length_c'] ?? ''));
    $rate        = trim((string)($lead['rate_c'] ?? ''));
    $totalRaw    = trim((string)($lead['total_price_c'] ?? '0'));
    $totalNum    = (float)preg_replace('/[^0-9.]/', '', $totalRaw);
    $totalPrice  = number_format($totalNum, 2);
    $signDateFmt = date('F j, Y \a\t g:i A', strtotime($signDateTime));
    $refId       = $paymentIntentId ? strtoupper(substr($paymentIntentId, 3, 12)) : strtoupper(substr(md5($leadId), 0, 12));

    $sigBase64 = '';
    if ($sigPngDataUri !== '') {
        $raw = $sigPngDataUri;
        if (strpos($raw, 'base64,') !== false) {
            $raw = explode('base64,', $raw, 2)[1];
        }
        $sigBase64 = $raw;
    }

    $html = '
<style>
body { font-family: Helvetica, Arial, sans-serif; color: #1e293b; font-size: 11px; line-height: 1.5; }
.header { border-bottom: 3px solid #4f46e5; padding-bottom: 16px; margin-bottom: 20px; }
.header-title { font-size: 22px; font-weight: bold; color: #4f46e5; }
.header-sub { font-size: 10px; color: #64748b; margin-top: 4px; }
.badge { background-color: #ecfdf5; color: #059669; font-size: 9px; font-weight: bold; padding: 4px 10px; }
.section { margin-bottom: 18px; }
.section-title { font-size: 11px; font-weight: bold; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid #e2e8f0; }
.info-table { width: 100%; border-collapse: collapse; }
.info-table td { padding: 6px 10px; border-bottom: 1px solid #f1f5f9; }
.label { font-size: 9px; font-weight: bold; color: #64748b; text-transform: uppercase; width: 140px; }
.value { font-size: 11px; color: #1e293b; }
.amount-box { background-color: #4f46e5; color: #ffffff; padding: 14px 20px; text-align: center; margin: 14px 0; }
.amount-label { font-size: 10px; text-transform: uppercase; }
.amount-val { font-size: 26px; font-weight: bold; margin-top: 2px; }
.sig-box { border: 2px solid #e2e8f0; padding: 10px; text-align: center; background-color: #fafbfc; }
.sig-meta { font-size: 9px; color: #64748b; margin-top: 6px; }
.terms { font-size: 9px; color: #64748b; line-height: 1.6; padding: 10px; background-color: #f8fafc; border: 1px solid #e2e8f0; margin-top: 14px; }
.footer { margin-top: 24px; border-top: 2px solid #e2e8f0; padding-top: 12px; text-align: center; font-size: 9px; color: #94a3b8; }
.ref-badge { background-color: #f1f5f9; color: #475569; font-size: 9px; padding: 3px 8px; font-family: courier; }
</style>';

    $html .= '<div class="header">';
    $html .= '<table width="100%"><tr>';
    $html .= '<td><span class="header-title">SERVICE AGREEMENT</span><br>';
    $html .= '<span class="header-sub">Agreement ID: ' . $h($refId) . ' &nbsp;|&nbsp; Date: ' . $h($signDateFmt) . '</span></td>';
    $html .= '<td align="right"><span class="badge">&check; SIGNED &amp; PAID</span></td>';
    $html .= '</tr></table></div>';

    $html .= '<div class="section"><div class="section-title">Client Information</div>';
    $html .= '<table class="info-table">';
    $html .= '<tr><td class="label">Full Name</td><td class="value">' . $h($name) . '</td></tr>';
    $html .= '<tr><td class="label">Email</td><td class="value">' . $h($email) . '</td></tr>';
    if ($phone) $html .= '<tr><td class="label">Phone</td><td class="value">' . $h($phone) . '</td></tr>';
    $html .= '</table></div>';

    $html .= '<div class="section"><div class="section-title">Service Details</div>';
    $html .= '<table class="info-table">';
    if ($serviceType) $html .= '<tr><td class="label">Service Type</td><td class="value">' . $h($serviceType) . '</td></tr>';
    if ($eventDate) $html .= '<tr><td class="label">Event Date</td><td class="value">' . $h($eventDate) . '</td></tr>';
    if ($pickup) $html .= '<tr><td class="label">Pickup</td><td class="value">' . $h($pickup) . '</td></tr>';
    if ($dropoff) $html .= '<tr><td class="label">Dropoff</td><td class="value">' . $h($dropoff) . '</td></tr>';
    if ($passengers) $html .= '<tr><td class="label">Passengers</td><td class="value">' . $h($passengers) . '</td></tr>';
    if ($serviceLen) $html .= '<tr><td class="label">Service Length</td><td class="value">' . $h($serviceLen) . ' hours</td></tr>';
    if ($rate) $html .= '<tr><td class="label">Hourly Rate</td><td class="value">$' . $h(number_format((float)$rate, 2)) . '</td></tr>';
    $html .= '</table></div>';

    $html .= '<div class="amount-box">';
    $html .= '<span class="amount-label">Total Amount Paid</span><br>';
    $html .= '<span class="amount-val">$' . $h($totalPrice) . '</span>';
    $html .= '</div>';

    $html .= '<div class="section"><div class="section-title">Payment Information</div>';
    $html .= '<table class="info-table">';
    $html .= '<tr><td class="label">Reference</td><td class="value"><span class="ref-badge">' . $h($paymentIntentId) . '</span></td></tr>';
    $html .= '<tr><td class="label">Status</td><td class="value" style="color:#059669;font-weight:bold;">Succeeded</td></tr>';
    $html .= '<tr><td class="label">Date</td><td class="value">' . $h($signDateFmt) . '</td></tr>';
    $html .= '</table></div>';

    $html .= '<div class="section"><div class="section-title">Electronic Signature</div>';
    $html .= '<div class="sig-box">';
    if ($sigBase64) {
        $html .= '<img src="@' . $sigBase64 . '" alt="Signature" width="280" height="90">';
    } else {
        $html .= '<em style="color:#94a3b8;">Signature on file</em>';
    }
    $html .= '<br><span class="sig-meta">Signed by <b>' . $h($name) . '</b> on ' . $h($signDateFmt) . '</span>';
    $html .= '</div></div>';

    $html .= '<div class="terms">';
    $html .= '<b>Terms &amp; Conditions:</b> By signing this agreement, the client acknowledges and agrees to the service terms outlined above. ';
    $html .= 'The total amount has been charged to the card on file. Cancellation and refund policies apply as per the service provider\'s standard terms. ';
    $html .= 'This electronically signed document is legally binding and constitutes a valid agreement between the parties.';
    $html .= '</div>';

    $html .= '<div class="footer">';
    $html .= 'This document was electronically generated and signed.<br>';
    $html .= 'Agreement ID: ' . $h($refId) . ' &nbsp;|&nbsp; Generated: ' . $h(date('Y-m-d H:i:s')) . '';
    $html .= '</div>';

    $pdfFileName = 'agreement_' . $leadId . '_' . date('Ymd_His') . '.pdf';
    $pdfFilePath = $pdfDir . $pdfFileName;

    $sigTmpFile = null;
    if ($sigBase64) {
        $sigBin = base64_decode($sigBase64, true);
        if ($sigBin !== false) {
            $sigTmpFile = tempnam(sys_get_temp_dir(), 'sig_') . '.png';
            file_put_contents($sigTmpFile, $sigBin);
            $html = str_replace(
                'src="@' . $sigBase64 . '"',
                'src="' . str_replace('\\', '/', $sigTmpFile) . '"',
                $html
            );
        }
    }

    try {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output($pdfFilePath, 'F');
    } catch (\Exception $e) {
        if ($sigTmpFile && file_exists($sigTmpFile)) @unlink($sigTmpFile);
        return ['success' => false, 'message' => 'PDF error: ' . $e->getMessage()];
    }

    if ($sigTmpFile && file_exists($sigTmpFile)) @unlink($sigTmpFile);

    if (!file_exists($pdfFilePath)) {
        return ['success' => false, 'message' => 'PDF file was not created'];
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base   = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
    $base   = rtrim(dirname($base), '/');
    $pdfUrl = $scheme . '://' . $host . $base . '/pdf/' . $pdfFileName;

    return [
        'success'       => true,
        'url'           => $pdfUrl,
        'relative_path' => 'pdf/' . $pdfFileName,
        'filename'      => $pdfFileName,
    ];
}
