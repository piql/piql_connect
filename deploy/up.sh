#!/bin/bash
export HOST_USERID=`id -u`
export HOST_GROUPID=`id -g`
sudo chown -R $HOST_USERID:$HOST_GROUPID /var/lib/docker/volumes
sudo chown -R $HOST_USERID:$HOST_GROUPID /var/lib/docker/volumes
sudo chown -R $HOST_USERID:$HOST_GROUPID ./nginx/certbot/conf
sudo chmod -R 777 ../resources/
docker-compose -p piqlConnect up --force-recreate
