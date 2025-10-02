<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- SweetAlert CSS for beautiful alert popups -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<div class="row w-100 mx-0">
    <div class="col-xl-12">
        <!-- Page header with breadcrumb navigation -->
        <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
            <div>
                <ol class="breadcrumb float-sm-right mt-2">
                    <!-- Home breadcrumb -->
                    <li class="breadcrumb-item">
                        <a href="index.php" style="color: #fe5500"><i
                                class="fas fa-home me-1"></i><?php echo lang("role_home"); ?></a>
                    </li>
                    <!-- View equipment breadcrumb -->
                    <li class="breadcrumb-item">
                        <a href="index.php?route=modules/admin/equipment-management/view_equipment"
                            style="color: #fe5500"><?php echo lang("equipment_Equipment"); ?></a>
                    </li>
                </ol>
            </div>
        </div>
        <div class="row1">
            <!-- Main card with orange top border -->
            <div class="card border-top" style="border-color: #FE5500 !important;">
                <div class="card-body p-4">
                    <!-- Error message display (if any) -->
                    <?php if (isset($_SESSION['error'])) { ?>
                        <div class="alert alert-danger">
                            <i
                                class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['error']);
                                                                            unset($_SESSION['error']); ?>
                        </div>
                    <?php } ?>

                    <!-- Success message display (if any) -->
                    <?php if (isset($_SESSION['success'])) { ?>
                        <div class="alert alert-success">
                            <i
                                class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success']);
                                                                        unset($_SESSION['success']); ?>
                        </div>
                    <?php } ?>

                    <!-- Table header section -->
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: #FE5500;"><?php echo lang("equipment_all_Equipment"); ?></h5>
                        </div>
                    </div>

                    <!-- Equipment table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover border text-nowrap mb-0 datatable mt-3 w-100"
                            id="equipment_table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="background-color: #FE5500; color: white;">
                                        <?php echo lang("role_Sr"); ?>
                                    </th>
                                    <th style="background-color: #FE5500; color: white;"><?php echo lang("equipment_Image"); ?></th>
                                    <th style="background-color: #FE5500; color: white;">
                                        <?php echo lang("equipment_Equipment_Name"); ?>
                                    </th>
                                    <th style="background-color: #FE5500; color: white;">
                                        <?php echo lang("equipment_Serial_Number"); ?>
                                    </th>
                                    <th style="background-color: #FE5500; color: white;">
                                        <?php echo lang("equipment_Equipment_Type"); ?>
                                    </th>
                                    <th style="background-color: #FE5500; color: white;">
                                        <?php echo lang("equipment_Status"); ?>
                                    </th>
                                    <th class="text-center" style="background-color: #FE5500; color: white;">
                                        <?php echo lang("role_action"); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    // Fetch all equipment from database
                                    $equipmentList = DB::query("SELECT * FROM `equipment`");

                                    if (!empty($equipmentList)) {
                                        foreach ($equipmentList as $itr => $equipment) {
                                ?>
                                            <tr>
                                                <td class="text-center"><?php echo ($itr + 1); ?></td>
                                                <td>
                                                    <?php if (!empty($equipment['image_path'])): ?>
                                                        <img src="uploads/equipment/<?php echo htmlspecialchars($equipment['image_path']); ?>"
                                                            alt="Equipment Image" width="60" height="60" style="object-fit: cover; border-radius: 5px;" />
                                                    <?php else: ?>
                                                        <span class="text-muted">No Image</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($equipment['equipment_name']); ?></td>
                                                <td><?php echo htmlspecialchars($equipment['serial_number']); ?></td>
                                                <td>
                                                    <?php 
                                                    switch($equipment['equipment_type']) {
                                                        case 'heavy': echo lang('equipment_Heavy_Machinery'); break;
                                                        case 'light': echo lang('equipment_Light_Equipment'); break;
                                                        case 'vehicle': echo lang('equipment_Vehicle'); break;
                                                        default: echo lang('equipment_Other');
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    switch($equipment['status']) {
                                                        case 'available': 
                                                            echo '<span class="badge bg-success">'.lang('equipment_Available').'</span>'; 
                                                            break;
                                                        case 'in_use': 
                                                            echo '<span class="badge bg-primary">'.lang('equipment_In_Use').'</span>'; 
                                                            break;
                                                        case 'maintenance': 
                                                            echo '<span class="badge bg-warning">'.lang('equipment_Maintenance').'</span>'; 
                                                            break;
                                                        default: 
                                                            echo '<span class="badge bg-secondary">'.lang('equipment_Out_of_Service').'</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <!-- Edit button -->
                                                    <a href="index.php?route=modules/equipment/edit_equipment&id=<?php echo $equipment['id']; ?>"
                                                        class="btn edit_btn" title="<?php echo lang("equipment_Edit"); ?>"
                                                        style="background-color: #FE5500; color: white; border: none; padding: 8px 16px; border-radius: 5px; transition: background 0.3s;"
                                                        onmouseover="this.style.backgroundColor='#E04A00'"
                                                        onmouseout="this.style.backgroundColor='#FE5500'">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <!-- Delete button -->
                                                    <button type="button" onclick="confirmDeleteEquipment(<?php echo $equipment['id']; ?>)"
                                                        class="btn btn-danger ms-2" title="<?php echo lang("equipment_Delete"); ?>"
                                                        style="background-color: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 5px; transition: background 0.3s;"
                                                        onmouseover="this.style.backgroundColor='#c82333'"
                                                        onmouseout="this.style.backgroundColor='#dc3545'">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="7" class="text-center"><i
                                                    class="fas fa-info-circle me-2"></i><?php echo lang("equipment_No_Equipment_Found"); ?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                } catch (Exception $e) {
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-danger"><i
                                                class="fas fa-exclamation-triangle me-2"></i><?php echo lang("role_error_loading_record"); ?>
                                            <?php echo htmlspecialchars($e->getMessage()); ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS styling for the page -->
<style>
    /* Style for active pagination item */
    .pagination .page-item.active .page-link {
        background-color: #fe5500 !important;
        border-color: #fe5500 !important;
    }

    /* Style for pagination links */
    .pagination .page-link {
        color: black !important;
    }

    @media (max-width: 360px) {
        .row1 {
            margin-left: -20px;
            margin-right: -20px;
        }
    }

    /* Responsive styles for mobile devices */
    @media (max-width: 576px) {
        #equipment_table_length {
            display: inline-block !important;
            float: left;
            font-size: 12px;
            margin-bottom: 5px;
        }

        #equipment_table_filter {
            display: inline-block !important;
            float: right;
            text-align: right;
        }

        #equipment_table_filter input[type="search"],
        #equipment_table_length select {
            width: 60% !important;
            font-size: 12px;
            padding: 6px;
            border-radius: 5px;
        }

        .edit_btn,
        .btn-danger {
            padding: 4px 8px !important;
            font-size: 12px !important;
            min-width: 70px;
        }

        .edit_btn i,
        .btn-danger i {
            margin-right: 4px;
        }
    }

    /* Styles for larger screens */
    @media (min-width: 577px) {
        #equipment_table_length,
        #equipment_table_filter {
            display: block !important;
            float: none !important;
            margin-bottom: 10px;
        }

        #equipment_table_filter input[type="search"],
        #equipment_table_length select {
            width: auto;
            font-size: 14px;
            border-radius: 5px;
        }
    }
