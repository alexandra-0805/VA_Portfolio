-- ------------------------------------------------------------------
-- Upgrade an existing database to the reorganized structure (MariaDB).
--   1. Remove duplicated content rows (keeps the oldest copy).
--   2. Add unique titles so seeding twice can never duplicate again.
--   3. Add the rate_limits table used for brute-force protection.
-- Back up first:  mysqldump -uroot alexandra_portfolio > backup.sql
-- ------------------------------------------------------------------

USE alexandra_portfolio;

DELETE newer FROM services newer JOIN services older ON older.title = newer.title AND older.id < newer.id;
DELETE newer FROM skills newer JOIN skills older ON older.name = newer.name AND older.id < newer.id;
DELETE newer FROM projects newer JOIN projects older ON older.title = newer.title AND older.id < newer.id;
DELETE newer FROM certificates newer JOIN certificates older ON older.title = newer.title AND older.id < newer.id;

ALTER TABLE services ADD UNIQUE KEY IF NOT EXISTS uq_services_title (title);
ALTER TABLE skills ADD UNIQUE KEY IF NOT EXISTS uq_skills_name (name);
ALTER TABLE projects ADD UNIQUE KEY IF NOT EXISTS uq_projects_title (title);
ALTER TABLE projects MODIFY technologies VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE certificates ADD UNIQUE KEY IF NOT EXISTS uq_certificates_title (title);
ALTER TABLE messages ADD INDEX IF NOT EXISTS idx_messages_status_date (status, created_at);

CREATE TABLE IF NOT EXISTS rate_limits (
    bucket CHAR(64) PRIMARY KEY,
    attempts INT UNSIGNED NOT NULL DEFAULT 0,
    window_start DATETIME NOT NULL
);
