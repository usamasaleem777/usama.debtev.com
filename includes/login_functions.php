<?php
function get_user_name($user_id) {
// TODO: Fix this function to get user's user_name;
$sql = "SELECT user_name FROM admin_users WHERE user_id='".$user_id."'";
$user_name = DB::queryFirstField($sql);
	return $user_name;
}

function get_full_user_name($user_id ="") {
	$user_full_name = "--";
	if ($user_id <> "" AND $user_id <> 0 ) {
		$sql = "SELECT first_name, last_name FROM admin_users WHERE user_id='".$user_id."'";
		$res = DB::queryFirstRow($sql);
		if (!empty($res)) {
			$user_full_name = $res['first_name']." ".$res['last_name'];
		}
	}
	return  $user_full_name;
	
}
 
 
function attempt_login_user($login, $password) {
    // session_destroy();
    // session_start();
    
    // Check if login is email, username, or kioskID and get user status
    $is_logged = DB::queryFirstRow(
        "SELECT * FROM users u WHERE 
        (u.email = %s OR u.user_name = %s OR u.kioskID = %s) 
        AND BINARY u.password = %s",
        $login, $login, $login, $password
    );

    if ($is_logged) {
        // Check if user is suspended or fired
        if ($is_logged['status'] === 'suspended') {
            $_SESSION['login_error'] = 'Your account has been suspended. Please contact administrator.';
            return false;
        }
        
        if ($is_logged['status'] === 'fire') {
            $_SESSION['login_error'] = 'Your account has been terminated and you cannot login.';
            return false;
        }
        
        // Proceed with login if account is active
        
        $_SESSION['is_logged'] = 1;
        $_SESSION['user_id'] = $is_logged['user_id'];    
        $_SESSION['user_name'] = $is_logged['user_name'];
        $_SESSION['user_email'] = $is_logged['email']; 
        $_SESSION['role_id'] = getUserRoleID($is_logged['user_id']); 
        $_SESSION['user_status'] = $is_logged['status'];
        
        setcookie("user_id", $is_logged['user_id'], time() + (86400 * 30 * 15), "/");
        return true;
    } else {
        $_SESSION['login_error'] = 'Invalid login credentials';
        return false;
    }
}
?>