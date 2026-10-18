<?php
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'Alexandra Concepcion | Virtual Assistant';
$activePage = $activePage ?? '';

if (!isset($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}

$navigationItems = [
    ['label' => 'Home', 'url' => 'index.php', 'key' => 'home'],
    ['label' => 'About', 'url' => 'about.php', 'key' => 'about'],
    ['label' => 'Services', 'url' => 'services.php', 'key' => 'services'],
    ['label' => 'Work', 'url' => 'projects.php', 'key' => 'projects'],
    ['label' => 'Resume', 'url' => 'resume.php', 'key' => 'resume'],
    ['label' => 'Contact', 'url' => 'contact.php', 'key' => 'contact'],
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta
        name="description"
        content="Portfolio of Alexandra Concepcion, Virtual Assistant, Admin Support and Front-End Developer."
    >
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <div id="page-loader" aria-hidden="true"><span></span></div>

    <nav class="navbar navbar-expand-lg fixed-top site-nav">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                Alexandra<span>.</span>
            </a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNav"
                aria-controls="mainNav"
                aria-label="Toggle navigation"
            >
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <?php foreach ($navigationItems as $navigationItem): ?>
                        <li class="nav-item">
                            <a
                                class="nav-link <?= $activePage === $navigationItem['key'] ? 'active' : '' ?>"
                                href="<?= e($navigationItem['url']) ?>"
                                <?= $activePage === $navigationItem['key'] ? 'aria-current="page"' : '' ?>
                            >
                                <?= e($navigationItem['label']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main id="main-content">
