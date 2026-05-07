<?php

declare(strict_types=1);

$action = $_GET["action"] ?? "list";
$postId = (int) ($_GET["id"] ?? 0);
$isEditing = $action === "edit" && $postId > 0;
$pdoReady = database_available() && db_table_exists("posts");
$error = "";
$categories = admin_post_categories();
$availableTags = admin_post_tags();

$form = [
    "id" => 0,
    "title" => "",
    "slug" => "",
    "excerpt" => "",
    "content" => "",
    "featured_image" => "assets/images/blogs/blog_img_1.jpg",
    "category" => "News",
    "primary_category_id" => 0,
    "tag_names" => "",
    "author_name" => "Admin Team",
    "status" => "draft",
    "published_at" => date("Y-m-d\TH:i"),
    "meta_title" => "",
    "meta_description" => "",
    "seo_keywords" => "",
    "canonical_url" => "",
    "permalink_path" => "",
];

if ($isEditing && $pdoReady) {
    $record = db_fetch_one("SELECT * FROM posts WHERE id = :id LIMIT 1", ["id" => $postId]);
    if ($record) {
        $form = array_merge($form, $record);
        $form["published_at"] = $record["published_at"] ? date("Y-m-d\TH:i", strtotime((string) $record["published_at"])) : "";
        $form["tag_names"] = implode(", ", admin_post_tag_names($postId));
    }
}

