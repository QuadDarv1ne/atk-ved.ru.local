<?php
/**
 * Миграция хардкода в Custom Post Types
 *
 * @package ATK_VED
 * @since 3.6.0
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Миграция услуг из хардкода в CPT
 */
function atk_ved_migrate_services(): array {
    $services = [
        [
            'title' => 'Поиск поставщиков и товаров',
            'content' => 'Подбираем надежных производителей и качественные товары под ваши требования. Проверяем репутацию фабрик, проводим переговоры на китайском языке.',
            'icon' => '🔍',
            'order' => 1,
        ],
        [
            'title' => 'Контроль качества товара',
            'content' => 'Проверяем качество продукции перед отправкой на всех этапах производства. Делаем фото и видео отчеты, проводим замеры и тестирование.',
            'icon' => '✓',
            'order' => 2,
        ],
        [
            'title' => 'Доставка грузов из Китая',
            'content' => 'Организуем быструю и надежную доставку любым удобным способом: авиа, море, ЖД, авто. Отслеживание груза в режиме реального времени.',
            'icon' => '🚢',
            'order' => 3,
        ],
        [
            'title' => 'Таможенное оформление',
            'content' => 'Берем на себя все вопросы таможенного оформления и сертификации. Подготовка документов, декларирование, уплата пошлин.',
            'icon' => '📋',
            'order' => 4,
        ],
        [
            'title' => 'Складские услуги',
            'content' => 'Предоставляем складские помещения для хранения и консолидации грузов. Бесплатное хранение 7 дней, переупаковка, маркировка.',
            'icon' => '🏭',
            'order' => 5,
        ],
        [
            'title' => 'Выкуп и оплата товаров',
            'content' => 'Выкупаем товары у поставщиков и обеспечиваем безопасные расчеты. Работаем с любыми способами оплаты.',
            'icon' => '💰',
            'order' => 6,
        ],
    ];

    $created = [];

    foreach ($services as $service) {
        // Проверяем, не существует ли уже
        $existing = get_posts([
            'post_type' => 'service',
            'title' => $service['title'],
            'posts_per_page' => 1,
        ]);

        if (!empty($existing)) {
            $created[] = [
                'id' => $existing[0]->ID,
                'title' => $service['title'],
                'status' => 'exists',
            ];
            continue;
        }

        $post_id = wp_insert_post([
            'post_type' => 'service',
            'post_title' => $service['title'],
            'post_content' => $service['content'],
            'post_status' => 'publish',
            'menu_order' => $service['order'],
        ]);

        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, '_service_icon', $service['icon']);
            
            $created[] = [
                'id' => $post_id,
                'title' => $service['title'],
                'status' => 'created',
            ];
        }
    }

    return $created;
}

/**
 * Миграция этапов работы в CPT
 */
