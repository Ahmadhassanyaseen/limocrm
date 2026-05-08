<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$vehicleId = $_GET['id'] ?? '';
$isEdit = !empty($vehicleId);
?>

<style>
  /* Vehicle page: modern, theme-friendly controls (scoped) */
  .vehicle-page .vehicle-label{
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: rgba(15,23,42,0.55);
    margin-bottom: .5rem;
    display: block;
  }
  .dark .vehicle-page .vehicle-label{ color: rgba(255,255,255,0.52); }

  .vehicle-page .form-control{
    height: 42px;
    border-radius: 12px;
    border-color: rgba(15,23,42,0.12) !important;
    background: rgba(15,23,42,0.02) !important;
  }
  .dark .vehicle-page .form-control{
    border-color: rgba(255,255,255,0.12) !important;
    background: rgba(255,255,255,0.04) !important;
  }
  .vehicle-page textarea.form-control{
    height: auto;
    min-height: 132px;
    resize: vertical;
  }
  .vehicle-page select.form-control{
    padding-right: 36px !important;
    cursor: pointer;
    background-image: linear-gradient(45deg, transparent 50%, currentColor 50%),
                      linear-gradient(135deg, currentColor 50%, transparent 50%);
    background-position: calc(100% - 18px) 50%, calc(100% - 13px) 50%;
    background-size: 6px 6px, 6px 6px;
    background-repeat: no-repeat;
  }

  .vehicle-page .vehicle-card{
    background: #fff;
    border: 1px solid rgba(15,23,42,0.12);
    border-radius: 16px;
    overflow: hidden;
  }
  .dark .vehicle-page .vehicle-card{
    background: rgba(255,255,255,0.04);
    border-color: rgba(255,255,255,0.10);
  }
  .vehicle-page .vehicle-card-header{
    background: rgba(15,23,42,0.02);
    border-bottom: 1px solid rgba(15,23,42,0.12);
  }
  .dark .vehicle-page .vehicle-card-header{
    background: rgba(255,255,255,0.03);
    border-bottom-color: rgba(255,255,255,0.10);
  }
</style>

