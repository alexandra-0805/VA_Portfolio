<?php
/**
 * Admin panel layout (top). Pages must call require_admin() before including.
 */
$pageTitle = ($adminTitle ?? 'Dashboard') . ' | Portfolio Admin';
$noIndex = true;
$extraStyles = ['assets/css/admin.css'];
$currentAdminPage = basename($_SERVER['SCRIPT_NAME'] ?? '');
$adminNavigation = [
    ['Dashboard', 'dashboard.php', 'bi-speedometer2'],
    ['Projects', 'projects.php', 'bi-grid'],
    ['Services', 'services.php', 'bi-stars'],
    ['Skills', 'skills.php', 'bi-bar-chart'],
    ['Certificates', 'certificates.php', 'bi-award'],
    ['Messages', 'messages.php', 'bi-envelope'],
    ['Feedback', 'feedback.php', 'bi-star'],
    ['Visitors', 'visitors.php', 'bi-people'],
    ['Reports', 'reports.php', 'bi-bar-chart-line'],
    ['Resume', 'resume.php', 'bi-file-earmark-person'],
];

require APP_PATH . '/views/partials/head.php';
?>
<body class="admin-body">
    <a class="skip-link" href="#admin-content">Skip to content</a>
    <nav class="navbar site-nav admin-topbar">
        <div class="container">
            <a class="navbar-brand" href="<?= e(url('admin/dashboard.php')) ?>">Admin<span>.</span></a>
            <div class="admin-topbar-actions">
                <a class="admin-view-site" href="<?= e(url('index.php')) ?>" target="_blank" rel="noopener">View site</a>
                <form method="post" action="<?= e(url('admin/auth/signout.php')) ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-dark admin-signout" type="submit">
                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </nav>
    <nav class="admin-nav-bar" aria-label="Admin sections">
        <div class="container">
            <div class="admin-nav-list">
                <?php foreach ($adminNavigation as [$label, $page, $icon]): ?>
                    <a class="admin-nav-link <?= $currentAdminPage === $page ? 'active' : '' ?>" href="<?= e(url('admin/' . $page)) ?>" <?= $currentAdminPage === $page ? 'aria-current="page"' : '' ?>>
                        <i class="bi <?= e($icon) ?>" aria-hidden="true"></i>
                        <?= e($label) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>
    <main id="admin-content" class="container py-5">
        <?= render_alerts() ?>
