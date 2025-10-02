<?php
// Start output buffering to capture any unwanted output.
ob_start();

// Disable error reporting (or log errors so nothing extra is output).
error_reporting(0);

require('../functions.php');
header('Content-Type: application/json');


// DataTables Parameters
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Custom Filter Parameters
$city = isset($_POST['city']) ? trim($_POST['city']) : '';
$state = isset($_POST['state']) ? trim($_POST['state']) : '';
// $legal = isset($_POST['legal']) ? trim($_POST['legal']) : '';
$over18 = isset($_POST['over18']) ? trim($_POST['over18']) : '';
$reference = isset($_POST['reference']) ? trim($_POST['reference']) : '';
if (!empty($reference)) {
    $whereClause .= " AND LOWER(TRIM(reference)) = %s";  // Match lowercase comparison
    $params[] = $reference;
}




// Ordering Parameters

$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) ? strtoupper($_POST['order'][0]['dir']) : 'ASC';
$orderDir = ($orderDir === 'ASC') ? 'ASC' : 'DESC';

// DataTables columns mapping (adjusted for database schema):
// 0 => id,
// 1 => name (we sort on last_name),
// 3 => phone_number,
// 4 => city,
// 5 => state,
// 6 => legal_us_work_eligibility,
// 7 => over_18,
// 8 => available_start_date.
// (Column 9 is actions, which is not orderable)
$columnsMap = array(
    0 => 'id',
    2 => 'first_name',
    3=> 'last_name',
    4 => 'phone_number',
    5 => 'email',
    6 => 'position_name',
    // 6 => 'city',
    // 7 => 'state',
    // 6 => 'legal_us_work_eligibility',
    7 => 'job',
    8 => 'kioskID',
    // 8 => 'job_applied',
    9 => 'dob',
    10 => 'start_date',
    // 9 => 'over_18',
    // 10 => 'reference', 
    11 => 'created_at'                // Matches column index 9
);
$orderColumn = isset($columnsMap[$orderColumnIndex]) ? $columnsMap[$orderColumnIndex] : 'id';


// Build WHERE Clause with Parameter Binding
$whereClause = " WHERE 1=1";
$params = array();

// if ($city !== '') {
//     $whereClause .= " AND city = %s";
//     $params[] = $city;
// }

// if ($state !== '') {
//     $whereClause .= " AND state = %s";
//     $params[] = $state;
// }


// if ($status !== '') {
//     $whereClause .= " AND status = %s";
//     $params[] = $status;
// }
// if ($legal !== '') {
//     // Convert legal filter to numeric:
//     // If the filter returns "Yes" then use 1; if "No", then 0.
//     $legalStr = strtolower($legal);
//     if ($legalStr === 'yes') {
//         $legalInt = 1;
//     } else if ($legalStr === 'no') {
//         $legalInt = 0;
//     } else {
//         $legalInt = intval($legal);
//     }
//     $whereClause .= " AND legal_us_work_eligibility = %d";
//     $params[] = $legalInt;
// }

// if ($over18 !== '') {
//     // over18 filter to numeric.
//     $over18Str = strtolower($over18);
//     if ($over18Str === 'yes') {
//         $over18Int = 1;
//     } else if ($over18Str === 'no') {
//         $over18Int = 0;
//     } else {
//         $over18Int = intval($over18);
//     }
//     $whereClause .= " AND over_18 = %d";
//     $params[] = $over18Int;
// }

if ($search !== '') {
    $whereClause .= " AND (
        first_name LIKE %s OR 
        last_name LIKE %s OR 
        middle_initial LIKE %s OR
        email LIKE %s OR
        phone_number LIKE %s
    )";
    $pattern = '%' . $search . '%';
    for ($i = 0; $i < 5; $i++) {
        $params[] = $pattern;
    }
}



// Total Records Count (without filtering)

$totalRecords = DB::queryFirstField("SELECT COUNT(*) FROM csv_uploads");

// Total Records Count (with filtering)

$totalFilteredQuery = "SELECT COUNT(*) FROM csv_uploads $whereClause";
$totalFiltered = call_user_func_array('DB::queryFirstField', array_merge(array($totalFilteredQuery), $params));


// Construct Data Query with Ordering and Pagination

// $dataQuery = "SELECT * FROM csv_uploads $whereClause ORDER BY $orderColumn $orderDir LIMIT $start, $length";

// Subquery to filter csv_uploads first
$subQuery = "SELECT * FROM csv_uploads $whereClause";

// Then join with positions
if ($_SESSION['lang'] === 'es') {
    $dataQuery = "
        SELECT a.*, 
            GROUP_CONCAT(p.position_name_es ORDER BY p.position_name_es SEPARATOR ', ') AS position_name,
            j.job_address
        FROM (SELECT * FROM csv_uploads) a
        LEFT JOIN positions p ON FIND_IN_SET(p.id, REPLACE(REPLACE(a.position, ' ', ''), ',,', ','))
        LEFT JOIN job j ON j.id = a.job
        GROUP BY a.id
        ORDER BY $orderColumn $orderDir 
        LIMIT $start, $length
    ";
} else {
    $dataQuery = "
        SELECT a.*, 
            GROUP_CONCAT(p.position_name ORDER BY p.position_name SEPARATOR ', ') AS position_name,
            j.job_address
        FROM (SELECT * FROM csv_uploads) a
        LEFT JOIN positions p ON FIND_IN_SET(p.id, REPLACE(REPLACE(a.position, ' ', ''), ',,', ','))
        LEFT JOIN job j ON j.id = a.job
        GROUP BY a.id
        ORDER BY $orderColumn $orderDir 
        LIMIT $start, $length
    ";
}
// Optionally log the query for debugging.
error_log("Data Query: " . $dataQuery);

$results = call_user_func_array('DB::query', array_merge(array($dataQuery), $params));

$data = array();
if ($results) {
    foreach ($results as $row) {
        // skip packet csv_uploads
        if($row['status'] == 'packet send') { continue; }
        // Construct full name from first_name, optional middle_initial, and last_name.
        // $name = $row['first_name'];
        // if (!empty($row['middle_initial'])) {
        //     $name .= ' ' . $row['middle_initial'] . '.';
        // }
        // $name .= ' ' . $row['last_name'];

        $data[] = [
            'id' => $row['id'],
            'actions' => '
                <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row['id'] . '" data-name="' . htmlspecialchars($row['name']) . '" title="Delete ' . htmlspecialchars($row['name']) . '"><i class="fas fa-trash-alt"></i></button>
                
            ',
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'phone_number' => $row['phone_number'] ?? '',
            'email' => $row['email'] ?? '',
            'position_name' => $row['position_name'],
            'reference' => $row['reference'],
            'kioskID' => $row['kioskID'],
            'created_at' => $row['created_at'],
        ];
    }
}


// Prepare and Output the JSON Response

$response = array(
    "draw" => $draw,
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
);

// Ensure no additional output and return valid JSON.
ob_clean();
echo json_encode($response);
exit;


?>