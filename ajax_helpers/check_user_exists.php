<?php
require('../functions.php');

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $email = $_POST['email'] ?? '';
    if (empty($email)) {
        throw new Exception('Email parameter is required');
    }

    // Check if user exists using user_id column
    $user = DB::queryFirstRow("SELECT user_id, first_name, last_name FROM users WHERE email = %s", $email);

    if ($user) {
        echo json_encode([
            'exists' => true,
            'user_id' => $user['user_id'],  // Changed from 'id' to 'user_id'
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name']
        ]);
    } else {
        echo json_encode(['exists' => false]);
    }
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'exists' => false
    ]);
}