FROM php:fpm-alpine

RUN apk add --no-cache composer

RUN mkdir /app

WORKDIR /app

COPY . .

RUN composer install --optimize-autoloader --no-dev
RUN php artisan config:cache && php artisan view:cache

ENTRYPOINT [ "php", "artisan", "serve" ]
