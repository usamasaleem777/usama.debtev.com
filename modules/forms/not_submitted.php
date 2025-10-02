<?php
if ($_SESSION['role_id'] == $admin_role || $_SESSION['role_id'] == $manager_role) {
    // Fetch additional information from other tables
    // $contractings = DB::query("SELECT * FROM craft_contracting");
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .row {
        margin-top: 10px;
    }

    .btn-orange {
        background-color: #FE5500;
        border-color: #FE5500;
        color: white;
    }

    .btn-orange:hover {
        background-color: #e04b00;
        border-color: #e04b00;
        color: white;
    }

    .bg-orange {
        background-color: #FE5500 !important;
    }

    #users-datatable_filter input {
        border-radius: 20px !important;
        border: 1px solid #FE5505 !important;
        padding: 5px 15px !important;
        margin-bottom: 15px;
    }

    #users-datatable th {
        background: #FE5505 !important;
        color: white !important;
        font-size: 0.85rem;
        padding: 8px 5px;
    }

    #users-datatable td {
        vertical-align: middle;
        padding: 8px 5px;
        font-size: 0.85rem;
    }

    .page-item.active .page-link {
        background: #FE5505 !important;
        border-color: #FE5505 !important;
    }

    .square-filter {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.9rem;
    }

    .square-filter:focus {
        border-color: #adb5bd;
        box-shadow: 0 0 0 3px rgba(108, 117, 125, 0.15);
    }

    .user-card .card {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
    }

    .user-card .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: #333;
    }

    .detail-item {
        display: flex;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
    }

    .detail-label {
        font-weight: 600;
        color: #555;
        min-width: 100px;
    }

    .detail-value {
        color: #333;
        flex-grow: 1;
    }

    /* Modal Header */
    .modal-header {
        background-color: #FE5500;
        /* color: white; */
        padding: 15px 20px;
        border-bottom: 2px solid #fff;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    /* Close Button */
    .btn-close-white {
        background-color: transparent;
        border: none;
        color: white;
        font-size: 1.5rem;
    }

    /* Card Header */
    .card-header {
        background-color: white;
        padding: 15px 20px;
        border-bottom: 2px solid #fff;
    }

    /* Card Title */
    .card-title {
        font-size: 1.2rem;
        font-weight: bold;
    }

    /* Note Form */
    #noteForm {
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    textarea.form-control {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        font-size: 1rem;
        resize: none;
    }

    /* Button Styles */
    button[type="submit"] {
        background-color: #FE5500;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 1rem;
        transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
        background-color: #d94400;
    }

    /* Cancel Button */
    #cancelEdit {
        font-size: 0.9rem;
        padding: 8px 16px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    #cancelEdit:hover {
        background-color: #a6a6a6;
    }

    /* Modal Body */
    .modal-body {
        padding: 20px;
    }

    #notesContainer {
        max-height: 350px;
        overflow-y: auto;
        padding-right: 5px;
    }

    #notesContainer .note-card {
        margin-bottom: 15px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .modal-dialog {
            max-width: 90%;
        }

        .modal-header,
        .card-header,
        .modal-body {
            padding: 15px;
        }

        .modal-title {
            font-size: 1.1rem;
        }

        .card-title {
            font-size: 1.1rem;
        }

        textarea.form-control {
            font-size: 0.9rem;
        }

        button[type="submit"] {
            font-size: 0.9rem;
            padding: 8px 15px;
        }
    }

    @media (min-width: 768px) {
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
        }

        .action-text {
            display: none;
        }

        .action-btn:hover {
            width: auto;
            padding: 0.25rem 0.5rem !important;
        }

        .action-btn:hover .action-text {
            display: inline;
            margin-left: 4px;
        }
    }

    .custom-dropdown {
        position: relative;
        display: inline-block;
    }

    .custom-dropdown-menu {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 180px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        border-radius: 4px;
        padding: 0;
        z-index: 9999;
        right: 0;
    }

    .custom-dropdown-menu.show {
        display: block;
    }

    .custom-dropdown-option {
        display: block;
        padding: 8px 12px;
        cursor: pointer;
        /* border-bottom: 1px solid #eee; */
        font-size: 14px;
        transition: all 0.2s ease-in-out;
    }

    .custom-dropdown-option:last-child {
        border-bottom: none;
    }

    .custom-dropdown-footer {
        padding: 8px 12px;
        /* border-top: 1px solid #eee; */
        text-align: right;
    }

    .custom-dropdown-continue {
        background-color: #2ecc71;
        color: white;
        border: none;
        padding: 4px 8px;
        border-radius: 3px;
        cursor: pointer;
    }

    .packet-dropdown {
        position: relative;
        display: inline-block;
    }

    .packet-dropdown-menu {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 180px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        border-radius: 4px;
        padding: 0;
        z-index: 1000;
        right: 0;
    }

    /* Add these media queries at the end of your existing CSS */

    @media (max-width: 767px) {

        /* Button container adjustments */
        .d-flex.gap-1 {
            flex-wrap: wrap;
            gap: 4px !important;
        }

        /* Base button styles for mobile */
        .btn-sm {
            padding: 0.3rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.2;
            min-width: 32px;
        }

        /* Icon-only buttons on mobile */
        .btn-sm i {
            margin-right: 0 !important;
        }

        /* Text hidden on mobile except for hover */
        .btn-sm .d-none-mobile {
            display: none;
        }

        /* Generate Link button adjustments */
        .btn-sm[data-bs-target="#generateLinkModal"] {
            padding: 0.3rem 0.6rem;
        }

        /* Dropdown button adjustments */
        .custom-dropdown>button {
            padding: 0.3rem 0.6rem;
            width: 100%;
            text-align: center;
        }

        /* Dropdown menu adjustments */
        .custom-dropdown-menu {
            width: 100vw;
            right: -15px !important;
            left: auto !important;
            min-width: 280px;
            transform: translateX(25%);
        }

        /* Table specific adjustments */
        #users-datatable td:last-child {
            min-width: 240px;
        }

        /* Modal button adjustments */
        .modal-footer .btn {
            width: 100%;
            margin: 4px 0;
        }
    }

    @media (max-width: 576px) {

        /* Further reduce button sizes */
        .btn-sm {
            padding: 0.25rem 0.4rem;
            font-size: 0.7rem;
        }

        /* Stack modal buttons */
        .modal-footer {
            flex-direction: column;
        }

        /* Adjust dropdown menu position */
        .custom-dropdown-menu {
            transform: translateX(15%);
        }

        /* Ensure buttons remain visible */
        #users-datatable td:last-child {
            min-width: 260px;
        }
    }

    @media (max-width: 400px) {

        /* Compact button layout */
        .d-flex.gap-1 {
            gap: 2px !important;
        }

        /* Hide button text completely */
        .btn-sm span.d-none-mobile {
            display: none;
        }

        /* Icon-only buttons */
        .btn-sm i {
            margin: 0 !important;
        }

        /* Smaller padding */
        .btn-sm {
            padding: 0.2rem 0.3rem;
        }

        /* Adjust dropdown menu positioning */
        .custom-dropdown-menu {
            transform: translateX(10%);
        }
    }

    .packet-dropdown-menu.show {
        display: block;
    }

    .packet-dropdown-option {
        display: block;
        padding: 8px 12px;
        cursor: pointer;
        /* border-bottom: 1px solid #eee; */
        font-size: 14px;
        transition: all 0.2s ease-in-out;
    }

    .packet-dropdown-option:last-child {
        border-bottom: none;
    }

    .packet-dropdown-footer {
        padding: 8px 12px;
        border-top: 1px solid #eee;
        text-align: right;
    }

    .packet-dropdown-continue {
        background-color: #2ecc71;
        color: white;
        border: none;
        padding: 4px 8px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
    }

    .packet-dropdown-continue:hover {
        background-color: #27ae60;
    }

    .packet-btn {
        background-color: #17a2b8;
        color: white;
        border-color: #17a2b8;
        padding: 0.25rem 0.5rem;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .packet-btn:hover {
        background-color: #138496;
        border-color: #138496;
    }

    @media screen and (max-width: 360px) {
        .page-header {
            margin-top: 10px !important;
            margin-bottom: 10px !important;
        }

        .row1 {
            margin-left: -30px;
            margin-right: -30px;
            margin-top: -25px;
        }

        .breadcrumb {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }

        .breadcrumb-item i {
            font-size: 0.7rem;
            margin-right: 0.25rem !important;
        }

        .card-title {
            font-size: 1rem !important;
        }

        .btn-orange.btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .user-card {
            padding: 0 5px;
        }

        .user-card .card-body {
            padding: 1rem;
        }

        .detail-item {
            flex-direction: column;
        }

        .detail-label {
            min-width: auto;
            margin-bottom: 0.1rem;
        }

        #users-cards-container {
            margin: 0 -5px;
        }

        .alert {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }
    }

    @media screen and (max-width: 576px) {
        .card-body {
            padding: 1rem !important;
        }

        .card-title {
            font-size: 1.1rem !important;
        }

        .user-card .card-body {
            padding: 1.25rem;
        }

        .detail-item {
            font-size: 0.9rem;
        }
    }

    @media screen and (min-width: 768px) and (max-width: 991px) {
        #roleFilter {
            max-width: 200px;
        }
    }

    /* Show text on desktop, icons on mobile */
    .d-none-desktop {
        display: none;
    }

    .d-none-mobile {
        display: inline-block;
    }

    @media (max-width: 767px) {
        .d-none-desktop {
            display: inline-block !important;
            margin-right: 0 !important;
        }

        .d-none-mobile {
            display: none !important;
        }

        /* Adjust button padding for icons */
        .btn-sm {
            padding: 0.35rem 0.6rem !important;
            min-width: 36px;
        }
    }

    /* Desktop hover effects */
    @media (min-width: 768px) {
        .btn-sm {
            padding: 0.4rem 0.8rem;
        }

        .btn-sm:hover {
            background-position: right center;
        }
    }
