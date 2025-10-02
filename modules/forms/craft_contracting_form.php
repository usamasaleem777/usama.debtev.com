<?php
if (isset($_GET['id'])) {
    $applicant_id = $_GET['id'];
} else {
    $applicant_id = 0;
}
$applicant = DB::queryFirstRow(
    "SELECT a.* 
    FROM applicants a
    WHERE a.id = %i",
    $applicant_id
);
$signature = DB::queryFirstRow(
    "SELECT * FROM application_signatures WHERE applicant_id = %i",
    $applicant_id
) ?? [];
$newApplicants = DB::query("
    SELECT applicants.*, positions.position_name
    FROM applicants 
    LEFT JOIN positions ON applicants.position = positions.id
    LIMIT 7
");
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Craft Hiring Form</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery (from CDN) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Default state - black */
        .form-check-input {
            border: 2px solid #000 !important;
            background-color: transparent !important;
        }
    </style>
    <style>
        input,
        textarea,
        select {
            box-shadow: unset !important;
        }

        .btn-primary {
            background-color: #FF5500;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: unset;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #E64A00;
            transform: scale(1.05);
        }

        .btn-primary:active {
            background-color: #CC3D00;
            transform: scale(0.95);
        }
    </style>
    <!--START css-->
    <style type="text/css">
        .nd_options_navigation_2 div>ul {
            list-style: none;
            margin: 0px;
            padding: 0px;
            text-align: right;
        }

        .nd_options_navigation_2 div>ul>li {
            display: inline-block;
            padding: 0px;
        }

        .nd_options_navigation_2 div>ul>li:after {
            content: "|";
            display: inline-block;
            margin: 0px 20px;
            color: #000000;
        }

        .nd_options_navigation_2 div>ul>li:last-child:after {
            content: "";
            margin: 0px;
        }

        .nd_options_navigation_2 div li a {
            color: #000000;
            font-size: 16px;
            line-height: 16px;
            font-family: Roboto;
        }

        .nd_options_navigation_2 div>ul li:hover>ul.sub-menu {
            display: block;
        }

        .nd_options_navigation_2 div>ul li>ul.sub-menu {
            z-index: 999;
            position: absolute;
            margin: 0px;
            padding: 0px;
            list-style: none;
            display: none;
            margin-left: -20px;
            padding-top: 20px;
            width: 190px;
        }

        .nd_options_navigation_2 div>ul li>ul.sub-menu>li {
            padding: 15px 20px;
            border-bottom: 1px solid #f1f1f1;
            text-align: left;
            background-color: #fff;
            position: relative;
            box-shadow: 0px 2px 5px #f1f1f1;
            float: left;
            width: 100%;
            box-sizing: border-box;
        }

        .nd_options_navigation_2 div>ul li>ul.sub-menu>li:hover {
            background-color: #f9f9f9;
        }

        .nd_options_navigation_2 div>ul li>ul.sub-menu>li:last-child {
            border-bottom: 0px solid #000;
        }

        .nd_options_navigation_2 div>ul li>ul.sub-menu li a {
            font-size: 14px;
            color: #000000;
            float: left;
            width: 100%;
        }

        .nd_options_navigation_2 div>ul li>ul.sub-menu li>ul.sub-menu {
            margin-left: 170px;
            top: 0;
            padding-top: 0;
            padding-left: 20px;
        }

        /*responsive*/
        .nd_options_navigation_2_sidebar div>ul {
            list-style: none;
            margin: 0px;
            padding: 0px;
        }

        .nd_options_navigation_2_sidebar div>ul>li {
            display: inline-block;
            width: 100%;
            padding: 0px 0px 20px 0px;
        }

        .nd_options_navigation_2_sidebar div li a {
            font-family: Roboto;
        }

        .nd_options_navigation_2_sidebar div li>a {
            padding: 10px 0px;
            display: inline-block;
            font-size: 24px;
            font-family: Roboto;
            text-transform: lowercase;
            color: #fff;
        }

        .nd_options_navigation_2_sidebar div li>a::first-letter {
            text-transform: uppercase;
        }

        .nd_options_navigation_2_sidebar div>ul li>ul.sub-menu {
            margin: 0px;
            padding: 0px;
            list-style: none;
        }

        .nd_options_navigation_2_sidebar div>ul li>ul.sub-menu>li {
            padding: 0px 20px;
            text-align: left;
        }

        .nd_options_navigation_2_sidebar div>ul li>ul.sub-menu li a {
            font-size: 14px;
        }

        .nd_options_navigation_2_sidebar_content li.nd_options_customizer_labels_color_new {
            padding: 0px !important;
            background-color: transparent !important;
        }

        /*top header*/
        .nd_options_navigation_top_header_2 {
            font-size: 13px;
            line-height: 18px;
        }

        .nd_options_navigation_top_header_2>ul {
            list-style: none;
            margin: 0px;
            padding: 0px;
        }

        .nd_options_navigation_top_header_2>ul>li {
            display: inline-block;
        }

        .nd_options_navigation_top_header_2>ul>li:after {
            content: "|";
            display: inline-block;
            margin: 0px 15px;
            font-size: 13px;
        }

        .nd_options_navigation_top_header_2>ul>li:last-child:after {
            content: "";
            margin: 0px;
        }

        .nd_options_navigation_top_header_2 li a {
            font-size: 13px;
        }

        .nd_options_navigation_top_header_2>ul li:hover>ul.nd_options_sub_menu {
            display: block;
        }

        .nd_options_navigation_top_header_2>ul li>ul.nd_options_sub_menu {
            padding: 10px 0px 0px 15px;
            position: absolute;
            margin: 0px;
            list-style: none;
            display: none;
            z-index: 9;
        }

        .nd_options_navigation_top_header_2>ul li>ul.nd_options_sub_menu>li {
            padding: 7px 15px;
            font-size: 13px;
            border-bottom: 1px solid #595959;
            background-color: #444444;
        }

        .nd_options_navigation_top_header_2>ul li>ul.nd_options_sub_menu>li:last-child {
            border-bottom: 0px solid #000;
        }

        #nd_options_navigation_top_header_2_left div:last-child div a img {
            margin-right: 0px;
        }

        #nd_options_navigation_top_header_2_right div:last-child div a img {
            margin-left: 0px;
        }

        /*arrow for item has children*/
        .nd_options_navigation_2 .menu ul.sub-menu li.menu-item-has-children>a:after {
            content: "";
            float: right;
            border-style: solid;
            border-width: 5px 0 5px 5px;
            border-color: transparent transparent transparent #000000;
            margin-top: 3px;
        }

        /* -------------------- */
        .nd_options_cursor_default_a>a {
            cursor: default;
        }

        .nd_options_customizer_labels_color_new {
            background-color: #444444;
        }

        /*hot*/
        .nd_options_navigation_type .menu li.nd_options_hot_label>a:after,
        #nd_options_header_5 .menu li.nd_options_hot_label>a:after,
        #nd_options_header_6 .menu li.nd_options_hot_label>a:after {
            content: "HOT";
            float: right;
            background-color: #444444;
            border-radius: 3px;
            color: #fff;
            font-size: 10px;
            line-height: 10px;
            padding: 3px 5px;
        }

        /*best*/
        .nd_options_navigation_type .menu li.nd_options_best_label>a:after,
        #nd_options_header_5 .menu li.nd_options_best_label>a:after,
        #nd_options_header_6 .menu li.nd_options_best_label>a:after {
            content: "BEST";
            float: right;
            background-color: #444444;
            border-radius: 3px;
            color: #fff;
            font-size: 10px;
            line-height: 10px;
            padding: 3px 5px;
        }

        /*new*/
        .nd_options_navigation_type .menu li.nd_options_new_label>a:after,
        #nd_options_header_5 .menu li.nd_options_new_label>a:after,
        #nd_options_header_6 .menu li.nd_options_new_label>a:after {
            content: "NEW";
            float: right;
            background-color: #444444;
            border-radius: 3px;
            color: #fff;
            font-size: 10px;
            line-height: 10px;
            padding: 3px 5px;
        }

        /*slide*/
        .nd_options_navigation_type .menu li.nd_options_slide_label>a:after,
        #nd_options_header_5 .menu li.nd_options_slide_label>a:after,
        #nd_options_header_6 .menu li.nd_options_slide_label>a:after {
            content: "SLIDE";
            float: right;
            background-color: #444444;
            border-radius: 3px;
            color: #fff;
            font-size: 10px;
            line-height: 10px;
            padding: 3px 5px;
        }

        /*demo*/
        .nd_options_navigation_type .menu li.nd_options_demo_label>a:after,
        #nd_options_header_5 .menu li.nd_options_demo_label>a:after,
        #nd_options_header_6 .menu li.nd_options_demo_label>a:after {
            content: "DEMO";
            float: right;
            background-color: #444444;
            border-radius: 3px;
            color: #fff;
            font-size: 10px;
            line-height: 10px;
            padding: 3px 5px;
        }

        /*all*/
        #nd_options_header_6 .menu li.nd_options_hot_label>a:after,
        #nd_options_header_6 .menu li.nd_options_best_label>a:after,
        #nd_options_header_6 .menu li.nd_options_new_label>a:after,
        #nd_options_header_6 .menu li.nd_options_slide_label>a:after,
        #nd_options_header_6 .menu li.nd_options_demo_label>a:after {
            padding: 5px 5px 3px 5px;
            border-radius: 0px;
            letter-spacing: 1px;
        }

        /*all*/
        .nd_elements_navigation_sidebar_content .menu li.nd_options_new_label>a:after,
        .nd_elements_navigation_sidebar_content .menu li.nd_options_hot_label>a:after,
        .nd_elements_navigation_sidebar_content .menu li.nd_options_best_label>a:after,
        .nd_elements_navigation_sidebar_content .menu li.nd_options_slide_label>a:after,
        .nd_elements_navigation_sidebar_content .menu li.nd_options_demo_label>a:after {
            display: none;
        }

        #multi-language {
            outline: none;
            background: unset;
            color: gray;
            border: unset;
        }
    </style>
