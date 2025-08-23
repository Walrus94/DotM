#!/bin/bash

set -euo pipefail

run_service()
{
    service "$1" start || exit 1
}

if [ ! -e .docker-init-done ] ; then
    touch .docker-init-done

    bash "$(dirname "$0")/generate-config.sh" || echo "config generation failed; continuing"
    composer --version || echo "composer missing; continuing"
    composer install --no-progress --optimize-autoloader || echo "composer install failed; continuing"
    if command -v patch >/dev/null 2>&1; then
        bin/local-patch || echo "local patches failed; continuing"
    fi
    php bin/config-css /tmp/config-css.js || echo "config-css failed; continuing"
    echo "Installing node, go grab a coffee"
    npm install || echo "npm install failed; continuing"
    npx update-browserslist-db@latest || echo "browserslist DB update failed; continuing"
    npx puppeteer browsers install chrome || echo "puppeteer install failed; continuing"
    npm run dev || echo "asset build failed; continuing"
fi

while ! nc -z mysql 3306
do
    echo "Waiting for MySQL..."
    sleep 10
done

echo "Run mysql migrations..."
if ! FKEY_MY_DATABASE=1 LOCK_MY_DATABASE=1 php /var/www/vendor/bin/phinx migrate; then
    echo "PHINX FAILED TO RUN MIGRATIONS"
    exit 1
fi

echo "Run postgres migrations..."
if ! php /var/www/vendor/bin/phinx migrate -c ./misc/phinx-pg.php; then
    echo "PHINX FAILED TO RUN MIGRATIONS"
    exit 1
fi

if [ ! -f /var/www/misc/phinx/seeded.txt ]; then
    echo "Run seed:run..."
    if ! php /var/www/vendor/bin/phinx seed:run; then
        echo "PHINX FAILED TO SEED"
        exit 1
    fi
    echo "Seeds have been run, delete to rerun" > /var/www/misc/phinx/seeded.txt
    chmod 400 /var/www/misc/phinx/seeded.txt
fi

echo "Start services..."

run_service cron
run_service nginx
run_service php${PHP_VER}-fpm

crontab /var/www/misc/docker/web/crontab

tail -f /var/log/nginx/access.log
