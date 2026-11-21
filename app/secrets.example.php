<?php
/**
 * TEMPLATE for the private settings file. Contains no real secrets.
 *
 * Copy it OUTSIDE the web folder to:
 *     E:\xampp_db\private\va_portfolio\config.php
 * (one level above htdocs), then fill in the real values.
 * Only include the keys you want to change; everything else uses app/config.php.
 */

return [
    'app' => [
        'url' => 'http://localhost/VA_Portfolio',
        'debug' => false, // true shows PHP errors on screen; never on a live server
    ],
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'alexandra_portfolio',
        'user' => 'root',
        'password' => '',
    ],
    'mail' => [
        'username' => 'you@gmail.com',
        'password' => 'abcdefghijklmnop', // Gmail App Password: 16 letters, no spaces
        'contact_to' => 'you@gmail.com',
    ],
    // Optional: change which alerts reach your Gmail (all are on by default).
    // 'notify' => [
    //     'to' => 'you@gmail.com',
    //     'visitors' => true,
    //     'reach' => true,
    //     'messages' => true,
    //     'feedback' => true,
    //     'activity_daily_cap' => 50,
    // ],
    'admin' => [
        'signup_enabled' => false, // enable only briefly to add an admin
        'idle_timeout_minutes' => 120,
    ],
];
