<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>
<?php
$data['id'] = $_SESSION['user']['id'];
$teamMembers = fetchAllTeamMembers($data);
$roles = fetchRoles();
if (!is_array($teamMembers)) { $teamMembers = []; }
if (!is_array($roles)) { $roles = []; }

$roleMap = [];
foreach ($roles as $r) { $roleMap[$r['id']] = $r['name']; }

$allUsers = [];
$allUsers[] = [
  'id' => $_SESSION['user']['id'],
  'first_name' => $_SESSION['user']['first_name'],
  'last_name' => $_SESSION['user']['last_name'],
  'email' => $_SESSION['user']['email'],
  'user_name' => $_SESSION['user']['user_name'],
  'status' => 'Active',
  'role_c' => '',
  'role_name' => 'Admin',
  'is_admin' => true,
];
foreach ($teamMembers as $m) {
  $allUsers[] = [
    'id' => $m['id'],
    'first_name' => $m['first_name'],
    'last_name' => $m['last_name'],
    'email' => $m['user_email_c'] ?? '',
    'user_name' => $m['user_name'],
    'status' => $m['status'] ?? 'Active',
    'role_c' => $m['role_c'] ?? '',
    'role_name' => $roleMap[$m['role_c'] ?? ''] ?? '—',
    'is_admin' => false,
  ];
}

$stats = ['total' => count($allUsers), 'active' => 0, 'inactive' => 0];
foreach ($allUsers as $u) {
  if (strtolower($u['status']) === 'active') { $stats['active']++; } else { $stats['inactive']++; }
}
?>

