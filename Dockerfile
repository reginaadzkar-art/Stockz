# ==========================================
# Stage 1: Build Frontend Assets (Vite)
# ==========================================
FROM node:22-alpine AS frontend

WORKDIR /app

# Install npm dependencies
COPY package*.json ./
RUN npm ci

# Copy frontend source files
COPY vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

# Build production assets
RUN npm run build

# ==========================================
# Stage 2: Production PHP-FPM + Nginx
# ==========================================
FROM php:8.2-fpm-alpine AS production

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    curl \
    bash \
    gettext \
    git \
    zip \
    unzip \
    libpng \
    libzip \
    icu-libs \
    libpq \
    sqlite-libs

# Install PHP extensions using docker-php-extension-installer
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    bcmath \
    gd \
    intl \
    mbstring \
    opcache \
    pcntl \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    redis \
    zip

# Copy Composer binary from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files and install PHP dependencies (without dev packages)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist

# Copy entire application code
COPY . .

# Copy compiled frontend assets from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Finish Composer dump-autoload for production
RUN composer dump-autoload --optimize --no-dev

# Copy custom configurations
COPY docker/php.ini $PHP_INI_DIR/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/nginx.conf.template /etc/nginx/nginx.conf.template
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Fix line endings & execution permissions for entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/entrypoint.sh

# Create necessary directories and set ownership & permissions
RUN mkdir -p /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/framework/cache/data \
             /var/www/html/storage/logs \
             /var/www/html/bootstrap/cache \
             /var/www/html/database && \
    chown -R www-data:www-data /var/www/html/storage \
                               /var/www/html/bootstrap/cache \
                               /var/www/html/database && \
    chmod -R 775 /var/www/html/storage \
                 /var/www/html/bootstrap/cache

# Render uses dynamic $PORT (usually 10000)
EXPOSE 10000 80

# Run entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
