<?php
/**
 * Helper Class - Фасад для вспомогательных функций
 *
 * @package ATK_VED
 * @since 3.3.0
 */

declare(strict_types=1);

namespace ATKVed;

/**
 * Класс Helper - статический фасад для вспомогательных функций
 *
 * Предоставляет объектно-ориентированный интерфейс к helper функциям.
 * Все методы статические для удобства использования.
 *
 * @example
 * ```php
 * use ATKVed\Helper;
 *
 * // Форматирование цены
 * echo Helper::formatPrice(15000); // '15 000 ₽'
 *
 * // Валидация
 * if (Helper::validateEmail($email)) { ... }
 *
 * // Цепочки вызовов
 * $slug = Helper::createSlug($name);
 * ```
 */
class Helper {

    /**
     * Санитизация номера телефона
     *
     * @param string $phone Номер телефона
     * @return string Очищенный номер
     */
    public static function sanitizePhone(string $phone): string {
        return preg_replace('/[^\d+]/', '', $phone);
    }

    /**
     * Валидация email
     *
     * @param string $email Email для проверки
     * @return bool true если email валиден
     */
    public static function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Получение инициалов из имени
     *
     * @param string $name Полное имя
     * @return string Инициалы
     */
    public static function getInitials(string $name): string {
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
    public static function formatPrice($price, string $currency = '₽'): string {
        return number_format((float) $price, 0, '.', ' ') . ' ' . $currency;
    }

    /**
     * Проверка URL на безопасность
     *
     * @param string $url URL для проверки
     * @return bool true если URL безопасен
     */
    public static function isSafeUrl(string $url): bool {
        if (empty($url)) {
            return false;
        }

        $parsed = wp_parse_url($url);

        if (!isset($parsed['host'])) {
            return false;
        }

        $allowed_schemes = ['http', 'https'];
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        return !isset($parsed['scheme']) || in_array($parsed['scheme'], $allowed_schemes, true);
    }

    /**
     * Логирование событий безопасности
     *
     * @param string $event Событие
     * @param string $details Детали
     * @return void
     */
    public static function logSecurityEvent(string $event, string $details = ''): void {
        $log_file = WP_CONTENT_DIR . '/security.log';
        $timestamp = date('Y-m-d H:i:s');
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        $log_entry = sprintf(
            "[%s] IP: %s | Event: %s | Details: %s | User Agent: %s\n",
            $timestamp,
            $ip,
            $event,
            $details,
            $user_agent
        );

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Получение данных компании
     *
     * @return array<string, string> Данные компании
     */
    public static function getCompanyInfo(): array {
        return [
            'name'    => get_theme_mod('atk_ved_company_name', 'АТК ВЭД'),
            'phone'   => get_theme_mod('atk_ved_phone', ''),
            'email'   => get_theme_mod('atk_ved_email', ''),
            'address' => get_theme_mod('atk_ved_address', ''),
            'years'   => get_theme_mod('atk_ved_years', '5'),
        ];
    }

    /**
     * Проверка, является ли запрос AJAX
     *
     * @return bool
     */
    public static function isAjax(): bool {
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Проверка, является ли запрос мобильным
     *
     * @return bool
     */
    public static function isMobile(): bool {
        return wp_is_mobile();
    }

    /**
     * Получение MIME типа файла
     *
     * @param string $filename Имя файла
     * @return string MIME тип
     */
    public static function getMimeType(string $filename): string {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $mime_types = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return $mime_types[$extension] ?? 'application/octet-stream';
    }

    /**
     * Генерация уникального токена
     *
     * @param int $length Длина токена
     * @return string Токен
     * @throws \Exception Если не удалось сгенерировать токен
     */
    public static function generateToken(int $length = 32): string {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Проверка токена
     *
     * @param string $token Токен для проверки
     * @param string $expected Ожидаемый токен
     * @return bool true если токены совпадают
     */
    public static function verifyToken(string $token, string $expected): bool {
        return hash_equals($expected, $token);
    }

    /**
     * Ограничение длины строки
     *
     * @param string $string Строка
     * @param int $length Максимальная длина
     * @param string $suffix Суффикс для обрезанной строки
     * @return string Обрезанная строка
     */
    public static function trimString(string $string, int $length, string $suffix = '...'): string {
        if (mb_strlen($string) <= $length) {
            return $string;
        }

        return mb_substr($string, 0, $length - mb_strlen($suffix)) . $suffix;
    }

    /**
     * Конвертация размера файла в человекочитаемый формат
     *
     * @param int $bytes Размер в байтах
     * @return string Человекочитаемый размер
     */
    public static function humanFileSize(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Получение относительного времени
     *
     * @param string|int $datetime Дата и время
     * @return string Относительное время
     */
    public static function relativeTime($datetime): string {
        $timestamp = is_numeric($datetime) ? (int) $datetime : strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 0) {
            return 'в будущем';
        }

        if ($diff < 60) {
            return 'только что';
        }

        if ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' мин. назад';
        }

        if ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' ч. назад';
        }

        if ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' дн. назад';
        }

        return date('d.m.Y', $timestamp);
    }

    /**
     * Проверка, является ли строка JSON
     *
     * @param string $string Строка для проверки
     * @return bool true если строка JSON
     */
    public static function isJson(string $string): bool {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Рекурсивная санитизация массива
     *
     * @param array<string, mixed> $data Массив для санитизации
     * @param callable $callback Функция санитизации
     * @return array<string, mixed> Очищенный массив
     */
    public static function sanitizeArray(array $data, callable $callback): array {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::sanitizeArray($value, $callback);
            } else {
                $result[$key] = $callback($value);
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
    public static function getDomain(string $url): string {
        $parsed = wp_parse_url($url);
        return $parsed['host'] ?? '';
    }

    /**
     * Проверка, является ли URL внутренним
     *
     * @param string $url URL для проверки
     * @return bool true если URL внутренний
     */
    public static function isInternalUrl(string $url): bool {
        $site_url = home_url();
        return strpos($url, $site_url) === 0;
    }

    /**
     * Генерация случайной строки
     *
     * @param int $length Длина строки
     * @return string Случайная строка
     */
    public static function randomString(int $length = 10): string {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $result = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $max)];
        }

        return $result;
    }

