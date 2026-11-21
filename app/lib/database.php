<?php
/**
 * Shared PDO connection (created lazily, once per request).
 */

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        config('db.host'),
        config('db.port'),
        config('db.name')
    );

    try {
        $pdo = new PDO($dsn, config('db.user'), config('db.password'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 5,
        ]);
    } catch (PDOException $exception) {
        error_log('Database connection failed: ' . $exception->getMessage());
        http_response_code(503);
        exit('The site is temporarily unavailable. Please try again shortly.');
    }

    return $pdo;
}

/** Run a prepared query and return the statement. */
function db_query(string $sql, array $params = []): PDOStatement
{
    $statement = db()->prepare($sql);
    $statement->execute($params);

    return $statement;
}
