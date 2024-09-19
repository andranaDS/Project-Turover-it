FROM composer:latest AS composer
FROM php:8.1-fpm-alpine

RUN apk add --update --no-cache tzdata autoconf g++ libtool make libzip-dev icu-dev libxml2-dev nano zip rabbitmq-c-dev libxslt libxslt-dev

RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_mysql intl zip xml opcache xsl \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && pecl install pcov && docker-php-ext-enable pcov

#Redis
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
        && pecl install redis \
        && docker-php-ext-enable redis.so

# gd
RUN apk add --no-cache \
      freetype \
      libjpeg-turbo \
      libpng \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \
      nodejs npm yarn \
    && docker-php-ext-configure gd \
      --with-freetype=/usr/include/ \
      --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd \
    && apk del --no-cache \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \

RUN rm -rf /var/cache/apk/* \
	rm -rf /tmp/*

RUN ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime

USER 1000

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY php-ci.ini $PHP_INI_DIR/conf.d/php-ci.ini

WORKDIR /var/www/api
