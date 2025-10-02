<?php
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $row = DB::queryFirstRow("
        SELECT a.id AS applicant_id
        FROM applicants a
        JOIN users u ON a.email = u.email
        WHERE u.user_id = %i
    ", $user_id);

    if ($row) {
        $applicantId = $row['applicant_id'];
    }
} //

?>
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

    /* Full sidebar styling */
    .sidebar-wrapper .sidebar-header .logo-img {
        width: 100px;
    }

    .sidebar-wrapper,
    .sidebar-wrapper .sidebar-header,
    .sidebar-wrapper .sidebar-nav,
    .sidebar-wrapper .metismenu,
    .metismenu ul,
    .metismenu li,
    .metismenu a {
        background-color: rgb(0, 0, 0) !important;
        color: #ffffff !important;
    }

    .sidebar-header {
        position: relative;
        padding: 15px 20px;
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
        <!-- <div class="logo-name flex-grow-1">
                <h5 class="mb-0 text-white" >
                    <?php
                    if (isset($_SESSION['company_name'])) {
                        $company_name = $_SESSION['company_name'];
                        echo abbreviateCompanyName($company_name);
                    } else {
                        echo "Craftsman";
                    }
                    ?>
                </h5>
            </div> -->

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

            <!-- Craftsman Modules Section -->
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined icon">construction</i></div>
                    <div class="menu-title"><?php echo lang("tools_Manage_Tools"); ?></div>
                </a>
                <ul>



                    <ul class="nav-list">
                        <li>
                            <a href="index.php?route=modules/tools/add_tool">
                                <i class="material-icons-outlined icon">add_circle_outline</i>
                                <span><?php echo lang("tools_Add_Tools"); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?route=modules/tools/list_tools">
                                <i class="material-icons-outlined icon">view_list</i>
                                <span><?php echo lang("tools_View_Tools"); ?></span>
                            </a>
                        </li>

                        <!-- <li>
                            <a href="index.php?route=modules/jobs/view_jobs">
                                <i class="material-icons-outlined icon">list_alt</i>
                                <span><?php echo lang("tools_View_Jobs"); ?></span>
                            </a>
                        </li> -->
                        <li>
                            <a href="index.php?route=modules/tools/assign_tools">
                                <i class="material-icons-outlined icon">assignment_turned_in</i>
                                <span><?php echo lang("tools_Assign_Tools"); ?></span>
                            </a>
                        </li>
                    </ul>
                </ul>

            </li>

            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined icon">handyman</i></div>
                    <div class="menu-title"><?php echo lang("equipment_Manage_Equipment"); ?></div>
                </a>
                <ul>
                    <ul class="nav-list">
                        <li>
                            <a href="index.php?route=modules/equipment/add_equipment">
                                <i class="material-icons-outlined icon">add_circle_outline</i>
                                <span><?php echo lang("equipment_Add_Equipment"); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?route=modules/equipment/list_equipment">
                                <i class="material-icons-outlined icon">view_list</i>
                                <span><?php echo lang("equipment_View_Equipment"); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?route=modules/equipment/assign_equipment">
                                <i class="material-icons-outlined icon">assignment_turned_in</i>
                                <span><?php echo lang("equipment_Assign_Equipment"); ?></span>
                            </a>
                        </li>
                        <!-- <li>
                            <a href="index.php?route=modules/equipment/maintenance">
                                <i class="material-icons-outlined icon">build</i>
                                <span><?php echo lang("equipment_Maintenance"); ?></span>
                            </a>
                        </li> -->
                    </ul>
                </ul>
            </li>

            <!-- jobs -->

            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined icon">work_outline</i></div>
                    <div class="menu-title"><?php echo lang("menu_jobs"); ?></div>
                </a>
                <ul>
                    <li>
                        <a href="index.php?route=modules/jobs/create_jobs">
                            <i class="material-icons-outlined icon">add_task</i>
                            <span><?php echo lang("tools_Create_Jobs"); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?route=modules/jobs/view_jobs">
                            <i class="material-icons-outlined">list_alt</i>
                            <?php echo lang("jobs_view_jobs"); ?>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- My Account Link -->
            <li>
                <a href="index.php?route=modules/profile/profile">
                    <div class="parent-icon"><i class="material-icons-outlined icon icon-badge" style="color: indigo;">badge</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_my_profile"); ?></div>
                </a>
            </li>



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