<?php

declare(strict_types=1);

function getDatabaseConnection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $configPath = dirname(__DIR__, 2) . '/config/config.php';
    if (!is_file($configPath)) {
        throw new RuntimeException(
            'Database configuration is missing. Copy config/config.example.php to config/config.php.'
        );
    }

    $config = require $configPath;
    if (!is_array($config)) {
        throw new RuntimeException('Database configuration must return an array.');
    }

    $database = $config['database'] ?? null;
    if (!is_array($database)) {
        throw new RuntimeException('Database configuration must contain a database section.');
    }

    foreach (['host', 'port', 'database', 'username', 'password', 'charset'] as $setting) {
        if (!array_key_exists($setting, $database)) {
            throw new RuntimeException("Database configuration is missing the {$setting} setting.");
        }
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $database['host'],
        $database['port'],
        $database['database'],
        $database['charset']
    );

    try {
        $pdo = new PDO(
            $dsn,
            $database['username'],
            $database['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $exception) {
        throw new RuntimeException('Database connection failed.', 0, $exception);
    }

    return $pdo;
}
