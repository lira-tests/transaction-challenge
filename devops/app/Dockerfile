FROM php:7.4-fpm-alpine

ARG USER_UID=1000
ARG USER_GID=1000

ENV DOCKERIZE_VERSION v0.6.1

WORKDIR /var/www

RUN apk add --no-cache  file re2c autoconf make g++ php7-dev curl-dev zlib-dev pcre-dev curl vim openssl bash oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql bcmath iconv mbstring pcntl curl exif tokenizer \
    && pecl install xdebug redis \
    && docker-php-ext-enable xdebug redis \
    && docker-php-source delete \
    && { \
      echo "xdebug.remote_enable=on"; \
      echo "xdebug.remote_autostart=on"; \
      echo "xdebug.remote_port=9001"; \
      echo "xdebug.remote_handler=dbgp"; \
      echo "xdebug.remote_connect_back=0"; \
      echo "xdebug.idekey=local"; \
      echo "xdebug.remote_host=172.77.1.1"; \
    } >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm -rf /var/www/html \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && ln -s public html \
    && deluser www-data \
    && addgroup -g $USER_GID www-data \
    && adduser -u $USER_UID -G www-data -g 'www-data' -s /sbin/nologin -D  www-data

USER www-data

EXPOSE 9000

ENTRYPOINT ["php-fpm"]