$publicUrlPreview = site_url(
    (string) (
        $form["permalink_path"] !== ""
            ? $form["permalink_path"]
            : build_post_permalink(
                (string) ($form["category"] ?: "updates"),
                (string) ($form["slug"] !== "" ? $form["slug"] : "story")
            )
    )
);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save_post"])) {
    $title = trim((string) ($_POST["title"] ?? ""));
    $slug = slugify((string) ($_POST["slug"] ?? $title));
    $excerpt = trim((string) ($_POST["excerpt"] ?? ""));
    $content = trim((string) ($_POST["content"] ?? ""));
    $featuredImage = trim((string) ($_POST["featured_image"] ?? ""));
    $category = trim((string) ($_POST["category"] ?? ""));
    $categoryRecord = ensure_post_category($category);
    $authorName = trim((string) ($_POST["author_name"] ?? ""));
    $tagNames = trim((string) ($_POST["tag_names"] ?? ""));
    $status = trim((string) ($_POST["status"] ?? "draft"));
    $publishedAt = trim((string) ($_POST["published_at"] ?? ""));
    $publishedAt = $publishedAt !== "" ? str_replace("T", " ", $publishedAt) . ":00" : null;
    $metaTitle = trim((string) ($_POST["meta_title"] ?? ""));
    $metaDescription = trim((string) ($_POST["meta_description"] ?? ""));
    $seoKeywords = trim((string) ($_POST["seo_keywords"] ?? ""));

    $permalinkPath = build_post_permalink((string) ($categoryRecord["slug"] ?? $category), $slug);
    $canonicalUrl = trim((string) ($_POST["canonical_url"] ?? ""));
    $canonicalUrl = $canonicalUrl !== "" ? $canonicalUrl : site_url($permalinkPath);
    $form = [
        "id" => (int) ($_POST["id"] ?? 0),
        "title" => $title,
        "slug" => $slug,
        "excerpt" => $excerpt,
        "content" => $content,
        "featured_image" => $featuredImage,
        "category" => $category,
        "primary_category_id" => (int) ($categoryRecord["id"] ?? 0),
        "tag_names" => $tagNames,
        "author_name" => $authorName,
        "status" => $status,
        "published_at" => trim((string) ($_POST["published_at"] ?? "")),
        "meta_title" => $metaTitle,
        "meta_description" => $metaDescription,
        "seo_keywords" => $seoKeywords,
        "canonical_url" => $canonicalUrl,
        "permalink_path" => $permalinkPath,
    ];

    if (!$pdoReady) {
        $error = "Database connection is not ready. Import the schema first.";
    } elseif ($title === "" || $content === "" || !$categoryRecord) {
        $error = "Title, category, and content are required.";
    } else {
        $metaTitle = $metaTitle !== "" ? $metaTitle : $title . " | Gracious Charity Blog";
        $metaDescription = $metaDescription !== "" ? $metaDescription : mb_substr($excerpt !== "" ? $excerpt : strip_tags($content), 0, 250);
        $authorId = (int) (current_admin()["id"] ?? 0);

        if ($form["id"] > 0) {
            $saved = db_execute(
                "UPDATE posts
                 SET author_id = :author_id, primary_category_id = :primary_category_id,
                     title = :title, slug = :slug, permalink_path = :permalink_path,
                     excerpt = :excerpt, content = :content,
                     featured_image = :featured_image, category = :category, author_name = :author_name,
                     status = :status, meta_title = :meta_title, meta_description = :meta_description,
                     seo_keywords = :seo_keywords, canonical_url = :canonical_url,
                     published_at = :published_at
                 WHERE id = :id",
                [
                    "id" => $form["id"],
                    "author_id" => $authorId > 0 ? $authorId : null,
                    "primary_category_id" => $categoryRecord["id"],
                    "title" => $title,
                    "slug" => $slug,
                    "permalink_path" => $permalinkPath,
                    "excerpt" => $excerpt,
                    "content" => $content,
                    "featured_image" => $featuredImage,
                    "category" => $categoryRecord["name"],
                    "author_name" => $authorName,
                    "status" => $status,
                    "meta_title" => $metaTitle,
                    "meta_description" => $metaDescription,
                    "seo_keywords" => $seoKeywords,
                    "canonical_url" => $canonicalUrl,
                    "published_at" => $publishedAt,
                ]
            );
            $savedPostId = $form["id"];
        } else {
            $saved = db_execute(
                "INSERT INTO posts
                    (author_id, primary_category_id, title, slug, permalink_path, excerpt, content, featured_image, category, author_name, status, meta_title, meta_description, seo_keywords, canonical_url, published_at)
                 VALUES
                    (:author_id, :primary_category_id, :title, :slug, :permalink_path, :excerpt, :content, :featured_image, :category, :author_name, :status, :meta_title, :meta_description, :seo_keywords, :canonical_url, :published_at)",
                [
                    "author_id" => $authorId > 0 ? $authorId : null,
                    "primary_category_id" => $categoryRecord["id"],
                    "title" => $title,
                    "slug" => $slug,
                    "permalink_path" => $permalinkPath,
                    "excerpt" => $excerpt,
                    "content" => $content,
                    "featured_image" => $featuredImage,
                    "category" => $categoryRecord["name"],
                    "author_name" => $authorName,
                    "status" => $status,
                    "meta_title" => $metaTitle,
                    "meta_description" => $metaDescription,
                    "seo_keywords" => $seoKeywords,
                    "canonical_url" => $canonicalUrl,
                    "published_at" => $publishedAt,
                ]
            );
            $savedPostId = (int) (db_last_insert_id() ?? 0);
        }

        if ($saved ?? false) {
            sync_post_tags($savedPostId, split_tag_list($tagNames));
        }

        header("Location: " . admin_url("index.php?page=posts"));
        exit;
    }
}

$posts = admin_posts();

$publishedPosts = count(array_filter(
    $posts,
    static fn(array $post): bool => (string) ($post["status"] ?? "") === "published"
));
$draftPosts = count(array_filter(
    $posts,
    static fn(array $post): bool => (string) ($post["status"] ?? "") === "draft"
));
$scheduledPosts = count(array_filter(
    $posts,
    static function (array $post): bool {
        $publishedAt = (string) ($post["published_at"] ?? "");

        return $publishedAt !== "" && strtotime($publishedAt) > time();
    }
));
?>
<div class="admin-topbar">
    <div>
        <h2>Blog Posts</h2>
        <p>Manage structured blog content with categories, tags, SEO metadata, and trustworthy public URLs.</p>
    </div>
    <div class="admin-actions">
        <a class="admin-btn light" href="../blog.php" target="_blank">Open Blog</a>
        <a class="admin-btn primary" href="<?php echo e(admin_url("index.php?page=posts&action=create")); ?>">New Post</a>
    </div>
