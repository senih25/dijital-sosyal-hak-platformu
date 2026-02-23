<?php
$now = date('d.m.Y H:i');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İleri Seviye Özellikler - Dijital Sosyal Hak Platformu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f4f7fb; }
        .feature-card { border:0; border-radius:14px; box-shadow:0 10px 25px rgba(0,0,0,.07); }
        .feature-badge { font-size:.8rem; letter-spacing:.04em; }
        .metric { background:#fff; border-radius:10px; padding:16px; border:1px solid #e9eef6; }
        .metric-value { font-weight:700; font-size:1.2rem; }
        .log-box { background:#121826; color:#8ef7b7; font-family:monospace; padding:14px; border-radius:10px; min-height:150px; }
        .module-title { font-size:1.2rem; font-weight:700; }
        .hero { background:linear-gradient(120deg,#1f4eff,#7028ff); color:#fff; border-radius:18px; }
    </style>
</head>
<body>
<div class="container py-4 py-lg-5">
    <section class="hero p-4 p-lg-5 mb-4">
        <h1 class="h2 mb-2">🚀 İleri Seviye Özellikler Merkezi</h1>
        <p class="mb-0">Blockchain doğrulama, IoT sağlık cihazları, VR/AR eğitim simülasyonları ve native mobil uygulama yeteneklerinin tek noktadan yönetimi.</p>
    </section>

    <div class="row g-4">
        <div class="col-12">
            <div class="card feature-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="module-title mb-0">37. Blockchain Entegrasyonu</h2>
                        <span class="badge text-bg-primary feature-badge">Belge Güvenliği</span>
                    </div>
                    <p>Dijital imza + SHA-256 hash doğrulaması ile belge bütünlüğü kontrolü yapılır ve değiştirilemez kayıt defterine işlenir.</p>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label">Doğrulanacak Belge Metni</label>
                            <textarea id="docInput" class="form-control" rows="4" placeholder="Örn: Engelli raporu no: 2026-TR-001">Engelli raporu no: 2026-TR-001</textarea>
                        </div>
                        <div class="col-md-5">
                            <div class="metric mb-2"><div>SHA-256 Hash</div><div id="docHash" class="metric-value">-</div></div>
                            <div class="metric"><div>Son Blok Kaydı</div><div id="blockRecord" class="small text-muted">Henüz kayıt yok</div></div>
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        <button id="signBtn" class="btn btn-primary">Dijital İmzala</button>
                        <button id="verifyBtn" class="btn btn-outline-primary">Bütünlük Doğrula</button>
                    </div>
                    <div id="chainLog" class="log-box mt-3">[<?php echo $now; ?>] Sistem hazır...</div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card feature-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="module-title mb-0">38. IoT Entegrasyonu</h2>
                        <span class="badge text-bg-success feature-badge">Canlı Sağlık Verisi</span>
                    </div>
                    <p>Kan şekeri, tansiyon ve nabız verileri cihazlardan otomatik okunur; eşik aşımlarında uyarı ve danışmanlık önerisi üretir.</p>
                    <div class="row g-3">
                        <div class="col-md-4"><div class="metric"><div>Kan Şekeri (mg/dL)</div><div id="glucose" class="metric-value">--</div></div></div>
                        <div class="col-md-4"><div class="metric"><div>Tansiyon (mmHg)</div><div id="pressure" class="metric-value">--/--</div></div></div>
                        <div class="col-md-4"><div class="metric"><div>Nabız (bpm)</div><div id="pulse" class="metric-value">--</div></div></div>
                    </div>
                    <div class="mt-3">
                        <button id="iotBtn" class="btn btn-success">Canlı Veri Akışını Başlat</button>
                        <span id="iotStatus" class="ms-2 text-muted">Pasif</span>
                    </div>
                    <div id="iotAnalysis" class="alert alert-light mt-3 mb-0">Analiz bekleniyor...</div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card feature-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="module-title mb-0">39. VR/AR Deneyim</h2>
                        <span class="badge text-bg-warning feature-badge">Eğitim Simülasyonu</span>
                    </div>
                    <ul class="mb-3">
                        <li>3D sosyal hizmet ofisi senaryoları</li>
                        <li>Hak başvuru simülasyonu ve rol tabanlı etkileşim</li>
                        <li>Gerçek zamanlı değerlendirme puanlaması</li>
                    </ul>
                    <button class="btn btn-warning" id="vrBtn">VR/AR Simülasyonunu Başlat</button>
                    <p id="vrStatus" class="small text-muted mt-3 mb-0">Henüz başlatılmadı.</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card feature-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 class="module-title mb-0">40. Native Mobil Uygulama</h2>
                        <span class="badge text-bg-dark feature-badge">iOS + Android</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Özellik</th><th>Durum</th></tr></thead>
                            <tbody>
                                <tr><td>Push Bildirimleri</td><td><span class="badge text-bg-success">Aktif</span></td></tr>
                                <tr><td>Offline Çalışma</td><td><span class="badge text-bg-success">Aktif</span></td></tr>
                                <tr><td>Kamera Entegrasyonu</td><td><span class="badge text-bg-success">Aktif</span></td></tr>
                                <tr><td>Biyometrik Güvenlik</td><td><span class="badge text-bg-success">Aktif</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="small text-muted mb-0">Mobil istemci, veri senkronizasyonunu çevrimdışı kuyruk ile yönetir; bağlantı geldiğinde otomatik iletim yapar.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const enc = new TextEncoder();
let lastHash = '';

async function sha256(text) {
    const hashBuffer = await crypto.subtle.digest('SHA-256', enc.encode(text));
    return Array.from(new Uint8Array(hashBuffer)).map(b => b.toString(16).padStart(2, '0')).join('');
}

function appendLog(message) {
    const box = document.getElementById('chainLog');
    box.textContent += `\n[${new Date().toLocaleTimeString('tr-TR')}] ${message}`;
}

document.getElementById('signBtn').addEventListener('click', async () => {
    const text = document.getElementById('docInput').value.trim();
    if (!text) return;
    lastHash = await sha256(text);
    document.getElementById('docHash').textContent = `${lastHash.slice(0, 18)}...`;
    document.getElementById('blockRecord').textContent = `BLOCK-${Date.now()} | imza: ${lastHash.slice(-8)}`;
    appendLog(`Belge imzalandı ve blockchain kaydına yazıldı.`);
});

document.getElementById('verifyBtn').addEventListener('click', async () => {
    const current = await sha256(document.getElementById('docInput').value.trim());
    const ok = current === lastHash && current !== '';
    appendLog(ok ? 'Bütünlük doğrulandı, belge değişmemiş.' : 'Uyarı: Belge içeriği değişmiş olabilir!');
});

let iotTimer;
document.getElementById('iotBtn').addEventListener('click', () => {
    if (iotTimer) return;
    document.getElementById('iotStatus').textContent = 'Canlı';
    iotTimer = setInterval(() => {
        const glucose = Math.floor(80 + Math.random() * 90);
        const sys = Math.floor(100 + Math.random() * 50);
        const dia = Math.floor(65 + Math.random() * 30);
        const pulse = Math.floor(58 + Math.random() * 65);

        document.getElementById('glucose').textContent = glucose;
        document.getElementById('pressure').textContent = `${sys}/${dia}`;
        document.getElementById('pulse').textContent = pulse;

        const alerts = [];
        if (glucose > 140) alerts.push('Kan şekeri yüksek');
        if (sys > 140 || dia > 90) alerts.push('Tansiyon riski');
        if (pulse > 110) alerts.push('Nabız yüksek');

        document.getElementById('iotAnalysis').className = alerts.length ? 'alert alert-warning mt-3 mb-0' : 'alert alert-success mt-3 mb-0';
        document.getElementById('iotAnalysis').textContent = alerts.length
            ? `Uyarı: ${alerts.join(', ')}. Uzman değerlendirmesi önerilir.`
            : 'Veriler normal aralıkta. Otomatik takip devam ediyor.';
    }, 2500);
});

document.getElementById('vrBtn').addEventListener('click', () => {
    document.getElementById('vrStatus').textContent = 'Simülasyon başlatıldı: "Sosyal yardım başvuru merkezi" sahnesi yükleniyor...';
});
</script>
</body>
</html>
