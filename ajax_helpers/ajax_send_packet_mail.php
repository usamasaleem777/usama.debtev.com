<?php
require('../functions.php');

require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applicant_id'])) {

  $applicant_id = $_POST['applicant_id'];
  $base_link = "http://localhost/craftHiring/forms.php?token=";
  $forms = '0';
  if (isset($_POST['forms']) && is_array($_POST['forms'])) {
    $forms = implode(',', $_POST['forms']);
  }

  // Get applicant data
  $applicant = DB::queryFirstRow("SELECT * FROM applicants WHERE id = %i", $applicant_id);
  if ($applicant == "") {
    echo json_encode([
      "status" => 400,
      "message" => "Applicant not found"
    ]);
    exit();
  }
  $kiosk_id = isset($_POST['kiosk_id']) ? trim($_POST['kiosk_id']) : '';

// Check uniqueness in users table only
$existing_kiosk = DB::queryFirstRow(
    "SELECT user_id FROM users WHERE kioskID = %s",
    $kiosk_id
);

if ($existing_kiosk) {
    echo json_encode([
        "status" => 400,
        "message" => "KIOSK ID already exists. Please use a unique ID."
    ]);
    exit();
}

// Check if empty after validation
if (empty($kiosk_id)) {
    echo json_encode([
        "status" => 400,
        "message" => "KIOSK ID is required"
    ]);
    exit();
}


  // get token (if not exits generate)
  $token_exits = DB::queryFirstRow("SELECT * FROM applicant_links WHERE applicant_id = %i", $applicant_id);

  if ($token_exits == "") {
    // Generate new token
    $token = bin2hex(random_bytes(16));
    $formLink = $base_link . $token;

    DB::insert('applicant_links', [
      'applicant_id' => $applicant_id,
      'token' => $token,
      'form_steps' => $forms,
      'form_link' => $formLink,
      'generated_date' => date('Y-m-d H:i:s'),
      'expires_at' => date('Y-m-d H:i:s', strtotime('+2 days')) // Expires in 2 days
    ]);
  } else {
    // Fetch record
    $token = $token_exits['token'];
    $formLink = $base_link . $token;

    // Update form steps wehre token match
    DB::update('applicant_links', [
      'form_steps' => $forms
    ], "token=%s", $token);
  }

  $applicant_id = $_POST['applicant_id'];

  $kiosk_id = isset($_POST['kiosk_id']) ? trim($_POST['kiosk_id']) : '';

  // Fetch applicant data
  $applicant = DB::queryFirstRow("SELECT * FROM applicants WHERE id = %i", $applicant_id);
  if (!$applicant) {
    echo json_encode([
      "status" => 400,
      "message" => "Applicant not found"
    ]);
    exit();
  }

  // Check if the user already exists in users_table (by email)
  $existing_user = DB::queryFirstRow("SELECT * FROM users WHERE email = %s", $applicant['email']);

  if (!$existing_user) {
    // Insert into users_table
 DB::insert('users', [
    'first_name'  => $applicant['first_name'],  // ✅ store first name
    'name'        => $applicant['first_name'],  // ✅ store first name
    'last_name'   => $applicant['last_name'],   // ✅ store last name
    'user_name'   => $applicant['first_name'] . ' ' . $applicant['last_name'], // ✅ full name
    'email'       => $applicant['email'],
    'password'    => $kiosk_id, // Use kiosk ID as a temporary password
    'kioskID'     => $kiosk_id,
    'phone'       => $applicant['phone_number'], // ✅ correct column name
    'role_id'     => 5, // Adjust role as needed
]);




    $user_id = DB::insertId();

     DB::insert('craft_contracting', [
      'id'=> $applicant_id,
      'first_name'   => $applicant['first_name'],
      'last_name'    => $applicant['last_name'],
      'email'        => $applicant['email'],
    ]);

    // Check for insertion errors
    if (DB::insertId() === null) {
      echo json_encode([
        "status"  => 400,
        "message" => "Failed to create user record"
      ]);
      exit();
    }
  } else {
    $user_id = $existing_user['user_id'];
  }

  // Update applicant record with user_id
  DB::update('applicants', [
    'user_id' => $user_id,
    'kioskID' => $kiosk_id,
  ], "id = %i", $applicant_id);

if ($existing_user) {
  DB::update('users', [
    'name'   => $applicant['first_name'],
    'email'  => $applicant['email'],
    'password' => $kiosk_id,
    'kioskID'=> $kiosk_id,
    'role_id'=> 5,
  ], "user_id = %i", $existing_user['user_id']);
}


  // Generate template 
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

  // Send mail
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
    $mail->addAddress($applicant['email'], $fullName);


    $mail->isHTML(true);
    $mail->Subject = 'Application Update';
    $mail->Body = $mail_template;
    $mail->AltBody = 'Complete your application process';

    if ($mail->send()) {
      echo json_encode([
        "status" => 200,
        "message" => "Email sent successfully",
      ]);
      DB::update('applicants', [
        'status' => 'packet send'
      ], 'id = %i', $applicant_id);
    } else {
      echo json_encode([
        "status" => 400,
        "message" => $mail->ErrorInfo
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
    "message" => "Invalid request"
  ]);
}