<?php
require_once 'config/config.php';

// UTF-8 encoding ayarı
$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_turkish_ci");

$pageTitle = 'Sosyal Hak Rehberliği';
$pageDescription = 'Güncel bilgiler, mevzuat, blog yazıları ve akademik çalışmalar';

// Filtreleme parametreleri
$type     = $_GET['type']     ?? 'all';
$category = $_GET['category'] ?? '';
$search   = $_GET['q']        ?? '';

// Kategorileri al
$stmtCategories = $pdo->query("
    SELECT * 
    FROM categories 
    WHERE status = 'active' 
    ORDER BY name ASC
");
$categories = $stmtCategories->fetchAll();

// İçerikleri getir
$query = "
    SELECT 
        c.*, 
        cat.name AS category_name, 
        u.name  AS author_name 
    FROM contents c 
    LEFT JOIN categories cat ON c.category_id = cat.id 
    LEFT JOIN users      u   ON c.author_id   = u.id 
    WHERE c.status = 'published'
";

$params = [];

if ($type !== 'all') {
    $query   .= " AND c.type = ?";
    $params[] = $type;
}

if ($category) {
    $query   .= " AND c.category_id = ?";
    $params[] = $category;
}

if ($search) {
    $query      .= " AND (c.title LIKE ? OR c.content LIKE ? OR c.summary LIKE ?)";
    $searchTerm  = "%$search%";
    $params[]    = $searchTerm;
    $params[]    = $searchTerm;
    $params[]    = $searchTerm;
}

$query .= " ORDER BY c.published_at DESC LIMIT 50";

$stmtContents = $pdo->prepare($query);
$stmtContents->execute($params);
$contents = $stmtContents->fetchAll();

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="hero-section" style="padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="mb-3">
                    <i class="fas fa-book-reader me-2"></i>
                    Sosyal Hak Rehberliği
                </h1>
                <p class="lead">
                    Güncel bilgiler, mevzuat düzenlemeleri ve akademik çalışmalar
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Arama ve Filtreleme -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="filter-section">
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">Arama</label>
                    <input type="text" name="q" class="form-control" 
                           placeholder="Başlık, içerik veya özet ara..." 
                           value="<?php echo escape($search); ?>">
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">İçerik Tipi</label>
                    <select name="type" class="form-select">
                        <option value="all" <?php echo $type === 'all' ? 'selected' : ''; ?>>Tümü</option>
                        <option value="blog" <?php echo $type === 'blog' ? 'selected' : ''; ?>>Blog</option>
                        <option value="mevzuat" <?php echo $type === 'mevzuat' ? 'selected' : ''; ?>>Mevzuat</option>
                        <option value="akademik" <?php echo $type === 'akademik' ? 'selected' : ''; ?>>Akademik</option>
                        <option value="duyuru" <?php echo $type === 'duyuru' ? 'selected' : ''; ?>>Duyuru</option>
                    </select>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="">Tüm Kategoriler</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo escape($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Ara
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>


<!-- İçerik Listesi -->
<section class="py-5">
    <div class="container">
        <?php if (!empty($search)): ?>
            <div class="alert alert-info mb-4">
                <i class="fas fa-search me-2"></i>
                "<strong><?php echo escape($search); ?></strong>" için
                <?php echo count($contents); ?> sonuç bulundu.
            </div>
        <?php endif; ?>

        <?php if (!empty($contents)): ?>
            <div class="row g-4">
                <?php foreach ($contents as $content): ?>
                    <?php $safeSlug = htmlspecialchars_decode(strip_tags($content['slug'])); ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card blog-card h-100"
                             onclick="window.open('icerik.php?slug=<?php echo urlencode($content['slug']); ?>', '_self')"
                             style="cursor: pointer;">
                            <?php if (!empty($content['image'])): ?>
                                <img src="<?php echo SITE_URL . '/' . escape($content['image']); ?>"
                                     alt="<?php echo escape($content['title']); ?>" class="card-img-top">
                            <?php else: ?>
                                <?php
                                $bgColors = [
                                    'blog'     => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                    'mevzuat'  => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                                    'akademik' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                                    'duyuru'   => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)'
                                ];
                                $bg = $bgColors[$content['type']] ?? $bgColors['blog'];
                                ?>
                                <div style="height: 180px; background: <?php echo $bg; ?>;
                                            display: flex; align-items: center; justify-content: center;" class="card-img-top">
                                    <i class="fas fa-<?php
                                        echo $content['type'] === 'mevzuat'  ? 'gavel'
                                            : ($content['type'] === 'akademik' ? 'graduation-cap'
                                            : ($content['type'] === 'duyuru'   ? 'bullhorn'
                                            : 'file-alt'));
                                    ?>" style="font-size: 3rem; color: white;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <?php
                                $badgeColors = [
                                    'blog'     => 'primary',
                                    'mevzuat'  => 'danger',
                                    'akademik' => 'info',
                                    'duyuru'   => 'success'
                                ];
                                $badgeColor = $badgeColors[$content['type']] ?? 'primary';

                                $typeNames = [
                                    'blog'     => 'Blog',
                                    'mevzuat'  => 'Mevzuat',
                                    'akademik' => 'Akademik',
                                    'duyuru'   => 'Duyuru'
                                ];
                                ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-<?php echo $badgeColor; ?>">
                                        <?php echo $typeNames[$content['type']] ?? 'İçerik'; ?>
                                    </span>
                                    <?php if (!empty($content['category_name'])): ?>
                                        <span class="badge bg-secondary">
                                            <?php echo escape($content['category_name']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <h5><?php echo escape($content['title']); ?></h5>
                                <p class="text-muted">
                                    <?php echo escape(
                                        truncateText($content['summary'] ?? $content['content'], 120)
                                    ); ?>
                                </p>
                                
                                <div class="meta mt-auto">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo formatDate($content['published_at']); ?>
                                    </small>
                                    <?php if (!empty($content['author_name'])): ?>
                                        <small class="text-muted ms-2">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo escape($content['author_name']); ?>
                                        </small>
                                    <?php endif; ?>
                                    <small class="text-muted ms-2">
                                        <i class="fas fa-eye me-1"></i>
                                        <?php echo number_format((int)$content['views']); ?> görüntülenme
                                    </small>
                                </div>

                                <!-- Direkt link - JavaScript bağımsız -->
                                <a href="icerik.php?slug=<?php echo urlencode($content['slug']); ?>"
                                   class="btn btn-outline-primary btn-sm w-100 mt-2"
                                   onclick="event.stopPropagation();">
                                    <i class="fas fa-arrow-right me-1"></i> Devamını Oku
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-search"
                   style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
                <h4 class="text-muted">İçerik Bulunamadı</h4>
                <p class="text-muted">Aradığınız kriterlere uygun içerik bulunmamaktadır.</p>
                <a href="<?php echo SITE_URL; ?>/rehberlik.php" class="btn btn-primary mt-3">
                    <i class="fas fa-redo me-2"></i>Filtreleri Temizle
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Kategoriler -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="mb-3">
                <i class="fas fa-layer-group me-2"></i>
                Kategoriler
            </h3>
        </div>
        
        <div class="row g-3">
            <?php foreach ($categories as $cat): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="<?php echo SITE_URL; ?>/rehberlik.php?category=<?php echo $cat['id']; ?>" 
                       class="btn btn-outline-primary w-100 <?php echo $category == $cat['id'] ? 'active' : ''; ?>">
                        <i class="fas fa-folder me-2"></i>
                        <?php echo escape($cat['name']); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Popüler Konular -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="mb-3">
                <i class="fas fa-fire me-2"></i>
                Popüler Konular
            </h3>
        </div>
        
        <div class="row g-3">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="<?php echo SITE_URL; ?>/rehberlik.php?q=evde+bakım" class="btn btn-light w-100">
                    #EvdeBakım
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="<?php echo SITE_URL; ?>/rehberlik.php?q=engelli+aylığı" class="btn btn-light w-100">
                    #EngelliAylığı
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="<?php echo SITE_URL; ?>/rehberlik.php?q=2022+kanun" class="btn btn-light w-100">
                    #2022Kanun
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="<?php echo SITE_URL; ?>/rehberlik.php?q=malulen+emeklilik" class="btn btn-light w-100">
                    #MalulenEmeklilik
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="<?php echo SITE_URL; ?>/rehberlik.php?q=sgk" class="btn btn-light w-100">
                    #SGK
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="<?php echo SITE_URL; ?>/rehberlik.php?q=sosyal+yardım" class="btn btn-light w-100">
                    #SosyalYardım
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>