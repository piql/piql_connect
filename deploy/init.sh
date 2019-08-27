#!/bin/bash

docker volume create piqlConnect-mariadb
sudo chown `id -u`:`id -g` -R /var/lib/docker/volumes/piqlConnect-mariadb

docker network create piqlConnect-net

