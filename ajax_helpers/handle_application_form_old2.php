<?php
require('../functions.php'); // Your file that includes MeekroDB

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

// $_POST already contains your form data as an array
$applicantData = $_POST;

echo '<pre>';
print_r($applicantData);
die();

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON: " . json_last_error_msg()]);
    exit;
}

if (empty($applicantData)) {
    echo json_encode(["status" => "error", "message" => "Decoded JSON is empty"]);
    exit;
}

// echo '<pre>';
// print_r($applicantData);
// die();

// Ensure all required keys are set and not empty
// $required_keys = [
//     "last_name", "first_name", "middle_initial", "ssn", "street_address", "city", 
//     "state", "zip_code", "phone_number", "work_eligibility", "position_desired",
//     "wage_salary", "employment_type", "criminal_history", "criminal_when",
//     "criminal_where", "start_date", "age_confirm", "high_school", "high_school_city_state",
//     "high_school_grad", "high_school_ged", "college", "college_city_state", 
//     "college_grad", "degree", "major", "currently_enrolled", "school_info", 
//     "skills", "monday_from", "monday_to", "total_hours", "schedule_requests",
//     "ref1_name", "ref1_relationship", "ref1_phone", "ref2_name", "ref2_relationship",
//     "ref2_phone", "ref3_name", "ref3_relationship", "ref3_phone", "employer_name",
//     "job_title", "duties", "employer_address", "employer_from", "employer_to",
//     "employer_location", "startPay", "endPay", "salary", "supervisor",
//     "employer_phone", "reason_leaving", "signature", "signature_date"
// ];

// // Filter non-empty values
// $filtered_data = [];
// foreach ($required_keys as $key) {
//     if (isset($applicantData[$key]) && !empty($applicantData[$key])) {
//         $filtered_data[$key] = $applicantData[$key]; // Store only non-empty values
//     }
// }
$employmentType = isset($applicantData['employment_type']) ? (is_array($applicantData['employment_type']) ? implode(', ', array_filter($applicantData['employment_type'])) : trim($applicantData['employment_type'])) : null;

// applicants insertion
DB::insert('applicants', [
    'last_name' => $applicantData['last_name'],
    'first_name' => $applicantData['first_name'],
    'middle_initial' => $applicantData['middle_initial'] ?? null,
    'ssn' => $applicantData['ssn'],
    'street_address' => $applicantData['street_address'],
    'city' => $applicantData['city'],
    'state' => $applicantData['state'],
    'zip_code' => $applicantData['zip_code'],
    'phone_number' => $applicantData['phone_number'],
    'legal_us_work_eligibility' => $applicantData['work_eligibility'],
    'position' => $applicantData['position_desired'],
    'salary' => $applicantData['wage_salary'],
    'employment_type' => $employmentType,
    'available_start_date' => $applicantData['start_date'],
    'over_18' => $applicantData['age_confirm']
]);

$applicant_id = DB::insertId();

