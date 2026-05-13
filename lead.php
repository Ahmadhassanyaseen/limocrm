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

/** @return list<string> */
function lead_parse_vehicle_image_urls($raw): array {
  $raw = (string)($raw ?? '');
  if ($raw === '') {
    return [];
  }
  $parts = preg_split('/\s*,\s*/', $raw) ?: [];
  $out = [];
  foreach ($parts as $p) {
    $p = trim($p);
    if ($p !== '' && preg_match('#^https?://#i', $p)) {
      $out[] = $p;
    }
  }
  return $out;
}

/** @return list<string> */
function lead_parse_facilities($raw): array {
  $raw = (string)($raw ?? '');
  if ($raw === '') {
    return [];
  }
  $parts = preg_split('/\s*,\s*/', $raw) ?: [];
  return array_values(array_filter(array_map('trim', $parts)));
}

function lead_fmt_vehicle_pct($n): string {
  if ($n === null || $n === '' || !is_numeric($n)) {
    return '—';
  }
  $f = (float)$n;

  return rtrim(rtrim(number_format($f, 2), '0'), '.') . '%';
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

$vehicleDisplayName = trim((string)($lead['vehicle_name'] ?? ''));
$vehicleFuelPctV      = isset($lead['vehicle_fuel_c']) && is_numeric($lead['vehicle_fuel_c']) ? (float)$lead['vehicle_fuel_c'] : null;
$vehicleDriverPctV   = isset($lead['vehicle_driver_commission_c']) && is_numeric($lead['vehicle_driver_commission_c']) ? (float)$lead['vehicle_driver_commission_c'] : null;
$vehicleImageUrls    = lead_parse_vehicle_image_urls($lead['vehicle_images_c'] ?? '');
$vehicleFacilities   = lead_parse_facilities($lead['vehicle_facilities_c'] ?? '');
$showVehicleCard     = ($vehicleDisplayName !== ''
  || $vehicleFuelPctV !== null
  || $vehicleDriverPctV !== null
  || count($vehicleImageUrls) > 0
  || count($vehicleFacilities) > 0
  || !empty($lead['vehicle_id_c']));
?>

<style>
/* ============================================
   MODERN LEAD DETAIL PAGE - CLEAN & RESPONSIVE
   ============================================ */
:root {
  --ld-primary: #4f46e5;
  --ld-primary-light: #6366f1;
  --ld-success: #10b981;
  --ld-warning: #f59e0b;
  --ld-danger: #ef4444;
  --ld-info: #0ea5e9;
  --ld-surface: #ffffff;
  --ld-surface-alt: #f9fafb;
  --ld-border: #e5e7eb;
  --ld-text: #111827;
  --ld-text-secondary: #6b7280;
  --ld-text-muted: #9ca3af;
  --ld-radius-sm: 8px;
  --ld-radius-md: 12px;
  --ld-radius-lg: 16px;
}

.main-content{
  padding-left: 0!important;
  padding-right: 0!important;
  margin-top: 0!important;
}

.dark {
  --ld-surface: #101520;
  --ld-surface-alt: #111827;
  --ld-border: #374151;
  --ld-text: #f9fafb;
  --ld-text-secondary: #d1d5db;
  --ld-text-muted: #9ca3af;
}

/* RESET & BASE */
.ld-page * { box-sizing: border-box; }
.ld-page {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  background: var(--ld-surface-alt);
  color: var(--ld-text);
  line-height: 1.5;
}

/* MODERN CARD SYSTEM */
.ld-card {
  background: var(--ld-surface);
  border: 1px solid var(--ld-border);
  border-radius: var(--ld-radius-lg);
  overflow: hidden;
  transition: all 0.2s ease;
}

.ld-card:hover {
  border-color: var(--ld-primary-light);
  box-shadow: 0 4px 20px rgba(79, 70, 229, 0.08);
}

.ld-card-header {
  background: var(--ld-surface-alt);
  border-bottom: 1px solid var(--ld-border);
  padding: 14px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.ld-card-header-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--ld-text);
  display: flex;
  align-items: center;
  gap: 8px;
}

.ld-card-body {
  padding: 16px 20px;
}

