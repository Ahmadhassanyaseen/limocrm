 <?php session_start(); 
// print_r($_SESSION['user']);
 if(!isset($_SESSION['user'])){

    header("Location: login.php");
 }
 
 ?>
 <html
  lang="en"
  dir="ltr"
  data-nav-layout="vertical"
  class="light"
  data-header-styles="light"
  data-menu-styles="dark"
  data-width="fullwidth"
  loader="disable"
  data-vertical-style="overlay"
>
 <head>
    <!-- Meta Data -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>LimoGen</title>
    
    <!-- Favicon -->
    <link
      rel="icon"
      href="assets/images/brand-logos/favicon.ico"
      type="image/x-icon"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css"
      integrity="sha512-XcIsjKMcuVe0Ucj/xgIXQnytNwBttJbNjltBV18IOnru2lDPe9KRRyvCXw6Y5H415vbBLRm8+q6fmLUU7DfO6Q=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.0/css/animations.min.css" integrity="sha512-GKHaATMc7acW6/GDGVyBhKV3rST+5rMjokVip0uTikmZHhdqFWC7fGBaq6+lf+DOS5BIO8eK6NcyBYUBCHUBXA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Choices JS -->
    <script src="assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
    <!-- Main Theme Js -->
    <script src="assets/js/main.js"></script>
    <!-- Style Css -->
    <link href="assets/css/styles.css" rel="stylesheet" />
    <!-- Node Waves Css -->
    <link href="assets/libs/node-waves/waves.min.css" rel="stylesheet" />
    <!-- Simplebar Css -->
    <link href="assets/libs/simplebar/simplebar.min.css" rel="stylesheet" />
    <!-- Color Picker Css -->
    <link rel="stylesheet" href="assets/libs/flatpickr/flatpickr.min.css" />
    <link
      rel="stylesheet"
      href="assets/libs/@simonwep/pickr/themes/nano.min.css"
    />
    <!-- Choices Css -->
    <link
      rel="stylesheet"
      href="assets/libs/choices.js/public/assets/styles/choices.min.css"
    />
    <!-- FlatPickr CSS -->
    <link rel="stylesheet" href="assets/libs/flatpickr/flatpickr.min.css" />
    <!-- Auto Complete CSS -->
    <link
      rel="stylesheet"
      href="assets/libs/@tarekraafat/autocomplete.js/css/autoComplete.css"
    />
<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js" type="text/javascript"></script>
    <meta http-equiv="imagetoolbar" content="no" />

    <style id="apexcharts-css">
      @keyframes opaque {
        0% {
          opacity: 0;
        }

        to {
          opacity: 1;
        }
      }

      @keyframes resizeanim {
        0%,
        to {
          opacity: 0;
        }
      }

      .apexcharts-canvas {
        position: relative;
        user-select: none;
      }

      .apexcharts-canvas ::-webkit-scrollbar {
        -webkit-appearance: none;
        width: 6px;
      }

      .apexcharts-canvas ::-webkit-scrollbar-thumb {
        border-radius: 4px;
        background-color: rgba(0, 0, 0, 0.5);
        box-shadow: 0 0 1px rgba(255, 255, 255, 0.5);
        -webkit-box-shadow: 0 0 1px rgba(255, 255, 255, 0.5);
      }

      .apexcharts-inner {
        position: relative;
      }

      .apexcharts-text tspan {
        font-family: inherit;
      }

      rect.legend-mouseover-inactive,
      .legend-mouseover-inactive rect,
      .legend-mouseover-inactive path,
      .legend-mouseover-inactive circle,
      .legend-mouseover-inactive line,
      .legend-mouseover-inactive text.apexcharts-yaxis-title-text,
      .legend-mouseover-inactive text.apexcharts-yaxis-label {
        transition: 0.15s ease all;
        opacity: 0.2;
      }

      .apexcharts-legend-text {
        padding-left: 15px;
        margin-left: -15px;
      }

      .apexcharts-series-collapsed {
        opacity: 0;
      }
      #save-Notes-btn{
