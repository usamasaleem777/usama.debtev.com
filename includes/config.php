<?php
ini_set('display_errors',1); 
date_default_timezone_set('America/Los_Angeles'); 
 


error_reporting(E_ALL);
if(session_id() == '') {
    session_start();
}
  
// Define System CONSTANTS
define('SYSTEM_ENCODING', 'utf8' ); 
define('BR','</br>');
$now = date("Y-m-d H:i:s");

 
define('FOLDER_NAME','hiring'); 
define('ROOT_PATH', realpath(dirname(__FILE__)."/../").'/');

define('SITE_ROOT', 'https://'.$_SERVER['HTTP_HOST'].'/'.FOLDER_NAME.'/');
define('MAIN_SITE','https://'.$_SERVER['HTTP_HOST'].'/'); 
define('DB_PREFIX', '');
  

// Constants for user roles by ID
define('ROLE_ID_ADMIN', 1);
define('ROLE_ID_MANAGER', 2);
define('ROLE_ID_CRAFTSMAN', 5); 
define('ROLE_ID_FOREMAN', 4); 
define('ROLE_ID_EMPLOYEE', 3); 
define('ROLE_ID_SUPERINTENDENT',6);
define('ROLE_ID_TOOL_MANAGER',7);
define('ROLE_ID_HR',8);



$admin_role = 1;
$manager_role = 2;
$craftsman_role = 5;
$foreman_role =4;
$employee_role =3;
$superintendent_role =6;
$tool_manager =7;
$hr =8;

 
	?>
