<?php
/**
 * Public site layout (top). Expects $pageTitle and $activePage.
 */
$pageTitle ??= config('site.name') . ' | Virtual Assistant';
$activePage ??= '';
$navigationItems = [
    'home' => ['Home', 'index.php'],
    'about' => ['About', 'about.php'],
    'services' => ['Services', 'services.php'],
    'projects' => ['Work', 'projects.php'],
    'resume' => ['Resume', 'resume.php'],
];

require APP_PATH . '/views/partials/head.php';
?>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>

    <nav class="navbar navbar-expand-lg fixed-top site-nav" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand" href="<?= e(url('index.php')) ?>" aria-label="<?= e(config('site.name')) ?>, home">
                Alexandra<span>.</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <?php foreach ($navigationItems as $key => [$label, $href]): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $activePage === $key ? 'active' : '' ?>" href="<?= e(url($href)) ?>" <?= $activePage === $key ? 'aria-current="page"' : '' ?>>
                                <?= e($label) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li class="nav-item nav-item-cta">
                        <a class="btn btn-primary btn-nav <?= $activePage === 'contact' ? 'active' : '' ?>" href="<?= e(url('contact.php')) ?>" <?= $activePage === 'contact' ? 'aria-current="page"' : '' ?>>
                            Get in touch
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main id="main-content">
