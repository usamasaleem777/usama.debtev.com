<?php
session_start();
session_unset();  
session_destroy();  
header("Location: \bixiscreatives\includes\login_functions.php");
exit();
?>
