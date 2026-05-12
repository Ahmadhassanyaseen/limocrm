<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>

<?php
$vehicleId = $_GET['id'] ?? '';
if (empty($vehicleId)) {
    header('Location: vehicles.php');
    exit;
}
?>

<style>
  /* === Modern Redesign Styles === */
  .vd-hero {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    background: linear-gradient(135deg, var(--primary-color) 0%, rgba(var(--primary-rgb), 0.8) 100%);
    min-height: 280px;
    display: flex;
    align-items: flex-end;
  }
  .dark .vd-hero {
    background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.3) 0%, rgba(var(--primary-rgb), 0.15) 100%);
  }
  .vd-hero-image {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.25;
  }
  .dark .vd-hero-image { opacity: 0.15; }
  .vd-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 50%, rgba(0,0,0,0.1) 100%);
  }
  .vd-hero-content {
    position: relative;
    z-index: 10;
    padding: 32px;
    width: 100%;
  }
  .vd-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 600;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.25);
    margin-right: 8px;
    margin-bottom: 8px;
  }
  .vd-hero-badge.status-active { background: rgba(34,197,94,0.3); border-color: rgba(34,197,94,0.5); }
  .vd-hero-badge.status-inactive { background: rgba(239,68,68,0.3); border-color: rgba(239,68,68,0.5); }
  .vd-hero-badge.status-maintenance { background: rgba(251,191,36,0.3); border-color: rgba(251,191,36,0.5); }
  .vd-hero-title {
    font-size: clamp(1.5rem, 4vw, 2.25rem);
    font-weight: 700;
    color: #fff;
    margin: 12px 0 8px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
  }
  .vd-hero-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    color: rgba(255,255,255,0.85);
    font-size: 14px;
  }
  .vd-hero-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .vd-action-bar {
    display: flex;
    gap: 10px;
    margin-top: 16px;
    flex-wrap: wrap;
  }

  /* Modern Card System */
  .vd-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(15,23,42,0.08);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .vd-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
  }
  .dark .vd-card {
    background: rgba(255,255,255,0.03);
    border-color: rgba(255,255,255,0.08);
  }
  .dark .vd-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
  }
  .vd-card-header {
    padding: 18px 20px;
    border-bottom: 1px solid rgba(15,23,42,0.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }
  .dark .vd-card-header {
    border-bottom-color: rgba(255,255,255,0.06);
  }
  .vd-card-title {
    font-size: 15px;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
  }
  .dark .vd-card-title { color: rgba(255,255,255,0.95); }
  .vd-card-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    background: rgba(var(--primary-rgb), 0.1);
    color: rgb(var(--primary-rgb));
  }
  .vd-card-body {
    padding: 20px;
  }

  /* Stat Cards */
  .vd-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
  }
  .vd-stat-card {
    background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.08) 0%, rgba(var(--primary-rgb), 0.03) 100%);
    border-radius: 14px;
    padding: 16px;
    text-align: center;
    border: 1px solid rgba(var(--primary-rgb), 0.12);
    transition: all 0.2s;
  }
  .dark .vd-stat-card {
    background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.15) 0%, rgba(var(--primary-rgb), 0.05) 100%);
    border-color: rgba(var(--primary-rgb), 0.2);
  }
  .vd-stat-card:hover {
    transform: scale(1.02);
    border-color: rgba(var(--primary-rgb), 0.3);
  }
  .vd-stat-icon {
    font-size: 24px;
    margin-bottom: 8px;
  }
  .vd-stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
  }
  .dark .vd-stat-value { color: rgba(255,255,255,0.95); }
  .vd-stat-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: rgba(15,23,42,0.5);
    margin-top: 4px;
  }
  .dark .vd-stat-label { color: rgba(255,255,255,0.45); }

  /* Feature Tags */
  .vd-feature-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }
  .vd-feature-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    background: rgba(var(--primary-rgb), 0.08);
    color: rgb(var(--primary-rgb));
    border: 1px solid rgba(var(--primary-rgb), 0.15);
    transition: all 0.2s;
  }
  .vd-feature-tag:hover {
    background: rgba(var(--primary-rgb), 0.15);
    transform: translateY(-1px);
  }

  /* Price Display */
  .vd-price-hero {
    background: linear-gradient(135deg, rgb(var(--primary-rgb)) 0%, rgba(var(--primary-rgb), 0.85) 100%);
    border-radius: 16px;
    padding: 24px;
    color: #fff;
    text-align: center;
    margin-bottom: 16px;
  }
  .vd-price-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.85;
    margin-bottom: 4px;
  }
  .vd-price-value {
    font-size: 42px;
    font-weight: 800;
    line-height: 1.1;
  }
  .vd-price-unit {
    font-size: 16px;
    font-weight: 400;
    opacity: 0.8;
  }

  /* Info List */
  .vd-info-list {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
  .vd-info-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px;
    border-radius: 10px;
    transition: background 0.15s;
  }
  .vd-info-item:hover {
    background: rgba(15,23,42,0.03);
  }
  .dark .vd-info-item:hover {
    background: rgba(255,255,255,0.04);
  }
  .vd-info-label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: rgba(15,23,42,0.6);
  }
  .dark .vd-info-label { color: rgba(255,255,255,0.5); }
  .vd-info-label i { font-size: 16px; }
  .vd-info-value {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
  }
  .dark .vd-info-value { color: rgba(255,255,255,0.9); }

  /* Progress Bars */
  .vd-progress-item {
    margin-bottom: 16px;
  }
  .vd-progress-item:last-child { margin-bottom: 0; }
  .vd-progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
  }
  .vd-progress-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 500;
    color: rgba(15,23,42,0.7);
  }
  .dark .vd-progress-label { color: rgba(255,255,255,0.6); }
  .vd-progress-value {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
  }
  .dark .vd-progress-value { color: rgba(255,255,255,0.9); }
  .vd-progress-bar {
    height: 8px;
    background: rgba(15,23,42,0.08);
    border-radius: 9999px;
    overflow: hidden;
  }
  .dark .vd-progress-bar { background: rgba(255,255,255,0.1); }
  .vd-progress-fill {
    height: 100%;
    border-radius: 9999px;
    background: linear-gradient(90deg, rgb(var(--primary-rgb)), rgba(var(--primary-rgb), 0.7));
    transition: width 0.8s ease-out;
  }

  /* Image Gallery Row - Horizontal Scroll */
  .vd-gallery-row {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding-bottom: 8px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
  }
  .vd-gallery-row::-webkit-scrollbar {
    height: 6px;
  }
  .vd-gallery-row::-webkit-scrollbar-track {
    background: rgba(15,23,42,0.06);
    border-radius: 9999px;
  }
  .dark .vd-gallery-row::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.08);
  }
  .vd-gallery-row::-webkit-scrollbar-thumb {
    background: rgba(var(--primary-rgb), 0.3);
    border-radius: 9999px;
  }
  .vd-gallery-row::-webkit-scrollbar-thumb:hover {
    background: rgba(var(--primary-rgb), 0.5);
  }
  .vd-gallery-item-row {
    flex: 0 0 280px;
    height: 180px;
    border-radius: 14px;
    overflow: hidden;
    cursor: pointer;
    position: relative;
    background: rgba(15,23,42,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
    scroll-snap-align: start;
  }
  .vd-gallery-item-row:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
  }
  .dark .vd-gallery-item-row:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
  }
  .vd-gallery-item-row img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .vd-gallery-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
  }
  .vd-gallery-item-row:hover .vd-gallery-overlay {
    background: rgba(0,0,0,0.35);
  }
  .vd-gallery-overlay i {
    color: #fff;
    font-size: 28px;
    opacity: 0;
    transition: opacity 0.2s;
  }
  .vd-gallery-item-row:hover .vd-gallery-overlay i {
    opacity: 1;
  }

  /* Empty State */
  .vd-empty {
    text-align: center;
    padding: 32px 16px;
    color: rgba(15,23,42,0.4);
  }
  .dark .vd-empty { color: rgba(255,255,255,0.35); }
  .vd-empty i { font-size: 36px; margin-bottom: 12px; display: block; }
  .vd-empty p { font-size: 13px; }

  /* Skeleton Loading */
  .vd-skeleton {
    animation: vd-pulse 1.8s ease-in-out infinite;
    background: rgba(15,23,42,0.06);
    border-radius: 8px;
  }
  .dark .vd-skeleton { background: rgba(255,255,255,0.06); }
  @keyframes vd-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
  }

  /* Responsive Grid */
  .vd-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: 1fr;
  }
  @media (min-width: 640px) {
    .vd-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  @media (min-width: 1024px) {
    .vd-grid {
      grid-template-columns: repeat(3, 1fr);
    }
    .vd-grid .vd-full { grid-column: span 1; }
  }
  @media (min-width: 1280px) {
    .vd-grid .vd-full-xl { grid-column: span 2; }
  }

  /* Description */
  .vd-description {
    font-size: 14px;
    line-height: 1.7;
    color: rgba(15,23,42,0.75);
  }
  .dark .vd-description { color: rgba(255,255,255,0.65); }

  /* Quick Actions */
  .vd-quick-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }
  .vd-quick-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    background: rgba(15,23,42,0.05);
    color: rgba(15,23,42,0.75);
    border: 1px solid rgba(15,23,42,0.08);
    transition: all 0.2s;
    cursor: pointer;
  }
  .dark .vd-quick-btn {
    background: rgba(255,255,255,0.05);
    color: rgba(255,255,255,0.7);
    border-color: rgba(255,255,255,0.1);
  }
  .vd-quick-btn:hover {
    background: rgba(var(--primary-rgb), 0.1);
    color: rgb(var(--primary-rgb));
    border-color: rgba(var(--primary-rgb), 0.2);
    transform: translateY(-1px);
  }

  /* Back Button */
  .vd-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.25);
    transition: all 0.2s;
    text-decoration: none;
  }
  .vd-back-btn:hover {
    background: rgba(255,255,255,0.25);
    color: #fff;
    transform: translateX(-2px);
  }
  .dark .vd-back-btn {
    background: rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.2);
  }
  .dark .vd-back-btn:hover {
    background: rgba(255,255,255,0.15);
  }

  /* Sticky Header on Mobile */
  .vd-sticky-header {
    position: sticky;
    top: 0;
    z-index: 100;
    padding: 12px 0;
    background: transparent;
    transition: background 0.3s;
  }
  .vd-sticky-header.scrolled {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(10px);
  }
  .dark .vd-sticky-header.scrolled {
    background: rgba(15,23,42,0.95);
  }

  /* ID Badge */
  .vd-id-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-family: monospace;
    background: rgba(15,23,42,0.06);
    color: rgba(15,23,42,0.5);
  }
  .dark .vd-id-badge {
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.4);
  }

  /* Scroll to Top */
  .vd-scroll-top {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgb(var(--primary-rgb));
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.4);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    z-index: 99;
  }
  .vd-scroll-top.visible {
    opacity: 1;
    visibility: visible;
  }
  .vd-scroll-top:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(var(--primary-rgb), 0.5);
  }
