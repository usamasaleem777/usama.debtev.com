<?php
session_start();
require('../functions.php');

// Check permissions first
if (!isset($_SESSION['role_id']) || 
    !in_array($_SESSION['role_id'], [$admin_role, $hr, $manager_role])) {
    die(json_encode(['error' => 'Unauthorized access']));
}

// Get filters from POST
$filters = [
    'city' => trim($_POST['city'] ?? ''),
    'state' => trim($_POST['state'] ?? ''),
    'over18' => $_POST['over18'] ?? '',
    'reference' => trim($_POST['reference'] ?? ''),
    'gender' => trim($_POST['gender'] ?? '') // Make sure gender filter is included
];

// Build query with parameter binding
$query = "
    SELECT 
        a.id,
        a.first_name,
        a.last_name,
        a.gender, 
        a.phone_number,
        p.position_name,
        a.city,
        a.state,
        a.legal_us_work_eligibility,
        a.over_18,
        a.kioskID,
        a.created_at,
        a.status
    FROM applicants a
    LEFT JOIN positions p ON a.position = p.id
    WHERE 1=1
";

$params = [];
if (!empty($filters['city']) && is_string($filters['city'])) {
    $query .= " AND a.city = %s";
    $params[] = $filters['city'];
}
if (!empty($filters['state']) && is_string($filters['state'])) {
    $query .= " AND a.state = %s";
    $params[] = $filters['state'];
}
if (!empty($filters['over18']) && in_array($filters['over18'], ['Yes', 'No'])) {
    $query .= " AND (a.over_18 = %s OR a.over_18 = %i)";
    $params[] = $filters['over18'];
    $params[] = ($filters['over18'] == 'Yes' ? 1 : 0);
}
if (!empty($filters['reference']) && is_string($filters['reference'])) {
    $query .= " AND a.reference LIKE %s";
    $params[] = '%'.$filters['reference'].'%';
}
if (!empty($filters['gender']) && in_array($filters['gender'], ['Male', 'Female', 'Other'])) {
    $query .= " AND a.gender = %s";
    $params[] = $filters['gender'];
}

$query .= " ORDER BY a.id DESC";

try {
    $applicants = DB::query($query, ...$params);
 $processedApplicants = array_map(function($applicant) {
        $applicant['over_18'] = ($applicant['over_18'] === 'Yes' || $applicant['over_18'] == 1) ? 'Yes' : 'No';
        return $applicant;
    }, $applicants);
    if (empty($applicants)) {
        die(json_encode(['error' => 'No applicants found matching your criteria']));
    }

    // Return as JSON for frontend to handle
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $applicants,
        'headers' => [
            'ID',
            'First Name', 
            'Last Name',
            'Gender', 
            'Phone Number',
            'Position',
            'City',
            'State',
            'Legal to Work in US',
            'Over 18',
            // 'Referred By',
            'Kiosk ID',
            'Created At',
            'Status'
        ]
    ]);
} catch (Exception $e) {
    die(json_encode(['error' => 'Export failed: ' . $e->getMessage()]));
}
?>