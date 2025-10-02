<?php
$applicant_count = DB::queryFirstField("SELECT COUNT(*) FROM applicants");

// Get user statistics
$totalUsers = DB::queryFirstField("SELECT COUNT(*) FROM users");
$newThisMonth = DB::queryFirstField("SELECT COUNT(*) FROM users WHERE MONTH(created_at) = MONTH(CURRENT_DATE())");
// $activeUsers = DB::queryFirstField("SELECT COUNT(*) FROM users WHERE last_login > DATE_SUB(NOW(), INTERVAL 7 DAY)");

$totalPositions = DB::queryFirstField("SELECT COUNT(*) FROM positions");

$totalApplicants = DB::queryFirstField("SELECT COUNT(*) FROM applicants");

// Fetch latest 7 users (adjust limit as needed)
$newUsers = DB::query("
    SELECT user_id, name, user_name, picture, created_at 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT 7
");

if ($_SESSION['lang'] === 'es') {
    $newApplicants = DB::query("
    SELECT applicants.*, p.position_name_es as position_name
    FROM applicants 
    LEFT JOIN positions p ON applicants.position = p.id
    ORDER BY created_at DESC
    LIMIT 7
    ");
} else {
    $newApplicants = DB::query("
    SELECT applicants.*, p.position_name
    FROM applicants 
    LEFT JOIN positions p ON applicants.position = p.id
    ORDER BY created_at DESC
    LIMIT 7
    ");
}

// Get recent logins for the activity card
$recentLogins = DB::query("
    SELECT u.user_id, u.name, u.email, u.kioskID as password, u.last_login, u.picture 
    FROM users u
    WHERE u.last_login IS NOT NULL
    ORDER BY u.last_login DESC
    LIMIT 7
");

?>

<style>
    #basic-datatable_filter input {
        border-radius: 20px !important;
        border: 1px solid #FE5505 !important;
        padding: 5px 15px !important;
        margin-bottom: 15px;
    }

    #basic-datatable th {
        background: #FE5505 !important;
        color: white !important;
    }

    #basic-datatable td {
        vertical-align: middle;
    }

    .page-item.active .page-link {
        background: #FE5505 !important;
        border-color: #FE5505 !important;
    }

    /* Compact Dashboard Styles */
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
        margin-top: 10px;
    }

    .text-indigo {
        color: #4b49ac;
    }

    .text-orange {
        color: #FE5500;
    }

    .bg-orange-50 {
        background-color: rgba(254, 85, 0, 0.1);
    }

    .btn-orange {
        background: linear-gradient(45deg, #FE5505, #FF8E53);
        color: white;
        border: none;
        font-size: 0.8875rem;
    }

    .btn-outline-orange {
        border-color: #FE5500;
        color: #FE5500;
        font-size: 0.6875rem;
    }

    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.625rem;
        line-height: 1.2;
    }

    .small {
        font-size: 0.8125rem;
    }

    .x-small {
        font-size: 0.75rem;
    }

    .xx-small {
        font-size: 0.625rem;
    }

    .user-list,
    .activity-list {
        max-height: 300px;
        overflow-y: auto;
    }

    h6 {
        color: #FE5500;
    }

    .rounded-3 {
        border-radius: 12px !important;
    }

    .rounded-4 {
        border-radius: 8px !important;
    }

    .badge {
        padding: 0.25em 0.5em;
        font-size: 0.625rem;
        font-weight: 500;
    }

    /* Icon Sizing */
    .card-icon-sm {
        font-size: 1.25rem !important;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-icon-xs {
        font-size: 1rem !important;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Table adjustments */
    .table th,
    .table td {
        padding: 0.5rem 0.75rem;
    }

    /* Activity item spacing */
    .activity-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    /* Custom scrollbar styling */
    .table-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #FE5500;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #d94600;
    }

    /* Clickable row styling */
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .clickable-row:hover {
        background-color: rgba(254, 85, 0, 0.1);
    }

    /* Status badge colors */
    .badge-pending {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-approved {
        background-color: #28a745;
        color: white;
    }

    .badge-rejected {
        background-color: #dc3545;
        color: white;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 767.98px) {
        .page-breadcrumb {
            padding-top: 10px;
            padding-bottom: 5px;
        }

        .breadcrumb-title,
        .breadcrumb-item {
            font-size: 0.75rem;
        }

        .card-body {
            padding: 0.75rem;
        }

        .col1 {
            margin-top: -20px !important;
        }

        .card-icon-sm,
        .card-icon-xs {
            width: 30px;
            height: 30px;
            font-size: 1rem !important;
        }

        h4 {
            font-size: 1.25rem;
        }

        .btn-orange,
        .btn-outline-orange {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .table th,
        .table td {
            padding: 0.3rem 0.5rem;
            font-size: 0.75rem;
        }

        .user-list,
        .activity-list {
            max-height: 250px;
        }

        .row-cols-md-2>* {
            flex: 0 0 auto;
            width: 50%;
        }

        .row-cols-xl-4>* {
            flex: 0 0 auto;
            width: 50%;
        }
    }

    @media (max-width: 360px) {

        .row-cols-md-2>*,
        .row-cols-xl-4>* {
            flex: 0 0 auto;
            width: 100%;
        }

        .breadcrumb-title,
        .breadcrumb-item {
            margin-top: 20px;
        }

        .col1 {
            margin-top: -20px !important;
        }

        .card-body {
            padding: 0.5rem;
        }

        h4 {
            font-size: 1.1rem;
        }

        .btn-orange {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }

        .table th,
        .table td {
            font-size: 0.7rem;
        }

        .small {
            font-size: 0.75rem;
        }

        .x-small {
            font-size: 0.7rem;
        }

        .xx-small {
            font-size: 0.6rem;
        }
    }

    @media (max-width: 430px) {

        .row-cols-md-2>*,
        .row-cols-xl-4>* {
            flex: 0 0 auto;
            width: 100%;
        }

        .breadcrumb-title,
        .breadcrumb-item {
            margin-top: 20px;
        }

        .col1 {
            margin-top: -20px !important;
        }

        .card-body {
            padding: 0.5rem;
        }

        h4 {
            font-size: 1.1rem;
        }

        .btn-orange {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }

        .table th,
        .table td {
            font-size: 0.7rem;
        }

        .small {
            font-size: 0.75rem;
        }

        .x-small {
            font-size: 0.7rem;
        }

        .xx-small {
            font-size: 0.6rem;
        }
    }
</style>

<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center py-2">
    <div class="breadcrumb-title pe-3 small" style="color: #FE5500; margin-top: 10px; margin-bottom: 5px;">
        <?php echo lang("dashboard"); ?>
    </div>
    <div class="ps-3" style="margin-top: 10px; margin-bottom: 5px;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0 small">
                <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active small" aria-current="page"><?php echo lang("admin_Admin"); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-3">
    <!-- Applicants Card -->
    <div class="col">
        <div class="card rounded-3 h-80">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h6 class="mb-0 small"><?php echo lang("admin_Applicants"); ?> <span
                            class="fw-500"><?php echo lang("admin_Details"); ?></span></h6>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-2 text-indigo"><?php echo htmlspecialchars($applicant_count); ?></h4>
                        <a href="index.php?route=modules/applicants/list_applicants"
                            class="btn btn-orange rounded-4 px-3 btn-sm small">
                            <i class="fas fa-eye me-1"></i> <?php echo lang("admin_View_Details"); ?>
                        </a>
                    </div>
                    <div class="bg-orange-50 rounded-2 card-icon-sm">
                        <span class="material-icons-outlined text-orange">business_center</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Positions Card -->
    <div class="col1">
        <div class="col">
            <div class="card rounded-3 h-80">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h6 class="mb-0 small"><?php echo lang("admin_position"); ?> <span
                                class="fw-500"><?php echo lang("admin_Details"); ?></span></h6>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-2 text-indigo"><?php echo $totalPositions; ?></h4>
                            <a href="index.php?route=modules/position/view_position"
                                class="btn btn-orange rounded-4 px-3 btn-sm small">
                                <i class="fas fa-eye me-1 "></i> <?php echo lang("admin_view_details"); ?>
                            </a>
                        </div>
                        <div class="bg-orange-50 rounded-2 card-icon-sm">
                            <span class="material-icons-outlined text-orange">menu_book</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Users Card -->
    <div class="col1">
        <div class="col">
            <div class="card rounded-3 h-80">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h6 class="mb-0 small"><?php echo lang("admin_total"); ?> <span
                                class="fw-500"><?php echo lang("admin_user"); ?></span></h6>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-2 text-indigo"><?= $totalUsers ?></h4>
                            <a href="index.php?route=modules/users/view_users"
                                class="btn btn-orange rounded-4 px-3 btn-sm small">
                                <i class="fas fa-eye me-1"></i> <?php echo lang("admin_view_all"); ?>
                            </a>
                        </div>
                        <div class="bg-orange-50 rounded-2 card-icon-sm">
                            <span class="material-icons-outlined text-orange">people_alt</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- New This Month Card -->
    <div class="col1">
        <div class="col">
            <div class="card rounded-3 h-80">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h6 class="mb-0 small"><?php echo lang("admin_user"); ?> <span
                                class="fw-500"><?php echo lang("admin_this_month"); ?></span></h6>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-2 text-indigo"><?= $newThisMonth ?></h4>
                            <a href="index.php?route=modules/users/view_users"
                                class="btn btn-orange rounded-4 px-3 btn-sm small">
                                <i class="fas fa-eye me-1"></i><?php echo lang("admin_view_new"); ?>
                            </a>
                        </div>
                        <div class="bg-orange-50 rounded-2 card-icon-sm">
                            <span class="material-icons-outlined text-orange">group_add</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Second Row - Compact Layout -->
<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card rounded-3 h-100">
            <div class="card-header border-0 p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <h6 class="mb-0 small"><?php echo lang("admin_new_applicant"); ?></h6>
                    <div class="dropdown">
                        <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                            data-bs-toggle="dropdown">
                            <span class="material-icons-outlined">more_vert</span>
                        </a>
                        <ul class="dropdown-menu small">
                            <li><a class="dropdown-item small" href="#"><?php echo lang("admin_view_all_user"); ?></a>
                            </li>
                            <li><a class="dropdown-item small" href="#"><?php echo lang("admin_add_new_user"); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="user-list p-2">
                    <div class="table-responsive">
                        <div class="table-container" style="overflow: auto; max-height: 500px;">
                            <table class="table align-middle mb-0 small">
                                <thead class="table-light small">
                                    <tr>
                                        <th class="small" style="background-color: #FE5500; color: white;">
                                            <?php echo lang("admin_first_name"); ?>
                                        </th>
                                        <th class="small" style="background-color: #FE5500; color: white;">
                                            <?php echo lang("admin_last_name"); ?>
                                        </th>
                                        <th class="small" style="background-color: #FE5500; color: white;">
                                            <?php echo lang("admin_position"); ?>
                                        </th>
                                        <th class="small" style="background-color: #FE5500; color: white;">
                                            State
                                        </th>
                                        <th class="small" style="background-color: #FE5500; color: white;">
                                            Status
                                        </th>
                                        <th class="small" style="background-color: #FE5500; color: white;">
                                            Submitted
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get most recent applicants ordered by created_at
                                    if ($_SESSION['lang'] === 'es') {
                                        $newApplicants = DB::query("
                                        SELECT applicants.*, p.position_name_es as position_name
                                        FROM applicants 
                                        LEFT JOIN positions p ON applicants.position = p.id
                                        ORDER BY created_at DESC
                                        LIMIT 7
                                        ");
                                    } else {
                                        $newApplicants = DB::query("
                                        SELECT applicants.*, p.position_name
                                        FROM applicants 
                                        LEFT JOIN positions p ON applicants.position = p.id
                                        ORDER BY created_at DESC
                                        LIMIT 7
                                        ");
                                    }

                                    foreach ($newApplicants as $applicant): ?>
                                        <tr class="clickable-row" onclick="window.location='index.php?route=modules/applicants/view_applicant_new&id=<?= $applicant['id'] ?>'">
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div>
                                                        <div class="small fw-500">
                                                            <?php if (isset($applicant['first_name'])) {
                                                                echo htmlspecialchars($applicant['first_name']);
                                                            } else {
                                                                echo 'N/A';
                                                            } ?>
                                                        </div>
                                                        <div class="text-muted x-small">
                                                            <?php if (isset($applicant['email'])) {
                                                                echo htmlspecialchars($applicant['email']);
                                                            } else {
                                                                echo 'N/A';
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="small">
                                                <?php if (isset($applicant['last_name'])) {
                                                    echo htmlspecialchars($applicant['last_name']);
                                                } else {
                                                    echo 'N/A';
                                                } ?>
                                            </td>
                                            <td>
                                                <span>
                                                    <?php if (isset($applicant['position_name'])) {
                                                        echo htmlspecialchars($applicant['position_name']);
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </span>
                                            </td>
                                            <td class="small">
                                                <?php if (isset($applicant['state'])) {
                                                    echo htmlspecialchars($applicant['state']);
                                                } else {
                                                    echo 'N/A';
                                                } ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status = isset($applicant['status']) ? strtolower($applicant['status']) : 'pending';
                                                $statusClass = 'badge-pending';
                                                if ($status === 'hired') {
                                                    $statusClass = 'badge-approved';
                                                } elseif ($status === 'rejected') {
                                                    $statusClass = 'badge-rejected';
                                                } elseif ($status === 'applied') {
                                                    $statusClass = 'badge-pending';
                                                } elseif ($status === 'packet_send') {
                                                    $statusClass = 'badge-pending';
                                                } elseif ($status === 'testing') {
                                                    $statusClass = 'badge-pending';
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $status)) ?>
                                                </span>
                                            </td>
                                            <td class="small">
                                                <?php if (isset($applicant['created_at'])) {
                                                    echo date('M d, Y h:i A', strtotime($applicant['created_at']));
                                                } else {
                                                    echo 'N/A';
                                                } ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent p-2">
                <div class="d-flex align-items-center justify-content-between small">
                    <span class="text-muted"><?php echo lang("admin_showing"); ?> <?= count($newApplicants) ?> of
                        <?= $totalApplicants ?> <?php echo lang("admin_users"); ?></span>
                    <a href="index.php?route=modules/applicants/list_applicants" class="btn btn-orange btn-xs py-1 px-2">
                        <i class="fas fa-plus me-1"></i> <?php echo lang("admin_view_applicant"); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logins Card -->
    <div class="col-12 col-lg-4">
        <div class="card rounded-3 h-100">
            <div class="card-header border-0 p-3">
                <h6 class="mb-0 small">Recent Logins</h6>
            </div>
            <div class="card-body p-0">
                <div class="activity-list p-2">
                    <div class="d-flex flex-column gap-1">
                        <?php foreach ($recentLogins as $login):
                            $timeAgo = timeAgo($login['last_login']);
                        ?>
                            <div class="d-flex gap-2 align-items-start activity-item clickable-row"
                                data-email="<?= htmlspecialchars($login['email']) ?>"
                                data-password="<?= htmlspecialchars($login['password']) ?>">
                                <div class="bg-orange-50 rounded-2 card-icon-xs">
                                    <?php if (!empty($login['picture'])): ?>
                                        <img src="<?= $login['picture'] ?>" class="rounded-circle" width="35" height="35" alt="User">
                                    <?php else: ?>
                                        <span class="material-icons-outlined text-orange">person</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small fw-500">
                                        <?= htmlspecialchars($login['name']) ?>
                                    </div>
                                    <div class="text-muted xx-small">
                                        Last login: <?= $timeAgo ?>
                                    </div>
                                </div>
                                <div class="xx-small text-muted">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent p-2">
                <a href="index.php?route=modules/users/view_users"
                    class="btn btn-outline-orange btn-sm w-100 py-1 small">
                    View All Users
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Simple DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#basic-datatable').DataTable({
            searching: true,
            paging: true,
            ordering: true,
            info: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search users...",
            },
            columnDefs: [{
                orderable: false,
                targets: [7]
            }]
        });
    });

    $(document).ready(function() {
        // Handle click on user rows - only for Recent Logins card
        $('.activity-list .clickable-row').on('click', function() {
            const email = $(this).data('email');

            Swal.fire({
                title: 'Switch User Confirmation',
                text: 'Are you sure you want to switch to this user?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, switch user',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading indicator
                    Swal.fire({
                        title: 'Switching User',
                        html: 'Please wait while we switch your account...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Use AJAX to switch user
                    $.ajax({
                        type: 'POST',
                        url: 'ajax_helpers/switch_user.php',
                        data: {
                            email: email
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Success',
                                    text: response.message || 'User switched successfully',
                                    icon: 'success'
                                }).then(() => {

                                    if (response.role === 'admin') {
                                        window.location.href = 'index.php';
                                    } else if (response.role === 'craftsman') {
                                        window.location.href = 'index.php';
                                    } else {
                                        window.location.href = 'index.php';
                                    }
                                });
                            } else {
                                Swal.fire('Error', response.message || 'Failed to switch user', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error', 'An error occurred while switching users: ' + error, 'error');
                        }
                    });
                }
            });
        });

        // Keep hover effect for all clickable rows
        $('.clickable-row').hover(
            function() {
                $(this).css('background-color', '#f8f9fa');
                $(this).css('cursor', 'pointer');
            },
            function() {
                $(this).css('background-color', '');
            }
        );
    });


    var options = {
        series: [],
        chart: {
            height: 290,
            type: 'donut',
        },
        legend: {
            position: 'bottom',
            show: !1
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                gradientToColors: ['#ee0979', '#17ad37', '#ec6ead'],
                shadeIntensity: 1,
                type: 'vertical',
                opacityFrom: 1,
                opacityTo: 1,
            },
        },
        colors: ["#ff6a00", "#98ec2d", "#3494e6"],
        dataLabels: {
            enabled: !1
        },
        plotOptions: {
            pie: {
                donut: {
                    size: "85%"
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 270
                },
                legend: {
                    position: 'bottom',
                    show: !1
                }
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#chart6"), options);
    chart.render();
</script>