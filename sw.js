const CACHE_NAME = 'dijital-sosyal-hak-v1';
const OFFLINE_URL = '/offline.html';
const PRECACHE_URLS = [
  '/',
  '/style.css',
  '/script.js',
  '/manifest.json',
  '/icons/icon.svg',
  OFFLINE_URL
];

self.addEventListener('install', (event) => {
  event.waitUntil(caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS)));
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))))
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  event.respondWith(
    caches.match(event.request).then((cached) => {
      if (cached) return cached;

      return fetch(event.request)
        .then((response) => {
          const copy = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, copy));
          return response;
        })
        .catch(() => caches.match(OFFLINE_URL));
    })
  );
});

self.addEventListener('message', (event) => {
  if (event.data?.type === 'LOCAL_NOTIFY') {
    const lang = event.data.lang || 'tr';
    const bodyByLang = {
      tr: 'Bildirimler başarıyla etkinleştirildi.',
      ku: 'Agahdarî bi serkeftî hatin çalak kirin.',
      ar: 'تم تفعيل الإشعارات بنجاح.'
    };

    self.registration.showNotification('Dijital Sosyal Hak Platformu', {
      body: bodyByLang[lang] || bodyByLang.tr,
      icon: '/icons/icon.svg'
    });
  }
});
