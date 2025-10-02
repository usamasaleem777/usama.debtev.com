<?php
require('../functions.php');

require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['applicant_id']) || isset($_POST['email']))) {

    // Get identifier - prefer email if available
    if (isset($_POST['email']) && !empty(trim($_POST['email']))) {
        $email = trim($_POST['email']);
        $applicant = DB::queryFirstRow("SELECT * FROM applicants WHERE email = %s", $email);
    } else {
        $applicant_id = (int)$_POST['applicant_id'];
        $applicant = DB::queryFirstRow("SELECT * FROM applicants WHERE id = %i", $applicant_id);
        $email = $applicant['email'] ?? null;
    }

    if (!$applicant || !$email) {
        echo json_encode([
            "status" => 400,
            "message" => "Applicant not found"
        ]);
        exit();
    }

    $base_link = "http://localhost/craftHiring/forms.php?token=";
    $forms = '0';
    
    if (isset($_POST['forms']) && is_array($_POST['forms'])) {
        $forms = implode(',', $_POST['forms']);
    }

    // Check if user already exists and has a kioskID
    $existing_user = DB::queryFirstRow("SELECT * FROM users WHERE email = %s", $email);
    $kiosk_id = $existing_user['kioskID'] ?? null;

    // Only require new kiosk_id if one doesn't exist
    if (empty($kiosk_id)) {
        $kiosk_id = isset($_POST['kiosk_id']) ? trim($_POST['kiosk_id']) : '';
        
        if (empty($kiosk_id)) {
            echo json_encode([
                "status" => 400,
                "message" => "KIOSK ID is required for new users"
            ]);
            exit();
        }

        // Check Kiosk ID uniqueness only if we're setting a new one
        $existing_kiosk_user = DB::queryFirstRow(
            "SELECT email FROM users WHERE kioskID = %s AND email != %s",
            $kiosk_id,
            $email
        );

        $existing_kiosk_applicant = DB::queryFirstRow(
            "SELECT email FROM applicants WHERE kioskID = %s AND email != %s",
            $kiosk_id,
            $email
        );

        if ($existing_kiosk_user || $existing_kiosk_applicant) {
            echo json_encode([
                "status" => 400,
                "message" => "KIOSK ID already in use by another account"
            ]);
            exit();
        }
    }

    // Handle token generation
    $token_exists = DB::queryFirstRow(
        "SELECT * FROM applicant_links WHERE applicant_id = %i",
        $applicant['id']
    );

    if (!$token_exists) {
        $token = bin2hex(random_bytes(16));
        $formLink = $base_link . $token;

        DB::insert('applicant_links', [
            'applicant_id' => $applicant['id'],
            'token' => $token,
            'form_steps' => $forms,
            'form_link' => $formLink,
            'generated_date' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+2 days'))
        ]);
    } else {
        $token = $token_exists['token'];
        $formLink = $base_link . $token;

        DB::update('applicant_links', [
            'form_steps' => $forms
        ], "token=%s", $token);
    }

    // Create or update user record
    if (!$existing_user) {
        DB::insert('users', [
            'name' => $applicant['first_name'],
            'email' => $email,
            'password' => $kiosk_id,
            'kioskID' => $kiosk_id,
            'role_id' => 5
        ]);

        DB::insert('craft_contracting', [
            'email' => $email,
            'first_name' => $applicant['first_name'],
            'last_name' => $applicant['last_name']
        ]);
    } else {
        DB::update('users', [
            'name' => $applicant['first_name'],
            'password' => $kiosk_id,
            'kioskID' => $kiosk_id,
            'role_id' => 5
        ], "email = %s", $email);
    }

    // Update applicant record
    DB::update('applicants', [
        'kioskID' => $kiosk_id,
        'status' => 'packet sent'
    ], "email = %s", $email);

    // Prepare and send email
    $fullName = $applicant['first_name'] . ' ' . $applicant['last_name'];
    $mail_template = '<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Email Template with Logo</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

  <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#f4f4f4">
    <tr>
      <td align="center">

        <!-- Main Container -->
        <table border="0" cellpadding="0" cellspacing="0" width="600"
          style="background-color: #ffffff; margin-top: 30px; border-radius: 6px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">

          <!-- Header with Logo -->
          <tr>
            <td bgcolor="#fe5500" align="center" style="padding: 30px;">
              <a href="https://craftgc.com/" target="_blank">
                <img src="https://craftgc.com/wp-content/uploads/2025/04/Craftcon-GC-logo_USA-flag-Gray-logo-new-color-removebg-preview.png" alt="Company Logo" width="120"
                  style="display: block; margin-bottom: 10px;">
              </a>
              <div style="color: #ffffff; font-size: 24px; font-weight: bold;">
                Welcome to Our Service
              </div>
            </td>
          </tr>

          <!-- Content -->
          <tr>
            <td style="padding: 30px; color: #333333; font-size: 16px; line-height: 1.5;">
              <p style="margin: 0 0 15px;">Hello <strong>' . $fullName . '</strong>,</p>

                            <!-- Add Credentials Section -->
              <p style="margin: 0 0 15px;">
                Please use these credentials to access your account:<br>
                <strong>Kiosk ID:</strong> ' . $kiosk_id . '<br>
                <strong>Password:</strong> ' . $kiosk_id . '
              </p>
              
              <!-- Add Login Link -->
              <p style="margin: 0 0 15px; text-align: center;">
                <a href="https://craftgc.com/hiring/" 
                   style="display: inline-block; background-color: #fe5500; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 4px;">
                   Login to Your Account
                </a>
              </p>

              <p style="margin: 0 0 15px;">Thank you for your continued interest in joining Craft Contracting. As part
                of our
                hiring process, we are now moving to the next step. Please complete the second application form to
                proceed further in our evaluation. We appreciate your time and look forward to learning more about you.

                Best regards,
                Craft Contracting Team</p>
              <p style="margin: 0 0 15px; text-align: center;">
                <a href="' . $formLink . '"
                  style="display: inline-block; background-color: #fe5500; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 4px;">Processed
                  Application</a>
              </p>
              <p style="margin: 0;">If you did not apply, you can safely ignore this email.</p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td bgcolor="#f1f1f1" align="center" style="padding: 20px; font-size: 12px; color: #666666;">
              &copy; 2024 Craft Contracting. Created by Industry Results.
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>

</body>

</html>';

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '32a6f4ea81f97c';
        $mail->Password = '757faefbfc76e6';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('admin@example.com', 'Admin');
        $mail->addAddress($email, $fullName);
        $mail->isHTML(true);
        $mail->Subject = 'Application Update';
        $mail->Body = $mail_template;
        $mail->AltBody = 'Complete your application process';

        if ($mail->send()) {
            echo json_encode([
                "status" => 200,
                "message" => "Email sent successfully"
            ]);
        } else {
            echo json_encode([
                "status" => 400,
                "message" => "Failed to send email"
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "status" => 400,
            "message" => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => 400,
        "message" => "Invalid request - must provide either applicant_id or email"
    ]);
}