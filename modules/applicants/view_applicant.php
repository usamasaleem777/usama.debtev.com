<?php
/************** START SECURITY CHECK ***********************/
// Define allowed roles - make sure these variables are defined somewhere in your application
$allowedRoles = array(
					$admin_role, 
					$manager_role,
					$hr
					
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


$applicant_id = $_GET['id'] ?? 0;

$applicant = DB::queryFirstRow(
    "SELECT a.* 
    FROM applicants a
    WHERE a.id = %i",
    $applicant_id
);

// Handle NULL values in education data
$education = DB::queryFirstRow(
    "SELECT * FROM education WHERE applicant_id = %i",
    $applicant_id
) ?? [];

$criminal_history = DB::query(
    "SELECT * FROM criminal_history WHERE applicant_id = %i",
    $applicant_id
) ?? [];


$newApplicants = DB::query("
SELECT applicants.*, positions.position_name
FROM applicants 
LEFT JOIN positions ON applicants.position = positions.id
LIMIT 7
");

$availability = DB::query(
    "SELECT * FROM availability WHERE applicant_id = %i",
    $applicant_id
) ?? [];



// Handle NULL values in other data
$employment_history = DB::query(
    "SELECT * FROM employment_history WHERE applicant_id = %i",
    $applicant_id
) ?? [];

$skills = DB::query(
    "SELECT skill_description FROM skills WHERE applicant_id = %i",
    $applicant_id
) ?? [];

$signature = DB::queryFirstRow(
    "SELECT * FROM application_signatures WHERE applicant_id = %i",
    $applicant_id
) ?? [];

$references = DB::query(
    "SELECT * FROM references_info WHERE applicant_id = %i",
    $applicant_id
) ?? [];


$currentStatus = $applicant['status'] ?? 'Pending';

// Handle Hire Applicant form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hire'])) {
    $applicant_id = $_POST['applicant_id'] ?? 0;

    try {
        DB::startTransaction();

        $applicant = DB::queryFirstRow("SELECT * FROM applicants WHERE id = %i", $applicant_id);
        if (!$applicant)
            throw new Exception("Applicant not found.");

        $role = DB::queryFirstRow("SELECT id FROM roles WHERE name = 'employee'");
        if (!$role){
            $role_id = (int) $role['0'];

        }
else{
        $role_id = (int) $role['id'];
}
       
        // Generate username
        $baseUsername = strtolower(preg_replace('/[^a-z]/i', '', $applicant['first_name'] . $applicant['last_name']));
        $username = $baseUsername;
        $counter = 1;
        while (DB::queryFirstRow("SELECT user_id FROM users WHERE user_name = %s", $username)) {
            $username = $baseUsername . $counter++;
        }
        
        // Create user record
        DB::insert('users', [
            'role_id' => $role_id,
            'user_name' => $username,
            'email' => $applicant['email'],
            'name' => $applicant['first_name'] . ' ' . $applicant['last_name'],
            'password' => $applicant['password'] ?? null,
            'phone' => $applicant['phone_number'] ?? null,
            'picture' => $applicant['picture'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $new_user_id = DB::insertId();

        // Create record for user roles tabel
        DB::insert('user_roles', [
            'user_id' => $new_user_id,
            'role_id' => $role_id,
            'assigned_at' => date('Y-m-d H:i:s')
        ]);


        DB::update('applicants', ['status' => 'hired'], 'id = %i', $applicant_id);
        DB::commit();

        // Redirect to clear POST data
        echo '<script>window.location.href = window.location.href;</script>';
        exit();

    } catch (Exception $e) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => $e->getMessage()];
        echo '<script>window.location.href = window.location.href;</script>';
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $applicant_id = $_POST['applicant_id'];
    $newStatus = $_POST['new_status'];

    try {
        DB::update('applicants', ['status' => $newStatus], 'id = %i', $applicant_id);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Status updated successfully'];
        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}


?>

<style>
    /* Fixed color gradient issue */
    .card-header. {
        background-color: #fe5500 !important;
        background-image: none !important;
    }

    /* Rest of existing styles remain the same */
    body {
        background: #f1f2f6 !important;
    }

    .card {
        border-radius: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card-title {
        margin: 0;
        color: #000;
        font-weight: bold;
        text-transform: uppercase;
    }
</style>



<!-- Content Header (Page header) -->
<div class="main-content app-content mt-0">
    <div class="side-app">
        <!-- CONTAINER -->
        <div class="main-container container-fluid" style="margin-top: 10%;">
            <!-- PAGE-HEADER -->
            <div class="page-header d-flex align-items-center justify-content-between mt-3">
                <h1 class="page-title">Applicant's Data</h1>
                <div>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php" style="color: #fe5500">Home</a></li>
                        <li class="breadcrumb-item"><a href="#" style="color: #fe5500">Applicants</a></li>
                        <li class="breadcrumb-item active">Applicant's Data</li>
                    </ol>
                </div>
            </div>
            <div class="d-flex justify-content-end align-items-center gap-3">
                <?php if ($currentStatus === 'hired'): ?>
                    <button class="btn btn-secondary" disabled style="min-width: 120px;">
                        <i class="fas fa-check-circle me-2"></i> Hired
                    </button>
                <?php else: ?>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="statusDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false"
                            style="min-width: 140px; background-color: <?=
                                ['shortlisted' => '#28a745', 'rejected' => '#dc3545', 'pending' => '#ffc107'][$currentStatus] ?? '#6c757d' ?>">
                            <?= ucfirst($currentStatus) ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                            <li><a class="dropdown-item status-item" href="#" data-status="hired">Hire</a></li>
                            <li><a class="dropdown-item status-item" href="#" data-status="shortlisted">Shortlisted</a>
                            </li>
                            <li><a class="dropdown-item status-item" href="#" data-status="rejected">Rejected</a></li>
                            <li><a class="dropdown-item status-item" href="#" data-status="pending">Pending</a></li>
                        </ul>
                    </div>
                    <form method="post" id="hireForm" style="display: none;">
                        <input type="hidden" name="applicant_id" value="<?= $applicant['id'] ?>">
                        <input type="hidden" name="hire">
                    </form>
                <?php endif; ?>
            </div>
            <!-- PAGE-HEADER END -->
            <div class="row">
                <div class="col-12">
                    <div class="card rounded-4">
                        <div class="card-body">
                            <!-- Content Header (Page header) -->
                            <div class="main-content app-content mt-0">
                                <div class="side-app">
                                    <!-- CONTAINER -->
                                    <div class="main-container container-fluid">
                                       
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card rounded-4">
                                                    <div class="card-body">


                                                        <!-- Personal Information Card with NULL handling -->
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h3 class="card-title">
                                                                    <?php echo lang("formview_personal_Information"); ?>
                                                                </h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_name"); ?></strong>
                                                                            <?= htmlspecialchars(($applicant['first_name'] ?? '') . ' ' . ($applicant['last_name'] ?? '')) ?>
                                                                        </p>
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_SSN"); ?></strong>
                                                                            <?= isset($applicant['ssn']) ? substr($applicant['ssn'], 0, 3) . '-XX-XXXX' : 'N/A' ?>
                                                                        </p>
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_job_type"); ?></strong>
                                                                            <?= htmlspecialchars($applicant['employment_type'] ?? 'N/A') ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_phone"); ?></strong>
                                                                            <?= htmlspecialchars($applicant['phone_number'] ?? 'N/A') ?>
                                                                        </p>
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_legel_to_work"); ?></strong>
                                                                            <?= ($applicant['legal_us_work_eligibility'] ?? false) ? 'Yes' : 'No' ?>
                                                                        </p>
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_starting_date"); ?></strong>
                                                                            <?= htmlspecialchars($applicant['available_start_date'] ?? 'N/A') ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_position"); ?></strong>
                                                                            <?= htmlspecialchars($newApplicants['position_name'] ?? 'N/A') ?>
                                                                        </p>
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_salary"); ?></strong>
                                                                            <?= htmlspecialchars($applicant['salary'] ?? 'N/A') ?>
                                                                        </p>
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_valid_passport/id"); ?></strong>
                                                                            <?= $applicant['passport_or_id'] ? 'Yes' : 'No' ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Address Information with NULL handling -->
                                                        <div class="card mb-4">
                                                            <div class="card-header ">
                                                                <h3 class="card-title">
                                                                    <?php echo lang("formview_address_information"); ?>
                                                                </h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_streat"); ?></strong>
                                                                            <?= htmlspecialchars($applicant['street_address'] ?? 'N/A') ?>
                                                                        </p>
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_city"); ?></strong>
                                                                            <?= htmlspecialchars($applicant['city'] ?? 'N/A') ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_state"); ?></strong>
                                                                            <?= htmlspecialchars($applicant['state'] ?? 'N/A') ?>
                                                                        </p>
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_zip_code"); ?></strong>
                                                                            <?= htmlspecialchars($applicant['zip_code'] ?? 'N/A') ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_email"); ?></strong>
                                                                            <?= htmlspecialchars($applicant['email'] ?? 'N/A') ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Education Card with NULL handling -->
                                                        <div class="card mb-4">
                                                            <div class="card-header ">
                                                                <h3 class="card-title">
                                                                    <?php echo lang("formview_education"); ?></h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <?php if (!empty($education)): ?>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_high_school"); ?></strong>
                                                                                <?= htmlspecialchars($education['high_school_name'] ?? 'N/A') ?>
                                                                            </p>
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_graduated"); ?></strong>
                                                                                <?= ($education['high_school_graduate'] ?? false) ? 'Yes' : 'No' ?>
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_college"); ?></strong>
                                                                                <?= htmlspecialchars($education['college_name'] ?? 'N/A') ?>
                                                                            </p>
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_degree"); ?></strong>
                                                                                <?= htmlspecialchars($education['college_degree'] ?? 'N/A') ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <p class="text-muted">
                                                                        <?php echo lang("formview_No_education_information_available"); ?>
                                                                    </p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <!-- Criminal History -->
                                                        <div class="card mb-4">
                                                            <div class="card-header">
                                                                <h3 class="card-title">
                                                                    <?php echo lang("formview_criminal_history"); ?>
                                                                </h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <?php if (!empty($criminal_history)):
                                                                    foreach ($criminal_history as $record): ?>
                                                                        <div class="border p-3 mb-3">
                                                                            <!-- Conviction Status -->
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_has_convictions"); ?></strong>
                                                                                <?= $record['has_conviction'] ? 'Yes' : 'No' ?>
                                                                            </p>

                                                                            <?php if ($record['has_conviction']): ?>
                                                                                <!-- Conviction Details -->
                                                                                <p><strong
                                                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_conviction_date"); ?></strong>
                                                                                    <?= isset($record['conviction_date']) ? date('M Y', strtotime($record['conviction_date'])) : 'N/A' ?>
                                                                                </p>

                                                                                <p><strong
                                                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_location"); ?></strong>
                                                                                    <?= htmlspecialchars(string: $record['conviction_location'] ?? 'N/A') ?>
                                                                                </p>

                                                                                <p><strong
                                                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_convicted_when"); ?></strong><br>
                                                                                    <?= nl2br(htmlspecialchars($record['convicted_when'] ?? 'Not specified')) ?>
                                                                                </p>

                                                                                <p><strong
                                                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_convicted_when"); ?></strong><br>
                                                                                    <?= nl2br(htmlspecialchars($record['convicted_where'] ?? 'Not specified')) ?>
                                                                                </p>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    <?php endforeach;
                                                                else: ?>
                                                                    <p class="text-muted">
                                                                        <?php echo lang("formview_no_criminal_history_recorded"); ?>
                                                                    </p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>

                                                        <!-- Work eligibilty -->

                                                        <div class="card mb-4">
                                                            <div class="card-header">
                                                                <h3 class="card-title">
                                                                    <?php echo lang("formview_Work_Eligibility_Verification"); ?>
                                                                </h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <?php if (!empty($applicant)): ?>
                                                                    <div class="border p-3 mb-3">
                                                                        <!-- Verification Date -->
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_verification_date"); ?></strong>
                                                                            <?= htmlspecialchars(string: $applicant['available_start_date'] ?? 'N/A') ?>
                                                                        </p>

                                                                        <!-- Age Verification -->
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_over_18_Years_Old"); ?></strong>
                                                                            <?= $applicant['over_18'] ? 'Yes' : 'No' ?>
                                                                        </p>
                                                                    </div>
                                                                <?php
                                                                else: ?>
                                                                    <p class="text-muted">
                                                                        <?php echo lang("formview_No_work_eligibility_verification_records_found"); ?>
                                                                    </p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>

                                                        <div class="card mb-4">
                                                            <div class="card-header">
                                                                <h3 class="card-title">
                                                                    <?php echo lang("formview_availability"); ?></h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <?php if (!empty($availability)):
                                                                    foreach ($availability as $slot): ?>
                                                                        <div class="border p-3 mb-3">
                                                                            <!-- Day and Time -->
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_day"); ?></strong>
                                                                                <?= date('l', strtotime($slot['day'])) ?>
                                                                                <!-- Converts day number to name -->
                                                                            </p>

                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_available_from"); ?>Available
                                                                                    From:</strong>
                                                                                <?= date('h:i A', strtotime($slot['time_from'])) ?>
                                                                            </p>

                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_available_to"); ?>:</strong>
                                                                                <?= date('h:i A', strtotime($slot['time_to'])) ?>
                                                                            </p>

                                                                            <!-- Hours and Special Requests -->
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_Total_hours"); ?></strong>
                                                                                <?= htmlspecialchars($slot['total_hours'] ?? 0) ?>
                                                                                hours
                                                                            </p>

                                                                            <?php if (!empty($slot['special_requests'])): ?>
                                                                                <p><strong
                                                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_special_requests"); ?></strong><br>
                                                                                    <?= nl2br(htmlspecialchars($slot['special_requests'])) ?>
                                                                                </p>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    <?php endforeach;
                                                                else: ?>
                                                                    <p class="text-muted">
                                                                        <?php echo lang("formview_No_availability_information_provided"); ?>
                                                                    </p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <!-- Employment History with NULL handling -->
                                                        <div class="card mb-4">
                                                            <div class="card-header">
                                                                <h3 class="card-title">
                                                                    <?php echo lang("formview_empolyment_histery"); ?>
                                                                </h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <?php if (!empty($employment_history)):
                                                                    foreach ($employment_history as $job): ?>
                                                                        <div class="border p-3 mb-3">
                                                                            <h5><?= htmlspecialchars($job['employer_name'] ?? 'Unknown Employer') ?>
                                                                            </h5>

                                                                            <!-- Basic Information -->
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_job_title"); ?></strong>
                                                                                <?= htmlspecialchars($job['job_title'] ?? 'N/A') ?>
                                                                            </p>
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_duration"); ?></strong>
                                                                                <?= isset($job['from_date']) ? date('M Y', strtotime($job['from_date'])) : 'N/A' ?>
                                                                                -
                                                                                <?= $job['to_date'] ? date('M Y', strtotime($job['to_date'])) : 'Present' ?>
                                                                            </p>

                                                                            <!-- Location Information -->
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_address"); ?></strong>
                                                                                <?= htmlspecialchars($job['address'] ?? 'N/A') ?>,
                                                                                <?= htmlspecialchars($job['city'] ?? '') ?>
                                                                                <?= htmlspecialchars($job['state'] ?? '') ?>
                                                                                <?= htmlspecialchars($job['zip_code'] ?? '') ?>
                                                                            </p>

                                                                            <!-- Compensation -->
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_salary_range"); ?></strong>
                                                                                $<?= ($job['starting_pay'] ?? 2) ?> -
                                                                                $<?= ($job['ending_pay'] ?? 2) ?>
                                                                            </p>

                                                                            <!-- Supervisor Information -->
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_supervisor"); ?></strong>
                                                                                <?= htmlspecialchars($job['supervisor_name'] ?? 'N/A') ?>
                                                                                <?php if (!empty($job['supervisor_phone'])): ?>
                                                                                    (<?= ($job['supervisor_phone']) ?>)
                                                                                <?php endif; ?>
                                                                            </p>

                                                                            <!-- Job Details -->
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_duties"); ?></strong><br>
                                                                                <?= nl2br(htmlspecialchars($job['duties'] ?? 'No duties specified')) ?>
                                                                            </p>

                                                                            <!-- Reason for Leaving -->
                                                                            <p><strong
                                                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_reason_for_leaving"); ?></strong><br>
                                                                                <?= nl2br(htmlspecialchars($job['reason_for_leaving'] ?? 'Not specified')) ?>
                                                                            </p>
                                                                        </div>
                                                                    <?php endforeach;
                                                                else: ?>
                                                                    <p class="text-muted">
                                                                        <?php echo lang("formview_no_employment_history_available"); ?>
                                                                    </p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>



                                                        <!-- Skills Card with NULL handling -->
                                                        <div class="card mb-4">
                                                            <div class="card-header">
                                                                <h3 class="card-title">
                                                                    <?php echo lang("formview_skills_&_qualifications"); ?>
                                                                </h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <?php if (!empty($skills)): ?>
                                                                    <ul class="list-unstyled">
                                                                        <?php foreach ($skills as $skill): ?>
                                                                            <li>
                                                                                <span
                                                                                    style="font-weight: bold; font-size: 1.2em; color: #000;">•</span>
                                                                                <?= htmlspecialchars($skill['skill_description'] ?? 'Unknown Skill') ?>
                                                                            </li>
                                                                        <?php endforeach; ?>
                                                                    </ul>
                                                                <?php else: ?>
                                                                    <p class="text-muted">
                                                                        <?php echo lang("formview_no_skill_listed"); ?></p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>


                                                        <!-- References Card with NULL handling -->
                                                        <div class="card mb-4">
                                                            <div class="card-header ">
                                                                <h3 class="card-title">
                                                                    <?php echo lang("formview_references"); ?></h3>
                                                            </div>

                                                            <?php if (!empty($references)):
                                                                $refCount = 1;
                                                                foreach ($references as $ref): ?>
                                                                    <div class="border p-3 mb-3">
                                                                        <h5 style="font-weight: 900; color: #000;">
                                                                            <?php echo lang("formview_reference_#"); ?>        <?= $refCount++ ?>
                                                                        </h5>
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_name"); ?></strong>
                                                                            <?= htmlspecialchars($ref['name'] ?? 'N/A') ?></p>
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_occupation"); ?></strong>
                                                                            <?= htmlspecialchars($ref['occupation'] ?? 'N/A') ?>
                                                                        </p>
                                                                        <p><strong
                                                                                style="font-weight: 900; color: #000;"><?php echo lang("formview_phone"); ?></strong>
                                                                            <?= htmlspecialchars($ref['phone_number'] ?? 'N/A') ?>
                                                                        </p>
                                                                    </div>
                                                                <?php endforeach;
                                                            else: ?>
                                                                <p class="text-muted">
                                                                    <?php echo lang("formview_no_references_provided"); ?>
                                                                </p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <!-- Signatures -->

                                                    <div class="card mb-4">
                                                        <div class="card-header">
                                                            <h3 class="card-title">
                                                                <?php echo lang("formview_applicaion_signature"); ?>
                                                            </h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <?php if (!empty($signature)): ?>
                                                                <div class="border p-3 mb-3">
                                                                    <!-- Signature -->
                                                                    <p>
                                                                        <img src="<?php echo htmlspecialchars($record['signature']); ?>" alt="Signature" style="max-width: 100%;">
                                                                    </p>

                                                                    <!-- Signature date -->
                                                                    <p><strong
                                                                            style="font-weight: 900; color: #000;"><?php echo lang("formview_date"); ?></strong>
                                                                        <?= $signature['signature_date'] ?? 'N/A' ?>
                                                                    </p>
                                                                </div>
                                                            <?php
                                                            else: ?>
                                                                <p class="text-muted">
                                                                    <?php echo lang("formview_no_signature_found"); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                <script>
                                    // Fix the broken event listener structure
                                    document.querySelectorAll('.status-item').forEach(item => {
                                        item.addEventListener('click', function (e) {
                                            e.preventDefault();
                                            const status = this.dataset.status;
                                            const applicantId = <?= $applicant['id'] ?>;

                                            if (status === 'hired') {
                                                if (confirm('Do you want to hire this applicant?')) {
                                                    document.getElementById('hireForm').submit();
                                                }
                                            } else {
                                                fetch(window.location.href, {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/x-www-form-urlencoded',
                                                    },
                                                    body: new URLSearchParams({
                                                        applicant_id: applicantId,
                                                        update_status: 1,
                                                        new_status: status
                                                    })
                                                })
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            window.location.reload();
                                                        } else {
                                                            alert('Error: ' + (data.error || 'Unknown error'));
                                                        }
                                                    })
                                                    .catch(error => {
                                                        console.error('Error:', error);
                                                        window.location.reload();

                                                    });
                                            }
                                        });
                                    });
                                   
                                </script>
                                </body>

                                </html>