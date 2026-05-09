<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>
<?php
$emailId = $_GET['id'] ?? '';
$leadId  = $_GET['lead_id'] ?? '';
?>

<style>
  .em-page { --em-surface: #ffffff; --em-surface-2: #f8fafc; --em-border: rgba(15,23,42,0.10); --em-text: #0f172a; --em-muted: rgba(15,23,42,0.55); }
  .dark .em-page { --em-surface: rgba(255,255,255,0.035); --em-surface-2: rgba(255,255,255,0.05); --em-border: rgba(255,255,255,0.08); --em-text: rgba(255,255,255,0.92); --em-muted: rgba(255,255,255,0.50); }

  .em-card { background: var(--em-surface); border: 1px solid var(--em-border); border-radius: 16px; overflow: hidden; transition: box-shadow 0.2s; }
  .em-card:hover { box-shadow: 0 4px 24px rgba(15,23,42,0.06); }
  .dark .em-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.25); }
  .em-card-header { background: rgba(15,23,42,0.025); border-bottom: 1px solid rgba(15,23,42,0.08); padding: 18px 24px; }
  .dark .em-card-header { background: rgba(255,255,255,0.025); border-bottom-color: rgba(255,255,255,0.08); }
  .em-card-body { padding: 0; }
  .em-card-icon { width: 32px; height: 32px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }

  .em-sticky-header {
    position: sticky; top: 0; z-index: 99;
    border-bottom: 1px solid var(--em-border);
    padding: 14px 24px; margin: -24px -24px 24px -24px;
    backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
    background: rgba(255,255,255,0.88); transition: box-shadow 0.2s;
  }
  .dark .em-sticky-header { background: rgba(18,18,30,0.88); }
  .em-sticky-header.scrolled { box-shadow: 0 4px 20px rgba(15,23,42,0.08); }
  .dark .em-sticky-header.scrolled { box-shadow: 0 4px 20px rgba(0,0,0,0.30); }

  .em-meta-row { display: flex; align-items: flex-start; gap: 12px; padding: 10px 24px; font-size: 13px; border-bottom: 1px solid rgba(15,23,42,0.05); }
  .dark .em-meta-row { border-bottom-color: rgba(255,255,255,0.05); }
  .em-meta-label { font-weight: 700; color: var(--em-muted); min-width: 55px; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; padding-top: 1px; }
  .em-meta-value { color: var(--em-text); word-break: break-word; flex: 1; }

  .em-body-frame {
    padding: 28px 32px; min-height: 300px;
    font-size: 14px; line-height: 1.8; color: var(--em-text);
  }
  .em-body-frame img { max-width: 100%; height: auto; }

  .em-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 8px; }
  .em-badge.sent { background: rgba(34,197,94,0.10); color: #16a34a; }
  .em-badge.draft { background: rgba(245,158,11,0.10); color: #d97706; }
  .em-badge.failed { background: rgba(239,68,68,0.10); color: #dc2626; }
  .em-badge.out { background: rgba(59,130,246,0.10); color: #3b82f6; }
  .em-badge.inbound { background: rgba(139,92,246,0.10); color: #8b5cf6; }

  .em-skeleton { border-radius: 8px; background: linear-gradient(90deg, var(--em-surface-2) 25%, rgba(var(--primary-rgb),0.04) 50%, var(--em-surface-2) 75%); background-size: 200% 100%; animation: em-shimmer 1.5s infinite; }
  @keyframes em-shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

  .em-plain-text { white-space: pre-wrap; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 13px; background: var(--em-surface-2); border-radius: 12px; padding: 20px 24px; }

  .em-html-body { padding: 0; min-height: 300px; }
  .em-html-body img { max-width: 100% !important; height: auto !important; }
  .em-html-body table { max-width: 100% !important; }
</style>

<div class="main-content app-content">
  <div class="container-fluid em-page">

    <!-- Sticky Header -->
    <div class="em-sticky-header" id="em-sticky-header">
      <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
          <?php if (!empty($leadId)): ?>
            <a href="lead.php?id=<?php echo urlencode($leadId); ?>" class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary hover:bg-primary/20 transition-colors flex-shrink-0" title="Back to Lead">
              <i class="ri-arrow-left-line text-lg"></i>
            </a>
          <?php else: ?>
            <a href="leads.php" class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary hover:bg-primary/20 transition-colors flex-shrink-0" title="Back to Leads">
              <i class="ri-arrow-left-line text-lg"></i>
            </a>
          <?php endif; ?>
          <div>
            <h1 class="text-lg font-bold mb-0 leading-tight" style="color:var(--em-text)" id="em-subject-title">Loading email...</h1>
            <div class="flex items-center gap-2 mt-0.5" id="em-header-meta" style="font-size:12px;color:var(--em-muted);">
              <span id="em-header-date">—</span>
              <span id="em-header-badges"></span>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0" id="em-header-actions" style="display:none;">
          <?php if (!empty($leadId)): ?>
            <a href="lead.php?id=<?php echo urlencode($leadId); ?>" style="height:38px;border-radius:10px;border:1px solid var(--em-border);background:var(--em-surface);color:var(--em-text);padding:0 16px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
              <i class="ri-arrow-left-s-line"></i> Back to Lead
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div id="em-loading" class="mb-6">
      <div class="em-card">
        <div style="padding:24px;">
          <div class="em-skeleton" style="height:20px;width:60%;margin-bottom:16px;"></div>
          <div class="em-skeleton" style="height:14px;width:40%;margin-bottom:10px;"></div>
          <div class="em-skeleton" style="height:14px;width:50%;margin-bottom:10px;"></div>
          <div class="em-skeleton" style="height:14px;width:35%;margin-bottom:24px;"></div>
          <div class="em-skeleton" style="height:200px;"></div>
        </div>
      </div>
    </div>

    <!-- Error -->
    <div id="em-error" style="display:none;" class="mb-6">
      <div class="em-card">
        <div style="text-align:center;padding:60px 20px;color:var(--em-muted);">
          <i class="ri-error-warning-line text-4xl mb-3" style="display:block;opacity:0.3;color:#ef4444;"></i>
          <div style="font-size:16px;font-weight:600;color:var(--em-text);margin-bottom:6px;">Email not found</div>
          <div style="font-size:13px;">The email may have been deleted or is not accessible.</div>
          <?php if (!empty($leadId)): ?>
            <a href="lead.php?id=<?php echo urlencode($leadId); ?>" style="display:inline-flex;align-items:center;gap:6px;margin-top:16px;color:rgb(var(--primary-rgb));font-weight:600;font-size:13px;text-decoration:none;">
              <i class="ri-arrow-left-line"></i> Return to Lead
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Email Content -->
    <div id="em-content" style="display:none;" class="grid grid-cols-12 gap-6 pb-12">

      <!-- Main Email Card -->
      <div class="col-span-12 xl:col-span-8">
        <div class="em-card">
          <div class="em-card-header">
            <div class="flex items-center gap-3">
              <div class="em-card-icon bg-info/10 text-info"><i class="ri-mail-open-line"></i></div>
              <div style="flex:1;min-width:0;">
                <div class="font-semibold text-base" style="color:var(--em-text);" id="em-subject">—</div>
              </div>
            </div>
          </div>
          <!-- Meta rows -->
          <div class="em-meta-row">
            <span class="em-meta-label">From</span>
            <span class="em-meta-value" id="em-from">—</span>
          </div>
          <div class="em-meta-row">
            <span class="em-meta-label">To</span>
            <span class="em-meta-value" id="em-to">—</span>
          </div>
          <div class="em-meta-row" id="em-cc-row" style="display:none;">
            <span class="em-meta-label">CC</span>
            <span class="em-meta-value" id="em-cc">—</span>
          </div>
          <div class="em-meta-row">
            <span class="em-meta-label">Date</span>
            <span class="em-meta-value" id="em-date">—</span>
          </div>
          <!-- Email body -->
          <div class="em-card-body">
            <div id="em-body-html" class="em-html-body" style="display:none;"></div>
            <div id="em-body-text" class="em-body-frame em-plain-text" style="display:none;"></div>
            <div id="em-body-empty" class="em-body-frame" style="display:none;text-align:center;padding:40px;color:var(--em-muted);">
              <i class="ri-mail-line text-3xl mb-2" style="display:block;opacity:0.2;"></i>No email content available
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar Info -->
      <div class="col-span-12 xl:col-span-4">
        <div class="em-card" style="position:sticky;top:80px;">
          <div class="em-card-header">
            <div class="flex items-center gap-3">
              <div class="em-card-icon bg-primary/10 text-primary"><i class="ri-information-line"></i></div>
              <div class="font-semibold text-sm" style="color:var(--em-text)">Email Details</div>
            </div>
          </div>
          <div style="padding:20px 24px;">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(15,23,42,0.06);font-size:13px;" class="dark-border-row">
              <span style="color:var(--em-muted);">Status</span>
              <span id="em-info-status">—</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(15,23,42,0.06);font-size:13px;" class="dark-border-row">
              <span style="color:var(--em-muted);">Type</span>
              <span id="em-info-type">—</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(15,23,42,0.06);font-size:13px;" class="dark-border-row">
              <span style="color:var(--em-muted);">Date Sent</span>
              <span id="em-info-date" style="font-weight:600;color:var(--em-text);">—</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(15,23,42,0.06);font-size:13px;" class="dark-border-row">
              <span style="color:var(--em-muted);">Flagged</span>
              <span id="em-info-flagged" style="font-weight:600;color:var(--em-text);">—</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(15,23,42,0.06);font-size:13px;" class="dark-border-row" id="em-info-parent-row" style="display:none;">
              <span style="color:var(--em-muted);">Related To</span>
              <span id="em-info-parent" style="font-weight:600;color:var(--em-text);">—</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;font-size:13px;">
              <span style="color:var(--em-muted);">Email ID</span>
              <span id="em-info-id" style="font-size:11px;color:var(--em-muted);font-family:monospace;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="">—</span>
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
$(function () {
  var emailId = <?php echo json_encode($emailId); ?>;
  var leadId  = <?php echo json_encode($leadId); ?>;

  function esc(s) { return $('<span>').text(s || '').html(); }
  function decodeHtml(str) {
    if (!str) return '';
    var txt = document.createElement('textarea');
    txt.innerHTML = str;
    return txt.value;
  }
  function fmtDate(d) {
    if (!d) return '—';
    var dt = new Date(d);
    if (isNaN(dt.getTime())) return d;
    return dt.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' }) +
           ' at ' + dt.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
  }

  if (!emailId) {
    $('#em-loading').hide();
    $('#em-error').show();
    return;
  }

  $.post('https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint', {
    action: 'fetch_single_email',
    email_id: emailId
  }, function (resp) {
    if (typeof resp === 'string') { try { resp = JSON.parse(resp); } catch(e) { resp = {}; } }
    $('#em-loading').hide();

    if (!resp || !resp.success || !resp.email) {
      $('#em-error').show();
      return;
    }

    var em = resp.email;
    var dateSent = em.date_sent_received || em.date_sent || em.date_entered || '';

    // Header
    $('#em-subject-title').text(em.name || '(No subject)');
    $('#em-header-date').text(fmtDate(dateSent));

    var statusCls = (em.status || '').toLowerCase() === 'sent' ? 'sent' : (em.status || '').toLowerCase() === 'draft' ? 'draft' : 'failed';
    var typeCls = em.type === 'out' ? 'out' : 'inbound';
    var typeLabel = em.type === 'out' ? 'Outbound' : 'Inbound';
    $('#em-header-badges').html(
      '<span class="em-badge ' + statusCls + '" style="margin-left:4px;"><i class="ri-circle-fill" style="font-size:6px;"></i> ' + esc((em.status || 'unknown').charAt(0).toUpperCase() + (em.status || '').slice(1)) + '</span>' +
      '<span class="em-badge ' + typeCls + '" style="margin-left:4px;"><i class="' + (em.type === 'out' ? 'ri-send-plane-line' : 'ri-mail-download-line') + '"></i> ' + typeLabel + '</span>'
    );
    $('#em-header-actions').show();

    // Main card
    $('#em-subject').text(em.name || '(No subject)');
    $('#em-from').text(em.from_addr || '—');
    $('#em-to').text(em.to_addrs || '—');
    if (em.cc_addrs) {
      $('#em-cc').text(em.cc_addrs);
      $('#em-cc-row').show();
    }
    $('#em-date').text(fmtDate(dateSent));

    // Body — decode HTML entities since SuiteCRM stores them encoded
    if (em.description_html) {
      $('#em-body-html').html(decodeHtml(em.description_html)).show();
    } else if (em.description) {
      $('#em-body-text').text(em.description).show();
    } else {
      $('#em-body-empty').show();
    }

    // Sidebar info
    $('#em-info-status').html('<span class="em-badge ' + statusCls + '"><i class="ri-circle-fill" style="font-size:6px;"></i> ' + esc((em.status || 'unknown').charAt(0).toUpperCase() + (em.status || '').slice(1)) + '</span>');
    $('#em-info-type').html('<span class="em-badge ' + typeCls + '"><i class="' + (em.type === 'out' ? 'ri-send-plane-line' : 'ri-mail-download-line') + '"></i> ' + typeLabel + '</span>');
    $('#em-info-date').text(fmtDate(dateSent));
    $('#em-info-flagged').html(em.flagged === '1' ? '<i class="ri-flag-fill" style="color:#f59e0b;"></i> Yes' : 'No');
    if (em.parent_type) {
      $('#em-info-parent').text(em.parent_type + (em.parent_id ? ' #' + em.parent_id.substring(0, 8) : ''));
      $('#em-info-parent-row').show();
    }
    $('#em-info-id').text(em.id).attr('title', em.id);

    $('#em-content').show();

  }, 'json').fail(function () {
    $('#em-loading').hide();
    $('#em-error').show();
  });

  // Sticky header scroll shadow
  var stickyHeader = document.getElementById('em-sticky-header');
  if (stickyHeader) {
    var scrollParent = stickyHeader.closest('.app-content') || window;
    (scrollParent === window ? window : scrollParent).addEventListener('scroll', function () {
      stickyHeader.classList.toggle('scrolled', (scrollParent === window ? window.scrollY : scrollParent.scrollTop) > 10);
    });
  }
});
</script>
