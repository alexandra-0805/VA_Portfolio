<?php
/**
 * Session hardening and IP-based rate limiting.
 */

function start_secure_session(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('portfolio_session');
    session_set_cookie_params([
        'path' => base_path() . '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => request_is_https(),
    ]);
    session_start();
}

/** True for HTTPS requests, including hosts that end HTTPS at a proxy in front of PHP. */
function request_is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https'
        || (string) ($_SERVER['SERVER_PORT'] ?? '') === '443';
}

function client_ip(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
}

function rate_limit_bucket(string $action): string
{
    return hash('sha256', $action . '|' . client_ip());
}

/**
 * True when this visitor has used up the attempts for an action.
 * Fails open (allows the request) if the rate_limits table is missing,
 * so a missing migration never locks everyone out.
 */
function rate_limited(string $action, int $maxAttempts, int $windowSeconds): bool
{
    try {
        $attempts = db_query(
            'SELECT attempts FROM rate_limits '
            . 'WHERE bucket = ? AND window_start > (NOW() - INTERVAL ? SECOND)',
            [rate_limit_bucket($action), $windowSeconds]
        )->fetchColumn();
    } catch (PDOException $exception) {
        error_log('Rate limit check failed: ' . $exception->getMessage());
        return false;
    }

    return (int) $attempts >= $maxAttempts;
}

/** Count one attempt; starts a fresh window when the old one has expired. */
function record_attempt(string $action, int $windowSeconds): void
{
    try {
        db_query(
            'INSERT INTO rate_limits (bucket, attempts, window_start) VALUES (?, 1, NOW()) '
            . 'ON DUPLICATE KEY UPDATE '
            . 'attempts = IF(window_start > (NOW() - INTERVAL ? SECOND), attempts + 1, 1), '
            . 'window_start = IF(window_start > (NOW() - INTERVAL ? SECOND), window_start, NOW())',
            [rate_limit_bucket($action), $windowSeconds, $windowSeconds]
        );
    } catch (PDOException $exception) {
        error_log('Rate limit update failed: ' . $exception->getMessage());
    }
}

function clear_attempts(string $action): void
{
    try {
        db_query('DELETE FROM rate_limits WHERE bucket = ?', [rate_limit_bucket($action)]);
    } catch (PDOException) {
        // Nothing to clear.
    }
}

/** Only allow http(s) links, so stored URLs can never run `javascript:`. */
function safe_url(string $value): string
{
    $value = trim($value);

    return filter_var($value, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $value)
        ? $value
        : '';
}
