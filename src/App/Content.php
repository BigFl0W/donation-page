<?php
declare(strict_types=1);

namespace App;

class Content
{
    // ─── POSTS ─────────────────────────────────────

    public static function publishedPosts(int $limit = 20): array
    {
        if (!Database::available()) return self::samplePosts();

        $posts = Database::fetchAll(
            "SELECT p.*, COALESCE(a.full_name, p.author_name, 'Admin Team') AS author
             FROM posts p
             LEFT JOIN admins a ON a.id = p.author_id
             WHERE p.status = 'published'
             ORDER BY COALESCE(p.published_at, p.updated_at) DESC
             LIMIT " . self::sanitizeLimit($limit)
        ) ?: [];

        return self::hydrateTaxonomy($posts);
    }

    public static function publishedPostsFiltered(int $limit, string $categorySlug, string $tagSlug): array
    {
        if (!Database::available() || $categorySlug === "" && $tagSlug === "") {
            return self::publishedPosts($limit);
        }

        $params = [];
        $joins = "";
        $wheres = ["p.status = 'published'"];

        if ($categorySlug !== "") {
            $wheres[] = "c.slug = :cat_slug";
            $params["cat_slug"] = $categorySlug;
            $joins .= " LEFT JOIN post_categories c ON c.id = p.category_id";
        }

        if ($tagSlug !== "") {
            $wheres[] = "t.slug = :tag_slug";
            $params["tag_slug"] = $tagSlug;
            $joins .= " LEFT JOIN post_tag_map ptm ON ptm.post_id = p.id
                        LEFT JOIN post_tags t ON t.id = ptm.tag_id";
        }

        $where = implode(" AND ", $wheres);
        $limit = self::sanitizeLimit($limit);

        $posts = Database::fetchAll(
            "SELECT DISTINCT p.*, COALESCE(a.full_name, p.author_name, 'Admin Team') AS author
             FROM posts p
             LEFT JOIN admins a ON a.id = p.author_id
             {$joins}
             WHERE {$where}
             ORDER BY COALESCE(p.published_at, p.updated_at) DESC
             LIMIT {$limit}",
            $params
        ) ?: [];

        return self::hydrateTaxonomy($posts);
    }

    public static function publishedPostBySlug(string $slug, string $categorySlug): ?array
    {
        if (!Database::available()) {
            foreach (self::publishedPosts(20) as $post) {
                if (($post["slug"] ?? "") === $slug) return $post;
            }
            return null;
        }

        $post = Database::fetchOne(
            "SELECT p.*, COALESCE(a.full_name, p.author_name, 'Admin Team') AS author
             FROM posts p
             LEFT JOIN admins a ON a.id = p.author_id
             WHERE p.slug = :slug AND p.status = 'published'
             LIMIT 1",
            ["slug" => $slug]
        );

        if ($post === null) return null;

        $hydrated = self::hydrateTaxonomy([$post]);
        $post = $hydrated[0] ?? $post;
        $post["media_gallery"] = self::postMedia((int) ($post["id"] ?? 0), (string) ($post["featured_image"] ?? ""));
        return $post;
    }

    public static function adminPosts(): array
    {
        if (!Database::available()) return [];
        return Database::fetchAll(
            "SELECT p.*, COALESCE(a.full_name, p.author_name, 'Admin Team') AS author
             FROM posts p
             LEFT JOIN admins a ON a.id = p.author_id
             ORDER BY COALESCE(p.updated_at, p.created_at) DESC
             LIMIT 100"
        ) ?: [];
    }

    public static function adminPostCategories(): array
    {
        if (!Database::available()) return [];
        return Database::fetchAll("SELECT * FROM post_categories ORDER BY name ASC") ?: [];
    }

    public static function adminPostTags(): array
    {
        if (!Database::available()) return [];
        return Database::fetchAll("SELECT * FROM post_tags ORDER BY name ASC") ?: [];
    }

