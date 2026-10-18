<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Alexandra Concepcion | Virtual Assistant';
$activePage = 'home';
$services = $pdo->query(
    'SELECT MIN(id) AS id, title, description, icon '
        . 'FROM services GROUP BY title, description, icon '
        . 'ORDER BY MIN(id) LIMIT 6'
)->fetchAll();
$skills = $pdo->query(
    'SELECT MIN(id) AS id, name, category, proficiency '
        . 'FROM skills GROUP BY name, category, proficiency '
        . 'ORDER BY proficiency DESC LIMIT 8'
)->fetchAll();
$projects = $pdo->query(
    'SELECT MIN(id) AS id, title, description, technologies, image, project_url '
        . 'FROM projects GROUP BY title, description, technologies, image, project_url '
        . 'ORDER BY MAX(created_at) DESC LIMIT 3'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 animate">
                <div class="eyebrow mb-3">
                    Virtual Assistant · Digital Support · IT Support · Data Entry
                </div>
                <h1 class="display-title mb-4">
                    Hi, It's me <em>Alexandra</em> Concepcion.
                </h1>
                <p class="hero-copy mb-4">
                    I help businesses stay organized, communicate clearly, and
                    move digital projects forward with calm, reliable support.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a class="btn btn-primary btn-lg" href="contact.php">
                        Hire me
                        <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                    </a>
                    <a class="btn btn-outline-dark btn-lg" href="projects.php">
                        View my work
                    </a>
                </div>
            </div>

            <div class="col-lg-5 hero-photo animate">
                <div class="orbit" aria-hidden="true"></div>
                <div class="portrait-placeholder">
                    <img
                        class="portrait-image"
                        src="assets/images/1x1.png"
                        alt="Portrait of Alexandra Concepcion">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="trust-strip">
    <div class="container">
        <div class="trust-grid">
            <div>
                <strong>Organized support</strong>
                <span>Clear systems for busy workflows</span>
            </div>
            <div>
                <strong>People-first communication</strong>
                <span>Patient, professional client care</span>
            </div>
            <div>
                <strong>Digital-ready mindset</strong>
                <span>Comfortable learning new tools</span>
            </div>
        </div>
    </div>
</section>

<section id="about">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5 animate">
                <div class="section-kicker mb-3">A little about me</div>
                <h2 class="section-title">
                    Thoughtful support, <span>well handled.</span>
                </h2>
            </div>
            <div class="col-lg-7 animate">
                <p class="fs-5 muted">
                    Alexandra Concepcion is a Virtual Assistant with experience in customer communication, 
                    business operations, digital projects, and content creation. 
                    She enjoys turning scattered tasks into organized workflows, 
                    solving problems, and helping businesses stay efficient. 
                    With a detail-oriented and adaptable approach, 
                    Alexandra is committed to providing reliable support and 
                    creating more space for meaningful work.

                </p>
                <div class="row g-3 mt-3">
                    <div class="col-sm-6">
                        <div class="soft-panel">
                            <strong>Organized & adaptable</strong>
                            <p class="muted mb-0 mt-2">
                                A fast learner who can manage multiple priorities with care.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="soft-panel">
                            <strong>People-first communication</strong>
                            <p class="muted mb-0 mt-2">
                                Clear, patient support for clients, customers, and collaborators.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white">
    <div class="container">
        <div class="section-heading-row mb-4">
            <div>
                <div class="section-kicker mb-2">How I can help</div>
                <h2 class="section-title">Practical <span>services.</span></h2>
            </div>
            <a href="services.php" class="btn btn-outline-dark">
                All services
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($services as $service): ?>
                <div class="col-md-6 col-lg-4 animate">
                    <article class="service-card">
                        <div class="service-icon" aria-hidden="true">
                            <i class="bi <?= e($service['icon']) ?>"></i>
                        </div>
                        <h3><?= e($service['title']) ?></h3>
                        <p><?= e($service['description']) ?></p>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="section-kicker mb-2">Capabilities</div>
                <h2 class="section-title">Tools for the <span>everyday.</span></h2>
                <p class="muted mt-3">
                    A growing toolkit for dependable administration, content, and
                    web support.
                </p>
            </div>

            <div class="col-lg-7">
                <div class="row g-3">
                    <?php foreach ($skills as $skill): ?>
                        <div class="col-md-6 animate">
                            <div class="skill-card">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong><?= e($skill['name']) ?></strong>
                                    <small class="muted">
                                        <?= (int) $skill['proficiency'] ?>%
                                    </small>
                                </div>
                                <div
                                    class="progress"
                                    role="progressbar"
                                    aria-label="<?= e($skill['name']) ?> proficiency"
                                    aria-valuenow="<?= (int) $skill['proficiency'] ?>"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                    <div
                                        class="progress-bar"
                                        style="width: <?= (int) $skill['proficiency'] ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white">
    <div class="container">
        <div class="section-heading-row mb-4">
            <div>
                <div class="section-kicker mb-2">Selected work</div>
                <h2 class="section-title">A few things <span>I've made.</span></h2>
            </div>
            <a href="projects.php" class="btn btn-outline-dark">
                See all work
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($projects as $project): ?>
                <?php
                $projectImage = $project['image'] ?:
                    'https://images.unsplash.com/photo-1558655146-9f40138edfeb'
                    . '?auto=format&fit=crop&w=900&q=80';
                ?>
                <div class="col-md-4 animate">
                    <a
                        class="project-card project-card-link"
                        href="project.php?id=<?= (int) $project['id'] ?>"
                        aria-label="View <?= e($project['title']) ?> project">
                        <img
                            class="project-image"
                            src="<?= e($projectImage) ?>"
                            alt="<?= e($project['title']) ?>"
                            loading="lazy">
                        <div class="project-body">
                            <h3><?= e($project['title']) ?></h3>
                            <p class="mb-3"><?= e($project['description']) ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-8">
                <div class="section-kicker contact-kicker mb-2">Let's connect</div>
                <h2 class="section-title">
                    Have a task in mind? <span>Let's make it lighter.</span>
                </h2>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="contact.php" class="btn btn-primary btn-lg">
                    Start a conversation
                    <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>