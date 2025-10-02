<?php
require_once('../functions.php');

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}


$fileId = $_POST['file_id'] ?? null;
$fileName = $_POST['file_name'] ?? null; 

if (!$fileId || !$fileName) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    DB::startTransaction();
    
   
    DB::delete('certification_files', 'id = %i', $fileId);
    
    // Delete physical file
    $filePath = 'ajax_helpers/uploads/certifications/' . $fileName;
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $filePath;
    
    if (file_exists($fullPath)) {
        if (!unlink($fullPath)) {
            throw new Exception('Failed to delete physical file');
        }
    }
    
    DB::commit();
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    DB::rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Deletion failed: ' . $e->getMessage()
    ]);
}