</style>

<div class="main-content app-content">
  <div class="container-fluid">

    <!-- Back Navigation -->
    <div class="mb-4">
      <a href="vehicles.php" class="vd-back-btn">
        <i class="ri-arrow-left-line"></i>
        <span>Back to Fleet</span>
      </a>
    </div>

    <!-- Hero Section (replaced after load) -->
    <div class="vd-hero mb-4" id="vd-hero" style="display:none;">
      <img class="vd-hero-image" id="vd-hero-img" src="" alt="">
      <div class="vd-hero-overlay"></div>
      <div class="vd-hero-content">
        <div id="vd-badges"></div>
        <h1 class="vd-hero-title" id="vd-title"></h1>
        <div class="vd-hero-meta" id="vd-meta"></div>
        <div class="vd-action-bar" id="vd-actions"></div>
      </div>
    </div>

    <!-- Hero Skeleton -->
    <div class="vd-hero mb-4" id="vd-hero-skeleton">
      <div class="vd-hero-overlay" style="background: rgba(0,0,0,0.1);"></div>
      <div class="vd-hero-content">
        <div class="vd-skeleton" style="width:120px;height:28px;border-radius:9999px;margin-bottom:12px;"></div>
        <div class="vd-skeleton" style="width:280px;height:36px;border-radius:12px;margin-bottom:16px;"></div>
        <div class="vd-skeleton" style="width:200px;height:20px;border-radius:8px;"></div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="vd-grid" id="vd-body">

      <!-- Price Card -->
      <div class="vd-card vd-full">
        <div class="vd-card-body" id="vd-pricing">
          <div class="vd-price-hero">
            <div class="vd-price-label">Hourly Rate</div>
            <div class="vd-price-value" id="vd-price-display">$0.00<span class="vd-price-unit">/hr</span></div>
          </div>
          <div class="vd-info-list" id="vd-pricing-list">
            <div class="vd-info-item">
              <div class="vd-skeleton" style="width:140px;height:16px;"></div>
              <div class="vd-skeleton" style="width:50px;height:16px;"></div>
            </div>
            <div class="vd-info-item">
              <div class="vd-skeleton" style="width:160px;height:16px;"></div>
              <div class="vd-skeleton" style="width:50px;height:16px;"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Card -->
      <div class="vd-card vd-full">
        <div class="vd-card-header">
          <h5 class="vd-card-title">
            <span class="vd-card-icon"><i class="ri-user-settings-line"></i></span>
            Capacity
          </h5>
        </div>
        <div class="vd-card-body" id="vd-capacity">
          <div class="vd-stat-grid">
            <div class="vd-stat-card">
              <div class="vd-stat-icon"><i class="ri-user-line" style="color:var(--primary-color)"></i></div>
              <div class="vd-skeleton" style="width:60px;height:32px;margin:0 auto;"></div>
              <div class="vd-stat-label">Passengers</div>
            </div>
            <div class="vd-stat-card">
              <div class="vd-stat-icon"><i class="ri-briefcase-2-line" style="color:var(--primary-color)"></i></div>
              <div class="vd-skeleton" style="width:60px;height:32px;margin:0 auto;"></div>
              <div class="vd-stat-label">Bags</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Fuel & Commission Card -->
      <div class="vd-card vd-full">
        <div class="vd-card-header">
          <h5 class="vd-card-title">
            <span class="vd-card-icon"><i class="ri-dashboard-2-line"></i></span>
            Performance
          </h5>
        </div>
        <div class="vd-card-body" id="vd-performance">
          <div class="vd-progress-item">
            <div class="vd-progress-header">
              <span class="vd-progress-label"><i class="ri-gas-station-line" style="color:#f59e0b"></i> Fuel Level</span>
              <span class="vd-progress-value" id="vd-fuel-value">0%</span>
            </div>
            <div class="vd-progress-bar">
              <div class="vd-progress-fill" id="vd-fuel-bar" style="width:0%"></div>
            </div>
          </div>
          <div class="vd-progress-item">
            <div class="vd-progress-header">
              <span class="vd-progress-label"><i class="ri-money-dollar-circle-line" style="color:#22c55e"></i> Driver Commission</span>
              <span class="vd-progress-value" id="vd-comm-value">0%</span>
            </div>
            <div class="vd-progress-bar">
              <div class="vd-progress-fill" id="vd-comm-bar" style="width:0%;background:linear-gradient(90deg,#22c55e,rgba(34,197,94,0.7))"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Basic Info Card -->
      <div class="vd-card vd-full">
        <div class="vd-card-header">
          <h5 class="vd-card-title">
            <span class="vd-card-icon"><i class="ri-information-line"></i></span>
            Vehicle Information
          </h5>
        </div>
        <div class="vd-card-body" id="vd-basic-info">
          <div class="vd-info-list">
            <div class="vd-info-item">
              <div class="vd-skeleton" style="width:120px;height:16px;"></div>
              <div class="vd-skeleton" style="width:150px;height:16px;"></div>
            </div>
            <div class="vd-info-item">
              <div class="vd-skeleton" style="width:140px;height:16px;"></div>
              <div class="vd-skeleton" style="width:100px;height:16px;"></div>
            </div>
            <div class="vd-info-item">
              <div class="vd-skeleton" style="width:130px;height:16px;"></div>
              <div class="vd-skeleton" style="width:80px;height:16px;"></div>
            </div>
          </div>
        </div>
      </div>
        <!-- <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5" id="vd-features-desc-row"> -->
      <!-- Features Card -->
      <div class="vd-card">
        <div class="vd-card-header">
          <h5 class="vd-card-title">
            <span class="vd-card-icon"><i class="ri-list-check-2"></i></span>
            Features & Amenities
          </h5>
        </div>
        <div class="vd-card-body" id="vd-features">
          <div class="vd-empty">
            <i class="ri-loader-4-line"></i>
            <p>Loading features...</p>
          </div>
        </div>
      </div>

      <!-- Description Card -->
      <div class="vd-card">
        <div class="vd-card-header">
          <h5 class="vd-card-title">
            <span class="vd-card-icon"><i class="ri-file-text-line"></i></span>
            Description
          </h5>
        </div>
        <div class="vd-card-body" id="vd-description">
          <div class="vd-skeleton" style="width:100%;height:16px;margin-bottom:10px;"></div>
          <div class="vd-skeleton" style="width:95%;height:16px;margin-bottom:10px;"></div>
          <div class="vd-skeleton" style="width:80%;height:16px;"></div>
        </div>
      </div>
    <!-- </div> -->

    </div>

    <!-- Features & Description Row -->
  

    <!-- Image Gallery Row -->
    <div class="vd-card mt-5" id="vd-images-row">
      <div class="vd-card-header">
        <h5 class="vd-card-title">
          <span class="vd-card-icon"><i class="ri-image-2-line"></i></span>
          Vehicle Images
        </h5>
      </div>
      <div class="vd-card-body">
        <div class="vd-gallery-row" id="vd-gallery-container">
          <div class="vd-gallery-item-row"><div class="vd-skeleton"></div></div>
          <div class="vd-gallery-item-row"><div class="vd-skeleton"></div></div>
          <div class="vd-gallery-item-row"><div class="vd-skeleton"></div></div>
          <div class="vd-gallery-item-row"><div class="vd-skeleton"></div></div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Scroll to Top Button -->
