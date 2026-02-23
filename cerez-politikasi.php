<?php
require_once 'config/config.php';

$pageTitle = 'Çerez Politikası';

include 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <h1 class="mb-4 text-primary">
                            <i class="fas fa-cookie-bite me-2"></i>
                            Çerez Politikası
                        </h1>
                        
                        <p class="lead text-muted mb-5">
                            Son Güncelleme: <?php echo date('d.m.Y'); ?>
                        </p>

                        <div class="alert alert-info">
                            <p class="mb-0"><i class="fas fa-info-circle me-2"></i>Bu sayfa, web sitemizde kullanılan çerezler hakkında bilgi vermektedir. Çerez tercihlerinizi istediğiniz zaman değiştirebilirsiniz.</p>
                        </div>

                        <h4 class="mt-5 mb-3">Çerez Nedir?</h4>
                        <p>Çerezler, ziyaret ettiğiniz web sitesi tarafından bilgisayarınıza veya mobil cihazınıza yerleştirilen küçük metin dosyalarıdır. Çerezler, web sitelerinin daha verimli çalışmasını sağlar ve site sahiplerine bilgi sağlar.</p>

                        <h4 class="mt-5 mb-3">Neden Çerez Kullanıyoruz?</h4>
                        <ul>
                            <li><strong>Temel İşlevsellik:</strong> Sitenin düzgün çalışması için gerekli</li>
                            <li><strong>Performans:</strong> Site performansını analiz etmek ve iyileştirmek</li>
                            <li><strong>Kişiselleştirme:</strong> Tercihlerinizi hatırlayarak deneyiminizi geliştirmek</li>
                            <li><strong>Pazarlama:</strong> Size uygun içerik ve reklamlar göstermek</li>
                        </ul>

                        <h4 class="mt-5 mb-3">Çerez Türleri</h4>

                        <div class="accordion" id="cookieAccordion">
                            <!-- Zorunlu Çerezler -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#cookie1">
                                        <i class="fas fa-check-circle text-danger me-2"></i><strong>1. Zorunlu Çerezler</strong> (Devre Dışı Bırakılamaz)
                                    </button>
                                </h2>
                                <div id="cookie1" class="accordion-collapse collapse show" data-bs-parent="#cookieAccordion">
                                    <div class="accordion-body">
                                        <p>Bu çerezler web sitesinin çalışması için gereklidir ve sistemlerimizde kapatılamaz.</p>
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Çerez Adı</th>
                                                    <th>Amacı</th>
                                                    <th>Süre</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><code>PHPSESSID</code></td>
                                                    <td>Oturum yönetimi</td>
                                                    <td>Oturum sonuna kadar</td>
                                                </tr>
                                                <tr>
                                                    <td><code>cookie_consent</code></td>
                                                    <td>Çerez tercihlerinizi saklar</td>
                                                    <td>1 yıl</td>
                                                </tr>
                                                <tr>
                                                    <td><code>csrf_token</code></td>
                                                    <td>Güvenlik (CSRF koruması)</td>
                                                    <td>Oturum sonuna kadar</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Performans Çerezleri -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cookie2">
                                        <i class="fas fa-chart-line text-primary me-2"></i><strong>2. Performans ve Analitik Çerezler</strong>
                                    </button>
                                </h2>
                                <div id="cookie2" class="accordion-collapse collapse" data-bs-parent="#cookieAccordion">
                                    <div class="accordion-body">
                                        <p>Bu çerezler ziyaretçilerin web sitemizi nasıl kullandığını anlamamıza yardımcı olur.</p>
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Çerez Adı</th>
                                                    <th>Sağlayıcı</th>
                                                    <th>Amacı</th>
                                                    <th>Süre</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><code>_ga</code></td>
                                                    <td>Google Analytics</td>
                                                    <td>Kullanıcıları ayırt eder</td>
                                                    <td>2 yıl</td>
                                                </tr>
                                                <tr>
                                                    <td><code>_gid</code></td>
                                                    <td>Google Analytics</td>
                                                    <td>Kullanıcıları ayırt eder</td>
                                                    <td>24 saat</td>
                                                </tr>
                                                <tr>
                                                    <td><code>_gat</code></td>
                                                    <td>Google Analytics</td>
                                                    <td>İstek oranını azaltır</td>
                                                    <td>1 dakika</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- İşlevsel Çerezler -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cookie3">
                                        <i class="fas fa-cog text-success me-2"></i><strong>3. İşlevsel Çerezler</strong>
                                    </button>
                                </h2>
                                <div id="cookie3" class="accordion-collapse collapse" data-bs-parent="#cookieAccordion">
                                    <div class="accordion-body">
                                        <p>Bu çerezler gelişmiş işlevsellik ve kişiselleştirme sağlar.</p>
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Çerez Adı</th>
                                                    <th>Amacı</th>
                                                    <th>Süre</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><code>user_preferences</code></td>
                                                    <td>Kullanıcı tercihlerini saklar</td>
                                                    <td>6 ay</td>
                                                </tr>
                                                <tr>
                                                    <td><code>language</code></td>
                                                    <td>Dil tercihi</td>
                                                    <td>1 yıl</td>
                                                </tr>
                                                <tr>
                                                    <td><code>theme</code></td>
                                                    <td>Tema tercihi (açık/koyu mod)</td>
                                                    <td>1 yıl</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Pazarlama Çerezleri -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cookie4">
                                        <i class="fas fa-bullhorn text-warning me-2"></i><strong>4. Hedefleme ve Reklamcılık Çerezleri</strong>
                                    </button>
                                </h2>
                                <div id="cookie4" class="accordion-collapse collapse" data-bs-parent="#cookieAccordion">
                                    <div class="accordion-body">
                                        <p>Bu çerezler, ilgi alanlarınıza uygun reklamlar göstermek için kullanılır.</p>
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Çerez Adı</th>
                                                    <th>Sağlayıcı</th>
                                                    <th>Amacı</th>
                                                    <th>Süre</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><code>_fbp</code></td>
                                                    <td>Facebook</td>
                                                    <td>Reklam takibi</td>
                                                    <td>3 ay</td>
                                                </tr>
                                                <tr>
                                                    <td><code>fr</code></td>
                                                    <td>Facebook</td>
                                                    <td>Reklam hedefleme</td>
                                                    <td>3 ay</td>
                                                </tr>
                                                <tr>
                                                    <td><code>IDE</code></td>
                                                    <td>Google DoubleClick</td>
                                                    <td>Reklam etkinliği ölçümü</td>
                                                    <td>1 yıl</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4 class="mt-5 mb-3">Çerez Tercihlerinizi Yönetin</h4>
                        
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>Çerez Ayarlarını Değiştir</h5>
                                <p>Çerez tercihlerinizi istediğiniz zaman değiştirebilirsiniz. Aşağıdaki butona tıklayarak çerez ayarları penceresini açabilirsiniz:</p>
                                <button class="btn btn-primary" onclick="openCookieSettings()">
                                    <i class="fas fa-cog me-2"></i>Çerez Ayarlarını Aç
                                </button>
                            </div>
                        </div>

                        <div class="card bg-light mt-3">
                            <div class="card-body">
                                <h5>Tarayıcı Ayarları</h5>
                                <p>Çerezleri tarayıcınızın ayarlarından da yönetebilirsiniz:</p>
                                <ul>
                                    <li><strong>Chrome:</strong> Ayarlar > Gizlilik ve güvenlik > Çerezler</li>
                                    <li><strong>Firefox:</strong> Seçenekler > Gizlilik ve Güvenlik > Çerezler</li>
                                    <li><strong>Safari:</strong> Tercihler > Gizlilik > Çerezler</li>
                                    <li><strong>Edge:</strong> Ayarlar > Çerezler ve site izinleri</li>
                                </ul>
                                <p class="text-warning mb-0"><i class="fas fa-exclamation-triangle me-2"></i><strong>Uyarı:</strong> Zorunlu çerezleri engellerseniz, web sitesinin bazı özellikleri düzgün çalışmayabilir.</p>
                            </div>
                        </div>

                        <h4 class="mt-5 mb-3">Üçüncü Taraf Çerezleri</h4>
                        
                        <p>Web sitemizde aşağıdaki üçüncü taraf hizmetler çerez kullanabilir:</p>
                        <ul>
                            <li><strong>Google Analytics:</strong> Ziyaretçi istatistikleri (<a href="https://policies.google.com/privacy" target="_blank">Gizlilik Politikası</a>)</li>
                            <li><strong>Facebook Pixel:</strong> Reklam optimizasyonu (<a href="https://www.facebook.com/privacy/explanation" target="_blank">Gizlilik Politikası</a>)</li>
                            <li><strong>Google Ads:</strong> Reklam gösterimi (<a href="https://policies.google.com/technologies/ads" target="_blank">Reklam Politikası</a>)</li>
                        </ul>

                        <h4 class="mt-5 mb-3">Çerezlerin Silinmesi</h4>
                        
                        <p>Bilgisayarınızda veya mobil cihazınızda depolanan çerezleri istediğiniz zaman silebilirsiniz. Bunun için tarayıcınızın yardım menüsüne bakın veya yukarıdaki tarayıcı ayarları bölümünden yararlanın.</p>

                        <h4 class="mt-5 mb-3">İletişim</h4>
                        
                        <div class="alert alert-primary">
                            <p><strong>Çerezler hakkında sorularınız için:</strong></p>
                            <p><strong>E-posta:</strong> <?php echo escape(getSetting($pdo, 'contact_email')); ?></p>
                            <p class="mb-0"><strong>Telefon:</strong> <?php echo escape(getSetting($pdo, 'contact_phone')); ?></p>
                        </div>

                        <div class="text-center mt-5">
                            <a href="<?php echo SITE_URL; ?>/kvkk.php" class="btn btn-outline-primary me-2">
                                <i class="fas fa-shield-alt me-2"></i>KVKK
                            </a>
                            <a href="<?php echo SITE_URL; ?>/gizlilik.php" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-user-shield me-2"></i>Gizlilik Politikası
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
