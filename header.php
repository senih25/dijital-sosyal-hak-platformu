<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1e40af">
    <title>Dijital Sosyal Hak Platformu</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
    <link rel="apple-touch-icon" href="/icons/icon.svg">
    <link rel="stylesheet" href="/style.css">
</head>
<body>

<a href="#main-content" class="skip-link" data-i18n="skip_to_content">Ana içeriğe geç</a>

<header class="main-header" role="banner">
    <div class="container header-flex">
        <div class="logo" data-i18n="site_title">Dijital Sosyal Hizmet</div>
        <nav aria-label="Ana menü">
            <a href="/" data-i18n="nav_home">Ana Sayfa</a>
            <a href="/hizmetlerimiz.php" data-i18n="nav_services">Hizmetler</a>
            <a href="/rehberlik.php" data-i18n="nav_guide">Hak Rehberi</a>
            <a href="/calculations.php" data-i18n="nav_calculation">Hesaplama</a>
            <a href="/iletisim.php" data-i18n="nav_contact">İletişim</a>
        </nav>
        <div class="header-controls" aria-label="Erişilebilirlik ve dil ayarları">
            <label class="visually-hidden" for="language-switcher">Dil seçimi</label>
            <select id="language-switcher" class="control-select" aria-label="Dil seçimi">
                <option value="tr">Türkçe</option>
                <option value="ku">Kurdî</option>
                <option value="ar">العربية</option>
            </select>
            <button id="font-toggle" class="control-btn" type="button" aria-pressed="false" data-i18n="large_font">Büyük Font</button>
            <button id="contrast-toggle" class="control-btn" type="button" aria-pressed="false" data-i18n="high_contrast">Yüksek Kontrast</button>
            <button id="read-aloud-toggle" class="control-btn" type="button" aria-pressed="false" data-i18n="read_aloud">Sesli Oku</button>
            <button id="install-pwa" class="control-btn" type="button" hidden data-i18n="install_app">Uygulamayı Yükle</button>
            <button id="enable-notifications" class="control-btn" type="button" data-i18n="enable_notifications">Bildirimleri Aç</button>
        </div>
    </div>
</header>

<main id="main-content" tabindex="-1">
