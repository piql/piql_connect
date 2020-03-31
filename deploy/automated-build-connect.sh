#!/bin/bash
export HOST_USERID=333
export HOST_GROUPID=333
export STORAGE_LOCATION_ID="095675df-6ef3-43b1-8180-05efd5578df0"
export ARCHIVEMATICA_SERVICE_SERVICE_SEEDER_STORAGE_ID="4d3d72a0-7023-11ea-9e4b-9d55b3d96e2e"
export ARCHIVEMATICA_SERVICE_SEEDER_API_AUTHORIZATION_KEY="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImMwMDdkMWVhMTY4ODhmZjc3NGU3OTc5Y2Q2N2RiMGU2YzEzMzlmZjkwY2U2ZjU3ODUzMzU4YmM0YjEyMDM3YTgyZDU5ZjliMmJiMmIyY2I5In0.eyJhdWQiOiIxIiwianRpIjoiYzAwN2QxZWExNjg4OGZmNzc0ZTc5NzljZDY3ZGIwZTZjMTMzOWZmOTBjZTZmNTc4NTMzNThiYzRiMTIwMzdhODJkNTlmOWIyYmIyYjJjYjkiLCJpYXQiOjE1ODUzMTA3NTEsIm5iZiI6MTU4NTMxMDc1MSwiZXhwIjoxNjE2ODQ2NzUxLCJzdWIiOiJmODAzNTMyMC03MDEzLTExZWEtOTAzYS03ZDgzN2NkOTA4MzEiLCJzY29wZXMiOltdfQ.LWOIQJ1mNhtJbTxgG0NSai0Ddjk1ueKpty4eCCUUNp-cHs6UDtfaGjGw_YSrslwtPnH_bL8qKCJcqm0qG2bfgMqUMoI4t3XE3mC3LW8QeAhtAYxuSswNAKYXQbOFOGKa7qGhtBbaGE4lqN1-qVfTb7EpfSwUfsGDENjn5CVbkj3zIDjNRuN_8uEMSSFHgzPJpY4Z5PHEofFb0Mkr4ocvI6qhBa0YyTKrSV2kYEVt74BikGjeavPbb1xi2V63FRWKgpjPBlPLF7yi-xa-GeMfIJz31T3fG6Xpa8WsTOjBbF71JmC5mNIVkwICjgcOD_7ZrpmmWh7OLHU_q17VrwNM0R-hj5nDMdoS4-dXxqZBA4uTWbofO_FPEDSgHAuhIsMS52D2dINbWqLBudLiGxyRcMxw5M2bnFTXgAIb1vlHW8KlUh9CmmmuMi2-_YsJxVao3sb93IfMJMzafJBUes5LQNVXQBrSAVUnrgIME5vrcG5VTguLQnyVEGH7IdDDira8EJ2o89gMQfHU8jdw2LZPUYwMtuKAoZqosb5ghDeweQxrDk9Zkib39bkPAgyYFjLYV6RvoViQU3n_JSewzFcolZY_0Zxe5SFHglPwUvAwm0Pgks7tinBNW1ydptUB_XT56w8wSP7QMNgceK2OP8QcqXLf8--TkY4GIzQiQaRANK4"

echo 'Copy env file'
cd .. || exit $?
cp .env.example .env || exit $?

echo 'Update storage location id'
replaceString=$(cat .env | grep STORAGE_LOCATION_ID)
sed -i "s/$replaceString/STORAGE_LOCATION_ID=$STORAGE_LOCATION_ID/g" .env || exit $?

echo 'Update storage url'
if [[ -v $ARCHIVEMATICA_SERVICE_SEEDER_STORAGE_ID ]] ; then
  echo "" >> .env
  echo "ARCHIVEMATICA_SERVICE_SEEDER_STORAGE_ID=$ARCHIVEMATICA_SERVICE_SEEDER_STORAGE_ID" >> .env
fi

echo 'Update archivematica api authorization key'
if [[ -v $ARCHIVEMATICA_SERVICE_SEEDER_API_AUTHORIZATION_KEY ]] ; then
  echo "" >> .env
  echo "ARCHIVEMATICA_SERVICE_SEEDER_API_AUTHORIZATION_KEY=$ARCHIVEMATICA_SERVICE_SEEDER_API_AUTHORIZATION_KEY" >> .env
fi

echo 'Composer'
composer install || exit $?

echo 'npm install'
npm install || exit $?
npm install cross-env || exit $?
#npm install vue || exit $?

echo 'Install vue'
npm install vue-loader || exit $?
composer require martinlindhe/laravel-vue-i18n-generator || exit $?
php artisan vue-i18n:generate || exit $?
#npm i --save vuex-i18n

echo 'npm run'
npm install bootstrap || exit $?
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
./localdev-up.sh || exit $?

echo 'Generate application key'
docker-compose -p piqlConnect exec app php artisan key:generate || exit $?

echo 'Migrate database tables'
docker-compose -p piqlConnect exec app php artisan migrate:fresh || exit $?

docker-compose -p piqlConnect exec app php artisan passport:keys

echo 'Seed database'
docker-compose -p piqlConnect exec app php artisan db:seed || exit $?

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

echo 'Finished successfully'
