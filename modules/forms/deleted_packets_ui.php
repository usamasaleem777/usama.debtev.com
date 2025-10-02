<?php
if ($_SESSION['role_id'] == $admin_role || $_SESSION['role_id'] == $manager_role) {

// Fetch additional information from other tables
// $contractings = DB::query("SELECT * FROM craft_contracting");


}
?>



<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
crossorigin="anonymous" referrerpolicy="no-referrer" />

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

/* Search Filter Styles */
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

/* Mobile Card Styles */
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

/* Action button styles for desktop */
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

/* Extra small devices (phones, 360px and down) */
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

@media screen and (max-width: 430px) {
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

/* Small devices (phones, 576px and down) */
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

/* Medium devices (tablets, 768px and up) */
@media screen and (min-width: 768px) and (max-width: 991px) {

    /* Adjust filter width */
    #roleFilter {
        max-width: 200px;
    }

    .row1 {
        margin-left: -30px;
        margin-right: -30px;
        margin-top: -25px;
    }

}
</style>

<div class="main-content app-content mt-0">
<!-- Page header with breadcrumb navigation -->
<div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
    <div style="margin-top: 25px;">
        <ol class="breadcrumb float-sm-right mt-2">
            <!-- Home breadcrumb -->
            <li class="breadcrumb-item">
                <a href="index.php" style="color: #fe5500"><i
                        class="fas fa-home me-1"></i><?php echo lang("user_home"); ?></a>
            </li>
            <!-- Position breadcrumb -->
            <li class="breadcrumb-item">
                <a href="#" style="color: #fe5500"><?php echo lang(key: "admin_list_data"); ?></a>
            </li>
            <!-- View position breadcrumb -->
            <li class="breadcrumb-item">
                <a href="#" style="color: #fe5500"><?php echo lang("admin_delete_packets"); ?></a>
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
                            <?php echo lang("admin_delete_packets"); ?>
                        </h3>

                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-hover" id="users-datatable"
                            style="width: 100% !important;">
                            <thead class="table-light">
                                <tr>
                                    <th><?php echo lang("form_first_name"); ?></th>
                                    <th><?php echo lang("form_last_name"); ?></th>
                                    <!-- <th><?php // echo lang("list_city"); ?></th> -->
                                    <th><?php echo lang("list_Phone"); ?></th>
                                    <th><?php echo lang("list_email"); ?></th>
                                    <th><?php echo lang("list_position"); ?></th>
                                    <th><?php echo lang("list_job"); ?></th>
                                    <th><?php echo lang("list_Created_at"); ?></th>
                                    <th><?php echo lang("list_reffered_by"); ?></th>
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


<!-- Add SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    $('#users-datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax_helpers/ajax_deleted_packets.php",
            "type": "GET",
            "dataSrc": function (json) {
                console.log("Server response:", json); // Debug response
                return json.data;
            }
        },
        "columns": [
            { "data": "first_name" },
            { "data": "last_name" },
            { "data": "phone_number" },
            { "data": "email" },
            { 
                "data": "position",
                "render": function (data, type, row) {
                    if (type === 'display') {
                        let options = '<option value="">No position applied</option>';
                        if (row.available_positions && row.available_positions.length > 0) {
                            try {
                                const positions = JSON.parse(row.available_positions);
                                positions.forEach(position => {
                                    const selected = position.id == row.current_position_id ? 'selected' : '';
                                    options += `<option value="${position.id}" ${selected}>${position.title}</option>`;
                                });
                            } catch (e) {
                                console.error("Error parsing positions:", e);
                            }
                        }
                        return `<select class="form-select position-select" data-applicant-id="${row.id}">${options}</select>`;
                    }
                    return data;
                }
            },
            { 
                "data": "job_applied",
                "render": function (data, type, row) {
                    if (type === 'display') {
                        let options = '<option value="">No job applied</option>';
                        if (row.applied_jobs && row.applied_jobs.length > 0) {
                            try {
                                const jobs = JSON.parse(row.applied_jobs);
                                jobs.forEach(job => {
                                    const selected = job.id == row.current_job_id ? 'selected' : '';
                                    options += `<option value="${job.id}" ${selected}>${job.title}</option>`;
                                });
                            } catch (e) {
                                console.error("Error parsing jobs:", e);
                            }
                        }
                        return `<select class="form-select job-select" data-applicant-id="${row.id}">${options}</select>`;
                    }
                    return data;
                }
            },
            { 
                "data": "created_at",
                "render": function (data) {
                    if (data) {
                        const date = new Date(data);
                        return date.toLocaleDateString('en-US', {
                            weekday: 'short',
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                    }
                    return "N/A";
                }
            },
            { 
                "data": "reference",
                "defaultContent": "N/A" 
            },
            {
                "data": "id",
                "render": function (data, type, row) {
                    return `
                        <a href="pdfs/pdf_data.php?id=${data}" class="btn btn-sm btn-danger">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="index.php?route=modules/forms/edit_packet&id=${data}" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i>
                        </a>
                    `;
                },
                "orderable": false,
                "searchable": false
            }
        ],
        "createdRow": function(row, data, dataIndex) {
            console.log("Created row:", data); // Debug each row
        }
    });
});
// Generic dropdown change handler for both position and job
$(document).on('change', '.position-select, .job-select', function () {
    const applicantId = $(this).data('applicant-id');
    const value = $(this).val();
    const field = $(this).hasClass('position-select') ? 'position' : 'job_applied';

    $.ajax({
        url: 'ajax_helpers/update_applicant_fields.php',
        method: 'POST',
        dataType: 'JSON',
        data: {
            applicant_id: applicantId,
            field: field,
            value: value
        },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Updated successfully.',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Failed to update.',
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Error while updating.',
            });
        }
    });
});
</script>