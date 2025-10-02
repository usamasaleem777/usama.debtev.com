<?php
// Handle delete action from URL parameters
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        // Delete position from database using the ID from URL
        DB::delete('positions', "id=%i", $_GET['id']);
        $success = "Position deleted successfully!";
    } catch (MeekroDBException $e) {
        // Catch and display any database errors
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch all positions from database, ordered by creation date (newest first)
$positions = DB::query("SELECT * FROM positions ORDER BY created_at DESC");

// Get unique position names for the filter dropdown
$positionNames = array_unique(array_column($positions, 'position_name'));
sort($positionNames); // Sort alphabetically

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
                        <!-- Position breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="#" style="color: #fe5500"><?php echo lang(key: "position_positions"); ?></a>
                        </li>
                        <!-- View position breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="#" style="color: #fe5500"><?php echo lang("position_view_position"); ?></a>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- PAGE-HEADER END -->

            <!-- Display success/error messages if they exist -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success rounded-4"><?= $success ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger rounded-4"><?= $error ?></div>
            <?php endif; ?>

            <!-- POSITIONS TABLE SECTION -->
            <div class="row1">
                <div class="row mt-4 rounded-4">
                    <div class="col-12">
                        <div class="card rounded-4">
                            <div class="card-body">
                                <!-- Card header with title and create button - Reordered for mobile -->
                                <div class="d-flex justify-content-between align-items-center flex-column flex-md-row">
                                    <a href="?route=modules/position/create_position"
                                        class="btn btn-orange btn-sm px-2 mobile-btn mb-2 mb-md-0 order-md-2"
                                        style="font-size:14px;">
                                        <i class="fa fa-plus me-2"></i><?php echo lang("position_create_new"); ?>
                                    </a>
                                    <h5 class="card-title fw-bold mb-3 mb-md-0 order-md-1 text-center text-md-start">
                                        <?php echo lang("position_position_list"); ?>
                                    </h5>
                                </div>

                                <!-- Position Filter Dropdown -->
                                <div class="col-md-8 mb-3">
                                    <select id="positionFilter" class="form-select square-filter">
                                        <option value=""><?php echo lang("position_all_positions"); ?></option>
                                        <?php foreach ($positionNames as $name): ?>
                                            <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Responsive Table -->
                                <div class="table-responsive">
                                    <table class="table align-middle table-hover" id="basic-datatable">
                                        <thead class="table-light">
                                            <tr>
                                                <th><?php echo lang(key: "position_id"); ?></th>
                                                <th><?php echo lang(key: "position_position_name"); ?></th>
                                                <th><?php echo lang(key: "position_status"); ?></th>
                                                <th><?php echo lang(key: "position_description"); ?></th>
                                                <th><?php echo lang(key: "position_created_at"); ?></th>
                                                <th class="text-center"><?php echo lang(key: "position_action"); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($positions as $position): ?>
                                                <tr>
                                                    <td class="align-middle">
                                                        <?= $position['id'] ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?= htmlspecialchars($position['position_name']) ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span
                                                            class="badge bg-<?= $position['status'] ? 'success' : 'danger' ?>">
                                                            <?= $position['status'] ? 'Active' : 'Inactive' ?>
                                                        </span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?= htmlspecialchars(substr($position['description'], 0, 50)) . (strlen($position['description']) > 50 ? '...' : '') ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?= date('M d, Y', strtotime($position['created_at'])) ?>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <!-- Edit Button -->
                                                            <a href="?route=modules/position/create_position&action=edit&id=<?= $position['id'] ?>"
                                                                class="btn btn-sm btn-action"
                                                                style="background: #FE5505; color: white;">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <!-- View Button -->
                                                            <button class="btn btn-sm btn-action view-details-btn"
                                                                data-bs-toggle="modal" data-bs-target="#viewDetailsModal"
                                                                data-id="<?= $position['id'] ?>">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <!-- Delete Button -->
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-sm btn-action btn-danger delete-position-btn"
                                                                data-id="<?= $position['id'] ?>">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VIEW DETAILS MODAL - Shows when view button is clicked -->
            <div class="modal fade" id="viewDetailsModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold"><?php echo lang(key: "position_position_details"); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Definition list for displaying position details -->
                            <dl class="row mb-0">
                                <dt class="col-sm-3"><?php echo lang(key: "position_id"); ?></dt>
                                <dd class="col-sm-9" id="detail-id"></dd>

                                <dt class="col-sm-3"><?php echo lang(key: "position_position_name"); ?></dt>
                                <dd class="col-sm-9" id="detail-position-name"></dd>

                                <dt class="col-sm-3"><?php echo lang(key: "position_status"); ?></dt>
                                <dd class="col-sm-9" id="detail-status"></dd>

                                <dt class="col-sm-3"><?php echo lang(key: "position_description"); ?></dt>
                                <dd class="col-sm-9" id="detail-description"></dd>

                                <dt class="col-sm-3"><?php echo lang(key: "position_created_at"); ?></dt>
                                <dd class="col-sm-9" id="detail-created"></dd>
                            </dl>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-3"
                                data-bs-dismiss="modal"><?php echo lang(key: "position_close"); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS STYLES -->
<style>
    /* Orange button styling (used for create button) */
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

    /* DataTable search input styling */
    #basic-datatable_filter input {
        border-radius: 20px !important;
        border: 1px solid #FE5505 !important;
        padding: 5px 15px !important;
        margin-bottom: 15px;
    }

    /* Table header styling */
    #basic-datatable th {
        background: #FE5505 !important;
        color: white !important;
        font-size: 0.85rem !important;
        padding: 0.75rem !important;
        text-align: center;
    }

    /* Table cell styling */
    #basic-datatable td {
        padding: 0.75rem !important;
        vertical-align: middle;
    }

    /* Active pagination button styling */
    .page-item.active .page-link {
        background: #FE5505 !important;
        border-color: #FE5505 !important;
    }

    /* View details button styling */
    .view-details-btn {
        border: 1px solid #FE5505 !important;
        color: #FE5505 !important;
        background: white !important;
    }

    .view-details-btn:hover {
        background: #FE5505 !important;
        color: white !important;
    }

    /* Position filter dropdown styling */
    #positionFilter {
        max-width: 300px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 8px 15px;
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
        #basic-datatable thead {
            display: none;
        }

        .card .rounded-4 {
            margin-right: -30px !important;
            border-radius: 0 !important;
            /* Add some padding */
        }

        .row1 {
            margin-top: -20px !important;
            margin-left: -30px !important;
            margin-right: -30px !important;
        }

        #basic-datatable tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        #basic-datatable td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem !important;
            border-bottom: 1px solid #f0f0f0;
        }

        #basic-datatable td:before {
            content: attr(data-label);
            font-weight: bold;
            margin-right: 1rem;
            color: #FE5505;
        }

        #basic-datatable td:last-child {
            border-bottom: none;
        }

        /* Action buttons */
        .btn-action {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.7rem !important;
        }

        /* Hide filter dropdown on very small screens */
        #positionFilter {
            max-width: 100%;
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
        #basic-datatable thead {
            display: none;
        }

        .card .rounded-4 {
            margin-right: -30px !important;
            border-radius: 0 !important;
            /* Add some padding */
        }

        .row1 {
            margin-top: -20px !important;
            margin-left: -30px !important;
            margin-right: -30px !important;
        }

        #basic-datatable tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        #basic-datatable td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem !important;
            border-bottom: 1px solid #f0f0f0;
        }

        #basic-datatable td:before {
            content: attr(data-label);
            font-weight: bold;
            margin-right: 1rem;
            color: #FE5505;
        }

        #basic-datatable td:last-child {
            border-bottom: none;
        }

        /* Action buttons */
        .btn-action {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.7rem !important;
        }

        /* Hide filter dropdown on very small screens */
        #positionFilter {
            max-width: 100%;
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
        #basic-datatable thead {
            display: none;
        }

        .card .rounded-4 {
            margin-right: -30px !important;
            border-radius: 0 !important;
            /* Add some padding */
        }

        .row1 {
            margin-top: -20px !important;
            margin-left: -30px !important;
            margin-right: -30px !important;
        }

        #basic-datatable tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        #basic-datatable td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem !important;
            border-bottom: 1px solid #f0f0f0;
        }

        #basic-datatable td:before {
            content: attr(data-label);
            font-weight: bold;
            margin-right: 1rem;
            color: #FE5505;
        }

        #basic-datatable td:last-child {
            border-bottom: none;
        }

        /* Action buttons */
        .btn-action {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.7rem !important;
        }

        /* Hide filter dropdown on very small screens */
        #positionFilter {
            max-width: 100%;
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

        /* Adjust filter width */
        #positionFilter {
            max-width: 250px;
        }
    }
