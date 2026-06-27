# syntax=docker/dockerfile:1
#
# Production image — multi-stage. Produces a lean PHP-FPM image containing the app,
# its Composer deps (no dev), and the pre-built Vite assets. No source bind-mount,
# no dev tooling. Served by a separate Nginx container (see docker-compose.prod.yml).

# ---------------------------------------------------------------------------
# Stage 1: build front-end assets (Admin + Shop) with Vite
# ---------------------------------------------------------------------------
FROM node:20-bookworm AS assets

WORKDIR /app
# Copy only what the builds need first (better layer caching)
COPY packages/Frooxi/Admin/package.json packages/Frooxi/Admin/
COPY packages/Frooxi/Shop/package.json  packages/Frooxi/Shop/
# Bring in the rest of the source (vite configs, JS/CSS, blade-referenced assets)
COPY . .

RUN cd packages/Frooxi/Admin && npm install && npm run build
RUN cd packages/Frooxi/Shop  && npm install && npm run build

# ---------------------------------------------------------------------------
# Stage 2: install PHP dependencies (production only)
# ---------------------------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
COPY packages ./packages
# Path-repository packages must be present before install resolves them.
RUN composer install --no-dev --no-scripts --prefer-dist --optimize-autoloader --no-interaction

# ---------------------------------------------------------------------------
# Stage 3: final runtime — PHP-FPM + extensions + app + built artifacts
# ---------------------------------------------------------------------------
FROM php:8.3-fpm-bookworm AS runtime

RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev libonig-dev libzip-dev libpng-dev libjpeg-dev \
        libfreetype6-dev libcurl4-openssl-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql mbstring bcmath intl zip gd exif calendar opcache pcntl \
    && rm -rf /var/lib/apt/lists/*

COPY docker/prod/php.ini /usr/local/etc/php/conf.d/zz-app-prod.ini

WORKDIR /var/www/html

# App source
COPY . .
# Production vendor/ and built assets from the earlier stages
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/themes ./public/themes

# Re-run package discovery now that vendor + source are both present, and cache config/routes.
RUN composer dump-autoload --optimize --no-dev --no-interaction \
    && php artisan package:discover --ansi || true

# Laravel needs these writable
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/prod/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
