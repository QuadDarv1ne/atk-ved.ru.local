<?php
/**
 * Улучшения доступности (Accessibility Enhancements)
 * 
 * @package ATK_VED
 * @since 2.2.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Добавление улучшений доступности
 */
function atk_ved_accessibility_enhancements() {
    // Добавляем стили для индикатора фокуса
    add_action('wp_head', 'atk_ved_add_focus_styles');
    
    // Улучшаем навигацию
    add_filter('wp_nav_menu_items', 'atk_ved_add_skip_links', 10, 2);
    
    // Улучшаем изображения
    add_filter('wp_get_attachment_image_attributes', 'atk_ved_add_img_alt_attributes', 10, 2);
    
    // Добавляем ARIA-роли
    add_action('wp_footer', 'atk_ved_add_aria_landmarks');
    
    // Улучшаем формы
    add_action('wp_footer', 'atk_ved_add_form_accessibility');
    
    // Добавляем возможность увеличения текста
    add_action('wp_footer', 'atk_ved_add_text_scaling_controls');
}
add_action('after_setup_theme', 'atk_ved_accessibility_enhancements');

/**
 * Добавление стилей для индикатора фокуса
 */
function atk_ved_add_focus_styles() {
    echo '<style>
        /* Улучшенный индикатор фокуса для доступности */
        a:focus,
        button:focus,
        input:focus,
        textarea:focus,
        select:focus,
        *[tabindex]:focus {
            outline: 3px solid #e31e24;
            outline-offset: 2px;
            border-radius: 2px;
        }
        
        /* Убираем фокус для мышиных пользователей */
        .js-focus-visible :focus:not(.focus-visible) {
            outline: none;
        }
        
        .js-focus-visible .focus-visible {
            outline: 3px solid #e31e24;
            outline-offset: 2px;
            border-radius: 2px;
        }
        
        /* Скрытие вспомогательных элементов для sighted users, но доступных для screen readers */
        .screen-reader-text {
            position: absolute;
            top: -9999px;
            left: -9999px;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        .screen-reader-text:focus {
            position: static;
            width: auto;
            height: auto;
            margin: 0;
            overflow: visible;
            clip: auto;
            white-space: normal;
            padding: 15px 23px 14px;
            background: #f1f1f1;
            color: #0073aa;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 3px;
            box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6);
            z-index: 100000;
        }
    </style>';
}

/**
 * Добавление ссылок для пропуска навигации
 */
function atk_ved_add_skip_links($items, $args) {
    // Добавляем ссылки для пропуска только в главное меню
    if ($args->theme_location === 'primary') {
        $skip_links = '
            <div class="skip-links" style="position: absolute; top: 0; left: 0; width: 1px; height: 1px; overflow: hidden;">
                <a class="screen-reader-text skip-link" href="#main-content">Перейти к основному содержанию</a>
                <a class="screen-reader-text skip-link" href="#main-navigation">Перейти к навигации</a>
                <a class="screen-reader-text skip-link" href="#search-form">Перейти к поиску</a>
            </div>';
        
        return $skip_links . $items;
    }
    return $items;
}

/**
 * Улучшение атрибутов alt для изображений
 */
function atk_ved_add_img_alt_attributes($attr, $attachment) {
    if (empty($attr['alt'])) {
        $attachment_title = get_the_title($attachment->ID);
        if (!empty($attachment_title)) {
            $attr['alt'] = $attachment_title;
        } else {
            $attr['alt'] = 'Изображение';
        }
    }
    return $attr;
}

/**
 * Добавление ARIA-ландшафта
 */