</style>

<!-- JAVASCRIPT -->
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable with configuration
        const table = $('#basic-datatable').DataTable({
            searching: true,       // Enable search functionality
            paging: true,          // Enable pagination
            ordering: true,        // Enable column sorting
            info: false,           // Hide "Showing X of Y entries" info
            caseInsensitive: true, // Case-insensitive searching
            dom: 'rtip',          // Simple table controls layout
            lengthMenu: [],        // Disable entries per page dropdown
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search positions...", // Custom search placeholder
            },
            columnDefs: [
                {
                    orderable: false,  // Disable sorting for action column
                    targets: [5],
                    searchable: false  // Disable searching for action column
                }
            ],
            initComplete: function () {
                // Set up filter handler for position names dropdown
                $('#positionFilter').on('change', function () {
                    const selected = $(this).val();
                    if (selected) {
                        // Use regex for exact match filtering on position name column
                        table.column(1).search('^' + selected + '$', true, false).draw();
                    } else {
                        // Clear filter if "All Positions" is selected
                        table.column(1).search('').draw();
                    }
                });

                // Add data-label attributes for mobile view
                if ($(window).width() <= 360) {
                    $('#basic-datatable thead th').each(function (i) {
                        $('#basic-datatable tbody td:nth-child(' + (i + 1) + ')').attr('data-label', $(this).text());
                    });
                }
            }
        });

        // Handle delete button clicks with SweetAlert
        $(document).on('click', '.delete-position-btn', function () {
            const positionId = $(this).data('id');

            Swal.fire({
                title: "<?php echo lang('position_delete_confirm_title'); ?>",
                text: "<?php echo lang('position_delete_confirm_text'); ?>",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FE5505",
                cancelButtonColor: "#d33",
                confirmButtonText: "<?php echo lang('position_delete_confirm_button'); ?>",
                cancelButtonText: "<?php echo lang('position_delete_cancel_button'); ?>",
                customClass: {
                    confirmButton: 'swal-confirm-btn',
                    cancelButton: 'swal-cancel-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to delete URL
                    window.location.href = "?route=modules/position/view_position&action=delete&id=" + positionId;
                }
            });
        });

        // Modal handling - Populate with data when view button is clicked
        $('#viewDetailsModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');

            // Get position data from PHP array converted to JS object
            const position = <?= json_encode(array_column($positions, null, 'id')) ?>[id];

            // Populate modal fields with position data
            $('#detail-id').text(position.id);
            $('#detail-position-name').text(position.position_name);
            $('#detail-status').html('<span class="badge bg-' + (position.status ? 'success' : 'danger') + '">' +
                (position.status ? 'Active' : 'Inactive') + '</span>');
            $('#detail-description').text(position.description);

            // Format created date nicely
            $('#detail-created').text(new Date(position.created_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }));
        });

        // Handle window resize to adjust table layout
        function handleResponsiveView() {
            if ($(window).width() <= 360) {
                // Add data-label attributes for mobile view
                $('#basic-datatable thead th').each(function (i) {
                    $('#basic-datatable tbody td:nth-child(' + (i + 1) + ')').attr('data-label', $(this).text());
                });

                // Adjust action buttons
                $('.btn-action').css({
                    'width': '24px',
                    'height': '24px',
                    'font-size': '0.7rem'
                });
            } else {
                // Reset to normal table view
                $('#basic-datatable tbody td').removeAttr('data-label');
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