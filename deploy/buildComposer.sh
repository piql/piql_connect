#!/bin/bash
host=$(ip addr show ens33 | grep -Po 'inet \K[\d.]+') || 127.0.0.1
uid=333
gid=333
HOST_USERID=uid HOST_GROUPID=gid CONNECT_HOSTNAME=host docker-compose -p piqlConnect build composer
