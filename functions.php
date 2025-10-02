<?php
// Load Configuration File
include_once('includes/config.php');
include_once('includes/load_classes.php');
include_once('includes/db_config.php');
// include_once('includes/general_functions.php');
include_once('includes/business_functions.php');
// include_once('includes/security.php');
include_once('includes/login_functions.php');

// Switch language
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    if (in_array($lang, ['en', 'es'])) { // Supported languages
        $_SESSION['lang'] = $lang;
    }
}