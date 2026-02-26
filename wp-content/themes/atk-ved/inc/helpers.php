<?php
/**
 * Helper Functions
 *
 * @package ATK_VED
 * @since 3.3.0
 */

declare(strict_types=1);

/**
 * Рендеринг формы захвата (лид-формы)
 *
 * @param string $type    Тип формы (contact, quick-lead, callback)
 * @param string $action  Действие AJAX обработчика
 * @return void
 */
function atk_ved_render_lead_form( string $type = 'contact', string $action = 'atk_ved_contact' ): void {
    $nonce = wp_create_nonce( 'atk_ved_nonce' );
    
    switch ( $type ) {
        case 'quick-lead':
            ?>
            <form class="ajax-form quick-lead-form" data-action="<?php echo esc_attr( $action ); ?>" method="post">
                <input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">
                <input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">
                
                <div class="form-group">
                    <input type="text" name="name" placeholder="<?php esc_attr_e( 'Ваше имя', 'atk-ved' ); ?>" required aria-label="<?php esc_attr_e( 'Ваше имя', 'atk-ved' ); ?>">
                </div>
                
                <div class="form-group">
                    <input type="tel" name="phone" placeholder="<?php esc_attr_e( 'Телефон', 'atk-ved' ); ?>" required aria-label="<?php esc_attr_e( 'Телефон', 'atk-ved' ); ?>">
                </div>
                
                <div class="form-group">
                    <input type="email" name="email" placeholder="<?php esc_attr_e( 'E-mail', 'atk-ved' ); ?>" required aria-label="<?php esc_attr_e( 'E-mail', 'atk-ved' ); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <?php _e( 'Получить консультацию', 'atk-ved' ); ?>
                </button>
                
                <p class="form-notice">
                    <?php _e( 'Нажимая кнопку, вы соглашаетесь с', 'atk-ved' ); ?>
                    <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>" target="_blank">
                        <?php _e( 'политикой конфиденциальности', 'atk-ved' ); ?>
                    </a>
                </p>
            </form>
            <?php
            break;
            
        case 'contact':
        default:
            ?>
            <form class="ajax-form contact-form" data-action="<?php echo esc_attr( $action ); ?>" method="post">
                <input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">
                <input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">
                
                <div class="form-group">
                    <input type="text" name="name" placeholder="<?php esc_attr_e( 'Ваше имя', 'atk-ved' ); ?>" required aria-label="<?php esc_attr_e( 'Ваше имя', 'atk-ved' ); ?>">
                </div>
                
                <div class="form-group">
                    <input type="tel" name="phone" placeholder="<?php esc_attr_e( 'Телефон', 'atk-ved' ); ?>" required aria-label="<?php esc_attr_e( 'Телефон', 'atk-ved' ); ?>">
                </div>
                
                <div class="form-group">
                    <input type="email" name="email" placeholder="<?php esc_attr_e( 'E-mail', 'atk-ved' ); ?>" required aria-label="<?php esc_attr_e( 'E-mail', 'atk-ved' ); ?>">
                </div>
                
                <div class="form-group">
                    <textarea name="message" placeholder="<?php esc_attr_e( 'Сообщение', 'atk-ved' ); ?>" rows="4" aria-label="<?php esc_attr_e( 'Сообщение', 'atk-ved' ); ?>"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <?php _e( 'Отправить заявку', 'atk-ved' ); ?>
                </button>
                
                <p class="form-notice">
                    <?php _e( 'Нажимая кнопку, вы соглашаетесь с', 'atk-ved' ); ?>
                    <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>" target="_blank">
                        <?php _e( 'политикой конфиденциальности', 'atk-ved' ); ?>
                    </a>
                </p>
            </form>
            <?php
            break;
    }
}

/**
 * Вывод SVG иконки
 *
 * @param string $name    Название иконки
 * @param string $class   CSS класс
 * @param int    $width   Ширина
 * @param int    $height  Высота
 * @return string SVG markup
 */
