<?php

declare(strict_types=1);

/**
 * POST /api/v1/user/export
 * Kullanıcının tüm kişisel verilerini JSON olarak dışa aktarır.
 *
 * Beklenen body: { "user_id": 123 }
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../includes/audit_logger.php';
require_once __DIR__ . '/../../includes/encryption_manager.php';
require_once __DIR__ . '/../../modules/data_export.php';

api_rate_limit('export', 3, 3600);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_error('Sadece POST desteklenmektedir.', 405);
}

$body   = api_get_body();
$userId = isset($body['user_id']) ? (int)$body['user_id'] : 0;

if ($userId <= 0) {
    api_error('Geçerli bir user_id sağlayın.');
}

try {
    $pdo        = api_get_pdo();
    $audit      = new AuditLoggerKVKK($pdo);
    $encryption = new EncryptionManager($pdo);
    $exporter   = new DataExport($pdo, $encryption, $audit);

    $filePath = $exporter->exportUserData($userId);
    $filename = basename($filePath);

    api_json([
        'success'   => true,
        'message'   => 'Veri export başarıyla oluşturuldu.',
        'file'      => $filename,
        'download'  => '/storage/exports/' . $filename,
    ]);
} catch (Throwable $e) {
    api_error('Export sırasında hata: ' . $e->getMessage(), 500);
}
