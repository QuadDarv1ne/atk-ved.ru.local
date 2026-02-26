/**
 * Modern UI Interactions
 * @package ATK_VED
 */

(function() {
    'use strict';

    // Smooth Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Modern Dropdown
    document.querySelectorAll('.dropdown-modern-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.closest('.dropdown-modern');
            
            // Close other dropdowns
            document.querySelectorAll('.dropdown-modern.active').forEach(d => {
                if (d !== dropdown) d.classList.remove('active');
            });
            
            dropdown.classList.toggle('active');
        });
    });

    // Close dropdown on outside click
    document.addEventListener('click', function() {
        document.querySelectorAll('.dropdown-modern.active').forEach(d => {
            d.classList.remove('active');
        });
    });

    // Modern Tabs
    document.querySelectorAll('.tab-modern').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabsContainer = this.closest('.tabs-modern');
            const targetId = this.dataset.tab;
            
            // Remove active class from all tabs
            tabsContainer.querySelectorAll('.tab-modern').forEach(t => {
                t.classList.remove('active');
            });
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Hide all tab panels
            tabsContainer.querySelectorAll('.tab-panel-modern').forEach(panel => {
                panel.classList.remove('active');
            });
            
            // Show target panel
            const targetPanel = tabsContainer.querySelector(`[data-panel="${targetId}"]`);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
        });
    });

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements with animation classes
    document.querySelectorAll('.fade-in-up, .scale-in, .slide-in-right').forEach(el => {
        observer.observe(el);
    });

    // Copy to Clipboard
    document.querySelectorAll('[data-copy]').forEach(btn => {
        btn.addEventListener('click', async function() {
            const text = this.dataset.copy;
            try {
                await navigator.clipboard.writeText(text);
                showToast('Скопировано!', 'success');
            } catch (err) {
                showToast('Ошибка копирования', 'error');
            }
        });
    });

    // Toast Notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert-modern alert-modern-${type}`;
        toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 10000; min-width: 300px; animation: slideInRight 0.3s ease;';
        toast.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                ${type === 'success' ? 
                    '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>' :
                    '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                }
            </svg>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Lazy Load Images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        img.classList.add('loaded');
                    }
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Progress Bar Animation
    document.querySelectorAll('.progress-modern-bar').forEach(bar => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const width = bar.dataset.progress || '0';
                    setTimeout(() => {
                        bar.style.width = width + '%';
                    }, 100);
                    observer.unobserve(bar);
                }
            });
        });
        observer.observe(bar);
    });

    // Form Validation
    document.querySelectorAll('.input-modern').forEach(input => {
        input.addEventListener('blur', function() {
            validateInput(this);
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                validateInput(this);
            }
        });
    });

    function validateInput(input) {
        const value = input.value.trim();
        const type = input.type;
        let isValid = true;
        let errorMessage = '';

        if (input.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'Это поле обязательно';
        } else if (type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Введите корректный email';
            }
        } else if (type === 'tel' && value) {
            const phoneRegex = /^[\d\s\-\+\(\)]+$/;
            if (!phoneRegex.test(value) || value.length < 10) {
                isValid = false;
                errorMessage = 'Введите корректный телефон';
            }
        }

        const group = input.closest('.input-group-modern');
        if (group) {
            let errorEl = group.querySelector('.error-message');
            if (!errorEl) {
                errorEl = document.createElement('div');
                errorEl.className = 'error-message';
                errorEl.style.cssText = 'color: #e31e24; font-size: 13px; margin-top: 4px;';
                group.appendChild(errorEl);
            }

            if (isValid) {
                input.classList.remove('error');
                input.classList.add('success');
                errorEl.textContent = '';
            } else {
                input.classList.remove('success');
                input.classList.add('error');
                errorEl.textContent = errorMessage;
            }
        }

        return isValid;
    }

    // Keyboard Navigation
    document.addEventListener('keydown', function(e) {
        // ESC to close modals/dropdowns
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active, .dropdown-modern.active').forEach(el => {
                el.classList.remove('active');
            });
        }
    });

    // Parallax Effect
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        document.querySelectorAll('[data-parallax]').forEach(el => {
            const speed = el.dataset.parallax || 0.5;
            el.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });

    // Number Counter Animation
    function animateCounter(element) {
        const target = parseInt(element.dataset.count);
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    });

    document.querySelectorAll('[data-count]').forEach(counter => {
        counterObserver.observe(counter);
    });

    // Ripple Effect
    document.querySelectorAll('.btn-modern, .icon-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255,255,255,0.5);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            setTimeout(() => ripple.remove(), 600);
        });
    });

    // Add ripple animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }
        .input-modern.error {
            border-color: #e31e24;
        }
        .input-modern.success {
            border-color: #10b981;
        }
    `;
    document.head.appendChild(style);

})();
