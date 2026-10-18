<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(true);

    $skillId = (int) ($_POST['id'] ?? 0);

    if (isset($_POST['delete'])) {
        $statement = $pdo->prepare('DELETE FROM skills WHERE id = ?');
        $statement->execute([$skillId]);
    } else {
        $skillData = [
            trim($_POST['name'] ?? ''),
            trim($_POST['category'] ?? ''),
            max(0, min(100, (int) ($_POST['proficiency'] ?? 0))),
        ];

        if ($skillId) {
            $statement = $pdo->prepare(
                'UPDATE skills SET name = ?, category = ?, proficiency = ? '
                . 'WHERE id = ?'
            );
            $statement->execute([...$skillData, $skillId]);
        } else {
            $statement = $pdo->prepare(
                'INSERT INTO skills (name, category, proficiency) VALUES (?, ?, ?)'
            );
            $statement->execute($skillData);
        }
    }

    redirect('skills.php');
}

$adminTitle = 'Skills';
$editSkill = null;

if (isset($_GET['edit'])) {
    $statement = $pdo->prepare('SELECT * FROM skills WHERE id = ?');
    $statement->execute([(int) $_GET['edit']]);
    $editSkill = $statement->fetch();
}

$skills = $pdo->query(
    'SELECT * FROM skills ORDER BY category, name'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="admin-page-header mb-4">
    <div>
        <div class="section-kicker">Portfolio content</div>
        <h1 class="h2 fw-bold">Skills</h1>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <section class="soft-panel">
            <h2 class="h4">
                <?= $editSkill ? 'Edit' : 'Add' ?> skill
            </h2>
            <form method="post">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input
                    type="hidden"
                    name="id"
                    value="<?= e($editSkill['id'] ?? '') ?>"
                >

                <div class="mb-3">
                    <label class="form-label" for="name">Skill name</label>
                    <input
                        class="form-control"
                        id="name"
                        name="name"
                        value="<?= e($editSkill['name'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="category">Category</label>
                    <input
                        class="form-control"
                        id="category"
                        name="category"
                        value="<?= e($editSkill['category'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="form-label" for="proficiency">Proficiency</label>
                    <input
                        class="form-control"
                        id="proficiency"
                        type="number"
                        name="proficiency"
                        min="0"
                        max="100"
                        value="<?= e($editSkill['proficiency'] ?? '') ?>"
                        required
                    >
                </div>

                <button class="btn btn-primary" type="submit">
                    <?= $editSkill ? 'Update' : 'Add' ?> skill
                </button>
            </form>
        </section>
    </div>

    <div class="col-lg-8">
        <div class="table-responsive bg-white rounded-3">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Category</th>
                        <th scope="col">Level</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$skills): ?>
                        <tr>
                            <td colspan="4" class="muted">No skills yet.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($skills as $skill): ?>
                        <tr>
                            <td><?= e($skill['name']) ?></td>
                            <td><?= e($skill['category']) ?></td>
                            <td><?= (int) $skill['proficiency'] ?>%</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a
                                        href="?edit=<?= (int) $skill['id'] ?>"
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
                                            value="<?= (int) $skill['id'] ?>"
                                        >
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            name="delete"
                                            type="submit"
                                            data-confirm="Delete this skill?"
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
