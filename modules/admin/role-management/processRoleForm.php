<?php
//TODO: Add Security
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle delete operation
    if (isset($_POST['btnDeleteRole'])) {
        $roleId = isset($_POST['record_id']) ? $_POST['record_id'] : null;
        
        if (empty($roleId)) {
            $_SESSION['error'] = "Invalid role ID";
            echo "<script>window.location.href='index.php?route=modules/admin/role-management/view_role';</script>";
            exit();
        }
        
        try {
            // Check if role is assigned to any user before deleting
            $userCount = DB::queryFirstField("SELECT COUNT(*) FROM users WHERE role_id = %i", $roleId);
            if ($userCount > 0) {
                $_SESSION['error'] = "Cannot delete role - it is assigned to users";
                echo "<script>window.location.href='index.php?route=modules/admin/role-management/view_role';</script>";
                exit();
            }
            
            DB::delete('roles', "id=%i", $roleId);
            $_SESSION['success'] = "Role deleted successfully";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error deleting role: " . $e->getMessage();
        }
        
        echo "<script>window.location.href='index.php?route=modules/admin/role-management/view_role';</script>";
        exit();
    }

    // Handle add/update operation
    if (isset($_POST['btnSubmitRole'])) {
        $roleId = isset($_POST['role_id']) ? $_POST['role_id'] : null;
        $roleTitle = isset($_POST['role_title']) ? trim($_POST['role_title']) : '';
        
        // Validate input
        if (empty($roleTitle)) {
            $_SESSION['error'] = "Role title cannot be empty";
            $redirect = $roleId ? "add_role&role_id=$roleId" : "add_role";
            echo "<script>window.location.href='index.php?route=modules/admin/role-management/$redirect';</script>";
            exit();
        }

        try {
            if (!empty($roleId)) {
                // Update existing role
                DB::update('roles', [
                    'name' => $roleTitle,
                ], "id=%i", $roleId);
                
                $_SESSION['success'] = "Role updated successfully";
            } else {
                // Create new role
                DB::insert('roles', [
                    'name' => $roleTitle,
                ]);
                
                $_SESSION['success'] = "Role added successfully";
            }

            echo "<script>window.location.href='index.php?route=modules/admin/role-management/view_role';</script>";
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Error saving role: " . $e->getMessage();
            $redirect = $roleId ? "add_role&role_id=$roleId" : "add_role";
            echo "<script>window.location.href='index.php?route=modules/admin/role-management/$redirect';</script>";
            exit();
        }
    }
}

// Redirect if accessed directly
echo "<script>window.location.href='index.php?route=modules/admin/role-management/view_role';</script>";
exit();
?>