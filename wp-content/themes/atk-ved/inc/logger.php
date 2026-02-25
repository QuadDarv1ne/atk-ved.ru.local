<?php
/**
 * Система логирования ошибок и событий
 * 
 * @package ATK_VED
 * @since 1.8.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для логирования событий
 */
class ATK_VED_Logger {
    
    private string $log_dir;
    private string $log_file;
    private int $max_file_size = 10485760; // 10 MB
    private int $max_files = 5;
    
    public function __construct() {
        $this->log_dir = WP_CONTENT_DIR . '/logs';
        $this->log_file = $this->log_dir . '/atk-ved-' . date('Y-m-d') . '.log';
        
        // Создаём директорию для логов если не существует
        if (!file_exists($this->log_dir)) {
            wp_mkdir_p($this->log_dir);
            // Запрещаем прямой доступ к директории логов
            file_put_contents($this->log_dir . '/index.php', '<?php // Silence is golden');
            file_put_contents($this->log_dir . '/.htaccess', 'deny from all');
        }
        
        // Регистрируем обработчики ошибок
        $this->register_error_handlers();
    }
    
    /**
     * Регистрация обработчиков ошибок
     */
    private function register_error_handlers(): void {
        // Обработчик PHP ошибок
        set_error_handler([$this, 'handle_php_error']);
        
        // Обработчик фатальных ошибок
        register_shutdown_function([$this, 'handle_fatal_error']);
        
        // Обработчик исключений
        set_exception_handler([$this, 'handle_exception']);
    }
    
    /**
     * Логирование сообщения
     * 
     * @param string $message Сообщение
     * @param string $level Уровень (info, warning, error, critical)
     * @param array $context Дополнительный контекст
     * @return bool
     */
    public function log(string $message, string $level = 'info', array $context = []): bool {
        // Не логируем в режиме production без WP_DEBUG
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            if ($level === 'info') {
                return false;
            }
        }
        
        // Проверяем размер файла и ротируем если нужно
        $this->rotate_logs();
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $this->get_client_ip();
        $user_id = get_current_user_id();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $request_uri = $_SERVER['REQUEST_URI'] ?? 'Unknown';
        
        $log_entry = sprintf(
            "[%s] [%s] [IP:%s] [User:%s] %s | Context: %s | UA: %s | URI: %s\n",
            $timestamp,
            strtoupper($level),
            $ip,
            $user_id ?: 'Guest',
            $message,
            json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $user_agent,
            $request_uri
        );
        
        return file_put_contents($this->log_file, $log_entry, FILE_APPEND | LOCK_EX) !== false;
    }
    
    /**
     * Обработчик PHP ошибок
     */
    public function handle_php_error(int $errno, string $errstr, string $errfile, int $errline): bool {
        $error_types = [
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        ];
        
        $error_type = $error_types[$errno] ?? 'Unknown Error';
        
        $this->log(
            "PHP {$error_type}: {$errstr} in {$errfile} on line {$errline}",
            'error',
            [
                'errno' => $errno,
                'file' => $errfile,
                'line' => $errline
            ]
        );
        
        // Не подавляем ошибку если это не notice/deprecated
        return !in_array($errno, [E_NOTICE, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED]);
    }
    
    /**
     * Обработчик фатальных ошибок
     */
    public function handle_fatal_error(): void {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $this->log(
                "Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}",
                'critical',
                [
                    'type' => $error['type'],
                    'file' => $error['file'],
                    'line' => $error['line']
                ]
            );
        }
    }
    
    /**
     * Обработчик исключений
     */
    public function handle_exception(Throwable $exception): void {
        $this->log(
            "Uncaught Exception: {$exception->getMessage()}",
            'critical',
            [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]
        );
        
        // Показываем стандартную страницу ошибки
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            http_response_code(500);
            echo '<h1>Произошла ошибка на сайте</h1>';
            echo '<p>Пожалуйста, попробуйте позже или свяжитесь с администрацией.</p>';
        }
    }
    
    /**
     * Логирование действий пользователя
     */
    public function log_user_action(string $action, array $data = []): bool {
        return $this->log(
            "User Action: {$action}",
            'info',
            array_merge([
                'user_id' => get_current_user_id(),
                'user_login' => wp_get_current_user()->user_login ?? 'Guest'
            ], $data)
        );
    }
    
    /**
     * Логирование событий безопасности
     */
    public function log_security_event(string $event, array $data = []): bool {
        return $this->log(
            "Security Event: {$event}",
            'warning',
            array_merge([
                'ip' => $this->get_client_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ], $data)
        );
    }
    
    /**
     * Ротация логов
     */
    private function rotate_logs(): void {
        if (!file_exists($this->log_file)) {
            return;
        }
        
        $file_size = filesize($this->log_file);
        
        if ($file_size >= $this->max_file_size) {
            // Переименовываем текущий файл
            $rotated_file = $this->log_dir . '/atk-ved-' . date('Y-m-d-His') . '.log';
            rename($this->log_file, $rotated_file);
            
            // Удаляем старые файлы
            $this->cleanup_old_logs();
        }
    }
    
    /**
     * Очистка старых логов
     */
    private function cleanup_old_logs(): void {
        $files = glob($this->log_dir . '/atk-ved-*.log');
        
        if (count($files) > $this->max_files) {
            usort($files, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            
            for ($i = $this->max_files; $i < count($files); $i++) {
                unlink($files[$i]);
            }
        }
    }
    
    /**
     * Получение IP адреса клиента
     */
    private function get_client_ip(): string {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }
    
    /**
     * Получение последних записей лога
     * 
     * @param int $lines Количество строк
     * @return array
     */
    public function get_recent_logs(int $lines = 100): array {
        if (!file_exists($this->log_file)) {
            return [];
        }
        
        $file = new SplFileObject($this->log_file);
        $file->seek(PHP_INT_MAX);
        $total_lines = $file->key();
        
        $start_line = max(0, $total_lines - $lines);
        $logs = [];
        
        $file->seek($start_line);
        
        while (!$file->eof()) {
            $line = $file->current();
            if (!empty(trim($line))) {
                $logs[] = $line;
            }
            $file->next();
        }
        
        return $logs;
    }
}

// Инициализация логгера
$GLOBALS['atk_ved_logger'] = new ATK_VED_Logger();

/**
 * Глобальная функция для логирования
 * 
 * @param string $message
 * @param string $level
 * @param array $context
 * @return bool
 */
function atk_ved_log(string $message, string $level = 'info', array $context = []): bool {
    return $GLOBALS['atk_ved_logger']->log($message, $level, $context);
}

/**
 * Логирование действий пользователя
 */
function atk_ved_log_user_action(string $action, array $data = []): bool {
    return $GLOBALS['atk_ved_logger']->log_user_action($action, $data);
}

/**
 * Логирование событий безопасности
 */
function atk_ved_log_security(string $event, array $data = []): bool {
    return $GLOBALS['atk_ved_logger']->log_security_event($event, $data);
}

// Логирование входа пользователя
add_action('wp_login', function($user_login, $user) {
    atk_ved_log_user_action('User Login', [
        'user_login' => $user_login,
        'user_id' => $user->ID
    ]);
}, 10, 2);

// Логирование неудачных попыток входа
add_action('wp_login_failed', function($username) {
    atk_ved_log_security('Failed Login Attempt', [
        'username' => $username
    ]);
});

// Логирование отправки форм
add_action('wp_ajax_atk_ved_contact_form', function() {
    atk_ved_log_user_action('Contact Form Submission');
}, 1);
