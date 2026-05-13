<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>

<style>
  .wf-page { --wf-surface: #ffffff; --wf-surface-2: #f8fafc; --wf-border: rgba(15,23,42,0.08); --wf-text: #0f172a; --wf-muted: rgba(15,23,42,0.55); }
  .dark .wf-page { --wf-surface: rgba(255,255,255,0.035); --wf-surface-2: rgba(255,255,255,0.05); --wf-border: rgba(255,255,255,0.08); --wf-text: rgba(255,255,255,0.92); --wf-muted: rgba(255,255,255,0.50); }

  .wf-stat { background: var(--wf-surface); border: 1px solid var(--wf-border); border-radius: 16px; padding: 20px 24px; position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; }
  .wf-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(15,23,42,0.08); }
  .dark .wf-stat:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
  .wf-stat .wf-glow { position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; border-radius: 50%; opacity: 0.08; pointer-events: none; }
  .wf-stat .wf-stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
  .wf-stat .wf-stat-num { font-size: 28px; font-weight: 800; line-height: 1; color: var(--wf-text); letter-spacing: -0.02em; }
  .wf-stat .wf-stat-label { font-size: 12px; font-weight: 600; color: var(--wf-muted); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px; }

  .wf-table-card { background: var(--wf-surface); border: 1px solid var(--wf-border); border-radius: 16px; overflow: hidden; }
  .wf-toolbar { padding: 16px 20px; border-bottom: 1px solid var(--wf-border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
  .wf-search { height: 38px; border-radius: 10px; border: 1px solid var(--wf-border); background: var(--wf-surface-2); color: var(--wf-text); padding: 0 12px 0 36px; font-size: 13px; outline: none; width: min(320px, 100%); transition: border-color 0.2s, box-shadow 0.2s; }
  .wf-search:focus { border-color: rgb(var(--primary-rgb)); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12); }
  .wf-search-wrap { position: relative; }
  .wf-search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 15px; color: var(--wf-muted); pointer-events: none; }
  .wf-filter-btn { height: 34px; border-radius: 8px; border: 1px solid var(--wf-border); background: transparent; color: var(--wf-muted); padding: 0 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
  .wf-filter-btn:hover, .wf-filter-btn.active { background: rgba(var(--primary-rgb), 0.06); color: rgb(var(--primary-rgb)); border-color: rgba(var(--primary-rgb), 0.20); }

  .wf-table { width: 100%; border-collapse: collapse; }
  .wf-table th { text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--wf-muted); padding: 12px 16px; border-bottom: 1px solid var(--wf-border); white-space: nowrap; }
  .wf-table td { padding: 14px 16px; font-size: 13px; color: var(--wf-text); border-bottom: 1px solid rgba(15,23,42,0.04); vertical-align: middle; }
  .dark .wf-table td { border-bottom-color: rgba(255,255,255,0.04); }
  .wf-table tbody tr { transition: background 0.1s; }
  .wf-table tbody tr:hover { background: rgba(var(--primary-rgb), 0.03); }
  .wf-table tbody tr:last-child td { border-bottom: none; }

  .wf-avatar { width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
  .wf-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 8px; text-transform: uppercase; letter-spacing: 0.04em; }
  .wf-badge-active { background: rgba(16,185,129,0.10); color: #059669; }
  .wf-badge-inactive { background: rgba(148,163,184,0.12); color: #64748b; }
  .wf-badge-oncreate { background: rgba(59,130,246,0.10); color: #2563eb; }
  .wf-badge-status { background: rgba(139,92,246,0.10); color: #7c3aed; }
  .wf-badge-event { background: rgba(245,158,11,0.10); color: #d97706; }
  .wf-badge-default { background: rgba(var(--primary-rgb),0.08); color: rgb(var(--primary-rgb)); }
  .wf-module-chip { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 8px; background: var(--wf-surface-2); border: 1px solid var(--wf-border); color: var(--wf-text); font-family: ui-monospace, SFMono-Regular, monospace; }

  .wf-action-btn { background: none; border: 1px solid var(--wf-border); border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; color: var(--wf-muted); font-size: 14px; text-decoration: none; }
  .wf-action-btn:hover { border-color: rgba(var(--primary-rgb),0.3); color: rgb(var(--primary-rgb)); background: rgba(var(--primary-rgb),0.04); }
  .wf-action-btn.danger:hover { border-color: rgba(239,68,68,0.3); color: #dc2626; background: rgba(239,68,68,0.04); }

  .wf-empty { text-align: center; padding: 60px 20px; color: var(--wf-muted); }
  .wf-empty i { display: block; font-size: 48px; opacity: 0.15; margin-bottom: 12px; }

  @keyframes wf-fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
  .wf-animate-row { animation: wf-fadeIn 0.25s ease forwards; }

  @media (max-width: 1024px) { #wf-stats { grid-template-columns: repeat(2, 1fr) !important; } }
  @media (max-width: 640px) { #wf-stats { grid-template-columns: 1fr !important; } }
</style>

<div class="main-content app-content">
  <div class="container-fluid wf-page">

    <!-- Page Header -->
    <div class="flex items-start justify-between flex-wrap gap-3 mb-6">
        <div>
        <div class="flex items-center gap-2 mb-1">
          <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="ri-git-merge-line text-primary text-base"></i>
          </div>
          <h1 class="page-title font-bold text-xl mb-0" style="color:var(--wf-text)">Workflows</h1>
        </div>
        <p class="text-xs mt-1 mb-0 ms-10" style="color:var(--wf-muted)">Automate actions with condition-based email workflows.</p>
        </div>
        <div class="flex items-center gap-2">
        <button type="button" class="wf-action-btn" title="Refresh" onclick="loadWorkflows()" style="width:36px;height:36px;font-size:16px;">
          <i class="ri-refresh-line"></i>
          </button>
        <a href="create_workflow.php" class="ti-btn ti-btn-sm bg-primary text-white font-semibold shadow-sm hover:shadow-md transition-all !rounded-xl px-4">
          <i class="ri-add-line me-1 text-base"></i> New Workflow
        </a>
      </div>
    </div>

    <!-- Stats -->
    <div id="wf-stats" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
      <div class="wf-stat">
        <div class="wf-glow" style="background:rgb(var(--primary-rgb));"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="wf-stat-num" id="stat-total">—</div>
            <div class="wf-stat-label">Total Workflows</div>
          </div>
          <div class="wf-stat-icon bg-primary/10 text-primary"><i class="ri-git-merge-line"></i></div>
        </div>
      </div>
      <div class="wf-stat">
        <div class="wf-glow" style="background:#10b981;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="wf-stat-num" id="stat-active">—</div>
            <div class="wf-stat-label">Active</div>
          </div>
          <div class="wf-stat-icon" style="background:rgba(16,185,129,0.1);color:#10b981;"><i class="ri-checkbox-circle-line"></i></div>
        </div>
      </div>
      <div class="wf-stat">
        <div class="wf-glow" style="background:#64748b;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="wf-stat-num" id="stat-inactive">—</div>
            <div class="wf-stat-label">Inactive</div>
          </div>
          <div class="wf-stat-icon" style="background:rgba(100,116,139,0.1);color:#64748b;"><i class="ri-pause-circle-line"></i></div>
        </div>
      </div>
      <div class="wf-stat">
        <div class="wf-glow" style="background:#3b82f6;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="wf-stat-num" id="stat-oncreate">—</div>
            <div class="wf-stat-label">On Create</div>
          </div>
          <div class="wf-stat-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6;"><i class="ri-add-circle-line"></i></div>
          </div>
        </div>
      </div>

    <!-- Table Card -->
    <div class="wf-table-card mb-6">
      <div class="wf-toolbar">
        <div class="wf-search-wrap">
          <i class="ri-search-line"></i>
          <input type="text" class="wf-search" id="wf-search" placeholder="Search by name, module, or template...">
        </div>
        <div class="flex items-center gap-2" id="wf-filter-bar">
          <button class="wf-filter-btn active" data-filter="all" onclick="setFilter('all',this)">All</button>
          <button class="wf-filter-btn" data-filter="active" onclick="setFilter('active',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#10b981;"></span> Active</button>
          <button class="wf-filter-btn" data-filter="inactive" onclick="setFilter('inactive',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#94a3b8;"></span> Inactive</button>
          <button class="wf-filter-btn" data-filter="on_create" onclick="setFilter('on_create',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#3b82f6;"></span> On Create</button>
          <button class="wf-filter-btn" data-filter="status_equals" onclick="setFilter('status_equals',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#8b5cf6;"></span> Status Match</button>
          <button class="wf-filter-btn" data-filter="event_date" onclick="setFilter('event_date',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#f59e0b;"></span> Event Date</button>
        </div>
      </div>
      <div class="overflow-auto">
        <table class="wf-table">
          <thead>
            <tr>
              <th>Workflow</th>
              <th>Module</th>
              <th>Condition</th>
              <th>Email Template</th>
              <th>Status</th>
              <th>Created</th>
              <th style="text-align:center;">Actions</th>
            </tr>
          </thead>
          <tbody id="wf-rows">
            <tr><td colspan="7" class="wf-empty"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading workflows...</td></tr>
          </tbody>
        </table>
        <div id="wf-empty" class="wf-empty" style="display:none;">
          <i class="ri-git-merge-line"></i>
          <div style="font-size:14px;font-weight:600;margin-bottom:4px;">No workflows found</div>
          <div style="font-size:12px;">Try adjusting your search or filter, or create a new workflow.</div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var allWorkflows = [];
var _wfFilter = 'all';
var API = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';
var SESSION_USER_ID = '<?php echo $_SESSION['user']['id'] ?? ''; ?>';

function setFilter(type, btn) {
  _wfFilter = type;
  document.querySelectorAll('.wf-filter-btn').forEach(function(b) { b.classList.remove('active'); });
  if (btn) btn.classList.add('active');
  applyFilters();
}

function applyFilters() {
  var q = ($('#wf-search').val() || '').toLowerCase().trim();
  var filtered = allWorkflows.filter(function(w) {
    var matchFilter = (_wfFilter === 'all')
      || (_wfFilter === 'active' && String(w.status) === 'Active')
      || (_wfFilter === 'inactive' && String(w.status) !== 'Active')
      || (_wfFilter === 'on_create' && w.condition_type === 'on_create')
      || (_wfFilter === 'status_equals' && w.condition_type === 'status_equals')
      || (_wfFilter === 'event_date' && w.condition_type === 'event_date');
    var matchSearch = !q
      || (w.workflow_name || '').toLowerCase().indexOf(q) !== -1
      || (w.module_name || '').toLowerCase().indexOf(q) !== -1
      || (w.condition_type || '').toLowerCase().indexOf(q) !== -1
      || (w.template_name || w.email_template_id || '').toLowerCase().indexOf(q) !== -1
      || (w.description || '').toLowerCase().indexOf(q) !== -1;
    return matchFilter && matchSearch;
  });
  renderTable(filtered);
}

window.loadWorkflows = function() {
  $('#wf-rows').html('<tr><td colspan="7" class="wf-empty"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading workflows...</td></tr>');
  $('#wf-empty').hide();

  $.ajax({
    url: API, type: 'POST',
    data: { action: 'fetch_workflows', id: SESSION_USER_ID },
    success: function(response) {
      try {
        var res = typeof response === 'string' ? JSON.parse(response) : response;
        allWorkflows = (res.rows && Array.isArray(res.rows)) ? res.rows : (Array.isArray(res) ? res : []);
        updateStats();
        applyFilters();
      } catch (e) {
        allWorkflows = [];
        $('#wf-rows').html('<tr><td colspan="7" class="wf-empty" style="color:#ef4444;"><i class="ri-error-warning-line" style="display:inline;font-size:16px;opacity:1;margin-right:4px;"></i> Unable to parse response.</td></tr>');
      }
    },
    error: function() {
      $('#wf-rows').html('<tr><td colspan="7" class="wf-empty" style="color:#ef4444;"><i class="ri-wifi-off-line" style="display:inline;font-size:16px;opacity:1;margin-right:4px;"></i> Failed to connect to server.</td></tr>');
    }
  });
};

function updateStats() {
  var active = 0, inactive = 0, oncreate = 0;
  allWorkflows.forEach(function(w) {
    if (String(w.status) === 'Active') active++; else inactive++;
    if (w.condition_type === 'on_create') oncreate++;
  });
  $('#stat-total').text(allWorkflows.length);
  $('#stat-active').text(active);
  $('#stat-inactive').text(inactive);
  $('#stat-oncreate').text(oncreate);
}

function renderTable(list) {
  if (!list.length) {
    $('#wf-rows').html('');
    $('#wf-empty').show();
      return;
    }
  $('#wf-empty').hide();
  var html = '';
  list.forEach(function(w, idx) {
    var date = w.date_entered ? new Date(w.date_entered).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
    var initial = (w.workflow_name || '?').charAt(0).toUpperCase();
    var condBadge = getConditionBadge(w);
    var statusBadge = String(w.status) === 'Active'
      ? '<span class="wf-badge wf-badge-active"><i class="ri-checkbox-circle-line"></i> Active</span>'
      : '<span class="wf-badge wf-badge-inactive"><i class="ri-pause-circle-line"></i> Inactive</span>';

    html += '<tr class="wf-animate-row" style="animation-delay:' + (idx * 25) + 'ms">';
    html += '<td><div class="flex items-center gap-3">';
    html += '<div class="wf-avatar bg-primary/10 text-primary">' + escHtml(initial) + '</div>';
    html += '<div><div style="font-weight:600;color:var(--wf-text);max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escHtml(w.workflow_name || 'Untitled') + '</div>';
    if (w.description) html += '<div style="font-size:11px;color:var(--wf-muted);max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escHtml(w.description) + '</div>';
    html += '</div></div></td>';
    html += '<td><span class="wf-module-chip"><i class="ri-database-2-line" style="font-size:12px;"></i> ' + escHtml(w.module_name || '—') + '</span></td>';
    html += '<td>' + condBadge + '</td>';
    html += '<td><div style="color:var(--wf-text);font-size:13px;font-weight:500;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escHtml(w.template_name || w.email_template_id || '—') + '</div></td>';
    html += '<td>' + statusBadge + '</td>';
    html += '<td style="color:var(--wf-muted);white-space:nowrap;font-size:12px;">' + date + '</td>';
    html += '<td><div class="flex items-center justify-center gap-1">';
    html += '<a href="edit_workflow.php?id=' + encodeURIComponent(w.id) + '" class="wf-action-btn" title="Edit"><i class="ri-edit-line"></i></a>';
    html += '<button class="wf-action-btn danger" title="Delete" onclick="deleteWorkflow(\'' + w.id + '\')"><i class="ri-delete-bin-line"></i></button>';
    html += '</div></td>';
    html += '</tr>';
  });
  $('#wf-rows').html(html);
}

function getConditionBadge(w) {
  var t = w.condition_type || '';
  if (t === 'on_create') return '<span class="wf-badge wf-badge-oncreate"><i class="ri-add-circle-line"></i> On Create</span>';
  if (t === 'status_equals') return '<span class="wf-badge wf-badge-status"><i class="ri-equalizer-line"></i> Status = ' + escHtml(w.status_value || '…') + '</span>';
  if (t === 'event_date') {
    var parts = (w.event_date_field || '').split('|');
    var dir = parts[1] || 'before';
    var days = parts[2] || '?';
    return '<span class="wf-badge wf-badge-event"><i class="ri-calendar-event-line"></i> ' + days + 'd ' + dir + ' event</span>';
  }
  return '<span class="wf-badge wf-badge-default">' + escHtml(t || '—') + '</span>';
}

window.deleteWorkflow = function(id) {
  Swal.fire({
    title: 'Delete workflow?', text: 'This action cannot be undone.', icon: 'warning',
    showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete', cancelButtonText: 'Cancel'
  }).then(function(res) {
    if (!res.isConfirmed) return;
    $.ajax({
      url: API, type: 'POST', data: { action: 'delete_workflow', id: id },
      success: function(response) {
        var d = typeof response === 'string' ? JSON.parse(response) : response;
        if (d.success) {
          Swal.fire({ icon: 'success', title: 'Deleted!', text: 'Workflow removed.', timer: 1200, showConfirmButton: false });
      loadWorkflows();
    } else {
          Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Failed to delete.' });
        }
      }
    });
  });
};

$('#wf-search').on('input', applyFilters);

$(document).ready(function() {
  loadWorkflows();
});

function escHtml(s) {
  var d = document.createElement('div');
  d.textContent = s || '';
  return d.innerHTML;
}
</script>
