FROM php:7.2
RUN apt-get update
RUN apt-get install git zlib1g-dev -y
RUN docker-php-ext-install -j$(nproc) mysqli \
    && docker-php-ext-install -j$(nproc) zip
RUN yes | pecl install xdebug
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
RUN echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" >> /usr/local/etc/php/conf.d/xdebug.ini
WORKDIR /yaspcc
RUN groupadd -g 1000 php
RUN useradd -g php -u 1000 php
USER php
ENTRYPOINT ["php","/yaspcc/cron.php"]