<?php

/************** START SECURITY CHECK ***********************/
// Define allowed roles - make sure these variables are defined somewhere in your application
$allowedRoles = array(
    $admin_role,
    $manager_role,
    $hr
); // You need to define these variables

// Check if role is allowed
if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], $allowedRoles)) {
    // User does not have access, redirect to home
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => "You do not have permission to view this page."
    ];
    echo '<script>window.location.href = "index.php";</script>';
    die();
}
/**************** END SECURITY CHECK ***********************/


$users = DB::query("
    SELECT 
        a.*, 
        p.position_name
    FROM applicants a
    LEFT JOIN positions p ON a.position = p.id
        ORDER BY a.id DESC 

    ");
$whatsappTemplates = DB::Query("SELECT id, short_name, message_text FROM templates WHERE message_type = 'Whatsapp'");
?>

<!-- Font Awesome for WhatsApp icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

<!-- libphonenumber-js for phone number formatting -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/libphonenumber-js/1.10.13/libphonenumber-js.min.js"></script>

<style>
    .dropdown-menu {
        display: none;
        position: absolute;
        z-index: 9999;
        background: #fff;
        border: 1px solid #ccc;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        border-radius: 4px;
        padding: 0;
        min-width: 180px;
        font-family: sans-serif;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-menu li {
        list-style: none;
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        font-size: 14px;
        transition: all 0.2s ease-in-out;
        background-color: #fff !important;
        /* fallback */
        color: #333 !important;
    }

    /* Green option */
    .dropdown-menu li.green {
        background-color: #e8f5e9 !important;
        color: #2e7d32 !important;
    }

    .dropdown-menu li.green:hover {
        background-color: #2e7d32 !important;
        color: #fff !important;
    }
/* Position column width control */
/* Position column - show full text but truncate with ellipsis if too long */
#applicants td:nth-child(8),
#applicants th:nth-child(8) {
    max-width: 150px; /* Adjust width as needed */
    white-space: nowrap; /* Prevent wrapping */
    overflow: hidden; /* Hide overflow */
    text-overflow: ellipsis; /* Show ... if text overflows */
    display: inline-block; /* Required for ellipsis */
}
    .dropdown-menu li.blue {
        background-color: #e3f2fd !important;
        color: #1565c0 !important;
    }

    .dropdown-menu li.blue:hover {
        background-color: #1565c0 !important;
        color: #fff !important;
    }

    /* Orange option */
    .dropdown-menu li.orange {
        background-color: #fff3e0 !important;
        color: #ef6c00 !important;
    }

    .dropdown-menu li.orange:hover {
        background-color: #ef6c00 !important;
        color: #fff !important;
    }

    .dropdown-menu li:last-child {
        border-bottom: none;
    }

    /* DataTable Header and Pagination Styling */
    #applicants thead th {
        background-color: #fe5500;
        color: white;
        border-bottom: 2px solid #fe5500;
        font-size: 12px;
        position: sticky;
        top: 0;
    }

    /* Limit width of referred by column */
    #applicants td:nth-child(12),
    #applicants th:nth-child(12) {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .page-title {
        font-size: 2rem;
    }

    .pagination .page-item.active .page-link {
        background-color: #fe5500 !important;
        border-color: #fe5500 !important;
    }

    .page-header>div {
        order: 1;
    }

    .page-header>h2 {
        order: 2;
        margin-top: 5px;
        text-align: right;
        width: 100%;
    }

    .pagination .page-link {
        color: black !important;
        padding: 0.25rem 0.5rem;
        font-size: 12px;
    }

    /* WhatsApp button styling */
    .whatsapp-btn {
        background-color: #25D366;
        color: white;
        border-color: #25D366;
        margin-left: 5px;
    }

    .whatsapp-btn:hover {
        background-color: #128C7E;
        border-color: #128C7E;
    }


    .phone-number {
        white-space: nowrap;
    }

    .country-flag {
        width: 16px;
        height: 12px;
        margin-right: 5px;
        vertical-align: middle;
        display: inline-block;
        background-size: cover;
    }

    /* View button styling */
    .view-btn {
        background-color: #fe5500;
        color: white;
        border-color: #fe5500;
        padding: 0.25rem 0.5rem;
        font-size: 12px;
    }

    .view-btn:hover {
        background-color: #d94600;
        border-color: #d94600;
    }

    .send-packet-btn i {
        display: none;
    }

    .action-buttons {
        display: flex;
        gap: 2px;
        flex-wrap: nowrap;
        justify-content: center;
        align-items: center;
    }

    .action-buttons .btn {
        min-width: 30px;
        height: 30px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        white-space: nowrap;
    }

    .action-buttons .btn i {
        margin: 0;
    }

    /* Specific button styles */
    .view-btn,
    .whatsapp-btn,
    .btn-info {
        min-width: 30px;
        height: 30px;
        padding: 0 5px;
    }

    /* Table container for scrolling */
    .table-responsive-container {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        position: relative;
    }

    /* Badge styling of Status  */
    .badge {
        display: inline-block;
        padding: 0.25em 0.6em;
        font-size: 0.9em;
        font-weight: 600;
        color: #fff;
        border-radius: 0.25rem;
        background-color: #6c757d;
        /* fallback background */
        border: none;
    }

    .badge-success {
        background-color: #28a745 !important;
        /* green */
    }

    .badge-danger {
        background-color: #dc3545 !important;
        /* red */
    }

    /* Action buttons column */
    #applicants th:nth-child(2),
    #applicants td:nth-child(2) {
        width: 5% !important;
        min-width: 60px;
        padding: 2px !important;
    }

    /* Hide length menu on mobile */
    @media (max-width: 767px) {
        .dataTables_length {
            display: none !important;
        }
    }

    /* Ensure all columns are visible on desktop */
    @media (min-width: 768px) {

        #applicants td,
        #applicants th {
            display: table-cell !important;
        }

        /* Header right alignment for desktop */
        .page-header {
            justify-content: flex-end !important;
        }
    }

    /* Header adjustments for desktop */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Responsive styles */
    @media (max-width: 767px) {

        /* Header adjustments - breadcrumb first, then title */
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        /* Remove padding and margins from card body and row */
        .card-body {
            padding: 10px 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .row {
            --bs-gutter-x: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        /* Ensure content touches the edges */
        .table-responsive-container {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* Adjust card padding */
        .card-body {
            padding: 10px 0 !important;
        }

        /* Make filter table full width */
        .card-body>table {
            width: 100% !important;
            margin: 0;
            padding: 0;
        }

        send-packet-btn i {
            display: inline-block;
            margin-right: 5px;
        }

        .send-packet-btn span {
            display: none;
        }

        /* Stack filter rows on mobile */
        .card-body>table tr {
            display: flex;
            flex-wrap: wrap;
            margin: 0;
            padding: 0;
        }

        /* Make filter cells full width on mobile */
        .card-body>table tr td {
            width: 100% !important;
            padding: 2px 0 !important;
            margin-bottom: 5px;
        }

        /* Adjust filter select elements */
        .card-body>table tr td select {
            width: 100% !important;
            font-size: 12px;
            padding: 2px;
            height: 28px;
        }

        /* Adjust search button */
        #Search {
            width: 100% !important;
            margin-top: 5px;
            font-size: 12px;
            padding: 2px;
        }

        /* Reset button adjustments */
        #resetbtn {
            margin-top: 5px;
            font-size: 12px;
            padding: 2px;
        }

        /* Table adjustments */
        #applicants {
            width: 100% !important;
            margin: 0;
            padding: 0;
        }

        /* Table cell padding */
        #applicants td,
        #applicants th {
            padding: 2px !important;
        }

        /* Show more columns on mobile - modified to show more columns */
        #applicants td:nth-child(1),
        /* ID */
        #applicants td:nth-child(2),
        /* Actions */
        #applicants td:nth-child(3),
        /* Name */
        #applicants td:nth-child(4),
        /* Position */
        #applicants td:nth-child(10),
        /* Status */
        #applicants th:nth-child(1),
        #applicants th:nth-child(2),
        #applicants th:nth-child(3),
        #applicants th:nth-child(4),
        #applicants th:nth-child(10) {
            display: table-cell !important;
        }

        /* Hide less important columns on mobile */
        #applicants td:nth-child(5),
        /* City */
        #applicants td:nth-child(6),
        /* State */
        #applicants td:nth-child(7),
        /* Legal to work */
        /* #applicants td:nth-child(8), */
        /* jobs */
        #applicants td:nth-child(8),
        /* Over 18 */
        #applicants td:nth-child(9),
        /* Available date */
        #applicants th:nth-child(5),
        #applicants th:nth-child(6),
        #applicants th:nth-child(7),
        #applicants th:nth-child(8),
        #applicants th:nth-child(9) {
            display: none !important;
        }

        /* Adjust action buttons */
        .action-buttons {
            justify-content: center;
        }

        .action-buttons .btn {
            margin: 2px;
            min-width: 25px;
            height: 25px;
            font-size: 10px;
        }

        ul.dropdown-menu {
            border-radius: 0 !important;
            padding: 0.25rem 0;
            /* optional: reduce vertical space */
            border: 1px solid #dee2e6;
            /* optional: subtle border */
        }
    }

    @media (max-width: 576px) {

        /* Further adjustments for very small screens */
        .page-title {
            font-size: 1rem;
            margin: 5px 0;
        }

        /* Make table font smaller */
        #applicants {
            font-size: 0.7rem;
        }

        /* Adjust breadcrumb */
        .breadcrumb {
            font-size: 10px;
            padding: 0.25rem 0;
            margin: 0;
        }

        /* Adjust filter labels */
        .form-label {
            font-size: 10px;
            margin-bottom: 0.1rem;
        }

        /* Adjust card titles */
        .card-title {
            font-size: 12px;
            margin: 5px 0;
        }
    }

    /* Extra small devices (360px and below) */
    @media (max-width: 360px) {

        /* Remove ONLY the outer card padding (container-level) */
        .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .row1 {
            margin-left: -20px !important;
            margin-right: -20px !important;
        }

        /* Keep all inner card padding intact */
        .card-body {
            padding: 20px !important;
            margin: 0 !important;
        }

        /* Table adjustments */
        .table-responsive-container {
            width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        /* Slightly reduce table cell padding */
        #applicants td,
        #applicants th {
            padding: 2px !important;
        }

        .page-header .breadcrumb {
            margin-right: -150px !important;
        }

        /* Remove card body padding and margins */
        .card-body {
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Remove row margins */
        .row {
            --bs-gutter-x: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        /* Make table touch screen edges */
        .table-responsive-container {
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
            padding: 0 !important;
        }

        /* Adjust filter table */
        .card-body>table {
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
        }

        /* Force filters to full width */
        .card-body>table tr td {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }

        .page-title {
            font-size: 1.4rem;
            margin-bottom: 5px;
            margin-left: -20px !important;
        }

        .nav-title1 {
            margin-right: -10px !important;
        }

        .navbar {
            margin-right: 5px !important;
        }

        .page-footer {
            margin-right: 5px;
        }

        #applicants {
            font-size: 0.6rem;
        }

        #applicants thead th,
        #applicants tbody td {
            padding: 1px !important;
            white-space: nowrap;
        }

        .breadcrumb {
            font-size: 9px;
            padding: 0.2rem 0;
        }

        .card-title {
            font-size: 11px;
            margin-bottom: 3px;
        }

        .form-label {
            font-size: 9px;
            margin-bottom: 0.1rem;
        }

        select.form-control,
        button.btn {
            font-size: 10px;
            padding: 1px 3px;
            height: 24px;
            margin: 0;
        }

        .action-buttons {
            min-width: 80px;
        }

        .action-buttons .btn {
            min-width: 25px;
            height: 25px;
            font-size: 10px;
        }

        .action-buttons .btn i {
            font-size: 10px;
        }

        /* Ensure text doesn't wrap */
        .action-buttons .btn span {
            display: none;
        }

        .action-buttons .btn i {
            margin: 0;
        }
    }

    @media (max-width: 430px) {

        /* Remove ONLY the outer card padding (container-level) */
        .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .row1 {
            margin-left: -20px !important;
            margin-right: -20px !important;
        }

        /* Keep all inner card padding intact */
        .card-body {
            padding: 20px !important;
            margin: 0 !important;
        }

        /* Table adjustments */
        .table-responsive-container {
            width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        /* Slightly reduce table cell padding */
        #applicants td,
        #applicants th {
            padding: 2px !important;
        }

        .page-header .breadcrumb {
            margin-right: -150px !important;
        }

        /* Remove card body padding and margins */
        .card-body {
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Remove row margins */
        .row {
            --bs-gutter-x: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        /* Make table touch screen edges */
        .table-responsive-container {
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
            padding: 0 !important;
        }

        /* Adjust filter table */
        .card-body>table {
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
        }

        /* Force filters to full width */
        .card-body>table tr td {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }

        .page-title {
            font-size: 1.4rem;
            margin-bottom: 5px;
            margin-left: -20px !important;
        }

        .nav-title1 {
            margin-right: -10px !important;
        }

        .navbar {
            margin-right: 5px !important;
        }

        .page-footer {
            margin-right: 5px;
        }

        #applicants {
            font-size: 0.6rem;
        }

        #applicants thead th,
        #applicants tbody td {
            padding: 1px !important;
            white-space: nowrap;
        }

        .breadcrumb {
            font-size: 9px;
            padding: 0.2rem 0;
        }

        .card-title {
            font-size: 11px;
            margin-bottom: 3px;
        }

        .form-label {
            font-size: 9px;
            margin-bottom: 0.1rem;
        }

        select.form-control,
        button.btn {
            font-size: 10px;
            padding: 1px 3px;
            height: 24px;
            margin: 0;
        }

        .action-buttons {
            gap: 1px;
        }

        .action-buttons .btn {
            min-width: 22px;
            height: 22px;
            font-size: 9px;
        }

        .action-buttons .btn i {
            font-size: 9px;
        }
    }
