<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/audit_logger.php';
require_once __DIR__ . '/../includes/consent_manager.php';
require_once __DIR__ . '/../modules/data_deletion.php';
require_once __DIR__ . '/../includes/encryption_manager.php';

/**
 * KVKK Uyum Yönetim Paneli (Admin)
 * - Veri silme istekleri yönetimi (approve / reject)
 * - Consent audit trail
 * - Şifrelenmiş alanlar raporlaması
 * - KVKK uyum attestation (sertifika)
 *
 * Bu dosya doğrudan tarayıcıdan erişildiğinde yönetim arayüzünü sunar.
 * Gerçek bir projede oturum / rol kontrolü eklenmeli.
 */

// ─── Bootstrap ──────────────────────────────────────────────────────────────
$configFile = __DIR__ . '/../config.php';
if (!file_exists($configFile)) {
    $configFile = __DIR__ . '/../config.example.php';
}
require_once $configFile;

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Throwable $e) {
    http_response_code(500);
    die('Veritabanı bağlantısı kurulamadı: ' . htmlspecialchars($e->getMessage()));
}

$audit       = new AuditLoggerKVKK($pdo);
$consent     = new ConsentManager($pdo);
$deletion    = new DataDeletion($pdo, $audit);
$encryption  = new EncryptionManager($pdo);

// ─── POST İşlemleri ─────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF token üret
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($csrfToken, $submittedToken)) {
        http_response_code(403);
        die('Geçersiz CSRF token.');
    }

    $action    = $_POST['action']     ?? '';
    $requestId = (int)($_POST['request_id'] ?? 0);

    if ($action === 'approve' && $requestId > 0) {
        $hardDelete = !empty($_POST['hard_delete']);
        $deletion->approveRequest($requestId, $hardDelete);
        $message = 'Talep onaylandı.';
    } elseif ($action === 'reject' && $requestId > 0) {
        $deletion->rejectRequest($requestId);
        $message = 'Talep reddedildi.';
    }
}

// ─── Veri ───────────────────────────────────────────────────────────────────
$deletionData = $deletion->getAllRequests(1, 100);
$consentData  = $consent->getAllConsents(1, 100);
$auditData    = $audit->getAllLogs(1, 100);
$keyInfo      = $encryption->getActiveKeyInfo();

// ─── HTML ────────────────────────────────────────────────────────────────────
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KVKK Uyum Paneli</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background: #f5f5f5; }
        h1,h2 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 30px; background: #fff; }
        th,td { border: 1px solid #ccc; padding: 8px 12px; text-align: left; font-size: 13px; }
        th { background: #4a90d9; color: #fff; }
        tr:nth-child(even) { background: #f9f9f9; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .badge-pending  { background: #fff3cd; color: #856404; }
        .badge-approved { background: #d4edda; color: #155724; }
        .badge-rejected { background: #f8d7da; color: #721c24; }
        .msg { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        form button { padding: 5px 10px; cursor: pointer; }
        .section { background: #fff; padding: 20px; margin-bottom: 30px; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,.1); }
        .attestation { background: #e8f4fd; border-left: 4px solid #4a90d9; padding: 15px; border-radius: 4px; }
    </style>
</head>
<body>
<h1>🔐 KVKK Uyum Yönetim Paneli</h1>

<?php if ($message): ?>
    <div class="msg"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- Şifreleme Anahtarı Durumu -->
<div class="section">
    <h2>🔑 Aktif Şifreleme Anahtarı</h2>
    <p><strong>ID:</strong> <?= htmlspecialchars((string)$keyInfo['id']) ?>
       &nbsp; <strong>Algoritma:</strong> <?= htmlspecialchars($keyInfo['algorithm']) ?>
       &nbsp; <strong>Oluşturulma:</strong> <?= htmlspecialchars($keyInfo['created_at']) ?></p>
</div>

<!-- Veri Silme Talepleri -->
<div class="section">
    <h2>🗑️ Veri Silme Talepleri (<?= $deletionData['total'] ?>)</h2>
    <table>
        <thead>
            <tr><th>ID</th><th>Kullanıcı</th><th>Neden</th><th>Talep Tarihi</th><th>Durum</th><th>İşlemler</th></tr>
        </thead>
        <tbody>
        <?php foreach ($deletionData['rows'] as $row): ?>
            <tr>
                <td><?= (int)$row['id'] ?></td>
                <td><?= (int)$row['user_id'] ?></td>
                <td><?= htmlspecialchars($row['reason'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['request_date']) ?></td>
                <td><span class="badge badge-<?= htmlspecialchars($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                <td>
                <?php if ($row['status'] === 'pending'): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="request_id" value="<?= (int)$row['id'] ?>">
                        <input type="hidden" name="action" value="approve">
                        <label><input type="checkbox" name="hard_delete"> Kalıcı sil</label>
                        <button type="submit">Onayla</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="request_id" value="<?= (int)$row['id'] ?>">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit">Reddet</button>
                    </form>
                <?php else: ?>
                    <em><?= htmlspecialchars($row['processed_date'] ?? '-') ?></em>
                <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Rıza Kayıtları -->
<div class="section">
    <h2>✅ Rıza Kayıtları (<?= $consentData['total'] ?>)</h2>
    <table>
        <thead>
            <tr><th>ID</th><th>Kullanıcı</th><th>Tip</th><th>Verildi</th><th>İptal</th><th>IP</th></tr>
        </thead>
        <tbody>
        <?php foreach ($consentData['rows'] as $row): ?>
            <tr>
                <td><?= (int)$row['id'] ?></td>
                <td><?= (int)$row['user_id'] ?></td>
                <td><?= htmlspecialchars($row['consent_type']) ?></td>
                <td><?= htmlspecialchars($row['given_at']) ?></td>
                <td><?= htmlspecialchars($row['revoked_at'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['ip_address']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Denetim Kayıtları -->
<div class="section">
    <h2>📋 Denetim Kayıtları (<?= $auditData['total'] ?>)</h2>
    <table>
        <thead>
            <tr><th>ID</th><th>Kullanıcı</th><th>İşlem</th><th>IP</th><th>Sonuç</th><th>Tarih</th></tr>
        </thead>
        <tbody>
        <?php foreach ($auditData['rows'] as $row): ?>
            <tr>
                <td><?= (int)$row['id'] ?></td>
                <td><?= htmlspecialchars((string)($row['user_id'] ?? '-')) ?></td>
                <td><?= htmlspecialchars($row['action']) ?></td>
                <td><?= htmlspecialchars($row['ip_address'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['result']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- KVKK Uyum Attestation -->
<div class="section">
    <h2>🏅 KVKK Uyum Attestation</h2>
    <div class="attestation">
        <strong>Dijital Sosyal Hak Platformu</strong> aşağıdaki KVKK gereksinimlerine uymaktadır:<br><br>
        ✅ Açık rıza kaydı ve yönetimi (Md. 3, 5)<br>
        ✅ Veri güvenliği (AES-256-CBC şifreleme) (Md. 12)<br>
        ✅ Değiştirilemez denetim izi (Md. 12)<br>
        ✅ Veri silme hakkı (Md. 7, 11)<br>
        ✅ Veri taşınabilirlik / export (Md. 11)<br>
        ✅ Anahtar rotasyon politikası (90 gün)<br><br>
        <em>Rapor tarihi: <?= date('d.m.Y H:i') ?></em>
    </div>
</div>

</body>
</html>
