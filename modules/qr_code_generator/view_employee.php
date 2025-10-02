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
 
$user_id = isset($_GET['id']) ? $_GET['id'] : 0;

try {
    $user = DB::queryFirstRow(
        "SELECT * FROM users 
        WHERE user_id = %i",
        $user_id
    );

    if (!$user)
        die("User not found");

} catch (MeekroDBException $e) {
    die("Database error: " . $e->getMessage());
}

$role = DB::queryFirstRow("SELECT name FROM roles WHERE id = %i", $user['role_id']);
?>

<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
    .user-profile-header {
        background: linear-gradient(135deg, #FE5500 0%, #FF8C00 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 20px;
    }

    .user-avatar {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .detail-card {
        border-left: 4px solid #FE5500;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .detail-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eaeaea;
        font-weight: 600;
    }

    .detail-label {
        font-weight: 600;
        color: #6c757d;
    }

    .detail-value {
        font-weight: 500;
        color: #343a40;
    }

    .badge-custom {
        background-color: #FE5500;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 500;
    }

    .social-icon {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 50%;
        margin-right: 8px;
        color: #FE5500;
        transition: all 0.3s;
    }

    .social-icon:hover {
        background-color: #FE5500;
        color: white;
        transform: translateY(-2px);
    }

    .btn-outline-secondary {
        background-color: #FE5500;
        color: #f8f9fa;
    }

    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
        color: #FE5500;
    }

    .profile-card-container {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .profile-card {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .profile-card-body {
        flex: 1;
        overflow-y: auto;
    }

    /* Extra small devices (phones, 360px and down) */
    @media screen and (max-width: 576px) {

        /* Header adjustments */
        .page-header {
            flex-direction: column;
            align-items: flex-start !important;
            padding: 0.5rem !important;
        }

        .breadcrumb {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.7rem !important;
            margin-top: 0.5rem !important;
        }

        .breadcrumb-item i {
            font-size: 0.6rem !important;
            margin-right: 0.2rem !important;
        }

        .btn-outline-secondary {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem !important;
        }

        /* Profile card adjustments */
        .profile-card-container {
            padding: 0 !important;
        }

        .profile-card {
            margin-bottom: 1rem !important;
            max-width: 100% !important;
        }

        .user-profile-header {
            padding: 15px !important;
        }

        .user-avatar {
            width: 60px !important;
            height: 60px !important;
            border-width: 2px !important;
        }

        .user-profile-header h4 {
            font-size: 1rem !important;
            margin-bottom: 0.25rem !important;
        }

        .badge-custom {
            font-size: 0.65rem !important;
            padding: 0.25rem 0.5rem !important;
        }

        .social-icon {
            width: 28px !important;
            height: 28px !important;
            font-size: 0.7rem !important;
            margin: 0 0.25rem !important;
        }

        .profile-card-body {
            padding: 0.75rem !important;
            font-size: 0.8rem !important;
        }

        /* Detail cards adjustments */
        .detail-card {
            margin-bottom: 0.75rem !important;
        }

        .card-header {
            padding: 0.5rem !important;
            font-size: 0.85rem !important;
        }

        .card-body {
            padding: 0.75rem !important;
        }

        .detail-label,
        .detail-value {
            font-size: 0.75rem !important;
        }

        /* Make cards stack in one column */
        .col-md-6 {
            width: 100% !important;
        }

        /* Adjust spacing between cards */
        .row>[class^="col-"] {
            padding-right: 0.25rem !important;
            padding-left: 0.25rem !important;
        }

        /* Make sure all content fits */
        .main-container {
            padding: 0.5rem !important;
        }

        /* Adjust list items */
        .list-unstyled li {
            margin-bottom: 0.25rem !important;
        }

        /* Reduce heading sizes */
        h6 {
            font-size: 0.9rem !important;
        }
    }
</style>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <!-- Page Header -->
            <div class="page-header pt-2 d-flex justify-content-between align-items-center">
                <a href="?route=modules/employee/employee" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> <?php echo lang("viewuser_back_to_list"); ?>
                </a>
                <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
                    <div style="margin-top: 25px;">
                        <ol class="breadcrumb float-sm-right mt-2">
                            <li class="breadcrumb-item">
                                <a href="index.php" style="color: #fe5500"><i
                                        class="fas fa-home me-1"></i><?php echo lang("user_home"); ?></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="?route=modules/user/view_user"
                                    style="color: #fe5500"><?php echo lang("admin_employee"); ?></a>
                            </li>
                            <li class="breadcrumb-item active" style="color: #fe5500">
                                <?php echo lang("employee_employee_details"); ?>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- User Profile Section -->
            <div class="row">
                <div class="col-lg-4 col-md-5 profile-card-container">
                    <!-- Profile Card -->
                    <div class="card profile-card">
                        <div class="user-profile-header text-center py-2">
                            <?php if (!empty($user['picture'])): ?>
                                <img src="<?= htmlspecialchars($user['picture']) ?>" class="user-avatar rounded-circle mb-1"
                                    alt="User Avatar">
                            <?php else: ?>
                                <div
                                    class="user-avatar rounded-circle mb-1 bg-white d-flex align-items-center justify-content-center mx-auto">
                                    <i class="fas fa-user text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <h4 class="mb-1"><?= htmlspecialchars($user['name']) ?></h4>
                            <p class="mb-1">
                                <span class="badge badge-custom">
                                    <?php if (isset($role['name']) && $role['name'] !== ''): ?>
                                        <?= htmlspecialchars($role['name']) ?>
                                    <?php else: ?>
                                        Unknown
                                    <?php endif; ?>
                                </span>
                            </p>
                        </div>
                        <div class="card-body profile-card-body p-2">
                            <div class="d-flex justify-content-center mb-2">
                                <a href="mailto:<?= isset($user['email']) ? $user['email'] : '#' ?>"
                                    class="social-icon"><i class="fas fa-envelope"></i></a>
                                <a href="tel:<?= isset($user['phone']) ? $user['phone'] : '' ?>" class="social-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </a>
                            </div>
                            <hr class="my-1">
                            <h6 class="mb-1"><?php echo lang("viewuser_contact_info"); ?></h6>
                            <ul class="list-unstyled">
                                <li class="mb-1"><i class="fas fa-envelope me-2" style="color: #FE5500;"></i>
                                    <?= isset($user['email']) ? $user['email'] : 'N/A' ?></li>
                                <li class="mb-1"><i class="fas fa-phone me-2" style="color: #FE5500;"></i>
                                    <?= isset($user['phone']) ? preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $user['phone']) : 'N/A' ?>
                                </li>
                                <li class="mb-1"><i class="fas fa-calendar-alt me-2" style="color: #FE5500;"></i>
                                    <?= date('M d, Y', strtotime($user['created_at'])) ?></li>
                                <li><i class="fas fa-id-card me-2" style="color: #FE5500;"></i> ID:
                                    <?= $user['user_id'] ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-md-7">
                    <!-- Main Details Card -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-card card mb-3">
                                        <div class="card-header" style="color: #FE5500;">
                                            <?php echo lang("viewuser_basic_info"); ?>
                                        </div>
                                        <div class="card-body">
                                            <dl class="row">
                                                <dt class="col-sm-5 detail-label">
                                                    <?php echo lang("viewuser_username"); ?>
                                                </dt>
                                                <dd class="col-sm-7 detail-value">
                                                    <?= htmlspecialchars($user['user_name']) ?>
                                                </dd>

                                                <dt class="col-sm-5 detail-label">
                                                    <?php echo lang("viewuser_fulname"); ?>
                                                </dt>
                                                <dd class="col-sm-7 detail-value"><?= htmlspecialchars($user['name']) ?>
                                                </dd>

                                                <dt class="col-sm-5 detail-label"><?php echo lang("viewuser_role"); ?>
                                                </dt>
                                                <dd class="col-sm-7 detail-value">
                                                    <?php if (isset($role['name']) && $role['name'] !== ''): ?>
                                                        <?= htmlspecialchars($role['name']) ?>
                                                    <?php else: ?>
                                                        Unknown
                                                    <?php endif; ?>
                                                </dd>
                                              
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card card mb-3">
                                        <div class="card-header" style="color: #FE5500;">
                                            <?php echo lang("viewuser_system_info"); ?>
                                        </div>
                                        <div class="card-body">
                                            <dl class="row">
                                                <dt class="col-sm-5 detail-label"><?php echo lang("user_user_id"); ?>
                                                </dt>
                                                <dd class="col-sm-7 detail-value"><?= $user['user_id'] ?></dd>
                                                <dt class="col-sm-5 detail-label">
                                                    <?php echo lang("user_Kiosk_ID"); ?>
                                                </dt>
                                                <dd class="col-sm-7 detail-value">
                                                    <?= htmlspecialchars($user['kioskID']) ?>
                                                </dd>
                                                <dt class="col-sm-5 detail-label">
                                                    <?php echo lang("viewuser_create_at"); ?>
                                                </dt>
                                                <dd class="col-sm-7 detail-value">
                                                    <?= date('M d, Y H:i', strtotime($user['created_at'])) ?>
                                                </dd>

                                                <dt class="col-sm-5 detail-label"><?php echo lang("viewuser_status"); ?>
                                                </dt>
                                                <dd class="col-sm-7 detail-value">
                                                    <span class="badge bg-success">Active</span>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="detail-card card">
                                        <div class="card-header" style="color: #FE5500;">
                                            <?php echo lang("viewuser_additional_info"); ?>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <dl class="row">
                                                        <dt class="col-sm-5 detail-label">
                                                            <?php echo lang("viewuser_Email"); ?>
                                                        </dt>
                                                        <dd class="col-sm-7 detail-value">
                                                            <?= isset($user['email']) ? $user['email'] : 'N/A' ?>
                                                        </dd>
                                                    </dl>
                                                </div>
                                                <div class="col-md-6">
                                                    <dl class="row">
                                                        <dt class="col-sm-5 detail-label">
                                                            <?php echo lang("viewuser_phone"); ?>
                                                        </dt>
                                                        <dd class="col-sm-7 detail-value">
                                                            <?= isset($user['phone']) ? preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $user['phone']) : 'N/A' ?>
                                                        </dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>