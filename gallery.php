<?php
require __DIR__ . "/config/autoload.php";

use App\Database;
use App\Helpers;

$galleryItems = Database::fetchAll(
    "SELECT id, title, media_type, media_path, description, status, created_at
     FROM gallery_items
     WHERE status = 'published'
     ORDER BY created_at DESC
     LIMIT 24"
) ?: [];

$featuredGalleryItem = $galleryItems[0] ?? null;
$galleryCards = array_slice($galleryItems, 1, 6);
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
                    <p class="mb-4">Browse a live collection of outreach moments, campaign highlights, and field documentation curated directly from the admin gallery.</p>
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
                                <?php if ($featuredGalleryItem): ?>
                                    <?php $featuredIsVideo = ($featuredGalleryItem['media_type'] ?? 'photo') === 'video'; ?>
                                    <?php if ($featuredIsVideo): ?>
                                        <video src="<?php echo Helpers::e($featuredGalleryItem['media_path']); ?>" controls muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>
                                    <?php else: ?>
                                        <img src="<?php echo Helpers::e($featuredGalleryItem['media_path']); ?>" alt="<?php echo Helpers::e($featuredGalleryItem['title']); ?>">
                                    <?php endif; ?>
                                    <span class="explore-video-badge"><i class="icofont-ui-play"></i> <?php echo $featuredIsVideo ? 'Featured Video' : 'Featured Photo'; ?></span>
                                <?php else: ?>
                                    <img src="assets/images/gallery/gallery_img_1.jpg" alt="Gallery feature">
                                    <span class="explore-video-badge"><i class="icofont-ui-play"></i> Featured Story</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="explore-feature-copy">
                                <div class="explore-kicker">Field Update</div>
                                <h3><?php echo Helpers::e($featuredGalleryItem['title'] ?? 'Community outreach moments worth revisiting.'); ?></h3>
                                <p><?php echo Helpers::e($featuredGalleryItem['description'] ?? 'The latest published gallery item appears here automatically as a featured field update.'); ?></p>
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
            <?php if ($galleryCards): ?>
                <?php foreach ($galleryCards as $item): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="explore-gallery-card">
                            <div class="explore-media-thumb">
                                <?php if (($item['media_type'] ?? 'photo') === 'video'): ?>
                                    <video src="<?php echo Helpers::e($item['media_path']); ?>" controls muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>
                                <?php else: ?>
                                    <img src="<?php echo Helpers::e($item['media_path']); ?>" alt="<?php echo Helpers::e($item['title']); ?>">
                                <?php endif; ?>
                                <span class="explore-photo-badge"><?php echo ($item['media_type'] ?? 'photo') === 'video' ? 'Video Highlight' : 'Photo Story'; ?></span>
                            </div>
                            <div class="copy">
                                <h3><?php echo Helpers::e($item['title']); ?></h3>
                                <p><?php echo Helpers::e($item['description'] ?: 'Published from the admin gallery and displayed automatically on the public gallery page.'); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="explore-intro-card">
                        <h3 class="mb-2">No gallery items published yet.</h3>
                        <p class="mb-0">Publish photos or videos from the admin dashboard and they will appear here automatically.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="wide-tb-100">
    <div class="container">
        <?php if ($galleryItems): ?>
            <div class="row g-4">
                <?php foreach (array_slice($galleryItems, 0, 9) as $item): ?>
                    <div class="col-md-6 col-lg-4">
                        <a class="explore-gallery-card d-block" href="<?php echo Helpers::e($item['media_path']); ?>" <?php echo ($item['media_type'] ?? 'photo') === 'video' ? '' : 'data-fancybox="gallery-feed"'; ?>>
                            <div class="explore-media-thumb">
                                <?php if (($item['media_type'] ?? 'photo') === 'video'): ?>
                                    <video src="<?php echo Helpers::e($item['media_path']); ?>" muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>
                                <?php else: ?>
                                    <img src="<?php echo Helpers::e($item['media_path']); ?>" alt="<?php echo Helpers::e($item['title']); ?>">
                                <?php endif; ?>
                                <span class="explore-photo-badge"><?php echo ($item['media_type'] ?? 'photo') === 'video' ? 'Video' : 'Photo'; ?></span>
                            </div>
                            <div class="copy">
                                <h3><?php echo Helpers::e($item['title']); ?></h3>
                                <p><?php echo Helpers::e($item['description'] ?: 'Published media item from the gallery manager.'); ?></p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . "/includes/footer.php"; ?>
