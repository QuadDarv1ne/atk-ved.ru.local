/**
 * Форма отслеживания груза
 */

(function($) {
    'use strict';
    
    // Создание секции отслеживания
    function createTrackingSection() {
        const trackingHTML = `
            <section class="tracking-section" id="tracking">
                <div class="container">
                    <h2 class="section-title reveal">ОТСЛЕДИТЬ ГРУЗ</h2>
                    <div class="tracking-wrapper">
                        <div class="tracking-info">
                            <div class="tracking-feature">
                                <div class="tracking-feature-icon">📍</div>
                                <div class="tracking-feature-content">
                                    <h4>Отслеживание в реальном времени</h4>
                                    <p>Узнайте точное местоположение вашего груза в любой момент</p>
                                </div>
                            </div>
                            <div class="tracking-feature">
                                <div class="tracking-feature-icon">🔔</div>
                                <div class="tracking-feature-content">
                                    <h4>Уведомления о статусе</h4>
                                    <p>Получайте SMS и email уведомления о каждом этапе доставки</p>
                                </div>
                            </div>
                            <div class="tracking-feature">
                                <div class="tracking-feature-icon">📊</div>
                                <div class="tracking-feature-content">
                                    <h4>Детальная информация</h4>
                                    <p>Полная история перемещения груза с датами и временем</p>
                                </div>
                            </div>
                        </div>
                        <div class="tracking-form-wrapper">
                            <form class="tracking-form" id="trackingForm">
                                <div class="form-header">
                                    <div class="form-icon">🔍</div>
                                    <h3>Введите номер отслеживания</h3>
                                </div>
                                <div class="form-group">
                                    <input type="text" id="tracking-number" name="tracking_number" placeholder="Например: ATK123456789" required>
                                    <button type="submit" class="cta-button">
                                        <span>Отследить</span>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 0L8.59 1.41L16.17 9H0V11H16.17L8.59 18.59L10 20L20 10L10 0Z"/>
                                        </svg>
                                    </button>
                                </div>
                                <div id="tracking-result"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        `;
        
        // Вставляем перед секцией FAQ
        $('.faq-section').before(trackingHTML);
    }
    
    // Обработка формы отслеживания
    function handleTrackingForm() {
        $('#trackingForm').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            const $result = $('#tracking-result');
            const trackingNumber = $('#tracking-number').val().trim();
            
            if (!trackingNumber) {
                showTrackingError('Введите номер отслеживания');
                return;
            }
            
            // Показываем загрузку
            $button.addClass('loading').prop('disabled', true);
            $result.html('<div class="tracking-loading">Поиск информации о грузе...</div>');
            
            // Симуляция запроса (в реальности здесь будет AJAX к API)
            setTimeout(function() {
                $button.removeClass('loading').prop('disabled', false);
                
                // Демо данные
                const demoData = {
                    number: trackingNumber,
                    status: 'В пути',
                    location: 'Владивосток, Россия',
                    progress: 65,
                    estimatedDelivery: '15.03.2026',
                    history: [
                        { date: '20.02.2026', time: '14:30', location: 'Шанхай, Китай', status: 'Груз отправлен' },
                        { date: '25.02.2026', time: '09:15', location: 'Порт Шанхай', status: 'Погрузка на судно' },
                        { date: '28.02.2026', time: '18:00', location: 'В море', status: 'Транспортировка' },
                        { date: '05.03.2026', time: '11:20', location: 'Владивосток', status: 'Прибытие в порт' },
                        { date: '06.03.2026', time: '10:00', location: 'Владивосток', status: 'Таможенное оформление' }
                    ]
                };
                
                showTrackingResult(demoData);
            }, 1500);
        });
    }
    
    function showTrackingResult(data) {
        const resultHTML = `
            <div class="tracking-result-card">
                <div class="tracking-header">
                    <div class="tracking-number-display">
                        <span class="label">Номер отслеживания:</span>
                        <span class="value">${data.number}</span>
                    </div>
                    <div class="tracking-status ${data.status === 'В пути' ? 'in-transit' : 'delivered'}">
                        ${data.status}
                    </div>
                </div>
                
                <div class="tracking-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${data.progress}%"></div>
                    </div>
                    <div class="progress-info">
                        <span>Текущее местоположение: <strong>${data.location}</strong></span>
                        <span>Ожидаемая доставка: <strong>${data.estimatedDelivery}</strong></span>
                    </div>
                </div>
                
                <div class="tracking-history">
                    <h4>История перемещения</h4>
                    <div class="timeline">
                        ${data.history.map((item, index) => `
                            <div class="timeline-item ${index === data.history.length - 1 ? 'active' : ''}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <div class="timeline-date">${item.date} ${item.time}</div>
                                    <div class="timeline-location">${item.location}</div>
                                    <div class="timeline-status">${item.status}</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <div class="tracking-actions">
                    <button class="cta-button secondary" onclick="window.print()">
                        Распечатать
                    </button>
                    <button class="cta-button secondary" onclick="atkOpenModal()">
                        Связаться с менеджером
                    </button>
                </div>
            </div>
        `;
        
        $('#tracking-result').html(resultHTML).addClass('active');
    }
    
    function showTrackingError(message) {
        const errorHTML = `
            <div class="tracking-error">
                <div class="error-icon">⚠️</div>
                <div class="error-message">${message}</div>
            </div>
        `;
        
        $('#tracking-result').html(errorHTML).addClass('active');
        
        setTimeout(function() {
            $('#tracking-result').removeClass('active').html('');
        }, 3000);
    }
    
    // Инициализация
    $(document).ready(function() {
        createTrackingSection();
        handleTrackingForm();
    });
    
})(jQuery);
