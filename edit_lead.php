
<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>
<?php
$data['id'] = $_GET['id'];
$response = fetchSingleLead($data);
$lead = $response[0];

$teamData = ['id' => $_SESSION['user']['id']];
$teamMembers = fetchAllTeamMembers($teamData);
if (!is_array($teamMembers)) { $teamMembers = []; }

function lead_text($value): string {
  return htmlspecialchars((string)($value ?? ''), ENT_QUOTES);
}

$leadId = $lead['id'] ?? ($_GET['id'] ?? '');
$leadName = trim((string)($lead['first_name'] ?? '') . ' ' . (string)($lead['last_name'] ?? ''));
$leadStatus = (string)($lead['status'] ?? '');
$tripPrice = $lead['total_price_c'] ?? 0;

$vehicleFuelPct       = is_numeric($lead['vehicle_fuel_c'] ?? null) ? (float)$lead['vehicle_fuel_c'] : 0.0;
$vehicleCommissionPct = is_numeric($lead['vehicle_driver_commission_c'] ?? null) ? (float)$lead['vehicle_driver_commission_c'] : 0.0;
$hasVehiclePcts       = ($vehicleFuelPct > 0 || $vehicleCommissionPct > 0);
?>

<style>
  .el-page { --el-surface: #ffffff; --el-surface-2: #f8fafc; --el-border: rgba(15,23,42,0.10); --el-text: #0f172a; --el-muted: rgba(15,23,42,0.55); }
  .dark .el-page { --el-surface: rgba(255,255,255,0.035); --el-surface-2: rgba(255,255,255,0.05); --el-border: rgba(255,255,255,0.08); --el-text: rgba(255,255,255,0.92); --el-muted: rgba(255,255,255,0.50); }

  .el-page .el-label { font-size: 12px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--el-muted); margin-bottom: 6px; display: block; }
  .el-page .el-input {
    height: 44px; border-radius: 12px; border: 1px solid var(--el-border);
    background: var(--el-surface-2); padding: 0 14px; width: 100%;
    font-size: 14px; color: var(--el-text); transition: border-color 0.2s, box-shadow 0.2s; outline: none;
  }
  .el-page .el-input:focus { border-color: rgb(var(--primary-rgb)); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12); }
  .el-page .el-input.is-invalid { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,0.10) !important; }
  .el-page .el-input[readonly] { opacity: 0.65; cursor: default; }
  .el-page textarea.el-input { height: auto; min-height: 110px; resize: vertical; padding: 12px 14px; }
  .el-page select.el-input {
    cursor: pointer; -webkit-appearance: none; appearance: none; padding-right: 38px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M8 11.4 2.6 6h10.8L8 11.4z'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 14px center;
  }

  .el-page .el-input-group { position: relative; display: flex; align-items: stretch; }
  .el-page .el-input-group .el-input { border-radius: 0 12px 12px 0; }
  .el-page .el-input-group .el-addon {
    display: flex; align-items: center; justify-content: center;
    min-width: 44px; font-size: 14px; font-weight: 700;
    color: var(--el-muted); background: rgba(15,23,42,0.04);
    border: 1px solid var(--el-border); border-right: none; border-radius: 12px 0 0 12px;
  }
  .dark .el-page .el-input-group .el-addon { background: rgba(255,255,255,0.06); }
  .el-page .el-input-group-right .el-input { border-radius: 12px 0 0 12px; }
  .el-page .el-input-group-right .el-addon { border: 1px solid var(--el-border); border-left: none; border-radius: 0 12px 12px 0; }

  .el-page .el-error { font-size: 11px; color: #ef4444; margin-top: 5px; display: none; align-items: center; gap: 4px; }
  .el-page .el-error.show { display: flex; }

  .el-page .el-card { background: var(--el-surface); border: 1px solid var(--el-border); border-radius: 16px; overflow: hidden; transition: box-shadow 0.2s; }
  .el-page .el-card:hover { box-shadow: 0 4px 24px rgba(15,23,42,0.06); }
  .dark .el-page .el-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.25); }
  .el-page .el-card-header { background: rgba(15,23,42,0.025); border-bottom: 1px solid rgba(15,23,42,0.08); padding: 18px 24px; }
  .dark .el-page .el-card-header { background: rgba(255,255,255,0.025); border-bottom-color: rgba(255,255,255,0.08); }
  .el-page .el-card-body { padding: 24px; }

  .el-page .el-card-header .el-card-icon {
    width: 32px; height: 32px; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
  }
  .el-page .el-step-num {
    width: 22px; height: 22px; border-radius: 50%; background: rgb(var(--primary-rgb));
    color: #fff; font-size: 11px; font-weight: 800;
    display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
  }

  .el-summary-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(15,23,42,0.06); font-size: 13px; }
  .dark .el-summary-row { border-bottom-color: rgba(255,255,255,0.06); }
  .el-summary-row:last-child { border-bottom: none; }
  .el-summary-label { color: var(--el-muted); }
  .el-summary-value { font-weight: 600; color: var(--el-text); }
  .el-summary-total { font-size: 22px; font-weight: 800; color: rgb(var(--primary-rgb)); letter-spacing: -0.02em; }

  .el-page .el-pct-badge {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: 10px; font-weight: 700; border-radius: 20px; padding: 2px 8px;
  }

  .el-page .el-save-btn {
    height: 42px; border-radius: 12px; border: none;
    background: rgb(var(--primary-rgb)); color: #fff;
    padding: 0 24px; font-size: 14px; font-weight: 700;
    cursor: pointer; transition: all 0.2s;
    display: inline-flex; align-items: center; gap: 8px;
  }
  .el-page .el-save-btn:hover { filter: brightness(1.08); box-shadow: 0 4px 16px rgba(var(--primary-rgb), 0.3); }
  .el-page .el-save-btn:disabled { opacity: 0.6; cursor: not-allowed; }
  .el-page .el-cancel-btn {
    align-content: center;
    height: 42px; border-radius: 12px;
    border: 1px solid var(--el-border); background: var(--el-surface);
    color: var(--el-text); padding: 0 20px; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all 0.15s;
  }
  .el-page .el-cancel-btn:hover { background: var(--el-surface-2); }
