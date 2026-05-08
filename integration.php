<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$widgetUserId = isset($_SESSION['user']['id']) ? (string) $_SESSION['user']['id'] : '';
$widgetUserIdAttr = htmlspecialchars($widgetUserId, ENT_QUOTES, 'UTF-8');
$widgetHeightPreview = '620';
$widgetHeightEmbed = '800px';

$embedSnippet = '<div id="limogen-widget"
     data-user-id="' . $widgetUserId . '"
     data-width="100%"
     data-height="' . $widgetHeightEmbed . '">
</div>

<script src="https://zabrin.xyz/limogen-widget/widget.js" async></script>';
$embedSnippetEscaped = htmlspecialchars($embedSnippet, ENT_QUOTES, 'UTF-8');
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2 py-4">
            <div>
                <h1 class="page-title font-bold text-2xl mb-1 text-gray-800 dark:text-gray-100">Website widget</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Preview your booking form and copy the embed code for your site.</p>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6 pb-12">
            <div class="xl:col-span-7 col-span-12">
                <div class="box bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl mb-6 overflow-hidden shadow-sm sticky " style="top: 4rem;">
                    <div class="box-header bg-gray-50/50 dark:bg-black/10 px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                            <i class="ri-eye-line text-xl"></i>
                        </span>
                        <div>
                            <h5 class="box-title font-bold text-gray-700 dark:text-gray-300 mb-0">Live preview</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-0 mt-0.5">How the widget appears to visitors</p>
                        </div>
                    </div>
                    <div class="box-body p-6">
                        <?php if ($widgetUserId === ''): ?>
                            <p class="text-sm text-amber-600 dark:text-amber-400 mb-0">No user session found. Please log in again.</p>
                        <?php else: ?>
                            <div id="limogen-widget"
                                 data-user-id="<?php echo $widgetUserIdAttr; ?>"
                                 data-width="100%"
                                 data-height="<?php echo htmlspecialchars($widgetHeightPreview, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <script src="https://zabrin.xyz/limogen-widget/widget.js" async></script>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-5 col-span-12">
                <div class="box bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl mb-6 overflow-hidden shadow-sm sticky " style="top: 4rem;">
                    <div class="box-header bg-gray-50/50 dark:bg-black/10 px-6 py-4 border-b border-gray-200 dark:border-white/10">
                        <div class="flex items-start justify-between gap-3 flex-wrap">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                    <i class="ri-code-s-slash-line text-xl"></i>
                                </span>
                                <div class="min-w-0">
                                    <h5 class="box-title font-bold text-gray-700 dark:text-gray-300 mb-0">Embed code</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0 mt-0.5">Paste before <code class="text-[11px]">&lt;/body&gt;</code> on your website</p>
                                </div>
                            </div>
                            <button type="button" class="ti-btn ti-btn-sm ti-btn-primary !rounded-xl font-semibold shrink-0" data-copy-target="widgetCode" id="widget-copy-btn">
                                <i class="ri-file-copy-line me-1"></i> Copy code
                            </button>
                        </div>
                    </div>
                    <div class="box-body p-6 space-y-4">
                        <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-black/20 p-4">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Linked account</p>
                            <p class="text-sm font-mono text-gray-800 dark:text-gray-200 break-all mb-0">
                                <?php echo $widgetUserId !== '' ? $widgetUserIdAttr : '—'; ?>
                            </p>
                        </div>

                        <?php if ($widgetUserId === ''): ?>
                            <p class="text-sm text-gray-500 mb-0">Log in to generate embed code.</p>
                        <?php else: ?>
                            <pre class="crm-code-box m-0 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 p-4 overflow-x-auto text-left" id="widgetCode"><code class="text-xs leading-relaxed font-mono text-gray-800 dark:text-gray-800 whitespace-pre"><?php echo $embedSnippetEscaped; ?></code></pre>
                            <p class="crm-copy-status text-xs text-emerald-600 dark:text-emerald-400 min-h-[1.25rem] mb-0"></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("click", function (e) {
    const btn = e.target.closest("[data-copy-target]");
    if (!btn) return;

    const target = btn.getAttribute("data-copy-target");
    const codeBox = document.getElementById(target);
    if (!codeBox) return;

    const text = codeBox.innerText;
    const labelHtml = btn.innerHTML;

    const done = () => {
        btn.innerHTML = '<i class="ri-check-line me-1"></i> Copied';
        const status = btn.closest(".box") && btn.closest(".box").querySelector(".crm-copy-status");
        if (status) status.innerText = "Copied to clipboard.";
        setTimeout(function () {
            btn.innerHTML = labelHtml;
            if (status) status.innerText = "";
        }, 1800);
    };

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(done).catch(function () {
            const ta = document.createElement("textarea");
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand("copy"); done(); } catch (err) {}
            document.body.removeChild(ta);
        });
    } else {
        const ta = document.createElement("textarea");
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand("copy"); done(); } catch (err) {}
        document.body.removeChild(ta);
    }
});
</script>

<?php include_once "components/layout/footer.php"; ?>
