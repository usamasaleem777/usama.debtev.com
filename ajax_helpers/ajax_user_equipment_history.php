<?php
require('../functions.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    try {
        // Updated query for equipment table
        $history = DB::query("
            SELECT 
                ue.id,
                ue.checkin_at,  
                ue.user_id,
                ue.equipment_id,
                e.equipment_name, 
                e.image_path AS equipment_picture,
                e.serial_number,
                ue.checkout_at,
                CASE 
                    WHEN ue.checkin_at IS NULL THEN 'Assigned'
                    ELSE 'Returned'
                END AS status
            FROM user_equipments ue
            JOIN equipment e ON ue.equipment_id = e.id
            WHERE ue.user_id = %i
            ORDER BY ue.checkout_at DESC
        ", $user_id);

        echo json_encode([
            'success' => true,
            'data' => $history
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error fetching history: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}