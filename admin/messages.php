<?php
require __DIR__ . '/../app/bootstrap.php';
require_admin();

if (is_post()) {
    verify_csrf();
    $id = (int) ($_POST['id'] ?? 0);

    match ($_POST['action'] ?? '') {
        'read' => db_query("UPDATE messages SET status = 'read' WHERE id = ?", [$id]),
        'delete' => db_query('DELETE FROM messages WHERE id = ?', [$id]),
        default => null,
    };

    redirect('admin/messages.php');
}

$adminTitle = 'Messages';
$messages = db_query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();

require APP_PATH . '/views/admin/header.php';
?>
<div class="admin-page-header mb-4">
    <div>
        <div class="section-kicker">Inbox</div>
        <h1 class="h2 fw-bold">Contact messages</h1>
    </div>
</div>

<div class="row g-3">
    <?php if (!$messages): ?>
        <div class="col-12"><div class="soft-panel"><p class="muted mb-0">No contact messages yet.</p></div></div>
    <?php endif; ?>

    <?php foreach ($messages as $message): ?>
        <div class="col-lg-6">
            <article class="soft-panel h-100">
                <div class="d-flex justify-content-between gap-3">
                    <strong><?= e($message['subject']) ?></strong>
                    <small class="muted text-nowrap"><?= e($message['created_at']) ?></small>
                </div>
                <p class="muted mb-2">
                    <?= e($message['name']) ?> ·
                    <a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a> ·
                    <span class="badge text-bg-<?= $message['status'] === 'unread' ? 'warning' : 'success' ?>"><?= e($message['status']) ?></span>
                </p>
                <p><?= nl2br(e($message['message'])) ?></p>

                <form method="post" class="d-flex gap-2">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
                    <?php if ($message['status'] === 'unread'): ?>
                        <button class="btn btn-sm btn-outline-dark" name="action" value="read" type="submit">Mark read</button>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-outline-danger" name="action" value="delete" type="submit" data-confirm="Delete this message?">Delete</button>
                </form>
            </article>
        </div>
    <?php endforeach; ?>
</div>
<?php require APP_PATH . '/views/admin/footer.php'; ?>
