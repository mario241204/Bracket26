const CACHE_NAME = 'bracket26-v1';
const ASSETS = [
  'index.php',
  'public/css/style.css',
  'public/js/app.js',
  'manifest.json'
];

// Instalación: Cacheamos los assets críticos
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('[SW] Cacheando assets base...');
      return cache.addAll(ASSETS);
    })
  );
});

// Activación: Limpieza de cachés antiguas
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    })
  );
});

// Estrategia: Cache First para assets, Network First para la API si fuera necesario
// Por ahora, Cache First simplificado para cumplir el requerimiento
self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      return cachedResponse || fetch(event.request);
    })
  );
});