if($applicant_id) {

    // Criminal history insertion
    if (!empty($applicantData['criminal_history']) && $applicantData['criminal_history'] == 'Yes') {
        DB::insert('criminal_history', [
            'applicant_id' => $applicant_id,
            'has_conviction' => 1,
            'conviction_date' => $applicantData['conviction_date'] ?? '',
            'conviction_location' => $applicantData['conviction_location'] ?? '',
            'convicted_when' => $applicantData['criminal_when'] ?? '',
            'convicted_where' => $applicantData['criminal_where'] ?? '',
        ]);
    }

    // Education insertion
    DB::insert('education', [
        'applicant_id' => $applicant_id,
        'high_school_name' => $applicantData['high_school'],
        'high_school_city' => $applicantData['high_school_city_state'],
        // 'high_school_state' => $applicantData['high_school_state'],
        // 'high_school_zip' => $applicantData['high_school_zip'],
        'high_school_graduate' => $applicantData['high_school_grad'],
        'ged' => $applicantData['high_school_ged'],
        'college_name' => $applicantData['college'],
        'college_city' => $applicantData['college_city_state'],
        // 'college_state' => $applicantData['college_state'],
        // 'college_zip' => $applicantData['college_zip'],
        'college_graduate' => $applicantData['college_grad'],
        'college_degree' => $applicantData['degree'],
        'college_major' => $applicantData['major'],
        'currently_enrolled' => $applicantData['currently_enrolled'],
        'enrolled_school_name' => $applicantData['school_info'],
        'expected_degree_date' => $applicantData['expected_degree_date'] ?? ''
    ]);

    // Skills insertion
    if (!empty($applicantData['skills'])) {
        $skills = explode(',', $applicantData['skills']);
        foreach ($skills as $skill) {
            DB::insert('skills', ['applicant_id' => $applicant_id, 'skill_description' => trim($skill)]);
        }
    }

    // Availability insertion
    DB::insert('availability', [
        'applicant_id' => $applicant_id,
        'day' => $applicantData['availability_day'],
        'time_from' => $applicantData['availability_from'],
        'time_to' => $applicantData['availability_to'],
        'total_hours' => $applicantData['availability_total_hours'],
        'special_requests' => $applicantData['availability_schedule_requests']
    ]);

    // References insertion
    for ($i = 1; $i <= 3; $i++) {
        $refKey = "ref{$i}_name";
        if (!empty($applicantData[$refKey])) {
            DB::insert('references_info', [
                'applicant_id' => $applicant_id,
                'name' => $applicantData[$refKey] ?? '',
                // 'occupation' => $applicantData["ref{$i}_occupation"],
                'relationship_duration' => $applicantData["ref{$i}_relationship"] ?? '',
                'phone_number' => $applicantData["ref{$i}_phone"] ?? ''
            ]);
        }
    }

    // Employment history insertion
    if (isset($applicantData['employer_name'])) {
        // Loop through each employer record
        for ($i = 0; $i < count($applicantData['employer_name']); $i++) {
            DB::insert('employment_history', [
                'applicant_id' => $applicant_id,
                'employer_name' => $applicantData['employer_name'][$i],
                'job_title' => $applicantData['job_title'][$i],
                'duties' => $applicantData['duties'][$i],
                'address' => $applicantData['employer_address'][$i],
                'city' => $applicantData['city'][$i] ?? '',
                'state' => $applicantData['employer_location'][$i],
                'zip_code' => $applicantData['zip_code'][$i] ?? '',
                'from_date' => $applicantData['employer_from'][$i],
                'to_date' => $applicantData['employer_to'][$i],
                'starting_pay' => $applicantData['startPay'][$i],
                'ending_pay' => $applicantData['endPay'][$i],
                'supervisor_name' => $applicantData['supervisor'][$i],
                'supervisor_phone' => $applicantData['employer_phone'][$i],
                'reason_for_leaving' => $applicantData['reason_leaving'][$i]
            ]);
        }
    }

    // Application signatures insertion
    DB::insert('application_signatures', [
        'applicant_id' => $applicant_id,
        'signature' => $applicantData['signature'],
        'signature_date' => $applicantData['signature_date']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Application submitted successfully',
    ]);

} else {
    echo json_encode(["success" => false, "message" => "Failed to insert applicant"]);
}


// try {
//     DB::startTransaction();
    
//     $employmentType = isset($applicantData['employment_type']) ? 
//         (is_array($applicantData['employment_type']) ? implode(', ', $applicantData['employment_type']) : $applicantData['employment_type']) 
//         : '';

//     DB::insert('applicants', [
//         'last_name' => $applicantData['last_name'],
//         'first_name' => $applicantData['first_name'],
//         'middle_initial' => $applicantData['middle_initial'] ?? null,
//         'ssn' => $applicantData['ssn'],
//         'street_address' => $applicantData['street_address'],
//         'city' => $applicantData['city'],
//         'state' => $applicantData['state'],
//         'zip_code' => $applicantData['zip_code'],
//         'phone_number' => $applicantData['phone_number'],
//         'legal_us_work_eligibility' => $applicantData['legal_us_work_eligibility'],
//         'position' => $applicantData['position'],
//         'salary' => $applicantData['salary'],
//         'employment_type' => $employmentType,
//         'available_start_date' => $applicantData['available_start_date'],
//         'over_18' => $applicantData['over_18']
//     ]);

