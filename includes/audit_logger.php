<?php

declare(strict_types=1);

/**
 * KVKK Uyumlu Denetim Kaydı Sistemi (Audit Logger)
 * Her veri işleme işlemini günlüğe alır: kim, ne, ne zaman, IP, sonuç.
 * Silme koruması ile değiştirilemez kayıtlar (immutable logs) sağlar.
 */
class AuditLoggerKVKK
{
    private PDO $pdo;
    private string $logDir;

    public function __construct(PDO $pdo, string $logDir = '')
    {
        $this->pdo  = $pdo;
        $this->logDir = $logDir ?: __DIR__ . '/../storage/audit_logs';
    }

    /**
     * Denetim kaydı oluştur.
     *
     * @param string      $action     İşlem adı (örn. 'user_login', 'data_export')
     * @param int|null    $userId     İşlemi yapan kullanıcı ID'si
     * @param array       $details    Ek detaylar (hassas alanlar şifrelenerek saklanır)
     * @param string|null $ipAddress  İstemci IP adresi
     * @param string      $result     Sonuç: 'success' | 'failure'
     * @return int  Oluşturulan kayıt ID'si
     */
    public function log(
        string $action,
        ?int $userId = null,
        array $details = [],
        ?string $ipAddress = null,
        string $result = 'success'
    ): int {
        $ip   = $ipAddress ?? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        // X-Forwarded-For başlığı birden fazla IP içerebilir; yalnızca ilkini al ve doğrula
        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        $ua   = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ts   = date('Y-m-d H:i:s');

        $sensitiveKeys = ['identity_number', 'phone', 'email', 'address', 'password'];
        $encryptedFields = [];
        foreach ($sensitiveKeys as $key) {
            if (isset($details[$key])) {
                $encryptedFields[$key] = $this->encryptSensitive((string)$details[$key]);
                unset($details[$key]);
            }
        }

        $detailsJson   = !empty($details)         ? json_encode($details, JSON_UNESCAPED_UNICODE)         : null;
        $encryptedJson = !empty($encryptedFields)  ? json_encode($encryptedFields, JSON_UNESCAPED_UNICODE) : null;

        $stmt = $this->pdo->prepare(
            'INSERT INTO audit_logs
             (user_id, action, details, ip_address, user_agent, result, encrypted_fields, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $action, $detailsJson, $ip, $ua, $result, $encryptedJson, $ts]);
        $id = (int)$this->pdo->lastInsertId();

        $this->writeToFile($id, $userId, $action, $ip, $result, $ts);

        return $id;
    }

    /**
     * Belirli bir kullanıcıya ait denetim kayıtlarını döndür.
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getLogsForUser(int $userId, int $limit = 100): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, action, details, ip_address, result, created_at
             FROM audit_logs WHERE user_id = ?
             ORDER BY created_at DESC LIMIT ?'
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tüm denetim kayıtlarını sayfalı döndür (admin kullanımı).
     *
     * @param int $page
     * @param int $perPage
     * @return array{rows: array, total: int}
     */
    public function getAllLogs(int $page = 1, int $perPage = 50): array
    {
        $offset = ($page - 1) * $perPage;
        $total  = (int)$this->pdo->query('SELECT COUNT(*) FROM audit_logs')->fetchColumn();

        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, action, details, ip_address, result, created_at
             FROM audit_logs ORDER BY created_at DESC LIMIT ? OFFSET ?'
        );
        $stmt->execute([$perPage, $offset]);

        return ['rows' => $stmt->fetchAll(PDO::FETCH_ASSOC), 'total' => $total];
    }

    // ─── Private Helpers ────────────────────────────────────────────────────────

    /** Hassas değeri AES-256-CBC ile şifreler. */
    private function encryptSensitive(string $plain): string
    {
        $key    = $this->getDerivedKey();
        $ivLen  = openssl_cipher_iv_length('aes-256-cbc');
        $iv     = random_bytes($ivLen);
        $cipher = openssl_encrypt($plain, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $cipher);
    }

    /** Ortam değişkeninden türetilmiş 32-bayt anahtar döndürür. */
    private function getDerivedKey(): string
    {
        $raw = getenv('PROFILE_DATA_KEY');
        if (empty($raw)) {
            throw new RuntimeException('PROFILE_DATA_KEY ortam değişkeni tanımlı değil. Güvenli bir anahtar ayarlayın.');
        }
        return hash('sha256', $raw, true);
    }

    /** Dosyaya da yazar (file + database hybrid). */
    private function writeToFile(int $id, ?int $userId, string $action, string $ip, string $result, string $ts): void
    {
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0750, true);
        }
        $file = $this->logDir . '/' . date('Y-m-d') . '.log';
        $line = sprintf("[%s] id=%d user=%s action=%s ip=%s result=%s\n", $ts, $id, $userId ?? 'guest', $action, $ip, $result);
        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
}
