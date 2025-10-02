<?php
/************** START SECURITY CHECK ***********************/
// Define allowed roles - make sure these variables are defined somewhere in your application
$allowedRoles = array(
					$admin_role, 
					$manager_role
					); // You need to define these variables

// Check if role is allowed
if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], $allowedRoles)) {
    // User does not have access, redirect to home
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => "You do not have permission to view this page."
    ];
    echo '<script>window.location.href = "index.php";</script>';
    die();
}
/**************** END SECURITY CHECK ***********************/


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>
        alert('⚠️ Invalid or missing ID.');
        window.location.href = 'index.php?route=modules/applicants/list_applicants';
    </script>";
    exit;
}

$id = intval($_GET['id']);

try {
    $applicant = DB::queryFirstRow("SELECT * FROM applicants WHERE id = %i", $id);

    if (!$applicant) {
        echo "<script>
            alert('❌ Applicant not found. Could not delete.');
            window.location.href = 'index.php?route=modules/applicants/list_applicants';
        </script>";
        exit;
    }
    DB::delete('employment_history', 'applicant_id = %i', $id);  // Delete employment history
    DB::delete('education', 'applicant_id = %i', $id);   // Delete education history
    DB::delete('application_signatures', 'applicant_id = %i', $id);  // Delete signature history
    DB::delete('criminal_history', 'applicant_id = %i', $id);   // Delete criminal history
    DB::delete('references_info', 'applicant_id = %i', $id);  // Delete employment history
    DB::delete('skills', 'applicant_id = %i', $id);   // Delete education history


    DB::delete('applicants', 'id=%i', $id);
    

    echo "<script>
        window.location.href = 'index.php?route=modules/applicants/list_applicants';
    </script>";
    exit;

} catch (Exception $e) {
    echo "<script>
        alert('⚠️ Error deleting applicant: " . addslashes($e->getMessage()) . "');
        window.location.href = 'index.php?route=modules/applicants/list_applicants';
    </script>";
    exit;
}
