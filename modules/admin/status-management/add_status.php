<style>
    /* Default button styles */
    .responsive-status-button {
        width: 40%;
        /* Default width for larger screens */
    }

    /* Adjust button size for small screens */
    @media (max-width: 360px) {
        .btn {
            width: 100px;
            margin-left: -20px;
            font-size: 12px;
        }

        .row1 {
            margin-left: -45px;
            margin-right: -45px;
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
                                <i class="fas fa-home me-1"></i><?php echo lang("status_home"); ?>
                            </a>
                        </li>
                        <!-- Manage status breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="index.php?route=modules/admin/status-management/add"
                                style="color: #fe5500"><?php echo lang("status_manage_status"); ?>
                            </a>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Main form row -->
            <div class="row1">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card ">
                            <div class="card-body p-4">
                                <!-- Form header with dynamic title -->
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <h5 class="mb-0 fw-bold" id="formTitle" style="color: #FE5500;">
                                            <?= lang("status_add") ?>
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

                                <!-- status Form -->
                                <form class="row"
                                    action="index.php?route=modules/admin/status-management/processStatusForm"
                                    method="POST">
                                    <div class="col-12">
                                        <!-- Hidden field for status ID (used in edit mode) -->
                                        <input type="hidden" id="status_id" name="status_id" value="" />

                                        <!-- status Title Input -->
                                        <label for="status_title" class="form-label">
                                            <?php echo lang("status_status_title"); ?>
                                        </label>
                                        <input type="text" class="form-control" name="status_title" id="status_title"
                                            required minlength="3" maxlength="50" pattern="[A-Za-z ]+"
                                            title="Only letters and spaces are allowed, min 3 and max 50 characters"
                                            value="" />
                                    </div>

                                    <div class="col-4">
                                        <!-- Dropdown for Active/Inactive Status -->
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : '' ?>>Active</option>
                                            <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </div>

                                    <!-- Form Submit Button -->
                                    <div class="row my-3">
                                        <div class="col-4 ms-auto text-end">
                                            <button type="submit" id="btnSubmitStatus" name="btnSubmitStatus"
                                                class="btn btn-success"
                                                style="background: linear-gradient(45deg, #FE5505, #FF8E53); color: white; border: none;">
                                                <span id="submitButtonText">
                                                    <?= lang("status_add_status") ?>
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