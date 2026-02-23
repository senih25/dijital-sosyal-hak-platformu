<?php
require_once 'includes/security.php';
SecurityManager::secureSessionStart();

$page_title = "KVKK Gizlilik Politikası - Dijital Sosyal Hizmet Platformu";
$page_description = "Kişisel verilerinizin korunması ve işlenmesi hakkında detaylı bilgiler. KVKK uyumlu veri işleme politikalarımız.";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Özel CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigasyon -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-hands-helping me-2"></i>
                Dijital Sosyal Hizmet
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sss.php">SSS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="iletisim.php">İletişim</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sayfa Başlığı -->
    <section class="py-5 mt-5 bg-gradient-primary text-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-shield-alt me-3"></i>
                        KVKK Gizlilik Politikası
                    </h1>
                    <p class="lead mb-0">
                        Kişisel verilerinizin korunması bizim için önceliktir. 
                        6698 sayılı KVKK'ya tam uyumlu veri işleme politikalarımız.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- KVKK İçeriği -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <!-- İçindekiler -->
                    <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>İçindekiler
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <a href="#veri-sorumlusu" class="list-group-item list-group-item-action">Veri Sorumlusu</a>
                                <a href="#toplanan-veriler" class="list-group-item list-group-item-action">Toplanan Veriler</a>
                                <a href="#isleme-amaclari" class="list-group-item list-group-item-action">İşleme Amaçları</a>
                                <a href="#hukuki-dayanaklar" class="list-group-item list-group-item-action">Hukuki Dayanaklar</a>
                                <a href="#veri-paylasimi" class="list-group-item list-group-item-action">Veri Paylaşımı</a>
                                <a href="#saklama-suresi" class="list-group-item list-group-item-action">Saklama Süresi</a>
                                <a href="#haklariniz" class="list-group-item list-group-item-action">Haklarınız</a>
                                <a href="#guvenlik" class="list-group-item list-group-item-action">Güvenlik</a>
                                <a href="#iletisim" class="list-group-item list-group-item-action">İletişim</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <!-- Veri Sorumlusu -->
                    <div id="veri-sorumlusu" class="mb-5">
                        <h2 class="h3 mb-4">
                            <i class="fas fa-building me-2 text-primary"></i>
                            Veri Sorumlusu
                        </h2>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p><strong>Dijital Sosyal Hizmet Platformu</strong> olarak, 6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") kapsamında veri sorumlusu sıfatıyla hareket etmekteyiz.</p>
                                
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h6>İletişim Bilgileri:</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-envelope me-2 text-primary"></i> kvkk@dijitalsosyalhizmet.com</li>
                                            <li><i class="fas fa-phone me-2 text-primary"></i> +90 XXX XXX XX XX</li>
                                            <li><i class="fas fa-map-marker-alt me-2 text-primary"></i> Türkiye</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Veri Koruma Sorumlusu:</h6>
                                        <p class="mb-0">KVKK uyumluluk süreçlerimizden sorumlu uzman ekibimiz ile iletişime geçebilirsiniz.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Toplanan Veriler -->
                    <div id="toplanan-veriler" class="mb-5">
                        <h2 class="h3 mb-4">
                            <i class="fas fa-database me-2 text-success"></i>
                            Toplanan Kişisel Veriler
                        </h2>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Kimlik Bilgileri:</h6>
                                        <ul>
                                            <li>Ad, soyad</li>
                                            <li>T.C. Kimlik Numarası</li>
                                            <li>Doğum tarihi</li>
                                            <li>Cinsiyet</li>
                                        </ul>
                                        
                                        <h6>İletişim Bilgileri:</h6>
                                        <ul>
                                            <li>E-posta adresi</li>
                                            <li>Telefon numarası</li>
                                            <li>Adres bilgileri</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Sağlık Bilgileri:</h6>
                                        <ul>
                                            <li>Engellilik durumu</li>
                                            <li>Sağlık raporu bilgileri</li>
                                            <li>Kronik hastalık durumu</li>
                                        </ul>
                                        
                                        <h6>Sosyoekonomik Bilgiler:</h6>
                                        <ul>
                                            <li>Gelir durumu</li>
                                            <li>Aile yapısı</li>
                                            <li>Sosyal güvenlik durumu</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Önemli:</strong> Tüm sağlık ve sosyoekonomik veriler, yalnızca hizmet sunumu için gerekli olan minimum düzeyde toplanır ve işlenir.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- İşleme Amaçları -->
                    <div id="isleme-amaclari" class="mb-5">
                        <h2 class="h3 mb-4">
                            <i class="fas fa-target me-2 text-warning"></i>
                            Veri İşleme Amaçları
                        </h2>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-handshake fa-2x text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6>Hizmet Sunumu</h6>
                                                <p class="mb-0">Sosyal hizmet danışmanlığı ve rehberlik hizmetlerinin sunulması</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-comments fa-2x text-success"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6>İletişim</h6>
                                                <p class="mb-0">Kullanıcılarımızla iletişim kurma ve bilgilendirme</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-chart-line fa-2x text-info"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6>Hizmet Geliştirme</h6>
                                                <p class="mb-0">Hizmet kalitesini artırma ve yeni hizmetler geliştirme</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-balance-scale fa-2x text-warning"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6>Yasal Yükümlülük</h6>
                                                <p class="mb-0">Yasal düzenlemelerden kaynaklanan yükümlülüklerin yerine getirilmesi</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hukuki Dayanaklar -->
                    <div id="hukuki-dayanaklar" class="mb-5">
                        <h2 class="h3 mb-4">
                            <i class="fas fa-gavel me-2 text-danger"></i>
                            Hukuki Dayanaklar
                        </h2>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Veri Kategorisi</th>
                                                <th>Hukuki Dayanak</th>
                                                <th>KVKK Maddesi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Kimlik ve İletişim Bilgileri</td>
                                                <td>Açık Rıza</td>
                                                <td>Madde 5/1-a</td>
                                            </tr>
                                            <tr>
                                                <td>Sağlık Bilgileri</td>
                                                <td>Açık Rıza + Kamu Yararı</td>
                                                <td>Madde 6/2-ç, Madde 5/2-e</td>
                                            </tr>
                                            <tr>
                                                <td>Sosyoekonomik Bilgiler</td>
                                                <td>Meşru Menfaat</td>
                                                <td>Madde 5/2-f</td>
                                            </tr>
                                            <tr>
                                                <td>Hizmet Kullanım Bilgileri</td>
                                                <td>Sözleşmenin Kurulması/İfası</td>
                                                <td>Madde 5/2-c</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Haklarınız -->
                    <div id="haklariniz" class="mb-5">
                        <h2 class="h3 mb-4">
                            <i class="fas fa-user-shield me-2 text-primary"></i>
                            KVKK Kapsamındaki Haklarınız
                        </h2>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="mb-4">KVKK'nın 11. maddesi uyarınca aşağıdaki haklara sahipsiniz:</p>
                                
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="border-start border-primary border-4 ps-3">
                                            <h6>Bilgi Talep Etme</h6>
                                            <p class="mb-0 small">Kişisel verilerinizin işlenip işlenmediğini öğrenme</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="border-start border-success border-4 ps-3">
                                            <h6>Erişim Hakkı</h6>
                                            <p class="mb-0 small">İşlenen kişisel verileriniz hakkında bilgi talep etme</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="border-start border-warning border-4 ps-3">
                                            <h6>Düzeltme Hakkı</h6>
                                            <p class="mb-0 small">Yanlış veya eksik verilerin düzeltilmesini isteme</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="border-start border-danger border-4 ps-3">
                                            <h6>Silme Hakkı</h6>
                                            <p class="mb-0 small">Kişisel verilerinizin silinmesini talep etme</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="border-start border-info border-4 ps-3">
                                            <h6>Taşınabilirlik</h6>
                                            <p class="mb-0 small">Verilerinizi yapılandırılmış formatta alma</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="border-start border-secondary border-4 ps-3">
                                            <h6>İtiraz Hakkı</h6>
                                            <p class="mb-0 small">Veri işlemeye itiraz etme ve şikayette bulunma</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-success mt-4">
                                    <h6><i class="fas fa-paper-plane me-2"></i>Başvuru Yöntemi</h6>
                                    <p class="mb-2">Haklarınızı kullanmak için aşağıdaki yöntemlerle başvurabilirsiniz:</p>
                                    <ul class="mb-0">
                                        <li><strong>E-posta:</strong> kvkk@dijitalsosyalhizmet.com</li>
                                        <li><strong>Başvuru Formu:</strong> Web sitemizden online form doldurma</li>
                                        <li><strong>Yazılı Başvuru:</strong> İmzalı dilekçe ile posta gönderimi</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Güvenlik -->
                    <div id="guvenlik" class="mb-5">
                        <h2 class="h3 mb-4">
                            <i class="fas fa-lock me-2 text-success"></i>
                            Veri Güvenliği
                        </h2>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="mb-4">Kişisel verilerinizin güvenliği için aşağıdaki teknik ve idari tedbirleri almaktayız:</p>
                                
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                                            <h6>Şifreleme</h6>
                                            <p class="small">Tüm veriler AES-256 şifreleme ile korunur</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <i class="fas fa-server fa-3x text-success mb-3"></i>
                                            <h6>Güvenli Sunucular</h6>
                                            <p class="small">ISO 27001 sertifikalı veri merkezleri</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <i class="fas fa-user-lock fa-3x text-warning mb-3"></i>
                                            <h6>Erişim Kontrolü</h6>
                                            <p class="small">Çok faktörlü kimlik doğrulama</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <h6>Güvenlik Önlemleri:</h6>
                                    <ul>
                                        <li>SSL/TLS şifreleme ile güvenli veri iletimi</li>
                                        <li>Düzenli güvenlik denetimleri ve penetrasyon testleri</li>
                                        <li>Personel eğitimleri ve gizlilik sözleşmeleri</li>
                                        <li>Veri yedekleme ve felaket kurtarma planları</li>
                                        <li>Güvenlik ihlali durumunda acil müdahale prosedürleri</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- İletişim -->
                    <div id="iletisim" class="mb-5">
                        <h2 class="h3 mb-4">
                            <i class="fas fa-envelope me-2 text-info"></i>
                            KVKK İletişim
                        </h2>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Veri Koruma Sorumlusu</h6>
                                        <p><i class="fas fa-envelope me-2"></i> kvkk@dijitalsosyalhizmet.com</p>
                                        <p><i class="fas fa-phone me-2"></i> +90 XXX XXX XX XX</p>
                                        
                                        <h6 class="mt-4">Başvuru Süreçleri</h6>
                                        <ul class="small">
                                            <li>Başvuru değerlendirme süresi: 30 gün</li>
                                            <li>Ücretsiz başvuru hakkı</li>
                                            <li>Yazılı veya elektronik ortamda yanıt</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Şikayet Mercii</h6>
                                        <p>Başvurunuzun reddedilmesi, verilen cevabın yetersiz bulunması veya süresinde başvurunuza cevap verilmemesi hallerinde, Kişisel Verileri Koruma Kurulu'na şikayette bulunabilirsiniz.</p>
                                        
                                        <div class="alert alert-light">
                                            <strong>KVKK İletişim:</strong><br>
                                            <i class="fas fa-globe me-2"></i> www.kvkk.gov.tr<br>
                                            <i class="fas fa-envelope me-2"></i> kvkk@kvkk.gov.tr
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Son Güncelleme -->
                    <div class="alert alert-secondary">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <strong>Son Güncelleme:</strong> 22 Şubat 2026<br>
                        <small>Bu politika, yasal değişiklikler ve hizmet geliştirmelerimiz doğrultusunda güncellenebilir. Önemli değişiklikler hakkında kullanıcılarımız bilgilendirilecektir.</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">Dijital Sosyal Hizmet</h5>
                    <p class="text-muted">
                        KVKK uyumlu, güvenli ve şeffaf sosyal hizmet platformu.
                    </p>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">Yasal Belgeler</h6>
                    <ul class="list-unstyled">
                        <li><a href="kvkk-politikasi.php" class="text-muted text-decoration-none">KVKK Politikası</a></li>
                        <li><a href="kullanim-kosullari.php" class="text-muted text-decoration-none">Kullanım Koşulları</a></li>
                        <li><a href="cerez-politikasi.php" class="text-muted text-decoration-none">Çerez Politikası</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">KVKK İletişim</h6>
                    <p class="text-muted mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        kvkk@dijitalsosyalhizmet.com
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-shield-alt me-2"></i>
                        Verileriniz güvende
                    </p>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        © 2026 Dijital Sosyal Hizmet Platformu. KVKK Uyumlu.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-success">
                        <i class="fas fa-check me-1"></i>KVKK Uyumlu
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Özel JS -->
    <script src="assets/js/main.js"></script>
    
    <!-- Smooth Scroll -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>