<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$userId = $_SESSION['user']['id'] ?? '';
$stripeData = null;
$paypalShow = null;
$stripeConnected = false;
$paypalConnected = false;
$preferredPayment = 'offline';
$storedPreferred = 'offline';
$preferenceMismatch = false;
$stripePubForm = '';
$paypalClientForm = '';

/**
 * SuiteCRM may return the full DB row inside `keys` (includes user_id, preferred_payment, etc.).
 * Canonical API returns nested keys + paypal + connected flags.
 */
function pm_flat_db_row(?array $keys): bool
{
    if (!is_array($keys)) {
        return false;
    }
    if (isset($keys['user_id'])) {
        return true;
    }
    // Raw row-shaped payload without user_id (some entry points omit it)
    return array_key_exists('paypal_client_id', $keys) && array_key_exists('preferred_payment', $keys);
}

function pm_mask_secret(string $sec): string
{
    if (strlen($sec) <= 12) {
        return str_repeat('*', strlen($sec));
    }
    return substr($sec, 0, 7) . str_repeat('*', max(0, strlen($sec) - 11)) . substr($sec, -4);
}

function pm_apply_payment_fetch(array $resp): array
{
    $defaults = [
        'stripe_connected'    => false,
        'paypal_connected'    => false,
        'stripe_data'         => null,
        'paypal_show'         => null,
        'preferred_stored'    => 'offline',
        'stripe_pub_form'     => '',
        'paypal_client_form'  => '',
    ];
    if (empty($resp['success'])) {
        return $defaults;
    }

    $keys = isset($resp['keys']) && is_array($resp['keys']) ? $resp['keys'] : null;

    if (pm_flat_db_row($keys)) {
        $pub = trim((string)($keys['stripe_publishable_key'] ?? ''));
        $sec = trim((string)($keys['stripe_secret_key'] ?? ''));
        $pc = trim((string)($keys['paypal_client_id'] ?? ''));
        $ps = trim((string)($keys['paypal_secret'] ?? ''));
        $stripeOk = ($pub !== '' && $sec !== '');
        $paypalOk = ($pc !== '' && $ps !== '');
        $pref = strtolower(trim((string)($keys['preferred_payment'] ?? ($resp['preferred_payment'] ?? 'offline'))));
        if (!in_array($pref, ['stripe', 'paypal', 'offline'], true)) {
            $pref = 'offline';
        }
        $stripeData = null;
        if ($stripeOk) {
            $stripeData = [
                'id'                     => (string)($keys['id'] ?? ''),
                'stripe_publishable_key' => $pub,
                'stripe_secret_key'      => pm_mask_secret($sec),
                'is_live'                => (int)($keys['is_live'] ?? 0),
                'connected_at'           => (string)($keys['connected_at'] ?? ''),
            ];
        }
        $paypalShow = null;
        if ($paypalOk) {
            $paypalShow = [
                'paypal_client_id' => $pc,
                'paypal_secret'    => pm_mask_secret($ps),
                'paypal_is_live'   => (int)($keys['paypal_is_live'] ?? 0),
            ];
        }

        return [
            'stripe_connected'   => $stripeOk,
            'paypal_connected'   => $paypalOk,
            'stripe_data'        => $stripeData,
            'paypal_show'        => $paypalShow,
            'preferred_stored'   => $pref,
            'stripe_pub_form'    => $pub,
            'paypal_client_form' => $pc,
        ];
    }

    $pref = strtolower(trim((string)($resp['preferred_payment'] ?? 'offline')));
    if (!in_array($pref, ['stripe', 'paypal', 'offline'], true)) {
        $pref = 'offline';
    }
    $stripeData = is_array($keys) ? $keys : null;
    $paypalShow = isset($resp['paypal']) && is_array($resp['paypal']) ? $resp['paypal'] : null;

    return [
        'stripe_connected'   => !empty($resp['connected']),
        'paypal_connected'   => !empty($resp['paypal_connected']),
        'stripe_data'        => $stripeData,
        'paypal_show'        => $paypalShow,
        'preferred_stored'   => $pref,
        'stripe_pub_form'    => is_array($stripeData) ? (string)($stripeData['stripe_publishable_key'] ?? '') : '',
        'paypal_client_form' => is_array($paypalShow) ? (string)($paypalShow['paypal_client_id'] ?? '') : '',
    ];
}

