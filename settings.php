<?php include_once 'components/layout/header.php'; ?>
<?php include_once 'components/layout/sidebar.php'; ?>

<?php
$u = $_SESSION['user'] ?? [];
$userId = trim((string)($u['id'] ?? ''));
$firstName = trim((string)($u['first_name'] ?? ''));
$lastName  = trim((string)($u['last_name'] ?? ''));
$userName  = trim((string)($u['user_name'] ?? ''));
$email     = trim((string)($u['email'] ?? ($u['email1'] ?? '')));
$phone     = trim((string)($u['phone_work'] ?? ($u['phone'] ?? '')));

$displayName = trim($firstName . ' ' . $lastName);
if ($displayName === '') {
  $displayName = $userName !== '' ? $userName : 'User';
}

$initials = '?';
$n1 = mb_substr($firstName !== '' ? $firstName : $displayName, 0, 1, 'UTF-8');
$n2 = $lastName !== '' ? mb_substr($lastName, 0, 1, 'UTF-8') : '';
if ($n2 === '' && $userName !== '') {
  $n2 = mb_substr($userName, 0, 1, 'UTF-8');
}
$initials = strtoupper(mb_substr(trim($n1 . $n2), 0, 2, 'UTF-8') ?: '?');

// Badge: admins / owners typically have empty role assignment in this CRM build
$accountBadge = (!empty($u['role_id'])) ? 'Team' : 'Admin';
?>

