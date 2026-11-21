<?php
/**
 * Automatic rating summary card. Expects $feedbackSummary from get_feedback_summary().
 */
?>
<div class="soft-panel rating-summary">
    <div class="section-kicker mb-3">Overall rating</div>
    <?php if ($feedbackSummary['total'] > 0): ?>
        <div class="rating-summary-score">
            <span class="rating-average"><?= e(format_rating($feedbackSummary['average'])) ?></span>
            <div>
                <?= rating_stars($feedbackSummary['average']) ?>
                <small class="d-block muted mt-1">
                    Based on <?= (int) $feedbackSummary['total'] ?>
                    <?= $feedbackSummary['total'] === 1 ? 'review' : 'reviews' ?>
                </small>
            </div>
        </div>

        <ul class="rating-bars" aria-label="Rating breakdown">
            <?php foreach ($feedbackSummary['distribution'] as $stars => $count): ?>
                <?php $percent = (int) round(($count / $feedbackSummary['total']) * 100); ?>
                <li class="rating-bar">
                    <span><?= (int) $stars ?> <i class="bi bi-star-fill" aria-hidden="true"></i></span>
                    <span class="rating-bar-track" aria-hidden="true">
                        <span class="rating-bar-fill" style="width: <?= $percent ?>%"></span>
                    </span>
                    <span>
                        <?= (int) $count ?>
                        <span class="visually-hidden"><?= $count === 1 ? 'review' : 'reviews' ?> with <?= (int) $stars ?> stars</span>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="rating-summary-score">
            <span class="rating-average">–</span>
            <div>
                <?= rating_stars(0) ?>
                <small class="d-block muted mt-1">No ratings yet</small>
            </div>
        </div>
    <?php endif; ?>
    <p class="rating-summary-note">
        <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
        Updates automatically with every new review.
    </p>
</div>
