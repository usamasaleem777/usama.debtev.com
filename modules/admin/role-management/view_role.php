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


?>
<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- SweetAlert CSS for beautiful alert popups -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<div class="row w-100 mx-0">
    <div class="col-xl-12">
        <!-- Page header with breadcrumb navigation -->
        <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
            <div>
                <ol class="breadcrumb float-sm-right mt-2">
                    <!-- Home breadcrumb -->
                    <li class="breadcrumb-item">
                        <a href="index.php" style="color: #fe5500"><i class="fas fa-home me-1"></i><?php echo lang("role_home"); ?></a>
                    </li>
                    <!-- View Roles breadcrumb -->
                    <li class="breadcrumb-item">
                        <a href="index.php?route=modules/admin/role-management/view_role" style="color: #fe5500"><?php echo lang("role_view_role"); ?></a>
                    </li>
                </ol>
            </div>
        </div>
<div class="row1">
        <!-- Main card with orange top border -->
        <div class="card border-top" style="border-color: #FE5500 !important;">
            <div class="card-body p-4">
                <!-- Error message display (if any) -->
                <?php if (isset($_SESSION['error'])) { ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                    </div>
                <?php } ?>

                <!-- Success message display (if any) -->
                <?php if (isset($_SESSION['success'])) { ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                    </div>
                <?php } ?>

                <!-- Table header section -->
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <h5 class="mb-0 fw-bold" style="color: #FE5500;"><?php echo lang("role_all_role"); ?></h5>
                    </div>
                </div>

                <!-- Roles table -->
                 
                <div class="table-responsive">
                    <table class="table table-striped table-hover border text-nowrap mb-0 datatable mt-3 w-100" id="applicants_table">
                        <thead>
                            <tr>
                                <th class="text-center"  style="background-color: #FE5500; color: white;"><?php echo lang("role_Sr"); ?></th> <!-- Serial number -->
                                <th  style="background-color: #FE5500; color: white;"><?php echo lang("role_Title"); ?></th> <!-- Role title -->
                                <th class="text-center"  style="background-color: #FE5500; color: white;"><?php echo lang("role_action"); ?></th> <!-- Action buttons -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                // Fetch all roles from database
                                $roles = DB::query("SELECT * FROM roles");
                                
                                if (!empty($roles)) {
                                    // Loop through each role and display in table row
                                    foreach ($roles as $itr => $role) {
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo ($itr + 1); ?></td> <!-- Row number -->
                                            <td><?php echo htmlspecialchars($role['name']); ?></td> <!-- Role name -->
                                            <td class="text-center">
                                                <!-- Edit button - links to edit page with role ID and name -->
                                                <a href="index.php?route=modules/admin/role-management/add_role&role_id=<?php echo $role['id']; ?>&role_title=<?php echo urlencode($role['name']); ?>"
                                                    class="btn edit_btn" title="<?php echo lang("role_edit"); ?>"
                                                    style="background-color: #FE5500; color: white; border: none; padding: 8px 16px; border-radius: 5px; transition: background 0.3s;"
                                                    onmouseover="this.style.backgroundColor='#E04A00'"
                                                    onmouseout="this.style.backgroundColor='#FE5500'">
                                                    <i class="fas fa-edit me-1"></i> <?php echo lang("role_edit"); ?>
                                                </a>

                                                <!-- Delete button - triggers confirmation dialog -->
                                                <button type="button" onclick="confirmDeleteRole(<?php echo $role['id']; ?>)" class="btn btn-danger ms-2"
                                                    title="<?php echo lang("role_delete"); ?>"
                                                    style="background-color: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 5px; transition: background 0.3s;"
                                                    onmouseover="this.style.backgroundColor='#c82333'"
                                                    onmouseout="this.style.backgroundColor='#dc3545'">
                                                    <i class="fas fa-trash-alt me-1"></i> <?php echo lang("role_delete"); ?>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    // Display message if no roles found
                                    ?>
                                    <tr>
                                        <td colspan="3" class="text-center"><i class="fas fa-info-circle me-2"></i><?php echo lang("role_no_found_roles"); ?></td>
                                    </tr>
                                    <?php
                                }
                            } catch (Exception $e) {
                                // Display error if database query fails
                                ?>
                                <tr>
                                    <td colspan="3" class="text-center text-danger"><i class="fas fa-exclamation-triangle me-2"></i><?php echo lang("role_error_loading_record"); ?>
                                        <?php echo htmlspecialchars($e->getMessage()); ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS styling for the page -->
<style>
    /* Style for active pagination item */
    .pagination .page-item.active .page-link {
        background-color: #fe5500 !important;
        border-color: #fe5500 !important;
    }

    /* Style for pagination links */
    .pagination .page-link {
        color: black !important;
    }
    @media (max-width: 360px) {
        .row1{
            margin-left: -20px;
            margin-right: -20px;
        }
    }
    /* Responsive styles for mobile devices */
    @media (max-width: 576px) {
        /* DataTable length dropdown */
        #applicants_table_length {
            display: inline-block !important;
            float: left;
            font-size: 12px;
            margin-bottom: 5px;
        }

        /* DataTable search box */
        #applicants_table_filter {
            display: inline-block !important;
            float: right;
            text-align: right;
        }

        /* Input field styling */
        #applicants_table_filter input[type="search"],
        #applicants_table_length select {
            width: 60% !important;
            font-size: 12px;
            padding: 6px;
            border-radius: 5px;
        }

        /* Button styling for mobile */
        .edit_btn,
        .btn-danger {
            padding: 4px 8px !important;
            font-size: 9px !important;
            min-width: 50px;
        }

        /* Icon spacing in buttons */
        .edit_btn i,
        .btn-danger i {
            margin-right: 4px;
        }
    }

    /* Styles for larger screens */
    @media (min-width: 577px) {
        /* DataTable controls positioning */
        #applicants_table_length,
        #applicants_table_filter {
            display: block !important;
            float: none !important;
            margin-bottom: 10px;
        }

        /* Input field styling */
        #applicants_table_filter input[type="search"],
        #applicants_table_length select {
            width: auto;
            font-size: 14px;
            border-radius: 5px;
        }
    }
