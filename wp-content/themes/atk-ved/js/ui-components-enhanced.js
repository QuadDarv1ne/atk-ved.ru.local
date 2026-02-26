/**
 * UI Components JavaScript v3.2
 * Современные UI паттерны и интерактивность
 *
 * @package ATK_VED
 * @since   3.2.0
 */

(function($) {
    'use strict';

    // ========================================================================
    // TOAST NOTIFICATIONS
    // ========================================================================

    const Toast = {
        container: null,

        init() {
            this.createContainer();
        },

        createContainer() {
            this.container = $('<div class="toast-container"></div>');
            $('body').append(this.container);
        },

        show(options = {}) {
            const {
                type = 'info',
                title = '',
                message = '',
                duration = 5000,
                closable = true
            } = options;

            const toast = $(`
                <div class="toast toast-${type}" role="alert" aria-live="assertive">
                    ${this.getIcon(type)}
                    <div class="toast-content">
                        ${title ? `<div class="toast-title">${title}</div>` : ''}
                        ${message ? `<div class="toast-message">${message}</div>` : ''}
                    </div>
                    ${closable ? '<button class="toast-close" aria-label="Закрыть">×</button>' : ''}
                    ${duration > 0 ? `<div class="toast-progress" style="animation-duration: ${duration}ms"></div>` : ''}
                </div>
            `);

            this.container.append(toast);

            // Close button handler
            toast.find('.toast-close').on('click', () => this.close(toast));

            // Auto close
            if (duration > 0) {
                setTimeout(() => this.close(toast), duration);
            }

            // Announce to screen readers
            if (window.atkA11y) {
                window.atkA11y.announce(message || title, 'polite');
            }
        },

        close(toast) {
            toast.css('animation', 'slideOutRight 0.3s ease-out');
            setTimeout(() => toast.remove(), 300);
        },

        getIcon(type) {
            const icons = {
                success: '<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                error: '<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                warning: '<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                info: '<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            };
            return icons[type] || icons.info;
        },

        success(message, title = 'Успешно') {
            this.show({ type: 'success', message, title });
        },

        error(message, title = 'Ошибка') {
            this.show({ type: 'error', message, title });
        },

        warning(message, title = 'Внимание') {
            this.show({ type: 'warning', message, title });
        },

        info(message, title = 'Информация') {
            this.show({ type: 'info', message, title });
        }
    };

    // Make Toast globally available
    window.atkToast = Toast;
    Toast.init();


    // ========================================================================
    // SCROLL PROGRESS INDICATOR
    // ========================================================================

    function initScrollProgress() {
        const $progressBar = $('<div class="scroll-progress"></div>');
        $('body').prepend($progressBar);

        $(window).on('scroll', function() {
            const scrollTop = $(this).scrollTop();
            const docHeight = $(document).height() - $(this).height();
            const scrollPercent = (scrollTop / docHeight) * 100;
            $progressBar.css('width', scrollPercent + '%');
        });
    }

    initScrollProgress();


    // ========================================================================
    // SMOOTH SCROLL WITH OFFSET
    // ========================================================================

    $('a[href^="#"]').on('click', function(e) {
        const targetId = $(this).attr('href');
        if (targetId === '#') return;

        const $target = $(targetId);
        if ($target.length) {
            e.preventDefault();
            const offset = 80; // Header height
            const targetPosition = $target.offset().top - offset;

            $('html, body').stop().animate({
                scrollTop: targetPosition
            }, 800, 'easeInOutQuart');
        }
    });


    // ========================================================================
    // ENHANCED MODAL
    // ========================================================================

    const Modal = {
        open(content, options = {}) {
            const {
                closeOnBackdrop = true,
                closeOnEsc = true,
                onClose = null
            } = options;

            const $modal = $(`
                <div class="modal active" role="dialog" aria-modal="true" aria-labelledby="modal-title">
                    <div class="modal-content">
                        <button class="modal-close" aria-label="Закрыть" data-close>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        ${content}
                    </div>
                </div>
            `);

            $('body').append($modal);
            $('body').css('overflow', 'hidden');

            // Focus trap
            const $focusable = $modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            const $firstFocusable = $focusable.first();
            $firstFocusable.focus();

            // Close handlers
            if (closeOnBackdrop) {
                $modal.on('click', (e) => {
                    if ($(e.target).hasClass('modal')) {
                        this.close($modal, onClose);
                    }
                });
            }

            if (closeOnEsc) {
                $(document).one('keydown.modal', (e) => {
                    if (e.key === 'Escape') {
                        this.close($modal, onClose);
                    }
                });
            }

            $modal.find('[data-close]').on('click', () => {
                this.close($modal, onClose);
            });

            return $modal;
        },

        close($modal, onClose) {
            $modal.removeClass('active');
            setTimeout(() => {
                $modal.remove();
                $('body').css('overflow', '');
                $(document).off('keydown.modal');
                if (typeof onClose === 'function') {
                    onClose();
                }
            }, 300);
        }
    };

    window.atkModal = Modal;


    // ========================================================================
    // BUTTON LOADING STATE
    // ========================================================================

    $.fn.loading = function(isLoading = true, options = {}) {
        const {
            loadingText = 'Загрузка...',
            originalText = null
        } = options;

        return this.each(function() {
            const $btn = $(this);

            if (isLoading) {
                if (!originalText) {
                    $btn.data('original-text', $btn.text());
                }
                $btn.addClass('btn-loading').attr('disabled', true);
                if (loadingText) {
                    $btn.text(loadingText);
                }
            } else {
                $btn.removeClass('btn-loading').attr('disabled', false);
                if (originalText || $btn.data('original-text')) {
                    $btn.text(originalText || $btn.data('original-text'));
                }
            }
        });
    };


    // ========================================================================
    // FORM VALIDATION & ENHANCEMENTS
    // ========================================================================

    function initFormEnhancements() {
        // Real-time validation
        $('input, textarea').on('blur', function() {
            const $field = $(this);
            const $formGroup = $field.closest('.form-group');

            if ($field.val().trim() === '' && $field.prop('required')) {
                $formGroup.addClass('error').removeClass('success');
            } else if ($field.val().trim() !== '') {
                $formGroup.addClass('success').removeClass('error');
            }
        });

        // Email validation
        $('input[type="email"]').on('blur', function() {
            const $field = $(this);
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const $formGroup = $field.closest('.form-group');

            if ($field.val() && !emailRegex.test($field.val())) {
                $formGroup.addClass('error');
                if (!$formGroup.find('.error-message').length) {
                    $formGroup.append('<div class="error-message">Введите корректный email</div>');
                }
            } else {
                $formGroup.removeClass('error');
                $formGroup.find('.error-message').remove();
            }
        });

        // Phone mask (simple)
        $('input[type="tel"]').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length > 0) {
                if (value[0] === '7' || value[0] === '8') {
                    value = '7' + value.slice(1);
                }
            }
            $(this).val(value);
        });
    }

    initFormEnhancements();


    // ========================================================================
    // INTERSECTION OBSERVER FOR ANIMATIONS
    // ========================================================================

    function initAnimations() {
        if (!('IntersectionObserver' in window)) return;

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe elements
        document.querySelectorAll('.service-card, .step-card, .review-card, .method-card, .advantage-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    }

    initAnimations();


    // ========================================================================
    // RIPPLE EFFECT FOR BUTTONS
    // ========================================================================

    function initRippleEffect() {
        $('.cta-button, .btn').on('click', function(e) {
            const $btn = $(this);
            const ripple = $('<span class="btn-ripple"></span>');
            const offset = $btn.offset();
            const x = e.pageX - offset.left;
            const y = e.pageY - offset.top;

            ripple.css({
                top: y + 'px',
                left: x + 'px'
            });

            $btn.append(ripple);

            setTimeout(() => ripple.remove(), 600);
        });
    }

    initRippleEffect();


    // ========================================================================
    // LAZY LOADING IMAGES
    // ========================================================================

    function initLazyLoading() {
        if ('loading' in HTMLImageElement.prototype) {
            // Native lazy loading supported
            $('img').attr('loading', 'lazy');
        } else {
            // Fallback
            const images = document.querySelectorAll('img[data-src]');
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        imageObserver.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        }
    }

    initLazyLoading();


    // ========================================================================
    // UTILITY FUNCTIONS
    // ========================================================================

    // Debounce
    $.debounce = function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };

    // Throttle
    $.throttle = function(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    };

})(jQuery);
