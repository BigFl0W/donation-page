<?php

declare(strict_types=1);

function sample_posts(): array
{
    return [
        [
            "id" => 1,
            "title" => "Grant Distributions Continue to Increase",
            "slug" => "grant-distributions-continue-to-increase",
            "permalink_path" => "blog/impact-stories/grant-distributions-continue-to-increase",
            "excerpt" => "How stronger donor coordination is helping programmes reach more families with measurable impact.",
            "content" => "<p>Our latest grant cycle has expanded support across education, health, and nutrition programmes. By coordinating donor reporting more effectively, the organisation is now able to serve more communities with clearer visibility into outcomes and milestones.</p><p>This article structure is database-ready, which means admins can later edit the full story, update featured media, and control publishing without touching code.</p>",
            "featured_image" => "assets/images/blogs/blog_img_1.jpg",
            "category" => "Impact Stories",
            "category_slug" => "impact-stories",
            "status" => "published",
            "published_at" => "2026-05-01 09:00:00",
            "author_name" => "Admin Team",
            "display_author" => "Admin Team",
            "meta_title" => "Grant Distributions Continue to Increase | Gracious Charity Blog",
            "meta_description" => "How stronger donor coordination is helping programmes reach more families with measurable impact.",
            "seo_keywords" => "charity impact, donor reporting, community support",
            "tags" => [
                ["name" => "Community Impact", "slug" => "community-impact"],
                ["name" => "Donors", "slug" => "donors"],
                ["name" => "Programmes", "slug" => "programmes"],
            ],
        ],
        [
            "id" => 2,
            "title" => "Community Volunteers Drive New Outreach Success",
            "slug" => "community-volunteers-drive-new-outreach-success",
            "permalink_path" => "blog/news/community-volunteers-drive-new-outreach-success",
            "excerpt" => "A closer look at how trained volunteers improved delivery and participation during outreach week.",
            "content" => "<p>Volunteer coordination played a central role in the latest outreach campaign. From beneficiary onboarding to field logistics, the team helped improve both efficiency and trust.</p><p>Future admin editing will make it easy to update this story with quotes, outcome metrics, and partner mentions.</p>",
            "featured_image" => "assets/images/blogs/blog_img_2.jpg",
            "category" => "News",
            "category_slug" => "news",
            "status" => "published",
            "published_at" => "2026-05-03 11:30:00",
            "author_name" => "Communications Desk",
            "display_author" => "Communications Desk",
            "meta_title" => "Community Volunteers Drive New Outreach Success | Gracious Charity Blog",
            "meta_description" => "A closer look at how trained volunteers improved delivery and participation during outreach week.",
            "seo_keywords" => "volunteers, outreach, charity news",
            "tags" => [
                ["name" => "Volunteers", "slug" => "volunteers"],
                ["name" => "Outreach", "slug" => "outreach"],
            ],
        ],
        [
            "id" => 3,
            "title" => "New Learning Support Initiative Launches This Quarter",
            "slug" => "new-learning-support-initiative-launches-this-quarter",
            "permalink_path" => "blog/announcements/new-learning-support-initiative-launches-this-quarter",
            "excerpt" => "Programme teams are preparing a broader school support initiative with local partners and sponsors.",
            "content" => "<p>The education team is launching a new initiative focused on classroom readiness, study materials, and community-based mentorship. The effort is designed to support children who need consistent access to learning resources.</p>",
            "featured_image" => "assets/images/blogs/blog_img_3.jpg",
            "category" => "Announcements",
            "category_slug" => "announcements",
            "status" => "published",
            "published_at" => "2026-05-05 08:15:00",
            "author_name" => "Programme Office",
            "display_author" => "Programme Office",
            "meta_title" => "New Learning Support Initiative Launches This Quarter | Gracious Charity Blog",
            "meta_description" => "Programme teams are preparing a broader school support initiative with local partners and sponsors.",
            "seo_keywords" => "education support, programme launch, charity announcements",
            "tags" => [
                ["name" => "Education", "slug" => "education"],
                ["name" => "Programmes", "slug" => "programmes"],
            ],
        ],
    ];
}

