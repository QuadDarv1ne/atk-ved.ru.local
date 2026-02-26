<?php
/**
 * Customizer - настройки темы.
 *
 * @package ATKVed
 */

declare( strict_types=1 );

namespace ATKVed;

use WP_Customize_Manager;

/**
 * Настройки кастомайзера.
 */
class Customizer {

    /**
     * Инициализация хуков.
     */
    public function init(): void {
        add_action( 'customize_register', [ $this, 'register_settings' ] );
    }

    /**
     * Регистрация настроек Customizer.
     *
     * @param WP_Customize_Manager $wp_customize Объект кастомайзера.
     *
     * @return void
     */
    public function register_settings( WP_Customize_Manager $wp_customize ): void {
        // Контакты
        $wp_customize->add_section( 'atk_ved_contacts', [
            'title'    => __( 'Контакты', 'atk-ved' ),
            'priority' => 30,
        ] );

        $this->add_fields( $wp_customize, 'atk_ved_contacts', [
            [ 'id' => 'atk_ved_phone',   'label' => __( 'Телефон', 'atk-ved' ), 'default' => '+7 (XXX) XXX-XX-XX', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ],
            [ 'id' => 'atk_ved_email',   'label' => __( 'Email', 'atk-ved' ), 'default' => 'info@atk-ved.ru', 'sanitize' => 'sanitize_email', 'type' => 'email' ],
            [ 'id' => 'atk_ved_address', 'label' => __( 'Адрес', 'atk-ved' ), 'default' => 'Москва, Россия', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ],
        ] );

        // Соцсети
        $wp_customize->add_section( 'atk_ved_social', [
            'title'    => __( 'Социальные сети', 'atk-ved' ),
            'priority' => 31,
        ] );

        $this->add_fields( $wp_customize, 'atk_ved_social', [
            [ 'id' => 'atk_ved_whatsapp',  'label' => 'WhatsApp',  'default' => '', 'sanitize' => 'esc_url_raw', 'type' => 'url' ],
            [ 'id' => 'atk_ved_telegram',  'label' => 'Telegram',  'default' => '', 'sanitize' => 'esc_url_raw', 'type' => 'url' ],
            [ 'id' => 'atk_ved_vk',        'label' => 'VK', 'default' => '', 'sanitize' => 'esc_url_raw', 'type' => 'url' ],
            [ 'id' => 'atk_ved_instagram', 'label' => 'Instagram', 'default' => '', 'sanitize' => 'esc_url_raw', 'type' => 'url' ],
            [ 'id' => 'atk_ved_youtube',   'label' => 'YouTube',   'default' => '', 'sanitize' => 'esc_url_raw', 'type' => 'url' ],
        ] );

        // Главный экран
        $wp_customize->add_section( 'atk_ved_hero', [
            'title'    => __( 'Главный экран', 'atk-ved' ),
            'priority' => 32,
        ] );

        $this->add_fields( $wp_customize, 'atk_ved_hero', [
            [ 'id' => 'atk_ved_hero_title',   'label' => __( 'Заголовок', 'atk-ved' ), 'default' => 'ТОВАРЫ ДЛЯ МАРКЕТПЛЕЙСОВ ИЗ КИТАЯ ОПТОМ', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ],
            [ 'id' => 'atk_ved_stat1_number', 'label' => __( 'Статистика 1 — число', 'atk-ved' ), 'default' => '500+', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ],
            [ 'id' => 'atk_ved_stat1_label',  'label' => __( 'Статистика 1 — подпись', 'atk-ved' ), 'default' => 'КЛИЕНТОВ', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ],
        ] );

        // Аналитика
        $wp_customize->add_section( 'atk_ved_analytics', [
            'title'    => __( 'Аналитика', 'atk-ved' ),
            'priority' => 40,
        ] );

        $this->add_fields( $wp_customize, 'atk_ved_analytics', [
            [ 'id' => 'atk_ved_metrika_id', 'label' => __( 'Яндекс.Метрика ID', 'atk-ved' ), 'default' => '', 'sanitize' => 'absint', 'type' => 'text' ],
            [ 'id' => 'atk_ved_ga_id',      'label' => __( 'Google Analytics ID', 'atk-ved' ), 'default' => '', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ],
        ] );
    }

    /**
     * Добавление полей в секцию.
     *
     * @param WP_Customize_Manager $mgr     Менеджер кастомайзера.
     * @param string               $section Секция.
     * @param array                $fields  Поля.
     *
     * @return void
     */
    private function add_fields( WP_Customize_Manager $mgr, string $section, array $fields ): void {
        foreach ( $fields as $f ) {
            $mgr->add_setting( $f['id'], [
                'default'           => $f['default'],
                'sanitize_callback' => $f['sanitize'],
                'transport'         => 'refresh',
            ] );

            $mgr->add_control( $f['id'], [
                'label'   => $f['label'],
                'section' => $section,
                'type'    => $f['type'],
            ] );
        }
    }
}
