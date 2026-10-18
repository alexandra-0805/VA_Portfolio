<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Services | Alexandra Concepcion';
$activePage = 'services';
$services = $pdo->query(
    'SELECT MIN(id) AS id, title, description, icon '
    . 'FROM services GROUP BY title, description, icon '
    . 'ORDER BY title'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <div class="section-kicker mb-3">Services</div>
        <h1 class="section-title">Support that keeps <span>things moving.</span></h1>
        <p class="hero-copy mt-4">
            Flexible help across administration, content, customer care, and
            digital projects.
        </p>
    </div>
</section>

<section>
    <div class="container">
        <div class="service-intro">
            <p class="muted mb-0">
                Each service is shaped around clear communication, dependable
                follow-through, and the tools your workflow already uses.
            </p>
        </div>

        <div class="row g-4">
            <?php foreach ($services as $service): ?>
                <div class="col-md-6 col-lg-4 animate">
                    <article class="service-card">
                        <div class="service-icon" aria-hidden="true">
                            <i class="bi <?= e($service['icon']) ?>"></i>
                        </div>
                        <h2 class="h3"><?= e($service['title']) ?></h2>
                        <p><?= e($service['description']) ?></p>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
