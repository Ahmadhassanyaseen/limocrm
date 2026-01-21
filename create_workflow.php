<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Workflow</title>

 
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
