<?php
/**
 * Accessibility enhancements
 * 
 * @package ATK_VED
 * @since 1.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add more robust focus management
 */
function atk_ved_improve_keyboard_navigation() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Trap focus inside modals
            const modals = document.querySelectorAll(".modal");
            modals.forEach(function(modal) {
                const focusableElements = modal.querySelectorAll(
                    "button, [href], input, select, textarea, [tabindex]:not([tabindex=\'-1\'])"
                );
                const firstElement = focusableElements[0];
                const lastElement = focusableElements[focusableElements.length - 1];
                
                modal.addEventListener("keydown", function(e) {
                    if (e.key === "Tab") {
                        if (e.shiftKey && document.activeElement === firstElement) {
                            e.preventDefault();
                            lastElement.focus();
                        } else if (!e.shiftKey && document.activeElement === lastElement) {
                            e.preventDefault();
                            firstElement.focus();
                        }
                    }
                });
            });
            
            // Add skip to main content link
            const skipLink = document.querySelector(".skip-link");
            if (skipLink) {
                skipLink.addEventListener("click", function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute("href"));
                    if (target) {
                        target.setAttribute("tabindex", "-1");
                        target.focus();
                        target.scrollIntoView({ behavior: "smooth", block: "start" });
                        
                        target.addEventListener("blur", function() {
                            this.removeAttribute("tabindex");
                        }, { once: true });
                    }
                });
            }
        });
    </script>';
}
add_action('wp_footer', 'atk_ved_improve_keyboard_navigation');

/**
 * Add more descriptive ARIA labels
 */
function atk_ved_add_descriptive_aria_labels() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Add ARIA labels to form elements
            const forms = document.querySelectorAll("form");
            forms.forEach(function(form, index) {
                if (!form.hasAttribute("aria-label") && !form.hasAttribute("aria-labelledby")) {
                    const titleElement = form.querySelector("h1, h2, h3");
                    if (titleElement) {
                        form.setAttribute("aria-label", titleElement.textContent.trim());
                    } else {
                        form.setAttribute("aria-label", "Форма " + (index + 1));
                    }
                }
            });
            
            // Add ARIA descriptions to complex widgets
            const accordions = document.querySelectorAll(".accordion-item, .faq-item");
            accordions.forEach(function(item, index) {
                const header = item.querySelector(".accordion-header, .faq-question") || item.querySelector("button, [role=\"button\"]");
                if (header) {
                    header.setAttribute("aria-expanded", item.classList.contains("is-active") ? "true" : "false");
                    
                    const content = item.querySelector(".accordion-body, .faq-answer") || header.nextElementSibling;
                    if (content) {
                        const contentId = "accordion-content-" + index;
                        content.id = contentId;
                        header.setAttribute("aria-controls", contentId);
                    }
                }
            });
        });
    </script>';
}
add_action('wp_footer', 'atk_ved_add_descriptive_aria_labels');