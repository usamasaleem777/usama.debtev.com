<style>
    /* Default button styles */
    .responsive-tool-button {
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
                        <!-- Manage tool breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="index.php?route=modules/admin/tool-management/add"
                                style="color: #fe5500"><?php echo lang("tool_add_tool"); ?>
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
                                            <?php echo lang("tool_add_tool"); ?>
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

                                <!-- tool Form -->
                                <form class="row form-row-responsive"
                                    action="index.php?route=modules/tools/processtoolForm"
                                    method="POST"
                                    enctype="multipart/form-data">
                                    
                                    <div class="row form-row-responsive">
                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <!-- Hidden field for tool ID (used in edit mode) -->
                                            <input type="hidden" id="tool_id" name="tool_id" value="" />

                                            <!-- tool Title Input -->
                                            <label for="tool_name" class="form-label">
                                                <?php echo lang("tools_Tool_name"); ?>
                                            </label>
                                            <input type="text" class="form-control" name="tool_name" id="tool_name"
                                                required minlength="3" maxlength="50"
                                                value="" />
                                        </div>
                                        
                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <!-- Description -->
                                            <label for="tool_description" class="form-label"><?php echo lang("tools_Description"); ?></label>
                                            <input type="text" class="form-control" name="tool_description" id="tool_description"
                                                 minlength="3" maxlength="50"
                                                value="" />
                                        </div>
                                    </div>

                                    <div class="row form-row-responsive mt-3">
                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <label for="quantity"><?php echo lang("tools_Quantity"); ?></label>
                                            <input type="number" class="form-control" name="quantity" id="quantity"
                                                required min="1" max="1000" pattern="[0-9]+"
                                                title="Only numbers are allowed, min 1 and max 1000"
                                                value="" />
                                        </div>

                                        <div class="col-md-6 col-12 form-col-responsive">
                                            <label for="tool_picture">
                                                <?php echo lang("tools_Upload Tool Picture"); ?>
                                            </label>
                                            <input type="file" class="form-control shadow-sm" name="tool_picture" id="tool_picture"
                                                accept="image/*" />
                                        </div>
                                    </div>
                                    
                                    <!-- Form Submit Button -->
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-4 ms-md-auto text-md-end text-center">
                                            <button type="submit" id="btnSubmittool" name="btnSubmittool"
                                                class="btn btn-success btn-responsive"
                                                style="background: linear-gradient(45deg, #FE5505, #FF8E53); color: white; border: none;">
                                                <span id="submitButtonText">
                                                    <?= lang("tool_add_tool") ?>
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