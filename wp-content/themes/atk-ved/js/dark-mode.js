/**
 * JavaScript для темной темы v3.3
 * 
 * @package ATK_VED
 * @subpackage Dark_Mode_JS
 */

(function($) {
    'use strict';
    
    // ============================================================================
    // СИСТЕМА ТЕМНОЙ ТЕМЫ
    // ============================================================================
    
    const DarkMode = {
        // Текущая тема
        currentTheme: 'auto',
        
        // Доступные темы
        themes: ['light', 'dark', 'auto'],
        
        // Инициализация
        init: function() {
            this.detectSystemTheme();
            this.loadUserPreference();
            this.bindEvents();
            this.updateUI();
            this.addSystemThemeListener();
        },
        
        // Определение системной темы
        detectSystemTheme: function() {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                this.systemTheme = 'dark';
            } else {
                this.systemTheme = 'light';
            }
        },
        
        // Загрузка пользовательских предпочтений
        loadUserPreference: function() {
            // Проверяем data-атрибут кнопки
            const toggleButton = document.querySelector('.dark-mode-toggle');
            if (toggleButton) {
                this.currentTheme = toggleButton.dataset.defaultTheme || 'auto';
            }
            
            // Проверяем localStorage
            const savedTheme = localStorage.getItem('atk_ved_theme');
            if (savedTheme && this.themes.includes(savedTheme)) {
                this.currentTheme = savedTheme;
            }
            
            // Проверяем cookie
            const cookieTheme = this.getCookie('atk_ved_theme');
            if (cookieTheme && this.themes.includes(cookieTheme)) {
                this.currentTheme = cookieTheme;
            }
            
            this.applyTheme();
        },
        
        // Привязка событий
        bindEvents: function() {
            const self = this;
            
            // Кнопка переключения темы
            $(document).on('click', '.theme-toggle-btn', function(e) {
                e.preventDefault();
                self.switchTheme();
            });
            
            // Клавиша Tab для фокуса
            $(document).on('keydown', '.theme-toggle-btn', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    self.switchTheme();
                }
            });
            
            // Hover эффекты для кнопки
            $(document).on('mouseenter', '.dark-mode-toggle', function() {
                $(this).addClass('hovered');
            }).on('mouseleave', '.dark-mode-toggle', function() {
                $(this).removeClass('hovered');
            });
        },
        
        // Переключение темы
        switchTheme: function() {
            const currentIndex = this.themes.indexOf(this.currentTheme);
            const nextIndex = (currentIndex + 1) % this.themes.length;
            const newTheme = this.themes[nextIndex];
            
            this.setTheme(newTheme);
            this.savePreference(newTheme);
            this.updateUI();
            this.animateTransition();
        },
        
        // Установка темы
        setTheme: function(theme) {
            this.currentTheme = theme;
            
            // Определяем фактическую тему для применения
            let actualTheme = theme;
            if (theme === 'auto') {
                actualTheme = this.systemTheme;
            }
            
            // Удаляем старые классы тем
            $('body').removeClass('theme-light theme-dark theme-auto');
            
            // Добавляем новый класс
            $('body').addClass('theme-' + theme);
            
            // Применяем фактическую тему для стилей
            if (actualTheme === 'dark') {
                $('body').addClass('theme-dark-active');
            } else {
                $('body').removeClass('theme-dark-active');
            }
        },
        
        // Сохранение предпочтений
        savePreference: function(theme) {
            // Сохраняем в localStorage
            localStorage.setItem('atk_ved_theme', theme);
            
            // Сохраняем в cookie
            this.setCookie('atk_ved_theme', theme, 30);
            
            // Отправляем на сервер (если нужно)
            if (typeof darkModeData !== 'undefined') {
                $.ajax({
                    url: darkModeData.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'atk_ved_switch_theme',
                        theme: theme,
                        nonce: darkModeData.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Theme preference saved');
                        }
                    }
                });
            }
        },
        
        // Обновление UI
        updateUI: function() {
            const toggleButton = $('.theme-toggle-btn');
            const tooltip = $('.theme-tooltip');
            
            // Обновляем иконку
            toggleButton.find('.theme-icon').css('opacity', '0');
            setTimeout(() => {
                if (this.currentTheme === 'light') {
                    toggleButton.find('.light-icon').css('opacity', '1');
                    tooltip.find('.light-text').show().siblings().hide();
                } else if (this.currentTheme === 'dark') {
                    toggleButton.find('.dark-icon').css('opacity', '1');
                    tooltip.find('.dark-text').show().siblings().hide();
                } else {
                    toggleButton.find('.auto-icon').css('opacity', '1');
                    tooltip.find('.auto-text').show().siblings().hide();
                }
            }, 150);
        },
        
        // Анимация перехода
        animateTransition: function() {
            const body = $('body');
            body.addClass('theme-transition');
            
            setTimeout(() => {
                body.removeClass('theme-transition');
            }, 300);
        },
        
        // Слушатель системной темы
        addSystemThemeListener: function() {
            const self = this;
            
            if (window.matchMedia) {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                mediaQuery.addListener(function(e) {
                    self.systemTheme = e.matches ? 'dark' : 'light';
                    
                    // Если текущая тема авто, обновляем
                    if (self.currentTheme === 'auto') {
                        self.applyTheme();
                    }
                });
            }
        },
        
        // Применение темы
        applyTheme: function() {
            if (this.currentTheme === 'auto') {
                this.setTheme('auto');
            } else {
                this.setTheme(this.currentTheme);
            }
        },
        
        // Работа с cookie
        setCookie: function(name, value, days) {
            let expires = '';
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = name + '=' + (value || '') + expires + '; path=/';
        },
        
        getCookie: function(name) {
            const nameEQ = name + '=';
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
    };
    
    // Инициализация при загрузке документа
    $(document).ready(function() {
        DarkMode.init();
        initThemeToggle();
    });

    // ============================================================================
    // УЛУЧШЕННЫЙ ПЕРЕКЛЮЧАТЕЛЬ ТЕМЫ v2.0
    // ============================================================================

    function initThemeToggle() {
        // Создаем кнопку переключения темы, если её нет
        if ($('.theme-toggle').length === 0) {
            $('body').append(`
                <button class="theme-toggle" 
                        type="button" 
                        aria-label="Переключить тему"
                        title="Переключить тему">
                    <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                    <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>
            `);
        }

        const $toggle = $('.theme-toggle');
        const $body = $('body');

        // Проверка сохранённой темы
        const savedTheme = localStorage.getItem('atk_ved_theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        // Применяем тему
        if (savedTheme === 'dark') {
            $body.addClass('dark-mode');
        } else if (savedTheme === 'light') {
            $body.removeClass('dark-mode');
        } else if (systemPrefersDark) {
            $body.addClass('auto-dark');
        }

        // Переключение темы
        $toggle.on('click', function() {
            const isDark = $body.hasClass('dark-mode');
            
            // Анимация иконки
            $(this).find('svg').css('transform', 'rotate(180deg)');
            setTimeout(() => {
                $(this).find('svg').css('transform', 'rotate(0deg)');
            }, 300);

            // Переключаем тему
            if (isDark) {
                $body.removeClass('dark-mode').addClass('light-mode');
                localStorage.setItem('atk_ved_theme', 'light');
            } else {
                $body.removeClass('light-mode auto-dark').addClass('dark-mode');
                localStorage.setItem('atk_ved_theme', 'dark');
            }

            // Анимация фона
            $body.css('transition', 'all 0.3s ease');
            setTimeout(() => {
                $body.css('transition', '');
            }, 300);
        });

        // Слушатель системных настроек
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            // Применяем системную тему только если нет пользовательских предпочтений
            if (!localStorage.getItem('atk_ved_theme')) {
                if (e.matches) {
                    $body.addClass('auto-dark').removeClass('dark-mode');
                } else {
                    $body.removeClass('auto-dark dark-mode');
                }
            }
        });

        // Показываем кнопку после загрузки
        $toggle.css('opacity', '0').animate({ opacity: '1' }, 500);
    }

    // Экспорт для использования в других скриптах
    window.ATK_VED_DarkMode = DarkMode;

})(jQuery);