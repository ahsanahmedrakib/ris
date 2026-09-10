#!/bin/bash
set -euo pipefail

echo "=== Installing npm dependencies ==="
npm ci

echo "=== Building frontend assets (Vite) ==="
npm run build

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Seeding database (if empty) ==="
php artisan db:seed --force || echo "Seed skipped (data may already exist)"

echo "=== Clearing caches ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Build finished successfully ==="