<?php
session_start();

const FAQ_FILE = __DIR__ . '/faq_data.json';

$seedFaqs = [
    [
        'category' => 'SGK İşlemleri',
        'question' => 'SGK engelli emeklilik şartları nelerdir?',
        'answer' => "2026 güncel uygulamada temel olarak en az %40 ve üzeri engellilik oranı, uygun sigortalılık süresi ve prim günü şartları aranır. Kişinin sigorta başlangıç tarihi ve statüsü (4A/4B/4C) nihai koşulları değiştirir. Kesin değerlendirme SGK dosya incelemesiyle yapılır."
    ],
    [
        'category' => 'Evde Bakım Maaşı',
        'question' => 'Evde bakım maaşı için gelir testi nasıl yapılır?',
        'answer' => "Gelir testinde hane toplam aylık net geliri, hanedeki kişi sayısına bölünerek kişi başı gelir bulunur. 2026 için asgari ücret 20.002 TL baz alınır ve başvuruya ilişkin mevzuat eşiği kapsamında değerlendirme yapılır. Ayrıca bakıma muhtaçlık kriteri de zorunludur."
    ],
    [
        'category' => 'Engelli Raporu',
        'question' => 'Engelli sağlık kurulu raporu nasıl alınır?',
        'answer' => "Yetkili hastaneye başvuru sonrası branş muayeneleri tamamlanır ve sağlık kurulu nihai oranı belirler. Rapor oranına itirazlar il sağlık müdürlüğü süreci üzerinden yürütülür."
    ],
    [
        'category' => 'ÇÖZGER',
        'question' => 'ÇÖZGER raporu hangi durumlarda kullanılır?',
        'answer' => "ÇÖZGER raporu 18 yaş altı çocuklar için düzenlenir. Özel eğitim, rehabilitasyon destekleri, sosyal yardımlar ve hak temelli resmi başvurularda kullanılır."
    ],
    [
        'category' => 'Sosyal Haklar',
        'question' => 'Sosyal yardım başvurusunda hangi belgeler istenir?',
        'answer' => "Kimlik belgesi, gelir ve hane bilgileri, sağlık/engellilik belgeleri ve başvurulan destek türüne göre ek evraklar istenir. İl/ilçe kurumları ek belge talep edebilir."
    ]
];

if (!file_exists(FAQ_FILE)) {
    file_put_contents(FAQ_FILE, json_encode($seedFaqs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$faqs = json_decode(file_get_contents(FAQ_FILE), true);
if (!is_array($faqs) || count($faqs) === 0) {
    $faqs = $seedFaqs;
}

$adminLoggedIn = !empty($_SESSION['admin_logged_in']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $adminLoggedIn) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $category = trim((string)($_POST['category'] ?? ''));
        $question = trim((string)($_POST['question'] ?? ''));
        $answer = trim((string)($_POST['answer'] ?? ''));

        if ($category !== '' && $question !== '' && $answer !== '') {
            $faqs[] = [
                'category' => $category,
                'question' => $question,
                'answer' => $answer
            ];
        }
    }

    if ($action === 'delete') {
        $index = (int)($_POST['index'] ?? -1);
        if (isset($faqs[$index])) {
            array_splice($faqs, $index, 1);
        }
    }

    file_put_contents(FAQ_FILE, json_encode(array_values($faqs), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header('Location: sss.php');
    exit;
}

$categories = array_values(array_unique(array_map(static fn(array $item): string => (string)($item['category'] ?? 'Genel'), $faqs)));
sort($categories);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSS - Dijital Sosyal Hak Rehberliği</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php">
            <i class="fas fa-home me-2"></i>Dijital Sosyal Hak Rehberliği
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Ana Sayfa</a>
            <a class="nav-link" href="hesaplama_araclari_calisir.php">Hesaplama</a>
            <a class="nav-link active" href="sss.php">SSS</a>
            <a class="nav-link" href="iletisim.php">İletişim</a>
            <a class="nav-link" href="admin.php">Admin</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="text-center mb-4">
        <h1 class="mb-3">
            <i class="fas fa-question-circle text-primary me-2"></i>
            Sıkça Sorulan Sorular
        </h1>
        <p class="text-muted">Sosyal haklar, SGK işlemleri, engelli raporu, evde bakım maaşı ve ÇÖZGER için güncel bilgi.</p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-8">
                    <input id="faqSearch" type="text" class="form-control" placeholder="Soru veya cevap içinde ara...">
                </div>
                <div class="col-md-4">
                    <select id="categoryFilter" class="form-select">
                        <option value="">Tüm Kategoriler</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="accordion" id="faqAccordion">
        <?php foreach ($faqs as $index => $faq): ?>
            <?php
            $question = (string)($faq['question'] ?? '');
            $answer = (string)($faq['answer'] ?? '');
            $category = (string)($faq['category'] ?? 'Genel');
            $searchText = strtolower($question . ' ' . $answer . ' ' . $category);
            ?>
            <div class="accordion-item faq-item" data-category="<?php echo htmlspecialchars($category); ?>" data-search="<?php echo htmlspecialchars($searchText); ?>">
                <h2 class="accordion-header" id="heading-<?php echo $index; ?>">
                    <button class="accordion-button <?php echo $index === 0 ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $index; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                        <span class="badge bg-primary-subtle text-primary-emphasis me-2"><?php echo htmlspecialchars($category); ?></span>
                        <?php echo htmlspecialchars($question); ?>
                    </button>
                </h2>
                <div id="collapse-<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <?php echo nl2br(htmlspecialchars($answer)); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($adminLoggedIn): ?>
        <div class="card border-0 shadow-sm mt-5">
            <div class="card-header bg-dark text-white">Admin Paneli - SSS Yönetimi</div>
            <div class="card-body">
                <form method="POST" class="row g-3 mb-3">
                    <input type="hidden" name="action" value="add">
                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <input type="text" class="form-control" name="category" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Soru</label>
                        <input type="text" class="form-control" name="question" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Yanıt</label>
                        <textarea class="form-control" name="answer" rows="3" required></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">Yeni SSS Ekle</button>
                    </div>
                </form>

                <hr>
                <div class="small text-muted mb-2">Mevcut kayıtlar</div>
                <?php foreach ($faqs as $index => $faq): ?>
                    <form method="POST" class="d-flex justify-content-between align-items-start gap-3 mb-2">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="index" value="<?php echo $index; ?>">
                        <div>
                            <strong>[<?php echo htmlspecialchars((string)($faq['category'] ?? 'Genel')); ?>]</strong>
                            <?php echo htmlspecialchars((string)($faq['question'] ?? '')); ?>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-secondary mt-4">SSS yönetimi için admin oturumu gerekir.</div>
    <?php endif; ?>
</div>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p class="mb-0">&copy; 2026 Dijital Sosyal Hak Rehberliği. Tüm hakları saklıdır.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const searchInput = document.getElementById('faqSearch');
const categoryFilter = document.getElementById('categoryFilter');
const faqItems = Array.from(document.querySelectorAll('.faq-item'));

function applyFaqFilter() {
    const query = searchInput.value.trim().toLowerCase();
    const selectedCategory = categoryFilter.value;

    faqItems.forEach((item) => {
        const isCategoryMatch = !selectedCategory || item.dataset.category === selectedCategory;
        const isSearchMatch = !query || item.dataset.search.includes(query);
        item.style.display = (isCategoryMatch && isSearchMatch) ? '' : 'none';
    });
}

searchInput.addEventListener('input', applyFaqFilter);
categoryFilter.addEventListener('change', applyFaqFilter);
</script>
<script src="ai-chatbot.js"></script>
</body>
</html>
