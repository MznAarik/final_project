# Stage 1: Build PHP environment
FROM php:8.2-fpm-bullseye AS php

WORKDIR /var/www/html

# System dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    zip unzip git curl pkg-config nginx supervisor \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd pdo pdo_mysql mbstring xml zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app
COPY . .

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Install PHP deps
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Stage 2: Nginx + Supervisor
FROM php:8.2-fpm-bullseye

WORKDIR /var/www/html

# Copy app from previous stage
COPY --from=php /var/www/html /var/www/html

# Install Nginx and Supervisor
RUN apt-get update && apt-get install -y nginx supervisor \
    && rm -rf /var/lib/apt/lists/*

# Configure Nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Supervisor config to run PHP-FPM + Nginx
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-n"]