<style>
  .us-page { --us-surface: #ffffff; --us-surface-2: #f8fafc; --us-border: rgba(15,23,42,0.08); --us-text: #0f172a; --us-muted: rgba(15,23,42,0.55); }
  .dark .us-page { --us-surface: rgba(255,255,255,0.035); --us-surface-2: rgba(255,255,255,0.05); --us-border: rgba(255,255,255,0.08); --us-text: rgba(255,255,255,0.92); --us-muted: rgba(255,255,255,0.50); }

  .us-stat { background: var(--us-surface); border: 1px solid var(--us-border); border-radius: 16px; padding: 20px 24px; position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; }
  .us-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(15,23,42,0.08); }
  .dark .us-stat:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
  .us-stat .us-stat-glow { position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; border-radius: 50%; opacity: 0.08; pointer-events: none; }
  .us-stat .us-stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
  .us-stat .us-stat-num { font-size: 28px; font-weight: 800; line-height: 1; color: var(--us-text); letter-spacing: -0.02em; }
  .us-stat .us-stat-label { font-size: 12px; font-weight: 600; color: var(--us-muted); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px; }

  .us-table-card { background: var(--us-surface); border: 1px solid var(--us-border); border-radius: 16px; overflow: hidden; }
  .us-toolbar { padding: 16px 20px; border-bottom: 1px solid var(--us-border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
  .us-search { height: 38px; border-radius: 10px; border: 1px solid var(--us-border); background: var(--us-surface-2); color: var(--us-text); padding: 0 12px 0 36px; font-size: 13px; outline: none; width: min(320px, 100%); transition: border-color 0.2s, box-shadow 0.2s; }
  .us-search:focus { border-color: rgb(var(--primary-rgb)); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12); }
  .us-search-wrap { position: relative; }
  .us-search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 15px; color: var(--us-muted); pointer-events: none; }
  .us-filter-btn { height: 34px; border-radius: 8px; border: 1px solid var(--us-border); background: transparent; color: var(--us-muted); padding: 0 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
  .us-filter-btn:hover, .us-filter-btn.active { background: rgba(var(--primary-rgb), 0.06); color: rgb(var(--primary-rgb)); border-color: rgba(var(--primary-rgb), 0.20); }

  .us-table { width: 100%; border-collapse: collapse; }
  .us-table th { text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--us-muted); padding: 12px 16px; border-bottom: 1px solid var(--us-border); white-space: nowrap; }
  .us-table td { padding: 14px 16px; font-size: 13px; color: var(--us-text); border-bottom: 1px solid rgba(15,23,42,0.04); vertical-align: middle; }
  .dark .us-table td { border-bottom-color: rgba(255,255,255,0.04); }
  .us-table tbody tr { transition: background 0.1s; }
  .us-table tbody tr:hover { background: rgba(var(--primary-rgb), 0.03); }
  .us-table tbody tr:last-child td { border-bottom: none; }

  .us-avatar { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; flex-shrink: 0; }
  .us-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 8px; }
  .us-badge-active { background: rgba(34,197,94,0.10); color: #16a34a; }
  .us-badge-inactive { background: rgba(239,68,68,0.10); color: #dc2626; }
  .us-badge-role { background: rgba(var(--primary-rgb),0.08); color: rgb(var(--primary-rgb)); }
  .us-badge-admin { background: rgba(245,158,11,0.10); color: #d97706; }

  .us-action-btn { background: none; border: 1px solid var(--us-border); border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; color: var(--us-muted); font-size: 14px; }
  .us-action-btn:hover { border-color: rgba(var(--primary-rgb),0.3); color: rgb(var(--primary-rgb)); background: rgba(var(--primary-rgb),0.04); }
  .us-action-btn.danger:hover { border-color: rgba(239,68,68,0.3); color: #dc2626; background: rgba(239,68,68,0.04); }

  .us-empty { text-align: center; padding: 50px 20px; color: var(--us-muted); }

  @keyframes us-fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
  .us-animate-row { animation: us-fadeIn 0.25s ease forwards; }

  /* Modal Modern Styles */
  .um-modal .ti-modal-box { max-width: 580px; }
  .um-modal .ti-modal-content { border-radius: 20px !important; border: 1px solid var(--us-border) !important; overflow: hidden; }
  .um-modal .ti-modal-header { background: var(--us-surface-2); border-bottom: 1px solid var(--us-border); padding: 20px 24px; }
  .um-modal .ti-modal-title { font-size: 16px; font-weight: 700; color: var(--us-text); }
  .um-modal .ti-modal-body { padding: 24px; background: var(--us-surface); }
  .um-modal .ti-modal-footer { border-top: 1px solid var(--us-border); padding: 16px 24px; background: var(--us-surface-2); }

  .um-avatar-preview { width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 800; color: rgb(var(--primary-rgb)); background: rgba(var(--primary-rgb),0.08); border: 2px dashed rgba(var(--primary-rgb),0.2); margin: 0 auto 16px; transition: all 0.3s; letter-spacing: 1px; }
  .um-avatar-preview.has-text { border-style: solid; border-color: rgba(var(--primary-rgb),0.3); }

  .um-field { margin-bottom: 0; }
  .um-label { font-size: 11.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--us-muted); margin-bottom: 6px; display: flex; align-items: center; gap: 4px; }
  .um-label .um-req { color: #ef4444; font-size: 14px; line-height: 1; }
  .um-input-wrap { position: relative; }
  .um-input-wrap .um-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: var(--us-muted); pointer-events: none; transition: color 0.2s; z-index: 1; }
  .um-input { height: 42px; border-radius: 10px; border: 1px solid var(--us-border); background: var(--us-surface-2); color: var(--us-text); padding: 0 12px 0 38px; width: 100%; font-size: 13px; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
  .um-input:focus { border-color: rgb(var(--primary-rgb)); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.08); }
  .um-input:focus ~ .um-icon, .um-input:focus + .um-icon { color: rgb(var(--primary-rgb)); }
  .um-input.is-invalid { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,0.08) !important; }
  .um-input.is-invalid ~ .um-icon { color: #ef4444 !important; }
  .um-input.is-valid { border-color: #22c55e !important; }
  .um-input.is-valid ~ .um-icon { color: #22c55e !important; }
  select.um-input { cursor: pointer; -webkit-appearance: none; appearance: none; padding-right: 34px; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M8 11.4 2.6 6h10.8L8 11.4z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; }
  .um-error { font-size: 11px; color: #ef4444; margin-top: 4px; display: none; padding-left: 2px; font-weight: 500; }
  .um-error.show { display: block; }

  .um-pw-bar { height: 3px; border-radius: 3px; background: var(--us-border); margin-top: 6px; overflow: hidden; }
  .um-pw-fill { height: 100%; border-radius: 3px; width: 0; transition: width 0.3s, background 0.3s; }
  .um-pw-text { font-size: 10px; margin-top: 3px; font-weight: 600; text-align: right; }

  .um-divider { border: none; border-top: 1px solid var(--us-border); margin: 4px 0; }
  .um-section-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; color: rgba(var(--primary-rgb),0.5); margin-bottom: 4px; }

  .um-toggle-pw { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--us-muted); font-size: 16px; padding: 2px; z-index: 1; }
  .um-toggle-pw:hover { color: var(--us-text); }
</style>

<div class="main-content app-content">
  <div class="container-fluid us-page">

    <!-- Page Header -->
    <div class="flex items-start justify-between flex-wrap gap-3 mb-6">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="ri-team-line text-primary text-base"></i>
          </div>
          <h1 class="page-title font-bold text-xl mb-0" style="color:var(--us-text)">Users Management</h1>
        </div>
        <p class="text-xs mt-1 mb-0 ms-10" style="color:var(--us-muted)">Manage your team members, roles, and access.</p>
      </div>
      <button type="button" class="ti-btn ti-btn-sm bg-primary text-white font-semibold shadow-sm hover:shadow-md transition-all !rounded-xl px-4" data-hs-overlay="#create-user">
        <i class="ri-add-line me-1 text-base"></i> Add Member
      </button>
    </div>

    <!-- Stats -->
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;" class="us-stat-grid">
      <div class="us-stat">
        <div class="us-stat-glow" style="background:rgb(var(--primary-rgb));"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="us-stat-num"><?php echo $stats['total']; ?></div>
            <div class="us-stat-label">Total Members</div>
          </div>
          <div class="us-stat-icon bg-primary/10 text-primary"><i class="ri-team-line"></i></div>
        </div>
      </div>
      <div class="us-stat">
        <div class="us-stat-glow" style="background:#22c55e;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="us-stat-num"><?php echo $stats['active']; ?></div>
            <div class="us-stat-label">Active</div>
          </div>
          <div class="us-stat-icon bg-success/10 text-success"><i class="ri-checkbox-circle-line"></i></div>
        </div>
      </div>
      <div class="us-stat">
        <div class="us-stat-glow" style="background:#ef4444;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="us-stat-num"><?php echo $stats['inactive']; ?></div>
            <div class="us-stat-label">Inactive</div>
          </div>
          <div class="us-stat-icon bg-danger/10 text-danger"><i class="ri-close-circle-line"></i></div>
        </div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="us-table-card mb-6">
      <div class="us-toolbar">
        <div class="us-search-wrap">
          <i class="ri-search-line"></i>
          <input type="text" class="us-search" id="us-search" placeholder="Search members...">
        </div>
        <div class="flex items-center gap-2">
          <button class="us-filter-btn active" data-filter="all" onclick="filterUsers('all',this)">All</button>
          <button class="us-filter-btn" data-filter="Active" onclick="filterUsers('Active',this)"><span class="w-1.5 h-1.5 rounded-full bg-success inline-block"></span> Active</button>
          <button class="us-filter-btn" data-filter="Inactive" onclick="filterUsers('Inactive',this)"><span class="w-1.5 h-1.5 rounded-full bg-danger inline-block"></span> Inactive</button>
        </div>
      </div>
      <div class="overflow-auto">
        <table class="us-table">
          <thead>
            <tr>
              <th>Member</th>
              <th>Username</th>
              <th>Email</th>
              <th>Role</th>
              <th>Status</th>
              <th style="text-align:center;">Actions</th>
            </tr>
          </thead>
          <tbody id="us-tbody">
            <?php foreach ($allUsers as $idx => $u): ?>
            <tr class="us-animate-row us-row" style="animation-delay:<?php echo $idx * 30; ?>ms" data-status="<?php echo htmlspecialchars($u['status']); ?>" data-search="<?php echo htmlspecialchars(strtolower($u['first_name'].' '.$u['last_name'].' '.$u['email'].' '.$u['user_name'].' '.$u['role_name'])); ?>">
              <td>
                <div class="flex items-center gap-3">
                  <div class="us-avatar bg-primary/10 text-primary"><?php echo strtoupper(mb_substr($u['first_name'],0,1).mb_substr($u['last_name'],0,1)); ?></div>
                  <div>
                    <div style="font-weight:600;color:var(--us-text);"><?php echo htmlspecialchars($u['first_name'].' '.$u['last_name']); ?></div>
                  </div>
                </div>
              </td>
              <td style="color:var(--us-muted);"><?php echo htmlspecialchars($u['user_name']); ?></td>
              <td style="color:var(--us-muted);"><?php echo htmlspecialchars($u['email']); ?></td>
              <td>
                <span class="us-badge <?php echo $u['is_admin'] ? 'us-badge-admin' : 'us-badge-role'; ?>">
                  <?php echo htmlspecialchars($u['role_name']); ?>
                </span>
              </td>
              <td>
                <span class="us-badge <?php echo strtolower($u['status']) === 'active' ? 'us-badge-active' : 'us-badge-inactive'; ?>">
                  <i class="ri-circle-fill" style="font-size:6px;"></i>
                  <?php echo htmlspecialchars($u['status']); ?>
                </span>
              </td>
              <td>
                <div class="flex items-center justify-center gap-1">
                  <button class="us-action-btn edit-user-btn" title="Edit" data-hs-overlay="#edit-user"
                    data-id="<?php echo $u['id']; ?>"
                    data-first-name="<?php echo htmlspecialchars($u['first_name']); ?>"
                    data-last-name="<?php echo htmlspecialchars($u['last_name']); ?>"
                    data-email="<?php echo htmlspecialchars($u['email']); ?>"
                    data-user-name="<?php echo htmlspecialchars($u['user_name']); ?>"
                    data-role="<?php echo $u['is_admin'] ? 'Admin' : htmlspecialchars($u['role_c']); ?>"
                    data-status="<?php echo htmlspecialchars($u['status']); ?>">
                    <i class="ri-edit-line"></i>
                  </button>
                  <button class="us-action-btn danger delete-user-btn" title="Delete" data-id="<?php echo $u['id']; ?>">
                    <i class="ri-delete-bin-line"></i>
                  </button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div id="us-empty" class="us-empty" style="display:none;">
          <i class="ri-team-line text-4xl mb-2" style="display:block;opacity:0.2;"></i>
          <div style="font-size:14px;font-weight:600;">No members found</div>
        </div>
      </div>
    </div>

    <!-- Create User Modal -->
    <div class="hs-overlay ti-modal um-modal" id="create-user" tabindex="-1" aria-overlay="true">
      <div class="hs-overlay-open:mt-7 ti-modal-box mt-0 ease-out">
        <div class="ti-modal-content">
          <div class="ti-modal-header">
            <h6 class="ti-modal-title">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center"><i class="ri-user-add-line text-primary text-lg"></i></div>
                <div>
                  <div style="font-size:15px;font-weight:700;color:var(--us-text);">Add Team Member</div>
                  <div style="font-size:11px;font-weight:500;color:var(--us-muted);margin-top:1px;">Fill in the details below</div>
                </div>
              </div>
            </h6>
            <button type="button" class="hs-dropdown-toggle ti-modal-close-btn" data-hs-overlay="#create-user">
              <span class="sr-only">Close</span><i class="ri-close-line"></i>
            </button>
          </div>
          <div class="ti-modal-body">
            <!-- Avatar Preview -->
            <div class="um-avatar-preview" id="add-avatar-preview">?</div>

            <div class="um-section-label">Personal Information</div>
            <hr class="um-divider">
            <div class="grid grid-cols-12 gap-x-4 gap-y-3 mt-3">
              <div class="xl:col-span-6 col-span-12 um-field">
                <label class="um-label">First Name <span class="um-req">*</span></label>
                <div class="um-input-wrap">
                  <input type="text" class="um-input" id="first-name" placeholder="John" maxlength="50">
                  <i class="ri-user-line um-icon"></i>
                </div>
                <div class="um-error" id="err-first-name"></div>
              </div>
              <div class="xl:col-span-6 col-span-12 um-field">
                <label class="um-label">Last Name <span class="um-req">*</span></label>
                <div class="um-input-wrap">
                  <input type="text" class="um-input" id="last-name" placeholder="Doe" maxlength="50">
                  <i class="ri-user-line um-icon"></i>
                </div>
                <div class="um-error" id="err-last-name"></div>
              </div>
              <div class="col-span-12 um-field">
                <label class="um-label">Email Address <span class="um-req">*</span></label>
                <div class="um-input-wrap">
                  <input type="email" class="um-input" id="email" placeholder="john@example.com">
                  <i class="ri-mail-line um-icon"></i>
                </div>
                <div class="um-error" id="err-email"></div>
              </div>
            </div>

            <div class="um-section-label mt-5">Account Settings</div>
            <hr class="um-divider">
            <div class="grid grid-cols-12 gap-x-4 gap-y-3 mt-3">
              <div class="xl:col-span-6 col-span-12 um-field">
                <label class="um-label">Username <span class="um-req">*</span></label>
                <div class="um-input-wrap">
                  <input type="text" class="um-input" id="user-name" placeholder="johndoe" maxlength="30">
                  <i class="ri-at-line um-icon"></i>
                </div>
                <div class="um-error" id="err-user-name"></div>
              </div>
              <div class="xl:col-span-6 col-span-12 um-field">
                <label class="um-label">Password <span class="um-req">*</span></label>
                <div class="um-input-wrap">
                  <input type="password" class="um-input" id="password" placeholder="Min 8 characters">
                  <i class="ri-lock-line um-icon"></i>
                  <button type="button" class="um-toggle-pw" onclick="togglePw('password',this)"><i class="ri-eye-off-line"></i></button>
                </div>
                <div class="um-pw-bar"><div class="um-pw-fill" id="pw-strength-fill"></div></div>
                <div class="um-pw-text" id="pw-strength-text" style="color:var(--us-muted)"></div>
                <div class="um-error" id="err-password"></div>
              </div>
              <div class="xl:col-span-6 col-span-12 um-field">
                <label class="um-label">Role <span class="um-req">*</span></label>
                <div class="um-input-wrap">
                  <select class="um-input" id="role">
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                      <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                  <i class="ri-shield-user-line um-icon"></i>
                </div>
                <div class="um-error" id="err-role"></div>
              </div>
              <div class="xl:col-span-6 col-span-12 um-field">
                <label class="um-label">Status</label>
                <div class="um-input-wrap">
                  <select class="um-input" id="Status">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                  </select>
                  <i class="ri-toggle-line um-icon"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="ti-modal-footer flex justify-between">
            <button type="button" class="hs-dropdown-toggle ti-btn btn-wave ti-btn-light !rounded-xl" data-hs-overlay="#create-user">Cancel</button>
            <button type="button" class="ti-btn bg-primary text-white !rounded-xl px-5 font-semibold" id="btn-add-user"><i class="ri-user-add-line me-1"></i> Add Member</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit User Modal -->
    <div class="hs-overlay ti-modal um-modal" id="edit-user" tabindex="-1" aria-overlay="true">
      <div class="hs-overlay-open:mt-7 ti-modal-box mt-0 ease-out">
        <div class="ti-modal-content">
          <div class="ti-modal-header">
            <h6 class="ti-modal-title">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center"><i class="ri-edit-line text-primary text-lg"></i></div>
                <div>
                  <div style="font-size:15px;font-weight:700;color:var(--us-text);">Edit Team Member</div>
                  <div style="font-size:11px;font-weight:500;color:var(--us-muted);margin-top:1px;">Update member details</div>
                </div>
              </div>
            </h6>
            <button type="button" class="hs-dropdown-toggle ti-modal-close-btn" data-hs-overlay="#edit-user">
              <span class="sr-only">Close</span><i class="ri-close-line"></i>
            </button>
          </div>
          <div class="ti-modal-body">
            <input type="hidden" id="edit-user-id">
            <!-- Avatar Preview -->
            <div class="um-avatar-preview" id="edit-avatar-preview">?</div>

            <div class="um-section-label">Personal Information</div>
            <hr class="um-divider">
            <div class="grid grid-cols-12 gap-x-4 gap-y-3 mt-3">
              <div class="xl:col-span-6 col-span-12 um-field">
                <label class="um-label">First Name <span class="um-req">*</span></label>
                <div class="um-input-wrap">
                  <input type="text" class="um-input" id="edit-first-name" placeholder="First Name" maxlength="50">
                  <i class="ri-user-line um-icon"></i>
                </div>
                <div class="um-error" id="err-edit-first-name"></div>
              </div>
              <div class="xl:col-span-6 col-span-12 um-field">
                <label class="um-label">Last Name <span class="um-req">*</span></label>
                <div class="um-input-wrap">
                  <input type="text" class="um-input" id="edit-last-name" placeholder="Last Name" maxlength="50">
                  <i class="ri-user-line um-icon"></i>
                </div>
                <div class="um-error" id="err-edit-last-name"></div>
              </div>
              <div class="col-span-12 um-field">
                <label class="um-label">Email Address <span class="um-req">*</span></label>
                <div class="um-input-wrap">
                  <input type="email" class="um-input" id="edit-email" placeholder="Email">
                  <i class="ri-mail-line um-icon"></i>
                </div>
                <div class="um-error" id="err-edit-email"></div>
              </div>
            </div>

            <div class="um-section-label mt-5">Account Settings</div>
            <hr class="um-divider">
            <div class="grid grid-cols-12 gap-x-4 gap-y-3 mt-3">
              <div class="xl:col-span-6 col-span-12 um-field">
                <label class="um-label">Username <span class="um-req">*</span></label>
                <div class="um-input-wrap">
                  <input type="text" class="um-input" id="edit-user-name" placeholder="Username" maxlength="30">
                  <i class="ri-at-line um-icon"></i>
                </div>
                <div class="um-error" id="err-edit-user-name"></div>
              </div>
              <div class="xl:col-span-6 col-span-12 um-field">
                <label class="um-label">Password <span style="font-weight:400;text-transform:none;letter-spacing:0;font-size:10px;color:var(--us-muted);">(leave empty to keep)</span></label>
                <div class="um-input-wrap">
                  <input type="password" class="um-input" id="edit-password" placeholder="New password">
                  <i class="ri-lock-line um-icon"></i>
                  <button type="button" class="um-toggle-pw" onclick="togglePw('edit-password',this)"><i class="ri-eye-off-line"></i></button>
                </div>
                <div class="um-pw-bar"><div class="um-pw-fill" id="edit-pw-strength-fill"></div></div>
                <div class="um-pw-text" id="edit-pw-strength-text" style="color:var(--us-muted)"></div>
                <div class="um-error" id="err-edit-password"></div>
              </div>
              <div class="xl:col-span-6 col-span-12 um-field">
                <label class="um-label">Role</label>
                <div class="um-input-wrap">
                  <select class="um-input" id="edit-role">
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                      <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                  <i class="ri-shield-user-line um-icon"></i>
                </div>
              </div>
              <div class="xl:col-span-6 col-span-12 um-field">
                <label class="um-label">Status</label>
                <div class="um-input-wrap">
                  <select class="um-input" id="edit-status">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                  </select>
                  <i class="ri-toggle-line um-icon"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="ti-modal-footer flex justify-between">
            <button type="button" class="hs-dropdown-toggle ti-btn btn-wave ti-btn-light !rounded-xl" data-hs-overlay="#edit-user">Cancel</button>
            <button type="button" class="ti-btn bg-primary text-white !rounded-xl px-5 font-semibold" id="btn-save-user"><i class="ri-save-line me-1"></i> Save Changes</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var _usFilter = 'all';

function filterUsers(status, btn) {
  _usFilter = status;
  document.querySelectorAll('.us-filter-btn').forEach(function(b) { b.classList.remove('active'); });
  if (btn) btn.classList.add('active');
  applyUserFilters();
}

function applyUserFilters() {
  var q = (document.getElementById('us-search').value || '').toLowerCase().trim();
  var rows = document.querySelectorAll('.us-row');
  var visible = 0;
  rows.forEach(function(row) {
    var matchStatus = (_usFilter === 'all' || row.dataset.status === _usFilter);
    var matchSearch = (!q || row.dataset.search.indexOf(q) !== -1);
    row.style.display = (matchStatus && matchSearch) ? '' : 'none';
    if (matchStatus && matchSearch) visible++;
  });
  document.getElementById('us-empty').style.display = visible === 0 ? '' : 'none';
}

document.getElementById('us-search').addEventListener('input', applyUserFilters);

function togglePw(id, btn) {
  var inp = document.getElementById(id);
  var ico = btn.querySelector('i');
  if (inp.type === 'password') { inp.type = 'text'; ico.className = 'ri-eye-line'; }
  else { inp.type = 'password'; ico.className = 'ri-eye-off-line'; }
}

function checkPwStrength(pw, fillEl, textEl) {
  if (!pw) { fillEl.style.width = '0'; textEl.textContent = ''; return; }
  var score = 0;
  if (pw.length >= 8) score++;
  if (pw.length >= 12) score++;
  if (/[A-Z]/.test(pw)) score++;
  if (/[0-9]/.test(pw)) score++;
  if (/[^A-Za-z0-9]/.test(pw)) score++;
  var levels = [
    { w:'20%', c:'#ef4444', t:'Very Weak' },
    { w:'40%', c:'#f97316', t:'Weak' },
    { w:'60%', c:'#eab308', t:'Fair' },
    { w:'80%', c:'#22c55e', t:'Strong' },
    { w:'100%',c:'#16a34a', t:'Very Strong' }
  ];
  var lv = levels[Math.min(score, 4)];
  fillEl.style.width = lv.w;
  fillEl.style.background = lv.c;
  textEl.textContent = lv.t;
  textEl.style.color = lv.c;
}

function setErr(id, msg) {
  var el = document.getElementById(id);
  if (!el) return;
  if (msg) { el.textContent = msg; el.classList.add('show'); }
  else { el.textContent = ''; el.classList.remove('show'); }
}

function markField(id, valid) {
  var el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('is-invalid','is-valid');
  if (valid === true) el.classList.add('is-valid');
  else if (valid === false) el.classList.add('is-invalid');
}

function updateAvatar(previewId, fn, ln) {
  var el = document.getElementById(previewId);
  if (!el) return;
  var initials = ((fn||'').charAt(0) + (ln||'').charAt(0)).toUpperCase();
  el.textContent = initials || '?';
  el.classList.toggle('has-text', !!initials);
}

$(document).ready(function() {
  var API = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';
  var createdBy = '<?php echo $_SESSION["user"]["id"]; ?>';

  // Live avatar preview for Add modal
  $('#first-name, #last-name').on('input', function() {
    updateAvatar('add-avatar-preview', $('#first-name').val(), $('#last-name').val());
  });

  // Live avatar preview for Edit modal
  $('#edit-first-name, #edit-last-name').on('input', function() {
    updateAvatar('edit-avatar-preview', $('#edit-first-name').val(), $('#edit-last-name').val());
  });

  // Password strength for Add
  $('#password').on('input', function() {
    checkPwStrength($(this).val(), document.getElementById('pw-strength-fill'), document.getElementById('pw-strength-text'));
  });

  // Password strength for Edit
  $('#edit-password').on('input', function() {
    checkPwStrength($(this).val(), document.getElementById('edit-pw-strength-fill'), document.getElementById('edit-pw-strength-text'));
  });

  // Live validation on blur for Add fields
  $('#first-name').on('blur', function() { var v = $(this).val().trim(); markField('first-name', v.length >= 2); setErr('err-first-name', v.length < 2 ? (v ? 'Min 2 characters' : 'First name is required') : ''); });
  $('#last-name').on('blur', function() { var v = $(this).val().trim(); markField('last-name', v.length >= 2); setErr('err-last-name', v.length < 2 ? (v ? 'Min 2 characters' : 'Last name is required') : ''); });
  $('#email').on('blur', function() { var v = $(this).val().trim(); var ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); markField('email', v ? ok : false); setErr('err-email', !v ? 'Email is required' : (!ok ? 'Enter a valid email address' : '')); });
  $('#user-name').on('blur', function() { var v = $(this).val().trim(); var ok = v.length >= 3 && /^[a-zA-Z0-9_.-]+$/.test(v); markField('user-name', v ? ok : false); setErr('err-user-name', !v ? 'Username is required' : (v.length < 3 ? 'Min 3 characters' : (!ok ? 'Letters, numbers, _ . - only' : ''))); });
  $('#password').on('blur', function() { var v = $(this).val(); markField('password', v.length >= 8); setErr('err-password', !v ? 'Password is required' : (v.length < 8 ? 'Min 8 characters' : '')); });
  $('#role').on('change', function() { markField('role', !!$(this).val()); setErr('err-role', !$(this).val() ? 'Select a role' : ''); });

  // Clear validation state on focus
  $('#create-user .um-input').on('focus', function() { $(this).removeClass('is-invalid'); var errId = 'err-' + this.id; setErr(errId, ''); });
  $('#edit-user .um-input').on('focus', function() { $(this).removeClass('is-invalid'); var errId = 'err-' + this.id; setErr(errId, ''); });

  // Add User
  $('#btn-add-user').click(function() {
    var fn = $('#first-name').val().trim();
    var ln = $('#last-name').val().trim();
    var em = $('#email').val().trim();
    var st = $('#Status').val();
    var rl = $('#role').val();
    var un = $('#user-name').val().trim();
    var pw = $('#password').val();

    $('#create-user .um-input').removeClass('is-invalid is-valid');
    $('.um-error').removeClass('show').text('');
    var err = false;
    var firstErr = null;

    if (!fn || fn.length < 2) { markField('first-name', false); setErr('err-first-name', !fn ? 'First name is required' : 'Min 2 characters'); err = true; if(!firstErr) firstErr = '#first-name'; }
    if (!ln || ln.length < 2) { markField('last-name', false); setErr('err-last-name', !ln ? 'Last name is required' : 'Min 2 characters'); err = true; if(!firstErr) firstErr = '#last-name'; }
    if (!em) { markField('email', false); setErr('err-email', 'Email is required'); err = true; if(!firstErr) firstErr = '#email'; }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) { markField('email', false); setErr('err-email', 'Enter a valid email address'); err = true; if(!firstErr) firstErr = '#email'; }
    if (!un || un.length < 3) { markField('user-name', false); setErr('err-user-name', !un ? 'Username is required' : 'Min 3 characters'); err = true; if(!firstErr) firstErr = '#user-name'; }
    else if (!/^[a-zA-Z0-9_.-]+$/.test(un)) { markField('user-name', false); setErr('err-user-name', 'Letters, numbers, _ . - only'); err = true; if(!firstErr) firstErr = '#user-name'; }
    if (!pw) { markField('password', false); setErr('err-password', 'Password is required'); err = true; if(!firstErr) firstErr = '#password'; }
    else if (pw.length < 8) { markField('password', false); setErr('err-password', 'Min 8 characters'); err = true; if(!firstErr) firstErr = '#password'; }
    if (!rl) { markField('role', false); setErr('err-role', 'Select a role'); err = true; if(!firstErr) firstErr = '#role'; }

    if (err) { if (firstErr) $(firstErr).focus(); return; }

    var btn = $(this), orig = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm align-middle me-1"></span> Adding...').prop('disabled', true);

    var fd = new FormData();
    fd.append('action','create_user'); fd.append('first_name',fn); fd.append('last_name',ln);
    fd.append('email',em); fd.append('user_name',un); fd.append('role',rl);
    fd.append('password1',pw); fd.append('status',st); fd.append('created_by',createdBy);

    $.ajax({ url:API, type:'POST', data:fd, contentType:false, processData:false,
      success: function(r) {
        var d = typeof r==='string' ? (function(){try{return JSON.parse(r)}catch(e){return r}})() : r;
        if (d.status==='success'||d.success) { Swal.fire({icon:'success',title:'Success!',text:'Team member added.',timer:1500,showConfirmButton:false}).then(function(){location.reload();}); }
        else { Swal.fire({icon:'error',title:'Error',text:d.message||'Failed to add user.'}); btn.html(orig).prop('disabled',false); }
      },
      error: function() { Swal.fire({icon:'error',title:'Server Error',text:'Something went wrong.'}); btn.html(orig).prop('disabled',false); }
    });
  });

  // Populate Edit Modal
  $(document).on('click', '.edit-user-btn', function() {
    var b = $(this);
    $('#edit-user-id').val(b.data('id'));
    $('#edit-first-name').val(b.data('first-name'));
    $('#edit-last-name').val(b.data('last-name'));
    $('#edit-email').val(b.data('email'));
    $('#edit-user-name').val(b.data('user-name'));
    $('#edit-status').val(b.data('status'));
    $('#edit-role').val(b.data('role'));
    $('#edit-password').val('');
    $('#edit-user .um-input').removeClass('is-invalid is-valid');
    $('#edit-user .um-error').removeClass('show').text('');
    updateAvatar('edit-avatar-preview', b.data('first-name'), b.data('last-name'));
    checkPwStrength('', document.getElementById('edit-pw-strength-fill'), document.getElementById('edit-pw-strength-text'));
  });

  // Save Edit
  $('#btn-save-user').click(function() {
    var id = $('#edit-user-id').val();
    var fn = $('#edit-first-name').val().trim();
    var ln = $('#edit-last-name').val().trim();
    var em = $('#edit-email').val().trim();
    var st = $('#edit-status').val();
    var rl = $('#edit-role').val();
    var un = $('#edit-user-name').val().trim();
    var pw = $('#edit-password').val();

    $('#edit-user .um-input').removeClass('is-invalid is-valid');
    $('#edit-user .um-error').removeClass('show').text('');
    var err = false;
    var firstErr = null;

    if (!fn || fn.length < 2) { markField('edit-first-name', false); setErr('err-edit-first-name', !fn ? 'First name is required' : 'Min 2 characters'); err = true; if(!firstErr) firstErr = '#edit-first-name'; }
    if (!ln || ln.length < 2) { markField('edit-last-name', false); setErr('err-edit-last-name', !ln ? 'Last name is required' : 'Min 2 characters'); err = true; if(!firstErr) firstErr = '#edit-last-name'; }
    if (!em) { markField('edit-email', false); setErr('err-edit-email', 'Email is required'); err = true; if(!firstErr) firstErr = '#edit-email'; }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) { markField('edit-email', false); setErr('err-edit-email', 'Enter a valid email'); err = true; if(!firstErr) firstErr = '#edit-email'; }
    if (!un || un.length < 3) { markField('edit-user-name', false); setErr('err-edit-user-name', !un ? 'Username is required' : 'Min 3 characters'); err = true; if(!firstErr) firstErr = '#edit-user-name'; }
    else if (!/^[a-zA-Z0-9_.-]+$/.test(un)) { markField('edit-user-name', false); setErr('err-edit-user-name', 'Letters, numbers, _ . - only'); err = true; if(!firstErr) firstErr = '#edit-user-name'; }
    if (pw.length > 0 && pw.length < 8) { markField('edit-password', false); setErr('err-edit-password', 'Min 8 characters'); err = true; if(!firstErr) firstErr = '#edit-password'; }

    if (err) { if (firstErr) $(firstErr).focus(); return; }

    var btn=$(this), orig=btn.html();
    btn.html('<span class="spinner-border spinner-border-sm align-middle me-1"></span> Saving...').prop('disabled',true);

    var fd = new FormData();
    fd.append('action','update_user'); fd.append('id',id); fd.append('first_name',fn);
    fd.append('last_name',ln); fd.append('email',em); fd.append('user_name',un);
    fd.append('role',rl); fd.append('status',st);
    if (pw.length>0) fd.append('password1',pw);

    $.ajax({ url:API, type:'POST', data:fd, contentType:false, processData:false,
      success: function(r) {
        var d = typeof r==='string' ? (function(){try{return JSON.parse(r)}catch(e){return r}})() : r;
        if (d.status==='success'||d.success) { Swal.fire({icon:'success',title:'Updated!',text:'Member updated.',timer:1500,showConfirmButton:false}).then(function(){location.reload();}); }
        else { Swal.fire({icon:'error',title:'Error',text:d.message||'Failed to update.'}); btn.html(orig).prop('disabled',false); }
      },
      error: function() { Swal.fire({icon:'error',title:'Server Error',text:'Something went wrong.'}); btn.html(orig).prop('disabled',false); }
    });
  });

  // Delete User
  $(document).on('click', '.delete-user-btn', function() {
    var userId = $(this).data('id');
    Swal.fire({
      title:'Delete member?', text:'This action cannot be undone.', icon:'warning',
      showCancelButton:true, confirmButtonColor:'#dc2626', confirmButtonText:'Delete', cancelButtonText:'Cancel'
    }).then(function(res) {
      if (!res.isConfirmed) return;
      var fd = new FormData();
      fd.append('action','delete_user'); fd.append('id',userId);
      $.ajax({ url:API, type:'POST', data:fd, contentType:false, processData:false,
        success: function(r) {
          var d = typeof r==='string' ? (function(){try{return JSON.parse(r)}catch(e){return r}})() : r;
          if (d.status==='success'||d.success) { Swal.fire({icon:'success',title:'Deleted!',timer:1500,showConfirmButton:false}).then(function(){location.reload();}); }
          else { Swal.fire({icon:'error',title:'Error',text:d.message||'Failed to delete.'}); }
        },
        error: function() { Swal.fire({icon:'error',title:'Error',text:'Something went wrong.'}); }
      });
    });
  });
});
</script>
