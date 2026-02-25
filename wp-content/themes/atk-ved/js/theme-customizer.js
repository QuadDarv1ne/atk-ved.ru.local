/**
 * Dark Mode Toggle & Theme Customizer
 * JavaScript для управления темой
 * 
 * @package ATK_VED
 * @since 3.3.0
 */

(function($) {
    'use strict';
    
    // Theme Manager Class
    class ThemeManager {
        constructor() {
            this.storageKey = 'atk_ved_theme_settings';
            this.defaultSettings = {
                darkMode: false,
                primaryColor: 'red',
                fontSize: 'base',
                contrast: 'normal',
            };
            
            this.settings = this.loadSettings();
            this.init();
        }
        
        init() {
            this.applySettings();
            this.bindEvents();
            this.checkSystemPreference();
        }
        
        loadSettings() {
            const saved = localStorage.getItem(this.storageKey);
            return saved ? JSON.parse(saved) : this.defaultSettings;
        }
        
        saveSettings() {
            localStorage.setItem(this.storageKey, JSON.stringify(this.settings));
        }
        
        applySettings() {
            // Dark Mode
            if (this.settings.darkMode) {
                document.body.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
            }
            
            // Primary Color
            this.applyPrimaryColor(this.settings.primaryColor);
            
            // Font Size
            this.applyFontSize(this.settings.fontSize);
            
            // Contrast
            this.applyContrast(this.settings.contrast);
        }
        
        applyPrimaryColor(color) {
            const colors = {
                red: { primary: '#e31e24', dark: '#c01a1f' },
                blue: { primary: '#2196F3', dark: '#1976D2' },
                green: { primary: '#4CAF50', dark: '#388E3C' },
                purple: { primary: '#9C27B0', dark: '#7B1FA2' },
                orange: { primary: '#FF9800', dark: '#F57C00' },
                teal: { primary: '#009688', dark: '#00796B' },
            };
            
            const selected = colors[color] || colors.red;
            
            document.documentElement.style.setProperty('--color-primary-500', selected.primary);
            document.documentElement.style.setProperty('--color-primary-600', selected.dark);
        }
        
        applyFontSize(size) {
            const sizes = {
                small: '14px',
                base: '16px',
                large: '18px',
                xlarge: '20px',
            };
            
            document.documentElement.style.setProperty('--font-size-base', sizes[size] || sizes.base);
        }
        
        applyContrast(contrast) {
            if (contrast === 'high') {
                document.body.classList.add('high-contrast');
            } else {
                document.body.classList.remove('high-contrast');
            }
        }
        
        toggleDarkMode() {
            this.settings.darkMode = !this.settings.darkMode;
            this.saveSettings();
            this.applySettings();
            
            // Track event
            this.trackEvent('theme_change', { darkMode: this.settings.darkMode });
        }
        
        setPrimaryColor(color) {
            this.settings.primaryColor = color;
            this.saveSettings();
            this.applySettings();
            
            this.trackEvent('theme_change', { primaryColor: color });
        }
        
        setFontSize(size) {
            this.settings.fontSize = size;
            this.saveSettings();
            this.applySettings();
            
            this.trackEvent('theme_change', { fontSize: size });
        }
        
        setContrast(contrast) {
            this.settings.contrast = contrast;
            this.saveSettings();
            this.applySettings();
            
            this.trackEvent('theme_change', { contrast });
        }
        
        resetSettings() {
            this.settings = { ...this.defaultSettings };
            this.saveSettings();
            this.applySettings();
            
            this.trackEvent('theme_reset');
        }
        
        checkSystemPreference() {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                if (!localStorage.getItem(this.storageKey)) {
                    this.settings.darkMode = true;
                    this.applySettings();
                }
            }
        }
        
        trackEvent(action, data) {
            // Analytics tracking (if available)
            if (typeof ym !== 'undefined') {
                ym(atkVedData.metrikaId || 0, 'reachGoal', action, data);
            }
            
            if (typeof gtag !== 'undefined') {
                gtag('event', action, {
                    event_category: 'Theme',
                    ...data,
                });
            }
            
            console.log('[Theme]', action, data);
        }
        
        bindEvents() {
            // Toggle Button
            $('.theme-toggle-btn').on('click', () => {
                this.toggleDarkMode();
            });
            
            // Panel Toggle
            $('.theme-panel-toggle').on('click', () => {
                $('.theme-panel').toggleClass('active');
            });
            
            // Panel Close
            $('.theme-panel-close').on('click', () => {
                $('.theme-panel').removeClass('active');
            });
            
            // Color Buttons
            $('.theme-color-btn').on('click', (e) => {
                const color = $(e.currentTarget).data('color');
                $('.theme-color-btn').removeClass('active');
                $(e.currentTarget).addClass('active');
                this.setPrimaryColor(color);
            });
            
            // Font Size Buttons
            $('.font-size-btn').on('click', (e) => {
                const size = $(e.currentTarget).data('size');
                $('.font-size-btn').removeClass('active');
                $(e.currentTarget).addClass('active');
                this.setFontSize(size);
            });
            
            // Contrast Buttons
            $('.contrast-btn').on('click', (e) => {
                const contrast = $(e.currentTarget).data('contrast');
                $('.contrast-btn').removeClass('active');
                $(e.currentTarget).addClass('active');
                this.setContrast(contrast);
            });
            
            // Reset Button
            $('.theme-reset').on('click', () => {
                this.resetSettings();
                
                // Reset UI
                $('.theme-color-btn').removeClass('active').first().addClass('active');
                $('.font-size-btn').removeClass('active').filter('[data-size="base"]').addClass('active');
                $('.contrast-btn').removeClass('active').filter('[data-contrast="normal"]').addClass('active');
            });
            
            // Close panel on outside click
            $(document).on('click', (e) => {
                if (!$(e.target).closest('.theme-panel, .theme-toggle-btn').length) {
                    $('.theme-panel').removeClass('active');
                }
            });
            
            // Keyboard navigation
            $(document).on('keydown', (e) => {
                if (e.key === 'Escape') {
                    $('.theme-panel').removeClass('active');
                }
            });
        }
    }
    
    // Initialize on DOM ready
    $(document).ready(function() {
        window.atkThemeManager = new ThemeManager();
    });
    
})(jQuery);

