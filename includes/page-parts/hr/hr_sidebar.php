<style>
    .logo-img {
        width: 120px !important;
        display: block;
        /* Makes the image a block element */
        margin-left: auto;
        /* Auto margins for horizontal centering */
        margin-right: auto;
    }

    /* Full sidebar styling */
    .sidebar-wrapper,
    .sidebar-wrapper .sidebar-header,
    .sidebar-wrapper .sidebar-nav,
    .sidebar-wrapper .metismenu,
    .metismenu ul,
    .metismenu li,
    .metismenu a {
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
    .material-icons-outlined.icon,
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

<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header" style="padding-bottom: 5px;">
        <div style="position: absolute; bottom: 0; left: 20px; right: 20px; height: 2px; background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 20%, rgba(255,255,255,1) 80%, rgba(255,255,255,0) 100%); opacity: 0.7;"></div>
        <div class="logo-icon" style="text-align: center; width: 100%;"> <!-- Added text-align and width -->
            <?php if (isset($_SESSION['company_logo'])): ?>
                <img src="<?php echo $_SESSION['company_logo']; ?>" class="logo-img" alt="<?php echo $_SESSION['company_name']; ?> Logo">
            <?php else: ?>
                <img src="https://craftgc.com/wp-content/uploads/2025/04/Craftcon-GC-logo_USA-flag-Gray-logo-new-color-removebg-preview.png" class="logo-img" alt="Default Logo" style="height: 90px;">
            <?php endif; ?>
        </div>
        <div class="logo-name flex-grow-1">
            <h5 class="mb-0 text-white d-none">Foreman Dashboard</h5>
        </div>
        <div class="sidebar-close">
            <span class="material-icons-outlined icon" style="color: black;">close</span>
        </div>
    </div>

    <div class="sidebar-nav pt-3">
        <ul class="metismenu" id="sidenav">
            <!-- Dashboard -->
            <li>
                <a href="index.php" class="">
                    <div class="parent-icon"><i class="material-icons-outlined icon">dashboard</i></div>
                    <div class="menu-title"><?php echo lang("dashboard"); ?></div>
                </a>
            </li>



            <li>
                <a href="index.php?route=modules/applicants/list_applicants">
                    <div class="parent-icon"><i class="material-icons-outlined icon">people_alt</i></div>
                    <div class="menu-title"><?php echo lang(key: "admin_applicants"); ?></div>
                </a>
            </li>

            <!-- Profile -->
            <li>
                <a href="index.php?route=modules/profile/profile">
                    <div class="parent-icon"><i class="material-icons-outlined icon">account_circle</i></div>
                    <div class="menu-title"><?php echo lang("my_profile"); ?></div>
                </a>
            </li>
        </ul>

    </div>
</aside>