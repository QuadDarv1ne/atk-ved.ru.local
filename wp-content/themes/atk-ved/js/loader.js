/**
 * Оптимизированный загрузчик скриптов
 * Загружает скрипты по требованию
 */

(function() {
    'use strict';
    
    // Проверка поддержки IntersectionObserver
    const supportsIntersectionObserver = 'IntersectionObserver' in window;
    
    // Lazy load для скриптов
    function loadScript(src, callback) {
        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        
        if (callback) {
            script.onload = callback;
        }
        
        document.body.appendChild(script);
    }
    
    // Загрузка скриптов при скролле к секции
    function initLazyScripts() {
        const sections = {
            '.calculator-section': 'calculator',
            '.tracking-section': 'tracking',
            '.gallery-section': 'gallery',
            '.statistics-section': 'statistics'
        };
        
        if (supportsIntersectionObserver) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const section = entry.target;
                        const scriptName = section.dataset.script;
                        
                        if (scriptName && !section.dataset.loaded) {
                            section.dataset.loaded = 'true';
                            // Скрипт уже загружен через wp_enqueue_script
                            observer.unobserve(section);
                        }
                    }
                });
            }, {
                rootMargin: '200px'
            });
            
            // Наблюдаем за секциями
            Object.keys(sections).forEach(function(selector) {
                const element = document.querySelector(selector);
                if (element) {
                    element.dataset.script = sections[selector];
                    observer.observe(element);
                }
            });
        }
    }
    
    // Оптимизация изображений
    function optimizeImages() {
        const images = document.querySelectorAll('img[loading="lazy"]');
        
        if (!supportsIntersectionObserver) {
            // Fallback для старых браузеров
            images.forEach(function(img) {
                img.loading = 'eager';
            });
            return;
        }
        
        const imageObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    // Добавляем класс для анимации появления
                    img.addEventListener('load', function() {
                        img.classList.add('loaded');
                    });
                    
                    imageObserver.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px'
        });
        
        images.forEach(function(img) {
            imageObserver.observe(img);
        });
    }
    
    // Предзагрузка критических изображений
    function preloadCriticalImages() {
        const criticalImages = [
            document.querySelector('.hero-image img'),
            document.querySelector('.logo-link img')
        ];
        
        criticalImages.forEach(function(img) {
            if (img && img.src) {
                const link = document.createElement('link');
                link.rel = 'preload';
                link.as = 'image';
                link.href = img.src;
                document.head.appendChild(link);
            }
        });
    }
    
    // Отложенная загрузка CSS
    function loadDeferredCSS() {
        const deferredStyles = document.querySelectorAll('link[rel="preload"][as="style"]');
        
        deferredStyles.forEach(function(link) {
            link.onload = function() {
                this.rel = 'stylesheet';
            };
        });
    }
    
    // Оптимизация производительности
    function optimizePerformance() {
        // Отключаем hover эффекты на touch устройствах
        if ('ontouchstart' in window) {
            document.body.classList.add('touch-device');
        }
        
        // Debounce для resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            document.body.classList.add('resize-active');
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                document.body.classList.remove('resize-active');
            }, 400);
        });
        
        // Отключаем анимации при скролле для производительности
        let scrollTimer;
        window.addEventListener('scroll', function() {
            clearTimeout(scrollTimer);
            if (!document.body.classList.contains('disable-hover')) {
                document.body.classList.add('disable-hover');
            }
            scrollTimer = setTimeout(function() {
                document.body.classList.remove('disable-hover');
            }, 200);
        }, { passive: true });
    }
    
    // Инициализация при загрузке DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initLazyScripts();
            optimizeImages();
            preloadCriticalImages();
            loadDeferredCSS();
            optimizePerformance();
        });
    } else {
        initLazyScripts();
        optimizeImages();
        preloadCriticalImages();
        loadDeferredCSS();
        optimizePerformance();
    }
    
})();
