-- ------------------------------------------------------------------
-- Add the Mentora PH project and the skills/tools used to build it.
-- INSERT IGNORE: safe to re-run, existing rows are left untouched.
-- ------------------------------------------------------------------

USE alexandra_portfolio;

INSERT IGNORE INTO skills (name, category, proficiency) VALUES
('Git', 'Development', 65), ('XAMPP & phpMyAdmin', 'Development', 75),
('AI Integration', 'Development', 70), ('API Integration', 'Development', 65),
('Team Leadership', 'Communication', 80);

INSERT IGNORE INTO projects (title, description, technologies, image, project_url) VALUES
('Mentora PH Recruitment System', 'A web-based HR recruitment and onboarding system built by a team I led as lead developer. It handles recruitment links, applications with resume upload, AI resume screening matched to department skill gaps, intern training tracking and AI assessment, evaluations, messaging, notifications, Google sign-in, and consolidated PDF/CSV reports for HR admins, agents, interns, and Employee VA applicants.', 'PHP, MySQL, HTML, CSS, JavaScript, XAMPP, phpMyAdmin, Git, Google OAuth, Gmail SMTP, AI APIs', 'assets/images/projects/mentora.png', NULL);