if ($userId !== '') {
    $resp = fetchUserStripeKeys(['user_id' => $userId]);
    if (empty($resp['success'])) {
        $resp = fetchPaymentMethods(['user_id' => $userId]);
    }
    $n = pm_apply_payment_fetch($resp);
    $stripeConnected = $n['stripe_connected'];
    $paypalConnected = $n['paypal_connected'];
    $stripeData = $n['stripe_data'];
    $paypalShow = $n['paypal_show'];
    $storedPreferred = $n['preferred_stored'];
    $stripePubForm = $n['stripe_pub_form'];
    $paypalClientForm = $n['paypal_client_form'];
    $preferredPayment = $storedPreferred;
    if ($storedPreferred === 'stripe' && !$stripeConnected) {
        $preferredPayment = 'offline';
        $preferenceMismatch = true;
    }
    if ($storedPreferred === 'paypal' && !$paypalConnected) {
        $preferredPayment = 'offline';
        $preferenceMismatch = true;
    }
}

function pm_e($v): string {
  return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<style>
/* ── Theme tokens ── */
.pm-page {
  --pm-bg: #f8fafc;
  --pm-surface: #ffffff;
  --pm-surface-2: #f1f5f9;
  --pm-border: rgba(15,23,42,.10);
  --pm-border-strong: rgba(15,23,42,.16);
  --pm-text: #0f172a;
  --pm-muted: rgba(15,23,42,.55);
  --pm-subtle: rgba(15,23,42,.04);
  /* max-width: 920px; */
  margin: 0 auto;
  /* padding: 32px 20px 72px; */
}
.dark .pm-page {
  --pm-bg: transparent;
  --pm-surface: rgba(255,255,255,.035);
  --pm-surface-2: rgba(255,255,255,.06);
  --pm-border: rgba(255,255,255,.08);
  --pm-border-strong: rgba(255,255,255,.14);
  --pm-text: rgba(255,255,255,.92);
  --pm-muted: rgba(255,255,255,.50);
  --pm-subtle: rgba(255,255,255,.03);
}

.pm-page-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  flex-wrap: wrap; gap: 12px; margin-bottom: 28px;
}
.pm-page-header h2 {
  font-size: 1.5rem; font-weight: 800; color: var(--pm-text); margin: 0 0 4px;
  letter-spacing: -.02em;
}
.pm-page-header p { color: var(--pm-muted); font-size: .88rem; margin: 0; line-height: 1.5; }

.pm-card {
  background: var(--pm-surface);
  border: 1px solid var(--pm-border);
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 20px;
  transition: border-color .25s, box-shadow .25s;
}
.pm-card:hover { border-color: var(--pm-border-strong); box-shadow: 0 4px 24px rgba(0,0,0,.05); }
.dark .pm-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,.25); }
.pm-card-header {
  padding: 18px 22px; border-bottom: 1px solid var(--pm-border);
  background: var(--pm-subtle);
  display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
}
.pm-card-body { padding: 24px 22px; }

/* Primary method selector */
.pm-method-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 12px;
}
@media (min-width: 640px) {
  .pm-method-grid { grid-template-columns: repeat(3, 1fr); }
}
.pm-opt {
  position: relative;
  display: block;
  padding: 16px 14px;
  border-radius: 14px;
  border: 2px solid var(--pm-border);
  background: var(--pm-surface-2);
  cursor: pointer;
  transition: border-color .2s, background .2s, box-shadow .2s;
}
.pm-opt:hover { border-color: var(--pm-border-strong); }
.pm-opt input {
  position: absolute; opacity: 0; width: 0; height: 0;
}
.pm-opt:has(input:checked) {
  border-color: rgb(var(--primary-rgb));
  background: rgba(var(--primary-rgb), .07);
  box-shadow: 0 0 0 1px rgba(var(--primary-rgb), .2);
}
.pm-opt-icon {
  width: 40px; height: 40px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px; margin-bottom: 10px;
}
.pm-opt-title { font-size: .95rem; font-weight: 800; color: var(--pm-text); margin: 0 0 4px; }
.pm-opt-desc { font-size: .78rem; color: var(--pm-muted); line-height: 1.45; margin: 0; }

