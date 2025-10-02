<?php
require('../functions.php');

require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applicant_id'])) {

  $applicant_id = $_POST['applicant_id'];


  $applicant = DB::queryFirstRow("SELECT * FROM applicants WHERE id = %i", $applicant_id);
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

             
              

              <p style="margin: 0 0 15px;">
  This is a friendly reminder to complete your <strong>Packet Form</strong> as part of the next step in our hiring process at Craft Contracting.
</p>
<p style="margin: 0 0 15px;">
  Kindly fill out the form at your earliest convenience to ensure we can proceed with your application. If you’ve already submitted it, please disregard this email.
</p>
<p style="margin: 0 0 15px;">
  Thank you for your interest in joining our team!
</p>
<p>
  Best regards,<br>
  <strong>Craft Contracting Team</strong>
</p>
              <p style="margin: 0 0 15px; text-align: center;">
                
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
