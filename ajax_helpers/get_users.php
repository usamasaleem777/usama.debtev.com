<?php
header('Content-Type: application/json');
require('../functions.php');

// Security check
$allowedRoles = array($admin_role, $manager_role);
if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], $allowedRoles)) {
    http_response_code(403);
    die(json_encode(['error' => 'Access denied']));
}

// Parameters
$start  = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 25);
$search = $_POST['search']['value'] ?? '';
$order_column = $_POST['order'][0]['column'] ?? 1;
$order_dir    = $_POST['order'][0]['dir'] ?? 'desc';

// Custom filters
$roleFilter  = $_POST['roleFilter'] ?? '';
$kioskFilter = $_POST['kioskFilter'] ?? '';
$phoneFilter = $_POST['phoneFilter'] ?? '';
$nameFilter  = $_POST['nameFilter'] ?? '';

// Columns
$columns = [
    'picture','user_id','kioskID','user_name','first_name',
    'last_name','email','phone','middle_initial','status',
    'role_name','last_login','actions'
];
$order_by = $columns[$order_column] . ' ' . $order_dir;

// Base query
$baseQuery = "
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    LEFT JOIN applicants a ON u.user_id = a.user_id
";

// Total count (without filters)
$totalRecords = DB::queryFirstField("SELECT COUNT(*) $baseQuery");

// Build WHERE
$whereClauses = [];
$params = [];

if ($search) {
    $s = "%$search%";
    $whereClauses[] = "(u.user_name LIKE %s 
                        OR u.first_name LIKE %s
                        OR u.last_name LIKE %s
                        OR u.email LIKE %s
                        OR u.kioskID LIKE %s)";
    array_push($params, $s, $s, $s, $s, $s);
}

if ($roleFilter) {
    $whereClauses[] = "r.name = %s";
    $params[] = $roleFilter;
}

if ($kioskFilter) {
    $whereClauses[] = "u.kioskID LIKE %s";
    $params[] = "%$kioskFilter%";
}

if ($phoneFilter) {
    $whereClauses[] = "(u.phone LIKE %s OR a.phone_number LIKE %s)";
    $params[] = "%$phoneFilter%";
    $params[] = "%$phoneFilter%";
}

if ($nameFilter) {
    $whereClauses[] = "(u.first_name LIKE %s OR u.last_name LIKE %s)";
    $params[] = "%$nameFilter%";
    $params[] = "%$nameFilter%";
}

$where = $whereClauses ? " WHERE " . implode(" AND ", $whereClauses) : "";

// Filtered count
$filteredRecords = DB::queryFirstField("SELECT COUNT(*) $baseQuery $where", ...$params);

// Data query
$query = "
    SELECT 
        u.user_id, u.kioskID, u.user_name, u.email, u.status, u.picture, u.last_login,
        r.name as role_name,
        COALESCE(u.first_name, a.first_name) as first_name,
        COALESCE(u.middle_initial, a.middle_initial) as middle_initial,
        COALESCE(u.last_name, a.last_name) as last_name,
        COALESCE(u.phone, a.phone_number) as phone
    $baseQuery
    $where
    ORDER BY $order_by
    LIMIT %i, %i
";
$users = DB::query($query, ...array_merge($params, [$start, $length]));

// Prepare data
$data = [];
foreach ($users as $user) {
    $data[] = [
        'picture' => $user['picture']
            ? '<img src="'.htmlspecialchars($user['picture']).'" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">'
            : '<div style="width:40px;height:40px;border-radius:50%;background:#f0f0f0;display:flex;align-items:center;justify-content:center;"><i class="fas fa-user" style="color:#999;"></i></div>',
        'user_id' => $user['user_id'],
        'kioskID' => $user['kioskID'] ?? 'N/A',
        'user_name' => htmlspecialchars($user['user_name']),
        'first_name' => htmlspecialchars($user['first_name'] ?? 'N/A'),
        'last_name' => htmlspecialchars($user['last_name'] ?? 'N/A'),
        'email' => htmlspecialchars($user['email'] ?? 'N/A'),
        'phone' => $user['phone'] ? preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $user['phone']) : 'N/A',
        'middle_initial' => htmlspecialchars($user['middle_initial'] ?? 'N/A'),
        'status' => '<span class="badge bg-'.($user['status'] == 'active' ? 'success' : ($user['status'] == 'suspended' ? 'danger' : 'warning')).'">'.ucfirst($user['status']).'</span>',
        'role_name' => htmlspecialchars($user['role_name'] ?? 'N/A'),
        'last_login' => $user['last_login'] ? date('m/d/Y h:i A', strtotime($user['last_login'])) : 'Never logged in',
        'actions' => generateActionButtons($user)
    ];
}

// Output
echo json_encode([
    'draw' => intval($_POST['draw'] ?? 1),
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $filteredRecords,
    'data' => $data
]);

function generateActionButtons($user) {
    $buttons = '<div class="d-flex gap-1 flex-wrap justify-content-center">';

    // View
    $buttons .= '<a href="?route=modules/users/view&id=' . $user['user_id'] . '" class="btn btn-sm rounded-3 px-2 action-btn" style="background: #FE5505; color: white; font-size: 0.75rem;" title="View">
                    <i class="fas fa-eye"></i>
                    <span class="action-text">View</span>
                </a>';

    // Edit
    $buttons .= '<a href="?route=modules/users/edituser&id=' . $user['user_id'] . '" class="btn btn-sm rounded-3 px-2 action-btn" style="background: #FE5505; color: white; font-size: 0.75rem;" title="Edit">
                    <i class="fas fa-edit"></i>
                    <span class="action-text">Edit</span>
                </a>';

    // Delete
    $buttons .= '<a href="?route=modules/users/view_users&delete_user_id=' . $user['user_id'] . '" class="btn btn-sm btn-danger rounded-3 delete-btn px-2 action-btn" style="font-size: 0.75rem;" title="Delete">
                    <i class="fas fa-trash"></i>
                    <span class="action-text">Delete</span>
                </a>';

    // Status toggle
    $statusConfig = [
        'active' => ['color'=>'#dc3545','icon'=>'fa-user-slash','title'=>'Suspend','text'=>'Suspend'],
        'suspended' => ['color'=>'#28a745','icon'=>'fa-user-clock','title'=>'Inactivate','text'=>'Inactivate'],
        'fire' => ['color'=>'#ffc107','icon'=>'fa-user-check','title'=>'Activate','text'=>'Activate']
    ];
    $status = $user['status'] ?? 'active';
    $config = $statusConfig[$status] ?? $statusConfig['active'];

    $buttons .= '<a href="?route=modules/users/view_users&toggle_status=' . $user['user_id'] . '" class="btn btn-sm rounded-3 px-2 action-btn" style="background: ' . $config['color'] . '; color: white; font-size: 0.75rem;" title="' . $config['title'] . '">
                    <i class="fas ' . $config['icon'] . '"></i>
                    <span class="action-text">' . $config['text'] . '</span>
                </a>';

    $buttons .= '</div>';
    return $buttons;
}
?>
