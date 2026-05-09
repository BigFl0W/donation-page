DROP DATABASE IF EXISTS donation_page;
CREATE DATABASE donation_page CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE donation_page;

CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admins (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id INT UNSIGNED NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_admins_role FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_group VARCHAR(80) NOT NULL,
    setting_key VARCHAR(120) NOT NULL UNIQUE,
    setting_value LONGTEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(120) NOT NULL UNIQUE,
    title VARCHAR(190) NOT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pages_created_by FOREIGN KEY (created_by) REFERENCES admins(id),
    CONSTRAINT fk_pages_updated_by FOREIGN KEY (updated_by) REFERENCES admins(id)
);

CREATE TABLE content_blocks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_id BIGINT UNSIGNED NOT NULL,
    block_key VARCHAR(120) NOT NULL,
    block_label VARCHAR(190) NOT NULL,
    block_type ENUM('text', 'html', 'image', 'cta', 'json') DEFAULT 'text',
    block_value LONGTEXT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_content_block (page_id, block_key),
    CONSTRAINT fk_content_blocks_page FOREIGN KEY (page_id) REFERENCES pages(id)
);

CREATE TABLE gallery_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    media_type ENUM('photo', 'video') DEFAULT 'photo',
    media_path VARCHAR(255) NOT NULL,
    thumbnail_path VARCHAR(255) NULL,
    description TEXT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE partners (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    partner_type ENUM('partner', 'sponsor') DEFAULT 'partner',
    logo_path VARCHAR(255) NULL,
    website_url VARCHAR(255) NULL,
    description TEXT NULL,
    tier VARCHAR(80) NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE programmes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    category VARCHAR(120) NULL,
    summary TEXT NULL,
    content LONGTEXT NULL,
    featured_image VARCHAR(255) NULL,
    goal_amount DECIMAL(15,2) DEFAULT 0.00,
    raised_amount DECIMAL(15,2) DEFAULT 0.00,
    status ENUM('draft', 'published', 'completed') DEFAULT 'draft',
    start_date DATE NULL,
    end_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE faqs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    answer LONGTEXT NOT NULL,
    category VARCHAR(120) NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE post_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT NULL,
    seo_title VARCHAR(190) NULL,
    seo_description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE post_tags (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    slug VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    author_id BIGINT UNSIGNED NULL,
    primary_category_id BIGINT UNSIGNED NULL,
    title VARCHAR(190) NOT NULL,
    slug VARCHAR(190) NOT NULL UNIQUE,
    permalink_path VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT NULL,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255) NULL,
    category VARCHAR(120) NULL,
    author_name VARCHAR(120) NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    meta_title VARCHAR(190) NULL,
    meta_description VARCHAR(255) NULL,
    seo_keywords VARCHAR(255) NULL,
    canonical_url VARCHAR(255) NULL,
    published_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_posts_author FOREIGN KEY (author_id) REFERENCES admins(id),
    CONSTRAINT fk_posts_primary_category FOREIGN KEY (primary_category_id) REFERENCES post_categories(id)
);

CREATE TABLE post_tag_map (
    post_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (post_id, tag_id),
    CONSTRAINT fk_post_tag_map_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    CONSTRAINT fk_post_tag_map_tag FOREIGN KEY (tag_id) REFERENCES post_tags(id) ON DELETE CASCADE
);

CREATE TABLE events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_by BIGINT UNSIGNED NULL,
    title VARCHAR(190) NOT NULL,
    slug VARCHAR(190) NOT NULL UNIQUE,
    summary TEXT NULL,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255) NULL,
    venue VARCHAR(190) NULL,
    city VARCHAR(120) NULL,
    event_start DATETIME NOT NULL,
    event_end DATETIME NULL,
    registration_url VARCHAR(255) NULL,
    status ENUM('draft', 'published', 'cancelled', 'completed') DEFAULT 'draft',
    is_featured TINYINT(1) DEFAULT 0,
    meta_title VARCHAR(190) NULL,
    meta_description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_events_created_by FOREIGN KEY (created_by) REFERENCES admins(id)
);

