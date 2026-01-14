FROM php:8.2-fpm

# Install system dependencies — IMPORTANT: add libonig-dev for mbstring
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
    nginx \
    supervisor \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /etc/nginx/sites-enabled/default \
    && mkdir -p /var/log/nginx /var/log/php-fpm /var/log/supervisor

# Configure GD
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

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy configs (make sure these files are in your repo root)
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Working directory — Railway clones your repo to /app
WORKDIR /app

# Optional: tweak php-fpm to listen on all interfaces (usually not needed)
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 9000/' /usr/local/etc/php-fpm.d/www.conf \
    && echo "daemon off;" >> /etc/nginx/nginx.conf

# Expose the public port Railway will use
EXPOSE 80

# Start supervisor (php-fpm + nginx)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]