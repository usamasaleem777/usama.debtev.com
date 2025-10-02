<?php
require('../functions.php'); // Your file that includes MeekroDB
require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';
header('Content-Type: application/json');

// $inputData = file_get_contents("php://input");

// if (!$inputData) {
//     echo json_encode(["status" => "error", "message" => "No raw input received"]);
//     exit;
// }

// $applicantData = json_decode($inputData, true);

// $_POST will automatically contain your form data
if (empty($_POST)) {
    echo json_encode(["status" => "error", "message" => "No form data received"]);
    exit;
}

// Process the applicant data
$applicantData = $_POST; // Assuming data comes from POST request


    // Define the email function outside the try block


// Validate required fields
$requiredFields = [
    'last_name', 'first_name',  'street_address', 
    'city', 'state', 'zip_code', 'phone_number', 'email', 'signature', 'gender'
    
];

foreach ($requiredFields as $field) {
    if (empty($applicantData[$field])) {
        echo json_encode([
            'success' => false,
            'message' => "Required field {$field} is missing"
        ]);
        exit;
    }
}

// Handle employment type (can be array or string)
$employmentType = isset($applicantData['employment_type'])
    ? (is_array($applicantData['employment_type'])
        ? implode(', ', array_filter($applicantData['employment_type']))
        : (is_string($applicantData['employment_type'])
            ? trim($applicantData['employment_type'])
            : null))
    : null;

    // try {
    //     $workEligibility = strtoupper(trim($applicantData['work_eligibility']));
    //     $allowedValues = ['YES', 'NO'];

    //     if (!in_array($workEligibility, $allowedValues)) {
    //         throw new Exception('Invalid work eligibility value');
    //     }
    // } catch (Exception $e) {
    //     echo json_encode([
    //         'success' => false,
    //         'message' => $e->getMessage()
    //     ]);
    //     exit;
    // }

    // try {
    //     $psEligibility = strtoupper(trim($applicantData['ps_eligibility']));
    //     $allowedValues1 = ['YES', 'NO'];

    //     if (!in_array($workEligibility, $allowedValues1)) {
    //         throw new Exception('Invalid work eligibility value');
    //     }
    // } catch (Exception $e) {
    //     echo json_encode([
    //         'success' => false,
    //         'message' => $e->getMessage()
    //     ]);
    //     exit;
    // }
    try {
        // Retrieve and validate ps_eligibility independently
        $psEligibility = strtoupper(trim($applicantData['ps_eligibility']));
        $allowedValues = ['YES', 'NO'];
    
        if (!in_array($psEligibility, $allowedValues)) {
            throw new Exception('Invalid ps eligibility value');
        }
    
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
    

    
try {

    // Begin database transaction
// DB::Transaction();
// $check_exists = DB::queryFirstRow(
//     "SELECT * FROM `applicants` WHERE `email` = %s AND `position` = %i",
//     $applicantData['email'],
//     $applicantData['position_desired']
// );
$captcha = $_POST['g-recaptcha-response'] ?? null;

if (!$captcha) {
    echo json_encode([
        'success' => false,
        'message' => 'Please complete the CAPTCHA.'
    ]);
    exit;
}

// Verify reCAPTCHA with Google
$captcha = $_POST['g-recaptcha-response'] ?? '';

if (!$captcha) {
    echo json_encode([
        'success' => false,
        'message' => 'CAPTCHA is missing.'
    ]);
    exit;
}

$secretKey = "6LfNWxMrAAAAAIV4LSrAgUpUP3sy9o-Q7rFkezFX"; 
$verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captcha}");
$responseData = json_decode($verifyResponse);

if (!$responseData->success) {
    $errors = isset($responseData->{'error-codes'}) ? implode(', ', $responseData->{'error-codes'}) : 'Unknown error';
    echo json_encode([
        'success' => false,
        'message' => 'CAPTCHA verification failed. Reason: ' . $errors
    ]);
    exit;
}

// Handle additional positions
// $positions = isset($_POST['additional_positions']) ? $_POST['additional_positions'] : [];
// $salaries = isset($_POST['additional_salaries']) ? $_POST['additional_salaries'] : [];
// $employmentTypes = isset($_POST['employment_type']) ? $_POST['employment_type'] : [];

// if (!is_array($employmentTypes)){
//     $employmentTypes= [$employmentTypes];
// }

// Uncomment and modify the position handling code
$positions = isset($_POST['additional_positions']) ? $_POST['additional_positions'] : [];
$salaries = isset($_POST['additional_salaries']) ? $_POST['additional_salaries'] : [];
$employmentTypes = isset($_POST['employment_type']) ? $_POST['employment_type'] : [];

// Convert arrays to comma-separated strings
$positionString = implode(', ', $positions);
$salariesString = implode(', ', $salaries);
$employmentTypeString = is_array($employmentTypes) ? implode(', ', $employmentTypes) : $employmentTypes;

// Collect the submitted form data
$dob = $_POST['dob'];

//$selectedJobId = $_POST['job']; // The selected job_id from the jobDesired dropdown


// Calculate age from DOB
$today = new DateTime();
$birthdate = new DateTime($dob);
$age = $today->diff($birthdate)->y;
$over_18 = ($age >= 18) ? 'Yes' : 'No';


// Validate gender
$allowedGenders = ['male', 'female', 'other'];
if (!in_array($applicantData['gender'], $allowedGenders)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid gender value'
    ]);
    exit;
}

