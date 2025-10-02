<?php
// Add this at the top to fetch jobs
$jobs = DB::query("SELECT * FROM job ORDER BY id DESC");
$jobNames = DB::query("SELECT DISTINCT job_title FROM job ORDER BY job_title");
// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        // First delete related user_jobs entries
        // DB::delete('user_jobs', "job_id=%i", $_GET['id']);
        
        // Then delete the job
        DB::delete('job', "id=%i", $_GET['id']);
        
        $success = "Job deleted successfully!";
    } catch (MeekroDBException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Corrected form handling with proper variable names
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formtype'])) {
//     try {
//         if ($_POST['formtype'] === 'assign_job') {
//             // Correct variable name from $jobs to $job
//             $job = DB::queryFirstRow("SELECT created_at, expires_at FROM jobs WHERE id=%i", $_POST['job_id']);

//             DB::insert('user_jobs', [
//                 'user_id' => $_POST['user_id'],
//                 'role_id' => $_POST['role_id'],
//                 'job_id' => $_POST['job_id'],
//                 'created_at' => $job['created_at'],
//                 'expires_at' => $job['expires_at']
//             ]);

//             $success = "Job assigned successfully!";
//         }
//     } catch (MeekroDBException $e) {
//         $error = "Assignment error: " . $e->getMessage();
//     }
// }


// Handle filters
// $searchDate = isset($_GET['search_date']) ? $_GET['search_date'] : '';
$jobName = isset($_GET['job_title']) ? $_GET['job_title'] : '';

$conditions = [];
$params = [];

// Add date filter condition
// if (!empty($searchDate)) {
//     $conditions[] = "DATE(created_at) = %s";
//     $params[] = $searchDate;
// }

// Add job name filter condition
if (!empty($jobName)) {
    $conditions[] = "job_title LIKE %s";
    $params[] = '%' . $jobName . '%';
}

// Build the SQL query
// $sql = "SELECT * FROM job";
// if (!empty($conditions)) {
//     $sql .= " WHERE " . implode(' AND ', $conditions);
// }
// $sql .= " ORDER BY created_at DESC";

// Execute the query with parameters
// $jobs = DB::query($sql, ...$params);
// Corrected user and role queries to match actual column names
// $users = DB::query("SELECT 
//     users.user_id AS user_id, 
//     users.user_name, 
//     roles.name AS role_name,
//     roles.id AS role_id 
//     FROM users 
//     LEFT JOIN roles ON users.role_id = roles.id 
//     ORDER BY user_name");
// $roles = DB::query("SELECT id, name AS name FROM roles ORDER BY name");
// ?>
<style>
    #basic-datatable thead th {
        background-color: #fe5500;
        color: white;
        border-bottom: 2px solid #fe5500;
    }

    .pagination .page-item.active .page-link {
        background-color: #fe5500 !important;
        border-color: #fe5500 !important;
    }

    .pagination .page-link {
        color: black !important;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- HTML structure begins here -->
<div class="main-content app-content mt-0">
    <div class="side-app">
        <!-- CONTAINER -->
        <div class="main-container container-fluid">
            <!-- PAGE HEADER WITH BREADCRUMBS -->
            <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
                <div style="margin-top: 15px;">
                    <ol class="breadcrumb float-sm-right mt-2">
                        <!-- Home breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="index.php" style="color: #fe5500"><i
                                    class="fas fa-home me-1"></i><?php echo lang("role_home"); ?></a>
                        </li>
                        <!-- Position breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="#" style="color: #fe5500"><?php echo lang(key: "job_job"); ?></a>
                        </li>
                        <!-- View position breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="#" style="color: #fe5500"><?php echo lang("job_view_job"); ?></a>
                        </li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- Display success/error messages -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success rounded-4"><?= $success ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger rounded-4"><?= $error ?></div>
            <?php endif; ?>

            <!-- JOBS TABLE SECTION -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card rounded-4">
                        <div class="card-body">
                            <!-- Within your card-body section -->
<div class="header-row d-flex justify-content-between align-items-center mb-3">
    <h2 class="card-title fw-bold mb-0"><?php echo lang(key: "job_Job_List"); ?></h2>
    <a href="?route=modules/jobs/create_jobs" class="btn btn-orange">
        <i class="fa fa-plus me-2"></i><?php echo lang(key: "job_create_new_job"); ?>
    </a>
</div>
                            <!-- Filters > -->
<form method="GET" class="mb-4">
     <input type="hidden" name="route" value="modules/jobs/view_jobs"> 
    <!-- <div class="row g-3 align-items-end"> 
        <div class="col-md-3">
            <label for="search_date" class="form-label">Created Date</label>
            <input type="date" name="" id="search_date" class="form-control" 
       value="<?php echo isset($_GET['search_date']) ? htmlspecialchars($_GET['search_date']) : ''; ?>"> -->

<!-- <select name="job_title" id="job_title" class="form-select">
    <option value="">All Jobs</option>
    <?php foreach ($jobNames as $job): ?>
        <option value="<?php echo htmlspecialchars($job['job_title']); ?>" 
            <?php echo (isset($_GET['job_title']) && $_GET['job_title'] === $job['job_title']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($job['job_title']); ?>
        </option>
    <?php endforeach; ?>
</select>
        </div> -->
        <!-- <div class="col-md-3">
    <label for="job_name" class="form-label">Job Name</label>
    <select name="job_name" id="job_name" class="form-select">
        <option value="">All Jobs</option>
        <?php foreach ($jobNames as $jobes): ?>
            <option value="<?= htmlspecialchars($jobes['job_title']) ?>" 
                <?= (isset($_GET['job_name']) && $_GET['job_name'] === $jobes['job_title']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($jobes['job_title']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-orange">Filter</button>
            <a href="?route=modules/jobs/view_jobs" class="btn btn-secondary">Reset</a>
        </div>
    </div> -->
</form>
                            <div class="table-responsive">
                                <table class="table align-middle table-hover" id="basic-datatable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th><?php echo lang(key: "job_title"); ?></th>
                                            <th><?php echo lang(key: "job_code"); ?></th>
                                            <th><?php echo lang(key: "job_state"); ?></th>
                                            <th><?php echo lang(key: "job_city"); ?></th>
                                            <th><?php echo lang(key: "job_address"); ?></th>
                                            <th><?php echo lang(key: "job_zip_code"); ?></th>
                                            <th><?php echo lang(key: "job_status"); ?></th>
                                            <th><?php echo lang(key: "job_hiring"); ?></th>
                                            <!-- <th>Description</th> -->
                                            <th><?php echo lang(key: "job_actions"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($jobs as $job_data): ?>
                                            <tr>
                                                <td><?= $job_data['id'] ?></td>
                                                <td><?= htmlspecialchars($job_data['job_title']) ?></td>
                                                <td><?= htmlspecialchars($job_data['job_code']) ?></td>
                                                <td><?= htmlspecialchars($job_data['job_state']) ?></td>
                                                <td><?= htmlspecialchars($job_data['job_city']) ?></td>
                                                <td><?= htmlspecialchars($job_data['job_address']) ?></td>
                                                <td><?= htmlspecialchars($job_data['job_zip']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $job_data['job_status'] ? 'success' : 'danger' ?>">
                                                        <?= ($job_data['job_status'] ?? 0) ? 'Active' : 'Inactive' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $job_data['job_hiring'] ? 'success' : 'danger' ?>">
                                                        <?= ($job_data['job_hiring'] ?? 0) ? 'Yes' : 'No' ?>
                                                    </span>
                                                </td>
                                                <!-- <td><?= htmlspecialchars($job_data['job_description']) ?></td> -->
                                                <!-- <td><?= htmlspecialchars(substr($job['description'], 0, 50)) . (strlen($job['description']) > 50 ? '...' : '') ?></td> -->
                                                <!-- <td><?= htmlspecialchars($job_data['made_by']) ?></td> -->                                              
                                                <!-- <td><?= date('M d, Y', strtotime($job['created_at'])) ?></td>
                                                <td><?= date('M d, Y', strtotime($job['expires_at'])) ?></td> -->
                                                <td>

                                                    <div class="d-flex gap-2">
                                                        <!-- Assign Button -->
                                                        <!-- <button class="btn btn-sm btn-warning rounded-3"
                                                            data-bs-toggle="modal" data-bs-target="#assignJobModal"
                                                            data-jobid="<?= $job_data['id'] ?>">
                                                            Assign
                                                        </button> -->
                                                        <!-- Edit Button -->
                                                        <a href="?route=modules/jobs/create_jobs&action=edit&id=<?= $job_data['id'] ?>"
                                                            class="btn btn-sm rounded-3 px-3"
                                                            style="background: #FE5505; color: white;">
                                                            <?php echo lang(key: "job_edit"); ?>
                                                        </a>
                                                        <!-- View Button -->
                                                        <!-- <button class="btn btn-sm view-details-btn rounded-3"
                                                            data-bs-toggle="modal" data-bs-target="#viewDetailsModal"
                                                            data-id="<?= $job_data['id'] ?>">
                                                            View
                                                        </button> -->
                                                        <!-- Delete Button -->
                                                        <a href="?route=modules/jobs/view_jobs&action=delete&id=<?= $job_data['id'] ?>" 
                                                        class="btn btn-sm btn-danger rounded-3 delete-btn"> <?php echo lang(key: "job_delete"); ?>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <div>                   
                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VIEW DETAILS MODAL -->
            <div class="modal fade" id="viewDetailsModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Job Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-0">
                                <dt class="col-sm-3">ID</dt>
                                <dd class="col-sm-9" id="detail-id"></dd>

                                <dt class="col-sm-3">Title</dt>
                                <dd class="col-sm-9" id="job_title"></dd>

                                <dt class="col-sm-3">Code</dt>
                                <dd class="col-sm-9" id="job_code"></dd>

                                <dt class="col-sm-3">State</dt>
                                <dd class="col-sm-9" id="job_state"></dd>

                                <dt class="col-sm-3">City</dt>
                                <dd class="col-sm-9" id="job_city"></dd>

                                <dt class="col-sm-3">Address</dt>
                                <dd class="col-sm-9" id="job_address"></dd>
                                
                                <dt class="col-sm-3">Zip Code</dt>
                                <dd class="col-sm-9" id="job_zip"></dd>

                                <!-- <dt class="col-sm-3">Status</dt>
                                <dd class="col-sm-9" id="job_state"></dd>

                                <dt class="col-sm-3">Hiring</dt>
                                <dd class="col-sm-9" id="job_hiring"></dd> -->

                                <!-- <dt class="col-sm-3">Description</dt>
                                <dd class="col-sm-9" id="job_description"></dd> -->
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <!-- Modified Assign Job Modal -->
            <!-- <div class="modal fade" id="assignJobModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Assign Job</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="formtype" value="assign_job">
                                <input type="hidden" name="job_id" id="assignJobId">
                                <input type="hidden" name="role_id" id="selectedRoleId">
                                <div class="mb-3">
    <label class="form-label">Select User</label>
    <select class="form-select" name="user_id" id="userSelect" required>
        <option value="">Choose user...</option>
        <?php foreach ($users as $user): ?>
            <?php if ($user['role_id'] != 1): // Skip users with role_id = 1 ?>
                <option value="<?= $user['user_id'] ?>" data-role-id="<?= $user['role_id'] ?>"
                    data-role-name="<?= ($user['role_name']) ?>">
                    <?= htmlspecialchars($user['user_name']) ?>
                </option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
</div> 

                                <div class="mb-3">
                                    <label class="form-label">User's Role</label>
                                    <input type="text" class="form-control" id="userRoleDisplay" readonly
                                        placeholder="Select a user to see their role">
                                </div> 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-orange" style="color: white;">Assign Job</button>
                            </div> -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- CSS STYLES -->
<style>
/* styles.css */
/* Table Header */
#basic-datatable thead th {
    background-color: #fe5500;
    color: white;
    border-bottom: 2px solid #fe5500;
}

/* Pagination */
.pagination .page-item.active .page-link {
    background-color: #fe5500 !important;
    border-color: #fe5500 !important;
}

.pagination .page-link {
    color: black !important;
}

/* Button Styles */
.btn-orange {
    background-color: #FE5500 !important;
    border-color: #FE5500 !important;
    color: white !important;
    transition: all 0.3s ease;
    min-width: 180px;
}

.btn-orange:hover,
.btn-orange:focus {
    background-color: #CC4400 !important;
    border-color: #CC4400 !important;
    opacity: 0.9;
}

/* Table Styles */
#basic-datatable th {
    background: #FE5505 !important;
    color: white !important;
}

/* View Details Button */
.view-details-btn {
    border-color: #FE5505;
    color: #FE5505;
}

/* Warning Button */
.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #e0a800;
}

/* Page Header */
.page-header {
    margin-bottom: 1.5rem;
    padding: 0 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Breadcrumb Overrides */
.breadcrumb {
    margin-bottom: 0;
    padding-left: 0;
    background-color: transparent;
}

.breadcrumb-item.active {
    color: #fe5500;
}

.breadcrumb-item a {
    color: #fe5500;
    text-decoration: none;
}

/* Modal Rounding */
.modal-content.rounded-4 {
    border-radius: 1rem !important;
}

/* Action Buttons Container */
.action-buttons {
    gap: 0.5rem;
    display: flex;
    flex-wrap: wrap;
}

.job-create-box {
   display: flex;
   align-items: center;
   justify-content: flex-end; /* pushes content to the right */
   
   background-color: #fff; } 





</style>

<!-- JAVASCRIPT -->
<script>
    $(document).ready(function () {
        // Initialize DataTable
        $('#basic-datatable').DataTable({
            searching: true,
            paging: true,
            ordering: true,
            info: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search jobs...",
            },
            columnDefs: [
                { orderable: false, targets: [6] } // Make actions column not sortable
            ]
        });

        // View Details Modal Handler
        $('#viewDetailsModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const jobId = button.data('id');
            const job = <?= json_encode($jobs) ?>.find(j => j.id == jobId);

            if (job) {
                $('#detail-id').text(job.id);
                $('#job_title').text(job.job_title);
                $('#job_code').text(job.job_code);
                $('#job_state').text(job.job_state);
                $('#job_city').text(job.job_city);
                $('#job_address').text(job.job_address);
                $('#job_zip').text(job.job_zip);
                $('#job_description').text(job.job_description);
              
            }
        });
    });
    // In the existing document ready function
    $('#assignJobModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const jobId = button.data('jobid');
        $('#assignJobId').val(jobId);
    });
    $(document).ready(function () {
        // Handle user selection change
        $('#userSelect').change(function () {
            const selectedOption = $(this).find('option:selected');
            const roleName = selectedOption.data('role-name') || 'No role assigned';
            const roleId = selectedOption.data('role-id') || '';

            $('#userRoleDisplay').val(roleName);
            $('#selectedRoleId').val(roleId);
        });

        // Clear fields when modal is closed
        $('#assignJobModal').on('hidden.bs.modal', function () {
            $('#userSelect').val('');
            $('#userRoleDisplay').val('');
            $('#selectedRoleId').val('');
        });
    });


    $(document).ready(function() {
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        const deleteUrl = $(this).attr('href');
        const row = $(this).closest('tr'); // Get the row to remove
        const dataTable = $('#basic-datatable').DataTable(); // Get DataTable instance

        Swal.fire({
            title: '<?php echo lang(key: "job_Are_you_sure?"); ?>',
            text: "<?php echo lang(key: "job_You won't be_able to revert this!"); ?>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#FE5500',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send AJAX request to delete
                $.ajax({
                    url: deleteUrl,
                    type: 'GET',
                    success: function(response) {
                        // Remove the row from DataTable
                        dataTable.row(row).remove().draw(false);
                        Swal.fire('<?php echo lang(key: "job_Deleted!"); ?>', '<?php echo lang(key: "job_The job has been_deleted."); ?>', 'success');
                    },
                    error: function() {
                        Swal.fire('<?php echo lang(key: "job_Error!"); ?>', '<?php echo lang(key: "job_Failed to_delete the job."); ?>', 'error');
                    }
                });
            }
        });
    });
});
</script>
    <!-- Add this JavaScript section at the bottom of your page -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
