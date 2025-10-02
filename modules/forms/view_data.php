<?php
// Include the necessary files for database connection and functions
// Fetch employee details from the database
    // Fetch employee general information
    // $employee = DB::queryFirstRow("SELECT * FROM craft_contracting WHERE id = %i", $employeeId);
    // if (!$employee) {
    //     die("Employee not found.");
    
    //}
    
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


    // Fetch additional information from other tables
$contracting = DB::queryFirstRow("SELECT * FROM craft_contracting WHERE id = %i", $applicant_id);
$language = DB::queryFirstRow("SELECT language FROM employee_lang WHERE id = %i", $applicant_id);
$w4_form = DB::queryFirstRow("SELECT * FROM w4_form WHERE id = %i", $applicant_id);
$quick_book = DB::queryFirstRow("SELECT * FROM quick_book WHERE id = %i", $applicant_id);
$mvr = DB::queryFirstRow("SELECT * FROM  mvr_released WHERE id = %i", $applicant_id);
$eligibility = DB::queryFirstRow("SELECT * FROM employment_eligibility_verification WHERE id = %i", $applicant_id);
$employment_data = DB::queryFirstRow("SELECT * FROM employment_data WHERE id = %i", $applicant_id);
$eligibility1 = DB::queryFirstRow("SELECT * FROM employment_eligibility_verification1 WHERE id = %i", $applicant_id);


?>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f5f5f5;
    }
.hhh{
    height: 60px;
}
    .main-container {
        max-width: 100%;
        margin: 20px auto;
        background: #fff;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
    }


    /* Single box for one field (like SSN) */
    .single-box {
        flex: 1;
        border: 1px solid #000;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: left;
        padding: 20px;

    }

    /* Label styling */
    .label {
        font-weight: bold;
        color: black;
    }

    /* Standard field container (for other sections) */
    .field-box {
        border: none;
        padding: 8px;
        min-height: 40px;
        margin-bottom: 10px;
        box-sizing: border-box;
        padding: 20px;
        color: rgb(0, 0, 0);
        font-size: 16px;

    }

    /* Availability table borders */
    .availability-table,
    .availability-table th,
    .availability-table td {
        border: 1.5px solid #000;
        text-align: center;
        border-collapse: collapse;
    }

    .availability-table td {
        color: black;
    }

    .availability-table {
        width: 100%;
    }

    .availability-table th {
        color: rgb(0, 0, 0);
        font-size: 16px
    }

    /* Basic row layout if not using Bootstrap */
    .row:after {
        content: "";
        display: table;
        clear: both;
    }

    [class*="col-"] {
        float: left;
        padding: 0 10px;
        box-sizing: border-box;
    }

    .col-md-4 {
        width: 33.3333%;
    }

    .col-md-6 {
        width: 50%;
    }

    .col-md-2 {
        width: 16.6667%;
    }

    .col-md-8 {
        width: 66.6667%;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {

        /* Make all grid columns full-width */
        [class*="col-"] {
            width: 100% !important;
            float: none;
            padding: 5px !important;
        }

        /* Convert flex groups into column layout */
        .group-container {
            flex-direction: column;
            gap: 5px;
        }

        /* Adjust box paddings and font sizes for a tighter mobile layout */
        .single-box,
        .field-box,
        .group-box td {
            padding: 5px !important;
            font-size: 14px;
        }

        /* Section titles: slightly smaller with less padding */
        .section-title {
            font-size: 16px;
            padding: 8px;
        }

        /* Availability table adjustments */
        .availability-table,
        .availability-table th,
        .availability-table td {
            font-size: 12px;
            padding: 6px;
        }
    }

    td {
        font-size: 16px;
        color: black;
    }

    /* === MOBILE-FIRST RESPONSIVENESS (ENHANCED) === */
    @media (max-width: 768px) {

        /* Full-width layout for all columns */
        [class*="col-"] {
            width: 100% !important;
            float: none !important;
            padding: 8px !important;
        }

        /* Page Header: Stack vertically */
        .page-header {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 10px;
            text-align: left;
        }

        .page-header .page-title {
            font-size: 20px;
        }

        .breadcrumb {
            padding-left: 0;
            margin-top: 5px;
        }

        /* Button dropdown styling */
        .btn,
        .dropdown-toggle {
            width: 100%;
            font-size: 14px;
            padding: 10px 14px;
            margin-top: 10px;
            text-align: center;
        }

        .dropdown-menu {
            width: 100%;
        }

        /* Reduce label and box sizes for small screens */
        .label {
            font-size: 14px;
            display: block;
            margin-bottom: 3px;
        }

        .field-box,
        .single-box {
            font-size: 18px;
            padding: 6px;
        }

        /* Stack grouped rows properly */
        .group-container {
            flex-direction: column !important;
            gap: 5px;
        }

        .group-box td {
            display: block;
            width: 100%;
            border-right: none !important;
            border-bottom: 1px solid #ccc;
        }

        /* Fix table overflow on smaller screens */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .availability-table {
            font-size: 13px;
            min-width: 600px;
        }

        /* Utility spacing tweaks */
        .single-box,
        .field-box {
            margin-bottom: 12px;
        }

        /* Responsive tweaks for signature/date row */
        .row:last-child .col-md-6 {
            width: 100% !important;
            margin-bottom: 12px;
        }
    }
    @media print {
  .skip-print {
    display: none !important;
  }

  body * {
    visibility: hidden;
  }

  #printable-form,
  #printable-form * {
    visibility: visible;
  }

  #printable-form {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    padding: 0.5cm;
    
    /* Scaling to fit */
    zoom: 0.85;
    transform: scale(0.85);
    transform-origin: top left;

    page-break-inside: avoid;
    page-break-after: avoid;
    page-break-before: avoid;
  }

  @page {
    size: A4 portrait;
    margin: 0.5cm;
  }

  #printable-form * {
    font-size: 0.95em !important;
    line-height: 1.1em !important;
    margin: 0 !important;
    padding: 0.1em 0 !important;
  }
}

