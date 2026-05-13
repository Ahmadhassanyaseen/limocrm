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
    // print_r($lead);
    // exit;

    $paymentMethods = fetchPaymentMethods(['user_id' => $lead['owner_c']]);
    // print_r($stripeRes);
    // exit;
    // $stripePk = '';
    // if (!empty($stripeRes['success'])) {
    //     $stripePk = (string)($stripeRes['stripe_publishable_key'] ?? '');
    // }
    $method = '';
    $keys = [];
    if(!empty($paymentMethods['keys']['preferred_payment'] ) && $paymentMethods['keys']['preferred_payment'] === 'stripe') {
        $method = 'stripe';
        $keys = (string)($paymentMethods['keys']['stripe_publishable_key'] ?? '');
    }
    if(!empty($paymentMethods['keys']['preferred_payment'] ) && $paymentMethods['keys']['preferred_payment'] === 'paypal') {
        $method = 'paypal';
        $keys = (string)($paymentMethods['keys']['paypal_client_id'] ?? '');
    }
    if(!empty($paymentMethods['keys']['preferred_payment'] ) && $paymentMethods['keys']['preferred_payment'] === 'offline') {
        $method = 'offline';
        $keys = [];
    }

    $out = json_encode([
        'success' => true,
        'lead' => $lead,
        'method' => $method,
        'keys' => $keys,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);

    echo $out !== false ? $out : json_encode(['success' => true, 'lead' => [], 'stripe_publishable_key' => $stripePk]);
    exit;
}

/* ──── SUBMIT payment → generate PDF locally ──── */
// if ($op === 'submit') {
//     $leadId      = trim((string)($_POST['lead_id'] ?? ''));
//     $sigPng      = (string)($_POST['signature_png'] ?? '');

//     $res = submitAgreementPayment([
//         'lead_id'            => $leadId,
//         'payment_method_id'  => $_POST['payment_method_id'] ?? '',
//         'signature_png'      => $sigPng,
//     ]);

//     if (empty($res['success'])) {
//         echo json_encode($res);
//         exit;
//     }

//     $leadRes = fetchSingleLead(['id' => $leadId]);
//     $lead = [];
//     if (!empty($leadRes) && is_array($leadRes)) {
//         $lead = isset($leadRes[0]) && is_array($leadRes[0]) ? $leadRes[0] : $leadRes;
//     }

//     $pdfUrl = '';
//     $pdfRelPath = '';
//     $now = date('Y-m-d H:i:s');

//     $pdfResult = limo_local_generate_pdf(
//         $leadId,
//         $lead,
//         $sigPng,
//         $res['payment_intent_id'] ?? '',
//         $now
//     );

//     if (!empty($pdfResult['success'])) {
//         $pdfUrl     = $pdfResult['url'] ?? '';
//         $pdfRelPath = $pdfResult['relative_path'] ?? '';
//     }

//     updateLeadAfterPayment([
//             'id'                   => $leadId,
//             'agreement_pdf_c'      => $pdfUrl,
//             'agreement_sign_date_c' => $now,
//             'status' => 'Converted',
//         ]);

//     $res['agreement_pdf_url'] = $pdfUrl;
//     echo json_encode($res);
//     exit;
// }

