<?php
if ($_SESSION['role_id'] == $admin_role || $_SESSION['role_id'] == $manager_role) {
    if (isset($_GET['id'])) {
        $applicantId = $_GET['id'];
    } else {
        $applicantId = 0;
    }

    $signature = null;


    $signature = DB::queryFirstRow(
        "SELECT * FROM application_signatures 
         WHERE user_id = %i",
        $applicantId
    );


    // fetch forms id like 3,6,9 etc....
    if ($applicantId > 0) {
        // Fetch all data based on craft_contracting_id
        $craft_data = DB::queryFirstRow("
            SELECT craft_contracting.*, p.position_name 
            FROM craft_contracting
            LEFT JOIN positions p ON craft_contracting.position = p.id
            WHERE craft_contracting.id = %i", $applicantId);

        $verification = DB::queryFirstRow("SELECT * FROM employment_eligibility_verification WHERE id = %i", $applicantId);
        $verification_1 = DB::queryFirstRow("SELECT * FROM employment_eligibility_verification1 WHERE id = %i", $applicantId);
        $mvr = DB::queryFirstRow("SELECT * FROM mvr_released WHERE id = %i", $applicantId);
        $quick_book = DB::queryFirstRow("SELECT * FROM quick_book WHERE id = %i", $applicantId);
        $w4 = DB::queryFirstRow("SELECT * FROM w4_form WHERE id = %i", $applicantId);
        $data = DB::queryFirstRow("SELECT * FROM employment_data WHERE id = %i", $applicantId);
        $certification_files = DB::queryFirstRow("SELECT * FROM certification_files WHERE id = %i", $applicantId);
        $non_compete = DB::queryFirstRow("SELECT * FROM non_compete_agreements WHERE applicant_id = %i", $applicantId);

        if ($craft_data) {
            // Output the data (example: converting to JSON)

        } else {
            header("Location: " . SITE_ROOT . "index.php?route=modules/forms/list_data");
        }
    } else {
        header("Location: " . SITE_ROOT . "index.php?route=modules/forms/list_data");
    }
}else{
    header("Location: " . SITE_ROOT . "index.php?route=modules/forms/list_data");
}



// Set up the navigation path
$formSteps = [1,2,3,4,5,6,7,8]; // Default compulsory steps

?>



<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employment Packet Form</title>

    <!-- jQuery (from CDN) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- recaptcha -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Add SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Add this line in the head section -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Default state - black */
        .form-check-input {
            border: 2px solid #000 !important;
            background-color: transparent !important;
        }

        /* Checked state - blue */
        .form-check-input:checked {
            background-color: #FF5500 !important;
            border-color: #FF5500 !important;
        }

        /* White checkmark */
        .form-check-input:checked[type=checkbox] {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='%23fff' d='M5.707 7.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4a1 1 0 0 0-1.414-1.414L7 8.586 5.707 7.293z'/%3e%3c/svg%3e") !important;
        }

        /* Blue focus glow */
        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(244, 174, 93, 0.25) !important;
        }

        .language-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 50vh;
            background: rgb(255, 255, 255);
        }

        .language-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
        }
        .form-group img{
                background: transparent;

            }
        .language-option {
            display: inline-block;
            padding: 1rem 2rem;
            margin: 1rem;
            border: 2px solid #FF5500;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: #FF5500;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .language-option:hover {
            background: #FF5500;
            color: white;
        }

        .step-icon i {
            color: white;
        }
.btn-loader {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
    margin-right: 8px;
    vertical-align: middle;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
        /* Add this at the end of existing styles */
        @media (max-width: 550px) {
            /* Target only the form columns */
            .main-content .col-sm-12, 
            .main-content .form-group,
            .main-content .col-12,
            .main-content {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            .top-header {
                padding: 8px 10px !important;
                flex-direction: column;
                gap: 8px;
            }

            .logo img {
                width: 80px;
            }

            .menu a {
                font-size: 14px;
                padding: 8px;
            }
            .form-group img{
                width: 50%;
                background: transparent;

            }
            .container {
                padding: 0 15px;
            }

            .progress-steps {
                gap: 4px;
            }

            .step-icon {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }

            .step-label {
                font-size: 0.7rem;
                display: none;
            }

            .form-step h3 {
                font-size: 1.25rem;
            }

            /* .row>[class^="col-"] {
                padding: 4px !important;
            } */

            .btn {
                width: 100%;
                margin: 4px 0;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 8px;
            }

            #signaturePad {
                width: 100% !important;
                height: 150px;
            }

            .footer-content {
                flex-direction: column;
                gap: 30px;
                text-align: center;
            }

            .footer-left,
            .footer-middle,
            .footer-right {
                width: 100%;
                padding: 0 15px;
            }

            .social-icons {
                justify-content: center;
            }

            /* Specific form adjustments */
            .col-md-2,
            .col-md-3,
            .col-md-4,
            .col-md-6 {
                width: 100%;
                max-width: 100%;
                flex: 0 0 100%;
            }

            input,
            select,
            textarea {
                font-size: 14px !important;
            }

            .employment-entry .row>[class^="col-"] {
                width: 100%;
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        .license-upload {
        border: 2px dashed #ddd;
        padding: 20px;
        width: 100%;
        cursor: pointer;
        text-align: center;
        color: #888;
        transition: background-color 0.3s ease;
        border-radius: 8px;
    }

    .license-upload:hover {
        background-color: #f9f9f9;
    }

    .license-input {
        display: none;
    }

    .preview-container img {
        max-height: 200px;
    }
        .preview-container {
            position: relative;
            min-height: 200px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .preview-container img,
        .preview-container video {
            width: 100%;
            height: auto;
            max-height: 300px;
            object-fit: contain;
        }

        .camera-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            display: none;
        }

        .camera-preview {
            position: relative;
            width: 100%;
            max-width: 600px;
            margin: 2rem auto;
            background: black;
        }

        .capture-controls {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .capture-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #fff;
            border: 4px solid #FF5500;
            cursor: pointer;
        }


        @media (max-width: 360px) {
            .top-header {
                padding: 8px 5px !important;
            }

            .menu a {
                font-size: 13px;
            }

            .mb-5 .text-center {
                text-align: center;
                display: flex;
                justify-content: center;
            }

            .step-icon {
                width: 25px;
                height: 25px;
                font-size: 12px;
            }
            .form-group img{
                width: 50%;
                background: transparent;

            }
            .form-step h3 {
                font-size: 1.1rem;
            }

            input,
            select,
            textarea {
                padding: 6px 8px !important;
            }
        }

        .file-upload-wrapper {
            position: relative;
        }

        .preview-content {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        .form-step {
            padding: 10px !important;
        }

        .preview-content img {
            transition: transform 0.3s ease;
        }

        .preview-content img:hover {
            transform: scale(1.05);
        }

        /* Add CSS for preview items */
        .preview-content {
            gap: 1rem;
        }

        .file-preview-item {
            position: relative;
            margin-bottom: 1rem;
        }

        .file-preview-item img {
            max-height: 150px;
            object-fit: contain;
        }

        .remove-file-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            padding: 2px 8px;
            border-radius: 50%;
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

        .btn-outline-secondary {
            background-color: #f1f2f6;
            color: #FF5500;
        }

        .btn-outline-secondary:hover {
            background-color: #FF5500;
            color: #f1f1f1;
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
            background-color: rgb(85, 84, 84);
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

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .progress {
            background-color: rgb(255, 255, 255);
            /* Match your page background */
            border-radius: 2px;
        }

        .progress-bar {
            height: 4px !important;
            border-radius: 4px;
            transition: width 0.3s ease-in-out;
        }

        /* Remove percentage text */
        .progress-bar::after {
            content: none !important;
        }
        .preview-loader, .camera-loader {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background: rgba(255,255,255,0.8);
    z-index: 10;
}

.camera-loader {
    background: rgba(0,0,0,0.7);
    color: white;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 0.25em;
}

.capture-spinner {
    width: 30px;
    height: 30px;
    border: 3px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Camera modal styles */
.camera-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
}

.camera-preview {
    position: relative;
    width: 100%;
    max-width: 500px;
    height: 70vh;
    background: #000;
    display: flex;
    flex-direction: column;
}

.camera-preview video {
    width: 100%;
    height: calc(100% - 120px); /* Make space for controls */
    object-fit: cover;
}

.camera-loader {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    z-index: 10;
    background: rgba(0,0,0,0.7);
}

.capture-controls {
    width: 100%;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
    background: rgba(0,0,0,0.7);
}

.capture-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: white;
    border: 4px solid #ddd;
    cursor: pointer;
    outline: none;
    transition: transform 0.2s;
}

.capture-btn:hover {
    transform: scale(1.05);
}

.capture-btn:active {
    transform: scale(0.95);
}

/* For the capture spinner animation */
.capture-spinner {
    width: 30px;
    height: 30px;
    margin: 15px;
    border: 3px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: #333;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
    </style>
</head>


</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Language selector code
        const langSelect = document.getElementById('multi-language1');
        if (langSelect) {
            langSelect.addEventListener('change', function () {
                this.setAttribute('data-value', this.value);
            });
            langSelect.setAttribute('data-value', langSelect.value);
        }
        // Mobile menu code
        const menuToggle = document.querySelector(".menu-toggle");
        if (menuToggle) {
            menuToggle.addEventListener("click", function () {
                const menu = document.querySelector(".menu");
                if (menu) {
                    menu.classList.toggle("active");
                }
            });
        }
    });
</script>
<!-- End Top Header -->

<!-- Start Navigation -->

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

    .signature-container {
        display: block !important;
    }

    .signature-image img {
        max-width: 100%;
        height: 100px;
        background: transparent;
    }


    .no-signature {
        color: #777 !important;
        font-style: italic !important;
    }

    .signature-date {
        display: none !important;
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

    /* Chrome, Safari, Edge, Opera */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }


    @media (max-width: 550px) {
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

        .mb-5 .text-center {
            text-align: center;
            display: flex;
            justify-content: center;
        }

        .menu.active {
            display: flex;
        }

        .menu-toggle {
            display: block;
        }
    }

    /* Progress Bar Styles */
    .progress-container {
        padding: 20px 0;
    }

    .progress-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-bottom: 30px;
    }

    .progress-steps::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 0;
        right: 0;
        height: 10px;
        z-index: 0;
        border-radius: 5px;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 1;
        width: 16%;
    }

    .progress-segment {
        width: 100%;
        height: 10px;
        background-color: #f1f2f6;
        margin-bottom: 15px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .step.active .progress-segment {
        background-color: #FF5500;
    }

    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f1f2f6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }

    .step.active .step-icon {
        background-color: #FF5500;
        color: white;
        transform: scale(1.1);
    }

    .step-label {
        font-size: 0.9rem;
        text-align: center;
        color: #666;
    }

    .step.active .step-label {
        color: #FF5500;
        font-weight: bold;
    }



    /* Add Font Awesome for icons */
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
</style>

<!-- JavaScript for Mobile Menu -->
<!-- <script>
       document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector(".menu-toggle");
        if (menuToggle) {
            menuToggle.addEventListener("click", function () {
                const menu = document.querySelector(".menu");
                if (menu) {
                    menu.classList.toggle("active");
                }
            });
        }
    });
    </script> -->


