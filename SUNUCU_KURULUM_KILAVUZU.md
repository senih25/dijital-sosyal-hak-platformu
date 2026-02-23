# SOSYAL HİZMET DANIŞMANLIK - SUNUCUYA KURULUM KILAVUZU
Tarih: 14 Aralık 2025
Domain: sosyalhizmetdanismanligi.com

## ÖNEMLİ: YAPILAN DEĞİŞİKLİKLER
✅ config/config.php - Site URL güncellendi (https://sosyalhizmetdanismanligi.com)
✅ config/database.php - Veritabanı bilgileri güncellendi
✅ .htaccess - HTTPS yönlendirmesi aktif, RewriteBase değiştirildi
✅ Hata raporlama kapatıldı (production için)
✅ KURULUM.sql - Eksik kolonları ekleyen SQL dosyası oluşturuldu

---

## ADIM 1: FTP İLE DOSYA YÜKLEME

### FTP Bilgileri:
- Host: ftp.sosyalhizmetdanismanligi.com
- Port: 21
- Kullanıcı: sosyarpa
- Şifre: !+9R!sy!NRhCg6jn
- Hedef Dizin: /public_html/

### Yüklenecek Dosyalar (FileZilla veya başka FTP programı ile):

```
KÖKTEN YÜKLENECEKLER:
├── admin/ (tüm klasör)
├── assets/ (tüm klasör)
├── config/ (tüm klasör)
├── includes/ (tüm klasör)
├── uploads/ (tüm klasör - boş bile olsa)
├── user/ (tüm klasör)
├── .htaccess
├── index.php
├── login.php
├── logout.php
├── rehberlik.php
├── hizmetlerimiz.php
├── iletisim.php
├── gizlilik.php
├── kvkk.php
├── cerez-politikasi.php
├── cart.php
├── cart-add.php
├── checkout.php
├── order-success.php
├── database.sql (geçici - import sonrası silinebilir)
├── KURULUM.sql (geçici - import sonrası silinebilir)
└── README.md (opsiyonel)
```

### Önemli Notlar:
- uploads/ klasörü için 777 izni verin (cPanel File Manager'dan)
- config/ klasörü için 755 izni yeterli
- .htaccess dosyasını mutlaka yükleyin

---

## ADIM 2: VERİTABANI KURULUMU (phpMyAdmin)

### 2.1 phpMyAdmin'e Giriş:
1. cPanel'e giriş yapın
2. "phpMyAdmin" butonuna tıklayın
3. Sol menüden "sosyarpa_sosyal" veritabanını seçin

### 2.2 Ana Veritabanını İmport Etme:
1. Üst menüden "İçe Aktar" (Import) sekmesine tıklayın
2. "Dosya Seç" butonuna tıklayın
3. Bilgisayarınızdan `database.sql` dosyasını seçin
4. "Karakter Seti" olarak "utf8mb4_turkish_ci" seçin
5. En alttaki "Git" (Go) butonuna tıklayın
6. ✅ Başarılı mesajı görmeli ve tablolar oluşmalı

### 2.3 Eksik Kolonları Ekleme:
1. Tekrar "İçe Aktar" sekmesine gidin
2. Bu sefer `KURULUM.sql` dosyasını seçin
3. "Karakter Seti" yine "utf8mb4_turkish_ci" olmalı
4. "Git" butonuna tıklayın
5. ✅ Başarılı mesajı görmelisiniz

### 2.4 Tabloları Kontrol Etme:
Sol menüden veritabanını genişletin ve şu tabloların olduğunu kontrol edin:
- ✅ users (kullanıcılar)
- ✅ products (ürünler)
- ✅ services (hizmetler)
- ✅ contents (içerikler)
- ✅ calculations (hesaplamalar)
- ✅ orders (siparişler)
- ✅ order_items (sipariş ürünleri)
- ✅ payments (ödemeler)
- ✅ invoices (faturalar)
- ✅ cart (sepet)
- ✅ settings (ayarlar)

---

## ADIM 3: DOSYA İZİNLERİ (cPanel File Manager)

1. cPanel'de "File Manager"ı açın
2. public_html klasörüne gidin
3. Şu klasörlere sağ tıklayıp "Change Permissions" seçin:

```
uploads/ → 777 (Okuma/Yazma/Çalıştırma - Herkes)
uploads/products/ → 777
uploads/users/ → 777
uploads/contents/ → 777
```

4. Diğer tüm dosyalar için:
```
PHP dosyaları → 644
Klasörler → 755
```

---

## ADIM 4: TEST VE KONTROL

### 4.1 Site Anasayfası:
🌐 https://sosyalhizmetdanismanligi.com
- Sayfa açılmalı
- Logo ve menü görünmeli
- Hizmetler ve ürünler listelenmeli

### 4.2 Admin Paneli:
🔐 https://sosyalhizmetdanismanligi.com/admin
- **Email:** admin@sosyalhizmetdanismanligi.com
- **Şifre:** admin123

⚠️ **ÖNEMLİ:** İlk girişten sonra şifreyi mutlaka değiştirin!

### 4.3 Kontrol Listesi:
- [ ] Anasayfa açılıyor
- [ ] Admin paneline giriş yapılabiliyor
- [ ] Ürünler görüntüleniyor
- [ ] Hizmetler görüntüleniyor
- [ ] Sepete ekleme çalışıyor
- [ ] Sipariş oluşturma çalışıyor
- [ ] Türkçe karakterler düzgün görünüyor
- [ ] Görseller yükleniyor

---

## ADIM 5: BANKA AYARLARI (IBAN)

Admin paneline giriş yaptıktan sonra:
1. Sol menüden "Banka Ayarları" seçin
2. IBAN bilgilerinizi girin:
   - Banka Adı
   - Hesap Sahibi
   - IBAN (26 haneli)
   - Hesap No
   - Şube Kodu
   - Swift Kodu
   - Bilgi Notu
3. "Kaydet" butonuna tıklayın

---

## ADIM 6: GÜVENLİK AYARLARI

### 6.1 Admin Şifresini Değiştir:
1. Admin paneli → Profil
2. Yeni güçlü şifre belirleyin
3. Kaydedin

### 6.2 Test Verilerini Temizle:
phpMyAdmin'de şu komutları çalıştırın (eğer test verileri varsa):

```sql
-- Test siparişlerini sil
DELETE FROM orders WHERE id < 10;

-- Test ödemelerini sil
DELETE FROM payments WHERE id < 10;

-- Test faturalarını sil
DELETE FROM invoices WHERE id < 10;
```

### 6.3 SQL Dosyalarını Sil:
FTP ile şu dosyaları sunucudan silin:
- database.sql
- KURULUM.sql
- fix_data.sql
- update_services.sql
- vb. tüm .sql dosyaları

---

## SORUN GİDERME

### "500 Internal Server Error" Hatası:
1. .htaccess dosyasındaki php_value satırlarını yorum satırı yapın (başına # koyun)
2. Eğer düzelirse, hosting sunucunuz bu ayarları desteklemiyor
3. Bunları php.ini dosyasından ayarlayın veya hosting desteğinden yardım isteyin

### "404 Not Found" Hatası:
1. .htaccess dosyasında RewriteBase / olduğunu kontrol edin
2. cPanel'de "MultiPHP Manager"dan PHP sürümünü kontrol edin (7.4 veya 8.0 önerilir)

### Türkçe Karakter Sorunu:
1. phpMyAdmin'de veritabanı karakter setini kontrol edin: utf8mb4_turkish_ci
2. Tüm tabloları ALTER TABLE ile utf8mb4'e çevirin:
```sql
ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
ALTER TABLE products CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
-- Her tablo için tekrarlayın
```

### Görseller Yüklenmiyor:
1. uploads/ klasörünün 777 iznine sahip olduğunu kontrol edin
2. config/config.php dosyasında SITE_URL'in doğru olduğunu kontrol edin

### Veritabanı Bağlantı Hatası:
1. config/database.php dosyasındaki bilgileri kontrol edin:
   - DB_HOST: localhost (bazı hostinglerde IP adresi olabilir)
   - DB_USER: sosyarpa_sosyaldk
   - DB_PASS: 879183264520saA!*
   - DB_NAME: sosyarpa_sosyal

2. cPanel'de MySQL Databases bölümünden kullanıcının veritabanına erişim hakkı olduğunu kontrol edin

---

## İLETİŞİM

Kurulum sırasında sorun yaşarsanız:
1. Hosting sağlayıcınızın teknik destek ekibine başvurun
2. Hata mesajlarını not edin
3. cPanel error_log dosyasını kontrol edin (public_html/error_log)

---

## TAMAMLANDI! 🎉

Site artık canlı ortamda çalışmaya hazır!

Son kontrol:
✅ https://sosyalhizmetdanismanligi.com - Anasayfa
✅ https://sosyalhizmetdanismanligi.com/admin - Admin Paneli
✅ https://sosyalhizmetdanismanligi.com/hizmetlerimiz.php - Hizmetler
✅ https://sosyalhizmetdanismanligi.com/rehberlik.php - Danışmanlık

İyi çalışmalar! 🚀
