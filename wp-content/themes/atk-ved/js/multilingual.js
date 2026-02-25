/**
 * Мультиязычность
 * 
 * @package ATK_VED
 * @since 2.0.0
 */

(function($) {
    'use strict';
    
    /**
     * Инициализация
     */
    $(document).ready(function() {
        initLanguageSwitcher();
        initLanguageDetection();
    });
    
    /**
     * Инициализация переключателя языков
     */
    function initLanguageSwitcher() {
        // Dropdown переключатель
        $('.lang-current').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $switcher = $(this).closest('.language-switcher-dropdown');
            $switcher.toggleClass('active');
        });
        
        // Закрытие при клике вне
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.language-switcher-dropdown').length) {
                $('.language-switcher-dropdown').removeClass('active');
            }
        });
        
        // Закрытие по Escape
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.language-switcher-dropdown').removeClass('active');
            }
        });
        
        // Переключение языка
        $('.lang-option, .lang-flag-btn').on('click', function(e) {
            e.preventDefault();
            
            const lang = $(this).data('lang');
            if (!lang) return;
            
            switchLanguage(lang);
        });
        
        // Клавиатурная навигация
        $('.lang-dropdown').on('keydown', '.lang-option', function(e) {
            const $options = $('.lang-dropdown .lang-option');
            const currentIndex = $options.index(this);
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextIndex = (currentIndex + 1) % $options.length;
                $options.eq(nextIndex).focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prevIndex = (currentIndex - 1 + $options.length) % $options.length;
                $options.eq(prevIndex).focus();
            } else if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).click();
            }
        });
    }
    
    /**
     * Переключение языка
     */
    function switchLanguage(lang) {
        const nonce = atkVedData?.nonce || '';
        
        // Показать загрузку
        showLanguageLoader();
        
        $.ajax({
            url: atkVedData?.ajaxUrl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'atk_ved_switch_language',
                nonce: nonce,
                lang: lang
            },
            success: function(response) {
                if (response.success) {
                    // Показать уведомление
                    showLanguageNotification(lang);
                    
                    // Перезагрузить страницу через 500ms
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);
                } else {
                    hideLanguageLoader();
                    showError(response.data?.message || 'Error switching language');
                }
            },
            error: function() {
                hideLanguageLoader();
                showError('Connection error');
            }
        });
    }
    
    /**
     * Показать загрузчик
     */
    function showLanguageLoader() {
        if ($('.lang-loader').length) return;
        
        const $loader = $('<div class="lang-loader"></div>');
        $loader.html(`
            <div class="lang-loader-spinner"></div>
            <div class="lang-loader-text">Switching language...</div>
        `);
        
        $('body').append($loader);
        
        // Добавить стили если их нет
        if (!$('#lang-loader-styles').length) {
            $('head').append(`
                <style id="lang-loader-styles">
                    .lang-loader {
                        position: fixed;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: rgba(0, 0, 0, 0.7);
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        z-index: 99999;
                        animation: fadeIn 0.3s ease;
                    }
                    .lang-loader-spinner {
                        width: 50px;
                        height: 50px;
                        border: 4px solid rgba(255, 255, 255, 0.3);
                        border-top-color: #e31e24;
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                    }
                    .lang-loader-text {
                        margin-top: 20px;
                        color: #fff;
                        font-size: 16px;
                        font-weight: 500;
                    }
                    @keyframes fadeIn {
                        from { opacity: 0; }
                        to { opacity: 1; }
                    }
                    @keyframes spin {
                        to { transform: rotate(360deg); }
                    }
                </style>
            `);
        }
    }
    
    /**
     * Скрыть загрузчик
     */
    function hideLanguageLoader() {
        $('.lang-loader').fadeOut(300, function() {
            $(this).remove();
        });
    }
    
    /**
     * Показать уведомление о смене языка
     */
    function showLanguageNotification(lang) {
        const languages = {
            'ru': { flag: '🇷🇺', name: 'Русский' },
            'en': { flag: '🇬🇧', name: 'English' },
            'zh': { flag: '🇨🇳', name: '中文' }
        };
        
        const langData = languages[lang] || languages['ru'];
        
        const $notification = $('<div class="lang-notification"></div>');
        $notification.html(`
            <span class="lang-notification-icon">${langData.flag}</span>
            <span class="lang-notification-text">Language: ${langData.name}</span>
            <button class="lang-notification-close" aria-label="Close">×</button>
        `);
        
        $('body').append($notification);
        
        // Закрытие уведомления
        $notification.find('.lang-notification-close').on('click', function() {
            $notification.fadeOut(300, function() {
                $(this).remove();
            });
        });
        
        // Автоматическое скрытие через 3 секунды
        setTimeout(function() {
            $notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    /**
     * Показать ошибку
     */
    function showError(message) {
        const $error = $('<div class="lang-error"></div>');
        $error.html(`
            <span class="lang-error-icon">⚠️</span>
            <span class="lang-error-text">${message}</span>
        `);
        
        $('body').append($error);
        
        // Добавить стили
        if (!$('#lang-error-styles').length) {
            $('head').append(`
                <style id="lang-error-styles">
                    .lang-error {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: #fff5f5;
                        border: 2px solid #ffcdd2;
                        border-radius: 12px;
                        padding: 15px 20px;
                        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                        z-index: 10000;
                        display: flex;
                        align-items: center;
                        gap: 12px;
                        animation: slideInRight 0.3s ease;
                    }
                    .lang-error-icon {
                        font-size: 24px;
                    }
                    .lang-error-text {
                        font-size: 14px;
                        font-weight: 500;
                        color: #c62828;
                    }
                </style>
            `);
        }
        
        setTimeout(function() {
            $error.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    /**
     * Определение языка браузера
     */
    function initLanguageDetection() {
        // Проверяем, был ли уже выбран язык
        if (getCookie('atk_ved_lang')) {
            return;
        }
        
        // Получаем язык браузера
        const browserLang = navigator.language || navigator.userLanguage;
        const langCode = browserLang.substring(0, 2);
        
        // Поддерживаемые языки
        const supportedLangs = ['ru', 'en', 'zh'];
        
        if (supportedLangs.includes(langCode)) {
            // Показать предложение сменить язык
            showLanguageSuggestion(langCode);
        }
    }
    
    /**
     * Показать предложение сменить язык
     */
    function showLanguageSuggestion(suggestedLang) {
        const languages = {
            'ru': { flag: '🇷🇺', name: 'Русский', question: 'Переключить на русский?' },
            'en': { flag: '🇬🇧', name: 'English', question: 'Switch to English?' },
            'zh': { flag: '🇨🇳', name: '中文', question: '切换到中文？' }
        };
        
        const langData = languages[suggestedLang];
        if (!langData) return;
        
        const $suggestion = $('<div class="lang-suggestion"></div>');
        $suggestion.html(`
            <div class="lang-suggestion-content">
                <span class="lang-suggestion-icon">${langData.flag}</span>
                <span class="lang-suggestion-text">${langData.question}</span>
            </div>
            <div class="lang-suggestion-actions">
                <button class="lang-suggestion-yes" data-lang="${suggestedLang}">Yes</button>
                <button class="lang-suggestion-no">No</button>
            </div>
        `);
        
        $('body').append($suggestion);
        
        // Добавить стили
        if (!$('#lang-suggestion-styles').length) {
            $('head').append(`
                <style id="lang-suggestion-styles">
                    .lang-suggestion {
                        position: fixed;
                        bottom: 20px;
                        right: 20px;
                        background: #fff;
                        border: 2px solid #e31e24;
                        border-radius: 12px;
                        padding: 20px;
                        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                        z-index: 10000;
                        max-width: 350px;
                        animation: slideInUp 0.3s ease;
                    }
                    .lang-suggestion-content {
                        display: flex;
                        align-items: center;
                        gap: 12px;
                        margin-bottom: 15px;
                    }
                    .lang-suggestion-icon {
                        font-size: 28px;
                    }
                    .lang-suggestion-text {
                        font-size: 15px;
                        font-weight: 500;
                        color: #333;
                    }
                    .lang-suggestion-actions {
                        display: flex;
                        gap: 10px;
                    }
                    .lang-suggestion-yes,
                    .lang-suggestion-no {
                        flex: 1;
                        padding: 10px 20px;
                        border: none;
                        border-radius: 8px;
                        font-size: 14px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    }
                    .lang-suggestion-yes {
                        background: #e31e24;
                        color: #fff;
                    }
                    .lang-suggestion-yes:hover {
                        background: #c01a1f;
                    }
                    .lang-suggestion-no {
                        background: #f8f9fa;
                        color: #666;
                    }
                    .lang-suggestion-no:hover {
                        background: #e0e0e0;
                    }
                    @keyframes slideInUp {
                        from {
                            opacity: 0;
                            transform: translateY(50px);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }
                </style>
            `);
        }
        
        // Обработчики
        $suggestion.find('.lang-suggestion-yes').on('click', function() {
            const lang = $(this).data('lang');
            $suggestion.remove();
            switchLanguage(lang);
        });
        
        $suggestion.find('.lang-suggestion-no').on('click', function() {
            $suggestion.fadeOut(300, function() {
                $(this).remove();
            });
            // Сохранить выбор в cookie
            setCookie('atk_ved_lang_suggestion_dismissed', '1', 30);
        });
    }
    
    /**
     * Получить cookie
     */
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }
    
    /**
     * Установить cookie
     */
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
    }
    
})(jQuery);
