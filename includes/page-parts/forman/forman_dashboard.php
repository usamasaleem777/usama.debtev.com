<?php
// Get statistics for Foreman Dashboard with error handling
try {
    $totalCraftsmen = DB::queryFirstField("SELECT COUNT(*) FROM users WHERE role_id = 3 AND status = 'active'") ?? 0;
} catch (Exception $e) {
    $totalCraftsmen = 0;
}

try {
    $tasksInProgress = DB::queryFirstField("SELECT COUNT(*) FROM tasks WHERE status = 'in_progress'") ?? 0;
} catch (Exception $e) {
    $tasksInProgress = 0;
}

try {
    $newCraftsmenThisMonth = DB::queryFirstField("SELECT COUNT(*) FROM users WHERE role_id = 3 AND MONTH(created_at) = MONTH(CURRENT_DATE())") ?? 0;
} catch (Exception $e) {
    $newCraftsmenThisMonth = 0;
}

// Fetch recent tasks with error handling
$recentTasks = [];
try {
    $recentTasks = DB::query("
        SELECT t.id as task_id, t.task_name, t.status, t.due_date, 
               u.name as craftsman_name, u.skill_level
        FROM tasks t
        LEFT JOIN users u ON t.assigned_to = u.id
        ORDER BY t.created_at DESC 
        LIMIT 5
    ");
} catch (Exception $e) {
    // If tasks table doesn't exist, use empty array
    $recentTasks = [];
}

// Fetch attendance data with error handling
$todayAttendance = [];
try {
    $todayAttendance = DB::query("
        SELECT a.id as attendance_id, u.name, a.in_time as check_in, 
               a.out_time as check_out, 
               CASE 
                   WHEN a.in_time IS NULL THEN 'absent'
                   WHEN TIME(a.in_time) > '08:30:00' THEN 'late'
                   ELSE 'present'
               END as status
        FROM attendance a
        JOIN users u ON a.user_id = u.id
        WHERE DATE(a.date) = CURDATE() AND a.role_id = 3
        LIMIT 5
    ");
} catch (Exception $e) {
    // If attendance table doesn't exist, use empty array
    $todayAttendance = [];
}
?>
<style>
    /* Dashboard Styles */
    .bg-teal-50 {
        background-color: rgba(0, 150, 136, 0.1);
    }

    .text-teal {
        color: #009688;
    }

    .bg-blue-50 {
        background-color: rgba(33, 150, 243, 0.1);
    }

    .text-blue {
        color: #2196F3;
    }

    .bg-orange-50 {
        background-color: rgba(254, 85, 0, 0.1);
    }

    .text-orange {
        color: #FE5500;
    }

    .card-icon-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-orange {
        background-color: #FE5500;
        color: white;
    }

    .btn-outline-orange {
        border-color: #FE5500;
        color: #FE5500;
    }

    .btn-outline-orange:hover {
        background-color: #FE5500;
        color: white;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- Dashboard Header -->
<div class="page-breadcrumb d-none d-sm-flex align-items-center py-2">
    <div class="breadcrumb-title pe-3 small" style="color: #FE5500;">
        <?php echo lang("dashboard"); ?>
    </div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0 small">
                <li class="breadcrumb-item"><i class="bx bx-home-alt"></i></li>
                <li class="breadcrumb-item active small" aria-current="page"><?php echo lang("foreman_dashboard"); ?></li>
            </ol>
        </nav>
    </div>
</div>

<!-- First Row - Summary Cards -->
<div class="row row-cols-1 row-cols-md-3 row-cols-xl-3 g-3 mb-3">
    <!-- Active Craftsmen Card -->
    <div class="col">
        <div class="card rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h6 class="mb-0 small"><?php echo lang("active_craftsmen"); ?></h6>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-2 text-indigo"><?= $totalCraftsmen ?></h4>
                        <a href="?route=modules/users/view_users?role=craftsman" class="btn btn-orange rounded-4 px-3 btn-sm small">
                            <i class="fas fa-eye me-1"></i> <?php echo lang("view_all"); ?>
                        </a>
                    </div>
                    <div class="bg-orange-50 rounded-2 card-icon-sm">
                        <i class="fas fa-hard-hat text-orange"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks in Progress Card -->
    <div class="col">
        <div class="card rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h6 class="mb-0 small"><?php echo lang("tasks_in_progress"); ?></h6>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-2 text-indigo"><?= $tasksInProgress ?></h4>
                        <a href="?route=modules/tasks/view_tasks" class="btn btn-orange rounded-4 px-3 btn-sm small">
                            <i class="fas fa-tasks me-1"></i> <?php echo lang("manage_tasks"); ?>
                        </a>
                    </div>
                    <div class="bg-teal-50 rounded-2 card-icon-sm">
                        <i class="fas fa-clipboard-list text-teal"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Craftsmen This Month Card -->
    <div class="col">
        <div class="card rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h6 class="mb-0 small"><?php echo lang("new_craftsmen_this_month"); ?></h6>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-2 text-indigo"><?= $newCraftsmenThisMonth ?></h4>
                        <a href="?route=modules/craftsmen/view_craftsmen?filter=new" class="btn btn-orange rounded-4 px-3 btn-sm small">
                            <i class="fas fa-user-plus me-1"></i> <?php echo lang("view_new"); ?>
                        </a>
                    </div>
                    <div class="bg-blue-50 rounded-2 card-icon-sm">
                        <i class="fas fa-users text-blue"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Second Row - Tasks and Attendance -->
<div class="row g-3">
    <!-- Current Tasks -->
    <div class="col-12 col-lg-7">
        <div class="card rounded-3 h-100">
            <div class="card-header border-0 p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <h6 class="mb-0 small"><?php echo lang("current_job_tasks"); ?></h6>
                    <a href="?route=modules/tasks/create_task" class="btn btn-orange btn-sm small">
                        <i class="fas fa-plus me-1"></i> <?php echo lang("new_task"); ?>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th style="background-color: #FE5500; color: white;"><?php echo lang("task"); ?></th>
                                <th style="background-color: #FE5500; color: white;"><?php echo lang("assigned_to"); ?></th>
                                <th style="background-color: #FE5500; color: white;"><?php echo lang("status"); ?></th>
                                <th style="background-color: #FE5500; color: white;"><?php echo lang("due_date"); ?></th>
                            </tr>

                        </thead>
                        <tbody>
                            <?php if (!empty($recentTasks)): ?>
                                <?php foreach ($recentTasks as $task): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($task['task_name']) ?></td>
                                        <td>
                                            <?= $task['craftsman_name'] ? htmlspecialchars($task['craftsman_name']) : lang("unassigned") ?>
                                            <?php if ($task['skill_level']): ?>
                                                <span class="badge bg-info"><?= $task['skill_level'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?=
                                                                $task['status'] == 'completed' ? 'bg-success' : ($task['status'] == 'in_progress' ? 'bg-warning' : 'bg-secondary')
                                                                ?>">
                                                <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($task['due_date'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-3"><?php echo lang("no_tasks_found"); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Attendance -->
    <div class="col-12 col-lg-5">
        <div class="card rounded-3 h-100">
            <div class="card-header border-0 p-3">
                <h6 class="mb-0 small"><?php echo lang("todays_attendance"); ?></h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (!empty($todayAttendance)): ?>
                        <?php foreach ($todayAttendance as $attendance): ?>
                            <div class="list-group-item border-0 py-2 px-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-user-circle fa-lg text-muted"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 small"><?= htmlspecialchars($attendance['name']) ?></h6>
                                            <div class="text-muted x-small">
                                                <?= $attendance['check_in'] ? lang("in") . date('h:i A', strtotime($attendance['check_in'])) : lang("not_checked_in") ?>
                                                <?= $attendance['check_out'] ? ' | ' . lang("out") . date('h:i A', strtotime($attendance['check_out'])) : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge <?=
                                                        $attendance['status'] == 'present' ? 'bg-success' : ($attendance['status'] == 'late' ? 'bg-warning' : 'bg-danger')
                                                        ?>">
                                        <?= ucfirst($attendance['status']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item border-0 py-3 text-center">
                            <?php echo lang("no_attendance_data"); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer bg-transparent p-2">
                <a href="?route=modules/attendance/view_attendance" class="btn btn-outline-orange btn-sm w-100">
                    <i class="fas fa-calendar-alt me-1"></i> <?php echo lang("view_full_attendance"); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Third Row - Quick Actions -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card rounded-3">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="?route=modules/craftsmen/add_craftsman" class="btn btn-orange btn-sm m-1">
                        <i class="fas fa-user-plus me-1"></i> <?php echo lang("add_craftsman"); ?>
                    </a>
                    <a href="?route=modules/tasks/create_task" class="btn btn-orange btn-sm m-1">
                        <i class="fas fa-tasks me-1"></i> <?php echo lang("create_task"); ?>
                    </a>
                    <a href="?route=modules/schedule/view_schedule" class="btn btn-orange btn-sm m-1">
                        <i class="fas fa-calendar-alt me-1"></i> <?php echo lang("manage_schedule"); ?>
                    </a>
                    <a href="?route=modules/reports/generate" class="btn btn-orange btn-sm m-1">
                        <i class="fas fa-file-export me-1"></i> <?php echo lang("generate_report"); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simple DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#basic-datatable').DataTable({
            searching: true,
            paging: true,
            ordering: true,
            info: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search users...",
            },
            columnDefs: [{
                orderable: false,
                targets: [7]
            }]
        });
    });

    var options = {
        series: [],
        chart: {
            height: 290,
            type: 'donut',
        },
        legend: {
            position: 'bottom',
            show: !1
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                gradientToColors: ['#ee0979', '#17ad37', '#ec6ead'],
                shadeIntensity: 1,
                type: 'vertical',
                opacityFrom: 1,
                opacityTo: 1,
            },
        },
        colors: ["#ff6a00", "#98ec2d", "#3494e6"],
        dataLabels: {
            enabled: !1
        },
        plotOptions: {
            pie: {
                donut: {
                    size: "85%"
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 270
                },
                legend: {
                    position: 'bottom',
                    show: !1
                }
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#chart6"), options);
    chart.render();
</script>