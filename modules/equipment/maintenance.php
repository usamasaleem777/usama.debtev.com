<style>
    /* Default button styles */
    .responsive-maintenance-button {
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
                        <!-- Manage maintenance breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="index.php?route=modules/admin/maintenance-management/add"
                                style="color: #fe5500"><?php echo lang("maintenance_Add_Maintenance"); ?>
                            </a>
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
                                            <?php echo lang("maintenance_Add_Maintenance"); ?>
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

                                <!-- Maintenance Form -->
                                <form class="row form-row-responsive"
                                    action="index.php?route=modules/maintenance/processMaintenanceForm"
                                    method="POST">
                                    
                                    <div class="row form-row-responsive">
                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <!-- Hidden field for maintenance ID (used in edit mode) -->
                                            <input type="hidden" id="maintenance_id" name="maintenance_id" value="" />

                                            <!-- Equipment Selection -->
                                            <label for="equipment_id" class="form-label">
                                                <?php echo lang("maintenance_Equipment"); ?>
                                            </label>
                                            <select class="form-control" name="equipment_id" id="equipment_id" required>
                                                <option value=""><?php echo lang("maintenance_Select_Equipment"); ?></option>
                                                <?php foreach ($equipmentList as $equipment): ?>
                                                    <option value="<?php echo $equipment['id']; ?>">
                                                        <?php echo htmlspecialchars($equipment['name']); ?> 
                                                        (<?php echo htmlspecialchars($equipment['serial_number']); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <!-- Maintenance Type -->
                                            <label for="maintenance_type" class="form-label"><?php echo lang("maintenance_Type"); ?></label>
                                            <select class="form-control" name="maintenance_type" id="maintenance_type" required>
                                                <option value=""><?php echo lang("maintenance_Select_Type"); ?></option>
                                                <option value="preventive"><?php echo lang("maintenance_Preventive"); ?></option>
                                                <option value="corrective"><?php echo lang("maintenance_Corrective"); ?></option>
                                                <option value="predictive"><?php echo lang("maintenance_Predictive"); ?></option>
                                                <option value="emergency"><?php echo lang("maintenance_Emergency"); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row form-row-responsive mt-3">
                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <!-- Start Date -->
                                            <label for="start_date" class="form-label"><?php echo lang("maintenance_Start_Date"); ?></label>
                                            <input type="datetime-local" class="form-control" name="start_date" id="start_date"
                                                required value="" />
                                        </div>

                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <!-- End Date -->
                                            <label for="end_date" class="form-label"><?php echo lang("maintenance_End_Date"); ?></label>
                                            <input type="datetime-local" class="form-control" name="end_date" id="end_date"
                                                value="" />
                                        </div>
                                    </div>

                                    <div class="row form-row-responsive mt-3">
                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <!-- Status -->
                                            <label for="status" class="form-label"><?php echo lang("maintenance_Status"); ?></label>
                                            <select class="form-control" name="status" id="status" required>
                                                <option value="scheduled"><?php echo lang("maintenance_Scheduled"); ?></option>
                                                <option value="in_progress"><?php echo lang("maintenance_In_Progress"); ?></option>
                                                <option value="completed"><?php echo lang("maintenance_Completed"); ?></option>
                                                <option value="cancelled"><?php echo lang("maintenance_Cancelled"); ?></option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <!-- Cost -->
                                            <label for="cost" class="form-label"><?php echo lang("maintenance_Cost"); ?></label>
                                            <input type="number" class="form-control" name="cost" id="cost"
                                                step="0.01" min="0" value="0" />
                                        </div>
                                    </div>
                                    
                                    <div class="row form-row-responsive mt-3">
                                        <div class="col-12 form-col-responsive">
                                            <!-- Description -->
                                            <label for="description" class="form-label"><?php echo lang("maintenance_Description"); ?></label>
                                            <textarea class="form-control" name="description" id="description"
                                                rows="3"></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="row form-row-responsive mt-3">
                                        <div class="col-12 form-col-responsive">
                                            <!-- Technician -->
                                            <label for="technician" class="form-label"><?php echo lang("maintenance_Technician"); ?></label>
                                            <input type="text" class="form-control" name="technician" id="technician"
                                                maxlength="100" value="" />
                                        </div>
                                    </div>
                                    
                                    <!-- Form Submit Button -->
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-4 ms-md-auto text-md-end text-center">
                                            <button type="submit" id="btnSubmitMaintenance" name="btnSubmitMaintenance"
                                                class="btn btn-success btn-responsive"
                                                style="background: linear-gradient(45deg, #FE5505, #FF8E53); color: white; border: none;">
                                                <span id="submitButtonText">
                                                    <?= lang("maintenance_Add_Maintenance") ?>
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