# Alexandra Portfolio

PHP / MySQL portfolio site with an admin panel, built for XAMPP.

## Project structure

```
VA_Portfolio/
├── index.php, about.php, services.php,      Public pages (URLs unchanged)
│   projects.php, project.php, resume.php,
│   contact.php
├── admin/                                    Admin panel (sign-in required)
│   ├── dashboard.php, messages.php, reports.php, resume.php
│   ├── projects.php, services.php, skills.php, certificates.php   (generic editor)
│   └── auth/                                 signin, signup, forgot/reset password, signout
├── app/                 PRIVATE – blocked from the web
│   ├── bootstrap.php    Required by every page: config, session, helpers
│   ├── config.php       Default (non-secret) settings
│   ├── secrets.example.php  Template for the private settings file
│   ├── lib/             env, helpers, database, security (rate limits), auth,
│   │                    content queries, mailer, uploads
│   ├── admin/           Content editor definitions + generic editor logic
│   └── views/           Layouts: partials/ (head, chatbot), site/, admin/, auth/
├── assets/
│   ├── css/style.css    Public site styles   ·  css/admin.css  Admin-only styles
│   ├── js/main.js       Shared behavior      ·  js/chatbot.js, js/auth.js
│   ├── images/          Portrait, favicon
│   └── docs/            Downloadable resume PDF
├── uploads/             Admin image uploads (scripts can never run here)
├── database/            PRIVATE – schema.sql, seed.sql, migrations/
├── scripts/             PRIVATE – create_admin.php, build_resume_pdf.py
└── vendor/              PRIVATE – Composer packages (PHPMailer)

E:\xampp_db\private\va_portfolio\     OUTSIDE the project and the web folder
├── config.php           Passwords and private settings
└── backups/             Database backups
```

## Setup

1. Copy `app/secrets.example.php` to `E:\xampp_db\private\va_portfolio\config.php`
   (one level above `htdocs`) and fill in the real values. Use a Gmail **App Password**
   (16 letters, no spaces) for `mail.password`, never your normal Gmail password.
2. Import `database/schema.sql`, then `database/seed.sql` for sample content.
   Upgrading an existing database? Back it up, then run the files in
   `database/migrations/` in date order (`..._cleanup_and_security.sql`, then
   `..._feedback_and_notifications.sql`).
3. Create an admin account (you are prompted for the password):
   ```
   e:\xampp_db\php\php.exe scripts/create_admin.php <username> <email>
   ```
   Run the same command again to reset a password.
4. `composer install` if `vendor/` is missing.
5. Open `http://localhost/VA_Portfolio/`. The admin panel is at `/admin/`.

## Feedback and Gmail alerts

- **Feedback** (`feedback.php`, also on the home page): visitors leave a 1–5 star rating
  and a comment. The average, review count, and star breakdown are calculated
  automatically from published feedback. Manage it in **Admin → Feedback** (hide,
  publish, delete). Set `feedback.auto_publish => false` to approve reviews first.
- **Gmail alerts** are sent through the `mail.*` SMTP account to `notify.to`
  (or `mail.contact_to`, then `site.email`):
  - **Visitors**: a visitor's first page view of the day.
  - **Reach me**: a visitor clicks your email, LinkedIn, GitHub, Upwork, or Quora link (once per link per day).
  - **Messages**: every contact form message (reply directly from Gmail).
  - **Feedback**: every new review, with its star rating.
  Turn any of them off with `'notify' => ['visitors' => false, ...]` in the private
  config. Visitor and reach alerts are capped at `notify.activity_daily_cap` per day
  (default 50) to protect Gmail's sending limit.
- Visits are recorded by `signal.php` from a small script in `assets/js/main.js`.
  Your own browsing while signed in to the admin panel, and bots, are ignored.
  Visitors are stored as a one-way hash, not as IP addresses. See **Admin → Visitors**.

## Security notes

- Secrets live only in the private config file outside the web folder, never in
  this project, its code, its SQL files, or git. There are no `.env` files.
- `app/`, `database/`, `scripts/`, `vendor/`, and `.git` return 403 over the web.
- Uploads are checked by real file type, renamed randomly, and cannot execute.
- Every form uses CSRF tokens; sign-out is POST-only.
- Sign-in, password reset, admin signup, and the contact form are rate-limited by IP.
- Admin sessions expire after `admin.idle_timeout_minutes` of inactivity.
- Public admin registration is **off**. Set `admin.signup_enabled => true` only while
  adding an admin, or use `scripts/create_admin.php` instead.
- Stored links must be `http(s)://`, so `javascript:` URLs are rejected.
