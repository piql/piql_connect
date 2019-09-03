#!/bin/bash
cd /var/www
ZSH=/var/www/.zsh sh -c "$(wget -O- https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh)" "" --unattended 
php artisan telescope:publish
php artisan migrate
php artisan db:seed
php artisan vue-i18n:generate
/usr/local/sbin/php-fpm
