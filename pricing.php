<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$userId = $_SESSION['user']['id'] ?? '';
$vehicles = [];
if ($userId !== '') {
  $vehicles = fetchVehicles(['user_id' => $userId]);
}
if (!is_array($vehicles)) {
  $vehicles = [];
}

$defaults = [
  'default_hourly_rate'   => '0',
  'fuel_surcharge_pct'    => '0',
  'driver_commission_pct' => '0',
];
if ($userId !== '') {
  $pr = fetchPricingDefaults(['user_id' => $userId]);
  if (!empty($pr['success']) && !empty($pr['defaults']) && is_array($pr['defaults'])) {
    $defaults = array_merge($defaults, $pr['defaults']);
  }
}

function pr_escape($v): string {
  return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$apiEntry = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';
?>

<style>
  .pr-page {
    --pr-surface: #ffffff;
    --pr-surface-2: #f8fafc;
    --pr-border: rgba(15, 23, 42, 0.10);
    --pr-text: #0f172a;
    --pr-muted: rgba(15, 23, 42, 0.55);
  }
  .dark .pr-page {
    --pr-surface: rgba(255, 255, 255, 0.035);
    --pr-surface-2: rgba(255, 255, 255, 0.05);
    --pr-border: rgba(255, 255, 255, 0.08);
    --pr-text: rgba(255, 255, 255, 0.92);
    --pr-muted: rgba(255, 255, 255, 0.50);
  }

  .pr-card {
    background: var(--pr-surface);
    border: 1px solid var(--pr-border);
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 1.5rem;
  }
  .pr-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--pr-border);
    background: rgba(15, 23, 42, 0.02);
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .dark .pr-card-header {
    background: rgba(255, 255, 255, 0.03);
  }
  .pr-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }
  .pr-card-body {
    padding: 24px;
  }

  .pr-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--pr-muted);
    margin-bottom: 6px;
  }
  .pr-input {
    width: 100%;
    height: 44px;
    border-radius: 12px;
    border: 1px solid var(--pr-border);
    background: var(--pr-surface-2);
    color: var(--pr-text);
    padding: 0 14px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .pr-input:focus {
    border-color: rgb(var(--primary-rgb));
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12);
  }
  .pr-input-sm {
    height: 38px;
    font-size: 13px;
    min-width: 88px;
  }

  .pr-save-top {
    height: 44px;
    border-radius: 12px;
    border: none;
    background: rgb(var(--primary-rgb));
    color: #fff;
    font-weight: 700;
    font-size: 14px;
    padding: 0 22px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: filter 0.2s, box-shadow 0.2s;
  }
  .pr-save-top:hover:not(:disabled) {
    filter: brightness(1.06);
    box-shadow: 0 4px 16px rgba(var(--primary-rgb), 0.28);
  }
  .pr-save-top:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .pr-note {
    font-size: 12px;
    color: var(--pr-muted);
    line-height: 1.55;
    padding: 12px 14px;
    border-radius: 10px;
    background: rgba(var(--primary-rgb), 0.05);
    border: 1px solid rgba(var(--primary-rgb), 0.12);
  }

  .pr-table-wrap {
    overflow-x: auto;
  }
  .pr-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }
  .pr-table th {
    text-align: left;
    padding: 12px 14px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--pr-muted);
    border-bottom: 1px solid var(--pr-border);
    background: var(--pr-surface-2);
    white-space: nowrap;
  }
  .pr-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--pr-border);
    vertical-align: middle;
    color: var(--pr-text);
  }
  .pr-table tbody tr:hover td {
    background: rgba(var(--primary-rgb), 0.02);
  }
  .pr-veh-name {
    font-weight: 600;
    max-width: 200px;
  }
  .pr-btn-row {
    height: 34px;
    border-radius: 10px;
    border: none;
    font-size: 12px;
    font-weight: 600;
    padding: 0 12px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s;
  }
  .pr-btn-row-save {
    background: rgb(var(--primary-rgb));
    color: #fff;
  }
  .pr-btn-row-save:hover:not(:disabled) {
    filter: brightness(1.08);
  }
  .pr-btn-row-save:disabled {
    opacity: 0.55;
    cursor: not-allowed;
  }
  .pr-btn-row-link {
    background: transparent;
    border: 1px solid var(--pr-border);
    color: var(--pr-muted);
    text-decoration: none;
  }
  .pr-btn-row-link:hover {
    border-color: rgba(var(--primary-rgb), 0.35);
    color: rgb(var(--primary-rgb));
  }
  .pr-btn-row-global {
    background: rgba(var(--primary-rgb), 0.08);
    border: 1px solid rgba(var(--primary-rgb), 0.22);
    color: rgb(var(--primary-rgb));
  }
  .pr-btn-row-global:hover:not(:disabled) {
    background: rgba(var(--primary-rgb), 0.14);
    border-color: rgba(var(--primary-rgb), 0.35);
  }
  .pr-btn-row-global:disabled {
    opacity: 0.55;
    cursor: not-allowed;
  }
