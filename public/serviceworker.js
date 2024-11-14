var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/offline',
    '/css/app.css',
    '/js/app.js',
];

self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
      caches.open(staticCacheName).then(cache => {
        // Create an array of promises, each for adding a file
        const cachePromises = filesToCache.map(file => {
          return fetch(file).then(response => {
            if (response.ok) {
              return cache.add(file); // Only add to cache if response is successful
            } else {
              //console.error(`Failed to fetch ${file}: ${response.status}`);
              return Promise.reject(`Failed to fetch ${file}`);
            }
          }).catch(error => {
            //console.error('Error caching file:', file, error);
            // Return a resolved promise for files that fail, so it doesn't block others
            return Promise.resolve();
          });
        });
  
        // Wait until all files are processed
        return Promise.all(cachePromises);
      })
    );
  });
  

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    )
});
