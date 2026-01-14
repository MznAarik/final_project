FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libavif-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# ---- Configure & install PHP extensions ----
RUN docker-php-ext-configure gd \
        --with-freetype=/usr/include/freetype2 \
        --with-jpeg=/usr/include \
        --with-webp \
        --with-avif \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_pgsql \
        mbstring \
        xml \
        zip

# ---- Install Composer ----
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ---- Set working directory ----
WORKDIR /var/www/html

# ---- Copy app (optional) ----
# COPY . .

# ---- Expose port ----
EXPOSE 9000

# ---- Default command ----
CMD ["php-fpm"]
