<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>

<style>
  .nt-page { --nt-surface: #ffffff; --nt-surface-2: #f8fafc; --nt-border: rgba(15,23,42,0.08); --nt-text: #0f172a; --nt-muted: rgba(15,23,42,0.55); }
  .dark .nt-page { --nt-surface: rgba(255,255,255,0.035); --nt-surface-2: rgba(255,255,255,0.05); --nt-border: rgba(255,255,255,0.08); --nt-text: rgba(255,255,255,0.92); --nt-muted: rgba(255,255,255,0.50); }

  .nt-stat { background: var(--nt-surface); border: 1px solid var(--nt-border); border-radius: 16px; padding: 20px 24px; position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; }
  .nt-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(15,23,42,0.08); }
  .dark .nt-stat:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
  .nt-stat .nt-glow { position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; border-radius: 50%; opacity: 0.08; pointer-events: none; }
  .nt-stat .nt-stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
  .nt-stat .nt-stat-num { font-size: 28px; font-weight: 800; line-height: 1; color: var(--nt-text); letter-spacing: -0.02em; }
  .nt-stat .nt-stat-label { font-size: 12px; font-weight: 600; color: var(--nt-muted); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px; }

  .nt-table-card { background: var(--nt-surface); border: 1px solid var(--nt-border); border-radius: 16px; overflow: hidden; }
  .nt-toolbar { padding: 16px 20px; border-bottom: 1px solid var(--nt-border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
  .nt-search { height: 38px; border-radius: 10px; border: 1px solid var(--nt-border); background: var(--nt-surface-2); color: var(--nt-text); padding: 0 12px 0 36px; font-size: 13px; outline: none; width: min(320px, 100%); transition: border-color 0.2s, box-shadow 0.2s; }
  .nt-search:focus { border-color: rgb(var(--primary-rgb)); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12); }
  .nt-search-wrap { position: relative; }
  .nt-search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 15px; color: var(--nt-muted); pointer-events: none; }
  .nt-filter-btn { height: 34px; border-radius: 8px; border: 1px solid var(--nt-border); background: transparent; color: var(--nt-muted); padding: 0 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
  .nt-filter-btn:hover, .nt-filter-btn.active { background: rgba(var(--primary-rgb), 0.06); color: rgb(var(--primary-rgb)); border-color: rgba(var(--primary-rgb), 0.20); }

  .nt-table { width: 100%; border-collapse: collapse; }
  .nt-table th { text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--nt-muted); padding: 12px 16px; border-bottom: 1px solid var(--nt-border); white-space: nowrap; }
  .nt-table td { padding: 14px 16px; font-size: 13px; color: var(--nt-text); border-bottom: 1px solid rgba(15,23,42,0.04); vertical-align: middle; }
  .dark .nt-table td { border-bottom-color: rgba(255,255,255,0.04); }
  .nt-table tbody tr { transition: background 0.1s; cursor: pointer; }
  .nt-table tbody tr:hover { background: rgba(var(--primary-rgb), 0.03); }
  .nt-table tbody tr:last-child td { border-bottom: none; }

  .nt-avatar { width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
  .nt-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 8px; text-transform: uppercase; letter-spacing: 0.04em; }
  .nt-badge-lead { background: rgba(59,130,246,0.10); color: #2563eb; }
  .nt-badge-contact { background: rgba(139,92,246,0.10); color: #7c3aed; }
  .nt-badge-general { background: rgba(var(--primary-rgb),0.08); color: rgb(var(--primary-rgb)); }

  .nt-action-btn { background: none; border: 1px solid var(--nt-border); border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; color: var(--nt-muted); font-size: 14px; text-decoration: none; }
  .nt-action-btn:hover { border-color: rgba(var(--primary-rgb),0.3); color: rgb(var(--primary-rgb)); background: rgba(var(--primary-rgb),0.04); }
  .nt-action-btn.danger:hover { border-color: rgba(239,68,68,0.3); color: #dc2626; background: rgba(239,68,68,0.04); }

  .nt-empty { text-align: center; padding: 60px 20px; color: var(--nt-muted); }
  .nt-empty i { display: block; font-size: 48px; opacity: 0.15; margin-bottom: 12px; }

  @keyframes nt-fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
  .nt-animate-row { animation: nt-fadeIn 0.25s ease forwards; }

  @media (max-width: 1024px) { #nt-stats { grid-template-columns: repeat(2, 1fr) !important; } }
  @media (max-width: 640px) { #nt-stats { grid-template-columns: 1fr !important; } }

  .nt-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9998; backdrop-filter:blur(4px); }
  .nt-overlay.open { display:block; }
  .nt-modal { display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:9999; width:560px; max-width:92vw; max-height:90vh; overflow-y:auto; background:var(--nt-surface); border:1px solid var(--nt-border); border-radius:20px; box-shadow:0 24px 64px rgba(0,0,0,.18); }
  .dark .nt-modal { background:#111827; }
  .nt-modal.open { display:block; }
  .nt-modal-head { padding:20px 24px; border-bottom:1px solid var(--nt-border); display:flex; align-items:center; justify-content:space-between; }
  .nt-modal-head h2 { margin:0; font-size:18px; font-weight:700; color:var(--nt-text); display:flex; align-items:center; gap:8px; }
  .nt-modal-close { width:36px; height:36px; border-radius:10px; border:1px solid var(--nt-border); background:transparent; color:var(--nt-muted); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; transition:all .15s; flex-shrink:0; }
  .nt-modal-close:hover { background:rgba(239,68,68,.08); color:#dc2626; border-color:rgba(239,68,68,.25); }
  .nt-modal-body { padding:24px; }
  .nt-modal-foot { padding:16px 24px; border-top:1px solid var(--nt-border); display:flex; align-items:center; justify-content:flex-end; gap:10px; }

  .nt-field { margin-bottom:16px; }
  .nt-field:last-child { margin-bottom:0; }
  .nt-label { display:block; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--nt-muted); margin-bottom:6px; }
  .nt-input, .nt-select, .nt-textarea {
    width:100%; border:1px solid var(--nt-border); background:var(--nt-surface-2); color:var(--nt-text);
    border-radius:12px; padding:10px 14px; font-size:14px; height:44px; transition:border-color .2s, box-shadow .2s;
  }
  .nt-textarea { height:auto; min-height:100px; resize:vertical; }
  .nt-select {
    appearance:none; -webkit-appearance:none; padding-right:40px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m2 4 4 4 4-4'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right 14px center; background-size:12px; cursor:pointer;
  }
  .nt-input:focus, .nt-select:focus, .nt-textarea:focus { outline:none; border-color:rgb(var(--primary-rgb)); box-shadow:0 0 0 4px rgba(var(--primary-rgb),.12); }
  .nt-input.err-border, .nt-select.err-border, .nt-textarea.err-border { border-color:#ef4444; box-shadow:0 0 0 4px rgba(239,68,68,.1); }
  .nt-row { display:grid; grid-template-columns:1fr; gap:14px; }
  @media(min-width:600px) { .nt-row-2 { grid-template-columns:1fr 1fr; } }

  .nt-btn { border-radius:12px; padding:10px 20px; font-weight:600; font-size:14px; border:1px solid var(--nt-border); background:transparent; color:var(--nt-text); cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:6px; }
  .nt-btn:hover { background:rgba(var(--primary-rgb),.06); }
  .nt-btn-primary { background:rgb(var(--primary-rgb)); border-color:rgb(var(--primary-rgb)); color:#fff; }
  .nt-btn-primary:hover { opacity:.9; }
  .nt-btn:disabled { opacity:.55; cursor:not-allowed; }

  .nt-desc-preview { max-height:80px; overflow:hidden; text-overflow:ellipsis; font-size:12px; color:var(--nt-muted); line-height:1.5; }

  .nt-detail-row { display:flex; gap:12px; padding:12px 0; border-bottom:1px solid var(--nt-border); }
  .nt-detail-row:last-child { border-bottom:none; }
  .nt-detail-label { width:120px; flex-shrink:0; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--nt-muted); padding-top:2px; }
  .nt-detail-value { flex:1; font-size:14px; color:var(--nt-text); line-height:1.6; word-break:break-word; }
</style>

<div class="main-content app-content">
  <div class="container-fluid nt-page">

    <div class="flex items-start justify-between flex-wrap gap-3 mb-6">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="ri-sticky-note-line text-primary text-base"></i>
          </div>
          <h1 class="page-title font-bold text-xl mb-0" style="color:var(--nt-text)">Notes</h1>
        </div>
        <p class="text-xs mt-1 mb-0 ms-10" style="color:var(--nt-muted)">Manage all your notes.</p>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" class="nt-action-btn" title="Refresh" onclick="loadNotes()" style="width:36px;height:36px;font-size:16px;">
          <i class="ri-refresh-line"></i>
        </button>
        <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Notes', 'create') == 1): ?>
        <button type="button" onclick="openAddModal()" class="ti-btn ti-btn-sm bg-primary text-white font-semibold shadow-sm hover:shadow-md transition-all !rounded-xl px-4">
          <i class="ri-add-line me-1 text-base"></i> Add Note
        </button>
        <?php endif; ?>
      </div>
    </div>

    <div id="nt-stats" style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
      <div class="nt-stat">
        <div class="nt-glow" style="background:rgb(var(--primary-rgb));"></div>
        <div class="flex items-center justify-between gap-3">
          <div><div class="nt-stat-num" id="stat-total">--</div><div class="nt-stat-label">Total Notes</div></div>
          <div class="nt-stat-icon bg-primary/10 text-primary"><i class="ri-sticky-note-line"></i></div>
        </div>
      </div>
      <div class="nt-stat">
        <div class="nt-glow" style="background:#3b82f6;"></div>
        <div class="flex items-center justify-between gap-3">
          <div><div class="nt-stat-num" id="stat-leads">--</div><div class="nt-stat-label">Linked to Leads</div></div>
          <div class="nt-stat-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6;"><i class="ri-user-follow-line"></i></div>
        </div>
      </div>
      <div class="nt-stat">
        <div class="nt-glow" style="background:#8b5cf6;"></div>
        <div class="flex items-center justify-between gap-3">
          <div><div class="nt-stat-num" id="stat-contacts">--</div><div class="nt-stat-label">Linked to Contacts</div></div>
          <div class="nt-stat-icon" style="background:rgba(139,92,246,0.1);color:#8b5cf6;"><i class="ri-contacts-line"></i></div>
        </div>
      </div>
    </div>

    <div class="nt-table-card mb-6">
      <div class="nt-toolbar">
        <div class="nt-search-wrap">
          <i class="ri-search-line"></i>
          <input type="text" class="nt-search" id="nt-search" placeholder="Search notes by subject or content...">
        </div>
        <div class="flex items-center gap-2" id="nt-filter-bar">
          <button class="nt-filter-btn active" data-filter="all" onclick="setFilter('all',this)">All</button>
          <button class="nt-filter-btn" data-filter="leads" onclick="setFilter('leads',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#3b82f6;"></span> Leads</button>
          <button class="nt-filter-btn" data-filter="contacts" onclick="setFilter('contacts',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#8b5cf6;"></span> Contacts</button>
          <button class="nt-filter-btn" data-filter="general" onclick="setFilter('general',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#94a3b8;"></span> General</button>
        </div>
      </div>
      <div class="overflow-auto">
        <table class="nt-table">
          <thead><tr><th>Subject</th><th>Related To</th><th>Description</th><th>Created</th><th style="text-align:center;">Actions</th></tr></thead>
          <tbody id="nt-rows">
            <tr><td colspan="5" class="nt-empty"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading notes...</td></tr>
          </tbody>
        </table>
        <div id="nt-empty" class="nt-empty" style="display:none;">
          <i class="ri-sticky-note-line"></i>
          <div style="font-size:14px;font-weight:600;margin-bottom:4px;">No notes found</div>
          <div style="font-size:12px;">Try adjusting your search or filter, or create a new note.</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Note Modal -->
<div class="nt-overlay" id="addOverlay" onclick="closeAddModal()"></div>
<div class="nt-modal" id="addModal">
  <div class="nt-modal-head">
    <h2><i class="ri-add-circle-line" style="color:rgb(var(--primary-rgb));"></i> Add Note</h2>
    <button class="nt-modal-close" onclick="closeAddModal()"><i class="ri-close-line"></i></button>
  </div>
  <div class="nt-modal-body">
    <div class="nt-field">
      <label class="nt-label">Subject <span style="color:#ef4444;">*</span></label>
      <input type="text" id="add_subject" class="nt-input" placeholder="Note subject..." />
    </div>
    <div class="nt-row nt-row-2">
      <div class="nt-field">
        <label class="nt-label">Related Module</label>
        <select id="add_parent_type" class="nt-select" onchange="onParentTypeChange('add')">
          <option value="">None (General)</option>
          <option value="Leads">Leads</option>
          <option value="Contacts">Contacts</option>
        </select>
      </div>
      <div class="nt-field">
        <label class="nt-label">Related Record</label>
        <select id="add_parent_id" class="nt-select" disabled>
          <option value="">Select module first...</option>
        </select>
      </div>
    </div>
    <div class="nt-field">
      <label class="nt-label">Description</label>
      <textarea id="add_description" class="nt-textarea" placeholder="Write your note here..."></textarea>
    </div>
  </div>
  <div class="nt-modal-foot">
    <button class="nt-btn" onclick="closeAddModal()">Cancel</button>
    <button class="nt-btn nt-btn-primary" id="btnAddSave" onclick="saveNote()"><i class="ri-save-line"></i> Save Note</button>
  </div>
</div>

<!-- Edit Note Modal -->
<div class="nt-overlay" id="editOverlay" onclick="closeEditModal()"></div>
<div class="nt-modal" id="editModal">
  <div class="nt-modal-head">
    <h2><i class="ri-edit-line" style="color:rgb(var(--primary-rgb));"></i> Edit Note</h2>
    <button class="nt-modal-close" onclick="closeEditModal()"><i class="ri-close-line"></i></button>
  </div>
  <div class="nt-modal-body">
    <input type="hidden" id="edit_id" />
    <div class="nt-field">
      <label class="nt-label">Subject <span style="color:#ef4444;">*</span></label>
      <input type="text" id="edit_subject" class="nt-input" />
    </div>
    <div class="nt-row nt-row-2">
      <div class="nt-field">
        <label class="nt-label">Related Module</label>
        <select id="edit_parent_type" class="nt-select" onchange="onParentTypeChange('edit')">
          <option value="">None (General)</option>
          <option value="Leads">Leads</option>
          <option value="Contacts">Contacts</option>
        </select>
      </div>
      <div class="nt-field">
        <label class="nt-label">Related Record</label>
        <select id="edit_parent_id" class="nt-select" disabled>
          <option value="">Select module first...</option>
        </select>
      </div>
    </div>
    <div class="nt-field">
      <label class="nt-label">Description</label>
      <textarea id="edit_description" class="nt-textarea"></textarea>
    </div>
  </div>
  <div class="nt-modal-foot">
    <button class="nt-btn" onclick="closeEditModal()">Cancel</button>
    <button class="nt-btn nt-btn-primary" id="btnEditSave" onclick="updateNote()"><i class="ri-save-line"></i> Update Note</button>
  </div>
</div>

<!-- View Note Detail Modal -->
<div class="nt-overlay" id="viewOverlay" onclick="closeViewModal()"></div>
<div class="nt-modal" id="viewModal" style="width:600px;">
  <div class="nt-modal-head">
    <h2><i class="ri-file-text-line" style="color:rgb(var(--primary-rgb));"></i> Note Details</h2>
    <button class="nt-modal-close" onclick="closeViewModal()"><i class="ri-close-line"></i></button>
  </div>
  <div class="nt-modal-body" id="viewModalBody" style="padding:0 24px 24px;"></div>
  <div class="nt-modal-foot" id="viewModalFoot"></div>
</div>

<?php include_once "components/layout/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var allNotes = [];
var _ntFilter = 'all';
var API = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';
var SESSION_USER_ID = '<?php echo $_SESSION['user']['id'] ?? ''; ?>';

function setFilter(type, btn) {
  _ntFilter = type;
  document.querySelectorAll('.nt-filter-btn').forEach(function(b) { b.classList.remove('active'); });
  if (btn) btn.classList.add('active');
  applyFilters();
}

function applyFilters() {
  var q = ($('#nt-search').val() || '').toLowerCase().trim();
  var filtered = allNotes.filter(function(n) {
    var matchFilter = (_ntFilter === 'all')
      || (_ntFilter === 'leads' && n.parent_type === 'Leads')
      || (_ntFilter === 'contacts' && n.parent_type === 'Contacts')
      || (_ntFilter === 'general' && !n.parent_type);
    var matchSearch = !q
      || (n.name || '').toLowerCase().indexOf(q) !== -1
      || (n.description || '').toLowerCase().indexOf(q) !== -1
      || (n.parent_name || '').toLowerCase().indexOf(q) !== -1;
    return matchFilter && matchSearch;
  });
  renderTable(filtered);
}

window.loadNotes = function() {
  $('#nt-rows').html('<tr><td colspan="5" class="nt-empty"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading notes...</td></tr>');
  $('#nt-empty').hide();
  $.ajax({
    url: API, type: 'POST',
    data: { action: 'fetch_notes', user_id: SESSION_USER_ID , is_admin: "<?php echo $_SESSION['user']['admin'] == 1 ? '1' : '0'; ?>" },
    success: function(response) {
      try {
        var res = typeof response === 'string' ? JSON.parse(response) : response;
        allNotes = (res.notes && Array.isArray(res.notes)) ? res.notes : [];
        updateStats();
        applyFilters();
      } catch (e) {
        allNotes = [];
        $('#nt-rows').html('<tr><td colspan="5" class="nt-empty" style="color:#ef4444;"><i class="ri-error-warning-line" style="display:inline;font-size:16px;opacity:1;margin-right:4px;"></i> Unable to parse response.</td></tr>');
      }
    },
    error: function() {
      $('#nt-rows').html('<tr><td colspan="5" class="nt-empty" style="color:#ef4444;"><i class="ri-wifi-off-line" style="display:inline;font-size:16px;opacity:1;margin-right:4px;"></i> Failed to connect to server.</td></tr>');
    }
  });
};

function updateStats() {
  var leads = 0, contacts = 0;
  allNotes.forEach(function(n) {
    if (n.parent_type === 'Leads') leads++;
    if (n.parent_type === 'Contacts') contacts++;
  });
  $('#stat-total').text(allNotes.length);
  $('#stat-leads').text(leads);
  $('#stat-contacts').text(contacts);
}

function renderTable(list) {
  if (!list.length) { $('#nt-rows').html(''); $('#nt-empty').show(); return; }
  $('#nt-empty').hide();
  var html = '';
  list.forEach(function(n, idx) {
    var date = n.date_entered ? new Date(n.date_entered).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : '--';
    var initial = (n.name || '?').charAt(0).toUpperCase();
    var parentBadge = getParentBadge(n);
    var desc = escHtml((n.description || '').substring(0, 120));
    if ((n.description || '').length > 120) desc += '...';

    html += '<tr class="nt-animate-row" style="animation-delay:' + (idx*25) + 'ms" onclick="onRowClick(event,\'' + n.id + '\')">';
    html += '<td><div class="flex items-center gap-3">';
    html += '<div class="nt-avatar bg-primary/10 text-primary">' + escHtml(initial) + '</div>';
    html += '<div style="font-weight:600;color:var(--nt-text);max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escHtml(n.name || 'Untitled') + '</div>';
    html += '</div></td>';
    html += '<td>' + parentBadge + '</td>';
    html += '<td><div class="nt-desc-preview">' + (desc || '<span style="color:var(--nt-muted)">--</span>') + '</div></td>';
    html += '<td style="color:var(--nt-muted);white-space:nowrap;font-size:12px;">' + date + '</td>';
    html += '<td><div class="flex items-center justify-center gap-1" onclick="event.stopPropagation()">';
    html += '<button class="nt-action-btn" title="View" onclick="openViewModal(\'' + n.id + '\')"><i class="ri-eye-line"></i></button>';
    <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Notes', 'update') == 1): ?>
    html += '<button class="nt-action-btn" title="Edit" onclick="openEditModal(\'' + n.id + '\')"><i class="ri-edit-line"></i></button>';
    <?php endif; ?>
    <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Notes', 'delete') == 1): ?>
    html += '<button class="nt-action-btn danger" title="Delete" onclick="deleteNote(\'' + n.id + '\')"><i class="ri-delete-bin-line"></i></button>';
    <?php endif; ?>
    html += '</div></td>';
    html += '</tr>';
  });
  $('#nt-rows').html(html);
}

function onRowClick(event, id) {
  if (event.target.closest('.nt-action-btn') || event.target.closest('button')) return;
  openViewModal(id);
}

function getParentBadge(n) {
  if (n.parent_type === 'Leads') return '<span class="nt-badge nt-badge-lead"><i class="ri-user-follow-line"></i> ' + escHtml(n.parent_name || 'Lead') + '</span>';
  if (n.parent_type === 'Contacts') return '<span class="nt-badge nt-badge-contact"><i class="ri-contacts-line"></i> ' + escHtml(n.parent_name || 'Contact') + '</span>';
  return '<span class="nt-badge nt-badge-general"><i class="ri-sticky-note-line"></i> General</span>';
}

/* ── View Note Detail Modal ── */
function openViewModal(id) {
  var n = allNotes.find(function(x) { return x.id === id; });
  if (!n) return;

  var date = n.date_entered ? new Date(n.date_entered).toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric', hour:'2-digit', minute:'2-digit' }) : '--';
  var modDate = n.date_modified ? new Date(n.date_modified).toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric', hour:'2-digit', minute:'2-digit' }) : '--';

  var html = '<div style="padding-top:20px;">';
  html += '<div class="nt-detail-row"><div class="nt-detail-label">Subject</div><div class="nt-detail-value" style="font-weight:700;font-size:16px;">' + escHtml(n.name || 'Untitled') + '</div></div>';
  html += '<div class="nt-detail-row"><div class="nt-detail-label">Related To</div><div class="nt-detail-value">' + getParentBadge(n) + '</div></div>';
  html += '<div class="nt-detail-row"><div class="nt-detail-label">Description</div><div class="nt-detail-value" style="white-space:pre-wrap;">' + escHtml(n.description || 'No description provided.') + '</div></div>';
  html += '<div class="nt-detail-row"><div class="nt-detail-label">Created</div><div class="nt-detail-value">' + date + '</div></div>';
  html += '<div class="nt-detail-row"><div class="nt-detail-label">Modified</div><div class="nt-detail-value">' + modDate + '</div></div>';
  html += '</div>';

  document.getElementById('viewModalBody').innerHTML = html;

  var footHtml = '';
  footHtml += '<button class="nt-btn" onclick="closeViewModal();openEditModal(\'' + n.id + '\')"><i class="ri-edit-line"></i> Edit</button>';
  footHtml += '<button class="nt-btn" onclick="closeViewModal()">Close</button>';
  document.getElementById('viewModalFoot').innerHTML = footHtml;

  document.getElementById('viewOverlay').classList.add('open');
  document.getElementById('viewModal').classList.add('open');
}

function closeViewModal() {
  document.getElementById('viewOverlay').classList.remove('open');
  document.getElementById('viewModal').classList.remove('open');
}

/* ── Add/Edit Modal Controls ── */
function openAddModal() {
  document.getElementById('add_subject').value = '';
  document.getElementById('add_parent_type').value = '';
  document.getElementById('add_parent_id').innerHTML = '<option value="">Select module first...</option>';
  document.getElementById('add_parent_id').disabled = true;
  document.getElementById('add_description').value = '';
  document.getElementById('addOverlay').classList.add('open');
  document.getElementById('addModal').classList.add('open');
  setTimeout(function() { document.getElementById('add_subject').focus(); }, 200);
}
function closeAddModal() {
  document.getElementById('addOverlay').classList.remove('open');
  document.getElementById('addModal').classList.remove('open');
}

function openEditModal(id) {
  var n = allNotes.find(function(x) { return x.id === id; });
  if (!n) return;
  document.getElementById('edit_id').value = n.id;
  document.getElementById('edit_subject').value = n.name || '';
  document.getElementById('edit_parent_type').value = n.parent_type || '';
  document.getElementById('edit_description').value = n.description || '';
  if (n.parent_type) {
    loadParentRecords('edit', n.parent_type, n.parent_id || '');
  } else {
    document.getElementById('edit_parent_id').innerHTML = '<option value="">Select module first...</option>';
    document.getElementById('edit_parent_id').disabled = true;
  }
  document.getElementById('editOverlay').classList.add('open');
  document.getElementById('editModal').classList.add('open');
}
function closeEditModal() {
  document.getElementById('editOverlay').classList.remove('open');
  document.getElementById('editModal').classList.remove('open');
}

/* ── Parent record loading ── */
function onParentTypeChange(prefix) {
  var type = document.getElementById(prefix + '_parent_type').value;
  if (!type) {
    document.getElementById(prefix + '_parent_id').innerHTML = '<option value="">Select module first...</option>';
    document.getElementById(prefix + '_parent_id').disabled = true;
    return;
  }
  loadParentRecords(prefix, type, '');
}

function loadParentRecords(prefix, type, selectedId) {
  var dd = document.getElementById(prefix + '_parent_id');
  dd.disabled = false;
  dd.innerHTML = '<option value="">Loading...</option>';
  var action = type === 'Leads' ? 'fetchAllUserLeads' : 'fetch_contacts';
  $.ajax({
    url: API, type: 'POST',
    data: { action: action, id: SESSION_USER_ID },
    success: function(response) {
      var res = typeof response === 'string' ? JSON.parse(response) : response;
      var records = [];
      if (type === 'Leads') {
        records = Array.isArray(res) ? res : (res.data || []);
      } else {
        records = Array.isArray(res) ? res : (res.contacts || res.data || []);
      }
      dd.innerHTML = '<option value="">Select record...</option>';
      records.forEach(function(r) {
        var name = ((r.first_name || '') + ' ' + (r.last_name || '')).trim() || r.name || r.id;
        var opt = document.createElement('option');
        opt.value = r.id;
        opt.textContent = name;
        if (selectedId && String(selectedId) === String(r.id)) opt.selected = true;
        dd.appendChild(opt);
      });
    },
    error: function() { dd.innerHTML = '<option value="">Failed to load</option>'; }
  });
}

function highlightField(id) {
  var el = document.getElementById(id);
  if (!el) return;
  el.classList.add('err-border');
  el.focus();
  setTimeout(function() { el.classList.remove('err-border'); }, 3000);
}

/* ── Save Note ── */
function saveNote() {
  var subject = document.getElementById('add_subject').value.trim();
  if (!subject) { highlightField('add_subject'); return Swal.fire({icon:'warning',title:'Required',text:'Subject is required.'}); }

  var btn = document.getElementById('btnAddSave');
  btn.disabled = true;

  $.ajax({
    url: API, type: 'POST',
    data: {
      action: 'save_note',
      name: subject,
      description: document.getElementById('add_description').value.trim(),
      parent_type: document.getElementById('add_parent_type').value,
      parent_id: document.getElementById('add_parent_id').value,
      assigned_user_id: SESSION_USER_ID,
      created_by: SESSION_USER_ID
    },
    success: function(response) {
      var res = typeof response === 'string' ? JSON.parse(response) : response;
      if (res.success) {
        Swal.fire({icon:'success',title:'Created',text:'Note saved successfully.',timer:1500,showConfirmButton:false});
        closeAddModal(); loadNotes();
      } else {
        Swal.fire({icon:'error',title:'Error',text:res.message || 'Failed to save.'});
      }
    },
    error: function() { Swal.fire({icon:'error',title:'Error',text:'Server connection failed.'}); },
    complete: function() { btn.disabled = false; }
  });
}

/* ── Update Note ── */
function updateNote() {
  var subject = document.getElementById('edit_subject').value.trim();
  if (!subject) { highlightField('edit_subject'); return Swal.fire({icon:'warning',title:'Required',text:'Subject is required.'}); }

  var btn = document.getElementById('btnEditSave');
  btn.disabled = true;

  $.ajax({
    url: API, type: 'POST',
    data: {
      action: 'update_note',
      id: document.getElementById('edit_id').value,
      name: subject,
      description: document.getElementById('edit_description').value.trim(),
      parent_type: document.getElementById('edit_parent_type').value,
      parent_id: document.getElementById('edit_parent_id').value
    },
    success: function(response) {
      var res = typeof response === 'string' ? JSON.parse(response) : response;
      if (res.success) {
        Swal.fire({icon:'success',title:'Updated',text:'Note updated successfully.',timer:1500,showConfirmButton:false});
        closeEditModal(); loadNotes();
      } else {
        Swal.fire({icon:'error',title:'Error',text:res.message || 'Failed to update.'});
      }
    },
    error: function() { Swal.fire({icon:'error',title:'Error',text:'Server connection failed.'}); },
    complete: function() { btn.disabled = false; }
  });
}

/* ── Delete Note ── */
window.deleteNote = function(id) {
  Swal.fire({
    title: 'Delete note?', text: 'This action cannot be undone.', icon: 'warning',
    showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete', cancelButtonText: 'Cancel'
  }).then(function(res) {
    if (!res.isConfirmed) return;
    $.ajax({
      url: API, type: 'POST', data: { action: 'delete_note', id: id },
      success: function(response) {
        var d = typeof response === 'string' ? JSON.parse(response) : response;
        if (d.success) {
          Swal.fire({icon:'success',title:'Deleted!',text:'Note removed.',timer:1200,showConfirmButton:false});
          loadNotes();
        } else { Swal.fire({icon:'error',title:'Error',text:d.message || 'Failed to delete.'}); }
      }
    });
  });
};

$('#nt-search').on('input', applyFilters);
$(document).ready(function() { loadNotes(); });

function escHtml(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
</script>
