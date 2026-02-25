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

});
