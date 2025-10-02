<?php
// addcrew.php
include 'includes/page-parts/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['formtype']) && $_POST['formtype'] === 'createcrew') {
        try {
            // Validate crew name
            if (empty($_POST['crew_name'])) {
                throw new Exception(lang("crew_name_empty_error"));
            }
            
            DB::insert('crew', [
                'crew_name' => $_POST['crew_name'],
                'status' => 'active'
            ]);
            
            // Store success message in session
            $_SESSION['success'] = 'created';
            
            // Output JavaScript to redirect
            echo '<script>window.location.href = "index.php?route=modules/crew-management/manage_crew";</script>';
            exit;
            
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Handle success messages from session
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
                        <a href="index.php" style="color: #fe5500"><i class="fas fa-home me-1"></i><?php echo lang("home"); ?></a>
                    </li>
                
                    <!-- Manage Crew breadcrumb -->
                    <li class="breadcrumb-item">
                        <a href="index.php?route=modules/crew-management/manage_crew" style="color: #fe5500"><?php echo lang("admin_manage_crew"); ?></a>
                    </li>
                    <!-- Add New Crew breadcrumb -->
                    <li class="breadcrumb-item active">
                        <?php echo lang("add_new_crew"); ?>
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

                    <!-- Card header section -->
                    <div class="d-flex align-items-start justify-content-between mb-4">
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: #FE5500;">
                                <i class="fas fa-users me-2"></i><?php echo lang("add_new_crew"); ?>
                            </h5>
                        </div>
                        <div>
                            <a href="index.php?route=modules/crew-management/manage_crew" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i><?php echo lang("back_to_crew_list"); ?>
                            </a>
                        </div>
                    </div>

                    <!-- Crew form -->
                    <form id="crewForm" method="POST" action="index.php?route=modules/crew-management/addcrew">
                        <input type="hidden" name="formtype" value="createcrew">
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><?php echo lang("crew_name"); ?> *</label>
                                    <input type="text" class="form-control" name="crew_name" id="crewName" required>
                                    <div class="invalid-feedback" id="crewNameFeedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn" 
                                    style="background-color: #FE5500; color: white; border: none; padding: 8px 16px; border-radius: 5px; transition: background 0.3s;"
                                    onmouseover="this.style.backgroundColor='#E04A00'"
                                    onmouseout="this.style.backgroundColor='#FE5500'">
                                <i class="fas fa-save me-2"></i><?php echo lang("save_crew"); ?>
                            </button>
                            <a href="index.php?route=modules/crew-management/manage_crew" class="btn btn-secondary ms-2">
                                <i class="fas fa-times me-2"></i><?php echo lang("cancel"); ?>
                            </a>
                        </div>
                    </form>
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
        /* Button styling for mobile */
        .btn {
            padding: 4px 8px !important;
            font-size: 9px !important;
            min-width: 50px;
        }

        /* Icon spacing in buttons */
        .btn i {
            margin-right: 4px;
        }
    }
</style>

<!-- JavaScript libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    // Form validation
    $('#crewForm').on('submit', function(e) {
        const crewName = $('#crewName').val().trim();
        if (!crewName) {
            $('#crewName').addClass('is-invalid');
            $('#crewNameFeedback').text('<?php echo lang("please_enter_crew_name"); ?>');
            e.preventDefault();
            return false;
        }
        return true;
    });
    
    $('#crewName').on('input', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>