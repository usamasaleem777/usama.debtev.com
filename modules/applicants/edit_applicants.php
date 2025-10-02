<?php
/************** START SECURITY CHECK ***********************/
// Define allowed roles - make sure these variables are defined somewhere in your application
$allowedRoles = array(
					$admin_role, 
					$manager_role
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


$applicant_id = $_GET['id'] ? $_GET['id'] : 0;

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
);
if (!$education) {
    $education = [];
}

$criminal_history = DB::query(
    "SELECT * FROM criminal_history WHERE applicant_id = %i",
    $applicant_id
);
if (!$criminal_history) {
    $criminal_history = [];
}

$availability = DB::query(
    "SELECT * FROM availability WHERE applicant_id = %i",
    $applicant_id
);
if (!$availability) {
    $availability = [];
}

// Handle NULL values in other data
$employment_history = DB::query(
    "SELECT * FROM employment_history WHERE applicant_id = %i",
    $applicant_id
);
if (!$employment_history) {
    $employment_history = [];
}

$skills = DB::query(
    "SELECT skill_description FROM skills WHERE applicant_id = %i",
    $applicant_id
);
if (!$skills) {
    $skills = [];
}

$signature = DB::queryFirstRow(
    "SELECT * FROM application_signatures WHERE applicant_id = %i",
    $applicant_id
);
if (!$signature) {
    $signature = [];
}

