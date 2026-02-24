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

// Sipariş numarası oluştur
function generateOrderNumber() {
    return 'SHR' . date('Ymd') . rand(1000, 9999);
}

// E-ticaret ödeme sağlayıcıları
function getPaymentProviders() {
    return [
        'iyzico' => [
            'name' => 'İyzico',
            'commission_rate' => 0.029,
            'fixed_fee' => 0.25,
            'currency' => 'TRY'
        ],
        'paytr' => [
            'name' => 'PayTR',
            'commission_rate' => 0.027,
            'fixed_fee' => 0.35,
            'currency' => 'TRY'
        ]
    ];
}

// Ödeme özeti hesapla (demo simülasyon)
function calculatePaymentBreakdown($amount, $providerKey) {
    $providers = getPaymentProviders();

    if (!isset($providers[$providerKey])) {
        return null;
    }

    $provider = $providers[$providerKey];
    $commission = ($amount * $provider['commission_rate']) + $provider['fixed_fee'];
    $netAmount = $amount - $commission;

    return [
        'provider' => $provider['name'],
        'gross_amount' => $amount,
        'commission' => max($commission, 0),
        'net_amount' => max($netAmount, 0),
        'currency' => $provider['currency']
    ];
}

// Fatura oluştur (demo)
function createInvoiceRecord($customerName, $orderNumber, $items, $taxRate = 0.20) {
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += ($item['quantity'] * $item['unit_price']);
    }

    $taxAmount = $subtotal * $taxRate;
    $total = $subtotal + $taxAmount;

    return [
        'invoice_no' => 'FAT-' . date('Ymd') . '-' . rand(10000, 99999),
        'customer_name' => $customerName,
        'order_number' => $orderNumber,
        'items' => $items,
        'subtotal' => $subtotal,
        'tax_rate' => $taxRate,
        'tax_amount' => $taxAmount,
        'total' => $total,
        'issued_at' => date('Y-m-d H:i:s')
    ];
}

// Kargo akışı
function getCargoTimeline($trackingNumber) {
    return [
        ['status' => 'Sipariş Alındı', 'time' => date('Y-m-d H:i', strtotime('-2 days'))],
        ['status' => 'Kargoya Verildi', 'time' => date('Y-m-d H:i', strtotime('-1 day'))],
        ['status' => 'Transfer Merkezinde', 'time' => date('Y-m-d H:i', strtotime('-8 hours'))],
        ['status' => 'Dağıtıma Çıktı', 'time' => date('Y-m-d H:i', strtotime('-2 hours'))],
        ['status' => 'Takip No', 'time' => $trackingNumber]
    ];
}

// İade/değişim uygunluk kontrolü
function evaluateReturnRequest($deliveredAt, $requestType = 'return', $maxDays = 14) {
    $deliveredTime = strtotime($deliveredAt);
    $diffDays = floor((time() - $deliveredTime) / 86400);
    $isEligible = $diffDays <= $maxDays;

    return [
        'type' => $requestType,
        'days_passed' => $diffDays,
        'max_days' => $maxDays,
        'eligible' => $isEligible,
        'next_step' => $isEligible ? 'Müşteri hizmetleri onayı bekleniyor' : 'Süre aşıldığı için manuel inceleme gerekli'
    ];
}

// Abonelik paketleri
function getSubscriptionPlans() {
    return [
        'premium_monthly' => [
            'title' => 'Premium Aylık',
            'price' => 799,
            'period' => 'monthly',
            'level' => 'Premium'
        ],
        'premium_yearly' => [
            'title' => 'Premium Yıllık',
            'price' => 7990,
            'period' => 'yearly',
            'level' => 'Premium+'
        ]
    ];
}

function simulateSubscription($planKey, $startedAt = null) {
    $plans = getSubscriptionPlans();
    if (!isset($plans[$planKey])) {
        return null;
    }

    $startedAt = $startedAt ?: date('Y-m-d');
    $periodText = $plans[$planKey]['period'] === 'monthly' ? '+1 month' : '+1 year';

    return [
        'plan' => $plans[$planKey],
        'started_at' => $startedAt,
        'next_payment_date' => date('Y-m-d', strtotime($periodText, strtotime($startedAt))),
        'auto_renewal' => true,
        'exclusive_contents' => [
            'Canlı uzman webinarları',
            'Üyeye özel rehber şablonları',
            'Öncelikli danışmanlık hattı'
        ]
    ];
}

// Referans sistemi
function generateReferralCode($prefix = 'DSH') {
    return $prefix . strtoupper(substr(md5(uniqid('', true)), 0, 7));
}

function calculateReferralCommission($saleAmount, $rate = 0.10) {
    return [
        'sale_amount' => $saleAmount,
        'rate' => $rate,
        'commission' => $saleAmount * $rate
    ];
}

function calculateReferralRewardLevel($totalCommission) {
    if ($totalCommission >= 5000) {
        return 'Platin Elçi';
    }
    if ($totalCommission >= 2000) {
        return 'Altın Elçi';
    }
    if ($totalCommission >= 750) {
        return 'Gümüş Elçi';
    }
    return 'Başlangıç';
}
?>
