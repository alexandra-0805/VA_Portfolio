<?php
require __DIR__ . '/../app/bootstrap.php';
require_admin();

if (is_post()) {
    verify_csrf();
    $id = (int) ($_POST['id'] ?? 0);

    match ($_POST['action'] ?? '') {
        'publish' => db_query("UPDATE feedback SET status = 'published' WHERE id = ?", [$id]),
        'hide' => db_query("UPDATE feedback SET status = 'hidden' WHERE id = ?", [$id]),
        'delete' => db_query('DELETE FROM feedback WHERE id = ?', [$id]),
        default => null,
    };

    redirect('admin/feedback.php');
}

$adminTitle = 'Feedback';
$publicSummary = get_feedback_summary();
$allSummary = get_feedback_summary(false);
$hiddenCount = $allSummary['total'] - $publicSummary['total'];
$feedbackRows = db_query('SELECT * FROM feedback ORDER BY created_at DESC, id DESC')->fetchAll();

require APP_PATH . '/views/admin/header.php';
?>
<div class="admin-page-header mb-4">
    <div>
        <div class="section-kicker">Reviews</div>
        <h1 class="h2 fw-bold">Feedback &amp; ratings</h1>
        <p class="admin-page-intro mb-0">
            Ratings are calculated automatically from published feedback.
            <?= config('feedback.auto_publish', true)
                ? 'New feedback goes live right away; hide anything you do not want shown.'
                : 'New feedback stays hidden until you publish it.' ?>
        </p>
    </div>
    <a class="btn btn-outline-dark" href="../feedback.php" target="_blank" rel="noopener">
        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
        View public page
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="soft-panel analytics-summary-card h-100">
            <span class="muted">Average rating</span>
            <strong><?= $publicSummary['total'] ? e(format_rating($publicSummary['average'])) : '–' ?><small class="muted fs-6"> / 5</small></strong>
            <?= rating_stars($publicSummary['average']) ?>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="soft-panel analytics-summary-card h-100">
            <span class="muted">Published reviews</span>
            <strong><?= (int) $publicSummary['total'] ?></strong>
            <small class="muted">Shown on your site</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="soft-panel analytics-summary-card h-100">
            <span class="muted">Hidden</span>
            <strong><?= (int) $hiddenCount ?></strong>
            <small class="muted">Not counted in the rating</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="soft-panel analytics-summary-card h-100">
            <span class="muted">Breakdown (published)</span>
            <div class="admin-rating-breakdown">
                <?php foreach ($publicSummary['distribution'] as $stars => $count): ?>
                    <span><?= (int) $stars ?>★ <strong><?= (int) $count ?></strong></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <?php if (!$feedbackRows): ?>
        <div class="col-12"><div class="soft-panel"><p class="muted mb-0">No feedback yet. Share your Feedback page link with clients to collect reviews.</p></div></div>
    <?php endif; ?>

    <?php foreach ($feedbackRows as $row): ?>
        <div class="col-lg-6">
            <article class="soft-panel h-100">
                <div class="d-flex justify-content-between gap-3">
                    <?= rating_stars((int) $row['rating']) ?>
                    <small class="muted text-nowrap"><?= e($row['created_at']) ?></small>
                </div>
                <p class="muted my-2">
                    <strong class="text-body"><?= e($row['name']) ?></strong>
                    <?php if (!empty($row['email'])): ?>
                        · <a href="mailto:<?= e($row['email']) ?>"><?= e($row['email']) ?></a>
                    <?php endif; ?>
                    · <span class="badge text-bg-<?= $row['status'] === 'published' ? 'success' : 'secondary' ?>"><?= e($row['status']) ?></span>
                </p>
                <p><?= nl2br(e($row['comment'])) ?></p>

                <form method="post" class="d-flex gap-2">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                    <?php if ($row['status'] === 'published'): ?>
                        <button class="btn btn-sm btn-outline-dark" name="action" value="hide" type="submit">Hide</button>
                    <?php else: ?>
                        <button class="btn btn-sm btn-outline-dark" name="action" value="publish" type="submit">Publish</button>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-outline-danger" name="action" value="delete" type="submit" data-confirm="Delete this feedback?">Delete</button>
                </form>
            </article>
        </div>
    <?php endforeach; ?>
</div>
<?php require APP_PATH . '/views/admin/footer.php'; ?>
