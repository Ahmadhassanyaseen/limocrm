<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$data['id'] = $_SESSION['user']['id'];
$leads = fetchAllUserLeads($data);
if (!is_array($leads)) { $leads = []; }

$US_STATES = [
  'Alabama'=>'AL','Alaska'=>'AK','Arizona'=>'AZ','Arkansas'=>'AR','California'=>'CA',
  'Colorado'=>'CO','Connecticut'=>'CT','Delaware'=>'DE','Florida'=>'FL','Georgia'=>'GA',
  'Hawaii'=>'HI','Idaho'=>'ID','Illinois'=>'IL','Indiana'=>'IN','Iowa'=>'IA',
  'Kansas'=>'KS','Kentucky'=>'KY','Louisiana'=>'LA','Maine'=>'ME','Maryland'=>'MD',
  'Massachusetts'=>'MA','Michigan'=>'MI','Minnesota'=>'MN','Mississippi'=>'MS','Missouri'=>'MO',
  'Montana'=>'MT','Nebraska'=>'NE','Nevada'=>'NV','New Hampshire'=>'NH','New Jersey'=>'NJ',
  'New Mexico'=>'NM','New York'=>'NY','North Carolina'=>'NC','North Dakota'=>'ND','Ohio'=>'OH',
  'Oklahoma'=>'OK','Oregon'=>'OR','Pennsylvania'=>'PA','Rhode Island'=>'RI','South Carolina'=>'SC',
  'South Dakota'=>'SD','Tennessee'=>'TN','Texas'=>'TX','Utah'=>'UT','Vermont'=>'VT',
  'Virginia'=>'VA','Washington'=>'WA','West Virginia'=>'WV','Wisconsin'=>'WI','Wyoming'=>'WY',
  'District of Columbia'=>'DC'
];
$STATE_ABBR_FLIP = array_flip($US_STATES);

function ra_parse_state(string $addr, array $states, array $abbrFlip): string {
  if ($addr === '') return '';
  foreach ($states as $name => $abbr) {
    if (stripos($addr, $name) !== false) return $name;
  }
  if (preg_match('/\b([A-Z]{2})\b/', $addr, $m)) {
    if (isset($abbrFlip[$m[1]])) return $abbrFlip[$m[1]];
  }
  return '';
}

function ra_float($v): float {
  if ($v === null || $v === '') return 0.0;
  return is_numeric($v) ? (float)$v : (float)preg_replace('/[^0-9.\-]/', '', (string)$v);
}

$now = new DateTime();
$monthStart = (clone $now)->modify('first day of this month')->setTime(0,0);
$lastMonthStart = (clone $now)->modify('first day of last month')->setTime(0,0);
$lastMonthEnd = (clone $now)->modify('last day of last month')->setTime(23,59,59);
$quarterMonth = (int)$now->format('n');
$quarterStart = (clone $now)->setDate((int)$now->format('Y'), (int)(floor(($quarterMonth-1)/3)*3+1), 1)->setTime(0,0);
$yearStart = (clone $now)->setDate((int)$now->format('Y'), 1, 1)->setTime(0,0);

$pnl = ['this_month'=>[],'last_month'=>[],'this_quarter'=>[],'this_year'=>[],'all_time'=>[]];
$trendData = [];
$geoData = [];
$funnelData = ['total'=>0,'assigned'=>0,'quoted'=>0,'converted'=>0];
$eventPnl = [];

for ($i = 23; $i >= 0; $i--) {
  $d = (clone $now)->modify("-{$i} months");
  $key = $d->format('Y-m');
  $trendData[$key] = 0.0;
}

$trendDaily90 = [];
for ($i = 89; $i >= 0; $i--) {
  $d = (clone $now)->modify("-{$i} days")->setTime(0, 0);
  $trendDaily90[$d->format('Y-m-d')] = 0.0;
}

