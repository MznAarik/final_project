FROM php:8.2-fpm-bullseye

# System dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip unzip git curl

# PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd pdo pdo_mysql mbstring tokenizer xml ctype curl fileinfo

WORKDIR /var/www

# Composer install
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --optimize-autoloader --no-interaction

# Copy the rest of the app
COPY . .

# Run Laravel
CMD php artisan serve --host=0.0.0.0 --port=${PORT}