.pm-stripe-icon {
  width: 48px; height: 48px; border-radius: 14px;
  background: #635BFF; display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.pm-stripe-icon svg { width: 26px; height: 26px; }
.pm-paypal-icon {
  width: 48px; height: 48px; border-radius: 14px;
  background: #003087; display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; color: #fff; font-size: 22px;
}

.pm-brand-info h3 {
  margin: 0 0 2px; font-size: 1.05rem; font-weight: 700; color: var(--pm-text);
}
.pm-brand-info p { margin: 0; font-size: .82rem; color: var(--pm-muted); }

.pm-badge {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 14px; border-radius: 20px; font-size: .78rem; font-weight: 700;
  letter-spacing: .02em; text-transform: uppercase;
}
.pm-badge-ok { background: rgba(16,185,129,.12); color: #059669; }
.dark .pm-badge-ok { background: rgba(16,185,129,.18); color: #34d399; }
.pm-badge-off { background: rgba(239,68,68,.08); color: #dc2626; }
.dark .pm-badge-off { background: rgba(239,68,68,.14); color: #f87171; }
.pm-badge-warn { background: rgba(245,158,11,.12); color: #d97706; }

.pm-details {
  display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
  margin-top: 16px;
}
@media (max-width: 580px) { .pm-details { grid-template-columns: 1fr; } }
.pm-detail-item {
  padding: 14px 16px; border-radius: 12px;
  background: var(--pm-surface-2); border: 1px solid var(--pm-border);
}
.pm-detail-label {
  display: block; font-size: 10px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .08em; color: var(--pm-muted); margin-bottom: 6px;
}
.pm-detail-val {
  font-size: .88rem; font-weight: 600; color: var(--pm-text);
  word-break: break-all; line-height: 1.45;
  font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
}

.pm-sep { border: none; border-top: 1px solid var(--pm-border); margin: 22px 0; }

.pm-form-row { margin-bottom: 18px; }
.pm-form-row label {
  display: block; font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .06em; color: var(--pm-muted); margin-bottom: 6px;
}
.pm-input {
  width: 100%; height: 46px; border-radius: 12px;
  border: 1px solid var(--pm-border); background: var(--pm-surface-2);
  color: var(--pm-text); padding: 0 16px; font-size: .9rem; outline: none;
  transition: border-color .2s, box-shadow .2s;
}
.pm-input:focus {
  border-color: rgb(var(--primary-rgb));
  box-shadow: 0 0 0 3px rgba(var(--primary-rgb),.12);
}
.pm-input::placeholder { color: var(--pm-muted); opacity: .7; }
.pm-hint { font-size: .76rem; color: var(--pm-muted); margin-top: 5px; }
.pm-hint code {
  background: var(--pm-surface-2); padding: 1px 5px; border-radius: 4px;
  font-size: .74rem; border: 1px solid var(--pm-border);
}

.pm-toggle-row {
  display: flex; align-items: center; gap: 14px; margin-bottom: 22px;
  padding: 14px 16px; border-radius: 12px; background: var(--pm-surface-2);
  border: 1px solid var(--pm-border);
}
.pm-toggle-row .pm-toggle-text { flex: 1; }
.pm-toggle-row .pm-toggle-text strong { font-size: .88rem; font-weight: 700; color: var(--pm-text); display: block; }
.pm-toggle-row .pm-toggle-text span { font-size: .78rem; color: var(--pm-muted); }
.pm-switch { position: relative; width: 48px; height: 26px; cursor: pointer; flex-shrink: 0; }
.pm-switch input { opacity: 0; width: 0; height: 0; }
.pm-switch .slider {
  position: absolute; inset: 0; background: var(--pm-border-strong);
  border-radius: 26px; transition: .3s;
}
.pm-switch .slider::before {
  content: ""; position: absolute; width: 20px; height: 20px; left: 3px; top: 3px;
  background: #fff; border-radius: 50%; transition: .3s;
  box-shadow: 0 1px 4px rgba(0,0,0,.15);
}
.pm-switch input:checked + .slider { background: rgb(var(--primary-rgb)); }
.pm-switch input:checked + .slider::before { transform: translateX(22px); }

.pm-btn-row { display: flex; gap: 10px; flex-wrap: wrap; }
.pm-btn {
  height: 44px; border-radius: 12px; border: none; font-size: .88rem;
  font-weight: 700; padding: 0 22px; cursor: pointer; display: inline-flex;
  align-items: center; gap: 8px; transition: all .2s;
}
.pm-btn-primary {
  background: rgb(var(--primary-rgb)); color: #fff;
}
.pm-btn-primary:hover:not(:disabled) {
  filter: brightness(1.06);
  box-shadow: 0 4px 18px rgba(var(--primary-rgb),.3);
}
.pm-btn-primary:disabled { opacity: .55; cursor: not-allowed; }
.pm-btn-secondary {
  background: var(--pm-surface-2); color: var(--pm-text);
  border: 1px solid var(--pm-border);
}
.pm-btn-secondary:hover:not(:disabled) {
  border-color: rgb(var(--primary-rgb));
  color: rgb(var(--primary-rgb));
}
.pm-btn-danger {
  background: rgba(239,68,68,.08); color: #dc2626;
  border: 1px solid rgba(239,68,68,.18);
}
.dark .pm-btn-danger { background: rgba(239,68,68,.12); color: #f87171; border-color: rgba(239,68,68,.22); }
.pm-btn-danger:hover:not(:disabled) { background: rgba(239,68,68,.16); }
.pm-btn-danger:disabled { opacity: .55; cursor: not-allowed; }

.pm-guide {
  border-left: 4px solid rgb(var(--primary-rgb));
}
.pm-guide-title {
  margin: 0 0 14px; font-size: 1rem; font-weight: 700; color: var(--pm-text);
  display: flex; align-items: center; gap: 10px;
}
.pm-guide-title i {
  width: 32px; height: 32px; border-radius: 10px;
  background: rgba(var(--primary-rgb),.1); color: rgb(var(--primary-rgb));
  display: flex; align-items: center; justify-content: center; font-size: 16px;
}
.pm-guide ol {
  margin: 0; padding-left: 20px; color: var(--pm-muted); font-size: .86rem; line-height: 1.8;
}
.pm-guide ol li::marker { color: rgb(var(--primary-rgb)); font-weight: 700; }
.pm-guide a { color: rgb(var(--primary-rgb)); font-weight: 600; text-decoration: none; }
.pm-guide a:hover { text-decoration: underline; }
.pm-guide strong { color: var(--pm-text); }

.pm-security-note {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 14px 16px; border-radius: 12px; margin-top: 20px;
  background: rgba(16,185,129,.06); border: 1px solid rgba(16,185,129,.14);
  font-size: .82rem; color: var(--pm-muted); line-height: 1.55;
}
.dark .pm-security-note { background: rgba(16,185,129,.08); border-color: rgba(16,185,129,.18); }
.pm-security-note i { font-size: 18px; color: #059669; flex-shrink: 0; margin-top: 1px; }
.dark .pm-security-note i { color: #34d399; }

.pm-active-hint {
  font-size: .8rem; color: var(--pm-muted); margin-top: 14px; padding: 12px 14px;
  border-radius: 10px; background: var(--pm-surface-2); border: 1px dashed var(--pm-border);
}
.pm-active-hint strong { color: var(--pm-text); }

.pm-banner-warn {
  display: flex; align-items: flex-start; gap: 10px;
  padding: 12px 14px; border-radius: 12px; margin-bottom: 16px;
  background: rgba(245, 158, 11, 0.10); border: 1px solid rgba(245, 158, 11, 0.22);
  font-size: .82rem; color: var(--pm-text); line-height: 1.45;
}
.pm-banner-warn i { flex-shrink: 0; margin-top: 2px; color: #d97706; font-size: 18px; }
.dark .pm-banner-warn {
  background: rgba(245, 158, 11, 0.12); border-color: rgba(245, 158, 11, 0.28); color: var(--pm-text);
}
</style>

<div class="main-content app-content">
  <div class="container-fluid">
    <div class="pm-page">

      <div class="pm-page-header">
        <div>
          <h2>Payment Methods</h2>
          <p>Choose how clients pay (cards with Stripe, PayPal, or offline). Configure each provider below.</p>
        </div>
      </div>

      <!-- Checkout method -->
      <div class="pm-card">
        <div class="pm-card-header">
          <div class="pm-brand-info" style="flex:1;min-width:200px;">
            <h3>Default checkout method</h3>
            <p>Used for agreements and payment flows. <strong>Offline</strong> is selected until you change it.</p>
          </div>
          <span class="pm-badge <?php echo $preferredPayment === 'offline' ? 'pm-badge-warn' : 'pm-badge-ok'; ?>">
            <i class="ri-focus-3-line"></i> <?php echo pm_e(ucfirst($preferredPayment)); ?>
          </span>
        </div>
        <div class="pm-card-body">
          <?php if ($preferenceMismatch): ?>
          <div class="pm-banner-warn">
            <i class="ri-error-warning-fill"></i>
            <div>
              Checkout was saved as <strong><?php echo pm_e(ucfirst($storedPreferred)); ?></strong>, but <?php echo $storedPreferred === 'stripe' ? 'Stripe keys are missing' : 'PayPal credentials are incomplete'; ?>.
              <strong>Offline</strong> is selected until you fix that and save again.
            </div>
          </div>
          <?php endif; ?>
          <div class="pm-method-grid" id="pm-method-grid">
            <label class="pm-opt">
              <input type="radio" name="pm_primary" value="offline" <?php echo $preferredPayment === 'offline' ? 'checked' : ''; ?>>
              <div class="pm-opt-icon bg-secondary/15 text-secondary"><i class="ri-bank-card-line"></i></div>
              <div class="pm-opt-title">Offline</div>
              <p class="pm-opt-desc">Cash, check, invoice, or pay outside the CRM. No online keys required.</p>
            </label>
            <label class="pm-opt">
              <input type="radio" name="pm_primary" value="stripe" <?php echo $preferredPayment === 'stripe' ? 'checked' : ''; ?>>
              <div class="pm-opt-icon" style="background:#635BFF22;color:#635BFF;"><i class="ri-secure-payment-line"></i></div>
              <div class="pm-opt-title">Stripe</div>
              <p class="pm-opt-desc">Cards on file. Add your Stripe API keys in the section below.</p>
            </label>
            <label class="pm-opt">
              <input type="radio" name="pm_primary" value="paypal" <?php echo $preferredPayment === 'paypal' ? 'checked' : ''; ?>>
              <div class="pm-opt-icon" style="background:#00308722;color:#003087;"><i class="ri-paypal-fill"></i></div>
              <div class="pm-opt-title">PayPal</div>
              <p class="pm-opt-desc">PayPal checkout. Add REST app credentials below.</p>
            </label>
          </div>
          <div class="pm-btn-row" style="margin-top:18px;">
            <button type="button" class="pm-btn pm-btn-primary" id="pm-save-pref-btn">
              <i class="ri-save-3-line"></i> Save checkout method
            </button>
          </div>
          <div class="pm-active-hint" id="pm-pref-hint">
            <strong>Tip:</strong> You must save valid Stripe or PayPal credentials <em>before</em> you can set that option as the active checkout method. Offline is always available.
          </div>
        </div>
      </div>

      <!-- Stripe -->
      <div class="pm-card">
        <div class="pm-card-header">
          <div class="pm-stripe-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M11.2 10.47c0-.59.48-.82 1.28-.82 1.14 0 2.59.35 3.73.97V6.99a9.9 9.9 0 00-3.73-.68c-3.04 0-5.07 1.59-5.07 4.25 0 4.15 5.71 3.49 5.71 5.28 0 .7-.6.92-1.45.92-1.26 0-2.86-.52-4.13-1.21v3.52c1.41.6 2.83.87 4.13.87 3.12 0 5.26-1.54 5.26-4.24-.01-4.48-5.73-3.68-5.73-5.38z" fill="#fff"/>
            </svg>
          </div>
          <div class="pm-brand-info">
            <h3>Stripe</h3>
            <p>Credit &amp; debit cards via Stripe</p>
          </div>
          <div style="margin-left:auto;">
            <?php if ($stripeConnected): ?>
              <span class="pm-badge pm-badge-ok"><i class="ri-checkbox-circle-fill"></i> Keys saved</span>
            <?php else: ?>
              <span class="pm-badge pm-badge-off"><i class="ri-close-circle-fill"></i> Not connected</span>
            <?php endif; ?>
          </div>
        </div>
        <div class="pm-card-body">
          <?php if ($stripeConnected && $stripeData): ?>
          <div class="pm-details">
            <div class="pm-detail-item">
              <span class="pm-detail-label">Publishable Key</span>
              <span class="pm-detail-val"><?php echo pm_e($stripeData['stripe_publishable_key']); ?></span>
            </div>
            <div class="pm-detail-item">
              <span class="pm-detail-label">Secret Key</span>
              <span class="pm-detail-val"><?php echo pm_e($stripeData['stripe_secret_key']); ?></span>
            </div>
            <div class="pm-detail-item">
              <span class="pm-detail-label">Mode</span>
              <span class="pm-detail-val" style="font-family:inherit;">
                <?php if (!empty($stripeData['is_live'])): ?>
                  <span style="color:#059669;"><i class="ri-shield-check-line"></i> Live</span>
                <?php else: ?>
                  <span style="color:#d97706;"><i class="ri-flask-line"></i> Test</span>
                <?php endif; ?>
              </span>
            </div>
            <div class="pm-detail-item">
              <span class="pm-detail-label">Connected Since</span>
              <span class="pm-detail-val" style="font-family:inherit;"><?php echo pm_e($stripeData['connected_at'] ?? '—'); ?></span>
            </div>
          </div>
          <?php endif; ?>

          <hr class="pm-sep">

          <form id="pm-stripe-form" autocomplete="off">
            <div class="pm-form-row">
              <label for="pm-pub-key">Publishable Key</label>
              <input type="text" id="pm-pub-key" class="pm-input" placeholder="pk_test_51Pk..." value="<?php echo $stripeConnected && $stripeData ? pm_e($stripeData['stripe_publishable_key']) : pm_e($stripePubForm); ?>">
              <p class="pm-hint">Starts with <code>pk_test_</code> or <code>pk_live_</code></p>
            </div>
            <div class="pm-form-row">
              <label for="pm-sec-key">Secret Key</label>
              <input type="password" id="pm-sec-key" class="pm-input" placeholder="sk_test_51Pk...">
              <p class="pm-hint">Starts with <code>sk_test_</code> or <code>sk_live_</code> — leave blank only when updating other fields (not supported); always paste full secret when (re)connecting.</p>
            </div>
            <div class="pm-toggle-row">
              <div class="pm-toggle-text">
                <strong>Stripe live mode</strong>
                <span>Real charges (use test keys until you are ready)</span>
              </div>
              <label class="pm-switch">
                <input type="checkbox" id="pm-live-toggle" <?php echo ($stripeConnected && !empty($stripeData['is_live'])) ? 'checked' : ''; ?>>
                <span class="slider"></span>
              </label>
            </div>
            <div class="pm-btn-row">
              <button type="submit" class="pm-btn pm-btn-primary" id="pm-save-stripe-btn">
                <i class="ri-link"></i> <?php echo $stripeConnected ? 'Update Stripe keys' : 'Connect Stripe'; ?>
              </button>
              <?php if ($stripeConnected): ?>
              <button type="button" class="pm-btn pm-btn-danger" id="pm-disconnect-stripe-btn">
                <i class="ri-link-unlink-m"></i> Remove Stripe keys
              </button>
              <?php endif; ?>
            </div>
          </form>
          <div class="pm-security-note">
            <i class="ri-shield-keyhole-line"></i>
            <div>Keys are sent over HTTPS and stored on the server. The Stripe secret is masked in the UI after save.</div>
          </div>
        </div>
      </div>

      <!-- PayPal -->
      <div class="pm-card">
        <div class="pm-card-header">
          <div class="pm-paypal-icon"><i class="ri-paypal-fill"></i></div>
          <div class="pm-brand-info">
            <h3>PayPal</h3>
            <p>REST API credentials from the PayPal Developer Dashboard</p>
          </div>
          <div style="margin-left:auto;">
            <?php if ($paypalConnected): ?>
              <span class="pm-badge pm-badge-ok"><i class="ri-checkbox-circle-fill"></i> Credentials saved</span>
            <?php else: ?>
              <span class="pm-badge pm-badge-off"><i class="ri-close-circle-fill"></i> Not connected</span>
            <?php endif; ?>
          </div>
        </div>
        <div class="pm-card-body">
          <?php if ($paypalConnected && $paypalShow): ?>
          <div class="pm-details">
            <div class="pm-detail-item">
              <span class="pm-detail-label">Client ID</span>
              <span class="pm-detail-val" style="word-break:break-all;"><?php echo pm_e($paypalShow['paypal_client_id'] ?? ''); ?></span>
            </div>
            <div class="pm-detail-item">
              <span class="pm-detail-label">Secret</span>
              <span class="pm-detail-val"><?php echo pm_e($paypalShow['paypal_secret'] ?? ''); ?></span>
            </div>
            <div class="pm-detail-item">
              <span class="pm-detail-label">Mode</span>
              <span class="pm-detail-val" style="font-family:inherit;">
                <?php if (!empty($paypalShow['paypal_is_live'])): ?>
                  <span style="color:#059669;"><i class="ri-shield-check-line"></i> Live</span>
                <?php else: ?>
                  <span style="color:#d97706;"><i class="ri-flask-line"></i> Sandbox</span>
                <?php endif; ?>
              </span>
            </div>
          </div>
          <hr class="pm-sep">
          <?php endif; ?>

          <form id="pm-paypal-form" autocomplete="off">
            <div class="pm-form-row">
              <label for="pm-paypal-client">Client ID</label>
              <input type="text" id="pm-paypal-client" class="pm-input" placeholder="PayPal application client ID" value="<?php echo ($paypalConnected && $paypalShow) ? pm_e($paypalShow['paypal_client_id'] ?? '') : pm_e($paypalClientForm); ?>">
            </div>
            <div class="pm-form-row">
              <label for="pm-paypal-secret">Secret</label>
              <input type="password" id="pm-paypal-secret" class="pm-input" placeholder="Application secret">
              <p class="pm-hint">Paste the full secret when connecting or rotating credentials.</p>
            </div>
            <div class="pm-toggle-row">
              <div class="pm-toggle-text">
                <strong>PayPal live mode</strong>
                <span>Use Live credentials for production; Sandbox for testing</span>
              </div>
              <label class="pm-switch">
                <input type="checkbox" id="pm-paypal-live-toggle" <?php echo ($paypalConnected && !empty($paypalShow['paypal_is_live'])) ? 'checked' : ''; ?>>
                <span class="slider"></span>
              </label>
            </div>
            <div class="pm-btn-row">
              <button type="submit" class="pm-btn pm-btn-primary" id="pm-save-paypal-btn">
                <i class="ri-link"></i> <?php echo $paypalConnected ? 'Update PayPal' : 'Save PayPal credentials'; ?>
              </button>
              <?php if ($paypalConnected): ?>
              <button type="button" class="pm-btn pm-btn-danger" id="pm-disconnect-paypal-btn">
                <i class="ri-link-unlink-m"></i> Remove PayPal
              </button>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>

      <!-- Guides -->
      <div class="pm-card pm-guide">
        <div class="pm-card-body">
          <div class="pm-guide-title">
            <i class="ri-question-line"></i> Stripe API keys
          </div>
          <ol>
            <li>Open the <a href="https://dashboard.stripe.com" target="_blank" rel="noopener">Stripe Dashboard</a>.</li>
            <li>Go to <strong>Developers</strong> → <strong>API keys</strong>.</li>
            <li>Copy the publishable and secret keys, paste above, and save.</li>
          </ol>
          <hr class="pm-sep" style="margin:20px 0;">
          <div class="pm-guide-title">
            <i class="ri-paypal-fill"></i> PayPal REST app
          </div>
          <ol>
            <li>Open <a href="https://developer.paypal.com/dashboard/" target="_blank" rel="noopener">PayPal Developer Dashboard</a>.</li>
            <li>Create or select an app under <strong>Apps &amp; Credentials</strong>.</li>
            <li>Copy <strong>Client ID</strong> and <strong>Secret</strong> (Sandbox or Live) into the PayPal section above.</li>
          </ol>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script>
(function () {
  var ENTRY = <?php echo json_encode('https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint'); ?>;
  var USER_ID = <?php echo json_encode($userId); ?>;
  var PM_STRIPE_READY = <?php echo $stripeConnected ? 'true' : 'false'; ?>;
  var PM_PAYPAL_READY = <?php echo $paypalConnected ? 'true' : 'false'; ?>;

  function parse(resp) {
    if (typeof resp === 'string') { try { resp = JSON.parse(resp); } catch (e) { resp = {}; } }
    return resp || {};
  }

  function prefVal() {
    var $c = $('input[name="pm_primary"]:checked');
    return $c.length ? $c.val() : 'offline';
  }

  $('#pm-save-pref-btn').on('click', function () {
    var method = prefVal();
    if (method === 'stripe' && !PM_STRIPE_READY) {
      Swal.fire({
        icon: 'warning',
        title: 'Stripe not ready',
        text: 'Add and save your Stripe publishable and secret keys below, then choose Stripe as the checkout method.'
      });
      return;
    }
    if (method === 'paypal' && !PM_PAYPAL_READY) {
      Swal.fire({
        icon: 'warning',
        title: 'PayPal not ready',
        text: 'Add and save your PayPal Client ID and Secret below, then choose PayPal as the checkout method.'
      });
      return;
    }
    var $btn = $(this).prop('disabled', true);
    $.post(ENTRY, {
      action: 'save_user_payment_preference',
      user_id: USER_ID,
      preferred_payment: prefVal()
    }).done(function (resp) {
      var data = parse(resp);
      if (data.success) {
        Swal.fire({ icon: 'success', title: 'Saved', text: data.message || 'Checkout method updated.', timer: 1600, showConfirmButton: false })
          .then(function () { location.reload(); });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Could not save preference.' });
      }
    }).fail(function () {
      Swal.fire({ icon: 'error', title: 'Network error', text: 'Could not reach the server.' });
    }).always(function () { $btn.prop('disabled', false); });
  });

  $('#pm-stripe-form').on('submit', function (e) {
    e.preventDefault();
    var pubKey = $.trim($('#pm-pub-key').val());
    var secKey = $.trim($('#pm-sec-key').val());
    var isLive = $('#pm-live-toggle').is(':checked') ? 1 : 0;

    if (!pubKey) { Swal.fire({ icon: 'warning', title: 'Missing', text: 'Publishable key is required.' }); return; }
    if (!secKey) { Swal.fire({ icon: 'warning', title: 'Missing', text: 'Secret key is required.' }); return; }

    var $btn = $('#pm-save-stripe-btn').prop('disabled', true);

    $.post(ENTRY, {
      action: 'save_user_stripe_keys',
      user_id: USER_ID,
      stripe_publishable_key: pubKey,
      stripe_secret_key: secKey,
      is_live: isLive,
      preferred_payment: 'stripe'
    }).done(function (resp) {
      var data = parse(resp);
      if (data.success) {
        Swal.fire({ icon: 'success', title: 'Saved', text: data.message || 'Stripe keys saved.', timer: 1600, showConfirmButton: false }).then(function () { location.reload(); });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Could not save keys.' });
      }
    }).fail(function () {
      Swal.fire({ icon: 'error', title: 'Network error', text: 'Could not reach the server.' });
    }).always(function () { $btn.prop('disabled', false); });
  });

  $('#pm-disconnect-stripe-btn').on('click', function () {
    Swal.fire({
      title: 'Remove Stripe keys?',
      text: 'Card payments via Stripe will stop until you reconnect. PayPal / offline settings are kept.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc2626',
      confirmButtonText: 'Yes, remove keys'
    }).then(function (result) {
      if (!result.isConfirmed) return;
      $.post(ENTRY, { action: 'delete_user_stripe_keys', user_id: USER_ID }).done(function (resp) {
        var data = parse(resp);
        if (data.success) {
          Swal.fire({ icon: 'success', title: 'Removed', text: data.message, timer: 1600, showConfirmButton: false }).then(function () { location.reload(); });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Could not remove Stripe keys.' });
        }
      }).fail(function () {
        Swal.fire({ icon: 'error', title: 'Network error', text: 'Could not reach the server.' });
      });
    });
  });

  $('#pm-paypal-form').on('submit', function (e) {
    e.preventDefault();
    var clientId = $.trim($('#pm-paypal-client').val());
    var secret = $.trim($('#pm-paypal-secret').val());
    var pLive = $('#pm-paypal-live-toggle').is(':checked') ? 1 : 0;

    if (!clientId) { Swal.fire({ icon: 'warning', title: 'Missing', text: 'PayPal Client ID is required.' }); return; }
    if (!secret) { Swal.fire({ icon: 'warning', title: 'Missing', text: 'PayPal Secret is required.' }); return; }

    var $btn = $('#pm-save-paypal-btn').prop('disabled', true);
    $.post(ENTRY, {
      action: 'save_user_paypal_keys',
      user_id: USER_ID,
      paypal_client_id: clientId,
      paypal_secret: secret,
      paypal_is_live: pLive,
      preferred_payment: 'paypal'
    }).done(function (resp) {
      var data = parse(resp);
      if (data.success) {
        Swal.fire({ icon: 'success', title: 'Saved', text: data.message || 'PayPal saved.', timer: 1600, showConfirmButton: false }).then(function () { location.reload(); });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Could not save PayPal credentials.' });
      }
    }).fail(function () {
      Swal.fire({ icon: 'error', title: 'Network error', text: 'Could not reach the server.' });
    }).always(function () { $btn.prop('disabled', false); });
  });

  $('#pm-disconnect-paypal-btn').on('click', function () {
    Swal.fire({
      title: 'Remove PayPal?',
      text: 'PayPal credentials will be cleared from your account.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc2626',
      confirmButtonText: 'Yes, remove'
    }).then(function (result) {
      if (!result.isConfirmed) return;
      $.post(ENTRY, { action: 'delete_user_paypal_keys', user_id: USER_ID }).done(function (resp) {
        var data = parse(resp);
        if (data.success) {
          Swal.fire({ icon: 'success', title: 'Removed', text: data.message, timer: 1600, showConfirmButton: false }).then(function () { location.reload(); });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Could not remove PayPal.' });
        }
      }).fail(function () {
        Swal.fire({ icon: 'error', title: 'Network error', text: 'Could not reach the server.' });
      });
    });
  });
})();
</script>
