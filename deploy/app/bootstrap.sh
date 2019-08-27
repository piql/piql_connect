#!/bin/bash
cd /var/www
php artisan telescope:publish
php artisan migrate
php artisan db:seed
php artisan vue-i18n:generate
/usr/local/sbin/php-fpm
