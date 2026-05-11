FROM php:8.4-fpm

# Arguments
ARG user=closer
ARG uid=1000
ARG gid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libsodium-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    sodium

# Install Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Kafka extension (optional - if using php-rdkafka)
# RUN apt-get install -y librdkafka-dev \
#     && pecl install rdkafka \
#     && docker-php-ext-enable rdkafka

# Install AMQP extension for RabbitMQ (optional)
RUN apt-get install -y libssl-dev librabbitmq-dev \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js (for Vite/build)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Create system user
RUN groupadd -g ${gid} ${user} \
    && useradd -u ${uid} -g ${gid} -m ${user} \
    && mkdir -p /home/${user}/.composer \
    && chown -R ${user}:${user} /home/${user}

# Configure PHP
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php.ini /usr/local/etc/php/php.ini

# Set working directory
WORKDIR /var/www

# Copy application
COPY --chown=${user}:${user} . /var/www

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm ci \
    && npm run build \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Set permissions
RUN chown -R ${user}:${user} /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Switch to non-root user
USER ${user}

# Expose port
EXPOSE 9000

CMD ["php-fpm"]
