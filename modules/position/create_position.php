<?php
$showSuccessAlert = false;
$alertMessage = '';
$alertType = '';
$redirectAfterUpdate = false;

// Check if form was submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission if formtype is set
    if (isset($_POST['formtype'])) {
        try {
            // Handle position creation
            if ($_POST['formtype'] === 'create_position') {
                // Insert new position into database
                DB::insert('positions', [
                    'position_name' => $_POST['position_name'],
                    'status' => $_POST['status'],
                    'description' => $_POST['description'],
                    'created_at' => DB::sqleval('NOW()') // Use current timestamp
                ]);

                $showSuccessAlert = true;
                $alertMessage = "Position created successfully!";
                $alertType = "success";
            }
            // Handle position update
            elseif ($_POST['formtype'] === 'update_position' && isset($_POST['id'])) {
                // Update existing position in database
                DB::update('positions', [
                    'position_name' => $_POST['position_name'],
                    'status' => $_POST['status'],
                    'description' => $_POST['description']
                ], "id=%i", $_POST['id']);

                $showSuccessAlert = true;
                $alertMessage = "Position updated successfully!";
                $alertType = "success";
                $redirectAfterUpdate = true;
            }
        } catch (MeekroDBException $e) {
            // Handle database errors
            $showSuccessAlert = true;
            $alertMessage = "Database error: " . $e->getMessage();
            $alertType = "error";
        }
    }
}

