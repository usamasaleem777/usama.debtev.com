<?php
include 'includes/page-parts/header.php';

// Fetch edit requests from craft_contracting table
$requests = DB::query("SELECT * FROM craft_contracting WHERE is_locked = 1 AND request_edit = 1");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contract_id = $_POST['contract_id'] ?? 0;
    $action = $_POST['action'] ?? '';
    
    if ($contract_id > 0 && in_array($action, ['accept', 'decline'])) {
        if ($action === 'accept') {
            // Accept the edit request - unlock the contract
            DB::update('craft_contracting', [
                'is_locked' => 0,
                'request_edit' => 0,
                'last_updated' => date('Y-m-d H:i:s'),
                'updated_by' => $_SESSION['user_id'] // Assuming you have user session
            ], "id=%i", $contract_id);
            
            // Add any additional logic for accepted requests here
            
        } else {
            // Decline the edit request - keep locked but reset request flag
            DB::update('craft_contracting', [
                'request_edit' => 0,
                'last_updated' => date('Y-m-d H:i:s'),
                'updated_by' => $_SESSION['user_id']
            ], "id=%i", $contract_id);
            
            // Add any additional logic for declined requests here
        }
        
        // Redirect to refresh the page
        echo '<script>window.location.href = "index.php?route=modules/contracts/edit_requests";</script>';
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
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .action-btns {
        white-space: nowrap;
    }
    @media (max-width: 360px) {
        .row1 {
            margin-left: -20px !important;
            margin-right: -20px !important;
        }
    }
</style>

<div class="container mt-4">
    <!-- Breadcrumb -->
    <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
        <div>
            <ol class="breadcrumb float-sm-right mt-2">
                <li class="breadcrumb-item"><a href="index.php" style="color: #fe5500"><i class="fas fa-home me-1"></i><?php echo lang("user_home"); ?></a></li>
                <li class="breadcrumb-item active" style="color: #fe5500"><?php echo lang("contract_edit_requests"); ?></li>
            </ol>
        </div>
    </div>
    
    <div class="row1">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-4"><i class="fas fa-edit me-2"></i><?php echo lang("contract_edit_requests"); ?></h5>
                
                <?php if (count($requests) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo lang("contract_id"); ?></th>
                                    <th><?php echo lang("first_name"); ?></th>
                                    <th><?php echo lang("last_name"); ?></th>
                                    <th><?php echo lang("email"); ?></th>
                                    <th><?php echo lang("request_date"); ?></th>
                                    <th><?php echo lang("actions"); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($request['id']) ?></td>
                                        <td><?= htmlspecialchars($request['first_name']) ?></td>
                                        <td><?= htmlspecialchars($request['last_name']) ?></td>
                                        <td><?= htmlspecialchars($request['email']) ?></td>
                                        <td><?= date('m/d/Y H:i', strtotime($request['request_date'])) ?></td>
                                        <td class="action-btns">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="contract_id" value="<?= $request['id'] ?>">
                                                <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check"></i> <?php echo lang("accept"); ?>
                                                </button>
                                                <button type="submit" name="action" value="decline" class="btn btn-danger btn-sm ms-1">
                                                    <i class="fas fa-times"></i> <?php echo lang("decline"); ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?php echo lang("no_edit_requests"); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/page-parts/footer.php'; ?>