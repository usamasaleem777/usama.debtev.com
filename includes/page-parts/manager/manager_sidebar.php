 <style>
     .logo-img {
         max-width: 100%;
         /* Ensure image never exceeds container */
         height: auto;
         /* Maintain aspect ratio */
         display: block;
         /* Makes the image a block element */
         margin: 0 auto;
         /* Center the image horizontally */
         padding: 10px 0;
         /* Add some spacing */
     }

     .sidebar-wrapper .sidebar-header .logo-img {
         width: 100px;
     }

     /* Full sidebar styling */
     .sidebar-wrapper,
     .sidebar-wrapper .sidebar-header,
     .sidebar-wrapper .sidebar-nav,
     .sidebar-wrapper .metismenu,
     .metismenu ul,
     /* Target submenus */
     .metismenu li,
     /* Target list items */
     .metismenu a {
         /* Target links */
         background-color: rgb(0, 0, 0) !important;
         color: #ffffff !important;
     }

     /* Hover states */
     .metismenu a:hover,
     .metismenu .active>a,
     .metismenu a:focus {
         background-color: linear-gradient(135deg, #FF5F13, #FF884B) !important;
         color: #ffffff !important;
     }

     /* Submenu indentation fix */
     .metismenu ul {
         padding-left: 0;
         border-left: none;
     }

     /* Menu titles */
     .menu-title {
         color: #ffffff !important;
     }

     /* Icons color */
     .material-icons-outlined.icon {
         color: #ffffff !important;
     }
 </style>
 <!--
       ###########################################################
       #                                                         #
       #                    START OF SIDEBAR                     #
       #                                                         #
       ###########################################################
    -->

 <aside class="sidebar-wrapper" data-simplebar="true">

     <!--
           #######################################################
           #               Sidebar Header Section                #
           #######################################################
        -->
     <div class="sidebar-header" style="padding-bottom: 5px;">
         <div style="position: absolute; bottom: 0; left: 20px; right: 20px; height: 2px; background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 20%, rgba(255,255,255,1) 80%, rgba(255,255,255,0) 100%); opacity: 0.7;"></div>
         <div class="logo-icon" style="text-align: center; width: 100%;"> <!-- Added text-align and width -->
             <?php if (isset($_SESSION['company_logo'])): ?>
                 <img src="<?php echo $_SESSION['company_logo']; ?>" class="logo-img" alt="<?php echo $_SESSION['company_name']; ?> Logo">
             <?php else: ?>
                 <img src="https://craftgc.com/wp-content/uploads/2025/04/Craftcon-GC-logo_USA-flag-Gray-logo-new-color-removebg-preview.png" class="logo-img" alt="Default Logo" style="height: 100px;">
             <?php endif; ?>
         </div>
         <div class="logo-name flex-grow-1">
             <!-- <h5 class="mb-0 text-white">
                 <?php
                    if (isset($_SESSION['company_name'])) {
                        $company_name = $_SESSION['company_name'];
                        echo abbreviateCompanyName($company_name);
                    } else {
                        echo "Manager";
                    }
                    ?>
             </h5> -->
         </div>

         <div class="sidebar-close">
             <span class="material-icons-outlined icon" style="color: black;">close</span>
         </div>
     </div>
     <!--
           #######################################################
           #             End of Sidebar Header Section           #
           #######################################################
        -->

     <div class="sidebar-nav pt-3">
         <!--
               ###################################################
               #                Navigation Section               #
               ###################################################
            -->
         <ul class="metismenu" id="sidenav">

             <!-- Dashboard Link -->
             <li>
                 <a href="index.php" class="">
                     <div class="parent-icon">
                         <i class="material-icons-outlined">home</i>
                     </div>
                     <div class="menu-title"><?php echo lang(key: "dashboard"); ?></div>
                 </a>
             </li>
             <!-- <li>
                    
                    <a href="index.php?route=modules/applicants/list_applicants">
                        <div class="parent-icon"><i class="material-icons-outlined icon icon-leads" style="color:goldenrod;">contact_page</i></div>
                        <div class="menu-title">Applicants</div>
                    </a>
                   
                </li>  -->
             <!-- End of Fresh Leads Link -->
             <li>
                 <a href="javascript:;" class="">
                     <a href="index.php?route=modules/applicants/list_applicants">
                         <div class="parent-icon"><i class="material-icons-outlined icon icon-leads" style="color:goldenrod;">contact_page</i></div>
                         <div class="menu-title"> <?php echo lang(key: "admin_applicants"); ?></div>
                     </a>
                 </a>
             </li>


             <!-- Manage Users Section with Submenu -->
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon"><i class="material-icons-outlined icon icon-people">people</i></div>
                     <div class="menu-title"><?php echo lang(key: "admin_manage_user"); ?></div>
                 </a>
                 <ul>

                     <!-- Add Users Link -->
                     <li><a href="index.php?route=modules/users/create_user"><i class="material-icons-outlined icon icon-person_add">person_add</i><?php echo lang(key: "admin_create_user"); ?></a></li>

                     <!-- Users Link -->
                     <li><a href="index.php?route=modules/users/view_users"><i class="material-icons-outlined icon icon-view_list">view_list</i><?php echo lang(key: "admin_view_user"); ?></a></li>
                 </ul>
             </li>
             <!-- Position -->
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon"><i class="material-icons" style="color:white;">business_center</i></div>
                     <div class="menu-title"><?php echo lang(key: "admin_position"); ?></div>
                 </a>
                 <ul>

                     <!-- Add Users Link -->
                     <li><a href="index.php?route=modules/position/create_position"><i class="material-icons" style="color:white;">add_business</i><?php echo lang(key: "admin_add_position"); ?></a></li>

                     <!-- Users Link -->
                     <li><a href="index.php?route=modules/position/view_position"><i class="material-icons-outlined icon icon-view_list">view_list</i><?php echo lang(key: "admin_view_position"); ?></a></li>
                 </ul>
             </li>
             <!-- TODO: CHECK WHY USER MENU IS OPEN WHEN FRESH LEAD IS CLICKED -->

             <!-- Role Management -->
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon"><i class="material-icons-outlined icon icon-people">people</i></div>
                     <div class="menu-title"><?php echo lang(key: "admin_manage_role"); ?></div>
                 </a>
                 <ul>

                     <!-- Add Users Link -->
                     <li><a href="index.php?route=modules/admin/role-management/add_role"><i class="material-icons-outlined icon icon-person_add">person_add</i><?php echo lang(key: "admin_add_role"); ?></a></li>

                     <!-- Users Link -->
                     <li><a href="index.php?route=modules/admin/role-management/view_role"><i class="material-icons-outlined icon icon-view_list">view_list</i><?php echo lang(key: "admin_view_role"); ?></a></li>
                 </ul>
             </li>

             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon"><i class="material-icons-outlined icon icon-people">description</i></div>
                     <div class="menu-title">Daily Forms</div>
                 </a>
                 <ul>
                    <li><a href="index.php?route=modules/daily_forms/view_daily_form"><i class="material-icons-outlined icon">visibility</i> View Daily Forms </a></li>
                 </ul>
             </li>


             <!-- My Account Link -->
             <li>
                 <a class="" href="javascript:;">
                     <a href="index.php?route=modules/profile/profile">
                         <div class="parent-icon"><i class="material-icons-outlined icon icon-badge" style="color: indigo;">badge</i></div>
                         <div class="menu-title"><?php echo lang(key: "admin_my_profile"); ?></div>
                     </a>
                 </a>
             </li>



         </ul>
         <!--
               ###################################################
               #               End of Navigation Section          #
               ###################################################
            -->
     </div>
 </aside>

 <!--
       ###########################################################
       #                                                         #
       #                    END OF SIDEBAR                       #
       #                                                         #
       ###########################################################
    -->