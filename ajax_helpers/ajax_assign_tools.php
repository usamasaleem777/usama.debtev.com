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

// Build WHERE clause
$whereClause = " WHERE role_id = %i";  // Always filter role_id = 5
$params = [5];

// Apply search filter on user_name
// Apply search filter on user_name and kioskID
if ($search !== '') {
    $whereClause .= " AND (user_name LIKE %s OR kioskID LIKE %s)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

// Ordering
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = (isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) === 'desc') ? 'DESC' : 'ASC';

$columnsMap = [
    0 => 'user_id',
    1 => 'user_name',
    2 => 'kioskID',
    3 => 'picture'
];
$orderColumn = isset($columnsMap[$orderColumnIndex]) ? $columnsMap[$orderColumnIndex] : 'user_id';

// Count total records (only for role_id = 5)
$totalRecordsQuery = "SELECT COUNT(*) FROM users WHERE role_id = %i";
$totalRecords = DB::queryFirstField($totalRecordsQuery, 5);

// Count filtered records
$totalFilteredQuery = "SELECT COUNT(*) FROM users $whereClause";
$totalFiltered = call_user_func_array('DB::queryFirstField', array_merge([$totalFilteredQuery], $params));

// Fetch paginated user data
$dataQuery = "
    SELECT user_id, user_name, kioskID, picture 
    FROM users 
    $whereClause 
    ORDER BY $orderColumn $orderDir 
    LIMIT $start, $length
";
$results = call_user_func_array('DB::query', array_merge([$dataQuery], $params));

// Process results
$data = [];
foreach ($results as $row) {
    $profilePicture = !empty($row['picture']) ? '
        <img src="' . htmlspecialchars($row['picture']) . '" 
             alt="Profile Picture" 
             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">'
        : '<div style="width: 40px; height: 40px; border-radius: 50%; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;"><i class="fas fa-user" style="color: #999;"></i></div>';

    $actions = '
         <button 
        class="btn btn-sm btn-warning assign-tools-btn" 
        data-user-id="' . $row['user_id'] . '" 
        title="Assign Tools to ' . htmlspecialchars($row['user_name']) . '">
        <i class="fas fa-tools"></i>
    </button>';

    $data[] = [
        'user_id' => $row['user_id'],
        'user_name' => $row['user_name'],
        'picture' => $profilePicture,
        'kioskID' => $row['kioskID'],
        'actions' => $actions
    ];
}

// Output JSON
ob_clean();
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
]);
exit;
?>