const CACHE_NAME = 'kiosco-pos-v2';
const STATIC_ASSETS = [
    '/dashboard',
    '/ventas/create',
    '/css/app.css',
    '/js/app.js',
    '/js/offline-manager.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css'
];

// Instalación: Cachear archivos base
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

// Activación: Limpiar versiones viejas
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

// Interceptación de Peticiones (Fetch)
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // No cachear peticiones POST (Ventas)
    if (request.method !== 'GET') return;

    // Estrategia: Network-First para datos de la API, Cache-First para assets
    if (url.pathname.includes('/offline-products')) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const clonedRes = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, clonedRes));
                    return response;
                })
                .catch(() => caches.match(request))
        );
    } else {
        event.respondWith(
            caches.match(request).then((cached) => cached || fetch(request))
        );
    }
});
