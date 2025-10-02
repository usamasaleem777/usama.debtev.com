<?php
require('../functions.php');
header('Content-Type: application/json');

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
if (empty($input)) {
    $input = $_POST;
}

// Validate required fields
$required_fields = ['first_name', 'last_name', 'email', 'phone', 'kiosk_id', 'role_id'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

try {
    // Start transaction
    DB::startTransaction();
    
    // Check if email already exists
    $existing_email = DB::queryFirstRow("SELECT user_id FROM users WHERE email = %s", $input['email']);
    if ($existing_email) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit;
    }
    
    // Check if kiosk ID already exists
    $existing_kiosk = DB::queryFirstRow("SELECT user_id FROM users WHERE kioskID = %s", $input['kiosk_id']);
    if ($existing_kiosk) {
        echo json_encode(['success' => false, 'message' => 'Kiosk ID already exists']);
        exit;
    }
    
    // Verify role exists
    $role_exists = DB::queryFirstRow("SELECT id FROM roles WHERE id = %i", $input['role_id']);
    if (!$role_exists) {
        echo json_encode(['success' => false, 'message' => 'Invalid role selected']);
        exit;
    }
    
    // Generate user_name from email (everything before @)
    $user_name = strtok($input['email'], '@');
    
    // Check if user_name already exists
    $existing_user_name = DB::queryFirstRow("SELECT user_id FROM users WHERE user_name = %s", $user_name);
    if ($existing_user_name) {
        // If exists, append random number to make it unique
        $user_name = $user_name . rand(100, 999);
    }
    
    // Create user with selected role
    DB::insert('users', [
        'first_name' => $input['first_name'],
        'last_name' => $input['last_name'],
        'email' => $input['email'],
        'phone' => $input['phone'],
        'kioskID' => $input['kiosk_id'],
        'password' => $input['kiosk_id'], // Storing kioskID as plain text password
        'role_id' => $input['role_id'],
        'user_name' => $user_name, 
        'status' => 'active',
        'created_at' => DB::sqleval('NOW()'),
    ]);
    $user_id = DB::insertId();
    
    // Create applicant record with kioskID
    DB::insert('applicants', [
        'user_id' => $user_id,
        'first_name' => $input['first_name'],
        'last_name' => $input['last_name'],
        'email' => $input['email'],
        'phone_number' => $input['phone'],
        'kioskID' => $input['kiosk_id'],
        'created_at' => DB::sqleval('NOW()'),
    ]);
    $applicant_id = DB::insertId();
    
    // Commit transaction
    DB::commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'User added successfully',
        'data' => [
            'user_id' => $user_id,
            'applicant_id' => $applicant_id,
            'user_name' => $user_name 
        ]
    ]);
    
} catch (Exception $e) {
    DB::rollback();
    error_log("Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error processing request', 'error' => $e->getMessage()]);
}