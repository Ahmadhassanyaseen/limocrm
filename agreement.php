<?php
/**
 * Standalone public agreement + e-sign + Stripe checkout (Payment Element / Card).
 * Opened via signed link only: agreement.php?l=LEAD_UUID&t=TOKEN
 *
 * Configure: config/agreement_config.php (see agreement_config.sample.php)
 * Stripe card data is collected by Stripe.js — it never touches your PHP server (PCI-friendly).
 */

declare(strict_types=1);

$leadId = isset($_GET['l']) ? trim((string)$_GET['l']) : '';
$token  = isset($_GET['t']) ? trim((string)$_GET['t']) : '';

$cfgPath = __DIR__ . '/config/agreement_config.php';
$stripePk = '';
if (is_readable($cfgPath)) {
    $c = include $cfgPath;
    if (is_array($c)) {
        $stripePk = (string)($c['stripe_publishable_key'] ?? '');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Service agreement &amp; payment</title>
  <script src="https://js.stripe.com/v3/"></script>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
  <style>
    :root {
      --bg: #f4f6fb;
      --card: #fff;
      --bd: rgba(15,23,42,0.08);
      --tx: #0f172a;
      --muted: #64748b;
      --primary: #4338ca;
      --danger: #dc2626;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      background: var(--bg);
      color: var(--tx);
      line-height: 1.55;
      min-height: 100vh;
      padding: 24px 16px 48px;
    }
    .wrap { max-width: 720px; margin: 0 auto; }
    .brand {
      text-align: center;
      margin-bottom: 22px;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--muted);
    }
    .card {
      background: var(--card);
      border: 1px solid var(--bd);
      border-radius: 16px;
      padding: 22px 22px 20px;
      margin-bottom: 18px;
      box-shadow: 0 4px 24px rgba(15,23,42,0.06);
    }
    h1 { font-size: 1.35rem; margin: 0 0 6px; letter-spacing: -0.02em; }
    .sub { font-size: 13px; color: var(--muted); margin: 0 0 16px; }
    .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 16px; font-size: 14px; }
    @media (max-width: 560px) { .grid2 { grid-template-columns: 1fr; } }
    .lbl { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); margin-bottom: 2px; }
    .val { font-weight: 600; word-break: break-word; }
    .hr { height: 1px; background: var(--bd); margin: 16px 0; border: 0; }
    h2 { font-size: 1rem; margin: 0 0 12px; display: flex; align-items: center; gap: 8px; }
    .sig-wrap {
      border: 1px dashed rgba(67,56,202,0.35);
      border-radius: 12px;
      background: #fafaff;
      touch-action: none;
    }
    canvas { display: block; width: 100%; height: 180px; cursor: crosshair; border-radius: 12px; }
    .sig-actions { display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap; }
    .btn {
      display: inline-flex; align-items: center; gap: 6px;
      border-radius: 10px; font-size: 14px; font-weight: 600;
      padding: 10px 16px; border: none; cursor: pointer; transition: opacity 0.15s;
    }
    .btn:disabled { opacity: 0.55; cursor: not-allowed; }
    .btn-secondary { background: #e2e8f0; color: var(--tx); }
    .btn-primary { background: var(--primary); color: #fff; }
    .btn-primary:hover:not(:disabled) { filter: brightness(1.05); }
    #card-element {
      padding: 12px 14px;
      border: 1px solid var(--bd);
      border-radius: 12px;
      background: #fff;
      margin-bottom: 12px;
    }
    .stripe-note { font-size: 11px; color: var(--muted); margin-bottom: 10px; line-height: 1.45; }
    #err-banner {
      display: none;
      background: rgba(220,38,38,0.09);
      border: 1px solid rgba(220,38,38,0.25);
      color: #991b1b;
      padding: 10px 12px;
      border-radius: 10px;
      font-size: 13px;
      margin-bottom: 14px;
    }
    #ok-banner {
      display: none;
      background: rgba(22,163,74,0.09);
      border: 1px solid rgba(22,163,74,0.25);
      color: #166534;
      padding: 14px;
      border-radius: 12px;
      font-size: 14px;
    }
    #state-missing-link { text-align: center; padding: 40px 20px; color: var(--muted); }
  </style>