</style>

<!-- JavaScript libraries -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        const table = $('#equipment_table').DataTable({
            responsive: true,
            "columnDefs": [{
                "orderable": false,
                "targets": 6 // Make action column non-sortable
            }],
            "language": {
                "emptyTable": "<?php echo lang('equipment_No_Equipment_Found'); ?>"
            },
            "initComplete": function() {
                if (window.innerWidth <= 576) {
                    setTimeout(function() {
                        $('#equipment_table_length').appendTo('#mobileLengthContainer').css(
                            'display', 'inline-block');
                        $('#equipment_table_filter').appendTo('#mobileSearchContainer').css(
                            'display', 'inline-block');
                        $('#equipment_table_filter label').css('float', 'right');
                    }, 100);
                }
            }
        });

        $(window).resize(function() {
            if (window.innerWidth <= 576) {
                $('#equipment_table_filter input[type="search"]').css('width', '100%');
                $('#equipment_table_length').css('float', 'left').css('width', '30%');
                $('#equipment_table_filter label').css('float', 'right');
            } else {
                $('#equipment_table_filter input[type="search"]').css('float', 'none').css('width',
                    'auto');
                $('#equipment_table_length').css('float', 'none').css('width', 'auto');
                $('#equipment_table_filter label').css('margin-left', '10px');
            }
        }).trigger('resize');
    });

    // Function to confirm equipment deletion
    function confirmDeleteEquipment(equipmentId) {
        Swal.fire({
            title: "<?php echo lang('equipment_Delete_Confirmation_Title'); ?>",
            text: "<?php echo lang('equipment_Delete_Confirmation_Text'); ?>",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FE5500",
            cancelButtonColor: "#d33",
            confirmButtonText: "<?php echo lang('role_yes_delete'); ?>",
            cancelButtonText: "<?php echo lang('role_cancel'); ?>"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'index.php?route=modules/equipment/processEquipmentForm',
                    type: 'POST',
                    data: {
                        id: equipmentId,
                        btnDeleteEquipment: true
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "<?php echo lang('equipment_Deleted'); ?>",
                            text: "<?php echo lang('equipment_Delete_Success'); ?>",
                            icon: "success",
                            confirmButtonColor: "#FE5500"
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: "<?php echo lang('equipment_Error'); ?>",
                            text: "<?php echo lang('equipment_Delete_Error'); ?>: " + error,
                            icon: "error",
                            confirmButtonColor: "#FE5500"
                        });
                    }
                });
            }
        });
    }
</script>