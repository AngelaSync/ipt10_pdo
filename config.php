<?php
declare(strict_types=1);

$host = '127.0.0.1';
$db   = 'ip10_lab';

try {
    // TODO(21): DSN built from the same values you used with mysqli
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        'root',
        '',
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_STRINGIFY_FETCHES  => false,
            // TODO(22): manual transaction control
            PDO::ATTR_AUTOCOMMIT         => false,
        ]
    );
} catch (PDOException $e) {
    error_log('DB connect failed: ' . $e->getMessage());
    die('Database unavailable');
}