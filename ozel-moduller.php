<?php include 'header.php'; ?>

<main class="modules-page">
    <section class="modules-hero">
        <div class="container">
            <h1>🎯 Özel Modüller</h1>
            <p>Sosyal hizmet süreçlerini dijitalleştiren üç ileri seviye modül</p>
        </div>
    </section>

    <section class="module-card" id="modul-34">
        <div class="container">
            <h2>34. Mevzuat Takip Sistemi</h2>
            <p>
                SGK, Resmî Gazete ve ilgili kurumların RSS beslemelerini otomatik izleyerek
                yeni mevzuat değişikliklerini tespit eder; etkilenebilecek hizmetleri analiz eder
                ve uzmanlara anlık bildirim gönderir.
            </p>

            <div class="feature-grid">
                <article>
                    <h3>RSS Besleme İzleme</h3>
                    <p>Birden fazla kaynaktan düzenli çekim yapar ve değişiklikleri kıyaslar.</p>
                </article>
                <article>
                    <h3>Değişiklik Bildirimleri</h3>
                    <p>E-posta, SMS ve panel içi bildirim kanallarını tek merkezden yönetir.</p>
                </article>
                <article>
                    <h3>Etki Analizi</h3>
                    <p>Değişikliğin hangi hak türlerini ve kullanıcı profillerini etkileyeceğini raporlar.</p>
                </article>
            </div>

            <form id="mevzuat-form" class="module-form">
                <label for="mevzuat-keyword">Takip Anahtar Kelimesi</label>
                <input id="mevzuat-keyword" name="keyword" type="text" placeholder="Örn: evde bakım, emeklilik, 2022 aylığı" required>

                <label for="mevzuat-impact">Etki Düzeyi Eşiği</label>
                <select id="mevzuat-impact" name="impact" required>
                    <option value="Yüksek">Yüksek</option>
                    <option value="Orta">Orta</option>
                    <option value="Düşük">Düşük</option>
                </select>

                <button type="submit">Takip Kuralı Oluştur</button>
            </form>
            <div id="mevzuat-result" class="module-result" aria-live="polite"></div>
        </div>
    </section>

    <section class="module-card" id="modul-35">
        <div class="container">
            <h2>35. Randevu Yönetim Sistemi</h2>
            <p>
                Danışan randevularını takvimle senkronize eder, otomatik hatırlatma gönderir,
                video görüşme bağlantısı üretir ve ödeme adımlarını tek akışta birleştirir.
            </p>

            <div class="feature-grid">
                <article>
                    <h3>Takvim Senkronizasyonu</h3>
                    <p>Google Calendar / Outlook entegrasyonu ile çakışmaları engeller.</p>
                </article>
                <article>
                    <h3>Otomatik Hatırlatma</h3>
                    <p>Randevudan 24 saat ve 1 saat önce kişiselleştirilmiş hatırlatmalar gönderir.</p>
                </article>
                <article>
                    <h3>Video + Ödeme Entegrasyonu</h3>
                    <p>Görüşme linki üretir, ödeme durumunu randevu kaydıyla ilişkilendirir.</p>
                </article>
            </div>

            <form id="randevu-form" class="module-form">
                <label for="appointment-type">Randevu Tipi</label>
                <select id="appointment-type" name="appointmentType" required>
                    <option value="online">Online Görüşme</option>
                    <option value="yuz-yuze">Yüz Yüze</option>
                </select>

                <label for="meeting-date">Randevu Tarihi</label>
                <input id="meeting-date" name="date" type="date" required>

                <label for="payment-status">Ödeme Durumu</label>
                <select id="payment-status" name="paymentStatus" required>
                    <option value="odendi">Ödendi</option>
                    <option value="beklemede">Beklemede</option>
                </select>

                <button type="submit">Randevu Akışını Planla</button>
            </form>
            <div id="randevu-result" class="module-result" aria-live="polite"></div>
        </div>
    </section>

    <section class="module-card" id="modul-36">
        <div class="container">
            <h2>36. Belge Yönetim Sistemi</h2>
            <p>
                Belgeleri şifreli depolama ile güvenle saklar, kategori bazlı düzenler,
                hızlı arama sunar, kontrollü paylaşım bağlantıları üretir ve son kullanma
                tarihlerini proaktif olarak takip eder.
            </p>

            <div class="feature-grid">
                <article>
                    <h3>Güvenli Şifreleme</h3>
                    <p>Dosyalar yükleme anında şifrelenir, erişim denetimi ile korunur.</p>
                </article>
                <article>
                    <h3>Kategorizasyon & Arama</h3>
                    <p>Belge türüne göre etiketleme ve anahtar kelimeyle hızlı bulma sağlar.</p>
                </article>
                <article>
                    <h3>Paylaşım & Son Kullanma Takibi</h3>
                    <p>Zaman sınırlı paylaşım linkleri ve bitiş tarihi yaklaşan belge uyarıları üretir.</p>
                </article>
            </div>

            <form id="belge-form" class="module-form">
                <label for="document-name">Belge Adı</label>
                <input id="document-name" name="documentName" type="text" placeholder="Örn: Sağlık Kurulu Raporu" required>

                <label for="document-category">Kategori</label>
                <select id="document-category" name="category" required>
                    <option value="saglik">Sağlık</option>
                    <option value="kimlik">Kimlik</option>
                    <option value="gelir">Gelir</option>
                    <option value="diger">Diğer</option>
                </select>

                <label for="document-expiry">Son Kullanma Tarihi</label>
                <input id="document-expiry" name="expiry" type="date" required>

                <button type="submit">Belge Politikasını Oluştur</button>
            </form>
            <div id="belge-result" class="module-result" aria-live="polite"></div>
        </div>
    </section>
</main>

<script src="script.js"></script>
<?php include 'footer.php'; ?>
