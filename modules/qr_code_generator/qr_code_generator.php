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
 
// Add this at the very top of your file (before any HTML output)
if (isset($_GET['generate_qr_url'])) {
    header('Content-Type: application/json');

    try {
        if (!isset($_GET['generate_qr_url'])) {
            throw new Exception('User ID parameter missing');
        }

        $user_id = intval($_GET['generate_qr_url']);
        if ($user_id <= 0) {
            throw new Exception('Invalid user ID');
        }

        // Generate a unique hash
        $unique_hash = bin2hex(random_bytes(16));
        $qr_url = "index.php?route=modules/qr_code_generator/employee?id=$user_id&hash=$unique_hash";

        // Debug output - remove in production
        error_log("Attempting to update user $user_id with QR URL: $qr_url");

        // Update database
        $result = DB::update('users', ['qr_unique_url' => $qr_url], "user_id=%i", $user_id);

        if ($result === false) {
            throw new Exception('Database update failed');
        }

        echo json_encode([
            'success' => true,
            'url' => $qr_url,
            'user_id' => $user_id
        ]);
        exit;
    } catch (Exception $e) {
        error_log("QR URL generation error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        exit;
    }
}
if (isset($_GET['delete_user_id'])) {
    $user_id = intval($_GET['delete_user_id']);

    try {
        DB::update('applicants', ['status' => 'pending'], "id=%i", $user_id);
        DB::delete('users', "user_id=%i", $user_id);

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => "User deleted successfully."
        ];
        echo '<script>window.location.href = "index.php?route=modules/qr_code_generator/qr_code_generator";</script>';
    } catch (MeekroDBException $e) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => "Delete failed: " . $e->getMessage()
        ];
        echo '<script>window.location.href = "index.php?route=modules/qr_code_generator/qr_code_generator";</script>';
    }
}