function atk_ved_migrate_process_steps(): array {
    $steps = [
        [
            'title' => 'Заявка и консультация',
            'content' => 'Оставьте заявку на сайте или свяжитесь с нами удобным способом. Наш менеджер проконсультирует вас по всем вопросам.',
            'icon' => '📝',
            'number' => '01',
            'order' => 1,
        ],
        [
            'title' => 'Поиск поставщиков',
            'content' => 'Находим надежных производителей и лучшие предложения. Проверяем репутацию и качество продукции.',
            'icon' => '🔍',
            'number' => '02',
            'order' => 2,
        ],
        [
            'title' => 'Расчет стоимости',
            'content' => 'Рассчитываем полную стоимость с учетом всех расходов: товар, доставка, таможня, сертификация.',
            'icon' => '💰',
            'number' => '03',
            'order' => 3,
        ],
        [
            'title' => 'Заключение договора',
            'content' => 'Подписываем договор и согласовываем условия поставки. Прозрачные условия и гарантии.',
            'icon' => '📋',
            'number' => '04',
            'order' => 4,
        ],
        [
            'title' => 'Контроль качества',
            'content' => 'Проверяем товар перед отправкой и делаем фотоотчет. Гарантируем соответствие заявленным характеристикам.',
            'icon' => '✓',
            'number' => '05',
            'order' => 5,
        ],
        [
            'title' => 'Доставка и получение',
            'content' => 'Доставляем груз и помогаем с таможенным оформлением. Отслеживание на всех этапах.',
            'icon' => '🚢',
            'number' => '06',
            'order' => 6,
        ],
    ];

    // Регистрируем CPT если еще не зарегистрирован
    if (!post_type_exists('process_step')) {
        register_post_type('process_step', [
            'labels' => [
                'name' => 'Этапы работы',
                'singular_name' => 'Этап',
            ],
            'public' => false,
            'show_ui' => true,
            'supports' => ['title', 'editor', 'page-attributes'],
            'menu_icon' => 'dashicons-list-view',
        ]);
    }

    $created = [];

    foreach ($steps as $step) {
        $existing = get_posts([
            'post_type' => 'process_step',
            'title' => $step['title'],
            'posts_per_page' => 1,
        ]);

        if (!empty($existing)) {
            $created[] = [
                'id' => $existing[0]->ID,
                'title' => $step['title'],
                'status' => 'exists',
            ];
            continue;
        }

        $post_id = wp_insert_post([
            'post_type' => 'process_step',
            'post_title' => $step['title'],
            'post_content' => $step['content'],
            'post_status' => 'publish',
            'menu_order' => $step['order'],
        ]);

        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, '_step_icon', $step['icon']);
            update_post_meta($post_id, '_step_number', $step['number']);
            
            $created[] = [
                'id' => $post_id,
                'title' => $step['title'],
                'status' => 'created',
            ];
        }
    }

    return $created;
}

/**
 * Миграция FAQ в CPT
 */
function atk_ved_migrate_faq(): array {
    $faqs = [
        [
            'question' => 'Какой минимальный объем заказа?',
            'answer' => 'Минимального объема нет - работаем с любыми партиями от 1 кг. Однако для оптимизации затрат рекомендуем заказы от 50 кг.',
            'order' => 1,
        ],
        [
            'question' => 'Сколько времени занимает доставка?',
            'answer' => 'Сроки зависят от способа доставки: авиа 7-10 дней, ЖД 20-25 дней, море 35-45 дней от склада в Китае до склада в России.',
            'order' => 2,
        ],
        [
            'question' => 'Как рассчитывается стоимость доставки?',
            'answer' => 'Стоимость зависит от веса, объема и способа доставки. Авиа от $5/кг, ЖД от $3/кг, море от $1.5/кг. Делаем точный расчет индивидуально.',
            'order' => 3,
        ],
        [
            'question' => 'Помогаете ли вы с таможенным оформлением?',
            'answer' => 'Да, мы берем на себя все вопросы таможенного оформления: декларирование, сертификация, уплата пошлин и сборов.',
            'order' => 4,
        ],
        [
            'question' => 'Что делать, если товар пришел с браком?',
            'answer' => 'Мы проверяем качество перед отправкой. Если брак обнаружен при получении - возвращаем деньги или меняем товар согласно договору.',
            'order' => 5,
        ],
        [
            'question' => 'Нужно ли открывать ИП для заказа?',
            'answer' => 'Для коммерческих поставок требуется ИП или ООО. Для личных покупок до 1000 евро в месяц регистрация не нужна.',
            'order' => 6,
        ],
    ];

    $created = [];

    foreach ($faqs as $faq) {
        $existing = get_posts([
            'post_type' => 'faq',
            'title' => $faq['question'],
            'posts_per_page' => 1,
        ]);

        if (!empty($existing)) {
            $created[] = [
                'id' => $existing[0]->ID,
                'title' => $faq['question'],
                'status' => 'exists',
            ];
            continue;
        }

        $post_id = wp_insert_post([
            'post_type' => 'faq',
            'post_title' => $faq['question'],
            'post_content' => $faq['answer'],
            'post_status' => 'publish',
            'menu_order' => $faq['order'],
        ]);

        if (!is_wp_error($post_id)) {
            $created[] = [
                'id' => $post_id,
                'title' => $faq['question'],
                'status' => 'created',
            ];
        }
    }

    return $created;
}

