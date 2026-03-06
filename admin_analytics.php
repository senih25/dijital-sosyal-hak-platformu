<?php
require_once __DIR__ . '/security.php';

if (!isset($pdo) || !$pdo instanceof PDO) {
    $configFile = __DIR__ . '/config.php';
    if (file_exists($configFile)) {
        require_once $configFile;
    } else {
        require_once __DIR__ . '/config.example.php';
    }

    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (Throwable $e) {
        http_response_code(500);
        die('Veritabanı bağlantısı kurulamadı: ' . htmlspecialchars($e->getMessage()));
    }
}

$summary = [
    'total_users' => (int)$pdo->query('SELECT COUNT(*) FROM user_profiles')->fetchColumn(),
    'active_users' => (int)$pdo->query("SELECT COUNT(*) FROM user_profiles WHERE updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn(),
    'total_calculations' => (int)$pdo->query('SELECT COUNT(*) FROM calculation_results')->fetchColumn(),
    'total_revenue' => (float)$pdo->query('SELECT COALESCE(SUM(score),0) FROM calculation_results')->fetchColumn(),
];

$popularServices = $pdo->query("SELECT calculator_type, COUNT(*) as total
    FROM calculation_results
    GROUP BY calculator_type
    ORDER BY total DESC
    LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$rightsStatus = $pdo->query("SELECT status, COUNT(*) as total
    FROM social_rights_history
    GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);

$dailyCalculations = $pdo->query("SELECT DATE(created_at) as gun, COUNT(*) as total
    FROM calculation_results
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
    GROUP BY DATE(created_at)
    ORDER BY gun ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Raporlama ve İstatistik</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; background: #f5f7fb; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap: 16px; margin-bottom: 24px; }
        .card { background: #fff; padding: 18px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; }
        th, td { padding: 10px; border-bottom: 1px solid #eceff4; text-align: left; }
        canvas { background: #fff; padding: 16px; border-radius: 12px; margin-top: 16px; }
    </style>
</head>
<body>
<h1>Admin Raporlama Paneli</h1>
<div class="grid">
    <div class="card"><strong>Toplam Kullanıcı:</strong><br><?= number_format($summary['total_users']) ?></div>
    <div class="card"><strong>30 Gün Aktif:</strong><br><?= number_format($summary['active_users']) ?></div>
    <div class="card"><strong>Toplam Hesaplama:</strong><br><?= number_format($summary['total_calculations']) ?></div>
    <div class="card"><strong>Gelir Skoru Toplamı:</strong><br><?= number_format($summary['total_revenue'], 2, ',', '.') ?> ₺</div>
</div>

<h2>Popüler Hizmetler</h2>
<table>
    <thead><tr><th>Hizmet</th><th>Kullanım</th></tr></thead>
    <tbody>
    <?php foreach ($popularServices as $service): ?>
        <tr>
            <td><?= htmlspecialchars($service['calculator_type']) ?></td>
            <td><?= (int)$service['total'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<canvas id="dailyChart" height="120"></canvas>
<canvas id="rightsChart" height="120"></canvas>

<script>
const dailyData = <?= json_encode($dailyCalculations, JSON_UNESCAPED_UNICODE) ?>;
const rightsData = <?= json_encode($rightsStatus, JSON_UNESCAPED_UNICODE) ?>;

new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: {
        labels: dailyData.map(item => item.gun),
        datasets: [{
            label: 'Günlük Hesaplama',
            data: dailyData.map(item => Number(item.total)),
            borderColor: '#3b82f6',
            tension: 0.25,
            fill: false
        }]
    }
});

new Chart(document.getElementById('rightsChart'), {
    type: 'bar',
    data: {
        labels: rightsData.map(item => item.status),
        datasets: [{
            label: 'Hak Başvuru Durumları',
            data: rightsData.map(item => Number(item.total)),
            backgroundColor: ['#f59e0b', '#10b981', '#ef4444', '#6366f1']
        }]
    }
});
</script>
</body>
</html>
