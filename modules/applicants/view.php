<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employment Application Form</title>
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
    document.querySelector(".menu-toggle").addEventListener("click", function() {
        document.querySelector(".menu").classList.toggle("active");
    });
    </script>


    <div class="container my-4">
        <div class="col-lg-10 mx-auto border p-3 bg-white">
            <h1 class="mb-5 text-center"><?php echo lang("form_title"); ?></h1>
            <form id="employmentApplication" action="submit_application.php" method="post">

                <!-- Personal Information -->
                <h3><?php echo lang("form_personal_info"); ?></h3>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="lastName" class="form-label"><?php echo lang("form_last_name"); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="lastName" name="last_name" required>
                    </div>
                    <div class="col-md-4">
                        <label for="firstName" class="form-label"><?php echo lang("form_first_name"); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="firstName" name="first_name" required>
                    </div>
                    <div class="col-md-2">
                        <label for="middleInitial" class="form-label"><?php echo lang("form_middle_initial"); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="middleInitial" name="middle_initial" maxlength="5">
                    </div>
                    <div class="col-md-2">
                        <label for="ssn" class="form-label"><?php echo lang("form_social_security"); ?> <span class="text-danger">*</span></label>
                        <!-- <input type="text" class="form-control" id="ssn" name="ssn" placeholder="XXX-XX-XXXX" required> -->

                        <input type="text" class="form-control" id="ssn" name="ssn" placeholder="XXX-XX-XXXX" required
                            pattern="^\d{3}-\d{2}-\d{4}$" title="Format: XXX-XX-XXXX (only numbers and dashes allowed)">
                    </div>
                </div>

                <!-- Address Information -->
                <h3><?php echo lang("form_address_info"); ?></h3>
                <div class="mb-3">
                    <label for="streetAddress" class="form-label"><?php echo lang("form_street_address"); ?><span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="streetAddress" name="street_address" required>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="city" class="form-label"><?php echo lang("form_city_name"); ?><span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="city" name="city" required>
                    </div>
                    <div class="col-md-3">
                        <label for="state" class="form-label"><?php echo lang("form_state"); ?><span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="state" name="state" required>
                    </div>
                    <div class="col-md-3">
                        <label for="zipCode" class="form-label"><?php echo lang("form_zip"); ?><span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="zipCode" name="zip_code" required>
                    </div>
                </div>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="phoneNumber" class="form-label"><?php echo lang("form_phone_number"); ?><span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" id="phoneNumber" name="phone_number" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label"><?php echo lang("form_email"); ?><span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="phoneNumber" name="email" required>
                </div>
                </div>
                <!-- Passport/Id Verfification -->
                <h3><?php echo lang("form_ps_id"); ?></h3>
                <div class="mb-3">
                    <label class="form-label"><?php echo lang("form_ps_id_label"); ?><span class="text-danger">*</span></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="ps_eligibility" id="psidYes"
                                value="Yes" required>
                            <label class="form-check-label" for="psYes"><?php echo lang("form_yes"); ?></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="ps_eligibility" id="psNo"
                                value="No">
                            <label class="form-check-label" for="psNo"><?php echo lang("form_no"); ?></label>
                        </div>
                    </div>
                </div>
                <!-- Work Eligibility -->
                <h3><?php echo lang("form_work_eligibility"); ?></h3>
                <div class="mb-3">
                    <label class="form-label"><?php echo lang("form_ask_eligible"); ?><span class="text-danger">*</span></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="work_eligibility" id="eligibilityYes"
                                value="Yes" required>
                            <label class="form-check-label" for="eligibilityYes"><?php echo lang("form_yes"); ?></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="work_eligibility" id="eligibilityNo"
                                value="No">
                            <label class="form-check-label" for="eligibilityNo"><?php echo lang("form_no"); ?></label>
                        </div>
                    </div>
                </div>

                <!-- Position & Compensation -->
                <h3><?php echo lang("form_Position"); ?></h3>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="positionDesired" class="form-label"><?php echo lang("form_position_desired"); ?><span
                                class="text-danger">*</span></label>
                        <!-- Dropdown dynamically populated using MeekroDB -->
                        <select class="form-select" id="positionDesired" name="position_desired" required>
                            <option value=""><?php echo lang("form_select_position"); ?></option>
                            <!-- <option value="test_position">test position</option> -->
                            <?php foreach($positions as $position): ?>
                            <option value="<?php echo $position['id']; ?>">
                                <?php echo htmlspecialchars($position['position_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="wageSalary" class="form-label"><?php echo lang("form_wage_salary"); ?><span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="wageSalary" name="wage_salary" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo lang("form_employment_type"); ?></label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="fullTime" name="employment_type[]"
                            value="Full Time">
                        <label class="form-check-label" for="fullTime"><?php echo lang("form_full_time"); ?></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="partTime" name="employment_type[]"
                            value="Part Time">
                        <label class="form-check-label" for="partTime"><?php echo lang("form_part_time"); ?></label>
                    </div>
                </div>

                <!-- Criminal History -->
                <h3><?php echo lang("form_criminal_history"); ?></h3>
                <div class="mb-3">
                    <label class="form-label"><?php echo lang("form_criminal_ask"); ?><span
                            class="text-danger">*</span></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="criminal_history" id="criminalYes"
                                value="Yes" required>
                            <label class="form-check-label" for="criminalYes"><?php echo lang("form_yes"); ?></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="criminal_history" id="criminalNo"
                                value="No">
                            <label class="form-check-label" for="criminalNo"><?php echo lang("form_no"); ?></label>
                        </div>
                    </div>
                </div>
                <div id="criminalDetails" style="display:none;">
                    <div class="mb-3">
                        <label for="criminalWhen" class="form-label"><?php echo lang("form_criminal_when"); ?></label>
                        <input type="text" class="form-control" id="criminalWhen" name="criminal_when">
                    </div>
                    <div class="mb-3">
                        <label for="criminalWhere" class="form-label"><?php echo lang("form_criminal_where"); ?></label>
                        <input type="text" class="form-control" id="criminalWhere" name="criminal_where">
                    </div>
                </div>

                <!-- Work Start Date & Age -->
                <h3><?php echo lang("form_work_start"); ?></h3>
                <div class="mb-3">
                    <label for="startDate" class="form-label"><?php echo lang("form_start_date"); ?><span
                            class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="startDate" name="start_date" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo lang("form_age"); ?><span
                            class="text-danger">*</span></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="age_confirm" id="ageYes" value="Yes"
                                required>
                            <label class="form-check-label" for="ageYes"><?php echo lang("form_yes"); ?></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="age_confirm" id="ageNo" value="No">
                            <label class="form-check-label" for="ageNo"><?php echo lang("form_no"); ?></label>
                        </div>
                    </div>
                    <div id="minorNotice" class="form-text" style="display:none;">
                    <?php echo lang("form_underage"); ?>
                    </div>
                </div>

                <!-- Education -->
                <h3><?php echo lang("form_education"); ?></h3>
                <div class="mb-3">
                    <label for="highSchool" class="form-label"><?php echo lang("form_high_school"); ?></label>
                    <input type="text" class="form-control" id="highSchool" name="high_school">
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="highSchoolCityState" class="form-label"><?php echo lang("form_city_state"); ?></label>
                        <input type="text" class="form-control" id="highSchoolCityState" name="high_school_city_state">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><?php echo lang("form_graduate"); ?></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="high_school_grad" id="hsGradYes"
                                    value="Yes">
                                <label class="form-check-label" for="hsGradYes"><?php echo lang("form_yes"); ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="high_school_grad" id="hsGradNo"
                                    value="No">
                                <label class="form-check-label" for="hsGradNo"><?php echo lang("form_no"); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><?php echo lang("form_GED"); ?></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="high_school_ged" id="hsGedYes"
                                    value="Yes">
                                <label class="form-check-label" for="hsGedYes"><?php echo lang("form_yes"); ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="high_school_ged" id="hsGedNo"
                                    value="No">
                                <label class="form-check-label" for="hsGedNo"><?php echo lang("form_no"); ?></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="college" class="form-label"><?php echo lang("form_college"); ?></label>
                    <input type="text" class="form-control" id="college" name="college">
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="collegeCityState" class="form-label"><?php echo lang("form_city_state"); ?></label>
                        <input type="text" class="form-control" id="collegeCityState" name="college_city_state">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><?php echo lang("form_graduate"); ?></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="college_grad" id="collegeGradYes"
                                    value="Yes">
                                <label class="form-check-label" for="collegeGradYes"><?php echo lang("form_yes"); ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="college_grad" id="collegeGradNo"
                                    value="No">
                                <label class="form-check-label" for="collegeGradNo"><?php echo lang("form_no"); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="degree" class="form-label"><?php echo lang("form_degree"); ?></label>
                        <input type="text" class="form-control" id="degree" name="degree">
                    </div>
                    <div class="col-md-2">
                        <label for="major" class="form-label"><?php echo lang("form_major"); ?></label>
                        <input type="text" class="form-control" id="major" name="major">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><?php echo lang("form_enrolled"); ?></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="currently_enrolled" id="enrolledYes"
                                value="Yes">
                            <label class="form-check-label" for="enrolledYes"><?php echo lang("form_yes"); ?></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="currently_enrolled" id="enrolledNo"
                                value="No">
                            <label class="form-check-label" for="enrolledNo"><?php echo lang("form_no"); ?></label>
                        </div>
                    </div>
                </div>
                <div id="enrollmentDetails" style="display:none;" class="mb-3">
                    <label for="schoolInfo" class="form-label"><?php echo lang("form_school_info"); ?></label>
                    <textarea class="form-control" id="schoolInfo" name="school_info" rows="2"></textarea>
                </div>

                <!-- Job-Related Skills/Accomplishments -->
                <h3><?php echo lang("form_skills_accomplishments"); ?></h3>
                <div class="mb-3">
                    <label for="skills" class="form-label"><?php echo lang("form_list_skills"); ?></label>
                    <textarea class="form-control" id="skills" name="skills" rows="3"></textarea>
                </div>

                <!-- Work Availability -->
                <h3><?php echo lang("form_work_availability"); ?></h3>
                <div class="mb-3">
                    <label class="form-label"><?php echo lang("form_available_time"); ?><span
                            class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col-5">
                            <select name="availability_day[]" id="" class="form-control" required>
                                <option value="Monday"><?php echo lang("form_monday"); ?></option>
                                <option value="Tuesday"><?php echo lang("form_tuesday"); ?></option>
                                <option value="Wednesday"><?php echo lang("form_wednesday"); ?></option>
                                <option value="Thursday"><?php echo lang("form_thursday"); ?></option>
                                <option value="Friday"><?php echo lang("form_friday"); ?></option>
                                <option value="Saturday"><?php echo lang("form_saturday"); ?></option>
                            </select>
                        </div>
                        <div class="col-3">
                            <input type="time" class="form-control" name="availability_from[]">
                        </div>
                        <div class="col-3">
                            <input type="time" class="form-control" name="availability_to[]">
                        </div>
                        <div class="col-1">
                            <button type="button" id="availability_more" class="btn btn-sm btn-success" style="background: #E64A00;border-color:#E64A00;"><?php echo lang("form_add"); ?></button>
                        </div>
                    </div>
                    <div id="availability_wrap"></div>
                    <!-- Repeat or duplicate for other days as needed -->
                </div>
                <div class="mb-3">
                    <label for="totalHours" class="form-label">Total hours per week you are available</label>
                    <input type="number" class="form-control" id="totalHours" name="availability_total_hours"  step="0.01" >
                </div>
                <div class="mb-3">
                    <label for="scheduleRequests" class="form-label"><?php echo lang("form_availability_schedule_requests"); ?></label>
                    <textarea class="form-control" id="scheduleRequests" name="availability_schedule_requests"
                        rows="2"></textarea>
                </div>

                <!-- References -->
                <h3><?php echo lang("form_references"); ?></h3>
                <div class="mb-3">
                    <label class="form-label"><?php echo lang("form_reference_1"); ?></label>
                    <input type="text" class="form-control mb-1" name="ref1_name" placeholder="<?php echo lang("form_name_occupation"); ?>">
                    <input type="text" class="form-control mb-1" name="ref1_relationship"
                        placeholder="<?php echo lang("form_reference_full"); ?>">
                    <input type="tel" class="form-control" name="ref1_phone" placeholder="<?php echo lang("form_phone_number"); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo lang("form_reference_2"); ?></label>
                    <input type="text" class="form-control mb-1" name="ref2_name" placeholder="<?php echo lang("form_name_occupation"); ?>">
                    <input type="text" class="form-control mb-1" name="ref2_relationship"
                        placeholder="<?php echo lang("form_reference_full"); ?>">
                    <input type="tel" class="form-control" name="ref2_phone" placeholder="<?php echo lang("form_phone_number"); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo lang("form_reference_3"); ?></label>
                    <input type="text" class="form-control mb-1" name="ref3_name" placeholder="<?php echo lang("form_name_occupation"); ?>">
                    <input type="text" class="form-control mb-1" name="ref3_relationship"
                        placeholder="<?php echo lang("form_reference_full"); ?>">
                    <input type="tel" class="form-control" name="ref3_phone" placeholder="<?php echo lang("form_phone_number"); ?>">
                </div>

                <!-- Employment History with dynamic repeating parts -->
                <h3><?php echo lang("form_employement_history"); ?></h3>
                <div id="employmentHistoryContainer">
                    <div class="employment-entry border p-3 mb-3">
                        <h5><?php echo lang("form_employer_info"); ?></h5>
                        <div class="mb-3">
                            <label for="employer_name[]" class="form-label"><?php echo lang("form_reference_name"); ?><span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="employer_name[]" required>
                        </div>
                        <div class="mb-3">
                            <label for="job_title[]" class="form-label"><?php echo lang("form_job_title"); ?><span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="job_title[]" required>
                        </div>
                        <div class="mb-3">
                            <label for="duties[]" class="form-label"><?php echo lang("form_duties"); ?></label>
                            <textarea class="form-control" name="duties[]" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="employer_address[]" class="form-label"><?php echo lang("form_employer_address"); ?><span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="employer_address[]" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="employer_from[]" class="form-label"><?php echo lang("form_employer_from"); ?><span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="employer_from[]" required>
                            </div>
                            <div class="col-md-6">
                                <label for="employer_to[]" class="form-label"><?php echo lang("form_employer_to"); ?><span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="employer_to[]" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="employer_location[]" class="form-label"><?php echo lang("form_employer_location"); ?><span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="employer_location[]" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="startPay[]" class="form-label"><?php echo lang("form_start_pay"); ?></label>
                                <input type="text" class="form-control" name="startPay[]">
                            </div>
                            <div class="col-md-4">
                                <label for="endPay[]" class="form-label"><?php echo lang("form_end_pay"); ?></label>
                                <input type="text" class="form-control" name="endPay[]">
                            </div>
                            <div class="col-md-4">
                                <label for="salary[]" class="form-label"><?php echo lang("form_salary"); ?></label>
                                <input type="text" class="form-control" name="salary[]">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="supervisor[]" class="form-label"><?php echo lang("form_supervisor"); ?><span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="supervisor[]" required>
                        </div>
                        <div class="mb-3">
                            <label for="employer_phone[]" class="form-label"><?php echo lang("form_telephone"); ?><span
                                    class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="employer_phone[]" required>
                        </div>
                        <div class="mb-3">
                            <label for="reason_leaving[]" class="form-label"><?php echo lang("form_reason_living"); ?></label>
                            <textarea class="form-control" name="reason_leaving[]" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <button type="button" id="addEmployer" class="btn btn-secondary mb-3"><?php echo lang("form_add_employer"); ?></button>

                <!-- Certification -->
                <h3><?php echo lang("form_certification"); ?></h3>
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
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="signature" class="form-label"><?php echo lang("form_signature"); ?><span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="signature" name="signature" required>
                    </div>
                    <div class="col-md-6">
                        <label for="signatureDate" class="form-label"><?php echo lang("form_date"); ?><span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="signatureDate" name="signature_date" required>
                    </div>
                </div>