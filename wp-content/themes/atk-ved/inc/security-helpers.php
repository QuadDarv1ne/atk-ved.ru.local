<?php
/**
 * Security Helper Functions
 * 
 * @package ATK_VED
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Получить валидированный IP адрес клиента
 * 
 * @return string Валидированный IP или '0.0.0.0' если невалидный
 */
function atk_ved_get_client_ip(): string {
    $ip = filter_var(
        $_SERVER['REMOTE_ADDR'] ?? '',
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    );
    
    return $ip ?: '0.0.0.0';
}

/**
 * Получить валидированный REQUEST_URI
 * 
 * @return string Валидированный URI
 */
function atk_ved_get_request_uri(): string {
    return filter_var(
        $_SERVER['REQUEST_URI'] ?? '',
        FILTER_SANITIZE_URL
    );
}

/**
 * Получить валидированный HTTP_REFERER
 * 
 * @return string Валидированный referer
 */
function atk_ved_get_referer(): string {
    return filter_var(
        $_SERVER['HTTP_REFERER'] ?? '',
        FILTER_SANITIZE_URL
    );
}

/**
 * Получить валидированный HTTP_HOST
 * 
 * @return string Валидированный host
 */
function atk_ved_get_http_host(): string {
    return filter_var(
        $_SERVER['HTTP_HOST'] ?? '',
        FILTER_SANITIZE_URL
    );
}

/**
 * Получить REQUEST_METHOD
 * 
 * @return string Request method (GET, POST, etc.)
 */
function atk_ved_get_request_method(): string {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    return in_array($method, ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], true) 
        ? $method 
        : 'GET';
}
