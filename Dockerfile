# ───────────────────────────────────────────
# Stage 1: Composer dependencies
# ───────────────────────────────────────────
FROM composer:2 AS composer-deps

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev

# ───────────────────────────────────────────
# Stage 2: Frontend assets
# ───────────────────────────────────────────
FROM node:22-alpine AS frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

COPY resources/ resources/
COPY vite.config.js ./
COPY public/ public/
COPY --from=composer-deps /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views vendor/laravel/framework/src/Illuminate/Pagination/resources/views/
RUN npm run build

# ───────────────────────────────────────────
# Stage 3: Production image (PHP-FPM + Nginx)
# ───────────────────────────────────────────
FROM php:8.4-fpm-alpine

# install-php-extensions handles deps, retries, and cleanup automatically
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
        bcmath \
        gd \
        intl \
        opcache \
        pcntl \
        pdo_pgsql \
        pgsql \
        redis \
        zip

RUN apk add --no-cache nginx supervisor

# PHP production settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/production/php.ini "$PHP_INI_DIR/conf.d/99-production.ini"
COPY docker/production/nginx.conf /etc/nginx/nginx.conf
COPY docker/production/supervisord.conf /etc/supervisord.conf

WORKDIR /var/www/html

# Copy application code
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=composer-deps /app/vendor vendor/
COPY --chown=www-data:www-data --from=frontend /app/public/build public/build/

# Create storage structure and ensure directories exist for uploads
RUN mkdir -p \
        storage/app/public/avatars \
        storage/app/public/banners \
        storage/app/public/site-settings \
        storage/app/private/site-assets \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Storage symlink
RUN rm -f public/storage && ln -s /var/www/html/storage/app/public public/storage

# Remove dev files from the image
RUN rm -rf node_modules tests .env .env.example docker/staging \
    Dockerfile.dev docker-compose.yml .git

COPY docker/production/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
