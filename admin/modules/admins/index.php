<?php

declare(strict_types=1);

$roles = app_config("roles", []);
$adminRows = [];

if (admin_database_ready()) {
    $adminRows = db_fetch_all(
        "SELECT a.full_name, a.email, a.status, r.name AS role_name
         FROM admins a
         INNER JOIN roles r ON r.id = a.role_id
         ORDER BY a.created_at ASC"
    );
}

if ($adminRows === []) {
    $seedAdmins = app_config("default_admins", []);

    foreach ($seedAdmins as $admin) {
        $adminRows[] = [
            "full_name" => $admin["name"],
            "email" => $admin["email"],
            "role_name" => $admin["role"],
            "status" => $admin["status"],
        ];
    }
}
?>
<div class="admin-topbar">
    <div>
        <h2>Admins & Roles</h2>
        <p>Add platform managers, define roles, and keep access auditable.</p>
    </div>
    <div class="admin-actions">
        <a class="admin-btn primary" href="#">Add Admin</a>
    </div>
</div>

<section class="admin-grid-2">
    <div class="admin-table-card">
        <div class="admin-section-title">
            <h3>Current Admin Accounts</h3>
        </div>
        <table class="admin-table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($adminRows as $admin): ?>
                <tr>
                    <td><?php echo e($admin["full_name"]); ?></td>
                    <td><?php echo e($admin["email"]); ?></td>
                    <td><?php echo e($admin["role_name"]); ?></td>
                    <td><span class="admin-badge success"><?php echo e($admin["status"]); ?></span></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td>Content Editor</td>
                <td>editor@graciouscharity.org</td>
                <td>editor</td>
                <td><span class="admin-badge info">Planned</span></td>
            </tr>
            <tr>
                <td>Finance Officer</td>
                <td>finance@graciouscharity.org</td>
                <td>finance</td>
                <td><span class="admin-badge info">Planned</span></td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="admin-list-card">
        <div class="admin-section-title">
            <h3>Role Permissions</h3>
        </div>
        <ul class="admin-list">
            <?php foreach ($roles as $role => $description): ?>
                <li>
                    <div>
                        <strong><?php echo e($role); ?></strong>
                        <div class="admin-helper"><?php echo e($description); ?></div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
