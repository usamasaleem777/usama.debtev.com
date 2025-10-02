<?php

require('../functions.php');

// Set the content type to JSON.
header('Content-Type: application/json');

try {
    
    $templates = DB::query("SELECT id, short_name, message_type, message_text FROM templates WHERE LOWER(message_type) = %s", "whatsapp");

    echo json_encode($templates);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