$roles = DB::query("SELECT DISTINCT name FROM roles ORDER BY name");
// Modify your users query to include a LEFT JOIN with applicants table
$users = DB::query("
    SELECT 
        u.*, 
        r.name as role_name,
        COALESCE(u.first_name, a.first_name, cc.first_name) as first_name,
        COALESCE(u.middle_initial, a.middle_initial) as middle_initial,
        COALESCE(u.last_name, a.last_name, cc.last_name) as last_name,
        COALESCE(u.phone, a.phone_number, cc.phone_number) as phone
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    LEFT JOIN applicants a ON u.user_id = a.id
    LEFT JOIN craft_contracting cc ON a.id = cc.id
    ORDER BY u.created_at DESC
");

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<div class="main-content app-content mt-0">
    <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
        <div style="margin-top: 25px;">
            <ol class="breadcrumb float-sm-right mt-2">
                <li class="breadcrumb-item">
                    <a href="index.php" style="color: #fe5500"><i
                            class="fas fa-home me-1"></i><?php echo lang("user_home"); ?></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="#" style="color: #fe5500"><?php echo lang(key: "user_users"); ?></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="#" style="color: #fe5500"><?php echo lang("user_user_mnagment"); ?></a>
                </li>
            </ol>
        </div>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show">
            <?= $message['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row1">
        <div class="row">
            <div class="col-12 mt-4">
                <div class="card rounded-4">
                    <div class="card-body p-3">
                        <div
                            class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded shadow-sm">
                            <!-- Title -->
                            <h3 class="card-title fw-bold m-0" style="font-size: 1.4rem; color: #333;">
                                <?php echo lang("user_user_list"); ?>
                            </h3>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <!-- Create User Button -->
                                <a href="?route=modules/users/create_user"
                                    class="btn btn-orange btn-sm d-flex align-items-center shadow-sm">
                                    <i class="fas fa-plus me-2"></i> <?php echo lang("user_new_user"); ?>
                                </a>
                                <!-- Generate QR Codes Button -->
                                <button id="generateSelectedQrBtn"
                                    class="btn btn-secondary btn-sm d-flex align-items-center shadow-sm" disabled>
                                    <i class="fas fa-qrcode me-2"></i> Generate QR Codes for Selected
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-end mb-3 flex-wrap">
                            <div class="col-md-3 mb-3 px-0">
                                <label style="font-size: 0.9rem;"><?php echo lang("user_filter_role"); ?></label>
                                <select id="roleFilter" class="form-select square-filter">
                                    <option value=""><?php echo lang("user_all_roles"); ?></option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= htmlspecialchars($role['name']) ?>">
                                            <?= htmlspecialchars($role['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2.5 mb-3 px-0">
                                <label
                                    style="font-size: 0.9rem; font-weight: 600;"><?php echo lang("user_search_kiosk_id"); ?></label>
                                <div class="input-group">
                                    <input type="text" id="kioskSearchInput" class="form-control square-filter"
                                        placeholder="<?php echo lang('user_search_kiosk_placeholder'); ?>"
                                        aria-label="Search users by Kiosk ID">
                                    <button class="btn btn-orange" type="button" id="kioskSearchButton"
                                        style="background-color: #FE5500 !important;" title="Search by Kiosk ID">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-2.5 mb-3 px-0">
                                <label
                                    style="font-size: 0.9rem; font-weight: 600;"><?php echo lang("user_search_phone"); ?></label>
                                <div class="input-group">
                                    <input type="text" id="phoneSearchInput" class="form-control square-filter"
                                        placeholder="<?php echo lang('user_search_phone_placeholder'); ?>"
                                        aria-label="Search users by phone number">
                                    <button class="btn btn-orange" type="button" id="phoneSearchButton"
                                        style="background-color: #FE5500 !important;" title="Search by phone">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-2.5 mb-3 px-0">
                                <label
                                    style="font-size: 0.9rem; font-weight: 600;"><?php echo lang("user_search_by_name"); ?></label>
                                <div class="input-group">
                                    <input type="text" id="nameSearchInput" class="form-control square-filter"
                                        placeholder="<?php echo lang('user_search_name_placeholder'); ?>"
                                        aria-label="Search users by name">
                                    <button class="btn btn-orange" type="button" id="nameSearchButton"
                                        style="background-color: #FE5500 !important;" title="Search by name">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-none d-md-block">
                            <div class="table-responsive">
                                <table class="table align-middle table-hover" id="users-datatable">
                                    <thead class="table-light">
                                        <tr>
                                            <th><input type="checkbox" id="selectAllCheckbox" title="Select All"></th>
                                            <th><?php echo lang("user_profile_picture"); ?></th>
                                            <th><?php echo lang("user_user_id"); ?></th>
                                            <th><?php echo lang("user_Kiosk_ID"); ?></th>
                                            <th><?php echo lang("user_username"); ?></th>
                                            <th><?php echo lang("user_first_name"); ?></th>
                                            <th><?php echo lang("user_last_name"); ?></th>
                                            <th><?php echo lang("user_email"); ?></th>
                                            <th><?php echo lang("user_phone"); ?></th>
                                            <th><?php echo lang("user_middle_initial"); ?></th>
                                            <th><?php echo lang("user_status"); ?></th>
                                            <th><?php echo lang("user_role"); ?></th>
                                            <th><?php echo lang("user_last_login"); ?></th>
                                            <th style="text-align: center;"><?php echo lang("user_Action"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="user-checkbox"
                                                        data-user-id="<?= $user['user_id'] ?>"
                                                        data-first-name="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                                                        data-last-name="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                                                        data-role="<?= htmlspecialchars($user['role_name'] ?? 'N/A') ?>">
                                                </td>
                                                <td>
                                                    <?php if (!empty($user['picture'])): ?>
                                                        <img src="<?= htmlspecialchars($user['picture']) ?>"
                                                            alt="Profile Picture"
                                                            style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div
                                                            style="width: 40px; height: 40px; border-radius: 50%; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-user" style="color: #999;"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $user['user_id'] ?></td>
                                                <td>
                                                    <?php if (isset($user['kioskID']) && $user['kioskID'] !== ''): ?>
                                                        <?= htmlspecialchars($user['kioskID']) ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($user['user_name']) ?></td>
                                                <td><?= htmlspecialchars($user['first_name'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($user['last_name'] ?? 'N/A') ?></td>
                                                <td>
                                                    <?php if (isset($user['email']) && $user['email'] !== ''): ?>
                                                        <?= htmlspecialchars($user['email']) ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (isset($user['phone']) && $user['phone'] !== ''): ?>
                                                        <?= preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $user['phone']) ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($user['middle_initial'] ?? 'N/A') ?></td>
                                                <td>
                                                    <span
                                                        class="badge bg-<?= $user['status'] == 'active' ? 'success' : 'danger' ?>">
                                                        <?= ucfirst($user['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (isset($user['role_name']) && $user['role_name'] !== ''): ?>
                                                        <?= htmlspecialchars($user['role_name']) ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($user['last_login'])): ?>
                                                        <?= date('m/d/Y h:i A', strtotime($user['last_login'])) ?>
                                                    <?php else: ?>
                                                        Never logged in
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1 flex-wrap justify-content-center">
                                                        <a href="?route=modules/qr_code_generator/view&id=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm rounded-3 px-2 action-btn"
                                                            style="background: #FE5505; color: white; font-size: 0.75rem;"
                                                            title="<?php echo lang("user_view"); ?>">
                                                            <i class="fas fa-eye"></i>
                                                            <span
                                                                class="action-text"><?php echo lang("user_view"); ?></span>
                                                        </a>
                                                        <a href="?route=modules/qr_code_generator/edituser&id=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm rounded-3 px-2 action-btn"
                                                            style="background: #FE5505; color: white; font-size: 0.75rem;"
                                                            title="<?php echo lang("user_edit"); ?>">
                                                            <i class="fas fa-edit"></i>
                                                            <span
                                                                class="action-text"><?php echo lang("user_edit"); ?></span>
                                                        </a>
                                                        <a href="?route=modules/qr_code_generator/qr_code_generator&delete_user_id=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm btn-danger rounded-3 delete-btn px-2 action-btn"
                                                            style="font-size: 0.75rem;"
                                                            title="<?php echo lang("user_delete"); ?>">
                                                            <i class="fas fa-trash"></i>
                                                            <span
                                                                class="action-text"><?php echo lang("user_delete"); ?></span>
                                                        </a>
                                                        <button
                                                            class="btn btn-sm btn-info rounded-3 generate-qr-btn px-2 action-btn"
                                                            data-user-id="<?= $user['user_id'] ?>"
                                                            data-first-name="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                                                            data-last-name="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                                                            data-role="<?= htmlspecialchars($user['role_name'] ?? 'N/A') ?>">
                                                            <i class="fas fa-qrcode"></i>
                                                            <span
                                                                class="d-md-none ms-1"><?php echo lang("user_generate_qr"); ?></span>
                                                        </button>

                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <div class="d-block d-md-none">
                            <div id="users-cards-container">
                                <div class="row" id="users-cards-row">
                                    <?php foreach ($users as $user): ?>
                                        <div class="col-12 mb-3 user-card">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start mb-2">
                                                        <?php if (!empty($user['picture'])): ?>
                                                            <img src="<?= htmlspecialchars($user['picture']) ?>"
                                                                alt="Profile Picture"
                                                                style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; margin-right: 15px;">
                                                        <?php else: ?>
                                                            <div
                                                                style="width: 50px; height: 50px; border-radius: 50%; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                                                <i class="fas fa-user"
                                                                    style="color: #999; font-size: 20px;"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div style="flex-grow: 1;">
                                                            <h5 class="card-title mb-0">
                                                                <?= htmlspecialchars($user['first_name'] ?? '') ?>
                                                                <?= htmlspecialchars($user['last_name'] ?? '') ?>
                                                            </h5>
                                                            <span class="badge bg-orange">
                                                                <?php if (isset($user['role_name']) && $user['role_name'] !== ''): ?>
                                                                    <?= htmlspecialchars($user['role_name']) ?>
                                                                <?php else: ?>
                                                                    N/A
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="user-details">
                                                        <div class="detail-item">
                                                            <span
                                                                class="detail-label"><?php echo lang("user_user_id"); ?>:</span>
                                                            <span class="detail-value"><?= $user['user_id'] ?></span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span
                                                                class="detail-label"><?php echo lang("user_username"); ?>:</span>
                                                            <span
                                                                class="detail-value"><?= htmlspecialchars($user['user_name']) ?></span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span
                                                                class="detail-label"><?php echo lang("user_email"); ?>:</span>
                                                            <span
                                                                class="detail-value"><?= isset($user['email']) && $user['email'] !== '' ? htmlspecialchars($user['email']) : 'N/A' ?></span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span
                                                                class="detail-label"><?php echo lang("user_phone"); ?>:</span>
                                                            <span
                                                                class="detail-value"><?= isset($user['phone']) && $user['phone'] !== '' ? htmlspecialchars($user['phone']) : 'N/A' ?></span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span
                                                                class="detail-label"><?php echo lang("user_middle_initial"); ?>:</span>
                                                            <span
                                                                class="detail-value"><?= isset($user['middle_initial']) && $user['middle_initial'] !== '' ? htmlspecialchars($user['middle_initial']) : 'N/A' ?></span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span
                                                                class="detail-label"><?php echo lang("user_status"); ?>:</span>
                                                            <span
                                                                class="badge bg-<?= $user['status'] == 'active' ? 'success' : 'danger' ?>">
                                                                <?= ucfirst($user['status']) ?>
                                                            </span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span
                                                                class="detail-label"><?php echo lang("user_last_login"); ?>:</span>
                                                            <span class="detail-value">
                                                                <?php if (!empty($user['last_login'])): ?>
                                                                    <?= date('m/d/Y h:i A', strtotime($user['last_login'])) ?>
                                                                <?php else: ?>
                                                                    Never logged in
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex gap-1 flex-wrap justify-content-center">
                                                        <a href="?route=modules/qr_code_generator/view&id=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm rounded-3 px-2 action-btn"
                                                            style="background: #FE5505; color: white; font-size: 0.75rem;"
                                                            title="<?php echo lang("user_view"); ?>">
                                                            <i class="fas fa-eye"></i>
                                                            <span
                                                                class="d-md-none ms-1"><?php echo lang("user_view"); ?></span>
                                                        </a>
                                                        <a href="?route=modules/qr_code_generator/edituser&id=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm rounded-3 px-2 action-btn"
                                                            style="background: #FE5505; color: white; font-size: 0.75rem;"
                                                            title="<?php echo lang("user_edit"); ?>">
                                                            <i class="fas fa-edit"></i>
                                                            <span
                                                                class="d-md-none ms-1"><?php echo lang("user_edit"); ?></span>
                                                        </a>
                                                        <a href="?route=modules/qr_code_generator/qr_code_generator&delete_user_id=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm btn-danger rounded-3 delete-btn px-2 action-btn"
                                                            style="font-size: 0.75rem;"
                                                            title="<?php echo lang("user_delete"); ?>">
                                                            <i class="fas fa-trash"></i>
                                                            <span
                                                                class="d-md-none ms-1"><?php echo lang("user_delete"); ?></span>
                                                        </a>
                                                        <button
                                                            class="btn btn-sm btn-info rounded-3 generate-qr-btn px-2 action-btn"
                                                            data-user-id="<?= $user['user_id'] ?>"
                                                            data-first-name="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                                                            data-last-name="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                                                            data-role="<?= htmlspecialchars($user['role_name'] ?? 'N/A') ?>">
                                                            <i class="fas fa-qrcode"></i>
                                                            <span
                                                                class="d-md-none ms-1"><?php echo lang("user_generate_qr"); ?></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <nav aria-label="User cards pagination">
                                    <ul class="pagination" id="cards-pagination"></ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">Employee QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="d-flex flex-column align-items-center">
                    <div class="qr-code-wrapper">
                        <div id="qrCodeContainer"></div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-orange" id="downloadQrBtn">Download QR</button>
            </div>
        </div>
    </div>
</div>

<!-- Updated PDF Preview Modal -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-orange text-white">
                <h5 class="modal-title" id="pdfPreviewModalLabel" style="color: white;">Employee QR Codes Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info py-2">
                                <i class="fas fa-info-circle me-2"></i> Preview shows 3 QR codes per row. The PDF will
                                include all selected employees.
                            </div>
                        </div>
                    </div>
                    <div id="pdfPreviewContainer" class="row g-3"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
                <button type="button" class="btn btn-orange" id="downloadPdfBtn">
                    <i class="fas fa-download me-1"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</div>


<style>
    /* Add these styles to your existing CSS */

    #qrCodeContainer {
        width: 100%;
        height: 100%;
        background: white;
        padding: 10px;
    }

    #qrCodeContainer img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .qr-logo-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 50%;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    }

    .qr-logo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: contain;
    }

    #qrCodeContainer {
        width: 200px;
        height: 200px;
        margin: 0 auto;
        border: 2px solid #ccc;
        padding: 10px;
        /* Example: grid of dots (not random, but visually similar) */
        background-size: 20px 20px;
        background-position: 0 0, 10px 10px;
        border-radius: 50%;
        border-color: #fe5500;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }


    #qrCodeContainer img {
        width: 78%;
        height: 78%;
    }

    .modal-content {
        border-radius: 15px;
        overflow: hidden;
    }

    .modal-header {
        background-color: #FE5500;
        color: white;
    }

    .modal-title {
        font-weight: 600;
    }

    .btn-close {
        filter: invert(1);
    }

    .row {
        margin-top: 10px;
    }

    .btn-orange {
        background-color: #FE5500;
        border-color: #FE5500;
        color: white;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 !important;
    }

    .action-text {
        display: none !important;
    }

    #qrCodeContainerpdf {
        width: 165px;
        height: 165px;
        margin: 0 auto;
        border: 2px solid #ccc;
        padding: 10px;
        /* Example: grid of dots (not random, but visually similar) */

        background-size: 20px 20px;
        background-position: 0 0, 10px 10px;
        border-radius: 50%;
        border-color: #fe5500;

        align-items: center;
        justify-content: center;
        overflow: hidden;
    }


    @media (max-width: 767.98px) {
        .action-btn {
            width: auto;
            padding: 0.25rem 0.5rem !important;
        }

        .action-text {
            display: inline !important;
            margin-left: 4px;
        }

        .btn-orange:hover {
            background-color: #e04b00;
            border-color: #e04b00;
            color: white;
        }

        .bg-orange {
            background-color: #FE5500 !important;
        }

        #users-datatable_filter input {
            border-radius: 20px !important;
            border: 1px solid #FE5505 !important;
            padding: 5px 15px !important;
            margin-bottom: 15px;
        }

        #users-datatable th {
            background: #FE5505 !important;
            color: white !important;
            font-size: 0.85rem;
            padding: 8px 5px;
        }

        #users-datatable td {
            vertical-align: middle;
            padding: 8px 5px;
            font-size: 0.85rem;
        }

        .page-item.active .page-link {
            background: #FE5505 !important;
            border-color: #FE5505 !important;
        }

        .square-filter {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.9rem;
        }

        .square-filter:focus {
            border-color: #adb5bd;
            box-shadow: 0 0 0 3px rgba(108, 117, 125, 0.15);
        }

        .user-card .card {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }

        .user-card .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
        }

        .detail-item {
            display: flex;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            min-width: 100px;
        }

        .detail-value {
            color: #333;
            flex-grow: 1;
        }
    }

    @media (min-width: 768px) {
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
        }

        .action-text {
            display: none;
        }

        .action-btn:hover {
            width: auto;
            padding: 0.25rem 0.5rem !important;
        }

        .action-btn:hover .action-text {
            display: inline;
            margin-left: 4px;
        }
    }

    @media screen and (max-width: 360px) {
        .page-header {
            margin-top: 10px !important;
            margin-bottom: 10px !important;
        }

        .row1 {
            margin-left: -30px;
            margin-right: -30px;
            margin-top: -25px;
        }

        .breadcrumb {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }

        .breadcrumb-item i {
            font-size: 0.7rem;
            margin-right: 0.25rem !important;
        }

        .card-title {
            font-size: 1rem !important;
        }

        .btn-orange.btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .user-card {
            padding: 0 5px;
        }

        .user-card .card-body {
            padding: 1rem;
        }

        .detail-item {
            flex-direction: column;
        }

        .detail-label {
            min-width: auto;
            margin-bottom: 0.1rem;
        }

        #users-cards-container {
            margin: 0 -5px;
        }

        .alert {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }
    }

    @media screen and (max-width: 430px) {
        .page-header {
            margin-top: 10px !important;
            margin-bottom: 10px !important;
        }

        .row1 {
            margin-left: -30px;
            margin-right: -30px;
            margin-top: -25px;
        }

        .breadcrumb {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }

        .breadcrumb-item i {
            font-size: 0.7rem;
            margin-right: 0.25rem !important;
        }

        .card-title {
            font-size: 1rem !important;
        }

        .btn-orange.btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .user-card {
            padding: 0 5px;
        }

        .user-card .card-body {
            padding: 1rem;
        }

        .detail-item {
            flex-direction: column;
        }

        .detail-label {
            min-width: auto;
            margin-bottom: 0.1rem;
        }

        #users-cards-container {
            margin: 0 -5px;
        }

        .alert {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }
    }

    @media screen and (max-width: 576px) {
        .card-body {
            padding: 1rem !important;
        }

        .card-title {
            font-size: 1.1rem !important;
        }

        .user-card .card-body {
            padding: 1.25rem;
        }

        .detail-item {
            font-size: 0.9rem;
        }
    }

    @media screen and (min-width: 768px) and (max-width: 991px) {
        #roleFilter {
            max-width: 200px;
        }
    }

    /* PDF Generate Code */
    .qr-code-pdf {
        border: 1px solid #ddd;
        padding: 10px;
        margin-bottom: 20px;
        text-align: center;
        background: white;
    }

    .qr-code-row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .qr-code-col {
        flex: 0 0 33.333%;
        max-width: 33.333%;
        padding: 0 15px;
        box-sizing: border-box;
    }

    .user-info {
        background-color: white;
        margin-top: 3px;
        font-size: 10px;
        font-weight: bold;
        color: black;
    }

    #selectAllCheckbox {
        cursor: pointer;
    }

    .user-checkbox {
        cursor: pointer;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>

<!-- jsPDF for PDF generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    $(document).ready(function () {
        const table = $('#users-datatable').DataTable({
            searching: true,
            paging: true,
            ordering: true,
            order: [
                [1, 'desc']
            ],
            info: true,
            lengthMenu: [
                [25, 50, 100, -1],
                [25, 50, 100, "All"]
            ],
            pageLength: 25,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "<?php echo lang('user_search_placeholder'); ?>",
            },
            initComplete: function () {
                $('#roleFilter').on('change', function () {
                    const selected = $(this).val();
                    const roleColIndex = table.column(':contains("<?php echo lang("user_role"); ?>")').index();
                    if (roleColIndex !== undefined) {
                        table.column(roleColIndex).search('^' + selected + '$', true, false).draw();
                    }
                });
            }
        });

        $('#searchInput').on('keyup', function () {
            table.search(this.value).draw();
        });

        $('#searchButton').on('click', function () {
            table.search($('#searchInput').val()).draw();
        });

        $('#kioskSearchInput').on('keyup', function (e) {
            if (e.key === 'Enter') {
                performKioskSearch();
            }
        });

        $('#kioskSearchButton').on('click', performKioskSearch);

        function performKioskSearch() {
            const kioskSearchTerm = $('#kioskSearchInput').val().trim();
            const kioskColIndex = table.column(':contains("<?php echo lang("user_Kiosk_ID"); ?>")').index();

            if (kioskColIndex !== undefined) {
                table.columns().search('');
                table.column(kioskColIndex).search(kioskSearchTerm).draw();
            }
        }

        $('#phoneSearchInput').on('keyup', function (e) {
            if (e.key === 'Enter') {
                performPhoneSearch();
            }
        });

        $('#phoneSearchButton').on('click', performPhoneSearch);

        function performPhoneSearch() {
            const phoneSearchTerm = $('#phoneSearchInput').val().trim();
            const phoneColIndex = table.column(':contains("<?php echo lang("user_phone"); ?>")').index();

            if (phoneColIndex !== undefined) {
                table.columns().search('');
                table.column(phoneColIndex).search(phoneSearchTerm).draw();
            }
        }

        $('#nameSearchInput').on('keyup', function (e) {
            if (e.key === 'Enter') {
                performNameSearch();
            }
        });

        $('#nameSearchButton').on('click', performNameSearch);

        function performNameSearch() {
            const nameSearchTerm = $('#nameSearchInput').val().trim();
            const nameColIndex = table.column(':contains("<?php echo lang("user_first_name"); ?>")').index();

            if (nameColIndex !== undefined) {
                table.columns().search('');
                table.column(nameColIndex).search(nameSearchTerm).draw();
            }
        }

        function initCardPagination() {
            let cardsPerPage = 10;
            const $cards = $('.user-card');
            const totalCards = $cards.length;
            let totalPages = Math.ceil(totalCards / cardsPerPage);
            let currentPage = 1;

            const pageSizeDropdown = `
            <div class="dropdown ms-2 d-inline">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="pageSizeDropdown" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    ${cardsPerPage} per page
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item page-size" data-size="10">10 per page</a></li>
                    <li><a class="dropdown-item page-size" data-size="25">25 per page</a></li>
                    <li><a class="dropdown-item page-size" data-size="50">50 per page</a></li>
                    <li><a class="dropdown-item page-size" data-size="100">100 per page</a></li>
                </ul>
            </div>
        `;

            $(pageSizeDropdown).insertBefore('#cards-pagination');

            showCards();
            updatePaginationUI();

            $('.page-size').on('click', function (e) {
                e.preventDefault();
                const size = $(this).data('size');
                cardsPerPage = parseInt(size);
                $('#pageSizeDropdown').text(`${size} per page`);
                totalPages = Math.ceil(totalCards / cardsPerPage);
                currentPage = 1;
                showCards();
                updatePaginationUI();
            });

            $(document).on('click', '#prev-page', function (e) {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    showCards();
                    updatePaginationUI();
                }
            });

            $(document).on('click', '#next-page', function (e) {
                e.preventDefault();
                if (currentPage < totalPages) {
                    currentPage++;
                    showCards();
                    updatePaginationUI();
                }
            });

            function showCards() {
                const startIndex = (currentPage - 1) * cardsPerPage;
                const endIndex = startIndex + cardsPerPage;
                $cards.hide();
                $cards.slice(startIndex, endIndex).show();
            }

            function updatePaginationUI() {
                const $pagination = $('#cards-pagination');
                $pagination.empty();
                $pagination.append('<li class="page-item"><a class="page-link" href="#" aria-label="Previous" id="prev-page"><span aria-hidden="true">&laquo;</span></a></li>');
                $pagination.append(`<li class="page-item disabled"><a class="page-link" href="#">Page ${currentPage} of ${totalPages}</a></li>`);
                $pagination.append('<li class="page-item"><a class="page-link" href="#" aria-label="Next" id="next-page"><span aria-hidden="true">&raquo;</span></a></li>');
                $('#prev-page').parent().toggleClass('disabled', currentPage === 1);
                $('#next-page').parent().toggleClass('disabled', currentPage === totalPages);
            }
        }

        if ($(window).width() < 768) {
            initCardPagination();
        }

        $(window).resize(function () {
            if ($(window).width() < 768 && $('#cards-pagination').children().length === 0) {
                initCardPagination();
            }
        });

        $(document).on('click', '.delete-btn', function (e) {
            e.preventDefault();
            const deleteUrl = $(this).attr('href');
            Swal.fire({
                title: '<?php echo lang("user_delete_confirmation_title"); ?>',
                text: '<?php echo lang("user_delete_confirmation_text"); ?>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FE5500',
                cancelButtonColor: '#d33',
                confirmButtonText: '<?php echo lang("user_delete_confirm_button"); ?>',
                cancelButtonText: '<?php echo lang("user_delete_cancel_button"); ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrl;
                }
            });
        });
    });

    // --- Fix: Select All Checkbox Functionality ---
    $('#selectAllCheckbox').change(function () {
        const isChecked = $(this).is(':checked');
        $('.user-checkbox').prop('checked', isChecked);
        toggleGenerateButton();
    });

    // When individual checkbox changes
    $(document).on('change', '.user-checkbox', function () {
        const totalCheckboxes = $('.user-checkbox').length;
        const checkedCheckboxes = $('.user-checkbox:checked').length;

        $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes);
        toggleGenerateButton();
    });

    // Function to enable/disable generate button
    function toggleGenerateButton() {
        const anyChecked = $('.user-checkbox:checked').length > 0;
        $('#generateSelectedQrBtn').prop('disabled', !anyChecked);
    }

    // --- QR Code Generation for a single user ---
    $(document).on('click', '.generate-qr-btn', function () {
        const userId = $(this).data('user-id');
        const firstName = $(this).data('first-name');
        const lastName = $(this).data('last-name');
        const role = $(this).data('role');

        Swal.fire({
            title: 'Generating QR Code...',
            html: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const qrUrl = `https://craftgc.com/app/employee.php?id=${userId}`;

        // Update employee info in modal
        $('#employeeName').text(`${firstName} ${lastName}`);
        $('#employeePosition').text(role);
        $('#employeeId').text(`ID: ${userId}`);

        generateQrCode(qrUrl, firstName, lastName, role, userId);
    });

    function generateQrCode(qrUrl, firstName, lastName, position, userId) {
        $('#qrCodeContainer').empty();
        Swal.fire({
            title: 'Generating QR Code',
            html: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const tempDiv = document.createElement("div");
        new QRCode(tempDiv, {
            text: qrUrl,
            width: 200,  // Reduced size for label
            height: 200, // Reduced size for label
            correctLevel: QRCode.CorrectLevel.H
        });

        setTimeout(() => {
            const originalCanvas = tempDiv.querySelector('canvas');
            if (!originalCanvas) {
                Swal.fire('Error', 'QR code generation failed.', 'error');
                return;
            }

            const qrImage = new Image();
            qrImage.src = originalCanvas.toDataURL("image/png");
            qrImage.onload = () => {
                // Canvas size optimized for 1.5" label at 300 DPI (450px)
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");
                canvas.width = 450;
                canvas.height = 450;

                // White background
                ctx.fillStyle = "#ffffff";
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                // Draw the QR code (smaller to leave space for text)
                const qrSize = 270; // Adjusted size for label
                const qrX = (canvas.width - qrSize) / 2;
                const qrY = 50; // Position from top

                ctx.drawImage(qrImage, qrX, qrY, qrSize, qrSize);

                const logo = new Image();
                logo.src = "./assets/images/craft_logo.png";
                logo.onload = () => {
                    const logoSize = 60; // Smaller logo for label
                    const logoX = (canvas.width - logoSize) / 2;
                    const logoY = qrY + (qrSize - logoSize) / 2;

                    // Draw white circle behind logo
                    ctx.beginPath();
                    ctx.arc(logoX + logoSize / 2, logoY + logoSize / 2, logoSize / 2 + 3, 0, Math.PI * 2);
                    ctx.fillStyle = "#ffffff";
                    ctx.fill();

                    ctx.drawImage(logo, logoX, logoY, logoSize, logoSize);

                    // Add text below QR code
                    ctx.fillStyle = "#000000";
                    ctx.textAlign = "center";

                    // Name (larger font)
                    ctx.font = "bold 36px Arial";
                    ctx.fillText(`${firstName} ${lastName}`, canvas.width / 2, qrY + qrSize + 60);

                    // Position
                    ctx.font = "bold 32px Arial";
                    ctx.fillText(position, canvas.width / 2, qrY + qrSize + 103);

                    // User ID (smaller font)
                    // ctx.font = "14px Arial";
                    // ctx.fillText(`ID: ${userId}`, canvas.width / 2, qrY + qrSize + 75);

                    const finalImage = canvas.toDataURL("image/png");
                    $('#qrCodeContainer').html(`<img src="${finalImage}" class="qr-code-image">`);

                    // Store all data needed for download
                    $('#downloadQrBtn').data({
                        'user-name': `${firstName} ${lastName}`,
                        'user-role': position,
                        'user-id': userId,
                        'qr-image': finalImage
                    });

                    Swal.close();
                    const qrModal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
                    qrModal.show();
                };

                logo.onerror = () => {
                    // Handle case where logo fails to load
                    console.error("Logo failed to load");
                    // Continue without logo
                    const finalImage = canvas.toDataURL("image/png");
                    $('#qrCodeContainer').html(`<img src="${finalImage}" class="qr-code-image">`);

                    $('#downloadQrBtn').data({
                        'user-name': `${firstName} ${lastName}`,
                        'user-role': position,
                        'user-id': userId,
                        'qr-image': finalImage
                    });

                    Swal.close();
                    const qrModal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
                    qrModal.show();
                };
            };
        }, 200);
    }

    $('#downloadQrBtn').on('click', function () {
        const userName = $(this).data('user-name');
        const userRole = $(this).data('user-role');
        const userId = $(this).data('user-id');
        const qrImage = $(this).data('qr-image');

        if (!qrImage) return;

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();

        // Set larger canvas size to accommodate text below QR code
        canvas.width = 450;
        canvas.height = 450; // Increased height for text

        img.onload = function () {
            // Draw white background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Calculate positions
            const qrSize = 260; // Size of QR code
            const qrX = (canvas.width - qrSize) / 2;
            const qrY = 100; // Position QR code higher to leave space for text

            // Draw QR code
            ctx.drawImage(img, qrX, qrY, qrSize, qrSize);

            // Draw logo overlay
            const logo = new Image();
            logo.src = "./assets/images/craft_logo.png";
            logo.onload = function () {
                const logoSize = 60;
                const logoX = (canvas.width - logoSize) / 2;
                const logoY = qrY + (qrSize - logoSize) / 2;

                // // Draw white circle behind logo
                // ctx.beginPath();
                // ctx.arc(logoX + logoSize / 2, logoY + logoSize / 2, logoSize / 2 + 10, 0, Math.PI * 2);
                // ctx.fillStyle = "#ffffff";
                // ctx.fill();

                // Draw logo
                // ctx.drawImage(logo, logoX, logoY, logoSize, logoSize);

                // --- Draw dots in the ring between QR and border , generating but not being added, as that area is commented out---
                const centerX = canvas.width / 2;
                const centerY = qrY + qrSize / 2;
                const qrRadius = qrSize / 2;
                const borderPadding = 50;
                const borderWidth = 5;
                const borderOuterRadius = qrRadius + borderPadding;
                const dotRadius = 1.5;
                const dotSpacing = 20;
                // const dotColor = "black";

                // Use clipping to restrict dots to the ring
                ctx.save();
                ctx.beginPath();
                ctx.arc(centerX, centerY, borderOuterRadius, 0, Math.PI * 2);
                ctx.arc(centerX, centerY, qrRadius, 0, Math.PI * 2, true);
                ctx.clip();

                // // Draw dots
                // ctx.beginPath();
                // for (let y = qrY - borderOuterRadius; y < qrY + qrSize + borderOuterRadius; y += dotSpacing) {
                //     for (let x = qrX - borderOuterRadius; x < qrX + qrSize + borderOuterRadius; x += dotSpacing) {
                //         ctx.moveTo(x + dotRadius, y);
                //         ctx.arc(x, y, dotRadius, 0, Math.PI * 2);
                //     }
                // }
                // ctx.fillStyle = dotColor;
                // ctx.fill();
                ctx.restore();

                // Draw thicker orange border
                ctx.beginPath();
                const borderPosition = borderOuterRadius + borderWidth / 2;
                ctx.arc(centerX, centerY, borderPosition, 0, Math.PI * 2);
                ctx.lineWidth = borderWidth;
                ctx.strokeStyle = "#FE5500";
                ctx.stroke();

                // --- Add Employee Information Below QR Code ---
                const textY = qrY + qrSize + 100 + 10;

                // // Employee Name
                // ctx.font = 'bold 36px Arial';
                // ctx.textAlign = 'center';
                // ctx.fillStyle = '#333';
                // ctx.fillText(userName, canvas.width / 2, textY);

                // // Employee Role
                // ctx.font = '24px Arial';
                // ctx.fillStyle = '#555';
                // ctx.fillText(userRole, canvas.width / 2, textY + 40);

                // // Employee ID
                // ctx.font = '20px Arial';
                // ctx.fillStyle = '#777';
                // ctx.fillText(`ID: ${userId}`, canvas.width / 2, textY + 80);

                // // Company Name
                // ctx.font = 'italic 20px Arial';
                // ctx.fillStyle = '#FE5500';
                // ctx.fillText('Craft Contracting', canvas.width / 2, textY + 120);

                // Create download link
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png', 1.0);
                link.download = `Craft_QR_${userName.replace(/ /g, '_')}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            };
        };
        img.src = qrImage;
    });
    // --- QR code for multiple selected users ---
    $('#generateSelectedQrBtn').click(function () {
        const selectedUsers = [];
        $('.user-checkbox:checked').each(function () {
            selectedUsers.push({
                id: $(this).data('user-id'),
                firstName: $(this).data('first-name'),
                lastName: $(this).data('last-name'),
                role: $(this).data('role')
            });
        });
        if (selectedUsers.length === 0) {
            Swal.fire('Error', 'No users selected', 'error');
            return;
        }
        generateQrCodesForPdf(selectedUsers);
    });

    function generateQrCodesForPdf(users) {
        Swal.fire({
            title: 'Generating QR Codes...',
            html: 'Please wait while we prepare your QR codes',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const qrPromises = users.map(user => {
            return new Promise((resolve) => {
                const qrUrl = `https://craftgc.com/app/employee.php?id=${user.id}`;
                const tempDiv = document.createElement("div");
                new QRCode(tempDiv, {
                    text: qrUrl,
                    width: 400,
                    height: 400,
                    correctLevel: QRCode.CorrectLevel.H
                });

                setTimeout(() => {
                    const canvas = tempDiv.querySelector('canvas');
                    if (canvas) {
                        const ctx = canvas.getContext('2d');
                        const logo = new Image();
                        logo.src = "./assets/images/craft_logo.png";
                        logo.onload = () => {
                            const logoSize = 60;
                            const logoX = (canvas.width - logoSize) / 2;
                            const logoY = (canvas.height - logoSize) / 2;

                            ctx.beginPath();
                            ctx.arc(logoX + logoSize / 2, logoY + logoSize / 2, logoSize / 2 + 3, 0, Math.PI * 2);
                            ctx.fillStyle = "#ffffff";
                            ctx.fill();

                            ctx.drawImage(logo, logoX, logoY, logoSize, logoSize);
                            resolve({
                                id: user.id,
                                firstName: user.firstName,
                                lastName: user.lastName,
                                role: user.role,
                                qrDataUrl: canvas.toDataURL('image/png')
                            });
                        };
                    } else {
                        resolve(null);
                    }
                }, 100);
            });
        });

        Promise.all(qrPromises).then(results => {
            Swal.close();
            displayPdfPreview(results.filter(r => r !== null));
        });
    }

    function displayPdfPreview(qrData) {
        const container = $('#pdfPreviewContainer');
        container.empty();

        for (let i = 0; i < qrData.length; i += 4) {
            const row = $('<div class="row"></div>'); // Use Bootstrap row
            for (let j = 0; j < 4; j++) {
                if (i + j >= qrData.length) break;
                const user = qrData[i + j];
                const col = $(`
                <div class=" qr-code-wrapper  col-md-3 text-center ">
                    <div class="qr-code-pdf " id="qrCodeContainerpdf">
                        <img src="${user.qrDataUrl}" style="width: 90px; height: 90px; margin-top: 10px;"> 
                        <div class="user-info mt-1 fw-bold">${user.firstName} ${user.lastName}</div>
                     <div class="user-info">${user.role || 'N/A'}</div>         
                    </div>
                     
                </div>
            `);
                row.append(col);
            }
            container.append(row);
        }

        $('#downloadPdfBtn').data('qr-data', qrData);
        const pdfModal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
        pdfModal.show();
    }

    $('#downloadPdfBtn').click(function () {
        const qrData = $(this).data('qr-data');
        if (!qrData || qrData.length === 0) return;

        Swal.fire({
            title: 'Generating PDF...',
            html: 'Please wait while we generate your PDF',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();
        const margin = 15;

        const circleDiameter = 40; // Slightly smaller diameter to fit 4 per row
        const qrWidth = 20; // Smaller QR image size
        const circleSpacing = (pageWidth - margin * 2 - circleDiameter * 4) / 3; // Adjusted for 4 circles

        let yPos = margin;

        // Title
        doc.setFontSize(16);
        doc.setFont(undefined, 'bold');
        doc.text('Employee QR Codes - Craft Contracting', pageWidth / 2, yPos, {
            align: 'center'
        });
        yPos += 25; // Increased from 15 to 25 to add more space after title
        doc.setFont(undefined, 'normal');

        for (let i = 0; i < qrData.length; i += 4) {
            if (yPos + circleDiameter + 25 > pageHeight - margin) {
                doc.addPage();
                yPos = margin;

                doc.setFontSize(16);
                doc.setFont(undefined, 'bold');
                doc.text('Employee QR Codes - Craft Contracting', pageWidth / 2, yPos, {
                    align: 'center'
                });
                yPos += 25; // Consistent with first page
                doc.setFont(undefined, 'normal');
            }

            let xPos = margin;

            for (let j = 0; j < 4; j++) {
                if (i + j >= qrData.length) break;
                const user = qrData[i + j];

                // Draw circular border with custom color
                const centerX = xPos + circleDiameter / 2;
                const centerY = yPos + circleDiameter / 2;
                doc.setDrawColor(254, 85, 0); // Border color #fe5500 in RGB
                doc.setLineWidth(0.7);
                doc.circle(centerX, centerY, circleDiameter / 2, 'S');

                // Draw dots in the ring between QR and border
                const qrRadius = qrWidth / 2;
                const borderRadius = circleDiameter / 2;
                const dotRadius = 0.2; // size of each dot
                const dotSpacing = 4.5; // spacing between dots
                const dotColor = [0, 0, 0]; // black dots

                // Set dot color
                // doc.setFillColor(dotColor[0], dotColor[1], dotColor[2]);

                // Loop in a spiral or grid to place dots in the ring
                // for (let r = qrRadius + 1; r <= borderRadius - 1; r += dotSpacing / 2) {
                //     // Calculate circumference at this radius
                //     const circumference = 2 * Math.PI * r;
                //     const numDots = Math.floor(circumference / dotSpacing);
                //     for (let k = 0; k < numDots; k++) {
                //         const angle = (k / numDots) * 2 * Math.PI;
                //         const dotX = centerX + r * Math.cos(angle);
                //         const dotY = centerY + r * Math.sin(angle);
                //         // Draw a filled circle for the dot
                //         doc.circle(dotX, dotY, dotRadius, 'F');
                //     }
                // }

                // Center QR image inside the circle
                const qrX = centerX - qrWidth / 2;
                const qrY = centerY - qrWidth / 2 - 5;
                doc.addImage(user.qrDataUrl, 'PNG', qrX, qrY, qrWidth, qrWidth);

                // Name and ID below
                doc.setFontSize(8); // Slightly smaller font to fit
                doc.setTextColor(0, 0, 0); // Black text
                doc.setFont("helvetica", "bold"); // Set font to bold


                // Calculate text width and height (approximate)
                const text1 = `${user.firstName} ${user.lastName}`;
                const text2 = `${user.role}`;
                const textHeight = 0; // Approximate height of text
                const padding = 0; // Padding around text

                // Get text width (approximate)
                const textWidth = Math.max(
                    doc.getStringUnitWidth(text1) * doc.internal.getFontSize() / doc.internal.scaleFactor,
                    doc.getStringUnitWidth(text2) * doc.internal.getFontSize() / doc.internal.scaleFactor
                );

                // Draw white background rectangle
                doc.setFillColor(255, 255, 255); // White
                doc.rect(
                    centerX - textWidth / 2 - padding, // x position
                    yPos + circleDiameter - 12 - textHeight - padding / 2, // y position
                    textWidth + padding * 2, // width
                    textHeight * 2 + padding * 2, // height
                    'F' // Fill mode
                );

                // Add text
                doc.text(text1, centerX, yPos + circleDiameter - 10, {
                    align: 'center'
                });
                doc.text(text2, centerX, yPos + circleDiameter - 5, {
                    align: 'center'
                });

                xPos += circleDiameter + circleSpacing;
            }

            yPos += circleDiameter + 25;
        }

        doc.save('Employee_QR_Codes.pdf');
        Swal.close();
    });
</script>