CREATE TABLE donations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(190) NULL,
    donor_email VARCHAR(190) NULL,
    donor_phone VARCHAR(60) NULL,
    campaign VARCHAR(190) NULL,
    currency CHAR(3) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    gateway ENUM('paystack', 'stripe', 'manual') NOT NULL,
    status ENUM('pending', 'successful', 'failed', 'refunded') DEFAULT 'pending',
    payment_reference VARCHAR(190) NOT NULL UNIQUE,
    paid_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE payment_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    donation_id BIGINT UNSIGNED NOT NULL,
    gateway ENUM('paystack', 'stripe') NOT NULL,
    gateway_reference VARCHAR(190) NOT NULL,
    transaction_type ENUM('charge', 'verification', 'webhook', 'refund') DEFAULT 'charge',
    amount DECIMAL(15,2) NULL,
    currency CHAR(3) NULL,
    payload_json LONGTEXT NULL,
    gateway_status VARCHAR(120) NULL,
    processed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payment_transactions_donation FOREIGN KEY (donation_id) REFERENCES donations(id)
);

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id BIGINT UNSIGNED NULL,
    action VARCHAR(190) NOT NULL,
    target_type VARCHAR(120) NULL,
    target_id BIGINT UNSIGNED NULL,
    details LONGTEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_logs_admin FOREIGN KEY (admin_id) REFERENCES admins(id)
);

CREATE TABLE contact_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_name VARCHAR(190) NOT NULL,
    sender_email VARCHAR(190) NOT NULL,
    subject VARCHAR(255) NULL,
    message LONGTEXT NOT NULL,
    status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO roles (id, name, description) VALUES
(1, 'super_admin', 'Full access to platform administration'),
(2, 'admin', 'Manage content, donations, and operations'),
(3, 'editor', 'Manage public content only'),
(4, 'finance', 'Review donation and gateway activity');

INSERT INTO admins (id, role_id, full_name, email, password_hash, status) VALUES
(1, 1, 'Super Admin', 'admin@graciouscharity.org', '$2y$10$EXDcifSr0hpOFQaPDU9Lbexiv6vkSyHytEynBiA5IlyPNIMCnabA2', 'active');

INSERT INTO settings (setting_group, setting_key, setting_value) VALUES
('site', 'site_name', 'Gracious Charity Platform'),
('site', 'contact_email', 'info@graciouscharity.org'),
('site', 'contact_phone', '+1234567899'),
('payments', 'paystack_public_key', ''),
('payments', 'paystack_secret_key', ''),
('payments', 'stripe_public_key', ''),
('payments', 'stripe_secret_key', '');

INSERT INTO post_categories (id, name, slug, description, seo_title, seo_description) VALUES
(1, 'Impact Stories', 'impact-stories', 'Stories that show measurable programme impact and donor outcomes.', 'Impact Stories | Gracious Charity', 'Impact stories and measurable outcomes from Gracious Charity initiatives.'),
(2, 'News', 'news', 'Latest platform news, updates, and announcements.', 'News | Gracious Charity', 'Latest news and field updates from Gracious Charity.'),
(3, 'Announcements', 'announcements', 'Important programme launches, notices, and public announcements.', 'Announcements | Gracious Charity', 'Official announcements and initiative launches from Gracious Charity.');

INSERT INTO post_tags (id, name, slug) VALUES
(1, 'Community Impact', 'community-impact'),
(2, 'Volunteers', 'volunteers'),
(3, 'Education', 'education'),
(4, 'Donors', 'donors'),
(5, 'Outreach', 'outreach'),
(6, 'Programmes', 'programmes');

INSERT INTO posts (id, author_id, primary_category_id, title, slug, permalink_path, excerpt, content, featured_image, category, author_name, status, meta_title, meta_description, seo_keywords, canonical_url, published_at) VALUES
(1, 1, 1, 'Grant Distributions Continue to Increase', 'grant-distributions-continue-to-increase', 'blog/impact-stories/grant-distributions-continue-to-increase', 'How stronger donor coordination is helping programmes reach more families with measurable impact.', '<p>Our latest grant cycle has expanded support across education, health, and nutrition programmes. By coordinating donor reporting more effectively, the organisation is now able to serve more communities with clearer visibility into outcomes and milestones.</p><p>This article structure is database-ready, which means admins can later edit the full story, update featured media, and control publishing without touching code.</p>', 'assets/images/blogs/blog_img_1.jpg', 'Impact Stories', 'Admin Team', 'published', 'Grant Distributions Continue to Increase | Gracious Charity Blog', 'How stronger donor coordination is helping programmes reach more families with measurable impact.', 'charity impact, donor reporting, community support', NULL, '2026-05-01 09:00:00'),
(2, 1, 2, 'Community Volunteers Drive New Outreach Success', 'community-volunteers-drive-new-outreach-success', 'blog/news/community-volunteers-drive-new-outreach-success', 'A closer look at how trained volunteers improved delivery and participation during outreach week.', '<p>Volunteer coordination played a central role in the latest outreach campaign. From beneficiary onboarding to field logistics, the team helped improve both efficiency and trust.</p><p>Future admin editing will make it easy to update this story with quotes, outcome metrics, and partner mentions.</p>', 'assets/images/blogs/blog_img_2.jpg', 'News', 'Communications Desk', 'published', 'Community Volunteers Drive New Outreach Success | Gracious Charity Blog', 'A closer look at how trained volunteers improved delivery and participation during outreach week.', 'volunteers, outreach, charity news', NULL, '2026-05-03 11:30:00'),
(3, 1, 3, 'New Learning Support Initiative Launches This Quarter', 'new-learning-support-initiative-launches-this-quarter', 'blog/announcements/new-learning-support-initiative-launches-this-quarter', 'Programme teams are preparing a broader school support initiative with local partners and sponsors.', '<p>The education team is launching a new initiative focused on classroom readiness, study materials, and community-based mentorship. The effort is designed to support children who need consistent access to learning resources.</p>', 'assets/images/blogs/blog_img_3.jpg', 'Announcements', 'Programme Office', 'published', 'New Learning Support Initiative Launches This Quarter | Gracious Charity Blog', 'Programme teams are preparing a broader school support initiative with local partners and sponsors.', 'education support, programme launch, charity announcements', NULL, '2026-05-05 08:15:00');