</head>
<body>

<div class="wrap">
  <div class="brand"><i class="ri-shield-check-line" style="vertical-align:middle;"></i> Limo CRM — Agreement</div>

  <div id="err-banner"></div>
  <div id="ok-banner"></div>

  <?php if ($leadId === '' || $token === ''): ?>
  <div class="card" id="state-missing-link">
    <p style="margin:0;"><strong>This page needs a valid link.</strong><br>Open the agreement URL from your email or booking confirmation.</p>
  </div>
  <?php else: ?>

  <?php if ($stripePk === ''): ?>
  <div class="card">
    <p style="margin:0;color:var(--danger);font-weight:600;">Stripe publishable key is not configured.</p>
    <p style="margin:12px 0 0;font-size:13px;color:var(--muted);">Copy <code>config/agreement_config.sample.php</code> to <code>config/agreement_config.php</code> and set <code>stripe_publishable_key</code>.</p>
  </div>
  <?php endif; ?>

  <div class="card" id="lead-card" style="display:none;">
    <h1>Booking agreement</h1>
    <p class="sub">Review the summary below. Sign electronically, then submit payment to confirm.</p>
    <div id="lead-details" class="grid2"></div>
  </div>

  <div class="card" id="sig-card" style="display:none;">
    <h2><i class="ri-edit-2-line" style="color:var(--primary);"></i> Electronic signature</h2>
    <p class="sub" style="margin-top:0;">Sign in the box with your finger or mouse.</p>
    <div class="sig-wrap">
      <canvas id="sig-canvas" width="664" height="180"></canvas>
    </div>
    <div class="sig-actions">
      <button type="button" class="btn btn-secondary" id="sig-clear"><i class="ri-brush-line"></i> Clear</button>
    </div>
  </div>

  <div class="card" id="pay-card" style="display:none;">
    <h2><i class="ri-bank-card-line" style="color:var(--primary);"></i> Payment</h2>
    <p class="stripe-note">
      Card details are sent directly to <strong>Stripe</strong> (encrypted). This site does not store your full card number or CVC.
    </p>
    <div id="card-element"></div>
    <div id="card-errors" style="color:var(--danger);font-size:13px;margin-bottom:10px;"></div>
    <button type="button" class="btn btn-primary" id="btn-submit" disabled>
      <i class="ri-check-double-line"></i> Sign &amp; pay
    </button>
  </div>

  <?php endif; ?>
</div>

