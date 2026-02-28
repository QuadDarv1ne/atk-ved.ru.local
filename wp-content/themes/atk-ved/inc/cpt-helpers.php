<?php
/**
 * Хелперы для работы с Custom Post Types
 *
 * @package ATK_VED
 * @since 3.6.0
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Получение всех услуг
 *
 * @param bool $use_cache Использовать кэширование
 * @return array<int, array<string, mixed>>
 */
function atk_ved_get_services(bool $use_cache = true): array {
    $args = [
        'post_type' => 'service',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'post_status' => 'publish',
    ];

    $posts = $use_cache ? atk_ved_get_cached_posts($args) : get_posts($args);

    $services = [];
    foreach ($posts as $post) {
        $services[] = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt ?: wp_trim_words($post->post_content, 20),
            'icon' => get_post_meta($post->ID, '_service_icon', true) ?: '📦',
        ];
    }

    return $services;
}

/**
 * Получение этапов работы
 *
 * @param bool $use_cache Использовать кэширование
 * @return array<int, array<string, mixed>>
 */
function atk_ved_get_process_steps(bool $use_cache = true): array {
    $args = [
        'post_type' => 'process_step',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'post_status' => 'publish',
    ];

    $posts = $use_cache ? atk_ved_get_cached_posts($args) : get_posts($args);

    $steps = [];
    foreach ($posts as $post) {
        $steps[] = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'icon' => get_post_meta($post->ID, '_step_icon', true) ?: '📝',
            'number' => get_post_meta($post->ID, '_step_number', true) ?: '',
        ];
    }

    return $steps;
}

/**
 * Получение FAQ
 *
 * @param bool $use_cache Использовать кэширование
 * @return array<int, array<string, mixed>>
 */
function atk_ved_get_faq(bool $use_cache = true): array {
    $args = [
        'post_type' => 'faq',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'post_status' => 'publish',
    ];

    $posts = $use_cache ? atk_ved_get_cached_posts($args) : get_posts($args);

    $faqs = [];
    foreach ($posts as $post) {
        $faqs[] = [
            'id' => $post->ID,
            'question' => $post->post_title,
            'answer' => $post->post_content,
        ];
    }

    return $faqs;
}

/**
 * Рендеринг услуг
 *
 * @param array<string, mixed> $args Аргументы
 * @return void
 */
function atk_ved_render_services(array $args = []): void {
    $defaults = [
        'animate' => true,
        'stagger' => true,
        'stagger_delay' => 100,
    ];

    $args = wp_parse_args($args, $defaults);
    $services = atk_ved_get_services();

    if (empty($services)) {
        echo '<p>' . esc_html__('Услуги не найдены', 'atk-ved') . '</p>';
        return;
    }

    $wrapper_attrs = '';
    if ($args['stagger']) {
        $wrapper_attrs = sprintf('data-stagger data-stagger-delay="%d"', $args['stagger_delay']);
    }

    echo '<div class="services-grid-enhanced" ' . $wrapper_attrs . '>';

    foreach ($services as $service) {
        ?>
        <article class="service-card-enhanced">
            <div class="service-icon" data-morph><?php echo esc_html($service['icon']); ?></div>
            <h3 class="service-title"><?php echo esc_html($service['title']); ?></h3>
            <p class="service-desc"><?php echo esc_html($service['excerpt']); ?></p>
        </article>
        <?php
    }

    echo '</div>';
}

/**
 * Рендеринг этапов работы
 *
 * @param array<string, mixed> $args Аргументы
 * @return void
 */
function atk_ved_render_process_steps(array $args = []): void {
    $defaults = [
        'animate' => true,
        'stagger' => true,
        'stagger_delay' => 120,
    ];

    $args = wp_parse_args($args, $defaults);
    $steps = atk_ved_get_process_steps();

    if (empty($steps)) {
        echo '<p>' . esc_html__('Этапы не найдены', 'atk-ved') . '</p>';
        return;
    }

    $wrapper_attrs = '';
    if ($args['stagger']) {
        $wrapper_attrs = sprintf('data-stagger data-stagger-delay="%d"', $args['stagger_delay']);
    }

    echo '<div class="process-grid" ' . $wrapper_attrs . '>';

    foreach ($steps as $step) {
        ?>
        <div class="process-step-card">
            <?php if (!empty($step['number'])): ?>
                <div class="step-number-badge"><?php echo esc_html($step['number']); ?></div>
            <?php endif; ?>
            <div class="step-icon-large" data-morph><?php echo esc_html($step['icon']); ?></div>
            <h3 class="step-title"><?php echo esc_html($step['title']); ?></h3>
            <p class="step-desc"><?php echo esc_html($step['content']); ?></p>
        </div>
        <?php
    }

    echo '</div>';
}

/**
 * Рендеринг FAQ
 *
 * @param array<string, mixed> $args Аргументы
 * @return void
 */
function atk_ved_render_faq(array $args = []): void {
    $defaults = [
        'animate' => true,
        'stagger' => true,
        'stagger_delay' => 100,
    ];

    $args = wp_parse_args($args, $defaults);
    $faqs = atk_ved_get_faq();

    if (empty($faqs)) {
        echo '<p>' . esc_html__('Вопросы не найдены', 'atk-ved') . '</p>';
        return;
    }

    $wrapper_attrs = '';
    if ($args['stagger']) {
        $wrapper_attrs = sprintf('data-stagger data-stagger-delay="%d"', $args['stagger_delay']);
    }

    echo '<div class="faq-grid" ' . $wrapper_attrs . '>';

    foreach ($faqs as $faq) {
        ?>
        <div class="faq-item accordion-item">
            <button class="faq-question accordion-header">
                <span><?php echo esc_html($faq['question']); ?></span>
                <span class="faq-icon accordion-icon">+</span>
            </button>
            <div class="faq-answer accordion-body">
                <div class="faq-answer-content accordion-content">
                    <?php echo wp_kses_post($faq['answer']); ?>
                </div>
            </div>
        </div>
        <?php
    }

    echo '</div>';
}
