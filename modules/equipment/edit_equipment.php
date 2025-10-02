<?php 



// Check if form was submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission if formtype is set
    if (isset($_POST['formtype'])) {
        try {
            // Handle job creation
            if ($_POST['formtype'] === 'create_job') {
                // Insert new job into database
                // DB::insert('job', [
                    // 'job_title' => $_POST['job_title'],
                    // 'job_code' => $_POST['job_code'],
                    // 'job_state' => $_POST['job_state'],
                    // 'job_city' => $_POST['job_city'],
                    // 'job_address' => $_POST['job_address'],
                    // 'job_zip' => $_POST['job_zip'],
                    // 'job_status' => $_POST['job_status'],
                    // 'job_hiring' => $_POST['job_hiring'],
                    // 'user_id' => $user_id,
                    // 'job_description' => $_POST['job_description']
                    // 'made_by' => $_POST['made_by'],
                    // 'created_at' => DB::sqleval('NOW()'),
                    // 'expires_at' => $_POST['expires_at']
                // ]);

                // $success = "Job created successfully!";
                // $_SESSION['success'] = "Job updated successfully!";
                // // Output a JavaScript redirect
                // echo '<script>window.location.href = "./index.php?route=modules/jobs/view_jobs";</script>';
                // exit();

            }
            // Handle job update
            elseif ($_POST['formtype'] === 'update_job' && isset($_POST['id'])) {
                // Update existing job in database
                DB::update('equipment', [
                    'equipment_name' => $_POST['equipment_name'],
                    'serial_number' => $_POST['serial_number'],
                    'equipment_type' => $_POST['equipment_type'],
                    'purchase_date' => $_POST['purchase_date'],
                    'status' => $_POST['status'],
                    'equipment_image' => $_POST['equipment_image']
                 
                    // 'job_description' => $_POST['job_description']

                    // 'description' => $_POST['description'],
                    // 'expires_at' => $_POST['expires_at']
                ], "id=%i", $_POST['id']);

                $success = "Job updated successfully!";
                // Output a JavaScript redirect
                echo '<script>window.location.href = "./index.php?route=modules/equipment/list_equipment";</script>';
                exit();
            }
        } catch (MeekroDBException $e) {
            // Handle database errors
            $error = "Database error: " . $e->getMessage();
        }
    }
}
// Get current user from session (modify according to your session variable)

// $current_user = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Unknown User';


// Check if we're editing a job (via URL parameters)
$equipment = null;
$equipment = DB::queryFirstRow("SELECT * FROM equipment WHERE id=%i", $_GET['id']);


?>




<style>
    /* Default button styles */
    .responsive-equipment-button {
        width: 100%;
        /* Full width on mobile */
        margin-bottom: 10px;
    }

    /* Adjust layout for small screens */
    @media (max-width: 768px) {
        .form-row-responsive {
            flex-direction: column;
        }
        
        .form-col-responsive {
            width: 100% !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-bottom: 15px;
        }
        
        .btn-responsive {
            width: 100% !important;
            margin-left: 0 !important;
            font-size: 14px;
        }
        
        .row1 {
            margin-left: -30px;
            margin-right: -30px;
            padding: 0 10px;
        }
        
        .card {
            padding: 15px !important;
        }
        
        .breadcrumb {
            margin-top: -10px;
            font-size: 12px;
            padding: 0;
        }
        
        .page-header {
            padding: 0 10px;
        }
        
        input, select, textarea {
            font-size: 14px !important;
        }
    }

    /* Medium screens */
    @media (min-width: 769px) and (max-width: 992px) {
        .form-col-responsive {
            width: 50% !important;
        }
    }
    
    /* Image preview styling */
    .image-preview-container {
        margin-top: 15px;
        text-align: center;
    }
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
    }
    .current-image-text {
        font-size: 12px;
        color: #666;
        margin-bottom: 5px;
    }
