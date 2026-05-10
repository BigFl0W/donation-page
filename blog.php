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
$default_blog_image = "assets/images/blogs/blog_img_1.jpg";
$publishedCount = count($posts);
$categoryCount = count(array_filter($categories, fn($category) => (int) ($category["post_count"] ?? 0) > 0));
$tagCount = count($tags);
$activeLabel = "All Posts";

if ($categoryFilter !== "") {
    foreach ($categories as $category) {
        if (($category["slug"] ?? "") === $categoryFilter) {
            $page_title = ($category["name"] ?? "") . " | Gracious Charity Blog";
            $breadcrumb_title = $category["name"] ?? "";
            $hero_title = $category["name"] ?? "";
            $page_description = "Browse " . ($category["name"] ?? "") . " articles from Gracious Charity.";
            $canonical_url = Helpers::siteUrl("blog?category=" . urlencode($categoryFilter));
            $activeLabel = (string) ($category["name"] ?? "Category");
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
    foreach ($tags as $tag) {
        if (($tag["slug"] ?? "") === $tagFilter) {
            $activeLabel = (string) ($tag["name"] ?? "Tagged Stories");
            break;
        }
    }
}

require __DIR__ . "/includes/header.php";
?>
<style>
    .journal-shell {
        background:
            radial-gradient(circle at top right, rgba(221, 160, 41, 0.12), transparent 26%),
            linear-gradient(180deg, #fffdf9 0%, #ffffff 100%);
    }
    .journal-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.65fr);
        gap: 32px;
        align-items: stretch;
        margin-bottom: 34px;
    }
    .journal-intro {
        background: linear-gradient(145deg, #fffefb 0%, #fff8ea 100%);
        border: 1px solid rgba(162, 114, 34, 0.12);
        border-radius: 34px;
        padding: 34px;
        box-shadow: 0 24px 70px rgba(77, 56, 18, 0.08);
    }
    .journal-kicker {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        font-size: 0.82rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        font-weight: 800;
        color: #9a6b18;
        margin-bottom: 18px;
    }
    .journal-kicker::before {
        content: "";
        width: 42px;
        height: 2px;
        background: rgba(154, 107, 24, 0.55);
    }
    .journal-title {
        font-size: clamp(2.5rem, 5vw, 4.2rem);
        line-height: 0.95;
        font-family: var(--font-heading);
        color: #132238;
        margin-bottom: 18px;
    }
    .journal-title em {
        color: var(--primary-color);
        font-style: italic;
    }
    .journal-summary {
        max-width: 720px;
        font-size: 1.04rem;
        line-height: 1.85;
        color: #5d677f;
        margin-bottom: 26px;
    }
    .journal-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 28px;
    }
    .journal-pills a {
        padding: 11px 18px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid rgba(19, 34, 56, 0.09);
        color: #243047;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.25s ease;
    }
    .journal-pills a.active,
    .journal-pills a:hover {
        background: var(--secondary-color);
        color: #fff;
        border-color: var(--secondary-color);
        box-shadow: 0 12px 28px rgba(71, 119, 99, 0.18);
    }
    .journal-meta-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }
    .journal-meta-card {
        background: rgba(255, 255, 255, 0.86);
        border: 1px solid rgba(19, 34, 56, 0.08);
        border-radius: 22px;
        padding: 18px 18px 16px;
    }
    .journal-meta-card strong {
        display: block;
        font-size: 1.6rem;
        line-height: 1;
        color: #132238;
        margin-bottom: 8px;
    }
    .journal-meta-card span {
        display: block;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.16em;
        color: #7d8798;
        font-weight: 800;
    }
    .editorial-brief {
        background: #132238;
        color: #ecf3ff;
        border-radius: 34px;
        padding: 30px;
        box-shadow: 0 24px 70px rgba(19, 34, 56, 0.18);
        position: relative;
        overflow: hidden;
    }
    .editorial-brief::after {
        content: "";
        position: absolute;
        inset: auto -30px -30px auto;
        width: 160px;
        height: 160px;
        background: radial-gradient(circle, rgba(221, 160, 41, 0.28), transparent 70%);
    }
    .editorial-brief .brief-label {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 0.75rem;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        font-weight: 800;
        color: #f6c96c;
        margin-bottom: 18px;
    }
    .editorial-brief h3 {
        color: #fff;
        margin-bottom: 14px;
        font-size: 1.7rem;
    }
    .editorial-brief p {
        color: rgba(236, 243, 255, 0.82);
        line-height: 1.8;
        margin-bottom: 0;
    }
    .editorial-list {
        list-style: none;
        padding: 0;
        margin: 24px 0 0;
        display: grid;
        gap: 14px;
    }
    .editorial-list li {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        color: rgba(236, 243, 255, 0.88);
        line-height: 1.65;
    }
    .editorial-list li i {
        color: #f6c96c;
        margin-top: 4px;
    }
    .journal-featured {
        border-radius: 32px;
        overflow: hidden;
        background: #fff;
        border: 1px solid rgba(19, 34, 56, 0.08);
        box-shadow: 0 26px 70px rgba(77, 56, 18, 0.08);
        margin-bottom: 28px;
    }
    .journal-featured .post-img img {
        height: 500px;
        object-fit: cover;
    }
    .journal-featured .post-content {
        margin: -72px 28px 28px;
        border-radius: 26px;
        padding: 28px 30px;
        background: rgba(255,255,255,0.97);
        box-shadow: 0 18px 40px rgba(19, 34, 56, 0.08);
        position: relative;
    }
    .journal-story-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #7d8798;
        font-weight: 800;
        margin-bottom: 14px;
    }
    .journal-featured .post-title {
        font-size: clamp(2rem, 3vw, 3rem);
        line-height: 1.04;
        margin-bottom: 14px;
    }
    .journal-featured .post-title a,
    .journal-card .post-title a {
        color: #132238;
    }
    .journal-featured p,
    .journal-card p {
        color: #5d677f;
        line-height: 1.82;
        margin-bottom: 0;
    }
    .journal-card {
        height: 100%;
        border-radius: 28px;
        overflow: hidden;
        background: #fff;
        border: 1px solid rgba(19, 34, 56, 0.08);
        box-shadow: 0 18px 45px rgba(77, 56, 18, 0.07);
    }
    .journal-card .post-img img {
        height: 290px;
        object-fit: cover;
    }
    .journal-card .post-content {
        padding: 24px 24px 26px;
    }
    .journal-card .post-title {
        font-size: 1.8rem;
        line-height: 1.08;
        margin: 10px 0 12px;
    }
    .journal-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-top: 18px;
    }
    .journal-card-footer .category-pill {
        display: inline-flex;
        align-items: center;
        padding: 9px 14px;
        border-radius: 999px;
        background: #f5f7fb;
        color: #50607b;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .journal-sidebar-card {
        background: #fff;
        border: 1px solid rgba(19, 34, 56, 0.08);
        border-radius: 28px;
        padding: 26px 28px;
        box-shadow: 0 18px 48px rgba(77, 56, 18, 0.07);
        margin-bottom: 22px;
    }
    .journal-sidebar-card h4 {
        margin-bottom: 18px;
        color: #132238;
    }
    .journal-category-list {
        display: grid;
        gap: 10px;
    }
    .journal-category-list a {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 16px;
        border-radius: 18px;
        background: #fcfbf8;
        border: 1px solid rgba(19, 34, 56, 0.05);
        color: #243047;
        font-weight: 700;
    }
    .journal-category-list a span:last-child {
        min-width: 34px;
        height: 34px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        color: var(--primary-color);
        font-size: 0.82rem;
        font-weight: 800;
    }
    .journal-standard-copy {
        color: #5d677f;
        line-height: 1.85;
        margin-bottom: 18px;
    }
    .journal-standard-grid {
        display: grid;
        gap: 12px;
    }
    .journal-standard-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 14px 16px;
        border-radius: 18px;
        background: linear-gradient(145deg, #fffdf8 0%, #f7f9fd 100%);
        border: 1px solid rgba(19, 34, 56, 0.06);
    }
    .journal-standard-item i {
        color: var(--primary-color);
        margin-top: 3px;
    }
    .journal-standard-item strong {
        display: block;
        color: #132238;
        margin-bottom: 4px;
    }
    @media (max-width: 991px) {
        .journal-hero {
            grid-template-columns: 1fr;
        }
        .journal-meta-grid {
            grid-template-columns: 1fr;
        }
        .journal-featured .post-img img {
            height: 360px;
        }
        .journal-featured .post-content {
            margin: 0;
            border-radius: 0;
        }
    }
