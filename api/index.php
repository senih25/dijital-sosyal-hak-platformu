<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '/');
$segments = explode('/', $path);
$endpoint = end($segments);

if ($endpoint === 'index.php' || $endpoint === 'api') {
    jsonResponse([
        'success' => true,
        'service' => 'Dijital Sosyal Hak Platformu API',
        'version' => 'v1',
        'documentation' => '/docs/API.md',
        'endpoints' => [
            '/api/v1/status.php',
            '/api/v1/rights-eligibility.php',
            '/api/v1/integrations-readiness.php',
        ],
    ]);
}

apiError('Endpoint bulunamadı.', 404);
