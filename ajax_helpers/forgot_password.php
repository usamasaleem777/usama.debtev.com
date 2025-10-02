<?php
include_once('../functions.php');
require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'An unknown error occurred'];

try {
    // Show errors during development
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate input
    if (empty($_POST['email'])) {
        throw new Exception('Email address is required');
    }

   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

error_log("Looking for user: " . $email);


    // Check if user exists (fix column names here)
    $user = DB::queryFirstRow("SELECT user_id, `name`, email FROM users WHERE email = %s", $email);
    if (!$user) {
        throw new Exception('No account found with this email address');
    }

    // Generate token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Update database
    $update = DB::update('users', [
        'reset_token' => $token,
        'reset_token_expires' => $expires
    ], "user_id = %i", $user['user_id']);

    if (!$update) {
        throw new Exception('Failed to set reset token.');
    }

    // Create reset link
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $resetLink = "$protocol://{$_SERVER['HTTP_HOST']}/reset_password.php?token=$token";

    // Configure PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration (Mailtrap example)
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'ca0959cb66cc9f'; // Replace with your actual username
        $mail->Password = '4c869dda30ce3b'; // Replace with your actual password

        // Email content
        $mail->setFrom('noreply@yourdomain.com', 'Your App Name');
        $mail->addAddress($user['email'], $user['name']);
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-container {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background-color: #fe5500;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h2{
        font-size: 25px;
    }
        .logo {
            max-height: 60px;
            background: transpernt;
        }
        .content {
            padding: 25px;
            background-color: #f9f9f9;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #fe5500;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 15px 0;
        }
        .footer {
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #777;
            background-color: #f0f0f0;
        }
        .expiry-note {
            color: #fe5500;
            font-size: 13px;
        }
             @media screen and (max-width: 360px) {
              .header h2{
        font-size: 14px;
    }
    }
    </style>
</head>
<body>
    <div class='email-container'>
         <div class='header'>
         <img src='https://craftgc.com/wp-content/uploads/2025/04/Craftcon-GC-logo_USA-flag-Gray-logo-new-color-removebg-preview.png' alt='Craft Hiring' class='logo'>
             <h2>Password Reset Request</h2>
         </div>
        
        <div class='content'>
            <p>Hello {$user['name']},</p>
            <p>We received a request to reset your password. Click the button below to set a new password:</p>
            
            <p style='text-align: center; color: white;'>
                <a href='$resetLink' class='button'>Reset Password</a>
            </p>
            
            <p class='expiry-note'>This link will expire in 1 hour for security reasons.</p>
            
            <p>If you didn't request a password reset, please ignore this email or contact support if you have concerns.</p>
        </div>
        
        <div class='footer'>
            <p>© ".date('Y')." Craft Hiring. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
";

        $mail->send();
        $response = [
            'success' => true,
            'message' => 'Password reset link sent to your email'
        ];
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        throw new Exception('Could not send email. Please try again later.');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Forgot Password Error: " . $e->getMessage());
}

echo json_encode($response);
