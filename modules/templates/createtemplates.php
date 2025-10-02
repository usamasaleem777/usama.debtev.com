<?php
include 'includes/page-parts/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['short_name'])) {
        $short_name = $_POST['short_name'];
    } else {
        $short_name = '';
    }

    if (isset($_POST['message_text'])) {
        $message_text = $_POST['message_text'];
    } else {
        $message_text = '';
    }

    if (isset($_POST['message_type'])) {
        $message_type = $_POST['message_type'];
    } else {
        $message_type = '';
    }

    DB::insert('templates', [
        'short_name'   => $short_name,
        'message_text' => $message_text,
        'message_type' => $message_type
    ]);

    if (isset($_POST['save_close'])) {
        echo "<script>window.location.href = 'index.php?route=modules/templates';</script>";
        exit;
    } elseif (isset($_POST['save_resume'])) {
        echo "<script>window.location.href = 'index.php?route=modules/templates/createtemplates';</script>";
        exit;
    }
}

?>

    <style>
        .btn-orange {
            background-color: #FE5500;
            color: white;
        }
        .btn-orange:hover {
            background-color: #e44b00;
        }
        
        /* General mobile styles */
        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }
            
            .page-header {
                margin-top: 0.5rem;
                margin-bottom: 0.5rem;
            }
            
            .breadcrumb {
                font-size: 0.7rem;
                padding: 0.3rem 0;
                flex-wrap: nowrap;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .breadcrumb-item {
                display: inline-block;
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .card-body {
                padding: 0.8rem;
            }
            
            h5 {
                font-size: 1.1rem;
                margin-bottom: 1rem;
            }
            
            .form-control, .form-select {
                font-size: 0.8rem;
                padding: 0.4rem 0.75rem;
            }
            
            .form-label {
                font-size: 0.85rem;
                margin-bottom: 0.3rem;
            }
            
            .btn {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem;
            }
            
            .placeholder-info {
                font-size: 0.75rem;
                padding: 0.5rem;
            }
            
            .placeholder-info p {
                margin-bottom: 0.5rem;
            }
            
            textarea.form-control {
                min-height: 120px;
            }
        }
        
        /* Specific styles for 360px and below */
        @media (max-width: 360px) {
            .breadcrumb {
                font-size: 0.7rem;
            }
            .row1{
                margin-left: -20px  !important;
                margin-right: -20px  !important;
            }
            .card-body {
                padding: 0.6rem;
            }
            
            h5 {
                font-size: 1rem;
            }
            
            .form-control, .form-select {
                font-size: 0.75rem;
            }
            
            .btn-group-mobile {
                display: flex;
                flex-direction: column;
                width: 100%;
                gap: 0.3rem;
            }
            
            .btn-group-mobile .btn {
                width: 100%;
                margin: 0.1rem 0;
            }
            
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .d-flex.justify-content-between > a {
                width: 100%;
                text-align: center;
            }
        }
    </style>
    <!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<div class="container mt-2">
    <!-- Page header with breadcrumb navigation -->
    <div class="page-header d-flex align-items-center justify-content-end mt-1 mb-1">
        <div style="margin-top: 15px;">
            <ol class="breadcrumb float-sm-right mt-1">
                <!-- Home breadcrumb -->
                <li class="breadcrumb-item">
                    <a href="index.php" style="color: #fe5500">
                    <i class="fas fa-home me-1"></i><?php echo lang("user_home"); ?>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="index.php?route=modules/templates/templates" style="color: #fe5500"><?php echo lang("templates_Tamplates"); ?></a>
                </li>
               
                <li class="breadcrumb-item active" style="color: #fe5500"><?php echo lang("templates_Create_Templates"); ?></li>
            </ol>
        </div>
    </div>
<div class="row1">
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3"><i class="bi bi-chat-dots"></i><?php echo lang("templates_Create_Templates"); ?></h5>
            <form method="POST">
                <div class="mb-2">
                    <label class="form-label"><?php echo lang("templates_short_name:"); ?></label>
                    <input type="text" class="form-control" name="short_name" required>
                </div>
                <div class="mb-2">
                    <label class="form-label"><?php echo lang("templates_massage_text:"); ?></label>
                    <textarea class="form-control" name="message_text" rows="4" required></textarea>
                </div>
                <div class="p-2 mb-2 bg-light border rounded placeholder-info">
                    <p><strong>{full_name}</strong> –<?php echo lang("templates_ Will_be_replaced_with_applicant's_full_name."); ?></p>
                    <p><strong>{job_applied}</strong> – <?php echo lang("templates_Will_be_replaced_with_job_position."); ?></p>
                    <p><strong>{token}</strong> – <?php echo lang("templates_Will_be_replaced_with_unique_token."); ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo lang("templates_massage_Type:"); ?></label>
                    <select class="form-select" name="message_type" required>
                        <option value="Whatsapp"><?php echo lang("templates_whatsApp"); ?></option>
                        <option value="Email"><?php echo lang("templates_Email"); ?></option>
                    </select>
                </div>
                <div class="d-flex justify-content-between flex-wrap">
                    <a href="template_list.php" class="btn btn-orange mb-1"><?php echo lang("templates_close"); ?></a>
                    <div class="btn-group-mobile">
                        <button type="submit" name="create_close" class="btn btn-orange mb-1"><?php echo lang("templates_Create_&_close"); ?></button>
                        <button type="submit" name="create_resume" class="btn btn-orange mb-1"><?php echo lang("templates_Create_&_Resume"); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>