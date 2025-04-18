// Template Name: Affan - PWA Mobile HTML Template
// Template Author: Designing World
// Template Author URL: https://themeforest.net/user/designing-world

const staticCacheName = 'precache-v1.0';
const dynamicCacheName = 'runtimeCache-v1.0';

// Pre Caching Assets
const precacheAssets = [
    '/',
    'css/bootstrap.min.css',
    'css/animate.css',
    'css/font-awesome.min.css',
    'css/owl.carousel.min.css',
    'fonts/fontawesome-webfont.eot',
    'fonts/fontawesome-webfont.svg',
    'fonts/fontawesome-webfont.ttf',
    'fonts/fontawesome-webfont.woff',
    'fonts/fontawesome-webfont.woff2',
    'fonts/FontAwesome.otf',
    'img/core-img/dot-blue.png',
    'img/core-img/dot.png',
    'img/core-img/logo.png',
    'img/core-img/logo-dark.png',
    'img/core-img/favicon.ico',
    'js/default/active.js',
    'js/default/dark-mode-switch.js',
    'js/bootstrap.bundle.min.js',
    'js/jquery.min.js',
    'js/owl.carousel.min.js',
    'js/pwa.js',
    'element-hero-blocks.html',
    'ayth.php',
    'Dashboard.php',
    'manifest.json',
    'style.css'
];

// Install Event
self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(staticCacheName).then(function (cache) {
            return cache.addAll(precacheAssets);
        })
    );
});

// Activate Event
self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(keys
                .filter(key => key !== staticCacheName && key !== dynamicCacheName)
                .map(key => caches.delete(key))
            );
        })
    );
});

// Fetch Event
self.addEventListener('fetch', function (event) {
    event.respondWith(
        caches.match(event.request).then(cacheRes => {
            return cacheRes || fetch(event.request).then(response => {
                return caches.open(dynamicCacheName).then(function (cache) {
                    cache.put(event.request, response.clone());
                    return response;
                })
            });
        }).catch(function() {
            // Fallback Page, When No Internet Connection
            return caches.match('page-fallback.html');
          })
    );
});