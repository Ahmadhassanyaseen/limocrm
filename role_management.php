

<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>





<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
            <div>
                <h1 class="page-title font-medium text-lg mb-0">Role Management</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Manage user roles and their access permissions.</p>
            </div>
            <div class="btn-list">
                <button type="button" class="ti-btn ti-btn-sm bg-primary text-white btn-wave waves-light waves-effect waves-light" id="btn-add-role">
                    <i class="ri-add-line font-medium align-middle"></i> Add New Role
                </button>
            </div>
        </div>

        <!-- Start:: row-1 -->
        <div class="grid grid-cols-12 gap-x-6">
            <div class="col-span-12">
                <div class="box">
                    <div class="box-header justify-between">
                        <div class="box-title">
                            Roles List
                        </div>
                        <div class="flex gap-2">
                            <div class="relative">
                                <input type="text" id="role-search" class="form-control form-control-sm ps-9" placeholder="Search roles...">
                                <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                    <i class="ri-search-line text-gray-500"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table whitespace-nowrap table-hover min-w-full">
                                <thead>
                                    <tr class="border-b border-defaultborder dark:border-defaultborder/10">
                                        <th scope="col" class="text-start font-semibold">Role Name</th>
                                        <th scope="col" class="text-start font-semibold">Description</th>
                                        <th scope="col" class="text-start font-semibold">Created By</th>
                                        <th scope="col" class="text-start font-semibold">Created At</th>
                                        <th scope="col" class="text-end font-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="roles-table-body">
                                    <!-- Roles populated by JS -->
                                </tbody>
                            </table>
                        </div>
                        <div id="no-roles-found" class="hidden text-center p-5">
                            <div class="text-gray-500">No roles found matching your search.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End:: row-1 -->

        <!-- Add/Edit Role Modal -->
        <div class="hs-overlay ti-modal hidden" id="role-modal">
            <div class="hs-overlay-open:mt-7 ti-modal-box mt-0 ease-out w-full max-w-5xl">
                <div class="ti-modal-content">
                    <div class="ti-modal-header bg-light">
                        <h6 class="ti-modal-title font-bold" id="role-modal-title">Add Role</h6>
                        <button type="button" class="hs-dropdown-toggle ti-modal-close-btn" data-hs-overlay="#role-modal">
                            <span class="sr-only">Close</span>
                            <svg class="w-3.5 h-3.5" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.258206 1.00652C0.351976 0.912791 0.479126 0.860131 0.611706 0.860131C0.744296 0.860131 0.871447 0.912791 0.965207 1.00652L3.61171 3.65302L6.25822 1.00652C6.30432 0.958771 6.35952 0.920671 6.42052 0.894471C6.48152 0.868271 6.54712 0.854471 6.61352 0.853901C6.67992 0.853321 6.74572 0.865971 6.80722 0.891111C6.86862 0.916251 6.92442 0.953381 6.97142 1.00032C7.01832 1.04727 7.05552 1.1031 7.08062 1.16454C7.10572 1.22599 7.11842 1.29183 7.11782 1.35822C7.11722 1.42461 7.10342 1.49022 7.07722 1.55122C7.05102 1.61222 7.01292 1.6674 6.96522 1.71352L4.31871 4.36002L6.96522 7.00648C7.05632 7.10078 7.10672 7.22708 7.10552 7.35818C7.10442 7.48928 7.05182 7.61468 6.95912 7.70738C6.86642 7.80018 6.74102 7.85268 6.60992 7.85388C6.47882 7.85498 6.35252 7.80458 6.25822 7.71348L3.61171 5.06702L0.965207 7.71348C0.870907 7.80458 0.744606 7.85498 0.613506 7.85388C0.482406 7.85268 0.357007 7.80018 0.264297 7.70738C0.171597 7.61468 0.119017 7.48928 0.117877 7.35818C0.116737 7.22708 0.167126 7.10078 0.258206 7.00648L2.90471 4.36002L0.258206 1.71352C0.164476 1.61976 0.111816 1.4926 0.111816 1.36002C0.111816 1.22744 0.164476 1.10028 0.258206 1.00652Z" fill="currentColor"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="ti-modal-body p-6">
                        <input type="hidden" id="role-id">
                        <div class="grid grid-cols-12 gap-6">
                            <div class="col-span-12 md:col-span-6">
                                <label for="role-name" class="form-label block text-sm font-medium mb-2">Role Name <span class="text-red-500">*</span></label>
                                <input type="text" class="form-control w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="role-name" placeholder="E.g., Sales Manager">
                            </div>
                            <div class="col-span-12 md:col-span-6">
                                <label for="role-description" class="form-label block text-sm font-medium mb-2">Description</label>
                                <textarea class="form-control w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="role-description" rows="1" placeholder="Brief description of the role"></textarea>
                            </div>
                            <div class="col-span-12 mt-2">
                                <div class="bg-gray-50 dark:bg-black/20 p-4 rounded-lg border border-defaultborder dark:border-defaultborder/10">
                                    <h6 class="font-bold text-sm uppercase text-gray-600 mb-4">Access Permissions</h6>
                                    <div class="table-responsive">
                                        <table class="table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead>
                                                <tr>
                                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500 uppercase">Module</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Create</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Read</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Update</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody id="permissions-body" class="divide-y divide-gray-200 dark:divide-gray-700">
                                                <!-- Permissions populated by JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ti-modal-footer bg-light px-6 py-4 flex justify-end gap-2 text-end">
                         <button type="button" class="hs-dropdown-toggle ti-btn ti-btn-light" data-hs-overlay="#role-modal">Cancel</button>
                        <button type="button" class="ti-btn bg-primary text-white hover:bg-primary-focus focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all" id="btn-save-role">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/libs/preline/preline.js"></script>

