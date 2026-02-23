<?php
require_once 'config/config.php';

$pageTitle = 'KVKK Aydınlatma Metni';

include 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <h1 class="mb-4 text-primary">
                            <i class="fas fa-shield-alt me-2"></i>
                            KVKK Aydınlatma Metni
                        </h1>
                        
                        <p class="lead text-muted mb-5">
                            Son Güncelleme: <?php echo date('d.m.Y'); ?>
                        </p>
                        
                        <h4 class="mt-5 mb-3">1. Veri Sorumlusunun Kimliği</h4>
                        <p>
                            6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") uyarınca, kişisel verileriniz; 
                            veri sorumlusu olarak <strong>Sosyal Hizmet Rehberlik & Danışmanlık</strong> tarafından aşağıda açıklanan 
                            kapsamda işlenebilecektir.
                        </p>
                        <div class="alert alert-info">
                            <strong>Veri Sorumlusu:</strong> Sosyal Hizmet Rehberlik & Danışmanlık<br>
                            <strong>Adres:</strong> <?php echo escape(getSetting($pdo, 'contact_address')); ?><br>
                            <strong>E-posta:</strong> <?php echo escape(getSetting($pdo, 'contact_email')); ?><br>
                            <strong>Telefon:</strong> <?php echo escape(getSetting($pdo, 'contact_phone')); ?>
                        </div>
                        
                        <h4 class="mt-5 mb-3">2. İşlenen Kişisel Veriler</h4>
                        <p>Platformumuzda aşağıdaki kişisel verileriniz işlenmektedir:</p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Veri Kategorisi</th>
                                        <th>Açıklama</th>
                                        <th>Saklama Süresi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Kimlik Bilgileri</strong></td>
                                        <td>Ad, soyad, TC kimlik numarası (isteğe bağlı)</td>
                                        <td>Hesap silinene kadar</td>
                                    </tr>
                                    <tr>
                                        <td><strong>İletişim Bilgileri</strong></td>
                                        <td>E-posta adresi, telefon numarası, adres</td>
                                        <td>Hesap silinene kadar</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Müşteri İşlem Bilgileri</strong></td>
                                        <td>Sipariş geçmişi, hesaplama kayıtları, danışmanlık talepleri</td>
                                        <td>10 yıl (Vergi mevzuatı gereği)</td>
                                    </tr>
                                    <tr>
                                        <td><strong>İşlem Güvenliği Bilgileri</strong></td>
                                        <td>IP adresi, çerez bilgileri, oturum kayıtları</td>
                                        <td>6 ay</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pazarlama Bilgileri</strong></td>
                                        <td>İletişim tercihleri, ilgi alanları</td>
                                        <td>Onay iptaline kadar</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <h4 class="mt-5 mb-3">3. Kişisel Verilerin Hangi Amaçla İşleneceği</h4>
                        <p>Kişisel verileriniz aşağıdaki amaçlarla işlenmektedir:</p>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Kullanıcı hesabı oluşturma, yönetme ve kimlik doğrulama</li>
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>İletişim taleplerinin değerlendirilmesi ve cevaplanması</li>
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Sosyal hizmet danışmanlık hizmetlerinin sunulması</li>
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>E-kitap, rehber ve dijital ürün satış işlemlerinin gerçekleştirilmesi</li>
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Gelir testi, engel oranı hesaplama gibi araçların kullanımı</li>
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Sipariş takibi ve müşteri hizmetleri desteği</li>
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Yasal yükümlülüklerin yerine getirilmesi (fatura, vergi mevzuatı)</li>
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Hizmet kalitesinin ölçülmesi ve geliştirilmesi</li>
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>İstatistiksel analizler ve raporlama</li>
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Pazarlama ve tanıtım faaliyetleri (onay dahilinde)</li>
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Sistem güvenliğinin sağlanması ve dolandırıcılığın önlenmesi</li>
                        </ul>
                        
                        <h4 class="mt-5 mb-3">4. İşlenen Kişisel Verilerin Kimlere ve Hangi Amaçla Aktarılabileceği</h4>
                        <p>Kişisel verileriniz aşağıdaki alıcı gruplarına aktarılabilir:</p>
                        <div class="accordion" id="dataTransferAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                        İş Ortakları ve Hizmet Sağlayıcılar
                                    </button>
                                </h2>
                                <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#dataTransferAccordion">
                                    <div class="accordion-body">
                                        Ödeme kuruluşları, kargo firmaları, bulut hizmet sağlayıcıları, SMS/e-posta gönderim hizmetleri gibi operasyonel ihtiyaçlar için.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                        Yasal Yükümlülükler
                                    </button>
                                </h2>
                                <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#dataTransferAccordion">
                                    <div class="accordion-body">
                                        Mahkemeler, savcılıklar, kolluk kuvvetleri, vergi daireleri gibi yetkili kamu kurum ve kuruluşlarına yasal zorunluluk halinde.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                        Danışmanlar ve Denetçiler
                                    </button>
                                </h2>
                                <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#dataTransferAccordion">
                                    <div class="accordion-body">
                                        Hukuk, muhasebe, vergi danışmanlığı ve denetim hizmetleri sunan profesyonel kuruluşlar.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="mt-5 mb-3">5. Kişisel Veri Toplamanın Yöntemi ve Hukuki Sebebi</h4>
                        <p>Kişisel verileriniz aşağıdaki yöntemlerle toplanmaktadır:</p>
                        <ul>
                            <li><strong>Web Sitesi:</strong> Kayıt formları, iletişim formları, hesaplama araçları</li>
                            <li><strong>E-posta:</strong> İletişim ve sipariş süreçleri</li>
                            <li><strong>Telefon:</strong> Danışmanlık ve müşteri hizmetleri görüşmeleri</li>
                            <li><strong>Çerezler:</strong> Otomatik veri toplama teknolojileri (detaylar için <a href="<?php echo SITE_URL; ?>/cerez-politikasi.php">Çerez Politikası</a>)</li>
                        </ul>
                        <p class="mt-3"><strong>Hukuki Sebepler:</strong></p>
                        <ul>
                            <li>Sözleşmenin kurulması veya ifası (KVKK m.5/2-c)</li>
                            <li>Hukuki yükümlülüğün yerine getirilmesi (KVKK m.5/2-ç)</li>
                            <li>Meşru menfaatler (KVKK m.5/2-f)</li>
                            <li>Açık rıza (KVKK m.5/1)</li>
                        </ul>
                        
                        <h4 class="mt-5 mb-3">6. Kişisel Veri Sahibinin KVKK'nın 11. Maddesinde Sayılan Hakları</h4>
                        <p>KVKK'nın 11. maddesi uyarınca aşağıdaki haklara sahipsiniz:</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-info-circle text-primary me-2"></i>Bilgi Alma</h5>
                                        <p class="card-text">Kişisel verilerinizin işlenip işlenmediğini öğrenme</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-eye text-primary me-2"></i>Erişim</h5>
                                        <p class="card-text">İşlenmişse buna ilişkin bilgi talep etme</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-edit text-primary me-2"></i>Düzeltme</h5>
                                        <p class="card-text">İşleme amacını ve verinin eksik/yanlış olması durumunda düzeltilmesini isteme</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-trash text-primary me-2"></i>Silme/Anonimleştirme</h5>
                                        <p class="card-text">KVKK'da öngörülen şartlar çerçevesinde silinmesini/yok edilmesini/anonim hale getirilmesini isteme</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-share text-primary me-2"></i>Aktarım Bilgisi</h5>
                                        <p class="card-text">Verilerin aktarıldığı üçüncü kişileri öğrenme</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-ban text-primary me-2"></i>İtiraz</h5>
                                        <p class="card-text">Otomatik sistemlerle analiz sonucu oluşan sonuca itiraz etme ve zararın giderilmesini isteme</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning mt-5">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Haklarınızı Nasıl Kullanabilirsiniz?</h5>
                            <p class="mb-2">Yukarıdaki haklarınızı kullanmak için aşağıdaki kanallardan başvurabilirsiniz:</p>
                            <ul class="mb-0">
                                <li><strong>Yazılı Başvuru:</strong> <?php echo escape(getSetting($pdo, 'contact_address')); ?></li>
                                <li><strong>E-posta:</strong> <a href="mailto:<?php echo escape(getSetting($pdo, 'contact_email')); ?>"><?php echo escape(getSetting($pdo, 'contact_email')); ?></a></li>
                                <li><strong>KEP Adresi:</strong> (varsa eklenecek)</li>
                                <li><strong>Güvenli Elektronik İmza:</strong> Kayıtlı Elektronik Posta (KEP) adresi veya platformumuz üzerinden</li>
                            </ul>
                            <p class="mt-3 mb-0"><small>Başvurularınız, talebin niteliğine göre en kısa sürede ve en geç 30 gün içinde ücretsiz olarak sonuçlandırılacaktır. İşlemin ayrıca bir maliyet gerektirmesi halinde, Kişisel Verileri Koruma Kurulu tarafından belirlenen tarifedeki ücret alınabilir.</small></p>
                        </div>
                        
                        <h4 class="mt-5 mb-3">7. Veri Güvenliği</h4>
                        <p>Kişisel verilerinizin güvenliğini sağlamak için:</p>
                        <ul>
                            <li>SSL/TLS şifreleme teknolojisi kullanılmaktadır</li>
                            <li>Güvenlik duvarları ve antivirüs sistemleri aktiftir</li>
                            <li>Erişim kontrolleri ve yetkilendirme mekanizmaları uygulanmaktadır</li>
                            <li>Düzenli güvenlik testleri yapılmaktadır</li>
                            <li>Personel eğitimleri ve gizlilik sözleşmeleri uygulanmaktadır</li>
                        </ul>
                        
                        <h4 class="mt-5 mb-3">8. İletişim</h4>
                        <p>KVKK kapsamında sorularınız için:</p>
                        <ul>
                            <li><strong>E-posta:</strong> <?php echo escape(getSetting($pdo, 'contact_email')); ?></li>
                            <li><strong>Telefon:</strong> <?php echo escape(getSetting($pdo, 'contact_phone')); ?></li>
                            <li><strong>Adres:</strong> <?php echo escape(getSetting($pdo, 'contact_address')); ?></li>
                        </ul>
                        
                        <div class="text-center mt-5">
                            <a href="<?php echo SITE_URL; ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-home me-2"></i>Ana Sayfaya Dön
                            </a>
                        </div 
                            kaydıyla, sözleşmenin taraflarına ait kişisel verilerin işlenmesinin gerekli olması 
                            hukuki sebebine dayalı olarak toplanmaktadır.
                        </p>
                        
                        <h4 class="mt-4">5. Kişisel Veri Sahibinin KVKK'nın 11. Maddesinde Sayılan Hakları</h4>
                        <p>Kişisel veri sahipleri olarak, haklarınız şunlardır:</p>
                        <ul>
                            <li>Kişisel veri işlenip işlenmediğini öğrenme,</li>
                            <li>Kişisel verileri işlenmişse buna ilişkin bilgi talep etme,</li>
                            <li>Kişisel verilerin işlenme amacını ve bunların amacına uygun kullanılıp kullanılmadığını öğrenme,</li>
                            <li>Yurt içinde veya yurt dışında kişisel verilerin aktarıldığı üçüncü kişileri bilme,</li>
                            <li>Kişisel verilerin eksik veya yanlış işlenmiş olması hâlinde bunların düzeltilmesini isteme,</li>
                            <li>Kişisel verilerin silinmesini veya yok edilmesini isteme,</li>
                            <li>Kişisel verilerin aktarıldığı üçüncü kişilere yukarıda sayılan (e) ve (f) bentleri uyarınca yapılan işlemlerin bildirilmesini isteme,</li>
                            <li>İşlenen verilerin münhasıran otomatik sistemler vasıtasıyla analiz edilmesi suretiyle kişinin kendisi aleyhine bir sonucun ortaya çıkmasına itiraz etme,</li>
                            <li>Kişisel verilerin kanuna aykırı olarak işlenmesi sebebiyle zarara uğraması hâlinde zararın giderilmesini talep etme.</li>
                        </ul>
                        
                        <h4 class="mt-4">6. İletişim</h4>
                        <p>
                            Yukarıda belirtilen haklarınızı kullanmak için <?php echo escape(getSetting($pdo, 'contact_email')); ?> 
                            adresine e-posta gönderebilir veya <?php echo escape(getSetting($pdo, 'contact_phone')); ?> 
                            numaralı telefondan bizimle iletişime geçebilirsiniz.
                        </p>
                        
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i>
                            Bu metin en son <strong><?php echo date('d.m.Y'); ?></strong> tarihinde güncellenmiştir.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