<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="vehicle-page flex items-start justify-between page-header-breadcrumb flex-wrap gap-3 mb-4">
            <div>
                <div class="text-sm text-textmuted dark:text-textmuted/50 font-medium">Fleet</div>
                <h1 class="page-title font-semibold text-xl mb-0 text-defaulttextcolor dark:text-defaulttextcolor/90"><?php echo $isEdit ? 'Edit Vehicle' : 'Add New Vehicle'; ?></h1>
                <p class="text-xs text-textmuted dark:text-textmuted/50 mt-1 mb-0"><?php echo $isEdit ? 'Update details for your vehicle.' : 'Register a new vehicle in your fleet.'; ?></p>
            </div>
            <div class="flex items-center gap-3">
                <a href="vehicles.php" class="ti-btn ti-btn-sm ti-btn-soft-secondary font-medium">
                    <i class="ri-arrow-left-line me-1"></i> Back to Fleet
                </a>
            </div>
        </div>

        <div class="vehicle-page grid grid-cols-12 gap-6 pb-12">
            <div class="xl:col-span-8 col-span-12">
                <form id="vehicle-form" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $vehicleId; ?>">
                    <input type="hidden" name="image_c" id="v-image-c">
                    
                    <!-- Basic Information Card -->
                    <div class="box vehicle-card mb-6 overflow-hidden shadow-sm">
                        <div class="box-header vehicle-card-header px-6 py-4">
                            <h5 class="box-title font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 flex items-center gap-2">
                                <i class="ri-information-line text-primary"></i>
                                Basic Information
                            </h5>
                        </div>
                        <div class="box-body p-6 space-y-6">
                            <div>
                                <label class="vehicle-label">Vehicle Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="v-name" class="form-control rounded-xl py-2.5" placeholder="e.g., Luxury Cadillac Escalade" required>
                            </div>
                            
                            <div class="grid grid-cols-12 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="vehicle-label">Category <span class="text-danger">*</span></label>
                                    <select name="vehicle_cetagory" id="v-category" class="form-control rounded-xl py-2.5" required>
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
                                    <label class="vehicle-label">Status</label>
                                    <select name="status" id="v-status" class="form-control rounded-xl py-2.5">
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Maintenance">Maintenance</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-12 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="vehicle-label">Passengers</label>
                                    <input type="number" name="passenger" id="v-passenger" class="form-control rounded-xl py-2.5" placeholder="0" min="0">
                                </div>
                                <div>
                                    <label class="vehicle-label">Bags</label>
                                    <input type="number" name="bags" id="v-bags" class="form-control rounded-xl py-2.5" placeholder="0" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Card -->
                    <div class="box vehicle-card mb-6 overflow-hidden shadow-sm">
                        <div class="box-header vehicle-card-header px-6 py-4">
                            <h5 class="box-title font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 flex items-center gap-2">
                                <i class="ri-money-dollar-circle-line text-primary"></i>
                                Pricing
                            </h5>
                            <p class="text-[11px] text-textmuted dark:text-textmuted/50 mt-1 mb-0">Set the hourly rate, fuel surcharge and driver commission for this vehicle.</p>
                        </div>
                        <div class="box-body p-6">
                            <div class="grid grid-cols-12 sm:grid-cols-3 gap-6">
                                <div>
                                    <label class="vehicle-label">Hourly Rate ($)</label>
                                    <div class="relative">
                                       
                                        <input type="number" name="rate_c" id="v-rate" class="form-control rounded-xl py-2.5 !pl-7" placeholder="0.00" min="0" step="0.01">
                                    </div>
                                    <p class="text-[10px] text-textmuted dark:text-textmuted/50 mt-2 italic">Charged per hour of service.</p>
                                </div>
                                <div>
                                    <label class="vehicle-label">Fuel Surcharge (%) </label>
                                    <div class="relative">
                                        <input type="number" name="fuel_c" id="v-fuel" class="form-control rounded-xl py-2.5 !pr-9" placeholder="0" min="0" max="100" step="0.01">
                                        
                                    </div>
                                    <p class="text-[10px] text-textmuted dark:text-textmuted/50 mt-2 italic">Fuel surcharge applied to the trip total.</p>
                                </div>
                                <div>
                                    <label class="vehicle-label">Driver Commission (%) </label>
                                    <div class="relative">
                                        <input type="number" name="driver_commission_c" id="v-commission" class="form-control rounded-xl py-2.5 !pr-9" placeholder="0" min="0" max="100" step="0.01">
                                        
                                    </div>
                                    <p class="text-[10px] text-textmuted dark:text-textmuted/50 mt-2 italic">Share of the trip earnings paid to the driver.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details & Features Card -->
                    <div class="box vehicle-card mb-6 overflow-hidden shadow-sm">
                        <div class="box-header vehicle-card-header px-6 py-4">
                            <h5 class="box-title font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 flex items-center gap-2">
                                <i class="ri-list-check-2 text-primary"></i>
                                Details & Features
                            </h5>
                        </div>

                        <div class="box-body p-6 space-y-6">
                            <div>
                                <label class="vehicle-label">Facilities / Features (Comma separated)</label>
                                <input type="text" name="facilities" id="v-facilities" class="form-control rounded-xl py-2.5" placeholder="WiFi, Drinks, Leather Seats, Entertainment System">
                                <p class="text-[10px] text-textmuted dark:text-textmuted/50 mt-2 italic">Separate different features with a comma.</p>
                            </div>
                            <div>
                                <label class="vehicle-label">Description</label>
                                <textarea name="description" id="v-description" class="form-control rounded-xl py-2.5" rows="5" placeholder="Provide a detailed description of the vehicle..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="vehicles.php" class="ti-btn ti-btn-soft-secondary !rounded-xl px-6">Cancel</a>
                        <button type="submit" class="ti-btn ti-btn-primary !rounded-xl px-8 font-semibold shadow-md shadow-primary/20 hover:shadow-lg transition-all" id="save-btn">
                            <i class="ri-save-line me-1"></i> <?php echo $isEdit ? 'Update Vehicle' : 'Save Vehicle'; ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Side Cards: Image Upload & Preview -->
            <div class="xl:col-span-4 col-span-12">
                <div class="box vehicle-card mb-6 overflow-hidden shadow-sm sticky top-4">
                    <div class="box-header vehicle-card-header px-6 py-4 text-center">
                        <h5 class="box-title font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90">Vehicle Image</h5>
                    </div>
                    <div class="box-body p-6">
                        <div class="mb-6">
                            <div class="relative group aspect-video rounded-2xl bg-black/5 dark:bg-white/5 border-2 border-dashed border-defaultborder dark:border-defaultborder/10 flex items-center justify-center overflow-hidden">
                                <img id="image-preview" src="" 
                                     class="w-full h-full object-cover hidden" 
                                     onerror="this.classList.add('hidden'); document.getElementById('placeholder').classList.remove('hidden')">
                                <div id="placeholder" class="text-center">
                                    <i class="ri-image-add-line text-4xl text-textmuted/40 mb-2 block"></i>
                                    <span class="text-xs font-medium text-textmuted dark:text-textmuted/50 uppercase tracking-widest">No Image Preview</span>
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
                            <p class="text-[10px] text-textmuted dark:text-textmuted/50 mt-2 text-center italic">Supported: JPG, PNG, WEBP (Max 2MB)</p>
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
                        $('#v-image-c').val(data.url);
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
                    console.log(data);
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

                    const imageUrl = v.image_c || v.images_c || '';
                    if (imageUrl) {
                        $('#image-preview').attr('src', imageUrl).removeClass('hidden');
                        $('#placeholder').addClass('hidden');
                        $('#v-image-c').val(imageUrl);
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