<?php
// Include the functions file
require('functions.php');


?>

<style>
    /* Original Form Styles */
    body {
        background: #f1f2f6;
    }

    /* Thank You Content Styles */
    .thank-you-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        padding: 40px;
        margin: 50px auto;
        max-width: 800px;
    }

    .checkmark-icon {
        color: #FF5500;
        font-size: 4rem;
        margin-bottom: 20px;
    }

    /* General Styles */
    .top-header {
        background-color: #000;
        color: #fff;
        /* padding: 10px 76px; */
        padding: 10px 138px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Navigation */
    .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Footer Styles */
    #nd_options_footer_5 {
        background-image: url('https://craftgc.com/wp-content/uploads/2019/09/paral-04.jpg');
        background-size: cover;
        color: white;
        padding: 50px 0;
    }
</style>

<!-- Main Content -->
<div class="container">
    <div class="thank-you-card text-center">
        <div class="checkmark-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor"
                viewBox="0 0 16 16">
                <path
                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
            </svg>
        </div>
        <h1 class="mb-3"><?php echo lang("application_sumbitted"); ?></h1>
        <p class="lead mb-4"><?php echo lang("thanks_greeting"); ?></p>



        <hr class="my-4">
        <p class="text-muted small"><br><?php echo lang("thanks_quries"); ?><a href="mailto:info@craftgc.com

" class="text-reset">info@craftgc.com
            </a> or (888) 359-3550

        </p>
    </div>
</div>


</body>

</html>