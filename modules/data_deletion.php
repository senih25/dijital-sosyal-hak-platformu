<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/audit_logger.php';

/**
 * Veri Silme Otomasyon Modülü
 * - Kullanıcı silme talebi oluşturma
 * - 30 günlük oto-silme işleme
 * - Cascade deletion (ilişkili veriler)
 * - Soft delete (arşiv) veya hard delete seçeneği
 * - Silme doğrulama ve kanıt (audit trail)
 */
class DataDeletion
{
    private PDO $pdo;
    private AuditLoggerKVKK $audit;

    public function __construct(PDO $pdo, AuditLoggerKVKK $audit)
    {
        $this->pdo   = $pdo;
        $this->audit = $audit;
    }

    // ─── Public API ─────────────────────────────────────────────────────────────

    /**
     * Kullanıcı adına silme talebi oluştur.
     *
     * @param int    $userId
     * @param string $reason  Silme nedeni
     * @return int  Oluşturulan talep ID'si
     */
    public function createRequest(int $userId, string $reason = ''): int
    {
        // Aynı kullanıcı için bekleyen talep var mı?
        $existing = $this->findPendingRequest($userId);
        if ($existing !== null) {
            return (int)$existing['id'];
        }

        $hash = bin2hex(random_bytes(16));
        $now  = date('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'INSERT INTO deletion_requests (user_id, reason, request_date, status, verification_hash)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $reason, $now, 'pending', $hash]);
        $id = (int)$this->pdo->lastInsertId();

        $this->audit->log('deletion_request_created', $userId, ['request_id' => $id, 'reason' => $reason]);
        return $id;
    }

    /**
     * Talebi onayla (admin tarafından).
     *
     * @param int    $requestId
     * @param bool   $hardDelete  true → kalıcı sil, false → soft delete (arşiv)
     * @param int    $adminId
     * @return bool
     */
    public function approveRequest(int $requestId, bool $hardDelete = false, int $adminId = 0): bool
    {
        $request = $this->getRequest($requestId);
        if ($request === null || $request['status'] !== 'pending') {
            return false;
        }

        $userId = (int)$request['user_id'];
        if ($hardDelete) {
            $this->hardDelete($userId);
        } else {
            $this->softDelete($userId);
        }

        $now  = date('Y-m-d H:i:s');
        $this->pdo->prepare(
            'UPDATE deletion_requests SET status = ?, processed_date = ? WHERE id = ?'
        )->execute(['approved', $now, $requestId]);

        $this->audit->log('deletion_request_approved', $adminId, [
            'request_id'  => $requestId,
            'target_user' => $userId,
            'hard_delete' => $hardDelete,
        ]);
        return true;
    }

    /**
     * Talebi reddet (admin tarafından).
     */
    public function rejectRequest(int $requestId, int $adminId = 0): bool
    {
        $request = $this->getRequest($requestId);
        if ($request === null || $request['status'] !== 'pending') {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $this->pdo->prepare(
            'UPDATE deletion_requests SET status = ?, processed_date = ? WHERE id = ?'
        )->execute(['rejected', $now, $requestId]);

        $this->audit->log('deletion_request_rejected', $adminId, ['request_id' => $requestId]);
        return true;
    }

    /**
     * 30 günü aşmış onaylanmamış (pending) talepleri otomatik işle.
     * Cron job tarafından çağrılmalıdır.
     *
     * @return int  İşlenen talep sayısı
     */
    public function processExpiredRequests(): int
    {
        $cutoff = date('Y-m-d H:i:s', strtotime('-30 days'));
        $stmt   = $this->pdo->prepare(
            'SELECT * FROM deletion_requests WHERE status = ? AND request_date <= ?'
        );
        $stmt->execute(['pending', $cutoff]);
        $rows  = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = 0;

        foreach ($rows as $row) {
            $this->approveRequest((int)$row['id'], true, 0);
            $count++;
        }
        return $count;
    }

    /**
     * Tüm silme taleplerini döndür (admin paneli için).
     *
     * @return array{rows: array, total: int}
     */
    public function getAllRequests(int $page = 1, int $perPage = 50): array
    {
        $offset = ($page - 1) * $perPage;
        $total  = (int)$this->pdo->query('SELECT COUNT(*) FROM deletion_requests')->fetchColumn();
        $stmt   = $this->pdo->prepare(
            'SELECT * FROM deletion_requests ORDER BY request_date DESC LIMIT ? OFFSET ?'
        );
        $stmt->execute([$perPage, $offset]);
        return ['rows' => $stmt->fetchAll(PDO::FETCH_ASSOC), 'total' => $total];
    }

    // ─── Private Helpers ────────────────────────────────────────────────────────

    private function findPendingRequest(int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM deletion_requests WHERE user_id = ? AND status = ? LIMIT 1'
        );
        $stmt->execute([$userId, 'pending']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private function getRequest(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM deletion_requests WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Kullanıcıya ait tüm kayıtları kalıcı sil (cascade).
     * user_profiles tablosunda ON DELETE CASCADE olduğundan ilişkili tablolar otomatik silinir.
     */
    private function hardDelete(int $userId): void
    {
        // Önce profil bul
        $profStmt = $this->pdo->prepare('SELECT id FROM user_profiles WHERE user_id = ?');
        $profStmt->execute([$userId]);
        $profileId = $profStmt->fetchColumn();

        if ($profileId) {
            $this->pdo->prepare('DELETE FROM user_profiles WHERE id = ?')->execute([$profileId]);
        }

        // Rıza ve silme talepleri kayıtlarını da temizle (soft references)
        $this->pdo->prepare('DELETE FROM consent_records WHERE user_id = ?')->execute([$userId]);
    }

    /**
     * Yumuşak silme: profil ve sağlık verilerini anonimleştir (arşiv).
     */
    private function softDelete(int $userId): void
    {
        $anon = 'DELETED_' . $userId;
        $this->pdo->prepare(
            "UPDATE user_profiles
             SET first_name = ?, last_name = ?, phone = NULL, email = NULL,
                 identity_number_hash = NULL, address = NULL, birth_date = NULL,
                 kvkk_explicit_consent = 0
             WHERE user_id = ?"
        )->execute([$anon, $anon, $userId]);
    }
}
