/**
 * Service Worker Registration
 * @package ATK_VED
 */
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/wp-content/themes/atk-ved/sw.js')
            .then(reg => {
                if (window.WP_DEBUG) console.log('SW registered');
                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            if (confirm('Доступна новая версия. Обновить?')) {
                                newWorker.postMessage({action: 'skipWaiting'});
                                window.location.reload();
                            }
                        }
                    });
                });
            })
            .catch(err => {
                if (window.WP_DEBUG) console.log('SW registration failed:', err);
            });

        let refreshing;
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (refreshing) return;
            refreshing = true;
            window.location.reload();
        });
    });
}
