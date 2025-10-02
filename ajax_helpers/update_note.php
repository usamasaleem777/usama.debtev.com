<?php
require_once('../functions.php'); 

header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['note_id']) || !is_numeric($_POST['note_id'])) {
        throw new Exception('Invalid note ID');
    }
    
    if (!isset($_POST['applicant_id']) || !is_numeric($_POST['applicant_id'])) {
        throw new Exception('Invalid applicant ID');
    }
    
    if (empty($_POST['note_text'])) {
        throw new Exception('Note text cannot be empty');
    }
    
    $noteId = (int)$_POST['note_id'];
    $applicantId = (int)$_POST['applicant_id'];
    $noteText = trim($_POST['note_text']);
    
    // Update note in database
    DB::update('applicant_notes', [
        'note_text' => $noteText,
        'updated_at' => date('Y-m-d H:i:s')
    ], "id = %i AND applicant_id = %i", $noteId, $applicantId);
    
    // Get updated note
    $updatedNote = DB::queryFirstRow("SELECT * FROM applicant_notes WHERE id = %i", $noteId);
    $updatedNote['created_at'] = date('M j, Y g:i a', strtotime($updatedNote['created_at']));
    
    echo json_encode([
        'success' => true,
        'note' => $updatedNote
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>