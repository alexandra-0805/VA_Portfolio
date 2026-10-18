<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$adminTitle = 'Reports';
$status = $_GET['status'] ?? 'all';
$allowedStatuses = ['all', 'unread', 'read'];
if (!in_array($status, $allowedStatuses, true)) {
    $status = 'all';
}
$dateFrom = trim($_GET['date_from'] ?? '');
$dateTo = trim($_GET['date_to'] ?? '');
$filters = [];
$parameters = [];

if ($status !== 'all') {
    $filters[] = 'status = ?';
    $parameters[] = $status;
}

if (preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $dateFrom)) {
    $filters[] = 'created_at >= ?';
    $parameters[] = $dateFrom . ' 00:00:00';
} else {
    $dateFrom = '';
}

if (preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $dateTo)) {
    $filters[] = 'created_at < DATE_ADD(?, INTERVAL 1 DAY)';
    $parameters[] = $dateTo;
} else {
    $dateTo = '';
}

$whereClause = $filters ? ' WHERE ' . implode(' AND ', $filters) : '';
$messageStatement = $pdo->prepare(
    'SELECT id, name, email, subject, status, created_at '
    . 'FROM messages' . $whereClause . ' ORDER BY created_at DESC'
);
$messageStatement->execute($parameters);
$messages = $messageStatement->fetchAll();

$summary = $pdo->query(
    "SELECT COUNT(*) AS total, "
    . "SUM(status = 'unread') AS unread, "
    . "SUM(status = 'read') AS `read` FROM messages"
)->fetch();

require __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header mb-4">
    <div>
        <div class="section-kicker">Insights</div>
        <h1 class="h2 fw-bold">Contact reports</h1>
    </div>
    <button class="btn btn-outline-dark" type="button" onclick="window.print()">
        <i class="bi bi-printer" aria-hidden="true"></i>
        Print report
    </button>
</div>

<div class="row g-3 mb-4">
    <?php foreach ([
        ['label' => 'Total messages', 'value' => $summary['total'] ?? 0, 'icon' => 'bi-envelope'],
        ['label' => 'Unread', 'value' => $summary['unread'] ?? 0, 'icon' => 'bi-envelope-exclamation'],
        ['label' => 'Read', 'value' => $summary['read'] ?? 0, 'icon' => 'bi-envelope-check'],
    ] as $stat): ?>
        <div class="col-md-4">
            <div class="soft-panel dashboard-stat-card h-100">
                <i class="bi <?= e($stat['icon']) ?> dashboard-stat-icon" aria-hidden="true"></i>
                <div class="display-6 fw-bold mt-2"><?= (int) $stat['value'] ?></div>
                <div class="muted"><?= e($stat['label']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<section class="soft-panel mb-4">
    <form class="row g-3 align-items-end" method="get">
        <div class="col-md-3">
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status" name="status">
                <?php foreach ($allowedStatuses as $option): ?>
                    <option value="<?= e($option) ?>" <?= $status === $option ? 'selected' : '' ?>>
                        <?= e(ucfirst($option)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="date-from">From</label>
            <input class="form-control" id="date-from" type="date" name="date_from" value="<?= e($dateFrom) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="date-to">To</label>
            <input class="form-control" id="date-to" type="date" name="date_to" value="<?= e($dateTo) ?>">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Filter</button>
            <a class="btn btn-outline-dark" href="reports.php">Clear</a>
        </div>
    </form>
</section>

<section class="soft-panel">
    <div class="table-responsive">
        <table class="table admin-table align-middle mb-0">
            <thead>
                <tr>
                    <th scope="col">Date</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Subject</th>
                    <th scope="col">Status</th>
                    <th scope="col">View</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$messages): ?>
                    <tr><td colspan="6" class="muted">No messages match these filters.</td></tr>
                <?php endif; ?>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= e($message['created_at']) ?></td>
                        <td><?= e($message['name']) ?></td>
                        <td><a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a></td>
                        <td><?= e($message['subject']) ?></td>
                        <td>
                            <span class="badge text-bg-<?= $message['status'] === 'unread' ? 'warning' : 'success' ?>">
                                <?= e($message['status']) ?>
                            </span>
                        </td>
                        <td><a class="btn btn-sm btn-outline-dark" href="messages.php">Open inbox</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
