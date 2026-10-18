<?php
require_once __DIR__ . '/config/database.php';

$projectId = (int) ($_GET['id'] ?? 0);
$statement = $pdo->prepare('SELECT * FROM projects WHERE id = ? LIMIT 1');
$statement->execute([$projectId]);
$project = $statement->fetch();

if (!$project) {
    http_response_code(404);
    $pageTitle = 'Project not found | Alexandra Concepcion';
    $activePage = 'projects';
    require __DIR__ . '/includes/header.php';
    ?>
    <section class="page-banner">
        <div class="container">
            <div class="section-kicker mb-3">Portfolio</div>
            <h1 class="section-title">Project <span>not found.</span></h1>
            <p class="hero-copy mt-4">
                This project may have been removed or is no longer available.
            </p>
            <a class="btn btn-primary mt-3" href="projects.php">
                Back to projects
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
            </a>
        </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $project['title'] . ' | Alexandra Concepcion';
$activePage = 'projects';
$projectImage = $project['image'] ?: 'https://images.unsplash.com/photo-1558655146-9f40138edfeb?auto=format&fit=crop&w=1200&q=80';
$technologies = array_filter(
    array_map('trim', explode(',', $project['technologies']))
);

require __DIR__ . '/includes/header.php';
?>

<section class="project-detail-hero">
    <div class="container">
        <a class="project-back-link" href="projects.php">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            Back to projects
        </a>
        <div class="project-detail-grid">
            <div>
                <div class="section-kicker mb-3">Selected project</div>
                <h1 class="section-title"><?= e($project['title']) ?></h1>
                <p class="project-detail-description">
                    <?= e($project['description']) ?>
                </p>
                <?php if ($technologies): ?>
                    <div class="project-detail-tags" aria-label="Technologies used">
                        <?php foreach ($technologies as $technology): ?>
                            <span class="tech-pill"><?= e($technology) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ($project['project_url']): ?>
                    <a
                        class="btn btn-primary mt-4"
                        href="<?= e($project['project_url']) ?>"
                        target="_blank"
                        rel="noopener"
                    >
                        Open project
                        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                    </a>
                <?php endif; ?>
            </div>
            <img
                class="project-detail-image"
                src="<?= e($projectImage) ?>"
                alt="<?= e($project['title']) ?>"
            >
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
