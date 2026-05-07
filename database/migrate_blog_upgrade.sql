CREATE TABLE IF NOT EXISTS post_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT NULL,
    seo_title VARCHAR(190) NULL,
    seo_description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS post_tags (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    slug VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS post_tag_map (
    post_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (post_id, tag_id)
);

ALTER TABLE posts ADD COLUMN IF NOT EXISTS primary_category_id BIGINT UNSIGNED NULL AFTER author_id;
ALTER TABLE posts ADD COLUMN IF NOT EXISTS permalink_path VARCHAR(255) NULL AFTER slug;
ALTER TABLE posts ADD COLUMN IF NOT EXISTS seo_keywords VARCHAR(255) NULL AFTER meta_description;
ALTER TABLE posts ADD COLUMN IF NOT EXISTS canonical_url VARCHAR(255) NULL AFTER seo_keywords;

INSERT INTO post_categories (name, slug, description, seo_title, seo_description)
VALUES
('Impact Stories', 'impact-stories', 'Stories that show measurable programme impact and donor outcomes.', 'Impact Stories | Gracious Charity', 'Impact stories and measurable outcomes from Gracious Charity initiatives.'),
('News', 'news', 'Latest platform news, updates, and announcements.', 'News | Gracious Charity', 'Latest news and field updates from Gracious Charity.'),
('Announcements', 'announcements', 'Important programme launches, notices, and public announcements.', 'Announcements | Gracious Charity', 'Official announcements and initiative launches from Gracious Charity.')
ON DUPLICATE KEY UPDATE
    description = VALUES(description),
    seo_title = VALUES(seo_title),
    seo_description = VALUES(seo_description);

UPDATE posts p
LEFT JOIN post_categories c ON c.name = p.category
SET p.primary_category_id = c.id,
    p.permalink_path = CONCAT('blog/', c.slug, '/', p.slug),
    p.meta_title = COALESCE(NULLIF(p.meta_title, ''), CONCAT(p.title, ' | Gracious Charity Blog')),
    p.meta_description = COALESCE(NULLIF(p.meta_description, ''), LEFT(COALESCE(NULLIF(p.excerpt, ''), p.title), 250)),
    p.seo_keywords = COALESCE(NULLIF(p.seo_keywords, ''), CONCAT(REPLACE(COALESCE(c.name, 'updates'), ' ', '-'), ', gracious charity')),
    p.canonical_url = COALESCE(NULLIF(p.canonical_url, ''), CONCAT('http://localhost/donation-page/blog/', c.slug, '/', p.slug))
WHERE c.id IS NOT NULL;

ALTER TABLE posts MODIFY permalink_path VARCHAR(255) NOT NULL;
CREATE UNIQUE INDEX idx_posts_permalink_path ON posts (permalink_path);

INSERT INTO post_tags (name, slug)
VALUES
('Community Impact', 'community-impact'),
('Volunteers', 'volunteers'),
('Education', 'education'),
('Donors', 'donors'),
('Outreach', 'outreach'),
('Programmes', 'programmes')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT IGNORE INTO post_tag_map (post_id, tag_id)
SELECT 1, id FROM post_tags WHERE slug IN ('community-impact', 'donors', 'programmes');
INSERT IGNORE INTO post_tag_map (post_id, tag_id)
SELECT 2, id FROM post_tags WHERE slug IN ('volunteers', 'outreach');
INSERT IGNORE INTO post_tag_map (post_id, tag_id)
SELECT 3, id FROM post_tags WHERE slug IN ('education', 'programmes');
