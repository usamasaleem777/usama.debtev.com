<?php 
header('Content-Type: application/json');

require('../functions.php');


$company_id = isset($_SESSION['company_id']) ? $_SESSION['company_id'] : null;

// Check if company_id is available, otherwise return an empty response
if (!$company_id) {
    echo json_encode(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
    exit;
}

// Parameters sent by DataTables
$start = isset($_POST['start']) ? intval($_POST['start']) : 0; 
$length = isset($_POST['length']) ? intval($_POST['length']) : 10; 
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;

// Ordering
$order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$order_dir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc'; 

// Columns
$columns = array(
    0 => 'user_id',
    1 => 'full_name',
    2 => 'user_email',
    3 => 'password',
    4 => 'role_id',
    5 => 'manager_id',
    6 => 'status'
);

// Base query
$query = "SELECT * FROM admin_users WHERE company_id = '$company_id'";



// Apply search filter
if (!empty($search)) {
    $query .= " AND (full_name LIKE '%$search%' 
                    OR user_email LIKE '%$search%' 
                    OR password LIKE '%$search%' 
                    OR role_id LIKE '%$search%' 
                    OR manager_id LIKE '%$search%')";
}

// Ordering
$order_column_name = isset($columns[$order_column]) ? $columns[$order_column] : 'user_id';
$query .= " ORDER BY $order_column_name $order_dir";

// Limit and offset for pagination
$query .= " LIMIT $start, $length";

// Fetching the data
$results = DB::query($query);

// Total records
$total_query = "SELECT COUNT(*) as total FROM admin_users WHERE company_id = '$company_id'";
$total_records = DB::queryFirstField($total_query);

// Total records with filtering
$total_filtered_query = "SELECT COUNT(*) as total_filtered FROM admin_users WHERE company_id = '$company_id'";

// Apply search filter
if (!empty($search)) {
    $total_filtered_query .= " AND (full_name LIKE '%$search%' 
                                     OR user_email LIKE '%$search%' 
                                     OR password LIKE '%$search%' 
                                     OR role_id LIKE '%$search%' 
                                     OR manager_id LIKE '%$search%')
                                    OR job_id LIKE '%$search%')";

                                     
}

$total_filtered_records = DB::queryFirstField($total_filtered_query);

// Preparing data to return
$data = array();
foreach ($results as $row) {
    $status = $row['user_status'] == 1 ? '<i class="bi bi-check2 text-green fs-2"></i>' : '<i class="bi bi-x-circle text-red fs-2"></i>';
    $data[] = array(
        'user_id' => $row['user_id'],
        'full_name' => getUserFullName($row['user_id']),
        'user_email' => $row['user_email'],
        'password' => $row['password'],
        'role_id' => ShowRoleName($row['role_id']) . '<br>(' . (isset($row['role_desc']) ? $row['role_desc'] : 'N/A') . ')',
        'manager_id' => ShowManagerName($row['manager_id']),
        'status' => $status,
        'actions' => '<a class="btn btn-warning btn-sm edit" href="?route=modules/users/edituser&user_id=' . $row['user_id'] . '"> <span class="material-icons-outlined fs-14" style="font-size: 14px;">edit</span></a>
                      <a id="delete_client_data" 
                         href="index.php?route=modules/users/view_agents&del=yes&del_id=' . $row['user_id'] . '" 
                         class="btn btn-sm btn btn-danger delete2" 
                         data-id="' . $row['user_id'] . '" 
                         data-Msg="Are you sure! You want to delete this record?" 
                         data-bs-toggle="tooltip" 
                         data-bs-original-title="Delete">
                         <span class="material-icons-outlined fs-14" style="font-size: 14px;">delete</span>
                      </a>'
    );
}

// Encode the data into JSON format
$json_data = [
    'draw' => $draw,
    'recordsTotal' => intval($total_records),
    'recordsFiltered' => intval($total_filtered_records),
    'data' => $data
];

// Return the JSON response
echo json_encode($json_data);
?>
