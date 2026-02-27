# Otomasyon Sistemleri

Bu klasör, platform için istenen 3 temel otomasyon sisteminin örnek ve genişletilebilir PHP implementasyonlarını içerir:

## 31) E-Posta Otomasyon
Dosya: `email_automation.php`

Özellikler:
- Hoş geldin serileri (3 adımlı akış)
- Son tarih bazlı hatırlatma e-postaları
- Özel gün kampanyaları
- Kullanıcı segmentasyonu (yeni/aktif/pasif/başvurusu bekleyen)
- Şablon kişiselleştirme (`{name}`, `{segment}` vb.)

Örnek çalıştırma:
```bash
php automation/email_automation.php
```

## 32) Sosyal Medya Otomasyon
Dosya: `social_media_automation.php`

Özellikler:
- Haftalık içerik takvimi üretimi
- Platform bazlı otomatik paylaşım kuyruğu
- İçerik metnine göre hashtag optimizasyonu
- Engagement takibi (toplam ve platform bazlı oran)

Örnek çalıştırma:
```bash
php automation/social_media_automation.php
```

## 33) Yedekleme Otomasyonu
Dosya: `backup_automation.php`

Özellikler:
- Günlük veri yedekleme (JSON export)
- Dosya/klasör yedekleme
- Bulut depolama senkronizasyonu (hedef dizin simülasyonu)
- Yedekten geri yükleme

Örnek çalıştırma:
```bash
php automation/backup_automation.php
```

> Not: Bu yapılar gerçek üretim ortamında; kuyruk sistemi (RabbitMQ/Redis), SMTP servisleri, sosyal ağ API anahtar yönetimi ve gerçek cloud SDK (S3, GCS vb.) ile genişletilmelidir.
