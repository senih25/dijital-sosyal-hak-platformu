<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/encryption_manager.php';
require_once __DIR__ . '/../includes/audit_logger.php';

/**
 * GDPR/KVKK Uyumlu Veri Taşınabilirlik (Data Export) Modülü
 * - Kullanıcının tüm kişisel verilerini JSON formatında dışa aktarır
 * - Şifrelenmiş alanları çözümleyerek export eder
 * - İndirme bağlantısı oluşturur
 */
class DataExport
{
    private PDO               $pdo;
    private EncryptionManager $encryption;
    private AuditLoggerKVKK   $audit;
    private string            $exportDir;

    public function __construct(PDO $pdo, EncryptionManager $encryption, AuditLoggerKVKK $audit, string $exportDir = '')
    {
        $this->pdo        = $pdo;
        $this->encryption = $encryption;
        $this->audit      = $audit;
        $this->exportDir  = $exportDir ?: __DIR__ . '/../storage/exports';
    }

    // ─── Public API ─────────────────────────────────────────────────────────────

    /**
     * Kullanıcının tüm kişisel verilerini toplar ve JSON dosyası olarak kaydeder.
     *
     * @param int $userId
     * @return string  Oluşturulan dosyanın yolu
     */
    public function exportUserData(int $userId): string
    {
        $data = [
            'export_info' => [
                'generated_at'    => date('c'),
                'format'          => 'GDPR-compatible JSON',
                'platform'        => 'Dijital Sosyal Hak Platformu',
            ],
            'profile'       => $this->fetchProfile($userId),
            'health'        => $this->fetchHealth($userId),
            'calculations'  => $this->fetchCalculations($userId),
            'rights_history'=> $this->fetchRightsHistory($userId),
            'consents'      => $this->fetchConsents($userId),
        ];

        if (!is_dir($this->exportDir)) {
            mkdir($this->exportDir, 0750, true);
        }

        $filename = 'export_user_' . $userId . '_' . time() . '.json';
        $filePath = $this->exportDir . '/' . $filename;
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->audit->log('data_export_created', $userId, ['file' => $filename]);
        return $filePath;
    }

    /**
     * Export dosyasını indirme için tarayıcıya gönderir (output buffering olmadan çağrılmalı).
     *
     * @param string $filePath  exportUserData() çıktısı
     */
    public function streamDownload(string $filePath): void
    {
        if (!file_exists($filePath)) {
            http_response_code(404);
            exit('Dosya bulunamadı.');
        }
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    // ─── Data Collectors ────────────────────────────────────────────────────────

    private function fetchProfile(int $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user_profiles WHERE user_id = ?');
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private function fetchHealth(int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT uhr.* FROM user_health_records uhr
             JOIN user_profiles up ON up.id = uhr.profile_id
             WHERE up.user_id = ?'
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        // Şifrelenmiş alanları çöz
        foreach (['chronic_conditions_encrypted', 'medications_encrypted'] as $field) {
            if (!empty($row[$field])) {
                try {
                    $row[$field . '_plain'] = $this->encryption->decrypt($row[$field]);
                } catch (Throwable $e) {
                    $row[$field . '_plain'] = null;
                }
            }
        }
        return $row;
    }

    private function fetchCalculations(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT cr.* FROM calculation_results cr
             JOIN user_profiles up ON up.id = cr.profile_id
             WHERE up.user_id = ?'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function fetchRightsHistory(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT srh.* FROM social_rights_history srh
             JOIN user_profiles up ON up.id = srh.profile_id
             WHERE up.user_id = ?'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function fetchConsents(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, consent_type, given_at, revoked_at, ip_address
             FROM consent_records WHERE user_id = ? ORDER BY given_at ASC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