function atk_ved_icon( string $name, string $class = '', int $width = 24, int $height = 24 ): string {
    $icons = [
        // Business icons
        'trophy' => '<path d="M12 2C9 2 7 3 7 5v2H5c-1.1 0-2 .9-2 2v2c0 2.2 1.8 4 4 4h1c.6 2.8 3 5 6 5s5.4-2.2 6-5h1c2.2 0 4-1.8 4-4V7c0-1.1-.9-2-2-2h-2V5c0-2-2-3-5-3zm0 2c1.7 0 3 .3 3 1v2H9V5c0-.7 1.3-1 3-1zM5 9h2v2c0 1.1-.9 2-2 2s-2-.9-2-2V9zm14 2c0 1.1-.9 2-2 2s-2-.9-2-2V9h2v2zm-7 8c-2.2 0-4-1.8-4-4h8c0 2.2-1.8 4-4 4z"/>',
        'truck' => '<path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.7 1.3 3 3 3s3-1.3 3-3h6c0 1.7 1.3 3 3 3s3-1.3 3-3h2v-5l-3-4zM6 18.5c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5 1.5.7 1.5 1.5-.7 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5 1.5.7 1.5 1.5-.7 1.5-1.5 1.5z"/>',
        'star' => '<path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>',
        'phone' => '<path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>',
        'mail' => '<path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>',
        'map-pin' => '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>',
        'whatsapp' => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012 0C5.477 0 .16 5.317.157 11.842c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.524 0 11.835-5.318 11.837-11.843a11.821 11.821 0 00-3.48-8.413Z"/>',
        'telegram' => '<path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>',
        'vk' => '<path d="M15.073 2H4.929C3.311 2 2 3.311 2 4.929v10.143c0 1.618 1.311 2.929 2.929 2.929h10.143c1.618 0 2.929-1.311 2.929-2.929V4.929C18 3.311 16.689 2 15.073 2zm-2.35 14.818c-1.485 0-2.736-.969-3.888-2.121-1.152-1.152-2.121-2.403-2.121-3.888 0-.288.096-.576.288-.768.192-.192.48-.288.768-.288h1.536c.576 0 .864.288 1.056.864.576 1.728 1.536 2.592 2.112 2.592.288 0 .576-.288.576-.864V9.649c0-.576-.288-.96-.768-1.056-.096 0-.192-.096-.288-.096-.288 0-.576.288-.864.864-.384.864-.96 1.248-1.632 1.248-.288 0-.576-.096-.768-.288-.192-.192-.288-.48-.288-.768V8.401c0-.576.096-1.056.384-1.44.576-.864 1.632-1.44 3.072-1.44 1.824 0 3.072.96 3.072 2.592v3.84c0 .576.288.864.672.864.288 0 .576-.288.864-.864.384-.864.96-1.248 1.632-1.248.288 0 .576.096.768.288.192.192.288.48.288.768v1.152c0 .576-.096 1.056-.384 1.44-.576.864-1.632 1.44-3.072 1.44-.576 0-.864-.288-1.056-.864-.48-1.536-1.344-2.4-2.016-2.4-.288 0-.576.288-.576.864v2.688c0 .48.192.768.672.768.288 0 .576-.288.864-.864.384-.864.96-1.248 1.632-1.248.288 0 .576.096.768.288.192.192.288.48.288.768v1.152c0 .576-.096 1.056-.384 1.44-.576.864-1.632 1.44-3.072 1.44z"/>',
        'download' => '<path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>',
        'file-pdf' => '<path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/>',
        'file' => '<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>',
        'close' => '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>',
        'menu' => '<path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>',
        'search' => '<path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 3 13.09 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>',
        'arrow-right' => '<path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>',
        'arrow-left' => '<path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>',
        'check' => '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>',
        'error' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>',
        'info' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>',
        'warning' => '<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>',
    ];

    if ( ! isset( $icons[ $name ] ) ) {
        return '';
    }

    $class_attr = $class ? ' class="' . esc_attr( $class ) . '"' : '';

    return '<svg' . $class_attr . ' width="' . $width . '" height="' . $height . '" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">' . $icons[ $name ] . '</svg>';
}

