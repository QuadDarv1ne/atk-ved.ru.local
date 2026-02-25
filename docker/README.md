# Docker разработка для АТК ВЭД

## Быстрый старт

### 1. Запуск контейнеров

```bash
# Перейдите в директорию docker
cd docker

# Запустите все сервисы
docker-compose up -d

# Или с пересборкой
docker-compose up -d --build
```

### 2. Доступ к сервисам

| Сервис | URL | Порт |
|--------|-----|------|
| WordPress | http://localhost:8080 | 8080 |
| phpMyAdmin | http://localhost:8081 | 8081 |
| Mailhog (email) | http://localhost:8025 | 8025 |
| MySQL | localhost | 3306 |

### 3. Остановка

```bash
# Остановить все контейнеры
docker-compose down

# Остановить и удалить volumes (данные БД будут удалены!)
docker-compose down -v
```

## Команды Docker

```bash
# Просмотр логов
docker-compose logs -f wordpress
docker-compose logs -f db

# Вход в контейнер WordPress
docker-compose exec wordpress bash

# Вход в контейнер MySQL
docker-compose exec db mysql -uatk_user -patk_password atk_ved

# Перезапуск контейнера
docker-compose restart wordpress

# Очистка кэша Docker
docker system prune -a
```

## Настройка WordPress

При первой установке используйте:
- **Имя БД**: `atk_ved`
- **Пользователь**: `atk_user`
- **Пароль**: `atk_password`
- **Хост**: `db:3306`

## Переменные окружения

Создайте файл `.env` в папке `docker/` для переопределения значений:

```env
MYSQL_ROOT_PASSWORD=your_secure_password
MYSQL_PASSWORD=your_secure_password
PMA_PASSWORD=your_secure_password
```

## Отладка

### Включение WP_DEBUG

В `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Просмотр логов PHP

```bash
docker-compose exec wordpress tail -f /var/www/html/wp-content/debug.log
```

### Xdebug (опционально)

Для включения Xdebug раскомментируйте в Dockerfile:
```dockerfile
# RUN pecl install xdebug && docker-php-ext-enable xdebug
```

## Бэкап базы данных

```bash
# Экспорт
docker-compose exec db mysqldump -uroot -proot_password atk_ved > backup.sql

# Импорт
docker-compose exec -T db mysql -uroot -proot_password atk_ved < backup.sql
```

## Проблемы и решения

### Порт 8080 уже занят

Измените порт в `docker-compose.yml`:
```yaml
ports:
  - "8081:80"  # Вместо 8080:80
```

### Ошибка подключения к БД

Проверьте что контейнер db запущен:
```bash
docker-compose ps
```

### Медленная работа

Увеличьте ресурсы Docker Desktop:
- CPU: 4 ядра
- RAM: 4 GB
- Swap: 2 GB

## Production развёртывание

Для продакшена используйте:
1. Реальные SSL сертификаты
2. Надёжные пароли
3. Внешнюю базу данных
4. Redis для кэширования
5. Nginx вместо Apache

---
**Версия:** 1.8.0  
**Обновлено:** Февраль 2026
