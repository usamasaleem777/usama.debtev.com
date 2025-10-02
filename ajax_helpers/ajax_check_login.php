<?php 
require('../functions.php');

$login = isset($_POST['login']) ? $_POST['login'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

if ($login && $password) {
    $check = attempt_login_user($login, $password);
    
    if ($check) {
        $user = DB::queryFirstRow("SELECT user_id FROM users WHERE 
                                 (email = %s OR BINARY user_name = %s OR kioskID = %s) 
                                 AND BINARY password = %s", 
                                 $login, $login, $login, $password);
        
        if ($user) {
            DB::update('users', [
                'last_login' => date('Y-m-d H:i:s')
            ], "user_id=%i", $user['user_id']);
        }
        
        echo '1'; // Login successful
    } else {
        // Handle specific login errors for JS
        if (isset($_SESSION['login_error'])) {
            if ($_SESSION['login_error'] === 'Your account has been suspended. Please contact administrator.') {
                echo 'suspended';
            } elseif ($_SESSION['login_error'] === 'Your account has been terminated and you cannot login.') {
                echo 'fired';
            } else {
                echo '0'; // Invalid login
            }
            unset($_SESSION['login_error']);
        } else {
            echo '0'; // Invalid login
        }
    }
}
?>