FROM php:8.2-fpm-bullseye

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    zip unzip git curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd pdo pdo_mysql mbstring xml ctype curl fileinfo zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install Node and Bun if needed
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && curl -fsSL https://bun.sh/install | bash

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Expose port for Railway
EXPOSE 3000

# Start PHP-FPM
CMD ["php-fpm"]
