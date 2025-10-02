<?php
$applicant_id = 0; // default

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $row = DB::queryFirstRow("
        SELECT a.id AS applicant_id
        FROM applicants a
        JOIN users u ON a.email = u.email
        WHERE u.user_id = %i
    ", $user_id);

    if ($row) {
        $applicant_id = $row['applicant_id'];
    }
} // fetch forms id like 3,6,9 etc....
if ($applicant_id > 0) {
    // Fetch all data based on craft_contracting_id
    $craft_data = DB::queryFirstRow("
    SELECT craft_contracting.*, p.position_name 
    FROM craft_contracting
    LEFT JOIN positions p ON craft_contracting.position = p.id
    WHERE craft_contracting.id = %i", $applicant_id);

    $verification = DB::queryFirstRow("SELECT * FROM employment_eligibility_verification WHERE id = %i", $applicant_id);
    $verification_1 = DB::queryFirstRow("SELECT * FROM employment_eligibility_verification1 WHERE id = %i", $applicant_id);
    $mvr = DB::queryFirstRow("SELECT * FROM mvr_released WHERE id = %i", $applicant_id);
    $quick_book = DB::queryFirstRow("SELECT * FROM quick_book WHERE id = %i", $applicant_id);
    $w4 = DB::queryFirstRow("SELECT * FROM w4_form WHERE id = %i", $applicant_id);
    $data = DB::queryFirstRow("SELECT * FROM employment_data WHERE id = %i", $applicant_id);



    if ($craft_data) {
        // Output the data (example: converting to JSON)

    } else {
        // header("Location: " . SITE_ROOT . "index.php?route=modules/forms/list_data");
    }
} else {
    //  header("Location: " . SITE_ROOT . "index.php?route=modules/forms/list_data");
}
?>

<form id="employmentApplication" action="handle_forms.php" method="post" enctype="multipart/form-data">

<div class="container my-5" id="form-content">

    <form style="page-break-inside: avoid;" method="post" id="craft_form">
        <!-- Top Section with Logo and Address -->


        <h2 class="bg-dark text-white">Employee Information</h2>

        <!-- Personal Information Section -->
        <div class="row mb-3">
            <div class="col-md-6 form-label-inline">
                <p for="fullName" class="form-label">Full Name:</p>
                <input type="text" class="form-control" name="fullName" id="fullName" placeholder="" value="<?=
                                                                                                            (!empty($craft_data['first_name']) ? $craft_data['first_name'] : '') .
                                                                                                                (!empty($craft_data['last_name']) ? ' ' . $craft_data['last_name'] : '')
                                                                                                            ?>">
            </div>
            <div class="col-md-6 form-label-inline">
                <p for="date" class="form-label">Date:</p>
                <input 
        type="text" 
        class="form-control" 
        name="date" 
        id="date"
        placeholder="MM/DD/YYYY"
        value="<?= !empty($craft_data['created_at']) ? date('m/d/Y', strtotime($craft_data['created_at'])) : '' ?>"
    >
            </div>


            <div class="col-md-6 form-label-inline">
                <p for="address" class="form-label">Address:</p>
                <input type="text" class="form-control" name="address" id="address" placeholder=""
                    value="<?= !empty($craft_data['street_address']) ? $craft_data['street_address'] : '' ?>">
            </div>
        </div>


        <div class="row mb-3">
            <div class="col-md-4 form-label-inline">
                <p for="city" class="form-label">City:</p>
                <input type="text" class="form-control" name="city" id="city"
                    value="<?= !empty($craft_data['city']) ? $craft_data['city'] : '' ?>">
            </div>
            <div class="col-md-4 form-label-inline">
                <p for="state" class="form-label">State:</p>
                <input type="text" class="form-control" name="state" id="state"
                    value="<?= !empty($craft_data['state']) ? $craft_data['state'] : '' ?>">
            </div>
            <div class="col-md-4 form-label-inline">
                <p for="zip" class="form-label">Code:</p>
                <input type="number" class="form-control" name="zip" id="zip"
                    value="<?= !empty($craft_data['zip_code']) ? $craft_data['zip_code'] : '' ?>">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 form-label-inline">
                <p for="phone" class="form-label">Phone ( )</p>
                <input type="tel" class="form-control" name="phone" id="phone" placeholder=""
                    value="<?= !empty($craft_data['phone_number']) ? $craft_data['phone_number'] : '' ?>">
            </div>
            <div class="col-md-6 form-label-inline">
                <p for="email" class="form-label">Email Address:</p>
                <input type="email" class="form-control" name="email" id="email"
                    value="<?= !empty($craft_data['email']) ? $craft_data['email'] : '' ?>">
            </div>
        </div>

        <div class="row mb-3">
            <!-- <div class="col-md-6 form-label-inline">
                <p for="startDate" class="form-label">First Day of Work:</p>
                <input type="date" class="form-control" name="joinDate" id="joinDate"
                    value="<?= !empty($craft_data['joinDate']) ? $craft_data['joinDate'] : '' ?>">
            </div>
            -->
        </div>

       
        <!-- Emergency Contact Information -->
        <h2 class="bg-dark text-white">Emergency Contact Information</h2>
        <div class="row mb-3">
            <div class="col-md-6 form-label-inline mb-3">
                <p for="contactName1" class="form-label">Name:</p>
                <input type="text" class="form-control" name="contactName1" id="contactName1"
                    value="<?= !empty($craft_data['contact_name1']) ? $craft_data['contact_name1'] : '' ?>">
            </div>
            <div class="col-md-6 form-label-inline mb-3">
                <p for="contactAddress1" class="form-label">Address:</p>
                <input type="text" class="form-control" name="contactAddress1" id="contactAddress1"
                    value="<?= !empty($craft_data['contact_address1']) ? $craft_data['contact_address1'] : '' ?>">
            </div>
            <div class="col-md-6 form-label-inline mb-3">
                <p for="contactPhone1" class="form-label">Phone ( )</p>
                <input type="tel" class="form-control" name="contactPhone1" id="contactPhone1" placeholder=""
                    value="<?= !empty($craft_data['contact_phone1']) ? $craft_data['contact_phone1'] : '' ?>">
            </div>
            <div class="col-md-6 form-label-inline mb-3">
                <p for="relationship1" class="form-label">Relationship:</p>
                <input type="text" class="form-control" name="relationship1" id="relationship1"
                    value="<?= !empty($craft_data['relationship1']) ? $craft_data['relationship1'] : '' ?>">
            </div>
            <div class="col-md-6 form-label-inline mb-3">
                <p for="contactName1" class="form-label">Name:</p>
                <input type="text" class="form-control" name="contactName2" id="contactName2"
                    value="<?= !empty($craft_data['contact_name2']) ? $craft_data['contact_name2'] : '' ?>">
            </div>
            <div class="col-md-6 form-label-inline mb-3">
                <p for="contactAddress1" class="form-label">Address:</p>
                <input type="text" class="form-control" name="contactAddress2" id="contactAddress2"
                    value="<?= !empty($craft_data['contact_address2']) ? $craft_data['contact_address2'] : '' ?>">
            </div>
            <div class="col-md-6 form-label-inline mb-3">
                <p for="contactPhone1" class="form-label">Phone ( )</p>
                <input type="tel" class="form-control" name="contactPhone2" id="contactPhone2" placeholder=""
                    value="<?= !empty($craft_data['contact_phone2']) ? $craft_data['contact_phone2'] : '' ?>">
            </div>
            <div class="col-md-6 form-label-inline mb-3">
                <p for="relationship1" class="form-label">Relationship:</p>
                <input type="text" name="relationship2" class="form-control" id="relationship2"
                    value="<?= !empty($craft_data['relationship2']) ? $craft_data['relationship2'] : '' ?>">
            </div>
        </div>

        <!-- Emergency Contact Information -->
        <h2 class="bg-dark text-white">Craft Employee File Notes</h2>
        <div class="row mb-3">
            <div class="col-md-12 ">
                <input type="text" name="note1" class="form-control" id="note1"
                    value="<?= !empty($craft_data['notes']) ? $craft_data['notes'] : '' ?>">
            </div>
            <div class="col-md-12 ">
                <input type="text" name="note2" class="form-control" id="note2"
                    value="<?= !empty($craft_data['note2']) ? $craft_data['note2'] : '' ?>">
            </div>
            <div class="col-md-12 ">
                <input type="text" name="note3" class="form-control" id="note3"
                    value="<?= !empty($craft_data['note3']) ? $craft_data['note3'] : '' ?>">
            </div>
        </div>

    </form>
</div>


<?php if (!empty($verification_1)): ?>


    <!-- <style>
        .form-group-custom {
            margin-bottom: 15px;
        }

        .input-inline {
            display: inline-block;
            width: calc(100% - 10px);
            padding: 2px;
            border: none;
            border-bottom: 1px solid #333;
            background-color: transparent;
            margin-right: 5px;
            margin-bottom: 10px;
        }

        .input-inline:focus {
            outline: none;
            border-bottom: 2px solid #333;
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 10px solid black;
            padding-bottom: 10px;
            margin-bottom: 5px;
        }

        .form-header img {
            max-width: 75px;
            height: auto;
        }

        .form-header .header-title {
            text-align: center;
            flex-grow: 1;
        }

        .section-title {
            background-color: #c0c0c0;
            font-weight: bold;
            font-size: 1.3rem;
            border: 1px solid #000;
            margin: 0;
            padding: 0;
        }

        .form-table {
            width: 100%;
        }

        .form-table th,
        .form-table td {
            text-align: left;
        }

        .input-label {
            display: block;
            font-weight: normal;
            font-size: 0.85rem;
            margin-bottom: 4px;
            color: #000;
        }

        .form-input {
            width: 100%;
            padding: 0;
            margin: 0;
            border: none;
        }

        .form-control {
            border: none;
            border-bottom: 2px solid #333;
            border-radius: 0;
            box-shadow: none;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #333;
        }

        .form-input:focus {
            outline: none;
            border: none;
        }

        .border-header-line {
            width: 100%;
            height: 1px;
            background-color: black;
            margin-top: 5px;
            /* This will make the line appear directly below the form-header */
        }

        .instructions {
            margin-top: 20px;
        }

        .instruction-bold {
            font-weight: bold;
            font-size: 0.75rem;
        }

        .instruction-normal {
            font-weight: normal;
            font-size: 0.75rem;
            margin-top: 0px;
            color: #000;
        }

        /* Custom Border for Table */
        .table-bordered {
            border: 1px solid black;
            border-collapse: collapse;
            margin: 0;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid black;
            text-align: left;
            padding-left: 4px;
            padding-right: 0px;
            padding-top: 0px;
            padding-bottom: 0px;
        }

        /* Remove margin between tables */
        .custom-table {
            margin-bottom: -1px;
        }

        .box-header {
            border: 2px solid #000;
            padding-left: 5px;
            background-color: #c0c0c0;
            font-size: 1rem;
            font-weight: normal;
            line-height: 1;
        }

        .box-header .main-title {
            font-weight: bold;
            font-size: 1.2rem;
            display: inline;
        }

        .box-header .sub-text {
            font-size: 0.85rem;
            font-weight: normal;
            display: inline;
            margin-left: 5px;
        }

        .box-container2 {
            border: 1px solid black;

        }

        .box-header2 {
            border-bottom: 1px solid #000;
            padding: 0px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: .25rem !important;
            font-size: 1rem;
            font-weight: normal;
        }

        .box-header2 .main-title2 {
            font-weight: normal;
            font-size: 14px;
            display: inline;
        }

        .main-box {
            display: flex;
            justify-content: space-between;

        }

        .main-content {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 75%;
            border: 1px solid #000;
        }

        .main-title3 {
            font-weight: normal;
            font-size: 12px;
            margin: 8px 0px 0px 5px;
            display: inline-block;
            line-height: 1;

        }

        .description {
            font-size: 11px;
            color: #555;
            display: inline;

        }

        input.form-control {
            margin-right: 10px;
            width: 100%;
        }

        .passport-photo {
            width: 20%;
            display: flex;
            justify-content: center;
            border: 1px solid #000;
        }

        .passport-photo .photo {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 50%;
        }

        .row-layout {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .Certificate {
            background-color: #c0c0c0;
        }

        .left-section,
        .right-section {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-text {
            font-size: 13px;
            color: #000;
        }

        .info-heading {
            font-size: 18px;
            color: #000;
            font-weight: bold;
        }

        .header-title {
            text-align: center;
            flex: 1;
            min-width: 200px;
        }

        .title-main,
        .title-sub {
            font-size: 15px;
            margin: 0;
            font-weight: bold;
        }

        .subtitle {
            font-weight: normal;
        }

        .form-info {
            text-align: center;
            min-width: 150px;
        }

        .info-main {
            font-size: 15px;
            margin: 0;
            font-weight: bold;
            color: black;
        }

        .info-small {
            font-size: 10px;
            margin: 0;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .logo {
                margin-bottom: 10px;
            }

            .header-title,
            .form-info {
                text-align: center;
                margin-bottom: 10px;
            }
        }
    </style> -->

    <div class="container my-5" id="form-content-2">
        <!-- <div class="form-header">
            <img src="../modules/forms/images/us_Department.svg" class="logo" alt="Logo">
            <div class="header-title">
                <h1 class="title-main">Employment Eligibility Verification</h1>
                <h1 class="title-sub">Department of Homeland Security<br><span class="subtitle">U.S. Citizenship and
                        Immigration Services</span></h1>
            </div>
            <div class="form-info">
                <h2 class="info-main">USCIS</h2>
                <h2 class="info-main">Form I-9</h2>
                <h2 class="info-small">OMB No. 1615-0047</h2>
                <h2 class="info-small">Expires 10/31/2022</h2>
            </div>
        </div>
        <div class="border-header-line"></div> -->

        <!-- Box container with header and content -->


        <div class="instructions m-0">
            <p class="instruction-bold mt-1" style="line-height: 1;"><strong>► START HERE: Read instructions carefully
                    before completing this form. The instructions must be available, either in paper or electronically,
                    during completion of this form. Employers are liable for errors in the completion of this
                    form.</strong></p>
            <p class="instruction-normal mb-1" style="line-height: 1.1;"><strong>ANTI-DISCRIMINATION NOTICE</strong>: It
                is illegal to discriminate against work-authorized individuals. Employers <strong>CANNOT</strong>
                specify which document(s) an employee may present to establish employment authorization and identity.
                The refusal to hire or continue to employ an individual because the documentation presented has a future
                expiration date may also constitute illegal discrimination.</p>
        </div>
        <div class="box-container">
            <div class="box-header">
                <span class="main-title mt-1" style="line-height: 1.1;">Section 1. Employee Information and
                    Attestation</span>
                <span class="sub-text">(Employees must complete and sign Section 1 of Form I-9 no later than the
                    <strong>first day of employment</strong>, but not before accepting a job offer.)</span>
            </div>
            <div class="box-content">
                <table class="table table-bordered custom-table">
                    <tr>
                        <th style="width: 30%;">
                            <label class="input-label mb-0" style="height:17px;">Last Name (Family Name)<br><br></label>
                            <input type="text" class="form-input"
                                value="<?= !empty($craft_data['last_name']) ? $craft_data['last_name'] : '' ?>">
                        </th>
                        <th style="width: 31%;">
                            <label class="input-label mb-0" style="height:17px;">First Name (Given Name)<br><br></label>
                            <input type="text" class="form-input"
                                value="<?= !empty($craft_data['first_name']) ? $craft_data['first_name'] : '' ?>">
                        </th>
                        <th style="width: 5%;">
                            <label class="input-label mb-0" style="height:17px;">Middle.Initial.<br><br></label>
                            <input type="text" class="form-input"
                                value="<?= !empty($craft_data['middle_name']) ? $craft_data['middle_name'] : '' ?>">
                        </th>
                        <th style="width: 30%;">
                            <label class="input-label mb-0" style="height:17px;">Other Last Name Used If Any</label>
                            <input type="text" class="form-input"
                                value="<?= !empty($verification['other_last_name']) ? $verification['other_last_name'] : '' ?>">
                        </th>
                    </tr>
                </table>
                <table class="table table-bordered custom-table">
                    <tr>
                        <th style="width: 30%;">
                            <label class="input-label mb-0" style="height:17px;">Address (Street Number And
                                Name)</label>
                            <input type="text" class="form-input"
                                value="<?= !empty($craft_data['street_address']) ? $craft_data['street_address'] : '' ?>">
                        </th>
                        <th style="width: 5%;">
                            <label class="input-label mb-0" style="height:17px;">Ap.Number</label>
                            <input type="text" class="form-input"
                                value="<?= !empty($verification_1['apt_number']) ? $verification_1['apt_number'] : '' ?>">
                        </th>
                        <th style="width: 30%;">
                            <label class="input-label mb-0" style="height:17px;">City Or Town</label>
                            <input type="text" class="form-input"
                                value="<?= !empty($craft_data['city']) ? $craft_data['city'] : '' ?>">
                        </th>
                        <th style="width: 10%;">
                            <label for="" class="input-label mb-0" style="height:17px;">State</label>
                            <input type="text" name="" class="form-input"
                                value="<?= !empty($craft_data['state']) ? $craft_data['state'] : '' ?>">
                        </th>
                        <th style="width: 15%;">
                            <label class="input-label mb-0" style="height:17px;">Zip</label>
                            <input type="text" class="form-input"
                                value="<?= !empty($craft_data['zip_code']) ? $craft_data['zip_code'] : '' ?>">
                        </th>
                    </tr>
                </table>
                <table class="table table-bordered custom-table">
                    <tr>
                        <th>
                            <label class="input-label">Date Of Birth(mm/dd/yyyy)</label>
                            <input type="date" class="form-input"
                                value="<?= !empty($craft_data['dob']) ? $craft_data['dob'] : '' ?>">
                        </th>
                        <th>
<label class="input-label">U.S. Social Security Number</label>
<input type="text" class="form-input" 
       maxlength="9" 
       pattern="\d{9}"
       title="Please enter 9-digit SSN without dashes"
       value="<?= !empty($verification['ssn']) ? htmlspecialchars($verification['ssn']) : '' ?>"
       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
</th>
<th>
<label class="input-label">Employee's Email Address</label>
<input type="email" class="form-input"
       value="<?= !empty($craft_data['email']) ? htmlspecialchars($craft_data['email']) : '' ?>">
</th>
                        <th>
                            <label for="" class="input-label">Employee's Telephone Number</label>
                            <input type="text" name="" class="form-input"
                                value="<?= !empty($craft_data['phone_number']) ? $craft_data['phone_number'] : '' ?>">
                        </th>
                    </tr>
                </table>
            </div>
        </div>

        <div class="instructions m-0" style="line-height:1">
            <p class="instruction-bold m-1"><strong>I am aware that federal law provides for imprisonment and/or fines
                    for false statements or use of false documents in connection with the completion of this
                    form.</strong></p>
            <p class="instruction-bold m-1"><strong>I attest, under I penalty of perjury, that I am (check one of the
                    following boxes):</strong></p>
        </div>
        <div class="box-container2">
<!-- Point 1 - U.S Citizen -->
<div class="box-header2">
    <input type="radio" id="point1" name="citizenship_status" value="U.S Citizen"
        <?= ($verification_1['citizenship_status'] ?? '') === 'U.S Citizen' ? 'checked' : '' ?>>
    <label for="point1" class="main-title2">1. A Citizen of the United States</label>
</div>

<!-- Point 2 - Non U.S Citizen -->
<div class="box-header2">
    <input type="radio" id="point2" name="citizenship_status" value="Non U.S Citizen"
        <?= ($verification_1['citizenship_status'] ?? '') === 'Non U.S Citizen' ? 'checked' : '' ?>>
    <label for="point2" class="main-title2">2. A noncitizen national of the United States</label>
</div>

<!-- Point 3 - Lawful Permanent Resident -->
<div class="box-header2">
    <input type="radio" id="point3" name="citizenship_status" value="Lawful Permanent Resident"
        <?= ($verification_1['citizenship_status'] ?? '') === 'Lawful Permanent Resident' ? 'checked' : '' ?>>
    <label for="point3" class="main-title2" style="white-space:nowrap;">3. A lawful permanent resident:</label>
    <input type="text" class="form-control p-0" name="allen_registration_number"
        value="<?= $verification_1['allen_registration_number'] ?? '' ?>"
        placeholder="Allen Registration Number/USCIS Number">
</div>

<!-- Point 4 - Allen Authorized to Work -->
<div class="box-header2">
    <input type="radio" id="point4" name="citizenship_status" value="Allen Authorized to Work"
        <?= ($verification_1['citizenship_status'] ?? '') === 'Allen Authorized to Work' ? 'checked' : '' ?>>
    <label for="point4" class="main-title2">4. An alien authorized to work</label>


<div style="margin-left: 20px; margin-top: 5px;">
    <div>Registration Number: 
        <input type="text" class="form-control p-0" name="registration_number"
            value="<?= !empty($verification_1['registration_number']) ? $verification_1['registration_number'] : '' ?>">
    </div>
    <div style="margin-top: 5px;">
        Expiration Date: 
        <input type="date" class="form-control p-0" name="expiration_date"
            value="<?= !empty($verification_1['expiration_date']) ? $verification_1['expiration_date'] : '' ?>">
    </div>
</div>
</div>

            <div class="main-box">
                <!-- Left side with 80% width -->

                <div style="line-height:1;width: 80%;">
                    <div style="display: inline-block;">
                        <div style="display: inline-block;">
                            <input class="form-group ms-1" type="checkbox" name="allen_authorized_work"
                                <?= (!empty($verification_1['allen_authorized_work']) && $verification_1['allen_authorized_work'] === '1') ? 'checked' : '' ?>>
                            <label class="form-check-label ps-1" style="font-size:14px;" for="checking">4 . An alien
                                authorized to work until (expiration date, if applicable, mm/dd/yyyy):</label>
                            <input type="text" style="display: inline-block;" class="form-control p-0"
                                value="<?= !empty($verification_1['expiration_date']) ? $verification_1['expiration_date'] : '' ?>">
                            <label class="form-check-label ps-4" style="font-size:14px;" for="checking">Some aliens may
                                write "N/A" in the expiration date field. (See instructions)</label>
                        </div>
                        <label class="form-check-label ps-1 pt-2" style="font-size:14px;" for="checking">Aliens
                            authorized to work must provide only one of the following document number to complete Form
                            1-9:</label>
                        <label class="form-check-label ps-1 pb-2" style="font-size:14px;" for="checking">An Alien
                            Registration Number/USCIS Number OR Form 1-94 Admission Number OR Foreign Passport
                            Number</label>
                    </div>

                    <div style="display: inline-block;">
                        <label class="form-check-label ps-4" style="font-size:14px;" for="checking">1 . Alien
                            registration Number/USCIS Number </label>
                        <input type="text" style="width:200px;display: inline-block;" class="form-control p-0"
                            value="<?= !empty($verification_1['registration_number']) ? $verification_1['registration_number'] : '' ?>">
                    </div>
                    <div style="text-align: center;">
                        <strong>OR</strong>
                    </div>
                    <div style="display: inline-block;">
                        <label class="form-check-label ps-4" style="font-size:14px;" for="checking">2 . Fom 1-94
                            Admission Number </label>
                        <input type="text" style="width:278px;display: inline-block;" class="form-control p-0"
                            value="<?= !empty($verification_1['allen_registration_number']) ? $verification_1['allen_registration_number'] : '' ?>">
                    </div>
                    <div style="text-align: center;">
                        <strong>OR</strong>
                    </div>
                    <div style="display: inline-block;">
                        <label class="form-check-label ps-4" style="font-size:14px;" for="checking">3 . Foreign Passport
                            Number </label>
                        <input type="text" style="width:300px;display: inline-block;" class="form-control p-0"
                            value="<?= !empty($verification_1['passport_number']) ? $verification_1['passport_number'] : '' ?>">
                    </div>
                    <div style="display: inline-block;" class="mb-2">
                        <label class="form-check-label ps-5" style="font-size:14px;" for="checking">Country of Issuance
                        </label>
                        <input type="text" style="width:332px;display: inline-block;" class="form-control p-0"
                            value="<?= !empty($verification_1['country_of_issuance']) ? $verification_1['country_of_issuance'] : '' ?>">
                    </div>
                </div>

                <!-- Right side with 20% width for passport photo -->
                <div class="passport-photo">
                    <!-- <img src="path/to/passport.jpg" alt="Passport Photo" class="photo" /> -->
                </div>
            </div>
        </div>

        <div class="box-container mt-1">
            <div class="box-content">
                <table class="table table-bordered custom-table">
                    <tr>
                        <th style="width: 70%;">
                            <label class="input-label mb-0" style="height:17px;">Signature Of Employee<br><br></label>
                            <input type="text" class="form-input"
                                value="<?= !empty($verification_1['signature_of_employee']) ? $verification_1['signature_of_employee'] : '' ?>">
                        </th>
                        <th style="width: 30%;">
                            <label class="input-label mb-0" style="height:17px;">Today's Date<br><br></label>
                            <input type="date" class="form-input" value="<?= date('Y-m-d') ?>">
                        </th>
                    </tr>
                </table>
            </div>
        </div>

        <div class="box-container mt-1" style="border: 1px solid black;">
            <div class="box-content Certificate ps-2" style="l">
                <div class="info-heading">
                    Preparer and/or Translators Certificate (Check One)
                </div>
                <div class="row-layout">
                    <div class="left-section">
                        <input type="checkbox" id="checkbox1" <?= (!empty($verification_1['translator_certificate']) && $verification_1['translator_certificate'] === '1') ? 'checked' : '' ?> />
                        <label for="checkbox1" style="white-space:nowrap; font-size:13px;">I did not use a preparer or
                            translator.</label>
                    </div>
                    <div class="right-section">
                        <input type="checkbox" id="checkbox2" <?= (!empty($verification_1['translator_certificate']) && $verification_1['translator_certificate'] === '2') ? 'checked' : '' ?> />
                        <label for="checkbox2" style="white-space:nowrap; font-size:13px;">A preparer(s) and/or
                            translator(s) assisted the employee in completing Section 1.</label>
                    </div>
                </div>
                <div class="info-text">
                    (Fields below must be completed and signed when preparers and/or translators assist an employee in
                    completing Section.)
                </div>
            </div>
        </div>

        <div class="instructions m-0">
            <p class="instruction-bold m-0"><strong>I attest, under penalty of perjury, that I have assisted in the
                    completion of Section 1 of this form and that to the best knowledge the information is true and
                    correct.</strong></p>
        </div>

        <div class="box-container">

            <div class="box-content">
                <table class="table table-bordered custom-table">
                    <tr>
                        <th style="width: 70%;">
                            <label class="input-label mb-0" style="height:17px;">Signature or Preparer or
                                Translators</label>
                            <input type="text" class="form-input"
                                value="<?= !empty($verification_1['signature_of_translator']) ? $verification_1['signature_of_translator'] : '' ?>">
                        </th>
                        <!-- <th style="width: 30%;">
                            <label class="input-label mb-0" style="height:17px;">Today's Date</label>
                            <input type="date" class="form-input" value="<?= date('Y-m-d') ?>">
                        </th> -->
                    </tr>
                </table>
                <table class="table table-bordered custom-table">
                <tr>
                        <th style="width: 60%;">
                            <label class="input-label mb-0" style="height:17px;">First Name(Family Name)</label>
                            <input type="text" class="form-input"
                                  value="<?= !empty($verification_1['translator_first_name']) ? $verification_1['translator_first_name'] : '' ?>">
                         </th>
<th style="width: 40%;">
    <label class="input-label mb-0" style="height:17px;">Last Name(Given Name)</label>
    <input type="text" class="form-input"
        value="<?= !empty($verification_1['translator_last_name']) ? $verification_1['translator_last_name'] : '' ?>">
</th>
</tr>
                </table>
                <table class="table table-bordered custom-table">
                <table class="table table-bordered custom-table">
<tr>
    <th style="width: 30%;">
        <label class="input-label mb-0" style="height:17px;">Address</label>
        <input type="text" class="form-input"
            value="<?= !empty($verification_1['translator_address']) ? $verification_1['translator_address'] : '' ?>">
    </th>
    <th style="width: 20%;">
        <label class="input-label mb-0" style="height:17px;">City Or Town</label>
        <input type="text" class="form-input"
            value="<?= !empty($verification_1['translator_city']) ? $verification_1['translator_city'] : '' ?>">
    </th>
    <th style="width: 5%;">
        <label class="input-label mb-0" style="height:17px;">State</label>
        <input type="text" class="form-input"
            value="<?= !empty($verification_1['translator_state']) ? $verification_1['translator_state'] : '' ?>">
    </th>
    <th style="width: 10%;">
        <label class="input-label mb-0" style="height:17px;">Zip</label>
        <input type="text" class="form-input"
            value="<?= !empty($verification_1['translator_zip']) ? $verification_1['translator_zip'] : '' ?>">
    </th>
</tr>
</table>
                </table>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php if (!empty($verification)): ?>
<!-- 
<style>
    .form-group-custom {
        margin-bottom: 15px;
    }

    .input-inline {
        display: inline-block;
        width: calc(100% - 10px);
        padding: 2px;
        border: none;
        border-bottom: 1px solid #333;
        background-color: transparent;
        margin-right: 5px;
        margin-bottom: 10px;
    }

    .input-inline:focus {
        outline: none;
        border-bottom: 2px solid #333;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 10px solid black;
        padding-bottom: 10px;
        margin-bottom: 5px;
    }

    .form-header img {
        max-width: 100px;
        height: auto;
    }

    .form-header .header-title {
        text-align: center;
        flex-grow: 1;
    }

    .section-title {
        background-color: #c0c0c0;
        /* Optional background color */
        font-weight: bold;
        font-size: 10px;
        /* Reduced font size */
        border: 1px solid #000;
        padding: 0.25rem;
        /* Reduced padding */
        margin-bottom: 5px;
        /* Reduced bottom margin */
    }

    .section-title p {
        font-size: 11px;
        /* Smaller font size for paragraph */
        font-style: italic;
        font-weight: normal;
        margin: 0;
        /* Removes extra margin */
    }

    .form-table {
        width: 100%;

    }

    .form-table th,
    .form-table td {
        text-align: left;
    }

    .input-label {
        display: block;
        font-weight: bold;
        font-size: 10px;
        margin-bottom: 4px;
    }

    .form-input {
        width: 100%;
        padding: 0;
        margin: 0;
        border: none;
    }

    .highlight-text {
        font-style: italic;
    }

    .table-bordered {
        border: 1px solid black;
        border-collapse: collapse;
        margin: 0;
        /* Remove any margin */
        padding: 0;
        /* Remove padding if necessary */
        width: 100%;
        padding: 0.2rem;
        height: 10%;
    }

    .form-input:focus {
        outline: none;
        border: none;
    }

    .form-table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;

    }

    @media (max-width: 768px) {
        .form-header {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .form-header img {
            margin-bottom: 10px;
        }

        .form-header .header-title {
            flex-grow: 0;
            margin-bottom: 10px;
        }

        .form-table th,
        .form-table td {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        .form-table th {
            text-align: left;
            border-top: 1px solid #ccc;
            /* Optional: to visually separate stacked cells */
        }
    }

    .flex-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        text-align: center;
        margin: 0;
        /* Remove any margin */
        padding: 0;
        /* Remove padding if necessary */

    }

    .flex-item {

        margin: 0;
        /* Remove any margin */
        padding: 0;
        /* Remove padding if necessary */
    }

    .divider {
        font-weight: bold;
        margin: 0;
        /* Remove any margin */
        padding: 0;
        /* Remove any padding */
        line-height: 1.5;
        /* Ensures consistent height without additional spacing */
    }

    .description {
        font-size: 12px;
    }

    label {
        margin-left: 10px;
        /* Adjust the value to your desired margin */
        display: block;
        /* Ensure the label remains on its own line */
    }

    .additional-info {
        height: 200px;
    }

    .qr-code-box {
        text-align: center;
        border: 1px solid #000;
        padding: 10px;
        height: 200px;
    }

    .form-control {
        border: none;

        border-radius: 0;
        box-shadow: none;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #333;
        margin: 0;
        padding: 0;
        width: 100%;
    }


    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .logo {
        width: 70px;
        /* Adjust as needed for the desired size */
        height: auto;
        margin-right: 15px;
    }

    .header-title {
        text-align: center;
        flex: 1;
        min-width: 200px;
    }

    .title-main,
    .title-sub {
        font-size: 15px;
        margin: 0;
        font-weight: bold;
    }

    .subtitle {
        font-weight: normal;
    }

    .form-info {
        text-align: center;
        min-width: 150px;
    }

    .info-main {
        font-size: 15px;
        margin: 0;
        font-weight: bold;
        color: black;
    }

    .info-small {
        font-size: 10px;
        margin: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .form-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .logo {
            margin-bottom: 10px;
        }

        .header-title,
        .form-info {
            text-align: center;
            margin-bottom: 10px;
        }
    }

    th {
        font-weight: bold;
    }

    .form-control-borderless {
        border: none;
        padding: 0;
        margin-bottom: 5px;
        box-shadow: none;
        outline: none;
        width: 100%;
    }

    .form-section {
        padding: 0 5px;
    }

    .form-label {
        font-size: 20px;
        margin-bottom: 2px;
    }

    .row-border {
        border: 1px solid black;
        padding: 5px;
        margin: 0;
    }

    .p-compact {
        padding: 2px;
    }

    .section-header {
        background-color: #c0c0c0;
        border: 1px solid black;
        padding: 5px;
    }

    .section-input {
        padding: 2px;
        border-right: 1px solid black;
    }

    .last-section {
        padding: 2px;
    }

    .form-control,
    .row,
    .col-md-4,
    .col-md-6 {
        padding: 2px !important;
        margin: 0 !important;
    }

    #form-content {
        width: 100%;
        max-width: 800px;
        /* Set maximum width */
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
</style> -->

<div class="container my-5" id="form-content-1">
    <form style="page-break-inside: avoid;" method="post" id="employee_eligibility_verification_form">
        <!-- <div class="form-header">
            <img src="../modules/forms/images/us_Department.svg" class="logo" alt="Logo">
            <div class="header-title">
                <h1 class="title-main">Employment Eligibility Verification</h1>
                <h1 class="title-sub">Department of Homeland Security<br><span class="subtitle">U.S. Citizenship and
                        Immigration Services</span></h1>
            </div>
            <div class="form-info">
                <h2 class="info-main">USCIS</h2>
                <h2 class="info-main">Form I-9</h2>
                <h2 class="info-small">OMB No. 1615-0047</h2>
                <h2 class="info-small">Expires 10/31/2022</h2>
            </div>
        </div> -->

        <div class="section-title"
            style="padding: 0.2rem; border: 1px solid #000; background-color:#c0c0c0;; margin-bottom: 3px;">
            <h1 style="font-size: 14px; margin: 0; font-weight: bold;">Section 2. Employer or Authorized
                Representative Review and Verification</h1>

            <p style="margin: 3px 0; font-size: 11px; line-height:1;">
                (Employers or their authorized representative must complete and sign Section 2 within 3 business
                days of the employee's first day of employment. You must physically examine one document from List A
                OR a combination of one document from List B and one document from List C as listed on the "Lists of
                Acceptable Documents.")
            </p>
        </div>

        <table class="table-bordered" style="width: 100%; border-collapse: collapse; margin: 0;">
            <tr>
                <th style="padding: 2px 3px; text-align: center; width: 14%; font-size: 10px;">Employee Info from
                    Section 1</th>
                <th style="width: 18%; padding: 0 1px;">
                    <label style="font-size:9px; margin: 0; padding: 0;">Last Name (Family Name)</label>
                    <input name="last_name" type="text" class="form-input"
                        style="width: 98%; padding: 1px; margin: 0; font-size: 10px; box-sizing: border-box;"
                        value="<?= !empty($craft_data['last_name']) ? $craft_data['last_name'] : '' ?>">
                </th>
                <th style="width: 15%; padding: 0 1px;">
                    <label style="font-size:9px; margin: 0; padding: 0;">First Name (Given Name)</label>
                    <input name="first_name" type="text" class="form-input"
                        style="width: 98%; padding: 1px; margin: 0; font-size: 10px; box-sizing: border-box;"
                        value="<?= !empty($craft_data['first_name']) ? $craft_data['first_name'] : '' ?>">
                </th>
                <th style="width: 1%; padding: 0 1px;">
                    <label style="font-size:9px; margin: 0; padding: 0;">M.I.</label>
                    <input name="mi" value="<?= !empty($verification['mi']) ? $verification['mi'] : '' ?>"
                        type="text" class="form-input"
                        style="width: 98%; padding: 1px; margin: 0; font-size: 10px; box-sizing: border-box;">
                </th>
                <th style="width: 14%; padding: 0 1px;">
                    <label style="font-size:9px; margin: 0; padding: 0;">Citizenship/Immigration Status</label>
                    <input name="citizen_immigration_status"
                        value="<?= !empty($verification['citizen_immigration_status']) ? $verification['citizen_immigration_status'] : '' ?>"
                        type="text" class="form-input"
                        style="width: 98%; padding: 1px; margin: 0; font-size: 10px; box-sizing: border-box;">
                </th>
            </tr>
        </table>

        <div style="margin-top: 0;">
            <div class="flex-container"
                style="display: flex; justify-content: space-between; align-items: center; font-size: 10px;">
                <div class="flex-item divider" style="text-align: center; margin: 0 3px;">
                    <div>List A</div>
                    <div class="description" style="font-size: 12px;">Identity and Employment Authorization</div>
                </div>
                <div class="divider"
                    style="margin-top: 0; padding-top: 0; margin-right: 60px; font-size: 12px; font-weight: bold;">
                    OR</div>


                <div class="flex-item divider" style="margin: 0; padding-right: 80px;">
                    <div style="margin:0;">List B</div>
                    <div class="description" style="font-size: 12px; ">Identity</div>
                </div>

                <div class="divider"
                    style="text-align: center; margin: 0 18px; font-weight: bold; padding-right:40px;">AND</div>
                <div class="flex-item divider" style="text-align: center; margin: 0 3px;">
                    <div>List C</div>
                    <div class="description" style="font-size: 12px;">Employment Authorization</div>
                </div>
            </div>
        </div>
        <div style="margin: 0; padding: 0; border: 1px solid black;">
            <div style="display: flex; flex-direction: row; margin: 0; padding: 0;">
                <!-- Left Column -->
                <div style="flex: 1; padding: 0; border-right: 1px solid black;">
                    <label style="font-size:9px; margin: 0; padding-left:5px;">Document Title</label>
                    <input type="text" name="document_title"
                        value="<?= !empty($verification['document_title']) ? $verification['document_title'] : '' ?>"
                        style="width: 100%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Issuing Authority</label>
                    <input type="text" name="issuing_authority"
                        value="<?= !empty($verification['issuing_authority']) ? $verification['issuing_authority'] : '' ?>"
                        style="width: 100%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Document Number</label>
                    <input type="number" name="document_number"
                        value="<?= !empty($verification['document_number']) ? $verification['document_number'] : '' ?>"
                        style="width: 100%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px;  margin-bottom:1px; padding-left:5px;">Expiration Date (if
                        any)</label>
                    <input type="date" name="expiration_date"
                        value="<?= !empty($verification['expiration_date']) ? $verification['expiration_date'] : '' ?>"
                        style="width: 100%; border: none; border-bottom: 3px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Document Title</label>
                    <input type="text" name="document_title_1"
                        value="<?= !empty($verification['document_title_1']) ? $verification['document_title_1'] : '' ?>"
                        style="width: 100%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Issuing Authority</label>
                    <input type="text" name="issuing_authority_1"
                        value="<?= !empty($verification['issuing_authority_1']) ? $verification['issuing_authority_1'] : '' ?>"
                        style="width: 100%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Document Number</label>
                    <input type="number" name="document_number_1"
                        value="<?= !empty($verification['document_number_1']) ? $verification['document_number_1'] : '' ?>"
                        style="width: 100%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Expiration Date (if any)</label>
                    <input type="date" name="expiration_date_1"
                        value="<?= !empty($verification['expiration_date_1']) ? $verification['expiration_date_1'] : '' ?>"
                        style="width: 100%; border: none; border-bottom: 3px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Document Title</label>

                    <input type="text" name="document_title_2"
                        value="<?= !empty($verification['document_title_2']) ? $verification['document_title_2'] : '' ?>"
                        style="width: 98%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Issuing Authority</label>
                    <input type="text" name="issuing_authority_2"
                        value="<?= !empty($verification['issuing_authority_2']) ? $verification['issuing_authority_2'] : '' ?>"
                        style="width: 98%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Document Number</label>
                    <input type="number" name="document_number_2"
                        value="<?= !empty($verification['document_number_2']) ? $verification['document_number_2'] : '' ?>"
                        style="width: 98%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Expiration Date (if any)</label>
                    <input type="date" name="expiration_date_2"
                        value="<?= !empty($verification['expiration_date_2']) ? $verification['expiration_date_2'] : '' ?>"
                        style="width: 98%; border: none; padding-left:5px; margin: 0; outline: none;">

                </div>

                <!-- Middle Column -->
                <div style="flex: 1; padding: 0;">
                    <label style="font-size:9px; margin: 0; padding-left:5px;">Document Title</label>
                    <input type="text" name="document_title_3"
                        value="<?= !empty($verification['document_title_3']) ? $verification['document_title_3'] : '' ?>"
                        style="width: 98%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Issuing Authority</label>
                    <input type="text" name="issuing_authority_3"
                        value="<?= !empty($verification['issuing_authority_3']) ? $verification['issuing_authority_3'] : '' ?>"
                        style="width: 98%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Document Number</label>
                    <input type="number" name="document_number_3"
                        value="<?= !empty($verification['document_number_3']) ? $verification['document_number_3'] : '' ?>"
                        style="width: 98%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">


                    <label style="display: block; margin:0;padding:0; font-size:10px;">Expiration Date (if
                        any)</label>
                    <input type="date" name="expiration_date_2"
                        value="<?= !empty($verification['expiration_date_3']) ? $verification['expiration_date_3'] : '' ?>"
                        style="width: 100%; border: none; border-bottom: 3px solid black; padding:0;  margin: 0;">

                    <div style="margin: 10px; padding: 10px; border: 1px solid black;">
                        <label style="font-size:9px; margin: 0; padding-left:5px;">Additional Information</label>
                        <textarea name="additional_information"
                            style="width: 98%; height: 165px; border: none; padding-left:5px; margin: 0; resize: none; outline: none;"><?= !empty($verification['additional_information']) ? $verification['additional_information'] : '' ?></textarea>
                    </div>
                </div>

                <!-- Right Column -->
                <div style="flex: 1; padding: 0;">
                    <label style="font-size:9px; margin: 0; padding-left:5px;">Document Title</label>
                    <input type="text" name="document_title_4"
                        value="<?= !empty($verification['document_title_4']) ? $verification['document_title_4'] : '' ?>"
                        style="width: 100%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Issuing Authority</label>
                    <input type="text" name="issuing_authority_4"
                        value="<?= !empty($verification['issuing_authority_4']) ? $verification['issuing_authority_4'] : '' ?>"
                        style="width: 100%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin: 0; padding-left:5px;">Document Number</label>
                    <input type="number" name="document_number_4"
                        value="<?= !empty($verification['document_number_4']) ? $verification['document_number_4'] : '' ?>"
                        style="width: 100%; border: none; border-bottom: 1px solid black; padding-left:5px; margin: 0; outline: none;">

                    <label style="font-size:9px; margin-top:1px; padding:0;">Expiration Date (if any)</label>
                    <input type="date" name="expiration_date_3"
                        value="<?= !empty($verification['expiration_date_4']) ? $verification['expiration_date_4'] : '' ?>"
                        style="width: 100%; border: none; border-bottom: 3px solid black; padding:0;  outline: none;">

                    <div style="margin: 10px; padding: 10px; border: 1px solid black;">
                        <label style="font-size:9px; margin: 0; padding-left:5px;">QR Code - Sections 2 & 3</label>
                        <input type="text" name="qr_code"
                            style="width: 100%; border: none; padding-left:5px; margin: 0; outline: none;">
                        <p style="margin-top: 2px; font-size:10px;">Do Not Write In This Space</p>
                    </div>
                </div>
            </div>
        </div>
        <div>
                <div class="col-12 p-0" style="font-weight: bold; color: black; font-size: 10px; line-height: 1.2;">
                    <p style="margin: 0;">
                        <strong>Certification:</strong> I attest, under penalty of perjury, that (1) I have examined
                        the document(s) presented by the above-named employee,
                        (2) the above-listed document(s) appear to be genuine and to relate to the employee named,
                        and (3) to the best of my knowledge the employee is authorized to work in the United States.
                        <br>
                        The employee's first day of employment (<em>mm/dd/yyyy</em>):
                        <input type="date" name="employee_start_date"
                            value="<?= !empty($data['employee_start_date']) ? $data['employee_start_date'] : '' ?>"
                            style="width: auto; display: inline; border: none; border-bottom: 1px solid black; margin-left: 5px;">
                        <em style="margin-left: 5px;">(See instructions for exemptions)</em>
                    </p>
                </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 9px; border: 1px solid black;">
                <!-- First Row -->
                <tr style="border-bottom: 1px solid black;">
                    <td style="padding: 2px; border-right: 1px solid black; width: 40%;">
                        <label style="font-size: 9px; margin: 0; padding: 0;">Signature of Employer or Authorized
                            Representative</label>
                        <input type="text" name="employer_signature_1"
                            value="<?= !empty($data['employer_signature_1']) ? $data['employer_signature_1'] : '' ?>"
                            style="width: 100%; border: none; outline: none; padding: 0; margin: 0; font-size: 9px;">
                    </td>
                    <td style="padding: 2px; border-right: 1px solid black; width: 20%;">
                        <label style="font-size: 9px; margin: 0; padding: 0;">Today's Date</label>
                        <input type="date" name="todays_date" value="<?= date('Y-m-d') ?>"
                            style="width: 100%; border: none; outline: none; padding: 0; margin: 0; font-size: 9px;">
                    </td>
                    <td style="padding: 2px; width: 40%;">
                        <label style="font-size: 9px; margin: 0; padding: 0;">Title of Employer or Authorized
                            Representative</label>
                        <input type="text" name="employer_title"
                            value="<?= !empty($data['employer_title']) ? $data['employer_title'] : '' ?>"
                            style="width: 100%; border: none; outline: none; padding: 0; margin: 0; font-size: 9px;">
                    </td>
                </tr>
                <!-- Second Row -->
                <tr style="border-bottom: 1px solid black;">
                    <td style="padding: 2px; border-right: 1px solid black;">
                        <label style="font-size: 9px; margin: 0; padding: 0;">Last Name of Employer or Authorized
                            Representative</label>
                        <input type="text" name="employer_last_name"
                            value="<?= !empty($data['employer_last_name']) ? $data['employer_last_name'] : '' ?>"
                            style="width: 100%; border: none;  outline: none; padding: 0; margin: 0; font-size: 9px;">
                    </td>
                    <td style="padding: 2px; border-right: 1px solid black;">
                        <label style="font-size: 9px; margin: 0; padding: 0;">First Name of Employer or Authorized
                            Representative</label>
                        <input type="text" name="employee_first_name"
                            value="<?= !empty($data['employee_first_name']) ? $data['employee_first_name'] : '' ?>"
                            style="width: 100%; border: none;  outline: none; padding: 0; margin: 0; font-size: 9px;">
                    </td>
                    <td style="padding: 2px;">
                        <label style="font-size: 9px; margin: 0; padding: 0;">Employer's Business or Organization
                            Name</label>
                        <input type="text" name="employer_business_name"
                            value="<?= !empty($data['employer_business_name']) ? $data['employer_business_name'] : '' ?>"
                            style="width: 100%; border: none;  outline: none; padding: 0; margin: 0; font-size: 9px;">
                    </td>
                </tr>
                <!-- Third Row -->
                <tr>
                    <td style="padding: 2px; border-right: 1px solid black;">
                        <label style="font-size: 9px; margin: 0; padding: 0;">Employer's Business or Organization
                            Address <em>(Street Number and Name)</em></label>
                        <input type="text" name="employer_address"
                            value="<?= !empty($data['employer_address']) ? $data['employer_address'] : '' ?>"
                            style="width: 100%; border: none;  outline: none; padding: 0; margin: 0; font-size: 9px;">
                    </td>
                    <td style="padding: 2px; border-right: 1px solid black;">
                        <label style="font-size: 9px; margin: 0; padding: 0;">City or Town</label>
                        <input type="text" name="employer_city"
                            value="<?= !empty($data['employer_city']) ? $data['employer_city'] : '' ?>"
                            style="width: 100%; border: none;  outline: none; padding: 0; margin: 0; font-size: 9px;">
                    </td>
                    <td style="padding: 2px;">
                        <div style="display: flex;">
                            <div style="width: 50%; padding-right: 1px; border-right: 1px solid black;">
                                <label style="font-size: 9px; margin: 0; padding: 0;">State</label>
                                <input type="text" name="employer_state"
                                    value="<?= !empty($data['state']) ? $data['state'] : '' ?>"
                                    style="width: 100%; border: none;  outline: none; padding: 0; margin: 0; font-size: 9px;">
                            </div>
                            <div style="width: 50%; padding-left: 1px;">
                                <label style="font-size: 9px; margin: 0; padding: 0;">ZIP Code</label>
                                <input type="number" name="employer_zip"
                                    value="<?= !empty($data['employer_zip']) ? $data['employer_zip'] : '' ?>"
                                    style="width: 100%; border: none;  outline: none; padding: 0; margin: 0; font-size: 9px;">
                            </div>
                        </div>
                    </td>
                </tr>
            </table>


            <div class="mb-1" style="font-size: 12px; padding-top: 4px;">
                <!-- Header Row -->
                <div class="row"
                    style="background-color: #c0c0c0; border: 1px solid black; padding: 2px; margin: 0;">
                    <div class="col-12">
                        <strong>Section 3. Reverification and Rehires</strong> <em>(To be completed and signed by
                            employer or authorized representative.)</em>
                    </div>
                </div>
                <!-- Sub-header Row -->
                <div class="row" style="border: 1px solid black; border-top: none; margin: 0;">
                    <div class="col-md-8"
                        style="padding: 4px; border-right: 1px solid black; background-color: #e0e0e0;">
                        <strong>A. New Name <em>(if applicable)</em></strong>
                    </div>
                    <div class="col-md-4" style="padding: 4px; background-color: #e0e0e0;">
                        <strong>B. Date of Rehire <em>(if applicable)</em></strong>
                    </div>
                </div>
                <!-- Input Fields Table -->
                <table class="table-bordered mb-2"
                    style="width: 100%; border-collapse: collapse; margin: 0; border: 1px solid black;">
                    <tr>
                        <!-- Inputs for New Name (if applicable) -->
                        <th style="padding: 2px;  width: 25%; ">
                            <label style="font-size: 9px; margin: 0; padding: 0; display: block;">Last Name
                                <em>(Given Name)</em></label>
                            <input type="text" name="last_name_1"
                                value="<?= !empty($verification['last_name_1']) ? $verification['last_name_1'] : '' ?>"
                                style="width: 95%; border: none; outline: none; padding: 0 0.5px; margin: 0;">
                        </th>
                        <th style="width: 25%; padding: 2px;">
                            <label style="font-size: 9px; margin: 0; padding: 0; display: block;">First Name
                                <em>(Given Name)</em></label>
                            <input type="text" name="first_name_1"
                                value="<?= !empty($verification['first_name_1']) ? $verification['first_name_1'] : '' ?>"
                                style="width: 95%; border: none; outline: none; padding: 0 0.5px; margin: 0;">
                        </th>
                        <th style="width: 15%; padding: 2px;">
                            <label style="font-size: 9px; margin: 0; padding: 0; display: block;">Middle
                                Initial</label>
                            <input type="text" name="middle_initial"
                                value="<?= !empty($verification['middle_initial']) ? $verification['middle_initial'] : '' ?>"
                                style="width: 95%; border: none; outline: none; padding: 0 0.5px; margin: 0;">
                        </th>
                        <!-- Date of Rehire Input -->
                        <th style="width: 35%; padding: 2px;">
                            <label style="font-size: 9px; margin: 0; padding: 0; display: block;">Date
                                <em>(mm/dd/yyyy)</em></label>
                            <input type="date" name="rehire_date"
                                value="<?= !empty($verification['rehire_date']) ? $verification['rehire_date'] : '' ?>"
                                style="width: 95%; border: none; outline: none; padding: 0 0.5px; margin: 0;">
                        </th>
                    </tr>
                </table>
            </div>



            <div style="font-size:0.6rem;">
                <!-- Header Row -->
                    <div class="col-12 m-1" style="line-height: 1.1;">
                        <strong>C.</strong> If the employee's previous grant of employment authorization has
                        expired, provide the information for the document or receipt that establishes continuing
                        employment authorization in the space provided below.
                    </div>
                <!-- Input Fields Row -->
                <table class="table-bordered mb-2"
                    style="width: 100%; border-collapse: collapse; margin: 0; border: 1px solid black;">
                    <tr>
                        <!-- Inputs for New Name (if applicable) -->
                        <th style="width: 25%; padding: 2px;">
                            <label style="font-size: 10px; margin-bottom: 2px;">Document Title</label>
                            <input type="text" name="document_title_5"
                                value="<?= !empty($verification['document_title_5']) ? $verification['document_title_5'] : '' ?>"
                                style="width: 95%; border: none; outline: none;  padding-left:9px;; margin: 0;">
                        </th>
                        <th style="width: 15%; padding: 2px;">
                            <label style="font-size: 10px; margin-bottom: 2px;">Document Number</label>
                            <input type="number" name="document_number_5"
                                value="<?= !empty($verification['document_number_5']) ? $verification['document_number_5'] : '' ?>"
                                style="width: 95%; border: none; outline: none; padding-left:9px; margin: 0;">
                        </th>
                        <!-- Date of Rehire Input -->
                        <th style="width: 35%; padding: 2px;">
                            <label style="font-size: 10px; margin-bottom: 2px;">Expiration Date <em>(if
                                    any)</em></label>
                            <input type="date" name="expiration_date_4"
                                value="<?= !empty($verification['expiration_date_4']) ? $verification['expiration_date_4'] : '' ?>"
                                style="width: 95%; border: none; outline: none; padding: 2px; margin: 0;">
                        </th>
                    </tr>
                </table>
            </div>
            <div class="mb-0" style="font-size: 0.6rem;font-weight:bold;">
                <!-- Header Text -->
                    <div class="col-12 p-0" style="line-height: 1.2;">
                        <p style="margin: 0;">
                            I attest, under penalty of perjury, that to the best of my knowledge, this employee is
                            authorized to work in the United States, and if the employee presented document(s), the
                            document(s) I have examined appear to be genuine and to relate to the individual.
                        </p>
                    </div>
                <!-- Input Fields Row -->

                <table class="table-bordered mb-2"
                    style="width: 100%; border-collapse: collapse; margin: 0; border: 1px solid black;">
                    <tr>
                        <!-- Inputs for New Name (if applicable) -->
                        <th style="width: 25%; padding: 2px;">
                            <label style="font-size:9px; margin-bottom: 2px;">Signature of Employer or Authorized
                                Representative</label>
                            <input type="text" name="employer_signature"
                                value="<?= !empty($verification['employer_signature']) ? $verification['employer_signature'] : '' ?>"
                                style="width: 95%; border: none; outline: none; padding-left:9px; margin: 0;">
                        </th>
                        <th style="width: 15%; padding: 2px;">
                            <label style="font-size:9px; margin-bottom: 2px;">Today's Date</label>
                            <input type="date" name="todays_date_2" value="<?= date('Y-m-d') ?>"
                                style="width: 95%; border: none; outline: none; padding-left:9px; margin: 0;">
                        </th>
                        <!-- Date of Rehire Input -->
                        <th style="width: 35%; padding: 2px;">
                            <label style="font-size:9px; margin-bottom: 2px;">Name of Employer or Authorized
                                Representative</label>
                            <input type="text" name="employee_name"
                                value="<?= !empty($verification['employee_name']) ? $verification['employee_name'] : '' ?>"
                                style="width: 95%; border: none; outline: none; padding-left:9px; margin: 0;">
                        </th>
                    </tr>
                </table>
            </div>
    </form>
</div>
</div>

<?php endif; ?>

<?php if (!empty($mvr)): ?>


<div class="container my-5" id="form-content-3">
    <h2 class="form-title mt-3">IDENTIFICATION CONSENT FORM</h2>
    <form style="page-break-inside: avoid;" method="post" id="mvr_form">
        <div class="mb-3" style=" line-height: 1; ">
            <p style="display: inline;margin: 0;">
                In conjunction with my potential employment at
            </p>
            <input type="text" style="display: inline; width: 48%;" class="form-control" name="company_name"
                value="<?= !empty($mvr['company_name']) ? $mvr['company_name'] : '' ?>" placeholder=""></br>

            <p style="display: inline;margin: 0;">
                ("the company"), I
            </p>
            <input type="text" style="display: inline; width: 52%;" class="form-control" name="applicant_name"
                value="<?= !empty($mvr['applicant_name']) ? $mvr['applicant_name'] : '' ?>" placeholder="">
            <p style="display: inline;margin: 0;">
                (applicant) consent to the
            </p></br>

            <p style="text-align:justify">
                release of my Motor Vehicle Records (MVR) to the company.
                I understand the company will use these records to evaluate my suitability to fulfill driving duties
                that may be related to the position for which I am applying.
                I also consent to the review, evaluation, and other use of any MVR I may have provided to the
                company.
                </br>
                This consent is given in satisfaction of Public Law 18 USC 2721 et. Seq., “Federal Drivers Privacy
                Protection Act”, and is intended to constitute “written consent” as required by this Act.
            </p>

        </div>


        <div class="mt-5 mb-5">
<p style="display: inline; margin: 0; vertical-align: middle;">
Signed (applicant)
</p>
<div class="signature-container" style="display: inline-block; margin-left: 20px; vertical-align: middle;">
<div class="signature-image" style="width: 250px; height: 100px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; background: white;">
    <?php if (!empty($signature['signature']) && strpos($signature['signature'], 'data:image/png') === 0): ?>
        <img src="<?php echo htmlspecialchars($signature['signature']); ?>" 
             alt="Applicant Signature" 
             style="max-width: 100%; max-height: 100%; object-fit: contain;">
    <?php else: ?>
        <div style="color: #777; font-style: italic;">No signature found</div>
    <?php endif; ?>
</div>
<?php if (!empty($signature['created_at'])): ?>
    <div class="signature-date" style="font-size: 15px; margin-top: 8px;">
    <input 
        type="text" 
        class="form-control" 
        name="date" 
        id="date"
        placeholder="MM/DD/YYYY"
        value="<?= !empty($craft_data['created_at']) ? date('m/d/Y', strtotime($craft_data['created_at'])) : '' ?>"
    >        <span><?php echo date('m/d/Y', strtotime($signature['created_at'])); ?></span>
    </div>
<?php endif; ?>
</div>
</div>

        <div class="mb-5">
            <p style="display: inline;margin: 0;">
                Date
            </p>
            <input type="date" style="display: inline; width: 94%;" class="form-control" name="date"
                value="<?= !empty($mvr['date']) ? $mvr['date'] : '' ?>" placeholder="">
        </div>

        <div class="mb-3">
            <p style="display: inline;margin: 0;">
                Driver's License Number:
            </p>
            <input type="text" style="display: inline; width: 40%;" class="form-control" name="license_number"
                value="<?= !empty($mvr['license_number']) ? $mvr['license_number'] : '' ?>" placeholder="">
            <p style="display: inline;margin: 0;">
                State
            </p>
            <input type="text" style="display: inline; width: 25%;" class="form-control" name="state"
                value="<?= !empty($mvr['state']) ? $mvr['state'] : '' ?>" placeholder="">
        </div>
    </form>
</div>


<!-- 
<style>
    /* Top section with logo and address */
    .top-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 15px 0;
        border-bottom: 4px dotted #333;
    }

    .top-section img {
        max-height: 50px;
    }

    /* Styling for form titles */
    h2,
    h3 {
        color: #333;
        text-align: center;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    /* Input field styling */
    .form-control {
        border: none;
        border-bottom: 2px solid #333;
        border-radius: 0;
        box-shadow: none;
        padding: 0rem .25rem;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #333;
    }

    .container {
        max-width: 800px;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        line-height: 0.9;

    }

    .centered-box {
        border: 2px solid black;
        height: 200px;
        width: 100%;
        justify-content: center;
        text-align: center;
        font-size: 1em;
        /* Adjust text size if needed */
    }

    @media print {
        .container {
            page-break-inside: avoid;
        }
    }
</style> -->

<div class="container my-5" id="form-content-4">
    <form style="page-break-inside: avoid;" method="post" id="quickbook_form">

        <!-- Top Section with Logo and Address -->
        <div class="top-section pt-0">
            <div>
                <h2 class="m-0">Intuit Direct Deposit</h2>
            </div>
            <div class="text-end">
                <img src="../modules/forms/images/intuit.png"> <!-- Replace 'logo.png' with actual logo path -->
            </div>
        </div>
        <!-- <h2 class="bg-dark text-white p-2">Employee Information</h2> -->
        <h5 class="m-0">Employee Direct Desposit Authorization</h5>
        <!-- Personal Information Section -->
        <div class="row">
            <div class="form-group d-flex align-items-center mt-2">
                <label for="instructions" class="mb-0"><strong>Instructions:</strong></label>
                <input name="instructions" class="form-control flex-grow-1" type="text"
                    value="<?= !empty($quick_book['instructions']) ? $quick_book['instructions'] : '' ?>">
            </div>
            <div class="d-flex pb-0 mb-0 mt-1">
                <label for="Employee" class="mb-0 me-1">
                    <p style="text-decoration:underline;">Employee:</p>
                </label>
                <p class="flex-grow-1 mb-0">Fill out and return to your employer.</p>
            </div>
            <div class="d-flex pb-0 mb-0" style=" margin-top: -15px;">
                <label for="employer" class="mb-0 me-1">
                    <p style="text-decoration:underline;">Employer:</p>
                </label>
                <p class="flex-grow-1">Save for your files only.</p>
            </div>

            <div class="d-flex pb-0 mb-0" style=" margin-top: -7px; text-align:justify">
                <p>This document must be signed by employees requesting automatic deposit of paychecks and
                    retained on file by the employer. Do not send this form to Intuit. Employees must attach a
                    voided
                    check for each of their accounts to help verify their account numbers and bank routing numbers.
                </p>
            </div>
        </div>

        <div class="row" style="margin-top: -10px;">
            <div class="form-group d-flex align-items-center">
                <label for="instructions" class="mb-0" style="white-space: nowrap;">
                    <strong>Bank Name:</strong>
                </label>
                <input name="bank_name" class="form-control flex-grow-1" type="text"
                    value="<?= !empty($quick_book['bank_name']) ? $quick_book['bank_name'] : '' ?>">
            </div>
            <div class="form-group d-flex align-items-center">
                <label for="instructions" class="mb-0" style="white-space: nowrap;">
                    <strong>Account </strong>
                </label>
                <input name="account_2" class="form-control flex-grow-1" type="text"
                    value="<?= !empty($quick_book['account_1']) ? $quick_book['account_1'] : '' ?>">
            </div>

            <div class="form-group d-flex align-items-center">
                <label for="instructions" class="mb-0 pt-2" style="white-space: nowrap;">
                    <p>Account 1 Type:</p>
                </label>
                <div class="d-flex align-items-center ps-3" style="gap: 20px;">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="accountType" id="checking"
                            value="checking" style="transform: scale(1.5);" <?= (!empty($quick_book['account_type']) && $quick_book['account_type'] === 'checking') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="checking">Checking</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="accountType" id="savings" value="savings"
                            style="transform: scale(1.5);" <?= (!empty($quick_book['account_type']) && $quick_book['account_type'] === 'savings') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="savings">Savings</label>
                    </div>
                </div>
            </div>

            <div class="form-group d-flex align-items-center" style="margin-top: -25px;">
                <label for="instructions" class="mb-0 pt-3" style="white-space: nowrap;">
                    <p> Bank routing number (ABA number): </p>
                </label>
                <input name="aba_number" class="form-control flex-grow-1" type="text"
                    value="<?= !empty($quick_book['aba_number']) ? $quick_book['aba_number'] : '' ?>">
            </div>

            <div class="form-group d-flex align-items-center" style="margin-top: -25px;">
                <label for="instructions" class="mb-0 pt-3" style="white-space: nowrap;">
                    <p> Account Number: </p>
                </label>
                <input name="account_number" class="form-control flex-grow-1" type="text"
                    value="<?= !empty($quick_book['account_number']) ? $quick_book['account_number'] : '' ?>">
            </div>
        </div>

        <div class="row">

            <div class="form-group d-flex align-items-center" style="margin-top: -25px;">
                <label for="instructions" class="mb-0 pt-3" style="white-space: nowrap;">
                    <p> Percentage or dollar amount to be deposited to this account:</p>
                </label>
                <input name="percentage" class="form-control flex-grow-1" type="number"
                    value="<?= !empty($quick_book['percentage']) ? $quick_book['percentage'] : '' ?>">
            </div>

            <!-- <div class="form-group d-flex align-items-center">
                <label for="instructions" class="mb-0" style="white-space: nowrap;">
                    <strong>Account 2</strong> (remainder to be deposited to this account)
                </label>
                <input name="account_2" class="form-control flex-grow-1" type="text"
                    value="<?= !empty($quick_book['account_2']) ? $quick_book['account_2'] : '' ?>">
            </div> -->

            <!-- <div class="form-group d-flex align-items-center">
                <label for="instructions" class="mb-0 pt-1" style="white-space: nowrap;">
                    <p>Account 2 Type:</p>
                </label>
                <div class="d-flex align-items-center ps-3" style="gap: 20px;">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="accountType2" id="checking"
                            value="checking" style="transform: scale(1.5);"
                            <?= (!empty($quick_book['account_2_type']) && $quick_book['account_2_type'] === 'checking') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="checking">Checking</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="accountType2" id="savings"
                            value="savings" style="transform: scale(1.5);" <?= (!empty($quick_book['account_2_type']) && $quick_book['account_2_type'] === 'savings') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="savings">Savings</label>
                    </div>
                </div>
            </div> -->

            <!-- <div class="form-group d-flex align-items-center" style="margin-top: -25px;">
                <label for="instructions" class="mb-0 pt-3" style="white-space: nowrap;">
                    <p> Bank routing number (ABA number): </p>
                </label>
                <input name="aba_number_2" class="form-control flex-grow-1" type="text"
                    value="<?= !empty($quick_book['aba_number_2']) ? $quick_book['aba_number_2'] : '' ?>">
            </div> -->

            <!-- <div class="form-group d-flex align-items-center" style="margin-top: -25px;">
                <label for="instructions" class="mb-0 pt-3" style="white-space: nowrap;">
                    <p> Account Number: </p>
                </label>
                <input name="account_number_2" class="form-control flex-grow-1" type="text"
                    value="<?= !empty($quick_book['account_number_2']) ? $quick_book['account_number_2'] : '' ?>">
            </div> -->
        </div>

        <div class="row" style="margin: 1px;">
            <div class="form-group d-flex align-items-center centered-box">
                attach a voided check for each account here
            </div>
        </div>

        <div class="row mt-2">
            <div class="form-group row">
                <div class="col-12 col-md-auto d-flex align-items-center mb-md-0">
                    <label for="authorization" class="mb-0 p-0">
                        <strong>Authorization</strong> (enter your company name in the blank space below)
                    </label>
                </div>
                <div class="col-12 col-md p-0">
                    <input name="authorization" class="form-control" type="text"
                        value="<?= !empty($quick_book['authorization']) ? $quick_book['authorization'] : '' ?>">
                </div>
            </div>
            <div class="form-group d-flex align-items-center">
                <label for="instructions" class="mb-0 pt-3" style="white-space: nowrap;">
                    <p> This authorizes </p>
                </label>
                <input name="authorizes_company" class="form-control flex-grow-1" type="text"
                    value="<?= !empty($quick_book['authorizes_company']) ? $quick_book['authorizes_company'] : '' ?>">
                <label for="instructions" class="mb-0 pt-3" style="white-space: nowrap;">
                    <p> (the "Company") </p>
                </label>
            </div>
            <div class="d-flex pb-0 mb-0" style="line-height:1;margin-top: -10px; text-align:justify">
                <p>to send credit entries (and appropriate debit and adjustment entries), electronically or by any
                    other
                    commercially accepted method, to my (our) account(s) indicated below and to other accounts I
                    (we) identify in
                    the future (the "Account"). This authorizes the financial institution holding the Account to
                    post all such entries. I
                    agree that the ACH transactions authorized herein shall comply with all applicable U.S. Law.
                    This authorization
                    will be in effect until the Company receives a written termination notice from myself and has a
                    reasonable opportunity to act on it. </p>
            </div>
        </div>

        <div class="row">
        <div class="form-group d-flex align-items-center" style="margin-top: -20px; gap: 10px;">
<label for="instructions" class="mb-0 pt-3" style="white-space: nowrap; min-width: 120px;">
<p>Authorized signature:</p>
</label>
<div class="d-flex align-items-center justify-content-center" 
 style="flex: 1; height: 38px; padding: 0 12px; border: 1px solid #ced4da; border-radius: 0.25rem; background-color: white; margin-right: 10px;">
<?php if (!empty($signature['signature']) && strpos($signature['signature'], 'data:image/png') === 0): ?>
    <img src="<?php echo htmlspecialchars($signature['signature']); ?>" 
         alt="Authorized Signature" 
         style="max-height: 30px; max-width: 100%; object-fit: contain;">
<?php else: ?>
    <span style="color: #6c757d; font-style: italic;">✓</span>
<?php endif; ?>
</div>
<label for="signature" class="mb-0 pt-3" style="white-space: nowrap; min-width: 90px;">
<p>Employee ID#:</p>
</label>
<input name="employee_id" class="form-control" type="number"
value="<?= !empty($quick_book['employee_id']) ? $quick_book['employee_id'] : '' ?>"
style="flex: 1;">
</div>
            <div class="form-group d-flex align-items-center" style="margin-top: -20px;">
                <label for="signature" class="mb-0 pt-3" style="white-space: nowrap;">
                    <p> Print name: </p>
                </label>
                <input name="print_name" class="form-control flex-grow-1" type="text"
                    value="<?= !empty($quick_book['print_name']) ? $quick_book['print_name'] : '' ?>">
                <label for="print_name" class="mb-0 pt-3" style="white-space: nowrap;">
                    <p> Date: </p>
                </label>
                <input 
        type="text" 
        class="form-control" 
        name="date" 
        id="date"
        placeholder="MM/DD/YYYY"
        value="<?= !empty($craft_data['created_at']) ? date('m/d/Y', strtotime($craft_data['created_at'])) : '' ?>"
    >
            </div>
        </div>

    </form>
</div>
<?php endif; ?>

    <?php if (!empty($non_compete)): ?>
            <div class="container my-5" id="form-content-5">
                <form style="page-break-inside: avoid;" method="post" id="non_compete_agreements">
                    <!-- Header Section -->
                    <div class="d-flex justify-content-between"
                        style="border-bottom: 2px solid black; margin-bottom: 15px;">
                        <div>
                            <h2 style="margin: 0;">NON-COMPETE AGREEMENT</h2>
                        </div>
                    </div>

                    <!-- Parties Section -->
                    <div style="margin-bottom: 15px;">
                        <p><strong>This Non-Compete Agreement ("Agreement")</strong> is entered into on this _____ day of
                            ___________, 20, by and between:</p>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Employer:</strong> Craft Contracting, LLC<br>
                                    <strong>Address:</strong>
                                    <?= !empty($non_compete['company_address']) ? $non_compete['company_address'] : '[Insert Company Address]' ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Employee:</strong>
                                    <?= !empty($craft_data['first_name']) || !empty($craft_data['last_name'])
                                        ? htmlspecialchars(trim($craft_data['first_name'] . ' ' . $craft_data['last_name']))
                                        : '________________________' ?>
                                    <strong>Address:</strong>
                                    <?= htmlspecialchars(isset($craft_data['street_address']) ? $craft_data['street_address'] : '________________________') ?>          
                  </p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 1: Purpose -->
                    <div style="margin-bottom: 15px;">
                        <h4 style="border-bottom: 1px solid #000; padding-bottom: 3px;">1. Purpose</h4>
                        <p>The purpose of this Agreement is to protect the legitimate business interests of the Employer,
                            including its confidential methods, trade secrets, customer relationships, and specialized
                            experience
                            related to the installation, dismantling, or servicing of <strong>Sprung structures</strong>.
                        </p>
                    </div>

                    <!-- Section 2: Non-Compete Obligation -->
                    <div style="margin-bottom: 15px;">
                        <h4 style="border-bottom: 1px solid #000; padding-bottom: 3px;">2. Non-Compete Obligation</h4>
                        <p>For a period of <strong>two (3) years</strong> following the termination of Employee’s employment
                            with
                            Employer, whether voluntary or involuntary, the Employee shall not, directly or indirectly:
                        </p>
                        <ul>
                            <li>Perform any work, services, or provide labor (as an employee, independent contractor,
                                consultant, or otherwise) for any entity or individual involved in the <strong>installation,
                                    dismantling, service, or construction of Sprung brand structures</strong>, within the
                                <strong>state of Ohio</strong> or any state in which Craft Contracting, LLC has an active
                                project at the time of termination.
                            </li>
                        </ul>
                    </div>

                    <!-- Section 3: Confidentiality -->
                    <div style="margin-bottom: 15px;">
                        <h4 style="border-bottom: 1px solid #000; padding-bottom: 3px;">3. Confidentiality</h4>
                        <p>Employee agrees not to disclose or use any confidential or proprietary information obtained
                            during their employment, including but not limited to project procedures, pricing, client lists,
                            and material specifications, especially as it relates to Sprung structures.</p>
                    </div>

                    <!-- Section 4: Acknowledgment -->
                    <div style="margin-bottom: 15px;">
                        <h4 style="border-bottom: 1px solid #000; padding-bottom: 3px;">4. Acknowledgment</h4>
                        <p>Employee acknowledges that this restriction is reasonable in scope and necessary to protect the
                            Employer's interests, and that employment with Employer constitutes sufficient consideration for
                            entering this Agreement.</p>
                    </div>

                    <!-- Section 5: Governing Law -->
                    <div style="margin-bottom: 15px;">
                        <h4 style="border-bottom: 1px solid #000; padding-bottom: 3px;">5. Governing Law</h4>
                        <p>This Agreement shall be governed by and construed in accordance with the laws of the
                            <strong>State of Ohio</strong>.
                        </p>
                    </div>

                    <!-- Section 6: Enforcement -->
                    <div style="margin-bottom: 20px;">
                        <h4 style="border-bottom: 1px solid #000; padding-bottom: 3px;">6. Enforcement</h4>
                        <p>If any provision is deemed unenforceable, the remaining provisions shall remain in effect.
                            Employer shall be entitled to injunctive relief, in addition to any other legal remedies, in the
                            event of a breach.</p>
                    </div>

                    <!-- Signature Section -->
                    <div style="margin-top: 30px;">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>EMPLOYEE:</strong></p>
                                <div style="border-top: 1px solid #000; width: 80%; margin-bottom: 5px;"></div>
                                <p>Signature:
                                    <?= !empty($non_compete['signature']) ? '<img src="' . $non_compete['signature'] . '" alt="Signature" style="max-height: 50px;">' : '________________________' ?>
                                </p>
                                <div style="border-top: 1px solid #000; width: 80%; margin-bottom: 5px;"></div>

                                <div style="border-top: 1px solid #000; width: 80%; margin-bottom: 5px;"></div>
                                <p>Print Name:
                                    <?= !empty($non_compete['employee_name']) ? $non_compete['employee_name'] : '________________________' ?>
                                </p>
                                <div style="border-top: 1px solid #000; width: 80%; margin-bottom: 5px;"></div>
                                <p>Date:
                                    <?= !empty($non_compete['signature_date']) ? $non_compete['signature_date'] : '________________________' ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>EMPLOYER (Craft Contracting, LLC):</strong></p>
                                <div style="border-top: 1px solid #000; width: 80%; margin-bottom: 5px;"></div>
                                <p>Authorized Signature: ________________________</p>
                                <div style="border-top: 1px solid #000; width: 80%; margin-bottom: 5px;"></div>
                                <p>Name & Title: ________________________</p>
                                <div style="border-top: 1px solid #000; width: 80%; margin-bottom: 5px;"></div>
                                <p>Date: ________________________</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif; ?>
       


    <?php if (!empty($w4)): ?>




        <div class="container my-5" id="form-content-6">
            <!-- Top Section with Logo and Address -->
            <form style="page-break-inside: avoid;" method="post" id="w4_form">
                <div class="d-flex flex-column flex-md-row justify-content-between" style="margin: 0; padding: 0;">
                    <div class="d-md-flex mr-auto p-1 bd-highlight text-center text-md-left"
                        style="border-bottom: 2px solid black; margin: 0; padding: 0;">
                        <div style="margin: 0; padding: 0;">
                            Form <strong style="font-size: 30px;">W-4</strong>
                            <p style="font-size: 9px; margin: 0;">Department of Treasury</p>
                            <p style="font-size: 9px; margin: 0;">Internal revenue service</p>
                        </div>
                    </div>
                    <div class="bd-highlight text-center p-2 my-2 my-md-0"
                        style="border-bottom: 2px solid black; border-left: 2px solid black; border-right: 2px solid black; margin: 0; padding: 0;">
                        <strong class="m-0" style="font-size: 20px; color: black; display: block;">Employee's Withholding
                            Certificate</strong>
                        <strong style="font-size: 11px; color: black; display: block; line-height:1;">Complete Form W-4 so
                            that your employer can withhold the correct federal income tax from your pay.</strong>
                        <strong style="font-size: 11px; color: black; display: block; line-height:1;">Give Form W-4 to your
                            employer.</strong>
                        <strong style="font-size: 11px; color: black; display: block; line-height:1;">Your withholding is
                            subject to review by the IRS.</strong>
                    </div>
                    <div class="bd-highlight p-1 text-center text-md-right"
                        style="border-bottom: 2px solid black; margin: 0; padding: 0;">
                        <p style="font-size: 10px; margin: 0; border-bottom: 1px solid black;">OMB No. 1545-0074</p>
                        <b style="font-size: 37px;">2023</b>
                    </div>
                </div>


                <!-- Top Section with Logo and Address -->
                <div class="d-flex flex-column flex-md-row justify-content-between" style="line-height:1;">
                    <div class="d-md-flex mr-auto p-1 bd-highlight text-md-left" style="border-bottom: 1px solid black;">
                        <div>
                            <strong style="font-size: 15px;">Step 1: Enter</strong><br>
                            <strong style="font-size: 15px;">Personal</strong><br>
                            <strong style="font-size: 15px;">Information</strong><br>
                        </div>
                    </div>
                    <div class="bd-highlight my-2 my-md-0 w-100" style="border-left: 1px solid black;border-right: 1px solid black;">
    <div class="row">
        <div class="col-6 pe-0" style="border-right: 1px solid black;">
            <strong style="display: inline;vertical-align: top;padding-left:6px">(a)</strong>
            <label style="font-size:11px;padding-left: 3px; display: inline-block" for="checking">First Name and Middle initial</label>
            <input type="text" class="form-control" name="first_name" 
                   value="<?= !empty($w4['first_name']) ? $w4['first_name'] : (!empty($craft_data['first_name']) ? $craft_data['first_name'] : '') ?>">
        </div>
        <div class="col-6 ps-0">
            <label style="font-size:11px;padding-left: 3px;" for="savings">Last Name</label>
            <input type="text" class="form-control" name="last_name" 
                   value="<?= !empty($w4['last_name']) ? $w4['last_name'] : (!empty($craft_data['last_name']) ? $craft_data['last_name'] : '') ?>">
        </div>
    </div>
    <div class="">
        <label style="font-size:11px;padding-left: 3px;" for="checking">Address</label>
        <input type="text" class="form-control" name="address"
               value="<?= !empty($w4['address']) ? $w4['address'] : (!empty($craft_data['street_address']) ? $craft_data['street_address'] : '') ?>">
    </div>
    <div class="">
    <label style="font-size:11px;padding-left: 3px;" for="checking">City or Town, State and Zip Code</label>
    <input type="text" class="form-control" name="city_state_zip"
           value="<?= !empty($w4['city_state_zip']) 
                   ? $w4['city_state_zip'] 
                   : (!empty($craft_data['city']) || !empty($craft_data['state']) || !empty($craft_data['zip_code'])
                      ? trim(
                          (!empty($craft_data['city']) ? $craft_data['city'] . ', ' : '') .
                          (!empty($craft_data['state']) ? $craft_data['state'] . ' ' : '') .
                          (!empty($craft_data['zip_code']) ? $craft_data['zip_code'] : '')
                        )
                      : '')
                   ?>">
</div>

<div class="ps-2" style="border-bottom: 1px solid black;line-height:1;">
    <strong style="display: inline;vertical-align: top;">(c)</strong>
    <div class="ps-2" style="display: inline-block; margin-left: 5px;">
        <div class="">
            <input class="form-group" type="radio" name="marital_status" value="Single or Married filing separately"
                <?= (!empty($verification['marital_status']) && $verification['marital_status'] == 'Single or Married filing separately' ? 'checked' : '' ) ?>>
            <label class="form-check-label" style="font-size:14px;">Single or Married filing separately</label>
        </div>
        <div class="">
            <input class="form-group" type="radio" name="marital_status" value="Married filing jointly or Qualifying surviving spouse"
                <?= (!empty($verification['marital_status']) && $verification['marital_status'] == 'Married filing jointly or Qualifying surviving spouse' ? 'checked' : '') ?>>
            <label class="form-check-label" style="font-size:14px;">Married filing jointly or Qualifying surviving spouse</label>
        </div>
        <div class="">
            <input class="form-group" type="radio" name="marital_status" value="Head of Household"
                <?= (!empty($verification['marital_status']) && $verification['marital_status'] == 'Head of Household' ? 'checked' : '') ?>>
            <label class="form-check-label" style="font-size:14px;">Head of Household</label>
        </div>
    </div>
</div>
</div>
                    <div class="bd-highlight p-1 text-md-right"
                        style="border-bottom: 1px solid black; max-width: 140px; margin: 0 auto;">
                        <div style="margin-bottom: 5px; border-bottom: 1px solid black;">
                            <b style="font-size:10px; padding-left: 3px;">(b) Social Security Number</b>
                            <input type="text" class="form-control" name="ssn"
                                value="<?= !empty($verification['ssn']) ? $verification['ssn'] : '' ?>">
                        </div>
                        <div style="font-size: 10px; line-height:1;">
                            <b>Does your name match the name on your social security card?</b>
                            <p>If not, to ensure you get credit for your earnings, contact SSA at 800-772-1213 or visit
                                www.ssa.gov</p>
                        </div>
                    </div>
                </div>

                <div style="border-bottom: 1px solid black; margin: 2px;line-height:1;">
                    <strong style="display: inline;">Complete Steps 2-4 Only if they apply to you; otherwise skip to the
                        step 5</strong>
                    <p style="display: inline; font-size: 14px; margin: 0;"> See page 2 for more information on each step,
                        who can claim exemption from withholding, other details, and privacy.</p>
                </div>


                <div class="d-flex flex-column flex-md-row justify-content-between" style="line-height:1;">
                    <!-- Left Section -->
                    <div class="p-1 bd-highlight text-md-left" style="min-width: 115px;">
                        <strong style="font-size: 15px;">Step 2: Multiple Jobs Or Spouse Works</strong>
                    </div>

                    <!-- Right Section -->
                    <div class="bd-highlight flex-grow-1" style="padding-left: 10px;">
                        <div class="ps-3 pt-2">
                            <p style="font-size:14px;margin-bottom: 0;">Complete this step if you(1) hold more than one job
                                at a time, or (2) married filing jointly and your spouse
                                also works. The correct amount of withholding dependson income earned from all of these
                                jobs.
                            </p>
                        </div>
                        <div class="ps-3 pt-1">
                            <p style="font-size:14px;margin-bottom: 0;">Do only one of the following</p>
                        </div>
                        <div class="ps-3 pt-1">
                            <strong style="display: inline;">(a)</strong>
                            <p style="display: inline; font-size: 14px; margin: 0;"> Reserved for future</p><br>
                            <strong style="display: inline;">(b)</strong>
                            <p style="display: inline; font-size: 14px; margin: 0;"> Use the multiple jobs worksheet on Page
                                3 and enter result in step 4(c) below; or</p><br>
                            <strong style="display: inline;">(c)</strong>
                            <p style="display: inline; font-size: 14px; margin: 0;"> If there are only two job in total, you
                                may check this box. Do the same on W-4 form fot other job.
                                this option is more accurate than (b) if pay at the lower paying job is more than half of
                                the that pay at the higher paying job.
                                Otherwise (b) is more accurate.
                            </p><br>
                            <strong style="display: inline;">TIP:</strong>
                            <p style="display: inline; font-size: 14px; margin: 0;">
                                If you have self-employeed income see page 2.
                            </p>
                        </div>

                    </div>
                </div>

                <div style="border-bottom: 2px solid black; margin: 2px; line-height:1;">
                    <strong style="display: inline;">Complete Steps 3-4 on form W-4 for ONLY ONE of these jobs.</strong>
                    <p style="display: inline; font-size: 14px; margin: 0;">
                        Leave those steps blanks for the other jobs. (Your withholding will be most accurate if you complete
                        steps 3-4(b) on the form W-4 for the highest paying job).
                    </p>
                </div>


                <div class="d-flex flex-column flex-md-row justify-content-between"
                    style="border-bottom: 2px solid black;line-height:1;">
                    <!-- Left Section -->
                    <div class="p-1 bd-highlight text-md-left" style="min-width: 115px;max-width: 115px;">
                        <strong style="font-size: 15px;">Step 3: Claim Dependent and Other Credits</strong><br>
                    </div>

                    <!-- Right Section -->
                    <div class="bd-highlight flex-grow-1" style="padding-left: 10px;">
                        <div class="ps-3 pt-1">
                            <p style="font-size:14px;margin-bottom: 0;">
                                If your total income will be $200,000 or less than ($400,000 or less if married filing
                                jointly):
                            </p>
                        </div>
                        <div class="ps-5">
                            <p style="display: inline; font-size: 14px; margin: 0;">
                                Multiply the number of qualifying children under age 17 by $2,000
                            </p>
                            <input type="number" style="display: inline; width: 30%;" class="form-control"
                                name="qualifying_children" placeholder="$"
                                value="<?= !empty($w4['qualifying_children']) ? $w4['qualifying_children'] : '' ?>"><br>
                            <p style="display: inline; font-size: 14px; margin: 0;">
                                Multiply the number of other dependents by $500 . .
                            </p>
                            <input type="number" style="display: inline; width: 30%;" class="form-control"
                                name="number_of_other_dependents"
                                value="<?= !empty($w4['number_of_other_dependents']) ? $w4['number_of_other_dependents'] : '' ?>"
                                placeholder="$">
                        </div>
                        <div class="ps-3 pb-1">
                            <p style="display: inline; font-size: 14px; margin: 0;">
                                Add the amounts above for qualifying children and other dependents.
                                You may add to this amount of any other credits. Enter the total here
                                . . . . . . . . . . . . . . . . . . . . . . .
                            </p>
                            <input type="number" style="display: inline;width:30%;" class="form-control"
                                name="amount_for_qualifying_children"
                                value="<?= !empty($w4['amount_for_qualifying_children']) ? $w4['amount_for_qualifying_children'] : '' ?>"
                                placeholder="$">
                        </div>
                    </div>
                </div>




                <div class="d-flex flex-column flex-md-row justify-content-between"
                    style="border-bottom: 2px solid black;line-height:1;">
                    <!-- Left Section -->
                    <div class="p-1 bd-highlight text-md-left" style="min-width: 115px;">
                        <strong style="font-size: 15px;">Step 4: (Optional) Other Adjustments</strong><br>
                    </div>

                    <!-- Right Section -->
                    <div class="bd-highlight flex-grow-1" style="padding-left: 10px;">
                        <div class="ps-3 pt-2 pb-1">
                            <strong style="display: inline;">(a) Other income (not from jobs).</strong>
                            <p style="display: inline; font-size: 14px; margin: 0;">
                                If you want tax withheld for other income you expect this year that would't have
                                withholding,
                                enter the emount of other income here. This may include interest, dividends and retirement
                                income. . . . .
                            </p>
                            <input type="number" style="display: inline;width:auto" class="form-control" name="tax_withheld"
                                value="<?= !empty($w4['tax_withheld']) ? $w4['tax_withheld'] : '' ?>" placeholder="$">
                        </div>

                        <div class="ps-3 pb-1">
                            <strong style="display: inline;">(b) Deductions </strong>
                            <p style="display: inline; font-size: 14px; margin: 0;">
                                If you expect to claim deductions other than the standard deduction and want to reduce your
                                withholding,
                                use the deduction worksheet on page 3 and enter then result here . . . . . . . . . . . . . .
                                . . . . . . .
                            </p>
                            <input type="number" style="display: inline;width:auto" class="form-control"
                                name="claim_deductions"
                                value="<?= !empty($w4['claim_deductions']) ? $w4['claim_deductions'] : '' ?>"
                                placeholder="$">
                        </div>

                        <div class="ps-3 pb-1">
                            <strong style="display: inline;">(c) Extra Withholding</strong>
                            <p style="display: inline; font-size: 14px; margin: 0;">
                                Enter any aditional tax you want held each pay period
                            </p>
                            <input type="number" style="display: inline;width:17.5%" class="form-control"
                                name="extra_withholding"
                                value="<?= !empty($w4['extra_withholding']) ? $w4['extra_withholding'] : '' ?>"
                                placeholder="$">
                        </div>

                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-between"
                    style="border-bottom: 2px solid black;line-height:1;">
                    <!-- Left Section -->
                    <div class="p-1 bd-highlight text-md-left" style="min-width: 115px;">
                        <strong style="font-size: 15px;">Step 5: Sign here</strong><br>
                    </div>

                    <!-- Right Section -->
                    <div class="bd-highlight flex-grow-1" style="padding-left: 10px; border-left: 1px solid black;">
                        <div class="row">
                            <p style="display: inline; font-size: 14px; margin: 0;">
                                Under penalities of perjury, i declare that thi Certificate, to the est of my knowledge and
                                belief
                                is truen correct and complete.
                            </p>
                        </div>

                        <div class="row">
                        <div class="col-md-9">
    <div class="form-control" style="height: 38px; padding: 6px 12px; display: flex; align-items: center;">
        <?php if (!empty($signature['signature']) && strpos($signature['signature'], 'data:image/png') === 0): ?>
            <img src="<?php echo htmlspecialchars($signature['signature']); ?>" 
                 alt="Employee's Signature" 
                 style="max-height: 30px; max-width: 100%; object-fit: contain;">
        <?php else: ?>
            <span style="color: #6c757d; font-style: italic;">No signature</span>
        <?php endif; ?>
    </div>
    <strong style="display: inline;">Employee's Signature </strong>
    <p style="display: inline; font-size: 14px; margin: 0;">
        (This form is not valid unless you sign it)
    </p>
</div>
                            <div class="col-md-date" class="form-control" name="date" placeholder="">
                                <strong style="display: inline;">Date:</strong>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="d-flex flex-column flex-md-row justify-content-between"
                    style="border-bottom: 2px solid black;line-height:1;">
                    <!-- Left Section -->
                    <div class="p-1 bd-highlight text-md-left" style="min-width: 115px;">
                        <strong style="font-size: 15px;">Employers Only</strong><br>
                    </div>

                    <!-- Right Section -->
                    <div class="bd-highlight flex-grow-1" style="padding-left: 10px;border-left: 1px solid black;">
                        <div class="row">
                            <div class="col-md-6" style="border-right: 1px solid black;">
                                <p style="display: inline; font-size: 14px; margin: 0;">
                                    Employers name and address
                                </p>
                                <input type="text" class="form-control pt-3" name="employers_name"
                                    value="<?= !empty($w4['employers_name']) ? $w4['employers_name'] : '' ?>"
                                    style="border:0">
                            </div>
                            <div class="col-md-3" style="border-right: 1px solid black;">
                                <p style="display: inline; font-size: 14px; margin: 0;">
                                    First Date of Employement
                                </p>
                                <input type="date" class="form-control" name="date_of_employement"
                                    value="<?= !empty($w4['date_of_employement']) ? $w4['date_of_employement'] : '' ?>"
                                    style="border:0">
                            </div>
                            <div class="col-md-3">
                                <p style="display: inline; font-size: 14px; margin: 0;">
                                    Employe Identification Number (EIN)
                                </p>
                                <input type="number" class="form-control" name="ein"
                                    value="<?= !empty($w4['ein']) ? $w4['ein'] : '' ?>" style="border:0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column flex-md-row justify-content-between" style="margin: 0; padding: 0;">
                    <div class="d-md-flex mr-auto p-1 bd-highlight text-center text-md-left">
                        <strong style="font-size: 13px;">For Privacy Act and Paperwork Reduction Act Notice, See Page
                            3</strong>
                    </div>
                    <div class="bd-highlight text-center my-md-0 p-1" style="margin: 0; padding: 0;">
                        <p style="display: inline; font-size: 12px; margin: 0;">
                            CatNo. 102Q
                        </p>
                    </div>
                    <div class="bd-highlight p-1 text-center text-md-right">
                        Form <strong style="font-size: 13px;">W-4</strong> (2023)
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
    <!-- <div class="my-5 text-center">

        <button type="submit" class="btn btn-dark w-50 mt-3 no-print mb-5 " id="submitBtn" onclick="printForm()" style="
    margin: auto;
">Download</button>
    </div> -->


  <script>
        function printForm() {
            document.getElementById('submitBtn').remove();

            // Show SweetAlert2 loading popup
            Swal.fire({
                title: 'Please wait...',
                text: 'Generating and downloading your file',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // IDs of all form content sections you want to print
            const ids = ["form-content", "form-content-1", "form-content-2", "form-content-3", "form-content-4", "form-content-5", "form-content-6"];
            const elements = ids.map(id => document.getElementById(id));

            // Create a wrapper container for all the content
            const printContainer = document.createElement("div");

            elements.forEach((el, index) => {
                if (el) {
                    const cloned = el.cloneNode(true);

                    // Add a page break after each section (except last)
                    if (index < elements.length - 1) {
                        cloned.style.pageBreakAfter = "always";
                    }

                    // Optionally remove 'my-5' margin class
                    cloned.classList.remove('my-5');

                    printContainer.appendChild(cloned);
                }
            });

            // Configure html2pdf options
            const options = {
                filename: 'Data_packet#' + <?= $applicant_id ?> + '.pdf',
                image: { type: 'jpeg', quality: 1.0 },
                html2canvas: {
                    scale: 4,
                    useCORS: true,
                    allowTaint: true,
                },
                jsPDF: {
                    unit: 'pt',
                    format: 'a4',
                    orientation: 'portrait'
                },
                pagebreak: { mode: ['css', 'legacy'] }
            };

            // Generate PDF
            html2pdf().set(options).from(printContainer).save().then(() => {
                // Close the SweetAlert popup once download is done
                Swal.close();
            });
        }
    </script>



