<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2 py-4">
            <div>
                <h1 class="page-title font-bold text-2xl mb-1 text-gray-800 dark:text-gray-100">Vehicle Management</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">View and manage your fleet of vehicles</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="hs-tooltip inline-block">
                    <button type="button" class="hs-tooltip-toggle ti-btn ti-btn-sm ti-btn-light !border-gray-200 dark:!border-white/10" onclick="loadVehicles()">
                        <i class="ri-refresh-line"></i>
                    </button>
                    <span class="hs-tooltip-content ti-tooltip-content" role="tooltip">Refresh List</span>
                </div>
                <a href="vehicle.php" class="ti-btn ti-btn-md bg-primary text-white font-medium shadow-sm hover:shadow-md transition-all btn-wave">
                    <i class="ri-add-circle-line me-1 align-middle text-lg"></i> Add Vehicle
                </a>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6 pb-12">
            <div class="xl:col-span-12 col-span-12">
                <div class="box bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
                    <div class="box-body p-0">
                        <div class="overflow-auto">
                            <table class="table w-full text-nowrap">
                                <thead class="bg-gray-50 dark:bg-black/20 border-b border-gray-200 dark:border-white/10">
                                    <tr>
                                        <th scope="col" class="text-start text-xs font-bold text-gray-500 uppercase px-6 py-4 tracking-wider">Vehicle</th>
                                        <th scope="col" class="text-start text-xs font-bold text-gray-500 uppercase px-6 py-4 tracking-wider">Category</th>
                                        <th scope="col" class="text-start text-xs font-bold text-gray-500 uppercase px-6 py-4 tracking-wider">Capacity</th>
                                        <th scope="col" class="text-start text-xs font-bold text-gray-500 uppercase px-6 py-4 tracking-wider">Rate</th>
                                        <th scope="col" class="text-start text-xs font-bold text-gray-500 uppercase px-6 py-4 tracking-wider">Status</th>
                                        <th scope="col" class="text-start text-xs font-bold text-gray-500 uppercase px-6 py-4 tracking-wider">Added On</th>
                                        <th scope="col" class="text-end text-xs font-bold text-gray-500 uppercase px-6 py-4 tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="vehicles-table-body" class="divide-y divide-gray-200 dark:divide-white/10">
                                    <!-- Vehicles will be injected here -->
                                    <tr id="table-loading">
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
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
        $('#vehicles-table-body').html(`
            <tr id="table-loading">
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
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

    function renderVehicles(vehicles) {
        if (!vehicles || vehicles.length === 0) {
            $('#vehicles-table-body').html(`
                <tr>
                    <td colspan="7" class="px-6 py-24 text-center">
                        <div class="flex flex-col items-center max-w-sm mx-auto">
                            <div class="mb-4 p-4 bg-gray-100 dark:bg-white/5 rounded-full">
                                <i class="ri-car-line text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">No Vehicles Found</h3>
                            <p class="text-sm text-gray-500 mb-6">Start building your fleet by adding your first vehicle.</p>
                            <a href="vehicle.php" class="ti-btn ti-btn-primary btn-wave">Add Vehicle</a>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        let html = '';
        vehicles.forEach(vehicle => {
            const date = vehicle.date_entered ? new Date(vehicle.date_entered).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '---';
            const category = (vehicle.vehicle_cetagory || 'General').replace(/_/g, ' ');
            
            const statusClass = {
                'Active': 'bg-success/10 text-success',
                'Inactive': 'bg-danger/10 text-danger',
                'Maintenance': 'bg-warning/10 text-warning'
            }[vehicle.status] || 'bg-gray-100 text-gray-500';

            html += `
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary overflow-hidden">
                                ${vehicle.images_c ? `<img src="${vehicle.images_c}" class="w-full h-full object-cover" onerror="this.outerHTML='<i class=ri-car-fill text-lg></i>'">` : '<i class="ri-car-fill text-lg"></i>'}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-800 dark:text-gray-100 leading-none mb-1">${vehicle.name}</div>
                                <div class="text-[11px] text-gray-500 font-medium uppercase tracking-wider">${vehicle.id.substring(0, 8)}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">${category}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300"><i class="ri-user-line text-[10px] me-1"></i> ${vehicle.passenger || 0} Pass</span>
                            <span class="text-xs font-medium text-gray-500"><i class="ri-briefcase-line text-[10px] me-1"></i> ${vehicle.bags || 0} Bags</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-primary">$${parseFloat(vehicle.rate_c || 0).toFixed(2)}</span>
                        <span class="text-[10px] text-gray-400 block font-medium">/ Hour</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider ${statusClass}">${vehicle.status || 'Active'}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs text-gray-500 font-medium">${date}</span>
                    </td>
                    <td class="px-6 py-4 text-end">
                        <div class="flex justify-end gap-1">
                            <a href="vehicle.php?id=${vehicle.id}" class="hs-tooltip-toggle ti-btn ti-btn-sm ti-btn-light !border-gray-200 dark:!border-white/10 hover:text-primary transition-all">
                                <i class="ri-edit-line"></i>
                                <span class="hs-tooltip-content ti-tooltip-content" role="tooltip">Edit</span>
                            </a>
                            <button onclick="deleteVehicleBtn('${vehicle.id}')" class="hs-tooltip-toggle ti-btn ti-btn-sm ti-btn-light !border-gray-200 dark:!border-white/10 hover:text-danger transition-all">
                                <i class="ri-delete-bin-line"></i>
                                <span class="hs-tooltip-content ti-tooltip-content" role="tooltip">Delete</span>
                            </button>
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