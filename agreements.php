<?php
include_once 'components/layout/header.php';
include_once 'components/layout/sidebar.php';
include_once 'config/api.php';

$userId = $_SESSION['user']['id'] ?? '';
$data = ['id' => $userId];
$rawLeads = fetchAllUserLeads($data);
if (!is_array($rawLeads)) {
    $rawLeads = [];
}

function agr_is_converted_status(?string $status): bool {
    $s = strtolower(trim((string)$status));
    return in_array($s, ['converted', 'won', 'success'], true);
}

$agreementRows = [];
foreach ($rawLeads as $L) {
    if (!agr_is_converted_status($L['status'] ?? null)) {
        continue;
    }
    $agreementRows[] = [
        'id' => (string)($L['id'] ?? ''),
        'first_name' => (string)($L['first_name'] ?? ''),
        'last_name' => (string)($L['last_name'] ?? ''),
        'email1' => (string)($L['email1'] ?? ''),
        'total_price_c' => $L['total_price_c'] ?? '',
        'status' => (string)($L['status'] ?? ''),
        'agreement_pdf_c' => (string)($L['agreement_pdf_c'] ?? ''),
        'agreement_sign_date_c' => (string)($L['agreement_sign_date_c'] ?? ''),
    ];
}

$agrJson = json_encode(
    $agreementRows,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
);
if ($agrJson === false) {
    $agrJson = '[]';
}
?>