foreach ($leads as $lead) {
  $status = strtolower(trim((string)($lead['status'] ?? '')));
  $revenue = ra_float($lead['total_price_c'] ?? 0);
  $fuel = ra_float($lead['fuel_c'] ?? 0);
  $commission = ra_float($lead['driver_commission_c'] ?? 0);
  $cost = $fuel + $commission;
  $dateStr = (string)($lead['date_entered'] ?? '');
  $dateObj = $dateStr ? new DateTime($dateStr) : null;
  $serviceType = trim((string)($lead['service_type_c'] ?? ''));
  if ($serviceType === '') $serviceType = 'Other';
  $pickupAddr = (string)($lead['pickup_address_c'] ?? '');

  $funnelData['total']++;
  if (in_array($status, ['assigned','in process','in_process'])) $funnelData['assigned']++;
  if ($revenue > 0) $funnelData['quoted']++;
  if (in_array($status, ['converted','won','success'])) $funnelData['converted']++;

  $state = ra_parse_state($pickupAddr, $US_STATES, $STATE_ABBR_FLIP);
  if ($state !== '') {
    if (!isset($geoData[$state])) $geoData[$state] = ['leads'=>0,'revenue'=>0.0];
    $geoData[$state]['leads']++;
    $geoData[$state]['revenue'] += $revenue;
  }

  if ($dateObj) {
    $ym = $dateObj->format('Y-m');
    if (isset($trendData[$ym])) {
      $trendData[$ym] += $revenue;
    }
    $day = $dateObj->format('Y-m-d');
    if (isset($trendDaily90[$day])) {
      $trendDaily90[$day] += $revenue;
    }
  }

  $isConverted = in_array($status, ['converted','won','success']);

  if ($isConverted) {
    $periods = ['all_time' => true];
    if ($dateObj) {
      if ($dateObj >= $monthStart) $periods['this_month'] = true;
      if ($dateObj >= $lastMonthStart && $dateObj <= $lastMonthEnd) $periods['last_month'] = true;
      if ($dateObj >= $quarterStart) $periods['this_quarter'] = true;
      if ($dateObj >= $yearStart) $periods['this_year'] = true;
    }

    foreach ($periods as $period => $_) {
      if (!isset($pnl[$period]['revenue'])) {
        $pnl[$period] = ['revenue'=>0,'cost'=>0,'deals'=>0];
      }
      $pnl[$period]['revenue'] += $revenue;
      $pnl[$period]['cost'] += $cost;
      $pnl[$period]['deals']++;
    }
  }

  if (!isset($eventPnl[$serviceType])) {
    $eventPnl[$serviceType] = ['deals'=>0,'revenue'=>0.0,'cost'=>0.0];
  }
  $eventPnl[$serviceType]['deals']++;
  $eventPnl[$serviceType]['revenue'] += $revenue;
  $eventPnl[$serviceType]['cost'] += $cost;
}

uasort($geoData, function($a, $b) { return $b['leads'] <=> $a['leads']; });
$geoData = array_slice($geoData, 0, 25, true);

$trendDailyMonth = [];
$walkDaily = clone $monthStart;
$endDaily = (clone $now)->setTime(0, 0);
while ($walkDaily <= $endDaily) {
  $dk = $walkDaily->format('Y-m-d');
  $trendDailyMonth[$dk] = $trendDaily90[$dk] ?? 0.0;
  $walkDaily->modify('+1 day');
}

uasort($eventPnl, function($a, $b) { return $b['revenue'] <=> $a['revenue']; });

$pnlJson = json_encode($pnl);
$trendLabels = json_encode(array_keys($trendData));
$trendValues = json_encode(array_values($trendData));
$trendDaily90Labels = json_encode(array_keys($trendDaily90));
$trendDaily90Values = json_encode(array_values($trendDaily90));
$trendDailyMonthLabels = json_encode(array_keys($trendDailyMonth));
$trendDailyMonthValues = json_encode(array_values($trendDailyMonth));

$funnelData['assigned'] += $funnelData['converted'];
$funnelData['quoted'] = max($funnelData['quoted'], $funnelData['converted']);
?>