function atk_ved_add_aria_landmarks() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Добавляем ARIA роли к основным областям
            const mainContent = document.querySelector("main, [role=\"main\"]") || document.querySelector("body");
            if (mainContent && !mainContent.hasAttribute("role")) {
                mainContent.setAttribute("role", "main");
                mainContent.setAttribute("aria-label", "Основное содержание");
            }
            
            const header = document.querySelector("header") || document.querySelector(".site-header");
            if (header) {
                header.setAttribute("role", "banner");
                header.setAttribute("aria-label", "Шапка сайта");
            }
            
            const navigation = document.querySelector("nav") || document.querySelector(".main-nav");
            if (navigation) {
                navigation.setAttribute("role", "navigation");
                navigation.setAttribute("aria-label", "Основная навигация");
            }
            
            const footer = document.querySelector("footer") || document.querySelector(".site-footer");
            if (footer) {
                footer.setAttribute("role", "contentinfo");
                footer.setAttribute("aria-label", "Подвал сайта");
            }
            
            // Добавляем ARIA для кнопок
            const buttons = document.querySelectorAll("button:not([aria-label]):not([aria-describedby])");
            buttons.forEach(function(button) {
                if (!button.getAttribute("title") && button.textContent.trim()) {
                    button.setAttribute("aria-label", button.textContent.trim());
                }
            });
            
            // Добавляем ARIA для форм
            const forms = document.querySelectorAll("form");
            forms.forEach(function(form, index) {
                if (!form.hasAttribute("aria-label") && !form.hasAttribute("aria-labelledby")) {
                    form.setAttribute("aria-label", "Форма " + (index + 1));
                }
            });
            
            // Добавляем ARIA для всплывающих окон
            const modals = document.querySelectorAll("[id*=\"modal\"], .modal");
            modals.forEach(function(modal, index) {
                if (modal.hasAttribute("role") || modal.classList.contains("modal")) {
                    modal.setAttribute("role", "dialog");
                    modal.setAttribute("aria-modal", "true");
                    if (!modal.hasAttribute("aria-label") && !modal.hasAttribute("aria-labelledby")) {
                        modal.setAttribute("aria-label", "Модальное окно " + (index + 1));
                    }
                }
            });
        });
    </script>';
}

/**
 * Улучшение доступности форм
 */
function atk_ved_add_form_accessibility() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Добавляем ARIA для полей форм
            const inputs = document.querySelectorAll("input, textarea, select");
            inputs.forEach(function(input) {
                if (!input.hasAttribute("aria-label") && 
                    !input.hasAttribute("aria-labelledby") &&
                    !input.id) {
                    
                    // Пытаемся найти связанную метку
                    const parentLabel = input.closest("label");
                    if (parentLabel && parentLabel.textContent.trim()) {
                        input.setAttribute("aria-label", parentLabel.textContent.trim());
                    } else {
                        const label = document.querySelector("label[for=\"" + input.name + "\"]");
                        if (label && label.textContent.trim()) {
                            input.setAttribute("aria-label", label.textContent.trim());
                        }
                    }
                }
                
                // Для обязательных полей
                if (input.hasAttribute("required")) {
                    input.setAttribute("aria-required", "true");
                }
            });
            
            // Добавляем обработчик для уведомлений об ошибках
            const formErrors = document.querySelectorAll(".form-error");
            formErrors.forEach(function(error, index) {
                const relatedInput = error.previousElementSibling;
                if (relatedInput && relatedInput.tagName.match(/INPUT|TEXTAREA|SELECT/i)) {
                    const errorId = "error-" + Date.now() + "-" + index;
                    error.id = errorId;
                    relatedInput.setAttribute("aria-describedby", errorId);
                    relatedInput.setAttribute("aria-invalid", "true");
                }
            });
        });
    </script>';
}

/**
 * Добавление элементов управления масштабированием текста
 */
function atk_ved_add_text_scaling_controls() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Добавляем кнопки управления размером текста
            if (!document.querySelector(".text-scaling-controls")) {
                const controlsHTML = `
                    <div class="text-scaling-controls" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; display: flex; gap: 10px; background: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <button id="text-increase" aria-label="Увеличить размер текста" style="padding: 8px 12px; border: 1px solid #ddd; background: #f8f8f8; cursor: pointer;">A+</button>
                        <button id="text-decrease" aria-label="Уменьшить размер текста" style="padding: 8px 12px; border: 1px solid #ddd; background: #f8f8f8; cursor: pointer;">A-</button>
                        <button id="text-reset" aria-label="Сбросить размер текста" style="padding: 8px 12px; border: 1px solid #ddd; background: #f8f8f8; cursor: pointer;">A</button>
                    </div>
                `;
                
                document.body.insertAdjacentHTML("beforeend", controlsHTML);
                
                let currentScale = 1;
                const minScale = 0.8;
                const maxScale = 1.5;
                const scaleStep = 0.1;
                
                document.getElementById("text-increase").addEventListener("click", function() {
                    if (currentScale < maxScale) {
                        currentScale += scaleStep;
                        document.body.style.transform = "scale(" + currentScale + ")";
                        document.body.style.transformOrigin = "top left";
                    }
                });
                
                document.getElementById("text-decrease").addEventListener("click", function() {
                    if (currentScale > minScale) {
                        currentScale -= scaleStep;
                        document.body.style.transform = "scale(" + currentScale + ")";
                        document.body.style.transformOrigin = "top left";
                    }
                });
                
                document.getElementById("text-reset").addEventListener("click", function() {
                    currentScale = 1;
                    document.body.style.transform = "scale(1)";
                });
            }
        });
    </script>';
}

