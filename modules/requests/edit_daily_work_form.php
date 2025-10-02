<?php
include 'includes/page-parts/header.php';

// Fetch edit requests with user details from users table
$requests = DB::query("
    SELECT dwr.id, u.first_name, u.last_name, u.email 
    FROM daily_work_reports dwr
    JOIN users u ON dwr.foreman_id = u.user_id
    WHERE dwr.is_locked = 1 AND dwr.request_edit = 1
");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = $_POST['report_id'] ?? 0;
    $action = $_POST['action'] ?? '';
    
    if ($report_id > 0 && in_array($action, ['accept', 'decline'])) {
        if ($action === 'accept') {
            // Accept the edit request
            DB::update('daily_work_reports', [
                'is_locked' => 0,
                'request_edit' => 0
            ], "id=%i", $report_id);
        } else {
            // Decline the edit request
            DB::update('daily_work_reports', [
                'request_edit' => 0
            ], "id=%i", $report_id);
        }
        
        // Redirect to prevent form resubmission
        echo '<script>window.location.href = "index.php?route=modules/requests/edit_daily_work_form";</script>';
        exit;
    }
}
?>

<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- HTML structure begins here -->
<div class="main-content app-content mt-0">
    <div class="side-app">
        <!-- CONTAINER -->
        <div class="main-container container-fluid">
            <!-- Page header with breadcrumb navigation -->
            <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
                <div style="margin-top: 15px;">
                    <ol class="breadcrumb float-sm-right mt-2">
                        <!-- Home breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="index.php" style="color: #fe5500"><i
                                    class="fas fa-home me-1"></i><?php echo lang("role_home"); ?></a>
                        </li>
                        <!-- Daily Report breadcrumb -->
                        <li class="breadcrumb-item active" style="color: #fe5500"><?php echo lang("daily_report_edit_requests"); ?></li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- DAILY REPORT EDIT REQUESTS SECTION -->
            <div class="row1">
                <div class="row mt-4 rounded-4">
                    <div class="col-12">
                        <div class="card rounded-4">
                            <div class="card-body">
                                <!-- Card header with title -->
                                <div class="d-flex justify-content-between align-items-center flex-column flex-md-row">
                                    <h5 class="card-title fw-bold mb-3 mb-md-0 order-md-1 text-center text-md-start">
                                        <i class="fas fa-file-alt me-2"></i><?php echo lang("daily_report_edit_requests"); ?>
                                    </h5>
                                </div>

                                <!-- Responsive Table -->
                                <div class="table-responsive">
                                    <?php if (count($requests) > 0): ?>
                                        <table class="table align-middle table-hover" id="daily-reports-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th><?php echo lang("first_name"); ?></th>
                                                    <th><?php echo lang("last_name"); ?></th>
                                                    <th><?php echo lang("email"); ?></th>
                                                    <th class="text-center"><?php echo lang("actions"); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($requests as $request): ?>
                                                    <tr>
                                                        <td class="align-middle">
                                                            <?= htmlspecialchars($request['first_name']) ?>
                                                        </td>
                                                        <td class="align-middle">
                                                            <?= htmlspecialchars($request['last_name']) ?>
                                                        </td>
                                                        <td class="align-middle">
                                                            <?= htmlspecialchars($request['email']) ?>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <div class="d-flex justify-content-center gap-2">
                                                                <!-- Accept Button -->
                                                                <form method="POST" class="d-inline">
                                                                    <input type="hidden" name="report_id" value="<?= $request['id'] ?>">
                                                                    <button type="submit" name="action" value="accept" class="btn btn-sm btn-action" style="background: #28a745; color: white;">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>
                                                                </form>
                                                                
                                                                <!-- Decline Button -->
                                                                <button class="btn btn-sm btn-action btn-danger decline-request-btn" 
                                                                    data-id="<?= $request['id'] ?>" 
                                                                    data-name="<?= htmlspecialchars($request['first_name'] . ' ' . htmlspecialchars($request['last_name'])) ?>">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <div class="alert alert-info rounded-4 mt-3">
                                            <?php echo lang("no_edit_requests"); ?>
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
</div>

