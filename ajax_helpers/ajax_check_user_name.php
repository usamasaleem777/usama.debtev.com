<?php
require('../functions.php');

if (isset($_POST['email'])) {
	$email = $_POST['email'];

	// Get the entered User Name and User ID from the AJAX request
	if (isset($_POST['user_id'])) {
		$user_id = $_POST['user_id'];
	}

	if (isset($_POST['user_id'])) {
	
		// Prepare and execute a query to check if the User Name exists
		$result = DB::queryFirstField("SELECT COUNT(*) FROM users WHERE email = %s AND user_id <> %s ", $email, $user_id);
	
	} else {

		// Prepare and execute a query to check if the User Name exists
		$result = DB::queryFirstField("SELECT COUNT(*) FROM users WHERE email = %s", $email);
	
	}
	// Create a response array
	$response = array('exists' => $result > 0);

	// Send the JSON response back to the AJAX call
	header('Content-Type: application/json');
	echo json_encode($response);
} else {

	echo "Sorry, No User Name was sent";
}


?>