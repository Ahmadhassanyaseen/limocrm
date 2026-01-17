

<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>
<?php
$data['id'] = $_SESSION['user']['id'];
    $teamMembers = fetchAllTeamMembers($data);
    $roles = fetchRoles();

    // print_r($teamMembers);
   
?>
   <div class="main-content app-content">
      <div class="container-fluid">
        <!-- Page Header -->
        <div
          class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2"
        >
          <div>
            
            <h1 class="page-title font-medium text-lg mb-0">Team</h1>
          </div>
          <div class="btn-list">
            
           <button type="button" class="ti-btn ti-btn-sm bg-primary text-white btn-wave waves-light waves-effect waves-light" data-hs-overlay="#create-user"><i class="ri-add-line font-medium align-middle"></i> Add Team Member</button>
          </div>
        </div>
        <!-- Page Header Close -->

        <div
            class="hs-overlay ti-modal"
            id="create-user"
            tabindex="-1"
            aria-overlay="true"
            >
            <div class="hs-overlay-open:mt-7 ti-modal-box mt-0 ease-out">
                <div class="ti-modal-content">
                <div class="ti-modal-header">
                    <h6 class="ti-modal-title">Add Team Member</h6>
                    <button
                    type="button"
                    class="hs-dropdown-toggle ti-modal-close-btn"
                    data-hs-overlay="#create-user"
                    >
                    <span class="sr-only">Close</span>
                    <svg
                        class="w-3.5 h-3.5"
                        width="8"
                        height="8"
                        viewBox="0 0 8 8"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                        d="M0.258206 1.00652C0.351976 0.912791 0.479126 0.860131 0.611706 0.860131C0.744296 0.860131 0.871447 0.912791 0.965207 1.00652L3.61171 3.65302L6.25822 1.00652C6.30432 0.958771 6.35952 0.920671 6.42052 0.894471C6.48152 0.868271 6.54712 0.854471 6.61352 0.853901C6.67992 0.853321 6.74572 0.865971 6.80722 0.891111C6.86862 0.916251 6.92442 0.953381 6.97142 1.00032C7.01832 1.04727 7.05552 1.1031 7.08062 1.16454C7.10572 1.22599 7.11842 1.29183 7.11782 1.35822C7.11722 1.42461 7.10342 1.49022 7.07722 1.55122C7.05102 1.61222 7.01292 1.6674 6.96522 1.71352L4.31871 4.36002L6.96522 7.00648C7.05632 7.10078 7.10672 7.22708 7.10552 7.35818C7.10442 7.48928 7.05182 7.61468 6.95912 7.70738C6.86642 7.80018 6.74102 7.85268 6.60992 7.85388C6.47882 7.85498 6.35252 7.80458 6.25822 7.71348L3.61171 5.06702L0.965207 7.71348C0.870907 7.80458 0.744606 7.85498 0.613506 7.85388C0.482406 7.85268 0.357007 7.80018 0.264297 7.70738C0.171597 7.61468 0.119017 7.48928 0.117877 7.35818C0.116737 7.22708 0.167126 7.10078 0.258206 7.00648L2.90471 4.36002L0.258206 1.71352C0.164476 1.61976 0.111816 1.4926 0.111816 1.36002C0.111816 1.22744 0.164476 1.10028 0.258206 1.00652Z"
                        fill="currentColor"
                        ></path>
                    </svg>
                    </button>
                </div>
                <div class="ti-modal-body">
                    <div class="grid grid-cols-12 gap-x-6 gap-y-2">
                    <div class="xl:col-span-6 col-span-12">
                        <label for="first-name" class="form-label">First Name</label>
                        <input
                        type="text"
                        class="form-control"
                        id="first-name"
                        placeholder="First Name"
                        name="first_name"
                        />
                    </div>
                    <div class="xl:col-span-6 col-span-12">
                        <label for="last-name" class="form-label">Last Name</label>
                        <input
                        type="text"
                        class="form-control"
                        id="last-name"
                        placeholder="Last Name"
                        name="last_name"
                        />
                    </div>
                    <div class="xl:col-span-6 col-span-12">
                        <label for="email" class="form-label">Email</label>
                        <input
                        type="email"
                        class="form-control"
                        id="email"
                        placeholder="Email"
                        name="email"
                        />
                    </div>
                  
                   
                   
                    <div class="xl:col-span-6 col-span-12">
                        <label for="Status" class="form-label">Status</label>
                        <select
                        class="form-control"
                        id="Status"
                        placeholder="Status"
                        name="status"
                        >
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="xl:col-span-6 col-span-12">
                        <label for="role" class="form-label">Role</label>
                        <select
                        class="form-control"
                        id="role"
                        placeholder="Role"
                        name="role"
                        >
                            <option value="">Select Role</option>
                            <?php if(!empty($roles) && is_array($roles)): ?>
                                <?php foreach($roles as $role): ?>
                                    <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="xl:col-span-6 col-span-12">
                        <label for="user-name" class="form-label">User Name</label>
                        <input
                        type="text"
                        class="form-control"
                        id="user-name"
                        placeholder="User Name"
                        name="user_name"
                        />
                    </div>
                    <div class="xl:col-span-6 col-span-12">
                        <label for="password" class="form-label">Password</label>
                        <input
                        type="password"
                        class="form-control"
                        id="password"
                        placeholder="Password"
                        name="password"
                        />
                    </div>
                    
                    </div>
                </div>
                <div class="ti-modal-footer">
                    <button
                    type="button"
                    class="hs-dropdown-toggle ti-btn btn-wave ti-btn-light waves-effect waves-light"
                    data-hs-overlay="#create-user"
                    >
                    Cancel
                    </button>
                    <button type="button" class="ti-btn bg-primary text-white">
                    Add User
                    </button>
                </div>
                </div>
            </div>
        </div>
        
        <!-- Edit User Modal -->
        <div
            class="hs-overlay ti-modal"
            id="edit-user"
            tabindex="-1"
            aria-overlay="true"
            >
            <div class="hs-overlay-open:mt-7 ti-modal-box mt-0 ease-out">
                <div class="ti-modal-content">
                <div class="ti-modal-header">
                    <h6 class="ti-modal-title">Edit Team Member</h6>
                    <button
                    type="button"
                    class="hs-dropdown-toggle ti-modal-close-btn"
                    data-hs-overlay="#edit-user"
                    >
                    <span class="sr-only">Close</span>
                    <svg
                        class="w-3.5 h-3.5"
                        width="8"
                        height="8"
                        viewBox="0 0 8 8"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                        d="M0.258206 1.00652C0.351976 0.912791 0.479126 0.860131 0.611706 0.860131C0.744296 0.860131 0.871447 0.912791 0.965207 1.00652L3.61171 3.65302L6.25822 1.00652C6.30432 0.958771 6.35952 0.920671 6.42052 0.894471C6.48152 0.868271 6.54712 0.854471 6.61352 0.853901C6.67992 0.853321 6.74572 0.865971 6.80722 0.891111C6.86862 0.916251 6.92442 0.953381 6.97142 1.00032C7.01832 1.04727 7.05552 1.1031 7.08062 1.16454C7.10572 1.22599 7.11842 1.29183 7.11782 1.35822C7.11722 1.42461 7.10342 1.49022 7.07722 1.55122C7.05102 1.61222 7.01292 1.6674 6.96522 1.71352L4.31871 4.36002L6.96522 7.00648C7.05632 7.10078 7.10672 7.22708 7.10552 7.35818C7.10442 7.48928 7.05182 7.61468 6.95912 7.70738C6.86642 7.80018 6.74102 7.85268 6.60992 7.85388C6.47882 7.85498 6.35252 7.80458 6.25822 7.71348L3.61171 5.06702L0.965207 7.71348C0.870907 7.80458 0.744606 7.85498 0.613506 7.85388C0.482406 7.85268 0.357007 7.80018 0.264297 7.70738C0.171597 7.61468 0.119017 7.48928 0.117877 7.35818C0.116737 7.22708 0.167126 7.10078 0.258206 7.00648L2.90471 4.36002L0.258206 1.71352C0.164476 1.61976 0.111816 1.4926 0.111816 1.36002C0.111816 1.22744 0.164476 1.10028 0.258206 1.00652Z"
                        fill="currentColor"
                        ></path>
                    </svg>
                    </button>
                </div>
                <div class="ti-modal-body">
                    <div class="grid grid-cols-12 gap-x-6 gap-y-2">
                    <input type="hidden" id="edit-user-id" name="edit_user_id">
                    <div class="xl:col-span-6 col-span-12">
                        <label for="edit-first-name" class="form-label">First Name</label>
                        <input
                        type="text"
                        class="form-control"
                        id="edit-first-name"
                        placeholder="First Name"
                        name="first_name"
                        />
                    </div>
                    <div class="xl:col-span-6 col-span-12">
                        <label for="edit-last-name" class="form-label">Last Name</label>
                        <input
                        type="text"
                        class="form-control"
                        id="edit-last-name"
                        placeholder="Last Name"
                        name="last_name"
                        />
                    </div>
                    <div class="xl:col-span-6 col-span-12">
                        <label for="edit-email" class="form-label">Email</label>
                        <input
                        type="email"
                        class="form-control"
                        id="edit-email"
                        placeholder="Email"
                        name="email"
                        />
                    </div>
                  
                    <div class="xl:col-span-6 col-span-12">
                        <label for="edit-status" class="form-label">Status</label>
                        <select
                        class="form-control"
                        id="edit-status"
                        name="status"
                        >
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="xl:col-span-6 col-span-12">
                        <label for="edit-role" class="form-label">Role</label>
                        <select
                        class="form-control"
                        id="edit-role"
                        name="role"
                        >
                            <option value="">Select Role</option>
                            <?php if(!empty($roles) && is_array($roles)): ?>
                                <?php foreach($roles as $role): ?>
                                    <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="xl:col-span-6 col-span-12">
                        <label for="edit-user-name" class="form-label">User Name</label>
                        <input
                        type="text"
                        class="form-control"
                        id="edit-user-name"
                        placeholder="User Name"
                        name="user_name"
                        />
                    </div>
                    <div class="xl:col-span-6 col-span-12">
                        <label for="edit-password" class="form-label">Password (Leave empty to keep current)</label>
                        <input
                        type="password"
                        class="form-control"
                        id="edit-password"
                        placeholder="Password"
                        name="password"
                        />
                    </div>
                    
                    </div>
                </div>
                <div class="ti-modal-footer">
                    <button
                    type="button"
                    class="hs-dropdown-toggle ti-btn btn-wave ti-btn-light waves-effect waves-light"
                    data-hs-overlay="#edit-user"
                    >
                    Cancel
                    </button>
                    <button type="button" class="ti-btn bg-primary text-white" id="btn-save-user">
                    Save Changes
                    </button>
                </div>
                </div>
            </div>
        </div>
        <!-- Start:: row-1 -->
        <div class="grid grid-cols-12 gap-x-6">
          <div
            class="xl:col-span-3 lg:col-span-4 md:col-span-6 sm:col-span-6 col-span-12"
            >
            <div class="box team-member text-center">
              <div class="team-bg-shape primary"></div>
              <div class="box-body">
                 <div class="mb-2 text-right">
                   <p
                    class="mb-2 text-[11px] badge leading-none bg-green-500 font-medium"
                  >
                   Active
                  </p>
                </div>
                <div class="mb-3 leading-none flex gap-2 justify-center">
                  <span class="avatar avatar-xl avatar-rounded bg-primary">
                    <span class="text-white font-semibold text-4xl"><?php echo strtoupper($_SESSION['user']['user_name'][0]); ?></span>
                  </span>
                </div>
                <div class="">
                  <p
                    class="mb-2 text-[11px] badge leading-none bg-primary font-medium"
                  >
                   Admin
                  </p>
                  <h6 class="mb-3 font-semibold"><?php echo $_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']; ?></h6>
                  <p class="text-textmuted dark:text-textmuted/50 text-xs mb-4">
                    <?php echo $_SESSION['user']['email']; ?>
                  </p>
                  <div class="flex justify-center">
                    <a
                      aria-label="anchor"
                      href="javascript:void(0);"
                      class="ti-btn ti-btn-icon ti-btn-soft-primary2 btn-wave ti-btn-sm  waves-effect waves-light"
                      ><i class="ri-eye-line"></i
                    ></a>

                    <a
                      aria-label="anchor"
                      href="javascript:void(0);"
                      class="ti-btn ti-btn-icon ti-btn-soft-primary btn-wave ti-btn-sm ms-2 waves-effect waves-light edit-user-btn"
                      data-hs-overlay="#edit-user"
                      data-id="<?php echo $_SESSION['user']['id']; ?>"
                      data-first-name="<?php echo $_SESSION['user']['first_name']; ?>"
                      data-last-name="<?php echo $_SESSION['user']['last_name']; ?>"
                      data-email="<?php echo $_SESSION['user']['email']; ?>"
                      data-user-name="<?php echo $_SESSION['user']['user_name']; ?>"
                      data-role="Admin" 
                      data-status="Active" 
                      ><i class="ri-edit-line"></i
                    ></a>
                    <a
                      aria-label="anchor"
                      href="javascript:void(0);"
                      class="ti-btn ti-btn-icon ti-btn-soft-primary1 btn-wave ti-btn-sm ms-2 waves-effect waves-light delete-user-btn"
                      data-id="<?php echo $_SESSION['user']['id']; ?>"
                      ><i class="ri-delete-bin-line"></i
                    ></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
            <?php 
            foreach($teamMembers as $member){
                ?>
             <div
            class="xl:col-span-3 lg:col-span-4 md:col-span-6 sm:col-span-6 col-span-12"
            >
            <div class="box team-member text-center">
              <div class="team-bg-shape primary"></div>
              <div class="box-body">
                <div class="mb-2 text-right">
                   <p
                    class="mb-2 text-[11px] badge leading-none <?php echo $member['status'] == 'Active' ? 'bg-green-500' : 'bg-red-500'; ?> font-medium"
                  >
                   <?php echo $member['status']; ?>
                  </p>
                </div>
                <div class="mb-3 leading-none flex gap-2 justify-center">
                  <span class="avatar avatar-xl avatar-rounded bg-primary">
                    <span class="text-white font-semibold text-4xl"><?php echo strtoupper($member['user_name'][0]); ?></span>
                  </span>
                </div>
                <div class="">
                  <p
                    class="mb-2 text-[11px] badge leading-none bg-primary font-medium"
                  >
                  <?php if(!empty($roles) && is_array($roles)): ?>
                                <?php foreach($roles as $role): ?>
                                    <?php if($role['id'] == $member['role_c']): ?>
                                        <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                  </p>
                  <h6 class="mb-3 font-semibold"><?php echo $member['first_name'] . ' ' . $member['last_name']; ?></h6>
                  <p class="text-textmuted dark:text-textmuted/50 text-xs mb-4">
                    <?php echo $member['user_email_c']; ?>
                  </p>
                   <div class="flex justify-center">
                    

                    <a
                      aria-label="anchor"
                      href="javascript:void(0);"
                      class="ti-btn ti-btn-icon ti-btn-soft-primary btn-wave ti-btn-sm ms-2 waves-effect waves-light edit-user-btn"
                      data-hs-overlay="#edit-user"
                      data-id="<?php echo $member['id']; ?>"
                      data-first-name="<?php echo $member['first_name']; ?>"
                      data-last-name="<?php echo $member['last_name']; ?>"
                      data-email="<?php echo $member['user_email_c']; ?>"
                      data-user-name="<?php echo $member['user_name']; ?>"
                      data-role="<?php echo $member['role_c']; ?>"
                      data-status="<?php echo $member['status']; ?>"
                      ><i class="ri-edit-line"></i
                    ></a>
                    <a
                      aria-label="anchor"
                      href="javascript:void(0);"
                      class="ti-btn ti-btn-icon ti-btn-soft-primary1 btn-wave ti-btn-sm ms-2 waves-effect waves-light delete-user-btn"
                      data-id="<?php echo $member['id']; ?>"
                      ><i class="ri-delete-bin-line"></i
                    ></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
                <?php
            }
            ?>
          
        </div>
        <!-- End:: row-1 -->
      </div>
    </div>



    
      
    
