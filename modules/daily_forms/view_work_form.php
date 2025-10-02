<?php
// Handle unlock request form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_unlock'])) {
    $report_id = (int)$_POST['report_id'];
    DB::update('daily_work_reports', ['request_edit' => 1], "id = %i", $report_id);
    $_SESSION['unlock_request_success'] = true;
    // We'll let the JavaScript handle the redirect
}

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $user = $_SESSION['user_id'];
    $username = $_SESSION['user_name'];

    // Fetch reports data with job information
    $reports = DB::query(
        "SELECT dwr.id, dwr.report_date, dwr.superintendent_name, 
            dwr.shift, j.job_title, dwr.is_locked, dwr.request_edit 
         FROM daily_work_reports dwr
         LEFT JOIN job j ON dwr.job_id = j.id
         WHERE dwr.foreman_id = %i
         ORDER BY dwr.report_date DESC",
        $user
    );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Work Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --primary-color: #fd7e14;
            --secondary-color: #f8f9fa;
        }
        body {
            background-color: white;
        }
        .form-section {
            background-color: white;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 3px solid var(--primary-color);
        }
        .section-title {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.1rem;
            font-weight: 600;
        }
        #reportsTable_wrapper .dataTables_length select {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.25rem 1.5rem 0.25rem 0.5rem;
            margin: 0 0.5rem;
            background-position: right 0.5rem center;
            background-size: 16px 12px;
        }
        #reportsTable thead th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }
        #reportsTable tbody tr {
            transition: background-color 0.2s;
        }
        #reportsTable tbody tr:hover {
            background-color: rgba(253, 126, 20, 0.05);
        }
        .btn-view {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            transition: all 0.3s;
        }
        .btn-view:hover {
            background-color: #e67300;
            border-color: #e67300;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-pdf {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            transition: all 0.3s;
        }

        .btn-pdf:hover {
            background-color: #bb2d3b;
            border-color: #bb2d3b;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            color: white;
        }

        /* Improved pagination visibility */
        .dataTables_paginate .page-item {
            margin: 0 2px;
        }
        .dataTables_paginate .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white !important;
        }
        .dataTables_paginate .page-link {
            color: var(--primary-color);
            padding: 0.3rem 0.75rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        .dataTables_paginate .page-link:hover {
            color: #e67300;
            background-color: #f8f9fa;
        }
        .dataTables_info {
            padding-top: 0.5rem !important;
            color: #6c757d !important;
            font-size: 0.875rem;
        }
        .dataTables_length label,
        .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0;
        }
        .dataTables_filter input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.25rem 0.5rem;
            margin-left: 0.5rem;
        }
        .dataTables_length {
            margin-bottom: 1rem;
        }
        .dataTables_paginate .page-item:not(.active) .page-link {
            background-color: white;
            color: #495057;
            font-weight: 500;
        }
        .dataTables_info {
            color: #495057 !important;
            font-weight: 500;
        }
        .badge-locked {
            background-color: #6c757d;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            height: 30px;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .btn-sm-custom {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            height: 30px !important;
            display: inline-flex !important;
            align-items: center !important;
        }
    </style>
</head>
<body>
    <div class="container py-3">
        <h4 class="text-center mb-3"><?php echo htmlspecialchars(lang("workform_Daily_Work_Report")); ?></h4>

        <div class="form-section mt-4">
            <h5 class="section-title">Work Reports History</h5>
            <div class="table-responsive">
                <table id="reportsTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Foreman</th>
                            <th>Job</th>
                            <th>Shift</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch reports data with job information
                       $reports = DB::query(
    "SELECT dwr.id, dwr.report_date, dwr.superintendent_name, 
        dwr.shift, j.job_title, dwr.is_locked 
     FROM daily_work_reports dwr
     LEFT JOIN job j ON dwr.job_id = j.id
     WHERE dwr.foreman_id = %i
     ORDER BY dwr.report_date DESC",
    $user);
                       foreach ($reports as $report) {
    // Handle null values
    $date = $report['report_date'] ?? null;
    $foreman = $report['superintendent_name'] ?? null;
    $job = $report['job_title'] ?? null;
    $shiftVal = $report['shift'] ?? null;
    $isLocked = $report['is_locked'] ?? 1; // Default to locked if not set
    
    // Format values or show NA
    $displayDate = $date ? htmlspecialchars($date) : 'NA';
    $displayForeman = $foreman ? htmlspecialchars($foreman) : 'NA';
    $displayJob = $job ? htmlspecialchars($job) : 'NA';
    
    // Handle shift conversion
    if ($shiftVal === null) {
        $displayShift = 'NA';
    } else {
        $displayShift = $shiftVal == 1 ? 'Day' : 'Night';
    }
    
    echo "<tr>
            <td>$displayDate</td>
            <td>$displayForeman</td>
            <td>$displayJob</td>
            <td>$displayShift</td>
            <td>
                <div class='action-buttons'>";
    
    // Always show View and PDF buttons
    echo "<a href='index.php?route=modules/daily_forms/view_report_details&id=" . $report['id'] . "' 
           class='btn btn-view btn-sm btn-sm-custom'>
            <i class='bi bi-eye me-1'></i>View
          </a>
          <a href='index.php?route=modules/daily_forms/pdf_work_form&id=" . $report['id'] . "' 
           class='btn btn-pdf btn-sm btn-sm-custom'>
            <i class='bi bi-file-earmark-pdf me-1'></i>PDF
          </a>";
    
    if ($isLocked == 1) {
        // Show Locked badge if report is locked
        echo "<span class='badge-locked'><i class='bi bi-lock-fill me-1'></i>Locked</span>";
    } else {
        // Show Edit and Lock buttons if report is not locked
        echo "<a href='index.php?route=modules/daily_forms/edit_work_form&id=" . $report['id'] . "' 
               class='btn btn-primary btn-sm btn-sm-custom'>
                <i class='bi bi-pencil me-1'></i>Edit
              </a>
              <a href='index.php?route=modules/daily_forms/lock_work_form&id=" . $report['id'] . "' 
               class='btn btn-sm btn-sm-custom' style='background-color: red; color: white;'>
                <i class='bi bi-lock me-1'></i>Lock
              </a>";
    }
    
    echo "</div>
            </td>
        </tr>";
}
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            $('#reportsTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 10,
                "language": {
                    "lengthMenu": "Show _MENU_ entries",
                    "zeroRecords": "No reports found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "search": "Search:",
                    "paginate": {
                        "first": '<i class="bi bi-chevron-double-left"></i>',
                        "last": '<i class="bi bi-chevron-double-right"></i>',
                        "next": '<i class="bi bi-chevron-right"></i>',
                        "previous": '<i class="bi bi-chevron-left"></i>'
                    }
                },
                "columnDefs": [
                    { "orderable": false, "targets": 4 }
                ]
            });
        });
    </script>
</body>
</html>
<?php 
} else {
    // Redirect if not logged in
    header("Location: login.php");
    exit();
}
?>