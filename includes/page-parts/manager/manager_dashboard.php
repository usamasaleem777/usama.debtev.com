<?php
require_once __DIR__ . '/../../../includes/classes/db.class.php';

// Fetch all data
$totalApplicants = DB::queryFirstField("SELECT COUNT(*) FROM applicants");
$topApplicants = DB::query("SELECT CONCAT(first_name, ' ', last_name) AS full_name, position FROM applicants WHERE first_name IS NOT NULL AND last_name IS NOT NULL ORDER BY id DESC LIMIT 2");
$craftsmen = DB::query("SELECT user_name FROM users WHERE role_id = 3 ORDER BY created_at DESC LIMIT 2");
$dailyReports = DB::query("SELECT j.job_title FROM daily_work_reports dwr LEFT JOIN job j ON dwr.job_id = j.id ORDER BY dwr.report_date DESC LIMIT 2");
$applicant_count = DB::queryFirstField("SELECT COUNT(*) FROM applicants");

$stats = [
    'Full-Time' => DB::queryFirstField("SELECT COUNT(*) FROM applicants WHERE employment_type = 'Full-Time'"),
    'Part-Time' => DB::queryFirstField("SELECT COUNT(*) FROM applicants WHERE employment_type = 'Part-Time'"),
    'Eligible' => DB::queryFirstField("SELECT COUNT(*) FROM applicants WHERE legal_us_work_eligibility = 1")
];
?>
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center py-2">
    <div class="breadcrumb-title pe-3 small" style="color: #FE5500; margin-top: 10px; margin-bottom: 5px;">
        <?php echo lang("dashboard"); ?>
    </div>
    <div class="ps-3" style="margin-top: 10px; margin-bottom: 5px;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0 small">
                <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active small" aria-current="page"><?php echo lang("manager"); ?></li>
            </ol>
        </nav>
    </div>
