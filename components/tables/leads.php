<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
<style>
  .leads-table-wrap {
    --lt-surface: #ffffff;
    --lt-surface-2: #f8fafc;
    --lt-border: rgba(15, 23, 42, 0.12);
    --lt-text: #0f172a;
    --lt-muted: rgba(15, 23, 42, 0.6);
    --lt-muted-2: rgba(15, 23, 42, 0.42);
    --lt-accent: #cf1c82;
    --lt-accent-2: rgba(207, 28, 130, 0.12);
    --lt-row-hover: rgba(15, 23, 42, 0.04);
    --lt-focus: rgba(207, 28, 130, 0.35);

    background: var(--lt-surface);
    border: 1px solid var(--lt-border);
    border-radius: 12px;
    overflow: hidden;
  }

  .dark .leads-table-wrap {
    --lt-surface: #0b1220;
    --lt-surface-2: rgba(255, 255, 255, 0.04);
    --lt-border: rgba(255, 255, 255, 0.08);
    --lt-text: rgba(255, 255, 255, 0.92);
    --lt-muted: rgba(255, 255, 255, 0.62);
    --lt-muted-2: rgba(255, 255, 255, 0.45);
    --lt-accent: #cf1c82;
    --lt-accent-2: rgba(207, 28, 130, 0.14);
    --lt-row-hover: rgba(255, 255, 255, 0.04);
    --lt-focus: rgba(207, 28, 130, 0.42);
  }

  /* DataTables controls (search/paging/info) */
  .leads-table-wrap div.dt-container {
    padding: 0;
  }

  .leads-table-wrap div.dt-container .dt-search {
    display: none;
  }

  .leads-table-wrap div.dt-container .dt-info {
    color: var(--lt-muted);
    padding: 12px 14px;
    font-size: 12px;
  }

  .leads-table-wrap div.dt-container .dt-paging {
    padding: 10px 14px;
  }

  .leads-table-wrap div.dt-container .dt-paging .dt-paging-button {
    border: 1px solid var(--lt-border) !important;
    background: transparent !important;
    color: var(--lt-text) !important;
    border-radius: 10px !important;
    margin: 0 3px !important;
    padding: 6px 10px !important;
  }

  .leads-table-wrap div.dt-container .dt-paging .dt-paging-button:hover {
    background: var(--lt-surface-2) !important;
    color: var(--lt-text) !important;
  }

  .leads-table-wrap div.dt-container .dt-paging .dt-paging-button.current,
  .leads-table-wrap div.dt-container .dt-paging .dt-paging-button.current:hover {
    background: var(--lt-accent) !important;
    color: #fff !important;
    border-color: var(--lt-accent) !important;
  }

  .leads-table-wrap div.dt-container .dt-paging .dt-paging-button.disabled,
  .leads-table-wrap div.dt-container .dt-paging .dt-paging-button.disabled:hover {
    opacity: 0.55;
    cursor: not-allowed !important;
    background: transparent !important;
  }

  /* Table shell */
  .leads-table-wrap table.dataTable {
    border-collapse: separate !important;
    border-spacing: 0;
    width: 100%;
    color: var(--lt-text);
  }

  .leads-table-wrap table.dataTable thead th {
    background: var(--lt-surface-2) !important;
    color: var(--lt-muted);
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    border-bottom: 1px solid var(--lt-border) !important;
    padding: 12px 12px !important;
  }

  .leads-table-wrap table.dataTable tbody td {
    border-bottom: 1px solid var(--lt-border);
    padding: 12px 12px !important;
    vertical-align: middle;
  }

  .leads-table-wrap table.dataTable tbody tr:hover td {
    background: var(--lt-row-hover);
  }

  .leads-table-wrap table.dataTable tbody tr[data-lead-id] {
    cursor: pointer;
  }

  /* Keep action buttons aligned like the reference */
  .leads-table-wrap .btn-list {
    display: inline-flex;
    gap: 8px;
    align-items: center;
    white-space: nowrap;
  }

  /* Horizontal scroll via parent wrapper */
  .leads-table-wrap.has-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .leads-table-wrap table.dataTable thead th,
  .leads-table-wrap table.dataTable tbody td {
    white-space: nowrap;
  }

  /* Hide DataTables sort icons */
  .leads-table-wrap table.dataTable thead th.dt-orderable-asc span.dt-column-order,
  .leads-table-wrap table.dataTable thead th.dt-orderable-desc span.dt-column-order,
  .leads-table-wrap table.dataTable thead th .dt-column-order {
    display: none !important;
  }
  .leads-table-wrap table.dataTable thead th {
    cursor: default !important;
  }

  /* Subtle two-line "Created" cell. */
  .leads-table-wrap .lt-created {
    line-height: 1.15;
  }

  .leads-table-wrap .lt-created .lt-created-date {
    font-size: 13px;
    color: var(--lt-text);
    font-weight: 500;
  }

  .leads-table-wrap .lt-created .lt-created-time {
    font-size: 11px;
    color: var(--lt-muted-2);
    margin-top: 2px;
  }

