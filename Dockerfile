FROM php:8.2-fpm-bullseye

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

# PHP extensions (ONLY what is needed)
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

# App files
COPY . .

# Node (only if your app truly needs it)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Bun (optional – only if used)
RUN curl -fsSL https://bun.sh/install | bash

# PHP deps
RUN composer install --optimize-autoloader --no-interaction --no-scripts

EXPOSE 9000

CMD ["php-fpm"]
