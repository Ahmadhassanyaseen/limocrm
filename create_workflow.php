<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Workflow</title>

  <style>
    :root{
      --bg:#f6f8fb; --card:#ffffff; --text:#1f2937; --muted:#6b7280; --line:#e5e7eb;
      --primary:#2563eb; --primary-2:#1d4ed8; --danger:#ef4444;
      --shadow: 0 10px 25px rgba(0,0,0,.06);
      --radius:14px;
      --font: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", "Liberation Sans", sans-serif;
    }
    *{box-sizing:border-box}
    body{ margin:0; font-family:var(--font); color:var(--text); background:var(--bg); }
    .page{ max-width:1100px; margin:24px auto; padding:0 16px 40px; }

    .topbar{ display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:18px; }
    .title{ font-size:20px; font-weight:700; letter-spacing:.2px; }
    .actions{ display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }

    .btn{
      border:1px solid var(--line); background:#fff; padding:10px 14px; border-radius:12px; cursor:pointer;
      font-weight:600; font-size:13px; transition:.15s; display:inline-flex; align-items:center; gap:8px;
    }
    .btn:hover{ transform:translateY(-1px); box-shadow:0 8px 18px rgba(0,0,0,.07) }
    .btn-primary{ background:var(--primary); border-color:var(--primary); color:#fff; }
    .btn-primary:hover{ background:var(--primary-2) }
    .btn-danger{ background:#fff; border-color:#fecaca; color:var(--danger); }

    .card{
      background:var(--card); border:1px solid var(--line); border-radius:var(--radius);
      box-shadow:var(--shadow); overflow:hidden; margin-bottom:14px;
    }
    .card-head{
      background:linear-gradient(180deg, #0f172a 0%, #111827 100%);
      color:#fff; padding:12px 16px; font-weight:800; font-size:12px; letter-spacing:.12em;
      display:flex; align-items:center; justify-content:space-between; text-transform:uppercase;
    }
    .card-body{ padding:16px; }

    .grid{ display:grid; grid-template-columns: 1fr 1fr; gap:14px 18px; }
    @media (max-width:860px){
      .grid{grid-template-columns:1fr}
      .topbar{flex-direction:column; align-items:flex-start}
      .actions{justify-content:flex-start}
    }

    .field{ display:flex; flex-direction:column; gap:6px; }
    label{ font-size:12px; font-weight:800; color:var(--muted); letter-spacing:.02em; }

    input[type="text"], select, textarea{
      border:1px solid var(--line); border-radius:12px; padding:11px 12px; font-size:14px;
      background:#fff; outline:none; transition:.15s;
    }
    input[type="text"]:focus, select:focus, textarea:focus{
      border-color:rgba(37,99,235,.55);
      box-shadow:0 0 0 4px rgba(37,99,235,.12);
    }
    textarea{ min-height:110px; resize:vertical; }

    .hint{ font-size:12px; color:var(--muted); margin-top:4px; }
    .info{
      padding:10px 12px; border:1px dashed rgba(37,99,235,.35); background:rgba(37,99,235,.06);
      border-radius:12px; color:#1e40af; font-size:13px; margin-top:8px;
    }
    .errorbox{
      display:none; margin-top:12px; padding:10px 12px; border:1px solid rgba(239,68,68,.25);
      background:rgba(239,68,68,.08); border-radius:12px; color:#991b1b; font-size:13px; white-space:pre-wrap;
    }
    .successbox{
      display:none; margin-top:12px; padding:10px 12px; border:1px solid rgba(16,185,129,.25);
      background:rgba(16,185,129,.08); border-radius:12px; color:#065f46; font-size:13px; white-space:pre-wrap;
    }
  </style>
</head>

<body>
  <div class="page">

    <div class="topbar">
      <div>
        <div class="title">Create Workflow</div>
        <div class="hint">Module → Email Template mapping save hoga (SuiteCRM CustomEntryPoint).</div>
      </div>

      <div class="actions">
        <a class="btn" href="workflows.php">← Back to Workflows</a>
        <button class="btn btn-primary" id="btnSave" onclick="saveWorkflow()">Save</button>
        <button class="btn btn-danger" onclick="resetForm()">Cancel</button>
      </div>
    </div>

    <div class="card" id="card-basic">
      <div class="card-head"><span>Basic</span></div>

      <div class="card-body">
        <div class="grid">

          <div class="field">
            <label for="name">Workflow Name</label>
            <input id="name" type="text" value="" placeholder="Enter workflow name" />
            <div class="hint">Example: Leads Auto Email / Quote Follow-up</div>
          </div>

          <div class="field">
            <label for="module">Module</label>
            <select id="module">
              <option value="">Select Module</option>
              <option value="Leads">Leads</option>
              <option value="AOS_Quotes">Quotes</option>
            </select>
            <div class="hint">Workflow kis module ke sath link hoga.</div>
          </div>

          <div class="field">
            <label for="email_template_id">Email Template</label>
            <select id="email_template_id">
              <option value="">Loading templates...</option>
            </select>
            <div class="hint">Templates SuiteCRM se load honge.</div>
          </div>

          <div class="field">
            <label for="status">Status</label>
            <select id="status">
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
              <option value="Draft">Draft</option>
            </select>
          </div>

          <div class="field" style="grid-column:1/-1">
            <label for="description">Description</label>
            <textarea id="description" placeholder="Optional description..."></textarea>
          </div>

          <div class="field" style="grid-column:1/-1">
            <div class="info">
              Save request <b>POST form-data</b> me ja rahi hai: <code>action=save_workflow</code>
            </div>
            <div class="errorbox" id="errorBox"></div>
            <div class="successbox" id="successBox"></div>
          </div>

        </div>
      </div>
    </div>

  </div>

  <script>
    const SUITE_ENTRYPOINT = "https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint";
    const SESSION_USER_ID  = "<?php echo $_SESSION['user']['id'] ?? ''; ?>";

    function showError(msg){
      const e = document.getElementById("errorBox");
      const s = document.getElementById("successBox");
      s.style.display = "none";
      e.style.display = "block";
      e.textContent = msg;
    }
    function showSuccess(msg){
      const e = document.getElementById("errorBox");
      const s = document.getElementById("successBox");
      e.style.display = "none";
      s.style.display = "block";
      s.textContent = msg;
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

    async function loadEmailTemplates(){
      const dd = document.getElementById("email_template_id");
      dd.innerHTML = `<option value="">Loading templates...</option>`;

      try{
        const res = await postForm(SUITE_ENTRYPOINT, {
          action: "fetch_email_templates",
          id: SESSION_USER_ID
        });

        const text = await res.text();
        let data;
        try { data = JSON.parse(text); }
        catch(e){
          dd.innerHTML = `<option value="">Failed to load templates</option>`;
          return showError("Templates response NOT JSON.\nHTTP: "+res.status+"\n\n"+text.slice(0,600));
        }

        dd.innerHTML = `<option value="">Select Email Template</option>`;
        if(Array.isArray(data) && data.length){
          data.forEach(t => {
            const opt = document.createElement("option");
            opt.value = t.id;
            opt.textContent = t.name;
            dd.appendChild(opt);
          });
        } else {
          dd.innerHTML = `<option value="">No templates found</option>`;
        }
      }catch(e){
        dd.innerHTML = `<option value="">Failed to load templates</option>`;
        showError("Template load error:\n" + (e?.message || e));
      }
    }

    function resetForm(){
      document.getElementById("name").value = "";
      document.getElementById("module").value = "";
      document.getElementById("email_template_id").value = "";
      document.getElementById("status").value = "Active";
      document.getElementById("description").value = "";
      document.getElementById("errorBox").style.display.display = "none";
      document.getElementById("successBox").style.display = "none";
      document.getElementById("errorBox").style.display = "none";
    }

    async function saveWorkflow(){
      const btn = document.getElementById("btnSave");
      btn.disabled = true;

      const payload = {
        name: document.getElementById("name").value.trim(),
        module: document.getElementById("module").value,
        email_template_id: document.getElementById("email_template_id").value,
        status: document.getElementById("status").value,
        description: document.getElementById("description").value.trim(),
        user_id: SESSION_USER_ID
      };

      if(!payload.name){ btn.disabled=false; return showError("Workflow name is required."); }
      if(!payload.module){ btn.disabled=false; return showError("Module is required."); }
      if(!payload.email_template_id){ btn.disabled=false; return showError("Email template is required."); }

      try{
        const res = await postForm(SUITE_ENTRYPOINT, {
          action: "save_workflow",
          name: payload.name,
          module: payload.module,
          email_template_id: payload.email_template_id,
          status: payload.status,
          description: payload.description,
          user_id: payload.user_id
        });

        const text = await res.text();
        let out;
        try { out = JSON.parse(text); }
        catch(e){
          btn.disabled=false;
          return showError("Save response NOT JSON.\nHTTP: "+res.status+"\n\n"+text.slice(0,800));
        }

        if(out && out.success){
          showSuccess("Workflow saved.\nID: " + (out.id || "(no id)"));
          setTimeout(() => window.location.href = "workflows.php", 600);
        } else {
          showError("Save failed:\n" + JSON.stringify(out, null, 2));
        }
      }catch(e){
        showError("Network/Server error:\n" + (e?.message || e));
      }finally{
        btn.disabled = false;
      }
    }

    loadEmailTemplates();
  </script>
</body>
</html>

<?php include_once "components/layout/footer.php"; ?>