function sample_events(): array
{
    return [
        [
            "id" => 1,
            "title" => "Annual Community Outreach and Fundraising Drive",
            "slug" => "annual-community-outreach-fundraising-drive",
            "summary" => "A flagship gathering designed to connect supporters, volunteers, and beneficiaries around one visible campaign day.",
            "content" => "<p>This annual event brings together donors, volunteers, partner organisations, and community leaders for a coordinated day of outreach and fundraising. It is designed to turn public visibility into practical support for current programmes.</p><p>Visitors can expect project showcases, field stories, sponsor engagement, and community participation opportunities.</p>",
            "featured_image" => "assets/images/events/event_single_large.jpg",
            "venue" => "Lagos Civic Center",
            "city" => "Lagos",
            "event_start" => "2026-08-24 10:00:00",
            "event_end" => "2026-08-24 16:00:00",
            "registration_url" => "contact-us.php",
            "status" => "published",
            "is_featured" => 1,
        ],
        [
            "id" => 2,
            "title" => "Back-to-School Outreach Launch",
            "slug" => "back-to-school-outreach-launch",
            "summary" => "Education support event focused on school kits, sponsor visibility, and volunteer activation.",
            "content" => "<p>The back-to-school outreach launch helps connect families with educational materials and programme support before the term begins. It is also an opportunity for sponsors and volunteers to engage directly with the initiative.</p>",
            "featured_image" => "assets/images/events/event_img_1.jpg",
            "venue" => "Ikeja Community Hall",
            "city" => "Ikeja",
            "event_start" => "2026-09-12 09:00:00",
            "event_end" => "2026-09-12 13:00:00",
            "registration_url" => "contact-us.php",
            "status" => "published",
            "is_featured" => 0,
        ],
        [
            "id" => 3,
            "title" => "Health and Family Awareness Day",
            "slug" => "health-and-family-awareness-day",
            "summary" => "A public engagement event designed to connect families with guidance, support, and partner services.",
            "content" => "<p>This event creates a welcoming space for families to access information, practical support, and community resources through a collaborative outreach experience.</p>",
            "featured_image" => "assets/images/events/event_img_3.jpg",
            "venue" => "Yaba Resource Center",
            "city" => "Yaba",
            "event_start" => "2026-09-28 11:30:00",
            "event_end" => "2026-09-28 15:30:00",
            "registration_url" => "contact-us.php",
            "status" => "published",
            "is_featured" => 0,
        ],
        [
            "id" => 4,
            "title" => "Partners and Sponsors Impact Brunch",
            "slug" => "partners-and-sponsors-impact-brunch",
            "summary" => "A relationship-building event for sharing programme updates, donor stories, and sponsorship opportunities.",
            "content" => "<p>This brunch format is designed for sponsor conversations, programme visibility, and stronger strategic relationships with long-term supporters.</p>",
            "featured_image" => "assets/images/events/event_img_5.jpg",
            "venue" => "Victoria Island Conference Hub",
            "city" => "Victoria Island",
            "event_start" => "2026-10-15 13:00:00",
            "event_end" => "2026-10-15 16:00:00",
            "registration_url" => "contact-us.php",
            "status" => "published",
            "is_featured" => 0,
        ],
    ];
}

function published_posts(int $limit = 20): array
{
    if (database_available() && db_table_exists("posts") && db_table_exists("post_categories")) {
        $posts = db_fetch_all(
            "SELECT p.*, COALESCE(a.full_name, p.author_name, 'Admin Team') AS display_author,
                    c.name AS category_name, c.slug AS category_slug
             FROM posts p
             LEFT JOIN admins a ON a.id = p.author_id
             LEFT JOIN post_categories c ON c.id = p.primary_category_id
             WHERE p.status = 'published'
             ORDER BY p.published_at DESC, p.created_at DESC
             LIMIT {$limit}"
        );

        return hydrate_posts_with_taxonomy($posts);
    }

    return array_slice(sample_posts(), 0, $limit);
}

