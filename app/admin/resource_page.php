<?php
/**
 * Generic admin editor: add / edit / delete rows for one resource
 * defined in app/admin/resources.php. Column names always come from the
 * definition (never from user input), and every value is validated.
 */

require_once APP_PATH . '/lib/uploads.php';

function render_resource_page(string $key): void
{
    require_admin();

    $resource = (require APP_PATH . '/admin/resources.php')[$key];
    $table = $resource['table'];
    $fields = $resource['fields'];
    $page = $key . '.php';
    $errors = [];
    $record = null;

    if (is_post()) {
        verify_csrf();
        $id = (int) ($_POST['id'] ?? 0);

        if (isset($_POST['delete'])) {
            db_query("DELETE FROM {$table} WHERE id = ?", [$id]);
            flash('success', ucfirst($resource['singular']) . ' deleted.');
            redirect('admin/' . $page);
        }

        [$values, $errors] = validate_resource_input($fields, $resource['upload_folder'] ?? null);

        if (!$errors) {
            try {
                save_resource_record($table, $values, $id);
                flash('success', ucfirst($resource['singular']) . ($id ? ' updated.' : ' added.'));
                redirect('admin/' . $page);
            } catch (PDOException $exception) {
                if ($exception->getCode() !== '23000') {
                    throw $exception;
                }
                $errors[] = 'An item with that title or name already exists.';
            }
        }

        // Re-show the form with what was typed.
        $record = ['id' => $id ?: ''] + $values;
    } elseif (isset($_GET['edit'])) {
        $record = db_query("SELECT * FROM {$table} WHERE id = ?", [(int) $_GET['edit']])->fetch() ?: null;
    }

    $rows = db_query("SELECT * FROM {$table} ORDER BY {$resource['order']}")->fetchAll();
    $isEditing = !empty($record['id']);
    $adminTitle = $resource['title'];

    require APP_PATH . '/views/admin/header.php';
    require APP_PATH . '/views/admin/resource.php';
    require APP_PATH . '/views/admin/footer.php';
}

/** @return array{0: array<string, string|int>, 1: list<string>} */
function validate_resource_input(array $fields, ?string $uploadFolder): array
{
    $values = [];
    $errors = [];

    foreach ($fields as $name => $field) {
        $type = $field['type'] ?? 'text';
        $label = $field['label'];
        $value = input($name, 10000);

        switch ($type) {
            case 'number':
                $number = filter_var($value, FILTER_VALIDATE_INT);
                if ($number === false || $number < $field['min'] || $number > $field['max']) {
                    $errors[] = "{$label} must be a whole number from {$field['min']} to {$field['max']}.";
                }
                $values[$name] = (int) $number;
                continue 2;

            case 'url':
                if ($value !== '' && safe_url($value) === '') {
                    $errors[] = "{$label} must be a full http(s):// link.";
                }
                break;

            case 'icon':
                if (!preg_match('/^bi-[a-z0-9-]+$/', $value)) {
                    $errors[] = "{$label} must look like bi-stars.";
                }
                break;

            case 'image':
                if ($value !== '' && safe_url($value) === '' && !is_local_media_path($value)) {
                    $errors[] = "{$label} must be an http(s):// link or a path inside uploads/ or assets/.";
                }

                if ($uploadFolder) {
                    try {
                        $value = upload_image($_FILES[$name . '_file'] ?? [], $uploadFolder) ?? $value;
                    } catch (RuntimeException $exception) {
                        $errors[] = $exception->getMessage();
                    }
                }
                break;
        }

        if (!empty($field['required']) && $value === '') {
            $errors[] = "{$label} is required.";
        } elseif (isset($field['max']) && mb_strlen($value) > $field['max']) {
            $errors[] = "{$label} must be {$field['max']} characters or fewer.";
        }

        $values[$name] = $value;
    }

    return [$values, $errors];
}

function is_local_media_path(string $value): bool
{
    return preg_match('#^(uploads|assets)/[A-Za-z0-9/_.-]+$#', $value) === 1 && !str_contains($value, '..');
}

function save_resource_record(string $table, array $values, int $id): void
{
    $columns = array_keys($values);

    if ($id) {
        $assignments = implode(', ', array_map(static fn ($column) => "{$column} = ?", $columns));
        db_query("UPDATE {$table} SET {$assignments} WHERE id = ?", [...array_values($values), $id]);
        return;
    }

    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    db_query('INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ") VALUES ({$placeholders})", array_values($values));
}