/**
 * Рендеринг контактной формы
 *
 * @return void
 */
function atk_ved_render_contact_form(): void {
    $nonce = wp_create_nonce( 'atk_ved_nonce' );
    ?>
    <form class="ajax-form contact-form" data-action="atk_ved_contact" method="post">
        <input type="hidden" name="action" value="atk_ved_contact">
        <input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">
        
        <div class="form-group">
            <input type="text" name="name" placeholder="<?php esc_attr_e( 'Ваше имя', 'atk-ved' ); ?>" required aria-label="<?php esc_attr_e( 'Ваше имя', 'atk-ved' ); ?>">
        </div>
        
        <div class="form-group">
            <input type="tel" name="phone" placeholder="<?php esc_attr_e( 'Телефон', 'atk-ved' ); ?>" required aria-label="<?php esc_attr_e( 'Телефон', 'atk-ved' ); ?>">
        </div>
        
        <div class="form-group">
            <input type="email" name="email" placeholder="<?php esc_attr_e( 'E-mail', 'atk-ved' ); ?>" required aria-label="<?php esc_attr_e( 'E-mail', 'atk-ved' ); ?>">
        </div>
        
        <div class="form-group">
            <textarea name="message" placeholder="<?php esc_attr_e( 'Сообщение', 'atk-ved' ); ?>" rows="4" aria-label="<?php esc_attr_e( 'Сообщение', 'atk-ved' ); ?>"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <?php _e( 'Отправить заявку', 'atk-ved' ); ?>
        </button>
        
        <p class="form-notice">
            <?php _e( 'Нажимая кнопку, вы соглашаетесь с', 'atk-ved' ); ?>
            <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>" target="_blank">
                <?php _e( 'политикой конфиденциальности', 'atk-ved' ); ?>
            </a>
        </p>
    </form>
    <?php
}

/**
 * Санитизация номера телефона
 *
 * @param string $phone Номер телефона
 * @return string Очищенный номер
 */
function atk_ved_sanitize_phone(string $phone): string {
    return preg_replace('/[^\d+]/', '', $phone);
}

/**
 * Валидация email
 *
 * @param string $email Email для проверки
 * @return bool true если email валиден
 */
function atk_ved_validate_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Получение инициалов из имени
 *
 * @param string $name Полное имя
 * @return string Инициалы
 */
function atk_ved_get_initials(string $name): string {
    if (empty($name)) {
        return 'А';
    }

    $parts = explode(' ', trim($name));
    $initials = '';

    foreach ($parts as $part) {
        if (!empty($part)) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }
    }

    return substr($initials, 0, 2) ?: 'А';
}

/**
 * Форматирование цены
 *
 * @param float|int $price Цена
 * @param string $currency Символ валюты
 * @return string Отформатированная цена
 */
function atk_ved_format_price($price, string $currency = '₽'): string {
    return number_format((float) $price, 0, '.', ' ') . ' ' . $currency;
}

/**
 * Проверка URL на безопасность
 *
 * @param string $url URL для проверки
 * @return bool true если URL безопасен
 */
function atk_ved_is_safe_url(string $url): bool {
    if (empty($url)) {
        return false;
    }

    $parsed = wp_parse_url($url);

    if (!isset($parsed['host'])) {
        return false;
    }

    $allowed_schemes = ['http', 'https'];
    if (!isset($parsed['scheme']) || !in_array($parsed['scheme'], $allowed_schemes, true)) {
        return false;
    }

    return true;
}

/**
 * Логирование событий безопасности
 *
 * @param string $event   Событие
 * @param string $details Детали
 * @return void
 */
