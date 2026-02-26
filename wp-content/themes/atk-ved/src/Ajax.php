<?php
/**
 * AJAX-обработчики.
 *
 * @package ATKVed
 */

declare( strict_types=1 );

namespace ATKVed;

/**
 * Регистрация и обработка AJAX-запросов.
 */
class Ajax {

    /**
     * Инициализация хуков.
     */
    public function init(): void {
        // Newsletter
        add_action( 'wp_ajax_atk_ved_newsletter_subscribe', [ $this, 'handle_newsletter_subscription' ] );
        add_action( 'wp_ajax_nopriv_atk_ved_newsletter_subscribe', [ $this, 'handle_newsletter_subscription' ] );
    }

    /**
     * Обработка подписки на рассылку.
     *
     * @return void
     */
    public function handle_newsletter_subscription(): void {
        check_ajax_referer( 'atk_newsletter_nonce', 'nonce' );

        $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

        if ( ! is_email( $email ) ) {
            wp_send_json_error( [ 'message' => __( 'Введите корректный email.', 'atk-ved' ) ] );
        }

        $subscribers = get_option( 'atk_ved_subscribers', [] );

        if ( in_array( $email, $subscribers, true ) ) {
            wp_send_json_success( [ 'message' => __( 'Вы уже подписаны.', 'atk-ved' ) ] );
        }

        $subscribers[] = $email;
        update_option( 'atk_ved_subscribers', $subscribers, false );

        // TODO: интеграция с Mailchimp / SendPulse / UniSender

        wp_send_json_success( [
            'message' => __( 'Спасибо! Первое письмо придёт в ближайшее время.', 'atk-ved' ),
        ] );
    }
}
