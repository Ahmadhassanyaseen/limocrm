<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="flex items-start justify-between page-header-breadcrumb flex-wrap gap-3 mb-4">
            <div>
                <div class="text-sm text-textmuted dark:text-textmuted/50 font-medium">Fleet</div>
                <h1 class="page-title font-semibold text-xl mb-0 text-defaulttextcolor dark:text-defaulttextcolor/90">Vehicle Management</h1>
                <p class="text-xs text-textmuted dark:text-textmuted/50 mt-1 mb-0">View, filter, and manage your fleet in one place.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="hs-tooltip inline-block">
                    <button type="button" class="hs-tooltip-toggle ti-btn ti-btn-sm ti-btn-soft-secondary" onclick="loadVehicles()">
                        <i class="ri-refresh-line"></i>
                    </button>
                    <span class="hs-tooltip-content ti-tooltip-content" role="tooltip">Refresh List</span>
                </div>
                <a href="vehicle.php" class="ti-btn ti-btn-sm bg-primary text-white font-medium shadow-sm hover:shadow-md transition-all btn-wave">
                    <i class="ri-add-circle-line me-1 align-middle text-lg"></i> Add Vehicle
                </a>
            </div>
        </div>

        <!-- Fleet stats -->
        <div class="grid xl:grid-cols-4 lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-6 mb-6" id="fleet-stats">
            <div class="box crm-card">
              <div class="box-body">
                <div class="flex justify-between mb-2">
                  <div class="p-2 border border-primary/10 bg-primary/10 rounded-full">
                    <span class="avatar avatar-md avatar-rounded bg-primary svg-white mb-0">
                      <i class="ri-truck-line"></i>
                    </span>
                  </div>
                </div>
                <p class="flex-auto text-textmuted dark:text-textmuted/50 text-[14px] mb-0">Total Vehicles</p>
                <div class="flex items-center justify-between mt-1">
                  <h4 class="mb-0 flex items-center" id="stat-total">0</h4>
                </div>
              </div>
            </div>

            <div class="box crm-card">
              <div class="box-body">
                <div class="flex justify-between mb-2">
                  <div class="p-2 border border-success/10 bg-success/10 rounded-full">
                    <span class="avatar avatar-md avatar-rounded bg-success svg-white mb-0">
                      <i class="ri-checkbox-circle-line"></i>
                    </span>
                  </div>
                </div>
                <p class="flex-auto text-textmuted dark:text-textmuted/50 text-[14px] mb-0">Active</p>
                <div class="flex items-center justify-between mt-1">
                  <h4 class="mb-0 flex items-center" id="stat-active">0</h4>
                </div>
              </div>
            </div>

            <div class="box crm-card">
              <div class="box-body">
                <div class="flex justify-between mb-2">
                  <div class="p-2 border border-warning/10 bg-warning/10 rounded-full">
                    <span class="avatar avatar-md avatar-rounded bg-warning svg-white mb-0">
                      <i class="ri-tools-line"></i>
                    </span>
                  </div>
                </div>
                <p class="flex-auto text-textmuted dark:text-textmuted/50 text-[14px] mb-0">Maintenance</p>
                <div class="flex items-center justify-between mt-1">
                  <h4 class="mb-0 flex items-center" id="stat-maintenance">0</h4>
                </div>
              </div>
            </div>

            <div class="box crm-card">
              <div class="box-body">
                <div class="flex justify-between mb-2">
                  <div class="p-2 border border-danger/10 bg-danger/10 rounded-full">
                    <span class="avatar avatar-md avatar-rounded bg-danger svg-white mb-0">
                      <i class="ri-error-warning-line"></i>
                    </span>
                  </div>
                </div>
                <p class="flex-auto text-textmuted dark:text-textmuted/50 text-[14px] mb-0">Inactive</p>
                <div class="flex items-center justify-between mt-1">
                  <h4 class="mb-0 flex items-center" id="stat-inactive">0</h4>
                </div>
              </div>
            </div>

            
        </div>

        <div class="grid grid-cols-12 gap-6 pb-12">
            <div class="xl:col-span-12 col-span-12">
                <div class="box overflow-hidden shadow-sm">
                    <div class="box-body p-0">
                        <div class="overflow-auto">
                            <table class="table w-full text-nowrap">
                                <thead class="bg-black/[0.02] dark:bg-white/5 border-b border-defaultborder dark:border-defaultborder/10">
                                    <tr>
                                        <th scope="col" class="text-start text-xs font-bold text-textmuted dark:text-textmuted/50 uppercase px-6 py-4 tracking-wider">Vehicle</th>
                                        <th scope="col" class="text-start text-xs font-bold text-textmuted dark:text-textmuted/50 uppercase px-6 py-4 tracking-wider">Category</th>
                                        <th scope="col" class="text-start text-xs font-bold text-textmuted dark:text-textmuted/50 uppercase px-6 py-4 tracking-wider">Capacity</th>
                                        <th scope="col" class="text-start text-xs font-bold text-textmuted dark:text-textmuted/50 uppercase px-6 py-4 tracking-wider">Rate</th>
                                        <th scope="col" class="text-start text-xs font-bold text-textmuted dark:text-textmuted/50 uppercase px-6 py-4 tracking-wider">Status</th>
                                        <th scope="col" class="text-start text-xs font-bold text-textmuted dark:text-textmuted/50 uppercase px-6 py-4 tracking-wider">Added On</th>
                                        <th scope="col" class="text-end text-xs font-bold text-textmuted dark:text-textmuted/50 uppercase px-6 py-4 tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="vehicles-table-body" class="divide-y divide-defaultborder dark:divide-defaultborder/10">
                                    <!-- Vehicles will be injected here -->
                                    <tr id="table-loading">
                                        <td colspan="7" class="px-6 py-12 text-center text-textmuted dark:text-textmuted/50">
                                            <div class="flex flex-col items-center gap-2">
                                                <i class="ri-loader-4-line text-3xl animate-spin text-primary"></i>
                                                <span class="font-medium">Fetching your fleet...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
    $(document).ready(function() {
        loadVehicles();
    });

    function loadVehicles() {
        updateFleetStats([]);
        $('#vehicles-table-body').html(`
            <tr id="table-loading">
                <td colspan="7" class="px-6 py-12 text-center text-textmuted dark:text-textmuted/50">
                    <div class="flex flex-col items-center gap-2">
                        <i class="ri-loader-4-line text-3xl animate-spin text-primary"></i>
                        <span class="font-medium">Fetching your fleet...</span>
                    </div>
                </td>
            </tr>
        `);

        $.ajax({
            url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
            type: 'POST',
            data: { action: 'fetch_vehicles', user_id: '<?php echo $_SESSION['user']['id']; ?>' },
            success: function(response) {
                try {
                    const vehicles = typeof response === 'string' ? JSON.parse(response) : response;
                    renderVehicles(vehicles);
                } catch (e) {
                    updateFleetStats([]);
                    $('#vehicles-table-body').html(`
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-danger">
                                <i class="ri-error-warning-line text-3xl mb-2"></i>
                                <p class="font-medium">Failed to load vehicles data.</p>
                            </td>
                        </tr>
                    `);
                }
            }
        });
    }

    function updateFleetStats(vehicles) {
        const list = Array.isArray(vehicles) ? vehicles : [];
        const total = list.length;

        let active = 0;
        let maintenance = 0;
        let inactive = 0;
        let rateSum = 0;
        let rateCount = 0;

        list.forEach(v => {
            const status = String(v.status || 'Active');
            if (status === 'Active') active++;
            else if (status === 'Maintenance') maintenance++;
            else if (status === 'Inactive') inactive++;

            const r = parseFloat(v.rate_c || 0);
            if (Number.isFinite(r)) {
                rateSum += r;
                rateCount++;
            }
        });

        const avg = rateCount > 0 ? (rateSum / rateCount) : 0;

        $('#stat-total').text(total);
        $('#stat-active').text(active);
        $('#stat-maintenance').text(maintenance);
        $('#stat-inactive').text(inactive);
        $('#stat-avg-rate').text('$' + avg.toFixed(2));
    }

    function renderVehicles(vehicles) {
        if (!vehicles || vehicles.length === 0) {
            updateFleetStats([]);
            $('#vehicles-table-body').html(`
                <tr>
                    <td colspan="7" class="px-6 py-24 text-center">
                        <div class="flex flex-col items-center max-w-sm mx-auto">
                            <div class="mb-4 p-4 bg-black/5 dark:bg-white/5 rounded-full">
                                <i class="ri-car-line text-4xl text-textmuted dark:text-textmuted/50"></i>
                            </div>
                            <h3 class="text-lg font-bold text-defaulttextcolor dark:text-defaulttextcolor/90 mb-1">No Vehicles Found</h3>
                            <p class="text-sm text-textmuted dark:text-textmuted/50 mb-6">Start building your fleet by adding your first vehicle.</p>
                            <a href="vehicle.php" class="ti-btn ti-btn-primary btn-wave">Add Vehicle</a>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        updateFleetStats(vehicles);

        let html = '';
        vehicles.forEach(vehicle => {
            const safeVehicleId = String(vehicle.id || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            const date = vehicle.date_entered ? new Date(vehicle.date_entered).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '---';
            const category = (vehicle.vehicle_cetagory || 'General').replace(/_/g, ' ');
            
            const statusClass = {
                'Active': 'bg-success/10 text-success',
                'Inactive': 'bg-danger/10 text-danger',
                'Maintenance': 'bg-warning/10 text-warning'
            }[vehicle.status] || 'bg-secondary/10 text-secondary';

            html += `
                <tr class="hover:bg-black/[0.02] dark:hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary overflow-hidden">
                                ${vehicle.images_c ? `<img src="${vehicle.images_c}" class="w-full h-full object-cover" onerror="this.outerHTML='<i class=ri-car-fill text-lg></i>'">` : '<i class="ri-car-fill text-lg"></i>'}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 leading-none mb-1">${vehicle.name}</div>
                                <div class="text-[11px] text-textmuted dark:text-textmuted/50 font-medium uppercase tracking-wider">${vehicle.id.substring(0, 8)}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-semibold text-defaulttextcolor/80 dark:text-defaulttextcolor/70">${category}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs font-medium text-defaulttextcolor dark:text-defaulttextcolor/90"><i class="ri-user-line text-[10px] me-1"></i> ${vehicle.passenger || 0} Pass</span>
                            <span class="text-xs font-medium text-textmuted dark:text-textmuted/50"><i class="ri-briefcase-line text-[10px] me-1"></i> ${vehicle.bags || 0} Bags</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-primary">$${parseFloat(vehicle.rate_c || 0).toFixed(2)}</span>
                        <span class="text-[10px] text-textmuted dark:text-textmuted/50 block font-medium">/ Hour</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider ${statusClass}">${vehicle.status || 'Active'}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs text-textmuted dark:text-textmuted/50 font-medium">${date}</span>
                    </td>
                    <td class="px-6 py-4 text-end">
                        <div class="btn-list justify-end">
                            <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                                <a
                                    href="vehicle.php?id=${vehicle.id}"
                                    class="hs-tooltip-toggle ti-btn ti-btn-icon !rounded-full me-2 ti-btn-soft-info"
                                    aria-label="Edit vehicle"
                                >
                                    <i class="ri-edit-line"></i>
                                </a>
                               
                            </div>

                            <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                                <button
                                    type="button"
                                    onclick="deleteVehicleBtn('${safeVehicleId}')"
                                    class="hs-tooltip-toggle ti-btn ti-btn-icon !rounded-full me-2 ti-btn-soft-primary2"
                                    aria-label="Delete vehicle"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                               
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        $('#vehicles-table-body').html(html);
    }

    function deleteVehicleBtn(id) {
        Swal.fire({
            title: 'Delete this vehicle?',
            text: "This action cannot be undone and will remove it from the fleet.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e9333f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'ti-btn ti-btn-danger m-1',
                cancelButton: 'ti-btn ti-btn-light m-1'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
                    type: 'POST',
                    data: { action: 'delete_vehicle', id: id },
                    success: function(resp) {
                        const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
                        if (data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Vehicle has been removed.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
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