/**
 * Улучшение доступности для аккордеонов
 */
function atk_ved_enhance_accordion_accessibility() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Улучшаем доступность аккордеонов
            const accordions = document.querySelectorAll(".accordion-item, .faq-item");
            accordions.forEach(function(item, index) {
                const header = item.querySelector(".accordion-header, .faq-question") || item.querySelector("button, [role=\"button\"]");
                if (header) {
                    header.setAttribute("role", "button");
                    header.setAttribute("tabindex", "0");
                    header.setAttribute("aria-expanded", item.classList.contains("is-active") ? "true" : "false");
                    
                    const content = item.querySelector(".accordion-body, .faq-answer") || header.nextElementSibling;
                    if (content) {
                        const contentId = "accordion-content-" + index;
                        content.id = contentId;
                        header.setAttribute("aria-controls", contentId);
                    }
                    
                    // Обработчики клавиатуры
                    header.addEventListener("keydown", function(e) {
                        if (e.key === "Enter" || e.key === " ") {
                            e.preventDefault();
                            header.click();
                        }
                    });
                }
            });
        });
    </script>';
}
add_action('wp_footer', 'atk_ved_enhance_accordion_accessibility');

/**
 * Улучшение доступности для табов
 */
function atk_ved_enhance_tabs_accessibility() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Улучшаем доступность табов
            const tabLists = document.querySelectorAll(".tabs");
            tabLists.forEach(function(tabList) {
                const tabs = tabList.querySelectorAll(".tab-button");
                const panels = tabList.querySelectorAll(".tab-panel");
                
                tabs.forEach(function(tab, index) {
                    tab.setAttribute("role", "tab");
                    tab.setAttribute("tabindex", "-1"); // Только активный таб получает tabindex="0"
                    
                    const panel = panels[index];
                    if (panel) {
                        const panelId = "tab-panel-" + Date.now() + "-" + index;
                        panel.id = panelId;
                        tab.setAttribute("aria-controls", panelId);
                        panel.setAttribute("role", "tabpanel");
                        panel.setAttribute("aria-labelledby", tab.id || "tab-" + index);
                        
                        if (tab.classList.contains("is-active")) {
                            tab.setAttribute("tabindex", "0");
                            tab.setAttribute("aria-selected", "true");
                            panel.setAttribute("aria-hidden", "false");
                        } else {
                            tab.setAttribute("aria-selected", "false");
                            panel.setAttribute("aria-hidden", "true");
                        }
                    }
                });
                
                // Добавляем навигацию клавишами
                tabs.forEach(function(tab) {
                    tab.addEventListener("keydown", function(e) {
                        let newTab;
                        switch(e.key) {
                            case "ArrowLeft":
                            case "ArrowUp":
                                e.preventDefault();
                                newTab = tab.previousElementSibling || tabs[tabs.length - 1];
                                break;
                            case "ArrowRight":
                            case "ArrowDown":
                                e.preventDefault();
                                newTab = tab.nextElementSibling || tabs[0];
                                break;
                            case "Home":
                                e.preventDefault();
                                newTab = tabs[0];
                                break;
                            case "End":
                                e.preventDefault();
                                newTab = tabs[tabs.length - 1];
                                break;
                        }
                        
                        if (newTab) {
                            tabs.forEach(t => t.setAttribute("tabindex", "-1"));
                            newTab.setAttribute("tabindex", "0");
                            newTab.focus();
                            newTab.click();
                        }
                    });
                });
            });
        });
    </script>';
}
add_action('wp_footer', 'atk_ved_enhance_tabs_accessibility');