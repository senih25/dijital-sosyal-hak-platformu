<?php

declare(strict_types=1);

/**
 * Yedekleme otomasyon sistemi.
 *
 * Özellikler:
 * - Günlük veri yedekleme (JSON export)
 * - Dosya yedekleme (klasör kopyalama)
 * - Bulut depolama senkronizasyonu (hedef dizine kopya)
 * - Geri yükleme sistemi
 */
class BackupAutomationSystem
{
    private string $backupRoot;
    private string $backupRootRealPath;

    public function __construct(string $backupRoot)
    {
        $this->backupRoot = rtrim($backupRoot, '/');
        if (!is_dir($this->backupRoot)) {
            mkdir($this->backupRoot, 0775, true);
        }
        $realPath = realpath($this->backupRoot);
        $this->backupRootRealPath = $realPath !== false ? $realPath : $this->backupRoot;
    }

    public function runDailyDataBackup(array $data, string $name = 'data'): string
    {
        $dateDir = $this->getDateDirectory();
        $file = $dateDir . '/' . $name . '_backup_' . date('Ymd_His') . '.json';
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $file;
    }

    public function runFileBackup(string $sourcePath, string $label = 'files'): string
    {
        if (!is_dir($sourcePath)) {
            throw new InvalidArgumentException('Kaynak klasör bulunamadı: ' . $sourcePath);
        }

        $target = $this->getDateDirectory() . '/' . $label . '_backup_' . date('Ymd_His');
        $this->copyDirectory($sourcePath, $target, [$this->backupRootRealPath]);
        return $target;
    }

    public function syncToCloudStorage(string $backupPath, string $cloudPath): string
    {
        if (!file_exists($backupPath)) {
            throw new InvalidArgumentException('Senkronize edilecek yol bulunamadı: ' . $backupPath);
        }

        if (!is_dir($cloudPath)) {
            mkdir($cloudPath, 0775, true);
        }

        $target = rtrim($cloudPath, '/') . '/' . basename($backupPath);

        if (is_dir($backupPath)) {
            $this->copyDirectory($backupPath, $target);
        } else {
            copy($backupPath, $target);
        }

        return $target;
    }

    public function restoreBackup(string $backupPath, string $restorePath): string
    {
        if (!file_exists($backupPath)) {
            throw new InvalidArgumentException('Geri yüklenecek yedek bulunamadı: ' . $backupPath);
        }

        if (is_dir($backupPath)) {
            $this->copyDirectory($backupPath, $restorePath);
        } else {
            if (!is_dir(dirname($restorePath))) {
                mkdir(dirname($restorePath), 0775, true);
            }
            copy($backupPath, $restorePath);
        }

        return $restorePath;
    }

    private function getDateDirectory(): string
    {
        $dateDir = $this->backupRoot . '/' . date('Y-m-d');
        if (!is_dir($dateDir)) {
            mkdir($dateDir, 0775, true);
        }
        return $dateDir;
    }

    private function copyDirectory(string $source, string $target, array $excludePaths = []): void
    {
        $sourceRealPath = realpath($source) ?: $source;

        foreach ($excludePaths as $excludePath) {
            $excludeRealPath = realpath($excludePath) ?: $excludePath;
            if (str_starts_with($sourceRealPath, $excludeRealPath)) {
                return;
            }
        }

        if (!is_dir($target)) {
            mkdir($target, 0775, true);
        }

        $items = scandir($source) ?: [];

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $sourceItem = $source . '/' . $item;
            $targetItem = $target . '/' . $item;

            if (is_dir($sourceItem)) {
                $this->copyDirectory($sourceItem, $targetItem, $excludePaths);
            } else {
                copy($sourceItem, $targetItem);
            }
        }
    }
}

// Örnek CLI kullanımı
if (PHP_SAPI === 'cli' && basename((string)($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    $basePath = __DIR__;
    $system = new BackupAutomationSystem($basePath . '/storage/backups');

    $sampleData = [
        'users' => [
            ['id' => 1, 'name' => 'Ayşe', 'email' => 'ayse@example.com'],
            ['id' => 2, 'name' => 'Mehmet', 'email' => 'mehmet@example.com'],
        ],
        'generated_at' => date(DATE_ATOM),
    ];

    $dataBackup = $system->runDailyDataBackup($sampleData, 'users');
    echo "Veri yedeği oluşturuldu: {$dataBackup}\n";

    $sampleSource = $basePath . '/samples';
    if (!is_dir($sampleSource)) {
        mkdir($sampleSource, 0775, true);
    }
    file_put_contents($sampleSource . '/example.txt', 'Örnek dosya yedeği');

    $fileBackup = $system->runFileBackup($sampleSource, 'automation_source');
    echo "Dosya yedeği oluşturuldu: {$fileBackup}\n";

    $cloudTarget = $system->syncToCloudStorage($dataBackup, $basePath . '/storage/cloud-sync');
    echo "Bulut senkronizasyonu tamamlandı: {$cloudTarget}\n";

    $restoreTarget = $system->restoreBackup($dataBackup, $basePath . '/storage/restore/users_restore.json');
    echo "Geri yükleme tamamlandı: {$restoreTarget}\n";
}
