<?php
/**
 * Create an admin account or reset an existing admin's password from the
 * command line, so no password hash ever needs to live in the SQL files.
 *
 * Usage (from the project folder):
 *   php scripts/create_admin.php <username> <email>
 * You will be prompted for the password (input is not stored in shell history).
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
require dirname(__DIR__) . '/app/bootstrap.php';

[, $username, $email] = $argv + [null, '', ''];

if (!preg_match('/^[A-Za-z0-9._-]{3,80}$/', $username) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Usage: php scripts/create_admin.php <username> <email>\n");
    exit(1);
}

$readSecret = static function (string $prompt): string {
    echo $prompt;
    if (DIRECTORY_SEPARATOR === '/') {
        shell_exec('stty -echo');
    }
    $value = rtrim((string) fgets(STDIN), "\r\n");
    if (DIRECTORY_SEPARATOR === '/') {
        shell_exec('stty echo');
    }
    echo PHP_EOL;

    return $value;
};

$password = $readSecret('New password (min ' . PASSWORD_MIN_LENGTH . ' characters): ');

if ($error = password_error($password, $readSecret('Confirm password: '))) {
    fwrite(STDERR, $error . PHP_EOL);
    exit(1);
}

db_query(
    'INSERT INTO admins (username, email, password) VALUES (?, ?, ?) '
    . 'ON DUPLICATE KEY UPDATE email = VALUES(email), password = VALUES(password)',
    [$username, $email, password_hash($password, PASSWORD_DEFAULT)]
);

echo "Admin '{$username}' is ready. Sign in at /admin/auth/signin.php" . PHP_EOL;
