<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$widgetUserId = isset($_SESSION['user']['id']) ? (string) $_SESSION['user']['id'] : '';
$widgetUserIdAttr = htmlspecialchars($widgetUserId, ENT_QUOTES, 'UTF-8');
$widgetHeightPreview = '620';
$widgetHeightEmbed = '800px';

$embedSnippet = '<div id="limogen-widget"
     data-user-id="' . $widgetUserId . '"
     data-width="100%"
     data-height="' . $widgetHeightEmbed . '">
</div>

<script src="https://zabrin.xyz/limogen-widget/widget.js" async></script>';
$embedSnippetEscaped = htmlspecialchars($embedSnippet, ENT_QUOTES, 'UTF-8');
?>

<style>
  .ig-page { --ig-surface: #ffffff; --ig-surface-2: #f8fafc; --ig-border: rgba(15,23,42,0.10); --ig-text: #0f172a; --ig-muted: rgba(15,23,42,0.55); }
  .dark .ig-page { --ig-surface: rgba(255,255,255,0.035); --ig-surface-2: rgba(255,255,255,0.05); --ig-border: rgba(255,255,255,0.08); --ig-text: rgba(255,255,255,0.92); --ig-muted: rgba(255,255,255,0.50); }

  .ig-card { background: var(--ig-surface); border: 1px solid var(--ig-border); border-radius: 16px; overflow: hidden; transition: box-shadow 0.2s; }
  .ig-card:hover { box-shadow: 0 4px 24px rgba(15,23,42,0.06); }
  .dark .ig-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.25); }
  .ig-card-header { background: rgba(15,23,42,0.025); border-bottom: 1px solid rgba(15,23,42,0.08); padding: 16px 24px; }
  .dark .ig-card-header { background: rgba(255,255,255,0.025); border-bottom-color: rgba(255,255,255,0.08); }
  .ig-card-body { padding: 24px; }
  .ig-card-icon { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }

  .ig-stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
  @media (max-width: 768px) { .ig-stat-grid { grid-template-columns: 1fr; } }
  .ig-stat { background: var(--ig-surface); border: 1px solid var(--ig-border); border-radius: 14px; padding: 20px 24px; display: flex; align-items: center; gap: 16px; transition: all 0.2s; }
  .ig-stat:hover { box-shadow: 0 4px 16px rgba(15,23,42,0.06); transform: translateY(-1px); }
  .dark .ig-stat:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.25); }
  .ig-stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
  .ig-stat-val { font-size: 24px; font-weight: 800; line-height: 1; color: var(--ig-text); }
  .ig-stat-label { font-size: 12px; color: var(--ig-muted); margin-top: 2px; }

  .ig-code-box {
    position: relative; border-radius: 14px; border: 1px solid var(--ig-border);
    background: var(--ig-surface-2); overflow: hidden;
  }
  .ig-code-box pre {
    margin: 0; padding: 20px 24px; overflow-x: auto;
    font-size: 12px; line-height: 1.7; font-family: 'JetBrains Mono', 'Fira Code', monospace;
    color: var(--ig-text);
  }
  .ig-code-copy {
    position: absolute; top: 12px; right: 12px; background: rgb(var(--primary-rgb)); color: #fff;
    border: none; border-radius: 10px; padding: 6px 14px; font-size: 12px; font-weight: 600;
    cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;
  }
  .ig-code-copy:hover { filter: brightness(1.1); transform: translateY(-1px); }

  .ig-info-item { padding: 14px 0; border-bottom: 1px solid rgba(15,23,42,0.06); display: flex; align-items: center; justify-content: space-between; gap: 12px; }
  .dark .ig-info-item { border-bottom-color: rgba(255,255,255,0.06); }
  .ig-info-item:last-child { border-bottom: none; }
  .ig-info-label { font-size: 12px; font-weight: 600; color: var(--ig-muted); display: flex; align-items: center; gap: 8px; }
  .ig-info-value { font-size: 13px; font-weight: 600; color: var(--ig-text); word-break: break-all; text-align: right; }

  .ig-domain-table { width: 100%; border-collapse: collapse; }
  .ig-domain-table th {
    text-align: left; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em;
    color: var(--ig-muted); padding: 10px 16px;
    border-bottom: 1px solid var(--ig-border); white-space: nowrap;
  }
  .ig-domain-table td {
    padding: 14px 16px; font-size: 13px; color: var(--ig-text);
    border-bottom: 1px solid rgba(15,23,42,0.04); vertical-align: middle;
  }
  .dark .ig-domain-table td { border-bottom-color: rgba(255,255,255,0.04); }
  .ig-domain-table tbody tr { transition: background 0.1s; }
  .ig-domain-table tbody tr:hover { background: rgba(var(--primary-rgb), 0.03); }
  .ig-domain-table tbody tr:last-child td { border-bottom: none; }

  .ig-domain-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;
    background: rgba(var(--primary-rgb), 0.08); color: rgb(var(--primary-rgb));
  }
  .ig-lead-count {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 28px; padding: 2px 8px; border-radius: 8px;
    font-size: 12px; font-weight: 700;
    background: rgba(34,197,94,0.10); color: #16a34a;
  }

  .ig-empty { text-align: center; padding: 40px 20px; color: var(--ig-muted); }
  .ig-skeleton { height: 48px; border-radius: 8px; margin-bottom: 8px;
    background: linear-gradient(90deg, var(--ig-surface-2) 25%, rgba(var(--primary-rgb),0.04) 50%, var(--ig-surface-2) 75%);
    background-size: 200% 100%; animation: ig-shimmer 1.5s infinite;
  }
  @keyframes ig-shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

  .ig-step { display: flex; gap: 16px; margin-bottom: 20px; }
  .ig-step:last-child { margin-bottom: 0; }
  .ig-step-num { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; flex-shrink: 0; background: rgba(var(--primary-rgb), 0.1); color: rgb(var(--primary-rgb)); }
  .ig-step-content h4 { font-size: 14px; font-weight: 700; color: var(--ig-text); margin: 0 0 4px 0; }
  .ig-step-content p { font-size: 12px; color: var(--ig-muted); margin: 0; line-height: 1.5; }