<?php include_once "components/layout/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Handle "Add User" button click
        $('#create-user .ti-modal-footer .bg-primary').click(function() {
            // Get input values
            var firstName = $('#first-name').val().trim();
            var lastName = $('#last-name').val().trim();
            var email = $('#email').val().trim();
            var status = $('#Status').val();
            var role = $('#role').val();
            var userName = $('#user-name').val().trim();
            var password = $('#password').val();

            // Reset error styles
            $('#create-user .form-control').removeClass('border-red-500');
            
            var hasError = false;

            // Basic Empty Validation
            if (firstName === '') { $('#first-name').addClass('border-red-500'); hasError = true; }
            if (lastName === '') { $('#last-name').addClass('border-red-500'); hasError = true; }
            if (email === '') { $('#email').addClass('border-red-500'); hasError = true; }
            if (userName === '') { $('#user-name').addClass('border-red-500'); hasError = true; }
            if (password === '') { $('#password').addClass('border-red-500'); hasError = true; }
            if (role === '') { $('#role').addClass('border-red-500'); hasError = true; }
            if (status === '') { $('#status').addClass('border-red-500'); hasError = true; }
            if (hasError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'All fields are required!',
                });
                return;
            }

            // Email Regex Validation
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                $('#email').addClass('border-red-500');
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address!',
                });
                return;
            }

            // Password Length Validation
            if (password.length < 8) {
                $('#password').addClass('border-red-500');
                Swal.fire({
                    icon: 'error',
                    title: 'Weak Password',
                    text: 'Password must be at least 8 characters long!',
                });
                return;
            }

            // Prepare Data for Remote API
            var formData = new FormData();
            formData.append('action', 'create_user');
            formData.append('first_name', firstName);
            formData.append('last_name', lastName);
            formData.append('email', email);
            formData.append('user_name', userName);
            formData.append('role', role);
            formData.append('password1', password); 
            formData.append('status', status);
            formData.append('created_by', '<?php echo $_SESSION["user"]["id"]; ?>');

            // Show loading state
            var btn = $(this);
            var originalText = btn.text();
            btn.text('Adding...').prop('disabled', true);

            // Send AJAX
            $.ajax({
                url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    
                    var data = response;
                    if (typeof response === 'string') {
                        try {
                            data = JSON.parse(response);
                        } catch (e) {
                            console.error("Parsing error", e);
                        }
                    }

                    if (data.status === 'success' || data.success === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Team member added successfully.',
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to add user.',
                        });
                        btn.text(originalText).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Something went wrong. Please try again later.',
                    });
                    btn.text(originalText).prop('disabled', false);
                }
            });
        });

        // Handle "Edit User" Button Click (Populate Modal)
        $('.edit-user-btn').click(function() {
            var btn = $(this);
            $('#edit-user-id').val(btn.data('id'));
            $('#edit-first-name').val(btn.data('first-name'));
            $('#edit-last-name').val(btn.data('last-name'));
            $('#edit-email').val(btn.data('email'));
            $('#edit-user-name').val(btn.data('user-name'));
            $('#edit-status').val(btn.data('status'));
            $('#edit-role').val(btn.data('role'));
            $('#edit-password').val(''); // Clear password field
        });

         // Handle "Save Changes" Button Click
        $('#btn-save-user').click(function() {
            // Get input values
            var id = $('#edit-user-id').val();
            var firstName = $('#edit-first-name').val().trim();
            var lastName = $('#edit-last-name').val().trim();
            var email = $('#edit-email').val().trim();
            var status = $('#edit-status').val();
            var role = $('#edit-role').val();
            var userName = $('#edit-user-name').val().trim();
            var password = $('#edit-password').val();

             // Reset error styles
            $('#edit-user .form-control').removeClass('border-red-500');
            
            var hasError = false;

             // Basic Empty Validation
             if (firstName === '') { $('#edit-first-name').addClass('border-red-500'); hasError = true; }
             if (lastName === '') { $('#edit-last-name').addClass('border-red-500'); hasError = true; }
             if (email === '') { $('#edit-email').addClass('border-red-500'); hasError = true; }
             if (userName === '') { $('#edit-user-name').addClass('border-red-500'); hasError = true; }
             
             if (hasError) {
                 Swal.fire({
                     icon: 'error',
                     title: 'Oops...',
                     text: 'Please fill in all required fields!',
                 });
                 return;
             }

            // Email Regex Validation
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                $('#edit-email').addClass('border-red-500');
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address!',
                });
                return;
            }

             // Password Length Validation (Only if entered)
             if (password.length > 0 && password.length < 8) {
                $('#edit-password').addClass('border-red-500');
                Swal.fire({
                    icon: 'error',
                    title: 'Weak Password',
                    text: 'Password must be at least 8 characters long!',
                });
                return;
            }

            // Prepare Data for Remote API
            var formData = new FormData();
            formData.append('action', 'update_user');
            formData.append('id', id);
            formData.append('first_name', firstName);
            formData.append('last_name', lastName);
            formData.append('email', email);
            formData.append('user_name', userName);
            formData.append('role', role);
            formData.append('status', status);
            
            if(password.length > 0) {
                 formData.append('password1', password);
            }

             // Show loading state
             var btn = $(this);
             var originalText = btn.text();
             btn.text('Saving...').prop('disabled', true);

             // Send AJAX
            $.ajax({
                url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    
                    var data = response;
                    if (typeof response === 'string') {
                        try {
                            data = JSON.parse(response);
                        } catch (e) {
                            console.error("Parsing error", e);
                        }
                    }

                    if (data.status === 'success' || data.success === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Team member updated successfully.',
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to update user.',
                        });
                        btn.text(originalText).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Something went wrong. Please try again later.',
                    });
                    btn.text(originalText).prop('disabled', false);
                }
            });

        });


        // Handle "Delete User" Button Click
        $('.delete-user-btn').click(function() {
            var btn = $(this);
            var userId = btn.data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX
                     var formData = new FormData();
                    formData.append('action', 'delete_user');
                    formData.append('id', userId);

                    $.ajax({
                        url: 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                             var data = response;
                            if (typeof response === 'string') {
                                try {
                                    data = JSON.parse(response);
                                } catch (e) {
                                    console.error("Parsing error", e);
                                }
                            }

                            if (data.status === 'success' || data.success === true) {
                                Swal.fire(
                                    'Deleted!',
                                    'User has been deleted.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    data.message || 'Failed to delete user.',
                                    'error'
                                );
                            }
                        },
                        error: function() {
                           Swal.fire(
                                'Error!',
                                'Something went wrong.',
                                'error'
                            );
                        }
                    });
                }
            })
        });

    });
</script>
<style>
    .border-red-500 {
        border-color: red !important;
    }
</style>