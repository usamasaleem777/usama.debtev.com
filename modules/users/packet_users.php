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

        /* Force cells to inherit row background */
        #applicants tbody tr[style*="background-color"] td {
            background-color: inherit !important;
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
                                        <ol class="breadcrumb float-sm-right" style="margin: 0; padding: 10px;">
                                            <!-- Home breadcrumb -->
                                            <li class="breadcrumb-item" style="padding: 0 2px;">
                                                <a href="index.php" style="color: #fe5500; font-size: 0.7rem;"><i
                                                        class="fas fa-home me-1"></i><?php echo lang("user_home"); ?></a>
                                            </li>
                                            <!-- Position breadcrumb -->
                                            <li class="breadcrumb-item" style="padding: 0 2px;">
                                                <a href="#"
                                                    style="color: #fe5500; font-size: 0.7rem;"><?php echo lang(key: "users"); ?></a>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h2 class="page-title" style="color: #fe5500; margin: 5px 0 0 0;">USERS</h2>

                    <!-- Filters Row -->
                    <div class="row1 mb-3 d-flex flex-wrap" style="gap: 15px;">
                        <div class="col-6 col-md-2">
                            <label for="ssn" class="form-label small mb-1"><?php echo lang("ssn"); ?></label>
                            <select class="form-control form-control-sm" id="ssn" name="ssn">
                                <option value=""><?php echo lang("list_all"); ?></option>
                                <option value="Yes" <?= (isset($_POST['ssn']) && $_POST['ssn'] == 'Yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="No" <?= (isset($_POST['ssn']) && $_POST['ssn'] == 'No') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="dob" class="form-label small mb-1"><?php echo lang("formview_dob"); ?></label>
                            <select class="form-control form-control-sm" id="dob" name="dob">
                                <option value=""><?php echo lang("list_all"); ?></option>
                                <option value="Yes" <?= (isset($_POST['dob']) && $_POST['dob'] == 'Yes') ? 'selected' : '' ?>>Yes</option>
                                <option value="No" <?= (isset($_POST['dob']) && $_POST['dob'] == 'No') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="table-responsive-container px-0" style="margin-top: 5px;">
                        <table class="table table-bordered border text-nowrap mb-0 datatable" id="applicants" style="width: 100% !important;">
                            <thead>
                                <tr>
                                    <th><?php echo lang("list_actions"); ?></th>
                                    <th><?php echo lang("list_id"); ?></th>
                                    <th><?php echo lang("user_profile_picture"); ?></th>
                                    <th><?php echo lang("form_first_name"); ?></th>
                                    <th><?php echo lang("form_last_name"); ?></th>
                                    <th><?php echo lang("form_middle_initial"); ?></th>
                                    <th><?php echo lang("list_city"); ?></th>
                                    <th><?php echo lang("list_State"); ?></th>
                                    <th><?php echo lang("form_zip"); ?></th>
                                    <th><?php echo lang("list_legal_to_us"); ?></th>
                                    <th><?php echo lang("ssn"); ?></th>
                                    <th><?php echo lang("forms_gender"); ?></th>
                                    <th><?php echo lang("formview_dob"); ?></th>
                                    <th><?php echo lang("forms_select"); ?></th>
                                    <th><?php echo lang("submitted_at"); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- DataTables JS -->
                    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
                    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

                    <script>
                        $(document).ready(function() {
                            const showLengthMenu = $(window).width() > 767;

                            const table = $('#applicants').DataTable({
                                processing: true,
                                serverSide: true,
                                pageLength: 50,
                                ordering: true,
                                pagination: true,
                                searching: true,
                                order: [
                                    [1, 'desc']
                                ],
                                lengthMenu: showLengthMenu ? [
                                    [50, 100, 150, -1],
                                    [50, 100, 150, "All"]
                                ] : false,
                                lengthChange: showLengthMenu,
                                ajax: {
                                    url: './ajax_helpers/get_packet_users.php',
                                    type: 'POST',
                                    data: function(d) {
                                        // Send filter values
                                        d.ssn = $('#ssn').val();
                                        d.dob = $('#dob').val();

                                        return {
                                            draw: d.draw,
                                            start: d.start,
                                            length: d.length,
                                            'search[value]': d.search.value,
                                            'order[0][column]': d.order[0].column,
                                            'order[0][dir]': d.order[0].dir,
                                            ssn: $('#ssn').val(),
                                            dob: $('#dob').val()
                                        }

                                    }

                                },
                                columns: [{
                                        data: "actions",
                                        orderable: false,
                                        searchable: false,
                                        render: function(data, type, row) {
                                            if (row.in_contract == 1 || row.in_contract == 2) {
                                                return `<div class="action-buttons" data-appid="${row.contract_id}">${data}</div>`;
                                            }
                                            return `<button class="btn btn-secondary btn-sm" disabled title="Not in contract"><i class="fas fa-ban"></i></button>`;
                                        }
                                    },
                                    {
                                        data: "contract_id"
                                    },
                                    {
                                        data: 'profile_picture',
                                        title: 'Photo',
                                        orderable: false,
                                        searchable: false,
                                        className: 'text-center'
                                    },

                                    {
                                        data: "first_name"
                                    },
                                    {
                                        data: "last_name"
                                    },
                                    {
                                        data: "middle_initial",
                                        defaultContent: "N/A"
                                    },
                                    {
                                        data: "contract_city",
                                        defaultContent: "N/A"
                                    },
                                    {
                                        data: "contract_state",
                                        defaultContent: "N/A"
                                    },
                                    {
                                        data: "contract_zip_code",
                                        defaultContent: "N/A"
                                    },
                                    {
                                        data: "legal_us_work_eligibility",
                                        render: function(data) {
                                            return data === "Yes" ? "Yes" : "No";
                                        }
                                    },
                                    {
                                        data: "ssn1",
                                        defaultContent: "N/A"
                                    },
                                    {
                                        data: "gender1",
                                        defaultContent: "N/A"
                                    },
                                    {
                                        data: "dob",
                                        defaultContent: "N/A"
                                    },
                                    {
                                        data: "marital_status",
                                        defaultContent: "N/A"
                                    },
                                    {
                                        data: "contract_created_at",
                                        defaultContent: "N/A",
                                        render: function(data) {
                                            if (!data) return "N/A";
                                            const date = new Date(data);
                                            return date.toLocaleDateString('en-US', {
                                                weekday: 'short',
                                                year: 'numeric',
                                                month: 'short',
                                                day: 'numeric'
                                            });
                                        }
                                    }
                                ],
                                responsive: false
                            });

                            // Reload table on filter change
                            $('#ssn, #dob').on('change', function() {
                                table.ajax.reload();
                            });
                        });
                    </script>
 