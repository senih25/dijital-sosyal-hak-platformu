<?php include 'header.php'; ?>

<section class="page-hero">
    <div class="container">
        <span class="badge">Gelişmiş CMS</span>
        <h1>İçerik Yönetim Sistemi</h1>
        <p>Blog, mevzuat güncellemeleri ve akademik makaleleri tek merkezden yönetin. SEO, kategori ve etiket altyapısı dahildir.</p>
    </div>
</section>

<main class="container">
    <section class="card" style="margin-bottom:1rem;">
        <h2 style="margin-top:0;font-family:var(--font-heading);">İçerik Operasyon Paneli</h2>
        <div class="filter-row">
            <input type="search" id="searchInput" placeholder="Başlık veya içerikte ara (SEO uyumlu arama)">
            <select id="typeFilter">
                <option value="all">Tüm İçerik Türleri</option>
                <option value="blog">Blog</option>
                <option value="mevzuat">Mevzuat Güncellemesi</option>
                <option value="akademik">Akademik Makale</option>
            </select>
            <select id="tagFilter">
                <option value="all">Tüm Etiketler</option>
                <option value="engelli-haklari">#engelli-haklari</option>
                <option value="bakim-destegi">#bakim-destegi</option>
                <option value="gelir-testi">#gelir-testi</option>
                <option value="ulusal-mevzuat">#ulusal-mevzuat</option>
            </select>
        </div>
        <p style="margin-bottom:0;color:#64748b;">SEO alanları: slug, meta title, meta description, canonical URL, schema markup ve okunabilirlik skoru.</p>
    </section>

    <section class="grid-3" id="contentGrid">
        <article class="card content-card" data-type="blog" data-tags="engelli-haklari,bakim-destegi" data-text="evde bakım maaşı başvuru adımları 2026 rehber">
            <span class="badge">Blog</span>
            <h3>Evde Bakım Maaşı Başvurusu: 2026 Yol Haritası</h3>
            <p>Kontrol listeleri, kurum sıralaması ve belge hazırlık adımlarıyla optimize edilmiş içerik.</p>
            <div class="tags"><span class="tag">#engelli-haklari</span><span class="tag">#bakim-destegi</span></div>
        </article>

        <article class="card content-card" data-type="mevzuat" data-tags="ulusal-mevzuat,gelir-testi" data-text="2022 sayılı kanun güncellemesi gelir sınırı">
            <span class="badge" style="background:#fff7e6;color:#92400e;">Mevzuat Güncellemesi</span>
            <h3>2022 Sayılı Kanun Gelir Sınırı Güncellemesi</h3>
            <p>Resmi değişiklik özeti, uygulama tarihi, etkilenen hak grupları ve sık sorulan sorular.</p>
            <div class="tags"><span class="tag">#ulusal-mevzuat</span><span class="tag">#gelir-testi</span></div>
        </article>

        <article class="card content-card" data-type="akademik" data-tags="engelli-haklari,ulusal-mevzuat" data-text="sosyal hizmet modelleri akademik değerlendirme makale">
            <span class="badge" style="background:#eef2ff;color:#3730a3;">Akademik Makale</span>
            <h3>Türkiye'de Hak Temelli Sosyal Hizmet Modelleri</h3>
            <p>Kaynakçalı içerik, metodoloji özeti, alıntılanabilir bölüm yapısı ve DOI alanı desteği.</p>
            <div class="tags"><span class="tag">#engelli-haklari</span><span class="tag">#ulusal-mevzuat</span></div>
        </article>
    </section>
</main>

<script>
const searchInput = document.getElementById('searchInput');
const typeFilter = document.getElementById('typeFilter');
const tagFilter = document.getElementById('tagFilter');
const cards = [...document.querySelectorAll('.content-card')];

function filterCards() {
    const search = searchInput.value.toLowerCase().trim();
    const type = typeFilter.value;
    const tag = tagFilter.value;

    cards.forEach(card => {
        const matchesSearch = !search || card.dataset.text.includes(search);
        const matchesType = type === 'all' || card.dataset.type === type;
        const matchesTag = tag === 'all' || card.dataset.tags.includes(tag);
        card.style.display = matchesSearch && matchesType && matchesTag ? 'block' : 'none';
    });
}

[searchInput, typeFilter, tagFilter].forEach(el => el.addEventListener('input', filterCards));
</script>

<?php include 'footer.php'; ?>
