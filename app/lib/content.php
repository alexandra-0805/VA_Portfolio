<?php
/**
 * Read-only content queries shared by the public pages and the dashboard.
 */

const DEFAULT_PROJECT_IMAGE = 'https://images.unsplash.com/photo-1558655146-9f40138edfeb?auto=format&fit=crop&w=1200&q=80';

function get_services(?int $limit = null): array
{
    return db_query('SELECT * FROM services ORDER BY id' . limit_sql($limit))->fetchAll();
}

function get_skills(?int $limit = null): array
{
    return db_query('SELECT * FROM skills ORDER BY proficiency DESC, name' . limit_sql($limit))->fetchAll();
}

function get_projects(?int $limit = null): array
{
    return db_query('SELECT * FROM projects ORDER BY created_at DESC, id DESC' . limit_sql($limit))->fetchAll();
}

function get_project(int $id): ?array
{
    return db_query('SELECT * FROM projects WHERE id = ? LIMIT 1', [$id])->fetch() ?: null;
}

function get_certificates(): array
{
    return db_query('SELECT * FROM certificates ORDER BY created_at DESC, id DESC')->fetchAll();
}

function limit_sql(?int $limit): string
{
    return $limit ? ' LIMIT ' . max(1, $limit) : '';
}

/** "HTML, CSS, JS" -> ['HTML', 'CSS', 'JS'] */
function split_list(?string $value, ?int $limit = null): array
{
    $items = array_values(array_filter(array_map('trim', explode(',', (string) $value))));

    return $limit ? array_slice($items, 0, $limit) : $items;
}

function project_image(array $project): string
{
    return media_url($project['image'] ?? '', DEFAULT_PROJECT_IMAGE);
}
