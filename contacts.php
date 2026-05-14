<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>

<style>
  .ct-page { --ct-surface: #ffffff; --ct-surface-2: #f8fafc; --ct-border: rgba(15,23,42,0.08); --ct-text: #0f172a; --ct-muted: rgba(15,23,42,0.55); }
  .dark .ct-page { --ct-surface: rgba(255,255,255,0.035); --ct-surface-2: rgba(255,255,255,0.05); --ct-border: rgba(255,255,255,0.08); --ct-text: rgba(255,255,255,0.92); --ct-muted: rgba(255,255,255,0.50); }

  .ct-stat { background: var(--ct-surface); border: 1px solid var(--ct-border); border-radius: 16px; padding: 20px 24px; position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; }
  .ct-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(15,23,42,0.08); }
  .dark .ct-stat:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
  .ct-stat .ct-glow { position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; border-radius: 50%; opacity: 0.08; pointer-events: none; }
  .ct-stat .ct-stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
  .ct-stat .ct-stat-num { font-size: 28px; font-weight: 800; line-height: 1; color: var(--ct-text); letter-spacing: -0.02em; }
  .ct-stat .ct-stat-label { font-size: 12px; font-weight: 600; color: var(--ct-muted); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px; }

  .ct-table-card { background: var(--ct-surface); border: 1px solid var(--ct-border); border-radius: 16px; overflow: hidden; }
  .ct-toolbar { padding: 16px 20px; border-bottom: 1px solid var(--ct-border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
  .ct-search { height: 38px; border-radius: 10px; border: 1px solid var(--ct-border); background: var(--ct-surface-2); color: var(--ct-text); padding: 0 12px 0 36px!important; font-size: 13px; outline: none; width: min(320px, 100%); transition: border-color 0.2s, box-shadow 0.2s; }
  .ct-search:focus { border-color: rgb(var(--primary-rgb)); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12); }
  .ct-search-wrap { position: relative; }
  .ct-search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 15px; color: var(--ct-muted); pointer-events: none; }
  .ct-filter-btn { height: 34px; border-radius: 8px; border: 1px solid var(--ct-border); background: transparent; color: var(--ct-muted); padding: 0 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
  .ct-filter-btn:hover, .ct-filter-btn.active { background: rgba(var(--primary-rgb), 0.06); color: rgb(var(--primary-rgb)); border-color: rgba(var(--primary-rgb), 0.20); }

  .ct-table { width: 100%; border-collapse: collapse; }
  .ct-table th { text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--ct-muted); padding: 12px 16px; border-bottom: 1px solid var(--ct-border); white-space: nowrap; }
  .ct-table td { padding: 14px 16px; font-size: 13px; color: var(--ct-text); border-bottom: 1px solid rgba(15,23,42,0.04); vertical-align: middle; }
  .dark .ct-table td { border-bottom-color: rgba(255,255,255,0.04); }
  .ct-table tbody tr { transition: background 0.1s; cursor: pointer; }
  .ct-table tbody tr:hover { background: rgba(var(--primary-rgb), 0.03); }
  .ct-table tbody tr:last-child td { border-bottom: none; }

  .ct-avatar { width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0; }
  .ct-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 8px; text-transform: uppercase; letter-spacing: 0.04em; }

  .ct-action-btn { background: none; border: 1px solid var(--ct-border); border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; color: var(--ct-muted); font-size: 14px; text-decoration: none; }
  .ct-action-btn:hover { border-color: rgba(var(--primary-rgb),0.3); color: rgb(var(--primary-rgb)); background: rgba(var(--primary-rgb),0.04); }
  .ct-action-btn.danger:hover { border-color: rgba(239,68,68,0.3); color: #dc2626; background: rgba(239,68,68,0.04); }

  .ct-empty { text-align: center; padding: 60px 20px; color: var(--ct-muted); }
  .ct-empty i { display: block; font-size: 48px; opacity: 0.15; margin-bottom: 12px; }

  @keyframes ct-fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
  .ct-animate-row { animation: ct-fadeIn 0.25s ease forwards; }

  @media (max-width: 1024px) { #ct-stats { grid-template-columns: repeat(2, 1fr) !important; } }
  @media (max-width: 640px) { #ct-stats { grid-template-columns: 1fr !important; } }

  .ct-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9998; backdrop-filter:blur(4px); }
  .ct-overlay.open { display:block; }
  .ct-modal { display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:9999; width:640px; max-width:92vw; max-height:90vh; overflow-y:auto; background:var(--ct-surface); border:1px solid var(--ct-border); border-radius:20px; box-shadow:0 24px 64px rgba(0,0,0,.18); }
  .dark .ct-modal { background:#111827; }
  .ct-modal.open { display:block; }
  .ct-modal-head { padding:20px 24px; border-bottom:1px solid var(--ct-border); display:flex; align-items:center; justify-content:space-between; }
  .ct-modal-head h2 { margin:0; font-size:18px; font-weight:700; color:var(--ct-text); display:flex; align-items:center; gap:8px; }
  .ct-modal-close { width:36px; height:36px; border-radius:10px; border:1px solid var(--ct-border); background:transparent; color:var(--ct-muted); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; transition:all .15s; flex-shrink:0; }
  .ct-modal-close:hover { background:rgba(239,68,68,.08); color:#dc2626; border-color:rgba(239,68,68,.25); }
  .ct-modal-body { padding:24px; }
  .ct-modal-foot { padding:16px 24px; border-top:1px solid var(--ct-border); display:flex; align-items:center; justify-content:flex-end; gap:10px; }

  .ct-field { margin-bottom:16px; }
  .ct-field:last-child { margin-bottom:0; }
  .ct-label { display:block; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--ct-muted); margin-bottom:6px; }
  .ct-section-title { font-size:13px; font-weight:700; color:var(--ct-text); margin:20px 0 12px; padding-bottom:8px; border-bottom:1px solid var(--ct-border); display:flex; align-items:center; gap:6px; }
  .ct-section-title:first-child { margin-top:0; }
  .ct-input, .ct-select, .ct-textarea {
    width:100%; border:1px solid var(--ct-border); background:var(--ct-surface-2); color:var(--ct-text);
    border-radius:12px; padding:10px 14px; font-size:14px; height:44px; transition:border-color .2s, box-shadow .2s;
  }
  .ct-textarea { height:auto; min-height:80px; resize:vertical; }
  .ct-select {
    appearance:none; -webkit-appearance:none; padding-right:40px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m2 4 4 4 4-4'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right 14px center; background-size:12px; cursor:pointer;
  }
  .ct-input:focus, .ct-select:focus, .ct-textarea:focus { outline:none; border-color:rgb(var(--primary-rgb)); box-shadow:0 0 0 4px rgba(var(--primary-rgb),.12); }
  .ct-input.err-border, .ct-select.err-border, .ct-textarea.err-border { border-color:#ef4444; box-shadow:0 0 0 4px rgba(239,68,68,.1); }
  .ct-row { display:grid; grid-template-columns:1fr; gap:14px; }
  @media(min-width:600px) { .ct-row-2 { grid-template-columns:1fr 1fr; } .ct-row-3 { grid-template-columns:1fr 1fr 1fr; } }

  .ct-btn { border-radius:12px; padding:10px 20px; font-weight:600; font-size:14px; border:1px solid var(--ct-border); background:transparent; color:var(--ct-text); cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:6px; }
  .ct-btn:hover { background:rgba(var(--primary-rgb),.06); }
  .ct-btn-primary { background:rgb(var(--primary-rgb)); border-color:rgb(var(--primary-rgb)); color:#fff; }
  .ct-btn-primary:hover { opacity:.9; }
  .ct-btn:disabled { opacity:.55; cursor:not-allowed; }
</style>

<div class="main-content app-content">
  <div class="container-fluid ct-page">

    <div class="flex items-start justify-between flex-wrap gap-3 mb-6">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="ri-contacts-book-line text-primary text-base"></i>
          </div>
          <h1 class="page-title font-bold text-xl mb-0" style="color:var(--ct-text)">Contacts</h1>
        </div>
        <p class="text-xs mt-1 mb-0 ms-10" style="color:var(--ct-muted)">Manage all your contacts.</p>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" class="ct-action-btn" title="Refresh" onclick="loadContacts()" style="width:36px;height:36px;font-size:16px;">
          <i class="ri-refresh-line"></i>
        </button>
        <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Contacts', 'create') == 1): ?>
        <button type="button" onclick="openAddModal()" class="ti-btn ti-btn-sm bg-primary text-white font-semibold shadow-sm hover:shadow-md transition-all !rounded-xl px-4">
          <i class="ri-add-line me-1 text-base"></i> Add Contact
        </button>
        <?php endif; ?>
      </div>
    </div>

    <div id="ct-stats" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
      <div class="ct-stat">
        <div class="ct-glow" style="background:rgb(var(--primary-rgb));"></div>
        <div class="flex items-center justify-between gap-3">
          <div><div class="ct-stat-num" id="stat-total">--</div><div class="ct-stat-label">Total Contacts</div></div>
          <div class="ct-stat-icon bg-primary/10 text-primary"><i class="ri-contacts-book-line"></i></div>
        </div>
      </div>
      <div class="ct-stat">
        <div class="ct-glow" style="background:#10b981;"></div>
        <div class="flex items-center justify-between gap-3">
          <div><div class="ct-stat-num" id="stat-with-email">--</div><div class="ct-stat-label">With Email</div></div>
          <div class="ct-stat-icon" style="background:rgba(16,185,129,0.1);color:#10b981;"><i class="ri-mail-line"></i></div>
        </div>
      </div>
      <div class="ct-stat">
        <div class="ct-glow" style="background:#3b82f6;"></div>
        <div class="flex items-center justify-between gap-3">
          <div><div class="ct-stat-num" id="stat-with-phone">--</div><div class="ct-stat-label">With Phone</div></div>
          <div class="ct-stat-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6;"><i class="ri-phone-line"></i></div>
        </div>
      </div>
      <div class="ct-stat">
        <div class="ct-glow" style="background:#f59e0b;"></div>
        <div class="flex items-center justify-between gap-3">
          <div><div class="ct-stat-num" id="stat-recent">--</div><div class="ct-stat-label">This Month</div></div>
          <div class="ct-stat-icon" style="background:rgba(245,158,11,0.1);color:#f59e0b;"><i class="ri-calendar-line"></i></div>
        </div>
      </div>
    </div>

    <div class="ct-table-card mb-6">
      <div class="ct-toolbar">
        <div class="ct-search-wrap">
          <i class="ri-search-line"></i>
          <input type="text" class="ct-search" id="ct-search" placeholder="Search contacts by name, email, phone...">
        </div>
        <div class="flex items-center gap-2" id="ct-filter-bar">
          <button class="ct-filter-btn active" data-filter="all" onclick="setFilter('all',this)">All</button>
          <button class="ct-filter-btn" data-filter="with-email" onclick="setFilter('with-email',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#10b981;"></span> With Email</button>
          <button class="ct-filter-btn" data-filter="with-phone" onclick="setFilter('with-phone',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#3b82f6;"></span> With Phone</button>
          <button class="ct-filter-btn" data-filter="recent" onclick="setFilter('recent',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#f59e0b;"></span> This Month</button>
        </div>
      </div>
      <div class="overflow-auto">
        <table class="ct-table">
          <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Company</th><th>Created</th><th style="text-align:center;">Actions</th></tr></thead>
          <tbody id="ct-rows">
            <tr><td colspan="6" class="ct-empty"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading contacts...</td></tr>
          </tbody>
        </table>
        <div id="ct-empty" class="ct-empty" style="display:none;">
          <i class="ri-contacts-book-line"></i>
          <div style="font-size:14px;font-weight:600;margin-bottom:4px;">No contacts found</div>
          <div style="font-size:12px;">Try adjusting your search or filter, or add a new contact.</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Contact Modal -->
<div class="ct-overlay" id="addOverlay" onclick="closeAddModal()"></div>
<div class="ct-modal" id="addModal">
  <div class="ct-modal-head">
    <h2><i class="ri-user-add-line" style="color:rgb(var(--primary-rgb));"></i> Add Contact</h2>
    <button class="ct-modal-close" onclick="closeAddModal()"><i class="ri-close-line"></i></button>
  </div>
  <div class="ct-modal-body">
    <div class="ct-section-title"><i class="ri-user-line" style="color:rgb(var(--primary-rgb));"></i> Personal Info</div>
    <div class="ct-row ct-row-2">
      <div class="ct-field"><label class="ct-label">First Name <span style="color:#ef4444;">*</span></label><input type="text" id="add_first_name" class="ct-input" placeholder="First name" /></div>
      <div class="ct-field"><label class="ct-label">Last Name <span style="color:#ef4444;">*</span></label><input type="text" id="add_last_name" class="ct-input" placeholder="Last name" /></div>
    </div>
    <div class="ct-row ct-row-2">
      <div class="ct-field"><label class="ct-label">Title</label><input type="text" id="add_title" class="ct-input" placeholder="e.g. Manager" /></div>
      <div class="ct-field"><label class="ct-label">Department</label><input type="text" id="add_department" class="ct-input" placeholder="e.g. Sales" /></div>
    </div>

    <div class="ct-section-title"><i class="ri-phone-line" style="color:#3b82f6;"></i> Contact Details</div>
    <div class="ct-field"><label class="ct-label">Email</label><input type="email" id="add_email" class="ct-input" placeholder="email@example.com" /></div>
    <div class="ct-row ct-row-2">
      <div class="ct-field"><label class="ct-label">Mobile Phone</label><input type="text" id="add_phone_mobile" class="ct-input" placeholder="+1 (555) 000-0000" /></div>
      <div class="ct-field"><label class="ct-label">Work Phone</label><input type="text" id="add_phone_work" class="ct-input" placeholder="+1 (555) 000-0000" /></div>
    </div>

    <div class="ct-section-title"><i class="ri-map-pin-line" style="color:#f59e0b;"></i> Address</div>
    <div class="ct-field"><label class="ct-label">Street</label><input type="text" id="add_address_street" class="ct-input" placeholder="Street address" /></div>
    <div class="ct-row ct-row-3">
      <div class="ct-field"><label class="ct-label">City</label><input type="text" id="add_address_city" class="ct-input" placeholder="City" /></div>
      <div class="ct-field"><label class="ct-label">State</label><input type="text" id="add_address_state" class="ct-input" placeholder="State" /></div>
      <div class="ct-field"><label class="ct-label">Zip</label><input type="text" id="add_address_zip" class="ct-input" placeholder="Zip code" /></div>
    </div>
    <div class="ct-field"><label class="ct-label">Country</label><input type="text" id="add_address_country" class="ct-input" placeholder="Country" /></div>

    <div class="ct-section-title"><i class="ri-file-text-line" style="color:#8b5cf6;"></i> Additional</div>
    <div class="ct-row ct-row-2">
      <div class="ct-field">
        <label class="ct-label">Lead Source</label>
        <select id="add_lead_source" class="ct-select">
          <option value="">-- Select --</option>
          <option value="Web">Web</option>
          <option value="Email">Email</option>
          <option value="Call">Call</option>
          <option value="Referral">Referral</option>
          <option value="Partner">Partner</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="ct-field" style="display:flex;align-items:flex-end;">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--ct-text);font-weight:600;">
          <input type="checkbox" id="add_do_not_call" style="width:18px;height:18px;accent-color:rgb(var(--primary-rgb));border-radius:4px;" /> Do Not Call
        </label>
      </div>
    </div>
    <div class="ct-field"><label class="ct-label">Description</label><textarea id="add_description" class="ct-textarea" placeholder="Notes about this contact..."></textarea></div>
  </div>
  <div class="ct-modal-foot">
    <button class="ct-btn" onclick="closeAddModal()">Cancel</button>
    <button class="ct-btn ct-btn-primary" id="btnAddSave" onclick="saveContact()"><i class="ri-save-line"></i> Save Contact</button>
  </div>
</div>

<!-- Edit Contact Modal -->
<div class="ct-overlay" id="editOverlay" onclick="closeEditModal()"></div>
<div class="ct-modal" id="editModal">
  <div class="ct-modal-head">
    <h2><i class="ri-edit-line" style="color:rgb(var(--primary-rgb));"></i> Edit Contact</h2>
    <button class="ct-modal-close" onclick="closeEditModal()"><i class="ri-close-line"></i></button>
  </div>
  <div class="ct-modal-body">
    <input type="hidden" id="edit_id" />
    <div class="ct-section-title"><i class="ri-user-line" style="color:rgb(var(--primary-rgb));"></i> Personal Info</div>
    <div class="ct-row ct-row-2">
      <div class="ct-field"><label class="ct-label">First Name <span style="color:#ef4444;">*</span></label><input type="text" id="edit_first_name" class="ct-input" /></div>
      <div class="ct-field"><label class="ct-label">Last Name <span style="color:#ef4444;">*</span></label><input type="text" id="edit_last_name" class="ct-input" /></div>
    </div>
    <div class="ct-row ct-row-2">
      <div class="ct-field"><label class="ct-label">Title</label><input type="text" id="edit_title" class="ct-input" /></div>
      <div class="ct-field"><label class="ct-label">Department</label><input type="text" id="edit_department" class="ct-input" /></div>
    </div>

    <div class="ct-section-title"><i class="ri-phone-line" style="color:#3b82f6;"></i> Contact Details</div>
    <div class="ct-field"><label class="ct-label">Email</label><input type="email" id="edit_email" class="ct-input" /></div>
    <div class="ct-row ct-row-2">
      <div class="ct-field"><label class="ct-label">Mobile Phone</label><input type="text" id="edit_phone_mobile" class="ct-input" /></div>
      <div class="ct-field"><label class="ct-label">Work Phone</label><input type="text" id="edit_phone_work" class="ct-input" /></div>
    </div>

    <div class="ct-section-title"><i class="ri-map-pin-line" style="color:#f59e0b;"></i> Address</div>
    <div class="ct-field"><label class="ct-label">Street</label><input type="text" id="edit_address_street" class="ct-input" /></div>
    <div class="ct-row ct-row-3">
      <div class="ct-field"><label class="ct-label">City</label><input type="text" id="edit_address_city" class="ct-input" /></div>
      <div class="ct-field"><label class="ct-label">State</label><input type="text" id="edit_address_state" class="ct-input" /></div>
      <div class="ct-field"><label class="ct-label">Zip</label><input type="text" id="edit_address_zip" class="ct-input" /></div>
    </div>
    <div class="ct-field"><label class="ct-label">Country</label><input type="text" id="edit_address_country" class="ct-input" /></div>

    <div class="ct-section-title"><i class="ri-file-text-line" style="color:#8b5cf6;"></i> Additional</div>
    <div class="ct-row ct-row-2">
      <div class="ct-field">
        <label class="ct-label">Lead Source</label>
        <select id="edit_lead_source" class="ct-select">
          <option value="">-- Select --</option>
          <option value="Web">Web</option>
          <option value="Email">Email</option>
          <option value="Call">Call</option>
          <option value="Referral">Referral</option>
          <option value="Partner">Partner</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="ct-field" style="display:flex;align-items:flex-end;">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--ct-text);font-weight:600;">
          <input type="checkbox" id="edit_do_not_call" style="width:18px;height:18px;accent-color:rgb(var(--primary-rgb));border-radius:4px;" /> Do Not Call
        </label>
      </div>
    </div>
    <div class="ct-field"><label class="ct-label">Description</label><textarea id="edit_description" class="ct-textarea"></textarea></div>
  </div>
  <div class="ct-modal-foot">
    <button class="ct-btn" onclick="closeEditModal()">Cancel</button>
    <button class="ct-btn ct-btn-primary" id="btnEditSave" onclick="updateContact()"><i class="ri-save-line"></i> Update Contact</button>
  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var allContacts = [];
var _ctFilter = 'all';
var API = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';
var SESSION_USER_ID = '<?php echo $_SESSION['user']['id'] ?? ''; ?>';

function setFilter(type, btn) {
  _ctFilter = type;
  document.querySelectorAll('.ct-filter-btn').forEach(function(b) { b.classList.remove('active'); });
  if (btn) btn.classList.add('active');
  applyFilters();
}

function isThisMonth(dateStr) {
  if (!dateStr) return false;
  var d = new Date(dateStr);
  var now = new Date();
  return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear();
}

function applyFilters() {
  var q = ($('#ct-search').val() || '').toLowerCase().trim();
  var filtered = allContacts.filter(function(c) {
    var fullName = ((c.first_name || '') + ' ' + (c.last_name || '')).toLowerCase();
    var matchFilter = (_ctFilter === 'all')
      || (_ctFilter === 'with-email' && c.email_address)
      || (_ctFilter === 'with-phone' && (c.phone_mobile || c.phone_work))
      || (_ctFilter === 'recent' && isThisMonth(c.date_entered));
    var matchSearch = !q
      || fullName.indexOf(q) !== -1
      || (c.email_address || '').toLowerCase().indexOf(q) !== -1
      || (c.phone_mobile || '').indexOf(q) !== -1
      || (c.phone_work || '').indexOf(q) !== -1
      || (c.department || '').toLowerCase().indexOf(q) !== -1
      || (c.title || '').toLowerCase().indexOf(q) !== -1;
    return matchFilter && matchSearch;
  });
  renderTable(filtered);
}

window.loadContacts = function() {
  $('#ct-rows').html('<tr><td colspan="6" class="ct-empty"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading contacts...</td></tr>');
  $('#ct-empty').hide();
  $.ajax({
    url: API, type: 'POST',
    data: { action: 'fetch_contacts_list', user_id: SESSION_USER_ID , is_admin: "<?php echo $_SESSION['user']['admin'] == 1 ? '1' : '0'; ?>" },
    success: function(response) {
      try {
        var res = typeof response === 'string' ? JSON.parse(response) : response;
        allContacts = (res.contacts && Array.isArray(res.contacts)) ? res.contacts : [];
        updateStats();
        applyFilters();
      } catch (e) {
        allContacts = [];
        $('#ct-rows').html('<tr><td colspan="6" class="ct-empty" style="color:#ef4444;"><i class="ri-error-warning-line" style="display:inline;font-size:16px;opacity:1;margin-right:4px;"></i> Unable to parse response.</td></tr>');
      }
    },
    error: function() {
      $('#ct-rows').html('<tr><td colspan="6" class="ct-empty" style="color:#ef4444;"><i class="ri-wifi-off-line" style="display:inline;font-size:16px;opacity:1;margin-right:4px;"></i> Failed to connect to server.</td></tr>');
    }
  });
};

function updateStats() {
  var withEmail = 0, withPhone = 0, recent = 0;
  allContacts.forEach(function(c) {
    if (c.email_address) withEmail++;
    if (c.phone_mobile || c.phone_work) withPhone++;
    if (isThisMonth(c.date_entered)) recent++;
  });
  $('#stat-total').text(allContacts.length);
  $('#stat-with-email').text(withEmail);
  $('#stat-with-phone').text(withPhone);
  $('#stat-recent').text(recent);
}

function renderTable(list) {
  if (!list.length) { $('#ct-rows').html(''); $('#ct-empty').show(); return; }
  $('#ct-empty').hide();
  var html = '';
  list.forEach(function(c, idx) {
    var fullName = ((c.first_name || '') + ' ' + (c.last_name || '')).trim() || 'Unnamed';
    var initials = ((c.first_name || '?').charAt(0) + (c.last_name || '').charAt(0)).toUpperCase();
    var date = c.date_entered ? new Date(c.date_entered).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : '--';
    var phone = c.phone_mobile || c.phone_work || '--';
    var company = c.department || c.title || '--';

    html += '<tr class="ct-animate-row" style="animation-delay:' + (idx*20) + 'ms" onclick="onRowClick(event,\'' + c.id + '\')">';
    html += '<td><div class="flex items-center gap-3">';
    html += '<div class="ct-avatar bg-primary/10 text-primary">' + escHtml(initials) + '</div>';
    html += '<div><div style="font-weight:600;color:var(--ct-text);">' + escHtml(fullName) + '</div>';
    if (c.title) html += '<div style="font-size:11px;color:var(--ct-muted);">' + escHtml(c.title) + '</div>';
    html += '</div></div></td>';
    html += '<td style="font-size:12px;">' + (c.email_address ? '<a href="mailto:' + escHtml(c.email_address) + '" onclick="event.stopPropagation()" style="color:rgb(var(--primary-rgb));text-decoration:none;">' + escHtml(c.email_address) + '</a>' : '<span style="color:var(--ct-muted)">--</span>') + '</td>';
    html += '<td style="font-size:12px;white-space:nowrap;">' + escHtml(phone) + '</td>';
    html += '<td style="font-size:12px;color:var(--ct-muted);">' + escHtml(company) + '</td>';
    html += '<td style="color:var(--ct-muted);white-space:nowrap;font-size:12px;">' + date + '</td>';
    html += '<td><div class="flex items-center justify-center gap-1" onclick="event.stopPropagation()">';
    html += '<a href="contact_detail.php?id=' + c.id + '" class="ct-action-btn" title="View"><i class="ri-eye-line"></i></a>';
    <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Contacts', 'update') == 1): ?>
    html += '<button class="ct-action-btn" title="Edit" onclick="openEditModal(\'' + c.id + '\')"><i class="ri-edit-line"></i></button>';
    <?php endif; ?>
    <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Contacts', 'delete') == 1): ?>
    html += '<button class="ct-action-btn danger" title="Delete" onclick="deleteContact(\'' + c.id + '\')"><i class="ri-delete-bin-line"></i></button>';
    <?php endif; ?>
    html += '</div></td>';
    html += '</tr>';
  });
  $('#ct-rows').html(html);
}

function onRowClick(event, id) {
  if (event.target.closest('.ct-action-btn') || event.target.closest('button') || event.target.closest('a')) return;
  window.location.href = 'contact_detail.php?id=' + id;
}

function highlightField(id) {
  var el = document.getElementById(id);
  if (!el) return;
  el.classList.add('err-border');
  el.focus();
  setTimeout(function() { el.classList.remove('err-border'); }, 3000);
}

/* ── Add Modal ── */
function openAddModal() {
  ['add_first_name','add_last_name','add_title','add_department','add_email','add_phone_mobile','add_phone_work','add_address_street','add_address_city','add_address_state','add_address_zip','add_address_country','add_description'].forEach(function(id) { document.getElementById(id).value = ''; });
  document.getElementById('add_lead_source').value = '';
  document.getElementById('add_do_not_call').checked = false;
  document.getElementById('addOverlay').classList.add('open');
  document.getElementById('addModal').classList.add('open');
  setTimeout(function() { document.getElementById('add_first_name').focus(); }, 200);
}
function closeAddModal() {
  document.getElementById('addOverlay').classList.remove('open');
  document.getElementById('addModal').classList.remove('open');
}

function saveContact() {
  var fn = document.getElementById('add_first_name').value.trim();
  var ln = document.getElementById('add_last_name').value.trim();
  if (!fn) { highlightField('add_first_name'); return Swal.fire({icon:'warning',title:'Required',text:'First name is required.'}); }
  if (!ln) { highlightField('add_last_name'); return Swal.fire({icon:'warning',title:'Required',text:'Last name is required.'}); }

  var btn = document.getElementById('btnAddSave');
  btn.disabled = true;

  $.ajax({
    url: API, type: 'POST',
    data: {
      action: 'save_contact',
      first_name: fn, last_name: ln,
      title: document.getElementById('add_title').value.trim(),
      department: document.getElementById('add_department').value.trim(),
      email: document.getElementById('add_email').value.trim(),
      phone_mobile: document.getElementById('add_phone_mobile').value.trim(),
      phone_work: document.getElementById('add_phone_work').value.trim(),
      primary_address_street: document.getElementById('add_address_street').value.trim(),
      primary_address_city: document.getElementById('add_address_city').value.trim(),
      primary_address_state: document.getElementById('add_address_state').value.trim(),
      primary_address_postalcode: document.getElementById('add_address_zip').value.trim(),
      primary_address_country: document.getElementById('add_address_country').value.trim(),
      lead_source: document.getElementById('add_lead_source').value,
      do_not_call: document.getElementById('add_do_not_call').checked ? '1' : '0',
      description: document.getElementById('add_description').value.trim(),
      assigned_user_id: SESSION_USER_ID,
      created_by: SESSION_USER_ID
    },
    success: function(response) {
      var res = typeof response === 'string' ? JSON.parse(response) : response;
      if (res.success) {
        Swal.fire({icon:'success',title:'Created',text:'Contact saved successfully.',timer:1500,showConfirmButton:false});
        closeAddModal(); loadContacts();
      } else {
        Swal.fire({icon:'error',title:'Error',text:res.message || 'Failed to save.'});
      }
    },
    error: function() { Swal.fire({icon:'error',title:'Error',text:'Server connection failed.'}); },
    complete: function() { btn.disabled = false; }
  });
}

/* ── Edit Modal ── */
function openEditModal(id) {
  var c = allContacts.find(function(x) { return x.id === id; });
  if (!c) return;
  document.getElementById('edit_id').value = c.id;
  document.getElementById('edit_first_name').value = c.first_name || '';
  document.getElementById('edit_last_name').value = c.last_name || '';
  document.getElementById('edit_title').value = c.title || '';
  document.getElementById('edit_department').value = c.department || '';
  document.getElementById('edit_email').value = c.email_address || '';
  document.getElementById('edit_phone_mobile').value = c.phone_mobile || '';
  document.getElementById('edit_phone_work').value = c.phone_work || '';
  document.getElementById('edit_address_street').value = c.primary_address_street || '';
  document.getElementById('edit_address_city').value = c.primary_address_city || '';
  document.getElementById('edit_address_state').value = c.primary_address_state || '';
  document.getElementById('edit_address_zip').value = c.primary_address_postalcode || '';
  document.getElementById('edit_address_country').value = c.primary_address_country || '';
  document.getElementById('edit_lead_source').value = c.lead_source || '';
  document.getElementById('edit_do_not_call').checked = c.do_not_call === '1';
  document.getElementById('edit_description').value = c.description || '';
  document.getElementById('editOverlay').classList.add('open');
  document.getElementById('editModal').classList.add('open');
}
function closeEditModal() {
  document.getElementById('editOverlay').classList.remove('open');
  document.getElementById('editModal').classList.remove('open');
}

function updateContact() {
  var fn = document.getElementById('edit_first_name').value.trim();
  var ln = document.getElementById('edit_last_name').value.trim();
  if (!fn) { highlightField('edit_first_name'); return Swal.fire({icon:'warning',title:'Required',text:'First name is required.'}); }
  if (!ln) { highlightField('edit_last_name'); return Swal.fire({icon:'warning',title:'Required',text:'Last name is required.'}); }

  var btn = document.getElementById('btnEditSave');
  btn.disabled = true;

  $.ajax({
    url: API, type: 'POST',
    data: {
      action: 'update_contact',
      id: document.getElementById('edit_id').value,
      first_name: fn, last_name: ln,
      title: document.getElementById('edit_title').value.trim(),
      department: document.getElementById('edit_department').value.trim(),
      email: document.getElementById('edit_email').value.trim(),
      phone_mobile: document.getElementById('edit_phone_mobile').value.trim(),
      phone_work: document.getElementById('edit_phone_work').value.trim(),
      primary_address_street: document.getElementById('edit_address_street').value.trim(),
      primary_address_city: document.getElementById('edit_address_city').value.trim(),
      primary_address_state: document.getElementById('edit_address_state').value.trim(),
      primary_address_postalcode: document.getElementById('edit_address_zip').value.trim(),
      primary_address_country: document.getElementById('edit_address_country').value.trim(),
      lead_source: document.getElementById('edit_lead_source').value,
      do_not_call: document.getElementById('edit_do_not_call').checked ? '1' : '0',
      description: document.getElementById('edit_description').value.trim()
    },
    success: function(response) {
      var res = typeof response === 'string' ? JSON.parse(response) : response;
      if (res.success) {
        Swal.fire({icon:'success',title:'Updated',text:'Contact updated successfully.',timer:1500,showConfirmButton:false});
        closeEditModal(); loadContacts();
      } else {
        Swal.fire({icon:'error',title:'Error',text:res.message || 'Failed to update.'});
      }
    },
    error: function() { Swal.fire({icon:'error',title:'Error',text:'Server connection failed.'}); },
    complete: function() { btn.disabled = false; }
  });
}

/* ── Delete ── */
window.deleteContact = function(id) {
  Swal.fire({
    title: 'Delete contact?', text: 'This action cannot be undone.', icon: 'warning',
    showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete', cancelButtonText: 'Cancel'
  }).then(function(res) {
    if (!res.isConfirmed) return;
    $.ajax({
      url: API, type: 'POST', data: { action: 'delete_contact', id: id },
      success: function(response) {
        var d = typeof response === 'string' ? JSON.parse(response) : response;
        if (d.success) {
          Swal.fire({icon:'success',title:'Deleted!',text:'Contact removed.',timer:1200,showConfirmButton:false});
          loadContacts();
        } else { Swal.fire({icon:'error',title:'Error',text:d.message || 'Failed to delete.'}); }
      }
    });
  });
};

$('#ct-search').on('input', applyFilters);
$(document).ready(function() { loadContacts(); });

function escHtml(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
</script>