<style>
  .ra-page { --ra-surface: #ffffff; --ra-surface-2: #f8fafc; --ra-border: rgba(15,23,42,0.08); --ra-text: #0f172a; --ra-muted: rgba(15,23,42,0.55); }
  .dark .ra-page { --ra-surface: rgba(255,255,255,0.035); --ra-surface-2: rgba(255,255,255,0.05); --ra-border: rgba(255,255,255,0.08); --ra-text: rgba(255,255,255,0.92); --ra-muted: rgba(255,255,255,0.50); }

  .ra-card { background: var(--ra-surface); border: 1px solid var(--ra-border); border-radius: 16px; overflow: hidden; transition: box-shadow 0.2s; }
  .ra-card:hover { box-shadow: 0 4px 24px rgba(15,23,42,0.06); }
  .dark .ra-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.25); }
  .ra-card-header { background: rgba(15,23,42,0.025); border-bottom: 1px solid rgba(15,23,42,0.08); padding: 16px 24px; }
  .dark .ra-card-header { background: rgba(255,255,255,0.025); border-bottom-color: rgba(255,255,255,0.08); }
  .ra-card-body { padding: 24px; }
  .ra-card-icon { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }

  .ra-filter-btn { height: 34px; border-radius: 8px; border: 1px solid var(--ra-border); background: transparent; color: var(--ra-muted); padding: 0 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
  .ra-filter-btn:hover, .ra-filter-btn.active { background: rgba(var(--primary-rgb), 0.06); color: rgb(var(--primary-rgb)); border-color: rgba(var(--primary-rgb), 0.20); }

  /* P&L Hero */
  .ra-pnl-hero { text-align: center; padding: 28px 24px 20px; position: relative; }
  .ra-pnl-hero::after { content: ''; position: absolute; bottom: 0; left: 24px; right: 24px; height: 1px; background: linear-gradient(90deg, transparent, var(--ra-border), transparent); }
  .ra-pnl-hero-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--ra-muted); margin-bottom: 6px; }
  .ra-pnl-hero-val { font-size: 36px; font-weight: 900; line-height: 1; letter-spacing: -0.03em; }
  .ra-pnl-hero-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-top: 8px; }

  /* P&L Stat Grid */
  .ra-pnl-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0; }
  .ra-pnl-stat { padding: 18px 16px; text-align: center; position: relative; border-bottom: 1px solid var(--ra-border); }
  .ra-pnl-stat:not(:last-child)::after { content: ''; position: absolute; right: 0; top: 16px; bottom: 16px; width: 1px; background: var(--ra-border); }
  .ra-pnl-stat-icon { width: 38px; height: 38px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; margin-bottom: 10px; position: relative; }
  .ra-pnl-stat-icon .ra-glow { position: absolute; inset: -4px; border-radius: 16px; opacity: 0.15; pointer-events: none; }
  .ra-pnl-stat-val { font-size: 18px; font-weight: 800; color: var(--ra-text); line-height: 1; letter-spacing: -0.02em; }
  .ra-pnl-stat-label { font-size: 10px; font-weight: 700; color: var(--ra-muted); text-transform: uppercase; letter-spacing: .06em; margin-top: 6px; }
  .ra-pnl-stat-sub { font-size: 10px; font-weight: 600; color: var(--ra-muted); margin-top: 3px; }

  /* P&L Bottom Row */
  .ra-pnl-bottom { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0; }
  .ra-pnl-mini { padding: 14px 12px; text-align: center; position: relative; }
  .ra-pnl-mini:not(:last-child)::after { content: ''; position: absolute; right: 0; top: 12px; bottom: 12px; width: 1px; background: var(--ra-border); }
  .ra-pnl-mini-val { font-size: 15px; font-weight: 800; color: var(--ra-text); line-height: 1; }
  .ra-pnl-mini-label { font-size: 9px; font-weight: 700; color: var(--ra-muted); text-transform: uppercase; letter-spacing: .06em; margin-top: 5px; }

  /* Conversion funnel — shared track, equal card chrome, connectors */
  .ra-funnel-strip {
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
    gap: 0;
  }
  .ra-funnel-stage-wrap {
    flex: 1 1 0;
    min-width: 0;
    display: flex;
    flex-direction: column;
  }
  .ra-funnel-chevron {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    color: var(--ra-muted);
    opacity: 0.42;
    align-self: center;
  }
  .ra-funnel-chevron i { font-size: 20px; }

  .ra-funnel-card {
    background: var(--ra-surface-2);
    border: 1px solid var(--ra-border);
    border-radius: 14px;
    padding: 18px 14px 16px;
    text-align: center;
    position: relative;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
  }
  .ra-funnel-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(15,23,42,0.07);
    border-color: rgba(var(--primary-rgb), 0.12);
  }
  .dark .ra-funnel-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,0.28); }
  .ra-funnel-card-glow {
    position: absolute;
    top: -28px;
    right: -28px;
    width: 88px;
    height: 88px;
    border-radius: 50%;
    opacity: 0.12;
    pointer-events: none;
  }
  .ra-funnel-card-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
    flex-shrink: 0;
  }
  .ra-funnel-card-count {
    font-size: 28px;
    font-weight: 900;
    color: var(--ra-text);
    line-height: 1;
    letter-spacing: -0.02em;
  }
  .ra-funnel-card-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--ra-muted);
    margin-top: 8px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    line-height: 1.3;
  }
  .ra-funnel-card-pct {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 999px;
    margin-top: 10px;
    flex-shrink: 0;
  }
  /* Fixed slot so every card keeps the same height */
  .ra-funnel-drop-slot {
    min-height: 22px;
    margin-top: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    flex-shrink: 0;
  }
  .ra-funnel-card-drop {
    font-size: 10px;
    font-weight: 700;
    color: #f87171;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    line-height: 1.2;
  }
  .dark .ra-funnel-card-drop { color: #fca5a5; }
  .ra-funnel-drop-placeholder { visibility: hidden; font-size: 10px; }

  .ra-funnel-track {
    width: 100%;
    margin-top: auto;
    padding-top: 14px;
  }
  .ra-funnel-track-rail {
    width: 100%;
    height: 6px;
    border-radius: 999px;
    background: var(--ra-border);
    overflow: hidden;
  }
  .dark .ra-funnel-track-rail { background: rgba(255,255,255,0.08); }
  .ra-funnel-track-fill {
    height: 100%;
    border-radius: 999px;
    transition: width 0.65s cubic-bezier(0.4, 0, 0.2, 1);
    min-width: 0;
  }

  @media (max-width: 900px) {
    .ra-funnel-strip { flex-wrap: wrap; justify-content: center; row-gap: 12px; }
    .ra-funnel-stage-wrap { flex: 1 1 calc(50% - 14px); max-width: calc(50% - 8px); }
    .ra-funnel-chevron {
      display: none;
    }
  }
  @media (max-width: 768px) {
    .ra-pnl-stats { grid-template-columns: 1fr !important; }
    .ra-pnl-stat:not(:last-child)::after { display: none; }
    .ra-pnl-bottom { grid-template-columns: repeat(2, 1fr); }
    .ra-funnel-stage-wrap { flex: 1 1 100%; max-width: 100%; }
  }

  .ra-table { width: 100%; border-collapse: collapse; }
  .ra-table th { text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--ra-muted); padding: 12px 16px; border-bottom: 1px solid var(--ra-border); white-space: nowrap; }
  .ra-table td { padding: 12px 16px; font-size: 13px; color: var(--ra-text); border-bottom: 1px solid rgba(15,23,42,0.04); vertical-align: middle; }
  .dark .ra-table td { border-bottom-color: rgba(255,255,255,0.04); }
  .ra-table tbody tr { transition: background 0.1s; }
  .ra-table tbody tr:hover { background: rgba(var(--primary-rgb), 0.03); }
  .ra-table tbody tr:last-child td { border-bottom: none; }
  .ra-table .ra-money { font-weight: 700; font-family: 'JetBrains Mono', monospace; font-size: 12px; }
  .ra-table .ra-pct-badge { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; }

  .ra-geo-bar-wrap { display: flex; align-items: center; gap: 12px; }
  .ra-geo-bar-track { flex: 1; height: 8px; border-radius: 4px; background: var(--ra-surface-2); overflow: hidden; }
  .ra-geo-bar-fill { height: 100%; border-radius: 4px; transition: width 0.6s ease; }

  @media (max-width: 1024px) {
    .ra-grid-2 { grid-template-columns: 1fr !important; }
  }

  /* Revenue trend filter row (narrower pills on small screens) */
  .ra-trend-header-row {
    flex: 1;
    justify-content: flex-end;
    min-width: 0;
  }
  .ra-trend-header-row .ra-filter-btn {
    padding: 0 10px;
    font-size: 11px;
  }
