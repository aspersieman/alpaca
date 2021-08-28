# ----------------------
# The FPM base container
# ----------------------
FROM php:8-fpm as dev

RUN docker-php-ext-install -j$(nproc) pdo_mysql
RUN pecl install pcov && \
    docker-php-ext-enable pcov && \
    # disable pcov by default
    mv /usr/local/etc/php/conf.d/docker-php-ext-pcov.ini /usr/local/etc/php/conf.d/docker-php-ext-pcov.ini.di
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        mariadb-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

WORKDIR /app

# ----------------------
# Composer install step
# ----------------------
FROM composer:2.1 as build

WORKDIR /app

COPY composer.* ./
COPY database/ database/

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

# ----------------------
# npm install step
# ----------------------
FROM node:12-alpine as node

WORKDIR /app

COPY *.json *.mix.js /app/
COPY resources /app/resources

RUN mkdir -p /app/public \
    && npm install && npm run production

# ----------------------
# The FPM production container
# ----------------------
FROM dev

COPY ./docker/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY . /app
COPY --from=build /app/vendor/ /app/vendor/
COPY --from=node /app/public/js/ /app/public/js/
COPY --from=node /app/public/css/ /app/public/css/
COPY --from=node /app/public/img/ /app/public/img/
COPY --from=node /app/public/fonts/ /app/public/fonts/
COPY --from=node /app/mix-manifest.json /app/public/mix-manifest.json

RUN chmod -R 777 /app/storage


