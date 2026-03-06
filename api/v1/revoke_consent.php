<?php

declare(strict_types=1);

/**
 * POST /api/v1/user/revoke-consent
 * Kullanıcının belirli bir rızasını iptal eder.
 *
 * Beklenen body: { "user_id": 123, "consent_type": "marketing" }
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../includes/consent_manager.php';
require_once __DIR__ . '/../../includes/audit_logger.php';

api_rate_limit('revoke_consent', 10, 3600);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_error('Sadece POST desteklenmektedir.', 405);
}

$body        = api_get_body();
$userId      = isset($body['user_id'])      ? (int)$body['user_id']          : 0;
$consentType = isset($body['consent_type']) ? trim((string)$body['consent_type']) : '';

if ($userId <= 0) {
    api_error('Geçerli bir user_id sağlayın.');
}
if (empty($consentType)) {
    api_error('consent_type gereklidir.');
}

try {
    $pdo     = api_get_pdo();
    $consent = new ConsentManager($pdo);
    $audit   = new AuditLoggerKVKK($pdo);

    $revoked = $consent->revokeConsent($userId, $consentType);
    $audit->log('consent_revoked', $userId, ['consent_type' => $consentType, 'revoked' => $revoked]);

    api_json([
        'success' => true,
        'revoked' => $revoked,
        'message' => $revoked ? 'Rıza başarıyla iptal edildi.' : 'İptal edilecek aktif rıza bulunamadı.',
    ]);
} catch (LogicException $e) {
    api_error($e->getMessage(), 422);
} catch (Throwable $e) {
    api_error('İşlem sırasında hata: ' . $e->getMessage(), 500);
}