/* ──── SUBMIT payment → generate PDF locally ──── */
if ($op === 'submit') {

    $leadId            = trim((string)($_POST['lead_id'] ?? ''));
    $sigPng            = (string)($_POST['signature_png'] ?? '');

    $paymentMethod     = trim((string)($_POST['payment_method'] ?? 'offline'));

    $paymentMethodId   = trim((string)($_POST['payment_method_id'] ?? ''));

    $paypalOrderId     = trim((string)($_POST['paypal_order_id'] ?? ''));

    $paypalPayerId     = trim((string)($_POST['paypal_payer_id'] ?? ''));

    if ($leadId === '') {
        echo json_encode([
            'success' => false,
            'message' => 'Missing lead id'
        ]);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT PROCESSING
    |--------------------------------------------------------------------------
    */

    $res = [
        'success' => true
    ];

    //
    // STRIPE
    //
    if ($paymentMethod === 'stripe') {

        $res = submitAgreementPayment([
    'lead_id'            => $leadId,
    'payment_method'     => $paymentMethod,
    'payment_method_id'  => $paymentMethodId,
    'paypal_order_id'    => $paypalOrderId,
    'paypal_payer_id'    => $paypalPayerId,
    'signature_png'      => $sigPng,
]);
    }

    //
    // PAYPAL
    //
    else if ($paymentMethod === 'paypal') {

        if ($paypalOrderId === '') {

            echo json_encode([
                'success' => false,
                'message' => 'Missing PayPal order ID'
            ]);

            exit;
        }

        // You can later verify from PayPal API here

        $res = [
            'success'            => true,
            'payment_intent_id'  => $paypalOrderId,
            'paypal_payer_id'    => $paypalPayerId,
        ];
    }

    //
    // OFFLINE
    //
    else if ($paymentMethod === 'offline') {

        $res = [
            'success' => true,
            'payment_intent_id' => '',
            'offline_payment' => true,
        ];
    }

    //
    // INVALID
    //
    else {

        echo json_encode([
            'success' => false,
            'message' => 'Invalid payment method'
        ]);

        exit;
    }

    if (empty($res['success'])) {
        echo json_encode($res);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | FETCH LEAD
    |--------------------------------------------------------------------------
    */

    $leadRes = fetchSingleLead(['id' => $leadId]);

    $lead = [];

    if (!empty($leadRes) && is_array($leadRes)) {

        $lead = isset($leadRes[0]) && is_array($leadRes[0])
            ? $leadRes[0]
            : $leadRes;
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE PDF
    |--------------------------------------------------------------------------
    */

    $pdfUrl = '';

    $now = date('Y-m-d H:i:s');

    $pdfResult = limo_local_generate_pdf(
        $leadId,
        $lead,
        $sigPng,
        $res['payment_intent_id'] ?? '',
        $now,
        $paymentMethod
    );

    if (!empty($pdfResult['success'])) {

        $pdfUrl = $pdfResult['url'] ?? '';
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE LEAD
    |--------------------------------------------------------------------------
    */

    updateLeadAfterPayment([
        'id'                     => $leadId,
        'agreement_pdf_c'        => $pdfUrl,
        'agreement_sign_date_c'  => $now,
        'status'                 => 'Converted',
    ]);

    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    $res['agreement_pdf_url'] = $pdfUrl;

    echo json_encode($res);

    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid operation']);

/* ================================================================
   LOCAL PDF GENERATION (uses TCPDF installed via composer)
   ================================================================ */
function limo_local_generate_pdf(
    $leadId,
    $lead,
    $sigPngDataUri,
    $paymentIntentId,
    $signDateTime,
    $paymentMethod = 'offline'
)
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
    $paymentLabel = 'Offline Payment';
$paymentStatus = 'Pending';
$paymentNote = 'You will be charged later by the service provider.';

if ($paymentMethod === 'stripe') {
    $paymentLabel = 'Stripe';
    $paymentStatus = 'Paid';
    $paymentNote = 'Payment was successfully processed using Stripe.';
}

if ($paymentMethod === 'paypal') {
    $paymentLabel = 'PayPal';
    $paymentStatus = 'Paid';
    $paymentNote = 'Payment was successfully processed using PayPal.';
}

    $sigBase64 = '';
    if ($sigPngDataUri !== '') {
        $raw = $sigPngDataUri;
        if (strpos($raw, 'base64,') !== false) {
            $raw = explode('base64,', $raw, 2)[1];
        }
        $sigBase64 = $raw;
    }

//     $html = '
// <style>
// body { font-family: Helvetica, Arial, sans-serif; color: #1e293b; font-size: 11px; line-height: 1.5; }
// .header { border-bottom: 3px solid #4f46e5; padding-bottom: 16px; margin-bottom: 20px; }
// .header-title { font-size: 22px; font-weight: bold; color: #4f46e5; }
// .header-sub { font-size: 10px; color: #64748b; margin-top: 4px; }
// .badge { background-color: #ecfdf5; color: #059669; font-size: 9px; font-weight: bold; padding: 4px 10px; }
// .section { margin-bottom: 18px; }
// .section-title { font-size: 11px; font-weight: bold; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid #e2e8f0; }
// .info-table { width: 100%; border-collapse: collapse; }
// .info-table td { padding: 6px 10px; border-bottom: 1px solid #f1f5f9; }
// .label { font-size: 9px; font-weight: bold; color: #64748b; text-transform: uppercase; width: 140px; }
// .value { font-size: 11px; color: #1e293b; }
// .amount-box { background-color: #4f46e5; color: #ffffff; padding: 14px 20px; text-align: center; margin: 14px 0; }
// .amount-label { font-size: 10px; text-transform: uppercase; }
// .amount-val { font-size: 26px; font-weight: bold; margin-top: 2px; }
// .sig-box { border: 2px solid #e2e8f0; padding: 10px; text-align: center; background-color: #fafbfc; }
// .sig-meta { font-size: 9px; color: #64748b; margin-top: 6px; }
// .terms { font-size: 9px; color: #64748b; line-height: 1.6; padding: 10px; background-color: #f8fafc; border: 1px solid #e2e8f0; margin-top: 14px; }
// .footer { margin-top: 24px; border-top: 2px solid #e2e8f0; padding-top: 12px; text-align: center; font-size: 9px; color: #94a3b8; }
// .ref-badge { background-color: #f1f5f9; color: #475569; font-size: 9px; padding: 3px 8px; font-family: courier; }
// </style>';

//     $html .= '<div class="header">';
//     $html .= '<table width="100%"><tr>';
//     $html .= '<td><span class="header-title">SERVICE AGREEMENT</span><br>';
//     $html .= '<span class="header-sub">Agreement ID: ' . $h($refId) . ' &nbsp;|&nbsp; Date: ' . $h($signDateFmt) . '</span></td>';
//     $html .= '<td align="right"><span class="badge"> SIGNED ' .
//     ($paymentMethod === 'offline'
//         ? '&amp; PAYMENT PENDING'
//         : '&amp; PAID')
//     . '</span></td>';
//     $html .= '</tr></table></div>';

//     $html .= '<div class="section"><div class="section-title">Client Information</div>';
//     $html .= '<table class="info-table">';
//     $html .= '<tr><td class="label">Full Name</td><td class="value">' . $h($name) . '</td></tr>';
//     $html .= '<tr><td class="label">Email</td><td class="value">' . $h($email) . '</td></tr>';
//     if ($phone) $html .= '<tr><td class="label">Phone</td><td class="value">' . $h($phone) . '</td></tr>';
//     $html .= '</table></div>';

//     $html .= '<div class="section"><div class="section-title">Service Details</div>';
//     $html .= '<table class="info-table">';
//     if ($serviceType) $html .= '<tr><td class="label">Service Type</td><td class="value">' . $h($serviceType) . '</td></tr>';
//     if ($eventDate) $html .= '<tr><td class="label">Event Date</td><td class="value">' . $h($eventDate) . '</td></tr>';
//     if ($pickup) $html .= '<tr><td class="label">Pickup</td><td class="value">' . $h($pickup) . '</td></tr>';
//     if ($dropoff) $html .= '<tr><td class="label">Dropoff</td><td class="value">' . $h($dropoff) . '</td></tr>';
//     if ($passengers) $html .= '<tr><td class="label">Passengers</td><td class="value">' . $h($passengers) . '</td></tr>';
//     if ($serviceLen) $html .= '<tr><td class="label">Service Length</td><td class="value">' . $h($serviceLen) . ' hours</td></tr>';
//     if ($rate) $html .= '<tr><td class="label">Hourly Rate</td><td class="value">$' . $h(number_format((float)$rate, 2)) . '</td></tr>';
//     $html .= '</table></div>';

//     $html .= '<div class="amount-box">';
//     // $html .= '<span class="amount-label">Total Amount Paid</span><br>';
//     $html .= '<span class="amount-label">' .
//     ($paymentMethod === 'offline'
//         ? 'Total Amount Due'
//         : 'Total Amount Paid')
//     . '</span><br>';
//     $html .= '<span class="amount-val">$' . $h($totalPrice) . '</span>';
//     $html .= '</div>';

//     // $html .= '<div class="section"><div class="section-title">Payment Information</div>';
//     // $html .= '<table class="info-table">';
//     // $html .= '<tr><td class="label">Reference</td><td class="value"><span class="ref-badge">' . $h($paymentIntentId) . '</span></td></tr>';
//     // $html .= '<tr><td class="label">Status</td><td class="value" style="color:#059669;font-weight:bold;">Succeeded</td></tr>';
//     // $html .= '<tr><td class="label">Date</td><td class="value">' . $h($signDateFmt) . '</td></tr>';
//     // $html .= '</table></div>';
//     $html .= '<div class="section">';
// $html .= '<div class="section-title">Payment Information</div>';

// $html .= '<table class="info-table">';

// $html .= '<tr>
// <td class="label">Method</td>
// <td class="value">' . $h($paymentLabel) . '</td>
// </tr>';

// if ($paymentIntentId) {

//     $html .= '<tr>
//     <td class="label">Reference</td>
//     <td class="value">
//         <span class="ref-badge">' . $h($paymentIntentId) . '</span>
//     </td>
//     </tr>';
// }

// $html .= '<tr>
// <td class="label">Status</td>
// <td class="value" style="font-weight:bold;">
// ' . $h($paymentStatus) . '
// </td>
// </tr>';

// $html .= '<tr>
// <td class="label">Date</td>
// <td class="value">' . $h($signDateFmt) . '</td>
// </tr>';

// $html .= '</table>';

// $html .= '<div style="
// margin-top:10px;
// padding:10px;
// background:#f8fafc;
// border:1px solid #e2e8f0;
// font-size:10px;
// color:#475569;
// ">
// ' . $h($paymentNote) . '
// </div>';

// $html .= '</div>';

//     $html .= '<div class="section"><div class="section-title">Electronic Signature</div>';
//     $html .= '<div class="sig-box">';
//     if ($sigBase64) {
//         $html .= '<img src="@' . $sigBase64 . '" alt="Signature" width="280" height="90">';
//     } else {
//         $html .= '<em style="color:#94a3b8;">Signature on file</em>';
//     }
//     $html .= '<br><span class="sig-meta">Signed by <b>' . $h($name) . '</b> on ' . $h($signDateFmt) . '</span>';
//     $html .= '</div></div>';

//    $html .= '<div class="terms">';
// $html .= '<b>Terms &amp; Conditions:</b> By signing this agreement, the client acknowledges and agrees to the service terms outlined above. ';

// $html .= (
//     $paymentMethod === 'offline'
//     ? 'Payment for this booking is still pending and will be collected later by the service provider. '
//     : 'The total amount has been successfully processed and paid. '
// );

// $html .= 'Cancellation and refund policies apply as per the service provider\'s standard terms. ';
// $html .= 'This electronically signed document is legally binding and constitutes a valid agreement between the parties.';
// $html .= '</div>';

//     $html .= '<div class="footer">';
//     $html .= 'This document was electronically generated and signed.<br>';
//     $html .= 'Agreement ID: ' . $h($refId) . ' &nbsp;|&nbsp; Generated: ' . $h(date('Y-m-d H:i:s')) . '';
//     $html .= '</div>';

$html = '
<style>
    body {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        color: #334155;
        font-size: 11px;
        line-height: 1.6;
        margin: 0;
        padding: 0;
    }

    .invoice-wrap {
        padding: 40px;
    }

    /* Header Section */
    .header-table {
        width: 100%;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 30px;
        margin-bottom: 30px;
    }

    .brand-color {
        color: #4f46e5;
    }

    .title-main {
        font-size: 24px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: -0.5px;
        margin: 0;
    }

    .subtitle {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
    }

    .status-badge {
        padding: 6px 14px;
        font-size: 10px;
        font-weight: 700;
        border-radius: 4px;
        text-transform: uppercase;
        background: ' . ($paymentMethod === 'offline' ? '#fef3c7' : '#dcfce7') . ';
        color: ' . ($paymentMethod === 'offline' ? '#92400e' : '#166534') . ';
    }

    /* Info Blocks */
    .info-grid {
        width: 100%;
        margin-bottom: 40px;
    }

    .info-grid td {
        vertical-align: top;
    }

    .label-tiny {
        font-size: 9px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: block;
    }

    .data-card {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        padding: 15px;
        border-radius: 8px;
        min-height: 80px;
    }

    /* Table Styling */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .items-table th {
        background: #f1f5f9;
        color: #475569;
        text-transform: uppercase;
        font-size: 9px;
        font-weight: 700;
        padding: 12px 15px;
        text-align: left;
        border-bottom: 2px solid #e2e8f0;
    }

    .items-table td {
        padding: 15px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
    }

    .text-bold { font-weight: 700; color: #1e293b; }
    .text-muted { color: #64748b; font-size: 10px; }

    /* Totals Area */
    .totals-wrapper {
        width: 100%;
        margin-top: 20px;
    }

    .total-box {
        background: #1e293b;
        color: #ffffff;
        padding: 25px;
        border-radius: 8px;
        text-align: right;
    }

    .total-label {
        font-size: 10px;
        color: #94a3b8;
        text-transform: uppercase;
    }

    .total-amount {
        font-size: 32px;
        font-weight: 700;
        margin-top: 5px;
    }

    /* Signature & Terms */
    .signature-section {
        margin-top: 50px;
        width: 100%;
    }

    .sig-display {
        border-bottom: 1px solid #e2e8f0;
        padding: 20px 0;
        text-align: center;
        margin-bottom: 10px;
    }

    .terms-box {
        margin-top: 40px;
        padding: 20px;
        background: #f8fafc;
        border-radius: 8px;
        font-size: 9.5px;
        color: #64748b;
        line-height: 1.8;
    }

    .footer-text {
        text-align: center;
        font-size: 9px;
        color: #cbd5e1;
        margin-top: 40px;
    }
</style>
';

$html .= '<div class="invoice-wrap">';

// HEADER
$html .= '
<table class="header-table">
    <tr>
        <td>
            <h1 class="title-main brand-color">Service Agreement</h1>
            <div class="subtitle">Ref: ' . $h($refId) . ' • Issued on ' . $h($signDateFmt) . '</div>
        </td>
        <td align="right">
            <span class="status-badge">' . ($paymentMethod === 'offline' ? 'Action Required' : 'Payment Received') . '</span>
        </td>
    </tr>
</table>';

// META INFO
$html .= '
<table class="info-grid">
    <tr>
        <td width="48%">
            <span class="label-tiny">Client Details</span>
            <div class="data-card">
                <div class="text-bold">' . $h($name) . '</div>
                <div class="text-muted">' . $h($email) . '</div>
                <div class="text-muted">' . $h($phone) . '</div>
            </div>
        </td>
        <td width="4%"></td>
        <td width="48%">
            <span class="label-tiny">Payment Overview</span>
            <div class="data-card">
                <div class="text-muted">Method: <span class="text-bold">' . $h($paymentLabel) . '</span></div>
                <div class="text-muted">Status: <span class="text-bold">' . $h($paymentStatus) . '</span></div>
                <div class="text-muted">Date: <span class="text-bold">' . $h($signDateFmt) . '</span></div>
            </div>
        </td>
    </tr>
</table>';

// TABLE
// $html .= '
// <table class="items-table">
//     <thead>
//         <tr>
//             <th width="45%">Service Description</th>
//             <th width="15%" align="center">Pax</th>
//             <th width="15%" align="center">Hrs/Qty</th>
//             <th width="25%" align="right">Amount</th>
//         </tr>
//     </thead>
//     <tbody>
//         <tr>
//             <td>
//                 <div class="text-bold">' . $h($serviceType ?: 'Transportation Service') . '</div>
//                 <div class="text-muted" style="margin-top:4px;">
//                     <b>From:</b> ' . $h($pickup) . '<br>
//                     <b>To:</b> ' . $h($dropoff) . '<br>
//                     <b>Schedule:</b> ' . $h($eventDate) . '
//                 </div>
//             </td>
//             <td align="center">' . $h($passengers ?: '-') . '</td>
//             <td align="center">' . $h($serviceLen ?: '-') . '</td>
//             <td align="right" class="text-bold">$' . $h(number_format((float)$rate, 2)) . '</td>
//         </tr>
//     </tbody>
// </table>';

/*
|--------------------------------------------------------------------------
| SERVICE TABLE - REFINED ALIGNMENT
|--------------------------------------------------------------------------
*/
$html .= '
<table class="items-table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr style="background-color: #f8fafc;">
            <th style="text-align: left; padding: 12px; border-bottom: 2px solid #e2e8f0; font-size: 9px; color: #94a3b8; text-transform: uppercase;">Service Description</th>
            <th style="text-align: center; padding: 12px; border-bottom: 2px solid #e2e8f0; font-size: 9px; color: #94a3b8; text-transform: uppercase;" width="10%">Pax</th>
            <th style="text-align: center; padding: 12px; border-bottom: 2px solid #e2e8f0; font-size: 9px; color: #94a3b8; text-transform: uppercase;" width="10%">Hrs/Qty</th>
            <th style="text-align: right; padding: 12px; border-bottom: 2px solid #e2e8f0; font-size: 9px; color: #94a3b8; text-transform: uppercase;" width="20%">Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding: 15px 12px; vertical-align: top; border-bottom: 1px solid #f1f5f9;">
                <div style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">' . $h($serviceType ?: 'Convention') . '</div>
                <div style="color: #64748b; font-size: 10px; line-height: 1.5;">
                    <span style="color: #4f46e5; font-weight: bold;">From:</span> ' . $h($pickup) . '<br>
                    <span style="color: #4f46e5; font-weight: bold;">To:</span> ' . $h($dropoff) . '<br>
                    <span style="color: #4f46e5; font-weight: bold;">Schedule:</span> ' . $h($eventDate) . '
                </div>
            </td>
            <td style="padding: 15px 12px; text-align: center; vertical-align: top; border-bottom: 1px solid #f1f5f9; font-size: 12px; font-weight: bold;">
                ' . $h($passengers ?: '-') . '
            </td>
            <td style="padding: 15px 12px; text-align: center; vertical-align: top; border-bottom: 1px solid #f1f5f9; font-size: 12px; font-weight: bold;">
                ' . $h($serviceLen ?: '-') . '
            </td>
            <td style="padding: 15px 12px; text-align: right; vertical-align: top; border-bottom: 1px solid #f1f5f9; font-size: 14px; font-weight: 700; color: #1e293b;">
                $' . $h(number_format((float)$rate, 2)) . '
            </td>
        </tr>
    </tbody>
</table>';

// TOTALS
$html .= '
<table class="totals-wrapper">
    <tr>
        <td width="55%">
            <div style="padding-right: 40px;">
                <span class="label-tiny">Internal Memo</span>
                <div class="text-muted">' . ($paymentNote ? $h($paymentNote) : 'No additional notes provided.') . '</div>
                ' . ($paymentIntentId ? '<div class="text-muted" style="margin-top:8px;"><b>TxID:</b> ' . $h($paymentIntentId) . '</div>' : '') . '
            </div>
        </td>
        <td width="45%">
            <div class="total-box">
                <div class="total-label">' . ($paymentMethod === 'offline' ? 'Balance Due' : 'Grand Total') . '</div>
                <div class="total-amount">$' . $h($totalPrice) . '</div>
            </div>
        </td>
    </tr>
</table>';

// SIGNATURE
$html .= '
<div class="signature-section">
    <span class="label-tiny">Authorized Electronic Signature</span>
    <div class="sig-display">
        ' . ($sigBase64 ? '<img src="@' . $sigBase64 . '" width="220">' : '<span class="text-muted">Digitally Verified</span>') . '
    </div>
    <div style="text-align:center;" class="text-muted">
        Signed by <b>' . $h($name) . '</b> on ' . $h($signDateFmt) . '
    </div>
</div>';

// TERMS
$html .= '
<div class="terms-box">
    <div class="text-bold" style="margin-bottom:8px;">Legal Terms & Conditions</div>
    This document confirms your booking for transportation services. ' . 
    ($paymentMethod === 'offline' 
        ? 'Payment is currently outstanding and must be settled per the agreed terms. ' 
        : 'Payment has been processed successfully via our secure gateway. ') . 
    'Cancellation and refund policies are subject to the master service agreement. This digital record serves as a binding contract between the provider and the client named above.
</div>';

// FOOTER
$html .= '
<div class="footer-text">
    Generated via Secure Portal • ID: ' . $h($refId) . ' • Page 1 of 1
</div>';

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
