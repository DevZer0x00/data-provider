FROM php:8.0.6-fpm-alpine

ENV TZ=Europe/Minsk

RUN apk add --update --no-cache \
    curl \
    zsh \
    zip \
    git \
    shadow \
    autoconf \
    g++ \
    make \
    mysql-client \
 && docker-php-ext-install pdo_mysql \
 && echo "date.timezone=$TZ" > "$PHP_INI_DIR/conf.d/date-time-zone.ini" \
 && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY entrypoint.sh /

WORKDIR /var/www/html
ADD . .

ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

RUN mkdir /var/www/.composer
RUN chown www-data:www-data /var/www/.composer

USER www-data

#ENTRYPOINT [ "/bin/sh", "/entrypoint.sh"]