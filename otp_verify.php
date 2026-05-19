<?php
/**
 * Enter 6-digit OTP from email → POST verify on mail-server → CRM test-user login → leads.php
 */
declare(strict_types=1);

require_once __DIR__ . '/config/otp_mail_server.php';
require_once __DIR__ . '/config/api.php';

// ===============================
// CONFIG — same bootstrap user as prior welcome flow (login.php: user_name + password1)
// ===============================
$AUTO_LOGIN_USER = 'test_limo_crm';
$AUTO_LOGIN_PASS = 'test@1234';

/**
 * @internal Auth-shell markup aligned with login.php (this file only).
 */
function limo_otp_h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function limo_otp_print_styles(): void
{
    ?>
  <style>
    :root {
      --crm-primary: #cf1c82;
      --crm-primary-hover: #b01670;
      --crm-primary-rgb: 207, 28, 130;
      --crm-surface: #ffffff;
      --crm-surface-elevated: rgba(255, 255, 255, 0.72);
      --crm-text: #0f172a;
      --crm-text-muted: #64748b;
      --crm-border: rgba(15, 23, 42, 0.08);
      --crm-input-bg: #f8fafc;
      --crm-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.06), 0 24px 48px -12px rgba(15, 23, 42, 0.12);
      --crm-radius-lg: 20px;
      --crm-radius-md: 12px;
      --crm-font: "DM Sans", system-ui, -apple-system, sans-serif;
    }
    @media (prefers-color-scheme: dark) {
      :root {
        --crm-surface: #0f1419;
        --crm-surface-elevated: rgba(22, 28, 36, 0.85);
        --crm-text: #f1f5f9;
        --crm-text-muted: #94a3b8;
        --crm-border: rgba(255, 255, 255, 0.08);
        --crm-input-bg: rgba(255, 255, 255, 0.06);
        --crm-shadow: 0 4px 24px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.06);
      }
    }
    *, *::before, *::after { box-sizing: border-box; }
    body.crm-login {
      margin: 0;
      min-height: 100vh;
      font-family: var(--crm-font);
      font-size: 15px;
      line-height: 1.5;
      color: var(--crm-text);
      background-color: var(--crm-surface);
      background-image:
        radial-gradient(ellipse 100% 80% at 0% 0%, rgba(var(--crm-primary-rgb), 0.14), transparent 50%),
        radial-gradient(ellipse 80% 60% at 100% 20%, rgba(14, 165, 233, 0.12), transparent 45%),
        radial-gradient(ellipse 60% 50% at 50% 100%, rgba(var(--crm-primary-rgb), 0.06), transparent 55%);
      -webkit-font-smoothing: antialiased;
    }
    @media (prefers-color-scheme: dark) {
      body.crm-login {
        background-image:
          radial-gradient(ellipse 100% 80% at 0% 0%, rgba(var(--crm-primary-rgb), 0.22), transparent 50%),
          radial-gradient(ellipse 80% 60% at 100% 15%, rgba(56, 189, 248, 0.12), transparent 45%),
          radial-gradient(ellipse 70% 40% at 50% 100%, rgba(var(--crm-primary-rgb), 0.1), transparent 50%);
      }
    }
    .crm-login-shell {
      display: grid;
      min-height: 100vh;
      grid-template-columns: 1fr;
    }
    @media (min-width: 1024px) {
      .crm-login-shell { grid-template-columns: minmax(0, 1fr) minmax(380px, 480px); }
    }
    .crm-login-form-col {
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: clamp(1.5rem, 4vw, 3rem);
      position: relative;
    }
    .crm-login-form-inner { width: 100%; max-width: 400px; margin: 0 auto; }
    .crm-login-brand {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1.75rem;
    }
    .crm-login-brand img { height: 32px; width: auto; }
    @media (prefers-color-scheme: dark) {
      .crm-login-brand .logo-light { display: none; }
      .crm-login-brand .logo-dark { display: block !important; }
    }
    .crm-login-brand .logo-dark { display: none; }
    .crm-otp-steps {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      flex-wrap: wrap;
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--crm-text-muted);
      margin: 0 0 0.75rem;
    }
    .crm-otp-steps .dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: rgba(var(--crm-primary-rgb), 0.35);
    }
    .crm-otp-steps .current { color: var(--crm-primary); }
    .crm-login-eyebrow {
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--crm-primary);
      margin: 0 0 0.5rem;
    }
    .crm-login-title {
      font-size: clamp(1.65rem, 4vw, 2rem);
      font-weight: 700;
      letter-spacing: -0.03em;
      line-height: 1.2;
      margin: 0 0 0.5rem;
      color: var(--crm-text);
    }
    .crm-login-sub {
      font-size: 0.9375rem;
      color: var(--crm-text-muted);
      margin: 0 0 1.5rem;
      max-width: 38ch;
    }
    .crm-login-sub strong { color: var(--crm-text); font-weight: 600; }
    .crm-hint-strip {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin: 0 0 1.25rem;
    }
    .crm-hint-chip {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.35rem 0.65rem;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--crm-text-muted);
      background: var(--crm-input-bg);
      border: 1px solid var(--crm-border);
    }
    .crm-hint-chip i { font-size: 0.9375rem; color: var(--crm-primary); }
    .crm-login-card {
      background: var(--crm-surface-elevated);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid var(--crm-border);
      border-radius: var(--crm-radius-lg);
      padding: clamp(1.5rem, 4vw, 2rem);
      box-shadow: var(--crm-shadow);
    }
    .crm-field { margin-bottom: 0; }
    .crm-label {
      display: flex;
      align-items: center;
      gap: 0.375rem;
      font-size: 0.8125rem;
      font-weight: 600;
      color: var(--crm-text);
      margin-bottom: 0.5rem;
    }
    .crm-label i { color: var(--crm-primary); font-size: 1rem; }
    .crm-input-wrap { position: relative; display: flex; align-items: center; }
    .crm-input-wrap .crm-input-icon {
      position: absolute;
      left: 14px;
      color: var(--crm-text-muted);
      font-size: 1.125rem;
      pointer-events: none;
      z-index: 1;
    }
    .crm-input-wrap .form-control.crm-otp-input {
      width: 100%;
      height: 52px;
      padding-left: 2.75rem !important;
      padding-right: 1rem;
      border-radius: var(--crm-radius-md);
      border: 1px solid var(--crm-border);
      background: var(--crm-input-bg);
      color: var(--crm-text);
      font-family: ui-monospace, "Cascadia Code", monospace;
      font-size: 1.125rem;
      font-weight: 600;
      letter-spacing: 0.38em;
      text-align: center;
      font-variant-numeric: tabular-nums;
      transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .crm-input-wrap .form-control.crm-otp-input::placeholder {
      color: var(--crm-text-muted);
      letter-spacing: 0.2em;
      font-weight: 500;
    }
    .crm-input-wrap .form-control.crm-otp-input:hover {
      border-color: rgba(var(--crm-primary-rgb), 0.35);
    }
    .crm-input-wrap .form-control.crm-otp-input:focus {
      outline: none;
      border-color: var(--crm-primary);
      box-shadow: 0 0 0 3px rgba(var(--crm-primary-rgb), 0.2);
      background: var(--crm-surface);
    }
    @media (prefers-color-scheme: dark) {
      .crm-input-wrap .form-control.crm-otp-input:focus {
        background: rgba(255, 255, 255, 0.04);
      }
    }
    .crm-submit {
      width: 100%;
      margin-top: 1.25rem;
      height: 48px;
      border: none;
      border-radius: var(--crm-radius-md);
      font-family: inherit;
      font-size: 0.9375rem;
      font-weight: 600;
      color: #fff;
      background: var(--crm-primary);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      transition: transform 0.15s, box-shadow 0.2s, background 0.2s;
      box-shadow: 0 4px 14px rgba(var(--crm-primary-rgb), 0.35);
    }
    .crm-submit:hover:not(:disabled) {
      background: var(--crm-primary-hover);
      transform: translateY(-1px);
      box-shadow: 0 8px 22px rgba(var(--crm-primary-rgb), 0.4);
    }
    .crm-submit:active:not(:disabled) { transform: translateY(0); }
    .crm-submit:disabled { opacity: 0.75; cursor: wait; }
    .crm-submit:focus-visible {
      outline: 2px solid var(--crm-text);
      outline-offset: 3px;
    }
    @keyframes crmOtpSpin {
      to { transform: rotate(360deg); }
    }
    .crm-otp-spin {
      display: inline-block;
      width: 16px;
      height: 16px;
      border: 2px solid rgba(255, 255, 255, 0.35);
      border-top-color: #fff;
      border-radius: 50%;
      animation: crmOtpSpin 0.75s linear infinite;
      vertical-align: -3px;
      margin-right: 0.35rem;
    }
    .crm-otp-err {
      margin-top: 1rem;
      padding: 0.75rem 0.875rem;
      border-radius: var(--crm-radius-md);
      font-size: 0.875rem;
      line-height: 1.45;
      color: #b91c1c;
      background: rgba(239, 68, 68, 0.08);
      border: 1px solid rgba(239, 68, 68, 0.28);
    }
    @media (prefers-color-scheme: dark) {
      .crm-otp-err {
        color: #fecaca;
        background: rgba(239, 68, 68, 0.12);
        border-color: rgba(239, 68, 68, 0.35);
      }
    }
    .crm-otp-err i { vertical-align: -2px; margin-right: 0.25rem; }
    .crm-actions-row {
      margin-top: 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }
    .crm-link-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.65rem 1rem;
      border-radius: var(--crm-radius-md);
      font-size: 0.875rem;
      font-weight: 600;
      text-decoration: none;
      color: var(--crm-primary);
      border: 1px solid rgba(var(--crm-primary-rgb), 0.35);
      background: rgba(var(--crm-primary-rgb), 0.06);
      transition: background 0.2s, border-color 0.2s, transform 0.15s;
    }
    .crm-link-btn:hover {
      background: rgba(var(--crm-primary-rgb), 0.12);
      border-color: var(--crm-primary);
    }
    .crm-login-foot {
      margin-top: 1.75rem;
      text-align: center;
      font-size: 0.8125rem;
      color: var(--crm-text-muted);
      line-height: 1.55;
    }
    .crm-login-foot a {
      color: var(--crm-primary);
      font-weight: 600;
      text-decoration: none;
    }
    .crm-login-foot a:hover { text-decoration: underline; }
    .crm-login-hero {
      display: none;
      position: relative;
      flex-direction: column;
      justify-content: space-between;
      padding: clamp(2rem, 5vw, 3.5rem);
      overflow: hidden;
      background: linear-gradient(155deg, #0b1220 0%, #111827 42%, #1a0a14 100%);
    }
    @media (min-width: 1024px) {
      .crm-login-hero { display: flex; }
    }
    .crm-login-hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 90% 70% at 20% 0%, rgba(var(--crm-primary-rgb), 0.45), transparent 55%),
        radial-gradient(ellipse 60% 50% at 100% 80%, rgba(56, 189, 248, 0.15), transparent 50%);
      pointer-events: none;
    }
    .crm-login-hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      opacity: 0.6;
      pointer-events: none;
    }
    .crm-hero-top { position: relative; z-index: 1; }
    .crm-hero-top a {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
    }
    .crm-hero-top img { height: 28px; }
    .crm-hero-body {
      position: relative;
      z-index: 1;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 2rem 0;
    }
    .crm-hero-visual {
      max-width: 300px;
      margin: 0 auto 2rem;
      filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.35));
    }
    .crm-hero-visual img { width: 100%; height: auto; display: block; }
    .crm-hero-quote {
      font-size: clamp(1.4rem, 2.5vw, 1.75rem);
      font-weight: 700;
      line-height: 1.25;
      letter-spacing: -0.02em;
      color: #fff;
      margin: 0 0 1.5rem;
      text-align: center;
      text-wrap: balance;
    }
    .crm-hero-list {
      list-style: none;
      margin: 0;
      padding: 0;
      display: grid;
      gap: 0.75rem;
      max-width: 320px;
      margin-left: auto;
      margin-right: auto;
    }
    .crm-hero-list li {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-size: 0.875rem;
      color: rgba(255, 255, 255, 0.88);
    }
    .crm-hero-list li i {
      flex-shrink: 0;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
      font-size: 1rem;
    }
    .crm-hero-bottom {
      position: relative;
      z-index: 1;
      font-size: 0.75rem;
      color: rgba(255, 255, 255, 0.45);
      text-align: center;
    }
    .crm-msg-lead {
      font-size: 0.9375rem;
      color: var(--crm-text-muted);
      margin: 0 0 1.25rem;
      line-height: 1.55;
      max-width: 36ch;
    }
    @media (max-width: 380px) {
      .crm-input-wrap .form-control.crm-otp-input { letter-spacing: 0.28em; }
    }
  </style>
    <?php
}

