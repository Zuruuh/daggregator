# syntax=docker/dockerfile:1.10-labs

FROM alpine:3.20 AS dependencies

RUN apk add --no-cache composer git

WORKDIR /srv
ENV COMPOSER_ALLOW_SUPERUSER=true

COPY --link composer.json composer.lock /srv/

RUN --mount=type=cache,target=/root/.composer/cache composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --audit \
    --ignore-platform-reqs

FROM alpine:3.20 AS app

WORKDIR /srv
ENV APP_ENV=prod
ENV APP_DEBUG=false

RUN apk add --no-cache \
    php83-fpm \
    php83-intl \
    php83-simplexml \
    php83-ctype \
    php83-mbstring \
    php83-xml \
    php83-xmlwriter \
    php83-xmlreader \
    php83-dom \
    php83-fileinfo \
    php83-pecl-redis \
    php83-tokenizer \
    composer && \
    apk add --no-cache -X http://dl-cdn.alpinelinux.org/alpine/edge/testing/ php83-pecl-excimer

RUN adduser -D php && chown -R php:php /srv /var/log/php83

USER php

COPY --chown=php:php --from=dependencies /srv/vendor /srv/vendor
COPY --chown=php:php ./php-fpm.conf /etc/php83/php-fpm.d/www.conf
COPY --chown=php:php . /srv

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --audit \
    --prefer-dist && \
    bin/console cache:warmup

STOPSIGNAL SIGQUIT

CMD [ "php-fpm83", "--nodaemonize", "--force-stderr" ]
