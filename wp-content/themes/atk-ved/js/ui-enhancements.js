/**
 * Улучшения UX/UI
 */

(function($) {
    'use strict';
    
    // Preloader
    $(window).on('load', function() {
        $('.preloader').addClass('hide');
        setTimeout(function() {
            $('.preloader').remove();
        }, 500);
    });
    
    // Lazy loading изображений
    function lazyLoadImages() {
        const images = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.add('loaded');
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
    
    // Tooltips
    function initTooltips() {
        $('[data-tooltip]').each(function() {
            const $this = $(this);
            const text = $this.data('tooltip');
            
            $this.wrap('<div class="tooltip-wrapper"></div>');
            $this.parent().append('<div class="tooltip">' + text + '</div>');
        });
        
        $('.tooltip-wrapper').hover(
            function() {
                $(this).find('.tooltip').addClass('show');
            },
            function() {
                $(this).find('.tooltip').removeClass('show');
            }
        );
    }
    
    // Модальные окна
    function initModals() {
        // Открытие модального окна
        $('[data-modal]').on('click', function(e) {
            e.preventDefault();
            const modalId = $(this).data('modal');
            $('#' + modalId).addClass('show');
            $('body').css('overflow', 'hidden');
        });
        
        // Закрытие модального окна
        $('.modal-close, .modal-backdrop').on('click', function(e) {
            if (e.target === this) {
                $(this).closest('.modal-backdrop').removeClass('show');
                $('body').css('overflow', '');
            }
        });
        
        // Закрытие по ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.modal-backdrop.show').removeClass('show');
                $('body').css('overflow', '');
            }
        });
    }
    
    // Уведомления
    window.showNotification = function(options) {
        const defaults = {
            type: 'info', // success, warning, error, info
            title: '',
            message: '',
            duration: 5000
        };
        
        const settings = $.extend({}, defaults, options);
        
        const icons = {
            success: '✓',
            warning: '⚠',
            error: '✕',
            info: 'ℹ'
        };
        
        const $notification = $('<div class="notification ' + settings.type + '">' +
            '<div class="notification-icon">' + icons[settings.type] + '</div>' +
            '<div class="notification-content">' +
                (settings.title ? '<div class="notification-title">' + settings.title + '</div>' : '') +
                '<div class="notification-message">' + settings.message + '</div>' +
            '</div>' +
            '<button class="notification-close">×</button>' +
        '</div>');
        
        if (!$('.notification-container').length) {
            $('body').append('<div class="notification-container"></div>');
        }
        
        $('.notification-container').append($notification);
        
        setTimeout(function() {
            $notification.addClass('show');
        }, 10);
        
        // Закрытие уведомления
        $notification.find('.notification-close').on('click', function() {
            $notification.removeClass('show').addClass('hide');
            setTimeout(function() {
                $notification.remove();
            }, 300);
        });
        
        // Автоматическое закрытие
        if (settings.duration > 0) {
            setTimeout(function() {
                $notification.removeClass('show').addClass('hide');
                setTimeout(function() {
                    $notification.remove();
                }, 300);
            }, settings.duration);
        }
    };
    
    // Tabs
    function initTabs() {
        $('.tab-link').on('click', function(e) {
            e.preventDefault();
            const $this = $(this);
            const target = $this.data('tab');
            
            $this.addClass('active').siblings().removeClass('active');
            $('#' + target).addClass('active').siblings('.tab-content').removeClass('active');
        });
    }
    
    // Dropdown
    function initDropdowns() {
        $('.dropdown-toggle').on('click', function(e) {
            e.stopPropagation();
            $(this).parent('.dropdown').toggleClass('active');
        });
        
        $(document).on('click', function() {
            $('.dropdown').removeClass('active');
        });
    }
    
    // Smooth scroll с offset
    function smoothScroll() {
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            
            if (target.length) {
                e.preventDefault();
                const offset = $('.site-header').outerHeight() || 70;
                
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - offset
                }, 800, 'swing');
            }
        });
    }
    
    // Ripple эффект для кнопок
    function initRipple() {
        $('.cta-button, .ripple').on('click', function(e) {
            const $this = $(this);
            const $ripple = $('<span class="ripple-effect"></span>');
            
            const x = e.pageX - $this.offset().left;
            const y = e.pageY - $this.offset().top;
            
            $ripple.css({
                top: y + 'px',
                left: x + 'px'
            });
            
            $this.append($ripple);
            
            setTimeout(function() {
                $ripple.remove();
            }, 600);
        });
    }
    
    // Копирование в буфер обмена
    window.copyToClipboard = function(text) {
        const $temp = $('<textarea>');
        $('body').append($temp);
        $temp.val(text).select();
        document.execCommand('copy');
        $temp.remove();
        
        showNotification({
            type: 'success',
            message: 'Скопировано в буфер обмена'
        });
    };
    
    // Форматирование номера телефона при вводе
    function formatPhoneInput() {
        $('input[type="tel"]').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            
            if (value.length > 0) {
                if (value[0] === '7' || value[0] === '8') {
                    value = '7' + value.substring(1);
                }
                
                let formatted = '+7';
                if (value.length > 1) {
                    formatted += ' (' + value.substring(1, 4);
                }
                if (value.length >= 5) {
                    formatted += ') ' + value.substring(4, 7);
                }
                if (value.length >= 8) {
                    formatted += '-' + value.substring(7, 9);
                }
                if (value.length >= 10) {
                    formatted += '-' + value.substring(9, 11);
                }
                
                $(this).val(formatted);
            }
        });
    }
    
    // Валидация форм
    function validateForm($form) {
        let isValid = true;
        
        $form.find('[required]').each(function() {
            const $field = $(this);
            const value = $field.val().trim();
            
            $field.removeClass('error');
            $field.next('.error-message').remove();
            
            if (!value) {
                isValid = false;
                $field.addClass('error');
                $field.after('<span class="error-message">Это поле обязательно</span>');
            } else if ($field.attr('type') === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    $field.addClass('error');
                    $field.after('<span class="error-message">Некорректный email</span>');
                }
            }
        });
        
        return isValid;
    }
    
    // Отправка формы с валидацией
    function initFormSubmit() {
        $('form.ajax-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            
            if (!validateForm($form)) {
                showNotification({
                    type: 'error',
                    message: 'Пожалуйста, заполните все обязательные поля'
                });
                return;
            }
            
            const $submitBtn = $form.find('[type="submit"]');
            const originalText = $submitBtn.text();
            
            $submitBtn.prop('disabled', true).html('<span class="loading-spinner"></span> Отправка...');
            
            $.ajax({
                url: atkVedData.ajaxUrl,
                type: 'POST',
                data: $form.serialize() + '&action=atk_ved_contact_form&nonce=' + atkVedData.nonce,
                success: function(response) {
                    if (response.success) {
                        showNotification({
                            type: 'success',
                            title: 'Успешно!',
                            message: response.data.message
                        });
                        $form[0].reset();
                    } else {
                        showNotification({
                            type: 'error',
                            title: 'Ошибка',
                            message: response.data.message
                        });
                    }
                },
                error: function() {
                    showNotification({
                        type: 'error',
                        title: 'Ошибка',
                        message: 'Произошла ошибка. Попробуйте позже.'
                    });
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });
    }
    
    // Счетчик символов
    function initCharCounter() {
        $('[data-max-length]').each(function() {
            const $field = $(this);
            const maxLength = $field.data('max-length');
            const $counter = $('<div class="char-counter"><span class="current">0</span> / ' + maxLength + '</div>');
            
            $field.after($counter);
            
            $field.on('input', function() {
                const length = $(this).val().length;
                $counter.find('.current').text(length);
                
                if (length > maxLength) {
                    $counter.addClass('exceeded');
                } else {
                    $counter.removeClass('exceeded');
                }
            });
        });
    }
    
    // Прогресс скролла
    function initScrollProgress() {
        const $progress = $('<div class="scroll-progress"><div class="scroll-progress-bar"></div></div>');
        $('body').append($progress);
        
        $(window).on('scroll', function() {
            const scrollTop = $(window).scrollTop();
            const docHeight = $(document).height() - $(window).height();
            const scrollPercent = (scrollTop / docHeight) * 100;
            
            $('.scroll-progress-bar').css('width', scrollPercent + '%');
        });
    }
    
    // Инициализация при загрузке
    $(document).ready(function() {
        lazyLoadImages();
        initTooltips();
        initModals();
        initTabs();
        initDropdowns();
        smoothScroll();
        initRipple();
        formatPhoneInput();
        initFormSubmit();
        initCharCounter();
        initScrollProgress();
        
        // Показать элементы при скролле
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.1
        });
        
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    });
    
})(jQuery);
