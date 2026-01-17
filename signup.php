<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Meta Data -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Xintra - TailwindCss Premium Admin &amp; Dashboard Template</title>
    <meta
      name="Description"
      content="Tailwind Responsive Admin Web Dashboard HTML5 Template"
    />
    <meta name="Author" content="Spruko Technologies Private Limited" />
    <meta
      name="keywords"
      content="tailwind template,tailwind dashboard,tailwind,tailwind admin template,dashboard,tailwind css templates,html dashboard template,tailwind dashboard template,dashboard tailwind,admin,html css templates,html dashboard,html css javascript templates,dashboard tailwind template,tailwind css dashboard"
    />
    <!-- Favicon -->
    <link
      rel="icon"
      href="assets/images/brand-logos/favicon.ico"
      type="image/x-icon"
    />
    <!-- Main Theme Js -->
    <script src="assets/js/authentication-main.js"></script>
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
      .border-red-500{
        border-color: red !important;
      }
    </style>
  </head>
  <body class="bg-white" cz-shortcut-listen="true">
    
    <div
      class="grid grid-cols-12 authentication authentication-cover-main mx-0"
      >
      <div class="xxl:col-span-6 xl:col-span-7 col-span-12">
        <div class="grid grid-cols-12 justify-center items-center h-full">
          <div
            class="xxl:col-span-3 xl:col-span-2 lg:col-span-3 md:col-span-3 sm:col-span-2 col-span-12"
          ></div>
          <div
            class="xxl:col-span-6 xl:col-span-8 lg:col-span-6 md:col-span-6 sm:col-span-8 col-span-12 px-3"
          >
            <div
              class="box my-auto border border-defaultborder dark:border-defaultborder/10"
            >
              <div class="box-body p-[3rem]">
                <p class="h5 mb-6 text-center">Sign Up</p>
                
                
                <form id="signupForm">
                  <div class="grid grid-cols-12 gap-y-4 gap-x-2">
                     <div class="xl:col-span-6 col-span-12">
                      <label for="first_name" class="form-label text-defaulttextcolor"
                        >First Name<sup class="text-xs text-danger">*</sup></label
                      >
                      <input
                        type="text"
                        class="form-control"
                        id="first_name"
                        name="first_name"
                        placeholder="first name"
                      />
                    </div>
                    <div class="xl:col-span-6 col-span-12">
                      <label for="last_name" class="form-label text-defaulttextcolor"
                        >Last Name<sup class="text-xs text-danger">*</sup></label
                      >
                      <input
                        type="text"
                        class="form-control"
                        id="last_name"
                        name="last_name"
                        placeholder="last name"
                      />
                    </div>
                    <div class="xl:col-span-12 col-span-12">
                      <label for="username" class="form-label text-defaulttextcolor"
                        >User Name<sup class="text-xs text-danger">*</sup></label
                      >
                      <input
                        type="text"
                        class="form-control"
                        id="username"
                        name="user_name"
                        placeholder="user name"
                      />
                    </div>
                    <div class="xl:col-span-12 col-span-12">
                      <label for="email" class="form-label text-defaulttextcolor"
                        >Email<sup class="text-xs text-danger">*</sup></label
                      >
                      <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        placeholder="email"
                      />
                    </div>
                    <div class="xl:col-span-12 col-span-12">
                      <label for="signup-password" class="form-label text-defaulttextcolor"
                        >Password<sup class="text-xs text-danger">*</sup></label
                      >
                      <div class="relative">
                        <input
                          type="password"
                          class="form-control create-password-input"
                          id="signup-password"
                          name="password1"
                          placeholder="password"
                        />
                        <a
                          aria-label="anchor"
                          href="javascript:void(0);"
                          class="show-password-button text-textmuted dark:text-textmuted/50"
                          onclick="createpassword('signup-password',this)"
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
                    <div class="xl:col-span-12 col-span-12">
                      <label
                        for="signup-confirmpassword"
                        class="form-label text-defaulttextcolor"
                        >Confirm Password<sup class="text-xs text-danger">*</sup></label
                      >
                      <div class="relative">
                        <input
                          type="password"
                          class="form-control create-password-input"
                          id="signup-confirmpassword"
                          name="password_confirm"
                          placeholder="confirm password"
                        />
                        <a
                          aria-label="anchor"
                          href="javascript:void(0);"
                          class="show-password-button text-textmuted dark:text-textmuted/50"
                          onclick="createpassword('signup-confirmpassword',this)"
                          id="button-addon21"
                          ><i class="ri-eye-off-line align-middle"></i
                        ></a>
                      </div>
                     
                    </div>
                  </div>
                  <div class="grid mt-4">
                    <button type="submit"  class="ti-btn ti-btn-primary">Sign Up</button>
                  </div>
                </form>
                <div class="text-center">
                  <p class="text-textmuted dark:text-textmuted/50 mt-3 mb-0">
                    Already have an account?
                    <a href="./login.php" class="text-primary"
                      >Sign In</a
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
                </div>
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
        <div class="authentication-cover overflow-hidden">
          <div class="authentication-cover-logo">
            <a aria-label="anchor" href="index.html">
              <img
                src="assets/images/brand-logos/desktop-white.png"
                alt=""
                class="authentication-brand desktop-white"
              />
            </a>
          </div>
          <div
            class="aunthentication-cover-content flex items-center justify-center"
          >
            <div>
              <h3 class="text-white mb-1 font-medium">Welcome!</h3>
              <h6 class="text-white mb-3 font-medium">Sign Up to Your Account</h6>
              <p class="text-white mb-1 op-6">
                Welcome to the Admin Dashboard. Please log in to securely manage
                your administrative tools and oversee platform activities. Your
                credentials ensure system integrity and functionality.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/show-password.js"></script>
    <script>
        document.getElementById('signup-password').addEventListener('input', function() {
            var password = this.value;
            var strengthBar = document.getElementById('password-strength-bar');
            var strengthText = document.getElementById('password-strength-text');
            var container = document.getElementById('password-strength-container');
            
            if(password.length > 0){
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }

            var strength = 0;
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]/)) strength += 1;
            if (password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 1;

            var width = (strength / 5) * 100;
            var color = 'bg-red-500';
            var text = 'Very Weak';

            switch(strength) {
                case 1: color = 'bg-red-500'; text = 'Very Weak'; break;
                case 2: color = 'bg-orange-500'; text = 'Weak'; break;
                case 3: color = 'bg-yellow-500'; text = 'Medium'; break;
                case 4: color = 'bg-blue-500'; text = 'Strong'; break;
                case 5: color = 'bg-green-500'; text = 'Very Strong'; break;
            }
            
            if (strength === 0 && password.length > 0) {
                 width = 10; // Show a little bit if typed something
            }

            strengthBar.className = `h-full transition-all duration-300 ${color}`;
            strengthBar.style.width = width + '%';
            strengthText.innerText = text;
        });
    </script>
    <script>
      $(document).ready(function() {
        $('#signupForm').on('submit', function(e) {
          e.preventDefault();

          // Reset borders
          $('.form-control').removeClass('border-red-500');

          // Get values
          var firstNameInput = $('#first_name');
          var lastNameInput = $('#last_name');
          var usernameInput = $('#username');
          var emailInput = $('#email');
          var passwordInput = $('#signup-password');
          var confirmPasswordInput = $('#signup-confirmpassword');

          var firstName = firstNameInput.val().trim();
          var lastName = lastNameInput.val().trim();
          var username = usernameInput.val().trim();
          var email = emailInput.val().trim();
          var password = passwordInput.val();
          var confirmPassword = confirmPasswordInput.val();

          var hasError = false;

          // Basic Validation
          if (firstName === '') {
             firstNameInput.addClass('border-red-500');
             hasError = true;
          }
          if (lastName === '') {
             lastNameInput.addClass('border-red-500');
             hasError = true;
          }
          if (username === '') {
             usernameInput.addClass('border-red-500');
             hasError = true;
          }
          if (email === '') {
             emailInput.addClass('border-red-500');
             hasError = true;
          }
           if (password === '') {
             passwordInput.addClass('border-red-500');
             hasError = true;
          }
           if (confirmPassword === '') {
             confirmPasswordInput.addClass('border-red-500');
             hasError = true;
          }

          if (hasError) {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'All fields are required!',
            });
            return;
          }

          // Email Regex
          var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(email)) {
             emailInput.addClass('border-red-500');
            Swal.fire({
              icon: 'error',
              title: 'Invalid Email',
              text: 'Please enter a valid email address!',
            });
            return;
          }

          // Password Validation
          if (password.length < 8) {
             passwordInput.addClass('border-red-500');
            Swal.fire({
              icon: 'error',
              title: 'Weak Password',
              text: 'Password must be at least 8 characters long!',
            });
            return;
          }

          if (password !== confirmPassword) {
             confirmPasswordInput.addClass('border-red-500');
             passwordInput.addClass('border-red-500');
            Swal.fire({
              icon: 'error',
              title: 'Mismatch',
              text: 'Passwords do not match!',
            });
            return;
          }

          // Prepare FormData
          var formData = new FormData(this);
          formData.append('action', 'create_user');

          // Send AJAX request
          $.ajax({
            url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
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
                            title: 'Registered!',
                            text: 'User has been registered successfully.',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = './login.php';
                            }
                        });
                    }else{
                         Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message || 'Registration failed. Please try again.',
                        });
                    }
               } catch (e) {
                   // Fallback if not JSON
                   Swal.fire({
                        icon: 'success',
                        title: 'Submitted',
                        text: 'Registration request sent.',
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = './login.php';
                            }
                    });
               }
            },
            error: function(xhr, status, error) {
              console.error(error);
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