</style>

<div class="main-content app-content mt-0">
    <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
        <div style="margin-top: 25px;">
            <ol class="breadcrumb float-sm-right mt-2">
                <li class="breadcrumb-item">
                    <a href="index.php" style="color: #fe5500"><i
                            class="fas fa-home me-1"></i><?php echo lang("user_home"); ?></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="#" style="color: #fe5500"><?php echo lang(key: "admin_list_data"); ?></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="#" style="color: #fe5500"><?php echo lang("admin_view_data"); ?></a>
                </li>
            </ol>
        </div>
    </div>

    <div class="row1">
        <div class="row">
            <div class="col-12 mt-4">
                <div class="card rounded-4">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title fw-bold m-0" style="font-size: 1.2rem;">
                                <?php echo lang("admin_packet_applicants"); ?>
                            </h3>
                        </div>

                        <!-- Add this just above your <table id="users-datatable"> -->
                        <div class="mb-2">
                            <input type="text" id="kioskIdFilter" class="form-control"
                                placeholder="Search by Kiosk ID"
                                style="max-width:200px; display:inline-block;">
                            <button id="kioskIdFilterBtn" class="btn btn-orange btn-sm ms-2">
                                Filter
                            </button>
                            <button id="kioskIdClearBtn" class="btn btn-secondary btn-sm ms-1">
                                Clear
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-hover" id="users-datatable"
                                style="width: 100% !important;">
                                <thead class="table-light">
                                    <tr>
                                        <th><?php echo lang("form_first_name"); ?></th>
                                        <th><?php echo lang("form_last_name"); ?></th>
                                        <th><?php echo lang("list_Phone"); ?></th>
                                        <th><?php echo lang("list_email"); ?></th>
                                        <th><?php echo lang("list_position"); ?></th>
                                        <th><?php echo lang("job_address"); ?></th>
                                        <th><?php echo lang("list_Created_at"); ?></th>
                                        <th><?php echo lang("packet_reffered_by"); ?></th>
                                        <th>Kiosk ID</th>
                                        <th><?php echo lang("list_actions"); ?></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