<div class="container-fluid my-4">
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12 col-xl-12 border rounded-3 p-4 bg-white shadow-sm"
            style=" overflow-y: auto;">
            <h1 class="mb-5 text-center"><?php echo lang("forms_title"); ?></h1>
            <!-- Progress Bar -->
            <!-- Progress Bar with Icons -->
            <!-- Current icon markup (already correct) -->

            <?php
            // After getting $formSteps from database
            $_SESSION['form_steps'] = $formSteps;

            // Generate step labels and icons only for included steps
            $stepIcons = [
                1 => '<i class="fas fa-language"></i>',
                2 => '<i class="fas fa-user "></i>',
                3 => '<i class="fas fa-passport"></i>',
                4 => '<i class="fas fa-briefcase"></i>',
                5 => '<i class="fas fa-graduation-cap"></i>',
                6 => '<i class="fas fa-history"></i>',
                7 => '<i class="fas fa-file-contract"></i>',
                8 => '<i class="fas fa-file-signature"></i>'

            ];

            $stepLabels = [
                1 => lang("forms_language"),
                2 => lang("forms_Craft_Contract"),
                3 => lang("forms_W4_Data"),
                4 => lang(key: "forms_Quick_book"),
                5 => lang("forms_Employment_Elgibility_Verification"),
                6 => lang("forms_MVR_Information"),
                7 => lang("Agreement"),
                8 => lang("forms_Certification")
            ];
            ?>


            <!-- Progress Bar HTML -->
            <div class="progress-steps">
                <?php foreach ($_SESSION['form_steps'] as $index => $stepNumber):
                    $isActive = $index === 0 ? 'active' : '';
                    ?>
                    <div class="step <?= $isActive ?>" data-step="<?= $stepNumber ?>">
                        <div class="progress-segment"></div>
                        <div class="step-icon"><?= $stepIcons[$stepNumber] ?></div>
                        <span class="step-label"><?= $stepLabels[$stepNumber] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Add hidden field with actual step sequence -->
            <input type="hidden" id="formStepSequence"
                value="<?= htmlspecialchars(json_encode($_SESSION['form_steps'])) ?>">

            <form id="employmentApplication" action="handle_edit_admin.php" method="post" enctype="multipart/form-data">

                <div class="form-step active" data-step="1">

                    <!-- Language Selector Container (shown only when needed) -->
                    <div class="language-container">
                        <div class="language-card">
                            <div class="language-header">
                                <h2 class="language-title">Please select your language<br><span
                                        class="language-subtitle">Por favor seleccione su idioma</span></h2>
                            </div>

                            <div class="language-selector">
                                <select id="multi-language1" name="language" class="language-dropdown">
                                    <option value="en" <?= (isset($_SESSION['lang']) && $_SESSION['lang'] == 'en') ? 'selected' : ''; ?>>English</option>
                                    <option value="es" <?= (isset($_SESSION['lang']) && $_SESSION['lang'] == 'es') ? 'selected' : ''; ?>>Español</option>
                                </select>


                                <div class="dropdown-arrow"></div>
                            </div>

                            <button id="confirmLanguage" class="btn btn-primary language-confirm next-step">
                                <?php echo lang("form_confirm_language"); ?>
                                <svg class="confirm-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <style>
                    .language-container {
                        display: flex;
                        justify-content: center;
                        padding: 2rem;
                        margin-top: 0px;
                        /* Added margin from top */
                    }

                    .language-card {
                        border-radius: 16px;
                        width: 100%;
                        max-width: 400px;
                        text-align: center;
                        transition: transform 0.3s ease;
                        margin-top: 0px;
                        /* Added to push higher */
                    }

                    .language-card:hover {
                        transform: translateY(-5px);
                    }

                    .language-header {
                        margin-bottom: 2rem;
                    }

                    .language-icon {
                        width: 48px;
                        height: 48px;
                        fill: #FF5500;
                        margin-bottom: 1rem;
                    }

                    .language-title {
                        font-size: 1.5rem;
                        color: rgb(41, 49, 63);
                        margin: 0;
                        line-height: 1.4;
                    }

                    .language-subtitle {
                        font-size: 1rem;
                        color: #718096;
                        display: block;
                        margin-top: 0.5rem;
                    }

                    .language-selector {
                        position: relative;
                        margin: 1.5rem 0;
                    }

                    .language-dropdown {
                        width: 100%;
                        padding: 12px 16px;
                        border: 2px solid #e2e8f0;
                        border-radius: 8px;
                        appearance: none;
                        font-size: 1rem;
                        background: white;
                        color: #2d3748;
                        transition: border-color 0.3s ease;
                    }

                    .language-dropdown:focus {
                        outline: none;
                        border-color: #FF5500;
                        box-shadow: 0 0 0 3px rgba(255, 85, 0, 0.1);
                    }

                    .dropdown-arrow {
                        position: absolute;
                        right: 16px;
                        top: 50%;
                        transform: translateY(-50%);
                        width: 0;
                        height: 0;
                        border-left: 6px solid transparent;
                        border-right: 6px solid transparent;
                        border-top: 6px solid #718096;
                        pointer-events: none;
                    }

                    .language-confirm {
                        width: 100%;
                        padding: 12px;
                        font-size: 1rem;
                        font-weight: 600;
                        background: #FF5500;
                        border: none;
                        border-radius: 8px;
                        transition: all 0.3s ease;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                    }

                    .language-confirm:hover {
                        background: #E64A00;
                        transform: translateY(-2px);
                    }

                    .language-confirm:active {
                        transform: translateY(0);
                    }

                    .confirm-icon {
                        width: 20px;
                        height: 20px;
                        fill: white;
                    }
                </style>


                <input type="hidden" id="formStepSequence" name="form_step_sequence"
                    value="<?php echo htmlspecialchars(json_encode($_SESSION['form_steps'])); ?>">
                <input type="hidden" name="applicant_ID" value="<?php echo htmlspecialchars($applicantId); ?>">
                <!-- Step 2 of the Multi-Step Form: Contract  -->

                <div class="form-step" data-step="2">
                    <input type="hidden" name="applicant_ID" value="<?php echo htmlspecialchars($applicantId); ?>">
                    <h3>Employee Information</h3>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="firstName" class="form-label"><?php echo lang("form_first_name"); ?> <span
                                    class="text-danger"></span></label>
                            <input type="text" class="form-control bg-light" id="firstName" name="first_name"
                                value="<?= htmlspecialchars($craft_data['first_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="lastName" class="form-label"><?php echo lang("form_last_name"); ?> <span
                                    class="text-danger"></span></label>
                            <input type="text" class="form-control bg-light" id="lastName" name="last_name"
                                value="<?= htmlspecialchars($craft_data['last_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="address" class="form-label"><?php echo lang("forms_address"); ?></label>
                            <input type="text" class="form-control bg-light" name="street_address" id="address"
                                placeholder="Enter Address"
                                value="<?= htmlspecialchars($craft_data['street_address'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 my-2">
                           <label for="dob" class="form-label"><?php echo lang("Date_birth"); ?><span class="text-danger"></span></label>
                           <input type="date" class="form-control" id="dob" name="dob"
                           value="<?= htmlspecialchars($craft_data['dob'] ?? '') ?>" required>
                       </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="city" class="form-label"><?php echo lang("forms_City"); ?></label>
                            <input type="text" class="form-control bg-light" name="city" id="city"
                                value="<?= htmlspecialchars($craft_data['city'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 form-label-inline">
                            <p for="state" class="form-label">State:</p>
                            <select class="form-control" name="state" id="state">
                                <option value="">Select State</option>
                                <option value="Alabama" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Alabama' ? 'selected' : '') ?>>Alabama</option>
                                <option value="Alaska" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Alaska' ? 'selected' : '') ?>>Alaska</option>
                                <option value="Arizona" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Arizona' ? 'selected' : '') ?>>Arizona</option>
                                <option value="Arkansas" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Arkansas' ? 'selected' : '') ?>>Arkansas</option>
                                <option value="California" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'California' ? 'selected' : '') ?>>California</option>
                                <option value="Colorado" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Colorado' ? 'selected' : '') ?>>Colorado</option>
                                <option value="Connecticut" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Connecticut' ? 'selected' : '') ?>>Connecticut</option>
                                <option value="Delaware" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Delaware' ? 'selected' : '') ?>>Delaware</option>
                                <option value="Florida" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Florida' ? 'selected' : '') ?>>Florida</option>
                                <option value="Georgia" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Georgia' ? 'selected' : '') ?>>Georgia</option>
                                <option value="Hawaii" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Hawaii' ? 'selected' : '') ?>>Hawaii</option>
                                <option value="Idaho" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Idaho' ? 'selected' : '') ?>>Idaho</option>
                                <option value="Illinois" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Illinois' ? 'selected' : '') ?>>Illinois</option>
                                <option value="Indiana" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Indiana' ? 'selected' : '') ?>>Indiana</option>
                                <option value="Iowa" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Iowa' ? 'selected' : '') ?>>Iowa</option>
                                <option value="Kansas" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Kansas' ? 'selected' : '') ?>>Kansas</option>
                                <option value="Kentucky" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Kentucky' ? 'selected' : '') ?>>Kentucky</option>
                                <option value="Louisiana" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Louisiana' ? 'selected' : '') ?>>Louisiana</option>
                                <option value="Maine" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Maine' ? 'selected' : '') ?>>Maine</option>
                                <option value="Maryland" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Maryland' ? 'selected' : '') ?>>Maryland</option>
                                <option value="Massachusetts" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Massachusetts' ? 'selected' : '') ?>>Massachusetts</option>
                                <option value="Michigan" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Michigan' ? 'selected' : '') ?>>Michigan</option>
                                <option value="Minnesota" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Minnesota' ? 'selected' : '') ?>>Minnesota</option>
                                <option value="Mississippi" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Mississippi' ? 'selected' : '') ?>>Mississippi</option>
                                <option value="Missouri" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Missouri' ? 'selected' : '') ?>>Missouri</option>
                                <option value="Montana" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Montana' ? 'selected' : '') ?>>Montana</option>
                                <option value="Nebraska" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Nebraska' ? 'selected' : '') ?>>Nebraska</option>
                                <option value="Nevada" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Nevada' ? 'selected' : '') ?>>Nevada</option>
                                <option value="New Hampshire" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'New Hampshire' ? 'selected' : '') ?>>New Hampshire</option>
                                <option value="New Jersey" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'New Jersey' ? 'selected' : '') ?>>New Jersey</option>
                                <option value="New Mexico" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'New Mexico' ? 'selected' : '') ?>>New Mexico</option>
                                <option value="New York" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'New York' ? 'selected' : '') ?>>New York</option>
                                <option value="North Carolina" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'North Carolina' ? 'selected' : '') ?>>North Carolina</option>
                                <option value="North Dakota" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'North Dakota' ? 'selected' : '') ?>>North Dakota</option>
                                <option value="Ohio" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Ohio' ? 'selected' : '') ?>>Ohio</option>
                                <option value="Oklahoma" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Oklahoma' ? 'selected' : '') ?>>Oklahoma</option>
                                <option value="Oregon" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Oregon' ? 'selected' : '') ?>>Oregon</option>
                                <option value="Pennsylvania" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Pennsylvania' ? 'selected' : '') ?>>Pennsylvania</option>
                                <option value="Rhode Island" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Rhode Island' ? 'selected' : '') ?>>Rhode Island</option>
                                <option value="South Carolina" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'South Carolina' ? 'selected' : '') ?>>South Carolina</option>
                                <option value="South Dakota" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'South Dakota' ? 'selected' : '') ?>>South Dakota</option>
                                <option value="Tennessee" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Tennessee' ? 'selected' : '') ?>>Tennessee</option>
                                <option value="Texas" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Texas' ? 'selected' : '') ?>>Texas</option>
                                <option value="Utah" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Utah' ? 'selected' : '') ?>>Utah</option>
                                <option value="Vermont" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Vermont' ? 'selected' : '') ?>>Vermont</option>
                                <option value="Virginia" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Virginia' ? 'selected' : '') ?>>Virginia</option>
                                <option value="Washington" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Washington' ? 'selected' : '') ?>>Washington</option>
                                <option value="West Virginia" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'West Virginia' ? 'selected' : '') ?>>West Virginia</option>
                                <option value="Wisconsin" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Wisconsin' ? 'selected' : '') ?>>Wisconsin</option>
                                <option value="Wyoming" <?= (!empty($craft_data['state']) && $craft_data['state'] == 'Wyoming' ? 'selected' : '') ?>>Wyoming</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="zip" class="form-label"><?php echo lang("forms_Postal_Code"); ?></label>
                            <input type="number" class="form-control bg-light" name="zip_code" id="zip"
                                value="<?= htmlspecialchars($craft_data['zip_code'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="contactPhone1" class="form-label"><?php echo lang("forms_phone"); ?></label>
                            <input type="tel" class="form-control" name="phone_number" id="phone_number"
                                placeholder="(XXX) XXX-XXXX" pattern="\(\d{3}\) \d{3}-\d{4}"
                                title="Phone number should be in the format: (XXX) XXX-XXXX"
                                value="<?= htmlspecialchars($craft_data['phone_number'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label"><?php echo lang("form_email"); ?><span
                                    class="text-danger"></span></label>
                            <input type="email" class="form-control bg-light" id="email" name="email"
                                pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                value="<?= htmlspecialchars($craft_data['email'] ?? '') ?>">
                            <div id="emailError" class="text-danger mt-1" style="display: none;"></div>
                        </div>
                    </div>
                    <h5 class="mb-0 fw-bold mt-4"><?php echo lang("forms_Emergency_Contact_Information"); ?></h5>
                    <div class="row mb-3">

                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="contactName1"
                                class="form-label"><?php echo lang("forms_Contact_Name_1"); ?></label>
                            <input type="text" class="form-control" name="contactName1" id="contactName1"
                                value="<?= htmlspecialchars($craft_data['contact_name1'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="contactAddress1"
                                class="form-label"><?php echo lang("forms_Contact_Address_1"); ?></label>
                            <input type="text" class="form-control" name="contactAddress1" id="contactAddress1"
                                value="<?= htmlspecialchars($craft_data['contact_address1'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="contactPhone1" class="form-label"><?php echo lang("forms_phone"); ?></label>
                            <input type="tel" class="form-control" name="contactPhone1" id="contactPhone1"
                                placeholder="(XXX) XXX-XXXX" pattern="\(\d{3}\) \d{3}-\d{4}"
                                title="Phone number should be in the format: (XXX) XXX-XXXX"
                                value="<?= htmlspecialchars($craft_data['contact_phone1'] ?? '') ?>">


                        </div>
                        <div class="col-md-4 mt-3">
                            <label for="relationship1"
                                class="form-label"><?php echo lang("forms_Relationship"); ?></label>
                            <input type="text" class="form-control" name="relationship1" id="relationship1"
                                value="<?= htmlspecialchars($craft_data['relationship1'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="contactName2"
                                class="form-label"><?php echo lang("forms_Contact_Name_2"); ?></label>
                            <input type="text" class="form-control" name="contactName2" id="contactName2"
                                value="<?= htmlspecialchars($craft_data['contact_name2'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="contactAddress2"
                                class="form-label"><?php echo lang("forms_Contact_Address_2"); ?></label>
                            <input type="text" class="form-control" name="contactAddress2" id="contactAddress2"
                                value="<?= htmlspecialchars($craft_data['contact_address2'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="contactPhone2" class="form-label"><?php echo lang("forms_phone"); ?></label>
                            <input type="tel" class="form-control" name="contactPhone2" id="contactPhone2"
                                placeholder="(XXX) XXX-XXXX" pattern="\(\d{3}\) \d{3}-\d{4}"
                                title="Phone number should be in the format: (XXX) XXX-XXXX"
                                value="<?= htmlspecialchars($craft_data['contact_phone2'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label for="relationship2"
                                class="form-label"><?php echo lang("forms_Relationship"); ?></label>
                            <input type="text" class="form-control" name="relationship2" id="relationship2"
                                value="<?= htmlspecialchars($craft_data['relationship2'] ?? '') ?>">
                        </div>
                    </div>




                    <!-- Navigation buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary prev-step"
                            style="background-color: white; color: #fe5500;"><?php echo lang("form_previous"); ?></button>
                        <button type="button"
                            class="btn btn-primary next-step"><?php echo lang("form_next"); ?></button>
                    </div>
                </div>
                <?php if (in_array(3, $_SESSION['form_steps'])): ?>
                    <!-- Step: Claim Dependent and Other Credits -->
                    <input type="hidden" name="applicant_ID" value="<?php echo htmlspecialchars($applicantId); ?>">
                    <div class="form-step" data-step="3">
                        <h5 class="mb-0 fw-bold"><?php echo lang("forms_Claim_Dependent_and_Other_Credits"); ?></h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="qualifying_children" class="form-label">
                                    <?php echo lang("forms_Multiply_the_number_of_qualifying_children_under_17_by_$2,000"); ?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="qualifying_children"
                                        id="qualifying_children"
                                        value="<?= htmlspecialchars($w4['qualifying_children'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="number_of_other_dependents" class="form-label">
                                    <?php echo lang("forms_Multiply_the_number_of_other_dependents_by_$500"); ?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="number_of_other_dependents"
                                        id="number_of_other_dependents"
                                        value="<?= htmlspecialchars($w4['number_of_other_dependents'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="amount_for_qualifying_children"
                                    class="form-label"><?php echo lang("forms_Add_the amounts above for qualifying_children and other dependents._You may add to this_amount any other credits. Enter the total here"); ?></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="amount_for_qualifying_children"
                                        id="amount_for_qualifying_children"
                                        value="<?= htmlspecialchars($w4['amount_for_qualifying_children'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-0 fw-bold"><?php echo lang("forms_(Optional)_Other_Adjustments"); ?></h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="tax_withheld"
                                    class="form-label"><?php echo lang("forms_Other_income_(not from jobs)"); ?></label>
                                <input type="number" class="form-control" name="tax_withheld" id="tax_withheld"
                                    value="<?= htmlspecialchars($w4['tax_withheld'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="claim_deductions"
                                    class="form-label"><?php echo lang("forms_Deductions"); ?></label>
                                <input type="number" class="form-control" name="claim_deductions" id="claim_deductions"
                                    value="<?= htmlspecialchars($w4['claim_deductions'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="extra_withholding"
                                    class="form-label"><?php echo lang("forms_Extra_Withholding"); ?></label>
                                <input type="number" class="form-control" name="extra_withholding" id="extra_withholding"
                                    value="<?= htmlspecialchars($w4['extra_withholding'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- <h5 class="mb-0 fw-bold"><?php echo lang("forms_Employers_Only"); ?></h5> -->
                        <div class="row mb-3">
                            <!-- <div class="col-md-4">
            <label for="employers_name" class="form-label">Employer's Name</label>
            <input type="text" class="form-control" name="employers_name" id="employers_name" required >
        </div>
        <div class="col-md-4">
            <label for="date_of_employement" class="form-label">First Date of Employment</label>
            <input type="date" class="form-control" name="date_of_employement" id="date_of_employement" >
        </div> -->
                            <!-- <div class="col-md-4">
                                <label for="ein"
                                    class="form-label"><?php echo lang("forms_Employer_Identification_Number_(EIN)"); ?></label>
                                <input type="number" class="form-control" name="ein" id="ein">
                            </div> -->
                        </div>



                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step"
                                style="background-color: white; color: #fe5500;"><?php echo lang("form_previous"); ?></button>
                            <button type="button"
                                class="btn btn-primary next-step"><?php echo lang("form_next"); ?></button>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Step: Quickbook Authorization -->
                <?php if (in_array(4, $_SESSION['form_steps'])): ?>
                    <div class="form-step" data-step="4">
                        <h5 class="mb-0 fw-bold"><?php echo lang("forms_Employee_Direct_Deposit_Authorization"); ?></h5>

                        <!-- Instructions Field -->
                        <!-- <div class="col-md-12">
                            <label for="instructions" class="form-label"><?php echo lang("forms_Instructions"); ?></label>
                            <textarea type="text" class="form-control" name="instructions" id="instructions"></textarea>
                        </div> -->

                        <!-- Bank Name and Percentage Fields in a Single Row -->
                        <div class="row">
                            <div class="col-md-6">
                                <label for="bank_name" class="form-label"><?php echo lang("forms_Bank_Name"); ?></label>
                                <input type="text" class="form-control" name="bank_name" id="bank_name"
                                    value="<?= htmlspecialchars($quick_book['bank_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="percentage"
                                    class="form-label"><?php echo lang("forms_Percentage_to_be_deposited"); ?></label>
                                <input type="number" class="form-control" name="percentage" id="percentage"
                                    value="<?= htmlspecialchars(100) ?>">
                            </div>
                        </div>

                        <!-- Account 1 Section -->
                        <h5 class="mb-2 fw-bold"><?php echo lang("forms_Account_1"); ?></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <!-- <label for="account_1" class="form-label"><?php echo lang("forms_Account_1"); ?></label> -->
                                <!-- <input type="text" class="form-control" name="account_1" id="account_1"
                                    value="<?= htmlspecialchars($quick_book['account_1'] ?? '') ?>"> -->
                            </div>
                          

                            <!-- Account 1 Type (Radio Buttons) -->
                            
                        </div>
                        <div class="row"> 
                        <div class="col-md-6">
                        <label for="account_type" class="form-label" style="font-weight: bold;"><?php echo lang("forms_Account_1_type"); ?>
                         </label><br>
                                <input class="form-check-input me-2" type="radio" name="accountType" id="checking"
                                    value="checking" style="transform: scale(1);" <?= (isset($quick_book['account_type']) && $quick_book['account_type'] == 'checking') ? 'checked' : '' ?>>
                                <label class="form-check-label me-3"
                                    for="checking"><?php echo lang("forms_checking"); ?></label>
                                <input class="form-check-input me-2" type="radio" name="accountType" id="savings"
                                    value="savings" style="transform: scale(1);" <?= (isset($quick_book['account_type']) && $quick_book['account_type'] == 'savings') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="savings"><?php echo lang("forms_savings"); ?></label>
                            </div>
                </div> 
                        <!-- Bank Routing and Account Number Fields (Account 1) -->
                        <div class="row">
                          
                           
                            <div class="col-md-6">
                                <label for="aba_number"
                                    class="form-label"><?php echo lang("forms_Bank_routing_number_(ABA number)"); ?></label>
                                <input type="text" class="form-control" name="aba_number" id="aba_number"
                                    value="<?= htmlspecialchars($quick_book['aba_number'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="account_number"
                                    class="form-label"><?php echo lang("forms_Account_Number"); ?></label>
                                <input type="text" class="form-control" name="account_number" id="account_number"
                                    value="<?= htmlspecialchars($quick_book['account_number'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Account 2 Section -->
                        <!-- <h5 class="mb-2 fw-bold"><?php echo lang("forms_Account_2_(Optional)"); ?></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="account_2" class="form-label"><?php echo lang("forms_Account_2"); ?></label>
                                <input type="text" class="form-control" name="account_2" id="account_2">
                            </div> -->

                        <!-- Account 2 Type (Radio Buttons) -->
                        <!-- <div class="col-md-6">
                                <label for="account_2_type"
                                    class="form-label"><?php echo lang("forms_Account_2_Type"); ?></label><br>
                                <input class="form-check-input me-2" type="radio" name="accountType2" id="checking_2"
                                    value="checking" style="transform: scale(1);">
                                <label class="form-check-label me-3"
                                    for="checking_2"><?php echo lang("forms_checking"); ?></label>
                                <input class="form-check-input me-2" type="radio" name="accountType2" id="savings_2"
                                    value="savings" style="transform: scale(1);">
                                <label class="form-check-label" for="savings_2"><?php echo lang("forms_savings"); ?></label>
                            </div>
                        </div> -->

                        <!-- Bank Routing and Account Number Fields (Account 2) -->
                        <!-- <div class="row">
                            <div class="col-md-6">
                                <label for="aba_number_2"
                                    class="form-label"><?php echo lang("forms_Bank_routing_number_(ABA number)"); ?></label>
                                <input type="text" class="form-control" name="aba_number_2" id="aba_number_2">
                            </div>
                            <div class="col-md-6">
                                <label for="account_number_2"
                                    class="form-label"><?php echo lang("forms_Account_Number"); ?></label>
                                <input type="text" class="form-control" name="account_number_2" id="account_number_2">
                            </div>
                        </div> -->

                        <!-- Authorization and Company Fields -->
                        <div class="row">
                            <!-- <div class="col-md-6">
                                <label for="authorization"
                                    class="form-label"><?php echo lang("forms_Authorization"); ?></label>
                                <input type="text" class="form-control" name="authorization" id="authorization"
                                    value="<?= htmlspecialchars($quick_book['authorization'] ?? '') ?>">
                            </div> -->
                            <!-- <div class="col-md-6">
                                <label for="authorizes_company"
                                    class="form-label"><?php echo lang("forms_Authorizes_Company"); ?></label>
                                <input type="text" class="form-control" name="authorizes_company" id="authorizes_company"
                                    value="<?= htmlspecialchars($quick_book['authorizes_company'] ?? '') ?>">
                            </div> -->
                        </div>

                        <!-- Employee ID Fields -->
                        <div class="row">

                            <!-- <div class="col-md-6">
                                <label for="print_name" class="form-label"><?php echo lang("forms_Print_name"); ?></label>
                                <input type="text" class="form-control" name="print_name" id="print_name">
                            </div>
                            <div class="col-md-6">
                                <label for="employee_id"
                                    class="form-label"><?php echo lang("forms_Employee_ID#"); ?></label>
                                <input type="number" class="form-control" name="employee_id" id="employee_id">
                            </div> -->
                        </div>

                        <!--  Date Fields -->
                        <div class="row">
                            <!-- <div class="col-md-6">
            <label for="signature" class="form-label">Signature</label>
            <input type="text" class="form-control" name="signature" id="signature" >
        </div> -->
                            <!-- <div class="col-md-6">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" name="date" id="date" >
        </div> -->
                        </div>
                        
                        <div class="col-md-6">
                                    <div class="form-group mb-3">
                                    <?php if (!empty($signature['signature']) && strpos($signature['signature'], 'data:image/') === 0): ?>
                                        <img src="<?= htmlspecialchars($signature['signature']) ?>" alt="Applicant Signature"
                                            style="background: transparent;">
                                    <?php else: ?>
                                        <!-- <div class="no-signature">No signature found</div> -->
                                    <?php endif; ?>
                                    </div>
                                    </div>
                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step"
                                style="background-color: white; color: #fe5500;"><?php echo lang("forms_Previous"); ?></button>
                            <button type="button"
                                class="btn btn-primary next-step"><?php echo lang("form_next"); ?></button>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Step: Employment Eligibility Verification -->
                <?php if (in_array(5, $_SESSION['form_steps'])): ?>
                    <div class="form-step" data-step="5">
                        <h5 class="mb-0 fw-bold"><?php echo lang("forms_U.S._Citizenship_and_Immigration_Services"); ?></h5>
                        <!-- <div class="row">
                            <div class="col-md-6">
                                <label for="first_name"
                                    class="form-label"><?php echo lang("forms_First_Name_(Given_Name)"); ?></label>
                                <input type="text" class="form-control" name="first_name" id="first_name">
                            </div>
                            <div class="col-md-6">
                                <label for="last_name"
                                    class="form-label"><?php echo lang("forms_Last_Name_(Family_Name)"); ?></label>
                                <input type="text" class="form-control" name="last_name" id="last_name">
                            </div>
                        </div> -->

                        <!-- <div class="row">
                            <div class="col-md-6">
                                <label for="middle_name" class="form-label"><?php echo lang("forms_Middle_Name"); ?></label>
                                <input type="text" class="form-control" name="middle_name" id="middle_name">
                            </div>
                            <div class="col-md-6">
                                <label for="other_last_name"
                                    class="form-label"><?php echo lang("forms_Other_Last_Name_Used(If Any)"); ?></label>
                                <input type="text" class="form-control" name="other_last_name" id="other_last_name">
                            </div>

                        </div> -->

                        <div class="row">
                            <!-- <div class="col-md-6">
                                <label for="mi" class="form-label"><?php echo lang("forms_M.I."); ?></label>
                                <input type="text" class="form-control" name="mi" id="mi">
                            </div> -->
                            <div class="col-md-6">
                                <label for="citizen_immigration_status"
                                    class="form-label"><?php echo lang("forms_Citizenship/Immigration_Status"); ?></label>
                                <input type="text" class="form-control" name="citizen_immigration_status"
                                    id="citizen_immigration_status"
                                    value="<?= htmlspecialchars($verification['citizen_immigration_status'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="ssn" class="form-label"><?php echo lang("form_social_security"); ?> <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ssn" name="ssn" placeholder="XXX-XX-XXXX"
                                    required pattern="^\d{3}-\d{2}-\d{4}$"
                                    title="Format: XXX-XX-XXXX (only numbers allowed)" maxlength="11"
                                    value="<?= htmlspecialchars($verification['ssn'] ?? '') ?>">
                            </div>


                        </div>


                        <h5 class="mb-0 fw-bold"><?php echo lang("forms_Identity_and_Employment_Authorization_(List A)"); ?>
                        </h5>

                        <div class="row">
                            <div class="col-md-3">
                                <label for="document_title"
                                    class="form-label"><?php echo lang("forms_Document_Title"); ?></label>
                                <input type="text" class="form-control" name="document_title" id="document_title"
                                    value="<?= htmlspecialchars($verification['document_title'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="issuing_authority"
                                    class="form-label"><?php echo lang("forms_Issuing_Authority"); ?></label>
                                <input type="text" class="form-control" name="issuing_authority" id="issuing_authority"
                                    value="<?= htmlspecialchars($verification['issuing_authority'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="document_number"
                                    class="form-label"><?php echo lang("forms_Documen_Number"); ?></label>
                                <input type="number" class="form-control" name="document_number" id="document_number"
                                    value="<?= htmlspecialchars($verification['document_number'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="expiration_date"
                                    class="form-label"><?php echo lang("forms_Expiration_Date_(if any)"); ?></label>
                                <input type="date" class="form-control" name="expiration_date" id="expiration_date"
                                    value="<?= htmlspecialchars($verification['expiration_date'] ?? '') ?>">
                            </div>
                        </div>
                        <h5 class="mb-0 fw-bold"><?php echo lang("forms_Identity_and_Employment_Authorization_(List B)"); ?>
                        </h5>
                        <!-- Additional Document Sections -->
                        <div class="row">
                            <div class="col-md-3">
                                <label for="document_title_1"
                                    class="form-label"><?php echo lang("forms_Document_Title"); ?></label>
                                <input type="text" class="form-control" name="document_title_1" id="document_title_1"
                                    value="<?= htmlspecialchars($verification['document_title_1'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="issuing_authority_1"
                                    class="form-label"><?php echo lang("forms_Issuing_Authority"); ?></label>
                                <input type="text" class="form-control" name="issuing_authority_1" id="issuing_authority_1"
                                    value="<?= htmlspecialchars($verification['issuing_authority_1'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="document_number_1"
                                    class="form-label"><?php echo lang("forms_Document_Number"); ?></label>
                                <input type="number" class="form-control" name="document_number_1" id="document_number_1"
                                    value="<?= htmlspecialchars($verification['document_number_1'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="expiration_date_1"
                                    class="form-label"><?php echo lang("forms_Expiration_Date_(if any)"); ?></label>
                                <input type="date" class="form-control" name="expiration_date_1" id="expiration_date_1"
                                    value="<?= htmlspecialchars($verification['expiration_date_1'] ?? '') ?>">
                            </div>
                        </div>
                        <h5 class="mb-0 fw-bold"><?php echo lang("forms_Identity_and_Employment_Authorization_(List C)"); ?>
                        </h5>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="document_title_2"
                                    class="form-label"><?php echo lang("forms_Document_Document_Title"); ?></label>
                                <input type="text" class="form-control" name="document_title_2" id="document_title_2"
                                    value="<?= htmlspecialchars($verification['document_title_2'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="issuing_authority_2"
                                    class="form-label"><?php echo lang("forms_Document_Issuing_Authority"); ?></label>
                                <input type="text" class="form-control" name="issuing_authority_2" id="issuing_authority_2"
                                    value="<?= htmlspecialchars($verification['issuing_authority_2'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="document_number_2"
                                    class="form-label"><?php echo lang("forms_Document_Number"); ?></label>
                                <input type="number" class="form-control" name="document_number_2" id="document_number_2"
                                    value="<?= htmlspecialchars($verification['document_number_2'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="expiration_date_2"
                                    class="form-label"><?php echo lang("forms_Expiration_Date_(if any)"); ?></label>
                                <input type="date" class="form-control" name="expiration_date_2" id="expiration_date_2"
                                    value="<?= htmlspecialchars($verification['expiration_date_2'] ?? '') ?>">
                            </div>
                        </div>

                        <h5 class="mb-0 fw-bold">
                            <?php echo lang("forms_Reverification_and_Rehires_A._New_Name_(if applicable)"); ?>
                        </h5>

                        <div class="row">
                            <div class="col-md-4">
                                <label for="first_name_1" class="form-label"><?php echo lang("forms_First_Name"); ?></label>
                                <input type="text" class="form-control" name="first_name_1" id="first_name_1"
                                    value="<?= htmlspecialchars($verification['first_name_1'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="last_name_1" class="form-label"><?php echo lang("forms_Last_Name"); ?></label>
                                <input type="text" class="form-control" name="last_name_1" id="last_name_1"
                                    value="<?= htmlspecialchars($verification['last_name_1'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="middle_initial"
                                    class="form-label"><?php echo lang("forms_Middle_Initial"); ?></label>
                                <input type="text" class="form-control" name="middle_initial" id="middle_initial"
                                    value="<?= htmlspecialchars($verification['middle_initial'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label for="rehire_date" class="form-label"><?php echo lang("forms_Date"); ?></label>
                                <input type="date" class="form-control" name="rehire_date" id="rehire_date"
                                    value="<?= htmlspecialchars($verification['rehire_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="marital_status" class="form-label">
                                    <?php echo lang("forms_select"); ?>
                                </label>
                                <select class="form-control" name="marital_status" id="marital_status">
                                    <option value=""><?php echo lang("select"); ?></option>
                                    <option value="Single or Married filing separately" 
                                            <?= htmlspecialchars($verification['marital_status'] ?? '') == 'Single or Married filing separately' ? 'selected' : '' ?>>
                                        <?php echo lang("forms_Single_or_Married_filing_separately"); ?>
                                    </option>
                                    <option value="Married filing jointly or Qualifying surviving spouse"
                                            <?= htmlspecialchars($verification['marital_status'] ?? '') == 'Married filing jointly or Qualifying surviving spouse' ? 'selected' : '' ?>>
                                        <?php echo lang("forms_Married_filing_jointly_or_Qualifying_surviving_spouse"); ?>
                                    </option>
                                    <option value="Head of Household"
                                            <?= htmlspecialchars($verification['marital_status'] ?? '') == 'Head of Household' ? 'selected' : '' ?>>
                                        <?php echo lang("forms_Head_of_Household"); ?>
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- <h5 class="mb-0 fw-bold"><?php echo lang("forms_Agreement"); ?></h5>
<h6 class="mb-0"><?php echo lang("forms_I agree that :_If the_employee's previous grant of_employment authorization has expired,_provide the information for the document or_receipt that establishes continuing employment authorization in the space_provided_below."); ?></h6> -->

                        <!-- <div class="row">
    <div class="col-md-4">
        <label for="document_title_5" class="form-label"><?php echo lang("forms_Document_Title"); ?></label>
        <input type="text" class="form-control" name="document_title_5" id="document_title_5">
    </div>
    <div class="col-md-4">
        <label for="document_number_5" class="form-label"><?php echo lang("forms_Document_Number"); ?></label>
        <input type="number" class="form-control" name="document_number_5" id="document_number_5">
    </div>
    <div class="col-md-4">
        <label for="expiration_date_4" class="form-label"><?php echo lang("forms_Expiration_Date"); ?></label>
        <input type="date" class="form-control" name="expiration_date_4" id="expiration_date_4">
    </div>