<!-- CSS STYLES -->
<style>
    /* Orange button styling */
    .btn-orange {
        background-color: #FE5500;
        border-color: #FE5500;
        color: white;
    }

    .btn-orange:hover {
        background-color: #e04b00;
        border-color: #e04b00;
        color: white;
    }

    /* Table header styling */
    #daily-reports-table th {
        background: #FE5505 !important;
        color: white !important;
        font-size: 0.85rem !important;
        padding: 0.75rem !important;
        text-align: center;
    }

    /* Table cell styling */
    #daily-reports-table td {
        padding: 0.75rem !important;
        vertical-align: middle;
    }

    /* Active pagination button styling */
    .page-item.active .page-link {
        background: #FE5505 !important;
        border-color: #FE5505 !important;
    }

    /* Action buttons styling */
    .btn-action {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 4px;
    }

    /* For mobile view */
    @media screen and (max-width: 676px) {
        /* Make table cells stack vertically */
        #daily-reports-table thead {
            display: none;
        }

        .card .rounded-4 {
            margin-right: -30px !important;
            border-radius: 0 !important;
        }

        .row1 {
            margin-top: -20px !important;
            margin-left: -30px !important;
            margin-right: -30px !important;
        }

        #daily-reports-table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        #daily-reports-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem !important;
            border-bottom: 1px solid #f0f0f0;
        }

        #daily-reports-table td:before {
            content: attr(data-label);
            font-weight: bold;
            margin-right: 1rem;
            color: #FE5505;
        }

        #daily-reports-table td:last-child {
            border-bottom: none;
        }

        /* Action buttons */
        .btn-action {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.7rem !important;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-left: 0;
            margin-right: 0;
        }
    }

    /* Extra small devices (360px and below) */
    @media screen and (max-width: 360px) {
        /* Make table cells stack vertically */
        #daily-reports-table thead {
            display: none;
        }

        .card .rounded-4 {
            margin-right: -30px !important;
            border-radius: 0 !important;
        }

        .row1 {
            margin-top: -20px !important;
            margin-left: -30px !important;
            margin-right: -30px !important;
        }

        #daily-reports-table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        #daily-reports-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem !important;
            border-bottom: 1px solid #f0f0f0;
        }

        #daily-reports-table td:before {
            content: attr(data-label);
            font-weight: bold;
            margin-right: 1rem;
            color: #FE5505;
        }

        #daily-reports-table td:last-child {
            border-bottom: none;
        }

        /* Action buttons */
        .btn-action {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.7rem !important;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-left: 0;
            margin-right: 0;
        }
    }

    /* Extra small devices (430px and below) */
    @media screen and (max-width: 430px) {
        /* Make table cells stack vertically */
        #daily-reports-table thead {
            display: none;
        }

        .card .rounded-4 {
            margin-right: -30px !important;
            border-radius: 0 !important;
        }

        .row1 {
            margin-top: -20px !important;
            margin-left: -30px !important;
            margin-right: -30px !important;
        }

        #daily-reports-table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        #daily-reports-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem !important;
            border-bottom: 1px solid #f0f0f0;
        }

        #daily-reports-table td:before {
            content: attr(data-label);
            font-weight: bold;
            margin-right: 1rem;
            color: #FE5505;
        }

        #daily-reports-table td:last-child {
            border-bottom: none;
        }

        /* Action buttons */
        .btn-action {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.7rem !important;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-left: 0;
            margin-right: 0;
        }
    }

    /* Medium devices (tablets) adjustments */
    @media screen and (min-width: 768px) and (max-width: 991px) {
        /* Adjust table layout */
        #daily-reports-table {
            width: 100%;
        }
    }
</style>

<!-- JAVASCRIPT -->
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable if there are requests
        <?php if (count($requests) > 0): ?>
            const table = $('#daily-reports-table').DataTable({
                searching: true,
                paging: true,
                ordering: true,
                info: false,
                dom: 'rtip',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search reports...",
                },
                columnDefs: [
                    {
                        orderable: false,
                        targets: [3],
                        searchable: false
                    }
                ],
                initComplete: function () {
                    // Add data-label attributes for mobile view
                    if ($(window).width() <= 360) {
                        $('#daily-reports-table thead th').each(function (i) {
                            $('#daily-reports-table tbody td:nth-child(' + (i + 1) + ')').attr('data-label', $(this).text());
                        });
                    }
                }
            });
        <?php endif; ?>

        // Handle decline button clicks with SweetAlert
        $(document).on('click', '.decline-request-btn', function () {
            const requestId = $(this).data('id');
            const userName = $(this).data('name');

            Swal.fire({
                title: "Decline Edit Request?",
                html: `Are you sure you want to decline the edit request for <strong>${userName}</strong>'s daily report?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, Decline",
                cancelButtonText: "Cancel",
                customClass: {
                    confirmButton: 'swal-confirm-btn',
                    cancelButton: 'swal-cancel-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form to submit the decline action
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';
                    
                    const reportInput = document.createElement('input');
                    reportInput.type = 'hidden';
                    reportInput.name = 'report_id';
                    reportInput.value = requestId;
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'decline';
                    
                    form.appendChild(reportInput);
                    form.appendChild(actionInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Handle window resize to adjust table layout
        function handleResponsiveView() {
            if ($(window).width() <= 360) {
                // Add data-label attributes for mobile view
                $('#daily-reports-table thead th').each(function (i) {
                    $('#daily-reports-table tbody td:nth-child(' + (i + 1) + ')').attr('data-label', $(this).text());
                });

                // Adjust action buttons
                $('.btn-action').css({
                    'width': '24px',
                    'height': '24px',
                    'font-size': '0.7rem'
                });
            } else {
                // Reset to normal table view
                $('#daily-reports-table tbody td').removeAttr('data-label');
                $('.btn-action').css({
                    'width': '32px',
                    'height': '32px',
                    'font-size': 'inherit'
                });
            }
        }

        // Initial check
        handleResponsiveView();

        // Check on window resize
        $(window).resize(handleResponsiveView);
    });
</script>

<?php include 'includes/page-parts/footer.php'; ?>