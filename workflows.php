<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<div class="main-content app-content">
  <div class="container-fluid">

    <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2 py-4">
      <div>
        <h1 class="page-title font-bold text-2xl mb-1 text-gray-800 dark:text-gray-100">Workflows</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Module → Email Template mappings</p>
      </div>

      <div class="flex items-center gap-3">
        <button type="button" class="ti-btn ti-btn-sm ti-btn-light !border-gray-200 dark:!border-white/10" onclick="loadWorkflows()">
          <i class="ri-refresh-line"></i>
        </button>

        <a href="create_workflow.php" class="ti-btn ti-btn-md bg-primary text-white font-medium shadow-sm hover:shadow-md transition-all btn-wave">
          <i class="ri-add-circle-line me-1 align-middle text-lg"></i> New Workflow
        </a>
      </div>
    </div>

    <div class="box custom-box !bg-transparent border-0 shadow-none mb-4">
      <div class="box-body p-0">
        <div class="grid grid-cols-12 gap-4">
          <div class="xl:col-span-8 col-span-12">
            <div class="relative group">
              <input type="text" id="workflow-search"
                class="form-control ps-10 h-[48px] !bg-white dark:!bg-black/20 border-gray-200 dark:border-white/10 rounded-xl focus:ring-2 focus:ring-primary/20 transition-all"
                placeholder="Search workflow / module / template...">
              <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none transition-colors group-focus-within:text-primary text-gray-400">
                <i class="ri-search-2-line text-xl"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-12 gap-x-6">
      <div class="xl:col-span-12 col-span-12">
        <div class="box custom-box overflow-hidden rounded-2xl border border-gray-200 dark:border-white/10 shadow-sm">
          <div class="box-body p-0">
            <div class="table-responsive">
              <table class="table whitespace-nowrap min-w-full">
                <thead>
                  <tr class="bg-gray-50/50 dark:bg-black/20 border-b border-gray-200 dark:border-white/10">
                    <th class="px-6 py-4 text-start text-xs font-bold uppercase tracking-wider text-gray-500">Workflow</th>
                    <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">Module</th>
                    <th class="px-6 py-4 text-start text-xs font-bold uppercase tracking-wider text-gray-500">Email Template</th>
                    <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-4 text-start text-xs font-bold uppercase tracking-wider text-gray-500">Created On</th>
                    <th class="px-6 py-4 text-end text-xs font-bold uppercase tracking-wider text-gray-500">Actions</th>
                  </tr>
                </thead>

                <tbody id="workflow-list" class="divide-y divide-gray-100 dark:divide-white/5">
                  <tr>
                    <td colspan="6" class="text-center py-20">
                      <div class="flex flex-col items-center">
                        <div class="ti-spinner w-10 h-10 border-[3px] border-t-primary border-gray-200 dark:border-white/10 rounded-full animate-spin mb-4"></div>
                        <span class="text-gray-500 font-medium tracking-wide">Fetching workflows...</span>
                      </div>
                    </td>
                  </tr>
                </tbody>

              </table>
            </div>
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

  let allWorkflows = [];

  async function postForm(url, dataObj){
    const body = new URLSearchParams();
    Object.keys(dataObj).forEach(k => body.append(k, dataObj[k] ?? ""));
    return fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
      body
    });
  }

  function statusBadge(status){
    const map = {
      "Active":  "bg-success/10 text-success dark:bg-success/10 dark:text-success",
      "Inactive":"bg-danger/10 text-danger dark:bg-danger/10 dark:text-danger",
      "Draft":   "bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400"
    };
    return map[status] || "bg-gray-100 text-gray-700";
  }

  async function loadWorkflows(){

document.getElementById("workflow-list").innerHTML = `
  <tr><td colspan="6" class="text-center py-20">
    <div class="flex flex-col items-center">
      <div class="ti-spinner w-8 h-8 border-[3px] border-t-primary border-gray-200 rounded-full animate-spin mb-4"></div>
      <span class="text-gray-500">Reloading workflows...</span>
    </div>
  </td></tr>
`;

try{

  const res  = await postForm(SUITE_ENTRYPOINT, {
    action:"fetch_workflows",
    id: SESSION_USER_ID
  });

  const text = await res.text();

  let json;
  try{
    json = JSON.parse(text);
  }catch(e){
    console.error("Invalid JSON:", text);
    document.getElementById("workflow-list").innerHTML =
      `<tr><td colspan="6" class="text-center text-danger py-10">Invalid Server Response</td></tr>`;
    return;
  }

  // ✅ SUPPORT BOTH TYPES OF RESPONSES
  let rows = [];

  if(Array.isArray(json)){
    rows = json;
  }
  else if(json.rows && Array.isArray(json.rows)){
    rows = json.rows;
  }

  allWorkflows = rows;
  renderWorkflows(allWorkflows);

}catch(err){

  console.error(err);
  document.getElementById("workflow-list").innerHTML =
    `<tr><td colspan="6" class="text-center text-danger py-10">Server Error</td></tr>`;

}
}

  function renderWorkflows(list){
    const tbody = document.getElementById("workflow-list");

    if(!list.length){
      tbody.innerHTML = `
        <tr>
          <td colspan="6" class="text-center py-24">
            <div class="bg-gray-50 dark:bg-black/10 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
              <i class="ri-git-merge-line text-4xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">No Workflows Found</h3>
            <p class="text-gray-500 mb-6">Create your first workflow mapping for Leads / Quotes.</p>
            <a href="create_workflow.php" class="ti-btn ti-btn-soft-primary">
              <i class="ri-add-line me-1"></i> Create Workflow
            </a>
          </td>
        </tr>
      `;
      return;
    }

    let html = "";
    list.forEach(w => {
      const date = w.date_entered ? new Date(w.date_entered).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}) : '—';

      html += `
        <tr class="group hover:bg-gray-50/80 dark:hover:bg-white/5 transition-all duration-300">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center transition-transform group-hover:scale-110">
                <i class="ri-flow-chart text-xl"></i>
              </div>
              <div>
                <div class="font-bold text-gray-800 dark:text-gray-100 text-[15px] mb-0.5 line-clamp-1">${w.workflow_name || '—'}</div>
                <div class="text-[12px] text-gray-500 line-clamp-1 opacity-70">${w.description || 'No description'}</div>
              </div>
            </div>
          </td>

          <td class="px-6 py-4 text-center">
            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wider bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
              ${w.module_name || '—'}
            </span>
          </td>

          <td class="px-6 py-4">
            <div class="text-sm text-gray-700 dark:text-gray-300 font-medium max-w-[320px] truncate">
            ${w.template_name || w.email_template_id || '—'}
            </div>
          </td>

          <td class="px-6 py-4 text-center">
            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold ${statusBadge(w.status)}">
              ${w.status || '—'}
            </span>
          </td>

          <td class="px-6 py-4">
            <div class="text-xs text-gray-500 font-medium">
              <i class="ri-calendar-line me-1 align-middle"></i> ${date}
            </div>
          </td>

          <td class="px-6 py-4 text-end">
            <div class="flex justify-end gap-2 outline-none">
              <button class="w-9 h-9 rounded-xl flex items-center justify-center bg-danger/10 text-danger hover:bg-danger hover:text-white border border-danger/20 transition-all"
                onclick="deleteWorkflow('${w.id}')">
                <i class="ri-delete-bin-line text-lg"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    });

    tbody.innerHTML = html;
  }

  document.getElementById("workflow-search").addEventListener("input", function(){
    const q = (this.value || "").toLowerCase();
    const filtered = allWorkflows.filter(w =>
      (w.workflow_name || "").toLowerCase().includes(q) ||
      (w.module_name || "").toLowerCase().includes(q) ||
      ((w.template_name || w.email_template_id || "")).toLowerCase().includes(q)
    );
    renderWorkflows(filtered);
  });

  function deleteWorkflow(id){
    Swal.fire({
      title: 'Delete workflow?',
      text: "This mapping will be archived (deleted=1).",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#fe5412',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, delete it!'
    }).then(async (result) => {
      if(!result.isConfirmed) return;

      const res = await postForm(SUITE_ENTRYPOINT, { action:"delete_workflow", id });
      const text = await res.text();

      let out;
      try { out = JSON.parse(text); }
      catch(e){ return Swal.fire('Error','Invalid response','error'); }

      if(out.success){
        Swal.fire({ icon:'success', title:'Deleted!', timer:1200, showConfirmButton:false });
        loadWorkflows();
      } else {
        Swal.fire('Error', out.message || 'Delete failed', 'error');
      }
    });
  }

  loadWorkflows()
</script>