<!-- Notes Modal (Corrected Structure) -->
<!-- Notes Modal (Corrected Structure) -->
<div class="modal fade" id="notesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header" style="background-color: #FE5500; color: white;">
                <h5 class="modal-title" style="color: white">
                    <i class="fas fa-sticky-note me-2"></i>
                    <?php echo lang("formview_applicants_notes"); ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Card Header (Note Section Title) -->
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>
                    <?php echo lang("formview_add_notes"); ?>
                </h5>
            </div>

            <!-- Note Form -->
            <form id="noteForm" class="p-4">
                <input type="hidden" id="noteId" name="note_id">
                <input type="hidden" id="modalApplicantId" name="applicant_id">

                <div class="mb-3">
                    <textarea class="form-control" id="noteText" name="note_text" rows="4"
                        placeholder="Enter your note here..."></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" id="cancelEdit" class="btn btn-secondary" style="display: none;">
                        <?php echo lang("formview_cancel"); ?>
                    </button>
                    <button type="submit" class="btn text-white ms-auto" style="background-color: #FE5500;">
                        <span id="saveButtonText"><?php echo lang("formview_save_notes"); ?></span>
                    </button>
                </div>
            </form>

            <!-- Modal Body (Notes Container) -->
            <div class="modal-body">
                <div id="notesContainer" class="mt-3" style="max-height: 400px; overflow-y: auto;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#users-datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "ajax_helpers/ajax_not_submitted.php",
                "type": "GET",
                "data": function(d) {
                    d.kioskID = $('#kioskIdFilter').val(); // Send KioskID filter
                }
            },
            "columns": [{
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
                    "data": "position",
                    "render": function(data, type, row) {
                        if (data) {
                            return data.split(',').join('<br>');
                        }
                        return '';
                    }
                }, {
                    "data": "job_address",
                    "defaultContent": "N/A"
                },
                {
                    "data": "generated_date",
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
                    "data": "reference",
                    "defaultContent": "N/A"
                },
                {
                    "data": "kioskID",
                    "defaultContent": "N/A"
                },
                {
                    "data": "actions",
                    "render": function(data, type, row) {
                        return `
        <div class="d-flex gap-1">
            <!-- View Button -->
            <a href="index.php?route=modules/applicants/view_applicant_new&id=${row.id}" 
               class="btn btn-sm btn-danger">
                <i class="fa fa-eye d-none-desktop"></i>
                <span class="d-none-mobile"><?php echo lang('view'); ?></span>
            </a>

            <!-- Comments Button -->
            <a href="#" 
            class="btn btn-sm btn-primary view-notes" 
            data-bs-toggle="modal" 
            data-bs-target="#notesModal"
            data-applicant-id="${row.id}">
                <i class="fa fa-comments d-none-desktop "></i>
                <span class="d-none-mobile"><?php echo lang('comments'); ?></span>
            </a>
            <!-- Generate Link Button -->
            <button type="button" class="btn btn-info btn-sm generate-link-btn" 
                data-bs-toggle="modal" 
                data-bs-target="#generateLinkModal"
                data-applicant-id="${row.id}"
                style="background-color: #008000; color:white">
                <i class="fas fa-link d-none-desktop"></i>
                <span class="d-none-mobile"><?php echo lang("generate_link"); ?></span>
            </button>

            <!-- Re-Send Packet Button -->
            <div class="custom-dropdown" tabindex="0">
                <button class="btn btn-sm btn-info text-white">
                    <i class="fas fa-sync-alt d-none-desktop"></i>
                    <span class="d-none-mobile"><?php echo lang("leave_Re-Send_Packet"); ?></span>
                </button>
                <div class="custom-dropdown-menu">

                    <label class="custom-dropdown-option"><input type="checkbox" value="3" checked> <?php echo lang("leave_W4_Data"); ?></label>
                    <label class="custom-dropdown-option"><input type="checkbox" value="4" checked> <?php echo lang("leave_Quick_Book"); ?></label>
                    <label class="custom-dropdown-option"><input type="checkbox" value="5" checked> <?php echo lang("leave_EGV"); ?></label>
                    <label class="custom-dropdown-option"><input type="checkbox" value="6" checked> <?php echo lang("leave_MVR_Information"); ?></label>
                    <label class="custom-dropdown-option"><input type="checkbox" value="7" > <?php echo lang("leave_NON_COMPETE_AGREEMENT"); ?></label>
                    
                    <div class="custom-dropdown-footer">
                        <button class="custom-dropdown-continue" data-applicant-id="${row.id}">
                            <i class="fas fa-paper-plane me-2"></i>
                            <?php echo lang("send"); ?>
                        </button>
                    </div>
                </div>
            </div>

           <button class="btn btn-sm btn-warning text-white send-reminder-btn" 
    data-applicant-id="${row.id}">
    <i class="fas fa-bell d-none-desktop"></i>
    <span class="d-none-mobile"><?php echo lang("send_reminder"); ?></span>
</button>

            
            <!-- Delete Button -->
            <button class="btn btn-sm btn-danger delete-applicant" data-applicant-id="${row.id}">
                <i class="fas fa-trash-alt d-none-desktop"></i>
                <span class="d-none-mobile"><?php echo lang("delete"); ?></span>
            </button>
        </div>
        `;
                    },
                    "orderable": true,
                    "searchable": true
                }
            ]
        });

        // Handle delete button click
        $(document).on('click', '.delete-applicant', function() {
            const applicantId = $(this).data('applicant-id');

            Swal.fire({
                title: '<?php echo lang("are_you_sure"); ?>',
                text: '<?php echo lang("delete_applicant_confirmation"); ?>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FE5500',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<?php echo lang("yes_delete"); ?>',
                cancelButtonText: '<?php echo lang("cancel"); ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'ajax_helpers/ajax_delete_applicant2.php',
                        method: 'POST',
                        data: {
                            applicant_id: applicantId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: '<?php echo lang("deleted"); ?>',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonColor: '#FE5500'
                                }).then(() => {
                                    $('#users-datatable').DataTable().ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: '<?php echo lang("error"); ?>',
                                    text: response.error,
                                    icon: 'error',
                                    confirmButtonColor: '#FE5500'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: '<?php echo lang("error"); ?>',
                                text: '<?php echo lang("error_deleting_applicant"); ?>',
                                icon: 'error',
                                confirmButtonColor: '#FE5500'
                            });
                        }
                    });
                }
            });
        });

        // Handle generate link button click to set applicant ID
        $(document).on('click', '.generate-link-btn', function() {
            const applicantId = $(this).data('applicant-id');
            $('#generateLinkForm input[name="applicant_id"]').val(applicantId);
        });

        $(document).on('click', '.custom-dropdown-continue', function(e) {
            e.preventDefault();
            e.stopPropagation();

            let applicant_id = $(this).data('applicant-id');
            const dropdown = $(this).closest('.custom-dropdown');
            const dropdownMenu = dropdown.find('.custom-dropdown-menu');

            const selectedValues = dropdown.find('input[type="checkbox"]:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedValues.length === 0) {
                Swal.fire({
                    title: "No Forms Selected",
                    text: "Please select at least one form to send",
                    icon: "warning",
                    confirmButtonColor: "#FE5500"
                });
                return;
            }

            const continueBtn = $(this);
            continueBtn.text('Sending...').prop('disabled', true);
            dropdownMenu.hide();

            $.ajax({
                url: 'ajax_helpers/ajax_send_packet_mail2.php',
                method: 'POST',
                data: {
                    applicant_id: applicant_id,
                    forms: selectedValues
                },
                success: function(response) {
                    try {
                        response = JSON.parse(response);
                        if (response.status == 200) {
                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                confirmButtonColor: "#FE5500"
                            });
                        } else if (response.status == 400) {
                            Swal.fire({
                                title: "Action Failed!",
                                text: response.message,
                                icon: "error",
                                confirmButtonColor: "#FE5500"
                            });
                        }
                    } catch (e) {
                        Swal.fire({
                            title: "Error",
                            text: "Invalid server response",
                            icon: "error",
                            confirmButtonColor: "#FE5500"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: "Error",
                        text: "Failed to send packet: " + error,
                        icon: "error",
                        confirmButtonColor: "#FE5500"
                    });
                },
                complete: function() {
                    continueBtn.text('Continue').prop('disabled', false);
                    dropdown.find('input[type="checkbox"]:checked').prop('checked', false);
                }
            });
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.custom-dropdown').length) {
                $('.custom-dropdown-menu').hide();
            }
        });

        $(document).on('click', '.custom-dropdown > button', function(e) {
            e.stopPropagation();
            const dropdown = $(this).parent();
            const menu = dropdown.find('.custom-dropdown-menu');
            $('.custom-dropdown-menu').not(menu).hide();
            menu.toggle();
        });
    });

    $('#generateLinkForm').submit(function(e) {
        e.preventDefault();
        const selectedForms = $('input[name="form_step[]"]:checked');
        const applicantId = $('input[name="applicant_id"]').val();

        if (!applicantId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Applicant ID is missing',
                confirmButtonColor: '#FE5500'
            });
            return false;
        }

        if (selectedForms.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Selection Required',
                text: 'Please select at least one form to generate the link',
                confirmButtonColor: '#FE5500'
            });
            return false;
        }

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'ajax_helpers/link_generator.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#generateLinkModal').modal('hide');
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
        today.setHours(0, 0, 0, 0);

        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const minDate = `${year}-${month}-${day}T00:00`;

        $expiryInput.attr('min', minDate);
    });

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

    $('#generateLinkModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
    });

    $('#generatedLinkModal').on('hidden.bs.modal', function() {
        $('#generatedLinkInput').val('');
    });
