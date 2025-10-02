<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crew_id = $_POST['crew_id'] ?? 0;
    $crew_name = $_POST['crew_name'] ?? '';
    $status = $_POST['status'] ?? 'active';

    if ($crew_id > 0) {
        DB::update('crew', [
            'crew_name' => $crew_name,
            'status' => $status
        ], "crew_id=%i", $crew_id);
    }
    
    // JavaScript redirect
    echo '<script>window.location.href = "index.php?route=modules/crew-management/manage_crew";</script>';
    exit;
}

$crew_id = $_GET['crew_id'] ?? null;

if (!$crew_id) {
    echo "<script>alert('".lang("invalid_crew_id")."'); window.location.href = 'index.php?route=modules/crew-management/manage_crew';</script>";
    exit;
}

$crew = DB::queryFirstRow("SELECT * FROM crew WHERE crew_id = %i", $crew_id);

if (!$crew) {
    echo "<script>alert('".lang("crew_not_found")."'); window.location.href = 'index.php?route=modules/crew-management/manage_crew';</script>";
    exit;
}

include 'includes/page-parts/header.php';
?>

<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="row w-100 mx-0">
    <div class="col-xl-12">
        <!-- Page header with breadcrumb navigation -->
        <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
            <div>
                <ol class="breadcrumb float-sm-right mt-2">
                    <!-- Home breadcrumb -->
                    <li class="breadcrumb-item">
                        <a href="index.php" style="color: #fe5500"><i class="fas fa-home me-1"></i><?php echo lang("home"); ?></a>
                    </li>
                    <!-- View Crews breadcrumb -->
                    <li class="breadcrumb-item">
                        <a href="index.php?route=modules/crew-management/manage_crew" style="color: #fe5500"><?php echo lang("admin_manage_crew"); ?></a>
                    </li>
                    <!-- Edit Crew breadcrumb -->
                    <li class="breadcrumb-item active" style="color: #fe5500"><?php echo lang("edit_crew"); ?></li>
                </ol>
            </div>
        </div>
        <div class="row1">
            <!-- Main card with orange top border -->
            <div class="card border-top" style="border-color: #FE5500 !important;">
                <div class="card-body p-4">
                    <h5 class="mb-4 fw-bold" style="color: #FE5500;"><i class="fas fa-users me-2"></i> <?php echo lang("edit_crew"); ?></h5>
                    
                    <form method="POST">
                        <input type="hidden" name="crew_id" value="<?= $crew_id ?>">
                        <div class="mb-3">
                            <label class="form-label"><?php echo lang("crew_name"); ?></label>
                            <input type="text" class="form-control" name="crew_name" value="<?= htmlspecialchars($crew['crew_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo lang("status"); ?></label>
                            <select name="status" class="form-select" required>
                                <option value="active" <?= $crew['status'] === 'active' ? 'selected' : '' ?>><?php echo lang("active"); ?></option>
                                <option value="inactive" <?= $crew['status'] === 'inactive' ? 'selected' : '' ?>><?php echo lang("inactive"); ?></option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="index.php?route=modules/crew-management/manage_crew" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> <?php echo lang("cancel"); ?>
                            </a>
                            <button type="submit" class="btn" 
                                    style="background-color: #FE5500; color: white; border: none; padding: 8px 16px; border-radius: 5px; transition: background 0.3s;"
                                    onmouseover="this.style.backgroundColor='#E04A00'"
                                    onmouseout="this.style.backgroundColor='#FE5500'">
                                <i class="fas fa-save me-1"></i> <?php echo lang("update_crew"); ?>
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4" style="border-color: #FE5500;">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold" style="color: #FE5500;"><i class="fas fa-users me-2"></i> <?php echo lang("crew_members"); ?></h6>
                        <a href="index.php?route=modules/crew-management/view_members&crew_id=<?= $crew_id ?>" class="btn" 
                           style="background-color: #FE5500; color: white; border: none; padding: 6px 12px; border-radius: 5px; transition: background 0.3s;"
                           onmouseover="this.style.backgroundColor='#E04A00'"
                           onmouseout="this.style.backgroundColor='#FE5500'">
                            <i class="fas fa-user-plus me-1"></i> <?php echo lang("manage_members"); ?>
                        </a>
                    </div>
                    
                    <?php
                    $members = DB::query("SELECT u.user_id, u.first_name, u.last_name, u.email 
                                        FROM users u 
                                        WHERE u.crew_id = %i 
                                        ORDER BY u.first_name, u.last_name", $crew_id);
                    ?>
                    
                    <?php if (count($members) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover border text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th style="background-color: #FE5500; color: white;"><?php echo lang("name"); ?></th>
                                        <th style="background-color: #FE5500; color: white;"><?php echo lang("email"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($members as $member): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></td>
                                            <td><?= htmlspecialchars($member['email']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> <?php echo lang("no_crew_members"); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

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
        .row1{
            margin-left: -20px;
            margin-right: -20px;
        }
    }
    /* Responsive styles for mobile devices */
    @media (max-width: 576px) {
        /* Button styling for mobile */
        .btn {
            padding: 4px 8px !important;
            font-size: 9px !important;
            min-width: 50px;
        }

        /* Icon spacing in buttons */
        .btn i {
            margin-right: 4px;
        }
    }

    /* Badge styles */
    .badge-active {
        background-color: #28a745;
    }
    .badge-inactive {
        background-color: #ffc107;
        color: #212529;
    }
</style>

<?php include 'includes/page-parts/footer.php'; ?>