<?php 
    if(!isset($_SESSION)) { 
        session_start();
    } 

    $user_id = $_SESSION['user_id'];

    $query = DB::update("admin_users", array(
        'signature'   => ''
    ), 'user_id=%s', $user_id);

    echo '<script type="text/javascript">
    window.location = "index.php?route=modules/profile/profile";
    </script>';
?>
