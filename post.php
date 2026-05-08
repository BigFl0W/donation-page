<?php
declare(strict_types=1);

require __DIR__ . "/config/autoload.php";

use App\Content;
use App\Helpers;

$slug = trim((string) ($_GET["slug"] ?? ""));
$categorySlug = trim((string) ($_GET["category"] ?? ""));
$post = $slug !== "" ? Content::publishedPostBySlug($slug, $categorySlug) : null;

if (!$post) {
    http_response_code(404);
    require __DIR__ . "/404-page.php";
    exit;
}

$page_title = $post["meta_title"] ?: $post["title"];
$page_description = $post["meta_description"] ?: $post["excerpt"];
$meta_keywords = $post["seo_keywords"] ?? "";
$canonical_url = $post["canonical_url"] ?: Helpers::postPublicUrl($post);
$share_image = Helpers::siteUrl((string) ($post["featured_image"] ?? ""));
$breadcrumb_title = $post["title"];
$hero_title = $post["title"];
$section_title = "Blog";
$section_url = "blog.php";
$structured_data = json_encode([
    "@context" => "https://schema.org",
    "@type" => "BlogPosting",
    "headline" => $post["title"],
    "description" => $page_description,
    "datePublished" => $post["published_at"],
    "author" => [
        "@type" => "Person",
        "name" => $post["display_author"] ?? $post["author_name"] ?? "Admin Team",
    ],
    "image" => [$share_image],
    "mainEntityOfPage" => $canonical_url,
    "publisher" => [
        "@type" => "Organization",
        "name" => "Gracious Charity",
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

require __DIR__ . "/includes/explore-header.php";
?>

<section class="wide-tb-100">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="explore-panel">
                    <div class="event-detail-hero">
                        <img src="<?php echo Helpers::e($post["featured_image"] ?? ""); ?>" alt="<?php echo Helpers::e($post["title"] ?? ""); ?>">
                    </div>
                    <div class="event-detail-copy">
                        <div class="story-meta mb-3">
                            <span><?php echo Helpers::e($post["category"] ?: "News"); ?></span>
                            <span><?php echo Helpers::e(date("j M Y", strtotime((string) ($post["published_at"] ?? "now")))); ?></span>
                            <span><?php echo Helpers::e($post["display_author"] ?? $post["author_name"] ?? "Admin Team"); ?></span>
                        </div>
                        <h2><?php echo Helpers::e($post["title"] ?? ""); ?></h2>
                        <p class="lead"><?php echo Helpers::e($post["excerpt"] ?? ""); ?></p>
                        <div class="event-detail-content">
                            <?php echo $post["content"] ?? ""; ?>
                        </div>
                        <?php if (!empty($post["tags"])): ?>
                            <div class="story-tag-row">
                                <span>Tags</span>
                                <div class="story-tag-cloud">
                                    <?php foreach ($post["tags"] as $tag): ?>
                                        <a href="blog.php?tag=<?php echo urlencode((string) ($tag["slug"] ?? "")); ?>"><?php echo Helpers::e((string) ($tag["name"] ?? "")); ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="explore-contact-card">
                    <div class="explore-kicker">Story Snapshot</div>
                    <ul class="explore-faq-list">
                        <li><i class="icofont-folder"></i><span><?php echo Helpers::e($post["category"] ?: "News"); ?></span></li>
                        <li><i class="icofont-calendar"></i><span><?php echo Helpers::e(date("j M Y", strtotime((string) ($post["published_at"] ?? "now")))); ?></span></li>
                        <li><i class="icofont-user-alt-3"></i><span><?php echo Helpers::e($post["display_author"] ?? $post["author_name"] ?? "Admin Team"); ?></span></li>
                        <li><i class="icofont-link"></i><span><?php echo Helpers::e(parse_url($canonical_url, PHP_URL_PATH) ?: ""); ?></span></li>
                    </ul>
                    <a href="blog.php" class="btn btn-default mt-3 w-100">Back to Blog</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . "/includes/explore-footer.php"; ?>
