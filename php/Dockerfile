FROM php:7.3-fpm

RUN apt-get update && apt-get install git zlib1g-dev libzip-dev -y
RUN docker-php-ext-install -j$(nproc) mysqli \
    && docker-php-ext-install -j$(nproc) zip
RUN yes | pecl install xdebug
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
RUN  echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
     && php composer-setup.php \
     && php -r "unlink('composer-setup.php');" \
     && chmod +x composer.phar \
     && mv composer.phar /usr/local/bin/composer
WORKDIR /yaspcc
RUN groupadd -g 1000 php
RUN useradd -g php -u 1000 php
USER php
ENTRYPOINT ["php-fpm","-F"]