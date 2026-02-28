/**
 * Toast Notifications System
 * Система уведомлений для пользовательских действий
 *
 * @package ATK_VED
 * @since 3.6.0
 */

(function() {
    'use strict';

    /**
     * Toast Manager
     */
    class ToastManager {
        constructor(options = {}) {
            this.options = {
                position: options.position || 'top-right',
                duration: options.duration || 5000,
                maxToasts: options.maxToasts || 5,
                pauseOnHover: options.pauseOnHover !== false,
                closeButton: options.closeButton !== false,
                progressBar: options.progressBar !== false,
                ...options
            };

            this.toasts = [];
            this.container = null;
            this.init();
        }

        /**
         * Инициализация
         */
        init() {
            this.createContainer();
        }

        /**
         * Создание контейнера
         */
        createContainer() {
            if (this.container) return;

            this.container = document.createElement('div');
            this.container.className = `toast-container ${this.options.position}`;
            this.container.setAttribute('role', 'region');
            this.container.setAttribute('aria-label', 'Уведомления');
            document.body.appendChild(this.container);
        }

        /**
         * Показать toast
         */
        show(message, type = 'info', options = {}) {
            const config = { ...this.options, ...options };
            
            // Ограничение количества
            if (this.toasts.length >= this.options.maxToasts) {
                this.remove(this.toasts[0]);
            }

            const toast = this.createToast(message, type, config);
            this.toasts.push(toast);
            this.container.appendChild(toast.element);

            // Анимация входа
            requestAnimationFrame(() => {
                toast.element.classList.add('entering');
            });

            // Автоудаление
            if (config.duration > 0) {
                toast.timer = setTimeout(() => {
                    this.remove(toast);
                }, config.duration);

                // Прогресс-бар
                if (config.progressBar) {
                    this.startProgress(toast, config.duration);
                }
            }

            // Пауза при наведении
            if (config.pauseOnHover) {
                toast.element.addEventListener('mouseenter', () => {
                    if (toast.timer) {
                        clearTimeout(toast.timer);
                        if (toast.progressBar) {
                            toast.progressBar.style.animationPlayState = 'paused';
                        }
                    }
                });

                toast.element.addEventListener('mouseleave', () => {
                    if (config.duration > 0) {
                        const remaining = toast.remainingTime || config.duration;
                        toast.timer = setTimeout(() => {
                            this.remove(toast);
                        }, remaining);
                        
                        if (toast.progressBar) {
                            toast.progressBar.style.animationPlayState = 'running';
                        }
                    }
                });
            }

            return toast;
        }

        /**
         * Создание toast элемента
         */
        createToast(message, type, config) {
            const toast = {
                id: Date.now() + Math.random(),
                type,
                element: document.createElement('div'),
                timer: null,
                progressBar: null,
                remainingTime: config.duration
            };

            toast.element.className = `toast ${type}`;
            toast.element.setAttribute('role', 'alert');
            toast.element.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');

            // Иконка
            const icon = this.getIcon(type);
            const iconEl = document.createElement('div');
            iconEl.className = 'toast-icon';
            iconEl.innerHTML = icon;
            toast.element.appendChild(iconEl);

            // Контент
            const content = document.createElement('div');
            content.className = 'toast-content';

            if (typeof message === 'string') {
                const messageEl = document.createElement('p');
                messageEl.className = 'toast-title';
                messageEl.textContent = message;
                content.appendChild(messageEl);
            } else if (message.title || message.message) {
                if (message.title) {
                    const titleEl = document.createElement('p');
                    titleEl.className = 'toast-title';
                    titleEl.textContent = message.title;
                    content.appendChild(titleEl);
                }
                if (message.message) {
                    const messageEl = document.createElement('p');
                    messageEl.className = 'toast-message';
                    messageEl.textContent = message.message;
                    content.appendChild(messageEl);
                }
            }

            toast.element.appendChild(content);

            // Кнопка закрытия
            if (config.closeButton) {
                const closeBtn = document.createElement('button');
                closeBtn.className = 'toast-close';
                closeBtn.innerHTML = '×';
                closeBtn.setAttribute('aria-label', 'Закрыть уведомление');
                closeBtn.addEventListener('click', () => this.remove(toast));
                toast.element.appendChild(closeBtn);
            }

            // Прогресс-бар
            if (config.progressBar && config.duration > 0) {
                const progressBar = document.createElement('div');
                progressBar.className = 'toast-progress';
                toast.element.appendChild(progressBar);
                toast.progressBar = progressBar;
            }

            // Действия
            if (message.actions && Array.isArray(message.actions)) {
                const actionsEl = document.createElement('div');
                actionsEl.className = 'toast-actions';
                
                message.actions.forEach(action => {
                    const btn = document.createElement('button');
                    btn.className = `toast-action ${action.type || 'secondary'}`;
                    btn.textContent = action.label;
                    btn.addEventListener('click', () => {
                        if (action.onClick) action.onClick();
                        this.remove(toast);
                    });
                    actionsEl.appendChild(btn);
                });
                
                content.appendChild(actionsEl);
            }

            return toast;
        }

        /**
         * Получить иконку по типу
         */
        getIcon(type) {
            const icons = {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'ℹ'
            };
            return icons[type] || icons.info;
        }

        /**
         * Запустить прогресс-бар
         */
        startProgress(toast, duration) {
            if (!toast.progressBar) return;

            toast.progressBar.style.width = '100%';
            toast.progressBar.style.transition = `width ${duration}ms linear`;
            
            requestAnimationFrame(() => {
                toast.progressBar.style.width = '0%';
            });
        }

        /**
         * Удалить toast
         */
        remove(toast) {
            if (!toast || !toast.element) return;

            // Очистить таймер
            if (toast.timer) {
                clearTimeout(toast.timer);
            }

            // Анимация выхода
            toast.element.classList.remove('entering');
            toast.element.classList.add('exiting');

            setTimeout(() => {
                if (toast.element && toast.element.parentNode) {
                    toast.element.parentNode.removeChild(toast.element);
                }
                
                const index = this.toasts.indexOf(toast);
                if (index > -1) {
                    this.toasts.splice(index, 1);
                }
            }, 300);
        }

        /**
         * Удалить все toasts
         */
        clear() {
            this.toasts.forEach(toast => this.remove(toast));
        }

        /**
         * Методы для разных типов
         */
        success(message, options) {
            return this.show(message, 'success', options);
        }

        error(message, options) {
            return this.show(message, 'error', options);
        }

        warning(message, options) {
            return this.show(message, 'warning', options);
        }

        info(message, options) {
            return this.show(message, 'info', options);
        }
    }

    /**
     * Глобальный экземпляр
     */
    const toast = new ToastManager();

    /**
     * Экспорт в глобальную область
     */
    window.Toast = toast;
    window.ToastManager = ToastManager;

    /**
     * Интеграция с формами
     */
    document.addEventListener('DOMContentLoaded', () => {
        // Заменить alert() на toast
        const forms = document.querySelectorAll('form[data-toast]');
        
        forms.forEach(form => {
            form.addEventListener('submit', async (e) => {
                const useToast = form.dataset.toast !== 'false';
                if (!useToast) return;

                // Перехватываем стандартные alert
                const originalAlert = window.alert;
                window.alert = (message) => {
                    toast.info(message);
                };

                // Восстанавливаем после отправки
                setTimeout(() => {
                    window.alert = originalAlert;
                }, 100);
            });
        });

        // AJAX формы
        document.addEventListener('ajaxSuccess', (e) => {
            if (e.detail && e.detail.message) {
                toast.success(e.detail.message);
            }
        });

        document.addEventListener('ajaxError', (e) => {
            if (e.detail && e.detail.message) {
                toast.error(e.detail.message);
            }
        });
    });

    /**
     * Примеры использования:
     * 
     * // Простое уведомление
     * Toast.success('Данные сохранены!');
     * Toast.error('Произошла ошибка');
     * Toast.warning('Внимание!');
     * Toast.info('Информация');
     * 
     * // С заголовком и сообщением
     * Toast.success({
     *     title: 'Успешно!',
     *     message: 'Ваша заявка отправлена'
     * });
     * 
     * // С настройками
     * Toast.success('Сохранено', {
     *     duration: 3000,
     *     position: 'bottom-right'
     * });
     * 
     * // С действиями
     * Toast.info({
     *     title: 'Новое обновление',
     *     message: 'Доступна новая версия',
     *     actions: [
     *         {
     *             label: 'Обновить',
     *             type: 'primary',
     *             onClick: () => location.reload()
     *         },
     *         {
     *             label: 'Позже',
     *             type: 'secondary'
     *         }
     *     ]
     * }, { duration: 0 });
     * 
     * // Создать свой экземпляр
     * const myToast = new ToastManager({
     *     position: 'bottom-center',
     *     duration: 3000
     * });
     */

})();