/* CARD ICON */
.ld-icon {
  width: 36px;
  height: 36px;
  border-radius: var(--ld-radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
}

.ld-icon-primary { background: rgba(79, 70, 229, 0.1); color: var(--ld-primary); }
.ld-icon-success { background: rgba(16, 185, 129, 0.1); color: var(--ld-success); }
.ld-icon-warning { background: rgba(245, 158, 11, 0.1); color: var(--ld-warning); }
.ld-icon-info { background: rgba(14, 165, 233, 0.1); color: var(--ld-info); }
.ld-icon-danger { background: rgba(239, 68, 68, 0.1); color: var(--ld-danger); }

/* DATA ROWS */
.ld-data-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid var(--ld-border);
  gap: 12px;
}

.ld-data-row:last-child { border-bottom: none; }

.ld-data-label {
  font-size: 13px;
  color: var(--ld-text-secondary);
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
}

.ld-data-value {
  font-size: 13px;
  color: var(--ld-text);
  font-weight: 600;
  text-align: right;
  word-break: break-word;
}

.ld-data-value a {
  color: var(--ld-primary);
  text-decoration: none;
  transition: color 0.15s;
}

.ld-data-value a:hover { color: var(--ld-primary-light); }

/* BADGES */
.ld-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 6px;
  background: rgba(79, 70, 229, 0.1);
  color: var(--ld-primary);
}

.ld-badge-success { background: rgba(16, 185, 129, 0.1); color: var(--ld-success); }
.ld-badge-warning { background: rgba(245, 158, 11, 0.1); color: var(--ld-warning); }
.ld-badge-info { background: rgba(14, 165, 233, 0.1); color: var(--ld-info); }

/* STICKY HEADER */
.ld-header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: var(--ld-surface);
  border-bottom: 1px solid var(--ld-border);
  padding: 12px 16px;
  /* margin: -16px -16px 16px -16px; */
  margin-bottom: 20px;
  backdrop-filter: blur(8px);
}

.ld-header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}

.ld-header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.ld-header-right {
  display: flex;
  align-items: center;
  gap: 8px;
}

.ld-back-btn {
  width: 36px;
  height: 36px;
  border-radius: var(--ld-radius-sm);
  background: rgba(79, 70, 229, 0.1);
  color: var(--ld-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: all 0.2s;
}

.ld-back-btn:hover {
  background: rgba(79, 70, 229, 0.2);
  transform: translateX(-2px);
}

.ld-avatar {
  width: 44px;
  height: 44px;
  border-radius: var(--ld-radius-md);
  background: linear-gradient(135deg, var(--ld-primary), var(--ld-primary-light));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
}

.ld-lead-info h1 {
  font-size: 16px;
  font-weight: 700;
  color: var(--ld-text);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.ld-lead-meta {
  font-size: 12px;
  color: var(--ld-text-secondary);
  margin-top: 2px;
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.ld-lead-meta a {
  color: var(--ld-text-secondary);
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 4px;
  transition: color 0.15s;
}

.ld-lead-meta a:hover { color: var(--ld-primary); }

/* ACTION BUTTONS */
.ld-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  font-size: 12px;
  font-weight: 600;
  border-radius: var(--ld-radius-sm);
  border: none;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.ld-btn:hover {
  transform: translateY(-1px);
  filter: brightness(1.05);
}

.ld-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.ld-btn-primary { background: var(--ld-warning); color: white; }
.ld-btn-success { background: var(--ld-success); color: white; }
.ld-btn-edit { background: var(--ld-primary); color: white; }

/* GRID LAYOUT - NO GAPS */
.ld-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
  padding: 0 20px;
}

@media (min-width: 768px) {
  .ld-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (min-width: 1280px) {
  .ld-grid { grid-template-columns: 2fr 1fr; }
}

.ld-grid > .ld-card {
  margin-bottom: 0;
  border-radius: 0;
}

.ld-grid > .ld-card:first-child { border-top-left-radius: var(--ld-radius-lg); }
.ld-grid > .ld-card:first-child + .ld-card { border-top-right-radius: var(--ld-radius-lg); }
@media (max-width: 767px) {
  .ld-grid > .ld-card { border-radius: var(--ld-radius-lg); margin-bottom: 12px; }
  .ld-grid > .ld-card:last-child { margin-bottom: 0; }
}

/* ADDRESS CARDS */
.ld-address-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 12px;
}

@media (min-width: 640px) {
  .ld-address-grid { grid-template-columns: 1fr 1fr; }
}

.ld-address-card {
  background: var(--ld-surface-alt);
  border-radius: var(--ld-radius-md);
  padding: 14px 16px;
  border: 1px solid var(--ld-border);
}

.ld-address-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 6px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.ld-address-label.pickup { color: var(--ld-success); }
.ld-address-label.dropoff { color: var(--ld-danger); }

.ld-address-text {
  font-size: 13px;
  color: var(--ld-text);
  line-height: 1.5;
}

/* PRICE CARD */
.ld-price-total {
  background: linear-gradient(135deg, rgba(79, 70, 229, 0.08), rgba(99, 102, 241, 0.04));
  border-radius: var(--ld-radius-md);
  padding: 20px;
  text-align: center;
  margin-bottom: 16px;
}

.ld-price-amount {
  font-size: 32px;
  font-weight: 800;
  color: var(--ld-primary);
  letter-spacing: -0.02em;
}

.ld-price-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--ld-text-secondary);
  margin-top: 4px;
}

