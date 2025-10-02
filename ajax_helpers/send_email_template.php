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
        $appid = $_POST['appid'] ?? null;
        $templateId = $_POST['template_id'] ?? null;

        if (!$appid) {
            throw new Exception("Applicant ID is required");
        }

        $applicant = DB::queryFirstRow("SELECT * FROM applicants WHERE id = %i", $appid);
        if (!$applicant) {
            throw new Exception("Applicant not found");
        }

        
        $existingLink = DB::queryFirstRow("SELECT * FROM applicant_links WHERE applicant_id = %i", $appid);
        
        if (!$existingLink) {
            $token = bin2hex(random_bytes(16)); 
            $formLink = "http://localhost/craftHiring/forms.php?token=$token";
            
            
            DB::insert('applicant_links', [
                'applicant_id' => $appid,
                'token' => $token,
                'form_link' => $formLink,
                'generated_date' => date('Y-m-d H:i:s'),
                'expires_at' => null 
            ]);
            
            $tokenGenerated = true;
        } else {
           
            $token = $existingLink['token'];
            $formLink = "http://localhost/craftHiring/forms.php?token=$token";
            $tokenGenerated = false;
        }

        if ($templateId) {
            $template = DB::queryFirstRow("SELECT * FROM templates WHERE id = %i", $templateId);
            if (!$template) {
                throw new Exception("Template not found");
            }

            $fullName = $applicant['first_name'] . ' ' . $applicant['last_name'];
            
            
            $plain_message = str_replace(
                ['{full_name}', '{job_applied}'],
                [$fullName, $applicant['position']],
                $template['message_text']
            );
            $plain_message .= "\n\nComplete your application: $formLink";

            
            $html_message = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
                <style type=\"text/css\">
                    /* Base styles */
                    body {
                        font-family: 'Segoe UI', Arial, sans-serif;
                        line-height: 1.6;
                        color: #333;
                        margin: 0;
                        padding: 0;
                        -webkit-text-size-adjust: 100%;
                        -ms-text-size-adjust: 100%;
                    }
                    /* Email container */
                    .email-container {
                        max-width: 600px;
                        margin: 0 auto;
                        border: 1px solid #e0e0e0;
                        border-radius: 8px;
                        overflow: hidden;
                    }
                    /* Header */
                    .header {
                        background-color: #fe5500;
                        color: white;
                        padding: 20px;
                        text-align: center;
                    }
                    /* Logo */
                    .logo {
                        max-width: 100%;
                        height: auto;
                        width: 200px;
                        margin: 0 auto 15px;
                        display: block;
                        background-color: transparent;
                    }
                    /* Content */
                    .content {
                        padding: 25px;
                        background-color: #ffffff;
                    }
                    /* Footer */
                    .footer {
                        padding: 15px;
                        text-align: center;
                        background-color: #f5f5f5;
                        font-size: 12px;
                        color: #777;
                    }
                    /* Button */
                    .button {
                        display: inline-block;
                        padding: 12px 24px;
                        background-color: #fe5500;
                        color: white !important;
                        text-decoration: none;
                        border-radius: 4px;
                        margin: 20px 0;
                        font-weight: bold;
                    }
                    /* Responsive styles */
                    @media only screen and (max-width: 600px) {
                        .email-container {
                            border-radius: 0;
                            border-left: none;
                            border-right: none;
                        }
                        .content {
                            padding: 20px 15px;
                        }
                        .logo {
                            width: 180px;
                        }
                        .button {
                            display: block;
                            margin: 25px auto;
                            text-align: center;
                            width: 80%;
                        }
                    }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='header'>
                        <img src='https://craftgc.com/wp-content/uploads/2025/04/Craftcon-GC-logo_USA-flag-Gray-logo-new-color-removebg-preview.png' 
                             alt='Craft Construction Logo' 
                             class='logo'
                             style='background-color: transparent;'>
                    </div>
                    
                    <div class='content'>
                        " . str_replace(
                            ['{full_name}', '{job_applied}'],
                            [$fullName, $applicant['position']],
                            $template['message_text']
                        ) . "
                        
                        <p style='text-align: center; margin-top: 30px;'>
                            <a href='$formLink' class='button' target='_blank'>Complete your application</a>
                        </p>
                    </div>
                    
                    <div class='footer'>
                        <p>© " . date('Y') . " Craft Construction. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>";

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
                $mail->Subject = $template['subject'] ?? 'Application Update';
                $mail->Body = $html_message;
                $mail->AltBody = $plain_message;

                if ($mail->send()) {
                    echo json_encode([
                        "status" => "success", 
                        "message" => "Email sent successfully",
                        "token_generated" => $tokenGenerated,
                        "link" => $formLink,
                        "applicant_id" => $appid,
                        "token" => $token
                    ]);
                } else {
                    throw new Exception("Mailer error: " . $mail->ErrorInfo);
                }
            } catch (Exception $e) {
                throw new Exception("Mailer error: " . $e->getMessage());
            }
            exit;
        }

        
        $templates = DB::query("SELECT id, short_name, message_text AS body FROM templates WHERE LOWER(message_type) = 'email'");
        echo json_encode([
            "status" => "success", 
            "templates" => $templates,
            "applicant_id" => $appid
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error", 
            "message" => $e->getMessage(),
            "applicant_id" => $appid ?? null
        ]);
        exit;
    }
}