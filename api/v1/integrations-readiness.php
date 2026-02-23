<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

jsonResponse([
    'success' => true,
    'data' => [
        [
            'provider' => 'SGK',
            'status' => 'planned',
            'authentication' => ['api_key', 'oauth2_client_credentials'],
            'supportedOperations' => [
                'insurance-status-check',
                'income-verification',
                'retirement-eligibility-precheck',
            ],
        ],
        [
            'provider' => 'e-Nabız',
            'status' => 'planned',
            'authentication' => ['oauth2_authorization_code'],
            'supportedOperations' => [
                'disability-report-verification',
                'health-board-report-status',
            ],
        ],
        [
            'provider' => 'e-Devlet',
            'status' => 'planned',
            'authentication' => ['oauth2', 'signed-request'],
            'supportedOperations' => [
                'identity-verification',
                'document-validation',
                'residency-and-household-check',
            ],
        ],
    ],
]);
