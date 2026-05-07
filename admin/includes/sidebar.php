<?php

declare(strict_types=1);
?>
<aside class="admin-sidebar">
    <div class="brand">
        <img src="../assets/images/logo_white.svg" alt="Gracious">
        <div>
            <h1>Gracious Admin</h1>
            <p>Platform control center</p>
        </div>
    </div>

    <div class="admin-user-chip">
        <strong><?php echo e($currentAdmin["name"] ?? "Admin User"); ?></strong>
        <span><?php echo e($currentAdmin["role"] ?? "admin"); ?></span>
    </div>

    <nav class="admin-nav">
        <a href="<?php echo e(admin_url("index.php?page=dashboard")); ?>" class="<?php echo is_active_admin_route("dashboard") ? "active" : ""; ?>">
            <span>Dashboard</span>
            <i class="icofont-dashboard-web"></i>
        </a>
        <a href="<?php echo e(admin_url("index.php?page=content")); ?>" class="<?php echo is_active_admin_route("content") ? "active" : ""; ?>">
            <span>Content</span>
            <i class="icofont-ui-edit"></i>
        </a>
        <a href="<?php echo e(admin_url("index.php?page=donations")); ?>" class="<?php echo is_active_admin_route("donations") ? "active" : ""; ?>">
            <span>Donations</span>
            <i class="icofont-database"></i>
        </a>
        <a href="<?php echo e(admin_url("index.php?page=admins")); ?>" class="<?php echo is_active_admin_route("admins") ? "active" : ""; ?>">
            <span>Admins</span>
            <i class="icofont-users-social"></i>
        </a>
        <a href="<?php echo e(admin_url("index.php?page=settings")); ?>" class="<?php echo is_active_admin_route("settings") ? "active" : ""; ?>">
            <span>Settings</span>
            <i class="icofont-settings"></i>
        </a>
        <a href="<?php echo e(admin_url("logout.php")); ?>">
            <span>Logout</span>
            <i class="icofont-logout"></i>
        </a>
    </nav>
</aside>