</style>

<style>
    /* Mail DropDown */
    .custom-dropdown {
        position: relative;
        display: inline-block;
    }

    /* .custom-dropdown-toggle {
                                                          padding: 10px 20px;
                                                          background-color: #3498db;
                                                          color: white;
                                                          cursor: pointer;
                                                          border: none;
                                                          border-radius: 5px;
                                                        } */

    .custom-dropdown-menu {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 240px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 10;
        border-radius: 5px;
        margin-top: 5px;
        padding: 10px;
    }

    .custom-dropdown-option {
        display: block;
        padding: 5px 0;
        cursor: pointer;
    }

    .custom-dropdown-option input[type="checkbox"] {
        margin-right: 10px;
    }

    .custom-dropdown-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: 10px;
    }

    .custom-dropdown-continue {
        background-color: #2ecc71;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
    }

    .custom-dropdown-continue:hover {
        background-color: #27ae60;
    }

    .custom-dropdown:focus-within .custom-dropdown-menu {
        display: block;
    }
</style>
</head>

<body>
    <!-- Content Header (Page header) -->
    <div class="main-content app-content mt-0">
        <div class="side-app">

            <!-- CONTAINER -->
            <div class="main-container container-fluid">
                <!-- PAGE-HEADER -->
                <div class="nav-title1">
                    <div class="page-header d-flex align-items-center justify-content-between mt-1"
                        style="padding: 5px 0;">
                        <div style="margin: 0; padding: 0;">
                            <!-- Page header with breadcrumb navigation -->
                            <div class="page-header d-flex align-items-center justify-content-end"
                                style="margin: 0; padding: 0;">
                                <div style="margin-top: 5px;">
                                    <ol class="breadcrumb float-sm-right" style="margin: 0; padding: 10px !important">
                                        <!-- Home breadcrumb -->
                                        <li class="breadcrumb-item" style="padding: 0 2px;">
                                            <a href="index.php" style="color: #fe5500; font-size: 0.7rem;"><i
                                                    class="fas fa-home me-1"></i><?php echo lang("user_home"); ?></a>
                                        </li>
                                        <!-- Position breadcrumb -->
                                        <li class="breadcrumb-item" style="padding: 0 2px;">
                                            <a href="#"
                                                style="color: #fe5500; font-size: 0.7rem;"><?php echo lang(key: "list_applicant"); ?></a>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- PAGE-HEADER END -->
                <h2 class="page-title" style="color: #fe5500; margin: 5px 0 0 0;">
                    <?php echo lang("list_applicant"); ?>
                </h2>
                <!-- Row -->
                <div class="row1">
                    <div class="row mx-0">
                        <div class="card-body bg-white" style="padding: 20px !important;">

                            <div class="d-flex justify-content-between mb-3">
                                <div></div>
                                <div>
                                    <button id="exportExcelBtn" class="btn btn-success btn-sm">
                                        <i class="fas fa-file-excel"></i> Export to Excel
                                    </button>
                                </div>
                            </div>
                            <!-- Filters -->
                            <div class="card mt-2">
                                <div class="card-body p-2">
                                    <h6 class="card-title d-flex justify-content-between align-items-center"
                                        style="font-size: 0.8rem;">
                                        <button type="button" class="btn btn-sm p-0 m-0" data-bs-toggle="collapse"
                                            data-bs-target="#filterCollapse">
                                            <i class="fa fa-sliders-h"></i> <?php echo lang("list_search_filter"); ?>
                                        </button>
                                    </h6>

                                    <div class="collapse hide" id="filterCollapse">
                                        <div class="d-flex justify-content-end">
                                            <button type="button" id="resetbtn" class="btn btn-sm btn-danger"
                                                style="background-color: #fe5500; border-color: #fe5500;">
                                                <?php echo lang("list_clear_filter"); ?>
                                            </button>
                                        </div>
                                        <div class="row g-1">
                                            <!-- City -->
                                            <div class="col-6 col-md-2">
                                                <label for="city"
                                                    class="form-label small mb-1"><?php echo lang("list_city"); ?></label>
                                                <select class="form-control form-control-sm" id="city" name="city">
                                                    <option value=""><?php echo lang("list_all_cities"); ?></option>
                                                    <?php
                                                    $cities = DB::Query("SELECT DISTINCT city FROM applicants");
                                                    foreach ($cities as $row) {
                                                        if (!empty($row['city'])) {
                                                            $selected = (isset($_POST['city']) && $_POST['city'] == $row['city']) ? 'selected' : '';
                                                            echo "<option value='{$row['city']}' $selected>{$row['city']}</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>

                                            </div>

                                            <!-- State -->
                                            <div class="col-6 col-md-2">
                                                <label for="state"
                                                    class="form-label small mb-1"><?php echo lang("list_State"); ?></label>
                                                <select class="form-control form-control-sm" id="state" name="state">
                                                    <option value=""><?php echo lang("list_all_states"); ?></option>
                                                    <?php
                                                    $states = DB::Query("SELECT DISTINCT state FROM applicants");
                                                    foreach ($states as $row) {
                                                        if (!empty($row['state'])) {
                                                            $selected = (isset($_POST['state']) && $_POST['state'] == $row['state']) ? 'selected' : '';
                                                            echo "<option value='{$row['state']}' $selected>{$row['state']}</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <!-- Legal -->
                                            <!-- <div class="col-6 col-md-2">
                                                    <label for="legal"
                                                        class="form-label small mb-1"><?php echo lang("list_legal_us"); ?></label>
                                                    <select class="form-control form-control-sm" id="legal" name="legal">
                                                        <option value=""><?php echo lang("list_all"); ?></option>
                                                        <option value="Yes" <?= (isset($_POST['legal']) && $_POST['legal'] == 'Yes') ? 'selected' : '' ?>>
                                                            Yes</option>
                                                        <option value="No" <?= (isset($_POST['legal']) && $_POST['legal'] == 'No') ? 'selected' : '' ?>>
                                                            No</option>
                                                    </select>
                                                </div> -->

                                            <!-- Over 18 -->
                                            <div class="col-6 col-md-2">
                                                <label for="over18"
                                                    class="form-label small mb-1"><?php echo lang("list_over_18"); ?></label>
                                                <select class="form-control form-control-sm" id="over18" name="over18">
                                                    <option value=""><?php echo lang("list_all"); ?></option>
                                                    <option value="Yes" <?= (isset($_POST['over18']) && $_POST['over18'] == 'Yes') ? 'selected' : '' ?>>
                                                        Yes</option>
                                                    <option value="No" <?= (isset($_POST['over18']) && $_POST['over18'] == 'No') ? 'selected' : '' ?>>
                                                        No</option>
                                                </select>
                                            </div>

                                            <!-- Status -->
                                            <!-- <div class="col-6 col-md-2">
                                                    <label for="status"
                                                        class="form-label small mb-1"><?php echo lang("list_status"); ?></label>
                                                    <select class="form-control form-control-sm" id="status" name="status">
                                                        <option value=""><?php echo lang("list_all"); ?></option>
                                                        <?php
                                                        // $statuses = DB::Query("SELECT DISTINCT status FROM applicants");
                                                        //  foreach ($statuses as $row) {
                                                        //    if (!empty($row['status'])) {
                                                        //        $selected = (isset($_POST['status']) && $_POST['status'] == $row['status']) ? 'selected' : '';
                                                        //        echo "<option value='{$row['status']}' $selected>{$row['status']}</option>";
                                                        //     }
                                                        // }
                                                        ?>
                                                    </select>
                                                </div> -->
                                            <!-- Add this in your filters section -->
                                            <div class="col-6 col-md-2">
                                                <label for="gender" class="form-label small mb-1">Gender</label>
                                                <select class="form-control form-control-sm" id="gender" name="gender">
                                                    <option value="">All Genders</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>

                                            <!-- Search Button -->
                                            <div class="col-6 col-md-2 d-flex flex-column justify-content-end">
                                                <button type="button" class="btn btn-sm btn-primary w-100" id="Search"
                                                    style="background-color: #fe5500; border-color: #fe5500;">
                                                    <span class="fa fa-eye"></span> <?php echo lang("leave_Search"); ?>
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive-container px-0" style="margin-top: 5px;">
                                <table class="table table-bordered border text-nowrap mb-0 datatable" id="applicants"
                                    style="margin: 0; padding: 0; width: 100% !important;">
                                    <thead>
                                        <tr>
                                            <th><?php echo lang("list_id"); ?></th>
                                            <th><?php echo lang("list_actions"); ?></th>
                                            <th><?php echo lang("form_first_name"); ?></th>
                                            <th><?php echo lang("form_last_name"); ?></th>
                                            <th><?php echo lang("Gender"); ?></th>
                                            <th><?php echo lang("Date Of Birth"); ?></th>
                                            <th><?php echo lang("list_phone"); ?></th>
                                            <th><?php echo lang("list_position"); ?></th>
                                            <th><?php echo lang("list_city"); ?></th>
                                            <th><?php echo lang("list_State"); ?></th>
                                            <!-- <th><?php // echo lang("list_job_location"); 
                                                        ?></th> -->
                                            <!-- <th><?php // echo lang("list_legal_to_us"); 
                                                        ?></th> -->
                                            <!-- <th><?php echo lang("list_job_address"); ?></th> -->
                                            <th><?php echo lang("list_over_18"); ?></th>
                                            <!-- <th><?php echo lang("list_reffered_by"); ?></th> -->
                                            <th>Kiosk ID</th>
                                            <th><?php echo lang("list_created_at"); ?></th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- CONTAINER CLOSED -->
        </div>
    </div>

    <!-- Link Generate Modal -->
    <div class="modal fade" id="generateLinkModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="generateLinkForm" method="post">
                    <div class="modal-header text-white" style="background-color: #fe5500; ">
                        <h5 class="modal-title" style="color:white;"><?php echo lang("generate_new"); ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="generate_link" value="1">
                        <input type="hidden" id="link_applicant_id" name="applicant_id" value="0">
                        <div class="mb-3">
                            <label class="form-label"><?php echo lang("expiry_date"); ?></label>
                            <input type="datetime-local" class="form-control" name="expiry_date">
                            <small class="text-muted"><?php echo lang("leave_empty"); ?></small>
                        </div>
                        <div class="mb-3">
                            <ul>
                                <li>
                                    <label class="custom-dropdown-option">
                                        <input type="checkbox" name="form_step[]" value="3" checked>
                                        <?php echo lang("leave_W4_Data"); ?>
                                    </label>
                                </li>
                                <li>
                                    <label class="custom-dropdown-option">
                                        <input type="checkbox" name="form_step[]" value="4" checked>
                                        <?php echo lang("leave_Quick_Book"); ?>
                                    </label>
                                </li>
                                <li>
                                    <label class="custom-dropdown-option">
                                        <input type="checkbox" name="form_step[]" value="5" checked>
                                        <?php echo lang("leave_EGV"); ?>
                                    </label>
                                </li>
                                <li>
                                    <label class="custom-dropdown-option">
                                        <input type="checkbox" name="form_step[]" value="6" checked>
                                        <?php echo lang("leave_MVR_Information"); ?>
                                    </label>
                                </li>
                                <li>
                                    <label class="custom-dropdown-option">
                                        <input type="checkbox" name="form_step[]" value="7">
                                        <?php echo lang("leave_NON_COMPETE_AGREEMENT"); ?>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal"><?php echo lang("cancel"); ?></button>
                        <button type="submit" class="btn text-white" style="background-color: #fe5500;">
                            <?php echo lang("generate_link"); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Generated Link Modal (New) -->
    <div class="modal fade" id="generatedLinkModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #fe5500;">
                    <h5 class="modal-title" style="color: white;"><?php echo lang("generate_link"); ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <input type="text" class="form-control" id="generatedLinkInput" readonly>
                        <button class="btn text-white" type="button" id="copyLinkButton"
                            style="background-color: #fe5500;">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?php echo lang("position_close"); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Country flag mapping (using ISO country codes)
        const countryFlags = {
            'PK': 'https://flagcdn.com/w20/pk.png', // Pakistan
            'US': 'https://flagcdn.com/w20/us.png', // United States
            'ES': 'https://flagcdn.com/w20/es.png', // Spain
            'MX': 'https://flagcdn.com/w20/mx.png' // Mexico
        };

        // Helper function to format numbers for specific countries
        function formatForSpecificCountry(parsedNumber) {
            const country = parsedNumber.country;
            let formattedNumber = parsedNumber.formatInternational();
            let flagHtml = '';

            if (country && countryFlags[country]) {
                flagHtml =
                    `<span class="country-flag" style="background-image: url(${countryFlags[country]})" title="${country}"></span>`;
            }

            switch (country) {
                case 'PK': // Pakistan
                    formattedNumber = parsedNumber.formatNational();
                    formattedNumber = `+${parsedNumber.countryCallingCode} ${formattedNumber}`;
                    break;
                case 'US': // United States
                    formattedNumber = parsedNumber.formatNational(); // (XXX) XXX-XXXX
                    break;
                case 'ES': // Spain
                    formattedNumber = parsedNumber.formatInternational(); // +34 XXX XXX XXX
                    break;
                case 'MX': // Mexico
                    // Format: +52 1 XXX XXX XXXX (mobile) or +52 XXX XXX XXXX (landline)
                    formattedNumber = parsedNumber.formatNational();
                    if (formattedNumber.startsWith('01')) {
                        formattedNumber = formattedNumber.substring(1); // Remove leading 0
                    }
                    formattedNumber = `+${parsedNumber.countryCallingCode} ${formattedNumber}`;
                    break;
                default:
                    formattedNumber = parsedNumber.formatInternational();
            }

            return {
                formatted: flagHtml + formattedNumber,
                whatsapp: parsedNumber.number,
                country: country
            };
        }

        // Function to format phone number with specific handling for PK, US, ES, MX
        function formatPhoneNumber(phoneNumber, countryCode = null) {
            if (!phoneNumber) return {
                formatted: "N/A",
                whatsapp: null,
                country: null
            };

            try {
                const cleanedNumber = phoneNumber.toString().replace(/[^\d+]/g, '');

                // If we have country from applicant data, use it
                if (countryCode) {
                    try {
                        const parsedNumber = libphonenumber.parsePhoneNumberFromString(cleanedNumber, countryCode);
                        if (parsedNumber && parsedNumber.isValid()) {
                            return formatForSpecificCountry(parsedNumber);
                        }
                    } catch (e) {
                        console.log("Failed to parse with provided country code", e);
                    }
                }

                // Try to parse without country code (will work for numbers with country codes)
                try {
                    const parsedNumber = libphonenumber.parsePhoneNumber(cleanedNumber);
                    if (parsedNumber && parsedNumber.isValid()) {
                        return formatForSpecificCountry(parsedNumber);
                    }
                } catch (e) {
                    console.log("Failed to parse without country code, trying specific countries");
                }

                // Special handling for common countries if automatic parsing fails
                const countriesToTry = ['PK', 'US', 'ES', 'MX']; // Pakistan, USA, Spain, Mexico
                for (const country of countriesToTry) {
                    try {
                        const parsedNumber = libphonenumber.parsePhoneNumberFromString(cleanedNumber, country);
                        if (parsedNumber && parsedNumber.isValid()) {
                            return formatForSpecificCountry(parsedNumber);
                        }
                    } catch (e) {
                        continue;
                    }
                }

                // Return original if parsing fails
                return {
                    formatted: phoneNumber,
                    whatsapp: cleanedNumber,
                    country: null
                };
            } catch (e) {
                console.error("Phone number parsing error:", e);
                return {
                    formatted: phoneNumber,
                    whatsapp: phoneNumber.replace(/[^\d+]/g, ''),
                    country: null
                };
            }
        }

        $(document).ready(function() {

            // Determine if we should show the length menu based on screen width
            var showLengthMenu = $(window).width() > 767; // Same breakpoint as your CSS

            var table = $('#applicants').DataTable({
                "processing": true,
                "serverSide": true,
                "pageLength": 50,
                "ordering": true,
                "order": [
                    [0, 'desc']
                ], // Add this line for initial sorting

                "lengthMenu": showLengthMenu ? [
                    [50, 100, 150, 200],
                    [50, 100, 150, "All"]
                ] : false, // Hide on mobile
                "lengthChange": showLengthMenu, // Disable length change on mobile
                "ajax": {
                    "url": "./ajax_helpers/get_applicants_data.php",
                    "type": "POST",
                    "data": function(d) {
                        d.city = $('#city').val();
                        d.state = $('#state').val();
                        // d.legal = $('#legal').val();
                        d.jobs = $('#jobs').val();
                        d.over18 = $('#over18').val();
                        d.reference = $('#reference').val();
                        d.gender = $('#gender').val(); // This is correct

                        if (d.over18 === "Yes") {
                            d.over18 = "1";
                        } else if (d.over18 === "No") {
                            d.over18 = "0";
                        }
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "actions",
                        "render": function(data, type, row) {
                            // Initialize default values
                            let whatsappLink = '#';
                            let phoneTitle = 'No phone number';
                            let disabledAttr = 'disabled';
                            let phoneData = ''; // will store the phone number in WhatsApp format

                            if (row.phone_number) {
                                const formatted = formatPhoneNumber(row.phone_number, row.country);
                                if (formatted.whatsapp) {
                                    whatsappLink = `https://wa.me/${formatted.whatsapp}`;
                                    phoneTitle = `Chat with ${row.name} on WhatsApp`;
                                    disabledAttr = '';
                                    phoneData = formatted.whatsapp;
                                }
                            }

                            // Only show Generate Link and Send Packet buttons if not HR
                            const showAdminButtons = <?php echo ($_SESSION['role_id'] != $hr) ? 'true' : 'false'; ?>;

                            // Render the actions
                            let actions = data.replace('View', '<i class="fas fa-eye"></i>')
                                .replace('btn-primary', 'btn-primary view-btn');

                            // Build the button group
                            return `
            <div class="action-buttons" data-appid="${row.id}">
                ${actions}
                ${showAdminButtons ? `
                    <button class="btn btn-sm btn-success generateLinkBtn" data-appId="${row.id}" title="Generate Link">
                        <i class="fa fa-link"></i>
                    </button>
                    <div class="custom-dropdown" tabindex="0">
                        <button class="btn btn-sm btn-info text-white p-2">
                            <i class="fas fa-paper-plane d-inline d-sm-none"></i>
                            <span class="d-none d-sm-inline"><?php echo lang("leave_Send_Packet"); ?></span>
                        </button>
                        <div class="custom-dropdown-menu">
                            <label class="custom-dropdown-option"><input type="checkbox" value="3" checked> <?php echo lang("leave_W4_Data"); ?></label>
                            <label class="custom-dropdown-option"><input type="checkbox" value="4" checked> <?php echo lang("leave_Quick_Book"); ?></label>
                            <label class="custom-dropdown-option"><input type="checkbox" value="5" checked> <?php echo lang("leave_EGV"); ?></label>
                            <label class="custom-dropdown-option"><input type="checkbox" value="6" checked> <?php echo lang("leave_MVR_Information"); ?></label>
                            <label class="custom-dropdown-option"><input type="checkbox" value="7"> <?php echo lang("leave_NON_COMPETE_AGREEMENT"); ?></label>
                            <label class="form-label">KIOSK ID</label>
                            <input type="text" class="form-control" name="kiosk_id" placeholder="Enter KIOSK ID">
                            <div class="custom-dropdown-footer">
                                <button class="custom-dropdown-continue" data-applicant="${row.id}">Continue</button>
                            </div>
                        </div>
                    </div>
                ` : ''}
            </div>`;
                        },
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "first_name"
                    },
                    {
                        "data": "last_name"
                    },
                    {
                        "data": "gender",
                        "render": function(data, type, row) {
                            return data || 'N/A'; // Display gender or 'N/A' if empty
                        }
                    },
                    {
                        "data": "dob",
                        "render": function(data, type, row) {
                            if (data && data !== 'N/A') {
                                try {
                                    // Try parsing the date in ISO format (YYYY-MM-DD)
                                    const date = new Date(data);
                                    if (!isNaN(date.getTime())) { // Check if date is valid
                                        return date.toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric'
                                        });
                                    }
                                } catch (e) {
                                    console.error("Date parsing error:", e);
                                }
                            }
                            return "N/A";
                        }
                    },
                    {
                        "data": "phone_number"
                    },
                     {
    "data": "position_name",
    "render": function(data, type, row) {
        if (data) {
            // Remove line breaks and show as single line
            return `<div class="position-cell">${data.replace(/,/g, ', ')}</div>`;
        }
        return '';
    },
    "width": "150px" // Fixed width
},

                    {
                        "data": "city",
                        "defaultContent": "N/A"
                    },
                    {
                        "data": "state",
                        "defaultContent": "N/A"
                    },
                    // {
                    //     "data": "legal_us_work_eligibility",
                    //     "render": function (data, type, row) {
                    //         return data == "1" ? 'Yes' : 'No';
                    //     }
                    // },
                    // {
                    //     "data": "jobs",
                    //     "defaultContent": "N/A"
                    // },
                    {
                        "data": "over_18",
                        "render": function(data, type, row) {
                            // Handle both string "Yes"/"No" and numeric 1/0 cases
                            if (data === "1" || data === "Yes") return 'Yes';
                            if (data === "0" || data === "No") return 'No';
                            return 'N/A';
                        },
                        "defaultContent": "N/A"
                    },
                    // {
                    //     "data": "reference",
                    //     "defaultContent": "N/A",
                    //     "width": "150px" // Set fixed width
                    // },
                    {
                        "data": "kioskID",
                        "defaultContent": "N/A"
                    },
                    {
                        "data": "created_at",
                        "defaultContent": "N/A",
                        "render": function(data, type, row) {
                            if (data) {
                                var date = new Date(data);
                                // Format the date to a more readable format
                                return date.toLocaleDateString('en-US', {
                                    weekday: 'short', // "Mon"
                                    year: 'numeric', // "2025"
                                    month: 'short', // "Apr"
                                    day: 'numeric' // "27"
                                });
                            }
                            return "N/A"; // If no date is available
                        }
                    },
                    {
                        "data": "status",
                        "defaultContent": "N/A",
                        "render": function(data, type, row) {
                            if (data === "Hired") {
                                return '<span class="badge badge-success">Hired</span>';
                            } else if (data === "Not Hired") {
                                return '<span class="badge badge-danger">Not Hired</span>';
                            } else {
                                return data;
                            }
                        }
                    },
                ],
                "createdRow": function(row, data, dataIndex) {
                    if (data.phone_number) {
                        const formatted = formatPhoneNumber(data.phone_number, data.country);
                        if (!formatted.country) {
                            $(row).find('td:eq(2)').css('color', 'red')
                                .attr('title', 'Phone number format may be invalid');
                        }
                    }
                },
                "responsive": false // Disable DataTables responsive feature
            });

            // $(document).on('click', '.custom-dropdown-continue', function (e) {
            //     e.preventDefault();

            //     // Find the parent dropdown
            //     const dropdown = $(this).closest('.custom-dropdown');

            //     // Get all checked checkboxes inside this dropdown
            //     const selectedValues = dropdown.find('input[type="checkbox"]:checked').map(function () {
            //     return $(this).val();
            //     }).get();

            //     // Show in console (or you can send it to server etc.)
            //     console.log("Selected values:", selectedValues);
            // });

            // Update table options on window resize
            $(window).on('resize', function() {
                var newWidth = $(window).width();
                if ((newWidth <= 767 && showLengthMenu) || (newWidth > 767 && !showLengthMenu)) {
                    showLengthMenu = newWidth > 767;
                    table.settings()[0]._iDisplayLength = 50; // Reset to default
                    table.settings()[0].aLengthMenu = showLengthMenu ? [
                        [50, 100, 150, 200],
                        [50, 100, 150, "All"]
                    ] : false;
                    table.page.len(50).draw();
                }
            });

            $('#Search').click(function() {
                table.ajax.reload();
            });

            $('#resetbtn').click(function() {
                $('#city').val('');
                $('#state').val('');
                // $('#legal').val('');
                $('#over18').val('');
                $('#reference').val('');
                $('#gender').val('');
                table.ajax.reload();
            });

            // Quick view button click event to display detailed applicant info
            $(document).on('click', '.quickBtn', function() {
                let applicant_id = $(this).attr('data-applicant_id');
                var info_content = ``;
                var note_content = ``;

                $.ajax({
                    url: './ajax_helpers/ajax_applicant_quick_view.php',
                    method: 'POST',
                    data: {
                        applicant_id: applicant_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 200) {
                            let applicant = response.data;
                            let applicant_status = response.applicant_status;
                            let applicant_notes = response.notes;

                            const email = applicant.email || '';

                            if (applicant.phone_number) {
                                const formatted = formatPhoneNumber(applicant.phone_number,
                                    applicant.country);
                                phoneDisplay = formatted.formatted;
                                if (formatted.whatsapp) {
                                    whatsappLink = `https://wa.me/${formatted.whatsapp}`;
                                    phoneTitle = `Chat with ${applicant.name} on WhatsApp`;
                                    disabledAttr = '';
                                    phoneData = formatted.whatsapp;
                                }
                            }

                            info_content = `
                                    <div class="row">
                                        <div class="col-lg-6 mb-2">
                                            <ul>
                                                <li><strong>Status:</strong> ${applicant_status}</li>
                                                <li><strong>Phone:</strong> ${phoneDisplay}</li>
                                                <li><strong>Date of Birth:</strong> ${applicant.date_of_birth || 'N/A'}</li>
                                                <li><strong>Experience:</strong> ${applicant.employment_experience || 'N/A'}</li>
                                                <li><strong>Expected Salary:</strong> ${applicant.expected_salary_contract || 'N/A'}</li>
                                                <li><strong>Joining Date:</strong> ${applicant.joining_date || 'N/A'}</li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-6 mb-2">
                                            <ul>
                                                <li><strong>CNIC:</strong> ${applicant.cnic_no || 'N/A'}</li>
                                                <li><strong>Gender:</strong> ${applicant.gender || 'N/A'}</li>
                                                <li><strong>Current Salary:</strong> ${applicant.current_salary || 'N/A'}</li>
                                                <li><strong>Working Hours:</strong> ${applicant.working_hours || 'N/A'}</li>
                                                <li><strong>Previous Company:</strong> ${applicant.previous_company || 'N/A'}</li>
                                                <li><strong>Interview Availability:</strong> ${applicant.interview_availability || 'N/A'}</li>
                                            </ul>
                                        </div>
                                        <div class="col-12 text-center mt-3">
                                            <div class="btn-group" data-email="${email}">
                                                <button type="button" class="btn btn-info btn-sm email-btn" title="Send Email to ${applicant.name}" ${email ? '' : 'disabled'}>
                                                    <i class="fas fa-envelope"></i> Email
                                                </button>
                                                <button type="button" class="btn btn-info btn-sm dropdown-toggle" ${email ? '' : 'disabled'} data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>

                                                <ul class="dropdown-menu rounded-0 shadow">
                                                    <li>
                                                        <span class="dropdown-item ${email ? '' : 'disabled'}">Select Template</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>`;
                            $('#info_content').html(info_content);

                            applicant_notes.forEach(function(note) {
                                const createdAtUTC = new Date(note.created_at);
                                const localDateTime = createdAtUTC.toLocaleString(
                                    'en-US', {
                                        dateStyle: 'long',
                                        timeStyle: 'short',
                                        hour12: true,
                                        timeZone: 'Asia/Karachi'
                                    });
                                note_content += `<div id="note${note.note_id}" class="bg-white p-2 mb-3 border">
                                        ${note.note}
                                     <p class="text-end text-muted mt-2">${localDateTime}</p>
                                     </div>`;
                            });
                            $('#appli_note_container').html(note_content);
                        }
                    }
                });
            });


            $(document).on('show.bs.dropdown', '.btn-group', function() {
                const $dropdownMenu = $(this).find('.dropdown-menu');
                const $templateItems = $dropdownMenu.find('.template-item');
                const $actionButtons = $(this).closest('.action-buttons');
                const appid = $actionButtons.data('appid');

                if ($templateItems.length > 0) return;

                const email = $(this).data('email');
                if (!email) {
                    $dropdownMenu.append(
                        '<li><span class="dropdown-item text-warning">No email available</span></li>');
                    return;
                }

                $.ajax({
                    url: './ajax_helpers/send_email_template.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        appid: appid
                    },
                    success: function(response) {
                        console.log("Response:", response);

                        let html = '';
                        if (response.status === 'success' && response.templates && response
                            .templates.length > 0) {

                            response.templates.forEach(function(template) {
                                html += `
                                    <li class="template-item d-flex justify-content-between align-items-center">
                                        <span class="dropdown-item">${template.short_name}</span>
                                        <button class="btn btn-sm btn-primary send-template-btn" 
                                                data-template-id="${template.id}">
                                            Send
                                        </button>
                                    </li>`;
                            });
                        } else {
                            const message = response.message || 'No templates found';
                            html =
                                `<li><span class="dropdown-item text-warning">${message}</span></li>`;
                        }

                        $dropdownMenu.html(html);


                        $dropdownMenu.on('click', '.send-template-btn', function() {
                            const templateId = $(this).data('template-id');
                            sendEmailTemplate(appid, templateId);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading templates:", error);
                        $dropdownMenu.html(
                            '<li><span class="dropdown-item text-danger">Failed to load templates</span></li>'
                        );
                    }
                });
            });


            function sendEmailTemplate(appid, templateId) {
                $.ajax({
                    url: './ajax_helpers/send_email_template.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        appid: appid,
                        template_id: templateId
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Email sent successfully!',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to send email',
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error sending email:", error);
                        alert('Failed to send email. Please try again.');
                    }
                });
            }

        });
    </script>

    <!-- Add SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Use event delegation if your table is reloaded via AJAX (which it is)
            document.addEventListener('click', function(e) {
                if (e.target.closest('.delete-btn')) {
                    e.preventDefault();

                    const button = e.target.closest('.delete-btn');
                    const applicantId = button.getAttribute('data-id');
                    const applicantName = button.getAttribute('data-name');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Do you really want to delete ${applicantName}? This action cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect to your delete URL
                            window.location.href =
                                `index.php?route=modules/applicants/delete_applicant&id=${applicantId}`;
                        }
                    });
                }
            });
        });
    </script>
    <script>
        // Excel Export Functionality
        $('#exportExcelBtn').click(function() {
            // Get current filters
            const filters = {
                city: $('#city').val(),
                state: $('#state').val(),
                over18: $('#over18').val(),
                reference: $('#reference').val(),
                gender: $('#gender').val()
            };

            // Show loading indicator
            Swal.fire({
                title: 'Preparing Excel File',
                html: 'Please wait while we prepare your download...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send AJAX request to export endpoint
            $.ajax({
                url: 'ajax_helpers/export_applicants.php',
                method: 'POST',
                data: filters,
                success: function(response) {
                    Swal.close();

                    if (response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Export Failed',
                            text: response.error
                        });
                        return;
                    }

                    // Prepare data for Excel
                    const data = [
                        response.headers, // Header row
                        ...response.data.map(item => [
                            item.id,
                            item.first_name,
                            item.last_name,
                            item.gender || 'N/A',
                            item.phone_number,
                            item.position_name,
                            item.city,
                            item.state,
                            item.legal_us_work_eligibility ? 'Yes' : 'No',
                          item.over_18 === "Yes" || item.over_18 === "1" ? 'Yes' : 'No',
                            // item.reference,
                            item.kioskID,
                            new Date(item.created_at).toLocaleDateString(),
                            item.status
                        ])
                    ];

                    // Create workbook
                    const wb = XLSX.utils.book_new();
                    const ws = XLSX.utils.aoa_to_sheet(data);

                    // Set column widths
                    const colWidths = [{
                            wch: 5
                        }, // ID
                        {
                            wch: 15
                        }, // First Name
                        {
                            wch: 15
                        }, // Last Name
                        {
                            wch: 10
                        }, // Gender (new column)
                        {
                            wch: 20
                        }, // Phone
                        {
                            wch: 20
                        }, // Position
                        {
                            wch: 15
                        }, // City
                        {
                            wch: 10
                        }, // State
                        {
                            wch: 15
                        }, // Legal
                        {
                            wch: 10
                        }, // Over 18
                        // {
                        //     wch: 20
                        // }, // Reference
                        {
                            wch: 15
                        }, // Kiosk ID
                        {
                            wch: 20
                        }, // Created At
                        {
                            wch: 15
                        } // Status
                    ];
                    ws['!cols'] = colWidths;

                    // Add worksheet to workbook
                    XLSX.utils.book_append_sheet(wb, ws, "Applicants");

                    // Generate XLSX file and download
                    const fileName = `applicants_export_${new Date().toISOString().slice(0, 10)}.xlsx`;
                    XLSX.writeFile(wb, fileName);

                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Export Failed',
                        text: 'An error occurred while generating the Excel file. Please try again.'
                    });
                    console.error('Export error:', xhr.responseText);
                }
            });
        });
    </script>
    <script>
        // Send Mail
        $(document).ready(function() {
            $(document).on('click', '.custom-dropdown-continue', function(e) {
                e.preventDefault();

                // Get applicant ID
                let applicant_id = $(this).attr('data-applicant');
                // Find the parent dropdown
                const dropdown = $(this).closest('.custom-dropdown');

                // Get the KIOSK ID input value
                const kioskId = dropdown.find('input[name="kiosk_id"]').val();

                // Basic validation
                if (!kioskId) {
                    Swal.fire('Error', 'KIOSK ID cannot be empty', 'error');
                    return;
                }
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Sending...').prop('disabled', true);



                // Get all checked checkboxes inside this dropdown
                const selectedValues = dropdown.find('input[type="checkbox"]:checked').map(function() {
                    return $(this).val();
                }).get();

                // Show in console (or you can send it to server etc.)
                // console.log("Selected values:",  selectedValues);
                // Uncheck all selected checkboxes
                $(this).text('Sending...').prop('disabled', true);
                dropdown.find('input[type="checkbox"]:checked').prop('checked', false);

                $.ajax({
                    url: 'ajax_helpers/ajax_send_packet_mail.php',
                    method: 'POST',
                    data: {
                        applicant_id: applicant_id,
                        forms: selectedValues,
                        kiosk_id: kioskId // Add KIOSK ID to the request

                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status == 200) {
                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                confirmButtonColor: "#FE5500"
                            }).then(() => {
                                // Reload the page to reflect changes
                                location.reload();
                            });
                        }
                        if (response.status == 400) {
                            Swal.fire({
                                title: "Action Failed!",
                                text: response.message,
                                icon: "error",
                                confirmButtonColor: "#FE5500"
                            });
                        }
                    }
                });
            });
        });

        function generateApplicantLink(applicantId) {
            // Show loading indicator
            Swal.fire({
                title: 'Generating Link...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // AJAX request to your PHP endpoint
            $.ajax({
                url: 'ajax_helpers/link_generator.php',
                type: 'POST',
                data: {
                    generate_link: 1,
                    applicant_id: applicantId
                },
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        // Auto-copy to clipboard
                        navigator.clipboard.writeText(response.link).then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Link Generated & Copied!',
                                text: 'Unique link copied to clipboard',
                                confirmButtonColor: '#fe5500',
                                timer: 2000
                            });
                        }).catch(err => {
                            console.error('Copy failed:', err);
                            Swal.fire({
                                icon: 'success',
                                title: 'Link Generated!',
                                html: `Here's your link: <br><code>${response.link}</code>`,
                                confirmButtonColor: '#fe5500'
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Generation Failed',
                            text: response.error || 'Unknown error occurred',
                            confirmButtonColor: '#fe5500'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Request Failed',
                        text: `Server error: ${xhr.statusText}`,
                        confirmButtonColor: '#fe5500'
                    });
                }
            });
        }

        // Generate Link
        $(document).on('click', '.generateLinkBtn', function() {
            var applicant_id = $(this).attr('data-appId');
            $('#link_applicant_id').val(applicant_id);
            $('#generateLinkModal').modal('show');

            $('#generateLinkForm').submit(function(e) {
                e.preventDefault();
                const selectedForms = $('input[name="form_step[]"]:checked');

                if (selectedForms.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selection Required',
                        text: 'Please select at least one form to generate the link',
                        confirmButtonColor: '#FE5500'
                    });
                    return false;
                }

                // If selections are made, continue with submission
                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: 'ajax_helpers/link_generator.php',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Close original modal
                            $('#generateLinkModal').modal('hide');

                            // Show new modal with generated link
                            $('#generatedLinkInput').val(response.link);
                            $('#generatedLinkModal').modal('show');
                        } else {
                            Swal.fire('Error', response.error || 'Unknown error', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Request failed: ' + xhr.statusText, 'error');
                    }
                });
            });

            $('#generateLinkModal').on('show.bs.modal', function() {
                const $expiryInput = $(this).find('input[name="expiry_date"]');
                const today = new Date();
                today.setHours(0, 0, 0, 0); // Set to start of today

                // Format to YYYY-MM-DDTHH:mm (local time)
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const minDate = `${year}-${month}-${day}T00:00`;

                $expiryInput.attr('min', minDate);
            });


            // Copy functionality
            $('#copyLinkButton').click(function() {
                var copyText = document.getElementById("generatedLinkInput");
                copyText.select();

                try {
                    navigator.clipboard.writeText(copyText.value).then(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Copied!',
                            text: 'Link copied to clipboard',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });
                } catch (err) {
                    // Fallback for older browsers
                    document.execCommand('copy');
                    Swal.fire({
                        icon: 'success',
                        title: 'Copied!',
                        text: 'Link copied to clipboard',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });

            // Reset forms when modals close
            $('#generateLinkModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
            });

            $('#generatedLinkModal').on('hidden.bs.modal', function() {
                $('#generatedLinkInput').val('');
            });

        });
    </script>