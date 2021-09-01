FROM php:7.4-cli

LABEL author="redditsaved"

ENV APP_ROOT=/app QUEUE_DRIVER=database NUM_PROCS=4 OPTIONS=

RUN docker-php-ext-install bcmath
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
        supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN groupadd supervisor
RUN usermod -a -G supervisor root

RUN printf '[PHP]\ndate.timezone = "Africa/Johannesburg"\n' > /usr/local/etc/php/conf.d/tzone.ini

ADD ./docker/supervisord.conf /etc/supervisor/
ADD ./docker/supervisor.lamoid.conf /etc/supervisor/conf.d/

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
