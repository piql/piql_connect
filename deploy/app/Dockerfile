FROM php:7.3-fpm

RUN apt-get update && apt-get upgrade -y && apt-get install -y supervisor procps vim-tiny wget iputils-ping libmcrypt-dev libmagickwand-dev mariadb-client libxml2-dev libssh-dev libzip-dev zip zsh lynx git && apt-get autoremove -y
RUN docker-php-ext-install bcmath soap pdo_mysql sockets mbstring exif zip
RUN pecl install mcrypt-1.0.2
RUN pecl install imagick-3.4.4
RUN docker-php-ext-enable imagick mcrypt
RUN docker-php-ext-configure zip --with-libzip && docker-php-ext-install zip
WORKDIR /var/www
RUN usermod -u 1000 www-data
RUN chown -R www-data:www-data /var/www
RUN chmod 775 /var/www
COPY ./app/bootstrap.sh /usr/local/bin
EXPOSE 9000
CMD ["/usr/local/bin/bootstrap.sh"]
