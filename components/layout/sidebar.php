<!-- components/layout/sidebar.php -->

<?php
// Important: session_start() yahan mat lagana – header.php ya main file mein pehle se lag chuka hoga
// Agar nahi laga to main entry file (index.php) ke sabse top pe lagao

// PDO connection – agar alag file mein hai to include kar lo
// Example (apna actual connection code daal dena)
try {
    $pdo = new PDO("mysql:host=localhost;dbname=zabrinxyz_suitem7;charset=utf8mb4", "db_username", "db_password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Production mein log karo, yahan sirf placeholder
    $permissions = [];
}

// Current user ka role fetch karo
$user_id = $_SESSION['user']['id'] ?? null;  // tumhare code mein $_SESSION['user']['id'] lag raha hai

$permissions = [];

if ($user_id && isset($pdo)) {
    // 1. User ka role_id nikaalo
    $stmt = $pdo->prepare("SELECT role_id FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $role_id = $user['role_id'] ?? null;

    if ($role_id) {
        // 2. Role ke permissions fetch karo (tumhare backend ke hisaab se table 'role_permissions' lag raha hai)
        $stmt = $pdo->prepare("
            SELECT 
                module, 
                can_create, 
                can_read, 
                can_update, 
                can_delete
            FROM role_permissions
            WHERE role_id = ?
        ");
        $stmt->execute([$role_id]);
        $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Helper function: koi bhi permission 1 hai to module show karo
function hasAccess($moduleName, $perms) {
    $moduleName = trim(strtolower($moduleName));
    foreach ($perms as $p) {
        if (trim(strtolower($p['module'])) === $moduleName) {
            return !empty($p['can_create']) || !empty($p['can_read']) ||
                   !empty($p['can_update']) || !empty($p['can_delete']);
        }
    }
    return false;
}

// URL logic (tumhara original)
$url_string = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($url_string, '/'));
$url = $parts[1] ?? 'index.php';  // adjust if needed (tumhare case mein $url[2] tha)
?>

<aside class="app-sidebar" id="sidebar">
    <!-- Header part same -->
    <div class="main-sidebar-header">
        <a href="index.php" class="header-logo">
            <!-- logo images same -->
        </a>
    </div>

    <div class="main-sidebar" id="sidebar-scroll" data-simplebar="init">
        <!-- simplebar wrapper same -->

        <nav aria-label="nav2" class="main-menu-container nav nav-pills flex-col sub-open">
            <!-- slide-left same -->

            <ul class="main-menu" style="display: block; margin-left: 0px; margin-right: 0px;">
                <li class="slide__category"><span class="category-name">MAIN</span></li>

                <!-- Dashboard – hamesha dikhao (permission independent) -->
                <li class="slide <?php echo $url == 'index.php' ? 'active' : ''; ?>">
                    <a href="./index.php" class="side-menu__item <?php echo $url == 'index.php' ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path>
                        </svg>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>

                <?php if (hasAccess('Leads', $permissions)) { ?>
                <li data-module="Leads" class="slide <?php echo $url == 'leads.php' ? 'active' : ''; ?>">
                    <a href="./leads.php" class="side-menu__item <?php echo $url == 'leads.php' ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 side-menu__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"></path>
                        </svg>
                        <span class="side-menu__label">Leads</span>
                    </a>
                </li>
                <?php } ?>

                <?php if (hasAccess('Team', $permissions)) { ?>
                <li data-module="Team" class="slide <?php echo $url == 'team.php' ? 'active' : ''; ?>">
                    <a href="./team.php" class="side-menu__item <?php echo $url == 'team.php' ? 'active' : ''; ?>">
                        <!-- Team icon same -->
                        <span class="side-menu__label">Team</span>
                    </a>
                </li>
                <?php } ?>

                <?php if (hasAccess('vehicles', $permissions) || hasAccess('Vehicles', $permissions)) { ?>
                <li data-module="vehicles" class="slide <?php echo $url == 'vehicles.php' ? 'active' : ''; ?>">
                    <a href="./vehicles.php" class="side-menu__item <?php echo $url == 'vehicles.php' ? 'active' : ''; ?>">
                        <!-- Vehicles icon -->
                        <span class="side-menu__label">Vehicles</span>
                    </a>
                </li>
                <?php } ?>

                <?php if (hasAccess('Role Management', $permissions) || hasAccess('Roles', $permissions)) { ?>
                <li data-module="Roles" class="slide <?php echo $url == 'role_management.php' ? 'active' : ''; ?>">
                    <a href="./role_management.php" class="side-menu__item <?php echo $url == 'role_management.php' ? 'active' : ''; ?>">
                        <!-- Role icon -->
                        <span class="side-menu__label">Role Management</span>
                    </a>
                </li>
                <?php } ?>

                <?php if (hasAccess('Email Templates', $permissions) || hasAccess('EmailTemplates', $permissions)) { ?>
                <li data-module="EmailTemplates" class="slide <?php echo $url == 'email_templates.php' ? 'active' : ''; ?>">
                    <a href="./email_templates.php" class="side-menu__item <?php echo $url == 'email_templates.php' ? 'active' : ''; ?>">
                        <!-- Email Templates icon -->
                        <span class="side-menu__label">Email Templates</span>
                    </a>
                </li>
                <?php } ?>

                <?php if (hasAccess('Task', $permissions) || hasAccess('Tasks', $permissions)) { ?>
                <li data-module="Tasks" class="slide <?php echo $url == 'task.php' ? 'active' : ''; ?>">
                    <a href="./task.php" class="side-menu__item <?php echo $url == 'task.php' ? 'active' : ''; ?>">
                        <!-- Tasks icon -->
                        <span class="side-menu__label">Tasks</span>
                    </a>
                </li>
                <?php } ?>

                <?php if (hasAccess('Workflows', $permissions)) { ?>
                <li data-module="Workflows" class="slide <?php echo $url == 'workflows.php' ? 'active' : ''; ?>">
                    <a href="./workflows.php" class="side-menu__item <?php echo $url == 'workflows.php' ? 'active' : ''; ?>">
                        <!-- Workflows icon -->
                        <span class="side-menu__label">Workflows</span>
                    </a>
                </li>
                <?php } ?>

                <?php if (hasAccess('Check', $permissions)) { ?>
                <li data-module="Check" class="slide <?php echo $url == 'check.php' ? 'active' : ''; ?>">
                    <a href="./check.php" class="side-menu__item <?php echo $url == 'check.php' ? 'active' : ''; ?>">
                        <!-- Check/Integration icon -->
                        <span class="side-menu__label">Integration</span>  <!-- ya jo label hai -->
                    </a>
                </li>
                <?php } ?>

                <?php if (hasAccess('Notes', $permissions)) { ?>
                <li class="slide <?php echo $url == 'notes.php' ? 'active' : ''; ?>">
                    <a href="./notes.php" class="side-menu__item">
                        <!-- Notes icon -->
                        <span class="side-menu__label">Notes</span>
                    </a>
                </li>
                <?php } ?>
            </ul>

            <!-- slide-right same -->
        </nav>
    </div>
</aside>