.ld-price-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 16px;
  border-bottom: 1px solid var(--ld-border);
}

.ld-price-row:last-child { border-bottom: none; }
.ld-price-row:last-child { background: rgba(79, 70, 229, 0.04); font-weight: 700; }

.ld-price-row-label {
  font-size: 13px;
  color: var(--ld-text-secondary);
  display: flex;
  align-items: center;
  gap: 8px;
}

.ld-price-row-value {
  font-size: 13px;
  font-weight: 600;
  color: var(--ld-text);
}

/* NOTES CARD */
.ld-notes-card {
  border-color: rgba(245, 158, 11, 0.2);
}

.ld-notes-card .ld-card-header {
  background: rgba(245, 158, 11, 0.05);
  border-bottom-color: rgba(245, 158, 11, 0.1);
}

.ld-notes-text {
  font-size: 13px;
  color: var(--ld-text);
  line-height: 1.6;
  white-space: pre-line;
}

/* VEHICLE CARD */
.ld-vehicle-title {
  font-size: 18px;
  font-weight: 700;
  color: var(--ld-text);
  margin-bottom: 4px;
}

.ld-vehicle-id {
  font-size: 11px;
  color: var(--ld-text-muted);
  font-family: monospace;
  margin-bottom: 12px;
}

.ld-vehicle-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 12px;
}

.ld-vehicle-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 8px;
  background: var(--ld-surface-alt);
  border: 1px solid var(--ld-border);
  color: var(--ld-text);
}

.ld-vehicle-gallery {
  border-radius: var(--ld-radius-md);
  overflow: hidden;
  aspect-ratio: 16/10;
  background: var(--ld-surface-alt);
  border: 1px solid var(--ld-border);
}

.ld-vehicle-gallery img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.ld-vehicle-thumbs {
  display: flex;
  gap: 8px;
  margin-top: 10px;
  flex-wrap: wrap;
}

.ld-vehicle-thumb {
  width: 56px;
  height: 40px;
  border-radius: 6px;
  overflow: hidden;
  border: 2px solid transparent;
  cursor: pointer;
  transition: border-color 0.15s;
  padding: 0;
  background: none;
}

.ld-vehicle-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.ld-vehicle-thumb:hover,
.ld-vehicle-thumb.active {
  border-color: var(--ld-primary);
}

.ld-vehicle-facilities {
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px solid var(--ld-border);
}

.ld-facility-tag {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  padding: 4px 10px;
  border-radius: 6px;
  background: rgba(79, 70, 229, 0.08);
  color: var(--ld-text);
  margin: 0 4px 4px 0;
}

/* EMAIL HISTORY */
.ld-email-card {
  margin-top: 20px;
  margin-left: 20px;
  margin-right: 20px;
}

.ld-email-table {
  width: 100%;
  border-collapse: collapse;
}

.ld-email-table th {
  text-align: left;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--ld-text-secondary);
  padding: 12px 16px;
  border-bottom: 1px solid var(--ld-border);
  white-space: nowrap;
}

.ld-email-table td {
  padding: 12px 16px;
  font-size: 13px;
  color: var(--ld-text);
  border-bottom: 1px solid var(--ld-border);
  vertical-align: middle;
}