/**
 * Additional Theme Enhancements
 * Дополнительные улучшения темы
 */

(function($) {
    'use strict';
    
    // Smooth Scroll to Anchors
    $('a[href^="#"]').on('click', function(e) {
        const targetId = $(this).attr('href');
        if (targetId === '#') return;
        
        const $target = $(targetId);
        if ($target.length) {
            e.preventDefault();
            
            const headerOffset = 80;
            const elementPosition = $target.offset().top;
            const offsetPosition = elementPosition - headerOffset;
            
            $('html, body').stop().animate({
                scrollTop: offsetPosition
            }, 800, 'swing');
            
            // Update URL without scrolling
            history.pushState(null, null, targetId);
        }
    });
    
    // Active Navigation Highlight
    function highlightActiveNav() {
        const sections = $('section[id]');
        const scrollPos = $(window).scrollTop() + 100;
        
        sections.each(function() {
            const sectionTop = $(this).offset().top;
            const sectionHeight = $(this).outerHeight();
            const sectionId = $(this).attr('id');
            
            if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                $('.main-nav a[href="#' + sectionId + '"]').addClass('active');
                $('.main-nav a[href="#' + sectionId + '"]').siblings().removeClass('active');
            }
        });
    }
    
    $(window).on('scroll', highlightActiveNav);
    highlightActiveNav();
    
    // Parallax Effect for Hero
    if ($('.hero-section').length) {
        $(window).on('scroll', function() {
            const scrolled = $(this).scrollTop();
            const parallax = $('.hero-image img');
            parallax.css('transform', 'translateY(' + (scrolled * 0.3) + 'px)');
        });
    }
    
    // Reveal on Scroll
    const revealElements = document.querySelectorAll('.reveal');
    
    if (revealElements.length && 'IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        revealElements.forEach(el => revealObserver.observe(el));
    }
    
    // Cursor Follower Effect (Desktop Only)
    if (window.matchMedia('(pointer: fine)').matches) {
        const cursorFollower = $('<div class="cursor-follower"></div>');
        $('body').append(cursorFollower);
        
        cursorFollower.css({
            position: 'fixed',
            width: '20px',
            height: '20px',
            border: '2px solid var(--color-primary-500)',
            borderRadius: '50%',
            pointerEvents: 'none',
            zIndex: '9999',
            transition: 'all 0.1s ease',
            transform: 'translate(-50%, -50%)',
        });
        
        $(document).on('mousemove', (e) => {
            cursorFollower.css({
                left: e.clientX + 'px',
                top: e.clientY + 'px',
            });
        });
        
        // Scale on interactive elements
        $('a, button, .cta-button').on('mouseenter', () => {
            cursorFollower.css({
                transform: 'translate(-50%, -50%) scale(1.5)',
                backgroundColor: 'rgba(227, 30, 36, 0.1)',
            });
        }).on('mouseleave', () => {
            cursorFollower.css({
                transform: 'translate(-50%, -50%) scale(1)',
                backgroundColor: 'transparent',
            });
        });
    }
    
    // Page Load Animation
    $(window).on('load', function() {
        $('body').addClass('page-loaded');
    });
    
})(jQuery);

/**
 * CSS for Cursor Follower
 */
if (typeof document !== 'undefined') {
    const style = document.createElement('style');
    style.textContent = `
        .cursor-follower {
            display: none;
        }
        
        @media (pointer: fine) {
            .cursor-follower {
                display: block;
            }
        }
        
        body.page-loaded .reveal {
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
    `;
    document.head.appendChild(style);
}
