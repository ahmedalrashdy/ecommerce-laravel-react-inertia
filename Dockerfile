FROM node:22-alpine AS node_dependencies
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

FROM dunglas/frankenphp:php8.4

ENV SERVER_NAME=":80"
ENV FRANKENPHP_CONFIG="worker ./public/index.php"

RUN apt-get update && apt-get install -y \
    supervisor \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    pcntl \
    bcmath \
    gd \
    intl \
    zip \
    opcache \
    redis \
    pdo_mysql \
    exif

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

COPY . .

RUN cp .env.example .env \
    && php artisan key:generate \
    && php artisan package:discover

COPY --from=node_dependencies /app/node_modules ./node_modules
RUN npm run build:ssr

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY .docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY .docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]