<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<style>
  .fleet-page {
    --fl-surface: #ffffff;
    --fl-surface-2: #f8fafc;
    --fl-border: rgba(15,23,42,0.08);
    --fl-text: #0f172a;
    --fl-muted: rgba(15,23,42,0.55);
  }
  .dark .fleet-page {
    --fl-surface: rgba(255,255,255,0.035);
    --fl-surface-2: rgba(255,255,255,0.05);
    --fl-border: rgba(255,255,255,0.08);
    --fl-text: rgba(255,255,255,0.92);
    --fl-muted: rgba(255,255,255,0.50);
  }

  .fl-stat-card {
    background: var(--fl-surface);
    border: 1px solid var(--fl-border);
    border-radius: 16px;
    padding: 20px 24px;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .fl-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(15,23,42,0.08);
  }
  .dark .fl-stat-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.3);
  }
  .fl-stat-card .fl-stat-glow {
    position: absolute;
    top: -20px;
    right: -20px;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    opacity: 0.08;
    pointer-events: none;
  }
  .fl-stat-card .fl-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }
  .fl-stat-card .fl-stat-num {
    font-size: 28px;
    font-weight: 800;
    line-height: 1;
    color: var(--fl-text);
    letter-spacing: -0.02em;
  }
  .fl-stat-card .fl-stat-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--fl-muted);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-top: 4px;
  }

  .fl-table-card {
    background: var(--fl-surface);
    border: 1px solid var(--fl-border);
    border-radius: 16px;
    overflow: hidden;
  }
  .fl-table-toolbar {
    padding: 16px 24px;
    border-bottom: 1px solid var(--fl-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
  }
  .fl-search {
    height: 38px;
    border-radius: 10px;
    border: 1px solid var(--fl-border);
    background: var(--fl-surface-2);
    color: var(--fl-text);
    padding: 0 12px 0 36px!important;
    font-size: 13px;
    outline: none;
    width: min(320px, 100%);
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .fl-search:focus {
    border-color: rgb(var(--primary-rgb));
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12);
  }
  .fl-search-wrap {
    position: relative;
  }
  .fl-search-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 15px;
    color: var(--fl-muted);
    pointer-events: none;
  }

  .fl-table-card table thead th {
    background: var(--fl-surface-2);
    color: var(--fl-muted);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 12px 20px;
    border-bottom: 1px solid var(--fl-border);
    white-space: nowrap;
  }
  .fl-table-card table tbody td {
    padding: 14px 20px;
    vertical-align: middle;
    border-bottom: 1px solid var(--fl-border);
    white-space: nowrap;
  }
  .fl-table-card table tbody tr {
    transition: background 0.15s;
    cursor: pointer;
  }
  .fl-table-card table tbody tr:hover td {
    background: rgba(var(--primary-rgb), 0.03);
  }
  .dark .fl-table-card table tbody tr:hover td {
    background: rgba(255,255,255,0.03);
  }
  .fl-table-card table tbody tr:last-child td {
    border-bottom: none;
  }

  .fl-vehicle-thumb {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .fl-vehicle-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .fl-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }
  .fl-badge::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
  }
  .fl-badge-active { background: rgba(34,197,94,0.10); color: #16a34a; }
  .fl-badge-active::before { background: #16a34a; }
  .fl-badge-inactive { background: rgba(239,68,68,0.10); color: #dc2626; }
  .fl-badge-inactive::before { background: #dc2626; }
  .fl-badge-maintenance { background: rgba(245,158,11,0.10); color: #d97706; }
  .fl-badge-maintenance::before { background: #d97706; }

  .fl-action-btn {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: 1px solid var(--fl-border);
    background: transparent;
    color: var(--fl-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.15s;
  }
  .fl-action-btn:hover {
    background: rgba(var(--primary-rgb), 0.08);
    color: rgb(var(--primary-rgb));
    border-color: rgba(var(--primary-rgb), 0.20);
  }
  .fl-action-btn.fl-action-danger:hover {
    background: rgba(239,68,68,0.08);
    color: #dc2626;
    border-color: rgba(239,68,68,0.20);
  }

  .fl-category-pill {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    background: rgba(var(--primary-rgb), 0.08);
    color: rgb(var(--primary-rgb));
  }

  .fl-rate-block {
    display: flex;
    align-items: baseline;
    gap: 2px;
  }
  .fl-rate-block .fl-rate-val {
    font-size: 15px;
    font-weight: 800;
    color: var(--fl-text);
  }
  .fl-rate-block .fl-rate-unit {
    font-size: 10px;
    font-weight: 600;
    color: var(--fl-muted);
  }

  .fl-filter-btn {
    height: 38px;
    border-radius: 10px;
    border: 1px solid var(--fl-border);
    background: transparent;
    color: var(--fl-muted);
    padding: 0 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .fl-filter-btn:hover, .fl-filter-btn.active {
    background: rgba(var(--primary-rgb), 0.06);
    color: rgb(var(--primary-rgb));
    border-color: rgba(var(--primary-rgb), 0.20);
  }

  @keyframes fl-fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .fl-animate-row {
    animation: fl-fadeIn 0.3s ease forwards;
  }
</style>

<div class="main-content app-content">
  <div class="container-fluid fleet-page">

    <!-- Page Header -->
    <div class="flex items-start justify-between flex-wrap gap-3 mb-6">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="ri-steering-2-line text-primary text-base"></i>
          </div>
          <h1 class="page-title font-bold text-xl mb-0 text-defaulttextcolor dark:text-defaulttextcolor/90">Fleet Management</h1>
        </div>
        <p class="text-xs text-textmuted dark:text-textmuted/50 mt-1 mb-0 ms-10">Manage your vehicles, track status, and keep your fleet running.</p>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" class="fl-filter-btn" onclick="loadVehicles()" title="Refresh">
          <i class="ri-refresh-line text-sm"></i> Refresh
        </button>
        <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Vehicles', 'create') == 1): ?>
        <a href="vehicle.php" class="ti-btn ti-btn-sm bg-primary text-white font-semibold shadow-sm hover:shadow-md transition-all !rounded-xl px-4" id="add-vehicle-btn">
          <i class="ri-add-line me-1 text-base"></i> Add Vehicle
        </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Fleet Stats -->
    <div class="grid xl:grid-cols-4 lg:grid-cols-2 md:grid-cols-2 grid-cols-1 gap-4 mb-6" id="intro-fleet-stats">
      <div class="fl-stat-card">
        <div class="fl-stat-glow" style="background: rgb(var(--primary-rgb));"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="fl-stat-num" id="stat-total">0</div>
            <div class="fl-stat-label">Total Vehicles</div>
          </div>
          <div class="fl-stat-icon bg-primary/10 text-primary"><i class="ri-car-line"></i></div>
        </div>
      </div>
      <div class="fl-stat-card">
        <div class="fl-stat-glow" style="background: #22c55e;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="fl-stat-num" id="stat-active">0</div>
            <div class="fl-stat-label">Active</div>
          </div>
          <div class="fl-stat-icon bg-success/10 text-success"><i class="ri-checkbox-circle-line"></i></div>
        </div>
      </div>
      <div class="fl-stat-card">
        <div class="fl-stat-glow" style="background: #f59e0b;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="fl-stat-num" id="stat-maintenance">0</div>
            <div class="fl-stat-label">Maintenance</div>
          </div>
          <div class="fl-stat-icon bg-warning/10 text-warning"><i class="ri-tools-line"></i></div>
        </div>
      </div>
      <div class="fl-stat-card">
        <div class="fl-stat-glow" style="background: #ef4444;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="fl-stat-num" id="stat-inactive">0</div>
            <div class="fl-stat-label">Inactive</div>
          </div>
          <div class="fl-stat-icon bg-danger/10 text-danger"><i class="ri-close-circle-line"></i></div>
        </div>
      </div>
    </div>

    <!-- Vehicle Table -->
    <div class="fl-table-card mb-6">
      <div class="fl-table-toolbar">
        <div class="fl-search-wrap">
          <i class="ri-search-line"></i>
          <input type="text" class="fl-search" id="fleet-search" placeholder="Search vehicles...">
        </div>
        <div class="flex items-center gap-2">
          <button class="fl-filter-btn active" data-filter="all" onclick="filterFleet('all', this)">All</button>
          <button class="fl-filter-btn" data-filter="Active" onclick="filterFleet('Active', this)"><span class="w-1.5 h-1.5 rounded-full bg-success inline-block"></span> Active</button>
          <button class="fl-filter-btn" data-filter="Maintenance" onclick="filterFleet('Maintenance', this)"><span class="w-1.5 h-1.5 rounded-full bg-warning inline-block"></span> Maintenance</button>
          <button class="fl-filter-btn" data-filter="Inactive" onclick="filterFleet('Inactive', this)"><span class="w-1.5 h-1.5 rounded-full bg-danger inline-block"></span> Inactive</button>
        </div>
      </div>
      <div class="overflow-auto" id="intro-fleet-table">
        <table class="w-full text-nowrap">
          <thead>
            <tr>
              <th class="text-start">Vehicle</th>
              <th class="text-start">Category</th>
              <th class="text-start">Capacity</th>
              <th class="text-start">Rate</th>
              <th class="text-start">Status</th>
              <th class="text-start">Added</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="vehicles-table-body">
            <tr id="table-loading">
              <td colspan="7" class="px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                    <i class="ri-loader-4-line text-2xl animate-spin text-primary"></i>
                  </div>
                  <span class="text-sm font-medium text-textmuted dark:text-textmuted/50">Loading your fleet...</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let allVehicles = [];
    let currentFilter = 'all';

    $(document).ready(function () {
        loadVehicles();

        $('#fleet-search').on('input', function () {
            applyFilterAndSearch();
        });
    });

    function loadVehicles() {
        updateFleetStats([]);
        $('#vehicles-table-body').html(`
            <tr id="table-loading">
              <td colspan="7" class="px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                    <i class="ri-loader-4-line text-2xl animate-spin text-primary"></i>
                  </div>
                  <span class="text-sm font-medium text-textmuted dark:text-textmuted/50">Loading your fleet...</span>
                </div>
              </td>
            </tr>
        `);

        $.ajax({
            url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
            type: 'POST',
            data: { action: 'fetch_vehicles', user_id: '<?php echo $_SESSION['user']['id']; ?>' , is_admin: "<?php echo $_SESSION['user']['admin'] == 1 ? '1' : '0'; ?>" },
            success: function (response) {
                try {
                    allVehicles = typeof response === 'string' ? JSON.parse(response) : response;
                    if (!Array.isArray(allVehicles)) allVehicles = [];
                    updateFleetStats(allVehicles);
                    applyFilterAndSearch();
                } catch (e) {
                    allVehicles = [];
                    updateFleetStats([]);
                    $('#vehicles-table-body').html(`
                        <tr>
                          <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                              <div class="w-12 h-12 rounded-full bg-danger/10 flex items-center justify-center">
                                <i class="ri-error-warning-line text-2xl text-danger"></i>
                              </div>
                              <span class="text-sm font-semibold text-danger">Failed to load vehicles.</span>
                              <button onclick="loadVehicles()" class="fl-filter-btn mt-1"><i class="ri-refresh-line me-1"></i> Retry</button>
                            </div>
                          </td>
                        </tr>
                    `);
                }
            }
        });
    }

    function filterFleet(status, btnEl) {
        currentFilter = status;
        $('.fl-filter-btn[data-filter]').removeClass('active');
        $(btnEl).addClass('active');
        applyFilterAndSearch();
    }

    function applyFilterAndSearch() {
        const query = ($('#fleet-search').val() || '').toLowerCase().trim();
        let filtered = allVehicles;

        if (currentFilter !== 'all') {
            filtered = filtered.filter(v => (v.status || 'Active') === currentFilter);
        }
        if (query) {
            filtered = filtered.filter(v => {
                const haystack = [v.name, v.vehicle_cetagory, v.id, v.status].join(' ').toLowerCase();
                return haystack.includes(query);
            });
        }
        renderVehicles(filtered);
    }

    function updateFleetStats(vehicles) {
        const list = Array.isArray(vehicles) ? vehicles : [];
        let active = 0, maintenance = 0, inactive = 0;
        list.forEach(v => {
            const s = String(v.status || 'Active');
            if (s === 'Active') active++;
            else if (s === 'Maintenance') maintenance++;
            else if (s === 'Inactive') inactive++;
        });
        $('#stat-total').text(list.length);
        $('#stat-active').text(active);
        $('#stat-maintenance').text(maintenance);
        $('#stat-inactive').text(inactive);
    }

    function renderVehicles(vehicles) {
        if (!vehicles || vehicles.length === 0) {
            const isFiltered = currentFilter !== 'all' || ($('#fleet-search').val() || '').trim() !== '';
            $('#vehicles-table-body').html(`
                <tr>
                  <td colspan="7" class="px-6 py-20 text-center">
                    <div class="flex flex-col items-center max-w-xs mx-auto">
                      <div class="w-16 h-16 rounded-2xl bg-primary/5 dark:bg-white/5 flex items-center justify-center mb-4">
                        <i class="${isFiltered ? 'ri-filter-off-line' : 'ri-car-line'} text-3xl text-textmuted/40"></i>
                      </div>
                      <h3 class="text-base font-bold text-defaulttextcolor dark:text-defaulttextcolor/90 mb-1">${isFiltered ? 'No Matches' : 'No Vehicles Yet'}</h3>
                      <p class="text-sm text-textmuted dark:text-textmuted/50 mb-5">${isFiltered ? 'Try adjusting your search or filter.' : 'Add your first vehicle to get started.'}</p>
                      ${isFiltered ? '' : '<a href="vehicle.php" class="ti-btn bg-primary text-white !rounded-xl px-5 font-semibold shadow-sm"><i class="ri-add-line me-1"></i> Add Vehicle</a>'}
                    </div>
                  </td>
                </tr>
            `);
            return;
        }

        let html = '';
        vehicles.forEach((vehicle, idx) => {
            const safeId = String(vehicle.id || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            const date = vehicle.date_entered
                ? new Date(vehicle.date_entered).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
                : '—';
            const category = (vehicle.vehicle_cetagory || 'General').replace(/_/g, ' ');
            const status = vehicle.status || 'Active';

            const badgeClass = {
                Active: 'fl-badge-active',
                Inactive: 'fl-badge-inactive',
                Maintenance: 'fl-badge-maintenance'
            }[status] || 'fl-badge-active';

            const imgField = (vehicle.image_c || vehicle.images_c || '').trim();
            const thumbUrl = imgField ? imgField.split(',')[0].trim() : '';

            const thumbBg = thumbUrl
                ? ''
                : 'bg-primary/8 text-primary';

            html += `
                <tr class="fl-animate-row" style="animation-delay: ${idx * 30}ms" data-vehicle-id="${vehicle.id}">
                  <td>
                    <div class="flex items-center gap-3">
                      <div class="fl-vehicle-thumb ${thumbBg}" style="${thumbUrl ? 'background:var(--fl-surface-2)' : ''}">
                        ${thumbUrl
                            ? `<img src="${thumbUrl}" alt="" onerror="this.outerHTML='<i class=\\'ri-car-fill text-xl text-primary\\'></i>'">`
                            : '<i class="ri-car-fill text-xl"></i>'}
                      </div>
                      <div>
                        <div class="text-[13px] font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 leading-tight">${vehicle.name || 'Untitled'}</div>
                        <div class="text-[10px] text-textmuted dark:text-textmuted/50 font-mono mt-0.5 tracking-wide">${(vehicle.id || '').substring(0, 8).toUpperCase()}</div>
                      </div>
                    </div>
                  </td>
                  <td><span class="fl-category-pill">${category}</span></td>
                  <td>
                    <div class="flex items-center gap-3">
                      <span class="text-xs font-medium text-defaulttextcolor dark:text-defaulttextcolor/90"><i class="ri-user-3-line text-[11px] me-1 text-primary/60"></i>${vehicle.passenger || 0}</span>
                      <span class="text-[10px] text-textmuted dark:text-textmuted/40">·</span>
                      <span class="text-xs font-medium text-defaulttextcolor dark:text-defaulttextcolor/90"><i class="ri-luggage-cart-line text-[11px] me-1 text-primary/60"></i>${vehicle.bags || 0}</span>
                    </div>
                  </td>
                  <td>
                    <div class="fl-rate-block">
                      <span class="fl-rate-val">$${parseFloat(vehicle.rate_c || 0).toFixed(2)}</span>
                      <span class="fl-rate-unit">/hr</span>
                    </div>
                  </td>
                  <td><span class="fl-badge ${badgeClass}">${status}</span></td>
                  <td><span class="text-xs text-textmuted dark:text-textmuted/50 font-medium">${date}</span></td>
                  <td class="text-end">
                    <div class="flex items-center justify-end gap-2">
                      <a href="vehicle_detail.php?id=${vehicle.id}" class="fl-action-btn" title="View details"><i class="ri-eye-line"></i></a>
                      <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Vehicles', 'update') == 1): ?>
                      <a href="vehicle.php?id=${vehicle.id}" class="fl-action-btn" title="Edit"><i class="ri-edit-line"></i></a>
                      <?php endif; ?>
                      <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Vehicles', 'delete') == 1): ?>
                      <button type="button" onclick="deleteVehicleBtn('${safeId}')" class="fl-action-btn fl-action-danger" title="Delete"><i class="ri-delete-bin-line"></i></button>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
            `;
        });

        $('#vehicles-table-body').html(html);

        $('#vehicles-table-body').off('click.rowNav').on('click.rowNav', 'tr[data-vehicle-id]', function (e) {
            if ($(e.target).closest('a, button, .fl-action-btn').length) return;
            window.location.href = 'vehicle_detail.php?id=' + $(this).data('vehicle-id');
        });
    }

    function deleteVehicleBtn(id) {
        Swal.fire({
            title: 'Delete this vehicle?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'ti-btn ti-btn-danger m-1 !rounded-xl',
                cancelButton: 'ti-btn ti-btn-light m-1 !rounded-xl'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
                    type: 'POST',
                    data: { action: 'delete_vehicle', id: id },
                    success: function (resp) {
                        const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
                        if (data.success) {
                            Swal.fire({ title: 'Deleted!', text: 'Vehicle removed from fleet.', icon: 'success', timer: 1500, showConfirmButton: false });
                            loadVehicles();
                        } else {
                            Swal.fire('Error', data.message || 'Failed to delete vehicle', 'error');
                        }
                    }
                });
            }
        });
    }
</script>
