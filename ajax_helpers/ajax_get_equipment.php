<?php
require('../functions.php');
header('Content-Type: application/json');

try {
    $equipment = DB::query("SELECT * FROM equipment ORDER BY equipment_name ASC");
    echo json_encode([
        'success' => true,
        'equipment' => $equipment
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching tools: ' . $e->getMessage()
    ]);
}