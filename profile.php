<?php
// Bootstrap before any output (session + auth redirect must run before
// layout includes emit any HTML).
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/config/api.php';

$u = $_SESSION['user'];

$firstName = trim((string)($u['first_name'] ?? ''));
$lastName  = trim((string)($u['last_name']  ?? ''));

$displayName = trim($firstName . ' ' . $lastName);
if ($displayName === '') {
    $displayName = trim((string)($u['user_name'] ?? '')) ?: 'User';
}

$email   = trim((string)($u['email1'] ?? ($u['email'] ?? '')));
$initial = strtoupper(mb_substr($displayName, 0, 1, 'UTF-8') ?: 'U');
?>
<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>

<style>
  .profile-avatar {
    width: 110px;
    height: 110px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 42px;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(135deg, var(--primary, #2563EB) 0%, rgba(37, 99, 235, 0.7) 100%);
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.25);
    letter-spacing: 0.5px;
  }

  /* Slightly opinionated tweaks for the SweetAlert change-password modal so
     password fields stack cleanly and look at home in both themes. */
  .swal-cp-form .cp-row { margin-top: 12px; text-align: left; }
  .swal-cp-form .cp-row:first-child { margin-top: 0; }
  .swal-cp-form label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    margin-bottom: 6px;
  }
  .dark .swal-cp-form label { color: rgba(255,255,255,0.65); }
  .swal-cp-form input.swal2-input {
    width: 100%;
    margin: 0 !important;
    box-shadow: none !important;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 14px;
  }
  .dark .swal-cp-form input.swal2-input {
    border-color: rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.04);
    color: rgba(255,255,255,0.9);
  }
</style>

<div class="main-content app-content">
  <div class="container-fluid">
    <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2 mb-4">
      <h1 class="page-title font-medium text-lg mb-0">Profile</h1>
    </div>

    <div class="grid grid-cols-12 gap-6">
      <div class="col-span-12 md:col-span-8 lg:col-span-6 xl:col-span-5 mx-auto w-full">
        <div class="box overflow-hidden">
          <div class="box-body text-center py-10 px-6">

            <div class="profile-avatar mx-auto mb-4" style="font-size: 48px;color: blue;" aria-hidden="true">
              <?php echo htmlspecialchars($initial, ENT_QUOTES); ?>
            </div>

            <div class="mt-5">
              <div class="text-2xl font-semibold text-defaulttextcolor dark:text-defaulttextcolor/90 leading-tight">
                <?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>
              </div>
              <?php if ($email !== ''): ?>
                <div class="text-sm text-textmuted dark:text-textmuted/50 mt-1 inline-flex items-center gap-1">
                  <i class="ri-mail-line"></i>
                  <span><?php echo htmlspecialchars($email, ENT_QUOTES); ?></span>
                </div>
              <?php endif; ?>
            </div>

            <div class="mt-7">
              <button type="button" id="open-change-password"
                      class="ti-btn bg-primary text-white btn-wave waves-effect waves-light !rounded-lg px-5 py-2.5">
                <i class="ri-lock-password-line me-1 align-middle"></i>Change Password
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once "components/layout/footer.php"; ?>

<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $(function () {
    const USER_ID = <?php echo json_encode((string)($u['id'] ?? '')); ?>;

    $('#open-change-password').on('click', function () {
      Swal.fire({
        title: 'Change Password',
        html:
          '<div class="swal-cp-form">' +
            '<div class="cp-row">' +
              '<label for="cp-current">Current Password</label>' +
              '<input id="cp-current" type="password" class="swal2-input" placeholder="Enter current password" autocomplete="current-password">' +
            '</div>' +
            '<div class="cp-row">' +
              '<label for="cp-new">New Password</label>' +
              '<input id="cp-new" type="password" class="swal2-input" placeholder="Min 8 characters" autocomplete="new-password">' +
            '</div>' +
            '<div class="cp-row">' +
              '<label for="cp-confirm">Confirm New Password</label>' +
              '<input id="cp-confirm" type="password" class="swal2-input" placeholder="Re-enter new password" autocomplete="new-password">' +
            '</div>' +
          '</div>',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Update Password',
        cancelButtonText:  'Cancel',
        confirmButtonColor: '#2563EB',
        showLoaderOnConfirm: true,
        allowOutsideClick: function () { return !Swal.isLoading(); },
        preConfirm: function () {
          const current = document.getElementById('cp-current').value;
          const next    = document.getElementById('cp-new').value;
          const confirm = document.getElementById('cp-confirm').value;

          if (!current) { Swal.showValidationMessage('Please enter your current password.'); return false; }
          if (next.length < 8) { Swal.showValidationMessage('New password must be at least 8 characters.'); return false; }
          if (next !== confirm) { Swal.showValidationMessage('New passwords do not match.'); return false; }

          return $.ajax({
            url: 'config/change_password_endpoint.php',
            type: 'POST',
            dataType: 'json',
            data: {
              id: USER_ID,
              current_password: current,
              new_password:     next
            }
          }).then(function (data) {
            if (!data || !data.success) {
              Swal.showValidationMessage((data && data.message) || 'Could not change password.');
              return false;
            }
            return data;
          }).catch(function () {
            Swal.showValidationMessage('Network error. Please try again.');
            return false;
          });
        }
      }).then(function (res) {
        if (!res.isConfirmed) return;
        Swal.fire({
          icon: 'success',
          title: 'Password updated',
          text:  (res.value && res.value.message) || 'Your password has been changed successfully.',
          timer: 2200,
          showConfirmButton: false
        });
      });
    });
  });
</script>
