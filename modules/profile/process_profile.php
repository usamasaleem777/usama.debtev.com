<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Correct database class path
require_once __DIR__ . '/../../includes/classes/db.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id = (int)$_POST['user_id'];
        $updateData = [
            'name' => $_POST['name'],
            'user_name' => $_POST['user_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone']
        ];

        DB::update('users', $updateData, "user_id=%i", $user_id);
        $_SESSION['success'] = "Profile updated successfully";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error updating profile: " . $e->getMessage();
    }
}

// 3. Redirect to the desired page with success or error message
header("Location: /craftHiring/index.php?route=modules/profile/profile");
exit();
?>