</head>

<body style="background: #f1f2f6;">
    <!-- Start Top Header -->

    <!-- Add this CSS in your <head> section -->
    <style>
        /* Language Selector Flags */
        #multi-language {
            padding-left: 30px;
            background-repeat: no-repeat;
            background-position: left 8px center;
            background-size: 20px 15px;
            color: white;
            border: none;
            background-color: rgba(0, 0, 0, 0.2);
            cursor: pointer;
            border-radius: 4px;
            padding: 6px 15px 6px 35px;
            transition: background-color 0.3s ease;
        }

        #multi-language:hover {
            background-color: rgba(0, 0, 0, 0.3);
        }

        #multi-language option {
            padding: 8px 15px 8px 35px !important;
            background-repeat: no-repeat !important;
            background-position: left 10px center !important;
            background-size: 20px 15px !important;
            color: #fff !important;
            background-color: #333 !important;
        }

        #multi-language option[value="en"] {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30"><path fill="%2300247D" d="M0 0h60v30H0z"/><path stroke="%23FFF" stroke-width="6" d="M0 0l60 30m0-30L0 30"/><path stroke="%23CF142B" stroke-width="4" d="M0 0l60 30m0-30L0 30"/><path stroke="%2300247D" stroke-width="2" d="M30 0v30M0 15h60"/></svg>') !important;
        }

        #multi-language option[value="es"] {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 40"><rect width="60" height="40" fill="%23C60B1E"/><rect y="15" width="60" height="10" fill="%23FFC400"/></svg>') !important;
        }

        #multi-language[data-value="en"] {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30"><path fill="%2300247D" d="M0 0h60v30H0z"/><path stroke="%23FFF" stroke-width="6" d="M0 0l60 30m0-30L0 30"/><path stroke="%23CF142B" stroke-width="4" d="M0 0l60 30m0-30L0 30"/><path stroke="%2300247D" stroke-width="2" d="M30 0v30M0 15h60"/></svg>') !important;
        }

        #multi-language[data-value="es"] {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 40"><rect width="60" height="40" fill="%23C60B1E"/><rect y="15" width="60" height="10" fill="%23FFC400"/></svg>') !important;
        }
    </style>

    <!--  modified header section -->
    <div class="top-header">
        <div class="social-icons">
            <a href="https://www.facebook.com/craftcontstruction">
                <img src="https://craftgc.com/wp-content/plugins/nd-shortcodes/addons/customizer/shortcodes/top-header/img/facebook-white.svg"
                    alt="Facebook">
            </a>
            <a href="https://www.instagram.com/craftcontracting/">
                <img src="https://craftgc.com/wp-content/plugins/nd-shortcodes/addons/customizer/shortcodes/top-header/img/instagram-white.svg"
                    alt="Instagram">
            </a>
        </div>
        <div class="top-header-right">
            <div class="d-flex gap-3">
                <select id="multi-language">
                    <option value="en" <?= (isset($_SESSION['lang']) && $_SESSION['lang'] == 'en') ? 'selected' : ''; ?>>
                        English</option>
                    <option value="es" <?= (isset($_SESSION['lang']) && $_SESSION['lang'] == 'es') ? 'selected' : ''; ?>>
                        Español</option>
                </select>
                <a href="https://craftgc.com/archive-projects/">
                    <img src="https://craftgc.com/wp-content/plugins/nd-shortcodes/addons/customizer/shortcodes/top-header/img/star-white.svg"
                        alt="Star">
                    <span>NEW PROJECTS</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Add this JavaScript at the bottom before </body> -->
    <script>
        // Update flag display when language changes
        document.getElementById('multi-language').addEventListener('change', function () {
            this.setAttribute('data-value', this.value);
        });

        // Initialize flag display
        const langSelect = document.getElementById('multi-language');
        langSelect.setAttribute('data-value', langSelect.value);
    </script>
    <!-- End Top Header -->

    <!-- Start Navigation -->
    <nav class="main-nav">
        <div class="container">
            <a href="https://craftgc.com/" class="logo">
                <img src="https://craftgc.com/wp-content/uploads/2024/06/craftcon-white-1.png" alt="Logo">
            </a>
            <button class="menu-toggle" aria-label="Toggle menu">&#9776;</button>
            <ul class="menu">
                <li><a href="https://craftgc.com/">HOME</a></li>
                <li><a href="https://craftgc.com/about-us/">ABOUT</a></li>
                <li><a href="https://craftgc.com/archive-projects/">PROJECTS</a></li>
                <li><a href="https://craftgc.com/services-1/">SERVICES</a></li>
                <li><a href="https://craftgc.com/blog/">BLOG</a></li>
                <li><a href="https://craftgc.com/privacy-policy-2/">PRIVACY POLICY</a></li>
                <li><a href="javascript:void(0);" class="highlight">JOIN OUR TEAM</a></li>
                <li><a href="https://craftgc.com/contact-1/">CONTACT US</a></li>
            </ul>
        </div>
    </nav>
    <!-- End Navigation -->

    <!-- Custom CSS -->
    <style>
        /* General Styles */
        .top-header {
            background-color: #000;
            color: #fff;
            /* padding: 10px 76px; */
            padding: 10px 138px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-header .social-icons img,
        .top-header-right img {
            width: 15px;
            height: 15px;
            margin-right: 8px;
        }

        .top-header-right a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        /* Navigation */
        .main-nav {
            background-color: #ff8800;
            border-bottom: 1px solid #000;
            padding: 28px 20px;
        }

        .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo img {
            width: 100px;
        }

        .menu {
            list-style: none;
            display: flex;
            gap: 20px;
            padding: 0;
            margin: 0;
        }

        .menu li {
            display: inline-block;
        }

        .menu a {
            color: #000;
            text-decoration: none;
            padding: 10px 10px;
            font-weight: bold;
        }

        .highlight {
            border-bottom: 2px solid red;
        }

        /* Mobile Menu */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .top-header {
                padding: 10px 6px;
            }

            /* .nd_options_section {
            padding: 50px 30px;
        } */
            .menu {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                background-color: #ff8800;
                text-align: center;
                padding: 10px 0;
            }

            .menu.active {
                display: flex;
            }

            .menu-toggle {
                display: block;
            }
        }
    </style>

    <!-- JavaScript for Mobile Menu -->
    <script>
        document.querySelector(".menu-toggle").addEventListener("click", function () {
            document.querySelector(".menu").classList.toggle("active");
        });
    </script>

    <div class="container my-4">
        <div class="col-lg-10 mx-auto bg-white p-4 shadow">
            <h1 class="mb-5 text-center" id="formHeading">Craft Contracting Form</h1>
            <form id="employmentApplication" action="submit_application.php" method="post">

                <!-- Employee Information -->
                <div id="step1">
                    <h3>Employee Information</h3>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="fullname" class="form-label">Full name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="fullname" name="full_name" value="<?php
                            if (isset($applicant['first_name']) || isset($applicant['last_name'])) {
                                echo htmlspecialchars(($applicant['first_name'] ?? '') . ' ' . ($applicant['last_name'] ?? ''));
                            } else {
                                echo '';
                            }
                            ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" readonly value="<?php
                            if (isset($signature['signature_date'])) {
                                echo $signature['signature_date'];
                            } else {
                                echo 'N/A';
                            }
                            ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="streetAddress" class="form-label">Address <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="streetAddress" name="street_address" value="<?php
                            if (isset($applicant['street_address'])) {
                                echo htmlspecialchars($applicant['street_address']);
                            } else {
                                echo '';
                            }
                            ?>" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="city" name="city" value="<?php
                            if (isset($applicant['city'])) {
                                echo htmlspecialchars($applicant['city']);
                            } else {
                                echo '';
                            }
                            ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="state" name="state" value="<?php
                            if (isset($applicant['state'])) {
                                echo htmlspecialchars($applicant['state']);
                            } else {
                                echo '';
                            }
                            ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="zipCode" class="form-label">Zip <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="zipCode" name="zip_code" value="<?php
                            if (isset($applicant['zip_code'])) {
                                echo htmlspecialchars($applicant['zip_code']);
                            } else {
                                echo '';
                            }
                            ?>" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="phoneNumber" class="form-label">Phone Number <span
                                    class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phoneNumber" name="phone_number" value="<?php
                            if (isset($applicant['phone_number'])) {
                                echo htmlspecialchars($applicant['phone_number']);
                            } else {
                                echo '';
                            }
                            ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php
                            if (isset($applicant['email'])) {
                                echo htmlspecialchars($applicant['email']);
                            } else {
                                echo '';
                            }
                            ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="startDate" class="form-label">Start Date <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="startDate" name="start_date" value="<?php
                            if (isset($applicant['available_start_date'])) {
                                echo htmlspecialchars($applicant['available_start_date']);
                            } else {
                                echo '';
                            }
                            ?>" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="wageSalary" class="form-label">Wage/Salary <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="wageSalary" name="wage_salary" value="<?php
                            if (isset($applicant['salary'])) {
                                echo htmlspecialchars($applicant['salary']);
                            } else {
                                echo '';
                            }
                            ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="positionDesired" class="form-label">Position Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="position" name="position" value="<?php
                            if (isset($newApplicants['position_name'])) {
                                echo htmlspecialchars($newApplicants['position_name']);
                            } else {
                                echo '';
                            }
                            ?>" readonly>
                        </div>
                    </div>

                    <div id="btnWrap" class="d-flex justify-content-between">
                    <button type="button" class="btn btn-primary" onclick="nextStep(2)">Next</button>
                </div>
                </div>

                <!-- Emergency Contact 1 -->
                <div id="step2" style="display: none;">
                    <h3>Emergency Contact 1</h3>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="emergencyname" class="form-label">Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="emergencyname" name="emergency_name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="emergencyaddress" class="form-label">Address <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="emergencyaddress" name="emergency_address"
                                required>
                        </div>
                        <div class="col-md-4">
                            <label for="emergencyphoneNumber" class="form-label">Phone Number <span
                                    class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="emergencyphoneNumber"
                                name="emergency_phone_number" required>
                        </div>
                        <div class="col-md-4">
                            <label for="relationship" class="form-label">Relationship <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="relationship" name="relationship" required>
                        </div>
                    </div>
                    <div id="btnWrap" class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="prevStep(1)">Back</button>
                    <button type="button" class="btn btn-primary" onclick="nextStep(3)">Next</button>
                </div>
                </div>

                <!-- Emergency Contact 2 -->
                <div id="step3" style="display: none;">
                    <h3>Emergency Contact 2</h3>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="emergencyname2" class="form-label">Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="emergencyname2" name="emergency_name2" required>
                        </div>
                        <div class="col-md-4">
                            <label for="emergencyaddress2" class="form-label">Address <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="emergencyaddress2" name="emergency_address2"
                                required>
                        </div>
                        <div class="col-md-4">
                            <label for="emergencyphoneNumber2" class="form-label">Phone Number <span
                                    class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="emergencyphoneNumber2"
                                name="emergency_phone_number2" required>
                        </div>
                        <div class="col-md-4">
                            <label for="relationship2" class="form-label">Relationship <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="relationship2" name="relationship2" required>
                        </div>
                    </div>
                    <div id="btnWrap" class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="prevStep(2)">Back</button>
                    <button type="button" class="btn btn-primary" onclick="nextStep(4)">Next</button>
                </div>
                </div>

                <!-- Craft Employee File Notes -->
                <div id="step4" style="display: none;">
                    <h3>Craft Employee File Notes</h3>
                    <div class="row mb-3">
                        <div class="col-12">
                            <input type="text" class="form-control" id="filenotes" name="file_notes" required>
                        </div>
                    </div>
                    <div id="btnWrap" class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="prevStep(3)">Back</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" id="downloadBtn" class="btn btn-primary">Download PDF</button>

                </div>
                </div>

            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
