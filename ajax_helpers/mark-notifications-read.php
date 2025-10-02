
<?php
require('../functions.php');

// Mark all unread notifications as read
DB::query("UPDATE applicants SET is_read = 1 WHERE is_read = 0");
DB::query("UPDATE craft_contracting SET is_read = 1 WHERE is_read = 0");

http_response_code(200); // Optional: respond with success
echo json_encode(["status" => "success"]);
