// Service Worker for Health Center Queue System PWA
// CORRECT VERSION - Only caches static assets, not Laravel routes

const CACHE_NAME = 'health-queue-v2';
const STATIC_CACHE_NAME = 'health-queue-static-v2';

// Only cache truly static assets - NO Laravel routes
const staticAssets = [
    '/css/app.css',
    '/images/icon-192x192.png',
    '/images/icon-512x512.png',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'
];

// Install event - cache only static assets
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Installing...');
    event.waitUntil(
        caches.open(STATIC_CACHE_NAME)
            .then((cache) => {
                console.log('[Service Worker] Caching static assets');
                return cache.addAll(staticAssets.map(url => new Request(url, {
                    credentials: 'same-origin',
                    mode: url.startsWith('http') ? 'cors' : 'same-origin'
                })))
                    .catch(err => {
                        console.error('[Service Worker] Failed to cache:', err);
                    });
            })
    );
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activating...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== STATIC_CACHE_NAME && cacheName !== CACHE_NAME) {
                        console.log('[Service Worker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch event - NETWORK FIRST for Laravel routes, CACHE FIRST for static assets
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip cross-origin requests except CDN assets
    if (url.origin !== location.origin && !url.href.includes('cdnjs.cloudflare.com')) {
        return;
    }

    // Skip POST, PUT, DELETE requests - let them go to network
    if (request.method !== 'GET') {
        return;
    }

    // Check if it's a static asset
    const isStaticAsset =
        request.url.includes('/css/') ||
        request.url.includes('/js/') ||
        request.url.includes('/images/') ||
        request.url.includes('/fonts/') ||
        request.url.includes('cdnjs.cloudflare.com');

    if (isStaticAsset) {
        // CACHE FIRST for static assets
        event.respondWith(
            caches.match(request)
                .then((cachedResponse) => {
                    if (cachedResponse) {
                        console.log('[Service Worker] Cache hit:', request.url);
                        return cachedResponse;
                    }

                    // Not in cache, fetch from network and cache it
                    return fetch(request).then((networkResponse) => {
                        // Clone the response before caching
                        const responseToCache = networkResponse.clone();

                        caches.open(STATIC_CACHE_NAME).then((cache) => {
                            cache.put(request, responseToCache);
                        });

                        return networkResponse;
                    });
                })
                .catch(() => {
                    console.log('[Service Worker] Fetch failed for:', request.url);
                })
        );
    } else {
        // NETWORK FIRST for Laravel routes (HTML pages)
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    // Always return fresh content for Laravel routes
                    console.log('[Service Worker] Network response:', request.url);
                    return networkResponse;
                })
                .catch((error) => {
                    console.log('[Service Worker] Network failed, showing offline page:', error);

                    // If offline and it's a navigation request, show offline page
                    if (request.mode === 'navigate') {
                        return caches.match('/offline.html').then((response) => {
                            return response || new Response('Offline - Please check your connection', {
                                status: 503,
                                statusText: 'Service Unavailable',
                                headers: new Headers({
                                    'Content-Type': 'text/plain'
                                })
                            });
                        });
                    }

                    return new Response('Network error', {
                        status: 408,
                        headers: { 'Content-Type': 'text/plain' }
                    });
                })
        );
    }
});

// Handle messages from the client
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }

    if (event.data && event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(
            caches.keys().then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => caches.delete(cacheName))
                );
            })
        );
    }
});
