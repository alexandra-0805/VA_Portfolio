<?php
/**
 * TEMPLATE for the private settings file. Contains no real secrets.
 *
 * Local XAMPP: copy it OUTSIDE the web folder to
 *     E:\xampp_db\private\va_portfolio\config.php
 * (one level above htdocs), then fill in the real values.
 *
 * Live host (InfinityFree or any host without a private folder): copy it to
 *     app/config.local.php
 * and upload it with the site. app/ is blocked from the web and git ignores the file.
 *
 * Only include the keys you want to change; everything else uses app/config.php.
 */

return [
    'app' => [
        // Local: 'http://localhost/VA_Portfolio'. Live: 'https://yourname.infinityfreeapp.com'
        'url' => 'http://localhost/VA_Portfolio',
        'debug' => false, // true shows PHP errors on screen; never on a live server
    ],
    'db' => [
        // Local XAMPP values. On InfinityFree, copy them from Control Panel > MySQL Databases:
        //   'host' => 'sql123.infinityfree.com',
        //   'name' => 'if0_12345678_portfolio',
        //   'user' => 'if0_12345678',
        //   'password' => 'your vPanel / hosting account password',
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
        // Live host only: opens admin/setup.php to create the tables and first admin.
        // Use 16+ random characters, and delete this line once you can sign in.
        // 'setup_token' => 'paste-a-long-random-value-here',
    ],
];
