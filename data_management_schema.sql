-- Veri Yönetimi ve Analitik Modülü
-- KULLANICI PROFİL SİSTEMİ + RAPORLAMA + YEDEKLEME

CREATE TABLE IF NOT EXISTS user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    identity_number_hash CHAR(64) NULL,
    phone VARCHAR(20) NULL,
    email VARCHAR(190) NULL,
    birth_date DATE NULL,
    gender ENUM('kadin', 'erkek', 'diger', 'belirtmek_istemiyorum') DEFAULT 'belirtmek_istemiyorum',
    city VARCHAR(120) NULL,
    district VARCHAR(120) NULL,
    address TEXT NULL,
    emergency_contact_name VARCHAR(190) NULL,
    emergency_contact_phone VARCHAR(20) NULL,
    kvkk_explicit_consent TINYINT(1) DEFAULT 0,
    kvkk_consent_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_profile_city (city),
    INDEX idx_profile_updated (updated_at)
);

CREATE TABLE IF NOT EXISTS user_health_records (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    disability_rate DECIMAL(5,2) NULL,
    disability_type VARCHAR(255) NULL,
    chronic_conditions_encrypted TEXT NULL,
    medications_encrypted TEXT NULL,
    report_reference VARCHAR(120) NULL,
    valid_until DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_health_profile FOREIGN KEY (profile_id) REFERENCES user_profiles(id) ON DELETE CASCADE,
    INDEX idx_health_disability_rate (disability_rate)
);

CREATE TABLE IF NOT EXISTS social_rights_history (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    right_code VARCHAR(120) NOT NULL,
    right_name VARCHAR(255) NOT NULL,
    application_date DATE NULL,
    status ENUM('beklemede', 'onaylandi', 'reddedildi', 'tamamlandi') DEFAULT 'beklemede',
    institution_name VARCHAR(255) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rights_profile FOREIGN KEY (profile_id) REFERENCES user_profiles(id) ON DELETE CASCADE,
    INDEX idx_rights_status (status),
    INDEX idx_rights_code (right_code)
);

CREATE TABLE IF NOT EXISTS calculation_results (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    calculator_type VARCHAR(120) NOT NULL,
    input_payload JSON NOT NULL,
    result_payload JSON NOT NULL,
    score DECIMAL(10,2) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_calc_profile FOREIGN KEY (profile_id) REFERENCES user_profiles(id) ON DELETE CASCADE,
    INDEX idx_calc_type (calculator_type),
    INDEX idx_calc_created (created_at)
);

CREATE TABLE IF NOT EXISTS analytics_daily (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    metric_date DATE NOT NULL,
    total_users INT DEFAULT 0,
    active_users INT DEFAULT 0,
    popular_service VARCHAR(255) NULL,
    total_calculations INT DEFAULT 0,
    approved_rights INT DEFAULT 0,
    rejected_rights INT DEFAULT 0,
    total_revenue DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_metric_date (metric_date)
);

CREATE TABLE IF NOT EXISTS backup_jobs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    backup_type ENUM('gunluk', 'haftalik', 'aylik', 'manuel') NOT NULL,
    backup_file VARCHAR(255) NOT NULL,
    storage_driver ENUM('local', 's3', 'gcs') DEFAULT 'local',
    storage_path VARCHAR(255) NULL,
    checksum_sha256 CHAR(64) NOT NULL,
    status ENUM('basarili', 'basarisiz', 'geri_yuklendi') DEFAULT 'basarili',
    started_at DATETIME NOT NULL,
    completed_at DATETIME NULL,
    restore_point_label VARCHAR(255) NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_backup_type (backup_type),
    INDEX idx_backup_status (status),
    INDEX idx_backup_started (started_at)
);

-- ─── KVKK UYUM TABLOLARI ────────────────────────────────────────────────────

-- Denetim Kaydı (immutable: satır silme/güncelleme trigger ile korunabilir)
CREATE TABLE IF NOT EXISTS audit_logs (
    id              BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NULL,
    action          VARCHAR(120)  NOT NULL,
    details         TEXT          NULL,
    ip_address      VARCHAR(45)   NOT NULL DEFAULT '',
    user_agent      VARCHAR(500)  NOT NULL DEFAULT '',
    result          ENUM('success','failure') NOT NULL DEFAULT 'success',
    encrypted_fields TEXT         NULL,
    created_at      DATETIME      NOT NULL,
    INDEX idx_audit_user   (user_id),
    INDEX idx_audit_action (action),
    INDEX idx_audit_ts     (created_at)
);

-- Şifreleme Anahtarları (anahtar rotasyonu)
CREATE TABLE IF NOT EXISTS encryption_keys (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    algorithm    VARCHAR(50)  NOT NULL DEFAULT 'aes-256-cbc',
    key_material VARCHAR(255) NOT NULL,
    status       ENUM('active','rotated','revoked') NOT NULL DEFAULT 'active',
    created_at   DATETIME     NOT NULL,
    rotated_at   DATETIME     NULL,
    INDEX idx_enckey_status (status)
);

-- Rıza Kayıtları (KVKK Md. 3/5)
CREATE TABLE IF NOT EXISTS consent_records (
    id           BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT          NOT NULL,
    consent_type ENUM('essential','analytics','marketing') NOT NULL,
    given_at     DATETIME     NOT NULL,
    revoked_at   DATETIME     NULL,
    ip_address   VARCHAR(45)  NOT NULL DEFAULT '',
    user_agent   VARCHAR(500) NOT NULL DEFAULT '',
    INDEX idx_consent_user   (user_id),
    INDEX idx_consent_type   (consent_type),
    INDEX idx_consent_given  (given_at)
);

-- Veri Silme Talepleri (KVKK Md. 7/11)
CREATE TABLE IF NOT EXISTS deletion_requests (
    id                BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id           INT          NOT NULL,
    reason            TEXT         NULL,
    request_date      DATETIME     NOT NULL,
    processed_date    DATETIME     NULL,
    status            ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    verification_hash VARCHAR(64)  NOT NULL,
    INDEX idx_del_user   (user_id),
    INDEX idx_del_status (status),
    INDEX idx_del_date   (request_date)
);