.ld-email-table tr:hover td {
  background: rgba(79, 70, 229, 0.02);
}

.ld-email-status {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 6px;
}

.ld-email-status.sent { background: rgba(16, 185, 129, 0.1); color: var(--ld-success); }
.ld-email-status.draft { background: rgba(245, 158, 11, 0.1); color: var(--ld-warning); }
.ld-email-status.failed { background: rgba(239, 68, 68, 0.1); color: var(--ld-danger); }
.ld-email-status.archived { background: rgba(107, 114, 128, 0.1); color: var(--ld-text-secondary); }

.ld-email-subject {
  font-weight: 600;
  color: var(--ld-text);
  text-decoration: none;
}

.ld-email-subject:hover {
  color: var(--ld-primary);
}

.ld-email-view-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 5px 10px;
  font-size: 11px;
  font-weight: 600;
  color: var(--ld-text-secondary);
  background: none;
  border: 1px solid var(--ld-border);
  border-radius: 6px;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.15s;
}

.ld-email-view-btn:hover {
  border-color: var(--ld-primary);
  color: var(--ld-primary);
}

.ld-email-empty {
  text-align: center;
  padding: 32px 20px;
  color: var(--ld-text-secondary);
}

.ld-email-empty i {
  font-size: 32px;
  opacity: 0.3;
  display: block;
  margin-bottom: 8px;
}

/* RESPONSIVE CONTAINER */
/* .ld-container {
  padding: 16px;
}

@media (min-width: 768px) {
  .ld-container { padding: 20px; }
}

@media (min-width: 1280px) {
  .ld-container { padding: 24px; }
} */

/* MOBILE OPTIMIZATION */
@media (max-width: 640px) {
  .ld-header {
    padding: 10px 12px;
    margin: -12px -12px 12px -12px;
  }

  .ld-header-content {
    flex-direction: column;
    align-items: flex-start;
  }

  .ld-header-right {
    width: 100%;
    justify-content: flex-start;
    flex-wrap: wrap;
  }

  .ld-btn {
    padding: 6px 10px;
    font-size: 11px;
  }

  .ld-lead-info h1 {
    font-size: 14px;
  }

  .ld-card-body {
    padding: 14px 16px;
  }

  .ld-data-row {
    padding: 8px 0;
  }

  .ld-price-amount {
    font-size: 26px;
  }

  .ld-email-table {
    display: block;
    overflow-x: auto;
  }
}

/* SMOOTH ANIMATIONS */
@keyframes ld-fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

.ld-card {
  animation: ld-fadeIn 0.3s ease forwards;
}

.ld-grid > .ld-card:nth-child(1) { animation-delay: 0s; }
.ld-grid > .ld-card:nth-child(2) { animation-delay: 0.05s; }
</style>

