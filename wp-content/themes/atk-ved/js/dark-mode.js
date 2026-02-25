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
    });
    
    // Экспорт для использования в других скриптах
    window.ATK_VED_DarkMode = DarkMode;
    
})(jQuery);