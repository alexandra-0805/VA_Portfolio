<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Projects | Alexandra Concepcion';
$activePage = 'projects';
$projects = $pdo->query(
    'SELECT MIN(id) AS id, title, description, technologies, image, project_url '
    . 'FROM projects GROUP BY title, description, technologies, image, project_url '
    . 'ORDER BY MAX(created_at) DESC'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <div class="section-kicker mb-3">Portfolio</div>
        <h1 class="section-title">Work with <span>purpose.</span></h1>
        <p class="hero-copy mt-4">
            A selection of digital, business, and web projects built through
            collaboration and care.
        </p>
    </div>
</section>

<section>
    <div class="container">
        <div class="row g-4">
            <?php foreach ($projects as $project): ?>
                <?php
                $projectImage = $project['image'] ?:
                    'https://images.unsplash.com/photo-1558655146-9f40138edfeb'
                    . '?auto=format&fit=crop&w=900&q=80';
                $technologies = array_filter(
                    array_map('trim', explode(',', $project['technologies']))
                );
                ?>
                <div class="col-md-6 col-lg-4 animate">
                    <a
                        class="project-card project-card-link"
                        href="project.php?id=<?= (int) $project['id'] ?>"
                        aria-label="View <?= e($project['title']) ?> project"
                    >
                        <img
                            class="project-image"
                            src="<?= e($projectImage) ?>"
                            alt="<?= e($project['title']) ?>"
                            loading="lazy"
                        >
                        <div class="project-body">
                            <h2 class="h3"><?= e($project['title']) ?></h2>
                            <p class="mb-3"><?= e($project['description']) ?></p>

                            <?php if ($technologies): ?>
                                <div class="d-flex flex-wrap gap-2 mb-3" aria-label="Technologies used">
                                    <?php foreach ($technologies as $technology): ?>
                                        <span class="tech-pill">
                                            <?= e($technology) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
