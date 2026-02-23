<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') . ' - ' : ''; ?>Dijital Sosyal Hak Platformu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="main-header">
    <div class="container header-flex">
        <div class="logo">Dijital Sosyal Hizmet</div>
        <nav>
            <a href="index.php">Ana Sayfa</a>
            <a href="hizmetlerimiz.php">Hizmetler</a>
            <a href="rehberlik.php">Hak Rehberi</a>
            <a href="calculators.php">Hesaplama</a>
            <a href="ai_entegrasyonu.php">AI Entegrasyonu</a>
            <a href="iletisim.php">İletişim</a>
        </nav>
    </div>
</header>
