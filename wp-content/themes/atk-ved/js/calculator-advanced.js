/**
 * Расширенный калькулятор доставки с экспортом в PDF
 */

(function($) {
    'use strict';
    
    let lastCalculation = null;
    
    // Экспорт в PDF
    function exportToPDF() {
        if (!lastCalculation) {
            if (typeof atkShowToast === 'function') {
                atkShowToast('Сначала выполните расчет', 'warning');
            }
            return;
        }
        
        // Создаем HTML для печати
        const printHTML = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Расчет доставки - АТК ВЭД</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 40px; }
                    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #e31e24; padding-bottom: 20px; }
                    .logo { font-size: 24px; font-weight: bold; color: #e31e24; }
                    .date { color: #666; font-size: 14px; margin-top: 10px; }
                    .section { margin: 20px 0; }
                    .section-title { font-size: 18px; font-weight: bold; margin-bottom: 15px; color: #2c2c2c; }
                    .param-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
                    .param-label { color: #666; }
                    .param-value { font-weight: bold; }
                    .result-card { background: #f8f8f8; padding: 20px; margin: 20px 0; border-radius: 8px; }
                    .result-method { font-size: 20px; font-weight: bold; color: #e31e24; margin-bottom: 10px; }
                    .result-days { color: #666; margin-bottom: 15px; }
                    .breakdown-item { display: flex; justify-content: space-between; padding: 8px 0; }
                    .total { font-size: 24px; font-weight: bold; color: #e31e24; text-align: right; margin-top: 20px; padding-top: 20px; border-top: 2px solid #e31e24; }
                    .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #666; font-size: 12px; }
                    .note { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0; }
                    @media print {
                        body { padding: 20px; }
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="logo">АТК ВЭД</div>
                    <div>Товары для маркетплейсов из Китая оптом</div>
                    <div class="date">Дата расчета: ${new Date().toLocaleDateString('ru-RU')}</div>
                </div>
                
                <div class="section">
                    <div class="section-title">Параметры груза</div>
                    <div class="param-row">
                        <span class="param-label">Вес груза:</span>
                        <span class="param-value">${lastCalculation.weight} кг</span>
                    </div>
                    <div class="param-row">
                        <span class="param-label">Объем груза:</span>
                        <span class="param-value">${lastCalculation.volume} м³</span>
                    </div>
                    <div class="param-row">
                        <span class="param-label">Стоимость товара:</span>
                        <span class="param-value">${lastCalculation.cost.toLocaleString('ru-RU')} ₽</span>
                    </div>
                    <div class="param-row">
                        <span class="param-label">Объемный вес:</span>
                        <span class="param-value">${lastCalculation.chargeableWeight.toFixed(1)} кг</span>
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">Расчет стоимости</div>
                    <div class="result-card">
                        <div class="result-method">${lastCalculation.methodName}</div>
                        <div class="result-days">Срок доставки: ${lastCalculation.days}</div>
                        
                        <div class="breakdown-item">
                            <span>Доставка (${lastCalculation.chargeableWeight.toFixed(1)} кг × ${lastCalculation.rate} ₽/кг)</span>
                            <span>${lastCalculation.deliveryCost.toLocaleString('ru-RU')} ₽</span>
                        </div>
                        <div class="breakdown-item">
                            <span>Страхование груза (2%)</span>
                            <span>${lastCalculation.insurance.toLocaleString('ru-RU')} ₽</span>
                        </div>
                        <div class="breakdown-item">
                            <span>Таможенное оформление (15%)</span>
                            <span>${lastCalculation.customs.toLocaleString('ru-RU')} ₽</span>
                        </div>
                        <div class="breakdown-item">
                            <span>Услуги компании</span>
                            <span>${lastCalculation.service.toLocaleString('ru-RU')} ₽</span>
                        </div>
                        
                        <div class="total">
                            Итого: ${lastCalculation.total.toLocaleString('ru-RU')} ₽
                        </div>
                    </div>
                </div>
                
                <div class="note">
                    <strong>Обратите внимание:</strong> Данный расчет является предварительным. 
                    Точная стоимость доставки определяется после согласования всех деталей с менеджером.
                </div>
                
                <div class="footer">
                    <p><strong>АТК ВЭД</strong> - Ваш надежный партнер в доставке из Китая</p>
                    <p>Телефон: ${$('.header-phone').text() || '+7 (XXX) XXX-XX-XX'} | Email: ${$('.header-email').text() || 'info@atk-ved.ru'}</p>
                    <p>Сайт: ${window.location.origin}</p>
                </div>
            </body>
            </html>
        `;
        
        // Открываем в новом окне для печати
        const printWindow = window.open('', '_blank');
        printWindow.document.write(printHTML);
        printWindow.document.close();
        
        // Автоматически открываем диалог печати
        printWindow.onload = function() {
            printWindow.print();
        };
        
        // Отслеживание в аналитике
        if (typeof ym !== 'undefined') {
            ym(window.atkVedMetrikaId, 'reachGoal', 'calculator_export_pdf');
        }
        if (typeof gtag !== 'undefined') {
            gtag('event', 'calculator_export_pdf', {
                'event_category': 'Calculator',
                'event_label': lastCalculation.methodName
            });
        }
    }
    
    // Сохранение расчета
    function saveCalculation(calculation) {
        lastCalculation = calculation;
        
        // Сохраняем в localStorage для истории
        let history = JSON.parse(localStorage.getItem('atk_ved_calc_history') || '[]');
        history.unshift({
            ...calculation,
            timestamp: new Date().toISOString()
        });
        
        // Храним только последние 10 расчетов
        history = history.slice(0, 10);
        localStorage.setItem('atk_ved_calc_history', JSON.stringify(history));
        
        // Показываем кнопки экспорта
        showExportButtons();
    }
    
    // Показать кнопки экспорта
    function showExportButtons() {
        if ($('.calc-export-buttons').length) return;
        
        const buttonsHTML = `
            <div class="calc-export-buttons">
                <button class="calc-export-btn calc-export-pdf" title="Экспорт в PDF">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    Экспорт в PDF
                </button>
                <button class="calc-export-btn calc-share-btn" title="Поделиться">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="18" cy="5" r="3"></circle>
                        <circle cx="6" cy="12" r="3"></circle>
                        <circle cx="18" cy="19" r="3"></circle>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                    </svg>
                    Поделиться
                </button>
                <button class="calc-export-btn calc-history-btn" title="История расчетов">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    История
                </button>
            </div>
        `;
        
        $('#calc-result').after(buttonsHTML);
        
        // Обработчики
        $('.calc-export-pdf').on('click', exportToPDF);
        $('.calc-share-btn').on('click', shareCalculation);
        $('.calc-history-btn').on('click', showHistory);
    }
    
    // Поделиться расчетом
    function shareCalculation() {
        if (!lastCalculation) return;
        
        const shareText = `Расчет доставки из Китая:\n${lastCalculation.methodName} - ${lastCalculation.total.toLocaleString('ru-RU')} ₽\nСрок: ${lastCalculation.days}`;
        const shareUrl = window.location.href;
        
        // Проверяем поддержку Web Share API
        if (navigator.share) {
            navigator.share({
                title: 'Расчет доставки - АТК ВЭД',
                text: shareText,
                url: shareUrl
            }).then(() => {
                if (typeof atkShowToast === 'function') {
                    atkShowToast('Расчет успешно отправлен', 'success');
                }
            }).catch(() => {});
        } else {
            // Fallback - копируем в буфер обмена
            const textToCopy = `${shareText}\n${shareUrl}`;
            navigator.clipboard.writeText(textToCopy).then(() => {
                if (typeof atkShowToast === 'function') {
                    atkShowToast('Ссылка скопирована в буфер обмена', 'success');
                }
            });
        }
        
        // Аналитика
        if (typeof ym !== 'undefined') {
            ym(window.atkVedMetrikaId, 'reachGoal', 'calculator_share');
        }
    }
    
    // Показать историю расчетов
    function showHistory() {
        const history = JSON.parse(localStorage.getItem('atk_ved_calc_history') || '[]');
        
        if (history.length === 0) {
            if (typeof atkShowToast === 'function') {
                atkShowToast('История расчетов пуста', 'info');
            }
            return;
        }
        
        let historyHTML = '<div class="calc-history-modal"><div class="calc-history-content">';
        historyHTML += '<h3>История расчетов</h3>';
        historyHTML += '<div class="calc-history-list">';
        
        history.forEach((calc, index) => {
            const date = new Date(calc.timestamp).toLocaleString('ru-RU');
            historyHTML += `
                <div class="calc-history-item" data-index="${index}">
                    <div class="calc-history-date">${date}</div>
                    <div class="calc-history-details">
                        <span>${calc.methodName}</span>
                        <span>${calc.weight} кг, ${calc.volume} м³</span>
                    </div>
                    <div class="calc-history-price">${calc.total.toLocaleString('ru-RU')} ₽</div>
                </div>
            `;
        });
        
        historyHTML += '</div>';
        historyHTML += '<button class="calc-history-close">Закрыть</button>';
        historyHTML += '</div></div>';
        
        $('body').append(historyHTML);
        
        // Обработчики
        $('.calc-history-close').on('click', function() {
            $('.calc-history-modal').remove();
        });
        
        $('.calc-history-item').on('click', function() {
            const index = $(this).data('index');
            const calc = history[index];
            
            // Заполняем форму
            $('#calc-weight').val(calc.weight);
            $('#calc-volume').val(calc.volume);
            $('#calc-cost').val(calc.cost);
            $('#calc-method').val(calc.method);
            
            // Выполняем расчет
            $('#calc-submit').click();
            
            $('.calc-history-modal').remove();
        });
    }
    
    // Интеграция с основным калькулятором
    $(document).ready(function() {
        // Перехватываем расчет из основного калькулятора
        const originalCalculate = window.calculateDelivery;
        if (typeof originalCalculate === 'function') {
            window.calculateDelivery = function() {
                originalCalculate();
                
                // Сохраняем данные расчета
                const weight = parseFloat($('#calc-weight').val()) || 0;
                const volume = parseFloat($('#calc-volume').val()) || 0;
                const cost = parseFloat($('#calc-cost').val()) || 0;
                const method = $('#calc-method').val();
                
                if (weight > 0 && cost > 0) {
                    const rates = {
                        air: { base: 8, name: 'Авиа', days: '5-10 дней' },
                        sea: { base: 2, name: 'Море', days: '30-45 дней' },
                        rail: { base: 4, name: 'ЖД', days: '15-20 дней' }
                    };
                    
                    const volumeWeight = method === 'air' ? volume * 167 : volume * 1000;
                    const chargeableWeight = Math.max(weight, volumeWeight);
                    const deliveryCost = chargeableWeight * rates[method].base;
                    const insurance = cost * 0.02;
                    const customs = cost * 0.15;
                    const service = 500;
                    const total = deliveryCost + insurance + customs + service;
                    
                    saveCalculation({
                        weight,
                        volume,
                        cost,
                        method,
                        methodName: rates[method].name,
                        days: rates[method].days,
                        rate: rates[method].base,
                        chargeableWeight,
                        deliveryCost,
                        insurance,
                        customs,
                        service,
                        total
                    });
                }
            };
        }
    });
    
    // Экспорт функций
    window.atkCalculatorExport = {
        exportToPDF,
        shareCalculation,
        showHistory
    };
    
})(jQuery);
