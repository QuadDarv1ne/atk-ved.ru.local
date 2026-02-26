/**
 * Additional UI Components JavaScript: Toasts, Progress Bars, Skeletons
 * 
 * @package ATK_VED
 * @since 2.2.0
 */

(function($) {
    'use strict';

    // Инициализация при загрузке DOM
    $(document).ready(function() {
        initAlerts();
        initProgressBars();
    });

    /* ==========================================================================
       TOAST NOTIFICATIONS
       ========================================================================== */

    /**
     * Показать уведомление (Toast)
     * @param {Object} options - Настройки уведомления
     * @param {string} options.type - Тип: info, success, warning, error
     * @param {string} options.title - Заголовок
     * @param {string} options.message - Сообщение
     * @param {number} options.duration - Время показа (мс)
     * @param {string} options.position - Позиция: top-right, top-left, top-center, bottom-right, bottom-left, bottom-center
     * @param {boolean} options.closable - Можно ли закрыть
     * @param {Function} options.onClose - Callback при закрытии
     */
    function showToast(options) {
        const settings = $.extend({
            type: 'info',
            title: '',
            message: '',
            duration: 5000,
            position: 'top-right',
            closable: true,
            onClose: null
        }, options);

        // Создание контейнера если не существует
        let $container = $('.toast-container.toast-' + settings.position);
        if (!$container.length) {
            $container = $('<div class="toast-container toast-' + settings.position + '"></div>');
            $('body').append($container);
        }

        // Иконки для типов
        const icons = {
            info: 'ℹ️',
            success: '✅',
            warning: '⚠️',
            error: '❌'
        };

        // Создание уведомления
        const toastId = 'toast-' + Date.now();
        const $toast = $(`
            <div class="toast toast-${settings.type}" id="${toastId}" role="alert">
                <div class="toast-icon">${icons[settings.type] || icons.info}</div>
                <div class="toast-content">
                    ${settings.title ? `<div class="toast-title">${escapeHtml(settings.title)}</div>` : ''}
                    ${settings.message ? `<div class="toast-message">${escapeHtml(settings.message)}</div>` : ''}
                </div>
                ${settings.closable ? '<button class="toast-close" aria-label="Закрыть">×</button>' : ''}
            </div>
        `);

        $container.append($toast);

        // Закрытие по кнопке
        $toast.find('.toast-close').on('click', function() {
            closeToast($toast, settings.onClose);
        });

        // Автозакрытие
        if (settings.duration > 0) {
            setTimeout(function() {
                closeToast($toast, settings.onClose);
            }, settings.duration);
        }

        // Ограничение количества уведомлений
        const maxToasts = 5;
        const $toasts = $container.find('.toast');
        if ($toasts.length > maxToasts) {
            closeToast($toasts.first(), null);
        }

        return $toast;
    }

    /**
     * Закрыть уведомление
     * @param {jQuery} $toast - Элемент уведомления
     * @param {Function} callback - Callback после закрытия
     */
    function closeToast($toast, callback) {
        $toast.addClass('toast-closing');
        
        setTimeout(function() {
            $toast.remove();
            
            // Удаление контейнера если пустой
            const $container = $('.toast-container');
            if ($container.children().length === 0) {
                $container.remove();
            }
            
            if (typeof callback === 'function') {
                callback();
            }
        }, 400);
    }

    /**
     * Закрыть все уведомления
     * @param {string} position - Позиция (опционально)
     */
    function closeAllToasts(position) {
        if (position) {
            $('.toast-container.toast-' + position).remove();
        } else {
            $('.toast-container').remove();
        }
    }

    /* ==========================================================================
       ALERT COMPONENTS
       ========================================================================== */

    function initAlerts() {
        // Закрытие alert по кнопке
        $(document).on('click', '.alert-close', function() {
            const $alert = $(this).closest('.alert');
            closeAlert($alert);
        });

        // Автозакрытие alert с data-duration
        $('.alert[data-duration]').each(function() {
            const $alert = $(this);
            const duration = parseInt($alert.data('duration')) || 5000;
            
            setTimeout(function() {
                closeAlert($alert);
            }, duration);
        });
    }

    /**
     * Закрыть alert
     * @param {jQuery} $alert - Элемент alert
     */
    function closeAlert($alert) {
        $alert.css({
            opacity: 0,
            transform: 'translateX(100%)'
        });
        
        setTimeout(function() {
            $alert.remove();
        }, 300);
    }

    /**
     * Показать alert
     * @param {Object} options - Настройки
     */
    function showAlert(options) {
        const settings = $.extend({
            type: 'info',
            title: '',
            message: '',
            dismissible: true,
            duration: 0,
            container: 'body'
        }, options);

        const icons = {
            info: 'ℹ️',
            success: '✅',
            warning: '⚠️',
            error: '❌'
        };

        const $alert = $(`
            <div class="alert alert-${settings.type} ${settings.dismissible ? 'alert-dismissible' : ''}" role="alert">
                <div class="alert-icon">${icons[settings.type] || icons.info}</div>
                <div class="alert-content">
                    ${settings.title ? `<strong class="alert-title">${escapeHtml(settings.title)}</strong>` : ''}
                    ${settings.message ? `<p class="alert-message">${escapeHtml(settings.message)}</p>` : ''}
                </div>
                ${settings.dismissible ? '<button class="alert-close" aria-label="Закрыть">×</button>' : ''}
            </div>
        `);

        $(settings.container).prepend($alert);

        if (settings.duration > 0) {
            setTimeout(function() {
                closeAlert($alert);
            }, settings.duration);
        }

        return $alert;
    }

    /* ==========================================================================
       PROGRESS BAR
       ========================================================================== */

    function initProgressBars() {
        // Анимация прогресс баров при появлении
        $('.progress-fill').each(function() {
            const $fill = $(this);
            const width = $fill.css('width');
            
            // Сохраняем целевую ширину в data-attribute
            $fill.attr('data-target-width', width);
            $fill.css('width', 0);
        });

        // Запуск анимации при появлении в viewport
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const $fill = $(entry.target);
                        const targetWidth = $fill.attr('data-target-width');
                        $fill.css('width', targetWidth);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.progress-fill').forEach(function(el) {
                observer.observe(el);
            });
        }
    }

    /**
     * Установить значение прогресс бара
     * @param {string|jQuery} selector - Селектор или элемент
     * @param {number} value - Значение
     * @param {number} max - Максимум
     */
    function setProgress(selector, value, max) {
        max = max || 100;
        const percentage = Math.round((value / max) * 100);
        const $progress = $(selector);
        
        if (!$progress.length) return;
        
        const $fill = $progress.find('.progress-fill');
        
        if ($fill.length) {
            $fill.css('width', percentage + '%');
            
            // Обновление ARIA атрибутов
            $fill.attr({
                'aria-valuenow': value,
                'aria-valuemin': 0,
                'aria-valuemax': max
            });
        }
        
        // Обновление текстового значения
        const $label = $progress.find('.progress-label');
        if ($label.length && !$label.attr('data-custom-label')) {
            $label.text(percentage + '%');
        }
    }

    /**
     * Инкремент прогресс бара
     * @param {string|jQuery} selector - Селектор
     * @param {number} step - Шаг
     */
    function incrementProgress(selector, step) {
        step = step || 10;
        const $progress = $(selector);
        const $fill = $progress.find('.progress-fill');
        
        if (!$fill.length) return;
        
        const currentWidth = parseFloat($fill.css('width')) || 0;
        const parentWidth = $fill.parent().width();
        const currentPercent = (currentWidth / parentWidth) * 100;
        const newPercent = Math.min(currentPercent + step, 100);
        
        $fill.css('width', newPercent + '%');
    }

    /**
     * Сбросить прогресс бар
     * @param {string|jQuery} selector - Селектор
     */
    function resetProgress(selector) {
        const $progress = $(selector);
        const $fill = $progress.find('.progress-fill');
        
        if (!$fill.length) return;
        
        $fill.css('width', 0);
        $fill.attr('aria-valuenow', 0);
        
        const $label = $progress.find('.progress-label');
        if ($label.length && !$label.attr('data-custom-label')) {
            $label.text('0%');
        }
    }

    /* ==========================================================================
       SKELETON LOADER
       ========================================================================== */

    /**
     * Показать skeleton загрузку
     * @param {string|jQuery} selector - Контейнер
     * @param {Object} options - Настройки
     */
    function showSkeleton(selector, options) {
        const settings = $.extend({
            lines: 3,
            showAvatar: false,
            showImage: false,
            showButton: false
        }, options);

        const $container = $(selector);
        if (!$container.length) return;

        let skeleton = '<div class="skeleton-card">';
        
        if (settings.showAvatar) {
            skeleton += '<div class="skeleton-avatar"></div>';
        }
        
        if (settings.showImage) {
            skeleton += '<div class="skeleton-image"></div>';
        }
        
        skeleton += '<div class="skeleton-title"></div>';
        
        for (let i = 0; i < settings.lines; i++) {
            skeleton += '<div class="skeleton-text"></div>';
        }
        
        if (settings.showButton) {
            skeleton += '<div class="skeleton-button"></div>';
        }
        
        skeleton += '</div>';

        $container.html(skeleton);
    }

    /**
     * Скрыть skeleton загрузку
     * @param {string|jQuery} selector - Контейнер
     */
    function hideSkeleton(selector) {
        $(selector).find('.skeleton-card').remove();
    }

    /* ==========================================================================
       UTILITY FUNCTIONS
       ========================================================================== */

    /**
     * Escape HTML для безопасности
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /* ==========================================================================
       GLOBAL API
       ========================================================================== */

    window.atkShowToast = showToast;
    window.atkCloseToast = closeToast;
    window.atkCloseAllToasts = closeAllToasts;
    
    window.atkShowAlert = showAlert;
    window.atkCloseAlert = closeAlert;
    
    window.atkSetProgress = setProgress;
    window.atkIncrementProgress = incrementProgress;
    window.atkResetProgress = resetProgress;
    
    window.atkShowSkeleton = showSkeleton;
    window.atkHideSkeleton = hideSkeleton;

    // Уведомления для форм
    $(document).on('ajaxComplete', function(e, xhr, settings) {
        try {
            const response = JSON.parse(xhr.responseText);
            
            if (settings.url.indexOf(atkVedData.ajaxUrl) !== -1) {
                if (response.success) {
                    atkShowToast({
                        type: 'success',
                        title: 'Успешно',
                        message: response.data.message || 'Операция выполнена успешно'
                    });
                } else {
                    atkShowToast({
                        type: 'error',
                        title: 'Ошибка',
                        message: response.data.message || 'Произошла ошибка'
                    });
                }
            }
        } catch (e) {
            // Не JSON ответ
        }
    });

})(jQuery);