<style>
  .stg-page {
    --stg-card: rgba(255,255,255,0.04);
    --stg-card-border: rgba(255,255,255,0.08);
    --stg-inner: rgba(15,23,42,0.45);
    --stg-muted: rgba(226,232,240,0.55);
    --stg-heading: rgba(248,250,252,0.96);
    --stg-accent: #CF1C82;
    --stg-accent-dim: rgba(207, 28, 130 , 0.12);
    --stg-gold: #CF1C82;
    --stg-input-bg: rgba(15,23,42,0.55);
    --stg-input-border: rgba(148,163,184,0.18);
    /* max-width: 1120px; */
    margin: 0 auto;
  }
  html:not(.dark) .stg-page {
    --stg-card: #ffffff;
    --stg-card-border: rgba(15,23,42,0.08);
    --stg-inner: #f8fafc;
    --stg-muted: rgba(15,23,42,0.55);
    --stg-heading: #0f172a;
    --stg-accent: #0d9488;
    --stg-accent-dim: rgba(13,148,136,0.12);
    --stg-input-bg: #f1f5f9;
    --stg-input-border: rgba(15,23,42,0.12);
  }

  .stg-card {
    background: var(--stg-card);
    border: 1px solid var(--stg-card-border);
    border-radius: 18px;
    padding: 24px 26px 28px;
    height: 100%;
    box-shadow: 0 10px 40px rgba(0,0,0,0.12);
  }
  html:not(.dark) .stg-card {
    box-shadow: 0 8px 30px rgba(15,23,42,0.06);
  }

  .stg-card-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--stg-heading);
    margin: 0 0 22px;
  }
  .stg-card-title i { font-size: 1.15rem; color: var(--stg-accent); }

  .stg-header-block {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--stg-card-border);
  }
  .stg-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: var(--stg-accent-dim);
    color: var(--stg-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    flex-shrink: 0;
    border: 2px solid rgba(45,212,191,0.35);
  }
  html:not(.dark) .stg-avatar {
    border-color: rgba(13,148,136,0.25);
  }
  .stg-meta-name { font-size: 1.12rem; font-weight: 700; color: var(--stg-heading); line-height: 1.2; }
  .stg-meta-handle { font-size: 0.82rem; color: var(--stg-muted); margin-top: 4px; }
  .stg-badge-admin {
    display: inline-block;
    margin-top: 8px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 999px;
    background: var(--stg-gold);
    color: #fff;
    box-shadow: 0 2px 8px rgba(212,175,55,0.25);
  }

  .stg-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--stg-muted);
    margin-bottom: 7px;
  }
  .stg-input, .stg-input-wrap input {
    width: 100%;
    height: 42px;
    border-radius: 11px;
    border: 1px solid var(--stg-input-border);
    background: var(--stg-input-bg);
    color: var(--stg-heading);
    padding: 0 14px;
    font-size: 13px;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
  }
  .stg-input:focus, .stg-input-wrap input:focus {
    border-color: var(--stg-accent);
    box-shadow: 0 0 0 3px var(--stg-accent-dim);
  }
  .stg-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  @media (max-width: 640px) { .stg-row-2 { grid-template-columns: 1fr; } }
  .stg-field { margin-bottom: 16px; }

  .stg-input-wrap { position: relative; }
  .stg-input-wrap input { padding-right: 44px; }
  .stg-toggle-pw {
    position: absolute;
    right: 4px;
    top: 50%;
    transform: translateY(-50%);
    width: 36px;
    height: 36px;
    border: none;
    background: transparent;
    border-radius: 8px;
    color: var(--stg-muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
  }
  .stg-toggle-pw:hover { color: var(--stg-accent); }

  .stg-actions { display: flex; justify-content: flex-end; margin-top: 8px; gap: 10px; }
  .stg-btn-primary {
    height: 42px;
    padding: 0 22px;
    border-radius: 11px;
    border: none;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    background: rgb(var(--primary-rgb));
    color: #fff;
    transition: filter 0.15s, transform 0.15s;
  }
  .stg-btn-primary:hover { filter: brightness(1.05); transform: translateY(-1px); }
  .stg-btn-gold {
    height: 42px;
    padding: 0 22px;
    border-radius: 11px;
    border: none;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    background: #CF1C82;
    color: #fff;
    box-shadow: 0 4px 14px rgba(212,175,55,0.28);
    transition: filter 0.15s;
  }
  .stg-btn-gold:hover { filter: brightness(1.06); }

  .stg-head {
    margin-bottom: 28px;
  }
  .stg-head h1 {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--stg-heading);
    margin: 0 0 6px;
  }
  .stg-head p { margin: 0; font-size: 13px; color: var(--stg-muted); }

  .stg-input.stg-invalid, .stg-input-wrap input.stg-invalid {
    border-color: #dc2626;
    box-shadow: 0 0 0 2px rgba(220,38,38,0.22);
  }
  .stg-field-hint {
    font-size: 11px;
    color: var(--stg-muted);
    margin-top: 5px;
    line-height: 1.35;
  }
  .stg-field-hint.stg-invalid-text { color: #f87171; }
  html:not(.dark) .stg-field-hint.stg-invalid-text { color: #dc2626; }
</style>

<div class="main-content app-content">
  <div class="container-fluid py-4">
    <div class="stg-page">
      <div class="stg-head">
        <h1>Settings</h1>
        <p>Update your profile and password. Changes apply to your LimoCRM account.</p>
      </div>

      <div class="grid grid-cols-12 gap-6">
        <!-- Profile -->
        <div class="xl:col-span-6 lg:col-span-6 col-span-12">
          <div class="stg-card">
            <h2 class="stg-card-title"><i class="ri-user-settings-line"></i> Profile</h2>

            <div class="stg-header-block">
              <div class="stg-avatar" aria-hidden="true"><?php echo htmlspecialchars($initials, ENT_QUOTES, 'UTF-8'); ?></div>
              <div>
                <div class="stg-meta-name"><?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="stg-meta-handle">@<?php echo htmlspecialchars($userName ?: 'username', ENT_QUOTES, 'UTF-8'); ?></div>
                <span class="stg-badge-admin"><?php echo htmlspecialchars($accountBadge, ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
            </div>

            <form id="stg-profile-form" autocomplete="on" novalidate>
              <input type="hidden" name="id" value="<?php echo htmlspecialchars($userId, ENT_QUOTES, 'UTF-8'); ?>">

              <div class="stg-row-2">
                <div class="stg-field">
                  <label class="stg-label" for="pf-first">First name</label>
                  <input class="stg-input" id="pf-first" name="first_name" type="text" required maxlength="100"
                         autocomplete="given-name"
                         value="<?php echo htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>">
                  <div class="stg-field-hint" data-hint-for="pf-first"></div>
                </div>
                <div class="stg-field">
                  <label class="stg-label" for="pf-last">Last name</label>
                  <input class="stg-input" id="pf-last" name="last_name" type="text" required maxlength="100"
                         autocomplete="family-name"
                         value="<?php echo htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'); ?>">
                  <div class="stg-field-hint" data-hint-for="pf-last"></div>
                </div>
              </div>

              <div class="stg-field">
                <label class="stg-label" for="pf-email">Email</label>
                <input class="stg-input" id="pf-email" name="email" type="email" required maxlength="254"
                       autocomplete="email" inputmode="email"
                       value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="stg-field-hint" data-hint-for="pf-email"></div>
              </div>

              <div class="stg-field">
                <label class="stg-label" for="pf-phone">Phone</label>
                <input class="stg-input" id="pf-phone" name="phone" type="tel" maxlength="32" autocomplete="tel"
                       placeholder="+1 555 123 4567 (optional)"
                       value="<?php echo htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="stg-field-hint" data-hint-for="pf-phone"></div>
              </div>

              <div class="stg-field">
                <label class="stg-label" for="pf-username">Username</label>
                <input class="stg-input" id="pf-username" name="user_name" type="text" required maxlength="64"
                       autocomplete="username" autocapitalize="none" spellcheck="false"
                       value="<?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="stg-field-hint" data-hint-for="pf-username"></div>
              </div>

              <div class="stg-actions">
                <button type="submit" class="stg-btn-primary" id="pf-save-btn">Save profile</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Password -->
        <div class="xl:col-span-6 lg:col-span-6 col-span-12">
          <div class="stg-card">
            <h2 class="stg-card-title"><i class="ri-lock-2-line"></i> Change password</h2>

            <form id="stg-password-form" autocomplete="off" novalidate>
              <div class="stg-field">
                <label class="stg-label" for="pw-current">Current password</label>
                <div class="stg-input-wrap">
                  <input class="stg-input" id="pw-current" name="current_password" type="password" maxlength="128"
                         autocomplete="current-password" placeholder="Enter current password">
                  <button type="button" class="stg-toggle-pw" data-target="pw-current" aria-label="Show password"><i class="ri-eye-line"></i></button>
                </div>
                <div class="stg-field-hint" data-hint-for="pw-current"></div>
              </div>
              <div class="stg-field">
                <label class="stg-label" for="pw-new">New password</label>
                <div class="stg-input-wrap">
                  <input class="stg-input" id="pw-new" name="new_password" type="password" maxlength="128"
                         autocomplete="new-password" placeholder="8+ chars, include a letter and a number">
                  <button type="button" class="stg-toggle-pw" data-target="pw-new" aria-label="Show password"><i class="ri-eye-line"></i></button>
                </div>
                <div class="stg-field-hint" data-hint-for="pw-new"></div>
              </div>
              <div class="stg-field">
                <label class="stg-label" for="pw-confirm">Confirm new password</label>
                <input class="stg-input" id="pw-confirm" name="confirm_password" type="password" maxlength="128"
                       autocomplete="new-password" placeholder="Re-enter new password">
                <div class="stg-field-hint" data-hint-for="pw-confirm"></div>
              </div>

              <div class="stg-actions">
                <button type="submit" class="stg-btn-gold" id="pw-save-btn">Change password</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once 'components/layout/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
  var USER_ID = <?php echo json_encode($userId, JSON_HEX_TAG | JSON_HEX_APOS); ?>;

  var RE_NAME = /^[\p{L}\p{M}\s.'\-]{1,100}$/u;
  /* Practical email shape; server still validates with PHP filter_var. */
  var RE_EMAIL = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,253}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,253}[a-zA-Z0-9])?)+$/;
  var RE_USERNAME = /^[a-zA-Z0-9._@-]{3,64}$/;
  var RE_NEW_PASSWORD = /^(?=.*[A-Za-z\p{L}])(?=.*\d).{8,128}$/u;

  function clearFieldErrors(ids) {
    ids.forEach(function (id) {
      var el = document.getElementById(id);
      var hint = document.querySelector('.stg-field-hint[data-hint-for="' + id + '"]');
      if (el) el.classList.remove('stg-invalid');
      if (hint) { hint.textContent = ''; hint.classList.remove('stg-invalid-text'); }
    });
  }

  function setFieldError(id, message) {
    var el = document.getElementById(id);
    var hint = document.querySelector('.stg-field-hint[data-hint-for="' + id + '"]');
    if (el) el.classList.add('stg-invalid');
    if (hint) {
      hint.textContent = message;
      hint.classList.add('stg-invalid-text');
    }
  }

  /** Optional phone: 7–15 digits, max 32 chars, common formatting only. */
  function validOptionalPhone(raw) {
    var p = raw.trim();
    if (p === '') return { ok: true };
    if (p.length > 32) return { ok: false, msg: 'Phone is too long (max 32 characters).' };
    var ext = /\s*(?:ext\.?|extension)\s*\d+/i;
    var base = p.replace(ext, '').trim();
    /* Allow ASCII phone chars only (blocks emoji / stray symbols). */
    if (!/^[\d\s()+\-\.]+$/.test(base)) return { ok: false, msg: 'Use digits, spaces, + ( ) . and - only.' };
    var digits = base.replace(/\D/g, '');
    if (digits.length < 7) return { ok: false, msg: 'Enter at least 7 digits, or leave phone blank.' };
    if (digits.length > 15) return { ok: false, msg: 'Too many digits (max 15).' };
    return { ok: true };
  }

  function validateProfile() {
    var ids = ['pf-first', 'pf-last', 'pf-email', 'pf-phone', 'pf-username'];
    clearFieldErrors(ids);

    var first = $('#pf-first').val().trim();
    var last = $('#pf-last').val().trim();
    var email = $('#pf-email').val().trim();
    var phone = $('#pf-phone').val();
    var user = $('#pf-username').val().trim();

    if (!first) { setFieldError('pf-first', 'First name is required.'); return null; }
    if (!RE_NAME.test(first)) { setFieldError('pf-first', 'Use letters and common name characters only (spaces, . \' -).'); return null; }
    if (!last) { setFieldError('pf-last', 'Last name is required.'); return null; }
    if (!RE_NAME.test(last)) { setFieldError('pf-last', 'Use letters and common name characters only (spaces, . \' -).'); return null; }

    if (!email) { setFieldError('pf-email', 'Email is required.'); return null; }
    if (email.length > 254) { setFieldError('pf-email', 'Email is too long.'); return null; }
    if (!RE_EMAIL.test(email)) { setFieldError('pf-email', 'Enter a valid email address.'); return null; }

    var phoneChk = validOptionalPhone(phone);
    if (!phoneChk.ok) { setFieldError('pf-phone', phoneChk.msg); return null; }

    if (!user) { setFieldError('pf-username', 'Username is required.'); return null; }
    if (!RE_USERNAME.test(user)) { setFieldError('pf-username', '3–64 characters: letters, numbers, . _ @ - only.'); return null; }

    return $('#stg-profile-form').serialize();
  }

  function validatePasswordForm() {
    var ids = ['pw-current', 'pw-new', 'pw-confirm'];
    clearFieldErrors(ids);
    /* Do not trim passwords — spaces may be intentional. */
    var cur = $('#pw-current').val();
    var nw = $('#pw-new').val();
    var cf = $('#pw-confirm').val();

    if (!cur.length) {
      setFieldError('pw-current', 'Enter your current password.');
      return null;
    }
    if (cur.length > 128) {
      setFieldError('pw-current', 'Password is too long.');
      return null;
    }

    if (nw.length < 8 || nw.length > 128 || !RE_NEW_PASSWORD.test(nw)) {
      setFieldError('pw-new', 'Use 8–128 characters with at least one letter and one number.');
      return null;
    }
    if (nw !== cf) {
      setFieldError('pw-confirm', 'Does not match the new password.');
      return null;
    }

    return { current_password: cur, new_password: nw };
  }

  $('.stg-toggle-pw').on('click', function () {
    var id = $(this).data('target');
    var $input = $('#' + id);
    var $ico = $(this).find('i');
    var isPw = $input.attr('type') === 'password';
    $input.attr('type', isPw ? 'text' : 'password');
    $ico.attr('class', isPw ? 'ri-eye-off-line' : 'ri-eye-line');
  });

  $('#stg-profile-form').on('input change', '.stg-input', function () {
    var id = this.id;
    if (['pf-first', 'pf-last', 'pf-email', 'pf-phone', 'pf-username'].indexOf(id) === -1) return;
    this.classList.remove('stg-invalid');
    var hint = document.querySelector('.stg-field-hint[data-hint-for="' + id + '"]');
    if (hint) { hint.textContent = ''; hint.classList.remove('stg-invalid-text'); }
  });

  $('#stg-password-form').on('input change', 'input.stg-input', function () {
    this.classList.remove('stg-invalid');
    var id = this.id;
    var hint = document.querySelector('.stg-field-hint[data-hint-for="' + id + '"]');
    if (hint) { hint.textContent = ''; hint.classList.remove('stg-invalid-text'); }
  });

  $('#stg-profile-form').on('submit', function (e) {
    e.preventDefault();
    var payload = validateProfile();
    if (payload === null) {
      var firstBad = document.querySelector('#stg-profile-form .stg-invalid');
      if (firstBad) firstBad.focus();
      return;
    }
    var $btn = $('#pf-save-btn');
    $btn.prop('disabled', true);
    $.post('config/profile_update_endpoint.php', payload)
      .done(function (res) {
        if (typeof res === 'string') { try { res = JSON.parse(res); } catch (x) { res = {}; } }
        if (res.success) {
          Swal.fire({ icon: 'success', title: 'Saved', text: res.message || 'Profile updated.', timer: 2000, showConfirmButton: false });
          setTimeout(function () { window.location.reload(); }, 850);
        } else {
          Swal.fire({ icon: 'error', title: 'Could not save', text: res.message || 'Please try again.' });
        }
      })
      .fail(function () {
        Swal.fire({ icon: 'error', title: 'Network error', text: 'Please try again.' });
      })
      .always(function () { $btn.prop('disabled', false); });
  });

  $('#stg-password-form').on('submit', function (e) {
    e.preventDefault();
    var creds = validatePasswordForm();
    if (creds === null) {
      var firstBad = document.querySelector('#stg-password-form .stg-invalid');
      if (firstBad) firstBad.focus();
      return;
    }
    var $btn = $('#pw-save-btn');
    $btn.prop('disabled', true);
    $.ajax({
      url: 'config/change_password_endpoint.php',
      method: 'POST',
      dataType: 'json',
      data: { id: USER_ID, current_password: creds.current_password, new_password: creds.new_password }
    })
      .done(function (data) {
        if (data && data.success) {
          $('#stg-password-form')[0].reset();
          clearFieldErrors(['pw-current', 'pw-new', 'pw-confirm']);
          Swal.fire({ icon: 'success', title: 'Password updated', text: data.message || 'You can continue with your new password.', timer: 2200, showConfirmButton: false });
        } else {
          Swal.fire({ icon: 'error', title: 'Could not update', text: (data && data.message) || 'Check your current password.' });
        }
      })
      .fail(function () {
        Swal.fire({ icon: 'error', title: 'Network error', text: 'Please try again.' });
      })
      .always(function () { $btn.prop('disabled', false); });
  });
})();
</script>
