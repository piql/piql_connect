FROM php:7.3-fpm

RUN apt-get update && apt-get upgrade -y && apt-get install -y supervisor procps vim-tiny wget iputils-ping libmcrypt-dev libmagickwand-dev mariadb-client libxml2-dev libssh-dev libzip-dev zip zsh lynx git && apt-get autoremove -y
RUN docker-php-ext-install bcmath soap pdo_mysql sockets mbstring exif zip
RUN pecl install mcrypt-1.0.2
RUN pecl install imagick-3.4.4
RUN docker-php-ext-enable imagick mcrypt
RUN docker-php-ext-configure zip --with-libzip && docker-php-ext-install zip
WORKDIR /var/www
RUN groupmod -g 333 www-data
RUN usermod -u 333 -g 333 www-data
#RUN chown -R 333:333 /var/www/storage
COPY ./app/bootstrap.sh /usr/local/bin
EXPOSE 9000
CMD ["/usr/local/bin/bootstrap.sh"]
