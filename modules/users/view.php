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



$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    // Fetch user data
    $user = DB::queryFirstRow("SELECT * FROM users WHERE user_id = %i", $user_id);
    if (!$user) {
        echo "<div class='alert alert-danger'>User not found</div>";
        exit;
    }

    // Fetch role
    $role = DB::queryFirstRow("SELECT name FROM roles WHERE id = %i", $user['role_id']);

    // Fetch applicant data if exists
    $applicant = DB::queryFirstRow("SELECT first_name, last_name FROM applicants WHERE user_id = %i", $user_id);
    
    // If applicant data exists, override the user's first/last name
    if ($applicant) {
        $user['first_name'] = $applicant['first_name'];
        $user['last_name'] = $applicant['last_name'];
    }

    $user_email = $user['email'] ?? '';
    $contract_row = DB::queryFirstRow("SELECT * FROM craft_contracting WHERE email = %s", $user_email);

    // Initialize all data variables
    $contract_id = null;
    $quick_book_row = null;
    $eligibility_data = null;
    $eligibility_data1 = null;
    $mvr_data = null;
    $w4_data = null;
    $signature_data = null;
    $certification_data = null;
    $employee_data = null;

    if ($contract_row) {
        $contract_id = $contract_row['id'];

        $quick_book_row = DB::queryFirstRow("SELECT * FROM quick_book WHERE id = %i", $contract_id);
        $eligibility_data = DB::queryFirstRow("SELECT * FROM employment_eligibility_verification WHERE id = %i", $contract_id);
        $eligibility_data1 = DB::queryFirstRow("SELECT * FROM employment_eligibility_verification1 WHERE id = %i", $contract_id);
        $mvr_data = DB::queryFirstRow("SELECT * FROM mvr_released WHERE id = %i", $contract_id);
        $w4_data = DB::queryFirstRow("SELECT * FROM w4_form WHERE id = %i", $contract_id);
        $signature_data = DB::queryFirstRow("SELECT * FROM application_signatures WHERE user_id = %i", $user_id);

        // Correct applicant_id fetch for certification
        $certification_data = DB::queryFirstRow("SELECT * FROM certification_files WHERE applicant_id = %i", $contract_id);

        $employee_data = DB::queryFirstRow("SELECT * FROM employment_data WHERE id = %i", $contract_id);
    }

    // Set flags
    $contract_exists = !empty($contract_row);
    $eligibility_exists = !empty($eligibility_data);
    $eligibility1_exists = !empty($eligibility_data1);
    $mvr_exists = !empty($mvr_data);
    $w4_exists = !empty($w4_data);
    $signature_exists = !empty($signature_data);
    $certification_exists = !empty($certification_data);
    $employee_exists = !empty($employee_data);
    $quick_book_exists = !empty($quick_book_row);
} catch (MeekroDBException $e) {
    echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}

