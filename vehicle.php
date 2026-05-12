<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$vehicleId = $_GET['id'] ?? '';
$isEdit = !empty($vehicleId);
?>

<style>
  .vf-page .vf-label {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: rgba(15,23,42,0.55);
    margin-bottom: 6px;
    display: block;
  }
  .dark .vf-page .vf-label { color: rgba(255,255,255,0.52); }

  .vf-page .vf-input {
    height: 44px;
    border-radius: 12px;
    border: 1px solid rgba(15,23,42,0.14);
    background: rgba(15,23,42,0.02);
    padding: 0 14px;
    width: 100%;
    font-size: 14px;
    color: #0f172a;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
  }
  .dark .vf-page .vf-input {
    border-color: rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.04);
    color: rgba(255,255,255,0.92);
  }
  .vf-page .vf-input:focus {
    border-color: rgb(var(--primary-rgb));
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12);
  }
  .vf-page .vf-input.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239,68,68,0.10) !important;
  }
  .vf-page textarea.vf-input {
    height: auto;
    min-height: 120px;
    resize: vertical;
    padding: 12px 14px;
  }
  .vf-page select.vf-input {
    cursor: pointer;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding-right: 38px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M8 11.4 2.6 6h10.8L8 11.4z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
  }

  .vf-page .vf-input-group {
    position: relative;
    display: flex;
    align-items: stretch;
  }
  .vf-page .vf-input-group .vf-input { border-radius: 0 12px 12px 0; }
  .vf-page .vf-input-group .vf-addon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    min-width: 44px;
    font-size: 14px;
    font-weight: 700;
    color: rgba(15,23,42,0.45);
    background: rgba(15,23,42,0.04);
    border: 1px solid rgba(15,23,42,0.14);
    border-right: none;
    border-radius: 12px 0 0 12px;
  }
  .dark .vf-page .vf-input-group .vf-addon {
    color: rgba(255,255,255,0.45);
    background: rgba(255,255,255,0.06);
    border-color: rgba(255,255,255,0.12);
  }
  .vf-page .vf-input-group-right .vf-input { border-radius: 12px 0 0 12px; }
  .vf-page .vf-input-group-right .vf-addon {
    border: 1px solid rgba(15,23,42,0.14);
    border-left: none;
    border-radius: 0 12px 12px 0;
  }
  .dark .vf-page .vf-input-group-right .vf-addon { border-color: rgba(255,255,255,0.12); }

  .vf-page .vf-error {
    font-size: 11px;
    color: #ef4444;
    margin-top: 5px;
    display: none;
    align-items: center;
    gap: 4px;
  }
  .vf-page .vf-error.show { display: flex; }
  .vf-page .vf-hint {
    font-size: 11px;
    color: rgba(15,23,42,0.42);
    margin-top: 5px;
    font-style: italic;
  }
  .dark .vf-page .vf-hint { color: rgba(255,255,255,0.38); }

  .vf-page .vf-card {
    background: #fff;
    border: 1px solid rgba(15,23,42,0.10);
    border-radius: 16px;
    overflow: hidden;
    transition: box-shadow 0.2s;
  }
  .vf-page .vf-card:hover { box-shadow: 0 4px 24px rgba(15,23,42,0.06); }
  .dark .vf-page .vf-card {
    background: rgba(255,255,255,0.035);
    border-color: rgba(255,255,255,0.08);
  }
  .dark .vf-page .vf-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.25); }
  .vf-page .vf-card-header {
    background: rgba(15,23,42,0.025);
    border-bottom: 1px solid rgba(15,23,42,0.08);
    padding: 18px 24px;
  }
  .dark .vf-page .vf-card-header {
    background: rgba(255,255,255,0.025);
    border-bottom-color: rgba(255,255,255,0.08);
  }
  .vf-page .vf-card-body { padding: 24px; }

  .vf-page .vf-card-header .vf-card-icon {
    width: 32px; height: 32px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
  }

  .vf-page .vf-step-num {
    width: 22px; height: 22px;
    border-radius: 50%;
    background: rgb(var(--primary-rgb));
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .vf-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid rgba(15,23,42,0.06);
    font-size: 13px;
  }
  .dark .vf-summary-row { border-bottom-color: rgba(255,255,255,0.06); }
  .vf-summary-row:last-child { border-bottom: none; }
  .vf-summary-label { color: rgba(15,23,42,0.5); }
  .dark .vf-summary-label { color: rgba(255,255,255,0.45); }
  .vf-summary-value { font-weight: 600; color: #0f172a; }
  .dark .vf-summary-value { color: rgba(255,255,255,0.88); }

  .vf-upload-zone {
    position: relative;
    aspect-ratio: 16/9;
    border-radius: 14px;
    overflow: hidden;
    background: rgba(15,23,42,0.03);
    border: 2px dashed rgba(15,23,42,0.14);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
  }
  .dark .vf-upload-zone {
    background: rgba(255,255,255,0.03);
    border-color: rgba(255,255,255,0.10);
  }
  /* Vehicle image sidebar card: opaque surfaces in dark mode (avoids “see-through” look) */
  .dark .vf-page .vf-card-vehicle-image {
    background: #101520;
    border-color: rgba(255,255,255,0.12);
  }
  .dark .vf-page .vf-card-vehicle-image .vf-card-header {
    background: #181d23;
    border-bottom-color: rgba(255,255,255,0.10);
  }
  .dark .vf-page .vf-card-vehicle-image .vf-upload-zone {
    background: #252d38;
    border-color: rgba(255,255,255,0.14);
  }
  .dark .vf-page .vf-card-vehicle-image .vf-upload-zone:hover {
    border-color: rgb(var(--primary-rgb));
    background: #2c3644;
  }
  .vf-upload-zone:hover {
    border-color: rgb(var(--primary-rgb));
    background: rgba(var(--primary-rgb), 0.03);
  }
  .vf-upload-zone img {
    position: absolute;
    inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
  }
  .vf-upload-zone .vf-upload-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
  }
  .vf-upload-zone:hover .vf-upload-overlay { opacity: 1; }

  .vf-upload-progress {
    height: 3px;
    background: rgba(15,23,42,0.08);
    border-radius: 3px;
    overflow: hidden;
    margin-top: 8px;
    display: none;
  }
  .dark .vf-upload-progress { background: rgba(255,255,255,0.08); }
  .vf-upload-progress.show { display: block; }
  .vf-upload-progress-bar {
    height: 100%;
    background: rgb(var(--primary-rgb));
    border-radius: 3px;
    width: 0;
    transition: width 0.4s ease;
  }

  .vf-gallery-thumbs {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
    gap: 8px;
    margin-top: 12px;
  }
  .vf-gallery-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 10px;
    overflow: hidden;
    background: rgba(15,23,42,0.06);
    border: 1px solid rgba(15,23,42,0.08);
    cursor: pointer;
    transition: box-shadow 0.15s, border-color 0.15s;
  }
  .dark .vf-gallery-item {
    background: rgba(255,255,255,0.06);
    border-color: rgba(255,255,255,0.10);
  }
  .vf-gallery-item.is-previewing {
    border-color: rgb(var(--primary-rgb));
    box-shadow: 0 0 0 2px rgba(var(--primary-rgb), 0.45);
  }
  .dark .vf-gallery-item.is-previewing {
    box-shadow: 0 0 0 2px rgba(var(--primary-rgb), 0.55);
  }
  .vf-gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }
  .vf-gallery-remove {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 26px;
    height: 26px;
    border: none;
    border-radius: 8px;
    background: rgba(0,0,0,0.55);
    color: #fff;
    font-size: 16px;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    transition: background 0.15s;
  }
  .vf-gallery-remove:hover { background: rgba(220,38,38,0.9); }
  .vf-gallery-hint {
    font-size: 11px;
    color: var(--textmuted);
    margin-top: 8px;
    text-align: center;
  }
  .vf-upload-zone-disabled {
    cursor: not-allowed;
    opacity: 0.72;
  }
  .vf-upload-zone-disabled:hover {
    border-color: rgba(15,23,42,0.14);
    background: rgba(15,23,42,0.03);
  }
  .dark .vf-upload-zone-disabled:hover {
    border-color: rgba(255,255,255,0.10);
    background: #252d38;
  }
