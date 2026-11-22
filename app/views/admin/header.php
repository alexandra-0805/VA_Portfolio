<?php
/**
 * Admin panel layout (top). Pages must call require_admin() before including.
 */
$pageTitle = ($adminTitle ?? 'Dashboard') . ' | Portfolio Admin';
$noIndex = true;
$extraStyles = ['assets/css/admin.css'];
$currentAdminPage = basename($_SERVER['SCRIPT_NAME'] ?? '');
$adminNavigation = [
    ['Dashboard', 'dashboard.php'],
    ['Projects', 'projects.php'],
    ['Services', 'services.php'],
    ['Skills', 'skills.php'],
    ['Certificates', 'certificates.php'],
    ['Messages', 'messages.php'],
    ['Feedback', 'feedback.php'],
    ['Visitors', 'visitors.php'],
    ['Reports', 'reports.php'],
    ['Resume', 'resume.php'],
    ['Settings', 'settings.php'],
];

// The "Admin." logo returns to the section you're in (Dashboard on any other page).
$adminHomePage = in_array($currentAdminPage, array_column($adminNavigation, 1), true)
    ? $currentAdminPage
    : 'dashboard.php';

require APP_PATH . '/views/partials/head.php';
?>
<body class="admin-body">
    <a class="skip-link" href="#admin-content">Skip to content</a>
    <?php /* One header that stays pinned to the top while scrolling (see admin.css). */ ?>
    <header class="navbar site-nav admin-topbar">
        <div class="container">
            <a class="navbar-brand" href="<?= e(url('admin/' . $adminHomePage)) ?>">Admin<span>.</span></a>

            <nav class="admin-nav-list" aria-label="Admin sections">
                <?php /* $navLabel/$navPage: don't reuse $page, the editor pages need theirs. */ ?>
                <?php foreach ($adminNavigation as $navIndex => [$navLabel, $navPage]): ?>
                    <a class="admin-nav-link <?= $currentAdminPage === $navPage ? 'active' : '' ?>" style="--i: <?= $navIndex ?>" href="<?= e(url('admin/' . $navPage)) ?>" <?= $currentAdminPage === $navPage ? 'aria-current="page"' : '' ?>>
                        <?= e($navLabel) ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="admin-topbar-actions">
                <?php $themeToggleClass = ''; require APP_PATH . '/views/partials/theme_toggle.php'; ?>
                <form method="post" action="<?= e(url('admin/auth/signout.php')) ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-dark admin-signout" type="submit">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </header>
    <main id="admin-content" class="container py-5">
        <?= render_alerts() ?>
