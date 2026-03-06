<?php

class BackupManager {
    private PDO $pdo;
    private string $backupDirectory;

    public function __construct(PDO $pdo, string $backupDirectory = __DIR__ . '/storage/backups') {
        $this->pdo = $pdo;
        $this->backupDirectory = rtrim($backupDirectory, '/');

        if (!is_dir($this->backupDirectory)) {
            mkdir($this->backupDirectory, 0755, true);
        }
    }

    public function createBackup(string $type = 'manuel'): array {
        $type = in_array($type, ['gunluk', 'haftalik', 'aylik', 'manuel'], true) ? $type : 'manuel';
        $started = date('Y-m-d H:i:s');
        $fileName = sprintf('backup_%s_%s.sql', $type, date('Ymd_His'));
        $fullPath = $this->backupDirectory . '/' . $fileName;

        try {
            $dump = $this->buildSqlDump();
            file_put_contents($fullPath, $dump);

            $checksum = hash_file('sha256', $fullPath);
            $this->recordBackup($type, $fileName, $checksum, $started, date('Y-m-d H:i:s'), 'basarili', null, 'local', $fullPath);

            return ['success' => true, 'file' => $fullPath, 'checksum' => $checksum];
        } catch (Throwable $e) {
            $this->recordBackup($type, $fileName, str_repeat('0', 64), $started, date('Y-m-d H:i:s'), 'basarisiz', $e->getMessage(), 'local', null);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function restoreBackup(string $backupFilePath): bool {
        if (!file_exists($backupFilePath)) {
            throw new RuntimeException('Yedek dosyası bulunamadı.');
        }

        $sql = file_get_contents($backupFilePath);
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $this->pdo->exec($sql);
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        $stmt = $this->pdo->prepare("UPDATE backup_jobs SET status = 'geri_yuklendi' WHERE backup_file = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([basename($backupFilePath)]);

        return true;
    }

    public function uploadToCloud(string $backupFilePath, string $driver = 's3'): array {
        if (!in_array($driver, ['s3', 'gcs'], true)) {
            return ['success' => false, 'message' => 'Desteklenmeyen bulut sürücüsü.'];
        }

        return [
            'success' => true,
            'message' => 'Bulut yükleme hazır. Entegrasyon için erişim anahtarları tanımlanmalı.',
            'driver' => $driver,
            'file' => $backupFilePath,
        ];
    }

    private function buildSqlDump(): string {
        $tables = $this->pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        $output = "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\nSET FOREIGN_KEY_CHECKS=0;\n";

        foreach ($tables as $table) {
            $create = $this->pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
            $output .= "\nDROP TABLE IF EXISTS `{$table}`;\n" . $create['Create Table'] . ";\n";

            $rows = $this->pdo->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $values = array_map(function($value) {
                    return $value === null ? 'NULL' : $this->pdo->quote((string)$value);
                }, array_values($row));

                $columns = array_map(fn($column) => "`{$column}`", array_keys($row));
                $output .= sprintf(
                    "INSERT INTO `%s` (%s) VALUES (%s);\n",
                    $table,
                    implode(', ', $columns),
                    implode(', ', $values)
                );
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $output;
    }

    private function recordBackup(
        string $type,
        string $file,
        string $checksum,
        string $startedAt,
        string $completedAt,
        string $status,
        ?string $error,
        string $storageDriver,
        ?string $storagePath
    ): void {
        $stmt = $this->pdo->prepare("INSERT INTO backup_jobs
            (backup_type, backup_file, storage_driver, storage_path, checksum_sha256, status, started_at, completed_at, error_message)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([$type, $file, $storageDriver, $storagePath, $checksum, $status, $startedAt, $completedAt, $error]);
    }
}
