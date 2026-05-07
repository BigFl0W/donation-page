<?php

declare(strict_types=1);
?>
<aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-sidebar-header">
        <div class="brand">
            <i class="icofont-unity-hand"></i>
            <span>Gracious Admin</span>
        </div>
    </div>

    <nav class="admin-nav">
        <ul class="wp-menu">
            <li class="wp-menu-item <?php echo is_active_admin_route("dashboard") ? "active" : ""; ?>">
                <a href="<?php echo e(admin_url("index.php?page=dashboard")); ?>">
                    <div class="wp-menu-arrow"><div></div></div>
                    <div class="wp-menu-image"><i class="icofont-dashboard"></i></div>
                    <div class="wp-menu-name">Dashboard</div>
                </a>
            </li>
            <li class="wp-menu-separator"></li>
            <li class="wp-menu-item <?php echo is_active_admin_route("content") ? "active" : ""; ?>">
                <a href="<?php echo e(admin_url("index.php?page=content")); ?>">
                    <div class="wp-menu-arrow"><div></div></div>
                    <div class="wp-menu-image"><i class="icofont-layout"></i></div>
                    <div class="wp-menu-name">Content</div>
                </a>
            </li>
            <li class="wp-menu-item <?php echo is_active_admin_route("posts") ? "active" : ""; ?>">
                <a href="<?php echo e(admin_url("index.php?page=posts")); ?>">
                    <div class="wp-menu-arrow"><div></div></div>
                    <div class="wp-menu-image"><i class="icofont-pin"></i></div>
                    <div class="wp-menu-name">Blog Posts</div>
                </a>
            </li>
            <li class="wp-menu-item <?php echo is_active_admin_route("events") ? "active" : ""; ?>">
                <a href="<?php echo e(admin_url("index.php?page=events")); ?>">
                    <div class="wp-menu-arrow"><div></div></div>
                    <div class="wp-menu-image"><i class="icofont-calendar"></i></div>
                    <div class="wp-menu-name">Events</div>
                </a>
            </li>
            <li class="wp-menu-item <?php echo is_active_admin_route("donations") ? "active" : ""; ?>">
                <a href="<?php echo e(admin_url("index.php?page=donations")); ?>">
                    <div class="wp-menu-arrow"><div></div></div>
                    <div class="wp-menu-image"><i class="icofont-money"></i></div>
                    <div class="wp-menu-name">Donations</div>
                </a>
            </li>
            <li class="wp-menu-separator"></li>
            <li class="wp-menu-item <?php echo is_active_admin_route("admins") ? "active" : ""; ?>">
                <a href="<?php echo e(admin_url("index.php?page=admins")); ?>">
                    <div class="wp-menu-arrow"><div></div></div>
                    <div class="wp-menu-image"><i class="icofont-users"></i></div>
                    <div class="wp-menu-name">Admins</div>
                </a>
            </li>
            <li class="wp-menu-item <?php echo is_active_admin_route("settings") ? "active" : ""; ?>">
                <a href="<?php echo e(admin_url("index.php?page=settings")); ?>">
                    <div class="wp-menu-arrow"><div></div></div>
                    <div class="wp-menu-image"><i class="icofont-settings-alt"></i></div>
                    <div class="wp-menu-name">Settings</div>
                </a>
            </li>
        </ul>
    </nav>

    <div id="collapse-menu" class="collapse-menu">
        <button type="button" id="collapse-button" aria-expanded="true">
            <span class="collapse-button-icon" aria-hidden="true"></span>
            <span class="collapse-button-label">Collapse menu</span>
        </button>
    </div>
</aside>
