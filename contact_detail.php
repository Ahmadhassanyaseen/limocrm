<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>

<?php $contactId = $_GET['id'] ?? ''; ?>

<style>
  .cd-page { --cd-surface: #ffffff; --cd-surface-2: #f8fafc; --cd-border: rgba(15,23,42,0.08); --cd-text: #0f172a; --cd-muted: rgba(15,23,42,0.55); }
  .dark .cd-page { --cd-surface: rgba(255,255,255,0.035); --cd-surface-2: rgba(255,255,255,0.05); --cd-border: rgba(255,255,255,0.08); --cd-text: rgba(255,255,255,0.92); --cd-muted: rgba(255,255,255,0.50); }

  .cd-header { background:var(--cd-surface); border:1px solid var(--cd-border); border-radius:20px; padding:28px 32px; margin-bottom:24px; position:relative; overflow:hidden; }
  .cd-header::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg, rgb(var(--primary-rgb)), #8b5cf6); }
  .cd-avatar-lg { width:72px; height:72px; border-radius:18px; display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:800; flex-shrink:0; background:rgba(var(--primary-rgb),0.10); color:rgb(var(--primary-rgb)); }
  .cd-name { font-size:24px; font-weight:800; color:var(--cd-text); line-height:1.2; }
  .cd-subtitle { font-size:13px; color:var(--cd-muted); margin-top:4px; }
  .cd-tag { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:600; padding:4px 12px; border-radius:8px; }

  .cd-card { background:var(--cd-surface); border:1px solid var(--cd-border); border-radius:16px; padding:24px; margin-bottom:20px; }
  .cd-card-title { font-size:14px; font-weight:700; color:var(--cd-text); margin-bottom:16px; display:flex; align-items:center; gap:8px; }
  .cd-card-title i { color:rgb(var(--primary-rgb)); font-size:18px; }

  .cd-detail { display:flex; gap:12px; padding:10px 0; border-bottom:1px solid var(--cd-border); }
  .cd-detail:last-child { border-bottom:none; }
  .cd-detail-label { width:140px; flex-shrink:0; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--cd-muted); padding-top:2px; }
  .cd-detail-value { flex:1; font-size:14px; color:var(--cd-text); line-height:1.5; word-break:break-word; }
  .cd-detail-value a { color:rgb(var(--primary-rgb)); text-decoration:none; }
  .cd-detail-value a:hover { text-decoration:underline; }

  .cd-btn { border-radius:12px; padding:10px 20px; font-weight:600; font-size:14px; border:1px solid var(--cd-border); background:transparent; color:var(--cd-text); cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
  .cd-btn:hover { background:rgba(var(--primary-rgb),.06); }
  .cd-btn-primary { background:rgb(var(--primary-rgb)); border-color:rgb(var(--primary-rgb)); color:#fff; }
  .cd-btn-primary:hover { opacity:.9; color:#fff; }
  .cd-btn-danger { border-color:rgba(239,68,68,.25); color:#dc2626; }
  .cd-btn-danger:hover { background:rgba(239,68,68,.06); }

  .cd-loading { text-align:center; padding:80px 20px; color:var(--cd-muted); }

  @media (max-width: 768px) { .cd-grid { grid-template-columns: 1fr !important; } }
</style>

<div class="main-content app-content">
  <div class="container-fluid cd-page">

    <div class="mb-4">
      <a href="contacts.php" style="font-size:13px;color:var(--cd-muted);text-decoration:none;display:inline-flex;align-items:center;gap:4px;" onmouseover="this.style.color='rgb(var(--primary-rgb))'" onmouseout="this.style.color='var(--cd-muted)'">
        <i class="ri-arrow-left-s-line"></i> Back to Contacts
      </a>
    </div>

    <div id="cd-content">
      <div class="cd-loading">
        <div class="spinner-border text-primary mb-3"></div>
        <div style="font-size:14px;font-weight:600;">Loading contact details...</div>
      </div>
    </div>
  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var API = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';
var CONTACT_ID = '<?php echo htmlspecialchars($contactId); ?>';

$(document).ready(function() {
  if (!CONTACT_ID) {
    $('#cd-content').html('<div class="cd-loading" style="color:#ef4444;"><i class="ri-error-warning-line" style="font-size:48px;opacity:.3;display:block;margin-bottom:12px;"></i><div>No contact ID provided.</div></div>');
    return;
  }
  loadContactDetail();
});

function loadContactDetail() {
  $.ajax({
    url: API, type: 'POST',
    data: { action: 'fetch_contact_detail', id: CONTACT_ID },
    success: function(response) {
      try {
        var res = typeof response === 'string' ? JSON.parse(response) : response;
        if (res.success && res.contact) {
          renderContact(res.contact);
        } else {
          $('#cd-content').html('<div class="cd-loading" style="color:#ef4444;"><i class="ri-user-unfollow-line" style="font-size:48px;opacity:.3;display:block;margin-bottom:12px;"></i><div>' + (res.message || 'Contact not found.') + '</div></div>');
        }
      } catch(e) {
        $('#cd-content').html('<div class="cd-loading" style="color:#ef4444;">Failed to parse response.</div>');
      }
    },
    error: function() {
      $('#cd-content').html('<div class="cd-loading" style="color:#ef4444;">Failed to connect to server.</div>');
    }
  });
}

function renderContact(c) {
  var fullName = ((c.first_name || '') + ' ' + (c.last_name || '')).trim() || 'Unnamed';
  var initials = ((c.first_name || '?').charAt(0) + (c.last_name || '').charAt(0)).toUpperCase();
  var created = c.date_entered ? new Date(c.date_entered).toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric', hour:'2-digit', minute:'2-digit' }) : '--';
  var modified = c.date_modified ? new Date(c.date_modified).toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric', hour:'2-digit', minute:'2-digit' }) : '--';

  var subtitle = [];
  if (c.title) subtitle.push(c.title);
  if (c.department) subtitle.push(c.department);

  var address = [c.primary_address_street, c.primary_address_city, c.primary_address_state, c.primary_address_postalcode, c.primary_address_country].filter(Boolean).join(', ');

  var html = '';

  html += '<div class="cd-header">';
  html += '<div class="flex items-start justify-between flex-wrap gap-4">';
  html += '<div class="flex items-center gap-4">';
  html += '<div class="cd-avatar-lg">' + esc(initials) + '</div>';
  html += '<div>';
  html += '<div class="cd-name">' + esc(fullName) + '</div>';
  if (subtitle.length) html += '<div class="cd-subtitle">' + esc(subtitle.join(' · ')) + '</div>';
  html += '<div class="flex items-center gap-2 mt-2 flex-wrap">';
  if (c.lead_source) html += '<span class="cd-tag" style="background:rgba(var(--primary-rgb),0.08);color:rgb(var(--primary-rgb));"><i class="ri-focus-3-line"></i> ' + esc(c.lead_source) + '</span>';
  if (c.do_not_call === '1') html += '<span class="cd-tag" style="background:rgba(239,68,68,0.08);color:#dc2626;"><i class="ri-phone-off-line"></i> Do Not Call</span>';
  html += '</div></div></div>';
  html += '<div class="flex items-center gap-2">';
  html += '<a href="edit_contact.php?id=' + c.id + '" class="cd-btn cd-btn-primary"><i class="ri-edit-line"></i> Edit</a>';
  html += '<button class="cd-btn cd-btn-danger" onclick="deleteContact(\'' + c.id + '\')"><i class="ri-delete-bin-line"></i> Delete</button>';
  html += '</div></div></div>';

  html += '<div class="cd-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">';

  html += '<div class="cd-card">';
  html += '<div class="cd-card-title"><i class="ri-phone-line"></i> Contact Information</div>';
  html += '<div class="cd-detail"><div class="cd-detail-label">Email</div><div class="cd-detail-value">' + (c.email_address ? '<a href="mailto:' + esc(c.email_address) + '">' + esc(c.email_address) + '</a>' : '<span style="color:var(--cd-muted)">Not set</span>') + '</div></div>';
  html += '<div class="cd-detail"><div class="cd-detail-label">Mobile Phone</div><div class="cd-detail-value">' + (c.phone_mobile ? '<a href="tel:' + esc(c.phone_mobile) + '">' + esc(c.phone_mobile) + '</a>' : '<span style="color:var(--cd-muted)">Not set</span>') + '</div></div>';
  html += '<div class="cd-detail"><div class="cd-detail-label">Work Phone</div><div class="cd-detail-value">' + (c.phone_work ? '<a href="tel:' + esc(c.phone_work) + '">' + esc(c.phone_work) + '</a>' : '<span style="color:var(--cd-muted)">Not set</span>') + '</div></div>';
  html += '<div class="cd-detail"><div class="cd-detail-label">Home Phone</div><div class="cd-detail-value">' + (c.phone_home || '<span style="color:var(--cd-muted)">Not set</span>') + '</div></div>';
  html += '</div>';

  html += '<div class="cd-card">';
  html += '<div class="cd-card-title"><i class="ri-map-pin-line"></i> Address</div>';
  html += '<div class="cd-detail"><div class="cd-detail-label">Full Address</div><div class="cd-detail-value">' + (address || '<span style="color:var(--cd-muted)">Not set</span>') + '</div></div>';
  html += '<div class="cd-detail"><div class="cd-detail-label">Street</div><div class="cd-detail-value">' + esc(c.primary_address_street || '--') + '</div></div>';
  html += '<div class="cd-detail"><div class="cd-detail-label">City</div><div class="cd-detail-value">' + esc(c.primary_address_city || '--') + '</div></div>';
  html += '<div class="cd-detail"><div class="cd-detail-label">State</div><div class="cd-detail-value">' + esc(c.primary_address_state || '--') + '</div></div>';
  html += '<div class="cd-detail"><div class="cd-detail-label">Country</div><div class="cd-detail-value">' + esc(c.primary_address_country || '--') + '</div></div>';
  html += '</div>';

  html += '</div>';

  html += '<div class="cd-card">';
  html += '<div class="cd-card-title"><i class="ri-file-text-line"></i> Description</div>';
  html += '<div style="font-size:14px;color:var(--cd-text);white-space:pre-wrap;line-height:1.7;">' + esc(c.description || 'No description provided.') + '</div>';
  html += '</div>';

  html += '<div class="cd-card">';
  html += '<div class="cd-card-title"><i class="ri-time-line"></i> System Info</div>';
  html += '<div class="cd-detail"><div class="cd-detail-label">Created</div><div class="cd-detail-value">' + created + '</div></div>';
  html += '<div class="cd-detail"><div class="cd-detail-label">Last Modified</div><div class="cd-detail-value">' + modified + '</div></div>';
  html += '<div class="cd-detail"><div class="cd-detail-label">Lead Source</div><div class="cd-detail-value">' + esc(c.lead_source || '--') + '</div></div>';
  html += '</div>';

  document.getElementById('cd-content').innerHTML = html;
}

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
          setTimeout(function() { window.location.href = 'contacts.php'; }, 1300);
        } else { Swal.fire({icon:'error',title:'Error',text:d.message || 'Failed to delete.'}); }
      }
    });
  });
};

function esc(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
</script>
