<?php
require_once 'config/config.php';
$pageTitle = 'Balthazard Hesaplama';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Balthazard Formülü ile Toplam Engellilik Oranı Hesaplama
                    </h2>
                </div>
                <div class="card-body">
                    <!-- Bilgilendirme -->
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i>Balthazard Formülü Nedir?</h5>
                        <p class="mb-2">
                            Balthazard formülü, birden fazla engellilik oranının bir arada bulunması durumunda 
                            <strong>toplam çalışma gücü kaybı oranını</strong> hesaplamak için kullanılan matematiksel bir yöntemdir.
                        </p>
                        <p class="mb-0">
                            <strong>Formül:</strong> En yüksek orandan başlayarak, her bir oran için: 
                            <code>Toplam = Toplam + [(100 - Toplam) × Yeni Oran / 100]</code>
                        </p>
                    </div>

                    <!-- Hesaplama Formu -->
                    <form id="balthazardForm">
                        <h5 class="mb-3">
                            <i class="fas fa-percentage me-2"></i>
                            Engellilik Oranlarını Girin
                        </h5>

                        <div id="ratesContainer">
                            <!-- İlk oran -->
                            <div class="rate-row mb-3">
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">Engel Türü</label>
                                        <input type="text" class="form-control" placeholder="Örn: Görme kaybı" name="rate_name[]">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Oran (%) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control rate-input" min="0" max="100" step="0.01" required name="rate_value[]" placeholder="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Sıra</label>
                                        <input type="text" class="form-control order-display" readonly value="1">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger w-100" onclick="removeRate(this)" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <button type="button" class="btn btn-outline-primary" onclick="addRate()">
                                <i class="fas fa-plus me-2"></i>Yeni Oran Ekle
                            </button>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-calculator me-2"></i>
                                Toplam Oranı Hesapla
                            </button>
                        </div>
                    </form>

                    <!-- Sonuç Alanı -->
                    <div id="resultSection" class="mt-4" style="display: none;">
                        <hr>
                        <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Hesaplama Sonucu</h5>
                        
                        <!-- Adım Adım Hesaplama -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="text-primary mb-3">Hesaplama Adımları:</h6>
                                <div id="calculationSteps"></div>
                            </div>
                        </div>

                        <!-- Toplam Sonuç -->
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4 class="mb-2">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Toplam Engellilik Oranı
                                </h4>
                                <h2 class="display-4 mb-0" id="totalRate">0%</h2>
                            </div>
                        </div>

                        <!-- İlerleme Çubuğu -->
                        <div class="mt-3">
                            <div class="progress" style="height: 30px;">
                                <div id="progressBar" class="progress-bar bg-success" role="progressbar" style="width: 0%">
                                    <span id="progressText">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Nasıl Çalışır? -->
            <div class="card mt-4 shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>
                        Balthazard Formülü Nasıl Çalışır?
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Örnek Hesaplama:</h6>
                    <p class="mb-3">
                        Bir kişinin <strong>%40 görme kaybı</strong>, <strong>%20 işitme kaybı</strong> ve 
                        <strong>%15 hareket kısıtlılığı</strong> olduğunu varsayalım:
                    </p>

                    <ol class="mb-3">
                        <li class="mb-2">
                            <strong>Oranları büyükten küçüğe sırala:</strong><br>
                            40%, 20%, 15%
                        </li>
                        <li class="mb-2">
                            <strong>İlk oran (en büyük):</strong><br>
                            Toplam = 40%
                        </li>
                        <li class="mb-2">
                            <strong>İkinci oran ekle:</strong><br>
                            Kalan kapasite = 100 - 40 = 60%<br>
                            Ekleme = 60 × (20/100) = 12%<br>
                            Yeni toplam = 40 + 12 = 52%
                        </li>
                        <li class="mb-2">
                            <strong>Üçüncü oran ekle:</strong><br>
                            Kalan kapasite = 100 - 52 = 48%<br>
                            Ekleme = 48 × (15/100) = 7.2%<br>
                            <strong>Nihai Toplam = 52 + 7.2 = 59.2%</strong>
                        </li>
                    </ol>

                    <div class="alert alert-warning">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Önemli Not:</strong>
                        Bu hesaplama sadece bilgilendirme amaçlıdır. Resmi engellilik oranı tespiti, 
                        yetkili sağlık kurulları tarafından yapılmalıdır.
                    </div>
                </div>
            </div>

            <!-- SSS -->
            <div class="card mt-4 shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        Sık Sorulan Sorular
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Neden oranlar doğrudan toplanmıyor?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Çünkü her engel, kalan çalışma kapasitesini etkiler. Örneğin %40 engelli birinin 
                                    kalan kapasitesi %60'tır. İkinci engel bu %60'lık kapasite üzerinden hesaplanır. 
                                    Aksi halde matematiksel olarak %100'ü aşan sonuçlar çıkabilir.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Oranları hangi sırayla girmeliyim?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sıralama önemli değil! Program otomatik olarak oranları büyükten küçüğe sıralayıp 
                                    hesaplama yapar. En yüksek orandan başlanması, matematiksel olarak doğru sonucu verir.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Bu hesaplama resmi belge olarak kullanılabilir mi?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Hayır. Bu araç sadece bilgilendirme ve ön hesaplama içindir. Resmi engellilik oranı, 
                                    sağlık kurulu raporları ile belgelenir ve yetkili kurumlar tarafından onaylanmalıdır.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
