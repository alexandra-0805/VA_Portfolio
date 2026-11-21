<?php
/**
 * General helpers shared by public, admin, and auth pages.
 */

/** Escape a value for safe HTML output. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . (preg_match('#^https?://#', $path) ? $path : url($path)));
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/** Trimmed string from POST data, capped to a sane length. */
function input(string $key, int $maxLength = 5000): string
{
    $value = $_POST[$key] ?? '';

    return is_string($value) ? mb_substr(trim($value), 0, $maxLength) : '';
}

/** URL path of the project root, e.g. "/VA_Portfolio" (no trailing slash). */
function base_path(): string
{
    static $basePath = null;

    if ($basePath === null) {
        $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
        $root = realpath(ROOT_PATH) ?: ROOT_PATH;
        $relative = $documentRoot !== '' && str_starts_with(strtolower($root), strtolower($documentRoot))
            ? substr($root, strlen($documentRoot))
            : '';
        $basePath = rtrim(str_replace('\\', '/', $relative), '/');
    }

    return $basePath;
}

/** Absolute URL path for a project-relative path, e.g. url('admin/dashboard.php'). */
function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}

/** URL for a static asset, cache-busted with the file's modification time. */
function asset(string $path): string
{
    $file = ROOT_PATH . '/' . ltrim($path, '/');
    $version = is_file($file) ? '?v=' . filemtime($file) : '';

    return url($path) . $version;
}

/** Resolve a stored image value (uploaded path or external URL) for output. */
function media_url(?string $value, string $fallback = ''): string
{
    $value = trim((string) $value);

    if ($value === '') {
        return $fallback;
    }

    return preg_match('#^https?://#i', $value) ? $value : url($value);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_is_valid(): bool
{
    $token = $_POST['csrf'] ?? '';

    return is_string($token) && hash_equals($_SESSION['csrf'] ?? '', $token);
}

/** Stop the request when the CSRF token is missing or wrong. */
function verify_csrf(): void
{
    if (!csrf_is_valid()) {
        http_response_code(403);
        exit('Your session expired. Please go back, refresh the page, and try again.');
    }
}

/** One-time message shown after a redirect. */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/** @return list<array{type: string, message: string}> */
function take_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $messages;
}

/** Render Bootstrap alerts for an error list and/or success message. */
function render_alerts(array $errors = [], string $success = ''): string
{
    $html = '';

    foreach (take_flashes() as $flash) {
        $html .= alert_html($flash['type'], $flash['message']);
    }

    if ($success !== '') {
        $html .= alert_html('success', $success);
    }

    if ($errors) {
        $html .= '<div class="alert alert-danger" role="alert">'
            . implode('<br>', array_map('e', $errors)) . '</div>';
    }

    return $html;
}

function alert_html(string $type, string $message): string
{
    $role = $type === 'danger' ? 'alert' : 'status';

    return '<div class="alert alert-' . e($type) . '" role="' . $role . '">' . e($message) . '</div>';
}
