<?php

declare(strict_types=1);

require __DIR__ . "/config/bootstrap.php";

$page_title = "Events";
$breadcrumb_title = "Events";
$hero_title = "Events";
$section_title = "";
$section_url = "events.php";

$featuredEvent = featured_event();
$upcomingEvents = published_events(6, true);

require __DIR__ . "/includes/explore-header.php";
?>

<section class="wide-tb-100">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="explore-intro-card">
                    <div class="explore-kicker">Community Calendar</div>
                    <h2 class="mb-3">Purpose-driven events that bring supporters, partners, and communities together.</h2>
                    <p class="mb-4">Discover the gatherings, outreach programmes, and public moments that connect our mission with supporters, partners, and the wider community.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="contact-us.php" class="btn btn-default">Host or Sponsor an Event</a>
                        <a href="donation-page.php" class="btn btn-outline-dark">Support a Campaign</a>
                    </div>
                </div>
            </div>
            <?php if ($featuredEvent): ?>
                <div class="col-lg-6">
                    <div class="explore-panel event-hero-panel">
                        <div class="event-hero-image">
                            <img src="<?php echo e($featuredEvent["featured_image"]); ?>" alt="<?php echo e($featuredEvent["title"]); ?>">
                        </div>
                        <div class="event-hero-copy">
                            <div class="explore-kicker">Featured Gathering</div>
                            <h3><?php echo e($featuredEvent["title"]); ?></h3>
                            <p><?php echo e($featuredEvent["summary"]); ?></p>
                            <div class="event-meta-list">
                                <span><i class="icofont-calendar"></i> <?php echo e(format_event_date($featuredEvent["event_start"])); ?></span>
                                <span><i class="icofont-clock-time"></i> <?php echo e(format_event_time($featuredEvent["event_start"])); ?></span>
                                <span><i class="icofont-location-pin"></i> <?php echo e($featuredEvent["venue"]); ?></span>
                            </div>
                            <a href="event.php?slug=<?php echo urlencode($featuredEvent["slug"]); ?>" class="btn btn-default mt-4 event-hero-btn">View Event Details</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="wide-tb-100 bg-light-gray">
    <div class="container">
        <div class="row align-items-end mb-4">
            <div class="col-lg-7">
                <h1 class="heading-main mb-0">
                    <small>Upcoming Events</small>
                    Engaging experiences designed to mobilize support
                </h1>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($upcomingEvents as $event): ?>
                <div class="col-md-6 col-lg-4">
                    <article class="event-card-pro">
                        <a class="event-card-image" href="event.php?slug=<?php echo urlencode($event["slug"]); ?>">
                            <img src="<?php echo e($event["featured_image"]); ?>" alt="<?php echo e($event["title"]); ?>">
                        </a>
                        <div class="event-card-copy">
                            <div class="event-date-pill"><?php echo e(format_event_date($event["event_start"])); ?></div>
                            <h3><a href="event.php?slug=<?php echo urlencode($event["slug"]); ?>"><?php echo e($event["title"]); ?></a></h3>
                            <p><?php echo e($event["summary"]); ?></p>
                            <div class="event-meta-list compact">
                                <span><i class="icofont-clock-time"></i> <?php echo e(format_event_time($event["event_start"])); ?></span>
                                <span><i class="icofont-location-pin"></i> <?php echo e($event["city"] ?: $event["venue"]); ?></span>
                            </div>
                            <a href="event.php?slug=<?php echo urlencode($event["slug"]); ?>" class="read-more-line event-read-more"><span>Read Event</span></a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="wide-tb-100">
    <div class="container">
        <div class="explore-accent-block">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="explore-kicker">Get Involved</div>
                    <h3 class="mb-3">Attend, sponsor, or support an upcoming event.</h3>
                    <p class="mb-0">Whether you want to participate, partner with us, or help fund an event, our calendar is built to turn interest into action.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="contact-us.php" class="btn btn-default me-2">Contact Us</a>
                    <a href="donation-page.php" class="btn btn-outline-dark">Donate Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . "/includes/explore-footer.php"; ?>
