<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$userId = $_SESSION['user']['id'] ?? '';
?>

<style>
  .tx-page {
    --tx-surface: #ffffff;
    --tx-surface-2: #f8fafc;
    --tx-border: rgba(15,23,42,0.08);
    --tx-text: #0f172a;
    --tx-muted: rgba(15,23,42,0.55);
  }
  .dark .tx-page {
    --tx-surface: rgba(255,255,255,0.035);
    --tx-surface-2: rgba(255,255,255,0.05);
    --tx-border: rgba(255,255,255,0.08);
    --tx-text: rgba(255,255,255,0.92);
    --tx-muted: rgba(255,255,255,0.50);
  }

  .tx-stat-card {
    background: var(--tx-surface);
    border: 1px solid var(--tx-border);
    border-radius: 16px;
    padding: 20px 24px;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .tx-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(15,23,42,0.08);
  }
  .dark .tx-stat-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.3);
  }
  .tx-stat-card .tx-stat-glow {
    position: absolute;
    top: -20px;
    right: -20px;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    opacity: 0.08;
    pointer-events: none;
  }
  .tx-stat-card .tx-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }
  .tx-stat-card .tx-stat-num {
    font-size: 28px;
    font-weight: 800;
    line-height: 1;
    color: var(--tx-text);
    letter-spacing: -0.02em;
  }
  .tx-stat-card .tx-stat-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--tx-muted);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-top: 4px;
  }

  .tx-table-card {
    background: var(--tx-surface);
    border: 1px solid var(--tx-border);
    border-radius: 16px;
    overflow: hidden;
  }
  .tx-table-toolbar {
    padding: 16px 24px;
    border-bottom: 1px solid var(--tx-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
  }
  .tx-search {
    height: 38px;
    border-radius: 10px;
    border: 1px solid var(--tx-border);
    background: var(--tx-surface-2);
    color: var(--tx-text);
    padding: 0 12px 0 36px;
    font-size: 13px;
    outline: none;
    width: min(320px, 100%);
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .tx-search:focus {
    border-color: rgb(var(--primary-rgb));
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.12);
  }
  .tx-search-wrap {
    position: relative;
  }
  .tx-search-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 15px;
    color: var(--tx-muted);
    pointer-events: none;
  }

  .tx-table-card table { width: 100%; border-collapse: collapse; }
  .tx-table-card table thead th {
    background: var(--tx-surface-2);
    color: var(--tx-muted);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 12px 20px;
    border-bottom: 1px solid var(--tx-border);
    white-space: nowrap;
  }
  .tx-table-card table tbody td {
    padding: 14px 20px;
    vertical-align: middle;
    border-bottom: 1px solid var(--tx-border);
    font-size: 13px;
    color: var(--tx-text);
  }
  .tx-table-card table tbody tr {
    transition: background 0.15s;
  }
  .tx-table-card table tbody tr:hover td {
    background: rgba(var(--primary-rgb), 0.03);
  }
  .dark .tx-table-card table tbody tr:hover td {
    background: rgba(255,255,255,0.03);
  }
  .tx-table-card table tbody tr:last-child td {
    border-bottom: none;
  }

  .tx-badge {
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
  .tx-badge::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
  }
  .tx-badge-succeeded { background: rgba(34,197,94,0.10); color: #16a34a; }
  .tx-badge-succeeded::before { background: #16a34a; }
  .tx-badge-failed { background: rgba(239,68,68,0.10); color: #dc2626; }
  .tx-badge-failed::before { background: #dc2626; }
  .tx-badge-pending { background: rgba(245,158,11,0.10); color: #d97706; }
  .tx-badge-pending::before { background: #d97706; }
  .tx-badge-default { background: rgba(107,114,128,0.10); color: #6b7280; }
  .tx-badge-default::before { background: #6b7280; }

  .tx-filter-btn {
    height: 38px;
    border-radius: 10px;
    border: 1px solid var(--tx-border);
    background: transparent;
    color: var(--tx-muted);
    padding: 0 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .tx-filter-btn:hover, .tx-filter-btn.active {
    background: rgba(var(--primary-rgb), 0.06);
    color: rgb(var(--primary-rgb));
    border-color: rgba(var(--primary-rgb), 0.20);
  }

  .tx-amount {
    font-size: 14px;
    font-weight: 800;
    color: var(--tx-text);
  }
  .tx-amount-currency {
    font-size: 10px;
    font-weight: 600;
    color: var(--tx-muted);
    margin-left: 2px;
    text-transform: uppercase;
  }

  .tx-id-mono {
    font-size: 11px;
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    color: var(--tx-muted);
    word-break: break-all;
    max-width: 200px;
    display: inline-block;
  }

  .tx-date-cell {
    font-size: 12px;
    font-weight: 500;
    color: var(--tx-text);
  }
  .tx-date-cell .tx-date-time {
    font-size: 10px;
    color: var(--tx-muted);
    margin-top: 2px;
  }

  .tx-lead-cell {
    font-size: 13px;
    font-weight: 600;
    color: var(--tx-text);
  }
  .tx-lead-cell .tx-lead-id {
    font-size: 10px;
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    color: var(--tx-muted);
    margin-top: 2px;
    letter-spacing: 0.03em;
  }

  .tx-export-btn {
    height: 38px;
    border-radius: 10px;
    border: 1px solid var(--tx-border);
    background: transparent;
    color: var(--tx-muted);
    padding: 0 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .tx-export-btn:hover {
    background: rgba(var(--primary-rgb), 0.06);
    color: rgb(var(--primary-rgb));
    border-color: rgba(var(--primary-rgb), 0.20);
  }

  .tx-action-btn {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: 1px solid var(--tx-border);
    background: transparent;
    color: var(--tx-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
  }
  .tx-action-btn:hover {
    background: rgba(var(--primary-rgb), 0.08);
    color: rgb(var(--primary-rgb));
    border-color: rgba(var(--primary-rgb), 0.20);
  }

  @keyframes tx-fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .tx-animate-row {
    animation: tx-fadeIn 0.3s ease forwards;
  }
</style>

<div class="main-content app-content">
  <div class="container-fluid tx-page">

    <div class="flex items-start justify-between flex-wrap gap-3 mb-6">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
            <i class="ri-exchange-dollar-line text-primary text-base"></i>
          </div>
          <h1 class="page-title font-bold text-xl mb-0 text-defaulttextcolor dark:text-defaulttextcolor/90">Transactions</h1>
        </div>
        <p class="text-xs text-textmuted dark:text-textmuted/50 mt-1 mb-0 ms-10">Track all payment transactions processed through Stripe.</p>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" class="tx-filter-btn" onclick="loadTransactions()" title="Refresh">
          <i class="ri-refresh-line text-sm"></i> Refresh
        </button>
        <button type="button" class="tx-export-btn" id="tx-export-csv" title="Export CSV">
          <i class="ri-download-2-line text-sm"></i> Export
        </button>
      </div>
    </div>

    <div class="grid xl:grid-cols-4 lg:grid-cols-2 md:grid-cols-2 grid-cols-1 gap-4 mb-6">
      <div class="tx-stat-card">
        <div class="tx-stat-glow" style="background: rgb(var(--primary-rgb));"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="tx-stat-num" id="stat-total">0</div>
            <div class="tx-stat-label">Total Transactions</div>
          </div>
          <div class="tx-stat-icon bg-primary/10 text-primary"><i class="ri-exchange-line"></i></div>
        </div>
      </div>
      <div class="tx-stat-card">
        <div class="tx-stat-glow" style="background: #22c55e;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="tx-stat-num" id="stat-revenue">$0</div>
            <div class="tx-stat-label">Total Revenue</div>
          </div>
          <div class="tx-stat-icon bg-success/10 text-success"><i class="ri-money-dollar-circle-line"></i></div>
        </div>
      </div>
      <div class="tx-stat-card">
        <div class="tx-stat-glow" style="background: #22c55e;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="tx-stat-num" id="stat-succeeded">0</div>
            <div class="tx-stat-label">Succeeded</div>
          </div>
          <div class="tx-stat-icon bg-success/10 text-success"><i class="ri-checkbox-circle-line"></i></div>
        </div>
      </div>
      <div class="tx-stat-card">
        <div class="tx-stat-glow" style="background: #ef4444;"></div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="tx-stat-num" id="stat-other">0</div>
            <div class="tx-stat-label">Failed / Pending</div>
          </div>
          <div class="tx-stat-icon bg-danger/10 text-danger"><i class="ri-error-warning-line"></i></div>
        </div>
      </div>
    </div>

    <div class="tx-table-card mb-6">
      <div class="tx-table-toolbar">
        <div class="tx-search-wrap">
          <i class="ri-search-line"></i>
          <input type="text" class="tx-search" id="tx-search" placeholder="Search by lead, amount, intent...">
        </div>
        <div class="flex items-center gap-2 flex-wrap">
          <button class="tx-filter-btn active" data-filter="all" onclick="filterTx('all', this)">All</button>
          <button class="tx-filter-btn" data-filter="succeeded" onclick="filterTx('succeeded', this)"><span class="w-1.5 h-1.5 rounded-full bg-success inline-block"></span> Succeeded</button>
          <button class="tx-filter-btn" data-filter="pending" onclick="filterTx('pending', this)"><span class="w-1.5 h-1.5 rounded-full bg-warning inline-block"></span> Pending</button>
          <button class="tx-filter-btn" data-filter="failed" onclick="filterTx('failed', this)"><span class="w-1.5 h-1.5 rounded-full bg-danger inline-block"></span> Failed</button>
        </div>
      </div>
      <div class="overflow-auto">
        <table class="w-full text-nowrap">
          <thead>
            <tr>
              <th class="text-start">Date</th>
              <th class="text-start">Lead</th>
              <th class="text-start">Amount</th>
              <th class="text-start">Status</th>
              <th class="text-start">Payment Intent</th>
              <th class="text-start">Description</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="tx-table-body">
            <tr id="tx-table-loading">
              <td colspan="7" class="px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                    <i class="ri-loader-4-line text-2xl animate-spin text-primary"></i>
                  </div>
                  <span class="text-sm font-medium text-textmuted dark:text-textmuted/50">Loading transactions...</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script>
(function () {
  var ENTRY = <?php echo json_encode('https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint'); ?>;
  var USER_ID = <?php echo json_encode($userId); ?>;
  var allTransactions = [];
  var currentFilter = 'all';

  function parse(r) {
    if (typeof r === 'string') { try { r = JSON.parse(r); } catch(e) { r = {}; } }
    return r || {};
  }

  function esc(v) { return $('<span>').text(v || '').html(); }

  function badgeClass(status) {
    if (!status) return 'tx-badge-default';
    var s = status.toLowerCase();
    if (s === 'succeeded' || s === 'paid') return 'tx-badge-succeeded';
    if (s === 'failed' || s === 'canceled' || s === 'cancelled') return 'tx-badge-failed';
    if (s === 'pending' || s === 'requires_action' || s === 'processing') return 'tx-badge-pending';
    return 'tx-badge-default';
  }

  function statusGroup(status) {
    if (!status) return 'other';
    var s = status.toLowerCase();
    if (s === 'succeeded' || s === 'paid') return 'succeeded';
    if (s === 'failed' || s === 'canceled' || s === 'cancelled') return 'failed';
    if (s === 'pending' || s === 'requires_action' || s === 'processing') return 'pending';
    return 'other';
  }

  function formatDate(d) {
    if (!d) return { date: '—', time: '' };
    var dt = new Date(d);
    if (isNaN(dt.getTime())) return { date: d, time: '' };
    return {
      date: dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
      time: dt.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
    };
  }

  function updateStats(rows) {
    var total = rows.length, succeeded = 0, revenue = 0;
    for (var i = 0; i < rows.length; i++) {
      var sg = statusGroup(rows[i].status);
      if (sg === 'succeeded') {
        succeeded++;
        revenue += parseFloat(rows[i].amount || 0);
      }
    }
    $('#stat-total').text(total);
    $('#stat-revenue').text('$' + revenue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    $('#stat-succeeded').text(succeeded);
    $('#stat-other').text(total - succeeded);
  }

  window.loadTransactions = function () {
    updateStats([]);
    $('#tx-table-body').html(
      '<tr><td colspan="7" class="px-6 py-16 text-center">' +
        '<div class="flex flex-col items-center gap-3">' +
          '<div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">' +
            '<i class="ri-loader-4-line text-2xl animate-spin text-primary"></i>' +
          '</div>' +
          '<span class="text-sm font-medium text-textmuted dark:text-textmuted/50">Loading transactions...</span>' +
        '</div></td></tr>'
    );

    $.post(ENTRY, { action: 'fetch_user_transactions', user_id: USER_ID }).done(function (resp) {
      var data = parse(resp);
      if (!data.success) {
        allTransactions = [];
        updateStats([]);
        $('#tx-table-body').html(
          '<tr><td colspan="7" class="px-6 py-16 text-center">' +
            '<div class="flex flex-col items-center gap-3">' +
              '<div class="w-12 h-12 rounded-full bg-danger/10 flex items-center justify-center">' +
                '<i class="ri-error-warning-line text-2xl text-danger"></i>' +
              '</div>' +
              '<span class="text-sm font-semibold text-danger">' + esc(data.message || 'Failed to load transactions.') + '</span>' +
              '<button onclick="loadTransactions()" class="tx-filter-btn mt-1"><i class="ri-refresh-line me-1"></i> Retry</button>' +
            '</div></td></tr>'
        );
        return;
      }
      allTransactions = data.data || [];
      updateStats(allTransactions);
      applyFilterAndSearch();
    }).fail(function () {
      allTransactions = [];
      updateStats([]);
      $('#tx-table-body').html(
        '<tr><td colspan="7" class="px-6 py-16 text-center">' +
          '<div class="flex flex-col items-center gap-3">' +
            '<div class="w-12 h-12 rounded-full bg-danger/10 flex items-center justify-center">' +
              '<i class="ri-wifi-off-line text-2xl text-danger"></i>' +
            '</div>' +
            '<span class="text-sm font-semibold text-danger">Could not reach the server.</span>' +
            '<button onclick="loadTransactions()" class="tx-filter-btn mt-1"><i class="ri-refresh-line me-1"></i> Retry</button>' +
          '</div></td></tr>'
      );
    });
  };

  window.filterTx = function (status, btnEl) {
    currentFilter = status;
    $('.tx-filter-btn[data-filter]').removeClass('active');
    $(btnEl).addClass('active');
    applyFilterAndSearch();
  };

  function applyFilterAndSearch() {
    var query = ($('#tx-search').val() || '').toLowerCase().trim();
    var filtered = allTransactions;

    if (currentFilter !== 'all') {
      filtered = filtered.filter(function (t) {
        return statusGroup(t.status) === currentFilter;
      });
    }
    if (query) {
      filtered = filtered.filter(function (t) {
        var haystack = [
          t.lead_name, t.lead_id, t.amount, t.status,
          t.stripe_payment_intent_id, t.description, t.date_entered
        ].join(' ').toLowerCase();
        return haystack.indexOf(query) > -1;
      });
    }
    renderTable(filtered);
  }

  function renderTable(rows) {
    if (!rows || rows.length === 0) {
      var isFiltered = currentFilter !== 'all' || ($('#tx-search').val() || '').trim() !== '';
      $('#tx-table-body').html(
        '<tr><td colspan="7" class="px-6 py-20 text-center">' +
          '<div class="flex flex-col items-center max-w-xs mx-auto">' +
            '<div class="w-16 h-16 rounded-2xl bg-primary/5 dark:bg-white/5 flex items-center justify-center mb-4">' +
              '<i class="' + (isFiltered ? 'ri-filter-off-line' : 'ri-exchange-dollar-line') + ' text-3xl text-textmuted/40"></i>' +
            '</div>' +
            '<h3 class="text-base font-bold text-defaulttextcolor dark:text-defaulttextcolor/90 mb-1">' + (isFiltered ? 'No Matches' : 'No Transactions Yet') + '</h3>' +
            '<p class="text-sm text-textmuted dark:text-textmuted/50 mb-5">' + (isFiltered ? 'Try adjusting your search or filter.' : 'Transactions will appear here once payments are processed.') + '</p>' +
          '</div></td></tr>'
      );
      return;
    }

    var html = '';
    for (var i = 0; i < rows.length; i++) {
      var r = rows[i];
      var dt = formatDate(r.date_entered);
      var leadDisplay = r.lead_name || '—';
      var leadIdShort = (r.lead_id || '').substring(0, 8).toUpperCase();
      var currency = (r.currency || 'usd').toUpperCase();
      var leadHref = r.lead_id ? 'lead.php?id=' + encodeURIComponent(String(r.lead_id)) : '';
      var viewCell = leadHref
        ? '<a href="' + leadHref + '" class="tx-action-btn" title="View lead"><i class="ri-eye-line"></i></a>'
        : '<span class="text-xs" style="color:var(--tx-muted);">—</span>';

      html += '<tr class="tx-animate-row" style="animation-delay: ' + (i * 30) + 'ms">'
        + '<td><div class="tx-date-cell">' + esc(dt.date) + '<div class="tx-date-time">' + esc(dt.time) + '</div></div></td>'
        + '<td><div class="tx-lead-cell">' + esc(leadDisplay) + '<div class="tx-lead-id">' + esc(leadIdShort) + '</div></div></td>'
        + '<td><span class="tx-amount">$' + esc(r.amount) + '</span><span class="tx-amount-currency">' + esc(currency) + '</span></td>'
        + '<td><span class="tx-badge ' + badgeClass(r.status) + '">' + esc(r.status) + '</span></td>'
        + '<td><span class="tx-id-mono">' + esc(r.stripe_payment_intent_id || '—') + '</span></td>'
        + '<td><span class="text-xs" style="color:var(--tx-muted);">' + esc(r.description || '—') + '</span></td>'
        + '<td class="text-end">' + viewCell + '</td>'
        + '</tr>';
    }
    $('#tx-table-body').html(html);
  }

  $('#tx-search').on('input', function () {
    applyFilterAndSearch();
  });

  $('#tx-export-csv').on('click', function () {
    if (!allTransactions.length) return;
    var csv = 'Date,Lead,Amount,Currency,Status,Payment Intent,Description\n';
    for (var i = 0; i < allTransactions.length; i++) {
      var r = allTransactions[i];
      csv += '"' + (r.date_entered || '') + '","' + (r.lead_name || r.lead_id || '') + '","' + (r.amount || '0.00') + '","' + (r.currency || 'usd') + '","' + (r.status || '') + '","' + (r.stripe_payment_intent_id || '') + '","' + (r.description || '') + '"\n';
    }
    var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'transactions_' + new Date().toISOString().slice(0, 10) + '.csv';
    link.click();
  });

  loadTransactions();
})();
</script>
