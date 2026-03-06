<?php

declare(strict_types=1);

/**
 * E-posta otomasyon sistemi.
 *
 * Özellikler:
 * - Hoş geldin serileri
 * - Hatırlatma e-postaları
 * - Özel gün kampanyaları
 * - Segmentasyon
 * - Kişiselleştirme
 */
class EmailAutomationSystem
{
    private array $welcomeSeries;

    public function __construct()
    {
        $this->welcomeSeries = [
            [
                'day_offset' => 0,
                'subject' => 'Aramıza hoş geldiniz, {name}! 👋',
                'template' => "Merhaba {name},\n\nPlatformumuza kayıt olduğunuz için teşekkür ederiz. İlk adım olarak profilinizi tamamlayabilirsiniz.",
            ],
            [
                'day_offset' => 2,
                'subject' => 'Haklarınızı daha hızlı keşfetmek ister misiniz?',
                'template' => "Merhaba {name},\n\nSize özel rehberleri görmek için tercihlerinizi güncelleyebilirsiniz.",
            ],
            [
                'day_offset' => 5,
                'subject' => 'Sizin için önerilen kaynaklar hazır ✅',
                'template' => "Merhaba {name},\n\n{segment} grubuna özel içerikleriniz hazır. Hemen incelemek için panelinize giriş yapın.",
            ],
        ];
    }

    /**
     * Kullanıcı segmentlerini çıkarır.
     */
    public function segmentUsers(array $users): array
    {
        $segments = [
            'yeni_kullanici' => [],
            'aktif_kullanici' => [],
            'pasif_kullanici' => [],
            'destek_basvurusu_bekleyen' => [],
        ];

        foreach ($users as $user) {
            $daysSinceLogin = $user['days_since_last_login'] ?? 999;
            $hasOpenApplication = (bool)($user['has_open_application'] ?? false);

            if (($user['days_since_signup'] ?? 999) <= 7) {
                $segments['yeni_kullanici'][] = $user;
            }

            if ($daysSinceLogin <= 7) {
                $segments['aktif_kullanici'][] = $user;
            } else {
                $segments['pasif_kullanici'][] = $user;
            }

            if ($hasOpenApplication) {
                $segments['destek_basvurusu_bekleyen'][] = $user;
            }
        }

        return $segments;
    }

    /**
     * Hoş geldin serisi e-posta planını üretir.
     */
    public function buildWelcomeSeries(array $user): array
    {
        $result = [];
        $segment = $this->resolveSegmentLabel($user);

        foreach ($this->welcomeSeries as $step) {
            $result[] = [
                'send_in_days' => $step['day_offset'],
                'subject' => $this->personalizeTemplate($step['subject'], $user, $segment),
                'body' => $this->personalizeTemplate($step['template'], $user, $segment),
            ];
        }

        return $result;
    }

    /**
     * Tarihe göre özel gün e-postası üretir.
     */
    public function buildSpecialDayCampaign(array $user, DateTimeImmutable $date): ?array
    {
        $segment = $this->resolveSegmentLabel($user);
        $monthDay = $date->format('m-d');

        $specialTemplates = [
            '01-01' => [
                'subject' => 'Yeni yıl hedeflerinizi birlikte planlayalım 🎯',
                'body' => 'Merhaba {name}, yeni yılda sosyal hak yolculuğunuz için kişiselleştirilmiş bir plan hazırladık.',
            ],
            '05-10' => [
                'subject' => 'Engelliler Haftası için özel bilgilendirme',
                'body' => 'Merhaba {name}, {segment} kullanıcılarımız için güncel başvuru rehberlerini derledik.',
            ],
            '12-03' => [
                'subject' => 'Dünya Engelliler Günü farkındalık bülteni',
                'body' => 'Merhaba {name}, başvuru süreçleri ve destek mekanizmaları hakkında yeni içeriklerimizi inceleyin.',
            ],
        ];

        if (!isset($specialTemplates[$monthDay])) {
            return null;
        }

        return [
            'subject' => $this->personalizeTemplate($specialTemplates[$monthDay]['subject'], $user, $segment),
            'body' => $this->personalizeTemplate($specialTemplates[$monthDay]['body'], $user, $segment),
        ];
    }

    /**
     * Açık görevler için hatırlatma e-postaları oluşturur.
     */
    public function buildReminders(array $tasks, array $user): array
    {
        $segment = $this->resolveSegmentLabel($user);
        $reminders = [];

        foreach ($tasks as $task) {
            $deadline = new DateTimeImmutable($task['deadline']);
            $today = new DateTimeImmutable('today');
            $daysLeft = (int)$today->diff($deadline)->format('%r%a');

            if ($daysLeft > 3) {
                continue;
            }

            $tone = $daysLeft <= 0 ? 'Acil' : 'Hatırlatma';
            $reminders[] = [
                'subject' => sprintf('%s: %s', $tone, $task['title']),
                'body' => $this->personalizeTemplate(
                    "Merhaba {name},\n\n{segment} kullanıcıları için önemli bir başvuru adımı: {$task['title']}. Son tarih: {$task['deadline']}",
                    $user,
                    $segment
                ),
                'days_left' => $daysLeft,
            ];
        }

        return $reminders;
    }

    /**
     * Şablon kişiselleştirme.
     */
    public function personalizeTemplate(string $template, array $user, string $segment): string
    {
        $replacements = [
            '{name}' => $user['name'] ?? 'Değerli Kullanıcı',
            '{email}' => $user['email'] ?? '',
            '{segment}' => $segment,
        ];

        return strtr($template, $replacements);
    }

    private function resolveSegmentLabel(array $user): string
    {
        if (($user['days_since_signup'] ?? 999) <= 7) {
            return 'Yeni Kullanıcı';
        }

        if (($user['days_since_last_login'] ?? 999) > 30) {
            return 'Pasif Kullanıcı';
        }

        return 'Aktif Kullanıcı';
    }
}

// Örnek CLI kullanımı
if (PHP_SAPI === 'cli' && basename((string)($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    $system = new EmailAutomationSystem();

    $user = [
        'name' => 'Ayşe Yılmaz',
        'email' => 'ayse@example.com',
        'days_since_signup' => 3,
        'days_since_last_login' => 1,
        'has_open_application' => true,
    ];

    $tasks = [
        ['title' => 'Gelir testi belgesi yükleme', 'deadline' => (new DateTimeImmutable('+2 days'))->format('Y-m-d')],
        ['title' => 'Sağlık kurulu raporu güncelleme', 'deadline' => (new DateTimeImmutable('+6 days'))->format('Y-m-d')],
    ];

    echo "=== HOŞ GELDİN SERİSİ ===\n";
    print_r($system->buildWelcomeSeries($user));

    echo "\n=== HATIRLATMALAR ===\n";
    print_r($system->buildReminders($tasks, $user));

    echo "\n=== ÖZEL GÜN ===\n";
    print_r($system->buildSpecialDayCampaign($user, new DateTimeImmutable('2026-05-10')));
}
