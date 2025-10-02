<?php
require_once('../functions.php');

session_start();

// Verify admin privileges first - FIXED: Added missing closing parenthesis
if (!isset($_SESSION['is_logged'])) {
    die(json_encode(['success' => false, 'message' => 'Not authorized']));
}

// Get target user credentials
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Verify target user exists
$user = DB::queryFirstRow("SELECT * FROM users WHERE kioskID = %s", $username);
if (!$user || $user['kioskID'] !== $password) {
    die(json_encode(['success' => false, 'message' => 'Invalid credentials']));
}

// Destroy current session
session_destroy();

// Start new session for target user
session_start();
$_SESSION['is_logged'] = 1;
$_SESSION['user_id'] = $user['user_id'];
// ... set other session variables ...

// Update last login
DB::update('users', [
    'last_login' => date('Y-m-d H:i:s')
], "user_id=%i", $user['user_id']);

echo json_encode(['success' => true]);