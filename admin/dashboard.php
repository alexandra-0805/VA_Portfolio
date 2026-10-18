<?php
require_once __DIR__ . '/../config/database.php';

$adminTitle = 'Dashboard';
$counts = [];
$countQueries = [
    'projects' => 'SELECT COUNT(*) FROM ('
        . 'SELECT title, description, technologies, image, project_url '
        . 'FROM projects GROUP BY title, description, technologies, image, project_url'
        . ') AS unique_projects',
    'skills' => 'SELECT COUNT(*) FROM ('
        . 'SELECT name, category, proficiency '
        . 'FROM skills GROUP BY name, category, proficiency'
        . ') AS unique_skills',
    'services' => 'SELECT COUNT(*) FROM ('
        . 'SELECT title, description, icon '
        . 'FROM services GROUP BY title, description, icon'
        . ') AS unique_services',
    'messages' => 'SELECT COUNT(*) FROM messages',
    'certificates' => 'SELECT COUNT(*) FROM ('
        . 'SELECT title, issuer, date, image, description '
        . 'FROM certificates GROUP BY title, issuer, date, image, description'
        . ') AS unique_certificates',
];

foreach ($countQueries as $key => $query) {
    $counts[$key] = (int) $pdo->query($query)->fetchColumn();
}

$unreadMessages = (int) $pdo
    ->query("SELECT COUNT(*) FROM messages WHERE status = 'unread'")
    ->fetchColumn();

$analytics = [
    'total_content' => array_sum($counts) - $counts['messages'],
    'average_skill' => (float) ($pdo
        ->query('SELECT COALESCE(AVG(proficiency), 0) FROM skills')
        ->fetchColumn()),
    'read_messages' => $counts['messages'] - $unreadMessages,
];
$analytics['response_rate'] = $counts['messages'] > 0
    ? round(($analytics['read_messages'] / $counts['messages']) * 100)
    : 0;

$messageTrend = [];
$trendStart = new DateTimeImmutable('first day of -5 months 00:00:00');
$trendEnd = new DateTimeImmutable('first day of next month 00:00:00');
$trendStatement = $pdo->prepare(
    'SELECT DATE_FORMAT(created_at, "%Y-%m") AS month_key, COUNT(*) AS total '
    . 'FROM messages WHERE created_at >= ? AND created_at < ? '
    . 'GROUP BY month_key ORDER BY month_key'
);
$trendStatement->execute([
    $trendStart->format('Y-m-d H:i:s'),
    $trendEnd->format('Y-m-d H:i:s'),
]);
$trendRows = $trendStatement->fetchAll();
$trendLookup = [];
foreach ($trendRows as $trendRow) {
    $trendLookup[$trendRow['month_key']] = (int) $trendRow['total'];
}
for ($month = $trendStart; $month < $trendEnd; $month = $month->modify('+1 month')) {
    $monthKey = $month->format('Y-m');
    $messageTrend[] = [
        'label' => $month->format('M'),
        'total' => $trendLookup[$monthKey] ?? 0,
    ];
}
$maxTrend = max(array_column($messageTrend, 'total')) ?: 1;
$trendPoints = [];
foreach ($messageTrend as $index => $month) {
    $x = 48 + ($index * 100);
    $y = 180 - (($month['total'] / $maxTrend) * 135);
    $trendPoints[] = round($x) . ',' . round($y);
}
$trendPolyline = implode(' ', $trendPoints);

$contentMix = [
    ['label' => 'Projects', 'value' => $counts['projects'], 'icon' => 'bi-grid'],
    ['label' => 'Services', 'value' => $counts['services'], 'icon' => 'bi-stars'],
    ['label' => 'Skills', 'value' => $counts['skills'], 'icon' => 'bi-bar-chart'],
    ['label' => 'Certificates', 'value' => $counts['certificates'], 'icon' => 'bi-award'],
];
$maxContentMix = max(array_column($contentMix, 'value')) ?: 1;

$dashboardCards = [
    ['key' => 'projects', 'label' => 'Projects', 'icon' => 'bi-grid', 'url' => 'projects.php'],
    ['key' => 'skills', 'label' => 'Skills', 'icon' => 'bi-bar-chart', 'url' => 'skills.php'],
    ['key' => 'services', 'label' => 'Services', 'icon' => 'bi-stars', 'url' => 'services.php'],
    ['key' => 'messages', 'label' => 'Messages', 'icon' => 'bi-envelope', 'url' => 'messages.php'],
    ['key' => 'reports', 'label' => 'Reports', 'icon' => 'bi-bar-chart-line', 'url' => 'reports.php', 'value' => 'View'],
    ['key' => 'certificates', 'label' => 'Certificates', 'icon' => 'bi-award', 'url' => 'certificates.php'],
    ['key' => 'resume', 'label' => 'Resume', 'icon' => 'bi-file-earmark-person', 'url' => 'resume.php', 'value' => 'PDF'],
];

require __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header mb-5">
    <div>
        <div class="section-kicker">Control center</div>
        <h1 class="section-title">
            Good day, <span><?= e($_SESSION['admin_username']) ?>.</span>
        </h1>
        <p class="admin-page-intro">
            Keep your portfolio content, messages, and professional profile up to date.
        </p>
    </div>
    <div class="admin-page-header-actions">
        <a class="btn btn-primary" href="../index.php" target="_blank" rel="noopener">
            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
            View live site
        </a>
        <a class="btn btn-outline-dark" href="reports.php">
            <i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i>
            View reports
        </a>
    </div>
</div>