// Insert applicant information
DB::insert('applicants', [
    'last_name' => $applicantData['last_name'],
    'first_name' => $applicantData['first_name'],
    'middle_initial' => $applicantData['middle_initial'] ?? null,
    'gender' => $applicantData['gender'],
    'street_address' => $applicantData['street_address'],
    'city' => $applicantData['city'],
    'state' => $applicantData['state'],
    'zip_code' => $applicantData['zip_code'],
    'phone_number' => $applicantData['phone_number'],
    'email' => $applicantData['email'],
    'passport_or_id' => $psEligibility,
    // 'legal_us_work_eligibility' => $workEligibility,
    'position' => $positionString,
    'salary' => $salariesString,
    'employment_type' => $employmentTypeString ?? null,
    'available_start_date' => $applicantData['start_date'],
    'over_18' => $over_18,
    'dob' => $dob ,  
    //'job_applied' => $selectedJobId,  // Store the selected job ID
    // 'reference' => $applicantData['GotToKnow'] ?? '',
    'reference_check'  => $applicantData['referred'] ?? 'No', // Yes / No from radio
    'refrence_name'=> $applicantData['ReferredByName'] ?? null, // Name input
    'referred_contact_info' => $applicantData['ReferredByContact'] ?? null, // Contact input
]);

$applicant_id = DB::insertId();