</div>

<?php if ($action === "create" || $isEditing): ?>
    <?php if ($error !== ""): ?>
        <div class="admin-alert error"><?php echo e($error); ?></div>
    <?php endif; ?>
    <form method="post" class="post-edit-layout">
        <input type="hidden" name="id" value="<?php echo e((string) $form["id"]); ?>">

        <div class="post-edit-main">
            <div class="admin-kicker">
                <i class="icofont-edit"></i>
                <?php echo $isEditing ? "Editorial Update" : "New Story"; ?>
            </div>

            <div class="admin-form-group title-group admin-title-field">
                <label for="post-title" class="sr-only">Title</label>
                <input id="post-title" name="title" type="text" value="<?php echo e((string) $form["title"]); ?>" placeholder="Write the post headline" required>
            </div>

            <div class="slug-group admin-permalink-box">
                <strong>Permalink</strong>
                <span><?php echo e($publicUrlPreview); ?></span>
                <input id="post-slug" name="slug" type="text" value="<?php echo e((string) $form["slug"]); ?>" placeholder="story-slug" required>
                <a href="<?php echo e($publicUrlPreview); ?>" target="_blank" class="admin-btn light">Preview</a>
            </div>

            <section class="admin-panel">
                <div class="admin-panel-head">
                    <div>
                        <h3>Article Content</h3>
                        <p>Main story copy for the public blog detail page.</p>
                    </div>
                </div>
                <div class="admin-form-group">
                    <textarea id="post-content" name="content" rows="18" placeholder="Start writing the article..."><?php echo e((string) $form["content"]); ?></textarea>
                </div>
            </section>

            <section class="admin-panel">
                <div class="admin-panel-head">
                    <div>
                        <h3>Story Setup</h3>
                        <p>Control how the article appears on listing pages and article cards.</p>
                    </div>
                </div>
                <div class="admin-grid-2">
                    <div class="admin-form-group">
                        <label for="post-category">Primary Category</label>
                        <input id="post-category" name="category" type="text" list="post-category-options" value="<?php echo e((string) $form["category"]); ?>" placeholder="Select or add category">
                    </div>
                    <div class="admin-form-group">
                        <label for="post-author">Author</label>
                        <input id="post-author" name="author_name" type="text" value="<?php echo e((string) $form["author_name"]); ?>">
                    </div>
                </div>
                <div class="admin-form-group">
                    <label for="post-excerpt">Excerpt</label>
                    <textarea id="post-excerpt" name="excerpt" rows="4" placeholder="Write a concise summary for blog cards and search results..."><?php echo e((string) $form["excerpt"]); ?></textarea>
                </div>
                <div class="admin-form-group">
                    <label for="post-image">Featured Image</label>
                    <input id="post-image" name="featured_image" type="text" value="<?php echo e((string) $form["featured_image"]); ?>" placeholder="assets/images/blogs/blog_img_1.jpg">
                </div>
            </section>

            <section class="admin-panel">
                <div class="admin-panel-head">
                    <div>
                        <h3>SEO & Discoverability</h3>
                        <p>Metadata that helps search engines and social previews stay polished.</p>
                    </div>
                </div>
                <div class="admin-grid-2">
                    <div class="admin-form-group">
                        <label for="post-meta-title">SEO Title</label>
                        <input id="post-meta-title" name="meta_title" type="text" value="<?php echo e((string) $form["meta_title"]); ?>">
                    </div>
                    <div class="admin-form-group">
                        <label for="post-keywords">Focus Keywords</label>
                        <input id="post-keywords" name="seo_keywords" type="text" value="<?php echo e((string) $form["seo_keywords"]); ?>" placeholder="charity impact, outreach, education">
                    </div>
                </div>
                <div class="admin-form-group">
                    <label for="post-meta-description">SEO Description</label>
                    <textarea id="post-meta-description" name="meta_description" rows="3"><?php echo e((string) $form["meta_description"]); ?></textarea>
                </div>
                <div class="admin-form-group">
                    <label for="post-canonical">Canonical URL</label>
                    <input id="post-canonical" name="canonical_url" type="text" value="<?php echo e((string) $form["canonical_url"]); ?>">
                </div>
            </section>
        </div>

        <aside class="post-edit-side admin-panel-stack">
            <section class="admin-panel side-panel">
                <div class="admin-section-title">
                    <h3>Publishing</h3>
                </div>
                <div class="side-panel-content">
                    <div class="publish-stat">
                        <i class="icofont-ui-text-chat"></i> Status:
                        <strong><?php echo e(ucfirst((string) $form["status"])); ?></strong>
                    </div>
                    <div class="publish-stat">
                        <i class="icofont-ui-calendar"></i> Publish time:
                        <strong><?php echo e($form["published_at"] ? date("M j, Y @ H:i", strtotime((string) $form["published_at"])) : "Immediately"); ?></strong>
                    </div>
                    <div class="admin-form-group mt-3">
                        <label for="post-status">Change Status</label>
                        <select id="post-status" name="status">
                            <option value="draft" <?php echo $form["status"] === "draft" ? "selected" : ""; ?>>Draft</option>
                            <option value="published" <?php echo $form["status"] === "published" ? "selected" : ""; ?>>Published</option>
                            <option value="pending" <?php echo $form["status"] === "pending" ? "selected" : ""; ?>>Pending Review</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label for="post-published">Publish Date</label>
                        <input id="post-published" name="published_at" type="datetime-local" value="<?php echo e((string) $form["published_at"]); ?>">
                    </div>
                </div>
                <div class="side-panel-footer">
                    <a href="<?php echo e(admin_url("index.php?page=posts")); ?>" class="admin-btn light">Back to Posts</a>
                    <button type="submit" name="save_post" class="admin-btn primary"><?php echo $isEditing ? "Save Changes" : "Publish Post"; ?></button>
                </div>
            </section>

            <section class="admin-panel side-panel">
                <div class="admin-section-title">
                    <h3>Taxonomy</h3>
                </div>
                <div class="side-panel-content">
                    <div class="admin-form-group">
                        <label for="post-tags">Tags</label>
                        <textarea id="post-tags" name="tag_names" rows="3" placeholder="Separate tags with commas"><?php echo e((string) $form["tag_names"]); ?></textarea>
                        <p class="admin-helper">Example: education, outreach, donors</p>
                    </div>
                </div>
            </section>

            <section class="admin-panel side-panel">
                <div class="admin-section-title">
                    <h3>Editorial Checklist</h3>
                </div>
                <div class="side-panel-content">
                    <ul class="admin-plain-list">
                        <li>
                            <div>
                                <strong>Clear headline</strong>
                                <span>Keep it specific and readable in search results.</span>
                            </div>
                            <span class="admin-chip">Required</span>
                        </li>
                        <li>
                            <div>
                                <strong>Category and tags</strong>
                                <span>Use them so the archive stays organized.</span>
                            </div>
                            <span class="admin-chip">Recommended</span>
                        </li>
                        <li>
                            <div>
                                <strong>SEO fields</strong>
                                <span>Fill them before publishing important campaign stories.</span>
                            </div>
                            <span class="admin-chip">Best Practice</span>
                        </li>
                    </ul>
                </div>
            </section>

            <?php if ($form["featured_image"]): ?>
                <section class="admin-panel side-panel">
                    <div class="admin-section-title">
                        <h3>Image Preview</h3>
                    </div>
                    <div class="side-panel-content">
                        <img src="../<?php echo e((string) $form["featured_image"]); ?>" alt="Featured image preview" style="width: 100%; height: auto; border-radius: 12px; border: 1px solid #e7ded8;">
                    </div>
                </section>
            <?php endif; ?>
        </aside>
    </form>

    <datalist id="post-category-options">
        <?php foreach ($categories as $categoryOption): ?>
            <option value="<?php echo e((string) $categoryOption["name"]); ?>"></option>
        <?php endforeach; ?>
    </datalist>
