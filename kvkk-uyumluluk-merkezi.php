<?php
require_once __DIR__ . '/security.php';
SecurityManager::secureSessionStart();

$userId = $_SESSION['user_id'] ?? 1;
$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = SecurityManager::sanitizeInput($_POST['action'] ?? '');

    if ($action === 'save_permissions') {
        $permissions = [
            'hizmet_sunumu' => isset($_POST['hizmet_sunumu']),
            'iletisim' => isset($_POST['iletisim']),
            'analiz' => isset($_POST['analiz']),
            'pazarlama' => isset($_POST['pazarlama'])
        ];

        KVKKCompliance::updateDataProcessingPermissions($userId, $permissions);
        KVKKCompliance::recordConsent($userId, 'veri_isleme_izinleri_guncellendi', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $message = 'Veri işleme izinleriniz güncellendi.';
    }

    if ($action === 'request_right') {
        $requestType = SecurityManager::sanitizeInput($_POST['request_type'] ?? '');

        if ($requestType === 'silme') {
            KVKKCompliance::requestDataDeletion($userId, 'Kullanıcı paneli üzerinden talep');
            $message = 'Silme talebiniz alınmıştır.';
        } elseif ($requestType === 'duzeltme') {
            $fields = [
                'field' => SecurityManager::sanitizeInput($_POST['field_name'] ?? ''),
                'new_value' => SecurityManager::sanitizeInput($_POST['new_value'] ?? '')
            ];
            KVKKCompliance::requestDataCorrection($userId, $fields);
            $message = 'Düzeltme talebiniz alınmıştır.';
        } elseif ($requestType === 'tasinabilirlik') {
            $export = KVKKCompliance::requestDataPortability($userId, 'json');
            $message = 'Taşınabilirlik talebiniz oluşturuldu. Önizleme verisi aşağıdadır.';
            $_SESSION['kvkk_export_preview'] = $export;
        } else {
            $error = 'Geçersiz hak talebi türü.';
        }
    }
}

$purposes = KVKKCompliance::getProcessingPurposes();
$notice = KVKKCompliance::getPrivacyNoticeSections();
$currentPermissions = $_SESSION['data_processing_permissions'][$userId]['permissions'] ?? [];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KVKK Uyum Merkezi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-3">KVKK Uyum Merkezi</h1>
    <p class="text-muted">Çerez yönetimi, veri işleme izinleri, kullanıcı hakları ve aydınlatma metinleri tek panelde yönetilir.</p>

    <?php if ($message): ?><div class="alert alert-success"><?php echo SecurityManager::preventXSS($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo SecurityManager::preventXSS($error); ?></div><?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-bold">1) Çerez ve Veri İşleme İzinleri</div>
                <div class="card-body">
                    <p class="small text-muted">Çerez detay ayarlarınız için <code>cookie-consent.js</code> aktif olarak kullanılmaktadır.</p>
                    <form method="POST">
                        <input type="hidden" name="action" value="save_permissions">
                        <?php foreach ($purposes as $key => $label): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="<?php echo $key; ?>" name="<?php echo $key; ?>" <?php echo !empty($currentPermissions[$key]) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="<?php echo $key; ?>"><?php echo SecurityManager::preventXSS($label); ?></label>
                            </div>
                        <?php endforeach; ?>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="pazarlama" name="pazarlama" <?php echo !empty($currentPermissions['pazarlama']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="pazarlama">Pazarlama iletişimi (SMS/E-posta)</label>
                        </div>
                        <button class="btn btn-primary">İzinleri Kaydet</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-bold">2) KVKK Hak Talepleri</div>
                <div class="card-body">
                    <form method="POST" class="mb-3">
                        <input type="hidden" name="action" value="request_right">
                        <input type="hidden" name="request_type" value="silme">
                        <button class="btn btn-outline-danger w-100">Verilerimi Sil (Madde 7)</button>
                    </form>

                    <form method="POST" class="mb-3">
                        <input type="hidden" name="action" value="request_right">
                        <input type="hidden" name="request_type" value="duzeltme">
                        <div class="mb-2"><input type="text" class="form-control" name="field_name" placeholder="Düzeltilecek alan (ör: telefon)" required></div>
                        <div class="mb-2"><input type="text" class="form-control" name="new_value" placeholder="Yeni değer" required></div>
                        <button class="btn btn-outline-warning w-100">Veri Düzeltme Talebi</button>
                    </form>

                    <form method="POST">
                        <input type="hidden" name="action" value="request_right">
                        <input type="hidden" name="request_type" value="tasinabilirlik">
                        <button class="btn btn-outline-success w-100">Veri Taşınabilirlik (JSON)</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header fw-bold">3) Aydınlatma Metni Özeti</div>
                <div class="card-body">
                    <p><strong>Veri Sorumlusu:</strong> <?php echo SecurityManager::preventXSS($notice['veri_sorumlusu']); ?></p>
                    <p class="mb-1"><strong>Toplanan Veri Kategorileri:</strong></p>
                    <ul>
                        <?php foreach ($notice['toplanan_veriler'] as $item): ?>
                            <li><?php echo SecurityManager::preventXSS($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="mb-1"><strong>Kullanıcı Hakları:</strong></p>
                    <ul>
                        <?php foreach ($notice['kullanici_haklari'] as $item): ?>
                            <li><?php echo SecurityManager::preventXSS($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <?php if (!empty($_SESSION['kvkk_export_preview'])): ?>
            <div class="col-12">
                <div class="card border-success">
                    <div class="card-header fw-bold">Veri Taşınabilirlik Önizlemesi</div>
                    <div class="card-body">
                        <pre class="mb-0"><?php echo SecurityManager::preventXSS($_SESSION['kvkk_export_preview']); ?></pre>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script src="cookie-consent.js"></script>
</body>
</html>
