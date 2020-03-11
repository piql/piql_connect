#!/bin/bash
rm -f ../laravel-echo-server.lock
docker-compose -p piqlConnect up --force-recreate -d