// Check if we're editing a position (via URL parameters)
$editPosition = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    // Fetch the position to edit from database
    $editPosition = DB::queryFirstRow("SELECT * FROM positions WHERE id=%i", $_GET['id']);
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
                        <!-- Position breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="#" style="color: #fe5500"><?php echo lang(key: "position_positions"); ?></a>
                        </li>
                        <!-- View position breadcrumb -->
                        <li class="breadcrumb-item active">
                            <?= $editPosition ? 'Edit' : 'Create' ?> <?php echo lang(key: "position_position"); ?>
                        </li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- MAIN FORM SECTION -->
            <div class="row1">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card rounded-4">
                            <div class="card-header" style="border-bottom: 2px solid #FE5500;">
                                <h3 class="card-title" style="color: #FE5500;">
                                    <i class="fa fa-briefcase me-2"></i><?= $editPosition ? 'Edit' : 'Create New' ?>
                                    <?php echo lang(key: "position_position"); ?>
                                </h3>
                            </div>
                            <div class="card-body p-4">
                                <!-- Position Form -->
                                <form class="row g-4" method="POST" id="positionForm">
                                    <!-- Hidden fields for form processing -->
                                    <input type="hidden" name="formtype"
                                        value="<?= $editPosition ? 'update_position' : 'create_position' ?>">
                                    <?php if ($editPosition): ?>
                                        <input type="hidden" name="id" value="<?= $editPosition['id'] ?>">
                                    <?php endif; ?>

                                    <!-- Position Name Field -->
                                    <div class="col-md-8">
                                        <label for="position_name"
                                            class="form-label"><?php echo lang(key: "position_position_name"); ?></label>
                                        <input type="text" class="form-control" name="position_name" id="position_name"
                                            placeholder="Enter position name"
                                            value="<?= $editPosition ? htmlspecialchars($editPosition['position_name']) : '' ?>"
                                            required>
                                    </div>

                                    <!-- Status Dropdown -->
                                    <div class="col-md-4">
                                        <label for="status"
                                            class="form-label"><?php echo lang(key: "position_status"); ?></label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="1" <?= $editPosition && $editPosition['status'] == 1 ? 'selected' : '' ?>><?php echo lang(key: "position_active"); ?></option>
                                            <option value="0" <?= $editPosition && $editPosition['status'] == 0 ? 'selected' : '' ?>><?php echo lang(key: "position_inactive"); ?>
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Description Textarea -->
                                    <div class="col-md-12">
                                        <label for="description"
                                            class="form-label"><?php echo lang(key: "position_description"); ?></label>
                                        <textarea class="form-control" name="description" id="description" rows="4"
                                            placeholder="<?php echo lang(key: "position_description"); ?>"><?= $editPosition ? htmlspecialchars($editPosition['description']) : '' ?></textarea>
                                    </div>

                                    <!-- Form Buttons -->
                                    <div class="card-footer row my-3">
                                        <div class="col-6">
                                            <a href="?route=modules/position/view_position"
                                                class="btn btn-orange my-1 mobile-btn">
                                                <i
                                                    class="fa fa-times me-2"></i><?php echo lang(key: "position_cancel"); ?>
                                            </a>
                                        </div>
                                        <div class="col-6 text-end">
                                            <button type="submit" name="btnSubmit"
                                                class="btn btn-orange my-1 mobile-btn">
                                                <i
                                                    class="fa <?= $editPosition ? 'fa-edit' : 'fa-plus-circle' ?> me-2"></i>
                                                <?= $editPosition ? lang(key: "position_update") : lang(key: "position_save_new") ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
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

    .card-header {
        background-color: rgba(254, 85, 0, 0.05);
    }

    .form-label {
        font-weight: 500;
    }

    .swal2-popup {
        font-size: 0.875rem !important;
    }

    /* SweetAlert custom styles */
    .custom-swal-popup {
        background-color: #FE5500;
        color: white;
        height: auto !important;
        min-height: 60px !important;
        max-height: 100px !important;
    }

    .custom-swal-title {
        margin-top: 5px !important;
        font-size: 14px !important;
        margin: 0 !important;
    }

    .custom-swal-icon {
        margin: 5px auto 0 !important;
    }

    .swal2-popup {
        font-size: 0.875rem !important;
        line-height: 1.2 !important;
    }

    /* Responsive styles */
    @media (max-width: 767px) {
        /* Header adjustments */
        .page-header {
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }

        .breadcrumb {
            font-size: 0.8rem;
            padding: 0.5rem 0;
        }

        /* Card title adjustments */
        .card-title {
            font-size: 1.1rem;
        }

        /* Button adjustments */
        .mobile-btn {
            font-size: 0.75rem !important;
            padding: 0.25rem 0.5rem !important;
            white-space: nowrap;
        }

        /* Form adjustments */
        .card-body {
            padding: 1rem !important;
        }

        .form-label {
            font-size: 0.9rem;
        }

        .form-control {
            font-size: 0.9rem;
            padding: 0.375rem 0.75rem;
        }

        /* Card adjustments */
        .card {
            margin-left: -5px;
            margin-right: -5px;
            border-radius: 0 !important;
        }

        /* Footer button container */
        .card-footer {
            padding: 0.75rem 0 !important;
        }
    }

    /* Extra small devices (phones, 360px and down) */
    @media (max-width: 360px) {
        .mobile-btn {
            font-size: 0.65rem !important;
            padding: 0.2rem 0.3rem !important;
        }
       
        .row1 {
            margin-left: -30px;
            margin-right: -35px;
            margin-top: -15px;
        }
        .card-footer{
            margin-left: 1px;
        }
        .mobile-btn i {
            margin-right: 0.1rem !important;
        }

        .breadcrumb {
            font-size: 0.7rem;
        }

        .card-title {
            font-size: 1rem;
        }
    }
</style>

<!-- JAVASCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Show SweetAlert notification if needed
        <?php if ($showSuccessAlert): ?>
            Swal.fire({
                position: 'top-end',
                title: '<?= $alertMessage ?>',
                showConfirmButton: false,
                timer: 1500,
                width: 300,
                padding: '0.5rem',
                backdrop: false,
                customClass: {
                    popup: 'custom-swal-popup',
                    title: 'custom-swal-title',
                    icon: 'custom-swal-icon'
                }
            }).then(() => {
                <?php if ($redirectAfterUpdate): ?>
                    // Redirect to view_position page after update
                    window.location.href = '?route=modules/position/view_position';
                <?php endif; ?>
            });
        <?php endif; ?>

        // Basic form validation
        $('#positionForm').on('submit', function () {
            const positionName = $('#position_name').val().trim();
            if (!positionName) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Position name is required',
                    showConfirmButton: false,
                    timer: 1500,
                    width: 300,
                    padding: '0.5rem',
                    backdrop: false,
                    customClass: {
                        popup: 'custom-swal-popup',
                        title: 'custom-swal-title',
                        icon: 'custom-swal-icon'
                    }
                });
                return false;
            }
            return true;
        });
    });
</script>