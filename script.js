// Sosyal Hizmet Rehberlik - Ana JavaScript Dosyası

// ========== HERO SLIDER ==========
let currentSlideIndex = 0;
let slideInterval;

function showSlide(index) {
    const slides = document.querySelectorAll('.hero-slider .slide');
    const dots = document.querySelectorAll('.slider-nav .dot');
    
    if (!slides.length) return;
    
    // Loop around
    if (index >= slides.length) {
        currentSlideIndex = 0;
    } else if (index < 0) {
        currentSlideIndex = slides.length - 1;
    } else {
        currentSlideIndex = index;
    }
    
    // Hide all slides
    slides.forEach(slide => {
        slide.classList.remove('active');
    });
    
    // Remove active from all dots
    dots.forEach(dot => {
        dot.classList.remove('active');
    });
    
    // Show current slide
    slides[currentSlideIndex].classList.add('active');
    dots[currentSlideIndex].classList.add('active');
}

function changeSlide(direction) {
    showSlide(currentSlideIndex + direction);
    resetSlideTimer();
}

function currentSlide(index) {
    showSlide(index);
    resetSlideTimer();
}

function autoSlide() {
    showSlide(currentSlideIndex + 1);
}

function resetSlideTimer() {
    clearInterval(slideInterval);
    slideInterval = setInterval(autoSlide, 5000);
}

// Initialize slider
if (document.querySelector('.hero-slider')) {
    resetSlideTimer();
}

// ========== MAIN FUNCTIONALITY ==========

document.addEventListener('DOMContentLoaded', function() {
    
    // Gelir Testi Hesaplama
    const incomeTestForm = document.getElementById('income-test-form');
    if (incomeTestForm) {
        incomeTestForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const householdIncome = parseFloat(document.getElementById('household-income').value);
            const householdMembers = parseInt(document.getElementById('household-members').value);
            
            if (householdIncome && householdMembers) {
                calculateIncomeTest(householdIncome, householdMembers);
            }
        });
    }
    
    // Engel Oranı Hesaplama
    const disabilityRateForm = document.getElementById('disability-rate-form');
    if (disabilityRateForm) {
        disabilityRateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const disabilityRate = parseInt(document.getElementById('disability-rate').value);
            
            if (disabilityRate) {
                calculateDisabilityRights(disabilityRate);
            }
        });
    }
    
    // Sosyal Haklar Sorgulama
    const rightsForm = document.getElementById('rights-form');
    if (rightsForm) {
        rightsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const disabilityRate = parseInt(document.getElementById('rights-disability-rate').value);
            
            if (disabilityRate) {
                showSocialRights(disabilityRate);
            }
        });
    }
    
    // Smooth Scroll - sadece aynı sayfa içi linkler için
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href && href.length > 1) {
                try {
                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                } catch (err) {
                    // Geçersiz selector, yoksay
                }
            }
        });
    });
    
    // Form Validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Gelir Testi Hesaplama Fonksiyonu
function calculateIncomeTest(householdIncome, householdMembers) {
    const perPersonIncome = householdIncome / householdMembers;
    const minWage = 17002.12; // 2024 asgari ücret (net)
    const threshold = minWage / 3;
    
    let eligibility = [];
    let statusClass = 'success';
    let statusText = '';
    
    if (perPersonIncome < threshold) {
        eligibility = [
            'Evde Bakım Maaşı',
            '2022 Sayılı Kanun Engelli Aylığı',
            'Sosyal Yardım Programları',
            'Ücretsiz Sağlık Hizmetleri'
        ];
        statusText = 'Birçok sosyal yardım için uygunsunuz';
        statusClass = 'success';
    } else if (perPersonIncome < (threshold * 2)) {
        eligibility = [
            'Bazı Sosyal Yardım Programları',
            'Kısmi Destekler'
        ];
        statusText = 'Bazı yardımlar için uygun olabilirsiniz';
        statusClass = 'warning';
    } else {
        eligibility = [
            'Gelir sınırı üzerinde',
            'Belirli yardımlar için uygun olmayabilirsiniz'
        ];
        statusText = 'Gelir seviyeniz yardım sınırının üzerinde';
        statusClass = 'info';
    }
    
    const resultHTML = `
        <div class="result-box animate-fade-in-up">
            <h5><i class="fas fa-chart-line me-2"></i>Hesaplama Sonuçları</h5>
            <div class="result-item">
                <strong>Hane Geliri:</strong> ${formatPrice(householdIncome)}
            </div>
            <div class="result-item">
                <strong>Hane Üye Sayısı:</strong> ${householdMembers} kişi
            </div>
            <div class="result-item">
                <strong>Kişi Başı Gelir:</strong> ${formatPrice(perPersonIncome)}
            </div>
            <div class="result-item">
                <strong>Asgari Ücret (Net):</strong> ${formatPrice(minWage)}
            </div>
            <div class="result-item">
                <strong>1/3 Eşik Değeri:</strong> ${formatPrice(threshold)}
            </div>
            <div class="alert alert-${statusClass} mt-3" role="alert">
                <strong>${statusText}</strong>
            </div>
            <h6 class="mt-3 mb-2">Faydalanabileceğiniz Haklar:</h6>
            <ul class="eligibility-list">
                ${eligibility.map(item => `<li>${item}</li>`).join('')}
            </ul>
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Bu hesaplama genel bir bilgilendirme amaçlıdır. Kesin değerlendirme için ilgili kurumlara başvurunuz.
                </small>
            </div>
        </div>
    `;
    
    document.getElementById('income-test-result').innerHTML = resultHTML;
}

