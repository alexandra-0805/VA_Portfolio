<?php
/**
 * Visitor activity: page views and clicks on contact links ("reach me").
 * Visitors are identified by a one-way hash; raw IPs and user agents are not stored.
 */

/** Contact links that count as someone reaching out: key => [name, icon]. */
const REACH_CHANNELS = [
    'email' => ['Email', 'bi-envelope'],
    'linkedin' => ['LinkedIn', 'bi-linkedin'],
    'github' => ['GitHub', 'bi-github'],
    'upwork' => ['Upwork', 'bi-briefcase'],
    'quora' => ['Quora', 'bi-quora'],
];

function reach_channel_name(string $channel): string
{
    return REACH_CHANNELS[$channel][0] ?? ucfirst($channel);
}

function user_agent(): string
{
    return mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500);
}

function visitor_hash(): string
{
    return hash('sha256', client_ip() . '|' . user_agent());
}

function is_probably_bot(): bool
{
    $agent = user_agent();

    return $agent === ''
        || preg_match('/bot|crawl|spider|slurp|headless|lighthouse|preview|fetch|monitor|curl|wget|python|java\//i', $agent) === 1;
}

/** "Chrome on Windows" style label from the user agent. */
function describe_device(?string $agent = null): string
{
    $agent ??= user_agent();

    $browser = match (true) {
        str_contains($agent, 'Edg/') => 'Edge',
        str_contains($agent, 'OPR/'), str_contains($agent, 'Opera') => 'Opera',
        str_contains($agent, 'SamsungBrowser') => 'Samsung Internet',
        str_contains($agent, 'Firefox/'), str_contains($agent, 'FxiOS') => 'Firefox',
        str_contains($agent, 'Chrome/'), str_contains($agent, 'CriOS') => 'Chrome',
        str_contains($agent, 'Safari/') => 'Safari',
        default => 'Unknown browser',
    };

    $system = match (true) {
        str_contains($agent, 'Windows') => 'Windows',
        str_contains($agent, 'Android') => 'Android',
        preg_match('/iPhone|iPad|iPod/', $agent) === 1 => 'iPhone / iPad',
        str_contains($agent, 'Mac OS X') => 'Mac',
        str_contains($agent, 'CrOS') => 'Chromebook',
        str_contains($agent, 'Linux') => 'Linux',
        default => 'unknown device',
    };

    return $browser . ' on ' . $system;
}

/** Keep only a same-site path such as "/VA_Portfolio/about.php". */
function clean_page_path(string $path): string
{
    $path = (string) parse_url($path, PHP_URL_PATH);

    return str_starts_with($path, '/') ? mb_substr($path, 0, 255) : '';
}

/** Only http(s) referrers are stored, without query strings. */
function clean_referrer(string $referrer): string
{
    $parts = parse_url($referrer);

    if (!isset($parts['scheme'], $parts['host']) || !in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
        return '';
    }

    return mb_substr(strtolower($parts['host']) . ($parts['path'] ?? ''), 0, 255);
}

/** Human description of where a visitor came from. */
function describe_referrer(string $referrer): string
{
    if ($referrer === '') {
        return 'Direct visit (typed the link, a bookmark, or an app)';
    }

    $host = explode('/', $referrer, 2)[0];
    $ownHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));

    return $host === $ownHost || $host === strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''))
        ? 'Another page of your portfolio'
        : $host;
}

function has_recent_event(string $visitor, string $type, ?string $detail, int $seconds): bool
{
    $sql = 'SELECT 1 FROM site_events WHERE visitor_hash = ? AND type = ? AND created_at > (NOW() - INTERVAL ? SECOND)';
    $params = [$visitor, $type, $seconds];

    if ($detail !== null) {
        $sql .= ' AND detail = ?';
        $params[] = $detail;
    }

    return (bool) db_query($sql . ' LIMIT 1', $params)->fetchColumn();
}

function record_site_event(string $type, string $detail, string $visitor, string $referrer = ''): void
{
    db_query(
        'INSERT INTO site_events (type, detail, referrer, device, visitor_hash) VALUES (?, ?, ?, ?, ?)',
        [$type, $detail, $referrer, describe_device(), $visitor]
    );
}

/** SQL condition for "today plus the previous N days", using the database clock. */
const SINCE_DAYS_SQL = 'created_at >= (CURDATE() - INTERVAL ? DAY)';

/** Unique visitors today (0) or over today plus the previous $daysBack days. */
function count_unique_visitors(int $daysBack = 0): int
{
    return (int) db_query(
        "SELECT COUNT(DISTINCT visitor_hash) FROM site_events WHERE type = 'visit' AND " . SINCE_DAYS_SQL,
        [$daysBack]
    )->fetchColumn();
}
