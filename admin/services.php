<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(true);

    $serviceId = (int) ($_POST['id'] ?? 0);

    if (isset($_POST['delete'])) {
        $statement = $pdo->prepare('DELETE FROM services WHERE id = ?');
        $statement->execute([$serviceId]);
    } else {
        $serviceData = [
            trim($_POST['title'] ?? ''),
            trim($_POST['description'] ?? ''),
            trim($_POST['icon'] ?? ''),
        ];

        if ($serviceId) {
            $statement = $pdo->prepare(
                'UPDATE services SET title = ?, description = ?, icon = ? '
                . 'WHERE id = ?'
            );
            $statement->execute([...$serviceData, $serviceId]);
        } else {
            $statement = $pdo->prepare(
                'INSERT INTO services (title, description, icon) VALUES (?, ?, ?)'
            );
            $statement->execute($serviceData);
        }
    }

    redirect('services.php');
}

$adminTitle = 'Services';
$editService = null;

if (isset($_GET['edit'])) {
    $statement = $pdo->prepare('SELECT * FROM services WHERE id = ?');
    $statement->execute([(int) $_GET['edit']]);
    $editService = $statement->fetch();
}

$services = $pdo->query(
    'SELECT MIN(id) AS id, title, description, icon '
    . 'FROM services GROUP BY title, description, icon '
    . 'ORDER BY title'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header mb-4">
    <div>
        <div class="section-kicker">Portfolio content</div>
        <h1 class="h2 fw-bold">Services</h1>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <section class="soft-panel">
            <h2 class="h4">
                <?= $editService ? 'Edit' : 'Add' ?> service
            </h2>
            <form method="post">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input
                    type="hidden"
                    name="id"
                    value="<?= e($editService['id'] ?? '') ?>"
                >

                <div class="mb-3">
                    <label class="form-label" for="title">Service title</label>
                    <input
                        class="form-control"
                        id="title"
                        name="title"
                        value="<?= e($editService['title'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea
                        class="form-control"
                        id="description"
                        name="description"
                        rows="4"
                        required
                    ><?= e($editService['description'] ?? '') ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="icon">Bootstrap icon class</label>
                    <input
                        class="form-control"
                        id="icon"
                        name="icon"
                        placeholder="bi-stars"
                        value="<?= e($editService['icon'] ?? 'bi-stars') ?>"
                        required
                    >
                </div>

                <button class="btn btn-primary" type="submit">
                    <?= $editService ? 'Update' : 'Add' ?> service
                </button>
            </form>
        </section>
    </div>

    <div class="col-lg-8">
        <div class="table-responsive bg-white rounded-3">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th scope="col">Service</th>
                        <th scope="col">Icon</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$services): ?>
                        <tr>
                            <td colspan="3" class="muted">No services yet.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?= e($service['title']) ?></td>
                            <td><?= e($service['icon']) ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a
                                        href="?edit=<?= (int) $service['id'] ?>"
                                        class="btn btn-sm btn-outline-dark"
                                    >
                                        Edit
                                    </a>
                                    <form method="post">
                                        <input
                                            type="hidden"
                                            name="csrf"
                                            value="<?= e(csrf_token()) ?>"
                                        >
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $service['id'] ?>"
                                        >
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            name="delete"
                                            type="submit"
                                            data-confirm="Delete this service?"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
