
<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>
 
<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2 py-4">
            <div>
                <h1 class="page-title font-bold text-2xl mb-1 text-gray-800 dark:text-gray-100">Email Templates</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Design and manage your professional communication assets</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="hs-tooltip inline-block">
                    <button type="button" class="hs-tooltip-toggle ti-btn ti-btn-sm ti-btn-light !border-gray-200 dark:!border-white/10" onclick="loadTemplates()">
                        <i class="ri-refresh-line"></i>
                    </button>
                    <span class="hs-tooltip-content ti-tooltip-content" role="tooltip">Refresh List</span>
                </div>
                <a href="email_template.php" class="ti-btn ti-btn-md bg-primary text-white font-medium shadow-sm hover:shadow-md transition-all btn-wave">
                    <i class="ri-add-circle-line me-1 align-middle text-lg"></i> New Template
                </a>
            </div>
        </div>
        <!-- Page Header Close -->

        <!-- Action Bar -->
        <div class="box custom-box !bg-transparent border-0 shadow-none mb-4">
            <div class="box-body p-0">
                <div class="grid grid-cols-12 gap-4">
                    <div class="xl:col-span-8 col-span-12">
                        <div class="relative group">
                            <input type="text" id="template-search" 
                                class="form-control ps-10 h-[48px] !bg-white dark:!bg-black/20 border-gray-200 dark:border-white/10 rounded-xl focus:ring-2 focus:ring-primary/20 transition-all" 
                                placeholder="Search by name or subject line...">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none transition-colors group-focus-within:text-primary text-gray-400">
                                <i class="ri-search-2-line text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Templates List Area -->
        <div class="grid grid-cols-12 gap-x-6">
            <div class="xl:col-span-12 col-span-12">
                <div class="box custom-box overflow-hidden rounded-2xl border border-gray-200 dark:border-white/10 shadow-sm">
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table whitespace-nowrap min-w-full">
                                <thead>
                                    <tr class="bg-gray-50/50 dark:bg-black/20 border-b border-gray-200 dark:border-white/10">
                                        <th scope="col" class="px-6 py-4 text-start text-xs font-bold uppercase tracking-wider text-gray-500">Template Details</th>
                                        <th scope="col" class="px-6 py-4 text-start text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Type</th>
                                        <th scope="col" class="px-6 py-4 text-start text-xs font-bold uppercase tracking-wider text-gray-500">Subject Line</th>
                                        <th scope="col" class="px-6 py-4 text-start text-xs font-bold uppercase tracking-wider text-gray-500">Created On</th>
                                        <th scope="col" class="px-6 py-4 text-end text-xs font-bold uppercase tracking-wider text-gray-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="template-list" class="divide-y divide-gray-100 dark:divide-white/5">
                                    <tr>
                                        <td colspan="5" class="text-center py-20">
                                            <div class="flex flex-col items-center">
                                                <div class="ti-spinner w-10 h-10 border-[3px] border-t-primary border-gray-200 dark:border-white/10 rounded-full animate-spin mb-4" role="status"></div>
                                                <span class="text-gray-500 font-medium tracking-wide">Fetching templates from LimoCRM...</span>
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

