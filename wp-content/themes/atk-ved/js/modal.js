/**
 * Модальные окна и улучшенные уведомления
 */

(function($) {
    'use strict';
    
    // Создание модального окна
    function createModal(title, content, type = 'default') {
        const modalHTML = `
            <div class="atk-modal-overlay">
                <div class="atk-modal atk-modal-${type}">
                    <button class="atk-modal-close" aria-label="Закрыть">&times;</button>
                    <div class="atk-modal-header">
                        <h3>${title}</h3>
                    </div>
                    <div class="atk-modal-body">
                        ${content}
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHTML);
        
        setTimeout(() => {
            $('.atk-modal-overlay').addClass('active');
        }, 10);
        
        // Закрытие по клику на overlay или кнопку
        $('.atk-modal-overlay').on('click', function(e) {
            if ($(e.target).hasClass('atk-modal-overlay') || $(e.target).hasClass('atk-modal-close')) {
                closeModal();
            }
        });
        
        // Закрытие по ESC
        $(document).on('keydown.modal', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    }
    
    function closeModal() {
        $('.atk-modal-overlay').removeClass('active');
        setTimeout(() => {
            $('.atk-modal-overlay').remove();
            $(document).off('keydown.modal');
        }, 300);
    }
    
    // Toast уведомления
    function showToast(message, type = 'success', duration = 3000) {
        const icons = {
            success: '✓',
            error: '✕',
            info: 'ℹ',
            warning: '⚠'
        };
        
        const toastHTML = `
            <div class="atk-toast atk-toast-${type}">
                <span class="atk-toast-icon">${icons[type]}</span>
                <span class="atk-toast-message">${message}</span>
            </div>
        `;
        
        const $toast = $(toastHTML);
        $('.atk-toast-container').append($toast);
        
        setTimeout(() => {
            $toast.addClass('active');
        }, 10);
        
        setTimeout(() => {
            $toast.removeClass('active');
            setTimeout(() => {
                $toast.remove();
            }, 300);
        }, duration);
    }
    
    // Модальное окно с формой заявки
    function openRequestModal() {
        const formHTML = `
            <form class="atk-modal-form" id="modalRequestForm">
                <div class="form-group">
                    <label for="modal-name">Ваше имя *</label>
                    <input type="text" id="modal-name" name="name" required>
                    <span class="error-message"></span>
                </div>
                <div class="form-group">
                    <label for="modal-phone">Телефон *</label>
                    <input type="tel" id="modal-phone" name="phone" required>
                    <span class="error-message"></span>
                </div>
                <div class="form-group">
                    <label for="modal-message">Ваш вопрос</label>
                    <textarea id="modal-message" name="message" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="privacy" required>
                        <span>Согласен с политикой конфиденциальности *</span>
                    </label>
                    <span class="error-message"></span>
                </div>
                <button type="submit" class="cta-button">
                    <span class="button-text">Отправить заявку</span>
                    <span class="button-loader"></span>
                </button>
            </form>
        `;
        
        createModal('Оставить заявку', formHTML, 'form');
        
        // Обработка формы
        $('#modalRequestForm').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            
            // Валидация
            if (!validateForm($form)) {
                return;
            }
            
            // Отправка
            $button.addClass('loading').prop('disabled', true);
            
            $.ajax({
                url: atkVedData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atk_ved_contact_form',
                    nonce: atkVedData.nonce,
                    name: $form.find('[name="name"]').val(),
                    phone: $form.find('[name="phone"]').val(),
                    email: $form.find('[name="phone"]').val() + '@placeholder.com',
                    message: $form.find('[name="message"]').val() || 'Запрос через модальное окно'
                },
                success: function(response) {
                    if (response.success) {
                        closeModal();
                        showToast('Спасибо! Мы свяжемся с вами в ближайшее время.', 'success', 4000);
                    } else {
                        showToast('Произошла ошибка. Попробуйте позже.', 'error');
                    }
                },
                error: function() {
                    showToast('Ошибка соединения. Попробуйте позже.', 'error');
                },
                complete: function() {
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        });
        
        // Маска для телефона
        $('#modal-phone').on('input', function() {
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
        });
    }
    
    // Валидация формы
    function validateForm($form) {
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
    
    // Инициализация
    $(document).ready(function() {
        // Создание контейнера для toast
        if (!$('.atk-toast-container').length) {
            $('body').append('<div class="atk-toast-container"></div>');
        }
        
        // Открытие модального окна по клику на кнопки
        $(document).on('click', '.cta-button, .open-modal', function(e) {
            const href = $(this).attr('href');
            if (!href || href === '#' || href === '#contact') {
                e.preventDefault();
                openRequestModal();
            }
        });
        
        // Замена alert на toast в существующих формах
        window.atkShowToast = showToast;
        window.atkOpenModal = openRequestModal;
    });
    
})(jQuery);
