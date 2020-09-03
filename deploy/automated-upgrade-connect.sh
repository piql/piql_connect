#!/bin/bash
export HOST_USERID=333
export HOST_GROUPID=333

# Environment variables
UPDATE_AM_SERVICE_CALLBACKS=true
CONNECT_HOSTNAME=pc-fmu-test.piql.com
LOCALDEV=true
STORAGE_LOCATION_ID=c19e5f62-8935-43d2-a948-bcf386c4374a

# Check for resources
if [[ ! -z $UPDATE_AM_SERVICE_CALLBACKS ]] ; then
  which lynx >& /dev/null
  if [ $? -ne 0 ] ; then
    echo "Installing lynx, please enter your password to gain sudo access for this operation"
    sudo apt update && sudo apt install lynx -y || exit $?
  fi
fi

# Select hostname
currHostname=$CONNECT_HOSTNAME
echo "Hostname to be used: $currHostname"
ping -c4 $currHostname
if [ $? -ne 0 ] ; then
  echo "ERROR: Hostname is not reachable"
  exit 1
fi

# Shut down if already running
./status.sh | grep Up
if [ $? -ne 0 ] ; then
  echo "No instance of connect is currently running, continuing..."
else
  echo "An instance of connect is currently running, shutting down"
  ./down.sh
  if [ $? -ne 0 ] ; then
    echo "Failed to shut down connect"
    exit 1
  fi
fi

echo "Copy env file"
cd .. || exit $?
cp .env.example .env || exit $?

echo "Set APP_URL"
sed -i "s/_HOSTNAME_/$currHostname/g" .env || exit $?

echo "Update storage location id"
if [[ ! -z $STORAGE_LOCATION_ID ]] ; then
  replaceString=$(cat .env | grep STORAGE_LOCATION_ID)
  sed -i "s/$replaceString/STORAGE_LOCATION_ID=$STORAGE_LOCATION_ID/g" .env || exit $?
fi

echo "Composer"
composer install || exit $?

echo "npm install"
npm install || exit $?
npm install cross-env || exit $?

echo "Install vue"
php artisan vue-i18n:generate || exit $?

echo "npm run"
if [[ ! -z $LOCALDEV ]] ; then
  npm run dev || exit $?
else
  npm run prod || exit $?
fi

cd deploy || exit $?

echo "Create docker volumes"
docker volume ls | grep am-pipeline-data
if [ $? -ne 0 ] ; then
  docker volume create --name=am-pipeline-data || exit $?
fi
docker volume ls | grep ss-location-data
if [ $? -ne 0 ] ; then
  docker volume create --name=ss-location-data || exit $?
fi
docker volume ls | grep piqlConnect-mariadb
if [ $? -ne 0 ] ; then
  docker volume create --name=piqlConnect-mariadb || exit $?
fi
docker volume ls | grep piqlConnect-mongodb
if [ $? -ne 0 ] ; then
  docker volume create --name=piqlConnect-mongodb || exit $?
fi

echo "Set file permissions"
sudo chown 333:$USER -R ../storage || exit $?
sudo chown 333:$USER -R ../bootstrap/cache || exit $?
sudo chown 333:$USER -R ../vendor || exit $?
sudo chown 333:$USER -R ../config || exit $?
sudo chown 333:$USER -R ../public || exit $?
sudo chown 333:$USER -R ../resources/js || exit $?
mkdir -p ../.config || exit $? # Only for Tinker
sudo chown 333:$USER -R ../.config || exit $? # Only for Tinker

# Tell docker about the server
docker network disconnect piqlconnect_piqlConnect-net compose_archivematica-storage-service_1
docker network connect --link piqlconnect_nginx_1:$currHostname piqlconnect_piqlConnect-net compose_archivematica-storage-service_1

echo "Run docker containers"
cp ../laravel-echo-server.json.example ../laravel-echo-server.json || exit $?
if [[ ! -z $LOCALDEV ]] ; then
    sed -i 's/https/http/g' ../laravel-echo-server.json || exit $?
    ./localdev-up.sh || exit $?
else
    ./up.sh || exit $?
fi

echo "Migrate database tables"
docker-compose -p piqlConnect exec -T app php artisan migrate || exit $?

echo "Set file permissions for docker volumes"
sudo chown 333:root /var/lib/docker/volumes/ss-location-data/_data || exit $?
sudo chown 333:root /var/lib/docker/volumes/ss-location-data/_data/archivematica || exit $?

echo "Update AM service callbacks"
if [[ ! -z $UPDATE_AM_SERVICE_CALLBACKS ]] ; then
  ./update-service-callbacks.php || exit $?
fi
    
echo "Finished successfully"