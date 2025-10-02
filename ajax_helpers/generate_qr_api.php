<?php
require('../functions.php');

header('Content-Type: application/json');

try {
    if (!isset($_GET['generate_qr_url'])) {
        throw new Exception('Missing generate_qr_url parameter');
    }

    $user_id = intval($_GET['generate_qr_url']);
    if ($user_id <= 0) {
        throw new Exception('Invalid user ID');
    }

    $unique_hash = bin2hex(random_bytes(16));
    $qr_url = "index.php?route=modules/qr_code_generator/employee&id=$user_id&hash=$unique_hash";

    $result = DB::update('users', ['qr_unique_url' => $qr_url], "user_id=%i", $user_id);

    if ($result === false) {
        throw new Exception('Database update failed');
    }

    echo json_encode([
        'success' => true,
        'url' => $qr_url,
        'user_id' => $user_id
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}
