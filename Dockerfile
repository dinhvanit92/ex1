FROM php:8.1-fpm-alpine3.15 as local

ARG APP_ENV
RUN apk update && apk upgrade
RUN apk upgrade --update --no-cache
RUN apk add --no-cache \
    nginx \
    icu-dev \
    # composer \
    oniguruma-dev \
    autoconf automake libtool nasm \
    pcre-dev g++ gcc make sudo \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    openrc supervisor rsyslog \
    nodejs npm \
    postgresql-dev \
    shadow \
    tzdata \
    git

# replace iconv
RUN apk add --no-cache --repository http://dl-3.alpinelinux.org/alpine/latest-stable/community gnu-libiconv
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

RUN export TZ=Asia/Ho_Chi_Minh

# RUN composer self-update

# docker-php-ext-install  redis
RUN if [ "${APP_ENV}" = "local" ]; then \
    git config --global http.sslVerify false \
; fi
#
RUN git clone -b release/5.3.4 --depth 1 https://github.com/phpredis/phpredis.git /usr/src/php/ext/redis

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install intl pdo_mysql pdo_pgsql exif gd zip
RUN docker-php-ext-install bcmath

RUN apk add gmp-dev
RUN docker-php-ext-install gmp

ARG INSTALL_XDEBUG=false
# Xdebug
RUN if [ "${INSTALL_XDEBUG}" = "true" ]; then \
 pecl install xdebug && docker-php-ext-enable xdebug \
; fi

# useradd
RUN groupadd -g 1000 www && \
    useradd -s /bin/bash -u 1000 -N -g www -K MAIL_DIR=/dev/null -d /var/www www

WORKDIR /var/www
ADD . /var/www
WORKDIR /var/www/ex1

ADD ./.docker/nginx/conf.d/ /etc/nginx/conf.d/
ADD ./.docker/nginx/nginx.conf /etc/nginx/nginx.conf

ADD ./.docker/php/8.1/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/zzz-www.conf
ADD ./.docker/php/8.1/php.ini /usr/local/etc/php/php.ini
ADD ./.docker/supervisor/supervisord.conf /etc/supervisord.conf
ADD ./.docker/supervisor/supervisor.d/ /etc/supervisor.d/

RUN mkdir /run/php-fpm8.1

RUN chown www:www -R /run/php-fpm8.1 && \
    chown www:www -R /var/lib/nginx && \
    chown www:www -R /var/www && \
    ln -sf /dev/stdout /var/log/messages

RUN ln -sf /dev/stdout /var/log/nginx/access.log
RUN ln -sf /dev/stderr /var/log/nginx/error.log

RUN chmod 755 /var/www/.docker/setup.sh
RUN APP_ENV=$APP_ENV sh /var/www/.docker/setup.sh

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

COPY --from=php:8.1-cli-alpine3.15 /usr/local/bin/phpdbg /usr/local/bin/

CMD ["/usr/bin/supervisord"]