<button class="vd-scroll-top" id="vd-scroll-top" aria-label="Scroll to top">
  <i class="ri-arrow-up-line"></i>
</button>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function () {
  const vehicleId = <?php echo json_encode($vehicleId); ?>;

  function esc(val) {
    if (!val) return '';
    const d = document.createElement('div');
    d.textContent = val;
    return d.innerHTML;
  }

  function money(val) {
    const n = parseFloat(val) || 0;
    return '$' + n.toFixed(2);
  }

  // Sticky header effect
  $(window).on('scroll', function() {
    if ($(this).scrollTop() > 100) {
      $('.vd-sticky-header').addClass('scrolled');
      $('#vd-scroll-top').addClass('visible');
    } else {
      $('.vd-sticky-header').removeClass('scrolled');
      $('#vd-scroll-top').removeClass('visible');
    }
  });

  // Scroll to top
  $('#vd-scroll-top').on('click', function() {
    $('html, body').animate({ scrollTop: 0 }, 300);
  });

  $.ajax({
    url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
    type: 'POST',
    data: { action: 'get_vehicle', id: vehicleId },
    success: function (resp) {
      const data = typeof resp === 'string' ? JSON.parse(resp) : resp;
      if (!data.success) {
        Swal.fire('Error', 'Failed to load vehicle details.', 'error').then(function () {
          window.location.href = 'vehicles.php';
        });
        return;
      }
      renderVehicleDetail(data.vehicle);
    },
    error: function () {
      Swal.fire('Error', 'Could not reach the server.', 'error').then(function () {
        window.location.href = 'vehicles.php';
      });
    }
  });

  function renderVehicleDetail(v) {
    const name      = v.name || 'Untitled Vehicle';
    const category  = (v.vehicle_cetagory || 'General').replace(/_/g, ' ');
    const status    = v.status || 'Active';
    const rate      = parseFloat(v.rate_c) || 0;
    const fuel      = parseFloat(v.fuel_percentage_c ?? v.fuel_c ?? 0) || 0;
    const comm      = parseFloat(v.driver_commission_c ?? v.commission_c ?? 0) || 0;
    const passengers = v.passenger || 0;
    const bags      = v.bags || 0;
    const rawImg   = v.image_c || v.images_c || '';
    const imageUrls = String(rawImg).split(',').map(function (s) { return s.trim(); }).filter(Boolean);
    const facilities = v.facilities || '';
    const desc      = v.description || '';
    const dateAdded = v.date_entered
      ? new Date(v.date_entered).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
      : '---';

    const statusClasses = {
      'Active': 'status-active',
      'Inactive': 'status-inactive',
      'Maintenance': 'status-maintenance'
    };
    const statusClass = statusClasses[status] || '';

    // Hero Section
    $('#vd-hero-skeleton').hide();
    $('#vd-hero').show();

    if (imageUrls.length) {
      $('#vd-hero-img').attr('src', imageUrls[0]).show();
    }

    $('#vd-badges').html(
      '<span class="vd-hero-badge ' + statusClass + '"><i class="ri-checkbox-circle-line"></i> ' + esc(status) + '</span>' +
      '<span class="vd-hero-badge"><i class="ri-car-line"></i> ' + esc(category) + '</span>'
    );

    $('#vd-title').html(esc(name));

    $('#vd-meta').html(
      '<span class="vd-hero-meta-item"><i class="ri-calendar-line"></i> Added ' + esc(dateAdded) + '</span>' +
      '<span class="vd-hero-meta-item vd-id-badge"><i class="ri-hashtag"></i> ' + esc(v.id) + '</span>'
    );

    $('#vd-actions').html(
      <?php if($_SESSION['user']['admin'] == 1 || limo_user_module_access('Vehicles', 'update') == 1): ?>
      '<a href="vehicle.php?id=' + encodeURIComponent(v.id) + '" class="vd-back-btn" style="background:var(--primary-color);border-color:var(--primary-color);">' +
        '<i class="ri-edit-line"></i> Edit Vehicle' +
      '</a>' +
      <?php endif; ?>
      '<a href="vehicles.php" class="vd-back-btn">' +
        '<i class="ri-arrow-left-line"></i> Back to Fleet' +
      '</a>'
    );

    // Pricing Card
    $('#vd-price-display').html(money(rate) + '<span class="vd-price-unit">/hr</span>');

    $('#vd-pricing-list').html(
      '<div class="vd-info-item">' +
        '<span class="vd-info-label"><i class="ri-gas-station-line" style="color:#f59e0b"></i> Fuel Surcharge</span>' +
        '<span class="vd-info-value">' + (fuel > 0 ? fuel + '%' : '—') + '</span>' +
      '</div>' +
      '<div class="vd-info-item">' +
        '<span class="vd-info-label"><i class="ri-steering-2-line" style="color:#8b5cf6"></i> Driver Commission</span>' +
        '<span class="vd-info-value">' + (comm > 0 ? comm + '%' : '—') + '</span>' +
      '</div>'
    );

    // Capacity Card
    $('#vd-capacity').html(
      '<div class="vd-stat-grid">' +
        '<div class="vd-stat-card">' +
          '<div class="vd-stat-icon"><i class="ri-user-line" style="color:var(--primary-color)"></i></div>' +
          '<div class="vd-stat-value">' + parseInt(passengers) + '</div>' +
          '<div class="vd-stat-label">Passengers</div>' +
        '</div>' +
        '<div class="vd-stat-card">' +
          '<div class="vd-stat-icon"><i class="ri-briefcase-2-line" style="color:var(--primary-color)"></i></div>' +
          '<div class="vd-stat-value">' + parseInt(bags) + '</div>' +
          '<div class="vd-stat-label">Bags</div>' +
        '</div>' +
      '</div>'
    );

    // Performance Card
    setTimeout(function() {
      $('#vd-fuel-bar').css('width', fuel + '%');
      $('#vd-fuel-value').text(fuel + '%');
      $('#vd-comm-bar').css('width', comm + '%');
      $('#vd-comm-value').text(comm + '%');
    }, 300);

    // Features Card
    if (facilities.trim()) {
      const tags = facilities.split(',').map(function (f) {
        f = f.trim();
        return f ? '<span class="vd-feature-tag"><i class="ri-check-line"></i> ' + esc(f) + '</span>' : '';
      }).join('');
      $('#vd-features').html('<div class="vd-feature-grid">' + tags + '</div>');
    } else {
      $('#vd-features').html('<div class="vd-empty"><i class="ri-checkbox-circle-line"></i><p>No features listed.</p></div>');
    }

    // Images Gallery Row
    if (imageUrls.length) {
      let gallery = '';
      imageUrls.forEach(function (u) {
        gallery += '<div class="vd-gallery-item-row" onclick="openLightbox(\'' + esc(u) + '\')">' +
          '<img src="' + esc(u) + '" alt="Vehicle" onerror="this.parentElement.style.display=\'none\'">' +
          '<div class="vd-gallery-overlay"><i class="ri-zoom-in-line"></i></div>' +
        '</div>';
      });
      $('#vd-gallery-container').html(gallery);
    } else {
      $('#vd-gallery-container').html('<div class="vd-empty"><i class="ri-image-add-line"></i><p>No images available.</p></div>');
    }

    // Basic Info Card
    $('#vd-basic-info').html(
      '<div class="vd-info-list">' +
        '<div class="vd-info-item">' +
          '<span class="vd-info-label"><i class="ri-car-line"></i> Name</span>' +
          '<span class="vd-info-value">' + esc(name) + '</span>' +
        '</div>' +
        '<div class="vd-info-item">' +
          '<span class="vd-info-label"><i class="ri-price-tag-3-line"></i> Category</span>' +
          '<span class="vd-info-value">' + esc(category) + '</span>' +
        '</div>' +
        '<div class="vd-info-item">' +
          '<span class="vd-info-label"><i class="ri-flag-line"></i> Status</span>' +
          '<span class="vd-info-value"><span class="vd-feature-tag" style="padding:4px 12px;font-size:11px;">' + esc(status) + '</span></span>' +
        '</div>' +
        '<div class="vd-info-item">' +
          '<span class="vd-info-label"><i class="ri-calendar-line"></i> Date Added</span>' +
          '<span class="vd-info-value">' + esc(dateAdded) + '</span>' +
        '</div>' +
        '<div class="vd-info-item">' +
          '<span class="vd-info-label"><i class="ri-honour-line"></i> ID</span>' +
          '<span class="vd-info-value vd-id-badge">' + esc(v.id) + '</span>' +
        '</div>' +
      '</div>'
    );

    // Description Card
    if (desc.trim()) {
      $('#vd-description').html('<p class="vd-description">' + esc(desc) + '</p>');
    } else {
      $('#vd-description').html('<div class="vd-empty"><i class="ri-file-text-line"></i><p>No description provided.</p></div>');
    }
  }

  // Simple lightbox
  window.openLightbox = function(src) {
    const overlay = $('<div>').css({
      position: 'fixed',
      inset: 0,
      background: 'rgba(0,0,0,0.9)',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      zIndex: 9999,
      cursor: 'pointer'
    }).on('click', function() { $(this).remove(); });

    const img = $('<img>').css({
      maxWidth: '90vw',
      maxHeight: '90vh',
      borderRadius: '12px',
      objectFit: 'contain'
    }).attr('src', src);

    overlay.append(img);
    $('body').append(overlay);
  };
});
</script>
