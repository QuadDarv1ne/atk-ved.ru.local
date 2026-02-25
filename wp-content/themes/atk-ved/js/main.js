jQuery(document).ready(function($) {
    
    // Плавная прокрутка к якорям
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if(target.length) {
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 70
            }, 800, 'swing');
            
            // Закрыть мобильное меню после клика
            $('.main-nav').removeClass('active');
            $('.menu-toggle').removeClass('active');
        }
    });
    
    // Аккордеон для FAQ с улучшенной анимацией
    $('.faq-question').on('click', function() {
        var $item = $(this).parent('.faq-item');
        var $answer = $(this).next('.faq-answer');
        
        if ($item.hasClass('active')) {
            $answer.slideUp(300);
            $item.removeClass('active');
        } else {
            $('.faq-item.active .faq-answer').slideUp(300);
            $('.faq-item.active').removeClass('active');
            $answer.slideDown(300);
            $item.addClass('active');
        }
    });
    
    // Липкая шапка при прокрутке с плавным переходом
    var lastScroll = 0;
    $(window).scroll(function() {
        var currentScroll = $(this).scrollTop();
        
        if (currentScroll > 50) {
            $('.site-header').addClass('scrolled');
        } else {
            $('.site-header').removeClass('scrolled');
        }
        
        lastScroll = currentScroll;
    });
    
    // Анимация появления элементов при прокрутке (Intersection Observer)
    if ('IntersectionObserver' in window) {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.service-card, .step-card, .review-card, .method-card').forEach(function(el) {
            observer.observe(el);
        });
    } else {
        // Fallback для старых браузеров
        function checkVisible() {
            $('.service-card, .step-card, .review-card, .method-card').each(function() {
                var elementTop = $(this).offset().top;
                var elementBottom = elementTop + $(this).outerHeight();
                var viewportTop = $(window).scrollTop();
                var viewportBottom = viewportTop + $(window).height();
                
                if (elementBottom > viewportTop && elementTop < viewportBottom - 100) {
                    $(this).addClass('visible');
                }
            });
        }
        
        $(window).on('scroll resize', checkVisible);
        checkVisible();
    }
    
    // Мобильное меню с улучшенной анимацией
    $('.menu-toggle').on('click', function(e) {
        e.stopPropagation();
        $('.main-nav').toggleClass('active');
        $(this).toggleClass('active');
        $('body').toggleClass('menu-open');
    });
    
    // Закрыть меню при клике вне его
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.main-nav, .menu-toggle').length) {
            $('.main-nav').removeClass('active');
            $('.menu-toggle').removeClass('active');
            $('body').removeClass('menu-open');
        }
    });
    
    // Параллакс эффект для hero секции
    $(window).scroll(function() {
        var scrolled = $(window).scrollTop();
        $('.hero-image img').css('transform', 'translateY(' + (scrolled * 0.3) + 'px)');
    });
    
    // Счетчик для статистики
    function animateCounter($elem, target) {
        $({ counter: 0 }).animate({ counter: target }, {
            duration: 2000,
            easing: 'swing',
            step: function() {
                $elem.text(Math.ceil(this.counter));
            },
            complete: function() {
                $elem.text(target);
            }
        });
    }
    
    // Запуск счетчика при появлении в viewport
    var counterAnimated = false;
    $(window).scroll(function() {
        if (!counterAnimated && $('.hero-stats').length) {
            var heroTop = $('.hero-stats').offset().top;
            var viewportBottom = $(window).scrollTop() + $(window).height();
            
            if (viewportBottom > heroTop) {
                counterAnimated = true;
                $('.stat-number').each(function() {
                    var $this = $(this);
                    var text = $this.text();
                    var number = parseInt(text.replace(/\D/g, ''));
                    if (number) {
                        $this.text('0');
                        animateCounter($this, number);
                    }
                });
            }
        }
    });
    
    // Кнопка "Наверх"
    var $backToTop = $('<button class="back-to-top" aria-label="Наверх"><span>↑</span></button>');
    $('body').append($backToTop);
    
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            $backToTop.addClass('visible');
        } else {
            $backToTop.removeClass('visible');
        }
    });
    
    $backToTop.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 600);
    });
    
});
