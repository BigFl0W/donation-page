<?php

declare(strict_types=1);

$pageTitle = "Dashboard";
$pageDescription = "Operational overview for content, fundraising, and platform activity.";
$statsConfig = app_config("stats", []);

/**
 * @return array<int, array{key:string,label:string,compare_key:string}>
 */
function dashboard_month_windows(int $count = 7): array
{
    $months = [];

    for ($offset = $count - 1; $offset >= 0; $offset--) {
        $current = strtotime("-{$offset} month");
        $comparison = strtotime("-12 month", $current);
        $months[] = [
            "key" => date("Y-m", $current),
            "label" => date("M", $current),
            "compare_key" => date("Y-m", $comparison),
        ];
    }

    return $months;
}

function dashboard_amount_label(float $amount, string $currency = "NGN"): string
{
    $absAmount = abs($amount);

    if ($absAmount >= 1000000) {
        return sprintf("%s %.1fM", $currency, $amount / 1000000);
    }

    if ($absAmount >= 1000) {
        return sprintf("%s %.0fK", $currency, $amount / 1000);
    }

    return sprintf("%s %.0f", $currency, $amount);
}

function dashboard_time_ago(?string $value): string
{
    if (!$value) {
        return "Recently";
    }

    $timestamp = strtotime($value);

    if ($timestamp === false) {
        return "Recently";
    }

    $diff = time() - $timestamp;

    if ($diff < 60) {
        return "Just now";
    }

    if ($diff < 3600) {
        return floor($diff / 60) . " min ago";
    }

    if ($diff < 86400) {
        return floor($diff / 3600) . " hrs ago";
    }

    return date("j M", $timestamp);
}

/**
 * @param array<int, float|int> $values
 */
function dashboard_svg_path(array $values, float $width, float $height, float $paddingX = 40, float $paddingY = 22): string
{
    if ($values === []) {
        return "";
    }

    $max = max($values);
    $max = $max > 0 ? $max : 1;
    $count = count($values);
    $stepX = $count > 1 ? ($width - ($paddingX * 2)) / ($count - 1) : 0;
    $baseline = $height - $paddingY;
    $usableHeight = $height - ($paddingY * 2);
    $points = [];

    foreach (array_values($values) as $index => $value) {
        $x = $paddingX + ($stepX * $index);
        $y = $baseline - (($value / $max) * $usableHeight);
        $points[] = [$x, $y];
    }

    if (count($points) === 1) {
        return sprintf("M%.2f %.2f", $points[0][0], $points[0][1]);
    }

    $path = sprintf("M%.2f %.2f", $points[0][0], $points[0][1]);

    for ($i = 1, $len = count($points); $i < $len; $i++) {
        $prev = $points[$i - 1];
        $point = $points[$i];
        $midX = ($prev[0] + $point[0]) / 2;
        $path .= sprintf(
            " C%.2f %.2f, %.2f %.2f, %.2f %.2f",
            $midX,
            $prev[1],
            $midX,
            $point[1],
            $point[0],
            $point[1]
        );
    }

    return $path;
}

/**
 * @param array<int, float|int> $values
 */
function dashboard_svg_area(array $values, float $width, float $height, float $paddingX = 40, float $paddingY = 22): string
{
    $line = dashboard_svg_path($values, $width, $height, $paddingX, $paddingY);

    if ($line === "") {
        return "";
    }

    $baseline = $height - $paddingY;

    return $line . sprintf(" L%.2f %.2f L%.2f %.2f Z", $width - $paddingX, $baseline, $paddingX, $baseline);
}

$dashboard = [
    "total_donations" => (string) ($statsConfig["total_donations"] ?? "NGN 0"),
    "monthly_donations" => (string) ($statsConfig["monthly_donations"] ?? "NGN 0"),
    "active_programmes" => (int) ($statsConfig["active_programmes"] ?? 0),
    "partners" => (int) ($statsConfig["partners"] ?? 0),
    "notifications" => [],
    "team_members" => [],
    "activity_rows" => [],
    "publishing_mix" => [
        "Blog Articles" => 0,
        "Events" => 0,
        "Programmes" => 0,
        "Partners" => 0,
        "FAQs" => 0,
    ],
    "upcoming_events" => [],
    "recent_donations" => [],
    "months" => dashboard_month_windows(7),
    "series_current" => [],
    "series_previous" => [],
    "gateway_mix" => [
        "Paystack" => 0,
        "Stripe" => 0,
        "Manual" => 0,
    ],
];

