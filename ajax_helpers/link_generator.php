<?php
require('../functions.php');
header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_link'])) {
    try {
        $applicant_id = (int)$_POST['applicant_id']; // This comes from the form input
        $expires_at = null;

        $form_steps = implode(',', $_POST['form_step']);

        if (!empty($_POST['expiry_date'])) {
            $expires_at = date('Y-m-d H:i:s', strtotime($_POST['expiry_date']));
            if ($expires_at < date('Y-m-d H:i:s')) {
                throw new Exception('Exception Occured');
            }
        }

        $token = bin2hex(random_bytes(16));
        $generated_link = "https://craftgc.com/app/forms.php?token=$token"; // UPDATE THIS URL

      $data=  DB::insert('applicant_links', [
            'applicant_id' => $applicant_id,
            'form_link' => $generated_link,
            'token' => $token,
            'form_steps' => $form_steps,
            'generated_date' => date('Y-m-d H:i:s'),
            'expires_at' => $expires_at
        ]);



        // Return JSON response
  // Return JSON response

  echo json_encode([
      'success' => true,
      'link' => $generated_link,
      'message' =>"Success",
  ]);
  exit();

} catch (Exception $e) {

  echo json_encode([
      'success' => false,
      'error' => $e->getMessage()
  ]);
  exit();
}
}
?>