function limo_otp_print_shell_start(string $title): void
{
    ?>
<!DOCTYPE html>
<html lang="en" class="crm-login-page">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="color-scheme" content="light dark" />
  <title><?php echo limo_otp_h($title); ?></title>
  <link rel="icon" href="assets/images/brand-logos/favicon.ico" type="image/x-icon" />
  <link href="assets/css/styles.css" rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css"
    integrity="sha512-XcIsjKMcuVe0Ucj/xgIXQnytNwBttJbNjltBV18IOnru2lDPe9KRRyvCXw6Y5H415vbBLRm8+q6fmLUU7DfO6Q=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap"
    rel="stylesheet"
  />
    <?php limo_otp_print_styles(); ?>
</head>
<body class="crm-login">
<div class="crm-login-shell">
<main class="crm-login-form-col">
<div class="crm-login-form-inner">
    <?php
}

function limo_otp_print_brand(): void
{
    ?>
<div class="crm-login-brand">
  <img src="assets/images/brand-logos/desktop-dark.png" class="logo-light" alt="LimoCRM" onerror="this.style.display='none'" />
  <img src="assets/images/brand-logos/desktop-white.png" class="logo-dark" alt="LimoCRM" onerror="this.style.display='none'" />
</div>
    <?php
}

/**
 * @param list<array{icon:string,text:string}> $items
 */
