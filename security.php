<?php
/**
 * Güvenlik ve KVKK Uyumluluk Fonksiyonları
 * Dijital Sosyal Hizmet Platformu
 */

class SecurityManager {
    
    /**
     * KVKK Uyumlu Veri Maskeleme
     */
    public static function maskPersonalData($data, $type = 'tc') {
        switch($type) {
            case 'tc':
                // TC Kimlik No: 12345***890
                return substr($data, 0, 5) . '***' . substr($data, -3);
            case 'phone':
                // Telefon: 0532***4567
                return substr($data, 0, 4) . '***' . substr($data, -4);
            case 'email':
                // Email: user***@domain.com
                $parts = explode('@', $data);
                return substr($parts[0], 0, 4) . '***@' . $parts[1];
            case 'name':
                // İsim: Ahmet ***
                $parts = explode(' ', $data);
                return $parts[0] . ' ***';
            default:
                return '***';
        }
    }
    
    /**
     * SQL Injection Koruması
     */
    public static function sanitizeInput($input) {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        return $input;
    }
    
    /**
     * XSS Koruması
     */
    public static function preventXSS($data) {
        return htmlentities($data, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * CSRF Token Oluşturma
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * CSRF Token Doğrulama
     */
    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Rate Limiting (Hız Sınırlama)
     */
    public static function checkRateLimit($ip, $action = 'general', $limit = 60, $window = 3600) {
        $key = "rate_limit_{$action}_{$ip}";
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'start_time' => time()];
        }
        
        $current_time = time();
        $session_data = $_SESSION[$key];
        
        // Zaman penceresi sıfırlama
        if ($current_time - $session_data['start_time'] > $window) {
            $_SESSION[$key] = ['count' => 1, 'start_time' => $current_time];
            return true;
        }
        
        // Limit kontrolü
        if ($session_data['count'] >= $limit) {
            return false;
        }
        
        $_SESSION[$key]['count']++;
        return true;
    }
    
    /**
     * Güvenli Dosya Yükleme
     */
    public static function validateFileUpload($file, $allowed_types = ['pdf', 'jpg', 'png', 'doc', 'docx']) {
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $file_size = $file['size'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        // Dosya türü kontrolü
        if (!in_array($file_extension, $allowed_types)) {
            return ['success' => false, 'message' => 'İzin verilmeyen dosya türü'];
        }
        
        // Dosya boyutu kontrolü
        if ($file_size > $max_size) {
            return ['success' => false, 'message' => 'Dosya boyutu çok büyük (Max: 5MB)'];
        }
        
        // MIME type kontrolü
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mimes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (!in_array($mime_type, $allowed_mimes)) {
            return ['success' => false, 'message' => 'Geçersiz dosya içeriği'];
        }
        
        return ['success' => true, 'message' => 'Dosya geçerli'];
    }
    
    /**
     * Güvenli Session Başlatma
     */
    public static function secureSessionStart() {
        // Session güvenlik ayarları
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_strict_mode', 1);
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Session hijacking koruması
        if (!isset($_SESSION['user_ip'])) {
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
        } elseif ($_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
            session_destroy();
            session_start();
        }
    }
    
    /**
     * Güvenli Parola Hash'leme
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    
    /**
     * Parola Doğrulama
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}

/**
 * KVKK Uyumluluk Sınıfı
 */
class KVKKCompliance {
    
    /**
     * Veri İşleme Onayı Kaydetme
     */
    public static function recordConsent($user_id, $consent_type, $ip_address) {
        global $pdo;
        
        $stmt = $pdo->prepare("
            INSERT INTO kvkk_onaylar (kullanici_id, onay_turu, ip_adresi, onay_tarihi) 
            VALUES (?, ?, ?, NOW())
        ");
        
        return $stmt->execute([$user_id, $consent_type, $ip_address]);
    }
    
    /**
     * Veri Silme Talebi
     */
    public static function requestDataDeletion($user_id, $reason) {
        global $pdo;
        
        $stmt = $pdo->prepare("
            INSERT INTO kvkk_silme_talepleri (kullanici_id, talep_nedeni, talep_tarihi, durum) 
            VALUES (?, ?, NOW(), 'beklemede')
        ");
        
        return $stmt->execute([$user_id, $reason]);
    }
    
    /**
     * Veri Taşınabilirlik
     */
    public static function exportUserData($user_id) {
        global $pdo;
        
        // Kullanıcı verilerini topla
        $user_data = [];
        
        // Temel bilgiler
        $stmt = $pdo->prepare("SELECT * FROM kullanicilar WHERE id = ?");
        $stmt->execute([$user_id]);
        $user_data['profile'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Danışmanlık geçmişi
        $stmt = $pdo->prepare("SELECT * FROM danismanlik_talepleri WHERE kullanici_id = ?");
        $stmt->execute([$user_id]);
        $user_data['consultations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // KVKK onayları
        $stmt = $pdo->prepare("SELECT * FROM kvkk_onaylar WHERE kullanici_id = ?");
        $stmt->execute([$user_id]);
        $user_data['consents'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return json_encode($user_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Veri İşleme Amaçları
     */
    public static function getProcessingPurposes() {
        return [
            'hizmet_sunumu' => 'Sosyal hizmet danışmanlığı sunumu',
            'iletisim' => 'Kullanıcı ile iletişim kurma',
            'analiz' => 'Hizmet kalitesini artırma amaçlı analiz',
            'yasal_yükümlülük' => 'Yasal yükümlülüklerin yerine getirilmesi',
            'meşru_menfaat' => 'Meşru menfaatlerin korunması'
        ];
    }
}

/**
 * Audit Log (Denetim Kaydı) Sınıfı
 */
class AuditLogger {
    
    public static function log($action, $user_id = null, $details = null, $ip_address = null) {
        global $pdo;
        
        $ip_address = $ip_address ?: $_SERVER['REMOTE_ADDR'];
        
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (kullanici_id, islem, detaylar, ip_adresi, islem_tarihi) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        return $stmt->execute([$user_id, $action, $details, $ip_address]);
    }
}
?>