<?php
require('../functions.php');

header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user has permission
if (!isset($_SESSION['role_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get input data
$assignment_id = isset($_POST['assignment_id']) ? intval($_POST['assignment_id']) : 0;
$equipment_id = isset($_POST['equipment_id']) ? intval($_POST['equipment_id']) : 0;
$serial_number = isset($_POST['serial_number']) ? trim($_POST['serial_number']) : '';
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

// Validate input
if ($assignment_id <= 0 || $equipment_id <= 0 || empty($serial_number) || $user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid input parameters']);
    exit;
}

try {
    // Begin transaction
    DB::startTransaction();
    
    // 1. Check if the assignment exists and is not already checked in
    $assignment = DB::queryFirstRow(
        "SELECT * FROM user_equipments 
        WHERE id = %i AND equipment_id = %i AND user_id = %i AND serial_number = %s AND checkin_at IS NULL 
        ORDER BY checkout_at ASC LIMIT 1",
        $assignment_id, $equipment_id, $user_id, $serial_number
    );
    
    if (!$assignment) {
        DB::rollback();
        echo json_encode(['success' => false, 'message' => 'Equipment assignment not found or already checked in']);
        exit;
    }
    
    // 2. Update the assignment record with checkin time
    DB::update('user_equipments', [
        'checkin_at' => DB::sqlEval('NOW()')
    ], "id = %i", $assignment_id);
    
    // 3. Update equipment availability - Fixed the status value (added quotes)
    DB::update('equipment', [
        'status' => 'available'  // Added quotes around 'available'
    ], "id = %i", $equipment_id);
    
    
    // Commit transaction
    DB::commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Equipment checked in successfully',
        'checkin_time' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    if (method_exists('DB', 'inTransaction') && DB::inTransaction()) {
        DB::rollback();
    }
    
    error_log("Check-in error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error checking in equipment: ' . $e->getMessage()
    ]);
}
?>