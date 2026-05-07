<?php

declare(strict_types=1);

require __DIR__ . "/config/bootstrap.php";

$categoryFilter = slugify((string) ($_GET["category"] ?? ""));
$tagFilter = slugify((string) ($_GET["tag"] ?? ""));
$posts = published_posts_filtered(12, $categoryFilter, $tagFilter);
$featuredPost = $posts[0] ?? null;
$secondaryPosts = array_slice($posts, 1);
$categories = blog_category_summaries();
$tags = blog_tag_summaries();

$page_title = "Blog | Gracious Charity";
$breadcrumb_title = "Blog";
$hero_title = "Blog";
$section_title = "";
$section_url = "blog.php";
$page_description = "News, impact stories, announcements, and programme updates from Gracious Charity.";
$meta_keywords = "charity blog, non-profit news, impact stories, outreach updates";
$canonical_url = site_url("blog");
$share_image = site_url("assets/images/blogs/blog_img_1.jpg");

if ($categoryFilter !== "") {
    foreach ($categories as $category) {
        if ($category["slug"] === $categoryFilter) {
            $page_title = $category["name"] . " | Gracious Charity Blog";
            $breadcrumb_title = $category["name"];
            $hero_title = $category["name"];
            $page_description = "Browse " . $category["name"] . " articles from Gracious Charity.";
            $canonical_url = site_url("blog?category=" . urlencode($categoryFilter));
            break;
        }
    }
}

if ($tagFilter !== "") {
    $page_title = "Tagged Stories | Gracious Charity Blog";
    $breadcrumb_title = "Tagged Stories";
    $hero_title = "Tagged Stories";
    $page_description = "Explore tagged Gracious Charity stories and updates.";
    $canonical_url = site_url("blog?tag=" . urlencode($tagFilter));
}

require __DIR__ . "/includes/explore-header.php";
?>

<section class="wide-tb-100">
    <div class="container">
        <div class="row align-items-end mb-4">
            <div class="col-lg-8">
                <h1 class="heading-main mb-0">
                    <small>News & Stories</small>
                    Credible updates, impact stories, and field insights
                </h1>
            </div>
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="story-filter-pills justify-content-lg-end">
                    <a class="<?php echo $categoryFilter === "" && $tagFilter === "" ? "active" : ""; ?>" href="blog.php">All Posts</a>
                    <?php foreach (array_slice($categories, 0, 3) as $category): ?>
                        <a class="<?php echo $categoryFilter === $category["slug"] ? "active" : ""; ?>" href="blog.php?category=<?php echo urlencode((string) $category["slug"]); ?>"><?php echo e((string) $category["name"]); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-8">
                <?php if ($featuredPost): ?>
                    <article class="story-hero-card mb-4">
                        <a class="story-hero-media" href="<?php echo e(post_public_url($featuredPost)); ?>">
                            <img src="<?php echo e((string) $featuredPost["featured_image"]); ?>" alt="<?php echo e((string) $featuredPost["title"]); ?>">
                        </a>
                        <div class="story-hero-copy">
                            <div class="story-meta">
                                <span><?php echo e((string) ($featuredPost["category"] ?: "Updates")); ?></span>
                                <span><?php echo e(date("j M Y", strtotime((string) $featuredPost["published_at"]))); ?></span>
                                <span><?php echo e((string) ($featuredPost["display_author"] ?? "Admin Team")); ?></span>
                            </div>
                            <h2><a href="<?php echo e(post_public_url($featuredPost)); ?>"><?php echo e((string) $featuredPost["title"]); ?></a></h2>
                            <p><?php echo e((string) $featuredPost["excerpt"]); ?></p>
                            <a href="<?php echo e(post_public_url($featuredPost)); ?>" class="btn btn-default">Read Full Story</a>
                        </div>
                    </article>
                <?php endif; ?>

                <div class="row g-4">
                    <?php foreach ($secondaryPosts as $post): ?>
                        <div class="col-md-6">
                            <article class="story-card-pro">
                                <a class="story-card-image" href="<?php echo e(post_public_url($post)); ?>">
                                    <img src="<?php echo e((string) $post["featured_image"]); ?>" alt="<?php echo e((string) $post["title"]); ?>">
                                </a>
                                <div class="story-card-copy">
                                    <div class="story-meta">
                                        <span><?php echo e((string) ($post["category"] ?: "News")); ?></span>
                                        <span><?php echo e(date("j M Y", strtotime((string) $post["published_at"]))); ?></span>
                                    </div>
                                    <h3><a href="<?php echo e(post_public_url($post)); ?>"><?php echo e((string) $post["title"]); ?></a></h3>
                                    <p><?php echo e((string) $post["excerpt"]); ?></p>
                                    <a href="<?php echo e(post_public_url($post)); ?>" class="read-more-line"><span>Read Story</span></a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <aside class="explore-contact-card story-sidebar-card mb-4">
                    <div class="explore-kicker">Categories</div>
                    <ul class="story-sidebar-list">
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="blog.php?category=<?php echo urlencode((string) $category["slug"]); ?>">
                                    <span><?php echo e((string) $category["name"]); ?></span>
                                    <strong><?php echo e((string) $category["post_count"]); ?></strong>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </aside>

                <aside class="explore-contact-card story-sidebar-card mb-4">
                    <div class="explore-kicker">Popular Tags</div>
                    <div class="story-tag-cloud">
                        <?php foreach ($tags as $tag): ?>
                            <a href="blog.php?tag=<?php echo urlencode((string) $tag["slug"]); ?>"><?php echo e((string) $tag["name"]); ?></a>
                        <?php endforeach; ?>
                    </div>
                </aside>

                <aside class="explore-contact-card story-sidebar-card">
                    <div class="explore-kicker">Publishing Standard</div>
                    <p>Every article is organized with structured categories, stored tags, and SEO-ready metadata for better search visibility and cleaner sharing links.</p>
                </aside>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . "/includes/explore-footer.php"; ?>