?>
<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
    :root {
        --primary-color: #FE5500;
        --primary-light: #FF8C00;
        --secondary-color: #6c757d;
        --dark-color: #343a40;
        --light-color: #f8f9fa;
        --success-color: #28a745;
        --border-radius: 10px;
        --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f5f7fa;
        color: #444;
    }

    .custom-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        padding-top: 60px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.7);
    }

    .custom-modal-content {
        background-color: #fff;
        margin: auto;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 700px;
        position: relative;
        text-align: center;
    }

    .custom-modal-close {
        position: absolute;
        top: 8px;
        right: 16px;
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .custom-modal-close:hover {
        color: #000;
    }

    .main-content {
        padding: 20px;
    }

    .user-profile-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        padding: 25px;
        position: relative;
        overflow: hidden;
    }

    .user-profile-header::before {
        content: "";
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
        transform: rotate(30deg);
    }

    .user-avatar {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
    }

    .user-avatar:hover {
        transform: scale(1.05);
    }

    .badge-custom {
        background-color: white;
        color: var(--primary-color);
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .social-icon {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        margin: 0 5px;
        color: white;
        transition: var(--transition);
    }

    .social-icon:hover {
        background-color: white;
        color: var(--primary-color);
        transform: translateY(-3px);
    }

    .profile-card {
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        border: none;
        transition: var(--transition);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .profile-card-body {
        flex: 1;
        padding: 20px;
        background-color: white;
    }

    .detail-card {
        border-radius: var(--border-radius);
        border: none;
        box-shadow: var(--box-shadow);
        margin-bottom: 20px;
    }

    .detail-card .card-header {
        border-bottom: none;
        font-weight: 600;
        color: var(--primary-color);
        border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
        padding: 12px 20px;
    }

    .detail-label {
        font-weight: 600;
        color: var(--secondary-color);
        font-size: 0.85rem;
    }

    .detail-value {
        font-weight: 500;
        color: var(--dark-color);
    }

    .data-card {
        border-radius: var(--border-radius);
        overflow: hidden;
        border: none;
        margin-bottom: 20px;
    }

    .data-card .card-header {
        color: black;
        font-weight: 500;
        padding: 15px 20px;
        border-bottom: none;
    }

    .data-card .card-body {
        padding: 20px;
    }

    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .data-table tr td {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .data-label {
        font-weight: 600;
        color: var(--secondary-color);
        width: 45%;
        padding-right: 15px;
    }

    .data-value {
        color: var(--dark-color);
        word-break: break-word;
    }

    .section-title {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 8px;
    }

    .section-title::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(to right, var(--primary-color), var(--primary-light));
        border-radius: 3px;
    }

    .signature-container {
        margin-top: 15px;
        border: 1px dashed #ddd;
        border-radius: var(--border-radius);
        padding: 20px;
        text-align: center;
        background-color: #f9f9f9;
    }

    .signature-image {
        max-width: 100%;
        max-height: 100px;
        margin-bottom: 10px;
        background: transparent;
    }

    .signature-label {
        font-weight: 600;
        color: var(--secondary-color);
        margin-bottom: 10px;
        display: block;
    }

    .btn-outline-primary {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-primary:hover {
        background-color: #e04b00;
        border-color: #e04b00;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .status-active {
        background-color: rgba(40, 167, 69, 0.2);
        color: #28a745;
    }

    .breadcrumb {
        background-color: transparent;
        padding: 0.5rem 1rem;
    }

    .breadcrumb-item a {
        color: var(--primary-color);
        text-decoration: none;
        transition: var(--transition);
    }

    .breadcrumb-item a:hover {
        color: #e04b00;
        text-decoration: underline;
    }

    .breadcrumb-item.active {
        color: var(--secondary-color);
    }

    .page-header {
        padding: 15px 20px;
        margin-bottom: 20px;
    }

    .tab-content {
        background-color: white;
        border-radius: 0 0 var(--border-radius) var(--border-radius);
        padding: 20px;
        box-shadow: var(--box-shadow);
    }

    .nav-tabs {
        border-bottom: none;
    }

    .nav-tabs .nav-link {
        border: none;
        color: var(--secondary-color);
        font-weight: 500;
        padding: 12px 20px;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
    }

    .nav-tabs .nav-link.active {
        background-color: white;
        color: var(--primary-color);
        font-weight: 600;
        border-bottom: 3px solid var(--primary-color);
    }

    .nav-tabs .nav-link:hover:not(.active) {
        color: var(--primary-color);
        background-color: rgba(254, 85, 0, 0.1);
    }

    /* Floating action button */
    .fab {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 5px 20px rgba(254, 85, 0, 0.3);
        z-index: 100;
        transition: var(--transition);
    }

    .fab:hover {
        transform: scale(1.1) translateY(-5px);
        color: white;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .profile-card-container {
            margin-bottom: 20px;
        }
    }

    @media (max-width: 768px) {
        .user-avatar {
            width: 80px;
            height: 80px;
        }

        .data-card {
            margin-bottom: 15px;
        }

        .detail-card .card-header,
        .data-card .card-header {
            padding: 10px 15px;
        }

        .data-table tr td {
            padding: 8px 10px;
        }
    }

    @media (max-width: 576px) {
        .user-avatar {
            width: 70px;
            height: 70px;
            border-width: 3px;
        }

        .user-profile-header {
            padding: 15px;
        }

        .badge-custom {
            font-size: 0.7rem;
            padding: 4px 8px;
        }

        .social-icon {
            width: 30px;
            height: 30px;
            font-size: 0.9rem;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start !important;
            padding: 10px !important;
        }

        .breadcrumb {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.8rem !important;
            margin-top: 0.5rem !important;
        }

        .section-title {
            font-size: 1.1rem;
        }

        .data-label {
            width: 50%;
        }
    }
</style>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="d-flex align-items-center justify-content-end mt-2 mb-3">
                <div style="margin-top: 10px !important;">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.php"><i class="fas fa-home me-1"></i><?php echo lang("user_home"); ?></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="?route=modules/user/view_user"><?php echo lang("user_users"); ?></a>
                        </li>
                        <li class="breadcrumb-item active">
                            <?php echo lang("viewuser_user_details"); ?>
                        </li>
                    </ol>
                </div>
            </div>
            <!-- Page Header -->
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <a href="?route=modules/users/view_users" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> <?php echo lang("viewuser_back_to_list"); ?>
                    </a>
                </div>

                <div>
                    <?php if ($contract_exists): ?>
                        <a href="pdfs/pdf_data.php?id=<?php echo $contract_row['id']; ?>" class="btn btn-danger" title="View PDF">
                            <i class="fas fa-file-pdf me-2"></i> View PDF
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled title="No Packet Filled">
                            <i class="fas fa-ban me-2"></i> No Packet Filled
                        </button>
                    <?php endif; ?>
                </div>
            </div>



            <!-- User Profile Section -->
            <div class="row">
                <!-- Left Profile Column -->
                <div class="col-lg-4 col-md-5">
                    <div class="card profile-card">
                        <div class="user-profile-header text-center py-3">
                            <?php if (!empty($user['picture'])): ?>
                                <img src="<?= htmlspecialchars($user['picture']) ?>" class="user-avatar rounded-circle mb-2"
                                    alt="User Avatar">
                            <?php else: ?>
                                <div class="user-avatar rounded-circle mb-2 bg-white d-flex align-items-center justify-content-center mx-auto">
                                    <i class="fas fa-user text-muted" style="font-size: 3rem;"></i>
                                </div>
                            <?php endif; ?>
                            <h4 class="mb-2"><?= htmlspecialchars($user['name']) ?></h4>
                            <p class="mb-2">
                                <span class="badge badge-custom">
                                    <?php if (isset($role['name']) && $role['name'] !== ''): ?>
                                        <?= htmlspecialchars($role['name']) ?>
                                    <?php else: ?>
                                        Unknown Role
                                    <?php endif; ?>
                                </span>
                            </p>
                            <div class="d-flex justify-content-center mb-1">
                                <a href="mailto:<?= isset($user['email']) ? $user['email'] : '#' ?>"
                                    class="social-icon"><i class="fas fa-envelope"></i></a>
                                <a href="tel:<?= isset($user['phone']) ? $user['phone'] : '' ?>" class="social-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </a>
                                <a href="#" class="social-icon"><i class="fas fa-comment-dots"></i></a>
                            </div>
                        </div>

                        <div class="profile-card-body">
                            <h5 class="section-title"><?php echo lang("viewuser_contact_info"); ?></h5>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope me-3" style="color: var(--primary-color); width: 20px;"></i>
                                        <span><?= isset($user['email']) ? $user['email'] : 'N/A' ?></span>
                                    </div>
                                </li>
                                <li class="mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone me-3" style="color: var(--primary-color); width: 20px;"></i>
                                        <span><?= isset($user['phone']) ? preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $user['phone']) : 'N/A' ?></span>
                                    </div>
                                </li>
                                <li class="mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt me-3" style="color: var(--primary-color); width: 20px;"></i>
                                        <span>Joined <?= date('M d, Y', strtotime($user['created_at'])) ?></span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-id-card me-3" style="color: var(--primary-color); width: 20px;"></i>
                                        <span>ID: <?= $user['user_id'] ?></span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right Content Column -->
                <div class="col-lg-8 col-md-7">
                    <!-- Main Details Tabs -->
                    <div class="card">
                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab" aria-controls="basic" aria-selected="true">
                                    <i class="fas fa-user-circle me-2"></i> Basic Info
                                </button>
                            </li>
                            <?php if ($contract_exists): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="contract-tab" data-bs-toggle="tab" data-bs-target="#contract" type="button" role="tab" aria-controls="contract" aria-selected="false">
                                        <i class="fas fa-file-contract me-2"></i> Contract
                                    </button>
                                </li>
                            <?php endif; ?>
                            <?php if ($eligibility_exists || $eligibility1_exists): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="eligibility-tab" data-bs-toggle="tab" data-bs-target="#eligibility" type="button" role="tab" aria-controls="eligibility" aria-selected="false">
                                        <i class="fas fa-passport me-2"></i> Eligibility
                                    </button>
                                </li>
                            <?php endif; ?>
                            <?php if ($quick_book_exists || $w4_exists): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab" aria-controls="payment" aria-selected="false">
                                        <i class="fas fa-money-bill-wave me-2"></i> Payment
                                    </button>
                                </li>
                            <?php endif; ?>
                            <?php if ($mvr_exists || $certification_exists): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="files-tab" data-bs-toggle="tab" data-bs-target="#files" type="button" role="tab" aria-controls="files" aria-selected="false">
                                        <i class="fas fa-file me-2"></i>Files
                                    </button>
                                </li>
                            <?php endif; ?>
                        </ul>

                        <div class="tab-content" id="profileTabsContent">
                            <!-- Basic Info Tab -->
                            <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
    <div class="row">
        <div class="col-md-6">
            <div class="detail-card card mb-4">
                <div class="card-header">
                    <i class="fas fa-id-card me-2"></i> Account Details
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5 detail-label">Username</dt>
                        <dd class="col-sm-7 detail-value"><?= htmlspecialchars($user['user_name']) ?></dd>

                        <!-- <dt class="col-sm-5 detail-label">Full Name</dt>
                        <dd class="col-sm-7 detail-value"><?= htmlspecialchars($user['name']) ?></dd> -->

                        <dt class="col-sm-5 detail-label">First Name</dt>
                        <dd class="col-sm-7 detail-value">
                            <?= !empty($user['first_name']) ? htmlspecialchars($user['first_name']) : 'N/A' ?>
                        </dd>

                        <dt class="col-sm-5 detail-label">Last Name</dt>
                        <dd class="col-sm-7 detail-value">
                            <?= !empty($user['last_name']) ? htmlspecialchars($user['last_name']) : 'N/A' ?>
                        </dd>

                        <dt class="col-sm-5 detail-label">Role</dt>
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
            <div class="detail-card card mb-4">
                <div class="card-header">
                    <i class="fas fa-calendar-alt me-2"></i> System Information
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5 detail-label">User ID</dt>
                        <dd class="col-sm-7 detail-value"><?= $user['user_id'] ?></dd>

                        <dt class="col-sm-5 detail-label">Kiosk ID</dt>
                        <dd class="col-sm-7 detail-value"><?= htmlspecialchars($user['kioskID']) ?></dd>

                        <dt class="col-sm-5 detail-label">Created At</dt>
                        <dd class="col-sm-7 detail-value">
                            <?= date('M d, Y H:i', strtotime($user['created_at'])) ?>
                        </dd>

                        <dt class="col-sm-5 detail-label">Status</dt>
                        <dd class="col-sm-7 detail-value">
                            <span class="badge bg-success">Active</span>
                        </dd>
                        <dt class="col-sm-5 detail-label">Last Login</dt>
                        <dd class="col-sm-7 detail-value">
                            <?= !empty($user['last_login']) ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never' ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-card card mb-4">
        <div class="card-header">
            <i class="fas fa-address-book me-2"></i> Contact Information
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-5 detail-label">Email</dt>
                        <dd class="col-sm-7 detail-value">
                            <?= isset($user['email']) ? $user['email'] : 'N/A' ?>
                        </dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-5 detail-label">Phone</dt>
                        <dd class="col-sm-7 detail-value">
                            <?= isset($user['phone']) ? preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $user['phone']) : 'N/A' ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

                            <?php if ($contract_exists): ?>
                                <!-- Contract Tab -->
                                <div class="tab-pane fade" id="contract" role="tabpanel" aria-labelledby="contract-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="data-card  mb-4">
                                                <div class="card-header">
                                                    <i class="fas fa-user me-2"></i> Personal Information
                                                </div>
                                                <div class="card-body">
                                                    <table class="data-table">
                                                        <tr>
                                                            <td class="data-label">First Name</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['first_name'] ?? 'N/A') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="data-label">Last Name</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['last_name'] ?? 'N/A') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="data-label">Date of Birth</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['dob'] ?? 'N/A') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="data-label">Phone Number</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['phone_number'] ?? 'N/A') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="data-label">Email</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['email'] ?? 'N/A') ?></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="data-card mb-4">
                                                <div class="card-header">
                                                    <i class="fas fa-map-marker-alt me-2"></i> Address Information
                                                </div>
                                                <div class="card-body">
                                                    <table class="data-table">
                                                        <tr>
                                                            <td class="data-label">Street Address</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['street_address'] ?? 'N/A') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="data-label">City</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['city'] ?? 'N/A') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="data-label">State</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['state'] ?? 'N/A') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="data-label">Zip Code</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['zip_code'] ?? 'N/A') ?></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="data-card mb-4">
                                                <div class="card-header">
                                                    <i class="fas fa-address-book me-2"></i> Emergency Contact 1
                                                </div>
                                                <div class="card-body">
                                                    <table class="data-table">
                                                        <tr>
                                                            <td class="data-label">Contact Name</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['contact_name1'] ?? 'N/A') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="data-label">Contact Address</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['contact_address1'] ?? 'N/A') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="data-label">Contact Number</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['contact_phone1'] ?? 'N/A') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="data-label">Relationship</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['relationship1'] ?? 'N/A') ?></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="data-card mb-4">
                                                <div class="card-header">
                                                    <i class="fas fa-address-book me-2"></i> Emergency Contact 2
                                                </div>
                                                <div class="card-body">
                                                    <table class="data-table">
                                                        <tr>
                                                            <td class="data-label">Contact Name</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['contact_name2'] ?? 'N/A') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="data-label">Contact Address</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['contact_address2'] ?? 'N/A') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="data-label">Contact Number</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['contact_phone2'] ?? 'N/A') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="data-label">Relationship</td>
                                                            <td class="data-value"><?= htmlspecialchars($contract_row['relationship2'] ?? 'N/A') ?></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ($signature_exists): ?>
                                        <div class="data-card">
                                            <div class="card-header">
                                                <i class="fas fa-signature me-2"></i> Signature
                                            </div>
                                            <div class="card-body">
                                                <div class="signature-container">
                                                    <div class="signature-label">Employee Signature</div>
                                                    <?php if (!empty($signature_data['signature']) && strpos($signature_data['signature'], 'data:image/png') === 0): ?>
                                                        <img src="<?php echo htmlspecialchars($signature_data['signature']); ?>"
                                                            class="signature-image"
                                                            alt="Employee Signature">
                                                    <?php else: ?>
                                                        <div class="text-muted">No signature available</div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($eligibility_exists || $eligibility1_exists): ?>
                                <!-- Eligibility Tab -->
                                <div class="tab-pane fade" id="eligibility" role="tabpanel" aria-labelledby="eligibility-tab">
                                    <?php if ($eligibility_exists): ?>
                                        <div class="data-card mb-4">
                                            <div class="card-header">
                                                <i class="fas fa-id-card me-2"></i> Employment Eligibility
                                            </div>
                                            <div class="card-body">
                                                <table class="data-table">
                                                    <tr>
                                                        <td class="data-label">Citizenship Status</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['citizen_immigration_status'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">SSN</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['ssn'] ?? 'N/A') ?></td>
                                                    </tr>
                                                </table>

                                                <h6 class="mt-4 mb-3" style="color: var(--primary-color);">Document 1</h6>
                                                <table class="data-table">
                                                    <tr>
                                                        <td class="data-label">Document Title</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['document_title'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Issuing Authority</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['issuing_authority'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Document Number</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['document_number'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Expiration Date</td>
                                                        <td class="data-value"><?= !empty($eligibility_data['expiration_date']) ? date('M d, Y', strtotime($eligibility_data['expiration_date'])) : 'N/A' ?></td>
                                                    </tr>
                                                </table>

                                                <h6 class="mt-4 mb-3" style="color: var(--primary-color);">Document 2</h6>
                                                <table class="data-table">
                                                    <tr>
                                                        <td class="data-label">Document Title</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['document_title_1'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Issuing Authority</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['issuing_authority_1'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Document Number</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['document_number_1'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Expiration Date</td>
                                                        <td class="data-value"><?= !empty($eligibility_data['expiration_date_1']) ? date('M d, Y', strtotime($eligibility_data['expiration_date_1'])) : 'N/A' ?></td>
                                                    </tr>
                                                </table>
                                                <h6 class="mt-4 mb-3" style="color: var(--primary-color);">Document 3</h6>
                                                <table class="data-table">
                                                    <tr>
                                                        <td class="data-label">Document Title</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['document_title_2'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Issuing Authority</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['issuing_authority_2'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Document Number</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['document_number_2'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Expiration Date</td>
                                                        <td class="data-value"><?= !empty($eligibility_data['expiration_date_2']) ? date('M d, Y', strtotime($eligibility_data['expiration_date_2'])) : 'N/A' ?></td>
                                                    </tr>
                                                </table>
                                                <h6 class="mt-4 mb-3" style="color: var(--primary-color);">Reverification and Rehires</h6>
                                                <table class="data-table">
                                                    <tr>
                                                        <td class="data-label">First Name</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['first_name_1'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Last Name</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['last_name_1'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Middle Initial</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['middle_initial'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Rehire Date</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['rehire_date'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Marital Status</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data['marital_status'] ?? 'N/A') ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($eligibility1_exists): ?>
                                        <div class="data-card ">
                                            <div class="card-header">
                                                <i class="fas fa-passport me-2"></i> Additional Eligibility
                                            </div>
                                            <div class="card-body">
                                                <table class="data-table">
                                                    <tr>
                                                        <td class="data-label">Citizenship Status</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data1['citizenship_status'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Alien Number</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data1['registration_number'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Form I-94 Number</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data1['allen_registration_number'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Passport Number</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data1['passport_number'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Country of Issuance</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data1['country_of_issuance'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Expiration Date</td>
                                                        <td class="data-value"><?= htmlspecialchars($eligibility_data1['expiration_date'] ?? 'N/A') ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($quick_book_exists || $w4_exists): ?>
                                <!-- Payment Tab -->
                                <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                                    <?php if ($quick_book_exists): ?>
                                        <div class="data-card">
                                            <div class="card-header">
                                                <i class="fas fa-university me-2"></i> Direct Deposit Information
                                            </div>
                                            <div class="card-body">
                                                <table class="data-table">
                                                    <tr>
                                                        <td class="data-label">Bank Name</td>
                                                        <td class="data-value"><?= htmlspecialchars($quick_book_row['bank_name'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Account Type</td>
                                                        <td class="data-value"><?= htmlspecialchars($quick_book_row['account_type'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Account Number</td>
                                                        <td class="data-value"><?= htmlspecialchars($quick_book_row['account_number'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Routing Number</td>
                                                        <td class="data-value"><?= htmlspecialchars($quick_book_row['aba_number'] ?? 'N/A') ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($w4_exists): ?>
                                        <div class="data-card">
                                            <div class="card-header">
                                                <i class="fas fa-university me-2"></i> W4 Form
                                            </div>
                                            <div class="card-body">
                                                <table class="data-table">
                                                    <tr>
                                                        <td class="data-label">Qualifying Children</td>
                                                        <td class="data-value"><?= htmlspecialchars($w4_data['qualifying_children'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Number of other dependents</td>
                                                        <td class="data-value"><?= htmlspecialchars($w4_data['number_of_other_dependents'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Amount for qualifying children</td>
                                                        <td class="data-value"><?= htmlspecialchars($w4_data['amount_for_qualifying_children'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Other income (not from jobs)</td>
                                                        <td class="data-value"><?= htmlspecialchars($w4_data['tax_withheld'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Deductions</td>
                                                        <td class="data-value"><?= htmlspecialchars($w4_data['claim_deductions'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">Extra Withholding</td>
                                                        <td class="data-value"><?= htmlspecialchars($w4_data['extra_withholding'] ?? 'N/A') ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($mvr_exists || $certification_exists): ?>
                                <!-- files Tab -->
                                <div class="tab-pane fade" id="files" role="tabpanel" aria-labelledby="files-tab">
                                    <?php if ($mvr_exists): ?>
                                        <div class="data-card ">
                                            <div class="card-header">
                                                <i class="fas fa-file me-2"></i> MVR RELEASE CONSENT FORM
                                            </div>
                                            <div class="card-body">
                                                <table class="data-table">
                                                    <tr>
                                                        <td class="data-label">Driver's License Number</td>
                                                        <td class="data-value"><?= htmlspecialchars($mvr_data['license_number'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">License Front</td>
                                                        <td class="data-value">
                                                            <?php if (!empty($mvr_data['license_front_filename'])): ?>
                                                                <img src="uploads/licenses/<?= htmlspecialchars($mvr_data['license_front_filename']) ?>" alt="Driver's License" style="max-width: 200px; max-height: 150px;">
                                                            <?php else: ?>
                                                                N/A
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="data-label">License Back</td>
                                                        <td class="data-value">
                                                            <?php if (!empty($mvr_data['license_back_filename'])): ?>
                                                                <img src="uploads/licenses/<?= htmlspecialchars($mvr_data['license_back_filename']) ?>" alt="Driver's License" style="max-width: 200px; max-height: 150px;">
                                                            <?php else: ?>
                                                                N/A
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($certification_exists): ?>
                                        <div class="data-card  mb-4">
                                            <div class="card-header">
                                                <i class="fas fa-certificate me-2"></i> Certifications
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <?php if (!empty($certification_data['file_name'])): ?>
                                                        <?php
                                                        $modal_id = 'certificateModal_' . htmlspecialchars($certification_data['id']);
                                                        $file_name = htmlspecialchars($certification_data['file_name']);
                                                        $file_path = "ajax_helpers/uploads/certifications/" . $file_name;

                                                        // Verify file exists and is readable
                                                        if (file_exists($file_path) && is_readable($file_path)): ?>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <p class="detail-label">Certificate File:</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <img src="<?= $file_path ?>"
                                                                    class="img-fluid rounded border"
                                                                    alt="Certificate"
                                                                    style="max-height: 200px;"
                                                                    onerror="this.onerror=null;this.src='path_to_placeholder_image';">
                                                            </div>

                                                            <!-- Modal -->
                                                            <div class="modal fade" id="<?= $modal_id ?>" tabindex="-1" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Certificate</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body text-center">
                                                                            <img src="<?= $file_path ?>"
                                                                                class="img-fluid"
                                                                                alt="Certificate"
                                                                                onerror="this.onerror=null;this.parentElement.innerHTML='<p class=\'text-danger\'>Failed to load certificate image</p>'">
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                            <a href="<?= $file_path ?>" class="btn btn-primary" download>Download</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="col-12">
                                                                <div class="alert alert-warning">
                                                                    Certificate file exists in database but cannot be accessed at:<br>
                                                                    <?= htmlspecialchars($file_path) ?>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <div class="col-12">
                                                            <p class="text-muted">No certification file available</p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<!-- Floating Action Button
<a href="" class="fab" title="Edit Profile">
    <i class="fas fa-pencil-alt"></i>
</a> -->

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Add active class to nav items when clicked
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            document.querySelectorAll('.nav-link').forEach(item => {
                item.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    document.querySelector(".custom-modal-close").addEventListener("click", function() {
        if (document.activeElement) {
            document.activeElement.blur(); // remove focus before hiding
        }
        document.getElementById("customModal").style.display = "none";
        document.getElementById("certificatePreview").innerHTML = "";
    });
</script>