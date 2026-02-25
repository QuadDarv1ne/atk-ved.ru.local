<?php
/**
 * UI Components Shortcodes: Modals, Tabs, Accordions
 * 
 * @package ATK_VED
 * @since 2.1.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Шорткод модального окна
 * 
 * Usage: [modal id="my-modal" trigger="Click me" size="md" position="center"]
 * Content goes here
 * [/modal]
 */
function atk_ved_modal_shortcode(array $atts, string $content = ''): string {
    $atts = shortcode_atts([
        'id' => 'modal-' . uniqid(),
        'trigger' => '',
        'trigger_class' => 'btn btn-primary',
        'size' => 'md', // sm, md, lg, xl, full
        'position' => 'center', // center, top, bottom, left, right
        'title' => '',
        'show_close' => '1',
        'close_on_backdrop' => '1',
        'class' => '',
        'footer' => '',
        'open_on_load' => '0', // Открытие модального окна при загрузке страницы
        'escape_close' => '1', // Закрытие по клавише ESC
    ], $atts);

    $size_class = '';
    if ($atts['size'] !== 'md') {
        $size_class = 'modal-' . esc_attr($atts['size']);
    }

    ob_start();
    ?>

    <?php if ($atts['trigger']): ?>
        <button type="button" 
                class="<?php echo esc_attr($atts['trigger_class']); ?>" 
                data-modal-open="<?php echo esc_attr($atts['id']); ?>"
                aria-haspopup="dialog"
                aria-controls="<?php echo esc_attr($atts['id']); ?>"
                aria-expanded="false">
            <?php echo esc_html($atts['trigger']); ?>
        </button>
    <?php endif; ?>

    <div id="<?php echo esc_attr($atts['id']); ?>" 
         class="modal modal-<?php echo esc_attr($atts['position']); ?> <?php echo esc_attr($size_class); ?> <?php echo esc_attr($atts['class']); ?>"
         role="dialog"
         aria-modal="true"
         aria-labelledby="<?php echo esc_attr($atts['id']); ?>-title"
         <?php if ($atts['close_on_backdrop'] !== '1') echo 'data-static-backdrop="true"'; ?>
         <?php if ($atts['escape_close'] !== '1') echo 'data-escape-close="false"'; ?>
         <?php if ($atts['open_on_load'] === '1') echo 'data-open-on-load="true"'; ?>
         style="display: none;">
        
        <div class="modal-backdrop" tabindex="-1"></div>
        
        <div class="modal-content" role="document">
            <?php if ($atts['title'] || $atts['show_close'] === '1'): ?>
            <div class="modal-header">
                <?php if ($atts['title']): ?>
                    <h3 class="modal-title" id="<?php echo esc_attr($atts['id']); ?>-title"><?php echo esc_html($atts['title']); ?></h3>
                <?php endif; ?>
                
                <?php if ($atts['show_close'] === '1'): ?>
                    <button type="button" 
                            class="modal-close" 
                            data-modal-close="<?php echo esc_attr($atts['id']); ?>"
                            aria-label="<?php esc_attr_e('Закрыть модальное окно', 'atk-ved'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="modal-body">
                <?php echo do_shortcode($content); ?>
            </div>

            <?php if ($atts['footer']): ?>
            <div class="modal-footer">
                <?php echo do_shortcode($atts['footer']); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('modal', 'atk_ved_modal_shortcode');

/**
 * Шорткод табов
 * 
 * Usage: [tabs id="tabs-1" style="default" vertical="0"]
 * [tab title="Tab 1" icon="📄"]Content 1[/tab]
 * [tab title="Tab 2" icon="📊"]Content 2[/tab]
 * [/tabs]
 */
function atk_ved_tabs_shortcode(array $atts, string $content = ''): string {
    $atts = shortcode_atts([
        'id' => 'tabs-' . uniqid(),
        'style' => 'default', // default, pill
        'vertical' => '0',
        'active' => '0',
        'class' => '',
        'keyboard_navigation' => '1', // Включить навигацию с клавиатуры
    ], $atts);

    $class = 'tabs';
    if ($atts['style'] === 'pill') {
        $class .= ' tabs-pill';
    }
    if ($atts['vertical'] === '1') {
        $class .= ' tabs-vertical';
    }
    if ($atts['class']) {
        $class .= ' ' . esc_attr($atts['class']);
    }

    // Парсим табы из контента
    preg_match_all('/\[tab([^\]]*)\](.*?)\[\/tab\]/s', $content, $matches, PREG_SET_ORDER);
    
    if (empty($matches)) {
        return '<p>' . __('Ошибка: нет табов в содержимом', 'atk-ved') . '</p>';
    }

    $active_index = (int) $atts['active'];
    $tabs_header = '';
    $tabs_content = '';

    foreach ($matches as $index => $match) {
        $tab_atts = shortcode_parse_atts($match[1]);
        $tab_title = $tab_atts['title'] ?? sprintf(__('Таб %d', 'atk-ved'), $index + 1);
        $tab_icon = $tab_atts['icon'] ?? '';
        $tab_content = $match[2];
        $tab_id = $atts['id'] . '-tab-' . $index;
        $is_active = ($index === $active_index);

        $tabs_header .= sprintf(
            '<button type="button" class="tab-button%s" id="%s-button" data-tab="%s" role="tab" aria-controls="%s" aria-selected="%s" tabindex="%s">',
            $is_active ? ' is-active' : '',
            esc_attr($tab_id),
            esc_attr($tab_id),
            esc_attr($tab_id . '-panel'),
            $is_active ? 'true' : 'false',
            $is_active ? '0' : '-1'
        );
        
        if ($tab_icon) {
            $tabs_header .= '<span class="tab-icon" aria-hidden="true">' . esc_html($tab_icon) . '</span>';
        }
        $tabs_header .= '<span class="tab-title">' . esc_html($tab_title) . '</span>';
        $tabs_header .= '</button>';

        $tabs_content .= sprintf(
            '<div class="tab-panel%s" id="%s-panel" role="tabpanel" aria-labelledby="%s-button" tabindex="0">',
            $is_active ? ' is-active' : '',
            esc_attr($tab_id),
            esc_attr($tab_id . '-button')
        );
        $tabs_content .= do_shortcode($tab_content);
        $tabs_content .= '</div>';
    }

    ob_start();
    ?>
    <div class="<?php echo esc_attr($class); ?>" id="<?php echo esc_attr($atts['id']); ?>" role="tablist"
         <?php if ($atts['keyboard_navigation'] === '1'): ?>data-keyboard-navigation="true"<?php endif; ?>
         aria-orientation="<?php echo $atts['vertical'] === '1' ? 'vertical' : 'horizontal'; ?>">
        <div class="tabs-header" role="tablist">
            <?php echo $tabs_header; ?>
        </div>
        <div class="tabs-content">
            <?php echo $tabs_content; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('tabs', 'atk_ved_tabs_shortcode');

/**
 * Шорткод отдельного таба (используется внутри tabs)
 */
function atk_ved_tab_shortcode(array $atts, string $content = ''): string {
    // Этот шорткод обрабатывается внутри atk_ved_tabs_shortcode
    return $content;
}
add_shortcode('tab', 'atk_ved_tab_shortcode');

/**
 * Шорткод аккордеона
 * 
 * Usage: [accordion id="accordion-1" exclusive="1" class=""]
 * [accordion-item title="Question 1" icon="1"]Answer 1[/accordion-item]
 * [accordion-item title="Question 2" icon="1"]Answer 2[/accordion-item]
 * [/accordion]
 */
function atk_ved_accordion_shortcode(array $atts, string $content = ''): string {
    $atts = shortcode_atts([
        'id' => 'accordion-' . uniqid(),
        'exclusive' => '0', // Только один открытый элемент
        'class' => '',
        'seamless' => '0',
        'keyboard_navigation' => '1', // Включить навигацию с клавиатуры
        'allow_multiple' => '1', // Разрешить открытие нескольких элементов
    ], $atts);

    $class = 'accordion';
    if ($atts['exclusive'] === '1') {
        $class .= ' accordion-exclusive';
    }
    if ($atts['seamless'] === '1') {
        $class .= ' accordion-seamless';
    }
    if ($atts['class']) {
        $class .= ' ' . esc_attr($atts['class']);
    }

    // Парсим элементы аккордеона
    preg_match_all('/\[accordion-item([^\]]*)\](.*?)\[\/accordion-item\]/s', $content, $matches, PREG_SET_ORDER);
    
    if (empty($matches)) {
        return '<p>' . __('Ошибка: нет элементов в аккордеоне', 'atk-ved') . '</p>';
    }

    ob_start();
    ?>
    <div class="<?php echo esc_attr($class); ?>" id="<?php echo esc_attr($atts['id']); ?>"
         <?php if ($atts['keyboard_navigation'] === '1'): ?>data-keyboard-navigation="true"<?php endif; ?>
         <?php if ($atts['allow_multiple'] !== '1'): ?>data-allow-multiple="false"<?php endif; ?>>
        <?php foreach ($matches as $index => $match): ?>
            <?php
            $item_atts = shortcode_parse_atts($match[1]);
            $item_title = $item_atts['title'] ?? sprintf(__('Элемент %d', 'atk-ved'), $index + 1);
            $item_icon = $item_atts['icon'] ?? '1';
            $item_content = $match[2];
            $item_id = $atts['id'] . '-item-' . $index;
            $is_active = isset($item_atts['active']) && $item_atts['active'] === '1';
            ?>
            
            <div class="accordion-item <?php echo $is_active ? 'is-active' : ''; ?>" 
                 id="<?php echo esc_attr($item_id); ?>">
                
                <button type="button" 
                        class="accordion-header" 
                        id="<?php echo esc_attr($item_id); ?>-header"
                        aria-expanded="<?php echo $is_active ? 'true' : 'false'; ?>"
                        aria-controls="<?php echo esc_attr($item_id); ?>-body"
                        <?php if ($atts['keyboard_navigation'] === '1'): ?>tabindex="0"<?php endif; ?>>
                    
                    <span class="accordion-title"><?php echo esc_html($item_title); ?></span>
                    
                    <?php if ($item_icon !== '0'): ?>
                    <span class="accordion-icon" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </span>
                    <?php endif; ?>
                </button>
                
                <div class="accordion-body" id="<?php echo esc_attr($item_id); ?>-body" aria-labelledby="<?php echo esc_attr($item_id); ?>-header">
                    <div class="accordion-content">
                        <?php echo do_shortcode($item_content); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('accordion', 'atk_ved_accordion_shortcode');

/**
 * Шорткод элемента аккордеона
 */
function atk_ved_accordion_item_shortcode(array $atts, string $content = ''): string {
    return $content;
}
add_shortcode('accordion-item', 'atk_ved_accordion_item_shortcode');

/**
 * Шорткод FAQ (аккордеон для часто задаваемых вопросов)
 * 
 * Usage: [faq]
 * [faq-item q="Вопрос?" a="Ответ"]
 * [/faq]
 */
function atk_ved_faq_shortcode(array $atts, string $content = ''): string {
    $atts = shortcode_atts([
        'id' => 'faq-' . uniqid(),
        'class' => '',
    ], $atts);

    $class = 'accordion accordion-seamless ' . esc_attr($atts['class']);

    // Парсим элементы FAQ
    preg_match_all('/\[faq-item([^\]]*)\]/s', $content, $matches, PREG_SET_ORDER);
    
    if (empty($matches)) {
        return '<p>' . __('Ошибка: нет вопросов в FAQ', 'atk-ved') . '</p>';
    }

    ob_start();
    ?>
    <div class="<?php echo esc_attr($class); ?>" id="<?php echo esc_attr($atts['id']); ?>" itemscope itemtype="https://schema.org/FAQPage">
        <?php foreach ($matches as $index => $match): ?>
            <?php
            $item_atts = shortcode_parse_atts($match[1]);
            $question = $item_atts['q'] ?? sprintf(__('Вопрос %d', 'atk-ved'), $index + 1);
            $answer = $item_atts['a'] ?? '';
            $item_id = $atts['id'] . '-item-' . $index;
            ?>
            
            <div class="accordion-item" itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
                <button type="button" class="accordion-header" aria-expanded="false">
                    <span class="accordion-title" itemprop="name"><?php echo esc_html($question); ?></span>
                    <span class="accordion-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </span>
                </button>
                
                <div class="accordion-body" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="accordion-content" itemprop="text">
                        <?php echo do_shortcode($answer); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('faq', 'atk_ved_faq_shortcode');

// Шорткод прогресс-бара
function atk_ved_progress_bar_shortcode(array $atts): string {
    $atts = shortcode_atts([
        'percent' => '50',
        'label' => '',
        'color' => '#e31e24', // Цвет из бренд-стиля
        'height' => '20',
        'animate' => '1',
        'class' => '',
    ], $atts);

    $percent = min(100, max(0, (int) $atts['percent']));
    $animation_class = $atts['animate'] === '1' ? 'progress-bar-animated' : '';

    ob_start();
    ?>
    <div class="progress-container <?php echo esc_attr($atts['class']); ?>">
        <?php if ($atts['label']): ?>
            <div class="progress-label">
                <span><?php echo esc_html($atts['label']); ?></span>
                <span><?php echo $percent; ?>%</span>
            </div>
        <?php endif; ?>
        <div class="progress-bar-wrapper" style="height: <?php echo esc_attr($atts['height']); ?>px; background-color: #e0e0e0;">
            <div class="progress-bar <?php echo esc_attr($animation_class); ?>" 
                 style="width: 0%; height: 100%; background-color: <?php echo esc_attr($atts['color']); ?>;"
                 data-percent="<?php echo $percent; ?>">
            </div>
        </div>
    </div>
    <style>
        .progress-container {
            margin: 15px 0;
        }
        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 14px;
            color: #2c2c2c;
        }
        .progress-bar-wrapper {
            border-radius: 10px;
            overflow: hidden;
        }
        .progress-bar {
            transition: width 1.5s ease-in-out;
        }
        .progress-bar.progress-bar-animated {
            animation: progressAnimation 1.5s ease-out;
        }
        @keyframes progressAnimation {
            from { width: 0%; }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.querySelector('.progress-bar[data-percent]');
            if (progressBar) {
                setTimeout(() => {
                    progressBar.style.width = progressBar.getAttribute('data-percent') + '%';
                }, 100);
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('progress_bar', 'atk_ved_progress_bar_shortcode');

add_shortcode('faq-item', '__return_empty_string');