<?php else: ?>
    <div class="admin-workspace-grid">
        <div class="admin-workspace-main">
            <section class="admin-panel">
                <div class="admin-panel-head">
                    <div>
                        <h3>Editorial Overview</h3>
                        <p>Track publishing volume, draft load, and scheduled stories in one workspace.</p>
                    </div>
                </div>
                <div class="admin-summary-grid">
                    <div class="admin-summary-metric">
                        <span>Total Posts</span>
                        <strong><?php echo e((string) count($posts)); ?></strong>
                        <small>All articles in the system</small>
                    </div>
                    <div class="admin-summary-metric">
                        <span>Published</span>
                        <strong><?php echo e((string) $publishedPosts); ?></strong>
                        <small>Live on the public blog</small>
                    </div>
                    <div class="admin-summary-metric">
                        <span>Scheduled / Drafts</span>
                        <strong><?php echo e((string) ($scheduledPosts + $draftPosts)); ?></strong>
                        <small>Still in workflow</small>
                    </div>
                </div>
            </section>

            <section class="admin-table-card">
                <div class="admin-section-title">
                    <h3>Published and Draft Posts</h3>
                </div>
                <table class="admin-table admin-table-clean">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Permalink</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Published</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <strong><?php echo e((string) $post["title"]); ?></strong>
                                <div class="admin-listing-meta">
                                    <span><?php echo e((string) ($post["author_name"] ?? "Admin Team")); ?></span>
                                </div>
                            </td>
                            <td><?php echo e((string) ($post["permalink_path"] ?? $post["slug"])); ?></td>
                            <td><?php echo e((string) ($post["category"] ?? "")); ?></td>
                            <td>
                                <span class="admin-badge <?php echo (($post["status"] ?? "") === "published") ? "success" : "warning"; ?>">
                                    <?php echo e((string) $post["status"]); ?>
                                </span>
                            </td>
                            <td><?php echo e(isset($post["published_at"]) ? (string) $post["published_at"] : ""); ?></td>
                            <td><a class="admin-btn light" href="<?php echo e(admin_url("index.php?page=posts&action=edit&id=" . (string) $post["id"])); ?>">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>

        <aside class="admin-workspace-side">
            <section class="admin-panel">
                <div class="admin-panel-head">
                    <div>
                        <h3>Content Structure</h3>
                    </div>
                </div>
                <ul class="admin-plain-list">
                    <li>
                        <div>
                            <strong>Categories</strong>
                            <span><?php echo e((string) count($categories)); ?> saved content groups</span>
                        </div>
                        <span class="admin-chip">Taxonomy</span>
                    </li>
                    <li>
                        <div>
                            <strong>Tags</strong>
                            <span><?php echo e((string) count($availableTags)); ?> reusable topic labels</span>
                        </div>
                        <span class="admin-chip">SEO</span>
                    </li>
                    <li>
                        <div>
                            <strong>Public archive</strong>
                            <span>Permalinks follow clean category-first blog URLs.</span>
                        </div>
                        <span class="admin-chip">Live</span>
                    </li>
                </ul>
            </section>

            <section class="admin-panel">
                <div class="admin-panel-head">
                    <div>
                        <h3>Editorial Notes</h3>
                    </div>
                </div>
                <div class="admin-note-box">
                    <strong>Publishing standard</strong>
                    <p>Use short excerpts, one clear primary category, and populated SEO fields for campaign stories that should rank well in search.</p>
                </div>
                <div class="admin-note-box mt-3">
                    <strong>Workflow</strong>
                    <p>Create the story, set a clean slug, confirm taxonomy, then review the public preview before publishing.</p>
                </div>
            </section>
        </aside>
    </div>
<?php endif; ?>