</style>

<div class="main-content app-content">
  <div class="container-fluid ig-page">

    <!-- Page Header -->
    <div class="flex items-center justify-between flex-wrap gap-3 py-4 mb-2">
      <div>
        <h1 class="page-title font-bold text-2xl mb-1" style="color:var(--ig-text)">Widget Integration</h1>
        <p class="text-sm" style="color:var(--ig-muted)">Embed the booking widget on your website and track lead sources</p>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" onclick="loadDomains()" class="ti-btn ti-btn-sm ti-btn-light !rounded-xl font-semibold">
          <i class="ri-refresh-line me-1"></i> Refresh
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div class="ig-stat-grid" id="ig-stats">
      <div class="ig-stat">
        <div class="ig-stat-icon bg-primary/10 text-primary"><i class="ri-global-line"></i></div>
        <div>
          <div class="ig-stat-val" id="ig-stat-domains">--</div>
          <div class="ig-stat-label">Active Domains</div>
        </div>
      </div>
      <div class="ig-stat">
        <div class="ig-stat-icon bg-success/10 text-success"><i class="ri-user-add-line"></i></div>
        <div>
          <div class="ig-stat-val" id="ig-stat-leads">--</div>
          <div class="ig-stat-label">Total Widget Leads</div>
        </div>
      </div>
      <div class="ig-stat">
        <div class="ig-stat-icon bg-info/10 text-info"><i class="ri-time-line"></i></div>
        <div>
          <div class="ig-stat-val" id="ig-stat-latest">--</div>
          <div class="ig-stat-label">Latest Lead</div>
        </div>
      </div>
    </div>

    <!-- Main Grid: Preview + Code + Domains -->
    <div class="grid grid-cols-12 gap-6 pb-12">

      <!-- LEFT: Preview -->
      <div class="xl:col-span-7 col-span-12">
        <div class="ig-card mb-6">
          <div class="ig-card-header">
            <div class="flex items-center gap-3">
              <div class="ig-card-icon bg-primary/10 text-primary"><i class="ri-eye-line"></i></div>
              <div>
                <div class="font-bold text-sm" style="color:var(--ig-text)">Live Preview</div>
                <div style="font-size:11px;color:var(--ig-muted);">How the widget appears to your visitors</div>
              </div>
            </div>
          </div>
          <div class="ig-card-body">
            <?php if ($widgetUserId === ''): ?>
              <div style="padding:24px;text-align:center;color:#f59e0b;font-size:14px;">
                <i class="ri-error-warning-line text-2xl" style="display:block;margin-bottom:8px;"></i>
                No user session found. Please log in again.
              </div>
            <?php else: ?>
              <div id="limogen-widget"
                   data-user-id="<?php echo $widgetUserIdAttr; ?>"
                   data-width="100%"
                   data-height="<?php echo htmlspecialchars($widgetHeightPreview, ENT_QUOTES, 'UTF-8'); ?>">
              </div>
              <script src="https://zabrin.xyz/limogen-widget/widget.js" async></script>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- RIGHT: Embed Code + Setup -->
      <div class="xl:col-span-5 col-span-12">

        <!-- Embed Code -->
        <div class="ig-card mb-6">
          <div class="ig-card-header">
            <div class="flex items-center gap-3">
              <div class="ig-card-icon bg-info/10 text-info"><i class="ri-code-s-slash-line"></i></div>
              <div>
                <div class="font-bold text-sm" style="color:var(--ig-text)">Embed Code</div>
                <div style="font-size:11px;color:var(--ig-muted);">Paste before <code style="font-size:10px;background:rgba(var(--primary-rgb),0.06);padding:2px 6px;border-radius:4px;">&lt;/body&gt;</code> on your website</div>
              </div>
            </div>
          </div>
          <div class="ig-card-body">
            <!-- Account Info -->
            <div class="ig-info-item" style="padding-top:0;">
              <span class="ig-info-label"><i class="ri-user-settings-line"></i> Linked Account</span>
              <span class="ig-info-value" style="font-family:monospace;font-size:11px;">
                <?php echo $widgetUserId !== '' ? $widgetUserIdAttr : '<span style="color:var(--ig-muted)">Not linked</span>'; ?>
              </span>
            </div>

            <?php if ($widgetUserId === ''): ?>
              <p style="font-size:13px;color:var(--ig-muted);text-align:center;padding:20px 0;">Log in to generate embed code.</p>
            <?php else: ?>
              <div class="ig-code-box" style="margin-top:8px;">
                <button type="button" class="ig-code-copy" id="ig-copy-btn" onclick="copyWidgetCode()">
                  <i class="ri-file-copy-line"></i> Copy
                </button>
                <pre id="ig-widget-code"><code><?php echo $embedSnippetEscaped; ?></code></pre>
              </div>
              <p id="ig-copy-status" style="font-size:11px;color:#16a34a;margin-top:8px;min-height:16px;"></p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Setup Guide -->
        <div class="ig-card mb-6">
          <div class="ig-card-header">
            <div class="flex items-center gap-3">
              <div class="ig-card-icon bg-warning/10 text-warning"><i class="ri-guide-line"></i></div>
              <div>
                <div class="font-bold text-sm" style="color:var(--ig-text)">Quick Setup</div>
                <div style="font-size:11px;color:var(--ig-muted);">Get started in 3 easy steps</div>
              </div>
            </div>
          </div>
          <div class="ig-card-body">
            <div class="ig-step">
              <div class="ig-step-num">1</div>
              <div class="ig-step-content">
                <h4>Copy the embed code</h4>
                <p>Click the copy button above to get the widget snippet.</p>
              </div>
            </div>
            <div class="ig-step">
              <div class="ig-step-num">2</div>
              <div class="ig-step-content">
                <h4>Paste on your website</h4>
                <p>Add the code before the closing <code style="font-size:11px;background:rgba(var(--primary-rgb),0.06);padding:1px 4px;border-radius:3px;">&lt;/body&gt;</code> tag on any page.</p>
              </div>
            </div>
            <div class="ig-step">
              <div class="ig-step-num">3</div>
              <div class="ig-step-content">
                <h4>Start receiving leads</h4>
                <p>The widget auto-detects the domain and tracks lead sources automatically.</p>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Embedded Domains (Full Width) -->
      <div class="col-span-12">
        <div class="ig-card">
          <div class="ig-card-header">
            <div class="flex items-center justify-between flex-wrap gap-3">
              <div class="flex items-center gap-3">
                <div class="ig-card-icon bg-success/10 text-success"><i class="ri-global-line"></i></div>
                <div>
                  <div class="font-bold text-sm" style="color:var(--ig-text)">Embedded Domains</div>
                  <div style="font-size:11px;color:var(--ig-muted);">Websites where the widget has generated leads</div>
                </div>
              </div>
              <span id="ig-domain-count" class="ig-domain-badge" style="display:none;">0 domains</span>
            </div>
          </div>
          <div class="ig-card-body" style="padding:0;">
            <!-- Loading -->
            <div id="ig-domain-loading" style="padding:24px;">
              <div class="ig-skeleton"></div>
              <div class="ig-skeleton"></div>
              <div class="ig-skeleton"></div>
            </div>
            <!-- Empty -->
            <div id="ig-domain-empty" class="ig-empty" style="display:none;">
              <i class="ri-global-line text-4xl mb-2" style="display:block;opacity:0.2;"></i>
              <div style="font-size:14px;font-weight:600;margin-bottom:4px;">No domains yet</div>
              <div style="font-size:12px;">Once leads come in from your embedded widget, their source domains will appear here.</div>
            </div>
            <!-- Error -->
            <div id="ig-domain-error" style="display:none;text-align:center;padding:30px;color:#ef4444;font-size:13px;">
              <i class="ri-error-warning-line text-lg"></i>
              <div style="margin-top:6px;">Failed to load domains. Please try refreshing.</div>
            </div>
            <!-- Table -->
            <div id="ig-domain-table-wrap" class="overflow-auto" style="display:none;">
              <table class="ig-domain-table">
                <thead>
                  <tr>
                    <th style="width:40px;">#</th>
                    <th>Domain / Source</th>
                    <th style="text-align:center;">Leads</th>
                    <th>Last Lead</th>
                    <th style="text-align:center;">Status</th>
                  </tr>
                </thead>
                <tbody id="ig-domain-tbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script>
