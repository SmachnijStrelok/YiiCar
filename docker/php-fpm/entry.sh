#!/bin/bash

mkdir -p storage/app/public storage/app/chunks storage/app/upload
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/

rm -rf /app/public/storage
cd /app/public; ln -s /app/storage/app/public/ storage || true

php artisan cache:clear
php artisan config:clear
php artisan view:clear

chmod -R 777 storage/logs/

cron -f &
docker-php-entrypoint php-fpm
