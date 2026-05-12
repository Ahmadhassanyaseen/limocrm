<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>

<style>
  .cw{
    --cw-primary:#6366f1;--cw-primary-light:rgba(99,102,241,.10);
    --cw-surface:#ffffff;--cw-surface2:#f8fafc;--cw-border:rgba(15,23,42,.08);
    --cw-text:#0f172a;--cw-muted:rgba(15,23,42,.55);--cw-green:#10b981;--cw-red:#ef4444;
    font-family:Inter,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
  }
  .dark .cw{
    --cw-surface:rgba(255,255,255,.03);--cw-surface2:#0b1220;--cw-border:rgba(255,255,255,.08);
    --cw-text:rgba(255,255,255,.92);--cw-muted:rgba(255,255,255,.50);
  }

  .cw-wrap{max-width:900px;margin:0 auto;padding:24px 16px 60px;}

  /* header */
  .cw-header{
    position:sticky;top:60px;z-index:40;
    background:var(--cw-surface);border:1px solid var(--cw-border);border-radius:16px;
    padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:14px;
    margin-bottom:20px;backdrop-filter:blur(12px);
    transition:box-shadow .25s;
  }
  .cw-header.scrolled{box-shadow:0 6px 24px rgba(0,0,0,.06);}
  .cw-header-left{display:flex;align-items:center;gap:14px;}
  .cw-back{
    width:40px;height:40px;border-radius:12px;border:1px solid var(--cw-border);
    background:transparent;color:var(--cw-text);display:inline-flex;align-items:center;justify-content:center;
    cursor:pointer;transition:all .2s;
  }
  .cw-back:hover{background:var(--cw-primary-light);color:var(--cw-primary);border-color:var(--cw-primary);}
  .cw-header h1{margin:0;font-size:20px;font-weight:700;color:var(--cw-text);letter-spacing:-.02em;}
  .cw-header p{margin:2px 0 0;font-size:13px;color:var(--cw-muted);}
  .cw-header-actions{display:flex;gap:8px;flex-shrink:0;}

  /* buttons */
  .cw-btn{
    border-radius:12px;padding:10px 20px;font-weight:600;font-size:14px;
    border:1px solid var(--cw-border);background:transparent;color:var(--cw-text);
    cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:6px;
  }
  .cw-btn:hover{background:var(--cw-primary-light);}
  .cw-btn-primary{background:var(--cw-primary);border-color:var(--cw-primary);color:#fff;}
  .cw-btn-primary:hover{background:#4f46e5;border-color:#4f46e5;}
  .cw-btn:disabled{opacity:.55;cursor:not-allowed;}

  /* panels */
  .cw-grid{display:grid;grid-template-columns:1fr;gap:18px;}
  @media(min-width:900px){.cw-grid{grid-template-columns:1fr 1fr;}}
  .cw-panel{
    background:var(--cw-surface);border:1px solid var(--cw-border);border-radius:16px;overflow:hidden;
  }
  .cw-panel-full{grid-column:1/-1;}
  .cw-panel-head{
    padding:14px 18px;border-bottom:1px solid var(--cw-border);display:flex;align-items:center;gap:10px;
  }
  .cw-panel-head .cw-step{
    width:28px;height:28px;border-radius:8px;background:var(--cw-primary);color:#fff;
    display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;
  }
  .cw-panel-head h3{margin:0;font-size:15px;font-weight:700;color:var(--cw-text);}
  .cw-panel-body{padding:18px;}

  /* form */
  .cw-field{margin-bottom:16px;}
  .cw-field:last-child{margin-bottom:0;}
  .cw-label{display:block;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--cw-muted);margin-bottom:6px;}
  .cw-input,.cw-select,.cw-textarea{
    width:100%;border:1px solid var(--cw-border);background:var(--cw-surface2);color:var(--cw-text);
    border-radius:12px;padding:10px 14px;font-size:14px;height:44px;
    transition:border-color .2s,box-shadow .2s;
  }
  .cw-textarea{height:auto;min-height:80px;resize:vertical;}
  .cw-select{
    appearance:none;-webkit-appearance:none;
    padding-right:40px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m2 4 4 4 4-4'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 14px center;background-size:12px;
    cursor:pointer;
  }
  .cw-input:focus,.cw-select:focus,.cw-textarea:focus{
    outline:none;border-color:var(--cw-primary);box-shadow:0 0 0 4px var(--cw-primary-light);
  }
  .cw-input.err-border,.cw-select.err-border{border-color:var(--cw-red);box-shadow:0 0 0 4px rgba(239,68,68,.1);}
  .cw-row{display:grid;grid-template-columns:1fr;gap:14px;}
  @media(min-width:600px){.cw-row-2{grid-template-columns:1fr 1fr;}}
  @media(min-width:600px){.cw-row-3{grid-template-columns:1fr 1fr 1fr;}}

  /* condition visual */
  .cw-cond-box{
    border:1px solid var(--cw-border);border-radius:14px;padding:16px;
    background:var(--cw-primary-light);margin-top:14px;
  }
  .cw-cond-icon{
    width:44px;height:44px;border-radius:12px;
    background:var(--cw-primary-light);color:var(--cw-primary);
    display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px;
  }
  .cw-cond-label{font-size:13px;font-weight:600;color:var(--cw-text);margin-bottom:12px;}
  .cw-hint{font-size:12px;color:var(--cw-muted);margin-top:8px;line-height:1.5;}

  /* days grid */
  .cw-days-grid{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;}
  .cw-day-btn{
    width:38px;height:34px;border-radius:8px;border:1px solid var(--cw-border);
    background:var(--cw-surface2);color:var(--cw-text);font-size:13px;font-weight:600;
    cursor:pointer;transition:all .15s;display:inline-flex;align-items:center;justify-content:center;
  }
  .cw-day-btn:hover{border-color:var(--cw-primary);color:var(--cw-primary);}
  .cw-day-btn.active{background:var(--cw-primary);border-color:var(--cw-primary);color:#fff;}

  /* direction toggle */
  .cw-dir-group{display:flex;gap:0;border-radius:10px;overflow:hidden;border:1px solid var(--cw-border);}
  .cw-dir-btn{
    flex:1;padding:15px 16px;font-weight:600;font-size:13px;border:none;
    background:var(--cw-surface2);color:var(--cw-muted);cursor:pointer;transition:all .15s;text-align:center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
  }
  .cw-dir-btn.active{background:var(--cw-primary);color:#fff;}

  /* error box */
  .cw-err{
    display:none;border:1px solid rgba(239,68,68,.25);background:rgba(239,68,68,.06);
    color:#b91c1c;border-radius:12px;padding:12px 14px;font-size:13px;margin-top:16px;
    line-height:1.5;
  }
  .dark .cw-err{color:#fecaca;}

  /* status badges in dropdown (custom option display) */
  .cw-status-chip{
    display:inline-flex;align-items:center;gap:6px;padding:2px 10px 2px 8px;
    border-radius:999px;font-size:12px;font-weight:600;
  }
  .cw-status-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0;}
</style>

<div class="main-content app-content cw">
  <div class="cw-wrap">

    <!-- Sticky Header -->
    <div class="cw-header" id="cwHeader">
      <div class="cw-header-left">
        <a href="workflows.php" class="cw-back" title="Back to Workflows">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 9H3m6-6L3 9l6 6"/></svg>
        </a>
        <div>
          <h1>Create Workflow</h1>
          <p>Set conditions and assign an email template</p>
        </div>
      </div>
      <div class="cw-header-actions">
        <a href="workflows.php" class="cw-btn">Cancel</a>
        <button class="cw-btn cw-btn-primary" id="btnSave" onclick="saveWorkflow()">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Save Workflow
        </button>
      </div>
    </div>

    <div class="cw-grid">

      <!-- Panel 1: Details -->
      <div class="cw-panel">
        <div class="cw-panel-head">
          <span class="cw-step">1</span>
          <h3>Workflow Details</h3>
        </div>
        <div class="cw-panel-body">
          <div class="cw-field">
            <label class="cw-label">Workflow Name</label>
            <input id="workflow_name" class="cw-input" placeholder="e.g. Follow-up after quote sent" />
          </div>
          <div class="cw-row cw-row-2">
            <div class="cw-field">
              <label class="cw-label">Module</label>
              <select id="module_name" class="cw-select">
                <option value="Leads">Leads</option>
              </select>
            </div>
            <div class="cw-field">
              <label class="cw-label">Status</label>
              <select id="status" class="cw-select">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="cw-field">
            <label class="cw-label">Description</label>
            <textarea id="description" class="cw-textarea" placeholder="Optional description..."></textarea>
          </div>
        </div>
      </div>

      <!-- Panel 2: Condition -->
      <div class="cw-panel">
        <div class="cw-panel-head">
          <span class="cw-step">2</span>
          <h3>Trigger Condition</h3>
        </div>
        <div class="cw-panel-body">
          <div class="cw-field">
            <label class="cw-label">When should this workflow run?</label>
            <select id="condition_type" class="cw-select" onchange="onConditionTypeChange()">
              <option value="on_create">On Create</option>
              <option value="status_equals">Status Equals</option>
              <option value="event_date">Event Date</option>
            </select>
          </div>
          <div id="conditionConfig"></div>
        </div>
      </div>

      <!-- Panel 3: Email Template (full-width) -->
      <div class="cw-panel cw-panel-full">
        <div class="cw-panel-head">
          <span class="cw-step">3</span>
          <h3>Email Template</h3>
        </div>
        <div class="cw-panel-body">
          <div class="cw-row cw-row-2">
            <div class="cw-field">
              <label class="cw-label">Select Email Template</label>
              <select id="email_template_id" class="cw-select">
                <option value="">Loading templates...</option>
              </select>
              <div class="cw-hint">This template will be sent automatically when the condition matches.</div>
            </div>
            <div class="cw-field" id="templatePreviewWrap" style="display:none;">
              <label class="cw-label">Preview</label>
              <div id="templatePreview" style="border:1px solid var(--cw-border);border-radius:12px;padding:14px;min-height:44px;background:var(--cw-surface2);font-size:13px;color:var(--cw-muted);"></div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Error box -->
    <div class="cw-err" id="errBox"></div>

  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script>
const SUITE_ENTRYPOINT = "https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint";
const SESSION_USER_ID  = "<?php echo $_SESSION['user']['id'] ?? ''; ?>";
let emailTemplates = [];
let selectedDays = 1;
let selectedDirection = 'before';

/* ── sticky header shadow ── */
window.addEventListener('scroll', () => {
  document.getElementById('cwHeader')?.classList.toggle('scrolled', window.scrollY > 10);
});

/* ── error display ── */
function setErr(msg){
  const box = document.getElementById("errBox");
  if(!msg){ box.style.display="none"; box.textContent=""; return; }
  box.style.display="block"; box.textContent = msg;
  box.scrollIntoView({behavior:'smooth',block:'center'});
}

/* ── post helper ── */
async function postForm(url, dataObj){
  const body = new URLSearchParams();
  Object.keys(dataObj).forEach(k => body.append(k, dataObj[k] ?? ""));
  return fetch(url, {
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"},
    body
  });
}

/* ── load templates ── */
async function loadEmailTemplates(){
  const dd = document.getElementById("email_template_id");
  dd.innerHTML = '<option value="">Loading templates...</option>';
  try{
    const res = await postForm(SUITE_ENTRYPOINT, {action:"fetch_email_templates", id:SESSION_USER_ID});
    const text = await res.text();
    let data;
    try{ data = JSON.parse(text); }catch(e){ data = []; }
    emailTemplates = Array.isArray(data) ? data : [];
    dd.innerHTML = '<option value="">Select a template</option>';
    emailTemplates.forEach(t => {
      const opt = document.createElement("option");
      opt.value = t.id;
      opt.textContent = t.name;
      dd.appendChild(opt);
    });
  }catch(e){
    dd.innerHTML = '<option value="">Failed to load templates</option>';
  }
}

/* ── template preview ── */
document.addEventListener('change', function(e){
  if(e.target.id === 'email_template_id'){
    const tid = e.target.value;
    const wrap = document.getElementById('templatePreviewWrap');
    const prev = document.getElementById('templatePreview');
    if(!tid){ wrap.style.display='none'; return; }
    const t = emailTemplates.find(x=>x.id===tid);
    if(t){
      wrap.style.display='';
      prev.innerHTML = '<strong style="color:var(--cw-text);">'+escHtml(t.name)+'</strong>'
        + (t.description ? '<br><span>'+escHtml(t.description.substring(0,120))+'</span>' : '');
    }
  }
});

function escHtml(s){
  const d=document.createElement('div'); d.textContent=s; return d.innerHTML;
}

/* ── condition type change ── */
function onConditionTypeChange(){
  const type = document.getElementById("condition_type").value;
  const host = document.getElementById("conditionConfig");

  if(type === "status_equals"){
    host.innerHTML = `
      <div class="cw-cond-box"> 
        <div class="cw-cond-icon">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="cw-cond-label">Run when lead status matches:</div>
        <div class="cw-field" style="margin-bottom:0;">
          <select id="status_value" class="cw-select">
              <option value="">Select status...</option>
            <option value="Formal">Formal</option>
            <option value="Converted">Converted</option>
            <option value="Agreement Sent">Agreement Sent</option>
            <option value="Dead">Dead</option>
            <option value="New">New</option>
            <option value="Assigned">Assigned</option>
          </select>
        </div>
        <div class="cw-hint">The workflow will trigger when a lead's status changes to the selected value.</div>
      </div>`;
    return;
  }

  if(type === "event_date"){
    selectedDirection = 'before';
    selectedDays = 1;
    let daysHtml = '';
    for(let i=1;i<=30;i++){
      daysHtml += '<button type="button" class="cw-day-btn'+(i===1?' active':'')+'" data-day="'+i+'" onclick="selectDay(this,'+i+')">'+i+'</button>';
    }
    host.innerHTML = `
      <div class="cw-cond-box">
        <div class="cw-cond-icon">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="cw-cond-label">Run relative to the event date:</div>
        <input type="hidden" id="event_date_field" value="event_date_c" />
        <div class="cw-field">
          <label class="cw-label">Direction</label>
          <div class="cw-dir-group">
            <button type="button" class="cw-dir-btn active" data-dir="before" onclick="selectDirection(this,'before')">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;"><polyline points="15 18 9 12 15 6"/></svg>
              Before Event
            </button>
            <button type="button" class="cw-dir-btn" data-dir="after" onclick="selectDirection(this,'after')">
              After Event
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:4px;"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>
        </div>
        <div class="cw-field" style="margin-bottom:0;">
          <label class="cw-label">Number of Days</label>
          <div class="cw-days-grid">${daysHtml}</div>
        </div>
        <div class="cw-hint">Execution is handled by a scheduled cron job. The email will be sent <strong><span id="dirLabel">before</span></strong> the event date by <strong><span id="daysLabel">1</span></strong> day(s).</div>
      </div>`;
    return;
  }

  host.innerHTML = `
    <div class="cw-cond-box">
      <div class="cw-cond-icon">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div class="cw-cond-label">Triggered immediately on record creation</div>
      <div class="cw-hint">The workflow will fire as soon as a new lead is created in the system.</div>
    </div>`;
}

function selectDay(btn, day){
  selectedDays = day;
  document.querySelectorAll('.cw-day-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const lbl = document.getElementById('daysLabel');
  if(lbl) lbl.textContent = day;
}

function selectDirection(btn, dir){
  selectedDirection = dir;
  document.querySelectorAll('.cw-dir-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const lbl = document.getElementById('dirLabel');
  if(lbl) lbl.textContent = dir;
}

/* ── reset ── */
function resetAll(){
  setErr("");
  document.getElementById("workflow_name").value = "";
  document.getElementById("module_name").value = "Leads";
  document.getElementById("status").value = "Active";
  document.getElementById("description").value = "";
  document.getElementById("condition_type").value = "on_create";
  document.getElementById("email_template_id").value = "";
  document.getElementById("templatePreviewWrap").style.display = "none";
  selectedDays = 1; selectedDirection = 'before';
  onConditionTypeChange();
}

/* ── save ── */
async function saveWorkflow(){
  setErr("");
  const btn  = document.getElementById("btnSave");
  btn.disabled = true;

  try{
    const condType = document.getElementById("condition_type").value;
    const payload = {
      name: document.getElementById("workflow_name").value.trim(),
      module: document.getElementById("module_name").value,
      status: document.getElementById("status").value,
      description: document.getElementById("description").value.trim(),
      condition_type: condType,
      status_value: "",
      event_date_field: "",
      event_date_direction: "",
      event_date_days: "",
      email_template_id: document.getElementById("email_template_id").value,
      user_id: SESSION_USER_ID
    };

    if(condType === "status_equals"){
      payload.status_value = document.getElementById("status_value")?.value?.trim() || "";
    }
    if(condType === "event_date"){
      payload.event_date_field = document.getElementById("event_date_field")?.value?.trim() || "event_date_c";
      payload.event_date_direction = selectedDirection;
      payload.event_date_days = String(selectedDays);
    }

    if(!payload.name){ highlightField('workflow_name'); return setErr("Workflow name is required."); }
    if(!payload.module){ return setErr("Module is required."); }
    if(!payload.email_template_id){ highlightField('email_template_id'); return setErr("Please select an email template."); }
    if(condType === "status_equals" && !payload.status_value){
      highlightField('status_value');
      return setErr("Please select a status value for the condition.");
    }

    const res = await postForm(SUITE_ENTRYPOINT, {action:"save_workflow", ...payload});
    const text = await res.text();
    let json;
    try{ json = JSON.parse(text); }catch(e){ return setErr("Invalid server response."); }
    if(!json.success) return setErr(json.message || "Save failed.");

    if(typeof Swal !== 'undefined'){
      await Swal.fire({icon:'success',title:'Workflow Created',text:'Your workflow has been saved successfully.',timer:2000,showConfirmButton:false});
    }
    window.location.href = 'workflows.php';
  }catch(e){
    setErr(e?.message || String(e));
  }finally{
    btn.disabled = false;
  }
}

function highlightField(id){
  const el = document.getElementById(id);
  if(!el) return;
  el.classList.add('err-border');
  el.focus();
  setTimeout(()=> el.classList.remove('err-border'), 3000);
}

/* ── init ── */
loadEmailTemplates();
onConditionTypeChange();
</script>
