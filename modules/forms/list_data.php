<?php
// Check if user has admin or manager role
if ($_SESSION['role_id'] == $admin_role || $_SESSION['role_id'] == $manager_role) {
    // Admin/manager specific logic can go here
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
   
    /* Modal styles */
    #addUserModal .modal-dialog {
        max-width: 600px;
    }
    
    #addUserModal .form-group {
        margin-bottom: 1rem;
    }
    
    #addUserModal label {
        font-weight: 600;
        margin-bottom: 0.5rem;
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
            margin-top: -10px;
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
            margin-top: -10px;
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
    <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
        <div style="margin-top: 25px;">
            <ol class="breadcrumb float-sm-right mt-2">
                <li class="breadcrumb-item">
                    <a href="index.php" style="color: #fe5500"><i
                            class="fas fa-home me-1"></i><?php echo lang("user_home"); ?></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="#" style="color: #fe5500"><?php echo lang("admin_list_data"); ?></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="#" style="color: #fe5500"><?php echo lang("admin_view_packets"); ?></a>
                </li>
            </ol>
        </div>
    </div>

    <div class="row1">
        <div class="row">
            <div class="col-md-12 mt-4">
                <div class="card rounded-4">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title fw-bold m-0" style="font-size: 1.2rem;">
                                <?php echo lang("admin_view_packets"); ?>
                            </h3>
                            <button class="btn btn-orange btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="fas fa-user-plus me-1"></i> Add New User
                            </button>
                        </div>

                        <!-- Add this above your DataTable -->
                        <div class="mb-2">
                            <input type="text" id="kioskIdFilter" class="form-control" placeholder="Search by Kiosk ID" style="max-width:200px; display:inline-block;">
                            <button id="kioskIdFilterBtn" class="btn btn-orange btn-sm ms-2">Filter</button>
                            <button id="kioskIdClearBtn" class="btn btn-secondary btn-sm ms-1">Clear</button>
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
                        <th><?php echo lang("list_Created_at"); ?></th>
                        <th><?php echo lang("list_reffered_by"); ?></th>
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

<!-- Modal for adding new user manually -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       pattern="^(\d{3}-\d{3}-\d{4}|\(\d{3}\) \d{3}-\d{4}|\d{10})$" 
                                       maxlength="14"
                                       required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kioskId">Kiosk ID</label>
                                <input type="text" class="form-control" id="kioskId" name="kioskId" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role_id">User Role</label>
                                <select class="form-select" id="role_id" name="role_id" required>
                                    <option value="">Select Role</option>
                                    <?php foreach (DB::query("SELECT id, name FROM roles") as $role): ?>
                                        <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-orange" id="saveUserBtn">Save User</button>
            </div>
        </div>
    </div>
</div>