<div class="main-content app-content">
  <div class="ld-page">
    <div class="ld-container">

      <!-- Sticky Header -->
      <div class="ld-header">
        <div class="ld-header-content">
          <div class="ld-header-left">
            <a href="leads.php" class="ld-back-btn" title="Back to Leads">
              <i class="ri-arrow-left-line"></i>
            </a>
            <div class="ld-avatar">
              <?php echo lead_text(lead_initials($leadName)); ?>
            </div>
            <div class="ld-lead-info">
              <h1>
                <?php echo $leadName !== '' ? lead_text($leadName) : 'Lead Details'; ?>
                <?php if (!empty($leadStatus)): ?>
                  <span class="ld-badge <?php if (!empty($leadStatus) && $leadStatus == 'Converted'): ?>ld-badge-success<?php endif; ?>"><?php echo lead_text($leadStatus); ?></span>
                <?php endif; ?>
                <?php if (!empty($lead['service_type_c'])): ?>
                  <span class="ld-badge ld-badge-info"><?php echo lead_text($lead['service_type_c']); ?></span>
                <?php endif; ?>
              </h1>
              <div class="ld-lead-meta">
                <span>Lead #<?php echo lead_text($leadId); ?></span>
                <?php if (!empty($lead['email1'])): ?>
                  <span style="opacity:0.4">|</span>
                  <a href="mailto:<?php echo urlencode((string)$lead['email1']); ?>">
                    <i class="ri-mail-line"></i><?php echo lead_text($lead['email1']); ?>
                  </a>
                <?php endif; ?>
                <?php if (!empty($lead['phone_c'])): ?>
                  <span style="opacity:0.4">|</span>
                  <a href="tel:<?php echo urlencode((string)$lead['phone_c']); ?>">
                    <i class="ri-phone-line"></i><?php echo lead_text($lead['phone_c']); ?>
                  </a>
                <?php endif; ?>
                <?php if (!empty($lead['event_date_c'])): ?>
                  <span style="opacity:0.4">|</span>
                  <span><i class="ri-calendar-event-line"></i><?php echo lead_text($lead['event_date_c']); ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Leads', 'update') == 1): ?>
          <div class="ld-header-right">
            <button type="button" id="send-formal-quote-btn" class="ld-btn ld-btn-primary" data-email-type="formal_quote" data-email-label="Formal Quote" <?php echo $hasEmail ? '' : 'disabled title="No email on file"'; ?>>
              <i class="ri-mail-send-line"></i> Send Quote
            </button>
            <button type="button" id="send-agreement-btn" class="ld-btn ld-btn-success" data-email-type="agreement" data-email-label="Agreement" <?php echo $hasEmail ? '' : 'disabled title="No email on file"'; ?>>
              <i class="ri-file-text-line"></i> Send Agreement
            </button>
            <a href="edit_lead.php?id=<?php echo urlencode((string)$leadId); ?>" class="ld-btn ld-btn-edit">
              <i class="ri-edit-line"></i> Edit
            </a>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Main Grid - No Gaps -->
      <div class="ld-grid">

        <!-- LEFT COLUMN -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
          <!-- Client Details -->
          <div class="ld-card">
            <div class="ld-card-header">
              <div class="ld-icon ld-icon-primary"><i class="ri-user-3-line"></i></div>
              <div class="ld-card-header-title">Client Details</div>
            </div>
            <div class="ld-card-body">
              <div class="ld-data-row">
                <span class="ld-data-label"><i class="ri-user-line"></i> Name</span>
                <span class="ld-data-value"><?php echo lead_text($leadName); ?></span>
              </div>
              <div class="ld-data-row">
                <span class="ld-data-label"><i class="ri-mail-line"></i> Email</span>
                <span class="ld-data-value">
                  <?php if (!empty($lead['email1'])): ?>
                    <a href="mailto:<?php echo urlencode((string)$lead['email1']); ?>"><?php echo lead_text($lead['email1']); ?></a>
                  <?php else: ?>
                    <span style="color:var(--ld-text-muted)">—</span>
                  <?php endif; ?>
                </span>
              </div>
              <div class="ld-data-row">
                <span class="ld-data-label"><i class="ri-phone-line"></i> Phone</span>
                <span class="ld-data-value">
                  <?php if (!empty($lead['phone_c'])): ?>
                    <a href="tel:<?php echo urlencode((string)$lead['phone_c']); ?>"><?php echo lead_text($lead['phone_c']); ?></a>
                  <?php else: ?>
                    <span style="color:var(--ld-text-muted)">—</span>
                  <?php endif; ?>
                </span>
              </div>
              <div class="ld-data-row">
                <span class="ld-data-label"><i class="ri-flag-line"></i> Status</span>
                <span class="ld-data-value">
                  <?php if (!empty($leadStatus)): ?>
                    <span class="ld-badge"><?php echo lead_text($leadStatus); ?></span>
                  <?php else: ?>
                    <span style="color:var(--ld-text-muted)">—</span>
                  <?php endif; ?>
                </span>
              </div>
              <div class="ld-data-row">
                <span class="ld-data-label"><i class="ri-global-line"></i> Lead Source</span>
                <span class="ld-data-value">
                  <?php if (!empty($lead['lead_source'])): ?>
                    <span class="ld-badge ld-badge-info"><?php echo lead_text($lead['lead_source']); ?></span>
                  <?php else: ?>
                    <span style="color:var(--ld-text-muted)">—</span>
                  <?php endif; ?>
                </span>
              </div>
              <div class="ld-data-row">
                <span class="ld-data-label"><i class="ri-user-star-line"></i> Assigned To</span>
                <span class="ld-data-value">
                  <?php
                    $assignedName = trim((string)($lead['assigned_user_name'] ?? ''));
                    if ($assignedName !== ''):
                  ?>
                    <span class="ld-badge ld-badge-success"><?php echo lead_text($assignedName); ?></span>
                  <?php else: ?>
                    <span style="color:var(--ld-text-muted)">— Unassigned —</span>
                  <?php endif; ?>
                </span>
              </div>
              <div class="ld-data-row">
                <span class="ld-data-label"><i class="ri-time-line"></i> Created</span>
                <span class="ld-data-value"><?php echo lead_text($lead['date_entered'] ?? '—'); ?></span>
              </div>
              <?php if (!empty($lead['description'])): ?>
              <div style="margin-top:16px;padding-top:16px;border-top:1px dashed var(--ld-border);">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--ld-text-secondary);margin-bottom:6px;display:flex;align-items:center;gap:6px;">
                  <i class="ri-file-text-line"></i> Description
                </div>
                <div style="font-size:13px;color:var(--ld-text);line-height:1.6;white-space:pre-line;"><?php echo lead_text($lead['description']); ?></div>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Trip Details -->
          <div class="ld-card">
            <div class="ld-card-header">
              <div class="ld-icon ld-icon-info"><i class="ri-road-map-line"></i></div>
              <div class="ld-card-header-title">Trip Details</div>
            </div>
            <div class="ld-card-body">
              <div class="ld-data-row">
                <span class="ld-data-label"><i class="ri-briefcase-line"></i> Service</span>
                <span class="ld-data-value"><?php echo lead_text($lead['service_type_c'] ?? '—'); ?></span>
              </div>
              <div class="ld-data-row">
                <span class="ld-data-label"><i class="ri-calendar-event-line"></i> Event Date</span>
                <span class="ld-data-value"><?php echo lead_text($lead['event_date_c'] ?? '—'); ?></span>
              </div>
              <div class="ld-data-row">
                <span class="ld-data-label"><i class="ri-group-line"></i> Passengers</span>
                <span class="ld-data-value"><?php echo lead_text($lead['passengers_c'] ?? '—'); ?></span>
              </div>
              <div class="ld-data-row">
                <span class="ld-data-label"><i class="ri-route-line"></i> Distance</span>
                <span class="ld-data-value"><?php echo lead_text($lead['distance_c'] ?? '—'); ?></span>
              </div>
              <div class="ld-data-row">
                <span class="ld-data-label"><i class="ri-timer-line"></i> Duration</span>
                <span class="ld-data-value"><?php echo lead_text($lead['duration_c'] ?? '—'); ?></span>
              </div>
              <div class="ld-data-row">
                <span class="ld-data-label"><i class="ri-time-line"></i> Service Length</span>
                <span class="ld-data-value"><?php echo $serviceLengthNum > 0 ? $serviceLengthNum . ' hrs' : '—'; ?></span>
              </div>
            </div>
          </div>

          <!-- Addresses -->
          <div class="ld-card">
            <div class="ld-card-header">
              <div class="ld-icon ld-icon-success"><i class="ri-map-pin-line"></i></div>
              <div class="ld-card-header-title">Addresses</div>
            </div>
            <div class="ld-card-body">
              <div class="ld-address-grid">
                <div class="ld-address-card">
                  <div class="ld-address-label pickup"><i class="ri-map-pin-2-fill"></i> Pickup</div>
                  <div class="ld-address-text"><?php echo lead_text($lead['pickup_address_c'] ?? '—'); ?></div>
                </div>
                <div class="ld-address-card">
                  <div class="ld-address-label dropoff"><i class="ri-map-pin-2-fill"></i> Dropoff</div>
                  <div class="ld-address-text"><?php echo lead_text($lead['dropoff_address_c'] ?? '—'); ?></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <?php if (!empty($lead['notes_c'])): ?>
          <div class="ld-card ld-notes-card">
            <div class="ld-card-header">
              <div class="ld-icon ld-icon-warning"><i class="ri-sticky-note-line"></i></div>
              <div class="ld-card-header-title">Notes / Special Requests</div>
            </div>
            <div class="ld-card-body">
              <div class="ld-notes-text"><?php echo lead_text($lead['notes_c']); ?></div>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <!-- RIGHT COLUMN -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
          <!-- Price Breakdown -->
          <div class="ld-card">
            <div class="ld-card-header">
              <div class="ld-icon ld-icon-warning"><i class="ri-currency-line"></i></div>
              <div class="ld-card-header-title">Price Breakdown</div>
            </div>
            <div class="ld-card-body">
              <div class="ld-price-total">
                <div class="ld-price-amount"><?php echo lead_money($tripPrice); ?></div>
                <div class="ld-price-label">Total Trip Cost</div>
                <?php if ($hasMismatch): ?>
                  <div style="font-size:11px;color:var(--ld-warning);margin-top:8px;display:flex;align-items:center;justify-content:center;gap:4px;">
                    <i class="ri-information-line"></i> Line items add up to <?php echo lead_money($sumOfParts); ?>
                  </div>
                <?php endif; ?>
              </div>

              <div style="border:1px solid var(--ld-border);border-radius:var(--ld-radius-md);overflow:hidden;">
                <div class="ld-price-row">
                  <span class="ld-price-row-label"><i class="ri-cash-line text-primary"></i> Quoted Price  <?php if ($hasCalc): ?>
                <div >
                  <span style="font-size:11px;color:var(--ld-text-secondary);">
                    <?php echo rtrim(rtrim(number_format($serviceLengthNum, 2), '0'), '.'); ?> hrs &times; <?php echo lead_money($rateNum); ?>/hr
                  </span>
                </div>
                <?php endif; ?></span>
                  <span class="ld-price-row-value"><?php echo $hasCalc ? lead_money($quotedPrice) : '—'; ?></span>
                </div>
                
                <div class="ld-price-row">
                  <span class="ld-price-row-label"><i class="ri-gas-station-line text-warning"></i> Fuel Surcharge</span>
                  <span class="ld-price-row-value" <?php echo $fuelNum <= 0 ? 'style="color:var(--ld-text-muted);font-weight:400;"' : ''; ?>>
                    <?php echo $fuelNum > 0 ? '+ ' . lead_money($fuelNum) : '—'; ?>
                  </span>
                </div>
                <div class="ld-price-row">
                  <span class="ld-price-row-label"><i class="ri-steering-2-line text-info"></i> Driver Commission</span>
                  <span class="ld-price-row-value" <?php echo $commissionNum <= 0 ? 'style="color:var(--ld-text-muted);font-weight:400;"' : ''; ?>>
                    <?php echo $commissionNum > 0 ? '+ ' . lead_money($commissionNum) : '—'; ?>
                  </span>
                </div>
                <div class="ld-price-row" style="background:rgba(79, 70, 229, 0.04);">
                  <span class="ld-price-row-label" style="font-weight:800;"><i class="ri-checkbox-circle-line text-success"></i> Total</span>
                  <span class="ld-price-row-value" style="font-size:16px;color:var(--ld-primary);"><?php echo lead_money($tripPrice); ?></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Vehicle -->
          <?php if ($showVehicleCard): ?>
          <div class="ld-card">
            <div class="ld-card-header">
              <div class="ld-icon ld-icon-info"><i class="ri-truck-line"></i></div>
              <div class="ld-card-header-title">Vehicle</div>
            </div>
            <div class="ld-card-body">
              <?php if ($vehicleDisplayName !== ''): ?>
                <div class="ld-vehicle-title"><?php echo lead_text($vehicleDisplayName); ?></div>
              <?php endif; ?>
              <?php if (!empty($lead['vehicle_id_c'])): ?>
                <div class="ld-vehicle-id">ID <?php echo lead_text((string)$lead['vehicle_id_c']); ?></div>
              <?php elseif ($vehicleDisplayName === ''): ?>
                <div style="font-size:13px;color:var(--ld-text-muted);margin-bottom:12px;">Vehicle details</div>
              <?php endif; ?>

              <div class="ld-vehicle-pills">
                <span class="ld-vehicle-pill"><i class="ri-gas-station-line text-warning"></i> Fuel <?php echo lead_fmt_vehicle_pct($vehicleFuelPctV); ?></span>
                <span class="ld-vehicle-pill"><i class="ri-steering-2-line text-info"></i> Driver <?php echo lead_fmt_vehicle_pct($vehicleDriverPctV); ?></span>
              </div>

              <?php if (count($vehicleImageUrls) > 0): ?>
                <?php $firstImg = $vehicleImageUrls[0]; ?>
                <div class="ld-vehicle-gallery">
                  <img id="ld-veh-main-img" src="<?php echo lead_text($firstImg); ?>" alt="<?php echo lead_text($vehicleDisplayName !== '' ? $vehicleDisplayName : 'Vehicle'); ?>">
                </div>
                <?php if (count($vehicleImageUrls) > 1): ?>
                  <div class="ld-vehicle-thumbs">
                    <?php foreach ($vehicleImageUrls as $idx => $imgUrl): ?>
                      <button type="button" class="ld-vehicle-thumb<?php echo $idx === 0 ? ' active' : ''; ?>" data-full="<?php echo lead_text($imgUrl); ?>">
                        <img src="<?php echo lead_text($imgUrl); ?>" alt="">
                      </button>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              <?php endif; ?>

              <?php if (count($vehicleFacilities) > 0): ?>
                <div class="ld-vehicle-facilities">
                  <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--ld-text-secondary);margin-bottom:8px;">
                    <i class="ri-star-smile-line"></i> Facilities
                  </div>
                  <div>
                    <?php foreach ($vehicleFacilities as $fac): ?>
                      <span class="ld-facility-tag"><i class="ri-check-line" style="font-size:11px;color:var(--ld-primary);"></i> <?php echo lead_text($fac); ?></span>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Email History -->
      <div class="ld-card ld-email-card" >
        <div class="ld-card-header">
          <div class="ld-icon ld-icon-info"><i class="ri-mail-line"></i></div>
          <div class="ld-card-header-title">Email History</div>
          <span id="ld-email-count" class="ld-badge" style="display:none;margin-left:auto;">0 emails</span>
          <button type="button" onclick="loadLeadEmails()" style="margin-left:auto;background:none;border:1px solid var(--ld-border);border-radius:6px;padding:6px 12px;font-size:11px;font-weight:600;color:var(--ld-text-secondary);cursor:pointer;display:inline-flex;align-items:center;gap:4px;transition:all 0.15s;">
            <i class="ri-refresh-line"></i> Refresh
          </button>
        </div>
        <div class="ld-card-body" style="padding:0;">
          <div id="ld-email-loading" style="padding:24px;text-align:center;color:var(--ld-text-secondary);">
            <div style="font-size:13px;">Loading emails...</div>
          </div>
          <div id="ld-email-empty" class="ld-email-empty" style="display:none;">
            <i class="ri-mail-line"></i>
            <div style="font-size:14px;font-weight:600;margin-bottom:4px;">No emails yet</div>
            <div style="font-size:12px;">Emails sent to this lead will appear here.</div>
          </div>
          <div id="ld-email-error" style="display:none;text-align:center;padding:30px;color:var(--ld-danger);font-size:13px;">
            <i class="ri-error-warning-line"></i>
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
</div>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function () {
  $(document).on('click', '.ld-vehicle-thumb', function () {
    var u = $(this).data('full');
    if (u) {
      $('#ld-veh-main-img').attr('src', u);
      $('.ld-vehicle-thumb').removeClass('active');
      $(this).addClass('active');
    }
  });

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
      confirmButtonColor: emailType === 'agreement' ? '#10b981' : '#f59e0b'
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
            window.location.reload();
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
        }
      });
    });
  }

  $('#send-formal-quote-btn, #send-agreement-btn').on('click', function () { sendLeadEmail($(this)); });

  /* Email History */
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
            '<td><a href="' + emailUrl + '" class="ld-email-subject"><i class="' + typeIcon + '" style="' + typeColor + ';margin-right:6px;font-size:14px;vertical-align:middle;"></i>' + escHtml(em.name || '(No subject)') + '</a></td>' +
            '<td style="font-size:12px;color:var(--ld-text-secondary);">' + escHtml(em.to_addrs) + '</td>' +
            '<td style="font-size:12px;color:var(--ld-text-secondary);">' + escHtml(em.from_addr) + '</td>' +
            '<td><span class="ld-email-status ' + sc + '"><i class="ri-circle-fill" style="font-size:6px;"></i> ' + escHtml(statusLabel) + '</span></td>' +
            '<td style="font-size:12px;white-space:nowrap;">' + formatDate(dateSent) + '</td>' +
            '<td style="text-align:center;"><a href="' + emailUrl + '" class="ld-email-view-btn"><i class="ri-eye-line"></i> View</a></td>' +
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
});
</script>