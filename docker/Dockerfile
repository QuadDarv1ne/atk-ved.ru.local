# Docker для разработки АТК ВЭД
# Версия: 1.8.0

FROM wordpress:php8.4-apache

# Установка дополнительных PHP расширений
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Установка GD для обработки изображений
RUN docker-php-ext-install gd

# Установка Zip для плагинов
RUN docker-php-ext-install zip

# Установка настроек PHP
RUN { \
    echo 'upload_max_filesize = 128M'; \
    echo 'post_max_size = 128M'; \
    echo 'memory_limit = 512M'; \
    echo 'max_execution_time = 300'; \
    echo 'max_input_time = 300'; \
} > /usr/local/etc/php/conf.d/uploads.ini

# Включение mod_rewrite для WordPress
RUN a2enmod rewrite

# Копирование темы
COPY . /var/www/html/wp-content/themes/atk-ved/

# Установка прав доступа
RUN chown -R www-data:www-data /var/www/html

# Expose порт
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Запуск
CMD ["apache2-foreground"]