$references = DB::query(
    "SELECT * FROM references_info WHERE applicant_id = %i",
    $applicant_id
);
if (!$references) {
    $references = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $employmentType = isset($_POST['employment_type']) ? $_POST['employment_type'] : 'N/A';
        $workEligibility = isset($_POST['legal_us_work_eligibility']) ? 1 : 0;
        $psEligibility = isset($_POST['passport_or_id']) ? 1 : 0;

        DB::startTransaction();

        
        DB::update('applicants', [
            'last_name' => isset($_POST['last_name']) ? $_POST['last_name'] : '',
            'user_id' => isset($_POST['user_id']) ? $_POST['user_id'] : '',
            'first_name' => isset($_POST['first_name']) ? $_POST['first_name'] : '',
            'street_address' => isset($_POST['street']) ? $_POST['street'] : '',
            'city' => isset($_POST['city']) ? $_POST['city'] : '',
            'dob' => isset($_POST['dob']) ? $_POST['dob'] : '',
            'state' => isset($_POST['state']) ? $_POST['state'] : '',
            'zip_code' => isset($_POST['zip']) ? $_POST['zip'] : '',
            'phone_number' => isset($_POST['phone_number']) ? $_POST['phone_number'] : '',
            'email' => isset($_POST['email']) ? $_POST['email'] : '',
            'passport_or_id' => isset($_POST['ps_id']) ? 1 : 0,
            'legal_us_work_eligibility' => isset($_POST['eligibility']) ? 1 : 0,
            'salary' => isset($_POST['salary']) ? $_POST['salary'] : '',
            'employment_type' => isset($_POST['employment_type']) ? $_POST['employment_type'] : 'Full-Time',
            'available_start_date' => isset($_POST['start_date']) ? $_POST['start_date'] : null,
            'over_18' => isset($_POST['age']) ? 1 : 0
        ], 'id = %i', $applicant_id);

       
        DB::update('criminal_history', [
            'has_conviction' => isset($_POST['has_conviction']) ? 1 : 0,
            'conviction_date' => isset($_POST['conviction_date']) ? $_POST['conviction_date'] : null,
            'conviction_location' => isset($_POST['location']) ? $_POST['location'] : null,
            'convicted_when' => isset($_POST['convicted_when']) ? $_POST['convicted_when'] : null,
            'convicted_where' => isset($_POST['convicted_where']) ? $_POST['convicted_where'] : null,
        ], 'applicant_id = %i', $applicant_id);

      
        DB::update('education', [
            'high_school_name' => isset($_POST['school']) ? $_POST['school'] : '',
            'high_school_city' => isset($_POST['school_city']) ? $_POST['school_city'] : '',
            'high_school_state' => isset($_POST['school_state']) ? $_POST['school_state'] : '',
            'high_school_graduate' => isset($_POST['school_grad']) ? 1 : 0,
            'college_name' => isset($_POST['college']) ? $_POST['college'] : null,
            'college_city' => isset($_POST['college_city']) ? $_POST['college_city'] : null,
            'college_state' => isset($_POST['college_state']) ? $_POST['college_state'] : null,
            'college_graduate' => isset($_POST['college_grad']) ? $_POST['college_grad'] : 'No',
            'college_degree' => isset($_POST['degree']) ? $_POST['degree'] : null,
            'college_major' => isset($_POST['major']) ? $_POST['major'] : null,
            'currently_enrolled' => isset($_POST['currently_enrolled']) ? $_POST['currently_enrolled'] : 'No',
            'enrolled_school_name' => isset($_POST['school_info']) ? $_POST['school_info'] : null,
            'expected_degree_date' => isset($_POST['expected_degree_date']) ? $_POST['expected_degree_date'] : null
        ], 'applicant_id = %i', $applicant_id);

        
        for ($i = 1; $i <= 3; $i++) {
            $refKey = "ref{$i}_name";
            if (!empty($_POST[$refKey])) {
                DB::insert('references_info', [
                    'applicant_id' => $applicant_id,
                    'name' => $_POST[$refKey],
                    'occupation' => isset($_POST["ref{$i}_occupation"]) ? $_POST["ref{$i}_occupation"] : null,
                    'phone_number' => isset($_POST["ref{$i}_phone"]) ? $_POST["ref{$i}_phone"] : null
                ]);
            }
        }

        // Employment history insertion
        if (!empty($_POST['employer_name']) && is_array($_POST['employer_name'])) {
            foreach ($_POST['employer_name'] as $i => $employerName) {
                $fromDate = isset($_POST['employer_from'][$i]) ? $_POST['employer_from'][$i] : null;
                $toDate = isset($_POST['employer_to'][$i]) ? $_POST['employer_to'][$i] : null;

                if (!empty($fromDate) && !empty($toDate)) {
                    $fromTimestamp = strtotime($fromDate);
                    $toTimestamp = strtotime($toDate);

                    if ($fromTimestamp === false || $toTimestamp === false || $fromTimestamp > $toTimestamp) {
                        continue;
                    }
                }
                
                $employerAddress = isset($_POST['employer_address'][$i])
                    ? explode(', ', $_POST['employer_address'][$i], 2)
                    : [null, null];

                try {
                    DB::insert('employment_history', [
                        'applicant_id' => $applicant_id,
                        'employer_name' => $employerName,
                        'job_title' => isset($_POST['job_title'][$i]) ? $_POST['job_title'][$i] : null,
                        'duties' => isset($_POST['duties'][$i]) ? $_POST['duties'][$i] : null,
                        'address' => isset($employerAddress[0]) ? $employerAddress[0] : null,
                        'city' => isset($employerAddress[1]) ? $employerAddress[1] : (isset($_POST['city']) ? $_POST['city'] : null),
                        'state' => isset($_POST['employer_location'][$i]) ? $_POST['employer_location'][$i] : (isset($_POST['state']) ? $_POST['state'] : null),
                        'zip_code' => isset($_POST['zip_code']) ? $_POST['zip_code'] : null,
                        'from_date' => $fromDate,
                        'to_date' => $toDate,
                        'starting_pay' => isset($_POST['startPay'][$i]) ? $_POST['startPay'][$i] : null,
                        'ending_pay' => isset($_POST['endPay'][$i]) ? $_POST['endPay'][$i] : null,
                        'supervisor_name' => isset($_POST['supervisor'][$i]) ? $_POST['supervisor'][$i] : null,
                        'supervisor_phone' => isset($_POST['employer_phone'][$i]) ? $_POST['employer_phone'][$i] : null,
                        'reason_for_leaving' => isset($_POST['reason_leaving'][$i]) ? $_POST['reason_leaving'][$i] : null
                    ]);
                } catch (Exception $e) {
                    error_log("DB Insert Error: " . $e->getMessage());
                }
            }
        }

        DB::commit();
        ob_end_clean();
        
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Applicant updated successfully'];
        echo '<script>window.location.href = "index.php?route=modules/applicants/list_applicants";</script>';        exit();
        
    } catch (Exception $e) {
        DB::rollback();
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: ' . $e->getMessage()];
    }
}
?>


