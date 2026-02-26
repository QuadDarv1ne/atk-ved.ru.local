<?php
/**
 * Helper Functions
 *
 * @package ATK_VED
 * @since 3.3.0
 */

declare(strict_types=1);

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
