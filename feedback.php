<?php
require __DIR__ . '/app/bootstrap.php';
require APP_PATH . '/lib/notify.php';

const FEEDBACK_LIMIT = 3;
const FEEDBACK_WINDOW = 3600;

$pageTitle = 'Feedback | Alexandra Concepcion';
$pageDescription = 'Ratings and feedback from clients and visitors of Alexandra Concepcion, Virtual Assistant.';
$activePage = 'feedback';
$errors = [];
$sent = false;
$limits = ['name' => 100, 'email' => 160, 'comment' => 1000];
$formData = array_fill_keys(array_keys($limits), '') + ['rating' => 0];

if (is_post()) {
    foreach ($limits as $field => $max) {
        $formData[$field] = input($field, $max + 1);
    }
    $formData['rating'] = (int) input('rating', 1);

    if (!csrf_is_valid()) {
        $errors[] = 'Your session expired. Please send the form again.';
    } elseif (input('website') !== '') {
        // Hidden honeypot field: only bots fill it. Pretend success.
        $sent = true;
    } elseif (rate_limited('feedback', FEEDBACK_LIMIT, FEEDBACK_WINDOW)) {
        $errors[] = 'You have shared feedback several times recently. Please try again later.';
    } else {
        if ($formData['name'] === '' || mb_strlen($formData['name']) > $limits['name']) {
            $errors[] = 'Please enter your name (up to 100 characters).';
        }
        if ($formData['email'] !== '' && (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($formData['email']) > $limits['email'])) {
            $errors[] = 'Please enter a valid email address, or leave it empty.';
        }
        if ($formData['rating'] < 1 || $formData['rating'] > 5) {
            $errors[] = 'Please choose a rating from 1 to 5 stars.';
        }
        if (mb_strlen($formData['comment']) < 5 || mb_strlen($formData['comment']) > $limits['comment']) {
            $errors[] = 'Please write a short comment (5 to 1,000 characters).';
        }
    }

    if (!$errors && !$sent) {
        record_attempt('feedback', FEEDBACK_WINDOW);
        $autoPublish = (bool) config('feedback.auto_publish', true);

        db_query(
            'INSERT INTO feedback (name, email, rating, comment, status) VALUES (?, ?, ?, ?, ?)',
            [
                $formData['name'],
                $formData['email'] !== '' ? $formData['email'] : null,
                $formData['rating'],
                $formData['comment'],
                $autoPublish ? 'published' : 'hidden',
            ]
        );

        $summary = get_feedback_summary(false);
        notify_owner(
            'feedback',
            "New {$formData['rating']}-star feedback from {$formData['name']}",
            "You received new feedback on your portfolio.\n\n"
            . 'Rating:  ' . text_stars($formData['rating']) . " ({$formData['rating']}/5 - " . FEEDBACK_RATING_LABELS[$formData['rating']] . ")\n"
            . "Name:    {$formData['name']}\n"
            . 'Email:   ' . ($formData['email'] ?: 'not given') . "\n\n"
            . "{$formData['comment']}\n\n"
            . ($autoPublish ? 'It is already live on your Feedback page.' : 'It is hidden until you publish it.') . "\n"
            . 'Your average is now ' . format_rating($summary['average']) . "/5 from {$summary['total']} reviews.\n"
            . 'Manage feedback: ' . site_link('admin/feedback.php'),
            $formData['email'] !== '' ? [$formData['email'], $formData['name']] : null
        );

        $sent = true;
    }

    if ($sent) {
        $formData = array_fill_keys(array_keys($limits), '') + ['rating' => 0];
    }
}

$feedbackSummary = get_feedback_summary();
$feedbackItems = get_feedback(30);
$successMessage = config('feedback.auto_publish', true)
    ? 'Thank you for your feedback! Your review is now live.'
    : 'Thank you for your feedback! It will appear here once it has been reviewed.';

require APP_PATH . '/views/site/header.php';
?>

