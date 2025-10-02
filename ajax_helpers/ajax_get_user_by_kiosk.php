<?php
require('../functions.php');

header('Content-Type: application/json');

try {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and sanitize input
    $kiosk_id = $_POST['kiosk_id'] ?? '';
    $kiosk_id = htmlspecialchars(strip_tags($kiosk_id));

    if (empty($kiosk_id)) {
        throw new Exception('KIOSK ID is required');
    }

    // Get user details using MeekroDB
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
            'picture' => $user['picture'],
            'signature' => $user['signature'] ?: null,
            'crew_name' => $user['crew_name'] ?: 'N/A'

        ]
    ];

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
