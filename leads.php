
<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<style>
  label{
    font-weight: 400;
  }
</style>
    
     <div class="main-content app-content"> 
        <div class="container-fluid">
             <!-- Page Header --> 
              <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
                 <div> 
                   
                    <h1 class="page-title font-medium text-lg mb-0">Leads</h1>
                </div> 
                
            </div>  
            <?php
                $data['id'] = $_SESSION['user']['id'];
                $leads = fetchAllUserLeads($data);
                // print_r($leads);

                if (!is_array($leads)) {
                  $leads = [];
                }

                $leadStats = [
                  'total' => count($leads),
                  'new' => 0,
                  'converted' => 0,
                  'dead' => 0,
                ];

                foreach ($leads as $lead) {
                  $statusRaw = $lead['status'] ?? '';
                  $status = strtolower(trim((string) $statusRaw));

                  if ($status === 'converted' || $status === 'won' || $status === 'success') {
                    $leadStats['converted']++;
                  } elseif ($status === 'dead' || $status === 'lost' || $status === 'closed' || $status === 'junk') {
                    $leadStats['dead']++;
                  } elseif ($status === 'new' || $status === 'open' || $status === 'new lead' || $status === 'new_lead') {
                    $leadStats['new']++;
                  }
                }

                $conversionRate = $leadStats['total'] > 0
                  ? round(($leadStats['converted'] / $leadStats['total']) * 100, 1)
                  : 0;
                ?>
                 <!-- Start::row-1 -->

            
          <div
            class="grid xl:grid-cols-5 lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-x-6"
            >
            <div class="">
              <div class="box crm-card">
                <div class="box-body">
                  <div class="">
                    <div class="flex justify-between mb-2">
                      <div
                        class="p-2 border border-primary/10 bg-primary/10 rounded-full"
                      >
                        <span
                          class="avatar avatar-md avatar-rounded bg-primary svg-white mb-0"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="32"
                            height="32"
                            fill="#000000"
                            viewBox="0 0 256 256"
                          >
                            <path
                              d="M224,200h-8V40a8,8,0,0,0-8-8H152a8,8,0,0,0-8,8V80H96a8,8,0,0,0-8,8v40H48a8,8,0,0,0-8,8v64H32a8,8,0,0,0,0,16H224a8,8,0,0,0,0-16ZM160,48h40V200H160ZM104,96h40V200H104ZM56,144H88v56H56Z"
                            ></path>
                          </svg>
                        </span>
                      </div>
                    </div>
                    <p
                      class="flex-auto text-textmuted dark:text-textmuted/50 text-[14px] mb-0"
                    >
                      Total Leads
                    </p>
                  </div>
                  <div class="flex items-center justify-between mt-1">
                    <h4 class="mb-0 flex items-center"><?php echo $leadStats['total']; ?></h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="">
              <div class="box crm-card">
                <div class="box-body">
                  <div class="">
                    <div class="flex justify-between mb-2">
                      <div
                        class="p-2 border border-primarytint1color/10 bg-primarytint1color/10 rounded-full"
                      >
                        <span
                          class="avatar avatar-rounded avatar-md bg-primarytint1color svg-white mb-0"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="32"
                            height="32"
                            fill="#000000"
                            viewBox="0 0 256 256"
                          >
                            <path
                              d="M205.66,61.64l-144,144a8,8,0,0,1-11.32-11.32l144-144a8,8,0,0,1,11.32,11.31ZM50.54,101.44a36,36,0,0,1,50.92-50.91h0a36,36,0,0,1-50.92,50.91ZM56,76A20,20,0,1,0,90.14,61.84h0A20,20,0,0,0,56,76ZM216,180a36,36,0,1,1-10.54-25.46h0A35.76,35.76,0,0,1,216,180Zm-16,0a20,20,0,1,0-5.86,14.14A19.87,19.87,0,0,0,200,180Z"
                            ></path>
                          </svg>
                        </span>
                      </div>
                    </div>
                    <p
                      class="flex-auto text-textmuted dark:text-textmuted/50 text-[14px] mb-0"
                    >
                      New Leads
                    </p>
                  </div>
                  <div class="flex items-center justify-between mt-1">
                    <h4 class="mb-0 flex items-center"><?php echo $leadStats['new']; ?></h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="">
              <div class="box crm-card">
                <div class="box-body">
                  <div class="">
                    <div class="flex justify-between mb-2">
                      <div
                        class="p-2 border border-primarytint2color/10 bg-primarytint2color/10 rounded-full"
                      >
                        <span
                          class="avatar avatar-rounded avatar-md bg-primarytint2color svg-white mb-0"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="32"
                            height="32"
                            fill="#000000"
                            viewBox="0 0 256 256"
                          >
                            <path
                              d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"
                            ></path>
                          </svg>
                        </span>
                      </div>
                    </div>
                    <p
                      class="flex-auto text-textmuted dark:text-textmuted/50 text-[14px] mb-0"
                    >
                      Converted Leads
                    </p>
                  </div>
                  <div class="flex items-center justify-between mt-1">
                    <h4 class="mb-0 flex items-center"><?php echo $leadStats['converted']; ?></h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="">
              <div class="box crm-card">
                <div class="box-body">
                  <div class="">
                    <div class="flex justify-between mb-2">
                      <div
                        class="p-2 border border-primarytint3color/10 bg-primarytint3color/10 rounded-full"
                      >
                        <span
                          class="avatar avatar-rounded avatar-md bg-primarytint3color svg-white mb-0"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="32"
                            height="32"
                            fill="#000000"
                            viewBox="0 0 256 256"
                          >
                            <path
                              d="M232,208a8,8,0,0,1-8,8H32a8,8,0,0,1-8-8V48a8,8,0,0,1,16,0V156.69l50.34-50.35a8,8,0,0,1,11.32,0L128,132.69,180.69,80H160a8,8,0,0,1,0-16h40a8,8,0,0,1,8,8v40a8,8,0,0,1-16,0V91.31l-58.34,58.35a8,8,0,0,1-11.32,0L96,123.31l-56,56V200H224A8,8,0,0,1,232,208Z"
                            ></path>
                          </svg>
                        </span>
                      </div>
                    </div>
                    <p
                      class="flex-auto text-textmuted dark:text-textmuted/50 text-[14px] mb-0"
                    >
                      Dead Leads
                    </p>
                  </div>
                  <div class="flex items-center justify-between mt-1">
                    <h4 class="mb-0 flex items-center"><?php echo $leadStats['dead']; ?></h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="">
              <div class="box crm-card">
                <div class="box-body">
                  <div class="">
                    <div class="flex justify-between mb-2">
                      <div
                        class="p-2 border border-secondary/10 bg-secondary/10 rounded-full"
                      >
                        <span
                          class="avatar avatar-rounded avatar-md bg-secondary svg-white mb-0"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="32"
                            height="32"
                            fill="#000000"
                            viewBox="0 0 256 256"
                          >
                            <path
                              d="M256,136a8,8,0,0,1-8,8H232v16a8,8,0,0,1-16,0V144H200a8,8,0,0,1,0-16h16V112a8,8,0,0,1,16,0v16h16A8,8,0,0,1,256,136Zm-57.87,58.85a8,8,0,0,1-12.26,10.3C165.75,181.19,138.09,168,108,168s-57.75,13.19-77.87,37.15a8,8,0,0,1-12.25-10.3c14.94-17.78,33.52-30.41,54.17-37.17a68,68,0,1,1,71.9,0C164.6,164.44,183.18,177.07,198.13,194.85ZM108,152a52,52,0,1,0-52-52A52.06,52.06,0,0,0,108,152Z"
                            ></path>
                          </svg>
                        </span>
                      </div>
                    </div>
                    <p
                      class="flex-auto text-textmuted dark:text-textmuted/50 text-[14px] mb-0"
                    >
                      Conversion Rate
                    </p>
                  </div>
                  <div class="flex items-center justify-between mt-1">
                    <h4 class="mb-0 flex items-center"><?php echo $conversionRate; ?>%</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End::row-1 -->
            <div class="grid grid-cols-12 gap-6"> 
                <div class="xl:col-span-12 col-span-12"> 
                  <div style="margin-bottom: 1rem;">
                    <div class="rounded-xl border border-defaultborder dark:border-defaultborder/10 bg-white dark:bg-bodybg p-3 mb-4">
                      <div class="flex lg:items-end gap-3 lg:gap-2">
                        <div class="flex-1" style="min-width: 300px;">
                          <label for="leads-filter-search" class="block text-xs text-textmuted dark:text-textmuted/50 mb-1">
                            Search
                          </label>
                          <input
                            id="leads-filter-search"
                            type="text"
                            placeholder="Search name, phone, email..."
                            class="w-full rounded-lg border border-defaultborder dark:border-defaultborder/10 bg-transparent px-3 py-2 text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 outline-none focus:border-primary focus:ring-2 focus:ring-primary/30"
                          />
                        </div>

                        <div class="flex  items-end gap-2 w-full lg:w-auto">
                          <div class="w-full sm:w-[220px] lg:w-[200px]">
                            <label for="leads-filter-status" class="block text-xs text-textmuted dark:text-textmuted/50 mb-1">
                              Status
                            </label>
                            <div class="relative">
                              <select
                                id="leads-filter-status"
                                class="appearance-none w-full rounded-lg border border-defaultborder dark:border-defaultborder/10 bg-transparent px-3 py-2 pr-9 text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 outline-none focus:border-primary focus:ring-2 focus:ring-primary/30"
                              >
                                <option value="">All Statuses</option>
                                <?php
                                  $statusSet = [];
                                  foreach ($leads as $lead) {
                                    $st = trim((string)($lead['status'] ?? ''));
                                    if ($st !== '') { $statusSet[$st] = true; }
                                  }
                                  $statuses = array_keys($statusSet);
                                  sort($statuses, SORT_NATURAL | SORT_FLAG_CASE);
                                  foreach ($statuses as $st) {
                                    echo '<option value="'.htmlspecialchars($st, ENT_QUOTES).'">'.htmlspecialchars($st).'</option>';
                                  }
                                ?>
                              </select>
                              <span class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-textmuted dark:text-textmuted/50" style="right: 0.75rem;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                  <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                              </span>
                            </div>
                          </div>

                          <div class="w-full sm:w-[200px] lg:w-[180px]">
                            <label for="leads-filter-event-from" class="block text-xs text-textmuted dark:text-textmuted/50 mb-1">
                              Event From
                            </label>
                            <input
                              id="leads-filter-event-from"
                              type="date"
                              class="w-full rounded-lg border border-defaultborder dark:border-defaultborder/10 bg-transparent px-3 py-2 text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 outline-none focus:border-primary focus:ring-2 focus:ring-primary/30"
                              aria-label="Event from"
                            />
                          </div>

                          <div class="w-full sm:w-[200px] lg:w-[180px]">
                            <label for="leads-filter-event-to" class="block text-xs text-textmuted dark:text-textmuted/50 mb-1">
                              Event To
                            </label>
                            <input
                              id="leads-filter-event-to"
                              type="date"
                              class="w-full rounded-lg border border-defaultborder dark:border-defaultborder/10 bg-transparent px-3 py-2 text-sm text-defaulttextcolor dark:text-defaulttextcolor/90 outline-none focus:border-primary focus:ring-2 focus:ring-primary/30"
                              aria-label="Event to"
                            />
                          </div>

                          <div class="w-full sm:w-auto">
                            <label class="block text-xs text-transparent mb-1 select-none">.</label>
                            <button
                              type="button"
                              id="leads-filter-clear-btn"
                              class="ti-btn ti-btn-soft-danger w-full sm:w-auto px-3 py-2 text-sm rounded-lg"
                            >
                              Clear
                            </button>
                          </div>
                        </div>
                    </div>
                  </div>
                   <?php include_once "components/tables/leads.php" ?>
                </div> 
            </div> 
        </div> 
    </div>

    <script>
      (function () {
        const $ = window.jQuery;
        if (!$) return;
        const clearBtn = document.getElementById("leads-filter-clear-btn");

        const searchInput = document.getElementById("leads-filter-search");
        const statusSelect = document.getElementById("leads-filter-status");
        const eventFrom = document.getElementById("leads-filter-event-from");
        const eventTo = document.getElementById("leads-filter-event-to");

        function parseDateToYmdNumber(dateStr) {
          if (!dateStr) return null;
          const s = String(dateStr).trim();

          // yyyy-mm-dd
          const iso = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
          if (iso) return Number(iso[1] + iso[2] + iso[3]);

          // mm/dd/yyyy or m/d/yyyy
          const us = s.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})/);
          if (us) {
            const mm = us[1].padStart(2, "0");
            const dd = us[2].padStart(2, "0");
            return Number(us[3] + mm + dd);
          }

          const d = new Date(s);
          if (!isNaN(d.getTime())) {
            const yyyy = String(d.getFullYear());
            const mm = String(d.getMonth() + 1).padStart(2, "0");
            const dd = String(d.getDate()).padStart(2, "0");
            return Number(yyyy + mm + dd);
          }

          return null;
        }

        function getTable() {
          if (window.leadsDataTable) return window.leadsDataTable;
          if ($.fn.dataTable && $.fn.dataTable.isDataTable && $.fn.dataTable.isDataTable("#leads-table")) {
            return $("#leads-table").DataTable();
          }
          return null;
        }

        function applyStatusFilter(table) {
          if (!table) return;
          const val = (statusSelect?.value || "").trim();
          // Status column index in table markup:
          // S.NO(0) Name(1) Phone(2) Pickup(3) Dropoff(4) Event Date(5)
          // Amount(6) Status(7) Created(8) Action(9)
          const statusColIdx = 7;
          if (!val) {
            table.column(statusColIdx).search("", false, false);
          } else {
            const escaped = val.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
            table.column(statusColIdx).search("^" + escaped + "$", true, false);
          }
        }

        function wireDateFilters() {
          // Register once
          if ($.fn.dataTable.ext.search.__leadsFiltersRegistered) return;
          $.fn.dataTable.ext.search.__leadsFiltersRegistered = true;

          $.fn.dataTable.ext.search.push(function (settings, data) {
            if (settings.nTable?.id !== "leads-table") return true;

            const eventDateText = data[5] || "";

            const fromVal = eventFrom?.value || "";
            const toVal = eventTo?.value || "";
            const fromNum = parseDateToYmdNumber(fromVal);
            const toNum = parseDateToYmdNumber(toVal);
            const rowNum = parseDateToYmdNumber(eventDateText);

            if (fromNum && rowNum && rowNum < fromNum) return false;
            if (toNum && rowNum && rowNum > toNum) return false;
            if ((fromNum || toNum) && !rowNum) return false;

            return true;
          });
        }

        function refresh() {
          const table = getTable();
          if (!table) return;
          applyStatusFilter(table);
          table.draw();
        }

        // Wiring
        wireDateFilters();

        // Use our search input for DataTables global search
        searchInput?.addEventListener("input", function () {
          const table = getTable();
          if (!table) return;
          table.search(this.value || "").draw();
        });

        statusSelect?.addEventListener("change", refresh);
        eventFrom?.addEventListener("change", refresh);
        eventTo?.addEventListener("change", refresh);

        clearBtn?.addEventListener("click", function () {
          if (searchInput) searchInput.value = "";
          if (statusSelect) statusSelect.value = "";
          if (eventFrom) eventFrom.value = "";
          if (eventTo) eventTo.value = "";

          const table = getTable();
          if (table) {
            table.search("");
            table.column(7).search("", false, false);
            table.draw();
          }
        });

        // In case DataTable initializes after this script, retry briefly
        let tries = 0;
        const t = setInterval(function () {
          tries++;
          const table = getTable();
          if (table || tries > 40) {
            clearInterval(t);
            if (table && searchInput?.value) table.search(searchInput.value).draw();
          }
        }, 100);
      })();
    </script>
    
      <?php include_once "components/layout/footer.php"; ?>