</style>

<div class="main-content app-content">
  <div class="container-fluid ra-page">

    <!-- Page Header -->
    <div class="flex items-start justify-between flex-wrap gap-3 mb-6">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="ri-bar-chart-box-line text-primary text-base"></i>
          </div>
          <h1 class="page-title font-bold text-xl mb-0" style="color:var(--ra-text)">Reports & Analytics</h1>
        </div>
        <p class="text-xs mt-1 mb-0 ms-10" style="color:var(--ra-muted)">Financial overview, revenue trends, and conversion metrics.</p>
      </div>
      <div id="ra-pnl-period-filters" class="flex items-center gap-2 flex-wrap">
        <button class="ra-filter-btn active" data-period="this_month" onclick="setPeriod('this_month',this)">This Month</button>
        <button class="ra-filter-btn" data-period="last_month" onclick="setPeriod('last_month',this)">Last Month</button>
        <button class="ra-filter-btn" data-period="this_quarter" onclick="setPeriod('this_quarter',this)">This Quarter</button>
        <button class="ra-filter-btn" data-period="this_year" onclick="setPeriod('this_year',this)">This Year</button>
        <button class="ra-filter-btn" data-period="all_time" onclick="setPeriod('all_time',this)">All Time</button>
      </div>
    </div>

    <!-- P&L Statement -->
    <div class="ra-card mb-6">
      <div class="ra-card-header">
        <div class="flex items-center justify-between flex-wrap gap-3">
          <div class="flex items-center gap-3">
            <div class="ra-card-icon bg-success/10 text-success"><i class="ri-money-dollar-circle-line"></i></div>
            <div>
              <div class="font-bold text-sm" style="color:var(--ra-text)">Profit & Loss Statement</div>
              <div style="font-size:11px;color:var(--ra-muted);" id="ra-pnl-period">This Month</div>
            </div>
          </div>
        </div>
      </div>
      <!-- Hero: Net Profit -->
      <div class="ra-pnl-hero">
        <div class="ra-pnl-hero-label">Net Profit</div>
        <div class="ra-pnl-hero-val" id="pnl-net" style="color:rgb(var(--primary-rgb));">$0.00</div>
        <div class="ra-pnl-hero-badge" id="pnl-net-badge" style="background:rgba(var(--primary-rgb),0.08);color:rgb(var(--primary-rgb));">
          <i class="ri-arrow-up-line" style="font-size:12px;"></i> <span id="pnl-net-pct">0.0%</span> net margin
        </div>
      </div>
      <!-- 3-column main stats -->
      <div class="ra-pnl-stats">
        <div class="ra-pnl-stat">
          <div class="ra-pnl-stat-icon" style="background:rgba(34,197,94,0.10);color:#22c55e;">
            <div class="ra-glow" style="background:#22c55e;"></div>
            <i class="ri-wallet-3-line"></i>
          </div>
          <div class="ra-pnl-stat-val" id="pnl-revenue">$0.00</div>
          <div class="ra-pnl-stat-label">Gross Revenue</div>
          <div class="ra-pnl-stat-sub" id="pnl-rev-pct">100.0%</div>
        </div>
        <div class="ra-pnl-stat">
          <div class="ra-pnl-stat-icon" style="background:rgba(239,68,68,0.10);color:#ef4444;">
            <div class="ra-glow" style="background:#ef4444;"></div>
            <i class="ri-truck-line"></i>
          </div>
          <div class="ra-pnl-stat-val" id="pnl-cost" style="color:#ef4444;">$0.00</div>
          <div class="ra-pnl-stat-label">Cost of Service</div>
          <div class="ra-pnl-stat-sub">Fuel + Driver</div>
        </div>
        <div class="ra-pnl-stat">
          <div class="ra-pnl-stat-icon" style="background:rgba(59,130,246,0.10);color:#3b82f6;">
            <div class="ra-glow" style="background:#3b82f6;"></div>
            <i class="ri-funds-line"></i>
          </div>
          <div class="ra-pnl-stat-val" id="pnl-gross">$0.00</div>
          <div class="ra-pnl-stat-label">Gross Profit</div>
          <div class="ra-pnl-stat-sub" id="pnl-gross-pct">0.0% margin</div>
        </div>
      </div>
      <!-- 4-column bottom stats -->
      <div class="ra-pnl-bottom">
        <div class="ra-pnl-mini">
          <div class="ra-pnl-mini-val" id="pnl-commissions" style="color:#f59e0b;">$0.00</div>
          <div class="ra-pnl-mini-label">Commissions (30%)</div>
        </div>
        <div class="ra-pnl-mini">
          <div class="ra-pnl-mini-val" id="pnl-processing" style="color:#f59e0b;">$0.00</div>
          <div class="ra-pnl-mini-label">Processing (2.9%)</div>
        </div>
        <div class="ra-pnl-mini">
          <div class="ra-pnl-mini-val" id="pnl-deals">0</div>
          <div class="ra-pnl-mini-label">Deals</div>
        </div>
        <div class="ra-pnl-mini">
          <div class="ra-pnl-mini-val" id="pnl-avg">$0.00</div>
          <div class="ra-pnl-mini-label">Avg Deal</div>
        </div>
      </div>
    </div>

    <!-- Conversion Funnel -->
    <div class="ra-card mb-6">
      <div class="ra-card-header">
        <div class="flex items-center gap-3">
          <div class="ra-card-icon bg-info/10 text-info"><i class="ri-filter-3-line"></i></div>
          <div>
            <div class="font-bold text-sm" style="color:var(--ra-text)">Conversion Funnel</div>
            <div style="font-size:11px;color:var(--ra-muted);">Lead progression through stages</div>
          </div>
        </div>
      </div>
      <div class="ra-card-body">
        <?php
        $funnelTiers = [
          ['label'=>'Total Leads','count'=>$funnelData['total'],'color'=>'#3b82f6','icon'=>'ri-group-line'],
          ['label'=>'Assigned','count'=>$funnelData['assigned'],'color'=>'#8b5cf6','icon'=>'ri-user-follow-line'],
          ['label'=>'Quoted','count'=>$funnelData['quoted'],'color'=>'#f59e0b','icon'=>'ri-file-text-line'],
          ['label'=>'Converted','count'=>$funnelData['converted'],'color'=>'#22c55e','icon'=>'ri-checkbox-circle-line'],
        ];
        $prevCount = 0;
        $funnelTotal = max(1, (int)$funnelData['total']);
        ?>
        <div class="ra-funnel-strip">
          <?php foreach ($funnelTiers as $idx => $tier):
            $pct = round(($tier['count'] / $funnelTotal) * 100, 1);
            $pct = min(100, max(0, $pct));
            $fillWidth = $funnelTotal > 0 ? ($tier['count'] / $funnelTotal) * 100 : 0;
            $fillWidth = min(100, max(0, $fillWidth));
            $dropPct = ($idx > 0 && $prevCount > 0) ? round((1 - $tier['count'] / $prevCount) * 100, 1) : 0;
            $showDrop = ($idx > 0 && $dropPct > 0);
          ?>
            <?php if ($idx > 0): ?>
              <div class="ra-funnel-chevron" aria-hidden="true"><i class="ri-arrow-right-s-line"></i></div>
            <?php endif; ?>
            <div class="ra-funnel-stage-wrap">
              <div class="ra-funnel-card">
                <div class="ra-funnel-card-glow" style="background:<?php echo htmlspecialchars($tier['color']); ?>;"></div>
                <div class="ra-funnel-card-icon" style="background:<?php echo htmlspecialchars($tier['color']); ?>18;color:<?php echo htmlspecialchars($tier['color']); ?>;">
                  <i class="<?php echo htmlspecialchars($tier['icon']); ?>"></i>
                </div>
                <div class="ra-funnel-card-count"><?php echo number_format($tier['count']); ?></div>
                <div class="ra-funnel-card-label"><?php echo htmlspecialchars($tier['label']); ?></div>
                <div class="ra-funnel-card-pct" style="background:<?php echo htmlspecialchars($tier['color']); ?>14;color:<?php echo htmlspecialchars($tier['color']); ?>;"><?php echo $pct; ?>% of total</div>
                <div class="ra-funnel-drop-slot">
                  <?php if ($showDrop): ?>
                    <span class="ra-funnel-card-drop" title="Drop-off vs previous funnel stage"><i class="ri-arrow-down-line"></i> <?php echo $dropPct; ?>% vs prior</span>
                  <?php else: ?>
                    <span class="ra-funnel-drop-placeholder">—</span>
                  <?php endif; ?>
                </div>
                <div class="ra-funnel-track">
                  <div class="ra-funnel-track-rail">
                    <div class="ra-funnel-track-fill" style="width:<?php echo $fillWidth; ?>%;background:<?php echo htmlspecialchars($tier['color']); ?>;"></div>
                  </div>
                </div>
              </div>
            </div>
          <?php $prevCount = $tier['count']; endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Row 2: Revenue Trend Chart -->
    <div class="ra-card mb-6">
      <div class="ra-card-header">
        <div class="flex items-center justify-between flex-wrap gap-3">
          <div class="flex items-center gap-3">
            <div class="ra-card-icon bg-primary/10 text-primary"><i class="ri-line-chart-line"></i></div>
            <div>
              <div class="font-bold text-sm" style="color:var(--ra-text)">Revenue Trend</div>
              <div style="font-size:11px;color:var(--ra-muted);" id="ra-revenue-chart-desc">Monthly gross revenue by lead creation date</div>
            </div>
          </div>
          <div id="ra-trend-range-filters" class="flex flex-wrap items-center gap-2 ra-trend-header-row">
            <button type="button" class="ra-filter-btn active" data-trend-range="m24" onclick="setTrendRange('m24', this)">24 months</button>
            <button type="button" class="ra-filter-btn" data-trend-range="m18" onclick="setTrendRange('m18', this)">18 months</button>
            <button type="button" class="ra-filter-btn" data-trend-range="m12" onclick="setTrendRange('m12', this)">12 months</button>
            <button type="button" class="ra-filter-btn" data-trend-range="m6" onclick="setTrendRange('m6', this)">6 months</button>
            <button type="button" class="ra-filter-btn" data-trend-range="m3" onclick="setTrendRange('m3', this)">3 months</button>
            <button type="button" class="ra-filter-btn" data-trend-range="this_month" onclick="setTrendRange('this_month', this)">This month</button>
          </div>
        </div>
      </div>
      <div class="ra-card-body" style="padding:16px 24px 24px;">
        <canvas id="ra-revenue-chart" height="100"></canvas>
      </div>
    </div>

    <!-- Row 3: Geographic + P&L by Event Type -->
    <div class="grid grid-cols-12 gap-6 mb-6 ra-grid-2" style="grid-template-columns: 1fr 1fr;">
      <!-- Geographic Analysis -->
      <div class="ra-card">
        <div class="ra-card-header">
          <div class="flex items-center gap-3">
            <div class="ra-card-icon bg-warning/10 text-warning"><i class="ri-map-pin-line"></i></div>
            <div>
              <div class="font-bold text-sm" style="color:var(--ra-text)">Geographic Analysis (Top 25 States)</div>
              <div style="font-size:11px;color:var(--ra-muted);">Lead distribution by pickup state</div>
            </div>
          </div>
        </div>
        <div class="ra-card-body" style="padding:0;">
          <div class="overflow-auto" style="max-height:500px;">
            <table class="ra-table">
              <thead><tr><th>#</th><th>State</th><th>Leads</th><th>Revenue</th><th>Avg Deal</th><th style="width:120px;">Distribution</th></tr></thead>
              <tbody>
                <?php
                $maxGeoLeads = !empty($geoData) ? max(array_column($geoData, 'leads')) : 1;
                $geoIdx = 0;
                foreach ($geoData as $state => $geo):
                  $geoIdx++;
                  $avg = $geo['leads'] > 0 ? $geo['revenue'] / $geo['leads'] : 0;
                  $barPct = round(($geo['leads'] / $maxGeoLeads) * 100);
                ?>
                <tr>
                  <td style="color:var(--ra-muted);font-weight:600;"><?php echo $geoIdx; ?></td>
                  <td style="font-weight:700;"><?php echo htmlspecialchars($state); ?></td>
                  <td><span style="font-weight:700;"><?php echo number_format($geo['leads']); ?></span></td>
                  <td class="ra-money" style="color:#22c55e;">$<?php echo number_format($geo['revenue'], 2); ?></td>
                  <td class="ra-money">$<?php echo number_format($avg, 2); ?></td>
                  <td>
                    <div class="ra-geo-bar-wrap">
                      <div class="ra-geo-bar-track"><div class="ra-geo-bar-fill" style="width:<?php echo $barPct; ?>%;background:rgb(var(--primary-rgb));"></div></div>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($geoData)): ?>
                <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--ra-muted);">No geographic data available</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- P&L by Event Type -->
      <div class="ra-card">
        <div class="ra-card-header">
          <div class="flex items-center gap-3">
            <div class="ra-card-icon bg-secondary/10 text-secondary"><i class="ri-calendar-event-line"></i></div>
            <div>
              <div class="font-bold text-sm" style="color:var(--ra-text)">P&L by Event Type</div>
              <div style="font-size:11px;color:var(--ra-muted);">Financial breakdown by service category</div>
            </div>
          </div>
        </div>
        <div class="ra-card-body" style="padding:0;">
          <div class="overflow-auto" style="max-height:500px;">
            <table class="ra-table">
              <thead><tr><th>Event Type</th><th style="text-align:right;">Deals</th><th style="text-align:right;">Revenue</th><th style="text-align:right;">Cost</th><th style="text-align:right;">Profit</th><th style="text-align:right;">Margin</th></tr></thead>
              <tbody>
                <?php foreach ($eventPnl as $evType => $ev):
                  $profit = $ev['revenue'] - $ev['cost'];
                  $margin = $ev['revenue'] > 0 ? round(($profit / $ev['revenue']) * 100, 1) : 0;
                  $marginColor = $margin >= 50 ? '#22c55e' : ($margin >= 20 ? '#f59e0b' : '#ef4444');
                ?>
                <tr>
                  <td style="font-weight:700;"><?php echo htmlspecialchars($evType); ?></td>
                  <td style="text-align:right;font-weight:600;"><?php echo $ev['deals']; ?></td>
                  <td style="text-align:right;color:#22c55e;" class="ra-money">$<?php echo number_format($ev['revenue'], 2); ?></td>
                  <td style="text-align:right;color:#ef4444;" class="ra-money">$<?php echo number_format($ev['cost'], 2); ?></td>
                  <td style="text-align:right;font-weight:700;" class="ra-money">$<?php echo number_format($profit, 2); ?></td>
                  <td style="text-align:right;"><span class="ra-pct-badge" style="background:<?php echo $marginColor; ?>15;color:<?php echo $marginColor; ?>;"><?php echo $margin; ?>%</span></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($eventPnl)): ?>
                <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--ra-muted);">No event data available</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<script>
