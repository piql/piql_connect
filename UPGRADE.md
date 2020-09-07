# Upgrading from version < 0.3 to a newer version

1. Backup database (mysqldump --host=127.0.0.1 -u <DB-user> -p --single-transaction --quick --lock-tables=false connect > ~/piqlconnect_db-$(date +%F).sql)
2. Stop PiqlConnect
3. Checkout new release branch
4. Install php 7.4 following these steps: https://www.cloudbooklet.com/install-php-7-4-on-ubuntu/
5. Install php extentions (sudo apt install php-xml php-pear php-bcmath php7.4-common php7.4-mysql php7.4-xml php7.4-xmlrpc php7.4-curl php7.4-gd php7.4-imagick php7.4-cli php7.4-dev php7.4-imap php7.4-mbstring php7.4-opcache php7.4-soap php7.4-zip php7.4-intl -y)
6. Install php mongodb support (pecl install mongodb)
7. Add mongodb extention to php.ini
8. Run buildComposer.sh
9. Install NodeJS version 10 following these steps: https://www.digitalocean.com/community/tutorials/how-to-install-node-js-on-ubuntu-18-04)
9. Run the automated update script
10. Check that there are no obvious data issues and that PiqlConnect is functioning as intended


## Rollback in case of issues

1. Stop PiqlConnect
2. Start the database container
3. Drop the connect database
4. Restore the database backup
5. Checkout the previous version of PiqlConnect
6. Rebuild the environment based on the procedure for that version. Make sure you don't run any database migrations
7. Check that all is OK and retry the upgrade