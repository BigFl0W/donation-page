<?php

declare(strict_types=1);

$pageTitle = "Dashboard";
$pageDescription = "Monitor donations, content activity, payment channels, and admin operations.";
$stats = app_config("stats", []);
?>
<div class="admin-topbar">
    <div>
        <h2>Dashboard</h2>
        <p><?php echo e($pageDescription); ?></p>
    </div>
    <div class="admin-actions">
        <a class="admin-btn light" href="../gallery.php" target="_blank">View Site</a>
        <a class="admin-btn primary" href="<?php echo e(admin_url("index.php?page=content")); ?>">Update Content</a>
    </div>
</div>

<section class="admin-grid mb-4">
    <div class="admin-card">
        <div class="label">Total Donations</div>
        <div class="value"><?php echo e((string) $stats["total_donations"]); ?></div>
        <p class="meta">Combined Paystack and Stripe collections</p>
    </div>
    <div class="admin-card">
        <div class="label">This Month</div>
        <div class="value"><?php echo e((string) $stats["monthly_donations"]); ?></div>
        <p class="meta">Latest campaign collections</p>
    </div>
    <div class="admin-card">
        <div class="label">Active Programmes</div>
        <div class="value"><?php echo e((string) $stats["active_programmes"]); ?></div>
        <p class="meta">Content-driven programme count</p>
    </div>
    <div class="admin-card">
        <div class="label">Partners</div>
        <div class="value"><?php echo e((string) $stats["partners"]); ?></div>
        <p class="meta">Live partner or sponsor profiles</p>
    </div>
</section>

<section class="admin-grid-2 mb-4">
    <div class="admin-table-card">
        <div class="admin-section-title">
            <h3>Recent Donations</h3>
        </div>
        <table class="admin-table">
            <thead>
            <tr>
                <th>Donor</th>
                <th>Gateway</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Ada M.</td>
                <td>Paystack</td>
                <td>NGN 120,000</td>
                <td><span class="admin-badge success">Successful</span></td>
            </tr>
            <tr>
                <td>Global Reach Ltd.</td>
                <td>Stripe</td>
                <td>USD 2,500</td>
                <td><span class="admin-badge success">Successful</span></td>
            </tr>
            <tr>
                <td>James K.</td>
                <td>Paystack</td>
                <td>NGN 35,000</td>
                <td><span class="admin-badge warning">Pending</span></td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="admin-list-card">
        <div class="admin-section-title">
            <h3>Quick Operations</h3>
        </div>
        <ul class="admin-list">
            <li>
                <div>
                    <strong>Content publishing</strong>
                    <div class="admin-helper">Update homepage, Explore pages, FAQs, and partners.</div>
                </div>
                <a class="admin-btn light" href="<?php echo e(admin_url("index.php?page=content")); ?>">Open</a>
            </li>
            <li>
                <div>
                    <strong>Admin management</strong>
                    <div class="admin-helper">Add new admins and control role permissions.</div>
                </div>
                <a class="admin-btn light" href="<?php echo e(admin_url("index.php?page=admins")); ?>">Open</a>
            </li>
            <li>
                <div>
                    <strong>Gateway review</strong>
                    <div class="admin-helper">Inspect Paystack and Stripe transactions from one panel.</div>
                </div>
                <a class="admin-btn light" href="<?php echo e(admin_url("index.php?page=donations")); ?>">Open</a>
            </li>
        </ul>
    </div>
</section>

<section class="admin-grid-3">
    <div class="admin-note-card">
        <h3>Platform Health</h3>
        <ul>
            <li>Admin scaffold is now isolated from the public site templates.</li>
            <li>Database hooks are prepared in `config/database.php`.</li>
            <li>Next step is to replace sample cards with real database queries.</li>
        </ul>
    </div>
    <div class="admin-note-card">
        <h3>Payments</h3>
        <ul>
            <li>Track Paystack references, Stripe payment intents, and refunds.</li>
            <li>Store raw gateway payloads for easier debugging.</li>
            <li>Expose export-ready donation reports in the finance area.</li>
        </ul>
    </div>
    <div class="admin-note-card">
        <h3>Team Access</h3>
        <ul>
            <li>Super admin can manage every other role.</li>
            <li>Editors stay focused on content without finance access.</li>
            <li>Finance users can audit donations without editing pages.</li>
        </ul>
    </div>
</section>