</div> -->
                        <!-- <h5 class="mb-0 fw-bold"><?php echo lang("forms_Agreement"); ?></h5>
<h6 class="mb-0"><?php echo lang("forms_I agree that : under penalty of perjury,_that to_the best of_my_knowledge, this employee is_authorized to work_in the United States, and if the employee presented document(s), the document(s) I have examined appear to be genuine and to relate to the individual."); ?></h6> -->


                        <!-- Navigation Buttons -->
                        <!-- <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step"
                                style="background-color: white; color: #fe5500;"><?php echo lang("forms_Previous"); ?></button>
                            <button type="button"
                                class="btn btn-primary next-step"><?php echo lang("form_next"); ?></button>
                        </div> -->
                    <?php endif; ?>

                    <!-- Step: Employment Eligibility Verification -->
                    <!-- <?php if (in_array(5, $_SESSION['form_steps'])): ?>
                    <div class="form-step" data-step=""> -->
                        <!-- <h5 class="mb-0 fw-bold"><?php echo lang("forms_U.S._Citizenship_and_Immigration_Services 2"); ?></h5> -->
                        <!--  <div class="row">
        
    </div> -->

                        <!-- <h5 class="mb-0 fw-bold">Employee Information and Attestation</h5>
    <div class="row">
        <div class="col-md-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" name="address" id="address">
        </div>
        <div class="col-md-3">
            <label for="apt_number" class="form-label">Apt. Number</label>
            <input type="text" class="form-control" name="apt_number" id="apt_number">
        </div>
        <div class="col-md-3">
            <label for="city" class="form-label">City Or Town</label>
            <input type="text" class="form-control" name="city" id="city">
        </div>
        <div class="col-md-4">
            <label for="state" class="form-label">State</label>
            <input type="text" class="form-control" name="state" id="state" placeholder="Enter State">
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <label for="zip" class="form-label">Zip</label>
            <input type="text" class="form-control" name="zip" id="zip">
        </div>
         
        
        <div class="col-md-3">
            <label for="employee_email" class="form-label">Employee's Email Address</label>
            <input type="email" class="form-control" name="employee_email" id="employee_email">
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <label for="employee_telephone" class="form-label">Employee's Telephone Number</label>
            <input type="text" class="form-control" name="employee_telephone" id="employee_telephone">
        </div>
    </div> -->

                        <!-- <h6 class="mb-0"><?php echo lang("forms_I am aware that federal law provides for imprisonment and/or fines for false statements_or use of false_documents in connection_with the completion of this form."); ?></h6> -->
                        <h6 class="mb-0">
                            *<?php echo lang("forms_I attest, under penalty of_perjury, that I_am (check one of the following boxes):"); ?>
                        </h6>

                        <div class="row">
                            <div class="col-md-6 form-check-inline">
                                <input class="form-check-input" type="radio" name="citizenship_status" id="citizen_of_us"
                                    value="U.S Citizen" <?= (isset($verification_1['citizenship_status']) && $verification_1['citizenship_status'] == 'U.S Citizen') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="citizen_of_us">
                                    <?= lang("forms_A Citizen_Of_United_States"); ?>
                                </label>
                            </div>

                            <div class="col-md-6 form-check-inline">
                                <input class="form-check-input" type="radio" name="citizenship_status" id="noncitizen_of_us"
                                    value="Non U.S Citizen" <?= (isset($verification_1['citizenship_status']) && $verification_1['citizenship_status'] == 'Non U.S Citizen') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="noncitizen_of_us">
                                    <?= lang("forms_A Noncitizen Of United States"); ?>
                                </label>
                            </div>

                            <div class="col-md-12 form-check-inline">
                                <input class="form-check-input" type="radio" name="citizenship_status"
                                    id="lawful_permanent_resident" value="Lawful Permanent Resident"
                                    <?= (isset($verification_1['citizenship_status']) && $verification_1['citizenship_status'] == 'Lawful Permanent Resident') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="lawful_permanent_resident">
                                    <?= lang("forms_A lawful permanent resident_(Alien Registration Number/USCIS Number):"); ?>
                                </label>
                            </div>

                            <div class="col-md-12 form-check-inline">
                                <input class="form-check-input" type="radio" name="citizenship_status"
                                    id="allen_authorized_work" value="Allen Authorized Worker"
                                    <?= (isset($verification_1['citizenship_status']) && $verification_1['citizenship_status'] == 'Allen Authorized Worker') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="allen_authorized_work">
                                    <?= lang("forms_An alien authorized to work_until_(expiration date, if applicable):"); ?>
                                </label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label for="registration_number"
                                    class="form-label"><?php echo lang("forms_Registration_Number"); ?></label>
                                <input type="text" class="form-control" name="registration_number" id="registration_number"
                                    value="<?= htmlspecialchars($verification_1['registration_number'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="expiration_date"
                                    class="form-label"><?php echo lang("forms_Expiration_Date"); ?></label>
                                <input type="date" class="form-control" name="expiration_date" id="expiration_date"
                                    value="<?= htmlspecialchars($verification_1['expiration_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="allen_registration_number"
                                    class="form-label"><?php echo lang("forms_Allen_Registration_Number"); ?></label>
                                <input type="text" class="form-control" name="allen_registration_number"
                                    id="allen_registration_number"
                                    value="<?= htmlspecialchars($verification_1['allen_registration_number'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label for="passport_number"
                                    class="form-label"><?php echo lang("forms_Allen_Passport_Number"); ?></label>
                                <input type="text" class="form-control" name="passport_number" id="passport_number"
                                    value="<?= htmlspecialchars($verification_1['passport_number'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="country_of_issuance"
                                    class="form-label"><?php echo lang("forms_Country_Of_Issuance"); ?></label>
                                <input type="text" class="form-control" name="country_of_issuance" id="country_of_issuance"
                                    value="<?= htmlspecialchars($verification_1['country_of_issuance'] ?? '') ?>">
                            </div>
                        </div>



                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step"
                                style="background-color: white; color: #fe5500;"><?php echo lang("forms_Previous"); ?></button>
                            <button type="button"
                                class="btn btn-primary next-step"><?php echo lang("form_next"); ?></button>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Step: MVR Released -->
                <?php if (in_array(6, $_SESSION['form_steps'])): ?>
                    <div class="form-step" data-step="6">
                        <h5 class="mb-0 fw-bold"><?php echo lang("forms_MVR_RELEASE_CONSENT_FORM"); ?></h5>

                        <!-- Company Name and Applicant Name Fields -->
                        <div class="row">
                            <!-- <div class="col-md-4">
                                <label for="company_name"
                                    class="form-label"><?php echo lang("forms_Company_Name"); ?></label>
                                <input type="text" class="form-control" name="company_name" id="company_name">
                            </div> -->

                        </div>

                        <!-- Date and License Number Fields -->
                        <div class="row p-4">

                            <div class="col-md-4">
                                <label for="license_number"
                                    class="form-label"><?php echo lang("forms_Driver's_License_Number"); ?></label>
                                <input type="text" class="form-control" name="license_number" id="license_number"
                                    value="<?= htmlspecialchars($mvr['license_number'] ?? '') ?>">
                            </div>

                        </div>

                        <!-- License Upload Section -->
                        <!-- License Upload Section -->
