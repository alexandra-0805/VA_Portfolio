-- ------------------------------------------------------------------
-- Sample portfolio content. INSERT IGNORE + unique titles make this
-- safe to re-run: existing rows are never duplicated.
-- No admin account is seeded; run `php scripts/create_admin.php`.
-- ------------------------------------------------------------------

USE alexandra_portfolio;

INSERT IGNORE INTO services (title, description, icon) VALUES
('Administrative Support', 'Keep calendars, documents, and recurring tasks organized and moving.', 'bi-kanban'),
('Data Entry', 'Accurate data entry, cleanup, and spreadsheet support.', 'bi-table'),
('Email & Client Communication', 'Professional, thoughtful responses and inbox assistance.', 'bi-chat-square-text'),
('Internet Research', 'Clear, useful research summarized for action.', 'bi-search'),
('File Management', 'Simple, consistent digital organization and file naming.', 'bi-folder2-open'),
('Customer Support', 'Patient customer care and practical issue resolution.', 'bi-headset'),
('Social Media Assistance', 'Content scheduling, captions, and day-to-day social support.', 'bi-share'),
('Canva Design Support', 'Clean graphics for posts, presentations, and campaigns.', 'bi-palette'),
('AI Content Creation', 'AI-assisted drafts, ideas, and workflow improvements.', 'bi-stars'),
('Front-End Development', 'Responsive interfaces built with foundational web skills.', 'bi-code-slash'),
('Graphic Design', 'Visual layouts shaped around your message and audience.', 'bi-vector-pen');

INSERT IGNORE INTO skills (name, category, proficiency) VALUES
('Microsoft Office', 'Productivity', 82), ('Google Workspace', 'Productivity', 84),
('Canva', 'Design', 88), ('AI Tools', 'Digital', 82),
('Basic Web Development', 'Development', 72), ('HTML', 'Development', 78),
('CSS', 'Development', 76), ('JavaScript', 'Development', 68),
('PHP', 'Development', 65), ('MySQL', 'Development', 64),
('Data Entry', 'Administration', 90), ('Internet Research', 'Administration', 88),
('File Management', 'Administration', 86), ('Customer Support', 'Communication', 85);

INSERT IGNORE INTO projects (title, description, technologies, image, project_url) VALUES
('Bakeshop Website', 'A friendly front-end website concept designed to improve customer engagement.', 'HTML, CSS, JavaScript', 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=900&q=80', 'https://drive.google.com/drive/folders/1x1cmwo_GXkbv3--IOjZ4E0WYpWzk7cJ7?usp=sharing'),
('Wooden Perfume Bottle', 'An innovative product concept developed and presented as a team.', 'Research, Presentation, Design', 'https://images.unsplash.com/photo-1594035910387-fea47794261f?auto=format&fit=crop&w=900&q=80', 'https://drive.google.com/drive/folders/1tiwzumF9iTp8SrpSbn3vqVrnbLfPrGKK?usp=sharing'),
('Digital Content Projects', 'Social media and promotional graphics created for small business ideas.', 'Canva, Content Planning, AI Tools', 'https://images.unsplash.com/photo-1558655146-9f40138edfeb?auto=format&fit=crop&w=900&q=80', 'https://drive.google.com/drive/folders/13AQiPXZFps0GQXkdQPmDbbFtpRQhavoY?usp=sharing'),
('Web Development Portfolio', 'A collection of responsive web development work built with HTML, CSS, and JavaScript.', 'HTML, CSS, JavaScript', 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=900&q=80', 'https://drive.google.com/drive/folders/11DS32zj3hPiS7ngc4R11gXnbi9aa09xv?usp=sharing');

INSERT IGNORE INTO certificates (title, issuer, date, url, description) VALUES
('Entrepreneurship', 'MIBEX', 'January 2024', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'Entrepreneurship certificate.'),
('Establishing & Operating Micro-Small Medium Enterprises', 'MIBEX', 'April 2024', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'Business operations certificate.'),
('Most Sustainable Product in Mini Customized Perfume Bottle', 'MIBEX', 'April 2024', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'Sustainability recognition.'),
('Best Non-food in Product', 'MIBEX', 'April 2024', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'Product development recognition.'),
('Visual Graphic Design NC II', 'TESDA', 'February 2026', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'Visual graphic design certification.'),
('Certificate of Content Creation', 'MIBEX', 'April 2026', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'Certificate of content creation.'),
('Certificate of MSMEs', 'MIBEX', 'April 2026', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'Certificate for micro, small, and medium enterprise learning.'),
('NC II Logo Design', 'TESDA', 'April 2026', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'NC II specialization certificate in logo design.'),
('NC II Print Media', 'TESDA', 'April 2026', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'NC II specialization certificate in print media.'),
('NC II Product Packaging', 'TESDA', 'April 2026', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'NC II specialization certificate in product packaging.'),
('NC II User Experience', 'TESDA', 'April 2026', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'NC II specialization certificate in user experience.'),
('NC II User Interface', 'TESDA', 'April 2026', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'NC II specialization certificate in user interface.'),
('NC II Visual Graphic Design', 'TESDA', 'April 2026', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'NC II specialization certificate in visual graphic design.'),
('NC III Product & Window Design', 'TESDA', 'April 2026', 'https://drive.google.com/drive/folders/1yp7-SgJTQgzfwyaOOZA_OB5dkwGu-0Ek?usp=sharing', 'NC III specialization certificate in product and window design.');
