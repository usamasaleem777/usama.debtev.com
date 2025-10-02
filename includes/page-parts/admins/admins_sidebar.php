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
        background-color: #000000 !important;
        color: #92939a !important;
        font-weight: 600;
    }

    /* Hover states */
    .metismenu a:hover,
    .metismenu .active>a,
    .metismenu a:focus {
        background-color: linear-gradient(135deg, #000000, rgb(26, 24, 24)) !important;
        color: #92939a !important;
        font-weight: 600;
    }

    /* Submenu indentation fix */
    .metismenu ul {
        padding-left: 0;
        border-left: none;
    }

    /* Menu titles */
    .menu-title {
        color: #92939a !important;
    }

    /* Icons color */
    .material-icons-outlined.icon {
        color: #92939a !important;
    }

    /* Icons color */
    .material-icons {
        color: #92939a !important;
    }

    /* Default logo image style */
    .logo-icon img {
        width: auto;
        max-width: 100%;
        height: 70px;
        transition: all 0.3s ease-in-out;
        display: block;
        margin: 0 auto;
    }

    /* Collapse state style */
    .sidebar.collapsed .logo-icon {
        padding: 10px;
        text-align: center;
    }

    .sidebar.collapsed .logo-icon img {
        width: 40px;
        height: 40px;
        object-fit: contain;
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
            <h5 class="mb-0 text-white d-none">
                <?php
                if (isset($_SESSION['company_name'])) {
                    $company_name = $_SESSION['company_name'];
                    echo abbreviateCompanyName($company_name);
                } else {
                    echo "Admin";
                }
                ?>
            </h5>
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
                    <div class="parent-icon"><i class="material-icons-outlined icon">dashboard</i></div>
                    <div class="menu-title"><?php echo lang(key: "dashboard"); ?></div>
                </a>
            </li>

            <!-- Applicants Link -->
            <li>
                <a href="index.php?route=modules/applicants/list_applicants">
                    <div class="parent-icon"><i class="material-icons-outlined icon">people_alt</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_applicants"); ?></div>
                </a>
            </li>
            

            <li>
        
            <!-- List Data Section -->
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined icon">storage</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_list_data"); ?></div>
                </a>
                <ul>
                    <!-- View Data Link -->
                    <li>
                        <!-- <a href="index.php?route=modules/forms/list_data">
                            <i class="material-icons-outlined">list_alt</i>
                            <?php echo lang(key: "admin_view_data"); ?>
                        </a> -->

                        <!-- Add Users Link -->
                    <li>
                        <a href="index.php?route=modules/forms/packet-applicants">
                            <i class="material-icons-outlined">assignment_ind</i>
                            <?php echo lang(key: "admin_packet_applicants"); ?>
                        </a>
                    </li>
                     <li>
                        <a href="index.php?route=modules/forms/not_submitted">
                            <i class="material-icons-outlined">assignment_ind</i>
                            <?php echo lang(key: "not_submitted_packets"); ?>
                        </a>
                    </li>
                    <!-- Package Applicants Link -->
                    <li>
                        <a href="index.php?route=modules/forms/list_data">
                            <i class="material-icons-outlined">list_alt</i>
                            <?php echo lang(key: "admin_view_packets"); ?>
                        </a>
                    </li>
                </ul>
            </li>


            <!-- Position Section -->
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined icon">work</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_position"); ?></div>
                </a>
                <ul>
                    <!-- Add Position Link -->
                    <li><a href="index.php?route=modules/position/create_position">
                            <i class="material-icons-outlined">add_business</i>
                            <?php echo lang(key: "admin_add_position"); ?>
                        </a>
                    </li>

                    <!-- View Position Link -->
                    <li><a href="index.php?route=modules/position/view_position">
                            <i class="material-icons-outlined">business_center</i>
                            <?php echo lang(key: "admin_view_position"); ?>
                        </a>
                    </li>
                </ul>
            </li>
