
<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>
<?php
$data['id'] = $_GET['id'];
$response = fetchSingleLead($data);
$lead = $response[0];

// print_r($lead);

function lead_text($value): string {
  return htmlspecialchars((string)($value ?? ''), ENT_QUOTES);
}
?>

<div class="main-content app-content">
  <div class="container-fluid">
    <?php
      $leadId = $lead['id'] ?? ($_GET['id'] ?? '');
      $leadName = trim((string)($lead['first_name'] ?? '') . ' ' . (string)($lead['last_name'] ?? ''));
      $leadStatus = (string)($lead['status'] ?? '');
      $tripPrice = $lead['total_price_c'] ?? 0;
      $rate = $lead['rate_c'] ?? '';
      $serviceLength = $lead['service_length_c'] ?? '';
    ?>

    <!-- Modern header -->
    <div class="mb-4">
      <div class="flex items-start justify-between gap-3 flex-wrap">
        <div class="flex items-start gap-3">
          <a href="lead.php?id=<?php echo urlencode((string)$leadId); ?>" class="ti-btn ti-btn-icon ti-btn-soft-secondary !rounded-full mt-1" aria-label="Back to Lead">
            <i class="ri-arrow-left-line"></i>
          </a>

          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <div class="text-sm text-textmuted dark:text-textmuted/50 font-medium">
                Edit Lead #<?php echo lead_text($leadId); ?>
              </div>
              <?php if (!empty($leadStatus)): ?>
                <span class="badge bg-primary/10 text-primary"><?php echo lead_text($leadStatus); ?></span>
              <?php endif; ?>
            </div>
            <div class="text-xl font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 leading-tight mt-0.5">
              <?php echo $leadName !== '' ? lead_text($leadName) : 'Edit Lead'; ?>
            </div>
            <div class="text-xs text-textmuted dark:text-textmuted/50 mt-1 flex items-center gap-2 flex-wrap">
              <?php if (!empty($lead['email1'])): ?>
                <a class="inline-flex items-center gap-1 hover:text-primary" href="mailto:<?php echo urlencode((string)$lead['email1']); ?>">
                  <i class="ri-mail-line"></i><span><?php echo lead_text($lead['email1']); ?></span>
                </a>
              <?php endif; ?>
              <?php if (!empty($lead['phone_c'])): ?>
                <span class="opacity-60">•</span>
                <a class="inline-flex items-center gap-1 hover:text-primary" href="tel:<?php echo urlencode((string)$lead['phone_c']); ?>">
                  <i class="ri-phone-line"></i><span><?php echo lead_text($lead['phone_c']); ?></span>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <a href="lead.php?id=<?php echo urlencode((string)$leadId); ?>" class="ti-btn bg-white dark:bg-bodybg border border-defaultborder dark:border-defaultborder/10 btn-wave !my-0 waves-effect waves-light">
            Cancel
          </a>
          <button type="button" id="saveLeadBtn" class="ti-btn ti-btn-primary !border-0 btn-wave me-0 waves-effect waves-light">
            <i class="ri-save-line me-1"></i> Save Changes
          </button>
        </div>
      </div>
    </div>

    <!-- Start::row-1 -->
    <form id="editLeadForm">
      <input type="hidden" name="id" value="<?php echo $lead['id']; ?>">
      
      <div class="grid grid-cols-12 gap-6">
        <div class="xl:col-span-6 col-span-12">
          <div class="box">
            <div class="box-header">
              <div class="box-title flex items-center gap-2">
                <i class="ri-user-3-line text-primary"></i>
                Lead Information
              </div>
            </div>
            <div class="box-body">
              <div class="space-y-4">
                <div class="grid grid-cols-12 gap-4">
                  <div class="col-span-12 sm:col-span-6">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" value="<?php echo $lead['first_name']; ?>">
                  </div>
                  <div class="col-span-12 sm:col-span-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" value="<?php echo $lead['last_name']; ?>">
                  </div>
                </div>
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 sm:col-span-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $lead['email1']; ?>" readonly>
                    </div>
                    
                    <div class="col-span-12 sm:col-span-6">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone" value="<?php echo !empty($lead['phone_c']) ? $lead['phone_c'] : ''; ?>">
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-4">
                  <div class="col-span-12 sm:col-span-6">
                    <label class="form-label">Distance</label>
                    <input type="text" class="form-control" name="distance" id="lead-distance" value="<?php echo $lead['distance_c']; ?>">
                  </div>
                  <div class="col-span-12 sm:col-span-6">
                    <label class="form-label">Duration</label>
                    <input type="text" class="form-control" name="duration" id="lead-duration" value="<?php echo $lead['duration_c']; ?>">
                  </div>
                </div>

                <div>
                   <label class="form-label">Status</label>
                   <select class="form-control" name="status">
                       <option value="New" <?php echo ($lead['status'] == 'New') ? 'selected' : ''; ?>>New</option>
                       <option value="Assigned" <?php echo ($lead['status'] == 'Assigned') ? 'selected' : ''; ?>>Assigned</option>
                       <option value="In Process" <?php echo ($lead['status'] == 'In Process') ? 'selected' : ''; ?>>In Process</option>
                       <option value="Converted" <?php echo ($lead['status'] == 'Converted') ? 'selected' : ''; ?>>Converted</option>
                       <option value="Recycled" <?php echo ($lead['status'] == 'Recycled') ? 'selected' : ''; ?>>Recycled</option>
                       <option value="Dead" <?php echo ($lead['status'] == 'Dead') ? 'selected' : ''; ?>>Dead</option>
                   </select>
                </div>
              </div>
            </div>
          </div>
          <div class="box">
            <div class="box-header">
              <div class="box-title flex items-center gap-2">
                <i class="ri-currency-line text-primary"></i>
                Lead Pricing
              </div>
            </div>
            <div class="box-body">
              <div class="rounded-xl border border-defaultborder dark:border-defaultborder/10 bg-primary/5 dark:bg-primary/10 p-4 mb-4">
                <div class="text-xs text-textmuted dark:text-textmuted/50">Current Total</div>
                <div class="text-2xl font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 mt-1">
                  $<?php echo lead_text($tripPrice); ?>
                </div>
                <div class="text-xs text-textmuted dark:text-textmuted/50 mt-1">
                  Update rate or hours to instantly recalculate.
                </div>
              </div>
              <div class="space-y-4">
                <div class="grid grid-cols-12 gap-4">
                  <div class="col-span-12 sm:col-span-6">
                    <label class="form-label">Rate (per hour)</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="rate" id="lead-rate" value="<?php echo lead_text($lead['rate_c'] ?? ''); ?>">
                  </div>
                  <div class="col-span-12 sm:col-span-6">
                    <label class="form-label">Service Length (Hours)</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="service_length" id="lead-service-length" value="<?php echo lead_text($lead['service_length_c'] ?? ''); ?>">
                  </div>
                </div>

                <?php
                  // Percentages live on the referenced vehicle. fetchSingleLead
                  // returns them as `vehicle_fuel_c` and `vehicle_driver_commission_c`.
                  $vehicleFuelPct       = is_numeric($lead['vehicle_fuel_c'] ?? null)
                                            ? (float)$lead['vehicle_fuel_c'] : 0.0;
                  $vehicleCommissionPct = is_numeric($lead['vehicle_driver_commission_c'] ?? null)
                                            ? (float)$lead['vehicle_driver_commission_c'] : 0.0;
                  $hasVehiclePcts       = ($vehicleFuelPct > 0 || $vehicleCommissionPct > 0);
                ?>

                <div class="grid grid-cols-12 gap-4">
                  <div class="col-span-12 sm:col-span-6">
                    <label class="form-label">Quoted Price</label>
                    <input type="text" class="form-control" id="lead-quoted-price" value="" readonly tabindex="-1">
                    <p class="text-xs text-textmuted dark:text-textmuted/50 mt-2 mb-0">
                      Computed as <span class="font-semibold">Rate × Service Length</span>.
                    </p>
                  </div>
                  <div class="col-span-12 sm:col-span-6">
                    <label class="form-label flex items-center justify-between gap-2">
                      <span>Fuel Surcharge</span>
                      <?php if ($vehicleFuelPct > 0): ?>
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold rounded-full bg-warning/10 text-warning px-2 py-0.5">
                          <i class="ri-gas-station-line"></i>
                          <?php echo rtrim(rtrim(number_format($vehicleFuelPct, 2), '0'), '.'); ?>% of quoted
                        </span>
                      <?php endif; ?>
                    </label>
                    <input type="number" step="0.01" min="0" class="form-control" name="fuel" id="lead-fuel" value="<?php echo lead_text($lead['fuel_c'] ?? ''); ?>">
                  </div>
                </div>

                <div class="grid grid-cols-12 gap-4">
                  <div class="col-span-12 sm:col-span-6">
                    <label class="form-label flex items-center justify-between gap-2">
                      <span>Driver Commission</span>
                      <?php if ($vehicleCommissionPct > 0): ?>
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold rounded-full bg-secondary/10 text-secondary px-2 py-0.5">
                          <i class="ri-steering-2-line"></i>
                          <?php echo rtrim(rtrim(number_format($vehicleCommissionPct, 2), '0'), '.'); ?>% of quoted
                        </span>
                      <?php endif; ?>
                    </label>
                    <input type="number" step="0.01" min="0" class="form-control" name="driver_commission" id="lead-commission" value="<?php echo lead_text($lead['driver_commission_c'] ?? ''); ?>">
                  </div>
                  <div class="col-span-12 sm:col-span-6">
                    <label class="form-label">Total Price</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="total_price" id="lead-total-price" value="<?php echo lead_text($lead['total_price_c'] ?? ''); ?>">
                    <p class="text-xs text-textmuted dark:text-textmuted/50 mt-2 mb-0">
                      Auto-calculated: <span class="font-semibold">Quoted + Fuel + Commission</span>.
                      Edit any field to override.
                    </p>
                  </div>
                </div>

                <?php if ($hasVehiclePcts): ?>
                  <p class="text-xs text-textmuted dark:text-textmuted/50 mt-1 mb-0 flex items-center gap-2">
                    <i class="ri-car-line text-primary"></i>
                    Fuel and commission re-calculate automatically from the assigned vehicle's percentages whenever Rate or Service Length changes.
                  </p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="xl:col-span-6 col-span-12">
          <div class="box">
            <div class="box-header">
              <div class="box-title flex items-center gap-2">
                <i class="ri-road-map-line text-primary"></i>
                Trip Details
              </div>
            </div>
            <div class="box-body">
              <div class="space-y-4">
               <div>
                  <label class="form-label">Service Type</label>
                  <select name="service_type" id="service_type" class="form-control" required>
                    <option value="">Select service</option>
                    <?php
                      $serviceOptions = [
                        "Airport",
                        "Bachelor Party",
                        "Bachelorette Party",
                        "Birthday",
                        "Casino",
                        "Church Function",
                        "Concert",
                        "Construction Shuttle",
                        "Convention",
                        "Corporate Event",
                        "Cruise Transfers",
                        "Family Reunion",
                        "General Day Trip",
                        "Golf Outing",
                        "Homecoming",
                        "Night out on Town",
                        "Over the Road",
                        "Prom",
                        "School Trip",
                        "Shuttle Service",
                        "Sports Event",
                        "Theme Park",
                        "Transfer",
                        "Wedding",
                        "Wedding Wire",
                        "Wine Tour",
                      ];

                      $currentService = (string)($lead['service_type_c'] ?? '');
                      foreach ($serviceOptions as $opt) {
                        $selected = ($opt === $currentService) ? ' selected' : '';
                        echo '<option value="'.htmlspecialchars($opt, ENT_QUOTES).'"'.$selected.'>'.htmlspecialchars($opt).'</option>';
                      }
                    ?>
                  </select>
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <label class="form-label">Event Date</label>
                        <input type="date" class="form-control" name="event_date" value="<?php echo date('Y-m-d', strtotime($lead['event_date_c'])); ?>">
                    </div>
                </div>

                <div>
                  <label class="form-label">Passengers</label>
                  <input type="number" class="form-control" name="passengers" value="<?php echo $lead['passengers_c']; ?>">
                </div>

                <div>
                  <label class="form-label">Pickup Address</label>
                  <input type="text" class="form-control" name="pickup_address" value="<?php echo $lead['pickup_address_c']; ?>">
                </div>

                <div>
                  <label class="form-label">Dropoff Address</label>
                  <input type="text" class="form-control" name="dropoff_address" value="<?php echo $lead['dropoff_address_c']; ?>">
                </div>
                
                

                <div>
                  <label class="form-label">
                    <i class="ri-sticky-note-line text-warning me-1"></i>
                    Notes / Special Requests
                  </label>
                  <textarea class="form-control" name="notes" rows="3"
                            placeholder="Flight info, gate codes, child seats, special requests..."><?php echo lead_text($lead['notes_c'] ?? ''); ?></textarea>
                  <p class="text-xs text-textmuted dark:text-textmuted/50 mt-2 mb-0">
                    Saved on the lead as <span class="font-semibold">notes_c</span>.
                  </p>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
    <!--End::row-1 -->
  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        function toNumber(val) {
            if (val === null || val === undefined) return 0;
            const num = parseFloat(String(val).replace(/,/g, ''));
            return Number.isFinite(num) ? num : 0;
        }

        function formatMoney(num) {
            const n = Number.isFinite(num) ? num : 0;
            return (Math.round(n * 100) / 100).toFixed(2);
        }

        // Vehicle percentages pulled from the referenced vehicle by fetchSingleLead.
        // 0 means "no vehicle assigned" or "vehicle has no surcharge configured" —
        // in that case we leave the absolute amounts alone.
        const VEHICLE_FUEL_PCT       = <?php echo $vehicleFuelPct; ?>;
        const VEHICLE_COMMISSION_PCT = <?php echo $vehicleCommissionPct; ?>;

        // Pricing model:
        //   quoted     = rate * hours
        //   fuel       = quoted * VEHICLE_FUEL_PCT       / 100   (when % > 0)
        //   commission = quoted * VEHICLE_COMMISSION_PCT / 100   (when % > 0)
        //   total      = quoted + fuel + commission
        //
        // Editing rate / hours auto-recomputes fuel + commission from the
        // vehicle's percentages, then the total.
        // Editing fuel or commission directly is a manual override — total
        // re-totals from the new components.
        // Editing total directly is also an override — components stay put.
        function recalcPricing(changedField) {
            const $rate       = $('#lead-rate');
            const $hours      = $('#lead-service-length');
            const $quoted     = $('#lead-quoted-price');
            const $fuel       = $('#lead-fuel');
            const $commission = $('#lead-commission');
            const $total      = $('#lead-total-price');

            const rate  = toNumber($rate.val());
            const hours = toNumber($hours.val());

            const quoted = rate * hours;
            $quoted.val('$' + formatMoney(quoted));

            // Re-derive fuel + commission from the vehicle's % whenever the
            // quoted price changed (i.e. rate / hours edits).
            if (changedField === 'rate' || changedField === 'hours') {
                if (VEHICLE_FUEL_PCT > 0) {
                    $fuel.val(formatMoney(quoted * VEHICLE_FUEL_PCT / 100));
                }
                if (VEHICLE_COMMISSION_PCT > 0) {
                    $commission.val(formatMoney(quoted * VEHICLE_COMMISSION_PCT / 100));
                }
            }

            if (changedField === 'total') {
                // Manual total override — leave components alone.
                return;
            }

            const fuel       = toNumber($fuel.val());
            const commission = toNumber($commission.val());
            $total.val(formatMoney(quoted + fuel + commission));
        }

        $('#lead-rate').on('input',           function() { recalcPricing('rate'); });
        $('#lead-service-length').on('input', function() { recalcPricing('hours'); });
        $('#lead-fuel').on('input',           function() { recalcPricing('fuel'); });
        $('#lead-commission').on('input',     function() { recalcPricing('commission'); });
        $('#lead-total-price').on('input',    function() { recalcPricing('total'); });

        // Initial pass: just update the read-only quoted display + total
        // from existing values. Don't overwrite stored fuel/commission on
        // page load — they may already be a manual override.
        recalcPricing('init');

        $('#saveLeadBtn').click(function() {
            var formData = $('#editLeadForm').serialize();
            
            // Show loading state
            var btn = $(this);
            var originalText = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm align-middle me-1"></span> Saving...').prop('disabled', true);

            $.ajax({
                url: 'config/update_lead_endpoint.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    // Check if the response from API is wrapped
                    // The CustomEntryPoint returns json_encoded string sometimes depending on implementation
                    // But here we controlled the endpoint.
                    
                    var data = response;
                    // If response is a string (double encoded), parse it
                    if (typeof response === 'string') {
                        try {
                            data = JSON.parse(response);
                        } catch (e) {
                            console.error("Parsing error", e);
                        }
                    }

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Lead updated successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = 'lead.php?id=' + $('input[name="id"]').val();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to update lead'
                        });
                        btn.html(originalText).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving.'
                    });
                    btn.html(originalText).prop('disabled', false);
                }
            });
        });
    });
