/**
 * Калькулятор стоимости доставки (Vanilla JS)
 * 
 * @package ATK_VED
 */

(function() {
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
    
    // Вспомогательные функции
    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => document.querySelectorAll(selector);
    
    function calculateDelivery() {
        const weight = parseFloat($('#calc-weight')?.value) || 0;
        const volume = parseFloat($('#calc-volume')?.value) || 0;
        const cost = parseFloat($('#calc-cost')?.value) || 0;
        const method = $('#calc-method')?.value || 'air';
        
        const resultEl = $('#calc-result');
        if (!resultEl) return;
        
        if (weight <= 0 || cost <= 0) {
            resultEl.innerHTML = '<p class="calc-error">Заполните все поля</p>';
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
        
        resultEl.innerHTML = resultHTML;
        resultEl.classList.add('active');
    }
    
    // Сравнение всех методов
    function compareAllMethods() {
        const weight = parseFloat($('#calc-weight')?.value) || 0;
        const volume = parseFloat($('#calc-volume')?.value) || 0;
        const cost = parseFloat($('#calc-cost')?.value) || 0;
        
        const resultEl = $('#calc-result');
        if (!resultEl) return;
        
        if (weight <= 0 || cost <= 0) {
            resultEl.innerHTML = '<p class="calc-error">Заполните все поля</p>';
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
        resultEl.innerHTML = compareHTML;
        resultEl.classList.add('active');
    }
    
    // Форматирование чисел
    function formatNumberInput(input) {
        let value = input.value.replace(/[^\d.]/g, '');
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        input.value = value;
    }
    
    // Инициализация
    function init() {
        const form = $('#calculator-form');
        if (!form) return;
        
        // Расчет при изменении полей
        const inputs = $$('#calculator-form input, #calculator-form select');
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                const weightEl = $('#calc-weight');
                const costEl = $('#calc-cost');
                if (weightEl?.value && costEl?.value) {
                    calculateDelivery();
                }
            });
            
            input.addEventListener('input', () => {
                const weightEl = $('#calc-weight');
                const costEl = $('#calc-cost');
                if (weightEl?.value && costEl?.value) {
                    calculateDelivery();
                }
            });
        });
        
        // Кнопка расчета
        const submitBtn = $('#calc-submit');
        if (submitBtn) {
            submitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                calculateDelivery();
            });
        }
        
        // Кнопка сравнения
        const compareBtn = $('#calc-compare');
        if (compareBtn) {
            compareBtn.addEventListener('click', (e) => {
                e.preventDefault();
                compareAllMethods();
            });
        }
        
        // Форматирование чисел
        const numberInputs = $$('#calc-weight, #calc-volume, #calc-cost');
        numberInputs.forEach(input => {
            input.addEventListener('input', () => formatNumberInput(input));
        });
    }
    
    // Запуск после загрузки DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();
