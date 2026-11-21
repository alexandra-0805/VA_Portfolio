<?php
/**
 * Shared <head> for every layout.
 * Expects: $pageTitle. Optional: $pageDescription, $noIndex.
 */
$pageDescription ??= 'Alexandra Concepcion is a Virtual Assistant in Cavite, Philippines, offering administrative support, customer service, data entry, and digital support for growing businesses.';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <?php if (!empty($noIndex)): ?>
        <meta name="robots" content="noindex, nofollow">
    <?php else: ?>
        <meta property="og:type" content="website">
        <meta property="og:title" content="<?= e($pageTitle) ?>">
        <meta property="og:description" content="<?= e($pageDescription) ?>">
    <?php endif; ?>
    <meta name="theme-color" content="#fffdf8">
    <link rel="icon" href="<?= e(asset('assets/images/favicon.svg')) ?>" type="image/svg+xml">
    <script>document.documentElement.classList.add('js');</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= e(asset('assets/css/style.css')) ?>">
    <?php foreach ($extraStyles ?? [] as $stylesheet): ?>
        <link rel="stylesheet" href="<?= e(asset($stylesheet)) ?>">
    <?php endforeach; ?>
</head>
