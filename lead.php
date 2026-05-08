

<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>
<?php
$data['id'] = $_GET['id'];
$response = fetchSingleLead($data);

// print_r($response);
?>



<?php
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
  $first = $parts[0] ?? '';
  $last = $parts[count($parts) - 1] ?? '';
  $i1 = $first !== '' ? mb_substr($first, 0, 1) : '';
  $i2 = $last !== '' ? mb_substr($last, 0, 1) : '';
  $out = strtoupper($i1 . $i2);
  return $out !== '' ? $out : '?';
}
?>

<div class="main-content app-content">
      <div class="container-fluid">
        <?php
          $leadId = $lead['id'] ?? ($_GET['id'] ?? '');
          $leadName = trim((string)($lead['first_name'] ?? '') . ' ' . (string)($lead['last_name'] ?? ''));
          $leadStatus = (string)($lead['status'] ?? '');
          $tripPrice = $lead['total_price_c'] ?? 0;
        ?>

        <!-- Modern header -->
        <div class="mb-4">
          <div class="flex items-start justify-between gap-3 flex-wrap">
            <div class="flex items-start gap-3">
              <a href="leads.php" class="ti-btn ti-btn-icon ti-btn-soft-secondary !rounded-full mt-1" aria-label="Back to Leads">
                <i class="ri-arrow-left-line"></i>
              </a>

              <div class="flex items-start gap-3">
                <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-semibold">
                  <?php echo lead_text(lead_initials($leadName)); ?>
                </div>

                <div>
                  <div class="flex items-center gap-2 flex-wrap">
                    <div class="text-sm text-textmuted dark:text-textmuted/50 font-medium">
                      Lead #<?php echo lead_text($leadId); ?>
                    </div>
                    <?php if (!empty($leadStatus)): ?>
                      <span class="badge bg-primary/10 text-primary"><?php echo lead_text($leadStatus); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($lead['service_type_c'])): ?>
                      <span class="badge bg-secondary/10 text-secondary"><?php echo lead_text($lead['service_type_c']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($lead['event_date_c'])): ?>
                      <span class="badge bg-success/10 text-success">
                        <i class="ri-calendar-line me-1 align-middle"></i><?php echo lead_text($lead['event_date_c']); ?>
                      </span>
                    <?php endif; ?>
                  </div>

                  <div class="text-xl font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 leading-tight mt-0.5">
                    <?php echo $leadName !== '' ? lead_text($leadName) : 'Lead Details'; ?>
                  </div>

                  <div class="mt-1 flex items-center gap-2 flex-wrap text-xs text-textmuted dark:text-textmuted/50">
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
                    <?php if (!empty($lead['date_entered'])): ?>
                      <span class="opacity-60">•</span>
                      <span class="inline-flex items-center gap-1"><i class="ri-time-line"></i>Created <?php echo lead_text($lead['date_entered']); ?></span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <?php
              $leadEmail   = (string)($lead['email1']   ?? '');
              $hasEmail    = $leadEmail !== '';
              $disabledTip = $hasEmail ? '' : 'No email on file for this lead';
            ?>

            <div class="btn-list flex flex-wrap items-center gap-2">
              <button
                type="button"
                id="send-formal-quote-btn"
                class="ti-btn bg-warning text-white ti-btn-sm btn-wave waves-effect waves-light"
                data-email-type="formal_quote"
                data-email-label="Formal Quote"
                <?php echo $hasEmail ? '' : 'disabled'; ?>
                <?php if (!$hasEmail): ?>title="<?php echo lead_text($disabledTip); ?>"<?php endif; ?>
              >
                <i class="ri-mail-send-line me-1 align-middle"></i>Send Formal Quote
              </button>

              <button
                type="button"
                id="send-agreement-btn"
                class="ti-btn bg-success text-white ti-btn-sm btn-wave waves-effect waves-light"
                data-email-type="agreement"
                data-email-label="Agreement"
                <?php echo $hasEmail ? '' : 'disabled'; ?>
                <?php if (!$hasEmail): ?>title="<?php echo lead_text($disabledTip); ?>"<?php endif; ?>
              >
                <i class="ri-file-text-line me-1 align-middle"></i>Send Agreement
              </button>

              <a href="edit_lead.php?id=<?php echo urlencode((string)$leadId); ?>" type="button" class="ti-btn bg-primary text-white ti-btn-sm btn-wave me-0 waves-effect waves-light">
                <i class="ri-edit-line me-1 align-middle"></i>Edit Lead
              </a>
            </div>
          </div>
        </div>

        <!-- Main panels -->
        <div class="grid grid-cols-12 gap-6">
          <!-- Client Details -->
          <div class="col-span-12 xl:col-span-4">
            <div class="box overflow-hidden h-full">
              <div class="box-header">
                <div class="box-title flex items-center gap-2">
                  <i class="ri-user-3-line text-primary"></i>
                  Client Details
                </div>
              </div>
              <div class="box-body">
                <div class="space-y-2">
                  <div class="flex items-center justify-between gap-3 py-2 border-b border-defaultborder dark:border-defaultborder/10">
                    <div class="text-xs text-textmuted dark:text-textmuted/50 flex items-center gap-2">
                      <i class="ri-user-line"></i><span>Name</span>
                    </div>
                    <div class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 font-medium text-right">
                      <?php echo lead_text($leadName); ?>
                    </div>
                  </div>

                  <div class="flex items-center justify-between gap-3 py-2 border-b border-defaultborder dark:border-defaultborder/10">
                    <div class="text-xs text-textmuted dark:text-textmuted/50 flex items-center gap-2">
                      <i class="ri-mail-line"></i><span>Email</span>
                    </div>
                    <div class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 text-right">
                      <?php if (!empty($lead['email1'])): ?>
                        <a class="hover:text-primary" href="mailto:<?php echo urlencode((string)$lead['email1']); ?>"><?php echo lead_text($lead['email1']); ?></a>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="flex items-center justify-between gap-3 py-2 border-b border-defaultborder dark:border-defaultborder/10">
                    <div class="text-xs text-textmuted dark:text-textmuted/50 flex items-center gap-2">
                      <i class="ri-phone-line"></i><span>Phone</span>
                    </div>
                    <div class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 text-right">
                      <?php if (!empty($lead['phone_c'])): ?>
                        <a class="hover:text-primary" href="tel:<?php echo urlencode((string)$lead['phone_c']); ?>"><?php echo lead_text($lead['phone_c']); ?></a>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="flex items-center justify-between gap-3 py-2 border-b border-defaultborder dark:border-defaultborder/10">
                    <div class="text-xs text-textmuted dark:text-textmuted/50 flex items-center gap-2">
                      <i class="ri-flag-line"></i><span>Status</span>
                    </div>
                    <div class="text-right">
                      <?php if (!empty($leadStatus)): ?>
                        <span class="badge bg-primary/10 text-primary"><?php echo lead_text($leadStatus); ?></span>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="flex items-center justify-between gap-3 py-2">
                    <div class="text-xs text-textmuted dark:text-textmuted/50 flex items-center gap-2">
                      <i class="ri-time-line"></i><span>Date Created</span>
                    </div>
                    <div class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 text-right">
                      <?php echo lead_text($lead['date_entered'] ?? ''); ?>
                    </div>
                  </div>
                </div>

                <?php if (!empty($lead['description'])): ?>
                  <div class="mt-4 pt-4 border-t border-defaultborder dark:border-defaultborder/10">
                    <div class="text-xs text-textmuted dark:text-textmuted/50 mb-1 flex items-center gap-2">
                      <i class="ri-file-text-line"></i><span>Description</span>
                    </div>
                    <div class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 whitespace-pre-line">
                      <?php echo lead_text($lead['description']); ?>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if (!empty($lead['notes_c'])): ?>
                  <div class="mt-4 pt-4 border-t border-defaultborder dark:border-defaultborder/10">
                    <div class="text-xs text-textmuted dark:text-textmuted/50 mb-1 flex items-center gap-2">
                      <i class="ri-sticky-note-line text-warning"></i><span>Notes / Special Requests</span>
                    </div>
                    <div class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 whitespace-pre-line">
                      <?php echo lead_text($lead['notes_c']); ?>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Trip Details -->
          <div class="col-span-12 xl:col-span-4">
            <div class="box overflow-hidden h-full">
              <div class="box-header">
                <div class="box-title flex items-center gap-2">
                  <i class="ri-road-map-line text-primary"></i>
                  Trip Details
                </div>
              </div>
              <div class="box-body">
                <div class="space-y-2">
                  <div class="flex items-center justify-between gap-3 py-2 border-b border-defaultborder dark:border-defaultborder/10">
                    <div class="text-xs text-textmuted dark:text-textmuted/50 flex items-center gap-2">
                      <i class="ri-briefcase-line"></i><span>Service Type</span>
                    </div>
                    <div class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 font-medium text-right">
                      <?php echo lead_text($lead['service_type_c'] ?? ''); ?>
                    </div>
                  </div>

                  <div class="flex items-center justify-between gap-3 py-2 border-b border-defaultborder dark:border-defaultborder/10">
                    <div class="text-xs text-textmuted dark:text-textmuted/50 flex items-center gap-2">
                      <i class="ri-calendar-line"></i><span>Event Date</span>
                    </div>
                    <div class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 text-right">
                      <?php echo lead_text($lead['event_date_c'] ?? ''); ?>
                    </div>
                  </div>

                  <div class="flex items-center justify-between gap-3 py-2 border-b border-defaultborder dark:border-defaultborder/10">
                    <div class="text-xs text-textmuted dark:text-textmuted/50 flex items-center gap-2">
                      <i class="ri-group-line"></i><span>Passengers</span>
                    </div>
                    <div class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 text-right">
                      <?php echo lead_text($lead['passengers_c'] ?? ''); ?>
                    </div>
                  </div>

                  <div class="flex items-center justify-between gap-3 py-2 border-b border-defaultborder dark:border-defaultborder/10">
                    <div class="text-xs text-textmuted dark:text-textmuted/50 flex items-center gap-2">
                      <i class="ri-route-line"></i><span>Distance</span>
                    </div>
                    <div class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 text-right">
                      <?php echo lead_text($lead['distance_c'] ?? ''); ?>
                    </div>
                  </div>

                  <div class="flex items-center justify-between gap-3 py-2">
                    <div class="text-xs text-textmuted dark:text-textmuted/50 flex items-center gap-2">
                      <i class="ri-timer-line"></i><span>Duration</span>
                    </div>
                    <div class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 text-right">
                      <?php echo lead_text($lead['duration_c'] ?? ''); ?>
                    </div>
                  </div>

                  <div class="mt-4 pt-4 border-t border-defaultborder dark:border-defaultborder/10 space-y-3">
                    <div>
                      <div class="text-xs text-textmuted dark:text-textmuted/50 mb-1 flex items-center gap-2">
                        <i class="ri-map-pin-line text-success"></i><span>Pickup</span>
                      </div>
                      <div class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 whitespace-pre-line">
                        <?php echo lead_text($lead['pickup_address_c'] ?? ''); ?>
                      </div>
                    </div>
                    <div>
                      <div class="text-xs text-textmuted dark:text-textmuted/50 mb-1 flex items-center gap-2">
                        <i class="ri-map-pin-line text-danger"></i><span>Dropoff</span>
                      </div>
                      <div class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 whitespace-pre-line">
                        <?php echo lead_text($lead['dropoff_address_c'] ?? ''); ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Price Breakdown -->
          <div class="col-span-12 xl:col-span-4">
            <div class="box overflow-hidden h-full">
              <div class="box-header">
                <div class="box-title flex items-center gap-2">
                  <i class="ri-currency-line text-primary"></i>
                  Price Breakdown
                </div>
              </div>
              <div class="box-body">
                <?php
                  // ---- Pricing inputs (defensive parsing) ----
                  $toFloat = function ($v) {
                      if ($v === null || $v === '') return 0.0;
                      if (is_numeric($v)) return (float)$v;
                      return (float)preg_replace('/[^0-9.\-]/', '', (string)$v);
                  };

                  $serviceLength    = $lead['service_length_c'] ?? null;
                  $rate             = $lead['rate_c'] ?? null;
                  $fuelRaw          = $lead['fuel_c'] ?? null;
                  $commissionRaw    = $lead['driver_commission_c'] ?? ($lead['commission_c'] ?? null);

                  $serviceLengthNum = $toFloat($serviceLength);
                  $rateNum          = $toFloat($rate);
                  $fuelNum          = $toFloat($fuelRaw);
                  $commissionNum    = $toFloat($commissionRaw);
                  $totalNum         = $toFloat($tripPrice);

                  $hasCalc     = ($serviceLengthNum > 0 && $rateNum > 0);
                  $quotedPrice = $hasCalc ? round($serviceLengthNum * $rateNum, 2) : 0.0;

                  $sumOfParts      = round($quotedPrice + $fuelNum + $commissionNum, 2);
                  $hasMismatch     = $totalNum > 0
                                      && abs($sumOfParts - $totalNum) >= 0.01
                                      && ($quotedPrice > 0 || $fuelNum > 0 || $commissionNum > 0);
                ?>

                <div class="rounded-xl border border-defaultborder dark:border-defaultborder/10 bg-primary/5 dark:bg-primary/10 p-4 mb-4">
                  <div class="text-xs text-textmuted dark:text-textmuted/50">Total Trip Cost</div>
                  <div class="text-2xl font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 mt-1">
                    <?php echo lead_money($tripPrice); ?>
                  </div>
                  <?php if ($hasMismatch): ?>
                    <div class="text-[11px] text-warning mt-1 flex items-center gap-1">
                      <i class="ri-information-line"></i>
                      Line items add up to <?php echo lead_money($sumOfParts); ?>
                    </div>
                  <?php endif; ?>
                </div>

                <div class="text-[11px] text-textmuted dark:text-textmuted/50 uppercase tracking-wider font-semibold mb-2">
                  Calculation
                </div>

                <div class="rounded-xl border border-defaultborder dark:border-defaultborder/10 divide-y divide-defaultborder dark:divide-defaultborder/10">
                  <!-- Quoted Price (service length × rate) -->
                  <div class="px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                      <span class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 font-medium flex items-center gap-2">
                        <i class="ri-cash-line text-primary"></i>
                        Quoted Price
                      </span>
                      <span class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 font-semibold">
                        <?php echo $hasCalc ? lead_money($quotedPrice) : '—'; ?>
                      </span>
                    </div>
                    <?php if ($hasCalc): ?>
                      <div class="text-[11px] text-textmuted dark:text-textmuted/50 mt-1 ms-6">
                        <?php echo rtrim(rtrim(number_format($serviceLengthNum, 2), '0'), '.'); ?>
                        <span class="opacity-70">hrs</span>
                        <span class="mx-1">×</span>
                        <?php echo lead_money($rateNum); ?>
                        <span class="opacity-70">/hr</span>
                      </div>
                    <?php endif; ?>
                  </div>

                  <!-- Fuel surcharge -->
                  <div class="px-4 py-3 flex items-center justify-between gap-3">
                    <span class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 font-medium flex items-center gap-2">
                      <i class="ri-gas-station-line text-warning"></i>
                      Fuel Surcharge
                    </span>
                    <span class="text-sm <?php echo $fuelNum > 0 ? 'text-defaulttextcolor dark:text-defaulttextcolor/90 font-semibold' : 'text-textmuted dark:text-textmuted/50'; ?>">
                      <?php echo $fuelNum > 0 ? '+ ' . lead_money($fuelNum) : '—'; ?>
                    </span>
                  </div>

                  <!-- Driver commission -->
                  <div class="px-4 py-3 flex items-center justify-between gap-3">
                    <span class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 font-medium flex items-center gap-2">
                      <i class="ri-steering-2-line text-secondary"></i>
                      Driver Commission
                    </span>
                    <span class="text-sm <?php echo $commissionNum > 0 ? 'text-defaulttextcolor dark:text-defaulttextcolor/90 font-semibold' : 'text-textmuted dark:text-textmuted/50'; ?>">
                      <?php echo $commissionNum > 0 ? '+ ' . lead_money($commissionNum) : '—'; ?>
                    </span>
                  </div>

                  <!-- Final total -->
                  <div class="px-4 py-3 bg-primary/5 dark:bg-primary/10 flex items-center justify-between gap-3">
                    <span class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 font-bold flex items-center gap-2">
                      <i class="ri-checkbox-circle-line text-success"></i>
                      Total
                    </span>
                    <span class="text-base text-primary font-bold">
                      <?php echo lead_money($tripPrice); ?>
                    </span>
                  </div>
                </div>
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
          // Shared payload — both buttons send the same identifying info, the
          // backend picks the right template based on `email_type`.
          const LEAD_PAYLOAD = {
            lead_id: <?php echo json_encode((string)$leadId); ?>,
            email:   <?php echo json_encode($leadEmail); ?>,
            name:    <?php echo json_encode($leadName); ?>
          };

          function originalButtonHtml($btn, label) {
            // Lazy-cache the original markup so we can restore after loading.
            if (!$btn.data('original-html')) {
              $btn.data('original-html', $btn.html());
            }
            return $btn.data('original-html');
          }

          function sendLeadEmail($btn) {
            const emailType  = $btn.data('email-type');
            const emailLabel = $btn.data('email-label');

            if (!LEAD_PAYLOAD.email) {
              Swal.fire({
                icon: 'warning',
                title: 'No email on file',
                text:  'Add an email address to this lead before sending a ' + emailLabel + '.'
              });
              return;
            }

            Swal.fire({
              icon: 'question',
              title: 'Send ' + emailLabel + '?',
              html:  'This will email the <b>' + emailLabel + '</b> to ' +
                     '<b>' + $('<div>').text(LEAD_PAYLOAD.email).html() + '</b>.',
              showCancelButton: true,
              confirmButtonText: 'Send ' + emailLabel,
              cancelButtonText:  'Cancel',
              confirmButtonColor: emailType === 'agreement' ? '#22c55e' : '#f59e0b'
            }).then(function (res) {
              if (!res.isConfirmed) return;

              originalButtonHtml($btn);
              $btn.html('<span class="spinner-border spinner-border-sm align-middle me-1"></span> Sending...')
                  .prop('disabled', true);

              $.ajax({
                url: 'config/send_lead_email_endpoint.php',
                type: 'POST',
                dataType: 'json',
                data: $.extend({}, LEAD_PAYLOAD, { email_type: emailType }),
                success: function (data) {
                  if (data && data.success) {
                    Swal.fire({
                      icon: 'success',
                      title: emailLabel + ' sent',
                      text:  data.message || ('The ' + emailLabel + ' email has been queued for delivery.'),
                      timer: 2200,
                      showConfirmButton: false
                    });
                  } else {
                    Swal.fire({
                      icon: 'error',
                      title: 'Could not send ' + emailLabel,
                      text:  (data && data.message) || 'Please try again, or check that a workflow / template is configured for this action.'
                    });
                  }
                },
                error: function (xhr) {
                  console.error(xhr.responseText);
                  Swal.fire({
                    icon: 'error',
                    title: 'Network error',
                    text:  'We could not reach the server. Please try again.'
                  });
                },
                complete: function () {
                  $btn.html($btn.data('original-html')).prop('disabled', false);
                }
              });
            });
          }

          $('#send-formal-quote-btn, #send-agreement-btn').on('click', function () {
            sendLeadEmail($(this));
          });
        });
      </script>