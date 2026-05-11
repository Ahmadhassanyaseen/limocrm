<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$userId = $_SESSION['user']['id'] ?? '';
$stripeData = null;
$connected = false;
if ($userId !== '') {
  $resp = fetchUserStripeKeys(['user_id' => $userId]);
  if (!empty($resp['success'])) {
    $connected = !empty($resp['connected']);
    $stripeData = $resp['keys'] ?? null;
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
  max-width: 860px;
  margin: 0 auto;
  padding: 32px 20px 72px;
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

/* ── Header ── */
.pm-page-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  flex-wrap: wrap; gap: 12px; margin-bottom: 32px;
}
.pm-page-header h2 {
  font-size: 1.5rem; font-weight: 800; color: var(--pm-text); margin: 0 0 4px;
  letter-spacing: -.02em;
}
.pm-page-header p { color: var(--pm-muted); font-size: .88rem; margin: 0; line-height: 1.5; }

/* ── Card ── */
.pm-card {
  background: var(--pm-surface);
  border: 1px solid var(--pm-border);
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 24px;
  transition: border-color .25s, box-shadow .25s;
}
.pm-card:hover { border-color: var(--pm-border-strong); box-shadow: 0 4px 24px rgba(0,0,0,.05); }
.dark .pm-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,.25); }
.pm-card-header {
  padding: 20px 24px; border-bottom: 1px solid var(--pm-border);
  background: var(--pm-subtle);
  display: flex; align-items: center; gap: 14px;
}
.pm-card-body { padding: 28px 24px; }