background:red
      }

      .apexcharts-tooltip {
        border-radius: 5px;
        box-shadow: 2px 2px 6px -4px #999;
        cursor: default;
        font-size: 14px;
        left: 62px;
        opacity: 0;
        pointer-events: none;
        position: absolute;
        top: 20px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        white-space: nowrap;
        z-index: 12;
        transition: 0.15s ease all;
      }

      .apexcharts-tooltip.apexcharts-active {
        opacity: 1;
        transition: 0.15s ease all;
      }

      .apexcharts-tooltip.apexcharts-theme-light {
        border: 1px solid #e3e3e3;
        background: rgba(255, 255, 255, 0.96);
      }

      .apexcharts-tooltip.apexcharts-theme-dark {
        color: #fff;
        background: rgba(30, 30, 30, 0.8);
      }

      .apexcharts-tooltip * {
        font-family: inherit;
      }

      .apexcharts-tooltip-title {
        padding: 6px;
        font-size: 15px;
        margin-bottom: 4px;
      }

      .apexcharts-tooltip.apexcharts-theme-light .apexcharts-tooltip-title {
        background: #eceff1;
        border-bottom: 1px solid #ddd;
      }

      .apexcharts-tooltip.apexcharts-theme-dark .apexcharts-tooltip-title {
        background: rgba(0, 0, 0, 0.7);
        border-bottom: 1px solid #333;
      }

      .apexcharts-tooltip-text-goals-value,
      .apexcharts-tooltip-text-y-value,
      .apexcharts-tooltip-text-z-value {
        display: inline-block;
        margin-left: 5px;
        font-weight: 600;
      }

      .apexcharts-tooltip-text-goals-label:empty,
      .apexcharts-tooltip-text-goals-value:empty,
      .apexcharts-tooltip-text-y-label:empty,
      .apexcharts-tooltip-text-y-value:empty,
      .apexcharts-tooltip-text-z-value:empty,
      .apexcharts-tooltip-title:empty {
        display: none;
      }

      .apexcharts-tooltip-text-goals-label,
      .apexcharts-tooltip-text-goals-value {
        padding: 6px 0 5px;
      }

      .apexcharts-tooltip-goals-group,
      .apexcharts-tooltip-text-goals-label,
      .apexcharts-tooltip-text-goals-value {
        display: flex;
      }

      .apexcharts-tooltip-text-goals-label:not(:empty),
      .apexcharts-tooltip-text-goals-value:not(:empty) {
        margin-top: -6px;
      }

      .apexcharts-tooltip-marker {
        width: 12px;
        height: 12px;
        position: relative;
        top: 0;
        margin-right: 10px;
        border-radius: 50%;
      }

      .apexcharts-tooltip-series-group {
        padding: 0 10px;
        display: none;
        text-align: left;
        justify-content: left;
        align-items: center;
      }

      .apexcharts-tooltip-series-group.apexcharts-active
        .apexcharts-tooltip-marker {
        opacity: 1;
      }

      .apexcharts-tooltip-series-group.apexcharts-active,
      .apexcharts-tooltip-series-group:last-child {
        padding-bottom: 4px;
      }

      .apexcharts-tooltip-y-group {
        padding: 6px 0 5px;
      }

      .apexcharts-custom-tooltip,
      .apexcharts-tooltip-box {
        padding: 4px 8px;
      }

      .apexcharts-tooltip-boxPlot {
        display: flex;
        flex-direction: column-reverse;
      }

      .apexcharts-tooltip-box > div {
        margin: 4px 0;
      }

      .apexcharts-tooltip-box span.value {
        font-weight: 700;
      }

      .apexcharts-tooltip-rangebar {
        padding: 5px 8px;
      }

      .apexcharts-tooltip-rangebar .category {
        font-weight: 600;
        color: #777;
      }

      .apexcharts-tooltip-rangebar .series-name {
        font-weight: 700;
        display: block;
        margin-bottom: 5px;
      }

      .apexcharts-xaxistooltip,
      .apexcharts-yaxistooltip {
        opacity: 0;
        pointer-events: none;
        color: #373d3f;
        font-size: 13px;
        text-align: center;
        border-radius: 2px;
        position: absolute;
        z-index: 10;
        background: #eceff1;
        border: 1px solid #90a4ae;
      }

      .apexcharts-xaxistooltip {
        padding: 9px 10px;
        transition: 0.15s ease all;
      }

      .apexcharts-xaxistooltip.apexcharts-theme-dark {
        background: rgba(0, 0, 0, 0.7);
        border: 1px solid rgba(0, 0, 0, 0.5);
        color: #fff;
      }

      .apexcharts-xaxistooltip:after,
      .apexcharts-xaxistooltip:before {
        left: 50%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
      }

      .apexcharts-xaxistooltip:after {
        border-color: transparent;
        border-width: 6px;
        margin-left: -6px;
      }

      .apexcharts-xaxistooltip:before {
        border-color: transparent;
        border-width: 7px;
        margin-left: -7px;
      }

      .apexcharts-xaxistooltip-bottom:after,
      .apexcharts-xaxistooltip-bottom:before {
        bottom: 100%;
      }

      .apexcharts-xaxistooltip-top:after,
      .apexcharts-xaxistooltip-top:before {
        top: 100%;
      }

      .apexcharts-xaxistooltip-bottom:after {
        border-bottom-color: #eceff1;
      }

      .apexcharts-xaxistooltip-bottom:before {
        border-bottom-color: #90a4ae;
      }

      .apexcharts-xaxistooltip-bottom.apexcharts-theme-dark:after,
      .apexcharts-xaxistooltip-bottom.apexcharts-theme-dark:before {
        border-bottom-color: rgba(0, 0, 0, 0.5);
      }

      .apexcharts-xaxistooltip-top:after {
        border-top-color: #eceff1;
      }

      .apexcharts-xaxistooltip-top:before {
        border-top-color: #90a4ae;
      }

      .apexcharts-xaxistooltip-top.apexcharts-theme-dark:after,
      .apexcharts-xaxistooltip-top.apexcharts-theme-dark:before {
        border-top-color: rgba(0, 0, 0, 0.5);
      }

      .apexcharts-xaxistooltip.apexcharts-active {
        opacity: 1;
        transition: 0.15s ease all;
      }

      .apexcharts-yaxistooltip {
        padding: 4px 10px;
      }

      .apexcharts-yaxistooltip.apexcharts-theme-dark {
        background: rgba(0, 0, 0, 0.7);
        border: 1px solid rgba(0, 0, 0, 0.5);
        color: #fff;
      }

      .apexcharts-yaxistooltip:after,
      .apexcharts-yaxistooltip:before {
        top: 50%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
      }

      .apexcharts-yaxistooltip:after {
        border-color: transparent;
        border-width: 6px;
        margin-top: -6px;
      }

      .apexcharts-yaxistooltip:before {
        border-color: transparent;
        border-width: 7px;
        margin-top: -7px;
      }

      .apexcharts-yaxistooltip-left:after,
      .apexcharts-yaxistooltip-left:before {
        left: 100%;
      }

      .apexcharts-yaxistooltip-right:after,
      .apexcharts-yaxistooltip-right:before {
        right: 100%;
      }

      .apexcharts-yaxistooltip-left:after {
        border-left-color: #eceff1;
      }

      .apexcharts-yaxistooltip-left:before {
        border-left-color: #90a4ae;
      }

      .apexcharts-yaxistooltip-left.apexcharts-theme-dark:after,
      .apexcharts-yaxistooltip-left.apexcharts-theme-dark:before {
        border-left-color: rgba(0, 0, 0, 0.5);
      }

      .apexcharts-yaxistooltip-right:after {
        border-right-color: #eceff1;
      }

      .apexcharts-yaxistooltip-right:before {
        border-right-color: #90a4ae;
      }

      .apexcharts-yaxistooltip-right.apexcharts-theme-dark:after,
      .apexcharts-yaxistooltip-right.apexcharts-theme-dark:before {
        border-right-color: rgba(0, 0, 0, 0.5);
      }

      .apexcharts-yaxistooltip.apexcharts-active {
        opacity: 1;
      }

      .apexcharts-yaxistooltip-hidden {
        display: none;
      }

      .apexcharts-xcrosshairs,
      .apexcharts-ycrosshairs {
        pointer-events: none;
        opacity: 0;
        transition: 0.15s ease all;
      }

      .apexcharts-xcrosshairs.apexcharts-active,
      .apexcharts-ycrosshairs.apexcharts-active {
        opacity: 1;
        transition: 0.15s ease all;
      }

      .apexcharts-ycrosshairs-hidden {
        opacity: 0;
      }

      .apexcharts-selection-rect {
        cursor: move;
      }

      .svg_select_boundingRect,
      .svg_select_points_rot {
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
      }

      .apexcharts-selection-rect + g .svg_select_boundingRect,
      .apexcharts-selection-rect + g .svg_select_points_rot {
        opacity: 0;
        visibility: hidden;
      }

      .apexcharts-selection-rect + g .svg_select_points_l,
      .apexcharts-selection-rect + g .svg_select_points_r {
        cursor: ew-resize;
        opacity: 1;
        visibility: visible;
      }

      .svg_select_points {
        fill: #efefef;
        stroke: #333;
        rx: 2;
      }

      .apexcharts-svg.apexcharts-zoomable.hovering-zoom {
        cursor: crosshair;
      }

      .apexcharts-svg.apexcharts-zoomable.hovering-pan {
        cursor: move;
      }

      .apexcharts-menu-icon,
      .apexcharts-pan-icon,
      .apexcharts-reset-icon,
      .apexcharts-selection-icon,
      .apexcharts-toolbar-custom-icon,
      .apexcharts-zoom-icon,
      .apexcharts-zoomin-icon,
      .apexcharts-zoomout-icon {
        cursor: pointer;
        width: 20px;
        height: 20px;
        line-height: 24px;
        color: #6e8192;
        text-align: center;
      }

      .apexcharts-menu-icon svg,
      .apexcharts-reset-icon svg,
      .apexcharts-zoom-icon svg,
      .apexcharts-zoomin-icon svg,
      .apexcharts-zoomout-icon svg {
        fill: #6e8192;
      }

      .apexcharts-selection-icon svg {
        fill: #444;
        transform: scale(0.76);
      }

      .apexcharts-theme-dark .apexcharts-menu-icon svg,
      .apexcharts-theme-dark .apexcharts-pan-icon svg,
      .apexcharts-theme-dark .apexcharts-reset-icon svg,
      .apexcharts-theme-dark .apexcharts-selection-icon svg,
      .apexcharts-theme-dark .apexcharts-toolbar-custom-icon svg,
      .apexcharts-theme-dark .apexcharts-zoom-icon svg,
      .apexcharts-theme-dark .apexcharts-zoomin-icon svg,
      .apexcharts-theme-dark .apexcharts-zoomout-icon svg {
        fill: #f3f4f5;
      }

      .apexcharts-canvas .apexcharts-reset-zoom-icon.apexcharts-selected svg,
      .apexcharts-canvas .apexcharts-selection-icon.apexcharts-selected svg,
      .apexcharts-canvas .apexcharts-zoom-icon.apexcharts-selected svg {
        fill: #008ffb;
      }

      .apexcharts-theme-light .apexcharts-menu-icon:hover svg,
      .apexcharts-theme-light .apexcharts-reset-icon:hover svg,
      .apexcharts-theme-light
        .apexcharts-selection-icon:not(.apexcharts-selected):hover
        svg,
      .apexcharts-theme-light
        .apexcharts-zoom-icon:not(.apexcharts-selected):hover
        svg,
      .apexcharts-theme-light .apexcharts-zoomin-icon:hover svg,
      .apexcharts-theme-light .apexcharts-zoomout-icon:hover svg {
        fill: #333;
      }

      .apexcharts-menu-icon,
      .apexcharts-selection-icon {
        position: relative;
      }

      .apexcharts-reset-icon {
        margin-left: 5px;
      }

      .apexcharts-menu-icon,
      .apexcharts-reset-icon,
      .apexcharts-zoom-icon {
        transform: scale(0.85);
      }

      .apexcharts-zoomin-icon,
      .apexcharts-zoomout-icon {
        transform: scale(0.7);
      }

      .apexcharts-zoomout-icon {
        margin-right: 3px;
      }

      .apexcharts-pan-icon {
        transform: scale(0.62);
        position: relative;
        left: 1px;
        top: 0;
      }

      .apexcharts-pan-icon svg {
        fill: #fff;
        stroke: #6e8192;
        stroke-width: 2;
      }

      .apexcharts-pan-icon.apexcharts-selected svg {
        stroke: #008ffb;
      }

      .apexcharts-pan-icon:not(.apexcharts-selected):hover svg {
        stroke: #333;
      }

      .apexcharts-toolbar {
        position: absolute;
        z-index: 11;
        max-width: 176px;
        text-align: right;
        border-radius: 3px;
        padding: 0 6px 2px;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .apexcharts-menu {
        background: #fff;
        position: absolute;
        top: 100%;
        border: 1px solid #ddd;
        border-radius: 3px;
        padding: 3px;
        right: 10px;
        opacity: 0;
        min-width: 110px;
        transition: 0.15s ease all;
        pointer-events: none;
      }

      .apexcharts-menu.apexcharts-menu-open {
        opacity: 1;
        pointer-events: all;
        transition: 0.15s ease all;
      }

      .apexcharts-menu-item {
        padding: 6px 7px;
        font-size: 12px;
        cursor: pointer;
      }

      .apexcharts-theme-light .apexcharts-menu-item:hover {
        background: #eee;
      }

      .apexcharts-theme-dark .apexcharts-menu {
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
      }

      @media screen and (min-width: 768px) {
        .apexcharts-canvas:hover .apexcharts-toolbar {
          opacity: 1;
        }
      }

      .apexcharts-canvas .apexcharts-element-hidden,
      .apexcharts-datalabel.apexcharts-element-hidden,
      .apexcharts-hide .apexcharts-series-points {
        opacity: 0;
      }

      .apexcharts-hidden-element-shown {
        opacity: 1;
        transition: 0.25s ease all;
      }

      .apexcharts-datalabel,
      .apexcharts-datalabel-label,
      .apexcharts-datalabel-value,
      .apexcharts-datalabels,
      .apexcharts-pie-label {
        cursor: default;
        pointer-events: none;
      }

      .apexcharts-pie-label-delay {
        opacity: 0;
        animation-name: opaque;
        animation-duration: 0.3s;
        animation-fill-mode: forwards;
        animation-timing-function: ease;
      }

      .apexcharts-radialbar-label {
        cursor: pointer;
      }

      .apexcharts-annotation-rect,
      .apexcharts-area-series .apexcharts-area,
      .apexcharts-area-series
        .apexcharts-series-markers
        .apexcharts-marker.no-pointer-events,
      .apexcharts-gridline,
      .apexcharts-line,
      .apexcharts-line-series
        .apexcharts-series-markers
        .apexcharts-marker.no-pointer-events,
      .apexcharts-point-annotation-label,
      .apexcharts-radar-series path:not(.apexcharts-marker),
      .apexcharts-radar-series polygon,
      .apexcharts-toolbar svg,
      .apexcharts-tooltip .apexcharts-marker,
      .apexcharts-xaxis-annotation-label,
      .apexcharts-yaxis-annotation-label,
      .apexcharts-zoom-rect {
        pointer-events: none;
      }

      .apexcharts-tooltip-active .apexcharts-marker {
        transition: 0.15s ease all;
      }

      .resize-triggers {
        animation: 1ms resizeanim;
        visibility: hidden;
        opacity: 0;
        height: 100%;
        width: 100%;
        overflow: hidden;
      }

      .contract-trigger:before,
      .resize-triggers,
      .resize-triggers > div {
        content: " ";
        display: block;
        position: absolute;
        top: 0;
        left: 0;
      }

      .resize-triggers > div {
        height: 100%;
        width: 100%;
        background: #eee;
        overflow: auto;
      }

      .contract-trigger:before {
        overflow: hidden;
        width: 200%;
        height: 200%;
      }

      .apexcharts-bar-goals-markers {
        pointer-events: none;
      }

      .apexcharts-bar-shadows {
        pointer-events: none;
      }

      .apexcharts-rangebar-goals-markers {
        pointer-events: none;
      }
    </style>
    <link rel="stylesheet" href="assets/css/custom.css">
  </head>
  <body cz-shortcut-listen="true">
    <!-- ========== Switcher  ========== -->
    <div
        id="hs-overlay-switcher"
        class="hs-overlay hidden ti-offcanvas ti-offcanvas-right"
        tabindex="-1"
      >
      <div class="ti-offcanvas-header z-10 relative">
        <h5 class="ti-offcanvas-title">Switcher</h5>
        <button
          type="button"
          class="ti-btn flex-shrink-0 p-0 !mb-0 transition-none text-defaulttextcolor dark:text-defaulttextcolor/80 hover:text-gray-700 focus:ring-gray-400 focus:ring-offset-white dark:hover:text-white/80 dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
          data-hs-overlay="#hs-overlay-switcher"
        >
          <span class="sr-only">Close modal</span>
          <i class="ri-close-circle-line leading-none text-lg"></i>
        </button>
      </div>
      <div
        class="ti-offcanvas-body !p-0 !border-b dark:border-white/10 z-10 relative !h-auto"
      >
        <div class="flex rtl:space-x-reverse" aria-label="Tabs" role="tablist">
          <button
            type="button"
            class="hs-tab-active:bg-danger/20 w-full !py-2 !px-4 hs-tab-active:border-b-transparent text-[0.813rem] border-0 hs-tab-active:text-danger dark:hs-tab-active:bg-danger/20 dark:hs-tab-active:border-b-white/10 dark:hs-tab-active:text-danger -mb-px bg-white font-normal text-center text-defaulttextcolor dark:text-defaulttextcolor/80 rounded-none hover:text-gray-700 dark:bg-bodybg dark:border-white/10 active"
            id="switcher-item-1"
            data-hs-tab="#switcher-1"
            aria-controls="switcher-1"
            role="tab"
          >
            Theme Style
          </button>
          <button
            type="button"
            class="hs-tab-active:bg-danger/20 w-full !py-2 !px-4 hs-tab-active:border-b-transparent text-[0.813rem] border-0 hs-tab-active:text-danger dark:hs-tab-active:bg-danger/20 dark:hs-tab-active:border-b-white/10 dark:hs-tab-active:text-danger -mb-px bg-white font-normal text-center text-defaulttextcolor dark:text-defaulttextcolor/80 rounded-none hover:text-gray-700 dark:bg-bodybg dark:border-white/10 dark:hover:text-gray-300"
            id="switcher-item-2"
            data-hs-tab="#switcher-2"
            aria-controls="switcher-2"
            role="tab"
          >
            Theme Colors
          </button>
        </div>
      </div>
      <div class="ti-offcanvas-body !p-0 !pb-[20rem]" id="switcher-body">
        <div
          id="switcher-1"
          role="tabpanel"
          aria-labelledby="switcher-item-1"
          class=""
        >
          <div class="">
            <p class="switcher-style-head">Theme Color Mode:</p>
            <div class="grid grid-cols-3 switcher-style">
              <div class="flex items-center">
                <input
                  type="radio"
                  name="theme-style"
                  class="ti-form-radio"
                  id="switcher-light-theme"
                  checked=""
                />
                <label
                  for="switcher-light-theme"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Light</label
                >
              </div>
              <div class="flex items-center">
                <input
                  type="radio"
                  name="theme-style"
                  class="ti-form-radio"
                  id="switcher-dark-theme"
                />
                <label
                  for="switcher-dark-theme"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Dark</label
                >
              </div>
            </div>
          </div>
          <div>
            <p class="switcher-style-head">Directions:</p>
            <div class="grid grid-cols-3 switcher-style">
              <div class="flex items-center">
                <input
                  type="radio"
                  name="direction"
                  class="ti-form-radio"
                  id="switcher-ltr"
                  checked=""
                />
                <label
                  for="switcher-ltr"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >LTR</label
                >
              </div>
              <div class="flex items-center">
                <input
                  type="radio"
                  name="direction"
                  class="ti-form-radio"
                  id="switcher-rtl"
                />
                <label
                  for="switcher-rtl"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >RTL</label
                >
              </div>
            </div>
          </div>
          <div>
            <p class="switcher-style-head">Navigation Styles:</p>
            <div class="grid grid-cols-3 switcher-style">
              <div class="flex items-center">
                <input
                  type="radio"
                  name="navigation-style"
                  class="ti-form-radio"
                  id="switcher-vertical"
                  checked=""
                />
                <label
                  for="switcher-vertical"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Vertical</label
                >
              </div>
              <div class="flex items-center">
                <input
                  type="radio"
                  name="navigation-style"
                  class="ti-form-radio"
                  id="switcher-horizontal"
                />
                <label
                  for="switcher-horizontal"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Horizontal</label
                >
              </div>
            </div>
          </div>
          <div>
            <p class="switcher-style-head">Navigation Menu Style:</p>
            <div class="grid grid-cols-2 gap-2 switcher-style">
              <div class="flex">
                <input
                  type="radio"
                  name="navigation-data-menu-styles"
                  class="ti-form-radio"
                  id="switcher-menu-click"
                  checked=""
                />
                <label
                  for="switcher-menu-click"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Menu Click</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="navigation-data-menu-styles"
                  class="ti-form-radio"
                  id="switcher-menu-hover"
                />
                <label
                  for="switcher-menu-hover"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Menu Hover</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="navigation-data-menu-styles"
                  class="ti-form-radio"
                  id="switcher-icon-click"
                />
                <label
                  for="switcher-icon-click"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Icon Click</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="navigation-data-menu-styles"
                  class="ti-form-radio"
                  id="switcher-icon-hover"
                />
                <label
                  for="switcher-icon-hover"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Icon Hover</label
                >
              </div>
            </div>
          </div>
          <div class="sidemenu-layout-styles">
            <p class="switcher-style-head">Sidemenu Layout Syles:</p>
            <div class="grid grid-cols-2 gap-2 switcher-style">
              <div class="flex">
                <input
                  type="radio"
                  name="sidemenu-layout-styles"
                  class="ti-form-radio"
                  id="switcher-default-menu"
                  checked=""
                />
                <label
                  for="switcher-default-menu"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Default Menu</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="sidemenu-layout-styles"
                  class="ti-form-radio"
                  id="switcher-closed-menu"
                />
                <label
                  for="switcher-closed-menu"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                >
                  Closed Menu</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="sidemenu-layout-styles"
                  class="ti-form-radio"
                  id="switcher-icontext-menu"
                />
                <label
                  for="switcher-icontext-menu"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Icon Text</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="sidemenu-layout-styles"
                  class="ti-form-radio"
                  id="switcher-icon-overlay"
                />
                <label
                  for="switcher-icon-overlay"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Icon Overlay</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="sidemenu-layout-styles"
                  class="ti-form-radio"
                  id="switcher-detached"
                />
                <label
                  for="switcher-detached"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Detached</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="sidemenu-layout-styles"
                  class="ti-form-radio"
                  id="switcher-double-menu"
                />
                <label
                  for="switcher-double-menu"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Double Menu</label
                >
              </div>
            </div>
          </div>
          <div>
            <p class="switcher-style-head">Page Styles:</p>
            <div class="grid grid-cols-3 switcher-style">
              <div class="flex">
                <input
                  type="radio"
                  name="data-page-styles"
                  class="ti-form-radio"
                  id="switcher-regular"
                  checked=""
                />
                <label
                  for="switcher-regular"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Regular</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="data-page-styles"
                  class="ti-form-radio"
                  id="switcher-classic"
                />
                <label
                  for="switcher-classic"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Classic</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="data-page-styles"
                  class="ti-form-radio"
                  id="switcher-modern"
                />
                <label
                  for="switcher-modern"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                >
                  Modern</label
                >
              </div>
            </div>
          </div>
          <div>
            <p class="switcher-style-head">Layout Width Styles:</p>
            <div class="grid grid-cols-3 switcher-style">
              <div class="flex">
                <input
                  type="radio"
                  name="layout-width"
                  class="ti-form-radio"
                  id="switcher-full-width"
                  checked=""
                />
                <label
                  for="switcher-full-width"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >FullWidth</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="layout-width"
                  class="ti-form-radio"
                  id="switcher-boxed"
                />
                <label
                  for="switcher-boxed"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Boxed</label
                >
              </div>
            </div>
          </div>
          <div>
            <p class="switcher-style-head">Menu Positions:</p>
            <div class="grid grid-cols-3 switcher-style">
              <div class="flex">
                <input
                  type="radio"
                  name="data-menu-positions"
                  class="ti-form-radio"
                  id="switcher-menu-fixed"
                  checked=""
                />
                <label
                  for="switcher-menu-fixed"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Fixed</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="data-menu-positions"
                  class="ti-form-radio"
                  id="switcher-menu-scroll"
                />
                <label
                  for="switcher-menu-scroll"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Scrollable
                </label>
              </div>
            </div>
          </div>
          <div>
            <p class="switcher-style-head">Header Positions:</p>
            <div class="grid grid-cols-3 switcher-style">
              <div class="flex">
                <input
                  type="radio"
                  name="data-header-positions"
                  class="ti-form-radio"
                  id="switcher-header-fixed"
                  checked=""
                />
                <label
                  for="switcher-header-fixed"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                >
                  Fixed</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="data-header-positions"
                  class="ti-form-radio"
                  id="switcher-header-scroll"
                />
                <label
                  for="switcher-header-scroll"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Scrollable
                </label>
              </div>
            </div>
          </div>
          <div class="">
            <p class="switcher-style-head">Loader:</p>
            <div class="grid grid-cols-3 switcher-style">
              <div class="flex">
                <input
                  type="radio"
                  name="page-loader"
                  class="ti-form-radio"
                  id="switcher-loader-enable"
                  checked=""
                />
                <label
                  for="switcher-loader-enable"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                >
                  Enable</label
                >
              </div>
              <div class="flex">
                <input
                  type="radio"
                  name="page-loader"
                  class="ti-form-radio"
                  id="switcher-loader-disable"
                />
                <label
                  for="switcher-loader-disable"
                  class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                  >Disable
                </label>
              </div>
            </div>
          </div>
        </div>
        <div
          id="switcher-2"
          class="hidden"
          role="tabpanel"
          aria-labelledby="switcher-item-2"
        >
          <div class="theme-colors">
            <p class="switcher-style-head">Menu Colors:</p>
            <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
              <div
                class="hs-tooltip ti-main-tooltip ti-form-radio switch-select"
              >
                <input
                  class="hs-tooltip-toggle ti-form-radio color-input color-white"
                  type="radio"
                  name="menu-colors"
                  id="switcher-menu-light"
                />
                <span
                  class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                  role="tooltip"
                  data-popper-reference-hidden=""
                  data-popper-escaped=""
                  data-popper-placement="bottom"
                  style="
                    position: fixed;
                    inset: 0px auto auto 0px;
                    margin: 0px;
                    transform: translate3d(0px, 5px, 0px);
                  "
                >
                  Light Menu
                </span>
              </div>
              <div
                class="hs-tooltip ti-main-tooltip ti-form-radio switch-select"
              >
                <input
                  class="hs-tooltip-toggle ti-form-radio color-input color-dark"
                  type="radio"
                  name="menu-colors"
                  id="switcher-menu-dark"
                  checked=""
                />
                <span
                  class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                  role="tooltip"
                  data-popper-reference-hidden=""
                  data-popper-escaped=""
                  data-popper-placement="bottom"
                  style="
                    position: fixed;
                    inset: 0px auto auto 0px;
                    margin: 0px;
                    transform: translate3d(0px, 5px, 0px);
                  "
                >
                  Dark Menu
                </span>
              </div>
              <div
                class="hs-tooltip ti-main-tooltip ti-form-radio switch-select"
              >
                <input
                  class="hs-tooltip-toggle ti-form-radio color-input color-primary"
                  type="radio"
                  name="menu-colors"
                  id="switcher-menu-primary"
                />
                <span
                  class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                  role="tooltip"
                  data-popper-reference-hidden=""
                  data-popper-escaped=""
                  data-popper-placement="bottom"
                  style="
                    position: fixed;
                    inset: 0px auto auto 0px;
                    margin: 0px;
                    transform: translate3d(0px, 5px, 0px);
                  "
                >
                  Color Menu
                </span>
              </div>
              <div
                class="hs-tooltip ti-main-tooltip ti-form-radio switch-select"
              >
                <input
                  class="hs-tooltip-toggle ti-form-radio color-input color-gradient"
                  type="radio"
                  name="menu-colors"
                  id="switcher-menu-gradient"
                />
                <span
                  class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                  role="tooltip"
                  data-popper-reference-hidden=""
                  data-popper-escaped=""
                  data-popper-placement="bottom"
                  style="
                    position: fixed;
                    inset: 0px auto auto 0px;
                    margin: 0px;
                    transform: translate3d(0px, 5px, 0px);
                  "
                >
                  Gradient Menu
                </span>
              </div>
              <div
                class="hs-tooltip ti-main-tooltip ti-form-radio switch-select"
              >
                <input
                  class="hs-tooltip-toggle ti-form-radio color-input color-transparent"
                  type="radio"
                  name="menu-colors"
                  id="switcher-menu-transparent"
                />
                <span
                  class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs !font-medium !text-white shadow-sm dark:!bg-black"
                  role="tooltip"
                  data-popper-reference-hidden=""
                  data-popper-escaped=""
                  data-popper-placement="bottom"
                  style="
                    position: fixed;
                    inset: 0px auto auto 0px;
                    margin: 0px;
                    transform: translate3d(0px, 5px, 0px);
                  "
                >
                  Transparent Menu
                </span>
              </div>
            </div>
            <div
              class="px-4 text-textmuted dark:text-textmuted/50 text-[.6875rem]"
            >
              <b class="me-2 font-normal">Note:</b>If you want to change color
              Menu dynamically change from below Theme Primary color picker.
            </div>
          </div>
          <div class="theme-colors">
            <p class="switcher-style-head">Header Colors:</p>
            <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
              <div
                class="hs-tooltip ti-main-tooltip ti-form-radio switch-select"
              >
                <input
                  class="hs-tooltip-toggle ti-form-radio color-input color-white !border"
                  type="radio"
                  name="header-colors"
                  id="switcher-header-light"
                  checked=""
                />
                <span
                  class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                  role="tooltip"
                  data-popper-reference-hidden=""
                  data-popper-escaped=""
                  data-popper-placement="bottom"
                  style="
                    position: fixed;
                    inset: 0px auto auto 0px;
                    margin: 0px;
                    transform: translate3d(0px, 5px, 0px);
                  "
                >
                  Light Header
                </span>
              </div>
              <div
                class="hs-tooltip ti-main-tooltip ti-form-radio switch-select"
              >
                <input
                  class="hs-tooltip-toggle ti-form-radio color-input color-dark"
                  type="radio"
                  name="header-colors"
                  id="switcher-header-dark"
                />
                <span
                  class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                  role="tooltip"
                  data-popper-reference-hidden=""
                  data-popper-escaped=""
                  data-popper-placement="bottom"
                  style="
                    position: fixed;
                    inset: 0px auto auto 0px;
                    margin: 0px;
                    transform: translate3d(0px, 5px, 0px);
                  "
                >
                  Dark Header
                </span>
              </div>
              <div
                class="hs-tooltip ti-main-tooltip ti-form-radio switch-select"
              >
                <input
                  class="hs-tooltip-toggle ti-form-radio color-input color-primary"
                  type="radio"
                  name="header-colors"
                  id="switcher-header-primary"
                />
                <span
                  class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                  role="tooltip"
                  data-popper-reference-hidden=""
                  data-popper-escaped=""
                  data-popper-placement="bottom"
                  style="
                    position: fixed;
                    inset: 0px auto auto 0px;
                    margin: 0px;
                    transform: translate3d(0px, 5px, 0px);
                  "
                >
                  Color Header
                </span>
              </div>
              <div
                class="hs-tooltip ti-main-tooltip ti-form-radio switch-select"
              >
                <input
                  class="hs-tooltip-toggle ti-form-radio color-input color-gradient"
                  type="radio"
                  name="header-colors"
                  id="switcher-header-gradient"
                />
                <span
                  class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                  role="tooltip"
                  data-popper-reference-hidden=""
                  data-popper-escaped=""
                  data-popper-placement="bottom"
                  style="
                    position: fixed;
                    inset: 0px auto auto 0px;
                    margin: 0px;
                    transform: translate3d(0px, 5px, 0px);
                  "
                >
                  Gradient Header
                </span>
              </div>
              <div
                class="hs-tooltip ti-main-tooltip ti-form-radio switch-select"
              >
                <input
                  class="hs-tooltip-toggle ti-form-radio color-input color-transparent"
                  type="radio"
                  name="header-colors"
                  id="switcher-header-transparent"
                />
                <span
                  class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                  role="tooltip"
                  data-popper-reference-hidden=""
                  data-popper-escaped=""
                  data-popper-placement="bottom"
                  style="
                    position: fixed;
                    inset: 0px auto auto 0px;
                    margin: 0px;
                    transform: translate3d(0px, 5px, 0px);
                  "
                >
                  Transparent Header
                </span>
              </div>
            </div>
            <div
              class="px-4 text-textmuted dark:text-textmuted/50 text-[.6875rem]"
            >
              <b class="me-2 !font-normal">Note:</b>If you want to change color
              Header dynamically change from below Theme Primary color picker.
            </div>
          </div>
          <div class="theme-colors">
            <p class="switcher-style-head">Theme Primary:</p>
            <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio color-input color-primary-1"
                  type="radio"
                  name="theme-primary"
                  id="switcher-primary"
                />
              </div>
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio color-input color-primary-2"
                  type="radio"
                  name="theme-primary"
                  id="switcher-primary1"
                />
              </div>
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio color-input color-primary-3"
                  type="radio"
                  name="theme-primary"
                  id="switcher-primary2"
                />
              </div>
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio color-input color-primary-4"
                  type="radio"
                  name="theme-primary"
                  id="switcher-primary3"
                />
              </div>
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio color-input color-primary-5"
                  type="radio"
                  name="theme-primary"
                  id="switcher-primary4"
                />
              </div>
              <div
                class="ti-form-radio switch-select ps-0 mt-1 color-primary-light"
              >
                <div class="theme-container-primary">
                  <button class="">nano</button>
                </div>
                <div class="pickr-container-primary">
                  <div class="pickr">
                    <button
                      type="button"
                      class="pcr-button"
                      role="button"
                      aria-label="toggle color picker dialog"
                      style="--pcr-color: rgba(92, 103, 247, 1)"
                    ></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="theme-colors">
            <p class="switcher-style-head">Theme Background:</p>
            <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio color-input color-bg-1"
                  type="radio"
                  name="theme-background"
                  id="switcher-background"
                />
              </div>
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio color-input color-bg-2"
                  type="radio"
                  name="theme-background"
                  id="switcher-background1"
                />
              </div>
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio color-input color-bg-3"
                  type="radio"
                  name="theme-background"
                  id="switcher-background2"
                />
              </div>
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio color-input color-bg-4"
                  type="radio"
                  name="theme-background"
                  id="switcher-background3"
                />
              </div>
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio color-input color-bg-5"
                  type="radio"
                  name="theme-background"
                  id="switcher-background4"
                />
              </div>
              <div
                class="ti-form-radio switch-select ps-0 mt-1 color-bg-transparent"
              >
                <div class="theme-container-background hidden">
                  <button>nano</button>
                </div>
                <div class="pickr-container-background">
                  <div class="pickr">
                    <button
                      type="button"
                      class="pcr-button"
                      role="button"
                      aria-label="toggle color picker dialog"
                      style="--pcr-color: rgba(92, 103, 247, 1)"
                    ></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="menu-image theme-colors">
            <p class="switcher-style-head">Menu With Background Image:</p>
            <div
              class="flex switcher-style space-x-3 rtl:space-x-reverse flex-wrap gap-3"
            >
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio bgimage-input bg-img1"
                  type="radio"
                  name="theme-images"
                  id="switcher-bg-img"
                />
              </div>
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio bgimage-input bg-img2"
                  type="radio"
                  name="theme-images"
                  id="switcher-bg-img1"
                />
              </div>
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio bgimage-input bg-img3"
                  type="radio"
                  name="theme-images"
                  id="switcher-bg-img2"
                />
              </div>
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio bgimage-input bg-img4"
                  type="radio"
                  name="theme-images"
                  id="switcher-bg-img3"
                />
              </div>
              <div class="ti-form-radio switch-select">
                <input
                  class="ti-form-radio bgimage-input bg-img5"
                  type="radio"
                  name="theme-images"
                  id="switcher-bg-img4"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="ti-offcanvas-footer sm:flex justify-between">
        <a
          href="https://1.envato.market/9LxNGy"
          target="_blank"
          class="ti-btn ti-btn-primary m-1"
          >Buy Now</a
        >
        <a
          href="https://1.envato.market/MGEaN"
          target="_blank"
          class="ti-btn ti-btn-secondary m-1"
          >Our Portfolio</a
        >
        <a
          href="javascript:void(0);"
          id="reset-all"
          class="ti-btn ti-btn-danger m-1"
          >Reset</a
        >
      </div>
    </div>
    <!-- ========== END Switcher  ========== -->
    <!-- Loader -->
    <div id="loader" class="loader-disable">
      <img src="assets/images/media/loader.svg" alt="" />
    </div>
    <!-- Loader -->
    <div class="page">
    
  <!-- app-header -->
      <header class="app-header sticky" id="header">
        <!-- Start::main-header-container -->
        <div class="main-header-container container-fluid">
          <!-- Start::header-content-left -->
          <div class="header-content-left">
            <!-- Start::header-element -->
            <div class="header-element">
              <div class="horizontal-logo">
                <a href="index.php" class="header-logo">
                  <img
                    src="assets/images/brand-logos/desktop-logo.png"
                    alt="logo"
                    class="desktop-logo"
                  />
                  <img
                    src="assets/images/brand-logos/toggle-dark.png"
                    alt="logo"
                    class="toggle-dark"
                  />
                  <img
                    src="assets/images/brand-logos/desktop-dark.png"
                    alt="logo"
                    class="desktop-dark"
                  />
                  <img
                    src="assets/images/brand-logos/toggle-logo.png"
                    alt="logo"
                    class="toggle-logo"
                  />
                  <img
                    src="assets/images/brand-logos/toggle-white.png"
                    alt="logo"
                    class="toggle-white"
                  />
                  <img
                    src="assets/images/brand-logos/desktop-white.png"
                    alt="logo"
                    class="desktop-white"
                  />
                </a>
              </div>
            </div>
            <!-- End::header-element -->
            <!-- Start::header-element -->
            <div class="header-element mx-lg-0">
              <a
                aria-label="Hide Sidebar"
                class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle"
                data-bs-toggle="sidebar"
                href="javascript:void(0);"
                ><span></span
              ></a>
            </div>
            <!-- End::header-element -->
            <!-- Start::header-element -->
            <div
              class="header-element header-search md:!block !hidden my-auto auto-complete-search"
            >
              <!-- Start::header-link -->
              <div
                class="autoComplete_wrapper"
                role="combobox"
                aria-owns="autoComplete_list_1"
                aria-haspopup="true"
                aria-expanded="false"
                >
                <input
                  type="text"
                  class="header-search-bar form-control"
                  id="header-search"
                  placeholder="Search anything here ..."
                  autocomplete="off"
                  autocapitalize="off"
                  aria-controls="autoComplete_list_1"
                  aria-autocomplete="both"
                />
                <ul id="autoComplete_list_1" role="listbox" hidden=""></ul>
              </div>
              <a
                aria-label="anchor"
                href="javascript:void(0);"
                class="header-search-icon border-0"
              >
                <i class="ri-search-line"></i>
              </a>
              <!-- End::header-link -->
            </div>
            <!-- End::header-element -->
          </div>
          <!-- End::header-content-left -->
          <!-- Start::header-content-right -->
          <ul class="header-content-right">
            <!-- Start::header-element -->
            <li class="header-element md:!hidden block">
              <a
                aria-label="anchor"
                href="javascript:void(0);"
                class="header-link"
                data-bs-toggle="modal"
                data-hs-overlay="#header-responsive-search"
              >
                <!-- Start::header-link-icon -->
                <i class="bi bi-search header-link-icon"></i>
                <!-- End::header-link-icon -->
              </a>
            </li>
            <!-- End::header-element -->
            <!-- Start::header-element -->
            <!-- <li
              class="header-element country-selector hs-dropdown ti-dropdown hidden sm:block [--placement:bottom-right] rtl:[--placement:bottom-left]"
              >
              <div
                class="ti-dropdown-divider divide-y divide-gray-200 dark:divide-white/10"
              ></div>
              Start::header-link|dropdown-toggle
              <a
                aria-label="anchor"
                href="javascript:void(0);"
                class="header-link hs-dropdown-toggle ti-dropdown-toggle"
                data-bs-auto-close="outside"
                data-bs-toggle="dropdown"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-6 h-6 header-link-icon"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802"
                  ></path>
                </svg>
              </a>
              End::header-link|dropdown-toggle
              <ul
                class="main-header-dropdown hs-dropdown-menu ti-dropdown-menu min-w-[10rem] hidden"
                data-popper-placement="none"
                role="menu"
              >
                <li>
                  <a
                    class="ti-dropdown-item flex items-center"
                    href="javascript:void(0);"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <span
                          class="avatar avatar-rounded avatar-xs leading-none me-2"
                        >
                          <img
                            src="assets/images/flags/us_flag.jpg"
                            alt="img"
                          />
                        </span>
                        English
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a
                    class="ti-dropdown-item flex items-center"
                    href="javascript:void(0);"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <span
                          class="avatar avatar-rounded avatar-xs leading-none me-2"
                        >
                          <img
                            src="assets/images/flags/spain_flag.jpg"
                            alt="img"
                          />
                        </span>
                        español
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a
                    class="ti-dropdown-item flex items-center"
                    href="javascript:void(0);"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <span
                          class="avatar avatar-rounded avatar-xs leading-none me-2"
                        >
                          <img
                            src="assets/images/flags/french_flag.jpg"
                            alt="img"
                          />
                        </span>
                        français
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a
                    class="ti-dropdown-item flex items-center"
                    href="javascript:void(0);"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <span
                          class="avatar avatar-rounded avatar-xs leading-none me-2"
                        >
                          <img
                            src="assets/images/flags/uae_flag.jpg"
                            alt="img"
                          />
                        </span>
                        عربي
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a
                    class="ti-dropdown-item flex items-center"
                    href="javascript:void(0);"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <span
                          class="avatar avatar-rounded avatar-xs leading-none me-2"
                        >
                          <img
                            src="assets/images/flags/germany_flag.jpg"
                            alt="img"
                          />
                        </span>
                        Deutsch
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a
                    class="ti-dropdown-item flex items-center"
                    href="javascript:void(0);"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <span
                          class="avatar avatar-rounded avatar-xs leading-none me-2"
                        >
                          <img
                            src="assets/images/flags/china_flag.jpg"
                            alt="img"
                          />
                        </span>
                        中国人
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a
                    class="ti-dropdown-item flex items-center"
                    href="javascript:void(0);"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <span
                          class="avatar avatar-rounded avatar-xs leading-none me-2"
                        >
                          <img
                            src="assets/images/flags/italy_flag.jpg"
                            alt="img"
                          />
                        </span>
                        Italiano
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a
                    class="ti-dropdown-item flex items-center"
                    href="javascript:void(0);"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <span
                          class="avatar avatar-rounded avatar-xs leading-none me-2"
                        >
                          <img
                            src="assets/images/flags/russia_flag.jpg"
                            alt="img"
                          />
                        </span>
                        Русский
                      </div>
                    </div>
                  </a>
                </li>
              </ul>
            </li> -->
            <!-- End::header-element -->
            <!-- Start::header-element -->
            <!-- light and dark theme -->
            <li
              class="header-element header-theme-mode hidden !items-center sm:block md:!px-[0.5rem] px-2"
              >
              <a
                aria-label="anchor"
                class="hs-dark-mode-active:hidden flex hs-dark-mode group flex-shrink-0 justify-center items-center gap-2 rounded-full font-medium transition-all text-xs dark:bg-bgdark dark:hover:bg-black/20 text-textmuted dark:text-textmuted/50 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
                href="javascript:void(0);"
                data-hs-theme-click-value="dark"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-6 h-6 header-link-icon"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"
                  ></path>
                </svg>
              </a>
              <a
                aria-label="anchor"
                class="hs-dark-mode-active:flex hidden hs-dark-mode group flex-shrink-0 justify-center items-center gap-2 rounded-full font-medium text-defaulttextcolor transition-all text-xs dark:bg-bodybg dark:bg-bgdark dark:hover:bg-black/20 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
                href="javascript:void(0);"
                data-hs-theme-click-value="light"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-6 h-6 header-link-icon"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                  ></path>
                </svg>
              </a>
            </li>
            <!-- End light and dark theme -->
            <!-- End::header-element -->
            <!-- Start::header-element -->
            <!-- <li
              class="header-element cart-dropdown hs-dropdown ti-dropdown [--auto-close:inside]"
              >
              
              <a
                aria-label="anchor"
                href="javascript:void(0);"
                class="header-link hs-dropdown-toggle ti-dropdown-toggle"
                data-bs-auto-close="outside"
                data-bs-toggle="dropdown"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-6 h-6 header-link-icon"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                  ></path>
                </svg>
                <span
                  class="badge bg-secondary rounded-full header-icon-badge text-white"
                  id="cart-icon-badge"
                  >5</span
                >
              </a>
             
              <div
                class="main-header-dropdown hs-dropdown-menu ti-dropdown-menu hidden"
                data-popper-placement="none"
                role="menu"
              >
                <div class="p-4">
                  <div class="flex items-center justify-between">
                    <p class="mb-0 text-[15px] font-medium">
                      Cart Items<span
                        class="badge bg-primarytint2color text-white ms-1 !py-[0.15rem] rounded-full"
                        id="cart-data"
                        >5</span
                      >
                    </p>
                    <div class="flex items-center gap-2">
                      <span
                        class="text-xs font-medium text-textmuted dark:text-textmuted/50"
                        >Sub Total :
                      </span>
                      <h6 class="mb-0">$740</h6>
                    </div>
                  </div>
                </div>
                <hr class="dropdown-divider" />
                <ul
                  class="list-none mb-0"
                  id="header-cart-items-scroll"
                  data-simplebar="init"
                >
                  <div class="simplebar-wrapper" style="margin: 0px">
                    <div class="simplebar-height-auto-observer-wrapper">
                      <div class="simplebar-height-auto-observer"></div>
                    </div>
                    <div class="simplebar-mask">
                      <div
                        class="simplebar-offset"
                        style="right: 0px; bottom: 0px"
                      >
                        <div
                          class="simplebar-content-wrapper"
                          tabindex="0"
                          role="region"
                          aria-label="scrollable content"
                          style="height: auto; overflow: hidden"
                        >
                          <div class="simplebar-content" style="padding: 0px">
                            <li class="ti-dropdown-item block">
                              <div
                                class="flex items-center cart-dropdown-item gap-4"
                              >
                                <div class="leading-none">
                                  <span class="avatar avatar-xl bg-primary/10">
                                    <img
                                      src="assets/images/ecommerce/png/30.png"
                                      alt="Wireless Headphones"
                                    />
                                  </span>
                                </div>
                                <div class="flex-auto">
                                  <div
                                    class="flex items-center justify-between mb-0"
                                  >
                                    <div class="mb-0 text-[14px] font-medium">
                                      <a href="cart.php"
                                        >Wireless Headphones</a
                                      >
                                      <div class="truncate">
                                        <p
                                          class="mb-0 header-cart-text truncate text-[11px] text-textmuted dark:text-textmuted/50"
                                        >
                                          Wireless freedom with crystal-clear
                                          sound and comfortable
                                        </p>
                                        <h6 class="font-medium mb-0 mt-1">
                                          <span
                                            class="text-success font-normal me-1 text-[11px] inline-block"
                                            >(Qty : 1)</span
                                          >$78
                                        </h6>
                                      </div>
                                    </div>
                                    <div class="text-end">
                                      <a
                                        href="javascript:void(0);"
                                        class="header-cart-remove dropdown-item-close"
                                        aria-label="anchor"
                                        ><i class="ri-close-line"></i
                                      ></a>
                                      <h6 class="font-medium mb-0 mt-3">
                                        <span
                                          class="text-info op-4 font-normal me-1 text-[11px] inline-block"
                                          >Total :</span
                                        >$75
                                      </h6>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </li>
                            <li class="ti-dropdown-item block">
                              <div
                                class="flex items-center cart-dropdown-item gap-4"
                              >
                                <div class="leading-none">
                                  <span class="avatar avatar-xl bg-primary/10">
                                    <img
                                      src="assets/images/ecommerce/png/29.png"
                                      alt="Ladies Hand Bag"
                                    />
                                  </span>
                                </div>
                                <div class="flex-auto">
                                  <div
                                    class="flex items-center justify-between mb-0"
                                  >
                                    <div class="mb-0 text-[14px] font-medium">
                                      <a href="cart.php">Ladies Hand Bag</a>
                                      <div class="truncate">
                                        <p
                                          class="mb-0 header-cart-text truncate text-[11px] text-textmuted dark:text-textmuted/50"
                                        >
                                          Both fashion and functionality.
                                        </p>
                                        <h6 class="font-medium mb-0 mt-1">
                                          <span
                                            class="text-success font-normal me-1 text-[11px] inline-block"
                                            >(Qty : 2)</span
                                          >$15
                                        </h6>
                                      </div>
                                    </div>
                                    <div class="text-end">
                                      <a
                                        href="javascript:void(0);"
                                        class="header-cart-remove dropdown-item-close"
                                        aria-label="anchor"
                                        ><i class="ri-close-line"></i
                                      ></a>
                                      <h6 class="font-medium mb-0 mt-3">
                                        <span
                                          class="text-info op-4 font-normal me-1 text-[11px] inline-block"
                                          >Total :</span
                                        >$30
                                      </h6>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </li>
                            <li class="ti-dropdown-item block">
                              <div
                                class="flex items-center cart-dropdown-item gap-4"
                              >
                                <div class="leading-none">
                                  <span class="avatar avatar-xl bg-primary/10">
                                    <img
                                      src="assets/images/ecommerce/png/32.png"
                                      alt="Alarm Clock"
                                    />
                                  </span>
                                </div>
                                <div class="flex-auto">
                                  <div
                                    class="flex items-center justify-between mb-0"
                                  >
                                    <div class="mb-0 text-[14px] font-medium">
                                      <a href="cart.php">Alarm Clock</a>
                                      <div class="truncate">
                                        <p
                                          class="mb-0 header-cart-text truncate text-[11px] text-textmuted dark:text-textmuted/50"
                                        >
                                          Add natural beauty to your space
                                        </p>
                                        <h6 class="font-medium mb-0 mt-1">
                                          <span
                                            class="text-success font-normal me-1 text-[11px] inline-block"
                                            >(Qty : 1)</span
                                          >$84
                                        </h6>
                                      </div>
                                    </div>
                                    <div class="text-end">
                                      <a
                                        href="javascript:void(0);"
                                        class="header-cart-remove dropdown-item-close"
                                        aria-label="anchor"
                                        ><i class="ri-close-line"></i
                                      ></a>
                                      <h6 class="font-medium mb-0 mt-3">
                                        <span
                                          class="text-info op-4 font-normal me-1 text-[11px] inline-block"
                                          >Total :</span
                                        >$84
                                      </h6>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </li>
                            <li class="ti-dropdown-item block">
                              <div
                                class="flex items-center cart-dropdown-item gap-4"
                              >
                                <div class="leading-none">
                                  <span class="avatar avatar-xl bg-primary/10">
                                    <img
                                      src="assets/images/ecommerce/png/12.png"
                                      alt="Kids' Party Wear Frock"
                                    />
                                  </span>
                                </div>
                                <div class="flex-auto">
                                  <div
                                    class="flex items-center justify-between mb-0"
                                  >
                                    <div class="mb-0 text-[14px] font-medium">
                                      <a href="cart.php"
                                        >Kids' Party Wear Frock</a
                                      >
                                      <div class="truncate">
                                        <p
                                          class="mb-0 header-cart-text truncate text-[11px] text-textmuted dark:text-textmuted/50"
                                        >
                                          Crafted from soft, breathable fabric
                                          and adorned with delightful
                                        </p>
                                        <h6 class="font-medium mb-0 mt-1">
                                          <span
                                            class="text-success font-normal me-1 text-[11px] inline-block"
                                            >(Qty : 1)</span
                                          >$37
                                        </h6>
                                      </div>
                                    </div>
                                    <div class="text-end">
                                      <a
                                        href="javascript:void(0);"
                                        class="header-cart-remove dropdown-item-close"
                                        aria-label="anchor"
                                        ><i class="ri-close-line"></i
                                      ></a>
                                      <h6 class="font-medium mb-0 mt-3">
                                        <span
                                          class="text-info op-4 font-normal me-1 text-[11px] inline-block"
                                          >Total :</span
                                        >$37
                                      </h6>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </li>
                            <li class="ti-dropdown-item block">
                              <div
                                class="flex items-center cart-dropdown-item gap-4"
                              >
                                <div class="leading-none">
                                  <span class="avatar avatar-xl bg-primary/10">
                                    <img
                                      src="assets/images/ecommerce/png/16.png"
                                      alt="Smart Watch"
                                    />
                                  </span>
                                </div>
                                <div class="flex-auto">
                                  <div
                                    class="flex items-center justify-between mb-0"
                                  >
                                    <div class="mb-0 text-[14px] font-medium">
                                      <a href="cart.php"
                                        >Advanced Smart Watch</a
                                      >
                                      <div class="truncate">
                                        <p
                                          class="mb-0 header-cart-text truncate text-[11px] text-textmuted dark:text-textmuted/50"
                                        >
                                          ultimate in wearable
                                          technology,combining cutting-edge
                                        </p>
                                        <h6 class="font-medium mb-0 mt-1">
                                          <span
                                            class="text-success font-normal me-1 text-[11px] inline-block"
                                            >(Qty : 2)</span
                                          >$29
                                        </h6>
                                      </div>
                                    </div>
                                    <div class="text-end">
                                      <a
                                        href="javascript:void(0);"
                                        class="header-cart-remove dropdown-item-close"
                                        aria-label="anchor"
                                        ><i class="ri-close-line"></i
                                      ></a>
                                      <h6 class="font-medium mb-0 mt-3">
                                        <span
                                          class="text-info op-4 font-normal me-1 text-[11px] inline-block"
                                          >Total :</span
                                        >$48
                                      </h6>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </li>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div
                      class="simplebar-placeholder"
                      style="width: 0px; height: 0px"
                    ></div>
                  </div>
                  <div
                    class="simplebar-track simplebar-horizontal"
                    style="visibility: hidden"
                  >
                    <div
                      class="simplebar-scrollbar"
                      style="width: 0px; display: none"
                    ></div>
                  </div>
                  <div
                    class="simplebar-track simplebar-vertical"
                    style="visibility: hidden"
                  >
                    <div
                      class="simplebar-scrollbar"
                      style="height: 0px; display: none"
                    ></div>
                  </div>
                </ul>
                <div class="p-4 empty-header-item border-t grid items-center">
                  <a
                    href="checkout.php"
                    class="ti-btn ti-btn-primary btn-wave text-center waves-effect waves-light"
                    >Proceed to checkout</a
                  >
                </div>
                <div class="p-[3rem] empty-item hidden">
                  <div class="text-center">
                    <span
                      class="avatar avatar-xl avatar-rounded bg-primary/10 !text-primary"
                    >
                      <i class="ri-shopping-cart-2-line fs-2"></i>
                    </span>
                    <h6 class="font-medium mb-1 mt-3">Your Cart is Empty</h6>
                    <span class="mb-3 font-normal text-[13px] block"
                      >Add some items to make me happy :)</span
                    >
                    <a
                      href="products.php"
                      class="ti-btn bg-primarytint1color text-white btn-wave ti-btn-sm m-1 waves-effect waves-light"
                      data-abc="true"
                      >continue shopping <i class="bi bi-arrow-right ms-1"></i
                    ></a>
                  </div>
                </div>
              </div>
              
            </li> -->
            <!-- End::header-element -->
            <!-- Start::header-element -->
           <li
              class="header-element notifications-dropdown !hidden xl:!block hs-dropdown ti-dropdown [--auto-close:inside]"
              >
              <!-- Start::header-link|dropdown-toggle -->
              <a
                aria-label="anchor"
                href="javascript:void(0);"
                class="header-link hs-dropdown-toggle ti-dropdown-toggle"
                data-bs-toggle="dropdown"
                data-bs-auto-close="outside"
                id="messageDropdown"
                aria-expanded="false"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-6 h-6 header-link-icon"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5"
                  ></path>
                </svg>
                <span
                  class="header-icon-pulse bg-primarytint2color rounded pulse pulse-secondary"
                ></span>
              </a>
              <!-- End::header-link|dropdown-toggle -->
              <!-- Start::main-header-dropdown -->
              <div
                class="main-header-dropdown hs-dropdown-menu ti-dropdown-menu hidden"
                data-popper-placement="none"
                role="menu"
                >
                <div class="p-4">
                  <div class="flex items-center justify-between">
                    <p class="mb-0 text-[15px] font-medium">Notifications</p>
                    <span
                      class="badge bg-secondary text-white rounded-sm"
                      id="notifiation-data"
                      >5 Unread</span
                    >
                  </div>
                </div>
                <div class="dropdown-divider"></div>
                <ul
                  class="list-none mb-0"
                  id="header-notification-scroll"
                  data-simplebar="init"
                >
                  <div class="simplebar-wrapper" style="margin: 0px">
                    <div class="simplebar-height-auto-observer-wrapper">
                      <div class="simplebar-height-auto-observer"></div>
                    </div>
                    <div class="simplebar-mask">
                      <div
                        class="simplebar-offset"
                        style="right: 0px; bottom: 0px"
                      >
                        <div
                          class="simplebar-content-wrapper"
                          tabindex="0"
                          role="region"
                          aria-label="scrollable content"
                          style="height: auto; overflow: hidden"
                        >
                          <div class="simplebar-content" style="padding: 0px">
                            <li class="ti-dropdown-item block">
                              <div class="flex items-center">
                                <div class="pe-2 leading-none">
                                  <span
                                    class="avatar avatar-md avatar-rounded bg-primary"
                                  >
                                    <img
                                      src="assets/images/faces/1.jpg"
                                      alt="user1"
                                    />
                                  </span>
                                </div>
                                <div
                                  class="grow flex items-center justify-between"
                                >
                                  <div>
                                    <p class="mb-0 font-medium">
                                      <a href="chat.php">New Messages</a>
                                    </p>
                                    <div
                                      class="text-textmuted dark:text-textmuted/50 font-normal text-xs header-notification-text truncate"
                                    >
                                      Jane Sam sent you a message.
                                    </div>
                                    <div
                                      class="font-normal text-[10px] text-textmuted dark:text-textmuted/50 op-8"
                                    >
                                      Now
                                    </div>
                                  </div>
                                  <div>
                                    <a
                                      aria-label="anchor"
                                      href="javascript:void(0);"
                                      class="min-w-fit-content dropdown-item-close1"
                                    >
                                      <i class="ri-close-line"></i>
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </li>
                            <li class="ti-dropdown-item block">
                              <div class="flex items-center">
                                <div class="pe-2 leading-none">
                                  <span
                                    class="avatar avatar-md bg-primary avatar-rounded text-xl"
                                  >
                                    <i
                                      class="fe fe-shopping-cart leading-none"
                                    ></i>
                                  </span>
                                </div>
                                <div
                                  class="grow flex items-center justify-between"
                                >
                                  <div>
                                    <p class="mb-0 font-medium">
                                      <a href="chat.php">Order Updates</a>
                                    </p>
                                    <div
                                      class="text-textmuted dark:text-textmuted/50 font-normal text-xs header-notification-text truncate"
                                    >
                                      Order
                                      <span class="text-primarytint1color"
                                        >#54321</span
                                      >
                                      has been shipped.
                                    </div>
                                    <div
                                      class="font-normal text-[10px] text-textmuted dark:text-textmuted/50 op-8"
                                    >
                                      2 hours ago
                                    </div>
                                  </div>
                                  <div>
                                    <a
                                      aria-label="anchor"
                                      href="javascript:void(0);"
                                      class="min-w-fit-content dropdown-item-close1"
                                    >
                                      <i class="ri-close-line"></i>
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </li>
                            <li class="ti-dropdown-item block">
                              <div class="flex items-center">
                                <div class="pe-2 leading-none">
                                  <span
                                    class="avatar avatar-md bg-orange avatar-rounded"
                                  >
                                    <img
                                      src="assets/images/faces/10.jpg"
                                      alt="user1"
                                    />
                                  </span>
                                </div>
                                <div
                                  class="grow flex items-center justify-between"
                                >
                                  <div>
                                    <p class="mb-0 font-medium">
                                      <a href="chat.php">Comment on Post</a>
                                    </p>
                                    <div
                                      class="text-textmuted dark:text-textmuted/50 font-normal text-xs header-notification-text truncate"
                                    >
                                      Reacted:
                                      <span class="text-primary3"
                                        >John Richard</span
                                      >
                                      on your next purchase!
                                    </div>
                                    <div
                                      class="font-normal text-[10px] text-textmuted dark:text-textmuted/50 op-8"
                                    >
                                      2 hours ago
                                    </div>
                                  </div>
                                  <div>
                                    <a
                                      aria-label="anchor"
                                      href="javascript:void(0);"
                                      class="min-w-fit-content dropdown-item-close1"
                                    >
                                      <i class="ri-close-line"></i>
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </li>
                            <li class="ti-dropdown-item block">
                              <div class="flex items-center">
                                <div class="pe-2 leading-none">
                                  <span
                                    class="avatar avatar-md bg-success avatar-rounded"
                                  >
                                    <img
                                      src="assets/images/faces/11.jpg"
                                      alt="user1"
                                    />
                                  </span>
                                </div>
                                <div
                                  class="grow flex items-center justify-between"
                                >
                                  <div>
                                    <p class="mb-0 font-medium">
                                      <a href="chat.php">Follow Request</a>
                                    </p>
                                    <div
                                      class="text-textmuted dark:text-textmuted/50 font-normal text-xs header-notification-text truncate"
                                    >
                                      <span class="text-info">Kelin Brown</span>
                                      has sent you the request.
                                    </div>
                                    <div
                                      class="font-normal text-[10px] text-textmuted dark:text-textmuted/50 op-8"
                                    >
                                      1 Day ago
                                    </div>
                                  </div>
                                  <div>
                                    <a
                                      aria-label="anchor"
                                      href="javascript:void(0);"
                                      class="min-w-fit-content dropdown-item-close1"
                                    >
                                      <i class="ri-close-line"></i>
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </li>
                            <li class="ti-dropdown-item block">
                              <div class="flex items-center">
                                <div class="pe-2 leading-none">
                                  <span
                                    class="avatar avatar-md bg-primarytint2color avatar-rounded"
                                  >
                                    <i
                                      class="ri-gift-line leading-none text-[1rem]"
                                    ></i>
                                  </span>
                                </div>
                                <div
                                  class="grow flex items-center justify-between"
                                >
                                  <div>
                                    <p class="mb-0 font-medium">
                                      <a href="chat.php">Exclusive Offers</a>
                                    </p>
                                    <div
                                      class="text-textmuted dark:text-textmuted/50 font-normal text-xs header-notification-text truncate"
                                    >
                                      Enjoy<span class="text-success"
                                        >20% off</span
                                      >
                                      on your next purchase!
                                    </div>
                                    <div
                                      class="font-normal text-[10px] text-textmuted dark:text-textmuted/50 op-8"
                                    >
                                      5 hours ago
                                    </div>
                                  </div>
                                  <div>
                                    <a
                                      aria-label="anchor"
                                      href="javascript:void(0);"
                                      class="min-w-fit-content dropdown-item-close1"
                                    >
                                      <i class="ri-close-line"></i>
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </li>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div
                      class="simplebar-placeholder"
                      style="width: 0px; height: 0px"
                    ></div>
                  </div>
                  <div
                    class="simplebar-track simplebar-horizontal"
                    style="visibility: hidden"
                  >
                    <div
                      class="simplebar-scrollbar"
                      style="width: 0px; display: none"
                    ></div>
                  </div>
                  <div
                    class="simplebar-track simplebar-vertical"
                    style="visibility: hidden"
                  >
                    <div
                      class="simplebar-scrollbar"
                      style="height: 0px; display: none"
                    ></div>
                  </div>
                </ul>
                <div class="p-4 empty-header-item1 border-t">
                  <div class="grid">
                    <a
                      href="javascript:void(0);"
                      class="ti-btn ti-btn-primary btn-wave waves-effect waves-light"
                      >View All</a
                    >
                  </div>
                </div>
                <div class="p-[3rem] empty-item1 hidden">
                  <div class="text-center">
                    <span
                      class="avatar avatar-xl avatar-rounded bg-secondary/10 !text-secondary"
                    >
                      <i class="ri-notification-off-line fs-2"></i>
                    </span>
                    <h6 class="font-medium mt-3">No New Notifications</h6>
                  </div>
                </div>
              </div>
              <!-- End::main-header-dropdown -->
            </li> 
            <!-- End::header-element -->
            <!-- Start::header-element -->
            <!-- <li class="header-element header-fullscreen">
              Start::header-link
              <a
                onclick="openFullscreen();"
                href="javascript:void(0);"
                class="header-link"
                aria-label="anchor"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-6 h-6 full-screen-open header-link-icon"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"
                  ></path>
                </svg>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-6 h-6 full-screen-close header-link-icon hidden"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25"
                  ></path>
                </svg>
              </a>
              End::header-link
            </li> -->
            <!-- End::header-element -->
            <!-- Start::header-element -->
            <li class="header-element ti-dropdown hs-dropdown">
              <!-- Start::header-link|dropdown-toggle -->
              <a
                aria-label="anchor"
                href="javascript:void(0);"
                class="header-link hs-dropdown-toggle ti-dropdown-toggle"
                id="mainHeaderProfile"
                data-bs-toggle="dropdown"
                data-bs-auto-close="outside"
                aria-expanded="false"
              >
                <div class="flex items-center">
                  <div>
                    <img
                      src="assets/images/faces/15.jpg"
                      alt="img"
                      class="avatar avatar-sm mb-0"
                    />
                  </div>
                </div>
              </a>
              <!-- End::header-link|dropdown-toggle -->
              <ul
                class="main-header-dropdown hs-dropdown-menu ti-dropdown-menu pt-0 overflow-hidden header-profile-dropdown hidden"
                aria-labelledby="mainHeaderProfile"
                role="menu"
              >
                <li>
                  <div
                    class="ti-dropdown-item text-center border-b border-defaultborder dark:border-defaultborder/10 block"
                  >
                    <span> <?php echo $_SESSION['user']['user_name']; ?> </span>
                    <span
                      class="block text-xs text-textmuted dark:text-textmuted/50"
                      ><?php echo $_SESSION['user']['email']; ?></span
                    >
                  </div>
                </li>
                <li>
                  <a
                    class="ti-dropdown-item flex items-center"
                    href="profile.php"
                    ><i
                      class="ri-user-line p-1 rounded-full bg-primary/10 text-primary me-2 text-[1rem]"
                    ></i
                    >Profile</a
                  >
                </li>
                <li>
                  <a class="ti-dropdown-item flex items-center" href="mail.php"
                    ><i
                      class="ri-mail-line p-1 rounded-full bg-primary/10 text-primary me-2 text-[1rem]"
                    ></i
                    >Mail Inbox</a
                  >
                </li>
               
                <li>
                  <a
                    class="ti-dropdown-item flex items-center"
                    href="settings.php"
                    ><i
                      class="ri-settings-line p-1 rounded-full bg-primary/10 text-primary ings me-2 text-[1rem]"
                    ></i
                    >Settings</a
                  >
                </li>
                <li
                  class="border-t border-defaultborder dark:border-defaultborder/10 bg-light"
                >
                  <a class="ti-dropdown-item flex items-center" href="chat.php"
                    ><i
                      class="ri-question-line p-1 rounded-full bg-primary/10 text-primary set me-2 text-[1rem]"
                    ></i
                    >Help</a
                  >
                </li>
                <li>
                  <span
                    class="ti-dropdown-item flex items-center cursor-pointer"
                    id="logout-btn"
                    ><i
                      class="ri-logout-box-line p-1 rounded-full bg-primary/10 text-primary ut me-2 text-[1rem]"
                    ></i
                    >Log Out</span
                  >
                </li>
              </ul>
            </li>
            <!-- End::header-element -->
            <!-- Start::header-element -->
            <!-- <li class="header-element">
              Start::header-link|switcher-icon
              <a
                href="javascript:void(0);"
                class="header-link switcher-icon"
                data-hs-overlay="#hs-overlay-switcher"
                aria-label="anchor"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-6 h-6 header-link-icon"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"
                  ></path>
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                  ></path>
                </svg>
              </a>
              End::header-link|switcher-icon
            </li> -->
            <!-- End::header-element -->
          </ul>
          <!-- End::header-content-right -->
        </div>
        <!-- End::main-header-container -->
      </header>
      <!-- /app-header -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You will be logged out of your session.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, log out!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'config/logout.php';
                    }
                });
            });
        }
    });
</script>