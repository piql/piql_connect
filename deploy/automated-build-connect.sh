#!/bin/bash
export HOST_USERID=333
export HOST_GROUPID=333

echo 'Copy env file'
cd .. || exit $?
cp .env.example .env || exit $?

if [[ ! -z $UPDATE_AM_SERVICE_CALLBACKS ]] ; then
  which lynx >& /dev/null
  if [ $? -ne 0 ] ; then
    echo "ERROR: Please install lynx"
    exit 1
  fi
fi

echo 'Update storage location id'
if [[ ! -z $STORAGE_LOCATION_ID ]] ; then
  replaceString=$(cat .env | grep STORAGE_LOCATION_ID)
  sed -i "s/$replaceString/STORAGE_LOCATION_ID=$STORAGE_LOCATION_ID/g" .env || exit $?
fi

echo 'Composer'
composer install || exit $?

echo 'npm install'
npm install || exit $?
npm install cross-env || exit $?

echo 'Install vue'
php artisan vue-i18n:generate || exit $?

echo 'npm run'
npm run prod || exit $? # has warnings

echo 'Set execute permissions to deploy scripts'
cd deploy || exit $?
chmod u+x localdev-up.sh || exit $?

echo 'Create docker volumes' # Remove before each nightly build
docker volume create --name=am-pipeline-data || exit $?
docker volume create --name=ss-location-data || exit $?
docker volume create --name=piqlConnect-mariadb || exit $?

echo 'Set file permissions'
sudo chown 333:$USER -R ../storage || exit $?
sudo chown 333:$USER -R ../bootstrap/cache || exit $?
sudo chown 333:$USER ../.env || exit $? # Is it needed? - Probably no
sudo chown 333:$USER -R ../vendor || exit $?
sudo chown 333:$USER -R ../config || exit $?
sudo chown 333:$USER -R ../public || exit $?
sudo chown 333:$USER -R ../resources/js || exit $?
mkdir ../.config || exit $? # Only for Tinker
sudo chown 333:$USER -R ../.config || exit $? # Only for Tinker

# Tell docker about the server
docker network connect --link piqlconnect_nginx_1:piqlconnect-dev.piql.com piqlconnect_piqlConnect-net compose_archivematica-storage-service_1

echo 'Run docker containers'
if [ ! -v "$LOCALDEV" ] ; then
    ./up.sh || exit $?
else
    ./localdev-up.sh || exit $?
fi

echo 'Generate application key'
docker-compose -p piqlConnect exec -T app php artisan key:generate || exit $?

echo 'Migrate database tables'
docker-compose -p piqlConnect exec -T app php artisan migrate:fresh || exit $?

echo 'Set passport keys'
docker-compose -p piqlConnect exec -T app php artisan passport:keys --force || exit $?

echo 'Seed database'
docker-compose -p piqlConnect exec -T app php artisan db:seed || exit $?

echo 'Set file permissions for docker volumes'
sudo chown 333:root /var/lib/docker/volumes/ss-location-data/_data || exit $?
sudo chown 333:root /var/lib/docker/volumes/ss-location-data/_data/archivematica || exit $?

echo 'Configure S3 in env file'
envfile='../.env'
sed -i 's/AWS_/#AWS_/g' $envfile
echo '
AWS_ACCESS_KEY_ID=A027DQI8VXPIJETYNXZQ
AWS_SECRET_ACCESS_KEY=OOE4owN4uin0ctQQ6VAqsDmsHnGh4AUjgrsbEFtg
AWS_DEFAULT_REGION=
AWS_BUCKET=connect-test
AWS_URL=https://s3.osl1.safedc.net
' >> $envfile || exit $?

echo 'Update AM service callbacks'
if [[ ! -z $UPDATE_AM_SERVICE_CALLBACKS ]] ; then
  ./update-service-callbacks.php || exit $?
fi
    
echo 'Finished successfully'