/**
 * Запуск всех миграций
 */
function atk_ved_run_all_migrations(): array {
    return [
        'services' => atk_ved_migrate_services(),
        'process_steps' => atk_ved_migrate_process_steps(),
        'faq' => atk_ved_migrate_faq(),
    ];
}

/**
 * Добавляем страницу миграции в админку
 */
function atk_ved_add_migration_page(): void {
    add_submenu_page(
        'tools.php',
        'Миграция данных',
        'Миграция данных',
        'manage_options',
        'atk-migration',
        'atk_ved_migration_page_html'
    );
}
add_action('admin_menu', 'atk_ved_add_migration_page');

/**
 * HTML страницы миграции
 */
function atk_ved_migration_page_html(): void {
    if (!current_user_can('manage_options')) {
        return;
    }

    $results = null;
    if (isset($_POST['run_migration']) && check_admin_referer('atk_migration')) {
        $results = atk_ved_run_all_migrations();
    }

    ?>
    <div class="wrap">
        <h1>Миграция данных в Custom Post Types</h1>
        
        <div class="card">
            <h2>О миграции</h2>
            <p>Эта утилита перенесет хардкод данные из шаблонов в Custom Post Types:</p>
            <ul>
                <li><strong>Услуги</strong> → CPT "service"</li>
                <li><strong>Этапы работы</strong> → CPT "process_step"</li>
                <li><strong>FAQ</strong> → CPT "faq"</li>
            </ul>
            <p><strong>Важно:</strong> Миграция безопасна и не удаляет существующие данные. Если записи уже существуют, они будут пропущены.</p>
        </div>

        <?php if ($results): ?>
        <div class="notice notice-success">
            <h3>✅ Миграция завершена!</h3>
            
            <h4>Услуги (<?php echo count($results['services']); ?>):</h4>
            <ul>
                <?php foreach ($results['services'] as $item): ?>
                <li>
                    <?php echo esc_html($item['title']); ?> 
                    <span class="badge"><?php echo $item['status'] === 'created' ? '✨ Создано' : '✓ Существует'; ?></span>
                </li>
                <?php endforeach; ?>
            </ul>

            <h4>Этапы работы (<?php echo count($results['process_steps']); ?>):</h4>
            <ul>
                <?php foreach ($results['process_steps'] as $item): ?>
                <li>
                    <?php echo esc_html($item['title']); ?> 
                    <span class="badge"><?php echo $item['status'] === 'created' ? '✨ Создано' : '✓ Существует'; ?></span>
                </li>
                <?php endforeach; ?>
            </ul>

            <h4>FAQ (<?php echo count($results['faq']); ?>):</h4>
            <ul>
                <?php foreach ($results['faq'] as $item): ?>
                <li>
                    <?php echo esc_html($item['title']); ?> 
                    <span class="badge"><?php echo $item['status'] === 'created' ? '✨ Создано' : '✓ Существует'; ?></span>
                </li>
                <?php endforeach; ?>
            </ul>

            <p><strong>Следующий шаг:</strong> Обновите шаблоны для использования данных из CPT.</p>
        </div>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field('atk_migration'); ?>
            <p>
                <button type="submit" name="run_migration" class="button button-primary button-hero">
                    🚀 Запустить миграцию
                </button>
            </p>
        </form>

        <style>
            .badge {
                display: inline-block;
                padding: 2px 8px;
                background: #46b450;
                color: white;
                border-radius: 3px;
                font-size: 12px;
                margin-left: 10px;
            }
        </style>
    </div>
    <?php
}

/**
 * Регистрация CPT для этапов работы
 */
