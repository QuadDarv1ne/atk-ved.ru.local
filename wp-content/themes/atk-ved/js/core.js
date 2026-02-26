/**
 * Core JavaScript - Основной функционал
 * Оптимизированная версия с минимальным кодом
 * @package ATK_VED
 * @version 3.3.1
 */

(function($) {
    'use strict';

    // Плавная прокрутка к якорям
    $('body').on('click', 'a[href^="#"]', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').stop(true, true).animate({
                scrollTop: target.offset().top - 70
            }, 800, 'swing');

            // Закрыть мобильное меню
            $('.main-nav, .menu-toggle').removeClass('active');
        }
    });

    // FAQ аккордеон
    $('body').on('click', '.faq-question', function() {
        const $item = $(this).closest('.faq-item');
        const $answer = $(this).next('.faq-answer');

        if ($item.hasClass('active')) {
            $answer.slideUp(300);
            $item.removeClass('active');
        } else {
            $('.faq-answer').slideUp(300);
            $('.faq-item').removeClass('active');
            $answer.slideDown(300);
            $item.addClass('active');
        }
    });

    // Липкая шапка
    let ticking = false;
    function updateHeader() {
        const scrolled = $(window).scrollTop() > 50;
        $('.site-header').toggleClass('scrolled', scrolled);
        ticking = false;
    }

    $(window).on('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateHeader);
            ticking = true;
        }
    });

    // Анимация появления элементов
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        document.querySelectorAll('.service-card, .step-card, .review-card, .method-card').forEach(function(el) {
            observer.observe(el);
        });
    }

    // Мобильное меню
    $('.menu-toggle').on('click', function() {
        $(this).toggleClass('active');
        $('.main-nav').toggleClass('active');
        $('body').toggleClass('menu-open');
    });

    // Закрытие меню по клику вне его
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.main-nav, .menu-toggle').length) {
            $('.main-nav, .menu-toggle').removeClass('active');
            $('body').removeClass('menu-open');
        }
    });

    // Кнопка "Наверх"
    const $scrollTop = $('<button>', {
        class: 'scroll-to-top',
        'aria-label': 'Наверх',
        html: '↑'
    }).appendTo('body');

    $(window).on('scroll', function() {
        $scrollTop.toggleClass('visible', $(window).scrollTop() > 300);
    });

    $scrollTop.on('click', function() {
        $('html, body').animate({ scrollTop: 0 }, 600);
    });

})(jQuery);
