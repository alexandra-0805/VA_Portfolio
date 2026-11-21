<?php
/**
 * Automatic email alerts to the site owner (Gmail) for new visitors,
 * contact-link clicks ("reach me"), contact messages, and feedback.
 * Each alert type can be switched off under 'notify' in the config.
 */

require_once APP_PATH . '/lib/mailer.php';

/** Alert types that can fire often, so they share a daily cap. */
const NOTIFY_ACTIVITY_EVENTS = ['visitors', 'reach'];

function notify_recipient(): string
{
    return config('notify.to') ?: config('mail.contact_to') ?: config('site.email');
}

/**
 * Email the owner about an event. Never throws: a failed alert must not break
 * the page that triggered it (the event is already saved in the database).
 *
 * @param string $event One of: visitors, reach, messages, feedback.
 * @param array{0: string, 1?: string}|null $replyTo
 */
function notify_owner(string $event, string $subject, string $body, ?array $replyTo = null): bool
{
    if (!config('notify.' . $event, true)) {
        return false;
    }

    if (in_array($event, NOTIFY_ACTIVITY_EVENTS, true) && !activity_alert_allowed()) {
        return false;
    }

    $body .= "\n\n--\nSent automatically by your portfolio on " . notify_time() . '.'
        . "\nTurn alerts on or off under 'notify' in your private config file.";

    try {
        send_mail(notify_recipient(), '[Portfolio] ' . $subject, $body, 'Portfolio alerts', $replyTo);
        return true;
    } catch (RuntimeException) {
        return false; // Already logged inside send_mail().
    }
}

/** Full link to a page of the site, for use inside emails. */
function site_link(string $path): string
{
    return rtrim((string) config('app.url'), '/') . '/' . ltrim($path, '/');
}

function notify_time(): string
{
    try {
        $timezone = new DateTimeZone((string) config('notify.timezone', 'Asia/Manila'));
    } catch (Exception) {
        $timezone = null;
    }

    return (new DateTimeImmutable('now', $timezone))->format('M j, Y \a\t g:i A (T)');
}

/** "★★★★☆" for a 1-5 rating. */
function text_stars(int $rating): string
{
    $rating = max(0, min(5, $rating));

    return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
}

/**
 * Counts one visitor/reach alert for today and says whether it is under the
 * daily cap. Protects the Gmail account's daily sending limit.
 */
function activity_alert_allowed(): bool
{
    $cap = (int) config('notify.activity_daily_cap', 50);

    if ($cap <= 0) {
        return true;
    }

    $bucket = hash('sha256', 'notify-activity|' . date('Y-m-d'));

    try {
        db_query(
            'INSERT INTO rate_limits (bucket, attempts, window_start) VALUES (?, 1, NOW()) '
            . 'ON DUPLICATE KEY UPDATE attempts = attempts + 1',
            [$bucket]
        );
        $sentToday = (int) db_query('SELECT attempts FROM rate_limits WHERE bucket = ?', [$bucket])->fetchColumn();
    } catch (PDOException $exception) {
        error_log('Alert cap check failed: ' . $exception->getMessage());
        return true;
    }

    return $sentToday <= $cap;
}
