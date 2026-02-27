/**
 * Продвинутый Lazy Loading с приоритетами
 * @package ATK_VED
 */

(function() {
    'use strict';

    // Конфигурация
    const config = {
        rootMargin: '50px',
        threshold: 0.01,
        loadingClass: 'is-loading',
        loadedClass: 'is-loaded',
        errorClass: 'has-error'
    };

    // Intersection Observer для изображений
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                loadImage(img);
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: config.rootMargin,
        threshold: config.threshold
    });

    // Загрузка изображения
    function loadImage(img) {
        const src = img.dataset.src;
        const srcset = img.dataset.srcset;
        
        if (!src) return;

        img.classList.add(config.loadingClass);

        const tempImg = new Image();
        
        tempImg.onload = () => {
            img.src = src;
            if (srcset) img.srcset = srcset;
            img.classList.remove(config.loadingClass);
            img.classList.add(config.loadedClass);
            
            // Анимация появления
            img.style.opacity = '0';
            requestAnimationFrame(() => {
                img.style.transition = 'opacity 0.3s ease';
                img.style.opacity = '1';
            });
        };

        tempImg.onerror = () => {
            img.classList.remove(config.loadingClass);
            img.classList.add(config.errorClass);
            console.error('Failed to load image:', src);
        };

        tempImg.src = src;
        if (srcset) tempImg.srcset = srcset;
    }

    // Lazy loading для iframe (видео, карты)
    const iframeObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const iframe = entry.target;
                const src = iframe.dataset.src;
                
                if (src) {
                    iframe.src = src;
                    iframe.classList.add(config.loadedClass);
                }
                
                observer.unobserve(iframe);
            }
        });
    }, {
        rootMargin: '100px',
        threshold: 0.01
    });

    // Приоритетная загрузка для критических изображений
    function loadCriticalImages() {
        const criticalImages = document.querySelectorAll('img[data-priority="high"]');
        criticalImages.forEach(img => {
            if (img.dataset.src) {
                loadImage(img);
            }
        });
    }

    // Предзагрузка следующих изображений
    function preloadNextImages() {
        const images = document.querySelectorAll('img[data-src]:not(.is-loaded)');
        const visibleImages = Array.from(images).slice(0, 3);
        
        visibleImages.forEach(img => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = img.dataset.src;
            document.head.appendChild(link);
        });
    }

    // Адаптивная загрузка на основе скорости соединения
    function getLoadingStrategy() {
        if ('connection' in navigator) {
            const connection = navigator.connection;
            const effectiveType = connection.effectiveType;
            
            // Медленное соединение - загружаем только видимое
            if (effectiveType === 'slow-2g' || effectiveType === '2g') {
                return { rootMargin: '0px', aggressive: false };
            }
            
            // Быстрое соединение - агрессивная предзагрузка
            if (effectiveType === '4g') {
                return { rootMargin: '200px', aggressive: true };
            }
        }
        
        return { rootMargin: '50px', aggressive: false };
    }

    // Инициализация
    function init() {
        // Загружаем критические изображения сразу
        loadCriticalImages();

        // Получаем стратегию загрузки
        const strategy = getLoadingStrategy();

        // Наблюдаем за изображениями
        const lazyImages = document.querySelectorAll('img[data-src]:not([data-priority="high"])');
        lazyImages.forEach(img => imageObserver.observe(img));

        // Наблюдаем за iframe
        const lazyIframes = document.querySelectorAll('iframe[data-src]');
        lazyIframes.forEach(iframe => iframeObserver.observe(iframe));

        // Предзагрузка при агрессивной стратегии
        if (strategy.aggressive) {
            setTimeout(preloadNextImages, 1000);
        }

        // Обработка динамически добавленных элементов
        const mutationObserver = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) {
                        const images = node.querySelectorAll('img[data-src]');
                        images.forEach(img => imageObserver.observe(img));
                        
                        const iframes = node.querySelectorAll('iframe[data-src]');
                        iframes.forEach(iframe => iframeObserver.observe(iframe));
                    }
                });
            });
        });

        mutationObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Запуск после загрузки DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Экспорт для использования в других скриптах
    window.ATKLazyLoad = {
        loadImage,
        preloadNextImages
    };

})();
