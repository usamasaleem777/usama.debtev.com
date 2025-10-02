<?php

$tasks = DB::query("
    SELECT uj.*, u.user_name, r.name AS role_name, j.name AS job_name
    FROM user_jobs uj
    LEFT JOIN users u ON uj.user_id = u.user_id
    LEFT JOIN roles r ON uj.role_id = r.id
    LEFT JOIN jobs j ON uj.job_id = j.id
    ORDER BY uj.created_at DESC
");
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <!-- Page Header -->
            <div class="page-header d-flex align-items-center justify-content-between mt-2 mb-2">
                <h4 class="fw-bold">Task Management</h4>
                <div>
                    <ol class="breadcrumb float-sm-right m-0">
                        <li class="breadcrumb-item"><a href="index.php" style="color: #fe5500">Home</a></li>
                        <li class="breadcrumb-item active">Task Management</li>
                    </ol>
                </div>
            </div>

            <!-- Tasks Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card rounded-4">
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-3">Task List</h5>

                            <div class="table-responsive">
                                <table class="table align-middle table-hover" id="tasks-datatable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Task ID</th>
                                            <th>User</th>
                                            <th>Role</th>
                                            <th>Job</th>
                                            <th>Created At</th>
                                            <th>Expires At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tasks as $task): ?>
                                            <tr>
                                                <td><?= $task['id'] ?></td>
                                                <td><?= htmlspecialchars($task['user_name'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($task['role_name'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($task['job_name'] ?? 'N/A') ?></td>
                                                <td><?= date('M d, Y H:i', strtotime($task['created_at'])) ?></td>
                                                <td><?= date('M d, Y H:i', strtotime($task['expires_at'])) ?></td>
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

<!-- Style and DataTable Script -->
<style>
    #tasks-datatable th {
        background: #FE5505 !important;
        color: white !important;
    }

    .btn-orange {
        background-color: #FE5500;
        color: white;
        border-color: #FE5500;
    }

    .btn-orange:hover {
        background-color: #e64b00;
        border-color: #e64b00;
    }
</style>

<script>
    $(document).ready(function () {
        $('#tasks-datatable').DataTable({
            searching: true,
            paging: true,
            ordering: true,
            info: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search tasks",
            },
            columnDefs: [
                { type: 'date', targets: [4, 5] }
            ],
            order: [[4, 'desc']]
        });
    });
</script>
