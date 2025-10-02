<?php
require('../functions.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    try {
        // Ensure you're selecting the correct column name
        $history = DB::query("
    SELECT 
        ut.chekin_at,  
        ut.user_id,
        ut.assigned_tools AS tool_id,
        t.tool_name, 
        t.tool_picture,
        ut.assigned_quantity, 
        ut.checkout_at
    FROM user_tools ut
    JOIN tools t ON ut.assigned_tools = t.tool_id
    WHERE ut.user_id = %i
    ORDER BY ut.checkout_at DESC
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