/* ── Stripe brand icon ── */
.pm-stripe-icon {
  width: 48px; height: 48px; border-radius: 14px;
  background: #635BFF; display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.pm-stripe-icon svg { width: 26px; height: 26px; }

.pm-brand-info h3 {
  margin: 0 0 2px; font-size: 1.1rem; font-weight: 700; color: var(--pm-text);
}
.pm-brand-info p { margin: 0; font-size: .82rem; color: var(--pm-muted); }

/* ── Status badges ── */
.pm-badge {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 14px; border-radius: 20px; font-size: .78rem; font-weight: 700;
  letter-spacing: .02em; text-transform: uppercase;
}
.pm-badge-ok { background: rgba(16,185,129,.12); color: #059669; }
.dark .pm-badge-ok { background: rgba(16,185,129,.18); color: #34d399; }
.pm-badge-off { background: rgba(239,68,68,.08); color: #dc2626; }
.dark .pm-badge-off { background: rgba(239,68,68,.14); color: #f87171; }

/* ── Key detail grid ── */
.pm-details {
  display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
  margin-top: 20px;
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

/* ── Separator ── */
.pm-sep { border: none; border-top: 1px solid var(--pm-border); margin: 24px 0; }

/* ── Form ── */
.pm-form-row { margin-bottom: 20px; }
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

/* ── Toggle switch ── */
.pm-toggle-row {
  display: flex; align-items: center; gap: 14px; margin-bottom: 28px;
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

/* ── Buttons ── */
.pm-btn-row { display: flex; gap: 10px; flex-wrap: wrap; }
.pm-btn {
  height: 44px; border-radius: 12px; border: none; font-size: .88rem;
  font-weight: 700; padding: 0 24px; cursor: pointer; display: inline-flex;
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
.pm-btn-danger {
  background: rgba(239,68,68,.08); color: #dc2626;
  border: 1px solid rgba(239,68,68,.18);
}
.dark .pm-btn-danger { background: rgba(239,68,68,.12); color: #f87171; border-color: rgba(239,68,68,.22); }
.pm-btn-danger:hover:not(:disabled) { background: rgba(239,68,68,.16); }
.pm-btn-danger:disabled { opacity: .55; cursor: not-allowed; }

/* ── Guide card ── */
.pm-guide {
  border-left: 4px solid rgb(var(--primary-rgb));
}
.pm-guide-title {
  margin: 0 0 16px; font-size: 1rem; font-weight: 700; color: var(--pm-text);
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
.pm-guide em { color: var(--pm-text); font-style: normal; font-weight: 600; }

/* ── Security note ── */
.pm-security-note {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 14px 16px; border-radius: 12px; margin-top: 24px;
  background: rgba(16,185,129,.06); border: 1px solid rgba(16,185,129,.14);
  font-size: .82rem; color: var(--pm-muted); line-height: 1.55;
}
.dark .pm-security-note { background: rgba(16,185,129,.08); border-color: rgba(16,185,129,.18); }
.pm-security-note i { font-size: 18px; color: #059669; flex-shrink: 0; margin-top: 1px; }
.dark .pm-security-note i { color: #34d399; }
</style>

<div class="main-content app-content">
  <div class="container-fluid">
    <div class="pm-page">

      <!-- Page header -->
      <div class="pm-page-header">
        <div>
          <h2>Payment Methods</h2>
          <p>Connect your Stripe account to process payments through the CRM.</p>
        </div>
      </div>

      <!-- ─── Stripe Connection Card ─── -->
      <div class="pm-card">
        <div class="pm-card-header">
          <div class="pm-stripe-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M11.2 10.47c0-.59.48-.82 1.28-.82 1.14 0 2.59.35 3.73.97V6.99a9.9 9.9 0 00-3.73-.68c-3.04 0-5.07 1.59-5.07 4.25 0 4.15 5.71 3.49 5.71 5.28 0 .7-.6.92-1.45.92-1.26 0-2.86-.52-4.13-1.21v3.52c1.41.6 2.83.87 4.13.87 3.12 0 5.26-1.54 5.26-4.24-.01-4.48-5.73-3.68-5.73-5.38z" fill="#fff"/>
            </svg>
          </div>
          <div class="pm-brand-info">
            <h3>Stripe Integration</h3>
            <p>Accept credit &amp; debit card payments securely</p>
          </div>
          <div style="margin-left:auto;">
            <?php if ($connected): ?>
              <span class="pm-badge pm-badge-ok"><i class="ri-checkbox-circle-fill"></i> Connected</span>
            <?php else: ?>
              <span class="pm-badge pm-badge-off"><i class="ri-close-circle-fill"></i> Not Connected</span>
            <?php endif; ?>
          </div>
        </div>

        <div class="pm-card-body">

          <?php if ($connected && $stripeData): ?>
          <!-- Current connection details -->
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
                <?php if ($stripeData['is_live']): ?>
                  <span style="color:#059669;"><i class="ri-shield-check-line"></i> Live</span>
                <?php else: ?>
                  <span style="color:#d97706;"><i class="ri-flask-line"></i> Test</span>
                <?php endif; ?>
              </span>
            </div>
            <div class="pm-detail-item">
              <span class="pm-detail-label">Connected Since</span>
              <span class="pm-detail-val" style="font-family:inherit;"><?php echo pm_e($stripeData['connected_at']); ?></span>
            </div>
          </div>
          <?php endif; ?>

          <hr class="pm-sep">

          <!-- Form -->
          <form id="pm-stripe-form" autocomplete="off">
            <div class="pm-form-row">
              <label for="pm-pub-key">Publishable Key</label>
              <input type="text" id="pm-pub-key" class="pm-input" placeholder="pk_test_51Pk..." value="<?php echo $connected ? pm_e($stripeData['stripe_publishable_key']) : ''; ?>">
              <p class="pm-hint">Starts with <code>pk_test_</code> or <code>pk_live_</code></p>
            </div>
            <div class="pm-form-row">
              <label for="pm-sec-key">Secret Key</label>
              <input type="password" id="pm-sec-key" class="pm-input" placeholder="sk_test_51Pk...">
              <p class="pm-hint">Starts with <code>sk_test_</code> or <code>sk_live_</code> &mdash; never shared publicly</p>
            </div>

            <div class="pm-toggle-row">
              <div class="pm-toggle-text">
                <strong>Live Mode</strong>
                <span>Enable when ready to process real payments</span>
              </div>
              <label class="pm-switch">
                <input type="checkbox" id="pm-live-toggle" <?php echo ($connected && !empty($stripeData['is_live'])) ? 'checked' : ''; ?>>
                <span class="slider"></span>
              </label>
            </div>

            <div class="pm-btn-row">
              <button type="submit" class="pm-btn pm-btn-primary" id="pm-save-btn">
                <i class="ri-link"></i> <?php echo $connected ? 'Update Keys' : 'Connect Stripe'; ?>
              </button>
              <?php if ($connected): ?>
              <button type="button" class="pm-btn pm-btn-danger" id="pm-disconnect-btn">
                <i class="ri-link-unlink-m"></i> Disconnect
              </button>
              <?php endif; ?>
            </div>
          </form>

          <div class="pm-security-note">
            <i class="ri-shield-keyhole-line"></i>
            <div>Your API keys are transmitted securely and stored server-side. The secret key is masked after saving and never exposed to the browser again.</div>
          </div>

        </div>
      </div>

      <!-- ─── How-to Guide Card ─── -->
      <div class="pm-card pm-guide">
        <div class="pm-card-body">
          <div class="pm-guide-title">
            <i class="ri-question-line"></i> How to get your Stripe API keys
          </div>
          <ol>
            <li>Log in to your <a href="https://dashboard.stripe.com" target="_blank" rel="noopener">Stripe Dashboard</a>.</li>
            <li>Navigate to <strong>Developers</strong> &rarr; <strong>API keys</strong>.</li>
            <li>Copy your <em>Publishable key</em> and <em>Secret key</em>.</li>
            <li>Paste them into the form above and click <strong>Connect Stripe</strong>.</li>
            <li>Use <strong>Test</strong> keys while developing; flip the toggle to <strong>Live</strong> when you're ready for production.</li>
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

  function parse(resp) {
    if (typeof resp === 'string') { try { resp = JSON.parse(resp); } catch(e) { resp = {}; } }
    return resp || {};
  }

  $('#pm-stripe-form').on('submit', function (e) {
    e.preventDefault();
    var pubKey = $.trim($('#pm-pub-key').val());
    var secKey = $.trim($('#pm-sec-key').val());
    var isLive = $('#pm-live-toggle').is(':checked') ? 1 : 0;

    if (!pubKey) { Swal.fire({ icon: 'warning', title: 'Missing', text: 'Publishable key is required.' }); return; }
    if (!secKey) { Swal.fire({ icon: 'warning', title: 'Missing', text: 'Secret key is required.' }); return; }

    var $btn = $('#pm-save-btn').prop('disabled', true);

    $.post(ENTRY, {
      action: 'save_user_stripe_keys',
      user_id: USER_ID,
      stripe_publishable_key: pubKey,
      stripe_secret_key: secKey,
      is_live: isLive
    }).done(function (resp) {
      var data = parse(resp);
      if (data.success) {
        Swal.fire({ icon: 'success', title: 'Saved', text: data.message || 'Stripe keys saved.', timer: 1800, showConfirmButton: false }).then(function(){ location.reload(); });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Could not save keys.' });
      }
    }).fail(function () {
      Swal.fire({ icon: 'error', title: 'Network error', text: 'Could not reach the server.' });
    }).always(function () { $btn.prop('disabled', false); });
  });

  $(document).on('click', '#pm-disconnect-btn', function () {
    Swal.fire({
      title: 'Disconnect Stripe?',
      text: 'This will remove your saved API keys. You can reconnect anytime.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc2626',
      confirmButtonText: 'Yes, disconnect'
    }).then(function (result) {
      if (!result.isConfirmed) return;
      $.post(ENTRY, { action: 'delete_user_stripe_keys', user_id: USER_ID }).done(function (resp) {
        var data = parse(resp);
        if (data.success) {
          Swal.fire({ icon: 'success', title: 'Disconnected', text: data.message, timer: 1800, showConfirmButton: false }).then(function(){ location.reload(); });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Could not disconnect.' });
        }
      }).fail(function () {
        Swal.fire({ icon: 'error', title: 'Network error', text: 'Could not reach the server.' });
      });
    });
  });
})();
</script>
