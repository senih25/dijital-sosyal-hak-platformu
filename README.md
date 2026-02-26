# 🌍 Dijital Sosyal Hak Platformu
### *Digital Social Rights Platform*

<div align="center">

**🇹🇷 Türkçe** | **🇬🇧 English**

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://mysql.com)
[![KVKK](https://img.shields.io/badge/KVKK-Uyumlu-green)](https://kvkk.gov.tr)
[![Open Source](https://img.shields.io/badge/Open%20Source-❤️-red)](https://github.com/senih25/dijital-sosyal-hak-platformu)

> **"Sosyal haklar bir lütuf değil, her bireyin doğuştan hakkıdır."**
> *"Social rights are not a privilege — they are a birthright."*

</div>

---

## 🎯 Misyon & Vizyon / Mission & Vision

### 🇹🇷 Türkçe

**Misyonumuz:** Türkiye'nin 81 ilinde yaşayan 850.000'den fazla bireyin sosyal haklarına dijital yollarla erişimini kolaylaştırmak; bilgiye erişim eşitsizliğini ortadan kaldırmak ve sosyal adaletin teknoloji aracılığıyla güçlendirilmesine öncülük etmek.

**Vizyonumuz:** Herkesin sosyal hak bilincine sahip olduğu, dijital araçların sosyal adalet için kullanıldığı ve akademik araştırmaların politika değişikliğine dönüştüğü sürdürülebilir bir dijital ekosistem inşa etmek.

### 🇬🇧 English

**Our Mission:** To empower 850,000+ individuals across all 81 provinces of Turkey with digital access to their social rights — eliminating information inequality and pioneering the use of technology for social justice.

**Our Vision:** To build a sustainable digital ecosystem where everyone is aware of their social rights, digital tools serve social justice, and academic research translates into policy change.

---

## 📊 Rakamlarla Platform / Platform at a Glance

| Metrik / Metric | Değer / Value |
|---|---|
| 🏙️ Hedef İl / Target Provinces | **81 il / provinces** |
| 👥 Hedef Kullanıcı / Target Users | **850.000+** |
| ⚖️ Sosyal Hak & Mevzuat / Social Rights & Legislation | **71+** |
| 📚 E-Learning Modülleri / E-Learning Modules | **Kurs + Video + Test + Sertifika** |
| 🤝 Danışmanlık Uzmanı / Consulting Experts | **Sosyal çalışmacı, avukat, akademisyen** |
| 🔬 Araştırma Ortakları / Research Partners | **Anonim veri + Akademik kuruluşlar** |
| 🔓 Lisans / License | **Açık Kaynak / Open Source** |

---

## 🏗️ Proje Ekosistemi / Project Ecosystem

Dijital Sosyal Hak Platformu dört ana sütun üzerine inşa edilmiştir:

*The Digital Social Rights Platform is built on four core pillars:*

### 📚 1. Eğitim / E-Learning

> Bilgiye erişim, değişimin ilk adımıdır. / *Access to knowledge is the first step to change.*

- **Kurs Sistemi:** Yapılandırılmış müfredat ile sosyal hak eğitimleri
- **Video İçerikler:** Uzman tarafından hazırlanmış açıklayıcı videolar
- **Testler & Değerlendirme:** Bilgi pekiştirme sınavları
- **Sertifikalar:** Tamamlama belgesi ile kariyer desteği
- **Çevrimdışı Erişim:** PWA desteği ile internet bağlantısı olmadan kullanım

### 👥 2. Canlı Danışmanlık / Live Consulting

> Her sorunun bir uzmanı vardır. / *Every problem has an expert.*

- **WebSocket Teknolojisi:** Gerçek zamanlı mesajlaşma ve destek
- **Uzman Eşleştirme:** Soruna uygun uzmanla akıllı eşleştirme algoritması
- **Video Görüşme:** Entegre video konferans desteği
- **Randevu Sistemi:** Esnek zamanlama ve takvim yönetimi
- **Gizlilik Koruması:** KVKK uyumlu oturum yönetimi

### 🎥 3. Canlı Yayın / Webinar & Live Stream

> Bilgi yayılır, etki büyür. / *Knowledge spreads, impact grows.*

- **YouTube Live Entegrasyonu:** Geniş kitlelere ulaşan yayınlar
- **Canlı Anket (Polling):** Katılımcılardan anlık geri bildirim
- **Katılım Takibi:** Otomatik devam kaydı ve raporlama
- **Soru-Cevap Oturumları:** Moderatörlü interaktif Q&A
- **Kayıt Arşivi:** Tüm yayınların erişilebilir dijital kütüphanesi

### 🔬 4. Araştırma / Research

> Veri, politikayı şekillendirir. / *Data shapes policy.*

- **Anonim Veri Analizi:** KVKK uyumlu gizlilik korumalı araştırma
- **Akademik Ortaklıklar:** Üniversiteler ve düşünce kuruluşları ile iş birliği
- **Politika Etki Raporları:** Karar vericilere yönelik veri odaklı raporlar
- **Trend Analizi:** Sosyal hak erişim desenlerinin haritalanması
- **Açık Veri Seti:** Araştırmacılar için anonim veri paylaşımı

---

## 🛠️ Teknik Mimari / Technical Architecture

### 🇹🇷 Türkçe

```
┌─────────────────────────────────────────────────────────────┐
│                    KULLANICI KATMANI                         │
│          Web (Bootstrap 5) · PWA · Mobil Uyumlu             │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                    API KATMANI                               │
│        RESTful API · WebSocket · JWT Auth                    │
└──────┬──────────────┬──────────────┬──────────────┬─────────┘
       │              │              │              │
  ┌────▼────┐   ┌────▼────┐   ┌────▼────┐   ┌────▼────┐
  │ E-Learn │   │ Danış.  │   │ Webinar │   │Araştırma│
  │ Modülü  │   │ Modülü  │   │ Modülü  │   │ Modülü  │
  └────┬────┘   └────┬────┘   └────┬────┘   └────┬────┘
       └──────────────┴──────────────┴──────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                   VERİTABANI KATMANI                         │
│           MySQL · Redis Cache · Anonim Araştırma DB          │
└─────────────────────────────────────────────────────────────┘
```

### Temel Özellikler

| Özellik | Teknoloji |
|---|---|
| **Backend** | PHP 7.4+ (Microservices-ready) |
| **Veritabanı** | MySQL 5.7+ / MariaDB |
| **Frontend** | Bootstrap 5, HTML5, CSS3, JavaScript |
| **Gerçek Zamanlı** | WebSocket |
| **E-posta** | PHPMailer (SMTP) |
| **Güvenlik** | KVKK uyumlu, AES-256-CBC şifreleme, CSRF, PDO |
| **Ölçeklendirme** | K8s-ready, Microservices mimarisi |
| **CDN & Cache** | Redis, Varnish desteği |
| **İzleme** | Datadog APM entegrasyonu |
| **Feature Flags** | DevCycle entegrasyonu |
| **Çok Dil** | TR / EN / AR (POEditor) |

### 🔐 Güvenlik & Uyumluluk

- ✅ **KVKK Uyumlu** veri işleme ve kullanıcı rızası yönetimi
- ✅ **SQL Injection Koruması** — PDO prepared statements
- ✅ **XSS Koruması** — `htmlspecialchars` ile çıktı temizleme
- ✅ **CSRF Koruması** — Token tabanlı form doğrulaması
- ✅ **Şifre Güvenliği** — `password_hash` / `password_verify`
- ✅ **AES-256-CBC** ile hassas veri şifreleme
- ✅ **Session Güvenliği** — Güvenli çerez yönetimi
- ✅ **Sosyal Hak Uygunluk Algoritması** — Şeffaf hesaplama formülleri

---

## 📈 Sosyal Etki Metrikleri / Social Impact Metrics

Platformun başarısını dört boyutta ölçüyoruz:

*We measure the platform's success across four dimensions:*

### 🗺️ Erişim / Access
- 80+ ilde aktif kullanıcı varlığı
- Kırsal ve kentsel nüfusa eşit erişim
- Çok dilli destek (TR/EN/AR)

### 📖 Eğitim / Education
- Kurs tamamlanma oranı (hedef: %75+)
- Sertifika kazanım sayısı
- Kullanıcı başına öğrenme saati

### 🤝 Danışmanlık / Consulting
- Oturum başarı oranı
- Ortalama çözüm süresi
- Kullanıcı memnuniyet skoru (NPS)

### 🏛️ Araştırma & Politika / Research & Policy
- Paylaşılan anonim veri seti sayısı
- Akademik yayın sayısı
- Politika değişikliğine katkı sağlanan alan sayısı

---

## 🤝 Katılım Yolları / How to Contribute

Dijital Sosyal Hak Platformu açık kaynak bir girişimdir. Her arka plandan katkıya açığız:

*The Digital Social Rights Platform is an open source initiative. We welcome contributions from all backgrounds:*

### 👨‍💻 Geliştiriciler / Developers
```
- Backend: PHP, MySQL, API geliştirme
- Frontend: Bootstrap, JavaScript, PWA
- DevOps: Docker, K8s, CI/CD pipeline
- Güvenlik: Penetrasyon testi, kod incelemesi
```

### 👩‍⚕️ Sosyal Çalışmacılar / Social Workers
```
- Alan uzmanlığı: Sosyal hak içerik doğrulama
- Kullanıcı araştırması: Gerçek kullanıcı geri bildirimi
- Vaka çalışmaları: Anonim başarı hikayeleri
```

### ⚖️ Avukatlar / Lawyers
```
- Mevzuat güncellemeleri: Yeni yasa ve yönetmelik takibi
- Hukuki içerik: Platform rehberlik içeriklerinin doğruluğu
- Danışmanlık havuzu: Canlı hukuki danışmanlık desteği
```

### 🔬 Araştırmacılar / Researchers
```
- Veri bilimi: Anonim veri analizi ve görselleştirme
- Akademik ortaklık: Araştırma projesi iş birlikleri
- Politika araştırması: Veri odaklı politika önerileri
```

**Katkıda bulunmak için:** [CONTRIBUTING.md](CONTRIBUTING.md) dosyasını inceleyin veya bir [Issue](https://github.com/senih25/dijital-sosyal-hak-platformu/issues) açın.

---

## 💰 Ekonomik Model & Sürdürülebilirlik / Economic Model & Sustainability

Platform, uzun vadeli sürdürülebilirlik için çeşitlendirilmiş bir gelir modeline sahiptir:

*The platform uses a diversified revenue model for long-term sustainability:*

| Kaynak / Source | Model | Açıklama / Description |
|---|---|---|
| 🏛️ **Kamu Kontratları** | B2G | Belediye ve bakanlıklarla sosyal hizmet dijitalleştirme |
| 📚 **E-Learning** | Freemium | Temel ücretsiz, premium içerik ücretli |
| 👥 **Uzman Danışmanlık** | Komisyon | Platform aracılık ücreti |
| 🎓 **Kurumsal Eğitim** | B2B | Üniversite ve STK'lara özel eğitim paketi |
| 🤝 **Sponsorluklar** | CSR | Kurumsal sosyal sorumluluk ortaklıkları |
| 🔬 **Araştırma Hibeleri** | Grant | TÜBİTAK, AB, uluslararası fon kuruluşları |

> 💡 **Sürdürülebilirlik İlkesi:** Platformun temel sosyal hak rehberliği her zaman **ücretsiz** kalacaktır.
> *The platform's core social rights guidance will always remain **free**.*

---

## 🚀 Hızlı Başlangıç / Quick Start

### 🐳 Docker ile (Önerilen / Recommended)

```bash
# Repoyu klonlayın / Clone the repository
git clone https://github.com/senih25/dijital-sosyal-hak-platformu.git
cd dijital-sosyal-hak-platformu

# Ortam değişkenlerini ayarlayın / Set up environment variables
cp .env.example .env
# .env dosyasını kendi değerlerinizle doldurun / Fill in your values

# Docker ile çalıştırın / Run with Docker
docker-compose up -d

# Tarayıcıda açın / Open in browser
# http://localhost:8080
```

### 🖥️ Yerel Geliştirme / Local Development (XAMPP)

```bash
# Gereksinimler / Requirements:
# - XAMPP (PHP 7.4+ ve MySQL 5.7+)
# - Web tarayıcısı / Web browser
# - VS Code (önerilen / recommended)

# 1. XAMPP'i başlatın (Apache + MySQL)
# 2. Projeyi htdocs'a kopyalayın
cp -r dijital-sosyal-hak-platformu/ C:\xampp\htdocs\

# 3. Yapılandırmayı güncelleyin / Update config
cp config/config.example.php config/config.php
# config/config.php içinde SITE_URL'yi güncelleyin

# 4. Veritabanını başlatın / Initialize database
# phpMyAdmin → Yeni DB: sosyal_hizmet_db (utf8mb4_turkish_ci)
# Import: database.sql

# 5. Siteye erişin / Access the site
# http://localhost/dijital-sosyal-hak-platformu
```

### 🗄️ Veritabanı Başlatma / Database Initialization

```bash
# phpMyAdmin veya MySQL CLI ile / via phpMyAdmin or MySQL CLI
mysql -u root -p sosyal_hizmet_db < database.sql
mysql -u root -p sosyal_hizmet_db < data_management_schema.sql
```

### 🧪 Test Verisi / Test Data

| Rol / Role | E-posta / Email | Şifre / Password |
|---|---|---|
| Admin | admin@sosyalhizmet.com | admin123 |
| Kullanıcı / User | musteri@test.com | musteri123 |

> ⚠️ **ÖNEMLİ / IMPORTANT:** Canlı yayına almadan önce tüm varsayılan şifreleri değiştirin!
> *Change all default passwords before going live!*

### 🔧 Bağımlılıklar / Dependencies

```bash
# PHP bağımlılıkları / PHP dependencies
composer install

# Ortam değişkenleri (üretim) / Environment variables (production)
# Doppler kullanımı önerilir / Doppler is recommended for secrets
```

---

## 🗺️ Yol Haritası / Roadmap

### 📅 2026

#### 🌱 Faz 1: Soft Launch (Q1-Q2 2026)
- [ ] Temel platform tamamlama (E-Learning + Danışmanlık)
- [ ] 10 pilot il lansmanı
- [ ] **50.000 kullanıcı** hedefi
- [ ] İlk akademik araştırma ortaklıkları
- [ ] Beta test ve kullanıcı geri bildirim döngüsü

#### 🌿 Faz 2: Bölgesel Genişleme (Q3-Q4 2026)
- [ ] Webinar modülü tam lansman
- [ ] 30 ile genişleme
- [ ] **200.000 kullanıcı** hedefi
- [ ] Çok dilli destek aktivasyonu (EN/AR)
- [ ] İlk kamu ortaklığı kontratı

### 📅 2027

#### 🌳 Faz 3: Ulusal Kapsam (Q1-Q2 2027)
- [ ] 81 ilin tamamına erişim
- [ ] **500.000 kullanıcı** hedefi
- [ ] Araştırma veri merkezi aktivasyonu
- [ ] Politika etki raporu yayını
- [ ] Mobil uygulama lansmanı (iOS + Android)

#### 🌲 Faz 4: Ekosistem Olgunluğu (Q3-Q4 2027)
- [ ] **850.000+ kullanıcı** hedefi
- [ ] Uluslararası model paylaşımı
- [ ] Açık veri seti yayını (anonim)
- [ ] Sürdürülebilir gelir modeli aktivasyonu
- [ ] Sonraki nesil platform geliştirme

---

## 📁 Proje Yapısı / Project Structure

```
dijital-sosyal-hak-platformu/
│
├── 📂 admin/                   # Admin paneli / Admin panel
│   └── includes/               # Admin header/footer
│
├── 📂 api/                     # RESTful API uç noktaları
├── 📂 app/                     # Uygulama çekirdeği / App core
├── 📂 config/                  # Yapılandırma dosyaları
│   ├── config.php              # Ana yapılandırma
│   ├── database.php            # Veritabanı bağlantısı
│   ├── doppler.php             # Gizli değer yönetimi
│   ├── devcycle.php            # Feature flags
│   ├── datadog.php             # APM izleme
│   └── localization.php       # Çok dilli destek
│
├── 📂 core/                    # Temel framework sınıfları
├── 📂 includes/                # Ortak bileşenler (header, footer)
├── 📂 modules/                 # Özellik modülleri (E-Learn, Danışm., vb.)
├── 📂 lang/                    # Dil dosyaları (TR/EN/AR)
├── 📂 docs/                    # Detaylı dokümantasyon
├── 📂 tests/                   # Test suite
├── 📂 automation/              # CI/CD ve otomasyon betikleri
│
├── index.php                   # Ana sayfa
├── database.sql                # Temel veritabanı şeması
├── data_management_schema.sql  # Analitik veri şeması
├── docker-compose.yml          # Docker yapılandırması
├── .env.example                # Ortam değişkeni şablonu
└── README.md                   # Bu dosya / This file
```

---

## 🔌 Entegrasyonlar / Integrations

| Entegrasyon | Amaç / Purpose | Dokümantasyon |
|---|---|---|
| **Doppler** | Gizli değer yönetimi / Secrets management | [DOPPLER_SETUP.md](docs/DOPPLER_SETUP.md) |
| **POEditor** | Çok dilli destek (TR/EN/AR) | [POEDITOR_SETUP.md](docs/POEDITOR_SETUP.md) |
| **Datadog** | APM izleme / Performance monitoring | [docs/](docs/) |
| **DevCycle** | Özellik bayrakları / Feature flags | [DEVCYCLE_SETUP.md](docs/DEVCYCLE_SETUP.md) |
| **YouTube Live** | Webinar yayını / Webinar streaming | API entegrasyonu |
| **PHPMailer** | E-posta bildirimleri / Email notifications | [EMAIL_SETUP.md](docs/) |

---

## 🐛 Sorun Giderme / Troubleshooting

<details>
<summary>Veritabanı Bağlantı Hatası / Database Connection Error</summary>

```
✅ MySQL servisinin çalıştığından emin olun
✅ config/database.php bilgilerini kontrol edin
✅ Veritabanı adı: sosyal_hizmet_db
✅ Collation: utf8mb4_turkish_ci
```
</details>

<details>
<summary>Türkçe Karakter Sorunu / Turkish Character Issue</summary>

```
✅ Veritabanı collation: utf8mb4_turkish_ci
✅ PHP dosyaları UTF-8 encoding ile kaydedilmeli
✅ config/config.php içinde charset kontrolü yapın
```
</details>

<details>
<summary>E-posta Gönderilmiyor / Email Not Sending</summary>

```
✅ config/email.php içindeki SMTP bilgilerini kontrol edin
✅ Gmail için Uygulama Şifresi oluşturun (2FA gerekli)
✅ Firewall SMTP portlarını (587/465) engellemiyor mu kontrol edin
```

Detaylı bilgi: `EMAIL_SETUP.md`
</details>

<details>
<summary>404 Sayfa Bulunamadı / 404 Not Found</summary>

```
✅ Apache mod_rewrite modülünün aktif olduğundan emin olun
✅ .htaccess dosyasının mevcut olduğunu kontrol edin
✅ SITE_URL yapılandırmasını kontrol edin
```
</details>

---

## 📄 Lisans / License

Bu proje **MIT Lisansı** ile lisanslanmıştır — açık kaynak, özgür kullanım, sosyal etki odaklı.

*This project is licensed under the **MIT License** — open source, free to use, focused on social impact.*

Temel sosyal hak rehberliği her zaman ücretsiz kalacaktır. / *Core social rights guidance will always remain free.*

---

## 🙏 Teşekkürler / Acknowledgements

Bu platform aşağıdaki kişi ve kuruluşların katkılarıyla hayat bulmaktadır:

*This platform comes to life through the contributions of the following people and organizations:*

- 👩‍⚕️ Sahada çalışan sosyal hizmet uzmanları / Social workers in the field
- ⚖️ Sosyal haklar üzerine çalışan avukatlar / Lawyers working on social rights
- 🎓 Akademik araştırmacılar / Academic researchers
- 💻 Açık kaynak geliştiriciler / Open source developers
- 🏛️ Hizmetlerin ulaşmasını sağlayan kamu kurumları / Public institutions enabling service delivery

---

<div align="center">

**"Teknoloji, sosyal adalet için en güçlü araçlardan biridir."**
*"Technology is one of the most powerful tools for social justice."*

[![GitHub Stars](https://img.shields.io/github/stars/senih25/dijital-sosyal-hak-platformu?style=social)](https://github.com/senih25/dijital-sosyal-hak-platformu)
[![GitHub Forks](https://img.shields.io/github/forks/senih25/dijital-sosyal-hak-platformu?style=social)](https://github.com/senih25/dijital-sosyal-hak-platformu/fork)

**Son Güncelleme / Last Updated:** Şubat 2026 / February 2026
**Versiyon / Version:** 2.0.0

📧 destek@sosyalhizmet.com | 🌐 [GitHub](https://github.com/senih25/dijital-sosyal-hak-platformu)

</div>