INSERT INTO post_tag_map (post_id, tag_id) VALUES
(1, 1),
(1, 4),
(1, 6),
(2, 2),
(2, 5),
(3, 3),
(3, 6);

INSERT INTO events (id, created_by, title, slug, summary, content, featured_image, venue, city, event_start, event_end, registration_url, status, is_featured, meta_title, meta_description) VALUES
(1, 1, 'Annual Community Outreach and Fundraising Drive', 'annual-community-outreach-fundraising-drive', 'A flagship gathering designed to connect supporters, volunteers, and beneficiaries around one visible campaign day.', '<p>This annual event brings together donors, volunteers, partner organisations, and community leaders for a coordinated day of outreach and fundraising. It is designed to turn public visibility into practical support for current programmes.</p><p>Visitors can expect project showcases, field stories, sponsor engagement, and community participation opportunities.</p>', 'assets/images/events/event_single_large.jpg', 'Lagos Civic Center', 'Lagos', '2026-08-24 10:00:00', '2026-08-24 16:00:00', 'contact-us.php', 'published', 1, 'Events | Annual Community Outreach and Fundraising Drive', 'A flagship gathering designed to connect supporters, volunteers, and beneficiaries around one visible campaign day.'),
(2, 1, 'Back-to-School Outreach Launch', 'back-to-school-outreach-launch', 'Education support event focused on school kits, sponsor visibility, and volunteer activation.', '<p>The back-to-school outreach launch helps connect families with educational materials and programme support before the term begins. It is also an opportunity for sponsors and volunteers to engage directly with the initiative.</p>', 'assets/images/events/event_img_1.jpg', 'Ikeja Community Hall', 'Ikeja', '2026-09-12 09:00:00', '2026-09-12 13:00:00', 'contact-us.php', 'published', 0, 'Events | Back-to-School Outreach Launch', 'Education support event focused on school kits, sponsor visibility, and volunteer activation.'),
(3, 1, 'Health and Family Awareness Day', 'health-and-family-awareness-day', 'A public engagement event designed to connect families with guidance, support, and partner services.', '<p>This event creates a welcoming space for families to access information, practical support, and community resources through a collaborative outreach experience.</p>', 'assets/images/events/event_img_3.jpg', 'Yaba Resource Center', 'Yaba', '2026-09-28 11:30:00', '2026-09-28 15:30:00', 'contact-us.php', 'published', 0, 'Events | Health and Family Awareness Day', 'A public engagement event designed to connect families with guidance, support, and partner services.'),
(4, 1, 'Partners and Sponsors Impact Brunch', 'partners-and-sponsors-impact-brunch', 'A relationship-building event for sharing programme updates, donor stories, and sponsorship opportunities.', '<p>This brunch format is designed for sponsor conversations, programme visibility, and stronger strategic relationships with long-term supporters.</p>', 'assets/images/events/event_img_5.jpg', 'Victoria Island Conference Hub', 'Victoria Island', '2026-10-15 13:00:00', '2026-10-15 16:00:00', 'contact-us.php', 'published', 0, 'Events | Partners and Sponsors Impact Brunch', 'A relationship-building event for sharing programme updates, donor stories, and sponsorship opportunities.');

INSERT INTO faqs (question, answer, category, status, sort_order) VALUES
('How can someone support the organisation?', 'Support can come through donations, sponsorships, volunteering, media partnerships, or programme collaboration.', 'General', 'published', 1),
('Can partners sponsor a specific project or campaign?', 'Yes. Sponsors can support specific projects, campaigns, or public events depending on the organisation’s current priorities.', 'Partners', 'published', 2),
('How will updates be shared with supporters?', 'Updates can be shared through the gallery, programme pages, event summaries, and news stories published on the platform.', 'Communications', 'published', 3);
