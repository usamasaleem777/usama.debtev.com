<?php
require('../functions.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id'] ?? 0);
    $equipments = $_POST['equipment'] ?? []; // This is now an array of objects

    if ($user_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid user.']);
        exit;
    }

    if (empty($equipments)) {
        echo json_encode(['success' => false, 'message' => 'No equipment selected.']);
        exit;
    }

    try {
        DB::startTransaction();

        foreach ($equipments as $equipment) {
            $equipment_id = intval($equipment['id'] ?? 0);
            $serial_number = $equipment['serial_number'] ?? '';

            // Check if equipment exists and is available
            $equipment_record = DB::queryFirstRow(
                "SELECT status FROM equipment WHERE id = %i",
                $equipment_id
            );
            
            if (!$equipment_record) {
                throw new Exception("Invalid equipment ID: $equipment_id");
            }

            if ($equipment_record['status'] !== 'available') {
                throw new Exception("Equipment ID $equipment_id is not available for checkout");
            }

            // Create checkout record
            DB::insert('user_equipments', [
                'user_id' => $user_id,
                'equipment_id' => $equipment_id,
                'serial_number' => $serial_number,
                'checkout_at' => date('Y-m-d H:i:s')
            ]);

            // Update equipment status to checked_out
            DB::update('equipment', 
                ['status' => 'checked_out'], 
                "id = %i", 
                $equipment_id
            );
        }

        DB::commit();
        echo json_encode(['success' => true, 'message' => 'Equipment assigned successfully!']);
    } catch (Exception $e) {
        DB::rollback();
        error_log("Equipment assignment error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}