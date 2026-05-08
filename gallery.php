<?php
require __DIR__ . "/config/autoload.php";
$page_title = "Photo & Video Gallery";
$breadcrumb_title = "Photo & Video Gallery";
$hero_title = "Photo & Video Gallery";
require __DIR__ . "/includes/header.php";
?>

<section class="wide-tb-100">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="explore-intro-card">
                    <div class="explore-kicker">Visual Impact</div>
                    <h2 class="mb-3">Stories from the field, captured with clarity and purpose.</h2>
                    <p class="mb-4">This page gives the platform a professional media hub where the admin can later replace images, upload video highlights, and document campaign outcomes without changing the overall structure.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="donation-page.php" class="btn btn-default">Support a Story</a>
                        <a href="contact-us.php" class="btn btn-outline-dark">Request Media Pack</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <div class="explore-panel">
                    <div class="row g-0">
                        <div class="col-md-7">
                            <div class="explore-media-thumb h-100">
                                <img src="assets/images/gallery/gallery_img_1.jpg" alt="Gallery feature">
                                <span class="explore-video-badge"><i class="icofont-ui-play"></i> Featured Video</span>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="explore-feature-copy">
                                <div class="explore-kicker">Field Update</div>
                                <h3>Community outreach moments worth revisiting.</h3>
                                <p>Use this area for monthly recaps, donor-ready reports, and campaign media summaries.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="wide-tb-100 bg-light-gray">
    <div class="container">
        <div class="row align-items-end mb-4">
            <div class="col-lg-7">
                <h1 class="heading-main mb-0">
                    <small>Featured Collection</small>
                    Campaigns, events, and programme highlights
                </h1>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="explore-gallery-card">
                    <div class="explore-media-thumb">
                        <img src="assets/images/gallery/gallery_img_2.jpg" alt="Community support">
                        <span class="explore-photo-badge">Photo Story</span>
                    </div>
                    <div class="copy">
                        <h3>Community Support Drive</h3>
                        <p>A professional gallery slot for relief distribution, outreach, and volunteer action shots.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="explore-gallery-card">
                    <div class="explore-media-thumb">
                        <img src="assets/images/gallery/gallery_img_4.jpg" alt="Children learning">
                        <span class="explore-photo-badge">Video Highlight</span>
                    </div>
                    <div class="copy">
                        <h3>Education in Action</h3>
                        <p>Ideal for classroom clips, project walkthroughs, or interviews with beneficiaries.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="explore-gallery-card">
                    <div class="explore-media-thumb">
                        <img src="assets/images/gallery/gallery_img_7.jpg" alt="Field operations">
                        <span class="explore-photo-badge">Event Album</span>
                    </div>
                    <div class="copy">
                        <h3>Field Operations</h3>
                        <p>Useful for milestone moments, launch-day media, and transparent impact storytelling.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="wide-tb-100">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="explore-stat-card">
                    <span class="number">120+</span>
                    <p>Images can be organized into albums for projects, outreach events, and partner activities.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="explore-stat-card">
                    <span class="number">24</span>
                    <p>Campaign videos can be spotlighted for supporters, media, and grant applications.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="explore-stat-card">
                    <span class="number">1 Hub</span>
                    <p>One consistent page can hold media resources now, while the admin updates content later.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . "/includes/footer.php"; ?>
