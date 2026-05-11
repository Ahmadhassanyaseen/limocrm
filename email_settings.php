<?php include_once 'components/layout/header.php'; ?>
<?php include_once 'components/layout/sidebar.php'; ?>

<style>
  .es-page {
    --es-surface: rgba(255, 255, 255, 0.04);
    --es-surface-2: rgba(15, 23, 42, 0.55);
    --es-border: rgba(148, 163, 184, 0.14);
    --es-heading: rgba(248, 250, 252, 0.96);
    --es-muted: rgba(148, 163, 184, 0.78);
    --es-mono: rgba(147, 197, 253, 0.95);
    --es-primary-dim: rgba(var(--primary-rgb), 0.18);
    width: 100%;
    max-width: none;
    margin: 0;
    padding: 0 0 48px;
  }
  html:not(.dark) .es-page {
    --es-surface: #ffffff;
    --es-surface-2: #f8fafc;
    --es-border: rgba(15, 23, 42, 0.1);
    --es-heading: #0f172a;
    --es-muted: rgba(15, 23, 42, 0.52);
    --es-mono: #0369a1;
  }

  .es-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px 24px;
    margin-bottom: 22px;
    padding-top: 4px;
    border-bottom: 1px solid var(--es-border);
    padding-bottom: 20px;
  }
  .es-head-text { flex: 1; min-width: 200px; }
  .es-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: var(--es-heading);
    margin: 0 0 6px;
  }
  .es-title i {
    font-size: 1.55rem;
    color: rgb(var(--primary-rgb));
  }
  .es-subtitle {
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.5;
    color: var(--es-muted);
    max-width: 52rem;
  }

  .es-toolbar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin-bottom: 16px;
    gap: 10px;
  }
  .es-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    height: 42px;
    padding: 0 22px;
    border-radius: 12px;
    border: none;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    background: rgb(var(--primary-rgb));
    color: #fff;
    box-shadow: 0 4px 16px rgba(var(--primary-rgb), 0.35);
    transition: filter 0.15s, transform 0.12s, box-shadow 0.15s;
  }
  .es-btn-primary:hover {
    filter: brightness(1.06);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(var(--primary-rgb), 0.4);
  }

  .es-table-wrap {
    background: var(--es-surface);
    border: 1px solid var(--es-border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.18);
  }
  html:not(.dark) .es-table-wrap { box-shadow: 0 6px 28px rgba(15, 23, 42, 0.06); }

  .es-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  .es-table th {
    text-align: left;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--es-muted);
    padding: 14px 20px;
    border-bottom: 1px solid var(--es-border);
    background: rgba(0, 0, 0, 0.1);
  }
  html:not(.dark) .es-table th { background: rgba(15, 23, 42, 0.035); }
  .es-table td {
    padding: 14px 20px;
    border-bottom: 1px solid var(--es-border);
    color: var(--es-heading);
    vertical-align: middle;
  }
  .es-table tr:last-child td { border-bottom: none; }
  .es-table tbody tr { transition: background 0.12s; }
  .es-table tbody tr:hover td { background: rgba(var(--primary-rgb), 0.06); }

  .es-acc-name { display: flex; align-items: center; gap: 10px; font-weight: 600; }
  .es-acc-name i { color: rgb(var(--primary-rgb)); opacity: 0.95; }
  .es-mono { font-family: ui-monospace, 'Cascadia Code', Menlo, monospace; font-size: 12px; color: var(--es-mono); }

  .es-badge-ssl {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    background: rgba(22, 163, 74, 0.15);
    color: #4ade80;
    border: 1px solid rgba(34, 197, 94, 0.25);
  }
  .es-badge-none {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    color: var(--es-muted);
    background: rgba(148, 163, 184, 0.1);
  }

  .es-action {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    height: 32px;
    padding: 0 10px;
    border-radius: 8px;
    border: 1px solid var(--es-border);
    background: transparent;
    color: var(--es-heading);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    margin-right: 6px;
    transition: border-color 0.15s, color 0.15s, background 0.15s;
  }
  .es-action:hover {
    border-color: rgb(var(--primary-rgb));
    color: rgb(var(--primary-rgb));
    background: var(--es-primary-dim);
  }
  .es-action-danger:hover {
    border-color: #dc2626;
    color: #f87171;
    background: rgba(220, 38, 38, 0.12);
  }
  html:not(.dark) .es-action-danger:hover {
    color: #b91c1c;
    background: rgba(220, 38, 38, 0.08);
  }

  .es-empty { padding: 48px 24px; text-align: center; color: var(--es-muted); font-size: 14px; }

  /* Modal (outside .es-page — use own tokens so panel is never transparent) */
  .es-modal-overlay {
    --es-modal-bg: #0f172a;
    --es-modal-input-bg: #1e293b;
    --es-modal-border: rgba(148, 163, 184, 0.22);
    --es-modal-heading: rgba(248, 250, 252, 0.96);
    --es-modal-muted: rgba(148, 163, 184, 0.82);
    --es-modal-input-fg: rgba(248, 250, 252, 0.95);
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1200;
    background: rgba(0, 0, 0, 0.55);
    align-items: center;
    justify-content: center;
    padding: 24px;
    overflow-y: auto;
  }
  html:not(.dark) .es-modal-overlay {
    --es-modal-bg: #ffffff;
    --es-modal-input-bg: #f1f5f9;
    --es-modal-border: rgba(15, 23, 42, 0.12);
    --es-modal-heading: #0f172a;
    --es-modal-muted: rgba(15, 23, 42, 0.55);
    --es-modal-input-fg: #0f172a;
  }
  .es-modal-overlay.open { display: flex; }
  .es-modal {
    width: min(560px, 100%);
    background: var(--es-modal-bg);
    border: 1px solid var(--es-modal-border);
    border-radius: 18px;
    box-shadow: 0 28px 80px rgba(0, 0, 0, 0.45);
    max-height: 92vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
  .es-modal-hd {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 22px 14px;
    border-bottom: 1px solid var(--es-modal-border);
    background: var(--es-modal-bg);
    flex-shrink: 0;
  }
  .es-modal-hd h3 { margin: 0; font-size: 1.08rem; font-weight: 700; color: var(--es-modal-heading); }
  .es-modal .es-label { color: var(--es-modal-muted); }
  .es-modal .es-hint:not(.err) { color: var(--es-modal-muted); }
  .es-modal .es-details { color: var(--es-modal-muted); }
  .es-modal .es-details summary { color: var(--es-modal-heading); }
  .es-modal-close {
    width: 38px; height: 38px;
    border: none;
    border-radius: 10px;
    background: transparent;
    color: var(--es-modal-muted);
    font-size: 22px;
    line-height: 1;
    cursor: pointer;
  }
  .es-modal-close:hover { color: rgb(var(--primary-rgb)); }

  .es-modal-bd {
    padding: 18px 22px 22px;
    overflow-y: auto;
    flex: 1;
    background: var(--es-modal-bg);
  }
  .es-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--es-muted);
    margin-bottom: 6px;
    margin-top: 4px;
  }
  .es-input, .es-select, .es-textarea {
    width: 100%;
    border-radius: 11px;
    border: 1px solid var(--es-border);
    background: var(--es-surface-2);
    color: var(--es-heading);
    padding: 10px 12px;
    font-size: 13px;
    outline: none;
  }
  .es-modal .es-input,
  .es-modal .es-select,
  .es-modal .es-textarea {
    border-color: var(--es-modal-border);
    background: var(--es-modal-input-bg);
    color: var(--es-modal-input-fg);
  }
  .es-input:focus, .es-select:focus, .es-textarea:focus {
    border-color: rgb(var(--primary-rgb));
    box-shadow: 0 0 0 3px var(--es-primary-dim);
  }
  .es-row-2 { display: grid; grid-template-columns: 1fr 120px; gap: 12px; }
  @media (max-width: 520px) { .es-row-2 { grid-template-columns: 1fr; } }
  .es-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  @media (max-width: 520px) { .es-grid-2 { grid-template-columns: 1fr; } }

  .es-modal-ft {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 16px 22px 20px;
    border-top: 1px solid var(--es-modal-border);
    background: var(--es-modal-bg);
    flex-shrink: 0;
  }
  .es-modal .es-btn-cancel {
    border-color: var(--es-modal-border);
    color: var(--es-modal-heading);
  }
  .es-modal .es-btn-cancel:hover { border-color: var(--es-modal-muted); }
  .es-btn-cancel {
    height: 40px;
    padding: 0 18px;
    border-radius: 11px;
    border: 1px solid var(--es-border);
    background: transparent;
    color: var(--es-heading);
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
  }
  .es-btn-cancel:hover { border-color: var(--es-muted); }
  .es-btn-save {
    height: 40px;
    padding: 0 22px;
    border-radius: 11px;
    border: none;
    background: rgb(var(--primary-rgb));
    color: #fff;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(var(--primary-rgb), 0.35);
    transition: filter 0.15s;
  }
  .es-btn-save:hover { filter: brightness(1.06); }
  .es-hint { font-size: 11px; color: var(--es-muted); margin-top: 5px; line-height: 1.35; }
  .es-hint.err { color: #f87171; }
  html:not(.dark) .es-hint.err { color: #dc2626; }
  .es-input.err, .es-select.err, .es-textarea.err { border-color: #dc2626; }

  .es-details { margin-top: 14px; color: var(--es-muted); }
  .es-details summary { cursor: pointer; font-weight: 700; color: var(--es-heading); font-size: 13px; }
  .es-details > div { margin-top: 12px; }
</style>

<div class="main-content app-content px-5 ">
  <div class="container-fluid px-3 px-md-4 px-xl-5 es-page">

    <div class="es-head">
      <div class="es-head-text">
        <h1 class="es-title"><i class="ri-mail-settings-line"></i> Email Settings</h1>
        <p class="es-subtitle">Configure outbound SMTP accounts for system and campaign email. Accounts are stored in SuiteCRM and scoped to your user.</p>
      </div>
      <div class="es-toolbar" style="margin:0;align-self:flex-end;">
        <button type="button" class="es-btn-primary" id="es-add-btn"><i class="ri-add-line"></i> Add Account</button>
      </div>
    </div>

    <div class="es-table-wrap">
      <table class="es-table">
        <thead>
          <tr>
            <th>Account Name</th>
            <th>User</th>
            <th>SMTP Server</th>
            <th>Port</th>
            <th>SSL</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="es-tbody">
          <tr><td colspan="6" class="es-empty">Loading…</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="es-modal-overlay" id="es-modal-overlay" aria-hidden="true">
  <div class="es-modal" role="dialog" aria-modal="true" aria-labelledby="es-modal-title">
    <div class="es-modal-hd">
      <h3 id="es-modal-title">Edit Outbound Account</h3>
      <button type="button" class="es-modal-close" id="es-modal-x" aria-label="Close">&times;</button>
    </div>
    <div class="es-modal-bd">
      <input type="hidden" id="es-f-id" value="">

      <label class="es-label" for="es-f-name">Account Name</label>
      <input class="es-input" id="es-f-name" maxlength="255" autocomplete="off">
      <div class="es-hint err" id="es-f-name-e" style="display:none;"></div>

      <label class="es-label" for="es-f-scope">User</label>
      <select class="es-select" id="es-f-scope">
        <option value="system">System</option>
        <option value="personal">Personal</option>
      </select>

      <div class="es-row-2" style="margin-top:12px;">
        <div>
          <label class="es-label" for="es-f-server">SMTP Server</label>
          <input class="es-input es-mono" id="es-f-server" maxlength="253" autocomplete="off">
          <div class="es-hint err" id="es-f-server-e" style="display:none;"></div>
        </div>
        <div>
          <label class="es-label" for="es-f-port">Port</label>
          <input class="es-input" id="es-f-port" inputmode="numeric" maxlength="5" autocomplete="off">
          <div class="es-hint err" id="es-f-port-e" style="display:none;"></div>
        </div>
      </div>

      <div class="es-grid-2" style="margin-top:12px;">
        <div>
          <label class="es-label" for="es-f-smtp-user">SMTP Username</label>
          <input class="es-input" id="es-f-smtp-user" maxlength="255" autocomplete="off">
          <div class="es-hint err" id="es-f-smtp-user-e" style="display:none;"></div>
        </div>
        <div>
          <label class="es-label" for="es-f-smtp-pass">SMTP Password</label>
          <input class="es-input" type="password" id="es-f-smtp-pass" maxlength="255" autocomplete="new-password"
                 placeholder="Leave blank to keep current">
          <div class="es-hint err" id="es-f-smtp-pass-e" style="display:none;"></div>
          <div class="es-hint" id="es-f-smtp-pass-h"></div>
        </div>
      </div>

      <div class="es-grid-2" style="margin-top:12px;">
        <div>
          <label class="es-label" for="es-f-ssl">SSL / TLS</label>
          <select class="es-select" id="es-f-ssl">
            <option value="0">None</option>
            <option value="1">SSL enabled</option>
            <option value="2">TLS</option>
          </select>
        </div>
        <div>
          <label class="es-label" for="es-f-auth">Auth required</label>
          <select class="es-select" id="es-f-auth">
            <option value="1">Yes</option>
            <option value="0">No</option>
          </select>
        </div>
      </div>

      <details class="es-details" id="es-detail-adv">
        <summary>From / Reply-to / Signature</summary>
        <div style="margin-top:12px;">
          <div class="es-grid-2">
            <div>
              <label class="es-label" for="es-f-fn">“From” name</label>
              <input class="es-input" id="es-f-fn" maxlength="255" autocomplete="off">
              <div class="es-hint err" id="es-f-fn-e" style="display:none;"></div>
            </div>
            <div>
              <label class="es-label" for="es-f-fa">“From” address</label>
              <input class="es-input" type="email" id="es-f-fa" maxlength="255" autocomplete="off">
              <div class="es-hint err" id="es-f-fa-e" style="display:none;"></div>
            </div>
          </div>
          <div class="es-grid-2" style="margin-top:10px;">
            <div>
              <label class="es-label" for="es-f-rn">“Reply-to” name</label>
              <input class="es-input" id="es-f-rn" maxlength="255" autocomplete="off">
              <div class="es-hint err" id="es-f-rn-e" style="display:none;"></div>
            </div>
            <div>
              <label class="es-label" for="es-f-ra">“Reply-to” address</label>
              <input class="es-input" type="email" id="es-f-ra" maxlength="255" autocomplete="off">
              <div class="es-hint err" id="es-f-ra-e" style="display:none;"></div>
            </div>
          </div>
          <label class="es-label" for="es-f-sig" style="margin-top:10px;">Signature</label>
          <textarea class="es-textarea" id="es-f-sig" rows="4" maxlength="65535" placeholder="Optional HTML/plain text signature"></textarea>
          <div class="es-hint err" id="es-f-sig-e" style="display:none;"></div>
        </div>
      </details>

      <input type="hidden" id="es-f-sendtype" value="SMTP">
      <input type="hidden" id="es-f-smtptype" value="SMTP">
    </div>
    <div class="es-modal-ft">
      <button type="button" class="es-btn-cancel" id="es-modal-cancel">Cancel</button>
      <button type="button" class="es-btn-save" id="es-modal-save">Save</button>
    </div>
  </div>
</div>

<?php include_once 'components/layout/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
  var END = 'config/outbound_email_endpoint.php';

  var RE_HOST = /^[A-Za-z0-9.\-_\[\]:]+$/;
  var RE_EMAIL = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,253}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,253}[a-zA-Z0-9])?)+$/;

  function esc(s) {
    var d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
  }

  function sslLabel(v) {
    if (v === '1' || v === 1) return '<span class="es-badge-ssl"><i class="ri-lock-line"></i> SSL</span>';
    if (v === '2' || v === 2) return '<span class="es-badge-ssl"><i class="ri-lock-line"></i> TLS</span>';
    return '<span class="es-badge-none">Off</span>';
  }

  function loadAccounts() {
    $.post(END, { op: 'list' })
      .done(function (res) {
        if (typeof res === 'string') { try { res = JSON.parse(res); } catch (e) { res = {}; } }
        if (!res.success) {
          $('#es-tbody').html('<tr><td colspan="6" class="es-empty">' + esc(res.message || 'Could not load accounts.') + '</td></tr>');
          return;
        }
        var rows = res.accounts || [];
        if (!rows.length) {
          $('#es-tbody').html('<tr><td colspan="6" class="es-empty">No outbound accounts yet. Click &ldquo;Add Account&rdquo; to create one.</td></tr>');
          return;
        }
        var html = '';
        rows.forEach(function (r) {
          html += '<tr>';
          html += '<td><div class="es-acc-name"><i class="ri-stack-line"></i> ' + esc(r.name || '') + '</div></td>';
          html += '<td>' + esc(r.assignee || '') + '</td>';
          html += '<td class="es-mono">' + esc(r.mail_smtpserver || '') + '</td>';
          html += '<td class="es-mono">' + esc(String(r.mail_smtpport || '')) + '</td>';
          html += '<td>' + sslLabel(r.mail_smtpssl) + '</td>';
          html += '<td>';
          html += '<button type="button" class="es-action es-edit" data-id="' + esc(r.id) + '" title="Edit"><i class="ri-pencil-line"></i></button>';
          html += '<button type="button" class="es-action es-test" data-id="' + esc(r.id) + '" title="Test connection"><i class="ri-send-plane-line"></i> Test</button>';
          html += '<button type="button" class="es-action es-action-danger es-delete" data-id="' + esc(r.id) + '" title="Delete"><i class="ri-delete-bin-line"></i></button>';
          html += '</td></tr>';
        });
        $('#es-tbody').html(html);
      })
      .fail(function () {
        $('#es-tbody').html('<tr><td colspan="6" class="es-empty">Network error.</td></tr>');
      });
  }

  function clearErrs() {
    $('#es-modal-overlay .es-hint.err').hide().text('');
    $('#es-modal-overlay .es-input.err, #es-modal-overlay .es-select.err, #es-modal-overlay .es-textarea.err').removeClass('err');
    $('#es-f-smtp-pass-h').removeClass('err');
  }

  var ADV_ERR_HINTS = ['es-f-fn-e', 'es-f-fa-e', 'es-f-rn-e', 'es-f-ra-e', 'es-f-sig-e'];

  function showErr(hintId, inputId, msg) {
    var $h = $('#' + hintId);
    var $in = $('#' + inputId);
    if (ADV_ERR_HINTS.indexOf(hintId) !== -1) {
      $('#es-detail-adv').prop('open', true);
    }
    if ($in.length) {
      $in.addClass('err');
    }
    $h.text(msg).show();
    if ($in.length) {
      $in.trigger('focus');
    }
  }

  var FIELD_ERR_MAP = {
    'es-f-name': 'es-f-name-e',
    'es-f-server': 'es-f-server-e',
    'es-f-port': 'es-f-port-e',
    'es-f-smtp-user': 'es-f-smtp-user-e',
    'es-f-smtp-pass': 'es-f-smtp-pass-e',
    'es-f-fn': 'es-f-fn-e',
    'es-f-fa': 'es-f-fa-e',
    'es-f-rn': 'es-f-rn-e',
    'es-f-ra': 'es-f-ra-e',
    'es-f-sig': 'es-f-sig-e'
  };

  function openModal(isNew) {
    clearErrs();
    $('#es-detail-adv').prop('open', false);
    $('#es-modal-overlay').addClass('open').attr('aria-hidden', 'false');
    $('#es-modal-title').text(isNew ? 'Add Outbound Account' : 'Edit Outbound Account');
    if (isNew) {
      $('#es-f-id').val('');
      $('#es-f-name').val('');
      $('#es-f-scope').val('system');
      $('#es-f-server').val('');
      $('#es-f-port').val('465');
      $('#es-f-smtp-user').val('');
      $('#es-f-smtp-pass').val('');
      $('#es-f-ssl').val('1');
      $('#es-f-auth').val('1');
      $('#es-f-fn').val('');
      $('#es-f-fa').val('');
      $('#es-f-rn').val('');
      $('#es-f-ra').val('');
      $('#es-f-sig').val('');
      $('#es-f-smtp-pass').attr('placeholder', 'Password');
      $('#es-f-smtp-pass-h').text('');
    }
  }

  function closeModal() {
    $('#es-modal-overlay').removeClass('open').attr('aria-hidden', 'true');
  }

  var RE_ACC_NAME = /^[\p{L}\p{M}0-9\s.'\-_&(),\/@:+]+$/u;

  function validateForm(editId, hasStoredPass) {
    clearErrs();

    var name = $('#es-f-name').val().trim();
    if (!name) {
      showErr('es-f-name-e', 'es-f-name', 'Account name is required.');
      return false;
    }
    if (name.length > 255) {
      showErr('es-f-name-e', 'es-f-name', 'Account name is too long (max 255 characters).');
      return false;
    }
    if (!RE_ACC_NAME.test(name)) {
      showErr('es-f-name-e', 'es-f-name', 'Use letters, numbers, spaces, and common punctuation only.');
      return false;
    }

    var host = $('#es-f-server').val().trim();
    if (!host) {
      showErr('es-f-server-e', 'es-f-server', 'SMTP server hostname is required.');
      return false;
    }
    if (host.length > 253) {
      showErr('es-f-server-e', 'es-f-server', 'Hostname is too long.');
      return false;
    }
    if (!RE_HOST.test(host)) {
      showErr('es-f-server-e', 'es-f-server', 'Invalid hostname (letters, digits, dots, hyphens, underscores; IPv6 in brackets).');
      return false;
    }

    var port = $('#es-f-port').val().trim();
    if (!/^\d+$/.test(port)) {
      showErr('es-f-port-e', 'es-f-port', 'Port must be a whole number.');
      return false;
    }
    var pn = parseInt(port, 10);
    if (pn < 1 || pn > 65535) {
      showErr('es-f-port-e', 'es-f-port', 'Port must be between 1 and 65535.');
      return false;
    }

    var auth = $('#es-f-auth').val() === '1';
    var su = $('#es-f-smtp-user').val().trim();
    if (auth && su === '') {
      showErr('es-f-smtp-user-e', 'es-f-smtp-user', 'SMTP username is required when authentication is enabled.');
      return false;
    }
    if (su.length > 255) {
      showErr('es-f-smtp-user-e', 'es-f-smtp-user', 'SMTP username is too long (max 255 characters).');
      return false;
    }

    var pw = $('#es-f-smtp-pass').val();
    if (pw.length > 255) {
      showErr('es-f-smtp-pass-e', 'es-f-smtp-pass', 'Password is too long (max 255 characters).');
      return false;
    }
    if (auth && !editId && pw.trim() === '') {
      showErr('es-f-smtp-pass-e', 'es-f-smtp-pass', 'Password is required for a new account when authentication is enabled.');
      return false;
    }
    if (auth && editId && pw.trim() === '' && !hasStoredPass) {
      showErr('es-f-smtp-pass-e', 'es-f-smtp-pass', 'Enter the SMTP password, or turn authentication off until one is saved in SuiteCRM.');
      return false;
    }

    var fn = $('#es-f-fn').val().trim();
    var fa = $('#es-f-fa').val().trim();
    if (fn && !fa) {
      showErr('es-f-fa-e', 'es-f-fa', '“From” address is required when “From” name is set.');
      return false;
    }
    if (fn.length > 255) {
      showErr('es-f-fn-e', 'es-f-fn', '“From” name is too long.');
      return false;
    }
    if (fa && !RE_EMAIL.test(fa)) {
      showErr('es-f-fa-e', 'es-f-fa', 'Enter a valid “From” email address.');
      return false;
    }

    var rn = $('#es-f-rn').val().trim();
    var ra = $('#es-f-ra').val().trim();
    if (rn && !ra) {
      showErr('es-f-ra-e', 'es-f-ra', '“Reply-to” address is required when “Reply-to” name is set.');
      return false;
    }
    if (rn.length > 255) {
      showErr('es-f-rn-e', 'es-f-rn', '“Reply-to” name is too long.');
      return false;
    }
    if (ra && !RE_EMAIL.test(ra)) {
      showErr('es-f-ra-e', 'es-f-ra', 'Enter a valid “Reply-to” email address.');
      return false;
    }

    var sig = $('#es-f-sig').val();
    if (sig.length > 65535) {
      showErr('es-f-sig-e', 'es-f-sig', 'Signature is too long (max 65535 characters).');
      return false;
    }

    return true;
  }

  function saveAccount() {
    var id = $('#es-f-id').val().trim();
    var editId = id;
    var hasStored = $('#es-f-smtp-pass-h').data('haspass') === 1;

    if (!validateForm(editId, !!hasStored)) return;

    var fd = {
      op: 'save',
      id: id,
      name: $('#es-f-name').val().trim(),
      account_scope: $('#es-f-scope').val(),
      mail_smtpserver: $('#es-f-server').val().trim(),
      mail_smtpport: $('#es-f-port').val().trim(),
      mail_smtpuser: $('#es-f-smtp-user').val().trim(),
      mail_smtppass: $('#es-f-smtp-pass').val(),
      mail_smtpssl: $('#es-f-ssl').val(),
      mail_smtpauth_req: $('#es-f-auth').val(),
      smtp_from_name: $('#es-f-fn').val().trim(),
      smtp_from_addr: $('#es-f-fa').val().trim(),
      reply_to_name: $('#es-f-rn').val().trim(),
      reply_to_addr: $('#es-f-ra').val().trim(),
      signature: $('#es-f-sig').val(),
      mail_sendtype: $('#es-f-sendtype').val(),
      mail_smtptype: $('#es-f-smtptype').val()
    };

    $.post(END, fd)
      .done(function (res) {
        if (typeof res === 'string') { try { res = JSON.parse(res); } catch (e) { res = {}; } }
        if (res.success) {
          closeModal();
          Swal.fire({ icon: 'success', title: 'Saved', text: res.message || 'Account saved.', timer: 1800, showConfirmButton: false });
          loadAccounts();
        } else {
          Swal.fire({ icon: 'error', title: 'Could not save', text: res.message || 'Try again.' });
        }
      })
      .fail(function () {
        Swal.fire({ icon: 'error', title: 'Network error', text: 'Try again.' });
      });
  }

  $(function () {
    loadAccounts();

    $('#es-add-btn').on('click', function () {
      openModal(true);
      $('#es-f-name').trigger('focus');
    });

    $('#es-modal-x, #es-modal-cancel').on('click', closeModal);
    $('#es-modal-overlay').on('click', function (e) {
      if (e.target === this) closeModal();
    });

    $('#es-modal-save').on('click', saveAccount);

    $('#es-modal-overlay').on('input change', '.es-input, .es-select, .es-textarea', function () {
      var fid = this.id;
      var hid = FIELD_ERR_MAP[fid];
      if (hid) {
        $('#' + hid).hide().text('');
        $(this).removeClass('err');
      }
      if (fid === 'es-f-auth') {
        $('#es-f-smtp-user-e, #es-f-smtp-pass-e').hide().text('');
        $('#es-f-smtp-user, #es-f-smtp-pass').removeClass('err');
      }
    });

    $(document).on('click', '.es-edit', function () {
      var rid = $(this).data('id');
      $.post(END, { op: 'detail', id: rid })
        .done(function (res) {
          if (typeof res === 'string') { try { res = JSON.parse(res); } catch (e) { res = {}; } }
          if (!res.success || !res.account) {
            Swal.fire({ icon: 'error', text: res.message || 'Unable to load account.' });
            return;
          }
          var a = res.account;
          openModal(false);
          $('#es-f-id').val(a.id);
          $('#es-f-name').val(a.name);
          $('#es-f-scope').val(a.type === 'system' ? 'system' : 'personal');
          $('#es-f-server').val(a.mail_smtpserver);
          $('#es-f-port').val(a.mail_smtpport);
          $('#es-f-smtp-user').val(a.mail_smtpuser);
          $('#es-f-smtp-pass').val('');
          $('#es-f-ssl').val(String(a.mail_smtpssl || '0'));
          $('#es-f-auth').val(a.mail_smtpauth_req === '1' ? '1' : '0');
          $('#es-f-fn').val(a.smtp_from_name || '');
          $('#es-f-fa').val(a.smtp_from_addr || '');
          $('#es-f-rn').val(a.reply_to_name || '');
          $('#es-f-ra').val(a.reply_to_addr || '');
          $('#es-f-sig').val(a.signature || '');
          $('#es-f-smtp-pass').attr('placeholder', 'Leave blank to keep current');
          var hp = a.mail_smtp_has_pass ? 1 : 0;
          $('#es-f-smtp-pass-h').removeClass('err').data('haspass', hp)
            .text(hp ? 'A password is stored. Leave blank to keep it.' : 'No password stored yet.');
        });
    });

    $(document).on('click', '.es-test', function () {
      var rid = $(this).data('id');
      $.post(END, { op: 'test', id: rid })
        .done(function (res) {
          if (typeof res === 'string') { try { res = JSON.parse(res); } catch (e) { res = {}; } }
          if (res.success) {
            Swal.fire({ icon: 'success', title: 'Connection OK', text: res.message });
          } else {
            Swal.fire({ icon: 'error', title: 'Test failed', text: res.message || 'Could not connect.' });
          }
        })
        .fail(function () {
          Swal.fire({ icon: 'error', title: 'Network error', text: 'Try again.' });
        });
    });

    $(document).on('click', '.es-delete', function () {
      var rid = $(this).data('id');
      if (!rid) return;
      Swal.fire({
        icon: 'warning',
        title: 'Delete this account?',
        text: 'The outbound email account will be removed from SuiteCRM. This cannot be undone.',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
        focusCancel: true
      }).then(function (result) {
        if (!result.isConfirmed) return;
        $.post(END, { op: 'delete', id: rid })
          .done(function (res) {
            if (typeof res === 'string') { try { res = JSON.parse(res); } catch (e) { res = {}; } }
            if (res.success) {
              Swal.fire({ icon: 'success', title: 'Deleted', text: res.message || 'Account removed.', timer: 1800, showConfirmButton: false });
              loadAccounts();
            } else {
              Swal.fire({ icon: 'error', title: 'Could not delete', text: res.message || 'Try again.' });
            }
          })
          .fail(function () {
            Swal.fire({ icon: 'error', title: 'Network error', text: 'Try again.' });
          });
      });
    });
  });
})();
</script>
