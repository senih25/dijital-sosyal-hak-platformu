<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesaplama Araçları - 2026</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php">Dijital Sosyal Hak Rehberliği</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Ana Sayfa</a>
            <a class="nav-link active" href="hesaplama_araclari_calisir.php">Hesaplama</a>
            <a class="nav-link" href="sss.php">SSS</a>
        </div>
    </div>
</nav>

<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h1 class="fw-bold">2026 Güncel Hesaplama Araçları</h1>
        <p class="mb-0">Asgari ücret: <strong>20.002 TL</strong> • Mevzuat güncellemesi entegre edildi.</p>
    </div>
</section>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white"><i class="fa-solid fa-house-user me-2"></i>Gelir Testi (2026)</div>
                <div class="card-body">
                    <form id="incomeForm" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Hane Kişi Sayısı</label>
                            <input type="number" min="1" class="form-control" id="householdSize" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Aylık Toplam Gelir (TL)</label>
                            <input type="number" min="0" class="form-control" id="monthlyIncome" required>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="specialNeed">
                                <label class="form-check-label" for="specialNeed">Hanede engelli/ağır bakım ihtiyacı olan birey var</label>
                            </div>
                        </div>
                        <div class="col-12 d-grid"><button class="btn btn-primary" type="submit">Gelir Testini Hesapla</button></div>
                    </form>
                    <div id="incomeResult" class="alert alert-info mt-3 d-none"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-success text-white"><i class="fa-solid fa-percent me-2"></i>Balthazard Hesaplama (2026)</div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Oranları virgülle girin (örn: 40,30,20).</p>
                    <form id="balthazardForm" class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Engellilik Oranları</label>
                            <input type="text" class="form-control" id="rates" required>
                        </div>
                        <div class="col-12 d-grid"><button class="btn btn-success" type="submit">Balthazard Hesapla</button></div>
                    </form>
                    <div id="balthazardResult" class="alert alert-success mt-3 d-none"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mt-4" id="reportCard">
        <div class="card-body">
            <h5 class="mb-3"><i class="fa-regular fa-file-pdf text-danger me-2"></i>Hesaplama Özeti</h5>
            <p class="text-muted mb-3">Gelir testi ve Balthazard sonuçlarını aynı raporda indirebilirsiniz.</p>
            <div id="reportContent" class="small text-muted">Henüz hesaplama yapılmadı.</div>
            <button class="btn btn-outline-danger mt-3" id="downloadPdf" type="button">PDF Olarak İndir</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
const ASGARI_UCRET_2026 = 20002;
const GELIR_TEST_ESIK_ORANI = 1 / 3; // 2026: bakım değerlendirmelerinde kişi başı gelir eşiği
const GELIR_TEST_OZEL_DURUM_KATSAYI = 1.1; // engelli/ağır kronik bakım ihtiyacında örnek bilgilendirme katsayısı
let latestIncome = null;
let latestBalthazard = null;

function renderReport() {
    const report = document.getElementById('reportContent');
    if (!latestIncome && !latestBalthazard) {
        report.innerHTML = 'Henüz hesaplama yapılmadı.';
        return;
    }

    report.innerHTML = `
        ${latestIncome ? `<div><strong>Gelir Testi:</strong> Kişi başı gelir ${latestIncome.perCapita.toFixed(2)} TL, eşik ${latestIncome.threshold.toFixed(2)} TL, durum: ${latestIncome.eligible ? 'Uygun' : 'Uygun Değil'}</div>` : ''}
        ${latestBalthazard ? `<div class="mt-2"><strong>Balthazard:</strong> Toplam engellilik oranı %${latestBalthazard.total.toFixed(2)}</div>` : ''}
    `;
}