<!-- Add SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Helper function to format phone numbers consistently
        function formatPhoneNumber(phoneNumber) {
            // Remove all non-digit characters
            const cleaned = ('' + phoneNumber).replace(/\D/g, '');
            
            // Check if the number starts with country code
            const match = cleaned.match(/^(1|)?(\d{3})(\d{3})(\d{4})$/);
            
            if (match) {
                // Format as (123) 456-7890
                const intlCode = match[1] ? '+1 ' : '';
                return [intlCode, '(', match[2], ') ', match[3], '-', match[4]].join('');
            }
            
            return phoneNumber; // return original if formatting fails
        }

        // Main DataTable
        const mainDataTable = $('#users-datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "ajax_helpers/ajax_packet_applicants.php",
                "type": "GET",
                "data": function(d) {
                    d.kioskID = $('#kioskIdFilter').val(); // Send KioskID filter
                }
            },
            "columns": [
                { "data": "first_name" },
                { "data": "last_name" },
                { 
                    "data": "phone_number",
                    "render": function(data) {
                        return formatPhoneNumber(data);
                    }
                },
                { "data": "email" },
                {
                    "data": "created_at",
                    "render": function (data, type, row) {
                        if (data) {
                            var date = new Date(data);
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
                { "data": "reference", "defaultContent": "N/A" },
                { "data": "kioskID", "defaultContent": "N/A" },
                {
                    "data": null,
                    "render": function (data, type, row) {
                        let buttons = `
                            <div class="d-flex flex-nowrap" style="gap: 5px;">
                                <a href="pdfs/pdf_data.php?id=${row.id}" class="btn btn-sm btn-danger action-btn" title="View PDF">
                                    <i class="fa fa-eye"></i>
                                    <span class="action-text">View</span>
                                </a>
                                <a href="index.php?route=modules/forms/edit_packet&id=${row.id}" class="btn btn-sm btn-warning action-btn" title="Edit">
                                    <i class="fa fa-edit"></i>
                                    <span class="action-text">Edit</span>
                                </a>`;
                        
                        if (!row.user_exists) {
                            buttons += `
                                <button class="btn btn-sm btn-primary add-user-btn action-btn" 
                                    data-id="${row.id}" 
                                    data-email="${row.email}"
                                    data-first-name="${row.first_name}"
                                    data-last-name="${row.last_name}"
                                    data-phone="${row.phone_number}"
                                    title="Add User">
                                    <i class="fa fa-user-plus"></i>
                                    <span class="action-text">Add User</span>
                                </button>`;
                        }
                        
                        buttons += `
                                <button class="btn btn-sm btn-danger delete-btn action-btn" data-id="${row.id}" title="Delete">
                                    <i class="fa fa-trash"></i>
                                    <span class="action-text">Delete</span>
                                </button>
                            </div>`;
                        
                        return buttons;
                    },
                    "orderable": false,
                    "searchable": false
                }
            ],
            // ADDED SORTING BY CREATED AT COLUMN (INDEX 4) IN DESCENDING ORDER
            "order": [[4, "desc"]]
        });

        // Save User button click handler
        $('#saveUserBtn').click(function() {
            const formData = {
                first_name: $('#firstName').val(),
                last_name: $('#lastName').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                kiosk_id: $('#kioskId').val(),
                user_name: $('#username').val(),
                role_id: $('#role_id').val()
            };

            // Simple validation
            if (!formData.first_name || !formData.last_name || !formData.email || !formData.phone || 
                !formData.kiosk_id || !formData.user_name || !formData.role_id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please fill all required fields'
                });
                return;
            }

            // Enhanced email validation
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(formData.email)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please enter a valid email address'
                });
                return;
            }

            // Enhanced phone validation (strict US format)
            const phoneRegex = /^(\d{3}-\d{3}-\d{4}|\(\d{3}\) \d{3}-\d{4}|\d{10})$/;
            const digitsOnly = formData.phone.replace(/\D/g, '');
            if (!phoneRegex.test(formData.phone) || digitsOnly.length !== 10) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please enter a valid 10-digit US phone number (e.g., (123) 456-7890 or 123-456-7890)'
                });
                return;
            }
            
            // Format phone number consistently before sending to server
            formData.phone = formatPhoneNumber(formData.phone);

            $.ajax({
                url: 'ajax_helpers/add_manual_user.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    $('#saveUserBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#addUserModal').modal('hide');
                            $('#addUserForm')[0].reset();
                            mainDataTable.ajax.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to add user'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.error?.message || 'Failed to add user'
                    });
                },
                complete: function() {
                    $('#saveUserBtn').prop('disabled', false).html('Save User');
                }
            });
        });

        // Add User button click handler (for existing applicants)
        $(document).on('click', '.add-user-btn', function() {
            const $btn = $(this);
            const applicantData = {
                applicant_id: $btn.data('id'),
                first_name: $btn.data('first-name'),
                last_name: $btn.data('last-name'),
                email: $btn.data('email'),
                phone: $btn.data('phone')
            };
            
            // Format phone number before showing in the form
            applicantData.phone = formatPhoneNumber(applicantData.phone);
            
            Swal.fire({
                title: 'Add User to System',
                html: `
                    <div class="container-fluid">
                        <div class="row mb-3">
                            <div class="col-12">
                                <p>This will create a user account from the packet data.</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="swal-username" class="form-label">Username </label>
                                <input type="text" id="swal-username" class="form-control" placeholder="Enter username" required>
                            </div>
                        </div>
                       
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="swal-kioskId" class="form-label">Kiosk ID </label>
                                <input type="text" id="swal-kioskId" class="form-control" placeholder="Enter kiosk ID" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="swal-roleId" class="form-label">Role </label>
                                <select id="swal-roleId" class="form-select" required>
                                    <option value="">Select Role</option>
                                    <?php foreach (DB::query("SELECT id, name FROM roles") as $role): ?>
                                        <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#FE5500',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Add User',
                cancelButtonText: 'Cancel',
                focusConfirm: false,
                preConfirm: () => {
                    const username = $('#swal-username').val();
                    
                    const kioskId = $('#swal-kioskId').val();
                    const roleId = $('#swal-roleId').val();
                    
                    if (!username ||  !kioskId || !roleId) {
                        Swal.showValidationMessage('All fields marked with * are required');
                        return false;
                    }
                    
                    return {
                        ...applicantData,
                        user_name: username,
                      
                        kiosk_id: kioskId,
                        role_id: roleId
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = result.value;
                    
                    $.ajax({
                        url: 'ajax_helpers/add_user_from_packet.php',
                        method: 'POST',
                        data: formData,
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.showLoading();
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    mainDataTable.ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to add user'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.error?.message || 'Failed to add user'
                            });
                        }
                    });
                }
            });
        });

        // Delete button click handler
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            
            Swal.fire({
                title: 'Confirm Delete',
                text: 'Are you sure you want to delete this record?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FE5500',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'modules/forms/delete-packets.php',
                        method: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message || 'The record has been deleted.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    mainDataTable.ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete record'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting'
                            });
                        }
                    });
                }
            });
        });

        // Real-time phone validation and formatting
        $('#phone').on('input', function() {
            let phone = $(this).val().replace(/\D/g, ''); // Remove all non-digit characters
            
            // Format the phone number as user types
            if (phone.length > 3 && phone.length <= 6) {
                phone = phone.replace(/(\d{3})(\d{0,3})/, '$1-$2');
            } else if (phone.length > 6) {
                phone = phone.replace(/(\d{3})(\d{3})(\d{0,4})/, '$1-$2-$3');
            }
            
            $(this).val(phone);
            
            // Validate the format
            const isValid = /^(\d{3}-\d{3}-\d{4}|\(\d{3}\) \d{3}-\d{4}|\d{10})$/.test(phone);
            
            if (phone && !isValid) {
                $(this).addClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
                $(this).after('<div class="invalid-feedback">Please enter a valid US phone number (10 digits)</div>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Prevent more than 10 digits from being entered
        $('#phone').on('keydown', function(e) {
            const phone = $(this).val().replace(/\D/g, '');
            if (phone.length >= 10 && e.key !== 'Backspace' && e.key !== 'Delete') {
                e.preventDefault();
            }
        });

        // Real-time email validation feedback
        $('#email').on('input', function() {
            const email = $(this).val();
            const isValid = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email);
            
            if (email && !isValid) {
                $(this).addClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
                $(this).after('<div class="invalid-feedback">Please enter a valid email address</div>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Kiosk ID filter functionality
        $('#kioskIdFilterBtn').click(function() {
            const kioskId = $('#kioskIdFilter').val().trim();
            
            // Reload DataTable with new filter
            mainDataTable.ajax.url(`ajax_helpers/ajax_packet_applicants.php?kiosk_id=${kioskId}`).load();
        });

        // Clear filter button
        $('#kioskIdClearBtn').click(function() {
            $('#kioskIdFilter').val('');
            
            // Reload DataTable without filter
            mainDataTable.ajax.url('ajax_helpers/ajax_packet_applicants.php').load();
        });
    });
</script>