<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>
<?php
$data['id'] = $_GET['id'];
$response = fetchSingleLead($data);
$lead = $response[0];

function lead_text($value): string {
  return htmlspecialchars((string)($value ?? ''), ENT_QUOTES);
}
function lead_money($value): string {
  $n = is_numeric($value) ? (float)$value : (float)preg_replace('/[^0-9.\-]/', '', (string)($value ?? '0'));
  return '$' . number_format($n, 2);
}
function lead_initials(string $name): string {
  $name = trim($name);
  if ($name === '') return '?';
  $parts = preg_split('/\s+/', $name) ?: [];
  return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
}

$leadId     = $lead['id'] ?? ($_GET['id'] ?? '');
$leadName   = trim((string)($lead['first_name'] ?? '') . ' ' . (string)($lead['last_name'] ?? ''));
$leadStatus = (string)($lead['status'] ?? '');
$leadEmail  = (string)($lead['email1'] ?? '');
$hasEmail   = $leadEmail !== '';
$tripPrice  = $lead['total_price_c'] ?? 0;

$toFloat = function ($v) { if ($v === null || $v === '') return 0.0; return is_numeric($v) ? (float)$v : (float)preg_replace('/[^0-9.\-]/', '', (string)$v); };
$serviceLengthNum = $toFloat($lead['service_length_c'] ?? null);
$rateNum          = $toFloat($lead['rate_c'] ?? null);
$fuelNum          = $toFloat($lead['fuel_c'] ?? null);
$commissionNum    = $toFloat($lead['driver_commission_c'] ?? ($lead['commission_c'] ?? null));
$totalNum         = $toFloat($tripPrice);
$hasCalc          = ($serviceLengthNum > 0 && $rateNum > 0);
$quotedPrice      = $hasCalc ? round($serviceLengthNum * $rateNum, 2) : 0.0;
$sumOfParts       = round($quotedPrice + $fuelNum + $commissionNum, 2);
$hasMismatch      = $totalNum > 0 && abs($sumOfParts - $totalNum) >= 0.01 && ($quotedPrice > 0 || $fuelNum > 0 || $commissionNum > 0);
?>