if (!$applicant_id) {
    throw new Exception("Failed to insert applicant");
}


    // Criminal history insertion (only if Yes)
    if (!empty($applicantData['criminal_history']) && $applicantData['criminal_history'] == 'Yes') {
        DB::insert('criminal_history', [
            'applicant_id' => $applicant_id,
            'has_conviction' => 1,
            'conviction_date' => $applicantData['conviction_date'] ?? null,
            'conviction_location' => $applicantData['conviction_location'] ?? null,
            'convicted_when' => $applicantData['criminal_when'] ?? null,
            'convicted_where' => $applicantData['criminal_where'] ?? null,
        ]);
    }

    // Parse education location data
    $highSchoolLocation = isset($applicantData['high_school_city_state']) 
        ? explode(', ', $applicantData['high_school_city_state'], 2)
        : [null, null];
    
    $collegeLocation = isset($applicantData['college_city_state']) 
        ? explode(', ', $applicantData['college_city_state'], 2)
        : [null, null];

    // Education insertion
    DB::insert('education', [
        'applicant_id' => $applicant_id,
        'high_school_name' => $applicantData['high_school'] ?? null,
        'high_school_city' => $highSchoolLocation[0] ?? null,
        'high_school_state' => $highSchoolLocation[1] ?? null,
        'high_school_graduate' => $applicantData['high_school_grad'] ?? 'No',
        'ged' => $applicantData['high_school_ged'] ?? 'No',
        'college_name' => $applicantData['college'] ?? null,
        'college_city' => $collegeLocation[0] ?? null,
        'college_state' => $collegeLocation[1] ?? null,
        'college_graduate' => $applicantData['college_grad'] ?? 'No',
        'college_degree' => $applicantData['degree'] ?? null,
        'college_major' => $applicantData['major'] ?? null,
        'currently_enrolled' => $applicantData['currently_enrolled'] ?? 'No',
        'enrolled_school_name' => $applicantData['school_info'] ?? null,
        'expected_degree_date' => $applicantData['expected_degree_date'] ?? null
    ]);

    // Skills insertion
    if (!empty($applicantData['skills'])) {
        $skills = is_array($applicantData['skills']) 
            ? $applicantData['skills'] 
            : explode(',', $applicantData['skills']);
        
        foreach ($skills as $skill) {
            $skill = trim($skill);
            if (!empty($skill)) {
                DB::insert('skills', [
                    'applicant_id' => $applicant_id, 
                    'skill_description' => $skill
                ]);
            }
        }
    }

    // Availability insertion
    // foreach ($applicantData['availability_day'] as $index => $day) {
    //     DB::insert('availability', [
    //         'applicant_id' => $applicant_id,
    //         'day' => $day,
    //         'time_from' => $applicantData['availability_from'][$index] ?? null,
    //         'time_to' => $applicantData['availability_to'][$index] ?? null,
    //         'total_hours' => $applicantData['availability_total_hours'] ?? null,
    //         'special_requests' => $applicantData['availability_schedule_requests'] ?? null
    //     ]);
    // }    
    // DB::insert('availability', [
    //     'applicant_id' => $applicant_id,
    //     'day' => $applicantData['availability_day'] ?? null,
    //     'time_from' => $applicantData['availability_from'] ?? null,
    //     'time_to' => $applicantData['availability_to'] ?? null,
    //     'total_hours' => $applicantData['availability_total_hours'] ?? null,
    //     'special_requests' => $applicantData['availability_schedule_requests'] ?? null
    // ]);

    // References insertion (1-3)
    for ($i = 1; $i <= 3; $i++) {
        $refKey = "ref{$i}_name";
        if (!empty($applicantData[$refKey])) {
            DB::insert('references_info', [
                'applicant_id' => $applicant_id,
                'name' => $applicantData[$refKey],
                'relationship_duration' => $applicantData["ref{$i}_relationship"] ?? null,
                'phone_number' => $applicantData["ref{$i}_phone"] ?? null
            ]);
        }
    }

    // Employment history insertion
