FROM php:7.4-fpm

RUN apt-get update && apt-get upgrade -y && apt-get install -y supervisor procps vim-tiny wget iputils-ping libonig-dev libmcrypt-dev libmagickwand-dev mariadb-client libxml2-dev libssh-dev libzip-dev zip zsh lynx git && apt-get autoremove -y
RUN docker-php-ext-install bcmath soap pdo_mysql sockets mbstring exif zip
RUN pecl install mcrypt-1.0.3
RUN pecl install imagick-3.4.4
RUN docker-php-ext-enable imagick mcrypt 
RUN docker-php-ext-configure zip & docker-php-ext-install zip
RUN pecl install mongodb
RUN echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongo.ini
RUN docker-php-ext-enable mongodb
WORKDIR /var/www
RUN groupmod -g 333 www-data
RUN usermod -u 333 -g 333 www-data
#RUN chown -R 333:333 /var/www/storage
RUN rm -f "$PHP_INI_DIR/php.ini-production"
RUN rm -f "$PHP_INI_DIR/php.ini-development"
COPY ./app/bootstrap.sh /usr/local/bin
COPY ./app/php.ini "$PHP_INI_DIR/php.ini"
EXPOSE 9000

RUN mkdir -p /var/log/supervisor
RUN chown www-data:www-data /var/log/supervisor
RUN mkdir -p /var/run/supervisor
RUN chown www-data:www-data /var/run/supervisor
CMD ["/usr/local/bin/bootstrap.sh"]

COPY ./app/policy.xml /etc/ImageMagick-6/

