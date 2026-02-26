/**
 * Components JavaScript
 * @package ATK_VED
 */

(function() {
    'use strict';

    // Mobile Menu Toggle
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-navigation');
    const menuOverlay = document.querySelector('.menu-overlay');

    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            mainNav.classList.toggle('active');
            if (menuOverlay) menuOverlay.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });

        if (menuOverlay) {
            menuOverlay.addEventListener('click', () => {
                menuToggle.classList.remove('active');
                mainNav.classList.remove('active');
                menuOverlay.classList.remove('active');
                document.body.classList.remove('menu-open');
            });
        }
    }

    // Mobile Submenu Toggle
    document.querySelectorAll('.main-menu .menu-item-has-children > a').forEach(link => {
        if (window.innerWidth <= 1024) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.closest('.menu-item-has-children');
                parent.classList.toggle('open');
            });
        }
    });

    // Header Scroll Effect
    let lastScroll = 0;
    const header = document.querySelector('.site-header');

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    }, { passive: true });

    // Carousel
    class Carousel {
        constructor(element) {
            this.carousel = element;
            this.track = element.querySelector('.carousel-track');
            this.slides = element.querySelectorAll('.carousel-slide');
            this.prevBtn = element.querySelector('.carousel-nav-prev');
            this.nextBtn = element.querySelector('.carousel-nav-next');
            this.dots = element.querySelectorAll('.carousel-dot');
            this.currentIndex = 0;
            this.autoplayInterval = null;
            
            this.init();
        }

        init() {
            if (this.prevBtn) {
                this.prevBtn.addEventListener('click', () => this.prev());
            }
            
            if (this.nextBtn) {
                this.nextBtn.addEventListener('click', () => this.next());
            }
            
            this.dots.forEach((dot, index) => {
                dot.addEventListener('click', () => this.goTo(index));
            });
            
            // Auto-play
            if (this.carousel.dataset.autoplay) {
                this.startAutoplay();
                
                this.carousel.addEventListener('mouseenter', () => this.stopAutoplay());
                this.carousel.addEventListener('mouseleave', () => this.startAutoplay());
            }
            
            // Touch support
            this.addTouchSupport();
        }

        goTo(index) {
            this.currentIndex = index;
            this.updateCarousel();
        }

        next() {
            this.currentIndex = (this.currentIndex + 1) % this.slides.length;
            this.updateCarousel();
        }

        prev() {
            this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
            this.updateCarousel();
        }

        updateCarousel() {
            this.track.style.transform = `translateX(-${this.currentIndex * 100}%)`;
            
            this.dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === this.currentIndex);
            });
        }

        startAutoplay() {
            this.autoplayInterval = setInterval(() => this.next(), 5000);
        }

        stopAutoplay() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
            }
        }

        addTouchSupport() {
            let startX = 0;
            let currentX = 0;
            
            this.carousel.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
            }, { passive: true });
            
            this.carousel.addEventListener('touchmove', (e) => {
                currentX = e.touches[0].clientX;
            }, { passive: true });
            
            this.carousel.addEventListener('touchend', () => {
                const diff = startX - currentX;
                if (Math.abs(diff) > 50) {
                    if (diff > 0) {
                        this.next();
                    } else {
                        this.prev();
                    }
                }
            });
        }
    }

    // Initialize all carousels
    document.querySelectorAll('.carousel').forEach(carousel => {
        new Carousel(carousel);
    });

    // FAQ Accordion
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', function() {
            const item = this.closest('.faq-item');
            const isActive = item.classList.contains('active');
            
            // Close all items
            document.querySelectorAll('.faq-item').forEach(i => {
                i.classList.remove('active');
            });
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });

    // FAQ Search
    const faqSearch = document.querySelector('.faq-search-input');
    if (faqSearch) {
        faqSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            
            document.querySelectorAll('.faq-item').forEach(item => {
                const question = item.querySelector('.faq-question-text').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer-content').textContent.toLowerCase();
                
                if (question.includes(query) || answer.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // FAQ Categories
    document.querySelectorAll('.faq-category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.dataset.category;
            
            // Update active button
            document.querySelectorAll('.faq-category-btn').forEach(b => {
                b.classList.remove('active');
            });
            this.classList.add('active');
            
            // Filter FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Contact Form
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('.form-submit');
            const formData = new FormData(this);
            
            // Validation
            let isValid = true;
            this.querySelectorAll('[required]').forEach(field => {
                const group = field.closest('.form-group');
                if (!field.value.trim()) {
                    group.classList.add('error');
                    isValid = false;
                } else {
                    group.classList.remove('error');
                }
            });
            
            if (!isValid) return;
            
            // Submit
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    this.reset();
                    alert('Спасибо! Ваше сообщение отправлено.');
                } else {
                    alert('Ошибка отправки. Попробуйте позже.');
                }
            } catch (error) {
                alert('Ошибка отправки. Попробуйте позже.');
            } finally {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            }
        });
    }

    // Lightbox for carousel
    const lightbox = document.querySelector('.carousel-lightbox');
    if (lightbox) {
        document.querySelectorAll('.carousel-grid-item').forEach(item => {
            item.addEventListener('click', function() {
                const img = this.querySelector('img');
                const lightboxImg = lightbox.querySelector('img');
                lightboxImg.src = img.src;
                lightbox.classList.add('active');
            });
        });
        
        lightbox.querySelector('.carousel-lightbox-close').addEventListener('click', () => {
            lightbox.classList.remove('active');
        });
        
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                lightbox.classList.remove('active');
            }
        });
    }

})();
