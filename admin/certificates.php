<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(true);

    $certificateId = (int) ($_POST['id'] ?? 0);

    if (isset($_POST['delete'])) {
        $statement = $pdo->prepare('DELETE FROM certificates WHERE id = ?');
        $statement->execute([$certificateId]);
        redirect('certificates.php');
    }

    $title = trim($_POST['title'] ?? '');
    $issuer = trim($_POST['issuer'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $uploadedImage = upload_image(
        $_FILES['image_file'] ?? [],
        'certificates'
    );

    if ($uploadedImage) {
        $image = $uploadedImage;
    }

    if ($certificateId) {
        $statement = $pdo->prepare(
            'UPDATE certificates SET title = ?, issuer = ?, date = ?, '
            . 'image = ?, url = ?, description = ? WHERE id = ?'
        );
        $statement->execute([
            $title,
            $issuer,
            $date,
            $image,
            $url,
            $description,
            $certificateId,
        ]);
    } else {
        $statement = $pdo->prepare(
            'INSERT INTO certificates '
            . '(title, issuer, date, image, url, description) '
            . 'VALUES (?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $title,
            $issuer,
            $date,
            $image,
            $url,
            $description,
        ]);
    }

    redirect('certificates.php');
}

$adminTitle = 'Certificates';
$editCertificate = null;

if (isset($_GET['edit'])) {
    $statement = $pdo->prepare(
        'SELECT * FROM certificates WHERE id = ?'
    );
    $statement->execute([(int) $_GET['edit']]);
    $editCertificate = $statement->fetch();
}

$certificates = $pdo->query(
    'SELECT MIN(id) AS id, title, issuer, date, image, url, description '
    . 'FROM certificates GROUP BY title, issuer, date, image, url, description '
    . 'ORDER BY MAX(created_at) DESC'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header mb-4">
    <div>
        <div class="section-kicker">Portfolio content</div>
        <h1 class="h2 fw-bold">Certificates</h1>
    </div>
    <a class="btn btn-primary" href="certificates.php#certificate-form">
        Add certificate
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-5" id="certificate-form">
        <section class="soft-panel">
            <h2 class="h4">
                <?= $editCertificate ? 'Edit' : 'Add' ?> certificate
            </h2>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input
                    type="hidden"
                    name="id"
                    value="<?= e($editCertificate['id'] ?? '') ?>"
                >

                <div class="mb-3">
                    <label class="form-label" for="title">Certificate title</label>
                    <input
                        class="form-control"
                        id="title"
                        name="title"
                        value="<?= e($editCertificate['title'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-7">
                        <label class="form-label" for="issuer">Issuer</label>
                        <input
                            class="form-control"
                            id="issuer"
                            name="issuer"
                            value="<?= e($editCertificate['issuer'] ?? '') ?>"
                        >
                    </div>
                    <div class="col-sm-5">
                        <label class="form-label" for="date">Date</label>
                        <input
                            class="form-control"
                            id="date"
                            name="date"
                            placeholder="February 2026"
                            value="<?= e($editCertificate['date'] ?? '') ?>"
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="image">Image URL or path</label>
                    <input
                        class="form-control"
                        id="image"
                        name="image"
                        value="<?= e($editCertificate['image'] ?? '') ?>"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="image-file">Or upload image</label>
                    <input
                        class="form-control"
                        id="image-file"
                        type="file"
                        name="image_file"
                        accept="image/jpeg,image/png,image/webp"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="url">Open link</label>
                    <input
                        class="form-control"
                        id="url"
                        name="url"
                        type="url"
                        placeholder="https://drive.google.com/..."
                        value="<?= e($editCertificate['url'] ?? '') ?>"
                    >
                </div>

                <div class="mb-4">
                    <label class="form-label" for="description">Description</label>
                    <textarea
                        class="form-control"
                        id="description"
                        name="description"
                        rows="4"
                    ><?= e($editCertificate['description'] ?? '') ?></textarea>
                </div>

                <button class="btn btn-primary" type="submit">
                    <?= $editCertificate ? 'Update' : 'Add' ?> certificate
                </button>
            </form>
        </section>
    </div>

    <div class="col-lg-7">
        <div class="table-responsive bg-white rounded-3">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Issuer</th>
                        <th scope="col">Date</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$certificates): ?>
                        <tr>
                            <td colspan="4" class="muted">
                                No certificates yet.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($certificates as $certificate): ?>
                        <tr>
                            <td><?= e($certificate['title']) ?></td>
                            <td><?= e($certificate['issuer']) ?></td>
                            <td><?= e($certificate['date']) ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a
                                        class="btn btn-sm btn-outline-dark"
                                        href="?edit=<?= (int) $certificate['id'] ?>"
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
                                            value="<?= (int) $certificate['id'] ?>"
                                        >
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            name="delete"
                                            type="submit"
                                            data-confirm="Delete this certificate?"
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