if (database_available()) {
    $totalDonationRow = db_fetch_one(
        "SELECT COALESCE(SUM(amount), 0) AS total, COALESCE(MAX(currency), 'NGN') AS currency
         FROM donations
         WHERE status = 'successful'"
    );
    $monthlyDonationRow = db_fetch_one(
        "SELECT COALESCE(SUM(amount), 0) AS total, COALESCE(MAX(currency), 'NGN') AS currency
         FROM donations
         WHERE status = 'successful'
           AND YEAR(COALESCE(paid_at, created_at)) = YEAR(CURRENT_DATE())
           AND MONTH(COALESCE(paid_at, created_at)) = MONTH(CURRENT_DATE())"
    );
    $activeProgrammeRow = db_fetch_one("SELECT COUNT(*) AS total FROM programmes WHERE status = 'published'");
    $partnerRow = db_fetch_one("SELECT COUNT(*) AS total FROM partners WHERE status = 'published'");

    $dashboard["total_donations"] = dashboard_amount_label((float) ($totalDonationRow["total"] ?? 0), (string) ($totalDonationRow["currency"] ?? "NGN"));
    $dashboard["monthly_donations"] = dashboard_amount_label((float) ($monthlyDonationRow["total"] ?? 0), (string) ($monthlyDonationRow["currency"] ?? "NGN"));
    $dashboard["active_programmes"] = (int) ($activeProgrammeRow["total"] ?? 0);
    $dashboard["partners"] = (int) ($partnerRow["total"] ?? 0);

    $teamMembers = db_fetch_all(
        "SELECT a.full_name, a.email, a.status, a.created_at, r.name AS role_name
         FROM admins a
         LEFT JOIN roles r ON r.id = a.role_id
         ORDER BY COALESCE(a.last_login_at, a.created_at) DESC, a.created_at DESC
         LIMIT 5"
    );

    $dashboard["team_members"] = array_map(
        static function (array $member): array {
            $initials = "";
            foreach (preg_split('/\s+/', (string) ($member["full_name"] ?? "")) ?: [] as $piece) {
                if ($piece !== "") {
                    $initials .= strtoupper(substr($piece, 0, 1));
                }
            }

            return [
                "name" => (string) ($member["full_name"] ?? "Admin"),
                "role" => ucwords(str_replace("_", " ", (string) ($member["role_name"] ?? "admin"))),
                "initials" => substr($initials !== "" ? $initials : "AD", 0, 2),
            ];
        },
        $teamMembers
    );

    $recentPosts = db_fetch_all(
        "SELECT p.title, p.status, p.updated_at, COALESCE(a.full_name, p.author_name, 'Admin Team') AS owner
         FROM posts p
         LEFT JOIN admins a ON a.id = p.author_id
         ORDER BY p.updated_at DESC
         LIMIT 4"
    );
    $recentEvents = db_fetch_all(
        "SELECT title, status, updated_at, event_start
         FROM events
         ORDER BY updated_at DESC
         LIMIT 4"
    );
    $recentDonations = db_fetch_all(
        "SELECT donor_name, amount, currency, gateway, status, COALESCE(paid_at, created_at) AS activity_at
         FROM donations
         ORDER BY COALESCE(paid_at, created_at) DESC
         LIMIT 4"
    );

    $activityFeed = [];
    foreach ($recentPosts as $post) {
        $activityFeed[] = [
            "title" => "Post updated: " . (string) $post["title"],
            "time" => (string) $post["updated_at"],
        ];
    }
    foreach ($recentEvents as $event) {
        $activityFeed[] = [
            "title" => "Event managed: " . (string) $event["title"],
            "time" => (string) $event["updated_at"],
        ];
    }
    foreach ($recentDonations as $donation) {
        $activityFeed[] = [
            "title" => "Donation " . strtoupper((string) $donation["status"]) . ": " . dashboard_amount_label((float) $donation["amount"], (string) $donation["currency"]),
            "time" => (string) $donation["activity_at"],
        ];
    }

    usort(
        $activityFeed,
        static fn(array $left, array $right): int => strtotime((string) $right["time"]) <=> strtotime((string) $left["time"])
    );

    $dashboard["notifications"] = array_map(
        static fn(array $item): array => [
            "title" => $item["title"],
            "time" => dashboard_time_ago((string) $item["time"]),
        ],
        array_slice($activityFeed, 0, 4)
    );

    $postActivityRows = db_fetch_all(
        "SELECT p.title AS item, COALESCE(a.full_name, p.author_name, 'Admin Team') AS owner, p.published_at AS activity_date, p.status, 'Post' AS item_type
         FROM posts p
         LEFT JOIN admins a ON a.id = p.author_id
         ORDER BY COALESCE(p.published_at, p.updated_at) DESC
         LIMIT 4"
    );
    $eventActivityRows = db_fetch_all(
        "SELECT e.title AS item, COALESCE(a.full_name, 'Events Desk') AS owner, e.event_start AS activity_date, e.status, 'Event' AS item_type
         FROM events e
         LEFT JOIN admins a ON a.id = e.created_by
         ORDER BY e.event_start ASC
         LIMIT 4"
    );
    $mergedActivity = array_merge($postActivityRows, $eventActivityRows);
    usort(
        $mergedActivity,
        static fn(array $left, array $right): int => strtotime((string) $right["activity_date"]) <=> strtotime((string) $left["activity_date"])
    );
    $dashboard["activity_rows"] = array_map(
        static fn(array $row): array => [
            "item" => (string) $row["item"],
            "owner" => (string) $row["owner"],
            "date" => date("M d, Y", strtotime((string) $row["activity_date"])),
            "status" => ucfirst((string) $row["status"]),
            "type" => (string) $row["item_type"],
        ],
        array_slice($mergedActivity, 0, 5)
    );

    $publishingMixRows = [
        "Blog Articles" => (int) ((db_fetch_one("SELECT COUNT(*) AS total FROM posts WHERE status = 'published'")["total"] ?? 0)),
        "Events" => (int) ((db_fetch_one("SELECT COUNT(*) AS total FROM events WHERE status = 'published'")["total"] ?? 0)),
        "Programmes" => (int) ((db_fetch_one("SELECT COUNT(*) AS total FROM programmes WHERE status = 'published'")["total"] ?? 0)),
        "Partners" => (int) ((db_fetch_one("SELECT COUNT(*) AS total FROM partners WHERE status = 'published'")["total"] ?? 0)),
        "FAQs" => (int) ((db_fetch_one("SELECT COUNT(*) AS total FROM faqs WHERE status = 'published'")["total"] ?? 0)),
    ];
    $dashboard["publishing_mix"] = $publishingMixRows;

    $upcomingEvents = db_fetch_all(
        "SELECT title, venue, city, event_start, status
         FROM events
         WHERE event_start >= NOW()
         ORDER BY event_start ASC
         LIMIT 4"
    );
    $dashboard["upcoming_events"] = array_map(
        static fn(array $event): array => [
            "title" => (string) $event["title"],
            "meta" => trim((string) (($event["venue"] ?? "") . (($event["city"] ?? "") !== "" ? ", " . $event["city"] : "")), ", "),
            "date" => date("j M Y", strtotime((string) $event["event_start"])),
            "status" => ucfirst((string) $event["status"]),
        ],
        $upcomingEvents
    );

    $dashboard["recent_donations"] = array_map(
        static fn(array $donation): array => [
            "donor" => (string) ($donation["donor_name"] ?: "Anonymous Donor"),
            "amount" => dashboard_amount_label((float) $donation["amount"], (string) $donation["currency"]),
            "gateway" => ucfirst((string) $donation["gateway"]),
            "status" => ucfirst((string) $donation["status"]),
        ],
        $recentDonations
    );

    $gatewayRows = db_fetch_all(
        "SELECT gateway, COUNT(*) AS total
         FROM donations
         GROUP BY gateway"
    );
    foreach ($gatewayRows as $gatewayRow) {
        $label = ucfirst((string) $gatewayRow["gateway"]);
        if (isset($dashboard["gateway_mix"][$label])) {
            $dashboard["gateway_mix"][$label] = (int) $gatewayRow["total"];
        }
    }

    $monthCountsCurrent = array_fill_keys(array_column($dashboard["months"], "key"), 0);
    $monthCountsPrevious = array_fill_keys(array_column($dashboard["months"], "compare_key"), 0);
    $publishingDates = db_fetch_all(
        "SELECT published_at AS activity_date FROM posts WHERE status = 'published' AND published_at IS NOT NULL
         UNION ALL
         SELECT event_start AS activity_date FROM events WHERE status IN ('published', 'completed')"
    );

    foreach ($publishingDates as $dateRow) {
        $monthKey = date("Y-m", strtotime((string) $dateRow["activity_date"]));

        if (array_key_exists($monthKey, $monthCountsCurrent)) {
            $monthCountsCurrent[$monthKey]++;
        }

        if (array_key_exists($monthKey, $monthCountsPrevious)) {
            $monthCountsPrevious[$monthKey]++;
        }
    }

    $dashboard["series_current"] = array_values($monthCountsCurrent);
    $dashboard["series_previous"] = array_values($monthCountsPrevious);
}

