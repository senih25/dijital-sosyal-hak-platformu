<?php

declare(strict_types=1);

if (!function_exists('loadEnvFile')) {
    function loadEnvFile(string $envPath): void
    {
        if (!is_file($envPath) || !is_readable($envPath)) {
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#') || strpos($trimmed, '=') === false) {
                continue;
            }

            [$name, $value] = array_map('trim', explode('=', $trimmed, 2));
            $value = trim($value, "\"'");

            if ($name !== '' && getenv($name) === false) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

$rootPath = dirname(__DIR__);
loadEnvFile($rootPath . '/.env');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', $rootPath);
}

if (!defined('SITE_URL')) {
    $siteUrl = getenv('SITE_URL') ?: '';
    define('SITE_URL', rtrim($siteUrl, '/'));
}

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'database_name';
$dbUser = getenv('DB_USER') ?: 'username';
$dbPass = getenv('DB_PASS') ?: 'password';

if (!defined('DB_HOST')) {
    define('DB_HOST', $dbHost);
}
if (!defined('DB_NAME')) {
    define('DB_NAME', $dbName);
}
if (!defined('DB_USER')) {
    define('DB_USER', $dbUser);
}
if (!defined('DB_PASS')) {
    define('DB_PASS', $dbPass);
}

if (!isset($pdo)) {
    try {
        $pdo = new PDO(
            sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME),
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (PDOException $exception) {
        $pdo = null;
        error_log('Database connection could not be established: ' . $exception->getMessage());
    }
}

require_once ROOT_PATH . '/functions.php';
