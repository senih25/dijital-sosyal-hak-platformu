<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/services/IncomeCalculatorService.php';

header('Content-Type: application/json; charset=utf-8');

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($requestMethod !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Geçersiz istek metodu.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $csrfToken = (string) ($_POST['csrf_token'] ?? '');
    if ($csrfToken !== '' && !verifyCSRFToken($csrfToken)) {
        throw new RuntimeException('CSRF doğrulaması başarısız.');
    }

    $haneGeliri = filter_input(INPUT_POST, 'hane_geliri', FILTER_VALIDATE_FLOAT);
    $uyeSayisi = filter_input(INPUT_POST, 'uye_sayisi', FILTER_VALIDATE_INT);

    if ($haneGeliri === false || $haneGeliri === null || $uyeSayisi === false || $uyeSayisi === null) {
        throw new InvalidArgumentException('Geçersiz giriş verisi gönderildi.');
    }

    $service = new IncomeCalculatorService();
    $result = $service->calculateHouseholdIncomeTest((float) $haneGeliri, (int) $uyeSayisi);

    echo json_encode([
        'ok' => true,
        'result' => $result,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'error' => $exception->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
