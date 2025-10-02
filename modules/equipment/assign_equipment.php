<?php
// Check user permissions
if ($_SESSION['role_id'] == $admin_role || $_SESSION['role_id'] == $tool_manager) {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo lang("craftman"); ?></title>

        <!-- Styles -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

        <style>
            /* Signature Styles */
            .signature-image {
                max-height: 40px;
                max-width: 150px;
                object-fit: contain;
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                padding: 2px;
                border-radius: 4px;
            }

            .signature-cell {
                vertical-align: middle;
                min-width: 160px;
            }

            .modal-header .signature-container {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            :root {
                --primary-color: #fe5500;
                --hover-color: #d94600;
            }

            .btn-primary,
            .bg-primary,
            .checkin-btn {
                background-color: var(--primary-color) !important;
                border-color: var(--primary-color) !important;
            }

            .btn-primary:hover,
            .checkin-btn:hover {
                background-color: var(--hover-color) !important;
                border-color: var(--hover-color) !important;
            }

            .card-header {
                background-color: var(--primary-color) !important;
                color: white !important;
            }

            .nav-tabs .nav-link.active {
                border-color: var(--primary-color);
                color: var(--primary-color);
            }

            .form-check-input:checked {
                background-color: var(--primary-color) !important;
                border-color: var(--primary-color) !important;
            }

            /* .img-thumbnail {
                border: 2px solid var(--primary-color);
            } */

            .table-hover tbody tr:hover {
                background-color: rgba(254, 85, 0, 0.1);
            }

            #assignEquipmentModal .form-check-input {
                width: 2.5em;
                height: 2.5em;
            }
        </style>
    </head>

    <body>
        <div class="main-content app-content mt-0">
            <div class="side-app">
                <div class="main-container container-fluid">

                    <!-- Header Section -->
                    <div class="page-header d-flex justify-content-between align-items-center">
                        <h2 class="page-title" style="color: var(--primary-color);">
                            <?php echo lang("Equipments_tool_assigned"); ?>
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.php" style="color: var(--primary-color);">
                                        <i class="fas fa-home me-1"></i><?php echo lang("user_home"); ?>
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <?php echo lang("Equipments_tool"); ?>
                                </li>
                            </ol>
                        </nav>
                    </div>


                    <!-- KIOSK ID Button -->
                    <div class="row mb-4" style="margin-top: 150px !important;">
                        <div class="col-12 text-center">
                            <button class="btn btn-lg btn-primary" data-bs-toggle="modal" data-bs-target="#kioskModal">
                                <i class="fas fa-id-card me-2"></i>
                                Enter Kiosk Id
                            </button>
                        </div>
                    </div>


                    <!-- KIOSK ID Modal -->
                    <div class="modal fade" id="kioskModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-search me-2"></i>
                                        Enter Kiosk Id
                                    </h5>
                                    <div class="signature-container ms-auto">
                                        <small class="text-muted"><?php echo lang("authorized_signature"); ?></small>
                                        <img id="modalSignature" src="" class="signature-image"
                                            style="max-height: 40px; margin-left: 15px; display: none;">
                                    </div
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                        <input type="text" class="form-control" id="kioskIdInput" placeholder="KIOSK ID">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <?php echo lang("close"); ?>
                                    </button>
                                    <button type="button" class="btn btn-primary" id="fetchUserBtn">
                                        <i class="fas fa-search me-2"></i>
                                        <?php echo lang("Fetch User"); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div id="userInfoSection" class="card mb-4 shadow" style="display: none;">
                        <div class="card-header">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-user-cog me-2 text-white"></i>
                                <?php echo lang("Craftman Info"); ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center">
                                    <img id="userProfilePic" src=""
                                        class="img-thumbnail rounded-circle"
                                        style="width: 150px; height: 150px;">
                                </div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-primary">
                                                    <?php echo lang("tools_id"); ?>
                                                </label>
                                                <p class="fs-5" id="userId"></p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-primary">
                                                    <?php echo lang("tools_KIOSK_ID"); ?>
                                                </label>
                                                <p class="fs-5" id="userKioskId"></p>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-primary">
                                                    <?php echo lang("crew"); ?>
                                                </label>
                                                <p class="fs-5" id="crew"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-primary">
                                                    <?php echo lang("tools_Username"); ?>
                                                </label>
                                                <p class="fs-4" id="userName"></p>
                                            </div>

                                            <section class="profile-section mt-4">
                                                <h5 class="fw-bold text-primary">
                                                    <i class="fas fa-signature me-2"></i>
                                                    <?php echo lang("formview_applicaion_signature"); ?>
                                                </h5>
                                                <div class="signature-container bg-light p-3 rounded">
                                                    <div id="signatureImage" class="text-center">
                                                        <!-- Signature will be dynamically inserted here -->
                                                    </div>

                                                </div>
                                            </section>
                                            <button class="btn btn-primary btn-lg assign-tools-btn">
                                                <i class="fas fa-tools me-2"></i>
                                                <?php echo lang("Assign Equipment"); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assignment History -->
                    <div id="assignmentHistorySection" class="card shadow" style="display: none;">
                        <div class="card-header bg-dark">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-history me-2 text-white"></i>
                                <?php echo lang("History"); ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th><?php echo lang("equipment_Equipment_Name"); ?></th>
                                            <th><?php echo lang("equipment_Picture"); ?></th>
                                            <th><?php echo lang("Assign Date"); ?></th>
                                            <th><?php echo lang("actions"); ?></th>
                                            <th><?php echo lang("signature"); ?></th> <!-- New Column -->

                                        </tr>
                                    </thead>
                                    <tbody id="historyTableBody" class="align-middle">
                                        <!-- Dynamic content -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Assign Tools Modal -->
                    <div class="modal fade" id="assignEquipmentModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form id="assignEquipmentForm">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fas fa-tools me-2"></i>
                                            <?php echo lang("Assign Tools"); ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="user_id" id="assignUserId">

                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th><?php echo lang("equipment_Equipment_Name"); ?></th>
                                                        <th><?php echo lang("picture"); ?></th>
                                                        <th><?php echo lang("Equipment Type"); ?></th>
                                                        <th><?php echo lang("Serial Number"); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $equipments = DB::query("SELECT id, equipment_name, image_path, serial_number, equipment_type FROM equipment ORDER BY equipment_name ASC");
                                                    foreach ($equipments as $tool): ?>
                                                        <tr class="tool-row align-middle">
                                                            <td class="text-center">
                                                                <input type="checkbox" class="form-check-input equipment-checkbox"
                                                                    data-equipment-id="<?= $tool['id'] ?>"
                                                                    data-serial-number="<?= htmlspecialchars($tool['serial_number']) ?>">
                                                            </td>
                                                            <td><?= htmlspecialchars($tool['equipment_name']) ?></td>
                                                            <td>
                                                                <img src="uploads/equipment/<?= $tool['image_path'] ?>"
                                                                    class="img-thumbnail" style="width: 60px; height: 60px;">
                                                            </td>
                                                            <td class="available-quantity"><?= $tool['equipment_type'] ?></td>
                                                            <td class="available-quantity"><?= $tool['serial_number'] ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <?php echo lang("close"); ?>
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            <?php echo lang("save_changes"); ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(document).ready(function() {
                let currentUserId = null;
                let currentUser = null;

                // Fetch user by KIOSK ID
                $('#fetchUserBtn').click(function() {
                    const kioskId = $('#kioskIdInput').val().trim();
                    if (!kioskId) {
                        showAlert('warning', '<?php echo lang("enter_kiosk_id_warning"); ?>');
                        return;
                    }

                    toggleLoading(true);

                    $.ajax({
                        url: 'ajax_helpers/ajax_get_user_by_kiosk.php',
                        method: 'POST',
                        data: {
                            kiosk_id: kioskId
                        },
                        success: function(response) {
                            if (response.success) {
                                currentUserId = response.user.user_id;
                                currentUser = response.user;
                                displayUserInfo(response.user);
                                loadAssignmentHistory();

                                // Proper modal dismissal
                                $('#kioskModal').modal('hide');
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                                $('#kioskModal').modal('hide').on('hidden.bs.modal', function() {
                                    $('body').css('overflow', 'auto');
                                });
                            } else {
                                showAlert('error', response.message);
                            }
                        },
                        error: function(xhr) {
                            showAlert('error', '<?php echo lang("fetch_error"); ?>');
                        },
                        complete: () => toggleLoading(false)
                    });
                });

                function refreshToolQuantities() {
                    $.ajax({
                        url: 'ajax_helpers/ajax_get_equipment.php',
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                $('.tool-row').each(function() {
                                    const $row = $(this);
                                    const toolName = $row.find('td:nth-child(2)').text().trim();
                                    const tool = response.equipment.find(t => t.equipment_name === toolName);
                                    if (tool) {
                                        $row.find('.available-quantity').text(tool.equipment_type);
                                    } else {
                                        $row.find('.available-quantity').text('<?php echo lang("not_available"); ?>');
                                    }
                                });
                            } else {
                                showAlert('error', response.message);
                            }
                        }
                    });
                }

                // Load assignment history
                function loadAssignmentHistory() {
                    $('#historyTableBody').html(`
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="spinner-border text-primary"></div>
                    </td>
                </tr>
            `);

                    $.ajax({
                        url: 'ajax_helpers/ajax_user_equipment_history.php',
                        method: 'POST',
                        data: {
                            user_id: currentUserId
                        },
                        success: function(response) {
                            $('#historyTableBody').empty();
                            if (response.success && response.data.length > 0) {
                                response.data.forEach(assignment => {
                                    $('#historyTableBody').append(createHistoryRow(assignment));
                                });
                            } else {
                                $('#historyTableBody').html(`
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    <?php echo lang("no_history"); ?>
                                </td>
                            </tr>
                        `);
                            }
                            $('#userInfoSection, #assignmentHistorySection').show();
                        },
                        error: function() {
                            $('#historyTableBody').html(`
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        <?php echo lang("load_error"); ?>
                    </td>
                </tr>
            `);
                        }
                    });
                }

                // Handle Assign Tools button click
                $(document).on('click', '.assign-tools-btn', function() {
                    if (!currentUserId) {
                        showAlert('warning', '<?php echo lang("no_user_selected"); ?>');
                        return;
                    }
                    $('#assignEquipmentModal').modal('show');
                });

                // Handle check-in actions
                // Handle check-in actions - Updated version
                $(document).on('click', '.checkin-btn', function() {
                    const equipmentId = $(this).data('equipment-id');
                    const serialNumber = $(this).data('serial-number');
                    const assignmentId = $(this).data('assignment-id'); // Make sure this is passed in your history row

                    Swal.fire({
                        title: '<?php echo lang("confirm_checkin"); ?>',
                        text: `<?php echo lang("confirm_checkin_text"); ?> ${serialNumber}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#fe5500',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<?php echo lang("confirm"); ?>'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'ajax_helpers/ajax_checkin_equipment.php',
                                method: 'POST',
                                data: {
                                    assignment_id: assignmentId,
                                    equipment_id: equipmentId,
                                    serial_number: serialNumber,
                                    user_id: currentUserId
                                },
                                success: function(response) {
                                    if (response.success) {
                                        showAlert('success', response.message);
                                        loadAssignmentHistory();
                                    } else {
                                        showAlert('error', response.message);
                                    }
                                },
                                error: function() {
                                    showAlert('error', '<?php echo lang("checkin_error"); ?>');
                                }
                            });
                        }
                    });
                });

                function fetchUserDetails(userId) {
                    $.ajax({
                        url: 'ajax_helpers/ajax_get_user.php',
                        method: 'POST',
                        data: {
                            user_id: userId
                        },
                        success: function(response) {
                            if (response.success) {
                                currentUser = response.user;
                                displayUserInfo(response.user);
                            }
                        }
                    });
                }

                // Handle equipment assignment
                $('#assignEquipmentForm').submit(function(e) {
                    e.preventDefault();

                    // Get selected equipment
                    const selectedEquipment = [];
                    $('.tool-row').each(function() {
                        const $row = $(this);
                        if ($row.find('.form-check-input').is(':checked')) {
                            selectedEquipment.push({
                                id: $row.find('.form-check-input').data('equipment-id'),
                                serial_number: $row.find('.form-check-input').data('serial-number')
                            });
                        }
                    });

                    if (selectedEquipment.length === 0) {
                        showAlert('warning', '<?php echo lang("select_equipment_warning"); ?>');
                        return;
                    }

                    $.ajax({
                        url: 'ajax_helpers/user_assign_equipment.php',
                        method: 'POST',
                        data: {
                            user_id: currentUserId,
                            equipment: selectedEquipment, // Array of objects {id, serial_number}
                            signature: currentUser.signature || ''
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#assignEquipmentModal').modal('hide');
                                showAlert('success', response.message);
                                loadAssignmentHistory();
                            } else {
                                showAlert('error', response.message);
                            }
                        }
                    });
                });
                // Helper functions
                function displayUserInfo(user) {
                    currentUser = user;
                    const defaultAvatar = 'uploads/profileImages/avatar5.png';
                    const profilePicPath = user.picture ?
                        user.picture :
                        defaultAvatar;

                    $('#userProfilePic').attr('src', profilePicPath).on('error', function() {
                        $(this).attr('src', defaultAvatar);
                    });
                    $('#userId').text(user.user_id);
                    $('#userKioskId').text(user.kioskID);
                    $('#userName').text(user.user_name);
                    $('#crew').text(user.crew_name || '<?php echo lang("not_assigned"); ?>');

                    // Handle signature display
                    const $sigContainer = $('#signatureImage');
                    $sigContainer.empty();

                    if (user.signature && user.signature.startsWith('data:image')) {
                        $sigContainer.html(`
                            <img src="${user.signature}" 
                                 alt="User Signature" 
                                 class="img-fluid"
                                 style="max-height: 80px; background: transparent;">
                        `);
                    } else {
                        $sigContainer.html(`
                            <div class="text-muted py-3">
                                <i class="fas fa-signature fa-2x"></i>
                                <div><?php echo lang("formview_no_signature_found"); ?></div>
                            </div>
                        `);
                    }
                }

                $(document).on('show.bs.modal', '#assignEquipmentModal', function() {
                    refreshToolQuantities();
                    if (currentUser && currentUser.signature) {
                        $('#modalSignature').attr('src', currentUser.signature).show();
                    } else {
                        $('#modalSignature').hide();
                    }
                });

                function createHistoryRow(assignment) {
                    const checkoutDate = new Date(assignment.checkout_at).toLocaleDateString();
                    const isCheckedIn = assignment.checkin_at;

                    const checkinDate = isCheckedIn ?
                        new Date(assignment.checkin_at).toLocaleDateString() :
                        `<button class="btn btn-sm btn-primary checkin-btn"
            data-equipment-id="${assignment.equipment_id}"
            data-serial-number="${assignment.serial_number}"
            data-assignment-id="${assignment.id}">
            <i class="fas fa-sign-in-alt me-2"></i>
            <?php echo lang("checkin"); ?>
        </button>`;

                    const signatureCell = !isCheckedIn ?
                        `<td class="signature-cell">
            ${currentUser.signature ? 
                `<img src="${currentUser.signature}" class="signature-image">` : 
                `<span class="text-muted">${lang('no_signature_available')}</span>`
            }
        </td>` :
                        `<td class="text-muted"><?php echo lang("returned"); ?></td>`;

                    return `
        <tr>
            <td>${assignment.equipment_name}</td>
            <td>
                <img src="uploads/equipment/${assignment.equipment_picture}" 
                     class="img-thumbnail" 
                     style="width: 50px; height: 50px;">
            </td>
            <td>${checkoutDate}</td>
            <td>${checkinDate}</td>
            ${signatureCell}
        </tr>
    `;
                }

                function toggleLoading(loading) {
                    const $btn = $('#fetchUserBtn');
                    if (loading) {
                        $btn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm"></span>
            <?php echo lang("loading"); ?>
        `);
                    } else {
                        $btn.prop('disabled', false).html(`
            <i class="fas fa-search me-2"></i>
            <?php echo lang("fetch_user"); ?>
        `);
                    }
                }

                function showAlert(icon, text) {
                    Swal.fire({
                        icon: icon,
                        text: text,
                        confirmButtonColor: '#fe5500',
                        timer: 3000,
                        showConfirmButton: false,
                        position: 'center'
                    });
                }

                function gatherFormData() {
                    const data = {
                        user_id: currentUserId,
                        equipments: {}
                    };

                    $('.equipment-row').each(function() {
                        const $row = $(this);
                        const equipmentId = $row.find('.equipment-checkbox').data('equipment-id');
                        const selected = $row.find('.form-check-input').prop('checked');

                        if (selected) {
                            data.equipments[equipmentId] = {
                                selected: 1
                            };
                        }
                    });

                    return data;
                }
            });
        </script>

    </body>

    </html>

<?php
} else {
    echo "<div class='alert alert-danger m-3'>" . lang("no_permission") . "</div>";
}
?>