<?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSS - Dijital Sosyal Hak Rehberliği</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="/">
                <i class="fas fa-home me-2"></i>Dijital Sosyal Hak Rehberliği
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Ana Sayfa</a>
                <a class="nav-link" href="hesaplama-araclari.php">Hesaplama</a>
                <a class="nav-link active" href="sss.php">SSS</a>
                <a class="nav-link" href="iletisim.php">İletişim</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="text-center mb-5">
            <i class="fas fa-question-circle text-primary me-3"></i>
            Sıkça Sorulan Sorular
        </h1>

        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        <i class="fas fa-building me-2 text-primary"></i>
                        SGK engelli emeklilik şartları nelerdir?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <strong>2026 güncel şartlar:</strong>
                        <ul>
                            <li>En az %40 engel oranı</li>
                            <li>Minimum 15 yıl sigortalılık</li>
                            <li>En az 3600 gün prim</li>
                            <li>Çalışma gücü kaybı testi</li>
                        </ul>
                        <div class="alert alert-info">
                            <i class="fas fa-calculator me-2"></i>
                            <a href="hesaplama-araclari.php" class="text-decoration-none">Hesaplama araçlarımızı kullanın</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        <i class="fas fa-home me-2 text-success"></i>
                        Evde bakım maaşı nasıl alınır?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <strong>Gerekli adımlar:</strong>
                        <ol>
                            <li>Bakıma muhtaç raporu</li>
                            <li>Gelir testi (2026: 17.002 TL/yıl)</li>
                            <li>Sosyal inceleme başvurusu</li>
                            <li>Belge tamamlama</li>
                        </ol>
                        <a href="hesaplama-araclari.php" class="btn btn-success btn-sm">
                            <i class="fas fa-calculator me-1"></i>Gelir Testi
                        </a>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        <i class="fas fa-phone me-2 text-info"></i>
                        Nasıl iletişime geçebilirim?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-envelope text-primary me-2"></i>E-posta</h6>
                                <p>info@sosyalhizmetdanismanligi.com</p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fab fa-instagram text-danger me-2"></i>Instagram</h6>
                                <p>@sosyalhizmet.danismanligi</p>
                            </div>
                        </div>
                        <a href="iletisim.php" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>Mesaj Gönder
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2026 Dijital Sosyal Hak Rehberliği. Tüm hakları saklıdır.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>