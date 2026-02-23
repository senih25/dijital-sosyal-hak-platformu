(function () {
    const style = document.createElement('style');
    style.textContent = `
        .ai-chatbot-toggle {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 1055;
            width: 58px;
            height: 58px;
            border: none;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            box-shadow: 0 10px 30px rgba(37, 99, 235, .35);
            cursor: pointer;
            font-size: 22px;
        }

        .ai-chatbot-panel {
            position: fixed;
            right: 20px;
            bottom: 90px;
            width: min(380px, calc(100vw - 24px));
            max-height: 75vh;
            z-index: 1055;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 18px 50px rgba(0, 0, 0, .2);
            overflow: hidden;
            display: none;
            border: 1px solid #e5e7eb;
        }

        .ai-chatbot-panel.open { display: flex; flex-direction: column; }
        .ai-chatbot-header { background: #1d4ed8; color: #fff; padding: 12px 14px; }
        .ai-chatbot-header h6 { margin: 0; font-size: 15px; }
        .ai-chatbot-header small { opacity: .9; }
        .ai-chatbot-kvkk {
            margin: 0;
            padding: 10px 12px;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
            color: #334155;
        }
        .ai-chatbot-body {
            padding: 12px;
            overflow: auto;
            background: #f9fbff;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .ai-chat-msg {
            max-width: 90%;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 14px;
            line-height: 1.45;
            white-space: pre-line;
        }
        .ai-chat-msg.bot { background: #e0ecff; color: #1e293b; align-self: flex-start; }
        .ai-chat-msg.user { background: #1d4ed8; color: #fff; align-self: flex-end; }

        .ai-chatbot-input-wrap {
            border-top: 1px solid #e5e7eb;
            padding: 10px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
            background: #fff;
        }

        .ai-chatbot-input-wrap input {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
        }

        .ai-chatbot-input-wrap button {
            border: none;
            border-radius: 8px;
            background: #1d4ed8;
            color: #fff;
            padding: 0 14px;
        }

        .ai-chatbot-quick {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding: 0 12px 10px;
            background: #f9fbff;
        }

        .ai-chatbot-quick button {
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 999px;
            font-size: 12px;
            padding: 6px 10px;
        }
    `;
    document.head.appendChild(style);

    const icon = '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';

    const toggle = document.createElement('button');
    toggle.className = 'ai-chatbot-toggle';
    toggle.setAttribute('aria-label', 'AI sohbet asistanını aç');
    toggle.innerHTML = icon;

    const panel = document.createElement('section');
    panel.className = 'ai-chatbot-panel';
    panel.setAttribute('aria-live', 'polite');

    panel.innerHTML = `
        <div class="ai-chatbot-header">
            <h6>AI Sosyal Hak Asistanı</h6>
            <small>SGK • Sosyal haklar • Başvuru süreçleri</small>
        </div>
        <p class="ai-chatbot-kvkk">
            KVKK Bilgilendirme: Bu sohbet aracı kişisel veri paylaşmadan genel bilgilendirme sağlar.
            TC kimlik numarası, telefon, adres ve sağlık verisi gibi hassas bilgileri yazmayın.
        </p>
        <div class="ai-chatbot-body" id="aiChatBody"></div>
        <div class="ai-chatbot-quick">
            <button type="button" data-q="SGK engelli emeklilik şartları">SGK emeklilik</button>
            <button type="button" data-q="evde bakım maaşı başvuru">Evde bakım maaşı</button>
            <button type="button" data-q="çözger raporu">ÇÖZGER</button>
            <button type="button" data-q="gelir testi nasıl hesaplanır">Gelir testi</button>
        </div>
        <form class="ai-chatbot-input-wrap" id="aiChatForm">
            <input id="aiChatInput" type="text" placeholder="Sorunuzu yazın..." autocomplete="off" required>
            <button type="submit">Gönder</button>
        </form>
    `;

    document.body.appendChild(toggle);
    document.body.appendChild(panel);

    const body = panel.querySelector('#aiChatBody');
    const form = panel.querySelector('#aiChatForm');
    const input = panel.querySelector('#aiChatInput');

    function addMessage(text, role) {
        const item = document.createElement('div');
        item.className = `ai-chat-msg ${role}`;
        item.textContent = text;
        body.appendChild(item);
        body.scrollTop = body.scrollHeight;
    }

    function answerFor(questionRaw) {
        const q = questionRaw.toLocaleLowerCase('tr-TR');

        if (q.includes('sgk') || q.includes('emeklilik') || q.includes('prim')) {
            return 'SGK engelli emeklilikte; engellilik oranı, sigorta başlangıç tarihi, prim günü ve sigortalılık süresi birlikte değerlendirilir. Ön değerlendirme için hesaplama araçlarını kullanabilir, resmi sonuç için SGK il müdürlüğüne başvurabilirsiniz.';
        }

        if (q.includes('evde bakım') || q.includes('bakım maaşı')) {
            return 'Evde bakım maaşı başvurusunda gelir kriteri + bakıma muhtaçlık kriteri birlikte incelenir. Hane kişi başı gelir hesabını hesaplama aracından yapabilirsiniz. Kesin karar Aile ve Sosyal Hizmetler birimlerince verilir.';
        }

        if (q.includes('çözger') || q.includes('cozger')) {
            return 'ÇÖZGER, 18 yaş altı çocuklar için düzenlenen özel gereksinim raporudur. Özel eğitim, rehabilitasyon ve sosyal destek başvurularında kullanılabilir. Yetkili hastane kurulu tarafından hazırlanır.';
        }

        if (q.includes('engel') || q.includes('rapor')) {
            return 'Engelli sağlık kurulu raporu için yetkili hastaneye başvuru gerekir. Branş muayeneleri sonrası kurul oran belirler. Orana itirazlar il sağlık müdürlüğü kanalıyla yapılabilir.';
        }

        if (q.includes('gelir testi') || q.includes('asgari') || q.includes('hesapla')) {
            return '2026 için asgari ücret 20.002 TL baz alınarak gelir testi hesaplanır. Hanenin aylık net geliri kişi sayısına bölünür. Eşik ve özel durum etkisi hesaplama ekranında ayrıntılı gösterilir.';
        }

        if (q.includes('başvuru') || q.includes('belge') || q.includes('evrak')) {
            return 'Başvurularda kimlik, gelir durumu, hane bilgileri ve destek türüne göre sağlık/rapor belgeleri istenir. Kurumlar ek belge talep edebilir. Başvuru öncesi güncel belge listesini kurumdan teyit etmeniz önerilir.';
        }

        return 'Sorunuzu anladım. Bu konuda genel bilgi verebilirim: süreç, gelir kriteri ve gerekli belgeleri adım adım açıklayabilirim. Daha net yardımcı olmam için başlık belirtin (örn: SGK emeklilik, evde bakım maaşı, ÇÖZGER, gelir testi).';
    }

    toggle.addEventListener('click', function () {
        panel.classList.toggle('open');
        if (panel.classList.contains('open') && body.childElementCount === 0) {
            addMessage('Merhaba 👋 Ben AI Sosyal Hak Asistanı. Sosyal haklar, SGK işlemleri ve başvuru süreçleri hakkında genel bilgi sunabilirim.', 'bot');
            addMessage('KVKK notu: Kişisel/hassas veri paylaşmadan devam edelim.', 'bot');
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const question = input.value.trim();
        if (!question) return;

        addMessage(question, 'user');
        input.value = '';

        setTimeout(function () {
            addMessage(answerFor(question), 'bot');
        }, 220);
    });

    panel.querySelectorAll('.ai-chatbot-quick button').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const q = btn.getAttribute('data-q') || '';
            input.value = q;
            form.dispatchEvent(new Event('submit', { cancelable: true }));
        });
    });
})();