function published_posts_filtered(int $limit = 20, string $categorySlug = "", string $tagSlug = ""): array
{
    if (database_available() && db_table_exists("posts") && db_table_exists("post_categories")) {
        $conditions = ["p.status = 'published'"];
        $joins = [
            "LEFT JOIN admins a ON a.id = p.author_id",
            "LEFT JOIN post_categories c ON c.id = p.primary_category_id",
        ];
        $params = [];

        if ($categorySlug !== "") {
            $conditions[] = "c.slug = :category_slug";
            $params["category_slug"] = $categorySlug;
        }

        if ($tagSlug !== "" && db_table_exists("post_tag_map") && db_table_exists("post_tags")) {
            $joins[] = "INNER JOIN post_tag_map ptm ON ptm.post_id = p.id";
            $joins[] = "INNER JOIN post_tags t ON t.id = ptm.tag_id";
            $conditions[] = "t.slug = :tag_slug";
            $params["tag_slug"] = $tagSlug;
        }

        $posts = db_fetch_all(
            "SELECT DISTINCT p.*, COALESCE(a.full_name, p.author_name, 'Admin Team') AS display_author,
                    c.name AS category_name, c.slug AS category_slug
             FROM posts p
             " . implode("\n", $joins) . "
             WHERE " . implode(" AND ", $conditions) . "
             ORDER BY p.published_at DESC, p.created_at DESC
             LIMIT {$limit}",
            $params
        );

        return hydrate_posts_with_taxonomy($posts);
    }

    $posts = array_values(array_filter(sample_posts(), static function (array $post) use ($categorySlug, $tagSlug): bool {
        $categoryMatch = $categorySlug === "" || ($post["category_slug"] ?? "") === $categorySlug;
        $tagMatch = $tagSlug === "";

        if ($tagSlug !== "") {
            foreach (($post["tags"] ?? []) as $tag) {
                if (($tag["slug"] ?? "") === $tagSlug) {
                    $tagMatch = true;
                    break;
                }
            }
        }

        return $categoryMatch && $tagMatch;
    }));

    return array_slice($posts, 0, $limit);
}

function published_post_by_slug(string $slug, string $categorySlug = ""): ?array
{
    if (database_available() && db_table_exists("posts") && db_table_exists("post_categories")) {
        $post = db_fetch_one(
            "SELECT p.*, COALESCE(a.full_name, p.author_name, 'Admin Team') AS display_author,
                    c.name AS category_name, c.slug AS category_slug
             FROM posts p
             LEFT JOIN admins a ON a.id = p.author_id
             LEFT JOIN post_categories c ON c.id = p.primary_category_id
             WHERE p.slug = :slug AND p.status = 'published'
             LIMIT 1",
            ["slug" => $slug]
        );

        if ($post) {
            $post = hydrate_posts_with_taxonomy([$post])[0];

            if ($categorySlug !== "" && ($post["category_slug"] ?? "") !== $categorySlug) {
                return null;
            }

            return $post;
        }
    }

    foreach (sample_posts() as $post) {
        if ($post["slug"] === $slug) {
            if ($categorySlug !== "" && ($post["category_slug"] ?? "") !== $categorySlug) {
                continue;
            }

            return $post;
        }
    }

    return null;
}

function featured_event(): ?array
{
    if (database_available() && db_table_exists("events")) {
        $event = db_fetch_one(
            "SELECT * FROM events
             WHERE status = 'published' AND is_featured = 1
             ORDER BY event_start ASC
             LIMIT 1"
        );

        if ($event) {
            return $event;
        }
    }

    foreach (sample_events() as $event) {
        if ((int) $event["is_featured"] === 1) {
            return $event;
        }
    }

    return sample_events()[0] ?? null;
}

function published_events(int $limit = 20, bool $excludeFeatured = false): array
{
    if (database_available() && db_table_exists("events")) {
        $sql = "SELECT * FROM events WHERE status = 'published'";
        $params = [];

        if ($excludeFeatured) {
            $sql .= " AND is_featured = 0";
        }

        $sql .= " ORDER BY event_start ASC LIMIT {$limit}";

        return db_fetch_all($sql, $params);
    }

    $events = sample_events();

    if ($excludeFeatured) {
        $events = array_values(array_filter($events, static fn(array $event): bool => (int) $event["is_featured"] === 0));
    }

    return array_slice($events, 0, $limit);
}

function published_event_by_slug(string $slug): ?array
{
    if (database_available() && db_table_exists("events")) {
        $event = db_fetch_one(
            "SELECT * FROM events WHERE slug = :slug AND status = 'published' LIMIT 1",
            ["slug" => $slug]
        );

        if ($event) {
            return $event;
        }
    }

    foreach (sample_events() as $event) {
        if ($event["slug"] === $slug) {
            return $event;
        }
    }

    return null;
}

