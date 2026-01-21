<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2 py-4">
            <div>
                <h1 class="page-title font-bold text-2xl mb-1 text-gray-800 dark:text-gray-100">Tasks</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage and track your operational workflows</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="hs-tooltip inline-block">
                    <button type="button" class="hs-tooltip-toggle ti-btn ti-btn-sm ti-btn-light !border-gray-200 dark:!border-white/10" onclick="loadTasks()">
                        <i class="ri-refresh-line"></i>
                    </button>
                    <span class="hs-tooltip-content ti-tooltip-content" role="tooltip">Refresh Board</span>
                </div>
                <button type="button" class="ti-btn ti-btn-md bg-primary text-white font-medium shadow-sm hover:shadow-md transition-all btn-wave" data-hs-overlay="#task-modal">
                    <i class="ri-add-circle-line me-1 align-middle text-lg"></i> New Task
                </button>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6 pb-12">
            <!-- To Do -->
            <div class="xl:col-span-3 col-span-12">
                <div class="task-column bg-gray-50/50 dark:bg-black/10 rounded-2xl p-4 border border-dashed border-gray-200 dark:border-white/5 min-h-[70vh]" id="todo-col" ondrop="drop(event, 'Not Started')" ondragover="allowDrop(event)">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
                            <span class="font-bold text-gray-700 dark:text-gray-300">To Do</span>
                        </div>
                        <span class="badge bg-gray-100 text-gray-500 rounded-full px-2 py-1 text-[10px] font-bold" id="count-todo">0</span>
                    </div>
                    <div class="task-list space-y-4" id="todo-list">
                        <!-- Tasks will be injected here -->
                    </div>
                </div>
            </div>

            <!-- In Progress -->
            <div class="xl:col-span-3 col-span-12">
                <div class="task-column bg-primary/5 rounded-2xl p-4 border border-dashed border-primary/20 min-h-[70vh]" id="inprogress-col" ondrop="drop(event, 'In Progress')" ondragover="allowDrop(event)">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                            <span class="font-bold text-gray-700 dark:text-gray-300">In Progress</span>
                        </div>
                        <span class="badge bg-primary/10 text-primary rounded-full px-2 py-1 text-[10px] font-bold" id="count-inprogress">0</span>
                    </div>
                    <div class="task-list space-y-4" id="inprogress-list">
                        <!-- Tasks will be injected here -->
                    </div>
                </div>
            </div>

            <!-- Review -->
            <div class="xl:col-span-3 col-span-12">
                <div class="task-column bg-warning/5 rounded-2xl p-4 border border-dashed border-warning/20 min-h-[70vh]" id="review-col" ondrop="drop(event, 'Pending Input')" ondragover="allowDrop(event)">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-warning"></span>
                            <span class="font-bold text-gray-700 dark:text-gray-300">Review</span>
                        </div>
                        <span class="badge bg-warning/10 text-warning rounded-full px-2 py-1 text-[10px] font-bold" id="count-review">0</span>
                    </div>
                    <div class="task-list space-y-4" id="review-list">
                        <!-- Tasks will be injected here -->
                    </div>
                </div>
            </div>

            <!-- Done -->
            <div class="xl:col-span-3 col-span-12">
                <div class="task-column bg-success/5 rounded-2xl p-4 border border-dashed border-success/20 min-h-[70vh]" id="done-col" ondrop="drop(event, 'Completed')" ondragover="allowDrop(event)">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-success"></span>
                            <span class="font-bold text-gray-700 dark:text-gray-300">Done</span>
                        </div>
                        <span class="badge bg-success/10 text-success rounded-full px-2 py-1 text-[10px] font-bold" id="count-done">0</span>
                    </div>
                    <div class="task-list space-y-4" id="done-list">
                        <!-- Tasks will be injected here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Task Modal -->
