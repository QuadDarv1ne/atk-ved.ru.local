<?php
/**
 * Объектный кэш для WordPress (Redis/Memcached совместимый)
 *
 * @package ATK_VED
 * @since   3.1.0
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Класс для управления кэшированием
 */
class ATK_VED_Cache_Manager {

    private static ?self $instance = null;
    private $cache = [];
    private $enabled = false;
    private $redis = null;
    private $memcached = null;

    public static function get_instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->enabled = $this->check_cache_support();
        
        if ( $this->enabled ) {
            $this->init_cache_backend();
            $this->add_hooks();
        }
    }

    /**
     * Проверка поддержки кэширования
     */
    private function check_cache_support(): bool {
        // Проверяем Redis
        if ( extension_loaded( 'redis' ) && defined( 'WP_REDIS_HOST' ) ) {
            return true;
        }

        // Проверяем Memcached
        if ( extension_loaded( 'memcached' ) && defined( 'WP_MEMCACHED_HOST' ) ) {
            return true;
        }

        // Проверяем APCu
        if ( extension_loaded( 'apcu' ) && function_exists( 'apcu_store' ) ) {
            return true;
        }

        // Проверяем объектный кэш WordPress
        global $wp_object_cache;
        return is_object( $wp_object_cache );
    }

    /**
     * Инициализация бэкенда кэширования
     */
    private function init_cache_backend(): void {
        // Redis
        if ( extension_loaded( 'redis' ) && defined( 'WP_REDIS_HOST' ) ) {
            $this->redis = new Redis();
            $this->redis->connect(
                WP_REDIS_HOST,
                defined( 'WP_REDIS_PORT' ) ? WP_REDIS_PORT : 6379,
                defined( 'WP_REDIS_TIMEOUT' ) ? WP_REDIS_TIMEOUT : 1
            );
            
            if ( defined( 'WP_REDIS_PASSWORD' ) && WP_REDIS_PASSWORD ) {
                $this->redis->auth( WP_REDIS_PASSWORD );
            }
            
            $this->redis->select(
                defined( 'WP_REDIS_DATABASE' ) ? WP_REDIS_DATABASE : 0
            );
            
            return;
        }

        // Memcached
        if ( extension_loaded( 'memcached' ) && defined( 'WP_MEMCACHED_HOST' ) ) {
            $this->memcached = new Memcached();
            $this->memcached->addServer(
                WP_MEMCACHED_HOST,
                defined( 'WP_MEMCACHED_PORT' ) ? WP_MEMCACHED_PORT : 11211
            );
            return;
        }
    }

    /**
     * Добавление хуков
     */
    private function add_hooks(): void {
        // Кэширование запросов к БД
        add_filter( 'posts_where', [ $this, 'cache_posts_where' ], 10, 2 );
        
        // Очистка кэша при обновлении
        add_action( 'save_post', [ $this, 'clear_post_cache' ], 10, 2 );
        add_action( 'deleted_post', [ $this, 'clear_post_cache' ], 10, 2 );
        
        // Очистка кэша терминов
        add_action( 'edited_terms', [ $this, 'clear_term_cache' ], 10, 2 );
        
        // Очистка кэша опций
        add_action( 'updated_option', [ $this, 'clear_option_cache' ], 10, 3 );
        add_action( 'deleted_option', [ $this, 'clear_option_cache' ], 10, 1 );
        
        // Кэширование REST API ответов
        add_filter( 'rest_pre_serve_request', [ $this, 'cache_rest_response' ], 10, 3 );
    }

    /**
     * Получение из кэша
     */
    public function get( string $key, $default = false ) {
        if ( ! $this->enabled ) {
            return $default;
        }

        $full_key = $this->get_full_key( $key );

        // Redis
        if ( $this->redis ) {
            $result = $this->redis->get( $full_key );
            return $result === false ? $default : maybe_unserialize( $result );
        }

        // Memcached
        if ( $this->memcached ) {
            $result = $this->memcached->get( $full_key );
            return $result === false ? $default : $result;
        }

        // APCu
        if ( function_exists( 'apcu_fetch' ) ) {
            $result = apcu_fetch( $full_key );
            return $result === false ? $default : $result;
        }

        // WordPress object cache
        return wp_cache_get( $key, '', false, $found );
    }

    /**
     * Сохранение в кэш
     */
    public function set( string $key, $value, int $ttl = 3600 ): bool {
        if ( ! $this->enabled ) {
            return false;
        }

        $full_key = $this->get_full_key( $key );

        // Redis
        if ( $this->redis ) {
            return $this->redis->setex( $full_key, $ttl, maybe_serialize( $value ) );
        }

        // Memcached
        if ( $this->memcached ) {
            return $this->memcached->set( $full_key, $value, $ttl );
        }

        // APCu
        if ( function_exists( 'apcu_store' ) ) {
            return apcu_store( $full_key, $value, $ttl );
        }

        // WordPress object cache
        return wp_cache_set( $key, $value, '', $ttl );
    }

    /**
     * Удаление из кэша
     */
    public function delete( string $key ): bool {
        if ( ! $this->enabled ) {
            return false;
        }

        $full_key = $this->get_full_key( $key );

        // Redis
        if ( $this->redis ) {
            return (bool) $this->redis->del( $full_key );
        }

        // Memcached
        if ( $this->memcached ) {
            return $this->memcached->delete( $full_key );
        }

        // APCu
        if ( function_exists( 'apcu_delete' ) ) {
            return apcu_delete( $full_key );
        }

        // WordPress object cache
        return wp_cache_delete( $key );
    }

    /**
     * Очистка кэша поста
     */
    public function clear_post_cache( int $post_id, WP_Post $post = null ): void {
        if ( ! $post ) {
            $post = get_post( $post_id );
        }

        if ( ! $post ) {
            return;
        }

        // Очищаем кэш по ID
        $this->delete( "post:{$post_id}" );
        
        // Очищаем кэш по slug
        $this->delete( "post:{$post->post_name}" );
        
        // Очищаем кэш таксономий
        $taxonomies = get_post_taxonomies( $post_id );
        foreach ( $taxonomies as $taxonomy ) {
            $terms = get_the_terms( $post_id, $taxonomy );
            if ( $terms && ! is_wp_error( $terms ) ) {
                foreach ( $terms as $term ) {
                    $this->delete( "term:{$taxonomy}:{$term->term_id}" );
                }
            }
        }
        
        // Очищаем кэш главной страницы если пост опубликован
        if ( $post->post_type === 'page' && $post->post_status === 'publish' ) {
            $this->delete( 'page:front' );
        }
    }

    /**
     * Очистка кэша терминов
     */
    public function clear_term_cache( int $term_id, string $taxonomy ): void {
        $this->delete( "term:{$taxonomy}:{$term_id}" );
        $this->delete( "term:{$taxonomy}:all" );
    }

    /**
     * Очистка кэша опций
     */
    public function clear_option_cache( string $option, $old_value = null, $value = null ): void {
        $this->delete( "option:{$option}" );
    }

    /**
     * Кэширование REST API ответов
     */
    public function cache_rest_response( $result, WP_REST_Server $server, WP_REST_Request $request ): ?array {
        if ( ! $this->enabled || $request->get_method() !== 'GET' ) {
            return $result;
        }

        $cache_key = 'rest:' . md5( $request->get_route() . wp_json_encode( $request->get_params() ) );
        $cached = $this->get( $cache_key );

        if ( $cached !== false ) {
            return $cached;
        }

        // Кэшируем на 5 минут
        if ( is_array( $result ) ) {
            $this->set( $cache_key, $result, 300 );
        }

        return $result;
    }

    /**
     * Кэширование SQL WHERE
     */
    public function cache_posts_where( string $where, WP_Query $query ): string {
        // Можно добавить кэширование сложных запросов
        return $where;
    }

    /**
     * Получение полного ключа
     */
    private function get_full_key( string $key ): string {
        $prefix = defined( 'WP_CACHE_KEY_SALT' ) ? WP_CACHE_KEY_SALT : 'atk_ved:';
        return $prefix . $key;
    }

    /**
     * Статистика кэша
     */
    public function get_stats(): array {
        if ( ! $this->enabled ) {
            return [ 'enabled' => false ];
        }

        $stats = [ 'enabled' => true ];

        if ( $this->redis ) {
            $info = $this->redis->info();
            $stats = array_merge( $stats, [
                'backend' => 'Redis',
                'version' => $info['redis_version'] ?? 'unknown',
                'connected_clients' => $info['connected_clients'] ?? 0,
                'used_memory' => $info['used_memory_human'] ?? 'unknown',
                'hits' => $info['keyspace_hits'] ?? 0,
                'misses' => $info['keyspace_misses'] ?? 0,
            ] );
        }

        if ( $this->memcached ) {
            $stats = array_merge( $stats, [
                'backend' => 'Memcached',
                'version' => $this->memcached->getVersion(),
            ] );
        }

        return $stats;
    }

    /**
     * Полная очистка кэша
     */
    public function flush(): bool {
        if ( ! $this->enabled ) {
            return false;
        }

        if ( $this->redis ) {
            return $this->redis->flushDb();
        }

        if ( $this->memcached ) {
            return $this->memcached->flush();
        }

        if ( function_exists( 'apcu_clear_cache' ) ) {
            return apcu_clear_cache();
        }

        return wp_cache_flush();
    }
}

