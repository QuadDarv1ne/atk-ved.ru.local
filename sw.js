/**
 * Service Worker для PWA
 * 
 * @package ATK_VED
 * @since 1.8.0
 */

const CACHE_NAME = 'atk-ved-v1';
const urlsToCache = [
    '/',
    '/index.php',
    '/wp-content/themes/atk-ved/style.css',
    '/wp-content/themes/atk-ved/css/critical.css',
    '/wp-content/themes/atk-ved/js/main.js'
];

// Установка Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
            .catch(err => {
                console.log('Cache install error:', err);
            })
    );
    self.skipWaiting();
});

// Активация Service Worker
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Перехват запросов
self.addEventListener('fetch', event => {
    // Игнорируем не-GET запросы
    if (event.request.method !== 'GET') {
        return;
    }

    // Игнорируем админку WordPress
    if (event.request.url.includes('/wp-admin/')) {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Кэш найден - возвращаем его
                if (response) {
                    return response;
                }

                // Кэш не найден - делаем запрос в сеть
                return fetch(event.request)
                    .then(response => {
                        // Проверяем валидность ответа
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        // Клонируем ответ для кэширования
                        const responseToCache = response.clone();

                        caches.open(CACHE_NAME)
                            .then(cache => {
                                cache.put(event.request, responseToCache);
                            });

                        return response;
                    })
                    .catch(() => {
                        // Ошибка сети - возвращаем offline страницу
                        return caches.match('/offline.html');
                    });
            })
    );
});

// Обработка push уведомлений
self.addEventListener('push', event => {
    const data = event.data ? event.data.json() : {};
    const title = data.title || 'АТК ВЭД';
    const options = {
        body: data.body || 'Новое уведомление',
        icon: '/wp-content/themes/atk-ved/images/icons/icon-192x192.png',
        badge: '/wp-content/themes/atk-ved/images/icons/icon-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'Открыть сайт',
                icon: '/wp-content/themes/atk-ved/images/icons/icon-72x72.png'
            },
            {
                action: 'close',
                title: 'Закрыть',
                icon: '/wp-content/themes/atk-ved/images/icons/icon-72x72.png'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Обработка кликов по уведомлениям
self.addEventListener('notificationclick', event => {
    event.notification.close();

    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Фоновая синхронизация
self.addEventListener('sync', event => {
    if (event.tag === 'sync-form-data') {
        event.waitUntil(
            // Логика синхронизации данных формы
            Promise.resolve()
        );
    }
});
