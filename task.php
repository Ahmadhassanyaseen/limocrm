<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>



<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
            <div>
                <h1 class="page-title font-medium text-lg mb-0">Tasks</h1>
            </div>
            <div class="btn-list">
                <button type="button" class="ti-btn ti-btn-primary !border-0 btn-wave me-0 waves-effect waves-light" onclick="addTask()">
                    <i class="ri-add-line me-1"></i> Add Task
                </button>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">
            <!-- To Do -->
            <div class="xl:col-span-3 col-span-12">
                <div class="task-column" id="todo" ondrop="drop(event)" ondragover="allowDrop(event)">
                    <div class="column-title">
                        <span>To Do</span>
                        <span class="badge bg-primary/10 text-primary rounded-full px-2 py-1 text-xs">3</span>
                    </div>
                    <div class="task-list" id="todo-list">
                        <div class="task-card" draggable="true" ondragstart="drag(event)" id="task-1">
                            <div class="font-semibold mb-1">Design System Update</div>
                            <p class="text-xs text-muted-foreground mb-2">Update the primary color palette for the dashboard.</p>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded">UI/UX</span>
                                <span class="text-[10px] text-muted-foreground">Jan 20</span>
                            </div>
                        </div>
                        <div class="task-card" draggable="true" ondragstart="drag(event)" id="task-2">
                            <div class="font-semibold mb-1">API Integration</div>
                            <p class="text-xs text-muted-foreground mb-2">Connect the leads table to the backend API.</p>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] bg-purple-100 text-purple-600 px-2 py-0.5 rounded">Dev</span>
                                <span class="text-[10px] text-muted-foreground">Jan 22</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- In Progress -->
            <div class="xl:col-span-3 col-span-12">
                <div class="task-column" id="inprogress" ondrop="drop(event)" ondragover="allowDrop(event)">
                    <div class="column-title">
                        <span>In Progress</span>
                        <span class="badge bg-warning/10 text-warning rounded-full px-2 py-1 text-xs">1</span>
                    </div>
                    <div class="task-list" id="inprogress-list">
                        <div class="task-card" draggable="true" ondragstart="drag(event)" id="task-3">
                            <div class="font-semibold mb-1">Drag & Drop Implementation</div>
                            <p class="text-xs text-muted-foreground mb-2">Implement native JS drag and drop for tasks.</p>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] bg-orange-100 text-orange-600 px-2 py-0.5 rounded">Feature</span>
                                <span class="text-[10px] text-muted-foreground">Jan 18</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review -->
            <div class="xl:col-span-3 col-span-12">
                <div class="task-column" id="review" ondrop="drop(event)" ondragover="allowDrop(event)">
                    <div class="column-title">
                        <span>Review</span>
                        <span class="badge bg-info/10 text-info rounded-full px-2 py-1 text-xs">0</span>
                    </div>
                    <div class="task-list" id="review-list">
                    </div>
                </div>
            </div>

            <!-- Done -->
            <div class="xl:col-span-3 col-span-12">
                <div class="task-column" id="done" ondrop="drop(event)" ondragover="allowDrop(event)">
                    <div class="column-title">
                        <span>Done</span>
                        <span class="badge bg-success/10 text-success rounded-full px-2 py-1 text-xs">0</span>
                    </div>
                    <div class="task-list" id="done-list">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <script>
function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
    ev.target.classList.add('dragging');
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
    var draggedElement = document.getElementById(data);
    draggedElement.classList.remove('dragging');
    
    // Find the closest task-column
    let target = ev.target;
    while (target && !target.classList.contains('task-column')) {
        target = target.parentElement;
    }
    
    if (target) {
        const taskList = target.querySelector('.task-list');
        taskList.appendChild(draggedElement);
        updateCounters();
    }
}

function updateCounters() {
    const columns = ['todo', 'inprogress', 'review', 'done'];
    columns.forEach(colId => {
        const col = document.getElementById(colId);
        const count = col.querySelector('.task-list').children.length;
        col.querySelector('.badge').innerText = count;
    });
}

function addTask() {
    const todoList = document.getElementById('todo-list');
    const id = 'task-' + Date.now();
    const newTask = document.createElement('div');
    newTask.className = 'task-card';
    newTask.draggable = true;
    newTask.id = id;
    newTask.ondragstart = drag;
    newTask.innerHTML = `
        <div class="font-semibold mb-1">New Task</div>
        <p class="text-xs text-muted-foreground mb-2">Double click to edit task description.</p>
        <div class="flex items-center justify-between">
            <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded">General</span>
            <span class="text-[10px] text-muted-foreground">Today</span>
        </div>
    `;
    todoList.appendChild(newTask);
    updateCounters();
}

