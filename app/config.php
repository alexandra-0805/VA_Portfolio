<?php
/**
 * Default (non-secret) settings.
 *
 * Passwords and other private values are NOT stored in this project. They live
 * in a private PHP file outside the web folder, by default:
 *     E:\xampp_db\private\va_portfolio\config.php
 * Its values override these defaults. Template: app/secrets.example.php
 */

return [
    'app' => [
        // Public base URL, used for links inside emails (never taken from the request).
        'url' => 'http://localhost/VA_Portfolio',
        'debug' => false,
    ],

    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'alexandra_portfolio',
        'user' => 'root',
        'password' => '',
    ],

    'mail' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => '',
        'password' => '',
        // Inbox that receives contact form notifications (falls back to site.email).
        'contact_to' => '',
    ],

    'admin' => [
        // Public admin registration stays closed unless enabled in the private file.
        'signup_enabled' => false,
        'idle_timeout_minutes' => 120,
    ],

    // Public profile details shown across the site (not secret).
    'site' => [
        'name' => 'Alexandra Concepcion',
        'email' => 'lykemaeconcepcion@gmail.com',
        'phone' => '+63 994 098 0220',
        'location' => 'Cavite, Philippines',
        'linkedin' => 'https://www.linkedin.com/in/alexandra-concepcion',
        'github' => 'https://github.com/alexandra-0805',
        'resume_pdf' => 'assets/docs/Alexandra_Concepcion_VA.pdf',
    ],
];
