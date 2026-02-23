<?php
require_once '../config/config.php';

// UTF-8 encoding ayarı
$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_turkish_ci");

// Admin kontrolü
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/login.php');
}

$pageTitle = 'Sosyal Medya Ayarları';

// Form gönderildi mi?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sosyal medya URL'lerini güncelle
        $socialMediaSettings = [
            'facebook_url',
            'instagram_url',
            'twitter_url',
            'linkedin_url',
            'youtube_url',
            'whatsapp_number'
        ];
        
        foreach ($socialMediaSettings as $key) {
            $value = $_POST[$key] ?? '';
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
        
        // Görünürlük ayarlarını güncelle
        $visibilitySettings = [
            'show_facebook',
            'show_instagram',
            'show_twitter',
            'show_linkedin',
            'show_youtube',
            'show_whatsapp'
        ];
        
        foreach ($visibilitySettings as $key) {
            $value = isset($_POST[$key]) ? '1' : '0';
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
        
        setFlashMessage('success', 'Sosyal medya ayarları başarıyla güncellendi!');
        redirect('social-media.php');
        
    } catch (Exception $e) {
        setFlashMessage('error', 'Bir hata oluştu: ' . $e->getMessage());
    }
}

include 'includes/admin_header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-share-alt me-2"></i>
        Sosyal Medya Ayarları
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Sosyal Medya Ayarları</li>
    </ol>

    <?php 
    $flash = getFlashMessage();
    if ($flash): 
        $alertClass = $flash['type'] == 'success' ? 'alert-success' : ($flash['type'] == 'error' ? 'alert-danger' : 'alert-info');
    ?>
        <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show" role="alert">
            <?php echo escape($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-link me-2"></i>
                    Sosyal Medya Hesapları
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        
                        <!-- Facebook -->
                        <div class="mb-4 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fab fa-facebook text-primary me-2"></i>
                                    Facebook
                                </h5>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="show_facebook" name="show_facebook" 
                                           <?php echo getSetting($pdo, 'show_facebook') == '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="show_facebook">
                                        Görünür
                                    </label>
                                </div>
                            </div>
                            <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                                   placeholder="https://facebook.com/sayfaadi" 
                                   value="<?php echo escape(getSetting($pdo, 'facebook_url')); ?>">
                        </div>

                        <!-- Instagram -->
                        <div class="mb-4 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fab fa-instagram text-danger me-2"></i>
                                    Instagram
                                </h5>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="show_instagram" name="show_instagram" 
                                           <?php echo getSetting($pdo, 'show_instagram') == '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="show_instagram">
                                        Görünür
                                    </label>
                                </div>
                            </div>
                            <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                                   placeholder="https://instagram.com/kullaniciadi" 
                                   value="<?php echo escape(getSetting($pdo, 'instagram_url')); ?>">
                        </div>

                        <!-- Twitter -->
                        <div class="mb-4 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fab fa-twitter text-info me-2"></i>
                                    Twitter (X)
                                </h5>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="show_twitter" name="show_twitter" 
                                           <?php echo getSetting($pdo, 'show_twitter') == '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="show_twitter">
                                        Görünür
                                    </label>
                                </div>
                            </div>
                            <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                                   placeholder="https://twitter.com/kullaniciadi" 
                                   value="<?php echo escape(getSetting($pdo, 'twitter_url')); ?>">
                        </div>

                        <!-- LinkedIn -->
                        <div class="mb-4 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fab fa-linkedin text-primary me-2"></i>
                                    LinkedIn
                                </h5>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="show_linkedin" name="show_linkedin" 
                                           <?php echo getSetting($pdo, 'show_linkedin') == '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="show_linkedin">
                                        Görünür
                                    </label>
                                </div>
                            </div>
                            <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                   placeholder="https://linkedin.com/company/sirket" 
                                   value="<?php echo escape(getSetting($pdo, 'linkedin_url')); ?>">
                        </div>

                        <!-- YouTube -->
                        <div class="mb-4 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fab fa-youtube text-danger me-2"></i>
                                    YouTube
                                </h5>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="show_youtube" name="show_youtube" 
                                           <?php echo getSetting($pdo, 'show_youtube') == '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="show_youtube">
                                        Görünür
                                    </label>
                                </div>
                            </div>
                            <input type="url" class="form-control" id="youtube_url" name="youtube_url" 
                                   placeholder="https://youtube.com/@kanaladi" 
                                   value="<?php echo escape(getSetting($pdo, 'youtube_url')); ?>">
                        </div>

                        <!-- WhatsApp -->
                        <div class="mb-4 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fab fa-whatsapp text-success me-2"></i>
                                    WhatsApp
                                </h5>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="show_whatsapp" name="show_whatsapp" 
                                           <?php echo getSetting($pdo, 'show_whatsapp') == '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="show_whatsapp">
                                        Görünür
                                    </label>
                                </div>
                            </div>
                            <input type="tel" class="form-control" id="whatsapp_number" name="whatsapp_number" 
                                   placeholder="905xxxxxxxxx (Sadece rakam)" 
                                   value="<?php echo escape(getSetting($pdo, 'whatsapp_number')); ?>">
                            <small class="form-text text-muted">Ülke kodu ile birlikte, boşluksuz yazın (örn: 905551234567)</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>
                                Ayarları Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i>
                    Kullanım Bilgisi
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Görünürlük Ayarları</h6>
                    <p class="small text-muted">
                        Her sosyal medya platformu için ayrı ayrı görünürlük ayarlayabilirsiniz. 
                        Kapalı olan platformlar header ve footer'da gösterilmeyecektir.
                    </p>
                    
                    <hr>
                    
                    <h6 class="mb-3">URL Formatları</h6>
                    <ul class="small text-muted">
                        <li><strong>Facebook:</strong> https://facebook.com/sayfaadi</li>
                        <li><strong>Instagram:</strong> https://instagram.com/kullaniciadi</li>
                        <li><strong>Twitter:</strong> https://twitter.com/kullaniciadi</li>
                        <li><strong>LinkedIn:</strong> https://linkedin.com/company/sirket</li>
                        <li><strong>YouTube:</strong> https://youtube.com/@kanaladi</li>
                        <li><strong>WhatsApp:</strong> Sadece rakamlar (905551234567)</li>
                    </ul>
                    
                    <hr>
                    
                    <h6 class="mb-3">İpuçları</h6>
                    <ul class="small text-muted">
                        <li>URL'leri tam olarak girin (https:// ile başlayan)</li>
                        <li>WhatsApp için ülke kodu dahil sadece rakam yazın</li>
                        <li>Görünür yapmak istediğiniz platformların switch'lerini açık bırakın</li>
                        <li>Boş bırakılan alanlar otomatik olarak gizlenecektir</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
