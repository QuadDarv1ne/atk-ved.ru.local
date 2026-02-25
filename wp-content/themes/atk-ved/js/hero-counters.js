/**
 * Анимированные счетчики для Hero секции
 */

(function($) {
    'use strict';
    
    // Данные статистики
    const stats = [
        { value: 500, suffix: '+', label: 'Довольных клиентов' },
        { value: 5, suffix: '+', label: 'Лет на рынке' },
        { value: 1000, suffix: '+', label: 'Контейнеров доставлено' },
        { value: 15, suffix: '', label: 'Городов доставки' }
    ];
    
    let countersAnimated = false;
    
    function initHeroCounters() {
        const $heroSection = $('.hero-section');
        if (!$heroSection.length) return;
        
        // Создаем HTML для счетчиков
        const statsHTML = `
            <div class="hero-stats">
                ${stats.map((stat, index) => `
                    <div class="stat-item" data-index="${index}">
                        <div class="stat-number" data-target="${stat.value}">
                            <span class="stat-value">0</span><span class="stat-suffix">${stat.suffix}</span>
                        </div>
                        <div class="stat-label">${stat.label}</div>
                    </div>
                `).join('')}
            </div>
        `;
        
        // Вставляем после hero-content
        $heroSection.find('.hero-content').after(statsHTML);
        
        // Запускаем анимацию при прокрутке
        initScrollAnimation();
    }
    
    function initScrollAnimation() {
        $(window).on('scroll', function() {
            if (countersAnimated) return;
            
            const $heroStats = $('.hero-stats');
            if (!$heroStats.length) return;
            
            const statsTop = $heroStats.offset().top;
            const windowBottom = $(window).scrollTop() + $(window).height();
            
            // Запускаем анимацию когда секция появляется в viewport
            if (windowBottom > statsTop + 100) {
                countersAnimated = true;
                animateCounters();
            }
        });
        
        // Проверяем сразу при загрузке
        $(window).trigger('scroll');
    }
    
    function animateCounters() {
        $('.stat-item').each(function(index) {
            const $item = $(this);
            const $number = $item.find('.stat-number');
            const $value = $item.find('.stat-value');
            const target = parseInt($number.data('target'));
            
            // Задержка для последовательной анимации
            setTimeout(function() {
                $item.addClass('animated');
                $number.addClass('counting');
                animateCounter($value, target);
                
                // Убираем класс counting после анимации
                setTimeout(function() {
                    $number.removeClass('counting');
                }, 2000);
            }, index * 150);
        });
    }
    
    function animateCounter($element, target) {
        const duration = 2000; // 2 секунды
        const steps = 60; // Количество шагов
        const stepDuration = duration / steps;
        const increment = target / steps;
        let current = 0;
        
        const timer = setInterval(function() {
            current += increment;
            
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            // Форматируем число с пробелами для тысяч
            const formatted = Math.floor(current).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            $element.text(formatted);
        }, stepDuration);
    }
    
    // Альтернативная анимация с easing
    function animateCounterEasing($element, target) {
        $({ counter: 0 }).animate({ counter: target }, {
            duration: 2000,
            easing: 'swing',
            step: function() {
                const formatted = Math.ceil(this.counter).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
                $element.text(formatted);
            },
            complete: function() {
                const formatted = target.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
                $element.text(formatted);
            }
        });
    }
    
    // Инициализация
    $(document).ready(function() {
        initHeroCounters();
    });
    
})(jQuery);
