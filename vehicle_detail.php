<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$vehicleId = $_GET['id'] ?? '';
if (empty($vehicleId)) {
    header('Location: vehicles.php');
    exit;
}
?>

<style>
  .vd-card {
    background: #fff;
    border: 1px solid rgba(15,23,42,0.12);
    border-radius: 16px;
    overflow: hidden;
  }
  .dark .vd-card {
    background: rgba(255,255,255,0.04);
    border-color: rgba(255,255,255,0.10);
  }
  .vd-card-header {
    background: rgba(15,23,42,0.02);
    border-bottom: 1px solid rgba(15,23,42,0.12);
  }
  .dark .vd-card-header {
    background: rgba(255,255,255,0.03);
    border-bottom-color: rgba(255,255,255,0.10);
  }
  .vd-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid rgba(15,23,42,0.08);
  }
  .dark .vd-row {
    border-bottom-color: rgba(255,255,255,0.08);
  }
  .vd-row:last-child { border-bottom: none; }
  .vd-row-label {
    font-size: 12px;
    color: rgba(15,23,42,0.55);
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
  }
  .dark .vd-row-label { color: rgba(255,255,255,0.50); }
  .vd-row-value {
    font-size: 14px;
    color: #0f172a;
    font-weight: 500;
    text-align: right;
  }
  .dark .vd-row-value { color: rgba(255,255,255,0.90); }
  .vd-feature-tag {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 600;
    background: rgba(var(--primary-rgb), 0.08);
    color: rgb(var(--primary-rgb));
    margin: 3px 4px 3px 0;
  }
  .vd-img-wrap {
    aspect-ratio: 16/9;
    border-radius: 12px;
    overflow: hidden;
    background: rgba(15,23,42,0.04);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .dark .vd-img-wrap { background: rgba(255,255,255,0.05); }
  .vd-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .vd-skeleton {
    animation: vd-pulse 1.8s ease-in-out infinite;
    background: rgba(15,23,42,0.06);
    border-radius: 8px;
  }
  .dark .vd-skeleton { background: rgba(255,255,255,0.06); }
  @keyframes vd-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
  }
</style>

