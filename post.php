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
$post_image = Helpers::siteUrl((string) ($post["featured_image"] ?: "assets/images/blogs/blog_img_1.jpg"));
$share_image = $post_image;
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

<style>
    .article-shell {
        background: radial-gradient(circle at top right, rgba(221, 160, 41, 0.05), transparent 30%),
                    linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        padding-top: 60px;
    }
    .article-hero {
        position: relative;
        border-radius: 32px;
        overflow: hidden;
        margin-bottom: 0;
        box-shadow: 0 24px 70px rgba(19, 34, 56, 0.08);
    }
    .article-hero img {
        width: 100%;
        height: 60vh;
        min-height: 450px;
        max-height: 650px;
        object-fit: cover;
        display: block;
    }
    .article-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(19, 34, 56, 0.95) 0%, rgba(19, 34, 56, 0.4) 50%, rgba(0,0,0,0.1) 100%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 60px;
    }
    .article-category {
        display: inline-flex;
        align-items: center;
        padding: 10px 20px;
        border-radius: 999px;
        background: var(--primary-color);
        color: #fff;
        font-weight: 800;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 24px;
        align-self: flex-start;
        box-shadow: 0 8px 20px rgba(237, 175, 35, 0.3);
    }
    .article-title {
        color: #fff;
        font-size: clamp(2.5rem, 5vw, 4.2rem);
        line-height: 1.05;
        margin-bottom: 24px;
        font-family: var(--font-heading);
        max-width: 900px;
    }
    .article-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
        color: rgba(255,255,255,0.85);
        font-size: 1.05rem;
        font-weight: 600;
    }
    .article-meta span {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .article-meta i {
        color: var(--primary-color);
        font-size: 1.2rem;
    }
    .article-content-wrapper {
        background: #fff;
        border-radius: 32px;
        padding: 60px 70px;
        box-shadow: 0 18px 45px rgba(77, 56, 18, 0.05);
        margin-top: -80px;
        position: relative;
        z-index: 10;
        margin-bottom: 60px;
    }
    .article-lead {
        font-size: 1.4rem;
        line-height: 1.8;
        color: #132238;
        font-weight: 500;
        margin-bottom: 30px;
    }
    .article-body {
        font-size: 1.15rem;
        line-height: 1.9;
        color: #4a5568;
    }
    .article-body p {
        margin-bottom: 1.8rem;
    }
    .article-body h2, .article-body h3, .article-body h4 {
        color: #132238;
        margin: 3rem 0 1.5rem;
        font-family: var(--font-heading);
    }
    .article-body h2 { font-size: 2.2rem; }
    .article-body h3 { font-size: 1.7rem; }
    .article-body blockquote {
        border-left: 4px solid var(--primary-color);
        padding: 2rem 2.5rem;
        background: linear-gradient(145deg, #fffdf8 0%, #fff8ea 100%);
        border-radius: 0 24px 24px 0;
        font-style: italic;
        font-size: 1.4rem;
        color: #132238;
        margin: 3rem 0;
        box-shadow: 0 12px 30px rgba(77, 56, 18, 0.05);
    }
    .article-body img {
        border-radius: 24px;
        margin: 3rem 0;
        box-shadow: 0 16px 40px rgba(19, 34, 56, 0.08);
        width: 100%;
        height: auto;
    }
    .article-author-box {
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 30px 35px;
        background: #f8fafc;
        border-radius: 24px;
        margin: 50px 0 0;
        border: 1px solid rgba(19, 34, 56, 0.05);
    }
    .article-author-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: var(--secondary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.8rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .article-author-info h4 {
        margin: 0 0 5px;
        color: #132238;
        font-size: 1.3rem;
        font-family: var(--font-heading);
    }
    .article-author-info p {
        margin: 0;
        color: #64748b;
        font-size: 1rem;
        line-height: 1.6;
    }
    .related-articles-section {
        background: #fff;
        padding: 80px 0;
        border-top: 1px solid rgba(19, 34, 56, 0.05);
    }
    .related-articles-section h3 {
        font-size: 2.5rem;
        font-family: var(--font-heading);
        color: #132238;
        margin-bottom: 40px;
        text-align: center;
    }
    .journal-card {
        height: 100%;
        border-radius: 28px;
        overflow: hidden;
        background: #fff;
        border: 1px solid rgba(19, 34, 56, 0.08);
        box-shadow: 0 18px 45px rgba(77, 56, 18, 0.07);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .journal-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 26px 60px rgba(77, 56, 18, 0.12);
    }
    .journal-card .post-img img {
        height: 260px;
        object-fit: cover;
        width: 100%;
    }
    .journal-card .post-content {
        padding: 30px;
    }
    .journal-card .post-title {
        font-size: 1.5rem;
        line-height: 1.3;
        margin: 12px 0 16px;
        font-family: var(--font-heading);
    }
    .journal-card .post-title a {
        color: #132238;
        text-decoration: none;
    }
    .journal-card .post-title a:hover {
        color: var(--primary-color);
    }
    .journal-card p {
        color: #5d677f;
        line-height: 1.7;
        margin-bottom: 0;
        font-size: 1.05rem;
    }
    .journal-story-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: #7d8798;
        font-weight: 800;
        margin-bottom: 16px;
    }
    @media (max-width: 991px) {
        .article-hero-overlay {
            padding: 40px 30px;
        }
        .article-title {
            font-size: 2.2rem;
        }
        .article-content-wrapper {
            padding: 40px 30px;
            margin-top: -40px;
            border-radius: 24px;
        }
    }
</style>

<section class="article-shell pb-0">
    <div class="container">
        <div class="article-hero">
            <img src="<?php echo Helpers::e($post_image); ?>" alt="<?php echo Helpers::e($post["title"] ?? ""); ?>">
            <div class="article-hero-overlay">
                <span class="article-category"><?php echo Helpers::e($post["category"] ?: "News"); ?></span>
                <h1 class="article-title"><?php echo Helpers::e($post["title"] ?? ""); ?></h1>
                <div class="article-meta">
                    <span><i class="icofont-ui-calendar"></i> <?php echo Helpers::e(date("j M Y", strtotime((string) ($post["published_at"] ?? "now")))); ?></span>
                    <span><i class="icofont-ui-user"></i> <?php echo Helpers::e($post["display_author"] ?? $post["author_name"] ?? "Admin Team"); ?></span>
                </div>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="article-content-wrapper">
                    <?php if (!empty($post["excerpt"])): ?>
                        <div class="article-lead">
                            <?php echo Helpers::e($post["excerpt"] ?? ""); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="article-body">
                        <?php echo $post["content"] ?? ""; ?>
                    </div>
                    
                    <?php if (!empty($post["tags"])): ?>
                        <div class="story-tag-row">
                            <span>Related Topics</span>
                            <div class="story-tag-cloud">
                                <?php foreach ($post["tags"] as $tag): ?>
                                    <a href="blog.php?tag=<?php echo urlencode((string) ($tag["slug"] ?? "")); ?>"><?php echo Helpers::e((string) ($tag["name"] ?? "")); ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="article-author-box">
                        <div class="article-author-avatar">
                            <?php echo strtoupper(substr(Helpers::e($post["display_author"] ?? $post["author_name"] ?? "Admin Team"), 0, 1)); ?>
                        </div>
                        <div class="article-author-info">
                            <h4><?php echo Helpers::e($post["display_author"] ?? $post["author_name"] ?? "Admin Team"); ?></h4>
                            <p>Author at Gracious Charity, reporting on impact stories, outreach efforts, and community support programmes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$rawRelated = Content::publishedPostsFiltered(6, $post['category_slug'] ?? '', '');
$relatedPosts = [];
foreach ($rawRelated as $rp) {
    if ((string)($rp['id'] ?? '') !== (string)($post['id'] ?? '') && ($rp['slug'] ?? '') !== ($post['slug'] ?? '')) {
        $relatedPosts[] = $rp;
    }
}
$relatedPosts = array_slice($relatedPosts, 0, 3);

if (empty($relatedPosts)) {
    $rawRelated = Content::publishedPostsFiltered(4, '', '');
    foreach ($rawRelated as $rp) {
        if ((string)($rp['id'] ?? '') !== (string)($post['id'] ?? '') && ($rp['slug'] ?? '') !== ($post['slug'] ?? '')) {
            $relatedPosts[] = $rp;
        }
    }
    $relatedPosts = array_slice($relatedPosts, 0, 3);
}
?>

<?php if (!empty($relatedPosts)): ?>
<section class="related-articles-section">
    <div class="container">
        <h3>Keep Reading</h3>
        <div class="row justify-content-center g-4">
            <?php foreach ($relatedPosts as $rel): ?>
            <?php $relImage = Helpers::siteUrl((string)($rel['featured_image'] ?: 'assets/images/blogs/blog_img_1.jpg')); ?>
            <div class="col-lg-4 col-md-6">
                <article class="journal-card">
                    <div class="post-img">
                        <a href="<?php echo Helpers::e(Helpers::postPublicUrl($rel)); ?>">
                            <img src="<?php echo Helpers::e($relImage); ?>" alt="<?php echo Helpers::e((string)($rel['title'] ?? '')); ?>" />
                        </a>
                    </div>
                    <div class="post-content">
                        <div class="journal-story-meta">
                            <span><?php echo Helpers::e(date('j M, Y', strtotime((string)($rel['published_at'] ?? 'now')))); ?></span>
                            <span><?php echo Helpers::e((string)($rel['category'] ?? 'News')); ?></span>
                        </div>
                        <h3 class="post-title"><a href="<?php echo Helpers::e(Helpers::postPublicUrl($rel)); ?>"><?php echo Helpers::e((string)($rel['title'] ?? '')); ?></a></h3>
                        <p><?php echo mb_strimwidth(strip_tags((string)($rel['excerpt'] ?? '')), 0, 100, "..."); ?></p>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . "/includes/explore-footer.php"; ?>
