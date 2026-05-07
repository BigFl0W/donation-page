CREATE DATABASE IF NOT EXISTS donation_page CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
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

INSERT INTO roles (name, description) VALUES
('super_admin', 'Full access to platform administration'),
('admin', 'Manage content, donations, and operations'),
('editor', 'Manage public content only'),
('finance', 'Review donation and gateway activity');
