<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/lib/params.php';
require_once __DIR__ . '/../app/lib/calculators.php';

header('Content-Type: application/json; charset=utf-8');

try {

    $haneGeliri = isset($_POST['hane_geliri'])
        ? (float)$_POST['hane_geliri']
        : 0;

    $uyeSayisi = isset($_POST['uye_sayisi'])
        ? (int)$_POST['uye_sayisi']
        : 0;

    $yil = (int)date('Y');

    $netAsgari = get_param($pdo, $yil, 'net_asgari_ucret');
    $esikOrani = get_param($pdo, $yil, 'evde_bakim_esik_orani');

    $result = calc_hane_gelir_testi(
        $haneGeliri,
        $uyeSayisi,
        $netAsgari,
        $esikOrani
    );

    echo json_encode([
        'ok' => true,
        'result' => $result
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {

    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
