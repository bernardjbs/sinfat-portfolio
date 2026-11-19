#!/bin/bash
set -e

cd /var/www/sinfat

# just deploy pulls before starting this script, so bash runs the new copy of it.
# Run by hand without that pull, it would rebuild the old code and still say deployed: refuse instead.
git fetch --quiet origin main
if [ "$(git rev-parse HEAD)" != "$(git rev-parse origin/main)" ]; then
    echo "✗ not at origin/main: run just deploy, or git pull origin main first" >&2
    exit 1
fi

echo "→ installing composer dependencies..."
composer install --no-dev --optimize-autoloader

# Straight after the PHP dependencies: the pulled code is already live, so its tables must exist
# before the slow frontend build
echo "→ running migrations..."
php artisan migrate --force

echo "→ installing npm dependencies..."
npm install --prefer-offline

echo "→ building frontend assets..."
npm run build

echo "→ caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "→ removing any sitemap file..."
# /sitemap.xml is a route built from the database; nginx would serve a stale file in its place
sudo rm -f public/sitemap.xml

echo "→ reloading nginx..."
sudo systemctl reload nginx

echo "✅ deployed"
