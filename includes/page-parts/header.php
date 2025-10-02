<!doctype html>
<html lang="en" data-bs-theme="blue-theme">
<?php
//TODO:: check for meta tag parameters for the page in session, request and fill them accordingly


?>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CraftHr</title>
  <!--favicon-->
  <link rel="icon" href="assets/images/favicon-32x32.png" type="image/png">

 <?php include_once 'includes/page-parts/header-scripts.php'; ?>
 
</head> 
<body>
  
  <style>
    /* General Icon Styles */
    .icon {
      font-size: 24px;
      vertical-align: middle;
    }

    /* Status Color Classes */
    .draft { color: gray; }
    .ready-for-designer { color: blue; }
    .ready-for-approval { color: green; }
    .pending-approval { color: orange; }
    .on-hold { color: gold; }
    .rejected { color: red; }
    .approved { color: green; }
    .revision { color: purple; }
    .scheduled { color: teal; }
    .published { color: darkgreen; }
    .archived { color: brown; }

    
      /* Sidebar Icon Colors with Purpose-Aligned Colors */
      .icon-home { color: #4169E1; } /* Royal Blue */
        .icon-business { color: #2F4F4F; } /* Dark Slate Gray */
        .icon-integration { color: #FF8C00; } /* Dark Orange */
        .icon-collections { color: #3CB371; } /* Medium Sea Green */
        .icon-settings { color: #6A5ACD; } /* Slate Blue */
        .icon-people { color: #4682B4; } /* Steel Blue */
        .icon-live_tv { color: #DC143C; } /* Crimson */
        .icon-dashboard { color: #008080; } /* Teal */
        .icon-assignment_ind { color: #DAA520; } /* Goldenrod */
        .icon-schema { color: #FF6347; } /* Tomato */
        .icon-approval { color: #228B22; } /* Forest Green */
        .icon-person { color: #663399; } /* Rebecca Purple */

    .alert-success {
        display: none; /* Hidden by default */
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #4CAF50; /* Green */
        color: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        font-family: Arial, sans-serif;
        font-size: 16px;
    }
    .alert-error {
        display: none; /* Hidden by default */
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #f01414; /* red */
        color: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        font-family: Arial, sans-serif;
        font-size: 16px;
    }
</style>
<div id="successAlert" class="alert-success">
    Success! Your action completed successfully.
</div>
<div id="errorAlert" class="alert-error">
    Error! Your action is not completed.
</div>