var pnlData = <?php echo $pnlJson; ?>;
var trendLabels = <?php echo $trendLabels; ?>;
var trendValues = <?php echo $trendValues; ?>;
var trendDaily90Labels = <?php echo $trendDaily90Labels; ?>;
var trendDaily90Values = <?php echo $trendDaily90Values; ?>;
var trendDailyMonthLabels = <?php echo $trendDailyMonthLabels; ?>;
var trendDailyMonthValues = <?php echo $trendDailyMonthValues; ?>;
var currentPeriod = 'this_month';
var periodNames = {
  'this_month': 'This Month',
  'last_month': 'Last Month',
  'this_quarter': 'This Quarter',
  'this_year': 'This Year',
  'all_time': 'All Time'
};

function fmt(n) {
  return '$' + Math.abs(n).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
}

function updatePnl(period) {
  var d = pnlData[period] || {revenue:0,cost:0,deals:0};
  var revenue = d.revenue || 0;
  var cost = d.cost || 0;
  var gross = revenue - cost;
  var commissions = revenue * 0.30;
  var processing = revenue * 0.029;
  var net = gross - commissions - processing;
  var deals = d.deals || 0;
  var avg = deals > 0 ? revenue / deals : 0;
  var grossMargin = revenue > 0 ? ((gross / revenue) * 100).toFixed(1) : '0.0';
  var netMargin = revenue > 0 ? ((net / revenue) * 100).toFixed(1) : '0.0';

  document.getElementById('pnl-net').textContent = (net < 0 ? '-' : '') + fmt(net);
  document.getElementById('pnl-net-pct').textContent = netMargin + '%';
  document.getElementById('pnl-revenue').textContent = fmt(revenue);
  document.getElementById('pnl-rev-pct').textContent = '100.0%';
  document.getElementById('pnl-cost').textContent = fmt(cost);
  document.getElementById('pnl-gross').textContent = fmt(gross);
  document.getElementById('pnl-gross-pct').textContent = grossMargin + '% margin';
  document.getElementById('pnl-commissions').textContent = fmt(commissions);
  document.getElementById('pnl-processing').textContent = fmt(processing);
  document.getElementById('pnl-deals').textContent = deals.toLocaleString();
  document.getElementById('pnl-avg').textContent = fmt(avg);
  document.getElementById('ra-pnl-period').textContent = periodNames[period] || period;

  var arrow = document.querySelector('#pnl-net-badge i');
  if (arrow) {
    arrow.className = net >= 0 ? 'ri-arrow-up-line' : 'ri-arrow-down-line';
    arrow.style.fontSize = '12px';
  }
  var badge = document.getElementById('pnl-net-badge');
  if (badge) {
    badge.style.background = net >= 0 ? 'rgba(var(--primary-rgb),0.08)' : 'rgba(239,68,68,0.08)';
    badge.style.color = net >= 0 ? 'rgb(var(--primary-rgb))' : '#ef4444';
  }
  var heroVal = document.getElementById('pnl-net');
  if (heroVal) {
    heroVal.style.color = net >= 0 ? 'rgb(var(--primary-rgb))' : '#ef4444';
  }
}

