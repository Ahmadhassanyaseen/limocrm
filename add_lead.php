<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<style>
  .al-page { --al-surface: #ffffff; --al-surface-2: #f8fafc; --al-border: rgba(15,23,42,0.10); --al-text: #0f172a; --al-muted: rgba(15,23,42,0.55); }
  .dark .al-page { --al-surface: rgba(255,255,255,0.035); --al-surface-2: rgba(255,255,255,0.05); --al-border: rgba(255,255,255,0.08); --al-text: rgba(255,255,255,0.92); --al-muted: rgba(255,255,255,0.50); }

  .al-page .al-label { font-size: 12px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--al-muted); margin-bottom: 6px; display: block; }
  .al-page .al-input {
    height: 44px; border-radius: 12px; border: 1px solid var(--al-border);
    background: var(--al-surface-2); padding: 0 14px; width: 100%;
    font-size: 14px; color: var(--al-text); transition: border-color 0.2s, box-shadow 0.2s; outline: none;
  }
  .al-page .al-input:focus { border-color: rgb(var(--primary-rgb)); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12); }
  .al-page .al-input.is-invalid { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,0.10) !important; }
  .al-page textarea.al-input { height: auto; min-height: 100px; resize: vertical; padding: 12px 14px; }
  .al-page select.al-input {
    cursor: pointer; -webkit-appearance: none; appearance: none; padding-right: 38px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M8 11.4 2.6 6h10.8L8 11.4z'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 14px center;
  }

  .al-page .al-input-group { position: relative; display: flex; align-items: stretch; }
  .al-page .al-input-group .al-input { border-radius: 0 12px 12px 0; }
  .al-page .al-input-group .al-addon {
    display: flex; align-items: center; justify-content: center;
    min-width: 44px; font-size: 14px; font-weight: 700;
    color: var(--al-muted); background: rgba(15,23,42,0.04);
    border: 1px solid var(--al-border); border-right: none; border-radius: 12px 0 0 12px;
  }
  .dark .al-page .al-input-group .al-addon { background: rgba(255,255,255,0.06); }
  .al-page .al-input-group-right .al-input { border-radius: 12px 0 0 12px; }
  .al-page .al-input-group-right .al-addon { border: 1px solid var(--al-border); border-left: none; border-radius: 0 12px 12px 0; }

  .al-page .al-error { font-size: 11px; color: #ef4444; margin-top: 5px; display: none; align-items: center; gap: 4px; }
  .al-page .al-error.show { display: flex; }

  .al-page .al-card { background: var(--al-surface); border: 1px solid var(--al-border); border-radius: 16px; overflow: hidden; transition: box-shadow 0.2s; }
  .al-page .al-card:hover { box-shadow: 0 4px 24px rgba(15,23,42,0.06); }
  .dark .al-page .al-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.25); }
  .al-page .al-card-header { background: rgba(15,23,42,0.025); border-bottom: 1px solid rgba(15,23,42,0.08); padding: 18px 24px; }
  .dark .al-page .al-card-header { background: rgba(255,255,255,0.025); border-bottom-color: rgba(255,255,255,0.08); }
  .al-page .al-card-body { padding: 24px; }

  .al-page .al-card-header .al-card-icon {
    width: 32px; height: 32px; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
  }
  .al-page .al-step-num {
    width: 22px; height: 22px; border-radius: 50%; background: rgb(var(--primary-rgb));
    color: #fff; font-size: 11px; font-weight: 800;
    display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
  }

  /* Sticky Header */
  .al-sticky-header {
    position: sticky; top: 0; z-index: 99;
    border-bottom: 1px solid var(--al-border);
    padding: 14px 24px; margin: -24px -24px 24px -24px;
    backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
    background: rgba(255,255,255,0.88); transition: box-shadow 0.2s;
  }
  .dark .al-sticky-header { background: rgba(18,18,30,0.88); }
  .al-sticky-header.scrolled { box-shadow: 0 4px 20px rgba(15,23,42,0.08); }
  .dark .al-sticky-header.scrolled { box-shadow: 0 4px 20px rgba(0,0,0,0.30); }

  .al-page .al-save-btn {
    height: 42px; border-radius: 12px; border: none;
    background: rgb(var(--primary-rgb)); color: #fff;
    padding: 0 24px; font-size: 14px; font-weight: 700;
    cursor: pointer; transition: all 0.2s;
    display: inline-flex; align-items: center; gap: 8px;
  }
  .al-page .al-save-btn:hover { filter: brightness(1.08); box-shadow: 0 4px 16px rgba(var(--primary-rgb), 0.3); }
  .al-page .al-save-btn:disabled { opacity: 0.6; cursor: not-allowed; }
  .al-page .al-cancel-btn {
    height: 42px; border-radius: 12px;
    border: 1px solid var(--al-border); background: var(--al-surface);
    color: var(--al-text); padding: 0 20px; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all 0.15s; text-decoration: none;
    display: inline-flex; align-items: center;
  }
  .al-page .al-cancel-btn:hover { background: var(--al-surface-2); }

  /* Vehicle Selection Grid */
  .al-vehicle-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
  .al-vehicle-card {
    border: 2px solid var(--al-border); border-radius: 14px;
    overflow: hidden; cursor: pointer; transition: all 0.2s;
    background: var(--al-surface);
  }
  .al-vehicle-card:hover { border-color: rgba(var(--primary-rgb), 0.3); box-shadow: 0 4px 20px rgba(15,23,42,0.08); transform: translateY(-2px); }
  .dark .al-vehicle-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.25); }
  .al-vehicle-card.selected { border-color: rgb(var(--primary-rgb)); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.15); }
  .al-vehicle-card .al-veh-img {
    width: 100%; aspect-ratio: 16/10; object-fit: cover; display: block;
    background: var(--al-surface-2);
  }
  .al-vehicle-card .al-veh-img-placeholder {
    width: 100%; aspect-ratio: 16/10; display: flex;
    align-items: center; justify-content: center;
    background: var(--al-surface-2); color: var(--al-muted); font-size: 32px;
  }
  .al-vehicle-card .al-veh-body { padding: 12px 14px; }
  .al-vehicle-card .al-veh-name { font-size: 14px; font-weight: 700; color: var(--al-text); margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .al-vehicle-card .al-veh-meta { font-size: 11px; color: var(--al-muted); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
  .al-vehicle-card .al-veh-rate { font-size: 13px; font-weight: 800; color: rgb(var(--primary-rgb)); margin-top: 6px; }
  .al-vehicle-card .al-veh-check {
    position: absolute; top: 10px; right: 10px;
    width: 26px; height: 26px; border-radius: 50%;
    background: rgb(var(--primary-rgb)); color: #fff;
    display: none; align-items: center; justify-content: center; font-size: 14px;
    box-shadow: 0 2px 8px rgba(var(--primary-rgb), 0.3);
  }
  .al-vehicle-card.selected .al-veh-check { display: flex; }
  .al-vehicle-card .al-veh-img-wrap { position: relative; }

  .al-vehicle-search {
    height: 38px; border-radius: 10px; border: 1px solid var(--al-border);
    background: var(--al-surface-2); color: var(--al-text);
    padding: 0 12px 0 36px!important; font-size: 13px; outline: none;
    width: min(300px, 100%); transition: border-color 0.2s;
  }
  .al-vehicle-search:focus { border-color: rgb(var(--primary-rgb)); }

  /* Summary sidebar */
  .al-summary-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(15,23,42,0.06); font-size: 13px; }
  .dark .al-summary-row { border-bottom-color: rgba(255,255,255,0.06); }
  .al-summary-row:last-child { border-bottom: none; }
  .al-summary-label { color: var(--al-muted); }
  .al-summary-value { font-weight: 600; color: var(--al-text); max-width: 180px; text-align: right; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .al-summary-total { font-size: 22px; font-weight: 800; color: rgb(var(--primary-rgb)); letter-spacing: -0.02em; }

  .al-skeleton { background: linear-gradient(90deg, var(--al-surface-2) 25%, rgba(var(--primary-rgb),0.04) 50%, var(--al-surface-2) 75%); background-size: 200% 100%; animation: al-shimmer 1.5s infinite; border-radius: 10px; }
  @keyframes al-shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

  /* Address autocomplete dropdown */
  .al-addr-wrap { position: relative; }
  .al-addr-list {
    position: absolute; top: 100%; left: 0; right: 0; z-index: 120;
    background: var(--al-surface); border: 1px solid var(--al-border);
    border-radius: 12px; margin-top: 4px; max-height: 240px; overflow-y: auto;
    box-shadow: 0 10px 36px rgba(15,23,42,0.12);
    display: none;
  }
  .dark .al-addr-list { background: #1e1e2e; border-color: rgba(255,255,255,0.12); box-shadow: 0 10px 36px rgba(0,0,0,0.5); }
  .al-addr-list.show { display: block; }
  .al-addr-item {
    padding: 10px 14px; cursor: pointer; font-size: 13px;
    color: var(--al-text); display: flex; align-items: flex-start; gap: 10px;
    border-bottom: 1px solid rgba(15,23,42,0.04); transition: background 0.1s;
  }
  .dark .al-addr-item { border-bottom-color: rgba(255,255,255,0.04); }
  .al-addr-item:last-child { border-bottom: none; }
  .al-addr-item:hover, .al-addr-item.active { background: rgba(var(--primary-rgb), 0.06); }
  .al-addr-item i { color: var(--al-muted); font-size: 16px; margin-top: 1px; flex-shrink: 0; }
  .al-addr-item-text { line-height: 1.4; }
  .al-addr-item-main { font-weight: 600; }
  .al-addr-item-sub { font-size: 11px; color: var(--al-muted); margin-top: 1px; }
  .al-addr-loading { padding: 14px; text-align: center; font-size: 12px; color: var(--al-muted); }
</style>

<div class="main-content app-content">
  <div class="container-fluid al-page">

    <!-- Sticky Header -->
    <div class="al-sticky-header" id="al-sticky-header">
      <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
          <a href="leads.php" class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary hover:bg-primary/20 transition-colors flex-shrink-0" title="Back to Leads">
            <i class="ri-arrow-left-line text-lg"></i>
          </a>
          <div>
            <h1 class="text-lg font-bold mb-0 leading-tight" style="color:var(--al-text)">New Lead</h1>
            <div style="font-size:12px;color:var(--al-muted);">Fill in the details and select a vehicle to create a new lead.</div>
          </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <a href="leads.php" class="al-cancel-btn">Cancel</a>
          <button type="button" id="al-save-btn" class="al-save-btn">
            <i class="ri-save-line"></i> Create Lead
          </button>
        </div>
      </div>
    </div>

    <!-- Form -->
    <form id="addLeadForm">
      <div class="grid grid-cols-12 gap-6 pb-12">

        <!-- LEFT COLUMN -->
        <div class="xl:col-span-8 col-span-12 space-y-6">

          <!-- Step 1: Lead Information -->
          <div class="al-card" id="intro-lead-information">
            <div class="al-card-header">
              <div class="flex items-center gap-3">
                <span class="al-step-num">1</span>
                <div class="al-card-icon bg-primary/10 text-primary"><i class="ri-user-3-line"></i></div>
                <div>
                  <div class="font-semibold text-sm" style="color:var(--al-text)">Lead Information</div>
                  <div style="font-size:11px;color:var(--al-muted)">Contact details of the customer</div>
                </div>
              </div>
            </div>
            <div class="al-card-body">
              <div class="grid grid-cols-12 gap-x-5 gap-y-5">
                <div class="col-span-12 sm:col-span-6">
                  <label class="al-label" for="al-first-name">First Name <span style="color:#ef4444">*</span></label>
                  <input type="text" class="al-input" id="al-first-name" name="first_name" placeholder="John">
                  <div class="al-error" id="err-first-name"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="al-label" for="al-last-name">Last Name <span style="color:#ef4444">*</span></label>
                  <input type="text" class="al-input" id="al-last-name" name="last_name" placeholder="Doe">
                  <div class="al-error" id="err-last-name"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="al-label" for="al-email">Email <span style="color:#ef4444">*</span></label>
                  <div class="al-input-group">
                    <div class="al-addon"><i class="ri-mail-line"></i></div>
                    <input type="email" class="al-input" id="al-email" name="email" placeholder="john@example.com">
                  </div>
                  <div class="al-error" id="err-email"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="al-label" for="al-phone">Phone <span style="color:#ef4444">*</span></label>
                  <div class="al-input-group">
                    <div class="al-addon"><i class="ri-phone-line"></i></div>
                    <input type="text" class="al-input" id="al-phone" name="phone" placeholder="+1 (555) 123-4567">
                  </div>
                  <div class="al-error" id="err-phone"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Trip Details -->
          <div class="al-card" id="intro-lead-trip-details">
            <div class="al-card-header">
              <div class="flex items-center gap-3">
                <span class="al-step-num">2</span>
                <div class="al-card-icon bg-info/10 text-info"><i class="ri-road-map-line"></i></div>
                <div>
                  <div class="font-semibold text-sm" style="color:var(--al-text)">Trip Details</div>
                  <div style="font-size:11px;color:var(--al-muted)">Service, date, addresses, and passengers</div>
                </div>
              </div>
            </div>
            <div class="al-card-body">
              <div class="grid grid-cols-12 gap-x-5 gap-y-5">
                <div class="col-span-12 sm:col-span-6">
                  <label class="al-label" for="al-service-type">Service Type <span style="color:#ef4444">*</span></label>
                  <select name="service_type" id="al-service-type" class="al-input">
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
                      foreach ($serviceOptions as $opt) {
                        echo '<option value="'.htmlspecialchars($opt, ENT_QUOTES).'">'.htmlspecialchars($opt).'</option>';
                      }
                    ?>
                  </select>
                  <div class="al-error" id="err-service-type"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="al-label" for="al-event-date">Event Date <span style="color:#ef4444">*</span></label>
                  <input type="date" class="al-input" id="al-event-date" name="pickup_date">
                  <div class="al-error" id="err-event-date"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="al-label" for="al-passengers">Passengers <span style="color:#ef4444">*</span></label>
                  <div class="al-input-group">
                    <div class="al-addon"><i class="ri-group-line"></i></div>
                    <input type="number" class="al-input" id="al-passengers" name="passengers" min="1" placeholder="0">
                  </div>
                  <div class="al-error" id="err-passengers"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                  <label class="al-label" for="al-service-length">Service Length (Hours) <span style="color:#ef4444">*</span></label>
                  <div class="al-input-group-right al-input-group">
                    <input type="number" step="0.5" min="0.5" class="al-input" id="al-service-length" name="service_length" placeholder="0">
                    <div class="al-addon" style="font-size:11px;font-weight:600;">hrs</div>
                  </div>
                  <div class="al-error" id="err-service-length"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12">
                  <label class="al-label" for="al-pickup">Pickup Address <span style="color:#ef4444">*</span></label>
                  <div class="al-addr-wrap">
                    <div class="al-input-group">
                      <div class="al-addon"><i class="ri-map-pin-2-line" style="color:#22c55e"></i></div>
                      <input type="text" class="al-input" id="al-pickup" name="pickup" placeholder="Start typing an address..." autocomplete="off">
                    </div>
                    <div class="al-addr-list" id="al-pickup-list"></div>
                  </div>
                  <div class="al-error" id="err-pickup"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12">
                  <label class="al-label" for="al-dropoff">Dropoff Address <span style="color:#ef4444">*</span></label>
                  <div class="al-addr-wrap">
                    <div class="al-input-group">
                      <div class="al-addon"><i class="ri-map-pin-2-line" style="color:#ef4444"></i></div>
                      <input type="text" class="al-input" id="al-dropoff" name="destination" placeholder="Start typing an address..." autocomplete="off">
                    </div>
                    <div class="al-addr-list" id="al-dropoff-list"></div>
                  </div>
                  <div class="al-error" id="err-dropoff"><i class="ri-error-warning-line"></i><span></span></div>
                </div>
                <div class="col-span-12">
                  <label class="al-label" for="al-notes">Notes / Special Requests</label>
                  <textarea class="al-input" id="al-notes" name="notes" placeholder="Flight info, gate codes, child seats, special requests..."></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 3: Select Vehicle -->
          <div class="al-card" id="intro-lead-select-vehicle">
            <div class="al-card-header">
              <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                  <span class="al-step-num">3</span>
                  <div class="al-card-icon bg-success/10 text-success"><i class="ri-car-line"></i></div>
                  <div>
                    <div class="font-semibold text-sm" style="color:var(--al-text)">Select Vehicle <span style="color:#ef4444">*</span></div>
                    <div style="font-size:11px;color:var(--al-muted)">Choose the vehicle for this trip</div>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <div style="position:relative;">
                    <i class="ri-search-line" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:14px;color:var(--al-muted);pointer-events:none;"></i>
                    <input type="text" class="al-vehicle-search" id="al-vehicle-search" placeholder="Search vehicles...">
                  </div>
                </div>
              </div>
            </div>
            <div class="al-card-body">
              <input type="hidden" name="vehicle_id" id="al-vehicle-id" value="">
              <div class="al-error mb-3" id="err-vehicle"><i class="ri-error-warning-line"></i><span></span></div>
              <div id="al-vehicle-grid" class="al-vehicle-grid">
                <div class="al-skeleton" style="height:200px;"></div>
                <div class="al-skeleton" style="height:200px;"></div>
                <div class="al-skeleton" style="height:200px;"></div>
                <div class="al-skeleton" style="height:200px;"></div>
              </div>
              <div id="al-vehicle-empty" style="display:none;text-align:center;padding:40px 20px;color:var(--al-muted);">
                <i class="ri-car-line text-4xl mb-2" style="display:block;opacity:0.3;"></i>
                <div style="font-size:14px;font-weight:600;">No vehicles found</div>
              </div>
            </div>
          </div>

        </div>

        <!-- RIGHT COLUMN: Live Summary -->
        <div class="xl:col-span-4 col-span-12">
          <div class="al-card" style="position:sticky;top:80px;" id="intro-lead-live-summary">
            <div class="al-card-header">
              <div class="flex items-center gap-3">
                <div class="al-card-icon bg-primary/10 text-primary"><i class="ri-file-list-3-line"></i></div>
                <div class="font-semibold text-sm" style="color:var(--al-text)">Live Summary</div>
              </div>
            </div>
            <div class="al-card-body">
              <!-- Selected vehicle preview -->
              <div id="al-sum-vehicle" style="display:none;" class="mb-4">
                <div style="border-radius:12px;overflow:hidden;border:1px solid var(--al-border);">
                  <img id="al-sum-veh-img" src="" alt="" style="width:100%;aspect-ratio:16/9;object-fit:cover;display:none;background:var(--al-surface-2);">
                  <div style="padding:10px 14px;">
                    <div id="al-sum-veh-name" style="font-size:14px;font-weight:700;color:var(--al-text);"></div>
                    <div id="al-sum-veh-meta" style="font-size:11px;color:var(--al-muted);margin-top:2px;"></div>
                  </div>
                </div>
              </div>
              <div id="al-sum-no-vehicle" style="text-align:center;padding:16px 0;color:var(--al-muted);font-size:13px;">
                <i class="ri-car-line text-2xl mb-1" style="display:block;opacity:0.3;"></i>
                No vehicle selected
              </div>

              <div style="margin:12px 0;border-top:1px dashed var(--al-border);"></div>

              <div class="al-summary-row"><span class="al-summary-label">Name</span><span class="al-summary-value" id="sum-name">—</span></div>
              <div class="al-summary-row"><span class="al-summary-label">Phone</span><span class="al-summary-value" id="sum-phone">—</span></div>
              <div class="al-summary-row"><span class="al-summary-label">Email</span><span class="al-summary-value" id="sum-email">—</span></div>
              <div class="al-summary-row"><span class="al-summary-label">Service</span><span class="al-summary-value" id="sum-service">—</span></div>
              <div class="al-summary-row"><span class="al-summary-label">Event Date</span><span class="al-summary-value" id="sum-date">—</span></div>
              <div class="al-summary-row"><span class="al-summary-label">Passengers</span><span class="al-summary-value" id="sum-passengers">—</span></div>
              <div class="al-summary-row"><span class="al-summary-label">Pickup</span><span class="al-summary-value" id="sum-pickup">—</span></div>
              <div class="al-summary-row"><span class="al-summary-label">Dropoff</span><span class="al-summary-value" id="sum-dropoff">—</span></div>

              <div style="margin:12px 0;border-top:2px dashed var(--al-border);"></div>

              <div class="al-summary-row"><span class="al-summary-label">Rate</span><span class="al-summary-value" id="sum-rate">—</span></div>
              <div class="al-summary-row"><span class="al-summary-label">Hours</span><span class="al-summary-value" id="sum-hours">—</span></div>
              <div class="al-summary-row"><span class="al-summary-label">Quoted</span><span class="al-summary-value" id="sum-quoted">—</span></div>
              <div class="al-summary-row"><span class="al-summary-label">Fuel Surcharge</span><span class="al-summary-value" id="sum-fuel">—</span></div>
              <div class="al-summary-row"><span class="al-summary-label">Commission</span><span class="al-summary-value" id="sum-comm">—</span></div>

              <div style="margin:14px 0 10px;border-top:2px solid var(--al-border);"></div>
              <div class="flex items-center justify-between">
                <span class="al-summary-label" style="font-weight:700;text-transform:uppercase;font-size:11px;letter-spacing:.06em;">Estimated Total</span>
                <span class="al-summary-total" id="sum-total">$0.00</span>
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

  var allVehicles = [];
  var selectedVehicle = null;
  var userId = '<?php echo $_SESSION["user"]["id"] ?? ""; ?>';

  function toNum(v) { var n = parseFloat(String(v).replace(/,/g, '')); return Number.isFinite(n) ? n : 0; }
  function fmt(n) { return (Math.round((Number.isFinite(n) ? n : 0) * 100) / 100).toFixed(2); }

  /* ── Load Vehicles ──────────────────────────────── */
  function loadVehicles() {
    $.ajax({
      url: 'config/api.php',
      type: 'POST',
      data: { action: 'fetch_vehicles_ajax', user_id: userId },
      success: function () {},
      error: function () {}
    });

    $.post('https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint', {
      action: 'fetch_vehicles',
      user_id: userId,
      is_admin: "<?php echo $_SESSION['user']['admin'] == 1 ? '1' : '0'; ?>"
    }, function (data) {
      if (typeof data === 'string') { try { data = JSON.parse(data); } catch(e) { data = []; } }
      allVehicles = Array.isArray(data) ? data.filter(function (v) { return v.status !== 'Inactive'; }) : [];
      renderVehicles(allVehicles);
    }).fail(function () {
      $('#al-vehicle-grid').html('<div style="text-align:center;padding:30px;color:var(--al-muted);">Failed to load vehicles. Please refresh.</div>');
    });
  }

  function renderVehicles(vehicles) {
    var grid = $('#al-vehicle-grid');
    grid.empty();

    if (!vehicles.length) {
      grid.hide();
      $('#al-vehicle-empty').show();
      return;
    }
    $('#al-vehicle-empty').hide();
    grid.show();

    vehicles.forEach(function (v) {
      var isSelected = selectedVehicle && selectedVehicle.id === v.id;
      var rawImg = v.image_c || v.images_c || '';
      var imgSrc = rawImg ? String(rawImg).split(',')[0].trim() : '';
      var imgHtml = imgSrc
        ? '<img class="al-veh-img" src="' + imgSrc + '" alt="" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">' +
          '<div class="al-veh-img-placeholder" style="display:none;"><i class="ri-car-fill"></i></div>'
        : '<div class="al-veh-img-placeholder"><i class="ri-car-fill"></i></div>';

      var card = $(
        '<div class="al-vehicle-card' + (isSelected ? ' selected' : '') + '" data-vehicle-id="' + v.id + '">' +
          '<div class="al-veh-img-wrap">' +
            imgHtml +
            '<div class="al-veh-check"><i class="ri-check-line"></i></div>' +
          '</div>' +
          '<div class="al-veh-body">' +
            '<div class="al-veh-name" title="' + (v.name || '') + '">' + (v.name || 'Unnamed') + '</div>' +
            '<div class="al-veh-meta">' +
              '<span><i class="ri-user-line"></i> ' + (v.passenger || 0) + '</span>' +
              '<span><i class="ri-briefcase-line"></i> ' + (v.bags || 0) + '</span>' +
              (v.vehicle_cetagory ? '<span>' + v.vehicle_cetagory + '</span>' : '') +
            '</div>' +
            '<div class="al-veh-rate">$' + fmt(toNum(v.rate_c)) + '/hr</div>' +
          '</div>' +
        '</div>'
      );

      card.on('click', function () { selectVehicle(v); });
      grid.append(card);
    });
  }

  function selectVehicle(v) {
    selectedVehicle = v;
    $('#al-vehicle-id').val(v.id);
    $('.al-vehicle-card').removeClass('selected');
    $('.al-vehicle-card[data-vehicle-id="' + v.id + '"]').addClass('selected');
    $('#err-vehicle').removeClass('show');

    var img = $('#al-sum-veh-img');
    var rawV = v.image_c || v.images_c || '';
    var vImgSrc = rawV ? String(rawV).split(',')[0].trim() : '';
    if (vImgSrc) {
      img.attr('src', vImgSrc).show();
    } else {
      img.hide();
    }
    $('#al-sum-veh-name').text(v.name || 'Unnamed');
    $('#al-sum-veh-meta').html(
      '<i class="ri-user-line"></i> ' + (v.passenger || 0) + ' pax &nbsp;·&nbsp; ' +
      '<i class="ri-briefcase-line"></i> ' + (v.bags || 0) + ' bags &nbsp;·&nbsp; ' +
      '$' + fmt(toNum(v.rate_c)) + '/hr'
    );
    $('#al-sum-vehicle').show();
    $('#al-sum-no-vehicle').hide();

    updateSummary();
  }

  /* ── Vehicle Search ────────────────────────────── */
  $('#al-vehicle-search').on('input', function () {
    var q = $(this).val().toLowerCase().trim();
    if (!q) { renderVehicles(allVehicles); return; }
    var filtered = allVehicles.filter(function (v) {
      return (v.name || '').toLowerCase().indexOf(q) !== -1 ||
             (v.vehicle_cetagory || '').toLowerCase().indexOf(q) !== -1;
    });
    renderVehicles(filtered);
  });

  /* ── Live Summary ──────────────────────────────── */
  function updateSummary() {
    var fn = $('#al-first-name').val() || '';
    var ln = $('#al-last-name').val() || '';
    $('#sum-name').text((fn + ' ' + ln).trim() || '—');
    $('#sum-phone').text($('#al-phone').val() || '—');
    $('#sum-email').text($('#al-email').val() || '—');
    $('#sum-service').text($('#al-service-type').val() || '—');
    $('#sum-date').text($('#al-event-date').val() || '—');
    $('#sum-passengers').text($('#al-passengers').val() || '—');
    $('#sum-pickup').text($('#al-pickup').val() || '—').attr('title', $('#al-pickup').val() || '');
    $('#sum-dropoff').text($('#al-dropoff').val() || '—').attr('title', $('#al-dropoff').val() || '');

    var hours = toNum($('#al-service-length').val());
    var rate = selectedVehicle ? toNum(selectedVehicle.rate_c) : 0;
    var fuelPct = selectedVehicle ? toNum(selectedVehicle.fuel_c) : 0;
    var commPct = selectedVehicle ? toNum(selectedVehicle.driver_commission_c) : 0;

    var quoted = rate * hours;
    var fuel = quoted * fuelPct / 100;
    var comm = quoted * commPct / 100;
    var total = quoted + fuel + comm;

    $('#sum-rate').text(rate ? '$' + fmt(rate) + '/hr' : '—');
    $('#sum-hours').text(hours ? hours + ' hrs' : '—');
    $('#sum-quoted').text('$' + fmt(quoted));
    $('#sum-fuel').text('$' + fmt(fuel) + (fuelPct > 0 ? ' (' + fuelPct + '%)' : ''));
    $('#sum-comm').text('$' + fmt(comm) + (commPct > 0 ? ' (' + commPct + '%)' : ''));
    $('#sum-total').text('$' + fmt(total));
  }

  $('#addLeadForm').on('input change', 'input, select, textarea', updateSummary);

  /* ── Validation ────────────────────────────────── */
  function showErr(inputSel, errSel, msg) {
    $(inputSel).addClass('is-invalid');
    $(errSel).addClass('show').find('span').text(msg);
  }
  function clearErrs() {
    $('.al-input').removeClass('is-invalid');
    $('.al-error').removeClass('show');
  }

  function validate() {
    clearErrs();
    var ok = true;

    if (!$('#al-vehicle-id').val()) {
      $('#err-vehicle').addClass('show').find('span').text('Please select a vehicle.');
      ok = false;
    }

    var fn = $('#al-first-name').val().trim();
    if (!fn) { showErr('#al-first-name', '#err-first-name', 'First name is required.'); ok = false; }
    else if (fn.length < 2) { showErr('#al-first-name', '#err-first-name', 'Must be at least 2 characters.'); ok = false; }

    var ln = $('#al-last-name').val().trim();
    if (!ln) { showErr('#al-last-name', '#err-last-name', 'Last name is required.'); ok = false; }
    else if (ln.length < 2) { showErr('#al-last-name', '#err-last-name', 'Must be at least 2 characters.'); ok = false; }

    var email = $('#al-email').val().trim();
    if (!email) { showErr('#al-email', '#err-email', 'Email is required.'); ok = false; }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showErr('#al-email', '#err-email', 'Enter a valid email.'); ok = false; }

    var phone = $('#al-phone').val().trim();
    if (!phone) { showErr('#al-phone', '#err-phone', 'Phone number is required.'); ok = false; }
    else if (phone.length < 7) { showErr('#al-phone', '#err-phone', 'Enter a valid phone number.'); ok = false; }

    if (!$('#al-service-type').val()) { showErr('#al-service-type', '#err-service-type', 'Please select a service type.'); ok = false; }

    if (!$('#al-event-date').val()) { showErr('#al-event-date', '#err-event-date', 'Event date is required.'); ok = false; }

    var pass = $('#al-passengers').val();
    if (pass === '' || isNaN(parseInt(pass))) { showErr('#al-passengers', '#err-passengers', 'Passenger count is required.'); ok = false; }
    else if (parseInt(pass) < 1) { showErr('#al-passengers', '#err-passengers', 'Must be at least 1.'); ok = false; }

    var sl = $('#al-service-length').val();
    if (sl === '' || isNaN(parseFloat(sl))) { showErr('#al-service-length', '#err-service-length', 'Service length is required.'); ok = false; }
    else if (parseFloat(sl) <= 0) { showErr('#al-service-length', '#err-service-length', 'Must be greater than 0.'); ok = false; }

    var pickup = $('#al-pickup').val().trim();
    if (!pickup) { showErr('#al-pickup', '#err-pickup', 'Pickup address is required.'); ok = false; }

    var dropoff = $('#al-dropoff').val().trim();
    if (!dropoff) { showErr('#al-dropoff', '#err-dropoff', 'Dropoff address is required.'); ok = false; }

    if (!ok) {
      var first = document.querySelector('.al-error.show') || document.querySelector('.al-input.is-invalid');
      if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return ok;
  }

  /* ── Save ──────────────────────────────────────── */
  $('#al-save-btn').click(function () {
    if (!validate()) return;

    var btn = $(this);
    var orig = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm align-middle me-1"></span> Creating...').prop('disabled', true);

    var formData = $('#addLeadForm').serialize();
    formData += '&assigned_user_id=' + encodeURIComponent(userId);

    $.ajax({
      url: 'config/save_lead_endpoint.php',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function (response) {
        var data = response;
        if (typeof response === 'string') { try { data = JSON.parse(response); } catch(e) {} }
        if (data.success) {
          Swal.fire({ icon: 'success', title: 'Lead Created!', text: 'The new lead has been saved successfully.', showConfirmButton: false, timer: 1500 }).then(function () {
            if (data.id) {
              window.location.href = 'lead.php?id=' + data.id;
            } else {
              window.location.href = 'leads.php';
            }
          });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to create lead' });
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

  /* ── Sticky header shadow ──────────────────────── */
  var stickyHeader = document.getElementById('al-sticky-header');
  if (stickyHeader) {
    var scrollParent = stickyHeader.closest('.app-content') || window;
    (scrollParent === window ? window : scrollParent).addEventListener('scroll', function () {
      var scrollTop = scrollParent === window ? window.scrollY : scrollParent.scrollTop;
      stickyHeader.classList.toggle('scrolled', scrollTop > 10);
    });
  }

  /* ── Address Autocomplete (Nominatim / OSM) ───── */
  function initAddressAutocomplete(inputId, listId) {
    var $input = $('#' + inputId);
    var $list  = $('#' + listId);
    var debounceTimer = null;
    var activeIdx = -1;

    $input.on('input', function () {
      var q = $(this).val().trim();
      clearTimeout(debounceTimer);
      activeIdx = -1;

      if (q.length < 3) { $list.removeClass('show').empty(); return; }

      $list.html('<div class="al-addr-loading"><i class="ri-loader-4-line ri-spin"></i> Searching...</div>').addClass('show');

      debounceTimer = setTimeout(function () {
        $.ajax({
          url: 'https://nominatim.openstreetmap.org/search',
          data: { q: q, format: 'json', addressdetails: 1, limit: 6 },
          dataType: 'json',
          headers: { 'Accept-Language': 'en' },
          success: function (results) {
            $list.empty();
            if (!results || !results.length) {
              $list.html('<div class="al-addr-loading">No results found</div>');
              return;
            }
            results.forEach(function (r, i) {
              var addr = r.address || {};
              var main = [addr.house_number, addr.road].filter(Boolean).join(' ') || r.display_name.split(',')[0];
              var sub = r.display_name;
              var $item = $('<div class="al-addr-item" data-idx="' + i + '">' +
                '<i class="ri-map-pin-line"></i>' +
                '<div class="al-addr-item-text">' +
                  '<div class="al-addr-item-main">' + $('<span>').text(main).html() + '</div>' +
                  '<div class="al-addr-item-sub">' + $('<span>').text(sub).html() + '</div>' +
                '</div>' +
              '</div>');
              $item.on('click', function () {
                $input.val(r.display_name).trigger('input');
                $list.removeClass('show').empty();
                $input.removeClass('is-invalid');
                $('#err-' + inputId.replace('al-', '')).removeClass('show');
              });
              $list.append($item);
            });
          },
          error: function () {
            $list.html('<div class="al-addr-loading">Search failed. Try again.</div>');
          }
        });
      }, 350);
    });

    $input.on('keydown', function (e) {
      var $items = $list.find('.al-addr-item');
      if (!$items.length || !$list.hasClass('show')) return;

      if (e.key === 'ArrowDown') {
        e.preventDefault();
        activeIdx = Math.min(activeIdx + 1, $items.length - 1);
        $items.removeClass('active').eq(activeIdx).addClass('active');
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        activeIdx = Math.max(activeIdx - 1, 0);
        $items.removeClass('active').eq(activeIdx).addClass('active');
      } else if (e.key === 'Enter') {
        e.preventDefault();
        if (activeIdx >= 0) $items.eq(activeIdx).trigger('click');
      } else if (e.key === 'Escape') {
        $list.removeClass('show').empty();
      }
    });

    $(document).on('click', function (e) {
      if (!$(e.target).closest('#' + inputId + ', #' + listId).length) {
        $list.removeClass('show');
      }
    });

    $input.on('focus', function () {
      if ($list.children().length && $(this).val().trim().length >= 3) $list.addClass('show');
    });
  }

  initAddressAutocomplete('al-pickup', 'al-pickup-list');
  initAddressAutocomplete('al-dropoff', 'al-dropoff-list');

  /* ── Init ──────────────────────────────────────── */
  loadVehicles();
});
</script>
