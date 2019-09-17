#!/bin/bash
export HOST_USERID=333
export HOST_GROUPID=333
sudo chown -R $HOST_USERID:$HOST_GROUPID /var/lib/docker/volumes
sudo chown -R $HOST_USERID:$HOST_GROUPID /var/lib/docker/volumes
sudo chmod -R 777 ../resources/
docker-compose -p piqlConnect up --force-recreate -d
