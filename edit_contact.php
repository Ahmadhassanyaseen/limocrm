<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>

<?php $contactId = $_GET['id'] ?? ''; ?>

<style>
  .ec-page { --ec-surface: #ffffff; --ec-surface-2: #f8fafc; --ec-border: rgba(15,23,42,0.08); --ec-text: #0f172a; --ec-muted: rgba(15,23,42,0.55); }
  .dark .ec-page { --ec-surface: rgba(255,255,255,0.035); --ec-surface-2: rgba(255,255,255,0.05); --ec-border: rgba(255,255,255,0.08); --ec-text: rgba(255,255,255,0.92); --ec-muted: rgba(255,255,255,0.50); }

  .ec-sticky { position:sticky; top:0; z-index:100; padding:16px 0; background:var(--ec-surface-2); border-bottom:1px solid var(--ec-border); margin:-16px -12px 24px; padding:16px 12px; transition:box-shadow .2s; }
  .ec-sticky.scrolled { box-shadow:0 4px 20px rgba(0,0,0,.08); }
  .dark .ec-sticky.scrolled { box-shadow:0 4px 20px rgba(0,0,0,.3); }

  .ec-card { background:var(--ec-surface); border:1px solid var(--ec-border); border-radius:16px; padding:24px 28px; margin-bottom:20px; }
  .ec-card-title { font-size:15px; font-weight:700; color:var(--ec-text); margin-bottom:20px; display:flex; align-items:center; gap:8px; padding-bottom:12px; border-bottom:1px solid var(--ec-border); }
  .ec-card-title i { color:rgb(var(--primary-rgb)); font-size:18px; }

  .ec-label { display:block; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--ec-muted); margin-bottom:6px; }
  .ec-input, .ec-select, .ec-textarea {
    width:100%; border:1px solid var(--ec-border); background:var(--ec-surface-2); color:var(--ec-text);
    border-radius:12px; padding:10px 14px; font-size:14px; height:44px; transition:border-color .2s, box-shadow .2s;
  }
  .ec-textarea { height:auto; min-height:100px; resize:vertical; }
  .ec-select {
    appearance:none; -webkit-appearance:none; padding-right:40px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m2 4 4 4 4-4'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right 14px center; background-size:12px; cursor:pointer;
  }
  .ec-input:focus, .ec-select:focus, .ec-textarea:focus { outline:none; border-color:rgb(var(--primary-rgb)); box-shadow:0 0 0 4px rgba(var(--primary-rgb),.12); }
  .ec-input.err-border, .ec-select.err-border, .ec-textarea.err-border { border-color:#ef4444; box-shadow:0 0 0 4px rgba(239,68,68,.1); }

  .ec-field { margin-bottom:16px; }
  .ec-field:last-child { margin-bottom:0; }
  .ec-row { display:grid; grid-template-columns:1fr; gap:14px; }
  @media(min-width:600px) { .ec-row-2 { grid-template-columns:1fr 1fr; } .ec-row-3 { grid-template-columns:1fr 1fr 1fr; } }

  .ec-btn { border-radius:12px; padding:10px 22px; font-weight:600; font-size:14px; border:1px solid var(--ec-border); background:transparent; color:var(--ec-text); cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
  .ec-btn:hover { background:rgba(var(--primary-rgb),.06); }
  .ec-btn-primary { background:rgb(var(--primary-rgb)); border-color:rgb(var(--primary-rgb)); color:#fff; }
  .ec-btn-primary:hover { opacity:.9; color:#fff; }
  .ec-btn:disabled { opacity:.55; cursor:not-allowed; }

  .ec-loading { text-align:center; padding:80px 20px; color:var(--ec-muted); }
</style>

<div class="main-content app-content">
  <div class="container-fluid ec-page">

    <div class="ec-sticky" id="ec-sticky">
      <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
          <a href="javascript:history.back()" class="ec-btn" style="padding:8px 14px;font-size:13px;"><i class="ri-arrow-left-s-line"></i> Back</a>
          <div>
            <h1 class="text-lg font-bold mb-0" style="color:var(--ec-text);" id="ec-page-title">Edit Contact</h1>
            <p class="text-xs mb-0 mt-1" style="color:var(--ec-muted);" id="ec-page-sub">Update contact information</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <a href="contacts.php" class="ec-btn">Cancel</a>
          <button type="button" class="ec-btn ec-btn-primary" id="btnSave" onclick="saveChanges()"><i class="ri-save-line"></i> Save Changes</button>
        </div>
      </div>
    </div>

    <div id="ec-content">
      <div class="ec-loading">
        <div class="spinner-border text-primary mb-3"></div>
        <div style="font-size:14px;font-weight:600;">Loading contact data...</div>
      </div>
    </div>
  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var API = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';
var CONTACT_ID = '<?php echo htmlspecialchars($contactId); ?>';

window.addEventListener('scroll', function() {
  var el = document.getElementById('ec-sticky');
  if (el) el.classList.toggle('scrolled', window.scrollY > 10);
});

$(document).ready(function() {
  if (!CONTACT_ID) {
    $('#ec-content').html('<div class="ec-loading" style="color:#ef4444;">No contact ID provided.</div>');
    return;
  }
  loadContact();
});

function loadContact() {
  $.ajax({
    url: API, type: 'POST',
    data: { action: 'fetch_contact_detail', id: CONTACT_ID },
    success: function(response) {
      try {
        var res = typeof response === 'string' ? JSON.parse(response) : response;
        if (res.success && res.contact) {
          renderForm(res.contact);
        } else {
          $('#ec-content').html('<div class="ec-loading" style="color:#ef4444;">' + (res.message || 'Contact not found.') + '</div>');
        }
      } catch(e) {
        $('#ec-content').html('<div class="ec-loading" style="color:#ef4444;">Failed to parse response.</div>');
      }
    },
    error: function() {
      $('#ec-content').html('<div class="ec-loading" style="color:#ef4444;">Failed to connect to server.</div>');
    }
  });
}

function renderForm(c) {
  var fullName = ((c.first_name || '') + ' ' + (c.last_name || '')).trim();
  document.getElementById('ec-page-title').textContent = 'Edit: ' + (fullName || 'Contact');

  var sources = ['','Web','Email','Call','Referral','Partner','Other'];
  var sourceOpts = sources.map(function(s) {
    var sel = (c.lead_source || '') === s ? ' selected' : '';
    return '<option value="' + s + '"' + sel + '>' + (s || '-- Select --') + '</option>';
  }).join('');

  var html = '';

  html += '<div class="ec-card">';
  html += '<div class="ec-card-title"><i class="ri-user-line"></i> Personal Information</div>';
  html += '<div class="ec-row ec-row-2">';
  html += '<div class="ec-field"><label class="ec-label">First Name <span style="color:#ef4444;">*</span></label><input type="text" id="ec_first_name" class="ec-input" value="' + esc(c.first_name || '') + '" /></div>';
  html += '<div class="ec-field"><label class="ec-label">Last Name <span style="color:#ef4444;">*</span></label><input type="text" id="ec_last_name" class="ec-input" value="' + esc(c.last_name || '') + '" /></div>';
  html += '</div>';
  html += '<div class="ec-row ec-row-2">';
  html += '<div class="ec-field"><label class="ec-label">Title</label><input type="text" id="ec_title" class="ec-input" value="' + esc(c.title || '') + '" /></div>';
  html += '<div class="ec-field"><label class="ec-label">Department</label><input type="text" id="ec_department" class="ec-input" value="' + esc(c.department || '') + '" /></div>';
  html += '</div>';
  html += '</div>';

  html += '<div class="ec-card">';
  html += '<div class="ec-card-title"><i class="ri-phone-line"></i> Contact Details</div>';
  html += '<div class="ec-field"><label class="ec-label">Email</label><input type="email" id="ec_email" class="ec-input" value="' + esc(c.email_address || '') + '" /></div>';
  html += '<div class="ec-row ec-row-2">';
  html += '<div class="ec-field"><label class="ec-label">Mobile Phone</label><input type="text" id="ec_phone_mobile" class="ec-input" value="' + esc(c.phone_mobile || '') + '" /></div>';
  html += '<div class="ec-field"><label class="ec-label">Work Phone</label><input type="text" id="ec_phone_work" class="ec-input" value="' + esc(c.phone_work || '') + '" /></div>';
  html += '</div>';
  html += '</div>';

  html += '<div class="ec-card">';
  html += '<div class="ec-card-title"><i class="ri-map-pin-line"></i> Address</div>';
  html += '<div class="ec-field"><label class="ec-label">Street</label><input type="text" id="ec_address_street" class="ec-input" value="' + esc(c.primary_address_street || '') + '" /></div>';
  html += '<div class="ec-row ec-row-3">';
  html += '<div class="ec-field"><label class="ec-label">City</label><input type="text" id="ec_address_city" class="ec-input" value="' + esc(c.primary_address_city || '') + '" /></div>';
  html += '<div class="ec-field"><label class="ec-label">State</label><input type="text" id="ec_address_state" class="ec-input" value="' + esc(c.primary_address_state || '') + '" /></div>';
  html += '<div class="ec-field"><label class="ec-label">Zip Code</label><input type="text" id="ec_address_zip" class="ec-input" value="' + esc(c.primary_address_postalcode || '') + '" /></div>';
  html += '</div>';
  html += '<div class="ec-field"><label class="ec-label">Country</label><input type="text" id="ec_address_country" class="ec-input" value="' + esc(c.primary_address_country || '') + '" /></div>';
  html += '</div>';

  html += '<div class="ec-card">';
  html += '<div class="ec-card-title"><i class="ri-file-text-line"></i> Additional Information</div>';
  html += '<div class="ec-row ec-row-2">';
  html += '<div class="ec-field"><label class="ec-label">Lead Source</label><select id="ec_lead_source" class="ec-select">' + sourceOpts + '</select></div>';
  html += '<div class="ec-field" style="display:flex;align-items:flex-end;"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--ec-text);font-weight:600;"><input type="checkbox" id="ec_do_not_call" style="width:18px;height:18px;accent-color:rgb(var(--primary-rgb));" ' + (c.do_not_call === '1' ? 'checked' : '') + ' /> Do Not Call</label></div>';
  html += '</div>';
  html += '<div class="ec-field"><label class="ec-label">Description</label><textarea id="ec_description" class="ec-textarea">' + esc(c.description || '') + '</textarea></div>';
  html += '</div>';

  document.getElementById('ec-content').innerHTML = html;
}

function highlightField(id) {
  var el = document.getElementById(id);
  if (!el) return;
  el.classList.add('err-border');
  el.focus();
  setTimeout(function() { el.classList.remove('err-border'); }, 3000);
}

function saveChanges() {
  var fn = document.getElementById('ec_first_name').value.trim();
  var ln = document.getElementById('ec_last_name').value.trim();
  if (!fn) { highlightField('ec_first_name'); return Swal.fire({icon:'warning',title:'Required',text:'First name is required.'}); }
  if (!ln) { highlightField('ec_last_name'); return Swal.fire({icon:'warning',title:'Required',text:'Last name is required.'}); }

  var btn = document.getElementById('btnSave');
  btn.disabled = true;

  $.ajax({
    url: API, type: 'POST',
    data: {
      action: 'update_contact',
      id: CONTACT_ID,
      first_name: fn,
      last_name: ln,
      title: document.getElementById('ec_title').value.trim(),
      department: document.getElementById('ec_department').value.trim(),
      email: document.getElementById('ec_email').value.trim(),
      phone_mobile: document.getElementById('ec_phone_mobile').value.trim(),
      phone_work: document.getElementById('ec_phone_work').value.trim(),
      primary_address_street: document.getElementById('ec_address_street').value.trim(),
      primary_address_city: document.getElementById('ec_address_city').value.trim(),
      primary_address_state: document.getElementById('ec_address_state').value.trim(),
      primary_address_postalcode: document.getElementById('ec_address_zip').value.trim(),
      primary_address_country: document.getElementById('ec_address_country').value.trim(),
      lead_source: document.getElementById('ec_lead_source').value,
      do_not_call: document.getElementById('ec_do_not_call').checked ? '1' : '0',
      description: document.getElementById('ec_description').value.trim()
    },
    success: function(response) {
      var res = typeof response === 'string' ? JSON.parse(response) : response;
      if (res.success) {
        Swal.fire({icon:'success',title:'Saved!',text:'Contact updated successfully.',timer:1500,showConfirmButton:false});
        setTimeout(function() { window.location.href = 'contact_detail.php?id=' + CONTACT_ID; }, 1600);
      } else {
        Swal.fire({icon:'error',title:'Error',text:res.message || 'Failed to update.'});
      }
    },
    error: function() { Swal.fire({icon:'error',title:'Error',text:'Server connection failed.'}); },
    complete: function() { btn.disabled = false; }
  });
}

function esc(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
</script>
