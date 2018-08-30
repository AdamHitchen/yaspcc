FROM php:7.2-fpm

RUN apt-get update
RUN apt-get install git zlib1g-dev -y
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
RUN mkdir /scripts
COPY ./startup.sh /scripts/startup.sh
WORKDIR /scripts
RUN chmod +x startup.sh
ENTRYPOINT ["/scripts/startup.sh"]