function copyWidgetCode() {
  var codeBox = document.getElementById('ig-widget-code');
  if (!codeBox) return;
  var text = codeBox.innerText;
  var btn = document.getElementById('ig-copy-btn');
  var status = document.getElementById('ig-copy-status');
  var originalHtml = btn.innerHTML;

  function done() {
    btn.innerHTML = '<i class="ri-check-line"></i> Copied!';
    if (status) status.textContent = 'Copied to clipboard.';
    setTimeout(function() {
      btn.innerHTML = originalHtml;
      if (status) status.textContent = '';
    }, 2000);
  }

  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).then(done).catch(function() {
      fallbackCopy(text, done);
    });
  } else {
    fallbackCopy(text, done);
  }
}

function fallbackCopy(text, cb) {
  var ta = document.createElement('textarea');
  ta.value = text;
  document.body.appendChild(ta);
  ta.select();
  try { document.execCommand('copy'); cb(); } catch(e) {}
  document.body.removeChild(ta);
}

function formatDate(d) {
  if (!d) return '—';
  var date = new Date(d);
  if (isNaN(date.getTime())) return d;
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) +
         ' ' + date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

function escHtml(str) { var d = document.createElement('div'); d.textContent = str || ''; return d.innerHTML; }

function loadDomains() {
  var userId = <?php echo json_encode($widgetUserId); ?>;
  if (!userId) return;

  $('#ig-domain-loading').show();
  $('#ig-domain-empty, #ig-domain-error, #ig-domain-table-wrap').hide();
  $('#ig-domain-count').hide();
  $('#ig-stat-domains, #ig-stat-leads, #ig-stat-latest').text('--');

  $.post('https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint', {
    action: 'fetch_embedded_domains',
    user_id: userId
  }, function(resp) {
    if (typeof resp === 'string') { try { resp = JSON.parse(resp); } catch(e) { resp = {}; } }
    $('#ig-domain-loading').hide();

    var domains = (resp && resp.domains) ? resp.domains : [];
    if (!domains.length) {
      $('#ig-domain-empty').show();
      $('#ig-stat-domains').text('0');
      $('#ig-stat-leads').text('0');
      $('#ig-stat-latest').text('None');
      return;
    }

    var totalLeads = 0;
    var latestDate = '';
    domains.forEach(function(d) { totalLeads += d.leads; if (d.last_lead > latestDate) latestDate = d.last_lead; });

    $('#ig-stat-domains').text(domains.length);
    $('#ig-stat-leads').text(totalLeads);
    $('#ig-stat-latest').text(latestDate ? formatDate(latestDate) : 'None');
    $('#ig-domain-count').text(domains.length + (domains.length === 1 ? ' domain' : ' domains')).show();

    var tbody = $('#ig-domain-tbody');
    tbody.empty();

    domains.forEach(function(d, i) {
      var row = '<tr>' +
        '<td style="font-size:12px;color:var(--ig-muted);font-weight:600;">' + (i + 1) + '</td>' +
        '<td>' +
          '<div style="display:flex;align-items:center;gap:10px;">' +
            '<div style="width:32px;height:32px;border-radius:10px;background:rgba(var(--primary-rgb),0.08);display:flex;align-items:center;justify-content:center;">' +
              '<i class="ri-global-line" style="color:rgb(var(--primary-rgb));font-size:14px;"></i>' +
            '</div>' +
            '<div>' +
              '<div style="font-weight:700;font-size:13px;">' + escHtml(d.domain) + '</div>' +
            '</div>' +
          '</div>' +
        '</td>' +
        '<td style="text-align:center;"><span class="ig-lead-count">' + d.leads + '</span></td>' +
        '<td style="font-size:12px;white-space:nowrap;">' + formatDate(d.last_lead) + '</td>' +
        '<td style="text-align:center;">' +
          '<span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:3px 10px;border-radius:8px;background:rgba(34,197,94,0.10);color:#16a34a;">' +
            '<i class="ri-circle-fill" style="font-size:6px;"></i> Active' +
          '</span>' +
        '</td>' +
      '</tr>';
      tbody.append(row);
    });

    $('#ig-domain-table-wrap').show();
  }, 'json').fail(function() {
    $('#ig-domain-loading').hide();
    $('#ig-domain-error').show();
  });
}

$(function() {
  loadDomains();
});
</script>
