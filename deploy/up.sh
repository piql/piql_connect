#!/bin/bash
export HOST_USERID=`id -u`
export HOST_GROUPID=`id -g`
docker-compose -p piqlConnect up --force-recreate