if (!empty($applicantData['employer_name']) && is_array($applicantData['employer_name'])) {
    foreach ($applicantData['employer_name'] as $i => $employerName) {
        // Validate date range
        $fromDate = $applicantData['employer_from'][$i] ?? null;
        $toDate = $applicantData['employer_to'][$i] ?? null;

        // Ensure dates are valid before comparing
        if (!empty($fromDate) && !empty($toDate)) {
            $fromTimestamp = strtotime($fromDate);
            $toTimestamp = strtotime($toDate);
            
            // If date range is invalid, skip this entry
            if ($fromTimestamp === false || $toTimestamp === false || $fromTimestamp > $toTimestamp) {
                continue;
            }
        }

        // Parse employer address
        $employerAddress = isset($applicantData['employer_address'][$i])
            ? explode(', ', $applicantData['employer_address'][$i], 2)
            : [null, null];

        try {
            DB::insert('employment_history', [
                'applicant_id' => $applicant_id,
                'employer_name' => $employerName,
                'job_title' => $applicantData['job_title'][$i] ?? null,
                'duties' => $applicantData['duties'][$i] ?? null,
                'address' => $employerAddress[0] ?? null,
                'city' => $employerAddress[1] ?? ($applicantData['city'] ?? null),
                'state' => $applicantData['employer_location'][$i] ?? ($applicantData['state'] ?? null),
                'zip_code' => $applicantData['zip_code'] ?? null, // Using applicant's zip if available
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'starting_pay' => $applicantData['startPay'][$i] ?? null,
                'ending_pay' => $applicantData['endPay'][$i] ?? null,
                'supervisor_name' => $applicantData['supervisor'][$i] ?? null,
                'supervisor_phone' => $applicantData['employer_phone'][$i] ?? null,
                'reason_for_leaving' => $applicantData['reason_leaving'][$i] ?? null
            ]);
        } catch (Exception $e) {
            error_log("DB Insert Error: " . $e->getMessage());
        }
    }
}

    // if (isset($applicantData['employer_name']) && is_array($applicantData['employer_name'])) {
    //     foreach ($applicantData['employer_name'] as $i => $employerName) {
    //         // Validate date range
    //         $fromDate = $applicantData['employer_from'][$i] ?? null;
    //         $toDate = $applicantData['employer_to'][$i] ?? null;
            
    //         // Skip invalid date ranges
    //         if ($fromDate && $toDate && strtotime($fromDate) > strtotime($toDate)) {
    //             continue;
    //         }

    //         // Parse employer address if needed
    //         $employerAddress = isset($applicantData['employer_address'][$i])
    //             ? explode(', ', $applicantData['employer_address'][$i], 2)
    //             : [null, null];

    //         DB::insert('employment_history', [
    //             'applicant_id' => $applicant_id,
    //             'employer_name' => $employerName,
    //             'job_title' => $applicantData['job_title'][$i] ?? null,
    //             'duties' => $applicantData['duties'][$i] ?? null,
    //             'address' => $employerAddress[0] ?? null,
    //             'city' => $employerAddress[1] ?? $applicantData['city'],
    //             'state' => $applicantData['employer_location'][$i] ?? $applicantData['state'],
    //             'zip_code' => $applicantData['zip_code'], // Using applicant's zip if not provided
    //             'from_date' => $fromDate,
    //             'to_date' => $toDate,
    //             'starting_pay' => $applicantData['startPay'][$i] ?? null,
    //             'ending_pay' => $applicantData['endPay'][$i] ?? null,
    //             'supervisor_name' => $applicantData['supervisor'][$i] ?? null,
    //             'supervisor_phone' => $applicantData['employer_phone'][$i] ?? null,
    //             'reason_for_leaving' => $applicantData['reason_leaving'][$i] ?? null
    //         ]);
    //     }
    // }

    // Validate signature date isn't in future
    // $signatureDate = $applicantData['signature_date'];
    // if (strtotime($signatureDate) > time()) {
    //     throw new Exception("Signature date cannot be in the future");
    // }

    // Application signatures insertion
    DB::insert('application_signatures', [
        'applicant_id' => $applicant_id,
        'signature' => $applicantData['signature'],
        
    ]);

     // Prepare data for the email
    //  $applicantName = $applicantData['first_name'] . ' ' . $applicantData['last_name'];
    //  $applicantEmail = $applicantData['email'];
    //  $applicantPhone = $applicantData['phone_number'];
    //  $applicantCity = $applicantData['city'];
    //  $applicantSkills = $applicantData['skills'];
    //  $applicantPosition = $positionString; // From earlier processing
     
     // Send notification email to HR
    //  sendHRNotificationEmail([
    //      'name' => $applicantName,
    //      'email' => $applicantEmail,
    //      'phone' => $applicantPhone,
    //      'position' => $applicantPosition,
    //      'city' => $applicantCity,
    //      'skills' => $applicantSkills,
    //  ], $applicant_id);
     
    // Commit transaction if all successful
    // DB::commit();

    echo json_encode([
        'success' => true,
        'message' => 'Application submitted successfully',
        'applicant_id' => $applicant_id
    ]);     
} 

catch (Exception $e) {
    // DB::rollback();
    
    error_log("Application submission failed: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Application submission failed: ' . $e->getMessage()
    ]);
}