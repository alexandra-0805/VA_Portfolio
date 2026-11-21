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

const FEEDBACK_RATING_LABELS = [1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Very good', 5 => 'Excellent'];

/** Published feedback, newest first. */
function get_feedback(?int $limit = null): array
{
    return db_query(
        "SELECT id, name, rating, comment, created_at FROM feedback WHERE status = 'published' "
        . 'ORDER BY created_at DESC, id DESC' . limit_sql($limit)
    )->fetchAll();
}

/**
 * Ratings calculated automatically from the feedback table:
 * average (1 decimal), total reviews, and how many gave each star value (5..1).
 *
 * @return array{average: float, total: int, distribution: array<int, int>}
 */
function get_feedback_summary(bool $publishedOnly = true): array
{
    $counts = db_query(
        'SELECT rating, COUNT(*) FROM feedback'
        . ($publishedOnly ? " WHERE status = 'published'" : '')
        . ' GROUP BY rating'
    )->fetchAll(PDO::FETCH_KEY_PAIR);

    $distribution = [];
    $total = 0;
    $sum = 0;

    for ($stars = 5; $stars >= 1; $stars--) {
        $distribution[$stars] = (int) ($counts[$stars] ?? 0);
        $total += $distribution[$stars];
        $sum += $stars * $distribution[$stars];
    }

    return [
        'average' => $total ? round($sum / $total, 1) : 0.0,
        'total' => $total,
        'distribution' => $distribution,
    ];
}

/** "4.5" or "5" */
function format_rating(float $rating): string
{
    return rtrim(rtrim(number_format($rating, 1), '0'), '.');
}

/** Star icons (full, half, empty) with an accessible label. */
function rating_stars(float $rating, string $extraClass = ''): string
{
    $html = '<span class="rating-stars ' . e($extraClass) . '" role="img" aria-label="'
        . e(format_rating($rating) . ' out of 5 stars') . '">';

    for ($star = 1; $star <= 5; $star++) {
        $icon = match (true) {
            $rating >= $star - 0.25 => 'bi-star-fill',
            $rating >= $star - 0.75 => 'bi-star-half',
            default => 'bi-star',
        };
        $html .= '<i class="bi ' . $icon . '" aria-hidden="true"></i>';
    }

    return $html . '</span>';
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