function setPeriod(period, btn) {
  currentPeriod = period;
  document.querySelectorAll('#ra-pnl-period-filters .ra-filter-btn').forEach(function(b) { b.classList.remove('active'); });
  if (btn) btn.classList.add('active');
  updatePnl(period);
}

updatePnl('this_month');

var raRevenueChart = null;
var raTrendTooltipTitles = [];

function ymToShortLabel(ymKey) {
  var parts = String(ymKey).split('-');
  if (parts.length < 2) return String(ymKey);
  var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var mi = parseInt(parts[1], 10) - 1;
  if (mi < 0 || mi > 11) return String(ymKey);
  return months[mi] + ' ' + parts[0].slice(2);
}

var raTrendDesc = {
  m24: 'Last 24 months (monthly)',
  m18: 'Last 18 months (monthly)',
  m12: 'Last 12 months (monthly)',
  m6: 'Last 6 months (monthly)',
  m3: 'Last 90 days · by calendar day',
  this_month: 'This month · by calendar day'
};

function trendIsDailyGranularity(range) {
  return range === 'm3' || range === 'this_month';
}

function ymdToShortLabel(ymd) {
  var parts = String(ymd).split('-');
  if (parts.length < 3) return String(ymd);
  var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var mi = parseInt(parts[1], 10) - 1;
  var day = parseInt(parts[2], 10);
  if (mi < 0 || mi > 11) return String(ymd);
  return months[mi] + ' ' + day;
}