<div class="main-content app-content">
  <div class="container-fluid">

    <!-- Header skeleton (replaced after load) -->
    <div class="mb-4" id="vd-header">
      <div class="flex items-start justify-between gap-3 flex-wrap">
        <div class="flex items-start gap-3">
          <a href="vehicles.php" class="ti-btn ti-btn-icon ti-btn-soft-secondary !rounded-full mt-1" aria-label="Back to Fleet">
            <i class="ri-arrow-left-line"></i>
          </a>
          <div>
            <div class="text-sm text-textmuted dark:text-textmuted/50 font-medium">Fleet</div>
            <h1 class="page-title font-semibold text-xl mb-0 text-defaulttextcolor dark:text-defaulttextcolor/90" id="vd-title">
              <span class="vd-skeleton inline-block" style="width:220px;height:24px"></span>
            </h1>
            <div class="mt-1 flex items-center gap-2 flex-wrap" id="vd-badges"></div>
          </div>
        </div>
        <div class="btn-list flex flex-wrap items-center gap-2" id="vd-actions">
          <a href="vehicles.php" class="ti-btn ti-btn-sm ti-btn-soft-secondary font-medium">
            <i class="ri-arrow-left-line me-1"></i> Back to Fleet
          </a>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="grid grid-cols-12 gap-6 pb-12" id="vd-body">

      <!-- Left column -->
      <div class="col-span-12 xl:col-span-4">
        <!-- Vehicle Image -->
        <div class="vd-card mb-6 shadow-sm">
          <div class="box-body p-6">
            <div class="vd-img-wrap" id="vd-image-wrap">
              <div class="text-center" id="vd-image-placeholder">
                <i class="ri-car-fill text-4xl text-textmuted/30 mb-1 block"></i>
                <span class="text-xs text-textmuted/40 uppercase tracking-widest font-semibold">No Image</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Basic Information -->
        <div class="vd-card mb-6 shadow-sm">
          <div class="vd-card-header px-6 py-4">
            <h5 class="font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 flex items-center gap-2 mb-0">
              <i class="ri-information-line text-primary"></i> Basic Information
            </h5>
          </div>
          <div class="box-body p-6" id="vd-basic-info">
            <div class="space-y-1">
              <div class="vd-skeleton" style="height:18px;width:100%;margin-bottom:12px"></div>
              <div class="vd-skeleton" style="height:18px;width:80%;margin-bottom:12px"></div>
              <div class="vd-skeleton" style="height:18px;width:60%"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Middle column -->
      <div class="col-span-12 xl:col-span-4">
        <!-- Pricing -->
        <div class="vd-card mb-6 shadow-sm">
          <div class="vd-card-header px-6 py-4">
            <h5 class="font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 flex items-center gap-2 mb-0">
              <i class="ri-money-dollar-circle-line text-primary"></i> Pricing
            </h5>
          </div>
          <div class="box-body p-6" id="vd-pricing">
            <div class="space-y-1">
              <div class="vd-skeleton" style="height:54px;width:100%;margin-bottom:16px;border-radius:12px"></div>
              <div class="vd-skeleton" style="height:18px;width:100%;margin-bottom:12px"></div>
              <div class="vd-skeleton" style="height:18px;width:70%"></div>
            </div>
          </div>
        </div>

        <!-- Capacity -->
        <div class="vd-card mb-6 shadow-sm">
          <div class="vd-card-header px-6 py-4">
            <h5 class="font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 flex items-center gap-2 mb-0">
              <i class="ri-group-line text-primary"></i> Capacity
            </h5>
          </div>
          <div class="box-body p-6" id="vd-capacity">
            <div class="vd-skeleton" style="height:18px;width:100%;margin-bottom:12px"></div>
            <div class="vd-skeleton" style="height:18px;width:60%"></div>
          </div>
        </div>
      </div>

      <!-- Right column -->
      <div class="col-span-12 xl:col-span-4">
        <!-- Features -->
        <div class="vd-card mb-6 shadow-sm">
          <div class="vd-card-header px-6 py-4">
            <h5 class="font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 flex items-center gap-2 mb-0">
              <i class="ri-list-check-2 text-primary"></i> Features
            </h5>
          </div>
          <div class="box-body p-6" id="vd-features">
            <div class="vd-skeleton" style="height:18px;width:100%;margin-bottom:12px"></div>
            <div class="vd-skeleton" style="height:18px;width:50%"></div>
          </div>
        </div>

        <!-- Description -->
        <div class="vd-card mb-6 shadow-sm">
          <div class="vd-card-header px-6 py-4">
            <h5 class="font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 flex items-center gap-2 mb-0">
              <i class="ri-file-text-line text-primary"></i> Description
            </h5>
          </div>
          <div class="box-body p-6" id="vd-description">
            <div class="vd-skeleton" style="height:18px;width:100%;margin-bottom:12px"></div>
            <div class="vd-skeleton" style="height:18px;width:90%;margin-bottom:12px"></div>
            <div class="vd-skeleton" style="height:18px;width:70%"></div>
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
  const vehicleId = <?php echo json_encode($vehicleId); ?>;

  function esc(val) {
    if (!val) return '';
    const d = document.createElement('div');
    d.textContent = val;
    return d.innerHTML;
  }

  function money(val) {
    const n = parseFloat(val) || 0;
    return '$' + n.toFixed(2);
  }

  $.ajax({
    url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
    type: 'POST',
    data: { action: 'get_vehicle', id: vehicleId },
    success: function (resp) {
      const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
      if (!data.success) {
        Swal.fire('Error', 'Failed to load vehicle details.', 'error').then(function () {
          window.location.href = 'vehicles.php';
        });
        return;
      }
      renderVehicleDetail(data.vehicle);
    },
    error: function () {
      Swal.fire('Error', 'Could not reach the server.', 'error').then(function () {
        window.location.href = 'vehicles.php';
      });
    }
  });

  function renderVehicleDetail(v) {
    const name     = v.name || 'Untitled Vehicle';
    const category = (v.vehicle_cetagory || 'General').replace(/_/g, ' ');
    const status   = v.status || 'Active';
    const rate     = parseFloat(v.rate_c) || 0;
    const fuel     = parseFloat(v.fuel_percentage_c ?? v.fuel_c ?? 0) || 0;
    const comm     = parseFloat(v.driver_commission_c ?? v.commission_c ?? 0) || 0;
    const passengers = v.passenger || 0;
    const bags     = v.bags || 0;
    const image    = v.image_c || v.images_c || '';
    const facilities = v.facilities || '';
    const desc     = v.description || '';
    const dateAdded = v.date_entered
      ? new Date(v.date_entered).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
      : '---';

    const statusClasses = {
      'Active': 'bg-success/10 text-success',
      'Inactive': 'bg-danger/10 text-danger',
      'Maintenance': 'bg-warning/10 text-warning'
    };
    const statusClass = statusClasses[status] || 'bg-secondary/10 text-secondary';

    // Header
    $('#vd-title').html(esc(name));
    $('#vd-badges').html(
      '<span class="badge ' + statusClass + '">' + esc(status) + '</span>' +
      '<span class="badge bg-secondary/10 text-secondary">' + esc(category) + '</span>' +
      '<span class="text-xs text-textmuted dark:text-textmuted/50"><i class="ri-calendar-line me-1"></i>Added ' + esc(dateAdded) + '</span>'
    );

    $('#vd-actions').html(
      <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Vehicles', 'update') == 1): ?>
      '<a href="vehicle.php?id=' + encodeURIComponent(v.id) + '" class="ti-btn bg-primary text-white ti-btn-sm btn-wave">' +
        '<i class="ri-edit-line me-1 align-middle"></i>Edit Vehicle' +
      '</a>' +
      <?php endif; ?>
      
      '<a href="vehicles.php" class="ti-btn ti-btn-sm ti-btn-soft-secondary font-medium">' +
        '<i class="ri-arrow-left-line me-1"></i> Back to Fleet' +
      '</a>'
    );

    // Image
    if (image) {
      $('#vd-image-wrap').html(
        '<img src="' + esc(image) + '" alt="' + esc(name) + '" onerror="this.parentElement.innerHTML=document.getElementById(\'vd-image-placeholder\')?.outerHTML || \'\';">'
      );
    }

    // Basic Info
    $('#vd-basic-info').html(
      '<div class="vd-row">' +
        '<span class="vd-row-label"><i class="ri-car-line"></i> Name</span>' +
        '<span class="vd-row-value">' + esc(name) + '</span>' +
      '</div>' +
      '<div class="vd-row">' +
        '<span class="vd-row-label"><i class="ri-price-tag-3-line"></i> Category</span>' +
        '<span class="vd-row-value">' + esc(category) + '</span>' +
      '</div>' +
      '<div class="vd-row">' +
        '<span class="vd-row-label"><i class="ri-flag-line"></i> Status</span>' +
        '<span class="vd-row-value"><span class="px-2 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider ' + statusClass + '">' + esc(status) + '</span></span>' +
      '</div>' +
      '<div class="vd-row">' +
        '<span class="vd-row-label"><i class="ri-calendar-line"></i> Date Added</span>' +
        '<span class="vd-row-value">' + esc(dateAdded) + '</span>' +
      '</div>' +
      '<div class="vd-row">' +
        '<span class="vd-row-label"><i class="ri-hashtag"></i> ID</span>' +
        '<span class="vd-row-value text-xs text-textmuted dark:text-textmuted/50 font-mono">' + esc(v.id) + '</span>' +
      '</div>'
    );

    // Pricing
    $('#vd-pricing').html(
      '<div class="rounded-xl border border-defaultborder dark:border-defaultborder/10 bg-primary/5 dark:bg-primary/10 p-4 mb-4">' +
        '<div class="text-xs text-textmuted dark:text-textmuted/50">Hourly Rate</div>' +
        '<div class="text-2xl font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 mt-1">' + money(rate) + '<span class="text-sm font-normal text-textmuted dark:text-textmuted/50 ms-1">/ hour</span></div>' +
      '</div>' +
      '<div class="rounded-xl border border-defaultborder dark:border-defaultborder/10 divide-y divide-defaultborder dark:divide-defaultborder/10">' +
        '<div class="px-4 py-3 flex items-center justify-between gap-3">' +
          '<span class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 font-medium flex items-center gap-2"><i class="ri-gas-station-line text-warning"></i> Fuel Surcharge</span>' +
          '<span class="text-sm font-semibold ' + (fuel > 0 ? 'text-defaulttextcolor dark:text-defaulttextcolor/90' : 'text-textmuted dark:text-textmuted/50') + '">' + (fuel > 0 ? fuel + '%' : '—') + '</span>' +
        '</div>' +
        '<div class="px-4 py-3 flex items-center justify-between gap-3">' +
          '<span class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 font-medium flex items-center gap-2"><i class="ri-steering-2-line text-secondary"></i> Driver Commission</span>' +
          '<span class="text-sm font-semibold ' + (comm > 0 ? 'text-defaulttextcolor dark:text-defaulttextcolor/90' : 'text-textmuted dark:text-textmuted/50') + '">' + (comm > 0 ? comm + '%' : '—') + '</span>' +
        '</div>' +
      '</div>'
    );

    // Capacity
    $('#vd-capacity').html(
      '<div class="grid grid-cols-2 gap-4">' +
        '<div class="rounded-xl border border-defaultborder dark:border-defaultborder/10 p-4 text-center">' +
          '<i class="ri-user-line text-2xl text-primary mb-1 block"></i>' +
          '<div class="text-2xl font-bold text-defaulttextcolor dark:text-defaulttextcolor/90">' + parseInt(passengers) + '</div>' +
          '<div class="text-xs text-textmuted dark:text-textmuted/50 font-medium uppercase tracking-wider">Passengers</div>' +
        '</div>' +
        '<div class="rounded-xl border border-defaultborder dark:border-defaultborder/10 p-4 text-center">' +
          '<i class="ri-briefcase-line text-2xl text-primary mb-1 block"></i>' +
          '<div class="text-2xl font-bold text-defaulttextcolor dark:text-defaulttextcolor/90">' + parseInt(bags) + '</div>' +
          '<div class="text-xs text-textmuted dark:text-textmuted/50 font-medium uppercase tracking-wider">Bags</div>' +
        '</div>' +
      '</div>'
    );

    // Features
    if (facilities.trim()) {
      const tags = facilities.split(',').map(function (f) {
        f = f.trim();
        return f ? '<span class="vd-feature-tag">' + esc(f) + '</span>' : '';
      }).join('');
      $('#vd-features').html('<div class="flex flex-wrap">' + tags + '</div>');
    } else {
      $('#vd-features').html('<p class="text-sm text-textmuted dark:text-textmuted/50 italic">No features listed.</p>');
    }

    // Description
    if (desc.trim()) {
      $('#vd-description').html(
        '<p class="text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 whitespace-pre-line leading-relaxed">' + esc(desc) + '</p>'
      );
    } else {
      $('#vd-description').html('<p class="text-sm text-textmuted dark:text-textmuted/50 italic">No description provided.</p>');
    }
  }
});
</script>
