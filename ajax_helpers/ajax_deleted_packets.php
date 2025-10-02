<?php
require('../functions.php');
header('Content-Type: application/json');

// DataTables Parameters
$start  = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$draw   = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

// Ordering
$orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
$orderDir = isset($_GET['order'][0]['dir']) ? strtoupper($_GET['order'][0]['dir']) : 'ASC';
$orderDir = ($orderDir === 'ASC') ? 'ASC' : 'DESC';

// Column mapping
$columnsMap = array(
    1 => 'first_name',
    2 => 'last_name',
    3 => 'phone_number',
    4 => 'email',
    5 => 'position',
    6 => 'j.job_title',
    7 => 'created_at',
    8 => 'reference'
);
$orderColumn = isset($columnsMap[$orderColumnIndex]) ? $columnsMap[$orderColumnIndex] : 'a.id';

// Total records without filtering (only deleted records)
$totalRecords = DB::queryFirstField("
    SELECT COUNT(*) 
    FROM applicants a
    INNER JOIN craft_contracting c ON a.id = c.id
    WHERE a.is_deleted = 1 AND c.is_deleted = 1
");

// Prepare search filter
$whereClause = "";
$params = [];

if (!empty($search)) {
    $whereClause = "AND (a.first_name LIKE %s OR a.last_name LIKE %s OR a.phone_number LIKE %s OR a.email LIKE %s OR j.job_title LIKE %s OR p.position_name LIKE %s OR a.reference LIKE %s)";
    $params = array_fill(0, 7, "%$search%");
}

// Get filtered total (only deleted records)
if (!empty($search)) {
    $totalFiltered = DB::queryFirstField("
        SELECT COUNT(*) 
        FROM applicants a
        INNER JOIN craft_contracting c ON a.id = c.id AND c.is_deleted = 1
        LEFT JOIN job j ON a.job_applied = j.id
        LEFT JOIN positions p ON a.position = p.id
        WHERE a.is_deleted = 1 $whereClause
    ", ...$params);
} else {
    $totalFiltered = $totalRecords;
}

// Add pagination
$limitClause = "LIMIT " . intval($start) . ", " . intval($length);

// Main data query (only deleted records)
$data = DB::query("
    SELECT 
        a.*, 
        j.job_title,
        p.position_name
    FROM applicants a
    INNER JOIN craft_contracting c ON a.id = c.id AND c.is_deleted = 1
    LEFT JOIN job j ON a.job_applied = j.id
    LEFT JOIN positions p ON a.position = p.id
    WHERE a.is_deleted = 1 $whereClause
    ORDER BY $orderColumn $orderDir
    $limitClause
", ...(!empty($search) ? $params : []));

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
}
unset($applicant); // Important

// Send response
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
]);