/**
 * Расширенный калькулятор доставки
 * 
 * @package ATK_VED
 * @since 2.0.0
 */

(function($) {
    'use strict';
    
    let calculationData = null;
    
    /**
     * Инициализация
     */
    $(document).ready(function() {
        initCalculator();
    });
    
    /**
     * Инициализация калькулятора
     */
    function initCalculator() {
        const $form = $('#advancedCalculatorForm');
        const $results = $('#calculatorResults');
        const $loader = $('#calculatorLoader');
        const $error = $('#calculatorError');
        
        // Отправка формы
        $form.on('submit', function(e) {
            e.preventDefault();
            calculateDelivery();
        });
        
        // Сброс формы
        $form.on('reset', function() {
            setTimeout(function() {
                $results.hide();
                $error.hide();
                calculationData = null;
            }, 10);
        });
        
        // Экспорт в PDF
        $('#exportPdfBtn').on('click', function() {
            exportToPDF();
        });
        
        // Форматирование числовых полей
        $('input[type="number"]').on('input', function() {
            const value = $(this).val();
            if (value && parseFloat(value) < 0) {
                $(this).val(0);
            }
        });
    }
    
    /**
     * Расчет стоимости доставки
     */
    function calculateDelivery() {
        const $form = $('#advancedCalculatorForm');
        const $results = $('#calculatorResults');
        const $loader = $('#calculatorLoader');
        const $error = $('#calculatorError');
        const nonce = $('.advanced-calculator').data('nonce');
        
        // Валидация
        if (!$form[0].checkValidity()) {
            $form[0].reportValidity();
            return;
        }
        
        // Сбор данных
        const formData = {
            action: 'atk_ved_calculate_advanced',
            nonce: nonce,
            weight: parseFloat($('#adv_weight').val()),
            volume: parseFloat($('#adv_volume').val()) || 0,
            product_value: parseFloat($('#adv_value').val()),
            category: $('#adv_category').val(),
            from_city: $('#adv_from').val(),
            to_city: $('#adv_to').val(),
            method: $('input[name="method"]:checked').val(),
            insurance: $('input[name="insurance"]').is(':checked') ? 1 : 0
        };
        
        // Показать загрузку
        $results.hide();
        $error.hide();
        $loader.show();
        
        // Прокрутка к результатам
        $('html, body').animate({
            scrollTop: $loader.offset().top - 100
        }, 500);
        
        // AJAX запрос
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                $loader.hide();
                
                if (response.success) {
                    calculationData = response.data;
                    displayResults(response.data);
                    $results.fadeIn();
                } else {
                    showError(response.data.message || 'Ошибка расчета');
                }
            },
            error: function(xhr, status, error) {
                $loader.hide();
                showError('Ошибка соединения с сервером');
                console.error('AJAX Error:', error);
            }
        });
    }
    
    /**
     * Отображение результатов
     */
    function displayResults(data) {
        const $content = $('#resultsContent');
        const calculations = data.calculations || [];
        const input = data.input_data || {};
        
        if (calculations.length === 0) {
            $content.html('<p class="no-results">Нет доступных вариантов доставки</p>');
            return;
        }
        
        let html = '';
        
        // Информация о грузе
        html += '<div class="results-info">';
        html += '<h4>Параметры груза</h4>';
        html += '<div class="info-grid">';
        html += `<div class="info-item">
            <span class="info-label">Вес:</span>
            <span class="info-value">${input.weight} кг</span>
        </div>`;
        html += `<div class="info-item">
            <span class="info-label">Объем:</span>
            <span class="info-value">${input.volume} м³</span>
        </div>`;
        html += `<div class="info-item">
            <span class="info-label">Расчетный вес:</span>
            <span class="info-value">${input.chargeable_weight} кг</span>
        </div>`;
        html += `<div class="info-item">
            <span class="info-label">Стоимость товара:</span>
            <span class="info-value">${formatNumber(input.product_value)} ₽</span>
        </div>`;
        html += '</div>';
        html += '</div>';
        
        // Варианты доставки
        html += '<div class="results-variants">';
        
        calculations.forEach((calc, index) => {
            const isRecommended = index === 0;
            
            html += `<div class="result-card ${isRecommended ? 'recommended' : ''}">`;
            
            if (isRecommended) {
                html += '<div class="recommended-badge">✓ Рекомендуем</div>';
            }
            
            html += '<div class="result-header">';
            html += `<div class="result-method">
                <span class="method-icon">${calc.icon}</span>
                <span class="method-name">${calc.method_name}</span>
            </div>`;
            html += `<div class="result-route">${calc.from} → ${calc.to}</div>`;
            html += `<div class="result-days">${calc.days_min}-${calc.days_max} дней</div>`;
            html += '</div>';
            
            html += '<div class="result-body">';
            html += '<table class="result-breakdown">';
            html += `<tr>
                <td>Доставка (${calc.chargeable_weight} кг × $${calc.rate_per_kg})</td>
                <td class="amount">${formatNumber(calc.delivery_cost_rub)} ₽</td>
            </tr>`;
            html += `<tr>
                <td>Таможенная пошлина</td>
                <td class="amount">${formatNumber(calc.customs_duty)} ₽</td>
            </tr>`;
            html += `<tr>
                <td>НДС (20%)</td>
                <td class="amount">${formatNumber(calc.vat)} ₽</td>
            </tr>`;
            
            if (calc.insurance_cost > 0) {
                html += `<tr>
                    <td>Страхование (3%)</td>
                    <td class="amount">${formatNumber(calc.insurance_cost)} ₽</td>
                </tr>`;
            }
            
            html += `<tr>
                <td>Услуги компании</td>
                <td class="amount">${formatNumber(calc.service_fee)} ₽</td>
            </tr>`;
            html += `<tr class="total-row">
                <td><strong>ИТОГО:</strong></td>
                <td class="amount total">${formatNumber(calc.total_rub)} ₽</td>
            </tr>`;
            html += '</table>';
            html += '</div>';
            
            html += '<div class="result-footer">';
            html += `<button class="calc-btn calc-btn-order" onclick="atkOpenModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Заказать доставку
            </button>`;
            html += '</div>';
            
            html += '</div>';
        });
        
        html += '</div>';
        
        // Примечание
        html += '<div class="results-note">';
        html += '<strong>Важно:</strong> Расчет является предварительным. ';
        html += 'Точную стоимость уточняйте у менеджера. ';
        html += 'Курс валют и тарифы могут меняться.';
        html += '</div>';
        
        $content.html(html);
    }
    
    /**
     * Экспорт в PDF
     */
    function exportToPDF() {
        if (!calculationData) {
            showError('Нет данных для экспорта');
            return;
        }
        
        const $btn = $('#exportPdfBtn');
        const originalText = $btn.find('span').text();
        const nonce = $('.advanced-calculator').data('nonce');
        
        // Показать загрузку
        $btn.prop('disabled', true);
        $btn.find('span').text('Генерация PDF...');
        
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'atk_ved_generate_pdf',
                nonce: nonce,
                calculation_data: JSON.stringify(calculationData)
            },
            success: function(response) {
                $btn.prop('disabled', false);
                $btn.find('span').text(originalText);
                
                if (response.success) {
                    // Открыть PDF в новой вкладке
                    window.open(response.data.pdf_url, '_blank');
                    
                    // Показать уведомление
                    showSuccess('PDF готов к скачиванию');
                } else {
                    showError(response.data.message || 'Ошибка генерации PDF');
                }
            },
            error: function() {
                $btn.prop('disabled', false);
                $btn.find('span').text(originalText);
                showError('Ошибка соединения с сервером');
            }
        });
    }
    
    /**
     * Показать ошибку
     */
    function showError(message) {
        const $error = $('#calculatorError');
        $error.html(`
            <div class="error-icon">⚠️</div>
            <div class="error-message">${message}</div>
        `).fadeIn();
        
        $('html, body').animate({
            scrollTop: $error.offset().top - 100
        }, 500);
    }
    
    /**
     * Показать успех
     */
    function showSuccess(message) {
        const $success = $('<div class="calc-success"></div>');
        $success.html(`
            <div class="success-icon">✓</div>
            <div class="success-message">${message}</div>
        `);
        
        $('.advanced-calculator').prepend($success);
        $success.fadeIn();
        
        setTimeout(function() {
            $success.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    /**
     * Форматирование числа
     */
    function formatNumber(num) {
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
    
})(jQuery);
