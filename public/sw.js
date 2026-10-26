/* SIGAP Service Worker — network-first, aman untuk konten dinamis */
const CACHE = 'sigap-v1';
const STATIC_ASSETS = [
  '/icons/icon-192.png',
  '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE).then((cache) => cache.addAll(STATIC_ASSETS)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const { request } = event;
  // Hanya GET, dan jangan cache API / halaman dinamis — selalu network-first
  if (request.method !== 'GET') return;
  event.respondWith(
    fetch(request)
      .then((response) => {
        // Cache hanya aset statis (icons)
        if (request.url.includes('/icons/') && response.ok) {
          const clone = response.clone();
          caches.open(CACHE).then((cache) => cache.put(request, clone));
        }
        return response;
      })
      .catch(() => caches.match(request))
  );
});
