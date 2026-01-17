

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
?>

<div class="main-content app-content">
      <div class="container-fluid">
        <!-- Page Header -->
        <div
          class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2"
         >
          <div>
            <nav aria-label="nav">
              <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item">
                  <a href="leads.php">Leads</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                  Lead Details
                </li>
              </ol>
            </nav>
            <h1 class="page-title font-medium text-lg mb-0">Lead Details</h1>
          </div>
          <div class="btn-list"> 
            <a href="edit_lead.php?id=<?php echo $lead['id']; ?>" type="button" class="ti-btn bg-primary text-white ti-btn-sm btn-wave me-0 waves-effect waves-light"><i class="ri-edit-line me-1 align-middle"></i>Edit Lead</a> </div>
        </div>
        <!-- Page Header Close -->
        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-x-6">
          <div class="xl:col-span-4 col-span-12">
            <div class="box overflow-hidden">
              <div class="box-header">
                <div class="box-title">Lead Information</div>
              </div>
              <div class="box-body p-0">
                <div class="table-responsive">
                  <table class="table text-nowrap">
                    <tbody>
                      <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                        <td><span class="font-medium">Lead Name :</span></td>
                        <td><?php echo $lead['first_name'] . ' ' . $lead['last_name']; ?></td>
                      </tr>
                      <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                        <td><span class="font-medium">Status :</span></td>
                        <td>
                          <span class="badge bg-primary/10 text-primary"><?php echo $lead['status']; ?></span>
                        </td>
                      </tr>
                      <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                        <td><span class="font-medium">Email :</span></td>
                         <td><?php echo $lead['email1']; ?></td>
                      </tr>
                      <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                        <td><span class="font-medium">Phone :</span></td>
                        <td><?php echo !empty($lead['phone_work']) ? $lead['phone_work'] : $lead['phone_mobile']; ?></td>
                      </tr>
                      <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                        <td><span class="font-medium">Date Created :</span></td>
                        <td><?php echo $lead['date_entered']; ?></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
             <div class="box overflow-hidden">
              <div class="box-header">
                <div class="box-title">Trip Details</div>
                
              </div>
              <div class="box-body p-0">
                <div class="table-responsive">
                  <table class="table text-nowrap">
                    <tbody>
                       <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                        <td><span class="font-medium">Service Type :</span></td>
                        <td><?php echo $lead['service_type_c']; ?></td>
                      </tr>
                      <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                        <td><span class="font-medium">Event Date :</span></td>
                        <td><?php echo $lead['event_date_c']; ?></td>
                      </tr>
                      
                       <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                        <td><span class="font-medium">Passengers :</span></td>
                        <td><?php echo $lead['passengers_c']; ?></td>
                      </tr>
                       <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                        <td><span class="font-medium">Distance :</span></td>
                        <td><?php echo $lead['distance_c']; ?></td>
                      </tr>
                       <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                        <td><span class="font-medium">Duration :</span></td>
                        <td><?php echo $lead['duration_c']; ?></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="xl:col-span-8 col-span-12">
            <div class="box">
              <div class="box-header justify-between items-center">
                <div class="box-title">Trip Itinerary</div>
              </div>
              <div class="box-body">
                <div class="flex gap-2 mb-4 items-center flex-wrap">
                  <div class="text-[15px] font-medium">Trip Price :</div>
                  <h5 class="font-medium mb-0">
                    $<?php echo $lead['total_price_c']; ?>
                  </h5>
                </div>
                
                <div class="grid grid-cols-12 sm:gap-x-6 mb-4">
                  <div class="xl:col-span-12 col-span-12">
                     <ul class="ti-list-group mb-0">
                      <li class="ti-list-group-item">
                        <div class="flex items-center">
                          <div class="me-2">
                            <i class="ri-map-pin-line text-[15px] text-success leading-none p-1 bg-success/10 !rounded-full"></i>
                          </div>
                          <div class="font-medium">
                            <span class="text-xs text-muted block">Pickup Address</span>
                            <?php echo $lead['pickup_address_c']; ?>
                          </div>
                        </div>
                      </li>
                      <li class="ti-list-group-item">
                        <div class="flex items-center">
                          <div class="me-2">
                            <i class="ri-map-pin-line text-[15px] text-danger leading-none p-1 bg-danger/10 !rounded-full"></i>
                          </div>
                          <div class="font-medium">
                             <span class="text-xs text-muted block">Dropoff Address</span>
                            <?php echo $lead['dropoff_address_c']; ?>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>

                <div class="text-[15px] font-medium mb-2">Lead Discussion/Notes</div>
                 <div class="box-body p-0">
                  <ul class="list-none profile-timeline mb-3">
                    <?php if(!empty($lead['description'])): ?>
                    <li>
                      <div>
                        <span class="avatar avatar-sm avatar-rounded profile-timeline-avatar">
                          <i class="ri-file-text-line"></i>
                        </span>
                        <p class="text-textmuted dark:text-textmuted/50 mb-2">
                          <span class="text-defaulttextcolor">
                            <span class="font-medium">Description</span>
                          </span>
                        </p>
                        <p class="text-textmuted dark:text-textmuted/50 mb-2 text-xs">
                          <?php echo $lead['description']; ?>
                        </p>
                      </div>
                    </li>
                    <?php endif; ?>
                    <!-- Discussions or logs can go here -->
                  </ul>
                 </div>
              </div>
            </div>
            
          </div>
        </div>
        <!--End::row-1 -->
      </div>
    </div>

    
      
    
      <?php include_once "components/layout/footer.php"; ?>