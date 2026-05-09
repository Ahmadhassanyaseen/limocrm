<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$templateId = $_GET['id'] ?? '';
$template = null;
if (!empty($templateId)) {
    $response = getEmailTemplate(['id' => $templateId]);
    if ($response['success']) { $template = $response['template']; }
}
$isEdit = !empty($template);
?>

<link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
<script src="https://unpkg.com/grapesjs"></script>
<script src="https://unpkg.com/grapesjs-preset-newsletter"></script>

<style>
  :root { --eb-surface: #ffffff; --eb-surface-2: #f8fafc; --eb-surface-3: #f1f5f9; --eb-border: rgba(15,23,42,0.08); --eb-text: #0f172a; --eb-muted: rgba(15,23,42,0.55); --eb-accent: rgb(var(--primary-rgb)); }
  .dark { --eb-surface: rgba(255,255,255,0.035); --eb-surface-2: rgba(255,255,255,0.05); --eb-surface-3: rgba(255,255,255,0.03); --eb-border: rgba(255,255,255,0.08); --eb-text: rgba(255,255,255,0.92); --eb-muted: rgba(255,255,255,0.50); }

  /* Header */
  .eb-header { position: sticky; top: 0; z-index: 100; background: var(--eb-surface); border-bottom: 1px solid var(--eb-border); padding: 10px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; backdrop-filter: blur(12px); transition: box-shadow 0.2s; }
  .eb-header.scrolled { box-shadow: 0 4px 20px rgba(15,23,42,0.06); }
  .dark .eb-header.scrolled { box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
  .eb-back { width: 36px; height: 36px; border-radius: 10px; border: 1px solid var(--eb-border); background: transparent; display: flex; align-items: center; justify-content: center; color: var(--eb-muted); font-size: 17px; transition: all 0.15s; text-decoration: none; }
  .eb-back:hover { border-color: rgba(var(--primary-rgb),0.3); color: var(--eb-accent); background: rgba(var(--primary-rgb),0.04); }

  .eb-mode-switch { display: flex; border: 1px solid var(--eb-border); border-radius: 10px; overflow: hidden; background: var(--eb-surface-2); }
  .eb-mode-btn { padding: 6px 14px; font-size: 11px; font-weight: 700; border: none; background: transparent; color: var(--eb-muted); cursor: pointer; transition: all 0.15s; display: flex; align-items: center; gap: 4px; }
  .eb-mode-btn.active { background: var(--eb-surface); color: var(--eb-accent); box-shadow: 0 1px 3px rgba(15,23,42,0.06); }
  .eb-mode-btn:not(.active):hover { color: var(--eb-text); }

  .eb-save-btn { height: 36px; border-radius: 10px; border: none; background: var(--eb-accent); color: #fff; padding: 0 18px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 5px; }
  .eb-save-btn:hover { box-shadow: 0 4px 14px rgba(var(--primary-rgb),0.3); transform: translateY(-1px); }
  .eb-save-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }

  /* Sidebar */
  .eb-sidebar { width: 340px; min-width: 340px; background: var(--eb-surface); border-right: 1px solid var(--eb-border); display: flex; flex-direction: column; overflow: hidden; }
  .eb-sidebar-tabs { display: flex; border-bottom: 1px solid var(--eb-border); background: var(--eb-surface-2); flex-shrink: 0; }
  .eb-tab { flex: 1; padding: 12px 0; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--eb-muted); background: transparent; border: none; cursor: pointer; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 5px; position: relative; }
  .eb-tab.active { color: var(--eb-accent); background: var(--eb-surface); }
  .eb-tab.active::after { content: ''; position: absolute; bottom: -1px; left: 0; right: 0; height: 2px; background: var(--eb-accent); }
  .eb-tab:not(.active):hover { color: var(--eb-text); }

  .eb-tab-content { flex: 1; overflow-y: auto; }
  .eb-tab-content::-webkit-scrollbar { width: 4px; }
  .eb-tab-content::-webkit-scrollbar-thumb { background: var(--eb-border); border-radius: 4px; }
  .eb-tab-panel { display: none; padding: 20px; }
  .eb-tab-panel.active { display: block; }

  /* Settings Panel */
  .eb-section { margin-bottom: 20px; }
  .eb-section-title { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(var(--primary-rgb),0.5); margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
  .eb-section-title i { font-size: 13px; }
  .eb-field { margin-bottom: 12px; }
  .eb-label { font-size: 11px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; color: var(--eb-muted); margin-bottom: 5px; display: flex; align-items: center; gap: 4px; }
  .eb-label .eb-req { color: #ef4444; font-size: 13px; line-height: 1; }
  .eb-input-wrap { position: relative; }
  .eb-input-wrap .eb-icon { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); font-size: 14px; color: var(--eb-muted); pointer-events: none; transition: color 0.2s; z-index: 1; }
  .eb-input { height: 38px; border-radius: 10px; border: 1px solid var(--eb-border); background: var(--eb-surface-2); color: var(--eb-text); padding: 0 12px 0 34px; width: 100%; font-size: 12.5px; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
  .eb-input:focus { border-color: var(--eb-accent); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.08); }
  .eb-input:focus ~ .eb-icon { color: var(--eb-accent); }
  .eb-input.is-invalid { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,0.08) !important; }
  .eb-input.is-invalid ~ .eb-icon { color: #ef4444 !important; }
  .eb-input.is-valid { border-color: #22c55e !important; }
  .eb-input.is-valid ~ .eb-icon { color: #22c55e !important; }
  textarea.eb-input { height: auto; min-height: 60px; padding: 10px 12px 10px 34px; resize: vertical; }
  select.eb-input { cursor: pointer; -webkit-appearance: none; appearance: none; padding-right: 32px; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M8 11.4 2.6 6h10.8L8 11.4z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; }
  .eb-error { font-size: 10px; color: #ef4444; margin-top: 3px; display: none; font-weight: 600; }
  .eb-error.show { display: block; }

  .eb-info-card { background: rgba(var(--primary-rgb),0.04); border: 1px solid rgba(var(--primary-rgb),0.1); border-radius: 12px; padding: 14px; }

  /* Widgets Panel - Elementor Style */
  .eb-widget-search { padding: 0 0 14px; }
  .eb-widget-search input { height: 36px; border-radius: 10px; border: 1px solid var(--eb-border); background: var(--eb-surface-2); color: var(--eb-text); padding: 0 12px 0 34px; width: 100%; font-size: 12px; outline: none; transition: border-color 0.2s; }
  .eb-widget-search input:focus { border-color: var(--eb-accent); }
  .eb-widget-search i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); font-size: 14px; color: var(--eb-muted); pointer-events: none; }

  .eb-widget-cat { margin-bottom: 16px; }
  .eb-widget-cat-header { display: flex; align-items: center; justify-content: space-between; padding: 6px 0; cursor: pointer; user-select: none; }
  .eb-widget-cat-header span { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--eb-muted); }
  .eb-widget-cat-header i { font-size: 14px; color: var(--eb-muted); transition: transform 0.2s; }
  .eb-widget-cat.collapsed .eb-widget-cat-header i { transform: rotate(-90deg); }
  .eb-widget-cat.collapsed .eb-widget-grid { display: none; }

  .eb-widget-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 8px; }
  .eb-widget { border: 1px solid var(--eb-border); border-radius: 10px; padding: 12px 8px; text-align: center; cursor: grab; transition: all 0.15s; background: var(--eb-surface); }
  .eb-widget:hover { border-color: rgba(var(--primary-rgb),0.3); background: rgba(var(--primary-rgb),0.03); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(15,23,42,0.06); }
  .dark .eb-widget:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
  .eb-widget:active { cursor: grabbing; transform: scale(0.97); }
  .eb-widget .eb-widget-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; margin: 0 auto 6px; }
  .eb-widget .eb-widget-name { font-size: 10px; font-weight: 700; color: var(--eb-text); line-height: 1.3; }

  /* Variable tokens */
  .eb-var-list { display: flex; flex-wrap: wrap; gap: 6px; }
  .eb-var-token { font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 6px; background: var(--eb-surface-2); border: 1px solid var(--eb-border); color: var(--eb-muted); cursor: pointer; transition: all 0.15s; font-family: monospace; }
  .eb-var-token:hover { background: rgba(var(--primary-rgb),0.06); color: var(--eb-accent); border-color: rgba(var(--primary-rgb),0.2); }

  /* Workspace */
  .eb-workspace { flex: 1; background: var(--eb-surface-3); position: relative; }
  .eb-code-view { height: 100%; padding: 20px; display: flex; flex-direction: column; }
  .eb-code-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
  .eb-code-badge { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; background: rgba(var(--primary-rgb),0.08); color: var(--eb-accent); padding: 3px 10px; border-radius: 6px; }
  .eb-code-area { flex: 1; border-radius: 14px; overflow: hidden; border: 1px solid rgba(255,255,255,0.06); }
  .eb-code-area textarea { width: 100%; height: 100%; font-family: 'JetBrains Mono', 'Fira Code', monospace; font-size: 13px; line-height: 1.7; padding: 20px; background: #0f172a; color: #7dd3fc; border: none; outline: none; resize: none; }
  .eb-code-area textarea::-webkit-scrollbar { width: 6px; }
  .eb-code-area textarea::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

  .eb-status-bar { padding: 6px 16px; background: var(--eb-surface); border-top: 1px solid var(--eb-border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
  .eb-status { font-size: 10px; font-weight: 600; color: var(--eb-muted); display: flex; align-items: center; gap: 4px; }
  .eb-dot { width: 6px; height: 6px; border-radius: 50%; background: #22c55e; display: inline-block; }
  .eb-dot-amber { background: #f59e0b; }

  /* GrapesJS Overrides */
  .gjs-cv-canvas { top: 48px !important; height: calc(100% - 48px) !important; }
  .gjs-one-bg { background-color: var(--eb-surface-3) !important; }
  .gjs-pn-panels { background-color: var(--eb-surface-3) !important; }
  .gjs-two-color { color: var(--eb-accent) !important; }
  .gjs-four-color { color: #fff !important; background-color: var(--eb-accent) !important; }
  .gjs-pn-devices, .gjs-pn-panel.gjs-pn-devices-c, .gjs-pn-devices-c { display: none !important; }
  .gjs-block { border-radius: 10px !important; border: 1px solid var(--eb-border) !important; box-shadow: none !important; }
  .gjs-block:hover { border-color: rgba(var(--primary-rgb),0.3) !important; }
</style>

<div class="main-content app-content">
  <div class="container-fluid !p-0">

    <!-- Header -->
    <div class="eb-header" id="eb-header">
      <div class="flex items-center gap-3">
        <a href="email_templates.php" class="eb-back" title="Back"><i class="ri-arrow-left-line"></i></a>
        <div>
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-primary/10 flex items-center justify-center"><i class="ri-mail-send-line text-primary" style="font-size:12px;"></i></div>
            <h1 style="font-size:14px;font-weight:700;color:var(--eb-text);margin:0;"><?php echo $isEdit ? 'Edit Template' : 'Create Template'; ?></h1>
            <?php if ($isEdit): ?>
              <span style="font-size:9px;font-weight:700;padding:2px 8px;border-radius:6px;background:rgba(34,197,94,0.08);color:#16a34a;text-transform:uppercase;letter-spacing:0.04em;">Editing</span>
            <?php else: ?>
              <span style="font-size:9px;font-weight:700;padding:2px 8px;border-radius:6px;background:rgba(59,130,246,0.08);color:#2563eb;text-transform:uppercase;letter-spacing:0.04em;">New</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <div class="eb-mode-switch">
          <button type="button" class="eb-mode-btn active" data-view="builder"><i class="ri-layout-grid-line"></i> Builder</button>
          <button type="button" class="eb-mode-btn" data-view="code"><i class="ri-code-s-slash-line"></i> Source</button>
        </div>
        <div style="width:1px;height:24px;background:var(--eb-border);"></div>
        <button type="button" class="eb-save-btn" id="save-template-btn"><i class="ri-save-line"></i> Save</button>
      </div>
    </div>

    <!-- Main Layout -->
    <div class="flex overflow-hidden" style="height:calc(100vh - 75px);">

      <!-- Sidebar -->
      <div class="eb-sidebar">
        <!-- Tabs -->
        <div class="eb-sidebar-tabs">
          <button class="eb-tab active" data-tab="widgets" onclick="switchTab('widgets',this)"><i class="ri-apps-2-line"></i> Widgets</button>
          <button class="eb-tab" data-tab="settings" onclick="switchTab('settings',this)"><i class="ri-settings-3-line"></i> Settings</button>
          <button class="eb-tab" data-tab="variables" onclick="switchTab('variables',this)"><i class="ri-braces-line"></i> Variables</button>
        </div>

        <div class="eb-tab-content">
          <!-- Widgets Panel -->
          <div class="eb-tab-panel active" id="panel-widgets">
            <div class="eb-widget-search" style="position:relative;">
              <i class="ri-search-line"></i>
              <input type="text" id="widget-search" placeholder="Search widgets...">
            </div>

            <div class="eb-widget-cat" data-cat="layout">
              <div class="eb-widget-cat-header" onclick="toggleCat(this)">
                <span><i class="ri-layout-line me-1"></i> Layout</span>
                <i class="ri-arrow-down-s-line"></i>
              </div>
              <div class="eb-widget-grid">
                <div class="eb-widget" draggable="true" data-block="header-section" onclick="insertBlock('header-section')">
                  <div class="eb-widget-icon" style="background:rgba(59,130,246,0.08);color:#3b82f6;"><i class="ri-layout-top-line"></i></div>
                  <div class="eb-widget-name">Header</div>
                </div>
                <div class="eb-widget" draggable="true" data-block="hero-modern" onclick="insertBlock('hero-modern')">
                  <div class="eb-widget-icon" style="background:rgba(139,92,246,0.08);color:#8b5cf6;"><i class="ri-star-line"></i></div>
                  <div class="eb-widget-name">Hero Banner</div>
                </div>
                <div class="eb-widget" draggable="true" data-block="footer-brand" onclick="insertBlock('footer-brand')">
                  <div class="eb-widget-icon" style="background:rgba(245,158,11,0.08);color:#f59e0b;"><i class="ri-layout-bottom-line"></i></div>
                  <div class="eb-widget-name">Footer</div>
                </div>
                <div class="eb-widget" draggable="true" data-block="divider-block" onclick="insertBlock('divider-block')">
                  <div class="eb-widget-icon" style="background:rgba(100,116,139,0.08);color:#64748b;"><i class="ri-separator"></i></div>
                  <div class="eb-widget-name">Divider</div>
                </div>
              </div>
            </div>

            <div class="eb-widget-cat" data-cat="content">
              <div class="eb-widget-cat-header" onclick="toggleCat(this)">
                <span><i class="ri-file-text-line me-1"></i> Content</span>
                <i class="ri-arrow-down-s-line"></i>
              </div>
              <div class="eb-widget-grid">
                <div class="eb-widget" draggable="true" data-block="1-col-text" onclick="insertBlock('1-col-text')">
                  <div class="eb-widget-icon" style="background:rgba(34,197,94,0.08);color:#22c55e;"><i class="ri-text"></i></div>
                  <div class="eb-widget-name">Text Block</div>
                </div>
                <div class="eb-widget" draggable="true" data-block="2-col-grid" onclick="insertBlock('2-col-grid')">
                  <div class="eb-widget-icon" style="background:rgba(6,182,212,0.08);color:#06b6d4;"><i class="ri-layout-column-line"></i></div>
                  <div class="eb-widget-name">Two Column</div>
                </div>
                <div class="eb-widget" draggable="true" data-block="image-block" onclick="insertBlock('image-block')">
                  <div class="eb-widget-icon" style="background:rgba(236,72,153,0.08);color:#ec4899;"><i class="ri-image-line"></i></div>
                  <div class="eb-widget-name">Image</div>
                </div>
                <div class="eb-widget" draggable="true" data-block="button-block" onclick="insertBlock('button-block')">
                  <div class="eb-widget-icon" style="background:rgba(249,115,22,0.08);color:#f97316;"><i class="ri-cursor-line"></i></div>
                  <div class="eb-widget-name">Button</div>
                </div>
                <div class="eb-widget" draggable="true" data-block="spacer-block" onclick="insertBlock('spacer-block')">
                  <div class="eb-widget-icon" style="background:rgba(148,163,184,0.08);color:#94a3b8;"><i class="ri-space"></i></div>
                  <div class="eb-widget-name">Spacer</div>
                </div>
                <div class="eb-widget" draggable="true" data-block="quote-block" onclick="insertBlock('quote-block')">
                  <div class="eb-widget-icon" style="background:rgba(168,85,247,0.08);color:#a855f7;"><i class="ri-double-quotes-l"></i></div>
                  <div class="eb-widget-name">Quote</div>
                </div>
              </div>
            </div>

            <div class="eb-widget-cat" data-cat="limo">
              <div class="eb-widget-cat-header" onclick="toggleCat(this)">
                <span><i class="ri-car-line me-1"></i> LimoCRM</span>
                <i class="ri-arrow-down-s-line"></i>
              </div>
              <div class="eb-widget-grid">
                <div class="eb-widget" draggable="true" data-block="pricing-table" onclick="insertBlock('pricing-table')">
                  <div class="eb-widget-icon" style="background:rgba(var(--primary-rgb),0.08);color:var(--eb-accent);"><i class="ri-money-dollar-circle-line"></i></div>
                  <div class="eb-widget-name">Price Table</div>
                </div>
                <div class="eb-widget" draggable="true" data-block="trip-details" onclick="insertBlock('trip-details')">
                  <div class="eb-widget-icon" style="background:rgba(14,165,233,0.08);color:#0ea5e9;"><i class="ri-road-map-line"></i></div>
                  <div class="eb-widget-name">Trip Details</div>
                </div>
                <div class="eb-widget" draggable="true" data-block="client-info" onclick="insertBlock('client-info')">
                  <div class="eb-widget-icon" style="background:rgba(34,197,94,0.08);color:#22c55e;"><i class="ri-user-line"></i></div>
                  <div class="eb-widget-name">Client Info</div>
                </div>
                <div class="eb-widget" draggable="true" data-block="vehicle-card" onclick="insertBlock('vehicle-card')">
                  <div class="eb-widget-icon" style="background:rgba(245,158,11,0.08);color:#f59e0b;"><i class="ri-car-line"></i></div>
                  <div class="eb-widget-name">Vehicle Card</div>
                </div>
                <div class="eb-widget" draggable="true" data-block="tracking-pixel" onclick="insertBlock('tracking-pixel')">
                  <div class="eb-widget-icon" style="background:rgba(16,185,129,0.08);color:#10b981;"><i class="ri-eye-line"></i></div>
                  <div class="eb-widget-name">Email Tracking</div>
                </div>
                <div class="eb-widget" draggable="true" data-block="unsubscribe-block" onclick="insertBlock('unsubscribe-block')">
                  <div class="eb-widget-icon" style="background:rgba(239,68,68,0.08);color:#ef4444;"><i class="ri-mail-close-line"></i></div>
                  <div class="eb-widget-name">Unsubscribe</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Settings Panel -->
          <div class="eb-tab-panel" id="panel-settings">
            <form id="template-info-form">
              <input type="hidden" id="template-id" name="id" value="<?php echo $template['id'] ?? ''; ?>">

              <div class="eb-section">
                <div class="eb-section-title"><i class="ri-information-line"></i> Identity</div>
                <div class="eb-field">
                  <label class="eb-label">Template Name <span class="eb-req">*</span></label>
                  <div class="eb-input-wrap">
                    <input type="text" class="eb-input" id="name" name="name" placeholder="e.g. Booking Confirmation" value="<?php echo htmlspecialchars($template['name'] ?? ''); ?>" maxlength="80">
                    <i class="ri-text eb-icon"></i>
                  </div>
                  <div class="eb-error" id="err-name"></div>
                </div>
                <div class="eb-field">
                  <label class="eb-label">Category</label>
                  <div class="eb-input-wrap">
                    <select class="eb-input" id="type" name="type">
                      <option value="email" <?php echo ($template['type'] ?? '') == 'email' ? 'selected' : ''; ?>>Standard Email</option>
                      <option value="campaign" <?php echo ($template['type'] ?? '') == 'campaign' ? 'selected' : ''; ?>>Campaign</option>
                      <option value="support" <?php echo ($template['type'] ?? '') == 'support' ? 'selected' : ''; ?>>Support</option>
                    </select>
                    <i class="ri-folder-line eb-icon"></i>
                  </div>
                </div>
              </div>

              <div class="eb-info-card">
                <div class="eb-section-title" style="margin-bottom:8px;"><i class="ri-mail-send-line"></i> Delivery</div>
                <div class="eb-field" style="margin-bottom:8px;">
                  <label class="eb-label">Subject Line <span class="eb-req">*</span></label>
                  <div class="eb-input-wrap">
                    <input type="text" class="eb-input" id="subject" name="subject" placeholder="Hello, $first_name!" value="<?php echo htmlspecialchars($template['subject'] ?? ''); ?>" style="background:var(--eb-surface);">
                    <i class="ri-chat-1-line eb-icon"></i>
                  </div>
                  <div class="eb-error" id="err-subject"></div>
                </div>
                <div class="eb-field" style="margin-bottom:0;">
                  <label class="eb-label">Internal Note</label>
                  <div class="eb-input-wrap">
                    <textarea class="eb-input" id="description" name="description" rows="2" placeholder="Who is this for?" style="background:var(--eb-surface);"><?php echo htmlspecialchars($template['description'] ?? ''); ?></textarea>
                    <i class="ri-file-text-line eb-icon" style="top:12px;transform:none;"></i>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <!-- Variables Panel -->
          <div class="eb-tab-panel" id="panel-variables">
            <div class="eb-section">
              <div class="eb-section-title"><i class="ri-user-line"></i> Client</div>
              <div class="eb-var-list">
                <span class="eb-var-token" onclick="copyVar('$first_name')">$first_name</span>
                <span class="eb-var-token" onclick="copyVar('$last_name')">$last_name</span>
                <span class="eb-var-token" onclick="copyVar('$email')">$email</span>
                <span class="eb-var-token" onclick="copyVar('$phone')">$phone</span>
              </div>
            </div>
            <div class="eb-section">
              <div class="eb-section-title"><i class="ri-road-map-line"></i> Trip</div>
              <div class="eb-var-list">
                <span class="eb-var-token" onclick="copyVar('$pickup_location')">$pickup_location</span>
                <span class="eb-var-token" onclick="copyVar('$dropoff_location')">$dropoff_location</span>
                <span class="eb-var-token" onclick="copyVar('$event_date')">$event_date</span>
                <span class="eb-var-token" onclick="copyVar('$passengers')">$passengers</span>
                <span class="eb-var-token" onclick="copyVar('$service_type')">$service_type</span>
                <span class="eb-var-token" onclick="copyVar('$distance')">$distance</span>
                <span class="eb-var-token" onclick="copyVar('$duration')">$duration</span>
              </div>
            </div>
            <div class="eb-section">
              <div class="eb-section-title"><i class="ri-money-dollar-circle-line"></i> Pricing</div>
              <div class="eb-var-list">
                <span class="eb-var-token" onclick="copyVar('$total_price')">$total_price</span>
                <span class="eb-var-token" onclick="copyVar('$quoted_price')">$quoted_price</span>
                <span class="eb-var-token" onclick="copyVar('$fuel_surcharge')">$fuel_surcharge</span>
                <span class="eb-var-token" onclick="copyVar('$service_length')">$service_length</span>
              </div>
            </div>
            <div class="eb-section">
              <div class="eb-section-title"><i class="ri-car-line"></i> Vehicle</div>
              <div class="eb-var-list">
                <span class="eb-var-token" onclick="copyVar('$vehicle_name')">$vehicle_name</span>
                <span class="eb-var-token" onclick="copyVar('$vehicle_type')">$vehicle_type</span>
                <span class="eb-var-token" onclick="copyVar('$vehicle_image')">$vehicle_image</span>
              </div>
            </div>
            <div class="eb-section">
              <div class="eb-section-title"><i class="ri-radar-line"></i> Tracking</div>
              <div class="eb-var-list">
                <span class="eb-var-token" onclick="copyVar('$tracking_pixel_url')">$tracking_pixel_url</span>
                <span class="eb-var-token" onclick="copyVar('$unsubscribe_url')">$unsubscribe_url</span>
              </div>
            </div>
            <div class="eb-section">
              <div class="eb-section-title"><i class="ri-calendar-line"></i> System</div>
              <div class="eb-var-list">
                <span class="eb-var-token" onclick="copyVar('$date_created')">$date_created</span>
                <span class="eb-var-token" onclick="copyVar('$company_name')">$company_name</span>
                <span class="eb-var-token" onclick="copyVar('$company_logo')">$company_logo</span>
              </div>
            </div>
            <div style="margin-top:12px;padding:10px 12px;border-radius:10px;background:rgba(59,130,246,0.06);border:1px solid rgba(59,130,246,0.1);">
              <div style="font-size:10px;font-weight:700;color:#3b82f6;margin-bottom:2px;">Tip</div>
              <div style="font-size:11px;color:var(--eb-muted);line-height:1.5;">Click any token to copy it. Paste into your email template where needed.</div>
            </div>
          </div>
        </div>

        <!-- Status Bar -->
        <div class="eb-status-bar">
          <div class="eb-status"><span class="eb-dot"></span> Ready</div>
          <div class="eb-status" id="eb-char-count">0 blocks</div>
        </div>
      </div>

      <!-- Workspace -->
      <div class="eb-workspace">
        <div id="builder-container" class="h-full">
          <div id="gjs" class="gjs-custom-editor"></div>
        </div>
        <div id="code-container" class="h-full hidden">
          <div class="eb-code-view">
            <div class="eb-code-header">
              <span style="font-size:11px;font-weight:700;color:var(--eb-muted);text-transform:uppercase;letter-spacing:0.08em;"><i class="ri-braces-line me-1"></i> HTML Source</span>
              <span class="eb-code-badge">Live Editor</span>
            </div>
            <div class="eb-code-area">
              <textarea id="raw-html"></textarea>
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
var gjsEditor;

function switchTab(tab, btn) {
  document.querySelectorAll('.eb-tab').forEach(function(t) { t.classList.remove('active'); });
  document.querySelectorAll('.eb-tab-panel').forEach(function(p) { p.classList.remove('active'); });
  btn.classList.add('active');
  document.getElementById('panel-' + tab).classList.add('active');
}

function toggleCat(header) {
  header.parentElement.classList.toggle('collapsed');
}

function copyVar(token) {
  navigator.clipboard.writeText(token);
  Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Copied: ' + token, showConfirmButton: false, timer: 1200 });
}

function insertBlock(blockId) {
  if (!gjsEditor) return;
  var block = gjsEditor.BlockManager.get(blockId);
  if (block) {
    var content = block.get('content');
    gjsEditor.addComponents(content);
    updateBlockCount();
  }
}

function updateBlockCount() {
  if (!gjsEditor) return;
  var count = gjsEditor.getWrapper().find('*').length;
  document.getElementById('eb-char-count').textContent = count + ' elements';
}

$(document).ready(function() {
  var initialHtml = <?= json_encode($template['body_html'] ?? '') ?>;

  gjsEditor = grapesjs.init({
    container: '#gjs',
    fromElement: false,
    height: '100%',
    storageManager: false,
    deviceManager: { devices: [] },
    plugins: ['gjs-preset-newsletter'],
    pluginsOpts: { 'gjs-preset-newsletter': {} },
    forceClass: false,
    avoidInlineStyle: false,
    canvas: { styles: ['https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'] }
  });

  if (initialHtml) {
    gjsEditor.on('load', function() {
      var txt = document.createElement('textarea');
      txt.innerHTML = initialHtml;
      gjsEditor.setComponents(txt.value);
      gjsEditor.refresh();
      updateBlockCount();
    });
  }

  var bm = gjsEditor.BlockManager;
  var allBlocks = [
    { id: 'header-section', label: 'Header', category: 'Layout', content: '<table style="width:100%;background:#ffffff;padding:25px 0;border-bottom:2px solid #5a66f1;"><tr><td align="center"><h2 style="margin:0;color:#1e293b;font-family:sans-serif;letter-spacing:2px;">BRAND LOGO</h2></td></tr></table>' },
    { id: 'hero-modern', label: 'Hero Banner', category: 'Layout', content: '<table style="width:100%;background:#1e293b;padding:60px 40px;color:#fff;"><tr><td align="center"><h1 style="font-size:32px;margin-bottom:15px;">Elite Experience Awaits</h1><p style="color:#94a3b8;font-size:18px;line-height:1.6;">Luxury transportation at its finest.</p><a href="#" style="display:inline-block;padding:15px 35px;background:#5a66f1;color:#fff;text-decoration:none;border-radius:50px;font-weight:bold;margin-top:30px;">Reserve Now</a></td></tr></table>' },
    { id: 'footer-brand', label: 'Footer', category: 'Layout', content: '<table style="width:100%;background:#f1f5f9;padding:40px;text-align:center;border-top:1px solid #e2e8f0;"><tr><td><p style="color:#94a3b8;font-size:12px;">&copy; 2026 LimoCRM. All rights reserved.</p><div style="margin-top:20px;"><a href="#" style="color:#5a66f1;font-size:12px;margin:0 10px;">Privacy</a><a href="#" style="color:#5a66f1;font-size:12px;margin:0 10px;">Unsubscribe</a></div></td></tr></table>' },
    { id: 'divider-block', label: 'Divider', category: 'Layout', content: '<table style="width:100%;padding:10px 40px;"><tr><td><hr style="border:none;border-top:1px solid #e2e8f0;margin:0;"></td></tr></table>' },
    { id: '1-col-text', label: 'Text Block', category: 'Content', content: '<table style="width:100%;padding:30px 40px;"><tr><td style="color:#475569;line-height:1.8;font-family:sans-serif;font-size:16px;">Your text content goes here. Edit this block to add your message.</td></tr></table>' },
    { id: '2-col-grid', label: 'Two Column', category: 'Content', content: '<table style="width:100%;padding:30px 40px;"><tr><td width="48%" style="padding:20px;background:#f8fafc;border-radius:12px;"><h3 style="color:#1e293b;margin-top:0;">Column 1</h3><p style="color:#64748b;">Content here</p></td><td width="4%"></td><td width="48%" style="padding:20px;background:#f8fafc;border-radius:12px;"><h3 style="color:#1e293b;margin-top:0;">Column 2</h3><p style="color:#64748b;">Content here</p></td></tr></table>' },
    { id: 'image-block', label: 'Image', category: 'Content', content: '<table style="width:100%;padding:20px 40px;"><tr><td align="center"><img src="https://via.placeholder.com/540x200/f1f5f9/94a3b8?text=Your+Image" style="max-width:100%;border-radius:12px;border:1px solid #e2e8f0;" alt="Image"></td></tr></table>' },
    { id: 'button-block', label: 'Button', category: 'Content', content: '<table style="width:100%;padding:20px 40px;"><tr><td align="center"><a href="#" style="display:inline-block;padding:14px 40px;background:#5a66f1;color:#fff;text-decoration:none;border-radius:10px;font-weight:bold;font-size:16px;font-family:sans-serif;">Click Here</a></td></tr></table>' },
    { id: 'spacer-block', label: 'Spacer', category: 'Content', content: '<table style="width:100%;"><tr><td style="height:30px;"></td></tr></table>' },
    { id: 'quote-block', label: 'Quote', category: 'Content', content: '<table style="width:100%;padding:20px 40px;"><tr><td style="border-left:4px solid #5a66f1;padding:20px 24px;background:#f8fafc;border-radius:0 12px 12px 0;"><p style="color:#475569;font-style:italic;font-size:16px;line-height:1.6;margin:0;">&ldquo;Exceptional service from start to finish.&rdquo;</p><p style="color:#94a3b8;font-size:13px;margin:10px 0 0;">— Happy Client</p></td></tr></table>' },
    { id: 'pricing-table', label: 'Price Table', category: 'LimoCRM', content: '<table style="width:100%;padding:20px 40px;"><tr><td><table style="width:100%;background:#1b314d;border:1px solid #243a57;border-radius:12px;color:#fff;"><tr><td colspan="2" style="padding:16px 20px;border-bottom:1px solid #243a57;"><span style="color:#dfca8b;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Pricing Breakdown</span></td></tr><tr><td style="padding:14px 20px;border-bottom:1px solid #243a57;color:#8ba4c0;">Service Length</td><td align="right" style="padding:14px 20px;border-bottom:1px solid #243a57;font-weight:700;">$service_length hrs</td></tr><tr><td style="padding:14px 20px;border-bottom:1px solid #243a57;color:#8ba4c0;">Quoted Price</td><td align="right" style="padding:14px 20px;border-bottom:1px solid #243a57;font-weight:700;">$quoted_price</td></tr><tr><td style="padding:14px 20px;border-bottom:1px solid #243a57;color:#8ba4c0;">Fuel Surcharge</td><td align="right" style="padding:14px 20px;border-bottom:1px solid #243a57;font-weight:700;">$fuel_surcharge</td></tr><tr><td style="padding:18px 20px;color:#dfca8b;font-weight:700;font-size:16px;">Total</td><td align="right" style="padding:18px 20px;font-weight:700;font-size:24px;">$total_price</td></tr></table></td></tr></table>' },
    { id: 'trip-details', label: 'Trip Details', category: 'LimoCRM', content: '<table style="width:100%;padding:20px 40px;"><tr><td><table style="width:100%;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;"><tr><td colspan="2" style="padding:16px 20px;border-bottom:1px solid #e2e8f0;font-weight:700;color:#1e293b;font-size:14px;">Trip Details</td></tr><tr><td style="padding:12px 20px;color:#64748b;width:35%;">Pickup</td><td style="padding:12px 20px;font-weight:600;color:#1e293b;">$pickup_location</td></tr><tr><td style="padding:12px 20px;color:#64748b;border-top:1px solid #e2e8f0;">Drop-off</td><td style="padding:12px 20px;font-weight:600;color:#1e293b;border-top:1px solid #e2e8f0;">$dropoff_location</td></tr><tr><td style="padding:12px 20px;color:#64748b;border-top:1px solid #e2e8f0;">Date</td><td style="padding:12px 20px;font-weight:600;color:#1e293b;border-top:1px solid #e2e8f0;">$event_date</td></tr><tr><td style="padding:12px 20px;color:#64748b;border-top:1px solid #e2e8f0;">Passengers</td><td style="padding:12px 20px;font-weight:600;color:#1e293b;border-top:1px solid #e2e8f0;">$passengers</td></tr></table></td></tr></table>' },
    { id: 'client-info', label: 'Client Info', category: 'LimoCRM', content: '<table style="width:100%;padding:20px 40px;"><tr><td><table style="width:100%;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;"><tr><td colspan="2" style="padding:16px 20px;border-bottom:1px solid #e2e8f0;font-weight:700;color:#1e293b;font-size:14px;">Client Information</td></tr><tr><td style="padding:12px 20px;color:#64748b;width:35%;">Name</td><td style="padding:12px 20px;font-weight:600;color:#1e293b;">$first_name $last_name</td></tr><tr><td style="padding:12px 20px;color:#64748b;border-top:1px solid #e2e8f0;">Email</td><td style="padding:12px 20px;font-weight:600;color:#1e293b;border-top:1px solid #e2e8f0;">$email</td></tr><tr><td style="padding:12px 20px;color:#64748b;border-top:1px solid #e2e8f0;">Phone</td><td style="padding:12px 20px;font-weight:600;color:#1e293b;border-top:1px solid #e2e8f0;">$phone</td></tr></table></td></tr></table>' },
    { id: 'vehicle-card', label: 'Vehicle Card', category: 'LimoCRM', content: '<table style="width:100%;padding:20px 40px;"><tr><td align="center"><table style="width:100%;max-width:400px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;"><tr><td style="padding:0;"><img src="https://via.placeholder.com/400x200/e2e8f0/94a3b8?text=Vehicle+Image" style="width:100%;display:block;" alt="Vehicle"></td></tr><tr><td style="padding:20px;text-align:center;"><h3 style="margin:0 0 4px;color:#1e293b;font-size:18px;">$vehicle_name</h3><p style="color:#64748b;margin:0;font-size:14px;">$vehicle_type</p></td></tr></table></td></tr></table>' },
    { id: 'tracking-pixel', label: 'Email Tracking', category: 'LimoCRM', content: '<img src="$tracking_pixel_url" width="1" height="1" alt="" style="display:block;width:1px;height:1px;border:0;overflow:hidden;" />' },
    { id: 'unsubscribe-block', label: 'Unsubscribe', category: 'LimoCRM', content: '<table style="width:100%;padding:16px 40px;text-align:center;"><tr><td><p style="color:#94a3b8;font-size:11px;margin:0;">You are receiving this email because you interacted with our service.</p><p style="margin:6px 0 0;"><a href="$unsubscribe_url" style="color:#ef4444;font-size:11px;text-decoration:underline;">Unsubscribe from future emails</a></p></td></tr></table>' }
  ];
  allBlocks.forEach(function(b) { bm.add(b.id, b); });

  gjsEditor.on('component:add', updateBlockCount);
  gjsEditor.on('component:remove', updateBlockCount);

  // Widget search
  $('#widget-search').on('input', function() {
    var q = $(this).val().toLowerCase().trim();
    $('.eb-widget').each(function() {
      var name = $(this).find('.eb-widget-name').text().toLowerCase();
      $(this).toggle(!q || name.indexOf(q) !== -1);
    });
  });

  // Mode switching
  $('.eb-mode-btn').on('click', function() {
    var view = $(this).data('view');
    $('.eb-mode-btn').removeClass('active');
    $(this).addClass('active');
    if (view === 'code') {
      var html = '';
      try { html = gjsEditor.runCommand('gjs-get-inlined-html'); } catch (e) {}
      if (!html) html = gjsEditor.getHtml();
      $('#raw-html').val(html);
      $('#builder-container').addClass('hidden');
      $('#code-container').removeClass('hidden');
    } else {
      gjsEditor.setComponents($('#raw-html').val());
      $('#code-container').addClass('hidden');
      $('#builder-container').removeClass('hidden');
      gjsEditor.refresh();
    }
  });

  // Validation on blur
  $('#name').on('blur', function() {
    var v = $(this).val().trim();
    $(this).removeClass('is-invalid is-valid');
    if (!v) { $(this).addClass('is-invalid'); $('#err-name').text('Template name is required').addClass('show'); }
    else if (v.length < 3) { $(this).addClass('is-invalid'); $('#err-name').text('Min 3 characters').addClass('show'); }
    else { $(this).addClass('is-valid'); $('#err-name').removeClass('show'); }
  });
  $('#subject').on('blur', function() {
    var v = $(this).val().trim();
    $(this).removeClass('is-invalid is-valid');
    if (!v) { $(this).addClass('is-invalid'); $('#err-subject').text('Subject line is required').addClass('show'); }
    else if (v.length < 3) { $(this).addClass('is-invalid'); $('#err-subject').text('Min 3 characters').addClass('show'); }
    else { $(this).addClass('is-valid'); $('#err-subject').removeClass('show'); }
  });
  $('#name, #subject').on('focus', function() {
    $(this).removeClass('is-invalid');
    $('#err-' + this.id).removeClass('show');
  });

  // Save
  $('#save-template-btn').on('click', function() {
    var name = $('#name').val().trim();
    var subject = $('#subject').val().trim();
    $('#name, #subject').removeClass('is-invalid is-valid');
    $('.eb-error').removeClass('show');
    var hasErr = false, firstErr = null;

    if (!name || name.length < 3) {
      $('#name').addClass('is-invalid');
      $('#err-name').text(!name ? 'Template name is required' : 'Min 3 characters').addClass('show');
      hasErr = true; if (!firstErr) firstErr = '#name';
    }
    if (!subject || subject.length < 3) {
      $('#subject').addClass('is-invalid');
      $('#err-subject').text(!subject ? 'Subject line is required' : 'Min 3 characters').addClass('show');
      hasErr = true; if (!firstErr) firstErr = '#subject';
    }
    if (hasErr) {
      switchTab('settings', document.querySelector('.eb-tab[data-tab="settings"]'));
      setTimeout(function() { $(firstErr).focus(); }, 150);
      return;
    }

    var btn = $(this), orig = btn.html();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm align-middle me-1"></span> Saving...');

    var finalHtml = '';
    var view = $('.eb-mode-btn.active').data('view');
    if (view === 'code') { finalHtml = $('#raw-html').val(); }
    else {
      try { finalHtml = gjsEditor.runCommand('gjs-get-inlined-html'); } catch (e) {}
      if (!finalHtml) finalHtml = gjsEditor.getHtml();
    }

    var fd = new FormData($('#template-info-form')[0]);
    fd.append('action', 'save_email_template');
    fd.append('body_html', finalHtml);
    fd.append('created_by', '<?php echo $_SESSION["user"]["id"]; ?>');

    $.ajax({
      url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
      type: 'POST', data: fd, processData: false, contentType: false,
      success: function(resp) {
        var d = typeof resp === 'string' ? JSON.parse(resp) : resp;
        if (d.success) {
          Swal.fire({ icon: 'success', title: 'Saved!', text: 'Template saved.', timer: 1500, showConfirmButton: false }).then(function() { window.location.href = 'email_templates.php'; });
        } else { btn.prop('disabled', false).html(orig); Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'Failed to save.' }); }
      },
      error: function() { btn.prop('disabled', false).html(orig); Swal.fire({ icon: 'error', title: 'Server Error', text: 'Something went wrong.' }); }
    });
  });
});
</script>
