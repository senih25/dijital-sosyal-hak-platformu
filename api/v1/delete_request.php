<?php

declare(strict_types=1);

/**
 * POST /api/v1/user/delete-request
 * Veri silme talebi oluşturur.
 *
 * Beklenen body: { "user_id": 123, "reason": "..." }
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../includes/audit_logger.php';
require_once __DIR__ . '/../../modules/data_deletion.php';

api_rate_limit('delete_request', 5, 3600);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_error('Sadece POST desteklenmektedir.', 405);
}

$body   = api_get_body();
$userId = isset($body['user_id']) ? (int)$body['user_id'] : 0;
$reason = isset($body['reason'])  ? trim((string)$body['reason']) : '';

if ($userId <= 0) {
    api_error('Geçerli bir user_id sağlayın.');
}

try {
    $pdo       = api_get_pdo();
    $audit     = new AuditLoggerKVKK($pdo);
    $deletion  = new DataDeletion($pdo, $audit);

    $requestId = $deletion->createRequest($userId, $reason);

    api_json([
        'success'    => true,
        'message'    => 'Silme talebiniz alındı. 30 gün içinde işlenecektir.',
        'request_id' => $requestId,
    ]);
} catch (Throwable $e) {
    api_error('Talep oluşturulamadı: ' . $e->getMessage(), 500);
}