function limo_otp_print_hero(string $quote, array $items): void
{
    ?>
</div>
</main>
<aside class="crm-login-hero" aria-label="Why we verify">
  <div class="crm-hero-top">
    <a href="./index.php">
      <img src="assets/images/brand-logos/desktop-white.png" alt="LimoCRM home" />
    </a>
  </div>
  <div class="crm-hero-body">
    <div class="crm-hero-visual">
      <img src="assets/images/media/login-a.svg" alt="" role="presentation" />
    </div>
    <p class="crm-hero-quote"><?php echo limo_otp_h($quote); ?></p>
    <ul class="crm-hero-list">
      <?php foreach ($items as $row) { ?>
      <li>
        <i class="<?php echo limo_otp_h($row['icon']); ?>" aria-hidden="true"></i>
        <span><?php echo limo_otp_h($row['text']); ?></span>
      </li>
      <?php } ?>
    </ul>
  </div>
  <p class="crm-hero-bottom">© LimoCRM · Internal use</p>
</aside>
</div>
</body>
</html>
    <?php
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_GET['send_failed']) && (string) $_GET['send_failed'] === '1') {
    $fid = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
    $flash = isset($_SESSION['limo_otp_flash_error']) ? (string) $_SESSION['limo_otp_flash_error'] : 'Could not send verification code.';
    unset($_SESSION['limo_otp_flash_error']);
    $uuidOk = $fid !== '' && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $fid);
    $retry = $uuidOk ? 'welcome.php?id=' . rawurlencode($fid) : 'login.php';
    $retryLabel = $uuidOk ? 'Try sending again' : 'Go to staff sign-in';

    limo_otp_print_shell_start('Could not send code | LimoCRM');
    limo_otp_print_brand();
    ?>
  <p class="crm-login-eyebrow">Email verification</p>
  <h1 class="crm-login-title" id="otp-heading">We couldn’t send the code</h1>
  <p class="crm-msg-lead"><?php echo limo_otp_h($flash); ?></p>
  <div class="crm-login-card" role="region" aria-labelledby="otp-heading">
    <p class="crm-msg-lead" style="margin-bottom:0">Check your mail provider, spam folder, or try again. If it keeps happening, contact support.</p>
    <div class="crm-actions-row">
      <a class="crm-link-btn" href="<?php echo limo_otp_h($retry); ?>"><i class="ri-refresh-line" aria-hidden="true"></i> <?php echo limo_otp_h($retryLabel); ?></a>
    </div>
  </div>
  <p class="crm-login-foot"><a href="login.php">Staff sign-in</a></p>
    <?php
    limo_otp_print_hero(
        'One quick step keeps your workspace and customers safe.',
        [
            ['icon' => 'ri-mail-send-line', 'text' => 'We only email the address on your lead record'],
            ['icon' => 'ri-timer-line', 'text' => 'Codes expire so old links stay useless to strangers'],
            ['icon' => 'ri-customer-service-2-line', 'text' => 'Need help? Your team can resend from the welcome link'],
        ]
    );
    exit;
}

