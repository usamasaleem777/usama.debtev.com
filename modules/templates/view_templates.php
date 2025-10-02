<?php
include 'includes/page-parts/header.php';

if (isset($_GET['del']) && $_GET['del'] === "yes" && isset($_GET['del_id'])) {
    try {
        DB::query("DELETE FROM templates WHERE id = %i", $_GET['del_id']);
        $_SESSION['flash_message'] = 'Template deleted successfully';
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Error deleting template: ' . $e->getMessage();
    }
    
    echo '<script>window.location.href = "index.php?route=modules/templates/view_templates";</script>';
    exit;
}

$templates = DB::query("SELECT * FROM templates");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>View Templates</title>
    <style>
        .btn-orange {
            background-color: #FE5500;
            color: white;
        }
        .btn-orange:hover {
            background-color: #e44b00;
        }
        .truncate {
            max-width: 500px;
            max-height: 100px;
            overflow-y: auto;
            overflow-x: hidden;
            border: 1px solid #ddd;
            padding: 5px;
            white-space: normal;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div class="container mt-4">
    <!-- Breadcrumb -->
    <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
                <div style="margin-top: 15px;">
                    <ol class="breadcrumb float-sm-right mt-2">
                        <!-- Home breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="index.php" style="color: #fe5500"><i
                                    class="fas fa-home me-1"></i><?php echo lang("role_home"); ?></a>
                        </li>
                        <!-- View position breadcrumb -->
                        <li class="breadcrumb-item">
                            <a href="#" style="color: #fe5500"><?php echo lang("template_All_Templates"); ?></a>
                        </li>
                    </ol>
                </div>
            </div>

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-4"><i class="bi bi-chat-dots"></i> <?php echo lang("template_All_Templates"); ?></h5>

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><?php echo lang("template_ID"); ?></th>
                            <th><?php echo lang("template_short_name"); ?></th>
                            <th><?php echo lang("template_Message_Text"); ?></th>
                            <th><?php echo lang("template_Message_Type"); ?></th>
                            <th><?php echo lang("template_Actions"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($templates as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['short_name']) ?></td>
                                <td class="truncate text-start"><?= nl2br(htmlspecialchars($row['message_text'])) ?></td>
                                <td><?= $row['message_type'] ?></td>
                                <td>
                                    <a href="index.php?route=modules/templates/edittemplate&id=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-orange me-1">
                                        <i class="fas fa-edit"></i> <?php echo lang("template_edit"); ?>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-btn" 
                                            data-id="<?= $row['id'] ?>">
                                        <i class="fas fa-trash-alt"></i> <?php echo lang("template_delete"); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($templates)): ?>
                            <tr><td colspan="5"><?php echo lang("template_No_templates_found"); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show flash messages if they exist
    <?php if (isset($_SESSION['flash_message'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?= addslashes($_SESSION['flash_message']); ?>',
            confirmButtonColor: '#FE5500'
        });
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?= addslashes($_SESSION['error_message']); ?>',
            confirmButtonColor: '#FE5500'
        });
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            const id = this.dataset.id;
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FE5500',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `index.php?route=modules/templates/view_templates&del=yes&del_id=${id}`;
                }
            });
        });
    });
});
</script>
</body>
</html>