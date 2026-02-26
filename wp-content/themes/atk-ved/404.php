<?php
/**
 * 404 Error Page
 *
 * @package ATK_VED
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content" class="error-404">
    <div class="container">
        <div class="error-404__content">
            
            <div class="error-404__visual" aria-hidden="true">
                <span class="error-404__number">404</span>
                <!-- SVG иконка "Грустный смайл/Робот" -->
                <svg class="error-404__icon" width="200" height="200" viewBox="0 0 200 200" fill="none">
                    <circle cx="100" cy="100" r="80" stroke="currentColor" stroke-width="4" opacity="0.2"/>
                    <!-- Глаза/Лицо -->
                    <path d="M70 80 L130 80 M70 120 L130 120" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                    <circle cx="75" cy="80" r="8" fill="currentColor"/>
                    <circle cx="125" cy="80" r="8" fill="currentColor"/>
                </svg>
            </div>

            <div class="error-404__text">
                <h1 class="error-404__title">
                    <?php esc_html_e('Страница не найдена', 'atk-ved'); ?>
                </h1>
                <p class="error-404__description">
                    <?php esc_html_e('К сожалению, запрашиваемая страница не существует или была перемещена.', 'atk-ved'); ?>
                </p>
            </div>

            <div class="error-404__actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <?php esc_html_e('На главную', 'atk-ved'); ?>
                </a>
                <!-- Кнопка "Назад" с классом для JS -->
                <button type="button" class="btn btn-outline js-back-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    <?php esc_html_e('Назад', 'atk-ved'); ?>
                </button>
            </div>

            <div class="error-404__search">
                <h2><?php esc_html_e('Попробуйте поиск', 'atk-ved'); ?></h2>
                <?php get_search_form(); ?>
            </div>

            <div class="error-404__links">
                <h3><?php esc_html_e('Полезные ссылки', 'atk-ved'); ?></h3>
                <nav class="error-404__nav" aria-label="<?php esc_attr_e('Полезные ссылки', 'atk-ved'); ?>">
                    <?php 
                    // Выводим меню 'footer_menu' или список страниц, если меню не задано
                    wp_nav_menu([
                        'theme_location' => 'footer_menu', // Используем меню футера или создайте новое '404_menu'
                        'container'      => false,
                        'fallback_cb'    => function() {
                            echo '<ul>';
                            echo '<li><a href="' . esc_url(home_url('/')) . '">' . __('Главная', 'atk-ved') . '</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/services/')) . '">' . __('Услуги', 'atk-ved') . '</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/contacts/')) . '">' . __('Контакты', 'atk-ved') . '</a></li>';
                            echo '</ul>';
                        }
                    ]); 
                    ?>
                </nav>
            </div>

        </div>
    </div>
</main>

<?php
get_footer();