<style>
    /* Fixed color gradient issue */
    .card-header {
        background-color: #fe5500 !important;
        background-image: none !important;
        color: white !important;
    }

    body {
        background: #f1f2f6 !important;
    }

    .card {
        border-radius: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }

    .card-title {
        margin: 0;
        color: white;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 16px;
    }

    .form-control {
        border-radius: 0;
        padding: 8px 12px;
        font-size: 14px;
    }

    .btn {
        border-radius: 0;
        padding: 8px 20px;
        font-size: 14px;
    }

    .btn {
        margin-bottom: 20px !important;
    }

    .btn-primary {
        background-color: #fe5500;
        border-color: #fe5500;
    }

    .btn-primary:hover {
        background-color: #fe5500;
        color: #f1f2f6;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    /* Mobile responsive styles */
    @media (max-width: 767px) {
        .container-fluid {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }

        .card-body {
            padding: 15px !important;
        }

        .col-md-4,
        .col-md-6 {
            padding-left: 8px !important;
            padding-right: 8px !important;
            margin-bottom: 10px;
        }

        .page-title {
            font-size: 18px !important;
            margin-bottom: 10px !important;
        }

        .breadcrumb {
            padding: 5px 0 !important;
            font-size: 12px !important;
        }

        .form-control {
            padding: 6px 10px !important;
            font-size: 13px !important;
        }

        .btn {
            padding: 6px 12px !important;
            font-size: 13px !important;
        }
    }

    @media (max-width: 360px) {
        .container-fluid {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }

        .card-body {
            padding: 10px !important;
        }

        .col-md-4,
        .col-md-6 {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }

        .page-title {
            font-size: 16px !important;
        }

        .breadcrumb {
            font-size: 11px !important;
        }

        .form-control {
            padding: 5px 8px !important;
            font-size: 12px !important;
        }

        .btn {
            padding: 5px 10px !important;
            font-size: 12px !important;
            margin-bottom: 5px;
        }

        .text-end {
            text-align: left !important;
        }

        .text-end .btn {
            display: block;
            width: 100%;
            margin-bottom: 5px;
        }
    }

    .section-header {
    background-color: #fe5500;
    color: white;
    padding: 10px 15px;
    margin: 20px 0 0 0; /* Removed bottom margin */
    font-weight: bold;
    text-transform: uppercase;
    font-size: 16px;
    border-top-left-radius: 5px; 
    border-top-right-radius: 5px; 
}

.section-content {
    padding: 15px;
    border: 1px solid #ddd;
    margin-top: 0; /* Ensures no top margin */
    margin-bottom: 20px;
    border-top: none; /* Removes the top border to prevent double border */
    border-bottom-left-radius: 5px; 
    border-bottom-right-radius: 5px; 
}
</style>
<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- Content Header (Page header) -->
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
            <div style="margin-top: 15px;">
                <ol class="breadcrumb float-sm-right mt-2">
                    <!-- Home breadcrumb -->
                    <li class="breadcrumb-item">
                        <a href="index.php" style="color: #fe5500"><i
                                class="fas fa-home me-1"></i><?php echo lang("formview_home"); ?></a>
                    </li>
                    <!-- Position breadcrumb -->
                    <li class="breadcrumb-item">
                        <a href="#" style="color: #fe5500"><?php echo lang(key: "formview_applicants"); ?></a>
                    </li>
                    <!-- View position breadcrumb -->
                    <li class="breadcrumb-item">
                        <a href="#" style="color: #fe5500"><?php echo lang("formview_applicants_data"); ?></a>
                    </li>
                </ol>
            </div>
        </div>
        <!-- CONTAINER -->
        <div class="main-container container-fluid" style="margin-top: 5%;">
            <!-- PAGE-HEADER -->
            <div class="page-header d-flex align-items-center justify-content-between mt-3">
                <h1 class="page-title"> <?php echo lang("formedit_applicants_data"); ?></h1>
            </div>
            <div class="d-flex justify-content-end align-items-center gap-3">
            </div>
            <!-- PAGE-HEADER END -->
            <div class="row">
                <div class="col-12">
                    <div class="card rounded-4">
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                            <div class="text-end mt-4">
    <button type="submit" 
            class="btn btn-primary ms-1 me-1" 
            style="border-radius: 8px; min-width: 120px;">
        <i class="fas fa-save me-1"></i><?php echo lang("formview_Save_changes"); ?>
    </button>
    <a href="index.php?route=modules/applicants/list_applicants"
       class="btn btn-secondary ms-1 me-1" 
       style="border-radius: 8px; min-width: 120px;">
        <i class="fas fa-times me-1"></i><?php echo lang("formview_cancel"); ?>
    </a>
</div>
                              <!-- Personal Information Section -->
<!-- Personal Information Section -->
<div class="section-header">
    <?php echo lang("formview_personal_Information"); ?>
</div>
<div class="section-content">
    <div class="row">
        
        <div class="col-md-3">
            <p><strong style="font-weight: 900; color: #000;">User_ID</strong>
                <input type="text" class="form-control" name="user_id"
                    value="<?php echo isset($applicant['user_id']) ? htmlspecialchars($applicant['user_id']) : ''; ?>">
            </p>
        </div>

        <div class="col-md-3">
            <p><strong style="font-weight: 900; color: #000;"><?php echo lang("form_first_name"); ?></strong>
                <input type="text" class="form-control" name="first_name"
                    value="<?php echo isset($applicant['first_name']) ? htmlspecialchars($applicant['first_name']) : ''; ?>">
            </p>
        </div>
        
        
        <div class="col-md-3">
            <p><strong style="font-weight: 900; color: #000;"><?php echo lang("form_last_name"); ?></strong>
                <input type="text" class="form-control" name="last_name"
                    value="<?php echo isset($applicant['last_name']) ? htmlspecialchars($applicant['last_name']) : ''; ?>">
            </p>
        </div>
        
        
        <div class="col-md-3">
            <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_email"); ?></strong>
                <input type="email" class="form-control" name="email"
                    value="<?php echo isset($applicant['email']) ? htmlspecialchars($applicant['email']) : ''; ?>">
            </p>
        </div>

         <div class="col-md-3">
            <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_dob"); ?></strong>
                <input type="date" class="form-control" name="dob"
                    value="<?php echo isset($applicant['dob']) ? htmlspecialchars($applicant['dob']) : ''; ?>">
            </p>
        </div>
        
        
        <div class="col-md-3">
            <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_phone"); ?></strong>
                <input type="text" class="form-control" name="phone_number"
                    value="<?php echo isset($applicant['phone_number']) ? htmlspecialchars($applicant['phone_number']) : ''; ?>">
            </p>
        </div>
    </div>
    
    <div class="row">
        
        <div class="col-md-3">
            <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_salary"); ?></strong>
                <input type="text" class="form-control" name="salary"
                    value="<?php echo isset($applicant['salary']) ? htmlspecialchars($applicant['salary']) : ''; ?>">
            </p>
        </div>
        
        
        <div class="col-md-3">
            <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_starting_date"); ?></strong>
                <input type="date" class="form-control" name="start_date"
                    value="<?php echo isset($applicant['available_start_date']) ? htmlspecialchars($applicant['available_start_date']) : ''; ?>">
            </p>
        </div>
        
     
        <div class="col-md-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="ps_id"
                    <?php echo (isset($applicant['passport_or_id']) && $applicant['passport_or_id']) ? 'checked' : ''; ?>>
                <label class="form-check-label">
                    <?php echo lang("form_ps_id"); ?>
                </label>
            </div>
        </div>
        
    
        <div class="col-md-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="eligibility"
                    <?php echo (isset($applicant['legal_us_work_eligibility']) && $applicant['legal_us_work_eligibility']) ? 'checked' : ''; ?>>
                <label class="form-check-label">
                    <?php echo lang("formview_legel_to_work"); ?>
                </label>
            </div>
        </div>
    </div>
</div>

<!-- Address Information Section -->
<div class="section-header">
    <?php echo lang("formview_address_information"); ?>
</div>
<div class="section-content">
    <div class="row">
        
        <div class="col-md-12">
            <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_streat"); ?></strong>
                <input type="text" class="form-control" name="street"
                    value="<?php echo isset($applicant['street_address']) ? htmlspecialchars($applicant['street_address']) : 'N/A'; ?>">
            </p>
        </div>
    </div>
    <div class="row">
        
        <div class="col-md-4">
            <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_city"); ?></strong>
                <input type="text" class="form-control" name="city"
                    value="<?php echo isset($applicant['city']) ? htmlspecialchars($applicant['city']) : 'N/A'; ?>">
            </p>
        </div>
        
        
        <div class="col-md-4">
            <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_state"); ?></strong>
                <input type="text" class="form-control" name="state"
                    value="<?php echo isset($applicant['state']) ? htmlspecialchars($applicant['state']) : 'N/A'; ?>">
            </p>
        </div>
        
       
        <div class="col-md-4">
            <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_zip_code"); ?></strong>
                <input type="number" class="form-control" name="zip"
                    value="<?php echo isset($applicant['zip_code']) ? htmlspecialchars($applicant['zip_code']) : 'N/A'; ?>">
            </p>
        </div>
    </div>
</div>

                                <!-- Education Section -->
                                <div class="section-header">
                                    <?php echo lang("formview_education"); ?>
                                </div>
                                <div class="section-content">
                                    <?php if (!empty($education)): ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong
                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_high_school"); ?></strong>
                                                    <input type="text" class="form-control"
                                                        name="school"
                                                        value="<?php echo isset($education['high_school_name']) ? htmlspecialchars($education['high_school_name']) : 'N/A'; ?>">
                                                </p>
                                                <p><strong
                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_graduated"); ?></strong>
                                                        <input class="form-check-input"
                                                    type="checkbox" name="graduated"
                                                    <?php echo (isset($applicant['high_school_graduate']) && $applicant['high_school_graduate']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label">
                                                </label>
                                                </p>
                                                <p><strong
                                                style="font-weight: 900; color: #000;"><?php echo lang("form_GED"); ?></strong>
                                                <input class="form-check-input"
                                                    type="checkbox" name="ps_id"
                                                    <?php echo (isset($applicant['ged']) && $applicant['ged']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label">
                                                </label>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong
                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_college"); ?></strong>
                                                    <input type="text" class="form-control"
                                                        name="college"
                                                        value="<?php echo isset($education['college_name']) ? htmlspecialchars($education['college_name']) : 'N/A'; ?>">
                                                </p>
                                                <p><strong
                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_degree"); ?></strong>
                                                    <input type="text" class="form-control"
                                                        name="degree"
                                                        value="<?php echo isset($education['college_degree']) ? htmlspecialchars($education['college_degree']) : 'N/A'; ?>">
                                                </p>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">
                                            <?php echo lang("formview_No_education_information_available"); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Criminal History Section -->
                                <div class="section-header">
    <?php echo lang("formview_criminal_history"); ?>
</div>
<div class="section-content">
    <?php if (!empty($criminal_history)):
        foreach ($criminal_history as $record): ?>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input"
                        type="checkbox" name="has_conviction"
                        value="1" <?php echo isset($record['has_conviction']) && $record['has_conviction'] ? 'checked' : ''; ?>>
                    <label
                        class="form-check-label"><?php echo lang("formview_has_convictions"); ?></label>
                </div>

                <?php if (isset($record['has_conviction']) && $record['has_conviction']): ?>
                    <div class="row">
                        
                        <div class="col-md-4">
                            <p><strong
                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_conviction_date"); ?></strong>
                                <input type="date" class="form-control"
                                    name="conviction_date"
                                    value="<?php echo isset($record['conviction_date']) ? date('M Y', strtotime($record['conviction_date'])) : 'N/A'; ?>">
                            </p>
                        </div>
                        
                        <div class="col-md-4">
                            <p><strong
                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_convicted_when"); ?></strong><br>
                                <input type="text" class="form-control"
                                    name="conviction_when"
                                    value="<?php echo isset($record['convicted_when']) ? nl2br(htmlspecialchars($record['convicted_when'])) : 'Not specified'; ?>">
                            </p>
                        </div>
                        
                        <div class="col-md-4">
                            <p><strong
                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_convicted_where"); ?></strong><br>
                                <input type="text" class="form-control"
                                    name="conviction_where"
                                    value="<?php echo isset($record['convicted_where']) ? nl2br(htmlspecialchars($record['convicted_where'])) : 'Not specified'; ?>">
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong
                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_location"); ?></strong>
                                <input type="text" class="form-control"
                                    name="location"
                                    value="<?php echo isset($record['conviction_location']) ? htmlspecialchars($record['conviction_location']) : 'N/A'; ?>">
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach;
    else: ?>
        <p class="text-muted">
            <?php echo lang("formview_no_criminal_history_recorded"); ?>
        </p>
    <?php endif; ?>
</div>
                                <!-- Work eligibilty Section -->
                                <div class="section-header">
    <?php echo lang("formview_Work_Eligibility_Verification"); ?>
</div>
<div class="section-content">
    <?php if (!empty($applicant)): ?>
        <div class="row">
            
            <div class="col-md-6">
                <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_verification_date"); ?></strong>
                    <input type="date" class="form-control"
                        name="verification_date"
                        value="<?php echo isset($applicant['available_start_date']) ? htmlspecialchars($applicant['available_start_date']) : 'N/A'; ?>">
                </p>
            </div>
            
            <div class="col-md-6">
                <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_over_18_Years_Old"); ?></strong>
                    <input type="text" class="form-control"
                        name="age"
                        value="<?php echo isset($applicant['over_18']) ? ($applicant['over_18'] ? 'Yes' : 'No') : 'No'; ?>">
                </p>
            </div>
        </div>
    <?php else: ?>
        <p class="text-muted">
            <?php echo lang("formview_No_work_eligibility_verification_records_found"); ?>
        </p>
    <?php endif; ?>
</div>
                                <!-- Employment History Section -->
                                <div class="section-header">
                                    <?php echo lang("formview_empolyment_histery"); ?>
                                </div>
                                <div class="section-content">
                                    <?php if (!empty($employment_history)):
                                        foreach ($employment_history as $index => $job): ?>
                                            <div class="mb-3">
                                                <h5><input type="hidden"
                                                        name="employment[<?php echo $index; ?>][id]"
                                                        value="<?php echo isset($job['id']) ? $job['id'] : ''; ?>">
                                                    <input type="text" class="form-control"
                                                        name="employment[<?php echo $index; ?>][employer_name]"
                                                        value="<?php echo isset($job['employer_name']) ? htmlspecialchars($job['employer_name']) : ''; ?>">
                                                </h5>

                                                <p><strong
                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_job_title"); ?></strong>
                                                    <input type="text" class="form-control"
                                                        name="job_title"
                                                        value="<?php echo isset($job['job_title']) ? htmlspecialchars($job['job_title']) : 'N/A'; ?>">
                                                </p>
                                                <p><strong
                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_duration"); ?></strong>
                                                    <input type="text" class="form-control"
                                                        name="duration_from"
                                                        value="<?php echo isset($job['from_date']) ? date('M Y', strtotime($job['from_date'])) : 'N/A'; ?>">
                                                    -
                                                    <input type="text" class="form-control"
                                                        name="duration_to"
                                                        value="<?php echo isset($job['to_date']) ? date('M Y', strtotime($job['to_date'])) : 'Present'; ?>">
                                                </p>

                                                <p><strong
                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_address"); ?></strong>
                                                    <input type="text" class="form-control"
                                                        name="job_address"
                                                        value="<?php echo isset($job['address']) ? htmlspecialchars($job['address']) : 'N/A'; ?>">
                                                    <input type="text" class="form-control"
                                                        name="job_city"
                                                        value="<?php echo isset($job['city']) ? htmlspecialchars($job['city']) : ''; ?>">
                                                    <input type="text" class="form-control"
                                                        name="job_state"
                                                        value="<?php echo isset($job['state']) ? htmlspecialchars($job['state']) : ''; ?>">
                                                    <input type="text" class="form-control"
                                                        name="zip_code"
                                                        value="<?php echo isset($job['zip_code']) ? htmlspecialchars($job['zip_code']) : ''; ?>">
                                                </p>

                                                <p><strong
                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_salary_range"); ?></strong>
                                                    <input type="number" class="form-control"
                                                        name="salary_from"
                                                        value="<?php echo isset($job['starting_pay']) ? $job['starting_pay'] : 0; ?>">
                                                    -
                                                    <input type="number" class="form-control"
                                                        name="salary_to"
                                                        value="<?php echo isset($job['ending_pay']) ? $job['ending_pay'] : 0; ?>">
                                                </p>

                                                <p><strong
                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_supervisor"); ?></strong>
                                                    <input type="text" class="form-control"
                                                        name="supervisor_name"
                                                        value="<?php echo isset($job['supervisor_name']) ? htmlspecialchars($job['supervisor_name']) : 'N/A'; ?>">
                                                    <?php if (isset($job['supervisor_phone']) && !empty($job['supervisor_phone'])): ?>
                                                        <input type="number" class="form-control"
                                                            name="supervisor_number"
                                                            value="<?php echo $job['supervisor_phone']; ?>">
                                                    <?php endif; ?>
                                                </p>

                                                <p><strong
                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_duties"); ?></strong><br>
                                                    <input type="text" class="form-control"
                                                        name="duties"
                                                        value="<?php echo isset($job['duties']) ? nl2br(htmlspecialchars($job['duties'])) : 'No duties specified'; ?>">
                                                </p>

                                                <p><strong
                                                        style="font-weight: 900; color: #000;"><?php echo lang("formview_reason_for_leaving"); ?></strong><br>
                                                    <input type="text" class="form-control"
                                                        name="reason"
                                                        value="<?php echo isset($job['reason_for_leaving']) ? nl2br(htmlspecialchars($job['reason_for_leaving'])) : 'Not specified'; ?>">
                                                </p>
                                            </div>
                                        <?php endforeach;
                                    else: ?>
                                        <p class="text-muted">
                                            <?php echo lang("formview_no_employment_history_available"); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Skills Section -->
                                <div class="section-header">
                                    <?php echo lang("formview_skills_&_qualifications"); ?>
                                </div>
                                <div class="section-content">
                                    <?php if (!empty($skills)): ?>
                                        <ul class="list-unstyled">
                                            <?php foreach ($skills as $skill): ?>
                                                <li>
                                                    <input type="text" class="form-control"
                                                        name="skills"
                                                        value="<?php echo isset($skill['skill_description']) ? htmlspecialchars($skill['skill_description']) : 'Unknown Skill'; ?>">
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted">
                                            <?php echo lang("formview_no_skill_listed"); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                                               <!-- References Section -->
                                                               <div class="section-header">
    <?php echo lang("formview_references"); ?>
</div>
<div class="section-content">
    <?php if (!empty($references)):
        $refCount = 1;
        foreach ($references as $ref): ?>
            <div class="mb-3">
                <h5 style="font-weight: 900; color: #000;">
                    <?php echo lang("formview_reference_#"); ?>
                    <?php echo $refCount++; ?>
                </h5>
                <div class="row">
                   
                    <div class="col-md-4">
                        <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_name"); ?></strong>
                            <input type="text" class="form-control"
                                name="ref<?php echo $refCount-1; ?>_name"
                                value="<?php echo isset($ref['name']) ? htmlspecialchars($ref['name']) : 'N/A'; ?>">
                        </p>
                    </div>
                    
                    <div class="col-md-4">
                        <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_occupation"); ?></strong>
                            <input type="text" class="form-control"
                                name="ref<?php echo $refCount-1; ?>_occupation"
                                value="<?php echo isset($ref['occupation']) ? htmlspecialchars($ref['occupation']) : 'N/A'; ?>">
                        </p>
                    </div>
                    
                    <div class="col-md-4">
                        <p><strong style="font-weight: 900; color: #000;"><?php echo lang("formview_phone"); ?></strong>
                            <input type="number" class="form-control"
                                name="ref<?php echo $refCount-1; ?>_phone"
                                value="<?php echo isset($ref['phone_number']) ? htmlspecialchars($ref['phone_number']) : 'N/A'; ?>">
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach;
    else: ?>
        <p class="text-muted">
            <?php echo lang("formview_no_references_provided"); ?>
        </p>
    <?php endif; ?>
</div>
                                <!-- Signatures Section -->
                                <div class="section-header">
                                    <?php echo lang("formview_applicaion_signature"); ?>
                                </div>
                                <div class="section-content">
                                    <?php if (!empty($signature)): ?>
                                        <div class="mb-3">
                                            <p><strong
                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_signature"); ?></strong>
                                            <div class="col-md-6 single-box">
                                                <p><span
                                                        class="label"><?php echo lang("formview_applicaion_signature") ?></span>
                                                </p>
                                                <div class="field-box"
                                                    style="background: none !important;">
                                                    <?php
                                                    $signatureData = isset($signature['signature']) ? $signature['signature'] : '';
                                                    if (!empty($signatureData) && strpos($signatureData, 'data:image/png') === 0) {
                                                        echo '<img src="' . htmlspecialchars($signatureData) . '" 
                                                            alt="Applicant Signature"
                                                            style="max-width: 300px; max-height: 150px; background: transparent;"
                                                            onerror="this.style.display=\'none\';this.parentNode.innerHTML+=\'<p class=\\\'text-danger\\\'>Error loading signature</p>\'">';
                                                    } else {
                                                        echo '<p class="text-muted">No signature available</p>';
                                                        if (!empty($signatureData)) {
                                                            echo '<details class="mt-2"><summary>Debug Data</summary>';
                                                            echo '<div style="word-break: break-all; font-size: 0.8em;">';
                                                            echo htmlspecialchars(substr($signatureData, 0, 100));
                                                            echo (strlen($signatureData) > 100) ? '...' : '';
                                                            echo '</div></details>';
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            </p>

                                            <p><strong
                                                    style="font-weight: 900; color: #000;"><?php echo lang("formview_date"); ?></strong>
                                                <?php echo isset($signature['signature_date']) ? $signature['signature_date'] : 'N/A'; ?>
                                            </p>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">
                                            <?php echo lang("formview_no_signature_found"); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Form Submission Buttons -->
                                <div class="text-end mt-4">
    <button type="submit" 
            class="btn btn-primary ms-1 me-1" 
            style="border-radius: 8px; min-width: 120px;">
        <i class="fas fa-save me-1"></i><?php echo lang("formview_Save_changes"); ?>
    </button>
    <a href="index.php?route=modules/applicants/list_applicants"
       class="btn btn-secondary ms-1 me-1" 
       style="border-radius: 8px; min-width: 120px;">
        <i class="fas fa-times me-1"></i><?php echo lang("formview_cancel"); ?>
    </a>
</div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>