<style>
  .ld-page { --ld-surface: #ffffff; --ld-surface-2: #f8fafc; --ld-border: rgba(15,23,42,0.10); --ld-text: #0f172a; --ld-muted: rgba(15,23,42,0.55); }
  .dark .ld-page { --ld-surface: rgba(255,255,255,0.035); --ld-surface-2: rgba(255,255,255,0.05); --ld-border: rgba(255,255,255,0.08); --ld-text: rgba(255,255,255,0.92); --ld-muted: rgba(255,255,255,0.50); }

  .ld-card { background: var(--ld-surface); border: 1px solid var(--ld-border); border-radius: 16px; overflow: hidden; transition: box-shadow 0.2s; }
  .ld-card:hover { box-shadow: 0 4px 24px rgba(15,23,42,0.06); }
  .dark .ld-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.25); }
  .ld-card-header { background: rgba(15,23,42,0.025); border-bottom: 1px solid rgba(15,23,42,0.08); padding: 16px 24px; }
  .dark .ld-card-header { background: rgba(255,255,255,0.025); border-bottom-color: rgba(255,255,255,0.08); }
  .ld-card-body { padding: 24px; }

  .ld-card-icon { width: 32px; height: 32px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }

  .ld-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 0; border-bottom: 1px solid rgba(15,23,42,0.06); }
  .dark .ld-row { border-bottom-color: rgba(255,255,255,0.06); }
  .ld-row:last-child { border-bottom: none; }
  .ld-row-label { font-size: 12px; color: var(--ld-muted); display: flex; align-items: center; gap: 8px; font-weight: 600; white-space: nowrap; }
  .ld-row-value { font-size: 13px; font-weight: 600; color: var(--ld-text); text-align: right; word-break: break-word; }
  .ld-row-value a { color: var(--ld-text); transition: color 0.15s; }
  .ld-row-value a:hover { color: rgb(var(--primary-rgb)); }

  .ld-addr-block { padding: 12px 16px; border-radius: 12px; background: var(--ld-surface-2); margin-bottom: 8px; }
  .ld-addr-block:last-child { margin-bottom: 0; }
  .ld-addr-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
  .ld-addr-text { font-size: 13px; color: var(--ld-text); line-height: 1.5; }

  .ld-price-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 16px; }
  .ld-price-row + .ld-price-row { border-top: 1px solid rgba(15,23,42,0.06); }
  .dark .ld-price-row + .ld-price-row { border-top-color: rgba(255,255,255,0.06); }
  .ld-price-label { font-size: 13px; color: var(--ld-text); display: flex; align-items: center; gap: 8px; }
  .ld-price-val { font-size: 13px; font-weight: 700; color: var(--ld-text); }

  .ld-total-box { border-radius: 14px; padding: 20px; text-align: center; margin-bottom: 20px; }
  .ld-total-amount { font-size: 32px; font-weight: 800; letter-spacing: -0.02em; line-height: 1; }
  .ld-total-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin-top: 6px; }

  .ld-notes-block { padding: 14px 16px; border-radius: 12px; background: rgba(245,158,11,0.06); border: 1px solid rgba(245,158,11,0.12); }
  .ld-notes-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #f59e0b; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
  .ld-notes-text { font-size: 13px; color: var(--ld-text); line-height: 1.6; white-space: pre-line; }

  .ld-sticky-header {
    position: sticky; top: 0; z-index: 99;
    border-bottom: 1px solid var(--ld-border);
    padding: 14px 24px; margin: -24px -24px 24px -24px;
    backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
    background: rgba(255,255,255,0.88); transition: box-shadow 0.2s;
  }
  .dark .ld-sticky-header { background: rgba(18,18,30,0.88); }
  .ld-sticky-header.scrolled { box-shadow: 0 4px 20px rgba(15,23,42,0.08); }
  .dark .ld-sticky-header.scrolled { box-shadow: 0 4px 20px rgba(0,0,0,0.30); }

  .ld-action-btn {
    height: 38px; border-radius: 10px; border: none;
    padding: 0 16px; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
    display: inline-flex; align-items: center; gap: 6px;
    white-space: nowrap;
  }
  .ld-action-btn:disabled { opacity: 0.5; cursor: not-allowed; }
  .ld-action-btn:hover:not(:disabled) { filter: brightness(1.08); }
  .ld-action-btn.btn-quote { background: #f59e0b; color: #fff; }
  .ld-action-btn.btn-agreement { background: #22c55e; color: #fff; }
  .ld-action-btn.btn-edit { background: rgb(var(--primary-rgb)); color: #fff; }

  .ld-avatar { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 800; flex-shrink: 0; }
  .ld-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 8px; }

  /* Email history table */
  .ld-email-table { width: 100%; border-collapse: collapse; }
  .ld-email-table th {
    text-align: left; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em;
    color: var(--ld-muted); padding: 10px 16px;
    border-bottom: 1px solid var(--ld-border);
    white-space: nowrap;
  }
  .ld-email-table td {
    padding: 12px 16px; font-size: 13px; color: var(--ld-text);
    border-bottom: 1px solid rgba(15,23,42,0.04);
    vertical-align: middle;
  }
  .dark .ld-email-table td { border-bottom-color: rgba(255,255,255,0.04); }
  .ld-email-table tbody tr { transition: background 0.1s; }
  .ld-email-table tbody tr:hover { background: rgba(var(--primary-rgb), 0.03); }
  .ld-email-table tbody tr:last-child td { border-bottom: none; }

  .ld-email-status {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 8px;
  }
  .ld-email-status.sent { background: rgba(34,197,94,0.10); color: #16a34a; }
  .ld-email-status.draft { background: rgba(245,158,11,0.10); color: #d97706; }
  .ld-email-status.failed { background: rgba(239,68,68,0.10); color: #dc2626; }
  .ld-email-status.archived { background: rgba(107,114,128,0.10); color: #6b7280; }

  .ld-email-subject { font-weight: 600; cursor: pointer; transition: color 0.15s; }
  .ld-email-subject:hover { color: rgb(var(--primary-rgb)); }

  .ld-email-empty {
    text-align: center; padding: 40px 20px; color: var(--ld-muted);
  }
  .ld-email-skeleton { height: 52px; border-radius: 8px; margin-bottom: 8px; }
  .ld-email-skeleton:last-child { margin-bottom: 0; }
  .ld-skeleton-anim {
    background: linear-gradient(90deg, var(--ld-surface-2) 25%, rgba(var(--primary-rgb),0.04) 50%, var(--ld-surface-2) 75%);
    background-size: 200% 100%; animation: ld-shimmer 1.5s infinite;
  }
  @keyframes ld-shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

  /* Email preview modal */
  .ld-email-modal-body { max-height: 400px; overflow-y: auto; padding: 20px; font-size: 14px; line-height: 1.7; color: var(--ld-text); }
</style>

<div class="main-content app-content">
  <div class="container-fluid ld-page">

    <!-- Sticky Header -->
    <div class="ld-sticky-header" id="ld-sticky-header">
      <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
          <a href="leads.php" class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary hover:bg-primary/20 transition-colors flex-shrink-0" title="Back to Leads">
            <i class="ri-arrow-left-line text-lg"></i>
          </a>
          <div class="ld-avatar bg-primary/10 text-primary">
            <?php echo lead_text(lead_initials($leadName)); ?>
          </div>
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h1 class="text-lg font-bold mb-0 leading-tight" style="color:var(--ld-text)">
                <?php echo $leadName !== '' ? lead_text($leadName) : 'Lead Details'; ?>
              </h1>
              <?php if (!empty($leadStatus)): ?>
                <span class="ld-badge bg-primary/10 text-primary <?php if (!empty($leadStatus) && $leadStatus == 'Converted'): ?>bg-success/10 text-success<?php endif; ?>"><?php echo lead_text($leadStatus); ?></span>
              <?php endif; ?>
              <?php if (!empty($lead['service_type_c'])): ?>
                <span class="ld-badge bg-secondary/10 text-secondary"><?php echo lead_text($lead['service_type_c']); ?></span>
              <?php endif; ?>
              <span style="color:var(--ld-muted);font-size:12px;font-weight:500;">Lead #<?php echo lead_text($leadId); ?></span>
            </div>
            <div class="flex items-center gap-3 mt-0.5 flex-wrap" style="font-size:12px;color:var(--ld-muted);">
              <?php if (!empty($lead['email1'])): ?>
                <a class="inline-flex items-center gap-1 hover:text-primary transition-colors" href="mailto:<?php echo urlencode((string)$lead['email1']); ?>">
                  <i class="ri-mail-line"></i><?php echo lead_text($lead['email1']); ?>
                </a>
              <?php endif; ?>
              <?php if (!empty($lead['phone_c'])): ?>
                <span style="opacity:.4">|</span>
                <a class="inline-flex items-center gap-1 hover:text-primary transition-colors" href="tel:<?php echo urlencode((string)$lead['phone_c']); ?>">
                  <i class="ri-phone-line"></i><?php echo lead_text($lead['phone_c']); ?>
                </a>
              <?php endif; ?>
              <?php if (!empty($lead['event_date_c'])): ?>
                <span style="opacity:.4">|</span>
                <span class="inline-flex items-center gap-1"><i class="ri-calendar-event-line"></i><?php echo lead_text($lead['event_date_c']); ?></span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Leads', 'update') == 1): ?>
        <div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
          <button type="button" id="send-formal-quote-btn" class="ld-action-btn btn-quote" data-email-type="formal_quote" data-email-label="Formal Quote" <?php echo $hasEmail ? '' : 'disabled title="No email on file"'; ?>>
            <i class="ri-mail-send-line"></i> Send Formal Quote
          </button>
          <button type="button" id="send-agreement-btn" class="ld-action-btn btn-agreement" data-email-type="agreement" data-email-label="Agreement" <?php echo $hasEmail ? '' : 'disabled title="No email on file"'; ?>>
            <i class="ri-file-text-line"></i> Send Agreement
          </button>
          <!-- <button type="button" id="copy-agreement-link-btn" class="ld-action-btn" title="Public page: sign &amp; pay (Stripe)" style="background:#4f46e5;color:#fff;">
            <i class="ri-links-line"></i> Agreement link
          </button> -->
          <a href="edit_lead.php?id=<?php echo urlencode((string)$leadId); ?>" class="ld-action-btn btn-edit">
            <i class="ri-edit-line"></i> Edit Lead
          </a>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Main Content: 2-column layout -->
    <div class="grid grid-cols-12 gap-6">

      <!-- LEFT: Client + Trip + Addresses -->
      <div class="col-span-12 xl:col-span-8">
        <div class="grid grid-cols-12 gap-6">

          <!-- Client Details -->
          <div class="col-span-12 md:col-span-6">
            <div class="ld-card" style="height:100%;">
              <div class="ld-card-header">
                <div class="flex items-center gap-3">
                  <div class="ld-card-icon bg-primary/10 text-primary"><i class="ri-user-3-line"></i></div>
                  <div class="font-semibold text-sm" style="color:var(--ld-text)">Client Details</div>
                </div>
              </div>
              <div class="ld-card-body">
                <div class="ld-row">
                  <span class="ld-row-label"><i class="ri-user-line"></i> Name</span>
                  <span class="ld-row-value"><?php echo lead_text($leadName); ?></span>
                </div>
                <div class="ld-row">
                  <span class="ld-row-label"><i class="ri-mail-line"></i> Email</span>
                  <span class="ld-row-value">
                    <?php if (!empty($lead['email1'])): ?>
                      <a href="mailto:<?php echo urlencode((string)$lead['email1']); ?>"><?php echo lead_text($lead['email1']); ?></a>
                    <?php else: ?>
                      <span style="color:var(--ld-muted)">—</span>
                    <?php endif; ?>
                  </span>
                </div>
                <div class="ld-row">
                  <span class="ld-row-label"><i class="ri-phone-line"></i> Phone</span>
                  <span class="ld-row-value">
                    <?php if (!empty($lead['phone_c'])): ?>
                      <a href="tel:<?php echo urlencode((string)$lead['phone_c']); ?>"><?php echo lead_text($lead['phone_c']); ?></a>
                    <?php else: ?>
                      <span style="color:var(--ld-muted)">—</span>
                    <?php endif; ?>
                  </span>
                </div>
                <div class="ld-row">
                  <span class="ld-row-label"><i class="ri-flag-line"></i> Status</span>
                  <span class="ld-row-value">
                    <?php if (!empty($leadStatus)): ?>
                      <span class="ld-badge bg-primary/10 text-primary"><?php echo lead_text($leadStatus); ?></span>
                    <?php else: ?>
                      <span style="color:var(--ld-muted)">—</span>
                    <?php endif; ?>
                  </span>
                </div>
                <div class="ld-row">
                  <span class="ld-row-label"><i class="ri-global-line"></i> Lead Source</span>
                  <span class="ld-row-value">
                    <?php if (!empty($lead['lead_source'])): ?>
                      <span class="ld-badge bg-info/10 text-info"><i class="ri-global-line" style="font-size:10px;margin-right:2px;"></i> <?php echo lead_text($lead['lead_source']); ?></span>
                    <?php else: ?>
                      <span style="color:var(--ld-muted)">—</span>
                    <?php endif; ?>
                  </span>
                </div>
                <div class="ld-row">
                  <span class="ld-row-label"><i class="ri-user-star-line"></i> Assigned To</span>
                  <span class="ld-row-value">
                    <?php
                      $assignedName = trim((string)($lead['assigned_user_name'] ?? ''));
                      if ($assignedName !== ''):
                    ?>
                      <span class="ld-badge bg-secondary/10 text-secondary"><i class="ri-user-star-line" style="font-size:10px;margin-right:2px;"></i> <?php echo lead_text($assignedName); ?></span>
                    <?php else: ?>
                      <span style="color:var(--ld-muted)">— Unassigned —</span>
                    <?php endif; ?>
                  </span>
                </div>
                <div class="ld-row">
                  <span class="ld-row-label"><i class="ri-time-line"></i> Created</span>
                  <span class="ld-row-value"><?php echo lead_text($lead['date_entered'] ?? '—'); ?></span>
                </div>

                <?php if (!empty($lead['description'])): ?>
                <div style="margin-top:16px;padding-top:16px;border-top:1px dashed var(--ld-border);">
                  <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--ld-muted);margin-bottom:6px;display:flex;align-items:center;gap:6px;">
                    <i class="ri-file-text-line"></i> Description
                  </div>
                  <div style="font-size:13px;color:var(--ld-text);line-height:1.6;white-space:pre-line;"><?php echo lead_text($lead['description']); ?></div>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Trip Details -->
          <div class="col-span-12 md:col-span-6">
            <div class="ld-card" style="height:100%;">
              <div class="ld-card-header">
                <div class="flex items-center gap-3">
                  <div class="ld-card-icon bg-info/10 text-info"><i class="ri-road-map-line"></i></div>
                  <div class="font-semibold text-sm" style="color:var(--ld-text)">Trip Details</div>
                </div>
              </div>
              <div class="ld-card-body">
                <div class="ld-row">
                  <span class="ld-row-label"><i class="ri-briefcase-line"></i> Service</span>
                  <span class="ld-row-value"><?php echo lead_text($lead['service_type_c'] ?? '—'); ?></span>
                </div>
                <div class="ld-row">
                  <span class="ld-row-label"><i class="ri-calendar-event-line"></i> Event Date</span>
                  <span class="ld-row-value"><?php echo lead_text($lead['event_date_c'] ?? '—'); ?></span>
                </div>
                <div class="ld-row">
                  <span class="ld-row-label"><i class="ri-group-line"></i> Passengers</span>
                  <span class="ld-row-value"><?php echo lead_text($lead['passengers_c'] ?? '—'); ?></span>
                </div>
                <div class="ld-row">
                  <span class="ld-row-label"><i class="ri-route-line"></i> Distance</span>
                  <span class="ld-row-value"><?php echo lead_text($lead['distance_c'] ?? '—'); ?></span>
                </div>
                <div class="ld-row">
                  <span class="ld-row-label"><i class="ri-timer-line"></i> Duration</span>
                  <span class="ld-row-value"><?php echo lead_text($lead['duration_c'] ?? '—'); ?></span>
                </div>
                <div class="ld-row">
                  <span class="ld-row-label"><i class="ri-time-line"></i> Service Length</span>
                  <span class="ld-row-value"><?php echo $serviceLengthNum > 0 ? $serviceLengthNum . ' hrs' : '—'; ?></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Addresses (full width below the two cards) -->
          <div class="col-span-12">
            <div class="ld-card">
              <div class="ld-card-header">
                <div class="flex items-center gap-3">
                  <div class="ld-card-icon bg-success/10 text-success"><i class="ri-map-pin-line"></i></div>
                  <div class="font-semibold text-sm" style="color:var(--ld-text)">Addresses</div>
                </div>
              </div>
              <div class="ld-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                  <div class="ld-addr-block" style="margin-bottom:0;">
                    <div class="ld-addr-label" style="color:#22c55e;"><i class="ri-map-pin-2-fill"></i> Pickup</div>
                    <div class="ld-addr-text"><?php echo lead_text($lead['pickup_address_c'] ?? '—'); ?></div>
                  </div>
                  <div class="ld-addr-block" style="margin-bottom:0;">
                    <div class="ld-addr-label" style="color:#ef4444;"><i class="ri-map-pin-2-fill"></i> Dropoff</div>
                    <div class="ld-addr-text"><?php echo lead_text($lead['dropoff_address_c'] ?? '—'); ?></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Notes (full width, if present) -->
          <?php if (!empty($lead['notes_c'])): ?>
          <div class="col-span-12">
            <div class="ld-notes-block">
              <div class="ld-notes-label"><i class="ri-sticky-note-line"></i> Notes / Special Requests</div>
              <div class="ld-notes-text"><?php echo lead_text($lead['notes_c']); ?></div>
            </div>
          </div>
          <?php endif; ?>

        </div>
      </div>

      <!-- RIGHT: Price Breakdown -->
      <div class="col-span-12 xl:col-span-4">
        <div class="ld-card" style="position:sticky;top:80px;">
          <div class="ld-card-header">
            <div class="flex items-center gap-3">
              <div class="ld-card-icon bg-warning/10 text-warning"><i class="ri-currency-line"></i></div>
              <div class="font-semibold text-sm" style="color:var(--ld-text)">Price Breakdown</div>
            </div>
          </div>
          <div class="ld-card-body">
            <div class="ld-total-box" style="background:rgba(var(--primary-rgb),0.06);">
              <div class="ld-total-amount" style="color:rgb(var(--primary-rgb));"><?php echo lead_money($tripPrice); ?></div>
              <div class="ld-total-label" style="color:var(--ld-muted);">Total Trip Cost</div>
              <?php if ($hasMismatch): ?>
                <div style="font-size:11px;color:#f59e0b;margin-top:8px;display:flex;align-items:center;justify-content:center;gap:4px;">
                  <i class="ri-information-line"></i> Line items add up to <?php echo lead_money($sumOfParts); ?>
                </div>
              <?php endif; ?>
            </div>

            <div style="border:1px solid var(--ld-border);border-radius:14px;overflow:hidden;">
              <div class="ld-price-row">
                <span class="ld-price-label"><i class="ri-cash-line text-primary"></i> Quoted Price</span>
                <span class="ld-price-val"><?php echo $hasCalc ? lead_money($quotedPrice) : '—'; ?></span>
              </div>
              <?php if ($hasCalc): ?>
              <div style="padding:0 16px 12px 40px;">
                <span style="font-size:11px;color:var(--ld-muted);">
                  <?php echo rtrim(rtrim(number_format($serviceLengthNum, 2), '0'), '.'); ?> hrs
                  &times;
                  <?php echo lead_money($rateNum); ?>/hr
                </span>
              </div>
              <?php endif; ?>
              <div class="ld-price-row">
                <span class="ld-price-label"><i class="ri-gas-station-line text-warning"></i> Fuel Surcharge</span>
                <span class="ld-price-val" <?php echo $fuelNum <= 0 ? 'style="color:var(--ld-muted);font-weight:400;"' : ''; ?>>
                  <?php echo $fuelNum > 0 ? '+ ' . lead_money($fuelNum) : '—'; ?>
                </span>
              </div>
              <div class="ld-price-row">
                <span class="ld-price-label"><i class="ri-steering-2-line text-secondary"></i> Driver Commission</span>
                <span class="ld-price-val" <?php echo $commissionNum <= 0 ? 'style="color:var(--ld-muted);font-weight:400;"' : ''; ?>>
                  <?php echo $commissionNum > 0 ? '+ ' . lead_money($commissionNum) : '—'; ?>
                </span>
              </div>
              <div class="ld-price-row" style="background:rgba(var(--primary-rgb),0.04);">
                <span class="ld-price-label" style="font-weight:800;"><i class="ri-checkbox-circle-line text-success"></i> Total</span>
                <span class="ld-price-val" style="font-size:16px;color:rgb(var(--primary-rgb));"><?php echo lead_money($tripPrice); ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Email History -->
    <div class="ld-card mt-6 mb-6">
      <div class="ld-card-header">
        <div class="flex items-center justify-between flex-wrap gap-3">
          <div class="flex items-center gap-3">
            <div class="ld-card-icon bg-info/10 text-info"><i class="ri-mail-line"></i></div>
            <div>
              <div class="font-semibold text-sm" style="color:var(--ld-text)">Email History</div>
              <div style="font-size:11px;color:var(--ld-muted);">All emails sent for this lead</div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span id="ld-email-count" class="ld-badge bg-primary/10 text-primary" style="display:none;">0 emails</span>
            <button type="button" onclick="loadLeadEmails()" style="background:none;border:1px solid var(--ld-border);border-radius:8px;padding:6px 12px;font-size:12px;font-weight:600;color:var(--ld-muted);cursor:pointer;display:inline-flex;align-items:center;gap:4px;transition:all 0.15s;" onmouseover="this.style.borderColor='rgba(var(--primary-rgb),0.3)';this.style.color='rgb(var(--primary-rgb))'" onmouseout="this.style.borderColor='var(--ld-border)';this.style.color='var(--ld-muted)'">
              <i class="ri-refresh-line"></i> Refresh
            </button>
          </div>
        </div>
      </div>
      <div class="ld-card-body" style="padding:0;">
        <div id="ld-email-loading" style="padding:24px;">
          <div class="ld-email-skeleton ld-skeleton-anim"></div>
          <div class="ld-email-skeleton ld-skeleton-anim"></div>
          <div class="ld-email-skeleton ld-skeleton-anim"></div>
        </div>
        <div id="ld-email-empty" class="ld-email-empty" style="display:none;">
          <i class="ri-mail-line text-4xl mb-2" style="display:block;opacity:0.2;"></i>
          <div style="font-size:14px;font-weight:600;margin-bottom:4px;">No emails yet</div>
          <div style="font-size:12px;">Emails sent to this lead will appear here.</div>
        </div>
        <div id="ld-email-error" style="display:none;text-align:center;padding:30px;color:#ef4444;font-size:13px;">
          <i class="ri-error-warning-line text-lg"></i>
          <div style="margin-top:6px;">Failed to load emails. Please try refreshing.</div>
        </div>
        <div class="overflow-auto" id="ld-email-table-wrap" style="display:none;">
          <table class="ld-email-table">
            <thead>
              <tr>
                <th>Subject</th>
                <th>To</th>
                <th>From</th>
                <th>Status</th>
                <th>Date Sent</th>
                <th style="text-align:center;">Actions</th>
              </tr>
            </thead>
            <tbody id="ld-email-tbody"></tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function () {
  var LEAD_PAYLOAD = {
    lead_id: <?php echo json_encode((string)$leadId); ?>,
    email:   <?php echo json_encode($leadEmail); ?>,
    name:    <?php echo json_encode($leadName); ?>
  };

  function sendLeadEmail($btn) {
    var emailType  = $btn.data('email-type');
    var emailLabel = $btn.data('email-label');

    if (!LEAD_PAYLOAD.email) {
      Swal.fire({ icon: 'warning', title: 'No email on file', text: 'Add an email address to this lead before sending a ' + emailLabel + '.' });
      return;
    }

    Swal.fire({
      icon: 'question',
      title: 'Send ' + emailLabel + '?',
      html: 'This will email the <b>' + emailLabel + '</b> to <b>' + $('<div>').text(LEAD_PAYLOAD.email).html() + '</b>.',
      showCancelButton: true,
      confirmButtonText: 'Send ' + emailLabel,
      cancelButtonText: 'Cancel',
      confirmButtonColor: emailType === 'agreement' ? '#22c55e' : '#f59e0b'
    }).then(function (res) {
      if (!res.isConfirmed) return;

      if (!$btn.data('original-html')) $btn.data('original-html', $btn.html());
      $btn.html('<span class="spinner-border spinner-border-sm align-middle me-1"></span> Sending...').prop('disabled', true);

      $.ajax({
        url: 'config/send_lead_email_endpoint.php',
        type: 'POST',
        dataType: 'json',
        data: $.extend({}, LEAD_PAYLOAD, { email_type: emailType }),
        success: function (data) {
          if (data && data.success) {
            Swal.fire({ icon: 'success', title: emailLabel + ' sent', text: data.message || 'The ' + emailLabel + ' email has been queued.', timer: 2200, showConfirmButton: false });
           
          } else {
            Swal.fire({ icon: 'error', title: 'Could not send ' + emailLabel, text: (data && data.message) || 'Please try again.' });
          }
        },
        error: function (xhr) {
          console.error(xhr.responseText);
          Swal.fire({ icon: 'error', title: 'Network error', text: 'Could not reach the server. Please try again.' });
        },
        complete: function () {
          $btn.html($btn.data('original-html')).prop('disabled', false);
           window.location.reload();
        }
      });
    });
  }

  $('#send-formal-quote-btn, #send-agreement-btn').on('click', function () { sendLeadEmail($(this)); });

  // $('#copy-agreement-link-btn').on('click', function () {
  //   $.post('config/get_agreement_link_endpoint.php', { lead_id: LEAD_PAYLOAD.lead_id })
  //     .done(function (data) {
  //       if (typeof data === 'string') { try { data = JSON.parse(data); } catch (e) { data = {}; } }
  //       if (!data || !data.success) {
  //         Swal.fire({ icon: 'error', title: 'Could not build link', text: (data && data.message) || 'Check SuiteCRM limo_agreement_config.php and lead access.' });
  //         return;
  //       }
  //       var url = data.url;
  //       function showUrl() {
  //         Swal.fire({ title: 'Agreement link', html: 'Send this URL to your client:<br><textarea readonly style="width:100%;min-height:84px;margin-top:10px;font-size:12px;border-radius:8px;padding:8px;">' + $('<div>').text(url).html() + '</textarea>', icon: 'info' });
  //       }
  //       if (navigator.clipboard && navigator.clipboard.writeText) {
  //         navigator.clipboard.writeText(url).then(function () {
  //           Swal.fire({ icon: 'success', title: 'Copied', text: 'Agreement link copied to clipboard.', timer: 2200, showConfirmButton: false });
  //         }).catch(showUrl);
  //       } else {
  //         showUrl();
  //       }
  //     })
  //     .fail(function () {
  //       Swal.fire({ icon: 'error', title: 'Error', text: 'Could not reach the server.' });
  //     });
  // });

  /* ── Email History ──────────────────────────────── */
  function escHtml(str) { return $('<span>').text(str || '').html(); }

  function formatDate(d) {
    if (!d) return '—';
    var date = new Date(d);
    if (isNaN(date.getTime())) return d;
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) +
           ' ' + date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
  }

  function statusClass(s) {
    s = (s || '').toLowerCase();
    if (s === 'sent') return 'sent';
    if (s === 'draft') return 'draft';
    if (s === 'send_error' || s === 'failed') return 'failed';
    return 'archived';
  }

  window.loadLeadEmails = function () {
    $('#ld-email-loading').show();
    $('#ld-email-empty, #ld-email-error, #ld-email-table-wrap').hide();
    $('#ld-email-count').hide();

    $.post('https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint', {
      action: 'fetch_lead_emails',
      lead_id: LEAD_PAYLOAD.lead_id
    }, function (resp) {
      if (typeof resp === 'string') { try { resp = JSON.parse(resp); } catch(e) { resp = {}; } }
      $('#ld-email-loading').hide();

      var emails = (resp && resp.emails) ? resp.emails : [];
      if (!emails.length) {
        $('#ld-email-empty').show();
        return;
      }

      $('#ld-email-count').text(emails.length + (emails.length === 1 ? ' email' : ' emails')).show();

      var tbody = $('#ld-email-tbody');
      tbody.empty();

      emails.forEach(function (em) {
        var sc = statusClass(em.status);
        var statusLabel = (em.status || 'unknown').charAt(0).toUpperCase() + (em.status || 'unknown').slice(1);
        var dateSent = em.date_sent_received || em.date_sent || em.date_entered || '';
        var emailUrl = 'email_detail.php?id=' + encodeURIComponent(em.id) + '&lead_id=' + encodeURIComponent(LEAD_PAYLOAD.lead_id);
        var typeIcon = em.type === 'out' ? 'ri-send-plane-line' : 'ri-mail-download-line';
        var typeColor = em.type === 'out' ? 'color:#3b82f6' : 'color:#8b5cf6';

        var row = $(
          '<tr>' +
            '<td>' +
              '<a href="' + emailUrl + '" class="ld-email-subject" style="text-decoration:none;">' +
                '<i class="' + typeIcon + '" style="' + typeColor + ';margin-right:6px;font-size:14px;vertical-align:middle;"></i>' +
                escHtml(em.name || '(No subject)') +
              '</a>' +
            '</td>' +
            '<td style="font-size:12px;color:var(--ld-muted);">' + escHtml(em.to_addrs) + '</td>' +
            '<td style="font-size:12px;color:var(--ld-muted);">' + escHtml(em.from_addr) + '</td>' +
            '<td><span class="ld-email-status ' + sc + '"><i class="ri-circle-fill" style="font-size:6px;"></i> ' + escHtml(statusLabel) + '</span></td>' +
            '<td style="font-size:12px;white-space:nowrap;">' + formatDate(dateSent) + '</td>' +
            '<td style="text-align:center;">' +
              '<a href="' + emailUrl + '" style="background:none;border:1px solid var(--ld-border);border-radius:8px;padding:5px 10px;font-size:11px;font-weight:600;color:var(--ld-muted);cursor:pointer;transition:all 0.15s;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">' +
                '<i class="ri-eye-line"></i> View' +
              '</a>' +
            '</td>' +
          '</tr>'
        );
        tbody.append(row);
      });

      $('#ld-email-table-wrap').show();

    }, 'json').fail(function () {
      $('#ld-email-loading').hide();
      $('#ld-email-error').show();
    });
  };

  loadLeadEmails();

  /* ── Sticky header ─────────────────────────────── */
  var stickyHeader = document.getElementById('ld-sticky-header');
  if (stickyHeader) {
    var scrollParent = stickyHeader.closest('.app-content') || window;
    (scrollParent === window ? window : scrollParent).addEventListener('scroll', function () {
      stickyHeader.classList.toggle('scrolled', (scrollParent === window ? window.scrollY : scrollParent.scrollTop) > 10);
    });
  }
});
</script>
