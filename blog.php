<?php
declare(strict_types=1);

require __DIR__ . "/config/autoload.php";

use App\Content;
use App\Helpers;

$categoryParam = trim((string) ($_GET["category"] ?? ""));
$tagParam = trim((string) ($_GET["tag"] ?? ""));
$categoryFilter = $categoryParam !== "" ? Helpers::slugify($categoryParam) : "";
$tagFilter = $tagParam !== "" ? Helpers::slugify($tagParam) : "";
$posts = Content::publishedPostsFiltered(12, $categoryFilter, $tagFilter);
$featuredPost = $posts[0] ?? null;
$secondaryPosts = array_slice($posts, 1);
$categories = Content::blogCategorySummaries();
$tags = Content::blogTagSummaries();

$page_title = "Blog | Gracious Charity";
$breadcrumb_title = "Blog";
$hero_title = "Blog";
$section_title = "";
$section_url = "blog.php";
$page_description = "News, impact stories, announcements, and programme updates from Gracious Charity.";
$meta_keywords = "charity blog, non-profit news, impact stories, outreach updates";
$canonical_url = Helpers::siteUrl("blog");
$share_image = Helpers::siteUrl("assets/images/blogs/blog_img_1.jpg");

if ($categoryFilter !== "") {
    foreach ($categories as $category) {
        if (($category["slug"] ?? "") === $categoryFilter) {
            $page_title = ($category["name"] ?? "") . " | Gracious Charity Blog";
            $breadcrumb_title = $category["name"] ?? "";
            $hero_title = $category["name"] ?? "";
            $page_description = "Browse " . ($category["name"] ?? "") . " articles from Gracious Charity.";
            $canonical_url = Helpers::siteUrl("blog?category=" . urlencode($categoryFilter));
            break;
        }
    }
}

if ($tagFilter !== "") {
    $page_title = "Tagged Stories | Gracious Charity Blog";
    $breadcrumb_title = "Tagged Stories";
    $hero_title = "Tagged Stories";
    $page_description = "Explore tagged Gracious Charity stories and updates.";
    $canonical_url = Helpers::siteUrl("blog?tag=" . urlencode($tagFilter));
}

require __DIR__ . "/includes/header.php";
?>

<section class="wide-tb-100">
    <div class="container">
        <div class="row align-items-end mb-4">
            <div class="col-lg-8">
                <h1 class="heading-main mb-0">
                    <small>News & Stories</small>
                    Some Of Our Recent Stories & News Blog
                </h1>
            </div>
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="story-filter-pills justify-content-lg-end">
                    <a class="<?php echo $categoryFilter === "" && $tagFilter === "" ? "active" : ""; ?>" href="blog.php">All Posts</a>
                    <?php foreach (array_slice($categories, 0, 3) as $category): ?>
                        <a class="<?php echo $categoryFilter === ($category["slug"] ?? "") ? "active" : ""; ?>" href="blog.php?category=<?php echo urlencode((string) ($category["slug"] ?? "")); ?>"><?php echo Helpers::e((string) ($category["name"] ?? "")); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-8">
                <?php if ($featuredPost): ?>
                    <article class="post-wrap">
                        <div class="post-img">
                            <a href="<?php echo Helpers::e(Helpers::postPublicUrl($featuredPost)); ?>">
                                <img src="<?php echo Helpers::e((string) ($featuredPost["featured_image"] ?? "")); ?>" alt="<?php echo Helpers::e((string) ($featuredPost["title"] ?? "")); ?>">
                            </a>
                        </div>
                        <div class="post-content">
                            <div class="post-date"><?php echo Helpers::e(date("j M, Y", strtotime((string) ($featuredPost["published_at"] ?? "now")))); ?></div>
                            <h3 class="post-title"><a href="<?php echo Helpers::e(Helpers::postPublicUrl($featuredPost)); ?>"><?php echo Helpers::e((string) ($featuredPost["title"] ?? "")); ?></a></h3>
                            <div class="post-category"><?php echo Helpers::e((string) ($featuredPost["category"] ?: "Updates")); ?> / <?php echo Helpers::e((string) ($featuredPost["display_author"] ?? $featuredPost["author"] ?? "Admin Team")); ?></div>
                            <p><?php echo Helpers::e((string) ($featuredPost["excerpt"] ?? "")); ?></p>
                            <div class="text-md-end">
                                <a href="<?php echo Helpers::e(Helpers::postPublicUrl($featuredPost)); ?>" class="read-more-line"><span>Read More</span></a>
                            </div>
                        </div>
                    </article>
                <?php endif; ?>

                <?php if (!empty($secondaryPosts)): ?>
                    <div class="row">
                        <?php foreach ($secondaryPosts as $post): ?>
                            <div class="col-md-6">
                                <article class="post-wrap blog-post-broken">
                                    <div class="post-img">
                                        <a href="<?php echo Helpers::e(Helpers::postPublicUrl($post)); ?>">
                                            <img src="<?php echo Helpers::e((string) ($post["featured_image"] ?? "")); ?>" alt="<?php echo Helpers::e((string) ($post["title"] ?? "")); ?>">
                                        </a>
                                    </div>
                                    <div class="post-content">
                                        <div class="post-date"><?php echo Helpers::e(date("j M, Y", strtotime((string) ($post["published_at"] ?? "now")))); ?></div>
                                        <h3 class="post-title"><a href="<?php echo Helpers::e(Helpers::postPublicUrl($post)); ?>"><?php echo Helpers::e((string) ($post["title"] ?? "")); ?></a></h3>
                                        <div class="post-category"><?php echo Helpers::e((string) ($post["category"] ?: "News")); ?></div>
                                        <div class="text-md-end">
                                            <a href="<?php echo Helpers::e(Helpers::postPublicUrl($post)); ?>" class="read-more-line"><span>Read More</span></a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($posts)): ?>
                    <div class="explore-contact-card">
                        <div class="explore-kicker">No Articles Found</div>
                        <h3 class="mb-3">No stories match this filter yet.</h3>
                        <p class="mb-0">Try another category or tag, or return to the full blog archive to browse all published updates.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <aside class="blog-list-categories rounded mb-4">
                    <h4 class="mb-4">Categories</h4>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($categories as $category): ?>
                            <li class="d-flex justify-content-between align-items-center py-2">
                                <a href="blog.php?category=<?php echo urlencode((string) ($category["slug"] ?? "")); ?>">
                                    <?php echo Helpers::e((string) ($category["name"] ?? "")); ?>
                                </a>
                                <strong><?php echo Helpers::e((string) ($category["post_count"] ?? "0")); ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </aside>

                <aside class="blog-list-categories rounded mb-4">
                    <h4 class="mb-4">Popular Tags</h4>
                    <div class="story-tag-cloud">
                        <?php foreach ($tags as $tag): ?>
                            <a href="blog.php?tag=<?php echo urlencode((string) ($tag["slug"] ?? "")); ?>"><?php echo Helpers::e((string) ($tag["name"] ?? "")); ?></a>
                        <?php endforeach; ?>
                    </div>
                </aside>

                <aside class="blog-list-categories rounded">
                    <h4 class="mb-4">Publishing Standard</h4>
                    <p>Every article is organized with structured categories, stored tags, and SEO-ready metadata for better search visibility and cleaner sharing links.</p>
                </aside>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . "/includes/footer.php"; ?>
