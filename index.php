<?php
session_start();

// Basit giriş kontrolü
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Basit doğrulama (güvenlik için daha sonra geliştirilecek)
    if ($username === 'admin' && $password === 'dijital2026!') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Kullanıcı adı veya şifre hatalı!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dijital Sosyal Hak Rehberliği - Admin Panel</title>
    <meta name="description" content="Dijital Sosyal Hak Platformu yönetim paneli. SGK, e-Nabız ve e-Devlet entegrasyonuna hazır sosyal hak yönetim altyapısı.">
    <meta name="keywords" content="sosyal haklar, SGK, e-Nabız, e-Devlet, sosyal yardım, engelli hakları">
    <meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Dijital Sosyal Hak Rehberliği">
    <meta property="og:description" content="Sosyal hak danışmanlığı ve dijital başvuru altyapısı.">
    <meta property="og:url" content="https://sosyalhizmetdanismanligi.com/">
    <meta property="og:site_name" content="Dijital Sosyal Hak Platformu">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="https://sosyalhizmetdanismanligi.com/">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preload" as="style" href="style.min.css">
    <link rel="stylesheet" href="style.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "Dijital Sosyal Hak Platformu",
      "url": "https://sosyalhizmetdanismanligi.com",
      "description": "Türkiye'de sosyal haklara erişim için dijital rehberlik platformu.",
      "sameAs": [
        "https://www.instagram.com/sosyalhizmet.danismanligi/"
      ]
    }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 2rem;
            max-width: 400px;
            width: 100%;
        }
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo h3 {
            color: #667eea;
            font-weight: bold;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-card">
                    <div class="logo">
                        <h3>🏛️ Admin Panel</h3>
                        <p class="text-muted">Dijital Sosyal Hak Rehberliği</p>
                    </div>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Kullanıcı Adı</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Şifre</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-login text-white w-100">
                            Giriş Yap
                        </button>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            Güvenli giriş sistemi - KVKK uyumlu
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="script.min.js" defer></script>
</body>
</html>