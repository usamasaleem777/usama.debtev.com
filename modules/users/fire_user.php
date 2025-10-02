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

if (isset($_GET['toggle_status'])) {
    $user_id = intval($_GET['toggle_status']);

    try {
        // Get current status
        $user = DB::queryFirstRow("SELECT status FROM users WHERE user_id=%i", $user_id);

        if ($user) {
            $new_status = ($user['status'] == 'active') ? 'suspended' : 'active';

            $result = DB::update('users', [
                'status' => $new_status,
                'updated_at' => date('Y-m-d H:i:s')
            ], "user_id=%i", $user_id);

            // Check if the update was successful
            if ($result) {

                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => "User status updated to " . $new_status . " successfully."
                ];
            } else {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => "Status update failed: No rows were affected."
                ];
                error_log("User status update failed for user_id: " . $user_id);
            }
        } else {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => "User not found."
            ];
        }
        echo '<script>window.location.href = "index.php?route=modules/users/view_users";</script>';
        exit(); // Add this to prevent further execution
    } catch (MeekroDBException $e) {
        error_log("User status update error: " . $e->getMessage());
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => "Status update failed: " . $e->getMessage()
        ];
        echo '<script>window.location.href = "index.php?route=modules/users/view_users";</script>';
        exit(); // Add this to prevent further execution
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
        echo '<script>window.location.href = "index.php?route=modules/users/view_users";</script>';
    } catch (MeekroDBException $e) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => "Delete failed: " . $e->getMessage()
        ];
        echo '<script>window.location.href = "index.php?route=modules/users/view_users";</script>';
    }
}

$roles = DB::query("SELECT DISTINCT name FROM roles ORDER BY name");
$status_filter = 'fire'; // Default to show only 'fire' status
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
    WHERE u.status = %s
    ORDER BY u.created_at DESC
