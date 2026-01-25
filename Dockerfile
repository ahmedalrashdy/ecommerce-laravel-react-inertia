FROM node:22-alpine AS frontend_builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build:ssr

FROM dunglas/frankenphp:php8.4

ENV SERVER_NAME=":80"
ENV FRANKENPHP_CONFIG="worker ./public/index.php"

# Install system dependencies and Node.js for SSR
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

# Install PHP extensions
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

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy dependency definitions
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Copy application code
COPY . .

# Copy frontend assets from builder stage
COPY --from=frontend_builder /app/public /var/www/html/public
COPY --from=frontend_builder /app/bootstrap/ssr /var/www/html/bootstrap/ssr

# Setup Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Config Files
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY .docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY .docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]