jQuery(document).ready(function($) {
    
    // Плавная прокрутка к якорям
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if(target.length) {
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });
    
    // Аккордеон для FAQ
    $('.faq-question').on('click', function() {
        $(this).next('.faq-answer').slideToggle();
        $(this).parent('.faq-item').toggleClass('active');
    });
    
    // Липкая шапка при прокрутке
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('.site-header').addClass('scrolled');
        } else {
            $('.site-header').removeClass('scrolled');
        }
    });
    
    // Анимация появления элементов при прокрутке
    function checkVisible() {
        $('.service-card, .step-card, .review-card').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('visible');
            }
        });
    }
    
    $(window).on('scroll resize', checkVisible);
    checkVisible();
    
    // Мобильное меню
    $('.menu-toggle').on('click', function() {
        $('.main-nav').toggleClass('active');
        $(this).toggleClass('active');
    });
    
});
