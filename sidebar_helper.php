<?php
// Add this at the top of sidebar.php after session_start()

// Helper function to check if module is visible for current user
function isModuleVisible($moduleName) {
    if (!isset($_SESSION['user']['role_permissions'])) {
        return true; // Show all modules if no permissions set (admin)
    }
    
    $permissions = $_SESSION['user']['role_permissions'];
    foreach ($permissions as $perm) {
        if ($perm['module'] == $moduleName) {
            return isset($perm['is_visible']) && $perm['is_visible'] == 1;
        }
    }
    return false; // Hide by default if module not found in permissions
}
?>
