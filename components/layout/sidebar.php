  <!-- Start::app-sidebar -->
   <?php
   $url_string = $_SERVER['REQUEST_URI'];
   $url = explode('/', $url_string);
   $url = $url[2];
   ?>
      <aside class="app-sidebar" id="sidebar">
        <!-- Start::main-sidebar-header -->
        <div class="main-sidebar-header">
          <a href="index.html" class="header-logo">
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
                        <li class="slide <?php echo $url == 'index.php' ? 'active' : ''; ?>">
                          <a href="./index.php" class="side-menu__item <?php echo $url == 'index.php' ? 'active' : ''; ?>">
                           <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path></svg>
                            <span class="side-menu__label">Dashboard</span>
                          </a>
                        </li>
                        <li class="slide <?php echo $url == 'leads.php' ? 'active' : ''; ?>">
                          <a href="./leads.php" class="side-menu__item <?php echo $url == 'leads.php' ? 'active' : ''; ?>">
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"></path> </svg>
                            <span class="side-menu__label">Leads</span>
                          </a>
                        </li>
                        <li class="slide <?php echo $url == 'team.php' ? 'active' : ''; ?>">
                          <a href="./team.php" class="side-menu__item <?php echo $url == 'team.php' ? 'active' : ''; ?>">
                          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 11C14.7614 11 17 13.2386 17 16V22H15V16C15 14.4023 13.7511 13.0963 12.1763 13.0051L12 13C10.4023 13 9.09634 14.2489 9.00509 15.8237L9 16V22H7V16C7 13.2386 9.23858 11 12 11ZM5.5 14C5.77885 14 6.05009 14.0326 6.3101 14.0942C6.14202 14.594 6.03873 15.122 6.00896 15.6693L6 16L6.0007 16.0856C5.88757 16.0456 5.76821 16.0187 5.64446 16.0069L5.5 16C4.7203 16 4.07955 16.5949 4.00687 17.3555L4 17.5V22H2V17.5C2 15.567 3.567 14 5.5 14ZM18.5 14C20.433 14 22 15.567 22 17.5V22H20V17.5C20 16.7203 19.4051 16.0796 18.6445 16.0069L18.5 16C18.3248 16 18.1566 16.03 18.0003 16.0852L18 16C18 15.3343 17.8916 14.694 17.6915 14.0956C17.9499 14.0326 18.2211 14 18.5 14ZM5.5 8C6.88071 8 8 9.11929 8 10.5C8 11.8807 6.88071 13 5.5 13C4.11929 13 3 11.8807 3 10.5C3 9.11929 4.11929 8 5.5 8ZM18.5 8C19.8807 8 21 9.11929 21 10.5C21 11.8807 19.8807 13 18.5 13C17.1193 13 16 11.8807 16 10.5C16 9.11929 17.1193 8 18.5 8ZM5.5 10C5.22386 10 5 10.2239 5 10.5C5 10.7761 5.22386 11 5.5 11C5.77614 11 6 10.7761 6 10.5C6 10.2239 5.77614 10 5.5 10ZM18.5 10C18.2239 10 18 10.2239 18 10.5C18 10.7761 18.2239 11 18.5 11C18.7761 11 19 10.7761 19 10.5C19 10.2239 18.7761 10 18.5 10ZM12 2C14.2091 2 16 3.79086 16 6C16 8.20914 14.2091 10 12 10C9.79086 10 8 8.20914 8 6C8 3.79086 9.79086 2 12 2ZM12 4C10.8954 4 10 4.89543 10 6C10 7.10457 10.8954 8 12 8C13.1046 8 14 7.10457 14 6C14 4.89543 13.1046 4 12 4Z"></path></svg>
                            <span class="side-menu__label">Team</span>
                          </a>
                        </li>
                        <li class="slide <?php echo $url == 'role_management.php' ? 'active' : ''; ?>">
                          <a href="./role_management.php" class="side-menu__item <?php echo $url == 'role_management.php' ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"></path> </svg>
                            <span class="side-menu__label">Role Management</span>
                          </a>
                        </li>
                        <li class="slide <?php echo $url == 'email_templates.php' ? 'active' : ''; ?>">
                          <a href="./email_templates.php" class="side-menu__item <?php echo $url == 'email_templates.php' ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" viewBox="0 0 24 24" fill="currentColor"><path d="M20.7134 8.12811L20.4668 8.69379C20.2864 9.10792 19.7136 9.10792 19.5331 8.69379L19.2866 8.12811C18.8471 7.11947 18.0555 6.31641 17.0677 5.87708L16.308 5.53922C15.8973 5.35653 15.8973 4.75881 16.308 4.57612L17.0252 4.25714C18.0384 3.80651 18.8442 2.97373 19.2761 1.93083L19.5293 1.31953C19.7058 0.893489 20.2942 0.893489 20.4706 1.31953L20.7238 1.93083C21.1558 2.97373 21.9616 3.80651 22.9748 4.25714L23.6919 4.57612C24.1027 4.75881 24.1027 5.35653 23.6919 5.53922L22.9323 5.87708C21.9445 6.31641 21.1529 7.11947 20.7134 8.12811ZM2 4C2 3.44772 2.44772 3 3 3H14V5H4.5052L12 11.662L16.3981 7.75259L17.7269 9.24741L12 14.338L4 7.22684V19H20V11H22V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4Z"></path></svg>
                            <span class="side-menu__label">Email Templates</span>
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