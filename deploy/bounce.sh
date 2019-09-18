#!/bin/bash
docker-compose -p piqlConnect down 
docker-compose -p piqlConnect up --force-recreate -d