function admin_posts(): array
{
    if (database_available() && db_table_exists("posts")) {
        return db_fetch_all(
            "SELECT p.id, p.title, p.slug, p.permalink_path, p.status, p.published_at,
                    COALESCE(c.name, p.category, 'General') AS category,
                    COALESCE(c.slug, 'general') AS category_slug
             FROM posts p
             LEFT JOIN post_categories c ON c.id = p.primary_category_id
             ORDER BY p.published_at DESC, p.created_at DESC"
        );
    }

    return sample_posts();
}

function admin_post_categories(): array
{
    if (database_available() && db_table_exists("post_categories")) {
        return db_fetch_all("SELECT id, name, slug FROM post_categories ORDER BY name ASC");
    }

    return [
        ["id" => 1, "name" => "Announcements", "slug" => "announcements"],
        ["id" => 2, "name" => "Impact Stories", "slug" => "impact-stories"],
        ["id" => 3, "name" => "News", "slug" => "news"],
    ];
}

function admin_post_tags(): array
{
    if (database_available() && db_table_exists("post_tags")) {
        return db_fetch_all("SELECT id, name, slug FROM post_tags ORDER BY name ASC");
    }

    return [
        ["id" => 1, "name" => "Community Impact", "slug" => "community-impact"],
        ["id" => 2, "name" => "Education", "slug" => "education"],
        ["id" => 3, "name" => "Outreach", "slug" => "outreach"],
        ["id" => 4, "name" => "Programmes", "slug" => "programmes"],
        ["id" => 5, "name" => "Volunteers", "slug" => "volunteers"],
    ];
}

function admin_post_tag_names(int $postId): array
{
    if (!database_available() || !db_table_exists("post_tag_map") || !db_table_exists("post_tags")) {
        return [];
    }

    $rows = db_fetch_all(
        "SELECT t.name
         FROM post_tag_map ptm
         INNER JOIN post_tags t ON t.id = ptm.tag_id
         WHERE ptm.post_id = :post_id
         ORDER BY t.name ASC",
        ["post_id" => $postId]
    );

    return array_map(static fn(array $row): string => (string) $row["name"], $rows);
}

function blog_category_summaries(): array
{
    if (database_available() && db_table_exists("post_categories") && db_table_exists("posts")) {
        return db_fetch_all(
            "SELECT c.id, c.name, c.slug, COUNT(p.id) AS post_count
             FROM post_categories c
             LEFT JOIN posts p ON p.primary_category_id = c.id AND p.status = 'published'
             GROUP BY c.id, c.name, c.slug
             ORDER BY c.name ASC"
        );
    }

    $counts = [];

    foreach (sample_posts() as $post) {
        $slug = $post["category_slug"];
        if (!isset($counts[$slug])) {
            $counts[$slug] = [
                "name" => $post["category"],
                "slug" => $slug,
                "post_count" => 0,
            ];
        }

        $counts[$slug]["post_count"]++;
    }

    return array_values($counts);
}

function blog_tag_summaries(): array
{
    if (database_available() && db_table_exists("post_tags") && db_table_exists("post_tag_map") && db_table_exists("posts")) {
        return db_fetch_all(
            "SELECT t.id, t.name, t.slug, COUNT(ptm.post_id) AS post_count
             FROM post_tags t
             LEFT JOIN post_tag_map ptm ON ptm.tag_id = t.id
             LEFT JOIN posts p ON p.id = ptm.post_id AND p.status = 'published'
             GROUP BY t.id, t.name, t.slug
             HAVING COUNT(ptm.post_id) > 0
             ORDER BY t.name ASC"
        );
    }

    $counts = [];

    foreach (sample_posts() as $post) {
        foreach (($post["tags"] ?? []) as $tag) {
            $slug = $tag["slug"];
            if (!isset($counts[$slug])) {
                $counts[$slug] = [
                    "name" => $tag["name"],
                    "slug" => $slug,
                    "post_count" => 0,
                ];
            }

            $counts[$slug]["post_count"]++;
        }
    }

    return array_values($counts);
}

