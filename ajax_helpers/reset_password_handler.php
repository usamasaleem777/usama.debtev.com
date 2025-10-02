<?php
include_once('../functions.php');

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Validate input
    if (empty($_POST['token'])) {
        throw new Exception('Invalid request');
    }

    if (empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
        throw new Exception('Please fill in all fields');
    }

    if ($_POST['new_password'] !== $_POST['confirm_password']) {
        throw new Exception('Passwords do not match');
    }

    if (strlen($_POST['new_password']) < 8) {
        throw new Exception('Password must be at least 8 characters');
    }

    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];

    // Check token validity using MeekroDB
    $user = DB::queryFirstRow("SELECT user_id, reset_token_expires FROM users WHERE reset_token = %s", $token);

    if (!$user) {
        throw new Exception('Invalid password reset token');
    }

    $now = new DateTime();
    $expires = new DateTime($user['reset_token_expires']);
    
    if ($now > $expires) {
        throw new Exception('Password reset token has expired');
    }
    DB::update('users', [
        'password' => $newPassword, 
        'reset_token' => null,
        'reset_token_expires' => null
    ], "user_id = %i", $user['user_id']);

    $response['success'] = true;
    $response['message'] = 'Your password has been reset successfully. You can now login with your new password.';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>