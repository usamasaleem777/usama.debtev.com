<?php
require('../functions.php');
header('Content-Type: application/json');

try {
    $tools = DB::query("SELECT * FROM tools ORDER BY tool_name ASC");
    echo json_encode([
        'success' => true,
        'tools' => $tools
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching tools: ' . $e->getMessage()
    ]);
}