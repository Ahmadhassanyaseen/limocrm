  <!-- Start::app-sidebar -->
   <?php
   $url_string = $_SERVER['REQUEST_URI'];
   $url = explode('/', $url_string);
   $url = $url[2];
   
   ?>
      <aside class="app-sidebar" id="sidebar">
        <!-- Start::main-sidebar-header -->
        <div class="main-sidebar-header">
          <a href="index.php" class="header-logo">
            <img
              src="assets/images/brand-logos/logo.png"
              alt="logo"
              class="desktop-logo"
            />
            <img
              src="assets/images/brand-logos/toggle-dark.png"
              alt="logo"
              class="toggle-dark"
            />
            <img
              src="assets/images/brand-logos/logo.png"
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
              src="assets/images/brand-logos/logo.png"
              alt="logo"
              class="desktop-white"
            />
          </a>
        </div>
        <!-- End::main-sidebar-header -->
        <!-- Start::main-sidebar -->
        <div class="main-sidebar" id="sidebar-scroll" data-simplebar="init">
          <div class="simplebar-wrapper" style="margin: -8px 0px -80px">
            <div class="simplebar-height-auto-observer-wrapper">
              <div class="simplebar-height-auto-observer"></div>
            </div>
            <div class="simplebar-mask">
              <div class="simplebar-offset" style="right: 0px; bottom: 0px">
                <div
                  class="simplebar-content-wrapper"
                  tabindex="0"
                  role="region"
                  aria-label="scrollable content"
                  style="height: 100%; overflow: hidden scroll"
                >
                  <div class="simplebar-content" style="padding: 8px 0px 80px">
                    <!-- Start::nav -->
                    <nav
                      aria-label="nav2"
                      class="main-menu-container nav nav-pills flex-col sub-open"
                    >
                      <div class="slide-left hidden" id="slide-left">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          fill="#7b8191"
                          width="24"
                          height="24"
                          viewBox="0 0 24 24"
                        >
                          <path
                            d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"
                          ></path>
                        </svg>
                      </div>
                      <ul
                        class="main-menu"
                        style="
                          display: block;
                          margin-left: 0px;
                          margin-right: 0px;
                        "
                      >
                      <li class="slide__category"><span class="category-name">Main</span></li>
                      <!-- Start::slide -->

                        <!-- Dashboard -->
                        <li class="slide <?php echo $url == 'index.php' ? 'active' : ''; ?>">
                          <a href="./index.php" class="side-menu__item <?php echo $url == 'index.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path></svg>
                            <span class="side-menu__label">Dashboard</span>
                          </a>
                        </li>

                        <!-- Leads -->
                        <li data-module="Leads" class="slide <?php echo $url == 'leads.php' ? 'active' : ''; ?>">
                          <a href="./leads.php" class="side-menu__item <?php echo $url == 'leads.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"></path></svg>
                            <span class="side-menu__label">Leads</span>
                          </a>
                        </li>

                        <!-- Vendors -->
                        <!-- <li data-module="Vendors" class="slide <?php echo $url == 'vendors.php' ? 'active' : ''; ?>">
                          <a href="./vendors.php" class="side-menu__item <?php echo $url == 'vendors.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"></path></svg>
                            <span class="side-menu__label">Vendors</span>
                          </a>
                        </li> -->

                        <!-- Vehicles -->
                        <li data-module="Vehicles" class="slide <?php echo $url == 'vehicles.php' ? 'active' : ''; ?>">
                          <a href="./vehicles.php" class="side-menu__item <?php echo $url == 'vehicles.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" viewBox="0 0 24 24" fill="currentColor"><path d="M19 20H5V21C5 21.5523 4.55228 22 4 22H3C2.44772 22 2 21.5523 2 21V11L4.4805 5.21216C4.79566 4.47679 5.51874 4 6.31879 4H17.6812C18.4813 4 19.2043 4.47679 19.5195 5.21216L22 11V21C22 21.5523 21.5523 22 21 22H20C19.4477 22 19 21.5523 19 21V20ZM20 13H4V18H20V13ZM4.17594 11H19.8241L17.6812 6H6.31879L4.17594 11ZM6.5 17C5.67157 17 5 16.3284 5 15.5C5 14.6716 5.67157 14 6.5 14C7.32843 14 8 14.6716 8 15.5C8 16.3284 7.32843 17 6.5 17ZM17.5 17C16.6716 17 16 16.3284 16 15.5C16 14.6716 16.6716 14 17.5 14C18.3284 14 19 14.6716 19 15.5C19 16.3284 18.3284 17 17.5 17Z"></path></svg>
                            <span class="side-menu__label">Vehicles</span>
                          </a>
                        </li>

                        <!-- Quotes -->
                        <!-- <li data-module="Quotes" class="slide <?php echo $url == 'quotes.php' ? 'active' : ''; ?>">
                          <a href="./quotes.php" class="side-menu__item <?php echo $url == 'quotes.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path></svg>
                            <span class="side-menu__label">Quotes</span>
                          </a>
                        </li> -->

                        <!-- Agreements -->
                        <li data-module="Agreements" class="slide <?php echo $url == 'agreements.php' ? 'active' : ''; ?>">
                          <a href="./agreements.php" class="side-menu__item <?php echo $url == 'agreements.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"></path></svg>
                            <span class="side-menu__label">Agreements</span>
                          </a>
                        </li>

                        <!-- Notes -->
                        <li data-module="Notes" class="slide <?php echo $url == 'notes.php' ? 'active' : ''; ?>">
                          <a href="./notes.php" class="side-menu__item <?php echo $url == 'notes.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path></svg>
                            <span class="side-menu__label">Notes</span>
                          </a>
                        </li>

                        <!-- Contacts -->
                        <?php $contactsActive = in_array($url, ['contacts.php','contact_detail.php','edit_contact.php']); ?>
                        <li data-module="Contacts" class="slide <?php echo $contactsActive ? 'active' : ''; ?>">
                          <a href="./contacts.php" class="side-menu__item <?php echo $contactsActive ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z"></path></svg>
                            <span class="side-menu__label">Contacts</span>
                          </a>
                        </li>

                        <!-- Calendar -->
                        <li data-module="Calendar" class="slide <?php echo $url == 'calendar.php' ? 'active' : ''; ?>">
                          <a href="./calendar.php" class="side-menu__item <?php echo $url == 'calendar.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"></path></svg>
                            <span class="side-menu__label">Calendar</span>
                          </a>
                        </li>

                        <!-- Reports & Analytics -->
                        <li data-module="Reports" class="slide <?php echo $url == 'reports.php' ? 'active' : ''; ?>">
                          <a href="./reports.php" class="side-menu__item <?php echo $url == 'reports.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605"></path></svg>
                            <span class="side-menu__label">Reports & Analytics</span>
                          </a>
                        </li>

                        <!-- Email Tracking -->
                        <!-- <li data-module="EmailTracking" class="slide <?php echo $url == 'email_tracking.php' ? 'active' : ''; ?>">
                          <a href="./email_tracking.php" class="side-menu__item <?php echo $url == 'email_tracking.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"></path></svg>
                            <span class="side-menu__label">Email Tracking</span>
                          </a>
                        </li> -->

                        <!-- Pricing -->
                        <li data-module="Vehicles" class="slide <?php echo $url == 'pricing.php' ? 'active' : ''; ?>">
                          <a href="./pricing.php" class="side-menu__item <?php echo $url == 'pricing.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path></svg>
                            <span class="side-menu__label">Pricing</span>
                          </a>
                        </li>

                        <!-- Commissions -->
                        <!-- <li data-module="Commissions" class="slide <?php echo $url == 'commissions.php' ? 'active' : ''; ?>">
                          <a href="./commissions.php" class="side-menu__item <?php echo $url == 'commissions.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path></svg>
                            <span class="side-menu__label">Commissions</span>
                          </a>
                        </li> -->

                        <!-- Campaigns -->
                        <!-- <li data-module="Campaigns" class="slide <?php echo $url == 'campaigns.php' ? 'active' : ''; ?>">
                          <a href="./campaigns.php" class="side-menu__item <?php echo $url == 'campaigns.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.75.75 0 0 1-1.021-.268l-.304-.527a8.17 8.17 0 0 1-1.082-4.099m2.542-6.78a12.158 12.158 0 0 0 0 6.78m0-6.78c2.598-.24 5.13-.84 7.5-1.762V16.6c-2.37-.921-4.902-1.521-7.5-1.762ZM21 12.75V12a.75.75 0 0 0-.75-.75H18a.75.75 0 0 0-.75.75v.75"></path></svg>
                            <span class="side-menu__label">Campaigns</span>
                          </a>
                        </li> -->

                        <!-- Chat -->
                        <!-- <li data-module="Chat" class="slide <?php echo $url == 'chat.php' ? 'active' : ''; ?>">
                          <a href="./chat.php" class="side-menu__item <?php echo $url == 'chat.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"></path></svg>
                            <span class="side-menu__label">Chat</span>
                            <span class="badge bg-danger-transparent ms-auto rounded-full">3</span>
                          </a>
                        </li> -->

                      <!-- ADMIN Section -->
                      <li class="slide__category"><span class="category-name" style="color: #e74c3c;">ADMIN</span></li>

                        <!-- User Management (Expandable) -->
                        <?php $userMgmtActive = in_array($url, ['users.php','role_management.php','employee_analytics.php']); ?>
                        <li class="slide has-sub open">
                          <a href="javascript:void(0);" class="side-menu__item <?php echo $userMgmtActive ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path></svg>
                            <span class="side-menu__label">User Management</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__angle" style="width:16px;height:16px;transition:transform .3s;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                          </a>
                          <ul class="slide-menu child1" style="display:block;">
                            <li class="slide <?php echo $url == 'users.php' ? 'active' : ''; ?>">
                              <a href="./users.php" class="side-menu__item <?php echo $url == 'users.php' ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"></path></svg>
                                Users
                              </a>
                            </li>
                            <li class="slide <?php echo $url == 'role_management.php' ? 'active' : ''; ?>">
                              <a href="./role_management.php" class="side-menu__item <?php echo $url == 'role_management.php' ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"></path></svg>
                                Role Management
                              </a>
                            </li>
                            <li class="slide <?php echo $url == 'employee_analytics.php' ? 'active' : ''; ?>">
                              <a href="./employee_analytics.php" class="side-menu__item <?php echo $url == 'employee_analytics.php' ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z"></path></svg>
                                Employee Analytics
                              </a>
                            </li>
                          </ul>
                        </li>

                        <!-- Pricing -->
                        <!-- <li data-module="Pricing" class="slide <?php echo $url == 'pricing.php' ? 'active' : ''; ?>">
                          <a href="./pricing.php" class="side-menu__item <?php echo $url == 'pricing.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"></path></svg>
                            <span class="side-menu__label">Pricing</span>
                          </a>
                        </li> -->

                        <!-- Coupons -->
                        <!-- <li data-module="Coupons" class="slide <?php echo $url == 'coupons.php' ? 'active' : ''; ?>">
                          <a href="./coupons.php" class="side-menu__item <?php echo $url == 'coupons.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"></path></svg>
                            <span class="side-menu__label">Coupons</span>
                          </a>
                        </li> -->

                        <!-- Payments -->
                        <li data-module="Payments" class="slide <?php echo $url == 'payments.php' ? 'active' : ''; ?>">
                          <a href="./payments.php" class="side-menu__item <?php echo $url == 'payments.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25h-15a2.25 2.25 0 0 0-2.25 2.25v10.5a2.25 2.25 0 0 0 2.25 2.25Z"></path></svg>
                            <span class="side-menu__label">Payments</span>
                          </a>
                        </li>

                        <!-- Email Settings (Expandable) -->
                        <?php $emailMgmtActive = in_array($url, ['email_settings.php','email_templates.php','email_template.php','email_analytics.php','campaign_builder.php','workflows.php']); ?>
                        <li class="slide has-sub open">
                          <a href="javascript:void(0);" class="side-menu__item <?php echo $emailMgmtActive ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"></path></svg>
                            <span class="side-menu__label">Email Management</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__angle" style="width:16px;height:16px;transition:transform .3s;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                          </a>
                          <ul class="slide-menu child1" style="display:block;">
                            <li class="slide <?php echo $url == 'email_templates.php' || $url == 'email_template.php' ? 'active' : ''; ?>">
                              <a href="./email_templates.php" class="side-menu__item <?php echo $url == 'email_templates.php' || $url == 'email_template.php' ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path></svg>
                                Email Templates
                              </a>
                            </li>
                            <li class="slide <?php echo $url == 'email_analytics.php' ? 'active' : ''; ?>">
                              <a href="./email_analytics.php" class="side-menu__item <?php echo $url == 'email_analytics.php' ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z"></path></svg>
                                Email Analytics
                              </a>
                            </li>
                            <li class="slide <?php echo $url == 'campaign_builder.php' ? 'active' : ''; ?>">
                              <a href="./campaign_builder.php" class="side-menu__item <?php echo $url == 'campaign_builder.php' ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"></path></svg>
                                Campaign Builder
                              </a>
                            </li>
                            <li class="slide <?php echo $url == 'workflows.php' ? 'active' : ''; ?>">
                              <a href="./workflows.php" class="side-menu__item <?php echo $url == 'workflows.php' ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z"></path></svg>
                                Workflows
                              </a>
                            </li>
                          </ul>
                        </li>

                        <!-- Integrations -->
                        <li data-module="Integrations" class="slide <?php echo $url == 'integration.php' ? 'active' : ''; ?>">
                          <a href="./integration.php" class="side-menu__item <?php echo $url == 'integration.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"></path></svg>
                            <span class="side-menu__label">Integrations</span>
                          </a>
                        </li>

                        <!-- Vendor Tiers -->
                        <!-- <li data-module="VendorTiers" class="slide <?php echo $url == 'vendor_tiers.php' ? 'active' : ''; ?>">
                          <a href="./vendor_tiers.php" class="side-menu__item <?php echo $url == 'vendor_tiers.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z"></path></svg>
                            <span class="side-menu__label">Vendor Tiers</span>
                          </a>
                        </li> -->

                        <!-- Audit Log -->
                        <li data-module="AuditLog" class="slide <?php echo $url == 'audit_log.php' ? 'active' : ''; ?>">
                          <a href="./audit_log.php" class="side-menu__item <?php echo $url == 'audit_log.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"></path></svg>
                            <span class="side-menu__label">Audit Log</span>
                          </a>
                        </li>

                        <!-- System -->
                        <li data-module="System" class="slide <?php echo $url == 'system.php' ? 'active' : ''; ?>">
                          <a href="./system.php" class="side-menu__item <?php echo $url == 'system.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25A2.25 2.25 0 0 1 5.25 3h13.5A2.25 2.25 0 0 1 21 5.25Z"></path></svg>
                            <span class="side-menu__label">System</span>
                          </a>
                        </li>

                        <!-- Settings -->
                        <li data-module="Settings" class="slide <?php echo $url == 'settings.php' ? 'active' : ''; ?>">
                          <a href="./settings.php" class="side-menu__item <?php echo $url == 'settings.php' ? 'active' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path></svg>
                            <span class="side-menu__label">Settings</span>
                          </a>
                        </li>

                        <!-- End::slide -->    
                    </ul>
                      <div class="slide-right hidden" id="slide-right">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          fill="#7b8191"
                          width="24"
                          height="24"
                          viewBox="0 0 24 24"
                        >
                          <path
                            d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"
                          ></path>
                        </svg>
                      </div>
                    </nav>
                    <!-- End::nav -->
                  </div>
                </div>
              </div>
            </div>
            <div
              class="simplebar-placeholder"
              style="width: auto; height: 1714px"
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
            style="visibility: visible"
          >
            <div
              class="simplebar-scrollbar"
              style="
                height: 67px;
                transform: translate3d(0px, 0px, 0px);
                display: block;
              "
            ></div>
          </div>
        </div>
        <!-- End::main-sidebar -->
      </aside>
      <!-- End::app-sidebar -->
      