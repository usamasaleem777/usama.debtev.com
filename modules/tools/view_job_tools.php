<?php
/************** START SECURITY CHECK ***********************/
// Define allowed roles - make sure these variables are defined somewhere in your application
$allowedRoles = array(
					$admin_role, 
					$manager_role,
					$tool_manager
					); // You need to define these variables

// Check if role is allowed
if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], $allowedRoles)) {
    // User does not have access, redirect to home
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => "You do not have permission to view this page."
    ];
    echo '<script>window.location.href = "index.php";</script>';
    exit();
}
/**************** END SECURITY CHECK ***********************/

// Fetch all job-tool assignments
$assignments = DB::query("
    SELECT j.job_title, t.tool_name, a.assigned_quantity
    FROM job_tool_assignments a
    JOIN job j ON a.job_id = j.id
    JOIN tools t ON a.tool_id = t.tool_id
");

// Group tools by job
$grouped = [];
foreach ($assignments as $row) {
    $job = $row['job_title'];
    $toolInfo = $row['tool_name'] . ' (' . $row['assigned_quantity'] . ')';
    
    if (!isset($grouped[$job])) {
        $grouped[$job] = [];
    }

    $grouped[$job][] = $toolInfo;
}
?>
<style>
       @media (max-width: 768px) {
    
        .row1 {
            margin-left: -30px;
            margin-right: -30px;
        }
    }
    /* Medium screens */
    @media (min-width: 769px) and (max-width: 992px) {
       .row1 {
            margin-left: -30px;
            margin-right: -30px;
        }
    }
</style>
<!-- View Job Tool Assignments -->
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid pt-4">
            <div class="page-header d-flex align-items-center justify-content-between mt-2 mb-4">
                <h4 class="mb-0 fw-bold" style="color: #FE5500;"><?php echo lang("tool_Job_Tool_Assignments"); ?></h4>
            </div>
<div class="row1">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table id="assignmentTable" class="table table-bordered text-nowrap border-bottom w-100">
                                    <thead>
                                        <tr>
                                            <th><?php echo lang("tool_id"); ?></th>
                                            <th><?php echo lang("tool_Job_Title"); ?></th>
                                            <th><?php echo lang("tool_Assigned_Tools_(with Quantity)"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 1; foreach ($grouped as $job => $tools): ?>
                                            <tr>
                                                <td><?= $count++ ?></td>
                                                <td><?= htmlspecialchars($job) ?></td>
                                                <td><?= htmlspecialchars(implode(', ', $tools)) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- Include DataTables JS with responsive extension -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" />

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('#assignmentTable').DataTable({
            responsive: true,
            autoWidth: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            pageLength: 10
        });
    });
</script>