<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'About Alexandra | Portfolio';
$activePage = 'about';
$certificates = $pdo->query(
    'SELECT MIN(id) AS id, title, issuer, date, image, url, description '
        . 'FROM certificates GROUP BY title, issuer, date, image, url, description '
        . 'ORDER BY MAX(created_at) DESC'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <div class="section-kicker mb-3">About me</div>
        <h1 class="section-title">
            Reliable support with a <span>human touch.</span>
        </h1>
    </div>
</section>

<section>
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <div
                    class="portrait-placeholder portrait-about mx-auto">
                    <img
                        class="portrait-image"
                        src="assets/images/1x1.png"
                        alt="Portrait of Alexandra Concepcion">
                </div>
            </div>

            <div class="col-lg-7 about-copy">
                <p class="about-lead">
                    Alexandra Concepcion has knowledge in Virtual Assistance, Data Entry, Customer Service,
                    IT Service Management, and Basic Web Development in Cavite, Philippines.
                </p>
                <p class="muted">
                    She's bring together customer communication, business operations,
                    digital content, and basic web development. I am detail-oriented,
                    adaptable, organized, and comfortable learning new tools quickly.
                </p>
                <p class="muted">
                    Whether the task is answering inquiries, organizing files,
                    researching a topic, planning content, or polishing a web page,
                    I approach it with patience, curiosity, and attention to detail.
                </p>

                <div class="row g-3 mt-3 about-highlights">
                    <?php foreach (
                        [
                            'Problem solver',
                            'Clear communicator',
                            'Fast learner',
                            'Detail-oriented',
                        ] as $highlight
                    ): ?>
                        <div class="col-sm-6">
                            <div class="soft-panel">
                                <strong>
                                    <i
                                        class="bi bi-check2-circle text-success me-2"
                                        aria-hidden="true"></i>
                                    <?= e($highlight) ?>
                                </strong>
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
        <div class="section-kicker mb-2">Learning milestones</div>
        <h2 class="section-title mb-4">
            Certificates & <span>recognition.</span>
        </h2>

        <?php if ($certificates): ?>
            <div class="row g-3">
                <?php foreach ($certificates as $certificate): ?>
                    <div class="col-md-6 col-lg-4">
                        <article
                            class="certificate-card p-4 h-100"
                            role="button"
                            tabindex="0"
                            data-certificate-image="<?= e($certificate['image']) ?>"
                            data-certificate-title="<?= e($certificate['title']) ?>"
                            aria-label="View <?= e($certificate['title']) ?> certificate">
                            <i
                                class="bi bi-award fs-3 text-danger"
                                aria-hidden="true"></i>
                            <h3 class="h5 fw-bold mt-3">
                                <?= e($certificate['title']) ?>
                            </h3>
                            <p class="muted mb-0">
                                <?= e($certificate['issuer']) ?>
                                <span aria-hidden="true">&middot;</span>
                                <?= e($certificate['date']) ?>
                            </p>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="muted mb-0">Learning milestones will appear here soon.</p>
        <?php endif; ?>
    </div>
</section>

<div
    class="certificate-viewer"
    id="certificateViewer"
    role="dialog"
    aria-modal="true"
    aria-labelledby="certificateViewerTitle"
    hidden>
    <div class="certificate-viewer-backdrop" data-certificate-close></div>
    <div class="certificate-viewer-content">
        <div class="certificate-viewer-header">
            <h2 class="h5 mb-0" id="certificateViewerTitle">Certificate preview</h2>
            <button
                class="certificate-viewer-close"
                type="button"
                aria-label="Close certificate preview"
                title="Close preview"
                data-certificate-close>
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>
        <img id="certificateViewerImage" src="" alt="">
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>