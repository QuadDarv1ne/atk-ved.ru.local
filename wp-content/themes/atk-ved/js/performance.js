/**
 * Performance Optimizations
 * @package ATK_VED
 */

(function() {
    'use strict';

    // Debounce function
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

    // Throttle function
    function throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Intersection Observer for lazy loading
    const lazyLoadObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                
                // Lazy load images
                if (element.tagName === 'IMG' && element.dataset.src) {
                    element.src = element.dataset.src;
                    element.removeAttribute('data-src');
                    element.classList.add('loaded');
                }
                
                // Lazy load background images
                if (element.dataset.bgSrc) {
                    element.style.backgroundImage = `url(${element.dataset.bgSrc})`;
                    element.removeAttribute('data-bg-src');
                }
                
                // Lazy load iframes
                if (element.tagName === 'IFRAME' && element.dataset.src) {
                    element.src = element.dataset.src;
                    element.removeAttribute('data-src');
                }
                
                lazyLoadObserver.unobserve(element);
            }
        });
    }, {
        rootMargin: '50px 0px',
        threshold: 0.01
    });

    // Observe all lazy load elements
    document.querySelectorAll('[data-src], [data-bg-src]').forEach(el => {
        lazyLoadObserver.observe(el);
    });

    // Scroll Reveal Animation
    const scrollRevealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                scrollRevealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    document.querySelectorAll('.scroll-reveal').forEach(el => {
        scrollRevealObserver.observe(el);
    });

    // Prefetch links on hover
    const prefetchedLinks = new Set();
    
    document.addEventListener('mouseover', (e) => {
        const link = e.target.closest('a[href]');
        if (!link) return;
        
        const href = link.href;
        if (prefetchedLinks.has(href)) return;
        if (!href.startsWith(window.location.origin)) return;
        
        const prefetchLink = document.createElement('link');
        prefetchLink.rel = 'prefetch';
        prefetchLink.href = href;
        document.head.appendChild(prefetchLink);
        
        prefetchedLinks.add(href);
    });

    // Image optimization - convert to WebP if supported
    function supportsWebP() {
        const canvas = document.createElement('canvas');
        if (canvas.getContext && canvas.getContext('2d')) {
            return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
        }
        return false;
    }

    if (supportsWebP()) {
        document.documentElement.classList.add('webp');
    }

    // Optimize scroll performance
    let ticking = false;
    let lastScrollY = window.pageYOffset;

    const handleScroll = () => {
        lastScrollY = window.pageYOffset;
        
        // Add/remove header shadow on scroll
        const header = document.querySelector('.site-header');
        if (header) {
            if (lastScrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }
        
        // Show/hide scroll to top button
        const scrollBtn = document.querySelector('.scroll-to-top');
        if (scrollBtn) {
            if (lastScrollY > 300) {
                scrollBtn.classList.add('visible');
            } else {
                scrollBtn.classList.remove('visible');
            }
        }
        
        ticking = false;
    };

    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(handleScroll);
            ticking = true;
        }
    }, { passive: true });

    // Optimize resize performance
    const handleResize = debounce(() => {
        // Update viewport height for mobile
        document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
        
        // Trigger custom resize event
        window.dispatchEvent(new CustomEvent('optimizedResize'));
    }, 250);

    window.addEventListener('resize', handleResize, { passive: true });
    handleResize(); // Initial call

    // Resource Hints
    function addResourceHint(url, type = 'prefetch') {
        const link = document.createElement('link');
        link.rel = type;
        link.href = url;
        document.head.appendChild(link);
    }

    // Preconnect to external domains
    const externalDomains = [
        'https://fonts.googleapis.com',
        'https://fonts.gstatic.com',
        'https://cdn.jsdelivr.net'
    ];

    externalDomains.forEach(domain => {
        const link = document.createElement('link');
        link.rel = 'preconnect';
        link.href = domain;
        link.crossOrigin = 'anonymous';
        document.head.appendChild(link);
    });

    // Critical CSS loaded indicator
    document.documentElement.classList.add('js-loaded');

    // Font loading optimization
    if ('fonts' in document) {
        Promise.all([
            document.fonts.load('400 1em sans-serif'),
            document.fonts.load('600 1em sans-serif'),
            document.fonts.load('700 1em sans-serif')
        ]).then(() => {
            document.documentElement.classList.add('fonts-loaded');
        });
    }

    // Service Worker registration (if available)
    if ('serviceWorker' in navigator && !window.location.hostname.includes('localhost')) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    if (window.WP_DEBUG) console.log('SW registered:', registration);
                })
                .catch(error => {
                    if (window.WP_DEBUG) console.log('SW registration failed:', error);
                });
        });
    }

    // Network Information API
    if ('connection' in navigator) {
        const connection = navigator.connection;
        
        // Adjust quality based on connection
        if (connection.effectiveType === '4g') {
            document.documentElement.classList.add('high-quality');
        } else if (connection.effectiveType === '3g') {
            document.documentElement.classList.add('medium-quality');
        } else {
            document.documentElement.classList.add('low-quality');
        }
        
        // Listen for connection changes
        connection.addEventListener('change', () => {
            if (window.WP_DEBUG) console.log('Connection changed:', connection.effectiveType);
        });
    }

    // Memory management - cleanup on page hide
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            // Pause animations
            document.querySelectorAll('[class*="animate-"]').forEach(el => {
                el.style.animationPlayState = 'paused';
            });
        } else {
            // Resume animations
            document.querySelectorAll('[class*="animate-"]').forEach(el => {
                el.style.animationPlayState = 'running';
            });
        }
    });

    // Passive event listeners for better scroll performance
    const passiveSupported = (() => {
        let passive = false;
        try {
            const options = {
                get passive() {
                    passive = true;
                    return false;
                }
            };
            window.addEventListener('test', null, options);
            window.removeEventListener('test', null, options);
        } catch (err) {
            passive = false;
        }
        return passive;
    })();

    // Export utilities
    window.ATK = window.ATK || {};
    window.ATK.debounce = debounce;
    window.ATK.throttle = throttle;
    window.ATK.addResourceHint = addResourceHint;
    window.ATK.passiveSupported = passiveSupported;

    // Performance monitoring
    if (window.performance && window.performance.timing) {
        window.addEventListener('load', () => {
            setTimeout(() => {
                const perfData = window.performance.timing;
                const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
                const connectTime = perfData.responseEnd - perfData.requestStart;
                const renderTime = perfData.domComplete - perfData.domLoading;
                
                if (window.WP_DEBUG) {
                    console.log('Performance Metrics:', {
                        pageLoadTime: `${pageLoadTime}ms`,
                        connectTime: `${connectTime}ms`,
                        renderTime: `${renderTime}ms`
                    });
                }
                
                // Send to analytics if needed
                if (window.gtag) {
                    gtag('event', 'timing_complete', {
                        name: 'load',
                        value: pageLoadTime,
                        event_category: 'Performance'
                    });
                }
            }, 0);
        });
    }

    // Detect and handle slow connections
    if ('connection' in navigator && navigator.connection.saveData) {
        document.documentElement.classList.add('save-data');
        if (window.WP_DEBUG) console.log('Data Saver mode detected');
    }

    // Optimize third-party scripts
    const loadThirdPartyScripts = () => {
        // Load non-critical scripts after page load
        const scripts = document.querySelectorAll('script[data-lazy]');
        scripts.forEach(script => {
            const newScript = document.createElement('script');
            newScript.src = script.dataset.src;
            newScript.async = true;
            document.body.appendChild(newScript);
        });
    };

    if (document.readyState === 'complete') {
        loadThirdPartyScripts();
    } else {
        window.addEventListener('load', loadThirdPartyScripts);
    }

})();