<style>
  .agr-page {
    --agr-surface: #ffffff;
    --agr-surface-2: #f8fafc;
    --agr-border: rgba(15,23,42,0.08);
    --agr-text: #0f172a;
    --agr-muted: rgba(15,23,42,0.55);
  }
  .dark .agr-page {
    --agr-surface: rgba(255,255,255,0.035);
    --agr-surface-2: rgba(255,255,255,0.05);
    --agr-border: rgba(255,255,255,0.08);
    --agr-text: rgba(255,255,255,0.92);
    --agr-muted: rgba(255,255,255,0.50);
  }

  .agr-stat-card {
    background: var(--agr-surface);
    border: 1px solid var(--agr-border);
    border-radius: 16px;
    padding: 20px 24px;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .agr-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(15,23,42,0.08);
  }
  .dark .agr-stat-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.3);
  }
  .agr-stat-card .agr-stat-glow {
    position: absolute;
    top: -20px;
    right: -20px;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    opacity: 0.08;
    pointer-events: none;
  }
  .agr-stat-card .agr-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }
  .agr-stat-card .agr-stat-num {
    font-size: 28px;
    font-weight: 800;
    line-height: 1;
    color: var(--agr-text);
    letter-spacing: -0.02em;
  }
  .agr-stat-card .agr-stat-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--agr-muted);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-top: 4px;
  }

  .agr-table-card {
    background: var(--agr-surface);
    border: 1px solid var(--agr-border);
    border-radius: 16px;
    overflow: hidden;
  }
  .agr-table-toolbar {
    padding: 16px 24px;
    border-bottom: 1px solid var(--agr-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
  }
  .agr-search {
    height: 38px;
    border-radius: 10px;
    border: 1px solid var(--agr-border);
    background: var(--agr-surface-2);
    color: var(--agr-text);
    padding: 0 12px 0 36px;
    font-size: 13px;
    outline: none;
    width: min(320px, 100%);
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .agr-search:focus {
    border-color: rgb(var(--primary-rgb));
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12);
  }
  .agr-search-wrap { position: relative; }
  .agr-search-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 15px;
    color: var(--agr-muted);
    pointer-events: none;
  }

  .agr-table-card table thead th {
    background: var(--agr-surface-2);
    color: var(--agr-muted);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 12px 20px;
    border-bottom: 1px solid var(--agr-border);
    white-space: nowrap;
  }
  .agr-table-card table tbody td {
    padding: 14px 20px;
    vertical-align: middle;
    border-bottom: 1px solid var(--agr-border);
    font-size: 13px;
    color: var(--agr-text);
  }
  .agr-table-card table tbody tr {
    transition: background 0.15s;
    cursor: pointer;
  }
  .agr-table-card table tbody tr:hover td {
    background: rgba(var(--primary-rgb), 0.03);
  }
  .dark .agr-table-card table tbody tr:hover td {
    background: rgba(255,255,255,0.03);
  }
  .agr-table-card table tbody tr:last-child td {
    border-bottom: none;
  }

  .agr-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }
  .agr-badge::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
  }
  .agr-badge-signed { background: rgba(34,197,94,0.10); color: #16a34a; }
  .agr-badge-signed::before { background: #16a34a; }
  .agr-badge-pending { background: rgba(245,158,11,0.10); color: #d97706; }
  .agr-badge-pending::before { background: #d97706; }

  .agr-filter-btn {
    height: 38px;
    border-radius: 10px;
    border: 1px solid var(--agr-border);
    background: transparent;
    color: var(--agr-muted);
    padding: 0 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .agr-filter-btn:hover, .agr-filter-btn.active {
    background: rgba(var(--primary-rgb), 0.06);
    color: rgb(var(--primary-rgb));
    border-color: rgba(var(--primary-rgb), 0.20);
  }

  .agr-action-btn {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: 1px solid var(--agr-border);
    background: transparent;
    color: var(--agr-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
  }
  .agr-action-btn:hover {
    background: rgba(var(--primary-rgb), 0.08);
    color: rgb(var(--primary-rgb));
    border-color: rgba(var(--primary-rgb), 0.20);
  }

  .agr-pdf-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: rgb(var(--primary-rgb));
    text-decoration: none;
  }
  .agr-pdf-link:hover { text-decoration: underline; }

  .agr-email-cell {
    max-width: 220px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .agr-rate-val { font-size: 15px; font-weight: 800; color: var(--agr-text); }

  @keyframes agr-fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .agr-animate-row { animation: agr-fadeIn 0.3s ease forwards; }

  .agr-info-card {
    background: var(--agr-surface);
    border: 1px solid var(--agr-border);
    border-radius: 16px;
    padding: 16px 20px;
    margin-top: 24px;
  }
  .agr-info-card summary {
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: var(--agr-text);
    list-style: none;
  }
  .agr-info-card summary::-webkit-details-marker { display: none; }
  .agr-info-card summary::after {
    content: '';
    float: right;
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 6px solid var(--agr-muted);
    margin-top: 6px;
  }
  .agr-info-card[open] summary::after {
    transform: rotate(180deg);
  }
</style>

<div class="main-content app-content">
  <div class="container-fluid agr-page">

    <div class="flex items-start justify-between flex-wrap gap-3 mb-6">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="ri-file-paper-2-line text-primary text-base"></i>
          </div>
          <h1 class="page-title font-bold text-xl mb-0 text-defaulttextcolor dark:text-defaulttextcolor/90">Agreements</h1>
        </div>
        <p class="text-xs text-textmuted dark:text-textmuted/50 mt-1 mb-0 ms-10">
          Converted leads you own (<code class="text-[10px]">owner_c</code>) with agreement PDF and signature date when available.
        </p>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" class="agr-filter-btn" onclick="location.reload()" title="Reload from server">
          <i class="ri-refresh-line text-sm"></i> Refresh
        </button>
      </div>
    </div>

    <div class="grid xl:grid-cols-4 lg:grid-cols-2 md:grid-cols-2 grid-cols-1 gap-4 mb-6">
      <div class="agr-stat-card">
        <div class="agr-stat-glow" style="background: rgb(var(--primary-rgb));"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="agr-stat-num" id="agr-stat-total">0</div>
            <div class="agr-stat-label">Converted deals</div>
          </div>
          <div class="agr-stat-icon bg-primary/10 text-primary"><i class="ri-user-star-line"></i></div>
        </div>
      </div>
      <div class="agr-stat-card">
        <div class="agr-stat-glow" style="background: #22c55e;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="agr-stat-num" id="agr-stat-signed">0</div>
            <div class="agr-stat-label">PDF on file</div>
          </div>
          <div class="agr-stat-icon bg-success/10 text-success"><i class="ri-file-check-line"></i></div>
        </div>
      </div>
      <div class="agr-stat-card">
        <div class="agr-stat-glow" style="background: #f59e0b;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="agr-stat-num" id="agr-stat-pending">0</div>
            <div class="agr-stat-label">Awaiting PDF</div>
          </div>
          <div class="agr-stat-icon bg-warning/10 text-warning"><i class="ri-time-line"></i></div>
        </div>
      </div>
      <div class="agr-stat-card">
        <div class="agr-stat-glow" style="background: #6366f1;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="agr-stat-num" id="agr-stat-value">$0</div>
            <div class="agr-stat-label">Combined amount</div>
          </div>
          <div class="agr-stat-icon bg-indigo-500/10 text-indigo-500"><i class="ri-money-dollar-circle-line"></i></div>
        </div>
      </div>
    </div>

    <div class="agr-table-card mb-6">
      <div class="agr-table-toolbar">
        <div class="agr-search-wrap">
          <i class="ri-search-line"></i>
          <input type="text" class="agr-search" id="agr-search" placeholder="Search name, email, amount...">
        </div>
        <div class="flex items-center gap-2 flex-wrap">
          <button type="button" class="agr-filter-btn active" data-filter="all" onclick="agrFilter('all', this)">All</button>
          <button type="button" class="agr-filter-btn" data-filter="signed" onclick="agrFilter('signed', this)"><span class="w-1.5 h-1.5 rounded-full bg-success inline-block"></span> PDF saved</button>
          <button type="button" class="agr-filter-btn" data-filter="pending" onclick="agrFilter('pending', this)"><span class="w-1.5 h-1.5 rounded-full bg-warning inline-block"></span> No PDF yet</button>
        </div>
      </div>
      <div class="overflow-auto">
        <table class="w-full">
          <thead>
            <tr>
              <th class="text-start">Lead</th>
              <th class="text-start">Email</th>
              <th class="text-start">Amount</th>
              <th class="text-start">PDF</th>
              <th class="text-start">Signed</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="agr-table-body">
            <tr>
              <td colspan="6" class="px-6 py-16 text-center text-textmuted text-sm">Loading…</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <details class="agr-info-card">
      <summary>Setup &amp; client signing</summary>
      <p class="text-xs text-textmuted dark:text-textmuted/50 mt-3 mb-2 max-w-2xl">
        Use <strong>Agreement link</strong> on a lead record to copy the public URL. Clients sign and pay without logging in.
      </p>
      <ul class="list-disc ms-5 text-sm text-textmuted space-y-1">
        <li>Run <code class="text-xs">database/limo_stripe_agreement.sql</code> on the SuiteCRM database (and add <code class="text-xs">stripe_customer_id_c</code> in Studio if needed).</li>
        <li>Create <code class="text-xs">custom/limo_agreement_config.php</code> on the SuiteCRM server (see <code class="text-xs">database/limo_agreement_config.sample.php</code>).</li>
      </ul>
    </details>

  </div>
</div>

<script type="application/json" id="agr-data"><?php echo $agrJson; ?></script>

<?php include_once 'components/layout/footer.php'; ?>

<script>
(function () {
  var raw = document.getElementById('agr-data');
  var allLeads = [];
  try { allLeads = JSON.parse(raw.textContent || '[]'); } catch (e) { allLeads = []; }
  if (!Array.isArray(allLeads)) allLeads = [];

  var currentFilter = 'all';

  function esc(v) { return $('<span>').text(v == null ? '' : String(v)).html(); }

  function hasPdf(row) {
    return !!(row.agreement_pdf_c && String(row.agreement_pdf_c).trim());
  }

  function money(n) {
    var x = parseFloat(n);
    if (isNaN(x)) x = 0;
    return '$' + x.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function parseMoney(row) {
    var v = row.total_price_c;
    if (v == null || v === '') return 0;
    if (typeof v === 'number') return v;
    return parseFloat(String(v).replace(/[^0-9.\-]/g, '')) || 0;
  }

  function fullName(row) {
    return [row.first_name, row.last_name].filter(Boolean).join(' ').trim() || '—';
  }

  function formatSigned(d) {
    if (!d || !String(d).trim()) return '—';
    var dt = new Date(d);
    if (isNaN(dt.getTime())) return esc(String(d));
    return esc(dt.toLocaleString(undefined, { dateStyle: 'medium', timeStyle: 'short' }));
  }

  function updateStats(list) {
    var total = list.length;
    var signed = 0;
    var value = 0;
    for (var i = 0; i < list.length; i++) {
      if (hasPdf(list[i])) signed++;
      value += parseMoney(list[i]);
    }
    $('#agr-stat-total').text(total);
    $('#agr-stat-signed').text(signed);
    $('#agr-stat-pending').text(total - signed);
    $('#agr-stat-value').text(money(value));
  }

  window.agrFilter = function (mode, btn) {
    currentFilter = mode;
    $('.agr-filter-btn[data-filter]').removeClass('active');
    $(btn).addClass('active');
    applyFilter();
  };

  function applyFilter() {
    var q = ($('#agr-search').val() || '').toLowerCase().trim();
    var filtered = allLeads.slice();

    if (currentFilter === 'signed') {
      filtered = filtered.filter(hasPdf);
    } else if (currentFilter === 'pending') {
      filtered = filtered.filter(function (r) { return !hasPdf(r); });
    }

    if (q) {
      filtered = filtered.filter(function (r) {
        var hay = [fullName(r), r.email1, r.total_price_c, r.agreement_pdf_c, r.agreement_sign_date_c, r.id].join(' ').toLowerCase();
        return hay.indexOf(q) > -1;
      });
    }

    renderRows(filtered);
  }

  function renderRows(rows) {
    if (!rows.length) {
      var filtered = currentFilter !== 'all' || ($('#agr-search').val() || '').trim() !== '';
      $('#agr-table-body').html(
        '<tr><td colspan="6" class="px-6 py-20 text-center">' +
          '<div class="flex flex-col items-center max-w-xs mx-auto">' +
            '<div class="w-16 h-16 rounded-2xl bg-primary/5 dark:bg-white/5 flex items-center justify-center mb-4">' +
              '<i class="' + (filtered ? 'ri-filter-off-line' : 'ri-file-paper-line') + ' text-3xl text-textmuted/40"></i>' +
            '</div>' +
            '<h3 class="text-base font-bold text-defaulttextcolor dark:text-defaulttextcolor/90 mb-1">' + (filtered ? 'No matches' : 'No converted leads') + '</h3>' +
            '<p class="text-sm text-textmuted dark:text-textmuted/50">' + (filtered ? 'Try another filter or search.' : 'When a lead is converted and assigned to you as owner, it appears here.') + '</p>' +
          '</div></td></tr>'
      );
      return;
    }

    var html = '';
    for (var i = 0; i < rows.length; i++) {
      var r = rows[i];
      var id = String(r.id || '');
      var name = fullName(r);
      var pdf = hasPdf(r);
      var pdfCell = pdf
        ? '<a href="' + esc(r.agreement_pdf_c) + '" target="_blank" rel="noopener" class="agr-pdf-link" onclick="event.stopPropagation();"><i class="ri-external-link-line"></i> Open</a>'
        : '<span class="agr-badge agr-badge-pending">Pending</span>';
      var signCell = formatSigned(r.agreement_sign_date_c);

      html += '<tr class="agr-animate-row" data-lead-id="' + esc(id) + '" style="animation-delay:' + (i * 25) + 'ms">' +
        '<td><div class="text-[13px] font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90">' + esc(name) + '</div>' +
            '<div class="text-[10px] text-textmuted font-mono mt-0.5">' + esc(id.substring(0, 8).toUpperCase()) + '</div></td>' +
        '<td><div class="agr-email-cell" title="' + esc(r.email1 || '') + '">' + esc(r.email1 || '—') + '</div></td>' +
        '<td><span class="agr-rate-val">' + esc(money(parseMoney(r))) + '</span></td>' +
        '<td>' + pdfCell + '</td>' +
        '<td><div class="text-xs text-defaulttextcolor dark:text-defaulttextcolor/90">' + signCell + '</div></td>' +
        '<td class="text-end"><a href="lead.php?id=' + encodeURIComponent(id) + '" class="agr-action-btn" title="View lead" onclick="event.stopPropagation();"><i class="ri-eye-line"></i></a></td>' +
        '</tr>';
    }
    $('#agr-table-body').html(html);

    $('#agr-table-body').off('click.agrRow').on('click.agrRow', 'tr[data-lead-id]', function (e) {
      if ($(e.target).closest('a, button').length) return;
      window.location.href = 'lead.php?id=' + encodeURIComponent($(this).data('lead-id'));
    });
  }

  updateStats(allLeads);
  applyFilter();

  $('#agr-search').on('input', applyFilter);
})();
</script>
