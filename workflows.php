<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap');

  .wf2 {
    --wf-bg: #f8fafc;
    --wf-card: #ffffff;
    --wf-border: rgba(15, 23, 42, .10);
    --wf-text: #0f172a;
    --wf-muted: rgba(15, 23, 42, .62);
    --wf-blue: #2563eb;
  }
  .dark .wf2{
    --wf-bg: #0b1220;
    --wf-card: rgba(255,255,255,.03);
    --wf-border: rgba(255,255,255,.10);
    --wf-text: rgba(255,255,255,.92);
    --wf-muted: rgba(255,255,255,.60);
  }

  .wf2, .wf2 * { font-family: "DM Sans", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
  .wf2 code, .wf2 pre, .wf2 .mono { font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }

  .wf2 .canvas {
    background: var(--wf-bg);
    border: 1px solid var(--wf-border);
    border-radius: 20px;
    padding: 18px;
  }
  .wf2 .card {
    background: var(--wf-card);
    border: 1px solid var(--wf-border);
    border-radius: 18px;
    overflow: hidden;
  }
  .wf2 .card-h {
    padding: 14px 16px;
    border-bottom: 1px solid var(--wf-border);
    display:flex; align-items:center; justify-content:space-between; gap:12px;
  }
  .wf2 .card-b { padding: 12px 16px; }
  .wf2 .h1 { font-weight: 800; letter-spacing: -.02em; color: var(--wf-text); }
  .wf2 .muted { color: var(--wf-muted); }
  .wf2 .btn-blue{
    background: var(--wf-blue);
    color:#fff;
    border-radius: 12px;
    padding: 10px 14px;
    font-weight: 700;
    border: 1px solid rgba(37,99,235,.35);
  }
  .wf2 .btn-blue:disabled{ opacity:.7; cursor:not-allowed; }
  .wf2 .btn-ghost{
    background: transparent;
    border: 1px solid var(--wf-border);
    border-radius: 12px;
    padding: 10px 12px;
    color: var(--wf-text);
    font-weight: 700;
  }
  .wf2 .badge{
    display:inline-flex; align-items:center; gap:8px;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
  }
  .wf2 .badge-active{
    background: rgba(34,197,94,.10);
    color: #16a34a;
    border: 1px solid rgba(34,197,94,.25);
  }
  .wf2 .badge-inactive{
    background: rgba(148,163,184,.18);
    color: rgba(15,23,42,.70);
    border: 1px solid rgba(15,23,42,.12);
  }
  .dark .wf2 .badge-inactive{
    color: rgba(255,255,255,.72);
    border-color: rgba(255,255,255,.12);
  }
  .wf2 .table th{
    font-size: 12px;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--wf-muted);
    font-weight: 800;
    border-bottom: 1px solid var(--wf-border);
    padding: 12px 14px;
  }
  .wf2 .table td{
    padding: 14px;
    border-bottom: 1px solid var(--wf-border);
    color: var(--wf-text);
    vertical-align: middle;
  }
  .wf2 .row:hover{ background: rgba(37,99,235,.04); }
  .dark .wf2 .row:hover{ background: rgba(37,99,235,.10); }
  .wf2 .quick a, .wf2 .quick button{
    width: 36px; height: 36px;
    display:inline-flex; align-items:center; justify-content:center;
    border-radius: 999px;
    border: 1px solid var(--wf-border);
    background: transparent;
    color: var(--wf-text);
  }
  .wf2 .quick a:hover, .wf2 .quick button:hover{
    border-color: rgba(37,99,235,.35);
    background: rgba(37,99,235,.06);
  }
  .wf2 .input{
    height: 44px;
    border-radius: 14px;
    border: 1px solid var(--wf-border);
    background: var(--wf-card);
    color: var(--wf-text);
    padding: 0 14px;
    width: 100%;
  }
  .wf2 .input:focus{ outline: none; box-shadow: 0 0 0 4px rgba(37,99,235,.12); border-color: rgba(37,99,235,.45); }
</style>