</style>

<!-- JavaScript libraries -->
<!-- SweetAlert for beautiful alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- jQuery for DOM manipulation and AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable with responsive feature
        const table = $('#applicants_table').DataTable({
            responsive: true,
            "columnDefs": [
                { "orderable": false, "targets": 2 } // Make action column non-sortable
            ],
            "language": {
                "emptyTable": "No roles available" // Message for empty table
            },
            "initComplete": function () {
                // On mobile, reposition the length and filter controls
                if (window.innerWidth <= 576) {
                    setTimeout(function () {
                        $('#applicants_table_length').appendTo('#mobileLengthContainer').css('display', 'inline-block');
                        $('#applicants_table_filter').appendTo('#mobileSearchContainer').css('display', 'inline-block');
                        $('#applicants_table_filter label').css('float', 'right');
                    }, 100);
                }
            }
        });

        // Handle window resize events
        $(window).resize(function () {
            if (window.innerWidth <= 576) {
                // Mobile styles
                $('#applicants_table_filter input[type="search"]').css('width', '100%');
                $('#applicants_table_length').css('float', 'left').css('width', '30%');
                $('#applicants_table_filter label').css('float', 'right');
            } else {
                // Desktop styles
                $('#applicants_table_filter input[type="search"]').css('float', 'none').css('width', 'auto');
                $('#applicants_table_length').css('float', 'none').css('width', 'auto');
                $('#applicants_table_filter label').css('margin-left', '10px');
            }
        }).trigger('resize'); // Trigger immediately on page load
    });

    // Function to confirm role deletion
    function confirmDeleteRole(roleId) {
        Swal.fire({
            title: "<?php echo lang('user_delete_confirmation_title'); ?>", // Confirmation title
            text: "<?php echo lang('user_delete_confirmation_text'); ?>", // Warning message
            icon: "warning", // Warning icon
            showCancelButton: true, // Show cancel option
            confirmButtonColor: "#FE5500", // Orange confirm button
            cancelButtonColor: "#d33", // Red cancel button
            confirmButtonText: "<?php echo lang('role_yes_delete'); ?>", // Confirm text
            cancelButtonText: "<?php echo lang('role_cancel'); ?>" // Cancel text
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, send AJAX request to delete the role
                $.ajax({
                    url: 'index.php?route=modules/admin/role-management/processRoleForm',
                    type: 'POST',
                    data: {
                        record_id: roleId,
                        btnDeleteRole: true
                    },
                    success: function(response) {
                        // On success, show confirmation message
                        Swal.fire({
                            title: "<?php echo lang('role_deleted'); ?>",
                            text: "<?php echo lang('role_delete_success'); ?>",
                            icon: "success",
                            confirmButtonColor: "#FE5500"
                        }).then(() => {
                            // Reload the page to reflect changes
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        // On error, show error message
                        Swal.fire({
                            title: "<?php echo lang('role_error'); ?>",
                            text: "<?php echo lang('role_delete_error'); ?>: " + error,
                            icon: "error",
                            confirmButtonColor: "#FE5500"
                        });
                    }
                });
            }
        });
    }
</script>