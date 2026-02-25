/**
 * Слайдер отзывов
 */

(function($) {
    'use strict';
    
    let currentSlide = 0;
    let autoplayInterval;
    const autoplayDelay = 5000;
    
    function initReviewsSlider() {
        const $reviewsSection = $('.reviews-section');
        if (!$reviewsSection.length) return;
        
        // Преобразуем статичную сетку в слайдер
        const $reviewsGrid = $reviewsSection.find('.reviews-grid');
        const $reviews = $reviewsGrid.find('.review-card');
        
        if ($reviews.length === 0) return;
        
        // Создаем структуру слайдера
        $reviewsGrid.addClass('reviews-slider');
        $reviews.each(function(index) {
            $(this).attr('data-slide', index);
            if (index === 0) {
                $(this).addClass('active');
            }
        });
        
        // Добавляем навигацию
        const navigationHTML = `
            <div class="slider-navigation">
                <button class="slider-btn slider-prev" aria-label="Предыдущий отзыв">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <div class="slider-dots"></div>
                <button class="slider-btn slider-next" aria-label="Следующий отзыв">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        `;
        
        $reviewsSection.find('.container').append(navigationHTML);
        
        // Создаем точки навигации
        const $dotsContainer = $('.slider-dots');
        $reviews.each(function(index) {
            const dotHTML = `<button class="slider-dot ${index === 0 ? 'active' : ''}" data-slide="${index}" aria-label="Перейти к отзыву ${index + 1}"></button>`;
            $dotsContainer.append(dotHTML);
        });
        
        // Обработчики событий
        $('.slider-prev').on('click', function() {
            stopAutoplay();
            prevSlide();
            startAutoplay();
        });
        
        $('.slider-next').on('click', function() {
            stopAutoplay();
            nextSlide();
            startAutoplay();
        });
        
        $('.slider-dot').on('click', function() {
            stopAutoplay();
            goToSlide($(this).data('slide'));
            startAutoplay();
        });
        
        // Свайп на мобильных
        let touchStartX = 0;
        let touchEndX = 0;
        
        $reviewsGrid.on('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
        });
        
        $reviewsGrid.on('touchend', function(e) {
            touchEndX = e.changedTouches[0].clientX;
            handleSwipe();
        });
        
        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > swipeThreshold) {
                stopAutoplay();
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
                startAutoplay();
            }
        }
        
        // Клавиатурная навигация
        $(document).on('keydown', function(e) {
            if (!$reviewsSection.is(':visible')) return;
            
            if (e.key === 'ArrowLeft') {
                stopAutoplay();
                prevSlide();
                startAutoplay();
            } else if (e.key === 'ArrowRight') {
                stopAutoplay();
                nextSlide();
                startAutoplay();
            }
        });
        
        // Пауза при наведении
        $reviewsGrid.on('mouseenter', stopAutoplay);
        $reviewsGrid.on('mouseleave', startAutoplay);
        
        // Запуск автопрокрутки
        startAutoplay();
    }
    
    function goToSlide(index) {
        const $reviews = $('.review-card');
        const totalSlides = $reviews.length;
        
        if (index < 0) {
            currentSlide = totalSlides - 1;
        } else if (index >= totalSlides) {
            currentSlide = 0;
        } else {
            currentSlide = index;
        }
        
        // Обновляем активные элементы
        $reviews.removeClass('active').eq(currentSlide).addClass('active');
        $('.slider-dot').removeClass('active').eq(currentSlide).addClass('active');
        
        // Анимация
        updateSliderPosition();
    }
    
    function updateSliderPosition() {
        const $slider = $('.reviews-slider');
        const slideWidth = $('.review-card').outerWidth(true);
        const offset = -currentSlide * slideWidth;
        
        $slider.css('transform', `translateX(${offset}px)`);
    }
    
    function nextSlide() {
        goToSlide(currentSlide + 1);
    }
    
    function prevSlide() {
        goToSlide(currentSlide - 1);
    }
    
    function startAutoplay() {
        stopAutoplay();
        autoplayInterval = setInterval(nextSlide, autoplayDelay);
    }
    
    function stopAutoplay() {
        if (autoplayInterval) {
            clearInterval(autoplayInterval);
            autoplayInterval = null;
        }
    }
    
    // Инициализация при загрузке
    $(document).ready(function() {
        initReviewsSlider();
        
        // Обновление позиции при изменении размера окна
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                updateSliderPosition();
            }, 250);
        });
    });
    
})(jQuery);
