<?php

declare(strict_types=1);

$action = $_GET["action"] ?? "list";
$eventId = (int) ($_GET["id"] ?? 0);
$isEditing = $action === "edit" && $eventId > 0;
$pdoReady = database_available() && db_table_exists("events");
$error = "";

$form = [
    "id" => 0,
    "title" => "",
    "slug" => "",
    "summary" => "",
    "content" => "",
    "featured_image" => "assets/images/events/event_img_1.jpg",
    "venue" => "",
    "city" => "",
    "event_start" => date("Y-m-d\TH:i"),
    "event_end" => date("Y-m-d\TH:i", strtotime("+4 hours")),
    "registration_url" => "contact-us.php",
    "status" => "draft",
    "is_featured" => 0,
];

if ($isEditing && $pdoReady) {
    $record = db_fetch_one("SELECT * FROM events WHERE id = :id LIMIT 1", ["id" => $eventId]);
    if ($record) {
        $form = array_merge($form, $record);
        $form["event_start"] = $record["event_start"] ? date("Y-m-d\TH:i", strtotime((string) $record["event_start"])) : "";
        $form["event_end"] = $record["event_end"] ? date("Y-m-d\TH:i", strtotime((string) $record["event_end"])) : "";
    }
}

$publicEventUrl = $form["slug"] !== "" ? site_url("event.php?slug=" . urlencode((string) $form["slug"])) : site_url("events.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save_event"])) {
    $title = trim((string) ($_POST["title"] ?? ""));
    $slug = slugify((string) ($_POST["slug"] ?? $title));
    $summary = trim((string) ($_POST["summary"] ?? ""));
    $content = trim((string) ($_POST["content"] ?? ""));
    $featuredImage = trim((string) ($_POST["featured_image"] ?? ""));
    $venue = trim((string) ($_POST["venue"] ?? ""));
    $city = trim((string) ($_POST["city"] ?? ""));
    $eventStart = trim((string) ($_POST["event_start"] ?? ""));
    $eventEnd = trim((string) ($_POST["event_end"] ?? ""));
    $registrationUrl = trim((string) ($_POST["registration_url"] ?? ""));
    $status = trim((string) ($_POST["status"] ?? "draft"));
    $isFeatured = isset($_POST["is_featured"]) ? 1 : 0;

    $form = [
        "id" => (int) ($_POST["id"] ?? 0),
        "title" => $title,
        "slug" => $slug,
        "summary" => $summary,
        "content" => $content,
        "featured_image" => $featuredImage,
        "venue" => $venue,
        "city" => $city,
        "event_start" => $eventStart,
        "event_end" => $eventEnd,
        "registration_url" => $registrationUrl,
        "status" => $status,
        "is_featured" => $isFeatured,
    ];

    if (!$pdoReady) {
        $error = "Database connection is not ready. Import the schema first.";
    } elseif ($title === "" || $eventStart === "" || $content === "") {
        $error = "Title, event start, and content are required.";
    } else {
        $params = [
            "title" => $title,
            "slug" => $slug,
            "summary" => $summary,
            "content" => $content,
            "featured_image" => $featuredImage,
            "venue" => $venue,
            "city" => $city,
            "event_start" => str_replace("T", " ", $eventStart) . ":00",
            "event_end" => $eventEnd !== "" ? str_replace("T", " ", $eventEnd) . ":00" : null,
            "registration_url" => $registrationUrl,
            "status" => $status,
            "is_featured" => $isFeatured,
            "meta_title" => "Events | " . $title,
            "meta_description" => mb_substr($summary !== "" ? $summary : strip_tags($content), 0, 250),
        ];

        if ($form["id"] > 0) {
            $params["id"] = $form["id"];
            db_execute(
                "UPDATE events
                 SET title = :title, slug = :slug, summary = :summary, content = :content,
                     featured_image = :featured_image, venue = :venue, city = :city,
                     event_start = :event_start, event_end = :event_end, registration_url = :registration_url,
                     status = :status, is_featured = :is_featured, meta_title = :meta_title,
                     meta_description = :meta_description
                 WHERE id = :id",
                $params
            );
        } else {
            db_execute(
                "INSERT INTO events
                    (created_by, title, slug, summary, content, featured_image, venue, city, event_start, event_end, registration_url, status, is_featured, meta_title, meta_description)
                 VALUES
                    (1, :title, :slug, :summary, :content, :featured_image, :venue, :city, :event_start, :event_end, :registration_url, :status, :is_featured, :meta_title, :meta_description)",
                $params
            );
        }

        header("Location: " . admin_url("index.php?page=events"));
        exit;
    }
}

