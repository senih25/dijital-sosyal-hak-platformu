<?php

declare(strict_types=1);

/**
 * API v1 ortak yardımcı fonksiyonları
 * Rate limiting, CSRF doğrulama, JSON yanıt yardımcıları.
 */

function api_json(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function api_error(string $message, int $status = 400): void
{
    api_json(['success' => false, 'error' => $message], $status);
}

/**
 * Session tabanlı rate limiting.
 * @param string $key    İşlem tanımlayıcısı
 * @param int    $limit  İzin verilen maksimum istek sayısı
 * @param int    $window Zaman penceresi (saniye)
 */
function api_rate_limit(string $key, int $limit = 10, int $window = 60): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $ip      = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $sKey    = 'rl_' . $key . '_' . md5($ip);
    $now     = time();

    if (!isset($_SESSION[$sKey]) || ($now - $_SESSION[$sKey]['start']) > $window) {
        $_SESSION[$sKey] = ['count' => 1, 'start' => $now];
        return;
    }
    $_SESSION[$sKey]['count']++;
    if ($_SESSION[$sKey]['count'] > $limit) {
        api_error('Rate limit aşıldı. Lütfen daha sonra tekrar deneyin.', 429);
    }
}

/**
 * PDO bağlantısı oluşturur (config'den).
 */
function api_get_pdo(): PDO
{
    $configFile = __DIR__ . '/../../config.php';
    if (!file_exists($configFile)) {
        $configFile = __DIR__ . '/../../config.example.php';
    }
    require_once $configFile;

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    return new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}

/**
 * Gelen JSON body'yi parse eder.
 * Yalnızca application/json Content-Type kabul edilir.
 */
function api_get_body(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') === false) {
        // JSON dışı isteklerde güvenli boş dizi döndür
        return [];
    }
    $raw = file_get_contents('php://input');
    if (empty($raw)) {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}
