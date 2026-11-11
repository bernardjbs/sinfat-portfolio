#!/bin/bash
set -e

cd /var/www/sinfat

echo "→ pulling latest code..."
git pull origin main

echo "→ installing composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "→ installing npm dependencies..."
npm install --prefer-offline

echo "→ building frontend assets..."
npm run build

echo "→ running migrations..."
php artisan migrate --force

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
