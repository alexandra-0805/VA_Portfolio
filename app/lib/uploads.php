<?php
/**
 * Validated file uploads. Files are checked by their real MIME type and
 * stored under random names, so uploads can never be executed as code.
 */

const IMAGE_MAX_BYTES = 5 * 1024 * 1024;
const PDF_MAX_BYTES = 10 * 1024 * 1024;

/**
 * Store an uploaded image in uploads/<folder>/.
 * Returns the project-relative path, null when no file was sent,
 * or throws RuntimeException for an invalid file.
 */
function upload_image(array $file, string $folder): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > IMAGE_MAX_BYTES) {
        throw new RuntimeException('Images must be JPG, PNG, or WebP and 5 MB or smaller.');
    }

    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

    if (!isset($extensions[$mimeType]) || !preg_match('/^[a-z0-9_-]+$/', $folder)) {
        throw new RuntimeException('Images must be JPG, PNG, or WebP and 5 MB or smaller.');
    }

    $directory = ROOT_PATH . '/uploads/' . $folder;

    if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
        throw new RuntimeException('The upload folder could not be created.');
    }

    $fileName = bin2hex(random_bytes(12)) . '.' . $extensions[$mimeType];

    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $fileName)) {
        throw new RuntimeException('The image could not be saved.');
    }

    return 'uploads/' . $folder . '/' . $fileName;
}

/** Replace the public resume PDF; returns false for an invalid upload. */
function replace_resume_pdf(array $file): bool
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || $file['size'] > PDF_MAX_BYTES) {
        return false;
    }

    if ((new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) !== 'application/pdf') {
        return false;
    }

    $target = ROOT_PATH . '/' . config('site.resume_pdf');

    if (!is_dir(dirname($target))) {
        mkdir(dirname($target), 0755, true);
    }

    return move_uploaded_file($file['tmp_name'], $target);
}
