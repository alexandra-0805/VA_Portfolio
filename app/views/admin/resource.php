<?php
/**
 * Generic editor markup: form on the left, table on the right.
 * Provided by render_resource_page(): $resource, $fields, $record, $rows, $errors, $isEditing, $page,
 * $visibleRows (rows shown before "See more") and $pageSize.
 */
$singular = $resource['singular'];
$hasUpload = !empty($resource['upload_folder']);
?>
<div class="admin-page-header mb-4">
    <div>
        <div class="section-kicker"><?= e($resource['kicker']) ?></div>
        <h1 class="h2 fw-bold"><?= e($resource['title']) ?></h1>
    </div>
    <a class="btn btn-primary" href="<?= e(url('admin/' . $page)) ?>#resource-form">
        <i class="bi bi-plus-lg" aria-hidden="true"></i> Add <?= e($singular) ?>
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-5" id="resource-form">
        <section class="soft-panel">
            <h2 class="h4 mb-3"><?= $isEditing ? 'Edit' : 'Add' ?> <?= e($singular) ?></h2>
            <?= render_alerts($errors) ?>

            <form method="post" <?= $hasUpload ? 'enctype="multipart/form-data"' : '' ?>>
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= e($record['id'] ?? '') ?>">

                <div class="row g-3 mb-4">
                    <?php foreach ($fields as $name => $field): ?>
                        <?php
                        $type = $field['type'] ?? 'text';
                        $value = $record[$name] ?? ($field['default'] ?? '');
                        $required = !empty($field['required']) ? 'required' : '';
                        $placeholder = isset($field['placeholder']) ? 'placeholder="' . e($field['placeholder']) . '"' : '';
                        $maxLength = isset($field['max']) && $type !== 'number' ? 'maxlength="' . (int) $field['max'] . '"' : '';
                        ?>
                        <div class="<?= e($field['col'] ?? 'col-12') ?>">
                            <label class="form-label" for="field-<?= e($name) ?>"><?= e($field['label']) ?></label>

                            <?php if ($type === 'textarea'): ?>
                                <textarea class="form-control" id="field-<?= e($name) ?>" name="<?= e($name) ?>" rows="4" <?= $maxLength ?> <?= $required ?>><?= e($value) ?></textarea>
                            <?php elseif ($type === 'number'): ?>
                                <input class="form-control" id="field-<?= e($name) ?>" type="number" name="<?= e($name) ?>" min="<?= (int) $field['min'] ?>" max="<?= (int) $field['max'] ?>" value="<?= e($value) ?>" <?= $required ?>>
                            <?php else: ?>
                                <input class="form-control" id="field-<?= e($name) ?>" type="<?= $type === 'url' ? 'url' : 'text' ?>" name="<?= e($name) ?>" value="<?= e($value) ?>" <?= $maxLength ?> <?= $placeholder ?> <?= $required ?>>
                            <?php endif; ?>

                            <?php if ($type === 'image' && $hasUpload): ?>
                                <?php if ($value): ?>
                                    <img class="admin-image-preview mt-2" src="<?= e(media_url($value)) ?>" alt="Current image" loading="lazy">
                                <?php endif; ?>
                                <label class="form-label mt-3" for="field-<?= e($name) ?>-file">Or upload an image</label>
                                <input class="form-control" id="field-<?= e($name) ?>-file" type="file" name="<?= e($name) ?>_file" accept="image/jpeg,image/png,image/webp">
                                <div class="form-text">JPG, PNG, or WebP, up to 5 MB.</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-primary" type="submit"><?= $isEditing ? 'Update' : 'Add' ?> <?= e($singular) ?></button>
                    <?php if ($isEditing): ?>
                        <a class="btn btn-outline-dark" href="<?= e(url('admin/' . $page)) ?>">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>
    </div>

    <div class="col-lg-7" id="resource-table">
        <div class="table-responsive bg-white rounded-3">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <?php foreach ($resource['columns'] as $label): ?>
                            <th scope="col"><?= e($label) ?></th>
                        <?php endforeach; ?>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$rows): ?>
                        <tr><td colspan="<?= count($resource['columns']) + 1 ?>" class="muted">No <?= e(strtolower($resource['title'])) ?> yet.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($rows as $index => $row): ?>
                        <tr <?= $index >= $visibleRows ? 'hidden data-more-row' : '' ?>>
                            <?php foreach (array_keys($resource['columns']) as $column): ?>
                                <td><?= e($row[$column]) ?><?= ($fields[$column]['type'] ?? '') === 'number' ? '%' : '' ?></td>
                            <?php endforeach; ?>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-sm btn-outline-dark" href="<?= e(url('admin/' . $page . '?edit=' . (int) $row['id'])) ?>#resource-form">Edit</a>
                                    <form method="post">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" name="delete" value="1" type="submit" data-confirm="Delete this <?= e($singular) ?>?">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($visibleRows < count($rows)): ?>
            <div class="admin-table-more" data-more-table>
                <span class="muted" data-more-count>
                    Showing <?= $visibleRows ?> of <?= count($rows) ?>
                </span>
                <a
                    class="btn btn-sm btn-outline-dark"
                    href="<?= e(url('admin/' . $page . '?show=' . ($visibleRows + $pageSize))) ?>#resource-table"
                    data-more-button
                    data-more-step="<?= $pageSize ?>"
                >
                    See more
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
