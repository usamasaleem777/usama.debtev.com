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



$user_id = isset($_GET['id']) ? $_GET['id'] : 0;

try {
    // Fetch user data
    $user = DB::queryFirstRow("SELECT * FROM users WHERE user_id = %i", $user_id);
    if (!$user) {
        die("User not found");
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $name = $_POST['name'];
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        $phone = isset($_POST['phone']) ? $_POST['phone'] : null;
        $kioskID = isset($_POST['kioskID']) ? $_POST['kioskID'] : null;
        $role_id = $_POST['role_id'];
        $password = !empty($_POST['password']) ? $_POST['password'] : null;
        $selected_job_ids = isset($_POST['job_ids']) ? $_POST['job_ids'] : array();

        DB::delete('user_jobs', 'user_id = %i', $user_id);

        // Check username uniqueness
        $existing = DB::queryFirstRow(
            "SELECT user_id FROM users 
            WHERE user_name = %s AND user_id != %i",
            $username,
            $user_id
        );
        if ($existing) {
            throw new Exception("Username already exists");
        }

        // Check email uniqueness
        if ($email) {
            $existing = DB::queryFirstRow(
                "SELECT user_id FROM users 
                WHERE email = %s AND user_id != %i",
                $email,
                $user_id
            );
            if ($existing) {
                throw new Exception("Email already exists");
            }
        }

        // Prepare update data
        $updateData = array(
            'user_name' => $username,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'kioskID' => $kioskID,
            'role_id' => $role_id,
        );

        // Only update password if provided
        if ($password) {
            $updateData['password'] = $password;
        }

        // Perform update
        DB::update('users', $updateData, "user_id = %i", $user_id);
        foreach ($selected_job_ids as $job_id) {
            DB::insert('user_jobs', array(
                'user_id' => $user_id,
                'job_id' => $job_id
            ));
        }

        $_SESSION['message'] = array(
            'type' => 'success',
            'text' => 'employee updated successfully!'
        );
        echo '<script>window.location.href = "index.php?route=modules/employee/employee";</script>';

        exit();
    }

} catch (Exception $e) {
    $error = $e->getMessage();
}

// Fetch all jobs
$all_jobs = DB::query("SELECT * FROM jobs");

// Fetch the jobs assigned to this user
$assigned_jobs = DB::query("SELECT job_id FROM user_jobs WHERE user_id = %i", $user_id);
$assigned_job_ids = array_column($assigned_jobs, 'job_id'); // Extract job_ids into a simple array
$roles = DB::query("SELECT * FROM roles");
?>

<style>
    /* Base Styles */
    body {
        font-size: 14px;
    }
    
    /* Form Styles */
    .form-control, .form-select {
        padding: 8px 12px;
        font-size: 14px;
        height: auto;
    }
    
    .form-label {
        font-size: 14px;
        margin-bottom: 4px;
    }
    
    /* Card Styles */
    .card {
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .card-header {
        padding: 12px 15px;
        background-color: white;
    }
    
    .card-title {
        font-size: 16px;
        font-weight: 600;
    }
    
    .card-body {
        padding: 15px;
    }
    
    /* Button Styles */
    .btn {
        padding: 8px 16px;
        font-size: 14px;
        border-radius: 4px;
    }
    
    /* Layout Adjustments */
    .main-container {
        padding: 10px;
    }
    
    .page-header {
        padding: 10px 0;
    }
    
    .breadcrumb {
        padding: 0.5rem;
        font-size: 12px;
    }
    
    .breadcrumb-item i {
        font-size: 12px;
    }
    
    /* Responsive Grid */
    .row.g-3 {
        margin-left: -8px;
        margin-right: -8px;
    }
    
    .row.g-3 > [class^="col-"] {
        padding-left: 8px;
        padding-right: 8px;
    }
    
    /* Mobile Specific Styles */
    @media (max-width: 360px) {
        body {
            font-size: 13px;
        }
        .row1{
            margin-left: -30px !important;
            margin-right: -30px !important;
            margin-top: -10px !important;
        }
        .card-header {
            padding: 10px;
        }
        
        .card-title {
            font-size: 15px;
        }
        
        .form-control, .form-select {
            padding: 6px 10px;
            font-size: 13px;
        }
        
        .form-label {
            font-size: 13px;
        }
        
        .btn {
            padding: 6px 12px;
            font-size: 13px;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .card-body {
            padding: 12px;
        }
        
        /* Stack form elements vertically on smallest screens */
        .col-md-6 {
            width: 100%;
        }
        
        /* Adjust breadcrumb */
        .breadcrumb {
            font-size: 11px;
            padding: 0.3rem;
        }
        
        /* Button container */
        .form-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <!-- Page header with breadcrumb navigation -->
            <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
                <div>
                    <ol class="breadcrumb float-sm-right mt-2">
                        <li class="breadcrumb-item">
                            <a href="index.php" style="color: #fe5500">
                                <i class="fas fa-home me-1"></i><?php echo lang("user_home"); ?>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#" style="color: #fe5500"><?php echo lang("admin_employee"); ?></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#" style="color: #fe5500"><?php echo lang("employee_edit_employee"); ?></a>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Display Messages -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Edit Form -->
             <div class="row1">
            <div class="card rounded-4">
                <div class="card-header" style="border-bottom: 2px solid #FE5500;">
                    <h3 class="card-title" style="color: #FE5500;">
                        <i class="fas fa-edit me-2"></i><?php echo lang("employee_edit_employee"); ?>
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <!-- Username -->
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo lang("user_username"); ?></label>
                                <input type="text" class="form-control" name="username"
                                    value="<?= htmlspecialchars($user['user_name']) ?>" required>
                            </div>

                            <!-- Full Name -->
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo lang("user_full_name"); ?></label>
                                <input type="text" class="form-control" name="name"
                                    value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>

                            <!-- Email -->
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo lang("user_email"); ?></label>
                                <input type="email" class="form-control" name="email"
                                    value="<?= htmlspecialchars(isset($user['email']) ? $user['email'] : '') ?>">
                            </div>

                            <!-- Phone -->
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo lang("user_phone"); ?></label>
                                <input type="text" class="form-control" name="phone"
                                    value="<?= htmlspecialchars(isset($user['phone']) ? $user['phone'] : '') ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label"><?php echo lang("user_Kiosk_ID"); ?></label>
                                <input type="text" class="form-control" name="kioskID"
                                    value="<?= htmlspecialchars(isset($user['kioskID']) ? $user['kioskID'] : '') ?>">
                            </div>
                            <!-- Role -->
                            <div class="col-12 col-md-6">
                                <label class="form-label">Role</label>
                                <select class="form-select" name="role_id" required>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id'] ?>" <?= $user['role_id'] == $role['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($role['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Password -->
                            <div class="col-12 col-md-6 password">
                                <label class="form-label"><?php echo lang("user_new_password"); ?></label>
                                <input type="password" class="form-control" name="password">
                            </div>

                            <!-- Form Actions -->
                            <div class="col-12 mt-4 form-actions">
                                <button type="submit" class="btn" style="background-color: #FE5500; color: white;">
                                    <i class="fas fa-save me-2"></i><?php echo lang("user_save_changes"); ?>
                                </button>
                                <a href="?route=modules/user/list" class="btn btn-secondary">
                                    <?php echo lang("user_cancel"); ?>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>