<?php
ob_start();
error_reporting(0);
require('../functions.php');
header('Content-Type: application/json');

// DataTables Parameters
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Normalize filter values to lowercase
$dob_filter = isset($_POST['dob']) ? strtolower($_POST['dob']) : '';
$ssn_filter = isset($_POST['ssn']) ? strtolower($_POST['ssn']) : '';

// Build WHERE clause
$whereClause = " WHERE 1=1";
$params = [];

// Apply search filter
if ($search !== '') {
    $whereClause .= " AND (
        a.first_name LIKE %ss OR 
        a.last_name LIKE %ss OR 
        a.middle_initial LIKE %ss OR
        a.email LIKE %ss OR
        a.zip_code LIKE %ss OR
        a.dob LIKE %ss OR
        eev.ssn LIKE %ss OR
        a.city LIKE %ss OR 
        a.state LIKE %ss 
    )";
    $pattern = '%' . $search . '%';
    for ($i = 0; $i < 9; $i++) {
        $params[] = $pattern;
    }
}

// Apply DOB filter
if ($dob_filter === 'yes') {
    $whereClause .= " AND a.dob IS NOT NULL AND a.dob != ''";
} elseif ($dob_filter === 'no') {
    $whereClause .= " AND (a.dob IS NULL OR a.dob = '')";
}

// Apply SSN filter
if ($ssn_filter === 'yes') {
    $whereClause .= " AND EXISTS (
        SELECT 1 FROM employment_eligibility_verification eev 
        WHERE eev.id = a.id AND eev.ssn IS NOT NULL AND eev.ssn != ''
    )";
} elseif ($ssn_filter === 'no') {
    $whereClause .= " AND NOT EXISTS (
        SELECT 1 FROM employment_eligibility_verification eev 
        WHERE eev.id = a.id AND eev.ssn IS NOT NULL AND eev.ssn != ''
    )";
}

// Ordering
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) === 'desc' ? 'DESC' : 'ASC';

$columnsMap = array(
    1 => 'contract_id',
    2 => 'profile_picture', // New profile picture column
    3 => 'a.first_name',
    4 => 'a.last_name',
    5 => 'a.middle_initial',
    6 => 'contract_city',
    7 => 'contract_state',
    8 => 'contract_zip_code',
    9 => 'a.legal_us_work_eligibility',
    10 => 'eev.ssn1',
    11 => 'u.gender1',
    12 => 'a.dob',
    13 => 'eev.marital_status',
    14 => 'contract_created_at',
);
$orderColumn = isset($columnsMap[$orderColumnIndex]) ? $columnsMap[$orderColumnIndex] : 'a.id';

// Count total records
$totalRecordsQuery = "
    SELECT COUNT(*) 
    FROM applicants a
    INNER JOIN users u ON a.email = u.email
";
$totalRecords = DB::queryFirstField($totalRecordsQuery);

// Count filtered records
$totalFilteredQuery = "
    SELECT COUNT(*) 
    FROM applicants a
    INNER JOIN users u ON a.email = u.email
    LEFT JOIN employment_eligibility_verification eev ON eev.id = a.id
    $whereClause
";
$totalFiltered = call_user_func_array('DB::queryFirstField', array_merge([$totalFilteredQuery], $params));

// Subquery to fetch required data
$subQuery = "
    SELECT 
        a.*, 
        u.picture AS profile_picture, 
        eev.ssn AS ssn1, 
        eev.marital_status AS marital_status,
        u.gender AS gender1,
        cc.id AS contract_id,
        cc.created_at AS contract_created_at,
        cc.state AS contract_state,
        cc.city AS contract_city,
        cc.zip_code AS contract_zip_code,
        CASE 
            WHEN cc.id IS NOT NULL AND DATE(cc.created_at) = CURDATE() THEN 2
            WHEN cc.id IS NOT NULL THEN 1
            ELSE 0 
        END AS in_contract
    FROM (
        SELECT MIN(id) AS id
        FROM applicants
        GROUP BY email
    ) filtered
    JOIN applicants a ON a.id = filtered.id
    JOIN users u ON a.email = u.email
    LEFT JOIN craft_contracting cc ON cc.email = a.email
    LEFT JOIN employment_eligibility_verification eev ON eev.id = a.id
    $whereClause
";

// Final data query
$dataQuery = "
    SELECT a.* 
    FROM ($subQuery) a
    GROUP BY a.id
    ORDER BY $orderColumn $orderDir 
    LIMIT $start, $length
";
$results = call_user_func_array('DB::query', array_merge([$dataQuery], $params));

// Process results and output JSON (unchanged from original)
// ... [rest of the code remains the same]

// Process results
$data = [];
foreach ($results as $row) {
    $inContract = isset($row['in_contract']) ? (int)$row['in_contract'] : 0;

     // Profile picture HTML
    $profilePicture = '';
    if (!empty($row['profile_picture'])) {
        $profilePicture = '
            <img src="' . htmlspecialchars($row['profile_picture']) . '" 
                 alt="Profile Picture" 
                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">';
    } else {
        $profilePicture = '
            <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #f0f0f0; 
                  display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user" style="color: #999;"></i>
            </div>';
    }

    $actions = '';
    if ($inContract === 1 || $inContract === 2) {
        $actions = '
            <a class="btn btn-sm" 
               href="pdfs/pdf_data.php?id=' . $row['contract_id'] . '" 
               style="background-color: #fe5500; border-color: #fe5500; color: white;" 
               title="' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '">    
                 <i class="fas fa-eye"></i>
            </a>
            <a class="btn btn-warning btn-sm" 
               href="index.php?route=modules/forms/edit_packet&id=' . $row['contract_id'] . '" 
               title="Edit ' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '">
                <i class="fas fa-edit"></i>
            </a>';
    } else {
        $actions = '
            <button class="btn btn-secondary btn-sm" disabled title="Not in contract">
                <i class="fas fa-ban"></i>
            </button>';
    }

    $data[] = [
        'DT_RowAttr' => [
            'style' => $inContract === 2
                ? 'background-color:rgb(157, 249, 143) !important; color: white;'
                : ''
        ],
        'contract_id' => $row['contract_id'],
        'actions' => $actions,
        'profile_picture' => $profilePicture,  
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'middle_initial' => $row['middle_initial'] ?? '',
        'contract_city' => $row['contract_city'],
        'contract_state' => $row['contract_state'],
        'zip_code' => $row['zip_code'],
        'legal_us_work_eligibility' => $row['legal_us_work_eligibility'],
        'ssn1' => $row['ssn1'],
        'gender1' => $row['gender1'],
        'dob' => $row['dob'],
        'marital_status' => $row['marital_status'],
        'in_contract' => $inContract,
        'contract_created_at' => $row['contract_created_at'],
    ];
}

ob_clean();
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
]);
exit;
?>
