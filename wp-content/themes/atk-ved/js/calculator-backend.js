/**
 * Калькулятор доставки - Frontend (Vanilla JS)
 * Работает с backend через AJAX
 *
 * @package ATK_VED
 * @since 3.5.0
 */

(function() {
    'use strict';

    // ===== Helpers =====
    const $ = (selector, context = document) => context.querySelector(selector);
    const $$ = (selector, context = document) => Array.from(context.querySelectorAll(selector));

    // ===== Калькулятор =====
    class DeliveryCalculator {
        constructor(container) {
            this.container = container;
            this.form = $('#deliveryCalculatorForm', container);
            this.results = $('#calculatorResults', container);
            this.resultsContent = $('#calculatorResults .calculator-results-content', container);
            this.error = $('#calculatorError', container);
            this.nonce = container.dataset.nonce || '';

            if (!this.form) return;

            this.init();
        }

        init() {
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        }

        async handleSubmit(e) {
            e.preventDefault();

            // Сброс
            this.hideError();
            this.showLoader();

            // Сбор данных
            const formData = new FormData(this.form);
            const data = {
                action: atkVed.calculator?.action || 'atk_ved_calculate_delivery',
                nonce: atkVed.calculator?.nonce || this.nonce,
                weight: parseFloat(formData.get('weight')) || 0,
                volume: parseFloat(formData.get('volume')) || 0,
                from_city: formData.get('from_city'),
                to_city: formData.get('to_city'),
                method: formData.get('method'),
                insurance: formData.get('insurance') === '1' ? 1 : 0,
                customs: formData.get('customs') === '1' ? 1 : 0
            };

            // Валидация
            if (data.weight <= 0) {
                this.showError(atkVed.i18n?.calcError || 'Укажите вес груза');
                return;
            }

            try {
                const response = await fetch(atkVed.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: data.action,
                        nonce: data.nonce,
                        weight: data.weight,
                        volume: data.volume,
                        from_city: data.from_city,
                        to_city: data.to_city,
                        method: data.method,
                        insurance: data.insurance,
                        customs: data.customs
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.displayResults(result.data);
                } else {
                    this.showError(result.data.message || 'Ошибка расчёта');
                }
            } catch (error) {
                console.error('Calculator error:', error);
                this.showError('Ошибка соединения. Попробуйте позже.');
            }
        }

        displayResults(data) {
            const { calculations, recommended, weight, volume, volumetric_weight, currency } = data;

            let html = '';

            // Заголовок
            html += '<div class="calculator-results-header">';
            html += `<h4>Результаты расчёта</h4>`;
            html += `<p class="calculator-subtitle">Вес: ${weight} кг | Объём: ${volume} м³ | Объёмный вес: ${volumetric_weight} кг</p>`;
            html += '</div>';

            // Карточки с расчётами
            html += '<div class="calculator-cards">';

            for (const [method, calc] of Object.entries(calculations)) {
                const isRecommended = method === recommended;
                const isCheapest = calc.cost === Math.min(...Object.values(calculations).map(c => c.cost));
                const isFastest = calc.days_min === Math.min(...Object.values(calculations).map(c => c.days_min));

                html += `<div class="calculator-card${isRecommended ? ' recommended' : ''}">`;

                // Бейджи
                if (isRecommended) {
                    html += '<div class="calculator-badge badge-recommended">✓ Рекомендуется</div>';
                }
                if (isCheapest) {
                    html += '<div class="calculator-badge badge-cheapest">✓ Дёшево</div>';
                }
                if (isFastest) {
                    html += '<div class="calculator-badge badge-fast">✓ Быстро</div>';
                }

                // Заголовок карточки
                html += '<div class="calculator-card-header">';
                html += `<div class="calculator-method-icon">${this.getMethodIcon(method)}</div>`;
                html += '<div class="calculator-method-info">';
                html += `<h5>${calc.method_name}</h5>`;
                html += `<span class="calculator-days">${calc.days_min}-${calc.days_max} дн.</span>`;
                html += '</div>';
                html += '</div>';

                // Детализация
                html += '<div class="calculator-card-body">';
                html += '<div class="calculator-detail-row">';
                html += `<span>Доставка (${calc.chargeable_weight} кг)</span>`;
                html += `<span>${this.formatPrice(calc.breakdown.base_cost)} ${currency}</span>`;
                html += '</div>';

                if (calc.insurance_cost > 0) {
                    html += '<div class="calculator-detail-row">';
                    html += '<span>Страхование</span>';
                    html += `<span>${this.formatPrice(calc.insurance_cost)} ${currency}</span>`;
                    html += '</div>';
                }

                if (calc.customs_cost > 0) {
                    html += '<div class="calculator-detail-row">';
                    html += '<span>Таможня</span>';
                    html += `<span>${this.formatPrice(calc.customs_cost)} ${currency}</span>`;
                    html += '</div>';
                }

                html += '</div>';

                // Итого
                html += '<div class="calculator-card-footer">';
                html += '<div class="calculator-total">';
                html += '<span>Итого:</span>';
                html += `<span class="calculator-total-price">${this.formatPrice(calc.cost)} ${currency}</span>`;
                html += '</div>';
                html += `<div class="calculator-total-rub">≈ ${this.formatPrice(calc.cost_rub)} ₽</div>`;
                html += '</div>';

                // Кнопка
                html += '<div class="calculator-card-action">';
                html += `<button class="calculator-order-btn" data-method="${method}" data-cost="${calc.cost}">`;
                html += '<span>Заказать</span>';
                html += '</button>';
                html += '</div>';

                html += '</div>';
            }

            html += '</div>';

            // Рекомендация
            html += '<div class="calculator-recommendation">';
            html += '<div class="calculator-recommendation-icon">💡</div>';
            html += '<div class="calculator-recommendation-text">';
            html += `<strong>Рекомендуем: ${calculations[recommended]?.method_name}</strong>`;
            html += `<p>Оптимальное соотношение цены и сроков для вашего груза</p>`;
            html += '</div>';
            html += '</div>';

            // Примечание
            html += '<p class="calculator-note">* Расчет является приблизительным. Точную стоимость уточняйте у менеджера.</p>';

            this.resultsContent.innerHTML = html;
            this.results.style.display = 'block';

            // Навешиваем обработчики на кнопки
            $$('.calculator-order-btn', this.results).forEach(btn => {
                btn.addEventListener('click', () => this.handleOrder(btn.dataset));
            });

            // Analytics
            this.trackCalculation(data);
        }

        handleOrder(data) {
            // Открытие модального окна с предзаполненными данными
            const modal = $('#orderModal');
            if (modal) {
                $('#order_method').value = data.method;
                $('#order_cost').value = data.cost;
                
                if (window.atkOpenModal) {
                    window.atkOpenModal('orderModal');
                }
            }

            // Метрика
            if (typeof ym !== 'undefined') {
                ym(atkVed.metrikaId, 'reachGoal', 'calculator_order');
            }
        }

        trackCalculation(data) {
            // Яндекс.Метрика
            if (typeof ym !== 'undefined') {
                ym(atkVed.metrikaId, 'reachGoal', 'calculator_used', {
                    weight: data.weight,
                    volume: data.volume,
                    recommended: data.recommended
                });
            }

            // Google Analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'calculator_used', {
                    event_category: 'Calculator',
                    event_label: 'Delivery Calculation',
                    value: data.weight
                });
            }
        }

        showLoader() {
            this.results.style.display = 'block';
            $('.calculator-loader', this.results).style.display = 'block';
            this.resultsContent.style.display = 'none';
        }

        hideLoader() {
            $('.calculator-loader', this.results).style.display = 'none';
            this.resultsContent.style.display = 'block';
        }

        showError(message) {
            this.error.textContent = message;
            this.error.style.display = 'block';
            this.results.style.display = 'none';
        }

        hideError() {
            this.error.style.display = 'none';
        }

        formatPrice(price) {
            return new Intl.NumberFormat('ru-RU', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(price);
        }

        getMethodIcon(method) {
            const icons = {
                air: '🛫',
                sea: '🚢',
                rail: '🚂',
                auto: '🚛'
            };
            return icons[method] || '📦';
        }
    }

    // ===== Инициализация =====
    function init() {
        $$('.delivery-calculator').forEach(container => {
            new DeliveryCalculator(container);
        });
    }

    // DOMContentLoaded или сразу, если уже загружен
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Экспорт для глобального доступа
    window.DeliveryCalculator = DeliveryCalculator;
})();
