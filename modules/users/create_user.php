<?php 
/************** START SECURITY CHECK ***********************/
// Define allowed roles - make sure these variables are defined somewhere in your application
$allowedRoles = array(
					$admin_role, 
					$manager_role
					); // You need to define these variables

// Check if role is allowed
if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], $allowedRoles)) {
    // User does not have access, redirect to home
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => "You do not have permission to view this page."
    ];
    echo '<script>window.location.href = "index.php";</script>';
    die();
}
/**************** END SECURITY CHECK ***********************/

?>
<style>
    @media screen and (max-width: 360px) {
        .row1{
            margin-left: -30px;
            margin-right: -30px;
            margin-top: -10px;
        }

    }
</style>
<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<div class="main-content app-content mt-0 h-100">
    <div class="side-app h-100">
        <div class="main-container container-fluid h-100 p-0">
            <!-- Page header with breadcrumb navigation -->
            <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
                <div style="margin-top: 25px;">
                    <ol class="breadcrumb float-sm-right mt-2">
                        <!-- Home breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="index.php" style="color: #fe5500"><i
                                    class="fas fa-home me-1"></i><?php echo lang("user_home"); ?></a>
                        </li>
                        <!-- Position breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="#" style="color: #fe5500"><?php echo lang(key: "user_users"); ?></a>
                        </li>
                        <!-- View position breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="#" style="color: #fe5500"><?php echo lang("user_create_user"); ?></a>
                        </li>
                    </ol>
                </div>
            </div>


            <!-- Display Messages -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Create Form -->
             <div class="row1">
            <div class="row h-100 m-0">
                <div class="col-12 p-0 h-100">
                    <div class="card rounded-4 h-100 d-flex flex-column">
                        <div class="card-header" style="border-bottom: 2px solid #FE5500;">
                            <h3 class="card-title" style="color: #FE5500;">
                                <i class="fas fa-user-plus me-2"></i><?php echo lang("user_create_new_user"); ?>
                            </h3>
                        </div>
                        <div class="card-body" style="background-color: white">
                            <form class="row g-3" action="index.php?route=modules/users/processuserform" method="POST">
                                <input type="hidden" value="createuser" name="formtype" />


                                <div class="col-md-6">
                                    <label class="form-label"><?php echo lang("form_first_name"); ?></label>
                                    <input type="text" class="form-control" name="first_name" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><?php echo lang("form_last_name"); ?></label>
                                    <input type="text" class="form-control" name="last_name" required>
                                </div>

                                <!-- Main User Info -->
                                <div class="col-md-6">
                                    <label class="form-label"><?php echo lang("user_username"); ?></label>
                                    <input type="text" class="form-control" name="user_name" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><?php echo lang("user_email"); ?></label>
                                    <input type="email" class="form-control" name="user_email" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><?php echo lang("user_phone_no"); ?></label>
                                    <input type="tel" class="form-control" name="user_phone" id="phoneNumber" placeholder="+1 (555) 123-4567 or +52 55 1234 5678">
                                    <div id="phoneError" class="text-danger mt-1" style="display: none; font-size: 0.875rem;"></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><?php echo lang("user_password"); ?></label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><?php echo lang("KIOSK ID"); ?></label>
                                    <input type="number" name="kioskID" class="form-control" required>
                                </div>

                                <!-- Role Selection -->
                                <div class="col-md-6">
                                    <label class="form-label"><?php echo lang("user_user_role"); ?></label>
                                    <select class="form-select" name="role_id" required>
                                        <option value=""><?php echo lang("user_select_role"); ?></option>
                                        <?php foreach (DB::query("SELECT id, name FROM roles") as $role): ?>
                                            <option value="<?= $role['id'] ?>">
                                                <?= $role['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Form Actions -->
                                <div class="col-12 mt-4">
                                    <button type="submit" name="btnSubmitClose" class="btn btn-orange"
                                        style="background-color:#fe5500; color: white;">
                                        <i class="fas fa-save me-2"></i><?php echo lang("user_create_user"); ?>
                                    </button>
                                    <a href="?route=modules/users/view_users" class="btn btn-secondary">
                                        <?php echo lang("user_cancel"); ?>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                                        </div>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
        $(document).ready(function () {

            // Phone number formatting and validation for international numbers
            document.getElementById('phoneNumber').addEventListener('input', function (e) {
                const input = e.target;
                let value = input.value;
                
                // Allow + at the beginning
                if (value.startsWith('+')) {
                    // For international numbers starting with +
                    const numbers = value.substring(1).replace(/\D/g, '');
                    if (numbers.length > 0) {
                        // Format international number with spaces for readability
                        let formatted = '+' + numbers;
                        if (numbers.length > 3) {
                            formatted = '+' + numbers.substring(0, 3) + ' ' + numbers.substring(3);
                        }
                        if (numbers.length > 6) {
                            formatted = '+' + numbers.substring(0, 3) + ' ' + numbers.substring(3, 6) + ' ' + numbers.substring(6);
                        }
                        if (numbers.length > 9) {
                            formatted = '+' + numbers.substring(0, 3) + ' ' + numbers.substring(3, 6) + ' ' + numbers.substring(6, 9) + ' ' + numbers.substring(9);
                        }
                        input.value = formatted;
                    }
                } else {
                    // For US numbers without + (backward compatibility)
                    const numbers = value.replace(/\D/g, '');
                    if (numbers.length > 0) {
                        let formatted = '';
                        // Format as (XXX) XXX-XXXX for US numbers
                        for (let i = 0; i < numbers.length && i < 10; i++) {
                            if (i === 0) formatted += '(';
                            if (i === 3) formatted += ') ';
                            if (i === 6) formatted += '-';
                            formatted += numbers[i];
                        }
                        input.value = formatted;
                    }
                }
                
                validatePhone();
            });

            function validatePhone() {
                const phoneInput = document.getElementById('phoneNumber');
                const errorDiv = document.getElementById('phoneError');
                let phoneNumber = phoneInput.value;
                
                // Remove all formatting characters except +
                phoneNumber = phoneNumber.replace(/[^\d+]/g, '');
                
                // Check if it's an international number (starts with +)
                if (phoneNumber.startsWith('+')) {
                    // International number validation - should have at least 7 digits after +
                    const digitsAfterPlus = phoneNumber.substring(1).replace(/\D/g, '');
                    if (digitsAfterPlus.length < 7) {
                        errorDiv.textContent = 'Please enter a valid international phone number (minimum 7 digits after country code)';
                        errorDiv.style.display = 'block';
                        return false;
                    }
                    if (digitsAfterPlus.length > 15) {
                        errorDiv.textContent = 'Phone number is too long (maximum 15 digits after country code)';
                        errorDiv.style.display = 'block';
                        return false;
                    }
                } else {
                    // US number validation (10 digits)
                    const digitsOnly = phoneNumber.replace(/\D/g, '');
                    if (digitsOnly.length !== 10) {
                        errorDiv.textContent = 'Please enter a valid 10-digit US phone number or international number starting with +';
                        errorDiv.style.display = 'block';
                        return false;
                    }
                }

                errorDiv.style.display = 'none';
                return true;
            }
        });
</script>