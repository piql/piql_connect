#!/bin/bash
docker exec -ti piqlconnect_app_1 php artisan queue:work
