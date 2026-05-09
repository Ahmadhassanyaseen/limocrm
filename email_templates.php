<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<style>
  .et-page { --et-surface: #ffffff; --et-surface-2: #f8fafc; --et-border: rgba(15,23,42,0.08); --et-text: #0f172a; --et-muted: rgba(15,23,42,0.55); }
  .dark .et-page { --et-surface: rgba(255,255,255,0.035); --et-surface-2: rgba(255,255,255,0.05); --et-border: rgba(255,255,255,0.08); --et-text: rgba(255,255,255,0.92); --et-muted: rgba(255,255,255,0.50); }

  .et-stat { background: var(--et-surface); border: 1px solid var(--et-border); border-radius: 16px; padding: 20px 24px; position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; }
  .et-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(15,23,42,0.08); }
  .dark .et-stat:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
  .et-stat .et-glow { position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; border-radius: 50%; opacity: 0.08; pointer-events: none; }
  .et-stat .et-stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
  .et-stat .et-stat-num { font-size: 28px; font-weight: 800; line-height: 1; color: var(--et-text); letter-spacing: -0.02em; }
  .et-stat .et-stat-label { font-size: 12px; font-weight: 600; color: var(--et-muted); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px; }

  .et-table-card { background: var(--et-surface); border: 1px solid var(--et-border); border-radius: 16px; overflow: hidden; }
  .et-toolbar { padding: 16px 20px; border-bottom: 1px solid var(--et-border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
  .et-search { height: 38px; border-radius: 10px; border: 1px solid var(--et-border); background: var(--et-surface-2); color: var(--et-text); padding: 0 12px 0 36px; font-size: 13px; outline: none; width: min(320px, 100%); transition: border-color 0.2s, box-shadow 0.2s; }
  .et-search:focus { border-color: rgb(var(--primary-rgb)); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12); }
  .et-search-wrap { position: relative; }
  .et-search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 15px; color: var(--et-muted); pointer-events: none; }
  .et-filter-btn { height: 34px; border-radius: 8px; border: 1px solid var(--et-border); background: transparent; color: var(--et-muted); padding: 0 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
  .et-filter-btn:hover, .et-filter-btn.active { background: rgba(var(--primary-rgb), 0.06); color: rgb(var(--primary-rgb)); border-color: rgba(var(--primary-rgb), 0.20); }

  .et-table { width: 100%; border-collapse: collapse; }
  .et-table th { text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--et-muted); padding: 12px 16px; border-bottom: 1px solid var(--et-border); white-space: nowrap; }
  .et-table td { padding: 14px 16px; font-size: 13px; color: var(--et-text); border-bottom: 1px solid rgba(15,23,42,0.04); vertical-align: middle; }
  .dark .et-table td { border-bottom-color: rgba(255,255,255,0.04); }
  .et-table tbody tr { transition: background 0.1s; }
  .et-table tbody tr:hover { background: rgba(var(--primary-rgb), 0.03); }
  .et-table tbody tr:last-child td { border-bottom: none; }

  .et-avatar { width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
  .et-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 8px; text-transform: uppercase; letter-spacing: 0.04em; }
  .et-badge-email { background: rgba(59,130,246,0.10); color: #2563eb; }
  .et-badge-campaign { background: rgba(139,92,246,0.10); color: #7c3aed; }
  .et-badge-support { background: rgba(245,158,11,0.10); color: #d97706; }
  .et-badge-default { background: rgba(var(--primary-rgb),0.08); color: rgb(var(--primary-rgb)); }

  .et-action-btn { background: none; border: 1px solid var(--et-border); border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; color: var(--et-muted); font-size: 14px; text-decoration: none; }
  .et-action-btn:hover { border-color: rgba(var(--primary-rgb),0.3); color: rgb(var(--primary-rgb)); background: rgba(var(--primary-rgb),0.04); }
  .et-action-btn.danger:hover { border-color: rgba(239,68,68,0.3); color: #dc2626; background: rgba(239,68,68,0.04); }

  .et-empty { text-align: center; padding: 60px 20px; color: var(--et-muted); }
  .et-empty i { display: block; font-size: 48px; opacity: 0.15; margin-bottom: 12px; }

  @keyframes et-fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
  .et-animate-row { animation: et-fadeIn 0.25s ease forwards; }

  .et-preview-modal .ti-modal-box { max-width: 800px; }
  .et-preview-modal .ti-modal-content { border-radius: 20px !important; border: 1px solid var(--et-border) !important; overflow: hidden; }
  .et-preview-modal .ti-modal-header { background: var(--et-surface-2); border-bottom: 1px solid var(--et-border); padding: 18px 24px; }
  .et-preview-modal .ti-modal-footer { border-top: 1px solid var(--et-border); padding: 14px 24px; background: var(--et-surface-2); }
</style>

<div class="main-content app-content">
  <div class="container-fluid et-page">

    <!-- Page Header -->
    <div class="flex items-start justify-between flex-wrap gap-3 mb-6">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="ri-mail-send-line text-primary text-base"></i>
          </div>
          <h1 class="page-title font-bold text-xl mb-0" style="color:var(--et-text)">Email Templates</h1>
        </div>
        <p class="text-xs mt-1 mb-0 ms-10" style="color:var(--et-muted)">Design and manage your professional communication assets.</p>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" class="et-action-btn" title="Refresh" onclick="loadTemplates()" style="width:36px;height:36px;font-size:16px;">
          <i class="ri-refresh-line"></i>
        </button>
        <a href="email_template.php" class="ti-btn ti-btn-sm bg-primary text-white font-semibold shadow-sm hover:shadow-md transition-all !rounded-xl px-4">
          <i class="ri-add-line me-1 text-base"></i> New Template
        </a>
      </div>
    </div>

    <!-- Stats -->
    <div id="et-stats" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
      <div class="et-stat">
        <div class="et-glow" style="background:rgb(var(--primary-rgb));"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="et-stat-num" id="stat-total">—</div>
            <div class="et-stat-label">Total Templates</div>
          </div>
          <div class="et-stat-icon bg-primary/10 text-primary"><i class="ri-file-list-3-line"></i></div>
        </div>
      </div>
      <div class="et-stat">
        <div class="et-glow" style="background:#3b82f6;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="et-stat-num" id="stat-email">—</div>
            <div class="et-stat-label">Email</div>
          </div>
          <div class="et-stat-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6;"><i class="ri-mail-line"></i></div>
        </div>
      </div>
      <div class="et-stat">
        <div class="et-glow" style="background:#8b5cf6;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="et-stat-num" id="stat-campaign">—</div>
            <div class="et-stat-label">Campaign</div>
          </div>
          <div class="et-stat-icon" style="background:rgba(139,92,246,0.1);color:#8b5cf6;"><i class="ri-megaphone-line"></i></div>
        </div>
      </div>
      <div class="et-stat">
        <div class="et-glow" style="background:#f59e0b;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="et-stat-num" id="stat-other">—</div>
            <div class="et-stat-label">Other</div>
          </div>
          <div class="et-stat-icon" style="background:rgba(245,158,11,0.1);color:#f59e0b;"><i class="ri-archive-line"></i></div>
        </div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="et-table-card mb-6">
      <div class="et-toolbar">
        <div class="et-search-wrap">
          <i class="ri-search-line"></i>
          <input type="text" class="et-search" id="et-search" placeholder="Search by name or subject...">
        </div>
        <div class="flex items-center gap-2" id="et-filter-bar">
          <button class="et-filter-btn active" data-filter="all" onclick="filterTemplates('all',this)">All</button>
          <button class="et-filter-btn" data-filter="email" onclick="filterTemplates('email',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#3b82f6;"></span> Email</button>
          <button class="et-filter-btn" data-filter="campaign" onclick="filterTemplates('campaign',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#8b5cf6;"></span> Campaign</button>
          <button class="et-filter-btn" data-filter="other" onclick="filterTemplates('other',this)"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#f59e0b;"></span> Other</button>
        </div>
      </div>
      <div class="overflow-auto">
        <table class="et-table">
          <thead>
            <tr>
              <th>Template</th>
              <th>Type</th>
              <th>Subject Line</th>
              <th>Created</th>
              <th style="text-align:center;">Actions</th>
            </tr>
          </thead>
          <tbody id="template-list">
            <tr><td colspan="5" class="et-empty"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading templates...</td></tr>
          </tbody>
        </table>
        <div id="et-empty" class="et-empty" style="display:none;">
          <i class="ri-mail-add-line"></i>
          <div style="font-size:14px;font-weight:600;margin-bottom:4px;">No templates found</div>
          <div style="font-size:12px;">Try adjusting your search or filter.</div>
        </div>
      </div>
    </div>

    <!-- Preview Modal -->
    <div class="hs-overlay ti-modal hidden et-preview-modal" id="preview-modal" tabindex="-1" aria-overlay="true">
      <div class="hs-overlay-open:mt-7 ti-modal-box mt-0 ease-out">
        <div class="ti-modal-content">
          <div class="ti-modal-header">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-success/10 flex items-center justify-center"><i class="ri-eye-line text-success text-lg"></i></div>
              <div>
                <div style="font-size:15px;font-weight:700;color:var(--et-text);" id="preview-title">Template Preview</div>
                <div style="font-size:11px;font-weight:500;color:var(--et-muted);margin-top:1px;">See how your email looks to recipients</div>
              </div>
            </div>
            <button type="button" class="hs-dropdown-toggle ti-modal-close-btn" data-hs-overlay="#preview-modal">
              <span class="sr-only">Close</span><i class="ri-close-line"></i>
            </button>
          </div>
          <div class="ti-modal-body p-0" style="background:var(--et-surface-2);height:550px;">
            <iframe id="preview-iframe" class="w-full h-full border-0" style="border-radius:0;"></iframe>
          </div>
          <div class="ti-modal-footer flex justify-end">
            <button type="button" class="ti-btn btn-wave ti-btn-light !rounded-xl" data-hs-overlay="#preview-modal">Close Preview</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var allTemplates = [];
var _etFilter = 'all';
var API = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';

function filterTemplates(type, btn) {
  _etFilter = type;
  document.querySelectorAll('.et-filter-btn').forEach(function(b) { b.classList.remove('active'); });
  if (btn) btn.classList.add('active');
  applyFilters();
}

function applyFilters() {
  var q = ($('#et-search').val() || '').toLowerCase().trim();
  var filtered = allTemplates.filter(function(t) {
    var matchType = (_etFilter === 'all') || (getTypeGroup(t.type) === _etFilter);
    var matchSearch = !q || (t.name || '').toLowerCase().indexOf(q) !== -1 || (t.subject || '').toLowerCase().indexOf(q) !== -1;
    return matchType && matchSearch;
  });
  renderTemplates(filtered);
}

function getTypeGroup(type) {
  if (!type) return 'other';
  var t = type.toLowerCase();
  if (t === 'email') return 'email';
  if (t === 'campaign') return 'campaign';
  return 'other';
}

function getTypeBadgeClass(type) {
  var g = getTypeGroup(type);
  if (g === 'email') return 'et-badge-email';
  if (g === 'campaign') return 'et-badge-campaign';
  return 'et-badge-support';
}

window.loadTemplates = function() {
  $('#template-list').html('<tr><td colspan="5" class="et-empty"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading templates...</td></tr>');
  $('#et-empty').hide();

  $.ajax({
    url: API, type: 'POST',
    data: { action: 'fetch_email_templates', id: '<?php echo $_SESSION['user']['id']; ?>' },
    success: function(response) {
      try {
        allTemplates = typeof response === 'string' ? JSON.parse(response) : response;
        if (!Array.isArray(allTemplates)) allTemplates = [];
        updateStats();
        applyFilters();
      } catch (e) {
        allTemplates = [];
        $('#template-list').html('<tr><td colspan="5" class="et-empty" style="color:#ef4444;"><i class="ri-error-warning-line" style="display:inline;font-size:16px;opacity:1;margin-right:4px;"></i> Unable to parse response.</td></tr>');
      }
    },
    error: function() {
      $('#template-list').html('<tr><td colspan="5" class="et-empty" style="color:#ef4444;"><i class="ri-wifi-off-line" style="display:inline;font-size:16px;opacity:1;margin-right:4px;"></i> Failed to connect to server.</td></tr>');
    }
  });
};

function updateStats() {
  var counts = { total: allTemplates.length, email: 0, campaign: 0, other: 0 };
  allTemplates.forEach(function(t) {
    var g = getTypeGroup(t.type);
    counts[g] = (counts[g] || 0) + 1;
  });
  $('#stat-total').text(counts.total);
  $('#stat-email').text(counts.email);
  $('#stat-campaign').text(counts.campaign);
  $('#stat-other').text(counts.other);
}

function renderTemplates(templates) {
  if (!templates.length) {
    $('#template-list').html('');
    $('#et-empty').show();
    return;
  }
  $('#et-empty').hide();
  var html = '';
  templates.forEach(function(t, idx) {
    var date = new Date(t.date_entered).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    var initial = (t.name || '?').charAt(0).toUpperCase();
    var badgeClass = getTypeBadgeClass(t.type);
    var typeLabel = (t.type || 'other').charAt(0).toUpperCase() + (t.type || 'other').slice(1);
    html += '<tr class="et-animate-row" style="animation-delay:' + (idx * 25) + 'ms">';
    html += '<td><div class="flex items-center gap-3">';
    html += '<div class="et-avatar bg-primary/10 text-primary">' + escHtml(initial) + '</div>';
    html += '<div><div style="font-weight:600;color:var(--et-text);max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escHtml(t.name) + '</div>';
    html += '<div style="font-size:11px;color:var(--et-muted);max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escHtml(t.description || 'No description') + '</div>';
    html += '</div></div></td>';
    html += '<td><span class="et-badge ' + badgeClass + '">' + escHtml(typeLabel) + '</span></td>';
    html += '<td><div style="color:var(--et-muted);max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escHtml(t.subject || '—') + '</div></td>';
    html += '<td style="color:var(--et-muted);white-space:nowrap;">' + date + '</td>';
    html += '<td><div class="flex items-center justify-center gap-1">';
    html += '<button class="et-action-btn preview-template-btn" title="Preview" data-id="' + t.id + '"><i class="ri-eye-line"></i></button>';
    html += '<a href="email_template.php?id=' + t.id + '" class="et-action-btn" title="Edit"><i class="ri-edit-line"></i></a>';
    html += '<button class="et-action-btn danger delete-template-btn" title="Delete" data-id="' + t.id + '"><i class="ri-delete-bin-line"></i></button>';
    html += '</div></td>';
    html += '</tr>';
  });
  $('#template-list').html(html);
}

$('#et-search').on('input', applyFilters);

$(document).ready(function() {
  loadTemplates();

  $(document).on('click', '.preview-template-btn', function() {
    var id = $(this).data('id');
    var template = allTemplates.find(function(t) { return t.id == id; });
    if (template) {
      $('#preview-title').text(template.name);
      HSOverlay.open(document.querySelector('#preview-modal'));
      setTimeout(function() {
        var doc = $('#preview-iframe')[0].contentWindow.document;
        doc.open();
        var txt = document.createElement('textarea');
        txt.innerHTML = template.body_html;
        doc.write(txt.value);
        doc.close();
      }, 200);
    }
  });

  $(document).on('click', '.delete-template-btn', function() {
    var id = $(this).data('id');
    Swal.fire({
      title: 'Delete template?', text: 'This cannot be undone.', icon: 'warning',
      showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete', cancelButtonText: 'Cancel'
    }).then(function(res) {
      if (!res.isConfirmed) return;
      $.ajax({
        url: API, type: 'POST', data: { action: 'delete_email_template', id: id },
        success: function(response) {
          var d = typeof response === 'string' ? JSON.parse(response) : response;
          if (d.success) {
            Swal.fire({ icon: 'success', title: 'Deleted!', text: 'Template removed.', timer: 1500, showConfirmButton: false });
            loadTemplates();
          } else {
            Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Failed to delete.' });
          }
        }
      });
    });
  });
});

function escHtml(s) {
  var d = document.createElement('div');
  d.textContent = s || '';
  return d.innerHTML;
}
</script>
