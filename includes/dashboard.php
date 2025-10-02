<?php
session_start();
if (!isset($_SESSION['is_logged'])) {
    header('Location: login.php'); 
    exit;
}

echo "<h1>Welcome, " . $_SESSION['name'] . "!</h1>";
echo "<p>Company: " . $_SESSION['company_name'] . "</p>";
?>
<a href="logout.php">Logout</a>
