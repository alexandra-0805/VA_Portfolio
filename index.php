<?php
require __DIR__ . '/app/bootstrap.php';

$pageTitle = 'Alexandra Concepcion | Virtual Assistant';
$activePage = 'home';
$services = get_services(6);
$skills = get_skills(8);
$projects = get_projects(3);

$highlights = [
    ['icon' => 'bi-kanban', 'title' => 'Organized support', 'text' => 'Clear systems for busy workflows'],
    ['icon' => 'bi-chat-heart', 'title' => 'People-first communication', 'text' => 'Patient, professional client care'],
    ['icon' => 'bi-bullseye', 'title' => 'Detail-oriented', 'text' => 'Accurate data and tidy records'],
    ['icon' => 'bi-lightning-charge', 'title' => 'Digital-ready mindset', 'text' => 'Quick to learn new tools'],
];

$tools = ['Google Workspace', 'Microsoft Office', 'Canva', 'TikTok Shop'];

$processSteps = [
    ['icon' => 'bi-telephone', 'title' => 'Discovery call', 'text' => 'We talk through your goals, recurring tasks, and the tools you already use.'],
    ['icon' => 'bi-list-check', 'title' => 'Clear plan', 'text' => 'I outline priorities, timelines, and how we will stay in touch.'],
    ['icon' => 'bi-gear', 'title' => 'Reliable execution', 'text' => 'I get to work and keep you updated, so nothing slips through the cracks.'],
    ['icon' => 'bi-arrow-repeat', 'title' => 'Review & refine', 'text' => 'We check in regularly and adjust the workflow as your needs grow.'],
];

require APP_PATH . '/views/site/header.php';
?>

<!-- Hero -->
<section class="hero">
    <div class="hero-glow" aria-hidden="true"></div>
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 animate">
                <span class="status-pill mb-4">
                    <span class="status-dot" aria-hidden="true"></span>
                    Open to new clients
                </span>
                <h1 class="display-title mb-4">
                    Reliable virtual support that keeps your business <em>moving.</em>
                </h1>
                <p class="hero-copy mb-4">
                    Hi, I'm <strong>Alexandra Concepcion</strong>, a Virtual Assistant
                    based in Cavite, Philippines. I help busy business owners stay
                    organized, respond to customers promptly, and keep everyday
                    operations running smoothly.
                </p>
                <div class="hero-actions d-flex flex-wrap gap-3">
                    <a class="btn btn-primary btn-lg" href="contact.php">
                        Work with me
                        <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                    </a>
                    <a class="btn btn-ghost btn-lg" href="projects.php">
                        View my work
                    </a>
                </div>

                <div class="hero-tools">
                    <span class="hero-tools-label">Tools I work with</span>
                    <ul class="hero-tools-list">
                        <?php foreach ($tools as $tool): ?>
                            <li><?= e($tool) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="col-lg-5 hero-photo animate" style="--delay: 120ms">
                <div class="hero-portrait-frame">
                    <div class="portrait-placeholder">
                        <img
                            class="portrait-image"
                            src="assets/images/1x1.png"
                            alt="Portrait of Alexandra Concepcion"
                            width="430"
                            height="430"
                            fetchpriority="high">
                    </div>

                    <div class="float-card float-card-top">
                        <span class="float-card-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
                        <span>
                            <strong>Fast replies</strong>
                            <small>Within one business day</small>
                        </span>
                    </div>

                    <div class="float-card float-card-bottom">
                        <span class="float-card-icon float-card-icon-accent"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
                        <span>
                            <strong>Cavite, Philippines</strong>
                            <small>Remote · GMT+8</small>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Highlights -->
<section class="trust-strip" aria-label="Working style highlights">
    <div class="container">
        <ul class="trust-grid">
            <?php foreach ($highlights as $highlight): ?>
                <li>
                    <i class="bi <?= e($highlight['icon']) ?>" aria-hidden="true"></i>
                    <span>
                        <strong><?= e($highlight['title']) ?></strong>
                        <span><?= e($highlight['text']) ?></span>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- About -->
<section id="about">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5 animate">
                <div class="section-kicker mb-3">A little about me</div>
                <h2 class="section-title">
                    Thoughtful support, <span>well handled.</span>
                </h2>
                <a href="about.php" class="text-link mt-4">
                    More about me
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
            <div class="col-lg-7 animate" style="--delay: 100ms">
                <p class="about-intro">
                    I'm a Virtual Assistant and Information Systems student with
                    hands-on experience in customer communication, business
                    operations, data management, and content creation.
                </p>
                <p class="muted">
                    I enjoy turning scattered tasks into organized workflows, so you
                    can spend less time on the busywork and more time on what
                    matters. You can count on me to be detail-oriented, adaptable,
                    and easy to work with.
                </p>
                <div class="row g-3 mt-2">
                    <div class="col-sm-6">
                        <div class="soft-panel feature-panel">
                            <i class="bi bi-check2-circle" aria-hidden="true"></i>
                            <div>
                                <strong>Organized &amp; adaptable</strong>
                                <p class="muted mb-0 mt-1">
                                    A fast learner who handles multiple priorities with care.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="soft-panel feature-panel">
                            <i class="bi bi-people" aria-hidden="true"></i>
                            <div>
                                <strong>Clear communication</strong>
                                <p class="muted mb-0 mt-1">
                                    Patient, timely updates for clients, customers, and teams.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services -->