$gate = $_SESSION['limo_otp_gate'] ?? null;
$leadId = is_array($gate) && isset($gate['lead_id']) ? trim((string) $gate['lead_id']) : '';
$hintEmail = is_array($gate) && isset($gate['email']) ? (string) $gate['email'] : '';
$expiresAt = is_array($gate) ? ($gate['expires_at'] ?? null) : null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($leadId === '' || !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $leadId)) {
        $error = 'Session expired. Please open your welcome link again.';
    } else {
        $otp = preg_replace('/\D/', '', (string) ($_POST['otp'] ?? ''));
        if (strlen($otp) !== 6) {
            $error = 'Enter the 6-digit code from your email.';
        } else {
            $vr = otp_mail_server_verify_otp($leadId, $otp);
            if ($vr['ok']) {
                $response = userLogin([
                    'user_name' => $AUTO_LOGIN_USER,
                    'password1' => $AUTO_LOGIN_PASS,
                ]);
                $loggedIn = (($response['status'] ?? '') === 'success')
                    || (!empty($response['success']) && $response['success'] === true);

                if ($loggedIn) {
                    $otpGateSnap = is_array($_SESSION['limo_otp_gate'] ?? null)
                        ? $_SESSION['limo_otp_gate']
                        : [];
                    unset($_SESSION['limo_otp_gate']);
                    $_SESSION['user'] = $response['user'];
                    $uname = trim((string) ($_SESSION['user']['user_name'] ?? ''));
                    if ($uname === $AUTO_LOGIN_USER) {
                        $_SESSION['welcome_lead_context'] = [
                            'supabase_lead_id' => trim((string) ($otpGateSnap['lead_id'] ?? '')),
                            'lead_name'        => trim((string) ($otpGateSnap['lead_name'] ?? '')),
                            'lead_email'       => trim((string) ($otpGateSnap['email'] ?? '')),
                        ];
                    }
                    header('Location: leads.php');
                    exit;
                }

                $error = $response['message'] ?? 'Code verified, but CRM sign-in failed. Try staff sign-in below.';
            } else {
                $d = $vr['data'] ?? [];
                $error = isset($d['message']) ? (string) $d['message'] : '';
                if ($error === '') {
                    $error = $vr['curlErr'] !== ''
                        ? $vr['curlErr']
                        : (($vr['http'] >= 400) ? ('Request failed (' . $vr['http'] . ')') : 'Invalid or expired code.');
                }
            }
        }
    }
}

