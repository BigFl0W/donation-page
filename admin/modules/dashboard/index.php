<?php

declare(strict_types=1);

$pageTitle = "Dashboard";
$pageDescription = "Operational overview for content, fundraising, and platform activity.";
$stats = app_config("stats", []);
$currentAdmin = current_admin();

$notifications = [
    ["title" => "Homepage content updated", "time" => "10 min ago"],
    ["title" => "New donation recorded", "time" => "42 min ago"],
    ["title" => "Event registration increased", "time" => "2 hrs ago"],
    ["title" => "Blog SEO metadata saved", "time" => "Today"],
];

$teamMembers = [
    ["name" => "Admin", "role" => "Super Admin", "initials" => "AD"],
    ["name" => "Programme Office", "role" => "Editorial", "initials" => "PO"],
    ["name" => "Finance Desk", "role" => "Finance", "initials" => "FD"],
    ["name" => "Communications", "role" => "Publishing", "initials" => "CM"],
];

$activityRows = [
    ["item" => "Learning Support Initiative", "owner" => "Programme Office", "date" => "May 05, 2026", "status" => "Published"],
    ["item" => "Community Outreach Drive", "owner" => "Communications", "date" => "May 07, 2026", "status" => "Scheduled"],
    ["item" => "Partner Impact Update", "owner" => "Admin", "date" => "May 08, 2026", "status" => "Review"],
];
?>
<section class="admin-dashboard-shell">
    <div class="admin-dashboard-topbar">
        <div class="admin-dashboard-crumbs">
            <span>Home</span>
            <i class="icofont-simple-right"></i>
            <span>Dashboard</span>
            <i class="icofont-simple-right"></i>
            <strong>Gracious Charity Admin</strong>
        </div>
        <div class="admin-dashboard-tools">
            <label class="admin-dashboard-search">
                <i class="icofont-search-1"></i>
                <input type="text" value="" placeholder="Search content, donations, events">
            </label>
            <a class="admin-icon-btn" href="../gallery.php" target="_blank" aria-label="View site">
                <i class="icofont-external-link"></i>
            </a>
            <a class="admin-icon-btn" href="<?php echo e(admin_url("index.php?page=posts&action=create")); ?>" aria-label="Create post">
                <i class="icofont-plus"></i>
            </a>
        </div>
    </div>

    <div class="admin-dashboard-grid">
        <div class="admin-dashboard-main">
            <div class="admin-dashboard-pagehead">
                <div>
                    <h2>Dashboard</h2>
                    <p><?php echo e($pageDescription); ?></p>
                </div>
                <div class="admin-dashboard-head-actions">
                    <a class="admin-btn light" href="../gallery.php" target="_blank">View Site</a>
                    <a class="admin-btn primary" href="<?php echo e(admin_url("index.php?page=posts&action=create")); ?>">New Post</a>
                </div>
            </div>

            <div class="admin-dashboard-stats">
                <article class="admin-mini-stat lavender">
                    <span>Total Donations</span>
                    <strong><?php echo e((string) $stats["total_donations"]); ?></strong>
                    <small>+11.01%</small>
                </article>
                <article class="admin-mini-stat sand">
                    <span>This Month</span>
                    <strong><?php echo e((string) $stats["monthly_donations"]); ?></strong>
                    <small>-0.03%</small>
                </article>
                <article class="admin-mini-stat blue">
                    <span>Active Programmes</span>
                    <strong><?php echo e((string) $stats["active_programmes"]); ?></strong>
                    <small>+15.03%</small>
                </article>
                <article class="admin-mini-stat mint">
                    <span>Partners</span>
                    <strong><?php echo e((string) $stats["partners"]); ?></strong>
                    <small>+6.08%</small>
                </article>
            </div>

            <div class="admin-dashboard-analytics">
                <section class="admin-analytics-card admin-analytics-wide">
                    <div class="admin-section-head">
                        <div>
                            <h3>Publishing Performance</h3>
                            <p>Posts, campaigns, and public engagement trends</p>
                        </div>
                        <div class="admin-dot-legend">
                            <span><i class="solid"></i>This year</span>
                            <span><i class="dashed"></i>Last year</span>
                        </div>
                    </div>
                    <div class="admin-line-chart">
                        <svg viewBox="0 0 760 260" preserveAspectRatio="none" aria-hidden="true">
                            <defs>
                                <linearGradient id="dashboardArea" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="rgba(71,119,99,0.18)"></stop>
                                    <stop offset="100%" stop-color="rgba(71,119,99,0.02)"></stop>
                                </linearGradient>
                            </defs>
                            <g class="grid">
                                <line x1="40" y1="30" x2="730" y2="30"></line>
                                <line x1="40" y1="90" x2="730" y2="90"></line>
                                <line x1="40" y1="150" x2="730" y2="150"></line>
                                <line x1="40" y1="210" x2="730" y2="210"></line>
                            </g>
                            <path class="area" d="M40 180 C95 110,140 210,190 170 S285 150,340 105 S445 55,500 118 S590 180,645 125 S700 95,730 92 L730 230 L40 230 Z"></path>
                            <path class="line main" d="M40 180 C95 110,140 210,190 170 S285 150,340 105 S445 55,500 118 S590 180,645 125 S700 95,730 92"></path>
                            <path class="line alt" d="M40 205 C95 138,140 102,190 124 S285 80,340 150 S445 170,500 112 S590 130,645 72 S700 85,730 44"></path>
                        </svg>
                        <div class="admin-chart-months">
                            <span>Jan</span>
                            <span>Feb</span>
                            <span>Mar</span>
                            <span>Apr</span>
                            <span>May</span>
                            <span>Jun</span>
                            <span>Jul</span>
                        </div>
                    </div>
                </section>

                <section class="admin-analytics-card admin-analytics-narrow">
                    <div class="admin-section-head compact">
                        <div>
                            <h3>Publishing Mix</h3>
                            <p>Where activity is concentrated</p>
                        </div>
                    </div>
                    <ul class="admin-distribution-list">
                        <li><span>Blog Articles</span><b><i style="width: 72%"></i></b></li>
                        <li><span>Events</span><b><i style="width: 48%"></i></b></li>
                        <li><span>Programmes</span><b><i style="width: 66%"></i></b></li>
                        <li><span>Partners</span><b><i style="width: 38%"></i></b></li>
                        <li><span>FAQs</span><b><i style="width: 29%"></i></b></li>
                    </ul>
                </section>
            </div>

            <div class="admin-dashboard-secondary">
                <section class="admin-analytics-card">
                    <div class="admin-section-head compact">
                        <div>
                            <h3>Monthly Publishing</h3>
                            <p>Editorial volume by month</p>
                        </div>
                    </div>
                    <div class="admin-bar-chart">
                        <span style="height: 54%"></span>
                        <span style="height: 86%"></span>
                        <span style="height: 68%"></span>
                        <span style="height: 92%"></span>
                        <span style="height: 44%"></span>
                        <span style="height: 73%"></span>
                    </div>
                    <div class="admin-chart-months">
                        <span>Jan</span>
                        <span>Feb</span>
                        <span>Mar</span>
                        <span>Apr</span>
                        <span>May</span>
                        <span>Jun</span>
                    </div>
                </section>

                <section class="admin-analytics-card">
                    <div class="admin-section-head compact">
                        <div>
                            <h3>Traffic Sources</h3>
                            <p>How visitors reach the platform</p>
                        </div>
                    </div>
                    <div class="admin-donut-wrap">
                        <div class="admin-donut-chart"></div>
                        <ul class="admin-traffic-key">
                            <li><i class="gold"></i><span>Search</span><strong>52.1%</strong></li>
                            <li><i class="green"></i><span>Direct</span><strong>22.8%</strong></li>
                            <li><i class="blue"></i><span>Social</span><strong>13.9%</strong></li>
                            <li><i class="soft"></i><span>Email</span><strong>11.2%</strong></li>
                        </ul>
                    </div>
                </section>
            </div>

            <section class="admin-analytics-card admin-activity-table">
                <div class="admin-section-head">
                    <div>
                        <h3>Publishing Activity</h3>
                        <p>Current content and campaign workflow</p>
                    </div>
                </div>
                <table class="admin-table admin-table-clean">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th>Owner</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($activityRows as $row): ?>
                        <tr>
                            <td><?php echo e($row["item"]); ?></td>
                            <td><?php echo e($row["owner"]); ?></td>
                            <td><?php echo e($row["date"]); ?></td>
                            <td>
                                <span class="admin-status-pill <?php echo strtolower($row["status"]); ?>">
                                    <?php echo e($row["status"]); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>

        <aside class="admin-dashboard-rail">
            <section class="admin-rail-card">
                <div class="admin-section-head compact">
                    <div>
                        <h3>Notifications</h3>
                    </div>
                </div>
                <ul class="admin-rail-list">
                    <?php foreach ($notifications as $note): ?>
                        <li>
                            <i class="icofont-notepad"></i>
                            <div>
                                <strong><?php echo e($note["title"]); ?></strong>
                                <span><?php echo e($note["time"]); ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="admin-rail-card">
                <div class="admin-section-head compact">
                    <div>
                        <h3>Users</h3>
                    </div>
                </div>
                <ul class="admin-user-list">
                    <?php foreach ($teamMembers as $member): ?>
                        <li>
                            <span class="avatar"><?php echo e($member["initials"]); ?></span>
                            <div>
                                <strong><?php echo e($member["name"]); ?></strong>
                                <span><?php echo e($member["role"]); ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="admin-rail-card">
                <div class="admin-section-head compact">
                    <div>
                        <h3>Operational Health</h3>
                    </div>
                </div>
                <div class="admin-signal-bars">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="admin-rail-foot">
                    <span>Editorial</span>
                    <span>SEO</span>
                    <span>Traffic</span>
                </div>
            </section>
        </aside>
    </div>
</section>
