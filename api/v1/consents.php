<?php

declare(strict_types=1);

/**
 * GET /api/v1/audit/consents
 * Onay günlüklerini döndürür.
 * Query params: ?user_id=123&page=1&per_page=50
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../includes/consent_manager.php';

api_rate_limit('consents', 30, 60);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_error('Sadece GET desteklenmektedir.', 405);
}

$userId  = isset($_GET['user_id'])  ? (int)$_GET['user_id']  : 0;
$page    = isset($_GET['page'])     ? max(1, (int)$_GET['page'])     : 1;
$perPage = isset($_GET['per_page']) ? min(100, max(1, (int)$_GET['per_page'])) : 50;

try {
    $pdo     = api_get_pdo();
    $consent = new ConsentManager($pdo);

    if ($userId > 0) {
        $history = $consent->getConsentHistory($userId);
        api_json(['success' => true, 'data' => $history, 'count' => count($history)]);
    } else {
        $result = $consent->getAllConsents($page, $perPage);
        api_json([
            'success'  => true,
            'data'     => $result['rows'],
            'total'    => $result['total'],
            'page'     => $page,
            'per_page' => $perPage,
        ]);
    }
} catch (Throwable $e) {
    api_error('Veriler alınamadı: ' . $e->getMessage(), 500);
}
