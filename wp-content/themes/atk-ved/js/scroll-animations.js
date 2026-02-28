/**
 * Scroll Animations with Intersection Observer
 * 
 * @package ATK_VED
 * @since 3.6.0
 */

(function() {
    'use strict';

    /**
     * Конфигурация анимаций
     */
    const config = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px',
        animationDelay: 100, // мс между элементами
    };

    /**
     * Типы анимаций
     */
    const animationTypes = {
        'fade-up': 'animate-fade-up',
        'fade-down': 'animate-fade-down',
        'fade-left': 'animate-fade-left',
        'fade-right': 'animate-fade-right',
        'zoom-in': 'animate-zoom-in',
        'zoom-out': 'animate-zoom-out',
        'flip': 'animate-flip',
        'slide-up': 'animate-slide-up',
        'bounce': 'animate-bounce-in',
    };

    /**
     * Intersection Observer для анимаций при скролле
     */
    const observerOptions = {
        threshold: config.threshold,
        rootMargin: config.rootMargin,
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                // Добавляем задержку для последовательной анимации
                const delay = entry.target.dataset.animationDelay || 
                             (index * config.animationDelay);
                
                setTimeout(() => {
                    entry.target.classList.add('animate-in');
                    
                    // Триггерим кастомное событие
                    entry.target.dispatchEvent(new CustomEvent('animated', {
                        detail: { element: entry.target }
                    }));
                }, delay);

                // Отключаем наблюдение после анимации (опционально)
                if (!entry.target.dataset.animationRepeat) {
                    observer.unobserve(entry.target);
                }
            } else if (entry.target.dataset.animationRepeat) {
                // Если нужно повторять анимацию
                entry.target.classList.remove('animate-in');
            }
        });
    }, observerOptions);

    /**
     * Инициализация анимаций
     */
    function initScrollAnimations() {
        // Находим все элементы с data-animate
        const animatedElements = document.querySelectorAll('[data-animate]');

        animatedElements.forEach((element, index) => {
            const animationType = element.dataset.animate;
            const animationClass = animationTypes[animationType] || animationTypes['fade-up'];

            // Добавляем базовый класс и класс типа анимации
            element.classList.add('animate-on-scroll', animationClass);

            // Устанавливаем задержку если указана
            if (element.dataset.animationDelay) {
                element.style.setProperty('--animation-delay', element.dataset.animationDelay + 'ms');
            }

            // Устанавливаем длительность если указана
            if (element.dataset.animationDuration) {
                element.style.setProperty('--animation-duration', element.dataset.animationDuration + 'ms');
            }

            // Начинаем наблюдение
            observer.observe(element);
        });

        console.log(`✨ Initialized ${animatedElements.length} scroll animations`);
    }

    /**
     * Анимация счетчиков
     */
    function animateCounters() {
        const counters = document.querySelectorAll('[data-counter]');

        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.dataset.counter);
                    const duration = parseInt(counter.dataset.counterDuration) || 2000;
                    const increment = target / (duration / 16); // 60fps

                    let current = 0;

                    const updateCounter = () => {
                        current += increment;
                        if (current < target) {
                            counter.textContent = Math.floor(current);
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.textContent = target;
                        }
                    };

                    updateCounter();
                    counterObserver.unobserve(counter);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => counterObserver.observe(counter));
    }

    /**
     * Параллакс эффект для изображений
     */
    function initParallax() {
        const parallaxElements = document.querySelectorAll('[data-parallax]');

        if (parallaxElements.length === 0) return;

        const handleScroll = () => {
            const scrolled = window.pageYOffset;

            parallaxElements.forEach(element => {
                const speed = parseFloat(element.dataset.parallax) || 0.5;
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        };

        // Используем requestAnimationFrame для плавности
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    /**
     * Анимация прогресс-баров
     */
    function animateProgressBars() {
        const progressBars = document.querySelectorAll('[data-progress]');

        const progressObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const bar = entry.target;
                    const progress = parseInt(bar.dataset.progress);
                    const duration = parseInt(bar.dataset.progressDuration) || 1500;

                    bar.style.setProperty('--progress', progress + '%');
                    bar.style.setProperty('--duration', duration + 'ms');
                    bar.classList.add('animate-progress');

                    progressObserver.unobserve(bar);
                }
            });
        }, { threshold: 0.5 });

        progressBars.forEach(bar => progressObserver.observe(bar));
    }

    /**
     * Stagger анимация для списков
     */
    function initStaggerAnimation() {
        const staggerContainers = document.querySelectorAll('[data-stagger]');

        staggerContainers.forEach(container => {
            const children = container.children;
            const delay = parseInt(container.dataset.staggerDelay) || 100;

            Array.from(children).forEach((child, index) => {
                child.dataset.animate = child.dataset.animate || 'fade-up';
                child.dataset.animationDelay = (index * delay).toString();
            });
        });
    }

    /**
     * Reveal текста по буквам/словам
     */
    function initTextReveal() {
        const textElements = document.querySelectorAll('[data-text-reveal]');

        textElements.forEach(element => {
            const type = element.dataset.textReveal; // 'chars' или 'words'
            const text = element.textContent;
            element.textContent = '';

            const items = type === 'chars' ? text.split('') : text.split(' ');

            items.forEach((item, index) => {
                const span = document.createElement('span');
                span.textContent = type === 'chars' ? item : item + ' ';
                span.style.setProperty('--char-index', index.toString());
                span.classList.add('reveal-item');
                element.appendChild(span);
            });

            element.dataset.animate = 'fade-up';
        });
    }

    /**
     * Морфинг SVG иконок
     */
    function initSVGMorph() {
        const morphIcons = document.querySelectorAll('[data-morph]');

        morphIcons.forEach(icon => {
            icon.addEventListener('mouseenter', () => {
                icon.classList.add('morph-active');
            });

            icon.addEventListener('mouseleave', () => {
                icon.classList.remove('morph-active');
            });
        });
    }

    /**
     * Ripple эффект для кнопок
     */
    function initRippleEffect() {
        const rippleButtons = document.querySelectorAll('[data-ripple], .btn, button');

        rippleButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple-effect');

                this.appendChild(ripple);

                setTimeout(() => ripple.remove(), 600);
            });
        });
    }

    /**
     * Плавное появление изображений
     */
    function initImageReveal() {
        const images = document.querySelectorAll('img[data-reveal]');

        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    // Добавляем placeholder пока грузится
                    img.classList.add('image-loading');

                    img.addEventListener('load', () => {
                        img.classList.remove('image-loading');
                        img.classList.add('image-loaded');
                    });

                    // Если изображение уже загружено
                    if (img.complete) {
                        img.classList.remove('image-loading');
                        img.classList.add('image-loaded');
                    }

                    imageObserver.unobserve(img);
                }
            });
        }, { threshold: 0.1 });

        images.forEach(img => imageObserver.observe(img));
    }

    /**
     * Инициализация при загрузке DOM
     */
    function init() {
        // Проверяем поддержку Intersection Observer
        if (!('IntersectionObserver' in window)) {
            console.warn('Intersection Observer not supported. Animations disabled.');
            // Показываем все элементы без анимации
            document.querySelectorAll('[data-animate]').forEach(el => {
                el.classList.add('animate-in');
            });
            return;
        }

        // Инициализируем все анимации
        initStaggerAnimation();
        initTextReveal();
        initScrollAnimations();
        animateCounters();
        animateProgressBars();
        initParallax();
        initSVGMorph();
        initRippleEffect();
        initImageReveal();

        // Добавляем класс для CSS
        document.documentElement.classList.add('animations-enabled');
    }

    // Запускаем после загрузки DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Экспортируем API для использования в других скриптах
    window.ATKAnimations = {
        observer,
        reinit: init,
        animateElement: (element, type) => {
            element.dataset.animate = type;
            observer.observe(element);
        }
    };

})();
