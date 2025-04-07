const staticCacheName = 'Autoapp-v1';
const PRECACHE = 'precache-v1';
const RUNTIME = 'runtime';


// Pre Caching Assets
const PRECACHE_URLS = [
    '/',
    'PWA/css/bootstrap.min.css',
    'PWA/css/animate.css',
    'PWA/css/font-awesome.min.css',
    'PWA/css/owl.carousel.min.css',
    'PWA/fonts/Fateh/IRANSans-Medium.eot',
    'PWA/fonts/fontawesome-webfont.svg',
    'PWA/fonts/Fateh/IRANSans-Medium.ttf',
    'PWA/fonts/Fateh/IRANSans-Medium.woff',
    'PWA/fonts/Fateh/IRANSans-Medium.woff2',
    'PWA/fonts/Fateh/FontAwesome.otf',
    'PWA/img/core-img/dot-blue.png',
    'PWA/img/core-img/dot.png',
    'PWA/img/core-img/logo-autoapp.png',
    'PWA/img/core-img/logo-autoapp.png',
    'PWA/img/core-img/favicon.ico',
    'PWA/js/default/active.js',
    'PWA/js/default/dark-mode-switch.js',
    'PWA/js/bootstrap.bundle.min.js',
    'PWA/js/jquery.min.js',
    'PWA/js/owl.carousel.min.js',
    'PWA/js/pwa.js',
    'PWA/style.css'
];

// install event
self.addEventListener('install', evt => {
    evt.waitUntil(
        caches.open(staticCacheName)
        .then((cache) => {
            console.log('caching shell assets');
            cache.addAll(assets);
        })
            .then(self.skipWaiting())
    );
});

// activate event
self.addEventListener('activate', evt => {
    evt.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(keys
                .filter(key => key !== staticCacheName)
                .map(key => caches.delete(key))
            );
        })
    );
});
// When we change the name we could have multiple cache, to avoid that we need to delet the old cache, so with this function we check the key that is our cache naming, if it is different from the actual naming we delete it, in this way we will always have only the last updated cache.
// fetch event
self.addEventListener('fetch', event => {
    // Skip cross-origin requests, like those for Google Analytics.
    if (event.request.url.startsWith(self.location.origin)) {
        event.respondWith(
            caches.match(event.request).then(cachedResponse => {
                if (cachedResponse) {
                    return cachedResponse;
                }

                return caches.open(RUNTIME).then(cache => {
                    return fetch(event.request).then(response => {
                        // Put a copy of the response in the runtime cache.
                        return cache.put(event.request, response.clone()).then(() => {
                            return response;
                        });
                    });
                });
            })
        );
    }
});