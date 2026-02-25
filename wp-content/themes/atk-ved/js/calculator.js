/**
 * Калькулятор стоимости доставки
 */

(function($) {
    'use strict';
    
    // Тарифы доставки (руб/кг)
    const rates = {
        air: { base: 8, name: 'Авиа', days: '5-10 дней' },
        sea: { base: 2, name: 'Море', days: '30-45 дней' },
        rail: { base: 4, name: 'ЖД', days: '15-20 дней' }
    };
    
    // Коэффициенты
    const coefficients = {
        insurance: 0.02, // 2% от стоимости товара
        customs: 0.15,   // 15% от стоимости товара
        service: 500     // фиксированная комиссия
    };
    
    function calculateDelivery() {
        const weight = parseFloat($('#calc-weight').val()) || 0;
        const volume = parseFloat($('#calc-volume').val()) || 0;
        const cost = parseFloat($('#calc-cost').val()) || 0;
        const method = $('#calc-method').val();
        
        if (weight <= 0 || cost <= 0) {
            $('#calc-result').html('<p class="calc-error">Заполните все поля</p>');
            return;
        }
        
        // Объемный вес (1 м³ = 167 кг для авиа, 1000 кг для моря/жд)
        const volumeWeight = method === 'air' ? volume * 167 : volume * 1000;
        const chargeableWeight = Math.max(weight, volumeWeight);
        
        // Расчет стоимости
        const deliveryCost = chargeableWeight * rates[method].base;
        const insurance = cost * coefficients.insurance;
        const customs = cost * coefficients.customs;
        const service = coefficients.service;
        
        const total = deliveryCost + insurance + customs + service;
        
        // Вывод результата
        const resultHTML = `
            <div class="calc-result-card">
                <div class="calc-result-header">
                    <h4>${rates[method].name}</h4>
                    <span class="calc-result-days">${rates[method].days}</span>
                </div>
                <div class="calc-result-breakdown">
                    <div class="calc-result-item">
                        <span>Доставка (${chargeableWeight.toFixed(1)} кг)</span>
                        <span>${deliveryCost.toFixed(0)} ₽</span>
                    </div>
                    <div class="calc-result-item">
                        <span>Страхование (2%)</span>
                        <span>${insurance.toFixed(0)} ₽</span>
                    </div>
                    <div class="calc-result-item">
                        <span>Таможня (15%)</span>
                        <span>${customs.toFixed(0)} ₽</span>
                    </div>
                    <div class="calc-result-item">
                        <span>Услуги компании</span>
                        <span>${service} ₽</span>
                    </div>
                </div>
                <div class="calc-result-total">
                    <span>Итого:</span>
                    <span class="calc-result-price">${total.toFixed(0)} ₽</span>
                </div>
                <button class="cta-button calc-order-btn" onclick="atkOpenModal()">
                    Заказать доставку
                </button>
                <p class="calc-result-note">* Расчет приблизительный. Точную стоимость уточняйте у менеджера.</p>
            </div>
        `;
        
        $('#calc-result').html(resultHTML).addClass('active');
    }
    
    // Сравнение всех методов
    function compareAllMethods() {
        const weight = parseFloat($('#calc-weight').val()) || 0;
        const volume = parseFloat($('#calc-volume').val()) || 0;
        const cost = parseFloat($('#calc-cost').val()) || 0;
        
        if (weight <= 0 || cost <= 0) {
            $('#calc-result').html('<p class="calc-error">Заполните все поля</p>');
            return;
        }
        
        let compareHTML = '<div class="calc-compare-grid">';
        
        Object.keys(rates).forEach(method => {
            const volumeWeight = method === 'air' ? volume * 167 : volume * 1000;
            const chargeableWeight = Math.max(weight, volumeWeight);
            const deliveryCost = chargeableWeight * rates[method].base;
            const insurance = cost * coefficients.insurance;
            const customs = cost * coefficients.customs;
            const service = coefficients.service;
            const total = deliveryCost + insurance + customs + service;
            
            compareHTML += `
                <div class="calc-compare-card">
                    <div class="calc-compare-icon">${method === 'air' ? '✈️' : method === 'sea' ? '🚢' : '🚂'}</div>
                    <h4>${rates[method].name}</h4>
                    <p class="calc-compare-days">${rates[method].days}</p>
                    <div class="calc-compare-price">${total.toFixed(0)} ₽</div>
                    <button class="cta-button secondary" onclick="atkOpenModal()">Выбрать</button>
                </div>
            `;
        });
        
        compareHTML += '</div>';
        $('#calc-result').html(compareHTML).addClass('active');
    }
    
    // Инициализация
    $(document).ready(function() {
        // Расчет при изменении полей
        $('#calculator-form input, #calculator-form select').on('change input', function() {
            const allFilled = $('#calc-weight').val() && $('#calc-cost').val();
            if (allFilled) {
                calculateDelivery();
            }
        });
        
        // Кнопка расчета
        $('#calc-submit').on('click', function(e) {
            e.preventDefault();
            calculateDelivery();
        });
        
        // Кнопка сравнения
        $('#calc-compare').on('click', function(e) {
            e.preventDefault();
            compareAllMethods();
        });
        
        // Форматирование чисел
        $('#calc-weight, #calc-volume, #calc-cost').on('input', function() {
            let value = $(this).val().replace(/[^\d.]/g, '');
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            $(this).val(value);
        });
    });
    
})(jQuery);
