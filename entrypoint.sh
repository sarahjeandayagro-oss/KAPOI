#!/usr/bin/env sh
set -e

mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs

php artisan config:clear
php artisan route:clear
php artisan view:clear || true
php artisan migrate --force --seed


exec php artisan serve --host 0.0.0.0 --port "${PORT:-10000}"
