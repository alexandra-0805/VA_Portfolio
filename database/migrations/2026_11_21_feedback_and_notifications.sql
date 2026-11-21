-- ------------------------------------------------------------------
-- Adds the feedback (ratings) section and visitor / "reach me" activity.
-- Safe to re-run (IF NOT EXISTS). Nothing existing is changed or removed.
-- ------------------------------------------------------------------

USE alexandra_portfolio;

-- Visitor feedback with a 1-5 star rating.
CREATE TABLE IF NOT EXISTS feedback (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(160) DEFAULT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    comment TEXT NOT NULL,
    status ENUM('published','hidden') NOT NULL DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_feedback_rating CHECK (rating BETWEEN 1 AND 5),
    INDEX idx_feedback_status_date (status, created_at)
);

-- Page views ("visit") and clicks on contact links ("reach").
-- Visitors are stored as a one-way hash, never as a raw IP address.
CREATE TABLE IF NOT EXISTS site_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('visit','reach') NOT NULL,
    detail VARCHAR(255) NOT NULL DEFAULT '',
    referrer VARCHAR(255) NOT NULL DEFAULT '',
    device VARCHAR(80) NOT NULL DEFAULT '',
    visitor_hash CHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_site_events_type_date (type, created_at),
    INDEX idx_site_events_visitor (visitor_hash, type, created_at)
);
