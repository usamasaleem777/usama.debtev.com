<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
if(session_id() == '') {
  session_start();
}  
 
if ($_SERVER['SERVER_NAME'] == 'localhost') {
  // Local environment
  $username = "root";
  $password = "";
  $hostname = "localhost";
  $dbName = "crafthiring";
} else {
  // Live database credentials
  $username = "crafthiring";
  $password = "Delta@7788"; // Add your production password here
  $hostname = "localhost"; // Add your live DB host here
  $dbName = "crafthiring";
}

DB::$user = $username;
DB::$password = $password;
DB::$dbName = $dbName;
DB::$host = $hostname; //defaults to localhost if omitted
DB::$encoding = 'utf8'; // defaults to latin1 if omitted
//setting query timezone to California time zone
DB::Query("SET time_zone = '-08:00';"); 

//connection to the database
$con = mysqli_connect($hostname,$username,$password,$dbName);

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
?>
