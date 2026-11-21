<?php
require __DIR__ . '/app/bootstrap.php';

$pageTitle = 'Projects | Alexandra Concepcion';
$activePage = 'projects';
$projects = get_projects();

require APP_PATH . '/views/site/header.php';
?>

<section class="page-banner">
    <div class="container">
        <div class="section-kicker mb-3">Portfolio</div>
        <h1 class="section-title">Work with <span>purpose.</span></h1>
        <p class="hero-copy mt-4">
            A selection of digital, business, and web projects built through collaboration and care.
        </p>
    </div>
</section>

<section>
    <div class="container">
        <?php if ($projects): ?>
            <div class="row g-4">
                <?php foreach ($projects as $project): ?>
                    <div class="col-md-6 col-lg-4 animate">
                        <a class="project-card project-card-link" href="<?= e(url('project.php?id=' . (int) $project['id'])) ?>" aria-label="View <?= e($project['title']) ?> project">
                            <img class="project-image" src="<?= e(project_image($project)) ?>" alt="" loading="lazy">
                            <div class="project-body">
                                <h2 class="h3"><?= e($project['title']) ?></h2>
                                <p class="mb-3"><?= e($project['description']) ?></p>
                                <?php if ($technologies = split_list($project['technologies'])): ?>
                                    <div class="d-flex flex-wrap gap-2 mb-3" aria-label="Technologies used">
                                        <?php foreach ($technologies as $technology): ?>
                                            <span class="tech-pill"><?= e($technology) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty-state">New projects are on the way.</p>
        <?php endif; ?>
    </div>
</section>

<?php require APP_PATH . '/views/site/footer.php'; ?>
