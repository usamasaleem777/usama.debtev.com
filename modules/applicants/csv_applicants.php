<?php
/************** START SECURITY CHECK ***********************/
// Define allowed roles - make sure these variables are defined somewhere in your application
$allowedRoles = array(
					$admin_role, 
					$manager_role
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

    $uploadDir = __DIR__ . '/../../assets/csv_files/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $expectedColumns = ['First name', 'Last name', 'Email', 'Mobile phone', 'Middle Innitial', 'Date of Birth', 'Start date', 'Position', 'Job', 'KIOSK ID'];

   $sampleFile = '';
    $fileExists = file_exists($sampleFile);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv']['tmp_name'])) {
        $file = $_FILES['csv'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($fileExtension !== 'csv') {
            showAlertRedirect('error', 'Invalid File', 'Only CSV files are allowed!', 'index.php?route=modules/applicants/csv_applicants');
        }

        $uniqueFileName = uniqid() . '_' . basename($file['name']);
        $uploadFilePath = $uploadDir . $uniqueFileName;

        if (!move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
            showAlertRedirect('error', 'Upload Failed', 'Error moving the file to the uploads directory.', 'index.php?route=modules/applicants/csv_applicants');
        }

        if (($handle = fopen($uploadFilePath, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            $header = array_map('trim', $header);

            if (empty($header)) {
                unlink($uploadFilePath);
                showAlertRedirect('error', 'Empty File', 'The uploaded CSV file is empty.', 'index.php?route=modules/applicants/csv_applicants');
            }

            $mappedIndexes = [];
            foreach ($expectedColumns as $col) {
                $index = array_search($col, $header);
                $mappedIndexes[$col] = ($index !== false) ? $index : null;
            }

            $rowCount = 0;
            $skippedCount = 0;

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $applicant_data = [];
                foreach ($expectedColumns as $col) {
                    $applicant_data[$col] = ($mappedIndexes[$col] !== null && isset($data[$mappedIndexes[$col]])) ? trim($data[$mappedIndexes[$col]]) : 'NA';
                }

                $existingApplicant = DB::queryFirstRow("SELECT email FROM applicants WHERE email = %s", $applicant_data['Email']);

                if ($existingApplicant) {
                    DB::update('applicants', [
                        'first_name' => $applicant_data['First name'],
                        'last_name' => $applicant_data['Last name'],
                        'phone_number' => $applicant_data['Mobile phone'],
                        'middle_initial' => $applicant_data['Middle Innitial'],
                        'email' => $applicant_data['Email'],
                        'available_start_date' => $applicant_data['Start date'],
                        'position' => $applicant_data['Position'],
                        'job_applied' => $applicant_data['Job'],
                        'city' => 'NA',
                        'dob' => $applicant_data['Date of Birth'],
                        'kioskID' => $applicant_data['KIOSK ID'],
                    ], "email = %s", $applicant_data['Email']);

                    DB::update('users', [
                        'user_name' => $applicant_data['First name'] . $applicant_data['Last name'],
                        'name' => $applicant_data['First name'] . ' ' . $applicant_data['Last name'],
                        'email' => $applicant_data['Email'],
                        'phone' => $applicant_data['Mobile phone'],
                        'kioskID' => $applicant_data['KIOSK ID'],
                    ], "email = %s", $applicant_data['Email']);

                    DB::update('csv_uploads', [
                        'first_name' => $applicant_data['First name'],
                        'last_name' => $applicant_data['Last name'],
                        'phone_number' => $applicant_data['Mobile phone'],
                        'middle_initial' => $applicant_data['Middle Innitial'],
                        'email' => $applicant_data['Email'],
                        'start_date' => $applicant_data['Start date'],
                        'position' => $applicant_data['Position'],
                        'job' => $applicant_data['Job'],
                        'dob' => $applicant_data['Date of Birth'],
                        'kioskID' => $applicant_data['KIOSK ID'],
                    ], "email = %s", $applicant_data['Email']);


                    $rowCount++;
                } else {
                    DB::insert('applicants', [
                        'first_name' => $applicant_data['First name'],
                        'last_name' => $applicant_data['Last name'],
                        'phone_number' => $applicant_data['Mobile phone'],
                        'middle_initial' => $applicant_data['Middle Innitial'],
                        'email' => $applicant_data['Email'],
                        'available_start_date' => $applicant_data['Start date'],
                        'position' => $applicant_data['Position'],
                        'job_applied' => $applicant_data['Job'],
                        'city' => 'NA',
                        'dob' => $applicant_data['Date of Birth'],
                        'kioskID' => $applicant_data['KIOSK ID'],
                    ]);

                    DB::insert('users', [
                        'user_name' => $applicant_data['First name'] . $applicant_data['Last name'],
                        'name' => $applicant_data['First name'] . ' ' . $applicant_data['Last name'],
                        'email' => $applicant_data['Email'],
                        'phone' => $applicant_data['Mobile phone'],
                        'kioskID' => $applicant_data['KIOSK ID'],
                    ]);

                    DB::insert('csv_uploads', [
                        'first_name' => $applicant_data['First name'],
                        'last_name' => $applicant_data['Last name'],
                        'phone_number' => $applicant_data['Mobile phone'],
                        'middle_initial' => $applicant_data['Middle Innitial'],
                        'email' => $applicant_data['Email'],
                        'start_date' => $applicant_data['Start date'],
                        'position' => $applicant_data['Position'],
                        'job' => $applicant_data['Job'],
                        'dob' => $applicant_data['Date of Birth'],
                        'kioskID' => $applicant_data['KIOSK ID'],
                    ]);
                    $skippedCount++;
                }
            }

            fclose($handle);
            unlink($uploadFilePath);

            $message = "File processed successfully. $rowCount records updated, $skippedCount inserted.";
            showAlertRedirect('success', 'Upload Complete', $message, 'index.php?route=modules/applicants/csv_applicants');
        } else {
            showAlertRedirect('error', 'File Error', 'Error opening the file for reading.', 'index.php?route=modules/applicants/csv_applicants');
        }
    }

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

        /* Blue option */
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
            z-index: 10;
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
                                        <ol class="breadcrumb float-sm-right" style="margin: 0; padding: 10px; !important">
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
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="card shadow-sm p-4">
                            <h5 class="mb-4 text-primary">Upload CSV File</h5>

                            <div class="mb-3">
                                <label for="csv" class="form-label fw-semibold">The CSV file should be of either Applicants or Users</label>
                                <input type="file" name="csv" class="form-control" id="csv" accept=".csv" required>
                                <small class="text-muted mt-2 d-block">CSV columns: 'First name', 'Last name', 'Email', 'Mobile phone', 'Middle Innitial', 'Date of Birth', 'Start date', 'Position', 'Job', 'KIOSK ID'</small>
                            </div>
                            <div class="mb-3 d-flex gap-2">
                                <!-- Upload File Button -->
                                <button type="submit" class="btn text-white px-4"
                                    style="background-color: #fe5500; border-color: #fe5500;">
                                    <i class="bi bi-upload me-1"></i> Upload File
                                </button>

                                <a href="<?php echo $fileExists ? $sampleFile : '/crafthiring/modules/applicants/sample.csv'; ?>" 
                                download="sample.csv"
                                class="btn btn-secondary px-4 <?php echo $fileExists ? '' : ''; ?>"
                                <?php if (!$fileExists) echo 'title="File not found"'; ?>>
                                    <i class="bi bi-download me-1"></i> Download Sample
                                </a>
                                <?php if (!$fileExists): ?>
                                    <small class="text-danger"></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>

                    

                    <!-- PAGE-HEADER END -->
                    <h2 class="page-title" style="color: #fe5500; margin: 5px 0 0 0;">
                        CSV Applicants
                    </h2>
                    <!-- Row -->
                    <div class="row1">
                        <div class="row mx-0">
                            <div class="card-body bg-white" style="padding: 20px !important;">
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
                                                <th><?php echo lang("list_phone"); ?></th>
                                                <th><?php echo lang("form_email"); ?></th>
                                                <th><?php echo lang("list_position"); ?></th>
                                                <th><?php echo lang("list_job"); ?></th>
                                                <th><?php echo lang("KIOSK ID"); ?></th>
                                                <th><?php echo lang("dob"); ?></th>
                                                <th><?php echo lang("form_start_date"); ?></th>
                                                <th><?php echo lang("list_created_at"); ?></th>
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

        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

        <script>
            // Country flag mapping (using ISO country codes)


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

            $(document).ready(function () {

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
                        "url": "./ajax_helpers/ajax_csv_uploads.php",
                        "type": "POST",
                        "data": function (d) {
                            // d.city = $('#city').val();
                            // d.state = $('#state').val();
                            // d.legal = $('#legal').val();
                            // d.jobs = $('#jobs').val();
                            // d.over18 = $('#over18').val();
                            // d.reference = $('#reference').val();
                        }
                    },
                    "columns": [{
                        "data": "id"

                    },

                    {
                        "data": "actions",

                    },
                    {
                        "data": "first_name"
                    },
                    {
                        "data": "last_name"
                    },
                    {
                        "data": "phone_number"
                    },
                    {
                        "data": "email"
                    },
                    {
                        "data": "position_name",
                        "render": function (data, type, row) {
                            if (data) {
                                return data.split(',').join('<br>');
                            }
                            return '';
                        }
                    },

                    {
                        "data": "job",
                        "defaultContent": "N/A"
                    },
                    {
                        "data": "kioskID",
                        "defaultContent": "N/A"
                    },
                    {
                        "data": "dob",
                        "defaultContent": "N/A"
                    },
                    {
                        "data": "available_start_date",
                        "render": function (data, type, row) {
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
                        "data": "created_at",
                        "defaultContent": "N/A",
                        "render": function (data, type, row) {
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
                    ],

                });
                // Update table options on window resize
                $(window).on('resize', function () {
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

                $('#Search').click(function () {
                    table.ajax.reload();
                });

                $('#resetbtn').click(function () {
                    $('#city').val('');
                    $('#state').val('');
                    // $('#legal').val('');
                    $('#over18').val('');
                    $('#reference').val('');
                    table.ajax.reload();
                });

                // Quick view button click event to display detailed applicant info
                $(document).on('click', '.quickBtn', function () {
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
                        success: function (response) {
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

                                applicant_notes.forEach(function (note) {
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


                $(document).on('show.bs.dropdown', '.btn-group', function () {
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
                        success: function (response) {
                            console.log("Response:", response);

                            let html = '';
                            if (response.status === 'success' && response.templates && response
                                .templates.length > 0) {

                                response.templates.forEach(function (template) {
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


                            $dropdownMenu.on('click', '.send-template-btn', function () {
                                const templateId = $(this).data('template-id');
                                sendEmailTemplate(appid, templateId);
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error("Error loading templates:", error);
                            $dropdownMenu.html(
                                '<li><span class="dropdown-item text-danger">Failed to load templates</span></li>'
                            );
                        }
                    });
                });



            });

            // Add this after your DataTable initialization
            $(document).on('click', '.transfer-btn', function () {
                const id = $(this).data('id');
                const $row = $(this).closest('tr');

                Swal.fire({
                    title: 'Confirm Transfer',
                    text: 'Transfer this applicant to main database?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#fe5500',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Transfer'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'index.php?route=modules/applicants/transfer_csv',
                            method: 'POST',
                            data: {
                                id: id
                            },
                            dataType: 'json',
                            beforeSend: function () {
                                $row.css('opacity', '0.5');
                            }
                        }).done(function (response) {
                            if (response.success) {
                                table.row($row).remove().draw();
                                Swal.fire('Success!', response.message, 'success');
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                                $row.css('opacity', '1');
                            }
                        }).fail(function () {
                            Swal.fire('Error!', 'Transfer failed', 'error');
                            $row.css('opacity', '1');
                        });
                    }
                });
            });
        </script>

        <!-- Add SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Use event delegation if your table is reloaded via AJAX (which it is)
                document.addEventListener('click', function (e) {
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
                                // Use AJAX to delete the applicant without redirecting
                                $.ajax({
                                    url: 'index.php?route=modules/applicants/delete_csv', // PHP endpoint for deletion
                                    type: 'POST',
                                    data: {
                                        id: applicantId
                                    }, // Send the applicant ID to delete
                                    success: function (response) {
                                        if (response.success) {
                                            Swal.fire('Deleted!', `${applicantName} has been deleted.`, 'success');
                                            // Optionally, remove the applicant from the page
                                            $(`#applicant-${applicantId}`).remove(); // Assuming applicant rows have a unique ID like applicant-ID
                                        } else {
                                            window.location.href = "index.php?route=modules/applicants/csv_applicants";

                                        }
                                    },
                                    error: function () {
                                        Swal.fire('Error!', 'There was an issue with the request.', 'error');
                                    }
                                });
                            }
                        });
                    }
                });
            });
        </script>
 