document.getElementById('incomeForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const householdSize = Number(document.getElementById('householdSize').value);
    const monthlyIncome = Number(document.getElementById('monthlyIncome').value);
    const specialNeed = document.getElementById('specialNeed').checked;
    const baseThreshold = ASGARI_UCRET_2026 * GELIR_TEST_ESIK_ORANI;
    const threshold = specialNeed ? (baseThreshold * GELIR_TEST_OZEL_DURUM_KATSAYI) : baseThreshold;
    const perCapita = monthlyIncome / householdSize;
    const eligible = perCapita <= threshold;

    latestIncome = { householdSize, monthlyIncome, threshold, perCapita, eligible, specialNeed, baseThreshold };

    const box = document.getElementById('incomeResult');
    box.classList.remove('d-none', 'alert-info', 'alert-danger', 'alert-success');
    box.classList.add(eligible ? 'alert-success' : 'alert-danger');
    box.innerHTML = `Kişi başı gelir: <strong>${perCapita.toFixed(2)} TL</strong> • Temel eşik: <strong>${baseThreshold.toFixed(2)} TL</strong>${specialNeed ? ` • Uygulanan eşik: <strong>${threshold.toFixed(2)} TL</strong>` : ''}<br>Sonuç: <strong>${eligible ? 'Sosyal destek gelir kriterine uygun.' : 'Gelir kriteri üzerinde.'}</strong>`;

    renderReport();
});

document.getElementById('balthazardForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const parts = document.getElementById('rates').value
        .split(',')
        .map(v => Number(v.trim()))
        .filter(v => !Number.isNaN(v) && v > 0 && v <= 100)
        .sort((a, b) => b - a);

    if (parts.length === 0) {
        alert('Geçerli oran giriniz.');
        return;
    }

    let total = 0;
    parts.forEach((rate, index) => {
        total = index === 0 ? rate : total + ((100 - total) * rate / 100);
    });

    latestBalthazard = { rates: parts, total };

    const box = document.getElementById('balthazardResult');
    box.classList.remove('d-none');
    box.innerHTML = `Sıralanan oranlar: <strong>${parts.join(', ')}</strong><br>Toplam Balthazard oranı: <strong>%${total.toFixed(2)}</strong>`;

    renderReport();
});

document.getElementById('downloadPdf').addEventListener('click', function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.setFontSize(14);
    doc.text('Dijital Sosyal Hak Rehberligi - 2026 Hesaplama Raporu', 10, 15);
    doc.setFontSize(11);
    doc.text(`Asgari Ucret (2026): ${ASGARI_UCRET_2026} TL`, 10, 24);
    doc.text(`Rapor Tarihi: ${new Date().toLocaleString('tr-TR')}`, 10, 31);

    let y = 42;
    if (latestIncome) {
        doc.text('Gelir Testi', 10, y); y += 7;
        doc.text(`Hane kisi sayisi: ${latestIncome.householdSize}`, 10, y); y += 7;
        doc.text(`Aylik toplam gelir: ${latestIncome.monthlyIncome.toFixed(2)} TL`, 10, y); y += 7;
        doc.text(`Kisi basi gelir: ${latestIncome.perCapita.toFixed(2)} TL`, 10, y); y += 7;
        doc.text(`Temel esik: ${latestIncome.baseThreshold.toFixed(2)} TL`, 10, y); y += 7;
        doc.text(`Uygulanan esik: ${latestIncome.threshold.toFixed(2)} TL`, 10, y); y += 7;
        doc.text(`Ozel durum: ${latestIncome.specialNeed ? 'Evet' : 'Hayir'}`, 10, y); y += 7;
        doc.text(`Durum: ${latestIncome.eligible ? 'Uygun' : 'Uygun Degil'}`, 10, y); y += 10;
    }

    if (latestBalthazard) {
        doc.text('Balthazard', 10, y); y += 7;
        doc.text(`Oranlar: ${latestBalthazard.rates.join(', ')}`, 10, y); y += 7;
        doc.text(`Toplam oran: %${latestBalthazard.total.toFixed(2)}`, 10, y); y += 7;
    }

    if (!latestIncome && !latestBalthazard) {
        doc.text('Rapor olusturmak icin once hesaplama yapiniz.', 10, y);
    }

    doc.save('hesaplama-raporu-2026.pdf');
});
</script>
<script src="ai-chatbot.js"></script>
</body>
</html>