.app-header{
  box-shadow:none!important;
}
  .el-sticky-header {
    position: sticky;
    top: 68px;
    z-index: 99;
    background: var(--el-surface);
    border-bottom: 1px solid var(--el-border);
    padding: 14px 24px;
    margin: -24px -24px 24px -24px;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    background: rgba(255,255,255,0.88);
    transition: box-shadow 0.2s;
  }
  .dark .el-sticky-header {
    background: rgba(18,18,30,0.88);
  }
  .el-sticky-header.scrolled {
    box-shadow: 0 4px 20px rgba(15,23,42,0.08);
  }
  .dark .el-sticky-header.scrolled {
    box-shadow: 0 4px 20px rgba(0,0,0,0.30);
  }
</style>

<div class="main-content app-content">
  <div class="container-fluid el-page">

    <!-- Sticky Page Header -->
    <div class="el-sticky-header" id="el-sticky-header">
      <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
          <a href="lead.php?id=<?php echo urlencode((string)$leadId); ?>" class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary hover:bg-primary/20 transition-colors flex-shrink-0" title="Back to Lead">
            <i class="ri-arrow-left-line text-lg"></i>
          </a>
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h1 class="text-lg font-bold mb-0 leading-tight" style="color:var(--el-text)">
                <?php echo $leadName !== '' ? lead_text($leadName) : 'Edit Lead'; ?>
              </h1>
              <?php if (!empty($leadStatus)): ?>
                <span class="badge bg-primary/10 text-primary text-[11px] font-semibold px-2.5 py-1 rounded-lg"><?php echo lead_text($leadStatus); ?></span>
              <?php endif; ?>
              <span style="color:var(--el-muted);font-size:12px;font-weight:500;">Lead #<?php echo lead_text($leadId); ?></span>
            </div>
            <div class="flex items-center gap-3 mt-0.5 flex-wrap" style="font-size:12px;color:var(--el-muted);">
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
            </div>
          </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <a href="lead.php?id=<?php echo urlencode((string)$leadId); ?>" class="el-cancel-btn">Cancel</a>
          <button type="button" id="saveLeadBtn" class="el-save-btn">
            <i class="ri-save-line"></i> Save Changes
          </button>
        </div>
      </div>
    </div>

    <!-- Form -->
    <form id="editLeadForm">
      <input type="hidden" name="id" value="<?php echo $lead['id']; ?>">

      <div class="grid grid-cols-12 gap-6 pb-12">

        <!-- LEFT COLUMN -->
        <div class="xl:col-span-8 col-span-12 space-y-6">

          <!-- Step 1: Lead Information -->
          <div class="el-card">
            <div class="el-card-header">
              <div class="flex items-center gap-3">
                <span class="el-step-num">1</span>
                <div class="el-card-icon bg-primary/10 text-primary"><i class="ri-user-3-line"></i></div>
                <div>
                  <div class="font-semibold text-sm" style="color:var(--el-text)">Lead Information</div>
                  <div style="font-size:11px;color:var(--el-muted)">Contact details and status</div>
                </div>
              </div>
            </div>
            <div class="el-card-body">
              <div class="grid grid-cols-12 gap-x-5 gap-y-5">
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-first-name">First Name <span style="color:#ef4444">*</span></label>
                  <input type="text" class="el-input" id="el-first-name" name="first_name" value="<?php echo lead_text($lead['first_name']); ?>">
                  <div class="el-error" id="err-first-name"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-last-name">Last Name <span style="color:#ef4444">*</span></label>
                  <input type="text" class="el-input" id="el-last-name" name="last_name" value="<?php echo lead_text($lead['last_name']); ?>">
                  <div class="el-error" id="err-last-name"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-email">Email</label>
                  <div class="el-input-group">
                    <div class="el-addon"><i class="ri-mail-line"></i></div>
                    <input type="email" class="el-input" id="el-email" name="email" value="<?php echo lead_text($lead['email1']); ?>" readonly>
                  </div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-phone">Phone <span style="color:#ef4444">*</span></label>
                  <div class="el-input-group">
                    <div class="el-addon"><i class="ri-phone-line"></i></div>
                    <input type="text" class="el-input" id="el-phone" name="phone" value="<?php echo lead_text($lead['phone_c'] ?? ''); ?>">
                  </div>
                  <div class="el-error" id="err-phone"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-status">Status <span style="color:#ef4444">*</span></label>
                  <select class="el-input" id="el-status" name="status">
                    <option value="">Select Status</option>
                    <option value="New" <?php echo ($lead['status'] == 'New') ? 'selected' : ''; ?>>New</option>
                    <option value="Assigned" <?php echo ($lead['status'] == 'Assigned') ? 'selected' : ''; ?>>Assigned</option>
                    <option value="In Process" <?php echo ($lead['status'] == 'In Process') ? 'selected' : ''; ?>>In Process</option>
                    <option value="Converted" <?php echo ($lead['status'] == 'Converted') ? 'selected' : ''; ?>>Converted</option>
                    <option value="Recycled" <?php echo ($lead['status'] == 'Recycled') ? 'selected' : ''; ?>>Recycled</option>
                    <option value="Dead" <?php echo ($lead['status'] == 'Dead') ? 'selected' : ''; ?>>Dead</option>
                  </select>
                  <div class="el-error" id="err-status"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-passengers">Passengers <span style="color:#ef4444">*</span></label>
                  <div class="el-input-group">
                    <div class="el-addon"><i class="ri-group-line"></i></div>
                    <input type="number" class="el-input" id="el-passengers" name="passengers" min="1" value="<?php echo lead_text($lead['passengers_c']); ?>">
                  </div>
                  <div class="el-error" id="err-passengers"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-lead-source">Lead Source</label>
                  <div class="el-input-group">
                    <div class="el-addon"><i class="ri-global-line"></i></div>
                    <input type="text" class="el-input" id="el-lead-source" name="lead_source" value="<?php echo lead_text($lead['lead_source'] ?? ''); ?>" placeholder="e.g. shmai.com" readonly>
                  </div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-assigned-user">Assign To</label>
                  <div class="el-input-group">
                    <div class="el-addon"><i class="ri-user-star-line"></i></div>
                    <select class="el-input" id="el-assigned-user" name="assigned_user_id">
                      <option value="">— Unassigned —</option>
                      <?php
                        $currentAssigned = (string)($lead['assigned_user_id'] ?? '');
                        foreach ($teamMembers as $member):
                          $memberId = (string)($member['id'] ?? '');
                          $memberName = trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''));
                          if ($memberName === '') $memberName = $member['user_name'] ?? $memberId;
                          $selected = ($memberId === $currentAssigned) ? ' selected' : '';
                      ?>
                        <option value="<?php echo lead_text($memberId); ?>"<?php echo $selected; ?>><?php echo lead_text($memberName); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Trip Details -->
          <div class="el-card">
            <div class="el-card-header">
              <div class="flex items-center gap-3">
                <span class="el-step-num">2</span>
                <div class="el-card-icon bg-info/10 text-info"><i class="ri-road-map-line"></i></div>
                <div>
                  <div class="font-semibold text-sm" style="color:var(--el-text)">Trip Details</div>
                  <div style="font-size:11px;color:var(--el-muted)">Service type, event date, and addresses</div>
                </div>
              </div>
            </div>
            <div class="el-card-body">
              <div class="grid grid-cols-12 gap-x-5 gap-y-5">
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-service-type">Service Type <span style="color:#ef4444">*</span></label>
                  <select name="service_type" id="el-service-type" class="el-input">
                    <option value="">Select service</option>
                    <?php
                      $serviceOptions = [
                        "Airport","Bachelor Party","Bachelorette Party","Birthday","Casino",
                        "Church Function","Concert","Construction Shuttle","Convention",
                        "Corporate Event","Cruise Transfers","Family Reunion","General Day Trip",
                        "Golf Outing","Homecoming","Night out on Town","Over the Road","Prom",
                        "School Trip","Shuttle Service","Sports Event","Theme Park","Transfer",
                        "Wedding","Wedding Wire","Wine Tour",
                      ];
                      $currentService = (string)($lead['service_type_c'] ?? '');
                      foreach ($serviceOptions as $opt) {
                        $selected = ($opt === $currentService) ? ' selected' : '';
                        echo '<option value="'.htmlspecialchars($opt, ENT_QUOTES).'"'.$selected.'>'.htmlspecialchars($opt).'</option>';
                      }
                    ?>
                  </select>
                  <div class="el-error" id="err-service-type"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-event-date">Event Date <span style="color:#ef4444">*</span></label>
                  <input type="date" class="el-input" id="el-event-date" name="event_date" value="<?php echo date('Y-m-d', strtotime($lead['event_date_c'])); ?>">
                  <div class="el-error" id="err-event-date"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-distance">Distance</label>
                  <div class="el-input-group-right el-input-group">
                    <input type="text" class="el-input" id="el-distance" name="distance" value="<?php echo lead_text($lead['distance_c']); ?>">
                    <div class="el-addon" style="font-size:11px;font-weight:600;">mi</div>
                  </div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-duration">Duration</label>
                  <div class="el-input-group-right el-input-group">
                    <input type="text" class="el-input" id="el-duration" name="duration" value="<?php echo lead_text($lead['duration_c']); ?>">
                    <div class="el-addon" style="font-size:11px;font-weight:600;">hrs</div>
                  </div>
                </div>
                <div class="col-span-12">
                  <label class="el-label" for="el-pickup">Pickup Address <span style="color:#ef4444">*</span></label>
                  <div class="el-input-group">
                    <div class="el-addon"><i class="ri-map-pin-2-line" style="color:#22c55e"></i></div>
                    <input type="text" class="el-input" id="el-pickup" name="pickup_address" value="<?php echo lead_text($lead['pickup_address_c']); ?>">
                  </div>
                  <div class="el-error" id="err-pickup"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12">
                  <label class="el-label" for="el-dropoff">Dropoff Address <span style="color:#ef4444">*</span></label>
                  <div class="el-input-group">
                    <div class="el-addon"><i class="ri-map-pin-2-line" style="color:#ef4444"></i></div>
                    <input type="text" class="el-input" id="el-dropoff" name="dropoff_address" value="<?php echo lead_text($lead['dropoff_address_c']); ?>">
                  </div>
                  <div class="el-error" id="err-dropoff"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12">
                  <label class="el-label" for="el-notes">Notes / Special Requests</label>
                  <textarea class="el-input" id="el-notes" name="notes" rows="3" placeholder="Flight info, gate codes, child seats, special requests..."><?php echo lead_text($lead['notes_c'] ?? ''); ?></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 3: Pricing -->
          <div class="el-card">
            <div class="el-card-header">
              <div class="flex items-center gap-3">
                <span class="el-step-num">3</span>
                <div class="el-card-icon bg-success/10 text-success"><i class="ri-currency-line"></i></div>
                <div>
                  <div class="font-semibold text-sm" style="color:var(--el-text)">Pricing</div>
                  <div style="font-size:11px;color:var(--el-muted)">Rate, surcharges, and total calculation</div>
                </div>
              </div>
            </div>
            <div class="el-card-body">
              <div class="grid grid-cols-12 gap-x-5 gap-y-5">
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-rate">Rate (per hour) <span style="color:#ef4444">*</span></label>
                  <div class="el-input-group">
                    <div class="el-addon">$</div>
                    <input type="number" step="0.01" min="0" class="el-input" id="el-rate" name="rate" value="<?php echo lead_text($lead['rate_c'] ?? ''); ?>">
                  </div>
                  <div class="el-error" id="err-rate"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-service-length">Service Length (Hours) <span style="color:#ef4444">*</span></label>
                  <div class="el-input-group-right el-input-group">
                    <input type="number" step="0.01" min="0" class="el-input" id="el-service-length" name="service_length" value="<?php echo lead_text($lead['service_length_c'] ?? ''); ?>">
                    <div class="el-addon" style="font-size:11px;font-weight:600;">hrs</div>
                  </div>
                  <div class="el-error" id="err-service-length"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-quoted">Quoted Price</label>
                  <div class="el-input-group">
                    <div class="el-addon">$</div>
                    <input type="text" class="el-input" id="el-quoted" readonly tabindex="-1" value="">
                  </div>
                  <div style="font-size:11px;color:var(--el-muted);margin-top:4px;font-style:italic;">Rate &times; Service Length</div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-fuel">
                    Fuel Surcharge <span style="color:#ef4444">*</span>
                    <?php if ($vehicleFuelPct > 0): ?>
                      <span class="el-pct-badge bg-warning/10 text-warning ms-1"><i class="ri-gas-station-line"></i> <?php echo rtrim(rtrim(number_format($vehicleFuelPct, 2), '0'), '.'); ?>%</span>
                    <?php endif; ?>
                  </label>
                  <div class="el-input-group">
                    <div class="el-addon">$</div>
                    <input type="number" step="0.01" min="0" class="el-input" id="el-fuel" name="fuel" value="<?php echo lead_text($lead['fuel_c'] ?? ''); ?>">
                  </div>
                  <div class="el-error" id="err-fuel"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-commission">
                    Driver Commission <span style="color:#ef4444">*</span>
                    <?php if ($vehicleCommissionPct > 0): ?>
                      <span class="el-pct-badge bg-secondary/10 text-secondary ms-1"><i class="ri-steering-2-line"></i> <?php echo rtrim(rtrim(number_format($vehicleCommissionPct, 2), '0'), '.'); ?>%</span>
                    <?php endif; ?>
                  </label>
                  <div class="el-input-group">
                    <div class="el-addon">$</div>
                    <input type="number" step="0.01" min="0" class="el-input" id="el-commission" name="driver_commission" value="<?php echo lead_text($lead['driver_commission_c'] ?? ''); ?>">
                  </div>
                  <div class="el-error" id="err-commission"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="el-label" for="el-total">Total Price <span style="color:#ef4444">*</span></label>
                  <div class="el-input-group">
                    <div class="el-addon">$</div>
                    <input type="number" step="0.01" min="0" class="el-input" id="el-total" name="total_price" value="<?php echo lead_text($lead['total_price_c'] ?? ''); ?>">
                  </div>
                  <div class="el-error" id="err-total"><i class="ri-error-warning-line"></i><span></span></div>
                  <div style="font-size:11px;color:var(--el-muted);margin-top:4px;font-style:italic;">Quoted + Fuel + Commission</div>
                </div>

                <?php if ($hasVehiclePcts): ?>
                <div class="col-span-12">
                  <div style="background:rgba(var(--primary-rgb),0.04);border:1px solid rgba(var(--primary-rgb),0.10);border-radius:10px;padding:10px 14px;font-size:12px;color:var(--el-muted);display:flex;align-items:center;gap:8px;">
                    <i class="ri-car-line text-primary text-base"></i>
                    Fuel &amp; commission auto-calculate from the assigned vehicle's percentages when Rate or Service Length changes.
                  </div>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

        </div>

        <!-- RIGHT COLUMN: Live Summary -->
        <div class="xl:col-span-4 col-span-12">
          <div class="el-card" style="position:sticky;top:80px;">
            <div class="el-card-header">
              <div class="flex items-center gap-3">
                <div class="el-card-icon bg-primary/10 text-primary"><i class="ri-file-list-3-line"></i></div>
                <div class="font-semibold text-sm" style="color:var(--el-text)">Live Summary</div>
              </div>
            </div>
            <div class="el-card-body">
              <div id="el-summary-content">
                <div class="el-summary-row"><span class="el-summary-label">Name</span><span class="el-summary-value" id="sum-name">—</span></div>
                <div class="el-summary-row"><span class="el-summary-label">Phone</span><span class="el-summary-value" id="sum-phone">—</span></div>
                <div class="el-summary-row"><span class="el-summary-label">Status</span><span class="el-summary-value" id="sum-status">—</span></div>
                <div class="el-summary-row"><span class="el-summary-label">Assigned To</span><span class="el-summary-value" id="sum-assigned">—</span></div>
                <div class="el-summary-row"><span class="el-summary-label">Service</span><span class="el-summary-value" id="sum-service">—</span></div>
                <div class="el-summary-row"><span class="el-summary-label">Event Date</span><span class="el-summary-value" id="sum-event-date">—</span></div>
                <div class="el-summary-row"><span class="el-summary-label">Passengers</span><span class="el-summary-value" id="sum-passengers">—</span></div>
                <div class="el-summary-row"><span class="el-summary-label">Pickup</span><span class="el-summary-value" id="sum-pickup" style="max-width:180px;text-align:right;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">—</span></div>
                <div class="el-summary-row"><span class="el-summary-label">Dropoff</span><span class="el-summary-value" id="sum-dropoff" style="max-width:180px;text-align:right;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">—</span></div>
              </div>

              <div style="margin:16px 0;border-top:2px dashed var(--el-border);"></div>

              <div class="el-summary-row"><span class="el-summary-label">Rate</span><span class="el-summary-value" id="sum-rate">—</span></div>
              <div class="el-summary-row"><span class="el-summary-label">Hours</span><span class="el-summary-value" id="sum-hours">—</span></div>
              <div class="el-summary-row"><span class="el-summary-label">Quoted</span><span class="el-summary-value" id="sum-quoted">—</span></div>
              <div class="el-summary-row"><span class="el-summary-label">Fuel</span><span class="el-summary-value" id="sum-fuel">—</span></div>
              <div class="el-summary-row"><span class="el-summary-label">Commission</span><span class="el-summary-value" id="sum-comm">—</span></div>

              <div style="margin:14px 0 10px;border-top:2px solid var(--el-border);"></div>
              <div class="flex items-center justify-between">
                <span class="el-summary-label" style="font-weight:700;text-transform:uppercase;font-size:11px;letter-spacing:.06em;">Total</span>
                <span class="el-summary-total" id="sum-total">$0.00</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </form>

  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {

  /* ── Helpers ─────────────────────────────────────── */
  function toNum(v) { var n = parseFloat(String(v).replace(/,/g, '')); return Number.isFinite(n) ? n : 0; }
  function fmt(n) { return (Math.round((Number.isFinite(n) ? n : 0) * 100) / 100).toFixed(2); }

  const FUEL_PCT = <?php echo $vehicleFuelPct; ?>;
  const COMM_PCT = <?php echo $vehicleCommissionPct; ?>;

  /* ── Pricing ─────────────────────────────────────── */
  function recalc(src) {
    var rate  = toNum($('#el-rate').val());
    var hours = toNum($('#el-service-length').val());
    var quoted = rate * hours;
    $('#el-quoted').val(fmt(quoted));

    if (src === 'rate' || src === 'hours') {
      if (FUEL_PCT > 0) $('#el-fuel').val(fmt(quoted * FUEL_PCT / 100));
      if (COMM_PCT > 0) $('#el-commission').val(fmt(quoted * COMM_PCT / 100));
    }
    if (src !== 'total') {
      var fuel = toNum($('#el-fuel').val());
      var comm = toNum($('#el-commission').val());
      $('#el-total').val(fmt(quoted + fuel + comm));
    }
    updateSummary();
  }

  $('#el-rate').on('input', function () { recalc('rate'); });
  $('#el-service-length').on('input', function () { recalc('hours'); });
  $('#el-fuel').on('input', function () { recalc('fuel'); });
  $('#el-commission').on('input', function () { recalc('commission'); });
  $('#el-total').on('input', function () { recalc('total'); });
  recalc('init');

  /* ── Live Summary ────────────────────────────────── */
  function updateSummary() {
    var fn = $('#el-first-name').val() || '';
    var ln = $('#el-last-name').val() || '';
    $('#sum-name').text((fn + ' ' + ln).trim() || '—');
    $('#sum-phone').text($('#el-phone').val() || '—');
    $('#sum-status').text($('#el-status').val() || '—');
    $('#sum-assigned').text($('#el-assigned-user option:selected').text().trim() || '—');
    $('#sum-service').text($('#el-service-type').val() || '—');
    $('#sum-event-date').text($('#el-event-date').val() || '—');
    $('#sum-passengers').text($('#el-passengers').val() || '—');
    $('#sum-pickup').text($('#el-pickup').val() || '—').attr('title', $('#el-pickup').val() || '');
    $('#sum-dropoff').text($('#el-dropoff').val() || '—').attr('title', $('#el-dropoff').val() || '');

    var r = toNum($('#el-rate').val());
    var h = toNum($('#el-service-length').val());
    $('#sum-rate').text(r ? '$' + fmt(r) + '/hr' : '—');
    $('#sum-hours').text(h ? h + ' hrs' : '—');
    $('#sum-quoted').text('$' + fmt(r * h));
    $('#sum-fuel').text('$' + fmt(toNum($('#el-fuel').val())));
    $('#sum-comm').text('$' + fmt(toNum($('#el-commission').val())));
    $('#sum-total').text('$' + fmt(toNum($('#el-total').val())));
  }

  $('#editLeadForm').on('input change', 'input, select, textarea', updateSummary);
  updateSummary();

  /* ── Validation ──────────────────────────────────── */
  function showErr(inputSel, errSel, msg) {
    $(inputSel).addClass('is-invalid');
    $(errSel).addClass('show').find('span').text(msg);
  }
  function clearErrs() {
    $('.el-input').removeClass('is-invalid');
    $('.el-error').removeClass('show');
  }

  function validate() {
    clearErrs();
    var ok = true;

    var fn = $('#el-first-name').val().trim();
    if (!fn) { showErr('#el-first-name', '#err-first-name', 'First name is required.'); ok = false; }
    else if (fn.length < 2) { showErr('#el-first-name', '#err-first-name', 'Must be at least 2 characters.'); ok = false; }

    var ln = $('#el-last-name').val().trim();
    if (!ln) { showErr('#el-last-name', '#err-last-name', 'Last name is required.'); ok = false; }
    else if (ln.length < 2) { showErr('#el-last-name', '#err-last-name', 'Must be at least 2 characters.'); ok = false; }

    var phone = $('#el-phone').val().trim();
    if (!phone) { showErr('#el-phone', '#err-phone', 'Phone number is required.'); ok = false; }
    else if (phone.length < 7) { showErr('#el-phone', '#err-phone', 'Enter a valid phone number.'); ok = false; }

    if (!$('#el-status').val()) { showErr('#el-status', '#err-status', 'Please select a status.'); ok = false; }

    var pass = $('#el-passengers').val();
    if (pass === '' || isNaN(parseInt(pass))) { showErr('#el-passengers', '#err-passengers', 'Passenger count is required.'); ok = false; }
    else if (parseInt(pass) < 1) { showErr('#el-passengers', '#err-passengers', 'Must be at least 1.'); ok = false; }
    else if (parseInt(pass) > 500) { showErr('#el-passengers', '#err-passengers', 'Maximum 500 passengers.'); ok = false; }

    if (!$('#el-service-type').val()) { showErr('#el-service-type', '#err-service-type', 'Please select a service type.'); ok = false; }

    if (!$('#el-event-date').val()) { showErr('#el-event-date', '#err-event-date', 'Event date is required.'); ok = false; }

    var pickup = $('#el-pickup').val().trim();
    if (!pickup) { showErr('#el-pickup', '#err-pickup', 'Pickup address is required.'); ok = false; }

    var dropoff = $('#el-dropoff').val().trim();
    if (!dropoff) { showErr('#el-dropoff', '#err-dropoff', 'Dropoff address is required.'); ok = false; }

    var rate = $('#el-rate').val();
    if (rate === '' || isNaN(parseFloat(rate))) { showErr('#el-rate', '#err-rate', 'Rate is required.'); ok = false; }
    else if (parseFloat(rate) < 0) { showErr('#el-rate', '#err-rate', 'Rate cannot be negative.'); ok = false; }

    var sl = $('#el-service-length').val();
    if (sl === '' || isNaN(parseFloat(sl))) { showErr('#el-service-length', '#err-service-length', 'Service length is required.'); ok = false; }
    else if (parseFloat(sl) <= 0) { showErr('#el-service-length', '#err-service-length', 'Must be greater than 0.'); ok = false; }

    var fuel = $('#el-fuel').val();
    if (fuel === '' || isNaN(parseFloat(fuel))) { showErr('#el-fuel', '#err-fuel', 'Fuel surcharge is required.'); ok = false; }
    else if (parseFloat(fuel) < 0) { showErr('#el-fuel', '#err-fuel', 'Cannot be negative.'); ok = false; }

    var comm = $('#el-commission').val();
    if (comm === '' || isNaN(parseFloat(comm))) { showErr('#el-commission', '#err-commission', 'Driver commission is required.'); ok = false; }
    else if (parseFloat(comm) < 0) { showErr('#el-commission', '#err-commission', 'Cannot be negative.'); ok = false; }

    var total = $('#el-total').val();
    if (total === '' || isNaN(parseFloat(total))) { showErr('#el-total', '#err-total', 'Total price is required.'); ok = false; }
    else if (parseFloat(total) < 0) { showErr('#el-total', '#err-total', 'Cannot be negative.'); ok = false; }

    if (!ok) {
      var first = document.querySelector('.el-input.is-invalid');
      if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return ok;
  }

  /* ── Save ────────────────────────────────────────── */
  $('#saveLeadBtn').click(function () {
    if (!validate()) return;

    var btn = $(this);
    var orig = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm align-middle me-1"></span> Saving...').prop('disabled', true);

    $.ajax({
      url: 'config/update_lead_endpoint.php',
      type: 'POST',
      data: $('#editLeadForm').serialize(),
      dataType: 'json',
      success: function (response) {
        var data = response;
        if (typeof response === 'string') {
          try { data = JSON.parse(response); } catch (e) {}
        }
        if (data.success) {
          Swal.fire({ icon: 'success', title: 'Success', text: 'Lead updated successfully!', showConfirmButton: false, timer: 1500 }).then(function () {
            window.location.href = 'lead.php?id=' + $('input[name="id"]').val();
          });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to update lead' });
          btn.html(orig).prop('disabled', false);
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while saving.' });
        btn.html(orig).prop('disabled', false);
      }
    });
  });

  var stickyHeader = document.getElementById('el-sticky-header');
  if (stickyHeader) {
    var scrollParent = stickyHeader.closest('.app-content') || window;
    (scrollParent === window ? window : scrollParent).addEventListener('scroll', function () {
      var scrollTop = scrollParent === window ? window.scrollY : scrollParent.scrollTop;
      stickyHeader.classList.toggle('scrolled', scrollTop > 10);
    });
  }

});
</script>
