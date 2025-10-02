<?php
require('../functions.php');

header('Content-Type: application/json');

// Define debug mode (set to false in production)
define('DEBUG_MODE', true);

try {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and validate input
    $kiosk_id = $_POST['kiosk_id'] ?? '';
    if (empty($kiosk_id)) {
        throw new Exception('KIOSK ID is required');
    }

    // Get user details
    $user = DB::queryFirstRow("
        SELECT 
            u.user_id,
            u.kioskID,
            u.user_name,
            u.picture,
            u.crew_id,
            c.crew_name,  
            u.role_id,
            sig.signature
        FROM users u
        LEFT JOIN application_signatures sig ON sig.user_id = u.user_id
        LEFT JOIN crew c ON c.crew_id = u.crew_id  
        WHERE u.kioskID = %s
        LIMIT 1
    ", $kiosk_id);

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found with this KIOSK ID'
        ]);
        exit;
    }

    // Format response
    $response = [
        'success' => true,
        'message' => 'User found',
        'user' => [
            'user_id' => $user['user_id'],
            'kioskID' => $user['kioskID'],
            'user_name' => $user['user_name'],
            'picture' => $user['picture'] ? 'uploads/profileImages/'.$user['picture'] : 'assets/images/default-avatar.png',
            'signature' => $user['signature'] ? 'data:image/png;base64,'.base64_encode($user['signature']) : null,
            'crew_name' => $user['crew_name'] ?: 'Not assigned',
            'role_id' => $user['role_id']
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    $message = DEBUG_MODE 
        ? $e->getMessage().' [File: '.$e->getFile().', Line: '.$e->getLine().']' 
        : 'An error occurred';
        
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
}