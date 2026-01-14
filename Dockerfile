FROM php:8.2-fpm

# 1. Install ALL dependencies in one layer (including pkg-config for safety)
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
    libonig-dev \
    pkg-config \
    nginx \
    supervisor \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /etc/nginx/sites-enabled/default \
    && mkdir -p /var/log/nginx /var/log/php-fpm /var/log/supervisor

# 2. Verify oniguruma is detectable BEFORE installing extensions
RUN pkg-config --modversion oniguruma || (echo "oniguruma not found by pkg-config" && exit 1)

# 3. Now configure & install extensions in a separate layer
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

# Rest of your Dockerfile (Composer, COPY configs, WORKDIR /app, sed tweaks, EXPOSE 80, CMD supervisord)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /app

RUN sed -i 's/listen = 127.0.0.1:9000/listen = 9000/' /usr/local/etc/php-fpm.d/www.conf \
    && echo "daemon off;" >> /etc/nginx/nginx.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]