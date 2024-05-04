FROM php:8.2-fpm

RUN apt-get update && apt-get install -y wget git unzip \
    && pecl install xdebug-3.2.0RC1 \
    && docker-php-ext-enable xdebug

RUN docker-php-ext-install pdo pdo_mysql

RUN docker-php-ext-install pcntl

RUN pecl install redis \
  && docker-php-ext-enable redis

RUN wget https://getcomposer.org/installer -O - -q \
    | php -- --install-dir=/bin --filename=composer --quiet

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /srv/link-loom