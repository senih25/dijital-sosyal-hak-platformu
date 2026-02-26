<?php
require_once __DIR__ . '/security.php';

/**
 * KVKK uyumlu kullanıcı profil sistemi.
 */
class UserProfileSystem {
    private PDO $pdo;
    private string $encryptionKey;

    public function __construct(PDO $pdo, ?string $encryptionKey = null) {
        $this->pdo = $pdo;
        $this->encryptionKey = $encryptionKey ?: (getenv('PROFILE_DATA_KEY') ?: 'change-this-key');
    }

    public function saveProfile(int $userId, array $profile): int {
        $firstName = SecurityManager::sanitizeInput($profile['first_name'] ?? '');
        $lastName = SecurityManager::sanitizeInput($profile['last_name'] ?? '');

        if ($firstName === '' || $lastName === '') {
            throw new InvalidArgumentException('Ad ve soyad zorunludur.');
        }

        $sql = "INSERT INTO user_profiles
            (user_id, first_name, last_name, identity_number_hash, phone, email, birth_date, gender, city, district, address,
             emergency_contact_name, emergency_contact_phone, kvkk_explicit_consent, kvkk_consent_at)
            VALUES
            (:user_id, :first_name, :last_name, :identity_hash, :phone, :email, :birth_date, :gender, :city, :district, :address,
             :emergency_name, :emergency_phone, :consent, :consent_at)
            ON DUPLICATE KEY UPDATE
             first_name = VALUES(first_name),
             last_name = VALUES(last_name),
             identity_number_hash = VALUES(identity_number_hash),
             phone = VALUES(phone),
             email = VALUES(email),
             birth_date = VALUES(birth_date),
             gender = VALUES(gender),
             city = VALUES(city),
             district = VALUES(district),
             address = VALUES(address),
             emergency_contact_name = VALUES(emergency_contact_name),
             emergency_contact_phone = VALUES(emergency_contact_phone),
             kvkk_explicit_consent = VALUES(kvkk_explicit_consent),
             kvkk_consent_at = VALUES(kvkk_consent_at)";

        $consent = (int)($profile['kvkk_explicit_consent'] ?? 0);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'identity_hash' => !empty($profile['identity_number']) ? hash('sha256', $profile['identity_number']) : null,
            'phone' => SecurityManager::sanitizeInput($profile['phone'] ?? ''),
            'email' => SecurityManager::sanitizeInput($profile['email'] ?? ''),
            'birth_date' => $profile['birth_date'] ?? null,
            'gender' => $profile['gender'] ?? 'belirtmek_istemiyorum',
            'city' => SecurityManager::sanitizeInput($profile['city'] ?? ''),
            'district' => SecurityManager::sanitizeInput($profile['district'] ?? ''),
            'address' => SecurityManager::sanitizeInput($profile['address'] ?? ''),
            'emergency_name' => SecurityManager::sanitizeInput($profile['emergency_contact_name'] ?? ''),
            'emergency_phone' => SecurityManager::sanitizeInput($profile['emergency_contact_phone'] ?? ''),
            'consent' => $consent,
            'consent_at' => $consent ? date('Y-m-d H:i:s') : null,
        ]);

        $profileId = (int)$this->pdo->lastInsertId();
        if ($profileId === 0) {
            $profileId = (int)$this->pdo->query("SELECT id FROM user_profiles WHERE user_id = " . (int)$userId)->fetchColumn();
        }

        AuditLogger::log('kullanici_profili_guncellendi', $userId, json_encode(['profile_id' => $profileId]));
        return $profileId;
    }

    public function saveHealthRecord(int $profileId, array $healthData): bool {
        $sql = "INSERT INTO user_health_records
            (profile_id, disability_rate, disability_type, chronic_conditions_encrypted, medications_encrypted, report_reference, valid_until)
            VALUES (:profile_id, :disability_rate, :disability_type, :chronic, :medications, :report_reference, :valid_until)
            ON DUPLICATE KEY UPDATE
            disability_rate = VALUES(disability_rate),
            disability_type = VALUES(disability_type),
            chronic_conditions_encrypted = VALUES(chronic_conditions_encrypted),
            medications_encrypted = VALUES(medications_encrypted),
            report_reference = VALUES(report_reference),
            valid_until = VALUES(valid_until)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'profile_id' => $profileId,
            'disability_rate' => $healthData['disability_rate'] ?? null,
            'disability_type' => SecurityManager::sanitizeInput($healthData['disability_type'] ?? ''),
            'chronic' => $this->encryptField($healthData['chronic_conditions'] ?? ''),
            'medications' => $this->encryptField($healthData['medications'] ?? ''),
            'report_reference' => SecurityManager::sanitizeInput($healthData['report_reference'] ?? ''),
            'valid_until' => $healthData['valid_until'] ?? null,
        ]);
    }

    public function saveCalculationResult(int $profileId, string $calculatorType, array $input, array $result, ?float $score = null): bool {
        $stmt = $this->pdo->prepare("INSERT INTO calculation_results
            (profile_id, calculator_type, input_payload, result_payload, score)
            VALUES (:profile_id, :calculator_type, :input_payload, :result_payload, :score)");

        return $stmt->execute([
            'profile_id' => $profileId,
            'calculator_type' => SecurityManager::sanitizeInput($calculatorType),
            'input_payload' => json_encode($input, JSON_UNESCAPED_UNICODE),
            'result_payload' => json_encode($result, JSON_UNESCAPED_UNICODE),
            'score' => $score,
        ]);
    }

    public function getProfileSummary(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        if (!$profile) {
            return [];
        }

        $healthStmt = $this->pdo->prepare("SELECT * FROM user_health_records WHERE profile_id = ? ORDER BY updated_at DESC LIMIT 1");
        $healthStmt->execute([(int)$profile['id']]);
        $health = $healthStmt->fetch(PDO::FETCH_ASSOC) ?: [];

        if ($health) {
            $health['chronic_conditions'] = $this->decryptField($health['chronic_conditions_encrypted'] ?? null);
            $health['medications'] = $this->decryptField($health['medications_encrypted'] ?? null);
            unset($health['chronic_conditions_encrypted'], $health['medications_encrypted']);
        }

        return [
            'profile' => $profile,
            'health' => $health,
            'rights_count' => $this->countRights((int)$profile['id']),
            'calculation_count' => $this->countCalculations((int)$profile['id']),
        ];
    }

    private function countRights(int $profileId): int {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM social_rights_history WHERE profile_id = ?');
        $stmt->execute([$profileId]);
        return (int)$stmt->fetchColumn();
    }

    private function countCalculations(int $profileId): int {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM calculation_results WHERE profile_id = ?');
        $stmt->execute([$profileId]);
        return (int)$stmt->fetchColumn();
    }

    private function encryptField(string $plainText): ?string {
        if ($plainText === '') {
            return null;
        }

        $cipher = 'aes-256-cbc';
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = random_bytes($ivLength);
        $encrypted = openssl_encrypt($plainText, $cipher, hash('sha256', $this->encryptionKey, true), OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv . $encrypted);
    }

    private function decryptField(?string $payload): ?string {
        if (empty($payload)) {
            return null;
        }

        $cipher = 'aes-256-cbc';
        $raw = base64_decode($payload, true);
        if ($raw === false) {
            return null;
        }

        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($raw, 0, $ivLength);
        $encrypted = substr($raw, $ivLength);

        return openssl_decrypt($encrypted, $cipher, hash('sha256', $this->encryptionKey, true), OPENSSL_RAW_DATA, $iv) ?: null;
    }
}