// Engel Oranı Hesaplama
function calculateDisabilityRights(disabilityRate) {
    let rights = [];
    let cardColor = 'primary';
    
    if (disabilityRate >= 40) {
        rights.push('Engelli Kimlik Kartı');
        rights.push('Engelli Sağlık Kurulu Raporu');
        rights.push('Ücretsiz/İndirimli Ulaşım');
    }
    
    if (disabilityRate >= 50) {
        rights.push('Engelli İstihdamı Kontenjanı');
        rights.push('MTV Muafiyeti (Taşıt için)');
        cardColor = 'success';
    }
    
    if (disabilityRate >= 60) {
        rights.push('2022 Sayılı Kanun Engelli Aylığı (Gelir testi ile)');
    }
    
    if (disabilityRate >= 70) {
        rights.push('Evde Bakım Maaşı (Gelir testi ile)');
        rights.push('Malulen Emeklilik (Sigorta şartları ile)');
        cardColor = 'warning';
    }
    
    if (disabilityRate >= 80) {
        rights.push('Yoğun Bakım Desteği');
        rights.push('Ağır Engelli Hakları');
        cardColor = 'danger';
    }
    
    if (rights.length === 0) {
        rights.push('Bu oran için engelli hakları tanımlanmamış olabilir');
        rights.push('Lütfen sağlık kuruluşunuzla görüşün');
    }
    
    const resultHTML = `
        <div class="result-box animate-fade-in-up">
            <h5><i class="fas fa-percentage me-2"></i>Engel Oranı: %${disabilityRate}</h5>
            <div class="alert alert-${cardColor} mt-3">
                <strong>Bu oranla faydalanabileceğiniz haklar:</strong>
            </div>
            <ul class="eligibility-list">
                ${rights.map(right => `<li>${right}</li>`).join('')}
            </ul>
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Bazı haklar ek kriterler gerektirebilir. Detaylı bilgi için danışmanlık hizmeti alabilirsiniz.
                </small>
            </div>
        </div>
    `;
    
    document.getElementById('disability-rate-result').innerHTML = resultHTML;
}

// Sosyal Haklar Göster
function showSocialRights(disabilityRate) {
    calculateDisabilityRights(disabilityRate);
    const resultDiv = document.getElementById('disability-rate-result');
    if (resultDiv) {
        document.getElementById('rights-result').innerHTML = resultDiv.innerHTML;
    }
}

// Fiyat Formatla
function formatPrice(price) {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY'
    }).format(price);
}

// Arama Fonksiyonu
function searchContent(query) {
    if (query.length < 3) {
        return;
    }
    
    // AJAX ile arama yapılabilir
    console.log('Arama yapılıyor: ' + query);
}

// Sepete Ekle
function addToCart(productId) {
    fetch('cart-add.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Ürün sepete eklendi!');
            updateCartCount();
        } else {
            alert('Bir hata oluştu: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu!');
    });
}

// Sepet Sayısını Güncelle
function updateCartCount() {
    fetch('cart-count.php')
    .then(response => response.json())
    .then(data => {
        const cartBadge = document.getElementById('cart-count');
        if (cartBadge && data.count) {
            cartBadge.textContent = data.count;
        }
    });
}

// Filtreleme
function filterContent(filterType, filterValue) {
    const url = new URL(window.location.href);
    url.searchParams.set(filterType, filterValue);
    window.location.href = url.toString();
}

// Scroll To Top
const scrollToTopBtn = document.getElementById('scroll-to-top');
if (scrollToTopBtn) {
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.style.display = 'block';
        } else {
            scrollToTopBtn.style.display = 'none';
        }
    });
    
    scrollToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ========== AI CHATBOT ==========
