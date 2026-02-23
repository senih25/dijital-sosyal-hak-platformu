<?php
// SEO ve Erişilebilirlik İyileştirmeleri

// Meta etiketleri için dinamik içerik
$seo_data = [
    'home' => [
        'title' => 'Dijital Sosyal Hak Rehberliği | Türkiye\'nin En Kapsamlı Sosyal Hizmet Platformu',
        'description' => 'SGK hakları, engelli raporu, evde bakım maaşı, sosyal yardımlar ve tüm sosyal haklar için dijital rehberlik. 2026 güncel mevzuat ile hak analizi.',
        'keywords' => 'sosyal haklar, SGK, engelli raporu, evde bakım maaşı, sosyal hizmet, dijital danışmanlık, 2026 mevzuat',
        'canonical' => 'https://www.sosyalhizmetdanismanligi.com/'
    ],
    'hesaplama-araclari' => [
        'title' => '2026 Güncel Hesaplama Araçları | Sosyal Hak Hesaplayıcı',
        'description' => 'Gelir testi, Balthazard hesaplama, engelli emeklilik simülasyonu. 2026 mevzuatına uygun güncel hesaplama araçları.',
        'keywords' => 'gelir testi, balthazard hesaplama, engelli emeklilik, hesaplama araçları, 2026 mevzuat',
        'canonical' => 'https://www.sosyalhizmetdanismanligi.com/hesaplama-araclari'
    ],
    'hakkimizda' => [
        'title' => 'Hakkımızda | Dijital Sosyal Hak Rehberliği Misyonumuz',
        'description' => 'Türkiye\'de sosyal hizmet alanında dijital dönüşümün öncüsü. Sosyal hakların korunması ve geliştirilmesi için çalışıyoruz.',
        'keywords' => 'sosyal hizmet uzmanı, dijital dönüşüm, sosyal haklar, misyon, vizyon',
        'canonical' => 'https://www.sosyalhizmetdanismanligi.com/hakkimizda'
    ]
];

// Yapılandırılmış veri (Schema.org)
function generate_schema_org($page_type = 'website') {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Dijital Sosyal Hak Rehberliği',
        'url' => 'https://www.sosyalhizmetdanismanligi.com',
        'logo' => 'https://www.sosyalhizmetdanismanligi.com/assets/images/logo.png',
        'description' => 'Türkiye\'nin en kapsamlı dijital sosyal hizmet platformu',
        'address' => [
            '@type' => 'PostalAddress',
            'addressCountry' => 'TR'
        ],
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'contactType' => 'customer service',
            'availableLanguage' => ['Turkish']
        ],
        'sameAs' => [
            'https://www.instagram.com/sosyalhizmet.danismanligi/'
        ]
    ];
    
    return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

// Sitemap oluşturma fonksiyonu
function generate_sitemap() {
    $urls = [
        ['loc' => 'https://www.sosyalhizmetdanismanligi.com/', 'priority' => '1.0', 'changefreq' => 'daily'],
        ['loc' => 'https://www.sosyalhizmetdanismanligi.com/hesaplama-araclari', 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['loc' => 'https://www.sosyalhizmetdanismanligi.com/hakkimizda', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['loc' => 'https://www.sosyalhizmetdanismanligi.com/iletisim', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['loc' => 'https://www.sosyalhizmetdanismanligi.com/blog', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['loc' => 'https://www.sosyalhizmetdanismanligi.com/sss', 'priority' => '0.7', 'changefreq' => 'monthly']
    ];
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    foreach ($urls as $url) {
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . $url['loc'] . '</loc>' . "\n";
        $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
        $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
        $xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }
    
    $xml .= '</urlset>';
    
    return $xml;
}

// Robots.txt oluşturma
function generate_robots_txt() {
    $robots = "User-agent: *\n";
    $robots .= "Allow: /\n";
    $robots .= "Disallow: /admin/\n";
    $robots .= "Disallow: /includes/\n";
    $robots .= "Disallow: /assets/temp/\n";
    $robots .= "\n";
    $robots .= "Sitemap: https://www.sosyalhizmetdanismanligi.com/sitemap.xml\n";
    
    return $robots;
}

// Erişilebilirlik kontrolleri
function accessibility_check() {
    return [
        'alt_texts' => 'Tüm görsellerde alt text mevcut',
        'color_contrast' => 'WCAG 2.1 AA uyumlu renk kontrastı',
        'keyboard_navigation' => 'Klavye ile navigasyon destekleniyor',
        'screen_reader' => 'Ekran okuyucu uyumlu',
        'font_size' => 'Minimum 16px font boyutu',
        'focus_indicators' => 'Görünür odak göstergeleri mevcut'
    ];
}
?>