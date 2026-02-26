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
    if (event.tag === 'contact-form-sync') {
        event.waitUntil(syncContactForm());
    }
    if (event.tag === 'newsletter-subscribe-sync') {
        event.waitUntil(syncNewsletterSubscription());
    }
});

// Синхронизация контактной формы
async function syncContactForm() {
    try {
        // Открываем IndexedDB или другое хранилище для получения данных формы
        const formData = await getQueuedFormData('contact_forms');
        
        for (const data of formData) {
            try {
                const response = await fetch('/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: data.payload
                });
                
                if (response.ok) {
                    // Удаляем успешно отправленную форму из очереди
                    await removeQueuedForm('contact_forms', data.id);
                    console.log('Contact form synced successfully');
                }
            } catch (error) {
                console.error('Failed to sync contact form:', error);
                // Оставляем в очереди для следующей попытки
            }
        }
    } catch (error) {
        console.error('Error syncing contact forms:', error);
    }
}

// Синхронизация подписки на рассылку
async function syncNewsletterSubscription() {
    try {
        const subscriptions = await getQueuedFormData('newsletter_subscriptions');
        
        for (const sub of subscriptions) {
            try {
                const response = await fetch('/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: sub.payload
                });
                
                if (response.ok) {
                    await removeQueuedForm('newsletter_subscriptions', sub.id);
                    console.log('Newsletter subscription synced successfully');
                }
            } catch (error) {
                console.error('Failed to sync newsletter subscription:', error);
            }
        }
    } catch (error) {
        console.error('Error syncing newsletter subscriptions:', error);
    }
}

// Функция для получения данных из очереди (заглушка - должна быть реализована с использованием IndexedDB или другого хранилища)
async function getQueuedFormData(type) {
    // В реальной реализации это будет использовать IndexedDB
    // или другой механизм хранения данных формы
    return [];
}

// Функция для удаления данных из очереди (заглушка)
async function removeQueuedForm(type, id) {
    // В реальной реализации это будет использовать IndexedDB
    // или другой механизм хранения данных формы
    console.log(`Removed ${type} with id ${id}`);
}

// Обработка события fetch для кэширования API-запросов
self.addEventListener('fetch', event => {
    // Если это AJAX-запрос для формы
    if (event.request.url.includes('/wp-admin/admin-ajax.php') && event.request.method === 'POST') {
        event.respondWith(
            handleFormSubmission(event.request)
        );
        return;
    }
    
    // Существующая логика для обычных запросов
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

// Обработка отправки формы
async function handleFormSubmission(request) {
    const clonedRequest = request.clone();
    
    try {
        // Пробуем отправить запрос напрямую
        const response = await fetch(request);
        
        // Если запрос успешен, возвращаем ответ
        if (response.ok) {
            return response;
        }
    } catch (error) {
        // Если произошла ошибка (например, нет интернета),
        // сохраняем данные формы в очередь для последующей синхронизации
        console.log('Network error, queuing form submission:', error);
        
        const payload = await clonedRequest.text();
        await queueFormData(clonedRequest.url, payload);
        
        // Запланировать фоновую синхронизацию
        await self.registration.sync.register('contact-form-sync');
        
        // Возвращаем ответ, указывающий, что форма добавлена в очередь
        return new Response(JSON.stringify({queued: true, message: 'Form queued for submission'}), {
            headers: {'Content-Type': 'application/json'}
        });
    }
}

// Функция для добавления данных формы в очередь (заглушка)
async function queueFormData(url, payload) {
    // В реальной реализации это будет использовать IndexedDB
    // или другой механизм хранения данных формы
    console.log('Form data queued for sync:', payload);
    
    // Сохраняем в localStorage как временный вариант
    const queuedForms = JSON.parse(localStorage.getItem('queued_forms') || '[]');
    queuedForms.push({
        id: Date.now(),
        url: url,
        payload: payload,
        timestamp: Date.now()
    });
    localStorage.setItem('queued_forms', JSON.stringify(queuedForms));
}
