<?php
require('../../functions.php');
header('Content-Type: application/json');

// Check permissions
if (!in_array($_SESSION['role_id'], [$admin_role, $manager_role])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

try {
    // Update is_deleted to 1 (soft delete)
    DB::update('applicants', [
        'is_deleted' => 1
    ], "id = %i", $id);
    
    // Also update craft_contracting if needed (assuming it has is_deleted column)
    DB::update('craft_contracting', [
        'is_deleted' => 1
    ], "id = %i", $id);

    echo json_encode(['success' => true, 'message' => 'Applicant deleted successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>