</style>
<div class="table-responsive table-bordered-default leads-table-wrap has-scroll">
                    <table id="leads-table" class="ti-custom-table text-nowrap" style="width:100%">
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
                          <th scope="col">Source</th>
                          <th scope="col">Assigned To</th>
                          <th scope="col">Created</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i=0; $i < count($leads); $i++) { ?>
                        <tr class="border-b border-defaultborder dark:border-defaultborder/10" data-lead-id="<?php echo $leads[$i]['id']; ?>">
                          <td><span class=""><?php echo $i+1; ?></span></td>
                          <td>
                            <div class="flex">
                              
                              <div class="flex-1 ms-2">
                                <p class="mb-0 text-[14px]"><?php echo $leads[$i]['first_name']; ?> <?php echo $leads[$i]['last_name']; ?></p>
                                <a
                                  href="javascript:void(0);"
                                  class="text-textmuted dark:text-textmuted/50 text-xs"
                                  ><?php echo $leads[$i]['email1']; ?></a
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
                            <?php $leadSource = trim((string)($leads[$i]['lead_source'] ?? '')); ?>
                            <?php if ($leadSource !== ''): ?>
                              <span class="badge bg-info/10 text-info" style="font-size:11px;"><i class="ri-global-line" style="font-size:10px;margin-right:2px;"></i><?php echo htmlspecialchars($leadSource); ?></span>
                            <?php else: ?>
                              <span class="text-textmuted dark:text-textmuted/50">—</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php $assignedUserName = trim((string)($leads[$i]['assigned_user_name'] ?? '')); ?>
                            <?php if ($assignedUserName !== ''): ?>
                              <span class="badge bg-secondary/10 text-secondary" style="font-size:11px;"><i class="ri-user-star-line" style="font-size:10px;margin-right:2px;"></i><?php echo htmlspecialchars($assignedUserName); ?></span>
                            <?php else: ?>
                              <span class="text-textmuted dark:text-textmuted/50">—</span>
                            <?php endif; ?>
                          </td>
                          <?php
                            $createdRaw = (string)($leads[$i]['date_entered'] ?? '');
                            $createdTs  = $createdRaw !== '' ? strtotime($createdRaw) : false;
                          ?>
                          <td data-order="<?php echo $createdTs ? (int)$createdTs : 0; ?>">
                            <?php if ($createdTs): ?>
                              <div class="lt-created">
                                <div class="lt-created-date"><?php echo date('M j, Y', $createdTs); ?></div>
                                <div class="lt-created-time"><?php echo date('g:i A', $createdTs); ?></div>
                              </div>
                            <?php else: ?>
                              <span class="text-textmuted dark:text-textmuted/50">—</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <div class="btn-list">
                              <div
                                class="hs-tooltip ti-main-tooltip [--placement:top]"
                              >
                                <a
                                  aria-label="anchor"
                                  href="lead.php?id=<?php echo $leads[$i]['id']; ?>"
                                  class="hs-tooltip-toggle ti-btn ti-btn-icon !rounded-full me-2 ti-btn-soft-primary"
                                >
                                  <i class="ri-eye-line"></i>
                                 
                                </a>
                              </div>
                              <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Leads', 'update') == 1): ?>
                              <div
                                class="hs-tooltip ti-main-tooltip [--placement:top]"
                              >
                                <a
                                  aria-label="anchor"
                                  href="edit_lead.php?id=<?php echo $leads[$i]['id']; ?>"
                                  class="hs-tooltip-toggle ti-btn ti-btn-icon !rounded-full me-2 ti-btn-soft-info"
                                >
                               <i class="ri-edit-line"></i>
                                
                                </a>
                              </div>
                              <?php endif; ?>
                              <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Leads', 'delete') == 1): ?>
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
                              <?php endif; ?>
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
        // 0 S.NO | 1 Name | 2 Phone | 3 Pickup | 4 Dropoff | 5 Event Date
        // 6 Amount | 7 Status | 8 Source | 9 Assigned To | 10 Created | 11 Action
        const CREATED_COL = 10;
        const ACTION_COL  = 11;

        const table = $('#leads-table').DataTable({
            language: {
                searchPlaceholder: "Search...",
                sSearch: "",
            },
            lengthChange: false,
            pageLength: 10,
            autoWidth: false,
            order: [[CREATED_COL, 'desc']],
            columnDefs: [
                { orderable: false, targets: [ACTION_COL] },
                { type: 'num',      targets: [CREATED_COL] }
            ],
            dom: 'rt<"dt-bottom flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2"ip>'
        });

        window.leadsDataTable = table;

        $('#leads-table tbody').on('click', 'tr[data-lead-id]', function (e) {
            if ($(e.target).closest('a, button, .btn-list').length) return;
            window.location.href = 'lead.php?id=' + $(this).data('lead-id');
        });
    });
</script>