<section class="section-alt">
    <div class="container">
        <div class="section-heading-row mb-5">
            <div>
                <div class="section-kicker mb-2">How I can help</div>
                <h2 class="section-title">Practical <span>services.</span></h2>
                <p class="section-lead">
                    Dependable day-to-day support, so you can focus on growing your business.
                </p>
            </div>
            <a href="services.php" class="btn btn-ghost">
                All services
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <?php if ($services): ?>
            <div class="row g-4">
                <?php foreach ($services as $index => $service): ?>
                    <div class="col-md-6 col-lg-4 animate" style="--delay: <?= ($index % 3) * 80 ?>ms">
                        <article class="service-card">
                            <div class="service-card-top">
                                <div class="service-icon" aria-hidden="true">
                                    <i class="bi <?= e($service['icon']) ?>"></i>
                                </div>
                                <span class="service-number" aria-hidden="true">
                                    <?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?>
                                </span>
                            </div>
                            <h3><?= e($service['title']) ?></h3>
                            <p><?= e($service['description']) ?></p>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty-state">Services will be listed here soon.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Process -->
<section>
    <div class="container">
        <div class="text-center mx-auto mb-5 section-intro-center">
            <div class="section-kicker mb-2">The process</div>
            <h2 class="section-title">How we'll <span>work together.</span></h2>
            <p class="section-lead mx-auto">
                A simple, transparent process, so you always know what's happening.
            </p>
        </div>

        <ol class="process-grid">
            <?php foreach ($processSteps as $index => $step): ?>
                <li class="process-step animate" style="--delay: <?= $index * 90 ?>ms">
                    <div class="process-step-head">
                        <span class="process-icon" aria-hidden="true">
                            <i class="bi <?= e($step['icon']) ?>"></i>
                        </span>
                        <span class="process-index">Step <?= $index + 1 ?></span>
                    </div>
                    <h3><?= e($step['title']) ?></h3>
                    <p><?= e($step['text']) ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<!-- Skills -->
<section class="section-alt">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5 skills-intro">
                <div class="section-kicker mb-2">Capabilities</div>
                <h2 class="section-title">Skills for the <span>everyday.</span></h2>
                <p class="section-lead">
                    A growing toolkit for dependable administration, customer care,
                    content, and web support.
                </p>
                <a href="resume.php" class="btn btn-ghost mt-2">
                    <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                    View full resume
                </a>
            </div>

            <div class="col-lg-7">
                <?php if ($skills): ?>
                    <div class="row g-3">
                        <?php foreach ($skills as $index => $skill): ?>
                            <?php $proficiency = max(0, min(100, (int) $skill['proficiency'])); ?>
                            <div class="col-md-6 animate" style="--delay: <?= ($index % 2) * 80 ?>ms">
                                <div class="skill-card">
                                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                                        <strong><?= e($skill['name']) ?></strong>
                                        <span class="skill-value"><?= $proficiency ?>%</span>
                                    </div>
                                    <div
                                        class="progress"
                                        role="progressbar"
                                        aria-label="<?= e($skill['name']) ?> proficiency"
                                        aria-valuenow="<?= $proficiency ?>"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                        <div
                                            class="progress-bar"
                                            style="width: <?= $proficiency ?>%"></div>
                                    </div>
                                    <?php if (!empty($skill['category'])): ?>
                                        <small class="skill-category"><?= e($skill['category']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="empty-state">Skills will be listed here soon.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Projects -->
<section>
    <div class="container">
        <div class="section-heading-row mb-5">
            <div>
                <div class="section-kicker mb-2">Selected work</div>
                <h2 class="section-title">A few things <span>I've made.</span></h2>
            </div>
            <a href="projects.php" class="btn btn-ghost">
                See all work
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <?php if ($projects): ?>
            <div class="row g-4">
                <?php foreach ($projects as $index => $project): ?>
                    <?php $technologies = split_list($project['technologies'], 3); ?>
                    <div class="col-md-6 col-lg-4 animate" style="--delay: <?= $index * 90 ?>ms">
                        <a
                            class="project-card project-card-link"
                            href="project.php?id=<?= (int) $project['id'] ?>"
                            aria-label="View <?= e($project['title']) ?> project">
                            <div class="project-media">
                                <img
                                    class="project-image"
                                    src="<?= e(project_image($project)) ?>"
                                    alt=""
                                    loading="lazy">
                            </div>
                            <div class="project-body">
                                <?php if ($technologies): ?>
                                    <div class="project-tags">
                                        <?php foreach ($technologies as $technology): ?>
                                            <span class="tech-pill"><?= e($technology) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <h3><?= e($project['title']) ?></h3>
                                <p><?= e($project['description']) ?></p>
                                <span class="project-cta">
                                    View project
                                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                </span>
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

<!-- Call to action -->
<section class="cta-section">
    <div class="container">
        <div class="cta-card animate">
            <div class="cta-copy">
                <div class="section-kicker contact-kicker mb-2">Let's connect</div>
                <h2 class="section-title">
                    Have a task in mind? <span>Let's make it lighter.</span>
                </h2>
                <p>
                    Tell me what's on your plate. I'll reply within one business day
                    with how I can help.
                </p>
            </div>
            <div class="cta-actions">
                <a href="contact.php" class="btn btn-primary btn-lg">
                    Start a conversation
                    <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                </a>
                <a href="mailto:<?= e(config('site.email')) ?>" class="cta-email">
                    <i class="bi bi-envelope" aria-hidden="true"></i>
                    <?= e(config('site.email')) ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php require APP_PATH . '/views/site/footer.php'; ?>
