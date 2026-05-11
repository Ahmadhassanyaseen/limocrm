

<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>
<?php
if (!function_exists('limo_dash_norm_status')) {
    function limo_dash_norm_status($s): string
    {
        return strtolower(trim((string) $s));
    }
    function limo_dash_is_won($s): bool
    {
        $s = limo_dash_norm_status($s);

        return in_array($s, ['converted', 'won', 'success'], true);
    }
    function limo_dash_is_lost($s): bool
    {
        $s = limo_dash_norm_status($s);

        return in_array($s, ['dead', 'lost', 'closed', 'junk'], true);
    }
    function limo_dash_parse_ts($raw): ?int
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return null;
        }
        $t = strtotime($raw);

        return $t ? $t : null;
    }
    function limo_dash_initials(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return '?';
        }
        $parts = preg_split('/\s+/', $name) ?: [];
        $a = mb_substr($parts[0], 0, 1, 'UTF-8');
        $b = count($parts) > 1 ? mb_substr($parts[count($parts) - 1], 0, 1, 'UTF-8') : '';

        return strtoupper($a . $b);
    }
}

$data['id'] = $_SESSION['user']['id'];
if ((int) ($_SESSION['user']['admin'] ?? 0) === 1) {
    $leads = fetchAllLeads($data);
} else {
    $leads = fetchAllUserLeads($data);
}

if (!is_array($leads)) {
    $leads = [];
}

$dashYear = (int) date('Y');
$monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$createdByMonth = array_fill(1, 12, 0);
$wonByMonth = array_fill(1, 12, 0);
$openAddedByMonth = array_fill(1, 12, 0);

foreach ($leads as $lead) {
    $st = $lead['status'] ?? '';
    $tEnter = limo_dash_parse_ts($lead['date_entered'] ?? '');
    if ($tEnter && (int) date('Y', $tEnter) === $dashYear) {
        $m = (int) date('n', $tEnter);
        $createdByMonth[$m]++;
        if (!limo_dash_is_won($st) && !limo_dash_is_lost($st)) {
            $openAddedByMonth[$m]++;
        }
    }
    if (limo_dash_is_won($st)) {
        $tWin = limo_dash_parse_ts($lead['date_modified'] ?? '') ?: $tEnter;
        if ($tWin && (int) date('Y', $tWin) === $dashYear) {
            $wm = (int) date('n', $tWin);
            $wonByMonth[$wm]++;
        }
    }
}

$limoSalesOverviewSeries = [
    ['name' => 'Leads created', 'data' => []],
    ['name' => 'Converted (won)', 'data' => []],
    ['name' => 'Open pipeline (added)', 'data' => []],
];
for ($m = 1; $m <= 12; $m++) {
    $limoSalesOverviewSeries[0]['data'][] = $createdByMonth[$m];
    $limoSalesOverviewSeries[1]['data'][] = $wonByMonth[$m];
    $limoSalesOverviewSeries[2]['data'][] = $openAddedByMonth[$m];
}

$limoSalesOverviewPayload = [
    'categories' => $monthLabels,
    'series' => $limoSalesOverviewSeries,
    'year' => $dashYear,
];

$byRep = [];
foreach ($leads as $lead) {
    $aname = trim((string) ($lead['assigned_user_name'] ?? ''));
    $aid = trim((string) ($lead['assigned_user_id'] ?? ''));
    if ($aname === '' && $aid === '') {
        $rkey = '__unassigned__';
        $disp = 'Unassigned';
    } else {
        $rkey = $aid !== '' ? $aid : ('n:' . md5($aname));
        $disp = $aname !== '' ? $aname : ('User ' . substr($aid, 0, 8));
    }
    if (!isset($byRep[$rkey])) {
        $byRep[$rkey] = ['name' => $disp, 'total' => 0, 'closed' => 0];
    }
    $byRep[$rkey]['total']++;
    if (limo_dash_is_won($lead['status'] ?? '')) {
        $byRep[$rkey]['closed']++;
    }
}
uasort($byRep, static function ($a, $b) {
    return $b['total'] <=> $a['total'];
});
$dashSalesReps = array_values($byRep);
$dashSalesReps = array_slice($dashSalesReps, 0, 15);
$dashTeamLeads = array_sum(array_column($dashSalesReps, 'total'));
$dashTeamClosed = array_sum(array_column($dashSalesReps, 'closed'));
$dashTeamRate = $dashTeamLeads > 0 ? round(100 * $dashTeamClosed / $dashTeamLeads, 2) : 0.0;
$dashIsAdmin = (int) ($_SESSION['user']['admin'] ?? 0) === 1;
$dashSalesRepFacePool = [11, 12, 14, 15];

