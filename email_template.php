<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$templateId = $_GET['id'] ?? '';
$template = null;

if (!empty($templateId)) {
    $response = getEmailTemplate(['id' => $templateId]);
    if ($response['success']) {
        $template = $response['template'];
    }
}
?>

<!-- GrapesJS Assets -->
<link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
<script src="https://unpkg.com/grapesjs"></script>
<script src="https://unpkg.com/grapesjs-preset-newsletter"></script>
 
<div class="main-content app-content">
    <div class="container-fluid !p-0">
        <!-- Sticky Sub-Header / Toolbar -->
        <div class="flex items-center justify-between px-6 py-3 bg-white dark:bg-black/20 border-b border-gray-200 dark:border-white/10 sticky top-0 z-[100]">
            <div class="flex items-center gap-4">
                <a href="email_templates.php" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:bg-gray-200 transition-all">
                    <i class="ri-arrow-left-line text-xl"></i>
                </a>
                <div>
                    <h1 class="font-bold text-lg mb-0 text-gray-800 dark:text-gray-100"><?php echo $template ? 'Edit Template' : 'New Template'; ?></h1>
                    <div class="flex items-center gap-2 text-[11px] text-gray-400 font-medium">
                        <span class="flex items-center gap-1"><i class="ri-history-line"></i> Auto-save ready</span>
                        <span class="text-gray-200 dark:text-white/10">|</span>
                        <span>LimoCRM Builder v2.0</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Mode Switcher -->
                <div class="flex bg-gray-100 dark:bg-white/5 p-1 rounded-xl border border-gray-200 dark:border-white/5">
                    <button type="button" class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all editor-toggle active shadow-sm" data-view="builder">
                        <i class="ri-layout-grid-line me-1.5 align-middle"></i>Builder
                    </button>
                    <button type="button" class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all editor-toggle text-gray-500 hover:text-primary" data-view="code">
                        <i class="ri-code-s-slash-line me-1.5 align-middle"></i>Source
                    </button>
                </div>
                
                <div class="w-px h-8 bg-gray-200 dark:bg-white/10 mx-1"></div>

                <button type="button" class="ti-btn ti-btn-md bg-primary text-white font-bold px-6 shadow-sm hover:shadow-primary/20 transition-all border-0 rounded-xl" id="save-template-btn">
                    <i class="ri-save-3-line me-1.5 align-middle text-lg"></i> Finish & Save
                </button>
            </div>
        </div>

        <div class="flex overflow-hidden" style="height: calc(100vh - 75px);">
            <!-- Sidebar: Attributes & Variables -->
            <div class="w-[320px] h-full bg-white dark:bg-black/20 border-r border-gray-200 dark:border-white/10 flex flex-col">
                <div class="flex-grow overflow-y-auto px-6 py-6 scrollbar-thin">
                    <form id="template-info-form">
                        <input type="hidden" id="template-id" name="id" value="<?php echo $template['id'] ?? ''; ?>">
                        
                        <!-- Group: basic -->
                        <div class="mb-8">
                            <label class="text-[10px] font-black tracking-widest text-primary uppercase mb-4 block">Basic Identity</label>
                            <div class="space-y-5">
                                <div class="relative group">
                                    <label for="name" class="text-xs font-bold text-gray-500 mb-1.5 block">Template Alias <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control !bg-gray-50 dark:!bg-white/5 !border-gray-200 dark:!border-white/10 focus:!border-primary rounded-xl" id="name" name="name" 
                                        placeholder="e.g. Booking Confirmation" value="<?php echo htmlspecialchars($template['name'] ?? ''); ?>" required>
                                </div>
                                <div class="relative">
                                    <label for="type" class="text-xs font-bold text-gray-500 mb-1.5 block">Organization Category</label>
                                    <select class="form-control !bg-gray-50 dark:!bg-white/5 !border-gray-200 dark:!border-white/10 focus:!border-primary rounded-xl" id="type" name="type">
                                        <option value="email" <?php echo ($template['type'] ?? '') == 'email' ? 'selected' : ''; ?>>Standard Email</option>
                                        <option value="campaign" <?php echo ($template['type'] ?? '') == 'campaign' ? 'selected' : ''; ?>>Marketing Campaign</option>
                                        <option value="support" <?php echo ($template['type'] ?? '') == 'support' ? 'selected' : ''; ?>>Support Ticket</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Group: email content -->
                        <div class="mb-8 p-5 rounded-2xl bg-primary/5 border border-primary/10">
                            <label class="text-[10px] font-black tracking-widest text-primary uppercase mb-4 block">Email Delivery Specs</label>
                            <div class="space-y-4">
                                <div>
                                    <label for="subject" class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 block">Default Subject Line <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control !bg-white dark:!bg-black/20 !border-primary/20 focus:!border-primary rounded-xl" id="subject" name="subject" 
                                        placeholder="Hello, $firstName!" value="<?php echo htmlspecialchars($template['subject'] ?? ''); ?>" required>
                                </div>
                                <div>
                                    <label for="description" class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 block">Internal Internal Note</label>
                                    <textarea class="form-control !bg-white dark:!bg-black/20 !border-primary/20 focus:!border-primary rounded-xl" id="description" name="description" rows="2" 
                                        placeholder="Who is this for?"><?php echo htmlspecialchars($template['description'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>

                     
                        <!-- Group: variables (Restored) -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <label class="text-[10px] font-black tracking-widest text-primary uppercase block mb-0">Smart Tokens</label>
                                <span class="px-2 py-0.5 rounded bg-success/10 text-success text-[10px] font-bold tracking-tighter cursor-help hs-tooltip inline-block">
                                    LIVE
                                    <span class="hs-tooltip-content ti-tooltip-content !p-2 !text-[10px]" role="tooltip">Tokens will be replaced with lead data</span>
                                </span>
                            </div>
                            <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-2xl border border-gray-100 dark:border-white/5">
                                <select class="form-control !bg-white dark:!bg-black/20 !border-gray-200 dark:!border-white/10 rounded-xl text-xs mb-3" id="variable-selector">
                                    <option value="">Insert variable...</option>
                                    <optgroup label="Core Identity">
                                        <option value="$firstName">First Name</option>
                                        <option value="$lastName">Last Name</option>
                                        <option value="$email">Email Address</option>
                                        <option value="$phone">Phone Number</option>
                                    </optgroup>
                                    <optgroup label="Trip Logistics">
                                        <option value="$pickupLocation">Pickup Point</option>
                                        <option value="$dropoffLocation">Destination</option>
                                        <option value="$pickupDate">Pickup Date</option>
                                        <option value="$totalPrice">Grand Total</option>
                                    </optgroup>
                                </select>
                                <p class="text-[10px] text-gray-400 mb-0 font-medium leading-relaxed italic pr-2">
                                    <i class="ri-lightbulb-line text-amber-500"></i> Tip: Variables copy to clipboard in builder mode. Just paste them into text blocks!
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main Workspace -->
            <div class="flex-grow h-full bg-gray-50 dark:bg-black/40 relative">
                <!-- Builder View -->
                <div id="builder-container" class="h-full">
                    <div id="gjs" class="gjs-custom-editor"></div>
                </div>
                <!-- Source Code View -->
                <div id="code-container" class="h-full hidden">
                    <div class="p-6 h-full flex flex-col">
                        <div class="flex items-center justify-between mb-3 px-2">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest"><i class="ri-braces-line me-1"></i> HTML Engine</span>
                            <span class="text-[10px] text-primary bg-primary/10 px-2 py-0.5 rounded-full font-bold">LIVE EDITOR</span>
                        </div>
                        <div class="flex-grow rounded-2xl overflow-hidden border border-gray-800 shadow-2xl">
                             <textarea id="raw-html" class="w-full h-full font-mono text-[13px] p-6 bg-[#1a1a1a] text-[#80ffea] outline-none resize-none scrollbar-dark"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script>
    $(document).ready(function() {
        // Safe data injection
        const initialHtml = <?= json_encode($template['body_html'] ?? '') ?>;
        
        // Initialize GrapesJS
        const editor = grapesjs.init({
            container: '#gjs',
            fromElement: false,
            height: '100%',
            storageManager: false,
            plugins: ['gjs-preset-newsletter'],
            pluginsOpts: {
                'gjs-preset-newsletter': {}
            },
            // Force inline styles and avoid classes
            forceClass: false,
            avoidInlineStyle: false,
            canvas: {
                styles: [
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'
                ]
            }
        });

        // Load Content
        if (initialHtml) {
            editor.on('load', () => {
                const decodeHtml = (html) => {
                    const txt = document.createElement("textarea");
                    txt.innerHTML = html;
                    return txt.value;
                };
                const contentToSet = decodeHtml(initialHtml);
                editor.setComponents(contentToSet);
                
                // Allow GrapesJS to automatically inline styles from <style> blocks if present
                editor.refresh();
            });
        }

        // Custom Layout Blocks (Ensuring inline styles)
        const bm = editor.BlockManager;
        const blocks = [
            { id: 'header-section', label: 'Classic Header', category: 'Layouts', content: '<table style="width: 100%; background-color: #ffffff; padding: 25px 0; border-bottom: 2px solid #5a66f1;"><tr><td align="center"><h2 style="margin: 0; color: #1e293b; font-family: sans-serif; letter-spacing: 2px;">BRAND LOGO</h2></td></tr></table>' },
            { id: 'hero-modern', label: 'Premium Hero', category: 'Layouts', content: '<table style="width: 100%; background-color: #1e293b; padding: 60px 40px; color: #ffffff;"><tr><td align="center"><h1 style="font-size: 32px; margin-bottom: 15px;">Elite Experience Awaits</h1><p style="color: #94a3b8; font-size: 18px; line-height: 1.6;">Welcome to the next level of luxury transportation. We premium service for discerning clients.</p><a href="#" style="display: inline-block; padding: 15px 35px; background-color: #5a66f1; color: white; text-decoration: none; border-radius: 50px; font-weight: bold; margin-top: 30px;">Reserve Now</a></td></tr></table>' },
            { id: '1-col-text', label: 'Text Block', category: 'Standard', content: '<table style="width: 100%; padding: 40px;"><tr><td style="color: #475569; line-height: 1.8; font-family: sans-serif; font-size: 16px;">Elite professionals dedicated to your safety and comfort. Our chauffeurs are trained to the highest standards.</td></tr></table>' },
            { id: '2-col-grid', label: 'Side-by-Side', category: 'Standard', content: '<table style="width: 100%; padding: 40px;"><tr><td width="48%" style="padding: 20px; background: #f8fafc; border-radius: 12px;"><h3 style="color: #1e293b;">Pickup Info</h3><p style="color: #64748b;">$pickupLocation</p></td><td width="4%"></td><td width="48%" style="padding: 20px; background: #f8fafc; border-radius: 12px;"><h3 style="color: #1e293b;">Date/Time</h3><p style="color: #64748b;">$pickupDate</p></td></tr></table>' },
            { id: 'footer-brand', label: 'Pro Footer', category: 'Layouts', content: '<table style="width: 100%; background: #f1f5f9; padding: 40px; text-align: center; border-top: 1px solid #e2e8f0;"><tr><td><p style="color: #94a3b8; font-size: 12px;">© 2026 LimoCRM Global. All rights reserved.</p><div style="margin-top: 20px;"><a href="#" style="color: #5a66f1; font-size: 12px; margin: 0 10px;">Privacy Policy</a><a href="#" style="color: #5a66f1; font-size: 12px; margin: 0 10px;">Unsubscribe</a></div></td></tr></table>' }
        ];
        blocks.forEach(b => bm.add(b.id, b));

        // Interaction Logic
        $('.editor-toggle').on('click', function() {
            const view = $(this).data('view');
            $('.editor-toggle').removeClass('active bg-white dark:bg-primary shadow-sm text-primary dark:text-white').addClass('text-gray-500');
            $(this).addClass('active bg-white dark:bg-primary shadow-sm text-primary dark:text-white').removeClass('text-gray-500');

            if (view === 'code') {
                let html = '';
                try { 
                    // Use the newsletter plugin inliner
                    html = editor.runCommand('gjs-get-inlined-html'); 
                } catch (e) {
                    console.error("Inlining Error:", e);
                }
                
                if (!html) html = editor.getHtml(); // Fallback if inliner fails
                
                $('#raw-html').val(html);
                $('#builder-container').addClass('hidden');
                $('#code-container').removeClass('hidden');
            } else {
                editor.setComponents($('#raw-html').val());
                $('#code-container').addClass('hidden');
                $('#builder-container').removeClass('hidden');
                editor.refresh();
            }
        });

        $('#variable-selector').on('change', function() {
            const val = $(this).val();
            if (!val) return;
            const view = $('.editor-toggle.active').data('view');
            if (view === 'code') {
                const el = $('#raw-html')[0];
                const start = el.selectionStart;
                el.value = el.value.substring(0, start) + val + el.value.substring(el.selectionEnd);
                el.focus();
                el.setSelectionRange(start + val.length, start + val.length);
            } else {
                navigator.clipboard.writeText(val);
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Token copied!', showConfirmButton: false, timer: 1500 });
            }
            $(this).val('');
        });

        $('#save-template-btn').on('click', function() {
            if (!$('#name').val() || !$('#subject').val()) {
                Swal.fire('Required Fields', 'Please enter a name and subject.', 'warning');
                return;
            }
            const btn = $(this);
            const origHtml = btn.html();
            btn.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin me-2"></i>Finalizing...');

            let finalHtml = '';
            const view = $('.editor-toggle.active').data('view');
            
            if (view === 'code') {
                finalHtml = $('#raw-html').val();
            } else {
                try {
                    // Force inlining on save
                    finalHtml = editor.runCommand('gjs-get-inlined-html');
                } catch (e) {
                    console.error("Save Inlining Error:", e);
                }
                
                if (!finalHtml) finalHtml = editor.getHtml();
            }

            const formData = new FormData($('#template-info-form')[0]);
            formData.append('action', 'save_email_template');
            formData.append('body_html', finalHtml);
            formData.append('created_by', '<?php echo $_SESSION["user"]["id"]; ?>');

            $.ajax({
                url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp) {
                    const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Secured!', text: 'Template saved with inline styles.', timer: 1500, showConfirmButton: false }).then(() => { window.location.href = 'email_templates.php'; });
                    } else {
                        btn.prop('disabled', false).html(origHtml);
                        Swal.fire('Failed', data.message, 'error');
                    }
                }
            });
        });
    });
</script>

<style>
    .editor-toggle.active {
        color: #5a66f1;
        font-weight: 800;
    }
    .dark .editor-toggle.active {
        background-color: #5a66f1;
        color: #fff;
    }
    .gjs-cv-canvas { top: 0 !important; height: 100% !important; }
    .gjs-one-bg { background-color: #f8fafc; }
    .scrollbar-thin::-webkit-scrollbar { width: 4px; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .scrollbar-dark::-webkit-scrollbar { width: 8px; }
    .scrollbar-dark::-webkit-scrollbar-track { background: #1a1a1a; }
    .scrollbar-dark::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
    
    /* GrapesJS Theming */
    .gjs-pn-panels { background-color: #f8fafc !important; }
    .gjs-two-color { color: #5a66f1 !important; }
    .gjs-three-color { color: #cbd5e1 !important; }
    .gjs-four-color { color: #fff !important; background-color: #5a66f1 !important; }
</style>