</style>
<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Content Header (Page header) -->
<div class="side-app">
    <!-- Page header with breadcrumb navigation -->
    <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
        <div style="margin-top: 25px;">
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
    
    <!-- CONTAINER -->
    <div id="printable-form"><div class="main-container container-fluid " style="margin-top: 1%;">
        <!-- PAGE-HEADER -->
        <div class="page-header d-flex align-items-center justify-content-between mt-2">
            <h1 class="page-title"> <?php echo lang("formview_detailed_data"); ?></h1>
        </div>

        <div class="d-flex justify-content-end align-items-center skip-print mb-2">

        <button id="printBtn" class="btn no-print ms-1 me-1" style="background-color: #fe5500; color: white; min-width: 120px;">
    <?php echo lang('print_form'); ?>
</button>
</div>
<!-- PAGE-HEADER END -->

        <!-- PERSONAL INFORMATION SECTION -->

        <!-- Group for Last, First, Middle Initial -->
        <div class="row">
            <div class="col-md-6 single-box hhh">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 33.33%;"><span class="label"><?php echo lang("form_last_name"); ?></span>
                            <?php
                            if (isset($applicant['last_name'])) {
                                echo htmlspecialchars($applicant['last_name']);
                            } else {
                                echo 'NA';
                            }
                            ?>
                        <td style="width: 33.33%;"><span class="label"><?php echo lang("form_first_name"); ?></span>
                            <?php
                            if (isset($applicant['first_name'])) {
                                echo htmlspecialchars($applicant['first_name']);
                            } else {
                                echo 'NA';
                            }
                            ?>
                        <td style="width: 33.33%;"><span class="label"><?php echo lang("form_middle_initial"); ?></span>
                            <?php
                            if (isset($applicant['middle_initial'])) {
                                echo htmlspecialchars($applicant['middle_initial']);
                            } else {
                                echo 'NA';
                            }
                            ?>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Next row: Street Address and City/State, Zip -->
        <div class="row">
            <div class="col-md-6 single-box">
                <table style="width: 100%">
                    <tr>
                        <td style="width: 33.33%;"><span class="label"><?php echo lang("form_street_address"); ?></span>
                            <?php
                            if (isset($applicant['street_address'])) {
                                echo htmlspecialchars($applicant['street_address']);
                            } else {
                                echo 'NA';
                            }
                            ?>
                        </td>
                        <td style="width: 33.33%;"><span class="label"><?php echo lang("form_city_name"); ?></span>
                            <?php
                            if (isset($applicant['city'])) {
                                echo htmlspecialchars($applicant['city']);
                            } else {
                                echo 'NA';
                            }
                            ?>
                        </td>
                        <td style="width: 33.33%;"><span class="label"><?php echo lang("form_zip"); ?></span>
                            <?php if (isset($applicant['zip'])) {
                                echo htmlspecialchars($applicant['zip']);
                            } else {
                                echo 'NA';
                            } ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4 single-box">
                <p><span class="label"> <?php echo lang("formview_phone"); ?></span></p>
                <div class="field-box"><?php if (isset($applicant['phone_number'])) {
                    echo htmlspecialchars($applicant['phone_number']);
                } else {
                    echo '(555) 555-5555';
                } ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 single-box">
                <p><span class="label"><?php echo lang("formview_email") ?></span></p>
                <div class="field-box"><?php if (isset($applicant['email'])) {
                    echo htmlspecialchars($applicant['email']);
                } else {
                    echo 'Email@gmail.com';
                } ?></div>
            </div>
            
        </div>

        <!-- JOB DETAILS SECTION -->
        <div class="row">
        <div class="col-md-6 single-box">
                <p><span class="label"><?php echo lang("formview_position") ?></span></p>
                <div class="field-box">
                    <?php
                    if (isset($applicant['position_names']) && !empty($applicant['position_names'])) {
                        echo htmlspecialchars($applicant['position_names']);
                    } else {
                        // Debug output
                        $rawPositions = DB::queryFirstField(
                            "SELECT position FROM applicants WHERE id = %i",
                            $applicant_id
                        );
                        echo "No positions found ";
                    }
                    ?>
                </div>                </div>

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_salary") ?></span></p>
                    <div class="field-box"><?php if (isset($applicant['salary'])) {
                        echo htmlspecialchars($applicant['salary']);
                    } else {
                        echo '$20/hr';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("form_work_start") ?></span></p>
                    <div class="field-box"><?php if (isset($applicant['available_start_date'])) {
                        echo htmlspecialchars($applicant['available_start_date']);
                    } else {
                        echo 'N/A';
                    } ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("form_emergency_contact_1") ?></span></p>
                    <div class="field-box"><?php if (isset($contracting['contact_name1'])) {
                        echo htmlspecialchars($contracting['contact_name1']);
                    } else {
                        echo 'NA';
                    } ?></div>
                </div>
                
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("form_emergency_address_1") ?></span></p>
                    <div class="field-box"><?php if (isset($contracting['contact_address1'])) {
                        echo htmlspecialchars($contracting['contact_address1']);
                    } else {
                        echo 'NA';
                    } ?></div>
                </div>

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("form_emergency_phone_1") ?></span></p>
                    <div class="field-box"><?php if (isset($contracting['contact_phone1'])) {
                        echo htmlspecialchars($contracting['contact_phone1']);
                    } else {
                        echo 'NA';
                    } ?></div>
                </div>

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("form_emergency_relation_1") ?></span></p>
                    <div class="field-box"><?php if (isset($contracting['contact_relationship1'])) {
                        echo htmlspecialchars($contracting['contact_relationship1']);
                    } else {
                        echo 'NA';
                    } ?></div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("form_emergency_contact_2") ?></span></p>
                    <div class="field-box"><?php if (isset($contracting['contact_name2'])) {
                        echo htmlspecialchars($contracting['contact_name2']);
                    } else {
                        echo 'NA';
                    } ?></div>
                </div>
                
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("form_emergency_address_2") ?></span></p>
                    <div class="field-box"><?php if (isset($contracting['contact_address2'])) {
                        echo htmlspecialchars($contracting['contact_address2']);
                    } else {
                        echo 'NA';
                    } ?></div>
                </div>

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("form_emergency_phone_2") ?></span></p>
                    <div class="field-box"><?php if (isset($contracting['contact_phone2'])) {
                        echo htmlspecialchars($contracting['contact_phone2']);
                    } else {
                        echo 'NA';
                    } ?></div>
                </div>

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("form_emergency_relation_2") ?></span></p>
                    <div class="field-box"><?php if (isset($contracting['contact_relationship2'])) {
                        echo htmlspecialchars($contracting['contact_relationship2']);
                    } else {
                        echo 'NA';
                    } ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 single-box">
                <p><span class="label"><?php echo lang("form_notes") ?></span></p>
                    <div class="field-box"><?php if (isset($contracting['notes'])) {
                        echo htmlspecialchars($contracting['notes']);
                    } else {
                        echo 'NA';
                    } ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_qualifying_children") ?></span></p>
                    <div class="field-box"> <?php if (isset($w4_form['qualifying_children'])) {
                        echo htmlspecialchars($w4_form['qualifying_children']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"> <?php echo lang("form_other_dependents") ?></span></p>
                    <div class="field-box"> <?php if (isset($w4_form['number_of_other_dependents'])) {
                        echo htmlspecialchars($w4_form['number_of_other_dependents']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"> <?php echo lang("form_amount") ?></span></p>
                    <div class="field-box"> <?php if (isset($w4_form['amount_for_qualifying_children'])) {
                        echo htmlspecialchars($w4_form['amount_for_qualifying_children']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"> <?php echo lang("form_tax") ?></span></p>
                    <div class="field-box"> <?php if (isset($w4_form['tax_withheld'])) {
                        echo htmlspecialchars($w4_form['tax_withheld']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>

                <div class="col-md-6 single-box">
                    <p><span class="label"> <?php echo lang("form_deductions") ?></span></p>
                    <div class="field-box"> <?php if (isset($w4_form['claim_deductions'])) {
                        echo htmlspecialchars($w4_form['claim_deductions']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>

                <div class="col-md-6 single-box">
                    <p><span class="label"> <?php echo lang("form_with_holdings") ?></span></p>
                    <div class="field-box"> <?php if (isset($w4_form['extra_withholding'])) {
                        echo htmlspecialchars($w4_form['claim_deductions']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>

                <div class="col-md-6 single-box">
                    <p><span class="label"> <?php echo lang("form_employer") ?></span></p>
                    <div class="field-box"> <?php if (isset($w4_form['employers_name'])) {
                        echo htmlspecialchars($w4_form['employers_name']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>

                <div class="col-md-6 single-box">
                    <p><span class="label"> <?php echo lang("form_date_of_employment") ?></span></p>
                    <div class="field-box"> <?php if (isset($w4_form['date_of_employment'])) {
                        echo htmlspecialchars($w4_form['date_of_employment']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>

                <div class="col-md-6 single-box">
                    <p><span class="label"> <?php echo lang("form_ein") ?></span></p>
                    <div class="field-box"> <?php if (isset($w4_form['ein'])) {
                        echo htmlspecialchars($w4_form['ein']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_quick_instructions") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['instructions'])) {
                        echo htmlspecialchars($quick_book['instructions']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_bank") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['bank_name'])) {
                        echo htmlspecialchars($quick_book['bank_name']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_percentage") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['percentage'])) {
                        echo htmlspecialchars($quick_book['percentage']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_account_1") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['account_1'])) {
                        echo htmlspecialchars($quick_book['account_1']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_account_type") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['account_type'])) {
                        echo htmlspecialchars($quick_book['account_type']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_aba") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['aba_number'])) {
                        echo htmlspecialchars($quick_book['aba_number']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_account") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['account_number'])) {
                        echo htmlspecialchars($quick_book['account_number']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
            </div>

            <div class="row">
            <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_account_2") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['account_2'])) {
                        echo htmlspecialchars($quick_book['account_2']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_account_type") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['account_2_type'])) {
                        echo htmlspecialchars($quick_book['account_2_type']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_aba") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['aba_number_2'])) {
                        echo htmlspecialchars($quick_book['aba_number_2']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_account") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['account_number_2'])) {
                        echo htmlspecialchars($quick_book['account_number_2']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_authorize") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['authorization'])) {
                        echo htmlspecialchars($quick_book['authorization']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 
                
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_company") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['authorizes_company'])) {
                        echo htmlspecialchars($quick_book['authorizes_company']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_id") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['employee_id'])) {
                        echo htmlspecialchars($quick_book['employee_id']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_print_name") ?></span></p>
                    <div class="field-box"><?php if (isset($quick_book['print_name'])) {
                        echo htmlspecialchars($quick_book['print_name']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 
                </div>

                <div class="row">
            <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_first_name") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['fist_name'])) {
                        echo htmlspecialchars($eligibility['first_name']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_last_name") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['last_name'])) {
                        echo htmlspecialchars($eligibility['last_name']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_middle_name") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['middle_name'])) {
                        echo htmlspecialchars($eligibility['middle_name']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_other_last_name") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['other_last_name'])) {
                        echo htmlspecialchars($eligibility['other_last_name']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_mi") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['mi'])) {
                        echo htmlspecialchars($eligibility['mi']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_citizen") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['citizen_immigration_status'])) {
                        echo htmlspecialchars($eligibility['citizen_immigration_status']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_dob") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['dob'])) {
                        echo htmlspecialchars($eligibility['dob']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_doc_title") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['document_title'])) {
                        echo htmlspecialchars($eligibility['document_title']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_issue") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['issuing_authority'])) {
                        echo htmlspecialchars($eligibility['issuing_authority']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_doc_num") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['document_number'])) {
                        echo htmlspecialchars($eligibility['document_number']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_expiration") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['expiration_date'])) {
                        echo htmlspecialchars($eligibility['expiration_date']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 
                </div>

                <div class="row">
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_doc_title_1") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['document_title_1'])) {
                        echo htmlspecialchars($eligibility['document_title_1']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_issue") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['issuing_authority_1'])) {
                        echo htmlspecialchars($eligibility['issuing_authority_1']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_doc_num") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['document_number_1'])) {
                        echo htmlspecialchars($eligibility['document_number_1']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_expiration") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['expiration_date_1'])) {
                        echo htmlspecialchars($eligibility['expiration_date_1']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                </div>


                <div class="row">

                
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_doc_title_2") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['document_title_2'])) {
                        echo htmlspecialchars($eligibility['document_title_2']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_issue_2") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['issuing_authority_2'])) {
                        echo htmlspecialchars($eligibility['issuing_authority_2']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_doc_num_2") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['document_number_2'])) {
                        echo htmlspecialchars($eligibility['document_number_2']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_expiration_2") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['expiration_date_2'])) {
                        echo htmlspecialchars($eligibility['expiration_date_2']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                </div>

                <div class="row">
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_first_name_1") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['first_name_1'])) {
                        echo htmlspecialchars($eligibility['first_name_1']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_last_name_1") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['last_name_1'])) {
                        echo htmlspecialchars($eligibility['last_name_1']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_middle_initial") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['middle_initial'])) {
                        echo htmlspecialchars($eligibility['middle_initial']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_rehire") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['rehire_date'])) {
                        echo htmlspecialchars($eligibility['rehire_date']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_doc_5") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['document_title_5'])) {
                        echo htmlspecialchars($eligibility['document_title_5']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_doc_num_5") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['document_number_5'])) {
                        echo htmlspecialchars($eligibility['document_number_5']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_exp_4") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility['expiration_date_4'])) {
                        echo htmlspecialchars($eligibility['expiration_date_4']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                </div>


                <div class="row">
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_citizen_of_us") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility1['citizen_of_us'])) {
                        echo htmlspecialchars($eligibility1['citizen_of_us']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_citizen_of_us") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility1['citizen_of_us'])) {
                        echo htmlspecialchars($eligibility1['citizen_of_us']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_non_citizen_of_us") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility1['non_citizen_of_us'])) {
                        echo htmlspecialchars($eligibility1['non_citizen_of_us']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_permanent_resident") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility1['lawful_permanent_resident'])) {
                        echo htmlspecialchars($eligibility1['lawful_permanent_resident']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_allien") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility1['allen_authorized_work'])) {
                        echo htmlspecialchars($eligibility1['allen_authorized_work']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_reg_no") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility1['registeration_number'])) {
                        echo htmlspecialchars($eligibility1['registeration_number']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 


                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_allen_reg_num") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility1['allen_registeration_number'])) {
                        echo htmlspecialchars($eligibility1['allen_registeration_number']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_passport") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility1['passport_number'])) {
                        echo htmlspecialchars($eligibility1['passport_number']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_passport") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility1['passport_number'])) {
                        echo htmlspecialchars($eligibility1['passport_number']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_issuance_country") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility1['country_of_issuance'])) {
                        echo htmlspecialchars($eligibility1['country_of_issuance']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_translator_certificate") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility1['translator_certificate'])) {
                        echo htmlspecialchars($eligibility1['translator_certificate']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_signature_of_translator") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility1['signature_of_translator'])) {
                        echo htmlspecialchars($eligibility1['signature_of_translator']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_first_name_2") ?></span></p>
                    <div class="field-box"><?php if (isset($eligibility1['first_name_2'])) {
                        echo htmlspecialchars($eligibility1['first_name_2']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 
                </div>

                <div class="row">
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_company_name") ?></span></p>
                    <div class="field-box"><?php if (isset($mvr['company_name'])) {
                        echo htmlspecialchars($mvr['company_name']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div>
                
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_license_number") ?></span></p>
                    <div class="field-box"><?php if (isset($mvr['license_number'])) {
                        echo htmlspecialchars($mvr['license_number']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                </div>

                <div class="row">

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_start_date") ?></span></p>
                    <div class="field-box"><?php if (isset($employment_data['employee_start_date'])) {
                        echo htmlspecialchars($employment_data['employee_start_date']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_signature_1") ?></span></p>
                    <div class="field-box"><?php if (isset($employment_data['employer_signature_1'])) {
                        echo htmlspecialchars($employment_data['employer_signature_1']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_signature_title") ?></span></p>
                    <div class="field-box"><?php if (isset($employment_data['employer_title'])) {
                        echo htmlspecialchars($employment_data['employer_title']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_employee_fisrt_name") ?></span></p>
                    <div class="field-box"><?php if (isset($employment_data['employee_fisrt_name'])) {
                        echo htmlspecialchars($employment_data['employee_fisrt_name']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_employee_last_name") ?></span></p>
                    <div class="field-box"><?php if (isset($employment_data['employer_last_name'])) {
                        echo htmlspecialchars($employment_data['employer_last_name']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_employer_business_name") ?></span></p>
                    <div class="field-box"><?php if (isset($employment_data['employer_business_name'])) {
                        echo htmlspecialchars($employment_data['employer_business_name']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_employer_address") ?></span></p>
                    <div class="field-box"><?php if (isset($employment_data['employer_address'])) {
                        echo htmlspecialchars($employment_data['employer_address']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 
                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_employer_state") ?></span></p>
                    <div class="field-box"><?php if (isset($employment_data['state'])) {
                        echo htmlspecialchars($employment_data['state']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_employer_zip") ?></span></p>
                    <div class="field-box"><?php if (isset($employment_data['employer_zip'])) {
                        echo htmlspecialchars($employment_data['employer_zip']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 

                <div class="col-md-6 single-box">
                    <p><span class="label"><?php echo lang("formview_signature_date") ?></span></p>
                    <div class="field-box"><?php if (isset($employment_data['signature_date'])) {
                        echo htmlspecialchars($employment_data['signature_date']);
                    } else {
                        echo 'N/A';
                    } ?></div>
                </div> 
                </div>
                
<script>
             document.getElementById("printBtn").addEventListener("click", function() {
    window.print();
});

</script>

