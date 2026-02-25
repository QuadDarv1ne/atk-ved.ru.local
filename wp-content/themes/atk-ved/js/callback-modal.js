/**
 * Модальное окно обратного звонка
 */

(function($) {
    'use strict';
    
    function initCallbackModal() {
        // Создаем плавающую кнопку
        const floatButtonHTML = `
            <button class="callback-float-btn" aria-label="Заказать обратный звонок">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
            </button>
        `;
        
        $('body').append(floatButtonHTML);
        
        // Создаем модальное окно
        const modalHTML = `
            <div class="callback-modal-overlay">
                <div class="callback-modal">
                    <div class="callback-modal-header">
                        <button class="callback-modal-close" aria-label="Закрыть">&times;</button>
                        <div class="callback-modal-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <h3>Заказать обратный звонок</h3>
                        <p>Оставьте свой номер и мы перезвоним вам в течение 15 минут</p>
                    </div>
                    <div class="callback-modal-body">
                        <div class="callback-benefits">
                            <div class="callback-benefit">
                                <div class="callback-benefit-icon">⚡</div>
                                <span>Быстрый ответ в течение 15 минут</span>
                            </div>
                            <div class="callback-benefit">
                                <div class="callback-benefit-icon">💼</div>
                                <span>Бесплатная консультация специалиста</span>
                            </div>
                            <div class="callback-benefit">
                                <div class="callback-benefit-icon">🎁</div>
                                <span>Специальное предложение для новых клиентов</span>
                            </div>
                        </div>
                        <form class="callback-form" id="callbackForm">
                            <div class="form-group">
                                <label for="callback-name">Ваше имя *</label>
                                <input type="text" id="callback-name" name="name" placeholder="Иван" required>
                                <span class="error-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="callback-phone">Телефон *</label>
                                <input type="tel" id="callback-phone" name="phone" placeholder="+7 (___) ___-__-__" required>
                                <span class="error-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="callback-time">Удобное время для звонка</label>
                                <select id="callback-time" name="time">
                                    <option value="any">В любое время</option>
                                    <option value="morning">Утро (9:00 - 12:00)</option>
                                    <option value="afternoon">День (12:00 - 15:00)</option>
                                    <option value="evening">Вечер (15:00 - 18:00)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="privacy" required>
                                    <span>Согласен с <a href="#" style="color: #e31e24; text-decoration: underline;">политикой конфиденциальности</a> *</span>
                                </label>
                                <span class="error-message"></span>
                            </div>
                            <button type="submit" class="submit-btn">
                                <span class="button-text">Заказать звонок</span>
                                <span class="button-loader"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHTML);
        
        // Обработчики открытия/закрытия
        $('.callback-float-btn').on('click', openCallbackModal);
        $('.callback-modal-close').on('click', closeCallbackModal);
        
        $('.callback-modal-overlay').on('click', function(e) {
            if ($(e.target).hasClass('callback-modal-overlay')) {
                closeCallbackModal();
            }
        });
        
        // Закрытие по ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('.callback-modal-overlay').hasClass('active')) {
                closeCallbackModal();
            }
        });
        
        // Обработка формы
        $('#callbackForm').on('submit', handleCallbackSubmit);
        
        // Маска для телефона
        $('#callback-phone').on('input', formatPhoneNumber);
        
        // Показываем кнопку при прокрутке
        showFloatButtonOnScroll();
    }
    
    function openCallbackModal() {
        $('.callback-modal-overlay').addClass('active');
        $('body').css('overflow', 'hidden');
        
        // Фокус на первое поле
        setTimeout(function() {
            $('#callback-name').focus();
        }, 300);
    }
    
    function closeCallbackModal() {
        $('.callback-modal-overlay').removeClass('active');
        $('body').css('overflow', '');
        
        // Очистка формы
        setTimeout(function() {
            $('#callbackForm')[0].reset();
            $('.error-message').text('');
            $('.error').removeClass('error');
        }, 300);
    }
    
    function handleCallbackSubmit(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $button = $form.find('.submit-btn');
        
        // Валидация
        if (!validateCallbackForm($form)) {
            return;
        }
        
        // Отправка
        $button.addClass('loading').prop('disabled', true);
        
        const formData = {
            action: 'atk_ved_contact_form',
            nonce: atkVedData.nonce,
            name: $form.find('[name="name"]').val(),
            phone: $form.find('[name="phone"]').val(),
            email: $form.find('[name="phone"]').val() + '@placeholder.com',
            message: 'Запрос обратного звонка. Удобное время: ' + $form.find('[name="time"] option:selected').text()
        };
        
        $.ajax({
            url: atkVedData.ajaxUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    closeCallbackModal();
                    if (typeof atkShowToast === 'function') {
                        atkShowToast('Спасибо! Мы перезвоним вам в течение 15 минут.', 'success', 5000);
                    } else {
                        alert('Спасибо! Мы перезвоним вам в течение 15 минут.');
                    }
                } else {
                    if (typeof atkShowToast === 'function') {
                        atkShowToast('Произошла ошибка. Попробуйте позже или позвоните нам.', 'error');
                    } else {
                        alert('Произошла ошибка. Попробуйте позже или позвоните нам.');
                    }
                }
            },
            error: function() {
                if (typeof atkShowToast === 'function') {
                    atkShowToast('Ошибка соединения. Попробуйте позже.', 'error');
                } else {
                    alert('Ошибка соединения. Попробуйте позже.');
                }
            },
            complete: function() {
                $button.removeClass('loading').prop('disabled', false);
            }
        });
    }
    
    function validateCallbackForm($form) {
        let isValid = true;
        
        $form.find('.error-message').text('');
        $form.find('.error').removeClass('error');
        
        // Имя
        const $name = $form.find('[name="name"]');
        if ($name.val().trim().length < 2) {
            showFieldError($name, 'Введите корректное имя');
            isValid = false;
        }
        
        // Телефон
        const $phone = $form.find('[name="phone"]');
        const phoneValue = $phone.val().replace(/\D/g, '');
        if (phoneValue.length < 11) {
            showFieldError($phone, 'Введите корректный номер телефона');
            isValid = false;
        }
        
        // Согласие
        const $privacy = $form.find('[name="privacy"]');
        if (!$privacy.is(':checked')) {
            showFieldError($privacy, 'Необходимо согласие с политикой');
            isValid = false;
        }
        
        return isValid;
    }
    
    function showFieldError($field, message) {
        $field.addClass('error');
        $field.closest('.form-group').find('.error-message').text(message);
    }
    
    function formatPhoneNumber() {
        let value = $(this).val().replace(/\D/g, '');
        
        if (value.length > 0) {
            if (value[0] === '7' || value[0] === '8') {
                value = '7' + value.substring(1);
            }
            
            let formatted = '+7';
            if (value.length > 1) {
                formatted += ' (' + value.substring(1, 4);
            }
            if (value.length >= 5) {
                formatted += ') ' + value.substring(4, 7);
            }
            if (value.length >= 8) {
                formatted += '-' + value.substring(7, 9);
            }
            if (value.length >= 10) {
                formatted += '-' + value.substring(9, 11);
            }
            
            $(this).val(formatted);
        }
    }
    
    function showFloatButtonOnScroll() {
        const $button = $('.callback-float-btn');
        
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 500) {
                $button.fadeIn();
            } else {
                $button.fadeOut();
            }
        });
        
        // Скрываем кнопку изначально
        $button.hide();
    }
    
    // Инициализация
    $(document).ready(function() {
        initCallbackModal();
    });
    
})(jQuery);