<section class="page-banner">
    <div class="container">
        <div class="section-kicker mb-3">Feedback</div>
        <h1 class="section-title">Kind words &amp; <span>honest ratings.</span></h1>
        <p class="hero-copy mt-4">
            Worked with me or just looked around? I'd love to hear how it went.
        </p>
    </div>
</section>

<section>
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <?php require APP_PATH . '/views/partials/rating_summary.php'; ?>
            </div>

            <div class="col-lg-7">
                <div class="soft-panel contact-form-card" id="leave-feedback">
                    <div class="section-kicker mb-3">Leave feedback</div>
                    <h2 class="h3 fw-bold mb-4">How was your experience?</h2>
                    <p class="form-intro">
                        Your name, rating, and comment are shown publicly. Your email stays private.
                    </p>

                    <?= render_alerts($errors, $sent ? $successMessage : '') ?>

                    <form method="post" action="#leave-feedback" data-validate novalidate>
                        <?= csrf_field() ?>
                        <div class="visually-hidden" aria-hidden="true">
                            <label for="website">Leave this field empty</label>
                            <input id="website" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <fieldset class="star-input">
                                    <legend class="form-label">Your rating</legend>
                                    <div class="d-flex align-items-center flex-wrap">
                                        <div class="star-input-options" data-rating-input>
                                            <?php for ($stars = 5; $stars >= 1; $stars--): ?>
                                                <input
                                                    type="radio"
                                                    id="rating-<?= $stars ?>"
                                                    name="rating"
                                                    value="<?= $stars ?>"
                                                    data-label="<?= e(FEEDBACK_RATING_LABELS[$stars]) ?>"
                                                    <?= $formData['rating'] === $stars ? 'checked' : '' ?>
                                                    required>
                                                <label for="rating-<?= $stars ?>" title="<?= e(FEEDBACK_RATING_LABELS[$stars]) ?>">
                                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                                    <span class="visually-hidden"><?= $stars ?> <?= $stars === 1 ? 'star' : 'stars' ?>, <?= e(FEEDBACK_RATING_LABELS[$stars]) ?></span>
                                                </label>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="star-input-text" data-rating-text aria-live="polite">
                                            <?= $formData['rating'] ? e(FEEDBACK_RATING_LABELS[$formData['rating']]) : 'Tap a star' ?>
                                        </span>
                                    </div>
                                    <div class="star-input-error">Please choose a rating.</div>
                                </fieldset>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="name">Name</label>
                                <input
                                    class="form-control"
                                    id="name"
                                    name="name"
                                    value="<?= e($formData['name']) ?>"
                                    autocomplete="name"
                                    required
                                    maxlength="100"
                                >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="email">Email <span class="muted fw-normal">(optional, private)</span></label>
                                <input
                                    class="form-control"
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="<?= e($formData['email']) ?>"
                                    autocomplete="email"
                                    maxlength="160"
                                >
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="comment">Your feedback</label>
                                <textarea
                                    class="form-control"
                                    id="comment"
                                    name="comment"
                                    rows="5"
                                    required
                                    minlength="5"
                                    maxlength="1000"
                                    placeholder="What went well? What could be better?"
                                ><?= e($formData['comment']) ?></textarea>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary" type="submit">
                                    Submit feedback
                                    <i class="bi bi-send" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-alt">
    <div class="container">
        <div class="section-kicker mb-2">Reviews</div>
        <h2 class="section-title mb-5">What people <span>are saying.</span></h2>

        <?php if ($feedbackItems): ?>
            <div class="row g-4">
                <?php foreach ($feedbackItems as $index => $feedbackItem): ?>
                    <div class="col-md-6 col-lg-4 animate" style="--delay: <?= ($index % 3) * 80 ?>ms">
                        <?php require APP_PATH . '/views/partials/feedback_card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty-state">No reviews yet. Yours could be the first!</p>
        <?php endif; ?>
    </div>
</section>

<?php require APP_PATH . '/views/site/footer.php'; ?>
