# Alexandra Portfolio

PHP/MySQL portfolio site for XAMPP.

## Local setup

1. Import `database/alexandra_portfolio.sql` into MySQL.
2. Confirm the database settings in `config/database.php`, or provide these environment variables:
	- `PORTFOLIO_DB_HOST`
	- `PORTFOLIO_DB_NAME`
	- `PORTFOLIO_DB_USER`
	- `PORTFOLIO_DB_PASSWORD`
3. Set `PORTFOLIO_CONTACT_EMAIL` to the email address that should receive contact form notifications. It defaults to `lykemaeconcepcion@gmail.com`.
4. Run `composer install` to install PHPMailer.
5. Set `PORTFOLIO_SMTP_USER` to the Gmail address used for sending and `PORTFOLIO_SMTP_APP_PASSWORD` to a Gmail App Password. Do not use the normal Gmail password.
6. If the variables are configured in `xampp/apache/conf/httpd.conf`, restart Apache from the XAMPP Control Panel so PHP receives the updated values. The mailer uses an IPv4 SMTP connection because some local Windows setups cannot connect to Gmail over IPv6.
7. Open `http://localhost/VA_AboutMe/` or the port configured by your local PHP server.

The admin area is available at `/admin/signin.php`. Use `/admin/forgot_password.php` if the admin password is forgotten. The reset email is sent to the admin email stored in the `admins` table, and `PORTFOLIO_APP_URL` can be set when the application is not hosted at `http://localhost/VA_AboutMe`. The SQL file contains the existing local-development admin account; change its password before deploying the site publicly.