<div class="row g-4 mb-5">
    <?php foreach ($dashboardCards as $card): ?>
        <div class="col-md-6 col-lg-3">
            <a
                class="dashboard-stat-link"
                href="<?= e($card['url']) ?>"
                aria-label="Open <?= e($card['label']) ?>"
            >
                <div class="soft-panel dashboard-stat-card h-100">
                    <div class="dashboard-stat-card-top">
                        <i
                            class="bi <?= e($card['icon']) ?> dashboard-stat-icon"
                            aria-hidden="true"
                        ></i>
                        <i class="bi bi-arrow-up-right dashboard-stat-arrow" aria-hidden="true"></i>
                    </div>
                    <div class="display-6 fw-bold mt-3">
                        <?= e($card['value'] ?? $counts[$card['key']]) ?>
                    </div>
                    <div class="dashboard-stat-label">
                        <?= e($card['label']) ?>
                        <?php if ($card['key'] === 'messages' && $unreadMessages): ?>
                            <span class="dashboard-stat-meta"><?= $unreadMessages ?> unread</span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<section class="dashboard-analytics mb-5" aria-labelledby="analytics-heading">
    <div class="admin-page-header mb-4">
        <div>
            <div class="section-kicker">Overview</div>
            <h2 class="h3 fw-bold mb-1" id="analytics-heading">Portfolio analytics</h2>
            <p class="admin-page-intro mb-0">
                A quick view of your content coverage and contact activity.
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="soft-panel analytics-summary-card h-100">
                <span class="muted">Published content</span>
                <strong><?= (int) $analytics['total_content'] ?></strong>
                <small class="muted">Projects, services, skills & certificates</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="soft-panel analytics-summary-card h-100">
                <span class="muted">Average skill level</span>
                <strong><?= (int) round($analytics['average_skill']) ?>%</strong>
                <small class="muted">Across <?= (int) $counts['skills'] ?> listed skills</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="soft-panel analytics-summary-card h-100">
                <span class="muted">Messages received</span>
                <strong><?= (int) $counts['messages'] ?></strong>
                <small class="muted"><?= (int) $unreadMessages ?> currently unread</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="soft-panel analytics-summary-card h-100">
                <span class="muted">Messages reviewed</span>
                <strong><?= (int) $analytics['response_rate'] ?>%</strong>
                <small class="muted">Marked as read in the inbox</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <section class="soft-panel analytics-panel h-100" aria-labelledby="message-activity-heading">
                <div class="analytics-panel-heading">
                    <div>
                        <h3 class="h5 fw-bold mb-1" id="message-activity-heading">Message activity</h3>
                        <p class="muted mb-0">Contact form submissions over the last six months.</p>
                    </div>
                    <i class="bi bi-graph-up-arrow analytics-panel-icon" aria-hidden="true"></i>
                </div>
                <div class="analytics-chart-legend">
                    <span><i aria-hidden="true"></i> Messages received</span>
                    <span class="analytics-chart-period">Last 6 months</span>
                </div>
                <div class="analytics-chart-wrap">
                    <svg
                        class="analytics-line-chart"
                        viewBox="0 0 600 230"
                        role="img"
                        aria-labelledby="message-chart-title message-chart-description"
                    >
                        <title id="message-chart-title">Contact messages by month</title>
                        <desc id="message-chart-description">
                            <?= e(implode(', ', array_map(
                                static fn (array $month): string => $month['label'] . ': ' . $month['total'],
                                $messageTrend
                            ))) ?>
                        </desc>
                        <line class="analytics-axis" x1="40" y1="180" x2="560" y2="180"></line>
                        <line class="analytics-grid-line" x1="40" y1="112" x2="560" y2="112"></line>
                        <line class="analytics-grid-line" x1="40" y1="45" x2="560" y2="45"></line>
                        <polyline
                            class="analytics-line"
                            points="<?= e($trendPolyline) ?>"
                            fill="none"
                            stroke="currentColor"
                        ></polyline>
                        <?php foreach ($messageTrend as $index => $month): ?>
                            <?php
                            $x = 48 + ($index * 100);
                            $y = 180 - (($month['total'] / $maxTrend) * 135);
                            ?>
                            <circle class="analytics-point" cx="<?= $x ?>" cy="<?= round($y) ?>" r="5">
                                <title><?= e($month['label'] . ': ' . $month['total'] . ' messages') ?></title>
                            </circle>
                            <text class="analytics-chart-label" x="<?= $x ?>" y="210" text-anchor="middle">
                                <?= e($month['label']) ?>
                            </text>
                            <text class="analytics-chart-value" x="<?= $x ?>" y="<?= max(25, round($y - 12)) ?>" text-anchor="middle">
                                <?= (int) $month['total'] ?>
                            </text>
                        <?php endforeach; ?>
                    </svg>
                </div>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="soft-panel analytics-panel h-100" aria-labelledby="content-mix-heading">
                <div class="analytics-panel-heading">
                    <div>
                        <h3 class="h5 fw-bold mb-1" id="content-mix-heading">Content mix</h3>
                        <p class="muted mb-0">Your current portfolio coverage.</p>
                    </div>
                    <i class="bi bi-pie-chart analytics-panel-icon" aria-hidden="true"></i>
                </div>
                <div class="analytics-content-chart" role="img" aria-label="Portfolio content counts">
                    <?php foreach ($contentMix as $item): ?>
                        <div class="analytics-content-bar">
                            <div class="analytics-content-bar-label">
                                <span>
                                    <i class="bi <?= e($item['icon']) ?> me-2" aria-hidden="true"></i>
                                    <?= e($item['label']) ?>
                                </span>
                                <strong><?= (int) $item['value'] ?></strong>
                            </div>
                            <div class="analytics-mix-track">
                                <div
                                    class="analytics-mix-fill"
                                    style="width: <?= (int) round(($item['value'] / $maxContentMix) * 100) ?>%"
                                ></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
