<?php
require('../functions.php');
require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'])) {
    $reportId = (int)$_POST['report_id'];
    
    // Verify report exists
    $report = DB::queryFirstRow("SELECT id FROM daily_work_reports WHERE id = %i", $reportId);
    if (!$report) {
        echo json_encode(['success' => false, 'message' => 'Report not found']);
        exit;
    }

    // Send approval email
    if (sendReportApprovalEmail($reportId)) {
        // Mark report as approved in database
        DB::update('daily_work_reports', [
            'is_approved' => 1,
            'approved_at' => date('Y-m-d H:i:s')
        ], "id = %i", $reportId);
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send email']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}