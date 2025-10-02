<?php
require('../functions.php');
header('Content-Type: application/json');
try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Valid email is required.");
    }

    $email = $data['email'];

    $existingApplicant = DB::query("SELECT * FROM applicants WHERE email = %s", $email);

    if (!$existingApplicant) {
        DB::insert('applicants', [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_initial' => $data['middle_initial'] ?? null,
            'phone_number' => $data['phone_number'],
            'email' => $data['email'],
            'dob' => $data['dob'],
            'kioskID' => $data['kioskID'],
            'gender' => $data['gender'],
            'crew' => $data['crew'],
            'manager' => $data['manager'],
            'job' => 10,
            'available_start_date' => $data['start_date'],
        ]);
    }

    DB::insert('users', [
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'middle_initial' => $data['middle_initial'] ?? null,
        'phone_number' => $data['phone_number'],
        'role_id' => 5,
        'password' => $data['kioskID'],
        'email' => $data['email'],
        'dob' => $data['dob'],
        'kioskID' => $data['kioskID'],
        'gender' => $data['gender'],
        'crew' => $data['crew'],
        'manager' => $data['manager'],
        'job' => 10,
        'start_date' => $data['start_date'],
    ]);

    echo json_encode(['success' => true, 'message' => 'Application submitted successfully!']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
