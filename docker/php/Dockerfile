FROM php:8.3-fpm-alpine

# Dependências do sistema
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libzip-dev \
    icu-dev \
    linux-headers \
    $PHPIZE_DEPS

# Extensões PHP
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        intl \
        opcache \
        bcmath \
        pcntl

# Redis (phpredis)
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev 2>/dev/null || true

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

EXPOSE 9000

CMD ["php-fpm"]