function atk_ved_register_process_steps_cpt(): void {
    register_post_type('process_step', [
        'labels' => [
            'name' => 'Этапы работы',
            'singular_name' => 'Этап',
            'add_new' => 'Добавить этап',
            'add_new_item' => 'Добавить новый этап',
            'edit_item' => 'Редактировать этап',
            'new_item' => 'Новый этап',
            'view_item' => 'Посмотреть этап',
            'search_items' => 'Найти этап',
            'not_found' => 'Этапы не найдены',
        ],
        'public' => false,
        'show_ui' => true,
        'supports' => ['title', 'editor', 'page-attributes'],
        'menu_icon' => 'dashicons-list-view',
        'menu_position' => 8,
        'show_in_rest' => true,
    ]);
}
add_action('init', 'atk_ved_register_process_steps_cpt');

/**
 * Добавление мета-полей для этапов
 */
function atk_ved_add_process_step_meta_boxes(): void {
    add_meta_box(
        'process_step_details',
        'Детали этапа',
        'atk_ved_process_step_meta_box_callback',
        'process_step',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'atk_ved_add_process_step_meta_boxes');

/**
 * Callback для мета-бокса этапов
 */
function atk_ved_process_step_meta_box_callback($post): void {
    wp_nonce_field('atk_ved_save_process_step_meta', 'atk_ved_process_step_meta_nonce');
    
    $icon = get_post_meta($post->ID, '_step_icon', true);
    $number = get_post_meta($post->ID, '_step_number', true);
    ?>
    <p>
        <label for="step_icon"><strong>Иконка (emoji):</strong></label><br>
        <input type="text" id="step_icon" name="step_icon" value="<?php echo esc_attr($icon); ?>" style="width: 100%;" placeholder="📝">
    </p>
    <p>
        <label for="step_number"><strong>Номер:</strong></label><br>
        <input type="text" id="step_number" name="step_number" value="<?php echo esc_attr($number); ?>" style="width: 100%;" placeholder="01">
    </p>
    <?php
}

/**
 * Сохранение мета-полей этапов
 */
function atk_ved_save_process_step_meta($post_id): void {
    if (!isset($_POST['atk_ved_process_step_meta_nonce']) || 
        !wp_verify_nonce($_POST['atk_ved_process_step_meta_nonce'], 'atk_ved_save_process_step_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['step_icon'])) {
        update_post_meta($post_id, '_step_icon', sanitize_text_field($_POST['step_icon']));
    }

    if (isset($_POST['step_number'])) {
        update_post_meta($post_id, '_step_number', sanitize_text_field($_POST['step_number']));
    }
}
add_action('save_post', 'atk_ved_save_process_step_meta');

/**
 * Добавление мета-полей для услуг
 */
function atk_ved_add_service_meta_boxes(): void {
    add_meta_box(
        'service_details',
        'Детали услуги',
        'atk_ved_service_meta_box_callback',
        'service',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'atk_ved_add_service_meta_boxes');

/**
 * Callback для мета-бокса услуг
 */
function atk_ved_service_meta_box_callback($post): void {
    wp_nonce_field('atk_ved_save_service_meta', 'atk_ved_service_meta_nonce');
    
    $icon = get_post_meta($post->ID, '_service_icon', true);
    ?>
    <p>
        <label for="service_icon"><strong>Иконка (emoji):</strong></label><br>
        <input type="text" id="service_icon" name="service_icon" value="<?php echo esc_attr($icon); ?>" style="width: 100%;" placeholder="🔍">
    </p>
    <?php
}

/**
 * Сохранение мета-полей услуг
 */
function atk_ved_save_service_meta($post_id): void {
    if (!isset($_POST['atk_ved_service_meta_nonce']) || 
        !wp_verify_nonce($_POST['atk_ved_service_meta_nonce'], 'atk_ved_save_service_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['service_icon'])) {
        update_post_meta($post_id, '_service_icon', sanitize_text_field($_POST['service_icon']));
    }
}
add_action('save_post', 'atk_ved_save_service_meta');
