FROM php:8.2-apache

# Enable Apache rewrite (needed for Laravel)
RUN a2enmod rewrite

WORKDIR /var/www/html

# System dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    zip unzip git curl pkg-config \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions (ONLY non-core ones)
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install \
        gd \
        pdo \
        pdo_mysql \
        mbstring \
        xml \
        zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app
COPY . .

# Fix permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Apache must point to /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Install PHP deps
RUN composer install --optimize-autoloader --no-interaction --no-scripts

EXPOSE 80

CMD ["apache2-foreground"]
