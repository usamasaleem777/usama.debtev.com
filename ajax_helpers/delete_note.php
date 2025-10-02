<?php
require_once('../functions.php');

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $note_id = isset($_POST['note_id']) ? (int) $_POST['note_id'] : 0;
    $applicant_id = isset($_POST['applicant_id']) ? (int) $_POST['applicant_id'] : 0;

    if ($note_id <= 0 || $applicant_id <= 0) {
        throw new Exception('Invalid IDs');
    }

    DB::delete('applicant_notes', 'id=%i AND applicant_id=%i', $note_id, $applicant_id);

    if (DB::affectedRows() === 0) {
        throw new Exception('Note not found or already deleted');
    }

    echo json_encode([
        'success' => true
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>