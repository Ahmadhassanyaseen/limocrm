<?php
include_once "components/layout/header.php";
include_once "components/layout/sidebar.php";
include_once "config/api.php";


// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Initialize session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include SuiteCRM modules
require_once 'include/entryPoint.php';
require_once 'modules/Users/User.php';
require_once 'modules/Users/utils.php';

// Get current user details
$current_user = new User();
$current_user->retrieve($_SESSION['user_id']);

// Handle form submissions
$message = '';
$messageType = '';

// Handle profile update
if (isset($_POST['update_profile'])) {
    $user = new User();
    $user->retrieve($_SESSION['user_id']);
    
    // Update user fields
    $user->first_name = trim($_POST['first_name'] ?? $user->first_name);
    $user->last_name = trim($_POST['last_name'] ?? $user->last_name);
    $user->email1 = trim($_POST['email'] ?? $user->email1);
    $user->phone_mobile = trim($_POST['phone'] ?? $user->phone_mobile);
    $user->title = trim($_POST['designation'] ?? $user->title);
    $user->address_street = trim($_POST['address'] ?? $user->address_street);
    $user->address_city = trim($_POST['city'] ?? $user->address_city);
    $user->address_state = trim($_POST['state'] ?? $user->address_state);
    $user->address_country = trim($_POST['country'] ?? $user->address_country);
    $user->address_postalcode = trim($_POST['postal_code'] ?? $user->address_postalcode);
    $user->website = trim($_POST['website'] ?? $user->website);
    $user->language = trim($_POST['language'] ?? $user->language);
    
    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $upload_dir = 'upload/profile_pics/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = $_SESSION['user_id'] . '_' . time() . '_' . basename($_FILES['profile_picture']['name']);
        $target_file = $upload_dir . $file_name;
        
        // Check file type
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                // Delete old picture if exists
                if (!empty($user->picture) && file_exists($user->picture)) {
                    unlink($user->picture);
                }
                $user->picture = $target_file;
            }
        }
    }
    
    // Save the user
    if ($user->save()) {
        $_SESSION['user_name'] = $user->first_name . ' ' . $user->last_name;
        $message = "Profile updated successfully!";
        $messageType = "success";
        
        // Log activity
        $GLOBALS['log']->info("Profile updated for user: " . $user->user_name);
    } else {
        $message = "Error updating profile. Please try again.";
        $messageType = "danger";
    }
}

// Handle notification settings
if (isset($_POST['update_notifications'])) {
    $preferences = $current_user->getPreference('notification_preferences', 'global');
    if (!is_array($preferences)) {
        $preferences = array();
    }
    
    $preferences['email_notifications'] = isset($_POST['email_notifications']) ? 1 : 0;
    $preferences['sms_notifications'] = isset($_POST['sms_notifications']) ? 1 : 0;
    $preferences['push_notifications'] = isset($_POST['push_notifications']) ? 1 : 0;
    $preferences['in_app_notifications'] = isset($_POST['in_app_notifications']) ? 1 : 0;
    
    $current_user->setPreference('notification_preferences', $preferences, 'global');
    $current_user->savePreferencesToDB();
    
    $message = "Notification settings updated successfully!";
    $messageType = "success";
}

// Handle security settings
if (isset($_POST['update_security'])) {
    $security_settings = $current_user->getPreference('security_settings', 'global');
    if (!is_array($security_settings)) {
        $security_settings = array();
    }
    
    $security_settings['email_verification'] = isset($_POST['email_verification']) ? 1 : 0;
    $security_settings['sms_verification'] = isset($_POST['sms_verification']) ? 1 : 0;
    $security_settings['phone_verification'] = isset($_POST['phone_verification']) ? 1 : 0;
    $security_settings['login_verification'] = isset($_POST['login_verification']) ? 1 : 0;
    $security_settings['password_verification'] = isset($_POST['password_verification']) ? 1 : 0;
    
    $current_user->setPreference('security_settings', $security_settings, 'global');
    $current_user->savePreferencesToDB();
    
    $message = "Security settings updated successfully!";
    $messageType = "success";
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if (User::checkPassword($current_user, $current_password)) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 8) {
                $current_user->setNewPassword($new_password);
                $current_user->save();
                $message = "Password changed successfully!";
                $messageType = "success";
            } else {
                $message = "Password must be at least 8 characters long.";
                $messageType = "danger";
            }
        } else {
            $message = "New passwords do not match.";
            $messageType = "danger";
        }
    } else {
        $message = "Current password is incorrect.";
        $messageType = "danger";
    }
}

