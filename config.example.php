<?php
define("DB_HOST", "localhost");
define("DB_NAME", "database_name");
define("DB_USER", "username");
define("DB_PASS", "password");

// KVKK hassas alan şifreleme anahtarı (üretim ortamında env'den alınması önerilir)
define("PROFILE_DATA_KEY", "change-this-secret-key");

// Yedekleme klasörü (opsiyonel)
define("BACKUP_STORAGE_PATH", __DIR__ . '/storage/backups');