</style>
<div class="main-content app-content">
  <div class="container-fluid">

    <!-- Page Header -->
    <div class="vf-page flex items-start justify-between page-header-breadcrumb flex-wrap gap-3 mb-5">
      <div class="flex items-start gap-3">
        <a href="vehicles.php" class="ti-btn ti-btn-icon ti-btn-soft-secondary !rounded-full mt-0.5" aria-label="Back to Fleet">
          <i class="ri-arrow-left-line"></i>
        </a>
        <div>
          <div class="text-sm text-textmuted dark:text-textmuted/50 font-medium">Fleet</div>
          <h1 class="page-title font-semibold text-xl mb-0 text-defaulttextcolor dark:text-defaulttextcolor/90">
            <?php echo $isEdit ? 'Edit Vehicle' : 'Add New Vehicle'; ?>
          </h1>
          <p class="text-xs text-textmuted dark:text-textmuted/50 mt-1 mb-0">
            <?php echo $isEdit ? 'Update details for your vehicle.' : 'Fill in the details to register a new vehicle in your fleet.'; ?>
          </p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <a href="vehicles.php" class="ti-btn ti-btn-sm ti-btn-soft-secondary font-medium !rounded-xl">
          <i class="ri-arrow-left-line me-1"></i> Back to Fleet
        </a>
      </div>
    </div>

    <div class="vf-page grid grid-cols-12 gap-6 pb-12">

      <!-- ======== LEFT: Form ======== -->
      <div class="xl:col-span-8 col-span-12">
        <form id="vehicle-form" enctype="multipart/form-data" novalidate>
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($vehicleId); ?>">
          <input type="hidden" name="image_c" id="v-image-c">

          <!-- Card 1: Basic Information -->
          <div class="vf-card mb-6" id="intro-vehicle-basic">
            <div class="vf-card-header flex items-center gap-3">
              <span class="vf-card-icon bg-primary/10 text-primary"><i class="ri-car-line"></i></span>
              <div>
                <h5 class="font-semibold text-[15px] text-defaulttextcolor dark:text-defaulttextcolor/90 mb-0 flex items-center gap-2">
                  <span class="vf-step-num">1</span> Basic Information
                </h5>
                <p class="text-[11px] text-textmuted dark:text-textmuted/50 mt-0.5 mb-0">Vehicle identity and classification.</p>
              </div>
            </div>
            <div class="vf-card-body space-y-5">
              <!-- Vehicle Name -->
              <div>
                <label class="vf-label" for="v-name">Vehicle Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="v-name" class="vf-input" placeholder="e.g., Luxury Cadillac Escalade" maxlength="120">
                <div class="vf-error" id="err-name"><i class="ri-error-warning-line"></i> <span></span></div>
              </div>

              <!-- Category + Status -->
              <div class="grid sm:grid-cols-2 gap-5">
                <div>
                  <label class="vf-label" for="v-category">Category <span class="text-danger">*</span></label>
                  <select name="vehicle_cetagory" id="v-category" class="vf-input">
                    <option value="">Select category...</option>
                    <option value="Sedan">Sedan</option>
                    <option value="SUV">SUV</option>
                    <option value="Stretch Limo">Stretch Limo</option>
                    <option value="Stretch SUV Limo">Stretch SUV Limo</option>
                    <option value="Mini Bus">Mini Bus</option>
                    <option value="Motor Coach">Motor Coach</option>
                    <option value="Party Bus">Party Bus</option>
                    <option value="Limousine">Limousine</option>
                    <option value="Sprinter">Sprinter</option>
                    <option value="Van">Van</option>
                    <option value="Executive Bus">Executive Bus</option>
                    <option value="Coach">Coach</option>
                    <option value="Motorcoach">Motorcoach</option>
                  </select>
                  <div class="vf-error" id="err-category"><i class="ri-error-warning-line"></i> <span></span></div>
                </div>
                <div>
                  <label class="vf-label" for="v-status">Status</label>
                  <select name="status" id="v-status" class="vf-input">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Maintenance">Maintenance</option>
                  </select>
                </div>
              </div>

              <!-- Passengers + Bags -->
              <div class="grid sm:grid-cols-2 gap-5">
                <div>
                  <label class="vf-label" for="v-passenger">Passengers <span class="text-danger">*</span></label>
                  <div class="vf-input-group">
                    <span class="vf-addon"><i class="ri-user-line text-base"></i></span>
                    <input type="number" name="passenger" id="v-passenger" class="vf-input" placeholder="0" min="0" max="200">
                  </div>
                  <div class="vf-error" id="err-passenger"><i class="ri-error-warning-line"></i> <span></span></div>
                </div>
                <div>
                  <label class="vf-label" for="v-bags">Bags <span class="text-danger">*</span></label>
                  <div class="vf-input-group">
                    <span class="vf-addon"><i class="ri-briefcase-line text-base"></i></span>
                    <input type="number" name="bags" id="v-bags" class="vf-input" placeholder="0" min="0" max="200">
                  </div>
                  <div class="vf-error" id="err-bags"><i class="ri-error-warning-line"></i> <span></span></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2: Pricing -->
          <div class="vf-card mb-6" id="intro-vehicle-pricing">
            <div class="vf-card-header flex items-center gap-3">
              <span class="vf-card-icon bg-success/10 text-success"><i class="ri-money-dollar-circle-line"></i></span>
              <div>
                <h5 class="font-semibold text-[15px] text-defaulttextcolor dark:text-defaulttextcolor/90 mb-0 flex items-center gap-2">
                  <span class="vf-step-num">2</span> Pricing
                </h5>
                <p class="text-[11px] text-textmuted dark:text-textmuted/50 mt-0.5 mb-0">Set the hourly rate, fuel surcharge and driver commission.</p>
              </div>
            </div>
            <div class="vf-card-body">
              <div class="grid sm:grid-cols-3 gap-5">
                <!-- Hourly Rate -->
                <div>
                  <label class="vf-label" for="v-rate">Hourly Rate <span class="text-danger">*</span></label>
                  <div class="vf-input-group">
                    <span class="vf-addon">$</span>
                    <input type="number" name="rate_c" id="v-rate" class="vf-input" placeholder="0.00" min="0" step="0.01">
                  </div>
                  <div class="vf-error" id="err-rate"><i class="ri-error-warning-line"></i> <span></span></div>
                  <p class="vf-hint">Charged per hour of service.</p>
                </div>
                <!-- Fuel Surcharge -->
                <div>
                  <label class="vf-label" for="v-fuel">Fuel Surcharge <span class="text-danger">*</span></label>
                  <div class="vf-input-group vf-input-group-right">
                    <input type="number" name="fuel_c" id="v-fuel" class="vf-input" placeholder="0" min="0" max="100" step="0.01">
                    <span class="vf-addon">%</span>
                  </div>
                  <div class="vf-error" id="err-fuel"><i class="ri-error-warning-line"></i> <span></span></div>
                  <p class="vf-hint">Applied to the trip total.</p>
                </div>
                <!-- Driver Commission -->
                <div>
                  <label class="vf-label" for="v-commission">Driver Commission <span class="text-danger">*</span></label>
                  <div class="vf-input-group vf-input-group-right">
                    <input type="number" name="driver_commission_c" id="v-commission" class="vf-input" placeholder="0" min="0" max="100" step="0.01">
                    <span class="vf-addon">%</span>
                  </div>
                  <div class="vf-error" id="err-commission"><i class="ri-error-warning-line"></i> <span></span></div>
                  <p class="vf-hint">Share paid to the driver.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 3: Details & Features -->
          <div class="vf-card mb-6" id="intro-vehicle-details">
            <div class="vf-card-header flex items-center gap-3">
              <span class="vf-card-icon bg-warning/10 text-warning"><i class="ri-list-check-2"></i></span>
              <div>
                <h5 class="font-semibold text-[15px] text-defaulttextcolor dark:text-defaulttextcolor/90 mb-0 flex items-center gap-2">
                  <span class="vf-step-num">3</span> Details & Features
                </h5>
                <p class="text-[11px] text-textmuted dark:text-textmuted/50 mt-0.5 mb-0">Add amenities and a description for this vehicle.</p>
              </div>
            </div>
            <div class="vf-card-body space-y-5">
              <div>
                <label class="vf-label" for="v-facilities">Facilities / Features <span class="text-danger">*</span></label>
                <input type="text" name="facilities" id="v-facilities" class="vf-input" placeholder="WiFi, Drinks, Leather Seats, Entertainment System">
                <div class="vf-error" id="err-facilities"><i class="ri-error-warning-line"></i> <span></span></div>
                <p class="vf-hint">Separate different features with a comma.</p>
              </div>
              <div>
                <label class="vf-label" for="v-description">Description <span class="text-danger">*</span></label>
                <textarea name="description" id="v-description" class="vf-input" rows="5" placeholder="Provide a detailed description of the vehicle..." maxlength="2000"></textarea>
                <div class="vf-error" id="err-description"><i class="ri-error-warning-line"></i> <span></span></div>
                <div class="flex justify-between mt-1">
                  <p class="vf-hint mb-0">A good description helps customers.</p>
                  <span class="text-[11px] text-textmuted dark:text-textmuted/50" id="desc-counter">0 / 2000</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit -->
          <div class="flex items-center justify-between gap-3 flex-wrap" id="intro-vehicle-save">
            <p class="text-xs text-textmuted dark:text-textmuted/50 mb-0"><span class="text-danger">*</span> Required fields</p>
            <div class="flex items-center gap-3">
              <a href="vehicles.php" class="ti-btn ti-btn-soft-secondary !rounded-xl px-6">Cancel</a>
              <button type="submit" class="ti-btn bg-primary text-white !rounded-xl px-8 font-semibold shadow-md shadow-primary/20 hover:shadow-lg transition-all" id="save-btn">
                <i class="ri-save-line me-1"></i> <?php echo $isEdit ? 'Update Vehicle' : 'Save Vehicle'; ?>
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- ======== RIGHT: Sidebar ======== -->
      <div class="xl:col-span-4 col-span-12">

        <!-- Image Upload -->
        <div class="vf-card vf-card-vehicle-image mb-6 sticky top-4" id="intro-vehicle-image">
          <div class="vf-card-header text-center">
            <h5 class="font-semibold text-[15px] text-defaulttextcolor dark:text-defaulttextcolor/90 mb-0">Vehicle images</h5>
          </div>
          <div class="vf-card-body">
            <div class="vf-upload-zone" id="upload-zone" role="button" tabindex="0">
              <img id="image-preview" src="" class="hidden" onerror="this.classList.add('hidden'); document.getElementById('upload-placeholder').classList.remove('hidden')">
              <div id="upload-placeholder" class="text-center px-4">
                <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-3">
                  <i class="ri-image-add-line text-2xl text-primary"></i>
                </div>
                <p class="text-sm font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 mb-1">Click to upload</p>
                <p class="text-[11px] text-textmuted dark:text-textmuted/50">Up to 3 images · JPG, PNG or WEBP (max 2MB each).</p>
              </div>
              <div class="vf-upload-overlay">
                <span class="text-white text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                  <i class="ri-upload-2-line text-lg"></i> Add photos
                </span>
              </div>
            </div>
            <div class="vf-gallery-thumbs" id="vf-gallery-thumbs" aria-live="polite"></div>
            <p class="vf-gallery-hint mb-0" id="vf-gallery-count"></p>
            <div class="vf-upload-progress" id="upload-progress">
              <div class="vf-upload-progress-bar" id="upload-progress-bar"></div>
            </div>
            <div class="vf-error mt-2" id="err-image"><i class="ri-error-warning-line"></i> <span></span></div>
            <input type="file" id="image-upload" name="vehicle_image" class="hidden" accept="image/jpeg,image/png,image/webp" multiple>
            <button type="button" class="ti-btn bg-primary/10 text-primary w-full !rounded-xl font-bold py-2.5 transition-all hover:bg-primary hover:text-white mt-4" id="vf-choose-images-btn">
              <i class="ri-upload-cloud-line me-1"></i> Choose images
            </button>
          </div>
        </div>

        <!-- Live Summary -->
        <div class="vf-card mb-6" id="intro-vehicle-live-preview">
          <div class="vf-card-header">
            <h5 class="font-semibold text-[15px] text-defaulttextcolor dark:text-defaulttextcolor/90 mb-0 flex items-center gap-2">
              <i class="ri-eye-line text-primary"></i> Live Preview
            </h5>
          </div>
          <div class="vf-card-body" id="live-summary">
            <div class="vf-summary-row">
              <span class="vf-summary-label">Name</span>
              <span class="vf-summary-value" id="sum-name">—</span>
            </div>
            <div class="vf-summary-row">
              <span class="vf-summary-label">Category</span>
              <span class="vf-summary-value" id="sum-category">—</span>
            </div>
            <div class="vf-summary-row">
              <span class="vf-summary-label">Status</span>
              <span id="sum-status"></span>
            </div>
            <div class="vf-summary-row">
              <span class="vf-summary-label">Capacity</span>
              <span class="vf-summary-value" id="sum-capacity">0 pass · 0 bags</span>
            </div>
            <div class="vf-summary-row">
              <span class="vf-summary-label">Photos</span>
              <span class="vf-summary-value" id="sum-photos">—</span>
            </div>
            <div class="vf-summary-row">
              <span class="vf-summary-label">Hourly Rate</span>
              <span class="vf-summary-value text-primary" id="sum-rate">$0.00</span>
            </div>
            <div class="vf-summary-row">
              <span class="vf-summary-label">Fuel Surcharge</span>
              <span class="vf-summary-value" id="sum-fuel">0%</span>
            </div>
            <div class="vf-summary-row">
              <span class="vf-summary-label">Commission</span>
              <span class="vf-summary-value" id="sum-commission">0%</span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function () {

  /** URL currently shown in the large preview (synced to a thumbnail ring). */
  var vfMainPreviewUrl = null;
  var MAX_VEHICLE_IMAGES = 3;

  // ──────── Load existing data (edit mode) ────────
  <?php if ($isEdit): ?>
    loadVehicleData(<?php echo json_encode($vehicleId); ?>);
  <?php endif; ?>

  function getVehicleImageUrls() {
    return String($('#v-image-c').val() || '').split(',').map(function (s) { return s.trim(); }).filter(Boolean);
  }

  function setVehicleImageUrls(urls) {
    $('#v-image-c').val(urls.join(','));
    refreshVehicleImagesUI();
  }

  function refreshVehicleImagesUI() {
    const urls = getVehicleImageUrls();
    if (!urls.length) {
      vfMainPreviewUrl = null;
      $('#image-preview').attr('src', '').addClass('hidden');
      $('#upload-placeholder').removeClass('hidden');
    } else {
      if (!vfMainPreviewUrl || urls.indexOf(vfMainPreviewUrl) === -1) {
        vfMainPreviewUrl = urls[0];
      }
      $('#image-preview').attr('src', vfMainPreviewUrl).removeClass('hidden');
      $('#upload-placeholder').addClass('hidden');
    }
    $('#vf-gallery-count').text(
      urls.length
        ? (urls.length + '/' + MAX_VEHICLE_IMAGES + ' images · tap a thumbnail to preview · × removes')
        : ''
    );
    const $g = $('#vf-gallery-thumbs').empty();
    urls.forEach(function (url) {
      const $remove = $('<button type="button" class="vf-gallery-remove" title="Remove">&times;</button>');
      const $img = $('<img alt="">').attr('src', url);
      const $item = $('<div class="vf-gallery-item"/>').append($img, $remove);
      if (url === vfMainPreviewUrl) {
        $item.addClass('is-previewing');
      }
      $g.append($item);
    });
    const photoN = urls.length;
    $('#sum-photos').text(photoN ? String(photoN) : '—');
    updateVehicleImageLimitUI();
  }

  function updateVehicleImageLimitUI() {
    const n = getVehicleImageUrls().length;
    const atMax = n >= MAX_VEHICLE_IMAGES;
    $('#image-upload').prop('disabled', atMax);
    $('#vf-choose-images-btn').prop('disabled', atMax).toggleClass('opacity-50 pointer-events-none', atMax);
    $('#upload-zone').toggleClass('vf-upload-zone-disabled', atMax);
  }

  function openVehicleImagePicker(e) {
    if (e) {
      e.preventDefault();
    }
    if (getVehicleImageUrls().length >= MAX_VEHICLE_IMAGES) {
      $('#err-image').addClass('show').find('span').text('Maximum of 3 images. Remove one to add another.');
      return;
    }
    $('#err-image').removeClass('show');
    document.getElementById('image-upload').click();
  }

  $('#upload-zone, #vf-choose-images-btn').on('click', openVehicleImagePicker);

  updateVehicleImageLimitUI();

  $('#vf-gallery-thumbs').on('click', '.vf-gallery-item', function (e) {
    if ($(e.target).closest('.vf-gallery-remove').length) {
      return;
    }
    const url = $(this).find('img').attr('src');
    if (!url) {
      return;
    }
    vfMainPreviewUrl = url;
    $('#image-preview').attr('src', url).removeClass('hidden');
    $('#upload-placeholder').addClass('hidden');
    $('#vf-gallery-thumbs .vf-gallery-item').removeClass('is-previewing');
    $(this).addClass('is-previewing');
  });

  $('#vf-gallery-thumbs').on('click', '.vf-gallery-remove', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const idx = $(this).closest('.vf-gallery-item').index();
    const urls = getVehicleImageUrls();
    urls.splice(idx, 1);
    setVehicleImageUrls(urls);
  });

  // ──────── Live summary binding ────────
  function updateSummary() {
    const name = $('#v-name').val().trim();
    const cat  = $('#v-category').val();
    const status = $('#v-status').val();
    const pass = parseInt($('#v-passenger').val()) || 0;
    const bags = parseInt($('#v-bags').val()) || 0;
    const rate = parseFloat($('#v-rate').val()) || 0;
    const fuel = parseFloat($('#v-fuel').val()) || 0;
    const comm = parseFloat($('#v-commission').val()) || 0;

    $('#sum-name').text(name || '—');
    $('#sum-category').text(cat || '—');
    $('#sum-capacity').text(pass + ' pass · ' + bags + ' bags');
    $('#sum-rate').text('$' + rate.toFixed(2));
    $('#sum-fuel').text(fuel + '%');
    $('#sum-commission').text(comm + '%');

    const photoN = getVehicleImageUrls().length;
    $('#sum-photos').text(photoN ? String(photoN) : '—');

    const statusColors = { Active: 'bg-success/10 text-success', Inactive: 'bg-danger/10 text-danger', Maintenance: 'bg-warning/10 text-warning' };
    $('#sum-status').html('<span class="px-2 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider ' + (statusColors[status] || '') + '">' + (status || '—') + '</span>');
  }

  $('#v-name, #v-category, #v-status, #v-passenger, #v-bags, #v-rate, #v-fuel, #v-commission').on('input change', updateSummary);
  updateSummary();

  // Description counter
  $('#v-description').on('input', function () {
    $('#desc-counter').text(this.value.length + ' / 2000');
  });

  // ──────── Validation ────────
  function clearErrors() {
    $('.vf-error').removeClass('show').find('span').text('');
    $('.vf-input').removeClass('is-invalid');
  }

  function showError(fieldId, errorId, msg) {
    $(fieldId).addClass('is-invalid');
    $(errorId).addClass('show').find('span').text(msg);
  }

  function validate() {
    clearErrors();
    let valid = true;

    const name = $('#v-name').val().trim();
    if (!name) {
      showError('#v-name', '#err-name', 'Vehicle name is required.');
      valid = false;
    } else if (name.length < 2) {
      showError('#v-name', '#err-name', 'Name must be at least 2 characters.');
      valid = false;
    }

    const cat = $('#v-category').val();
    if (!cat) {
      showError('#v-category', '#err-category', 'Please select a category.');
      valid = false;
    }

    const pass = $('#v-passenger').val();
    if (pass === '' || isNaN(parseInt(pass))) {
      showError('#v-passenger', '#err-passenger', 'Passenger count is required.');
      valid = false;
    } else if (parseInt(pass) < 0) {
      showError('#v-passenger', '#err-passenger', 'Must be 0 or more.');
      valid = false;
    } else if (parseInt(pass) > 200) {
      showError('#v-passenger', '#err-passenger', 'Maximum 200 passengers.');
      valid = false;
    }

    const bags = $('#v-bags').val();
    if (bags === '' || isNaN(parseInt(bags))) {
      showError('#v-bags', '#err-bags', 'Bag count is required.');
      valid = false;
    } else if (parseInt(bags) < 0) {
      showError('#v-bags', '#err-bags', 'Must be 0 or more.');
      valid = false;
    } else if (parseInt(bags) > 200) {
      showError('#v-bags', '#err-bags', 'Maximum 200 bags.');
      valid = false;
    }

    const rate = $('#v-rate').val();
    if (!rate || parseFloat(rate) <= 0) {
      showError('#v-rate', '#err-rate', 'Please enter a valid hourly rate.');
      valid = false;
    } else if (parseFloat(rate) > 99999) {
      showError('#v-rate', '#err-rate', 'Rate seems too high. Max $99,999.');
      valid = false;
    }

    const fuel = $('#v-fuel').val();
    if (fuel === '' || isNaN(parseFloat(fuel))) {
      showError('#v-fuel', '#err-fuel', 'Fuel surcharge is required (use 0 if none).');
      valid = false;
    } else if (parseFloat(fuel) < 0 || parseFloat(fuel) > 100) {
      showError('#v-fuel', '#err-fuel', 'Must be between 0 and 100.');
      valid = false;
    }

    const comm = $('#v-commission').val();
    if (comm === '' || isNaN(parseFloat(comm))) {
      showError('#v-commission', '#err-commission', 'Driver commission is required (use 0 if none).');
      valid = false;
    } else if (parseFloat(comm) < 0 || parseFloat(comm) > 100) {
      showError('#v-commission', '#err-commission', 'Must be between 0 and 100.');
      valid = false;
    }

    const facilities = $('#v-facilities').val().trim();
    if (!facilities) {
      showError('#v-facilities', '#err-facilities', 'Please list at least one feature.');
      valid = false;
    }

    const desc = $('#v-description').val().trim();
    if (!desc) {
      showError('#v-description', '#err-description', 'Description is required.');
      valid = false;
    } else if (desc.length < 10) {
      showError('#v-description', '#err-description', 'Description must be at least 10 characters.');
      valid = false;
    }

    const imgCount = getVehicleImageUrls().length;
    if (imgCount > MAX_VEHICLE_IMAGES) {
      $('#err-image').addClass('show').find('span').text('More than 3 images are not allowed.');
      valid = false;
    }

    const hasImage = imgCount > 0;
    if (!hasImage) {
      $('#err-image').addClass('show').find('span').text('Please upload at least one vehicle image.');
      valid = false;
    }

    if (!valid) {
      const firstErr = document.querySelector('.vf-input.is-invalid') || document.querySelector('.vf-error.show');
      if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    return valid;
  }

  // Clear individual field errors on re-input
  $('#v-name').on('input', function () { if (this.value.trim()) { $(this).removeClass('is-invalid'); $('#err-name').removeClass('show'); } });
  $('#v-category').on('change', function () { if (this.value) { $(this).removeClass('is-invalid'); $('#err-category').removeClass('show'); } });
  $('#v-rate').on('input', function () { if (parseFloat(this.value) > 0) { $(this).removeClass('is-invalid'); $('#err-rate').removeClass('show'); } });
  $('#v-passenger').on('input', function () { $(this).removeClass('is-invalid'); $('#err-passenger').removeClass('show'); });
  $('#v-bags').on('input', function () { $(this).removeClass('is-invalid'); $('#err-bags').removeClass('show'); });
  $('#v-fuel').on('input', function () { $(this).removeClass('is-invalid'); $('#err-fuel').removeClass('show'); });
  $('#v-commission').on('input', function () { $(this).removeClass('is-invalid'); $('#err-commission').removeClass('show'); });
  $('#v-facilities').on('input', function () { if (this.value.trim()) { $(this).removeClass('is-invalid'); $('#err-facilities').removeClass('show'); } });
  $('#v-description').on('input', function () { if (this.value.trim().length >= 10) { $(this).removeClass('is-invalid'); $('#err-description').removeClass('show'); } });

  // ──────── Image upload (multiple, comma-separated URLs in image_c, max 3) ────────
  const MAX_FILE_SIZE = 2 * 1024 * 1024;
  const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

  $('#image-upload').on('change', function () {
    const input = this;
    let files = input.files ? Array.from(input.files) : [];
    if (!files.length) return;

    const currentCount = getVehicleImageUrls().length;
    if (currentCount >= MAX_VEHICLE_IMAGES) {
      $('#err-image').addClass('show').find('span').text('Maximum of 3 images. Remove one to add another.');
      input.value = '';
      return;
    }

    const slots = MAX_VEHICLE_IMAGES - currentCount;
    if (files.length > slots) {
      $('#err-image').addClass('show').find('span').text(
        'Only ' + slots + ' more image(s) allowed (3 max). Uploading the first ' + slots + ' selected file(s).'
      );
      files = files.slice(0, slots);
    } else {
      $('#err-image').removeClass('show');
    }

    for (let fi = 0; fi < files.length; fi++) {
      const file = files[fi];
      if (!ALLOWED_TYPES.includes(file.type)) {
        $('#err-image').addClass('show').find('span').text('Only JPG, PNG or WEBP files are allowed.');
        input.value = '';
        return;
      }
      if (file.size > MAX_FILE_SIZE) {
        $('#err-image').addClass('show').find('span').text('Each file must be under 2MB (yours: ' + (file.size / 1024 / 1024).toFixed(1) + 'MB).');
        input.value = '';
        return;
      }
    }

    const reader = new FileReader();
    reader.onload = function (e) {
      $('#image-preview').attr('src', e.target.result).removeClass('hidden');
      $('#upload-placeholder').addClass('hidden');
    };
    reader.readAsDataURL(files[0]);

    const total = files.length;
    let uploadedOk = true;

    function finishBatch() {
      $('#upload-progress-bar').css('width', '100%');
      setTimeout(function () {
        $('#upload-progress').removeClass('show');
        $('#upload-progress-bar').css('width', '0');
      }, 450);
      input.value = '';
      $('#err-image').removeClass('show');
      refreshVehicleImagesUI();
    }

    function uploadAt(index) {
      if (!uploadedOk) return;
      if (index >= total) {
        finishBatch();
        return;
      }

      if (getVehicleImageUrls().length >= MAX_VEHICLE_IMAGES) {
        finishBatch();
        return;
      }

      const formData = new FormData();
      formData.append('vehicle_image', files[index]);
      formData.append('action', 'upload_vehicle_image');

      const pct = Math.round(((index + 0.35) / total) * 100);
      $('#upload-progress').addClass('show');
      $('#upload-progress-bar').css('width', pct + '%');

      $.ajax({
        url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function () {
          const xhr = new window.XMLHttpRequest();
          xhr.upload.addEventListener('progress', function (e) {
            if (e.lengthComputable && total > 0) {
              const filePct = e.loaded / e.total;
              const overall = ((index + filePct) / total) * 100;
              $('#upload-progress-bar').css('width', Math.min(99, Math.round(overall)) + '%');
            }
          });
          return xhr;
        },
        success: function (resp) {
          var data;
          try {
            data = typeof resp === 'string' ? JSON.parse(resp) : resp;
          } catch (ex) {
            uploadedOk = false;
            $('#err-image').addClass('show').find('span').text('Invalid response from server.');
            $('#upload-progress').removeClass('show');
            $('#upload-progress-bar').css('width', '0');
            input.value = '';
            refreshVehicleImagesUI();
            return;
          }
          if (data.success && data.url) {
            const urls = getVehicleImageUrls();
            if (urls.indexOf(data.url) === -1 && urls.length < MAX_VEHICLE_IMAGES) {
              urls.push(data.url);
            }
            $('#v-image-c').val(urls.join(','));
            $('#err-image').removeClass('show');
          } else {
            uploadedOk = false;
            $('#err-image').addClass('show').find('span').text(data.message || 'Upload failed. Try again.');
            $('#upload-progress').removeClass('show');
            $('#upload-progress-bar').css('width', '0');
            input.value = '';
            refreshVehicleImagesUI();
          }
        },
        error: function () {
          uploadedOk = false;
          $('#upload-progress').removeClass('show');
          $('#upload-progress-bar').css('width', '0');
          input.value = '';
          $('#err-image').addClass('show').find('span').text('Network error during upload.');
          refreshVehicleImagesUI();
        },
        complete: function () {
          if (!uploadedOk) return;
          uploadAt(index + 1);
        }
      });
    }

    uploadAt(0);
  });

  // ──────── Form submit ────────
  $('#vehicle-form').on('submit', function (e) {
    e.preventDefault();
    if (!validate()) return;
    saveVehicle();
  });

  function saveVehicle() {
    const btn = $('#save-btn');
    btn.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin me-1"></i> Saving...');

    const formData = new FormData($('#vehicle-form')[0]);
    formData.append('action', 'save_vehicle');
    formData.append('user_id', '<?php echo $_SESSION['user']['id']; ?>');

    $.ajax({
      url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (resp) {
        const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: '<?php echo $isEdit ? "Updated!" : "Saved!"; ?>',
            text: 'Vehicle has been <?php echo $isEdit ? "updated" : "added to your fleet"; ?> successfully.',
            timer: 2000,
            showConfirmButton: false
          }).then(function () {
            window.location.href = 'vehicles.php';
          });
        } else {
          Swal.fire('Error', data.message || 'Something went wrong.', 'error');
          btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> <?php echo $isEdit ? "Update Vehicle" : "Save Vehicle"; ?>');
        }
      },
      error: function () {
        Swal.fire('Error', 'Could not reach the server. Please try again.', 'error');
        btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> <?php echo $isEdit ? "Update Vehicle" : "Save Vehicle"; ?>');
      }
    });
  }

  // ──────── Load vehicle data (edit mode) ────────
  function loadVehicleData(id) {
    $.ajax({
      url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
      type: 'POST',
      data: { action: 'get_vehicle', id: id },
      success: function (resp) {
        const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
        if (data.success) {
          const v = data.vehicle;
          $('#v-name').val(v.name);
          $('#v-category').val(v.vehicle_cetagory);
          $('#v-status').val(v.status || 'Active');
          $('#v-passenger').val(v.passenger);
          $('#v-bags').val(v.bags);
          $('#v-rate').val(v.rate_c);
          $('#v-fuel').val(v.fuel_percentage_c ?? v.fuel_c ?? '');
          $('#v-commission').val(v.driver_commission_c ?? v.commission_c ?? '');
          $('#v-facilities').val(v.facilities);
          $('#v-description').val(v.description);
          $('#desc-counter').text((v.description || '').length + ' / 2000');

          const rawImages = v.image_c || v.images_c || '';
          const capped = String(rawImages).split(',').map(function (s) { return s.trim(); }).filter(Boolean).slice(0, MAX_VEHICLE_IMAGES);
          $('#v-image-c').val(capped.join(','));
          refreshVehicleImagesUI();

          updateSummary();
        } else {
          Swal.fire('Error', 'Failed to load vehicle data.', 'error');
        }
      },
      error: function () {
        Swal.fire('Error', 'Could not reach the server.', 'error');
      }
    });
  }
});
</script>
