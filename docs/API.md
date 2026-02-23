# API Sistemi Dokümantasyonu

Bu doküman, harici sistemlerle entegrasyona hazırlanan RESTful API yapısını açıklar.

## Temel Bilgiler
- **Base URL:** `/api`
- **Versiyon:** `v1`
- **Format:** `application/json`
- **Kimlik Doğrulama:** Şu an public + hazırlık modunda (ileride OAuth2/API Key)

## Endpoint'ler

### 1) Servis Durumu
- **GET** `/api/v1/status.php`
- Sağlık kontrolü ve versiyon bilgisi döner.

### 2) Sosyal Hak Uygunluk Ön Değerlendirme
- **POST** `/api/v1/rights-eligibility.php`
- İstek örneği:

```json
{
  "householdIncome": 15000,
  "householdMembers": 4,
  "minWage": 17002.12
}
```

### 3) Entegrasyon Hazırlık Durumu
- **GET** `/api/v1/integrations-readiness.php`
- SGK, e-Nabız, e-Devlet için planlanan operasyonları listeler.

## Harici Kurum Entegrasyon Tasarımı

### SGK
- Kullanım senaryoları:
  - Sigorta durumu doğrulama
  - Gelir/prim tabanı kontrolü
  - Emeklilik ön uygunluk kontrolü
- Önerilen auth: OAuth2 Client Credentials + IP whitelist

### e-Nabız
- Kullanım senaryoları:
  - Sağlık kurulu raporu doğrulama
  - Engellilik oranı kontrolü
- Önerilen auth: OAuth2 Authorization Code + kullanıcı rızası akışı

### e-Devlet
- Kullanım senaryoları:
  - Kimlik doğrulama
  - Belge/doğrulama kodu kontrolü
  - Hane/ikamet bilgisi teyidi
- Önerilen auth: OAuth2 + imzalı istek doğrulaması

## Güvenlik Notları
- Tüm API trafiği sadece HTTPS üzerinden servis edilmelidir.
- Rate-limit ve audit-log mekanizması eklenmelidir.
- KVKK kapsamında kişisel veri minimizasyonu zorunludur.

## cURL Örnekleri

```bash
curl -s http://localhost/api/v1/status.php
```

```bash
curl -s -X POST http://localhost/api/v1/rights-eligibility.php \
  -H "Content-Type: application/json" \
  -d '{"householdIncome":12000,"householdMembers":3}'
```