<?php if ($leadId !== '' && $token !== '' && $stripePk !== ''): ?>
<script>
(function () {
  var LEAD_ID = <?php echo json_encode($leadId); ?>;
  var TOKEN = <?php echo json_encode($token); ?>;
  var STRIPE_PK = <?php echo json_encode($stripePk); ?>;

  var errEl = document.getElementById('err-banner');
  var okEl = document.getElementById('ok-banner');

  function showErr(msg) {
    errEl.style.display = 'block';
    errEl.textContent = msg;
  }
  function clearErr() {
    errEl.style.display = 'none';
    errEl.textContent = '';
  }

  var stripe = Stripe(STRIPE_PK);
  var elements = stripe.elements();
  var card = elements.create('card', { style: { base: { fontSize: '16px' } } });
  card.mount('#card-element');
  card.on('change', function (e) {
    document.getElementById('card-errors').textContent = e.error ? e.error.message : '';
  });

  var canvas = document.getElementById('sig-canvas');
  var ctx = canvas.getContext('2d');
  var drawing = false;
  var hasInk = false;

  function pos(e) {
    var r = canvas.getBoundingClientRect();
    var x = (e.clientX ?? (e.touches && e.touches[0].clientX)) - r.left;
    var y = (e.clientY ?? (e.touches && e.touches[0].clientY)) - r.top;
    var sx = canvas.width / r.width;
    var sy = canvas.height / r.height;
    return { x: x * sx, y: y * sy };
  }
  function start(e) {
    e.preventDefault();
    drawing = true;
    var p = pos(e);
    ctx.beginPath();
    ctx.moveTo(p.x, p.y);
  }
  function move(e) {
    if (!drawing) return;
    e.preventDefault();
    var p = pos(e);
    ctx.strokeStyle = '#0f172a';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineTo(p.x, p.y);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(p.x, p.y);
    hasInk = true;
  }
  function end() { drawing = false; ctx.beginPath(); }
  canvas.addEventListener('mousedown', start);
  canvas.addEventListener('mousemove', move);
  canvas.addEventListener('mouseup', end);
  canvas.addEventListener('mouseleave', end);
  canvas.addEventListener('touchstart', start, { passive: false });
  canvas.addEventListener('touchmove', move, { passive: false });
  canvas.addEventListener('touchend', end);

  document.getElementById('sig-clear').addEventListener('click', function () {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasInk = false;
  });

  function money(s) {
    var n = parseFloat(String(s).replace(/[^0-9.]/g, ''));
    if (isNaN(n)) return '—';
    return '$' + n.toFixed(2);
  }

  fetch('config/agreement_api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
    body: new URLSearchParams({ op: 'fetch', lead_id: LEAD_ID, token: TOKEN })
  }).then(function (r) { return r.json(); }).then(function (data) {
    if (!data.success) {
      showErr(data.message || 'Unable to load this agreement.');
      return;
    }
    var L = data.lead || {};
    var html = '';
    html += '<div><div class="lbl">Name</div><div class="val">' + escapeHtml((L.first_name || '') + ' ' + (L.last_name || '')) + '</div></div>';
    html += '<div><div class="lbl">Email</div><div class="val">' + escapeHtml(L.email || '') + '</div></div>';
    html += '<div><div class="lbl">Phone</div><div class="val">' + escapeHtml(L.phone || '') + '</div></div>';
    html += '<div><div class="lbl">Trip total</div><div class="val">' + money(L.total_price) + '</div></div>';
    html += '<div style="grid-column:1/-1;"><div class="lbl">Pickup</div><div class="val">' + escapeHtml(L.pickup_address || '') + '</div></div>';
    html += '<div style="grid-column:1/-1;"><div class="lbl">Dropoff</div><div class="val">' + escapeHtml(L.dropoff_address || '') + '</div></div>';
    html += '<div><div class="lbl">Event date</div><div class="val">' + escapeHtml(L.event_date || '') + '</div></div>';
    html += '<div><div class="lbl">Service</div><div class="val">' + escapeHtml(L.service_type || '') + '</div></div>';
    document.getElementById('lead-details').innerHTML = html;
    document.getElementById('lead-card').style.display = 'block';
    document.getElementById('sig-card').style.display = 'block';
    document.getElementById('pay-card').style.display = 'block';
    document.getElementById('btn-submit').disabled = false;
  }).catch(function () {
    showErr('Network error loading agreement.');
  });

  function escapeHtml(s) {
    var d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
  }

  document.getElementById('btn-submit').addEventListener('click', function () {
    clearErr();
    if (!hasInk) {
      showErr('Please add your signature in the signature box.');
      return;
    }
    var btn = this;
    btn.disabled = true;

    stripe.createPaymentMethod({ type: 'card', card: card }).then(function (res) {
      if (res.error) {
        showErr(res.error.message);
        btn.disabled = false;
        return;
      }
      var pm = res.paymentMethod.id;
      var png = canvas.toDataURL('image/png');

      return fetch('config/agreement_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: new URLSearchParams({
          op: 'submit',
          lead_id: LEAD_ID,
          token: TOKEN,
          payment_method_id: pm,
          signature_png: png
        })
      }).then(function (r) { return r.json(); }).then(function (out) {
        if (!out.success) {
          showErr(out.message || 'Payment failed.');
          btn.disabled = false;
          return;
        }
        document.getElementById('lead-card').style.display = 'none';
        document.getElementById('sig-card').style.display = 'none';
        document.getElementById('pay-card').style.display = 'none';
        okEl.style.display = 'block';
        okEl.innerHTML = '<strong>Thank you.</strong> Your agreement and payment are on file. ' +
          (out.payment_intent_id ? '<br><span style="font-size:12px;opacity:.85;">Ref: ' + escapeHtml(out.payment_intent_id) + '</span>' : '');
      });
    }).catch(function () {
      showErr('Something went wrong. Please try again.');
      btn.disabled = false;
    });
  });
})();
</script>
<?php endif; ?>

</body>
</html>