<div class="row">
    <!-- Front Side -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><?php echo lang("forms_License_Front_Side"); ?></h5>
                <div class="file-upload-wrapper">
                    <label for="licenseFront" id="frontLabel" class="license-upload fw-bold fs-5" style="font-size: 18px !important; color: black !important;">
                        Upload Front Photo
                    </label>
                    <input type="file" name="license_front" id="licenseFront" class="license-input"
                        accept="image/*,application/pdf" capture="environment"
                        onchange="previewLicense(event, 'frontPreview'); updateLabel(event, 'frontLabel');">
                    <div id="frontPreview" class="preview-container mt-3"></div>
                    <?php if (!empty($mvr['license_front_filename'])): ?>
                        <img src="uploads/licenses/<?= htmlspecialchars($mvr['license_front_filename']) ?>"
                            class="img-thumbnail">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Side -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><?php echo lang("forms_License_Back_Side"); ?></h5>
                <div class="file-upload-wrapper">
                    <label for="licenseBack" id="backLabel" class="license-upload fw-bold fs-5" style="font-size: 18px !important; color: black !important;">
                        Upload Back Photo
                    </label>
                    <input type="file" name="license_back" id="licenseBack" class="license-input"
                        accept="image/*,application/pdf" capture="environment"
                        onchange="previewLicense(event, 'backPreview'); updateLabel(event, 'backLabel');">
                    <div id="backPreview" class="preview-container mt-3"></div>
                    <?php if (!empty($mvr['license_back_filename'])): ?>
                        <img src="uploads/licenses/<?= htmlspecialchars($mvr['license_back_filename']) ?>"
                            class="img-thumbnail">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button"
                                class="btn btn-outline-secondary prev-step"><?php echo lang("forms_Previous"); ?></button>
                            <button type="button"
                                class="btn btn-primary next-step"><?php echo lang("form_next"); ?></button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (in_array(7, $_SESSION['form_steps'])): ?>
                    <!-- Step: Agreement -->
                    <div class="form-step" data-step="7">
                        <!-- Add Non-Compete Agreement Section -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h4 class="mb-3">NON-COMPETE AGREEMENT</h4>

                                <div class="non-compete-content mb-4" style="border: 1px solid #ddd; padding: 20px;">
                                    <p>This Non-Compete Agreement ("Agreement") is entered into on this
                                        <?php echo date('j'); ?> day of <?php echo date('F'); ?>, <?php echo date('Y'); ?>,
                                        by and between:</p>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Employer:</strong><br>
                                            Craft Contracting, LLC<br>
                                            Address: 14130 Meridian Pkwy, Riverside, CA 92508<br>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Employee:</label>
                                                <?php
                                                if (!empty($craft_data['first_name']) || !empty($craft_data['last_name'])) {
                                                    $employee_name = trim(($craft_data['first_name'] ?? '') . ' ' . ($craft_data['last_name'] ?? ''));
                                                    $employee_name = htmlspecialchars($employee_name);
                                                } else {
                                                    $employee_name = 'N/A';
                                                }
                                                ?>
                                                <input type="text" class="form-control bg-light"
                                                    value="<?= $employee_name ?>" readonly>
                                            </div>

                                            <div class="form-group">
                                                <label>Address:</label>
                                                <?php
                                                if (!empty($craft_data['street_address']) || !empty($craft_data['city']) || !empty($craft_data['state'])) {
                                                    $address = trim(($craft_data['street_address'] ?? '') . ', ' . ($craft_data['city'] ?? '') . ', ' . ($craft_data['state'] ?? ''));
                                                    $address = htmlspecialchars($address);
                                                } else {
                                                    $address = 'N/A';
                                                }
                                                ?>
                                                <input type="text" class="form-control" value="<?= $address ?>" readonly>
                                            </div>

                                        </div>

                                    </div>

                                    <!-- Agreement Content -->
                                    <div class="agreement-text">
                                        <h6>1. Purpose</h6>
                                        <p>The purpose of this Agreement is to protect the legitimate business interests of
                                            the Employer, including its confidential methods, trade secrets, customer
                                            relationships, and specialized experience related to the installation,
                                            dismantling, or servicing of Sprung structures.</p>

                                        <h6>2. Non-Compete Obligation</h6>
                                        <p>For a period of three (3) years following the termination of Employee's
                                            employment with Employer, whether voluntary or involuntary, the Employee shall
                                            not, directly or indirectly:</p>
                                        <ul>
                                            <li>Perform any work, services, or provide labor (as an employee, independent
                                                contractor, consultant, or otherwise) for any entity or individual involved
                                                in the installation, dismantling, service, or construction of Sprung brand
                                                structures, within the state of Ohio or any state in which Craft
                                                Contracting, LLC has an active project at the time of termination.</li>
                                        </ul>

                                        <h6>3. Confidentiality</h6>
                                        <p>Employees agree not to disclose or use any confidential or proprietary
                                            information obtained during their employment, including but not limited to
                                            project procedures, pricing, client lists, and material specifications,
                                            especially as it relates to Sprung structures.</p>

                                        <h6>4. Acknowledgment</h6>
                                        <p>Employee acknowledges that this restriction is reasonable in scope and necessary
                                            to protect the Employer's interests, and that employment with Employer
                                            constitutes sufficient consideration for entering this Agreement.</p>

                                        <h6>5. Governing Law</h6>
                                        <p>This Agreement shall be governed by and construed in accordance with the laws of
                                            the State of Ohio.</p>

                                        <h6>6. Enforcement</h6>
                                        <p>If any provision is deemed unenforceable, the remaining provisions shall remain
                                            in effect. Employer shall be entitled to injunctive relief, in addition to any
                                            other legal remedies, in the event of a breach.</p>

                                        <br>
                                        <p><strong>IN WITNESS WHEREOF, the parties have executed this Agreement as of the
                                                date written below.</strong></p>

                                    </div>

                                    <!-- Signature Section -->
                                     
                                    <div class="col-md-6">
                                    <div class="form-group mb-3">
                                    <?php if (!empty($signature['signature']) && strpos($signature['signature'], 'data:image/') === 0): ?>
                                        <img src="<?= htmlspecialchars($signature['signature']) ?>" alt="Applicant Signature"
                                            style="background: transparent;">
                                    <?php else: ?>
                                        <!-- <div class="no-signature">No signature found</div> -->
                                    <?php endif; ?>
                                    </div>
                                    </div>




                                    <div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Date <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="signature_date"
                                                    value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Agreement Checkbox -->
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" id="agreeTerms" name="agreed_terms"
                                            value="1" <?= htmlspecialchars($non_compete['agreed_terms'] ?? '') == '1' ? 'checked' : ''; ?> 
                                            style="transform: scale(1.5);" > <!-- Adjust scale to increase size -->
                                        <label class="form-check-label" for="agreeTerms">
                                            I agree to the terms and conditions of the Non-Compete Agreement.
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button"
                                class="btn btn-outline-secondary prev-step"><?php echo lang("forms_Previous"); ?></button>
                            <button type="button"
                                class="btn btn-primary next-step"><?php echo lang("form_next"); ?></button>
                        </div>
                    </div>
                <?php endif; ?>



                <!-- Certification -->
                <div class="form-step" data-step="8">

                    <!-- <h3><?php echo lang("form_certification"); ?></h3>
            <div class="mb-3">
                <p>
                    <?php echo lang("form_agree1"); ?>
                </p>
                <p>
                    <?php echo lang("form_agree2"); ?>
                </p>
                <p>
                    <?php echo lang("form_agree3"); ?>
                </p>
            </div>  -->
                    <!-- certification -->
                    <h5 class="mb-0 fw-bold"><?php echo lang("forms_Certification"); ?></h5>
                    <!-- <div class="row">
                        <div class="col-md-6">
                            <label for="employee_start_date"
                                class="form-label"><?php echo lang("forms_First_Day_of_Employment"); ?></label>
                            <input type="date" class="form-control" name="employee_start_date" id="employee_start_date">
                        </div>
                        <div class="col-md-6">
                            <label for="employer_signature_1"
                                class="form-label"><?php echo lang("forms_Signature of_Employer_or Authorized Representative"); ?></label>
                            <input type="text" class="form-control" name="employer_signature_1"
                                id="employer_signature_1">
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <label for="employer_title"
                                class="form-label"><?php echo lang("forms_Title of_Employer or_Authorized Representative"); ?></label>
                            <input type="text" class="form-control" name="employer_title" id="employer_title">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="employee_first_name"
                                class="form-label"><?php echo lang("forms_First Name_of_Employer or_Authorized Representative"); ?></label>
                            <input type="text" class="form-control" name="employee_first_name" id="employee_first_name">
                        </div>
                        <div class="col-md-6">
                            <label for="employer_last_name"
                                class="form-label"><?php echo lang("forms_Last Name_of_Employer or Authorized Representative"); ?></label>
                            <input type="text" class="form-control" name="employer_last_name" id="employer_last_name">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="employer_business_name"
                                class="form-label"><?php echo lang("forms_Employer's_Business or_Organization Name"); ?></label>
                            <input type="text" class="form-control" name="employer_business_name"
                                id="employer_business_name">
                        </div>
                    </div>

                    <h5 class="mb-0 fw-bold"><?php echo lang("forms_Employer's Business_or Organization_Address"); ?>
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="employer_address"
                                class="form-label"><?php echo lang("forms_Employer's Address"); ?></label>
                            <input type="text" class="form-control" name="employer_address" id="employer_address">
                        </div>
                        <div class="col-md-6">
                            <label for="employer_city"
                                class="form-label"><?php echo lang("forms_City or Town"); ?></label>
                            <input type="text" class="form-control" name="employer_city" id="employer_city">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="state" class="form-label"><?php echo lang("forms_state"); ?></label>
                            <input type="text" class="form-control" name="state" id="state" placeholder="Enter State">
                        </div>
                        <div class="col-md-6">
                            <label for="employer_zip" class="form-label"><?php echo lang("forms_Zip_Code"); ?></label>
                            <input type="number" class="form-control" name="employer_zip" id="employer_zip">
                        </div>
                    </div> -->

                    <!-- Add PDF Upload Field Here -->
                    <!-- HTML Changes -->
                    <div class="col-md-8">
    <div class="file-upload-wrapper">
        <label for="license_file" class="form-label">
        <?php echo lang("forms_certificates_label"); ?>
