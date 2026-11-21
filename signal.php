<?php
/**
 * Background endpoint called by assets/js/main.js on the public pages.
 *   type=visit  a page was viewed      -> email on a visitor's first view of the day
 *   type=reach  a contact link clicked -> email once per link per visitor per day
 * Always answers "204 No Content"; visitors never wait on it or see errors.
 */

require __DIR__ . '/app/bootstrap.php';
require APP_PATH . '/lib/activity.php';
require APP_PATH . '/lib/notify.php';

const SIGNAL_LIMIT = 200;
const SIGNAL_WINDOW = 3600;
const ALERT_REPEAT_SECONDS = 86400;

http_response_code(204);

// Admin browsing and bots are not visitors.
if (!is_post() || !csrf_is_valid() || is_admin() || is_probably_bot()) {
    exit;
}

// Release the session lock so sending an email never slows the visitor's next page.
session_write_close();
ignore_user_abort(true);

try {
    if (rate_limited('signal', SIGNAL_LIMIT, SIGNAL_WINDOW)) {
        exit;
    }
    record_attempt('signal', SIGNAL_WINDOW);

    $type = input('type', 10);
    $page = clean_page_path(input('page', 255));
    $title = input('title', 120) ?: ($page ?: 'your portfolio');
    $visitor = visitor_hash();

    if ($type === 'visit' && $page !== '') {
        $referrer = clean_referrer(input('referrer', 1000));
        $isNewToday = !has_recent_event($visitor, 'visit', null, ALERT_REPEAT_SECONDS);
        record_site_event('visit', $page, $visitor, $referrer);

        if ($isNewToday) {
            $visitorsToday = count_unique_visitors();
            notify_owner(
                'visitors',
                'New visitor on ' . $title,
                "Someone just opened your portfolio.\n\n"
                . "Page:        {$title}\n"
                . 'Came from:   ' . describe_referrer($referrer) . "\n"
                . 'Device:      ' . describe_device() . "\n"
                . "Visitors today: {$visitorsToday}\n\n"
                . 'See all visitor activity: ' . site_link('admin/visitors.php')
            );
        }
    } elseif ($type === 'reach') {
        $channel = input('channel', 20);

        if (!isset(REACH_CHANNELS[$channel])) {
            exit;
        }

        $isNewToday = !has_recent_event($visitor, 'reach', $channel, ALERT_REPEAT_SECONDS);
        record_site_event('reach', $channel, $visitor);

        if ($isNewToday) {
            notify_owner(
                'reach',
                'Someone is reaching out via ' . reach_channel_name($channel),
                'A visitor just clicked your ' . reach_channel_name($channel) . " link on your portfolio.\n\n"
                . "Clicked on:  {$title}\n"
                . 'Device:      ' . describe_device() . "\n\n"
                . 'Keep an eye on your ' . reach_channel_name($channel) . " messages.\n"
                . 'See all visitor activity: ' . site_link('admin/visitors.php')
            );
        }
    }
} catch (PDOException $exception) {
    // Most likely the feedback/activity migration has not been run yet.
    error_log('Visitor activity error: ' . $exception->getMessage());
}
