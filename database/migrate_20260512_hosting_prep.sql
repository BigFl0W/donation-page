-- Hosting prep migration for older databases.
-- Use this only when upgrading an existing database.
-- Fresh installs should use database/schema.sql instead.

ALTER TABLE admins
    ADD COLUMN avatar VARCHAR(255) NULL AFTER full_name;

ALTER TABLE programmes
    ADD COLUMN content_type VARCHAR(20) NOT NULL DEFAULT 'cause' AFTER slug,
    ADD COLUMN mission_statement TEXT NULL AFTER summary;

ALTER TABLE donations
    ADD COLUMN metadata TEXT NULL AFTER payment_reference;

ALTER TABLE contact_messages
    CHANGE COLUMN sender_name name VARCHAR(190) NOT NULL,
    CHANGE COLUMN sender_email email VARCHAR(190) NOT NULL,
    ADD COLUMN phone VARCHAR(60) NULL AFTER email,
    MODIFY COLUMN message TEXT NOT NULL,
    ADD COLUMN admin_reply TEXT NULL AFTER status,
    ADD COLUMN replied_at DATETIME NULL AFTER admin_reply;

CREATE TABLE IF NOT EXISTS blog_comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id BIGINT UNSIGNED NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    comment_text TEXT NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    status ENUM('pending', 'approved', 'spam') DEFAULT 'pending',
    is_admin_reply TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_blog_comments_post_id (post_id),
    KEY idx_blog_comments_parent_id (parent_id),
    CONSTRAINT fk_blog_comments_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    CONSTRAINT fk_blog_comments_parent FOREIGN KEY (parent_id) REFERENCES blog_comments(id) ON DELETE CASCADE
);
