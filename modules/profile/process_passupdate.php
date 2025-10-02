<?php
// process_passupdate.php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Output buffering (LAST RESORT for headers issue)
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Correct database class path
require_once __DIR__ . '/../../includes/classes/db.class.php';

// Function to log errors (adapt to your logging system)
function logError($message) {
    error_log(date("[Y-m-d H:i:s] ") . $message . "\n", 3, __DIR__ . "/error.log");
}

// Function to safely redirect (after cleaning buffer)
function safeRedirect($url) {
    ob_end_clean(); // Clean the buffer
    header("Location: " . $url);
    exit();
}

// Function to log the user out and redirect
function logoutAndRedirect($loginPageUrl) {
    session_unset(); // Clear all session variables
    session_destroy(); // Destroy the session
    safeRedirect($loginPageUrl);
    exit();
}

// Very aggressive output cleanup
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formtype1']) && $_POST['formtype1'] == "adminuser") {
    try {
        // 2. Sanitize and validate input
        $user_id = (int)$_SESSION['user_id']; // Get user_id from session (secure)
        $current_password = trim($_POST['current_password']);
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            throw new Exception("All password fields are required.");
        }

        if ($new_password !== $confirm_password) {
            throw new Exception("New password and confirm password do not match.");
        }

        if (strlen($new_password) < 8) { // Minimum password length
            throw new Exception("New password must be at least 8 characters long.");
        }

        // 3. Fetch user's current password from the database
        $user = DB::queryFirstRow("SELECT password FROM users WHERE user_id = %i", $user_id);

        if (!$user) {
            throw new Exception("User not found.");
        }

        // 4.  !!!!!!!!!!!!!!!!!!!!!!!!!!! SECURITY ALERT: PLAIN TEXT COMPARISON !!!!!!!!!!!!!!!!!!!!!!!!!!!!
        if ($current_password !== $user['password']) {
            throw new Exception("Incorrect current password.");
        }
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        // 5. Update the password in the database
        DB::update(
            "users",
            array(
                'password' => $new_password // Store the new password (still plain text!)
            ),
            "user_id=%i",
            $user_id
        );

        $_SESSION['success'] = "Password updated successfully. Please log in with your new password.";
        logoutAndRedirect("/craftHiring/login.php"); // Redirect to login page
        exit();

    } catch (Exception $e) {
        logError("Exception: " . $e->getMessage() . ". User ID: " . $user_id . ". POST data: " . print_r($_POST, true));
        $_SESSION['error'] = "Error updating password: " . $e->getMessage();
        safeRedirect("/craftHiring/index.php?route=modules/profile/profile"); // Redirect (even on error)
        exit();
    }
} else {
    // Handle cases where the form wasn't submitted correctly
    logError("Invalid request.  POST data: " . print_r($_POST, true));
    $_SESSION['error'] = "Invalid request.";
    safeRedirect("/craftHiring/index.php?route=modules/profile/profile");
    exit();
}
?>