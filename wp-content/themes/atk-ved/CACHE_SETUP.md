# 📦 Настройка кэширования

## Типы кэширования в теме АТК ВЭД

### 1. Объектный кэш (Redis/Memcached)

**Рекомендуется:** Redis 7+

#### Установка Redis (Docker)

```yaml
# docker-compose.yml
services:
  redis:
    image: redis:7-alpine
    container_name: atk_ved_redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
    command: redis-server --appendonly yes
```

#### Настройка WordPress

1. Скопируйте `redis.config.example` в `.env`
2. Добавьте константы в `wp-config.php`:

```php
// Перед строкой "That's all, stop editing!"
define( 'WP_REDIS_HOST', 'redis' );
define( 'WP_REDIS_PORT', 6379 );
define( 'WP_REDIS_TIMEOUT', 1 );
define( 'WP_REDIS_DATABASE', 0 );
define( 'WP_CACHE_KEY_SALT', 'atk_ved_ru_local_' );
define( 'WP_CACHE', true );
```

3. Установите плагин Redis Object Cache:

```bash
# WP-CLI
wp plugin install redis-cache --activate

# Или через админку:
# Плагины → Добавить новый → Redis Object Cache
```

4. Активируйте кэш:
   - Настройки → Redis → Enable Object Cache

---

### 2. Page Cache (HTML кэширование)

Встроено в тему. Включается в Customizer:

**Внешний вид → Настроить → Производительность → Включить кэширование страниц**

#### Настройки

| Параметр | Значение | Описание |
|----------|----------|----------|
| **Время жизни кэша** | 1 час | Через сколько удаляется старый кэш |
| **Очистка при обновлении** | Авто | Кэш очищается при публикации поста |

#### Ручная очистка кэша

```php
// В functions.php или через консоль
atk_ved_cache_flush();
```

---

### 3. Browser Cache (HTTP заголовки)

Настроено в `.htaccess`:

```apache
# Кэширование изображений - 1 год
Header set Cache-Control "max-age=31536000, public, immutable"

# Кэширование CSS/JS - 1 месяц
Header set Cache-Control "max-age=2592000, public"

# HTML не кэшируется
<FilesMatch "\.(html|php)$">
    Header set Cache-Control "max-age=0, private, no-store, no-cache, must-revalidate"
</FilesMatch>
```

---

### 4. Database Query Cache

Автоматически кэшируются:

- Результаты запросов `WP_Query`
- Опции WordPress
- Данные таксономий
- Метаданные постов

#### Оптимизация БД

```bash
# WP-CLI команда для оптимизации
wp db optimize

# Очистка ревизий
wp post delete $(wp post list --post_type='revision' --format=ids)

# Очистка спам-комментариев
wp comment delete $(wp comment list --status=spam --format=ids)
```

---

## 🎯 Рекомендуемая конфигурация

### Для разработки

```env
# .env
WP_REDIS_HOST=localhost
WP_REDIS_PORT=6379
WP_DEBUG=true
WP_CACHE=false  # Отключаем page cache для отладки
```

### Для продакшена

```env
# .env
WP_REDIS_HOST=redis
WP_REDIS_PORT=6379
WP_DEBUG=false
WP_CACHE=true
WP_CACHE_KEY_SALT=your_production_salt_here
```

---

## 📊 Мониторинг кэша

### Статистика Redis

```bash
# Подключение к Redis CLI
redis-cli

# Просмотр статистики
INFO

# Просмотр ключей
KEYS atk_ved:*

# Удаление всех ключей
FLUSHDB

# Мониторинг в реальном времени
MONITOR
```

### Статистика в WordPress

```php
// Получить статистику кэша
$stats = ATK_VED_Cache_Manager::get_instance()->get_stats();
print_r($stats);

// Вывод в админке
add_action('admin_notices', function() {
    $stats = ATK_VED_Cache_Manager::get_instance()->get_stats();
    echo '<div class="notice notice-info"><pre>';
    print_r($stats);
    echo '</pre></div>';
});
```

---

## ⚡ Проверка эффективности

### Hit/Miss Ratio

Хороший показатель: **>80% hits**

```bash
redis-cli INFO stats | grep keyspace
```

### Время генерации страниц

```php
// Добавьте в footer.php
add_action('wp_footer', function() {
    if (WP_DEBUG) {
        echo '<!-- Generated in ' . timer_stop() . ' seconds -->';
        echo '<!-- Queries: ' . get_num_queries() . ' -->';
        echo '<!-- Cache hits: ' . $GLOBALS['wp_object_cache']->cache_hits . ' -->';
        echo '<!-- Cache misses: ' . $GLOBALS['wp_object_cache']->cache_misses . ' -->';
    }
});
```

---

## 🔧 Troubleshooting

### Кэш не работает

1. Проверьте подключение к Redis:
   ```bash
   redis-cli ping  # Должен вернуть PONG
   ```

2. Проверьте константы в `wp-config.php`:
   ```php
   var_dump(defined('WP_REDIS_HOST'));  // true
   var_dump(extension_loaded('redis'));  // true
   ```

3. Проверьте логи Redis:
   ```bash
   docker logs atk_ved_redis
   ```

### Кэш не очищается

Принудительная очистка:

```php
// Через WP-CLI
wp cache flush

// Через PHP
wp_cache_flush();
atk_ved_cache_flush();

# Очистка page cache
rm -rf wp-content/cache/atk-ved/*
```

### Проблемы с памятью Redis

```bash
# Проверка использования памяти
redis-cli INFO memory

# Ограничение памяти в redis.conf
maxmemory 256mb
maxmemory-policy allkeys-lru
```

---

## 📈 Ожидаемые результаты

| Метрика | Без кэша | С Redis | Улучшение |
|---------|----------|---------|-----------|
| **Время генерации** | 0.5s | 0.05s | -90% |
| **Запросов к БД** | 50-100 | 5-10 | -90% |
| **TTFB** | 800ms | 100ms | -87% |
| **RPS** | 50 | 500 | +900% |

---

## 📚 Дополнительные ресурсы

- [Redis Documentation](https://redis.io/documentation)
- [WordPress Object Cache](https://developer.wordpress.org/reference/classes/wp_object_cache/)
- [Redis Object Cache Plugin](https://wordpress.org/plugins/redis-cache/)

---

**Версия:** 3.1.0  
**Последнее обновление:** Февраль 2026
