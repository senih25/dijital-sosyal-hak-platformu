<?php

declare(strict_types=1);

/**
 * KVKK/GDPR Uyumlu Rıza Yönetim Sistemi (Consent Manager)
 * - Açık rıza kaydı ve geçmişi
 * - Rıza tipleri: marketing, analytics, essential
 * - Rıza iptal işlemleri ve günlüğü
 * - KVKK/GDPR uyumlu consent state machine
 */
class ConsentManager
{
    // Geçerli rıza tipleri
    public const TYPE_ESSENTIAL  = 'essential';
    public const TYPE_ANALYTICS  = 'analytics';
    public const TYPE_MARKETING  = 'marketing';

    public const VALID_TYPES = [self::TYPE_ESSENTIAL, self::TYPE_ANALYTICS, self::TYPE_MARKETING];

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ─── Public API ─────────────────────────────────────────────────────────────

    /**
     * Kullanıcıdan rıza al ve kaydet.
     *
     * @param int    $userId
     * @param string $consentType  'essential' | 'analytics' | 'marketing'
     * @param string $ipAddress
     * @param string $userAgent
     * @return int  Oluşturulan consent kaydı ID'si
     */
    public function giveConsent(int $userId, string $consentType, string $ipAddress = '', string $userAgent = ''): int
    {
        $this->assertValidType($consentType);

        // essential tipi geri alınamaz ama tekrar kaydedilebilir (idempotent)
        $existing = $this->findActive($userId, $consentType);
        if ($existing !== null) {
            return (int)$existing['id'];
        }

        $now  = date('Y-m-d H:i:s');
        $ip   = $ipAddress ?: ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        // X-Forwarded-For başlığı birden fazla IP içerebilir; yalnızca ilkini al ve doğrula
        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        $ua   = $userAgent ?: ($_SERVER['HTTP_USER_AGENT'] ?? '');

        $stmt = $this->pdo->prepare(
            'INSERT INTO consent_records (user_id, consent_type, given_at, ip_address, user_agent)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $consentType, $now, $ip, $ua]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Kullanıcı rızasını geri al.
     *
     * @param int    $userId
     * @param string $consentType
     * @return bool  Başarılıysa true
     * @throws LogicException  essential tipini iptal etmeye çalışırsa
     */
    public function revokeConsent(int $userId, string $consentType): bool
    {
        $this->assertValidType($consentType);

        if ($consentType === self::TYPE_ESSENTIAL) {
            throw new LogicException('Zorunlu (essential) rıza geri alınamaz.');
        }

        $existing = $this->findActive($userId, $consentType);
        if ($existing === null) {
            return false; // Zaten aktif rıza yok
        }

        $now  = date('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare(
            'UPDATE consent_records SET revoked_at = ? WHERE id = ? AND revoked_at IS NULL'
        );
        $stmt->execute([$now, $existing['id']]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Belirli bir rıza tipinin aktif olup olmadığını kontrol et.
     */
    public function hasConsent(int $userId, string $consentType): bool
    {
        $this->assertValidType($consentType);
        return $this->findActive($userId, $consentType) !== null;
    }

    /**
     * Kullanıcının tüm aktif rızalarını döndür.
     *
     * @return array<string, bool>  ['essential' => true, 'analytics' => false, ...]
     */
    public function getConsents(int $userId): array
    {
        $result = [];
        foreach (self::VALID_TYPES as $type) {
            $result[$type] = $this->hasConsent($userId, $type);
        }
        return $result;
    }

    /**
     * Kullanıcının tüm rıza geçmişini döndür.
     */
    public function getConsentHistory(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, consent_type, given_at, revoked_at, ip_address
             FROM consent_records WHERE user_id = ? ORDER BY given_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Admin: Tüm kullanıcıların rıza kayıtlarını sayfalı getir.
     *
     * @return array{rows: array, total: int}
     */
    public function getAllConsents(int $page = 1, int $perPage = 50): array
    {
        $offset = ($page - 1) * $perPage;
        $total  = (int)$this->pdo->query('SELECT COUNT(*) FROM consent_records')->fetchColumn();
        $stmt   = $this->pdo->prepare(
            'SELECT id, user_id, consent_type, given_at, revoked_at, ip_address
             FROM consent_records ORDER BY given_at DESC LIMIT ? OFFSET ?'
        );
        $stmt->execute([$perPage, $offset]);
        return ['rows' => $stmt->fetchAll(PDO::FETCH_ASSOC), 'total' => $total];
    }

    // ─── Private Helpers ────────────────────────────────────────────────────────

    private function findActive(int $userId, string $consentType): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM consent_records
             WHERE user_id = ? AND consent_type = ? AND revoked_at IS NULL
             ORDER BY given_at DESC LIMIT 1'
        );
        $stmt->execute([$userId, $consentType]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private function assertValidType(string $type): void
    {
        if (!in_array($type, self::VALID_TYPES, true)) {
            throw new InvalidArgumentException(
                'Geçersiz rıza tipi: ' . $type . '. Geçerli tipler: ' . implode(', ', self::VALID_TYPES)
            );
        }
    }
}
