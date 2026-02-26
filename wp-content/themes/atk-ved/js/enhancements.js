/**
 * Дополнительные улучшения и эффекты
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Preloader
        $(window).on('load', function() {
            setTimeout(function() {
                $('.preloader').addClass('hidden');
                setTimeout(function() {
                    $('.preloader').remove();
                }, 500);
            }, 500);
        });
        
        // Scroll Progress Bar
        const $progressBar = $('<div class="scroll-progress-bar"></div>');
        $('body').prepend($progressBar);
        
        $(window).on('scroll', function() {
            const scrollTop = $(window).scrollTop();
            const docHeight = $(document).height() - $(window).height();
            const scrollPercent = (scrollTop / docHeight) * 100;
            $progressBar.css('width', scrollPercent + '%');
        });
        
        // Lazy Load Images
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                img.addEventListener('load', function() {
                    this.classList.add('loaded');
                });
            });
        } else {
            // Fallback для старых браузеров
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
            document.body.appendChild(script);
        }
        
        // Scroll Reveal Animation
        function revealOnScroll() {
            const reveals = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
            
            reveals.forEach(element => {
                const windowHeight = window.innerHeight;
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < windowHeight - elementVisible) {
                    element.classList.add('active');
                }
            });
        }
        
        window.addEventListener('scroll', revealOnScroll);
        revealOnScroll(); // Initial check
        
        // Smooth Scroll for Anchor Links
        $('a[href*="#"]:not([href="#"])').on('click', function(e) {
            const target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 70
                }, 800, 'swing');
            }
        });
        
        // Number Counter Animation
        function animateCounter($element) {
            const target = parseInt($element.text().replace(/\D/g, ''));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(function() {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                $element.text(Math.floor(current));
            }, 16);
        }
        
        // Trigger counter animation when visible
        const $counters = $('.counter');
        if ($counters.length) {
            let counterAnimated = false;
            $(window).on('scroll', function() {
                if (!counterAnimated) {
                    $counters.each(function() {
                        const $this = $(this);
                        const elementTop = $this.offset().top;
                        const windowBottom = $(window).scrollTop() + $(window).height();
                        
                        if (windowBottom > elementTop && !$this.hasClass('animated')) {
                            $this.addClass('animated');
                            animateCounter($this);
                            counterAnimated = true;
                        }
                    });
                }
            });
        }
        
        // Parallax Effect
        $(window).on('scroll', function() {
            const scrolled = $(window).scrollTop();
            $('.parallax-section').each(function() {
                const $this = $(this);
                const speed = $this.data('speed') || 0.5;
                $this.css('background-position-y', (scrolled * speed) + 'px');
            });
        });
        
        // Form Input Animation
        $('.form-field input, .form-field textarea').on('focus', function() {
            $(this).parent().addClass('focused');
        }).on('blur', function() {
            if (!$(this).val()) {
                $(this).parent().removeClass('focused');
            }
        });
        
        // Copy to Clipboard
        $('.copy-to-clipboard').on('click', function(e) {
            e.preventDefault();
            const text = $(this).data('text');
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function() {
                    if (typeof atkShowToast === 'function') {
                        atkShowToast('Скопировано в буфер обмена', 'success', 2000);
                    }
                });
            }
        });
        
        // Accordion Enhancement
        $('.accordion-header').on('click', function() {
            const $item = $(this).parent();
            const $content = $(this).next('.accordion-content');
            
            if ($item.hasClass('active')) {
                $content.slideUp(300);
                $item.removeClass('active');
            } else {
                $('.accordion-item.active .accordion-content').slideUp(300);
                $('.accordion-item.active').removeClass('active');
                $content.slideDown(300);
                $item.addClass('active');
            }
        });
        
        // Tabs
        $('.tab-button').on('click', function() {
            const tabId = $(this).data('tab');
            
            $('.tab-button').removeClass('active');
            $(this).addClass('active');
            
            $('.tab-content').removeClass('active');
            $('#' + tabId).addClass('active');
        });
        
        // Image Zoom on Hover
        $('.zoom-on-hover').on('mouseenter', function() {
            $(this).find('img').css('transform', 'scale(1.1)');
        }).on('mouseleave', function() {
            $(this).find('img').css('transform', 'scale(1)');
        });
        
        // Sticky Elements
        function handleStickyElements() {
            $('.sticky-element').each(function() {
                const $this = $(this);
                const offsetTop = $this.data('offset-top') || 100;
                const scrollTop = $(window).scrollTop();
                
                if (scrollTop > offsetTop) {
                    $this.addClass('is-sticky');
                } else {
                    $this.removeClass('is-sticky');
                }
            });
        }
        
        $(window).on('scroll', handleStickyElements);
        handleStickyElements();
        
        // Reading Progress (for blog posts)
        if ($('.post-content').length) {
            const $readingProgress = $('<div class="reading-progress"></div>');
            $('.post-content').prepend($readingProgress);
            
            $(window).on('scroll', function() {
                const contentHeight = $('.post-content').height();
                const windowHeight = $(window).height();
                const scrollTop = $(window).scrollTop();
                const contentTop = $('.post-content').offset().top;
                
                const progress = ((scrollTop - contentTop) / (contentHeight - windowHeight)) * 100;
                $readingProgress.css('width', Math.min(Math.max(progress, 0), 100) + '%');
            });
        }
        
        // Keyboard Navigation Enhancement
        $(document).on('keydown', function(e) {
            // ESC key closes modals
            if (e.key === 'Escape') {
                $('.atk-modal-overlay').trigger('click');
            }
            
            // Tab trap in modal
            if (e.key === 'Tab' && $('.atk-modal-overlay.active').length) {
                const $modal = $('.atk-modal-overlay.active .atk-modal');
                const $focusable = $modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                const $first = $focusable.first();
                const $last = $focusable.last();
                
                if (e.shiftKey) {
                    if (document.activeElement === $first[0]) {
                        e.preventDefault();
                        $last.focus();
                    }
                } else {
                    if (document.activeElement === $last[0]) {
                        e.preventDefault();
                        $first.focus();
                    }
                }
            }
        });
        
        // Performance: Debounce scroll events
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Apply debounce to scroll handlers
        const debouncedScroll = debounce(function() {
            // Custom scroll handlers here
        }, 100);
        
        $(window).on('scroll', debouncedScroll);
        
        // Console Easter Egg
        console.log('%c👋 Привет, разработчик!', 'font-size: 20px; color: #e31e24; font-weight: bold;');
        console.log('%cЕсли вы ищете работу или хотите сотрудничать, свяжитесь с нами!', 'font-size: 14px; color: #666;');
        
    });
    
})(jQuery);