    public static function adminPostTagNames(int $postId): array
    {
        if (!Database::available()) return [];
        $rows = Database::fetchAll(
            "SELECT t.name FROM post_tags t
             JOIN post_tag_map ptm ON ptm.tag_id = t.id
             WHERE ptm.post_id = :id
             ORDER BY t.name ASC",
            ["id" => $postId]
        ) ?: [];
        return array_map(fn($r) => $r["name"], $rows);
    }

    public static function blogCategorySummaries(): array
    {
        if (!Database::available()) return [];
        return Database::fetchAll(
            "SELECT c.*, COUNT(p.id) AS post_count
             FROM post_categories c
             LEFT JOIN posts p ON p.category_id = c.id AND p.status = 'published'
             GROUP BY c.id
             ORDER BY c.name ASC"
        ) ?: [];
    }

    public static function blogTagSummaries(): array
    {
        if (!Database::available()) return [];
        return Database::fetchAll(
            "SELECT t.*, COUNT(ptm.post_id) AS post_count
             FROM post_tags t
             LEFT JOIN post_tag_map ptm ON ptm.tag_id = t.id
             LEFT JOIN posts p ON p.id = ptm.post_id AND p.status = 'published'
             GROUP BY t.id
             ORDER BY t.name ASC"
        ) ?: [];
    }

    public static function ensurePostCategory(string $name): ?array
    {
        if (!Database::available() || trim($name) === "") return null;
        $slug = Helpers::slugify($name);
        $existing = Database::fetchOne(
            "SELECT id FROM post_categories WHERE slug = :slug",
            ["slug" => $slug]
        );
        if ($existing) return $existing;
        Database::execute(
            "INSERT INTO post_categories (name, slug) VALUES (:name, :slug)",
            ["name" => $name, "slug" => $slug]
        );
        $id = Database::lastInsertId();
        return $id ? ["id" => (int)$id] : null;
    }

    public static function syncPostTags(int $postId, array $tagNames): void
    {
        if (!Database::available()) return;
        Database::execute("DELETE FROM post_tag_map WHERE post_id = :id", ["id" => $postId]);
        foreach ($tagNames as $name) {
            $name = trim((string)$name);
            if ($name === "") continue;
            $slug = Helpers::slugify($name);
            $tag = Database::fetchOne(
                "SELECT id FROM post_tags WHERE slug = :slug", ["slug" => $slug]
            );
            if (!$tag) {
                Database::execute(
                    "INSERT INTO post_tags (name, slug) VALUES (:name, :slug)",
                    ["name" => $name, "slug" => $slug]
                );
                $tagId = Database::lastInsertId();
            } else {
                $tagId = $tag["id"];
            }
            if ($tagId) {
                Database::execute(
                    "INSERT IGNORE INTO post_tag_map (post_id, tag_id) VALUES (:post_id, :tag_id)",
                    ["post_id" => $postId, "tag_id" => (int)$tagId]
                );
            }
        }
    }

    // ─── EVENTS ─────────────────────────────────────

    public static function publishedEvents(int $limit = 10, bool $excludeFeatured = false): array
    {
        if (!Database::available()) return self::sampleEvents();

        $where = "WHERE e.status = 'published'";
        if ($excludeFeatured) $where .= " AND e.is_featured = 0";

        return Database::fetchAll(
            "SELECT e.*, COALESCE(a.full_name, 'Events Desk') AS organizer
             FROM events e
             LEFT JOIN admins a ON a.id = e.created_by
             {$where}
             ORDER BY e.event_start ASC
             LIMIT " . self::sanitizeLimit($limit)
        ) ?: [];
    }

    public static function featuredEvent(): ?array
    {
        if (!Database::available()) {
            $events = self::publishedEvents(1);
            return $events[0] ?? null;
        }

        return Database::fetchOne(
            "SELECT e.*, COALESCE(a.full_name, 'Events Desk') AS organizer
             FROM events e
             LEFT JOIN admins a ON a.id = e.created_by
             WHERE e.status = 'published' AND e.is_featured = 1
             ORDER BY e.event_start ASC
             LIMIT 1"
        );
    }

