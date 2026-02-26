/**
 * Premium Landing Interactions
 * @package ATK_VED
 */

(function() {
    'use strict';

    // FAQ Accordion
    document.querySelectorAll('.faq-premium-question').forEach(question => {
        question.addEventListener('click', function() {
            const item = this.closest('.faq-premium-item');
            const isActive = item.classList.contains('active');
            
            // Close all items
            document.querySelectorAll('.faq-premium-item').forEach(i => {
                i.classList.remove('active');
            });
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                const headerOffset = 80;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Counter animation for stats
    function animateCounter(element) {
        const target = parseInt(element.textContent.replace(/\D/g, ''));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        const suffix = element.textContent.replace(/[0-9]/g, '');

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                element.textContent = target + suffix;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current) + suffix;
            }
        }, 16);
    }

    // Observe stats for animation
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const numbers = entry.target.querySelectorAll('.stat-number');
                numbers.forEach(num => animateCounter(num));
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    const statsBar = document.querySelector('.stats-bar');
    if (statsBar) {
        statsObserver.observe(statsBar);
    }

    // Parallax effect for hero background
    window.addEventListener('scroll', () => {
        const heroBg = document.querySelector('.hero-premium-bg');
        if (heroBg) {
            const scrolled = window.pageYOffset;
            heroBg.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    }, { passive: true });

    // Reveal animations on scroll
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Apply reveal animation to cards
    document.querySelectorAll('.service-premium-card, .delivery-card, .testimonial-premium-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        revealObserver.observe(card);
    });

    // Add stagger delay to grid items
    document.querySelectorAll('.services-premium-grid .service-premium-card').forEach((card, index) => {
        card.style.transitionDelay = `${index * 0.1}s`;
    });

    document.querySelectorAll('.delivery-grid .delivery-card').forEach((card, index) => {
        card.style.transitionDelay = `${index * 0.1}s`;
    });

    // Testimonials slider (if needed)
    const testimonialsGrid = document.querySelector('.testimonials-premium-grid');
    if (testimonialsGrid && window.innerWidth < 768) {
        // Convert to slider on mobile
        testimonialsGrid.style.display = 'flex';
        testimonialsGrid.style.overflowX = 'auto';
        testimonialsGrid.style.scrollSnapType = 'x mandatory';
        
        document.querySelectorAll('.testimonial-premium-card').forEach(card => {
            card.style.minWidth = '280px';
            card.style.scrollSnapAlign = 'start';
        });
    }

})();
