<?php 
    if(!isset($_SESSION)) { 
        session_start();
    } 

    // if role_id is not admin_role, manager_role or hr role   then exit
 

    if($_SESSION['role_id']  != $super_admin_role && $_SESSION['role_id']  != $admin_role && $_SESSION['role_id']  != $manager_role && $_SESSION['role_id']  != $hr_role )    {
        echo "<h3>Only admins can do this edit!</h3>";
        exit();
    }

    $user_id = $_REQUEST['user_id'];
    $company_id = $_SESSION['company_id'];
    
    $query = DB::update("admin_users", array(
        'signature'   => ''
    ), 'user_id=%s', $user_id);

    echo '<script type="text/javascript">
    window.location = "index.php?route=modules/users/edituser&user_id=' . $user_id . '";
    </script>';
?>
