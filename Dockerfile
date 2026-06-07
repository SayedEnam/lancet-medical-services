FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx supervisor curl git unzip libzip-dev oniguruma-dev icu-dev sqlite-dev mysql-client \
    && docker-php-ext-install pdo pdo_mysql mbstring intl zip bcmath opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

COPY . .
RUN composer install --no-dev --optimize-autoloader \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

RUN chown -R www-data:www-data storage bootstrap/cache

CMD ["php-fpm"]
