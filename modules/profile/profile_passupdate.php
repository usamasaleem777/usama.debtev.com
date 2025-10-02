<?php 

    if (isset($_POST) && isset($_POST['formtype1'])) {
        if ($_POST['formtype1'] == "adminuser") {
            @extract($_POST);
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            $current_password = $_POST['current_password'];
            
            
            // $password1 = DB::query("SELECT password FROM admin_users WHERE user_id = $user_id");
            $password1 = DB::query("SELECT password FROM admin_users WHERE user_id = $user_id");
            
            if($current_password == $password1[0]['password']){
                if ($new_password === $confirm_password) {

                    // Assuming $user_id is already defined in your code
                    DB::update("admin_users", array(
                        'password' => $new_password,
                        'last_modified_by' => $_SESSION['user_name']
                    ), 'user_id=%s', $user_id);
    
                    echo '
                    <script> alert("Your password is updated!"); </script>
                    <script type="text/javascript">
                            window.location = "index.php?route=modules/profile/profile";
                          </script>';
                } else {
                    echo '
                    <script> alert("Confirm password does not match!"); </script>
                    <script type="text/javascript">
                            window.location = "index.php?route=modules/profile/profile";
                          </script>
                          
                          
                    ';
                }
            }else{
                echo '<script> alert("Current Password does not match!"); </script>
                    <script type="text/javascript">
                            window.location = "index.php?route=modules/profile/profile";
                          </script>
                          
                    ';
            }
            
            
        }
    }
?>