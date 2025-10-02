<?php
require('../functions.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id'] ?? 0);
    $tools = $_POST['tools'] ?? [];

    if ($user_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid user.']);
        exit;
    }

    try {
        DB::startTransaction();

        foreach ($tools as $tool_id => $toolData) {
            $tool_id = intval($tool_id);

            if (isset($toolData['selected']) && !empty($toolData['quantity'])) {
                $assigned_qty = (int)$toolData['quantity'];

                // Get current tool quantity
                $tool = DB::queryFirstRow("SELECT quantity FROM tools WHERE tool_id = %i", $tool_id);
                if (!$tool) {
                    throw new Exception("Invalid tool ID: $tool_id");
                }

                $available_qty = (int)$tool['quantity'];

                // Validate available quantity
                if ($assigned_qty > $available_qty) {
                    throw new Exception("Not enough quantity available for tool ID $tool_id");
                }

                // Create new assignment record
                DB::insert('user_tools', [
                    'user_id' => $user_id,
                    'assigned_tools' => $tool_id,
                    'assigned_quantity' => $assigned_qty,
                    'checkout_at' => date('Y-m-d H:i:s')
                ]);

                // Update tool quantity
                $new_qty = $available_qty - $assigned_qty;
                DB::update('tools', ['quantity' => $new_qty], "tool_id=%i", $tool_id);
            }
        }

        DB::commit();
        echo json_encode(['success' => true, 'message' => 'Tools assigned successfully!']);
    } catch (Exception $e) {
        DB::rollback();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