// Инициализация
function atk_ved_init_cache_manager(): void {
    ATK_VED_Cache_Manager::get_instance();
}
add_action( 'after_setup_theme', 'atk_ved_init_cache_manager' );


// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Быстрое кэширование
 */
function atk_ved_cache_get( string $key, $default = false ) {
    return ATK_VED_Cache_Manager::get_instance()->get( $key, $default );
}

function atk_ved_cache_set( string $key, $value, int $ttl = 3600 ): bool {
    return ATK_VED_Cache_Manager::get_instance()->set( $key, $value, $ttl );
}

function atk_ved_cache_delete( string $key ): bool {
    return ATK_VED_Cache_Manager::get_instance()->delete( $key );
}

function atk_ved_cache_flush(): bool {
    return ATK_VED_Cache_Manager::get_instance()->flush();
}


// ============================================================================
// PAGE CACHE (HTML кэширование)
// ============================================================================

/**
 * Кэширование полных страниц
 */
class ATK_VED_Page_Cache {

    private static ?self $instance = null;
    private $cache_dir = '';
    private $enabled = false;

    public static function get_instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->cache_dir = WP_CONTENT_DIR . '/cache/atk-ved/';
        $this->enabled = get_theme_mod( 'atk_ved_enable_page_cache', false );

