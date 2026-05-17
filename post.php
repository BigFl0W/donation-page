<?php
declare(strict_types=1);

require __DIR__ . "/config/autoload.php";

use App\Content;
use App\Helpers;

$slug = trim((string) ($_GET["slug"] ?? ""));
$categorySlug = trim((string) ($_GET["category"] ?? ""));
$post = $slug !== "" ? Content::publishedPostBySlug($slug, $categorySlug) : null;
$brandName = Helpers::brandName();

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
        "name" => $brandName,
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

require __DIR__ . "/includes/explore-header.php";
?>

<style>
    .article-shell {
        background:
            radial-gradient(circle at top right, rgba(221, 160, 41, 0.08), transparent 26%),
            linear-gradient(180deg, #f6f3ec 0%, #fbfbf9 26%, #ffffff 100%);
        padding-top: 42px;
    }
    .article-hero {
        position: relative;
        border-radius: 36px;
        overflow: hidden;
        margin-bottom: 0;
        box-shadow: 0 30px 90px rgba(19, 34, 56, 0.14);
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
    }
    .article-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: inherit;
        filter: blur(22px) saturate(0.9);
        transform: scale(1.08);
        opacity: 0.5;
    }
    .article-hero img {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 66vh;
        min-height: 420px;
        max-height: 720px;
        object-fit: contain;
        object-position: center center;
        display: block;
        transform: scale(1);
    }
    .article-hero-overlay {
        position: absolute;
        inset: 0;
        background:
            linear-gradient(180deg, rgba(8, 15, 28, 0.12) 0%, rgba(8, 15, 28, 0.28) 32%, rgba(8, 15, 28, 0.82) 72%, rgba(8, 15, 28, 0.94) 100%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 48px 54px 96px;
        z-index: 2;
    }
    .article-hero-copy {
        max-width: 820px;
    }
    .article-kicker {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        color: rgba(255,255,255,0.82);
        font-size: 0.78rem;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        font-weight: 700;
    }
    .article-kicker::before {
        content: "";
        width: 54px;
        height: 1px;
        background: rgba(255,255,255,0.5);
    }
    .article-category {
        display: inline-flex;
        align-items: center;
        padding: 9px 18px;
        border-radius: 999px;
        background: var(--primary-color);
        color: #fff;
        font-weight: 800;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.16em;
        margin-bottom: 18px;
        align-self: flex-start;
        box-shadow: 0 12px 28px rgba(237, 175, 35, 0.28);
    }
    .article-title {
        color: #fff;
        font-size: clamp(1.55rem, 2.7vw, 3rem);
        line-height: 1.08;
        margin-bottom: 18px;
        font-family: var(--font-heading);
        max-width: 720px;
        text-wrap: balance;
        text-shadow: 0 10px 28px rgba(0, 0, 0, 0.22);
    }
    .article-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 14px 18px;
        color: rgba(255,255,255,0.88);
        font-size: 0.88rem;
        font-weight: 700;
        padding-top: 18px;
        border-top: 1px solid rgba(255,255,255,0.16);
    }
    .article-meta span {
        display: flex;
        align-items: center;
        gap: 9px;
    }
    .article-meta i {
        color: var(--primary-color);
        font-size: 1.05rem;
    }
    .article-content-wrapper {
        background: #fff;
        border-radius: 34px;
        padding: 58px 70px 62px;
        box-shadow: 0 22px 64px rgba(37, 32, 24, 0.08);
        margin-top: -74px;
        position: relative;
        z-index: 10;
        margin-bottom: 74px;
        border: 1px solid rgba(162, 114, 34, 0.08);
    }
    .article-lead {
        font-size: 1.08rem;
        line-height: 1.72;
        color: #132238;
        font-weight: 500;
        margin-bottom: 28px;
    }
    .article-body {
        font-size: 1.04rem;
        line-height: 1.95;
        color: #475569;
    }
    .article-body p {
        margin-bottom: 1.65rem;
    }
    .article-body h2, .article-body h3, .article-body h4 {
        color: #132238;
        margin: 3.2rem 0 1.2rem;
        font-family: var(--font-heading);
    }
    .article-body h2 { font-size: 1.9rem; }
    .article-body h3 { font-size: 1.5rem; }
    .article-body blockquote {
        border-left: 4px solid var(--primary-color);
        padding: 2.1rem 2.4rem;
        background: linear-gradient(145deg, #fffdf8 0%, #fff7e7 100%);
        border-radius: 0 28px 28px 0;
        font-style: italic;
        font-size: 1.12rem;
        color: #132238;
        margin: 3rem 0;
        box-shadow: 0 16px 36px rgba(77, 56, 18, 0.06);
    }
    .article-body img {
        border-radius: 28px;
        margin: 3rem 0;
        box-shadow: 0 20px 44px rgba(19, 34, 56, 0.1);
        width: 100%;
        height: auto;
    }
    .article-author-box {
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: center;
        gap: 24px;
        padding: 30px 34px;
        background: linear-gradient(145deg, #f7f9fc 0%, #fdfdfb 100%);
        border-radius: 28px;
        margin: 58px 0 0;
        border: 1px solid rgba(19, 34, 56, 0.06);
        box-shadow: 0 14px 40px rgba(19, 34, 56, 0.05);
    }
    .article-author-avatar {
        width: 78px;
        height: 78px;
        border-radius: 50%;
        background: linear-gradient(145deg, #3e6f63 0%, #5d8e82 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.95rem;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 14px 32px rgba(62, 111, 99, 0.22);
    }
    .article-author-info h4 {
        margin: 0 0 7px;
        color: #132238;
        font-size: 1.28rem;
        font-family: var(--font-heading);
    }
    .article-author-info p {
        margin: 0;
        color: #64748b;
        font-size: 0.98rem;
        line-height: 1.7;
    }
    .article-media-gallery {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 18px;
        margin-top: 48px;
    }
    .article-media-intro {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin: 46px 0 20px;
        padding-top: 26px;
        border-top: 1px solid rgba(19, 34, 56, 0.08);
    }
    .article-media-heading {
        margin: 0;
        font-size: 1.35rem;
        color: #132238;
        font-family: var(--font-heading);
    }
    .article-media-note {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 0.94rem;
        line-height: 1.6;
    }
    .article-media-count {
        display: inline-flex;
        align-items: center;
        padding: 8px 14px;
        border-radius: 999px;
        background: #f5f7fa;
        color: #475569;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .article-media-card {
        background: #fff;
        border: 1px solid rgba(19, 34, 56, 0.08);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 18px 38px rgba(19, 34, 56, 0.07);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .article-media-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 44px rgba(19, 34, 56, 0.1);
    }
    .article-media-card:first-child {
        grid-column: span 12;
    }
    .article-media-card:not(:first-child) {
        grid-column: span 4;
    }
    .article-media-card img,
    .article-media-card video {
        width: 100%;
        height: 260px;
        display: block;
        object-fit: cover;
        background: #0f172a;
    }
    .article-media-card:first-child img,
    .article-media-card:first-child video {
        height: 430px;
    }
    .article-media-caption {
        padding: 14px 16px 16px;
        color: #64748b;
        font-size: 0.9rem;
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
        padding: 24px;
    }
    .journal-card .post-title {
        font-size: 1.3rem;
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
        font-size: 0.95rem;
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
            padding: 36px 28px 78px;
        }
        .article-title {
            font-size: 2rem;
        }
        .article-content-wrapper {
            padding: 38px 32px;
            margin-top: -42px;
            border-radius: 24px;
        }
        .article-media-card:not(:first-child) {
            grid-column: span 6;
        }
        .article-media-card:first-child img,
        .article-media-card:first-child video {
            height: 340px;
        }
        .article-media-intro {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    @media (max-width: 767px) {
        .article-hero img {
            min-height: 340px;
            height: 54vh;
        }
        .article-kicker {
            font-size: 0.72rem;
            letter-spacing: 0.18em;
        }
        .article-kicker::before {
            width: 34px;
        }
        .article-category {
            font-size: 0.74rem;
            padding: 8px 16px;
            margin-bottom: 14px;
        }
        .article-title {
            font-size: 1.55rem;
            line-height: 1.08;
        }
        .article-meta {
            font-size: 0.82rem;
            gap: 10px 14px;
            padding-top: 14px;
        }
        .article-lead {
            font-size: 1.06rem;
        }
        .article-body {
            font-size: 0.96rem;
            line-height: 1.84;
        }
        .article-body blockquote {
            padding: 1.35rem 1.5rem;
            font-size: 1.05rem;
        }
        .article-author-box {
            grid-template-columns: 1fr;
            text-align: center;
        }
        .article-author-avatar {
            margin: 0 auto;
        }
        .article-media-gallery {
            grid-template-columns: 1fr;
        }
        .article-media-intro {
            margin: 40px 0 18px;
            padding-top: 22px;
        }
        .article-media-heading {
            font-size: 1.18rem;
        }
        .article-media-card,
        .article-media-card:first-child,
        .article-media-card:not(:first-child) {
            grid-column: auto;
        }
        .article-media-card img,
        .article-media-card video,
        .article-media-card:first-child img,
        .article-media-card:first-child video {
            height: 260px;
        }
    }
</style>

<section class="article-shell pb-0">
    <div class="container">
        <div class="article-hero" style="background-image: url('<?php echo Helpers::e($post_image); ?>');">
            <img src="<?php echo Helpers::e($post_image); ?>" alt="<?php echo Helpers::e($post["title"] ?? ""); ?>">
            <div class="article-hero-overlay">
                <div class="article-hero-copy">
                    <div class="article-kicker">Field Journal</div>
                    <span class="article-category"><?php echo Helpers::e($post["category"] ?: "News"); ?></span>
                    <h1 class="article-title"><?php echo Helpers::e($post["title"] ?? ""); ?></h1>
                    <div class="article-meta">
                        <span><i class="icofont-ui-calendar"></i> <?php echo Helpers::e(date("j M Y", strtotime((string) ($post["published_at"] ?? "now")))); ?></span>
                        <span><i class="icofont-ui-user"></i> <?php echo Helpers::e($post["display_author"] ?? $post["author_name"] ?? "Admin Team"); ?></span>
                    </div>
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

                    <?php if (!empty($post["media_gallery"])): ?>
                        <div class="article-media-intro">
                            <div>
                                <h3 class="article-media-heading">Photo Highlights</h3>
                                <p class="article-media-note">A visual record of the moments, people, and support shared during this story.</p>
                            </div>
                            <div class="article-media-count"><?php echo Helpers::e((string) count($post["media_gallery"])); ?> Items</div>
                        </div>
                        <div class="article-media-gallery">
                            <?php foreach (($post["media_gallery"] ?? []) as $mediaItem): ?>
                                <?php $mediaPath = Helpers::siteUrl((string) ($mediaItem["media_path"] ?? "")); ?>
                                <div class="article-media-card">
                                    <?php if (($mediaItem["media_type"] ?? "image") === "video"): ?>
                                        <video controls preload="metadata">
                                            <source src="<?php echo Helpers::e($mediaPath); ?>">
                                        </video>
                                    <?php else: ?>
                                        <img src="<?php echo Helpers::e($mediaPath); ?>" alt="<?php echo Helpers::e((string) ($mediaItem["caption"] ?? $post["title"] ?? "")); ?>">
                                    <?php endif; ?>
                                    <?php if (!empty($mediaItem["caption"])): ?>
                                        <div class="article-media-caption"><?php echo Helpers::e((string) $mediaItem["caption"]); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
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
                            <p>Author at <?php echo Helpers::e($brandName); ?>, reporting on impact stories, outreach efforts, and community support programmes.</p>
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
