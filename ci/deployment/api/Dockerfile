ARG NODE_VERSION=16
ARG BITNAMI_NGINX_VERSION=1.21.6

# encore
FROM node:${NODE_VERSION}-alpine AS api_platform_encore

ARG API_PATH=.

COPY ${API_PATH}/package.json ${API_PATH}/yarn.lock ${API_PATH}/webpack.config.js ./
COPY ${API_PATH}/assets ./assets/

RUN yarn install
RUN yarn build

# php
# this is heavily inspired from api-platform configuration (https://github.com/api-platform/api-platform/blob/v2.5.7/api/Dockerfile)
FROM registry.gitlab.com/agsi/free-work-back/php:8.1-fpm-base AS api_platform_php

ARG API_PATH=.
ARG CI_PATH=./ci/deployment/api

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY ${CI_PATH}/php/conf.d/api-platform.prod.ini $PHP_INI_DIR/conf.d/api-platform.ini

RUN set -eux; \
	{ \
		echo '[www]'; \
		echo 'ping.path = /ping'; \
	} | tee /usr/local/etc/php-fpm.d/docker-healthcheck.conf

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
# install Symfony Flex globally to speed up download of Composer packages (parallelized prefetching)
RUN set -eux; \
	composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"
ENV COMPOSER_MEMORY_LIMIT=-1

WORKDIR /srv/api

# build for production
ARG APP_ENV=prod

# prevent the reinstallation of vendors at every changes in the source code
COPY ${API_PATH}/composer.json ${API_PATH}/composer.lock ${API_PATH}/symfony.lock ./
RUN set -eux; \
	composer install --prefer-dist --no-dev --no-scripts --no-progress --no-suggest; \
	composer clear-cache

# do not use .env files in production
COPY ${API_PATH}/.env ./

# copy only specifically what we need
COPY ${API_PATH}/bin bin/
COPY ${API_PATH}/config config/
COPY ${API_PATH}/migrations migrations/
COPY ${API_PATH}/public public/
COPY ${API_PATH}/src src/
COPY ${API_PATH}/templates templates/
COPY ${API_PATH}/translations translations/
COPY --from=api_platform_encore /public/build public/build/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync
VOLUME /srv/api/var

COPY ${CI_PATH}/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

# we must enable php-fpm status page to use it: pm.status_path = /status
COPY ${CI_PATH}/php/healthcheck/php-fpm-healthcheck /usr/local/bin/php-fpm-healthcheck
RUN chmod +x /usr/local/bin/php-fpm-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

COPY ${CI_PATH}/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# "nginx" stage
# depends on the "php" stage above
# The OpenResty distribution of NGINX is only needed for Kubernetes compatiblity (dynamic upstream resolution)
FROM bitnami/nginx:${BITNAMI_NGINX_VERSION} AS api_platform_nginx

WORKDIR /srv/api/public

COPY --from=api_platform_php /srv/api/public ./

STOPSIGNAL SIGQUIT	