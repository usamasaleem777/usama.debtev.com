<?php
include 'includes/page-parts/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['formtype'])) {
        try {
            if ($_POST['formtype'] === 'createcrew') {
                if (empty($_POST['crew_name'])) {
                    throw new Exception(lang("crew_name_empty_error"));
                }
                
                DB::insert('crew', [
                    'crew_name' => $_POST['crew_name'],
                    'status' => 'active'
                ]);
                
                $_SESSION['success'] = 'created';
                echo '<script>window.location.href = "index.php?route=modules/crew-management/manage_crew";</script>';
                exit;
                
            } elseif ($_POST['formtype'] === 'updatecrew') {
                if (empty($_POST['crew_id']) || empty($_POST['crew_name'])) {
                    throw new Exception(lang("required_fields_missing_error"));
                }
                
                DB::update('crew', [
                    'crew_name' => $_POST['crew_name'],
                    'status' => $_POST['status']
                ], "crew_id=%s", $_POST['crew_id']);
                
                $_SESSION['success'] = 'updated';
                echo '<script>window.location.href = "index.php?route=modules/crew-management/manage_crew";</script>';
                exit;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Handle crew deletion
if (isset($_GET['delete_crew'])) {
    try {
        $deleteCrewId = $_GET['delete_crew'];
        $users = DB::query("SELECT user_id, crew_id FROM users WHERE FIND_IN_SET(%s, crew_id)", $deleteCrewId);
        
        foreach ($users as $user) {
            $crewIds = array_filter(explode(',', $user['crew_id']));
            $updatedCrewIds = array_diff($crewIds, [$deleteCrewId]);
            DB::update('users', [
                'crew_id' => implode(',', $updatedCrewIds)
            ], "user_id=%s", $user['user_id']);
        }
        
        DB::delete('crew', "crew_id=%s", $deleteCrewId);
        
        $_SESSION['success'] = 'deleted';
        echo '<script>window.location.href = "index.php?route=modules/crew-management/manage_crew";</script>';
        exit;
    } catch (Exception $e) {
        $error = lang("delete_crew_error") . $e->getMessage();
    }
}

// Fetch all crews with member counts
try {
    $crews = DB::query("SELECT c.*, 
                       (SELECT COUNT(*) FROM users WHERE FIND_IN_SET(c.crew_id, crew_id)) as member_count 
                       FROM crew c 
                       ORDER BY c.crew_name");
} catch (Exception $e) {
    $crews = [];
    $error = lang("load_crews_error") . $e->getMessage();
}

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
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
                    <!-- View Crews breadcrumb -->
                    <li class="breadcrumb-item">
                        <a href="index.php?route=modules/crew-management/manage_crew" style="color: #fe5500"><?php echo lang("admin_manage_crew"); ?></a>
                    </li>
                </ol>
            </div>
        </div>
        <div class="row1">
            <!-- Main card with orange top border -->
            <div class="card border-top" style="border-color: #FE5500 !important;">
                <div class="card-body p-4">
                    <!-- Error message display (if any) -->
                    <?php if (isset($error)) { ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php } ?>

                    <!-- Success message display (if any) -->
                    <?php if ($success === 'created'): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo lang("crew_created_success"); ?>
                        </div>
                    <?php elseif ($success === 'updated'): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo lang("crew_updated_success"); ?>
                        </div>
                    <?php elseif ($success === 'deleted'): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo lang("crew_deleted_success"); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Table header section -->
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: #FE5500;"><?php echo lang("admin_crew"); ?></h5>
                        </div>
                        <div>
                            <a href="index.php?route=modules/crew-management/addcrew" class="btn" style="background-color:#fe5500; color: white;">
                                <i class="fas fa-plus me-1"></i> <?php echo lang("add_new_crew"); ?>
                            </a>
                        </div>
                    </div>

                    <!-- Crews table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover border text-nowrap mb-0 datatable mt-3 w-100" id="crewTable">
                            <thead>
                                <tr>
                                    <th style="background-color: #FE5500; color: white;"><?php echo lang("crew_name"); ?></th>
                                    <th style="background-color: #FE5500; color: white;"><?php echo lang("members"); ?></th>
                                    <th style="background-color: #FE5500; color: white;"><?php echo lang("status"); ?></th>
                                    <th class="text-center" style="background-color: #FE5500; color: white;"><?php echo lang("actions"); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($crews as $crew): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($crew['crew_name']) ?></td>
                                        <td>
                                            <?= $crew['member_count'] ?>
                                            <a href="index.php?route=modules/crew-management/view_members&crew_id=<?= urlencode($crew['crew_id']) ?>" 
                                               class="btn btn-sm" style="background-color: #fe5500; color: white; margin-left: 8px;">
                                                <i class="fas fa-user-plus"></i> <?php echo lang("manage"); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $crew['status'] === 'active' ? 'success' : 'warning' ?>">
                                                <?= ucfirst(lang($crew['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="index.php?route=modules/crew-management/editcrew&crew_id=<?= urlencode($crew['crew_id']) ?>" 
                                               class="btn edit_btn" title="<?php echo lang("edit"); ?>"
                                               style="background-color: #FE5500; color: white; border: none; padding: 8px 16px; border-radius: 5px; transition: background 0.3s;"
                                               onmouseover="this.style.backgroundColor='#E04A00'"
                                               onmouseout="this.style.backgroundColor='#FE5500'">
                                                <i class="fas fa-edit me-1"></i> <?php echo lang("edit"); ?>
                                            </a>

                                            <button class="btn btn-danger delete-crew ms-2" 
                                                    data-id="<?= htmlspecialchars($crew['crew_id']) ?>"
                                                    data-name="<?= htmlspecialchars($crew['crew_name']) ?>"
                                                    title="<?php echo lang("delete"); ?>"
                                                    style="border: none; padding: 8px 16px; border-radius: 5px; transition: background 0.3s;"
                                                    onmouseover="this.style.backgroundColor='#c82333'"
                                                    onmouseout="this.style.backgroundColor='#dc3545'">
                                                <i class="fas fa-trash-alt me-1"></i> <?php echo lang("delete"); ?>
                                            </button>
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
        #crewTable_length {
            display: inline-block !important;
            float: left;
            font-size: 12px;
            margin-bottom: 5px;
        }

        /* DataTable search box */
        #crewTable_filter {
            display: inline-block !important;
            float: right;
            text-align: right;
        }

        /* Input field styling */
        #crewTable_filter input[type="search"],
        #crewTable_length select {
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
        #crewTable_length,
        #crewTable_filter {
            display: block !important;
            float: none !important;
            margin-bottom: 10px;
        }

        /* Input field styling */
        #crewTable_filter input[type="search"],
        #crewTable_length select {
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
<!-- DataTables for enhanced table functionality -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable with responsive feature
        const table = $('#crewTable').DataTable({
            responsive: true,
            "columnDefs": [
                { "orderable": false, "targets": 3 } // Make action column non-sortable
            ],
            "language": {
                "emptyTable": "No crews available" // Message for empty table
            },
            "initComplete": function () {
                // On mobile, reposition the length and filter controls
                if (window.innerWidth <= 576) {
                    setTimeout(function () {
                        $('#crewTable_length').appendTo('#mobileLengthContainer').css('display', 'inline-block');
                        $('#crewTable_filter').appendTo('#mobileSearchContainer').css('display', 'inline-block');
                        $('#crewTable_filter label').css('float', 'right');
                    }, 100);
                }
            }
        });

        // Handle window resize events
        $(window).resize(function () {
            if (window.innerWidth <= 576) {
                // Mobile styles
                $('#crewTable_filter input[type="search"]').css('width', '100%');
                $('#crewTable_length').css('float', 'left').css('width', '30%');
                $('#crewTable_filter label').css('float', 'right');
            } else {
                // Desktop styles
                $('#crewTable_filter input[type="search"]').css('float', 'none').css('width', 'auto');
                $('#crewTable_length').css('float', 'none').css('width', 'auto');
                $('#crewTable_filter label').css('margin-left', '10px');
            }
        }).trigger('resize'); // Trigger immediately on page load

        // Delete confirmation
        $(document).on('click', '.delete-crew', function() {
            const crewId = $(this).data('id');
            const crewName = $(this).data('name');
            
            Swal.fire({
                title: '<?php echo lang("delete_crew_confirm_title"); ?>',
                html: `<?php echo lang("delete_crew_confirm_text"); ?> <strong>${crewName}</strong>?<br>
                      <?php echo lang("delete_crew_confirm_warning"); ?>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FE5500',
                confirmButtonText: '<?php echo lang("delete"); ?>',
                cancelButtonText: '<?php echo lang("cancel"); ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `index.php?route=modules/crew-management/manage_crew&delete_crew=${encodeURIComponent(crewId)}`;
                }
            });
        });
    });
</script>

<?php
ob_end_flush();
?>