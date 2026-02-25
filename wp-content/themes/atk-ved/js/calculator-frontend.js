/**
 * JavaScript для калькулятора доставки
 * 
 * @package ATK_VED
 * @since 2.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initCalculator();
    });

    function initCalculator() {
        const $calculator = $('.delivery-calculator');
        if (!$calculator.length) return;

        const $form = $('#deliveryCalculatorForm');
        const $results = $('#calculatorResults');
        const $resultsContent = $results.find('.calculator-results-content');
        const $error = $('#calculatorError');
        const $loader = $results.find('.calculator-loader');

        $form.on('submit', function(e) {
            e.preventDefault();
            
            // Сброс
            $error.hide();
            $results.show();
            $loader.show();
            $resultsContent.hide();

            // Сбор данных
            const formData = {
                action: 'atk_ved_calculate_delivery',
                nonce: $calculator.data('nonce'),
                weight: parseFloat($form.find('[name="weight"]').val()) || 0,
                volume: parseFloat($form.find('[name="volume"]').val()) || 0,
                from_city: $form.find('[name="from_city"]').val(),
                to_city: $form.find('[name="to_city"]').val(),
                method: $form.find('[name="method"]:checked').val(),
                insurance: $form.find('[name="insurance"]').is(':checked') ? '1' : '0',
                customs: $form.find('[name="customs"]').is(':checked') ? '1' : '0'
            };

            // Валидация
            if (formData.weight <= 0) {
                showError('Пожалуйста, укажите вес груза');
                return;
            }

            // Отправка
            $.ajax({
                url: atkVedData.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $loader.hide();
                    
                    if (response.success) {
                        displayResults(response.data);
                    } else {
                        showError(response.data.message || 'Ошибка при расчёте');
                    }
                },
                error: function() {
                    $loader.hide();
                    showError('Ошибка соединения. Попробуйте позже.');
                }
            });
        });

        function displayResults(data) {
            let html = '';

            // Информация о грузе
            html += '<div class="calculator-cargo-info">';
            html += '<p><strong>Вес:</strong> ' + data.weight + ' кг</p>';
            html += '<p><strong>Объём:</strong> ' + data.volume + ' м³</p>';
            if (data.volumetric_weight > 0) {
                html += '<p><strong>Объёмный вес:</strong> ' + data.volumetric_weight + ' кг</p>';
            }
            html += '</div>';

            // Карточки результатов
            const methods = data.calculations;
            for (const key in methods) {
                const calc = methods[key];
                const isRecommended = key === data.recommended;
                
                html += '<div class="calculator-result-card' + (isRecommended ? ' recommended' : '') + '">';
                
                // Header
                html += '<div class="calculator-result-header">';
                html += '<span class="calculator-result-method">' + calc.method_name + '</span>';
                html += '<span class="calculator-result-days">' + calc.days_min + '–' + calc.days_max + ' дней</span>';
                html += '</div>';
                
                // Cost
                html += '<div class="calculator-result-cost">';
                html += '$' + calc.cost_usd;
                html += '<small> ≈ ' + calc.cost_rub + ' ₽</small>';
                html += '</div>';
                
                // Breakdown
                html += '<div class="calculator-breakdown">';
                html += '<div class="calculator-breakdown-item">';
                html += '<span>Базовая стоимость:</span>';
                html += '<span>$' + calc.breakdown.base_cost + '</span>';
                html += '</div>';
                
                if (calc.insurance_cost > 0) {
                    html += '<div class="calculator-breakdown-item">';
                    html += '<span>Страховка:</span>';
                    html += '<span>$' + calc.insurance_cost + '</span>';
                    html += '</div>';
                }
                
                if (calc.customs_cost > 0) {
                    html += '<div class="calculator-breakdown-item">';
                    html += '<span>Таможня:</span>';
                    html += '<span>$' + calc.customs_cost + '</span>';
                    html += '</div>';
                }
                
                html += '<div class="calculator-breakdown-item total">';
                html += '<span>Итого:</span>';
                html += '<span>$' + calc.cost + '</span>';
                html += '</div>';
                html += '</div>';
                
                html += '</div>';
            }

            // Кнопка заказа
            html += '<div class="calculator-actions">';
            html += '<a href="#contact" class="cta-button" style="display: inline-flex; align-items: center; gap: 10px;">';
            html += '<span>Заказать доставку</span>';
            html += '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
            html += '<path d="M5 12h14M12 5l7 7-7 7"/>';
            html += '</svg>';
            html += '</a>';
            html += '</div>';

            $resultsContent.html(html).fadeIn();
            
            // Отправка события в аналитику
            if (typeof ym !== 'undefined') {
                ym(atkVedData.metrikaId || 0, 'reachGoal', 'calculator_used');
            }
            if (typeof gtag !== 'undefined') {
                gtag('event', 'calculator_used', {
                    'event_category': 'Calculator',
                    'event_label': 'Delivery Calculation'
                });
            }
        }

        function showError(message) {
            $error.text(message).fadeIn();
            $results.hide();
        }
    }

})(jQuery);
