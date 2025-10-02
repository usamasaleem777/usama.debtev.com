<?php
require('../functions.php');
header('Content-Type: application/json');

if (!isset($_POST['applicant_id']) || !isset($_POST['field']) || !isset($_POST['value'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$applicantId = intval($_POST['applicant_id']);
$field = $_POST['field'];
$value = intval($_POST['value']);

// Only allow specific fields to be updated (for security)
$allowedFields = ['position', 'job_applied'];

if (!in_array($field, $allowedFields)) {
    echo json_encode(['success' => false, 'message' => 'Invalid field']);
    exit;
}

try {
    DB::update('applicants', [
        $field => $value
    ], "id=%i", $applicantId);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
