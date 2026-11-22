<?php
/**
 * Single entry point required by every page (public, admin, and auth).
 * Loads configuration, helpers, and starts a hardened session.
 */

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', __DIR__);

// Private settings (passwords). The first file that exists is used:
//  1. outside the web folder: <xampp>/private/va_portfolio/config.php (local XAMPP)
//  2. app/config.local.php: for web hosts that only allow files inside the site.
//     app/ is blocked from the web (.htaccess) and the file is ignored by git.
define('SECRETS_FILE', (static function (): string {
    foreach ([dirname(ROOT_PATH, 2) . '/private/va_portfolio/config.php', APP_PATH . '/config.local.php'] as $file) {
        if (is_file($file)) {
            return $file;
        }
    }
    return '';
})());

$GLOBALS['__config'] = array_replace_recursive(
    require APP_PATH . '/config.php',
    SECRETS_FILE !== '' ? (array) require SECRETS_FILE : []
);

ini_set('display_errors', config('app.debug') ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

require APP_PATH . '/lib/helpers.php';
require APP_PATH . '/lib/database.php';
require APP_PATH . '/lib/security.php';
require APP_PATH . '/lib/auth.php';
require APP_PATH . '/lib/content.php';

start_secure_session();

/** Read a setting with dot notation, e.g. config('mail.host'). */
function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['__config'];

    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}