// Function to download PDF with improved formatting
function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Set title of the PDF
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text("Craft Contracting Form", 10, 10);

    // Set font size and style for the body text
    doc.setFontSize(12);
    doc.setFont('helvetica', 'normal');

    // Add title section
    const yStart = 20;
    let yPosition = yStart;
    const sectionGap = 15;

    // Function to add headings and data with proper formatting
    function addSection(title, data, yPosition) {
        // Set background for the heading
        doc.setFillColor(220, 220, 220); // Light gray background for headings
        doc.rect(10, yPosition, 190, 8, 'F'); // Draw rectangle for heading background
        doc.setTextColor(0, 0, 0); // Text color for heading (black)
        doc.text(title, 12, yPosition + 5); // Title of the section

        // Data section below the heading
        doc.setTextColor(100, 100, 100); // Set text color for data (gray)
        const dataYPosition = yPosition + sectionGap;
        doc.text(data, 12, dataYPosition); // Add data below the heading

        return dataYPosition + sectionGap; // Return new Y position for the next section
    }

    // Full name section
    yPosition = addSection('Full Name:', document.getElementById('fullname').value, yPosition);

    // Street Address section
    yPosition = addSection('Street Address:', document.getElementById('streetAddress').value, yPosition);

    // City section
    yPosition = addSection('City:', document.getElementById('city').value, yPosition);

    // State section
    yPosition = addSection('State:', document.getElementById('state').value, yPosition);

    // Zip Code section
    yPosition = addSection('Zip Code:', document.getElementById('zipCode').value, yPosition);

    // Phone Number section
    yPosition = addSection('Phone Number:', document.getElementById('phoneNumber').value, yPosition);

    // Email section
    yPosition = addSection('Email:', document.getElementById('email').value, yPosition);

    // Start Date section
    yPosition = addSection('Start Date:', document.getElementById('startDate').value, yPosition);

    // Wage/Salary section
    yPosition = addSection('Wage/Salary:', document.getElementById('wageSalary').value, yPosition);

    // Position section
    yPosition = addSection('Position:', document.getElementById('position').value, yPosition);

    // Reference 1 Information
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text("Reference 1 Information", 10, yPosition); // Position the title for Reference 1
    yPosition += sectionGap;

    // Emergency Contact 1 section
    yPosition = addSection('Emergency Contact 1 - Name:', document.getElementById('emergencyname')?.value || 'N/A', yPosition);
    yPosition = addSection('Emergency Contact 1 - Address:', document.getElementById('emergencyaddress')?.value || 'N/A', yPosition);
    yPosition = addSection('Emergency Contact 1 - Phone:', document.getElementById('emergencyphoneNumber')?.value || 'N/A', yPosition);
    yPosition = addSection('Emergency Contact 1 - Relationship:', document.getElementById('relationship')?.value || 'N/A', yPosition);

    // Reference 2 Information
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text("Reference 2 Information", 10, yPosition); // Position the title for Reference 2
    yPosition += sectionGap;

    // Emergency Contact 2 section
    yPosition = addSection('Emergency Contact 2 - Name:', document.getElementById('emergencyname2')?.value || 'N/A', yPosition);
    yPosition = addSection('Emergency Contact 2 - Address:', document.getElementById('emergencyaddress2')?.value || 'N/A', yPosition);
    yPosition = addSection('Emergency Contact 2 - Phone:', document.getElementById('emergencyphoneNumber2')?.value || 'N/A', yPosition);
    yPosition = addSection('Emergency Contact 2 - Relationship:', document.getElementById('relationship2')?.value || 'N/A', yPosition);

    // File Notes section
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text("File Notes", 10, yPosition); // Position the title for File Notes
    yPosition += sectionGap;

    // File Notes section
    yPosition = addSection('File Notes:', document.getElementById('filenotes')?.value || 'N/A', yPosition);

    // Add page if the content exceeds one page
    if (yPosition > 250) {
        doc.addPage();
        yPosition = 20; // Reset yPosition for the new page after adding it
    }

    // Save PDF with a filename
    doc.save('employment_application_form.pdf');
}