// Add event listeners to remove 'dragging' class if drag is cancelled
document.addEventListener('dragend', function(event) {
    if (event.target.classList.contains('task-card')) {
        event.target.classList.remove('dragging');
    }
});
</script> -->
<script>
function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
    ev.target.classList.add('dragging');
}

function drop(ev) {
    ev.preventDefault();
    const data = ev.dataTransfer.getData("text");
    const draggedElement = document.getElementById(data);
    if (!draggedElement) return;

    draggedElement.classList.remove('dragging');

    // closest task-column find
    let target = ev.target;
    while (target && !target.classList.contains('task-column')) {
        target = target.parentElement;
    }

    if (target) {
        const taskList = target.querySelector('.task-list');
        taskList.appendChild(draggedElement);
        updateCounters();
    }
}

function updateCounters() {
    const columns = ['todo', 'inprogress', 'review', 'done'];
    columns.forEach(colId => {
        const col = document.getElementById(colId);
        const list = col.querySelector('.task-list');
        const badge = col.querySelector('.badge');
        if (!list || !badge) return;
        badge.innerText = list.children.length;
    });
}

/* =========================
   EDIT + DELETE (Event Delegation)
   ========================= */

// 1) Double click: make title/desc editable
document.addEventListener('dblclick', function(e) {
    const title = e.target.closest('.task-title');
    const desc  = e.target.closest('.task-desc');

    if (title) {
        makeEditable(title);
    } else if (desc) {
        makeEditable(desc);
    }
});

function makeEditable(el) {
    // Enable editing
    el.setAttribute('contenteditable', 'true');
    el.dataset.editing = "1";

    // Put cursor at end
    const range = document.createRange();
    range.selectNodeContents(el);
    range.collapse(false);
    const sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);

    el.focus();
}

// 2) Save edit on Enter (for title) and on blur (title/desc)
document.addEventListener('keydown', function(e) {
    const editingEl = e.target;
    if (editingEl && editingEl.dataset && editingEl.dataset.editing === "1") {
        // Enter press to finish (prevent new line)
        if (e.key === 'Enter') {
            e.preventDefault();
            editingEl.blur();
        }
        // Escape to cancel -> revert to original
        if (e.key === 'Escape') {
            e.preventDefault();
            // Revert if backup exists
            if (editingEl.dataset.backup !== undefined) {
                editingEl.textContent = editingEl.dataset.backup;
            }
            editingEl.blur();
        }
    }
});

// backup original content when focusing for edit
document.addEventListener('focusin', function(e) {
    const el = e.target;
    if (el && el.dataset && el.dataset.editing === "1") {
        if (el.dataset.backup === undefined) {
            el.dataset.backup = el.textContent;
        }
    }
});

// finalize on blur
document.addEventListener('focusout', function(e) {
    const el = e.target;
    if (el && el.dataset && el.dataset.editing === "1") {
        el.removeAttribute('contenteditable');
        delete el.dataset.editing;
        delete el.dataset.backup;

        // optional: prevent empty title/desc
        if (el.classList.contains('task-title') && el.textContent.trim() === '') {
            el.textContent = 'Untitled Task';
        }
        if (el.classList.contains('task-desc') && el.textContent.trim() === '') {
            el.textContent = 'No description';
        }
    }
});

// 3) Delete button
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.task-delete-btn');
    if (!btn) return;

    const card = btn.closest('.task-card');
    if (!card) return;

    // simple confirm (remove if you don't want it)
    if (!confirm('Delete this task?')) return;

    card.remove();
    updateCounters();
});

// drag end cleanup
document.addEventListener('dragend', function(event) {
    if (event.target.classList && event.target.classList.contains('task-card')) {
        event.target.classList.remove('dragging');
    }
});

/* =========================
   Add Task (with delete button + editable targets)
   ========================= */
function addTask() {
    const todoList = document.getElementById('todo-list');
    const id = 'task-' + Date.now();

    const newTask = document.createElement('div');
    newTask.className = 'task-card';
    newTask.draggable = true;
    newTask.id = id;
    newTask.ondragstart = drag;

    newTask.innerHTML = `
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <div class="task-title font-semibold mb-1">New Task</div>
                <p class="task-desc text-xs text-muted-foreground mb-2">Double click to edit task description.</p>
            </div>
            <div class="task-actions">
                <button type="button" class="task-delete-btn" title="Delete Task" aria-label="Delete Task">✕</button>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded">General</span>
            <span class="text-[10px] text-muted-foreground">Today</span>
        </div>
    `;

    todoList.appendChild(newTask);
    updateCounters();
}
</script>


<?php include_once "components/layout/footer.php"; ?>