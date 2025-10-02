<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/classes/db.class.php';

try {
    // Validate request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    if (!isset($_POST['user_id']) || !isset($_FILES['picture'])) {
        throw new Exception("Missing required parameters");
    }

    $user_id = (int)$_POST['user_id'];
    $file = $_FILES['picture'];

    // Validate file upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error: " . $file['error']);
    }

    // Verify MIME type
    $mime = mime_content_type($file['tmp_name']);
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png'
    ];
    
    if (!array_key_exists($mime, $allowedTypes)) {
        throw new Exception("Only JPG and PNG files are allowed");
    }

    // Set upload directory
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/craftHiring/uploads/profile/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Clean existing profile files
    $existingFiles = glob($uploadDir . "profile.*");
    foreach ($existingFiles as $existingFile) {
        if (is_file($existingFile)) {
            unlink($existingFile);
        }
    }

    // Generate new filename
    $extension = $allowedTypes[$mime];
    $newFilename = "profile.$extension";
    $targetPath = $uploadDir . $newFilename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception("Failed to save profile picture");
    }

    // Update database
    DB::update('users', ['picture' => $newFilename], "user_id=%i", $user_id);
    
    $_SESSION['success'] = "Profile picture updated successfully!";
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header("Location: /craftHiring/index.php?route=modules/profile/profile");
exit();