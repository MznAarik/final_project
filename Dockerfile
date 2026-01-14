# Base image
FROM php:8.2-fpm-bullseye

# Set working directory
WORKDIR /var/www/html

# Install system dependencies + Nginx
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libzip-dev libxml2-dev \
    zip unzip git curl nginx \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd pdo pdo_mysql mbstring xml zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Enable Nginx to serve Laravel
COPY nginx.conf /etc/nginx/sites-available/default

# Expose port
EXPOSE 80

# Start both services
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