<br>
<br>
            <?php echo lang("forms_Upload_License"); ?>
            <span class="text-muted">(<?php echo lang("forms_upload_file"); ?>)</span>
        </label>

        <div class="input-group">
            <input type="file" class="form-control" name="license_files[]" id="license_file"
                accept=".pdf,.jpg,.jpeg,.png" multiple onchange="previewFile()">
            <?php
            $existing_files = DB::query("SELECT * FROM certification_files WHERE applicant_id = %i", $applicantId);
            if (!empty($existing_files)):
                ?>
                <div class="existing-files mt-3">
                    <h6><?php echo lang("forms_Uploaded_Files"); ?>:</h6>
                    <ul class="list-group">
                        <?php foreach ($existing_files as $file):
                            $file_path = 'ajax_helpers/uploads/certifications/' . $file['file_name'];
                            $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file_path;
                            $file_exists = file_exists($full_path);
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="file-info d-flex align-items-center">
                                    <?php
                                    $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                                    $icon_class = 'fa-file';
                                    $icon_color = 'text-primary';

                                    if ($extension === 'pdf') {
                                        $icon_class = 'fa-file-pdf';
                                        $icon_color = 'text-danger';
                                    } elseif (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                                        $icon_class = 'fa-file-image';
                                        $icon_color = 'text-success';
                                    }
                                    ?>
                                    <i class="far <?php echo $icon_class; ?> me-2 <?php echo $icon_color; ?>"></i>
                                    <a href="<?php echo $file_path; ?>" target="_blank"
                                        class="text-truncate" style="max-width: 200px;">
                                        <?php echo $file['file_name']; ?>
                                    </a>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-danger delete-file"
                                        data-file-id="<?php echo $file['id']; ?>"
                                        data-file-name="<?php echo htmlspecialchars($file['file_name']); ?>">
                                        <i class="fas fa-trash"></i> <?php echo lang("Delete"); ?>
                                    </button>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <!-- Preview Container -->
        <div id="filePreview" class="mt-2" style="display: none;">
            <div class="preview-content d-flex flex-wrap gap-3"></div>
            <small class="text-muted d-block mt-1">
                <?php echo lang("forms_Click_preview_to_enlarge"); ?>
            </small>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <label class="form-label"><?php echo lang("form_date"); ?><span
                class="text-danger">*</span></label>
        <div class="d-flex align-items-center">
            <span class="me-2"><?php echo date('d/m/Y'); ?></span>
            <input type="hidden" name="signature_date" value="<?php echo date('Y-m-d'); ?>">
        </div>
    </div>
