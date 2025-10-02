<?php
require('../functions.php');

header('Content-Type: application/json');

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. Only POST requests are accepted.', 400);
    }

    // Validate required fields
    $requiredFields = ['applicant_id', 'first_name', 'last_name', 'email', 'phone', 'kiosk_id'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        throw new Exception('Missing required fields: ' . implode(', ', $missingFields), 400);
    }

    // Validate email format
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format', 400);
    }

    DB::startTransaction();

    // Check if user already exists
    $existingUser = DB::queryFirstRow(
        "SELECT user_id FROM users WHERE email = %s", 
        $_POST['email']
    );
    
    if ($existingUser) {
        throw new Exception('User with this email already exists (User ID: ' . $existingUser['user_id'] . ')', 409);
    }

    // Generate username (you can modify this logic as needed)
    $userName = strtolower($_POST['first_name'][0] . $_POST['last_name']);

    // Create user with kioskID as both kioskID and plain text password
    $userId = DB::insert('users', [
        'first_name' => $_POST['first_name'],
        'last_name'  => $_POST['last_name'],
        'user_name'  => $userName,
        'email'      => $_POST['email'],
        'phone'      => $_POST['phone'],
        'kioskID'    => $_POST['kiosk_id'],
        'password'   => $_POST['kiosk_id'], 
        'role_id'    => 5, 
        'status'     => 'active',
        'created_at' => DB::sqleval('NOW()')
    ]);

    if (!$userId) {
        throw new Exception('Failed to create user record', 500);
    }

    // Prepare applicant data
    $applicantData = [
        'first_name'   => $_POST['first_name'],
        'last_name'    => $_POST['last_name'],
        'email'        => $_POST['email'],
        'phone_number' => $_POST['phone'],
        'user_id'      => $userId
    ];

    // Check if applicant exists
    $existingApplicant = DB::queryFirstRow(
        "SELECT id FROM applicants WHERE id = %i", 
        $_POST['applicant_id']
    );

    if ($existingApplicant) {
        // Update existing applicant
        $updateData = ['user_id' => $userId];
        $updated = DB::update('applicants', $updateData, 'id = %i', $_POST['applicant_id']);
        
        if (!$updated) {
            throw new Exception('Failed to update applicant record', 500);
        }
    } else {
        // Insert new applicant
        $applicantData['id'] = $_POST['applicant_id'];
        $applicantData['created_at'] = DB::sqleval('NOW()');
        
        $inserted = DB::insert('applicants', $applicantData);
        
        if (!$inserted) {
            throw new Exception('Failed to create applicant record', 500);
        }
    }

    DB::commit();

    // Success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'User created successfully',
        'data' => [
            'user_id'      => $userId,
            'applicant_id' => $_POST['applicant_id'],
            'user_name'    => $userName,
            'email'        => $_POST['email'],
            'kioskID'      => $_POST['kiosk_id']
        ]
    ]);

} catch (Exception $e) {
    DB::rollback();
    
    // Set proper HTTP status code
    $code = is_int($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600 
            ? $e->getCode() 
            : 500;
    http_response_code($code);
    
    echo json_encode([
        'success' => false,
        'error' => [
            'message' => $e->getMessage(),
            'code'    => $code
        ],
        'request_data' => $_POST
    ]);
}