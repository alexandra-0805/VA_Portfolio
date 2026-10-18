<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function verify_csrf(bool $adminRequired = false): void
{
    if ($adminRequired && !is_admin()) {
        redirect('signin.php');
    }

    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419);
        exit('Invalid security token. Please go back and try again.');
    }
}

function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect('signin.php');
    }
}

function upload_image(array $file, string $folder): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) {
        return null;
    }

    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

    if (!isset($allowedMimeTypes[$mimeType])) {
        return null;
    }

    $fileName = bin2hex(random_bytes(12)) . '.' . $allowedMimeTypes[$mimeType];
    $directory = __DIR__ . '/../uploads/' . $folder;

    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $fileName)) {
        return null;
    }

    return 'uploads/' . $folder . '/' . $fileName;
}

function replace_resume_pdf(array $file): bool
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($file['size'] > 10 * 1024 * 1024) {
        return false;
    }

    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

    if ($mimeType !== 'application/pdf') {
        return false;
    }

    return move_uploaded_file(
        $file['tmp_name'],
        __DIR__ . '/../assets/Alexandra_Concepcion_VA.pdf'
    );
}
