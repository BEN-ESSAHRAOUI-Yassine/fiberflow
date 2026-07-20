FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git unzip zip libpng-dev libjpeg-dev libfreetype6-dev \
    libpq-dev libzip-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql gd zip bcmath mbstring

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY --from=node:22-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node:22-slim /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

COPY docker/php/php.ini /usr/local/etc/php/conf.d/fiberflow.ini

WORKDIR /var/www/html