    public static function publishedEventBySlug(string $slug): ?array
    {
        if (!Database::available()) {
            foreach (self::publishedEvents(20) as $ev) {
                if (($ev["slug"] ?? "") === $slug) return $ev;
            }
            return null;
        }

        $event = Database::fetchOne(
            "SELECT e.*, COALESCE(a.full_name, 'Events Desk') AS organizer
             FROM events e
             LEFT JOIN admins a ON a.id = e.created_by
             WHERE e.slug = :slug AND e.status = 'published'
             LIMIT 1",
            ["slug" => $slug]
        ) ?: null;

        if ($event === null) {
            return null;
        }

        $event["media_gallery"] = self::eventMedia((int) ($event["id"] ?? 0), (string) ($event["featured_image"] ?? ""));
        return $event;
    }

    public static function adminEvents(): array
    {
        if (!Database::available()) return [];
        return Database::fetchAll(
            "SELECT e.*, COALESCE(a.full_name, 'Events Desk') AS organizer
             FROM events e
             LEFT JOIN admins a ON a.id = e.created_by
             ORDER BY e.event_start DESC
             LIMIT 100"
        ) ?: [];
    }

    public static function formatEventDate(string $value): string
    {
        $ts = strtotime($value);
        if ($ts === false) return $value;
        return date("F j, Y", $ts);
    }

    public static function formatEventTime(string $value): string
    {
        $ts = strtotime($value);
        if ($ts === false) return $value;
        return date("g:i A", $ts);
    }

    // ─── SAMPLES ────────────────────────────────────

    private static function samplePosts(): array
    {
        return [
            [
                "title"       => "5 Ways Your Donation Changes Lives",
                "slug"        => "5-ways-your-donation-changes-lives",
                "category"    => "Impact Stories",
                "category_slug" => "impact-stories",
                "excerpt"     => "See how every contribution creates lasting impact in communities around the world.",
                "content"     => "",
                "author"      => "Admin Team",
                "published_at" => date("Y-m-d H:i:s", strtotime("-2 days")),
                "status"      => "published",
            ],
            [
                "title"       => "Building a Healthier Future Together",
                "slug"        => "building-a-healthier-future-together",
                "category"    => "Healthcare",
                "category_slug" => "healthcare",
                "excerpt"     => "Our healthcare programme reached over 5,000 families this quarter.",
                "content"     => "",
                "author"      => "Admin Team",
                "published_at" => date("Y-m-d H:i:s", strtotime("-1 week")),
                "status"      => "published",
            ],
            [
                "title"       => "Education for All: New School Initiative",
                "slug"        => "education-for-all-new-school-initiative",
                "category"    => "Education",
                "category_slug" => "education",
                "excerpt"     => "We've launched a new programme to support primary education in rural areas.",
                "content"     => "",
                "author"      => "Admin Team",
                "published_at" => date("Y-m-d H:i:s", strtotime("-2 weeks")),
                "status"      => "published",
            ],
        ];
    }