<div class="main-content app-content wf2">
  <div class="container-fluid">
    <div class="canvas mt-4">
      <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
          <div class="muted text-sm font-semibold">Workflow Automation</div>
          <div class="h1 text-2xl mt-1">Workflows</div>
          <div class="muted text-sm mt-1">Simple automation: 1 condition → 1 email template</div>
        </div>
        <div class="flex items-center gap-2">
          <button class="btn-ghost" type="button" onclick="loadWorkflows()">
            <i class="ri-refresh-line me-1"></i> Refresh
          </button>
          <a class="btn-blue" href="<?php echo $APP_BASE; ?>workflows/create.php">
            <i class="ri-add-line me-1"></i> New Workflow
          </a>
        </div>
      </div>

        <div class="card mt-4">
        <div class="card-h">
          <div class="font-bold" style="color:var(--wf-text)">All Workflows</div>
          <div class="w-full max-w-[420px]">
            <input id="search" class="input" placeholder="Search name / module / condition / template..." />
          </div>
        </div>
        <div class="card-b p-0">
          <div class="table-responsive">
            <table class="table w-full">
              <thead>
                <tr>
                  <th class="text-start">Name</th>
                  <th class="text-center">Module</th>
                  <th class="text-center">Condition</th>
                  <th class="text-start">Email Template</th>
                  <th class="text-center">Status</th>
                  <th class="text-start">Created</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody id="rows">
                <tr><td colspan="7" class="text-center py-12 muted">Loading…</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="card mt-4" id="emptyState" style="display:none">
        <div class="card-b text-center py-12">
          <div class="text-5xl mb-3" style="color:rgba(37,99,235,.45)"><i class="ri-git-merge-line"></i></div>
          <div class="h1 text-xl">No workflows yet</div>
          <div class="muted mt-2">Create your first conditions-first workflow for Leads or Quotes.</div>
          <div class="mt-5">
            <a class="btn-blue" href="create.php"><i class="ri-add-line me-1"></i> New Workflow</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const SUITE_ENTRYPOINT = "https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint";
  const SESSION_USER_ID  = "<?php echo $_SESSION['user']['id'] ?? ''; ?>";
  let all = [];

  async function postForm(url, dataObj){
    const body = new URLSearchParams();
    Object.keys(dataObj).forEach(k => body.append(k, dataObj[k] ?? ""));
    return fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
      body
    });
  }

  function fmtDate(s){
    if(!s) return "—";
    try { return new Date(s).toLocaleString(); } catch(e){ return s; }
  }

  function conditionLabel(t, statusValue, eventField){
    const map = {
      on_create: "On Create",
      status_equals: "Status = " + (statusValue || "…"),
      event_date: "Event date (" + (eventField || "event_date_c") + ")"
    };
    return map[t] || (t || "—");
  }

  function statusBadge(s){
    if(String(s) === "Active") return `<span class="badge badge-active">Active</span>`;
    return `<span class="badge badge-inactive">Inactive</span>`;
  }

  async function loadWorkflows(){
    document.getElementById("rows").innerHTML = `<tr><td colspan="7" class="text-center py-12 muted">Loading…</td></tr>`;
    document.getElementById("emptyState").style.display = "none";

    const res = await postForm(SUITE_ENTRYPOINT, { action: "fetch_workflows", id: SESSION_USER_ID });
    const text = await res.text();
    let json;
    try { json = JSON.parse(text); } catch(e){
      document.getElementById("rows").innerHTML = `<tr><td colspan="7" class="text-center py-12 text-danger">Invalid server response</td></tr>`;
      return;
    }
    all = (json.rows && Array.isArray(json.rows)) ? json.rows : (Array.isArray(json) ? json : []);
    render(all);
  }

  function render(list){
    const host = document.getElementById("rows");
    if(!list.length){
      host.innerHTML = "";
      document.getElementById("emptyState").style.display = "block";
      return;
    }
    document.getElementById("emptyState").style.display = "none";

    host.innerHTML = list.map(w => {
      const mod = w.module_name || "—";
      const cond = conditionLabel(w.condition_type, w.status_value, w.event_date_field);
      const created = w.date_entered;
      return `
        <tr class="row">
          <td class="text-start">
            <div class="font-bold">${w.workflow_name || "—"}</div>
            <div class="muted text-sm">${w.description || ""}</div>
          </td>
          <td class="text-center"><span class="mono text-sm">${mod}</span></td>
          <td class="text-center"><span class="mono text-sm">${cond}</span></td>
          <td class="text-start">
            <div class="text-sm" style="color:var(--wf-text)">${w.template_name || w.email_template_id || "—"}</div>
          </td>
          <td class="text-center">${statusBadge(w.status)}</td>
          <td class="text-start muted text-sm">${fmtDate(created)}</td>
          <td class="text-end">
            <div class="quick inline-flex items-center gap-2">
              <a href="edit_workflow.php?id=${encodeURIComponent(w.id)}" title="Edit"><i class="ri-edit-line"></i></a>
              <button type="button" onclick="deleteWorkflow('${w.id}')" title="Delete"><i class="ri-delete-bin-line"></i></button>
            </div>
          </td>
        </tr>
      `;
    }).join("");
  }

  async function deleteWorkflow(id){
    const ok = await Swal.fire({
      title: "Delete workflow?",
      text: "This will soft-delete the workflow.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Delete",
      confirmButtonColor: "#ef4444",
    });
    if(!ok.isConfirmed) return;

    const res = await postForm(SUITE_ENTRYPOINT, { action:"delete_workflow", id });
    const text = await res.text();
    let json;
    try { json = JSON.parse(text); } catch(e){ json = { success:false, message:"Invalid response" }; }
    if(json.success){
      await Swal.fire({ icon:"success", title:"Deleted", timer:900, showConfirmButton:false });
      loadWorkflows();
    } else {
      Swal.fire({ icon:"error", title:"Delete failed", text: json.message || "Error" });
    }
  }

  document.getElementById("search").addEventListener("input", function(){
    const q = (this.value || "").toLowerCase();
    const filtered = all.filter(w =>
      String(w.workflow_name || "").toLowerCase().includes(q) ||
      String(w.module_name || "").toLowerCase().includes(q) ||
      String(w.condition_type || "").toLowerCase().includes(q) ||
      String(w.template_name || w.email_template_id || "").toLowerCase().includes(q)
    );
    render(filtered);
  });

  loadWorkflows();
</script>