</style>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Include SweetAlert for beautiful alerts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- Main content container -->
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid pt-4">
            <!-- Page header with breadcrumb navigation -->
            <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
                <div>
                    <ol class="breadcrumb float-sm-right mt-2">
                        <!-- Home breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="index.php" style="color: #fe5500">
                                <i class="fas fa-home me-1"></i><?php echo lang("role_home"); ?>
                            </a>
                        </li>
                        <!-- Edit equipment breadcrumb -->
                        <li class="breadcrumb-item active">
                            <?php echo lang("equipment_Edit_Equipment"); ?>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Main form row -->
            <div class="row1">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body p-4">
                                <!-- Form header with dynamic title -->
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <h5 class="mb-0 fw-bold" id="formTitle" style="color: #FE5500;">
                                            <?php echo lang("equipment_Edit_Equipment"); ?>
                                        </h5>
                                    </div>
                                </div>

                                <!-- Display error/success messages if they exist -->
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); ?>
                                    </div>
                                    <?php unset($_SESSION['error']); ?>
                                <?php endif; ?>
                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); ?>
                                    </div>
                                    <?php unset($_SESSION['success']); ?>
                                <?php endif; ?>

                                <!-- Equipment Form -->
                                <form class="row form-row-responsive"
                                    action="index.php?route=modules/equipment/processEquipmentForm"
                                    method="POST"
                                    enctype="multipart/form-data">
                                    
                                    <div class="row form-row-responsive">
                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <!-- Hidden field for equipment ID -->
                                            <input type="hidden" id="equipment_id" name="id" value="<?php echo htmlspecialchars($equipment['id'] ?? ''); ?>" />

                                            <!-- Equipment Name Input -->
                                            <label for="equipment_name" class="form-label">
                                                <?php echo lang("equipment_Equipment_Name"); ?>
                                            </label>
                                            <input type="text" class="form-control" name="equipment_name" id="equipment_name"
                                                required minlength="3" maxlength="50"
                                                value="<?php echo htmlspecialchars($equipment['equipment_name'] ?? ''); ?>" />
                                        </div>
                                        
                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <!-- Serial Number -->
                                            <label for="serial_number" class="form-label"><?php echo lang("equipment_Serial_Number"); ?></label>
                                            <input type="text" class="form-control" name="serial_number" id="serial_number"
                                                 minlength="3" maxlength="50"
                                                value="<?php echo htmlspecialchars($equipment['serial_number'] ?? ''); ?>" />
                                        </div>
                                    </div>

                                    <div class="row form-row-responsive mt-3">
                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <!-- Equipment Type -->
                                            <label for="equipment_type" class="form-label"><?php echo lang("equipment_Equipment_Type"); ?></label>
                                            <select class="form-control" name="equipment_type" id="equipment_type" required>
                                                <option value=""><?php echo lang("equipment_Select_Type"); ?></option>
                                                <option value="heavy" <?php echo (isset($equipment['equipment_type']) && $equipment['equipment_type'] == 'heavy') ? 'selected' : ''; ?>>
                                                    <?php echo lang("equipment_Heavy_Machinery"); ?>
                                                </option>
                                                <option value="light" <?php echo (isset($equipment['equipment_type']) && $equipment['equipment_type'] == 'light') ? 'selected' : ''; ?>>
                                                    <?php echo lang("equipment_Light_Equipment"); ?>
                                                </option>
                                                <option value="vehicle" <?php echo (isset($equipment['equipment_type']) && $equipment['equipment_type'] == 'vehicle') ? 'selected' : ''; ?>>
                                                    <?php echo lang("equipment_Vehicle"); ?>
                                                </option>
                                                <option value="other" <?php echo (isset($equipment['equipment_type']) && $equipment['equipment_type'] == 'other') ? 'selected' : ''; ?>>
                                                    <?php echo lang("equipment_Other"); ?>
                                                </option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <label for="purchase_date"><?php echo lang("equipment_Purchase_Date"); ?></label>
                                            <input type="date" class="form-control" name="purchase_date" id="purchase_date"
                                                required value="<?php echo htmlspecialchars($equipment['purchase_date'] ?? ''); ?>" />
                                        </div>
                                    </div>

                                    <div class="row form-row-responsive mt-3">
                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <label for="status"><?php echo lang("equipment_Status"); ?></label>
                                            <select class="form-control" name="status" id="status" required>
                                                <option value="available" <?php echo (isset($equipment['status']) && $equipment['status'] == 'available') ? 'selected' : ''; ?>>
                                                    <?php echo lang("equipment_Available"); ?>
                                                </option>
                                                <option value="in_use" <?php echo (isset($equipment['status']) && $equipment['status'] == 'in_use') ? 'selected' : ''; ?>>
                                                    <?php echo lang("equipment_In_Use"); ?>
                                                </option>
                                                <option value="maintenance" <?php echo (isset($equipment['status']) && $equipment['status'] == 'maintenance') ? 'selected' : ''; ?>>
                                                    <?php echo lang("equipment_Maintenance"); ?>
                                                </option>
                                                <option value="out_of_service" <?php echo (isset($equipment['status']) && $equipment['status'] == 'out_of_service') ? 'selected' : ''; ?>>
                                                    <?php echo lang("equipment_Out_of_Service"); ?>
                                                </option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <label for="equipment_image">
                                                <?php echo lang("equipment_Upload_Image"); ?>
                                            </label>
                                            <input type="file" class="form-control shadow-sm" name="equ
                                            ipment_image" id="equipment_image"
                                                accept="image/*" />
                                                
                                            <!-- Current image display (if exists) -->
                                            <!-- <?php if (!empty($equipment['image_path'])): ?>
                                                <div class="image-preview-container">
                                                    <p class="current-image-text"><?php echo lang("equipment_Current_Image"); ?></p>
                                                    <img src="<?php echo htmlspecialchars($equipment['image_path']); ?>" 
                                                         class="image-preview" 
                                                         alt="<?php echo lang("equipment_Equipment_Image"); ?>">
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image">
                                                        <label class="form-check-label" for="remove_image">
                                                            <?php echo lang("equipment_Remove_Image"); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endif; ?> -->
                                        </div>
                                    </div>
                                    
                                    <!-- Form Submit Button -->
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-4 ms-md-auto text-md-end text-center">
                                            <button type="submit" id="btnSubmitEquipment" name="btnSubmitEquipment"
                                                class="btn btn-success btn-responsive"
                                                style="background: linear-gradient(45deg, #FE5505, #FF8E53); color: white; border: none;">
                                                <span id="submitButtonText">
                                                    <?php echo lang("equipment_Update_Equipment"); ?>
                                                </span>
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

<script>
// Image preview functionality
document.getElementById('equipment_image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        const previewContainer = document.querySelector('.image-preview-container');
        
        // Create container if it doesn't exist
        if (!previewContainer) {
            const container = document.createElement('div');
            container.className = 'image-preview-container mt-2';
            event.target.parentNode.appendChild(container);
        }
        
        reader.onload = function(e) {
            // Remove existing preview if any
            const existingPreview = document.querySelector('.image-preview');
            if (existingPreview) {
                existingPreview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.className = 'image-preview';
                img.src = e.target.result;
                document.querySelector('.image-preview-container').prepend(img);
            }
        }
        reader.readAsDataURL(file);
    }
});
</script>