// Event listener for the download button
document.getElementById('downloadBtn').addEventListener('click', downloadPDF);
</script>


    <script>
        function nextStep(step) {
            document.getElementById('step' + (step - 1)).style.display = 'none';
            document.getElementById('step' + step).style.display = 'block';
        }

        function prevStep(step) {
            document.getElementById('step' + (step + 1)).style.display = 'none';
            document.getElementById('step' + step).style.display = 'block';
        }
    </script>


    <script>
        document.getElementById('nextButton').addEventListener('click', function () {
            var currentSection = document.querySelector('div[id^="section"]:not([style*="display: none"])');
            var nextSectionId = "section" + (parseInt(currentSection.id.replace('section', '')) + 1);
            var nextSection = document.getElementById(nextSectionId);

            if (nextSection) {
                currentSection.style.display = 'none';  // Hide current section
                nextSection.style.display = 'block';    // Show next section
            }
        });

    </script>

    
<script>
    let currentStep = 1;  // Track the current step

    // Function to change the heading based on the step
    function updateHeading(step) {
        const heading = document.getElementById("formHeading");
        switch (step) {
            case 1:
                heading.textContent = "Craft Contracting Form - 1/5";
                break;
            case 2:
                heading.textContent = "Craft Contracting Form - 2/5";
                break;
            case 3:
                heading.textContent = "Craft Contracting Form - 3/5";
                break;
            case 4:
                heading.textContent = "Craft Contracting Form - 4/5";
                break;
            case 5:
                heading.textContent = "Craft Contracting Form - 5/5";
                break;
            default:
                heading.textContent = "Craft Contracting Form";
        }
    }

    // Function to show the next step
    function nextStep(step) {
        // Hide current step
        document.getElementById("step" + currentStep).style.display = "none";

        // Update current step
        currentStep = step;

        // Show the new step
        document.getElementById("step" + currentStep).style.display = "block";

        // Update heading to reflect the current step
        updateHeading(currentStep);
    }

    // Function to show the previous step
    function prevStep(step) {
        // Hide current step
        document.getElementById("step" + currentStep).style.display = "none";

        // Update current step
        currentStep = step;

        // Show the previous step
        document.getElementById("step" + currentStep).style.display = "block";

        // Update heading to reflect the current step
        updateHeading(currentStep);
    }

    // Initialize the heading and show the first step on page load
    window.onload = function() {
        document.getElementById("step" + currentStep).style.display = "block"; // Show first step
        updateHeading(currentStep); // Update heading for step 1
    };
