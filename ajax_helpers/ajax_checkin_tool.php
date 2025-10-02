<?php
require('../functions.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['tool_id'], $_POST['quantity'])) {
    $user_id = intval($_POST['user_id']);
    $tool_id = intval($_POST['tool_id']);
    $quantity = intval($_POST['quantity']);

    try {
        DB::startTransaction();

        // Get the most recent active assignment
        $assignment = DB::queryFirstRow(
            "SELECT * FROM user_tools 
             WHERE user_id = %i AND assigned_tools = %i AND chekin_at IS NULL
             ORDER BY checkout_at DESC 
             LIMIT 1",
            $user_id,
            $tool_id
        );

        if (!$assignment) {
            throw new Exception("No active assignment found for this tool");
        }

        // Verify requested quantity doesn't exceed assigned quantity
        if ($quantity > $assignment['assigned_quantity']) {
            throw new Exception("Cannot check in more than assigned quantity ({$assignment['assigned_quantity']})");
        }

        // Update specific assignment
        DB::update('user_tools', [
            'chekin_at' => DB::sqleval('NOW()'),
        ], 'id = %i', $assignment['id']);

        // Update tool quantity
        DB::query(
            "UPDATE tools SET quantity = quantity + %i WHERE tool_id = %i",
            $quantity,
            $tool_id
        );

        DB::commit();
        echo json_encode([
            'success' => true,
            'message' => 'Tool checked in successfully',
            'chekin_at' => DB::queryFirstField("SELECT NOW()")
        ]);
    } catch (Exception $e) {
        DB::rollback();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
