<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
<style>
  div.dt-container .dt-paging .dt-paging-button.current,
div.dt-container .dt-paging .dt-paging-button.current:hover {
  background: #cf1c82 !important;
  color: #fff !important;
  border: 1px solid #fff !important;
}
table.dataTable thead th{
    background:lightgray!important;
}

</style>
<div class="table-responsive overflow-auto table-bordered-default">
                    <table id="leads-table" class="ti-custom-table text-nowrap">
                      <thead>
                        <tr
                          class="border-b border-defaultborder dark:border-defaultborder/10"
                        >
                          <th scope="col">S.NO</th>
                          <th scope="col">Name</th>
                          <th scope="col">Phone Number</th>
                          <th scope="col">Pickup Address</th>
                          <th scope="col">Dropoff Address</th>
                          <th scope="col">Event Date</th>
                          <th scope="col">Amount</th>
                          <th scope="col">Status</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i=0; $i < count($leads); $i++) { ?>
                        <tr class="border-b border-defaultborder dark:border-defaultborder/10">
                          <td><span class=""><?php echo $i+1; ?></span></td>
                          <td>
                            <div class="flex">
                              
                              <div class="flex-1 ms-2">
                                <p class="mb-0 text-[14px]"><?php echo $leads[$i]['first_name']; ?> <?php echo $leads[$i]['last_name']; ?></p>
                                <a
                                  href="javascript:void(0);"
                                  class="text-textmuted dark:text-textmuted/50 text-xs"
                                  ><?php echo $leads[$i]['email_c']; ?></a
                                >
                              </div>
                            </div>
                          </td>
                          <td><span class=""><?php echo $leads[$i]['phone_c']; ?></span></td>
                          <td><span class=""><i
                                class="ri-map-pin-fill text-textmuted dark:text-textmuted/50 me-1"
                              ></i
                              >
                             <?php echo $leads[$i]['pickup_address_c']; ?> 
                          </span></td>
                          
                          <td>
                            <span
                              ><i
                                class="ri-map-pin-fill text-textmuted dark:text-textmuted/50 me-1"
                              ></i
                              ><?php echo $leads[$i]['dropoff_address_c']; ?></span
                            >
                          </td>
                          <td><span><?php echo $leads[$i]['event_date_c']; ?></span></td>
                          <td><span>$<?php echo $leads[$i]['total_price_c']; ?></span></td>
                          <td>
                            <span class="badge bg-success/10 text-success"
                              ><?php echo $leads[$i]['status']; ?></span
                            >
                          </td>
                          <td>
                            <div class="btn-list">
                              <div
                                class="hs-tooltip ti-main-tooltip [--placement:top]"
                              >
                                <a
                                  aria-label="anchor"
                                  href="javascript:void(0);"
                                  class="hs-tooltip-toggle ti-btn ti-btn-icon !rounded-full me-2 ti-btn-soft-primary"
                                >
                                  <i class="ri-eye-line"></i>
                                 
                                </a>
                              </div>
                              <div
                                class="hs-tooltip ti-main-tooltip [--placement:top]"
                              >
                                <a
                                  aria-label="anchor"
                                  href="javascript:void(0);"
                                  class="hs-tooltip-toggle ti-btn ti-btn-icon !rounded-full me-2 ti-btn-soft-info"
                                >
                               <i class="ri-edit-line"></i>
                                
                                </a>
                              </div>
                              <div
                                class="hs-tooltip ti-main-tooltip [--placement:top]"
                              >
                                <a
                                  aria-label="anchor"
                                  href="javascript:void(0);"
                                  class="hs-tooltip-toggle ti-btn ti-btn-icon !rounded-full me-2 ti-btn-soft-primary2"
                                >
                                  <i class="ri-delete-bin-line"></i>
                                  
                                </a>
                              </div>
                            </div>
                          </td>
                        </tr>
                        <?php } ?>
                        
                      </tbody>
                    </table>
                  </div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script>
    $(document).ready(function() {
        $('#leads-table').DataTable({
            language: {
                searchPlaceholder: "Search...",
                sSearch: "",
            },
            lengthChange: false,
            pageLength: 10,
            scrollX: true
        });
    });
</script>