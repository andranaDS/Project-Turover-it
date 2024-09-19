ARG PHP_VERSION=8.1
FROM php:${PHP_VERSION}-fpm-alpine

RUN apk add --update --no-cache acl fcgi file gettext tzdata autoconf g++ libtool make libzip-dev icu-dev libxml2-dev nano zip rabbitmq-c-dev libxslt libxslt-dev

ARG APCU_VERSION=5.1.21

RUN docker-php-ext-configure intl \
    && docker-php-ext-install pcntl pdo pdo_mysql intl zip xml opcache xsl  \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && pecl install apcu-${APCU_VERSION} && docker-php-ext-enable apcu

RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
        && pecl install redis \
        && docker-php-ext-enable redis.so

# Setup GD extension
RUN apk add --no-cache \
      freetype \
      libjpeg-turbo \
      libpng \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \
    && docker-php-ext-configure gd \
      --with-freetype=/usr/include/ \
      --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd \
    && apk del --no-cache \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev

RUN pecl clear-cache \
    && rm -rf /var/cache/apk/* \
    && rm -rf /tmp/*

# blackfire
RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && architecture=$(uname -m) \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/alpine/$architecture/$version \
    && mkdir -p /tmp/blackfire \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get ('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8307\n" > $PHP_INI_DIR/conf.d/blackfire.ini \
    && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz

# new relic
RUN rm -rf /etc/dpkg/
RUN mkdir -p /etc/default
RUN \
  curl -L https://download.newrelic.com/php_agent/release/newrelic-php5-9.21.0.311-linux-musl.tar.gz | tar -C /tmp -zx && \
  export NR_INSTALL_USE_CP_NOT_LN=1 && \
  export NR_INSTALL_SILENT=1 && \
  /tmp/newrelic-php5-*/newrelic-install install && \
  rm -rf /tmp/newrelic-php5-* /tmp/nrinstall* && \
  sed -i \
      -e 's/"REPLACE_WITH_REAL_KEY"/"eu01xx4d3c39ee55e356c9b667193ce4a062NRAL"/' \
      -e 's/newrelic.appname = "PHP Application"/newrelic.appname = "backend-prod"/' \
      -e 's/;newrelic.daemon.app_connect_timeout =.*/newrelic.daemon.app_connect_timeout=15s/' \
      -e 's/;newrelic.daemon.start_timeout =.*/newrelic.daemon.start_timeout=5s/' \
      /usr/local/etc/php/conf.d/newrelic.ini \

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
