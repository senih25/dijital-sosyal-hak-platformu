<?php
// Genel yardımcı fonksiyonlar

// Güvenli HTML çıktısı
function escape($html) {
    return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
}

// URL slug oluşturma
function createSlug($text) {
    $turkish = array('ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç');
    $english = array('i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c');
    $text = str_replace($turkish, $english, $text);
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

// Şifre hashleme
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Şifre doğrulama
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Oturum kontrolü
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Admin kontrolü
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Kullanıcı bilgisi al
function getCurrentUser($pdo) {
    if (!isLoggedIn()) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND status = 'active'");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Yönlendirme
function redirect($url) {
    header("Location: " . $url);
    exit;
}

// Mesaj gösterme
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Tarih formatla
function formatDate($date, $format = 'd.m.Y') {
    return date($format, strtotime($date));
}

// Fiyat formatla
function formatPrice($price) {
    return number_format($price, 2, ',', '.') . ' ₺';
}

// Kısa metin
function truncateText($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

// Dosya yükleme
function uploadFile($file, $directory = 'uploads/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }
    
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        return false;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $uploadPath = ROOT_PATH . '/' . $directory;
    
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {
        return $directory . $filename;
    }
    
    return false;
}

// Site ayarı al
function getSetting($pdo, $key, $default = '') {
    static $settings = [];
    
    if (empty($settings)) {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings[$key] ?? $default;
}

// CSRF token oluştur
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF token doğrula
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Gelir testi hesaplama
function calculateIncomeTest($householdIncome, $householdMembers) {
    $perPersonIncome = $householdIncome / $householdMembers;
    
    // 2024 asgari ücret net değeri (örnek - güncellenebilir)
    $minWage = 17002.12;
    $minWageThirdPart = $minWage / 3;
    
    $eligibility = [];
    
    if ($perPersonIncome < $minWageThirdPart) {
        $eligibility[] = "Evde Bakım Maaşı";
        $eligibility[] = "2022 Sayılı Kanun Engelli Aylığı";
        $eligibility[] = "Sosyal Yardım Programları";
    } else if ($perPersonIncome < ($minWageThirdPart * 2)) {
        $eligibility[] = "Bazı Sosyal Yardım Programları";
    } else {
        $eligibility[] = "Gelir sınırı üzerinde - Belirli yardımlar için uygun olmayabilirsiniz";
    }
    
    return [
        'per_person_income' => $perPersonIncome,
        'min_wage' => $minWage,
        'threshold' => $minWageThirdPart,
        'eligibility' => $eligibility
    ];
}

// Engel oranına göre haklar
function getDisabilityRights($disabilityRate) {
    $rights = [];
    
    if ($disabilityRate >= 40) {
        $rights[] = "Engelli Kimlik Kartı";
        $rights[] = "Engelli Sağlık Kurulu Raporu";
        $rights[] = "Ücretsiz/İndirimli Ulaşım";
    }
    
    if ($disabilityRate >= 50) {
        $rights[] = "Engelli İstihdamı Kontenjanı";
        $rights[] = "MTV Muafiyeti (Taşıt için)";
    }
    
    if ($disabilityRate >= 60) {
        $rights[] = "2022 Sayılı Kanun Engelli Aylığı (Gelir testi ile)";
    }
    
    if ($disabilityRate >= 70) {
        $rights[] = "Evde Bakım Maaşı (Gelir testi ile)";
        $rights[] = "Malulen Emeklilik (Sigorta şartları ile)";
    }
    
    if ($disabilityRate >= 80) {
        $rights[] = "Yoğun Bakım Desteği";
        $rights[] = "Ağır Engelli Hakları";
    }
    
    return $rights;
}

// E-posta gönderme (basit versiyon - SMTP entegrasyonu yapılabilir)
function sendEmail($to, $subject, $message) {
    $headers = "From: " . getSetting($GLOBALS['pdo'], 'contact_email', 'noreply@sosyalhizmet.com') . "\r\n";
    $headers .= "Reply-To: " . getSetting($GLOBALS['pdo'], 'contact_email', 'noreply@sosyalhizmet.com') . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}



// Basit OCR + belge analiz sistemi
function extractTextFromDocument($filePath, $originalName = '') {
    $extension = strtolower(pathinfo($originalName ?: $filePath, PATHINFO_EXTENSION));

    if ($extension === 'txt') {
        return trim((string) file_get_contents($filePath));
    }

    // PDF/DOCX/Image için örnek OCR akışı (üretim ortamında Tesseract/Azure/AWS Textract bağlanabilir)
    $normalizedName = strtolower($originalName);
    $tokens = preg_split('/[^a-z0-9çğıöşü]+/iu', $normalizedName);
    $tokens = array_filter($tokens);

    $hints = [];
    foreach ($tokens as $token) {
        if (in_array($token, ['kanser', 'onkoloji', 'kemoterapi', 'radyoterapi'], true)) {
            $hints[] = 'Onkoloji tedavi geçmişi ifadesi tespit edildi';
        }
        if (in_array($token, ['diyabet', 'şeker'], true)) {
            $hints[] = 'Diyabet ile ilişkili anahtar kelime bulundu';
        }
        if (in_array($token, ['kalp', 'kardiyo', 'hipertansiyon'], true)) {
            $hints[] = 'Kardiyovasküler risk göstergesi bulundu';
        }
        if (in_array($token, ['engelli', 'rapor', 'heyet'], true)) {
            $hints[] = 'Engellilik raporu bağlamı algılandı';
        }
    }

    if (empty($hints)) {
        $hints[] = 'OCR örnek motoru dosya adından sınırlı metin çıkardı';
    }

    return implode('. ', $hints) . '.';
}

function analyzeHealthReportWithAI($extractedText) {
    $text = mb_strtolower($extractedText, 'UTF-8');

    $categories = [
        'Kronik Hastalık' => ['diyabet', 'hipertansiyon', 'koah', 'astım', 'kronik'],
        'Onkoloji' => ['kanser', 'onkoloji', 'kemoterapi', 'radyoterapi', 'metastaz'],
        'Nörolojik Durum' => ['epilepsi', 'parkinson', 'alzheimer', 'nöroloji', 'felç'],
        'Ortopedik Değerlendirme' => ['ortopedi', 'amputasyon', 'protezi', 'hareket kısıtlılığı'],
        'Psikiyatrik Değerlendirme' => ['depresyon', 'anksiyete', 'bipolar', 'psikoz', 'travma']
    ];

    $matchedCategories = [];
    foreach ($categories as $category => $keywords) {
        foreach ($keywords as $keyword) {
            if (mb_stripos($text, $keyword, 0, 'UTF-8') !== false) {
                $matchedCategories[] = $category;
                break;
            }
        }
    }

    if (empty($matchedCategories)) {
        $matchedCategories[] = 'Genel Sağlık Değerlendirmesi';
    }

    $riskRules = [
        'Yüksek' => ['metastaz', 'yoğun bakım', 'acil', 'ileri evre', 'çoklu organ'],
        'Orta' => ['ameliyat', 'kronik', 'düzenli takip', 'ilaç raporu', 'engel oranı'],
        'Düşük' => ['kontrol', 'stabil', 'hafif', 'takip önerildi']
    ];

    $riskScore = 10;
    $riskSignals = [];
    foreach ($riskRules['Yüksek'] as $signal) {
        if (mb_stripos($text, $signal, 0, 'UTF-8') !== false) {
            $riskScore += 25;
            $riskSignals[] = $signal;
        }
    }
    foreach ($riskRules['Orta'] as $signal) {
        if (mb_stripos($text, $signal, 0, 'UTF-8') !== false) {
            $riskScore += 12;
            $riskSignals[] = $signal;
        }
    }
    foreach ($riskRules['Düşük'] as $signal) {
        if (mb_stripos($text, $signal, 0, 'UTF-8') !== false) {
            $riskScore -= 5;
        }
    }

    $riskScore = max(0, min(100, $riskScore));
    $riskLevel = $riskScore >= 65 ? 'Yüksek' : ($riskScore >= 35 ? 'Orta' : 'Düşük');

    return [
        'extracted_text' => $extractedText,
        'categories' => array_values(array_unique($matchedCategories)),
        'risk_score' => $riskScore,
        'risk_level' => $riskLevel,
        'risk_signals' => array_values(array_unique($riskSignals))
    ];
}

// Sosyal hak uygunluk tahmini (kural tabanlı ML benzeri skorlayıcı)
function predictSocialBenefitEligibility($userData) {
    $income = (float)($userData['household_income'] ?? 0);
    $members = max(1, (int)($userData['household_members'] ?? 1));
    $disabilityRate = max(0, min(100, (int)($userData['disability_rate'] ?? 0)));
    $chronicIllness = !empty($userData['chronic_illness']);
    $workingStatus = $userData['working_status'] ?? 'calisiyor';

    $perCapita = $income / $members;
    $minWage = 17002.12;
    $threshold = $minWage / 3;

    $score = 0.20;

    if ($perCapita <= $threshold) {
        $score += 0.35;
    } elseif ($perCapita <= $threshold * 1.6) {
        $score += 0.20;
    } else {
        $score += 0.05;
    }

    $score += min(0.30, $disabilityRate / 100 * 0.30);

    if ($chronicIllness) {
        $score += 0.08;
    }

    if ($workingStatus === 'calismiyor') {
        $score += 0.07;
    } elseif ($workingStatus === 'duzensiz') {
        $score += 0.04;
    }

    $successProbability = max(0, min(0.98, $score));

    $suggestions = [];
    if ($perCapita > $threshold) {
        $suggestions[] = 'Gelir testi başvurusunda güncel gider kalemlerinizi belgeleyin.';
    }
    if ($disabilityRate < 40) {
        $suggestions[] = 'Yeni sağlık kurulu değerlendirmesi ile engel oranı güncellemesini düşünün.';
    } else {
        $suggestions[] = 'Engelli kimlik kartı ve ulaşım destekleri için belediye başvurularını kontrol edin.';
    }
    if ($chronicIllness) {
        $suggestions[] = 'Kronik hastalık raporlarını e-Nabız çıktıları ile destekleyin.';
    }

    $segment = $successProbability >= 0.70 ? 'Yüksek Potansiyel' : ($successProbability >= 0.45 ? 'Geliştirilebilir' : 'Destek Gerekiyor');

    return [
        'success_probability' => round($successProbability * 100, 2),
        'segment' => $segment,
        'per_capita_income' => round($perCapita, 2),
        'threshold' => round($threshold, 2),
        'suggestions' => $suggestions
    ];
}


// Sipariş numarası oluştur
function generateOrderNumber() {
    return 'SHR' . date('Ymd') . rand(1000, 9999);
}
?>
