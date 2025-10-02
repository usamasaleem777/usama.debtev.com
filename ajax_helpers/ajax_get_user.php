<?php
require('../functions.php');
header('Content-Type: application/json');

try {
    $user_id = $_POST['user_id'];
    $user = DB::queryFirstRow("
    SELECT 
        u.user_id,
        u.kioskID,
        u.user_name,
        u.picture,
        u.crew_id,
        c.crew_name,  
        u.role_id,
        sig.signature
    FROM users u
    LEFT JOIN application_signatures sig ON sig.user_id = u.user_id
    LEFT JOIN crew c ON c.crew_id = u.crew_id  
        WHERE user_id = %i 
        LIMIT 1
    ", $user_id);
    
    echo json_encode(['success' => true, 'user' => $user]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}