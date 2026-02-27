<?php

declare(strict_types=1);

/**
 * Sosyal medya otomasyon sistemi.
 *
 * Özellikler:
 * - İçerik takvimi
 * - Otomatik paylaşım kuyruğu
 * - Hashtag optimizasyonu
 * - Engagement takibi
 */
class SocialMediaAutomationSystem
{
    /**
     * Haftalık içerik takvimi oluşturur.
     */
    public function buildContentCalendar(array $contentPool, array $platforms, DateTimeImmutable $startDate): array
    {
        $calendar = [];
        $slot = 0;

        for ($day = 0; $day < 7; $day++) {
            $date = $startDate->modify(sprintf('+%d days', $day))->format('Y-m-d');

            foreach ($platforms as $platform) {
                $content = $contentPool[$slot % count($contentPool)];
                $calendar[] = [
                    'date' => $date,
                    'platform' => $platform,
                    'content_type' => $content['type'],
                    'content' => $content['text'],
                    'suggested_time' => $this->bestTimeByPlatform($platform),
                ];
                $slot++;
            }
        }

        return $calendar;
    }

    /**
     * İçerik metnine göre hashtag seçer.
     */
    public function optimizeHashtags(string $content, array $candidateHashtags, int $limit = 8): array
    {
        $scored = [];
        $normalizedContent = mb_strtolower($content);

        foreach ($candidateHashtags as $tag => $baseScore) {
            $tagPlain = str_replace('#', '', mb_strtolower($tag));
            $relevanceBoost = mb_strpos($normalizedContent, $tagPlain) !== false ? 40 : 0;
            $lengthPenalty = max(0, mb_strlen($tagPlain) - 12);
            $score = $baseScore + $relevanceBoost - $lengthPenalty;
            $scored[$tag] = $score;
        }

        arsort($scored);
        return array_slice(array_keys($scored), 0, $limit);
    }

    /**
     * Takvimden otomatik paylaşım kuyruğu oluşturur.
     */
    public function buildAutoPublishQueue(array $calendar, array $hashtagsByPlatform): array
    {
        $queue = [];

        foreach ($calendar as $item) {
            $hashtags = $hashtagsByPlatform[$item['platform']] ?? [];
            $queue[] = [
                'scheduled_at' => $item['date'] . ' ' . $item['suggested_time'],
                'platform' => $item['platform'],
                'payload' => trim($item['content'] . ' ' . implode(' ', $hashtags)),
                'status' => 'queued',
            ];
        }

        return $queue;
    }

    /**
     * Engagement metriklerini hesaplar.
     */
    public function trackEngagement(array $publishedPosts): array
    {
        $summary = [
            'total_posts' => count($publishedPosts),
            'total_impressions' => 0,
            'total_interactions' => 0,
            'engagement_rate' => 0.0,
            'platform_breakdown' => [],
        ];

        foreach ($publishedPosts as $post) {
            $platform = $post['platform'];
            $impressions = (int)($post['impressions'] ?? 0);
            $interactions = (int)($post['likes'] ?? 0) + (int)($post['comments'] ?? 0) + (int)($post['shares'] ?? 0);

            $summary['total_impressions'] += $impressions;
            $summary['total_interactions'] += $interactions;

            if (!isset($summary['platform_breakdown'][$platform])) {
                $summary['platform_breakdown'][$platform] = [
                    'posts' => 0,
                    'impressions' => 0,
                    'interactions' => 0,
                    'engagement_rate' => 0.0,
                ];
            }

            $summary['platform_breakdown'][$platform]['posts']++;
            $summary['platform_breakdown'][$platform]['impressions'] += $impressions;
            $summary['platform_breakdown'][$platform]['interactions'] += $interactions;
        }

        $summary['engagement_rate'] = $summary['total_impressions'] > 0
            ? round(($summary['total_interactions'] / $summary['total_impressions']) * 100, 2)
            : 0.0;

        foreach ($summary['platform_breakdown'] as $platform => $data) {
            $summary['platform_breakdown'][$platform]['engagement_rate'] = $data['impressions'] > 0
                ? round(($data['interactions'] / $data['impressions']) * 100, 2)
                : 0.0;
        }

        return $summary;
    }

    private function bestTimeByPlatform(string $platform): string
    {
        return match (mb_strtolower($platform)) {
            'instagram' => '20:00:00',
            'twitter', 'x' => '12:30:00',
            'linkedin' => '09:00:00',
            'facebook' => '18:30:00',
            default => '10:00:00',
        };
    }
}

// Örnek CLI kullanımı
if (PHP_SAPI === 'cli' && basename((string)($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    $system = new SocialMediaAutomationSystem();

    $contentPool = [
        ['type' => 'blog', 'text' => 'Erişilebilirlik desteklerinde yeni dönem başladı.'],
        ['type' => 'video', 'text' => 'Gelir testi başvurusu adım adım nasıl yapılır?'],
        ['type' => 'duyuru', 'text' => 'Yeni danışmanlık randevu takvimi yayında.'],
    ];

    $platforms = ['Instagram', 'LinkedIn', 'Facebook'];
    $calendar = $system->buildContentCalendar($contentPool, $platforms, new DateTimeImmutable('today'));

    $hashtags = [
        'Instagram' => $system->optimizeHashtags($contentPool[0]['text'], ['#sosyalhak' => 90, '#engellihakları' => 85, '#duyuru' => 60]),
        'LinkedIn' => $system->optimizeHashtags($contentPool[1]['text'], ['#sosyalpolitika' => 75, '#kamuhizmeti' => 70, '#farkındalık' => 65]),
        'Facebook' => $system->optimizeHashtags($contentPool[2]['text'], ['#rehberlik' => 80, '#hakhukuk' => 72, '#yardım' => 64]),
    ];

    echo "=== İÇERİK TAKVİMİ ===\n";
    print_r(array_slice($calendar, 0, 5));

    echo "\n=== PAYLAŞIM KUYRUĞU ===\n";
    print_r(array_slice($system->buildAutoPublishQueue($calendar, $hashtags), 0, 3));

    echo "\n=== ENGAGEMENT ÖRNEĞİ ===\n";
    print_r($system->trackEngagement([
        ['platform' => 'Instagram', 'impressions' => 1800, 'likes' => 120, 'comments' => 18, 'shares' => 9],
        ['platform' => 'LinkedIn', 'impressions' => 950, 'likes' => 62, 'comments' => 11, 'shares' => 6],
    ]));
}
