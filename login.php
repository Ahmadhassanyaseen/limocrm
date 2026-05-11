<!DOCTYPE html>
<html lang="en" class="crm-login-page">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="color-scheme" content="light dark" />
    <title>Sign in | LimoCRM</title>

    <link rel="icon" href="assets/images/brand-logos/favicon.ico" type="image/x-icon" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css"
      integrity="sha512-XcIsjKMcuVe0Ucj/xgIXQnytNwBttJbNjltBV18IOnru2lDPe9KRRyvCXw6Y5H415vbBLRm8+q6fmLUU7DfO6Q=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap"
      rel="stylesheet"
    />

    <style>
      :root {
        --crm-primary: #cf1c82;
        --crm-primary-hover: #b01670;
        --crm-primary-rgb: 207, 28, 130;
        --crm-surface: #ffffff;
        --crm-surface-elevated: rgba(255, 255, 255, 0.72);
        --crm-text: #0f172a;
        --crm-text-muted: #64748b;
        --crm-border: rgba(15, 23, 42, 0.08);
        --crm-input-bg: #f8fafc;
        --crm-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.06), 0 24px 48px -12px rgba(15, 23, 42, 0.12);
        --crm-radius-lg: 20px;
        --crm-radius-md: 12px;
        --crm-font: "DM Sans", system-ui, -apple-system, sans-serif;
      }

      @media (prefers-color-scheme: dark) {
        :root {
          --crm-surface: #0f1419;
          --crm-surface-elevated: rgba(22, 28, 36, 0.85);
          --crm-text: #f1f5f9;
          --crm-text-muted: #94a3b8;
          --crm-border: rgba(255, 255, 255, 0.08);
          --crm-input-bg: rgba(255, 255, 255, 0.06);
          --crm-shadow: 0 4px 24px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.06);
        }
      }

      *,
      *::before,
      *::after {
        box-sizing: border-box;
      }

      body.crm-login {
        margin: 0;
        min-height: 100vh;
        font-family: var(--crm-font);
        font-size: 15px;
        line-height: 1.5;
        color: var(--crm-text);
        background-color: var(--crm-surface);
        background-image:
          radial-gradient(ellipse 100% 80% at 0% 0%, rgba(var(--crm-primary-rgb), 0.14), transparent 50%),
          radial-gradient(ellipse 80% 60% at 100% 20%, rgba(14, 165, 233, 0.12), transparent 45%),
          radial-gradient(ellipse 60% 50% at 50% 100%, rgba(var(--crm-primary-rgb), 0.06), transparent 55%);
        -webkit-font-smoothing: antialiased;
      }

      @media (prefers-color-scheme: dark) {
        body.crm-login {
          background-image:
            radial-gradient(ellipse 100% 80% at 0% 0%, rgba(var(--crm-primary-rgb), 0.22), transparent 50%),
            radial-gradient(ellipse 80% 60% at 100% 15%, rgba(56, 189, 248, 0.12), transparent 45%),
            radial-gradient(ellipse 70% 40% at 50% 100%, rgba(var(--crm-primary-rgb), 0.1), transparent 50%);
        }
      }

      .crm-login-shell {
        display: grid;
        min-height: 100vh;
        grid-template-columns: 1fr;
      }

      @media (min-width: 1024px) {
        .crm-login-shell {
          grid-template-columns: minmax(0, 1fr) minmax(380px, 480px);
        }
      }

      /* Form column */
      .crm-login-form-col {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: clamp(1.5rem, 4vw, 3rem);
        position: relative;
      }

      .crm-login-form-inner {
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
      }

      .crm-login-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 2rem;
      }

      .crm-login-brand img {
        height: 32px;
        width: auto;
      }

      @media (prefers-color-scheme: dark) {
        .crm-login-brand .logo-light {
          display: none;
        }
        .crm-login-brand .logo-dark {
          display: block !important;
        }
      }

      .crm-login-brand .logo-dark {
        display: none;
      }

      .crm-login-eyebrow {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--crm-primary);
        margin: 0 0 0.5rem;
      }

      .crm-login-title {
        font-size: clamp(1.75rem, 4vw, 2rem);
        font-weight: 700;
        letter-spacing: -0.03em;
        line-height: 1.2;
        margin: 0 0 0.5rem;
        color: var(--crm-text);
      }

      .crm-login-sub {
        font-size: 0.9375rem;
        color: var(--crm-text-muted);
        margin: 0 0 2rem;
        max-width: 34ch;
      }

      .crm-login-card {
        background: var(--crm-surface-elevated);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--crm-border);
        border-radius: var(--crm-radius-lg);
        padding: clamp(1.5rem, 4vw, 2rem);
        box-shadow: var(--crm-shadow);
      }

      .crm-field {
        margin-bottom: 1.25rem;
      }

      .crm-field:last-of-type {
        margin-bottom: 0;
      }

      .crm-label {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--crm-text);
        margin-bottom: 0.5rem;
      }

      .crm-label .req {
        color: #ef4444;
        font-weight: 500;
      }

      .crm-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
        
      }

      .crm-input-wrap .crm-input-icon {
        position: absolute;
        left: 14px;
        color: var(--crm-text-muted);
        font-size: 1.125rem;
        pointer-events: none;
        z-index: 1;
      }

      .crm-input-wrap .form-control {
        width: 100%;
        height: 48px;
        padding-left: 2.75rem!important;
        padding-right: 3rem;
        border-radius: var(--crm-radius-md);
        border: 1px solid var(--crm-border);
        background: var(--crm-input-bg);
        color: var(--crm-text);
        font-family: inherit;
        font-size: 0.9375rem;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
      }

      .crm-input-wrap .form-control::placeholder {
        color: var(--crm-text-muted);
        opacity: 0.85;
      }

      .crm-input-wrap .form-control:hover {
        border-color: rgba(var(--crm-primary-rgb), 0.35);
      }

      .crm-input-wrap .form-control:focus {
        outline: none;
        border-color: var(--crm-primary);
        box-shadow: 0 0 0 3px rgba(var(--crm-primary-rgb), 0.2);
        background: var(--crm-surface);
      }

      @media (prefers-color-scheme: dark) {
        .crm-input-wrap .form-control:focus {
          background: rgba(255, 255, 255, 0.04);
        }
      }

      .crm-input-wrap .form-control.border-red-500 {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
      }

      .crm-input-wrap .show-password-button {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        color: var(--crm-text-muted);
        text-decoration: none;
        transition: color 0.2s, background 0.2s;
      }

      .crm-input-wrap .show-password-button:hover {
        color: var(--crm-text);
        background: rgba(var(--crm-primary-rgb), 0.08);
      }

      .crm-input-wrap .show-password-button:focus-visible {
        outline: 2px solid var(--crm-primary);
        outline-offset: 2px;
      }

      .crm-submit {
        width: 100%;
        margin-top: 1.5rem;
        height: 48px;
        border: none;
        border-radius: var(--crm-radius-md);
        font-family: inherit;
        font-size: 0.9375rem;
        font-weight: 600;
        color: #fff;
        background: var(--crm-primary);
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.2s, background 0.2s;
        box-shadow: 0 4px 14px rgba(var(--crm-primary-rgb), 0.35);
      }

      .crm-submit:hover:not(:disabled) {
        background: var(--crm-primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 8px 22px rgba(var(--crm-primary-rgb), 0.4);
      }

      .crm-submit:active:not(:disabled) {
        transform: translateY(0);
      }

      .crm-submit:disabled {
        opacity: 0.85;
        cursor: not-allowed;
        transform: none;
      }

      .crm-submit:focus-visible {
        outline: 2px solid var(--crm-text);
        outline-offset: 3px;
      }

      .crm-loading-spinner {
        display: inline-block;
        width: 18px;
        height: 18px;
        border: 2px solid rgba(255, 255, 255, 0.35);
        border-top-color: #fff;
        border-radius: 50%;
        animation: crmSpin 0.75s linear infinite;
        vertical-align: middle;
        margin-right: 0.5rem;
      }

      @keyframes crmSpin {
        to {
          transform: rotate(360deg);
        }
      }

      .crm-login-foot {
        margin-top: 1.75rem;
        text-align: center;
        font-size: 0.8125rem;
        color: var(--crm-text-muted);
      }

      .crm-login-foot a {
        color: var(--crm-primary);
        font-weight: 600;
        text-decoration: none;
      }

      .crm-login-foot a:hover {
        text-decoration: underline;
      }

      /* Hero column */
      .crm-login-hero {
        display: none;
        position: relative;
        flex-direction: column;
        justify-content: space-between;
        padding: clamp(2rem, 5vw, 3.5rem);
        overflow: hidden;
        background: linear-gradient(155deg, #0b1220 0%, #111827 42%, #1a0a14 100%);
      }

      @media (min-width: 1024px) {
        .crm-login-hero {
          display: flex;
        }
      }

      .crm-login-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
          radial-gradient(ellipse 90% 70% at 20% 0%, rgba(var(--crm-primary-rgb), 0.45), transparent 55%),
          radial-gradient(ellipse 60% 50% at 100% 80%, rgba(56, 189, 248, 0.15), transparent 50%);
        pointer-events: none;
      }

      .crm-login-hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.6;
        pointer-events: none;
      }

      .crm-hero-top {
        position: relative;
        z-index: 1;
      }

      .crm-hero-top a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
      }

      .crm-hero-top img {
        height: 28px;
      }

      .crm-hero-body {
        position: relative;
        z-index: 1;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 2rem 0;
      }

      .crm-hero-visual {
        max-width: 320px;
        margin: 0 auto 2rem;
        filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.35));
      }

      .crm-hero-visual img {
        width: 100%;
        height: auto;
        display: block;
      }

      .crm-hero-quote {
        font-size: clamp(1.5rem, 2.5vw, 1.875rem);
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: -0.02em;
        color: #fff;
        margin: 0 0 1.5rem;
        text-align: center;
        text-wrap: balance;
      }

      .crm-hero-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 0.75rem;
        max-width: 320px;
        margin-left: auto;
        margin-right: auto;
      }

      .crm-hero-list li {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.88);
      }

      .crm-hero-list li i {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        font-size: 1rem;
      }

      .crm-hero-bottom {
        position: relative;
        z-index: 1;
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.45);
        text-align: center;
      }

      #password-strength-container {
        margin-top: 0.5rem;
      }
    </style>
  </head>
  <body class="crm-login">
    <div class="crm-login-shell">
      <main class="crm-login-form-col" aria-labelledby="login-heading">
        <div class="crm-login-form-inner">
          <div class="crm-login-brand">
            <img src="assets/images/brand-logos/desktop-dark.png" class="logo-light" alt="LimoCRM" onerror="this.style.display='none'" />
            <img src="assets/images/brand-logos/desktop-white.png" class="logo-dark" alt="LimoCRM" onerror="this.style.display='none'" />
          </div>

          <p class="crm-login-eyebrow">Secure access</p>
          <h1 id="login-heading" class="crm-login-title">Welcome back</h1>
          <p class="crm-login-sub">
            Sign in to pick up where you left off—leads, fleet, agreements, and your team in one workspace.
          </p>

          <div class="crm-login-card">
            <form id="signinForm" novalidate>
              <div class="crm-field">
                <label class="crm-label" for="username">
                  Username <span class="req" aria-hidden="true">*</span>
                </label>
                <div class="crm-input-wrap">
                  <span class="crm-input-icon" aria-hidden="true"><i class="ri-user-3-line"></i></span>
                  <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="user_name"
                    autocomplete="username"
                    placeholder="e.g. jane.doe"
                    required
                  />
                </div>
              </div>

              <div class="crm-field">
                <label class="crm-label" for="signin-password">
                  Password <span class="req" aria-hidden="true">*</span>
                </label>
                <div class="crm-input-wrap">
                  <span class="crm-input-icon" aria-hidden="true"><i class="ri-lock-2-line"></i></span>
                  <input
                    type="password"
                    class="form-control create-password-input"
                    id="signin-password"
                    name="password1"
                    autocomplete="current-password"
                    placeholder="Your password"
                    required
                  />
                  <a
                    href="javascript:void(0);"
                    class="show-password-button"
                    onclick="createpassword('signin-password',this)"
                    id="button-addon2"
                    role="button"
                    tabindex="0"
                    aria-label="Show password"
                    onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}"
                    ><i class="ri-eye-off-line" aria-hidden="true"></i
                  ></a>
                </div>
                <div class="mt-2" id="password-strength-container" style="display: none">
                  <div class="progress h-1.5 w-full bg-gray-200 dark:bg-white/10 rounded-full overflow-hidden">
                    <div id="password-strength-bar" class="h-full transition-all duration-300 w-0 bg-red-500"></div>
                  </div>
                  <p id="password-strength-text" class="text-xs mt-1 text-gray-500 dark:text-gray-400"></p>
                </div>
              </div>

              <button type="submit" id="signinBtn" class="crm-submit">Sign in</button>
            </form>
          </div>

          <p class="crm-login-foot">
            Need an account or a password reset? Ask your LimoCRM administrator.
          </p>
        </div>
      </main>

      <aside class="crm-login-hero" aria-label="Product highlights">
        <div class="crm-hero-top">
          <a href="./index.php">
            <img src="assets/images/brand-logos/desktop-white.png" alt="LimoCRM home" />
          </a>
        </div>
        <div class="crm-hero-body">
          <div class="crm-hero-visual">
            <img src="assets/images/media/login-a.svg" alt="" role="presentation" />
          </div>
          <p class="crm-hero-quote">Run your limo business from one calm, connected hub.</p>
          <ul class="crm-hero-list">
            <li>
              <i class="ri-dashboard-3-line" aria-hidden="true"></i>
              <span>Pipeline and fleet visibility without tab overload</span>
            </li>
            <li>
              <i class="ri-shield-check-line" aria-hidden="true"></i>
              <span>Role-based access so the right people see the right work</span>
            </li>
            <li>
              <i class="ri-time-line" aria-hidden="true"></i>
              <span>Fewer handoffs—from lead to agreement to payment</span>
            </li>
          </ul>
        </div>
        <p class="crm-hero-bottom">© LimoCRM · Internal use</p>
      </aside>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/show-password.js"></script>

    <script>
      $(document).ready(function () {
        $("#signinForm").on("submit", function (e) {
          e.preventDefault();

          $(".form-control").removeClass("border-red-500");

          var btn = $("#signinBtn");
          var originalBtnHtml = btn.html();
          btn.prop("disabled", true).html('<span class="crm-loading-spinner"></span>Signing in…');

          var usernameInput = $("#username");
          var passwordInput = $("#signin-password");
          var username = usernameInput.val().trim();
          var password = passwordInput.val();
          var hasError = false;

          if (username === "") {
            usernameInput.addClass("border-red-500");
            hasError = true;
          }
          if (password === "") {
            passwordInput.addClass("border-red-500");
            hasError = true;
          }

          if (hasError) {
            btn.prop("disabled", false).html(originalBtnHtml);
            Swal.fire({
              icon: "warning",
              title: "Almost there",
              text: "Please enter both your username and password.",
              confirmButtonColor: "#cf1c82",
            });
            (username === "" ? usernameInput : passwordInput).trigger("focus");
            return;
          }

          var formData = new FormData(this);
          formData.append("action", "user_login");

          $.ajax({
            url: "config/login.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
              try {
                var res = typeof response === "string" ? JSON.parse(response) : response;
                if (res.status == "success" || res.success == true) {
                  Swal.fire({
                    icon: "success",
                    title: "You’re in",
                    text: "Redirecting to your dashboard…",
                    showConfirmButton: false,
                    timer: 1400,
                    timerProgressBar: true,
                  }).then(function () {
                    window.location.href = "./index.php";
                  });
                } else {
                  btn.prop("disabled", false).html(originalBtnHtml);
                  Swal.fire({
                    icon: "error",
                    title: "Couldn’t sign you in",
                    text: res.message || "Check your username and password and try again.",
                    confirmButtonColor: "#cf1c82",
                  });
                }
              } catch (err) {
                btn.prop("disabled", false).html(originalBtnHtml);
                Swal.fire({
                  icon: "error",
                  title: "Unexpected response",
                  text: "Please try again or contact support if this keeps happening.",
                  confirmButtonColor: "#cf1c82",
                });
              }
            },
            error: function () {
              btn.prop("disabled", false).html(originalBtnHtml);
              Swal.fire({
                icon: "error",
                title: "Connection issue",
                text: "We couldn’t reach the server. Check your connection and try again.",
                confirmButtonColor: "#cf1c82",
              });
            },
          });
        });
      });
    </script>
  </body>
</html>