function ensure_post_category(string $name): ?array
{
    $name = trim($name);

    if ($name === "" || !database_available() || !db_table_exists("post_categories")) {
        return null;
    }

    $existing = db_fetch_one(
        "SELECT id, name, slug FROM post_categories WHERE name = :name OR slug = :slug LIMIT 1",
        [
            "name" => $name,
            "slug" => slugify($name),
        ]
    );

    if ($existing) {
        return $existing;
    }

    $slug = slugify($name);

    db_execute(
        "INSERT INTO post_categories (name, slug, seo_title, seo_description)
         VALUES (:name, :slug, :seo_title, :seo_description)",
        [
            "name" => $name,
            "slug" => $slug,
            "seo_title" => $name . " | Gracious Charity",
            "seo_description" => "Browse " . $name . " articles from Gracious Charity.",
        ]
    );

    $id = db_last_insert_id();

    return $id ? ["id" => (int) $id, "name" => $name, "slug" => $slug] : null;
}

function sync_post_tags(int $postId, array $tagNames): void
{
    if ($postId <= 0 || !database_available() || !db_table_exists("post_tags") || !db_table_exists("post_tag_map")) {
        return;
    }

    $tagIds = [];

    foreach ($tagNames as $tagName) {
        $tagName = trim($tagName);

        if ($tagName === "") {
            continue;
        }

        $tagSlug = slugify($tagName);
        $existing = db_fetch_one(
            "SELECT id FROM post_tags WHERE slug = :slug LIMIT 1",
            ["slug" => $tagSlug]
        );

        if ($existing) {
            $tagIds[] = (int) $existing["id"];
            continue;
        }

        db_execute(
            "INSERT INTO post_tags (name, slug) VALUES (:name, :slug)",
            ["name" => $tagName, "slug" => $tagSlug]
        );

        $newId = db_last_insert_id();
        if ($newId) {
            $tagIds[] = (int) $newId;
        }
    }

    db_execute("DELETE FROM post_tag_map WHERE post_id = :post_id", ["post_id" => $postId]);

    foreach (array_unique($tagIds) as $tagId) {
        db_execute(
            "INSERT INTO post_tag_map (post_id, tag_id) VALUES (:post_id, :tag_id)",
            [
                "post_id" => $postId,
                "tag_id" => $tagId,
            ]
        );
    }
}

function admin_events(): array
{
    if (database_available() && db_table_exists("events")) {
        return db_fetch_all(
            "SELECT id, title, slug, venue, event_start, status, is_featured
             FROM events
             ORDER BY event_start ASC"
        );
    }

    return sample_events();
}

function format_event_date(string $value): string
{
    $timestamp = strtotime($value);

    if ($timestamp === false) {
        return $value;
    }

    return date("j M Y", $timestamp);
}

function format_event_time(string $value): string
{
    $timestamp = strtotime($value);

    if ($timestamp === false) {
        return $value;
    }

    return date("g:i A", $timestamp);
}

function hydrate_posts_with_taxonomy(array $posts): array
{
    if ($posts === []) {
        return [];
    }

    $postIds = array_values(array_filter(array_map(static fn(array $post): int => (int) ($post["id"] ?? 0), $posts)));
    $tagsByPost = [];

    if ($postIds !== [] && database_available() && db_table_exists("post_tag_map") && db_table_exists("post_tags")) {
        $tagRows = db_fetch_all(
            "SELECT ptm.post_id, t.name, t.slug
             FROM post_tag_map ptm
             INNER JOIN post_tags t ON t.id = ptm.tag_id
             WHERE ptm.post_id IN (" . implode(",", array_map("intval", $postIds)) . ")
             ORDER BY t.name ASC"
        );

        foreach ($tagRows as $tagRow) {
            $postId = (int) $tagRow["post_id"];
            $tagsByPost[$postId][] = [
                "name" => $tagRow["name"],
                "slug" => $tagRow["slug"],
            ];
        }
    }

    foreach ($posts as &$post) {
        $post["category"] = $post["category_name"] ?? $post["category"] ?? "General";
        $post["category_slug"] = $post["category_slug"] ?? slugify((string) $post["category"]);
        $post["permalink_path"] = $post["permalink_path"] ?? build_post_permalink((string) $post["category_slug"], (string) $post["slug"]);
        $post["tags"] = $tagsByPost[(int) ($post["id"] ?? 0)] ?? [];
    }
    unset($post);

    return $posts;
}
