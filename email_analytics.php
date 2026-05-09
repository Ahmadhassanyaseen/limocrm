<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<style>
  .ea-page { --ea-surface: #ffffff; --ea-surface-2: #f8fafc; --ea-border: rgba(15,23,42,0.08); --ea-text: #0f172a; --ea-muted: rgba(15,23,42,0.55); }
  .dark .ea-page { --ea-surface: rgba(255,255,255,0.035); --ea-surface-2: rgba(255,255,255,0.05); --ea-border: rgba(255,255,255,0.08); --ea-text: rgba(255,255,255,0.92); --ea-muted: rgba(255,255,255,0.50); }

  .ea-stat { background: var(--ea-surface); border: 1px solid var(--ea-border); border-radius: 16px; padding: 20px 24px; position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; }
  .ea-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(15,23,42,0.08); }
  .dark .ea-stat:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
  .ea-stat .ea-glow { position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; border-radius: 50%; opacity: 0.08; pointer-events: none; }
  .ea-stat .ea-stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
  .ea-stat .ea-stat-num { font-size: 28px; font-weight: 800; line-height: 1; color: var(--ea-text); letter-spacing: -0.02em; }
  .ea-stat .ea-stat-label { font-size: 12px; font-weight: 600; color: var(--ea-muted); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px; }

  .ea-table-card { background: var(--ea-surface); border: 1px solid var(--ea-border); border-radius: 16px; overflow: hidden; }
  .ea-toolbar { padding: 16px 20px; border-bottom: 1px solid var(--ea-border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
  .ea-search { height: 38px; border-radius: 10px; border: 1px solid var(--ea-border); background: var(--ea-surface-2); color: var(--ea-text); padding: 0 12px 0 36px; font-size: 13px; outline: none; width: min(320px, 100%); transition: border-color 0.2s, box-shadow 0.2s; }
  .ea-search:focus { border-color: rgb(var(--primary-rgb)); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12); }
  .ea-search-wrap { position: relative; }
  .ea-search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 15px; color: var(--ea-muted); pointer-events: none; }
  .ea-filter-btn { height: 34px; border-radius: 8px; border: 1px solid var(--ea-border); background: transparent; color: var(--ea-muted); padding: 0 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
  .ea-filter-btn:hover, .ea-filter-btn.active { background: rgba(var(--primary-rgb), 0.06); color: rgb(var(--primary-rgb)); border-color: rgba(var(--primary-rgb), 0.20); }

  .ea-table { width: 100%; border-collapse: collapse; }
  .ea-table th { text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--ea-muted); padding: 12px 16px; border-bottom: 1px solid var(--ea-border); white-space: nowrap; }
  .ea-table td { padding: 14px 16px; font-size: 13px; color: var(--ea-text); border-bottom: 1px solid rgba(15,23,42,0.04); vertical-align: middle; }
  .dark .ea-table td { border-bottom-color: rgba(255,255,255,0.04); }
  .ea-table tbody tr { transition: background 0.1s; cursor: pointer; }
  .ea-table tbody tr:hover { background: rgba(var(--primary-rgb), 0.03); }
  .ea-table tbody tr:last-child td { border-bottom: none; }

  .ea-avatar { width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0; }
  .ea-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 8px; text-transform: uppercase; letter-spacing: 0.04em; }
  .ea-badge-sent { background: rgba(16,185,129,0.10); color: #059669; }
  .ea-badge-draft { background: rgba(245,158,11,0.10); color: #d97706; }
  .ea-badge-read { background: rgba(59,130,246,0.10); color: #2563eb; }
  .ea-badge-replied { background: rgba(139,92,246,0.10); color: #7c3aed; }
  .ea-badge-default { background: rgba(var(--primary-rgb),0.08); color: rgb(var(--primary-rgb)); }
  .ea-badge-opened { background: rgba(34,197,94,0.10); color: #16a34a; }
  .ea-badge-unsub { background: rgba(239,68,68,0.10); color: #dc2626; }

  .ea-lead-chip { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 8px; background: rgba(var(--primary-rgb),0.06); color: rgb(var(--primary-rgb)); cursor: pointer; transition: background 0.15s; text-decoration: none; }
  .ea-lead-chip:hover { background: rgba(var(--primary-rgb),0.12); color: rgb(var(--primary-rgb)); }

  .ea-type-out { color: #059669; }
  .ea-type-in { color: #2563eb; }

  .ea-action-btn { background: none; border: 1px solid var(--ea-border); border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; color: var(--ea-muted); font-size: 14px; text-decoration: none; }
  .ea-action-btn:hover { border-color: rgba(var(--primary-rgb),0.3); color: rgb(var(--primary-rgb)); background: rgba(var(--primary-rgb),0.04); }

  .ea-empty { text-align: center; padding: 60px 20px; color: var(--ea-muted); }
  .ea-empty i { display: block; font-size: 48px; opacity: 0.15; margin-bottom: 12px; }

  .ea-pagination { padding: 14px 20px; border-top: 1px solid var(--ea-border); display: flex; align-items: center; justify-content: space-between; gap: 12px; }
  .ea-page-info { font-size: 12px; color: var(--ea-muted); font-weight: 600; }
  .ea-page-btn { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--ea-border); background: transparent; color: var(--ea-muted); font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s; }
  .ea-page-btn:hover:not(:disabled) { border-color: rgba(var(--primary-rgb),0.3); color: rgb(var(--primary-rgb)); background: rgba(var(--primary-rgb),0.04); }
  .ea-page-btn:disabled { opacity: 0.35; cursor: default; }
  .ea-page-btn.active { background: rgb(var(--primary-rgb)); color: #fff; border-color: rgb(var(--primary-rgb)); }

  @keyframes ea-fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
  .ea-animate-row { animation: ea-fadeIn 0.25s ease forwards; }

  @media (max-width: 1280px) { #ea-stats { grid-template-columns: repeat(3, 1fr) !important; } }
  @media (max-width: 768px) { #ea-stats { grid-template-columns: repeat(2, 1fr) !important; } }
  @media (max-width: 480px) { #ea-stats { grid-template-columns: 1fr !important; } }
</style>

<div class="main-content app-content">
  <div class="container-fluid ea-page">

    <!-- Page Header -->
    <div class="flex items-start justify-between flex-wrap gap-3 mb-6">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="ri-bar-chart-grouped-line text-primary text-base"></i>
          </div>
          <h1 class="page-title font-bold text-xl mb-0" style="color:var(--ea-text)">Email Analytics</h1>
        </div>
        <p class="text-xs mt-1 mb-0 ms-10" style="color:var(--ea-muted)">Track all emails sent to leads assigned to you.</p>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" class="ea-action-btn" title="Refresh" onclick="loadEmails()" style="width:36px;height:36px;font-size:16px;">
          <i class="ri-refresh-line"></i>
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div id="ea-stats" style="display:grid;grid-template-columns:repeat(6,1fr);gap:16px;margin-bottom:24px;">
      <div class="ea-stat">
        <div class="ea-glow" style="background:rgb(var(--primary-rgb));"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="ea-stat-num" id="stat-total">—</div>
            <div class="ea-stat-label">Total Emails</div>
          </div>
          <div class="ea-stat-icon bg-primary/10 text-primary"><i class="ri-mail-line"></i></div>
        </div>
      </div>
      <div class="ea-stat">
        <div class="ea-glow" style="background:#10b981;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="ea-stat-num" id="stat-sent">—</div>
            <div class="ea-stat-label">Sent</div>
          </div>
          <div class="ea-stat-icon" style="background:rgba(16,185,129,0.1);color:#10b981;"><i class="ri-send-plane-line"></i></div>
        </div>
      </div>
      <div class="ea-stat">
        <div class="ea-glow" style="background:#3b82f6;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="ea-stat-num" id="stat-inbound">—</div>
            <div class="ea-stat-label">Inbound</div>
          </div>
          <div class="ea-stat-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6;"><i class="ri-inbox-line"></i></div>
        </div>
      </div>
      <div class="ea-stat">
        <div class="ea-glow" style="background:#22c55e;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="ea-stat-num" id="stat-opened">—</div>
            <div class="ea-stat-label">Opened</div>
          </div>
          <div class="ea-stat-icon" style="background:rgba(34,197,94,0.1);color:#22c55e;"><i class="ri-eye-line"></i></div>
        </div>
      </div>
      <div class="ea-stat">
        <div class="ea-glow" style="background:#ef4444;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="ea-stat-num" id="stat-unsub">—</div>
            <div class="ea-stat-label">Unsubscribed</div>
          </div>
          <div class="ea-stat-icon" style="background:rgba(239,68,68,0.1);color:#ef4444;"><i class="ri-mail-close-line"></i></div>
        </div>
      </div>
      <div class="ea-stat">
        <div class="ea-glow" style="background:#8b5cf6;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="ea-stat-num" id="stat-leads">—</div>
            <div class="ea-stat-label">Unique Leads</div>
          </div>
          <div class="ea-stat-icon" style="background:rgba(139,92,246,0.1);color:#8b5cf6;"><i class="ri-group-line"></i></div>
        </div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="ea-table-card mb-6">
      <div class="ea-toolbar">
        <div class="ea-search-wrap">
          <i class="ri-search-line"></i>
          <input type="text" class="ea-search" id="ea-search" placeholder="Search by subject, lead, or email...">
        </div>
        <div class="flex items-center gap-2" id="ea-filter-bar">
          <button class="ea-filter-btn active" data-filter="all" onclick="setFilter('all',this)">All</button>
          <button class="ea-filter-btn" data-filter="out" onclick="setFilter('out',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#10b981;"></span> Sent</button>
          <button class="ea-filter-btn" data-filter="in" onclick="setFilter('in',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#3b82f6;"></span> Inbound</button>
          <button class="ea-filter-btn" data-filter="draft" onclick="setFilter('draft',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#f59e0b;"></span> Draft</button>
          <button class="ea-filter-btn" data-filter="opened" onclick="setFilter('opened',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#22c55e;"></span> Opened</button>
          <button class="ea-filter-btn" data-filter="unsub" onclick="setFilter('unsub',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#ef4444;"></span> Unsubscribed</button>
        </div>
      </div>
      <div class="overflow-auto">
        <table class="ea-table">
          <thead>
            <tr>
              <th>Subject</th>
              <th>Lead</th>
              <th>From</th>
              <th>To</th>
              <th>Status</th>
              <th>Opened</th>
              <th>Unsub</th>
              <th>Date</th>
              <th style="text-align:center;">Action</th>
            </tr>
          </thead>
          <tbody id="email-list">
            <tr><td colspan="9" class="ea-empty"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading emails...</td></tr>
          </tbody>
        </table>
        <div id="ea-empty" class="ea-empty" style="display:none;">
          <i class="ri-mail-open-line"></i>
          <div style="font-size:14px;font-weight:600;margin-bottom:4px;">No emails found</div>
          <div style="font-size:12px;">Try adjusting your search or filter criteria.</div>
        </div>
      </div>
      <div class="ea-pagination" id="ea-pagination" style="display:none;">
        <div class="ea-page-info" id="ea-page-info"></div>
        <div class="flex items-center gap-1" id="ea-page-btns"></div>
      </div>
    </div>

  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var allEmails = [];
var filteredEmails = [];
var _eaFilter = 'all';
var _eaPage = 1;
var _eaPerPage = 15;
var API = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';

function setFilter(type, btn) {
  _eaFilter = type;
  _eaPage = 1;
  document.querySelectorAll('.ea-filter-btn').forEach(function(b) { b.classList.remove('active'); });
  if (btn) btn.classList.add('active');
  applyFilters();
}

function applyFilters() {
  var q = ($('#ea-search').val() || '').toLowerCase().trim();
  filteredEmails = allEmails.filter(function(e) {
    var matchType = (_eaFilter === 'all')
      || (_eaFilter === 'out' && e.type === 'out')
      || (_eaFilter === 'in' && (e.type === 'inbound' || e.type === 'in'))
      || (_eaFilter === 'draft' && e.status === 'draft')
      || (_eaFilter === 'opened' && e.opened_c && e.opened_c !== '')
      || (_eaFilter === 'unsub' && e.unsubscribe_c && e.unsubscribe_c !== '' && e.unsubscribe_c !== '0');
    var matchSearch = !q
      || (e.name || '').toLowerCase().indexOf(q) !== -1
      || (e.lead_name || '').toLowerCase().indexOf(q) !== -1
      || (e.from_addr || '').toLowerCase().indexOf(q) !== -1
      || (e.to_addrs || '').toLowerCase().indexOf(q) !== -1;
    return matchType && matchSearch;
  });
  renderPage();
}

window.loadEmails = function() {
  $('#email-list').html('<tr><td colspan="9" class="ea-empty"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading emails...</td></tr>');
  $('#ea-empty').hide();
  $('#ea-pagination').hide();

  $.ajax({
    url: API, type: 'POST',
    data: { action: 'fetch_user_email_analytics', user_id: '<?php echo $_SESSION['user']['id']; ?>' },
    success: function(response) {
      try {
        var res = typeof response === 'string' ? JSON.parse(response) : response;
        allEmails = (res.success && Array.isArray(res.emails)) ? res.emails : [];
        updateStats();
        applyFilters();
      } catch (err) {
        allEmails = [];
        $('#email-list').html('<tr><td colspan="9" class="ea-empty" style="color:#ef4444;"><i class="ri-error-warning-line" style="display:inline;font-size:16px;opacity:1;margin-right:4px;"></i> Unable to parse response.</td></tr>');
      }
    },
    error: function() {
      $('#email-list').html('<tr><td colspan="9" class="ea-empty" style="color:#ef4444;"><i class="ri-wifi-off-line" style="display:inline;font-size:16px;opacity:1;margin-right:4px;"></i> Failed to connect to server.</td></tr>');
    }
  });
};

function updateStats() {
  var sent = 0, inbound = 0, opened = 0, unsub = 0, leads = {};
  allEmails.forEach(function(e) {
    if (e.type === 'out') sent++;
    else if (e.type === 'inbound' || e.type === 'in') inbound++;
    if (e.opened_c && e.opened_c !== '') opened++;
    if (e.unsubscribe_c && e.unsubscribe_c !== '' && e.unsubscribe_c !== '0') unsub++;
    if (e.lead_id || e.parent_id) leads[e.lead_id || e.parent_id] = true;
  });
  $('#stat-total').text(allEmails.length);
  $('#stat-sent').text(sent);
  $('#stat-inbound').text(inbound);
  $('#stat-opened').text(opened);
  $('#stat-unsub').text(unsub);
  $('#stat-leads').text(Object.keys(leads).length);
}

function renderPage() {
  if (!filteredEmails.length) {
    $('#email-list').html('');
    $('#ea-empty').show();
    $('#ea-pagination').hide();
    return;
  }
  $('#ea-empty').hide();

  var totalPages = Math.ceil(filteredEmails.length / _eaPerPage);
  if (_eaPage > totalPages) _eaPage = totalPages;
  var start = (_eaPage - 1) * _eaPerPage;
  var pageItems = filteredEmails.slice(start, start + _eaPerPage);

  var html = '';
  pageItems.forEach(function(e, idx) {
    var date = e.date_sent_received || e.date_entered || '';
    if (date) {
      var d = new Date(date);
      date = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }
    var initial = (e.name || '?').charAt(0).toUpperCase();
    var statusBadge = getStatusBadge(e);
    var typeIcon = e.type === 'out'
      ? '<i class="ri-send-plane-line ea-type-out" title="Outbound" style="font-size:14px;"></i>'
      : '<i class="ri-inbox-line ea-type-in" title="Inbound" style="font-size:14px;"></i>';
    var leadId = e.lead_id || e.parent_id || '';

    html += '<tr class="ea-animate-row" style="animation-delay:' + (idx * 20) + 'ms" onclick="viewEmail(\'' + e.id + '\',\'' + leadId + '\')">';
    html += '<td><div class="flex items-center gap-3">';
    html += '<div class="ea-avatar bg-primary/10 text-primary">' + typeIcon + '</div>';
    html += '<div style="max-width:260px;"><div style="font-weight:600;color:var(--ea-text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escHtml(e.name || 'No Subject') + '</div></div>';
    html += '</div></td>';
    html += '<td>';
    if (e.lead_name && e.lead_name.trim()) {
      html += '<a href="lead.php?id=' + leadId + '" class="ea-lead-chip" onclick="event.stopPropagation();">';
      html += '<i class="ri-user-line" style="font-size:12px;"></i> ' + escHtml(e.lead_name);
      html += '</a>';
    } else { html += '<span style="color:var(--ea-muted);">—</span>'; }
    html += '</td>';
    html += '<td><div style="color:var(--ea-muted);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escHtml(e.from_addr || '—') + '</div></td>';
    html += '<td><div style="color:var(--ea-muted);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escHtml(e.to_addrs || '—') + '</div></td>';
    html += '<td>' + statusBadge + '</td>';
    html += '<td>';
    if (e.opened_c && e.opened_c !== '') {
      var od = new Date(e.opened_c);
      var openedStr = od.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ' ' + od.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
      html += '<span class="ea-badge ea-badge-opened"><i class="ri-eye-line"></i> ' + openedStr + '</span>';
    } else {
      html += '<span style="color:var(--ea-muted);font-size:11px;">—</span>';
    }
    html += '</td>';
    html += '<td>';
    if (e.unsubscribe_c && e.unsubscribe_c !== '' && e.unsubscribe_c !== '0') {
      html += '<span class="ea-badge ea-badge-unsub"><i class="ri-close-circle-line"></i> Yes</span>';
    } else {
      html += '<span style="color:var(--ea-muted);font-size:11px;">—</span>';
    }
    html += '</td>';
    html += '<td style="color:var(--ea-muted);white-space:nowrap;font-size:12px;">' + date + '</td>';
    html += '<td><div class="flex items-center justify-center"><a href="email_detail.php?id=' + e.id + '&lead_id=' + leadId + '" class="ea-action-btn" title="View Details" onclick="event.stopPropagation();"><i class="ri-eye-line"></i></a></div></td>';
    html += '</tr>';
  });
  $('#email-list').html(html);

  // Pagination
  if (totalPages > 1) {
    var startNum = start + 1;
    var endNum = Math.min(start + _eaPerPage, filteredEmails.length);
    $('#ea-page-info').text('Showing ' + startNum + '–' + endNum + ' of ' + filteredEmails.length + ' emails');
    var pgHtml = '<button class="ea-page-btn" onclick="goPage(' + (_eaPage - 1) + ')" ' + (_eaPage <= 1 ? 'disabled' : '') + '><i class="ri-arrow-left-s-line"></i></button>';
    var showPages = getPageRange(_eaPage, totalPages);
    showPages.forEach(function(p) {
      if (p === '...') { pgHtml += '<span style="padding:0 4px;color:var(--ea-muted);">...</span>'; }
      else { pgHtml += '<button class="ea-page-btn' + (p === _eaPage ? ' active' : '') + '" onclick="goPage(' + p + ')">' + p + '</button>'; }
    });
    pgHtml += '<button class="ea-page-btn" onclick="goPage(' + (_eaPage + 1) + ')" ' + (_eaPage >= totalPages ? 'disabled' : '') + '><i class="ri-arrow-right-s-line"></i></button>';
    $('#ea-page-btns').html(pgHtml);
    $('#ea-pagination').show();
  } else {
    $('#ea-pagination').hide();
  }
}

function getPageRange(current, total) {
  if (total <= 7) { var arr = []; for (var i = 1; i <= total; i++) arr.push(i); return arr; }
  if (current <= 3) return [1,2,3,4,'...',total];
  if (current >= total - 2) return [1,'...',total-3,total-2,total-1,total];
  return [1,'...',current-1,current,current+1,'...',total];
}

window.goPage = function(p) {
  var totalPages = Math.ceil(filteredEmails.length / _eaPerPage);
  if (p < 1 || p > totalPages) return;
  _eaPage = p;
  renderPage();
  document.querySelector('.ea-table-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
};

function getStatusBadge(e) {
  var s = (e.status || '').toLowerCase();
  if (s === 'sent') return '<span class="ea-badge ea-badge-sent"><i class="ri-check-line"></i> Sent</span>';
  if (s === 'read') return '<span class="ea-badge ea-badge-read"><i class="ri-eye-line"></i> Read</span>';
  if (s === 'replied') return '<span class="ea-badge ea-badge-replied"><i class="ri-reply-line"></i> Replied</span>';
  if (s === 'draft') return '<span class="ea-badge ea-badge-draft"><i class="ri-edit-line"></i> Draft</span>';
  return '<span class="ea-badge ea-badge-default">' + escHtml(s || 'Unknown') + '</span>';
}

window.viewEmail = function(emailId, leadId) {
  window.location.href = 'email_detail.php?id=' + emailId + '&lead_id=' + leadId;
};

$('#ea-search').on('input', function() { _eaPage = 1; applyFilters(); });

$(document).ready(function() {
  loadEmails();
});

function escHtml(s) {
  var d = document.createElement('div');
  d.textContent = s || '';
  return d.innerHTML;
}
</script>