</div>

                    <!-- Recaptch Button -->
                    <div class="row mb-4">

                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary prev-step"
                            style="background-color: white; color: #fe5500;"><?php echo lang("form_previous"); ?></button>
                        <button type="button"
                            class="btn btn-primary ms-auto next-step"><?php echo lang("form_submit"); ?></button>
                    </div>

            </form>
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

    /* Add red asterisk (*) next to required labels */
    input[required]+.form-label::before {
        content: '*';
        /* Insert the asterisk */
        color: #FF5500;
        /* Red color for the asterisk */
        margin-left: 5px;
        /* Add space between label text and asterisk */
    }

    /* Styling for the required asterisk  */
    .required-asterisk {
        color: #FF5500;
        font-weight: bold;
    }

    input:required {
        /* Your default border styles */
        border: 1px solid #ccc;
    }

    /* Style for invalid required inputs (initially, they might have this) */
    /* Keep a subtle visual difference if you want */
    /* border-color: #eee; */


    /* Target invalid required inputs when the form has been interacted with
   (using focus as a trigger - this has limitations) */
    form:focus-within input:required:invalid {
        border-color: #FF5500;
    }

    /* Optional: Style the focus state of invalid fields */
    input:required:invalid:focus {
        outline-color: #FF5500;
    }



    /* Optional: Error message style */
    .invalid-feedback {
        color: #FF5500;
        font-size: 0.9rem;
    }
</style>

<!-- Signature  -->
<script>
    $(document).ready(function () {
        $('#ssn').on('input', function () {
            // Remove any non-digit characters
            let value = $(this).val().replace(/\D/g, '');

            // Add dashes at the appropriate positions
            if (value.length > 3) {
                value = value.substring(0, 3) + '-' + value.substring(3);
            }
            if (value.length > 6) {
                value = value.substring(0, 6) + '-' + value.substring(6);
            }

            // Limit to 9 digits (plus 2 dashes)
            if (value.length > 11) {
                value = value.substring(0, 11);
            }

            $(this).val(value);
        });
    });
    document.addEventListener('DOMContentLoaded', function () {
        const formSteps = document.querySelectorAll('.form-step');
        const progressContainer = document.querySelector('.progress-steps');
        const stepSequence = JSON.parse(document.getElementById('formStepSequence').value);
        let currentVisibleStep = 0;

        function initializeSteps() {
            formSteps.forEach(step => {
                const stepNumber = parseInt(step.dataset.step);
                step.style.display = stepSequence.includes(stepNumber) ? 'none' : 'none';
            });
            showStep(currentVisibleStep);
            updateProgress();
        }
        setupProgressClickHandlers();

        function showStep(visibleIndex) {
            // Scroll to top
            $('html, body').animate({scrollTop: 0}, 100); // 100ms duration

            const targetStep = stepSequence[visibleIndex];
            formSteps.forEach(step => {
                const stepNumber = parseInt(step.dataset.step);
                if (stepNumber === targetStep) {
                    step.style.display = 'block';
                    step.classList.add('active');
                } else {
                    step.style.display = 'none';
                    step.classList.remove('active');
                }
            });
        }

        function setupProgressClickHandlers() {
            document.querySelectorAll('.progress-steps .step').forEach((stepElem, index) => {
                stepElem.style.cursor = 'pointer';
                stepElem.addEventListener('click', async () => {
                    if (!validateStep(currentVisibleStep)) return;
                    await submitStepData(stepSequence[currentVisibleStep]);
                    currentVisibleStep = index;
                    showStep(currentVisibleStep);
                    updateProgress();
                });
            });
        }

        function updateProgress() {
            const progressSteps = document.querySelectorAll('.progress-steps .step');
            progressSteps.forEach((stepElem, index) => {
                const isCompleted = index < currentVisibleStep;
                const isActive = index === currentVisibleStep;

                stepElem.querySelector('.progress-segment').style.backgroundColor =
                    isCompleted ? '#FF5500' : '#f1f2f6';

                const icon = stepElem.querySelector('.step-icon');
                icon.style.backgroundColor = (isCompleted || isActive) ? '#FF5500' : '#f1f2f6';
                icon.style.cursor = 'pointer';

                stepElem.classList.toggle('clickable', true);
            });
        }

        function setupProgressClickHandlers() {
            document.querySelectorAll('.progress-steps .step').forEach((stepElem, index) => {
                stepElem.style.cursor = 'pointer';
                stepElem.addEventListener('click', async () => {
                    if (index === currentVisibleStep) return;
                    if (currentVisibleStep === stepSequence.length - 1 && index < currentVisibleStep) {
                        currentVisibleStep = index;
                        showStep(currentVisibleStep);
                        updateProgress();
                        return;
                    }
                    if (!validateStep(currentVisibleStep)) return;
                    await submitStepData(stepSequence[currentVisibleStep]);
                    currentVisibleStep = index;
                    showStep(currentVisibleStep);
                    updateProgress();
                });
            });
        }

       document.querySelectorAll('.next-step').forEach(button => {
    button.addEventListener('click', async function (e) {
        e.preventDefault();
        
        
        const originalText = this.innerHTML;
        this.innerHTML = `<span class="btn-loader"></span> ${originalText}`;
        this.disabled = true;
        
        try {
            if (!validateStep(currentVisibleStep)) {
                this.innerHTML = originalText;
                this.disabled = false;
                return;
            }
            
            const result = await submitStepData(stepSequence[currentVisibleStep]);
            
            if (currentVisibleStep === stepSequence.length - 1) {
                
                window.location.href = 'index.php?route=modules/craftman/thanks2';
            } else {
                currentVisibleStep++;
                showStep(currentVisibleStep);
                updateProgress();
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: error.message,
                footer: 'Please check your entries and try again'
            });
        } finally {
           
            this.innerHTML = originalText;
            this.disabled = false;
        }
    });
});
        document.querySelectorAll('.prev-step').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                if (currentVisibleStep > 0) {
                    currentVisibleStep--;
                    showStep(currentVisibleStep);
                    updateProgress();
                }
            });
        });

        function validateStep(visibleIndex) {
            const targetStep = stepSequence[visibleIndex];
            const stepForm = document.querySelector(`.form-step[data-step="${targetStep}"]`);
            let isValid = true;
            stepForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            stepForm.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            const translatorSelected = document.querySelector('input[name="translator_certificate"][value="2"]')?.checked;
            const signatureField = document.getElementById('signature_of_translator');

            if (translatorSelected && (!signatureField || !signatureField.value.trim())) {
                isValid = false;
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Signature is required when using a translator';

                // Add feedback after the label
                const label = document.querySelector('label[for="signature_of_translator"]');
                if (label) {
                    label.insertAdjacentElement('afterend', feedback);
                } else if (signatureField) {
                    signatureField.insertAdjacentElement('beforebegin', feedback);
                }

                // Scroll to signature field
                setTimeout(() => {
                    if (signatureField) {
                        signatureField.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        signatureField.focus();
                    }
                }, 50);
            }

            // Original validation for required fields (SSN, etc.)
            stepForm.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'This field is required';
                    field.after(feedback);
                    if (field.id === 'ssn') {
                        feedback.textContent = 'Please enter Social Security Number';
                        field.focus();
                    }
                }
                if (field.id === 'ssn' && field.value.trim()) {
                    const ssnPattern = /^\d{3}-\d{2}-\d{4}$/;
                    if (!ssnPattern.test(field.value.trim())) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'Please enter Social Security Number';
                        field.after(feedback);
                        field.focus();
                    }
                }
            });

            if (!isValid) {
                Swal.fire('Validation Error', 'Please fill all required fields', 'error');
            }
            return isValid;
        }
        async function submitStepData(targetStep) {
            const formData = new FormData();
            const stepForm = document.querySelector(`.form-step[data-step="${targetStep}"]`);
            formData.append('applicant_ID', document.querySelector('[name="applicant_ID"]').value);
            formData.append('save_step', targetStep);
            stepForm.querySelectorAll('input, select, textarea').forEach(field => {
                if (field.name && !field.disabled) {
                    if (field.type === 'checkbox') {
                        formData.append(field.name, field.checked ? '1' : '0');
                    } else if (field.type === 'radio') {
                        if (field.checked) {
                            formData.append(field.name, field.value);
                        }
                    } else if (field.type === 'file' && field.files[0]) {
                        formData.append(field.name, field.files[0]);
                    } else {
                        formData.append(field.name, field.value);
                    }
                }
            });
            try {
                const response = await fetch('handle_edit_admin.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (!response.ok || !result.success) {
                    throw new Error(result.message || `Step ${targetStep} save failed`);
                }
                if (targetStep === 8) {
                    window.location.href = 'index.php?route=modules/craftman/edit_my_packet';
                }
                return result;
            } catch (error) {
                console.error('Submission error:', error);
                throw new Error('Network error - please try again');
            }
        }
        showStep(currentVisibleStep);
        updateProgress();
    });
