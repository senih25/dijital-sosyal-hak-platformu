# KVKK Uyum Kılavuzu

Bu belge, Dijital Sosyal Hak Platformu'nun **6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK)** ve **GDPR** kapsamındaki teknik uyum durumunu açıklar.

---

## KVKK Madde-Madde Uyum Tablosu

| Madde | Başlık | Uygulama | Durum |
|-------|--------|----------|-------|
| Md. 3 | Tanımlar – Açık Rıza | `ConsentManager::giveConsent()` | ✅ |
| Md. 4 | Veri işleme ilkeleri | PDO prepared statements, input validation | ✅ |
| Md. 5 | Kişisel verilerin işlenme şartları | `consent_records` tablosu | ✅ |
| Md. 7 | Kişisel verilerin silinmesi | `DataDeletion::approveRequest()` | ✅ |
| Md. 10 | Aydınlatma yükümlülüğü | `kvkk-politikasi.php`, `gizlilik.php` | ✅ |
| Md. 11 | İlgili kişinin hakları (export/delete) | `DataExport`, `DataDeletion` | ✅ |
| Md. 12 | Veri güvenliği | AES-256-CBC, audit logs, key rotation | ✅ |

---

## Sistem Mimarisi

```
includes/
  audit_logger.php        – Değiştirilemez denetim kaydı (DB + dosya)
  encryption_manager.php  – AES-256-CBC + 90 günlük anahtar rotasyonu
  consent_manager.php     – KVKK/GDPR rıza state machine

modules/
  data_deletion.php       – 30 günlük oto-silme, soft/hard delete
  data_export.php         – GDPR uyumlu JSON export

admin/
  kvkk_dashboard.php      – Admin yönetim paneli

api/v1/
  export.php              – POST /api/v1/user/export
  delete_request.php      – POST /api/v1/user/delete-request
  consents.php            – GET  /api/v1/audit/consents
  revoke_consent.php      – POST /api/v1/user/revoke-consent
  helpers.php             – Rate limiting, JSON yardımcıları

tests/
  kvkk_compliance_test.php – 18 birim testi
```

---

## API Dokümantasyonu

### POST `/api/v1/user/export`

Kullanıcının tüm kişisel verilerini JSON formatında dışa aktarır.

**Rate Limit:** 3 istek / saat

**İstek:**
```json
{ "user_id": 123 }
```

**Yanıt (200):**
```json
{
  "success": true,
  "message": "Veri export başarıyla oluşturuldu.",
  "file": "export_user_123_1700000000.json",
  "download": "/storage/exports/export_user_123_1700000000.json"
}
```

---

### POST `/api/v1/user/delete-request`

Veri silme talebi oluşturur. Talep 30 gün içinde işlenir.

**Rate Limit:** 5 istek / saat

**İstek:**
```json
{ "user_id": 123, "reason": "Hesabımı kapatmak istiyorum" }
```

**Yanıt (200):**
```json
{
  "success": true,
  "message": "Silme talebiniz alındı. 30 gün içinde işlenecektir.",
  "request_id": 42
}
```

---

### GET `/api/v1/audit/consents`

Rıza kayıtlarını listeler.

**Rate Limit:** 30 istek / dakika

**Query Parametreleri:**
| Parametre | Tip | Açıklama |
|-----------|-----|----------|
| `user_id` | int | Opsiyonel – belirli kullanıcının geçmişi |
| `page`    | int | Sayfa numarası (varsayılan: 1) |
| `per_page`| int | Sayfa başına kayıt (varsayılan: 50, max: 100) |

**Yanıt (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 123,
      "consent_type": "marketing",
      "given_at": "2024-01-15 10:30:00",
      "revoked_at": null,
      "ip_address": "192.168.1.1"
    }
  ],
  "total": 1
}
```

---

### POST `/api/v1/user/revoke-consent`

Belirli bir rıza tipini iptal eder. `essential` tipi iptal edilemez.

**Rate Limit:** 10 istek / saat

**İstek:**
```json
{ "user_id": 123, "consent_type": "marketing" }
```

**Yanıt (200):**
```json
{
  "success": true,
  "revoked": true,
  "message": "Rıza başarıyla iptal edildi."
}
```

**Hata (422) – essential tipini iptal etmeye çalışırsa:**
```json
{ "success": false, "error": "Zorunlu (essential) rıza geri alınamaz." }
```

---

## Veritabanı Şemaları

```sql
-- Denetim Kaydı
CREATE TABLE audit_logs (
    id              BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NULL,
    action          VARCHAR(120) NOT NULL,
    details         TEXT NULL,
    ip_address      VARCHAR(45) NOT NULL DEFAULT '',
    user_agent      VARCHAR(500) NOT NULL DEFAULT '',
    result          ENUM('success','failure') NOT NULL DEFAULT 'success',
    encrypted_fields TEXT NULL,
    created_at      DATETIME NOT NULL
);

