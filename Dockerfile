# ──────────────────────────────────────────────────────────────────────────────
# Stage 1 — Composer dependencies
# ──────────────────────────────────────────────────────────────────────────────
FROM composer:2.7 AS vendor

WORKDIR /app

COPY composer.json composer.lock* ./

# Install production dependencies only (no dev tools)
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# ──────────────────────────────────────────────────────────────────────────────
# Stage 2 — PHP-FPM runtime image
# ──────────────────────────────────────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS app

LABEL maintainer="Immobilier Platform"
LABEL description="API Laravel — Plateforme Immobilière"

# ── System dependencies ───────────────────────────────────────────────────────
RUN apk add --no-cache \
    bash \
    curl \
    freetype \
    git \
    icu-libs \
    libjpeg-turbo \
    libpng \
    libwebp \
    libzip \
    oniguruma \
    # Build-time headers (removed after extension install)
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        freetype-dev \
        icu-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libzip-dev \
        oniguruma-dev \
    \
    # PHP extensions required by Laravel 11
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        zip \
    \
    # phpredis extension (requires $PHPIZE_DEPS — must run before apk del .build-deps)
    && pecl install redis \
    && docker-php-ext-enable redis \
    \
    # Clean up build dependencies
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*

# ── PHP runtime configuration ─────────────────────────────────────────────────
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# ── Application code ──────────────────────────────────────────────────────────
WORKDIR /var/www

# Copy vendor from build stage
COPY --from=vendor /app/vendor ./vendor

# Copy application source (respects .dockerignore)
COPY . .

# ── Permissions ───────────────────────────────────────────────────────────────
RUN mkdir -p storage/app/public storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 storage bootstrap/cache

# ── Entrypoint ────────────────────────────────────────────────────────────────
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
