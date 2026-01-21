<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$vehicleId = $_GET['id'] ?? '';
$isEdit = !empty($vehicleId);
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2 py-4">
            <div>
                <h1 class="page-title font-bold text-2xl mb-1 text-gray-800 dark:text-gray-100"><?php echo $isEdit ? 'Edit Vehicle' : 'Add New Vehicle'; ?></h1>
                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo $isEdit ? 'Update details for your vehicle' : 'Register a new vehicle in your fleet'; ?></p>
            </div>
            <div class="flex items-center gap-3">
                <a href="vehicles.php" class="ti-btn ti-btn-md ti-btn-light !border-gray-200 dark:!border-white/10 font-medium">
                    <i class="ri-arrow-left-line me-1"></i> Back to Fleet
                </a>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6 pb-12">
            <div class="xl:col-span-8 col-span-12">
                <form id="vehicle-form" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $vehicleId; ?>">
                    <input type="hidden" name="images_c" id="v-images-c">
                    
                    <!-- Basic Information Card -->
                    <div class="box bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl mb-6 overflow-hidden shadow-sm">
                        <div class="box-header bg-gray-50/50 dark:bg-black/10 px-6 py-4 border-b border-gray-200 dark:border-white/10">
                            <h5 class="box-title font-bold text-gray-700 dark:text-gray-300">Basic Information</h5>
                        </div>
                        <div class="box-body p-6 space-y-6">
                            <div>
                                <label class="text-xs font-bold text-gray-500 mb-2 block uppercase tracking-wider">Vehicle Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="v-name" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl py-2.5" placeholder="e.g., Luxury Cadillac Escalade" required>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="text-xs font-bold text-gray-500 mb-2 block uppercase tracking-wider">Category <span class="text-danger">*</span></label>
                                    <select name="vehicle_cetagory" id="v-category" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl py-2.5" required>
                                        <option value="Sedan">Sedan</option>
                                        <option value="SUV">SUV</option>
                                        <option value="Stretch Limo">Stretch Limo</option>
                                        <option value="Stretch SUV Limo">Stretch SUV Limo</option>
                                        <option value="Mini Bus">Mini Bus</option>
                                        <option value="Motor Coach">Motor Coach</option>
                                        <option value="Van">Van</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 mb-2 block uppercase tracking-wider">Status</label>
                                    <select name="status" id="v-status" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl py-2.5">
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Maintenance">Maintenance</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-6">
                                <div>
                                    <label class="text-xs font-bold text-gray-500 mb-2 block uppercase tracking-wider">Passengers</label>
                                    <input type="number" name="passenger" id="v-passenger" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl py-2.5" placeholder="0" min="0">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 mb-2 block uppercase tracking-wider">Bags</label>
                                    <input type="number" name="bags" id="v-bags" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl py-2.5" placeholder="0" min="0">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 mb-2 block uppercase tracking-wider">Rate per Hour ($)</label>
                                    <input type="number" name="rate_c" id="v-rate" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl py-2.5" placeholder="0.00" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details & Features Card -->
                    <div class="box bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl mb-6 overflow-hidden shadow-sm">
                        <div class="box-header bg-gray-50/50 dark:bg-black/10 px-6 py-4 border-b border-gray-200 dark:border-white/10">
                            <h5 class="box-title font-bold text-gray-700 dark:text-gray-300">Details & Features</h5>
                        </div>
                        <div class="box-body p-6 space-y-6">
                            <div>
                                <label class="text-xs font-bold text-gray-500 mb-2 block uppercase tracking-wider">Facilities / Features (Comma separated)</label>
                                <input type="text" name="facilities" id="v-facilities" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl py-2.5" placeholder="WiFi, Drinks, Leather Seats, Entertainment System">
                                <p class="text-[10px] text-gray-400 mt-2 italic">Separate different features with a comma.</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 mb-2 block uppercase tracking-wider">Description</label>
                                <textarea name="description" id="v-description" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl py-2.5" rows="5" placeholder="Provide a detailed description of the vehicle..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="vehicles.php" class="ti-btn ti-btn-light !rounded-xl px-6">Cancel</a>
                        <button type="submit" class="ti-btn ti-btn-primary !rounded-xl px-8 font-bold shadow-md shadow-primary/20 hover:shadow-lg transition-all" id="save-btn">
                            <i class="ri-save-line me-1"></i> <?php echo $isEdit ? 'Update Vehicle' : 'Save Vehicle'; ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Side Cards: Image Upload & Preview -->
            <div class="xl:col-span-4 col-span-12">
                <div class="box bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl mb-6 overflow-hidden shadow-sm sticky top-4">
                    <div class="box-header bg-gray-50/50 dark:bg-black/10 px-6 py-4 border-b border-gray-200 dark:border-white/10 text-center">
                        <h5 class="box-title font-bold text-gray-700 dark:text-gray-300">Vehicle Image</h5>
                    </div>
                    <div class="box-body p-6">
                        <div class="mb-6">
                            <div class="relative group aspect-video rounded-2xl bg-gray-100 dark:bg-white/5 border-2 border-dashed border-gray-200 dark:border-white/10 flex items-center justify-center overflow-hidden">
                                <img id="image-preview" src="" 
                                     class="w-full h-full object-cover hidden" 
                                     onerror="this.classList.add('hidden'); document.getElementById('placeholder').classList.remove('hidden')">
                                <div id="placeholder" class="text-center">
                                    <i class="ri-image-add-line text-4xl text-gray-300 mb-2 block"></i>
                                    <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">No Image Preview</span>
                                </div>
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer" onclick="document.getElementById('image-upload').click()">
                                    <span class="text-white text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                                        <i class="ri-upload-2-line text-lg"></i> Change Photo
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <input type="file" id="image-upload" name="vehicle_image" class="hidden" accept="image/*" onchange="previewImage(this)">
                            <button type="button" class="ti-btn bg-primary/10 text-primary w-full !rounded-xl font-bold py-2.5 transition-all hover:bg-primary hover:text-white" onclick="document.getElementById('image-upload').click()">
                                <i class="ri-upload-cloud-line me-1"></i> Choose Image
                            </button>
                            <p class="text-[10px] text-gray-400 mt-2 text-center italic">Supported: JPG, PNG, WEBP (Max 2MB)</p>
                        </div>
                    </div>
                </div>
                
                <?php if($isEdit): ?>
                <div class="box bg-gray-50 dark:bg-white/5 border border-dashed border-gray-200 dark:border-white/10 rounded-2xl p-6 text-center">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-black mb-1">System Intelligence</p>
                    <p class="text-xs text-gray-500 font-medium">This vehicle is linked to <br><span class="text-primary font-bold">12 Active Bookings</span></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        <?php if($isEdit): ?>
            loadVehicleData('<?php echo $vehicleId; ?>');
        <?php endif; ?>

        $('#vehicle-form').on('submit', function(e) {
            e.preventDefault();
            saveVehicle();
        });
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result).removeClass('hidden');
                $('#placeholder').addClass('hidden');
            }
            reader.readAsDataURL(input.files[0]);

            // Real-time upload
            const formData = new FormData();
            formData.append('vehicle_image', input.files[0]);
            formData.append('action', 'upload_vehicle_image');

            $.ajax({
                url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp) {
                    const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
                    if (data.success) {
                        $('#v-images-c').val(data.url);
                        console.log('Image uploaded:', data.url);
                    } else {
                        Swal.fire('Upload Error', data.message || 'Image upload failed', 'error');
                    }
                }
            });
        }
    }

    function loadVehicleData(id) {
        $.ajax({
            url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
            type: 'POST',
            data: { action: 'get_vehicle', id: id },
            success: function(resp) {
                const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (data.success) {
                    const v = data.vehicle;
                    $('#v-name').val(v.name);
                    $('#v-category').val(v.vehicle_cetagory);
                    $('#v-status').val(v.status || 'Active');
                    $('#v-passenger').val(v.passenger);
                    $('#v-bags').val(v.bags);
                    $('#v-rate').val(v.rate_c);
                    $('#v-facilities').val(v.facilities);
                    $('#v-description').val(v.description);

                    if (v.images_c) {
                        $('#image-preview').attr('src', v.images_c).removeClass('hidden');
                        $('#placeholder').addClass('hidden');
                        $('#v-images-c').val(v.images_c);
                    }
                } else {
                    Swal.fire('Error', 'Failed to fetch vehicle data', 'error');
                }
            }
        });
    }

    function saveVehicle() {
        const btn = $('#save-btn');
        btn.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin me-1"></i> Saving...');

        // FormData handles file uploads automatically
        const formData = new FormData($('#vehicle-form')[0]);
        formData.append('action', 'save_vehicle');
        formData.append('user_id', '<?php echo $_SESSION['user']['id']; ?>');

        $.ajax({
            url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Vehicle details have been updated.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'vehicles.php';
                    });
                } else {
                    Swal.fire('Error', data.message || 'Operation failed', 'error');
                }
                btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> <?php echo $isEdit ? 'Update Vehicle' : 'Save Vehicle'; ?>');
            }
        });
    }
</script>

<style>
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1);
    }
</style>