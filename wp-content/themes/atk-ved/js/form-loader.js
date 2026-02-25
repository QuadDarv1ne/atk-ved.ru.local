/**
 * Индикатор загрузки для форм
 */

(function($) {
    'use strict';
    
    // Создание оверлея загрузки
    function createFormLoader($form, message = 'Отправка...') {
        const loaderHTML = `
            <div class="form-loading-overlay">
                <div class="form-loader"></div>
                <div class="form-loader-text">${message}</div>
                <div class="form-progress-bar">
                    <div class="form-progress-fill"></div>
                </div>
            </div>
        `;
        
        // Добавляем класс для позиционирования
        $form.addClass('form-with-loader');
        
        // Удаляем старый оверлей если есть
        $form.find('.form-loading-overlay').remove();
        
        // Добавляем новый
        $form.append(loaderHTML);
    }
    
    // Показать индикатор загрузки
    function showFormLoader($form, message) {
        if (!$form.find('.form-loading-overlay').length) {
            createFormLoader($form, message);
        }
        
        setTimeout(function() {
            $form.find('.form-loading-overlay').addClass('active');
        }, 10);
    }
    
    // Скрыть индикатор загрузки
    function hideFormLoader($form) {
        $form.find('.form-loading-overlay').removeClass('active');
        
        setTimeout(function() {
            $form.find('.form-loading-overlay').remove();
        }, 300);
    }
    
    // Показать успешную отправку
    function showFormSuccess($form, message = 'Спасибо!', subtext = 'Ваша заявка принята') {
        const successHTML = `
            <div class="form-success-overlay">
                <div class="form-success-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div class="form-success-text">${message}</div>
                <div class="form-success-subtext">${subtext}</div>
            </div>
        `;
        
        // Скрываем загрузку
        hideFormLoader($form);
        
        // Показываем успех
        $form.append(successHTML);
        
        setTimeout(function() {
            $form.find('.form-success-overlay').addClass('active');
        }, 10);
        
        // Автоматически скрываем через 3 секунды
        setTimeout(function() {
            $form.find('.form-success-overlay').removeClass('active');
            setTimeout(function() {
                $form.find('.form-success-overlay').remove();
                $form[0].reset();
            }, 300);
        }, 3000);
    }
    
    // Показать ошибку
    function showFormError($form, message = 'Ошибка!', subtext = 'Попробуйте еще раз') {
        const errorHTML = `
            <div class="form-error-overlay">
                <div class="form-error-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
                <div class="form-error-text">${message}</div>
                <div class="form-error-subtext">${subtext}</div>
                <button class="form-error-retry">Попробовать снова</button>
            </div>
        `;
        
        // Скрываем загрузку
        hideFormLoader($form);
        
        // Показываем ошибку
        $form.append(errorHTML);
        
        setTimeout(function() {
            $form.find('.form-error-overlay').addClass('active');
        }, 10);
        
        // Обработчик кнопки повтора
        $form.find('.form-error-retry').on('click', function() {
            $form.find('.form-error-overlay').removeClass('active');
            setTimeout(function() {
                $form.find('.form-error-overlay').remove();
            }, 300);
        });
        
        // Автоматически скрываем через 5 секунд
        setTimeout(function() {
            $form.find('.form-error-overlay').removeClass('active');
            setTimeout(function() {
                $form.find('.form-error-overlay').remove();
            }, 300);
        }, 5000);
    }
    
    // Интеграция с существующими формами
    function enhanceExistingForms() {
        // Форма быстрого поиска
        $('#quickSearchForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            handleFormSubmit($(this), 'atk_ved_quick_search');
        });
        
        // Форма обратной связи
        $('#contactForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            handleFormSubmit($(this), 'atk_ved_contact_form');
        });
    }
    
    function handleFormSubmit($form, action) {
        // Валидация
        const name = $form.find('input[name="name"]').val().trim();
        const phone = $form.find('input[name="phone"]').val().trim();
        const privacy = $form.find('input[name="privacy"]').is(':checked');
        
        if (!name || !phone || !privacy) {
            if (typeof atkShowToast === 'function') {
                atkShowToast('Пожалуйста, заполните все поля', 'warning');
            }
            return;
        }
        
        // Показываем загрузку
        showFormLoader($form, 'Отправка заявки...');
        
        // Отправка данных
        const formData = {
            action: action,
            nonce: atkVedData.nonce,
            name: name,
            phone: phone,
            email: phone + '@placeholder.com',
            message: $form.find('textarea[name="message"]').val() || 'Запрос через форму'
        };
        
        $.ajax({
            url: atkVedData.ajaxUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showFormSuccess(
                        $form,
                        'Спасибо за заявку!',
                        'Мы свяжемся с вами в ближайшее время'
                    );
                    
                    // Дополнительное уведомление
                    if (typeof atkShowToast === 'function') {
                        setTimeout(function() {
                            atkShowToast('Заявка успешно отправлена', 'success', 3000);
                        }, 500);
                    }
                } else {
                    showFormError(
                        $form,
                        'Ошибка отправки',
                        'Проверьте данные и попробуйте снова'
                    );
                }
            },
            error: function() {
                showFormError(
                    $form,
                    'Ошибка соединения',
                    'Проверьте интернет-соединение'
                );
            }
        });
    }
    
    // Экспорт функций для использования в других скриптах
    window.atkFormLoader = {
        show: showFormLoader,
        hide: hideFormLoader,
        success: showFormSuccess,
        error: showFormError
    };
    
    // Инициализация
    $(document).ready(function() {
        enhanceExistingForms();
    });
    
})(jQuery);
