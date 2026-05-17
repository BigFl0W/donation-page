<?php
declare(strict_types=1);

require __DIR__ . "/config/autoload.php";

use App\Content;
use App\Helpers;

$slug = trim((string) ($_GET["slug"] ?? ""));
$event = $slug !== "" ? Content::publishedEventBySlug($slug) : null;

if (!$event) {
    http_response_code(404);
    require __DIR__ . "/404-page.php";
    exit;
}

$page_title = $event["meta_title"] ?: $event["title"];
$breadcrumb_title = $event["title"];
$hero_title = $event["title"];
$section_title = "Events";
$section_url = "events.php";

require __DIR__ . "/includes/header.php";
?>

<style>
    .event-media-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }
    .event-media-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 14px 36px rgba(19, 34, 56, 0.08);
        border: 1px solid rgba(19, 34, 56, 0.08);
    }
    .event-media-card img,
    .event-media-card video {
        width: 100%;
        height: 240px;
        display: block;
        object-fit: cover;
        background: #0f172a;
    }
    .event-media-caption {
        padding: 14px 16px;
        color: #64748b;
        font-size: 0.92rem;
        line-height: 1.6;
    }
</style>

<section class="wide-tb-100">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="explore-panel">
                    <div class="event-detail-hero">
                        <img src="<?php echo Helpers::e($event["featured_image"] ?? ""); ?>" alt="<?php echo Helpers::e($event["title"] ?? ""); ?>">
                    </div>
                    <div class="event-detail-copy">
                        <div class="explore-kicker">Event Overview</div>
                        <h2><?php echo Helpers::e($event["title"] ?? ""); ?></h2>
                        <p class="lead"><?php echo Helpers::e($event["summary"] ?? ""); ?></p>
                        <div class="event-meta-list mb-4">
                            <span><i class="icofont-user-alt-3"></i> <?php echo Helpers::e($event["organizer"] ?? "Events Desk"); ?></span>
                            <?php if (!empty($event["status"])): ?><span><i class="icofont-flag-alt-2"></i> <?php echo Helpers::e(ucfirst((string)$event["status"])); ?></span><?php endif; ?>
                            <?php if (!empty($event["event_end"])): ?><span><i class="icofont-clock-time"></i> Ends <?php echo Helpers::e(Content::formatEventTime($event["event_end"])); ?></span><?php endif; ?>
                        </div>
                        <div class="event-detail-content">
                            <?php echo $event["content"] ?? ""; ?>
                        </div>
                        <?php if (!empty($event["media_gallery"])): ?>
                            <div class="event-media-gallery">
                                <?php foreach (($event["media_gallery"] ?? []) as $mediaItem): ?>
                                    <?php $mediaPath = Helpers::siteUrl((string) ($mediaItem["media_path"] ?? "")); ?>
                                    <div class="event-media-card">
                                        <?php if (($mediaItem["media_type"] ?? "image") === "video"): ?>
                                            <video controls preload="metadata">
                                                <source src="<?php echo Helpers::e($mediaPath); ?>">
                                            </video>
                                        <?php else: ?>
                                            <img src="<?php echo Helpers::e($mediaPath); ?>" alt="<?php echo Helpers::e((string) ($mediaItem["caption"] ?? $event["title"] ?? "")); ?>">
                                        <?php endif; ?>
                                        <?php if (!empty($mediaItem["caption"])): ?>
                                            <div class="event-media-caption"><?php echo Helpers::e((string) $mediaItem["caption"]); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="explore-contact-card">
                    <div class="explore-kicker">Event Details</div>
                    <ul class="explore-faq-list">
                        <li><i class="icofont-calendar"></i><span><?php echo Helpers::e(Content::formatEventDate($event["event_start"] ?? "")); ?></span></li>
                        <li><i class="icofont-clock-time"></i><span><?php echo Helpers::e(Content::formatEventTime($event["event_start"] ?? "")); ?></span></li>
                        <?php if (!empty($event["event_end"])): ?><li><i class="icofont-hour-glass"></i><span><?php echo Helpers::e(Content::formatEventTime($event["event_end"])); ?></span></li><?php endif; ?>
                        <li><i class="icofont-location-pin"></i><span><?php echo Helpers::e($event["venue"] ?? ""); ?></span></li>
                        <li><i class="icofont-map"></i><span><?php echo Helpers::e($event["city"] ?? ""); ?></span></li>
                    </ul>
                    <a href="<?php echo Helpers::e($event["registration_url"] ?? "contact-us.php"); ?>" class="btn btn-default mt-3 w-100">Register Interest</a>
                    <a href="events.php" class="btn btn-outline-dark mt-3 w-100">Back to All Events</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . "/includes/footer.php"; ?>
