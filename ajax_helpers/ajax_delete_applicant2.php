<?php
require('../functions.php');
header('Content-Type: application/json');

// Check authorization
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['applicant_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$applicant_id = intval($_POST['applicant_id']);

try {
    // Verify applicant exists and has correct status
    $applicant = DB::queryFirstRow("SELECT id, status FROM applicants WHERE id = %d", $applicant_id);
    if (!$applicant) {
        echo json_encode(['success' => false, 'error' => 'Applicant not found']);
        exit;
    }
    
    if ($applicant['status'] !== 'packet send') {
        echo json_encode([
            'success' => false,
            'error' => 'Only applicants with "packet send" status can be deleted',
            'current_status' => $applicant['status']
        ]);
        exit;
    }

    // Permanent delete - completely remove from applicants table
    DB::query("DELETE FROM applicants WHERE id = %d", $applicant_id);

    echo json_encode([
        'success' => true,
        'message' => 'Applicant deleted permanently',
        'applicant_id' => $applicant_id,
        'method' => 'permanent_delete'
    ]);

} catch (Exception $e) {
    error_log("Delete applicant error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to delete applicant',
        'debug_info' => [
            'message' => $e->getMessage()
        ]
    ]);
}
?>