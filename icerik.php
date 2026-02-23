<?php
require_once 'config/config.php';

// UTF-8
header('Content-Type: text/html; charset=utf-8');
$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_turkish_ci");

// Slug al - URL decode yap
$slug = $_GET['slug'] ?? '';
$slug = urldecode($slug); // urlencode ile gönderildiği için decode et
$slug = trim($slug);

if (empty($slug)) {
    redirect(SITE_URL . '/rehberlik.php');
}

// İçeriği getir
try {
    $stmt = $pdo->prepare("
        SELECT c.*, 
               cat.name AS category_name, 
               u.name AS author_name,
               u.bio  AS author_bio
        FROM contents c
        LEFT JOIN categories cat ON c.category_id = cat.id
        LEFT JOIN users u ON c.author_id = u.id
        WHERE c.slug = ? AND c.status = 'published'
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    $content = $stmt->fetch();

    if (!$content) {
        // Debug: Slug'u göster ve içerik bulunamadı mesajı ver
        $pageTitle = 'İçerik Bulunamadı';
        include 'includes/header.php';
        echo '<div class="container py-5 text-center">';
        echo '<h3 class="text-danger">İçerik Bulunamadı</h3>';
        echo '<p class="text-muted">Aranan slug: <code>' . htmlspecialchars($slug) . '</code></p>';
        echo '<a href="rehberlik.php" class="btn btn-primary">Geri Dön</a>';
        echo '</div>';
        include 'includes/footer.php';
        exit;
    }

    // Görüntülenme sayısını artır (hata verse bile site bozulmasın)
    try {
        $updateViews = $pdo->prepare("UPDATE contents SET views = views + 1 WHERE id = ?");
        $updateViews->execute([$content['id']]);
    } catch (PDOException $e) {
        error_log("Views update error: " . $e->getMessage());
    }

} catch (PDOException $e) {
    error_log("İçerik detay hatası: " . $e->getMessage());
    redirect(SITE_URL . '/rehberlik.php');
}

// Güvenli varsayılanlar
$content['views'] = isset($content['views']) ? (int)$content['views'] : 0;

$pageTitle       = $content['title'];
$pageDescription = $content['summary'] ?? substr(strip_tags($content['content']), 0, 150);

// PDF linki için mevcut sayfa URL'si
$currentUrl = SITE_URL . '/icerik.php?slug=' . urlencode($slug);

include 'includes/header.php';
?>

<section class="py-5">
    <div class="container">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Ana Sayfa</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/rehberlik.php">Rehberlik</a></li>
                <li class="breadcrumb-item active"><?php echo escape($content['title']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Ana içerik -->
            <div class="col-lg-8">

                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">

                        <h1 class="mb-3"><?php echo escape($content['title']); ?></h1>

                        <!-- Meta bilgiler -->
                        <div class="text-muted mb-3">
                            <i class="fas fa-calendar me-1"></i>
                            <?php echo formatDate($content['published_at']); ?>

                            <?php if (!empty($content['author_name'])): ?>
                                <span class="ms-3">
                                    <i class="fas fa-user me-1"></i>
                                    <?php echo escape($content['author_name']); ?>
                                </span>
                            <?php endif; ?>

                            <span class="ms-3">
                                <i class="fas fa-eye me-1"></i>
                                <?php echo number_format($content['views'] + 1); ?> görüntülenme
                            </span>
                        </div>

                        <!-- Kapak görseli -->
                        <?php if (!empty($content['image'])): ?>
                            <img src="<?php echo SITE_URL . '/' . escape($content['image']); ?>" 
                                 class="img-fluid rounded mb-4" alt="<?php echo escape($content['title']); ?>">
                        <?php endif; ?>

                        <!-- İçerik gövdesi -->
                        <div class="content-body" style="font-size:1.1rem; line-height:1.8;">
                            <?php echo nl2br($content['content']); ?>
                        </div>

                        <hr class="my-4">

                        <!-- PDF butonu (sayfanın PDF'ini dış servisle açan basit çözüm) -->
                        <h5 class="mb-3">
                            <i class="fas fa-file-pdf me-2 text-danger"></i>PDF Olarak Görüntüle / Yazdır
                        </h5>
                        <a href="https://www.printfriendly.com/print?url=<?php echo urlencode($currentUrl); ?>" 
                           target="_blank" class="btn btn-outline-danger mb-4">
                            <i class="fas fa-file-pdf me-2"></i>PDF / Yazdır Görünümü
                        </a>

                        <!-- Sosyal Medya Paylaşım -->
                        <h5 class="mb-3"><i class="fas fa-share-alt me-2"></i>Paylaş</h5>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <a class="btn btn-primary"
                               href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($currentUrl); ?>"
                               target="_blank">
                                <i class="fab fa-facebook-f me-1"></i> Facebook
                            </a>

                            <a class="btn btn-info text-white"
                               href="https://twitter.com/intent/tweet?url=<?php echo urlencode($currentUrl); ?>&text=<?php echo urlencode($content['title']); ?>"
                               target="_blank">
                                <i class="fab fa-twitter me-1"></i> Twitter
                            </a>

                            <a class="btn btn-success"
                               href="https://wa.me/?text=<?php echo urlencode($content['title'].' - '.$currentUrl); ?>"
                               target="_blank">
                                <i class="fab fa-whatsapp me-1"></i> WhatsApp
                            </a>

                            <a class="btn btn-secondary"
                               href="mailto:?subject=<?php echo rawurlencode($content['title']); ?>&body=<?php echo rawurlencode($currentUrl); ?>">
                                <i class="fas fa-envelope me-1"></i> E-posta
                            </a>
                        </div>

                        <!-- Yorum Alanı (form, backend'e yük bindirmeden basit çözüm) -->
                        <hr class="my-4">
                        <h5 class="mb-3"><i class="fas fa-comments me-2"></i>Görüşlerinizi Paylaşın</h5>
                        <p class="text-muted">
                            Şu an için yorumlar doğrudan sistemde saklanmıyor; formu doldurduğunuzda mesajınız bize e-posta olarak iletilir.
                        </p>
                        <form method="post" action="<?php echo SITE_URL; ?>/iletisim.php">
                            <input type="hidden" name="konu" value="İçerik Yorumu: <?php echo escape($content['title']); ?>">
                            <div class="mb-3">
                                <label class="form-label">Adınız Soyadınız</label>
                                <input type="text" name="adsoyad" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">E-posta Adresiniz</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Yorumunuz</label>
                                <textarea name="mesaj" rows="4" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Yorumu Gönder
                            </button>
                        </form>

                    </div>
                </div>

                <!-- Önerilen İçerikler -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><i class="fas fa-lightbulb me-2"></i>Önerilen İçerikler</h5>

                        <?php
                        $recommended = [];
                        try {
                            $stmtRec = $pdo->prepare("
                                SELECT title, slug 
                                FROM contents 
                                WHERE status='published' 
                                  AND id != ? 
                                ORDER BY views DESC, published_at DESC 
                                LIMIT 4
                            ");
                            $stmtRec->execute([$content['id']]);
                            $recommended = $stmtRec->fetchAll();
                        } catch (PDOException $e) {
                            error_log("Recommended contents error: " . $e->getMessage());
                        }
                        ?>

                        <?php if ($recommended): ?>
                            <ul class="list-group">
                                <?php foreach ($recommended as $rec): ?>
                                    <li class="list-group-item">
                                        <a href="<?php echo SITE_URL; ?>/icerik.php?slug=<?php echo escape($rec['slug']); ?>">
                                            <?php echo escape($rec['title']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted mb-0">Şu anda önerilecek başka içerik bulunamadı.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Sağ kolon -->
            <div class="col-lg-4">

                <!-- Yazar Kutusu -->
                <?php if (!empty($content['author_name']) || !empty($content['author_bio'])): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="mb-3">
                                <i class="fas fa-user-circle me-2 text-primary"></i>
                                Yazar
                            </h5>
                            <p class="fw-bold mb-1">
                                <?php echo escape($content['author_name'] ?? 'Bilinmeyen Yazar'); ?>
                            </p>
                            <?php if (!empty($content['author_bio'])): ?>
                                <p class="text-muted mb-0">
                                    <?php echo nl2br(escape($content['author_bio'])); ?>
                                </p>
                            <?php else: ?>
                                <p class="text-muted mb-0">
                                    Bu içerik, Sosyal Hizmet Rehberlik ekibi tarafından hazırlanmıştır.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Kategori Kutusu -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3">
                            <i class="fas fa-layer-group me-2 text-primary"></i>
                            Kategori
                        </h5>
                        <p class="text-muted mb-0">
                            <?php echo escape($content['category_name'] ?? 'Belirtilmemiş'); ?>
                        </p>
                    </div>
                </div>

                <!-- Küçük CTA -->
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-3">
                            <i class="fas fa-hands-helping me-2 text-primary"></i>
                            Danışmanlık İhtiyacınız mı Var?
                        </h5>
                        <p class="text-muted">
                            Bu içerikle ilgili profesyonel destek almak isterseniz bizimle iletişime geçebilirsiniz.
                        </p>
                        <a href="<?php echo SITE_URL; ?>/iletisim.php?konu=<?php echo urlencode($content['title']); ?>" 
                           class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i>İletişim Formu
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>

<style>
.content-body {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #444;
}
.breadcrumb {
    background-color: #f8f9fa;
    padding: 12px 20px;
    border-radius: 8px;
}
</style>

<?php include 'includes/footer.php'; ?>
