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
                        <div class="event-detail-content">
                            <?php echo $event["content"] ?? ""; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="explore-contact-card">
                    <div class="explore-kicker">Event Details</div>
                    <ul class="explore-faq-list">
                        <li><i class="icofont-calendar"></i><span><?php echo Helpers::e(Content::formatEventDate($event["event_start"] ?? "")); ?></span></li>
                        <li><i class="icofont-clock-time"></i><span><?php echo Helpers::e(Content::formatEventTime($event["event_start"] ?? "")); ?></span></li>
                        <li><i class="icofont-location-pin"></i><span><?php echo Helpers::e($event["venue"] ?? ""); ?></span></li>
                        <li><i class="icofont-map"></i><span><?php echo Helpers::e($event["city"] ?? ""); ?></span></li>
                    </ul>
                    <a href="<?php echo Helpers::e($event["registration_url"] ?? "contact-us.php"); ?>" class="btn btn-default mt-3 w-100">Register Interest</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . "/includes/footer.php"; ?>
