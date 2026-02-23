<?php
require_once __DIR__ . '/security.php';
SecurityManager::secureSessionStart();

$demoUser = [
    'id' => 1,
    'username' => 'admin',
    'password' => 'dijital2026!',
    'email' => 'admin@platform.local',
    'phone' => '05320000000',
    'security_question' => 'İlk evcil hayvanınızın adı?',
    'security_answer' => 'boncuk'
];

$error = null;
$info = null;
$show2FA = isset($_SESSION['pending_2fa_user']);
$showAuthenticatorSetup = !isset($_SESSION['authenticator_secret']);

if (isset($_POST['login'])) {
    $username = SecurityManager::sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!SecurityManager::checkRateLimit($_SERVER['REMOTE_ADDR'] ?? 'unknown', 'login_attempt', 8, 900)) {
        $error = 'Çok fazla giriş denemesi yaptınız. Lütfen 15 dakika sonra tekrar deneyin.';
    } elseif ($username === $demoUser['username'] && $password === $demoUser['password']) {
        $_SESSION['pending_2fa_user'] = $demoUser;
        $_SESSION['security_question'] = $demoUser['security_question'];
        $_SESSION['authenticator_secret'] = $_SESSION['authenticator_secret'] ?? TwoFactorAuthManager::generateAuthenticatorSecret();
        $show2FA = true;
        SecurityLogger::logAuthAttempt($demoUser['id'], 'password_login_success', true);
        $info = 'Şifre doğrulandı. Lütfen ikinci doğrulama adımını tamamlayın.';
    } else {
        SecurityLogger::logAuthAttempt($demoUser['id'], 'password_login_failed', false, ['username' => $username]);
        $error = 'Kullanıcı adı veya şifre hatalı!';
    }
}

if (isset($_POST['send_2fa']) && isset($_SESSION['pending_2fa_user'])) {
    $method = SecurityManager::sanitizeInput($_POST['two_factor_method'] ?? 'email');
    $destination = $method === 'sms'
        ? $_SESSION['pending_2fa_user']['phone']
        : $_SESSION['pending_2fa_user']['email'];

    $code = TwoFactorAuthManager::sendCode($_SESSION['pending_2fa_user']['id'], $destination, $method);
    $maskedDestination = SecurityManager::maskPersonalData($destination, $method === 'sms' ? 'phone' : 'email');
    $info = "{$maskedDestination} adresine doğrulama kodu gönderildi. Demo kod: {$code}";
    $show2FA = true;
}

if (isset($_POST['verify_2fa']) && isset($_SESSION['pending_2fa_user'])) {
    $userId = $_SESSION['pending_2fa_user']['id'];
    $verificationType = SecurityManager::sanitizeInput($_POST['verification_type'] ?? 'code');
    $verified = false;

    if ($verificationType === 'code') {
        $verified = TwoFactorAuthManager::verifyCode($userId, $_POST['verification_code'] ?? '');
    } elseif ($verificationType === 'authenticator') {
        $verified = TwoFactorAuthManager::verifyAuthenticatorCode(
            $_SESSION['authenticator_secret'],
            $_POST['authenticator_code'] ?? ''
        );
    } elseif ($verificationType === 'security_question') {
        $verified = TwoFactorAuthManager::verifySecurityQuestion(
            $_POST['security_answer'] ?? '',
            $_SESSION['pending_2fa_user']['security_answer']
        );
    }

    if ($verified) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['user_id'] = $userId;
        AuditLogger::log('user_login_completed', $userId, json_encode(['2fa_type' => $verificationType]));
        unset($_SESSION['pending_2fa_user']);
        header('Location: dashboard.php');
        exit;
    }

    $show2FA = true;
    $error = 'İkinci doğrulama başarısız oldu. Lütfen bilgilerinizi kontrol edin.';
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dijital Sosyal Hak Rehberliği - Güvenli Admin Girişi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 32px 0;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 2rem;
            width: 100%;
        }
        .logo { text-align: center; margin-bottom: 1.5rem; }
        .logo h3 { color: #667eea; font-weight: bold; }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: bold;
        }
        .security-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: .8rem 1rem;
            border-radius: 8px;
            font-size: .9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-xl-6">
                <div class="login-card">
                    <div class="logo">
                        <h3>🏛️ Admin Panel</h3>
                        <p class="text-muted mb-0">KVKK uyumlu + iki faktörlü doğrulama</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo SecurityManager::preventXSS($error); ?></div>
                    <?php endif; ?>

                    <?php if ($info): ?>
                        <div class="alert alert-info"><?php echo SecurityManager::preventXSS($info); ?></div>
                    <?php endif; ?>

                    <?php if (!$show2FA): ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Kullanıcı Adı</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Şifre</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-login text-white w-100">Giriş Yap</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="mb-3">
                            <label class="form-label">SMS / E-posta ile kod gönder</label>
                            <div class="input-group">
                                <select class="form-select" name="two_factor_method">
                                    <option value="email">E-posta</option>
                                    <option value="sms">SMS</option>
                                </select>
                                <button type="submit" name="send_2fa" class="btn btn-outline-primary">Kod Gönder</button>
                            </div>
                        </form>

                        <form method="POST">
                            <div class="mb-2">
                                <label class="form-label">Doğrulama Yöntemi</label>
                                <select name="verification_type" class="form-select" required>
                                    <option value="code">SMS / E-posta Kodu</option>
                                    <option value="authenticator">Google Authenticator (TOTP)</option>
                                    <option value="security_question">Güvenlik Sorusu</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Kod</label>
                                <input type="text" name="verification_code" maxlength="6" class="form-control" placeholder="6 haneli kod">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Authenticator Kodu</label>
                                <input type="text" name="authenticator_code" maxlength="6" class="form-control" placeholder="Google Authenticator kodu">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Güvenlik Sorusu: <?php echo SecurityManager::preventXSS($_SESSION['security_question'] ?? '-'); ?></label>
                                <input type="text" name="security_answer" class="form-control" placeholder="Cevabınız">
                            </div>
                            <button type="submit" name="verify_2fa" class="btn btn-login text-white w-100">Doğrulamayı Tamamla</button>
                        </form>

                        <div class="security-box mt-3">
                            <strong>Google Authenticator Kurulum Anahtarı:</strong>
                            <code><?php echo SecurityManager::preventXSS($_SESSION['authenticator_secret']); ?></code>
                            <?php if ($showAuthenticatorSetup): ?>
                                <div class="text-muted mt-1">Bu anahtarı ilk girişte Authenticator uygulamanıza ekleyin.</div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-3 text-center">
                        <small class="text-muted">Giriş denemeleri, doğrulama adımları ve şüpheli aktiviteler güvenlik loglarına kaydedilir.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
