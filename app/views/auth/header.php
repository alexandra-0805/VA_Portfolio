<?php
/**
 * Layout (top) for sign in, sign up, and password reset pages.
 * Expects: $pageTitle, $authHeading. Optional: $authIntro, $errors, $success.
 */
$pageTitle .= ' | Portfolio Admin';
$noIndex = true;
$extraStyles = ['assets/css/admin.css'];

require APP_PATH . '/views/partials/head.php';
?>
<body>
    <main class="admin-signin min-vh-100 d-flex align-items-center">
        <span class="auth-orb auth-orb-one" aria-hidden="true"></span>
        <span class="auth-orb auth-orb-two" aria-hidden="true"></span>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5 col-xl-4">
                    <section class="soft-panel admin-auth-card">
                        <div class="admin-auth-brand">
                            <span class="admin-auth-brand-icon" aria-hidden="true"><i class="bi bi-shield-lock"></i></span>
                            <span>Portfolio admin</span>
                        </div>
                        <div class="section-kicker mb-2">Secure workspace</div>
                        <h1 class="h3 fw-bold mb-2"><?= e($authHeading) ?></h1>
                        <?php if (!empty($authIntro)): ?>
                            <p class="muted"><?= e($authIntro) ?></p>
                        <?php endif; ?>

                        <?= render_alerts($errors ?? [], $success ?? '') ?>