    /**
     * Конвертация кириллицы в латиницу (транслитерация)
     *
     * @param string $string Строка для транслитерации
     * @return string Транслитерированная строка
     */
    public static function transliterate(string $string): string {
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

        return strtr($string, $converter);
    }

    /**
     * Создание slug (URL-friendly строки)
     *
     * @param string $string Строка
     * @return string Slug
     */
    public static function createSlug(string $string): string {
        $string = self::transliterate($string);
        $string = preg_replace('/[^a-zA-Z0-9\s-]/', '', $string);
        $string = trim(preg_replace('/[\s-]+/', '-', $string));
        return strtolower($string);
    }

    /**
     * Проверка заполненности поля
     *
     * @param mixed $value Значение для проверки
     * @return bool true если поле заполнено
     */
    public static function isFilled($value): bool {
        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (is_array($value)) {
            return !empty($value);
        }

        return $value !== null && $value !== false;
    }

    /**
     * Безопасное получение значения из массива
     *
     * @param array<string, mixed> $array Массив
     * @param string $key Ключ
     * @param mixed $default Значение по умолчанию
     * @return mixed Значение или default
     */
    public static function get(array $array, string $key, $default = null) {
        return $array[$key] ?? $default;
    }

    /**
     * Форматирование даты
     *
     * @param string|int $date Дата
     * @param string $format Формат
     * @return string Отформатированная дата
     */
    public static function formatDate($date, string $format = 'd.m.Y'): string {
        $timestamp = is_numeric($date) ? (int) $date : strtotime($date);
        return date($format, $timestamp);
    }

    /**
     * Получение первого изображения из контента
     *
     * @param string $content Контент поста
     * @return string|null URL изображения или null
     */
    public static function getFirstImageFromContent(string $content): ?string {
        if (preg_match('/<img[^>]+src="([^"]+)"/', $content, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Проверка, является ли устройство планшетом
     *
     * @return bool
     */
    public static function isTablet(): bool {
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        return stripos($user_agent, 'tablet') !== false ||
               stripos($user_agent, 'ipad') !== false ||
               stripos($user_agent, 'android') !== false;
    }

    /**
     * Очистка HTML от опасных тегов
     *
     * @param string $html HTML для очистки
     * @return string Очищенный HTML
     */
    public static function sanitizeHtml(string $html): string {
        return wp_kses_post($html);
    }

    /**
     * Создание excerpt (краткого содержания)
     *
     * @param string $text Текст
     * @param int $length Максимальная длина
     * @return string Excerpt
     */
    public static function createExcerpt(string $text, int $length = 150): string {
        $text = wp_strip_all_tags($text);
        return self::trimString($text, $length, '...');
    }
}