<!-- Preview Modal -->
<div class="hs-overlay ti-modal hidden" id="preview-modal" tabindex="-1" aria-overlay="true">
    <div class="hs-overlay-open:mt-7 ti-modal-box mt-0 ease-out max-w-4xl !rounded-2xl overflow-hidden border-0 shadow-2xl">
        <div class="ti-modal-content">
            <div class="ti-modal-header bg-gray-50 dark:bg-black/20 border-b px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-success/10 text-success flex items-center justify-center">
                        <i class="ri-eye-line text-xl"></i>
                    </div>
                    <div>
                        <h6 class="ti-modal-title font-bold text-gray-800 dark:text-gray-100" id="preview-title">Template Preview</h6>
                        <p class="text-xs text-gray-500 mb-0">See how your email looks to customers</p>
                    </div>
                </div>
                <button type="button" class="hs-dropdown-toggle p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-white/10 transition-colors" data-hs-overlay="#preview-modal">
                    <i class="ri-close-line text-xl text-gray-500"></i>
                </button>
            </div>
            <div class="ti-modal-body p-0 bg-gray-100 dark:bg-black/40" style="height: 650px;">
                <iframe id="preview-iframe" class="w-full h-full border-0"></iframe>
            </div>
            <div class="ti-modal-footer bg-white dark:bg-black/20 border-t px-6 py-3 flex justify-between items-center text-xs text-gray-400">
                <span>Responsive View Active</span>
                <button type="button" class="ti-btn ti-btn-light !rounded-lg" data-hs-overlay="#preview-modal">Close Preview</button>
            </div>
        </div>
    </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        let allTemplates = [];

        // Load Templates
        window.loadTemplates = function() {
            $('#template-list').html(`
                <tr>
                    <td colspan="5" class="text-center py-20">
                        <div class="flex flex-col items-center">
                            <div class="ti-spinner w-8 h-8 border-[3px] border-t-primary border-gray-200 rounded-full animate-spin mb-4"></div>
                            <span class="text-gray-500">Reloading templates...</span>
                        </div>
                    </td>
                </tr>
            `);

            $.ajax({
                url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
                type: 'POST',
                data: { action: 'fetch_email_templates', id: '<?php echo $_SESSION['user']['id']; ?>' },
                success: function(response) {
                    try {
                        allTemplates = typeof response === 'string' ? JSON.parse(response) : response;
                        renderTemplates(allTemplates);
                    } catch (e) {
                        console.error("Parse Error:", e);
                        $('#template-list').html('<tr><td colspan="5" class="text-center py-8 text-red-500 font-medium">Unable to parse templates response.</td></tr>');
                    }
                },
                error: function() {
                    $('#template-list').html('<tr><td colspan="5" class="text-center py-8 text-danger font-medium">Failed to connect to the server.</td></tr>');
                }
            });
        }

        function renderTemplates(templates) {
            let html = '';
            if (templates.length === 0) {
                html = `
                    <tr>
                        <td colspan="5" class="text-center py-24">
                            <div class="bg-gray-50 dark:bg-black/10 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="ri-mail-add-line text-4xl text-gray-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">No Templates Found</h3>
                            <p class="text-gray-500 mb-6">Start by creating a new template for your marketing campaigns.</p>
                            <a href="email_template.php" class="ti-btn ti-btn-soft-primary">
                                <i class="ri-add-line me-1"></i> Create My First Template
                            </a>
                        </td>
                    </tr>
                `;
            } else {
                templates.forEach(t => {
                    const date = new Date(t.date_entered).toLocaleDateString('en-US', {
                        year: 'numeric', month: 'short', day: 'numeric'
                    });
                    
                    const typeClass = {
                        'email': 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                        'campaign': 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
                        'support': 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400'
                    }[t.type] || 'bg-gray-100 text-gray-700';

                    html += `
                        <tr class="group hover:bg-gray-50/80 dark:hover:bg-white/5 transition-all duration-300">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center transition-transform group-hover:scale-110">
                                        <i class="ri-article-line text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 dark:text-gray-100 text-[15px] mb-0.5 line-clamp-1">${t.name}</div>
                                        <div class="text-[12px] text-gray-500 line-clamp-1 opacity-70">${t.description || 'No description provided'}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wider ${typeClass}">
                                    ${t.type}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium max-w-[280px] truncate">
                                    ${t.subject}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-gray-500 font-medium">
                                    <i class="ri-calendar-line me-1 align-middle"></i> ${date}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-end">
                                <div class="flex justify-end gap-2 outline-none">
                                    <button class="w-9 h-9 rounded-xl flex items-center justify-center bg-success/10 text-success hover:bg-success hover:text-white border border-success/20 transition-all preview-template-btn" data-id="${t.id}" data-hs-overlay="#preview-modal">
                                        <i class="ri-eye-line text-lg"></i>
                                    </button>
                                    <a href="email_template.php?id=${t.id}" class="w-9 h-9 rounded-xl flex items-center justify-center bg-primary/10 text-primary hover:bg-primary hover:text-white border border-primary/20 transition-all">
                                        <i class="ri-edit-line text-lg"></i>
                                    </a>
                                    <button class="w-9 h-9 rounded-xl flex items-center justify-center bg-danger/10 text-danger hover:bg-danger hover:text-white border border-danger/20 transition-all delete-template-btn" data-id="${t.id}">
                                        <i class="ri-delete-bin-line text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#template-list').html(html);
        }

        // Search Logic
        $('#template-search').on('input', function() {
            const query = $(this).val().toLowerCase();
            const filtered = allTemplates.filter(t => 
                (t.name ? t.name.toLowerCase() : '').includes(query) || 
                (t.subject ? t.subject.toLowerCase() : '').includes(query)
            );
            renderTemplates(filtered);
        });

        // Preview Template
        $(document).on('click', '.preview-template-btn', function() {
            const id = $(this).data('id');
            const template = allTemplates.find(t => t.id == id);
            
            if (template) {
                $('#preview-title').text(template.name);
                const doc = $('#preview-iframe')[0].contentWindow.document;
                doc.open();
                
                // Decode HTML entities if they exist
                const txt = document.createElement("textarea");
                txt.innerHTML = template.body_html;
                const decodedHtml = txt.value;
                
                doc.write(decodedHtml);
                doc.close();
            }
        });

        // Delete Template
        $(document).on('click', '.delete-template-btn', function() {
            const id = $(this).data('id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This template will be permanently archived and cannot be recovered.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#fe5412',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'rounded-lg px-4 py-2 font-bold',
                    cancelButton: 'rounded-lg px-4 py-2 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
                        type: 'POST',
                        data: { action: 'delete_email_template', id: id },
                        success: function(response) {
                            const data = typeof response === 'string' ? JSON.parse(response) : response;
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Template has been removed.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                loadTemplates();
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        }
                    });
                }
            });
        });

        // Initial Load
        loadTemplates();
    });
</script>

<style>
    /* Modern Scrollbar */
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: transparent;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
    
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
