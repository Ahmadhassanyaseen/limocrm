<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap');
  .wf2 { --blue:#2563EB; --bg:#f8fafc; --card:#fff; --border:rgba(15,23,42,.10); --text:#0f172a; --muted:rgba(15,23,42,.62); }
  .dark .wf2{ --bg:#0b1220; --card:rgba(255,255,255,.03); --border:rgba(255,255,255,.10); --text:rgba(255,255,255,.92); --muted:rgba(255,255,255,.60); }
  .wf2,.wf2 *{ font-family:"DM Sans",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; }
  .wf2 code,.wf2 pre,.wf2 .mono{ font-family:"JetBrains Mono",ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace; }
  .wf2 .shell{ background:var(--bg); border:1px solid var(--border); border-radius:20px; overflow:hidden; }
  .wf2 .topbar{ padding:16px 18px; display:flex; align-items:center; justify-content:space-between; gap:12px; background:var(--card); border-bottom:1px solid var(--border); }
  .wf2 .title{ font-weight:900; letter-spacing:-.02em; color:var(--text); font-size:20px; }
  .wf2 .muted{ color:var(--muted); }
  .wf2 .btn{ border-radius:12px; padding:10px 14px; font-weight:800; border:1px solid var(--border); background:transparent; color:var(--text); }
  .wf2 .btn-blue{ background:var(--blue); border-color:rgba(37,99,235,.35); color:#fff; }
  .wf2 .btn-outline{ background:transparent; border-color:rgba(37,99,235,.30); color:var(--text); }
  .wf2 .btn:disabled{ opacity:.7; cursor:not-allowed; }
  .wf2 .grid3{ display:grid; grid-template-columns: 1fr; gap:14px; padding:16px; }
  @media(min-width: 1200px){ .wf2 .grid3{ grid-template-columns: 1fr 1fr 1fr; align-items:start; } }
  .wf2 .panel{ background:var(--card); border:1px solid var(--border); border-radius:18px; overflow:hidden; }
  .wf2 .ph{ padding:14px 16px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:10px;}
  .wf2 .ph h3{ margin:0; font-size:14px; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); font-weight:900; }
  .wf2 .pb{ padding:14px 16px; }
  .wf2 .label{ font-size:12px; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); font-weight:900; margin-bottom:6px; display:block; }
  .wf2 .input, .wf2 .select, .wf2 .textarea{
    width:100%; border:1px solid var(--border); background:var(--card); color:var(--text);
    border-radius:14px; padding: 10px 12px; height:44px;
  }
  .wf2 .textarea{ height:auto; min-height: 92px; resize: vertical; }
  .wf2 .select{ padding-right: 40px; background-image: linear-gradient(45deg, transparent 50%, currentColor 50%), linear-gradient(135deg, currentColor 50%, transparent 50%); background-position: calc(100% - 18px) 50%, calc(100% - 13px) 50%; background-size: 6px 6px, 6px 6px; background-repeat:no-repeat; cursor:pointer; }
  .wf2 .input:focus, .wf2 .select:focus, .wf2 .textarea:focus{ outline:none; box-shadow:0 0 0 4px rgba(37,99,235,.12); border-color:rgba(37,99,235,.45); }
  .wf2 .row{ display:grid; grid-template-columns: 1fr; gap:10px; margin-bottom:12px; }
  @media(min-width: 768px){ .wf2 .row2{ grid-template-columns: 1fr 1fr; } }
  .wf2 .cond-card{ border:1px solid var(--border); border-radius:16px; padding:12px; background: rgba(37,99,235,.03); }
  .dark .wf2 .cond-card{ background: rgba(37,99,235,.10); }
  .wf2 .cond-or{ text-align:center; font-weight:900; letter-spacing:.12em; color:var(--muted); margin:10px 0; }
  .wf2 .cond-item{ display:grid; grid-template-columns: 1fr 180px 1fr 44px; gap:10px; align-items:end; margin-bottom:10px; }
  .wf2 .act-item{ border:1px solid var(--border); border-radius:16px; padding:12px; background: rgba(255,255,255,.65); }
  .dark .wf2 .act-item{ background: rgba(255,255,255,.03); }
  .wf2 .act-head{ display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:10px; }
  .wf2 .step-pill{ display:inline-flex; align-items:center; gap:8px; border-radius:999px; padding:6px 10px; background:rgba(37,99,235,.10); color:var(--blue); font-weight:900; font-size:12px; }
  .wf2 .mini{ width:44px; height:44px; display:inline-flex; align-items:center; justify-content:center; border-radius:14px; border:1px solid var(--border); background:transparent; color:var(--text); }
  .wf2 .timeline{ position:relative; }
  .wf2 .timeline:before{
    content:""; position:absolute; left: 18px; top: 0; bottom: 0;
    width:2px; background: rgba(37,99,235,.18);
  }
  .wf2 .stickybar{
    position: sticky; bottom: 0; z-index: 30;
    background: rgba(255,255,255,.85);
    backdrop-filter: blur(10px);
    border-top: 1px solid var(--border);
    padding: 12px 16px;
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
  }
  .dark .wf2 .stickybar{ background: rgba(10,16,30,.75); }
  .wf2 .info{
    border:1px dashed rgba(37,99,235,.35);
    background: rgba(37,99,235,.06);
    border-radius: 14px;
    padding: 10px 12px;
    color: var(--muted);
    font-size: 12px;
  }
  .wf2 .err{ display:none; border:1px solid rgba(239,68,68,.25); background: rgba(239,68,68,.08); color:#b91c1c; border-radius:14px; padding:10px 12px; white-space:pre-wrap; }
  .dark .wf2 .err{ color:#fecaca; }
</style>

<div class="main-content app-content wf2">
  <div class="container-fluid mt-4">
    <div class="shell">
      <div class="topbar">
        <div>
          <div class="muted text-sm font-semibold">Workflows</div>
          <div class="title">Create Workflow</div>
          <div class="muted text-sm">Conditions first → then Actions.</div>
        </div>
        <div class="flex items-center gap-2 flex-wrap justify-end">
          <!-- <a class="btn" href="index.php"><i class="ri-arrow-left-line me-1"></i> Back</a> -->
          <!-- <button class="btn" type="button" onclick="resetAll()">Reset</button> -->
          <button class="btn btn-blue" id="btnSave" type="button" onclick="saveWorkflow()"><i class="ri-save-3-line me-1"></i> Save</button>
        </div>
      </div>

      <div class="grid3">
        <!-- PANEL 1: Details -->
        <div class="panel">
          <div class="ph"><h3>Workflow Details</h3></div>
          <div class="pb">
            <div class="row row2">
              <div>
                <label class="label">Workflow Name</label>
                <input id="workflow_name" class="input" placeholder="Estimated Quote Follow-up" />
              </div>
              <div>
                <label class="label">Module</label>
                <select id="module_name" class="select" onchange="onModuleChange()">
                  <option value="">Select module</option>
                  <option value="Leads">Leads</option>
                  <option value="AOS_Quotes">Quotes</option>
                  <option value="Contacts">Contacts</option>
                </select>
              </div>
            </div>

            <div class="row row2">
              <div>
                <label class="label">Status</label>
                <select id="status" class="select">
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
              <div></div>
            </div>

            <div class="row">
              <label class="label">Description</label>
              <textarea id="description" class="textarea" placeholder="Optional…"></textarea>
            </div>

            <div class="err" id="errBox"></div>
          </div>
        </div>

        <!-- PANEL 2: Condition -->
        <div class="panel">
          <div class="ph">
            <h3>Condition</h3>
          </div>
          <div class="pb">
            <div class="row">
              <label class="label">When should this run?</label>
              <select id="condition_type" class="select" onchange="onConditionTypeChange()">
                <option value="on_create">On Create</option>
                <option value="status_equals">Status =</option>
                <option value="event_date">Event Date</option>
              </select>
              <div class="muted text-sm mt-2">This is the only condition (simple mode).</div>
            </div>

            <div id="conditionConfig"></div>
          </div>
        </div>

        <!-- PANEL 3: Email Template -->
        <div class="panel">
          <div class="ph">
            <h3>Email Template</h3>
          </div>
          <div class="pb">
            <div class="row">
              <label class="label">Template</label>
              <select id="email_template_id" class="select">
                <option value="">Loading templates…</option>
              </select>
              <div class="muted text-sm mt-2">This template will be sent when the condition matches.</div>
            </div>
          </div>
        </div>
      </div>

      <div class="stickybar">
        <div class="info">
          This workflow is created via <span class="mono">action=save_workflow</span> (SuiteCRM CustomEntryPoint).
        </div>
        <div class="flex items-center gap-2">
          <!-- <a class="btn" href="index.php"><i class="ri-arrow-left-line me-1"></i> Back</a> -->
          <!-- <button class="btn" type="button" onclick="resetAll()">Reset</button> -->
          <button class="btn btn-blue" id="btnSave2" type="button" onclick="saveWorkflow()"><i class="ri-save-3-line me-1"></i> Save Changes</button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script>
  const SUITE_ENTRYPOINT = "https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint";
  const SESSION_USER_ID  = "<?php echo $_SESSION['user']['id'] ?? ''; ?>";
  let emailTemplates = [];

  function setErr(msg){
    const box = document.getElementById("errBox");
    if(!msg){ box.style.display="none"; box.textContent=""; return; }
    box.style.display="block"; box.textContent = msg;
  }

  async function postForm(url, dataObj){
    const body = new URLSearchParams();
    Object.keys(dataObj).forEach(k => body.append(k, dataObj[k] ?? ""));
    return fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
      body
    });
  }

  function addConditionGroup(){
    condGroupCount++;
    const host = document.getElementById("conditionGroups");
    const div = document.createElement("div");
    div.dataset.group = String(condGroupCount);
    div.innerHTML = `
      <div class="cond-or">OR</div>
      <div class="cond-card">
        <div class="flex items-center justify-between gap-2 mb-2">
          <div class="mono text-sm muted">Group ${condGroupCount}</div>
          <button class="mini" type="button" title="Remove group" onclick="removeConditionGroup(${condGroupCount})"><i class="ri-close-line"></i></button>
        </div>
        <div data-group-rows="${condGroupCount}"></div>
        <button class="btn btn-outline w-full mt-2" type="button" onclick="addConditionRow(${condGroupCount})"><i class="ri-add-line me-1"></i> Add Condition</button>
      </div>
    `;
    host.appendChild(div);
    addConditionRow(condGroupCount);
  }

  function removeConditionGroup(group){
    const el = document.querySelector(`[data-group="${group}"]`);
    if(el) el.remove();
  }

  function ensureBaseGroup(){
    const host = document.getElementById("conditionGroups");
    if(host.children.length) return;
    host.innerHTML = `
      <div class="cond-card">
        <div class="flex items-center justify-between gap-2 mb-2">
          <div class="mono text-sm muted">Group 1</div>
        </div>
        <div data-group-rows="1"></div>
        <button class="btn btn-outline w-full mt-2" type="button" onclick="addConditionRow(1)"><i class="ri-add-line me-1"></i> Add Condition</button>
      </div>
    `;
    addConditionRow(1);
  }

  function addConditionRow(group){
    ensureBaseGroup();
    const rowsHost = document.querySelector(`[data-group-rows="${group}"]`);
    const row = document.createElement("div");
    row.className = "cond-item";
    row.innerHTML = `
      <div>
        <label class="label">Field</label>
        <select class="select" data-cond-field>${fieldOptionsHtml("")}</select>
      </div>
      <div>
        <label class="label">Operator</label>
        <select class="select" data-cond-op>${operatorOptionsHtml()}</select>
      </div>
      <div>
        <label class="label">Value</label>
        <input class="input mono" data-cond-val placeholder="value" />
      </div>
      <div>
        <button class="mini" type="button" title="Remove" onclick="this.closest('.cond-item').remove()"><i class="ri-close-line"></i></button>
      </div>
    `;
    rowsHost.appendChild(row);
  }

  function redrawConditions(){
    // refresh field dropdowns when module changes
    document.querySelectorAll("[data-cond-field]").forEach(sel => {
      const current = sel.value;
      sel.innerHTML = fieldOptionsHtml(current);
      if(current) sel.value = current;
    });
    document.querySelectorAll("#trigger_field").forEach(sel => {
      const current = sel.value;
      sel.innerHTML = fieldOptionsHtml(current);
      if(current) sel.value = current;
    });
  }

  async function loadEmailTemplates(){
    const dd = document.getElementById("email_template_id");
    dd.innerHTML = `<option value="">Loading templates…</option>`;
    try{
      const res = await postForm(SUITE_ENTRYPOINT, { action:"fetch_email_templates", id: SESSION_USER_ID });
      const text = await res.text();
      let data;
      try { data = JSON.parse(text); } catch(e){ data = []; }
      emailTemplates = Array.isArray(data) ? data : [];
      dd.innerHTML = `<option value="">Select template</option>`;
      emailTemplates.forEach(t => {
        const opt = document.createElement("option");
        opt.value = t.id;
        opt.textContent = t.name;
        dd.appendChild(opt);
      });
    }catch(e){
      dd.innerHTML = `<option value="">Failed to load</option>`;
    }
  }

  function onConditionTypeChange(){
    const type = document.getElementById("condition_type").value;
    const host = document.getElementById("conditionConfig");
    if(type === "status_equals"){
      host.innerHTML = `
        <div class="row">
          <label class="label">Status value</label>
          <input id="status_value" class="input mono" placeholder="e.g. Formal" />
        </div>
      `;
      return;
    }
    if(type === "event_date"){
      host.innerHTML = `
        <div class="row">
          <label class="label">Event date field</label>
          <input id="event_date_field" class="input mono" value="event_date_c" />
          <div class="muted text-sm mt-2">Execution for event_date is typically handled by cron (not immediate).</div>
        </div>
      `;
      return;
    }
    host.innerHTML = `<div class="muted text-sm">Runs immediately when a record is created.</div>`;
  }

  function resetAll(){
    setErr("");
    document.getElementById("workflow_name").value = "";
    document.getElementById("module_name").value = "";
    document.getElementById("status").value = "Active";
    document.getElementById("description").value = "";
    document.getElementById("condition_type").value = "on_create";
    document.getElementById("email_template_id").value = "";
    onConditionTypeChange();
  }

  async function saveWorkflow(){
    setErr("");
    const btn = document.getElementById("btnSave");
    const btn2 = document.getElementById("btnSave2");
    btn.disabled = true; btn2.disabled = true;

    try{
      const payload = {
        name: document.getElementById("workflow_name").value.trim(),
        module: document.getElementById("module_name").value,
        status: document.getElementById("status").value,
        description: document.getElementById("description").value.trim(),
        condition_type: document.getElementById("condition_type").value,
        status_value: document.getElementById("status_value")?.value?.trim() || "",
        event_date_field: document.getElementById("event_date_field")?.value?.trim() || "",
        email_template_id: document.getElementById("email_template_id").value,
        user_id: SESSION_USER_ID
      };

      if(!payload.name) return setErr("Workflow name is required.");
      if(!payload.module) return setErr("Module is required.");
      if(!payload.email_template_id) return setErr("Email template is required.");
      if(payload.condition_type === "status_equals" && !payload.status_value) return setErr("Status value is required.");

      const res = await postForm(SUITE_ENTRYPOINT, { action:"save_workflow", ...payload });
      const text = await res.text();
      let json;
      try { json = JSON.parse(text); } catch(e){ return setErr("Invalid server response."); }
      if(!json.success) return setErr(json.message || "Save failed.");
      window.location.href = `edit_workflow.php?id=${encodeURIComponent(json.id)}`;
    }catch(e){
      setErr(e?.message || String(e));
    }finally{
      btn.disabled = false; btn2.disabled = false;
    }
  }

  // init
  loadEmailTemplates();
  onConditionTypeChange();
</script>

