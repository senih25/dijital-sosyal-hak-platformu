<?php

declare(strict_types=1);

/**
 * KVKK Uyum Birim Testleri
 *
 * Çalıştırma: php tests/kvkk_compliance_test.php
 *
 * Gerçek bir veritabanına ihtiyaç duymadan çalışmak için SQLite bellek içi
 * veritabanı kullanır.  PHP'nin pdo_sqlite eklentisi yüklenmiş olmalıdır.
 */

// Test ortamı için PROFILE_DATA_KEY ayarla (yoksa audit_logger exception fırlatır)
if (!getenv('PROFILE_DATA_KEY')) {
    putenv('PROFILE_DATA_KEY=test-secret-key-for-unit-tests-only');
}

require_once __DIR__ . '/../includes/audit_logger.php';
require_once __DIR__ . '/../includes/encryption_manager.php';
require_once __DIR__ . '/../includes/consent_manager.php';
require_once __DIR__ . '/../modules/data_deletion.php';
require_once __DIR__ . '/../modules/data_export.php';

// ─── Basit Test Çerçevesi ────────────────────────────────────────────────────

$passed = 0;
$failed = 0;

function test(string $name, callable $fn): void
{
    global $passed, $failed;
    try {
        $fn();
        echo "  ✅  {$name}\n";
        $passed++;
    } catch (Throwable $e) {
        echo "  ❌  {$name}: " . $e->getMessage() . "\n";
        $failed++;
    }
}

function assert_true(bool $cond, string $msg = 'Beklenen: true'): void
{
    if (!$cond) {
        throw new RuntimeException($msg);
    }
}

function assert_equals($a, $b, string $msg = ''): void
{
    if ($a !== $b) {
        throw new RuntimeException($msg ?: "Beklenen: " . var_export($b, true) . ", Gerçek: " . var_export($a, true));
    }
}

// ─── SQLite In-Memory Şema ───────────────────────────────────────────────────

