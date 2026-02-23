<?php
require_once '../config/config.php';

// UTF-8 encoding ayarı
$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_turkish_ci");

// Kullanıcı girişi kontrolü
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php');
}

$pageTitle = 'Gelir Testi Hesaplama';

// Form gönderildi mi?
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $householdSize = intval($_POST['household_size'] ?? 0);
    $monthlyIncome = floatval($_POST['monthly_income'] ?? 0);
    $hasDisability = isset($_POST['has_disability']);
    $isStudent = isset($_POST['is_student']);
    $isSenior = isset($_POST['is_senior']);
    
    // 2024 yılı gelir test limitleri (asgari ücretin katları)
    // Bu değerler gerçek yasal düzenlemelere göre güncellenmelidir
    $minimumWage = 17002; // 2024 asgari ücret
    
    $incomeLimits = [
        1 => $minimumWage * 1.5,  // Tek kişi
        2 => $minimumWage * 2.5,  // İki kişi
        3 => $minimumWage * 3.0,  // Üç kişi
        4 => $minimumWage * 3.5,  // Dört kişi
        5 => $minimumWage * 4.0,  // Beş kişi
    ];
    
    // 5+ kişi için her ek kişi başına +0.5 kat ekle
    if ($householdSize > 5) {
        $incomeLimits[$householdSize] = $incomeLimits[5] + (($householdSize - 5) * $minimumWage * 0.5);
    }
    
    $incomeLimit = $incomeLimits[$householdSize] ?? $minimumWage * 4.0;
    
    // Özel durumlar için limit artışı
    if ($hasDisability) {
        $incomeLimit *= 1.2; // %20 artış
    }
    if ($isStudent) {
        $incomeLimit *= 1.1; // %10 artış
    }
    if ($isSenior) {
        $incomeLimit *= 1.15; // %15 artış
    }
    
    $perCapitaIncome = $householdSize > 0 ? $monthlyIncome / $householdSize : $monthlyIncome;
    $perCapitaLimit = $incomeLimit / $householdSize;
    
    $isEligible = $monthlyIncome <= $incomeLimit;
    $eligibilityPercentage = $incomeLimit > 0 ? ($monthlyIncome / $incomeLimit) * 100 : 0;
    
    $result = [
        'household_size' => $householdSize,
        'monthly_income' => $monthlyIncome,
        'income_limit' => $incomeLimit,
        'per_capita_income' => $perCapitaIncome,
        'per_capita_limit' => $perCapitaLimit,
        'is_eligible' => $isEligible,
        'eligibility_percentage' => $eligibilityPercentage,
        'has_disability' => $hasDisability,
        'is_student' => $isStudent,
        'is_senior' => $isSenior
    ];
}

