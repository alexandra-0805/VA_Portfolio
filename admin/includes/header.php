<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();

$adminTitle = $adminTitle ?? 'Admin | Alexandra Portfolio';
$adminPage = basename($_SERVER['PHP_SELF']);
$adminNavigation = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php', 'page' => 'dashboard.php', 'icon' => 'bi-speedometer2'],
    ['label' => 'Projects', 'url' => 'projects.php', 'page' => 'projects.php', 'icon' => 'bi-grid'],
    ['label' => 'Services', 'url' => 'services.php', 'page' => 'services.php', 'icon' => 'bi-stars'],
    ['label' => 'Skills', 'url' => 'skills.php', 'page' => 'skills.php', 'icon' => 'bi-bar-chart'],
    ['label' => 'Messages', 'url' => 'messages.php', 'page' => 'messages.php', 'icon' => 'bi-envelope'],
    ['label' => 'Reports', 'url' => 'reports.php', 'page' => 'reports.php', 'icon' => 'bi-bar-chart-line'],
    ['label' => 'Certificates', 'url' => 'certificates.php', 'page' => 'certificates.php', 'icon' => 'bi-award'],
    ['label' => 'Resume', 'url' => 'resume.php', 'page' => 'resume.php', 'icon' => 'bi-file-earmark-person'],
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($adminTitle) ?></title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >
    <link rel="stylesheet" href="../assets/css/style.css?v=7">
</head>
<body>
    <a class="skip-link" href="#admin-content">Skip to content</a>
    <nav class="navbar site-nav admin-topbar">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                Admin
            </a>
            <div class="admin-topbar-actions">
                <a
                    class="admin-view-site"
                    href="../index.php"
                    target="_blank"
                    rel="noopener"
                >
                    View site
                </a>
                <a class="btn btn-outline-dark admin-signout" href="signout.php" aria-label="Sign out" title="Sign out">
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                    Sign out
                </a>
            </div>
        </div>
    </nav>
    <nav class="admin-nav-bar" aria-label="Admin sections">
        <div class="container">
            <div class="admin-nav-list">
                <?php foreach ($adminNavigation as $navigationItem): ?>
                    <a
                        class="admin-nav-link <?= $adminPage === $navigationItem['page'] ? 'active' : '' ?>"
                        href="<?= e($navigationItem['url']) ?>"
                        <?= $adminPage === $navigationItem['page'] ? 'aria-current="page"' : '' ?>
                    >
                        <i class="bi <?= e($navigationItem['icon']) ?>" aria-hidden="true"></i>
                        <?= e($navigationItem['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>
    <main id="admin-content" class="container py-5">