</style>

<section class="wide-tb-100 journal-shell">
    <div class="container">
        <div class="journal-hero">
            <div class="journal-intro">
                <div class="journal-kicker">Editorial Journal</div>
                <h1 class="journal-title">Stories from the field, <em>impact in focus.</em></h1>
                <p class="journal-summary">Browse verified updates, outreach stories, programme milestones, and community reflections from Gracious Charity. Every article is organized for clarity, shareability, and trust.</p>
                <div class="journal-pills">
                    <a class="<?php echo $categoryFilter === "" && $tagFilter === "" ? "active" : ""; ?>" href="blog.php">All Posts</a>
                    <?php foreach (array_slice($categories, 0, 3) as $category): ?>
                        <a class="<?php echo $categoryFilter === ($category["slug"] ?? "") ? "active" : ""; ?>" href="blog.php?category=<?php echo urlencode((string) ($category["slug"] ?? "")); ?>"><?php echo Helpers::e((string) ($category["name"] ?? "")); ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="journal-meta-grid">
                    <div class="journal-meta-card">
                        <strong><?php echo Helpers::e((string) $publishedCount); ?></strong>
                        <span>Published Stories</span>
                    </div>
                    <div class="journal-meta-card">
                        <strong><?php echo Helpers::e((string) $categoryCount); ?></strong>
                        <span>Active Categories</span>
                    </div>
                    <div class="journal-meta-card">
                        <strong><?php echo Helpers::e((string) $tagCount); ?></strong>
                        <span>Editorial Tags</span>
                    </div>
                </div>
            </div>
            <div class="editorial-brief">
                <div class="brief-label"><i class="icofont-heart-beat"></i> Why This Journal Matters</div>
                <h3><?php echo Helpers::e($activeLabel); ?></h3>
                <p>Follow the stories behind our outreach, relief efforts, education support, and community partnerships, all in one place.</p>
                <ul class="editorial-list">
                    <li><i class="icofont-check-circled"></i><span>See how programmes are reaching families, students, and underserved communities.</span></li>
                    <li><i class="icofont-check-circled"></i><span>Track the people, partnerships, and moments that shape our mission on the ground.</span></li>
                    <li><i class="icofont-check-circled"></i><span>Explore updates by topic so you can follow the causes that matter most to you.</span></li>
                </ul>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-8">
                <?php if ($featuredPost): ?>
                    <?php $featuredImage = (string) ($featuredPost["featured_image"] ?: $default_blog_image); ?>
                    <article class="post-wrap journal-featured">
                        <div class="post-img">
                            <a href="<?php echo Helpers::e(Helpers::postPublicUrl($featuredPost)); ?>">
                                <img src="<?php echo Helpers::e($featuredImage); ?>" alt="<?php echo Helpers::e((string) ($featuredPost["title"] ?? "")); ?>">
                            </a>
                        </div>
                        <div class="post-content">
                            <div class="journal-story-meta">
                                <span><?php echo Helpers::e(date("j M, Y", strtotime((string) ($featuredPost["published_at"] ?? "now")))); ?></span>
                                <span><?php echo Helpers::e((string) ($featuredPost["category"] ?: "Updates")); ?></span>
                                <span><?php echo Helpers::e((string) ($featuredPost["display_author"] ?? $featuredPost["author"] ?? "Admin Team")); ?></span>
                            </div>
                            <h3 class="post-title"><a href="<?php echo Helpers::e(Helpers::postPublicUrl($featuredPost)); ?>"><?php echo Helpers::e((string) ($featuredPost["title"] ?? "")); ?></a></h3>
                            <p><?php echo Helpers::e((string) ($featuredPost["excerpt"] ?? "")); ?></p>
                            <div class="journal-card-footer">
                                <span class="category-pill">Featured Story</span>
                                <a href="<?php echo Helpers::e(Helpers::postPublicUrl($featuredPost)); ?>" class="read-more-line"><span>Read More</span></a>
                            </div>
                        </div>
                    </article>
                <?php endif; ?>

                <?php if (!empty($secondaryPosts)): ?>
                    <div class="row g-4">
                        <?php foreach ($secondaryPosts as $post): ?>
                            <?php $postImage = (string) ($post["featured_image"] ?: $default_blog_image); ?>
                            <div class="col-md-6">
                                <article class="post-wrap blog-post-broken journal-card">
                                    <div class="post-img">
                                        <a href="<?php echo Helpers::e(Helpers::postPublicUrl($post)); ?>">
                                            <img src="<?php echo Helpers::e($postImage); ?>" alt="<?php echo Helpers::e((string) ($post["title"] ?? "")); ?>">
                                        </a>
                                    </div>
                                    <div class="post-content">
                                        <div class="journal-story-meta">
                                            <span><?php echo Helpers::e(date("j M, Y", strtotime((string) ($post["published_at"] ?? "now")))); ?></span>
                                            <span><?php echo Helpers::e((string) ($post["display_author"] ?? $post["author"] ?? "Admin Team")); ?></span>
                                        </div>
                                        <h3 class="post-title"><a href="<?php echo Helpers::e(Helpers::postPublicUrl($post)); ?>"><?php echo Helpers::e((string) ($post["title"] ?? "")); ?></a></h3>
                                        <p><?php echo Helpers::e((string) ($post["excerpt"] ?? "")); ?></p>
                                        <div class="journal-card-footer">
                                            <span class="category-pill"><?php echo Helpers::e((string) ($post["category"] ?: "News")); ?></span>
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
                <aside class="journal-sidebar-card">
                    <h4>Categories</h4>
                    <div class="journal-category-list">
                        <?php foreach ($categories as $category): ?>
                            <a href="blog.php?category=<?php echo urlencode((string) ($category["slug"] ?? "")); ?>">
                                <span><?php echo Helpers::e((string) ($category["name"] ?? "")); ?></span>
                                <span><?php echo Helpers::e((string) ($category["post_count"] ?? "0")); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </aside>

                <aside class="journal-sidebar-card">
                    <h4>Popular Tags</h4>
                    <div class="story-tag-cloud">
                        <?php foreach ($tags as $tag): ?>
                            <a href="blog.php?tag=<?php echo urlencode((string) ($tag["slug"] ?? "")); ?>"><?php echo Helpers::e((string) ($tag["name"] ?? "")); ?></a>
                        <?php endforeach; ?>
                    </div>
                </aside>

                <aside class="journal-sidebar-card">
                    <h4>Explore Gracious</h4>
                    <p class="journal-standard-copy">Go beyond the stories and discover the wider work happening across Gracious Charity, from live events to programmes you can support directly.</p>
                    <div class="journal-standard-grid">
                        <div class="journal-standard-item">
                            <i class="icofont-calendar"></i>
                            <div><strong>Attend Our Events</strong><span>Join upcoming outreach activities, gatherings, and community initiatives.</span></div>
                        </div>
                        <div class="journal-standard-item">
                            <i class="icofont-handshake-deal"></i>
                            <div><strong>Support a Programme</strong><span>See the causes and projects currently creating visible impact in communities.</span></div>
                        </div>
                        <div class="journal-standard-item">
                            <i class="icofont-users-social"></i>
                            <div><strong>Partner With Us</strong><span>Work with Gracious Charity as a donor, sponsor, volunteer, or strategic ally.</span></div>
                        </div>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:18px">
                        <a href="events.php" class="btn btn-default btn-sm">View Events</a>
                        <a href="causes-projects" class="btn btn-outline-primary btn-sm">Our Programmes</a>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . "/includes/footer.php"; ?>