    private static function sampleEvents(): array
    {
        return [
            [
                "title"       => "Annual Fundraising Gala 2025",
                "slug"        => "annual-fundraising-gala-2025",
                "summary"     => "Join us for an evening of inspiration and impact.",
                "venue"       => "Grand Convention Center",
                "city"        => "Abuja",
                "event_start" => date("Y-m-d H:i:s", strtotime("+2 months")),
                "event_end"   => date("Y-m-d H:i:s", strtotime("+2 months +4 hours")),
                "status"      => "published",
                "organizer"   => "Events Desk",
            ],
            [
                "title"       => "Community Health Outreach",
                "slug"        => "community-health-outreach",
                "summary"     => "Free health screening for local communities.",
                "venue"       => "Community Hall",
                "city"        => "Lagos",
                "event_start" => date("Y-m-d H:i:s", strtotime("+1 month")),
                "event_end"   => date("Y-m-d H:i:s", strtotime("+1 month +6 hours")),
                "status"      => "published",
                "organizer"   => "Events Desk",
            ],
            [
                "title"       => "Youth Leadership Workshop",
                "slug"        => "youth-leadership-workshop",
                "summary"     => "Empowering the next generation of leaders.",
                "venue"       => "Youth Center",
                "city"        => "Port Harcourt",
                "event_start" => date("Y-m-d H:i:s", strtotime("+3 weeks")),
                "event_end"   => date("Y-m-d H:i:s", strtotime("+3 weeks +1 day")),
                "status"      => "published",
                "organizer"   => "Events Desk",
            ],
            [
                "title"       => "Partner Appreciation Dinner",
                "slug"        => "partner-appreciation-dinner",
                "summary"     => "Celebrating our valued partners.",
                "venue"       => "Civic Centre",
                "city"        => "Abuja",
                "event_start" => date("Y-m-d H:i:s", strtotime("+3 months")),
                "event_end"   => null,
                "status"      => "planned",
                "organizer"   => "Events Desk",
            ],
        ];
    }

    // ─── INTERNAL ───────────────────────────────────

    private static function hydrateTaxonomy(array $posts): array
    {
        $cats = [];
        if (Database::available()) {
            $allCats = Database::fetchAll("SELECT id, name, slug FROM post_categories") ?: [];
            foreach ($allCats as $c) $cats[$c["id"]] = $c;
        }

        return array_map(function ($post) use ($cats) {
            $catId = $post["category_id"] ?? null;
            if ($catId && isset($cats[$catId])) {
                $post["category"] = $cats[$catId]["name"];
                $post["category_slug"] = $cats[$catId]["slug"];
            } elseif (empty($post["category"])) {
                $post["category"] = "General";
                $post["category_slug"] = "general";
            } else {
                $post["category_slug"] = Helpers::slugify($post["category"]);
            }
            return $post;
        }, $posts);
    }

    private static function postMedia(int $postId, string $featuredImage = ""): array
    {
        if (!Database::available() || $postId <= 0 || !Database::tableExists("post_media")) {
            return $featuredImage !== "" ? [[
                "media_type" => self::inferMediaType($featuredImage),
                "media_path" => $featuredImage,
                "caption" => "",
                "sort_order" => 0,
            ]] : [];
        }

        $rows = Database::fetchAll(
            "SELECT media_type, media_path, caption, sort_order
             FROM post_media
             WHERE post_id = :post_id
             ORDER BY sort_order ASC, id ASC",
            ["post_id" => $postId]
        ) ?: [];

        if ($rows === [] && $featuredImage !== "") {
            return [[
                "media_type" => self::inferMediaType($featuredImage),
                "media_path" => $featuredImage,
                "caption" => "",
                "sort_order" => 0,
            ]];
        }

        return $rows;
    }

    private static function eventMedia(int $eventId, string $featuredImage = ""): array
    {
        if (!Database::available() || $eventId <= 0 || !Database::tableExists("event_media")) {
            return $featuredImage !== "" ? [[
                "media_type" => self::inferMediaType($featuredImage),
                "media_path" => $featuredImage,
                "caption" => "",
                "sort_order" => 0,
            ]] : [];
        }

        $rows = Database::fetchAll(
            "SELECT media_type, media_path, caption, sort_order
             FROM event_media
             WHERE event_id = :event_id
             ORDER BY sort_order ASC, id ASC",
            ["event_id" => $eventId]
        ) ?: [];

        if ($rows === [] && $featuredImage !== "") {
            return [[
                "media_type" => self::inferMediaType($featuredImage),
                "media_path" => $featuredImage,
                "caption" => "",
                "sort_order" => 0,
            ]];
        }

        return $rows;
    }

    private static function inferMediaType(string $path): string
    {
        $ext = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ["mp4", "webm", "mov", "avi", "mkv"], true) ? "video" : "image";
    }

    private static function sanitizeLimit(int $limit): int
    {
        return max(1, min(1000, $limit));
    }
}
