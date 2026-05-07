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
            <div class="admin-form-group title-group">
                <label for="post-title" class="sr-only">Title</label>
                <input id="post-title" name="title" type="text" value="<?php echo e((string) $form["title"]); ?>" placeholder="Enter title here" required>
            </div>
            
            <div class="admin-form-group slug-group">
                <label for="post-slug">Permalink:</label>
                <span><?php echo e(site_url("blog/")); ?></span>
                <input id="post-slug" name="slug" type="text" value="<?php echo e((string) $form["slug"]); ?>" required>
                <a href="<?php echo e($publicUrlPreview); ?>" target="_blank" class="admin-btn light">View Post</a>
            </div>

            <div class="admin-table-card">
                <div class="admin-section-title">
                    <h3>Content</h3>
                </div>
                <div class="admin-form-group">
                    <textarea id="post-content" name="content" rows="20" placeholder="Start writing..."><?php echo e((string) $form["content"]); ?></textarea>
                </div>
            </div>

            <div class="admin-table-card">
                <div class="admin-section-title">
                    <h3>Excerpt</h3>
                </div>
                <div class="admin-form-group">
                    <textarea id="post-excerpt" name="excerpt" rows="3" placeholder="Write a short summary..."><?php echo e((string) $form["excerpt"]); ?></textarea>
                </div>
            </div>

            <div class="admin-table-card">
                <div class="admin-section-title">
                    <h3>SEO Settings</h3>
                </div>
                <div class="admin-form-group">
                    <label for="post-meta-title">SEO Title</label>
                    <input id="post-meta-title" name="meta_title" type="text" value="<?php echo e((string) $form["meta_title"]); ?>">
                </div>
                <div class="admin-form-group">
                    <label for="post-meta-description">SEO Description</label>
                    <textarea id="post-meta-description" name="meta_description" rows="2"><?php echo e((string) $form["meta_description"]); ?></textarea>
                </div>
                <div class="admin-form-group">
                    <label for="post-keywords">Focus Keywords</label>
                    <input id="post-keywords" name="seo_keywords" type="text" value="<?php echo e((string) $form["seo_keywords"]); ?>" placeholder="charity impact, outreach, education">
                </div>
                <div class="admin-form-group">
                    <label for="post-canonical">Canonical URL</label>
                    <input id="post-canonical" name="canonical_url" type="text" value="<?php echo e((string) $form["canonical_url"]); ?>">
                </div>
            </div>
        </div>

        <div class="post-edit-side">
            <div class="admin-table-card side-panel">
                <div class="admin-section-title">
                    <h3>Publish</h3>
                </div>
                <div class="side-panel-content">
                    <div class="publish-stat">
                        <i class="icofont-key"></i> Status: <strong><?php echo e(ucfirst((string) $form["status"])); ?></strong>
                    </div>
                    <div class="publish-stat">
                        <i class="icofont-calendar"></i> Published on: <strong><?php echo e($form["published_at"] ? date("M j, Y @ H:i", strtotime($form["published_at"])) : "Immediately"); ?></strong>
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
                    <div class="admin-form-group">
                        <label for="post-author">Author</label>
                        <input id="post-author" name="author_name" type="text" value="<?php echo e((string) $form["author_name"]); ?>">
                    </div>
                </div>
                <div class="side-panel-footer">
                    <a href="<?php echo e(admin_url("index.php?page=posts")); ?>" class="admin-btn light">Move to Trash</a>
                    <button type="submit" name="save_post" class="admin-btn primary">Update</button>
                </div>
            </div>

            <div class="admin-table-card side-panel">
                <div class="admin-section-title">
                    <h3>Categories</h3>
                </div>
                <div class="side-panel-content">
                    <div class="admin-form-group">
                        <input id="post-category" name="category" type="text" list="post-category-options" value="<?php echo e((string) $form["category"]); ?>" placeholder="Select or add category">
                    </div>
                </div>
            </div>

            <div class="admin-table-card side-panel">
                <div class="admin-section-title">
                    <h3>Tags</h3>
                </div>
                <div class="side-panel-content">
                    <div class="admin-form-group">
                        <textarea id="post-tags" name="tag_names" rows="2" placeholder="Separate tags with commas"><?php echo e((string) $form["tag_names"]); ?></textarea>
                        <p class="admin-helper">Separate tags with commas</p>
                    </div>
                </div>
            </div>

            <div class="admin-table-card side-panel">
                <div class="admin-section-title">
                    <h3>Featured Image</h3>
                </div>
                <div class="side-panel-content">
                    <div class="admin-form-group">
                        <input id="post-image" name="featured_image" type="text" value="<?php echo e((string) $form["featured_image"]); ?>" placeholder="Image URL path">
                        <?php if ($form["featured_image"]): ?>
                            <div class="mt-2">
                                <img src="../<?php echo e($form["featured_image"]); ?>" alt="Preview" style="max-width: 100%; height: auto; border: 1px solid #ddd;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <datalist id="post-category-options">
        <?php foreach ($categories as $categoryOption): ?>
            <option value="<?php echo e((string) $categoryOption["name"]); ?>"></option>
        <?php endforeach; ?>
    </datalist>
<?php else: ?>
    <section class="admin-table-card">
        <div class="admin-section-title">
            <h3>Published and Draft Posts</h3>
        </div>
        <table class="admin-table">
            <thead>
            <tr>
                <th>Title</th>
                <th>URL Path</th>
                <th>Category</th>
                <th>Status</th>
                <th>Published</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo e((string) $post["title"]); ?></td>
                    <td><?php echo e((string) ($post["permalink_path"] ?? $post["slug"])); ?></td>
                    <td><?php echo e((string) ($post["category"] ?? "")); ?></td>
                    <td><span class="admin-badge <?php echo (($post["status"] ?? "") === "published") ? "success" : "warning"; ?>"><?php echo e((string) $post["status"]); ?></span></td>
                    <td><?php echo e(isset($post["published_at"]) ? (string) $post["published_at"] : ""); ?></td>
                    <td><a class="admin-btn light" href="<?php echo e(admin_url("index.php?page=posts&action=edit&id=" . (string) $post["id"])); ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
<?php endif; ?>
