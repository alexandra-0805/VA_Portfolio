<?php
require __DIR__ . '/app/bootstrap.php';

$project = get_project((int) ($_GET['id'] ?? 0));
$activePage = 'projects';

if (!$project) {
    http_response_code(404);
    $pageTitle = 'Project not found | Alexandra Concepcion';
    require APP_PATH . '/views/site/header.php';
    ?>
    <section class="page-banner">
        <div class="container">
            <div class="section-kicker mb-3">Portfolio</div>
            <h1 class="section-title">Project <span>not found.</span></h1>
            <p class="hero-copy mt-4">This project may have been removed or is no longer available.</p>
            <a class="btn btn-primary mt-3" href="<?= e(url('projects.php')) ?>">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to projects
            </a>
        </div>
    </section>
    <?php
    require APP_PATH . '/views/site/footer.php';
    exit;
}

$pageTitle = $project['title'] . ' | Alexandra Concepcion';
$pageDescription = $project['description'];
$technologies = split_list($project['technologies']);
$projectUrl = safe_url((string) $project['project_url']);

require APP_PATH . '/views/site/header.php';
?>

<section class="project-detail-hero">
    <div class="container">
        <a class="project-back-link" href="<?= e(url('projects.php')) ?>">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to projects
        </a>
        <div class="project-detail-grid">
            <div>
                <div class="section-kicker mb-3">Selected project</div>
                <h1 class="section-title"><?= e($project['title']) ?></h1>
                <p class="project-detail-description"><?= e($project['description']) ?></p>
                <?php if ($technologies): ?>
                    <div class="project-detail-tags" aria-label="Technologies used">
                        <?php foreach ($technologies as $technology): ?>
                            <span class="tech-pill"><?= e($technology) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ($projectUrl): ?>
                    <a class="btn btn-primary mt-4" href="<?= e($projectUrl) ?>" target="_blank" rel="noopener noreferrer">
                        Open project <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                    </a>
                <?php endif; ?>
            </div>
            <img class="project-detail-image" src="<?= e(project_image($project)) ?>" alt="<?= e($project['title']) ?>">
        </div>
    </div>
</section>

<?php require APP_PATH . '/views/site/footer.php'; ?>