-- Şifreleme Anahtarları
CREATE TABLE encryption_keys (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    algorithm    VARCHAR(50) NOT NULL DEFAULT 'aes-256-cbc',
    key_material VARCHAR(255) NOT NULL,
    status       ENUM('active','rotated','revoked') NOT NULL DEFAULT 'active',
    created_at   DATETIME NOT NULL,
    rotated_at   DATETIME NULL
);

-- Rıza Kayıtları
CREATE TABLE consent_records (
    id           BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL,
    consent_type ENUM('essential','analytics','marketing') NOT NULL,
    given_at     DATETIME NOT NULL,
    revoked_at   DATETIME NULL,
    ip_address   VARCHAR(45) NOT NULL DEFAULT '',
    user_agent   VARCHAR(500) NOT NULL DEFAULT ''
);

-- Veri Silme Talepleri
CREATE TABLE deletion_requests (
    id                BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id           INT NOT NULL,
    reason            TEXT NULL,
    request_date      DATETIME NOT NULL,
    processed_date    DATETIME NULL,
    status            ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    verification_hash VARCHAR(64) NOT NULL
);
```

---

## Admin Workflow Rehberi

### Veri Silme Talebi İşleme

1. `admin/kvkk_dashboard.php` adresine gidin.
2. **Veri Silme Talepleri** bölümünden bekleyen talepleri inceleyin.
3. **Kalıcı sil** seçeneğini işaretleyin veya işaretsiz bırakın (soft delete / hard delete).
4. **Onayla** veya **Reddet** butonuna tıklayın.
5. İşlem, `audit_logs` tablosuna otomatik olarak kaydedilir.

### 30 Günlük Otomatik Silme (Cron)

```bash
# Crontab ayarı (her gün gece yarısı)
0 0 * * * php /path/to/project/cron/process_deletions.php
```

`cron/process_deletions.php` içeriği:
```php
<?php
require_once __DIR__ . '/../includes/audit_logger.php';
require_once __DIR__ . '/../modules/data_deletion.php';
// ... PDO bağlantısı kur ...
$deletion = new DataDeletion($pdo, $audit);
$count = $deletion->processExpiredRequests();
echo "İşlenen talep sayısı: {$count}\n";
```

### Anahtar Rotasyonu (Cron)

```bash
# Her hafta pazar günü
0 2 * * 0 php /path/to/project/cron/rotate_keys.php
```

---

## Veri İşleme Akış Diyagramı

```
Kullanıcı Kaydı
    │
    ▼
ConsentManager::giveConsent('essential')
    │
    ▼
UserProfileSystem::saveProfile()
    │  → Hassas alanlar şifrelenir (EncryptionManager)
    │  → AuditLoggerKVKK::log('kullanici_profili_guncellendi')
    │
    ▼
Veri İşleme
    │
    ├── Analytics/Marketing rıza varsa → ilgili işlemler
    │
    └── Kullanıcı "Verilerimi Sil" talebi
            │
            ▼
        DataDeletion::createRequest()
            │  → deletion_requests tablosuna yazılır
            │  → AuditLoggerKVKK::log('deletion_request_created')
            │
            ▼
        Admin onayı VEYA 30 gün sonra otomatik
            │
            ▼
        DataDeletion::approveRequest(hardDelete=true/false)
            │  → Soft: profil anonimleştirilir
            │  → Hard: cascade delete
            │  → AuditLoggerKVKK::log('deletion_request_approved')
```

---

## Güvenlik Notları

- **Şifreleme:** AES-256-CBC, her şifreleme için benzersiz IV kullanılır.
- **Anahtar Rotasyonu:** 90 günde bir otomatik, eski anahtarla şifreli veriler hâlâ çözülebilir.
- **Denetim Kaydı:** Veritabanı + dosya tabanlı hibrit; satırlar INSERT-only olmalı (DELETE/UPDATE kısıtlama için DB trigger eklenebilir).
- **Rate Limiting:** Tüm API endpoint'lerinde session tabanlı rate limit uygulanır.
- **CSRF:** Formlar için `SecurityManager::generateCSRFToken()` kullanın.
- **Input Validation:** `SecurityManager::sanitizeInput()` tüm kullanıcı girdilerine uygulanır.

---

## Test Çalıştırma

```bash
php tests/kvkk_compliance_test.php
```

Beklenen çıktı: 18 test, 0 başarısız.
