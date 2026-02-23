<?php
// Çalışır hesaplama araçları sayfası
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesaplama Araçları - Dijital Sosyal Hak Rehberliği</title>
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 4rem 0;
        }
        
        .calculator-card {
            border: none;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            border-radius: 15px;
        }
        
        .calculator-card:hover {
            transform: translateY(-5px);
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 12px 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }
        
        .btn-calculate {
            background: linear-gradient(135deg, var(--success-color), #059669);
            border: none;
            padding: 15px 30px;
            font-weight: 600;
            border-radius: 10px;
            font-size: 1.1rem;
        }
        
        .result-box {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            padding: 20px;
        }
        
        .table-responsive {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="fas fa-home me-2"></i>Dijital Sosyal Hak Rehberliği
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Ana Sayfa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="hesaplama-araclari.php">
                            <i class="fas fa-calculator me-1"></i>Hesaplama Araçları
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sss.php">
                            <i class="fas fa-question-circle me-1"></i>SSS
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="iletisim.php">
                            <i class="fas fa-envelope me-1"></i>İletişim
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-calculator me-3"></i>
                2026 Güncel Hesaplama Araçları
            </h1>
            <p class="lead">Sosyal haklar için güncel mevzuata uygun hesaplama araçları</p>
            <div class="mt-4">
                <span class="badge bg-light text-dark fs-6 me-2">
                    <i class="fas fa-calendar me-1"></i>2026 Mevzuat
                </span>
                <span class="badge bg-light text-dark fs-6">
                    <i class="fas fa-shield-alt me-1"></i>KVKK Uyumlu
                </span>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            
            <!-- Gelir Testi Hesaplama -->
            <div class="col-lg-6 mb-4">
                <div class="card calculator-card h-100">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-family me-2"></i>
                            Aile-Hane Gelir Testi
                        </h4>
                        <small>Evde bakım maaşı uygunluk kontrolü</small>
                    </div>
                    <div class="card-body">
                        <form id="gelirTestiForm">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-users me-1"></i>Hane Halkı Sayısı
                                </label>
                                <select class="form-select" id="haneHalkiSayisi" required>
                                    <option value="">Seçiniz</option>
                                    <option value="1">1 kişi</option>
                                    <option value="2">2 kişi</option>
                                    <option value="3">3 kişi</option>
                                    <option value="4">4 kişi</option>
                                    <option value="5">5 kişi</option>
                                    <option value="6">6+ kişi</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-money-bill-wave me-1"></i>Toplam Aylık Gelir (TL)
                                </label>
                                <input type="number" class="form-control" id="toplamGelir" placeholder="Örn: 5000" required>
                                <small class="text-muted">Tüm aile üyelerinin geliri dahil</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="engelliVar">
                                    <label class="form-check-label" for="engelliVar">
                                        Hanede engelli birey var
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="yasliVar">
                                    <label class="form-check-label" for="yasliVar">
                                        Hanede 65+ yaşlı birey var
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-calculate text-white w-100">
                                <i class="fas fa-calculator me-2"></i>HESAPLA
                            </button>
                        </form>
                        
                        <div id="gelirSonuc" class="mt-4" style="display: none;">
                            <div class="result-box">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-chart-line me-2"></i>Hesaplama Sonucu
                                </h5>
                                <div id="sonucIcerik"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SGK Emeklilik Hesaplama -->
            <div class="col-lg-6 mb-4">
                <div class="card calculator-card h-100">
                    <div class="card-header bg-success text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-building me-2"></i>
                            SGK Emeklilik Hesaplama
                        </h4>
                        <small>Engelli/Malulen emeklilik uygunluk</small>
                    </div>
                    <div class="card-body">
                        <form id="emeklilikForm">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar me-1"></i>Yaşınız
                                </label>
                                <input type="number" class="form-control" id="yas" placeholder="Örn: 45" min="18" max="80" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-percentage me-1"></i>Engel Oranınız (%)
                                </label>
                                <select class="form-select" id="engelOrani" required>
                                    <option value="">Seçiniz</option>
                                    <option value="40">%40-49</option>
                                    <option value="50">%50-59</option>
                                    <option value="60">%60-69</option>
                                    <option value="70">%70-79</option>
                                    <option value="80">%80-89</option>
                                    <option value="90">%90-100</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-clock me-1"></i>Prim Ödeme Süreniz (Yıl)
                                </label>
                                <input type="number" class="form-control" id="primSuresi" placeholder="Örn: 12" min="0" max="50" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar-alt me-1"></i>Sigorta Başlangıç Yılı
                                </label>
                                <select class="form-select" id="sigortaBaslangic" required>
                                    <option value="">Seçiniz</option>
                                    <option value="2008oncesi">2008 Öncesi</option>
                                    <option value="2008sonrasi">2008 Sonrası</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-calculate text-white w-100">
                                <i class="fas fa-search me-2"></i>UYGUNLUK KONTROL ET
                            </button>
                        </form>
                        
                        <div id="emeklilikSonuc" class="mt-4" style="display: none;">
                            <div class="result-box">
                                <h5 class="text-success mb-3">
                                    <i class="fas fa-check-circle me-2"></i>Değerlendirme Sonucu
                                </h5>
                                <div id="emeklilikIcerik"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- 2026 Güncel Gelir Şartları Tablosu -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card calculator-card">
                    <div class="card-header bg-warning text-dark text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-table me-2"></i>
                            2026 Güncel Gelir Şartları Formülleri
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Yaş Grubu</th>
                                        <th>Gelir Limiti (Yıllık)</th>
                                        <th>Aylık Karşılığı</th>
                                        <th>Formül</th>
                                        <th>Açıklama</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>0-18 yaş</strong></td>
                                        <td class="text-success fw-bold">17.002 TL</td>
                                        <td>1.417 TL</td>
                                        <td><code>Asgari ücret × 12 × 1/3</code></td>
                                        <td>ÇÖZGER raporu gerekli</td>
                                    </tr>
                                    <tr>
                                        <td><strong>18-65 yaş</strong></td>
                                        <td class="text-success fw-bold">17.002 TL</td>
                                        <td>1.417 TL</td>
                                        <td><code>Asgari ücret × 12 × 1/3</code></td>
                                        <td>Çalışma gücü kaybı %60+</td>
                                    </tr>
                                    <tr>
                                        <td><strong>65+ yaş</strong></td>
                                        <td class="text-success fw-bold">17.002 TL</td>
                                        <td>1.417 TL</td>
                                        <td><code>Asgari ücret × 12 × 1/3</code></td>
                                        <td>Yaşlılık aylığı</td>
                                    </tr>
                                    <tr class="table-info">
                                        <td><strong>Evde Bakım</strong></td>
                                        <td class="text-primary fw-bold">25.503 TL</td>
                                        <td>2.125 TL</td>
                                        <td><code>Asgari ücret × 12 × 1/2</code></td>
                                        <td>Bakıma muhtaç raporu</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="alert alert-info mt-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>2026 Önemli Notlar:
                            </h6>
                            <ul class="mb-0">
                                <li><strong>Asgari Ücret 2026:</strong> 17.002 TL (net)</li>
                                <li><strong>Hesaplama:</strong> Hane halkı toplam geliri / hane halkı sayısı</li>
                                <li><strong>Gelir Türleri:</strong> Maaş, emekli maaşı, kira geliri, tarımsal gelir dahil</li>
                                <li><strong>İstisna:</strong> Engelli aylığı ve evde bakım maaşı gelir hesabına dahil edilmez</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hesaplama Örnekleri -->
        <div class="row mt-5">
            <div class="col-md-6 mb-4">
                <div class="card calculator-card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>Başarılı Örnek
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-success">4 Kişilik Aile - Uygun</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-user me-2 text-muted"></i>Baba: 8.000 TL maaş</li>
                            <li><i class="fas fa-user me-2 text-muted"></i>Anne: Çalışmıyor</li>
                            <li><i class="fas fa-child me-2 text-muted"></i>Çocuk 1: Engelli (aylık almıyor)</li>
                            <li><i class="fas fa-child me-2 text-muted"></i>Çocuk 2: Öğrenci</li>
                        </ul>
                        <div class="bg-light p-3 rounded">
                            <strong>Hesaplama:</strong><br>
                            8.000 TL ÷ 4 kişi = 2.000 TL<br>
                            <span class="text-success">✓ Limit altında (1.417 TL)</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card calculator-card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-times-circle me-2"></i>Uygun Olmayan Örnek
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-danger">3 Kişilik Aile - Uygun Değil</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-user me-2 text-muted"></i>Baba: 12.000 TL maaş</li>
                            <li><i class="fas fa-user me-2 text-muted"></i>Anne: 6.000 TL maaş</li>
                            <li><i class="fas fa-child me-2 text-muted"></i>Çocuk: Engelli</li>
                        </ul>
                        <div class="bg-light p-3 rounded">
                            <strong>Hesaplama:</strong><br>
                            18.000 TL ÷ 3 kişi = 6.000 TL<br>
                            <span class="text-danger">✗ Limit üstünde (1.417 TL)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- İletişim Çağrısı -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card calculator-card bg-primary text-white">
                    <div class="card-body text-center py-5">
                        <h3 class="mb-3">
                            <i class="fas fa-phone me-2"></i>
                            Profesyonel Danışmanlık Alın
                        </h3>
                        <p class="lead mb-4">
                            Hesaplama sonuçlarınız hakkında detaylı bilgi ve başvuru süreçleri için uzman desteği alın.
                        </p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="iletisim.php" class="btn btn-light btn-lg">
                                <i class="fas fa-envelope me-2"></i>İletişime Geç
                            </a>
                            <a href="sss.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-question-circle me-2"></i>SSS'ye Bak
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>
                        <i class="fas fa-home me-2"></i>Dijital Sosyal Hak Rehberliği
                    </h5>
                    <p class="text-light">
                        Türkiye'de sosyal hizmet alanında dijital dönüşümün öncüsü. 
                        Sosyal hakların korunması ve geliştirilmesi için çalışıyoruz.
                    </p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>
                        <i class="fas fa-calculator me-2"></i>Hesaplama Araçları
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>2026 Güncel Mevzuat
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>KVKK Uyumlu
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>Anlık Hesaplama
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>
                        <i class="fas fa-phone me-2"></i>İletişim
                    </h5>
                    <p><i class="fas fa-envelope me-2"></i>info@sosyalhizmetdanismanligi.com</p>
                    <div class="social-links">
                        <a href="https://www.instagram.com/sosyalhizmet.danismanligi/" class="text-light me-3" target="_blank">
                            <i class="fab fa-instagram fa-2x"></i>
                        </a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0">&copy; 2026 Dijital Sosyal Hak Rehberliği. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Hesaplama JavaScript -->
    <script>
        // Gelir Testi Hesaplama
        document.getElementById('gelirTestiForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const haneHalkiSayisi = parseInt(document.getElementById('haneHalkiSayisi').value);
            const toplamGelir = parseFloat(document.getElementById('toplamGelir').value);
            const engelliVar = document.getElementById('engelliVar').checked;
            const yasliVar = document.getElementById('yasliVar').checked;
            
            // Kişi başı gelir hesaplama
            const kisiBasiGelir = toplamGelir / haneHalkiSayisi;
            const yillikGelir = kisiBasiGelir * 12;
            
            // 2026 limitleri
            const normalLimit = 17002; // TL/yıl
            const evdeBakimLimit = 25503; // TL/yıl
            
            let sonuc = '';
            let uygun = false;
            
            if (engelliVar || yasliVar) {
                if (yillikGelir <= evdeBakimLimit) {
                    uygun = true;
                    sonuc = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle me-2"></i>EVDE BAKIM MAAŞI İÇİN UYGUN</h6>
                            <p class="mb-2"><strong>Kişi başı yıllık gelir:</strong> ${yillikGelir.toLocaleString('tr-TR')} TL</p>
                            <p class="mb-2"><strong>Limit:</strong> ${evdeBakimLimit.toLocaleString('tr-TR')} TL</p>
                            <p class="mb-0"><strong>Potansiyel maaş:</strong> 2.841 TL/ay (ağır engelli) veya 1.420 TL/ay (yaşlı)</p>
                        </div>
                    `;
                } else {
                    sonuc = `
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-times-circle me-2"></i>EVDE BAKIM MAAŞI İÇİN UYGUN DEĞİL</h6>
                            <p class="mb-2"><strong>Kişi başı yıllık gelir:</strong> ${yillikGelir.toLocaleString('tr-TR')} TL</p>
                            <p class="mb-2"><strong>Limit:</strong> ${evdeBakimLimit.toLocaleString('tr-TR')} TL</p>
                            <p class="mb-0">Gelir limiti aşıldı.</p>
                        </div>
                    `;
                }
            } else {
                if (yillikGelir <= normalLimit) {
                    uygun = true;
                    sonuc = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle me-2"></i>SOSYAL YARDIM İÇİN UYGUN</h6>
                            <p class="mb-2"><strong>Kişi başı yıllık gelir:</strong> ${yillikGelir.toLocaleString('tr-TR')} TL</p>
                            <p class="mb-2"><strong>Limit:</strong> ${normalLimit.toLocaleString('tr-TR')} TL</p>
                            <p class="mb-0">Diğer sosyal yardımlar için başvurabilirsiniz.</p>
                        </div>
                    `;
                } else {
                    sonuc = `
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>SOSYAL YARDIM İÇİN UYGUN DEĞİL</h6>
                            <p class="mb-2"><strong>Kişi başı yıllık gelir:</strong> ${yillikGelir.toLocaleString('tr-TR')} TL</p>
                            <p class="mb-2"><strong>Limit:</strong> ${normalLimit.toLocaleString('tr-TR')} TL</p>
                            <p class="mb-0">Gelir limiti aşıldı.</p>
                        </div>
                    `;
                }
            }
            
            document.getElementById('sonucIcerik').innerHTML = sonuc;
            document.getElementById('gelirSonuc').style.display = 'block';
            document.getElementById('gelirSonuc').scrollIntoView({ behavior: 'smooth' });
        });

        // SGK Emeklilik Hesaplama
        document.getElementById('emeklilikForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const yas = parseInt(document.getElementById('yas').value);
            const engelOrani = parseInt(document.getElementById('engelOrani').value);
            const primSuresi = parseInt(document.getElementById('primSuresi').value);
            const sigortaBaslangic = document.getElementById('sigortaBaslangic').value;
            
            let sonuc = '';
            let emeklilikTuru = '';
            let uygun = false;
            
            // Engelli emeklilik kontrolü
            if (engelOrani >= 40 && primSuresi >= 15) {
                uygun = true;
                emeklilikTuru = 'Engelli Emeklilik';
                sonuc = `
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle me-2"></i>ENGELLİ EMEKLİLİK İÇİN UYGUN</h6>
                        <p class="mb-2"><strong>Engel Oranı:</strong> %${engelOrani} (Min: %40)</p>
                        <p class="mb-2"><strong>Prim Süresi:</strong> ${primSuresi} yıl (Min: 15 yıl)</p>
                        <p class="mb-0">Sağlık kurulu raporu ile başvurabilirsiniz.</p>
                    </div>
                `;
            }
            // Malulen emeklilik kontrolü
            else if (engelOrani >= 60) {
                if (sigortaBaslangic === '2008oncesi' && primSuresi >= 10) {
                    uygun = true;
                    emeklilikTuru = 'Malulen Emeklilik (2008 Öncesi)';
                    sonuc = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle me-2"></i>MALULEN EMEKLİLİK İÇİN UYGUN</h6>
                            <p class="mb-2"><strong>Çalışma Gücü Kaybı:</strong> %${engelOrani} (Min: %60)</p>
                            <p class="mb-2"><strong>Prim Süresi:</strong> ${primSuresi} yıl (2008 öncesi: Min 10 yıl)</p>
                            <p class="mb-0">SGK sağlık kuruluna başvurabilirsiniz.</p>
                        </div>
                    `;
                } else if (sigortaBaslangic === '2008sonrasi' && primSuresi >= 15) {
                    uygun = true;
                    emeklilikTuru = 'Malulen Emeklilik (2008 Sonrası)';
                    sonuc = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle me-2"></i>MALULEN EMEKLİLİK İÇİN UYGUN</h6>
                            <p class="mb-2"><strong>Çalışma Gücü Kaybı:</strong> %${engelOrani} (Min: %60)</p>
                            <p class="mb-2"><strong>Prim Süresi:</strong> ${primSuresi} yıl (2008 sonrası: Min 15 yıl)</p>
                            <p class="mb-0">SGK sağlık kuruluna başvurabilirsiniz.</p>
                        </div>
                    `;
                } else {
                    sonuc = `
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>MALULEN EMEKLİLİK İÇİN YETERSİZ PRİM</h6>
                            <p class="mb-2"><strong>Çalışma Gücü Kaybı:</strong> %${engelOrani} ✓</p>
                            <p class="mb-2"><strong>Prim Süresi:</strong> ${primSuresi} yıl</p>
                            <p class="mb-0"><strong>Gerekli:</strong> ${sigortaBaslangic === '2008oncesi' ? '10' : '15'} yıl</p>
                        </div>
                    `;
                }
            } else {
                sonuc = `
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-times-circle me-2"></i>EMEKLİLİK İÇİN UYGUN DEĞİL</h6>
                        <p class="mb-2"><strong>Engel Oranı:</strong> %${engelOrani}</p>
                        <p class="mb-2"><strong>Prim Süresi:</strong> ${primSuresi} yıl</p>
                        <p class="mb-0">Minimum %40 engel oranı ve 15 yıl prim gerekli.</p>
                    </div>
                `;
            }
            
            if (uygun) {
                sonuc += `
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="text-primary">Sonraki Adımlar:</h6>
                        <ol class="mb-0">
                            <li>Güncel sağlık kurulu raporu alın</li>
                            <li>SGK'ya başvuru yapın</li>
                            <li>Gerekli belgeleri tamamlayın</li>
                            <li>Süreç takibi yapın</li>
                        </ol>
                    </div>
                `;
            }
            
            document.getElementById('emeklilikIcerik').innerHTML = sonuc;
            document.getElementById('emeklilikSonuc').style.display = 'block';
            document.getElementById('emeklilikSonuc').scrollIntoView({ behavior: 'smooth' });
        });
    </script>
</body>
</html>