//     $applicant_id = DB::insertId();

//     if (!empty($applicantData['criminal_history']) && $applicantData['criminal_history'] == 'Yes') {
//         DB::insert('criminal_history', [
//             'applicant_id' => $applicant_id,
//             'has_conviction' => 1,
//             'conviction_date' => $applicantData['conviction_date'],
//             'conviction_location' => $applicantData['conviction_location']
//         ]);
//     }

//     DB::insert('education', [
//         'applicant_id' => $applicant_id,
//         'high_school_name' => $applicantData['high_school_name'],
//         'high_school_city' => $applicantData['high_school_city'],
//         'high_school_state' => $applicantData['high_school_state'],
//         'high_school_zip' => $applicantData['high_school_zip'],
//         'high_school_graduate' => $applicantData['high_school_graduate'],
//         'ged' => $applicantData['ged'],
//         'college_name' => $applicantData['college_name'],
//         'college_city' => $applicantData['college_city'],
//         'college_state' => $applicantData['college_state'],
//         'college_zip' => $applicantData['college_zip'],
//         'college_graduate' => $applicantData['college_graduate'],
//         'college_degree' => $applicantData['college_degree'],
//         'college_major' => $applicantData['college_major'],
//         'currently_enrolled' => $applicantData['currently_enrolled'],
//         'enrolled_school_name' => $applicantData['enrolled_school_name'],
//         'expected_degree_date' => $applicantData['expected_degree_date']
//     ]);

//     if (!empty($applicantData['skills'])) {
//         $skills = explode(',', $applicantData['skills']);
//         foreach ($skills as $skill) {
//             DB::insert('skills', ['applicant_id' => $applicant_id, 'skill_description' => trim($skill)]);
//         }
//     }

//     for ($i = 1; $i <= 3; $i++) {
//         $refKey = "ref{$i}_name";
//         if (!empty($applicantData[$refKey])) {
//             DB::insert('references_info', [
//                 'applicant_id' => $applicant_id,
//                 'name' => $applicantData[$refKey],
//                 'occupation' => $applicantData["ref{$i}_occupation"],
//                 'relationship_duration' => $applicantData["ref{$i}_relationship_duration"],
//                 'phone_number' => $applicantData["ref{$i}_phone"]
//             ]);
//         }
//     }

//     if (!empty($applicantData['employment_history'])) {
//         foreach ($applicantData['employment_history'] as $history) {
//             DB::insert('employment_history', [
//                 'applicant_id' => $applicant_id,
//                 'employer_name' => $history['employer_name'],
//                 'job_title' => $history['job_title'],
//                 'duties' => $history['duties'],
//                 'address' => $history['address'],
//                 'city' => $history['city'],
//                 'state' => $history['state'],
//                 'zip_code' => $history['zip_code'],
//                 'from_date' => $history['from_date'],
//                 'to_date' => $history['to_date'],
//                 'starting_pay' => $history['starting_pay'],
//                 'ending_pay' => $history['ending_pay'],
//                 'supervisor_name' => $history['supervisor_name'],
//                 'supervisor_phone' => $history['supervisor_phone'],
//                 'reason_for_leaving' => $history['reason_for_leaving']
//             ]);
//         }
//     }

//     DB::insert('application_signatures', [
//         'applicant_id' => $applicant_id,
//         'signature' => $applicantData['signature'],
//         'signature_date' => $applicantData['signature_date']
//     ]);

//     DB::commit();

//     echo json_encode(["status" => "success", "message" => "Applicant data inserted successfully", "applicant_id" => $applicant_id]);
// } catch (Exception $e) {
//     DB::rollback();
//     echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
// }
