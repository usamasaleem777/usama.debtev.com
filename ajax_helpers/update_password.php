<?php
require('../functions.php');

header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('Invalid CSRF token');
    }

    // Check authentication
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized access');
    }

    // Get POST data
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_id = (int) $_SESSION['user_id'];

    // Validate inputs
    if (empty($current_password)) {
        throw new Exception('Current password is required');
    }

    if (empty($new_password)) {
        throw new Exception('New password is required');
    }

    if (strlen($new_password) < 8) {
        throw new Exception('New password must be at least 8 characters long');
    }

    if ($new_password !== $confirm_password) {
        throw new Exception('New passwords do not match');
    }

    // Get user data - fetch plain text password (assuming it's stored in plain text)
    $user = DB::queryFirstRow("SELECT password FROM users WHERE user_id = %i", $user_id);
    if (!$user) {
        throw new Exception('User not found');
    }

    // Verify current password (plain text comparison)
    if ($current_password !== $user['password']) {
        throw new Exception('Current password is incorrect');
    }

    // Update password (store in plain text)
    DB::update('users', [
        'password' => $new_password
    ], "user_id = %i", $user_id);

    echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>