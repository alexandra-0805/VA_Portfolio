-- ------------------------------------------------------------------
-- Portfolio database structure. Safe to re-run (IF NOT EXISTS).
-- Then load seed.sql for sample content, and create an admin with:
--   php scripts/create_admin.php
-- ------------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS alexandra_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE alexandra_portfolio;

-- Admin accounts and password resets ------------------------------------

CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    email VARCHAR(160) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin_password_resets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    otp_hash CHAR(64) DEFAULT NULL,
    otp_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    expires_at DATETIME NOT NULL,
    used_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_password_reset_admin FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    INDEX idx_password_reset_lookup (admin_id, expires_at, used_at)
);

-- Brute-force / spam protection (sign in, password reset, contact form).
CREATE TABLE IF NOT EXISTS rate_limits (
    bucket CHAR(64) PRIMARY KEY,
    attempts INT UNSIGNED NOT NULL DEFAULT 0,
    window_start DATETIME NOT NULL
);

-- Portfolio content -----------------------------------------------------

CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    technologies VARCHAR(255) NOT NULL DEFAULT '',
    image VARCHAR(500) DEFAULT NULL,
    project_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_projects_title (title)
);

CREATE TABLE IF NOT EXISTS services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(80) NOT NULL DEFAULT 'bi-stars',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_services_title (title)
);

CREATE TABLE IF NOT EXISTS skills (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,
    proficiency TINYINT UNSIGNED NOT NULL DEFAULT 70,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_skills_name (name)
);

CREATE TABLE IF NOT EXISTS certificates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    issuer VARCHAR(150) DEFAULT NULL,
    date VARCHAR(80) DEFAULT NULL,
    image VARCHAR(500) DEFAULT NULL,
    url VARCHAR(500) DEFAULT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_certificates_title (title)
);

-- Contact form inbox ----------------------------------------------------

CREATE TABLE IF NOT EXISTS messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(160) NOT NULL,
    subject VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread','read') NOT NULL DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_messages_status_date (status, created_at)
);
