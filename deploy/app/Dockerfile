FROM php:7.3-fpm

RUN apt-get update && apt-get upgrade -y && apt-get install -y procps vim-tiny wget iputils-ping libmcrypt-dev libmagickwand-dev mariadb-client libxml2-dev && apt-get autoremove -y
RUN pecl install mcrypt-1.0.2
RUN pecl install imagick-3.4.4
RUN docker-php-ext-install bcmath soap pdo_mysql
RUN docker-php-ext-enable imagick mcrypt

WORKDIR /var/www
RUN usermod -u 1000 www-data
RUN chown -R www-data:www-data /var/www
EXPOSE 9000
CMD ["php-fpm"]