<!-- Edit Requests Section -->
<li>
    <a href="javascript:;" class="has-arrow">
        <div class="parent-icon">
            <i class="material-icons-outlined icon">edit_note</i>
        </div>
        <div class="menu-title"><?php echo lang("admin_Edit_Requests"); ?></div>
    </a>
    <ul>
        <!-- Edit Packet Form Requests Link -->
        <li>
            <a href="index.php?route=modules/requests/edit_packet_form">
                <i class="material-icons-outlined">edit</i>
                <?php echo lang("admin_Edit_Packet_Form_Requests"); ?>
            </a>
        </li>
         <!-- Edit Daily Work Form Requests Link -->
        <li>
            <a href="index.php?route=modules/requests/edit_daily_work_form">
                <i class="material-icons-outlined">edit</i>
                <?php echo lang("admin_Edit_daily_Form_Requests"); ?>
            </a>
        </li>
    </ul>
</li>
            <!-- Manage Users Section -->
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined icon">manage_accounts</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_manage_user"); ?></div>
                </a>
                <ul>
                    <!-- Add Users Link -->
                    <li><a href="index.php?route=modules/users/create_user">
                            <i class="material-icons-outlined">person_add</i>
                            <?php echo lang(key: "admin_create_user"); ?>
                        </a>
                    </li>
                    <!-- Users Link -->
                    <li>
                        <a href="index.php?route=modules/users/view_users">
                            <i class="material-icons-outlined">group</i>
                            <?php echo lang(key: "admin_view_user"); ?>
                        </a>
                    </li>

                    <li>
                        <a href="index.php?route=modules/users/fire_user">
                            <i class="material-icons-outlined">person_off</i> <!-- New icon for firing user -->
                            <?php echo lang(key: "admin_fire_user"); ?>
                        </a>
                    </li>

                    <li class="packet-users-li">
                        <a href="javascript:void(0);" id="packetUsersLink">
                            <i class="material-icons-outlined">lock</i>
                            <div>
                                <?php echo lang(key: "packet_users"); ?><br>
                                <small><?php echo lang(key: "Protected"); ?></small>
                            </div>
                        </a>
                    </li>

                </ul>
            </li>

            <!-- Role Management Section -->
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined icon">admin_panel_settings</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_manage_role"); ?></div>
                </a>
                <ul>
                    <!-- Add Role Link -->
                    <li><a href="index.php?route=modules/admin/role-management/add_role">
                            <i class="material-icons-outlined">add_moderator</i>
                            <?php echo lang(key: "admin_add_role"); ?>
                        </a>
                    </li>

                    <!-- View Role Link -->
                    <li><a href="index.php?route=modules/admin/role-management/view_role">
                            <i class="material-icons-outlined">verified_user</i>
                            <?php echo lang(key: "admin_view_role"); ?>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- Jobs Management -->
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined icon icon-people">people</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_manage_jobs"); ?></div>
                </a>
                <ul>

                    <!-- Add Users Link -->
                    <li><a href="index.php?route=modules/jobs/create_jobs">
                            <i class="material-icons-outlined">add_business</i><?php echo lang(key: "admin_add_jobs"); ?></a>
                    </li>

                    <!-- Users Link -->
                    <li><a href="index.php?route=modules/jobs/view_jobs">
                            <i class="material-icons-outlined">business_center</i><?php echo lang(key: "admin_view_jobs"); ?></a>
                    </li>
                </ul>
            </li>

            <!-- Status Management Section -->
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined icon">flag</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_manage_status"); ?></div>
                </a>
                <ul>
                    <!-- Add Status Link -->
                    <li><a href="index.php?route=modules/admin/status-management/add_status">
                            <i class="material-icons-outlined">add_circle_outline</i>
                            <?php echo lang(key: "admin_add_status"); ?>
                        </a>
                    </li>

                    <!-- View Status Link -->
                    <li><a href="index.php?route=modules/admin/status-management/view_status">
                            <i class="material-icons-outlined">list_alt</i>
                            <?php echo lang(key: "admin_view_status"); ?>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Form Links Section -->
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined icon">link</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_manage_links"); ?></div>
                </a>
                <ul>
                    <!-- Manage Links -->
                    <li>
                        <a href="index.php?route=modules/links/list_links">
                            <i class="material-icons-outlined">link</i>
                            <?php echo lang(key: "admin_view_links"); ?>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Crew Management Section -->
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="material-icons-outlined icon">people</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_crew"); ?></div>
                </a>
                <ul>
                    <!-- Manage Crew Link -->
                    <li>
                        <a href="index.php?route=modules/crew-management/manage_crew">
                            <i class="material-icons-outlined">group</i>
                            <?php echo lang(key: "admin_manage_crew"); ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="index.php?route=modules/qr_code_generator/qr_code_generator">
                    <div class="parent-icon"><i class="material-icons-outlined icon icon-people">people</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_QR_Generate"); ?></div>
                </a>
            </li>
            <li>
                <a href="javascript:;" class="has-arrow d-flex align-items-center">
                    <div class="parent-icon">
                        <i class="material-icons-outlined icon">build</i> <!-- Changed to "build" for Tools -->
                    </div>
                    <div class="menu-title">
                        <?php echo lang("tools_Tools"); ?>
                    </div>
                </a>
                <ul>
                    <a href="index.php?route=modules/tools/add_tool" class="menu-item d-flex align-items-center">
                        <div class="parent-icon">
                            <i class="material-icons-outlined icon">add</i> <!-- Changed to "build_circle" for View Tools -->
                        </div>
                        <div class="menu-title"><?php echo lang("tool_add_tool"); ?></div>
                    </a>
                </ul>
                <ul>
                    <a href="index.php?route=modules/tools/list_tools" class="menu-item d-flex align-items-center">
                        <div class="parent-icon">
                            <i class="material-icons-outlined icon">build_circle</i> <!-- Changed to "build_circle" for View Tools -->
                        </div>
                        <div class="menu-title"><?php echo lang("tools_view_tools"); ?></div>
                    </a>
                </ul>
            </li>
            <!-- Uploads link -->
            <li> 
                <a href="javascript:;" class="has-arrow d-flex align-items-center">
                    <div class="parent-icon">
                        <i class="material-icons-outlined icon">upload_file</i>
                    </div>
                    <div class="menu-title">
                        <?php echo lang(key: "admin_uploads"); ?>
                    </div>
                </a>


                <ul>
                    <a href="index.php?route=modules/applicants/csv_applicants" class="menu-item d-flex align-items-center">
                        <div class="parent-icon">
                            <i class="material-icons-outlined icon">table_chart</i>
                        </div>
                        <div class="menu-title">CSV</div>
                    </a>
                </ul>
            </li>
            <!-- Templates Section -->
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon">
                        <i class="material-icons-outlined icon">description</i>
                    </div>
                    <div class="menu-title"><?php echo lang("admin_Template"); ?></div>
                </a>
                <ul>
                    <!-- New Templates Link -->
                    <li>
                        <a href="index.php?route=modules/templates/createtemplates">
                            <i class="material-icons-outlined">add_circle</i>
                            <?php echo lang("admin_New_Template"); ?>
                        </a>
                    </li>

                    <!-- View Templates Link -->
                    <li>
                        <a href="index.php?route=modules/templates/view_templates">
                            <i class="material-icons-outlined">collections_bookmark</i>
                            <?php echo lang("admin_View_Templates"); ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="index.php?route=modules/employee/employee">
                    <div class="parent-icon"><i class="material-icons-outlined icon icon-people">people</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_employee"); ?></div>
                </a>
            </li>
            <!-- My Account Link -->

            <li>
                <a href="index.php?route=modules/profile/profile">
                    <div class="parent-icon"><i class="material-icons-outlined icon">account_circle</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_my_profile"); ?></div>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById("packetUsersLink").addEventListener("click", function() {
        Swal.fire({
            title: '<span style="color:#fe5500;">Enter Password</span>',
            input: 'password',
            inputLabel: 'This section is protected',
            inputPlaceholder: 'Enter your password',
            inputAttributes: {
                maxlength: 20,
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Submit',
            preConfirm: (password) => {
                if (password !== "chevy24594") {
                    Swal.showValidationMessage('Incorrect password');
                } else {
                    return true;
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "index.php?route=modules/users/packet_users";
            }
        });
    });
</script>