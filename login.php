<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Meta Data -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Sign in | LimoCRM</title>
   
    <link
      rel="icon"
      href="assets/images/brand-logos/favicon.ico"
      type="image/x-icon"
    />
    <!-- Main Theme Js -->
    <!-- <script src="assets/js/authentication-main.js"></script> -->
    <!-- Style Css -->
    <link href="assets/css/styles.css" rel="stylesheet" />

    <meta http-equiv="imagetoolbar" content="no" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css"
      integrity="sha512-XcIsjKMcuVe0Ucj/xgIXQnytNwBttJbNjltBV18IOnru2lDPe9KRRyvCXw6Y5H415vbBLRm8+q6fmLUU7DfO6Q=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <style>
      :root{
        --crm-primary: #cf1c82;
        --crm-primary-rgb: 207, 28, 130;
      }

      /* Validation */
      .border-red-500{
        border-color: #ef4444 !important;
      }

      /* Page shell */
      body.crm-login{
        background:
          radial-gradient(1200px 600px at 15% 10%, rgba(var(--crm-primary-rgb), .10), transparent 55%),
          radial-gradient(900px 500px at 90% 35%, rgba(14, 165, 233, .10), transparent 55%),
          #ffffff;
        min-height: 100vh;
      }

      .dark body.crm-login{
        background:
          radial-gradient(1200px 600px at 15% 10%, rgba(var(--crm-primary-rgb), .20), transparent 55%),
          radial-gradient(900px 500px at 90% 35%, rgba(14, 165, 233, .12), transparent 55%),
          #0b1220;
      }

      /* Card + controls */
      .crm-auth-card{
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
      }
      .dark .crm-auth-card{
        box-shadow: 0 14px 36px rgba(0, 0, 0, .35);
      }

      .crm-auth-card .form-control{
        height: 42px;
        border-radius: 12px;
      }
      .crm-auth-card .form-control:focus{
        border-color: var(--crm-primary) !important;
        box-shadow: 0 0 0 3px rgba(var(--crm-primary-rgb), .18) !important;
      }

      .crm-primary-btn{
        background: var(--crm-primary) !important;
        border-color: var(--crm-primary) !important;
      }
      .crm-primary-btn:hover{
        filter: brightness(0.95);
      }

      .crm-loading-spinner{
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,0.45);
        border-top-color: #fff;
        border-radius: 9999px;
        animation: crmSpin 0.8s linear infinite;
        vertical-align: middle;
      }
      @keyframes crmSpin { to { transform: rotate(360deg); } }
    </style>
  </head>
  <body class="crm-login">
    
    <div
      class="grid grid-cols-12 authentication authentication-cover-main mx-0 min-h-screen"
      >
      <div class="xxl:col-span-6 xl:col-span-7 col-span-12">
        <div class="grid grid-cols-12 justify-center items-center h-full py-10">
          <div
            class="xxl:col-span-3 xl:col-span-2 lg:col-span-3 md:col-span-3 sm:col-span-2 col-span-12"
          ></div>
          <div
            class="xxl:col-span-6 xl:col-span-8 lg:col-span-6 md:col-span-6 sm:col-span-8 col-span-12 px-3"
          >
            <div
              class="box my-auto border border-defaultborder dark:border-defaultborder/10 crm-auth-card"
            >
              <div class="box-body p-[2.5rem]">
                <div class="flex items-center justify-center mb-5">
                  <img src="assets/images/brand-logos/desktop-dark.png" class="h-7 dark:hidden" alt="LimoCRM" onerror="this.classList.add('hidden')" />
                  <img src="assets/images/brand-logos/desktop-white.png" class="h-7 hidden dark:block" alt="LimoCRM" onerror="this.classList.add('hidden')" />
                </div>

                <div class="text-center mb-6">
                  <p class="text-lg font-semibold mb-1 text-defaulttextcolor dark:text-defaulttextcolor/90">Welcome back</p>
                  <p class="text-xs text-textmuted dark:text-textmuted/50 mb-0">Sign in to manage leads, quotes, and fleet operations.</p>
                </div>
                
                
                <form id="signinForm">
                  <div class="grid grid-cols-12 gap-y-4 gap-x-2">
                   
                    <div class="xl:col-span-12 col-span-12">
                      <label for="username" class="form-label text-defaulttextcolor"
                        >User Name<sup class="text-xs text-danger">*</sup></label
                      >
                      <input
                        type="text"
                        class="form-control"
                        id="username"
                        name="user_name"
                        placeholder="Enter your username"
                      />
                    </div>
                   
                    <div class="xl:col-span-12 col-span-12">
                      <label for="signin-password" class="form-label text-defaulttextcolor"
                        >Password<sup class="text-xs text-danger">*</sup></label
                      >
                      <div class="relative">
                        <input
                          type="password"
                          class="form-control create-password-input"
                          id="signin-password"
                          name="password1"
                          placeholder="Enter your password"
                        />
                        <a
                          aria-label="anchor"
                          href="javascript:void(0);"
                          class="show-password-button text-textmuted dark:text-textmuted/50"
                          onclick="createpassword('signin-password',this)"
                          id="button-addon2"
                          ><i class="ri-eye-off-line align-middle"></i
                        ></a>
                      </div>
                       <div class="mt-2" id="password-strength-container" style="display:none;">
                          <div class="progress h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                              <div id="password-strength-bar" class="h-full transition-all duration-300 w-0 bg-red-500"></div>
                          </div>
                          <p id="password-strength-text" class="text-xs mt-1 text-gray-500"></p>
                      </div>
                    </div>
                   
                  </div>
                  <div class="grid mt-4">
                    <button type="submit" id="signinBtn" class="ti-btn ti-btn-primary crm-primary-btn">Sign In</button>
                  </div>
                </form>

                <!-- <div class="flex items-center justify-between mt-4 text-xs">
                  <span class="text-textmuted dark:text-textmuted/50">Need access? Contact your admin.</span>
                  <a href="./signup.php" class="text-primary font-medium">Create account</a>
                </div>
                <div class="text-center">
                  <p class="text-textmuted dark:text-textmuted/50 mt-3 mb-0">
                    Don't have an account?
                    <a href="./signup.php" class="text-primary"
                      >Sign Up</a
                    >
                  </p>
                </div>
                <div class="btn-list text-center mt-3">
                  <button
                    aria-label="button"
                    type="button"
                    class="ti-btn ti-btn-icon btn-wave ti-btn-soft-primary"
                  >
                    <i
                      class="ri-facebook-line leading-none align-center text-[17px]"
                    ></i>
                  </button>
                  <button
                    aria-label="button"
                    type="button"
                    class="ti-btn ti-btn-icon btn-wave ti-btn-soft-primary1"
                  >
                    <i
                      class="ri-twitter-x-line leading-none align-center text-[17px]"
                    ></i>
                  </button>
                  <button
                    aria-label="button"
                    type="button"
                    class="ti-btn ti-btn-icon btn-wave ti-btn-soft-primary2"
                  >
                    <i
                      class="ri-instagram-line leading-none align-center text-[17px]"
                    ></i>
                  </button>
                </div> -->
              </div>
            </div>
          </div>
          <div
            class="xxl:col-span-3 xl:col-span-2 lg:col-span-3 md:col-span-3 sm:col-span-2 col-span-12"
          ></div>
        </div>
      </div>
      <div
        class="xxl:col-span-6 xl:col-span-5 lg:col-span-12 xl:block hidden px-0"
      >
        <div class="authentication-cover  relative" style="background: radial-gradient(900px 600px at 20% 20%, rgba(255,255,255,.16), transparent 60%), linear-gradient(135deg, #0b1220 0%, #111827 40%, rgba(var(--crm-primary-rgb), .55) 120%);">
          <div class="authentication-cover-logo">
            <a aria-label="anchor" href="index.html">
              <img
                src="assets/images/brand-logos/desktop-white.png"
                alt=""
                class="authentication-brand desktop-white"
              />
            </a>
          </div>
          <div class="aunthentication-cover-content absolute inset-0">
            <img
              src="assets/images/media/login-a.svg"
              alt="Login"
              class="w-full object-contain"
            />
            
            <h3 class="text-white text-4xl font-bold text-center ">Run your limo business in one place</h3>
            <div class="absolute inset-0 bg-gradient-to-br from-black/35 via-black/20 to-transparent"></div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/show-password.js"></script>
  
    <script>
      $(document).ready(function() {
        $('#signinForm').on('submit', function(e) {
          e.preventDefault();

          // Reset borders
          $('.form-control').removeClass('border-red-500');

          // Button loading state
          var btn = $('#signinBtn');
          var originalBtnHtml = btn.html();
          btn.prop('disabled', true).html('<span class="crm-loading-spinner me-2"></span> Signing in...');

          // Get values
        
          var usernameInput = $('#username');
          var passwordInput = $('#signin-password');

          var username = usernameInput.val().trim();
          var password = passwordInput.val();

          var hasError = false;

          
          if (username === '') {
             usernameInput.addClass('border-red-500');
             hasError = true;
          }
          
           if (password === '') {
             passwordInput.addClass('border-red-500');
             hasError = true;
          }
           

          if (hasError) {
            btn.prop('disabled', false).html(originalBtnHtml);
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'All fields are required!',
            });
            return;
          }

        

         
          // Prepare FormData
          var formData = new FormData(this);
          formData.append('action', 'user_login');

          // Send AJAX request
          $.ajax({
            url: 'config/login.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
               // Assuming the server returns a JSON object or just a success status
               // You might need to adjust this depending on the actual server response format
               // For now, let's assume any success callback means we try to interpret it
               // or just show success if no specific error flag is returned.
               // Since the user didn't specify the response format, I'll log it and show success.
               console.log(response);
               
               // If response is a JSON string, parse it
               try {
                   var res = typeof response === 'string' ? JSON.parse(response) : response;
                    if(res.status == 'success' || res.success == true){
                        Swal.fire({
                            icon: 'success',
                            title: 'Login!',
                            text: 'User has been logged in successfully.',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = './index.php';
                            }
                        });
                    }else{
                         btn.prop('disabled', false).html(originalBtnHtml);
                         Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message || 'Login failed. Please try again.',
                        });
                    }
               } catch (e) {
                   btn.prop('disabled', false).html(originalBtnHtml);
                   // Fallback if not JSON
                   Swal.fire({
                        icon: 'error',
                        title: 'Submitted',
                        text: 'Login request sent.',
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = './login.php';
                            }
                    });
               }
            },
            error: function(xhr, status, error) {
              console.error(error);
              btn.prop('disabled', false).html(originalBtnHtml);
              Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Something went wrong. Please try again later.',
              });
            }
          });
        });
      });
    </script>
  </body>
</html>
