<?php
session_start();

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dijital Sosyal Hak Rehberliği</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="dashboard.php">Admin Dashboard</a>
        <div class="ms-auto">
            <a href="index.php" class="btn btn-outline-secondary btn-sm">Siteye Dön</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5><i class="fa-regular fa-circle-question text-primary me-2"></i>SSS Yönetimi</h5>
                    <p class="text-muted mb-3">Sıkça sorulan soruları görüntüleyin, ekleyin veya kaldırın.</p>
                    <a href="sss.php" class="btn btn-primary">SSS Sayfasına Git</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5><i class="fa-solid fa-calculator text-success me-2"></i>Hesaplama Araçları</h5>
                    <p class="text-muted mb-3">2026 gelir testi ve Balthazard araçlarını kontrol edin.</p>
                    <a href="hesaplama_araclari_calisir.php" class="btn btn-success">Araçları Aç</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="ai-chatbot.js"></script>
</body>
</html>
