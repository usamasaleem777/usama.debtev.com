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


if (isset($_GET['id'])) {
    $applicant_id = $_GET['id'];
} else {
    $applicant_id = 0;
}

if ($_SESSION['lang'] === 'es') {
    $applicant = DB::queryFirstRow(
        "SELECT a.*, 
        GROUP_CONCAT(p.position_name_es SEPARATOR ', ') AS position_names
        FROM applicants a
        LEFT JOIN positions p ON FIND_IN_SET(p.id, REPLACE(REPLACE(a.position, ' ', ''), ',,', ','))
        WHERE a.id = %i
        GROUP BY a.id",
        $applicant_id
    );
} else {
    $applicant = DB::queryFirstRow(
        "SELECT a.*, 
        GROUP_CONCAT(p.position_name SEPARATOR ', ') AS position_names
        FROM applicants a
        LEFT JOIN positions p ON FIND_IN_SET(p.id, REPLACE(REPLACE(a.position, ' ', ''), ',,', ','))
        WHERE a.id = %i
        GROUP BY a.id",
        $applicant_id
    );
}
// Handle NULL values in education data
$education = DB::queryFirstRow(
    "SELECT * FROM education WHERE applicant_id = %i",
    $applicant_id
);
if (!$education) {
    $education = [];
}

$criminal_history = DB::query(
    "SELECT * FROM criminal_history WHERE applicant_id = %i",
    $applicant_id
);
if (!$criminal_history) {
    $criminal_history = [];
}

$availability = DB::query(
    "SELECT * FROM availability WHERE applicant_id = %i",
    $applicant_id
);
if (!$availability) {
    $availability = [];
}

$statuses = DB::query(
    "SELECT id, title FROM status WHERE status ='1'"
);
if (!$statuses) {
    $statuses = [];
}


// Handle NULL values in other data
$employment_history = DB::query(
    "SELECT * FROM employment_history WHERE applicant_id = %i",
    $applicant_id
);
if (!$employment_history) {
    $employment_history = [];
}

$skills = DB::query(
    "SELECT skill_description FROM skills WHERE applicant_id = %i",
    $applicant_id
);
if (!$skills) {
    $skills = [];
}

// Debug the fetched data
$signature = DB::queryFirstRow(
    "SELECT * FROM application_signatures WHERE applicant_id = %i",
    $applicant_id
);
$references = DB::query(
    "SELECT * FROM references_info WHERE applicant_id = %i",
    $applicant_id
);
if (!$references) {
    $references = [];
}

if (isset($applicant['status'])) {
    $currentStatus = $applicant['status'];
} else {
    $currentStatus = 'Pending';
}

