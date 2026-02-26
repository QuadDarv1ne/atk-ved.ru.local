/**
 * Back to Top Button
 * @package ATK_VED
 */

(function() {
    'use strict';

    // Создаём кнопку
    const btn = document.createElement('button');
    btn.className = 'back-to-top';
    btn.setAttribute('aria-label', 'Вернуться наверх');
    btn.innerHTML = `
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 15l-6-6-6 6"/>
        </svg>
    `;
    document.body.appendChild(btn);

    // Показываем/скрываем кнопку при скролле
    let isVisible = false;
    
    function toggleButton() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const shouldShow = scrollTop > 300;

        if (shouldShow && !isVisible) {
            btn.classList.add('visible');
            isVisible = true;
        } else if (!shouldShow && isVisible) {
            btn.classList.remove('visible');
            isVisible = false;
        }
    }

    // Плавный скролл наверх
    function scrollToTop(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // События
    window.addEventListener('scroll', toggleButton, { passive: true });
    btn.addEventListener('click', scrollToTop);

    // Проверяем при загрузке
    toggleButton();
})();
