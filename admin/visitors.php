<?php
require __DIR__ . '/../app/bootstrap.php';
require APP_PATH . '/lib/activity.php';
require_admin();

$adminTitle = 'Visitors';
$last30 = 29; // today plus the previous 29 days

$visitorStats = [
    ['label' => 'Visitors today', 'value' => count_unique_visitors(), 'icon' => 'bi-person'],
    ['label' => 'Visitors (7 days)', 'value' => count_unique_visitors(6), 'icon' => 'bi-people'],
    ['label' => 'Visitors (30 days)', 'value' => count_unique_visitors($last30), 'icon' => 'bi-people-fill'],
    [
        'label' => 'Reach-outs (30 days)',
        'value' => (int) db_query("SELECT COUNT(*) FROM site_events WHERE type = 'reach' AND " . SINCE_DAYS_SQL, [$last30])->fetchColumn(),
        'icon' => 'bi-hand-index',
    ],
];

$topPages = db_query(
    'SELECT detail, COUNT(*) AS views, COUNT(DISTINCT visitor_hash) AS visitors FROM site_events '
    . "WHERE type = 'visit' AND " . SINCE_DAYS_SQL . ' GROUP BY detail ORDER BY views DESC LIMIT 8',
    [$last30]
)->fetchAll();

$reachByChannel = db_query(
    "SELECT detail, COUNT(*) FROM site_events WHERE type = 'reach' AND " . SINCE_DAYS_SQL . ' GROUP BY detail',
    [$last30]
)->fetchAll(PDO::FETCH_KEY_PAIR);

$recentEvents = db_query('SELECT type, detail, referrer, device, created_at FROM site_events ORDER BY id DESC LIMIT 50')->fetchAll();

/** "/VA_Portfolio/about.php" -> "about.php" */
$pageName = static fn (string $path): string => basename($path) ?: 'index.php';

require APP_PATH . '/views/admin/header.php';
?>
<div class="admin-page-header mb-4">
    <div>
        <div class="section-kicker">Activity</div>
        <h1 class="h2 fw-bold">Visitors &amp; reach-outs</h1>
        <p class="admin-page-intro mb-0">
            Page views and clicks on your email and social profile links. Your own visits while signed in are not counted.
            Gmail alerts go to <strong><?= e(config('notify.to') ?: config('mail.contact_to') ?: config('site.email')) ?></strong>.
        </p>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($visitorStats as $stat): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="soft-panel dashboard-stat-card h-100">
                <i class="bi <?= e($stat['icon']) ?> dashboard-stat-icon" aria-hidden="true"></i>
                <div class="display-6 fw-bold mt-2"><?= (int) $stat['value'] ?></div>
                <div class="muted"><?= e($stat['label']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <section class="soft-panel h-100">
            <h2 class="h5 fw-bold mb-3">Most viewed pages (30 days)</h2>
            <div class="table-responsive">
                <table class="table admin-table align-middle mb-0">
                    <thead><tr><th scope="col">Page</th><th scope="col">Views</th><th scope="col">Visitors</th></tr></thead>
                    <tbody>
                        <?php if (!$topPages): ?>
                            <tr><td colspan="3" class="muted">No visits recorded yet.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($topPages as $page): ?>
                            <tr>
                                <td><?= e($pageName($page['detail'])) ?></td>
                                <td><?= (int) $page['views'] ?></td>
                                <td><?= (int) $page['visitors'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <div class="col-lg-5">
        <section class="soft-panel h-100">
            <h2 class="h5 fw-bold mb-3">Reach-outs by link (30 days)</h2>
            <ul class="list-unstyled mb-0 d-grid gap-2">
                <?php foreach (REACH_CHANNELS as $channel => [$channelName, $icon]): ?>
                    <li class="d-flex justify-content-between">
                        <span><i class="bi <?= e($icon) ?> me-2" aria-hidden="true"></i><?= e($channelName) ?></span>
                        <strong><?= (int) ($reachByChannel[$channel] ?? 0) ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </div>
</div>

<section class="soft-panel">
    <h2 class="h5 fw-bold mb-3">Recent activity</h2>
    <div class="table-responsive">
        <table class="table admin-table align-middle mb-0">
            <thead>
                <tr>
                    <th scope="col">Time</th>
                    <th scope="col">Activity</th>
                    <th scope="col">Device</th>
                    <th scope="col">Came from</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$recentEvents): ?>
                    <tr><td colspan="4" class="muted">No activity yet. Visits appear here once someone opens your site.</td></tr>
                <?php endif; ?>
                <?php foreach ($recentEvents as $event): ?>
                    <tr>
                        <td class="text-nowrap"><?= e($event['created_at']) ?></td>
                        <td>
                            <?php if ($event['type'] === 'reach'): ?>
                                <span class="badge text-bg-warning">Reach-out</span> Clicked <?= e(reach_channel_name($event['detail'])) ?>
                            <?php else: ?>
                                <span class="badge text-bg-light">Visit</span> <?= e($pageName($event['detail'])) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= e($event['device']) ?></td>
                        <td><?= $event['type'] === 'visit' ? e(describe_referrer($event['referrer'])) : '' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require APP_PATH . '/views/admin/footer.php'; ?>
