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
/************** END SECURITY CHECK ***********************/

if (isset($_GET['toggle_status'])) {
    $user_id = intval($_GET['toggle_status']);
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    try {
        // Get current status
        $user = DB::queryFirstRow("SELECT status FROM users WHERE user_id=%i", $user_id);

        if ($user) {
            // Define status rotation
            $status_rotation = [
                'active' => ($action === 'fire') ? 'fire' : 'suspended', // If fired, go to inactive, else suspended
                'suspended' => 'fire',
                'fire' => 'active'
            ];

            $new_status = $status_rotation[$user['status']] ?? 'active';

            $result = DB::update('users', [
                'status' => $new_status,
                'updated_at' => date('Y-m-d H:i:s')
            ], "user_id=%i", $user_id);

            if ($result) {
                $message = ($action === 'fire') ? 
                    "User has been fired and status set to inactive." :
                    "User status updated to " . $new_status . " successfully.";
                
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => $message
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
        exit();
    } catch (MeekroDBException $e) {
        error_log("User status update error: " . $e->getMessage());
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => "Status update failed: " . $e->getMessage()
        ];
        echo '<script>window.location.href = "index.php?route=modules/users/view_users";</script>';
        exit();
    }
}
$roles = DB::query("SELECT DISTINCT name FROM roles ORDER BY name");

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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title fw-bold m-0" style="font-size: 1.2rem;">
                                <?php echo lang("user_user_list"); ?>
                            </h3>
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
                                        <!-- Data will be populated by DataTables via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-block d-md-none">
    <div id="users-cards-container">
        <div class="row" id="users-cards-row">
            <!-- Cards will be injected here via JS -->
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
        min-width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 0.5rem !important;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .action-text {
        display: none;
        margin-left: 4px;
    }
    
    .action-btn:hover {
        min-width: auto;
        padding: 0 0.5rem !important;
    }
    
    .action-btn:hover .action-text {
        display: inline;
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
$(document).ready(function () {
    // Initialize DataTable for desktop
    const table = $('#users-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'ajax_helpers/get_users.php',
            type: 'POST',
            data: function (d) {
                d.roleFilter   = $('#roleFilter').val();
                d.kioskFilter  = $('#kioskSearchInput').val();
                d.phoneFilter  = $('#phoneSearchInput').val();
                d.nameFilter   = $('#nameSearchInput').val();
            },
            dataSrc: function (json) {
                if ($(window).width() < 768) {
                    renderUserCards(json.data); // populate cards
                }
                return json.data; // keep DataTables working
            }
        },
        columns: [
            { data: 'picture' },
            { data: 'user_id' },
            { data: 'kioskID' },
            { data: 'user_name' },
            { data: 'first_name' },
            { data: 'last_name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'middle_initial' },
            { data: 'status' },
            { data: 'role_name' },
            { data: 'last_login' },
            { data: 'actions' }
        ],
        searching: true,
        paging: true,
        ordering: true,
        order: [[1, 'desc']],
        info: true,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
        pageLength: 25,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "<?php echo lang('user_search_placeholder'); ?>"
        }
    });

    // Apply filters on change
    $('#roleFilter, #kioskSearchInput, #phoneSearchInput, #nameSearchInput').on('change keyup', function () {
        table.ajax.reload();
    });

    // =========================
    // Render Cards (Mobile View)
    // =========================
    function renderUserCards(users) {
        const $row = $('#users-cards-row');
        $row.empty();

        users.forEach(user => {
            const card = `
            <div class="col-12 mb-3 user-card">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-2">
                           ${user.picture 
    ? user.picture 
    : `<div style="width:50px;height:50px;border-radius:50%;background-color:#f0f0f0;
                  display:flex;align-items:center;justify-content:center;margin-right:15px;">
           <i class="fas fa-user" style="color:#999;font-size:20px;"></i>
       </div>`
}

                            <div style="flex-grow:1;">
                                <h5 class="card-title mb-0">${user.first_name ?? ''} ${user.last_name ?? ''}</h5>
                                <span class="badge" style="background-color:#FE5500;">
                                    ${user.role_name ?? 'N/A'}
                                </span>
                            </div>
                        </div>
                        <div class="user-details">
                            <div><strong><?php echo lang("user_user_id"); ?>:</strong> ${user.user_id}</div>
                            <div><strong><?php echo lang("user_username"); ?>:</strong> ${user.user_name}</div>
                            <div><strong><?php echo lang("user_email"); ?>:</strong> ${user.email || 'N/A'}</div>
                            <div><strong><?php echo lang("user_phone"); ?>:</strong> ${user.phone || 'N/A'}</div>
                            <div><strong><?php echo lang("user_middle_initial"); ?>:</strong> ${user.middle_initial || 'N/A'}</div>
                            <div><strong><?php echo lang("user_status"); ?>:</strong> 
                                <span class="badge bg-${user.status === 'active' ? 'success' : 'danger'}">
                                    ${user.status}
                                </span>
                            </div>
                            <div><strong><?php echo lang("user_last_login"); ?>:</strong> 
                                ${user.last_login || 'Never logged in'}
                            </div>
                        </div>
                        <div class="d-flex gap-1 flex-wrap justify-content-center mt-2">
                            ${user.actions}
                        </div>
                    </div>
                </div>
            </div>`;
            $row.append(card);
        });

        // Reset and init mobile pagination
        $('#cards-pagination').empty();
        $('#pageSizeDropdown').parent().remove();
        if ($(window).width() < 768) {
            initCardPagination();
        }
    }

    // =========================
    // Mobile Card Pagination
    // =========================
    function initCardPagination() {
        const $cards = $('.user-card');
        const $pagination = $('#cards-pagination');
        const $row = $('#users-cards-row');

        let pageSize = parseInt(localStorage.getItem('cardsPageSize')) || 5;
        let currentPage = 1;
        const totalCards = $cards.length;
        const totalPages = Math.ceil(totalCards / pageSize);

        function showPage(page) {
            currentPage = page;
            $cards.hide();
            const start = (page - 1) * pageSize;
            const end = start + pageSize;
            $cards.slice(start, end).show();

            $pagination.find('.page-item').removeClass('active');
            $pagination.find(`[data-page="${page}"]`).addClass('active');
        }

        function updatePagination() {
            $pagination.empty();
            const prevDisabled = currentPage === 1 ? ' disabled' : '';
            const nextDisabled = currentPage === totalPages ? ' disabled' : '';

            $pagination.append(`<li class="page-item${prevDisabled}">
                <a class="page-link" href="#" id="prev-page">Previous</a></li>`);

            for (let i = 1; i <= totalPages; i++) {
                $pagination.append(`<li class="page-item ${i === currentPage ? 'active' : ''}" data-page="${i}">
                    <a class="page-link" href="#">${i}</a></li>`);
            }

            $pagination.append(`<li class="page-item${nextDisabled}">
                <a class="page-link" href="#" id="next-page">Next</a></li>`);
        }

        // Page size dropdown
        const dropdownHtml = `
            <div class="mb-2">
                <label for="pageSizeDropdown">Items per page:</label>
                <select id="pageSizeDropdown" class="form-select form-select-sm" style="width: auto; display: inline-block; margin-left: 5px;">
                    <option value="5" ${pageSize === 5 ? 'selected' : ''}>5</option>
                    <option value="10" ${pageSize === 10 ? 'selected' : ''}>10</option>
                    <option value="20" ${pageSize === 20 ? 'selected' : ''}>20</option>
                </select>
            </div>
        `;
        $row.before(dropdownHtml);

        // Bind events
        $pagination.off('click').on('click', '.page-link', function (e) {
            e.preventDefault();
            const $parent = $(this).parent();

            if ($parent.hasClass('disabled')) return;

            if (this.id === 'prev-page' && currentPage > 1) {
                showPage(currentPage - 1);
            } else if (this.id === 'next-page' && currentPage < totalPages) {
                showPage(currentPage + 1);
            } else if ($parent.data('page')) {
                showPage($parent.data('page'));
            }
            updatePagination();
        });

        $('#pageSizeDropdown').off('change').on('change', function () {
            pageSize = parseInt($(this).val());
            localStorage.setItem('cardsPageSize', pageSize);
            currentPage = 1;
            initCardPagination();
        });

        showPage(currentPage);
        updatePagination();
    }

    // =========================
    // Delete User
    // =========================
    $(document).on("click", ".delete-user", function (e) {
        e.preventDefault();
        const userId = $(this).data("id");

        Swal.fire({
            title: '<?php echo lang("delete_confirmation_title"); ?>',
            text: '<?php echo lang("delete_confirmation_text"); ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<?php echo lang("delete_confirmation_yes"); ?>',
            cancelButtonText: '<?php echo lang("delete_confirmation_no"); ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("ajax_helpers/delete_user.php", { id: userId }, function (response) {
                    if (response.success) {
                        Swal.fire('<?php echo lang("deleted"); ?>', '<?php echo lang("user_deleted_success"); ?>', 'success');
                        table.ajax.reload();
                    } else {
                        Swal.fire('<?php echo lang("error"); ?>', response.error, 'error');
                    }
                }, "json");
            }
        });
    });

    // =========================
    // Toggle User Status
    // =========================
    $(document).on("click", ".toggle-status", function (e) {
        e.preventDefault();
        const userId = $(this).data("id");
        const newStatus = $(this).data("status");

        $.post("ajax_helpers/toggle_status.php", { id: userId, status: newStatus }, function (response) {
            if (response.success) {
                Swal.fire('<?php echo lang("updated"); ?>', '<?php echo lang("status_updated_success"); ?>', 'success');
                table.ajax.reload();
            } else {
                Swal.fire('<?php echo lang("error"); ?>', response.error, 'error');
            }
        }, "json");
    });
});

</script>