</script>

    
    <!-- START Footer -->
    <div id="nd_options_footer_5" class="nd_options_section">
        <div class="nd_options_container nd_options_clearfix">
            <div class="footer-content">
                <!-- Logo and Description -->
                <div class="footer-left">
                    <img src="https://craftgc.com/wp-content/uploads/2024/06/craftcon-white-1024x278.png"
                        alt="Craft Contracting" width="200">
                    <p>Craft Contracting was established in 2016. Craft has expanded to become a
                        successful
                        multi-disciplined firm providing services to a diversified range of Civil
                        and Building
                        Construction and Maintenance projects in the federal, public, and private
                        sectors.</p>

                    <!-- Social Media Links -->
                    <div class="social-icons" style="display: flex;">
                        <a href="https://www.instagram.com/craftcontracting/" target="_blank">
                            <img src="https://craftgc.com/wp-content/uploads/2019/09/icon-social4-white.png"
                                alt="Instagram" width="22">
                        </a>
                        <a href="https://www.facebook.com/craftcontstruction" target="_blank">
                            <img src="https://craftgc.com/wp-content/uploads/2019/09/icon-social2-white.png"
                                alt="Facebook" width="22">
                        </a>
                    </div>
                </div>

                <!-- Services Section -->
                <div class="footer-middle">
                    <h2>SERVICES</h2>
                    <ul>
                        <li><a href="https://craftgc.com/services-1/">Civil Service</a></li>
                        <li><a href="https://craftgc.com/services-1/">General Service</a></li>
                        <li><a href="https://craftgc.com/services-1/">Residential Service</a></li>
                        <li><a href="https://craftgc.com/services-1/">Commercial Service</a></li>
                        <li><a href="https://craftgc.com/services-1/">Government Service</a></li>
                        <li><a href="https://craftgc.com/services-1/">Facilities Maintenance</a>
                        </li>
                    </ul>
                </div>

                <!-- Privacy and Contact -->
                <div class="footer-right">
                    <h3><a href="https://craftgc.com/services-2/">PRIVACY POLICY</a></h3>
                    <h3><a href="https://craftgc.com/contact-1/">CONTACT US</a></h3>
                </div>
            </div>

            <!-- Copyright -->
            <div class="footer-bottom">
                <p>© 2024 Craft Contracting. Created by <a href="https://www.industryresults.com/">Industry
                        Results</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Custom CSS for Styling -->
    <style>
        .nd_options_section {
            background-image: url('https://craftgc.com/wp-content/uploads/2019/09/paral-04.jpg');
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            color: #fff;
            padding: 50px 30px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .footer-left,
        .footer-middle,
        .footer-right {
            flex: 1;
            min-width: 250px;
        }

        .footer-right h3 a {
            color: #fff;
            font-size: 1.3rem !important;
            text-decoration: unset;
        }

        .footer-left img {
            display: block;
            margin-bottom: 15px;
        }

        .footer-left p {
            font-size: 16px;
            line-height: 1.5;
        }

        .social-icons a {
            margin-right: 10px;
        }

        .footer-middle ul {
            list-style: none;
            padding: 0;
        }

        .footer-middle ul li a {
            color: #fff;
            text-decoration: none;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.4);
            padding-top: 10px;
        }
    </style>


    <script>
        $(document).ready(function () {

            // Change language
            $("#multi-language").on('change', function () {
                var selectedLang = $(this).val(); // Get selected language
                var currentUrl = window.location.href; // Get current URL

                // Remove existing lang parameter if exists
                var newUrl = new URL(currentUrl);
                newUrl.searchParams.set('lang', selectedLang); // Set new lang parameter

                // Redirect to new URL with lang parameter
                window.location.href = newUrl.toString();
            });

            // Toggle criminal history details based on selection
            $('input[name="criminal_history"]').change(function () {
                if ($(this).val() === 'Yes') {
                    $('#criminalDetails').slideDown();
                } else {
                    $('#criminalDetails').slideUp();
                }
            });

            // Toggle enrollment details
            $('input[name="currently_enrolled"]').change(function () {
                if ($(this).val() === 'Yes') {
                    $('#enrollmentDetails').slideDown();
                } else {
                    $('#enrollmentDetails').slideUp();
                }
            });

            // Display minor notice if under 18
            $('input[name="age_confirm"]').change(function () {
                if ($(this).val() === 'No') {
                    $('#minorNotice').show();
                } else {
                    $('#minorNotice').hide();
                }
            });

            // Handle dynamic addition of employment history sections
            $('#addEmployer').click(function () {
                var newEntry = $('.employment-entry').first().clone();
                // Clear values in cloned fields
                newEntry.find('input, textarea').each(function () {
                    $(this).val('');
                });
                $('#employmentHistoryContainer').append(newEntry);
            });

            // Add availability row
            let avail_count = 1;
            $('#availability_more').on('click', function () {
                avail_count++;
                var avail_row = `<div id="avail_row${avail_count}" class="row g-2 mt-2">
                        <div class="col-5">
                            <select name="availability_day[]" id="" class="form-control" required>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <input type="time" class="form-control" name="availability_from[]">
                        </div>
                        <div class="col-3">
                            <input type="time" class="form-control" name="availability_to[]">
                        </div>
                        <div class="col-1">
                            <button type="button" data-count="${avail_count}" class="btn btn-sm btn-danger remove_avail_row">X</button>
                        </div>
                    </div>`;
                $("#availability_wrap").append(avail_row);

                // Remove availability row
                $('.remove_avail_row').on('click', function () {
                    let row_count = $(this).attr('data-count');
                    $(`#avail_row${row_count}`).remove();
                    calculateTotalHours();
                });

            });

            $(document).ready(function () {
                function calculateTotalHours() {
                    let totalMinutes = 0;

                    $("input[name='availability_from[]']").each(function (index) {
                        let fromTime = $(this).val();
                        let toTime = $("input[name='availability_to[]']").eq(index).val();

                        if (fromTime && toTime) {
                            let from = fromTime.split(":");
                            let to = toTime.split(":");

                            let fromMinutes = parseInt(from[0]) * 60 + parseInt(from[1]);
                            let toMinutes = parseInt(to[0]) * 60 + parseInt(to[1]);

                            if (toMinutes > fromMinutes) {
                                totalMinutes += toMinutes - fromMinutes;
                            }
                        }
                    });

                    let totalHours = (totalMinutes / 60).toFixed(
                        2); // Convert minutes to hours with 2 decimal places
                    $("#totalHours").val(totalHours);
                }

                $(document).on("change",
                    "input[name='availability_from[]'], input[name='availability_to[]']",
                    calculateTotalHours);
            });

            //.................


            /*  Example form submit handler (replace with AJAX or standard submission as needed)
              $('#employmentApplication').submit(function(e) {
                  e.preventDefault();
                  // var formData = $(this).serialize();
      
                  var formData = $(this).serializeArray(); // Convert form data to array
                  var jsonData = {};
      
                  // Convert serialized array into JSON object
                  $.each(formData, function() {
                      jsonData[this.name] = this.value;
                  });
      
                  // AJAX submission
                  $.ajax({
                      url: 'ajax_helpers/handle_application_form.php', // Your processing script
                      type: 'POST',
                      data: $(this).serialize(),
                      dataType: 'json',
                      beforeSend: function() {
                          // Show loading state
                          $('button[type="submit"]').prop('disabled', true)
                              .html(
                                  '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...'
                              );
                      },
                      success: function(response) {
                          if (response.success) {
                              // Show simple alert
                              // alert('Application submitted successfully!');
                              $("#employmentApplication")[0].reset();
                              
                              // Redirect to thank you page
                              window.location.href = 'thanks.php';
                          } else {
                              // Show error message
                              alert(response.message || 'Error submitting application');
                              $('button[type="submit"]').prop('disabled', false).html(
                                  'Submit Application');
                          }
                      },
                      error: function() {
                          // Show error message
                          alert('There was an error. Please try again.');
                          $('button[type="submit"]').prop('disabled', false).html(
                              'Submit Application');
                      }
                  });
                  For production, remove the alert and process the form data on the server.
              }); */
        }); 
    </script>
    <script>
        $(document).ready(function () {
            // Form submission handler
            $('#employmentApplication').on('submit', function (e) {
                e.preventDefault(); // Always prevent default first

                const form = this;

                // Show confirmation dialog
                Swal.fire({
                    title: '<?php ("form_submit_confirm_title"); ?>',
                    text: '<?php ("form_submit_confirm_text"); ?>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#FF5500',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '<?php ("form_submit_confirm_button"); ?>',
                    cancelButtonText: '<?php ("form_cancel_button"); ?>'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitForm(form);
                    }
                });
            });

            function submitForm(form) {
                const submitBtn = $(form).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();

                // Show loading state
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php ("form_submitting"); ?>'
                );

                $.ajax({
                    url: 'ajax_helpers/handle_application_form.php',
                    type: 'POST',
                    data: $(form).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            form.reset();
                            window.location.href = 'thanks.php'; // Direct redirect
                        } else {
                            Swal.fire({
                                title: '<?php ("form_error_title"); ?>',
                                text: response.message || '<?php ("form_error_generic"); ?>',
                                icon: 'error',
                                confirmButtonColor: '#FF5500'
                            });
                        }
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            title: '<?php ("form_error_title"); ?>',
                            text: '<?php ("form_error_network"); ?>',
                            icon: 'error',
                            confirmButtonColor: '#FF5500'
                        });
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            }
        });
        // jquery to prevent future date on signature
        $(document).ready(function () {
            $('#signatureDate').on('change', function () {
                const inputDate = new Date($(this).val());
                const now = new Date();


                now.setHours(0, 0, 0, 0);


                inputDate.setHours(0, 0, 0, 0);
                if (inputDate > now) {
                    $('#dateError').text("Signature date cannot be in the future.");
                    $(this).val('');
                } else {
                    $('#dateError').text('');
                }
            });
        });


    </script>
</body>

</html>