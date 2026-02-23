<?php
require_once 'config/config.php';

// UTF-8 encoding ayarı
header('Content-Type: text/html; charset=utf-8');
$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_turkish_ci");

// Slug parametresi
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    redirect(SITE_URL . '/hizmetlerimiz.php');
}

// Hizmeti getir
try {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE slug = ? AND status = 'active'");
    $stmt->execute([$slug]);
    $service = $stmt->fetch();
    
    if (!$service) {
        redirect(SITE_URL . '/hizmetlerimiz.php');
    }
} catch (PDOException $e) {
    error_log("Hizmet detay hatası: " . $e->getMessage());
    redirect(SITE_URL . '/hizmetlerimiz.php');
}

$pageTitle = $service['title'];
$pageDescription = $service['short_description'];

include 'includes/header.php';
?>

<!-- Hizmet Detay -->
<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Ana Sayfa</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/hizmetlerimiz.php">Hizmetlerimiz</a></li>
                <li class="breadcrumb-item active"><?php echo escape($service['title']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-4">
                                <i class="<?php echo escape($service['icon']); ?>" style="font-size: 4rem; color: var(--primary-color);"></i>
                            </div>
                            <div>
                                <h1 class="mb-2"><?php echo escape($service['title']); ?></h1>
                                <?php if ($service['price'] > 0): ?>
                                    <span class="badge bg-success" style="font-size: 1.2rem;">
                                        <?php echo formatPrice($service['price']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-info" style="font-size: 1.1rem;">
                                        Ücretsiz Danışmanlık
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <?php if ($service['slug'] === 'pdf-araclari'): ?>

<hr class="my-5">

<h5 class="text-primary mb-3">
    <i class="fas fa-file-pdf me-2"></i>
    Online PDF Araçları
</h5>

<div id="pdf-widget-container" style="min-height:700px;"></div>

<script src="https://api.process-machine.com/solutions/scripts/widget.js"></script>
<script>
    if (typeof SolutionWidget !== 'undefined') {
        const widget = new SolutionWidget({
            sid: 29,
            containerId: "#pdf-widget-container",
            locale: "tr"
        });
        widget.build();
    } else {
        console.error('SolutionWidget yüklenmedi');
    }
</script>

<?php endif; ?>


                        <hr class="my-4">

                        <div>
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-clipboard-list me-2"></i>Detaylı Açıklama
                            </h5>
                            <div class="service-description">
                                <?php echo nl2br(escape($service['description'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- İletişim Kartı -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-4">
                            <i class="fas fa-phone-alt me-2 text-primary"></i>
                            Hemen Başvurun
                        </h5>
                        
                        <div class="d-grid gap-2">
                            <a href="<?php echo SITE_URL; ?>/iletisim.php?hizmet=<?php echo escape($service['slug']); ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-envelope me-2"></i>
                                İletişim Formu
                            </a>
                            
                            <?php 
                            $whatsapp = getSetting($pdo, 'whatsapp_number');
                            if ($whatsapp): 
                            ?>
                                <a href="https://wa.me/<?php echo $whatsapp; ?>?text=Merhaba, <?php echo urlencode($service['title']); ?> hakkında bilgi almak istiyorum." 
                                   class="btn btn-success btn-lg" target="_blank">
                                    <i class="fab fa-whatsapp me-2"></i>
                                    WhatsApp
                                </a>
                            <?php endif; ?>
                            
                            <?php 
                            $phone = getSetting($pdo, 'contact_phone');
                            if ($phone): 
                            ?>
                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $phone); ?>" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-phone me-2"></i>
                                    <?php echo escape($phone); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Diğer Hizmetler -->
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-4">
                            <i class="fas fa-concierge-bell me-2 text-primary"></i>
                            Diğer Hizmetlerimiz
                        </h5>
                        
                        <?php
                        try {
                            $otherStmt = $pdo->prepare("SELECT title, slug, icon FROM services WHERE status = 'active' AND id != ? ORDER BY id ASC LIMIT 5");
                            $otherStmt->execute([$service['id']]);
                            $otherServices = $otherStmt->fetchAll();
                            
                            if ($otherServices):
                        ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($otherServices as $other): ?>
                                    <a href="<?php echo SITE_URL; ?>/hizmet.php?slug=<?php echo escape($other['slug']); ?>" 
                                       class="list-group-item list-group-item-action">
                                        <i class="<?php echo escape($other['icon']); ?> me-2 text-primary"></i>
                                        <?php echo escape($other['title']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php 
                            endif;
                        } catch (PDOException $e) {
                            // Hata sessizce loglanır
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="mb-3">Size Nasıl Yardımcı Olabiliriz?</h3>
                <p class="mb-0 text-muted">Profesyonel danışmanlarımızla iletişime geçin, size özel çözümler sunalım.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="<?php echo SITE_URL; ?>/iletisim.php?hizmet=<?php echo escape($service['slug']); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane me-2"></i>Hemen Başvur
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.service-description {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #555;
}

.breadcrumb {
    background-color: #f8f9fa;
    padding: 12px 20px;
    border-radius: 8px;
}
</style>

<?php include 'includes/footer.php'; ?>
