// Cookie Consent Management System
// Sosyal Hizmet Rehberlik & Danışmanlık

// Cookie yönetimi fonksiyonları
const CookieConsent = {
    // Cookie oluştur
    setCookie: function(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/;SameSite=Lax';
    },

    // Cookie oku
    getCookie: function(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    },

    // Cookie sil
    deleteCookie: function(name) {
        document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/';
    },

    // Çerez tercihlerini kaydet
    savePreferences: function(preferences) {
        const consentData = {
            necessary: true, // Her zaman true
            performance: preferences.performance || false,
            functional: preferences.functional || false,
            marketing: preferences.marketing || false,
            timestamp: new Date().getTime()
        };
        
        this.setCookie('cookie_consent', JSON.stringify(consentData), 365);
        return consentData;
    },

    // Kaydedilmiş tercihleri al
    getPreferences: function() {
        const consent = this.getCookie('cookie_consent');
        if (consent) {
            try {
                return JSON.parse(consent);
            } catch (e) {
                return null;
            }
        }
        return null;
    },

    // Banner'ı göster
    showBanner: function() {
        const banner = document.getElementById('cookieConsentBanner');
        if (banner) {
            banner.style.display = 'block';
            setTimeout(() => {
                banner.classList.add('show');
            }, 100);
        }
    },

    // Banner'ı gizle
    hideBanner: function() {
        const banner = document.getElementById('cookieConsentBanner');
        if (banner) {
            banner.classList.remove('show');
            setTimeout(() => {
                banner.style.display = 'none';
            }, 300);
        }
    },

    // Çerezleri yükle (tercih edilen kategoriler için)
    loadCookies: function(preferences) {
        // Google Analytics
        if (preferences.performance) {
            this.loadGoogleAnalytics();
        }

        // Facebook Pixel
        if (preferences.marketing) {
            this.loadFacebookPixel();
        }

        // Diğer üçüncü taraf scriptler buraya eklenebilir
    },

    // Google Analytics yükle
    loadGoogleAnalytics: function() {
        // Google Analytics kodunuz varsa buraya ekleyin
        // Örnek:
        /*
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-XXXXX-Y', 'auto');
        ga('send', 'pageview');
        */
    },

    // Facebook Pixel yükle
    loadFacebookPixel: function() {
        // Facebook Pixel kodunuz varsa buraya ekleyin
        // Örnek:
        /*
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', 'YOUR_PIXEL_ID');
        fbq('track', 'PageView');
        */
    },

    // İsteğe bağlı çerezleri temizle
    clearOptionalCookies: function() {
        // Google Analytics çerezlerini temizle
        this.deleteCookie('_ga');
        this.deleteCookie('_gid');
        this.deleteCookie('_gat');
        this.deleteCookie('_gat_gtag_UA_XXXXX_Y');

        // Facebook çerezlerini temizle
        this.deleteCookie('_fbp');
        this.deleteCookie('fr');

        // Diğer üçüncü taraf çerezleri buraya ekleyin
    }
};

// Tüm çerezleri kabul et
function acceptAllCookies() {
    const preferences = {
        performance: true,
        functional: true,
        marketing: true
    };
    
    const consent = CookieConsent.savePreferences(preferences);
    CookieConsent.loadCookies(consent);
    CookieConsent.hideBanner();
    closeCookieSettings();
    
    // Kullanıcıya bildirim göster
    showNotification('Tüm çerezler kabul edildi', 'success');
}

// İsteğe bağlı çerezleri reddet
function rejectOptionalCookies() {
    const preferences = {
        performance: false,
        functional: false,
        marketing: false
    };
    
    CookieConsent.savePreferences(preferences);
    CookieConsent.clearOptionalCookies();
    CookieConsent.hideBanner();
    closeCookieSettings();
    
    // Kullanıcıya bildirim göster
    showNotification('Sadece zorunlu çerezler kullanılacak', 'info');
}

// Çerez ayarlarını aç
function openCookieSettings() {
    CookieConsent.hideBanner();
    const modal = document.getElementById('cookieSettingsModal');
    if (modal) {
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
        
        // Mevcut tercihleri yükle
        const preferences = CookieConsent.getPreferences();
        if (preferences) {
            document.getElementById('performanceCookies').checked = preferences.performance;
            document.getElementById('functionalCookies').checked = preferences.functional;
            document.getElementById('marketingCookies').checked = preferences.marketing;
        }
    }
}

// Çerez ayarlarını kapat
function closeCookieSettings() {
    const modal = document.getElementById('cookieSettingsModal');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }
}

// Çerez ayarlarını kaydet
function saveCookieSettings() {
    const preferences = {
        performance: document.getElementById('performanceCookies').checked,
        functional: document.getElementById('functionalCookies').checked,
        marketing: document.getElementById('marketingCookies').checked
    };
    
    const consent = CookieConsent.savePreferences(preferences);
    
    // Kabul edilmeyen çerezleri temizle
    if (!preferences.performance || !preferences.marketing) {
        CookieConsent.clearOptionalCookies();
    }
    
    // Kabul edilen çerezleri yükle
    CookieConsent.loadCookies(consent);
    
    closeCookieSettings();
    
    // Kullanıcıya bildirim göster
    showNotification('Çerez tercihleri kaydedildi', 'success');
}

// Bildirim göster
function showNotification(message, type = 'info') {
    // Bootstrap toast veya basit bir bildirim sistemi
    const notification = document.createElement('div');
    notification.className = `cookie-notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
        ${message}
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Modal dışına tıklandığında kapat
window.onclick = function(event) {
    const modal = document.getElementById('cookieSettingsModal');
    if (event.target == modal) {
        closeCookieSettings();
    }
}

// Sayfa yüklendiğinde kontrol et
document.addEventListener('DOMContentLoaded', function() {
    const preferences = CookieConsent.getPreferences();
    
    if (!preferences) {
        // İlk ziyaret - banner'ı 1 saniye sonra göster
        setTimeout(() => {
            CookieConsent.showBanner();
        }, 1000);
    } else {
        // Daha önce tercih yapılmış - çerezleri yükle
        CookieConsent.loadCookies(preferences);
    }
});

// Global fonksiyon - Çerez politikası sayfasından çağrılabilir
window.openCookieSettings = openCookieSettings;
