#!/bin/bash
docker-compose -p piqlConnect down --rmi local
docker-compose -p piqlConnect build --no-cache
