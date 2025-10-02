<?php

require('../functions.php');
header('Content-Type: application/json');

// DataTables Parameters
$start  = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$draw   = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

// Ordering Parameters
$orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
$orderDir = isset($_GET['order'][0]['dir']) ? strtoupper($_GET['order'][0]['dir']) : 'ASC';
$orderDir = ($orderDir === 'ASC') ? 'ASC' : 'DESC';

// Column mapping based on JS DataTable columns
$columnsMap = array(
    0 => 'first_name',
    1 => 'last_name', 
    2 => 'phone_number',
    3 => 'email',
    4 => 'position',
    5 => 'job_address',
    6 => 'reference',
    7 => 'generated_date',
    8 => 'kioskID', // Assuming kioskID is the 8th column in the DataTable

);

$orderColumn = isset($columnsMap[$orderColumnIndex]) ? $columnsMap[$orderColumnIndex] : 'id';

// Total records (without filtering but only with status = 'packet send')

// Total records (without filtering but only with status = 'packet send' and not in verification table)
$totalRecords = DB::queryFirstField("SELECT COUNT(*) FROM applicants a
    LEFT JOIN job j ON j.id = a.job_applied
    LEFT JOIN applicant_links al ON al.applicant_id = a.id
    WHERE a.status = %s
    AND NOT EXISTS (
        SELECT 1 FROM employment_eligibility_verification eev 
        WHERE eev.id = a.id
    )", 'packet send');

// Prepare search filter
$whereClause = "WHERE a.status = %s 
    AND NOT EXISTS (
        SELECT 1 FROM employment_eligibility_verification eev 
        WHERE eev.id = a.id
    )";


$params = ['packet send'];

// KioskID filter
$kioskID = isset($_GET['kioskID']) ? trim($_GET['kioskID']) : '';
if ($kioskID !== '') {
    $whereClause .= " AND a.kioskID LIKE %s";
    $params[] = "%$kioskID%";
}

if (!empty($search)) {
    $whereClause .= " AND (first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR phone_number LIKE %s OR position LIKE %s)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Get filtered total
$totalFiltered = !empty($search)
    ? DB::queryFirstField("SELECT COUNT(*) FROM applicants a $whereClause", ...$params)
    : $totalRecords;

// Add limit and offset for pagination
$paramsForQuery = $params;
$paramsForQuery[] = $start;
$paramsForQuery[] = $length;

// Main data query
if ($_SESSION['lang'] === 'es') {

$data = DB::query("
    SELECT 
        a.id,
        a.first_name,
        a.last_name,
        a.phone_number,
        a.email,
        a.kioskID,
        p.position_name_es AS position,
        j.job_address,
        a.reference,
        al.generated_date
    FROM applicants a
    LEFT JOIN job j ON j.id = a.job_applied
    LEFT JOIN applicant_links al ON al.applicant_id = a.id
    LEFT JOIN positions p ON p.id = a.position  -- Add JOIN to get position_name
    $whereClause
     ORDER BY $orderColumn $orderDir ",
    ...$paramsForQuery
);
} else {
    $data = DB::query("
    SELECT 
        a.id,
        a.first_name,
        a.last_name,
        a.phone_number,
        a.email,
        a.kioskID,
        p.position_name AS position,
        j.job_address,
        a.reference,
        al.generated_date
    FROM applicants a
    LEFT JOIN job j ON j.id = a.job_applied
    LEFT JOIN applicant_links al ON al.applicant_id = a.id
    LEFT JOIN positions p ON p.id = a.position  -- Add JOIN to get position_name
    $whereClause
     ORDER BY $orderColumn $orderDir ",
    ...$paramsForQuery
);
}


// Send response
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
]);

?>