if ($dashboard["team_members"] === []) {
    $dashboard["team_members"] = [
        ["name" => "Admin", "role" => "Super Admin", "initials" => "AD"],
    ];
}

$maxMixValue = max($dashboard["publishing_mix"]) ?: 1;
$maxGatewayValue = max($dashboard["gateway_mix"]) ?: 1;
$currentPath = dashboard_svg_path($dashboard["series_current"], 760, 260);
$previousPath = dashboard_svg_path($dashboard["series_previous"], 760, 260);
$currentArea = dashboard_svg_area($dashboard["series_current"], 760, 260);
?>
<section class="admin-dashboard-shell">
    <div class="admin-dashboard-topbar">
        <div class="admin-dashboard-crumbs">
            <span>Home</span>
            <i class="icofont-simple-right"></i>
            <span>Dashboard</span>
            <i class="icofont-simple-right"></i>
            <?php $logoPath = Helpers::brandLogoPath(); ?>
            <?php if ($logoPath): ?>
                <strong><?php echo e(Helpers::brandName()); ?></strong>
            <?php else: ?>
                <strong><?php echo e(Helpers::brandName()); ?></strong>
            <?php endif; ?>
        </div>
        <div class="admin-dashboard-tools">
            <label class="admin-dashboard-search">
                <i class="icofont-search-1"></i>
                <input type="text" value="" placeholder="Search content, donations, events">
            </label>
            <a class="admin-icon-btn" href="../index.php" target="_blank" aria-label="View site">
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
                    <a class="admin-btn light" href="../index.php" target="_blank">View Site</a>
                    <a class="admin-btn primary" href="<?php echo e(admin_url("index.php?page=posts&action=create")); ?>">New Post</a>
                </div>
            </div>

            <div class="admin-dashboard-stats">
                <article class="admin-mini-stat lavender">
                    <span>Total Donations</span>
                    <strong><?php echo e((string) $dashboard["total_donations"]); ?></strong>
                    <small>Successful transactions</small>
                </article>
                <article class="admin-mini-stat sand">
                    <span>This Month</span>
                    <strong><?php echo e((string) $dashboard["monthly_donations"]); ?></strong>
                    <small>Current month collections</small>
                </article>
                <article class="admin-mini-stat blue">
                    <span>Active Programmes</span>
                    <strong><?php echo e((string) $dashboard["active_programmes"]); ?></strong>
                    <small>Published programme records</small>
                </article>
                <article class="admin-mini-stat mint">
                    <span>Partners</span>
                    <strong><?php echo e((string) $dashboard["partners"]); ?></strong>
                    <small>Published partner profiles</small>
                </article>
            </div>

            <div class="admin-dashboard-analytics">
                <section class="admin-analytics-card admin-analytics-wide">
                    <div class="admin-section-head">
                        <div>
                            <h3>Publishing Performance</h3>
                            <p>Posts and events across the last seven months</p>
                        </div>
                        <div class="admin-dot-legend">
                            <span><i class="solid"></i>This period</span>
                            <span><i class="dashed"></i>Same period last year</span>
                        </div>
                    </div>
                    <div class="admin-line-chart">
                        <svg viewBox="0 0 760 260" preserveAspectRatio="none" aria-hidden="true">
                            <g class="grid">
                                <line x1="40" y1="30" x2="730" y2="30"></line>
                                <line x1="40" y1="90" x2="730" y2="90"></line>
                                <line x1="40" y1="150" x2="730" y2="150"></line>
                                <line x1="40" y1="210" x2="730" y2="210"></line>
                            </g>
                            <?php if ($currentArea !== ""): ?>
                                <path class="area" d="<?php echo e($currentArea); ?>"></path>
                            <?php endif; ?>
                            <?php if ($currentPath !== ""): ?>
                                <path class="line main" d="<?php echo e($currentPath); ?>"></path>
                            <?php endif; ?>
                            <?php if ($previousPath !== ""): ?>
                                <path class="line alt" d="<?php echo e($previousPath); ?>"></path>
                            <?php endif; ?>
                        </svg>
                        <div class="admin-chart-months">
                            <?php foreach ($dashboard["months"] as $month): ?>
                                <span><?php echo e($month["label"]); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <section class="admin-analytics-card admin-analytics-narrow">
                    <div class="admin-section-head compact">
                        <div>
                            <h3>Publishing Mix</h3>
                            <p>Live content across the platform</p>
                        </div>
                    </div>
                    <ul class="admin-distribution-list">
                        <?php foreach ($dashboard["publishing_mix"] as $label => $value): ?>
                            <li>
                                <span><?php echo e($label); ?> (<?php echo e((string) $value); ?>)</span>
                                <b><i style="width: <?php echo e((string) max(8, (int) round(($value / $maxMixValue) * 100))); ?>%"></i></b>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            </div>

            <div class="admin-dashboard-secondary">
                <section class="admin-analytics-card">
                    <div class="admin-section-head compact">
                        <div>
                            <h3>Donation Channels</h3>
                            <p>Distribution of recorded gateway activity</p>
                        </div>
                    </div>
                    <div class="admin-bar-chart">
                        <?php foreach ($dashboard["gateway_mix"] as $value): ?>
                            <span style="height: <?php echo e((string) max(12, (int) round(($value / $maxGatewayValue) * 100))); ?>%"></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="admin-chart-months" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
                        <?php foreach (array_keys($dashboard["gateway_mix"]) as $gatewayLabel): ?>
                            <span><?php echo e($gatewayLabel); ?></span>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="admin-analytics-card">
                    <div class="admin-section-head compact">
                        <div>
                            <h3>Upcoming Events</h3>
                            <p>Next scheduled public-facing activities</p>
                        </div>
                    </div>
                    <ul class="admin-plain-list">
                        <?php if ($dashboard["upcoming_events"] !== []): ?>
                            <?php foreach ($dashboard["upcoming_events"] as $event): ?>
                                <li>
                                    <div>
                                        <strong><?php echo e($event["title"]); ?></strong>
                                        <span><?php echo e($event["meta"] !== "" ? $event["meta"] : "Venue to be confirmed"); ?></span>
                                    </div>
                                    <span class="admin-chip"><?php echo e($event["date"]); ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>
                                <div>
                                    <strong>No upcoming events</strong>
                                    <span>Create the next public event to populate this panel.</span>
                                </div>
                                <span class="admin-chip">Pending</span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </section>
            </div>

            <section class="admin-analytics-card admin-activity-table">
                <div class="admin-section-head">
                    <div>
                        <h3>Publishing Activity</h3>
                        <p>Recent posts and event records from the live database</p>
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
                    <?php foreach ($dashboard["activity_rows"] as $row): ?>
                        <tr>
                            <td>
                                <strong><?php echo e($row["item"]); ?></strong>
                                <div class="admin-listing-meta">
                                    <span><?php echo e($row["type"]); ?></span>
                                </div>
                            </td>
                            <td><?php echo e($row["owner"]); ?></td>
                            <td><?php echo e($row["date"]); ?></td>
                            <td>
                                <span class="admin-status-pill <?php echo e(strtolower($row["status"])); ?>">
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
                        <h3>Recent Activity</h3>
                    </div>
                </div>
                <ul class="admin-rail-list">
                    <?php foreach ($dashboard["notifications"] as $note): ?>
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
                        <h3>Admins Online Scope</h3>
                    </div>
                </div>
                <ul class="admin-user-list">
                    <?php foreach ($dashboard["team_members"] as $member): ?>
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
                        <h3>Recent Donations</h3>
                    </div>
                </div>
                <ul class="admin-plain-list">
                    <?php if ($dashboard["recent_donations"] !== []): ?>
                        <?php foreach ($dashboard["recent_donations"] as $donation): ?>
                            <li>
                                <div>
                                    <strong><?php echo e($donation["donor"]); ?></strong>
                                    <span><?php echo e($donation["gateway"]); ?> / <?php echo e($donation["status"]); ?></span>
                                </div>
                                <span class="admin-chip"><?php echo e($donation["amount"]); ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>
                            <div>
                                <strong>No donations recorded yet</strong>
                                <span>Successful transactions will appear here when they start coming in.</span>
                            </div>
                            <span class="admin-chip">Awaiting</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </section>
        </aside>
    </div>
</section>
