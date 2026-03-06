<?php

declare(strict_types=1);

/**
 * Şifreleme Anahtarı Rotasyon Yöneticisi
 * - 90 günlük otomatik anahtar rotasyonu
 * - AES-256-CBC şifreleme / çözme
 * - Eski anahtarlarla şifrelenmiş verileri çözebilme
 * - Yeni veri için her zaman en güncel anahtarı kullanır
 */
class EncryptionManager
{
    private const CIPHER         = 'aes-256-cbc';
    private const ROTATION_DAYS  = 90;

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ─── Public API ─────────────────────────────────────────────────────────────

    /**
     * Veriyi aktif anahtar ile şifrele.
     * Dönen string: base64( key_id(4B) | iv | ciphertext )
     */
    public function encrypt(string $plaintext): string
    {
        $keyRow = $this->getActiveKey();
        $key    = $this->deriveKey($keyRow['key_material']);
        $ivLen  = openssl_cipher_iv_length(self::CIPHER);
        $iv     = random_bytes($ivLen);
        $cipher = openssl_encrypt($plaintext, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);

        if ($cipher === false) {
            throw new RuntimeException('Şifreleme başarısız.');
        }

        // 4-byte big-endian key_id prefix
        $prefix = pack('N', (int)$keyRow['id']);
        return base64_encode($prefix . $iv . $cipher);
    }

    /**
     * Şifreli veriyi çöz (hangi anahtar ile şifrelendiğini otomatik algılar).
     */
    public function decrypt(string $payload): string
    {
        $raw   = base64_decode($payload, true);
        if ($raw === false || strlen($raw) < 20) {
            throw new InvalidArgumentException('Geçersiz şifreli veri.');
        }

        $keyId  = unpack('N', substr($raw, 0, 4))[1];
        $ivLen  = openssl_cipher_iv_length(self::CIPHER);
        $iv     = substr($raw, 4, $ivLen);
        $cipher = substr($raw, 4 + $ivLen);

        $keyRow = $this->getKeyById((int)$keyId);
        $key    = $this->deriveKey($keyRow['key_material']);

        $plain  = openssl_decrypt($cipher, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
        if ($plain === false) {
            throw new RuntimeException('Şifre çözme başarısız (keyId=' . $keyId . ').');
        }
        return $plain;
    }

    /**
     * Gerekirse yeni anahtar oluştur (90 günü geçen aktif anahtar varsa rotasyon yap).
     * Cron job tarafından çağrılabilir.
     *
     * @return bool  Yeni anahtar oluşturulduysa true
     */
    public function rotateIfNeeded(): bool
    {
        $active = $this->findActiveKey();
        if ($active !== null) {
            $age = (int)((time() - strtotime($active['created_at'])) / 86400);
            if ($age < self::ROTATION_DAYS) {
                return false;
            }
            // Eskiyi devre dışı bırak
            $this->pdo->prepare('UPDATE encryption_keys SET status = ? WHERE id = ?')
                      ->execute(['rotated', $active['id']]);
        }
        $this->createKey();
        return true;
    }

    /**
     * Mevcut aktif anahtar bilgisini döndürür (key_material içermez).
     */
    public function getActiveKeyInfo(): array
    {
        $row = $this->findActiveKey();
        if ($row === null) {
            $row = $this->createKey();
        }
        return ['id' => $row['id'], 'algorithm' => $row['algorithm'], 'created_at' => $row['created_at']];
    }

    // ─── Private Helpers ────────────────────────────────────────────────────────

    private function getActiveKey(): array
    {
        $row = $this->findActiveKey();
        if ($row === null) {
            $row = $this->createKey();
        }
        return $row;
    }

    private function findActiveKey(): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM encryption_keys WHERE status = ? ORDER BY created_at DESC LIMIT 1'
        );
        $stmt->execute(['active']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private function getKeyById(int $id): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM encryption_keys WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new RuntimeException('Anahtar bulunamadı (id=' . $id . ').');
        }
        return $row;
    }

    private function createKey(): array
    {
        $material = bin2hex(random_bytes(32)); // 256-bit key material
        $now      = date('Y-m-d H:i:s');
        $stmt     = $this->pdo->prepare(
            'INSERT INTO encryption_keys (algorithm, key_material, status, created_at) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([self::CIPHER, $material, 'active', $now]);
        $id = (int)$this->pdo->lastInsertId();
        return ['id' => $id, 'algorithm' => self::CIPHER, 'key_material' => $material, 'created_at' => $now, 'status' => 'active'];
    }

    /** key_material'den 32-byte AES anahtarı türetir. */
    private function deriveKey(string $material): string
    {
        return hash('sha256', $material, true);
    }
}