$events = admin_events();
?>
<div class="admin-topbar">
    <div>
        <h2>Events</h2>
        <p>Manage event publishing, registration links, venues, and featured campaign visibility.</p>
    </div>
    <div class="admin-actions">
        <a class="admin-btn light" href="../events.php" target="_blank">Open Events</a>
        <a class="admin-btn primary" href="<?php echo e(admin_url("index.php?page=events&action=create")); ?>">New Event</a>
    </div>
</div>

<?php if ($action === "create" || $isEditing): ?>
    <?php if ($error !== ""): ?>
        <div class="admin-alert error"><?php echo e($error); ?></div>
    <?php endif; ?>
    <div class="post-edit-layout">
        <div class="post-edit-main">
            <section class="admin-table-card">
                <div class="admin-section-title">
                    <h3><?php echo $isEditing ? "Edit Event" : "Create Event"; ?></h3>
                </div>
                <form method="post">
                    <input type="hidden" name="id" value="<?php echo e((string) $form["id"]); ?>">
                    <div class="admin-grid-2">
                        <div class="admin-form-group">
                            <label for="event-title">Title</label>
                            <input id="event-title" name="title" type="text" value="<?php echo e((string) $form["title"]); ?>" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="event-slug">Slug</label>
                            <input id="event-slug" name="slug" type="text" value="<?php echo e((string) $form["slug"]); ?>" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="event-venue">Venue</label>
                            <input id="event-venue" name="venue" type="text" value="<?php echo e((string) $form["venue"]); ?>">
                        </div>
                        <div class="admin-form-group">
                            <label for="event-city">City</label>
                            <input id="event-city" name="city" type="text" value="<?php echo e((string) $form["city"]); ?>">
                        </div>
                        <div class="admin-form-group">
                            <label for="event-start">Event Start</label>
                            <input id="event-start" name="event_start" type="datetime-local" value="<?php echo e((string) $form["event_start"]); ?>" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="event-end">Event End</label>
                            <input id="event-end" name="event_end" type="datetime-local" value="<?php echo e((string) $form["event_end"]); ?>">
                        </div>
                        <div class="admin-form-group">
                            <label for="event-registration">Registration URL</label>
                            <input id="event-registration" name="registration_url" type="text" value="<?php echo e((string) $form["registration_url"]); ?>">
                        </div>
                        <div class="admin-form-group">
                            <label for="event-image">Featured Image</label>
                            <input id="event-image" name="featured_image" type="text" value="<?php echo e((string) $form["featured_image"]); ?>">
                        </div>
                    </div>
                    <div class="admin-form-group">
                        <label for="event-summary">Summary</label>
                        <input id="event-summary" name="summary" type="text" value="<?php echo e((string) $form["summary"]); ?>">
                    </div>
                    <div class="admin-form-group">
                        <label for="event-content">Content</label>
                        <textarea id="event-content" name="content" rows="10"><?php echo e((string) $form["content"]); ?></textarea>
                    </div>
                    <div class="admin-grid-2">
                        <div class="admin-form-group">
                            <label for="event-status">Status</label>
                            <input id="event-status" name="status" type="text" value="<?php echo e((string) $form["status"]); ?>">
                        </div>
                        <div class="admin-form-group">
                            <label class="d-inline-flex align-items-center gap-2">
                                <input type="checkbox" name="is_featured" value="1" <?php echo ((int) $form["is_featured"] === 1) ? "checked" : ""; ?>>
                                <span>Featured event</span>
                            </label>
                        </div>
                    </div>
                    <button type="submit" name="save_event" class="admin-btn primary">Save Event</button>
                    <a href="<?php echo e(admin_url("index.php?page=events")); ?>" class="admin-btn light">Back</a>
                </form>
            </section>
        </div>

        <aside class="post-edit-side">
            <section class="admin-panel side-panel">
                <div class="admin-section-title">
                    <h3>Event Visibility</h3>
                </div>
                <div class="side-panel-content">
                    <div class="publish-stat"><i class="icofont-link"></i> Public URL</div>
                    <div class="admin-helper"><?php echo e($publicEventUrl); ?></div>
                    <div class="publish-stat mt-3"><i class="icofont-ui-calendar"></i> Start date</div>
                    <div class="admin-helper"><?php echo e($form["event_start"] !== "" ? (string) $form["event_start"] : "Not scheduled"); ?></div>
                    <div class="publish-stat mt-3"><i class="icofont-star"></i> Featured</div>
                    <div class="admin-helper"><?php echo ((int) $form["is_featured"] === 1) ? "Yes, highlighted on the public site." : "Standard event listing."; ?></div>
                </div>
            </section>

            <section class="admin-panel side-panel">
                <div class="admin-section-title">
                    <h3>Event Checklist</h3>
                </div>
                <div class="side-panel-content">
                    <ul class="admin-plain-list">
                        <li>
                            <div>
                                <strong>Venue details</strong>
                                <span>Make sure the location is accurate for attendees.</span>
                            </div>
                            <span class="admin-chip">Required</span>
                        </li>
                        <li>
                            <div>
                                <strong>Registration link</strong>
                                <span>Use a direct CTA or internal contact path.</span>
                            </div>
                            <span class="admin-chip">Optional</span>
                        </li>
                        <li>
                            <div>
                                <strong>Summary copy</strong>
                                <span>Keep the preview concise for list pages and SEO.</span>
                            </div>
                            <span class="admin-chip">Recommended</span>
                        </li>
                    </ul>
                </div>
            </section>
        </aside>
    </div>
