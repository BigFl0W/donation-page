<?php

declare(strict_types=1);

$pageTitle = "Dashboard";
$pageDescription = "Operational overview for content, fundraising, and platform activity.";
$stats = app_config("stats", []);
?>
<div class="admin-topbar">
    <div>
        <h2>Dashboard</h2>
        <p><?php echo e($pageDescription); ?></p>
    </div>
    <div class="admin-actions">
        <a class="admin-btn light" href="../gallery.php" target="_blank">View Site</a>
        <a class="admin-btn primary" href="<?php echo e(admin_url("index.php?page=posts&action=create")); ?>">New Post</a>
    </div>
</div>

<section class="admin-kpi-row mb-4">
    <div class="admin-kpi-card">
        <div class="label">Total Donations</div>
        <div class="value"><?php echo e((string) $stats["total_donations"]); ?></div>
        <p class="meta">All channels</p>
    </div>
    <div class="admin-kpi-card">
        <div class="label">This Month</div>
        <div class="value"><?php echo e((string) $stats["monthly_donations"]); ?></div>
        <p class="meta">Current period</p>
    </div>
    <div class="admin-kpi-card">
        <div class="label">Active Programmes</div>
        <div class="value"><?php echo e((string) $stats["active_programmes"]); ?></div>
        <p class="meta">Published initiatives</p>
    </div>
    <div class="admin-kpi-card">
        <div class="label">Partners</div>
        <div class="value"><?php echo e((string) $stats["partners"]); ?></div>
        <p class="meta">Active records</p>
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

    <div class="admin-list-card admin-operations-card">
        <div class="admin-section-title">
            <h3>Publishing Queue</h3>
        </div>
        <ul class="admin-list">
            <li>
                <div>
                    <strong>Blog</strong>
                    <div class="admin-helper">Create or revise articles with categories, tags, and SEO fields.</div>
                </div>
                <a class="admin-btn light" href="<?php echo e(admin_url("index.php?page=posts")); ?>">Open</a>
            </li>
            <li>
                <div>
                    <strong>Events</strong>
                    <div class="admin-helper">Manage upcoming schedules, featured entries, and registration links.</div>
                </div>
                <a class="admin-btn light" href="<?php echo e(admin_url("index.php?page=events")); ?>">Open</a>
            </li>
            <li>
                <div>
                    <strong>Site content</strong>
                    <div class="admin-helper">Maintain homepage sections, partners, FAQs, and reusable content blocks.</div>
                </div>
                <a class="admin-btn light" href="<?php echo e(admin_url("index.php?page=content")); ?>">Open</a>
            </li>
        </ul>
    </div>
</section>

<section class="admin-grid-3">
    <div class="admin-note-card admin-note-premium">
        <h3>Editorial</h3>
        <p class="admin-note-copy">Posts now support structured categories, stored tags, cleaner URLs, and SEO metadata.</p>
    </div>
    <div class="admin-note-card admin-note-premium">
        <h3>Operations</h3>
        <p class="admin-note-copy">The dashboard is organized around day-to-day publishing and review tasks rather than onboarding prompts.</p>
    </div>
    <div class="admin-note-card admin-note-premium">
        <h3>Finance</h3>
        <p class="admin-note-copy">Donation reporting is in place, with room to connect live gateway totals and exports next.</p>
    </div>
</section>