</div>
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-3">
    <!-- Applicant Statistics -->
    <div class="col">
        <div class="card rounded-3 h-20" style="min-height: 180px;">
            <div class="card-body p-3 d-flex flex-column">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h6 class="mb-0 small"><?php echo lang("admin_Applicants"); ?></h6>
                </div>
                <div class="d-flex justify-content-between flex-grow-1">
                    <div class="d-flex flex-column justify-content-between">
                        <h4 class="mb-0 text-indigo"><?php echo $totalApplicants; ?></h4>
                        <a href="index.php?route=modules/applicants/list_applicants" class="btn btn-orange rounded-4 px-3 btn-sm small align-self-start">
                            <i class="fas fa-eye me-1"></i> <?php echo lang("admin_View_Details"); ?>
                        </a>
                    </div>
                    <div class="bg-orange-50 rounded-2 card-icon-sm align-self-center">
                        <span class="material-icons-outlined text-orange">people</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Applicants -->
    <div class="col">
        <div class="card rounded-3 h-20" style="min-height: 180px;">
            <div class="card-body p-3 d-flex flex-column">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h6 class="mb-0 small"><?php echo lang("recent_applicants"); ?></h6>
                </div>
                <div class="flex-grow-1 mb-2">
                    <div class="list-group list-group-flush small">
                        <?php foreach ($topApplicants as $applicant): ?>
                            <div class="list-group-item border-0 px-0 py-1">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user text-orange me-2" style="font-size: 0.8rem;"></i>
                                    <div class="text-truncate">
                                        <?php echo htmlspecialchars($applicant['full_name'] ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="index.php?route=modules/applicants/list_applicants" class="btn btn-orange rounded-4 px-3 btn-sm small align-self-start">
                    <i class="fas fa-eye me-1"></i> <?php echo lang("view_all"); ?>
                </a>
            </div>
        </div>
    </div>

  

    <!-- Craftsmen Overview -->
    <div class="col">
        <div class="card rounded-3 h-20" style="min-height: 180px;">
            <div class="card-body p-3 d-flex flex-column">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h6 class="mb-0 small"><?php echo lang("recent_craftsmen"); ?></h6>
                </div>
                <div class="flex-grow-1 mb-2">
                    <div class="list-group list-group-flush small">
                        <?php foreach ($craftsmen as $craftsman): ?>
                            <div class="list-group-item border-0 px-0 py-1">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-tie text-orange me-2" style="font-size: 0.8rem;"></i>
                                    <div class="text-truncate">
                                        <?php echo htmlspecialchars($craftsman['user_name']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="?route=modules/users/view_users" class="btn btn-orange rounded-4 px-3 btn-sm small align-self-start">
                    <i class="fas fa-eye me-1"></i> <?php echo lang("view_all"); ?>
                </a>
            </div>
        </div>
    </div>
      <!-- Quick Stats -->
      <div class="col">
        <div class="card rounded-3 h-20" style="min-height: 180px;">
            <div class="card-body p-3 d-flex flex-column">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h6 class="mb-0 small"><?php echo lang("hiring_stats"); ?></h6>
                </div>
                <div class="flex-grow-1">
                    <div class="row g-2 small">
                        <div class="col-6">
                            <div class="p-2 bg-indigo-10 rounded-3">
                                <div class="fw-bold"><?php echo $stats['Full-Time']; ?></div>
                                <small class="text-muted">Full-Time</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-orange-10 rounded-3">
                                <div class="fw-bold"><?php echo $stats['Part-Time']; ?></div>
                                <small class="text-muted">Part-Time</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-indigo-10 rounded-3">
                                <div class="fw-bold"><?php echo $stats['Eligible']; ?></div>
                                <small class="text-muted">Eligible</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-orange-50 rounded-2 card-icon-sm align-self-end mt-auto">
                    <span class="material-icons-outlined text-orange">analytics</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-3">
  
</div>
<!-- Daily Reort Work -->
<div class="col-12 col-lg-8">
    <div class="card rounded-3 h-100">
        <div class="card-header border-0 p-3">
            <div class="d-flex align-items-start justify-content-between">
                <h6 class="mb-0 small">Daily Work Reports</h6>
                <div class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                        data-bs-toggle="dropdown">
                        <span class="material-icons-outlined">more_vert</span>
                    </a>
                    <ul class="dropdown-menu small">
                        <li><a class="dropdown-item small" href="index.php?route=modules/daily_forms/view_daily_form">View All Reports</a></li>
                        <li><a class="dropdown-item small" href="index.php?route=modules/daily_forms/create_daily_form">Create New Report</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="user-list p-2">
                <div class="table-responsive">
                    <div class="table-container" style="overflow: auto; max-height: 500px;">
                        <table class="table align-middle mb-0 small">
                            <thead class="table-light small">
                                <tr>
                                    <th class="small" style="background-color: #FE5500; color: white;">Job</th>
                                    <th class="small" style="background-color: #FE5500; color: white;">Crew</th>
                                    <th class="small" style="background-color: #FE5500; color: white;">Date</th>
                                    <th class="small" style="background-color: #FE5500; color: white;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get most recent daily reports
                                $dailyReports = DB::query("
                                    SELECT dwr.id, j.job_title, c.crew_name, dwr.report_date, dwr.is_locked 
                                    FROM daily_work_reports dwr 
                                    LEFT JOIN job j ON dwr.job_id = j.id 
                                    LEFT JOIN crew c ON dwr.crew_id = c.crew_id 
                                    ORDER BY dwr.report_date DESC 
                                    LIMIT 7
                                ");

                                foreach ($dailyReports as $report): ?>
                                    <tr class="clickable-row" onclick="window.location='index.php?route=modules/daily_forms/view_daily_form&report_id=<?= $report['id'] ?>'">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div>
                                                    <div class="small fw-500">
                                                        <?php echo htmlspecialchars($report['job_title'] ?? 'N/A'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="small">
                                            <?php echo htmlspecialchars($report['crew_name'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="small">
                                            <?php echo date('M d, Y', strtotime($report['report_date'])); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = $report['is_locked'] ? 'badge-approved' : 'badge-pending';
                                            $statusText = $report['is_locked'] ? 'Completed' : 'In Progress';
                                            ?>
                                            <span class="badge <?= $statusClass ?>">
                                                <?= $statusText ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-transparent p-2">
            <div class="d-flex align-items-center justify-content-between small">
                <span class="text-muted">Showing <?= count($dailyReports) ?> reports</span>
                <a href="index.php?route=modules/daily_forms/view_daily_form" class="btn btn-orange btn-xs py-1 px-2">
                    <i class="fas fa-plus me-1"></i> View All Reports
                </a>
            </div>
        </div>
    </div>
</div>
<style>
    #basic-datatable_filter input {
        border-radius: 20px !important;
        border: 1px solid #FE5505 !important;
        padding: 5px 15px !important;
        margin-bottom: 15px;
    }

    #basic-datatable th {
        background: #FE5505 !important;
        color: white !important;
    }

    #basic-datatable td {
        vertical-align: middle;
    }

    .page-item.active .page-link {
        background: #FE5505 !important;
        border-color: #FE5505 !important;
    }

    /* Compact Dashboard Styles */
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
        margin-top: 10px;
    }

    .text-indigo {
        color: #4b49ac;
    }

    .text-orange {
        color: #FE5500;
    }

    .bg-orange-50 {
        background-color: rgba(254, 85, 0, 0.1);
    }

    .btn-orange {
        background: linear-gradient(45deg, #FE5505, #FF8E53);
        color: white;
        border: none;
        font-size: 0.8875rem;
    }

    .btn-outline-orange {
        border-color: #FE5500;
        color: #FE5500;
        font-size: 0.6875rem;
    }

    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.625rem;
        line-height: 1.2;
    }

    .small {
        font-size: 0.8125rem;
    }

    .x-small {
        font-size: 0.75rem;
    }

    .xx-small {
        font-size: 0.625rem;
    }

    .user-list,
    .activity-list {
        max-height: 300px;
        overflow-y: auto;
    }

    h6 {
        color: #FE5500;
    }

    .rounded-3 {
        border-radius: 12px !important;
    }

    .rounded-4 {
        border-radius: 8px !important;
    }

    .badge {
        padding: 0.25em 0.5em;
        font-size: 0.625rem;
        font-weight: 500;
    }

    /* Icon Sizing */
    .card-icon-sm {
        font-size: 1.25rem !important;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-icon-xs {
        font-size: 1rem !important;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Table adjustments */
    .table th,
    .table td {
        padding: 0.5rem 0.75rem;
    }

    /* Activity item spacing */
    .activity-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    /* Custom scrollbar styling */
    .table-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #FE5500;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #d94600;
    }

    /* Clickable row styling */
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .clickable-row:hover {
        background-color: rgba(254, 85, 0, 0.1);
    }

    /* Status badge colors */
    .badge-pending {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-approved {
        background-color: #28a745;
        color: white;
    }

    .badge-rejected {
        background-color: #dc3545;
        color: white;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 767.98px) {
        .page-breadcrumb {
            padding-top: 10px;
            padding-bottom: 5px;
        }

        .breadcrumb-title,
        .breadcrumb-item {
            font-size: 0.75rem;
        }

        .card-body {
            padding: 0.75rem;
        }

        .col1 {
            margin-top: -20px !important;
        }

        .card-icon-sm,
        .card-icon-xs {
            width: 30px;
            height: 30px;
            font-size: 1rem !important;
        }

        h4 {
            font-size: 1.25rem;
        }

        .btn-orange,
        .btn-outline-orange {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .table th,
        .table td {
            padding: 0.3rem 0.5rem;
            font-size: 0.75rem;
        }

        .user-list,
        .activity-list {
            max-height: 250px;
        }

        .row-cols-md-2>* {
            flex: 0 0 auto;
            width: 50%;
        }

        .row-cols-xl-4>* {
            flex: 0 0 auto;
            width: 50%;
        }
    }

    @media (max-width: 360px) {

        .row-cols-md-2>*,
        .row-cols-xl-4>* {
            flex: 0 0 auto;
            width: 100%;
        }

        .breadcrumb-title,
        .breadcrumb-item {
            margin-top: 20px;
        }

        .col1 {
            margin-top: -20px !important;
        }

        .card-body {
            padding: 0.5rem;
        }

        h4 {
            font-size: 1.1rem;
        }

        .btn-orange {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }

        .table th,
        .table td {
            font-size: 0.7rem;
        }

        .small {
            font-size: 0.75rem;
        }

        .x-small {
            font-size: 0.7rem;
        }

        .xx-small {
            font-size: 0.6rem;
        }
    }

    @media (max-width: 430px) {

        .row-cols-md-2>*,
        .row-cols-xl-4>* {
            flex: 0 0 auto;
            width: 100%;
        }

        .breadcrumb-title,
        .breadcrumb-item {
            margin-top: 20px;
        }

        .col1 {
            margin-top: -20px !important;
        }

        .card-body {
            padding: 0.5rem;
        }

        h4 {
            font-size: 1.1rem;
        }

        .btn-orange {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }

        .table th,
        .table td {
            font-size: 0.7rem;
        }

        .small {
            font-size: 0.75rem;
        }

        .x-small {
            font-size: 0.7rem;
        }

        .xx-small {
            font-size: 0.6rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
       
    });
</script>