</script>
<script>
    $(document).on('click', '.send-reminder-btn', function() {
        const applicantId = $(this).data('applicant-id');

        Swal.fire({
            title: '<?php echo lang("send_reminder_confirmation"); ?>',
            text: '<?php echo lang("send_reminder_confirmation_text"); ?>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#FE5500',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax_helpers/ajax_send_reminder.php',
                    method: 'POST',
                    data: {
                        applicant_id: applicantId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 200) {
                            Swal.fire({
                                title: '<?php echo lang("Reminder Sent"); ?>',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#FE5500'
                            });
                        } else {
                            Swal.fire({
                                title: '<?php echo lang("error"); ?>',
                                text: response.message || 'Something went wrong.',
                                icon: 'error',
                                confirmButtonColor: '#FE5500'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: '<?php echo lang("error"); ?>',
                            text: 'AJAX error: ' + error,
                            icon: 'error',
                            confirmButtonColor: '#FE5500'
                        });
                    }
                });
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Handle notes modal opening
        $(document).on('click', '.view-notes', function() {
            const applicantId = $(this).data('applicant-id');
            $('#modalApplicantId').val(applicantId);
            loadNotes(applicantId);
        });

        // Handle note submission
        $('#noteForm').on('submit', function(e) {
            e.preventDefault();

            const applicantId = $('#modalApplicantId').val();
            if (!applicantId) {
                showAlert('error', 'Error', 'Invalid applicant ID');
                return;
            }

            const formData = $(this).serialize();
            const noteText = $('#noteText').val().trim();
            const noteId = $('#noteId').val();
            const url = noteId ? 'ajax_helpers/update_note.php' : 'ajax_helpers/save_note.php';

            if (!noteText) {
                showAlert('error', 'Error', '<?php echo lang("formview_Note_text_cannot_be_empty"); ?>');
                return;
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (noteId) {
                            $('#note-' + noteId + ' .note-text').text(response.note.note_text);
                            resetForm();
                            showAlert('success', '<?php echo lang("formview_Success"); ?>', '<?php echo lang("formview_Note_updated_successfully!"); ?>');
                        } else {
                            addNoteToDOM(response.note);
                            $('#noteText').val('');
                            showAlert('success', '<?php echo lang("formview_Success"); ?>', '<?php echo lang("formview_Note_saved_successfully!"); ?>');
                        }
                        loadNotes(applicantId); // Refresh notes list
                    } else {
                        showAlert('error', 'Error', response.error || '<?php echo lang("formview_Error_saving_note"); ?>');
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('error', 'Error', '<?php echo lang("formview_Error_saving_note:"); ?> ' + error);
                }
            });
        });

        // Handle note editing
        $(document).on('click', '.edit-note', function(e) {
            e.preventDefault();
            const noteId = $(this).data('note-id');
            const noteText = $('#note-' + noteId + ' .note-text').text();

            $('#noteId').val(noteId);
            $('#noteText').val(noteText).focus();
            $('#saveButtonText').text('<?php echo lang("formview_update_note"); ?>');
            $('#cancelEdit').show();
        });

        // Handle cancel edit
        $('#cancelEdit').on('click', resetForm);

        // Handle note deletion
        $(document).on('click', '.delete-note', function(e) {
            e.preventDefault();
            const $noteElement = $(this).closest('.note-card');
            const noteId = $(this).data('note-id');
            const applicantId = $('#modalApplicantId').val();

            Swal.fire({
                title: '<?php echo lang("formview_Are_you_sure?"); ?>',
                text: "<?php echo lang("formview_You won't be able to revert this!"); ?>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FE5500',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<?php echo lang("formview_Yes,_delete_it!"); ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'ajax_helpers/delete_note.php',
                        type: 'POST',
                        data: {
                            note_id: noteId,
                            applicant_id: applicantId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                $noteElement.remove();
                                checkEmptyNotes();
                                showAlert('success', '<?php echo lang("formview_Deleted!"); ?>', '<?php echo lang("formview_Note_deleted_successfully!"); ?>');
                                loadNotes(applicantId); // Refresh notes list
                            } else {
                                showAlert('error', 'Error', response.error || '<?php echo lang("formview_Error_deleting_note"); ?>');
                            }
                        },
                        error: function(xhr, status, error) {
                            showAlert('error', 'Error', '<?php echo lang("formview_Error_deleting_note"); ?> ' + error);
                        }
                    });
                }
            });
        });

        // Helper functions
        function loadNotes(applicantId) {
            console.log('Loading notes for applicant ID:', applicantId);
            $('#notesContainer').html('<div class="text-center py-2">Loading notes...</div>');
            $.ajax({
                url: 'ajax_helpers/get_notes.php',
                type: 'GET',
                data: {
                    applicant_id: applicantId,
                    debug: true // Add debug flag
                },
                success: function(response) {
                    console.log('Server response:', response);
                    $('#notesContainer').html(response);
                    checkEmptyNotes();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('#notesContainer').html(`<div class="alert alert-danger">Error loading notes: ${error}</div>`);
                }
            });
        }

        function resetForm() {
            $('#noteId').val('');
            $('#noteText').val('');
            $('#saveButtonText').text('<?php echo lang("formview_save_notes"); ?>');
            $('#cancelEdit').hide();
        }

        function addNoteToDOM(note) {
            $('#noNotesMessage').remove();
            const newNote = `
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
    // Place this after your DataTable initialization
    $('#kioskIdFilterBtn').on('click', function() {
        $('#users-datatable').DataTable().ajax.reload();
    });
    $('#kioskIdClearBtn').on('click', function() {
        $('#kioskIdFilter').val('');
        $('#users-datatable').DataTable().ajax.reload();
    });
    $('#kioskIdFilter').on('keypress', function(e) {
        if (e.which === 13) {
            $('#users-datatable').DataTable().ajax.reload();
        }
    });
</script>