<script>
    const modules = ['Leads', 'Users']; 
    let allRoles = []; // Store roles locally for sorting/searching

    $(document).ready(function() {
        fetchRoles();

        $('#btn-add-role').click(function() {
            resetModal();
            openModal();
        });

        $('#btn-save-role').click(function() {
            saveRole();
        });

        // Search implementation
        $('#role-search').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            var visibleRows = 0;
            $("#roles-table-body tr").filter(function() {
                var match = $(this).text().toLowerCase().indexOf(value) > -1;
                $(this).toggle(match);
                if(match) visibleRows++;
                return match;
            });
            
            if(visibleRows === 0) {
                 $('#no-roles-found').removeClass('hidden');
            } else {
                 $('#no-roles-found').addClass('hidden');
            }
        });
    });

    function fetchRoles() {
        var formData = new FormData();
        formData.append('action', 'fetch_roles');

        // Show simplified loading if needed, or just refresh
        $('#roles-table-body').html('<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div> Loading roles...</td></tr>');

        $.ajax({
            url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                let data = parseResponse(response);
                let rows = '';
                if(Array.isArray(data) && data.length > 0) {
                    allRoles = data;
                     data.forEach(role => {
                        let createdDate = new Date(role.created_at).toLocaleDateString();
                        rows += `
                            <tr class="hover:bg-gray-50 dark:hover:bg-black/20 transition-colors duration-200">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="avatar bg-primary avatar-sm text-primary rounded-full">
                                            ${role.name.charAt(0).toUpperCase()}
                                        </div>
                                        <div class="font-medium text-gray-800 dark:text-gray-200">${role.name}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">${role.description || '-'}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="badge bg-light text-dark"><?php echo $_SESSION['user']['first_name'] ?? 'Admin'; ?></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">${createdDate}</td>
                                <td class="px-4 py-3 text-end">
                                    <div class="btn-group">
                                        <button class="ti-btn ti-btn-sm ti-btn-soft-primary p-1 rounded-md hover:bg-primary hover:text-white transition-all" onclick='editRole(${JSON.stringify(role)})' data-bs-toggle="tooltip" title="Edit">
                                            <i class="ri-edit-line text-lg"></i>
                                        </button>
                                        <button class="ti-btn ti-btn-sm ti-btn-soft-danger p-1 rounded-md hover:bg-danger hover:text-white transition-all ms-1" onclick="deleteRole('${role.id}')" data-bs-toggle="tooltip" title="Delete">
                                            <i class="ri-delete-bin-line text-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                     $('#no-roles-found').addClass('hidden');
                } else {
                    rows = '<tr><td colspan="5" class="text-center py-5 text-gray-500">No roles found. Click "Add Role" to create one.</td></tr>';
                }
                $('#roles-table-body').html(rows);
            },
            error: function() {
                 $('#roles-table-body').html('<tr><td colspan="5" class="text-center text-red-500 py-4">Failed to load roles.</td></tr>');
            }
        });
    }

    function resetModal() {
        $('#role-id').val('');
        $('#role-name').val('');
        $('#role-description').val('');
        $('#role-modal-title').text('Create New Role');
        $('#btn-save-role').text('Save Role');
        
        // Reset permissions
        let permRows = '';
        modules.forEach(module => {
            permRows += `
                <tr class="hover:bg-gray-50 dark:hover:bg-black/10">
                    <td class="px-3 py-2 font-medium text-gray-700 dark:text-gray-300">${module}</td>
                    <td class="text-center align-middle"><input type="checkbox" class="form-checkbox w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary" data-module="${module}" data-perm="can_create"></td>
                    <td class="text-center align-middle"><input type="checkbox" class="form-checkbox w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary" data-module="${module}" data-perm="can_read"></td>
                    <td class="text-center align-middle"><input type="checkbox" class="form-checkbox w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary" data-module="${module}" data-perm="can_update"></td>
                    <td class="text-center align-middle"><input type="checkbox" class="form-checkbox w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary" data-module="${module}" data-perm="can_delete"></td>
                </tr>
            `;
        });
        $('#permissions-body').html(permRows);
    }

    function openModal() {
       HSOverlay.open(document.querySelector('#role-modal'));
    }

    function closeModal() {
       HSOverlay.close(document.querySelector('#role-modal'));
    }

    function editRole(role) {
        resetModal();
        $('#role-id').val(role.id);
        $('#role-name').val(role.name);
        $('#role-description').val(role.description);
        $('#role-modal-title').text('Edit Role');
        $('#btn-save-role').text('Update Role');

        if(role.permissions) {
            role.permissions.forEach(perm => {
                // Ensure we only process known modules, or dynamically add others if needed
                if(modules.includes(perm.module)) {
                    $(`input[data-module="${perm.module}"][data-perm="can_create"]`).prop('checked', perm.can_create == 1);
                    $(`input[data-module="${perm.module}"][data-perm="can_read"]`).prop('checked', perm.can_read == 1);
                    $(`input[data-module="${perm.module}"][data-perm="can_update"]`).prop('checked', perm.can_update == 1);
                    $(`input[data-module="${perm.module}"][data-perm="can_delete"]`).prop('checked', perm.can_delete == 1);
                }
            });
        }

        openModal();
    }

    function saveRole() {
        let id = $('#role-id').val();
        let name = $('#role-name').val();
        let description = $('#role-description').val();
        
        if(!name) {
             const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            Toast.fire({
                icon: 'error',
                title: 'Role Name is required'
            });
            return;
        }

        let permissions = [];
        $('#permissions-body tr').each(function() {
            let row = $(this);
            let module = row.find('td:first').text();
            let perm = {
                module: module,
                can_create: row.find(`input[data-perm="can_create"]`).is(':checked'),
                can_read: row.find(`input[data-perm="can_read"]`).is(':checked'),
                can_update: row.find(`input[data-perm="can_update"]`).is(':checked'),
                can_delete: row.find(`input[data-perm="can_delete"]`).is(':checked')
            };
            permissions.push(perm);
        });

        let action = id ? 'update_role' : 'create_role';
        
        var formData = new FormData();
        formData.append('action', action);
        if(id) formData.append('id', id);
        formData.append('name', name);
        formData.append('description', description);
        formData.append('permissions', JSON.stringify(permissions));
        formData.append('created_by', '<?php echo $_SESSION['user']['id'] ?? 'Admin'; ?>');

        let btn = $('#btn-save-role');
        let originalText = btn.text();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

        $.ajax({
            url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                let data = parseResponse(response);
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    closeModal();
                    fetchRoles();
                } else {
                    Swal.fire('Error', data.message || 'Failed to save role', 'error');
                }
                btn.prop('disabled', false).text(originalText);
            },
            error: function() {
                 Swal.fire('Error', 'Server connection error', 'error');
                 btn.prop('disabled', false).text(originalText);
            }
        });
    }

    function deleteRole(id) {
        Swal.fire({
            title: 'Delete this role?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('action', 'delete_role');
                formData.append('id', id);

                $.ajax({
                    url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        let data = parseResponse(response);
                        if(data.success) {
                             Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Role has been deleted.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            fetchRoles();
                        } else {
                            Swal.fire('Error', data.message || 'Failed to delete role', 'error');
                        }
                    }
                });
            }
        });
    }

    function parseResponse(response) {
        if (typeof response === 'string') {
            try {
                return JSON.parse(response);
            } catch (e) {
                console.error("Parsing error", e);
                return {};
            }
        }
        return response;
    }
</script>