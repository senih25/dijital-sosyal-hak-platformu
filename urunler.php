<?php
require_once 'config/config.php';

$pageTitle = 'Hizmet Paketlerimiz';
$pageDescription = 'Sosyal hak danışmanlığı ve takip hizmetlerimiz için özel paketlerimizi inceleyin.';

// Filtreleme
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

$query = "SELECT p.*, pc.name as category_name 
          FROM products p 
          LEFT JOIN product_categories pc ON p.category_id = pc.id 
          WHERE p.status = 'active'";
$params = [];

if ($category) {
    $query .= " AND p.category_id = ?";
    $params[] = $category;
}

if ($search) {
    $query .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Sıralama
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY p.price DESC";
        break;
    case 'name':
        $query .= " ORDER BY p.title ASC";
        break;
    default: // newest
        $query .= " ORDER BY p.created_at DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Kategorileri al
$categories = $pdo->query("SELECT * FROM product_categories ORDER BY name")->fetchAll();

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header bg-gradient py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center text-white">
                <h1 class="display-4 mb-3 fw-bold">
                    <i class="fas fa-box-open me-3"></i>
                    Hizmet Paketlerimiz
                </h1>
                <p class="lead">Size en uygun danışmanlık ve takip paketini seçin</p>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="py-5">
    <div class="container">
        <!-- Filter Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Paket ara..." value="<?php echo escape($search); ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-select">
                                    <option value="">Tüm Kategoriler</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo escape($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="sort" class="form-select">
                                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>En Yeni</option>
                                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Fiyat: Düşükten Yükseğe</option>
                                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Fiyat: Yüksekten Düşüğe</option>
                                    <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>İsme Göre</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Ara
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <?php if (count($products) > 0): ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow hover-card package-card">
                            <div class="card-body p-4">
                                <?php if ($product['category_name']): ?>
                                    <div class="text-center mb-3">
                                        <span class="badge bg-primary bg-gradient fs-6 px-4 py-2">
                                            <?php echo escape($product['category_name']); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <h3 class="card-title text-center mb-3">
                                    <?php echo escape($product['title']); ?>
                                </h3>
                                
                                <?php if ($product['short_description']): ?>
                                    <p class="text-center text-muted mb-4">
                                        <?php echo escape($product['short_description']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="text-center mb-4">
                                    <h2 class="text-primary mb-0">
                                        <?php echo number_format($product['price'], 2); ?> ₺
                                    </h2>
                                    <small class="text-muted">KDV Dahil</small>
                                </div>
                                
                                <div class="package-details">
                                    <?php 
                                    // HTML içeriği güvenli şekilde göster
                                    echo $product['description']; 
                                    ?>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-transparent border-top-0 p-4">
                                <div class="d-grid gap-2">
                                    <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-success btn-lg">
                                        <i class="fas fa-shopping-cart me-2"></i>Sepete Ekle
                                    </button>
                                    <a href="<?php echo SITE_URL; ?>/iletisim.php?paket=<?php echo urlencode($product['title']); ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-phone-alt me-2"></i>Bilgi Al
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h3 class="text-muted">Paket Bulunamadı</h3>
                <p class="text-muted">Aradığınız kriterlere uygun paket bulunmamaktadır.</p>
                <a href="<?php echo SITE_URL; ?>/urunler.php" class="btn btn-primary">
                    <i class="fas fa-redo me-2"></i>Tüm Paketler
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.hover-card {
    transition: transform 0.3s, box-shadow 0.3s;
}

.hover-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2) !important;
}

.package-card {
    border-radius: 15px;
    overflow: hidden;
}

.package-details {
    font-size: 0.95rem;
}

.package-details ul {
    list-style: none;
    padding: 0;
}

.package-details li {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.package-details li:last-child {
    border-bottom: none;
}

.package-details h5 {
    color: #333;
    font-weight: 600;
    margin-top: 20px;
    margin-bottom: 15px;
}
</style>

<script>
// Sepete ekle fonksiyonu
function addToCart(productId) {
    <?php if (!isset($_SESSION['user_id'])): ?>
        // Giriş yapmamış kullanıcılar için
        if (confirm('Satın alma işlemi için giriş yapmanız gerekmektedir. Giriş sayfasına yönlendirilmek ister misiniz?')) {
            window.location.href = '<?php echo SITE_URL; ?>/login.php?redirect=urunler';
        }
        return;
    <?php endif; ?>
    
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
                    window.location.href = '<?php echo SITE_URL; ?>/login.php?redirect=urunler';
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

<?php include 'includes/footer.php'; ?>
