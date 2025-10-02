<?php

// Get report ID from URL
$reportId = isset($_GET['report_id']) ? intval($_GET['report_id']) : 0;

// Fetch all reports with superintendent names
$reports = DB::query("
    SELECT 
        dwr.id,
        dwr.report_date,
        j.job_title,
        c.crew_name,
        dwr.foreman_id,
        dwr.is_locked,
        u.name AS superintendent_name
    FROM daily_work_reports dwr
    LEFT JOIN job j ON dwr.job_id = j.id
    LEFT JOIN crew c ON dwr.crew_id = c.crew_id
    LEFT JOIN users u ON dwr.foreman_id = u.user_id
    ORDER BY dwr.report_date DESC
");

// Fetch specific report if ID provided
$report = null;
if ($reportId) {
    $report = DB::queryFirstRow("
        SELECT 
            dwr.*,
            j.job_title,
            j.job_address,
            j.job_state,
            c.crew_name,
            u.name AS superintendent_name

        FROM daily_work_reports dwr
        LEFT JOIN job j ON dwr.job_id = j.id
        LEFT JOIN crew c ON dwr.crew_id = c.crew_id
        LEFT JOIN users u ON dwr.foreman_id = u.user_id
        WHERE dwr.id = %i
    ", $reportId);
}

// Initialize variables
$equipment = [];
$materials = [];
$subcontractors = [];
// $signature = null;
$signature = $report['signature_data'] ?? null;
$weatherConditions = [];
$siteConditions = [];
$craftCount = 0;
$totalHeadCount = 0;

// Fetch related data only if report exists
if ($report) {
    // Equipment with tool names
    $equipment = DB::query("
        SELECT 
            eu.id,
            t.tool_name,
            eu.quantity,
            eu.hours_used,
            eu.comments
        FROM equipment_usage eu
        JOIN tools t ON eu.tool_id = t.tool_id
        WHERE eu.report_id = %i
    ", $reportId);

    // Materials used
    $materials = DB::query("
        SELECT 
            id,
            material_name,
            quantity,
            comments
        FROM material_usage
        WHERE report_id = %i
    ", $reportId);

    // Subcontractors
    $subcontractors = DB::query("
        SELECT 
            id,
            subcontractor_name,
            headcount
        FROM subcontractors
        WHERE report_id = %i
    ", $reportId);

    // Calculate headcounts
    $craftCount = (int) ($report['craft_count'] ?? 0);
    $subcontractorHeadCount = 0;
    foreach ($subcontractors as $sub) {
        $subcontractorHeadCount += (int) ($sub['headcount'] ?? 0);
    }
    $totalHeadCount = $craftCount + $subcontractorHeadCount;

    // Weather and site conditions
    $weatherConditions = $report['weather_conditions'] ?
        explode(',', $report['weather_conditions']) : [];

    $siteConditions = $report['site_conditions'] ?
        explode(',', $report['site_conditions']) : [];

    // Signature - handle null safely
    // $signature = DB::queryFirstRow("
    //     SELECT signature_data
    //     FROM daily_work_reports
    //     WHERE id = %i
    // ", $report['foreman_id']);
}

// Get report statistics
$stats = DB::queryFirstRow("
    SELECT 
        COUNT(*) AS total_reports,
        COUNT(DISTINCT crew_id) AS active_crews,
        SUM(is_locked = 1) AS completed_reports,
        SUM(is_locked = 0) AS pending_reports
    FROM daily_work_reports
");

// Assign to variables with default values
$totalReports = $stats['total_reports'] ?? 0;
$activeCrews = $stats['active_crews'] ?? 0;
$completedReports = $stats['completed_reports'] ?? 0;
$pendingReports = $stats['pending_reports'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View - Daily Work Report</title>

    <!-- Add Bootstrap for tab functionality -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        /* All existing styles remain unchanged */
        :root {
            --primary-color: #fd7e14;
            --secondary-color: #f8f9fa;
            --light-gray: #f5f5f5;
            --border-color: #dee2e6;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .admin-header {
            background: linear-gradient(135deg, #fd7e14 0%, #e67300 100%);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .report-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .report-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
        }

        .card-header i {
            margin-right: 10px;
            font-size: 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .info-row {
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            flex-wrap: wrap;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 200px;
        }

        .info-value {
            color: #212529;
            flex: 1;
        }

        .status-badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
        }

        .badge-locked {
            background-color: #dc3545;
            color: white;
        }

        .badge-open {
            background-color: #28a745;
            color: white;
        }

        .signature-container {
            background-color: var(--light-gray);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            padding: 1rem;
            text-align: center;
            margin-top: 1rem;
        }

        .signature-image {
            max-width: 300px;
            max-height: 150px;
            margin: 0 auto;
            display: block;
        }

        .table-container {
            margin-top: 1rem;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            background-color: #e9ecef;
            font-weight: 600;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(253, 126, 20, 0.05);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .section-icon {
            color: var(--primary-color);
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .empty-message {
            color: #6c757d;
            font-style: italic;
            padding: 1rem;
            text-align: center;
            background-color: var(--light-gray);
            border-radius: 5px;
        }

        .summary-section {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 2rem;
        }

        .summary-card {
            flex: 1;
            min-width: 200px;
            background-color: white;
            border-radius: 8px;
            padding: 1.25rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            text-align: center;
            border-left: 4px solid var(--primary-color);
        }

        .summary-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0.5rem 0;
        }

        .summary-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .filter-section {
            background-color: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .report-list {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }

        .report-item {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .report-item:hover {
            background-color: rgba(253, 126, 20, 0.1);
        }

        .report-item.active {
            background-color: rgba(253, 126, 20, 0.2);
            border-left: 4px solid var(--primary-color);
        }

        .report-item h5 {
            margin-bottom: 5px;
        }

        .report-item .badge {
            margin-right: 5px;
        }

        .btn-orange {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-orange:hover {
            background-color: #e67300;
            border-color: #e67300;
            color: white;
        }

        .editable-field {
            background-color: #fffde7;
            border: 1px dashed #ffc107;
            padding: 5px;
            cursor: pointer;
            border-radius: 4px;
            min-height: 20px;
            display: block;
            min-width: 50px;
            /* Ensure fields have minimum width */
        }

        .editable-field.empty-field {
            background-color: #fff9c4;
            font-style: italic;
            color: #6c757d;
        }

        .editable-field:hover {
            background-color: #fff9c4;
        }

        .edit-input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .save-btn-container {
            position: sticky;
            bottom: 20px;
            background-color: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 100;
            text-align: center;
        }

        /* Ensure table cells are editable */
        .table td .editable-field {
            min-height: 30px;
            display: flex;
            align-items: center;
        }

        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
            }

            .info-label {
                min-width: 100%;
                margin-bottom: 5px;
            }

            .action-buttons .btn {
                flex: 1 0 100%;
                margin-bottom: 10px;
            }
        }

        /* New styles for tabbed interface */
        .report-tabs .nav-link {
            color: #495057;
            font-weight: 500;
            padding: 1rem 1.5rem;
            border: none;
            border-bottom: 3px solid transparent;
        }

        .report-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
            background-color: transparent;
        }

        .report-tabs .nav-link:hover:not(.active) {
            color: var(--primary-color);
        }

        .tab-content {
            padding-top: 1.5rem;
        }
    </style>
</head>


<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0"><i class="bi bi-clipboard2-data"></i> Daily Work Reports</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="container">

        <div class="row">
            <div class="col-lg-4">
                <!-- Reports List -->
                <div class="filter-section">
                    <h3 class="mb-4"><i class="bi bi-list-check"></i> Daily Work Reports</h3>

                    <div class="mb-3">
                        <input type="text" id="reportSearch" class="form-control" placeholder="Search reports...">
                    </div>

                    <div class="report-list">
                        <?php if (count($reports) > 0): ?>
                            <?php foreach ($reports as $r): ?>
                                <div class="report-item <?php echo $reportId == $r['id'] ? 'active' : ''; ?>"
                                    data-report-id="<?php echo $r['id']; ?>">
                                    <h5><?php echo htmlspecialchars($r['job_title'] ?? ''); ?></h5>
                                    <p class="mb-1">
                                        <span
                                            class="badge bg-primary"><?php echo date('M j, Y', strtotime($r['report_date'])); ?></span>
                                        <span
                                            class="badge bg-secondary"><?php echo htmlspecialchars($r['crew_name'] ?? ''); ?></span>
                                        <span class="badge <?php echo $r['is_locked'] ? 'badge-locked' : 'badge-open'; ?>">
                                            <?php echo $r['is_locked'] ? 'Locked' : 'Open'; ?>
                                        </span>
                                    </p>
                                    <p class="mb-0 text-muted small">
                                        Superintendent:
                                        <?php if (!empty($r['superintendent_name'])): ?>
                                            <?php echo htmlspecialchars($r['superintendent_name']); ?>
                                        <?php else: ?>
                                            <span class="text-danger">Not assigned</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i> No daily work reports found.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <?php if ($reportId && $report): ?>
                    <!-- Report Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2><i class="bi bi-file-text me-2"></i>Report Details</h2>
                        <div>
                            <span class="badge bg-primary p-2 me-2">Report ID: DWR-<?php echo $report['id']; ?></span>
                            <span
                                class="badge <?php echo $report['is_locked'] ? 'badge-locked' : 'badge-open'; ?> status-badge">
                                <i class="bi <?php echo $report['is_locked'] ? 'bi-lock' : 'bi-unlock'; ?> me-1"></i>
                                <?php echo $report['is_locked'] ? 'Locked' : 'Open'; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Edit/Save Buttons -->
                    <div class="action-buttons mb-4">
                        <button id="edit-report-btn" class="btn btn-orange">
                            <i class="bi bi-pencil me-2"></i>Edit Report
                        </button>
                        <div id="save-buttons" style="display:none;">
                            <button id="save-report-btn" class="btn btn-success">
                                <i class="bi bi-save me-2"></i>Save Changes
                            </button>
                            <button id="cancel-edit-btn" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </button>
                        </div>

                        <button id="approve-report-btn" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>Approve Report
                        </button>
                    </div>

                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs report-tabs" id="reportTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic"
                                type="button" role="tab">
                                <i class="bi bi-info-circle me-1"></i>Basic Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="workforce-tab" data-bs-toggle="tab" data-bs-target="#workforce"
                                type="button" role="tab">
                                <i class="bi bi-people me-1"></i>Work Force
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="equipment-tab" data-bs-toggle="tab" data-bs-target="#equipment"
                                type="button" role="tab">
                                <i class="bi bi-tools me-1"></i>Equipment
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials"
                                type="button" role="tab">
                                <i class="bi bi-box-seam me-1"></i>Materials
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes"
                                type="button" role="tab">
                                <i class="bi bi-journal-text me-1"></i>Notes
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="signature-tab" data-bs-toggle="tab" data-bs-target="#signature"
                                type="button" role="tab">
                                <i class="bi bi-pen-fill me-1"></i>Signature
                            </button>
                        </li>

                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="reportTabContent">
                        <!-- Basic Information Tab -->
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <div class="report-card">
                                <div class="card-header">
                                    <i class="bi bi-info-circle"></i> Basic Information
                                </div>
                                <div class="card-body">
                                    <div class="info-row">
                                        <div class="info-label">Job Site</div>
                                        <div class="info-value">
                                            <?php echo htmlspecialchars($report['job_title'] ?? ''); ?> -
                                            <?php echo htmlspecialchars($report['job_address'] ?? ''); ?>,
                                            <?php echo htmlspecialchars($report['job_state'] ?? ''); ?>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Report Date</div>
                                        <div class="info-value">
                                            <span
                                                class="editable-field <?php echo empty($report['report_date']) ? 'empty-field' : ''; ?>"
                                                data-field="report_date"
                                                data-original="<?php echo htmlspecialchars($report['report_date']); ?>">
                                                <?php
                                                if (!empty($report['report_date'])) {
                                                    echo date('F j, Y', strtotime($report['report_date']));
                                                } else {
                                                    echo 'No date specified';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Shift</div>
                                        <div class="info-value">
                                            <span
                                                class="editable-field <?php echo empty($report['shift']) ? 'empty-field' : ''; ?>"
                                                data-field="shift"
                                                data-original="<?php echo htmlspecialchars($report['shift']); ?>">
                                                <?php
                                                if (!empty($report['shift'])) {
                                                    echo $report['shift'] == 1 ? 'Day Shift' : 'Night Shift';
                                                } else {
                                                    echo 'Shift not specified';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Crew</div>
                                        <div class="info-value">
                                            <span class="<?php echo empty($report['crew_name']) ? 'empty-field' : ''; ?>">
                                                <?php
                                                if (!empty($report['crew_name'])) {
                                                    echo htmlspecialchars($report['crew_name']);
                                                } else {
                                                    echo 'Crew not assigned';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Foreman</div>
                                        <div class="info-value">
                                            <span class="<?php echo empty($report['foreman_id']) ? 'empty-field' : ''; ?>">
                                                <?php
                                                if (!empty($report['foreman_id'])) {
                                                    echo htmlspecialchars($report['foreman_id']);
                                                } else {
                                                    echo 'Foreman not assigned';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Superintendent</div>
                                        <div class="info-value">
                                            <?php if (!empty($report['superintendent_name'])): ?>
                                                <?php echo htmlspecialchars($report['superintendent_name']); ?>
                                            <?php else: ?>
                                                <span class="empty-field">Not assigned</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Craft Count</div>
                                        <div class="info-value">
                                            <span
                                                class="editable-field <?php echo empty($craftCount) ? 'empty-field' : ''; ?>"
                                                data-table="daily_work_reports" data-field="craft_count"
                                                data-original="<?php echo htmlspecialchars($craftCount); ?>">
                                                <?php
                                                if (!empty($craftCount)) {
                                                    echo $craftCount . ' workers';
                                                } else {
                                                    echo 'Craft count not specified';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Weather Conditions</div>
                                        <div class="info-value">
                                            <span
                                                class="editable-field <?php echo empty($weatherConditions) ? 'empty-field' : ''; ?>"
                                                data-table="daily_work_reports" data-field="weather_conditions"
                                                data-original="<?php echo htmlspecialchars(implode(',', $weatherConditions)); ?>">
                                                <?php if (!empty($weatherConditions)): ?>
                                                    <?php foreach ($weatherConditions as $condition): ?>
                                                        <span
                                                            class="badge bg-info me-1"><?php echo htmlspecialchars(ucwords($condition) ?? ''); ?></span>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span>No weather conditions recorded</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Site Conditions</div>
                                        <div class="info-value">
                                            <span
                                                class="editable-field <?php echo empty($siteConditions) ? 'empty-field' : ''; ?>"
                                                data-table="daily_work_reports" data-field="site_conditions"
                                                data-original="<?php echo htmlspecialchars(implode(',', $siteConditions)); ?>">
                                                <?php if (!empty($siteConditions)): ?>
                                                    <?php foreach ($siteConditions as $condition): ?>
                                                        <span
                                                            class="badge bg-secondary me-1"><?php echo htmlspecialchars(ucwords($condition) ?? ''); ?></span>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span>No site conditions recorded</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Work Force Tab -->
                        <div class="tab-pane fade" id="workforce" role="tabpanel">
                            <div class="report-card">
                                <div class="card-header">
                                    <i class="bi bi-people"></i> Work Force
                                </div>
                                <div class="card-body">
                                    <h5 class="mt-0"><i class="bi bi-building"></i> Subcontractors</h5>
                                    <?php if (!empty($subcontractors)): ?>
                                        <div class="table-container">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Company Name</th>
                                                        <th>Headcount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($subcontractors as $sub): ?>
                                                        <tr>
                                                            <td>
                                                                <span
                                                                    class="editable-field <?php echo empty($sub['subcontractor_name']) ? 'empty-field' : ''; ?>"
                                                                    data-table="subcontractors" data-id="<?php echo $sub['id']; ?>"
                                                                    data-field="subcontractor_name"
                                                                    data-original="<?php echo htmlspecialchars($sub['subcontractor_name']); ?>">
                                                                    <?php
                                                                    if (!empty($sub['subcontractor_name'])) {
                                                                        echo htmlspecialchars($sub['subcontractor_name']);
                                                                    } else {
                                                                        echo 'No name provided';
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="editable-field <?php echo empty($sub['headcount']) ? 'empty-field' : ''; ?>"
                                                                    data-table="subcontractors" data-id="<?php echo $sub['id']; ?>"
                                                                    data-field="headcount"
                                                                    data-original="<?php echo htmlspecialchars($sub['headcount']); ?>">
                                                                    <?php
                                                                    if (!empty($sub['headcount'])) {
                                                                        echo $sub['headcount'];
                                                                    } else {
                                                                        echo '0';
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-message">No subcontractors recorded</div>
                                    <?php endif; ?>

                                    <div class="info-row mt-3">
                                        <div class="info-label">Total Head Count</div>
                                        <div class="info-value fw-bold"><?php echo $totalHeadCount; ?> workers</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Equipment Tab -->
                        <div class="tab-pane fade" id="equipment" role="tabpanel">
                            <div class="report-card">
                                <div class="card-header">
                                    <i class="bi bi-tools"></i> Equipment
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($equipment)): ?>
                                        <div class="table-container">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Equipment</th>
                                                        <th>Quantity</th>
                                                        <th>Hours Used</th>
                                                        <th>Comments</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($equipment as $eq): ?>
                                                        <tr>
                                                            <td>
                                                                <span
                                                                    class="editable-field <?php echo empty($eq['tool_name']) ? 'empty-field' : ''; ?>"
                                                                    data-table="equipment_usage" data-id="<?php echo $eq['id']; ?>"
                                                                    data-field="tool_name"
                                                                    data-original="<?php echo htmlspecialchars($eq['tool_name']); ?>">
                                                                    <?php
                                                                    if (!empty($eq['tool_name'])) {
                                                                        echo htmlspecialchars($eq['tool_name']);
                                                                    } else {
                                                                        echo 'No tool name';
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="editable-field <?php echo empty($eq['quantity']) ? 'empty-field' : ''; ?>"
                                                                    data-table="equipment_usage" data-id="<?php echo $eq['id']; ?>"
                                                                    data-field="quantity"
                                                                    data-original="<?php echo htmlspecialchars($eq['quantity']); ?>">
                                                                    <?php
                                                                    if (!empty($eq['quantity'])) {
                                                                        echo $eq['quantity'];
                                                                    } else {
                                                                        echo '0';
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="editable-field <?php echo empty($eq['hours_used']) ? 'empty-field' : ''; ?>"
                                                                    data-table="equipment_usage" data-id="<?php echo $eq['id']; ?>"
                                                                    data-field="hours_used"
                                                                    data-original="<?php echo htmlspecialchars($eq['hours_used']); ?>">
                                                                    <?php
                                                                    if (!empty($eq['hours_used'])) {
                                                                        echo $eq['hours_used'];
                                                                    } else {
                                                                        echo '0';
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="editable-field <?php echo empty($eq['comments']) ? 'empty-field' : ''; ?>"
                                                                    data-table="equipment_usage" data-id="<?php echo $eq['id']; ?>"
                                                                    data-field="comments"
                                                                    data-original="<?php echo htmlspecialchars($eq['comments']); ?>">
                                                                    <?php
                                                                    if (!empty($eq['comments'])) {
                                                                        echo htmlspecialchars($eq['comments']);
                                                                    } else {
                                                                        echo 'No comments';
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-message">No equipment recorded</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Materials Tab -->
                        <div class="tab-pane fade" id="materials" role="tabpanel">
                            <div class="report-card">
                                <div class="card-header">
                                    <i class="bi bi-box-seam"></i> Materials
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($materials)): ?>
                                        <div class="table-container">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Material</th>
                                                        <th>Quantity</th>
                                                        <th>Comments</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($materials as $mat): ?>
                                                        <tr>
                                                            <td>
                                                                <span
                                                                    class="editable-field <?php echo empty($mat['material_name']) ? 'empty-field' : ''; ?>"
                                                                    data-table="material_usage" data-id="<?php echo $mat['id']; ?>"
                                                                    data-field="material_name"
                                                                    data-original="<?php echo htmlspecialchars($mat['material_name']); ?>">
                                                                    <?php
                                                                    if (!empty($mat['material_name'])) {
                                                                        echo htmlspecialchars($mat['material_name']);
                                                                    } else {
                                                                        echo 'No material name';
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="editable-field <?php echo empty($mat['quantity']) ? 'empty-field' : ''; ?>"
                                                                    data-table="material_usage" data-id="<?php echo $mat['id']; ?>"
                                                                    data-field="quantity"
                                                                    data-original="<?php echo htmlspecialchars($mat['quantity']); ?>">
                                                                    <?php
                                                                    if (!empty($mat['quantity'])) {
                                                                        echo $mat['quantity'];
                                                                    } else {
                                                                        echo '0';
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="editable-field <?php echo empty($mat['comments']) ? 'empty-field' : ''; ?>"
                                                                    data-table="material_usage" data-id="<?php echo $mat['id']; ?>"
                                                                    data-field="comments"
                                                                    data-original="<?php echo htmlspecialchars($mat['comments']); ?>">
                                                                    <?php
                                                                    if (!empty($mat['comments'])) {
                                                                        echo htmlspecialchars($mat['comments']);
                                                                    } else {
                                                                        echo 'No comments';
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-message">No materials recorded</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Tab -->
                        <div class="tab-pane fade" id="notes" role="tabpanel">
                            <div class="report-card">
                                <div class="card-header">
                                    <i class="bi bi-journal-text"></i> Notes
                                </div>
                                <div class="card-body">
                                    <div class="info-row">
                                        <div class="info-label">Foreman Notes</div>
                                        <div class="info-value">
                                            <span
                                                class="editable-field <?php echo empty($report['notes']) ? 'empty-field' : ''; ?>"
                                                data-table="daily_work_reports" data-field="notes"
                                                data-original="<?php echo htmlspecialchars($report['notes']); ?>">
                                                <?php
                                                if (!empty($report['notes'])) {
                                                    echo nl2br(htmlspecialchars($report['notes']));
                                                } else {
                                                    echo 'No notes provided - click to add';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Signature Tab -->
                        <!-- Signature Tab -->
                        <div class="tab-pane fade" id="signature" role="tabpanel">
                            <div class="report-card">
                                <div class="card-header">
                                    <i class="bi bi-pen-fill me-1"></i> Signature
                                </div>
                                <div class="card-body">
                                    <div class="signature-image">
                                        <?php if (!empty($signature) && strpos($signature, 'data:image/png') === 0): ?>
                                            <img src="<?php echo htmlspecialchars($signature); ?>" alt="Foreman Signature"
                                                style="background: transparent;">
                                        <?php else: ?>
                                            <div class="no-signature">No signature found</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button Container -->
                    <div class="save-btn-container mt-4" id="sticky-save-buttons" style="display:none;">
                        <button id="save-report-bottom-btn" class="btn btn-success btn-lg">
                            <i class="bi bi-save me-2"></i>Save All Changes
                        </button>
                        <button id="cancel-edit-bottom-btn" class="btn btn-secondary btn-lg ms-2">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center py-5">
                        <i class="bi bi-info-circle display-4 mb-3"></i>
                        <h3>Select a Report</h3>
                        <p class="mb-0">Choose a daily work report from the list to view its details</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS for tab functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Report selection
            const reportItems = document.querySelectorAll('.report-item');
            reportItems.forEach(item => {
                item.addEventListener('click', function () {
                    const reportId = this.getAttribute('data-report-id');
                    window.location.href = `index.php?route=modules/daily_forms/view_daily_form&&report_id=${reportId}`;
                });
            });

            // Report search
            const reportSearch = document.getElementById('reportSearch');
            if (reportSearch) {
                reportSearch.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase();
                    const reportItems = document.querySelectorAll('.report-item');

                    reportItems.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Edit Mode Functionality
            const editBtn = document.getElementById('edit-report-btn');
            const saveBtns = document.getElementById('save-buttons');
            const cancelBtn = document.getElementById('cancel-edit-btn');
            const saveReportBtn = document.getElementById('save-report-btn');
            const saveBottomBtn = document.getElementById('save-report-bottom-btn');
            const cancelBottomBtn = document.getElementById('cancel-edit-bottom-btn');
            const stickySave = document.getElementById('sticky-save-buttons');
            let editMode = false;
            let changes = [];

            // Toggle edit mode
            function toggleEditMode() {
                editMode = !editMode;
                if (editMode) {
                    editBtn.style.display = 'none';
                    saveBtns.style.display = 'block';
                    stickySave.style.display = 'block';
                } else {
                    editBtn.style.display = 'block';
                    saveBtns.style.display = 'none';
                    stickySave.style.display = 'none';
                    // Reset all editable fields to original values
                    document.querySelectorAll('.editable-field.editing').forEach(field => {
                        const original = field.getAttribute('data-original');
                        field.textContent = original;
                        field.classList.remove('editing');
                    });
                    changes = [];
                }
            }

            // Make field editable
            function makeEditable(field) {
                if (!editMode) return;

                const original = field.getAttribute('data-original');
                const fieldName = field.getAttribute('data-field');
                const table = field.getAttribute('data-table');
                const id = field.getAttribute('data-id');

                // Create input based on field type
                let input;
                if (fieldName === 'notes') {
                    input = document.createElement('textarea');
                    input.className = 'edit-input';
                    input.rows = 4;
                    input.value = original;
                } else {
                    input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'edit-input';
                    input.value = original;
                }

                // Replace content with input
                field.innerHTML = '';
                field.appendChild(input);
                field.classList.add('editing');
                input.focus();

                // Save on Enter (except for textareas)
                if (fieldName !== 'notes') {
                    input.addEventListener('keypress', function (e) {
                        if (e.key === 'Enter') {
                            saveField(field, input.value);
                        }
                    });
                }

                // Save on blur
                input.addEventListener('blur', function () {
                    saveField(field, input.value);
                });
            }

            // Save field changes
            function saveField(field, value) {
                const fieldName = field.getAttribute('data-field');
                const table = field.getAttribute('data-table');
                const id = field.getAttribute('data-id');
                const original = field.getAttribute('data-original');

                // For main report fields, set default table if missing
                const effectiveTable = table || 'daily_work_reports';

                // Store change
                changes.push({
                    table: effectiveTable,
                    id: id,
                    field: fieldName,
                    value: value,
                    original: original
                });

                // Update display
                if (fieldName === 'notes') {
                    if (value) {
                        field.innerHTML = value.replace(/\n/g, '<br>');
                        field.classList.remove('empty-field');
                    } else {
                        field.innerHTML = 'No notes provided - click to add';
                        field.classList.add('empty-field');
                    }
                } else if (fieldName === 'weather_conditions' || fieldName === 'site_conditions') {
                    // Format conditions as badges
                    const conditions = value.split(',').filter(c => c.trim() !== '');
                    let html = '';
                    if (conditions.length > 0) {
                        field.classList.remove('empty-field');
                        conditions.forEach(condition => {
                            const badgeClass = fieldName === 'weather_conditions' ? 'bg-info' : 'bg-secondary';
                            html += `<span class="badge ${badgeClass} me-1">${condition.trim()}</span>`;
                        });
                    } else {
                        field.classList.add('empty-field');
                        html = '<span>No conditions recorded</span>';
                    }
                    field.innerHTML = html;
                } else {
                    if (value) {
                        field.textContent = value;
                        field.classList.remove('empty-field');
                    } else {
                        // Handle empty fields with appropriate messages
                        switch (fieldName) {
                            case 'report_date':
                                field.textContent = 'No date specified';
                                break;
                            case 'shift':
                                field.textContent = 'Shift not specified';
                                break;
                            case 'craft_count':
                                field.textContent = 'Craft count not specified';
                                break;
                            case 'subcontractor_name':
                                field.textContent = 'No name provided';
                                break;
                            case 'headcount':
                                field.textContent = '0';
                                break;
                            case 'tool_name':
                                field.textContent = 'No tool name';
                                break;
                            case 'quantity':
                                field.textContent = '0';
                                break;
                            case 'hours_used':
                                field.textContent = '0';
                                break;
                            case 'comments':
                                field.textContent = 'No comments';
                                break;
                            case 'material_name':
                                field.textContent = 'No material name';
                                break;
                            default:
                                field.textContent = 'Not specified';
                        }
                        field.classList.add('empty-field');
                    }
                }

                field.classList.remove('editing');
            }

            // Save all changes to server
            function saveChanges() {
                if (changes.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Changes',
                        text: 'There are no changes to save!',
                    });
                    return;
                }

                const reportId = <?php echo $reportId ?: 0; ?>;
                if (!reportId) return;

                // Prepare data to send
                const formData = new FormData();
                formData.append('report_id', reportId);
                formData.append('changes', JSON.stringify(changes));

                fetch('ajax_helpers/update_report.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Report updated successfully!',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed',
                                text: data.message || 'An unknown error occurred',
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'An error occurred while saving the report',
                        });
                    });
            }

            // Event Listeners
            editBtn.addEventListener('click', toggleEditMode);
            cancelBtn.addEventListener('click', toggleEditMode);
            cancelBottomBtn.addEventListener('click', toggleEditMode);
            saveReportBtn.addEventListener('click', saveChanges);
            saveBottomBtn.addEventListener('click', saveChanges);

            // Make fields editable on click
            document.querySelectorAll('.editable-field').forEach(field => {
                field.addEventListener('click', () => makeEditable(field));
            });
        });

        // Add after the existing event listeners
        const approveBtn = document.getElementById('approve-report-btn');
        if (approveBtn) {
            approveBtn.addEventListener('click', function () {
                const reportId = <?php echo $reportId; ?>; // Get the actual report ID

                Swal.fire({
                    title: 'Approve Report?',
                    text: 'This will send an approval email to the admin',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Yes, Approve'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request
                        fetch('ajax_helpers/approve_report.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `report_id=${reportId}`
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Report Approved!',
                                        text: 'Admin has been notified via email',
                                    });
                                    // Update UI
                                    approveBtn.disabled = true;
                                    approveBtn.innerHTML = '<i class="bi bi-check2-all me-2"></i>Approved';
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Approval Failed',
                                        text: data.message || 'Could not send approval email',
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Network Error',
                                    text: 'Failed to send approval request',
                                });
                            });
                    }
                });
            });
        }
    </script>
</body>

</html>