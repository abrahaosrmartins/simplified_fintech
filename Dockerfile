FROM php:8.3-fpm

ARG user=abrahao
ARG uid=1000

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libxml2-dev \
    libonig-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring xml pcntl bcmath sockets

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Install redis
RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www

# Copy custom configurations PHP
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Adjust persmissions
RUN chown -R $user:$user /var/lib/
RUN chmod -R 755 /var/lib/
# RUN chmod -R 775 /var/www/html/src/laravel/storage /var/www/html/src/laravel/storage/bootstrap/cache
# RUN chown -R www-data:www-data /var/www/html/src/laravel

USER $user
