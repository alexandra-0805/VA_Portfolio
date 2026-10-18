<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$adminTitle = 'Projects';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(true);

    if (isset($_POST['delete'])) {
        $statement = $pdo->prepare('DELETE FROM projects WHERE id = ?');
        $statement->execute([(int) $_POST['delete']]);
        redirect('projects.php');
    }

    $projectId = (int) ($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $technologies = trim($_POST['technologies'] ?? '');
    $projectUrl = trim($_POST['project_url'] ?? '');
    $image = trim($_POST['image'] ?? '');

    if ($projectUrl !== '' && !filter_var($projectUrl, FILTER_VALIDATE_URL)) {
        $projectUrl = '';
    }

    $uploadedImage = upload_image(
        $_FILES['image_file'] ?? [],
        'projects'
    );

    if ($uploadedImage) {
        $image = $uploadedImage;
    }

    if ($projectId) {
        $statement = $pdo->prepare(
            'UPDATE projects SET title = ?, description = ?, technologies = ?, '
            . 'image = ?, project_url = ? WHERE id = ?'
        );
        $statement->execute([
            $title,
            $description,
            $technologies,
            $image,
            $projectUrl,
            $projectId,
        ]);
    } else {
        $statement = $pdo->prepare(
            'INSERT INTO projects '
            . '(title, description, technologies, image, project_url) '
            . 'VALUES (?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $title,
            $description,
            $technologies,
            $image,
            $projectUrl,
        ]);
    }

    redirect('projects.php');
}

$editProject = null;

if (isset($_GET['edit'])) {
    $statement = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $statement->execute([(int) $_GET['edit']]);
    $editProject = $statement->fetch();
}

$projects = $pdo->query(
    'SELECT * FROM projects ORDER BY created_at DESC'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header mb-4">
    <div>
        <div class="section-kicker">Portfolio content</div>
        <h1 class="h2 fw-bold">Projects</h1>
    </div>
    <a class="btn btn-primary" href="projects.php#project-form">
        Add project
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-5" id="project-form">
        <section class="soft-panel">
            <h2 class="h4">
                <?= $editProject ? 'Edit' : 'Add' ?> project
            </h2>

            <form method="post" enctype="multipart/form-data">
                <input
                    type="hidden"
                    name="csrf"
                    value="<?= e(csrf_token()) ?>"
                >
                <input
                    type="hidden"
                    name="id"
                    value="<?= e($editProject['id'] ?? '') ?>"
                >

                <div class="mb-3">
                    <label class="form-label" for="title">Title</label>
                    <input
                        class="form-control"
                        id="title"
                        name="title"
                        value="<?= e($editProject['title'] ?? '') ?>"
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
                    ><?= e($editProject['description'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="technologies">Technologies</label>
                    <input
                        class="form-control"
                        id="technologies"
                        name="technologies"
                        placeholder="HTML, CSS, Bootstrap"
                        value="<?= e($editProject['technologies'] ?? '') ?>"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="image">Image URL or path</label>
                    <input
                        class="form-control"
                        id="image"
                        name="image"
                        value="<?= e($editProject['image'] ?? '') ?>"
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

                <div class="mb-4">
                    <label class="form-label" for="project-url">Project URL</label>
                    <input
                        class="form-control"
                        id="project-url"
                        type="url"
                        name="project_url"
                        value="<?= e($editProject['project_url'] ?? '') ?>"
                    >
                </div>

                <button class="btn btn-primary" type="submit">
                    <?= $editProject ? 'Update' : 'Add' ?> project
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
                        <th scope="col">Tech</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$projects): ?>
                        <tr>
                            <td colspan="3" class="muted">No projects yet.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?= e($project['title']) ?></td>
                            <td><?= e($project['technologies']) ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a
                                        href="?edit=<?= (int) $project['id'] ?>"
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
                                        <button
                                            name="delete"
                                            value="<?= (int) $project['id'] ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            type="submit"
                                            data-confirm="Delete this project?"
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
