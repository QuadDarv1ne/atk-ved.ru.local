/**
 * Улучшения доступности (WCAG 2.1 AA)
 * @package ATK_VED
 */

(function() {
    'use strict';

    // Управление фокусом
    class FocusManager {
        constructor() {
            this.focusableElements = 'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])';
            this.init();
        }

        init() {
            // Видимый индикатор фокуса
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    document.body.classList.add('keyboard-nav');
                }
            });

            document.addEventListener('mousedown', () => {
                document.body.classList.remove('keyboard-nav');
            });

            // Ловушка фокуса для модальных окон
            this.trapFocusInModals();
        }

        trapFocusInModals() {
            document.addEventListener('keydown', (e) => {
                if (e.key !== 'Tab') return;

                const modal = document.querySelector('.modal.is-open');
                if (!modal) return;

                const focusable = modal.querySelectorAll(this.focusableElements);
                const firstFocusable = focusable[0];
                const lastFocusable = focusable[focusable.length - 1];

                if (e.shiftKey) {
                    if (document.activeElement === firstFocusable) {
                        lastFocusable.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastFocusable) {
                        firstFocusable.focus();
                        e.preventDefault();
                    }
                }
            });
        }

        // Переход к основному контенту
        skipToContent() {
            const skipLink = document.createElement('a');
            skipLink.href = '#main-content';
            skipLink.className = 'skip-to-content';
            skipLink.textContent = 'Перейти к основному содержимому';
            skipLink.addEventListener('click', (e) => {
                e.preventDefault();
                const main = document.getElementById('main-content');
                if (main) {
                    main.tabIndex = -1;
                    main.focus();
                }
            });
            document.body.insertBefore(skipLink, document.body.firstChild);
        }
    }

    // Объявления для скринридеров
    class LiveRegion {
        constructor() {
            this.region = this.create();
        }

        create() {
            const region = document.createElement('div');
            region.setAttribute('role', 'status');
            region.setAttribute('aria-live', 'polite');
            region.setAttribute('aria-atomic', 'true');
            region.className = 'sr-only';
            document.body.appendChild(region);
            return region;
        }

        announce(message, priority = 'polite') {
            this.region.setAttribute('aria-live', priority);
            this.region.textContent = message;
            
            setTimeout(() => {
                this.region.textContent = '';
            }, 1000);
        }
    }

    // Улучшение контрастности
    class ContrastChecker {
        constructor() {
            this.minContrast = 4.5; // WCAG AA
        }

        getLuminance(r, g, b) {
            const [rs, gs, bs] = [r, g, b].map(c => {
                c = c / 255;
                return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
            });
            return 0.2126 * rs + 0.7152 * gs + 0.0722 * bs;
        }

        getContrast(rgb1, rgb2) {
            const lum1 = this.getLuminance(...rgb1);
            const lum2 = this.getLuminance(...rgb2);
            const brightest = Math.max(lum1, lum2);
            const darkest = Math.min(lum1, lum2);
            return (brightest + 0.05) / (darkest + 0.05);
        }

        checkElement(element) {
            const style = window.getComputedStyle(element);
            const color = style.color;
            const bgColor = style.backgroundColor;
            
            // Парсинг RGB
            const parseRGB = (str) => {
                const match = str.match(/\d+/g);
                return match ? match.map(Number) : [0, 0, 0];
            };

            const textRGB = parseRGB(color);
            const bgRGB = parseRGB(bgColor);
            
            const contrast = this.getContrast(textRGB, bgRGB);
            
            if (contrast < this.minContrast) {
                console.warn(`Low contrast detected: ${contrast.toFixed(2)}:1`, element);
                element.setAttribute('data-low-contrast', 'true');
            }
        }
    }

    // Управление размером шрифта
    class FontSizeController {
        constructor() {
            this.sizes = ['normal', 'large', 'x-large'];
            this.currentIndex = 0;
            this.init();
        }

        init() {
            const controls = document.createElement('div');
            controls.className = 'font-size-controls';
            controls.innerHTML = `
                <button type="button" class="font-size-btn" data-action="decrease" aria-label="Уменьшить размер шрифта">A-</button>
                <button type="button" class="font-size-btn" data-action="increase" aria-label="Увеличить размер шрифта">A+</button>
                <button type="button" class="font-size-btn" data-action="reset" aria-label="Сбросить размер шрифта">A</button>
            `;

            controls.addEventListener('click', (e) => {
                const btn = e.target.closest('.font-size-btn');
                if (!btn) return;

                const action = btn.dataset.action;
                this.handleAction(action);
            });

            document.body.appendChild(controls);
        }

        handleAction(action) {
            switch(action) {
                case 'increase':
                    this.currentIndex = Math.min(this.currentIndex + 1, this.sizes.length - 1);
                    break;
                case 'decrease':
                    this.currentIndex = Math.max(this.currentIndex - 1, 0);
                    break;
                case 'reset':
                    this.currentIndex = 0;
                    break;
            }

            document.documentElement.setAttribute('data-font-size', this.sizes[this.currentIndex]);
            localStorage.setItem('fontSize', this.sizes[this.currentIndex]);
            
            // Объявление для скринридера
            if (window.liveRegion) {
                window.liveRegion.announce(`Размер шрифта изменен на ${this.sizes[this.currentIndex]}`);
            }
        }

        restore() {
            const saved = localStorage.getItem('fontSize');
            if (saved) {
                this.currentIndex = this.sizes.indexOf(saved);
                document.documentElement.setAttribute('data-font-size', saved);
            }
        }
    }

    // Улучшение форм
    class FormAccessibility {
        constructor() {
            this.init();
        }

        init() {
            // Связывание label с input
            document.querySelectorAll('input, select, textarea').forEach(field => {
                if (!field.id) {
                    field.id = `field-${Math.random().toString(36).substr(2, 9)}`;
                }

                const label = field.closest('label') || document.querySelector(`label[for="${field.id}"]`);
                if (!label && field.placeholder) {
                    field.setAttribute('aria-label', field.placeholder);
                }

                // Валидация с объявлениями
                field.addEventListener('invalid', (e) => {
                    e.preventDefault();
                    const message = field.validationMessage;
                    this.showError(field, message);
                    
                    if (window.liveRegion) {
                        window.liveRegion.announce(`Ошибка: ${message}`, 'assertive');
                    }
                });
            });
        }

        showError(field, message) {
            let error = field.nextElementSibling;
            if (!error || !error.classList.contains('field-error')) {
                error = document.createElement('span');
                error.className = 'field-error';
                error.setAttribute('role', 'alert');
                field.parentNode.insertBefore(error, field.nextSibling);
            }
            error.textContent = message;
            field.setAttribute('aria-invalid', 'true');
            field.setAttribute('aria-describedby', error.id || (error.id = `error-${field.id}`));
        }
    }

    // Инициализация
    document.addEventListener('DOMContentLoaded', () => {
        const focusManager = new FocusManager();
        focusManager.skipToContent();

        window.liveRegion = new LiveRegion();
        
        const fontSizeController = new FontSizeController();
        fontSizeController.restore();

        new FormAccessibility();

        // Проверка контрастности в dev режиме
        if (window.location.search.includes('debug=a11y')) {
            const checker = new ContrastChecker();
            document.querySelectorAll('*').forEach(el => {
                if (el.textContent.trim()) {
                    checker.checkElement(el);
                }
            });
        }
    });

})();
