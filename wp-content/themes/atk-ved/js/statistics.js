/**
 * Анимированная статистика
 */

(function($) {
    'use strict';
    
    // Данные статистики
    const statistics = {
        clients: { value: 500, suffix: '+', label: 'Довольных клиентов' },
        years: { value: 5, suffix: '+', label: 'Лет на рынке' },
        containers: { value: 1000, suffix: '+', label: 'Контейнеров доставлено' },
        countries: { value: 15, suffix: '', label: 'Городов доставки' }
    };
    
    // Создание секции статистики
    function createStatisticsSection() {
        const statsHTML = `
            <section class="statistics-section">
                <div class="container">
                    <div class="statistics-grid">
                        <div class="stat-card" data-stat="clients">
                            <div class="stat-icon">👥</div>
                            <div class="stat-number" data-target="${statistics.clients.value}">0</div>
                            <div class="stat-suffix">${statistics.clients.suffix}</div>
                            <div class="stat-label">${statistics.clients.label}</div>
                        </div>
                        <div class="stat-card" data-stat="years">
                            <div class="stat-icon">📅</div>
                            <div class="stat-number" data-target="${statistics.years.value}">0</div>
                            <div class="stat-suffix">${statistics.years.suffix}</div>
                            <div class="stat-label">${statistics.years.label}</div>
                        </div>
                        <div class="stat-card" data-stat="containers">
                            <div class="stat-icon">📦</div>
                            <div class="stat-number" data-target="${statistics.containers.value}">0</div>
                            <div class="stat-suffix">${statistics.containers.suffix}</div>
                            <div class="stat-label">${statistics.containers.label}</div>
                        </div>
                        <div class="stat-card" data-stat="countries">
                            <div class="stat-icon">🌍</div>
                            <div class="stat-number" data-target="${statistics.countries.value}">0</div>
                            <div class="stat-suffix">${statistics.countries.suffix}</div>
                            <div class="stat-label">${statistics.countries.label}</div>
                        </div>
                    </div>
                </div>
            </section>
        `;
        
        // Вставляем после секции преимуществ
        $('.advantages-section').after(statsHTML);
    }
    
    // Анимация счетчиков
    function animateCounter($element, target, duration = 2000) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;
        
        const timer = setInterval(function() {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            $element.text(Math.floor(current));
        }, 16);
    }
    
    // Запуск анимации при появлении в viewport
    function initStatisticsAnimation() {
        let animated = false;
        
        $(window).on('scroll', function() {
            if (animated) return;
            
            const $section = $('.statistics-section');
            if (!$section.length) return;
            
            const sectionTop = $section.offset().top;
            const windowBottom = $(window).scrollTop() + $(window).height();
            
            if (windowBottom > sectionTop + 100) {
                animated = true;
                
                $('.stat-card').each(function(index) {
                    const $card = $(this);
                    const $number = $card.find('.stat-number');
                    const target = parseInt($number.data('target'));
                    
                    setTimeout(function() {
                        $card.addClass('animate');
                        animateCounter($number, target);
                    }, index * 200);
                });
            }
        });
    }
    
    // Инициализация
    $(document).ready(function() {
        createStatisticsSection();
        initStatisticsAnimation();
        
        // Триггер для первой проверки
        $(window).trigger('scroll');
    });
    
})(jQuery);
