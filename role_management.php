<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<style>
  .rm-page { --rm-surface: #ffffff; --rm-surface-2: #f8fafc; --rm-border: rgba(15,23,42,0.08); --rm-text: #0f172a; --rm-muted: rgba(15,23,42,0.55); }
  .dark .rm-page { --rm-surface: rgba(255,255,255,0.035); --rm-surface-2: rgba(255,255,255,0.05); --rm-border: rgba(255,255,255,0.08); --rm-text: rgba(255,255,255,0.92); --rm-muted: rgba(255,255,255,0.50); }

  .rm-stat { background: var(--rm-surface); border: 1px solid var(--rm-border); border-radius: 16px; padding: 20px 24px; position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; }
  .rm-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(15,23,42,0.08); }
  .dark .rm-stat:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
  .rm-stat .rm-glow { position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; border-radius: 50%; opacity: 0.08; pointer-events: none; }
  .rm-stat .rm-stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
  .rm-stat .rm-stat-num { font-size: 28px; font-weight: 800; line-height: 1; color: var(--rm-text); letter-spacing: -0.02em; }
  .rm-stat .rm-stat-label { font-size: 12px; font-weight: 600; color: var(--rm-muted); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px; }

  .rm-table-card { background: var(--rm-surface); border: 1px solid var(--rm-border); border-radius: 16px; overflow: hidden; }
  .rm-toolbar { padding: 16px 20px; border-bottom: 1px solid var(--rm-border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
  .rm-search { height: 38px; border-radius: 10px; border: 1px solid var(--rm-border); background: var(--rm-surface-2); color: var(--rm-text); padding: 0 12px 0 36px; font-size: 13px; outline: none; width: min(320px, 100%); transition: border-color 0.2s, box-shadow 0.2s; }
  .rm-search:focus { border-color: rgb(var(--primary-rgb)); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12); }
  .rm-search-wrap { position: relative; }
  .rm-search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 15px; color: var(--rm-muted); pointer-events: none; }

  .rm-table { width: 100%; border-collapse: collapse; }
  .rm-table th { text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--rm-muted); padding: 12px 16px; border-bottom: 1px solid var(--rm-border); white-space: nowrap; }
  .rm-table td { padding: 14px 16px; font-size: 13px; color: var(--rm-text); border-bottom: 1px solid rgba(15,23,42,0.04); vertical-align: middle; }
  .dark .rm-table td { border-bottom-color: rgba(255,255,255,0.04); }
  .rm-table tbody tr { transition: background 0.1s; }
  .rm-table tbody tr:hover { background: rgba(var(--primary-rgb), 0.03); }
  .rm-table tbody tr:last-child td { border-bottom: none; }

  .rm-avatar { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; flex-shrink: 0; }
  .rm-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 8px; }
  .rm-badge-perm { background: rgba(var(--primary-rgb),0.08); color: rgb(var(--primary-rgb)); }
  .rm-badge-count { background: rgba(34,197,94,0.10); color: #16a34a; }

  .rm-action-btn { background: none; border: 1px solid var(--rm-border); border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; color: var(--rm-muted); font-size: 14px; }
  .rm-action-btn:hover { border-color: rgba(var(--primary-rgb),0.3); color: rgb(var(--primary-rgb)); background: rgba(var(--primary-rgb),0.04); }
  .rm-action-btn.danger:hover { border-color: rgba(239,68,68,0.3); color: #dc2626; background: rgba(239,68,68,0.04); }

  .rm-empty { text-align: center; padding: 50px 20px; color: var(--rm-muted); }

  @keyframes rm-fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
  .rm-animate-row { animation: rm-fadeIn 0.25s ease forwards; }

  .rm-perm-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
  .rm-perm-on { background: #22c55e; }
  .rm-perm-off { background: rgba(15,23,42,0.12); }
  .dark .rm-perm-off { background: rgba(255,255,255,0.12); }

  /* Modal styles */
  .rm-modal .ti-modal-box { max-width: 720px; }
  .rm-modal .ti-modal-content { border-radius: 20px !important; border: 1px solid var(--rm-border) !important; overflow: hidden; }
  .rm-modal .ti-modal-header { background: var(--rm-surface-2); border-bottom: 1px solid var(--rm-border); padding: 20px 24px; }
  .rm-modal .ti-modal-body { padding: 24px; background: var(--rm-surface); }
  .rm-modal .ti-modal-footer { border-top: 1px solid var(--rm-border); padding: 16px 24px; background: var(--rm-surface-2); }

  .rm-label { font-size: 11.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--rm-muted); margin-bottom: 6px; display: flex; align-items: center; gap: 4px; }
  .rm-label .rm-req { color: #ef4444; font-size: 14px; line-height: 1; }
  .rm-input-wrap { position: relative; }
  .rm-input-wrap .rm-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: var(--rm-muted); pointer-events: none; transition: color 0.2s; z-index: 1; }
  .rm-input { height: 42px; border-radius: 10px; border: 1px solid var(--rm-border); background: var(--rm-surface-2); color: var(--rm-text); padding: 0 12px 0 38px; width: 100%; font-size: 13px; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
  .rm-input:focus { border-color: rgb(var(--primary-rgb)); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.08); }
  .rm-input.is-invalid { border-color: #ef4444 !important; }
  textarea.rm-input { height: auto; padding: 10px 12px 10px 38px; resize: none; }
  .rm-error { font-size: 11px; color: #ef4444; margin-top: 4px; display: none; padding-left: 2px; font-weight: 500; }
  .rm-error.show { display: block; }

  .rm-perm-card { background: var(--rm-surface-2); border: 1px solid var(--rm-border); border-radius: 14px; overflow: hidden; margin-top: 16px; }
  .rm-perm-card .rm-perm-header { padding: 12px 16px; border-bottom: 1px solid var(--rm-border); display: flex; align-items: center; gap: 8px; }
  .rm-perm-table { width: 100%; border-collapse: collapse; }
  .rm-perm-table th { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: var(--rm-muted); padding: 10px 14px; text-align: center; }
  .rm-perm-table th:first-child { text-align: left; }
  .rm-perm-table td { padding: 10px 14px; font-size: 13px; font-weight: 500; color: var(--rm-text); border-top: 1px solid var(--rm-border); text-align: center; }
  .rm-perm-table td:first-child { text-align: left; }
  .rm-perm-table tbody tr:hover { background: rgba(var(--primary-rgb),0.03); }

  .rm-toggle { position: relative; width: 36px; height: 20px; display: inline-block; }
  .rm-toggle input { opacity: 0; width: 0; height: 0; }
  .rm-toggle .rm-slider { position: absolute; cursor: pointer; inset: 0; background: rgba(15,23,42,0.15); border-radius: 20px; transition: 0.2s; }
  .dark .rm-toggle .rm-slider { background: rgba(255,255,255,0.15); }
  .rm-toggle .rm-slider::before { content: ''; position: absolute; width: 16px; height: 16px; left: 2px; bottom: 2px; background: white; border-radius: 50%; transition: 0.2s; }
  .rm-toggle input:checked + .rm-slider { background: rgb(var(--primary-rgb)); }
  .rm-toggle input:checked + .rm-slider::before { transform: translateX(16px); }

  .rm-section-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; color: rgba(var(--primary-rgb),0.5); }
  .rm-divider { border: none; border-top: 1px solid var(--rm-border); margin: 4px 0; }
</style>

<div class="main-content app-content">
  <div class="container-fluid rm-page">

    <!-- Page Header -->
    <div class="flex items-start justify-between flex-wrap gap-3 mb-6">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="ri-shield-user-line text-primary text-base"></i>
          </div>
          <h1 class="page-title font-bold text-xl mb-0" style="color:var(--rm-text)">Role Management</h1>
        </div>
        <p class="text-xs mt-1 mb-0 ms-10" style="color:var(--rm-muted)">Manage user roles and their access permissions.</p>
      </div>
      <button type="button" class="ti-btn ti-btn-sm bg-primary text-white font-semibold shadow-sm hover:shadow-md transition-all !rounded-xl px-4" id="btn-add-role">
        <i class="ri-add-line me-1 text-base"></i> Add Role
      </button>
    </div>

    <!-- Stats -->
    <div id="rm-stats" style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
      <div class="rm-stat">
        <div class="rm-glow" style="background:rgb(var(--primary-rgb));"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="rm-stat-num" id="stat-total">—</div>
            <div class="rm-stat-label">Total Roles</div>
          </div>
          <div class="rm-stat-icon bg-primary/10 text-primary"><i class="ri-shield-star-line"></i></div>
        </div>
      </div>
      <div class="rm-stat">
        <div class="rm-glow" style="background:#22c55e;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="rm-stat-num" id="stat-modules">—</div>
            <div class="rm-stat-label">Modules</div>
          </div>
          <div class="rm-stat-icon bg-success/10 text-success"><i class="ri-apps-line"></i></div>
        </div>
      </div>
      <div class="rm-stat">
        <div class="rm-glow" style="background:#8b5cf6;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="rm-stat-num" id="stat-perms">—</div>
            <div class="rm-stat-label">Total Permissions</div>
          </div>
          <div class="rm-stat-icon" style="background:rgba(139,92,246,0.1);color:#8b5cf6;"><i class="ri-key-2-line"></i></div>
        </div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="rm-table-card mb-6">
      <div class="rm-toolbar">
        <div class="rm-search-wrap">
          <i class="ri-search-line"></i>
          <input type="text" class="rm-search" id="rm-search" placeholder="Search roles...">
        </div>
      </div>
      <div class="overflow-auto">
        <table class="rm-table">
          <thead>
            <tr>
              <th>Role Name</th>
              <th>Description</th>
              <th>Permissions</th>
              <th>Created</th>
              <th style="text-align:center;">Actions</th>
            </tr>
          </thead>
          <tbody id="roles-table-body">
            <tr><td colspan="5" class="rm-empty"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading roles...</td></tr>
          </tbody>
        </table>
        <div id="rm-empty" class="rm-empty" style="display:none;">
          <i class="ri-shield-star-line text-4xl mb-2" style="display:block;opacity:0.2;"></i>
          <div style="font-size:14px;font-weight:600;">No roles found</div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Role Modal -->
    <div class="hs-overlay ti-modal hidden rm-modal" id="role-modal">
      <div class="hs-overlay-open:mt-7 ti-modal-box mt-0 ease-out">
        <div class="ti-modal-content">
          <div class="ti-modal-header">
            <h6 class="ti-modal-title">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center"><i class="ri-shield-user-line text-primary text-lg"></i></div>
                <div>
                  <div style="font-size:15px;font-weight:700;color:var(--rm-text);" id="role-modal-title">Add Role</div>
                  <div style="font-size:11px;font-weight:500;color:var(--rm-muted);margin-top:1px;" id="role-modal-sub">Define role name and permissions</div>
                </div>
              </div>
            </h6>
            <button type="button" class="hs-dropdown-toggle ti-modal-close-btn" data-hs-overlay="#role-modal">
              <span class="sr-only">Close</span><i class="ri-close-line"></i>
            </button>
          </div>
          <div class="ti-modal-body">
            <input type="hidden" id="role-id">

            <div class="rm-section-label">Role Details</div>
            <hr class="rm-divider">
            <div class="grid grid-cols-12 gap-x-4 gap-y-3 mt-3">
              <div class="xl:col-span-6 col-span-12">
                <label class="rm-label">Role Name <span class="rm-req">*</span></label>
                <div class="rm-input-wrap">
                  <input type="text" class="rm-input" id="role-name" placeholder="E.g., Sales Manager" maxlength="60">
                  <i class="ri-shield-star-line rm-icon"></i>
                </div>
                <div class="rm-error" id="err-role-name"></div>
              </div>
              <div class="xl:col-span-6 col-span-12">
                <label class="rm-label">Description</label>
                <div class="rm-input-wrap">
                  <textarea class="rm-input" id="role-description" rows="1" placeholder="Brief description of the role"></textarea>
                  <i class="ri-file-text-line rm-icon" style="top:14px;transform:none;"></i>
                </div>
              </div>
            </div>

            <div class="rm-perm-card">
              <div class="rm-perm-header">
                <i class="ri-key-2-line text-primary"></i>
                <span style="font-size:12px;font-weight:700;color:var(--rm-text);">Access Permissions</span>
                <span style="margin-left:auto;font-size:10px;color:var(--rm-muted);font-weight:600;">Toggle access for each module</span>
              </div>
              <table class="rm-perm-table">
                <thead>
                  <tr>
                    <th>Module</th>
                    <th>Create</th>
                    <th>Read</th>
                    <th>Update</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody id="permissions-body"></tbody>
              </table>
            </div>
          </div>
          <div class="ti-modal-footer flex justify-between">
            <button type="button" class="hs-dropdown-toggle ti-btn btn-wave ti-btn-light !rounded-xl" data-hs-overlay="#role-modal">Cancel</button>
            <button type="button" class="ti-btn bg-primary text-white !rounded-xl px-5 font-semibold" id="btn-save-role"><i class="ri-save-line me-1"></i> Save Role</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/libs/preline/preline.js"></script>

<script>
const modules = ['Leads', 'Vehicles', 'Contacts', 'Notes', 'Agreements'];
let allRoles = [];
var API = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';

$(document).ready(function() {
  fetchRoles();

  $('#btn-add-role').click(function() {
    resetModal();
    openModal();
  });

  $('#btn-save-role').click(function() {
    saveRole();
  });

  $('#rm-search').on('input', function() {
    var q = $(this).val().toLowerCase().trim();
    var visible = 0;
    $('#roles-table-body tr.rm-row').each(function() {
      var match = $(this).attr('data-search').indexOf(q) !== -1;
      $(this).toggle(match);
      if (match) visible++;
    });
    $('#rm-empty').toggle(visible === 0);
  });
});

function fetchRoles() {
  var fd = new FormData();
  fd.append('action', 'fetch_roles');

  $('#roles-table-body').html('<tr><td colspan="5" class="rm-empty"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading roles...</td></tr>');

  $.ajax({
    url: API, type: 'POST', data: fd, contentType: false, processData: false,
    success: function(response) {
      var data = parseResponse(response);
      if (Array.isArray(data) && data.length > 0) {
        allRoles = data;
        renderTable(data);
        updateStats(data);
      } else {
        allRoles = [];
        $('#roles-table-body').html('');
        $('#rm-empty').show();
        updateStats([]);
      }
    },
    error: function() {
      $('#roles-table-body').html('<tr><td colspan="5" class="rm-empty" style="color:#ef4444;"><i class="ri-error-warning-line me-1"></i> Failed to load roles.</td></tr>');
    }
  });
}

function renderTable(data) {
  var rows = '';
  data.forEach(function(role, idx) {
    var date = new Date(role.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    var permCount = 0;
    if (role.permissions) {
      role.permissions.forEach(function(p) {
        if (p.can_create == 1) permCount++;
        if (p.can_read == 1) permCount++;
        if (p.can_update == 1) permCount++;
        if (p.can_delete == 1) permCount++;
      });
    }
    var searchStr = (role.name + ' ' + (role.description || '')).toLowerCase();
    rows += '<tr class="rm-animate-row rm-row" style="animation-delay:' + (idx * 30) + 'ms" data-search="' + searchStr.replace(/"/g, '') + '">';
    rows += '<td><div class="flex items-center gap-3">';
    rows += '<div class="rm-avatar bg-primary/10 text-primary">' + role.name.charAt(0).toUpperCase() + '</div>';
    rows += '<div style="font-weight:600;color:var(--rm-text);">' + escHtml(role.name) + '</div>';
    rows += '</div></td>';
    rows += '<td style="color:var(--rm-muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escHtml(role.description || '—') + '</td>';
    rows += '<td><span class="rm-badge rm-badge-count"><i class="ri-key-2-line me-1" style="font-size:12px;"></i>' + permCount + ' active</span></td>';
    rows += '<td style="color:var(--rm-muted);">' + date + '</td>';
    rows += '<td><div class="flex items-center justify-center gap-1">';
    rows += '<button class="rm-action-btn" title="Edit" onclick=\'editRole(' + JSON.stringify(role).replace(/'/g, "\\'") + ')\'><i class="ri-edit-line"></i></button>';
    rows += '<button class="rm-action-btn danger" title="Delete" onclick="deleteRole(\'' + role.id + '\')"><i class="ri-delete-bin-line"></i></button>';
    rows += '</div></td>';
    rows += '</tr>';
  });
  $('#roles-table-body').html(rows);
  $('#rm-empty').hide();
}

function updateStats(data) {
  $('#stat-total').text(data.length);
  $('#stat-modules').text(modules.length);
  var totalPerms = 0;
  data.forEach(function(r) {
    if (r.permissions) {
      r.permissions.forEach(function(p) {
        if (p.can_create == 1) totalPerms++;
        if (p.can_read == 1) totalPerms++;
        if (p.can_update == 1) totalPerms++;
        if (p.can_delete == 1) totalPerms++;
      });
    }
  });
  $('#stat-perms').text(totalPerms);
}

function buildPermToggle(mod, perm, checked) {
  var id = 'perm-' + mod.replace(/\s+/g, '-') + '-' + perm;
  return '<label class="rm-toggle"><input type="checkbox" id="' + id + '" data-module="' + mod + '" data-perm="' + perm + '"' + (checked ? ' checked' : '') + '><span class="rm-slider"></span></label>';
}

function resetModal() {
  $('#role-id').val('');
  $('#role-name').val('').removeClass('is-invalid');
  $('#role-description').val('');
  $('#role-modal-title').text('Create New Role');
  $('#role-modal-sub').text('Define role name and permissions');
  $('#btn-save-role').html('<i class="ri-save-line me-1"></i> Save Role');
  $('.rm-error').removeClass('show').text('');

  var rows = '';
  modules.forEach(function(mod) {
    rows += '<tr>';
    rows += '<td style="font-weight:600;">' + mod + '</td>';
    rows += '<td>' + buildPermToggle(mod, 'can_create', false) + '</td>';
    rows += '<td>' + buildPermToggle(mod, 'can_read', false) + '</td>';
    rows += '<td>' + buildPermToggle(mod, 'can_update', false) + '</td>';
    rows += '<td>' + buildPermToggle(mod, 'can_delete', false) + '</td>';
    rows += '</tr>';
  });
  $('#permissions-body').html(rows);
}

function openModal() {
  HSOverlay.open(document.querySelector('#role-modal'));
}

function closeModal() {
  HSOverlay.close(document.querySelector('#role-modal'));
}

function editRole(role) {
  resetModal();
  $('#role-id').val(role.id);
  $('#role-name').val(role.name);
  $('#role-description').val(role.description);
  $('#role-modal-title').text('Edit Role');
  $('#role-modal-sub').text('Update role details and permissions');
  $('#btn-save-role').html('<i class="ri-save-line me-1"></i> Update Role');

  if (role.permissions) {
    role.permissions.forEach(function(perm) {
      if (modules.indexOf(perm.module) !== -1) {
        $('input[data-module="' + perm.module + '"][data-perm="can_create"]').prop('checked', perm.can_create == 1);
        $('input[data-module="' + perm.module + '"][data-perm="can_read"]').prop('checked', perm.can_read == 1);
        $('input[data-module="' + perm.module + '"][data-perm="can_update"]').prop('checked', perm.can_update == 1);
        $('input[data-module="' + perm.module + '"][data-perm="can_delete"]').prop('checked', perm.can_delete == 1);
      }
    });
  }
  openModal();
}

function saveRole() {
  var id = $('#role-id').val();
  var name = $('#role-name').val().trim();
  var desc = $('#role-description').val().trim();

  $('#role-name').removeClass('is-invalid');
  $('.rm-error').removeClass('show').text('');

  if (!name) {
    $('#role-name').addClass('is-invalid');
    $('#err-role-name').text('Role name is required').addClass('show');
    $('#role-name').focus();
    return;
  }
  if (name.length < 2) {
    $('#role-name').addClass('is-invalid');
    $('#err-role-name').text('Min 2 characters').addClass('show');
    $('#role-name').focus();
    return;
  }

  var permissions = [];
  $('#permissions-body tr').each(function() {
    var row = $(this);
    var mod = row.find('td:first').text().trim();
    permissions.push({
      module: mod,
      can_create: row.find('input[data-perm="can_create"]').is(':checked'),
      can_read: row.find('input[data-perm="can_read"]').is(':checked'),
      can_update: row.find('input[data-perm="can_update"]').is(':checked'),
      can_delete: row.find('input[data-perm="can_delete"]').is(':checked')
    });
  });

  var action = id ? 'update_role' : 'create_role';
  var fd = new FormData();
  fd.append('action', action);
  if (id) fd.append('id', id);
  fd.append('name', name);
  fd.append('description', desc);
  fd.append('permissions', JSON.stringify(permissions));
  fd.append('created_by', '<?php echo $_SESSION['user']['id'] ?? 'Admin'; ?>');

  var btn = $('#btn-save-role'), orig = btn.html();
  btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm align-middle me-1"></span> Saving...');

  $.ajax({
    url: API, type: 'POST', data: fd, contentType: false, processData: false,
    success: function(response) {
      var d = parseResponse(response);
      if (d.success) {
        Swal.fire({ icon: 'success', title: 'Success!', text: d.message || 'Role saved.', timer: 1500, showConfirmButton: false });
        closeModal();
        fetchRoles();
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Failed to save role.' });
      }
      btn.prop('disabled', false).html(orig);
    },
    error: function() {
      Swal.fire({ icon: 'error', title: 'Server Error', text: 'Something went wrong.' });
      btn.prop('disabled', false).html(orig);
    }
  });
}

function deleteRole(id) {
  Swal.fire({
    title: 'Delete this role?', text: 'This action cannot be undone.', icon: 'warning',
    showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete', cancelButtonText: 'Cancel'
  }).then(function(res) {
    if (!res.isConfirmed) return;
    var fd = new FormData();
    fd.append('action', 'delete_role');
    fd.append('id', id);
    $.ajax({
      url: API, type: 'POST', data: fd, contentType: false, processData: false,
      success: function(response) {
        var d = parseResponse(response);
        if (d.success) {
          Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1500, showConfirmButton: false });
          fetchRoles();
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Failed to delete.' });
        }
      }
    });
  });
}

function parseResponse(response) {
  if (typeof response === 'string') {
    try { return JSON.parse(response); } catch (e) { return {}; }
  }
  return response;
}

function escHtml(s) {
  var d = document.createElement('div');
  d.textContent = s;
  return d.innerHTML;
}
</script>