        if ( $this->enabled ) {
            $this->init();
        }
    }

    private function init(): void {
        // Создаём директорию кэша
        if ( ! file_exists( $this->cache_dir ) ) {
            wp_mkdir_p( $this->cache_dir );
        }

        // Перехватываем вывод
        add_action( 'template_redirect', [ $this, 'start_cache' ], 0 );
        add_action( 'shutdown', [ $this, 'end_cache' ], 0 );
    }

    public function start_cache(): void {
        if ( ! $this->should_cache() ) {
            return;
        }

        $cache_file = $this->get_cache_file();

        // Проверяем существующий кэш
        if ( file_exists( $cache_file ) && ! $this->is_expired( $cache_file ) ) {
            readfile( $cache_file );
            exit;
        }

        // Начинаем буферизацию
        ob_start( [ $this, 'save_cache' ] );
    }

    public function end_cache(): void {
        if ( ob_get_level() > 0 ) {
            ob_end_flush();
        }
    }

    public function save_cache( string $content ): string {
        if ( ! $this->should_cache() ) {
            return $content;
        }

        $cache_file = $this->get_cache_file();
        
        // Сохраняем кэш
        file_put_contents( $cache_file, $content );
        file_put_contents( $cache_file . '.meta', serialize( [
            'time' => time(),
            'url' => $_SERVER['REQUEST_URI'] ?? '',
        ] ) );

        return $content;
    }

    private function should_cache(): bool {
        // Не кэшируем админку
        if ( is_admin() ) {
            return false;
        }

        // Не кэшируем AJAX
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return false;
        }

        // Не кэшируем REST API
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            return false;
        }

        // Не кэшируем для авторизованных
        if ( is_user_logged_in() ) {
            return false;
        }

        // Не кэшируем поиск и архивы
        if ( is_search() || is_archive() || is_feed() ) {
            return false;
        }

        // Не кэшируем если есть POST данные
        if ( ! empty( $_POST ) ) {
            return false;
        }

        return true;
    }

    private function get_cache_file(): string {
        $path = $this->cache_dir;
        
        // Добавляем домен для мультисайта
        if ( is_multisite() ) {
            $path .= get_current_blog_id() . '/';
        }
        
        $path .= md5( $_SERVER['REQUEST_URI'] ?? '/' ) . '.html';
        
        return $path;
    }

    private function is_expired( string $cache_file ): bool {
        $meta_file = $cache_file . '.meta';
        
        if ( ! file_exists( $meta_file ) ) {
            return true;
        }

        $meta = unserialize( file_get_contents( $meta_file ) );
        $age = time() - ( $meta['time'] ?? 0 );

        // Кэш устарел через 1 час
        return $age > HOUR_IN_SECONDS;
    }
}

// Инициализация page cache
function atk_ved_init_page_cache(): void {
    ATK_VED_Page_Cache::get_instance();
}
add_action( 'after_setup_theme', 'atk_ved_init_page_cache' );