</script>

<script>
  let currentCameraSide = 'front';
let mediaStream = null;

function previewLicense(event, previewId) {
    const preview = document.getElementById(previewId);
    const file = event.target.files[0];
    
    // Show loader while processing
    preview.innerHTML = `
        <div class="preview-loader">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    setTimeout(() => { // Simulate processing delay (can be removed)
        preview.innerHTML = '';
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.onload = () => {
                preview.innerHTML = '';
                preview.appendChild(img);
            };
        } else if (file.type === 'application/pdf') {
            preview.innerHTML = `
                <div class="pdf-preview">
                    <i class="fas fa-file-pdf fa-4x text-danger"></i>
                    <div>${file.name}</div>
                </div>
            `;
        }
    }, 500); // Remove this timeout in production
}

let loaderTimeout = setTimeout(() => {
    this.innerHTML = `<span class="btn-loader"></span> ${originalText}`;
}, 300);


clearTimeout(loaderTimeout);
function capturePhoto(side) {
    currentCameraSide = side;
    const modal = document.createElement('div');
    modal.className = 'camera-modal';
    modal.innerHTML = `
        <div class="camera-preview">
            <div class="camera-loader">
                <div class="spinner-border text-white" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Initializing camera...</p>
            </div>
            <video autoplay playsinline style="display:none;"></video>
            <div class="capture-controls" style="display:none;">
                <button class="capture-btn" onclick="takeSnapshot()"></button>
                <button class="btn btn-danger mt-3" onclick="closeCamera()">
                    <?php echo lang("forms_Close"); ?>
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    modal.style.display = 'block';
    startCamera();
}

function startCamera() {
    const loader = document.querySelector('.camera-loader');
    const video = document.querySelector('.camera-modal video');
    const controls = document.querySelector('.capture-controls');
    
    navigator.mediaDevices.getUserMedia({
        video: {
            facingMode: 'environment'
        }
    })
    .then(stream => {
        mediaStream = stream;
        video.srcObject = stream;
        
        video.onloadedmetadata = () => {
            video.style.display = 'block';
            controls.style.display = 'flex';
            loader.style.display = 'none';
        };
    })
    .catch(error => {
        console.error('Error accessing camera:', error);
        loader.innerHTML = `
            <div class="text-danger">
                <i class="fas fa-camera-slash fa-3x"></i>
                <p><?php echo lang("forms_Camera_access_denied"); ?></p>
                <button class="btn btn-secondary" onclick="closeCamera()">
                    <?php echo lang("forms_Close"); ?>
                </button>
            </div>
        `;
    });
}

