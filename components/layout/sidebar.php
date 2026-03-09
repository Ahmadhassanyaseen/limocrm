<!-- Start::app-sidebar -->
<?php
$url_string = $_SERVER['REQUEST_URI'];
$url = explode('/', $url_string);
$url = end($url);
?>
<aside class="app-sidebar" id="sidebar">
    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header">
        <a href="index.php" class="header-logo">
            <img src="assets/images/brand-logos/logo.png" alt="logo" class="desktop-logo" />
        </a>
    </div>
    
    <!-- Start::main-sidebar -->
    <div class="main-sidebar" id="sidebar-scroll">
        <div class="simplebar-content">
            <nav class="main-menu-container nav nav-pills flex-col sub-open">
                <ul class="main-menu">
                    <li class="slide__category"><span class="category-name">MAIN</span></li>
                    
                    <!-- Dashboard -->
                    <li class="slide <?php echo $url == 'index.php' ? 'active' : ''; ?>">
                        <a href="index.php" class="side-menu__item">
                            <i class="ri-dashboard-line side-menu__icon"></i>
                            <span class="side-menu__label">Dashboard</span>
                        </a>
                    </li>
                    
                    <!-- Leads -->
                    <li class="slide <?php echo $url == 'leads.php' ? 'active' : ''; ?>">
                        <a href="leads.php" class="side-menu__item">
                            <i class="ri-user-line side-menu__icon"></i>
                            <span class="side-menu__label">Leads</span>
                        </a>
                    </li>
                    
                    <!-- Team -->
                    <li class="slide <?php echo $url == 'team.php' ? 'active' : ''; ?>">
                        <a href="team.php" class="side-menu__item">
                            <i class="ri-team-line side-menu__icon"></i>
                            <span class="side-menu__label">Team</span>
                        </a>
                    </li>
                    
                    <!-- Vehicles -->
                    <li class="slide <?php echo $url == 'vehicles.php' ? 'active' : ''; ?>">
                        <a href="vehicles.php" class="side-menu__item">
                            <i class="ri-car-line side-menu__icon"></i>
                            <span class="side-menu__label">Vehicles</span>
                        </a>
                    </li>
                    
                    <!-- Role Management -->
                    <li class="slide <?php echo $url == 'role_management.php' ? 'active' : ''; ?>">
                        <a href="role_management.php" class="side-menu__item">
                            <i class="ri-shield-user-line side-menu__icon"></i>
                            <span class="side-menu__label">Role Management</span>
                        </a>
                    </li>
                    
                    <!-- Email Templates -->
                    <li class="slide <?php echo $url == 'email_templates.php' ? 'active' : ''; ?>">
                        <a href="email_templates.php" class="side-menu__item">
                            <i class="ri-mail-line side-menu__icon"></i>
                            <span class="side-menu__label">Email Templates</span>
                        </a>
                    </li>
                    
                    <!-- Tasks -->
                    <li class="slide <?php echo $url == 'task.php' ? 'active' : ''; ?>">
                        <a href="task.php" class="side-menu__item">
                            <i class="ri-task-line side-menu__icon"></i>
                            <span class="side-menu__label">Tasks</span>
                        </a>
                    </li>
                    
                    <!-- Workflows -->
                    <li class="slide <?php echo $url == 'workflows.php' ? 'active' : ''; ?>">
                        <a href="workflows.php" class="side-menu__item">
                            <i class="ri-flow-chart side-menu__icon"></i>
                            <span class="side-menu__label">Workflows</span>
                        </a>
                    </li>
                    
                    <!-- Integration -->
                    <li class="slide <?php echo $url == 'integration.php' ? 'active' : ''; ?>">
                        <a href="integration.php" class="side-menu__item">
                            <i class="ri-plug-line side-menu__icon"></i>
                            <span class="side-menu__label">Integration</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</aside>
<!-- End::app-sidebar -->