// Handle Hire Applicant form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hire'])) {
    if (isset($_POST['applicant_id'])) {
        $applicant_id = $_POST['applicant_id'];
    } else {
        $applicant_id = 0;
    }

    try {
        DB::startTransaction();

        $applicant = DB::queryFirstRow("SELECT * FROM applicants WHERE id = %i", $applicant_id);
        if (!$applicant) {
            throw new Exception("Applicant not found.");
        }

        $role = DB::queryFirstRow("SELECT id FROM roles WHERE name = 'employee'");
        if (!$role) {
            $role_id = (int) $role['0'];
        } else {
            $role_id = (int) $role['id'];
        }

        // Generate username
        $baseUsername = strtolower(preg_replace('/[^a-z]/i', '', $applicant['first_name'] . $applicant['last_name']));
        $username = $baseUsername;
        $counter = 1;
        while (DB::queryFirstRow("SELECT user_id FROM users WHERE user_name = %s", $username)) {
            $username = $baseUsername . $counter++;
        }

        // Create user record
        $userData = [
            'role_id' => $role_id,
            'user_name' => $username,
            'email' => $applicant['email'],
            'name' => $applicant['first_name'] . ' ' . $applicant['last_name'],
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if (isset($applicant['password'])) {
            $userData['password'] = $applicant['password'];
        }

        if (isset($applicant['phone_number'])) {
            $userData['phone'] = $applicant['phone_number'];
        }

        if (isset($applicant['picture'])) {
            $userData['picture'] = $applicant['picture'];
        }

        DB::insert('users', $userData);

        $new_user_id = DB::insertId();

        // Create record for user roles tabel
        DB::insert('user_roles', [
            'user_id' => $new_user_id,
            'role_id' => $role_id,
            'assigned_at' => date('Y-m-d H:i:s')
        ]);

        DB::update('applicants', ['status' => 'hired'], 'id = %i', $applicant_id);
        DB::commit();

        // Redirect to clear POST data
        echo '<script>window.location.href = window.location.href;</script>';
        exit();

    } catch (Exception $e) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => $e->getMessage()];
        echo '<script>window.location.href = window.location.href;</script>';
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $applicant_id = $_POST['applicant_id'];
    $newStatus = $_POST['new_status'];

    try {
        DB::update('applicants', ['status' => $newStatus], 'id = %i', $applicant_id);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Status updated successfully'];
        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Add this after existing POST handlers
// Update your existing link generation handler
// Update your existing link generation handler
//      echo '<script>window.location.href = window.location.href;</script>';
//     exit();


// }

?>
<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />



<style>
    /* Base Styles */
    :root {
        --primary-color: #FE5500;
        --secondary-color: #34495e;
        --accent-color: #3498db;
        --border-color: #e0e0e0;
        --text-color: #333;
        --light-text: #777;
        --background-color: #f9f9f9;
        --card-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    .btn {
        margin: 5px;
    }

    body {
        font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
        line-height: 1.6;
        color: var(--text-color);
        background-color: #f5f5f5;
        padding: 20px;
    }

    /* Applicant Profile Container */
    .applicant-profile {

        margin: 0 auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* Profile Header */
    .profile-header {
        padding: 25px 30px;
        background: linear-gradient(to right, #FE5500, #ffffff);
        color: white;
        border-bottom: 4px solid var(--accent-color);
    }


    .profile-header h1 {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .position-tag {
        display: inline-block;
        background: var(--accent-color);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }

    /* Profile Sections */
    .profile-sections {
        padding: 0 30px 30px;
    }

    .profile-section {
        margin-top: 30px;
        padding-bottom: 25px;
        border-bottom: 1px solid var(--border-color);
    }

    .profile-section:last-child {
        border-bottom: none;
    }

    .profile-section h2 {
        font-size: 20px;
        color: var(--primary-color);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .profile-section h2 i {
        color: var(--accent-color);
    }

    .subsection {
        margin-bottom: 20px;
    }


    .subsection h3 {
        font-size: 16px;
        color: var(--secondary-color);
        margin-bottom: 15px;
        padding-left: 10px;
        border-left: 3px solid var(--accent-color);
    }

    /* Section Grid Layout */
    .section-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }

    .info-item {
        margin-bottom: 10px;
    }

    .info-item .label {
        display: block;
        font-size: 13px;
        color: var(--light-text);
        margin-bottom: 3px;
        font-weight: 500;
    }

    .info-item .value {
        font-size: 15px;
        word-break: break-word;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    /* Lists */
    .skills-list {
        list-style-position: inside;
        columns: 2;
        column-gap: 30px;
    }

    .skills-list li {
        margin-bottom: 5px;
        break-inside: avoid;
    }

    /* References Grid */
    .references-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }

    .reference-card {
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 15px;
        background: var(--background-color);
    }

    .reference-card h3 {
        font-size: 16px;
        margin-bottom: 10px;
        color: var(--primary-color);
    }

    .reference-detail {
        margin-bottom: 5px;
        font-size: 14px;
    }

    .reference-detail .label {
        font-weight: 500;
        color: var(--light-text);
    }

    /* Job Cards */
    .job-card {
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 20px;
        margin-bottom: 20px;
        background: var(--background-color);
    }

    .profile-header h1 {
        color: #f5f5f5;
    }

    .job-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .job-header h3 {
        font-size: 17px;
        color: var(--primary-color);
    }

    .job-period {
        font-size: 14px;
        color: var(--light-text);
    }

    .job-title {
        font-style: italic;
        color: var(--secondary-color);
        margin-bottom: 15px;
        font-size: 15px;
    }

    .job-details {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 15px;
    }

    .detail-group {
        flex: 1;
        min-width: 200px;
    }

    .detail {
        margin-bottom: 8px;
        font-size: 14px;
    }

    .detail .label {
        font-weight: 500;
        color: var(--light-text);
    }

    .job-description {
        margin-top: 10px;
    }

    .job-description .label {
        font-weight: 500;
        color: var(--light-text);
        display: block;
        margin-bottom: 5px;
    }

    .job-description p {
        font-size: 14px;
        line-height: 1.5;
    }

    /* Signature */
    .signature-container {
        display: flex;
        align-items: center;
        gap: 30px;
    }

    .signature-image {
        width: 250px;
        height: 100px;
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
    }

    .signature-image img {
        max-width: 100%;
        max-height: 100%;
    }

    .no-signature {
        color: var(--light-text);
        font-style: italic;
    }

    .signature-date {
        font-size: 15px;
    }

    .signature-date .label {
        font-weight: 500;
        color: var(--light-text);
        margin-right: 5px;
    }

    /* No Data States */
    .no-data {
        color: var(--light-text);
        font-style: italic;
        font-size: 14px;
    }

    @media (max-width: 360) {
        .row1 {
            margin-left: -25px !important;
            margin-right: -25px !important;
        }

        .skip-print .btn,
        .skip-print .dropdown {
            width: 100% !important;
        }
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .page-header .breadcrumb {
            flex-direction: row;
            align-items: flex-end !important;
        }

        .row1 {
            margin-left: -25px !important;
            margin-right: -25px !important;
        }

        .breadcrumb {
            font-size: 12px;
            padding: 0.5rem 0;
            white-space: nowrap;
            overflow-x: auto;
            display: row;
            width: 100%;
        }

        .skip-print {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start !important;
        }

        .skip-print .btn,
        .skip-print .dropdown {
            width: 100%;
        }

        .dropdown-menu {
            width: 100%;
        }

        .profile-header {
            padding: 15px !important;
            background: #FE5500 !important;
        }

        .profile-header h1 {
            font-size: 22px !important;
            color: #f5f5f5
        }

        .position-tag {
            font-size: 12px !important;
        }

        .profile-sections {
            padding: 0 15px 15px !important;
        }

        .profile-section h2 {
            font-size: 18px !important;
        }

        .section-grid {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }

        .references-grid {
            grid-template-columns: 1fr !important;
        }

        .job-card {
            padding: 15px !important;
        }

        .job-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 5px !important;
        }

        .job-details {
            flex-direction: column !important;
            gap: 10px !important;
        }

        .detail-group {
            min-width: 100% !important;
        }

        .skills-list {
            columns: 1 !important;
        }

        .signature-container {
            flex-direction: column !important;
        }

        .signature-image {
            width: 100% !important;
        }
    }

    @media print {

        body,
        html {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            height: auto !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body * {
            visibility: hidden;
            margin: 0 !important;
            padding: 0 !important;
        }

        .applicant-profile,
        .applicant-profile * {
            visibility: visible;
        }

        @page {
            size: A4 portrait;
            margin: 0 !important;
        }

        .applicant-profile {
            position: absolute;
            left: 0;
            top: 0;
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
            overflow: hidden !important;
            background: white !important;
            font-size: 8pt !important;
            line-height: 1.2 !important;
        }

        * {
            max-width: 100% !important;
            box-sizing: border-box !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .section-grid,
        .employment-grid,
        .references-grid {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            /* Ensures four equal columns */
            gap: 6px !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .profile-section {
            grid-column: auto !important;
            margin-bottom: 4px !important;
            padding: 4px !important;
            page-break-inside: avoid !important;
            background: inherit !important;
            border: none !important;
        }

        .job-card,
        .reference-card {
            grid-column: span 1 !important;
            padding: 4px !important;
            margin: 2px 0 !important;
            border: 0.5px solid #ccc !important;
            background: white !important;
        }

        .job-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            font-size: 8.5pt !important;
            margin-bottom: 2px !important;
        }

        .job-header h3 {
            font-size: 9pt !important;
            margin: 0 !important;
            padding: 0 !important;
            white-space: nowrap !important;
            /* Prevents line breaks */
        }

        .job-period {
            font-size: 8pt !important;
        }

        .row {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            /* Ensures four equal columns */
            gap: 6px !important;
        }

        .job-description {
            font-size: 8pt !important;
            margin: 0 !important;
            padding: 2px !important;
        }

        .job-description .label {
            font-weight: bold !important;
        }

        .job-description .value {
            display: block !important;
            margin-top: 2px !important;
        }

        .info-item,
        .label,
        .value {
            margin: 0 !important;
            padding: 0 !important;
            font-size: 8pt !important;
        }

        h1 {
            font-size: 10pt !important;
        }

        h2 {
            font-size: 9pt !important;
        }

        h3 {
            font-size: 8.5pt !important;
        }

        .signature-container {
            display: block !important;
        }

        .signature-image {
            border: 2px solid #000 !important;
            background-color: transparent !important;
            padding: 8px !important;
            min-height: 40px !important;
            width: 200px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 5px 0 0 0 !important;
        }

        .signature-image img {
            max-height: 30px !important;
            width: auto !important;
        }

        .no-signature {
            color: #777 !important;
            font-style: italic !important;
        }

        .signature-date {
            display: none !important;
        }

        .no-print,
        .skip-print,
        .breadcrumb,
        .page-header,
        .dropdown,
        .btn,
        .modal,
        .print-controls {
            display: none !important;
        }

        html,
        body {
            break-inside: avoid !important;
            break-after: avoid !important;
            break-before: avoid !important;
        }
       
    }
     @media print and (-webkit-min-device-pixel-ratio:0) {
            .applicant-profile {
                padding-top: 0 !important;
            }

            body {
                padding-top: 0 !important;
            }
        }
</style>
<style>
    /* Base Styles */
    :root {
        --primary-color: #FE5500;
        --secondary-color: #34495e;
        --accent-color: #3498db;
        --border-color: #e0e0e0;
        --text-color: #333;
        --light-text: #777;
        --background-color: #f9f9f9;
        --card-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
        /* Base Styles */
        :root {
            --primary-color: #FE5500;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --border-color: #e0e0e0;
            --text-color: #333;
            --light-text: #777;
            --background-color: #f9f9f9;
            --card-shadow: 0 2px 4px rgba(43, 8, 8, 0.05);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #f5f5f5;
            padding: 20px;
        }
            body {
                font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
                line-height: 1.6;
                color: var(--text-color);
                background-color: #f5f5f5;
                padding: 20px;
            }

            /* Additional styles for notes section */
            .note-card {
                border-left: 4px solid #FE5500;
                transition: all 0.3s ease;
            }

            .note-card:hover {
                background-color: #f8f9fa;
            }

            .note-date {
                font-size: 0.8rem;
                color: #6c757d;
            }

            .empty-notes {
                padding: 20px;
                text-align: center;
                color: #6c757d;
            }

            /* Applicant Profile Container */
            .applicant-profile {

                margin: 0 auto;
                background: white;
                border-radius: 8px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }
                /* Applicant Profile Container */
                .applicant-profile {

                    margin: 0 auto;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                    overflow: hidden;
                }

                /* Profile Header */
                .profile-header {
                    padding: 25px 30px;
                    background: linear-gradient(to right, #FE5500, #ffffff);
                }
                    /* Profile Header */
                    .profile-header {
                        padding: 25px 30px;
                        background: linear-gradient(to right, #FE5500, #ffffff);
                        color: white;
                        border-bottom: 4px solid var(--accent-color);
                    }


                    .profile-header h1 {
                        font-size: 28px;
                        font-weight: 600;
                        margin-bottom: 5px;
                    }

                    .position-tag {
                        display: inline-block;
                        background: var(--accent-color);
                        padding: 5px 12px;
                        border-radius: 20px;
                        font-size: 14px;
                        font-weight: 500;
                    }

                    /* Profile Sections */
                    .profile-sections {
                        padding: 0 30px 30px;
                    }

                    .profile-section {
                        margin-top: 30px;
                        padding-bottom: 25px;
                        border-bottom: 1px solid var(--border-color);
                    }

                    .profile-section:last-child {
                        border-bottom: none;
                    }

                    .profile-section h2 {
                        font-size: 20px;
                        color: var(--primary-color);
                        margin-bottom: 20px;
                        display: flex;
                        align-items: center;
                        gap: 10px;
                    }

                    .profile-section h2 i {
                        color: var(--accent-color);
                    }

                    .subsection {
                        margin-bottom: 20px;
                    }

                    .subsection h3 {
                        font-size: 16px;
                        color: var(--secondary-color);
                        margin-bottom: 15px;
                        padding-left: 10px;
                        border-left: 3px solid var(--accent-color);
                    }

                    /* Section Grid Layout */
                    .section-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                        gap: 15px;
                    }

                    .info-item {
                        margin-bottom: 10px;
                    }

                    .info-item .label {
                        display: block;
                        font-size: 13px;
                        color: var(--light-text);
                        margin-bottom: 3px;
                        font-weight: 800;
                    }

                    .info-item .value {
                        font-size: 15px;
                        word-break: break-word;
                    }

                    .full-width {
                        grid-column: 1 / -1;
                    }

                    /* Lists */
                    .skills-list {
                        list-style-position: inside;
                        columns: 2;
                        column-gap: 30px;
                    }

                    .skills-list li {
                        margin-bottom: 5px;
                        break-inside: avoid;
                    }

                    /* References Grid */
                    .references-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                        gap: 15px;
                    }

                    .reference-card {
                        border: 1px solid var(--border-color);
                        border-radius: 6px;
                        padding: 15px;
                        background: var(--background-color);
                    }

                    .reference-card h3 {
                        font-size: 16px;
                        margin-bottom: 10px;
                        color: var(--primary-color);
                    }

                    .reference-detail {
                        margin-bottom: 5px;
                        font-size: 14px;
                    }

                    .reference-detail .label {
                        font-weight: 500;
                        color: var(--light-text);
                    }

                    /* Job Cards */
                    .job-card {
                        border: 1px solid var(--border-color);
                        border-radius: 6px;
                        padding: 20px;
                        margin-bottom: 20px;
                        background: var(--background-color);
                    }

                    .job-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 10px;
                    }

                    .job-header h3 {
                        font-size: 17px;
                        color: var(--primary-color);
                    }

                    .job-period {
                        font-size: 14px;
                        color: var(--light-text);
                    }

                    .job-title {
                        font-style: italic;
                        color: var(--secondary-color);
                        margin-bottom: 15px;
                        font-size: 15px;
                    }

                    .job-details {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 20px;
                        margin-bottom: 15px;
                    }

                    .detail-group {
                        flex: 1;
                        min-width: 200px;
                    }

                    .detail {
                        margin-bottom: 8px;
                        font-size: 14px;
                    }

                    .detail .label {
                        font-weight: 500;
                        color: var(--light-text);
                    }

                    .job-description {
                        margin-top: 10px;
                    }

                    .job-description .label {
                        font-weight: 500;
                        color: var(--light-text);
                        display: block;
                        margin-bottom: 5px;
                    }

                    .job-description p {
                        font-size: 14px;
                        line-height: 1.5;
                    }

                    /* Signature */
                    .signature-container {
                        display: flex;
                        align-items: center;
                        gap: 30px;
                    }

                    .signature-image {
                        width: 250px;
                        height: 100px;
                        border: 1px solid var(--border-color);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background: white;
                    }

                    .signature-image img {
                        max-width: 100%;
                        max-height: 100%;
                    }

                    .no-signature {
                        color: var(--light-text);
                        font-style: italic;
                    }

                    .signature-date {
                        font-size: 15px;
                    }

                    .signature-date .label {
                        font-weight: 500;
                        color: var(--light-text);
                        margin-right: 5px;
                    }

                    /* No Data States */
                    .no-data {
                        color: var(--light-text);
                        font-style: italic;
                        font-size: 14px;
                    }

                    /* Mobile Responsive Styles */
                    @media (max-width: 768px) {
                        .page-header {
                            flex-direction: column;
                            align-items: flex-start !important;
                        }

                        .breadcrumb {
                            font-size: 12px;
                            padding: 0.5rem 0;
                            white-space: nowrap;
                            overflow-x: auto;
                            display: block;
                            width: 100%;
                        }

                        .skip-print {
                            flex-direction: column;
                            gap: 10px;
                            align-items: flex-start !important;
                        }

                        .skip-print .btn,
                        .skip-print .dropdown {
                            width: 100%;
                        }

                        .dropdown-menu {
                            width: 100%;
                        }

                        .profile-header {
                            padding: 15px !important;
                            background: #FE5500 !important;
                        }

                        .profile-header h1 {
                            font-size: 22px !important;
                        }

                        .position-tag {
                            font-size: 12px !important;
                        }

                        .profile-sections {
                            padding: 0 15px 15px !important;
                        }

                        .profile-section h2 {
                            font-size: 18px !important;
                        }

                        .section-grid {
                            grid-template-columns: 1fr !important;
                            gap: 10px !important;
                        }

                        .references-grid {
                            grid-template-columns: 1fr !important;
                        }

                        .job-card {
                            padding: 15px !important;
                        }

                        .job-header {
                            flex-direction: column;
                            align-items: flex-start !important;
                            gap: 5px !important;
                        }

                        .job-details {
                            flex-direction: column !important;
                            gap: 10px !important;
                        }

                        .detail-group {
                            min-width: 100% !important;
                        }

                        .skills-list {
                            columns: 1 !important;
                        }

                        .signature-container {
                            flex-direction: column !important;
                        }

                        .signature-image {
                            width: 100% !important;
                        }
                    }

                    @media print {

                        body,
                        html {
                            margin: 0 !important;
                            padding: 0 !important;
                            width: 100% !important;
                            height: auto !important;
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }

                        body * {
                            visibility: hidden;
                            margin: 0 !important;
                            padding: 0 !important;
                        }

                        .applicant-profile,
                        .applicant-profile * {
                            visibility: visible;
                        }

                        @page {
                            size: A4 portrait;
                            margin: 0 !important;
                        }

                        .applicant-profile {
                            position: absolute;
                            left: 0;
                            top: 0;
                            width: 100% !important;
                            max-width: 100% !important;
                            height: auto !important;
                            min-height: 100vh !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            box-sizing: border-box;
                            overflow: hidden !important;
                            background: white !important;
                            font-size: 8pt !important;
                            line-height: 1.2 !important;
                        }

                        * {
                            max-width: 100% !important;
                            box-sizing: border-box !important;
                            margin-left: 0 !important;
                            margin-right: 0 !important;
                        }

                        .section-grid,
                        .employment-grid,
                        .references-grid {
                            display: grid !important;
                            grid-template-columns: repeat(4, 1fr) !important;
                            /* Ensures four equal columns */
                            gap: 6px !important;
                            margin: 0 !important;
                            padding: 0 !important;
                        }

                        .profile-section {
                            grid-column: auto !important;
                            margin-bottom: 4px !important;
                            padding: 4px !important;
                            page-break-inside: avoid !important;
                            background: inherit !important;
                            border: none !important;
                        }

                        .job-card,
                        .reference-card {
                            grid-column: span 1 !important;
                            padding: 4px !important;
                            margin: 2px 0 !important;
                            border: 0.5px solid #ccc !important;
                            background: white !important;
                        }

                        .job-header {
                            display: flex !important;
                            align-items: center !important;
                            justify-content: space-between !important;
                            font-size: 8.5pt !important;
                            margin-bottom: 2px !important;
                        }

                        .job-header h3 {
                            font-size: 9pt !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            white-space: nowrap !important;
                            /* Prevents line breaks */
                        }

                        .job-period {
                            font-size: 8pt !important;
                        }

                        .row {
                            display: grid !important;
                            grid-template-columns: repeat(4, 1fr) !important;
                            /* Ensures four equal columns */
                            gap: 6px !important;
                        }

                        .job-description {
                            font-size: 8pt !important;
                            margin: 0 !important;
                            padding: 2px !important;
                        }

                        .job-description .label {
                            font-weight: bold !important;
                        }

                        .job-description .value {
                            display: block !important;
                            margin-top: 2px !important;
                        }

                        .info-item,
                        .label,
                        .value {
                            margin: 0 !important;
                            padding: 0 !important;
                            font-size: 8pt !important;
                        }

                        h1 {
                            font-size: 10pt !important;
                        }

                        h2 {
                            font-size: 9pt !important;
                        }

                        h3 {
                            font-size: 8.5pt !important;
                        }

                        .signature-container {
                            display: block !important;
                        }

                        .signature-image {
                            border: 2px solid #000 !important;
                            background-color: transparent !important;
                            padding: 8px !important;
                            min-height: 40px !important;
                            width: 200px !important;
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                            margin: 5px 0 0 0 !important;
                        }

                        .signature-image img {
                            max-height: 30px !important;
                            width: auto !important;
                        }

                        .no-signature {
                            color: #777 !important;
                            font-style: italic !important;
                        }

                        .signature-date {
                            display: none !important;
                        }

                        .no-print,
                        .skip-print,
                        .breadcrumb,
                        .page-header,
                        .dropdown,
                        .btn,
                        .modal,
                        .print-controls {
                            display: none !important;
                        }

                        html,
                        body {
                            break-inside: avoid !important;
                            break-after: avoid !important;
                            break-before: avoid !important;
                        }

                        @media print and (-webkit-min-device-pixel-ratio:0) {
                            .applicant-profile {
                                padding-top: 0 !important;
                            }

                            body {
                                padding-top: 0 !important;
                            }
                        }
                    }
</style>
<!-- Content Header (Page header) -->
<div class="side-app">
    <!-- Page header with breadcrumb navigation -->
    <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
        <div class="head">
            <ol class="breadcrumb float-sm-right mt-2">
                <!-- Home breadcrumb -->
                <li class="breadcrumb-item">
                    <a href="index.php" style="color: #fe5500"><i
                            class="fas fa-home me-1"></i><?php echo lang("list_home"); ?></a>
                </li>
                <!-- Position breadcrumb -->
                <li class="breadcrumb-item">
                    <a href="#" style="color: #fe5500"><?php echo lang(key: "formview_applicants"); ?></a>
                </li>
                <!-- View position breadcrumb -->
                <li class="breadcrumb-item">
                    <a href="#" style="color: #fe5500"><?php echo lang("formview_applicants_data"); ?></a>
                </li>
            </ol>
        </div>
    </div>

    <div class="row1">
        <div class="page-header d-flex align-items-center justify-content-between mt-2">
            <h1 class="page-title skip-print"> <?php echo lang("formview_applicants_data"); ?></h1>
        </div>

        <div class="d-flex justify-content-end align-items-center skip-print mb-2">
            <?php if ($currentStatus === 'hired'): ?>
                <button class="btn btn-secondary" disabled style="min-width: 120px;">
                    <i class="fas fa-check-circle me-2"></i> <?php echo lang("list_hired"); ?>
                </button>
            <?php else: ?>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="statusDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 140px; background-color: <?php
                        $statusColors = [
                            'shortlisted' => '#28a745',
                            'rejected' => '#dc3545',
                            'pending' => '#ffc107',
                            'hired' => '#6c757d',
                        ];
                        echo $statusColors[$currentStatus] ?? '#6c757d';
                        ?>">
                        <?= ucfirst($currentStatus) ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                        <?php foreach ($statuses as $status): ?>
                            <li>
                                <a class="dropdown-item status-item" href="#"
                                    data-status="<?= htmlspecialchars($status['title']) ?>">
                                    <?= ucfirst(htmlspecialchars($status['title'])) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <form method="post" id="hireForm" style="display: none;">
                    <input type="hidden" name="applicant_id" value="<?php if (isset($applicant['id'])) {
                        echo $applicant['id'];
                    } ?>">
                    <input type="hidden" name="hire">
                </form>
            <?php endif; ?>
            <!-- Print Button -->
            <button id="printBtn" class="btn no-print "
                style="background-color: #0000FF; color: white; min-width: 120px;">
                <i class="fas fa-print"></i>
                <?php echo lang('print_form'); ?>
            </button>
            <!-- Generate Link Button triggering modal -->
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#generateLinkModal"
                style="background-color: #008000; color:white">
                <i class="fas fa-link me-2"></i><?php echo lang("generate_link"); ?>
            </button>
            <!-- Send Packet Button -->
<button type="button" class="btn btn-info" id="sendPacketBtn" 
    style="background-color: #6f42c1; color:white">
    <i class="fas fa-paper-plane me-2"></i><?php echo lang("leave_Send_Packet"); ?>
</button>
        </div>

        <div class="applicant-profile">
            <!-- Applicant Header -->
            <div class="profile-header">
                <h1>
                    <?php echo htmlspecialchars($applicant['first_name'] ?? '') . ' ' .
                        htmlspecialchars($applicant['middle_initial'] ?? '') . ' ' .
                        htmlspecialchars($applicant['last_name'] ?? ''); ?>
                </h1>
                <div class="position-tag">
                    <?php echo htmlspecialchars($applicant['position_names'] ?? 'Position not specified'); ?>
                </div>
            </div>

            <!-- Main Content Sections -->
            <div class="profile-sections">
                <!-- Contact Information -->
                <section class="profile-section">
                    <h2><i class="fas fa-user"></i> <?php echo lang("form_personal_info"); ?></h2>
                    <div class="section-grid">
                        <div class="info-item">
                            <span class="label"><?php echo lang("formview_degree"); ?></span>
                            <span class="value">
                                <?php echo htmlspecialchars($applicant['street_address'] ?? 'N/A'); ?>,
                                <?php echo htmlspecialchars($applicant['city'] ?? ''); ?>
                                <?php echo htmlspecialchars($applicant['zip'] ?? ''); ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label"><?php echo lang("formview_phone"); ?></span>
                            <span
                                class="value"><?php echo htmlspecialchars($applicant['phone_number'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label"><?php echo lang("formview_email"); ?></span>
                            <span class="value"><?php echo htmlspecialchars($applicant['email'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label"><?php echo lang("form_Work_Eligibility"); ?>:</span>
                            <span
                                class="value"><?php echo ($applicant['legal_us_work_eligibility'] ?? false) ? 'Yes' : 'No'; ?></span>
                        </div>

                        <div class="info-item">
                            <span class="label"><?php echo lang("formview_dob"); ?></span>
                            <span
                                class="value"><?php echo htmlspecialchars($applicant['dob'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label"><?php echo lang("Gender"); ?></span>
                            <span
                                class="value"><?php echo htmlspecialchars($applicant['gender'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                </section>

                <!-- Job Information -->
                <section class="profile-section">
                    <h2><i class="fas fa-briefcase"></i> <?php echo lang("form_Job_Information"); ?></h2>
                    <div class="section-grid">
                        <div class="info-item">
                            <span class="label"><?php echo lang("form_position"); ?>:</span>
                            <span
                                class="value"><?php echo htmlspecialchars($applicant['position_names'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label"><?php echo lang("formview_salary"); ?></span>
                            <span class="value"><?php echo htmlspecialchars($applicant['salary'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label"><?php echo lang("formview_Employment_Type:"); ?></span>
                            <span
                                class="value"><?php echo htmlspecialchars($applicant['employment_type'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label"><?php echo lang("formview_Start_Date:"); ?></span>
                            <span
                                class="value"><?php echo htmlspecialchars($applicant['available_start_date'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label"></span> <?php echo lang("formview_over_18_Years_Old") ?></span><br>
                                <span class="value"><?php echo ($applicant['over_18'] ?? false) ? 'Yes' : 'No'; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label"><?php echo lang("formview_ID/Passport:"); ?></span>
                            <span
                                class="value"><?php echo ($applicant['passport_or_id'] ?? false) ? 'Yes' : 'No'; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label"><?php echo lang("form_availability_schedule_requests"); ?></span>
                            <span class="value">
                                <?php if (isset($availability[0]['special_requests'])) {
                                    echo nl2br(htmlspecialchars($availability[0]['special_requests']));
                                } else {
                                    echo 'N/A';
                                } ?>
                            </span>
                        </div>
                    </div>
                </section>

                <!-- Education -->
                <section class="profile-section">
                    <h2><i class="fas fa-graduation-cap"></i> <?php echo lang("form_education"); ?></h2>

                    <div class="subsection">
                        <h3><?php echo lang("formview_high_school"); ?></h3>
                        <div class="section-grid">
                            <div class="info-item">
                                <span class="label"><?php echo lang("formview_high_school_1"); ?></span>
                                <span
                                    class="value"><?php echo htmlspecialchars($education['high_school_name'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label"><?php echo lang("formview_location"); ?></span>
                                <span
                                    class="value"><?php echo htmlspecialchars($education['high_school_city'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label"><?php echo lang("formview_graduated"); ?></span>
                                <span
                                    class="value"><?php echo ($education['high_school_graduate'] ?? false) ? 'Yes' : 'No'; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label"><?php echo lang("form_GED"); ?></span>
                                <span class="value"><?php echo ($education['ged'] ?? false) ? 'Yes' : 'No'; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="subsection">
                        <h3><?php echo lang("formview_college"); ?></h3>
                        <div class="section-grid">
                            <div class="info-item">
                                <span class="label"><?php echo lang("formview_high_college_1"); ?></span>
                                <span
                                    class="value"><?php echo htmlspecialchars($education['college_name'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label"><?php echo lang("formview_location"); ?></span>
                                <span
                                    class="value"><?php echo htmlspecialchars($education['college_city'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label"><?php echo lang("formview_graduated"); ?></span>
                                <span
                                    class="value"><?php echo ($education['college_graduate'] ?? false) ? 'Yes' : 'No'; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label"><?php echo lang("formview_degree"); ?></span>
                                <span
                                    class="value"><?php echo htmlspecialchars($education['college_degree'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label"><?php echo lang("formview_currently_enrolled"); ?></span>
                                <span
                                    class="value"><?php echo htmlspecialchars($education['college_degree'] ?? false) ? 'Yes' : 'No'; ?></span>
                            </div>
                            <?php if (isset($education['currently_enrolled']) && $education['currently_enrolled'] == 1): ?>
                                <div class="info-item">
                                    <span class="label"><?php echo lang("formview_current_school"); ?></span>
                                    <span class="value">
                                        <?php echo isset($education['enrolled_school_name']) ? htmlspecialchars($education['enrolled_school_name']) : 'N/A'; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <!-- Skills -->
                <section class="profile-section">
                    <h2><i class="fas fa-tools"></i> <?php echo lang("admin_position"); ?></h2>
                    <?php if (!empty($skills)): ?>
                        <ul class="skills-list">
                            <?php foreach ($skills as $skill): ?>
                                <li><?php echo htmlspecialchars($skill['skill_description'] ?? 'Unknown skill'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-data"><?php echo lang("formview_no_skill_listed"); ?></p>
                    <?php endif; ?>
                </section>

                <!-- Criminal History -->
                <section class="profile-section">
                    <h2><i class="fas fa-gavel"></i> <?php echo lang("form_criminal_history"); ?></h2>
                    <?php if (!empty($criminal_history)): ?>
                        <?php foreach ($criminal_history as $record): ?>
                            <div class="section-grid">
                                <div class="info-item">
                                    <span class="label"><?php echo lang("formview_has_convictions"); ?></span>
                                    <span
                                        class="value"><?php echo ($record['has_conviction'] ?? false) ? 'Yes' : 'No'; ?></span>
                                </div>
                                <?php if ($record['formview_conviction_date'] ?? false): ?>
                                    <div class="info-item">
                                        <span class="label"><?php echo lang("formview_phone"); ?></span>
                                        <span
                                            class="value"><?php echo isset($record['conviction_date']) ? date('M Y', strtotime($record['conviction_date'])) : 'N/A'; ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label"><?php echo lang("formview_location"); ?></span>
                                        <span
                                            class="value"><?php echo htmlspecialchars($record['conviction_location'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="info-item full-width">
                                        <span class="label"><?php echo lang("formview_convicted_when:"); ?></span>
                                        <span
                                            class="value"><?php echo nl2br(htmlspecialchars($record['convicted_when'] ?? 'Not specified')); ?></span>
                                    </div>
                                    <div class="info-item full-width">
                                        <span class="label"><?php echo lang("formview_Convicted_Where:"); ?></span>
                                        <span
                                            class="value"><?php echo nl2br(htmlspecialchars($record['convicted_where'] ?? 'Not specified')); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data"><?php echo lang("formview_no_criminal_history_recorded") ?></p>
                    <?php endif; ?>
                </section>

                <!-- References -->
                <section class="profile-section">
                    <h2><i class="fas fa-address-book"></i> <?php echo lang("formview_references"); ?></h2>
                    <?php if (!empty($references)): ?>
                        <div class="references-grid">
                            <?php foreach ($references as $ref): ?>
                                <div class="reference-card">
                                    <h3><?php echo htmlspecialchars($ref['name'] ?? 'N/A'); ?></h3>
                                    <div class="reference-detail">
                                        <span class="label"><?php echo lang("formview_Relation:"); ?></span>
                                        <span><?php echo htmlspecialchars($ref['relationship_duration'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="reference-detail">
                                        <span class="label"><?php echo lang("formview_phone"); ?></span>
                                        <span><?php echo htmlspecialchars($ref['phone_number'] ?? 'N/A'); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-data"><?php echo lang("formview_no_references_provided") ?></p>
                    <?php endif; ?>
                </section>

                 <!-- Refered by -->
                <!-- <section class="profile-section">
                    <h2><i class="fas fa-user-friends"></i> <?php echo lang("form_GotToKnow"); ?></h2>
                        <div class="references-grid">
                                <div class="reference-card">
                                    <h3><?php echo htmlspecialchars($applicant['reference'] ?? 'N/A'); ?></h3>
                                </div>
                        </div>
                </section> -->
                <!-- Referred By -->
<section class="profile-section">
    <h2><i class="fas fa-user-friends"></i> <?php echo lang("form_GotToKnow"); ?></h2>
    <div class="references-grid">
        
        <!-- Reference Check (Yes/No) -->
        <div class="reference-card">
            <h3><?php echo htmlspecialchars($applicant['reference_check'] ?? 'N/A'); ?></h3>
        </div>

        <!-- Name of Person -->
        <div class="reference-card">
            <h3><?php echo htmlspecialchars($applicant['refrence_name'] ?? 'N/A'); ?></h3>
            <p>Name of Referrer</p>
        </div>

        <!-- Contact Info -->
        <div class="reference-card">
            <h3><?php echo htmlspecialchars($applicant['referred_contact_info'] ?? 'N/A'); ?></h3>
            <p>Contact Info</p>
        </div>

    </div>
</section>


                <!-- Employment History -->

                <section class="profile-section">
                    <h2><i class="fas fa-building"></i> <?php echo lang("formview_Employment_History"); ?></h2>
                    <?php if (!empty($employment_history)): ?>
                        <?php foreach ($employment_history as $job): ?>
                            <div class="job-card">
                                <div class="job-header">
                                    <h3><?php echo htmlspecialchars($job['employer_name'] ?? 'N/A'); ?></h3>
                                    <div class="job-period">
                                        <?php echo isset($job['from_date']) ? date('M Y', strtotime($job['from_date'])) : 'N/A'; ?>
                                        -
                                        <?php echo isset($job['to_date']) ? date('M Y', strtotime($job['to_date'])) : 'Present'; ?>
                                    </div>
                                </div>

                                <div class="job-title"><?php echo htmlspecialchars($job['job_title'] ?? 'N/A'); ?></div>

                                <!-- First Row - 4 columns -->
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="job-description">
                                            <span class="label"><?php echo lang("form_duties"); ?>:</span>
                                            <p class="value">
                                                <?php echo isset($job['duties']) ? nl2br(htmlspecialchars($job['duties'])) : 'N/A'; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="job-description">
                                            <span class="label">City:</span>
                                            <p class="value">
                                                <?php echo isset($job['city']) ? htmlspecialchars($job['city']) : 'N/A'; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="job-description">
                                            <span class="label">State:</span>
                                            <p class="value">
                                                <?php echo isset($job['state']) ? htmlspecialchars($job['state']) : 'N/A'; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="job-description">
                                            <span class="label">Zip Code:</span>
                                            <p class="value">
                                                <?php echo isset($job['zip_code']) ? htmlspecialchars($job['zip_code']) : 'N/A'; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Second Row - 4 columns -->
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="job-description">
                                            <span class="label"><?php echo lang("form_start_pay"); ?>:</span>
                                            <p class="value">
                                                <?php echo isset($job['starting_pay']) ? '$' . htmlspecialchars($job['starting_pay']) : 'N/A'; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="job-description">
                                            <span class="label"><?php echo lang("form_end_pay"); ?>:</span>
                                            <p class="value">
                                                <?php echo isset($job['ending_pay']) ? '$' . htmlspecialchars($job['ending_pay']) : 'N/A'; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="job-description">
                                            <span class="label">Wage/Salary Desired:</span>
                                            <p class="value">
                                                <?php echo isset($job['hourly_pay']) ? '$' . htmlspecialchars($job['hourly_pay']) : 'N/A'; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="job-description">
                                            <span class="label"><?php echo lang("form_supervisor"); ?>:</span>
                                            <p class="value"><?php echo htmlspecialchars($job['supervisor_name'] ?? 'N/A'); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Third Row - 4 columns -->
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="job-description">
                                            <span class="label">Telephone:</span>
                                            <p class="value"><?php echo htmlspecialchars($job['supervisor_phone'] ?? 'N/A'); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="job-description">
                                            <span class="label"><?php echo lang("form_reason_living"); ?>:</span>
                                            <p class="value">
                                                <?php echo nl2br(htmlspecialchars($job['reason_for_leaving'] ?? 'N/A')); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Empty space for alignment -->
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data"><?php echo lang("formview_no_employment_history_available"); ?></p>
                    <?php endif; ?>
                </section>

                <!-- Signature -->
                <section class="profile-section">
                    <h2><i class="fas fa-signature"></i> <?php echo lang("formview_applicaion_signature") ?></h2>
                    <div class="signature-container">
                        <div class="signature-image">
                            <?php if (!empty($signature['signature']) && strpos($signature['signature'], 'data:image/png') === 0): ?>
                                <img src="<?php echo htmlspecialchars($signature['signature']); ?>"
                                    alt="Applicant Signature" style="background: transparent;">
                            <?php else: ?>
                                <div class="no-signature"><?php echo lang("formview_no_signature_found"); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="signature-date">
                            <span class="label">Date:</span>
                            <span><?php echo htmlspecialchars($applicant['signature_date'] ?? date('m/d/Y')); ?></span>
                        </div>
                    </div>
                </section>
            </div>
        </div>

    </div>
    <!-- Notes section -->
    <div class="row1">
        <div class="col-md-12 mt-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header text-white" style="background-color: #FE5500; color: white;">
                            <h5 class="card-title mb-0" style="color:rgb(255, 255, 255);"><i
                                    class="fas fa-edit me-2"></i><?php echo lang("formview_add_notes"); ?></h5>
                        </div>
                        <div class="card-body">
                            <form id="noteForm" method="post">
                                <input type="hidden" name="applicant_id"
                                    value="<?= htmlspecialchars($applicant_id ?? '') ?>">
                                <input type="hidden" name="note_id" id="noteId" value="">
                                <div class="form-group mb-3">
                                    <textarea name="note_text" id="noteText" class="form-control"
                                        placeholder="<?php echo lang("formview_Enter_your note here..."); ?>" rows="3"
                                        style="resize: none;" required></textarea>
                                </div>
                                <button type="submit" name="save_note" class="btn text-white"
                                    style="background-color: #FE5500; color: white;">
                                    <i class="fas fa-save me-1"></i> <span
                                        id="saveButtonText"><?php echo lang("formview_save_notes"); ?></span>
                                </button>
                                <button type="button" id="cancelEdit" class="btn btn-secondary" style="display: none;">
                                    <i class="fas fa-times me-1"></i> <?php echo lang("formview_cancel"); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header text-white" style="background-color: #FE5500;">
                            <h5 class="card-title mb-0" style="color:rgb(255, 255, 255);"><i
                                    class="fas fa-sticky-note me-2"></i><?php echo lang("formview_applicants_notes"); ?>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div id="notesContainer" style="max-height: 400px; overflow-y: auto;">
                                <?php
                                // Initialize variables
                                $notes = [];

                                // Check if applicant_id is valid
                                if (empty($applicant_id) || !is_numeric($applicant_id)) {
                                    echo '<div class="alert alert-danger m-3">Invalid applicant ID</div>';
                                } else {
                                    // Display notes
                                    try {
                                        $notes = DB::query("SELECT * FROM applicant_notes WHERE applicant_id = %i ORDER BY created_at DESC", $applicant_id);

                                        if (count($notes) > 0) {
                                            foreach ($notes as $note) {
                                                echo '
                <div class="note-card p-3 border-bottom" id="note-' . (int) $note['id'] . '">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <p class="mb-1 note-text">' . htmlspecialchars($note['note_text']) . '</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">' . htmlspecialchars(date('M j, Y g:i a', strtotime($note['created_at']))) . '</small>
                        <div>
                            <a href="#" class="btn btn-sm text-white edit-note me-1" style="background-color: #FE5500;" data-note-id="' . (int) $note['id'] . '" data-applicant-id="' . (int) $applicant_id . '">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="btn btn-sm text-white delete-note" style="background-color: #FE5500;" data-note-id="' . (int) $note['id'] . '" data-applicant-id="' . (int) $applicant_id . '">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>';
                                            }
                                        } else {
                                            echo '
            <div class="text-center py-4 text-muted" id="noNotesMessage">
                <i class="fas fa-sticky-note fa-2x mb-3"></i>
                <p>No notes found for this applicant</p>
            </div>';
                                        }
                                    } catch (Exception $e) {
                                        echo '<div class="alert alert-danger m-3">Error loading notes: ' . htmlspecialchars($e->getMessage()) . '</div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Close notes -->
    </div>
</div>



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
                    <input type="hidden" name="applicant_id" value="<?= $applicant_id ?>">
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
                                    <input type="checkbox" name="form_step[]" value="7" >
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
                    <button class="btn text-white" type="button" id="copyLinkButton" style="background-color: #fe5500;">
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
<!-- Send Packet Modal -->
<div class="modal fade" id="sendPacketModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #6f42c1;">
                <h5 class="modal-title"><?php echo lang("leave_Send_Packet"); ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="sendPacketForm">
                    <input type="hidden" name="applicant_id" value="<?= $applicant_id ?>">
                    <div class="mb-3">
                        <label><?php echo lang("form_select_forms"); ?></label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="form_step[]" value="3" id="formW4" checked>
                            <label class="form-check-label" for="formW4"><?php echo lang("leave_W4_Data"); ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="form_step[]" value="4" id="formQuickBook" checked>
                            <label class="form-check-label" for="formQuickBook"><?php echo lang("leave_Quick_Book"); ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="form_step[]" value="5" id="formEGV" checked>
                            <label class="form-check-label" for="formEGV"><?php echo lang("leave_EGV"); ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="form_step[]" value="6" id="formMVR" checked>
                            <label class="form-check-label" for="formMVR"><?php echo lang("leave_MVR_Information"); ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="form_step[]" value="7" id="formNonCompete">
                            <label class="form-check-label" for="formNonCompete"><?php echo lang("leave_NON_COMPETE_AGREEMENT"); ?></label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="kioskId"><?php echo lang("form_kiosk_id"); ?></label>
                        <input type="text" class="form-control" name="kiosk_id" id="kioskId" placeholder="<?php echo lang("form_enter_kiosk_id"); ?>" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo lang("cancel"); ?></button>
                <button type="button" class="btn text-white" id="confirmSendPacket" style="background-color: #6f42c1;">
                    <?php echo lang("form_send_packet"); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Add SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Add SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        $('#printBtn').on('click', function () {
            var $target = $('.applicant-profile');

            // Clone the target element and its children
            var $clone = $target.clone(true);

            // Add a print-specific class to the cloned element
            $clone.addClass('print-version');

            // Create a temporary container for printing
            var $printContainer = $('<div class="print-area"></div>').append($clone);

            // Append to body
            $('body').append($printContainer);

            // Trigger print
            window.print();

            // Remove the temporary print container after print
            $printContainer.remove();
        });
    });
    document.querySelectorAll('.status-item').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const status = this.dataset.status;
            const applicantId = <?= $applicant['id'] ?>;

            if (status === 'hired') {
                // SweetAlert2 confirmation only when clicking the "hire" button
                Swal.fire({
                    title: 'Do you want to hire this applicant?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#FF5500',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, hire them!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('hireForm').submit();  // Submit the form if confirmed
                    }
                });
            } else {
                // For other status changes, just update the status without a confirmation
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        applicant_id: applicantId,
                        update_status: 1,
                        new_status: status
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Error: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.location.reload();
                    });
            }
        });
    });

</script>


<script>
    $('#generateLinkForm').submit(function (e) {
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
            success: function (response) {
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
            error: function (xhr) {
                Swal.fire('Error', 'Request failed: ' + xhr.statusText, 'error');
            }
        });
    });

    $('#generateLinkModal').on('show.bs.modal', function () {
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
    $('#copyLinkButton').click(function () {
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
    $('#generateLinkModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
    });

    $('#generatedLinkModal').on('hidden.bs.modal', function () {
        $('#generatedLinkInput').val('');
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Handle note submission
        $('#noteForm').on('submit', function (e) {
            e.preventDefault();

            var formData = $(this).serialize();
            var noteText = $('#noteText').val().trim();
            var noteId = $('#noteId').val();
            var url = noteId ? 'ajax_helpers/update_note.php' : 'ajax_helpers/save_note.php';

            if (!noteText) {
                showAlert('error', 'Error', '<?php echo lang("formview_Note_text_cannot_be_empty"); ?> ');
                return;
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        if (noteId) {
                            // Update existing note in DOM
                            $('#note-' + noteId + ' .note-text').text(response.note.note_text);
                            resetForm();
                            showAlert('success', '<?php echo lang("formview_Success"); ?>', '<?php echo lang("formview_Note_updated_successfully!"); ?>');
                        } else {
                            // Add new note to DOM
                            addNoteToDOM(response.note);
                            $('#noteText').val('');
                            showAlert('success', '<?php echo lang("formview_Success"); ?>', '<?php echo lang("formview_Note_saved_successfully!"); ?>');
                        }
                    } else {
                        showAlert('error', 'Error', response.error || '<?php echo lang("formview_Error_saving_note"); ?>');
                    }
                },
                error: function (xhr, status, error) {
                    showAlert('error', 'Error', '<?php echo lang("formview_Error_saving_note:"); ?> ' + error);
                }
            });
        });

        // Handle note editing
        $(document).on('click', '.edit-note', function (e) {
            e.preventDefault();

            var noteId = $(this).data('note-id');
            var noteText = $('#note-' + noteId + ' .note-text').text();

            // Fill the form with note data
            $('#noteId').val(noteId);
            $('#noteText').val(noteText).focus();
            $('#saveButtonText').text('<?php echo lang("formview_update_note"); ?>');
            $('#cancelEdit').show();
        });

        // Handle cancel edit
        $('#cancelEdit').on('click', function () {
            resetForm();
        });

        // Handle note deletion
        $(document).on('click', '.delete-note', function (e) {
            e.preventDefault();

            var $noteElement = $(this).closest('.note-card');
            var noteId = $(this).data('note-id');
            var applicantId = $(this).data('applicant-id');

            Swal.fire({
                title: '<?php echo lang("formview_Are_you_sure?"); ?> ',
                text: "<?php echo lang("formview_You won't be able to revert this!"); ?> ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FE5500',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<?php echo lang("formview_Yes,_delete_it!"); ?> '
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'ajax_helpers/delete_note.php',
                        type: 'POST',
                        data: { note_id: noteId, applicant_id: applicantId },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                $noteElement.remove();
                                checkEmptyNotes();
                                showAlert('<?php echo lang("formview_success"); ?>', '<?php echo lang("formview_Deleted!"); ?>', '<?php echo lang("formview_Note_deleted_successfully!"); ?> ');
                            } else {
                                showAlert('error', 'Error', response.error || '<?php echo lang("formview_Error_deleting_note"); ?> ');
                            }
                        },
                        error: function (xhr, status, error) {
                            showAlert('error', 'Error', '<?php echo lang("formview_Error_deleting_note"); ?>  ' + error);
                        }
                    });
                }
            });
        });

        // Helper functions
        function resetForm() {
            $('#noteId').val('');
            $('#noteText').val('');
            $('#saveButtonText').text('<?php echo lang("formview_save_notes"); ?>');
            $('#cancelEdit').hide();
        }

        function addNoteToDOM(note) {
            $('#noNotesMessage').remove();
            var newNote = `
            <div class="note-card p-3 border-bottom" id="note-${note.id}">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <p class="mb-1 note-text">${escapeHtml(note.note_text)}</p>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">${note.created_at}</small>
                    <div>
                        <a href="#" class="btn btn-sm text-white edit-note me-1" style="background-color: #FE5500;" 
                           data-note-id="${note.id}" 
                           data-applicant-id="${note.applicant_id}">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="btn btn-sm text-white delete-note" style="background-color: #FE5500;" 
                           data-note-id="${note.id}" 
                           data-applicant-id="${note.applicant_id}">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                </div>
            </div>`;
            $('#notesContainer').prepend(newNote);
        }

        function checkEmptyNotes() {
            if ($('.note-card').length === 0) {
                $('#notesContainer').html(`
                <div class="text-center py-4 text-muted" id="noNotesMessage">
                    <i class="fas fa-sticky-note fa-2x mb-3"></i>
                    <p>No notes found for this applicant</p>
                </div>`);
            }
        }

        function showAlert(icon, title, text) {
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                confirmButtonColor: '#FE5500',
                timer: 3000
            });
        }

        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    });

</script>
<script>
    // Send Packet Functionality
$(document).ready(function() {
    // Show modal when button clicked
    $('#sendPacketBtn').click(function() {
        $('#sendPacketModal').modal('show');
    });

    // Handle form submission
    $('#confirmSendPacket').click(function() {
        const kioskId = $('#kioskId').val();
        if (!kioskId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a KIOSK ID',
                confirmButtonColor: '#FE5500'
            });
            return;
        }

        const formData = $('#sendPacketForm').serialize();
        
        // Show loading state
        const $button = $(this);
        $button.html('<i class="fas fa-spinner fa-spin"></i> Sending...').prop('disabled', true);

        $.ajax({
            url: 'ajax_helpers/ajax_send_packet_mail.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                $button.html('<?php echo lang("form_send_packet"); ?>').prop('disabled', false);
                if (response.status == 200) {
                    Swal.fire({
                        title: "Success",
                        text: response.message,
                        icon: "success",
                        confirmButtonColor: "#FE5500"
                    }).then(() => {
                        $('#sendPacketModal').modal('hide');
                        $('#sendPacketForm')[0].reset();
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: response.message,
                        icon: "error",
                        confirmButtonColor: "#FE5500"
                    });
                }
            },
            error: function(xhr) {
                $button.html('<?php echo lang("form_send_packet"); ?>').prop('disabled', false);
                Swal.fire({
                    title: "Error",
                    text: "Failed to send packet. Please try again.",
                    icon: "error",
                    confirmButtonColor: "#FE5500"
                });
            }
        });
    });
});
</script>