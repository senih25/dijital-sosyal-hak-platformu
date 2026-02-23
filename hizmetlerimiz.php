<?php
require_once 'config/config.php';

// UTF-8 encoding ayarı
header('Content-Type: text/html; charset=utf-8');
$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_turkish_ci");

$pageTitle = 'Hizmetlerimiz';
$pageDescription = 'Profesyonel danışmanlık hizmetlerimiz ve dijital ürünlerimiz';

// Hizmetleri al
try {
    $stmtServices = $pdo->query("SELECT * FROM services WHERE status = 'active' ORDER BY id ASC");
    $services = $stmtServices->fetchAll();
} catch (PDOException $e) {
    error_log("Hizmetler yüklenemedi: " . $e->getMessage());
    $services = [];
}

// Dijital Ürünleri al (4 adet)
try {
    $stmtProducts = $pdo->query("SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT 4");
    $products = $stmtProducts->fetchAll();
} catch (PDOException $e) {
    $products = [];
}

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="hero-section" style="padding: 60px 0;" data-ab-test="hizmetler_hero_cta">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="mb-3" data-ab-variant="A">
                    <i class="fas fa-concierge-bell me-2"></i>
                    Hizmetlerimiz
                </h1>
                <p class="lead" data-ab-variant="A">
                    Size özel danışmanlık hizmetlerimiz ve dijital ürünlerimizle yanınızdayız
                </p>
                <p class="lead" data-ab-variant="B">
                    Haklarınızı hızla öğrenin: uzman danışmanlık ve dijital rehber paketleriyle hemen başlayın
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Danışmanlık Hizmetleri -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-3">
                <i class="fas fa-user-tie me-2"></i>
                Profesyonel Danışmanlık Hizmetleri
            </h2>
            <p class="text-muted">Deneyimli uzmanlarımızdan profesyonel destek alın</p>
        </div>

        <?php if (!empty($services)): ?>
            <div class="row g-4">
                <?php foreach ($services as $index => $service): ?>
                    <div class="col-lg-6">
                        <div class="service-card h-100 text-start" style="padding: 30px;">
                            <div class="d-flex align-items-start">
                                <div class="me-4">
                                    <i class="fas <?php echo escape($service['icon'] ?? 'fa-concierge-bell'); ?>" style="font-size: 3rem; color: var(--primary-color);"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="mb-3"><?php echo escape($service['title']); ?></h4>
                                    <p class="text-muted"><?php echo $service['short_description']; ?></p>
                                    
                                    <?php if ($service['price'] > 0): ?>
                                        <div class="mt-3">
                                            <span class="badge bg-success" style="font-size: 1.1rem;">
                                                <?php echo formatPrice($service['price']); ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-3">
                                            <span class="badge bg-info" style="font-size: 1rem;">
                                                Ücretsiz Danışmanlık
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-2 d-flex gap-2 flex-wrap justify-content-center">
                                        <a href="<?php echo SITE_URL; ?>/hizmet.php?slug=<?php echo escape($service['slug']); ?>" class="btn btn-primary flex-fill" style="font-size: 0.95rem; padding: 8px 20px; line-height: 1.4; min-width: 100px;">
                                            <i class="fas fa-info-circle" style="font-size: 0.9rem;"></i> Detay
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/iletisim.php?hizmet=<?php echo escape($service['slug']); ?>" class="btn btn-outline-primary flex-fill" style="font-size: 0.95rem; padding: 8px 20px; line-height: 1.4; min-width: 100px;">
                                            <i class="fas fa-phone" style="font-size: 0.9rem;"></i> Başvur
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                Şu anda aktif hizmet bulunmamaktadır.
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- E-Kitap Mağazası -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-3">
                <i class="fas fa-box-open me-2"></i>
                Dijital Ürünler
            </h2>
            <p class="text-muted">Bilgi dolu rehber ve danışmanlık paketlerimizi inceleyin</p>
        </div>

        <?php if (!empty($products)): ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="product-card">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo SITE_URL . '/' . escape($product['image']); ?>" alt="<?php echo escape($product['title']); ?>">
                            <?php else: ?>
                                <div style="height: 250px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-book" style="font-size: 4rem; color: white;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="mb-3"><?php echo escape($product['title']); ?></h5>
                                <p class="text-muted small"><?php echo escape(truncateText($product['short_description'] ?? $product['description'], 80)); ?></p>
                                
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <?php if ($product['discount_price'] && $product['discount_price'] < $product['price']): ?>
                                            <span class="price"><?php echo formatPrice($product['discount_price']); ?></span>
                                            <span class="old-price ms-2"><?php echo formatPrice($product['price']); ?></span>
                                        <?php else: ?>
                                            <span class="price"><?php echo formatPrice($product['price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-download me-1"></i><?php echo $product['sales_count']; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="<?php echo SITE_URL; ?>/urun.php?slug=<?php echo escape($product['slug']); ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-2"></i>İncele
                                    </a>
                                    <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo escape(addslashes($product['name'])); ?>', '<?php echo (float)$product['price']; ?>')" class="btn btn-success btn-sm" data-cta="true">
                                        <i class="fas fa-shopping-cart me-2"></i>Satın Al
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                Şu anda mağazada ürün bulunmamaktadır. Yakında yeni ürünler eklenecektir.
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Neden Bizi Tercih Etmelisiniz -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-3">
                <i class="fas fa-star me-2"></i>
                Neden Bizi Tercih Etmelisiniz?
            </h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-certificate" style="font-size: 3rem; color: var(--primary-color);"></i>
                    </div>
                    <h5>Uzman Kadro</h5>
                    <p class="text-muted">Alanında deneyimli sosyal hizmet uzmanları</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-shield-alt" style="font-size: 3rem; color: var(--secondary-color);"></i>
                    </div>
                    <h5>Güvenilir Bilgi</h5>
                    <p class="text-muted">Güncel mevzuat ve resmi kaynaklara dayalı</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-headset" style="font-size: 3rem; color: var(--accent-color);"></i>
                    </div>
                    <h5>7/24 Destek</h5>
                    <p class="text-muted">Sorularınız için her zaman ulaşılabilir</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-user-check" style="font-size: 3rem; color: var(--primary-color);"></i>
                    </div>
                    <h5>Kişiselleştirilmiş</h5>
                    <p class="text-muted">Size özel çözümler ve danışmanlık</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="mb-3">Haklarınızı Öğrenmeye Hazır mısınız?</h3>
                <p class="mb-0">Profesyonel danışmanlarımızla iletişime geçin, size özel çözümler sunalım.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="<?php echo SITE_URL; ?>/iletisim.php" class="btn btn-light btn-lg">
                    <i class="fas fa-phone me-2"></i>Hemen İletişime Geç
                </a>
            </div>
        </div>
    </div>
</section>

<script>
// Sepete ekle fonksiyonu
function addToCart(productId, productName, productPrice) {
    <?php if (!isset($_SESSION['user_id'])): ?>
        // Giriş yapmamış kullanıcılar için
        if (confirm('Satın alma işlemi için giriş yapmanız gerekmektedir. Giriş sayfasına yönlendirilmek ister misiniz?')) {
            window.location.href = '<?php echo SITE_URL; ?>/login.php?redirect=hizmetlerimiz';
        }
        return;
    <?php endif; ?>
    

    if (window.AnalyticsTracker && typeof window.AnalyticsTracker.trackAddToCart === 'function') {
        window.AnalyticsTracker.trackAddToCart({
            id: productId,
            name: productName || 'Ürün',
            price: productPrice || 0,
            quantity: 1,
            category: 'digital_product',
            currency: 'TRY'
        });

        if (typeof window.AnalyticsTracker.trackABConversion === 'function') {
            window.AnalyticsTracker.trackABConversion('hizmetler_hero_cta', 'add_to_cart_click', productPrice || 0);
        }
    }

    // AJAX ile sepete ekle
    fetch('<?php echo SITE_URL; ?>/cart-add.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Başarılı
            alert(data.productName + ' sepete eklendi!');
            
            // Sepet sayısını güncelle
            const cartBadge = document.getElementById('cart-count');
            if (cartBadge) {
                cartBadge.textContent = data.cartCount;
            } else if (data.cartCount > 0) {
                // Badge yoksa oluştur
                const cartLink = document.querySelector('a[href*="cart.php"]');
                if (cartLink) {
                    const badge = document.createElement('span');
                    badge.id = 'cart-count';
                    badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                    badge.textContent = data.cartCount;
                    cartLink.querySelector('i').parentElement.classList.add('position-relative');
                    cartLink.querySelector('i').parentElement.appendChild(badge);
                }
            }
            
            // Sepete git sorusu
            if (confirm('Ürün sepete eklendi! Sepete gitmek ister misiniz?')) {
                window.location.href = '<?php echo SITE_URL; ?>/cart.php';
            }
        } else {
            // Hata
            if (data.redirect === 'login') {
                if (confirm(data.message + ' Giriş sayfasına yönlendirilmek ister misiniz?')) {
                    window.location.href = '<?php echo SITE_URL; ?>/login.php?redirect=hizmetlerimiz';
                }
            } else {
                alert('Hata: ' + data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu. Lütfen tekrar deneyin.');
    });
}
</script>


<hr class="my-5">

<h3 class="text-center mb-4">
  <i class="fas fa-tools text-primary"></i>
  Online Araçlar
</h3>

<p class="text-center text-muted mb-4">
  Başvurularınız için gerekli belgeleri hızlıca hazırlayabileceğiniz dijital araçlar.
</p>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm text-center">
      <div class="card-body">
        <h5 class="card-title">PDF Araçları</h5>
        <p class="card-text">
          PDF sıkıştırma, dönüştürme, birleştirme ve daha fazlası.
        </p>
        <a href="/pdf-araclari.php" class="btn btn-primary">
          Araçlara Git
        </a>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
