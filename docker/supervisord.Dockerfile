FROM php:7.4-cli

LABEL author="redditsaved"

ENV APP_ROOT=/app QUEUE_DRIVER=database NUM_PROCS=4 OPTIONS=

RUN apt-get update && apt-get install -y \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  mariadb-client \
  libzip-dev \
  unzip \
  supervisor

RUN pecl install pcov

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install -j$(nproc) \
  bcmath \
  gd \
  pcntl \
  pdo_mysql \
  zip

RUN docker-php-ext-enable \
  pcov

# disable pcov by default
RUN mv /usr/local/etc/php/conf.d/docker-php-ext-pcov.ini /usr/local/etc/php/conf.d/docker-php-ext-pcov.ini.di

RUN printf '[PHP]\ndate.timezone = "Africa/Johannesburg"\n' > /usr/local/etc/php/conf.d/tzone.ini

RUN groupadd supervisor
RUN usermod -a -G supervisor root

ADD ./docker/supervisord.conf /etc/supervisor/
ADD ./docker/supervisor.lamoid.conf /etc/supervisor/conf.d/

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
