#!/bin/bash
cd /var/www
#groupmod -g 333 www-data
#usermod -u 333 -g 333 www-data
chmod 777 ./storage/logs/*
ZSH=/var/www/.zsh sh -c "$(wget -O- https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh)" "" --unattended 
php artisan telescope:publish
php artisan migrate
php artisan db:seed
php artisan vue-i18n:generate
/usr/bin/supervisord
/usr/local/sbin/php-fpm
