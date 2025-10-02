   <style>
    /* Add this to your CSS */
.main-content-wrapper {
    min-height: 60vh;
    display: flex;
    flex-direction: column;
    position: relative;
}

.page-footer {
    margin-top: auto; /* Pushes footer to bottom */
    background: #fff; /* Match your background */
    padding: 15px;
    /* Optional styling */
    border-top: 1px solid #dee2e6;
    z-index: 100;
}

/* If you have a sidebar */
.sidebar {
    position: fixed;
    left: 0;
    height: 100vh;
    width: 250px; /* Match your sidebar width */
}
   </style>
   <!--start footer-->
   <div class="main-content-wrapper">

     <footer class="page-footer">
     <p class="mb-0">Copyright © <?php echo date('Y'); ?>. All rights reserved.</p>
    </footer>
    <!--top footer-->

 

  <?php include_once 'includes/page-parts/footer-scripts.php'; ?>

  </body>

</html>