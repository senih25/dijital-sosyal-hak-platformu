# 🏥 Sosyal Hizmet Rehberlik & Danışmanlık# Sosyal Hizmet Rehberlik & Danışmanlık Web Sitesi



Türkiye'de yaşayan bireylere sosyal haklar, engelli hakları, gelir testleri ve danışmanlık hizmetleri sunan kapsamlı bir web platformu.## 🚀 Kurulum Talimatları



---### Gereksinimler

- **XAMPP** (PHP 7.4+ ve MySQL 5.7+)

## 📋 İçindekiler- Web tarayıcısı

- Metin editörü (Visual Studio Code önerilir)

- [Özellikler](#-özellikler)

- [Teknolojiler](#-teknolojiler)### Adım 1: XAMPP Kurulumu

- [Kurulum](#-kurulum)1. XAMPP'i indirin ve kurun: https://www.apachefriends.org/

- [Veritabanı Yapılandırması](#-veritabanı-yapılandırması)2. XAMPP Control Panel'i açın

- [Kullanım](#-kullanım)3. **Apache** ve **MySQL** servislerini başlatın

- [E-posta Yapılandırması](#-e-posta-yapılandırması)

- [Giriş Bilgileri](#-giriş-bilgileri)### Adım 2: Projeyi Yerleştirme

- [Önemli Notlar](#-önemli-notlar)1. Bu projeyi `C:\xampp\htdocs\` klasörüne kopyalayın

2. Proje yolu: `C:\xampp\htdocs\sosyal-hizmet-rehberlik\`

---

### Adım 3: Veritabanı Oluşturma

## ✨ Özellikler1. Tarayıcınızda şu adrese gidin: http://localhost/phpmyadmin

2. Sol menüden **"New"** (Yeni) butonuna tıklayın

### 👤 Kullanıcı Paneli3. Veritabanı adı: `sosyal_hizmet_db`

- ✅ Kayıt ve giriş sistemi4. Collation: `utf8mb4_turkish_ci`

- ✅ Profil yönetimi ve şifre değiştirme5. **Create** (Oluştur) butonuna tıklayın

- ✅ Şifremi unuttum (e-posta ile sıfırlama)6. Oluşturulan veritabanına tıklayın

- ✅ Sipariş geçmişi görüntüleme7. Üst menüden **Import** (İçe Aktar) sekmesine gidin

- ✅ Gelir testi hesaplama aracı8. **Choose File** butonuna tıklayıp `database.sql` dosyasını seçin

- ✅ Balthazard formülü ile engel oranı hesaplama9. **Go** (Git) butonuna tıklayın



### 👨‍💼 Admin Paneli### Adım 4: Yapılandırma

- ✅ Kullanıcı yönetimi (görüntüleme, düzenleme, silme)1. `config/database.php` dosyasını açın

- ✅ Sipariş yönetimi (oluşturma, düzenleme, durum güncelleme)2. Veritabanı bilgilerini kontrol edin:

- ✅ Ödeme kayıtları (ekleme, görüntüleme)```php

- ✅ Fatura yönetimi (oluşturma, hesaplama)define('DB_HOST', 'localhost');

- ✅ Ürün/Paket yönetimidefine('DB_USER', 'root');

- ✅ Hizmet yönetimidefine('DB_PASS', '');

- ✅ Kategori yönetimidefine('DB_NAME', 'sosyal_hizmet_db');

- ✅ İçerik yönetimi (blog, rehber içerikleri)```

- ✅ Duyuru yönetimi (e-posta bildirimleri ile)3. Gerekirse şifre kısmını XAMPP ayarlarınıza göre güncelleyin

- ✅ Mesaj kutusu

- ✅ Sosyal medya ayarları### Adım 5: Site URL Ayarı

1. `config/config.php` dosyasını açın

### 🧮 Hesaplama Araçları2. `SITE_URL` değerini kontrol edin:

- ✅ **Gelir Testi:** 2024 asgari ücret bazlı hane geliri hesaplama```php

- ✅ **Balthazard Hesaplama:** Çoklu engel oranlarını birleştirme formülüdefine('SITE_URL', 'http://localhost/sosyal-hizmet-rehberlik');

```

### 📧 E-posta Sistemi

- ✅ Şifre sıfırlama e-postaları### Adım 6: Klasör İzinleri

- ✅ Hoş geldin e-postalarıAşağıdaki klasörlerin yazma iznine sahip olduğundan emin olun:

- ✅ Sipariş onay e-postaları- `uploads/`

- ✅ Duyuru bildirimleri (toplu e-posta)- `uploads/products/`

- `uploads/contents/`

---- `uploads/users/`



## 🛠 TeknolojilerWindows'ta bu klasörleri manuel oluşturun veya site ilk çalıştığında otomatik oluşturulacaktır.



- **Backend:** PHP 7.4+### Adım 7: Siteye Erişim

- **Veritabanı:** MySQL 5.7+ / MariaDBTarayıcınızda şu adreslere gidin:

- **Frontend:** Bootstrap 5, HTML5, CSS3, JavaScript

- **E-posta:** PHPMailer (SMTP)#### Ana Site

- **Sunucu:** Apache (XAMPP)```

- **Karakter Seti:** UTF-8 (Türkçe desteği)http://localhost/sosyal-hizmet-rehberlik

```

---

#### Admin Paneli

## 📥 Kurulum```

http://localhost/sosyal-hizmet-rehberlik/admin

### 1️⃣ Gereksinimler```



- XAMPP (PHP 7.4+ ve MySQL 5.7+)**Varsayılan Admin Girişi:**

- Web tarayıcısı (Chrome, Firefox, Edge önerilir)- E-posta: `admin@sosyalhizmet.com`

- Metin editörü (VS Code önerilir)- Şifre: `admin123`



### 2️⃣ XAMPP Kurulumu## 📁 Proje Yapısı



1. XAMPP'i indirin: https://www.apachefriends.org/```

2. Kurulumu tamamlayınsosyal-hizmet-rehberlik/

3. **XAMPP Control Panel**'i açın│

4. **Apache** ve **MySQL** servislerini başlatın├── admin/                      # Admin paneli

│   ├── includes/              # Admin header/footer

### 3️⃣ Projeyi Yerleştirme│   └── index.php              # Admin ana sayfa

│

```bash├── assets/                     # Statik dosyalar

# Projeyi XAMPP htdocs klasörüne kopyalayın│   ├── css/                   # CSS dosyaları

C:\xampp\htdocs\sosyal-hizmet-rehberlik\│   ├── js/                    # JavaScript dosyaları

```│   └── images/                # Görseller

│

### 4️⃣ Yapılandırma Dosyaları├── config/                     # Yapılandırma dosyaları

│   ├── config.php             # Ana config

#### config/database.php│   └── database.php           # Veritabanı bağlantısı

```php│

define('DB_HOST', 'localhost');├── includes/                   # Ortak dosyalar

define('DB_USER', 'root');│   ├── header.php             # Site başlığı

define('DB_PASS', ''); // XAMPP varsayılan şifre boş│   ├── footer.php             # Site alt bilgisi

define('DB_NAME', 'sosyal_hizmet_db');│   └── functions.php          # Yardımcı fonksiyonlar

define('DB_CHARSET', 'utf8mb4');│

```├── user/                       # Kullanıcı paneli

│   └── dashboard.php          # Kullanıcı ana sayfa

#### config/config.php│

```php├── uploads/                    # Yüklenen dosyalar

define('SITE_URL', 'http://localhost/sosyal-hizmet-rehberlik');│

define('SITE_NAME', 'Sosyal Hizmet Rehberlik');├── index.php                   # Ana sayfa

```├── hizmetlerimiz.php          # Hizmetler sayfası

├── rehberlik.php              # Sosyal hak rehberliği

---├── iletisim.php               # İletişim sayfası

├── login.php                  # Giriş sayfası

## 🗄 Veritabanı Yapılandırması├── logout.php                 # Çıkış işlemi

├── kvkk.php                   # KVKK metni

### Adım 1: phpMyAdmin'e Giriş├── database.sql               # Veritabanı SQL dosyası

```├── .htaccess                  # Apache yapılandırması

http://localhost/phpmyadmin└── README.md                  # Bu dosya

``````



### Adım 2: Veritabanı Oluşturma## 🎯 Özellikler

1. Sol menüden **"New"** butonuna tıklayın

2. **Veritabanı adı:** `sosyal_hizmet_db`### Kullanıcı Tarafı

3. **Collation:** `utf8mb4_turkish_ci` (Türkçe karakter desteği için önemli!)✅ Ana sayfa ile hero section

4. **Create** butonuna tıklayın✅ 3 hesaplama aracı (Gelir testi, Engel oranı, Sosyal haklar)

✅ Hızlı yardım arama

### Adım 3: SQL Dosyasını İçe Aktarma✅ Hizmetler ve e-kitap mağazası

1. Oluşturduğunuz `sosyal_hizmet_db` veritabanına tıklayın✅ Blog, mevzuat ve akademik içerikler

2. Üst menüden **Import** sekmesine gidin✅ Filtreleme ve arama özellikleri

3. **Choose File** butonuna tıklayın✅ İletişim formu (KVKK uyumlu)

4. `database.sql` dosyasını seçin✅ Responsive tasarım

5. **Go** butonuna tıklayın✅ WhatsApp entegrasyonu

6. ✅ "Import has been successfully finished" mesajını görmelisiniz✅ Sosyal medya bağlantıları



### Veritabanı Tabloları (Toplam 16 tablo)### Admin Paneli

- ✅ users (kullanıcılar)✅ Dashboard (istatistikler)

- ✅ products (ürünler/paketler)✅ Kullanıcı yönetimi

- ✅ orders (siparişler)✅ İçerik yönetimi (Blog, mevzuat, akademik)

- ✅ order_items (sipariş kalemleri)✅ Kategori yönetimi

- ✅ payments (ödemeler)✅ Hizmet yönetimi

- ✅ invoices (faturalar)✅ Ürün (e-kitap) yönetimi

- ✅ services (hizmetler)✅ Sipariş yönetimi

- ✅ categories (kategoriler)✅ Mesaj yönetimi

- ✅ contents (içerikler)✅ Duyuru yönetimi

- ✅ announcements (duyurular)✅ Site ayarları

- ✅ messages (mesajlar)

- ✅ settings (ayarlar)### Kullanıcı Paneli

- ✅ social_media (sosyal medya linkleri)✅ Profil yönetimi

- ✅ password_resets (şifre sıfırlama tokenları)✅ Sipariş geçmişi

- ✅ calculations (hesaplama kayıtları)✅ Kayıtlı hesaplamalar

- ✅ contact_messages (iletişim mesajları)✅ Güvenli oturum yönetimi



---## 🔐 Güvenlik



## 🚀 Kullanım- CSRF koruması

- SQL Injection koruması (PDO prepared statements)

### Siteye Erişim- XSS koruması (htmlspecialchars)

```- Şifre hashleme (password_hash)

http://localhost/sosyal-hizmet-rehberlik- KVKK uyumlu veri toplama

```- Session güvenliği



### Admin Paneline Erişim## 🛠️ Özelleştirme

```

http://localhost/sosyal-hizmet-rehberlik/admin### Site Ayarlarını Değiştirme

```1. Admin paneline giriş yapın

2. **Ayarlar** menüsüne gidin

---3. İletişim bilgileri, sosyal medya linkleri vb. güncelleyin



## 🔐 Giriş Bilgileri### Logo ve Tasarım

- Logo: `assets/images/` klasörüne ekleyin

### 👨‍💼 Admin Hesabı- Renkler: `assets/css/style.css` dosyasındaki CSS değişkenlerini düzenleyin

- **E-posta:** admin@sosyalhizmet.com- İkonlar: Font Awesome kullanılmaktadır

- **Şifre:** admin123

### E-posta Ayarları

### 👤 Test Müşteri Hesabı`includes/functions.php` dosyasındaki `sendEmail()` fonksiyonunu SMTP ayarları ile güncelleyin.

- **E-posta:** musteri@test.com

- **Şifre:** musteri123## 📧 Destek



> ⚠️ **ÖNEMLİ:** Canlı yayına almadan önce admin şifresini mutlaka değiştirin!Herhangi bir sorun yaşarsanız:

- E-posta: admin@sosyalhizmet.com

---- Veritabanı hatalarını `config/config.php` içinde hata raporlamayı açarak kontrol edin



## 📧 E-posta Yapılandırması## 📝 Lisans



E-posta sistemi şifre sıfırlama ve duyuru bildirimleri için gereklidir.Bu proje eğitim ve ticari kullanım için geliştirilmiştir.



### Gmail ile SMTP Kurulumu (Önerilen)## 🎉 Başarılar!



#### 1️⃣ Gmail Uygulama Şifresi Oluşturma:Site başarıyla kuruldu! Güvenli ve kullanışlı bir platform oluşturdunuz.



1. Google hesabınıza gidin: https://myaccount.google.com/security---

2. **2 Adımlı Doğrulama**'yı etkinleştirin (zorunlu)

3. **Uygulama şifreleri** bölümünü bulun**Not:** Production ortamına almadan önce:

4. "Sosyal Hizmet Rehberlik" adıyla uygulama şifresi oluşturun1. Hata raporlamayı kapatın (`config/config.php`)

5. Oluşturulan 16 haneli şifreyi kopyalayın2. Veritabanı şifresi belirleyin

3. SSL sertifikası kurun (HTTPS)

#### 2️⃣ config/email.php Düzenleme:4. `.htaccess` dosyasındaki güvenlik ayarlarını aktif edin

5. Varsayılan admin şifresini değiştirin

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'sizin-email@gmail.com'); // Değiştirin
define('SMTP_PASSWORD', 'xxxx xxxx xxxx xxxx'); // Uygulama şifresi
define('SMTP_FROM_EMAIL', 'sizin-email@gmail.com');
define('SMTP_FROM_NAME', 'Sosyal Hizmet Rehberlik');
```

### Diğer E-posta Sağlayıcıları

**Yandex Mail:**
```php
define('SMTP_HOST', 'smtp.yandex.com');
define('SMTP_PORT', 465);
```

**Outlook/Hotmail:**
```php
define('SMTP_HOST', 'smtp-mail.outlook.com');
define('SMTP_PORT', 587);
```

Detaylı bilgi için `EMAIL_SETUP.md` dosyasına bakın.

---

## ⚙️ Önemli Notlar

### 📁 Klasör İzinleri

Aşağıdaki klasörlerin yazma izni olmalı (dosya yükleme için):
```
uploads/contents/
uploads/products/
uploads/users/
```

### 🔒 Güvenlik Tavsiyeleri

1. ✅ Canlıya almadan **admin şifresini değiştirin**
2. ✅ `config/database.php` dosyasına güçlü şifre koyun
3. ✅ Test kullanıcılarını silin veya şifrelerini değiştirin
4. ✅ HTTPS kullanın (SSL sertifikası)
5. ✅ PHP hata mesajlarını kapatın (production'da)

### 🌐 Canlı Sunucuya Taşıma

1. Tüm dosyaları FTP ile sunucuya yükleyin
2. phpMyAdmin'de yeni veritabanı oluşturun
3. `database.sql` dosyasını içe aktarın
4. `config/database.php` ve `config/config.php` dosyalarını sunucu bilgilerinize göre güncelleyin
5. `config/email.php` dosyasını yapılandırın
6. Klasör izinlerini kontrol edin (uploads/)

---

## 🧮 Hesaplama Formülleri

### Gelir Testi (2024)
```
Asgari Ücret: 17,002 TL
Hane Başına Limit:
- 1 kişi: 1.0 × asgari ücret
- 2 kişi: 1.5 × asgari ücret
- 3 kişi: 2.0 × asgari ücret
- 4 kişi: 2.5 × asgari ücret
- 5+ kişi: 3.0 × asgari ücret + (her ek kişi için +0.5)
```

### Balthazard Formülü
```
Toplam engel oranı hesaplama (en yüksekten düşüğe):
Toplam = Oran₁
Kalan = 100 - Toplam
Ekleme = Kalan × (Oran₂ / 100)
Toplam = Toplam + Ekleme
(Tüm oranlar için tekrarla)
```

**Örnek:**
- %60 + %40 = 60 + (40 × 40%) = 60 + 16 = **76%**
- %50 + %30 + %20 = 50 + 15 + 7 = **72%**

---

## 🐛 Sorun Giderme

### Veritabanı Bağlantı Hatası
```
✅ MySQL servisinin çalıştığından emin olun
✅ config/database.php bilgilerini kontrol edin
✅ Veritabanı adının doğru olduğunu kontrol edin
```

### Türkçe Karakter Sorunu
```
✅ Veritabanı collation: utf8mb4_turkish_ci
✅ Tablolar collation: utf8mb4_turkish_ci
✅ PHP dosyaları UTF-8 encoding ile kaydedilmeli
```

### E-posta Gönderilmiyor
```
✅ SMTP bilgilerini kontrol edin
✅ Gmail uygulama şifresini doğru kopyaladığınızdan emin olun
✅ 2 Adımlı Doğrulama'nın aktif olduğunu kontrol edin
✅ Firewall SMTP portlarını engellemiyorsa kontrol edin
```

### 404 Sayfa Bulunamadı Hatası
```
✅ Apache mod_rewrite modülünün aktif olduğundan emin olun
✅ .htaccess dosyasının mevcut olduğunu kontrol edin
✅ SITE_URL yapılandırmasını kontrol edin
```

---

## 📞 İletişim & Destek

Sorularınız için:
- 📧 E-posta: destek@sosyalhizmet.com
- 🌐 Web: http://localhost/sosyal-hizmet-rehberlik/iletisim.php

---

## 🔌 Entegrasyonlar (Integrations)

| Entegrasyon | Açıklama | Yapılandırma |
|---|---|---|
| **Doppler** | Gizli değer yönetimi | `config/doppler.php` · `docs/DOPPLER_SETUP.md` |
| **POEditor** | Çok dilli destek (TR / EN / AR) | `config/localization.php` · `docs/POEDITOR_SETUP.md` |
| **Datadog** | İsteğe bağlı APM izleme | `config/datadog.php` · `includes/monitoring.php` |
| **DevCycle** | Özellik bayrakları | `config/devcycle.php` · `docs/DEVCYCLE_SETUP.md` |

### Hızlı başlangıç

```bash
# Ortam değişkenlerini ayarlayın
cp .env.example .env
# .env dosyasını kendi değerlerinizle doldurun

# Bağımlılıkları yükleyin
composer install

# Yerel sunucuyu başlatın
php -S localhost:8080
```

> Üretim ortamında gizli değerleri `.env` yerine [Doppler](https://doppler.com) üzerinden yönetmeniz önerilir.

---

## 📄 Lisans

Bu proje özel bir proje olup, tüm hakları saklıdır.

---

## 🙏 Teşekkürler

Sosyal Hizmet Rehberlik platformunu kullandığınız için teşekkür ederiz!

**Son Güncelleme:** 14 Aralık 2025
**Versiyon:** 1.0.0

## 📊 Veri Yönetimi ve Analitik (Yeni)

Bu sürümle birlikte aşağıdaki modüller eklendi:

- `data_management_schema.sql`: Kullanıcı profilleri, sağlık kayıtları, sosyal hak geçmişi, hesaplama sonuçları, günlük analitik ve yedekleme iş kayıtları için tablo şemaları.
- `user_profile_system.php`: KVKK uyumlu profil/sağlık verisi saklama, hassas alan şifreleme (AES-256-CBC), hesaplama sonucu geçmişi tutma.
- `admin_analytics.php`: Admin için kullanıcı, popüler hizmet, hesaplama trendi ve gelir skoru raporları (grafik + tablo).
- `backup_manager.php` + `backup_cli.php`: Günlük/haftalık/aylık/manuel yedek alma, checksum, kayıt, geri yükleme ve bulut entegrasyon iskeleti.

### Hızlı Kullanım

1. SQL şemasını veritabanına import edin:
   - `data_management_schema.sql`
2. Zamanlanmış görev örnekleri:
   - Günlük: `php backup_cli.php gunluk`
   - Haftalık: `php backup_cli.php haftalik`
   - Aylık: `php backup_cli.php aylik`
3. Rapor sayfası:
   - `admin_analytics.php`