include '../includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-circle fa-4x text-primary"></i>
                        <h5 class="mt-3"><?php echo escape($_SESSION['user_name']); ?></h5>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="dashboard.php" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>Anasayfa
                        </a>
                        <a href="profile.php" class="btn btn-outline-primary">
                            <i class="fas fa-user me-2"></i>Profil Bilgilerim
                        </a>
                        <a href="orders.php" class="btn btn-outline-primary">
                            <i class="fas fa-shopping-cart me-2"></i>Siparişlerim
                        </a>
                        <a href="addresses.php" class="btn btn-outline-primary">
                            <i class="fas fa-map-marker-alt me-2"></i>Adreslerim
                        </a>
                        <a href="invoices.php" class="btn btn-outline-primary">
                            <i class="fas fa-file-invoice me-2"></i>Faturalarım
                        </a>
                        <a href="payments.php" class="btn btn-outline-primary">
                            <i class="fas fa-credit-card me-2"></i>Ödemelerim
                        </a>
                        <a href="calculations.php" class="btn btn-primary active">
                            <i class="fas fa-calculator me-2"></i>Gelir Testi
                        </a>
                        <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Gelir Testi Hesaplama
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Bilgilendirme:</strong> Bu hesaplama, sosyal yardım başvurularında gelir durumunuzun uygunluğunu kontrol etmenize yardımcı olur. 
                        Lütfen tüm bilgileri eksiksiz ve doğru bir şekilde doldurunuz.
                    </div>

                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="household_size" class="form-label">
                                    Hane Halkı Büyüklüğü <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="household_size" name="household_size" 
                                       min="1" max="20" required
                                       value="<?php echo $result['household_size'] ?? ''; ?>"
                                       placeholder="Hanede yaşayan kişi sayısı">
                                <small class="text-muted">Evde yaşayan toplam kişi sayısını giriniz</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="monthly_income" class="form-label">
                                    Aylık Hane Geliri (₺) <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="monthly_income" name="monthly_income" 
                                       min="0" step="0.01" required
                                       value="<?php echo $result['monthly_income'] ?? ''; ?>"
                                       placeholder="0.00">
                                <small class="text-muted">Tüm hane üyelerinin toplam aylık geliri</small>
                            </div>
                        </div>

                        <h6 class="mt-3 mb-3">Özel Durumlar</h6>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="has_disability" name="has_disability"
                                       <?php echo ($result['has_disability'] ?? false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="has_disability">
                                    Hanede engelli birey bulunuyor
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_student" name="is_student"
                                       <?php echo ($result['is_student'] ?? false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_student">
                                    Hanede üniversite öğrencisi bulunuyor
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_senior" name="is_senior"
                                       <?php echo ($result['is_senior'] ?? false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_senior">
                                    Hanede 65 yaş üstü birey bulunuyor
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-calculator me-2"></i>
                                Hesapla
                            </button>
                        </div>
                    </form>

                    <?php if ($result): ?>
                        <hr class="my-4">
                        
                        <!-- Sonuç Kartı -->
                        <div class="card bg-<?php echo $result['is_eligible'] ? 'success' : 'warning'; ?> text-white">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-<?php echo $result['is_eligible'] ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                                    Değerlendirme Sonucu
                                </h5>
                                <p class="card-text fs-5">
                                    <?php if ($result['is_eligible']): ?>
                                        <strong>Tebrikler!</strong> Gelir durumunuz sosyal yardım başvurusu için uygun görünmektedir.
                                    <?php else: ?>
                                        <strong>Bilgilendirme:</strong> Gelir durumunuz mevcut limitin üzerinde görünmektedir. 
                                        Ancak nihai değerlendirme yetkili birimler tarafından yapılacaktır.
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Detaylı Bilgiler -->
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">Hane Halkı Büyüklüğü</h6>
                                        <p class="card-text fs-4 mb-0">
                                            <i class="fas fa-users text-primary me-2"></i>
                                            <?php echo $result['household_size']; ?> Kişi
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">Aylık Hane Geliri</h6>
                                        <p class="card-text fs-4 mb-0">
                                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                                            <?php echo number_format($result['monthly_income'], 2); ?> ₺
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">Kişi Başı Gelir</h6>
                                        <p class="card-text fs-4 mb-0">
                                            <i class="fas fa-user text-info me-2"></i>
                                            <?php echo number_format($result['per_capita_income'], 2); ?> ₺
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">Gelir Limiti</h6>
                                        <p class="card-text fs-4 mb-0">
                                            <i class="fas fa-chart-line text-warning me-2"></i>
                                            <?php echo number_format($result['income_limit'], 2); ?> ₺
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- İlerleme Çubuğu -->
                        <div class="mt-3">
                            <h6>Gelir Limiti Kullanımı</h6>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-<?php echo $result['is_eligible'] ? 'success' : 'danger'; ?>" 
                                     role="progressbar" 
                                     style="width: <?php echo min($result['eligibility_percentage'], 100); ?>%"
                                     aria-valuenow="<?php echo $result['eligibility_percentage']; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    <?php echo number_format($result['eligibility_percentage'], 1); ?>%
                                </div>
                            </div>
                            <small class="text-muted">
                                <?php if ($result['is_eligible']): ?>
                                    Geliriniz, limit tutarının %<?php echo number_format($result['eligibility_percentage'], 1); ?>'ı kadardır.
                                <?php else: ?>
                                    Geliriniz, limit tutarını %<?php echo number_format($result['eligibility_percentage'] - 100, 1); ?> aşmaktadır.
                                <?php endif; ?>
                            </small>
                        </div>

                        <!-- Özel Durumlar Göstergesi -->
                        <?php if ($result['has_disability'] || $result['is_student'] || $result['is_senior']): ?>
                            <div class="alert alert-info mt-4">
                                <strong><i class="fas fa-info-circle me-2"></i>Özel Durumlar:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php if ($result['has_disability']): ?>
                                        <li>Engelli birey olması nedeniyle gelir limiti %20 artırıldı</li>
                                    <?php endif; ?>
                                    <?php if ($result['is_student']): ?>
                                        <li>Üniversite öğrencisi olması nedeniyle gelir limiti %10 artırıldı</li>
                                    <?php endif; ?>
                                    <?php if ($result['is_senior']): ?>
                                        <li>65 yaş üstü birey olması nedeniyle gelir limiti %15 artırıldı</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- Bilgilendirme Notu -->
                        <div class="alert alert-warning mt-4">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Önemli Not:</strong>
                            <p class="mb-0">
                                Bu hesaplama sadece tahmini bir değerlendirmedir. Nihai karar, resmi başvuru sonrasında 
                                gerekli belgeler ve incelemeler yapıldıktan sonra yetkili birimler tarafından verilecektir.
                                Daha detaylı bilgi için lütfen ilgili kurumla iletişime geçiniz.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Yardımcı Bilgiler -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>
                        Sık Sorulan Sorular
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Hane halkı büyüklüğüne kimler dahil edilir?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Aynı evde yaşayan ve ortak ekonomik bütçeyi paylaşan tüm bireyler hane halkına dahildir. 
                                    Buna çocuklar, eşler, anne-baba, kardeşler ve diğer yakın akrabalar dahildir.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Hangi gelirler hesaplamaya dahil edilir?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Tüm düzenli gelirler hesaplamaya dahil edilmelidir: maaş, ücret, kira geliri, emekli maaşı, 
                                    tarımsal gelir, ticari gelir vb. Tek seferlik yardımlar genellikle hesaba katılmaz.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Gelir testi sonucu bağlayıcı mıdır?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Hayır, bu hesaplama sadece bir ön değerlendirmedir. Resmi başvuru sonrasında 
                                    yetkili birimler detaylı inceleme yaparak nihai kararı verecektir.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
