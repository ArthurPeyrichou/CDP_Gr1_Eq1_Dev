FROM node:lts-alpine AS builder

WORKDIR /build

COPY  package.json package-lock.json webpack.config.js ./
COPY assets ./assets

RUN mkdir public
RUN npm install
RUN npm run build

FROM php:7.3-apache

RUN apt-get update && \
apt-get install -y \
libzip-dev unzip wait-for-it

RUN docker-php-ext-install zip
RUN docker-php-ext-install opcache
RUN docker-php-ext-install pdo_mysql

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer

WORKDIR /var/www/firescrum/

COPY docker/apache-config/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY . .
COPY --from=builder /build/public/build ./public/build
COPY .env.docker .env

RUN composer install
