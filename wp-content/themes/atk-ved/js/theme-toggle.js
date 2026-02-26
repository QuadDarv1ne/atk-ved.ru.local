/**
 * Theme Toggle - Light/Dark Mode
 * @package ATK_VED
 */

(function() {
    'use strict';

    const THEME_KEY = 'atk-ved-theme';
    const THEME_DARK = 'dark';
    const THEME_LIGHT = 'light';

    // Получить сохраненную тему или системную
    function getPreferredTheme() {
        const saved = localStorage.getItem(THEME_KEY);
        if (saved) return saved;
        
        return window.matchMedia('(prefers-color-scheme: dark)').matches 
            ? THEME_DARK 
            : THEME_LIGHT;
    }

    // Применить тему
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(THEME_KEY, theme);
        
        // Обновить иконку кнопки
        updateToggleIcon(theme);
    }

    // Обновить иконку
    function updateToggleIcon(theme) {
        const btn = document.querySelector('.theme-toggle');
        if (!btn) return;

        const icon = btn.querySelector('.theme-icon');
        if (!icon) return;

        icon.innerHTML = theme === THEME_DARK 
            ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>'
            : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
        
        btn.setAttribute('aria-label', theme === THEME_DARK ? 'Светлая тема' : 'Темная тема');
    }

    // Переключить тему
    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-theme') || THEME_LIGHT;
        const next = current === THEME_DARK ? THEME_LIGHT : THEME_DARK;
        applyTheme(next);
    }

    // Инициализация
    function init() {
        // Применить тему сразу (до загрузки DOM)
        const theme = getPreferredTheme();
        applyTheme(theme);

        // После загрузки DOM
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.querySelector('.theme-toggle');
            if (btn) {
                btn.addEventListener('click', toggleTheme);
            }

            // Слушать изменения системной темы
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                if (!localStorage.getItem(THEME_KEY)) {
                    applyTheme(e.matches ? THEME_DARK : THEME_LIGHT);
                }
            });
        });
    }

    init();
})();