let rateCount = 1;

function addRate() {
    rateCount++;
    const container = document.getElementById('ratesContainer');
    const newRow = `
        <div class="rate-row mb-3">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Engel Türü</label>
                    <input type="text" class="form-control" placeholder="Engel türünü girin" name="rate_name[]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Oran (%) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control rate-input" min="0" max="100" step="0.01" required name="rate_value[]" placeholder="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sıra</label>
                    <input type="text" class="form-control order-display" readonly value="${rateCount}">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger w-100" onclick="removeRate(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', newRow);
    updateDeleteButtons();
}

function removeRate(button) {
    button.closest('.rate-row').remove();
    rateCount--;
    updateOrderNumbers();
    updateDeleteButtons();
}

function updateOrderNumbers() {
    const rows = document.querySelectorAll('.rate-row');
    rows.forEach((row, index) => {
        row.querySelector('.order-display').value = index + 1;
    });
}

function updateDeleteButtons() {
    const rows = document.querySelectorAll('.rate-row');
    rows.forEach((row, index) => {
        const deleteBtn = row.querySelector('button[onclick*="removeRate"]');
        deleteBtn.disabled = rows.length === 1;
    });
}

document.getElementById('balthazardForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Oranları topla
    const rateInputs = document.querySelectorAll('.rate-input');
    const rateNames = document.querySelectorAll('input[name="rate_name[]"]');
    
    let rates = [];
    rateInputs.forEach((input, index) => {
        const value = parseFloat(input.value);
        if (!isNaN(value) && value > 0) {
            rates.push({
                name: rateNames[index].value || `Oran ${index + 1}`,
                value: value
            });
        }
    });
    
    if (rates.length === 0) {
        alert('Lütfen en az bir geçerli oran girin!');
        return;
    }
    
    // Büyükten küçüğe sırala
    rates.sort((a, b) => b.value - a.value);
    
    // Balthazard formülü ile hesapla
    let total = 0;
    let steps = [];
    
    rates.forEach((rate, index) => {
        if (index === 0) {
            total = rate.value;
            steps.push({
                step: 1,
                description: `<strong>${rate.name}:</strong> ${rate.value}% (En yüksek oran)`,
                calculation: `Toplam = ${rate.value}%`,
                result: total.toFixed(2)
            });
        } else {
            const remaining = 100 - total;
            const addition = (remaining * rate.value) / 100;
            const oldTotal = total;
            total += addition;
            
            steps.push({
                step: index + 1,
                description: `<strong>${rate.name}:</strong> ${rate.value}%`,
                calculation: `Kalan kapasite = 100 - ${oldTotal.toFixed(2)} = ${remaining.toFixed(2)}%<br>
                            Ekleme = ${remaining.toFixed(2)} × (${rate.value}/100) = ${addition.toFixed(2)}%<br>
                            Yeni toplam = ${oldTotal.toFixed(2)} + ${addition.toFixed(2)} = ${total.toFixed(2)}%`,
                result: total.toFixed(2)
            });
        }
    });
    
    // Sonuçları göster
    displayResults(steps, total);
});

function displayResults(steps, total) {
    // Adımları göster
    const stepsHtml = steps.map(step => `
        <div class="mb-3 pb-3 border-bottom">
            <h6 class="text-success">Adım ${step.step}:</h6>
            <p class="mb-1">${step.description}</p>
            <small class="text-muted">${step.calculation}</small>
        </div>
    `).join('');
    
    document.getElementById('calculationSteps').innerHTML = stepsHtml;
    document.getElementById('totalRate').textContent = total.toFixed(2) + '%';
    
    // İlerleme çubuğu
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    progressBar.style.width = Math.min(total, 100) + '%';
    progressText.textContent = total.toFixed(2) + '%';
    
    // Renk ayarla
    if (total < 40) {
        progressBar.className = 'progress-bar bg-success';
    } else if (total < 70) {
        progressBar.className = 'progress-bar bg-warning';
    } else {
        progressBar.className = 'progress-bar bg-danger';
    }
    
    // Sonuç bölümünü göster ve scroll
    const resultSection = document.getElementById('resultSection');
    resultSection.style.display = 'block';
    resultSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>

<style>
.rate-row {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #0d6efd;
}

.rate-row:hover {
    background-color: #e9ecef;
}

#calculationSteps {
    max-height: 400px;
    overflow-y: auto;
}

.progress {
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.accordion-button:not(.collapsed) {
    background-color: #e7f3ff;
    color: #0d6efd;
}
</style>

<?php include 'includes/footer.php'; ?>
