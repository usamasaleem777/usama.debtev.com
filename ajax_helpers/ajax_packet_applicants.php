<?php
require('../functions.php');
header('Content-Type: application/json');

// DataTables Parameters
$start  = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$draw   = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

// Check if we only want non-existing users
$nonExistingOnly = isset($_GET['non_existing_only']) && $_GET['non_existing_only'] == 1;

// Ordering
$orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 4; // Default to created_at
$orderDir = isset($_GET['order'][0]['dir']) ? strtoupper($_GET['order'][0]['dir']) : 'DESC'; // Default DESC
$orderDir = ($orderDir === 'ASC') ? 'ASC' : 'DESC';

// UPDATED COLUMN MAPPING - Aligned with client-side DataTable
$columnsMap = array(
    0 => 'a.first_name',     // Client index 0
    1 => 'a.last_name',      // Client index 1
    2 => 'a.phone_number',   // Client index 2
    3 => 'a.email',          // Client index 3
    4 => 'a.created_at',     // Client index 4 (created_at)
    5 => 'a.reference',      // Client index 5
    6 => 'a.kioskID'         // Client index 6
);
$orderColumn = isset($columnsMap[$orderColumnIndex]) ? $columnsMap[$orderColumnIndex] : 'a.created_at';

// Base query conditions
$baseConditions = "a.is_deleted = 0 AND c.is_deleted = 0";

// Add condition for non-existing users if requested
if ($nonExistingOnly) {
    $baseConditions .= " AND NOT EXISTS (SELECT 1 FROM users u WHERE u.email = a.email)";
}

// Prepare search filter
$whereClause = "";
$params = [];

// KioskID filter
$kioskID = isset($_GET['kioskID']) ? trim($_GET['kioskID']) : '';
if ($kioskID !== '') {
    $whereClause .= " AND a.kioskID LIKE %s";
    $params[] = "%$kioskID%";
}

// Search filter
if (!empty($search)) {
    $whereClause .= " AND (a.first_name LIKE %s OR a.phone_number LIKE %s OR a.email LIKE %s)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Total records without filtering (only non-deleted)
$totalRecords = DB::queryFirstField("
    SELECT COUNT(*) 
    FROM applicants a
    INNER JOIN craft_contracting c ON a.id = c.id
    WHERE $baseConditions
");

// Get filtered total (only non-deleted)
if ($whereClause !== "") {
    $totalFiltered = DB::queryFirstField("
        SELECT COUNT(*) 
        FROM applicants a
        INNER JOIN craft_contracting c ON a.id = c.id
        WHERE $baseConditions $whereClause
    ", ...$params);
} else {
    $totalFiltered = $totalRecords;
}

// Add pagination
$limitClause = "LIMIT " . intval($start) . ", " . intval($length);

// Main data query (only non-deleted)
if ($whereClause !== "") {
    $data = DB::query("
        SELECT 
            a.*, 
            j.job_title,
            p.position_name,
            (SELECT COUNT(*) FROM users u WHERE u.email = a.email) AS user_exists
        FROM applicants a
        INNER JOIN craft_contracting c ON a.id = c.id AND c.is_deleted = 0
        LEFT JOIN job j ON a.job_applied = j.id
        LEFT JOIN positions p ON a.position = p.id
        WHERE $baseConditions $whereClause
        ORDER BY $orderColumn $orderDir
        $limitClause
    ", ...$params);
} else {
    $data = DB::query("
        SELECT 
            a.*, 
            j.job_title,
            p.position_name,
            (SELECT COUNT(*) FROM users u WHERE u.email = a.email) AS user_exists
        FROM applicants a
        INNER JOIN craft_contracting c ON a.id = c.id AND c.is_deleted = 0
        LEFT JOIN job j ON a.job_applied = j.id
        LEFT JOIN positions p ON a.position = p.id
        WHERE $baseConditions
        ORDER BY $orderColumn $orderDir
        $limitClause
    ");
}

// Get all available jobs
$jobsList = DB::query("
    SELECT id, job_title AS title
    FROM job
    ORDER BY job_title ASC
");

// Get all available positions
if ($_SESSION['lang'] === 'es') {
    $positionsList = DB::query("
        SELECT id, position_name_es AS title
        FROM positions
        ORDER BY position_name ASC
    ");
} else {
    $positionsList = DB::query("
        SELECT id, position_name AS title
        FROM positions
        ORDER BY position_name ASC
    ");
}

// Attach jobs, positions, and current selections to each applicant
foreach ($data as &$applicant) {
    $applicant['applied_jobs'] = $jobsList;
    $applicant['current_job_id'] = $applicant['job_applied'];

    $applicant['available_positions'] = $positionsList;
    $applicant['current_position_id'] = $applicant['position'];
    
    // Convert user_exists to boolean
    $applicant['user_exists'] = (bool)$applicant['user_exists'];
}
unset($applicant); // Important

// Send response
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
]);
?>