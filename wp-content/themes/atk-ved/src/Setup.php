<?php
/**
 * Настройки темы.
 *
 * @package ATKVed
 */

declare( strict_types=1 );

namespace ATKVed;

/**
 * Регистрация поддержки темы, меню, размеров изображений.
 */
class Setup {

    /**
     * Инициализация хуков.
     */
    public function init(): void {
        add_action( 'after_setup_theme', [ $this, 'setup_theme' ] );
        add_action( 'widgets_init', [ $this, 'register_sidebars' ] );
    }

    /**
     * Настройка поддержки темы.
     *
     * @return void
     */
    public function setup_theme(): void {
        // Основные возможности
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'custom-logo' );
        add_theme_support( 'responsive-embeds' );
        add_theme_support( 'editor-styles' );
        add_theme_support( 'wp-block-styles' );
        add_theme_support( 'align-wide' );

        // HTML5
        add_theme_support( 'html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ] );

        // Регистрация меню
        register_nav_menus( [
            'primary'         => __( 'Главное меню', 'atk-ved' ),
            'footer-services' => __( 'Футер — Услуги', 'atk-ved' ),
            'footer-company'  => __( 'Футер — Компания', 'atk-ved' ),
        ] );

        // Размеры изображений
        add_image_size( 'atk-ved-hero',    800, 600, true );
        add_image_size( 'atk-ved-service', 400, 300, true );
    }

    /**
     * Регистрация сайдбаров.
     *
     * @return void
     */
    public function register_sidebars(): void {
        register_sidebar( [
            'name'          => __( 'Сайдбар', 'atk-ved' ),
            'id'            => 'sidebar-1',
            'description'   => __( 'Добавьте виджеты сюда', 'atk-ved' ),
            'before_widget' => '<div class="widget">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ] );
    }
}
