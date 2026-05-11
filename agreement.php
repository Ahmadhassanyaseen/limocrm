<?php
/**
 * Standalone public agreement + e-sign + Stripe checkout.
 * URL: agreement.php?lead_id=LEAD_UUID
 *
 * Stripe publishable key is fetched dynamically from the DB
 * based on the lead's owner_c → limo_user_stripe_keys table.
 */

declare(strict_types=1);

$leadId = isset($_GET['lead_id']) ? trim((string)$_GET['lead_id']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Service Agreement &amp; Payment</title>
  <script src="https://js.stripe.com/v3/"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --ag-bg: #f0f2f7;
      --ag-card: #ffffff;
      --ag-card-alt: #f8fafc;
      --ag-bd: rgba(15,23,42,.07);
      --ag-bd-strong: rgba(15,23,42,.12);
      --ag-tx: #0f172a;
      --ag-tx-secondary: #475569;
      --ag-muted: #94a3b8;
      --ag-primary: #4f46e5;
      --ag-primary-light: rgba(79,70,229,.08);
      --ag-success: #059669;
      --ag-success-light: rgba(5,150,105,.08);
      --ag-danger: #dc2626;
      --ag-danger-light: rgba(220,38,38,.06);
      --ag-radius: 16px;
      --ag-radius-sm: 12px;
      --ag-shadow: 0 1px 3px rgba(15,23,42,.04), 0 4px 24px rgba(15,23,42,.06);
      --ag-font: 'Inter', ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: var(--ag-font);
      background: var(--ag-bg);
      color: var(--ag-tx);
      line-height: 1.6;
      min-height: 100vh;
      -webkit-font-smoothing: antialiased;
    }

    /* ── Top bar ── */
    .ag-topbar {
      background: var(--ag-card);
      border-bottom: 1px solid var(--ag-bd);
      padding: 16px 24px;
      display: flex; align-items: center; justify-content: center; gap: 10px;
    }
    .ag-topbar-icon {
      width: 36px; height: 36px; border-radius: 10px;
      background: var(--ag-primary-light);
      display: flex; align-items: center; justify-content: center;
      color: var(--ag-primary); font-size: 18px;
    }
    .ag-topbar-text {
      font-size: 13px; font-weight: 700; letter-spacing: .06em;
      text-transform: uppercase; color: var(--ag-tx-secondary);
    }

    /* ── Container ── */
    .ag-wrap {
      max-width: 680px; margin: 0 auto;
      padding: 32px 20px 64px;
    }
    @media (max-width: 480px) { .ag-wrap { padding: 20px 14px 48px; } }

    /* ── Progress steps ── */
    .ag-steps {
      display: flex; align-items: center; justify-content: center;
      gap: 0; margin-bottom: 32px;
    }
    .ag-step {
      display: flex; align-items: center; gap: 8px;
      font-size: 12px; font-weight: 600; color: var(--ag-muted);
      text-transform: uppercase; letter-spacing: .04em;
    }
    .ag-step.active { color: var(--ag-primary); }
    .ag-step.done { color: var(--ag-success); }
    .ag-step-num {
      width: 28px; height: 28px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 12px; font-weight: 700;
      border: 2px solid var(--ag-bd-strong);
      background: var(--ag-card); color: var(--ag-muted);
      transition: all .3s;
    }
    .ag-step.active .ag-step-num {
      border-color: var(--ag-primary); background: var(--ag-primary); color: #fff;
    }
    .ag-step.done .ag-step-num {
      border-color: var(--ag-success); background: var(--ag-success); color: #fff;
    }
    .ag-step-line {
      width: 40px; height: 2px; background: var(--ag-bd-strong);
      margin: 0 8px; border-radius: 2px;
    }
    .ag-step-line.done { background: var(--ag-success); }
    @media (max-width: 480px) {
      .ag-step span { display: none; }
      .ag-step-line { width: 28px; margin: 0 4px; }
    }

    /* ── Card ── */
    .ag-card {
      background: var(--ag-card);
      border: 1px solid var(--ag-bd);
      border-radius: var(--ag-radius);
      box-shadow: var(--ag-shadow);
      margin-bottom: 20px;
      overflow: hidden;
      transition: opacity .3s, transform .3s;
    }
    .ag-card-header {
      padding: 20px 24px 16px;
      display: flex; align-items: center; gap: 12px;
      border-bottom: 1px solid var(--ag-bd);
    }
    .ag-card-header-icon {
      width: 40px; height: 40px; border-radius: var(--ag-radius-sm);
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; flex-shrink: 0;
    }
    .ag-card-header h2 {
      font-size: 1rem; font-weight: 700; margin: 0; color: var(--ag-tx);
    }
    .ag-card-header p {
      font-size: 12px; color: var(--ag-muted); margin: 2px 0 0; line-height: 1.4;
    }
    .ag-card-body { padding: 24px; }
    @media (max-width: 480px) {
      .ag-card-header { padding: 16px 18px 14px; }
      .ag-card-body { padding: 18px; }
    }

    /* ── Detail grid ── */
    .ag-details {
      display: grid; grid-template-columns: 1fr 1fr;
      gap: 0; border: 1px solid var(--ag-bd); border-radius: var(--ag-radius-sm);
      overflow: hidden;
    }
    @media (max-width: 480px) { .ag-details { grid-template-columns: 1fr; } }
    .ag-detail {
      padding: 14px 16px;
      border-bottom: 1px solid var(--ag-bd);
      border-right: 1px solid var(--ag-bd);
    }
    .ag-details > .ag-detail:nth-child(2n) { border-right: none; }
    @media (max-width: 480px) { .ag-detail { border-right: none !important; } }
    .ag-detail:last-child, .ag-details > .ag-detail:nth-last-child(2):nth-child(odd) ~ .ag-detail { border-bottom: none; }
    .ag-detail.full { grid-column: 1 / -1; border-right: none; }
    .ag-detail-label {
      display: block; font-size: 10px; font-weight: 700; text-transform: uppercase;
      letter-spacing: .08em; color: var(--ag-muted); margin-bottom: 4px;
    }
    .ag-detail-val {
      font-size: 14px; font-weight: 600; color: var(--ag-tx); word-break: break-word;
    }
    .ag-detail-val.highlight {
      color: var(--ag-primary); font-size: 18px; font-weight: 800;
    }

    /* ── Signature ── */
    .ag-sig-area {
      border: 2px dashed rgba(79,70,229,.25);
      border-radius: var(--ag-radius-sm);
      background: linear-gradient(135deg, #fafaff 0%, #f5f3ff 100%);
      position: relative;
      overflow: hidden;
    }
    .ag-sig-area canvas {
      display: block; width: 100%; height: 200px;
      cursor: crosshair; border-radius: 10px;
    }
    .ag-sig-hint {
      position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%);
      font-size: 11px; color: var(--ag-muted); pointer-events: none;
      opacity: .7; transition: opacity .3s;
    }
    .ag-sig-hint.hidden { opacity: 0; }
    .ag-sig-actions {
      display: flex; gap: 8px; margin-top: 12px;
    }

    /* ── Stripe card ── */
    #card-element {
      padding: 14px 16px;
      border: 1px solid var(--ag-bd-strong);
      border-radius: var(--ag-radius-sm);
      background: var(--ag-card);
      transition: border-color .2s, box-shadow .2s;
    }
    #card-element:focus-within, #card-element.StripeElement--focus {
      border-color: var(--ag-primary);
      box-shadow: 0 0 0 3px rgba(79,70,229,.1);
    }
    .ag-stripe-badge {
      display: inline-flex; align-items: center; gap: 6px;
      font-size: 11px; color: var(--ag-muted); margin-top: 10px;
    }
    .ag-stripe-badge svg { width: 40px; height: 16px; }

    /* ── Buttons ── */
    .ag-btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 8px;
      border-radius: var(--ag-radius-sm); font-size: 14px; font-weight: 700;
      padding: 12px 20px; border: none; cursor: pointer;
      transition: all .2s; text-decoration: none;
    }
    .ag-btn:disabled { opacity: .5; cursor: not-allowed; }
    .ag-btn-secondary {
      background: var(--ag-card-alt); color: var(--ag-tx-secondary);
      border: 1px solid var(--ag-bd-strong);
    }
    .ag-btn-secondary:hover:not(:disabled) { background: #f1f5f9; }
    .ag-btn-primary {
      background: var(--ag-primary); color: #fff;
      box-shadow: 0 2px 8px rgba(79,70,229,.25);
    }
    .ag-btn-primary:hover:not(:disabled) {
      filter: brightness(1.06);
      box-shadow: 0 4px 16px rgba(79,70,229,.35);
    }
    .ag-btn-full { width: 100%; }

    /* ── Banners ── */
    .ag-banner {
      display: none; border-radius: var(--ag-radius-sm);
      padding: 14px 18px; margin-bottom: 20px;
      font-size: 14px; font-weight: 500; line-height: 1.5;
      align-items: flex-start; gap: 12px;
    }
    .ag-banner.show { display: flex; }
    .ag-banner-icon {
      width: 32px; height: 32px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; flex-shrink: 0;
    }
    .ag-banner-error {
      background: var(--ag-danger-light);
      border: 1px solid rgba(220,38,38,.15);
      color: #991b1b;
    }
    .ag-banner-error .ag-banner-icon { background: rgba(220,38,38,.1); color: var(--ag-danger); }
    .ag-banner-success {
      background: var(--ag-success-light);
      border: 1px solid rgba(5,150,105,.15);
      color: #065f46;
    }
    .ag-banner-success .ag-banner-icon { background: rgba(5,150,105,.12); color: var(--ag-success); }

    /* ── Loading ── */
    .ag-loading {
      text-align: center; padding: 56px 24px; color: var(--ag-muted);
    }
    .ag-loading-spinner {
      width: 40px; height: 40px; border: 3px solid var(--ag-bd-strong);
      border-top-color: var(--ag-primary); border-radius: 50%;
      animation: ag-spin .7s linear infinite;
      margin: 0 auto 16px;
    }
    @keyframes ag-spin { to { transform: rotate(360deg); } }
    .ag-loading p { font-size: 14px; font-weight: 500; }

    /* ── Missing link ── */
    .ag-empty {
      text-align: center; padding: 56px 24px;
    }
    .ag-empty-icon {
      width: 64px; height: 64px; border-radius: 18px;
      background: var(--ag-primary-light); color: var(--ag-primary);
      display: flex; align-items: center; justify-content: center;
      font-size: 28px; margin: 0 auto 18px;
    }
    .ag-empty h3 { font-size: 1.1rem; font-weight: 700; margin: 0 0 6px; color: var(--ag-tx); }
    .ag-empty p { font-size: 13px; color: var(--ag-muted); max-width: 360px; margin: 0 auto; }

    /* ── Security footer ── */
    .ag-footer {
      text-align: center; padding: 24px 0 0; color: var(--ag-muted);
      font-size: 11px; display: flex; flex-direction: column; align-items: center; gap: 6px;
    }
    .ag-footer-locks {
      display: flex; gap: 16px; font-size: 11px; color: var(--ag-muted);
    }
    .ag-footer-locks span { display: flex; align-items: center; gap: 4px; }
  </style>
</head>
<body>

<!-- Top bar -->
<div class="ag-topbar">
  <div class="ag-topbar-icon"><i class="ri-shield-check-fill"></i></div>
  <span class="ag-topbar-text">Secure Agreement</span>
</div>

<div class="ag-wrap">

  <!-- Banners -->
  <div class="ag-banner ag-banner-error" id="err-banner">
    <div class="ag-banner-icon"><i class="ri-error-warning-fill"></i></div>
    <div id="err-text"></div>
  </div>
  <div class="ag-banner ag-banner-success" id="ok-banner">
    <div class="ag-banner-icon"><i class="ri-checkbox-circle-fill"></i></div>
    <div id="ok-text"></div>
  </div>

  <?php if ($leadId === ''): ?>
  <!-- Missing link state -->
  <div class="ag-card">
    <div class="ag-empty">
      <div class="ag-empty-icon"><i class="ri-link-unlink-m"></i></div>
      <h3>No agreement link found</h3>
      <p>Open the agreement URL from your email or booking confirmation to proceed.</p>
    </div>
  </div>

  <?php else: ?>

  <!-- Progress steps -->
  <!-- <div class="ag-steps" id="ag-steps">
    <div class="ag-step active" id="step-1"><div class="ag-step-num">1</div><span>Review</span></div>
    <div class="ag-step-line" id="line-1"></div>
    <div class="ag-step" id="step-2"><div class="ag-step-num">2</div><span>Sign</span></div>
    <div class="ag-step-line" id="line-2"></div>
    <div class="ag-step" id="step-3"><div class="ag-step-num">3</div><span>Pay</span></div>
  </div> -->

  <!-- Loading -->
  <div class="ag-card" id="loading-card">
    <div class="ag-loading">
      <div class="ag-loading-spinner"></div>
      <p>Loading your agreement...</p>
    </div>
  </div>

  <!-- 1. Booking Details -->
  <div class="ag-card" id="lead-card" style="display:none;">
    <div class="ag-card-header">
      <div class="ag-card-header-icon" style="background:var(--ag-primary-light);color:var(--ag-primary);">
        <i class="ri-file-list-3-line"></i>
      </div>
      <div>
        <h2>Booking Details</h2>
        <p>Please review the information below carefully</p>
      </div>
    </div>
    <div class="ag-card-body" style="padding:0;">
      <div class="ag-details" id="lead-details"></div>
    </div>
  </div>

  <!-- 2. Electronic Signature -->
  <div class="ag-card" id="sig-card" style="display:none;">
    <div class="ag-card-header">
      <div class="ag-card-header-icon" style="background:rgba(168,85,247,.08);color:#7c3aed;">
        <i class="ri-quill-pen-line"></i>
      </div>
      <div>
        <h2>Electronic Signature</h2>
        <p>Draw your signature using a mouse or your finger</p>
      </div>
    </div>
    <div class="ag-card-body">
      <div class="ag-sig-area">
        <canvas id="sig-canvas" width="640" height="200"></canvas>
        <div class="ag-sig-hint" id="sig-hint"><i class="ri-pencil-line"></i> Sign here</div>
      </div>
      <div class="ag-sig-actions">
        <button type="button" class="ag-btn ag-btn-secondary" id="sig-clear">
          <i class="ri-eraser-line"></i> Clear
        </button>
      </div>
    </div>
  </div>

  <!-- 3. Payment -->
  <div class="ag-card" id="pay-card" style="display:none;">
    <div class="ag-card-header">
      <div class="ag-card-header-icon" style="background:rgba(16,185,129,.08);color:#059669;">
        <i class="ri-bank-card-2-line"></i>
      </div>
      <div>
        <h2>Payment Information</h2>
        <p>Your card data is encrypted and sent directly to Stripe</p>
      </div>
    </div>
    <div class="ag-card-body">
      <div id="card-element"></div>
      <div id="card-errors" style="color:var(--ag-danger);font-size:13px;min-height:20px;margin:8px 0 4px;"></div>
      <div class="ag-stripe-badge">
        <i class="ri-lock-2-line"></i> Secured by
        <svg viewBox="0 0 60 25" xmlns="http://www.w3.org/2000/svg"><path d="M5 10.2c0-.7.6-1 1.6-1 1.4 0 3.2.4 4.6 1.2V6.7A12.3 12.3 0 006.6 6C2.8 6 .4 8 .4 11.2c0 5.1 7 4.3 7 6.5 0 .9-.7 1.1-1.8 1.1-1.5 0-3.5-.6-5.1-1.5v3.8c1.7.7 3.5 1.1 5.1 1.1 3.8 0 6.5-1.9 6.5-5.2C13.1 11.5 5 12.5 5 10.2z" fill="#6772E5"/><path d="M17.4 3.3l-3.8.8v15.6c0 2.9 2.1 4 4.2 4 1.3 0 2.3-.2 2.8-.5v-3.1c-.5.2-3 .9-3-1.4V12h3v-3.2h-3l-.2-5.5z" fill="#6772E5"/><path d="M24.4 11.5l-.2-2.7h-3.4v13.5h3.9V14c.9-1.2 2.5-1 3-.8V8.8c-.5-.2-2.4-.5-3.3 2.7z" fill="#6772E5"/><path d="M32 4.2l-3.8.8v3.8h3.8V4.2zM28.2 8.8v13.5h3.9V8.8h-3.9z" fill="#6772E5"/><path d="M40.6 8.5c-1.5 0-2.5.7-3 1.2l-.2-1h-3.5v17.5l3.9-.8v-4.3c.6.4 1.4 1 2.7 1 2.7 0 5.2-2.2 5.2-7 0-4.4-2.5-6.6-5.1-6.6zm-.9 10.2c-.9 0-1.4-.3-1.8-.7v-5.6c.4-.5 1-.8 1.8-.8 1.4 0 2.3 1.5 2.3 3.5 0 2.1-1 3.6-2.3 3.6z" fill="#6772E5"/><path d="M55.4 15.5c0-4.1-2-7-5.2-7s-5.5 3-5.5 7c0 4.6 2.5 6.9 6 6.9 1.7 0 3-.4 4-.9v-3c-1 .5-2.1.8-3.5.8s-2.6-.5-2.7-2.2h6.8c0-.2.1-1 .1-1.6zm-6.9-1.5c0-1.6 1-2.3 1.9-2.3s1.8.7 1.8 2.3h-3.7z" fill="#6772E5"/></svg>
      </div>

      <div style="margin-top:20px;">
        <button type="button" class="ag-btn ag-btn-primary ag-btn-full" id="btn-submit" disabled>
          <i class="ri-check-double-line"></i> Confirm &amp; Pay
        </button>
      </div>
    </div>
  </div>

  <!-- Security footer -->
  <div class="ag-footer" id="ag-footer" style="display:none;">
    <div class="ag-footer-locks">
      <span><i class="ri-lock-2-line"></i> 256-bit SSL</span>
      <span><i class="ri-shield-check-line"></i> PCI Compliant</span>
      <span><i class="ri-eye-off-line"></i> Data Privacy</span>
    </div>
  </div>

  <?php endif; ?>
</div>

<?php if ($leadId !== ''): ?>
<script>
(function () {
  var LEAD_ID = <?php echo json_encode($leadId); ?>;

  var errEl = document.getElementById('err-banner');
  var errText = document.getElementById('err-text');
  var okEl = document.getElementById('ok-banner');
  var okText = document.getElementById('ok-text');
  var loadingCard = document.getElementById('loading-card');

  var stripe = null;
  var cardEl = null;

  function showErr(msg) { errText.textContent = msg; errEl.classList.add('show'); }
  function clearErr() { errEl.classList.remove('show'); errText.textContent = ''; }

  function markStep(n) {
    for (var i = 1; i <= 3; i++) {
      var s = document.getElementById('step-' + i);
      var l = document.getElementById('line-' + (i - 1));
      // if (i < n) { s.className = 'ag-step done'; if (l) l.className = 'ag-step-line done'; }
      // else if (i === n) { s.className = 'ag-step active'; }
      // else { s.className = 'ag-step'; }
    }
  }

  var canvas = document.getElementById('sig-canvas');
  var ctx = canvas.getContext('2d');
  var drawing = false;
  var hasInk = false;
  var sigHint = document.getElementById('sig-hint');

  function pos(e) {
    var r = canvas.getBoundingClientRect();
    var x = (e.clientX ?? (e.touches && e.touches[0].clientX)) - r.left;
    var y = (e.clientY ?? (e.touches && e.touches[0].clientY)) - r.top;
    return { x: x * (canvas.width / r.width), y: y * (canvas.height / r.height) };
  }
  function startDraw(e) {
    e.preventDefault(); drawing = true;
    var p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y);
    if (sigHint) sigHint.classList.add('hidden');
  }
  function moveDraw(e) {
    if (!drawing) return; e.preventDefault();
    var p = pos(e);
    ctx.strokeStyle = '#0f172a'; ctx.lineWidth = 2.5; ctx.lineCap = 'round'; ctx.lineJoin = 'round';
    ctx.lineTo(p.x, p.y); ctx.stroke(); ctx.beginPath(); ctx.moveTo(p.x, p.y);
    hasInk = true;
  }
  function endDraw() { drawing = false; ctx.beginPath(); }
  canvas.addEventListener('mousedown', startDraw);
  canvas.addEventListener('mousemove', moveDraw);
  canvas.addEventListener('mouseup', endDraw);
  canvas.addEventListener('mouseleave', endDraw);
  canvas.addEventListener('touchstart', startDraw, { passive: false });
  canvas.addEventListener('touchmove', moveDraw, { passive: false });
  canvas.addEventListener('touchend', endDraw);

  document.getElementById('sig-clear').addEventListener('click', function () {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasInk = false;
    if (sigHint) sigHint.classList.remove('hidden');
  });

  function money(s) {
    var n = parseFloat(String(s).replace(/[^0-9.]/g, ''));
    if (isNaN(n)) return '—';
    return '$' + n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function esc(s) {
    var d = document.createElement('div');
    d.textContent = s || '';
    return d.innerHTML;
  }

  fetch('config/agreement_api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
    body: new URLSearchParams({ op: 'fetch', lead_id: LEAD_ID })
  }).then(function (r) {
    return r.text().then(function (raw) {
      try {
        var jsonStart = raw.indexOf('{');
        if (jsonStart > 0) raw = raw.substring(jsonStart);
        return JSON.parse(raw);
      } catch (e) {
        throw new Error('Invalid JSON response');
      }
    });
  }).then(function (data) {
    loadingCard.style.display = 'none';

    if (!data.success) { showErr(data.message || 'Unable to load this agreement.'); return; }

    var stripePk = data.stripe_publishable_key || '';
    if (!stripePk) {
      showErr('Payment processing is not configured for this account. Please contact the service provider.');
      return;
    }

    stripe = Stripe(stripePk);
    var elements = stripe.elements();
    cardEl = elements.create('card', {
      style: { base: { fontSize: '15px', fontFamily: "'Inter', sans-serif", color: '#0f172a', '::placeholder': { color: '#94a3b8' } } }
    });
    cardEl.mount('#card-element');
    cardEl.on('change', function (e) {
      document.getElementById('card-errors').textContent = e.error ? e.error.message : '';
    });

    var L = data.lead || {};
    var name = ((L.first_name || '') + ' ' + (L.last_name || '')).trim();
    var email = L.email1 || L.email || '';
    var phone = L.phone_c || L.phone_work || L.phone_mobile || '';
    var total = L.total_price_c || L.total_price || '';
    var pickup = L.pickup_address_c || '';
    var dropoff = L.dropoff_address_c || '';
    var eventDate = L.event_date_c || '';
    var serviceType = L.service_type_c || '';
    var passengers = L.passengers_c || '';
    var serviceLen = L.service_length_c || '';

    var html = '';
    html += '<div class="ag-detail"><span class="ag-detail-label">Client Name</span><span class="ag-detail-val">' + esc(name) + '</span></div>';
    html += '<div class="ag-detail"><span class="ag-detail-label">Email</span><span class="ag-detail-val">' + esc(email) + '</span></div>';
    html += '<div class="ag-detail"><span class="ag-detail-label">Phone</span><span class="ag-detail-val">' + esc(phone) + '</span></div>';
    html += '<div class="ag-detail"><span class="ag-detail-label">Total Amount</span><span class="ag-detail-val highlight">' + money(total) + '</span></div>';
    html += '<div class="ag-detail full"><span class="ag-detail-label">Pickup Location</span><span class="ag-detail-val">' + esc(pickup) + '</span></div>';
    html += '<div class="ag-detail full"><span class="ag-detail-label">Dropoff Location</span><span class="ag-detail-val">' + esc(dropoff) + '</span></div>';
    html += '<div class="ag-detail"><span class="ag-detail-label">Event Date</span><span class="ag-detail-val">' + esc(eventDate) + '</span></div>';
    html += '<div class="ag-detail"><span class="ag-detail-label">Service Type</span><span class="ag-detail-val">' + esc(serviceType) + '</span></div>';
    if (passengers) html += '<div class="ag-detail"><span class="ag-detail-label">Passengers</span><span class="ag-detail-val">' + esc(passengers) + '</span></div>';
    if (serviceLen) html += '<div class="ag-detail"><span class="ag-detail-label">Service Length</span><span class="ag-detail-val">' + esc(serviceLen) + ' hrs</span></div>';
    document.getElementById('lead-details').innerHTML = html;

    document.getElementById('lead-card').style.display = 'block';
    document.getElementById('sig-card').style.display = 'block';
    document.getElementById('pay-card').style.display = 'block';
    document.getElementById('ag-footer').style.display = 'flex';
    document.getElementById('btn-submit').disabled = false;
    markStep(1);
  }).catch(function (err) {
    loadingCard.style.display = 'none';
    console.error('Error:', err);
    showErr('Network error — could not load agreement data.');
  });

  document.getElementById('btn-submit').addEventListener('click', function () {
    clearErr();
    if (!hasInk) { showErr('Please add your signature before submitting.'); return; }
    if (!stripe || !cardEl) { showErr('Payment system is not ready. Refresh the page.'); return; }

    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Processing...';

    Swal.fire({
      title: 'Processing Payment',
      html: '<div style="display:flex;flex-direction:column;align-items:center;gap:12px;padding:8px 0;">' +
            '<div style="width:56px;height:56px;border:4px solid #e2e8f0;border-top-color:#4f46e5;border-radius:50%;animation:ag-spin .7s linear infinite;"></div>' +
            '<p style="color:#475569;font-size:14px;margin:0;">Please wait while we securely process your payment and generate your agreement document...</p>' +
            '<p style="color:#94a3b8;font-size:12px;margin:0;">Do not close or refresh this page.</p></div>',
      allowOutsideClick: false,
      allowEscapeKey: false,
      showConfirmButton: false,
      customClass: { popup: 'ag-swal-popup' }
    });

    stripe.createPaymentMethod({ type: 'card', card: cardEl }).then(function (res) {
      if (res.error) {
        Swal.close();
        showErr(res.error.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-check-double-line"></i> Confirm & Pay';
        return;
      }

      return fetch('config/agreement_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: new URLSearchParams({
          op: 'submit',
          lead_id: LEAD_ID,
          payment_method_id: res.paymentMethod.id,
          signature_png: canvas.toDataURL('image/png')
        })
      }).then(function (r) {
        return r.text().then(function (raw) {
          try {
            var j = raw.indexOf('{');
            if (j > 0) raw = raw.substring(j);
            return JSON.parse(raw);
          } catch (e) { return { success: false, message: 'Invalid server response' }; }
        });
      }).then(function (out) {
        if (!out.success) {
          Swal.close();
          showErr(out.message || 'Payment failed.');
          btn.disabled = false;
          btn.innerHTML = '<i class="ri-check-double-line"></i> Confirm & Pay';
          return;
        }

        Swal.fire({
          icon: 'success',
          title: 'Payment Successful!',
          html: '<div style="text-align:center;padding:4px 0;">' +
                '<p style="color:#475569;font-size:14px;margin:0 0 8px;">Your agreement has been signed and payment processed successfully.</p>' +
                (out.payment_intent_id ? '<p style="color:#94a3b8;font-size:12px;margin:0 0 8px;">Reference: ' + esc(out.payment_intent_id) + '</p>' : '') +
                (out.agreement_pdf_url ? '<a href="' + esc(out.agreement_pdf_url) + '" target="_blank" style="display:inline-flex;align-items:center;gap:6px;background:#4f46e5;color:#fff;padding:10px 20px;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;margin-top:4px;"><i class="ri-file-download-line"></i> Download Agreement PDF</a>' : '') +
                '</div>',
          confirmButtonText: 'Done',
          confirmButtonColor: '#4f46e5',
          allowOutsideClick: false
        });

        document.getElementById('lead-card').style.display = 'none';
        document.getElementById('sig-card').style.display = 'none';
        document.getElementById('pay-card').style.display = 'none';
        document.getElementById('ag-footer').style.display = 'none';

        okText.innerHTML = '<strong style="font-size:16px;">Thank you!</strong><br>' +
          'Your agreement has been signed and payment processed successfully.' +
          (out.payment_intent_id ? '<br><span style="font-size:12px;opacity:.7;">Reference: ' + esc(out.payment_intent_id) + '</span>' : '') +
          (out.agreement_pdf_url ? '<br><a href="' + esc(out.agreement_pdf_url) + '" target="_blank" style="display:inline-flex;align-items:center;gap:6px;color:#4f46e5;font-weight:600;font-size:14px;margin-top:8px;"><i class="ri-file-download-line"></i> Download Agreement PDF</a>' : '');
        okEl.classList.add('show');
      });
    }).catch(function () {
      Swal.close();
      showErr('Something went wrong. Please try again.');
      btn.disabled = false;
      btn.innerHTML = '<i class="ri-check-double-line"></i> Confirm & Pay';
    });
  });
})();
</script>
<?php endif; ?>

</body>
</html>
