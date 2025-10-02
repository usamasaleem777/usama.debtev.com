<?php
require_once('../functions.php'); 

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $applicant_id = isset($_POST['applicant_id']) ? (int) $_POST['applicant_id'] : 0;
    $note_text = isset($_POST['note_text']) ? trim($_POST['note_text']) : '';
    $user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

    if ($applicant_id <= 0) {
        throw new Exception('Invalid applicant ID');
    }

    if (empty($note_text)) {
        throw new Exception('Note text cannot be empty');
    }

    DB::insert('applicant_notes', [
        'applicant_id' => $applicant_id,
        'note_text' => $note_text,
        'created_by' => $user_id,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    $note_id = DB::insertId();

    // Fixed query with proper parameter binding
    $note = DB::queryFirstRow(
        "SELECT *, DATE_FORMAT(created_at, '%%b %%e, %%Y %%l:%%i %%p') as created_at 
         FROM applicant_notes 
         WHERE id = %i",
        $note_id
    );

    echo json_encode([
        'success' => true,
        'note' => $note
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>