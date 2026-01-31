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
<form id="Notes-form">
                <input type="hidden" id="Notes-id" name="id">
                <div class="ti-modal-body p-6 space-y-5">
                    <div>
                        <label class="text-xs font-bold text-gray-500 mb-1.5 block">Notes Subject <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="Notes-name" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl" placeholder="What needs to be done?" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 mb-1.5 block">Status</label>
                            <select name="status" id="Notes-status" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl">
                                <option value="Not Started">To Do</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Pending Input">Review</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 mb-1.5 block">Priority</label>
                            <select name="priority" id="Notes-priority" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl">
                                <option value="High">High</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 mb-1.5 block">Due Date</label>
                        <input type="date" name="date_entered" id="Notes-date-due" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 mb-1.5 block">Description</label>
                        <textarea name="description" id="Notes-description" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl" rows="3" placeholder="Tell us more about this Notes..."></textarea>
                    </div>
                </div>
                <div class="ti-modal-footer bg-gray-50 dark:bg-black/20 border-t px-6 py-4 flex justify-end gap-2">
                    <button type="button" class="ti-btn ti-btn-light !rounded-xl px-4" data-hs-overlay="#Notes-modal">Cancel</button>
                    <button type="submit" class="ti-btn ti-btn-primary !rounded-xl px-6 font-bold" id="save-Notes-btn">Save Notes</button>
                </div>
            </form>

 
</body>
</html>

<?php include_once "components/layout/footer.php"; ?>
