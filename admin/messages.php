<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(true);

    if (isset($_POST['read'])) {
        $statement = $pdo->prepare(
            "UPDATE messages SET status = 'read' WHERE id = ?"
        );
        $statement->execute([(int) $_POST['read']]);
    }

    if (isset($_POST['delete'])) {
        $statement = $pdo->prepare('DELETE FROM messages WHERE id = ?');
        $statement->execute([(int) $_POST['delete']]);
    }

    redirect('messages.php');
}

$adminTitle = 'Messages';
$messages = $pdo->query(
    'SELECT * FROM messages ORDER BY created_at DESC'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header mb-4">
    <div>
        <div class="section-kicker">Inbox</div>
        <h1 class="h2 fw-bold">Contact messages</h1>
    </div>
</div>

<div class="row g-3">
    <?php if (!$messages): ?>
        <div class="col-12">
            <div class="soft-panel">
                <p class="muted mb-0">No contact messages yet.</p>
            </div>
        </div>
    <?php endif; ?>

    <?php foreach ($messages as $message): ?>
        <div class="col-lg-6">
            <article class="soft-panel h-100">
                <div class="d-flex justify-content-between gap-3">
                    <strong><?= e($message['subject']) ?></strong>
                    <small class="muted">
                        <?= e($message['created_at']) ?>
                    </small>
                </div>
                <p class="muted mb-2">
                    <?= e($message['name']) ?> ·
                    <?= e($message['email']) ?> ·
                    <span
                        class="badge text-bg-<?= $message['status'] === 'unread'
                            ? 'warning'
                            : 'success' ?>"
                    >
                        <?= e($message['status']) ?>
                    </span>
                </p>
                <p><?= nl2br(e($message['message'])) ?></p>

                <form method="post" class="d-flex gap-2">
                    <input
                        type="hidden"
                        name="csrf"
                        value="<?= e(csrf_token()) ?>"
                    >
                    <?php if ($message['status'] === 'unread'): ?>
                        <button
                            class="btn btn-sm btn-outline-dark"
                            name="read"
                            value="<?= (int) $message['id'] ?>"
                            type="submit"
                        >
                            Mark read
                        </button>
                    <?php endif; ?>
                    <button
                        class="btn btn-sm btn-outline-danger"
                        name="delete"
                        value="<?= (int) $message['id'] ?>"
                        type="submit"
                        data-confirm="Delete this message?"
                    >
                        Delete
                    </button>
                </form>
            </article>
        </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