function takeSnapshot() {
    const video = document.querySelector('.camera-modal video');
    const preview = document.getElementById(`${currentCameraSide}Preview`);
    const captureBtn = document.querySelector('.capture-btn');
    
    // Show capturing indicator
    captureBtn.innerHTML = '<div class="capture-spinner"></div>';
    captureBtn.disabled = true;
    
    setTimeout(() => { // Let the animation show briefly
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        
        canvas.toBlob(blob => {
            const file = new File([blob], `license_${currentCameraSide}.png`, {
                type: 'image/png'
            });
            const inputId = `license${currentCameraSide.charAt(0).toUpperCase() + currentCameraSide.slice(1)}`;
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById(inputId).files = dataTransfer.files;
            
            // Show processing loader in preview
            preview.innerHTML = `
                <div class="preview-loader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            // Simulate processing delay (remove in production)
            setTimeout(() => {
                previewLicense({
                    target: document.getElementById(inputId)
                }, `${currentCameraSide}Preview`);
                closeCamera();
            }, 800);
            
        }, 'image/png');
    }, 200);
}

function closeCamera() {
    const modal = document.querySelector('.camera-modal');
    if (modal) modal.remove();
    if (mediaStream) {
        mediaStream.getTracks().forEach(track => track.stop());
        mediaStream = null;
    }
}
</script>









<script>
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
    $("#multi-language1").on('change', function () {
        var selectedLang = $(this).val(); // Get selected language
        var currentUrl = window.location.href; // Get current URL

        // Remove existing lang parameter if exists
        var newUrl = new URL(currentUrl);
        newUrl.searchParams.set('lang', selectedLang); // Set new lang parameter

        // Redirect to new URL with lang parameter
        window.location.href = newUrl.toString();
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const radioButtons = document.querySelectorAll("input[name='citizenship_status']");
        const dependentFields = [
            "registration_number",
            "allen_registration_number",
            "passport_number",
            "expiration_datee",
            "country_of_issuance"
        ];

        const expirationDateField = document.getElementById("expiration_datee");

        function updateFields() {
            const selected = document.querySelector("input[name='citizenship_status']:checked");
            const selectedValue = selected ? selected.value : null;

            if (selectedValue === "U.S Citizen") {
                // Disable all and clear
                dependentFields.forEach(id => {
                    const field = document.getElementById(id);
                    if (field) {
                        field.disabled = true;
                        field.required = false;
                        field.value = "";
                    }
                });
            } else if (selectedValue) {
                // Enable all
                dependentFields.forEach(id => {
                    const field = document.getElementById(id);
                    if (field) {
                        field.disabled = false;
                        field.required = false; // Default not required
                    }
                });

                // Make expiration date required only if last option is selected
                if (selectedValue === "Non-Citizen National") {
                    if (expirationDateField) expirationDateField.required = true;
                }
            } else {
                // No selection
                dependentFields.forEach(id => {
                    const field = document.getElementById(id);
                    if (field) {
                        field.disabled = false;
                        field.required = false;
                    }
                });
            }
        }

        radioButtons.forEach(rb => rb.addEventListener("change", updateFields));
        updateFields(); // Run once on load
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const bankNameInput = document.getElementById("bank_name");

        const relatedFields = [
            "percentage",
            "account_1",
            "aba_number",
            "account_number"
        ];

        const radioButtons = document.querySelectorAll("input[name='accountType']");

        function toggleRequiredFields() {
            const isBankNameFilled = bankNameInput.value.trim() !== "";

            // Toggle 'required' on text/number inputs
            relatedFields.forEach(id => {
                const field = document.getElementById(id);
                if (field) {
                    field.required = isBankNameFilled;
                }
            });

            // Toggle 'required' for at least one radio button
            if (isBankNameFilled) {
                radioButtons.forEach(rb => rb.required = true);
            } else {
                radioButtons.forEach(rb => rb.required = false);
            }
        }

        bankNameInput.addEventListener("input", toggleRequiredFields);
    });
</script>

<script src="https://unpkg.com/browser-image-compression/dist/browser-image-compression.js"></script>

<script>
  async function handleImageUpload(inputId, fieldName) {
    const fileInput = document.getElementById(inputId);
    const file = fileInput.files[0];
    if (!file) return;

    const options = {
      maxSizeMB: 2,
      maxWidthOrHeight: 1920,
      useWebWorker: true
    };

    try {
      const compressedFile = await imageCompression(file, options);

      // Show SweetAlert if compression occurred
      if (compressedFile.size < file.size) {
        const originalSizeMB = (file.size / 1024 / 1024).toFixed(2);
        const newSizeMB = (compressedFile.size / 1024 / 1024).toFixed(2);

        Swal.fire({
          icon: 'info',
          title: 'Image Compressed',
          html: `
            <p><strong>${fieldName}</strong> was compressed:</p>
            <p>Original: ${originalSizeMB} MB</p>
            <p>Compressed: ${newSizeMB} MB</p>
          `,
          showConfirmButton: false, // Remove OK button
          timer: 2000, // Close the alert after 2 seconds (2000 ms)
          timerProgressBar: true, // Show the progress bar while the timer runs
        });
      }

      // Proceed with upload
      const formData = new FormData();
      formData.append('image', compressedFile);
      formData.append('field', fieldName); // Optional: Send which field it is

      const response = await fetch('/upload-endpoint.php', {
        method: 'POST',
        body: formData,
      });

      const result = await response.json();
      console.log(`${fieldName} Upload success:`, result);

    } catch (error) {
      console.error(`${fieldName} Compression or upload failed:`, error);
    }
  }

  document.getElementById('licenseFront').addEventListener('change', function () {
    handleImageUpload('licenseFront', 'License Front');
  });

  document.getElementById('licenseBack').addEventListener('change', function () {
    handleImageUpload('licenseBack', 'License Back');
  });
</script>













<script>
    $(document).ready(function () {

        // Phone number formatting and validation
        document.getElementById('contactPhone1').addEventListener('input', function (e) {
            const input = e.target;
            const numbers = input.value.replace(/\D/g, ''); // Remove all non-digits
            const char = {
                3: ' ',
                6: '-'
            };
            let formatted = '';

            // Format as (XXX) XXX-XXXX
            for (let i = 0; i < numbers.length && i < 10; i++) {
                if (i === 0) formatted += '(';
                if (i === 3) formatted += ') ';
                if (i === 6) formatted += '-';
                formatted += numbers[i];
            }

            input.value = formatted;
            validatePhone();
        });

        function validatePhone() {
            const phoneInput = document.getElementById('contactPhone1');
            const errorDiv = document.getElementById('phoneError');
            const phoneNumber = phoneInput.value.replace(/\D/g, ''); // Remove formatting

            if (phoneNumber.length !== 10) {
                errorDiv.textContent = 'Please enter a valid 10-digit US phone number';
                errorDiv.style.display = 'block';
                return false;
            }

            errorDiv.style.display = 'none';
            return true;
        }
    });
    $(document).ready(function () {

        // Phone number formatting and validation
        document.getElementById('contactPhone2').addEventListener('input', function (e) {
            const input = e.target;
            const numbers = input.value.replace(/\D/g, ''); // Remove all non-digits
            const char = {
                3: ' ',
                6: '-'
            };
            let formatted = '';

            // Format as (XXX) XXX-XXXX
            for (let i = 0; i < numbers.length && i < 10; i++) {
                if (i === 0) formatted += '(';
                if (i === 3) formatted += ') ';
                if (i === 6) formatted += '-';
                formatted += numbers[i];
            }

            input.value = formatted;
            validatePhone();
        });

        function validatePhone() {
            const phoneInput = document.getElementById('contactPhone2');
            const errorDiv = document.getElementById('phoneError');
            const phoneNumber = phoneInput.value.replace(/\D/g, ''); // Remove formatting

            if (phoneNumber.length !== 10) {
                errorDiv.textContent = 'Please enter a valid 10-digit US phone number';
                errorDiv.style.display = 'block';
                return false;
            }

            errorDiv.style.display = 'none';
            return true;
        }
    });
    $(document).ready(function () {

        // Phone number formatting and validation
        document.getElementById('phone_number').addEventListener('input', function (e) {
            const input = e.target;
            const numbers = input.value.replace(/\D/g, ''); // Remove all non-digits
            const char = {
                3: ' ',
                6: '-'
            };
            let formatted = '';

            // Format as (XXX) XXX-XXXX
            for (let i = 0; i < numbers.length && i < 10; i++) {
                if (i === 0) formatted += '(';
                if (i === 3) formatted += ') ';
                if (i === 6) formatted += '-';
                formatted += numbers[i];
            }

            input.value = formatted;
            validatePhone();
        });

        function validatePhone() {
            const phoneInput = document.getElementById('contactPhone2');
            const errorDiv = document.getElementById('phoneError');
            const phoneNumber = phoneInput.value.replace(/\D/g, ''); // Remove formatting

            if (phoneNumber.length !== 10) {
                errorDiv.textContent = 'Please enter a valid 10-digit US phone number';
                errorDiv.style.display = 'block';
                return false;
            }

            errorDiv.style.display = 'none';
            return true;
        }
    });
</script>

<script>
    // JavaScript Changes
    let uploadedFiles = [];

    function previewFile() {
        const fileInput = document.getElementById('license_file');
        const files = fileInput.files;
        const filePreview = document.getElementById('filePreview');
        const previewContainer = document.querySelector('.preview-content');

        Array.from(files).forEach(file => {
            // Validate file size
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: `The file "${file.name}" exceeds 5MB limit`,
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            if (!validTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: `"${file.name}" is not a valid file type`,
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Add to uploaded files array
            if (!uploadedFiles.some(existingFile => existingFile.name === file.name)) {
                uploadedFiles.push(file);
                createPreview(file);
            }
        });

        filePreview.style.display = uploadedFiles.length > 0 ? 'block' : 'none';
    }

    function createPreview(file) {
        const reader = new FileReader();
        const previewContainer = document.querySelector('.preview-content');

        reader.onload = (e) => {
            const previewItem = document.createElement('div');
            previewItem.className = 'position-relative';
            previewItem.style.maxWidth = '200px';

            previewItem.innerHTML = `
            <div class="card">
                <div class="card-body p-2">
                    ${file.type.startsWith('image/') ?
                    `<a href="${e.target.result}" target="_blank">
                            <img src="${e.target.result}" class="card-img-top" alt="Preview">
                        </a>` :
                    `<div class="d-flex align-items-center">
                            <i class="fas fa-file-pdf text-danger fa-3x me-3"></i>
                            <div>
                                <div class="text-truncate" style="max-width: 120px">${file.name}</div>
                                <small>${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                            </div>
                        </div>`
                }
                   
                </div>
            </div>
        `;

            previewContainer.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    }

    function removeFile(fileName) {
        // Remove from array
        uploadedFiles = uploadedFiles.filter(file => file.name !== fileName);

        // Remove from DOM
        const previewItems = document.querySelectorAll('.preview-content > div');
        previewItems.forEach(item => {
            if (item.querySelector('.text-truncate')?.textContent === fileName) {
                item.remove();
            }
        });

        if (uploadedFiles.length === 0) {
            document.getElementById('filePreview').style.display = 'none';
        }
    }

    // function clearFileInput() {
    //     uploadedFiles = [];
    //     document.querySelector('.preview-content').innerHTML = '';
    //     document.getElementById('filePreview').style.display = 'none';
    // }
    // $(document).ready(function () {
    //     $('#license_file').on('change', function () {
    //         let newValue = $(this).val();
    //         console.log("Value changed to:", newValue);
    //         // you can perform any other action here
    //     });
    // });

    $(document).ready(function () {
        $('#license_file').on('change', function (e) {
            e.preventDefault();

            const files = this.files;
            const maxSize = 5 * 1024 * 1024; // 5MB
            const validTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            const applicantID = '<?php echo $applicantId ?>';
            const formData = new FormData();

            let validFiles = [];
            let errors = [];

            // ✅ Real-time validation + filtering
            Array.from(files).forEach(file => {
                if (!validTypes.includes(file.type)) {
                    errors.push(`${file.name}: Invalid file type (PDF, JPG, PNG only)`);
                } else if (file.size > maxSize) {
                    errors.push(`${file.name}: File exceeds 5MB limit`);
                } else {
                    validFiles.push(file);
                }
            });

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Files',
                    html: errors.join('<br>'),
                    confirmButtonColor: '#FF5500'
                });
                this.value = ''; // ❌ Clear invalid files
                return;
            }

            // ✅ Append validated files
            validFiles.forEach(file => {
                formData.append('license_files[]', file);
            });

            // ✅ Append applicant ID
            formData.append('applicant_ID', applicantID);

            // 🔥 AJAX call
            $.ajax({
                url: 'ajax_helpers/ajax_get_certificates.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Certificates Saved!',
                            html: `Successfully uploaded ${response.files_uploaded} file(s)<br> <small>Total certificates: ${response.total_files}</small>`,
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Failed',
                            text: response.message || 'Unknown error occurred',
                            confirmButtonColor: '#FF5500'
                        });
                    }
                },
                error: function (xhr) {
                    const errorMessage = xhr.responseJSON?.message ||
                        xhr.statusText ||
                        'Connection error';

                    Swal.fire({
                        icon: 'error',
                        title: 'Request Failed',
                        text: errorMessage,
                        confirmButtonColor: '#FF5500'
                    });
                },
                complete: function () {
                    // Optional cleanup
                }
            });
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const radioButtons = document.querySelectorAll("input[name='translator_certificate']");

        // Fields
        const signatureField = document.getElementById("signature_of_translator");

        // All other translator fields
        const otherTranslatorFields = [
            "translator_first_name",
            "translator_last_name",
            "translator_address",
            "translator_city",
            "translator_state",
            "translator_zip"
        ].map(id => document.getElementById(id));

        function updateTranslatorFields() {
            const selected = document.querySelector("input[name='translator_certificate']:checked");
            const selectedValue = selected ? selected.value : null;

            if (selectedValue === "1") {
                // Disable all fields and make none required
                signatureField.disabled = true;
                signatureField.required = false;
                signatureField.value = "";

                otherTranslatorFields.forEach(field => {
                    if (field) {
                        field.disabled = true;
                        field.required = false;
                        field.value = "";
                    }
                });

            } else if (selectedValue === "2") {
                // Enable all fields
                signatureField.disabled = false;
                signatureField.required = true;

                otherTranslatorFields.forEach(field => {
                    if (field) {
                        field.disabled = false;
                        field.required = false;
                    }
                });

            } else {
                // No selection yet: enable but not required
                signatureField.disabled = false;
                signatureField.required = false;

                otherTranslatorFields.forEach(field => {
                    if (field) {
                        field.disabled = false;
                        field.required = false;
                    }
                });
            }
        }

        radioButtons.forEach(rb => rb.addEventListener("change", updateTranslatorFields));
        updateTranslatorFields(); // Initialize on load
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const radioButtons = document.querySelectorAll("input[name='citizenship_status']");
        const fields = {
            registration_number: document.getElementById("registration_number"),
            allen_registration_number: document.getElementById("allen_registration_number"),
            passport_number: document.getElementById("passport_number"),
            country_of_issuance: document.getElementById("country_of_issuance")
        };

        function updateFields() {
            const selected = document.querySelector("input[name='citizenship_status']:checked");
            const selectedValue = selected ? selected.value : null;

            // Reset all fields
            for (let key in fields) {
                fields[key].disabled = false;
                fields[key].required = false;
            }

            if (!selectedValue) return;

            switch (selectedValue) {
                case "U.S Citizen":
                    for (let key in fields) {
                        fields[key].disabled = true;
                        fields[key].value = ""; // optional: clear
                        fields[key].required = false;
                    }
                    break;

                case "Non U.S Citizen":
                    // Keep all enabled and unrequired
                    break;

                case "Lawful Permanent Resident":
                    fields["allen_registration_number"].required = true;
                    break;

                case "Allen Authorized Worker":
                    for (let key in fields) {
                        fields[key].required = true;
                    }
                    break;
            }
        }

        radioButtons.forEach(rb => rb.addEventListener("change", updateFields));
        updateFields(); // Run once on load
    });
</script>


<!-- Include Signature Pad library -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('signaturePad');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: '#FF5500'
        });
        const signatureInput = document.getElementById('signatureInput');

        // Prevent form submission if signature is empty
        document.querySelector('form').addEventListener('submit', function (e) {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                // alert('<?php echo lang("form_provide_signature"); ?>');
                return false;
            }

            // Ensure signature is saved before submission
            if (!signatureInput.value) {
                const signatureData = signaturePad.toDataURL('image/png');
                signatureInput.value = signatureData;
            }
            return true;
        });
        // Auto-save signature when drawing stops
        canvas.addEventListener('mouseup', function () {
            if (!signaturePad.isEmpty()) {
                const signatureData = signaturePad.toDataURL('image/png');
                signatureInput.value = signatureData;
            }
        });

        // Clear signature button
        document.getElementById('clearSignature').addEventListener('click', function () {
            signaturePad.clear();
            signatureInput.value = '';
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Use event delegation for dynamically loaded elements
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-file')) {
            const button = e.target.closest('.delete-file');
            const fileId = button.getAttribute('data-file-id');
            const fileName = button.getAttribute('data-file-name');
            const listItem = button.closest('li');
            
            Swal.fire({
                title: '<?php echo lang("Are you sure?"); ?>',
                text: '<?php echo lang("You won\'t be able to revert this!"); ?>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<?php echo lang("Yes, delete it!"); ?>',
                cancelButtonText: '<?php echo lang("Cancel"); ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading indicator
                    Swal.showLoading();
                    
                    // AJAX request to delete the file
                    fetch('ajax_helpers/delete_certification.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'file_id=' + fileId + '&file_name=' + encodeURIComponent(fileName)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Remove the file item from the list
                            listItem.remove();
                            
                            // Show success message
                            Swal.fire(
                                '<?php echo lang("Deleted!"); ?>',
                                '<?php echo lang("Your file has been deleted."); ?>',
                                'success'
                            );
                            
                            // If no files left, hide the entire uploaded files section
                            const filesList = document.querySelector('.existing-files .list-group');
                            if (filesList && filesList.children.length === 0) {
                                document.querySelector('.existing-files').remove();
                            }
                        } else {
                            throw new Error(data.message || 'Unknown error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire(
                            '<?php echo lang("Error!"); ?>',
                            error.message || '<?php echo lang("An error occurred while deleting the file"); ?>',
                            'error'
                        );
                    });
                }
            });
        }
    });
});
</script>

</body>

</html>