(function initEnhancedChatbot() {
    const chatRoot = document.getElementById('ai-chatbot');
    if (!chatRoot) return;

    const logEl = document.getElementById('chat-log');
    const formEl = document.getElementById('chat-form');
    const inputEl = document.getElementById('chat-input');
    const langEl = document.getElementById('chat-lang');
    const voiceBtn = document.getElementById('voice-btn');
    const learningScoreEl = document.getElementById('learning-score');

    const state = {
        lang: 'tr',
        context: [],
        learningScore: Number(localStorage.getItem('aiLearningScore') || 0)
    };

    const intents = {
        tr: {
            gelir: 'Gelir testi için hane geliri ve kişi sayısını paylaşın, size uygun destekleri hesaplayayım.',
            engelli: 'Engellilik oranına göre ulaşım, aylık ve bakım yardımlarını kontrol edebiliriz.',
            belge: 'Rapor yükleyerek OCR tabanlı belge analizi ile risk ve kategori sonucu alabilirsiniz.',
            varsayilan: 'Sorunuzu anladım. Gelir testi, engellilik hakları, başvuru evrakları veya destek programları hakkında detay verebilirim.'
        },
        en: {
            income: 'Share household income and member count. I can estimate social support eligibility.',
            disability: 'I can map disability rate to transport, allowance and care benefits.',
            report: 'Upload a report to run OCR-based extraction, categorization and risk analysis.',
            default: 'I can help with eligibility, document requirements and social rights guidance.'
        }
    };

    function addMessage(text, role) {
        const div = document.createElement('div');
        div.className = `chat-msg ${role}`;
        div.textContent = text;
        logEl.appendChild(div);
        logEl.scrollTop = logEl.scrollHeight;
    }

    function detectIntent(message) {
        const normalized = message.toLowerCase();
        if (state.lang === 'tr') {
            if (normalized.includes('gelir') || normalized.includes('maaş')) return intents.tr.gelir;
            if (normalized.includes('engel') || normalized.includes('rapor')) return intents.tr.engelli;
            if (normalized.includes('belge') || normalized.includes('ocr')) return intents.tr.belge;
            return intents.tr.varsayilan;
        }
        if (normalized.includes('income') || normalized.includes('salary')) return intents.en.income;
        if (normalized.includes('disability') || normalized.includes('rate')) return intents.en.disability;
        if (normalized.includes('document') || normalized.includes('ocr') || normalized.includes('report')) return intents.en.report;
        return intents.en.default;
    }

    function respond(message) {
        const base = detectIntent(message);
        const contextualSuffix = state.context.length
            ? (state.lang === 'tr' ? ` Önceki sorunuz: "${state.context[state.context.length - 1]}".` : ` Previous context: "${state.context[state.context.length - 1]}".`)
            : '';

        const response = base + contextualSuffix;
        addMessage(response, 'bot');

        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(response);
            utterance.lang = state.lang === 'tr' ? 'tr-TR' : 'en-US';
            window.speechSynthesis.speak(utterance);
        }

        state.learningScore += 1;
        localStorage.setItem('aiLearningScore', String(state.learningScore));
        learningScoreEl.textContent = (state.lang === 'tr' ? 'Öğrenme skoru: ' : 'Learning score: ') + state.learningScore;
    }

    formEl.addEventListener('submit', function (e) {
        e.preventDefault();
        const message = inputEl.value.trim();
        if (!message) return;

        addMessage(message, 'user');
        state.context.push(message);
        if (state.context.length > 4) state.context.shift();

        respond(message);
        inputEl.value = '';
    });

    langEl.addEventListener('change', function () {
        state.lang = this.value;
        chatRoot.setAttribute('data-lang', state.lang);
        addMessage(state.lang === 'tr' ? 'Dil Türkçe olarak güncellendi.' : 'Language switched to English.', 'bot');
        learningScoreEl.textContent = (state.lang === 'tr' ? 'Öğrenme skoru: ' : 'Learning score: ') + state.learningScore;
    });

    voiceBtn.addEventListener('click', function () {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            addMessage(state.lang === 'tr' ? 'Tarayıcı sesli giriş desteklemiyor.' : 'Voice input is not supported in this browser.', 'bot');
            return;
        }

        const recognition = new SpeechRecognition();
        recognition.lang = state.lang === 'tr' ? 'tr-TR' : 'en-US';
        recognition.start();

        recognition.onresult = function (event) {
            const spoken = event.results[0][0].transcript;
            inputEl.value = spoken;
            addMessage(spoken, 'user');
            state.context.push(spoken);
            respond(spoken);
            inputEl.value = '';
        };
    });

    learningScoreEl.textContent = 'Öğrenme skoru: ' + state.learningScore;
    addMessage('Merhaba! Size sosyal hak, gelir testi ve başvuru süreçlerinde yardımcı olabilirim.', 'bot');
})();