// Handle account deactivation
if (isset($_POST['deactivate_account'])) {
    $reason = trim($_POST['deactivation_reason'] ?? '');
    $password = $_POST['confirm_password_deactivate'];
    
    if (User::checkPassword($current_user, $password)) {
        $current_user->status = 'Inactive';
        $current_user->employee_status = 'Terminated';
        
        // Log deactivation reason
        if (!empty($reason)) {
            $current_user->description = $reason;
        }
        
        if ($current_user->save()) {
            session_destroy();
            header("Location: login.php?account_deactivated=1");
            exit();
        }
    } else {
        $message = "Incorrect password. Account not deactivated.";
        $messageType = "danger";
    }
}

// Get current preferences
$notification_prefs = $current_user->getPreference('notification_preferences', 'global');
$security_settings = $current_user->getPreference('security_settings', 'global');

// Get user language options
$languages = get_languages();
?>

<style>
.profile-preview {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
</style>

<div class="main-content app-content">
    <div class="container-fluid"> 
        <!-- Page Header --> 
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
            <div>
                <h1 class="page-title font-medium text-lg mb-0">Profile Settings</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div> 
        </div> 
        
        <!-- Alert Messages -->
        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="">
            <div class="max-w-[85%] mx-auto">
                <div class="box">
                    <ul class="nav nav-tabs tab-style-8 scaleX rounded m-4 profile-settings-tab gap-2 flex flex-wrap" id="profileTabs" role="tablist">
                        <li class="nav-item me-1" role="presentation">
                            <button type="button" class="nav-link !px-6 text-primary rounded-md bg-primary/10 <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'account') ? 'active' : ''; ?>" data-hs-tab="#account-pane" role="tab" data-tab="account">
                                <i class="ri-user-settings-line me-2"></i>Account
                            </button>
                        </li>
                        <li class="nav-item me-1" role="presentation">
                            <button type="button" class="nav-link !px-6 text-primary rounded-md bg-primary/10 <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'notification') ? 'active' : ''; ?>" data-hs-tab="#notification-tab-pane" role="tab" data-tab="notification">
                                <i class="ri-notification-line me-2"></i>Notification
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link !px-6 text-primary rounded-md bg-primary/10 <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'security') ? 'active' : ''; ?>" data-hs-tab="#security-tab-pane" role="tab" data-tab="security">
                                <i class="ri-shield-keyhole-line me-2"></i>Security
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link !px-6 text-primary rounded-md bg-primary/10 <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'password') ? 'active' : ''; ?>" data-hs-tab="#password-tab-pane" role="tab" data-tab="password">
                                <i class="ri-lock-password-line me-2"></i>Change Password
                            </button>
                        </li>
                    </ul>
                    
                    <div class="p-4 border-b border-t border-dashed border-defaultborder dark:border-defaultborder/10 tab-content">
                        <!-- Account Settings Tab -->
                        <div class="tab-pane <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'account') ? 'show active' : ''; ?> overflow-hidden p-0 border-0" id="account-pane" role="tabpanel">
                            <form method="POST" enctype="multipart/form-data" id="profileForm">
                                <div class="flex justify-between items-center mb-4 flex-wrap gap-1">
                                    <div class="font-semibold block text-[15px]">
                                        <i class="ri-user-settings-line me-2"></i>Account Settings :
                                    </div>
                                    <button type="submit" name="update_profile" class="ti-btn ti-btn-primary ti-btn-sm">
                                        <i class="ri-save-line leading-none me-2"></i>Save Changes
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-12 sm:gap-x-6 gap-y-3">
                                    <div class="xl:col-span-12 col-span-12">
                                        <div class="flex items-start flex-wrap gap-4">
                                            <div class="position-relative">
                                                <?php 
                                                $profile_pic = !empty($current_user->picture) && file_exists($current_user->picture) 
                                                    ? $current_user->picture 
                                                    : 'assets/images/faces/default-avatar.png';
                                                ?>
                                                <img src="<?php echo $profile_pic; ?>" alt="Profile" class="profile-preview" id="profilePreview">
                                            </div>
                                            <div>
                                                <span class="font-medium block mb-2">Profile Picture</span>
                                                <div class="btn-list mb-1">
                                                    <label for="profile_picture" class="ti-btn ti-btn-sm ti-btn-primary btn-wave waves-effect waves-light cursor-pointer">
                                                        <i class="ri-upload-2-line me-1"></i>Change Image
                                                    </label>
                                                    <input type="file" name="profile_picture" id="profile_picture" class="hidden" accept="image/jpeg,image/png,image/gif" onchange="previewImage(this)">
                                                    <button type="button" class="ti-btn ti-btn-sm ti-btn-soft-primary1 btn-wave waves-effect waves-light" onclick="removeProfilePicture()">
                                                        <i class="ri-delete-bin-line me-1"></i>Remove
                                                    </button>
                                                </div>
                                                <span class="block text-xs text-textmuted dark:text-textmuted/50">
                                                    Use JPEG, PNG, or GIF. Best size: 200x200 pixels. Keep it under 5MB
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="xl:col-span-6 col-span-12">
                                        <label for="first_name" class="form-label">First Name : <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" 
                                            value="<?php echo htmlspecialchars($current_user->first_name ?? ''); ?>" 
                                            placeholder="Enter First Name" required>
                                    </div>
                                    
                                    <div class="xl:col-span-6 col-span-12">
                                        <label for="last_name" class="form-label">Last Name : <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" 
                                            value="<?php echo htmlspecialchars($current_user->last_name ?? ''); ?>" 
                                            placeholder="Enter Last Name" required>
                                    </div>
                                    
                                    <div class="xl:col-span-6 col-span-12">
                                        <label for="email" class="form-label">Email : <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                            value="<?php echo htmlspecialchars($current_user->email1 ?? ''); ?>" 
                                            placeholder="Enter Email" required>
                                    </div>
                                    
                                    <div class="xl:col-span-6 col-span-12">
                                        <label for="designation" class="form-label">Designation :</label>
                                        <input type="text" class="form-control" id="designation" name="designation" 
                                            value="<?php echo htmlspecialchars($current_user->title ?? ''); ?>" 
                                            placeholder="Enter Designation">
                                    </div>
                                    
                                    <div class="xl:col-span-6 col-span-12">
                                        <label for="language" class="form-label">Language :</label>
                                        <select class="form-control" id="language" name="language">
                                            <option value="en_us" <?php echo ($current_user->language == 'en_us') ? 'selected' : ''; ?>>English (US)</option>
                                            <option value="en_uk" <?php echo ($current_user->language == 'en_uk') ? 'selected' : ''; ?>>English (UK)</option>
                                            <option value="ar" <?php echo ($current_user->language == 'ar') ? 'selected' : ''; ?>>Arabic</option>
                                            <option value="ko" <?php echo ($current_user->language == 'ko') ? 'selected' : ''; ?>>Korean</option>
                                            <option value="zh" <?php echo ($current_user->language == 'zh') ? 'selected' : ''; ?>>Chinese</option>
                                            <option value="fr" <?php echo ($current_user->language == 'fr') ? 'selected' : ''; ?>>French</option>
                                            <option value="de" <?php echo ($current_user->language == 'de') ? 'selected' : ''; ?>>German</option>
                                            <option value="es" <?php echo ($current_user->language == 'es') ? 'selected' : ''; ?>>Spanish</option>
                                        </select>
                                    </div>
                                    
                                    <div class="xl:col-span-6 col-span-12">
                                        <label for="phone" class="form-label">Phone No :</label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                            value="<?php echo htmlspecialchars($current_user->phone_mobile ?? ''); ?>" 
                                            placeholder="Enter Phone Number">
                                    </div>
                                    
                                    <div class="xl:col-span-6 col-span-12">
                                        <label for="website" class="form-label">Website :</label>
                                        <input type="url" class="form-control" id="website" name="website" 
                                            placeholder="https://" 
                                            value="<?php echo htmlspecialchars($current_user->website ?? ''); ?>">
                                    </div>
                                    
                                    <div class="xl:col-span-6 col-span-12">
                                        <label for="city" class="form-label">City :</label>
                                        <input type="text" class="form-control" id="city" name="city" 
                                            value="<?php echo htmlspecialchars($current_user->address_city ?? ''); ?>" 
                                            placeholder="Enter City">
                                    </div>
                                    
                                    <div class="xl:col-span-6 col-span-12">
                                        <label for="state" class="form-label">State/Province :</label>
                                        <input type="text" class="form-control" id="state" name="state" 
                                            value="<?php echo htmlspecialchars($current_user->address_state ?? ''); ?>" 
                                            placeholder="Enter State">
                                    </div>
                                    
                                    <div class="xl:col-span-6 col-span-12">
                                        <label for="country" class="form-label">Country :</label>
                                        <input type="text" class="form-control" id="country" name="country" 
                                            value="<?php echo htmlspecialchars($current_user->address_country ?? ''); ?>" 
                                            placeholder="Enter Country">
                                    </div>
                                    
                                    <div class="xl:col-span-6 col-span-12">
                                        <label for="postal_code" class="form-label">Postal Code :</label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                            value="<?php echo htmlspecialchars($current_user->address_postalcode ?? ''); ?>" 
                                            placeholder="Enter Postal Code">
                                    </div>
                                    
                                    <div class="xl:col-span-12 col-span-12">
                                        <label for="address" class="form-label">Street Address :</label>
                                        <textarea class="form-control" id="address" name="address" rows="3" 
                                            placeholder="Enter Street Address"><?php echo htmlspecialchars($current_user->address_street ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Notification Settings Tab -->
                        <div class="tab-pane <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'notification') ? 'show active' : ''; ?> overflow-hidden p-0 border-0" id="notification-tab-pane" role="tabpanel">
                            <form method="POST" id="notificationForm">
                                <div class="flex justify-between items-center mb-4 flex-wrap gap-1">
                                    <div class="font-semibold block text-[15px]">
                                        <i class="ri-notification-line me-2"></i>Notifications Settings:
                                    </div>
                                    <button type="submit" name="update_notifications" class="ti-btn ti-btn-primary ti-btn-sm">
                                        <i class="ri-save-line leading-none me-2"></i>Save Notification Settings
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-12 sm:gap-x-6 gx-5 gap-y-3">
                                    <div class="xl:col-span-12 col-span-12">
                                        <p class="text-[14px] mb-1 font-medium">Configure Notifications</p>
                                        <p class="text-xs mb-0 text-textmuted dark:text-textmuted/50">
                                            Choose how you want to receive notifications and alerts.
                                        </p>
                                    </div>
                                    
                                    <div class="xl:col-span-12 col-span-12">
                                        <div class="flex items-top justify-between mt-3 p-3 bg-gray-50 rounded">
                                            <div class="mail-notification-settings">
                                                <p class="text-[14px] mb-1 font-medium">Push Notifications</p>
                                                <p class="text-xs mb-0 text-textmuted">
                                                    Alerts sent to your mobile device or desktop browser.
                                                </p>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="push_notifications" id="push_notifications"
                                                    <?php echo (!empty($notification_prefs['push_notifications'])) ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-top justify-between mt-3 p-3 bg-gray-50 rounded">
                                            <div class="mail-notification-settings">
                                                <p class="text-[14px] mb-1 font-medium">Email Notifications</p>
                                                <p class="text-xs mb-0 text-textmuted">
                                                    Receive updates and alerts via email.
                                                </p>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="email_notifications" id="email_notifications"
                                                    <?php echo (!empty($notification_prefs['email_notifications'])) ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-top justify-between mt-3 p-3 bg-gray-50 rounded">
                                            <div class="mail-notification-settings">
                                                <p class="text-[14px] mb-1 font-medium">In-App Notifications</p>
                                                <p class="text-xs mb-0 text-textmuted">
                                                    Alerts that appear within the application interface.
                                                </p>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="in_app_notifications" id="in_app_notifications"
                                                    <?php echo (!empty($notification_prefs['in_app_notifications'])) ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-top justify-between mt-3 p-3 bg-gray-50 rounded">
                                            <div class="mail-notification-settings">
                                                <p class="text-[14px] mb-1 font-medium">SMS Notifications</p>
                                                <p class="text-xs mb-0 text-textmuted">
                                                    Text messages sent to your mobile phone.
                                                </p>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="sms_notifications" id="sms_notifications"
                                                    <?php echo (!empty($notification_prefs['sms_notifications'])) ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Security Settings Tab -->
                        <div class="tab-pane <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'security') ? 'show active' : ''; ?> overflow-hidden p-0 border-0" id="security-tab-pane" role="tabpanel">
                            <form method="POST" id="securityForm">
                                <div class="flex justify-between items-center mb-4 flex-wrap gap-1">
                                    <div class="font-semibold block text-[15px]">
                                        <i class="ri-shield-keyhole-line me-2"></i>Security Settings :
                                    </div>
                                    <button type="submit" name="update_security" class="ti-btn ti-btn-primary ti-btn-sm">
                                        <i class="ri-save-line leading-none me-2"></i>Save Security Settings
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12">
                                        <div class="sm:flex block items-top justify-between p-3 bg-gray-50 rounded">
                                            <div class="w-50">
                                                <p class="text-[14px] mb-1 font-medium">Two-Factor Authentication</p>
                                                <p class="text-xs mb-0 text-textmuted">
                                                    Add an extra layer of security to your account.
                                                </p>
                                            </div>
                                            <button type="button" class="ti-btn ti-btn-outline-primary ti-btn-sm" onclick="setup2FA()">
                                                <i class="ri-qr-code-line me-2"></i>Setup 2FA
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="col-span-12">
                                        <div class="p-3 bg-gray-50 rounded">
                                            <p class="text-[14px] mb-2 font-medium">Verification Methods</p>
                                            <p class="text-xs mb-2 text-textmuted">
                                                Control how your identity is verified for sensitive operations.
                                            </p>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                        name="email_verification" id="email_verification"
                                                        <?php echo (!empty($security_settings['email_verification'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="email_verification">
                                                        Email Verification
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                        name="sms_verification" id="sms_verification"
                                                        <?php echo (!empty($security_settings['sms_verification'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="sms_verification">
                                                        SMS Verification
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                        name="phone_verification" id="phone_verification"
                                                        <?php echo (!empty($security_settings['phone_verification'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="phone_verification">
                                                        Phone Call Verification
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-span-12">
                                        <div class="sm:flex block items-top justify-between p-3 bg-gray-50 rounded">
                                            <div class="w-50">
                                                <p class="text-[14px] mb-1 font-medium">Login Verification</p>
                                                <p class="text-xs mb-0 text-textmuted">
                                                    Require verification when logging in from new devices.
                                                </p>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="login_verification" id="login_verification"
                                                    <?php echo (!empty($security_settings['login_verification'])) ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-span-12">
                                        <div class="sm:flex block items-top justify-between p-3 bg-gray-50 rounded">
                                            <div class="w-50">
                                                <p class="text-[14px] mb-1 font-medium">Password Verification for Changes</p>
                                                <p class="text-xs mb-0 text-textmuted">
                                                    Require password verification when changing account details.
                                                </p>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="password_verification" id="password_verification"
                                                    <?php echo (!empty($security_settings['password_verification'])) ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-span-12">
                                        <div class="p-3 bg-gray-50 rounded">
                                            <p class="text-[14px] mb-2 font-medium">Session Management</p>
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm mb-1">Active Sessions</p>
                                                    <p class="text-xs text-textmuted">You are currently logged in on this device</p>
                                                </div>
                                                <button type="button" class="ti-btn ti-btn-outline-danger ti-btn-sm" onclick="logoutAllDevices()">
                                                    <i class="ri-logout-box-line me-2"></i>Logout All Devices
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Change Password Tab -->
                        <div class="tab-pane <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'password') ? 'show active' : ''; ?> overflow-hidden p-0 border-0" id="password-tab-pane" role="tabpanel">
                            <form method="POST" id="passwordForm" onsubmit="return validatePassword()">
                                <div class="flex justify-between items-center mb-4 flex-wrap gap-1">
                                    <div class="font-semibold block text-[15px]">
                                        <i class="ri-lock-password-line me-2"></i>Change Password :
                                    </div>
                                    <button type="submit" name="change_password" class="ti-btn ti-btn-primary ti-btn-sm">
                                        <i class="ri-save-line leading-none me-2"></i>Update Password
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="xl:col-span-12 col-span-12">
                                        <label for="current_password" class="form-label">Current Password : <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="xl:col-span-6 col-span-12">
                                        <label for="new_password" class="form-label">New Password : <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                        </div>
                                        <div class="password-strength mt-2">
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar" id="passwordStrength" style="width: 0%;"></div>
                                            </div>
                                            <small class="text-muted" id="passwordStrengthText">Enter new password</small>
                                        </div>
                                    </div>
                                    
                                    <div class="xl:col-span-6 col-span-12">
                                        <label for="confirm_password" class="form-label">Confirm New Password : <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                        </div>
                                        <div id="passwordMatch" class="mt-1 small"></div>
                                    </div>
                                    
                                    <div class="xl:col-span-12 col-span-12">
                                        <div class="alert alert-info">
                                            <i class="ri-information-line me-2"></i>
                                            <strong>Password Requirements:</strong>
                                            <ul class="mt-2 mb-0">
                                                <li>Minimum 8 characters long</li>
                                                <li>At least one uppercase letter</li>
                                                <li>At least one lowercase letter</li>
                                                <li>At least one number</li>
                                                <li>At least one special character (!@#$%^&*)</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="box-footer border-t-0">
                        <div class="btn-list float-end">
                            <button type="button" class="ti-btn bg-danger text-white btn-wave waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#deactivateModal">
                                <i class="ri-delete-bin-line me-2"></i>Deactivate Account
                            </button>
                            <button type="button" class="ti-btn ti-btn-primary btn-wave waves-effect waves-light" onclick="saveActiveTab()">
                                <i class="ri-save-line me-2"></i>Save Current Tab
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deactivate Account Modal -->
<div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deactivateModalLabel">Deactivate Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="deactivateForm">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ri-alert-line me-2"></i>
                        <strong>Warning:</strong> Deactivating your account will:
                        <ul class="mt-2 mb-0">
                            <li>Hide your profile from other users</li>
                            <li>Prevent you from logging in</li>
                            <li>Stop all notifications</li>
                            <li>You can reactivate later by contacting support</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label for="deactivation_reason" class="form-label">Reason for deactivation (optional):</label>
                        <textarea class="form-control" id="deactivation_reason" name="deactivation_reason" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password_deactivate" class="form-label">Enter your password to confirm: <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password_deactivate" name="confirm_password_deactivate" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ti-btn ti-btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="deactivate_account" class="ti-btn bg-danger text-white">Deactivate Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once "components/layout/footer.php"; ?>