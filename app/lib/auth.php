<?php
/**
 * Admin authentication helpers.
 */

function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}

/** Guard for admin-only pages; also enforces the idle timeout. */
function require_admin(): void
{
    if (!is_admin()) {
        redirect('admin/auth/signin.php');
    }

    $idleSeconds = max(5, (int) config('admin.idle_timeout_minutes')) * 60;

    if (time() - ($_SESSION['admin_last_seen'] ?? 0) > $idleSeconds) {
        logout_admin();
        start_secure_session();
        flash('warning', 'You were signed out after a period of inactivity.');
        redirect('admin/auth/signin.php');
    }

    $_SESSION['admin_last_seen'] = time();
}

/** Auth pages (sign in, reset, ...) are pointless when already signed in. */
function redirect_if_admin(): void
{
    if (is_admin()) {
        redirect('admin/dashboard.php');
    }
}

function login_admin(array $admin): void
{
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_last_seen'] = time();
}

function logout_admin(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }

    session_destroy();
}

const PASSWORD_MIN_LENGTH = 12;

/** Validate a new password + confirmation; returns an error message or ''. */
function password_error(string $password, string $confirmation): string
{
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return 'Your password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
    }

    if (strlen($password) > 1024) {
        return 'Your password is too long.';
    }

    if ($password !== $confirmation) {
        return 'The password confirmation does not match.';
    }

    return '';
}

/** Six-digit one-time code and its storage hash. */
function make_one_time_code(): array
{
    $code = (string) random_int(100000, 999999);

    return [$code, hash('sha256', $code)];
}

function one_time_code_matches(string $code, string $hash): bool
{
    return preg_match('/^\d{6}$/', $code) === 1 && hash_equals($hash, hash('sha256', $code));
}
