<?php
require_once __DIR__ . '/../includes/functions.php';

$adminTitle = 'Resume';
$message = '';
$errorMessage = '';
$resumePath = __DIR__ . '/../assets/Alexandra_Concepcion_VA.pdf';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(true);

    if (replace_resume_pdf($_FILES['resume_file'] ?? [])) {
        $message = 'Resume PDF updated successfully.';
    } else {
        $errorMessage = 'Please upload a valid PDF file smaller than 10 MB.';
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header mb-4">
    <div>
        <div class="section-kicker">Professional profile</div>
        <h1 class="h2 fw-bold">Resume</h1>
    </div>
    <a
        class="btn btn-outline-dark"
        href="../resume.php"
        target="_blank"
        rel="noopener"
    >
        View resume
        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <section class="soft-panel">
            <h2 class="h4">Update resume PDF</h2>
            <p class="muted">
                Replace the downloadable PDF used on the public resume page.
                The file must be a PDF smaller than 10 MB.
            </p>

            <?php if ($message): ?>
                <div class="alert alert-success" role="status">
                    <?= e($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger" role="alert">
                    <?= e($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <label class="form-label" for="resume-file">Resume PDF</label>
                <input
                    class="form-control mb-4"
                    id="resume-file"
                    type="file"
                    name="resume_file"
                    accept="application/pdf,.pdf"
                    required
                >
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-upload" aria-hidden="true"></i>
                    Update PDF
                </button>
            </form>
        </section>
    </div>

    <div class="col-lg-5">
        <section class="soft-panel h-100">
            <div class="service-icon" aria-hidden="true">
                <i class="bi bi-file-earmark-pdf"></i>
            </div>
            <h2 class="h5 fw-bold">Current resume file</h2>
            <p class="muted mb-0">
                <?= file_exists($resumePath) ? 'A resume PDF is currently available.' : 'No resume PDF has been uploaded yet.' ?>
            </p>
        </section>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
