# 🐳 Docker Development Environment for ATK VED

## Требования

- Docker & Docker Compose
- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+

---

## 🚀 Быстрый старт

### 1. Запуск через Docker Compose

```bash
# Клонировать репозиторий
git clone <repo-url>
cd atk-ved.ru.local

# Запустить контейнеры
docker-compose up -d

# Открыть сайт
open http://localhost:8080
```

### 2. Установка WordPress

```bash
# Скачать WordPress
docker-compose exec wordpress wp core download --locale=ru_RU

# Настроить wp-config.php
docker-compose exec wordpress wp config create \
    --dbname=wordpress \
    --dbuser=wordpress \
    --dbpass=wordpress \
    --dbhost=mysql \
    --extra-php <<PHP
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
PHP

# Установить WordPress
docker-compose exec wordpress wp core install \
    --url=localhost:8080 \
    --title="АТК ВЭД" \
    --admin_user=admin \
    --admin_password=admin \
    --admin_email=admin@atk-ved.ru \
    --skip-email

# Активировать тему
docker-compose exec wordpress wp theme activate atk-ved
```

---

## 📁 Структура Docker

```
docker/
├── docker-compose.yml      # Основная конфигурация
├── docker-compose.dev.yml  # Разработка
├── docker-compose.prod.yml # Продакшен
└── wordpress/
    ├── Dockerfile          # Кастомный образ WordPress
    └── uploads.ini         # Настройки загрузки файлов
```

---

## 🔧 Команды Docker

```bash
# Запуск
docker-compose up -d

# Остановка
docker-compose down

# Перезапуск
docker-compose restart

# Логи
docker-compose logs -f wordpress
docker-compose logs -f mysql

# Выполнение команд
docker-compose exec wordpress bash
docker-compose exec mysql mysql -u wordpress -p wordpress

# WP-CLI
docker-compose exec wordpress wp <command>

# Очистка
docker-compose down -v  # Удалить volumes
docker-compose rm -f    # Удалить контейнеры
```

---

## 🛠️ Полезные команды WP-CLI

```bash
# Обновление базы данных
docker-compose exec wordpress wp db optimize

# Очистка кэша
docker-compose exec wordpress wp cache flush

# Экспорт БД
docker-compose exec wordpress wp db export /tmp/backup.sql

# Импорт БД
docker-compose exec wordpress wp db import /tmp/backup.sql

# Проверка состояния
docker-compose exec wordpress wp health check

# Список плагинов
docker-compose exec wordpress wp plugin list

# Активация плагина
docker-compose exec wordpress wp plugin activate contact-form-7
```

---

## 📊 Переменные окружения

Создайте файл `.env` в корне проекта:

```env
# Database
MYSQL_ROOT_PASSWORD=rootpassword
MYSQL_DATABASE=wordpress
MYSQL_USER=wordpress
MYSQL_PASSWORD=wordpress

# WordPress
WORDPRESS_DB_HOST=mysql
WORDPRESS_DB_USER=wordpress
WORDPRESS_DB_PASSWORD=wordpress
WORDPRESS_DB_NAME=wordpress

# Ports
HTTP_PORT=8080
HTTPS_PORT=443

# Redis (optional)
REDIS_HOST=redis
REDIS_PORT=6379
```

---

## 🔒 Безопасность

### Для продакшена:

1. Измените пароли в `.env`
2. Используйте `docker-compose.prod.yml`
3. Настройте SSL сертификаты
4. Включите firewall
5. Настройте backup

```bash
# Продакшен запуск
docker-compose -f docker-compose.prod.yml up -d
```

---

## 📈 Мониторинг

```bash
# Статус контейнеров
docker-compose ps

# Использование ресурсов
docker stats

# Логи MySQL
docker-compose logs mysql

# Логи WordPress
docker-compose logs wordpress
```

---

## 🐛 Troubleshooting

### Контейнер не запускается

```bash
# Проверить логи
docker-compose logs wordpress

# Пересоздать контейнер
docker-compose rm -f wordpress
docker-compose up -d wordpress
```

### Ошибки БД

```bash
# Перезапустить MySQL
docker-compose restart mysql

# Проверить подключение
docker-compose exec mysql mysql -u wordpress -p -e "SELECT 1"
```

### Проблемы с правами

```bash
# Исправить права на uploads
docker-compose exec wordpress chown -R www-data:www-data /var/www/html/wp-content/uploads
```

---

## 📚 Ресурсы

- [Docker Documentation](https://docs.docker.com/)
- [WordPress Docker Official Image](https://hub.docker.com/_/wordpress)
- [WP-CLI Documentation](https://wp-cli.org/)

---

**Версия:** 3.3.0  
**Последнее обновление:** Февраль 2026
