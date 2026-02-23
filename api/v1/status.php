<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

jsonResponse([
    'success' => true,
    'data' => [
        'service' => 'dijital-sosyal-hak-platformu',
        'status' => 'ok',
        'time' => date(DATE_ATOM),
        'environment' => 'production-ready',
    ],
]);