function buildPdo(): PDO
{
    $pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $pdo->exec("CREATE TABLE IF NOT EXISTS audit_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        action TEXT NOT NULL,
        details TEXT,
        ip_address TEXT NOT NULL DEFAULT '',
        user_agent TEXT NOT NULL DEFAULT '',
        result TEXT NOT NULL DEFAULT 'success',
        encrypted_fields TEXT,
        created_at TEXT NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS encryption_keys (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        algorithm TEXT NOT NULL,
        key_material TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'active',
        created_at TEXT NOT NULL,
        rotated_at TEXT
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS consent_records (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        consent_type TEXT NOT NULL,
        given_at TEXT NOT NULL,
        revoked_at TEXT,
        ip_address TEXT NOT NULL DEFAULT '',
        user_agent TEXT NOT NULL DEFAULT ''
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS deletion_requests (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        reason TEXT,
        request_date TEXT NOT NULL,
        processed_date TEXT,
        status TEXT NOT NULL DEFAULT 'pending',
        verification_hash TEXT NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS user_profiles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL UNIQUE,
        first_name TEXT NOT NULL,
        last_name TEXT NOT NULL,
        phone TEXT,
        email TEXT,
        identity_number_hash TEXT,
        address TEXT,
        birth_date TEXT,
        kvkk_explicit_consent INTEGER DEFAULT 0
    )");

    return $pdo;
}

// ─── Test Grupları ───────────────────────────────────────────────────────────

echo "\n=== 1. Audit Logging Testleri ===\n";

$pdo = buildPdo();
$auditLogDir = sys_get_temp_dir() . '/kvkk_test_audit_' . uniqid();

test('Audit log kaydı oluşturulabilmeli', function () use ($pdo, $auditLogDir) {
    $logger = new AuditLoggerKVKK($pdo, $auditLogDir);
    $id     = $logger->log('test_action', 1, ['key' => 'value'], '127.0.0.1');
    assert_true($id > 0, 'log() pozitif ID döndürmeli');
});

test('Kullanıcı bazlı log getirme çalışmalı', function () use ($pdo, $auditLogDir) {
    $logger = new AuditLoggerKVKK($pdo, $auditLogDir);
    $logger->log('action_a', 99, [], '10.0.0.1');
    $logs = $logger->getLogsForUser(99);
    assert_true(count($logs) >= 1, 'En az 1 log bekleniyor');
    assert_equals($logs[0]['action'], 'action_a');
});

test('Tüm logları sayfalı getirme çalışmalı', function () use ($pdo, $auditLogDir) {
    $logger = new AuditLoggerKVKK($pdo, $auditLogDir);
    $result = $logger->getAllLogs(1, 100);
    assert_true(isset($result['rows'], $result['total']));
    assert_true($result['total'] >= 2);
});

test('Hassas alanlar şifrelenmiş olarak saklanmalı', function () use ($pdo, $auditLogDir) {
    $logger = new AuditLoggerKVKK($pdo, $auditLogDir);
    $logger->log('login', 5, ['email' => 'test@example.com', 'phone' => '05001234567'], '1.2.3.4');

    $stmt = $pdo->prepare('SELECT encrypted_fields, details FROM audit_logs WHERE action = ? AND user_id = ?');
    $stmt->execute(['login', 5]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    assert_true(!empty($row['encrypted_fields']), 'encrypted_fields dolu olmalı');
    $enc = json_decode($row['encrypted_fields'], true);
    assert_true(isset($enc['email']), 'email şifrelenmiş alanda bulunmalı');
    assert_true($enc['email'] !== 'test@example.com', 'email düz metin olmamalı');
});

// ─── 2. Şifreleme Testleri ─────────────────────────────────────────────────

echo "\n=== 2. Şifreleme / Çözme Testleri ===\n";

$pdo2 = buildPdo();

test('Şifreleme ve çözme döngüsü başarılı olmalı', function () use ($pdo2) {
    $mgr       = new EncryptionManager($pdo2);
    $plaintext = 'Gizli sağlık verisi: Diyabet';
    $encrypted = $mgr->encrypt($plaintext);
    $decrypted = $mgr->decrypt($encrypted);
    assert_equals($decrypted, $plaintext, 'Decrypt sonucu orijinalle eşleşmeli');
});

test('Farklı anahtarlarla şifrelenmiş veriler çözülmeli', function () use ($pdo2) {
    $mgr  = new EncryptionManager($pdo2);
    $enc1 = $mgr->encrypt('veri1');
    // Rotasyon yap (anahtar yaşı kontrolünü atlatmak için doğrudan DB'yi manipüle ediyoruz)
    $pdo2->exec("UPDATE encryption_keys SET status = 'rotated'");
    $enc2 = $mgr->encrypt('veri2');
    // Eski anahtar ile şifreli veri hâlâ çözülmeli
    $dec1 = $mgr->decrypt($enc1);
    $dec2 = $mgr->decrypt($enc2);
    assert_equals($dec1, 'veri1', 'Eski key ile şifreli veri çözülmeli');
    assert_equals($dec2, 'veri2', 'Yeni key ile şifreli veri çözülmeli');
});

test('Geçersiz payload exception fırlatmalı', function () use ($pdo2) {
    $mgr = new EncryptionManager($pdo2);
    $thrown = false;
    try {
        $mgr->decrypt('bozuk_veri!!');
    } catch (Throwable $e) {
        $thrown = true;
    }
    assert_true($thrown, 'Geçersiz payload için exception bekleniyor');
});

test('rotateIfNeeded() 90 günden genç anahtarı değiştirmemeli', function () use ($pdo2) {
    $mgr     = new EncryptionManager($pdo2);
    $before  = $mgr->getActiveKeyInfo();
    $rotated = $mgr->rotateIfNeeded();
    assert_true(!$rotated, '90 günden genç anahtarda rotasyon olmamalı');
    assert_equals($mgr->getActiveKeyInfo()['id'], $before['id'], 'Anahtar ID değişmemeli');
});

// ─── 3. Consent State Machine Testleri ────────────────────────────────────

echo "\n=== 3. Rıza (Consent) State Machine Testleri ===\n";

$pdo3 = buildPdo();

test('Rıza verilebilmeli ve kontrol edilebilmeli', function () use ($pdo3) {
    $cm = new ConsentManager($pdo3);
    $cm->giveConsent(10, ConsentManager::TYPE_MARKETING, '127.0.0.1');
    assert_true($cm->hasConsent(10, ConsentManager::TYPE_MARKETING));
});

test('Rıza iptal edilebilmeli', function () use ($pdo3) {
    $cm = new ConsentManager($pdo3);
    $cm->giveConsent(11, ConsentManager::TYPE_ANALYTICS, '127.0.0.1');
    $revoked = $cm->revokeConsent(11, ConsentManager::TYPE_ANALYTICS);
    assert_true($revoked);
    assert_true(!$cm->hasConsent(11, ConsentManager::TYPE_ANALYTICS));
});

test('Essential rıza iptal edilememeli', function () use ($pdo3) {
    $cm     = new ConsentManager($pdo3);
    $thrown = false;
    try {
        $cm->revokeConsent(12, ConsentManager::TYPE_ESSENTIAL);
    } catch (LogicException $e) {
        $thrown = true;
    }
    assert_true($thrown, 'Essential rızayı iptal etmeye çalışmak LogicException fırlatmalı');
});

test('Geçersiz consent tipi exception fırlatmalı', function () use ($pdo3) {
    $cm     = new ConsentManager($pdo3);
    $thrown = false;
    try {
        $cm->giveConsent(13, 'invalid_type');
    } catch (InvalidArgumentException $e) {
        $thrown = true;
    }
    assert_true($thrown);
});

test('getConsents() tüm tipleri döndürmeli', function () use ($pdo3) {
    $cm = new ConsentManager($pdo3);
    $cm->giveConsent(20, ConsentManager::TYPE_ESSENTIAL, '127.0.0.1');
    $consents = $cm->getConsents(20);
    assert_true(array_key_exists('essential', $consents));
    assert_true(array_key_exists('analytics', $consents));
    assert_true(array_key_exists('marketing', $consents));
    assert_true($consents['essential'] === true);
});

// ─── 4. Veri Silme Flow Testleri ───────────────────────────────────────────

echo "\n=== 4. Veri Silme Flow Testleri ===\n";

$pdo4 = buildPdo();

test('Silme talebi oluşturulabilmeli', function () use ($pdo4, $auditLogDir) {
    $audit    = new AuditLoggerKVKK($pdo4, $auditLogDir);
    $deletion = new DataDeletion($pdo4, $audit);
    $id       = $deletion->createRequest(50, 'Test silme');
    assert_true($id > 0);
});

test('Aynı kullanıcı için tekrar talep oluşturulunca mevcut ID dönmeli', function () use ($pdo4, $auditLogDir) {
    $audit    = new AuditLoggerKVKK($pdo4, $auditLogDir);
    $deletion = new DataDeletion($pdo4, $audit);
    $id1      = $deletion->createRequest(51, 'İlk talep');
    $id2      = $deletion->createRequest(51, 'İkinci talep');
    assert_equals($id1, $id2, 'Tekrar talep aynı ID döndürmeli');
});

test('Talep onaylanabilmeli', function () use ($pdo4, $auditLogDir) {
    $audit    = new AuditLoggerKVKK($pdo4, $auditLogDir);
    $deletion = new DataDeletion($pdo4, $audit);

    // Kullanıcı profili oluştur
    $pdo4->exec("INSERT INTO user_profiles (user_id, first_name, last_name) VALUES (60, 'Ali', 'Veli')");

    $requestId = $deletion->createRequest(60, 'Hesabımı sil');
    $approved  = $deletion->approveRequest($requestId, false, 1);
    assert_true($approved);
});

test('Talep reddedilebilmeli', function () use ($pdo4, $auditLogDir) {
    $audit    = new AuditLoggerKVKK($pdo4, $auditLogDir);
    $deletion = new DataDeletion($pdo4, $audit);
    $id       = $deletion->createRequest(70, 'Reddet beni');
    $rejected = $deletion->rejectRequest($id);
    assert_true($rejected);
});

test('30 günü geçmiş talep processExpiredRequests() ile işlenmeli', function () use ($pdo4, $auditLogDir) {
    $audit    = new AuditLoggerKVKK($pdo4, $auditLogDir);
    $deletion = new DataDeletion($pdo4, $audit);

    $oldDate = date('Y-m-d H:i:s', strtotime('-31 days'));
    $hash    = bin2hex(random_bytes(16));
    $pdo4->exec("INSERT INTO user_profiles (user_id, first_name, last_name) VALUES (80, 'Eski', 'Kullanici')");
    $pdo4->prepare(
        "INSERT INTO deletion_requests (user_id, reason, request_date, status, verification_hash) VALUES (80, 'otomatik', ?, 'pending', ?)"
    )->execute([$oldDate, $hash]);

    $count = $deletion->processExpiredRequests();
    assert_true($count >= 1, 'En az 1 talep işlenmeli');
});

// ─── Özet ────────────────────────────────────────────────────────────────────

echo "\n──────────────────────────────────────────\n";
echo "  Geçen: {$passed}  |  Başarısız: {$failed}\n";
echo "──────────────────────────────────────────\n\n";

exit($failed > 0 ? 1 : 0);
