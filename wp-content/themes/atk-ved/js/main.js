jQuery(document).ready(function($) {

    // Оптимизированная плавная прокрутка к якорям
    $('body').on('click', 'a[href^="#"]', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if(target.length) {
            $('html, body').stop(true, true).animate({
                scrollTop: target.offset().top - 70
            }, 800, 'swing');

            // Закрыть мобильное меню после клика
            $('.main-nav').removeClass('active');
            $('.menu-toggle').removeClass('active');
        }
    });

    // Оптимизированный аккордеон для FAQ с улучшенной анимацией
    $('body').on('click', '.faq-question', function() {
        var $item = $(this).closest('.faq-item');
        var $answer = $(this).next('.faq-answer');
        var $allItems = $('.faq-item');
        var $allAnswers = $('.faq-answer');

        if ($item.hasClass('active')) {
            $answer.slideUp(300, function() {
                $item.removeClass('active');
            });
        } else {
            $allAnswers.slideUp(300);
            $allItems.removeClass('active');
            $answer.slideDown(300, function() {
                $item.addClass('active');
            });
        }
    });

    // Липкая шапка при прокрутке с оптимизацией
    let ticking = false;
    function updateHeader() {
        var currentScroll = $(window).scrollTop();
        if (currentScroll > 50) {
            $('.site-header').addClass('scrolled');
        } else {
            $('.site-header').removeClass('scrolled');
        }
        ticking = false;
    }

    $(window).on('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateHeader);
            ticking = true;
        }
    });

    // Анимация появления элементов при прокрутке (Intersection Observer) - оптимизированная версия
    if ('IntersectionObserver' in window) {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        const elements = document.querySelectorAll('.service-card, .step-card, .review-card, .method-card');
        elements.forEach(function(el) {
            observer.observe(el);
        });
    } else {
        // Fallback для старых браузеров - оптимизированная версия
        let scrollTimeout;
        function checkVisible() {
            const windowHeight = $(window).height();
            const windowTop = $(window).scrollTop();
            const windowBottom = windowTop + windowHeight;

            $('.service-card, .step-card, .review-card, .method-card:hidden').each(function() {
                const $element = $(this);
                const elementTop = $element.offset().top;
                const elementBottom = elementTop + $element.outerHeight();

                if (elementBottom > windowTop && elementTop < windowBottom - 100) {
                    $element.addClass('visible');
                }
            });
        }

        $(window).on('scroll', function() {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(checkVisible, 50);
        });
        checkVisible();
    }

    // Мобильное меню с улучшенной анимацией
    $('.menu-toggle').on('click', function(e) {
        e.stopPropagation();
        $('.main-nav').toggleClass('active');
        $(this).toggleClass('active');
        $('body').toggleClass('menu-open');
    });

    // Закрыть меню при клике вне его
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.main-nav, .menu-toggle').length) {
            $('.main-nav').removeClass('active');
            $('.menu-toggle').removeClass('active');
            $('body').removeClass('menu-open');
        }
    });

    // Оптимизированный параллакс эффект для hero секции
    let tickingParallax = false;
    function updateParallax() {
        const scrolled = $(window).scrollTop();
        $('.hero-image img').css('transform', 'translateY(' + (scrolled * 0.3) + 'px)');
        tickingParallax = false;
    }

    $(window).on('scroll', function() {
        if (!tickingParallax) {
            requestAnimationFrame(updateParallax);
            tickingParallax = true;
        }
    });

    // Оптимизированный счетчик для статистики
    function animateCounter($elem, target) {
        $({ counter: 0 }).animate({ counter: target }, {
            duration: 2000,
            easing: 'swing',
            step: function() {
                $elem.text(Math.ceil(this.counter));
            },
            complete: function() {
                $elem.text(target);
            }
        });
    }

    // Запуск счетчика при появлении в viewport - оптимизированная версия
    let counterAnimated = false;
    $(window).on('scroll', function() {
        if (!counterAnimated && $('.hero-stats').length) {
            const $heroStats = $('.hero-stats');
            const heroTop = $heroStats.offset().top;
            const viewportBottom = $(window).scrollTop() + $(window).height();

            if (viewportBottom > heroTop) {
                counterAnimated = true;
                $('.stat-number').each(function() {
                    const $this = $(this);
                    const text = $this.text();
                    const number = parseInt(text.replace(/\D/g, ''));
                    if (number) {
                        $this.text('0');
                        animateCounter($this, number);
                    }
                });
            }
        }
    });

    // Кнопка "Наверх" (Scroll to Top) - оптимизированная версия
    const $scrollToTop = $('#scrollToTop');
    
    if ($scrollToTop.length) {
        // Показ/скрытие кнопки при прокрутке
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                $scrollToTop.stop(true, true).fadeIn(300);
            } else {
                $scrollToTop.stop(true, true).fadeOut(300);
            }
        });

        // Плавная прокрутка наверх
        $scrollToTop.on('click', function(e) {
            e.preventDefault();
            $('html, body').stop(true, true).animate({
                scrollTop: 0
            }, 600, 'swing');
        });

        // Закрытие по Escape
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $scrollToTop.is(':visible')) {
                $scrollToTop.focus();
            }
        });
    }

    // Оптимизированная обработка формы быстрого поиска
    $('body').on('submit', '#quickSearchForm', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $button = $form.find('button[type="submit"]');
        const buttonText = $button.text();

        // Валидация
        const name = $form.find('input[name="name"]').val().trim();
        const phone = $form.find('input[name="phone"]').val().trim();
        const privacy = $form.find('input[name="privacy"]').is(':checked');

        if (!name || !phone || !privacy) {
            if (typeof atkShowToast === 'function') {
                atkShowToast('Пожалуйста, заполните все поля и согласитесь с политикой конфиденциальности', 'warning');
            } else {
                alert('Пожалуйста, заполните все поля и согласитесь с политикой конфиденциальности');
            }
            return;
        }

        // Отправка данных
        $button.text('Отправка...').prop('disabled', true);

        $.ajax({
            url: atkVedData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atk_ved_quick_search',
                nonce: atkVedData.nonce,
                name: name,
                phone: phone
            },
            success: function(response) {
                if (response.success) {
                    if (typeof atkShowToast === 'function') {
                        atkShowToast('Спасибо! Ваша заявка принята. Мы свяжемся с вами в ближайшее время.', 'success', 4000);
                    } else {
                        alert('Спасибо! Ваша заявка принята. Мы свяжемся с вами в ближайшее время.');
                    }
                    $form[0].reset();
                } else {
                    if (typeof atkShowToast === 'function') {
                        atkShowToast('Произошла ошибка. Пожалуйста, попробуйте позже или свяжитесь с нами по телефону.', 'error');
                    } else {
                        alert('Произошла ошибка. Пожалуйста, попробуйте позже или свяжитесь с нами по телефону.');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                if (typeof atkShowToast === 'function') {
                    atkShowToast('Ошибка соединения. Попробуйте позже.', 'error');
                } else {
                    alert('Произошла ошибка. Пожалуйста, попробуйте позже или свяжитесь с нами по телефону.');
                }
            },
            complete: function() {
                $button.text(buttonText).prop('disabled', false);
            }
        });
    });

    // Оптимизированная обработка формы обратной связи
    $('body').on('submit', '#contactForm', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $button = $form.find('button[type="submit"]');
        const buttonText = $button.text();

        // Валидация
        const name = $form.find('input[name="name"]').val().trim();
        const email = $form.find('input[name="email"]').val().trim();
        const phone = $form.find('input[name="phone"]').val().trim();
        const message = $form.find('textarea[name="message"]').val().trim();
        const privacy = $form.find('input[name="privacy"]').is(':checked');
        const consent = $form.find('input[name="consent"]').is(':checked');

        // Очистка предыдущих ошибок
        $form.find('.form-error').remove();
        $form.find('.field-error').removeClass('field-error');

        const errors = {};

        if (!name) {
            errors.name = 'Имя обязательно';
        }

        if (!email) {
            errors.email = 'Email обязателен';
        } else if (!isValidEmail(email)) {
            errors.email = 'Некорректный email';
        }

        if (!message) {
            errors.message = 'Сообщение обязательно';
        }

        if (!privacy) {
            errors.privacy = 'Необходимо согласие с политикой конфиденциальности';
        }

        if (!consent) {
            errors.consent = 'Необходимо согласие на обработку персональных данных';
        }

        // Показываем ошибки
        if (Object.keys(errors).length > 0) {
            for (const field in errors) {
                const $field = $form.find('[name="' + field + '"]');
                $field.addClass('field-error');
                $field.after('<div class="form-error">' + errors[field] + '</div>');
            }
            
            if (typeof atkShowToast === 'function') {
                atkShowToast('Пожалуйста, исправьте ошибки в форме', 'warning');
            } else {
                alert('Пожалуйста, исправьте ошибки в форме');
            }
            return;
        }

        // Отправка данных
        $button.text('Отправка...').prop('disabled', true);

        $.ajax({
            url: atkVedData.ajaxUrl,
            type: 'POST',
            data: $form.serialize() + '&action=atk_ved_contact_form&nonce=' + atkVedData.nonce,
            success: function(response) {
                if (response.success) {
                    if (typeof atkShowToast === 'function') {
                        atkShowToast(response.data?.message || 'Спасибо! Ваше сообщение отправлено. Мы свяжемся с вами в течение 15 минут.', 'success', 4000);
                    } else {
                        alert(response.data?.message || 'Спасибо! Ваше сообщение отправлено. Мы свяжемся с вами в течение 15 минут.');
                    }
                    $form[0].reset();
                } else {
                    // Обработка ошибок с сервера
                    if (response.data && response.data.errors) {
                        for (const field in response.data.errors) {
                            if (response.data.errors[field]) {
                                const $field = $form.find('[name="' + field + '"]');
                                $field.addClass('field-error');
                                $field.after('<div class="form-error">' + response.data.errors[field] + '</div>');
                            }
                        }
                    }
                    
                    if (typeof atkShowToast === 'function') {
                        atkShowToast(response.data?.message || 'Произошла ошибка. Пожалуйста, попробуйте позже.', 'error');
                    } else {
                        alert(response.data?.message || 'Произошла ошибка. Пожалуйста, попробуйте позже.');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                if (typeof atkShowToast === 'function') {
                    atkShowToast('Ошибка соединения. Попробуйте позже.', 'error');
                } else {
                    alert('Произошла ошибка. Пожалуйста, попробуйте позже или свяжитесь с нами по телефону.');
                }
            },
            complete: function() {
                $button.text(buttonText).prop('disabled', false);
            }
        });
    });

    // Валидация email
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Добавляем стили для ошибок
    if (!$('#form-validation-styles').length) {
        $('head').append(`
            <style id="form-validation-styles">
                .field-error {
                    border-color: #e31e24 !important;
                    box-shadow: 0 0 0 2px rgba(227, 30, 36, 0.2) !important;
                }
                .form-error {
                    color: #e31e24;
                    font-size: 12px;
                    margin-top: 5px;
                    display: block;
                }
            </style>
        `);
    }

    // Система уведомлений - оптимизированная версия
    window.atkShowToast = function(message, type = 'info', duration = 3000) {
        // Удаляем предыдущие уведомления
        $('.toast-notification').remove();
        
        // Создаем новое уведомление
        const toastClass = 'toast-notification toast-' + type;
        const $toast = $(document.createElement('div'));
        $toast.addClass(toastClass).text(message);
        
        // Добавляем в DOM
        $('body').append($toast);
        
        // Показываем
        setTimeout(() => {
            $toast.addClass('show');
        }, 100);
        
        // Автоматически скрываем
        if (duration > 0) {
            setTimeout(() => {
                $toast.removeClass('show');
                setTimeout(() => {
                    $toast.remove();
                }, 300);
            }, duration);
        }
        
        return $toast;
    };

    // Функция для скрытия уведомления
    window.atkHideToast = function($toast) {
        $toast.removeClass('show');
        setTimeout(() => {
            $toast.remove();
        }, 300);
    };

    // Оптимизированная обработка формы улучшенного контакта
    $('body').on('submit', '#contactFormEnhanced', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $button = $form.find('button[type="submit"]');
        const buttonText = $button.text();

        // Валидация
        const name = $form.find('input[name="name"]').val().trim();
        const email = $form.find('input[name="email"]').val().trim();
        const phone = $form.find('input[name="phone"]').val().trim();
        const message = $form.find('textarea[name="message"]').val().trim();
        const privacy = $form.find('input[name="privacy"]').is(':checked');
        const consent = $form.find('input[name="consent"]').is(':checked');

        // Очистка предыдущих ошибок
        $form.find('.form-error').remove();
        $form.find('.field-error').removeClass('field-error');

        const errors = {};

        if (!name) {
            errors.name = 'Имя обязательно';
        }

        if (!email) {
            errors.email = 'Email обязателен';
        } else if (!isValidEmail(email)) {
            errors.email = 'Некорректный email';
        }

        if (!phone) {
            errors.phone = 'Телефон обязателен';
        }

        if (!message) {
            errors.message = 'Сообщение обязательно';
        }

        if (!privacy) {
            errors.privacy = 'Необходимо согласие с политикой конфиденциальности';
        }

        if (!consent) {
            errors.consent = 'Необходимо согласие на обработку персональных данных';
        }

        // Показываем ошибки
        if (Object.keys(errors).length > 0) {
            for (const field in errors) {
                const $field = $form.find('[name="' + field + '"]');
                $field.addClass('field-error');
                $field.after('<div class="form-error">' + errors[field] + '</div>');
            }
            
            if (typeof atkShowToast === 'function') {
                atkShowToast('Пожалуйста, исправьте ошибки в форме', 'warning');
            } else {
                alert('Пожалуйста, исправьте ошибки в форме');
            }
            return;
        }

        // Отправка данных
        $button.text('Отправка...').prop('disabled', true);

        $.ajax({
            url: atkVedData.ajaxUrl,
            type: 'POST',
            data: $form.serialize(),
            success: function(response) {
                if (response.success) {
                    if (typeof atkShowToast === 'function') {
                        atkShowToast(response.data?.message || 'Спасибо! Ваше сообщение отправлено. Мы свяжемся с вами в течение 15 минут.', 'success', 4000);
                    } else {
                        alert(response.data?.message || 'Спасибо! Ваше сообщение отправлено. Мы свяжемся с вами в течение 15 минут.');
                    }
                    $form[0].reset();
                } else {
                    // Обработка ошибок с сервера
                    if (response.data && response.data.errors) {
                        for (const field in response.data.errors) {
                            if (response.data.errors[field]) {
                                const $field = $form.find('[name="' + field + '"]');
                                $field.addClass('field-error');
                                $field.after('<div class="form-error">' + response.data.errors[field] + '</div>');
                            }
                        }
                    }
                    
                    if (typeof atkShowToast === 'function') {
                        atkShowToast(response.data?.message || 'Произошла ошибка. Пожалуйста, попробуйте позже.', 'error');
                    } else {
                        alert(response.data?.message || 'Произошла ошибка. Пожалуйста, попробуйте позже.');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                if (typeof atkShowToast === 'function') {
                    atkShowToast('Ошибка соединения. Попробуйте позже.', 'error');
                } else {
                    alert('Произошла ошибка. Пожалуйста, попробуйте позже или свяжитесь с нами по телефону.');
                }
            },
            complete: function() {
                $button.text(buttonText).prop('disabled', false);
            }
        });
    });
    
    // ============================================================================
    // НОВЫЕ ОПТИМИЗАЦИИ JAVASCRIPT v2.8
    // ============================================================================
    
    // Оптимизированная система уведомлений
    window.atkShowToast = function(message, type = 'info', duration = 3000) {
        // Удаляем предыдущие уведомления
        $('.toast-notification').remove();
        
        // Создаем новое уведомление
        let toastClass = 'toast-notification toast-' + type;
        let $toast = $('<div class="' + toastClass + '">' + message + '</div>');
        
        // Добавляем в DOM
        $('body').append($toast);
        
        // Показываем
        setTimeout(() => {
            $toast.addClass('show');
        }, 100);
        
        // Автоматически скрываем
        if (duration > 0) {
            setTimeout(() => {
                $toast.removeClass('show');
                setTimeout(() => {
                    $toast.remove();
                }, 300);
            }, duration);
        }
        
        return $toast;
    };
    
    // Оптимизированная система lazy loading для изображений
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const src = img.dataset.src;
                        
                        if (src) {
                            img.src = src;
                            img.classList.remove('lazy');
                            img.classList.add('lazy-loaded');
                            observer.unobserve(img);
                        }
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }
    
    // Оптимизированная система кэширования API запросов
    const apiCache = new Map();
    
    window.atkCachedApiRequest = function(url, options = {}) {
        const cacheKey = url + JSON.stringify(options);
        const cached = apiCache.get(cacheKey);
        
        if (cached && Date.now() - cached.timestamp < 300000) { // 5 минут
            return Promise.resolve(cached.data);
        }
        
        return $.ajax({
            url: url,
            ...options
        }).then(data => {
            apiCache.set(cacheKey, {
                data: data,
                timestamp: Date.now()
            });
            return data;
        });
    };
    
    // Оптимизированная система обработки ошибок
    window.atkHandleError = function(error, context = '') {
        console.error('ATK Error [' + context + ']:', error);
        
        // Логируем в систему мониторинга
        if (typeof atkVedData !== 'undefined' && atkVedData.logError) {
            $.post(atkVedData.ajaxUrl, {
                action: 'log_error',
                error: error.toString(),
                context: context,
                nonce: atkVedData.nonce
            });
        }
        
        // Показываем пользователю дружелюбное сообщение
        if (typeof atkShowToast === 'function') {
            atkShowToast('Произошла ошибка. Пожалуйста, попробуйте позже.', 'error');
        }
    };
    
    // Оптимизированная система аналитики
    window.atkTrackEvent = function(category, action, label = '', value = 0) {
        // Google Analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', action, {
                event_category: category,
                event_label: label,
                value: value
            });
        }
        
        // Yandex Metrika
        if (typeof ym !== 'undefined' && typeof atkVedData !== 'undefined' && atkVedData.ymId) {
            ym(atkVedData.ymId, 'reachGoal', action);
        }
        
        // Внутренняя аналитика
        if (typeof atkVedData !== 'undefined') {
            $.post(atkVedData.ajaxUrl, {
                action: 'track_event',
                category: category,
                action: action,
                label: label,
                value: value,
                nonce: atkVedData.nonce
            });
        }
    };
    
    // Оптимизированная система предзагрузки критических ресурсов
    function preloadCriticalResources() {
        const criticalResources = [
            '/wp-content/themes/atk-ved/css/critical.css',
            '/wp-content/themes/atk-ved/js/critical.js'
        ];
        
        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = resource;
            link.as = resource.endsWith('.css') ? 'style' : 'script';
            document.head.appendChild(link);
        });
    }
    
    // Оптимизированная система обработки производительности
    function measurePerformance() {
        if ('performance' in window) {
            const perfData = {
                loadTime: performance.now(),
                memory: performance.memory || null,
                navigation: performance.navigation || null
            };
            
            // Отправляем метрики производительности
            if (typeof atkVedData !== 'undefined') {
                setTimeout(() => {
                    $.post(atkVedData.ajaxUrl, {
                        action: 'log_performance',
                        data: perfData,
                        nonce: atkVedData.nonce
                    });
                }, 5000); // Отправляем через 5 секунд после загрузки
            }
        }
    }
    
    // Инициализация оптимизированных систем
    $(document).ready(function() {
        initLazyLoading();
        preloadCriticalResources();
        measurePerformance();
        
        // Отслеживаем важные события
        $(document).on('click', '[data-track]', function() {
            const $el = $(this);
            atkTrackEvent(
                $el.data('track-category') || 'interaction',
                $el.data('track-action') || 'click',
                $el.data('track-label') || $el.text().trim()
            );
        });
    });
    
    // ============================================================================
    // JAVASCRIPT ДЛЯ НОВЫХ КОМПОНЕНТОВ v2.8
    // ============================================================================
    
    // Слайдер отзывов
    function initTestimonialsSlider() {
        $('.enhanced-slider').each(function() {
            const $slider = $(this);
            const $wrapper = $slider.find('.slider-wrapper');
            const $slides = $slider.find('.slider-slide');
            const $prev = $slider.find('.slider-prev');
            const $next = $slider.find('.slider-next');
            const $indicators = $slider.find('.indicator');
            
            let currentIndex = 0;
            const slideCount = $slides.length;
            const slideWidth = 100;
            
            // Функция перехода к слайду
            function goToSlide(index) {
                if (index < 0) index = slideCount - 1;
                if (index >= slideCount) index = 0;
                
                currentIndex = index;
                const translateX = -index * slideWidth;
                $wrapper.css('transform', `translateX(${translateX}%)`);
                
                // Обновляем индикаторы
                $indicators.removeClass('active');
                $indicators.eq(index).addClass('active');
                
                // Обновляем активные слайды
                $slides.removeClass('active');
                $slides.eq(index).addClass('active');
            }
            
            // Навигация
            $prev.on('click', function() {
                goToSlide(currentIndex - 1);
            });
            
            $next.on('click', function() {
                goToSlide(currentIndex + 1);
            });
            
            // Индикаторы
            $indicators.on('click', function() {
                const index = $(this).data('slide');
                goToSlide(index);
            });
            
            // Автопрокрутка
            if ($slider.data('autoplay')) {
                const interval = $slider.data('interval') || 5000;
                let autoSlide = setInterval(() => {
                    goToSlide(currentIndex + 1);
                }, interval);
                
                // Останавливаем при наведении
                $slider.hover(
                    () => clearInterval(autoSlide),
                    () => {
                        autoSlide = setInterval(() => {
                            goToSlide(currentIndex + 1);
                        }, interval);
                    }
                );
            }
        });
    }
    
    // Анимированные счетчики
    function initAnimatedCounters() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const $counter = $(entry.target);
                    const target = parseInt($counter.data('target'));
                    let current = 0;
                    const duration = 2000;
                    const steps = 60;
                    const increment = target / steps;
                    const interval = duration / steps;
                    
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        $counter.text(Math.floor(current));
                    }, interval);
                    
                    observer.unobserve(entry.target);
                }
            });
        });
        
        $('.stat-number[data-target]').each(function() {
            observer.observe(this);
        });
    }
    
    // Улучшенный аккордеон
    function initEnhancedAccordion() {
        $('.enhanced-accordion .faq-question').on('click', function() {
            const $item = $(this).closest('.faq-item');
            const $accordion = $(this).closest('.enhanced-accordion');
            const allowMultiple = $accordion.data('multiple');
            
            if (!allowMultiple) {
                $accordion.find('.faq-item').not($item).removeClass('active')
                    .find('.faq-answer').css('max-height', '0');
            }
            
            $item.toggleClass('active');
            const $answer = $item.find('.faq-answer');
            const isActive = $item.hasClass('active');
            
            if (isActive) {
                $answer.css('max-height', $answer.prop('scrollHeight') + 'px');
            } else {
                $answer.css('max-height', '0');
            }
        });
    }
    
    // Анимация при загрузке страницы
    function initPageAnimations() {
        // Анимация заголовков
        gsap.from('.section-title', {
            duration: 1,
            y: 50,
            opacity: 0,
            ease: 'power3.out',
            stagger: 0.2
        });
        
        // Анимация карточек
        gsap.from('.enhanced-card', {
            duration: 0.8,
            y: 30,
            opacity: 0,
            ease: 'power2.out',
            stagger: 0.1,
            scrollTrigger: {
                trigger: '.enhanced-card',
                start: 'top 85%'
            }
        });
    }
    
    // Инициализация всех новых компонентов
    $(document).ready(function() {
        initTestimonialsSlider();
        initAnimatedCounters();
        initEnhancedAccordion();
        
        // Инициализация анимаций после загрузки страницы
        setTimeout(initPageAnimations, 100);
    });
    
    // ============================================================================
    // JAVASCRIPT ДЛЯ WELCOME PAGE И DEMO IMPORT v2.9
    // ============================================================================
    
    // Анимация прогресса импорта
    function initImportProgress() {
        $('.import-progress').each(function() {
            const $progress = $(this);
            const $bar = $progress.find('.progress-bar');
            const $text = $progress.find('.progress-text');
            const totalSteps = $progress.data('total-steps') || 5;
            let currentStep = 0;
            
            function updateProgress() {
                currentStep++;
                const percentage = (currentStep / totalSteps) * 100;
                $bar.css('width', percentage + '%');
                $text.text(`Шаг ${currentStep} из ${totalSteps}`);
                
                if (currentStep < totalSteps) {
                    setTimeout(updateProgress, 1500);
                } else {
                    $text.text('Импорт завершен!');
                    $progress.addClass('completed');
                }
            }
            
            // Начинаем при клике на кнопку импорта
            $('.start-import').on('click', function(e) {
                e.preventDefault();
                $progress.addClass('active');
                updateProgress();
            });
        });
    }
    
    // Анимация карточек при наведении
    function initCardHoverEffects() {
        $('.feature-card, .step, .quick-link').on('mouseenter', function() {
            $(this).addClass('hovered');
        }).on('mouseleave', function() {
            $(this).removeClass('hovered');
        });
    }
    
    // Плавная прокрутка к разделам
    function initSmoothScrolling() {
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 800);
            }
        });
    }
    
    // Анимация появления элементов
    function initOnboardingAnimations() {
        // Анимация шагов onboarding
        gsap.from('.step', {
            duration: 0.6,
            y: 30,
            opacity: 0,
            ease: 'power2.out',
            stagger: 0.2,
            scrollTrigger: {
                trigger: '.onboarding-steps',
                start: 'top 80%'
            }
        });
        
        // Анимация карточек функций
        gsap.from('.feature-card', {
            duration: 0.5,
            x: -50,
            opacity: 0,
            ease: 'power2.out',
            stagger: 0.1,
            scrollTrigger: {
                trigger: '.atk-features-grid',
                start: 'top 85%'
            }
        });
        
        // Анимация quick links
        gsap.from('.quick-link', {
            duration: 0.4,
            scale: 0.9,
            opacity: 0,
            ease: 'back.out(1.7)',
            stagger: 0.05,
            scrollTrigger: {
                trigger: '.quick-links-grid',
                start: 'top 90%'
            }
        });
    }
    
    // Валидация формы импорта
    function initImportFormValidation() {
        $('.import-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();
            
            // Показываем прогресс
            $submitBtn.prop('disabled', true).text('Импорт в процессе...');
            
            // Симуляция процесса импорта
            setTimeout(() => {
                // Показываем результаты
                $('.import-results').addClass('show');
                $submitBtn.text('Импорт завершен!').addClass('success');
                
                // Скрываем через 3 секунды
                setTimeout(() => {
                    $('.import-results').removeClass('show');
                    $submitBtn.text(originalText).removeClass('success').prop('disabled', false);
                }, 3000);
            }, 2000);
        });
    }
    
    // Инициализация всех админских компонентов
    $(document).ready(function() {
        initImportProgress();
        initCardHoverEffects();
        initSmoothScrolling();
        initImportFormValidation();
        
        // Инициализация анимаций для админки
        if ($('.atk-welcome-page').length || $('.atk-demo-import-page').length) {
            setTimeout(initOnboardingAnimations, 300);
        }
        
        // Tooltips для админки
        $('[data-tooltip]').each(function() {
            const tooltipText = $(this).data('tooltip');
            $(this).attr('title', tooltipText);
        });
    });
    
    // ============================================================================
    // JAVASCRIPT ДЛЯ DATABASE OPTIMIZATION & CACHING v2.9.1
    // ============================================================================
    
    // Инициализация системы оптимизации базы данных
    function initDatabaseOptimization() {
        // Обработчики кнопок оптимизации
        $('.btn-optimize').on('click', function() {
            executeDatabaseAction('optimize', $(this));
        });
        
        $('.btn-clean').on('click', function() {
            executeDatabaseAction('clean', $(this));
        });
        
        $('.btn-analyze').on('click', function() {
            executeDatabaseAction('analyze', $(this));
        });
        
        $('.btn-flush').on('click', function() {
            executeCacheAction('flush', $(this));
        });
        
        // Автоматическое обновление статистики
        if ($('.atk-db-optimization-dashboard').length) {
            setInterval(updateCacheStats, 30000); // Обновление каждые 30 секунд
        }
    }
    
    // Выполнение действий с базой данных
    function executeDatabaseAction(action, $button) {
        const originalText = $button.html();
        const actionText = {
            'optimize': 'Оптимизация...',
            'clean': 'Очистка...',
            'analyze': 'Анализ...'
        };
        
        // Показываем прогресс
        $button.addClass('btn-loading').html(actionText[action]);
        
        // Отправляем AJAX запрос
        $.ajax({
            url: atkVedData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atk_ved_' + action + '_database',
                nonce: atkVedData.nonce
            },
            success: function(response) {
                if (response.success) {
                    showResults(response.data, 'success');
                } else {
                    showResults(response.data || {message: 'Ошибка выполнения'}, 'error');
                }
            },
            error: function() {
                showResults({message: 'Ошибка соединения'}, 'error');
            },
            complete: function() {
                $button.removeClass('btn-loading').html(originalText);
            }
        });
    }
    
    // Выполнение действий с кэшем
    function executeCacheAction(action, $button) {
        const originalText = $button.html();
        const actionText = {
            'flush': 'Очистка кэша...'
        };
        
        $button.addClass('btn-loading').html(actionText[action]);
        
        $.ajax({
            url: atkVedData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atk_ved_' + action + '_cache',
                nonce: atkVedData.nonce
            },
            success: function(response) {
                if (response.success) {
                    showResults(response.data, 'success');
                    updateCacheStats(); // Обновляем статистику после очистки
                } else {
                    showResults(response.data || {message: 'Ошибка выполнения'}, 'error');
                }
            },
            error: function() {
                showResults({message: 'Ошибка соединения'}, 'error');
            },
            complete: function() {
                $button.removeClass('btn-loading').html(originalText);
            }
        });
    }
    
    // Показ результатов
    function showResults(data, type) {
        const $results = $('.optimization-results');
        const $resultsContent = $results.find('.results-content');
        
        let html = '';
        
        if (data.message) {
            html += `<p><strong>${data.message}</strong></p>`;
        }
        
        if (data.tables_optimized !== undefined) {
            html += `<p>Оптимизировано таблиц: ${data.tables_optimized}</p>`;
        }
        
        if (data.total_cleaned !== undefined) {
            html += `<p>Очищено элементов: ${data.total_cleaned}</p>`;
        }
        
        if (data.deleted_items !== undefined) {
            html += `<p>Удалено из кэша: ${data.deleted_items} элементов</p>`;
        }
        
        if (data.execution_time) {
            html += `<p>Время выполнения: ${data.execution_time}</p>`;
        }
        
        if (data.cleaned_items) {
            html += '<h4>Подробности очистки:</h4><ul>';
            for (const [item, count] of Object.entries(data.cleaned_items)) {
                html += `<li>${item}: ${count}</li>`;
            }
            html += '</ul>';
        }
        
        if (data.errors && data.errors.length > 0) {
            html += '<h4>Ошибки:</h4><ul>';
            data.errors.forEach(error => {
                html += `<li class="error">${error}</li>`;
            });
            html += '</ul>';
        }
        
        $resultsContent.html(html);
        $results.removeClass('results-success results-error results-warning')
                .addClass(`results-${type}`)
                .show();
        
        // Автоматическое скрытие через 5 секунд
        setTimeout(() => {
            $results.fadeOut();
        }, 5000);
    }
    
    // Обновление статистики кэша
    function updateCacheStats() {
        if (!$('.atk-cache-management').length) return;
        
        $.ajax({
            url: atkVedData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atk_ved_cache_stats',
                nonce: atkVedData.nonce
            },
            success: function(response) {
                if (response.success) {
                    const stats = response.data;
                    $('.cache-stat-transients .stat-number').text(stats.transients_count || 0);
                    $('.cache-stat-theme .stat-number').text(stats.theme_cache_count || 0);
                    $('.cache-stat-rest .stat-number').text(stats.rest_cache_count || 0);
                    $('.cache-stat-pages .stat-number').text(stats.page_cache_count || 0);
                    $('.cache-stat-size .stat-number').text(stats.cache_size_mb + ' MB');
                }
            }
        });
    }
    
    // Анимация прогресса оптимизации
    function initOptimizationProgress() {
        $('.optimization-progress').each(function() {
            const $progress = $(this);
            const $bar = $progress.find('.progress-bar');
            const $text = $progress.find('.progress-text');
            
            // Симуляция прогресса
            function simulateProgress() {
                let progress = 0;
                const interval = setInterval(() => {
                    progress += Math.random() * 15;
                    if (progress >= 100) {
                        progress = 100;
                        clearInterval(interval);
                        $text.text('Завершено!');
                    } else {
                        $text.text(`Выполняется... ${Math.round(progress)}%`);
                    }
                    $bar.css('width', progress + '%');
                }, 200);
            }
            
            // Запуск при начале оптимизации
            $('.optimization-btn').on('click', function() {
                $progress.show();
                simulateProgress();
            });
        });
    }
    
    // Инициализация всех компонентов оптимизации
    $(document).ready(function() {
        initDatabaseOptimization();
        initOptimizationProgress();
        updateCacheStats();
    });
    
    // ============================================================================
    // JAVASCRIPT ДЛЯ ADVANCED SECURITY v2.9.1
    // ============================================================================
    
    // Инициализация системы безопасности
    function initAdvancedSecurity() {
        // Обработчики кнопок безопасности
        $('.btn-scan').on('click', function() {
            executeSecurityAction('scan', $(this));
        });
        
        $('.btn-report').on('click', function() {
            executeSecurityAction('report', $(this));
        });
        
        $('.btn-fix').on('click', function() {
            const fixType = $(this).data('fix-type');
            executeSecurityFix(fixType, $(this));
        });
        
        // Автоматическое обновление статуса безопасности
        if ($('.atk-security-dashboard').length) {
            setInterval(updateSecurityStatus, 60000); // Обновление каждую минуту
        }
    }
    
    // Выполнение действий безопасности
    function executeSecurityAction(action, $button) {
        const originalText = $button.html();
        const actionText = {
            'scan': 'Сканирование...',
            'report': 'Генерация отчета...'
        };
        
        // Показываем прогресс
        $button.addClass('btn-loading').html(actionText[action]);
        $('.security-progress').show();
        
        // Отправляем AJAX запрос
        $.ajax({
            url: atkVedData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atk_ved_security_' + action,
                nonce: atkVedData.nonce
            },
            success: function(response) {
                if (response.success) {
                    if (action === 'scan') {
                        displaySecurityScanResults(response.data);
                    } else if (action === 'report') {
                        displaySecurityReport(response.data);
                    }
                } else {
                    showSecurityMessage('Ошибка выполнения: ' + (response.data?.message || 'Неизвестная ошибка'), 'error');
                }
            },
            error: function() {
                showSecurityMessage('Ошибка соединения с сервером', 'error');
            },
            complete: function() {
                $button.removeClass('btn-loading').html(originalText);
                $('.security-progress').hide();
            }
        });
    }
    
    // Выполнение исправлений безопасности
    function executeSecurityFix(fixType, $button) {
        const originalText = $button.html();
        
        $button.addClass('btn-loading').html('Применение...');
        
        $.ajax({
            url: atkVedData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atk_ved_security_fix',
                fix_type: fixType,
                nonce: atkVedData.nonce
            },
            success: function(response) {
                if (response.success) {
                    const result = response.data;
                    if (result.status === 'success') {
                        showSecurityMessage(result.message, 'success');
                        updateSecurityStatus(); // Обновляем статус после исправления
                    } else if (result.status === 'manual') {
                        showSecurityMessage(result.message, 'warning');
                    } else {
                        showSecurityMessage(result.message, 'error');
                    }
                } else {
                    showSecurityMessage('Ошибка при применении исправления', 'error');
                }
            },
            error: function() {
                showSecurityMessage('Ошибка соединения', 'error');
            },
            complete: function() {
                $button.removeClass('btn-loading').html(originalText);
            }
        });
    }
    
    // Отображение результатов сканирования
    function displaySecurityScanResults(data) {
        // Обновляем оценку безопасности
        const score = calculateSecurityScore(data);
        $('.security-score').text(score).removeClass('score-excellent score-good score-fair score-poor')
                           .addClass(getScoreClass(score));
        
        // Обновляем карточки статуса
        if (data.checks) {
            $.each(data.checks, function(checkKey, check) {
                const $card = $('.security-card[data-check="' + checkKey + '"]');
                if ($card.length) {
                    $card.removeClass('card-secure card-warning card-error')
                         .addClass(getCardClass(check.status));
                    $card.find('.status-icon').removeClass('status-ok status-warning status-error')
                         .addClass('status-' + check.status);
                }
            });
        }
        
        // Отображаем проблемы
        if (data.issues && data.issues.length > 0) {
            let issuesHtml = '';
            $.each(data.issues, function(index, issue) {
                issuesHtml += `
                    <li class="issue-item issue-${issue.type}">
                        <h4>
                            <span class="status-icon status-${issue.type}">${issue.type === 'error' ? '❌' : '⚠️'}</span>
                            ${issue.title}
                        </h4>
                        <p class="issue-description">${issue.description}</p>
                        <div class="issue-actions">
                            <button class="security-btn btn-fix" data-fix-type="${issue.check_key}">
                                Исправить
                            </button>
                        </div>
                    </li>
                `;
            });
            
            $('.issues-list').html(issuesHtml);
            $('.security-issues').show();
        } else {
            $('.issues-list').html('<li class="issue-item issue-success"><p>Проблем безопасности не обнаружено! 🎉</p></li>');
        }
        
        showSecurityMessage('Сканирование завершено успешно', 'success');
    }
    
    // Отображение отчета по безопасности
    function displaySecurityReport(data) {
        // Обновляем оценку безопасности
        $('.security-score').text(data.security_score).removeClass('score-excellent score-good score-fair score-poor')
                           .addClass(getScoreClass(data.security_score));
        
        // Отображаем последние события
        if (data.recent_events && data.recent_events.length > 0) {
            let eventsHtml = '';
            $.each(data.recent_events.slice(-10), function(index, event) {
                const eventTypeClass = getEventTypeClass(event.message);
                eventsHtml += `
                    <tr class="event-${eventTypeClass}">
                        <td>${event.timestamp}</td>
                        <td>${event.message}</td>
                        <td>${event.ip || 'N/A'}</td>
                    </tr>
                `;
            });
            
            $('.events-table tbody').html(eventsHtml);
        }
        
        // Отображаем заблокированные IP
        if (data.blocked_ips && Object.keys(data.blocked_ips).length > 0) {
            let ipHtml = '';
            $.each(data.blocked_ips, function(ip, expiry) {
                const expiryDate = new Date(expiry * 1000).toLocaleString();
                ipHtml += `
                    <div class="ip-item">
                        <span class="ip-address">${ip}</span>
                        <span class="ip-expiry">Истекает: ${expiryDate}</span>
                    </div>
                `;
            });
            $('.ip-list').html(ipHtml);
            $('.blocked-ips-section').show();
        }
        
        showSecurityMessage('Отчет сгенерирован успешно', 'success');
    }
    
    // Обновление статуса безопасности
    function updateSecurityStatus() {
        $.ajax({
            url: atkVedData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atk_ved_security_scan',
                nonce: atkVedData.nonce
            },
            success: function(response) {
                if (response.success) {
                    const score = calculateSecurityScore(response.data);
                    $('.security-score').text(score).removeClass('score-excellent score-good score-fair score-poor')
                                       .addClass(getScoreClass(score));
                }
            }
        });
    }
    
    // Показ сообщений безопасности
    function showSecurityMessage(message, type) {
        const messageTypeClass = {
            'success': 'results-success',
            'error': 'results-error',
            'warning': 'results-warning'
        };
        
        const $message = $('<div class="optimization-results ' + messageTypeClass[type] + '">' +
                          '<div class="results-content"><p>' + message + '</p></div></div>');
        
        $('.atk-security-dashboard').prepend($message);
        
        // Автоматическое скрытие через 5 секунд
        setTimeout(() => {
            $message.fadeOut(() => {
                $message.remove();
            });
        }, 5000);
    }
    
    // Вспомогательные функции
    function calculateSecurityScore(scanData) {
        if (!scanData.checks) return 100;
        
        const totalChecks = Object.keys(scanData.checks).length;
        const issuesCount = scanData.issues ? scanData.issues.length : 0;
        
        if (totalChecks === 0) return 100;
        
        const score = Math.max(0, 100 - Math.round((issuesCount / totalChecks) * 100));
        return score;
    }
    
    function getScoreClass(score) {
        if (score >= 90) return 'score-excellent';
        if (score >= 70) return 'score-good';
        if (score >= 50) return 'score-fair';
        return 'score-poor';
    }
    
    function getCardClass(status) {
        return status === 'ok' ? 'card-secure' : (status === 'warning' ? 'card-warning' : 'card-error');
    }
    
    function getEventTypeClass(message) {
        if (message.includes('blocked') || message.includes('attack')) return 'critical';
        if (message.includes('warning') || message.includes('failed')) return 'warning';
        return 'info';
    }
    
    // Инициализация всех компонентов безопасности
    $(document).ready(function() {
        initAdvancedSecurity();
        updateSecurityStatus();
    });
    
    // ============================================================================
    // JAVASCRIPT ДЛЯ СОВРЕМЕННОГО FOOTER v3.1
    // ============================================================================
    
    // Инициализация компонентов footer
    function initModernFooter() {
        // Обработчик формы подписки
        $('.newsletter-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $email = $form.find('input[type="email"]');
            const $button = $form.find('.cta-button');
            const email = $email.val().trim();
            
            // Валидация email
            if (!isValidEmail(email)) {
                showFooterMessage('Пожалуйста, введите корректный email', 'error');
                $email.focus();
                return;
            }
            
            // Анимация отправки
            const originalText = $button.text();
            $button.prop('disabled', true).text('Отправка...');
            
            // Симуляция отправки (в реальном проекте здесь будет AJAX)
            setTimeout(() => {
                showFooterMessage('Спасибо за подписку! Мы отправим вам первое письмо в ближайшее время.', 'success');
                $form[0].reset();
                $button.prop('disabled', false).text(originalText);
            }, 1500);
        });
        
        // Анимация прогресса прокрутки для кнопки "наверх"
        let ticking = false;
        function updateScrollProgress() {
            const scrollTop = $(window).scrollTop();
            const docHeight = $(document).height() - $(window).height();
            const progress = (scrollTop / docHeight) * 100;
            
            $('.progress-ring path').css('stroke-dasharray', `${progress}, 100`);
            ticking = false;
        }
        
        $(window).on('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(updateScrollProgress);
                ticking = true;
            }
        });
        
        // Анимация появления элементов footer при прокрутке
        if ('IntersectionObserver' in window) {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('footer-animate-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            // Наблюдаем за элементами footer
            $('.footer-col').each(function() {
                observer.observe(this);
            });
        }
    }
    
    // Валидация email
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Показ сообщений в footer
    function showFooterMessage(message, type) {
        // Удаляем предыдущие сообщения
        $('.footer-message').remove();
        
        // Создаем новое сообщение
        const messageClass = `footer-message message-${type}`;
        const $message = $(`<div class="${messageClass}">${message}</div>`);
        
        // Добавляем в footer
        $('.footer-newsletter').after($message);
        
        // Показываем
        setTimeout(() => {
            $message.addClass('show');
        }, 100);
        
        // Автоматически скрываем
        setTimeout(() => {
            $message.removeClass('show');
            setTimeout(() => {
                $message.remove();
            }, 300);
        }, 5000);
    }
    
    // Анимация появления footer
    function animateFooterEntrance() {
        const $footer = $('.modern-footer');
        if ($footer.length) {
            // Анимация по секциям
            gsap.from('.footer-top', {
                duration: 1,
                y: 50,
                opacity: 0,
                ease: 'power3.out'
            });
            
            gsap.from('.footer-col', {
                duration: 0.8,
                y: 30,
                opacity: 0,
                ease: 'power2.out',
                stagger: 0.1,
                scrollTrigger: {
                    trigger: '.footer-grid',
                    start: 'top 85%'
                }
            });
            
            gsap.from('.footer-bottom', {
                duration: 1,
                y: 20,
                opacity: 0,
                ease: 'power2.out',
                scrollTrigger: {
                    trigger: '.footer-bottom',
                    start: 'top 90%'
                }
            });
        }
    }
    
    // Инициализация всех компонентов footer
    $(document).ready(function() {
        initModernFooter();
        setTimeout(animateFooterEntrance, 300);
    });

    // ============================================================================
    // JAVASCRIPT ДЛЯ UX/UI УЛУЧШЕНИЙ v3.1
    // ============================================================================
    
    // Анимация появления элементов при прокрутке
    function initScrollAnimations() {
        if ('IntersectionObserver' in window) {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        const animationClass = element.dataset.animation || 'fade-in-up';
                        element.classList.add('animate');
                        observer.unobserve(element);
                    }
                });
            }, observerOptions);
            
            // Наблюдаем за элементами с анимациями
            document.querySelectorAll('[data-animation]').forEach(function(element) {
                observer.observe(element);
            });
        }
    }
    
    // Улучшенные галереи изображений
    function initImageGalleries() {
        $('.gallery-item').on('click', function() {
            const $this = $(this);
            const imageUrl = $this.find('img').attr('src');
            const title = $this.find('.gallery-title').text();
            
            // Создаем модальное окно для просмотра
            const modal = `
                <div class="modal-enhanced" id="galleryModal">
                    <div class="modal-content-enhanced">
                        <div style="text-align: right; margin-bottom: 20px;">
                            <button class="btn-close" style="background: none; border: none; font-size: 24px; cursor: pointer;">×</button>
                        </div>
                        <img src="${imageUrl}" alt="${title}" style="width: 100%; border-radius: 8px;">
                        <h3 style="margin: 20px 0 0; text-align: center;">${title}</h3>
                    </div>
                </div>
            `;
            
            $('body').append(modal);
            
            // Показываем модальное окно
            setTimeout(() => {
                $('#galleryModal').addClass('active');
            }, 10);
            
            // Закрытие модального окна
            $('#galleryModal .btn-close, #galleryModal').on('click', function(e) {
                if (e.target === this) {
                    $('#galleryModal').removeClass('active');
                    setTimeout(() => {
                        $('#galleryModal').remove();
                    }, 400);
                }
            });
        });
    }
    
    // Улучшенные формы
    function initEnhancedForms() {
        // Анимация фокуса для полей формы
        $('.form-group-enhanced input, .form-group-enhanced textarea, .form-group-enhanced select').on('focus', function() {
            $(this).parent().addClass('focused');
        }).on('blur', function() {
            if (!$(this).val()) {
                $(this).parent().removeClass('focused');
            }
        });
        
        // Валидация форм в реальном времени
        $('.form-enhanced').each(function() {
            const $form = $(this);
            
            $form.find('input[required], textarea[required]').on('input', function() {
                const $field = $(this);
                const value = $field.val().trim();
                const isValid = value.length > 0;
                
                $field.toggleClass('valid', isValid);
                $field.toggleClass('invalid', !isValid && value.length > 0);
            });
        });
    }
    
    // Улучшенные уведомления
    function showEnhancedNotification(message, type = 'success', duration = 5000) {
        // Удаляем предыдущие уведомления
        $('.notification-enhanced').remove();
        
        const notificationClass = `notification-enhanced notification-${type}`;
        const $notification = $(`<div class="${notificationClass}">${message}</div>`);
        
        $('body').append($notification);
        
        // Показываем уведомление
        setTimeout(() => {
            $notification.addClass('show');
        }, 100);
        
        // Автоматически скрываем
        if (duration > 0) {
            setTimeout(() => {
                $notification.removeClass('show');
                setTimeout(() => {
                    $notification.remove();
                }, 400);
            }, duration);
        }
    }
    
    // Улучшенные кнопки с эффектами загрузки
    function initEnhancedButtons() {
        $('.btn-enhanced').on('click', function(e) {
            const $button = $(this);
            const $originalContent = $button.find('span').text();
            
            if ($button.hasClass('loading')) {
                return false;
            }
            
            // Добавляем состояние загрузки
            $button.addClass('loading');
            $button.find('span').html('<div class="loading-spinner"></div>');
            
            // Симуляция загрузки (в реальном проекте здесь будет AJAX)
            setTimeout(() => {
                $button.removeClass('loading');
                $button.find('span').text($originalContent);
                showEnhancedNotification('Действие выполнено успешно!', 'success');
            }, 2000);
        });
    }
    
    // Плавная прокрутка к якорям
    function initSmoothScrolling() {
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 800, 'easeInOutCubic');
            }
        });
    }
    
    // Улучшенные hover-эффекты
    function initHoverEffects() {
        // Добавляем классы для hover-эффектов
        $('.hover-lift').on('mouseenter', function() {
            $(this).addClass('hovered');
        }).on('mouseleave', function() {
            $(this).removeClass('hovered');
        });
        
        $('.hover-scale').on('mouseenter', function() {
            $(this).addClass('hovered');
        }).on('mouseleave', function() {
            $(this).removeClass('hovered');
        });
    }
    
    // Инициализация всех UX/UI компонентов
    $(document).ready(function() {
        initScrollAnimations();
        initImageGalleries();
        initEnhancedForms();
        initEnhancedButtons();
        initSmoothScrolling();
        initHoverEffects();
    });

});