function atk_ved_log_security_event( string $event, string $details = '' ): void {
	$log_file     = WP_CONTENT_DIR . '/security.log';
	$timestamp    = date( 'Y-m-d H:i:s' );
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$ip           = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$user_agent   = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

	$log_entry = sprintf(
		"[%s] IP: %s | Event: %s | Details: %s | User Agent: %s\n",
		$timestamp,
		$ip,
		$event,
		$details,
		$user_agent
	);

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	file_put_contents( $log_file, $log_entry, FILE_APPEND | LOCK_EX );
}

/**
 * Получение данных компании
 *
 * @return array<string, string> Данные компании
 */
function atk_ved_get_company_info(): array {
	return [
		'name'    => get_theme_mod( 'atk_ved_company_name', 'АТК ВЭД' ),
		'phone'   => get_theme_mod( 'atk_ved_phone', '' ),
		'email'   => get_theme_mod( 'atk_ved_email', '' ),
		'address' => get_theme_mod( 'atk_ved_address', '' ),
		'years'   => get_theme_mod( 'atk_ved_years', '5' ),
	];
}

/**
 * Проверка, является ли запрос AJAX
 *
 * @return bool
 */
function atk_ved_is_ajax(): bool {
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	return ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) &&
		strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest';
}

/**
 * Проверка, является ли запрос мобильным
 *
 * @return bool
 */
function atk_ved_is_mobile(): bool {
	return wp_is_mobile();
}

/**
 * Получение MIME типа файла
 *
 * @param string $filename Имя файла
 * @return string MIME тип
 */
function atk_ved_get_mime_type( string $filename ): string {
	$extension = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

	$mime_types = [
		'jpg'  => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'png'  => 'image/png',
		'gif'  => 'image/gif',
		'webp' => 'image/webp',
		'svg'  => 'image/svg+xml',
		'pdf'  => 'application/pdf',
		'doc'  => 'application/msword',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'xls'  => 'application/vnd.ms-excel',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	];

	return $mime_types[ $extension ] ?? 'application/octet-stream';
}

/**
 * Генерация уникального токена
 *
 * @param int $length Длина токена
 * @return string Токен
 * @throws \Exception Если не удалось сгенерировать токен
 */
function atk_ved_generate_token( int $length = 32 ): string {
	return bin2hex( random_bytes( (int) ( $length / 2 ) ) );
}

/**
 * Проверка токена
 *
 * @param string $token    Токен для проверки
 * @param string $expected Ожидаемый токен
 * @return bool true если токены совпадают
 */
function atk_ved_verify_token( string $token, string $expected ): bool {
	return hash_equals( $expected, $token );
}

/**
 * Ограничение длины строки
 *
 * @param string $string Строка
 * @param int    $length Максимальная длина
 * @param string $suffix Суффикс для обрезанной строки
 * @return string Обрезанная строка
 */
function atk_ved_trim_string( string $string, int $length, string $suffix = '...' ): string {
	if ( mb_strlen( $string ) <= $length ) {
		return $string;
	}

	return mb_substr( $string, 0, $length - mb_strlen( $suffix ) ) . $suffix;
}

/**
 * Конвертация размера файла в человекочитаемый формат
 *
 * @param int $bytes Размер в байтах
 * @return string Человекочитаемый размер
 */
function atk_ved_human_file_size( int $bytes ): string {
	$units = [ 'B', 'KB', 'MB', 'GB', 'TB' ];

	$bytes = max( $bytes, 0 );
	$pow   = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
	$pow   = min( $pow, count( $units ) - 1 );

	$bytes /= ( 1 << ( 10 * $pow ) );

	return round( $bytes, 2 ) . ' ' . $units[ $pow ];
}

/**
 * Получение относительного времени
 *
 * @param string|int $datetime Дата и время
 * @return string Относительное время
 */
