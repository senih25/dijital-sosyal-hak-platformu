<?php
require_once 'config/config.php';

// UTF-8 encoding ayarı
$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_turkish_ci");

$pageTitle = 'İletişim';
$pageDescription = 'Bizimle iletişime geçin';

$success = false;
$error = '';

// Form gönderildi mi?
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token'])) {
    if (verifyCSRFToken($_POST['csrf_token'])) {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $requestType = $_POST['request_type'] ?? 'diger';
        $kvkkConsent = isset($_POST['kvkk_consent']) ? 1 : 0;
        
        if (empty($name) || empty($email) || empty($message)) {
            $error = 'Lütfen tüm zorunlu alanları doldurun.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Geçerli bir e-posta adresi girin.';
        } elseif (!$kvkkConsent) {
            $error = 'KVKK aydınlatma metnini onaylamalısınız.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, request_type, kvkk_consent) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $phone, $subject, $message, $requestType, $kvkkConsent]);
                
                $success = true;
                setFlashMessage('Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.', 'success');
                redirect(SITE_URL . '/iletisim.php?success=1');
            } catch (PDOException $e) {
                $error = 'Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.';
            }
        }
    } else {
        $error = 'Güvenlik doğrulaması başarısız. Lütfen tekrar deneyin.';
    }
}

// Başarı parametresi kontrol
if (isset($_GET['success'])) {
    $success = true;
}

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="hero-section" style="padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="mb-3">
                    <i class="fas fa-envelope me-2"></i>
                    İletişim
                </h1>
                <p class="lead">
                    Sorularınız için bizimle iletişime geçin
                </p>
            </div>
        </div>
    </div>
</section>

<!-- İletişim Formu ve Bilgiler -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- İletişim Formu -->
            <div class="col-lg-7">
                <div class="contact-form">
                    <h3 class="mb-4">
                        <i class="fas fa-paper-plane me-2"></i>
                        Bize Mesaj Gönderin
                    </h3>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo escape($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            Mesajınız başarıyla gönderildi! En kısa sürede size dönüş yapacağız.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="Adınız ve soyadınız">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-posta <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="ornek@email.com">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+90 5XX XXX XX XX">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="request_type" class="form-label">Talep Türü <span class="text-danger">*</span></label>
                                <select class="form-select" id="request_type" name="request_type" required>
                                    <option value="">Seçiniz...</option>
                                    <option value="danismanlik">Danışmanlık</option>
                                    <option value="bilgi">Bilgi Alma</option>
                                    <option value="destek">Destek</option>
                                    <option value="diger">Diğer</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label for="subject" class="form-label">Konu</label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="Mesajınızın konusu">
                            </div>
                            
                            <div class="col-12">
                                <label for="message" class="form-label">Mesajınız <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="6" required placeholder="Mesajınızı buraya yazın..."></textarea>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="kvkk_consent" name="kvkk_consent" required>
                                    <label class="form-check-label" for="kvkk_consent">
                                        <a href="<?php echo SITE_URL; ?>/kvkk.php" target="_blank">KVKK Aydınlatma Metni</a>'ni okudum ve kabul ediyorum. <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Mesajı Gönder
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- İletişim Bilgileri -->
            <div class="col-lg-5">
                <div class="contact-info-box">
                    <h4 class="mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        İletişim Bilgileri
                    </h4>
                    
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Adres</strong>
                            <p><?php echo nl2br(escape(getSetting($pdo, 'contact_address', 'Türkiye'))); ?></p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Telefon</strong>
                            <p><?php echo escape(getSetting($pdo, 'contact_phone')); ?></p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>E-posta</strong>
                            <p><?php echo escape(getSetting($pdo, 'contact_email')); ?></p>
                        </div>
                    </div>
                    
                    <?php if (getSetting($pdo, 'whatsapp_number')): ?>
                    <div class="info-item">
                        <i class="fab fa-whatsapp"></i>
                        <div>
                            <strong>WhatsApp</strong>
                            <p>
                                <a href="https://wa.me/<?php echo escape(getSetting($pdo, 'whatsapp_number')); ?>" target="_blank" class="text-white">
                                    WhatsApp ile İletişim
                                </a>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Çalışma Saatleri</strong>
                            <p>
                                Pazartesi - Cuma: 09:00 - 18:00<br>
                                Cumartesi: 10:00 - 14:00<br>
                                Pazar: Kapalı
                            </p>
                        </div>
                    </div>
                    
                    <hr class="bg-white my-4">
                    
                    <h5 class="mb-3">Sosyal Medya</h5>
                    <div class="d-flex gap-3">
                        <?php if (getSetting($pdo, 'facebook_url')): ?>
                            <a href="<?php echo escape(getSetting($pdo, 'facebook_url')); ?>" target="_blank" class="btn btn-light btn-sm">
                                <i class="fab fa-facebook"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (getSetting($pdo, 'instagram_url')): ?>
                            <a href="<?php echo escape(getSetting($pdo, 'instagram_url')); ?>" target="_blank" class="btn btn-light btn-sm">
                                <i class="fab fa-instagram"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (getSetting($pdo, 'linkedin_url')): ?>
                            <a href="<?php echo escape(getSetting($pdo, 'linkedin_url')); ?>" target="_blank" class="btn btn-light btn-sm">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (getSetting($pdo, 'youtube_url')): ?>
                            <a href="<?php echo escape(getSetting($pdo, 'youtube_url')); ?>" target="_blank" class="btn btn-light btn-sm">
                                <i class="fab fa-youtube"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Hakkımızda -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="text-center mb-5">
                    <h2 class="mb-3">
                        <i class="fas fa-users me-2"></i>
                        Hakkımızda
                    </h2>
                </div>
                
                <div class="bg-white p-5 rounded shadow-sm">
                    <h4 class="mb-3">Misyonumuz</h4>
                    <p class="lead">
                        Türkiye'de engelli, yaşlı ve kronik hasta bireylerin sosyal haklarına erişimini kolaylaştırmak, 
                        doğru ve güncel bilgi ile toplumsal farkındalık yaratmaktır.
                    </p>
                    
                    <h4 class="mb-3 mt-5">Vizyonumuz</h4>
                    <p class="lead">
                        Herkesin sosyal haklarını bildiği, bu haklardan faydalanabildiği ve sosyal adalete dayalı 
                        bir toplum inşa etmektir.
                    </p>
                    
                    <h4 class="mb-3 mt-5">Değerlerimiz</h4>
                    <div class="row g-4 mt-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-heart text-primary me-3" style="font-size: 2rem;"></i>
                                <div>
                                    <h5>Saygı</h5>
                                    <p class="text-muted">Her bireyin onuruna ve haklarına saygı gösteririz</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-eye text-primary me-3" style="font-size: 2rem;"></i>
                                <div>
                                    <h5>Şeffaflık</h5>
                                    <p class="text-muted">Tüm süreçlerimizde açık ve şeffaf davranırız</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-hands-helping text-primary me-3" style="font-size: 2rem;"></i>
                                <div>
                                    <h5>Empati</h5>
                                    <p class="text-muted">İnsanları anlama ve onlarla birlikte hareket ederiz</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-seedling text-primary me-3" style="font-size: 2rem;"></i>
                                <div>
                                    <h5>Sosyal Fayda</h5>
                                    <p class="text-muted">Topluma değer katan çalışmalar yaparız</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
