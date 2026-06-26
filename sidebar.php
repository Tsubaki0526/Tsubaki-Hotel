
<aside class="sidebar" id="sidebar-collapse">
    <div class="sidebar-header">
        <div class="sidebar-avatar">
            <img src="img/user.png" alt="">
        </div>
        <div class="sidebar-user">
            <strong><?php echo htmlspecialchars($user['username']);?></strong>
            <small><span class="online-dot"></span> <?php _e('admin_label') ?></small>
        </div>
    </div>
    <ul class="nav sidebar-nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php if(isset($_GET['dashboard'])) echo 'active'; ?>" href="index.php?dashboard">
                <i class="fa fa-dashboard"></i> <span><?php _e('nav_dashboard') ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if(isset($_GET['reservation'])) echo 'active'; ?>" href="index.php?reservation">
                <i class="fa fa-calendar"></i> <span><?php _e('nav_reservations') ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if(isset($_GET['room_mang'])) echo 'active'; ?>" href="index.php?room_mang">
                <i class="fa fa-bed"></i> <span><?php _e('nav_rooms') ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if(isset($_GET['staff_mang'])) echo 'active'; ?>" href="index.php?staff_mang">
                <i class="fa fa-users"></i> <span><?php _e('nav_staff') ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if(isset($_GET['complain'])) echo 'active'; ?>" href="index.php?complain">
                <i class="fa fa-comments"></i> <span><?php _e('nav_complaints') ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if(isset($_GET['payments'])) echo 'active'; ?>" href="index.php?payments">
                <i class="fa fa-credit-card"></i> <span><?php _e('nav_payments') ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if(isset($_GET['statistics'])) echo 'active'; ?>" href="index.php?statistics">
                <i class="fa fa-pie-chart"></i> <span><?php _e('nav_statistics') ?></span>
            </a>
        </li>

        <li class="sidebar-divider"><span><?php _e('nav_website') ?></span></li>

        <li class="nav-item">
            <a class="nav-link <?php if(isset($_GET['admin_blog'])) echo 'active'; ?>" href="index.php?admin_blog">
                <i class="fa fa-newspaper-o"></i> <span><?php _e('nav_blog') ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if(isset($_GET['admin_services'])) echo 'active'; ?>" href="index.php?admin_services">
                <i class="fa fa-cogs"></i> <span><?php _e('nav_services') ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if(isset($_GET['admin_room_types'])) echo 'active'; ?>" href="index.php?admin_room_types">
                <i class="fa fa-bed"></i> <span><?php _e('nav_room_types') ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if(isset($_GET['admin_messages'])) echo 'active'; ?>" href="index.php?admin_messages">
                <i class="fa fa-envelope"></i> <span><?php _e('nav_messages') ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if(isset($_GET['admin_settings'])) echo 'active'; ?>" href="index.php?admin_settings">
                <i class="fa fa-cog"></i> <span><?php _e('nav_settings') ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="public/index.php" target="_blank">
                <i class="fa fa-external-link"></i> <span><?php _e('nav_view_site') ?></span>
            </a>
        </li>
    </ul>
</aside>
