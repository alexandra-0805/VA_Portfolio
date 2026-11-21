<?php
require __DIR__ . '/../app/bootstrap.php';
require APP_PATH . '/lib/uploads.php';
require_admin();

$adminTitle = 'Resume';
$errors = [];

if (is_post()) {
    verify_csrf();

    if (replace_resume_pdf($_FILES['resume_file'] ?? [])) {
        flash('success', 'Resume PDF updated successfully.');
        redirect('admin/resume.php');
    }

    $errors[] = 'Please upload a valid PDF file smaller than 10 MB.';
}

$resumeFile = ROOT_PATH . '/' . config('site.resume_pdf');

require APP_PATH . '/views/admin/header.php';
?>
<div class="admin-page-header mb-4">
    <div>
        <div class="section-kicker">Professional profile</div>
        <h1 class="h2 fw-bold">Resume</h1>
    </div>
    <a class="btn btn-outline-dark" href="<?= e(url('resume.php')) ?>" target="_blank" rel="noopener">
        View resume <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <section class="soft-panel">
            <h2 class="h4">Update resume PDF</h2>
            <p class="muted">Replace the downloadable PDF used on the public resume page. The file must be a PDF smaller than 10 MB.</p>
            <?= render_alerts($errors) ?>

            <form method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <label class="form-label" for="resume-file">Resume PDF</label>
                <input class="form-control mb-4" id="resume-file" type="file" name="resume_file" accept="application/pdf,.pdf" required>
                <button class="btn btn-primary" type="submit"><i class="bi bi-upload" aria-hidden="true"></i> Update PDF</button>
            </form>
        </section>
    </div>

    <div class="col-lg-5">
        <section class="soft-panel h-100">
            <div class="service-icon" aria-hidden="true"><i class="bi bi-file-earmark-pdf"></i></div>
            <h2 class="h5 fw-bold">Current resume file</h2>
            <?php if (is_file($resumeFile)): ?>
                <p class="muted">Last updated <?= e(date('F j, Y g:i A', filemtime($resumeFile))) ?> · <?= e(number_format(filesize($resumeFile) / 1024)) ?> KB</p>
                <a class="text-link" href="<?= e(asset(config('site.resume_pdf'))) ?>" target="_blank" rel="noopener">Open current PDF <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            <?php else: ?>
                <p class="muted mb-0">No resume PDF has been uploaded yet.</p>
            <?php endif; ?>
        </section>
    </div>
</div>
<?php require APP_PATH . '/views/admin/footer.php'; ?>
