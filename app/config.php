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

    // Automatic Gmail alerts. Sent with the mail.* SMTP account above.
    'notify' => [
        // Inbox for alerts (falls back to mail.contact_to, then site.email).
        'to' => '',
        'visitors' => true,  // a visitor's first page view of the day
        'reach' => true,     // a visitor clicks your email / LinkedIn / GitHub / Upwork / Quora link
        'messages' => true,  // a contact form message
        'feedback' => true,  // new feedback with a star rating
        // Max visitor + reach alerts per day, to stay under Gmail's sending limit. 0 = no limit.
        'activity_daily_cap' => 50,
        'timezone' => 'Asia/Manila',
    ],

    'feedback' => [
        // true: new feedback shows on the site right away. false: it waits until you publish it in the admin panel.
        'auto_publish' => true,
    ],

    'admin' => [
        // Public admin registration stays closed unless enabled in the private file.
        'signup_enabled' => false,
        'idle_timeout_minutes' => 120,
        // Opens admin/setup.php (first admin on a host without a command line).
        // Set a long random value only in the private file, and remove it after setup.
        'setup_token' => '',
    ],

    // Public profile details shown across the site (not secret).
    'site' => [
        'name' => 'Alexandra Concepcion',
        'email' => 'lykemaeconcepcion@gmail.com',
        'phone' => '+63 994 098 0220',
        'location' => 'Cavite, Philippines',
        'linkedin' => 'https://www.linkedin.com/in/alexandra-concepcion',
        'github' => 'https://github.com/alexandra-0805',
        'upwork' => 'https://www.upwork.com/freelancers/~01bcf2086aeec075ba',
        'quora' => 'https://www.quora.com/profile/Alexandra-Concepcion-9',
        'resume_pdf' => 'assets/docs/Alexandra_Concepcion_VA.pdf',
    ],
];
