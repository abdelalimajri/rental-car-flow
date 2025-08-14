FROM php:8.2-fpm

# Install system dependencies & PHP extensions in one go
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nano \
    iputils-ping \
 && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Composer (copy only the binary to save space)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Create user for application
RUN groupadd -g 1000 www\
 &&  useradd -u 1000 -ms /bin/bash -g www www

# set permissions
RUN mkdir -p /var/www/html/var /var/www/html/public \
 && chown -R www:www /var/www/html \
 && chmod -R 755 /var/www/html

# Switch to non-root user
USER www

EXPOSE 9000
CMD php bin/console tailwind:build && php-fpm