function atk_ved_relative_time( $datetime ): string {
	$timestamp = is_numeric( $datetime ) ? (int) $datetime : strtotime( $datetime );
	$diff      = time() - $timestamp;

	if ( $diff < 0 ) {
		return 'в будущем';
	}

	if ( $diff < 60 ) {
		return 'только что';
	}

	if ( $diff < 3600 ) {
		$mins = floor( $diff / 60 );
		return $mins . ' мин. назад';
	}

	if ( $diff < 86400 ) {
		$hours = floor( $diff / 3600 );
		return $hours . ' ч. назад';
	}

	if ( $diff < 604800 ) {
		$days = floor( $diff / 86400 );
		return $days . ' дн. назад';
	}

	return date( 'd.m.Y', $timestamp );
}

/**
 * Проверка, является ли строка JSON
 *
 * @param string $string Строка для проверки
 * @return bool true если строка JSON
 */
function atk_ved_is_json( string $string ): bool {
	json_decode( $string );
	return JSON_ERROR_NONE === json_last_error();
}

/**
 * Рекурсивная санитизация массива
 *
 * @param array<string, mixed> $data     Массив для санитизации
 * @param callable             $callback Функция санитизации
 * @return array<string, mixed> Очищенный массив
 */
function atk_ved_sanitize_array( array $data, callable $callback ): array {
	$result = [];

	foreach ( $data as $key => $value ) {
		if ( is_array( $value ) ) {
			$result[ $key ] = atk_ved_sanitize_array( $value, $callback );
		} else {
			$result[ $key ] = $callback( $value );
		}
	}

	return $result;
}

/**
 * Получение домена из URL
 *
 * @param string $url URL
 * @return string Домен
 */
function atk_ved_get_domain( string $url ): string {
	$parsed = wp_parse_url( $url );
	return $parsed['host'] ?? '';
}

/**
 * Проверка, является ли URL внутренним
 *
 * @param string $url URL для проверки
 * @return bool true если URL внутренний
 */
function atk_ved_is_internal_url( string $url ): bool {
	$site_url = home_url();
	return strpos( $url, $site_url ) === 0;
}

/**
 * Генерация случайной строки
 *
 * @param int $length Длина строки
 * @return string Случайная строка
 */
function atk_ved_random_string( int $length = 10 ): string {
	$chars  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$result = '';
	$max    = strlen( $chars ) - 1;

	for ( $i = 0; $i < $length; $i++ ) {
		$result .= $chars[ random_int( 0, $max ) ];
	}

	return $result;
}

/**
 * Конвертация кириллицы в латиницу (транслитерация)
 *
 * @param string $string Строка для транслитерации
 * @return string Транслитерированная строка
 */
function atk_ved_transliterate( string $string ): string {
	$converter = [
		'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',
		'д' => 'd',    'е' => 'e',    'ё' => 'e',    'ж' => 'zh',
		'з' => 'z',    'и' => 'i',    'й' => 'y',    'к' => 'k',
		'л' => 'l',    'м' => 'm',    'н' => 'n',    'о' => 'o',
		'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
		'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',
		'ч' => 'ch',   'ш' => 'sh',   'щ' => 'sch',  'ь' => '',
		'ы' => 'y',    'ъ' => '',     'э' => 'e',    'ю' => 'yu',
		'я' => 'ya',
		'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',
		'Д' => 'D',    'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',
		'З' => 'Z',    'И' => 'I',    'Й' => 'Y',    'К' => 'K',
		'Л' => 'L',    'М' => 'M',    'Н' => 'N',    'О' => 'O',
		'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
		'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'C',
		'Ч' => 'Ch',   'Ш' => 'Sh',   'Щ' => 'Sch',  'Ь' => '',
		'Ы' => 'Y',    'Ъ' => '',     'Э' => 'E',    'Ю' => 'Yu',
		'Я' => 'Ya',
	];

	return strtr( $string, $converter );
}

/**
 * Создание slug (URL-friendly строки)
 *
 * @param string $string Строка
 * @return string Slug
 */
function atk_ved_create_slug( string $string ): string {
	$string = atk_ved_transliterate( $string );
	$string = preg_replace( '/[^a-zA-Z0-9\s-]/', '', $string );
	$string = trim( preg_replace( '/[\s-]+/', '-', $string ) );
	return strtolower( $string );
}
