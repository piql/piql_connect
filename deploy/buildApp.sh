#!/bin/bash
sh ./init-letsencrypt.sh
docker-compose -p piqlConnect build --no-cache app 