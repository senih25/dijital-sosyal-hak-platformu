<?php
require_once 'functions.php';

$reportAnalysis = null;
$predictionResult = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['analyze_report'])) {
        if (!isset($_FILES['health_report']) || $_FILES['health_report']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Lütfen analiz için bir belge yükleyin.';
        } else {
            $file = $_FILES['health_report'];
            $tmpPath = $file['tmp_name'];
            $extractedText = extractTextFromDocument($tmpPath, $file['name']);
            $reportAnalysis = analyzeHealthReportWithAI($extractedText);
        }
    }

    if (isset($_POST['predict_eligibility'])) {
        $payload = [
            'household_income' => $_POST['household_income'] ?? 0,
            'household_members' => $_POST['household_members'] ?? 1,
            'disability_rate' => $_POST['disability_rate'] ?? 0,
            'chronic_illness' => $_POST['chronic_illness'] ?? '',
            'working_status' => $_POST['working_status'] ?? 'calisiyor'
        ];
        $predictionResult = predictSocialBenefitEligibility($payload);
    }
}
?>
<?php
$pageTitle = 'Yapay Zeka Entegrasyonu';
include 'header.php';
?>

<main class="container ai-page">
    <section class="ai-hero">
        <h1>🤖 Yapay Zeka Entegrasyonu</h1>
        <p>Belge analizi, sosyal hak uygunluk tahmini ve gelişmiş chatbot modülü.</p>
    </section>

    <?php if (!empty($errors)): ?>
        <div class="alert-box error">
            <?php foreach ($errors as $error): ?>
                <p><?= escape($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <section class="ai-card">
        <h2>25. Belge Analiz Sistemi</h2>
        <form method="POST" enctype="multipart/form-data" class="stack">
            <label>Sağlık Raporu Yükle (PDF/JPG/PNG/TXT)</label>
            <input type="file" name="health_report" accept=".pdf,.jpg,.jpeg,.png,.txt" required>
            <button type="submit" name="analyze_report">AI ile Analiz Et</button>
        </form>

        <?php if ($reportAnalysis): ?>
            <div class="result-panel">
                <h3>Analiz Sonucu</h3>
                <p><strong>Çıkarılan Metin:</strong> <?= escape($reportAnalysis['extracted_text']); ?></p>
                <p><strong>Kategoriler:</strong> <?= escape(implode(', ', $reportAnalysis['categories'])); ?></p>
                <p><strong>Risk Seviyesi:</strong> <?= escape($reportAnalysis['risk_level']); ?> (%<?= escape((string)$reportAnalysis['risk_score']); ?>)</p>
                <p><strong>Risk Sinyalleri:</strong> <?= escape(!empty($reportAnalysis['risk_signals']) ? implode(', ', $reportAnalysis['risk_signals']) : 'Sinyal bulunamadı'); ?></p>
            </div>
        <?php endif; ?>
    </section>

    <section class="ai-card">
        <h2>26. Tahmin Sistemi</h2>
        <form method="POST" class="grid-form">
            <label>Hane Geliri (₺)
                <input type="number" step="0.01" min="0" name="household_income" required>
            </label>
            <label>Hane Üye Sayısı
                <input type="number" min="1" name="household_members" required>
            </label>
            <label>Engellilik Oranı (%)
                <input type="number" min="0" max="100" name="disability_rate" required>
            </label>
            <label>Çalışma Durumu
                <select name="working_status">
                    <option value="calisiyor">Çalışıyor</option>
                    <option value="duzensiz">Düzensiz Gelir</option>
                    <option value="calismiyor">Çalışmıyor</option>
                </select>
            </label>
            <label class="full">Kronik Hastalık Durumu
                <input type="checkbox" name="chronic_illness" value="1"> Var
            </label>
            <button type="submit" name="predict_eligibility" class="full">Uygunluk Tahmini Yap</button>
        </form>

        <?php if ($predictionResult): ?>
            <div class="result-panel">
                <h3>Tahmin Sonucu</h3>
                <p><strong>Başarı Olasılığı:</strong> %<?= escape((string)$predictionResult['success_probability']); ?></p>
                <p><strong>Segment:</strong> <?= escape($predictionResult['segment']); ?></p>
                <p><strong>Kişi Başı Gelir:</strong> <?= escape(number_format($predictionResult['per_capita_income'], 2, ',', '.')); ?> ₺</p>
                <ul>
                    <?php foreach ($predictionResult['suggestions'] as $suggestion): ?>
                        <li><?= escape($suggestion); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </section>

    <section class="ai-card">
        <h2>27. Chatbot Geliştirme</h2>
        <p>Bağlamsal yanıt, çok dilli destek (TR/EN), öğrenme puanı ve sesli etkileşim içerir.</p>
        <div class="chatbot-shell" id="ai-chatbot" data-lang="tr">
            <div class="chat-header">
                <strong>Hak Asistanı AI</strong>
                <div>
                    <select id="chat-lang">
                        <option value="tr">Türkçe</option>
                        <option value="en">English</option>
                    </select>
                    <button type="button" id="voice-btn">🎤</button>
                </div>
            </div>
            <div class="chat-log" id="chat-log"></div>
            <form id="chat-form" class="chat-form">
                <input type="text" id="chat-input" placeholder="Sorunuzu yazın..." required>
                <button type="submit">Gönder</button>
            </form>
            <small id="learning-score">Öğrenme skoru: 0</small>
        </div>
    </section>
</main>

<script src="script.js"></script>
<?php include 'footer.php'; ?>
