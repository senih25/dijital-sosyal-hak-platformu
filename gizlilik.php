<?php
require_once 'config/config.php';

$pageTitle = 'Gizlilik Politikası';

include 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <h1 class="mb-4 text-primary">
                            <i class="fas fa-user-shield me-2"></i>
                            Gizlilik Politikası
                        </h1>
                        
                        <p class="lead text-muted mb-5">
                            Son Güncelleme: <?php echo date('d.m.Y'); ?>
                        </p>

                        <div class="alert alert-info">
                            <p class="mb-0"><i class="fas fa-info-circle me-2"></i><strong>Sosyal Hizmet Rehberlik & Danışmanlık</strong> olarak gizliliğinize saygı duyuyoruz. Bu politika, kişisel bilgilerinizi nasıl topladığımızı, kullandığımızı, koruduğumuzu ve paylaştığımızı açıklamaktadır.</p>
                        </div>

                        <h4 class="mt-5 mb-3">1. Toplanan Bilgiler</h4>
                        
                        <h5 class="mt-4">1.1. Kullanıcı Tarafından Sağlanan Bilgiler</h5>
                        <ul>
                            <li><strong>Kayıt Bilgileri:</strong> Ad, soyad, e-posta, telefon, şifre</li>
                            <li><strong>Profil Bilgileri:</strong> Doğum tarihi, cinsiyet, adres bilgileri</li>
                            <li><strong>İletişim Bilgileri:</strong> İletişim formları ve e-postalar aracılığıyla paylaştığınız bilgiler</li>
                            <li><strong>Ödeme Bilgileri:</strong> Kredi kartı bilgileri (3. parti ödeme sistemleri üzerinden işlenir)</li>
                            <li><strong>Hesaplama Verileri:</strong> Gelir testi, engel oranı hesaplama gibi araçlarda girdiğiniz veriler</li>
                        </ul>

                        <h5 class="mt-4">1.2. Otomatik Olarak Toplanan Bilgiler</h5>
                        <ul>
                            <li><strong>Teknik Bilgiler:</strong> IP adresi, tarayıcı türü, işletim sistemi, cihaz bilgileri</li>
                            <li><strong>Kullanım Bilgileri:</strong> Ziyaret edilen sayfalar, tıklanan bağlantılar, oturum süreleri</li>
                            <li><strong>Konum Bilgileri:</strong> Genel coğrafi konum (şehir/bölge seviyesi)</li>
                            <li><strong>Çerez Verileri:</strong> Detaylar için <a href="<?php echo SITE_URL; ?>/cerez-politikasi.php">Çerez Politikası</a> sayfamıza bakınız</li>
                        </ul>

                        <h4 class="mt-5 mb-3">2. Bilgilerin Kullanım Amaçları</h4>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card h-100 bg-light">
                                    <div class="card-body">
                                        <h5><i class="fas fa-cogs text-primary me-2"></i>Hizmet Sunumu</h5>
                                        <ul class="mb-0">
                                            <li>Hesap oluşturma ve yönetimi</li>
                                            <li>Danışmanlık hizmetleri</li>
                                            <li>Sipariş işleme ve teslimat</li>
                                            <li>Müşteri destek hizmetleri</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 bg-light">
                                    <div class="card-body">
                                        <h5><i class="fas fa-shield-alt text-success me-2"></i>Güvenlik</h5>
                                        <ul class="mb-0">
                                            <li>Kimlik doğrulama</li>
                                            <li>Dolandırıcılık önleme</li>
                                            <li>Güvenlik ihlali tespiti</li>
                                            <li>Yasal uyum</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 bg-light">
                                    <div class="card-body">
                                        <h5><i class="fas fa-chart-line text-info me-2"></i>İyileştirme</h5>
                                        <ul class="mb-0">
                                            <li>Site performansı analizi</li>
                                            <li>Kullanıcı deneyimi geliştirme</li>
                                            <li>A/B testleri</li>
                                            <li>Hata ayıklama</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 bg-light">
                                    <div class="card-body">
                                        <h5><i class="fas fa-bullhorn text-warning me-2"></i>İletişim</h5>
                                        <ul class="mb-0">
                                            <li>Bilgilendirme e-postaları</li>
                                            <li>Pazarlama (onaylı kullanıcılar)</li>
                                            <li>Duyurular ve güncellemeler</li>
                                            <li>Anketler ve geri bildirim</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4 class="mt-5 mb-3">3. Bilgi Paylaşımı ve Aktarımı</h4>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Alıcı Grubu</th>
                                        <th>Paylaşım Nedeni</th>
                                        <th>Veri Türü</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Ödeme Sağlayıcıları</strong></td>
                                        <td>Ödeme işlemlerinin gerçekleştirilmesi</td>
                                        <td>İsim, e-posta, tutar bilgisi</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kargo Firmaları</strong></td>
                                        <td>Fiziksel ürün teslimatı</td>
                                        <td>İsim, adres, telefon</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Analitik Hizmetler</strong></td>
                                        <td>İstatistiksel analiz ve raporlama</td>
                                        <td>Anonim kullanım verileri</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Bulut Hizmet Sağlayıcıları</strong></td>
                                        <td>Veri depolama ve barındırma</td>
                                        <td>Tüm kullanıcı verileri (şifreli)</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Yasal Makamlar</strong></td>
                                        <td>Yasal zorunluluklar ve mahkeme kararları</td>
                                        <td>Talep edilen veriler</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-warning">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Önemli:</strong> Kişisel bilgilerinizi, pazarlama amaçlı olarak üçüncü taraflara satmıyoruz veya kiralamıyoruz.
                        </div>

                        <h4 class="mt-5 mb-3">4. Veri Güvenliği</h4>
                        
                        <p>Bilgilerinizin güvenliğini sağlamak için çeşitli teknik ve organizasyonel önlemler alıyoruz:</p>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="text-center p-3 border rounded">
                                    <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                                    <h5>SSL/TLS Şifreleme</h5>
                                    <p class="small mb-0">Tüm veri iletimi şifrelidir</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 border rounded">
                                    <i class="fas fa-database fa-3x text-success mb-3"></i>
                                    <h5>Güvenli Depolama</h5>
                                    <p class="small mb-0">Veriler güvenli sunucularda saklanır</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 border rounded">
                                    <i class="fas fa-user-lock fa-3x text-info mb-3"></i>
                                    <h5>Erişim Kontrolü</h5>
                                    <p class="small mb-0">Sınırlı ve yetkili erişim</p>
                                </div>
                            </div>
                        </div>

                        <h4 class="mt-5 mb-3">5. Çerezler ve İzleme Teknolojileri</h4>
                        
                        <p>Web sitemizde kullanıcı deneyimini geliştirmek için çerezler kullanıyoruz. Çerez tercihlerinizi yönetmek için:</p>
                        <ul>
                            <li>Sayfanın altındaki çerez ayarları butonunu kullanabilirsiniz</li>
                            <li>Tarayıcınızın ayarlarından çerezleri yönetebilirsiniz</li>
                            <li>Detaylı bilgi için <a href="<?php echo SITE_URL; ?>/cerez-politikasi.php">Çerez Politikası</a> sayfamızı ziyaret edebilirsiniz</li>
                        </ul>

                        <h4 class="mt-5 mb-3">6. Haklarınız</h4>
                        
                        <p>Kişisel verilerinizle ilgili aşağıdaki haklara sahipsiniz:</p>
                        
                        <div class="accordion" id="rightsAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#right1">
                                        <i class="fas fa-eye text-primary me-2"></i>Erişim Hakkı
                                    </button>
                                </h2>
                                <div id="right1" class="accordion-collapse collapse show" data-bs-parent="#rightsAccordion">
                                    <div class="accordion-body">
                                        Hangi kişisel verilerinizi işlediğimizi öğrenme ve bu verilerin bir kopyasını talep etme hakkınız vardır.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#right2">
                                        <i class="fas fa-edit text-success me-2"></i>Düzeltme Hakkı
                                    </button>
                                </h2>
                                <div id="right2" class="accordion-collapse collapse" data-bs-parent="#rightsAccordion">
                                    <div class="accordion-body">
                                        Yanlış veya eksik kişisel verilerinizin düzeltilmesini talep edebilirsiniz.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#right3">
                                        <i class="fas fa-trash text-danger me-2"></i>Silme Hakkı ("Unutulma Hakkı")
                                    </button>
                                </h2>
                                <div id="right3" class="accordion-collapse collapse" data-bs-parent="#rightsAccordion">
                                    <div class="accordion-body">
                                        Belirli koşullarda kişisel verilerinizin silinmesini talep edebilirsiniz.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#right4">
                                        <i class="fas fa-ban text-warning me-2"></i>İtiraz Hakkı
                                    </button>
                                </h2>
                                <div id="right4" class="accordion-collapse collapse" data-bs-parent="#rightsAccordion">
                                    <div class="accordion-body">
                                        Kişisel verilerinizin işlenmesine itiraz edebilir ve pazarlama e-postalarından çıkabilirsiniz.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#right5">
                                        <i class="fas fa-file-export text-info me-2"></i>Taşınabilirlik Hakkı
                                    </button>
                                </h2>
                                <div id="right5" class="accordion-collapse collapse" data-bs-parent="#rightsAccordion">
                                    <div class="accordion-body">
                                        Kişisel verilerinizi yapılandırılmış, yaygın kullanılan ve makine tarafından okunabilir bir formatta almanız ve başka bir hizmet sağlayıcıya aktarmanız mümkündür.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4 class="mt-5 mb-3">7. Çocukların Gizliliği</h4>
                        
                        <div class="alert alert-danger">
                            <p><strong><i class="fas fa-child me-2"></i>18 Yaş Altı Kullanıcılar:</strong></p>
                            <p class="mb-0">Hizmetlerimiz 18 yaşın altındaki bireyler için tasarlanmamıştır. Bilerek 18 yaşın altındaki çocuklardan kişisel bilgi toplamıyoruz. Eğer 18 yaşın altındaki bir çocuğun bilgilerini topladığımızı fark ederseniz, lütfen bizimle iletişime geçin ve bu bilgileri derhal sileceğiz.</p>
                        </div>

                        <h4 class="mt-5 mb-3">8. Üçüncü Taraf Bağlantıları</h4>
                        
                        <p>Web sitemiz, üçüncü taraf web sitelerine bağlantılar içerebilir. Bu sitelerin gizlilik uygulamalarından sorumlu değiliz. Bu siteleri ziyaret ederken kendi gizlilik politikalarını okumanızı öneririz.</p>

                        <h4 class="mt-5 mb-3">9. Veri Saklama Süreleri</h4>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Veri Türü</th>
                                        <th>Saklama Süresi</th>
                                        <th>Açıklama</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Hesap Bilgileri</td>
                                        <td>Hesap aktif olduğu sürece</td>
                                        <td>Hesap silindiğinde 30 gün içinde tamamı silinir</td>
                                    </tr>
                                    <tr>
                                        <td>İşlem Kayıtları</td>
                                        <td>10 yıl</td>
                                        <td>Vergi ve muhasebe mevzuatı gereği</td>
                                    </tr>
                                    <tr>
                                        <td>İletişim Mesajları</td>
                                        <td>2 yıl</td>
                                        <td>Müşteri hizmetleri ve kalite takibi için</td>
                                    </tr>
                                    <tr>
                                        <td>Log Kayıtları</td>
                                        <td>6 ay</td>
                                        <td>Güvenlik ve teknik destek için</td>
                                    </tr>
                                    <tr>
                                        <td>Pazarlama Onayları</td>
                                        <td>Onay iptaline kadar</td>
                                        <td>İstediğiniz zaman iptal edebilirsiniz</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h4 class="mt-5 mb-3">10. Politika Değişiklikleri</h4>
                        
                        <p>Bu Gizlilik Politikasını zaman zaman güncelleyebiliriz. Önemli değişiklikler olduğunda sizi e-posta yoluyla veya web sitemizde duyuru yaparak bilgilendireceğiz. Politikayı düzenli olarak gözden geçirmenizi öneririz.</p>

                        <h4 class="mt-5 mb-3">11. İletişim</h4>
                        
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="text-white">Gizlilikle İlgili Sorularınız İçin:</h5>
                                <p><strong>E-posta:</strong> <?php echo escape(getSetting($pdo, 'contact_email')); ?></p>
                                <p><strong>Telefon:</strong> <?php echo escape(getSetting($pdo, 'contact_phone')); ?></p>
                                <p class="mb-0"><strong>Adres:</strong> <?php echo escape(getSetting($pdo, 'contact_address')); ?></p>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <a href="<?php echo SITE_URL; ?>/kvkk.php" class="btn btn-outline-primary me-2">
                                <i class="fas fa-shield-alt me-2"></i>KVKK Aydınlatma Metni
                            </a>
                            <a href="<?php echo SITE_URL; ?>/cerez-politikasi.php" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-cookie-bite me-2"></i>Çerez Politikası
                            </a>
                            <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>Ana Sayfa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