<?php else: ?>
    <div class="admin-workspace-grid">
        <div class="admin-workspace-main">
            <section class="admin-panel">
                <div class="admin-panel-head">
                    <div>
                        <h3>Event Overview</h3>
                        <p>Current publishing and scheduling status across active events</p>
                    </div>
                </div>
                <div class="admin-summary-grid">
                    <div class="admin-summary-metric">
                        <span>Total Events</span>
                        <strong><?php echo e((string) count($events)); ?></strong>
                        <small>All records</small>
                    </div>
                    <div class="admin-summary-metric">
                        <span>Featured</span>
                        <strong><?php echo e((string) count(array_filter($events, static fn(array $event): bool => (int) ($event["is_featured"] ?? 0) === 1))); ?></strong>
                        <small>Homepage priority</small>
                    </div>
                    <div class="admin-summary-metric">
                        <span>Published</span>
                        <strong><?php echo e((string) count(array_filter($events, static fn(array $event): bool => ($event["status"] ?? "") === "published"))); ?></strong>
                        <small>Live on site</small>
                    </div>
                </div>
            </section>

            <section class="admin-table-card">
                <div class="admin-section-title">
                    <h3>Upcoming and Published Events</h3>
                </div>
                <table class="admin-table admin-table-clean">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Venue</th>
                        <th>Start</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td>
                                <strong><?php echo e((string) $event["title"]); ?></strong>
                                <div class="admin-listing-meta">
                                    <span><?php echo e((string) $event["slug"]); ?></span>
                                </div>
                            </td>
                            <td><?php echo e((string) ($event["venue"] ?? "")); ?></td>
                            <td><?php echo e(isset($event["event_start"]) ? (string) $event["event_start"] : ""); ?></td>
                            <td><span class="admin-badge <?php echo (($event["status"] ?? "") === "published") ? "success" : "warning"; ?>"><?php echo e((string) $event["status"]); ?></span></td>
                            <td><?php echo ((int) ($event["is_featured"] ?? 0) === 1) ? "Yes" : "No"; ?></td>
                            <td><a class="admin-btn light" href="<?php echo e(admin_url("index.php?page=events&action=edit&id=" . (string) $event["id"])); ?>">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>

        <aside class="admin-workspace-side">
            <section class="admin-panel">
                <div class="admin-panel-head">
                    <div>
                        <h3>Event Actions</h3>
                    </div>
                </div>
                <ul class="admin-plain-list">
                    <li>
                        <div>
                            <strong>Create event</strong>
                            <span>Add a new public event with venue and schedule details.</span>
                        </div>
                        <a class="admin-btn light" href="<?php echo e(admin_url("index.php?page=events&action=create")); ?>">Open</a>
                    </li>
                    <li>
                        <div>
                            <strong>Public events page</strong>
                            <span>Review how current listings appear on the website.</span>
                        </div>
                        <a class="admin-btn light" href="../events.php" target="_blank">View</a>
                    </li>
                </ul>
            </section>

            <section class="admin-panel">
                <div class="admin-panel-head">
                    <div>
                        <h3>Checklist</h3>
                    </div>
                </div>
                <div class="admin-note-box">
                    <strong>Professional event publishing</strong>
                    <span>Use strong summaries, a confirmed venue, and a clean registration destination for every live event.</span>
                </div>
            </section>
        </aside>
    </div>
<?php endif; ?>
