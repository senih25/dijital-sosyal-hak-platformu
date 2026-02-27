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
        $_SESSION['authenticator_secret'] = $_SESSION['authenticator_secret'] 
            ?? TwoFactorAuthManager::generateAuthenticatorSecret();

        $show2FA = true;

        SecurityLogger::logAuthAttempt($demoUser['id'], 'password_login_success', true);

        $info = 'Şifre doğrulandı. Lütfen ikinci doğrulama adımını tamamlayın.';

    } else {
        SecurityLogger::logAuthAttempt($demoUser['id'], 'password_login_failed', false, [
            'username' => $username
        ]);
        $error = 'Kullanıcı adı veya şifre hatalı!';
    }
}

if (isset($_POST['send_2fa']) && isset($_SESSION['pending_2fa_user'])) {

    $method = SecurityManager::sanitizeInput($_POST['two_factor_method'] ?? 'email');

    $destination = $method === 'sms'
        ? $_SESSION['pending_2fa_user']['phone']
        : $_SESSION['pending_2fa_user']['email'];

    TwoFactorAuthManager::sendCode(
        $_SESSION['pending_2fa_user']['id'],
        $destination,
        $method
    );

    $maskedDestination = SecurityManager::maskPersonalData(
        $destination,
        $method === 'sms' ? 'phone' : 'email'
    );

    $info = "{$maskedDestination} adresine doğrulama kodu gönderildi.";
    $show2FA = true;
}

if (isset($_POST['verify_2fa']) && isset($_SESSION['pending_2fa_user'])) {

    $userId = $_SESSION['pending_2fa_user']['id'];
    $verificationType = SecurityManager::sanitizeInput($_POST['verification_type'] ?? 'code');
    $verified = false;

    if ($verificationType === 'code') {

        $verified = TwoFactorAuthManager::verifyCode(
            $userId,
            $_POST['verification_code'] ?? ''
        );

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

        AuditLogger::log(
            'user_login_completed',
            $userId,
            json_encode(['2fa_type' => $verificationType])
        );

        unset($_SESSION['pending_2fa_user']);

        header('Location: dashboard.php');
        exit;
    }

    $show2FA = true;
    $error = 'İkinci doğrulama başarısız oldu.';
}


/*
|--------------------------------------------------------------------------
| DevCycle Feature Flags
|--------------------------------------------------------------------------
*/

$devcycleEnabled = false;
$showNewDashboard = false;
$dashboardVariant = null;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

if (file_exists(__DIR__ . '/config/devcycle.php')) {

    require_once __DIR__ . '/config/devcycle.php';

    try {

        $devcycle = new DevCycleManager();
        $userId = $_SESSION['user_id'] ?? 'anonymous';

        $showNewDashboard = $devcycle->isFeatureEnabled($userId, 'new-dashboard');
        $dashboardVariant = $devcycle->getVariant($userId, 'dashboard-redesign');
        $devcycleEnabled = true;

    } catch (\Exception $e) {
        error_log('DevCycle unavailable: ' . $e->getMessage());
    }
}