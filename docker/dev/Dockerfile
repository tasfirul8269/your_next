# syntax=docker/dockerfile:1
#
# Development image — source is bind-mounted (see docker-compose.dev.yml), so this
# image only provides the PHP runtime + extensions + Composer. No app code is baked in.
# The container runs `php artisan serve` (the dev web server); Vite HMR runs in
# separate node containers.

FROM php:8.3-cli-bookworm

# --- System deps for the PHP extensions this app needs ---
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libicu-dev libonig-dev libzip-dev libpng-dev libjpeg-dev \
        libfreetype6-dev libcurl4-openssl-dev libxml2-dev default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql mbstring bcmath intl zip gd exif calendar pcntl \
    && rm -rf /var/lib/apt/lists/*

# --- Xdebug (off by default; enable via XDEBUG_MODE env in compose) ---
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY docker/dev/php.ini /usr/local/etc/php/conf.d/zz-app-dev.ini

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8000

# Source is mounted at runtime; install deps then boot the dev server.
CMD ["sh", "-c", "composer install --no-interaction && php artisan serve --host=0.0.0.0 --port=8000"]