if ($leadId === '' || !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $leadId)) {
    limo_otp_print_shell_start('Session expired | LimoCRM');
    limo_otp_print_brand();
    ?>
  <p class="crm-login-eyebrow">Email verification</p>
  <h1 class="crm-login-title" id="exp-heading">This link session expired</h1>
  <p class="crm-msg-lead">Open your original welcome link from email or SMS to get a fresh code. Staff can use the button below.</p>
  <div class="crm-login-card" role="region" aria-labelledby="exp-heading">
    <div class="crm-actions-row">
      <a class="crm-link-btn" href="login.php"><i class="ri-login-circle-line" aria-hidden="true"></i> Staff sign-in</a>
    </div>
  </div>
  <p class="crm-login-foot">Leads: use the same welcome URL your team shared with you.</p>
    <?php
    limo_otp_print_hero(
        'Your secure path into LimoCRM.',
        [
            ['icon' => 'ri-link', 'text' => 'Each visit uses a unique link tied to your lead'],
            ['icon' => 'ri-shield-keyhole-line', 'text' => 'OTP proves you control the inbox we have on file'],
            ['icon' => 'ri-door-open-line', 'text' => 'After verification you land in the app, ready to work'],
        ]
    );
    exit;
}

$maskEmail = static function (string $e): string {
    $e = trim($e);
    if ($e === '' || strpos($e, '@') === false) {
        return 'your inbox';
    }
    [$loc, $dom] = explode('@', $e, 2);
    $ln = strlen($loc);
    if ($ln <= 1) {
        $lm = '•••';
    } elseif ($ln === 2) {
        $lm = substr($loc, 0, 1) . '•';
    } else {
        $lm = substr($loc, 0, 1) . str_repeat('•', $ln - 2) . substr($loc, -1);
    }

    return $lm . '@' . $dom;
};
$displayEmail = $maskEmail($hintEmail);

