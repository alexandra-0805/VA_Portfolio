<?php
/**
 * One published review. Expects $feedbackItem (id, name, rating, comment, created_at).
 */
$feedbackRating = (int) $feedbackItem['rating'];
?>
<article class="feedback-card">
    <?= rating_stars($feedbackRating) ?>
    <blockquote><?= nl2br(e($feedbackItem['comment'])) ?></blockquote>
    <footer>
        <span class="feedback-avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($feedbackItem['name'], 0, 1))) ?></span>
        <span>
            <strong><?= e($feedbackItem['name']) ?></strong>
            <small><?= e(date('M j, Y', strtotime($feedbackItem['created_at']))) ?></small>
        </span>
    </footer>
</article>