$leadStats = [
  'total'     => count($leads),
  'new'       => 0,
  'converted' => 0,
  'formal'    => 0,
  'revenue'   => 0.0,
];

foreach ($leads as $lead) {
  $statusRaw = $lead['status'] ?? '';
  $status = strtolower(trim((string) $statusRaw));

  if ($status === 'converted' || $status === 'won' || $status === 'success') {
    $leadStats['converted']++;
    $leadStats['revenue'] += (float) ($lead['total_price_c'] ?? 0);
  } elseif ($status === 'formal' || $status === 'formal quote' || $status === 'quote sent') {
    $leadStats['formal']++;
  } elseif ($status === 'new' || $status === 'open' || $status === 'new lead' || $status === 'new_lead') {
    $leadStats['new']++;
  }
}

$leadShare = function ($n) use ($leadStats) {
  if ($leadStats['total'] <= 0) return '0%';
  return round(($n / $leadStats['total']) * 100, 1) . '%';
};

$formatRevenue = function ($n) {
  $n = (float) $n;
  if ($n >= 1000000) return '$' . number_format($n / 1000000, 1) . 'M';
  if ($n >= 1000)    return '$' . number_format($n / 1000, 1)    . 'K';
  return '$' . number_format($n, 0);
};
?>
    
      <!-- Start::app-content -->
      <div class="main-content app-content">
        <div class="container-fluid">
          <!-- Start::page-header -->
          <div
            class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2"
            >
            <div>
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item">
                    <a href="javascript:void(0);"> Dashboards </a>
                  </li>
                  <li class="breadcrumb-item active" aria-current="page">
                    CRM
                  </li>
                </ol>
              </nav>
              <h1 class="page-title font-medium text-lg mb-0">CRM</h1>
            </div>
            <div class="btn-list">
              <button
                type="button"
                class="ti-btn bg-white dark:bg-bodybg border border-defaultborder dark:border-defaultborder/10 btn-wave !my-0 waves-effect waves-light"
              >
                <i class="ri-filter-3-line align-middle me-1 leading-none"></i>
                Filter
              </button>
              <button
                type="button"
                class="ti-btn ti-btn-primary !border-0 btn-wave me-0 waves-effect waves-light"
              >
                <i class="ri-share-forward-line me-1"></i> Share
              </button>
            </div>
          </div>
          <!-- End::page-header -->
          <!-- Start::row-1 -->
          <div
            class="grid xl:grid-cols-5 lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-x-6"
            >
            <!-- All Leads -->
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
                      All Leads
                    </p>
                  </div>
                  <div class="flex items-center justify-between mt-1">
                    <h4 class="mb-0 flex items-center"><?php echo number_format($leadStats['total']); ?></h4>
                    <span
                      class="text-primary badge bg-primary/10 rounded-full flex items-center text-[11px] me-0 ms-2 mb-0"
                    >100%</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Converted -->
            <div class="">
              <div class="box crm-card">
                <div class="box-body">
                  <div class="">
                    <div class="flex justify-between mb-2">
                      <div
                        class="p-2 border border-success/10 bg-success/10 rounded-full"
                      >
                        <span
                          class="avatar avatar-rounded avatar-md bg-success svg-white mb-0"
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
                      Converted
                    </p>
                  </div>
                  <div class="flex items-center justify-between mt-1">
                    <h4 class="mb-0 flex items-center"><?php echo number_format($leadStats['converted']); ?></h4>
                    <span
                      class="text-success badge bg-success/10 rounded-full flex items-center text-[11px] me-0 ms-2 mb-0"
                    ><?php echo $leadShare($leadStats['converted']); ?></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- New -->
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
                              d="M256,136a8,8,0,0,1-8,8H232v16a8,8,0,0,1-16,0V144H200a8,8,0,0,1,0-16h16V112a8,8,0,0,1,16,0v16h16A8,8,0,0,1,256,136Zm-57.87,58.85a8,8,0,0,1-12.26,10.3C165.75,181.19,138.09,168,108,168s-57.75,13.19-77.87,37.15a8,8,0,0,1-12.25-10.3c14.94-17.78,33.52-30.41,54.17-37.17a68,68,0,1,1,71.9,0C164.6,164.44,183.18,177.07,198.13,194.85ZM108,152a52,52,0,1,0-52-52A52.06,52.06,0,0,0,108,152Z"
                            ></path>
                          </svg>
                        </span>
                      </div>
                    </div>
                    <p
                      class="flex-auto text-textmuted dark:text-textmuted/50 text-[14px] mb-0"
                    >
                      New
                    </p>
                  </div>
                  <div class="flex items-center justify-between mt-1">
                    <h4 class="mb-0 flex items-center"><?php echo number_format($leadStats['new']); ?></h4>
                    <span
                      class="text-primarytint1color badge bg-primarytint1color/10 rounded-full flex items-center text-[11px] me-0 ms-2 mb-0"
                    ><?php echo $leadShare($leadStats['new']); ?></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Formal -->
            <div class="">
              <div class="box crm-card">
                <div class="box-body">
                  <div class="">
                    <div class="flex justify-between mb-2">
                      <div
                        class="p-2 border border-warning/10 bg-warning/10 rounded-full"
                      >
                        <span
                          class="avatar avatar-rounded avatar-md bg-warning svg-white mb-0"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="32"
                            height="32"
                            fill="#000000"
                            viewBox="0 0 256 256"
                          >
                            <path
                              d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM160,51.31,188.69,80H160ZM200,216H56V40h88V88a8,8,0,0,0,8,8h48V216ZM88,136a8,8,0,0,1,8-8h64a8,8,0,0,1,0,16H96A8,8,0,0,1,88,136Zm0,32a8,8,0,0,1,8-8h64a8,8,0,0,1,0,16H96A8,8,0,0,1,88,168Z"
                            ></path>
                          </svg>
                        </span>
                      </div>
                    </div>
                    <p
                      class="flex-auto text-textmuted dark:text-textmuted/50 text-[14px] mb-0"
                    >
                      Formal
                    </p>
                  </div>
                  <div class="flex items-center justify-between mt-1">
                    <h4 class="mb-0 flex items-center"><?php echo number_format($leadStats['formal']); ?></h4>
                    <span
                      class="text-warning badge bg-warning/10 rounded-full flex items-center text-[11px] me-0 ms-2 mb-0"
                    ><?php echo $leadShare($leadStats['formal']); ?></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Total Revenue -->
            <div class="">
              <div class="box crm-card">
                <div class="box-body">
                  <div class="">
                    <div class="flex justify-between mb-2">
                      <div
                        class="p-2 border border-success/10 bg-success/10 rounded-full"
                      >
                        <span
                          class="avatar avatar-rounded avatar-md bg-success svg-white mb-0"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="32"
                            height="32"
                            fill="#000000"
                            viewBox="0 0 256 256"
                          >
                            <path
                              d="M152,120H136V56h8a32,32,0,0,1,32,32,8,8,0,0,0,16,0,48.05,48.05,0,0,0-48-48h-8V24a8,8,0,0,0-16,0V40H112a48,48,0,0,0,0,96h8v64H104a32,32,0,0,1-32-32,8,8,0,0,0-16,0,48.05,48.05,0,0,0,48,48h16v16a8,8,0,0,0,16,0V216h16a48,48,0,0,0,0-96Zm-40,0a32,32,0,0,1,0-64h8v64Zm40,80H136V136h16a32,32,0,0,1,0,64Z"
                            ></path>
                          </svg>
                        </span>
                      </div>
                    </div>
                    <p
                      class="flex-auto text-textmuted dark:text-textmuted/50 text-[14px] mb-0"
                    >
                      Total Revenue
                    </p>
                  </div>
                  <div class="flex items-center justify-between mt-1">
                    <h4 class="mb-0 flex items-center"><?php echo $formatRevenue($leadStats['revenue']); ?></h4>
                    <span
                      class="text-success badge bg-success/10 rounded-full flex items-center text-[11px] me-0 ms-2 mb-0"
                      title="From <?php echo number_format($leadStats['converted']); ?> converted lead<?php echo $leadStats['converted'] === 1 ? '' : 's'; ?>"
                    ><?php echo number_format($leadStats['converted']); ?> won</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End::row-1 -->
          <!-- Start::row-2 -->
          <div class="grid grid-cols-12 gap-x-6">
            
            <div class="<?php echo $dashIsAdmin ? 'col-span-12 md:col-span-12 xxl:col-span-6' : 'col-span-12'; ?>">
              <div class="box">
                <div class="box-header justify-between">
                  <div class="box-title">Sales Overview</div>
                  <span class="text-xs font-medium text-textmuted dark:text-textmuted/50 border border-defaultborder dark:border-defaultborder/10 rounded-full px-3 py-1">
                    Jan–Dec <?php echo (int) $dashYear; ?>
                  </span>
                </div>
                <div class="box-body">
                  <p class="text-xs text-textmuted dark:text-textmuted/50 mb-3">
                    Monthly lead counts for <?php echo (int) $dashYear; ?> from
                    <?php echo (int) ($_SESSION['user']['admin'] ?? 0) === 1 ? 'all leads' : 'your visible leads'; ?>
                    (<?php echo number_format(count($leads)); ?> total).
                  </p>
                  <div
                    id="sales-overview-crm"
                    style="min-height: 285px"
                    class=""
                    >
                  
                  </div>
                  <script type="application/json" id="limo-dashboard-sales-json"><?php echo json_encode($limoSalesOverviewPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?></script>
                </div>
              </div>
            </div>
            <?php if ($dashIsAdmin): ?>
            <div class="col-span-12 md:col-span-12 xxl:col-span-6">
              <div class="box overflow-hidden">
                <div class="box-header justify-between">
                  <div class="box-title">Sales Performance</div>
                  <span class="text-xs font-medium text-textmuted dark:text-textmuted/50 border border-defaultborder dark:border-defaultborder/10 rounded-full px-3 py-1">
                    By assignee
                  </span>
                </div>
                <div class="box-body p-0">
                  <div class="table-responsive">
                    <table class="ti-custom-table ti-custom-table-head">
                      <thead>
                        <tr
                          class="border-b border-defaultborder dark:border-defaultborder/10"
                        >
                          <th scope="col">S.No.</th>
                          <th scope="col">Representative</th>
                          <th scope="col">Deals Closed</th>
                          <th scope="col">Leads</th>
                          <th scope="col">Rate (%)</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (count($dashSalesReps) === 0): ?>
                        <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                          <td colspan="5" class="text-center text-textmuted dark:text-textmuted/50 py-8">
                            No leads in view yet. Assign leads to teammates to see performance here.
                          </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($dashSalesReps as $idx => $rep): ?>
                        <?php
                          $closed = (int) $rep['closed'];
                          $total = (int) $rep['total'];
                          $rate = $total > 0 ? round(100 * $closed / $total, 1) : 0.0;
                          $trendUp = $dashTeamLeads === 0
                            ? ($closed > 0)
                            : ($rate + 0.001 >= $dashTeamRate);
                          $dashFaceId = $dashSalesRepFacePool[abs(crc32((string) ($rep['name'] ?? '') . "\0" . (string) $idx)) % count($dashSalesRepFacePool)];
                          ?>
                        <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                          <td><?php echo $idx + 1; ?></td>
                          <td>
                            <div class="flex items-center">
                              <div class="me-2 leading-none">
                                <span class="avatar avatar-xs avatar-rounded overflow-hidden ring-1 ring-defaultborder dark:ring-defaultborder/20">
                                  <img src="assets/images/faces/<?php echo (int) $dashFaceId; ?>.jpg" alt="" class="w-full h-full object-cover" width="32" height="32" />
                                </span>
                              </div>
                              <div>
                                <span class="font-medium"><?php echo htmlspecialchars($rep['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                              </div>
                            </div>
                          </td>
                          <td><?php echo number_format($closed); ?></td>
                          <td><?php echo number_format($total); ?></td>
                          <td>
                            <?php echo htmlspecialchars((string) $rate, ENT_QUOTES, 'UTF-8'); ?>
                            <?php if ($trendUp): ?>
                            <i class="ri-arrow-up-s-fill ms-1 text-success align-middle text-lg" title="At or above team win rate (<?php echo htmlspecialchars((string) $dashTeamRate, ENT_QUOTES, 'UTF-8'); ?>%)"></i>
                            <?php else: ?>
                            <i class="ri-arrow-down-s-fill ms-1 text-danger align-middle text-lg" title="Below team win rate (<?php echo htmlspecialchars((string) $dashTeamRate, ENT_QUOTES, 'UTF-8'); ?>%)"></i>
                            <?php endif; ?>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <?php endif; ?>
            
          </div>
          <!-- End::row-2 -->
         
          <!-- Start::row-4 -->
          <div class="grid grid-cols-12 gap-x-6">
            <div class="xl:col-span-12 col-span-12">
              <div class="box overflow-hidden">
                <div class="box-header justify-between">
                  <div class="box-title">Leads Report</div>
                  <div class="ti-dropdown hs-dropdown">
                    <div
                      class="ti-btn ti-btn-light border btn-full ti-btn-sm"
                      data-bs-toggle="dropdown"
                    >
                      View All<i class="ri-arrow-down-s-line ms-1"></i>
                    </div>
                    <ul
                      class="ti-dropdown-menu hs-dropdown-menu hidden"
                      role="menu"
                    >
                      <li>
                        <a class="ti-dropdown-item" href="javascript:void(0);"
                          >Download</a
                        >
                      </li>
                      <li>
                        <a class="ti-dropdown-item" href="javascript:void(0);"
                          >Import</a
                        >
                      </li>
                      <li>
                        <a class="ti-dropdown-item" href="javascript:void(0);"
                          >Export</a
                        >
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="box-body active-tab">
               <?php include_once "components/tables/leads.php" ?>
                </div>
                
              </div>
            </div>
          </div>
          <div class="tester">

          <div class="col">

          
          </div>
                                          </div>











































































          </div>
          <!-- End::row-4 -->
        </div>
      </div>
    
      <?php include_once "components/layout/footer.php"; ?>