limo_otp_print_shell_start('Verify code | LimoCRM');
limo_otp_print_brand();
?>
  <div class="crm-otp-steps" aria-hidden="true">
    <span>Code sent</span><span class="dot" aria-hidden="true"></span><span class="current">Enter code</span><span class="dot" aria-hidden="true"></span><span>Continue</span>
  </div>
  <p class="crm-login-eyebrow">Almost there</p>
  <h1 class="crm-login-title" id="verify-heading">Check your email</h1>
  <p class="crm-login-sub">
    We sent a <strong>6-digit code</strong> to <strong><?php echo limo_otp_h($displayEmail); ?></strong>. Enter it here to open your workspace—no username or password needed for this step.
  </p>
  <?php if ($expiresAt): ?>
    <div class="crm-hint-strip">
      <span class="crm-hint-chip"><i class="ri-time-line" aria-hidden="true"></i> Expires <?php echo limo_otp_h((string) $expiresAt); ?></span>
      <?php if (!empty($gate['verify_url'])): ?>
        <span class="crm-hint-chip"><i class="ri-external-link-line" aria-hidden="true"></i> Or use the link in the email</span>
      <?php endif; ?>
    </div>
  <?php elseif (!empty($gate['verify_url'])): ?>
    <div class="crm-hint-strip">
      <span class="crm-hint-chip"><i class="ri-external-link-line" aria-hidden="true"></i> You can also tap the verification link we sent</span>
    </div>
  <?php endif; ?>

  <div class="crm-login-card">
    <form method="post" autocomplete="off" id="otp-form" novalidate>
      <div class="crm-field">
        <label class="crm-label" for="otp"><i class="ri-key-2-line" aria-hidden="true"></i> Verification code</label>
        <div class="crm-input-wrap">
          <span class="crm-input-icon" aria-hidden="true"><i class="ri-shield-check-line"></i></span>
          <input
            class="form-control crm-otp-input"
            type="text"
            name="otp"
            id="otp"
            inputmode="numeric"
            pattern="\d*"
            maxlength="6"
            placeholder="______"
            required
            autocomplete="one-time-code"
            autofocus
            aria-describedby="otp-hint"
          />
        </div>
        <p id="otp-hint" class="crm-login-foot" style="margin-top: 0.5rem; text-align: left;">
          Paste or type six numbers. Wrong code? Request a new one below.
        </p>
      </div>
      <?php if ($error !== ''): ?>
        <div class="crm-otp-err" role="alert"><i class="ri-error-warning-line" aria-hidden="true"></i><?php echo limo_otp_h($error); ?></div>
      <?php endif; ?>
      <button type="submit" class="crm-submit" id="otp-submit"><i class="ri-login-circle-line" aria-hidden="true"></i> Verify & continue</button>
    </form>
  </div>

  <div class="crm-actions-row">
    <a class="crm-link-btn" href="welcome.php?id=<?php echo rawurlencode($leadId); ?>">
      <i class="ri-mail-send-line" aria-hidden="true"></i> Send a new code
    </a>
  </div>

  <p class="crm-login-foot">
    <a href="login.php">Staff sign-in</a> — for teammates with a LimoCRM username and password.
  </p>
  <script>
    (function () {
      var otp = document.getElementById('otp');
      var form = document.getElementById('otp-form');
      var btn = document.getElementById('otp-submit');
      if (otp) {
        otp.addEventListener('input', function () {
          this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
        otp.focus();
      }
      if (form && btn) {
        form.addEventListener('submit', function () {
          btn.disabled = true;
          btn.innerHTML = '<span class="crm-otp-spin" aria-hidden="true"></span> Verifying…';
        });
      }
    })();
  </script>
<?php
limo_otp_print_hero(
    'Verify it’s you—then you’re in.',
    [
        ['icon' => 'ri-mail-line', 'text' => 'The code goes only to the email on your lead'],
        ['icon' => 'ri-shield-check-line', 'text' => 'Short-lived codes reduce risk if someone gets the link'],
        ['icon' => 'ri-dashboard-3-line', 'text' => 'Then we drop you straight into leads and your pipeline'],
    ]
);
