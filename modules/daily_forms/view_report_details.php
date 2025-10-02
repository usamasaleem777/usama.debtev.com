<?php

// Fetch report ID from URL
$report_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$report_id) {
    die("<div class='alert alert-danger'>Invalid report ID</div>");
}

try {
    // Fetch main report data with job title
    $report = DB::queryFirstRow(
        "SELECT dwr.*, j.job_title AS job_name 
         FROM daily_work_reports dwr
         LEFT JOIN job j ON dwr.job_id = j.id
         WHERE dwr.id = %i",
        $report_id
    );

    if (!$report) {
        die("<div class='alert alert-danger'>Report not found</div>");
    }


    // Fetch related data with basic error handling
    $subcontractors = [];
    $equipment = [];
    $materials = [];
    $crew = null;
    $foreman = null;

    try {
        $subcontractors = DB::query(
            "SELECT * FROM subcontractors WHERE report_id = %i",
            $report_id
        );
    } catch (Exception $e) {
        // Silently fail - empty array will be handled in display
    }

    try {
        // First try with tool name
        $equipment = DB::query(
            "SELECT eu.*, t.name AS tool_name 
             FROM equipment_usage eu
             LEFT JOIN tools t ON eu.tool_id = t.id
             WHERE report_id = %i",
            $report_id
        );
    } catch (Exception $e) {
        try {
            // Fallback to basic query if name column doesn't exist
            $equipment = DB::query(
                "SELECT * FROM equipment_usage 
                 WHERE report_id = %i",
                $report_id
            );
            // Add tool_name field for consistency
            foreach ($equipment as &$item) {
                $item['tool_name'] = 'Equipment #' . $item['tool_id'];
            }
        } catch (Exception $e) {
            // Silently fail - empty array will be handled in display
        }
    }

    try {
        $materials = DB::query(
            "SELECT * FROM material_usage WHERE report_id = %i",
            $report_id
        );
    } catch (Exception $e) {
        // Silently fail - empty array will be handled in display
    }

    // Fetch reference data with basic error handling
    if (!empty($report['crew_id'])) {
        try {
            $crew = DB::queryFirstRow("SELECT * FROM crew WHERE id = %i", $report['crew_id']);
        } catch (Exception $e) {
            // Silently fail - null will be handled in display
        }
    }

    if (!empty($report['foreman_id'])) {
        try {
            $foreman = DB::queryFirstRow("SELECT * FROM users WHERE id = %i", $report['foreman_id']);
        } catch (Exception $e) {
            // Silently fail - null will be handled in display
        }
    }

} catch (Exception $e) {
    die("<div class='alert alert-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Helper function for safe display
function displayValue($value, $default = 'N/A')
{
    return !empty($value) ? htmlspecialchars($value) : $default;
}



$site_conditions = !empty($report['site_conditions'])
    ? explode(',', $report['site_conditions'])
    : [];

// Format dates
$report_date = !empty($report['report_date'])
    ? date('F j, Y', strtotime($report['report_date']))
    : 'N/A';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Work Report Viewer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #fd7e14;
            --secondary-color: #f8f9fa;
            --border-color: #e9ecef;
            --text-color: #495057;
            --light-bg: #f8f9fa;
        }

        body {
            background-color: #f5f6f8;
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .report-container {
            max-width: 1200px;
            margin: 2rem auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .report-header {
            background: linear-gradient(135deg, #fd7e14 0%, #e66400 100%);
            color: white;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .report-title {
            font-weight: 700;
            margin: 0;
            font-size: 1.8rem;
        }

        .report-subtitle {
            opacity: 0.9;
            margin: 0.5rem 0 0;
            font-weight: 400;
        }

        .report-status {
            display: inline-block;
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .status-active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .status-locked {
            background-color: rgba(40, 167, 69, 0.2);
        }

        .nav-tabs {
            background: white;
            padding: 0 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .nav-tabs .nav-link {
            color: var(--text-color);
            font-weight: 500;
            padding: 1rem 1.5rem;
            border: none;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary-color);
            background-color: var(--light-bg);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background-color: transparent;
            border-bottom: 3px solid var(--primary-color);
        }

        .tab-content {
            padding: 2rem;
        }

        .info-card {
            background: white;
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            transition: transform 0.2s;
        }

        .info-card:hover {
            transform: translateY(-3px);
        }

        .card-header {
            background-color: rgba(253, 126, 20, 0.05);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }

        .card-header i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .detail-row {
            display: flex;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            flex: 0 0 30%;
            font-weight: 500;
            color: #6c757d;
        }

        .detail-value {
            flex: 0 0 70%;
            font-weight: 500;
        }

        .weather-display {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .weather-stats {
            padding: 1rem;
            background-color: rgba(253, 126, 20, 0.03);
            border-radius: 8px;
            border-left: 3px solid var(--primary-color);
        }

        .badge-tag {
            display: inline-block;
            background-color: rgba(253, 126, 20, 0.1);
            color: #e67300;
            padding: 0.35rem 0.8rem;
            border-radius: 20px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }

        .table-details {
            width: 100%;
            border-collapse: collapse;
        }

        .table-details th,
        .table-details td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table-details th {
            background-color: rgba(253, 126, 20, 0.05);
            color: var(--primary-color);
            font-weight: 600;
        }

        .table-details tr:last-child td {
            border-bottom: none;
        }

        .notes-content {
            background-color: var(--light-bg);
            border-radius: 8px;
            padding: 1.5rem;
            white-space: pre-wrap;
            line-height: 1.6;
        }

        .footer-actions {
            display: flex;
            justify-content: space-between;
            padding: 1.5rem;
            border-top: 1px solid var(--border-color);
            background: white;
        }

        .btn-print {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
        }

        .btn-print:hover {
            background-color: #e67300;
            border-color: #e67300;
        }

        .back-link {
            color: var(--primary-color);
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .back-link i {
            margin-right: 0.5rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .empty-data {
            color: #6c757d;
            font-style: italic;
            padding: 1rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .detail-row {
                flex-direction: column;
            }

            .detail-label,
            .detail-value {
                flex: 0 0 100%;
                width: 100%;
            }

            .detail-label {
                margin-bottom: 0.5rem;
            }

            .nav-tabs .nav-link {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="report-container">
        <!-- Report Header -->
        <div class="report-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="report-title">Daily Work Report</h1>
                    <p class="report-subtitle">Report ID: <?= displayValue($report['id']) ?></p>
                    <span class="report-status <?= $report['is_locked'] ? 'status-locked' : 'status-active' ?>">
                        <i class="bi <?= $report['is_locked'] ? 'bi-lock-fill' : 'bi-unlock' ?>"></i>
                        <?= $report['is_locked'] ? 'Locked Report' : 'Active Report' ?>
                    </span>
                </div>
                <div class="text-end">
                    <p class="mb-0"><strong>Date:</strong> <?= $report_date ?></p>
                    <p class="mb-0"><strong>Shift:</strong> <?= displayValue($report['shift']) ?></p>
                    <p class="mb-0"><strong>Foreman:</strong>
                        <?= displayValue($foreman['name'] ?? $report['foreman_id']) ?></p>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic"
                    type="button" role="tab">
                    <i class="bi bi-info-circle"></i> Basic Info
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="conditions-tab" data-bs-toggle="tab" data-bs-target="#conditions"
                    type="button" role="tab">
                    <i class="bi bi-cloud-sun"></i> Conditions
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="workforce-tab" data-bs-toggle="tab" data-bs-target="#workforce"
                    type="button" role="tab">
                    <i class="bi bi-people"></i> Workforce
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="equipment-tab" data-bs-toggle="tab" data-bs-target="#equipment"
                    type="button" role="tab">
                    <i class="bi bi-tools"></i> Equipment
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials"
                    type="button" role="tab">
                    <i class="bi bi-box-seam"></i> Materials
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button"
                    role="tab">
                    <i class="bi bi-journal-text"></i> Notes & Status
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="reportTabsContent">
            <!-- Basic Info Tab -->
            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                <div class="info-card">
                    <div class="card-header">
                        <i class="bi bi-building"></i>
                        Project Details
                    </div>
                    <div class="card-body">
                        <div class="detail-row">
                            <div class="detail-label">Job ID</div>
                            <div class="detail-value"><?= displayValue($report['job_id']) ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Job Name</div>
                            <div class="detail-value"><?= displayValue($report['job_name'] ?? 'N/A') ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Report Date</div>
                            <div class="detail-value"><?= $report_date ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Shift</div>
                            <div class="detail-value"><?= displayValue($report['shift']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="card-header">
                        <i class="bi bi-person-badge"></i>
                        Personnel Information
                    </div>
                    <div class="card-body">
                        <div class="detail-row">
                            <div class="detail-label">Crew ID</div>
                            <div class="detail-value"><?= displayValue($report['crew_id']) ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Crew Name</div>
                            <div class="detail-value"><?= displayValue($crew['name'] ?? 'N/A') ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Foreman</div>
                            <div class="detail-value"><?= displayValue($foreman['name'] ?? $report['foreman_id']) ?>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Superintendent</div>
                            <div class="detail-value"><?= displayValue($report['superintendent_name']) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conditions Tab -->
            <div class="tab-pane fade" id="conditions" role="tabpanel">
                <div class="info-card">
                    <div class="card-header">
                        <i class="bi bi-cloud-sun"></i>
                        Weather Conditions
                    </div>
                    <div class="card-body">
                        <?php
                        $weather_data = !empty($report['weather_conditions'])
                            ? json_decode($report['weather_conditions'], true)
                            : null;

                        if ($weather_data): ?>
                            <div class="weather-display">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?= htmlspecialchars($weather_data['icon']) ?>" alt="Weather Icon"
                                        style="width: 50px; height: 50px; margin-right: 15px;">
                                    <div>
                                        <h5 style="margin: 0; color: var(--primary-color);">
                                            <?= htmlspecialchars($weather_data['condition']) ?>
                                        </h5>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($weather_data['location']) ?>
                                        </small>
                                    </div>
                                </div>

                                <div class="weather-stats w-75">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Temperature:</span>
                                        <strong><?= htmlspecialchars($weather_data['temperature']) ?>°C</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Humidity:</span>
                                        <strong><?= htmlspecialchars($weather_data['humidity']) ?>%</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Wind Speed:</span>
                                        <strong><?= htmlspecialchars($weather_data['wind_speed']) ?> kph</strong>
                                    </div>
                                </div>

                                <?php if (!empty($weather_data['manual_conditions'])): ?>
                                    <div class="mt-3 pt-3 border-top">
                                        <h6>Additional Conditions:</h6>
                                        <?php foreach ($weather_data['manual_conditions'] as $condition): ?>
                                            <span class="badge-tag"><?= htmlspecialchars($condition) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-data">No weather data recorded</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="info-card">
                    <div class="card-header">
                        <i class="bi bi-geo-alt"></i>
                        Site Conditions
                    </div>
                    <div class="card-body">
                        <?php if (!empty($site_conditions)): ?>
                            <div class="mb-3">
                                <?php foreach ($site_conditions as $condition): ?>
                                    <span class="badge-tag"><?= displayValue($condition) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-data">No site conditions recorded</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Workforce Tab -->
            <div class="tab-pane fade" id="workforce" role="tabpanel">
                <div class="info-card">
                    <div class="card-header">
                        <i class="bi bi-people"></i>
                        Crew Composition
                    </div>
                    <div class="card-body">
                        <div class="detail-row">
                            <div class="detail-label">Craft Workers</div>
                            <div class="detail-value"><?= displayValue($report['craft_count'] ?? '0') ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Total Headcount</div>
                            <div class="detail-value"><?= displayValue($report['total_head_count'] ?? '0') ?></div>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="card-header">
                        <i class="bi bi-building"></i>
                        Subcontractors
                    </div>
                    <div class="card-body">
                        <?php if (!empty($subcontractors)): ?>
                            <table class="table-details">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Headcount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subcontractors as $sub): ?>
                                        <tr>
                                            <td><?= displayValue($sub['subcontractor_name']) ?></td>
                                            <td><?= displayValue($sub['headcount']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-data">No subcontractors recorded</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Equipment Tab -->
            <div class="tab-pane fade" id="equipment" role="tabpanel">
                <div class="info-card">
                    <div class="card-header">
                        <i class="bi bi-tools"></i>
                        Equipment Usage
                    </div>
                    <div class="card-body">
                        <?php if (!empty($equipment)): ?>
                            <table class="table-details">
                                <thead>
                                    <tr>
                                        <th>Equipment</th>
                                        <th>ID</th>
                                        <th>Qty</th>
                                        <th>Hours Used</th>
                                        <th>Comments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($equipment as $eq): ?>
                                        <tr>
                                            <td><?= displayValue($eq['tool_name'] ?? 'Equipment #' . ($eq['tool_id'] ?? 'N/A')) ?>
                                            </td>
                                            <td><?= displayValue($eq['tool_id']) ?></td>
                                            <td><?= displayValue($eq['quantity']) ?></td>
                                            <td><?= displayValue($eq['hours_used']) ?></td>
                                            <td><?= displayValue($eq['comments']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-data">No equipment usage recorded</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Materials Tab -->
            <div class="tab-pane fade" id="materials" role="tabpanel">
                <div class="info-card">
                    <div class="card-header">
                        <i class="bi bi-box-seam"></i>
                        Materials Used
                    </div>
                    <div class="card-body">
                        <?php if (!empty($materials)): ?>
                            <table class="table-details">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Qty</th>
                                        <th>Comments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($materials as $mat): ?>
                                        <tr>
                                            <td><?= displayValue($mat['material_name']) ?></td>
                                            <td><?= displayValue($mat['quantity']) ?></td>
                                            <td><?= displayValue($mat['comments']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-data">No materials recorded</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Notes & Status Tab -->
            <div class="tab-pane fade" id="notes" role="tabpanel">
                <div class="info-card">
                    <div class="card-header">
                        <i class="bi bi-journal-text"></i>
                        Daily Notes
                    </div>
                    <div class="card-body">
                        <?php if (!empty($report['notes'])): ?>
                            <div class="notes-content">
                                <?= nl2br(htmlspecialchars($report['notes'])) ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-data">No notes recorded</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="info-card">
                    <div class="card-header">
                        <i class="bi bi-lock"></i>
                        Report Status
                    </div>
                    <div class="card-body">
                        <div class="detail-row">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <span class="badge <?= $report['is_locked'] ? 'bg-success' : 'bg-warning' ?>">
                                    <?= $report['is_locked'] ? 'Locked' : 'Unlocked' ?>
                                </span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Locked By</div>
                            <div class="detail-value">
                                <?= $report['is_locked']
                                    ? displayValue($report['superintendent_name'])
                                    : 'N/A' ?>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Date Created</div>
                            <div class="detail-value">
                                <?= displayValue($report['created_at']
                                    ? date('F j, Y H:i', strtotime($report['created_at']))
                                    : 'N/A') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="footer-actions">
            <a href="index.php?route=modules/daily_forms/view_work_form" class="back-link">
                <i class="bi bi-arrow-left"></i> Back to Reports List
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tab persistence on page reload
        document.addEventListener('DOMContentLoaded', function () {
            const triggerTabList = [].slice.call(document.querySelectorAll('#reportTabs button'));
            triggerTabList.forEach(function (triggerEl) {
                const tabTrigger = new bootstrap.Tab(triggerEl);

                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault();
                    tabTrigger.show();

                    // Store active tab in session storage
                    sessionStorage.setItem('activeTab', triggerEl.getAttribute('data-bs-target'));
                });
            });

            // Check for active tab in session storage
            const activeTab = sessionStorage.getItem('activeTab');
            if (activeTab) {
                const triggerEl = document.querySelector(`[data-bs-target="${activeTab}"]`);
                if (triggerEl) {
                    bootstrap.Tab.getInstance(triggerEl).show();
                }
            }
        });
    </script>
</body>

</html>