<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
  $data['id'] = $_SESSION['user']['id'];
  if($_SESSION['user']['admin'] == 1){
    $leads = fetchAllLeads($data);
  } else {
    $leads = fetchAllUserLeads($data);
  }
  if (!is_array($leads)) { $leads = []; }

  $leadStats = ['total' => count($leads), 'new' => 0, 'converted' => 0, 'dead' => 0];
  foreach ($leads as $lead) {
    $status = strtolower(trim((string)($lead['status'] ?? '')));
    if (in_array($status, ['converted','won','success'])) { $leadStats['converted']++; }
    elseif (in_array($status, ['dead','lost','closed','junk'])) { $leadStats['dead']++; }
    elseif (in_array($status, ['new','open','new lead','new_lead'])) { $leadStats['new']++; }
  }
  $conversionRate = $leadStats['total'] > 0 ? round(($leadStats['converted'] / $leadStats['total']) * 100, 1) : 0;
?>

<style>
  .ld-page {
    --ld-surface: #ffffff;
    --ld-surface-2: #f8fafc;
    --ld-border: rgba(15,23,42,0.08);
    --ld-text: #0f172a;
    --ld-muted: rgba(15,23,42,0.55);
  }
  .dark .ld-page {
    --ld-surface: rgba(255,255,255,0.035);
    --ld-surface-2: rgba(255,255,255,0.05);
    --ld-border: rgba(255,255,255,0.08);
    --ld-text: rgba(255,255,255,0.92);
    --ld-muted: rgba(255,255,255,0.50);
  }

  .ld-stat {
    background: var(--ld-surface);
    border: 1px solid var(--ld-border);
    border-radius: 16px;
    padding: 20px 24px;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .ld-stat:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(15,23,42,0.08);
  }
  .dark .ld-stat:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
  .ld-stat .ld-stat-glow {
    position: absolute; top: -20px; right: -20px;
    width: 80px; height: 80px; border-radius: 50%;
    opacity: 0.08; pointer-events: none;
  }
  .ld-stat .ld-stat-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
  }
  .ld-stat .ld-stat-num {
    font-size: 28px; font-weight: 800; line-height: 1;
    color: var(--ld-text); letter-spacing: -0.02em;
  }
  .ld-stat .ld-stat-label {
    font-size: 12px; font-weight: 600; color: var(--ld-muted);
    text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px;
  }

  .ld-table-card {
    background: var(--ld-surface);
    border: 1px solid var(--ld-border);
    border-radius: 16px;
    overflow: hidden;
  }
  .ld-toolbar {
    padding: 16px 20px;
    border-bottom: 1px solid var(--ld-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
  }
  .ld-toolbar-row2 {
    padding: 12px 20px;
    border-bottom: 1px solid var(--ld-border);
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }
  .ld-search {
    height: 38px; border-radius: 10px;
    border: 1px solid var(--ld-border);
    background: var(--ld-surface-2);
    color: var(--ld-text);
    padding: 0 12px 0 36px!important;
    font-size: 13px; outline: none;
    width: min(320px, 100%);
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .ld-search:focus {
    border-color: rgb(var(--primary-rgb));
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12);
  }
  .ld-search-wrap { position: relative; }
  .ld-search-wrap i {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    font-size: 15px; color: var(--ld-muted); pointer-events: none;
  }
  .ld-filter-btn {
    height: 34px; border-radius: 8px;
    border: 1px solid var(--ld-border);
    background: transparent;
    color: var(--ld-muted);
    padding: 0 14px;
    font-size: 12px; font-weight: 600;
    cursor: pointer; transition: all 0.15s;
    display: inline-flex; align-items: center; gap: 6px;
    white-space: nowrap;
  }
  .ld-filter-btn:hover, .ld-filter-btn.active {
    background: rgba(var(--primary-rgb), 0.06);
    color: rgb(var(--primary-rgb));
    border-color: rgba(var(--primary-rgb), 0.20);
  }
  .ld-date-input {
    height: 34px; border-radius: 8px;
    border: 1px solid var(--ld-border);
    background: var(--ld-surface-2);
    color: var(--ld-text);
    padding: 0 10px; font-size: 12px; outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .ld-date-input:focus {
    border-color: rgb(var(--primary-rgb));
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12);
  }
  .ld-clear-btn {
    height: 34px; border-radius: 8px;
    border: 1px solid rgba(239,68,68,0.20);
    background: rgba(239,68,68,0.04);
    color: #dc2626; padding: 0 12px;
    font-size: 12px; font-weight: 600;
    cursor: pointer; transition: all 0.15s;
    display: inline-flex; align-items: center; gap: 4px;
    white-space: nowrap;
  }
  .ld-clear-btn:hover { background: rgba(239,68,68,0.10); }
  .ld-separator {
    width: 1px; height: 20px; background: var(--ld-border);
    flex-shrink: 0;
  }
</style>

<div class="main-content app-content">
  <div class="container-fluid ld-page">

    <!-- Page Header -->
    <div class="flex items-start justify-between flex-wrap gap-3 mb-6">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="ri-bar-chart-box-line text-primary text-base"></i>
          </div>
          <h1 class="page-title font-bold text-xl mb-0 text-defaulttextcolor dark:text-defaulttextcolor/90">Lead Management</h1>
        </div>
        <p class="text-xs text-textmuted dark:text-textmuted/50 mt-1 mb-0 ms-10">Track, filter, and convert your leads into customers.</p>
      </div>
      <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Leads', 'create') == 1): ?>
      <div class="flex items-center gap-2" id="add-lead-btn">
        <a href="add_lead.php" class="ti-btn ti-btn-sm bg-primary text-white font-semibold shadow-sm hover:shadow-md transition-all !rounded-xl px-4">
          <i class="ri-add-line me-1 text-base"></i> New Lead
        </a>
      </div>
      <?php endif; ?>
    </div>

    <!-- Stat Cards -->
    <div class="grid xl:grid-cols-5 lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-4 mb-6" id="intro-leads-stats">
      <div class="ld-stat">
        <div class="ld-stat-glow" style="background: rgb(var(--primary-rgb));"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="ld-stat-num"><?php echo $leadStats['total']; ?></div>
            <div class="ld-stat-label">Total Leads</div>
          </div>
          <div class="ld-stat-icon bg-primary/10 text-primary"><i class="ri-bar-chart-grouped-line"></i></div>
        </div>
      </div>
      <div class="ld-stat">
        <div class="ld-stat-glow" style="background: #3b82f6;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="ld-stat-num"><?php echo $leadStats['new']; ?></div>
            <div class="ld-stat-label">New Leads</div>
          </div>
          <div class="ld-stat-icon bg-info/10 text-info"><i class="ri-sparkling-line"></i></div>
        </div>
      </div>
      <div class="ld-stat">
        <div class="ld-stat-glow" style="background: #22c55e;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="ld-stat-num"><?php echo $leadStats['converted']; ?></div>
            <div class="ld-stat-label">Converted</div>
          </div>
          <div class="ld-stat-icon bg-success/10 text-success"><i class="ri-checkbox-circle-line"></i></div>
        </div>
      </div>
      <div class="ld-stat">
        <div class="ld-stat-glow" style="background: #ef4444;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="ld-stat-num"><?php echo $leadStats['dead']; ?></div>
            <div class="ld-stat-label">Dead Leads</div>
          </div>
          <div class="ld-stat-icon bg-danger/10 text-danger"><i class="ri-close-circle-line"></i></div>
        </div>
      </div>
      <div class="ld-stat">
        <div class="ld-stat-glow" style="background: #8b5cf6;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="ld-stat-num"><?php echo $conversionRate; ?>%</div>
            <div class="ld-stat-label">Conversion Rate</div>
          </div>
          <div class="ld-stat-icon bg-secondary/10 text-secondary"><i class="ri-percent-line"></i></div>
        </div>
      </div>
    </div>

    <?php
      $statusSet = [];
      foreach ($leads as $lead) {
        $st = trim((string)($lead['status'] ?? ''));
        if ($st !== '') {
          if (!isset($statusSet[$st])) $statusSet[$st] = 0;
          $statusSet[$st]++;
        }
      }
      $statuses = array_keys($statusSet);
      sort($statuses, SORT_NATURAL | SORT_FLAG_CASE);
    ?>

    <!-- Table Card with integrated toolbar -->
    <div class="ld-table-card mb-6">
      <!-- Toolbar row 1: search + status pill buttons -->
      <div class="ld-toolbar">
        <div class="ld-search-wrap">
          <i class="ri-search-line"></i>
          <input id="leads-filter-search" type="text" class="ld-search" placeholder="Search leads...">
        </div>
        <div class="flex items-center gap-2 flex-wrap">
          <button class="ld-filter-btn active" data-status="" onclick="filterLeadStatus('', this)">All</button>
          <?php foreach ($statuses as $st): ?>
          <button class="ld-filter-btn" data-status="<?php echo htmlspecialchars($st, ENT_QUOTES); ?>" onclick="filterLeadStatus('<?php echo htmlspecialchars($st, ENT_QUOTES); ?>', this)">
            <?php echo htmlspecialchars($st); ?>
            <span style="opacity:.55;font-size:11px;"><?php echo $statusSet[$st]; ?></span>
          </button>
          <?php endforeach; ?>
        </div>
      </div>
      <!-- Toolbar row 2: date filters + clear -->
      <div class="ld-toolbar-row2">
        <i class="ri-calendar-event-line text-sm" style="color:var(--ld-muted)"></i>
        <span style="font-size:12px;font-weight:600;color:var(--ld-muted);white-space:nowrap;">Event Date</span>
        <input id="leads-filter-event-from" type="date" class="ld-date-input" title="From date">
        <span style="font-size:11px;color:var(--ld-muted);">to</span>
        <input id="leads-filter-event-to" type="date" class="ld-date-input" title="To date">
        <div class="ld-separator"></div>
        <button type="button" id="leads-filter-clear-btn" class="ld-clear-btn">
          <i class="ri-close-line text-sm"></i> Clear
        </button>
      </div>
      <!-- Table -->
      <div class="overflow-auto" id="intro-leads-table">
        <?php include_once "components/tables/leads.php"; ?>
      </div>
    </div>

  </div>
</div>

<script>
var _ldCurrentStatus = '';

function filterLeadStatus(status, btn) {
  _ldCurrentStatus = status;
  document.querySelectorAll('.ld-filter-btn').forEach(function (b) { b.classList.remove('active'); });
  if (btn) btn.classList.add('active');
  _ldRefresh();
}

(function () {
  const $ = window.jQuery;
  if (!$) return;

  const searchInput = document.getElementById('leads-filter-search');
  const eventFrom = document.getElementById('leads-filter-event-from');
  const eventTo = document.getElementById('leads-filter-event-to');
  const clearBtn = document.getElementById('leads-filter-clear-btn');
  const statusColIdx = 7;

  function parseDateToYmdNumber(dateStr) {
    if (!dateStr) return null;
    const s = String(dateStr).trim();
    const iso = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (iso) return Number(iso[1] + iso[2] + iso[3]);
    const us = s.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})/);
    if (us) return Number(us[3] + us[1].padStart(2, '0') + us[2].padStart(2, '0'));
    const d = new Date(s);
    if (!isNaN(d.getTime())) {
      return Number(String(d.getFullYear()) + String(d.getMonth() + 1).padStart(2, '0') + String(d.getDate()).padStart(2, '0'));
    }
    return null;
  }

  function getTable() {
    if (window.leadsDataTable) return window.leadsDataTable;
    if ($.fn.dataTable && $.fn.dataTable.isDataTable && $.fn.dataTable.isDataTable('#leads-table')) {
      return $('#leads-table').DataTable();
    }
    return null;
  }

  function applyStatusFilter(table) {
    if (!table) return;
    const val = (_ldCurrentStatus || '').trim();
    if (!val) {
      table.column(statusColIdx).search('', false, false);
    } else {
      const escaped = val.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
      table.column(statusColIdx).search('^' + escaped + '$', true, false);
    }
  }

  function wireDateFilters() {
    if ($.fn.dataTable.ext.search.__leadsFiltersRegistered) return;
    $.fn.dataTable.ext.search.__leadsFiltersRegistered = true;
    $.fn.dataTable.ext.search.push(function (settings, data) {
      if (settings.nTable?.id !== 'leads-table') return true;
      const eventDateText = data[5] || '';
      const fromNum = parseDateToYmdNumber(eventFrom?.value || '');
      const toNum = parseDateToYmdNumber(eventTo?.value || '');
      const rowNum = parseDateToYmdNumber(eventDateText);
      if (fromNum && rowNum && rowNum < fromNum) return false;
      if (toNum && rowNum && rowNum > toNum) return false;
      if ((fromNum || toNum) && !rowNum) return false;
      return true;
    });
  }

  window._ldRefresh = function () {
    const table = getTable();
    if (!table) return;
    applyStatusFilter(table);
    table.draw();
  };

  wireDateFilters();

  searchInput?.addEventListener('input', function () {
    const table = getTable();
    if (table) table.search(this.value || '').draw();
  });

  eventFrom?.addEventListener('change', _ldRefresh);
  eventTo?.addEventListener('change', _ldRefresh);

  clearBtn?.addEventListener('click', function () {
    if (searchInput) searchInput.value = '';
    if (eventFrom) eventFrom.value = '';
    if (eventTo) eventTo.value = '';
    _ldCurrentStatus = '';
    document.querySelectorAll('.ld-filter-btn').forEach(function (b) { b.classList.remove('active'); });
    const allBtn = document.querySelector('.ld-filter-btn[data-status=""]');
    if (allBtn) allBtn.classList.add('active');
    const table = getTable();
    if (table) { table.search(''); table.column(statusColIdx).search('', false, false); table.draw(); }
  });

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
