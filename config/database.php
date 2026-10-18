<?php
$host = getenv('PORTFOLIO_DB_HOST') ?: '127.0.0.1';
$port = getenv('PORTFOLIO_DB_PORT') ?: '3306';
$db = getenv('PORTFOLIO_DB_NAME') ?: 'alexandra_portfolio';
$user = getenv('PORTFOLIO_DB_USER') ?: 'root';
$pass = getenv('PORTFOLIO_DB_PASSWORD') ?: '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_TIMEOUT => 5,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $exception) {
    http_response_code(500);
    exit('Database connection failed. Import database/alexandra_portfolio.sql and check config/database.php.');
}