</script>

<style>
  /* Modern, theme-friendly form controls on this page */
  .form-label{
    font-size: 12px !important;
    font-weight: 600 !important;
    color: rgba(15, 23, 42, 0.65) !important;
    margin-bottom: 6px !important;
  }
  .dark .form-label{
    color: rgba(255,255,255,0.55) !important;
  }

  .form-control{
    border: 1px solid rgba(15, 23, 42, 0.12) !important;
    border-radius: 10px !important;
    padding: 10px 12px !important;
    height: 42px !important;
    line-height: 20px !important;
    background: rgba(15, 23, 42, 0.02) !important;
    color: rgba(15, 23, 42, 0.92) !important;
  }
  .form-control::placeholder{
    color: rgba(15, 23, 42, 0.42) !important;
  }
  .dark .form-control{
    border-color: rgba(255,255,255,0.12) !important;
    background: rgba(255,255,255,0.04) !important;
    color: rgba(255,255,255,0.90) !important;
  }
  .dark .form-control::placeholder{
    color: rgba(255,255,255,0.45) !important;
  }
  .form-control:focus{
    border-color: var(--primary-color, #cf1c82) !important;
    box-shadow: 0 0 0 3px rgba(207, 28, 130, 0.14) !important;
  }

  /* Select caret spacing */
  select.form-control{
    padding-right: 36px !important;
    cursor: pointer;
    background-image: linear-gradient(45deg, transparent 50%, currentColor 50%),
                      linear-gradient(135deg, currentColor 50%, transparent 50%);
    background-position: calc(100% - 18px) 50%, calc(100% - 13px) 50%;
    background-size: 6px 6px, 6px 6px;
    background-repeat: no-repeat;
  }

  /* Keep textarea comfortable (not forced to 42px) */
  textarea.form-control{
    height: auto !important;
    min-height: 110px;
    padding: 10px 12px !important;
    resize: vertical;
  }
</style>