FROM php:8-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    zip \
    unzip


RUN apt-get clean && rm -rf /var/lib/apt/lists/*


RUN docker-php-ext-install zip


RUN pecl install redis && docker-php-ext-enable redis


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www


COPY composer.json ./


RUN if [ -f composer.json ]; then composer install --no-scripts --no-autoloader; fi


COPY . .


RUN if [ -f composer.json ]; then composer dump-autoload --optimize; fi


RUN mkdir -p /var/www/storage && \
    chown -R www-data:www-data /var/www/storage && \
    chmod -R 775 /var/www/storage