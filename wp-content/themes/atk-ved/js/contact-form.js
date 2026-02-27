/**
 * Contact Form Handler
 *
 * @package ATK_VED
 */

(function() {
    'use strict';

    const $ = (selector) => document.querySelector(selector);

    function init() {
        const form = $('#contact-form');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const messageEl = form.querySelector('.form-message');

            if (!submitBtn || !messageEl) return;

            // Отключаем кнопку
            submitBtn.disabled = true;
            submitBtn.textContent = 'Отправка...';

            // reCAPTCHA v3
            const recaptchaSiteKey = window.atkVed?.recaptcha?.siteKey || '';
            
            if (recaptchaSiteKey && typeof grecaptcha !== 'undefined') {
                // reCAPTCHA включена
                handleFormSubmitWithRecaptcha(form, submitBtn, messageEl, recaptchaSiteKey);
            } else {
                // Без reCAPTCHA
                handleFormSubmit(form, submitBtn, messageEl);
            }
        });
    }

    /**
     * Отправка формы с reCAPTCHA v3
     */
    function handleFormSubmitWithRecaptcha(form, submitBtn, messageEl, siteKey) {
        grecaptcha.ready(function() {
            grecaptcha.execute(siteKey, {action: 'contact_form'}).then(function(token) {
                // Добавляем токен в форму
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'recaptcha_response';
                hiddenInput.value = token;
                form.appendChild(hiddenInput);

                // Отправляем форму
                handleFormSubmit(form, submitBtn, messageEl);
            });
        });
    }

    /**
     * Отправка формы
     */
    function handleFormSubmit(form, submitBtn, messageEl) {
        // Собираем данные
        const formData = new FormData(form);
        formData.append('action', 'atk_contact_form');
        formData.append('contact_nonce', window.atkVed?.nonce || '');

        // Отправляем
        fetch(window.atkVed?.ajaxUrl || '/wp-admin/admin-ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            messageEl.style.display = 'block';

            if (data.success) {
                messageEl.className = 'form-message success';
                messageEl.textContent = data.data.message;
                form.reset();
            } else {
                messageEl.className = 'form-message error';
                messageEl.textContent = data.data.message;
            }
        })
        .catch(() => {
            messageEl.style.display = 'block';
            messageEl.className = 'form-message error';
            messageEl.textContent = 'Ошибка соединения. Попробуйте позже.';
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Отправить заявку';
        });
    }

    // Запуск после загрузки DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
