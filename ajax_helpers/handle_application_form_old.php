<?php
require('../functions.php'); // Your file that includes MeekroDB

header('Content-Type: application/json');

function validateSSN($ssn) {
    return preg_match('/^\d{3}-\d{2}-\d{4}$/', $ssn);
}

function validatePhone($phone) {
    // Basic phone validation - adjust according to your needs
    return preg_match('/^[0-9]{10,15}$/', preg_replace('/[^0-9]/', '', $phone));
}

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function sendToAPI($data) {
    $apiUrl = 'http://localhost/crafthiring/api/receive_application.php'; // Replace with your API endpoint
    $apiKey = '16e92999952387889f4556a8d4faba85bec26cef7ea9f1708b4dd8cbafa3dfd4'; // Replace with your actual API key
    
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        throw new Exception('API connection failed: ' . curl_error($ch));
    }
    
    curl_close($ch);
    
    if ($httpCode < 200 || $httpCode >= 300) {
        throw new Exception('API returned error: ' . $response);
    }
    
    return $response;
}

try {
    // Validate required fields
    $required = [
        'last_name', 'first_name', 'ssn', 'street_address', 'city', 'state', 
        'zip_code', 'phone_number', 'work_eligibility', 'position_desired',
        'wage_salary', 'start_date', 'age_confirm', 'signature', 'signature_date'
    ];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Validate specific fields
    if (!validateSSN($_POST['ssn'])) {
        throw new Exception("Invalid SSN format. Please use XXX-XX-XXXX format.");
    }
    
    if (!validatePhone($_POST['phone_number'])) {
        throw new Exception("Invalid phone number format.");
    }
    
    if (!validateDate($_POST['start_date'])) {
        throw new Exception("Invalid start date format.");
    }
    
    if (!validateDate($_POST['signature_date'])) {
        throw new Exception("Invalid signature date format.");
    }
    
    // Process employment history (dynamic fields)
    $employmentHistory = [];
    if (isset($_POST['employer_name']) && is_array($_POST['employer_name'])) {
        $count = count($_POST['employer_name']);
        for ($i = 0; $i < $count; $i++) {
            $employmentHistory[] = [
                'employer_name' => $_POST['employer_name'][$i],
                'job_title' => $_POST['job_title'][$i],
                'duties' => $_POST['duties'][$i],
                'address' => $_POST['employer_address'][$i],
                'from_date' => $_POST['employer_from'][$i],
                'to_date' => $_POST['employer_to'][$i],
                'location' => $_POST['employer_location'][$i],
                'starting_pay' => $_POST['startPay'][$i],
                'ending_pay' => $_POST['endPay'][$i],
                'salary' => $_POST['salary'][$i],
                'supervisor' => $_POST['supervisor'][$i],
                'phone' => $_POST['employer_phone'][$i],
                'reason_for_leaving' => $_POST['reason_leaving'][$i]
            ];
        }
    }
    
    // Process references
    $references = [];
    for ($i = 1; $i <= 3; $i++) {
        if (!empty($_POST["ref{$i}_name"])) {
            $references[] = [
                'name' => $_POST["ref{$i}_name"],
                'relationship' => $_POST["ref{$i}_relationship"],
                'phone' => $_POST["ref{$i}_phone"]
            ];
        }
    }
    
    // Process availability
    $availability = [];
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    foreach ($days as $day) {
        if (!empty($_POST["{$day}_from"]) && !empty($_POST["{$day}_to"])) {
            $availability[] = [
                'day' => ucfirst($day),
                'from' => $_POST["{$day}_from"],
                'to' => $_POST["{$day}_to"]
            ];
        }
    }
    
    // Prepare the data structure for API
    $apiData = [
        'last_name' => $_POST['last_name'],
        'first_name' => $_POST['first_name'],
        'middle_initial' => $_POST['middle_initial'] ?? '',
        'ssn' => $_POST['ssn'],
        'street_address' => $_POST['street_address'],
        'city' => $_POST['city'],
        'state' => $_POST['state'],
        'zip_code' => $_POST['zip_code'],
        'phone_number' => $_POST['phone_number'],
        'legal_us_work_eligibility' => $_POST['work_eligibility'] ?? '',
        'position' => $_POST['position_desired'],
        'salary' => $_POST['wage_salary'],
        'employment_type' => $_POST['employment_type'][0] ?? '', // first employment type
        'available_start_date' => $_POST['start_date'],
        'over_18' => ($_POST['age_confirm'] ?? '') === 'Yes' ? 1 : 0,
        'criminal_history' => [
            'has_conviction' => ($_POST['criminal_history'] ?? '') === 'Yes' ? 1 : 0,
            'conviction_date' => $_POST['criminal_when'] ?? null,
            'conviction_location' => $_POST['criminal_where'] ?? null,
            'convicted_when' => $_POST['criminal_when'] ?? null,
            'convicted_where' => $_POST['criminal_where'] ?? null
        ],
        'begin_work' => [
            'date' => $_POST['start_date'],
            '18_years' => ($_POST['age_confirm'] ?? '') === 'Yes' ? 1 : 0
        ],
        'education' => [
            'high_school_name' => $_POST['high_school'] ?? '',
            'high_school_city' => $_POST['high_school_city_state'] ?? '',
            'high_school_state' => '',
            'high_school_zip' => '',
            'high_school_graduate' => ($_POST['high_school_grad'] ?? '') === 'Yes' ? 1 : 0,
            'ged' => ($_POST['high_school_ged'] ?? '') === 'Yes' ? 1 : 0,
            'college_name' => $_POST['college'] ?? '',
            'college_city' => $_POST['college_city_state'] ?? '',
            'college_state' => '',
            'college_zip' => '',
            'college_graduate' => ($_POST['college_grad'] ?? '') === 'Yes' ? 1 : 0,
            'college_degree' => $_POST['degree'] ?? '',
            'college_major' => $_POST['major'] ?? '',
            'currently_enrolled' => ($_POST['currently_enrolled'] ?? '') === 'Yes' ? 1 : 0,
            'enrolled_school_name' => $_POST['currently_enrolled'] === 'Yes' ? ($_POST['school_info'] ?? null) : null,
            'expected_degree_date' => null
        ],
        'skills' => !empty($_POST['skills']) ? explode(', ', $_POST['skills']) : [],
        'availability' => [
            [
                'day' => 'Monday',
                'time_from' => $_POST['monday_from'] ?? '',
                'time_to' => $_POST['monday_to'] ?? '',
                'total_hours' => $_POST['total_hours'] ?? 0,
                'special_requests' => $_POST['schedule_requests'] ?? ''
            ]
        ],
        'references' => [
            [
                'name' => $_POST['ref1_name'] ?? '',
                'occupation' => $_POST['ref1_relationship'] ?? '',
                'relationship_duration' => '',
                'phone_number' => $_POST['ref1_phone'] ?? ''
            ],
            [
                'name' => $_POST['ref2_name'] ?? '',
                'occupation' => $_POST['ref2_relationship'] ?? '',
                'relationship_duration' => '',
                'phone_number' => $_POST['ref2_phone'] ?? ''
            ]
        ],
        'employment_history' => array_map(function ($index) {
            return [
                'employer_name' => $_POST['employer_name'][$index] ?? '',
                'job_title' => $_POST['job_title'][$index] ?? '',
                'duties' => $_POST['duties'][$index] ?? '',
                'address' => $_POST['employer_address'][$index] ?? '',
                'city' => '',
                'state' => '',
                'zip_code' => '',
                'from_date' => $_POST['employer_from'][$index] ?? '',
                'to_date' => $_POST['employer_to'][$index] ?? '',
                'starting_pay' => $_POST['startPay'][$index] ?? '',
                'ending_pay' => $_POST['endPay'][$index] ?? '',
                'supervisor_name' => $_POST['supervisor'][$index] ?? '',
                'supervisor_phone' => $_POST['employer_phone'][$index] ?? '',
                'reason_for_leaving' => $_POST['reason_leaving'][$index] ?? ''
            ];
        }, array_keys($_POST['employer_name'] ?? [])),
        'signature' => [
            'signature' => $_POST['signature'] ?? '',
            'signature_date' => $_POST['signature_date'] ?? ''
        ]
    ];
    
    
    
    
    // echo '<pre>';
    // print_r($apiData);
    // die();
    // Send to API
    $apiResponse = sendToAPI($apiData);
    $apiResponseData = json_decode($apiResponse, true);
    
    // Return success with API response
    echo json_encode([
        'success' => true,
        'message' => 'Application submitted successfully',
        'api_response' => $apiResponseData,
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_fields' => [] // You could add specific field errors here
    ]);
}