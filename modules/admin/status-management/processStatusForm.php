<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle delete operation
    if (isset($_POST['btnDeleteStatus'])) {
        $statusId = isset($_POST['status_id']) ? $_POST['status_id'] : null;
        
        if (empty($statusId)) {
            $_SESSION['error'] = "Invalid status ID";
            echo "<script>window.location.href='index.php?route=modules/admin/status-management/view_status';</script>";
            exit();
        }
        
        try {
            DB::delete('status', "id=%i", $statusId);
            $_SESSION['success'] = "Status deleted successfully";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error deleting status: " . $e->getMessage();
        }
        
        echo "<script>window.location.href='index.php?route=modules/admin/status-management/view_status';</script>";
        exit();
    }

    // Handle add/update operation
    if (isset($_POST['btnSubmitStatus'])) {
        $statusId = isset($_POST['status_id']) ? $_POST['status_id'] : null;
        $statusTitle = isset($_POST['status_title']) ? trim($_POST['status_title']) : '';
        // Get the status value from the dropdown (either '0' or '1')
        $status = isset($_POST['status']) ? $_POST['status'] : null;

        // Validate input
        if (empty($statusTitle)) {
            $_SESSION['error'] = "Status title cannot be empty";
            $redirect = $statusId ? "add_status&status_id=$statusId" : "add_role";
            echo "<script>window.location.href='index.php?route=modules/admin/status-management/$redirect';</script>";
            exit();
        }

        try {
            if (!empty($statusId)) {
                // Update existing status
                DB::update('status', [
                    'title' => $statusTitle,
                    'status' => $status,  // Update the status field with '0' or '1'
                ], "id=%i", $statusId);
                
                $_SESSION['success'] = "Status updated successfully";
            } else {
                // Create new status
                DB::insert('status', [
                    'title' => $statusTitle,
                    'status' => $status,  // Insert the status field with '0' or '1'
                ]);
                
                $_SESSION['success'] = "Status added successfully";
            }

            echo "<script>window.location.href='index.php?route=modules/admin/status-management/view_status';</script>";
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Error saving status: " . $e->getMessage();
            $redirect = $statusId ? "add_status&status_id=$statusId" : "add_status";
            echo "<script>window.location.href='index.php?route=modules/admin/status-management/$redirect';</script>";
            exit();
        }
    }
}

// Redirect if accessed directly
echo "<script>window.location.href='index.php?route=modules/admin/status-management/view_status';</script>";
exit();
?>
