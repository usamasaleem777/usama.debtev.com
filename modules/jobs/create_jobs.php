<?php
$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
// Check if form was submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission if formtype is set
    if (isset($_POST['formtype'])) {
        try {
            // Handle job creation
            if ($_POST['formtype'] === 'create_job') {
                // Insert new job into database
                DB::insert('job', [
                    'job_title' => $_POST['job_title'],
                    'job_code' => $_POST['job_code'],
                    'job_state' => $_POST['job_state'],
                    'job_city' => $_POST['job_city'],
                    'job_address' => $_POST['job_address'],
                    'job_zip' => $_POST['job_zip'],
                    'job_status' => $_POST['job_status'],
                    'job_hiring' => $_POST['job_hiring'],
                    'user_id' => $user_id,
                    // 'job_description' => $_POST['job_description']
                    // 'made_by' => $_POST['made_by'],
                    // 'created_at' => DB::sqleval('NOW()'),
                    // 'expires_at' => $_POST['expires_at']
                ]);

                $success = "Job created successfully!";
                $_SESSION['success'] = "Job updated successfully!";
                // Output a JavaScript redirect
                echo '<script>window.location.href = "./index.php?route=modules/jobs/view_jobs";</script>';
                exit();

            }
            // Handle job update
            elseif ($_POST['formtype'] === 'update_job' && isset($_POST['id'])) {
                // Update existing job in database
                DB::update('job', [
                    'job_title' => $_POST['job_title'],
                    'job_code' => $_POST['job_code'],
                    'job_state' => $_POST['job_state'],
                    'job_city' => $_POST['job_city'],
                    'job_address' => $_POST['job_address'],
                    'job_zip' => $_POST['job_zip'],
                    'job_status' => $_POST['job_status'],
                    'job_hiring' => $_POST['job_hiring'],
                    'user_id' => $user_id,
                    // 'job_description' => $_POST['job_description']

                    // 'description' => $_POST['description'],
                    // 'expires_at' => $_POST['expires_at']
                ], "id=%i", $_POST['id']);

                $success = "Job updated successfully!";
                // Output a JavaScript redirect
                echo '<script>window.location.href = "./index.php?route=modules/jobs/view_jobs";</script>';
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
$editJob = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    // Fetch the job to edit from database
    $editJob = DB::queryFirstRow("SELECT * FROM job WHERE id=%i", $_GET['id']);
}

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- HTML structure begins here -->
<div class="main-content app-content mt-0">
    <div class="side-app">
        <!-- CONTAINER -->
        <div class="main-container container-fluid">
            <!-- PAGE HEADER WITH BREADCRUMBS -->
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
                            <a href="#" style="color: #fe5500"><?php echo lang(key: "job_jobs"); ?></a>
                        </li>
                        <!-- View position breadcrumb -->
                        <li class="breadcrumb-item  active"><?= $editJob ? 'Edit' : 'Create' ?>
                            <?php echo lang(key: "job_job"); ?></li>
                    </ol>
                </div>
            </div>

            <!-- PAGE-HEADER END -->

            <!-- Display success/error messages -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success rounded-4"><?= $success ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger rounded-4"><?= $error ?></div>
            <?php endif; ?>

            <!-- MAIN FORM SECTION -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 2px solid #FE5500;">
                            <h3 class="card-title" style="color: #FE5500;">
                                <i class="fa fa-briefcase me-2"></i><?= $editJob ? 'Edit' : 'Create New' ?>
                                <?php echo lang(key: "job_job"); ?>
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <!-- Job Form -->
                            <form class="row g-4" method="POST">
                                <!-- Hidden fields -->
                                <input type="hidden" name="formtype"
                                    value="<?= $editJob ? 'update_job' : 'create_job' ?>">
                                <?php if ($editJob): ?>
                                    <input type="hidden" name="id" value="<?= $editJob['id'] ?>">
                                <?php endif; ?>

                                <!-- Job Name -->
                                <div class="col-md-4">
                                    <label for="name" class="form-label"><?php echo lang(key: "job_title"); ?></label>
                                    <input type="text" class="form-control" name="job_title" id="job_title"
                                        placeholder="Enter title"
                                        value="<?= $editJob ? htmlspecialchars($editJob['job_title']) : '' ?>" required>
                                </div>

                                <!-- Job code  -->
                                <div class="col-md-4">
                                    <label for="name" class="form-label"><?php echo lang(key: "job_code"); ?></label>
                                    <input type="text" class="form-control" name="job_code" id="job_code"
                                        placeholder="Enter job code"
                                        value="<?= $editJob ? htmlspecialchars($editJob['job_code']) : '' ?>" required>
                                </div>

                                <!-- Job State -->
                                <div class="col-md-4">
                                    <label for="job_state"
                                        class="form-label"><?php echo lang(key: "jobs_state"); ?></label>
                                    <select class="form-control" name="job_state" id="job_state" required>
                                        <option value="">Select State</option>
                                        <option value="Alabama" <?= isset($editJob) && $editJob['job_state'] == 'Alabama' ? 'selected' : '' ?>>Alabama</option>
                                        <option value="Alaska" <?= isset($editJob) && $editJob['job_state'] == 'Alaska' ? 'selected' : '' ?>>Alaska</option>
                                        <option value="Arizona" <?= isset($editJob) && $editJob['job_state'] == 'Arizona' ? 'selected' : '' ?>>Arizona</option>
                                        <option value="Arkansas" <?= isset($editJob) && $editJob['job_state'] == 'Arkansas' ? 'selected' : '' ?>>Arkansas</option>
                                        <option value="California" <?= isset($editJob) && $editJob['job_state'] == 'California' ? 'selected' : '' ?>>California</option>
                                        <option value="Colorado" <?= isset($editJob) && $editJob['job_state'] == 'Colorado' ? 'selected' : '' ?>>Colorado</option>
                                        <option value="Connecticut" <?= isset($editJob) && $editJob['job_state'] == 'Connecticut' ? 'selected' : '' ?>>Connecticut
                                        </option>
                                        <option value="Delaware" <?= isset($editJob) && $editJob['job_state'] == 'Delaware' ? 'selected' : '' ?>>Delaware</option>
                                        <option value="Florida" <?= isset($editJob) && $editJob['job_state'] == 'Florida' ? 'selected' : '' ?>>Florida</option>
                                        <option value="Georgia" <?= isset($editJob) && $editJob['job_state'] == 'Georgia' ? 'selected' : '' ?>>Georgia</option>
                                        <option value="Hawaii" <?= isset($editJob) && $editJob['job_state'] == 'Hawaii' ? 'selected' : '' ?>>Hawaii</option>
                                        <option value="Idaho" <?= isset($editJob) && $editJob['job_state'] == 'Idaho' ? 'selected' : '' ?>>Idaho</option>
                                        <option value="Illinois" <?= isset($editJob) && $editJob['job_state'] == 'Illinois' ? 'selected' : '' ?>>Illinois</option>
                                        <option value="Indiana" <?= isset($editJob) && $editJob['job_state'] == 'Indiana' ? 'selected' : '' ?>>Indiana</option>
                                        <option value="Iowa" <?= isset($editJob) && $editJob['job_state'] == 'Iowa' ? 'selected' : '' ?>>Iowa</option>
                                        <option value="Kansas" <?= isset($editJob) && $editJob['job_state'] == 'Kansas' ? 'selected' : '' ?>>Kansas</option>
                                        <option value="Kentucky" <?= isset($editJob) && $editJob['job_state'] == 'Kentucky' ? 'selected' : '' ?>>Kentucky</option>
                                        <option value="Louisiana" <?= isset($editJob) && $editJob['job_state'] == 'Louisiana' ? 'selected' : '' ?>>Louisiana</option>
                                        <option value="Maine" <?= isset($editJob) && $editJob['job_state'] == 'Maine' ? 'selected' : '' ?>>Maine</option>
                                        <option value="Maryland" <?= isset($editJob) && $editJob['job_state'] == 'Maryland' ? 'selected' : '' ?>>Maryland</option>
                                        <option value="Massachusetts" <?= isset($editJob) && $editJob['job_state'] == 'Massachusetts' ? 'selected' : '' ?>>Massachusetts
                                        </option>
                                        <option value="Michigan" <?= isset($editJob) && $editJob['job_state'] == 'Michigan' ? 'selected' : '' ?>>Michigan</option>
                                        <option value="Minnesota" <?= isset($editJob) && $editJob['job_state'] == 'Minnesota' ? 'selected' : '' ?>>Minnesota</option>
                                        <option value="Mississippi" <?= isset($editJob) && $editJob['job_state'] == 'Mississippi' ? 'selected' : '' ?>>Mississippi
                                        </option>
                                        <option value="Missouri" <?= isset($editJob) && $editJob['job_state'] == 'Missouri' ? 'selected' : '' ?>>Missouri</option>
                                        <option value="Montana" <?= isset($editJob) && $editJob['job_state'] == 'Montana' ? 'selected' : '' ?>>Montana</option>
                                        <option value="Nebraska" <?= isset($editJob) && $editJob['job_state'] == 'Nebraska' ? 'selected' : '' ?>>Nebraska</option>
                                        <option value="Nevada" <?= isset($editJob) && $editJob['job_state'] == 'Nevada' ? 'selected' : '' ?>>Nevada</option>
                                        <option value="New Hampshire" <?= isset($editJob) && $editJob['job_state'] == 'New Hampshire' ? 'selected' : '' ?>>New Hampshire</option>
                                        <option value="New Jersey" <?= isset($editJob) && $editJob['job_state'] == 'New Jersey' ? 'selected' : '' ?>>New Jersey</option>
                                        <option value="New Mexico" <?= isset($editJob) && $editJob['job_state'] == 'New Mexico' ? 'selected' : '' ?>>New Mexico</option>
                                        <option value="New York" <?= isset($editJob) && $editJob['job_state'] == 'New York' ? 'selected' : '' ?>>New York</option>
                                        <option value="North Carolina" <?= isset($editJob) && $editJob['job_state'] == 'North Carolina' ? 'selected' : '' ?>>North Carolina
                                        </option>
                                        <option value="North Dakota" <?= isset($editJob) && $editJob['job_state'] == 'North Dakota' ? 'selected' : '' ?>>North Dakota</option>
                                        <option value="Ohio" <?= isset($editJob) && $editJob['job_state'] == 'Ohio' ? 'selected' : '' ?>>Ohio</option>
                                        <option value="Oklahoma" <?= isset($editJob) && $editJob['job_state'] == 'Oklahoma' ? 'selected' : '' ?>>Oklahoma</option>
                                        <option value="Oregon" <?= isset($editJob) && $editJob['job_state'] == 'Oregon' ? 'selected' : '' ?>>Oregon</option>
                                        <option value="Pennsylvania" <?= isset($editJob) && $editJob['job_state'] == 'Pennsylvania' ? 'selected' : '' ?>>Pennsylvania
                                        </option>
                                        <option value="Rhode Island" <?= isset($editJob) && $editJob['job_state'] == 'Rhode Island' ? 'selected' : '' ?>>Rhode Island</option>
                                        <option value="South Carolina" <?= isset($editJob) && $editJob['job_state'] == 'South Carolina' ? 'selected' : '' ?>>South Carolina
                                        </option>
                                        <option value="South Dakota" <?= isset($editJob) && $editJob['job_state'] == 'South Dakota' ? 'selected' : '' ?>>South Dakota</option>
                                        <option value="Tennessee" <?= isset($editJob) && $editJob['job_state'] == 'Tennessee' ? 'selected' : '' ?>>Tennessee</option>
                                        <option value="Texas" <?= isset($editJob) && $editJob['job_state'] == 'Texas' ? 'selected' : '' ?>>Texas</option>
                                        <option value="Utah" <?= isset($editJob) && $editJob['job_state'] == 'Utah' ? 'selected' : '' ?>>Utah</option>
                                        <option value="Vermont" <?= isset($editJob) && $editJob['job_state'] == 'Vermont' ? 'selected' : '' ?>>Vermont</option>
                                        <option value="Virginia" <?= isset($editJob) && $editJob['job_state'] == 'Virginia' ? 'selected' : '' ?>>Virginia</option>
                                        <option value="Washington" <?= isset($editJob) && $editJob['job_state'] == 'Washington' ? 'selected' : '' ?>>Washington</option>
                                        <option value="West Virginia" <?= isset($editJob) && $editJob['job_state'] == 'West Virginia' ? 'selected' : '' ?>>West Virginia</option>
                                        <option value="Wisconsin" <?= isset($editJob) && $editJob['job_state'] == 'Wisconsin' ? 'selected' : '' ?>>Wisconsin</option>
                                        <option value="Wyoming" <?= isset($editJob) && $editJob['job_state'] == 'Wyoming' ? 'selected' : '' ?>>Wyoming</option>
                                    </select>
                                </div>



                                <!-- Job City -->
                                <div class="col-md-4">
                                    <label for="name" class="form-label"><?php echo lang(key: "job_city"); ?></label>
                                    <input type="text" class="form-control" name="job_city" id="Job_city"
                                        placeholder="Enter city"
                                        value="<?= $editJob ? htmlspecialchars($editJob['job_city']) : '' ?>" required>
                                </div>

                                <!-- Job address -->
                                <div class="col-md-4">
                                    <label for="name" class="form-label"><?php echo lang(key: "job_address"); ?></label>
                                    <input type="text" class="form-control" name="job_address" id="job_address"
                                        placeholder="Enter address"
                                        value="<?= $editJob ? htmlspecialchars($editJob['job_address']) : '' ?>"
                                        required>
                                </div>

                                <!-- Job zip code  -->
                                <div class="col-md-4">
                                    <label for="name"
                                        class="form-label"><?php echo lang(key: "job_zip_code"); ?></label>
                                    <input type="text" class="form-control" name="job_zip" id="job_zip"
                                        placeholder="Enter zip code"
                                        value="<?= $editJob ? htmlspecialchars($editJob['job_zip']) : '' ?>" required>
                                </div>

                                <!-- Job Status Dropdown -->
                                <div class="col-md-4">
                                    <label for="status"
                                        class="form-label"><?php echo lang(key: "job_status"); ?></label>
                                    <select class="form-control" id="job_status" name="job_status"
                                        value="<?= $editJob ? htmlspecialchars($editJob['job_status']) : '' ?>"
                                        required>
                                        <option value="1"><?php echo lang(key: "job_active"); ?></option>
                                        <option value="0"><?php echo lang(key: "job_inactive"); ?></option>
                                    </select>
                                </div>

                                <!-- Job Hiring Dropdown -->
                                <div class="col-md-4">
                                    <label for="status"
                                        class="form-label"><?php echo lang(key: "job_hiring"); ?></label>
                                    <select class="form-control" id="job_hiring" name="job_hiring"
                                        value="<?= $editJob ? htmlspecialchars($editJob['job_hiring']) : '' ?>"
                                        required>
                                        <option value="1"><?php echo lang(key: "job_yes"); ?>
                                        </option>
                                        <option value="0"><?php echo lang(key: "job_no"); ?>
                                        </option>
                                    </select>
                                </div>

                                <!-- Description -->
                                <!-- <div class="col-md-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" name="job_description" id="job_description" rows="4"
                                        placeholder="Description"><?= $editJob ? htmlspecialchars($editJob['job_description']) : '' ?></textarea>
                                </div> -->

                                <!-- Form Buttons -->
                                <div class="card-footer row my-3">
                                    <div class="col-6">
                                        <a href="?route=modules/jobs/view_jobs" class="btn btn-orange my-1">
                                            <i class="fa fa-times me-2"></i><?php echo lang(key: "job_cancel"); ?>
                                        </a>
                                    </div>
                                    <div class="col-6 text-end">
                                        <button type="submit" name="btnSubmit" class="btn btn-orange my-1">
                                            <i class="fa <?= $editJob ? 'fa-edit' : 'fa-plus-circle' ?> me-2"></i>
                                            <?= $editJob ? 'Update' : 'Save' ?>
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

<!-- CSS STYLES (same as original) -->
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
</style>

<!-- JAVASCRIPT VALIDATION -->
<script>
    $(document).ready(function () {
        $('form').on('submit', function () {
            const name = $('#name').val().trim();
            const expiresAt = $('#expires_at').val();

            if (!name) {
                alert('Job name is required');
                return false;
            }
            if (!expiresAt) {
                alert('Expiration date is required');
                return false;
            }
            return true;
        });
    });
</script>