function sliceDailySeries(labels, values) {
  var L = Array.isArray(labels) ? labels : [];
  var V = Array.isArray(values) ? values : [];
  return {
    labels: L.map(ymdToShortLabel),
    values: V.slice(),
    tooltips: L.slice()
  };
}

function sliceTrendForRange(range) {
  if (range === 'm3') {
    return sliceDailySeries(trendDaily90Labels, trendDaily90Values);
  }
  if (range === 'this_month') {
    return sliceDailySeries(trendDailyMonthLabels, trendDailyMonthValues);
  }

  var keys = Array.isArray(trendLabels) ? trendLabels.slice() : [];
  var vals = Array.isArray(trendValues) ? trendValues.slice() : [];
  var n = keys.length;
  var takeMap = { m24: 24, m18: 18, m12: 12, m6: 6 };

  if (n === 0) {
    return { labels: [], values: [], tooltips: [] };
  }

  var take = takeMap[range] || n;
  take = Math.max(1, Math.min(take, n));
  var start = n - take;
  var sk = keys.slice(start);
  var sv = vals.slice(start);
  return {
    labels: sk.map(ymToShortLabel),
    values: sv,
    tooltips: sk.slice()
  };
}

function setTrendRange(range, btn) {
  var pane = document.getElementById('ra-trend-range-filters');
  if (pane) {
    pane.querySelectorAll('.ra-filter-btn').forEach(function(b) { b.classList.remove('active'); });
  }
  if (btn) btn.classList.add('active');

  var sliced = sliceTrendForRange(range);
  raTrendTooltipTitles = sliced.tooltips || [];

  var descEl = document.getElementById('ra-revenue-chart-desc');
  if (descEl) {
    var bit = raTrendDesc[range] || '';
    var prefix = trendIsDailyGranularity(range)
      ? 'Daily gross revenue by lead creation date'
      : 'Monthly gross revenue by lead creation date';
    descEl.textContent = prefix + (bit ? ' · ' + bit : '');
  }

  if (raRevenueChart) {
    raRevenueChart.data.labels = sliced.labels.slice();
    raRevenueChart.data.datasets[0].data = sliced.values.slice();
    raRevenueChart.update();
  }
}