</style>

<div class="main-content app-content">
  <div class="container-fluid pr-page">

    <div class="flex items-start justify-between flex-wrap gap-3 mb-6">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="ri-money-dollar-circle-line text-primary text-base"></i>
          </div>
          <h1 class="page-title font-bold text-xl mb-0 text-defaulttextcolor dark:text-defaulttextcolor/90">Pricing</h1>
        </div>
        <p class="text-xs text-textmuted dark:text-textmuted/50 mt-1 mb-0 ms-10">Default rates and surcharges for quotes, plus per-vehicle pricing.</p>
      </div>
    </div>

    <!-- Default pricing -->
    <div class="pr-card">
      <div class="pr-card-header">
        <div class="pr-card-icon bg-primary/10 text-primary"><i class="ri-settings-3-line"></i></div>
        <div>
          <div class="font-semibold text-sm" style="color:var(--pr-text)">Default pricing</div>
          <div style="font-size:11px;color:var(--pr-muted);">Stored for your account (new leads can use these as a baseline)</div>
        </div>
      </div>
      <div class="pr-card-body">
        
        <form id="form-pricing-defaults" class="grid grid-cols-12 gap-5 items-end">
          <input type="hidden" name="user_id" value="<?php echo pr_escape($userId); ?>">
          <div class="xl:col-span-4 md:col-span-6 col-span-12">
            <label class="pr-label" for="pr-default-rate">Default rate (per hour)</label>
            <div class="flex items-center gap-2">
              <span class="text-sm font-semibold" style="color:var(--pr-muted);">$</span>
              <input type="number" step="0.01" min="0" class="pr-input flex-1" id="pr-default-rate" name="default_hourly_rate"
                value="<?php echo pr_escape($defaults['default_hourly_rate']); ?>">
            </div>
          </div>
          <div class="xl:col-span-4 md:col-span-6 col-span-12">
            <label class="pr-label" for="pr-default-fuel">Fuel surcharge (%)</label>
            <div class="flex items-center gap-2">
              <input type="number" step="0.01" min="0" max="100" class="pr-input flex-1" id="pr-default-fuel" name="fuel_surcharge_pct"
                value="<?php echo pr_escape($defaults['fuel_surcharge_pct']); ?>">
              <span class="text-sm font-semibold" style="color:var(--pr-muted);">%</span>
            </div>
          </div>
          <div class="xl:col-span-4 md:col-span-6 col-span-12">
            <label class="pr-label" for="pr-default-driver">Driver commission (%)</label>
            <div class="flex items-center gap-2">
              <input type="number" step="0.01" min="0" max="100" class="pr-input flex-1" id="pr-default-driver" name="driver_commission_pct"
                value="<?php echo pr_escape($defaults['driver_commission_pct']); ?>">
              <span class="text-sm font-semibold" style="color:var(--pr-muted);">%</span>
            </div>
          </div>
          <div class="col-span-12 flex flex-wrap gap-2 items-center">
            <button type="submit" class="pr-save-top" id="btn-save-defaults"><i class="ri-save-line"></i> Save defaults</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Vehicles -->
    <div class="pr-card mb-6">
      <div class="pr-card-header">
        <div class="pr-card-icon bg-success/10 text-success"><i class="ri-bus-line"></i></div>
        <div>
          <div class="font-semibold text-sm" style="color:var(--pr-text)">Fleet vehicles</div>
          <div style="font-size:11px;color:var(--pr-muted);"><?php echo count($vehicles); ?> vehicle<?php echo count($vehicles) === 1 ? '' : 's'; ?> — edit rate and percentages per unit</div>
        </div>
      </div>
      <div class="pr-card-body" style="padding:0;">
        <?php if (count($vehicles) === 0): ?>
          <div class="p-8 text-center" style="color:var(--pr-muted);">
            <i class="ri-bus-line text-4xl mb-2 opacity-30"></i>
            <p class="mb-0 text-sm">No vehicles yet. Add vehicles under <a href="vehicles.php" class="text-primary font-semibold">Vehicles</a>.</p>
          </div>
        <?php else: ?>
        <div class="pr-table-wrap">
          <table class="pr-table">
            <thead>
              <tr>
                <th>Vehicle</th>
                <th>Category</th>
                <th>Status</th>
                <th>Rate / hr</th>
                <th>Fuel %</th>
                <th>Driver %</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($vehicles as $v):
                $vid = pr_escape($v['id'] ?? '');
                $vname = pr_escape($v['name'] ?? '—');
                $cat = pr_escape($v['vehicle_cetagory'] ?? '—');
                $st = pr_escape($v['status'] ?? '—');
                $rate = pr_escape($v['rate_c'] ?? '0');
                $fuel = pr_escape($v['fuel_c'] ?? '0');
                $drv = pr_escape($v['driver_commission_c'] ?? '0');
              ?>
              <tr data-vehicle-row="<?php echo $vid; ?>">
                <td class="pr-veh-name"><?php echo $vname; ?></td>
                <td><?php echo $cat; ?></td>
                <td><span class="badge bg-primary/10 text-primary" style="font-size:11px;"><?php echo $st; ?></span></td>
                <td>
                  <div class="flex items-center gap-1">
                    <span class="text-xs" style="color:var(--pr-muted);">$</span>
                    <input type="number" step="0.01" min="0" class="pr-input pr-input-sm pr-in-rate" value="<?php echo $rate; ?>" aria-label="Rate per hour">
                  </div>
                </td>
                <td>
                  <div class="flex items-center gap-1">
                    <input type="number" step="0.01" min="0" max="100" class="pr-input pr-input-sm pr-in-fuel" value="<?php echo $fuel; ?>" aria-label="Fuel percent">
                    <span class="text-xs" style="color:var(--pr-muted);">%</span>
                  </div>
                </td>
                <td>
                  <div class="flex items-center gap-1">
                    <input type="number" step="0.01" min="0" max="100" class="pr-input pr-input-sm pr-in-driver" value="<?php echo $drv; ?>" aria-label="Driver percent">
                    <span class="text-xs" style="color:var(--pr-muted);">%</span>
                  </div>
                </td>
                <td style="text-align:right;">
                  <div class="flex items-center justify-end gap-2 flex-wrap">
                    <button type="button" class="pr-btn-row pr-btn-row-save pr-save-vehicle" data-id="<?php echo $vid; ?>"><i class="ri-save-line"></i> Save</button>
                    <button type="button" class="pr-btn-row pr-btn-row-global pr-use-global" data-id="<?php echo $vid; ?>" title="Copy default pricing above and save this vehicle"><i class="ri-global-line"></i> Use global</button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
  var ENTRY = <?php echo json_encode($apiEntry); ?>;
  var USER_ID = <?php echo json_encode((string)$userId); ?>;

  function parseResp(resp) {
    if (typeof resp === 'string') {
      try { return JSON.parse(resp); } catch (e) { return {}; }
    }
    return resp || {};
  }

  /** Read pricing values from the default (global) form at the top of the page. */
  function getGlobalPricingVals() {
    var rate = $('#pr-default-rate').val();
    var fuel = $('#pr-default-fuel').val();
    var driver = $('#pr-default-driver').val();
    if (rate === '' || rate == null) rate = '0';
    if (fuel === '' || fuel == null) fuel = '0';
    if (driver === '' || driver == null) driver = '0';
    return { rate: rate, fuel: fuel, driver: driver };
  }

  /** POST update_vehicle_pricing; optionally updates row inputs first. optional successText for Swal */
  function postVehiclePricing(tr, vehicleId, rate, fuel, driver, actionButtons, successText) {
    actionButtons.prop('disabled', true);
    $.post(ENTRY, {
      action: 'update_vehicle_pricing',
      user_id: USER_ID,
      id: vehicleId,
      rate_c: rate,
      fuel_c: fuel,
      driver_commission_c: driver
    }).done(function (resp) {
      var data = parseResp(resp);
      if (data.success) {
        tr.find('.pr-in-rate').val(rate);
        tr.find('.pr-in-fuel').val(fuel);
        tr.find('.pr-in-driver').val(driver);
        Swal.fire({
          icon: 'success',
          title: 'Updated',
          text: successText || data.message || 'Vehicle pricing saved.',
          timer: 1700,
          showConfirmButton: false
        });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Update failed.' });
      }
    }).fail(function () {
      Swal.fire({ icon: 'error', title: 'Network error', text: 'Could not reach the server.' });
    }).always(function () {
      actionButtons.prop('disabled', false);
    });
  }

  $('#form-pricing-defaults').on('submit', function (e) {
    e.preventDefault();
    var btn = $('#btn-save-defaults');
    btn.prop('disabled', true);
    $.post(ENTRY, {
      action: 'save_pricing_defaults',
      user_id: USER_ID,
      default_hourly_rate: $('#pr-default-rate').val(),
      fuel_surcharge_pct: $('#pr-default-fuel').val(),
      driver_commission_pct: $('#pr-default-driver').val()
    }).done(function (resp) {
      var data = parseResp(resp);
      if (data.success) {
        Swal.fire({ icon: 'success', title: 'Saved', text: data.message || 'Defaults updated.', timer: 1800, showConfirmButton: false });
      } else {
        Swal.fire({ icon: 'error', title: 'Could not save', text: data.message || 'Check that the database table exists.' });
      }
    }).fail(function () {
      Swal.fire({ icon: 'error', title: 'Network error', text: 'Could not reach the server.' });
    }).always(function () {
      btn.prop('disabled', false);
    });
  });

  $(document).on('click', '.pr-save-vehicle', function () {
    var id = $(this).data('id');
    var tr = $('tr[data-vehicle-row="' + id + '"]');
    var rate = tr.find('.pr-in-rate').val();
    var fuel = tr.find('.pr-in-fuel').val();
    var driver = tr.find('.pr-in-driver').val();
    var btns = tr.find('.pr-save-vehicle, .pr-use-global');
    postVehiclePricing(tr, id, rate, fuel, driver, btns, 'Vehicle pricing saved.');
  });

  $(document).on('click', '.pr-use-global', function () {
    var id = $(this).data('id');
    var tr = $('tr[data-vehicle-row="' + id + '"]');
    var g = getGlobalPricingVals();
    var btns = tr.find('.pr-save-vehicle, .pr-use-global');
    postVehiclePricing(tr, id, g.rate, g.fuel, g.driver, btns, 'Vehicle pricing set to global defaults.');
  });
})();
</script>
