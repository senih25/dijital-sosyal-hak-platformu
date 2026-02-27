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
        switch ($type) {
            case 'tc':
                return substr($data, 0, 5) . '***' . substr($data, -3);
            case 'phone':
                return substr($data, 0, 4) . '***' . substr($data, -4);
            case 'email':
                $parts = explode('@', $data);
                return substr($parts[0], 0, 4) . '***@' . ($parts[1] ?? '***');
            case 'name':
                $parts = explode(' ', $data);
                return ($parts[0] ?? '***') . ' ***';
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
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
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

        if ($current_time - $session_data['start_time'] > $window) {
            $_SESSION[$key] = ['count' => 1, 'start_time' => $current_time];
            return true;
        }

        if ($session_data['count'] >= $limit) {
            SecurityLogger::logSuspiciousActivity('rate_limit_exceeded', null, [
                'action' => $action,
                'ip_address' => $ip,
                'limit' => $limit,
                'window' => $window
            ]);
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
        $max_size = 5 * 1024 * 1024;

        if (!in_array($file_extension, $allowed_types, true)) {
            SecurityLogger::logFileAccess('upload_denied', $file['name'], [
                'reason' => 'İzin verilmeyen dosya türü',
                'extension' => $file_extension
            ]);
            return ['success' => false, 'message' => 'İzin verilmeyen dosya türü'];
        }

        if ($file_size > $max_size) {
            SecurityLogger::logFileAccess('upload_denied', $file['name'], [
                'reason' => 'Dosya boyutu limiti aşıldı',
                'size' => $file_size
            ]);
            return ['success' => false, 'message' => 'Dosya boyutu çok büyük (Max: 5MB)'];
        }

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

        if (!in_array($mime_type, $allowed_mimes, true)) {
            SecurityLogger::logFileAccess('upload_denied', $file['name'], [
                'reason' => 'Geçersiz MIME türü',
                'mime_type' => $mime_type
            ]);
            return ['success' => false, 'message' => 'Geçersiz dosya içeriği'];
        }

        SecurityLogger::logFileAccess('upload_validated', $file['name'], [
            'size' => $file_size,
            'mime_type' => $mime_type
        ]);

        return ['success' => true, 'message' => 'Dosya geçerli'];
    }

    /**
     * Güvenli Session Başlatma
     */
    public static function secureSessionStart() {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
        ini_set('session.use_strict_mode', 1);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_ip'])) {
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        } elseif ($_SESSION['user_ip'] !== ($_SERVER['REMOTE_ADDR'] ?? 'unknown')) {
            SecurityLogger::logSuspiciousActivity('session_ip_mismatch', null, [
                'expected_ip' => $_SESSION['user_ip'],
                'actual_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
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

    public static function recordConsent($user_id, $consent_type, $ip_address) {
        global $pdo;

        if (isset($pdo)) {
            $stmt = $pdo->prepare('INSERT INTO kvkk_onaylar (kullanici_id, onay_turu, ip_adresi, onay_tarihi) VALUES (?, ?, ?, NOW())');
            $result = $stmt->execute([$user_id, $consent_type, $ip_address]);
        } else {
            $result = true;
        }

        SecurityLogger::logDataChange('kvkk_consent_recorded', $user_id, [
            'consent_type' => $consent_type,
            'ip_address' => $ip_address
        ]);

        return $result;
    }

    public static function updateDataProcessingPermissions($user_id, $permissions) {
        $_SESSION['data_processing_permissions'][$user_id] = [
            'permissions' => $permissions,
            'updated_at' => date('c')
        ];

        SecurityLogger::logDataChange('kvkk_permissions_updated', $user_id, [
            'permissions' => $permissions
        ]);

        return true;
    }

    public static function requestDataDeletion($user_id, $reason) {
        global $pdo;

        if (isset($pdo)) {
            $stmt = $pdo->prepare("INSERT INTO kvkk_silme_talepleri (kullanici_id, talep_nedeni, talep_tarihi, durum) VALUES (?, ?, NOW(), 'beklemede')");
            $result = $stmt->execute([$user_id, $reason]);
        } else {
            $_SESSION['kvkk_requests'][] = [
                'user_id' => $user_id,
                'request_type' => 'silme',
                'reason' => $reason,
                'status' => 'beklemede',
                'created_at' => date('c')
            ];
            $result = true;
        }

        SecurityLogger::logDataChange('kvkk_deletion_requested', $user_id, ['reason' => $reason]);
        return $result;
    }

    public static function requestDataCorrection($user_id, $fields) {
        $_SESSION['kvkk_requests'][] = [
            'user_id' => $user_id,
            'request_type' => 'duzeltme',
            'fields' => $fields,
            'status' => 'beklemede',
            'created_at' => date('c')
        ];

        SecurityLogger::logDataChange('kvkk_correction_requested', $user_id, ['fields' => $fields]);
        return true;
    }

    public static function requestDataPortability($user_id, $format = 'json') {
        $_SESSION['kvkk_requests'][] = [
            'user_id' => $user_id,
            'request_type' => 'tasinabilirlik',
            'format' => $format,
            'status' => 'hazirlaniyor',
            'created_at' => date('c')
        ];

        SecurityLogger::logDataChange('kvkk_portability_requested', $user_id, ['format' => $format]);
        return self::exportUserData($user_id, $format);
    }

    public static function exportUserData($user_id, $format = 'json') {
        global $pdo;

        $user_data = [
            'meta' => [
                'exported_at' => date('c'),
                'user_id' => $user_id,
                'purpose' => 'KVKK veri taşınabilirlik talebi'
            ]
        ];

        if (isset($pdo)) {
            $stmt = $pdo->prepare('SELECT * FROM kullanicilar WHERE id = ?');
            $stmt->execute([$user_id]);
            $user_data['profile'] = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare('SELECT * FROM danismanlik_talepleri WHERE kullanici_id = ?');
            $stmt->execute([$user_id]);
            $user_data['consultations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare('SELECT * FROM kvkk_onaylar WHERE kullanici_id = ?');
            $stmt->execute([$user_id]);
            $user_data['consents'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $user_data['profile'] = [
                'id' => $user_id,
                'name' => 'Demo Kullanıcı',
                'email' => 'demo@example.com'
            ];
            $user_data['consents'] = $_SESSION['data_processing_permissions'][$user_id]['permissions'] ?? [];
            $user_data['requests'] = $_SESSION['kvkk_requests'] ?? [];
        }

        SecurityLogger::logDataChange('kvkk_data_exported', $user_id, ['format' => $format]);

        if ($format === 'csv') {
            $lines = ["alan,deger"];
            foreach ($user_data['profile'] as $key => $value) {
                $lines[] = $key . ',"' . str_replace('"', '""', (string) $value) . '"';
            }
            return implode("\n", $lines);
        }

        return json_encode($user_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public static function getProcessingPurposes() {
        return [
            'hizmet_sunumu' => 'Sosyal hizmet danışmanlığı sunumu',
            'iletisim' => 'Kullanıcı ile iletişim kurma',
            'analiz' => 'Hizmet kalitesini artırma amaçlı analiz',
            'yasal_yukumluluk' => 'Yasal yükümlülüklerin yerine getirilmesi',
            'mesru_menfaat' => 'Meşru menfaatlerin korunması'
        ];
    }

    public static function getPrivacyNoticeSections() {
        return [
            'veri_sorumlusu' => 'Sosyal Hizmet Rehberlik & Danışmanlık Platformu',
            'toplanan_veriler' => [
                'Kimlik ve iletişim bilgileri',
                'İşlem güvenliği kayıtları',
                'Talep ve başvuru kayıtları',
                'Çerez tercihleri ve izinler'
            ],
            'kullanici_haklari' => [
                'Veriye erişim',
                'Düzeltme talebi',
                'Silme/yok etme talebi',
                'Taşınabilirlik talebi',
                'İşlemeye itiraz'
            ]
        ];
    }
}

/**
 * İki Faktörlü Doğrulama Sınıfı
 */
class TwoFactorAuthManager {

    public static function generateChallenge($user_id, $method = 'email') {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires_at = time() + 300;

        $_SESSION['2fa'][$user_id] = [
            'method' => $method,
            'code' => $code,
            'expires_at' => $expires_at,
            'verified' => false
        ];

        SecurityLogger::logAuthAttempt($user_id, '2fa_challenge_generated', true, [
            'method' => $method,
            'expires_at' => date('c', $expires_at)
        ]);

        return $code;
    }

    public static function sendCode($user_id, $destination, $method = 'email') {
        $code = self::generateChallenge($user_id, $method);

        // Demo ortamı için simülasyon.
        $_SESSION['2fa_last_delivery'] = [
            'user_id' => $user_id,
            'destination' => $destination,
            'method' => $method,
            'code' => $code,
            'sent_at' => date('c')
        ];

        SecurityLogger::logAuthAttempt($user_id, '2fa_code_sent', true, [
            'destination' => SecurityManager::maskPersonalData($destination, $method === 'sms' ? 'phone' : 'email'),
            'method' => $method
        ]);

        return $code;
    }

    public static function verifyCode($user_id, $code) {
        if (!isset($_SESSION['2fa'][$user_id])) {
            SecurityLogger::logAuthAttempt($user_id, '2fa_verify_failed', false, ['reason' => 'challenge_not_found']);
            return false;
        }

        $challenge = $_SESSION['2fa'][$user_id];

        if (time() > $challenge['expires_at']) {
            SecurityLogger::logAuthAttempt($user_id, '2fa_verify_failed', false, ['reason' => 'expired']);
            return false;
        }

        $valid = hash_equals($challenge['code'], trim($code));
        $_SESSION['2fa'][$user_id]['verified'] = $valid;

        SecurityLogger::logAuthAttempt($user_id, '2fa_verify', $valid, ['method' => $challenge['method']]);
        return $valid;
    }

    public static function generateAuthenticatorSecret($length = 32) {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        return $secret;
    }

    public static function verifyAuthenticatorCode($secret, $userCode, $window = 1) {
        $currentTimeSlice = floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            $calculated = self::totpCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculated, trim($userCode))) {
                SecurityLogger::logAuthAttempt(null, 'authenticator_verify', true);
                return true;
            }
        }

        SecurityLogger::logAuthAttempt(null, 'authenticator_verify', false);
        return false;
    }

    public static function verifySecurityQuestion($userAnswer, $expectedAnswer) {
        $valid = hash_equals(
            mb_strtolower(trim($expectedAnswer), 'UTF-8'),
            mb_strtolower(trim($userAnswer), 'UTF-8')
        );

        SecurityLogger::logAuthAttempt(null, 'security_question_verify', $valid);
        return $valid;
    }

    private static function base32Decode($secret) {
        if (empty($secret)) {
            return '';
        }

        $map = array_flip(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'));
        $secret = strtoupper($secret);
        $binaryString = '';

        for ($i = 0; $i < strlen($secret); $i++) {
            if (!isset($map[$secret[$i]])) {
                continue;
            }
            $binaryString .= str_pad(decbin($map[$secret[$i]]), 5, '0', STR_PAD_LEFT);
        }

        $decoded = '';
        foreach (str_split($binaryString, 8) as $byte) {
            if (strlen($byte) === 8) {
                $decoded .= chr(bindec($byte));
            }
        }

        return $decoded;
    }

    private static function totpCode($secret, $timeSlice) {
        $secretKey = self::base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncatedHash = substr($hash, $offset, 4);
        $value = unpack('N', $truncatedHash)[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }
}

/**
 * Güvenlik Log Sistemi
 */
class SecurityLogger {

    private static function getLogDir() {
        $logDir = __DIR__ . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        return $logDir;
    }

    public static function log($type, $message, $context = []) {
        $record = [
            'timestamp' => date('c'),
            'type' => $type,
            'message' => $message,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'cli',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'context' => $context
        ];

        file_put_contents(
            self::getLogDir() . '/security.log',
            json_encode($record, JSON_UNESCAPED_UNICODE) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );

        return true;
    }

    public static function logAuthAttempt($user_id, $action, $success, $context = []) {
        $context['user_id'] = $user_id;
        $context['success'] = $success;
        self::log('auth', $action, $context);

        if (!$success) {
            self::checkSuspiciousActivity($_SERVER['REMOTE_ADDR'] ?? 'unknown', 'auth_failed');
        }

        return true;
    }

    public static function logFileAccess($action, $file_path, $context = []) {
        $context['file_path'] = $file_path;
        return self::log('file_access', $action, $context);
    }

    public static function logDataChange($action, $user_id, $context = []) {
        $context['user_id'] = $user_id;
        return self::log('data_change', $action, $context);
    }

    public static function logSuspiciousActivity($activity_type, $user_id = null, $context = []) {
        $context['user_id'] = $user_id;
        self::log('suspicious_activity', $activity_type, $context);

        file_put_contents(
            self::getLogDir() . '/security_alerts.log',
            sprintf("[%s] ALERT: %s | %s\n", date('Y-m-d H:i:s'), $activity_type, json_encode($context, JSON_UNESCAPED_UNICODE)),
            FILE_APPEND | LOCK_EX
        );

        return true;
    }

    public static function checkSuspiciousActivity($ip_address, $activity = 'auth_failed') {
        $key = 'suspicious_' . md5($ip_address . '_' . $activity);
        $now = time();

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'first_attempt' => $now];
        }

        $_SESSION[$key]['count']++;

        if (($now - $_SESSION[$key]['first_attempt']) > 600) {
            $_SESSION[$key] = ['count' => 1, 'first_attempt' => $now];
        }

        if ($_SESSION[$key]['count'] >= 5) {
            self::logSuspiciousActivity('multiple_failed_attempts', null, [
                'ip_address' => $ip_address,
                'activity' => $activity,
                'count' => $_SESSION[$key]['count']
            ]);
            return true;
        }

        return false;
    }
}

/**
 * Audit Log (Denetim Kaydı) Sınıfı
 */
class AuditLogger {

    public static function log($action, $user_id = null, $details = null, $ip_address = null) {
        global $pdo;

        $ip_address = $ip_address ?: ($_SERVER['REMOTE_ADDR'] ?? 'unknown');

        if (isset($pdo)) {
            $stmt = $pdo->prepare('INSERT INTO audit_logs (kullanici_id, islem, detaylar, ip_adresi, islem_tarihi) VALUES (?, ?, ?, ?, NOW())');
            return $stmt->execute([$user_id, $action, $details, $ip_address]);
        }

        return SecurityLogger::log('audit', $action, [
            'user_id' => $user_id,
            'details' => $details,
            'ip_address' => $ip_address
        ]);
    }
}
?>
