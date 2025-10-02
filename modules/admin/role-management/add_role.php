<?php
/************** START SECURITY CHECK ***********************/
// Define allowed roles - make sure these variables are defined somewhere in your application
$allowedRoles = array(
					$admin_role 
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

 
// Initialize variables for edit mode
$editMode = false;          // Flag to check if we're in edit mode
$editRoleId = '';           // Stores the role ID being edited
$editRoleTitle = '';        // Stores the role title being edited

// Check if we're editing an existing role (role_id parameter exists)
if (isset($_GET['role_id']) && !empty($_GET['role_id'])) {
    $editMode = true;       // Set edit mode to true
    $editRoleId = $_GET['role_id'];  // Get the role ID from URL

    // Fetch existing role data from database
    try {
        $roleData = DB::queryFirstRow("SELECT * FROM roles WHERE id = %i", $editRoleId);
        if ($roleData) {
            $editRoleTitle = $roleData['name'];  // Get role title if found
        } else {
            // If role not found, show alert and redirect
            echo "<script>
                alert('Role not found');
                window.location.href = 'index.php?route=modules/admin/role-management/view_role';
            </script>";
            exit();
        }
    } catch (Exception $e) {
        // If database error occurs, show alert and redirect
        echo "<script>
            alert('Error fetching role data');
            window.location.href = 'index.php?route=modules/admin/role-management/view_role';
        </script>";
        exit();
    }
}
?>
<style>
    /* Default button styles */
    .responsive-role-button {
        width: 40%; /* Default width for larger screens */
    }
    
    /* Adjust button size for small screens */
    @media (max-width: 360px) {
        .btn {
            width: 100px;
            margin-left: -20px;
            font-size: 12px;
        }
        .row1{
            margin-left: -35px;
            margin-right: -35px;
        }
    }
</style>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Include SweetAlert for beautiful alerts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- Main content container -->
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid pt-4">
            <!-- Page header with breadcrumb navigation -->
            <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
                <div>
                    <ol class="breadcrumb float-sm-right mt-2">
                        <!-- Home breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="index.php" style="color: #fe5500">
                                <i class="fas fa-home me-1"></i><?php echo lang("role_home"); ?>
                            </a>
                        </li>
                        <!-- Manage Role breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="index.php?route=modules/admin/role-management/add"
                                style="color: #fe5500"><?php echo lang("role_manage_role"); ?>
                            </a>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Main form row -->
             <div class="row1">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card ">
                        <div class="card-body p-4">
                            <!-- Form header with dynamic title -->
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <h5 class="mb-0 fw-bold" id="formTitle" style="color: #FE5500;">
                                        <?= $editMode ? lang("role_edit") : lang("role_add_role") ?>
                                    </h5>
                                </div>
                            </div>

                            <!-- Display error/success messages if they exist -->
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                                <?php unset($_SESSION['error']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>

                            <!-- Role Form -->
                            <form class="row" action="index.php?route=modules/admin/role-management/processRoleForm"
                                method="POST">
                                <div class="col-12">
                                    <!-- Hidden field for role ID (used in edit mode) -->
                                    <input type="hidden" id="role_id" name="role_id"
                                        value="<?= $editMode ? $editRoleId : '' ?>" />
                                    
                                    <!-- Role Title Input -->
                                    <label for="role_title" class="form-label">
                                        <?php echo lang("role_role_title"); ?>
                                    </label>
                                    <input type="text" class="form-control" name="role_title" id="role_title" required
                                        minlength="3" maxlength="50" pattern="[A-Za-z ]+"
                                        title="Only letters and spaces are allowed, min 3 and max 50 characters"
                                        value="<?= $editMode ? $editRoleTitle : '' ?>" />
                                </div>
                                
                                <!-- Form Submit Button -->
                                <div class="row my-3">
                                    <div class="col-4 ms-auto text-end">
                                        <button type="submit" id="btnSubmitRole" name="btnSubmitRole"
                                            class="btn btn-success "
                                            style="background: linear-gradient(45deg, #FE5505, #FF8E53); color: white; border: none; ">
                                            <span id="submitButtonText">
                                                <?= $editMode ? lang("role_update_role") : lang("role_add_role") ?>
                                            </span>
                                        </button>
                                    </div>
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

<!-- JavaScript for form handling -->
<script>
    $(document).ready(function () {
        // Check URL parameters to see if we're in edit mode
        const urlParams = new URLSearchParams(window.location.search);
        const isEdit = urlParams.has('role_id') && urlParams.has('role_title');

        if (isEdit) {
            // If in edit mode, populate form fields from URL parameters
            const roleId = urlParams.get('role_id');
            const roleTitle = decodeURIComponent(urlParams.get('role_title'));

            // Set form values
            $('#role_id').val(roleId);
            $('#role_title').val(roleTitle);
            
            // Update UI text for edit mode
            $('#formTitle').text("<?= lang('role_edit') ?>");
            $('#submitButtonText').text("<?= lang('role_update_role') ?>");

            // Focus and select the role title field for easy editing
            $('#role_title').focus().select();
        }
    });
</script>