<div class="hs-overlay ti-modal hidden" id="task-modal" tabindex="-1">
    <div class="hs-overlay-open:mt-7 ti-modal-box mt-0 ease-out max-w-lg !rounded-2xl overflow-hidden border-0 shadow-2xl">
        <div class="ti-modal-content">
            <div class="ti-modal-header bg-gray-50 dark:bg-black/20 border-b px-6 py-4">
                <h6 class="ti-modal-title font-bold text-gray-800 dark:text-gray-100" id="modal-title">Create New Task</h6>
                <button type="button" class="hs-dropdown-toggle p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-white/10" data-hs-overlay="#task-modal">
                    <i class="ri-close-line text-xl text-gray-500"></i>
                </button>
            </div>
            <form id="task-form">
                <input type="hidden" id="task-id" name="id">
                <div class="ti-modal-body p-6 space-y-5">
                    <div>
                        <label class="text-xs font-bold text-gray-500 mb-1.5 block">Task Subject <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="task-name" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl" placeholder="What needs to be done?" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 mb-1.5 block">Status</label>
                            <select name="status" id="task-status" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl">
                                <option value="Not Started">To Do</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Pending Input">Review</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 mb-1.5 block">Priority</label>
                            <select name="priority" id="task-priority" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl">
                                <option value="High">High</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 mb-1.5 block">Due Date</label>
                        <input type="date" name="date_entered" id="task-date-due" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 mb-1.5 block">Description</label>
                        <textarea name="description" id="task-description" class="form-control !border-gray-200 dark:!border-white/10 rounded-xl" rows="3" placeholder="Tell us more about this task..."></textarea>
                    </div>
                </div>
                <div class="ti-modal-footer bg-gray-50 dark:bg-black/20 border-t px-6 py-4 flex justify-end gap-2">
                    <button type="button" class="ti-btn ti-btn-light !rounded-xl px-4" data-hs-overlay="#task-modal">Cancel</button>
                    <button type="submit" class="ti-btn ti-btn-primary !rounded-xl px-6 font-bold" id="save-task-btn">Save Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let allTasks = [];

    $(document).ready(function() {
        loadTasks();

        $('#task-form').on('submit', function(e) {
            e.preventDefault();
            saveTask();
        });

        // Click card to edit
        $(document).on('click', '.task-card', function() {
            const id = $(this).attr('id').replace('task-', '');
            editTask(id);
        });
    });

    function loadTasks() {
        // Clear columns and show loading state if needed
        $('.task-list').html('');
        
        $.ajax({
            url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
            type: 'POST',
            data: { 
                action: 'fetch_tasks', 
                id: '<?php echo $_SESSION['user']['id']; ?>' 
            },
            success: function(response) {
                try {
                    allTasks = typeof response === 'string' ? JSON.parse(response) : response;
                    renderTasks(allTasks);
                } catch (e) {
                    console.error("Parse Error:", e);
                }
            }
        });
    }

    function renderTasks(tasks) {
        $('.task-list').html('');
        
        tasks.forEach(task => {
            const priorityClass = {
                'High': 'bg-danger/10 text-danger',
                'Medium': 'bg-warning/10 text-warning',
                'Low': 'bg-success/10 text-success'
            }[task.priority] || 'bg-gray-100 text-gray-500';

            const columnId = {
                'Not Started': 'todo-list',
                'In Progress': 'inprogress-list',
                'Pending Input': 'review-list',
                'Completed': 'done-list'
            }[task.status] || 'todo-list';

            const date = task.date_entered ? new Date(task.date_entered).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : 'No date';

            const html = `
                <div class="task-card bg-white dark:bg-white/5 p-4 rounded-xl border border-gray-200 dark:border-white/10 shadow-sm cursor-grab active:cursor-grabbing hover:border-primary/50 transition-all group" draggable="true" ondragstart="drag(event, '${task.id}')" id="task-${task.id}">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="font-bold text-gray-800 dark:text-gray-100 text-[14px] leading-tight line-clamp-2">${task.name}</div>
                        <div class="flex gap-1 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="event.stopPropagation(); editTask('${task.id}')" class="p-1.5 text-gray-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all" title="Edit Task"><i class="ri-edit-line"></i></button>
                            <button onclick="event.stopPropagation(); deleteTask('${task.id}')" class="p-1.5 text-gray-400 hover:text-danger hover:bg-danger/10 rounded-lg transition-all" title="Delete Task"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 line-clamp-3">${task.description || 'No description provided'}</p>
                    <div class="flex items-center justify-between mt-auto">
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider ${priorityClass}">${task.priority}</span>
                        <div class="flex items-center gap-1 text-[10px] font-medium text-gray-400">
                            <i class="ri-calendar-line"></i> ${date}
                        </div>
                    </div>
                </div>
            `;

            $(`#${columnId}`).append(html);
        });

        updateCounters();
    }

    function updateCounters() {
        $('#count-todo').text($('#todo-list').children().length);
        $('#count-inprogress').text($('#inprogress-list').children().length);
        $('#count-review').text($('#review-list').children().length);
        $('#count-done').text($('#done-list').children().length);
    }

    // Drag and Drop Logic
    function allowDrop(ev) {
        ev.preventDefault();
    }

    function drag(ev, id) {
        ev.dataTransfer.setData("taskId", id);
        $(ev.target).addClass('opacity-50');
    }

    document.addEventListener('dragend', function(e) {
        $('.task-card').removeClass('opacity-50');
    });

    function drop(ev, newStatus) {
        ev.preventDefault();
        const taskId = ev.dataTransfer.getData("taskId");
        const draggedElement = document.getElementById(`task-${taskId}`);
        
        let targetList = $(ev.target).closest('.task-column').find('.task-list');
        if (targetList.length) {
            targetList.append(draggedElement);
            updateTaskStatus(taskId, newStatus);
            updateCounters();
        }
    }

    function updateTaskStatus(id, status) {
        $.ajax({
            url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
            type: 'POST',
            data: { 
                action: 'update_task_status',
                id: id,
                status: status
            },
            success: function(resp) {
                // Background update, no need for major alert unless error
                const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (!data.success) {
                    Swal.fire('Error', 'Failed to sync status change.', 'error');
                    loadTasks();
                }
            }
        });
    }

    function saveTask() {
        const btn = $('#save-task-btn');
        btn.prop('disabled', true).text('Saving...');

        const formData = new FormData($('#task-form')[0]);
        formData.append('action', 'save_task');
        formData.append('assigned_user_id', '<?php echo $_SESSION['user']['id']; ?>');

        $.ajax({
            url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (data.success) {
                    HSOverlay.close('#task-modal');
                    resetModal();
                    loadTasks();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Task has been recorded.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
                btn.prop('disabled', false).text('Save Task');
            }
        });
    }

    function editTask(id) {
        const task = allTasks.find(t => t.id == id);
        if (task) {
            $('#modal-title').text('Update Task Details');
            $('#task-id').val(task.id);
            $('#task-name').val(task.name);
            $('#task-status').val(task.status);
            $('#task-priority').val(task.priority);
            $('#task-date-due').val(task.date_entered ? task.date_entered.substring(0, 10) : '');
            $('#task-description').val(task.description);
            
            HSOverlay.open('#task-modal');
        }
    }

    function deleteTask(id) {
        Swal.fire({
            title: 'Delete this task?',
            text: "This will remove the task permanently.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e9333f',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
                    type: 'POST',
                    data: { action: 'delete_task', id: id },
                    success: function() {
                        loadTasks();
                        Swal.fire({ title: 'Deleted!', icon: 'success', timer: 1000, showConfirmButton: false });
                    }
                });
            }
        });
    }

    function resetModal() {
        $('#modal-title').text('Create New Task');
        $('#task-form')[0].reset();
        $('#task-id').val('');
    }

    // Modal reset on close
    $(document).on('click', '[data-hs-overlay="#task-modal"]', function() {
        if (!$(this).hasClass('ri-edit-line')) {
            resetModal();
        }
    });
</script>

<style>
    .task-column {
        transition: background-color 0.2s ease;
    }
    .task-card:hover {
        transform: translateY(-2px);
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>