(function() {
  var isDark = document.documentElement.classList.contains('dark');
  var gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(15,23,42,0.06)';
  var textColor = isDark ? 'rgba(255,255,255,0.5)' : 'rgba(15,23,42,0.55)';

  var initialSlice = sliceTrendForRange('m24');
  raTrendTooltipTitles = initialSlice.tooltips || [];

  var ctx = document.getElementById('ra-revenue-chart');
  if (!ctx) return;
  raRevenueChart = new Chart(ctx.getContext('2d'), {
    type: 'bar',
    data: {
      labels: initialSlice.labels,
      datasets: [{
        label: 'Revenue',
        data: initialSlice.values,
        backgroundColor: 'rgba(207,28,130,0.55)',
        hoverBackgroundColor: 'rgba(207,28,130,0.8)',
        borderColor: 'rgba(207,28,130,1)',
        borderWidth: 1,
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: isDark ? '#1e293b' : '#fff',
          titleColor: isDark ? '#fff' : '#0f172a',
          bodyColor: isDark ? 'rgba(255,255,255,0.7)' : 'rgba(15,23,42,0.65)',
          borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(15,23,42,0.1)',
          borderWidth: 1,
          padding: 12,
          cornerRadius: 10,
          callbacks: {
            title: function(items) {
              if (!items || !items.length) return '';
              var idx = items[0].dataIndex;
              var t = raTrendTooltipTitles[idx];
              if (!t) return items[0].label || '';
              if (/^\d{4}-\d{2}-\d{2}$/.test(t)) {
                var p = t.split('-');
                var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                var d = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
                var wk = d.toLocaleDateString(undefined, { weekday: 'short' });
                return t + ' (' + wk + ')';
              }
              return t + ' revenue';
            },
            label: function(item) {
              return '$' + (item.parsed.y || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
            }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: {
            color: textColor,
            font: { size: 10, weight: 600 },
            maxRotation: 45,
            autoSkip: true,
            autoSkipPadding: 4,
            maxTicksLimit: 20
          }
        },
        y: {
          grid: { color: gridColor },
          ticks: {
            color: textColor,
            font: { size: 11, weight: 600 },
            callback: function(v) { return '$' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v); }
          }
        }
      }
    }
  });

  var descEl = document.getElementById('ra-revenue-chart-desc');
  if (descEl) {
    descEl.textContent = 'Monthly gross revenue by lead creation date · ' + (raTrendDesc.m24 || '');
  }
})();
</script>
