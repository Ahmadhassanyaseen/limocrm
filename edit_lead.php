
<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>
<?php
$data['id'] = $_GET['id'];
$response = fetchSingleLead($data);
$lead = $response[0];
?>

<div class="main-content app-content">
  <div class="container-fluid">
    <!-- Page Header -->
    <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
      <div>
        <nav aria-label="nav">
          <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item">
              <a href="leads.php">Leads</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              Edit Lead
            </li>
          </ol>
        </nav>
        <h1 class="page-title font-medium text-lg mb-0">Edit Lead</h1>
      </div>
      <div class="btn-list">
        <a href="lead.php?id=<?php echo $lead['id']; ?>" class="ti-btn bg-white dark:bg-bodybg border border-defaultborder dark:border-defaultborder/10 btn-wave !my-0 waves-effect waves-light">
          Cancel
        </a>
        <button type="button" id="saveLeadBtn" class="ti-btn ti-btn-primary !border-0 btn-wave me-0 waves-effect waves-light">
          <i class="ri-save-line me-1"></i> Save Changes
        </button>
      </div>
    </div>

    <!-- Start::row-1 -->
    <form id="editLeadForm">
      <input type="hidden" name="id" value="<?php echo $lead['id']; ?>">
      
      <div class="grid grid-cols-12 gap-x-6">
        <div class="xl:col-span-6 col-span-12">
          <div class="box">
            <div class="box-header">
              <div class="box-title">Lead Information</div>
            </div>
            <div class="box-body">
              <div class="space-y-4">
                <div class="grid grid-cols-12 gap-4">
                  <div class="col-span-6">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" value="<?php echo $lead['first_name']; ?>">
                  </div>
                  <div class="col-span-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" value="<?php echo $lead['last_name']; ?>">
                  </div>
                </div>
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $lead['email1']; ?>">
                    </div>
                    
                    <div class="col-span-6">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone" value="<?php echo !empty($lead['phone_work']) ? $lead['phone_work'] : $lead['phone_mobile']; ?>">
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
              <div class="box-title">Lead Pricing</div>
            </div>
            <div class="box-body">
              <div class="space-y-4">
                <div class="grid grid-cols-12 gap-4">
                  <div class="col-span-6">
                    <label class="form-label">Total Price</label>
                    <input type="text" class="form-control" name="total_price" value="<?php echo $lead['total_price_c']; ?>">
                  </div>
                  <div class="col-span-6">
                    <label class="form-label">Distance</label>
                    <input type="text" class="form-control" name="distance" value="<?php echo $lead['distance_c']; ?>">
                  </div>
                </div>
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-6">
                    <label class="form-label">Duration</label>
                    <input type="text" class="form-control" name="duration" value="<?php echo $lead['duration_c']; ?>">
                    </div>
                    
                    <div class="col-span-6">
                    <label class="form-label">Service Length</label>
                    <input type="text" class="form-control" name="service_length" value="<?php echo $lead['service_length_c']; ?>">
                    </div>
                </div>

               
              </div>
            </div>
          </div>
        </div>

        <div class="xl:col-span-6 col-span-12">
          <div class="box">
            <div class="box-header">
              <div class="box-title">Trip Details</div>
            </div>
            <div class="box-body">
              <div class="space-y-4">
               <div>
                  <label class="form-label">Service Type</label>
                  <input type="text" class="form-control" name="service_type" value="<?php echo $lead['service_type_c']; ?>">
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
                  <label class="form-label">Description</label>
                  <textarea class="form-control" name="description" rows="3"><?php echo $lead['description']; ?></textarea>
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