<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    apiError('Bu endpoint için sadece POST desteklenir.', 405);
}

$input = getJsonInput();
$householdIncome = (float)($input['householdIncome'] ?? 0);
$householdMembers = (int)($input['householdMembers'] ?? 0);
$minWage = (float)($input['minWage'] ?? 17002.12);

if ($householdMembers < 1) {
    apiError('householdMembers en az 1 olmalıdır.', 422);
}

if ($householdIncome < 0) {
    apiError('householdIncome negatif olamaz.', 422);
}

$perPersonIncome = $householdIncome / $householdMembers;
$threshold = $minWage / 3;

$eligibility = [];
$status = 'info';
$statusText = '';

if ($perPersonIncome < $threshold) {
    $eligibility = [
        'Evde Bakım Maaşı',
        '2022 Sayılı Kanun Engelli Aylığı',
        'Sosyal Yardım Programları',
        'Ücretsiz Sağlık Hizmetleri',
    ];
    $status = 'success';
    $statusText = 'Birçok sosyal yardım için uygunsunuz';
} elseif ($perPersonIncome < ($threshold * 2)) {
    $eligibility = [
        'Bazı Sosyal Yardım Programları',
        'Kısmi Destekler',
    ];
    $status = 'warning';
    $statusText = 'Bazı yardımlar için uygun olabilirsiniz';
} else {
    $eligibility = [
        'Gelir sınırı üzerinde',
        'Belirli yardımlar için uygun olmayabilirsiniz',
    ];
    $statusText = 'Gelir seviyeniz yardım sınırının üzerinde';
}

jsonResponse([
    'success' => true,
    'data' => [
        'householdIncome' => round($householdIncome, 2),
        'householdMembers' => $householdMembers,
        'perPersonIncome' => round($perPersonIncome, 2),
        'minWage' => round($minWage, 2),
        'threshold' => round($threshold, 2),
        'status' => $status,
        'statusText' => $statusText,
        'eligibility' => $eligibility,
    ],
    'integrationHints' => [
        'sgk' => 'SGK gelir ve sigorta doğrulama adaptörü ile entegre edilebilir.',
        'e_nabiz' => 'Sağlık raporu ve engellilik oranı doğrulama akışına hazır.',
        'e_devlet' => 'Kimlik ve belge doğrulama adımlarına genişletilebilir.',
    ],
]);