", $status_filter);

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
                    <a href="#" style="color: #fe5500"><?php echo lang(key: "admin_fire_user"); ?></a>
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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="card-title fw-bold m-0" style="font-size: 1.4rem;">
                                <?php echo lang(key: "admin_fire_user"); ?>
                            </h2>
                            <a href="?route=modules/users/create_user" class="btn btn-orange btn-sm">
                                <i class="fas fa-plus me-1"></i><?php echo lang("user_new_user"); ?>
                            </a>
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
                                <label style="font-size: 0.9rem; font-weight: 600;"><?php echo lang("user_search_kiosk_id"); ?></label>
                                <div class="input-group">
                                    <input type="text"
                                        id="kioskSearchInput"
                                        class="form-control square-filter"
                                        placeholder="<?php echo lang('user_search_kiosk_placeholder'); ?>"
                                        aria-label="Search users by Kiosk ID">
                                    <button class="btn btn-orange"
                                        type="button"
                                        id="kioskSearchButton"
                                        style="background-color: #FE5500 !important;"
                                        title="Search by Kiosk ID">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-2.5 mb-3 px-0">
                                <label style="font-size: 0.9rem; font-weight: 600;"><?php echo lang("user_search_phone"); ?></label>
                                <div class="input-group">
                                    <input type="text"
                                        id="phoneSearchInput"
                                        class="form-control square-filter"
                                        placeholder="<?php echo lang('user_search_phone_placeholder'); ?>"
                                        aria-label="Search users by phone number">
                                    <button class="btn btn-orange"
                                        type="button"
                                        id="phoneSearchButton"
                                        style="background-color: #FE5500 !important;"
                                        title="Search by phone">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-2.5 mb-3 px-0">
                                <label style="font-size: 0.9rem; font-weight: 600;"><?php echo lang("user_search_by_name"); ?></label>
                                <div class="input-group">
                                    <input type="text"
                                        id="nameSearchInput"
                                        class="form-control square-filter"
                                        placeholder="<?php echo lang('user_search_name_placeholder'); ?>"
                                        aria-label="Search users by name">
                                    <button class="btn btn-orange"
                                        type="button"
                                        id="nameSearchButton"
                                        style="background-color: #FE5500 !important;"
                                        title="Search by name">
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
                                                    <?php if (!empty($user['picture'])): ?>
                                                        <img src="<?= htmlspecialchars($user['picture']) ?>"
                                                            alt="Profile Picture"
                                                            style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
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
                                                    <span class="badge bg-<?= $user['status'] == 'active' ? 'success' : 'danger' ?>">
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
                                                        <a href="?route=modules/users/view&id=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm rounded-3 px-2 action-btn"
                                                            style="background: #FE5505; color: white; font-size: 0.75rem;"
                                                            title="<?php echo lang("user_view"); ?>">
                                                            <i class="fas fa-eye"></i>
                                                            <span
                                                                class="action-text"><?php echo lang("user_view"); ?></span>
                                                        </a>
                                                        <a href="?route=modules/users/edituser&id=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm rounded-3 px-2 action-btn"
                                                            style="background: #FE5505; color: white; font-size: 0.75rem;"
                                                            title="<?php echo lang("user_edit"); ?>">
                                                            <i class="fas fa-edit"></i>
                                                            <span
                                                                class="action-text"><?php echo lang("user_edit"); ?></span>
                                                        </a>
                                                        <a href="?route=modules/users/view_users&delete_user_id=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm btn-danger rounded-3 delete-btn px-2 action-btn"
                                                            style="font-size: 0.75rem;"
                                                            title="<?php echo lang("user_delete"); ?>">
                                                            <i class="fas fa-trash"></i>
                                                            <span
                                                                class="action-text"><?php echo lang("user_delete"); ?></span>
                                                        </a>
                                                        <a href="?route=modules/users/view_users&toggle_status=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm rounded-3 px-2 action-btn"
                                                            style="background: <?= $user['status'] == 'active' ? '#dc3545' : '#28a745' ?>; color: white; font-size: 0.75rem;"
                                                            title="<?= $user['status'] == 'active' ? lang('user_suspend') : lang('user_activate') ?>">
                                                            <i class="fas <?= $user['status'] == 'active' ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                                            <span class="action-text"><?= $user['status'] == 'active' ? lang('user_suspend') : lang('user_activate') ?></span>
                                                        </a>
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
                                                            <div style="width: 50px; height: 50px; border-radius: 50%; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                                                <i class="fas fa-user" style="color: #999; font-size: 20px;"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div style="flex-grow: 1;">
                                                            <h5 class="card-title mb-0"><?= htmlspecialchars($user['first_name'] ?? '') ?> <?= htmlspecialchars($user['last_name'] ?? '') ?></h5>
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
                                                        <!-- In the mobile card view -->
                                                        <div class="detail-item">
                                                            <span class="detail-label"><?php echo lang("user_status"); ?>:</span>
                                                            <span class="badge bg-<?= $user['status'] == 'active' ? 'success' : 'danger' ?>">
                                                                <?= ucfirst($user['status']) ?>
                                                            </span>
                                                        </div>

                                                        <div class="detail-item">
                                                            <span class="detail-label"><?php echo lang("user_last_login"); ?>:</span>
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
                                                        <a href="?route=modules/users/view&id=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm rounded-3 px-2 action-btn"
                                                            style="background: #FE5505; color: white; font-size: 0.75rem;"
                                                            title="<?php echo lang("user_view"); ?>">
                                                            <i class="fas fa-eye"></i>
                                                            <span
                                                                class="d-md-none ms-1"><?php echo lang("user_view"); ?></span>
                                                        </a>
                                                        <a href="?route=modules/users/edituser&id=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm rounded-3 px-2 action-btn"
                                                            style="background: #FE5505; color: white; font-size: 0.75rem;"
                                                            title="<?php echo lang("user_edit"); ?>">
                                                            <i class="fas fa-edit"></i>
                                                            <span
                                                                class="d-md-none ms-1"><?php echo lang("user_edit"); ?></span>
                                                        </a>
                                                        <a href="?route=modules/users/view_users&delete_user_id=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm btn-danger rounded-3 delete-btn px-2 action-btn"
                                                            style="font-size: 0.75rem;"
                                                            title="<?php echo lang("user_delete"); ?>">
                                                            <i class="fas fa-trash"></i>
                                                            <span
                                                                class="d-md-none ms-1"><?php echo lang("user_delete"); ?></span>
                                                        </a>
                                                        <!-- And in the action buttons -->
                                                        <a href="?route=modules/users/view_users&toggle_status=<?= $user['user_id'] ?>"
                                                            class="btn btn-sm rounded-3 px-2 action-btn"
                                                            style="background: <?= $user['status'] == 'active' ? '#dc3545' : '#28a745' ?>; color: white; font-size: 0.75rem;"
                                                            title="<?= $user['status'] == 'active' ? lang('user_suspend') : lang('user_activate') ?>">
                                                            <i class="fas <?= $user['status'] == 'active' ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                                            <span class="d-md-none ms-1"><?= $user['status'] == 'active' ? lang('user_suspend') : lang('user_activate') ?></span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <nav aria-label="User cards pagination">
                                    <ul class="pagination" id="cards-pagination">
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .badge.bg-success {
        background-color: #28a745 !important;
    }

    .badge.bg-danger {
        background-color: #dc3545 !important;
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
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
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
            initComplete: function() {
                $('#roleFilter').on('change', function() {
                    const selected = $(this).val();
                    const roleColIndex = table.column(':contains("<?php echo lang("user_role"); ?>")').index();
                    if (roleColIndex !== undefined) {
                        table.column(roleColIndex).search('^' + selected + '$', true, false).draw();
                    }
                });
            }
        });

        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });

        $('#searchButton').on('click', function() {
            table.search($('#searchInput').val()).draw();
        });

        $('#kioskSearchInput').on('keyup', function(e) {
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

        $('#phoneSearchInput').on('keyup', function(e) {
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

        $('#nameSearchInput').on('keyup', function(e) {
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

            $('.page-size').on('click', function(e) {
                e.preventDefault();
                const size = $(this).data('size');
                cardsPerPage = parseInt(size);
                $('#pageSizeDropdown').text(`${size} per page`);
                totalPages = Math.ceil(totalCards / cardsPerPage);
                currentPage = 1;
                showCards();
                updatePaginationUI();
            });

            $(document).on('click', '#prev-page', function(e) {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    showCards();
                    updatePaginationUI();
                }
            });

            $(document).on('click', '#next-page', function(e) {
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

        $(window).resize(function() {
            if ($(window).width() < 768 && $('#cards-pagination').children().length === 0) {
                initCardPagination();
            }
        });

        $(document).on('click', '.delete-btn', function(e) {
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

    $(document).on('click', '[href*="toggle_status"]', function(e) {
        e.preventDefault();
        const toggleUrl = $(this).attr('href');
        const isActivating = $(this).find('i').hasClass('fa-user-check');

        Swal.fire({
            title: '<?php echo lang("user_status_confirmation_title"); ?>',
            text: isActivating ? '<?php echo lang("user_activate_confirmation_text"); ?>' : '<?php echo lang("user_suspend_confirmation_text"); ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FE5500',
            cancelButtonColor: '#d33',
            confirmButtonText: isActivating ? '<?php echo lang("user_activate"); ?>' : '<?php echo lang("user_suspend"); ?>',
            cancelButtonText: '<?php echo lang("user_delete_cancel_button"); ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = toggleUrl;
            }
            return false; // Add this line to ensure no further execution
        });
        return false; // Prevent default behavior
    });
</script>