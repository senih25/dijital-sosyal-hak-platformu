<?php
session_start();

// DevCycle feature flags integration
$devcycleEnabled = false;
$showNewDashboard = false;
$dashboardVariant = null;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
if (file_exists(__DIR__ . '/config/devcycle.php')) {
    require_once __DIR__ . '/config/devcycle.php';
    try {
        $devcycle = new DevCycleManager();
        $userId = $_SESSION['user_id'] ?? 'anonymous';
        $userEmail = $_SESSION['user_email'] ?? '';

        $showNewDashboard = $devcycle->isFeatureEnabled($userId, 'new-dashboard');
        $dashboardVariant = $devcycle->getVariant($userId, 'dashboard-redesign');
        $devcycleEnabled = true;
    } catch (\Exception $e) {
        error_log('DevCycle unavailable: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dijital Sosyal Hak Rehberliği</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #1d4ed8;
            --brand-secondary: #0ea5e9;
            --soft-bg: #f8fafc;
        }
        body { background: var(--soft-bg); }
        .hero {
            background: linear-gradient(135deg, rgba(29,78,216,.95), rgba(14,165,233,.9));
            color: #fff;
            padding: 6rem 0 5rem;
        }
        .card-elevated {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 8px 30px rgba(15,23,42,.08);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .card-elevated:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 35px rgba(15,23,42,.12);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--brand-primary);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php">
            <i class="fa-solid fa-shield-heart me-2"></i>Dijital Sosyal Hak Rehberliği
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="index.php">Ana Sayfa</a></li>
                <li class="nav-item"><a class="nav-link" href="hesaplama_araclari_calisir.php">Hesaplama Araçları</a></li>
                <li class="nav-item"><a class="nav-link" href="sss.php">SSS</a></li>
                <li class="nav-item"><a class="nav-link" href="iletisim.php">İletişim</a></li>
                <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7 text-center text-lg-start">
                <span class="badge bg-light text-primary mb-3">2026 Mevzuatına Uyumlu</span>
                <?php if ($showNewDashboard): ?>
                <h1 class="display-5 fw-bold mb-3">Yeni Dijital Gösterge Paneli ile haklarınızı takip edin</h1>
                <p class="lead mb-4">Kişiselleştirilmiş gösterge panelinizle SGK süreçlerini, engellilik haklarını ve sosyal yardım başvurularını tek ekrandan yönetin.</p>
                <?php elseif ($dashboardVariant === 'variation-a'): ?>
                <h1 class="display-5 fw-bold mb-3">Haklarınıza hızla ulaşın – Yenilenmiş Arayüz</h1>
                <p class="lead mb-4">SGK süreçlerinden engellilik raporlarına, evde bakım maaşı başvurularından gelir testine kadar profesyonel ve anlaşılır rehberlik sunuyoruz.</p>
                <?php else: ?>
                <h1 class="display-5 fw-bold mb-3">Dijital Sosyal Hak Rehberliği ile haklarınıza güvenle ulaşın</h1>
                <p class="lead mb-4">SGK süreçlerinden engellilik raporlarına, evde bakım maaşı başvurularından gelir testine kadar profesyonel ve anlaşılır rehberlik sunuyoruz.</p>
                <?php endif; ?>
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start">
                    <a href="hesaplama_araclari_calisir.php" class="btn btn-light btn-lg px-4"><i class="fa-solid fa-calculator me-2"></i>Hesaplama Araçlarını Aç</a>
                    <a href="sss.php" class="btn btn-outline-light btn-lg px-4"><i class="fa-regular fa-circle-question me-2"></i>SSS'ye Git</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card card-elevated">
                    <div class="card-body p-4">
                        <h5 class="fw-bold">Hızlı Başvuru Yol Haritası</h5>
                        <ol class="mb-0 text-muted">
                            <li>Durumunuza uygun hak kategorisini belirleyin.</li>
                            <li>Hesaplama araçlarıyla ön değerlendirme yapın.</li>
                            <li>Gerekli belgeleri hazırlayın ve başvuruyu tamamlayın.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Hizmetlerimiz</h2>
            <p class="text-muted">Sosyal hak başvurularında uçtan uca danışmanlık.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-xl-3"><div class="card card-elevated h-100"><div class="card-body"><i class="fa-solid fa-building-columns text-primary fs-3"></i><h5 class="mt-3">SGK İşlemleri</h5><p class="text-muted mb-0">Emeklilik, prim gün hesapları ve resmi süreç danışmanlığı.</p></div></div></div>
            <div class="col-md-6 col-xl-3"><div class="card card-elevated h-100"><div class="card-body"><i class="fa-solid fa-wheelchair text-primary fs-3"></i><h5 class="mt-3">Engelli Hakları</h5><p class="text-muted mb-0">Engelli raporu, ÇÖZGER ve bakım destekleri hakkında rehberlik.</p></div></div></div>
            <div class="col-md-6 col-xl-3"><div class="card card-elevated h-100"><div class="card-body"><i class="fa-solid fa-file-circle-check text-primary fs-3"></i><h5 class="mt-3">Başvuru Hazırlığı</h5><p class="text-muted mb-0">Belge listeleri, başvuru dosyası kontrolü ve süreç takibi.</p></div></div></div>
            <div class="col-md-6 col-xl-3"><div class="card card-elevated h-100"><div class="card-body"><i class="fa-solid fa-hand-holding-heart text-primary fs-3"></i><h5 class="mt-3">Sosyal Yardım Desteği</h5><p class="text-muted mb-0">Gelir testi, yardım uygunluğu ve kurum yönlendirmeleri.</p></div></div></div>
        </div>
    </div>
</section>

<section class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-6 col-lg-3"><div class="stat-number">25.000+</div><div class="text-muted">Aylık Ziyaretçi</div></div>
            <div class="col-6 col-lg-3"><div class="stat-number">4.800+</div><div class="text-muted">Tamamlanan Hesaplama</div></div>
            <div class="col-6 col-lg-3"><div class="stat-number">%94</div><div class="text-muted">Memnuniyet Oranı</div></div>
            <div class="col-6 col-lg-3"><div class="stat-number">81 İl</div><div class="text-muted">Türkiye Genelinde Erişim</div></div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container text-center">
        <h3 class="fw-bold">Haklarınızı ertelemeyin, bugün başlayın</h3>
        <p class="text-muted">Güncel mevzuata göre hazırlanmış dijital rehberlik ve hesaplama araçları ile doğru adımı atın.</p>
        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <a href="hesaplama_araclari_calisir.php" class="btn btn-primary btn-lg">Hemen Hesapla</a>
            <a href="iletisim.php" class="btn btn-outline-primary btn-lg">Uzman Desteği Al</a>
        </div>
    </div>
</section>

<footer class="bg-dark text-white py-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <small>© 2026 Dijital Sosyal Hak Rehberliği</small>
        <small class="text-secondary">Güvenilir • Erişilebilir • Mevzuata Uyumlu</small>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="ai-chatbot.js"></script>
</body>
</html>
