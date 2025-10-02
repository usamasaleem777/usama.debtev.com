<?php
require('../functions.php'); 
header('Content-Type: application/json');

require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate applicant_ID
        if (empty($_POST['applicant_ID'])) {
            throw new Exception("Missing required field: applicant_ID");
        }
        $applicant_ID = intval($_POST['applicant_ID']);

        // Fetch applicant details from DB using MeekroDB
        $row = DB::queryFirstRow(
            "SELECT first_name, last_name, email FROM applicants WHERE id = %i",
            $applicant_ID
        );
        if (!$row) {
            throw new Exception("Applicant not found for ID: $applicant_ID");
        }
        $firstName = htmlspecialchars($row['first_name']);
        $lastName = htmlspecialchars($row['last_name']);
        $email = filter_var($row['email'], FILTER_SANITIZE_EMAIL);
        $fullName = "{$firstName} {$lastName}";

       


        // Construct email template
       $html_message = "
<!DOCTYPE html>
<html>
<head>
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
    <style type=\"text/css\">
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f4f4f9; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        .email-container { max-width: 600px; margin: 40px auto; border: 1px solid #ddd; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); background: white; }
        .header { background-color: #fe5500; color: white; padding: 30px 20px; text-align: center; font-size: 24px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; box-shadow: inset 0 -4px 8px rgba(0,0,0,0.15); }
        .logo { max-width: 120px; height: auto; margin: 0 auto 20px; display: block; background-color: transparent; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .content { padding: 30px 40px; background-color: #ffffff; font-size: 16px; color: #555; line-height: 1.8; }
        .content h2 { color: #fe5500; font-weight: 700; margin-bottom: 20px; text-align: center; }
        .content p { margin-bottom: 18px; }
        .footer { padding: 20px; text-align: center; background-color: #f9f9f9; font-size: 13px; color: #999; border-top: 1px solid #eee; }
        @media only screen and (max-width: 600px) {
            .email-container { margin: 20px 10px; border-radius: 8px; box-shadow: none; }
            .content { padding: 20px 15px; font-size: 15px; }
            .header { font-size: 20px; padding: 20px 15px; }
            .logo { max-width: 100px; margin-bottom: 15px; }
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>
            <img src='https://craftgc.com/wp-content/uploads/2025/04/Craftcon-GC-logo_USA-flag-Gray-logo-new-color-removebg-preview.png' 
                 alt='Craft Construction Logo' 
                 class='logo'>
            Packet Form Submitted
        </div>
        <div class='content'>
            <h2>Dear Hiring Team,</h2>
            <p>
                The following applicant has successfully filled out the packet form. Please review their submission as soon as possible.
            </p>
            <p>
                <strong>Applicant Name:</strong> {$firstName} {$lastName}<br>
                <strong>Email:</strong> {$email}<br>
                <strong>Applicant ID:</strong> {$applicant_ID}
            </p>
            <p>
                Kindly proceed with the next steps in the review process. If you require further information, please refer to the application system or contact the applicant directly.
            </p>
        </div>
        <div class='footer'>
            <p>© " . date('Y') . " Craft Construction. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
";


        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = '32a6f4ea81f97c';
            $mail->Password = '757faefbfc76e6';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('admin@example.com', 'Craft Construction');
            $mail->addAddress('jobs@craftgc.com', 'hiring team');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Application Received - Thank You!';
            $mail->Body = $html_message;
            $mail->AltBody = strip_tags($html_message);

            if ($mail->send()) {
                echo json_encode([
                    "status" => "success", 
                    "message" => "Thank-you email sent successfully to {$email}"
                ]);
            } else {
                throw new Exception("Mailer error: " . $mail->ErrorInfo);
            }
        } catch (Exception $e) {
            throw new Exception("Mailer error: " . $e->getMessage());
        }
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error", 
            "message" => $e->getMessage()
        ]);
    }
    exit;
}
